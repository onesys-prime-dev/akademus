# Akademus Server Setup (Hostinger VPS + CloudPanel, one time per environment)

How to prepare the VPS so the GitHub Actions pipeline ([CI-CD.md](CI-CD.md)) can deploy to it. Do this once per
environment, **staging first**. Nothing here is automated by the pipeline, on purpose: the deploy user is the
CloudPanel site user **without sudo**, and the pipeline never creates sites, databases, services or cron jobs.

**Target:** Hostinger VPS with the *Ubuntu 24.04 with CloudPanel* template: nginx, PHP 8.5 (FPM), MySQL 8,
Supervisor, cron. Staging and production can be **two CloudPanel sites on the same VPS**, each with its own site
user, database, email worker and cron jobs.

| | Staging | Production |
|---|---|---|
| CloudPanel site user (`VPS_USERNAME`) | `valoris-staging` | e.g. `valoris` |
| Domain | `valoris.staging.srv1404227.hstgr.cloud` | the production domain |
| `DEPLOY_PATH` | `/home/valoris-staging/htdocs/valoris.staging.srv1404227.hstgr.cloud` | `/home/<prod-user>/htdocs/<prod-domain>` |
| Database / DB user | e.g. `valoris_staging` / `valoris_staging` | e.g. `valoris_production` / `valoris_production` |
| Supervisor program (email worker) | `valoris-staging-mail` | `valoris-mail` |
| Cron jobs | CloudPanel → staging site → *Cron Jobs* | CloudPanel → production site → *Cron Jobs* |

Below, `<user>` is the site user and `<domain>` the site's domain. Commands marked **[root]** run on the VPS as
root, **[site]** as the site user (`su - <user>` from root, or SSH as that user), **[local]** on your computer
(Git Bash on Windows).

## 0. The VPS (once)

1. **OS**: hPanel → VPS → *Operating System* → **Ubuntu 24.04 with CloudPanel**. CloudPanel's admin UI is at
   `https://<vps-ip>:8443`; create the admin account on the first visit.
2. **Root SSH**: hPanel → VPS → *Overview* shows the root login; add your own SSH key under *Settings → SSH keys*.
3. **Firewall** (hPanel → VPS → *Security → Firewall*, and CloudPanel → *Admin Area → Security*): allow **22** from
   anywhere (GitHub-hosted runners have no fixed IP), **80** and **443**; restrict **8443** to your own IP.
4. **PHP 8.5** — the version CI tests (`.ci/php-version`); the deploy refuses any other version:

   ```bash
   # [root]
   php8.5 -v
   php8.5 -m | grep -iwE 'intl|mbstring|mysqli|curl|json|openssl|fileinfo'   # all 7 must be listed
   command -v flock mysqldump gzip sha256sum tar curl                        # all must be found
   ```

5. **Supervisor** (runs the email worker, §5):

   ```bash
   # [root]
   apt-get update && apt-get install -y supervisor
   systemctl enable --now supervisor
   ```

## 1. Create the site

In CloudPanel: **Sites → Add Site → Create a PHP Site**, application **Generic**, PHP **8.5**, domain `<domain>`,
site user `<user>` with a strong password (keep it in the password manager). CloudPanel creates the site user and
`/home/<user>/htdocs/<domain>` (= `DEPLOY_PATH`).

Then, for the site:

1. **Root directory.** *Sites → <site> → Settings → Root Directory* must be `<domain>/public` (the site directory's
   `public`). The deploy turns `<domain>/public` into a symlink to `current/public`; do **not** point the root at
   `current/public` yourself.
2. **Remove the placeholder.** CloudPanel may put a sample `public/` (or other files) into the site directory.
   Remove it once, while there is no real data in it:

   ```bash
   # [site]
   cd /home/<user>/htdocs/<domain>
   ls -la                # check there is nothing you need
   rm -rf public         # the deploy refuses to replace a real public/ directory
   ```

