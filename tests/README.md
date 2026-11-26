# Akademus PHPUnit tests

| Suite | Folder | Needs a database | What it covers |
|---|---|---|---|
| `unit` | `tests/unit` | no | Bootstrap and configuration sanity |
| `database` | `tests/database` | yes | Migrations build the full schema; seeders are complete and idempotent |
| `feature` | `tests/feature` | yes | HTTP: public pages, `/health`, admin auth filter, and that **every non-public GET route refuses anonymous visitors** |

Browser (Playwright) tests live in `e2e/` — see `e2e/README.md`.

## Database

Tests use the `tests` database group (MySQL, no table prefix — `app/Config/Database.php`). Every database test
class rolls the schema back, runs all `App` migrations and seeds `ReferenceDataSeeder` (`tests/_support/DatabaseTestCase.php`),
so **use a dedicated, disposable database** — never the dev one.

Local setup (XAMPP):

1. Create an empty database, e.g. `akademus_ci` (`utf8mb4` / `utf8mb4_general_ci`).
2. Add the connection to your `.env`:

   ```ini
   database.tests.hostname = localhost
   database.tests.database = akademus_ci
   database.tests.username = root
   database.tests.password =
   ```

   (CI sets the same values through `database_tests_*` environment variables.)

## Running

```console
composer test                                  # all suites
vendor/bin/phpunit --testsuite unit            # one suite
vendor/bin/phpunit --filter AnonymousAccess    # one test class
composer test -- --coverage-text               # with coverage (needs PCOV or Xdebug)
```

## When a test fails

- **AnonymousAccessTest** — a GET route answered 200 to a visitor who is not logged in. Protect it (route filter or
  controller check). Only if the page is public by design, add its route pattern to `PUBLIC_ROUTES` in the test.
- **SeedersTest** — reference data changed. Update the seeder and the expected counts together.
- **MigrationsTest** — a migration failed or a `Create<Name>Table` migration did not create `<name>`.
