// M4 Exam Schedule  (TC-M4-01 .. TC-M4-07)
// spec: specs/admission-officer.md, specs/applicant-dashboard-and-form.md
// seed: e2e/seed.spec.ts
//
// Each describe logs in once in a beforeEach hook instead of repeating the login in every test.
import { test, expect } from './support/throttle';
import { BASE, hasApplicantCreds, loginAsApplicant, NO_CREDS_REASON, pagePost } from './support/applicant';
import { OFFICER_BASE, hasOfficerCreds, loginAsOfficer, NO_OFFICER_CREDS_REASON } from './support/officer';
import { hasAdminCreds } from './support/admin';
import { acquireLock, releaseLock, setExamEnabled, withExamEnabled } from './support/exam-setting';
import { EXAM_URL, createScheduleApi, dateAhead, deleteScheduleApi, showScheduleApi } from './support/exam';
import { sessionGet, sessionPostForm } from './support/session-http';

// exam_enabled is one global flag that these tests flip and read, and the schedules share calendar days,
// so the whole module runs in one worker, in order.
test.describe.configure({ mode: 'serial' });

const MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

// ---------------------------------------------------------------------------------------------
// Applicant side: TC-M4-03 .. TC-M4-06
// ---------------------------------------------------------------------------------------------
test.describe('Applicant exam schedule', () => {
  test.beforeEach(async ({ page }) => {
    test.skip(!hasApplicantCreds(), NO_CREDS_REASON);
    await loginAsApplicant(page);
  });

  test('Book and cancel an exam slot', async () => {
    test.fixme(true, 'Needs an open slot (the seeder adds "E2E Seed Exam Hall") and a disposable eligible applicant; note the sheet marks M4-03 to M4-05 as not working because /exam-schedule is gated on approval.');

    // 1. Book a slot, then cancel
    // Expected: booking shown, slot count decremented; cancellation frees the slot
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

  test('Exam schedule redirects to dashboard when exam disabled', async ({ page, browser }) => {
    const check = async () => {
      // 1. With exam_enabled=0 visit /exam-schedule (the test switches it OFF first and restores it afterwards)
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

  test('Delete a schedule that has bookings', async () => {
    // Observed in code: ExamScheduleService::deleteSchedule() deletes the schedule row only, leaving its
    // exam_bookings orphaned (no block, no cancellation of the bookings).
    test.fixme(true, 'Needs a schedule with a booking, which requires an applicant booking through the applicant exam flow (exam must be enabled and a disposable applicant provisioned).');
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

  test('Check in and cancel booking', async () => {
    // 1. In schedule details check in an applicant and cancel another's booking
    test.fixme(true, 'Check-in and cancel act on real exam_bookings rows. Creating a booking requires a disposable applicant to book through the applicant exam flow (exam enabled, slot created by an officer); none is provisioned, and the applicant exam booking spec is itself fixme. Needs seeded throwaway applicants with bookings.');
    // Expected: attendance marked (status attended + checked_in_at); cancelled slot freed (booked_slots decremented)
  });

  test('Record exam result', async ({ page }) => {
    // Safe checks: an unknown / empty result status is refused before anything is written
    for (const result_status of ['', 'MAYBE', 'passed!']) {
      const res = await sessionPostForm(page, 'admission-officer/exam-bookings/999999999/result', { result_status, score: '80' });
      expect(res.json, JSON.stringify(result_status)).toMatchObject({ success: false, message: 'Result status is required.' });
    }

    // 1. Enter score pass/fail via the exam tab; try non-numeric and out-of-range score
    test.fixme(true, 'Saving a result rewrites a real exam_bookings row and feeds the dashboard passing rate; no disposable booking exists (see "Check in and cancel booking"). Also note the server does not validate the score: a non-numeric value is cast to 0 and out-of-range values are stored; only the browser input enforces min 0 / max 100.');
  });
});
