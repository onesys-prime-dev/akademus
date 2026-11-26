// M2 Dashboard  (TC-M2-01 .. TC-M2-04)
// spec: specs/applicant-dashboard-and-form.md, specs/admission-officer.md
// seed: e2e/seed.spec.ts
//
// Each describe logs in once in a beforeEach hook instead of repeating the login in every test.
import { test, expect } from './support/throttle';
import { postForm } from './support/csrf';
import { BASE, hasApplicantCreds, loginAsApplicant, NO_CREDS_REASON } from './support/applicant';
import { hasOfficerCreds, loginAsOfficer, NO_OFFICER_CREDS_REASON } from './support/officer';

// ---------------------------------------------------------------------------------------------
// TC-M2-01  Applicant dashboard
// ---------------------------------------------------------------------------------------------
test.describe('Applicant dashboard', () => {
  test.beforeEach(async ({ page }) => {
    test.skip(!hasApplicantCreds(), NO_CREDS_REASON);
    await loginAsApplicant(page);
  });

  test('Dashboard renders for a signed-in applicant', async ({ page }) => {
    // 1. Sign in with E2E credentials (hook)
    await expect(page).toHaveURL(BASE + 'dashboard');
    await expect(page).toHaveTitle('Akademus | Dashboard');

    const main = page.getByRole('main');
    await expect(main.getByRole('heading', { name: /^Hello, / })).toBeVisible();
    await expect(main.getByRole('heading', { name: 'Overall Application Progress' })).toBeVisible();
    await expect(main.getByText(/^\d+%$/).first()).toBeVisible();
    await expect(main.getByRole('heading', { name: 'Applicant Form' })).toBeVisible();
    await expect(main.getByRole('heading', { name: 'Documents' })).toBeVisible();
    await expect(main.getByRole('heading', { name: 'Payments' })).toBeVisible();

    const sidebar = page.getByRole('navigation').first();
    for (const name of ['Dashboard', 'Applicant Form', 'Requirements', 'Tracking Progress', 'Admission Processing Fee', 'FAQ & Chat Support', 'Follow-Up Messages']) {
      await expect(sidebar.getByRole('link', { name }), name).toBeVisible();
    }
  });

  test('Dashboard checklist links navigate correctly', async ({ page }) => {
    const main = page.getByRole('main');

    // 1. Click each checklist item/button
    const targets: Array<[string, string]> = [
      ['Continue', 'applicant-form'],
      ['Upload', 'requirements'],
      ['View', 'admission-processing-fee'],
    ];
    for (const [label, path] of targets) {
      await page.goto(BASE + 'dashboard');
      await main.getByRole('link', { name: label, exact: true }).click();
      await expect(page, label).toHaveURL(new RegExp(`/${path}(/step/\\d+)?$`));
    }
  });

  test('Dashboard banner reflects application state', async ({ page }) => {
    // 1. The shared E2E applicant's state changes as other specs save drafts, so the exact copy is
    // state dependent. Fresh state shows the prompt below; once step data exists the form-progress
    // counter on the Applicant Form card must agree with the banner.
    const main = page.getByRole('main');
    const banner = main.getByRole('heading', { name: /^Hello, / }).locator('xpath=following::p[1]');
    await expect(banner).toBeVisible();
    const text = (await banner.innerText()).trim();
    expect(text.length).toBeGreaterThan(10);
    const completed = await main.getByText(/^\d+ of \d+ completed$/).first().innerText();
    if (completed.startsWith('0 of')) {
      expect(text).toBe('Welcome! Start your application by filling out the applicant form below.');
    }
  });

  test('Banner variants for fresh and form-complete applicants', async () => {
    test.fixme(true, 'Needs two throwaway applicants (one fresh, one with a completed form) to compare banner copy.');
  });

  test('Sidebar pages: FAQ, calculator, tracking progress, follow-up', async ({ page }) => {
    // 1. Open each page
    const pages = [
      { path: 'faq', title: 'Akademus | FAQ & Chat Support', heading: 'FAQ & Chat Support' },
      { path: 'tracking-progress', title: 'Akademus | Track Submission Progress', heading: 'Track Submission Progress' },
      { path: 'follow-up-messages', title: 'Akademus | Follow-Up Messages', heading: 'Follow-Up Messages' },
    ];
    for (const p of pages) {
      const response = await page.goto(BASE + p.path);
      expect(response?.status(), p.path).toBe(200);
      await expect(page, p.path).toHaveTitle(p.title);
      await expect(page.getByRole('main').getByRole('heading', { level: 1, name: p.heading }), p.path).toBeVisible();
    }

    // The calculator is gated by the tuition_calculator_enabled setting; when disabled it redirects.
    await page.goto(BASE + 'tuition-fee-calculator');
    await expect(page).toHaveURL(/\/(tuition-fee-calculator|dashboard)$/);

    // FAQ items expand and collapse (one open at a time)
    await page.goto(BASE + 'faq');
    const items = page.locator('.faq-item');
    const first = items.nth(0);
    const second = items.nth(1);
    await expect(first).not.toHaveClass(/open/);
    await first.locator('xpath=./*[1]').click();
    await expect(first).toHaveClass(/open/);
    await second.locator('xpath=./*[1]').click();
    await expect(second).toHaveClass(/open/);
    await expect(first).not.toHaveClass(/open/);
    await second.locator('xpath=./*[1]').click();
    await expect(second).not.toHaveClass(/open/);
  });

  test('AI chat widget opens and handles empty message', async ({ page }) => {
    // Never reach Groq. The FAQ chat posts to /api/groq/chat (the plan named /api/groq/applicant).
    const groqCalls: string[] = [];
    await page.route('**/api/groq/**', async (route) => {
      groqCalls.push(route.request().url());
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({ choices: [{ message: { content: 'Mocked admissions reply.' } }] }),
      });
    });

    // 1. Open the chat on the FAQ & Chat Support page
    await page.goto(BASE + 'faq');
    const input = page.locator('#chat-input');
    await expect(input).toBeVisible();
    await expect(page.getByText(/Hi! I'm the Akademus Portal AI Assistant/)).toBeVisible();

    // 2. Empty message is not sent
    await page.locator('#chat-send-btn').click();
    await expect(page.locator('#chatMessages .msg.user')).toHaveCount(0);
    expect(groqCalls).toEqual([]);

    // 3. Non-empty message gets the mocked reply
    await input.fill('What are the requirements?');
    await page.locator('#chat-send-btn').click();
    await expect(page.locator('#chatMessages .msg.user .msg-bubble')).toHaveText('What are the requirements?');
    await expect(page.locator('#chatMessages .msg.bot .msg-bubble').last()).toHaveText('Mocked admissions reply.');
    expect(groqCalls).toHaveLength(1);
  });
});

