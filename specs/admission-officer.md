# Admission Officer Portal Test Plan

## Application Overview

Admission officer portal under /admission-officer: login (GET/POST /admission-officer/login, users.role = admission_officer, session ao_logged_in, shared LoginThrottle), logout, dashboard (+ /dashboard/search, /data, /calendar-data), applicants list (/applicants, /applicants/fetch-applicants, POST /applicants/bulk-update), applicant detail (/detailed-applicant/{id} with tabs: application form, requirements, admission fee, exam, clearance, reservation fee, fast-track fee), document review (/applicants/document/{app}/{doc}, POST .../decide), POST /applicants/{id}/decide, POST /applicants/{id}/request-revision, payment approve/reject/proof (/admission-officer/payments/{id}/...), exam schedule (/exam-schedule with list/calendar toggle, create/update/delete, check-in, cancel booking, POST /exam-bookings/{id}/result), follow-up messages (/follow-up, conversations, send, unread-count), AI assistant (/api/groq/officer, mock). ALL scenarios REQUIRE AN OFFICER ACCOUNT (not available to the planner; no credentials in .env.e2e known) - suggest OFFICER_EMAIL/OFFICER_PASSWORD in .env.e2e and a test-DB seeded officer. Approve/reject and SIS handoff and emails must be run against test DB with stubs. Planned from routes/controllers/views; not explored live.

## Test Scenarios

### 1. Officer authentication and access control

**Seed:** `e2e/seed.spec.ts`

#### 1.1. Officer login page renders

**File:** `e2e/officer/login-render.spec.ts`

**Steps:**
  1. Open /admission-officer/login unauthenticated (no account needed)
    - expect: Email (placeholder officer@akademus.edu.ph) and password inputs and a sign-in button are visible

#### 1.2. Officer login with valid credentials (REQUIRES OFFICER ACCOUNT)

**File:** `e2e/officer/login-happy.spec.ts`

**Steps:**
  1. Sign in with OFFICER_EMAIL/OFFICER_PASSWORD
    - expect: Redirected to /admission-officer/dashboard
  2. Revisit /admission-officer/login while signed in
    - expect: Redirects to dashboard

#### 1.3. Officer login with wrong password or unknown email

**File:** `e2e/officer/login-invalid.spec.ts`

**Steps:**
  1. Submit bad credentials; submit empty form
    - expect: Error shown, stays on login; empty blocked by required

#### 1.4. Applicant credentials cannot log in as officer

**File:** `e2e/officer/login-applicant.spec.ts`

**Steps:**
  1. Submit E2E applicant email/password at officer login
    - expect: Rejected (role must be admission_officer)

#### 1.5. Login throttling after repeated failures

**File:** `e2e/officer/login-throttle.spec.ts`

**Steps:**
  1. Submit wrong password repeatedly (test DB, unique email)
    - expect: Eventually a retry/lockout message from LoginThrottle; correct login blocked during lockout

#### 1.6. Protected officer URLs redirect unauthenticated users

**File:** `e2e/officer/access.spec.ts`

**Steps:**
  1. Without session visit dashboard, applicants, detailed-applicant/1, exam-schedule, follow-up; POST payments/1/approve and applicants/1/decide
    - expect: Redirect to /admission-officer/login or 401; no state changed

#### 1.7. Officer logout

**File:** `e2e/officer/logout.spec.ts`

**Steps:**
  1. Click logout (/admission-officer/logout), press Back
    - expect: Back at login; protected pages redirect

### 2. Dashboard

**Seed:** `e2e/seed.spec.ts`

#### 2.1. Dashboard widgets render (REQUIRES OFFICER ACCOUNT)

**File:** `e2e/officer/dashboard.spec.ts`

**Steps:**
  1. Open dashboard
    - expect: Key metrics, applicants per program, applicants per status, passing rate, to-do list, calendar and overview table visible, no console errors

#### 2.2. Academic year and semester filters

**File:** `e2e/officer/dashboard-filters.spec.ts`

**Steps:**
  1. Change academic year and semester dropdowns
    - expect: /dashboard/data refetched and figures update

#### 2.3. Dashboard search

**File:** `e2e/officer/dashboard-search.spec.ts`

**Steps:**
  1. Type an applicant name, then nonsense string, then empty
    - expect: Matching dropdown results; 'no results' state; clearing hides dropdown; SQL/HTML characters handled safely

#### 2.4. Calendar shows exam schedules

**File:** `e2e/officer/dashboard-calendar.spec.ts`

**Steps:**
  1. Navigate months in calendar
    - expect: calendar-data loads per month, events displayed

### 3. Applicants list and review

**Seed:** `e2e/seed.spec.ts`

#### 3.1. Applicants list loads with pagination

**File:** `e2e/officer/applicants-list.spec.ts`

**Steps:**
  1. Open /admission-officer/applicants
    - expect: Table with name, program, status; pagination controls work

#### 3.2. Search, filter and sort applicants

**File:** `e2e/officer/applicants-filter.spec.ts`

**Steps:**
  1. Search by name/email, filter by status and program, sort a column; search no-match term
    - expect: Table updates through fetch-applicants; empty state for no match

#### 3.3. Open applicant detail and tabs

**File:** `e2e/officer/applicant-detail.spec.ts`

**Steps:**
  1. Click an applicant, switch through every tab
    - expect: Profile header and each tab content load via fetch-content without error
  2. Open /detailed-applicant/999999
    - expect: Not found message or redirect, no PHP error

