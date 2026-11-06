// M4 Exam Schedule  (TC-M4-01 .. TC-M4-07)
// spec: specs/admission-officer.md, specs/applicant-dashboard-and-form.md
// seed: e2e/seed.spec.ts
//
// Each describe logs in once in a beforeEach hook instead of repeating the login in every test.
import { test, expect } from './support/throttle';
import { BASE, ATTENDEE_APPLICANT_EMAIL, DOCS_APPLICANT_EMAIL, DROPOUT_APPLICANT_EMAIL, hasApplicantCreds, loginAsApplicant, NO_CREDS_REASON, pagePost } from './support/applicant';
import { OFFICER_BASE, hasOfficerCreds, loginAsOfficer, NO_OFFICER_CREDS_REASON } from './support/officer';
import { hasAdminCreds } from './support/admin';
import { acquireLock, releaseLock, setExamEnabled, withExamEnabled } from './support/exam-setting';
import { EXAM_URL, createScheduleApi, dateAhead, deleteScheduleApi, showScheduleApi } from './support/exam';
import { sessionGet, sessionPostForm } from './support/session-http';
import type { Browser, Page } from '@playwright/test';

// A signed-in applicant in its own browser context (so it can run next to the officer's page).
async function applicantPage(browser: Browser, email: string): Promise<Page> {
  const page = await (await browser.newContext()).newPage();
  await loginAsApplicant(page, { email });
  return page;
}

// Cancels every live booking of a schedule (an attended one too) and deletes it, so the seeded
// applicants are free to book again on the next run. Runs in finally blocks.
async function cleanUpSchedule(officer: Page, id: number | string) {
  try {
    const { applicants } = await showScheduleApi(officer, id);
    for (const a of applicants ?? []) {
      if (a.status !== 'cancelled') await sessionPostForm(officer, `admission-officer/exam-schedule/bookings/cancel/${a.booking_id}`, {});
    }
  } finally {
    await deleteScheduleApi(officer, id);
  }
}

// exam_enabled is one global flag that these tests flip and read, and the schedules share calendar days,
// so the whole module runs in one worker, in order.
// The timeout is generous because tests wait for the exam lock while another browser project holds it.
test.describe.configure({ mode: 'serial', timeout: 180_000 });

const MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

