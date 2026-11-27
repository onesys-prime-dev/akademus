#!/usr/bin/env bash
# Akademus server-side deployment script. Runs on the VPS as the CloudPanel site user - no sudo, no root.
# It is shipped inside the release artifact (checksummed with it), so the script that runs is the one CI tested.
# See docs/CI-CD.md (lifecycle) and docs/SERVER-SETUP.md (one-time server setup).
#
#   deploy-remote.sh deploy   - preflight, unpack, link shared, back up the DB, migrate, switch, restart worker
#   deploy-remote.sh verify   - check the live release on the server (environment, revision, migrations)
#   deploy-remote.sh finalize - mark the live release healthy, remove old releases, backups and uploads
#   deploy-remote.sh rollback - switch back to the previous release after a failed deploy or verification
#
# Inputs (environment variables, set by .github/workflows/deploy-reusable.yml):
#   DEPLOY_PATH      /home/<site-user>/htdocs/<domain>
#   PHP_BIN          /usr/bin/php8.5
#   TARGET_ENV       staging | production
#   EXPECTED_PHP     major.minor CI tested (.ci/php-version)            (deploy)
#   SITE_URL         public URL; must match app.baseURL in shared/.env (deploy)
#   RELEASE_ID       <UTC yyyymmddThhmmssZ>-<sha7>                      (deploy, rollback)
#   RELEASE_SHA      full commit SHA                                    (deploy, verify)
#   RELEASE_VERSION  release tag such as v1.4.0, or empty (staging)     (deploy)
#   INCOMING         .deploy/incoming/<RELEASE_ID> holding release.tar.gz, deploy-remote.sh, SHA256SUMS
#   KEEP_RELEASES    healthy releases to keep (default 5)
#   KEEP_BACKUPS     database backups to keep (default 5 staging / 15 production)
#
# Layout under DEPLOY_PATH (CloudPanel site directory):
#   shared/.env  shared/writable/  shared/backups/     data - created once, never touched by CI
#   releases/<RELEASE_ID>/                             code; .env and writable are symlinks into shared/
#   current -> releases/<RELEASE_ID>                   the live release
#   public  -> current/public                          CloudPanel's web root (nginx resolves it with $realpath_root)
#   .deploy/                                           lock, state, uploads
set -euo pipefail
# Files must stay readable by nginx (static assets); secrets and backups are chmod 600 explicitly.
umask 022
# shared/.env alone decides the CodeIgniter environment; an inherited CI_ENVIRONMENT would override it.
unset CI_ENVIRONMENT

REQUIRED_EXTENSIONS=(intl mbstring mysqli curl json openssl fileinfo)
MIN_FREE_MB=1024
ID_PATTERN='^[0-9]{8}T[0-9]{6}Z-[0-9a-f]{7}$'

log() { printf '%s %s\n' "$(date -u +%H:%M:%S)" "$*"; }
group() { echo "::group::$*"; }
endgroup() { echo "::endgroup::"; }
fail() { echo "::error title=Deploy (${TARGET_ENV:-?})::$*"; exit 1; }
warn() { echo "::warning::$*"; }

command="${1:-}"
: "${DEPLOY_PATH:?DEPLOY_PATH is required}"
: "${PHP_BIN:?PHP_BIN is required}"
: "${TARGET_ENV:?TARGET_ENV is required}"
KEEP_RELEASES="${KEEP_RELEASES:-5}"
if [[ "$TARGET_ENV" == production ]]; then KEEP_BACKUPS="${KEEP_BACKUPS:-15}"; else KEEP_BACKUPS="${KEEP_BACKUPS:-5}"; fi