#### 3.4. Review and approve a document

**File:** `e2e/officer/document-approve.spec.ts`

**Steps:**
  1. Open Requirements tab, view a document, approve it (POST decide)
    - expect: Document status Verified; applicant's dashboard checklist reflects it

#### 3.5. Reject a document requires reason

**File:** `e2e/officer/document-reject.spec.ts`

**Steps:**
  1. Reject without and with a reason
    - expect: Reason required; with reason status Rejected and applicant sees reason

#### 3.6. Approve applicant (SIS handoff stubbed)

**File:** `e2e/officer/applicant-approve.spec.ts`

**Steps:**
  1. Use throwaway applicant with all docs verified, click Approve
    - expect: Status Approved; reservation fee becomes available to applicant; no real email or SIS call (stubbed)

#### 3.7. Reject applicant

**File:** `e2e/officer/applicant-reject.spec.ts`

**Steps:**
  1. Click Reject with and without remarks
    - expect: Status Rejected; applicant notified (email stubbed)

#### 3.8. Request revision

**File:** `e2e/officer/request-revision.spec.ts`

**Steps:**
  1. Request revision with and without a note
    - expect: Applicant can edit and resubmit; empty note validated

#### 3.9. Bulk update applicants

**File:** `e2e/officer/bulk-update.spec.ts`

**Steps:**
  1. Select multiple rows and apply bulk action; try with none selected
    - expect: Bulk bar appears only with selection; statuses updated; no-selection rejected

#### 3.10. Decision on invalid state

**File:** `e2e/officer/decide-invalid.spec.ts`

**Steps:**
  1. POST /applicants/{id}/decide with invalid status value
    - expect: Rejected with error; status unchanged

### 4. Payment verification

**Seed:** `e2e/seed.spec.ts`

#### 4.1. View payment proof

**File:** `e2e/officer/payment-proof.spec.ts`

**Steps:**
  1. On admission fee tab open proof (/payments/{id}/proof)
    - expect: Image/PDF served; non-existent id returns 404

#### 4.2. Approve payment

**File:** `e2e/officer/payment-approve.spec.ts`

**Steps:**
  1. Approve a pending admission fee payment
    - expect: Status Verified; applicant sees paid state; double approve is idempotent

#### 4.3. Reject payment with reason

**File:** `e2e/officer/payment-reject.spec.ts`

**Steps:**
  1. Reject pending payment
    - expect: Status Rejected with reason; applicant can resubmit

#### 4.4. Reservation fee verification triggers SIS handoff (stub)

**File:** `e2e/officer/reservation-handoff.spec.ts`

**Steps:**
  1. Approve reservation payment for approved applicant with SIS stubbed
    - expect: Exactly one handoff payload to stub; failure of SIS does not corrupt payment status

### 5. Exam schedule

**Seed:** `e2e/seed.spec.ts`

#### 5.1. Exam schedule disabled page

**File:** `e2e/officer/exam-disabled.spec.ts`

**Steps:**
  1. With exam_enabled=0 open /admission-officer/exam-schedule
    - expect: Disabled view displayed

#### 5.2. List and calendar toggle

**File:** `e2e/officer/exam-views.spec.ts`

**Steps:**
  1. Toggle list/calendar views, month cards, date picker
    - expect: Both views show the same schedules

#### 5.3. Create exam schedule

**File:** `e2e/officer/exam-create.spec.ts`

**Steps:**
  1. Open create modal, fill date, time, venue, capacity, save
    - expect: Schedule appears in list; modal closes

#### 5.4. Create schedule validation

**File:** `e2e/officer/exam-create-invalid.spec.ts`

**Steps:**
  1. Submit empty; past date; end before start; capacity 0 or negative
    - expect: Validation errors, nothing created

#### 5.5. Edit and delete schedule

**File:** `e2e/officer/exam-edit-delete.spec.ts`

**Steps:**
  1. Edit capacity, then delete a schedule with and without bookings
    - expect: Changes saved; delete confirmed; deletion with bookings handled (blocked or bookings cancelled)

#### 5.6. Check in and cancel booking

**File:** `e2e/officer/exam-bookings.spec.ts`

**Steps:**
  1. In schedule details check in an applicant and cancel another's booking
    - expect: Attendance marked; cancelled slot freed

#### 5.7. Record exam result

**File:** `e2e/officer/exam-result.spec.ts`

**Steps:**
  1. Enter score pass/fail via exam tab (POST exam-bookings/{id}/result); try non-numeric and out-of-range score
    - expect: Valid saved and reflected in passing-rate; invalid rejected

### 6. Follow-up messages

**Seed:** `e2e/seed.spec.ts`

#### 6.1. Conversation list and unread count

**File:** `e2e/officer/followup-list.spec.ts`

**Steps:**
  1. Open /admission-officer/follow-up
    - expect: Conversations listed; unread badge matches /unread-count

#### 6.2. Send message to applicant

**File:** `e2e/officer/followup-send.spec.ts`

**Steps:**
  1. Open a conversation, send empty then 'QA follow-up'
    - expect: Empty blocked; message appears and is visible to the applicant

#### 6.3. Officer AI assistant (mocked)

**File:** `e2e/officer/ai-assistant.spec.ts`

**Steps:**
  1. Mock /api/groq/officer and send a prompt
    - expect: Mock reply rendered; no external call
