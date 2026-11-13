import { Page, expect } from '@playwright/test';
import { BASE } from './base';

export const ADMIN_LOGIN = BASE + 'admin/login';
export const ADMIN_HOME = BASE + 'admin/theme';

export function hasAdminCreds() {
  return !!process.env.ADMIN_EMAIL && !!process.env.ADMIN_PASSWORD;
}

export const NO_ADMIN_CREDS_REASON = 'ADMIN_EMAIL / ADMIN_PASSWORD not set (.env.e2e)';

// Signs in as the pre-provisioned admin (credentials come from .env.e2e via playwright.config.js).
// Observed behaviour: success redirects to /admin/theme (there is no /admin/dashboard route).
export async function loginAsAdmin(page: Page) {
  await page.goto(ADMIN_LOGIN);
  await page.locator('input[name=email]').fill(process.env.ADMIN_EMAIL!);
  await page.locator('#admin-password').fill(process.env.ADMIN_PASSWORD!);
  await page.locator('#login-btn').click();
  await expect(page).toHaveURL(ADMIN_HOME, { timeout: 15_000 });
}

// Admin pages and their sidebar link targets (all of these exist as GET routes).
export const ADMIN_PAGES = [
  'admin/users',
  'admin/theme',
  'admin/programs',
  'admin/programs/presets',
  'admin/presets',
  'admin/form-fields',
  'admin/requirement-categories',
  'admin/fast-track-requirements',
  'admin/payment-fees',
  'admin/settings',
];

// Reads the public theme JSON (GET /admin/theme/json needs no session).
export async function readTheme(page: Page): Promise<Record<string, string>> {
  const res = await page.request.get(BASE + 'admin/theme/json');
  return res.json();
}

export { BASE };