3. **Vhost uses `$realpath_root`.** *Sites → <site> → Vhost*. The PHP location must pass the resolved path to PHP-FPM,
   so every request runs entirely in the release `current` points to, and OPcache sees new paths after each deploy
   (that is why no PHP-FPM reload — and no sudo — is needed):

   ```nginx
   fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
   fastcgi_param DOCUMENT_ROOT   $realpath_root;
   ```

   If it uses `$document_root`, change those two lines and save. Also check that `location /` contains
   `try_files $uri $uri/ /index.php?$args;` (CodeIgniter's front controller).
4. **PHP settings** (*Sites → <site> → Settings → PHP Settings*): `upload_max_filesize` ≥ `10M`, `post_max_size` ≥
   `12M`, `memory_limit` ≥ `256M` (payment proofs may be up to 10 MB). The deploy warns when the CLI value is lower;
   the CLI ini is `/etc/php/8.5/cli/php.ini`.
5. **SSL.** *Sites → <site> → SSL/TLS → Actions → New Let's Encrypt Certificate* (needs `<domain>` to resolve to the
   VPS and port 80 open). Until it exists, set the GitHub variable `TLS_INSECURE=true` for **staging only**; set it
   back to `false` afterwards. Production refuses `true`.

## 2. Database

*Sites → <site> → Databases → Add Database*: one database and user per environment (CloudPanel gives the user rights
on its own database only). MySQL listens on `127.0.0.1:3306`.

Every deploy backs the database up with `mysqldump --single-transaction --no-tablespaces` before migrating, as the
site user with the credentials from `shared/.env`. That needs no extra privileges. Only if a deploy fails with
*"Access denied; you need (at least one of) the PROCESS privilege(s)"*, grant it as MySQL root (it gives no access
to other databases):

```sql
GRANT PROCESS ON *.* TO 'valoris_staging'@'localhost';   -- use the host CloudPanel created: SELECT user, host FROM mysql.user;
```

## 3. Directory layout and `shared/.env`

Create the shared directory and the hand-managed `.env` as the **site user** (CI never creates or reads it):

```bash
# [site]
cd /home/<user>/htdocs/<domain>
mkdir -p shared/writable shared/backups
php8.5 -r 'echo "hex2bin:".bin2hex(random_bytes(32)), PHP_EOL;'     # encryption key - new environments only
nano shared/.env
chmod 600 shared/.env
```

After the first deployment the directory looks like this; only `shared/` holds data:

```
<domain>/                        DEPLOY_PATH
  shared/
    .env                         hand-managed, never in git or GitHub
    writable/                    cache, logs, session, uploads - linked into every release
    backups/                     <env>-<release>.sql.gz, taken before every migration (0600)
  releases/<UTC time>-<sha7>/    one per deployment, 5 healthy kept; .env and writable are symlinks into shared/
  current -> releases/<id>       the live release
  public  -> current/public      web root served by nginx
  .deploy/                       lock file, state, temporary uploads
```

