import { Page, expect } from '@playwright/test';
import { BASE } from './base';

export const OFFICER_LOGIN = BASE + 'admission-officer/login';
export const OFFICER_DASHBOARD = BASE + 'admission-officer/dashboard';
export const OFFICER_BASE = BASE + 'admission-officer/';

export function hasOfficerCreds() {
  return !!process.env.OFFICER_EMAIL && !!process.env.OFFICER_PASSWORD;
}

export const NO_OFFICER_CREDS_REASON = 'OFFICER_EMAIL / OFFICER_PASSWORD not set (.env.e2e)';

// Signs in as the pre-provisioned admission officer (credentials come from .env.e2e via playwright.config.js).
// The officer login form submits through fetch() and then navigates to the dashboard.
export async function loginAsOfficer(page: Page) {
  await page.goto(OFFICER_LOGIN);
  await page.locator('input[name=email]').fill(process.env.OFFICER_EMAIL!);
  await page.locator('input[name=password]').fill(process.env.OFFICER_PASSWORD!);
  await page.locator('#login-btn').click();
  await expect(page).toHaveURL(OFFICER_DASHBOARD, { timeout: 15_000 });
}

// Looks up applicants through the officer list endpoint (read only). Returns the raw JSON rows.
export async function fetchApplicantRows(page: Page, query = ''): Promise<any[]> {
  const res = await page.request.get(OFFICER_BASE + 'applicants/fetch-applicants' + (query ? '?' + query : ''), {
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
  });
  const json = await res.json();
  return json.data ?? [];
}

export { BASE };
