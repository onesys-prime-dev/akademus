# Akademus E2E tests (Playwright)

End-to-end tests for the Akademus admissions portal. They drive the real app through Apache/XAMPP against a **separate test database** (`akademus_test`), never the dev database.

- 156 real tests per browser, in 19 module/public spec files. `playwright test --list --project=chromium` shows 159 because it also counts the empty `seed.spec.ts` placeholder and the two sample tests in `example.spec.js`.
- Browsers configured: Chromium, Firefox, WebKit (see [../playwright.config.js](../playwright.config.js)).
- The specs are organised by the module codes (M1 to M19) of the *Valoris Testing Pass 1 (Tasks)* sheet.

---

## 1. One-time setup

1. **XAMPP** running (Apache and MySQL). The app is expected at `http://localhost/akademus/public/` (see `support/base.ts`).
2. **Dependencies:** `npm ci`, then `npx playwright install`.
3. **Test database `akademus_test`** must already exist and be migrated. See [Limitations](#9-limitations-and-known-gaps): this repo cannot build it from scratch yet. Credentials are the `database.tests.*` entries in the root `.env`.
4. **Credentials file `.env.e2e`** in the repo root (git-ignored; Playwright loads it automatically, and the seeder reads it too):

   | Variable | Used for |
   |---|---|
   | `E2E_EMAIL`, `E2E_PASSWORD` | Main applicant that most applicant specs sign in as |
   | `E2E_EXISTING_EMAIL` | An already-registered email (duplicate sign-up test) |
   | `OFFICER_EMAIL`, `OFFICER_PASSWORD` | Admission officer |
   | `ADMIN_EMAIL`, `ADMIN_PASSWORD` | Administrator |
   | `E2E_TEMP_EMAIL`, `E2E_TEMP_PASSWORD` | Applicant that keeps `temp_password=1` |
   | `E2E_FRESH_EMAIL`, `E2E_FRESH_PASSWORD` | Applicant with no application |
   | `E2E_SECOND_EMAIL`, `E2E_SECOND_PASSWORD` | Applicant in a second program (Nursing) |

   If a variable is missing, the tests that need it are **skipped**, not failed (each hook calls `test.skip(!hasXCreds(), …)`).
5. **Switch the app to the test DB and seed it:**
   ```
   npm run e2e:db:on
   npm run e2e:db:seed
   ```

## 2. Running the tests

```
npm run e2e                                   # everything, all 3 browsers
npx playwright test --project=chromium        # one browser (fastest)
npx playwright test e2e/m05-applicants.spec.ts
npx playwright test -g "Bulk update"          # by test title
npx playwright test --ui                      # interactive runner
npx playwright test --workers=1               # one at a time, if something looks racy
npx playwright show-report                    # open the last HTML report
npx playwright test --list                    # list tests without running
```

Notes:
- `global-setup.js` **refuses to start** locally while `E2E_USE_TEST_DB` is off (so you cannot hit the dev DB by accident). It also starts the email queue worker (`php spark emails:send`). Set `E2E_MAIL_WORKER=0` if you run the worker yourself; CI skips it.
- Run `npm run e2e:db:off` when you want to use the app against the dev database again.
- Tests create and delete their own throwaway data and clean up in `finally` blocks, but a crashed run can leave leftovers (see [Flags to monitor](#5-flags-and-state-to-monitor)).

## 3. Test database seeder

`app/Database/Seeds/E2ETestSeeder.php`, run with `npm run e2e:db:seed` (= `php spark db:seed E2ETestSeeder`).

- Safe: throws unless the connection is `akademus_test`. Always uses the `tests` group, whatever the `.env` flag says.
- Idempotent: users are matched by email and updated; everything else is created only if missing. Run it as often as you like, and before a full run to reset the baseline.
- Creates/updates: the admin (password in `admin_credentials`), the officer, the six applicants above, programs BSCS/BSIT/BSN, fee types and payment accounts (via the two existing seeders), and the baseline settings below.
- Also adds **fixtures for the tests that are still `test.fixme`**: applicant `e2e.payer@example.com` with a pending counter payment (reference `E2E-SEED-PENDING`) and a booking in an open exam slot (`E2E Seed Exam Hall`, 60 days ahead). No test uses them yet.
- Does **not**: create tables, create form presets or requirement categories, or delete the `QA …` leftovers from earlier runs.

## 4. Layout

```
e2e/
  m01-authentication.spec.ts   M1   sign-in/up, password recovery, all three logins, logout, access control
  m02-dashboard.spec.ts        M2   applicant dashboard, FAQ/chat, tour; officer dashboard, search, calendar
  m03-follow-up.spec.ts        M3   officer + applicant follow-up messages, officer AI assistant (mocked)
  m04-exam-schedule.spec.ts    M4   applicant and officer exam schedule
  m05-applicants.spec.ts       M5   applicant list/filter/detail, decisions, documents, bulk update, revisions
  m06-applicant-form.spec.ts   M6   application form, choose-path
  m07-requirements.spec.ts     M7   requirements and uploads
  m09-payment.spec.ts          M9   officer payment checks, tuition calculator
  m10 … m19-*.spec.ts          M10-M19 admin modules (theme, form fields, presets, requirement categories,
                                programs, program assignments, fee settings, system settings,
                                fast track, users)
  public-pages.spec.ts         landing pages (not in the task sheet)
  support/                     helpers (login functions, HTTP helpers, locks, throttle reset, fixtures)
  global-setup.js, test-db.js  test-DB guard and mail worker
  seed.spec.ts, example.spec.js  Playwright planner seed / sample (not real tests)
  tsconfig.json                editor support only
```

There is no `m08` file: no Tracking Progress tests exist yet.

### Conventions
- **Login hooks:** each `describe` signs in once in `test.beforeEach` (`loginAsApplicant`, `loginAsOfficer`, `loginAsAdmin`). Tests that test the login itself, run anonymously, or need a second browser context do their own thing.
- **Which `test` to import:** use `./support/throttle` (clears the login throttle before each test). Applicant-form specs use `./support/applicant-form`, which also takes a lock around the shared applicant's draft.
- **Test statuses in code:**
  - `test.fixme(...)`: planned but not safe or not possible yet (usually needs a disposable applicant, a mail sink or an SIS stub). 30 of these.
  - `test.fail(...)`: documents a **known app bug**; the test is expected to fail and will start "unexpectedly passing" once the bug is fixed, at which point remove the `test.fail`. 10 of these.
  - `test.skip(...)` in a hook or body: environment precondition missing (credentials, data, feature off).
- **Mocked:** Groq AI calls (`**/api/groq/**`), EmailJS on the contact form, and the requirements upload endpoint. Nothing reaches an external service.

## 5. Flags and state to monitor

These are shared, global switches. If tests behave strangely, check them first.

| What | Where | Expected baseline | Why it matters |
|---|---|---|---|
| `E2E_USE_TEST_DB` | root `.env` (`npm run e2e:db:on/off`) | `true` | `false` means the app is on the dev DB and `global-setup` aborts. |
| `exam_enabled` | table `theme_settings` / Admin > Settings | **ON** | ON is the seeder baseline. The "exam disabled" tests (applicant redirect, officer disabled page) switch it OFF through the admin UI under a lock and restore it, as does the System Settings test. A crashed run can leave it OFF. `npm run e2e:db:seed` resets it to ON. |
| `fast_track_enabled` | `theme_settings` | ON | The choose-path screen only shows when it is ON. |
| `tuition_calculator_enabled` | `theme_settings` | ON | When OFF the calculator test skips (page redirects to dashboard). |
| Login throttle | files `writable/cache/throttler_login_*` | none present | 5 attempts per 60 s per IP. Each test clears them; the M1 module runs serially for this reason. If sign-in fails with "Too many login attempts", delete those files. |
| Lock folders | OS temp dir: `akademus-e2e-exam-setting.lock`, `akademus-e2e-applicant-draft.lock` | absent | Created while a test holds the lock; stale ones are removed after a few minutes. If a run was killed and tests hang waiting, delete them. |
| Leftover QA data | programs `QA…`, payment methods `QA_METHOD_…`, users `e2e+…`, schedules `QA …`, theme logos in `public/assets/images/logos/` | accumulates | There is no delete route for payment methods and logo uploads leave an orphan file. Harmless, but the lists grow. |
| Shared applicant draft | the main E2E applicant's form | any | Form specs overwrite its personal-information step; the lock serialises them. |

Serial modules (a failure skips the rest of that module): **M1, M4, M14, M19** plus the "failed logins" groups and payment methods. They share counters, rows or the exam flag and cannot run in parallel safely.

## 6. Coverage of the task sheet (`Valoris Testing Pass 1 (Tasks).csv`)

The sheet lists **87 task IDs** (its subtitle says 60). **Not all of them are created.**

Legend: ✅ covered · 🟡 partly covered · ⏳ written but `test.fixme` (blocked) · ❌ no test yet

**Totals: ✅ 42 · 🟡 25 · ⏳ 12 · ❌ 8**

| ID | Task | Status | Notes |
|---|---|---|---|
| M1-01 | Applicant registration | ✅ | Creates a real user on every run. |
| M1-02 | Duplicate email | 🟡 | Needs `E2E_EXISTING_EMAIL`. Different-letter-case variant not tested. |
| M1-03 | Missing/invalid fields | ✅ | Empty, bad email, mobile length. |
| M1-04 | Applicant login | 🟡 | Session persistence on refresh not tested. |
| M1-05 | Change temp password | ⏳ | Now feasible with the seeded `E2E_TEMP_*` applicant. |
| M1-06 | Wrong password / unknown email | ✅ | App says "User not found" for unknown email, not the sheet's text (recorded in the test). |
| M1-07 | Officer login | ✅ | |
| M1-08 | Admin login | ✅ | Plus a `test.fail` for the visible default credentials. |
| M1-09 | Forgot password | 🟡 | Unknown/invalid email ✅; registered email ⏳ (would queue a real email). |
| M1-10 | Reset via link | 🟡 | Invalid token ✅; successful reset and validation ⏳ (need a DB-seeded token). |
| M1-11 | Change password (logged in) | 🟡 | Only the unauthenticated rejection and CSRF check. |
| M1-12 | Logout | ✅ | Applicant, officer and admin, incl. Back button. |
| M1-13 | Protected pages | ✅ | |
| M2-01 | Applicant dashboard | 🟡 | New-applicant empty state not tested. |
| M2-02 | Officer dashboard data | ✅ | Null-field rendering not tested. |
| M2-03 | Officer search | ✅ | Includes special-character input. |
| M2-04 | Officer calendar | ✅ | |
| M3-01 | Officer sends follow-up | 🟡 | Empty message blocked ✅; a real send ⏳ (queues email). |
| M3-02 | Applicant views/replies | ✅ | Sends "QA test message" each run. |
| M3-03 | Unread counter | 🟡 | Officer badge vs endpoint ✅; applicant badge reset not tested. |
| M3-04 | Conversation list | ✅ | |
| M4-01 | Create exam schedule | ✅ | |
| M4-02 | Edit/delete schedule | 🟡 | Delete with bookings ⏳. |
| M4-03 | Applicant books slot | ⏳ | Sheet marks it *Not Working as Expected* (page gated on approval). Only an invalid-slot POST is checked. |
| M4-04 | Booking when full | ⏳ | Same as above. |
| M4-05 | Cancel booking | ⏳ | Same as above. |
| M4-06 | Scheduling disabled | 🟡 | Redirect and officer disabled page ✅. |
| M4-07 | Check-in / cancel | ⏳ | Seeded fixture exists, test not written. |
| M5-01 | Applicants list | ✅ | |
| M5-02 | Search/filter/paginate | ✅ | The app has no sort control. |
| M5-03 | Applicant profile | ✅ | All tabs. |
| M5-04 | View uploaded document | ❌ | No test opens an applicant document (only payment proofs). |
| M5-05 | Bulk update | 🟡 | Validation and dialog ✅; applying ⏳. Sheet says *Not Tested*. |
| M5-06 | Decision with remarks | 🟡 | Remarks required + invalid input ✅; real approve/reject ⏳. |
| M6-01 | Load form | 🟡 | Covered indirectly by step tests. |
| M6-02 | Fill and save step | ✅ | Required-field validation too. |
| M6-03 | Draft retained | ✅ | Re-login test. |
| M6-04 | Edit saved step | 🟡 | Back/forward keeps data; edit after save not separate. |
| M6-05 | Program-specific fields | ⏳ | Needs two programs with different presets (the seeded Nursing applicant helps). |
| M7-01 | Requirements list | ✅ | |
| M7-02 | Upload valid document | 🟡 | Upload endpoint is mocked; server storage not covered. |
| M7-03 | Invalid type/size | ⏳ | |
| M7-04 | View / replace | ⏳ | Access to another applicant's document ✅ (404). |
| M7-05 | Submit application | 🟡 | Blocked-when-incomplete ✅; successful submit ⏳. |
| M8-01 | Progress timeline | ❌ | Only the page title/heading is checked in M2. |
| M8-02 | Status updates after officer action | ❌ | |
| M9-01 | Submit admission fee | ❌ | |
| M9-02 | Proof validation | ❌ | |
| M9-03 | Reservation fee | ❌ | |
| M9-04 | Officer approves payment | ⏳ | Validation ✅; seeded pending payment available. |
| M9-05 | Officer rejects payment | ⏳ | Remarks-required ✅; same fixture. |
| M9-06 | Tuition calculator | 🟡 | Page renders only; no computed amounts checked. |
| M10-01 | Change theme | ✅ | Restores the original theme afterwards. |
| M10-02 | Theme preview | 🟡 | Preview route and iframe only. |
| M10-03 | Non-admin denied | 🟡 | Applicant session ✅ (in M1); officer session not tested. |
| M11-01 | Add/edit field | 🟡 | Page-level; editing a field is ⏳ (unsafe on the shared DB). |
| M11-02 | Create/rename/reorder page | ✅ | |
| M11-03 | Toggle/delete page | ✅ | |
| M11-04 | Reset to defaults | ⏳ | Irreversible on a shared DB. |
| M12-01 | Create preset | ✅ | |
| M12-02 | Edit preset | ✅ | |
| M12-03 | Deactivate/delete preset | ✅ | |
| M12-04 | Preview form for a program | ❌ | |
| M13-01 | Create category | ✅ | |
| M13-02 | Edit category | ✅ | |
| M13-03 | Delete category | ✅ | |
| M13-04 | Reflects on applicant side | ❌ | |
| M14-01 | List programs | ✅ | |
| M14-02 | Add program | ✅ | |
| M14-03 | Edit/delete program | ✅ | |
| M14-04 | Programs on sign-up | ✅ | |
| M15-01 | Assign preset | ✅ | |
| M15-02 | Reassign preset | 🟡 | Confirm in `m15` whether the replace step is asserted. |
| M16-01 | Update fee amount | ✅ | Negative/non-numeric rejected. |
| M16-02 | Add payment method | ✅ | The Add dialog has a bug (no CSRF token); marked `test.fail`. |
| M16-03 | Edit payment method | ✅ | |
| M16-04 | Toggle payment method | 🟡 | Toggle ✅; not checked from the applicant payment form. |
| M17-01 | Enable/disable exam scheduling | ✅ | Checks admin, officer and applicant pages. |
| M17-02 | Non-admin cannot save | 🟡 | Anonymous and applicant POSTs ✅ (in M1); officer not tested. |
| M18-01 | Define fast-track requirements | ✅ | |
| M18-02 | Applicant uses fast track | ⏳ | Needs the fresh applicant; seeded `E2E_FRESH_*` makes it possible. |
| M19-01 | List users | ✅ | |
| M19-02 | Create officer | ✅ | New officer logs in. |
| M19-03 | Create administrator | 🟡 | Role validation ✅; creating an admin account not asserted. |
| M19-04 | Update / change role | 🟡 | Edit ✅; role change and credential migration not asserted. |
| M19-05 | Reset password | ✅ | |
| M19-06 | Delete user | ✅ | Own-account delete blocked. |

**Extra tests not in the sheet:** all landing pages (`public-pages`), 404 behaviour, CSRF and anonymous-access checks on admin/officer/applicant endpoints, login throttling for officer and admin, XSS/SQL-injection input checks, officer AI assistant.

**Cases with a ⏳ that the seeder can now unblock:** M1-05 (temp password), M4-07 and the exam result test (seeded booking), M9-04/05 (seeded pending payment), M18-02 and the Fast Track path (fresh applicant), M6-05 (second program). Remember that approving/rejecting payments and applicants queues real emails unless the mail worker is off or stubbed.

## 7. Known application bugs the tests record

Each is a `test.fail` and flips to "unexpectedly passed" once fixed:
- Admin login page shows the default credentials.
- Four admin controllers (presets, program presets, requirement categories, fast track) skip the admin session check for anonymous requests.
- Exam schedule create has no server-side validation (past date, end before start, capacity ≤ 0).
- Personal-information step has no date-of-birth / mobile validation.
- `GET /applicant-form/step/4` is served even when earlier steps are incomplete.
- The floating chat button covers the follow-up send button.
- Add Payment Method dialog omits the CSRF token (403).
- `/applynowpage` returns 500; system pages use the title "Document"; the home page logs an SVG path error (Chromium/WebKit).

## 8. CI

[../.github/workflows/playwright.yml](../.github/workflows/playwright.yml) installs dependencies and runs `npx playwright test` on Ubuntu with no database, no seeding and no `.env.e2e`. As it stands, the DB-dependent tests will skip or fail there; it needs a MySQL service, the test schema, `npm run e2e:db:seed` and the credentials as secrets before it is meaningful.

## 9. Limitations and known gaps

- **The test database cannot be rebuilt from the repo.** The only schema file is `app/Database/Migrations/akademus_db_May5_2026.sql`; loading it and then running `php spark migrate -g tests` fails ("table already exists") because the dump's `migrations` table does not match the migration files. `akademus_test` has to be kept or copied by hand.
- Several specs are shared-state sensitive (global settings, shared applicant). Prefer running the whole suite from a seeded baseline and avoid running two copies at once.
- Most "approve/reject/submit" flows are `fixme` because they send real emails or trigger the SIS handoff, which cannot be intercepted from the browser.
- Firefox and WebKit have not been run since the consolidation; chromium has.
