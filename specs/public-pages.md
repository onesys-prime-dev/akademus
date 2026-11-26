# Public Pages and Password Recovery Test Plan

## Application Overview

Public landing pages of Akademus (base http://localhost/akademus/public/): / and /homepage (LandingPage\HomeController), /about-us and /about, /features, /contact, /systems/{akademus,mentora,learnix,sis,attendance,otis}, /applynowpage (ApplyNow), plus password recovery (POST /forgot-password, GET /reset-password/{token}, POST /reset-password) and temp-password change (POST /change-password, shown for applicants with temp_password=1). Plan derived from routes and controller code; live browser exploration was blocked because the Playwright planner setup requires "npm run e2e:db:on" (E2E_USE_TEST_DB). Text expectations are therefore approximate and must be confirmed on first run. Forgot-password sends email: stub/mock or use the email queue worker started by e2e/global-setup.js against the test DB only. Auth sign-in/up are covered in specs/authentication.md.

## Test Scenarios

### 1. Landing pages

**Seed:** `e2e/seed.spec.ts`

#### 1.1. Home page loads at / and /homepage

**File:** `e2e/public/home.spec.ts`

**Steps:**
  1. Navigate to / then to /homepage
    - expect: Both return HTTP 200 and show the same landing content
    - expect: Nav links to About, Features, Contact and an Apply Now / Sign in call to action are visible
    - expect: No console errors or broken images

#### 1.2. Header navigation reaches every landing page

**File:** `e2e/public/navigation.spec.ts`

**Steps:**
  1. From home click About, Features, Contact in turn, then the logo
    - expect: URLs become /about-us (or /about), /features, /contact and back to home
    - expect: Each page has a non-empty heading and title

#### 1.3. Apply Now button leads to sign-up/sign-in

**File:** `e2e/public/apply-now.spec.ts`

**Steps:**
  1. Click Apply Now on home; also open /applynowpage directly
    - expect: User reaches the apply-now page or /sign-in /sign-up
    - expect: Links to sign-in and sign-up work

#### 1.4. About, Features and Contact pages render content

**File:** `e2e/public/static-pages.spec.ts`

**Steps:**
  1. Visit /about-us, /about, /features, /contact
    - expect: Each returns 200 with visible content
    - expect: Both route variants (/about-us vs /about, /features duplicate route) do not 404 or error 500

#### 1.5. System pages render

**File:** `e2e/public/systems.spec.ts`

**Steps:**
  1. Visit /systems/akademus, mentora, learnix, sis, attendance, otis
    - expect: Each returns 200 with its system name in a heading

#### 1.6. Unknown public URL shows 404

**File:** `e2e/public/not-found.spec.ts`

**Steps:**
  1. Visit /systems/unknown and /no-such-page
    - expect: 404 page is displayed, no PHP stack trace or path disclosure

#### 1.7. Contact page has working contact info or form validation

**File:** `e2e/public/contact.spec.ts`

**Steps:**
  1. Open /contact; if a form exists submit empty, then invalid email (mock email delivery)
    - expect: Required validation blocks submission
    - expect: No real email is sent in tests

#### 1.8. Responsive layout on mobile viewport

**File:** `e2e/public/responsive.spec.ts`

**Steps:**
  1. Set viewport 375x667, open home, open the menu toggle
    - expect: Mobile menu opens and links are reachable, no horizontal scroll

### 2. Password recovery and temp password

**Seed:** `e2e/seed.spec.ts`

#### 2.1. Forgot password with registered email shows generic confirmation

**File:** `e2e/public/forgot-password-ok.spec.ts`

**Steps:**
  1. On /sign-in click Forgot Password?, enter the E2E applicant email, submit (REQUIRES EXISTING ACCOUNT; email queue stubbed)
    - expect: Message 'If that email is registered, a reset link has been sent.' appears

#### 2.2. Forgot password with unregistered email gives identical message

**File:** `e2e/public/forgot-password-unknown.spec.ts`

**Steps:**
  1. Submit nobody-<timestamp>@example.com
    - expect: Same generic message (no account enumeration)

#### 2.3. Forgot password with invalid or empty email

**File:** `e2e/public/forgot-password-invalid.spec.ts`

**Steps:**
  1. Submit empty then 'abc'
    - expect: 'Please enter a valid email address.' or HTML5 validation; no request accepted

#### 2.4. Reset page with invalid/expired token

**File:** `e2e/public/reset-invalid-token.spec.ts`

**Steps:**
  1. Open /reset-password/not-a-real-token
    - expect: Invalid/expired link message or redirect to sign-in; no reset form usable
  2. POST /reset-password with bogus token and valid passwords
    - expect: JSON success false 'This link is invalid or has expired.'

#### 2.5. Reset password validation (length and mismatch)

**File:** `e2e/public/reset-validation.spec.ts`

**Steps:**
  1. With a valid token (seeded in test DB, REQUIRES DB FIXTURE) enter 'short' then mismatched passwords
    - expect: 'Password must be at least 8 characters.'
    - expect: 'Passwords do not match.'

#### 2.6. Reset password happy path

**File:** `e2e/public/reset-happy.spec.ts`

**Steps:**
  1. Using a DB-seeded token for a throwaway applicant set a new 8+ char password, then sign in with it
    - expect: Success message, redirect to sign-in, new password works and old one fails, token cannot be reused

#### 2.7. First-login temp password forces change

**File:** `e2e/public/temp-password.spec.ts`

**Steps:**
  1. Sign in as a throwaway applicant with temp_password=1 (REQUIRES FIXTURE)
    - expect: Change-password modal/prompt appears on dashboard
  2. Submit short password, mismatched passwords, then valid matching passwords
    - expect: Errors 'Password must be at least 8 characters.' and 'Passwords do not match.', then success and temp flag cleared

#### 2.8. change-password endpoint rejects unauthenticated requests

**File:** `e2e/public/change-password-auth.spec.ts`

**Steps:**
  1. POST /change-password without a session via request fixture
    - expect: JSON success false, message 'Unauthorized'
