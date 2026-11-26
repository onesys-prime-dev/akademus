#!/usr/bin/env bash
# One-time server preparation for an Akademus environment (run on the VPS, staging first).
# DRY RUN by default: prints what it would do. Add --apply to execute. Never deletes anything.
#
#   sudo bash deploy/server-setup.sh --env staging --deploy-path /var/www/akademus-staging \
#        --php-bin /usr/bin/php8.2 --user deploy --web-group www-data [--from /var/www/old-app] [--apply]
#
#   --from   existing app directory: its .env and writable/ are COPIED into shared/ (originals untouched)
#
# Afterwards: point the web server at <deploy-path>/current/public (see docs/CI-CD.md) and deploy once.
set -euo pipefail

ENVIRONMENT="" DEPLOY_PATH="" PHP_BIN="" DEPLOY_USER="" WEB_GROUP="www-data" FROM="" APPLY=false
while [[ $# -gt 0 ]]; do
  case "$1" in
    --env) ENVIRONMENT="$2"; shift 2 ;;
    --deploy-path) DEPLOY_PATH="${2%/}"; shift 2 ;;
    --php-bin) PHP_BIN="$2"; shift 2 ;;
    --user) DEPLOY_USER="$2"; shift 2 ;;
    --web-group) WEB_GROUP="$2"; shift 2 ;;
    --from) FROM="${2%/}"; shift 2 ;;
    --apply) APPLY=true; shift ;;
    *) echo "Unknown option $1" >&2; exit 2 ;;
  esac
done

