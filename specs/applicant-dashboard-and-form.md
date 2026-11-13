# Applicant Dashboard and Application Form Test Plan

## Application Overview

Applicant area after sign-in: /dashboard (progress, banner, checklists, tour dismiss POST /dashboard/tour/dismiss), /applicant-form (+ /choose-path for Regular vs Fast Track, /step/{n}, POST /update/{n}, POST /get-drafts, POST /resubmit), /requirements (+ /step/{n}, POST /update/{n}, GET /document/{id}, POST /submit), /tracking-progress, /exam-schedule (only if exam_enabled=1), /follow-up-messages, /faq, /tuition-fee-calculator. Steps are dynamic (admin form presets). Application locks after submit ('Your application has already been submitted and cannot be edited.'). All scenarios REQUIRE the pre-provisioned applicant (E2E_EMAIL/E2E_PASSWORD from .env.e2e, never hard-code) against the test DB (npm run e2e:db:on); submit scenarios mutate state so use a throwaway applicant. Plan written from routes/controllers/views; live exploration was blocked by the db-on guard, so selectors and copy need confirmation.

## Test Scenarios

### 1. Access control

**Seed:** `e2e/seed.spec.ts`

#### 1.1. Unauthenticated access redirects to sign-in

**File:** `e2e/applicant/access-control.spec.ts`

**Steps:**
  1. In a fresh context visit /dashboard, /applicant-form, /requirements, /tracking-progress, /exam-schedule, /follow-up-messages, /reservation-fee, /admission-processing-fee, /fast-track-fee, /tuition-fee-calculator
    - expect: Each redirects to /sign-in (calculator may redirect to /dashboard)
  2. POST /applicant-form/update/1 and /requirements/update/1 without session
    - expect: 401/Unauthorized JSON, no data saved

#### 1.2. Applicant cannot reach admin or officer areas

**File:** `e2e/applicant/no-privilege.spec.ts`

**Steps:**
  1. Signed in as applicant visit /admin/users, /admin/theme, /admin/settings, /admin/programs, /admission-officer/dashboard, /admission-officer/applicants
    - expect: Redirected to admin/login or admission-officer/login; no admin content rendered
    - expect: Same for POST /admin/users/store and POST /admission-officer/payments/1/approve (no changes)

#### 1.3. Logout ends session

**File:** `e2e/applicant/logout.spec.ts`

**Steps:**
  1. Sign in, click logout (/logout), press browser Back
    - expect: Redirected to sign-in/home; Back does not reveal dashboard content; /dashboard redirects to sign-in

### 2. Dashboard

**Seed:** `e2e/seed.spec.ts`

#### 2.1. Dashboard renders for a signed-in applicant

**File:** `e2e/applicant/dashboard.spec.ts`

**Steps:**
  1. Sign in with E2E credentials
    - expect: Lands on /dashboard
    - expect: Banner message, overall progress, form checklist, documents checklist and payment fees checklist visible
    - expect: Sidebar links: dashboard, application form, requirements, tracking, payments, FAQ, follow-up

#### 2.2. Dashboard banner reflects application state

**File:** `e2e/applicant/dashboard-banner.spec.ts`

**Steps:**
  1. Use fresh applicant, then one with completed form but no documents
    - expect: Fresh: prompt to start form; form complete: 'Your form is complete! Proceed to upload the required documents.'

#### 2.3. Dashboard checklist links navigate correctly

**File:** `e2e/applicant/dashboard-links.spec.ts`

**Steps:**
  1. Click each checklist item/button
    - expect: Goes to applicant-form, requirements or the relevant fee page

#### 2.4. Onboarding tour can be dismissed and stays dismissed

**File:** `e2e/applicant/dashboard-tour.spec.ts`

**Steps:**
  1. On first login close/skip tour, reload
    - expect: POST /dashboard/tour/dismiss succeeds and tour does not reappear

#### 2.5. Tour dismiss endpoint requires authentication

**File:** `e2e/applicant/tour-unauth.spec.ts`

