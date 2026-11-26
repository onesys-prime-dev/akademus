#!/usr/bin/env bash
# Runs ON THE SERVER (piped over SSH by .github/workflows/deploy-reusable.yml). See docs/CI-CD.md.
#
#   remote-deploy.sh <environment> <sha> <deploy_path> <php_bin> <site_url> <tls_insecure> <expected_php> <bundle_file>
#
# Layout under DEPLOY_PATH:
#   releases/<UTC timestamp>-<sha12>/   one directory per release (immutable code + vendor)
#   shared/.env                         environment config (created once by hand, never by CI)
#   shared/writable/                    cache, logs, session, uploads (persist across releases)
#   current -> releases/<...>           the live release (web root = current/public)
#   backups/                            gzipped mysqldump taken before each migration
#   incoming/                           uploaded bundles (cleaned after each deploy)
#
# Order: lock -> preflight -> unpack -> link shared -> backup DB -> migrate + reference seed (new code)
#        -> atomic symlink switch -> reload PHP-FPM + restart worker -> health check -> prune.
# A failed migration leaves the live release untouched. A failed health check switches back.
set -Eeuo pipefail
umask 027
# shared/.env alone decides the environment: an inherited CI_ENVIRONMENT (e.g. from a CI job or the
# deploy user's shell) would override it - and 'testing' even crashes spark outside PHPUnit.
unset CI_ENVIRONMENT

ENVIRONMENT="$1"; SHA="$2"; DEPLOY_PATH="${3%/}"; PHP_BIN="$4"; SITE_URL="${5%/}"
TLS_INSECURE="$6"; EXPECTED_PHP="$7"; BUNDLE="$8"
KEEP_RELEASES="${KEEP_RELEASES:-5}"
case "$ENVIRONMENT" in production) KEEP_BACKUPS="${KEEP_BACKUPS:-15}" ;; *) KEEP_BACKUPS="${KEEP_BACKUPS:-5}" ;; esac
FPM_SERVICE="${PHP_FPM_SERVICE:-php${EXPECTED_PHP}-fpm}"
WORKER_SERVICE="akademus-mail@${ENVIRONMENT}"

log()  { printf '\n==> %s\n' "$*"; }
fail() { echo "::error title=Deploy (${ENVIRONMENT})::$*"; exit 1; }