// ---------------------------------------------------------------------------------------------
// Applicant side: TC-M4-03 .. TC-M4-06
// ---------------------------------------------------------------------------------------------
test.describe('Applicant exam schedule', () => {
  test.beforeEach(async ({ page }) => {
    test.skip(!hasApplicantCreds(), NO_CREDS_REASON);
    await loginAsApplicant(page);
  });

  test('Book and cancel an exam slot', async ({ browser }) => {
    test.skip(!hasOfficerCreds(), NO_OFFICER_CREDS_REASON);
    // The officer creates a fresh slot for every run, so the (schedule, applicant) pair is always new and no
    // re-seed is needed. The applicant is the seeded "e2e.docs" account: all required documents approved.
    const officerCtx = await browser.newContext();
    const officer = await officerCtx.newPage();
    await loginAsOfficer(officer);
    const applicant = await applicantPage(browser, DOCS_APPLICANT_EMAIL);

    await withExamEnabled(browser, async () => {
      const location = `QA Book ${Date.now()}`;
      const scheduleId = await createScheduleApi(officer, { date: dateAhead(0).iso, location, slots: 2, start: '21:00', end: '22:00' });
      try {
        // 1. Documents are complete, so the page opens and the sidebar link is unlocked
        await applicant.goto(BASE + 'exam-schedule');
        test.skip(applicant.url() === BASE + 'dashboard', 'Exam scheduling is disabled, or e2e.docs is missing / has incomplete documents (run npm run e2e:db:seed once)');
        await expect(applicant.locator('a.sidebar-nav-link', { hasText: 'Exam Schedule' })).not.toHaveClass(/disabled-link/);
        await expect(applicant.getByText('Choose an Exam Slot')).toBeVisible();

        const slotRow = applicant.locator('.es-slots-table tbody tr', { hasText: location });
        await expect(slotRow).toContainText('2 left');

        // 2. Book the slot: the booking card shows it as Scheduled
        const [book] = await Promise.all([
          applicant.waitForResponse((r) => r.url().includes('/exam-schedule/book')),
          slotRow.getByRole('button', { name: 'Book' }).click(),
        ]);
        expect(await book.json()).toMatchObject({ success: true });
        await expect(applicant.getByText('My Exam Schedule')).toBeVisible();
        await expect(applicant.locator('.es-badge--scheduled')).toHaveText('Scheduled');
        await expect(applicant.locator('.es-page').getByText(location)).toBeVisible();
        expect(Number((await showScheduleApi(officer, scheduleId)).schedule.booked_slots)).toBe(1);

        // 3. Cancel: the slot is offered again with its seat back
        applicant.once('dialog', (d) => d.accept());
        const [cancel] = await Promise.all([
          applicant.waitForResponse((r) => r.url().includes('/exam-schedule/cancel')),
          applicant.locator('#btn-cancel-booking').click(),
        ]);
        expect(await cancel.json()).toMatchObject({ success: true });
        await expect(applicant.getByText('Choose an Exam Slot')).toBeVisible();
        await expect(applicant.locator('.es-slots-table tbody tr', { hasText: location })).toContainText('2 left');
        expect(Number((await showScheduleApi(officer, scheduleId)).schedule.booked_slots)).toBe(0);
      } finally {
        await cleanUpSchedule(officer, scheduleId);
        await officerCtx.close();
        await applicant.context().close();
      }
    });
  });

  test('Cannot book full or non-existent slot', async ({ page }) => {
    // 1. POST /exam-schedule/book with a non-existent slot id. With exam scheduling disabled the
    // endpoint answers "Exam scheduling is disabled."; with it enabled it must reject the unknown slot.
    // Either way: success false with a message. (A genuinely full slot needs an officer-created fixture.)
    for (const field of ['slot_id', 'schedule_id']) {
      const res = await pagePost(page, 'exam-schedule/book', { [field]: '999999' });
      expect(res.json, field).toMatchObject({ success: false });
      expect(typeof res.json.message, field).toBe('string');
      expect(res.json.message.length, field).toBeGreaterThan(0);
    }
  });

  test('Exam schedule is locked until required documents are complete', async ({ page, browser }) => {
    // The E2E applicant is a fresh draft with no uploaded documents, so the page must stay closed:
    // the sidebar shows Exam Schedule locked and a direct visit bounces back to the dashboard.
    await withExamEnabled(browser, async () => {
      await page.goto(BASE + 'dashboard');
      const link = page.locator('a.sidebar-nav-link', { hasText: 'Exam Schedule' });
      test.skip(!(await link.count()), 'Exam scheduling is disabled and no admin credentials are available to enable it');
      test.skip(!(await link.getAttribute('class'))!.includes('disabled-link'), 'The E2E applicant already has all required documents, so Exam Schedule is unlocked');

      // 1. Sidebar link is locked (no real href, lock icon shown)
      await expect(link).toHaveClass(/disabled-link/);
      await expect(link).toHaveAttribute('href', 'javascript:void(0)');
      await expect(link.locator('.lock-icon')).toBeVisible();

      // 2. Opening the page directly redirects to the dashboard
      await page.goto(BASE + 'exam-schedule');
      await expect(page).toHaveURL(BASE + 'dashboard');
    });
  });

  test('Exam schedule redirects to dashboard when exam disabled', async ({ page, browser }) => {
    const check = async () => {
      // 1. With exam_enabled=0 visit /exam-schedule (the test switches it OFF first and restores it afterwards).
      // Note: the E2E applicant has no verified documents either, so the docs gate redirects here too;
      // the "locked until required documents are complete" test covers that gate on its own.
      await page.goto(BASE + 'exam-schedule');
      await expect(page).toHaveURL(BASE + 'dashboard');
    };

    if (!hasAdminCreds()) return check();

    // Make sure the flag is OFF for the check, then restore whatever it was.
    await acquireLock();
    let previous: boolean | null = null;
    try {
      previous = await setExamEnabled(browser, false);
      await check();
    } finally {
      try {
        if (previous === true) await setExamEnabled(browser, true);
      } finally {
        releaseLock();
      }
    }
  });
});

