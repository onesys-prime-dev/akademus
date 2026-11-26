#!/usr/bin/env bash
# Rehearses deploy/remote-deploy.sh on the CI runner (no real server involved) - CI job "deploy-rehearsal".
#
# Builds the release bundle exactly like deploy-reusable.yml, prepares a server layout in a temp dir,
# stubs `sudo` / `systemctl` (the stubbed "PHP-FPM reload" restarts a php dev server on the new release),
# then checks three scenarios:
#   1. normal deploy            -> succeeds, current -> new release, /health reports the commit
#   2. failing migration        -> fails, live release untouched, half-built release removed
#   3. failing health check     -> fails, current switched back to the previous release
#
# Needs: MySQL reachable with DB_HOST/DB_PORT/DB_USER/DB_PASSWORD, an empty database DB_NAME, PHP, composer.
set -euo pipefail
cd "$(dirname "$0")/.."

SHA="$(git rev-parse HEAD)"
PHP_VERSION="$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')"
WORK="$(mktemp -d)"
DEPLOY_PATH="$WORK/srv/akademus"
PORT=8090
SITE_URL="http://127.0.0.1:${PORT}"
BUNDLE="akademus-${SHA:0:12}.tar.gz"
STUBS="$WORK/stubs"
SERVER_PID_FILE="$WORK/server.pid"

pass() { echo "PASS: $*"; }
die()  { echo "::error title=Deploy rehearsal::$*"; exit 1; }

stop_server() {
  if [[ -f "$SERVER_PID_FILE" ]]; then
    kill "$(cat "$SERVER_PID_FILE")" 2>/dev/null || true
    rm -f "$SERVER_PID_FILE"
  fi
}
trap 'stop_server; rm -rf "$WORK"' EXIT

echo "== Build bundle (as deploy-reusable.yml does)"
BUILD="$WORK/build"
mkdir -p "$BUILD"
git archive HEAD | tar -x -C "$BUILD"
( cd "$BUILD" && composer install --no-dev --classmap-authoritative --no-scripts --no-interaction --no-progress --prefer-dist -q )
echo "$SHA" > "$BUILD/REVISION"
echo "rehearsal" > "$BUILD/RELEASE"
make_bundle() { # $1 = source dir
  mkdir -p "$DEPLOY_PATH/incoming"
  tar --exclude-from=deploy/bundle-exclude.txt -czf "$DEPLOY_PATH/incoming/$BUNDLE" -C "$1" .
  ( cd "$DEPLOY_PATH/incoming" && sha256sum "$BUNDLE" > "$BUNDLE.sha256" )
}
tar -tzf <(tar --exclude-from=deploy/bundle-exclude.txt -czf - -C "$BUILD" .) | grep -qE '(^|/)\.env$|^\./writable/' \
  && die "bundle contains .env or writable/"

echo "== Server layout (what deploy/server-setup.sh creates)"
mkdir -p "$DEPLOY_PATH"/{releases,backups,shared/writable/{cache,logs,session,uploads,debugbar}}
cat > "$DEPLOY_PATH/shared/.env" <<EOF
CI_ENVIRONMENT = production
app.baseURL = '${SITE_URL}/'
encryption.key = hex2bin:$(php -r 'echo bin2hex(random_bytes(32));')
database.default.hostname = ${DB_HOST}
database.default.port = ${DB_PORT}
database.default.database = ${DB_NAME}
database.default.username = ${DB_USER}
database.default.password = ${DB_PASSWORD}
database.default.DBDriver = MySQLi
EOF
chmod 640 "$DEPLOY_PATH/shared/.env"

echo "== Stubs for sudo / systemctl"
mkdir -p "$STUBS"
cat > "$STUBS/sudo" <<EOF
#!/usr/bin/env bash
[[ "\$1" == "-n" ]] && shift
if [[ "\$1 \$2" == "systemctl reload" ]]; then
  [[ -f "$SERVER_PID_FILE" ]] && kill "\$(cat "$SERVER_PID_FILE")" 2>/dev/null || true
  sleep 1
  # Only the php process is backgrounded, so \$! is its PID and stop_server really stops it.
  ( cd "$DEPLOY_PATH/current" && { nohup php -S 127.0.0.1:${PORT} -t "\$(readlink -f public)" vendor/codeigniter4/framework/system/rewrite.php > "$WORK/server.log" 2>&1 & echo \$! > "$SERVER_PID_FILE"; } )
  sleep 1
  echo "stub: reloaded (php dev server restarted on \$(readlink -f "$DEPLOY_PATH/current"))"
  exit 0
