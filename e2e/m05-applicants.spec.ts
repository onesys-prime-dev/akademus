// M5 Applicants  (TC-M5-01 .. TC-M5-06)
// spec: specs/admission-officer.md
// seed: e2e/seed.spec.ts
//
// Every test is an admission officer, so the login happens once in the beforeEach hook.
import { test, expect } from './support/throttle';
import {
  OFFICER_BASE, fetchApplicantRows, hasOfficerCreds, loginAsOfficer, NO_OFFICER_CREDS_REASON,
} from './support/officer';
import { sessionPostForm, sessionPostJson } from './support/session-http';

test.describe('Applicants list and review', () => {
  test.beforeEach(async ({ page }) => {
    test.skip(!hasOfficerCreds(), NO_OFFICER_CREDS_REASON);
    await loginAsOfficer(page);
  });

  test('Applicants list loads with pagination', async ({ page }) => {
    // 1. Open /admission-officer/applicants
    const [res] = await Promise.all([
      page.waitForResponse((r) => r.url().includes('/applicants/fetch-applicants')),
      page.goto(OFFICER_BASE + 'applicants'),
    ]);
    const first = await res.json();
    expect(first.page).toBe(1);

    // Table columns
    for (const col of ['Applicant Name', 'Email', 'Applicant ID', 'Applicant Type', 'Program', 'Status', 'Date Added']) {
      await expect(page.locator('.applicants-table__head').getByText(col, { exact: true })).toBeVisible();
    }

    const rows = page.locator('#applicants-table-body tr');
    if (first.total === 0) {
      await expect(rows.first()).toContainText('No applicants found');
      return;
    }
    await expect(page.locator('#applicants-table-body tr[data-row]')).toHaveCount(Math.min(first.total, first.limit));
    await expect(page.locator('#page-number')).toHaveText('1');
    await expect(page.locator('[data-filter="all"] .filters-button_count')).toHaveText(String(first.counts.all));

    // Pagination controls work when there is more than one page
    const totalPages = Math.ceil(first.total / first.limit);
    if (totalPages > 1) {
      const [next] = await Promise.all([
        page.waitForResponse((r) => r.url().includes('/applicants/fetch-applicants') && r.url().includes('page=2')),
        page.locator('[data-page="next"]').click(),
      ]);
      expect(next.ok()).toBe(true);
      await expect(page.locator('#page-number')).toHaveText('2');
      await Promise.all([
        page.waitForResponse((r) => r.url().includes('/applicants/fetch-applicants') && r.url().includes('page=1')),
        page.locator('[data-page="prev"]').click(),
      ]);
      await expect(page.locator('#page-number')).toHaveText('1');
    } else {
      await expect(page.locator('[data-page="next"]')).toHaveClass(/pagination__btn--hidden/);
    }
  });

  test('Search, filter and sort applicants', async ({ page }) => {
    await Promise.all([
      page.waitForResponse((r) => r.url().includes('/applicants/fetch-applicants')),
      page.goto(OFFICER_BASE + 'applicants'),
    ]);
    const search = page.locator('input[data-search]');
    const rows = page.locator('#applicants-table-body tr[data-row]');
    await expect(page.locator('#applicants-table-body tr').first()).toBeVisible();
    test.skip((await rows.count()) === 0, 'No applicants in the test DB');

    // 1a. Search by email of an existing applicant
    const email = (await rows.first().locator('td').nth(2).innerText()).trim();
    const [byEmail] = await Promise.all([
      page.waitForResponse((r) => r.url().includes('/applicants/fetch-applicants') && r.url().includes('search=')),
      search.fill(email),
    ]);
    expect((await byEmail.json()).total).toBeGreaterThan(0);
    await expect(rows.first().locator('td').nth(2)).toHaveText(email);

    // 1b. Search no-match term: empty state
    await Promise.all([
      page.waitForResponse((r) => r.url().includes('/applicants/fetch-applicants') && r.url().includes('search=zzqq')),
      search.fill(`zzqq${Date.now()}`),
    ]);
    await expect(page.locator('#applicants-table-body')).toContainText('No applicants found');

    // Reset search
    await Promise.all([
      page.waitForResponse((r) => r.url().includes('/applicants/fetch-applicants')),
      search.fill(''),
    ]);

    // 1c. Filter by status: the table is refetched, the active pill and its count follow the response
    for (const status of ['pending', 'approved', 'rejected', 'in_progress']) {
      const [res] = await Promise.all([
        page.waitForResponse((r) => r.url().includes('/applicants/fetch-applicants') && r.url().includes(`status=${status}`)),
        page.locator(`[data-filter="${status}"]`).click(),
      ]);
      const json = await res.json();
      await expect(page.locator(`[data-filter="${status}"]`)).toHaveClass(/filter-active/);
      await expect(page.locator(`[data-filter="${status}"] .filters-button_count`)).toHaveText(String(json.counts[status] ?? 0));
      if (json.total > 0) {
        await expect(page.locator('#applicants-table-body tr[data-row]')).toHaveCount(Math.min(json.total, json.limit));
      } else {
        await expect(page.locator('#applicants-table-body')).toContainText('No applicants found');
      }
    }
    await Promise.all([
      page.waitForResponse((r) => r.url().includes('/applicants/fetch-applicants')),
      page.locator('[data-filter="all"]').click(),
    ]);

    // 1d. Filter by program
    const programs = (await page.locator('select[data-program] option').evaluateAll((os) => os.map((o: any) => o.value))).filter(Boolean);
    if (programs.length) {
      const [res] = await Promise.all([
        page.waitForResponse((r) => r.url().includes('/applicants/fetch-applicants') && r.url().includes(`program=${programs[0]}`)),
        page.locator('select[data-program]').selectOption(programs[0]),
      ]);
      expect(res.ok()).toBe(true);
    }

    // Sorting: plan discrepancy. The table headers are plain <th> cells and fetch-applicants accepts
    // only status, program, search and page, so there is no sort control to exercise.
    await expect(page.locator('.applicants-table__head [data-sort], .applicants-table__head button')).toHaveCount(0);
  });

  test('Open applicant detail and tabs', async ({ page }) => {
    const errors: string[] = [];
    page.on('pageerror', (e) => errors.push(e.message));

    const rows = await fetchApplicantRows(page);
    test.skip(rows.length === 0, 'No applicants in the test DB');
    const applicant = rows[0];

    // 1. Click an applicant from the list
    await page.goto(OFFICER_BASE + 'applicants');
    await page.locator(`#applicants-table-body tr[data-id="${applicant.applicant_id}"]`).click();
    await expect(page).toHaveURL(new RegExp(`/admission-officer/detailed-applicant/${applicant.applicant_id}$`));

    // Profile header
    await expect(page.locator('.applicant-profile__name')).toBeVisible();
    await expect(page.locator('.applicant-profile__name')).not.toHaveText('Unknown Applicant');
    await expect(page.locator('.applicant-profile__id')).toContainText(String(applicant.applicant_number));

    // ...and switch through every tab; each loads through fetch-content without an error state
    const tabs = await page.locator('.application-tabs__item').evaluateAll((els) => els.map((e: any) => e.dataset.tab));
    expect(tabs).toEqual(expect.arrayContaining(['applicant_form', 'requirements', 'admission_fee', 'reservation_fee', 'clearance']));
    for (const tab of tabs) {
      if (tab === 'applicant_form') continue; // already rendered server side as the initial tab
      const [res] = await Promise.all([
        page.waitForResponse((r) => r.url().includes('/detailed-applicant/fetch-content') && r.url().includes(`tab=${tab}`)),
        page.locator(`.application-tabs__item[data-tab="${tab}"]`).click(),
      ]);
      const json = await res.json();
      expect(json.success, `tab ${tab}: ${json.message ?? ''}`).toBe(true);
      await expect(page.locator(`.application-tabs__item[data-tab="${tab}"]`)).toHaveClass(/application-tabs__item--active/);
      await expect(page.locator('#application-content')).not.toContainText('Failed to load content');
      await expect(page.locator('#application-content')).not.toContainText('Error loading tab');
    }

    // 2. A non-existent id: the controller redirects to the list (no 'not found' message, no PHP error)
    await page.goto(OFFICER_BASE + 'detailed-applicant/999999');
    await expect(page).toHaveURL(OFFICER_BASE + 'applicants');
    await expect(page.locator('body')).not.toContainText(/Fatal error|Stack trace|ErrorException/);
    expect(errors).toEqual([]);
  });

  test('Review and approve a document', async ({ page }) => {
    // Safe checks first: the decide endpoint only accepts approved / rejected (nothing is written).
    const bad = await sessionPostJson(page, 'admission-officer/applicants/document/1/decide', { status: 'bogus' });
    expect(bad.json).toMatchObject({ success: false, message: 'Invalid status' });
    const missing = await sessionPostJson(page, 'admission-officer/applicants/document/1/decide', {});
    expect(missing.json).toMatchObject({ success: false, message: 'Invalid status' });

    // 1. Open Requirements tab, view a document, approve it (POST decide)
    test.fixme(true, 'Approving flips a real applicant document to Verified and cannot be undone from the UI; no throwaway applicant with an uploaded document is provisioned in the test DB. Needs a seeded disposable applicant.');
  });

  test('Reject a document requires reason', async ({ page }) => {
    // Plan discrepancy (recorded, not asserted as a failure): the document modal has a single Reject
    // button with no reason field, and decideDocument() accepts only {status}. No reason can be given
    // or required, so "applicant sees reason" is not implemented.
    // What is verifiable without touching data: an unknown status is refused.
    const bad = await sessionPostJson(page, 'admission-officer/applicants/document/1/decide', { status: 'declined' });
    expect(bad.json).toMatchObject({ success: false, message: 'Invalid status' });

    // 1. Reject without and with a reason
    test.fixme(true, 'Rejecting changes a real applicant document; no throwaway applicant with an uploaded document exists in the test DB. Also the app has no rejection-reason input (see comment above).');
  });

  test('Bulk update applicants', async ({ page }) => {
    // Server side: no ids, bad status and unknown ids are all refused without touching data
    const none = await sessionPostJson(page, 'admission-officer/applicants/bulk-update', { ids: [], status: 'approved' });
    expect(none.json).toMatchObject({ success: false, message: 'Invalid payload' });
    const badStatus = await sessionPostJson(page, 'admission-officer/applicants/bulk-update', { ids: [1], status: 'bogus' });
    expect(badStatus.json).toMatchObject({ success: false, message: 'Invalid payload' });
    const unknown = await sessionPostJson(page, 'admission-officer/applicants/bulk-update', { ids: [999999999], status: 'pending' });
    expect(unknown.json).toMatchObject({ success: false, message: 'No valid applicants found' });

    // UI: the bulk bar is present but its actions stay disabled until rows are selected
    await Promise.all([
      page.waitForResponse((r) => r.url().includes('/applicants/fetch-applicants')),
      page.goto(OFFICER_BASE + 'applicants'),
    ]);
    const rows = page.locator('#applicants-table-body tr[data-row]');
    await expect(page.locator('#applicants-table-body tr').first()).toBeVisible();
    test.skip((await rows.count()) === 0, 'No applicants in the test DB');

    const approve = page.locator('.bulk-actions__btn--approve');
    const reject = page.locator('.bulk-actions__btn--reject');
    // 1a. Try with none selected
    await expect(approve).toBeDisabled();
    await expect(reject).toBeDisabled();

    // 1b. Select multiple rows
    await rows.nth(0).locator('[data-select]').check();
    await expect(approve).toBeEnabled();
    if ((await rows.count()) > 1) await rows.nth(1).locator('[data-select]').check();
    const selected = await page.locator('[data-select]:checked').count();

    // Opening the bulk dialog lists the selection; it is cancelled so nothing is changed
    await approve.click();
    await expect(page.locator('#bulk-modal-overlay')).toHaveClass(/active/);
    await expect(page.locator('#bulk-modal-title')).toHaveText('Bulk Approve Applicants');
    await expect(page.locator('#bulk-modal-subtitle')).toHaveText(`${selected} applicant(s) selected`);
    await expect(page.locator('#bulk-modal-list .bulk-modal-item')).toHaveCount(selected);
    await page.locator('#bulk-modal-cancel').click();
    await expect(page.locator('#bulk-modal-overlay')).not.toHaveClass(/active/);

    // Cancel clears the selection and disables the actions again
    await page.locator('.bulk-actions__btn--cancel').click();
    await expect(page.locator('[data-select]:checked')).toHaveCount(0);
    await expect(approve).toBeDisabled();

    // 1c. Apply a bulk action
    test.fixme(true, 'Applying approved/rejected rewrites real applicant statuses and queues emails / SIS handoffs that cannot be stubbed from the browser. Needs disposable applicants and a stubbed mailer/SIS.');
  });

  test('Approve applicant (SIS handoff stubbed)', async ({ page }) => {
    // Non-mutating part: the approve modal demands remarks before it will submit anything.
    const pending = await fetchApplicantRows(page, 'status=pending');
    test.skip(pending.length === 0, 'No pending applicant in the test DB to open the approve modal on');
    await page.goto(OFFICER_BASE + 'detailed-applicant/' + pending[0].applicant_id);

    await page.locator('#btn-approve').click();
    await expect(page.locator('#decision-modal-title')).toHaveText('Approve Application');
    await page.locator('#decision-modal-submit').click();
    await expect(page.locator('#decision-error')).toHaveText('Please add remarks before submitting.');

    // Templates fill the remarks box
    await page.locator('#decision-templates .decision-template-pill').first().click();
    await expect(page.locator('#decision-remarks')).not.toHaveValue('');
    await page.locator('#decision-modal-cancel').click();
    await expect(page.locator('#decision-modal-overlay')).not.toHaveClass(/active/);

    // 1. Use throwaway applicant with all docs verified, click Approve
    test.fixme(true, 'Approving queues a real email (NotificationService) and an SIS sync row whose HTTP call runs post-response; neither can be intercepted from the browser with page.route, and no disposable applicant is provisioned. Needs a seeded throwaway applicant plus a stubbed mailer/SIS endpoint.');
  });

  test('Reject applicant', async ({ page }) => {
    // Non-mutating part: the decline modal demands remarks before it will submit anything.
    const pending = await fetchApplicantRows(page, 'status=pending');
    test.skip(pending.length === 0, 'No pending applicant in the test DB to open the decline modal on');
    await page.goto(OFFICER_BASE + 'detailed-applicant/' + pending[0].applicant_id);

    // 1. Click Reject without remarks
    await page.locator('#btn-decline').click();
    await expect(page.locator('#decision-modal-title')).toHaveText('Decline Application');
    await page.locator('#decision-modal-submit').click();
    await expect(page.locator('#decision-error')).toHaveText('Please add remarks before submitting.');
    await page.locator('#decision-modal-cancel').click();
    await expect(page.locator('#decision-modal-overlay')).not.toHaveClass(/active/);

    // ...and with remarks
    test.fixme(true, 'Declining changes a real applicant status and queues an email that cannot be stubbed from the browser; no disposable applicant is provisioned. Needs a seeded throwaway applicant and a stubbed mailer.');
  });

  test('Decision on invalid state', async ({ page }) => {
    const rows = await fetchApplicantRows(page);
    const target = rows[0]?.applicant_id ?? 1;
    const before = rows[0]?.status;

    // 1. POST /applicants/{id}/decide with invalid status values
    for (const status of ['bogus', 'pending', 'in_progress', '', 'APPROVED']) {
      const res = await sessionPostForm(page, `admission-officer/applicants/${target}/decide`, { status, remarks: 'qa' });
      expect(res.json, `status=${JSON.stringify(status)}`).toMatchObject({ success: false, message: 'Invalid request.' });
    }
    // Missing status entirely
    const missing = await sessionPostForm(page, `admission-officer/applicants/${target}/decide`, { remarks: 'qa' });
    expect(missing.json).toMatchObject({ success: false, message: 'Invalid request.' });

    // Valid status on an applicant that does not exist
    const unknown = await sessionPostForm(page, 'admission-officer/applicants/999999999/decide', { status: 'approved', remarks: 'qa' });
    expect(unknown.json).toMatchObject({ success: false, message: 'Applicant not found.' });

    // Status unchanged
    if (rows[0]) {
      const after = (await fetchApplicantRows(page)).find((r) => r.applicant_id === target);
      expect(after?.status).toBe(before);
    }
    await page.goto(OFFICER_BASE + 'applicants');
    await expect(page).toHaveURL(OFFICER_BASE + 'applicants');
  });

  test('Request revision', async ({ page }) => {
    // Server side validation (applicant id 999999 never exists; validation runs before any lookup)
    const noNote = await sessionPostForm(page, 'admission-officer/applicants/999999/request-revision', { 'page_ids[]': '1', note: '   ' });
    expect(noNote.json).toMatchObject({ success: false, message: 'Select at least one step and provide a note.' });
    const noPages = await sessionPostForm(page, 'admission-officer/applicants/999999/request-revision', { note: 'QA note' });
    expect(noPages.json).toMatchObject({ success: false, message: 'Select at least one step and provide a note.' });
    const noApplication = await sessionPostForm(page, 'admission-officer/applicants/999999/request-revision', { 'page_ids[]': '1', note: 'QA note' });
    expect(noApplication.json).toMatchObject({ success: false, message: 'No submitted application found.' });

    // UI validation on a pending applicant (cancelled before anything is sent)
    const pending = await fetchApplicantRows(page, 'status=pending');
    test.skip(pending.length === 0, 'No pending applicant in the test DB to open the revision modal on');
    await page.goto(OFFICER_BASE + 'detailed-applicant/' + pending[0].applicant_id);
    const revisionButton = page.locator('#btn-revision');
    test.skip((await revisionButton.count()) === 0, 'Pending applicant already has a revision requested or a decision');

    // 1a. Request revision without a step
    await revisionButton.click();
    await page.locator('#revision-modal-submit').click();
    await expect(page.locator('#revision-error')).toHaveText('Select at least one step.');

    // 1b. With a step but without a note
    await page.locator('#revision-step-checklist input').first().check();
    await page.locator('#revision-modal-submit').click();
    await expect(page.locator('#revision-error')).toHaveText('Please add a note explaining the issue.');
    await page.locator('#revision-modal-cancel').click();
    await expect(page.locator('#revision-modal-overlay')).not.toHaveClass(/active/);

    // 1c. With a note: applicant can edit and resubmit
    test.fixme(true, 'A valid request sets the applicant application to needs_revision, inserts a follow-up message and queues an email (not stubbable from the browser); undoing needs the applicant to resubmit. No disposable applicant is provisioned.');
  });
});