**Steps:**
  1. POST /dashboard/tour/dismiss without session
    - expect: JSON success false with 401

#### 2.6. Sidebar pages: FAQ, calculator, tracking progress, follow-up

**File:** `e2e/applicant/sidebar-pages.spec.ts`

**Steps:**
  1. Open /faq, /tuition-fee-calculator, /tracking-progress, /follow-up-messages
    - expect: Each renders without errors; FAQ items expand/collapse

#### 2.7. Tuition fee calculator computes values

**File:** `e2e/applicant/tuition-calculator.spec.ts`

**Steps:**
  1. Choose program/options, calculate; try with nothing selected
    - expect: Total shown for valid input; sensible validation for empty input

#### 2.8. AI chat widget opens and handles empty message

**File:** `e2e/applicant/chat-widget.spec.ts`

**Steps:**
  1. Open chat widget; mock POST /api/groq/applicant via page.route; send empty and non-empty message
    - expect: Empty not sent; mocked reply displayed; no real Groq call

### 3. Application form

**Seed:** `e2e/seed.spec.ts`

#### 3.1. Choose path screen: Regular vs Fast Track

**File:** `e2e/applicant/choose-path.spec.ts`

**Steps:**
  1. As new applicant open /applicant-form
    - expect: Redirected to /applicant-form/choose-path with both options
  2. Submit with nothing chosen, then choose Regular
    - expect: Validation error for none; Regular continues to step 1

#### 3.2. Invalid path value rejected

**File:** `e2e/applicant/choose-path-invalid.spec.ts`

**Steps:**
  1. POST /applicant-form/choose-path with path=bogus
    - expect: Rejected or redirected back, application type unchanged

#### 3.3. Step 1 personal information validation

**File:** `e2e/applicant/form-step1-validation.spec.ts`

**Steps:**
  1. Clear required fields and click Next
    - expect: Inline errors, stays on step 1, step not saved
  2. Enter invalid date of birth (future), invalid mobile (letters)
    - expect: Validation message from stepValidationError; no advance

#### 3.4. Complete step 1 and advance

**File:** `e2e/applicant/form-step1-happy.spec.ts`

**Steps:**
  1. Fill valid personal info, click Next/Save
    - expect: 'Step saved successfully'; progress indicator advances to step 2

#### 3.5. Program-specific fields appear per selected program

**File:** `e2e/applicant/form-program-fields.spec.ts`

**Steps:**
  1. Compare steps for two applicants in different programs (GET /api/presets/program/{id})
    - expect: Pages/fields match the program's assigned preset or global preset

#### 3.6. Navigate steps back and forth keeps data

**File:** `e2e/applicant/form-navigation.spec.ts`

**Steps:**
  1. Fill step 1, Next, Back
    - expect: Previously entered values retained

#### 3.7. Save draft and resume after re-login

**File:** `e2e/applicant/form-resume.spec.ts`

**Steps:**
  1. Partially fill a step, leave, log out, sign in again, reopen /applicant-form
    - expect: Saved steps are retained and user resumes at first incomplete step (POST /get-drafts returns drafts)

#### 3.8. Direct URL to non-existent step

**File:** `e2e/applicant/form-bad-step.spec.ts`

**Steps:**
  1. Visit /applicant-form/step/999 and /applicant-form/step/0
    - expect: Graceful error/redirect, no PHP error

#### 3.9. Cannot skip ahead to later steps

**File:** `e2e/applicant/form-skip.spec.ts`

**Steps:**
  1. As fresh applicant GET /applicant-form/step/4
    - expect: Redirected to current step or error; no data of others exposed

#### 3.10. Text field XSS and long input are sanitized

**File:** `e2e/applicant/form-xss.spec.ts`

**Steps:**
  1. Enter <script>alert(1)</script> and a 5000 char string in text fields, save and reload
    - expect: No script executes; value escaped or rejected; no 500

#### 3.11. Family and educational background are deferrable

**File:** `e2e/applicant/form-deferred-steps.spec.ts`

**Steps:**
  1. Skip family/educational steps and complete required ones
    - expect: Application can reach 'pending' status without them

