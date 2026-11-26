// M9 Payment  (TC-M9-01 .. TC-M9-06)
// spec: specs/admission-officer.md, specs/applicant-dashboard-and-form.md
// seed: e2e/seed.spec.ts
//
// Each describe logs in once in a beforeEach hook instead of repeating the login in every test.
import { test, expect } from './support/throttle';
import { BASE, hasApplicantCreds, loginAsApplicant, NO_CREDS_REASON } from './support/applicant';
import {
  OFFICER_BASE, fetchApplicantRows, hasOfficerCreds, loginAsOfficer, NO_OFFICER_CREDS_REASON,
} from './support/officer';
import { sessionGet, sessionPostForm } from './support/session-http';

test.describe('Applicant payment', () => {
  test.beforeEach(async ({ page }) => {
    test.skip(!hasApplicantCreds(), NO_CREDS_REASON);
    await loginAsApplicant(page);
  });

  test('Tuition fee calculator computes values', async ({ page }) => {
    await page.goto(BASE + 'tuition-fee-calculator');
    test.skip(
      new URL(page.url()).pathname.endsWith('/dashboard'),
      'Calculator disabled in the test DB (tuition_calculator_enabled != 1): /tuition-fee-calculator redirects to /dashboard. Selectors could not be observed.',
    );

    // 1. Page renders (interaction steps need the calculator enabled to confirm live selectors)
    await expect(page.getByRole('main').getByRole('heading').first()).toBeVisible();
  });
});

test.describe('Payment verification', () => {
  test.beforeEach(async ({ page }) => {
    test.skip(!hasOfficerCreds(), NO_OFFICER_CREDS_REASON);
    await loginAsOfficer(page);
  });

  test('View payment proof', async ({ page }) => {
    // 2. A non-existent payment id has no proof: 404
    const missing = await sessionGet(page, 'admission-officer/payments/999999999/proof');
    expect(missing.status).toBe(404);

    // 1. On the admission fee tab open the proof (look for an applicant that has one)
    const rows = await fetchApplicantRows(page);
    let proofPath: string | null = null;
    for (const row of rows.slice(0, 10)) {
      const res = await page.request.get(
        `${OFFICER_BASE}detailed-applicant/fetch-content?applicant_id=${row.applicant_id}&tab=admission_fee&status=all&search=&page=1`,
        { headers: { 'X-Requested-With': 'XMLHttpRequest' } },
      );
      const json = await res.json();
      const m = String(json.html ?? '').match(/admission-officer\/payments\/(\d+)\/proof/);
      if (m) { proofPath = `admission-officer/payments/${m[1]}/proof`; break; }
    }
    test.skip(!proofPath, 'No applicant with an uploaded admission-fee proof in the test DB');

    const proof = await sessionGet(page, proofPath!);
    expect(proof.status).toBe(200);
    expect(proof.contentType).toMatch(/^(image\/|application\/pdf)/);
  });

  test('Approve payment', async ({ page }) => {
    // Safe check: approving a payment that does not exist changes nothing
    const unknown = await sessionPostForm(page, 'admission-officer/payments/999999999/approve', { remarks: 'qa' });
    expect(unknown.status).toBe(200);
    expect(unknown.json).toMatchObject({ success: false, message: 'Payment record not found.' });

    // 1. Approve a pending admission fee payment; double approve is idempotent
    test.fixme(true, 'Approving marks a real payment Paid and queues a payment.approved email that cannot be stubbed from the browser; no disposable pending COUNTER payment is provisioned in the test DB.');
  });

  test('Reject payment with reason', async ({ page }) => {
    // Remarks are mandatory when rejecting (checked before any lookup)
    for (const remarks of ['', '   ']) {
      const res = await sessionPostForm(page, 'admission-officer/payments/999999999/reject', { remarks });
      expect(res.json, JSON.stringify(remarks)).toMatchObject({ success: false, message: 'Remarks are required when rejecting a payment.' });
    }
    // With remarks, an unknown payment is still refused
    const unknown = await sessionPostForm(page, 'admission-officer/payments/999999999/reject', { remarks: 'QA reason' });
    expect(unknown.json).toMatchObject({ success: false, message: 'Payment record not found.' });

    // 1. Reject pending payment: status Rejected with reason; applicant can resubmit
    test.fixme(true, 'Rejecting marks a real payment Rejected and queues a payment.rejected email that cannot be stubbed from the browser; no disposable pending COUNTER payment is provisioned in the test DB.');
  });

  test('Reservation fee verification triggers SIS handoff (stub)', async () => {
    // 1. Approve reservation payment for approved applicant with SIS stubbed
    test.fixme(true, 'The SIS handoff is a server-to-server HTTP call (SisSyncService, run in a post_system event) so page.route cannot intercept it, and approving the payment would also send a real email. Needs a disposable approved applicant with a pending COUNTER reservation payment, a stubbed SIS endpoint configured in the test environment, and a mail sink.');
    // Expected: exactly one handoff payload reaches the stub; an SIS failure does not corrupt the payment status
  });
});