[[ "$ENVIRONMENT" =~ ^(staging|production)$ ]] || { echo "--env must be staging or production" >&2; exit 2; }
[[ "$DEPLOY_PATH" == /* && "$DEPLOY_PATH" != "/" ]] || { echo "--deploy-path must be absolute" >&2; exit 2; }
[[ -x "$PHP_BIN" ]] || { echo "--php-bin '$PHP_BIN' is not executable" >&2; exit 2; }
id "$DEPLOY_USER" > /dev/null 2>&1 || { echo "--user '$DEPLOY_USER' does not exist" >&2; exit 2; }
getent group "$WEB_GROUP" > /dev/null || { echo "--web-group '$WEB_GROUP' does not exist" >&2; exit 2; }
[[ -z "$FROM" || -d "$FROM" ]] || { echo "--from '$FROM' is not a directory" >&2; exit 2; }
$APPLY && [[ $EUID -ne 0 ]] && { echo "--apply needs root (sudo)" >&2; exit 2; }

HERE="$(cd "$(dirname "$0")" && pwd)"
PHP_MINOR=$("$PHP_BIN" -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')
FPM_SERVICE="php${PHP_MINOR}-fpm"

run() { if $APPLY; then echo "+ $*"; "$@"; else echo "[dry-run] $*"; fi; }
render() { # template -> destination with placeholders replaced
  local src="$1" dst="$2"
  local out
  out=$(sed -e "s#__DEPLOY_PATH__#${DEPLOY_PATH}#g" -e "s#__PHP_BIN__#${PHP_BIN}#g" -e "s#__DEPLOY_USER__#${DEPLOY_USER}#g" \
            -e "s#__WEB_GROUP__#${WEB_GROUP}#g" -e "s#__ENV__#${ENVIRONMENT}#g" -e "s#__FPM_SERVICE__#${FPM_SERVICE}#g" "$src")
  if $APPLY; then printf '%s\n' "$out" > "$dst"; echo "+ wrote $dst"; else echo "[dry-run] write $dst:"; printf '%s\n' "$out" | sed 's/^/    /'; fi
}

echo "== Directories under $DEPLOY_PATH"
run mkdir -p "$DEPLOY_PATH"/{releases,backups,incoming,shared/writable/{cache,logs,session,uploads,debugbar}}

if [[ -n "$FROM" ]]; then
  echo "== Copying configuration and data from $FROM (originals are left in place)"
  if [[ -f "$DEPLOY_PATH/shared/.env" ]]; then echo "shared/.env already exists - not overwritten"
  elif [[ -f "$FROM/.env" ]]; then run cp -p "$FROM/.env" "$DEPLOY_PATH/shared/.env"
  else echo "WARNING: $FROM/.env not found"; fi
  [[ -d "$FROM/writable" ]] && run cp -a "$FROM/writable/." "$DEPLOY_PATH/shared/writable/"
fi

if [[ ! -f "$DEPLOY_PATH/shared/.env" && -z "$FROM" ]]; then
  echo "== shared/.env does not exist: create it by hand before the first deploy. Minimum keys:"
  cat <<'EOF'
    CI_ENVIRONMENT = production
    app.baseURL = 'https://your-site/'
    encryption.key = hex2bin:<64 hex chars>   # php -r 'echo bin2hex(random_bytes(32));'  - never change it later
    database.default.hostname / database / username / password / DBDriver = MySQLi
    email.* , api.groq* , SMARTPAY_SECRET_KEY , SIS_API_URL , SIS_API_KEY   (see the repo's env template)
EOF
fi

echo "== Ownership and permissions"
run chown -R "$DEPLOY_USER:$WEB_GROUP" "$DEPLOY_PATH"
run chmod 750 "$DEPLOY_PATH" "$DEPLOY_PATH"/{releases,backups,incoming,shared}
run chmod 700 "$DEPLOY_PATH/backups"
run find "$DEPLOY_PATH/shared/writable" -type d -exec chmod 2770 {} +
[[ -f "$DEPLOY_PATH/shared/.env" ]] && run chmod 640 "$DEPLOY_PATH/shared/.env"

echo "== Email worker (systemd) and scheduled commands (cron)"
run mkdir -p /etc/akademus
if $APPLY; then printf 'DEPLOY_PATH=%s\nPHP_BIN=%s\n' "$DEPLOY_PATH" "$PHP_BIN" > "/etc/akademus/${ENVIRONMENT}.conf"; echo "+ wrote /etc/akademus/${ENVIRONMENT}.conf"
else echo "[dry-run] write /etc/akademus/${ENVIRONMENT}.conf (DEPLOY_PATH, PHP_BIN)"; fi
render "$HERE/systemd/akademus-mail@.service" /etc/systemd/system/akademus-mail@.service
render "$HERE/cron/akademus.cron" "/etc/cron.d/akademus-${ENVIRONMENT}"
run chmod 644 "/etc/cron.d/akademus-${ENVIRONMENT}"
run systemctl daemon-reload
run systemctl enable "akademus-mail@${ENVIRONMENT}"
echo "(The worker starts with the first deploy, once ${DEPLOY_PATH}/current exists.)"

echo "== sudo rules for the deploy user"
render "$HERE/sudoers/akademus-deploy" /etc/sudoers.d/akademus-deploy
if $APPLY; then
  chmod 440 /etc/sudoers.d/akademus-deploy
  visudo -cf /etc/sudoers.d/akademus-deploy || { rm -f /etc/sudoers.d/akademus-deploy; echo "Invalid sudoers file removed" >&2; exit 1; }
fi

cat <<EOF

== Next steps (manual)
1. Web server document root: ${DEPLOY_PATH}/current/public
   nginx:  root ${DEPLOY_PATH}/current/public;
           fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
           fastcgi_param DOCUMENT_ROOT   \$realpath_root;      # resolve the symlink per request
   Apache: DocumentRoot ${DEPLOY_PATH}/current/public  (with AllowOverride All for public/.htaccess)
   PHP-FPM must run as a user in group '${WEB_GROUP}'.
2. GitHub environment '${ENVIRONMENT}': DEPLOY_PATH=${DEPLOY_PATH}  PHP_BIN=${PHP_BIN}
   VPS_KNOWN_HOSTS = output of: ssh-keyscan -p <SSH_PORT> <VPS_HOST>
3. Run the "Deploy ${ENVIRONMENT}" workflow (staging: Actions > Deploy staging > Run workflow).
EOF
$APPLY || echo "This was a dry run. Re-run with --apply to make these changes."