#### 3.12. Submitted application is locked

**File:** `e2e/applicant/form-locked.spec.ts`

**Steps:**
  1. After submission POST /applicant-form/update/1
    - expect: 'Your application has already been submitted and cannot be edited.'

#### 3.13. Resubmit without pending revision

**File:** `e2e/applicant/form-resubmit.spec.ts`

**Steps:**
  1. POST /applicant-form/resubmit when no revision requested
    - expect: 'There is no pending revision to resubmit.'
  2. After officer requests revision (REQUIRES OFFICER ACCOUNT), edit and resubmit
    - expect: Status returns to pending/for review

### 4. Requirements and document uploads

**Seed:** `e2e/seed.spec.ts`

#### 4.1. Requirements page lists documents with required flags

**File:** `e2e/applicant/requirements-list.spec.ts`

**Steps:**
  1. Open /requirements
    - expect: Documents for the program shown, required vs optional distinguished, progress counter uploaded/total

#### 4.2. Upload valid PDF and image

**File:** `e2e/applicant/requirements-upload.spec.ts`

**Steps:**
  1. On a requirement step upload a small PDF, then a PNG
    - expect: Upload succeeds, progress updates, file viewable via /requirements/document/{id}

#### 4.3. Reject invalid file type and oversize file

**File:** `e2e/applicant/requirements-invalid-file.spec.ts`

**Steps:**
  1. Upload .exe/.txt and a file above the size limit
    - expect: Clear error, nothing stored

#### 4.4. Replace an uploaded document

**File:** `e2e/applicant/requirements-replace.spec.ts`

**Steps:**
  1. Upload a second file to the same requirement
    - expect: New file replaces old; status resets to pending review

#### 4.5. Cannot view another applicant's document

**File:** `e2e/applicant/requirements-idor.spec.ts`

**Steps:**
  1. Request /requirements/document/{id} with an id belonging to someone else
    - expect: Unauthorized/404, no file returned

#### 4.6. Fast Track applicants only need fast-track required documents

**File:** `e2e/applicant/requirements-fasttrack.spec.ts`

**Steps:**
  1. Choose Fast Track path, open /requirements
    - expect: Only documents flagged is_fast_track_required are mandatory; others deferred

#### 4.7. Submit application blocked until required documents uploaded

**File:** `e2e/applicant/requirements-submit-blocked.spec.ts`

**Steps:**
  1. Click submit with missing required documents (POST /requirements/submit)
    - expect: Error listing missing documents; status unchanged

#### 4.8. Submit application with all required documents (throwaway applicant)

**File:** `e2e/applicant/requirements-submit.spec.ts`

**Steps:**
  1. Complete form, upload all required, submit
    - expect: Success, application locked, status pending review, dashboard and tracking progress update
    - expect: Any notification email is stubbed

### 5. Exam schedule and follow-ups

**Seed:** `e2e/seed.spec.ts`

#### 5.1. Exam schedule redirects to dashboard when exam disabled

**File:** `e2e/applicant/exam-disabled.spec.ts`

**Steps:**
  1. With exam_enabled=0 visit /exam-schedule
    - expect: Redirect to /dashboard

#### 5.2. Book and cancel an exam slot

**File:** `e2e/applicant/exam-book-cancel.spec.ts`

**Steps:**
  1. REQUIRES exam_enabled=1 and an officer-created slot. Book a slot, then cancel
    - expect: Booking shown, slot count decremented; cancellation frees the slot

#### 5.3. Cannot book full or non-existent slot

**File:** `e2e/applicant/exam-book-invalid.spec.ts`

**Steps:**
  1. POST /exam-schedule/book with invalid or full slot id
    - expect: JSON success false with message

#### 5.4. Send follow-up message to officer

**File:** `e2e/applicant/follow-up.spec.ts`

**Steps:**
  1. Open /follow-up-messages, send empty message, then 'QA test message'
    - expect: Empty: 'Message cannot be empty'; valid message appears in thread; unread count endpoint responds
