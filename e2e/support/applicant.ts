import { Page, expect } from '@playwright/test';
import { gotoAuthPage } from './auth-page';
import { BASE, SIGN_IN } from './base';

export function hasApplicantCreds() {
  return !!process.env.E2E_EMAIL && !!process.env.E2E_PASSWORD;
}

export const NO_CREDS_REASON = 'E2E_EMAIL / E2E_PASSWORD not set (.env.e2e)';

// Signs in as the pre-provisioned E2E applicant (credentials come from .env.e2e via playwright.config.js).
// By default also skips the first-login onboarding tour, whose modal would otherwise cover the dashboard.
export async function loginAsApplicant(page: Page, opts: { dismissTour?: boolean } = {}) {
  const { dismissTour = true } = opts;
  await gotoAuthPage(page, SIGN_IN);
  await page.locator('input[name=email]').fill(process.env.E2E_EMAIL!);
  await page.locator('#password').fill(process.env.E2E_PASSWORD!);
  await page.locator('#sign-in-button').click();
  await expect(page).toHaveURL(/\/(dashboard|applicant-form)/, { timeout: 15_000 });
  if (dismissTour) await skipTourIfShown(page);
}

export async function skipTourIfShown(page: Page) {
  const skip = page.getByRole('button', { name: 'Skip tour' });
  try {
    await skip.waitFor({ state: 'visible', timeout: 3000 });
  } catch {
    return;
  }
  await Promise.all([
    page.waitForResponse((r) => r.url().includes('/dashboard/tour/dismiss')),
    skip.click(),
  ]);
}

// POST from inside the signed-in page so the app's own fetch wrapper adds the X-CSRF-TOKEN header.
export async function pagePost(page: Page, path: string, fields: Record<string, string> = {}) {
  return page.evaluate(
    async ({ url, fields }) => {
      const body = new FormData();
      for (const [k, v] of Object.entries(fields)) body.append(k, v);
      const res = await fetch(url, { method: 'POST', body, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      const text = await res.text();
      let json: any = null;
      try { json = JSON.parse(text); } catch { /* not JSON */ }
      return { status: res.status, text, json, url: res.url };
    },
    { url: BASE + path.replace(/^\//, ''), fields },
  );
}

export { BASE, SIGN_IN };