`shared/.env` (adjust every value; the deploy checks the marked keys and refuses a `.env` whose `app.baseURL`
host differs from the environment's `SITE_URL`). Copy the remaining keys — `email.*`, Groq, SmartPay,
`SIS_API_URL` / `SIS_API_KEY` — from the repository's `env` template:

```ini
CI_ENVIRONMENT = production                     # REQUIRED on staging too (controls error display); production refuses anything else
app.baseURL = 'https://valoris.staging.srv1404227.hstgr.cloud/'   # REQUIRED, same host as SITE_URL
encryption.key = hex2bin:<64 hex characters>    # REQUIRED; never change it once data exists

database.default.hostname = 127.0.0.1           # REQUIRED
database.default.database = valoris_staging     # REQUIRED
database.default.username = valoris_staging     # REQUIRED
database.default.password = ...                 # REQUIRED key
database.default.DBDriver = MySQLi
database.default.port = 3306
```

**Keep a copy of each `.env` (above all `encryption.key`) in the password manager.** Personal data is encrypted with
that key; without it the data cannot be decrypted. Staging must send mail through a test SMTP inbox, never real SMTP.

To change configuration later: edit `shared/.env`, then run `[site] cd current && php8.5 spark cache:clear` and
restart the worker (`php8.5 spark emails:restart`), or simply redeploy.

## 4. SSH access for GitHub Actions

Create one key pair **per environment** on your own computer (not on the server):

```bash
# [local]
ssh-keygen -t ed25519 -N '' -C 'github-actions akademus staging' -f akademus-staging-deploy
ssh-keygen -t ed25519 -N '' -C 'github-actions akademus production' -f akademus-production-deploy
```

1. Add the `.pub` key to the matching site user: CloudPanel *Sites → <site> → SSH/FTP → SSH Keys*, or as root:

   ```bash
   # [root]
   install -d -m 700 -o <user> -g <user> /home/<user>/.ssh
   echo 'ssh-ed25519 AAAA... github-actions akademus staging' >> /home/<user>/.ssh/authorized_keys
   chown <user>:<user> /home/<user>/.ssh/authorized_keys && chmod 600 /home/<user>/.ssh/authorized_keys
   ```

2. Collect the server host key and **check its fingerprint** against the server's own
   (`[root] ssh-keygen -lf /etc/ssh/ssh_host_ed25519_key.pub`); the two `SHA256:…` values must be identical:

   ```bash
   # [local]
   ssh-keyscan -p 22 -t ed25519 <VPS_HOST> > known_hosts
   ssh-keygen -lf known_hosts
   ```

   * Scan exactly the value you put in `VPS_HOST` (IP *or* hostname — SSH matches it literally).
   * On a port other than 22 the line starts with `[<host>]:<port> ssh-ed25519 …`; keep that form.
   * Staging and production on the same VPS use the same line (same `VPS_HOST` / `SSH_PORT`).
3. Test: `[local] ssh -i akademus-staging-deploy -p 22 valoris-staging@<VPS_HOST> 'php8.5 -v'`.
4. Put the values into the GitHub environment (*Settings → Environments → staging / production*), see
   [CI-CD.md §7](CI-CD.md#7-github-environments-secrets-and-variables):

   | Name | Kind | Staging value |
   |---|---|---|
   | `VPS_HOST` | secret | the VPS IP or `srv1404227.hstgr.cloud` |
   | `VPS_USERNAME` | secret | `valoris-staging` |
   | `VPS_PRIVATE_KEY` | secret | contents of `akademus-staging-deploy` (including the BEGIN/END lines) |
   | `VPS_KNOWN_HOSTS` | secret | the `known_hosts` line |
   | `DEPLOY_PATH` | variable | `/home/valoris-staging/htdocs/valoris.staging.srv1404227.hstgr.cloud` (leading `/`) |
   | `PHP_BIN` | variable | `/usr/bin/php8.5` |
   | `SITE_URL` | variable | `https://valoris.staging.srv1404227.hstgr.cloud` |
   | `SSH_PORT` | variable | `22` |
   | `TLS_INSECURE` | variable | `false` once the Let's Encrypt certificate exists (`true` only until then, staging only) |

5. Delete the private key files from your computer once they are stored in GitHub (or keep them only in the
   password manager).

## 5. Email worker (Supervisor, as root)

`php spark emails:send` is a long-running worker that polls the `email_queue` table. Supervisor keeps it running
as the site user. `/etc/supervisor/conf.d/valoris-staging-mail.conf` (and the same for production with its own
user, path and name):

```ini
[program:valoris-staging-mail]
command=/usr/bin/php8.5 /home/valoris-staging/htdocs/valoris.staging.srv1404227.hstgr.cloud/current/spark emails:send
directory=/home/valoris-staging/htdocs/valoris.staging.srv1404227.hstgr.cloud/current
user=valoris-staging
numprocs=1
autostart=true
autorestart=true
startsecs=5
stopasgroup=true
killasgroup=true
stopwaitsecs=60
redirect_stderr=true
stdout_logfile=/home/valoris-staging/htdocs/valoris.staging.srv1404227.hstgr.cloud/shared/writable/logs/email-worker.log
stdout_logfile_maxbytes=10MB
stdout_logfile_backups=5
```

```bash
# [root]
supervisorctl reread && supervisorctl update
supervisorctl status          # valoris-staging-mail RUNNING (FATAL/BACKOFF before the first deploy, see below)
```

Every deploy and rollback runs `php spark emails:restart`: the worker finishes its current batch and exits, and
Supervisor starts it again through `current/` — i.e. on the new release. No sudo is needed. Before the first
deployment `current/` does not exist yet, so the program shows `FATAL`/`BACKOFF`; run
`supervisorctl start valoris-staging-mail` after the first deploy.

## 6. Scheduled commands (cron, as the site user)

CloudPanel: *Sites → <site> → Cron Jobs → Add Cron Job* — three jobs (they run as the site user; `flock -n` skips
a run while the previous one is still busy; output goes to the shared logs):

| Schedule | Command |
|---|---|
| `* * * * *` (every minute) | `cd /home/<user>/htdocs/<domain>/current && flock -n /tmp/<user>-sis.lock /usr/bin/php8.5 spark sis:sync >> /home/<user>/htdocs/<domain>/shared/writable/logs/cron-sis-sync.log 2>&1` |
| `5 * * * *` (hourly at :05) | `cd /home/<user>/htdocs/<domain>/current && flock -n /tmp/<user>-app-reminders.lock /usr/bin/php8.5 spark notifications:application >> /home/<user>/htdocs/<domain>/shared/writable/logs/cron-notifications.log 2>&1` |
| `35 * * * *` (hourly at :35) | `cd /home/<user>/htdocs/<domain>/current && flock -n /tmp/<user>-req-reminders.lock /usr/bin/php8.5 spark notifications:requirements >> /home/<user>/htdocs/<domain>/shared/writable/logs/cron-notifications.log 2>&1` |

Why these schedules: SIS handoff retries start after 1 minute (backoff 1, 5, 15, 60, 240, 720 min); the shortest
reminder level is 2 hours, so hourly is enough. Check with `[site] crontab -l`.

## 7. First deployment

1. Complete sections 0–6 for the environment and fill its GitHub secrets and variables.
2. Deploy: staging by merging a PR into `main` (or *Actions → Deploy staging → Run workflow*); production by pushing
   a release tag ([CI-CD.md §5](CI-CD.md#5-releasing-to-production)). The first deploy migrates the empty database and
   loads the reference data; it creates `public -> current/public`.
3. `[root] supervisorctl start valoris-staging-mail` if it was not running yet.
4. Check:

   ```bash
   # [local]
   curl -s https://valoris.staging.srv1404227.hstgr.cloud/health      # {"status":"ok","release":"<sha>",...}
   # [site]
   cd /home/<user>/htdocs/<domain> && ls -l public current && cat current/.release.json
   tail -n 20 shared/writable/logs/email-worker.log shared/writable/logs/cron-sis-sync.log
   ```

5. Demo accounts (staging only, optional — deploys never create them):
   `[site] cd current && php8.5 spark db:seed DemoUserSeeder`.
6. Open the site, log in, check that pages load with their CSS and images.

## 8. Moving from the earlier layout (`server-setup.sh`)

If the server was prepared with the former `deploy/server-setup.sh`, remove what it installed — otherwise the jobs
run twice and the old worker keeps running. Use the names that exist on your server:

```bash
# [root]
systemctl disable --now akademus-mail@staging.service valoris-staging-mail.service 2>/dev/null
rm -f /etc/cron.d/akademus-staging /etc/cron.d/valoris-staging
rm -f /etc/systemd/system/akademus-mail@.service /etc/systemd/system/valoris-staging-mail.service
rm -rf /etc/systemd/system/akademus-mail@staging.service.d
rm -f /etc/sudoers.d/akademus-deploy /etc/sudoers.d/akademus-deploy-staging /etc/sudoers.d/valoris-staging
rm -rf /etc/akademus
systemctl daemon-reload
```

Then, in the site directory: move old backups into `shared/backups/` (`mv backups/* shared/backups/`), remove
`incoming/`, `backups/`, `deploy.conf` and `.deploy.lock`, set the CloudPanel Root Directory back to `<domain>/public`
(§1), and continue with §5–§7. Old release directories (`releases/<14 digits>-<sha12>`) are not touched by the new
deploy; delete them by hand after the first successful new deploy.

## Checklist

- [ ] VPS: PHP 8.5 with the 7 extensions, Supervisor installed, firewall 22/80/443
- [ ] CloudPanel site (Generic, PHP 8.5), Root Directory `<domain>/public`, placeholder `public/` removed
- [ ] Vhost uses `$realpath_root`; PHP upload limits; SSL issued (`TLS_INSECURE=false`)
- [ ] Database and user; `shared/.env` (chmod 600) with `app.baseURL` = `SITE_URL`; `encryption.key` stored safely
- [ ] Deploy key for the site user; `VPS_*` secrets and the variables set in the GitHub environment
- [ ] Supervisor program `<user>-mail`; three cron jobs in CloudPanel
- [ ] After the first deploy: `/health` reports the commit; worker running; cron logs written