// The onboarding tour needs the tour modal left open, so it logs in with dismissTour: false.
test.describe('Applicant onboarding tour', () => {
  test('Onboarding tour can be dismissed and stays dismissed', async ({ page }) => {
    test.skip(!hasApplicantCreds(), NO_CREDS_REASON);
    await loginAsApplicant(page, { dismissTour: false });

    // 1. On first login close/skip the tour. has_seen_tour is persisted, so on later runs the tour is
    // already gone and only the "stays dismissed" half can be checked.
    const welcome = page.getByRole('heading', { name: 'Welcome to Akademus!' });
    const shown = await welcome.waitFor({ state: 'visible', timeout: 3000 }).then(() => true, () => false);
    if (shown) {
      const [dismiss] = await Promise.all([
        page.waitForResponse((r) => r.url().includes('/dashboard/tour/dismiss')),
        page.getByRole('button', { name: 'Skip tour' }).click(),
      ]);
      expect(dismiss.status()).toBe(200);
      expect(await dismiss.json()).toEqual({ success: true });
      await expect(welcome).toBeHidden();
    }

    // 2. Reload: tour does not reappear
    await page.goto(BASE + 'dashboard');
    await expect(page.getByRole('heading', { name: /^Hello, / })).toBeVisible();
    await expect(welcome).toBeHidden();
  });

  test('Tour dismiss endpoint requires authentication', async ({ request }) => {
    // 1. POST /dashboard/tour/dismiss without session
    const res = await postForm(request, 'dashboard/tour/dismiss');
    expect(res.status()).toBe(401);
    expect(await res.json()).toEqual({ success: false });
  });
});

