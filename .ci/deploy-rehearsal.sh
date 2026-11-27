#!/usr/bin/env bash
# Rehearses .github/scripts/deploy-remote.sh on the CI runner (no real server) - CI job "deploy-rehearsal".
#
# Builds the release artifact exactly like deploy-reusable.yml, prepares a CloudPanel-like site directory
# (/…/home/<user>/htdocs/<domain>) and runs the script's commands the way the workflow does. A php dev
# server is (re)started on the resolved web root after every switch, like nginx with $realpath_root.
#   1. normal deploy -> verify -> finalize
#   2. failing migration          -> deploy fails, live release untouched, rollback "not-needed"
#   3. failed verification        -> rollback switches back to the previous release, workers signalled
#   4. finalize                   -> failed releases removed, live and previous kept
#   5. production version guard   -> an older tag is refused
#   6. preflight guards           -> wrong .env for the site, a real public/ directory
#
# Needs: MySQL reachable with DB_HOST/DB_PORT/DB_USER/DB_PASSWORD, an empty database DB_NAME, PHP, composer.
set -euo pipefail
cd "$(dirname "$0")/.."

SHA="$(git rev-parse HEAD)"
WORK="$(mktemp -d)"
PORT=8090
SITE_URL="http://127.0.0.1:${PORT}"
SERVER_PID_FILE="$WORK/server.pid"
DEPLOY_PATH="$WORK/home/akademus-test/htdocs/akademus.test"
PHP_BIN="$(command -v php)"
EXPECTED_PHP="$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')"
export DEPLOY_PATH PHP_BIN EXPECTED_PHP SITE_URL TARGET_ENV=staging KEEP_RELEASES=3
passed=0

ok() { passed=$((passed + 1)); echo "PASS $passed: $*"; }
die() { echo "::error title=Deploy rehearsal::$*"; exit 1; }
# Print deploy output as plain text: the expected failures of these scenarios (a broken migration, a rollback)
# must not become error/warning annotations on an otherwise green run.
show() {
  local token="rehearsal-output-$RANDOM$RANDOM"
  echo "::stop-commands::$token"
  printf '%s\n' "$1"
  echo "::$token::"
}
live() { basename "$(readlink "$DEPLOY_PATH/current")"; }
signal() { cat "$DEPLOY_PATH/shared/writable/worker-restart" 2> /dev/null || true; }

stop_server() {
  if [[ -f "$SERVER_PID_FILE" ]]; then
    kill "$(cat "$SERVER_PID_FILE")" 2> /dev/null || true
    rm -f "$SERVER_PID_FILE"
    sleep 1
  fi
}
serve() { # what nginx does with $realpath_root: resolve public -> current/public -> releases/<id>/public
  stop_server
  local root
  root="$(readlink -f "$DEPLOY_PATH/public")"
  (cd "$root/.." && { nohup php -S "127.0.0.1:${PORT}" -t "$root" vendor/codeigniter4/framework/system/rewrite.php > "$WORK/server.log" 2>&1 & echo $! > "$SERVER_PID_FILE"; })
  sleep 1
}
health_has() { curl -sf --max-time 5 "$SITE_URL/health" | grep -q "$1"; }
trap 'stop_server; rm -rf "$WORK"' EXIT

echo "== Build the release (as deploy-reusable.yml does)"
BUILD="$WORK/build"
mkdir -p "$BUILD" "$WORK/dist"
git archive HEAD | tar -x -C "$BUILD"
(cd "$BUILD" && composer install --no-dev --classmap-authoritative --no-scripts --no-interaction --no-progress --prefer-dist -q)
echo "$SHA" > "$BUILD/REVISION"
echo "rehearsal" > "$BUILD/RELEASE"
tar -tzf <(tar --exclude-from=.github/scripts/bundle-exclude.txt -czf - -C "$BUILD" .) | grep -qE '(^|/)\.env$|^\./writable/|^\./\.github/' \
  && die "release contains .env, writable/ or .github/"