[[ "$ENVIRONMENT" =~ ^(staging|production)$ ]] || fail "Unknown environment '$ENVIRONMENT'"
[[ "$SHA" =~ ^[0-9a-f]{40}$ ]] || fail "Invalid commit SHA"
[[ "$DEPLOY_PATH" == /* && "$DEPLOY_PATH" != "/" ]] || fail "DEPLOY_PATH must be an absolute path other than /"

cd "$DEPLOY_PATH"
mkdir -p releases backups incoming

# ------------------------------------------------------------------ lock (no parallel deploys)
# The lock lives on file descriptor 9 for the whole script. Commands that may leave long-running
# processes behind (service reloads) run with "9>&-", so nothing they start can keep holding the lock.
exec 9> "$DEPLOY_PATH/.deploy.lock"
flock -w 600 9 || fail "Another deploy holds $DEPLOY_PATH/.deploy.lock"

# ------------------------------------------------------------------ preflight
log "Preflight"
php_version=$("$PHP_BIN" -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;') || fail "PHP_BIN '$PHP_BIN' does not run"
[[ "$php_version" == "$EXPECTED_PHP" ]] || fail "PHP_BIN is PHP $php_version but CI tested PHP $EXPECTED_PHP (.ci/php-version)"
for ext in intl mbstring mysqli curl json openssl fileinfo; do
  "$PHP_BIN" -r "exit(extension_loaded('$ext') ? 0 : 1);" || fail "PHP extension '$ext' is not loaded"
done
[[ -f shared/.env ]] || fail "shared/.env is missing - run deploy/server-setup.sh first (docs/CI-CD.md)"
for d in cache logs session uploads debugbar; do
  [[ -d "shared/writable/$d" && -w "shared/writable/$d" ]] || fail "shared/writable/$d is missing or not writable"
done
grep -qE '^\s*CI_ENVIRONMENT\s*=\s*production' shared/.env || echo "::warning::shared/.env does not set CI_ENVIRONMENT = production (errors may be shown to users)"
upload_mb=$("$PHP_BIN" -r 'echo (int) ini_get("upload_max_filesize");')
(( upload_mb >= 10 )) || echo "::warning::upload_max_filesize is ${upload_mb}M for $PHP_BIN; payment proofs may be up to 10 MB (check the PHP-FPM ini too, docs/CI-CD.md)"
avail_kb=$(df -Pk "$DEPLOY_PATH" | awk 'NR==2 {print $4}')
(( avail_kb > 1048576 )) || fail "Less than 1 GB free on $DEPLOY_PATH"
( cd incoming && sha256sum -c "${BUNDLE}.sha256" ) || fail "Bundle checksum mismatch"

# ------------------------------------------------------------------ unpack
RELEASE="releases/$(date -u +%Y%m%d%H%M%S)-${SHA:0:12}"
log "Unpacking into $RELEASE"
mkdir -p "$RELEASE"
tar -xzf "incoming/$BUNDLE" -C "$RELEASE"
[[ "$(cat "$RELEASE/REVISION")" == "$SHA" ]] || fail "Bundle REVISION does not match $SHA"

ln -sfn ../../shared/.env "$RELEASE/.env"
rm -rf "$RELEASE/writable"
ln -sfn ../../shared/writable "$RELEASE/writable"
find "$RELEASE" -type d -exec chmod 750 {} +
find "$RELEASE" -type f -exec chmod 640 {} +
chmod 750 "$RELEASE/spark"

# On any failure before the switch, remove the half-prepared release; always remove the credentials file.
SWITCHED=false
MYCNF=""
on_exit() {
  local rc=$?
  [[ -n "$MYCNF" ]] && rm -f "$MYCNF"
  if [[ $rc -ne 0 && "$SWITCHED" == false && -n "${RELEASE:-}" && -d "$RELEASE" ]]; then
    rm -rf "$RELEASE"
  fi
}
trap on_exit EXIT

# ------------------------------------------------------------------ database backup
log "Backing up the database"
MYCNF=$(mktemp)
chmod 600 "$MYCNF"
# Credentials go from shared/.env straight into a 0600 option file; they are never echoed.
# shellcheck disable=SC2016 # the single-quoted text is PHP code, not shell
DB_NAME=$(cd "$RELEASE" && "$PHP_BIN" -r '
  require "vendor/autoload.php";
  $env = (new CodeIgniter\Config\DotEnv(getcwd()))->parse() ?? [];
  $g = fn ($k, $d = "") => $env["database.default.$k"] ?? $d;
  file_put_contents($argv[1], sprintf("[client]\nhost=%s\nport=%s\nuser=%s\npassword=\"%s\"\n",
      $g("hostname", "localhost"), $g("port", "3306"), $g("username"), addcslashes($g("password"), "\\\"")));
  echo $g("database");' "$MYCNF")
[[ -n "$DB_NAME" ]] || fail "database.default.database is not set in shared/.env"
BACKUP="backups/${ENVIRONMENT}-$(date -u +%Y%m%d%H%M%S)-${SHA:0:12}.sql.gz"
mysqldump --defaults-extra-file="$MYCNF" --single-transaction --routines --triggers --no-tablespaces "$DB_NAME" | gzip > "$BACKUP"
chmod 600 "$BACKUP"
gzip -dc "$BACKUP" | tail -n 1 | grep -q 'Dump completed' || fail "Database backup is incomplete"
echo "Backup: $DEPLOY_PATH/$BACKUP"

# ------------------------------------------------------------------ migrate (with the NEW code, before switching)
# spark exits 0 even when a migration or seeder throws (the command catches the exception and only
# prints it), so the exit code alone cannot be trusted: also fail on a printed exception block.
spark_in_release() {
  local out rc=0
  out=$(cd "$RELEASE" && "$PHP_BIN" spark "$@" 2>&1) || rc=$?
  printf '%s\n' "$out"
  [[ $rc -eq 0 ]] || return 1
  ! grep -qE '^\s*\[[A-Za-z0-9_\]*(Exception|Error)\]' <<<"$out"
}

log "Migrating"
spark_in_release migrate || fail "Migration failed - the live release was NOT switched. Backup: $DEPLOY_PATH/$BACKUP"
# Authoritative check: nothing may still be pending (pending rows show --- as group / migration date).
pending=$(cd "$RELEASE" && "$PHP_BIN" spark migrate:status 2>&1 | grep -cE '^\|.*\|\s*---\s*\|' || true)
[[ "$pending" -eq 0 ]] || fail "Migration failed - $pending migration(s) still pending, the live release was NOT switched. Backup: $DEPLOY_PATH/$BACKUP"
spark_in_release db:seed ReferenceDataSeeder || fail "Reference data seeding failed - the live release was NOT switched"

# ------------------------------------------------------------------ switch
PREVIOUS=$(readlink current 2>/dev/null || true)
switch_to() { ln -sfn "$1" current.tmp && mv -Tf current.tmp current; }
reload_services() {
  sudo -n systemctl reload "$FPM_SERVICE" 9>&- || return 1   # clears OPcache for the new paths
  if systemctl list-unit-files "akademus-mail@.service" --no-legend 2>/dev/null | grep -q akademus-mail; then
    sudo -n systemctl restart "$WORKER_SERVICE" 9>&- || return 1
  else
    echo "::warning::$WORKER_SERVICE is not installed - the email queue worker was not restarted (docs/CI-CD.md)"
  fi
}

log "Switching current -> $RELEASE (previous: ${PREVIOUS:-none})"
switch_to "$RELEASE"
SWITCHED=true
( cd current && "$PHP_BIN" spark cache:clear ) || echo "::warning::cache:clear failed"

rollback() {
  echo "::error title=Deploy (${ENVIRONMENT})::$1 - rolling back to ${PREVIOUS:-nothing}"
  if [[ -n "$PREVIOUS" ]]; then
    switch_to "$PREVIOUS"
    reload_services || true
    ( cd current && "$PHP_BIN" spark cache:clear ) || true
  fi
  echo "The database was migrated and is NOT rolled back automatically. Backup: $DEPLOY_PATH/$BACKUP"
  echo "See 'Restoring the database' in docs/CI-CD.md if the previous release cannot run on the new schema."
  exit 1
}

reload_services || rollback "Reloading $FPM_SERVICE / restarting $WORKER_SERVICE failed"

# ------------------------------------------------------------------ health check
log "Health check $SITE_URL/health"
curl_tls=(); [[ "$TLS_INSECURE" == "true" ]] && curl_tls=(--insecure)
healthy=false
for _ in $(seq 1 12); do
  body=$(curl -sS --max-time 10 "${curl_tls[@]}" "$SITE_URL/health" || true)
  if [[ "$body" == *'"status": "ok"'* || "$body" == *'"status":"ok"'* ]] && [[ "$body" == *"$SHA"* ]]; then
    healthy=true; break
  fi
  sleep 5
done
$healthy || rollback "Health check failed (last response: ${body:-<none>})"
echo "Healthy: $SITE_URL is serving $SHA"

# ------------------------------------------------------------------ prune
log "Pruning"
# Release directories are named <UTC timestamp>-<sha>, so a reverse name sort is newest first.
mapfile -t old < <(find releases -mindepth 1 -maxdepth 1 -type d | sort -r | tail -n +$((KEEP_RELEASES + 1)))
for r in "${old[@]}"; do
  [[ "$r" == "$(readlink current)" || "$r" == "$PREVIOUS" ]] && continue
  rm -rf "$r" && echo "removed $r"
done
find backups -maxdepth 1 -name "${ENVIRONMENT}-*.sql.gz" | sort -r | tail -n +$((KEEP_BACKUPS + 1)) | xargs -r rm -f
rm -f "incoming/$BUNDLE" "incoming/${BUNDLE}.sha256"

echo "DEPLOYED ${ENVIRONMENT} ${SHA} -> ${RELEASE}"
