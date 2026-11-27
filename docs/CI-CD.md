# Akademus CI/CD

How code gets from a pull request to staging and production, what every check does, what has to be configured
in GitHub and on the servers, and what to do when something fails.

- [1. Overview](#1-overview)
- [2. Workflows and triggers](#2-workflows-and-triggers)
- [3. The CI gate (ci.yml): every job](#3-the-ci-gate-ciyml-every-job)
- [4. Deployment lifecycle](#4-deployment-lifecycle)
- [5. Releasing to production](#5-releasing-to-production)
- [6. Required GitHub settings](#6-required-github-settings)
- [7. GitHub Environments: secrets and variables](#7-github-environments-secrets-and-variables)
- [8. Server setup (one time per environment)](#8-server-setup-one-time-per-environment) — details in [SERVER-SETUP.md](SERVER-SETUP.md)
- [9. CodeIgniter 4 deployment notes](#9-codeigniter-4-deployment-notes)
- [10. Database: migrations, seeding, backups, restore](#10-database-migrations-seeding-backups-restore)
- [11. Rolling back](#11-rolling-back)
- [12. Running the checks locally](#12-running-the-checks-locally)
- [13. Troubleshooting](#13-troubleshooting)

---

## 1. Overview

```
 feature branch ──PR──► main ──(merge)──► deploy-staging.yml ──► staging
                  │                         ├─ ci.yml (exact merge commit)
                  │                         └─ deploy-reusable.yml (environment: staging)
                  │
                  └─ ci.yml runs on every PR push ── "CI / CI gate" must be green to merge

 git tag v1.4.0 (commit on main) ──► deploy-production.yml ──► production
                                      ├─ validate: SemVer tag, commit on main, newer than last release
                                      ├─ ci.yml only if this exact commit has no green CI run yet
                                      ├─ deploy-reusable.yml (environment: production, reviewer approval)
                                      └─ GitHub Release with generated notes
```

Principles:

- **One gate.** `ci.yml` is the only definition of "green". PRs, staging and production all use it.
- **Exact commit.** Everything (tests, bundle, deploy, health check) is pinned to one 40-character SHA. The server
  reports the SHA it serves at `/health`, and the deploy only succeeds when it matches.
- **No direct pushes to main.** Changes arrive through pull requests; the branch ruleset enforces it.
- **Nothing secret leaves GitHub or the server.** CI never creates or reads a `.env`; bundles contain no `.env`,
  no `writable/`, no tests; the server's `.env` and data live in `shared/` and are only linked into releases.

## 2. Workflows and triggers

| File | Trigger | Purpose |
|---|---|---|
| `.github/workflows/ci.yml` | PR into `main` (opened, synchronize, reopened, ready for review); `workflow_call`; weekly schedule; manual | The CI gate. The weekly/manual run also runs Firefox + WebKit E2E. |
| `.github/workflows/deploy-staging.yml` | push to `main` (i.e. a merged PR); manual (main only) | CI on the merge commit, then deploy to **staging** |
| `.github/workflows/deploy-production.yml` | push of a tag `v*` | Validate the release, reuse or run CI, deploy to **production**, publish a GitHub Release |
| `.github/workflows/deploy-reusable.yml` | `workflow_call` only | Shared build + deploy logic for both environments |
| `.github/actions/setup-app` | composite action | PHP + extensions + Composer cache + install, used by every PHP job |
| `.github/scripts/` | scripts used by the workflows | `deploy-remote.sh` (the server-side deploy, shipped inside every release artifact) and `bundle-exclude.txt` (what stays out of the release). `.github/` itself is never part of the release |
| `.github/dependabot.yml` | weekly | Updates pinned actions, Composer and npm packages through PRs |

**Why CI runs again after a merge.** Merging creates a new commit on `main` that was never tested as such (it combines
the PR with whatever else landed). Staging therefore runs the full gate on that exact commit before deploying.
Production does **not** re-run CI when that commit already has a green `ci.yml` or `deploy-staging.yml` run, so a
normal release costs no extra CI time; if no green run exists (e.g. staging's run was cancelled), it runs CI first.

**Concurrency.**
- PR runs: one per PR; a new push cancels the previous run.
- `staging-pipeline` / `production-pipeline`: one pipeline per environment at a time; never cancelled mid-way.
  A newer pending run replaces an older *pending* one (the latest main wins).
- `deploy-staging` / `deploy-production` (deploy job): plus a `flock` on the server, so two deploys can never overlap.

## 3. The CI gate (ci.yml): every job

All jobs check out the commit under test (`SHA`), use least-privilege `permissions`, pinned action SHAs and the
PHP version in `.ci/php-version`. **`CI gate`** is the only required status check: it fails if any job below did
not succeed (`php-next` and the Firefox/WebKit E2E runs are informational and never block).

| Job | What it checks | Fails when | Fix |
|---|---|---|---|
| **Dependencies & lockfiles** (`deps`) | `composer validate --strict` (lock in sync), `composer dump-autoload --strict-psr` (namespace/path case — Linux is case-sensitive), `npm ci` + `npm ls` | lock out of date, PSR-4 mismatch, broken npm lock | `composer update --lock`, fix the namespace or folder name, `npm install` |
| **Code style** | `php-cs-fixer` with CodeIgniter's coding standard (`.php-cs-fixer.dist.php`, `app/Views` excluded). **All risky rules are off**: the formatter only changes layout, never behaviour (e.g. it will not turn `==` into `===` — MySQLi returns IDs as strings) | any file not formatted | `composer cs:fix` and commit |
| **Static analysis** | PHPStan level 5 (`phpstan.neon`) | any error | fix the code; do not suppress |
| **Maintainability & architecture** | PHPMD (`phpmd.xml`) and deptrac layer rules (`deptrac.yaml`) — existing findings are in `phpmd.baseline.xml` / `deptrac.baseline.yaml` | a **new** finding | refactor; regenerate a baseline (`composer md:baseline` / `deptrac:baseline`) only for a deliberate, reviewed exception |
| **Security** | `composer audit --locked`; Gitleaks CLI (PR commits, or the whole tree when deploying); Semgrep (`p/php`, `p/secrets`, `p/security-audit`) | any advisory, any secret, any high-severity (error) Semgrep finding | update the package; **remove and rotate** a leaked secret; fix the code. Results are also uploaded to code scanning when available. |
| **Database** | MySQL 8.4: `spark migrate` on an empty DB → all applied → `migrate:rollback` to zero (every `down()`) → nothing left → migrate again → `db:seed DatabaseSeeder` twice (idempotent) → `.ci/schema-integrity.php` (InnoDB/utf8mb4, primary keys, indexed FKs, column collations, no orphan rows, views queryable) | any step | fix the migration/seeder |
| **PHPUnit** (`tests`) | unit, database and feature suites with PCOV coverage; **coverage ratchet** (`.ci/coverage-baseline.txt`); no ERROR/WARNING/NOTICE/deprecation in `writable/logs` (`.ci/log-allowlist.txt`) | failing test, coverage drop > 0.1 point, unexpected log entries | fix; add tests. When coverage rises, raise the baseline in the same PR (`php .ci/coverage-ratchet.php build/logs/clover.xml --update`) |
| **Routes, filters, translations, filesystem** (`app-config`) | routes load; `adminAuth` / `officerAuth` attached to the protected areas; every `lang()` key exists; `writable/` layout, no committed runtime files / `.env` / keys / SQL dumps, no stray PHP in `public/`; `shellcheck`; `actionlint` | any | as reported |
| **Deploy rehearsal** | runs the real `.github/scripts/deploy-remote.sh` on the runner against MySQL in a CloudPanel-like directory: deploy → verify → finalize; failing migration (live release untouched); failed verification (rollback); finalize removes failed releases; production version guard; foreign `.env` / real `public/` refused | any scenario | fix `.github/scripts/deploy-remote.sh` |
| **E2E (chromium)** | Playwright suite against `php spark serve` + a migrated, seeded MySQL; throwaway credentials from `.ci/e2e-credentials.sh` | any test (after 2 retries) | download the `playwright-chromium` artifact (report + traces + server logs) |
| **Next PHP version** (`php-next`, non-blocking) | PHPStan + unit tests on the next PHP version (`PHP_NEXT` in `ci.yml`, currently 8.6) | never — problems show as a yellow **warning** and in the job summary; the job stays green | usually the framework or a dependency is not ready yet: wait for their update (Dependabot); fix it in our code only if the warning points to `app/` |

Other test coverage worth knowing (inside PHPUnit):

- `AnonymousAccessTest` requests **every GET route** without a session and fails if a non-public page answers 200.
  A new public page must be added to `PUBLIC_ROUTES` on purpose.
- `EmailTest` renders every `EmailService::send*()` email with a mock mailer (nothing is sent), checks escaping, and
  checks that every queued email type has a handler in `php spark emails:send`.
- `HealthEndpointTest`, `AdminAuthFilterTest`, `MigrationsTest`, `SeedersTest`, `PublicPagesTest`.

Artifacts (kept 7–14 days): `phpunit-reports` (JUnit, Clover, HTML coverage, logs), `schema` (`schema.sql`),
`semgrep-sarif`, `playwright-<browser>` (on failure), `release-<env>-<sha>` (`release.tar.gz`, `deploy-remote.sh`, `SHA256SUMS`).

## 4. Deployment lifecycle

Atomic releases on a CloudPanel site, deployed as the **site user without sudo** (one-time server setup:
[SERVER-SETUP.md](SERVER-SETUP.md)). Layout under `DEPLOY_PATH` (`/home/<site-user>/htdocs/<domain>`):

```
shared/.env  shared/writable/  shared/backups/    data - created once by hand, never touched by CI
releases/<UTC yyyymmddThhmmssZ>-<sha7>/           code; .env and writable are symlinks into shared/
current -> releases/<id>                          the live release
public  -> current/public                         CloudPanel's web root; nginx resolves it with $realpath_root
.deploy/                                          lock, state, uploads
```

`deploy-reusable.yml` runs two jobs.

**build** (no secrets, no environment):
1. Check out the exact SHA and verify `HEAD` matches.
2. `composer install --no-dev --classmap-authoritative --no-scripts` on the PHP version from `.ci/php-version`.
3. Write `REVISION` (the SHA) and `RELEASE` (tag or `main@sha`).
4. `release.tar.gz` = everything except `.github/scripts/bundle-exclude.txt` (no `.git`, `.github`, `.env*`,
   `writable/`, tests, E2E, Node, CI configs). The build fails if it contains any of those or lacks
   `vendor/autoload.php`.
5. Upload the artifact: `release.tar.gz` + `deploy-remote.sh` + `SHA256SUMS`. The server runs the script from the
   artifact, so the script that deploys is exactly the one CI tested for that commit.

**deploy** (`environment: staging|production` → approval rules and environment secrets apply here only):
1. Validate the environment's secrets/variables (`DEPLOY_PATH` must be a CloudPanel `…/htdocs/…` path; production
   refuses `TLS_INSECURE=true`).
2. Download the artifact, verify `SHA256SUMS`.
3. Configure SSH with `VPS_PRIVATE_KEY` and **strict host key checking** against `VPS_KNOWN_HOSTS`; test the login.
4. `scp` the artifact to `DEPLOY_PATH/.deploy/incoming/<id>/` (connection failures retried 3×).
5. `deploy-remote.sh deploy` on the server:

| Step | What happens | On failure |
|---|---|---|
| lock | `flock .deploy/lock` | waits up to 10 min, then fails |
| preflight | path is inside `htdocs` and owned by the SSH user; `PHP_BIN` version = `.ci/php-version` and extensions; `shared/.env` has the required keys, `app.baseURL` host = `SITE_URL` host, production has `CI_ENVIRONMENT = production`; `mysqldump`; `shared/writable` writable; ≥ 1 GB free; `public` is not a real directory; production **version guard** (never an older tag than the live one) | fails, nothing changed |
| unpack | checksums; `releases/<id>/`; `REVISION` = SHA; `.env` → `../../shared/.env`; `writable` → `../../shared/writable`; `.release.json` (sha, version, previous release) | the release is marked failed by the rollback step |
| boot check | `php spark routes` in the new release (loads config, filters and routes) | fails, nothing switched |
| backup | `mysqldump --single-transaction --no-tablespaces` → `shared/backups/<env>-<id>.sql.gz` (0600); credentials go from `shared/.env` into a temporary 0600 option file, never printed | fails, nothing migrated or switched |
| migrate | `php spark migrate` (output checked for exceptions, then no pending migration left) and `php spark db:seed ReferenceDataSeeder` **from the new release, before switching** | fails, **live release untouched** |
| switch | atomic `ln -s` + `mv -T` of `current`; first deploy creates `public -> current/public`; `spark cache:clear`; `spark emails:restart` (the worker exits, Supervisor restarts it on the new release) | — |

6. **Verify** from outside and on the server: `SITE_URL/health` reports `status: ok` **and** the new SHA (12 tries ×
   5 s); the home page answers 200 and its stylesheet is served; `deploy-remote.sh verify` checks the live
   `REVISION`, the CodeIgniter environment and that no migration is pending.
7. **Rollback** (only if a step after the upload failed): `deploy-remote.sh rollback` marks the release `.failed`
   and, if it was already switched in, points `current` back to the previous release, clears the cache and restarts
   the worker. The database is **not** rolled back (see §10).
8. **Finalize** (only on success): mark the release `.healthy`; keep the 5 newest healthy releases (always the live
   and the previous one), delete failed ones; keep 5 (staging) / 15 (production) backups; remove the upload.
9. Job summary (outcome of deploy, verification and rollback, migration output); remove the SSH key (always).

**Zero downtime, no sudo.** Code is switched atomically; requests in flight finish on the old release. nginx passes
`$realpath_root` to PHP-FPM, so each request resolves `public → current → releases/<id>` once and OPcache sees new
file paths after a switch — no PHP-FPM reload is needed. The email worker restarts itself on a signal. Migrations
run **before** the switch while the old code is still serving, so every migration must be compatible with the
currently deployed code (see §10). No maintenance mode is used; if a change truly needs one, do it in two releases.

## 5. Releasing to production

1. Make sure the commit is merged into `main` and staging looks good (staging deploys every merge).
2. Tag that commit with the next version and push the tag:

   ```console
   git checkout main && git pull
   git tag -a v1.4.0 -m "v1.4.0"          # or: git tag -a v1.4.0 <sha> -m "v1.4.0"
   git push origin v1.4.0
   ```

3. Approve the deployment in **Actions → Deploy production → Review deployments** (required reviewers).
4. When it finishes, a GitHub Release `v1.4.0` with generated notes is created.

Rules enforced by `validate`:

- Tag format `vMAJOR.MINOR.PATCH` only (`v1.4.0`; not `1.4.0`, `v1.4`, `v1.4.0-rc.1`).
- The tagged commit must be on `main`.
- The tag must be newer than every existing release tag (rollbacks use a **new** tag, see §11).
- CI must have passed for that exact commit (reused, or run now).

## 6. Required GitHub settings

Apply these once (repository **Settings**, admin rights needed).

**Branch ruleset for `main`** (Settings → Rules → Rulesets → New branch ruleset; target: default branch)
- Restrict deletions; block force pushes.
- Require a pull request before merging: 1 approval, dismiss stale approvals on new commits, require conversation resolution.
- Require status checks to pass: add **`CI gate`** (shown as *CI / CI gate*); require branches to be up to date before merging.
- Bypass list: empty (administrators only for emergencies, and documented).
- This is what makes direct pushes to `main` impossible.

**Tag ruleset for releases** (New tag ruleset; target pattern `v*`)
- Restrict creations, updates and deletions to the release managers (bypass list = those people / a team).

**Environments** (Settings → Environments)
- `staging` → Deployment branches and tags: **Selected branches** → `main`.
- `production` → Deployment branches and tags: **Selected tags** → `v*`; **Required reviewers**: the release
  approvers; enable **Prevent self-review**; optional wait timer.

**Actions** (Settings → Actions → General)
- Workflow permissions: **Read repository contents** (the workflows request anything else per job).
- Allow actions: GitHub-owned + `shivammathur/setup-php` (all others are run as pinned binaries/images).
- Fork PR workflows: require approval for outside contributors.

**Code security** (optional): enable code scanning to see Semgrep results in the Security tab
(private repositories need GitHub Code Security; without it the upload step is skipped and the gate still enforces).

## 7. GitHub Environments: secrets and variables

Both environments (`staging`, `production`) already contain these; the workflows only read them inside the
`deploy` job of `deploy-reusable.yml`.

| Name | Kind | Used for |
|---|---|---|
| `VPS_HOST` | secret | SSH host |
| `VPS_USERNAME` | secret | SSH user (the deploy user; on CloudPanel the site user) |
| `VPS_PRIVATE_KEY` | secret | SSH private key (ed25519 recommended, no passphrase, dedicated to deploys) |
| `VPS_KNOWN_HOSTS` | secret | `ssh-keyscan -p <port> <host>` output; host keys are verified strictly |
| `GITLEAKS_LICENSE` | secret | **not needed** — CI uses the Gitleaks CLI, which requires no license (the secret can stay, it is unused) |
| `DEPLOY_PATH` | variable | the CloudPanel site directory `/home/<site-user>/htdocs/<domain>` (leading `/`; must contain `/htdocs/`) |
| `PHP_BIN` | variable | PHP CLI on the server as an absolute path, e.g. `/usr/bin/php8.5` (its major.minor must equal `.ci/php-version`) |
| `SITE_URL` | variable | public URL, e.g. `https://staging.example.org` (used for the health checks and the environment link) |
| `SSH_PORT` | variable | SSH port (default 22) |
| `TLS_INSECURE` | variable | `true` skips TLS verification for the health check — staging with a self-signed certificate only; refused for production. Set `false` once Let's Encrypt is active ([SERVER-SETUP.md §1](SERVER-SETUP.md#1-create-the-site)) |

Current staging values are listed in [SERVER-SETUP.md §4](SERVER-SETUP.md#4-ssh-access-for-github-actions).

## 8. Server setup (one time per environment)

The one-time setup of a Hostinger VPS with CloudPanel is described step by step in **[SERVER-SETUP.md](SERVER-SETUP.md)**.
It is done by hand, on purpose: the pipeline deploys as the CloudPanel site user without sudo and never creates
sites, databases, services or cron jobs. In short, per environment:

1. CloudPanel PHP 8.5 site (Generic); Root Directory `<domain>/public`; placeholder `public/` removed; vhost uses
   `$realpath_root`; PHP upload limits; Let's Encrypt.
2. Database and user; `shared/.env` (chmod 600) with `app.baseURL` = `SITE_URL`.
3. Deploy key for the site user; the GitHub environment's secrets and variables (§7).
4. Supervisor program `<site-user>-mail` for `php spark emails:send` (as root).
5. Three cron jobs in CloudPanel's *Cron Jobs* tab (as the site user):

| Command | Schedule | Why |
|---|---|---|
| `php spark sis:sync` | every minute | SIS handoff retries start after 1 minute (backoff 1, 5, 15, 60, 240, 720 min) |
| `php spark notifications:application` | hourly at :05 | reminder levels start at 2 hours |
| `php spark notifications:requirements` | hourly at :35 | same, offset to spread load |
| `php spark emails:send` | Supervisor, always running; every deploy runs `spark emails:restart` | long-running worker polling `email_queue` every 2 s |

## 9. CodeIgniter 4 deployment notes

- **Environment**: `shared/.env` must set `CI_ENVIRONMENT = production` (errors hidden, logging threshold 4) on
  staging and production. The deploy refuses anything else on production and warns on staging.
- **Web root**: only `public/` is served; `app/`, `vendor/`, `writable/` and `.env` are outside the document root.
- **writable/**: one shared directory (`shared/writable`) linked into every release, so sessions, logs, uploads and
  the worker restart signal (`writable/worker-restart`) survive deploys. It is never part of a release.
- **Caches**: `writable/cache` is cleared on every deploy (`spark cache:clear`). `spark optimize` is deliberately
  **not** used: it edits `app/Config/Optimize.php` and runs `composer install` on the server; the bundle already
  ships an authoritative Composer classmap.
- **OPcache**: no reload needed — nginx passes `$realpath_root`, so a new release means new file paths for OPcache.
- **Email worker**: `php spark emails:restart` (run by every deploy and rollback) makes `emails:send` finish its
  batch and exit; Supervisor starts it again on the live release. Use it after editing `shared/.env` too.
- **Encryption**: personal data columns are encrypted with `encryption.key`. Never change it on a running
  environment and keep a copy in your password manager — without it the data cannot be decrypted.
- **Health endpoint**: `GET /health` → `{"status","release","checks":{"database","writable","migrations"}}`,
  200 or 503, no details. Safe to use for uptime monitoring too.
- **PHP upload limits**: payment proofs may be up to 10 MB, so PHP-FPM needs `upload_max_filesize ≥ 10M` and
  `post_max_size ≥ 12M` (CI uses 12M / 16M). The deploy preflight warns when the CLI value is lower.
- **Debug toolbar**: shown in `development` only; `CI_DEBUG_TOOLBAR=false` switches it off (the E2E job does).
- **Email**: production must have working `email.*` settings; staging should use a test SMTP inbox (e.g. a
  sandbox service) so real applicants never receive staging mail.

## 10. Database: migrations, seeding, backups, restore

- The schema is defined only by `app/Database/Migrations` (one file per table, then views). There is no SQL dump.
- **Fresh database**: `php spark migrate && php spark db:seed DatabaseSeeder`.
  `DatabaseSeeder` = `ReferenceDataSeeder` (always) + `DemoUserSeeder` (not in production; fictional
  `demo.*@akademus.test` accounts, password from `DEMO_PASSWORD` or printed once) + `E2ETestSeeder` (only on the
  E2E database). Deploys run only `ReferenceDataSeeder`, which never overwrites existing rows.
- **Writing migrations** (they run while the previous release is still live):
  - *expand / contract*: add new columns/tables first (nullable or with defaults), deploy code that uses them,
    remove old columns in a **later** release;
  - never rename or drop something the currently deployed code still uses;
  - always implement `down()` — CI rolls every migration back.
- **Backups**: every deploy writes `DEPLOY_PATH/shared/backups/<env>-<release id>.sql.gz` (0600) before migrating.
  Copy production backups off the server regularly (they are not uploaded anywhere by CI).
- **Restoring the database** (only if a migration damaged data and the previous release cannot run):

  ```console
  # on the server, as the site user (no sudo needed)
  cd $DEPLOY_PATH
  ls -1 shared/backups/                            # pick the backup taken right before the bad deploy
  cat releases/*/.release.json                     # find the previous release
  gunzip -c shared/backups/production-<id>.sql.gz | mysql --defaults-extra-file=<(printf '[client]\nuser=...\npassword=...\n') <database>
  ln -sfn releases/<previous-release> .current.tmp && mv -Tf .current.tmp current
  cd current && php8.5 spark cache:clear && php8.5 spark emails:restart
  ```

  Data written between the backup and the restore is lost — prefer fixing forward with a new migration.

## 11. Rolling back

- **Automatic**: if verification fails after the switch, the workflow runs `deploy-remote.sh rollback`, which points
  `current` back to the previous release. A failed migration never switches in the first place.
- **Code rollback, production**: tag the last good commit with a **new, higher** version and push it
  (`git tag -a v1.4.1 <good-sha> -m "Rollback to <good-sha>"`). The normal pipeline deploys it (CI is reused).
- **Code rollback, staging**: revert the bad PR on `main` (through a PR) — staging redeploys.
- **Emergency** (GitHub unavailable): as the site user, point `current` at a previous `releases/` directory and
  run `spark cache:clear` + `spark emails:restart` (commands in §10). Fix forward afterwards.
- **Production never moves to an older tag**: the server-side version guard refuses it; roll back with a new, higher tag.
- The database is never rolled back automatically (see §10).

## 12. Running the checks locally

```console
composer check                 # code style, PHPStan, PHPMD, deptrac
composer cs:fix                # apply code style
composer test                  # PHPUnit (needs database.tests.* in .env, see tests/README.md)
php .ci/check-lang-keys.php
bash .ci/check-writable.sh
DB_HOST=localhost DB_USER=root DB_NAME=<db> php .ci/schema-integrity.php
npm run e2e                    # Playwright (see e2e/README.md)
```

## 13. Troubleshooting

| Symptom | Cause / fix |
|---|---|
| PR shows "Expected — Waiting for status to be reported" for *CI gate* | The ruleset requires the check but CI has not run: push a commit or close/reopen the PR. |
| `deps` fails on `--strict-psr` | A class namespace does not match its folder (case matters on Linux). Rename consistently; on Windows use `git mv` for case-only renames. |
| `code-style` fails | `composer cs:fix`, commit. |
| `maintainability` fails on an old file | A *new* finding of a rule in that file. Fix it; only regenerate the baseline for an agreed exception. |
| Gitleaks finding | Remove the secret from the code **and rotate it** (it is in git history). For a confirmed false positive, add a narrow entry to `.gitleaks.toml`. |
| `composer audit` fails | `composer update <package>` (or wait for a fix and document the advisory). |
| `database` fails at rollback | A migration's `down()` does not undo its `up()`. |
| `tests` fails on coverage | Coverage dropped; add tests. |
| `tests` fails on logs | Code logged an error/warning during tests; see the `phpunit-reports` artifact (`writable/logs`). |
| `AnonymousAccessTest` fails | A new route renders without login. Protect it, or add it to `PUBLIC_ROUTES` if it is public by design. |
| `e2e` fails | Download `playwright-chromium`: HTML report, traces (`npx playwright show-trace`), `serve.log`, app logs. |
| Deploy: `Environment '<env>' is missing: secret VPS_…` although the secrets are set | The calling workflow (`deploy-staging.yml` / `deploy-production.yml`) lost `secrets: inherit`: a reusable workflow only sees the secrets its caller passes, environment secrets included (variables are not affected). |
| Deploy: `Host key verification failed` | `VPS_KNOWN_HOSTS` is missing/outdated, or was scanned for the hostname while `VPS_HOST` is the IP (or the other way round): `ssh-keyscan -p <port> <VPS_HOST value>` → update the secret. A reinstalled VPS has new host keys. |
| Deploy: `Permission denied (publickey)` | Public key not in the deploy user's `authorized_keys`, or wrong `VPS_USERNAME`. |
| Deploy: `DEPLOY_PATH must be absolute` | The variable lacks the leading `/` (e.g. `home/...` instead of `/home/...`). |
| Deploy: `PHP_BIN is PHP x but CI tested PHP y` | Align the server's PHP with `.ci/php-version` (or change `.ci/php-version` in a PR — CI then tests that version). |
| Deploy: `must be inside a CloudPanel htdocs directory` / `is not owned by` | `DEPLOY_PATH` is not the site directory, or `VPS_USERNAME` is not that site's user. |
| Deploy: `shared/.env is missing` / `… is missing or empty` | Create or complete `shared/.env` ([SERVER-SETUP.md §3](SERVER-SETUP.md#3-directory-layout-and-sharedenv)). |
| Deploy: `app.baseURL host … differs from SITE_URL host` | The `.env` belongs to another site, or `SITE_URL` is wrong. |
| Deploy: `public is a real directory` | Remove CloudPanel's placeholder `public/` once ([SERVER-SETUP.md §1](SERVER-SETUP.md#1-create-the-site)). |
| Deploy: `Database backup failed` | Wrong database credentials in `shared/.env`, or MySQL asks for the `PROCESS` privilege ([SERVER-SETUP.md §2](SERVER-SETUP.md#2-database)). |
| Deploy: `refusing to deploy older vX` | Production runs a newer tag. Roll back with a new, higher tag (§11). |
| Site shows CloudPanel 404 / placeholder | Root Directory is not `<domain>/public`, or no deploy has run yet ([SERVER-SETUP.md §1](SERVER-SETUP.md#1-create-the-site)). |
| Site still shows the old release after a deploy | The vhost uses `$document_root` instead of `$realpath_root`. |
| Scheduled commands run twice / two email workers | Files from the earlier `server-setup.sh` layout are still installed ([SERVER-SETUP.md §8](SERVER-SETUP.md#8-moving-from-the-earlier-layout-server-setupsh)). |
| Deploy: migration failed | Live site untouched. Fix the migration in a PR. The backup path is in the log. |
| Deploy: verification failed → rolled back | Check `SITE_URL/health` checks, `shared/writable/logs`, PHP-FPM / nginx logs, the Root Directory (`<domain>/public`) and `$realpath_root`. |
| Production deploy did not start after tagging | Tag not `vX.Y.Z`, commit not on `main`, tag not newer than the last release, or waiting for reviewer approval. |
| Deploy waits for a long time | Another pipeline for the same environment is running (concurrency) or the server lock is held. |
| Emails not sent after deploy | `supervisorctl status <site-user>-mail`; `shared/writable/logs/email-worker.log`; before the first deploy start it once with `supervisorctl start`. |
