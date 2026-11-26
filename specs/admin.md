# Admin Portal Test Plan

## Application Overview

Admin portal under /admin: login (GET/POST /admin/login via ThemeController; success redirects to admin/theme; failure 'Invalid credentials'; shared LoginThrottle), logout, users (/admin/users with store/update/reset-password/delete; roles validated; 'All fields are required.', 'Invalid email address.', 'Password must be at least 8 characters.', 'Email is already in use.'), theme editor (/admin/theme, save, json, preview, upload-logo limited to JPEG/PNG/GIF/WebP under 2 MB, custom presets), programs (/admin/programs CRUD, /admin/programs/presets + assign-preset), form fields (/admin/form-fields, pages create/rename/toggle/reorder/delete, save, reset; 401 JSON when unauthorized), form presets (/admin/presets CRUD; only one global preset - 'A global preset already exists. Deactivate it first.'), requirement categories (/admin/requirement-categories CRUD), fast-track requirements (/admin/fast-track-requirements), payment fees and methods (/admin/payment-fees, update fee ('Invalid amount.'), add/update/toggle method ('Method name is required.')), settings (/admin/settings, e.g. exam_enabled). NOTE: no routes were found for /admin/dashboard or an admin applicants list although views/controllers (Admin\\ApplicantsController, admin/dashboard.php) exist - verify and report as a possible gap. ALL authenticated scenarios REQUIRE AN ADMIN ACCOUNT (ADMIN_EMAIL/ADMIN_PASSWORD not available); run only against the test DB because they mutate global configuration. Deletes should target records the test itself created (unique names with timestamp). Planned from code; not explored live.

## Test Scenarios

### 1. Admin authentication and access control

**Seed:** `e2e/seed.spec.ts`

#### 1.1. Admin login page renders

**File:** `e2e/admin/login-render.spec.ts`

**Steps:**
  1. Open /admin/login
    - expect: Login form with email and password and submit button

#### 1.2. Admin login happy path (REQUIRES ADMIN ACCOUNT)

**File:** `e2e/admin/login-happy.spec.ts`

**Steps:**
  1. Sign in with ADMIN_EMAIL/ADMIN_PASSWORD
    - expect: Redirect to /admin/theme with admin sidebar
  2. Visit /admin/login again while signed in
    - expect: Redirect to /admin/theme

#### 1.3. Admin login invalid credentials and empty form

**File:** `e2e/admin/login-invalid.spec.ts`

**Steps:**
  1. Submit wrong password, unknown email, empty fields
    - expect: 'Invalid credentials' message; stays on login

#### 1.4. Applicant and officer credentials rejected at admin login

**File:** `e2e/admin/login-wrong-role.spec.ts`

**Steps:**
  1. Use the E2E applicant (and officer) credentials at /admin/login
    - expect: Rejected with 'Invalid credentials'

#### 1.5. Admin login throttling

**File:** `e2e/admin/login-throttle.spec.ts`

**Steps:**
  1. Repeated failures with a unique email
    - expect: Retry/lockout message appears

#### 1.6. Every admin page redirects when unauthenticated

**File:** `e2e/admin/access-unauth.spec.ts`

**Steps:**
  1. Visit /admin/users, /admin/theme, /admin/programs, /admin/presets, /admin/form-fields, /admin/requirement-categories, /admin/fast-track-requirements, /admin/payment-fees, /admin/settings
    - expect: Each redirects to /admin/login (or 401 JSON for XHR); no content leaked
  2. POST /admin/users/store, /admin/settings/save, /admin/payment-fees/update/1, /admin/form-fields/save without session
    - expect: Rejected, no changes

#### 1.7. Applicant session cannot use admin pages

**File:** `e2e/admin/access-applicant.spec.ts`

**Steps:**
  1. Sign in as E2E applicant, visit the admin URLs above and POST admin endpoints
    - expect: Redirect to admin login or denied; no admin data returned

#### 1.8. Admin logout

**File:** `e2e/admin/logout.spec.ts`

**Steps:**
  1. Click logout (/admin/logout) then Back
    - expect: Redirected to login; protected pages inaccessible

### 2. Users management

**Seed:** `e2e/seed.spec.ts`

#### 2.1. Users list renders (REQUIRES ADMIN ACCOUNT)

**File:** `e2e/admin/users-list.spec.ts`

**Steps:**
  1. Open /admin/users
    - expect: Users table with role and actions

#### 2.2. Create officer user

**File:** `e2e/admin/users-create.spec.ts`

**Steps:**
  1. Add user with unique email, valid role and 8+ char password
    - expect: 'User created successfully.' and row appears; new officer can log in at officer login

#### 2.3. Create user validation

**File:** `e2e/admin/users-create-invalid.spec.ts`

**Steps:**
  1. Submit empty, invalid email, 7-char password, invalid role (tampered), duplicate email
    - expect: 'All fields are required.', 'Invalid email address.', 'Password must be at least 8 characters.', 'Invalid role.', 'Email is already in use.'

#### 2.4. Edit, reset password and delete user

