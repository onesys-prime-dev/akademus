# Authentication (Sign-in and Sign-up) Test Plan

## Application Overview

Akademus admissions app (CodeIgniter 4, XAMPP). Sign-up at http://localhost/akademus/public/sign-up has fields firstname, lastname, email, mobile (10-13 chars), and a searchable program dropdown (hidden input "program"); there is NO password field, since a temporary password is generated server-side (temp_password=1) and delivered to the applicant. Submit goes to /sign-up/register (JSON response, redirect to /success). Sign-in at http://localhost/akademus/public/sign-in has email, password (with show/hide toggle), remember_me checkbox, "Forgot Password?" link, and a "Click here" link to sign-up; it posts to /sign-in/login (JSON, "Invalid email or password" on failure, redirect to /dashboard on success). Server validation: firstname/lastname/program required, email required|valid_email|is_unique (message: "This email is already registered. Please use a different email or sign in instead."). All scenarios assume a fresh browser context with no session. Scenarios marked REQUIRES EXISTING ACCOUNT need a pre-provisioned test applicant (credentials supplied via env vars, not guessed). Sign-up submissions create DB rows and may send email, so only the happy-path scenario submits valid data, using a unique timestamped email.

## Test Scenarios

### 1. Sign-up

**Seed:** `e2e/seed.spec.ts`

#### 1.1. Sign-up page renders all fields

**File:** `e2e/authentication/signup-render.spec.ts`

**Steps:**
  1. Navigate to http://localhost/akademus/public/sign-up
    - expect: Title is 'Akademus | Sign Up'
    - expect: First name, last name, email, mobile inputs, 'Select a Program' dropdown and 'CREATE ACCOUNT' button are visible
    - expect: No password field is shown

#### 1.2. Sign-up happy path with unique email

**File:** `e2e/authentication/signup-happy.spec.ts`

**Steps:**
  1. Open sign-up page. Fill first name 'QA', last name 'Tester', email 'qa.test+<Date.now()>@example.com', mobile '09171234567'
    - expect: Fields accept input
  2. Click program dropdown, optionally type in search box, select the first program (e.g. Bachelor of Science in Computer Science)
    - expect: Trigger text shows the chosen program name
    - expect: Hidden program input has a value
  3. Click CREATE ACCOUNT (this creates a DB record; run sparingly and against a test DB if possible)
    - expect: Request to /sign-up/register succeeds
    - expect: Browser is redirected to /success and an applicant number is displayed

#### 1.3. Sign-up with all fields empty

**File:** `e2e/authentication/signup-empty.spec.ts`

**Steps:**
  1. Open sign-up page and click CREATE ACCOUNT without entering anything
    - expect: No successful registration; HTML5 required validation or an error message appears
    - expect: Page stays on /sign-up

#### 1.4. Sign-up without selecting a program

**File:** `e2e/authentication/signup-no-program.spec.ts`

**Steps:**
  1. Fill all text fields with valid data (unique email) but leave program unselected, click CREATE ACCOUNT
    - expect: Registration is blocked with a program-required error
    - expect: No account is created

#### 1.5. Sign-up with invalid email formats

**File:** `e2e/authentication/signup-invalid-email.spec.ts`

**Steps:**
  1. Fill valid data but email 'not-an-email', select program, submit
    - expect: Browser/server rejects the email; no account created
  2. Repeat with 'user@', '@example.com' and 'user name@example.com'
    - expect: Each is rejected

#### 1.6. Sign-up mobile number length boundaries

**File:** `e2e/authentication/signup-mobile.spec.ts`

**Steps:**
  1. Use a 9-digit mobile '091712345' with otherwise valid data and submit
    - expect: Rejected: mobile must be at least 10 characters
  2. Use a 14-character mobile '09171234567890' and submit
    - expect: Rejected: mobile must be at most 13 characters
  3. Enter letters 'abcdefghij'
    - expect: Observe and record behavior (expected rejection; note if accepted as a possible defect)

#### 1.7. Sign-up with duplicate email

**File:** `e2e/authentication/signup-duplicate.spec.ts`

**Steps:**
  1. Fill the form with an email of an already registered applicant (REQUIRES EXISTING ACCOUNT, e.g. env E2E_EXISTING_EMAIL), valid other fields and a program, submit
    - expect: Error shown: 'This email is already registered. Please use a different email or sign in instead.'
    - expect: No new account is created; page stays on /sign-up

#### 1.8. Sign-up whitespace-only names

**File:** `e2e/authentication/signup-whitespace.spec.ts`

**Steps:**
  1. Enter spaces in first and last name with otherwise valid data, submit
    - expect: Registration rejected (record behavior; flag as defect if accepted)

#### 1.9. Program dropdown search and selection

**File:** `e2e/authentication/signup-program-dropdown.spec.ts`

