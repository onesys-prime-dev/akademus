// spec: specs/public-pages.md
// seed: e2e/seed.spec.ts
// Public landing pages (no module in the task catalogue). No login needed.
import { test, expect } from '@playwright/test';
import { BASE, SIGN_IN } from './support/base';

test.describe('Landing pages', () => {
  test('Home page loads at / and /homepage', async ({ page }) => {
    // 1. Navigate to / then to /homepage
    for (const path of ['', 'homepage']) {
      const response = await page.goto(BASE + path);
      expect(response?.status()).toBe(200);
      await expect(page).toHaveTitle('Akademus | Home');
      await expect(page.getByRole('heading', { level: 1, name: 'Where Smart Learning Begins and Grows' })).toBeVisible();
      const nav = page.locator('header nav');
      await expect(nav.getByRole('link', { name: 'About Us' })).toBeVisible();
      await expect(nav.getByRole('link', { name: 'Features' })).toBeVisible();
      await expect(nav.getByRole('link', { name: 'Contact' })).toBeVisible();
      await expect(page.getByRole('banner').getByRole('link', { name: 'Get Started' })).toBeVisible();

      // no broken images
      const broken = await page.evaluate(() =>
        [...document.images].filter((i) => i.complete && i.naturalWidth === 0 && i.offsetParent !== null).map((i) => i.src));
      expect(broken).toEqual([]);
    }
  });

  test('Home page has no console errors', async ({ page, browserName }) => {
    // Known app defect: an inline SVG on the home page has a malformed path "d" attribute
    // (home view line ~196). Chromium and WebKit log a console error for it; Firefox does not
    // report it, so the defect is only observable (and expected to fail) there.
    test.fail(browserName !== 'firefox', 'Home page logs "<path> attribute d: Expected number" for a malformed SVG path');
    const errors: string[] = [];
    page.on('console', (m) => { if (m.type() === 'error') errors.push(m.text()); });
    page.on('pageerror', (e) => errors.push(e.message));

    // 1. Load home and check console
    await page.goto(BASE);
    await page.waitForLoadState('load');
    expect(errors).toEqual([]);
  });

  test('Header navigation reaches every landing page', async ({ page }) => {
    await page.goto(BASE);
    const nav = page.locator('header nav');

    // 1. From home click About, Features, Contact in turn, then the logo
    const targets = [
      { link: 'About Us', url: /\/about-us$/, title: 'Akademus | About Us' },
      { link: 'Features', url: /\/features$/, title: 'Akademus | Features' },
      { link: 'Contact', url: /\/contact$/, title: 'Akademus | Contact' },
    ];
    for (const t of targets) {
      await nav.getByRole('link', { name: t.link }).click();
      await expect(page).toHaveURL(t.url);
      await expect(page).toHaveTitle(t.title);
      await expect(page.getByRole('heading').first()).not.toBeEmpty();
    }

    await page.locator('header a.header__logo').click();
    await expect(page).toHaveURL(/\/homepage$/);
    await expect(page).toHaveTitle('Akademus | Home');
  });

  test('About, Features and Contact pages render content', async ({ page }) => {
    const pages = [
      { path: 'about-us', title: 'Akademus | About Us', heading: 'Get to Know Us Better' },
      { path: 'about', title: 'Akademus | About Us', heading: 'Get to Know Us Better' },
      { path: 'features', title: 'Akademus | Features', heading: 'Akademus - Modern School ERP' },
      { path: 'contact', title: 'Akademus | Contact', heading: 'CONTACT US' },
    ];

    // 1. Visit /about-us, /about, /features, /contact
    for (const p of pages) {
      const response = await page.goto(BASE + p.path);
      expect(response?.status(), p.path).toBe(200);
      await expect(page, p.path).toHaveTitle(p.title);
      await expect(page.getByRole('heading', { name: p.heading }).first(), p.path).toBeVisible();
    }
  });

  test('Contact page has working contact info or form validation', async ({ page }) => {
    // The form sends via EmailJS from the browser; block any such request so nothing real is sent.
    const sent: string[] = [];
    // Only the send API is blocked; the EmailJS browser library itself is loaded from a CDN.
    await page.route(/api\.emailjs\.com/i, async (route) => {
      sent.push(route.request().url());
      await route.abort();
    });

    // 1. Open /contact
    await page.goto(BASE + 'contact');
    const form = page.locator('#contactForm');
    await expect(form).toBeVisible();
    await expect(page.getByRole('heading', { name: 'VISIT US' })).toBeVisible();

    // 2. Submit empty: native required validation blocks submission
    await form.locator('button[type=submit], #submitBtn').first().click();
    await expect(form.locator('input[name=first_name]')).toBeFocused();
    expect(await form.locator('input[name=first_name]').evaluate((el: HTMLInputElement) => el.validity.valueMissing)).toBe(true);

    // 3. Invalid email is rejected by the browser
    await form.locator('input[name=first_name]').fill('QA');
    await form.locator('input[name=last_name]').fill('Tester');
    await form.locator('input[name=email]').fill('abc');
    await form.locator('textarea[name=message]').fill('Test message');
    await form.locator('#submitBtn').click();
    expect(await form.locator('input[name=email]').evaluate((el: HTMLInputElement) => el.validity.typeMismatch)).toBe(true);
    await expect(form.locator('#submitBtn')).toBeEnabled();
    expect(sent).toEqual([]);
  });

  test('System pages render', async ({ page }) => {
    // 1. Visit each system page
    for (const name of ['akademus', 'mentora', 'learnix', 'sis', 'attendance', 'otis']) {
      const response = await page.goto(BASE + 'systems/' + name);
      expect(response?.status(), name).toBe(200);
      // Plan expects the system name in a heading; the live pages use the name in the
      // hero text ("Step Into SIS") and h4 feature headings, so check the visible body text.
      await expect(page.locator('body'), name).toContainText(new RegExp(name, 'i'));
      await expect(page.getByRole('heading').first(), name).toBeVisible();
    }
  });

  test('System pages have a descriptive document title', async ({ page }) => {
    // Plan discrepancy / app defect: system pages are served with the generic title "Document".
    test.fail(true, 'System pages use <title>Document</title>');

    // 1. Visit a system page
    await page.goto(BASE + 'systems/sis');
    await expect(page).not.toHaveTitle('Document');
  });

  test('Apply Now button leads to sign-up/sign-in', async ({ page }) => {
    // 1. Click the call to action on home (labelled "Get Started" in the live app)
    await page.goto(BASE);
    await page.getByRole('banner').getByRole('link', { name: 'Get Started' }).click();
    await expect(page).toHaveURL(SIGN_IN);

    // sign-in <-> sign-up links work
    // (the sign-in page links to sign-up via "Click here" under "Don't have an account?")
    await page.getByRole('link', { name: 'Click here' }).click();
    await expect(page).toHaveURL(/sign-up/);
  });

  test('/applynowpage opens without a server error', async ({ page }) => {
    // Known app defect: ApplyNow::index loads view "applynowpage.php" which does not exist -> HTTP 500.
    test.fail(true, 'GET /applynowpage returns 500 (ViewException: Invalid file "applynowpage.php")');

    // 1. Open /applynowpage directly
    const response = await page.goto(BASE + 'applynowpage');
    expect(response?.status()).toBeLessThan(400);
  });

  test('Unknown public URL shows 404', async ({ page }) => {
    // 1. Visit /systems/unknown and /no-such-page
    for (const path of ['systems/unknown', 'no-such-page']) {
      const response = await page.goto(BASE + path);
      expect(response?.status(), path).toBe(404);
    }
  });

  test('404 page does not disclose stack trace or file paths', async ({ page }) => {
    // Browser requests (Accept: text/html) get the plain error_404 view, which shows no trace or paths.
    // 1. Visit an unknown URL and inspect the body
    const response = await page.goto(BASE + 'no-such-page');
    const body = (await response?.text()) ?? '';
    expect(body).not.toMatch(/xampp|vendor[\\/]|"trace"|Stack trace/i);
  });

  test.describe('Mobile viewport', () => {
    // Set the viewport via context options: page.setViewportSize can hang on Firefox under load.
    test.use({ viewport: { width: 375, height: 667 } });

    test('Responsive layout on mobile viewport', async ({ page }) => {
      // 1. Open home at 375x667, open the menu toggle
      await page.goto(BASE);

      await expect(page.locator('header nav.header__nav')).toBeHidden();
      await page.locator('#hpHamburger').click();
      const overlay = page.locator('#hpOverlay');
      await expect(overlay.getByRole('link', { name: 'About Us' })).toBeVisible();
      await expect(overlay.getByRole('link', { name: 'Features' })).toBeVisible();
      await expect(overlay.getByRole('link', { name: 'Contact' })).toBeVisible();
      await expect(overlay.getByRole('link', { name: 'Get Started' })).toBeVisible();

      // close, then no horizontal scroll
      await page.locator('#hpOverlayClose').click();
      const overflow = await page.evaluate(() => document.documentElement.scrollWidth - document.documentElement.clientWidth);
      expect(overflow).toBeLessThanOrEqual(0);
    });
  });
});