fi
echo "stub: sudo \$*"
EOF
printf '#!/usr/bin/env bash\nexit 0\n' > "$STUBS/systemctl"
chmod +x "$STUBS"/*

deploy() { # runs remote-deploy.sh the way the workflow does (script on stdin)
  PATH="$STUBS:$PATH" KEEP_RELEASES=3 bash -s -- staging "$SHA" "$DEPLOY_PATH" php "$SITE_URL" false "$PHP_VERSION" "$BUNDLE" < deploy/remote-deploy.sh
}
current() { readlink "$DEPLOY_PATH/current"; }

echo; echo "======== Scenario 1: normal deploy"
make_bundle "$BUILD"
deploy || die "scenario 1: deploy failed"
first="$(current)"
[[ "$(cat "$DEPLOY_PATH/$first/REVISION")" == "$SHA" ]] || die "scenario 1: current is not the new release"
[[ -L "$DEPLOY_PATH/$first/.env" && -L "$DEPLOY_PATH/$first/writable" ]] || die "scenario 1: shared .env / writable are not linked"
curl -sf "$SITE_URL/health" | grep -q "$SHA" || die "scenario 1: /health does not report $SHA"
ls "$DEPLOY_PATH"/backups/staging-*.sql.gz > /dev/null || die "scenario 1: no database backup"
[[ ! -e "$DEPLOY_PATH/incoming/$BUNDLE" ]] || die "scenario 1: incoming bundle not cleaned up"
pass "normal deploy -> $first"

echo; echo "======== Scenario 2: failing migration (live release must stay untouched)"
sleep 1
BROKEN="$WORK/broken"; cp -a "$BUILD" "$BROKEN"
cat > "$BROKEN/app/Database/Migrations/2099-01-01-000000_RehearsalBrokenMigration.php" <<'EOF'
<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class RehearsalBrokenMigration extends Migration
{
    public function up(): void { $this->db->query('THIS IS NOT SQL'); }
    public function down(): void {}
}
EOF
( cd "$BROKEN" && composer dump-autoload --classmap-authoritative --no-dev -q )
make_bundle "$BROKEN"
# Must fail, and for the right reason (not e.g. a held lock).
if out=$(deploy 2>&1); then echo "$out"; die "scenario 2: deploy succeeded despite a broken migration"; fi
echo "$out"
grep -q "Migration failed" <<<"$out" || die "scenario 2: deploy failed, but not because of the migration"
[[ "$(current)" == "$first" ]] || die "scenario 2: current changed after a failed migration"
[[ "$(find "$DEPLOY_PATH/releases" -mindepth 1 -maxdepth 1 -type d | wc -l)" -eq 1 ]] || die "scenario 2: half-built release left behind"
curl -sf "$SITE_URL/health" | grep -q "$SHA" || die "scenario 2: site not healthy after failed deploy"
pass "failed migration -> still serving $first"

echo; echo "======== Scenario 3: failing health check (automatic code rollback)"
sleep 1
make_bundle "$BUILD"
stop_server
sleep 1
curl -sf --max-time 3 "$SITE_URL/health" > /dev/null && die "scenario 3: the previous server is still answering, the scenario would be meaningless"
touch "$WORK/break-reload-after-switch"
# The reload after the switch "succeeds" but leaves the server down -> health fails -> rollback,
# whose reload starts the server again on the previous release.
cat > "$STUBS/sudo" <<EOF
#!/usr/bin/env bash
[[ "\$1" == "-n" ]] && shift
if [[ "\$1 \$2" == "systemctl reload" && -f "$WORK/break-reload-after-switch" ]]; then
  rm -f "$WORK/break-reload-after-switch"   # first reload (after switch): server stays down
  echo "stub: reloaded (server intentionally left down)"; exit 0
fi
if [[ "\$1 \$2" == "systemctl reload" ]]; then
  # Only the php process is backgrounded, so \$! is its PID and stop_server really stops it.
  ( cd "$DEPLOY_PATH/current" && { nohup php -S 127.0.0.1:${PORT} -t "\$(readlink -f public)" vendor/codeigniter4/framework/system/rewrite.php > "$WORK/server.log" 2>&1 & echo \$! > "$SERVER_PID_FILE"; } )
  sleep 1; echo "stub: reloaded"; exit 0
fi
exit 0
EOF
chmod +x "$STUBS/sudo"
if out=$(deploy 2>&1); then echo "$out"; die "scenario 3: deploy succeeded although the health check could not pass"; fi
echo "$out"
grep -q "Health check failed" <<<"$out" || die "scenario 3: deploy failed, but not because of the health check"
grep -q "rolling back" <<<"$out" || die "scenario 3: no rollback was performed"
[[ "$(current)" == "$first" ]] || die "scenario 3: current was not rolled back to $first"
curl -sf "$SITE_URL/health" | grep -q "$SHA" || die "scenario 3: site not healthy after rollback"
pass "failed health check -> rolled back to $first"

echo; echo "All deploy rehearsal scenarios passed."