# Tolerate a trailing slash or doubled slashes in the configured path (e.g. ".../<domain>/").
while [[ "$DEPLOY_PATH" == *//* ]]; do DEPLOY_PATH="${DEPLOY_PATH//\/\//\/}"; done
DEPLOY_PATH="${DEPLOY_PATH%/}"
if [[ -n "${INCOMING:-}" ]]; then
  while [[ "$INCOMING" == *//* ]]; do INCOMING="${INCOMING//\/\//\/}"; done
  INCOMING="${INCOMING%/}"
fi
SHARED="$DEPLOY_PATH/shared"
RELEASES="$DEPLOY_PATH/releases"
BACKUPS="$SHARED/backups"
STATE_DIR="$DEPLOY_PATH/.deploy"
STATE_FILE="$STATE_DIR/state"

# ----------------------------------------------------------------------------- guards and helpers
guard_paths() {
  [[ "$DEPLOY_PATH" == /* ]] || fail "DEPLOY_PATH must be absolute (got '$DEPLOY_PATH')"
  [[ "$DEPLOY_PATH" == */htdocs/* ]] || fail "DEPLOY_PATH must be inside a CloudPanel htdocs directory (got '$DEPLOY_PATH')"
  [[ "$DEPLOY_PATH" != *..* ]] || fail "DEPLOY_PATH must not contain '..'"
  [[ -d "$DEPLOY_PATH" ]] || fail "DEPLOY_PATH '$DEPLOY_PATH' does not exist (create the CloudPanel site first, docs/SERVER-SETUP.md)"
  [[ -O "$DEPLOY_PATH" ]] || fail "DEPLOY_PATH '$DEPLOY_PATH' is not owned by $(id -un) - wrong VPS_USERNAME for this site?"
  [[ "$TARGET_ENV" == staging || "$TARGET_ENV" == production ]] || fail "TARGET_ENV must be staging or production"
  [[ "$KEEP_RELEASES" =~ ^[0-9]+$ && "$KEEP_RELEASES" -ge 2 ]] || fail "KEEP_RELEASES must be a number >= 2"
  [[ "$KEEP_BACKUPS" =~ ^[0-9]+$ && "$KEEP_BACKUPS" -ge 1 ]] || fail "KEEP_BACKUPS must be a number >= 1"
}

guard_incoming() {
  : "${INCOMING:?INCOMING is required}"
  [[ "$INCOMING" == "$STATE_DIR/incoming/"* && "$INCOMING" != *..* ]] || fail "INCOMING must be inside $STATE_DIR/incoming/"
}

with_lock() {
  mkdir -p "$STATE_DIR"
  exec 9> "$STATE_DIR/lock"
  flock -w 600 9 || fail "Another deployment holds $STATE_DIR/lock (waited 10 minutes)"
}

state_get() { [[ -f "$STATE_FILE" ]] && grep -E "^$1=" "$STATE_FILE" | tail -n1 | cut -d= -f2- || true; }
state_set() {
  local tmp="$STATE_FILE.tmp"
  { [[ -f "$STATE_FILE" ]] && grep -vE "^$1=" "$STATE_FILE" || true; echo "$1=$2"; } > "$tmp"
  mv -f "$tmp" "$STATE_FILE"
}

# Read a key from shared/.env without printing it. Strips surrounding quotes.
env_value() {
  local line
  line="$(grep -E "^[[:space:]]*$1[[:space:]]*=" "$SHARED/.env" | tail -n1 || true)"
  line="${line#*=}"
  line="${line%$'\r'}"
  line="${line#"${line%%[![:space:]]*}"}"
  line="${line%"${line##*[![:space:]]}"}"
  [[ "$line" =~ ^\"(.*)\"$ || "$line" =~ ^\'(.*)\'$ ]] && line="${BASH_REMATCH[1]}"
  printf '%s' "$line"
}

url_host() { local u="${1#*://}"; u="${u%%/*}"; printf '%s' "${u,,}"; }

current_release() { [[ -L "$DEPLOY_PATH/current" ]] && basename "$(readlink "$DEPLOY_PATH/current")" || true; }

switch_to() {
  local id="$1"
  [[ -d "$RELEASES/$id" ]] || fail "Release $id does not exist"
  ln -sfn "releases/$id" "$DEPLOY_PATH/.current.tmp"
  mv -Tf "$DEPLOY_PATH/.current.tmp" "$DEPLOY_PATH/current"
  if [[ ! -e "$DEPLOY_PATH/public" && ! -L "$DEPLOY_PATH/public" ]]; then
    ln -s current/public "$DEPLOY_PATH/public"
    log "Created web root symlink public -> current/public"
  fi
  log "current -> releases/$id"
}

# Run spark in a release. spark exits 0 even when a migration or seeder throws (the command catches the
# exception and only prints it), so the exit code alone cannot be trusted: also fail on a printed exception.
spark() {
  local dir="$1" out rc=0
  shift
  out=$(cd "$dir" && "$PHP_BIN" spark "$@" 2>&1) || rc=$?
  printf '%s\n' "$out"
  [[ $rc -eq 0 ]] || return 1
  ! grep -qE '^\s*\[[A-Za-z0-9_\]*(Exception|Error)\]' <<< "$out"
}

pending_migrations() { # count of migrations not yet applied for the release in $1
  (cd "$1" && "$PHP_BIN" spark migrate:status 2>&1 | grep -cE '^\|.*\|\s*---\s*\|' || true)
}

# After a switch: clear the file cache and tell the email workers to restart on the live release.
after_switch() {
  spark "$DEPLOY_PATH/current" cache:clear > /dev/null || warn "cache:clear failed"
  spark "$DEPLOY_PATH/current" emails:restart || warn "emails:restart failed - restart the Supervisor program by hand"
}

release_meta() { # release dir, key
  # shellcheck disable=SC2016 # PHP code
  "$PHP_BIN" -r 'echo json_decode((string) @file_get_contents($argv[1]), true)[$argv[2]] ?? "";' "$1/.release.json" "$2"
}

# Remove the uploaded artifact directory; only ever inside .deploy/incoming/.
remove_incoming() {
  [[ -n "${INCOMING:-}" ]] || return 0
  [[ "$INCOMING" == "$STATE_DIR/incoming/"* && "$INCOMING" != *..* ]] || { warn "Not removing unexpected INCOMING path '$INCOMING'"; return 0; }
  rm -rf "$INCOMING"
}

# ----------------------------------------------------------------------------- preflight
preflight() {
  group "Preflight checks ($TARGET_ENV)"
  guard_paths

  [[ -x "$PHP_BIN" ]] || command -v "$PHP_BIN" > /dev/null || fail "PHP_BIN '$PHP_BIN' is not executable"
  local php_version
  php_version="$("$PHP_BIN" -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')"
  [[ "$php_version" == "${EXPECTED_PHP:?EXPECTED_PHP is required}" ]] \
    || fail "PHP_BIN is PHP $php_version but CI tested PHP $EXPECTED_PHP (.ci/php-version)"
  local modules missing=()
  modules="$("$PHP_BIN" -m | tr '[:upper:]' '[:lower:]')"
  for ext in "${REQUIRED_EXTENSIONS[@]}"; do grep -qx "$ext" <<< "$modules" || missing+=("$ext"); done
  ((${#missing[@]} == 0)) || fail "PHP is missing extensions: ${missing[*]}"
  local upload_mb
  upload_mb="$("$PHP_BIN" -r 'echo (int) ini_get("upload_max_filesize");')"
  ((upload_mb >= 10)) || warn "upload_max_filesize is ${upload_mb}M for $PHP_BIN; payment proofs may be up to 10 MB (docs/SERVER-SETUP.md)"
  log "PHP $php_version with required extensions"

  [[ -f "$SHARED/.env" ]] || fail "$SHARED/.env is missing - create it by hand (docs/SERVER-SETUP.md)"
  [[ -r "$SHARED/.env" ]] || fail "$SHARED/.env is not readable by $(id -un)"
  local key
  for key in encryption.key app.baseURL database.default.hostname database.default.database database.default.username; do
    [[ -n "$(env_value "$key")" ]] || fail "shared/.env: $key is missing or empty"
  done
  grep -qE '^[[:space:]]*database\.default\.password[[:space:]]*=' "$SHARED/.env" || fail "shared/.env: database.default.password is missing"
  local ci_env
  ci_env="$(env_value CI_ENVIRONMENT)"
  if [[ "$ci_env" != production ]]; then
    [[ "$TARGET_ENV" == production ]] && fail "shared/.env: CI_ENVIRONMENT is '${ci_env:-unset}', production requires 'production'"
    warn "shared/.env: CI_ENVIRONMENT is '${ci_env:-unset}' on staging (errors may be shown to users)"
  fi
  if [[ -n "${SITE_URL:-}" && "$(url_host "$(env_value app.baseURL)")" != "$(url_host "$SITE_URL")" ]]; then
    fail "shared/.env: app.baseURL host '$(url_host "$(env_value app.baseURL)")' differs from SITE_URL host '$(url_host "$SITE_URL")' - wrong .env for this site?"
  fi
  [[ "$(stat -c '%a' "$SHARED/.env")" =~ [1-7]$ ]] && warn "shared/.env is readable by other users - run: chmod 600 $SHARED/.env"
  log "shared/.env present with required keys (values not shown)"

  command -v mysqldump > /dev/null || fail "mysqldump not found on PATH (needed for the pre-migration backup)"

  mkdir -p "$SHARED/writable"/{cache,logs,session,uploads,debugbar} "$BACKUPS"
  chmod 700 "$BACKUPS"
  local probe="$SHARED/writable/.deploy-write-test"
  touch "$probe" 2> /dev/null || fail "$SHARED/writable is not writable by $(id -un)"
  rm -f "$probe"
  log "shared/writable writable"

  local free_mb
  free_mb="$(df -Pm "$DEPLOY_PATH" | awk 'NR==2 {print $4}')"
  ((free_mb >= MIN_FREE_MB)) || fail "Only ${free_mb} MB free under $DEPLOY_PATH (need ${MIN_FREE_MB} MB)"
  log "${free_mb} MB free"

  if [[ -e "$DEPLOY_PATH/public" && ! -L "$DEPLOY_PATH/public" ]]; then
    fail "$DEPLOY_PATH/public is a real directory. Remove CloudPanel's placeholder by hand (docs/SERVER-SETUP.md); the deploy will not delete it."
  fi
  if [[ -L "$DEPLOY_PATH/public" && "$(readlink "$DEPLOY_PATH/public")" != current/public ]]; then
    fail "$DEPLOY_PATH/public points to '$(readlink "$DEPLOY_PATH/public")', expected 'current/public'"
  fi
  if [[ -e "$DEPLOY_PATH/current" && ! -L "$DEPLOY_PATH/current" ]]; then
    fail "$DEPLOY_PATH/current exists but is not a symlink"
  fi

  # Version guard: production never moves to a lower version. A rollback is a NEW, higher tag (docs/CI-CD.md).
  local live live_version lowest
  live="$(current_release)"
  if [[ "$TARGET_ENV" == production && -n "${RELEASE_VERSION:-}" && -n "$live" && -f "$RELEASES/$live/.release.json" ]]; then
    live_version="$(release_meta "$RELEASES/$live" version)"
    if [[ -n "$live_version" && "$live_version" != "$RELEASE_VERSION" ]]; then
      lowest="$(printf '%s\n%s\n' "${live_version#v}" "${RELEASE_VERSION#v}" | sort -V | head -n1)"
      [[ "$lowest" == "${live_version#v}" ]] || fail "Production runs $live_version; refusing to deploy older $RELEASE_VERSION (tag the good commit with a new, higher version)"
    fi
    log "Version guard: live ${live_version:-unknown} -> $RELEASE_VERSION"
  fi
  endgroup
}

# ----------------------------------------------------------------------------- database backup
backup_database() {
  local db_name file cnf
  db_name="$(env_value database.default.database)"
  file="$BACKUPS/${TARGET_ENV}-${RELEASE_ID}.sql.gz"
  cnf="$(mktemp)"
  chmod 600 "$cnf"
  # Credentials go from shared/.env straight into a 0600 option file; they are never printed.
  printf '[client]\nhost=%s\nport=%s\nuser=%s\npassword="%s"\n' \
    "$(env_value database.default.hostname)" "$(env_value database.default.port || true)" \
    "$(env_value database.default.username)" "$(env_value database.default.password | sed 's/[\\"]/\\&/g')" > "$cnf"
  sed -i 's/^port=$/port=3306/' "$cnf"
  local rc=0
  (set -o pipefail; mysqldump --defaults-extra-file="$cnf" --single-transaction --routines --triggers --no-tablespaces "$db_name" | gzip > "$file") || rc=$?
  rm -f "$cnf"
  chmod 600 "$file" 2> /dev/null || true
  if [[ $rc -ne 0 ]] || ! gzip -dc "$file" | tail -n 1 | grep -q 'Dump completed'; then
    rm -f "$file"
    fail "Database backup failed - nothing was migrated or switched (docs/SERVER-SETUP.md: GRANT PROCESS)"
  fi
  log "Backup: $file ($(du -h "$file" | cut -f1))"
}

# ----------------------------------------------------------------------------- commands
cmd_deploy() {
  : "${RELEASE_ID:?}" "${RELEASE_SHA:?}"
  guard_incoming
  [[ "$RELEASE_ID" =~ $ID_PATTERN ]] || fail "Invalid RELEASE_ID '$RELEASE_ID'"
  [[ "$RELEASE_SHA" =~ ^[0-9a-f]{40}$ ]] || fail "Invalid RELEASE_SHA"
  with_lock
  preflight

  local dir="$RELEASES/$RELEASE_ID" previous
  previous="$(current_release)"
  [[ ! -e "$dir" ]] || fail "Release directory $dir already exists"

  group "Unpack release $RELEASE_ID"
  (cd "$INCOMING" && sha256sum -c --quiet SHA256SUMS) || fail "Artifact checksum mismatch on the server"
  mkdir -p "$dir"
  state_set pending "$RELEASE_ID"
  tar -xzf "$INCOMING/release.tar.gz" -C "$dir"
  [[ -f "$dir/spark" && -f "$dir/vendor/autoload.php" ]] || fail "Artifact is incomplete (spark or vendor/ missing)"
  [[ "$(cat "$dir/REVISION" 2> /dev/null)" == "$RELEASE_SHA" ]] || fail "Artifact REVISION does not match $RELEASE_SHA"
  rm -rf "$dir/writable"
  ln -sfn ../../shared/writable "$dir/writable"
  ln -sfn ../../shared/.env "$dir/.env"
  # shellcheck disable=SC2016 # PHP code
  "$PHP_BIN" -r 'file_put_contents($argv[1], json_encode(["sha" => $argv[2], "version" => $argv[3],
      "environment" => $argv[4], "deployed_at" => gmdate("c"), "previous_release" => $argv[5]],
      JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");' \
    "$dir/.release.json" "$RELEASE_SHA" "${RELEASE_VERSION:-}" "$TARGET_ENV" "$previous"
  log "Unpacked to $dir"
  endgroup

  group "Boot check"
  # Loads the configuration, filters and routes of the new release. (Not "spark env": it exits 1 even on success.)
  spark "$dir" routes > /dev/null || fail "The new release does not boot (php spark routes failed)"
  log "The new release boots"
  endgroup

  group "Pre-migration backup"
  backup_database
  endgroup

  group "Database migrations"
  spark "$dir" migrate || fail "Migration failed - the live release was NOT switched. Backup in shared/backups/."
  local pending
  pending="$(pending_migrations "$dir")"
  [[ "$pending" -eq 0 ]] || fail "Migration failed - $pending migration(s) still pending, the live release was NOT switched. Backup in shared/backups/."
  spark "$dir" db:seed ReferenceDataSeeder || fail "Reference data seeding failed - the live release was NOT switched"
  endgroup

  group "Switch release"
  switch_to "$RELEASE_ID"
  state_set previous "$previous"
  state_set current "$RELEASE_ID"
  state_set pending ""
  after_switch
  log "Email workers told to restart (Supervisor brings them back on the new release)"
  endgroup

  echo "DEPLOY_RESULT release=$RELEASE_ID previous=${previous:-none}"
}

cmd_verify() {
  guard_paths
  local live="$DEPLOY_PATH/current" out
  [[ -L "$live" ]] || fail "No live release"
  if [[ -n "${RELEASE_SHA:-}" ]]; then
    [[ "$(cat "$live/REVISION")" == "$RELEASE_SHA" ]] || fail "Live release is $(cat "$live/REVISION"), expected $RELEASE_SHA"
  fi
  # "spark env" without arguments prints the environment but exits 1 (CodeIgniter 4.7), so only its output counts.
  out="$(cd "$live" && "$PHP_BIN" spark env 2>&1 || true)"
  grep -q 'environment is currently set as' <<< "$out" || fail "php spark env failed on the live release: $out"
  grep -q 'production' <<< "$out" || {
    [[ "$TARGET_ENV" == production ]] && fail "Live release does not run in the production environment: $out"
    warn "Live release environment: $out"
  }
  [[ "$(pending_migrations "$live")" -eq 0 ]] || fail "The live release has pending migrations"
  log "Live release $(basename "$(readlink "$live")"): revision, environment and migrations OK"
  echo "VERIFY_RESULT ok"
}

cmd_finalize() {
  guard_paths
  with_lock
  local live previous
  live="$(current_release)"
  previous="$(state_get previous)"
  [[ -n "$live" ]] || fail "No live release to finalize"
  touch "$RELEASES/$live/.healthy"
  remove_incoming

  group "Remove old releases (keep $KEEP_RELEASES healthy, always including live and previous)"
  local kept=1 id
  if [[ -n "$previous" && "$previous" != "$live" && -d "$RELEASES/$previous" ]]; then kept=2; fi
  while IFS= read -r id; do
    [[ -n "$id" ]] || continue
    if [[ "$id" == "$live" || "$id" == "$previous" ]]; then continue; fi
    if [[ -f "$RELEASES/$id/.healthy" ]] && ((kept < KEEP_RELEASES)); then
      kept=$((kept + 1))
      continue
    fi
    [[ "$id" =~ $ID_PATTERN ]] || { warn "Skipping unexpected directory releases/$id"; continue; }
    rm -rf "${RELEASES:?}/$id"
    log "Removed releases/$id"
  done < <(find "$RELEASES" -mindepth 1 -maxdepth 1 -type d -printf '%f\n' | sort -r)
  endgroup

  group "Remove old backups (keep $KEEP_BACKUPS)"
  find "$BACKUPS" -maxdepth 1 -name "${TARGET_ENV}-*.sql.gz" -printf '%f\n' 2> /dev/null | sort -r \
    | tail -n +$((KEEP_BACKUPS + 1)) | while IFS= read -r f; do rm -f "${BACKUPS:?}/$f" && log "Removed backups/$f"; done
  endgroup
  echo "FINALIZE_RESULT live=$live"
}

cmd_rollback() {
  : "${RELEASE_ID:?}"
  guard_paths
  with_lock
  local live previous
  live="$(current_release)"
  previous="$(state_get previous)"
  if [[ -d "$RELEASES/$RELEASE_ID" ]]; then touch "$RELEASES/$RELEASE_ID/.failed"; fi
  [[ "$(state_get pending)" == "$RELEASE_ID" ]] && state_set pending ""
  remove_incoming

  if [[ "$live" != "$RELEASE_ID" ]]; then
    log "Live release is ${live:-none}, not $RELEASE_ID - the failed release was never switched in; nothing to roll back"
    echo "ROLLBACK_RESULT not-needed"
    return 0
  fi
  if [[ -z "$previous" || ! -d "$RELEASES/$previous" ]]; then
    fail "No previous release to roll back to (first deployment?) - $RELEASE_ID stays live; fix forward"
  fi

  group "Roll back to $previous"
  switch_to "$previous"
  state_set current "$previous"
  state_set previous ""
  after_switch
  endgroup
  warn "Rolled back code to $previous. Database migrations from $RELEASE_ID were NOT reverted (backup in shared/backups/)."
  echo "ROLLBACK_RESULT release=$previous"
}

case "$command" in
  deploy) cmd_deploy ;;
  verify) cmd_verify ;;
  finalize) cmd_finalize ;;
  rollback) cmd_rollback ;;
  *) fail "Usage: deploy-remote.sh deploy|verify|finalize|rollback" ;;
esac
