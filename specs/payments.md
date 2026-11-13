# Payments Test Plan

## Application Overview

Applicant payment pages: /admission-processing-fee, /reservation-fee, /fast-track-fee (GET page, POST /{fee}/submit, GET /{fee}/proof/{id}); SmartPay: POST /payment/admission-fee and /payment/reservation-fee, webhook POST /api/smartpay/payment-webhook. Manual submit validation (all three controllers): payment method id required|is_natural_no_zero; reference number required, 3-50 chars ('Please enter your transaction / reference number.'); proof_of_payment max 10 MB, ext jpg/jpeg/png/pdf ('File size must not exceed 10 MB.', 'Only JPG, PNG, and PDF files are allowed.'). Paying the reservation fee triggers the SIS handoff for approved applicants: SIS handoff, SmartPay calls, webhook and emails MUST be mocked/stubbed (page.route or test config); never perform real payments. Officer verification is in specs/admission-officer.md and fee/method config in specs/admin.md. Requires applicant account (E2E_EMAIL/E2E_PASSWORD) on the test DB. Written from code; live exploration blocked by db-on guard.

## Test Scenarios

### 1. Applicant payments

**Seed:** `e2e/seed.spec.ts`

#### 1.1. Payment pages require login

**File:** `e2e/payments/access.spec.ts`

**Steps:**
  1. Unauthenticated GET the three fee pages and proof URLs, POST submit endpoints
    - expect: Redirect to /sign-in; no payment record created; proof URLs return no file

#### 1.2. Fee page shows amount and active payment methods

**File:** `e2e/payments/fee-page.spec.ts`

**Steps:**
  1. Sign in, open each of the three fee pages
    - expect: Amount matches admin-configured fee; only active methods listed (toggled-off methods hidden); instructions/account details shown

#### 1.3. Reservation fee gated by application status

**File:** `e2e/payments/reservation-gate.spec.ts`

**Steps:**
  1. As applicant not yet approved open /reservation-fee
    - expect: Redirect (to dashboard or the fee page) or message that fee is not yet available

#### 1.4. Submit admission processing fee with valid proof

**File:** `e2e/payments/admission-fee-happy.spec.ts`

**Steps:**
  1. Select method, enter reference 'QA-REF-001', upload small PNG, submit (throwaway applicant)
    - expect: Success message; status shows pending verification; dashboard payment checklist updates
    - expect: Proof viewable via /admission-processing-fee/proof/{id}

#### 1.5. Missing payment method

**File:** `e2e/payments/validation-method.spec.ts`

**Steps:**
  1. Submit without selecting method on each fee page
    - expect: Validation error, no record created

#### 1.6. Reference number validation

**File:** `e2e/payments/validation-reference.spec.ts`

**Steps:**
  1. Submit empty, 'ab' (2 chars) and 51 chars
    - expect: Empty: 'Please enter your transaction / reference number.'; length errors for too short/long

#### 1.7. Proof of payment validation

**File:** `e2e/payments/validation-proof.spec.ts`

**Steps:**
  1. Submit with no file, with .exe/.txt, and with a file >10 MB
    - expect: Missing file error; 'Only JPG, PNG, and PDF files are allowed.'; 'File size must not exceed 10 MB.'

#### 1.8. Duplicate submission while pending

**File:** `e2e/payments/duplicate.spec.ts`

**Steps:**
  1. Submit the same fee twice rapidly (double click)
    - expect: Only one payment record; second attempt blocked/redirected

#### 1.9. Tampered method id or amount

**File:** `e2e/payments/tamper.spec.ts`

**Steps:**
  1. POST payment_method_id=0, =abc and a disabled method id
    - expect: Rejected by is_natural_no_zero / method check; amount is server-side, not client-supplied

#### 1.10. Cannot access another applicant's proof

**File:** `e2e/payments/proof-idor.spec.ts`

**Steps:**
  1. GET /reservation-fee/proof/{other id}
    - expect: Denied/404

#### 1.11. Fast track fee only for Fast Track applicants

**File:** `e2e/payments/fasttrack-gate.spec.ts`

**Steps:**
  1. Regular-path applicant opens /fast-track-fee
    - expect: Blocked or redirected

#### 1.12. Rejected payment can be resubmitted

**File:** `e2e/payments/resubmit.spec.ts`

**Steps:**
  1. REQUIRES OFFICER ACCOUNT to reject; then applicant sees rejection reason and submits again
    - expect: New submission accepted; status back to pending

### 2. SmartPay (mock required)

**Seed:** `e2e/seed.spec.ts`

#### 2.1. SmartPay checkout initiation (mocked)

**File:** `e2e/payments/smartpay-initiate.spec.ts`

**Steps:**
  1. Route-stub POST /payment/admission-fee and /payment/reservation-fee; click Pay online
    - expect: Browser is sent to the stubbed checkout URL; no external call made; unauthenticated POST rejected

#### 2.2. SmartPay webhook rejects invalid signature or payload

**File:** `e2e/payments/smartpay-webhook.spec.ts`

**Steps:**
  1. POST /api/smartpay/payment-webhook with empty body and with bad signature (test DB only)
    - expect: 4xx response; no payment marked paid; no SIS handoff triggered

#### 2.3. Reservation fee paid triggers SIS handoff (stubbed)

**File:** `e2e/payments/sis-handoff.spec.ts`

**Steps:**
  1. With SIS client stubbed, mark reservation fee paid for approved throwaway applicant via officer approval
    - expect: Stub receives one handoff with applicant and program name; real SIS is never contacted; repeat approval does not duplicate handoff