**File:** `e2e/admin/users-modify.spec.ts`

**Steps:**
  1. Edit the test-created user, reset its password (email stubbed), delete it
    - expect: Each action succeeds; deleted user absent; deleting the currently signed-in admin is blocked

### 3. Programs, presets and form configuration

**Seed:** `e2e/seed.spec.ts`

#### 3.1. Programs list and create program

**File:** `e2e/admin/programs-create.spec.ts`

**Steps:**
  1. Open /admin/programs, create program with unique name/code
    - expect: 'Program created successfully.' and listed; appears in sign-up dropdown /get-programs

#### 3.2. Program create validation

**File:** `e2e/admin/programs-validation.spec.ts`

**Steps:**
  1. Submit empty and duplicate name/code
    - expect: Validation errors, no record

#### 3.3. Edit and delete program

**File:** `e2e/admin/programs-edit-delete.spec.ts`

**Steps:**
  1. Edit test program ('Program updated successfully.'), then delete; open /admin/programs/999999/edit
    - expect: Updated then removed; invalid id flashes 'Program not found.'

#### 3.4. Assign preset to program

**File:** `e2e/admin/program-presets.spec.ts`

**Steps:**
  1. Open /admin/programs/presets and assign a preset to test program
    - expect: Assignment saved; /api/presets/program/{id} returns that preset's pages

#### 3.5. Form presets CRUD

**File:** `e2e/admin/presets-crud.spec.ts`

**Steps:**
  1. Create preset, edit, deactivate, delete (test-created only)
    - expect: 'Preset created successfully!', 'Preset updated successfully!', 'Preset deactivated successfully!', 'Preset deleted successfully!'

#### 3.6. Only one global preset allowed

**File:** `e2e/admin/presets-global.spec.ts`

**Steps:**
  1. With a global preset active create a second global preset
    - expect: 'A global preset already exists. Deactivate it first.'

#### 3.7. Form fields: add, reorder, toggle, rename and delete page

**File:** `e2e/admin/form-fields-pages.spec.ts`

**Steps:**
  1. Create page with empty title, then 'QA Page <ts>', rename, toggle, reorder, delete
    - expect: 'Title is required' for empty; other actions succeed and reflect in applicant form

#### 3.8. Form fields: save and reset

**File:** `e2e/admin/form-fields-save.spec.ts`

**Steps:**
  1. Change a field label/required flag and save; post empty payload; reset
    - expect: Saved; empty gives 'No fields received'; reset restores defaults

#### 3.9. Requirement categories CRUD

**File:** `e2e/admin/requirement-categories.spec.ts`

**Steps:**
  1. Create, edit, delete a test category; submit empty name
    - expect: Listed/updated/removed; empty name rejected

#### 3.10. Fast track requirements

**File:** `e2e/admin/fast-track-requirements.spec.ts`

**Steps:**
  1. Toggle documents flagged as fast-track required and save
    - expect: 'Fast Track requirements saved.' and applicant fast-track list matches

### 4. Payments, settings and theme

**Seed:** `e2e/seed.spec.ts`

#### 4.1. Update fee amount

**File:** `e2e/admin/payment-fee-update.spec.ts`

**Steps:**
  1. Set admission fee to valid number; try -1, 'abc', empty
    - expect: 'Fee amount updated.' for valid; 'Invalid amount.' otherwise; applicant fee page shows new amount; restore original

#### 4.2. Add, edit and toggle payment method

**File:** `e2e/admin/payment-methods.spec.ts`

**Steps:**
  1. Add 'QA Method <ts>', edit, toggle inactive, add with empty name
    - expect: 'Method name is required.' for empty; toggled-off method hidden from applicant payment pages

#### 4.3. Toggle non-existent method

**File:** `e2e/admin/payment-method-missing.spec.ts`

**Steps:**
  1. POST /admin/payment-methods/toggle/999999
    - expect: 'Method not found.'

#### 4.4. System settings save and exam toggle

**File:** `e2e/admin/settings.spec.ts`

**Steps:**
  1. Open /admin/settings, toggle exam_enabled, save, then check applicant /exam-schedule and officer exam page; restore
    - expect: Setting persisted; exam pages enabled/disabled accordingly

#### 4.5. Theme editor save, preview and logo upload

**File:** `e2e/admin/theme.spec.ts`

**Steps:**
  1. Change primary colour, preview, save; upload valid PNG logo and a .txt or >2 MB file
    - expect: Saved flag shown; invalid logo gives 'Logo must be a JPEG, PNG, GIF, or WebP image under 2 MB.'; restore theme afterwards

#### 4.6. Custom theme preset save and delete

**File:** `e2e/admin/theme-presets.spec.ts`

**Steps:**
  1. Save custom preset then delete it
    - expect: Preset appears then disappears

#### 4.7. Admin dashboard and applicants routes gap

**File:** `e2e/admin/dashboard-gap.spec.ts`

**Steps:**
  1. Visit /admin/dashboard and /admin/applicants while signed in as admin
    - expect: Document actual behaviour (404 means routes are missing for existing views/controllers); test should be updated once routes exist