**Steps:**
  1. Open dropdown, type 'Computer' in 'Search program...'
    - expect: Options filter to matching programs only
  2. Type a nonsense string
    - expect: No options are shown / empty state
  3. Clear search, select an option, then click outside the panel
    - expect: Panel closes and selection persists

### 2. Sign-in

**Seed:** `e2e/seed.spec.ts`

#### 2.1. Sign-in page renders

**File:** `e2e/authentication/signin-render.spec.ts`

**Steps:**
  1. Navigate to http://localhost/akademus/public/sign-in
    - expect: Title 'Akademus | Sign In'
    - expect: Email, password, remember me checkbox, 'Forgot Password?' link and 'Sign In' button visible

#### 2.2. Sign-in happy path (REQUIRES EXISTING ACCOUNT)

**File:** `e2e/authentication/signin-happy.spec.ts`

**Steps:**
  1. Enter valid credentials from env vars E2E_EMAIL / E2E_PASSWORD (do not hardcode or guess) and click Sign In
    - expect: Redirected to http://localhost/akademus/public/dashboard
    - expect: Session established; dashboard content visible. If the account still has a temporary password, a change-password prompt may appear instead (note which)

#### 2.3. Sign-in with empty fields

**File:** `e2e/authentication/signin-empty.spec.ts`

**Steps:**
  1. Click Sign In with both fields empty
    - expect: Login is not performed; validation message shown; stays on /sign-in
  2. Enter only email, submit; then only password, submit
    - expect: Each case is rejected with a validation message

#### 2.4. Sign-in with invalid email format

**File:** `e2e/authentication/signin-invalid-email.spec.ts`

**Steps:**
  1. Enter 'notanemail' and any password, submit
    - expect: Rejected by HTML5 validation or shows 'Invalid email or password'

#### 2.5. Sign-in with wrong password (REQUIRES EXISTING ACCOUNT)

**File:** `e2e/authentication/signin-wrong-password.spec.ts`

**Steps:**
  1. Enter an existing email (env E2E_EMAIL) and password 'WrongPass!123', submit
    - expect: Message 'Invalid email or password' shown
    - expect: Not redirected to dashboard

#### 2.6. Sign-in with unregistered email

**File:** `e2e/authentication/signin-unknown-email.spec.ts`

**Steps:**
  1. Enter 'nobody.<timestamp>@example.com' with any password, submit
    - expect: Same generic message 'Invalid email or password' (no account enumeration)

#### 2.7. Password visibility toggle

**File:** `e2e/authentication/signin-password-toggle.spec.ts`

**Steps:**
  1. Type a password and click the eye toggle button
    - expect: Input type switches between password and text; value unchanged

#### 2.8. SQL injection and XSS strings in sign-in

**File:** `e2e/authentication/signin-injection.spec.ts`

**Steps:**
  1. Submit email "' OR 1=1 --" and password "' OR '1'='1"
    - expect: Login fails; no server error; no dashboard access
  2. Submit email '<script>alert(1)</script>@x.com'
    - expect: No script executes; error is generic

#### 2.9. Protected page access without session

**File:** `e2e/authentication/signin-guard.spec.ts`

**Steps:**
  1. In a fresh context navigate to http://localhost/akademus/public/dashboard
    - expect: Redirected to /sign-in

#### 2.10. Logout after sign-in (REQUIRES EXISTING ACCOUNT)

**File:** `e2e/authentication/signin-logout.spec.ts`

**Steps:**
  1. Sign in, then trigger logout and revisit /dashboard
    - expect: Session ended and redirected to /sign-in

### 3. Navigation between auth pages

**Seed:** `e2e/seed.spec.ts`

#### 3.1. Sign-in to sign-up via 'Click here' link

**File:** `e2e/authentication/nav-signin-to-signup.spec.ts`

**Steps:**
  1. On /sign-in click the 'Click here' link
    - expect: URL becomes http://localhost/akademus/public/sign-up and the sign-up form shows

#### 3.2. Sign-up to sign-in link and header 'Get Started'

**File:** `e2e/authentication/nav-signup-to-signin.spec.ts`

**Steps:**
  1. On /sign-up click the sign-in link in the form (if present) and the header 'Get Started' button
    - expect: Both lead to http://localhost/akademus/public/sign-in

#### 3.3. Forgot Password link behavior

**File:** `e2e/authentication/nav-forgot-password.spec.ts`

**Steps:**
  1. On /sign-in click 'Forgot Password?'
    - expect: Forgot-password UI opens (link href is '#', so likely a modal); do not submit real emails

#### 3.4. Portal logo returns to home

**File:** `e2e/authentication/nav-logo.spec.ts`

**Steps:**
  1. Click 'Akademus Portal' logo on each auth page
    - expect: Navigates to http://localhost/akademus/public/