// ---------------------------------------------------------------------------------------------
// Officer side: TC-M4-01, TC-M4-02, TC-M4-06, TC-M4-07
// ---------------------------------------------------------------------------------------------
test.describe('Officer exam schedule', () => {
  test.beforeEach(async ({ page }) => {
    test.skip(!hasOfficerCreds(), NO_OFFICER_CREDS_REASON);
    await loginAsOfficer(page);
  });

  test('Exam schedule disabled page', async ({ page, browser }) => {
    const check = async () => {
      // 1. With exam_enabled=0 open /admission-officer/exam-schedule
      await page.goto(OFFICER_BASE + 'exam-schedule');
      const heading = page.getByRole('heading', { name: 'Entrance Exam is Disabled' });
      test.skip(!(await heading.isVisible()) && !hasAdminCreds(), 'exam_enabled is ON in this DB and no admin credentials are available to switch it off');
      await expect(heading).toBeVisible();
      await expect(page.getByText('Exam scheduling has been turned off by the administrator.')).toBeVisible();
      await expect(page.locator('[data-calendar], #exam-schedule-table-body')).toHaveCount(0);

      // The AJAX content endpoint refuses too
      const content = await sessionGet(page, 'admission-officer/exam-schedule/content?view=list');
      expect(content.status).toBe(403);
      expect(content.json).toMatchObject({ success: false, message: 'Exam scheduling is currently disabled.' });
    };

    if (!hasAdminCreds()) return check();

    // Make sure the flag is OFF for the check, then restore whatever it was.
    await acquireLock();
    let previous: boolean | null = null;
    try {
      previous = await setExamEnabled(browser, false);
      await check();
    } finally {
      try {
        if (previous === true) await setExamEnabled(browser, true);
      } finally {
        releaseLock();
      }
    }
  });

  test('Create exam schedule', async ({ page, browser }) => {
    await withExamEnabled(browser, async () => {
      // The list view of the current month; the new schedule uses today's date so it lands in that month
      await page.goto(`${EXAM_URL}?view=list`);
      test.skip(await page.getByRole('heading', { name: 'Entrance Exam is Disabled' }).isVisible(), 'Exam scheduling is disabled and no admin credentials are available to enable it');

      const location = `QA Create ${Date.now()}`;
      let createdId: string | null = null;
      try {
        // 1. Open create modal, fill date, time, venue, capacity, save
        await page.locator('[data-open-schedule-modal="create"]').click();
        const modal = page.locator('#exam-schedule-modal');
        await expect(modal).toBeVisible();
        const form = modal.locator('#exam-schedule-form');
        await form.locator('[name=exam_date]').fill(dateAhead(0).iso);
        await form.locator('[name=start_time]').fill('21:00');
        await form.locator('[name=end_time]').fill('22:00');
        await form.locator('[name=location]').fill(location);
        await form.locator('[name=max_slots]').fill('2');

        const [created] = await Promise.all([
          page.waitForResponse((r) => r.url().includes('/exam-schedule/create')),
          modal.getByRole('button', { name: 'Save' }).click(),
        ]);
        const body = await created.json();
        expect(body.success).toBe(true);
        createdId = String(body.id);

        // Modal closes and the schedule appears in the list
        // (the app hides modals via [hidden] + opacity:0 while keeping display:flex, so check the attribute)
        await expect(modal).toHaveAttribute('hidden', '');
        await Promise.all([
          page.waitForResponse((r) => r.url().includes('/exam-schedule/content') && r.url().includes('search=')),
          page.locator('input[data-search]').fill(location),
        ]);
        const row = page.locator(`tr[data-schedule-id="${createdId}"]`);
        await expect(row).toContainText(location);
        await expect(row).toContainText('0 / 2');
        await expect(row.locator('.status-badge')).toContainText('Open');
      } finally {
        if (createdId) await deleteScheduleApi(page, createdId);
      }
    });
  });

  test('Create schedule validation (browser constraints)', async ({ page, browser }) => {
    await withExamEnabled(browser, async () => {
      await page.goto(`${EXAM_URL}?view=list`);
      test.skip(await page.getByRole('heading', { name: 'Entrance Exam is Disabled' }).isVisible(), 'Exam scheduling is disabled and no admin credentials are available to enable it');

      let created = false;
      page.on('response', (r) => { if (r.url().includes('/exam-schedule/create')) created = true; });

      await page.locator('[data-open-schedule-modal="create"]').click();
      const modal = page.locator('#exam-schedule-modal');
      const form = modal.locator('#exam-schedule-form');
      await expect(modal).toBeVisible();

      // 1a. Submit empty: blocked by the required attributes
      await modal.getByRole('button', { name: 'Save' }).click();
      await expect(modal).toBeVisible();
      expect(await form.locator('[name=exam_date]').evaluate((el: HTMLInputElement) => el.validity.valueMissing)).toBe(true);

      // 1b. Capacity 0 (and a negative number) violates min=1
      await form.locator('[name=exam_date]').fill(dateAhead(0).iso);
      await form.locator('[name=start_time]').fill('21:00');
      await form.locator('[name=end_time]').fill('22:00');
      await form.locator('[name=location]').fill(`QA Invalid ${Date.now()}`);
      for (const bad of ['0', '-3']) {
        await form.locator('[name=max_slots]').fill(bad);
        await modal.getByRole('button', { name: 'Save' }).click();
        await expect(modal).toBeVisible();
        expect(await form.locator('[name=max_slots]').evaluate((el: HTMLInputElement) => el.validity.rangeUnderflow), bad).toBe(true);
      }
      expect(created).toBe(false);
    });
  });

  test('Server rejects past date and end before start', async ({ page }) => {
    // CONFIRMED APP BUG: ExamScheduleController::create() always answers {success:true}; the service inserts
    // whatever it is given (past dates, end before start, capacity 0 or negative). Remove this test.fail
    // once server side validation exists.
    test.fail(true, 'App bug: POST exam-schedule/create has no server-side validation (past date, end before start, capacity <= 0 are all accepted)');

    const stamp = Date.now();
    const payloads = [
      { label: 'past date', exam_date: '2020-01-01', start_time: '09:00', end_time: '10:00', max_slots: '3' },
      { label: 'end before start', exam_date: dateAhead(440).iso, start_time: '10:00', end_time: '09:00', max_slots: '3' },
      { label: 'capacity 0', exam_date: dateAhead(440).iso, start_time: '09:00', end_time: '10:00', max_slots: '0' },
      { label: 'capacity negative', exam_date: dateAhead(440).iso, start_time: '09:00', end_time: '10:00', max_slots: '-5' },
    ];
    const createdIds: string[] = [];
    try {
      for (const p of payloads) {
        const { label, ...fields } = p;
        const res = await sessionPostForm(page, 'admission-officer/exam-schedule/create', { ...fields, location: `QA Bad ${label} ${stamp}` });
        if (res.json?.success && res.json.id) createdIds.push(String(res.json.id));
        expect(res.json?.success, label).toBe(false);
      }
    } finally {
      // Never leave the invalid rows behind
      for (const id of createdIds) await deleteScheduleApi(page, id);
    }
  });

  test('Edit and delete schedule', async ({ page, browser }) => {
    await withExamEnabled(browser, async () => {
      await page.goto(`${EXAM_URL}?view=list`);
      test.skip(await page.getByRole('heading', { name: 'Entrance Exam is Disabled' }).isVisible(), 'Exam scheduling is disabled and no admin credentials are available to enable it');

      // A throwaway schedule in the current month (today's date) so the list shows it
      const location = `QA Edit ${Date.now()}`;
      const id = await createScheduleApi(page, { date: dateAhead(0).iso, location, slots: 2, start: '21:00', end: '22:00' });
      let deleted = false;
      try {
        await page.goto(`${EXAM_URL}?view=list`);
        await Promise.all([
          page.waitForResponse((r) => r.url().includes('/exam-schedule/content') && r.url().includes('search=')),
          page.locator('input[data-search]').fill(location),
        ]);
        const row = page.locator(`tr[data-schedule-id="${id}"]`);
        await expect(row).toContainText('0 / 2');

        // 1a. Edit capacity
        await row.locator('[data-action="edit"]').click();
        const modal = page.locator('#exam-schedule-modal');
        await expect(modal).toBeVisible();
        const form = modal.locator('#exam-schedule-form');
        await expect(form.locator('[name=location]')).toHaveValue(location);
        await form.locator('[name=max_slots]').fill('5');
        const [updated] = await Promise.all([
          page.waitForResponse((r) => r.url().includes(`/exam-schedule/update/${id}`)),
          modal.getByRole('button', { name: 'Save' }).click(),
        ]);
        expect((await updated.json()).success).toBe(true);
        // (the app hides modals via [hidden] + opacity:0 while keeping display:flex, so check the attribute)
        await expect(modal).toHaveAttribute('hidden', '');
        await expect(page.locator(`tr[data-schedule-id="${id}"]`)).toContainText('0 / 5');
        expect(Number((await showScheduleApi(page, id)).schedule.max_slots)).toBe(5);

        // 1b. Delete a schedule without bookings (confirmed through the browser dialog)
        page.once('dialog', (d) => d.accept());
        const [del] = await Promise.all([
          page.waitForResponse((r) => r.url().includes(`/exam-schedule/delete/${id}`)),
          page.locator(`tr[data-schedule-id="${id}"] [data-action="delete"]`).click(),
        ]);
        expect((await del.json()).success).toBe(true);
        deleted = true;
        await expect(page.locator(`tr[data-schedule-id="${id}"]`)).toHaveCount(0);
        expect((await showScheduleApi(page, id)).schedule).toBeNull();
      } finally {
        if (!deleted) await deleteScheduleApi(page, id);
      }
    });
  });

  test('Delete a schedule that has bookings', async ({ page, browser }) => {
    // ExamScheduleService::deleteSchedule() cancels the schedule's bookings before deleting it (and the
    // controller emails the affected applicants), so the applicant is not left holding an orphaned booking
    // and can book another slot afterwards.

    const dropout = await applicantPage(browser, DROPOUT_APPLICANT_EMAIL);
    await withExamEnabled(browser, async () => {
      await page.goto(`${EXAM_URL}?view=list`);
      test.skip(await page.getByRole('heading', { name: 'Entrance Exam is Disabled' }).isVisible(), 'Exam scheduling is disabled and no admin credentials are available to enable it');

      const stamp = Date.now();
      const first = await createScheduleApi(page, { date: dateAhead(0).iso, location: `QA Doomed ${stamp}`, slots: 2, start: '21:00', end: '22:00' });
      const second = await createScheduleApi(page, { date: dateAhead(0).iso, location: `QA Next ${stamp}`, slots: 2, start: '22:00', end: '23:00' });
      let orphanBookingId: string | null = null;
      try {
        await dropout.goto(BASE + 'exam-schedule');
        test.skip(dropout.url() === BASE + 'dashboard', 'Seeded applicant is missing or has incomplete documents (run npm run e2e:db:seed once)');
        expect((await pagePost(dropout, 'exam-schedule/book', { schedule_id: String(first) })).json).toMatchObject({ success: true });
        orphanBookingId = String((await showScheduleApi(page, first)).applicants[0].booking_id);

        // 1. The officer deletes the schedule that holds a booking
        await deleteScheduleApi(page, first);

        // 2. The applicant must be able to book another slot afterwards
        const res = await pagePost(dropout, 'exam-schedule/book', { schedule_id: String(second) });
        expect(res.json, 'applicant is stuck with an orphaned booking').toMatchObject({ success: true });
      } finally {
        if (orphanBookingId) await sessionPostForm(page, `admission-officer/exam-schedule/bookings/cancel/${orphanBookingId}`, {});
        await cleanUpSchedule(page, second);
        await deleteScheduleApi(page, first);
        await dropout.context().close();
      }
    });
  });

  test('List and calendar toggle', async ({ page, browser }) => {
    await withExamEnabled(browser, async () => {
      await page.goto(EXAM_URL);
      test.skip(await page.getByRole('heading', { name: 'Entrance Exam is Disabled' }).isVisible(), 'Exam scheduling is disabled and no admin credentials are available to enable it');

      // A throwaway schedule far enough ahead to sit alone on its day
      const when = dateAhead(430);
      const location = `QA Views ${Date.now()}`;
      const id = await createScheduleApi(page, { date: when.iso, location });
      try {
        // 1a. Calendar view of that month shows the day with the schedule
        await page.goto(`${EXAM_URL}?view=calendar&month=${when.month}&year=${when.year}`);
        await expect(page.locator('[data-month-current]')).toHaveText(`${MONTHS[when.month - 1]} ${when.year}`);
        await expect(page.locator('[data-calendar] [data-day]')).not.toHaveCount(0);
        const dayCell = page.locator(`[data-calendar] [data-day="${when.day}"]`);
        await expect(dayCell).toHaveClass(/is-clickable/);
        await expect(dayCell).toContainText('1 open');

        // 1b. Toggle to list: the same schedule is listed
        await Promise.all([
          page.waitForResponse((r) => r.url().includes('/exam-schedule/content') && r.url().includes('view=list')),
          page.locator('[data-view="list"]').click(),
        ]);
        await expect(page.locator('.exam-schedule__toggle-item--list')).toHaveClass(/toggle--item--active/);
        await expect(page.locator(`tr[data-schedule-id="${id}"]`)).toContainText(location);

        // ...and back to the calendar
        await Promise.all([
          page.waitForResponse((r) => r.url().includes('/exam-schedule/content') && r.url().includes('view=calendar')),
          page.locator('[data-view="calendar"]').click(),
        ]);
        await expect(page.locator('[data-calendar]')).toBeVisible();

        // 1c. Month cards move the month
        const nextMonth = when.month === 12 ? 1 : when.month + 1;
        const nextYear = when.month === 12 ? when.year + 1 : when.year;
        await Promise.all([
          page.waitForResponse((r) => r.url().includes('/exam-schedule/content')),
          page.locator('[data-month-next]').click(),
        ]);
        await expect(page.locator('[data-month-current]')).toHaveText(`${MONTHS[nextMonth - 1]} ${nextYear}`);
        await Promise.all([
          page.waitForResponse((r) => r.url().includes('/exam-schedule/content')),
          page.locator('[data-month-prev]').click(),
        ]);
        await expect(page.locator('[data-month-current]')).toHaveText(`${MONTHS[when.month - 1]} ${when.year}`);

        // 1d. Date picker jumps to another month
        await page.locator('[data-picker-trigger]').click();
        await expect(page.locator('[data-picker] [data-dropdown]')).toBeVisible();
        const targetMonth = when.month === 1 ? 12 : when.month - 1;
        await page.locator('[data-picker] [data-month]').selectOption(String(targetMonth));
        await Promise.all([
          page.waitForResponse((r) => r.url().includes('/exam-schedule/content')),
          page.locator('[data-picker] [data-apply]').click(),
        ]);
        await expect(page.locator('[data-month-current]')).toContainText(MONTHS[targetMonth - 1]);
      } finally {
        await deleteScheduleApi(page, id);
      }
    });
  });

  test('Check in and cancel booking', async ({ page, browser }) => {
    // Two seeded eligible applicants book a schedule the test creates itself; the officer checks one in
    // and cancels the other's booking from the schedule details. Cleanup cancels what is left and deletes
    // the schedule, so the applicants can book again on the next run.
    const attendee = await applicantPage(browser, ATTENDEE_APPLICANT_EMAIL);
    const dropout = await applicantPage(browser, DROPOUT_APPLICANT_EMAIL);

    await withExamEnabled(browser, async () => {
      await page.goto(`${EXAM_URL}?view=list`);
      test.skip(await page.getByRole('heading', { name: 'Entrance Exam is Disabled' }).isVisible(), 'Exam scheduling is disabled and no admin credentials are available to enable it');

      const location = `QA CheckIn ${Date.now()}`;
      const scheduleId = await createScheduleApi(page, { date: dateAhead(0).iso, location, slots: 3, start: '21:00', end: '22:00' });
      try {
        // Both applicants book through the applicant flow
        for (const applicant of [attendee, dropout]) {
          await applicant.goto(BASE + 'exam-schedule');
          test.skip(applicant.url() === BASE + 'dashboard', 'Seeded applicant is missing or has incomplete documents (run npm run e2e:db:seed once)');
          const res = await pagePost(applicant, 'exam-schedule/book', { schedule_id: String(scheduleId) });
          expect(res.json).toMatchObject({ success: true });
        }

        // 1. Open the schedule details from the list
        await page.goto(`${EXAM_URL}?view=list`);
        await Promise.all([
          page.waitForResponse((r) => r.url().includes('/exam-schedule/content') && r.url().includes('search=')),
          page.locator('input[data-search]').fill(location),
        ]);
        const row = page.locator(`tr[data-schedule-id="${scheduleId}"]`);
        await expect(row).toContainText('2 / 3');
        await Promise.all([
          page.waitForResponse((r) => r.url().includes(`/exam-schedule/show/${scheduleId}`)),
          row.locator('td').first().click(),
        ]);
        const details = page.locator('#schedule-details-modal');
        await expect(details).toBeVisible();
        const attendeeRow = details.locator('[data-applicants-body] tr', { hasText: 'Attendee' });
        const dropoutRow = details.locator('[data-applicants-body] tr', { hasText: 'Dropout' });
        await expect(attendeeRow.locator('.status-badge')).toContainText('Booked');
        await expect(dropoutRow.locator('.status-badge')).toContainText('Booked');

        // 2. Check in the attendee: status becomes Attended and the check-in time is set
        await Promise.all([
          page.waitForResponse((r) => r.url().includes('/bookings/check-in/')),
          attendeeRow.getByRole('button', { name: 'Check-in' }).click(),
        ]);
        await expect(attendeeRow.locator('.status-badge')).toContainText('Attended');
        await expect(attendeeRow).not.toContainText('Not yet');
        await expect(attendeeRow.getByRole('button', { name: 'Check-in' })).toHaveCount(0);

        // 3. Cancel the other booking (confirmed through the browser dialog): the seat is freed
        page.once('dialog', (d) => d.accept());
        await Promise.all([
          page.waitForResponse((r) => r.url().includes('/bookings/cancel/')),
          dropoutRow.getByRole('button', { name: 'Cancel' }).click(),
        ]);
        await expect(dropoutRow.locator('.status-badge')).toContainText('Cancelled');
        await expect(dropoutRow.getByRole('button')).toHaveCount(0);
        expect(Number((await showScheduleApi(page, scheduleId)).schedule.booked_slots)).toBe(1);

        // The cancelled applicant sees the slot again; the checked-in one keeps an Attended booking
        await dropout.goto(BASE + 'exam-schedule');
        await expect(dropout.locator('.es-slots-table tbody tr', { hasText: location })).toContainText('2 left');
        await attendee.goto(BASE + 'exam-schedule');
        await expect(attendee.locator('.es-badge--attended')).toBeVisible();
      } finally {
        await cleanUpSchedule(page, scheduleId);
        await attendee.context().close();
        await dropout.context().close();
      }
    });
  });

  test('Record exam result', async ({ page, browser }) => {
    // Safe checks: an unknown / empty result status is refused before anything is written
    for (const result_status of ['', 'MAYBE', 'passed!']) {
      const res = await sessionPostForm(page, 'admission-officer/exam-bookings/999999999/result', { result_status, score: '80' });
      expect(res.json, JSON.stringify(result_status)).toMatchObject({ success: false, message: 'Result status is required.' });
    }

    // 1. A seeded applicant books a fresh schedule and is checked in; the officer records pass, then fail.
    // (The server does not validate the score: a non-numeric value is cast to 0 and out-of-range values are
    // stored; only the browser input enforces min 0 / max 100, so that is not asserted here.)
    const attendee = await applicantPage(browser, ATTENDEE_APPLICANT_EMAIL);
    await withExamEnabled(browser, async () => {
      await page.goto(`${EXAM_URL}?view=list`);
      test.skip(await page.getByRole('heading', { name: 'Entrance Exam is Disabled' }).isVisible(), 'Exam scheduling is disabled and no admin credentials are available to enable it');

      const scheduleId = await createScheduleApi(page, { date: dateAhead(0).iso, location: `QA Result ${Date.now()}`, slots: 2, start: '21:00', end: '22:00' });
      try {
        await attendee.goto(BASE + 'exam-schedule');
        test.skip(attendee.url() === BASE + 'dashboard', 'Seeded applicant is missing or has incomplete documents (run npm run e2e:db:seed once)');
        expect((await pagePost(attendee, 'exam-schedule/book', { schedule_id: String(scheduleId) })).json).toMatchObject({ success: true });
        const bookingId = String((await showScheduleApi(page, scheduleId)).applicants[0].booking_id);
        expect((await sessionPostForm(page, `admission-officer/exam-schedule/bookings/check-in/${bookingId}`, {})).json).toMatchObject({ success: true });

        // Passed with a score: the applicant sees the result
        const passed = await sessionPostForm(page, `admission-officer/exam-bookings/${bookingId}/result`, { result_status: 'PASSED', score: '85', result_remarks: 'QA remark' });
        expect(passed.json).toMatchObject({ success: true, message: 'Exam result saved successfully.' });
        await attendee.goto(BASE + 'exam-schedule');
        await expect(attendee.locator('.es-badge--result-passed')).toHaveText('Passed');
        await expect(attendee.locator('.es-page')).toContainText('85');
        await expect(attendee.locator('.es-page')).toContainText('QA remark');

        // The officer can change it to Failed
        const failed = await sessionPostForm(page, `admission-officer/exam-bookings/${bookingId}/result`, { result_status: 'FAILED', score: '40' });
        expect(failed.json).toMatchObject({ success: true });
        await attendee.reload();
        await expect(attendee.locator('.es-badge--result-failed')).toHaveText('Failed');
      } finally {
        await cleanUpSchedule(page, scheduleId);
        await attendee.context().close();
      }
    });
  });
});