# Stage an upload like the workflow: .deploy/incoming/<id>/{release.tar.gz,deploy-remote.sh,SHA256SUMS}. Prints the id.
stage() { # source dir
  local id in
  sleep 1 # release ids are per second
  id="$(date -u +%Y%m%dT%H%M%SZ)-${SHA:0:7}"
  in="$DEPLOY_PATH/.deploy/incoming/$id"
  mkdir -p "$in"
  tar --exclude-from=.github/scripts/bundle-exclude.txt -czf "$in/release.tar.gz" -C "$1" .
  cp .github/scripts/deploy-remote.sh "$in/deploy-remote.sh"
  (cd "$in" && sha256sum release.tar.gz deploy-remote.sh > SHA256SUMS)
  echo "$id"
}
run() { # command id [extra env...] - runs the uploaded copy of the script, as the workflow does
  local cmd="$1" id="$2"
  shift 2
  env RELEASE_ID="$id" RELEASE_SHA="$SHA" INCOMING="$DEPLOY_PATH/.deploy/incoming/$id" "$@" \
    bash "$DEPLOY_PATH/.deploy/incoming/$id/deploy-remote.sh" "$cmd"
}
run_installed() { # command id [extra env...] - verify/finalize/rollback after the upload may be gone
  local cmd="$1" id="$2"
  shift 2
  env RELEASE_ID="$id" RELEASE_SHA="$SHA" INCOMING="$DEPLOY_PATH/.deploy/incoming/$id" "$@" \
    bash .github/scripts/deploy-remote.sh "$cmd"
}

echo "== CloudPanel-like site (what docs/SERVER-SETUP.md creates by hand)"
mkdir -p "$DEPLOY_PATH/shared"
write_env() { # baseURL
  cat > "$DEPLOY_PATH/shared/.env" << EOF
CI_ENVIRONMENT = production
app.baseURL = '$1/'
encryption.key = hex2bin:$(php -r 'echo bin2hex(random_bytes(32));')
database.default.hostname = ${DB_HOST}
database.default.port = ${DB_PORT}
database.default.database = ${DB_NAME}
database.default.username = ${DB_USER}
database.default.password = ${DB_PASSWORD}
database.default.DBDriver = MySQLi
EOF
  chmod 600 "$DEPLOY_PATH/shared/.env"
}
write_env "$SITE_URL"

echo; echo "======== 1. Normal deploy -> verify -> finalize"
id1="$(stage "$BUILD")"
out=$(run deploy "$id1" 2>&1) || { show "$out"; die "1: deploy failed"; }
show "$out"
grep -q "^DEPLOY_RESULT release=$id1 previous=none" <<< "$out" || die "1: no DEPLOY_RESULT"
[[ "$(live)" == "$id1" ]] || die "1: current is not the new release"
[[ "$(readlink "$DEPLOY_PATH/public")" == current/public ]] || die "1: public -> current/public was not created"
[[ -L "$DEPLOY_PATH/releases/$id1/.env" && -L "$DEPLOY_PATH/releases/$id1/writable" ]] || die "1: shared .env / writable not linked"
ls "$DEPLOY_PATH"/shared/backups/staging-"$id1".sql.gz > /dev/null || die "1: no database backup"
[[ -n "$(signal)" ]] || die "1: email workers were not signalled"
serve
health_has "$SHA" || die "1: /health does not report $SHA"
run_installed verify "$id1" | tee "$WORK/verify.log"
grep -q '^VERIFY_RESULT ok' "$WORK/verify.log" || die "1: verify failed"
run_installed finalize "$id1"
[[ -f "$DEPLOY_PATH/releases/$id1/.healthy" ]] || die "1: release not marked healthy"
[[ ! -e "$DEPLOY_PATH/.deploy/incoming/$id1" ]] || die "1: upload not removed"
ok "normal deploy -> $id1"