// ---------------------------------------------------------------------------------------------
// TC-M2-02 .. TC-M2-04  Officer dashboard
// ---------------------------------------------------------------------------------------------
const MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

test.describe('Officer dashboard', () => {
  test.beforeEach(async ({ page }) => {
    test.skip(!hasOfficerCreds(), NO_OFFICER_CREDS_REASON);
    await loginAsOfficer(page);
  });

  test('Dashboard widgets render', async ({ page }) => {
    // The hook has already logged in; collect errors from a fresh load of the dashboard.
    const errors: string[] = [];
    page.on('pageerror', (e) => errors.push(e.message));
    page.on('console', (m) => {
      // External fonts/CDN assets may be unreachable in the test environment; only script errors matter.
      if (m.type() === 'error' && !/Failed to load resource/i.test(m.text())) errors.push(m.text());
    });
    await page.reload();

    // 1. Open dashboard: key metrics
    for (const label of ['Total Applicants', 'Pending Review', 'Submitted Applications', 'Approved (Complete)']) {
      await expect(page.locator('.metric__details--title', { hasText: label })).toBeVisible();
    }
    await expect(page.locator('.metric__details--value')).toHaveCount(4);

    // applicants per program, per status, passing rate, to-do list
    await expect(page.locator('.applicants-per-program__body')).toBeVisible();
    await expect(page.locator('.applicants-per-status__body')).toBeVisible();
    await expect(page.locator('.passing-rate__body')).toBeVisible();
    // (scoped to the task list: the CI debug toolbar also dumps the task strings into the page)
    const tasks = page.locator('.to-do-list__details--task');
    await expect(tasks.filter({ hasText: 'Review Pending Applications' })).toBeVisible();
    await expect(tasks.filter({ hasText: 'Respond to Follow-up Messages' })).toBeVisible();
    await expect(tasks.filter({ hasText: 'Fill Open Exam Slots' })).toBeVisible();

    // calendar and upcoming exam schedules
    await expect(page.locator('#calendar')).toBeVisible();
    await expect(page.getByText('Upcoming Exam Schedules', { exact: true })).toBeVisible();

    // overview table
    await expect(page.getByText('Recent Applicants', { exact: true })).toBeVisible();
    await expect(page.locator('#recent-applicants-table-body')).toBeVisible();
    await expect(page.getByRole('button', { name: 'Review Applicants' })).toBeVisible();

    expect(errors).toEqual([]);
  });

  test('Academic year and semester filters', async ({ page }) => {
    // Plan discrepancy: the academic-year and semester dropdowns are commented out in
    // dashboard/components/top-controls.php. The only dashboard filter is the program dropdown,
    // so that is what is exercised here.
    await expect(page.locator('.top-controls select')).toHaveCount(1);
    const dropdown = page.locator('select[data-program]');
    await expect(dropdown).toBeVisible();

    const options = await dropdown.locator('option').evaluateAll((os) => os.map((o: any) => o.value));
    test.skip(options.filter(Boolean).length === 0, 'No programs in the test DB to filter by');
    const programId = options.filter(Boolean)[0];

    // 1. Change the dropdown: /dashboard/data is refetched and the figures update
    const [response] = await Promise.all([
      page.waitForResponse((r) => r.url().includes('/admission-officer/dashboard/data') && r.url().includes('program=' + programId)),
      dropdown.selectOption(programId),
    ]);
    expect(response.ok()).toBe(true);
    const data = await response.json();
    expect(data).toHaveProperty('statusCounts');
    await expect(page.locator('.metric__details--value').first()).toHaveText(
      new Intl.NumberFormat('en-US').format(data.statusCounts.all || 0),
    );

    // Back to all programs
    const [all] = await Promise.all([
      page.waitForResponse((r) => r.url().includes('/admission-officer/dashboard/data') && !r.url().includes('program=')),
      dropdown.selectOption(''),
    ]);
    expect(all.ok()).toBe(true);
  });

  test('Dashboard search', async ({ page }) => {
    let dialogSeen = false;
    page.on('dialog', async (d) => { dialogSeen = true; await d.dismiss(); });

    const input = page.locator('input[data-search]');
    const dropdown = page.locator('.search-dropdown');

    // 1a. Type an applicant name taken from the recent applicants table
    const firstRow = page.locator('#recent-applicants-table-body tr[data-row]').first();
    if (await firstRow.count()) {
      const name = (await firstRow.locator('td').first().innerText()).trim();
      const term = name.split(/\s+/)[0].slice(0, Math.max(2, Math.min(name.length, 6)));
      const [res] = await Promise.all([
        page.waitForResponse((r) => r.url().includes('/dashboard/search')),
        input.fill(term),
      ]);
      expect(res.ok()).toBe(true);
      await expect(dropdown).toHaveClass(/search-dropdown--open/);
      await expect(dropdown.locator('.search-dropdown__item').first()).toBeVisible();
    }

    // 1b. Nonsense string: empty state
    await input.fill('');
    const nonsense = `zzqq${Date.now()}`;
    await Promise.all([
      page.waitForResponse((r) => r.url().includes('/dashboard/search')),
      input.fill(nonsense),
    ]);
    await expect(dropdown.locator('.search-dropdown__empty')).toContainText('No results for');

    // 1c. Clearing hides the dropdown
    await input.fill('');
    await expect(dropdown).not.toHaveClass(/search-dropdown--open/);

    // SQL / HTML characters are handled safely: request succeeds, nothing executes
    for (const evil of ["' OR 1=1 --", '<img src=x onerror=alert(1)>', '%_\\']) {
      const [res] = await Promise.all([
        page.waitForResponse((r) => r.url().includes('/dashboard/search')),
        input.fill(evil),
      ]);
      expect(res.status(), evil).toBe(200);
      await expect(dropdown).toHaveClass(/search-dropdown--open/);
      await input.fill('');
    }
    expect(dialogSeen).toBe(false);
  });

  test('Calendar shows exam schedules', async ({ page }) => {
    const calendar = page.locator('#calendar');
    const month = Number(await calendar.getAttribute('data-month'));
    const year = Number(await calendar.getAttribute('data-year'));
    await expect(page.locator('.calendar__current--month')).toHaveText(MONTHS[month - 1]);

    // The server-rendered grid flags the days of this month that have exams
    const daysWithExams = await page.locator('.calendar__day--has-exam').count();

    // 1. Navigate to the next month: calendar-data loads for that month
    const nextMonth = month === 12 ? 1 : month + 1;
    const nextYear = month === 12 ? year + 1 : year;
    const [res] = await Promise.all([
      page.waitForResponse((r) => r.url().includes('/dashboard/calendar-data')),
      page.locator('.calendar__months--next').click(),
    ]);
    expect(res.url()).toContain(`month=${nextMonth}`);
    expect(res.url()).toContain(`year=${nextYear}`);
    const body = await res.json();
    expect(body.header).toContain(MONTHS[nextMonth - 1]);
    await expect(page.locator('.calendar__current--month')).toHaveText(MONTHS[nextMonth - 1]);
    await expect(page.locator('.calendar__current--year')).toHaveText(String(nextYear));
    await expect(page.locator('.calendar__day--label')).toHaveCount(7);

    // ...and back again: the flagged days of the original month are restored from the server
    await Promise.all([
      page.waitForResponse((r) => r.url().includes('/dashboard/calendar-data')),
      page.locator('.calendar__months--prev').click(),
    ]);
    await expect(page.locator('.calendar__current--month')).toHaveText(MONTHS[month - 1]);
    await expect(page.locator('.calendar__day--has-exam')).toHaveCount(daysWithExams);
  });
});
