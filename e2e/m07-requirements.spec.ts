// M7 Requirements  (TC-M7-01 .. TC-M7-05)
// spec: specs/applicant-dashboard-and-form.md
// seed: e2e/seed.spec.ts
//
// The applicant logs in once in the beforeEach hook instead of in every test.
import { test, expect } from './support/throttle';
import { BASE, hasApplicantCreds, loginAsApplicant, NO_CREDS_REASON } from './support/applicant';

// Smallest valid PDF / PNG payloads
const PDF = Buffer.from('%PDF-1.1\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Count 0/Kids[]>>endobj\ntrailer<</Root 1 0 R>>\n%%EOF');
const PNG = Buffer.from(
  'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==',
  'base64',
);

test.describe('Requirements and document uploads', () => {
  test.beforeEach(async ({ page }) => {
    test.skip(!hasApplicantCreds(), NO_CREDS_REASON);
    await loginAsApplicant(page);
  });

  test('Requirements page lists documents with required flags', async ({ page }) => {
    // 1. Open /requirements
    await page.goto(BASE + 'requirements');
    await expect(page).toHaveTitle('Akademus | Requirements');
    await expect(page.getByRole('heading', { name: /Form Progress — Step 1 of \d+/ })).toBeVisible();
    await expect(page.getByRole('heading', { name: 'Personal Documents' })).toBeVisible();
    await expect(page.getByText('Birth Certificate').first()).toBeVisible();
    await expect(page.getByText('REQUIRED').first()).toBeVisible();
    await expect(page.locator('.upload-counter')).toHaveText(/^\d+\/\d+ required uploaded$/);
    expect(await page.locator('input[type=file]').count()).toBeGreaterThan(0);
  });

  test('Upload valid PDF and image', async ({ page }) => {
    // Uploads auto-save on file selection. The save endpoint is stubbed so no encrypted file is stored
    // for the shared E2E applicant and no "requirements.completed" email can be queued; the real
    // server-side storage/progress/view path is therefore not covered here.
    const uploads: Array<{ url: string; type: string; body: string }> = [];
    await page.route('**/requirements/update/*', async (route) => {
      const req = route.request();
      uploads.push({ url: req.url(), type: req.headers()['content-type'] ?? '', body: req.postData() ?? '' });
      await route.fulfill({ json: { status: 'success' } });
    });

    // 1. On a requirement step upload a small PDF, then a PNG
    await page.goto(BASE + 'requirements');
    await expect(page.getByRole('heading', { name: 'Personal Documents' })).toBeVisible();
    const inputs = page.locator('input[type=file]');

    await inputs.nth(0).setInputFiles({ name: 'birth-certificate.pdf', mimeType: 'application/pdf', buffer: PDF });
    await expect.poll(() => uploads.length, { timeout: 10_000 }).toBe(1);
    await expect(page.getByText('Auto-saved changes')).toBeVisible();

    await inputs.nth(1).setInputFiles({ name: 'good-moral.png', mimeType: 'image/png', buffer: PNG });
    await expect.poll(() => uploads.length, { timeout: 10_000 }).toBe(2);

    expect(uploads[0].type).toContain('multipart/form-data');
    expect(uploads[0].body).toContain('birth-certificate.pdf');
    expect(uploads[1].body).toContain('good-moral.png');
  });

  test('Reject invalid file type and oversize file', async () => {
    test.fixme(true, 'requirements.js has no client-side type/size validation (only the input accept hint), so rejection can only be checked against the real server, which would store files on the shared E2E applicant. Needs a throwaway applicant.');

    // 1. Upload .exe/.txt and a file above the size limit
    // Expected: clear error, nothing stored
  });

  test('Replace an uploaded document', async () => {
    test.fixme(true, 'Needs real uploads (stored encrypted files and review status) on a throwaway applicant; uploads are stubbed in "Upload valid PDF and image" to protect the shared E2E applicant.');

    // 1. Upload a second file to the same requirement
    // Expected: new file replaces old; status resets to pending review
  });

  test("Cannot view another applicant's document", async ({ page }) => {
    // 1. Request /requirements/document/{id} with ids that are not this applicant's.
    // (The controller scopes lookups by session applicant_id; without a second applicant's real id,
    // ids 1 and 999999 stand in for "someone else's / non-existent" documents.)
    for (const id of ['1', '999999']) {
      const response = await page.request.get(BASE + 'requirements/document/' + id, { maxRedirects: 0 });
      expect(response.status(), id).toBe(404);
      expect(response.headers()['content-type'] ?? '', id).not.toMatch(/pdf|image\//);
    }
  });

  test('Submit application blocked until required documents uploaded', async ({ page }) => {
    // Safety net: POST /requirements/submit unconditionally sets the application to "pending" on the
    // server (no missing-document check), so make sure this test can never reach it.
    const submitCalls: string[] = [];
    await page.route('**/requirements/submit', async (route) => {
      submitCalls.push(route.request().url());
      await route.abort();
    });
    // "Save & Continue" only autosaves (it does not validate) and posts to requirements/update/N.
    // Stub that save so stepping through the wizard changes nothing on the server.
    await page.route('**/requirements/update/*', (route) =>
      route.fulfill({ json: { status: 'success' } }));

    // 1. Step through to the final (Review) step, where "Submit Form" validates required documents
    await page.goto(BASE + 'requirements');
    await expect(page.getByRole('heading', { name: 'Personal Documents' })).toBeVisible();
    const submit = page.getByRole('button', { name: 'Submit Form' });
    const next = page.getByRole('button', { name: 'Save & Continue' });
    const maxSteps: number = await page.evaluate(() => (window as any).requirementsConfig.maxSteps);
    for (let step = 1; step < maxSteps; step++) {
      // wait for each step to finish loading before clicking again (a second click mid-transition skips a step)
      await next.click();
      await expect(page.getByRole('heading', { level: 4, name: `Form Progress — Step ${step + 1} of ${maxSteps}` })).toBeVisible();
    }
    await expect(submit).toBeVisible();

    const required = await page.locator('input[type=file][required]').count();
    test.skip(required === 0, 'All required documents already uploaded for this applicant; cannot exercise the blocked state.');

    const dialogs: string[] = [];
    page.on('dialog', async (d) => { dialogs.push(d.message()); await d.accept(); });
    await submit.click();

    await expect.poll(() => dialogs).toEqual(['Please fill out all required fields before proceeding.']);
    // the file inputs are visually hidden (their labels are the visible controls), so check the class
    await expect(page.locator('input[type=file].input-error').first()).toHaveClass(/input-error/);
    await expect(page.locator('#req-confirm-modal')).toBeHidden();
    expect(submitCalls).toEqual([]);
  });

  test('Submit application with all required documents (throwaway applicant)', async () => {
    test.fixme(true, 'Submitting locks the application, queues notification emails and can trigger payments/SIS handoff downstream; needs a disposable applicant plus a stubbed mailer.');

    // 1. Complete form, upload all required, submit
    // Expected: success, application locked, status pending review, dashboard and tracking progress update
  });

  test('Fast Track applicants only need fast-track required documents', async () => {
    test.fixme(true, 'The Fast Track path can only be chosen by an applicant with no application yet (choose-path redirects otherwise); the E2E applicant already has a draft. Needs a fresh throwaway applicant.');

    // 1. Choose Fast Track path, open /requirements
    // Expected: only is_fast_track_required documents are mandatory; others deferred
  });
});