echo; echo "======== 2. Failing migration (live release must stay untouched)"
BROKEN="$WORK/broken"
cp -a "$BUILD" "$BROKEN"
cat > "$BROKEN/app/Database/Migrations/2099-01-01-000000_RehearsalBrokenMigration.php" << 'EOF'
<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class RehearsalBrokenMigration extends Migration
{
    public function up(): void { $this->db->query('THIS IS NOT SQL'); }
    public function down(): void {}
}
EOF
(cd "$BROKEN" && composer dump-autoload --classmap-authoritative --no-dev -q)
id2="$(stage "$BROKEN")"
if out=$(run deploy "$id2" 2>&1); then show "$out"; die "2: deploy succeeded despite a broken migration"; fi
show "$out"
grep -q "Migration failed" <<< "$out" || die "2: deploy failed, but not because of the migration"
[[ "$(live)" == "$id1" ]] || die "2: current changed after a failed migration"
out=$(run rollback "$id2" 2>&1) || { show "$out"; die "2: rollback command failed"; }
grep -q "ROLLBACK_RESULT not-needed" <<< "$out" || { show "$out"; die "2: rollback should not be needed"; }
[[ -f "$DEPLOY_PATH/releases/$id2/.failed" ]] || die "2: failed release not marked"
health_has "$SHA" || die "2: site not healthy after the failed deploy"
ok "failed migration -> still serving $id1"

echo; echo "======== 3. Failed verification (automatic code rollback)"
id3="$(stage "$BUILD")"
out=$(run deploy "$id3" 2>&1) || { show "$out"; die "3: deploy failed"; }
[[ "$(live)" == "$id3" ]] || die "3: not switched to $id3"
before="$(signal)"
# The workflow's external verification "fails" here; it then runs rollback:
out=$(run rollback "$id3" 2>&1) || { show "$out"; die "3: rollback failed"; }
show "$out"
grep -q "ROLLBACK_RESULT release=$id1" <<< "$out" || die "3: rollback did not report $id1"
[[ "$(live)" == "$id1" ]] || die "3: current was not switched back to $id1"
[[ -f "$DEPLOY_PATH/releases/$id3/.failed" ]] || die "3: rolled-back release not marked failed"
[[ "$(signal)" != "$before" ]] || die "3: email workers were not signalled after the rollback"
serve
health_has "$SHA" || die "3: site not healthy after rollback"
ok "failed verification -> rolled back to $id1"

echo; echo "======== 4. Finalize removes failed releases"
run_installed finalize "$id1"
[[ ! -e "$DEPLOY_PATH/releases/$id2" && ! -e "$DEPLOY_PATH/releases/$id3" ]] || die "4: failed releases still present"
[[ -d "$DEPLOY_PATH/releases/$id1" ]] || die "4: live release removed"
ok "finalize keeps only healthy releases"

echo; echo "======== 5. Production version guard"
id5="$(stage "$BUILD")"
out=$(run deploy "$id5" TARGET_ENV=production RELEASE_VERSION=v2.0.0 2>&1) || { show "$out"; die "5: v2.0.0 deploy failed"; }
run_installed finalize "$id5" TARGET_ENV=production > /dev/null
id6="$(stage "$BUILD")"
if out=$(run deploy "$id6" TARGET_ENV=production RELEASE_VERSION=v1.9.0 2>&1); then show "$out"; die "5: an older version was deployed"; fi
grep -q "refusing to deploy older v1.9.0" <<< "$out" || { show "$out"; die "5: refused, but not by the version guard"; }
[[ "$(live)" == "$id5" ]] || die "5: current changed"
run rollback "$id6" TARGET_ENV=production > /dev/null
ok "production refuses v1.9.0 after v2.0.0"

echo; echo "======== 6. Preflight guards"
write_env "https://another-site.example"
id7="$(stage "$BUILD")"
if out=$(run deploy "$id7" 2>&1); then die "6: deployed with another site's .env"; fi
grep -q "differs from SITE_URL" <<< "$out" || { show "$out"; die "6: wrong failure for a foreign .env"; }
write_env "$SITE_URL"
mv "$DEPLOY_PATH/public" "$WORK/public-link"
mkdir "$DEPLOY_PATH/public"
if out=$(run deploy "$id7" 2>&1); then die "6: replaced a real public/ directory"; fi
grep -q "is a real directory" <<< "$out" || { show "$out"; die "6: wrong failure for a real public/"; }
rmdir "$DEPLOY_PATH/public"
mv "$WORK/public-link" "$DEPLOY_PATH/public"
[[ "$(live)" == "$id5" ]] || die "6: current changed"
ok "foreign .env and a real public/ directory are refused"

echo; echo "All $passed deploy rehearsal scenarios passed."
