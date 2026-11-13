// M17 System Settings  (TC-M17-01 .. TC-M17-02)
// spec: specs/admin.md
// seed: e2e/seed.spec.ts
//
// Every test here is an administrator, so the login happens once in the beforeEach hook.
import { test, expect } from './support/throttle';
import { hasAdminCreds, loginAsAdmin, NO_ADMIN_CREDS_REASON } from './support/admin';
import { BASE } from './support/base';
import { hasApplicantCreds, loginAsApplicant } from './support/applicant';
import { hasOfficerCreds, loginAsOfficer } from './support/officer';
import { acquireLock, releaseLock } from './support/exam-setting';

// exam_enabled is global and the M4 specs depend on it (baseline ON): this module takes the same lock as they do, so they never overlap. The flag is always restored in the finally block.

test.describe('System Settings', () => {
  test.beforeEach(async ({ page }) => {
    test.skip(!hasAdminCreds(), NO_ADMIN_CREDS_REASON);
    await loginAsAdmin(page);
  });

  test('System settings save and exam toggle', async ({ page, browser }) => {

    await acquireLock();
    try {
      await page.goto(BASE + 'admin/settings');
      const exam = page.locator('#toggle-exam');
      await expect(page.getByRole('button', { name: 'Save Settings' })).toBeVisible();
      const original = await exam.isChecked();
      await expect(page.locator('#toggle-exam-label')).toHaveText(original ? 'ON' : 'OFF');

      const setExam = async (enabled: boolean) => {
        await page.goto(BASE + 'admin/settings');
        if ((await exam.isChecked()) !== enabled) await page.locator('label[for="toggle-exam"]').click();
        await expect(exam).toBeChecked({ checked: enabled });
        await page.getByRole('button', { name: 'Save Settings' }).click();
        await expect(page).toHaveURL(BASE + 'admin/settings');
        await expect(page.locator('.flash-success')).toHaveText('Settings saved.');
        // Persisted
        await expect(page.locator('#toggle-exam')).toBeChecked({ checked: enabled });
        await expect(page.locator('#toggle-exam-label')).toHaveText(enabled ? 'ON' : 'OFF');
      };

      const checkPortals = async (enabled: boolean) => {
        // Officer exam page
        if (hasOfficerCreds()) {
          const ctx = await browser.newContext();
          try {
            const officerPage = await ctx.newPage();
            await loginAsOfficer(officerPage);
            await officerPage.goto(BASE + 'admission-officer/exam-schedule');
            const disabledHeading = officerPage.getByRole('heading', { name: 'Entrance Exam is Disabled' });
            if (enabled) await expect(disabledHeading).toHaveCount(0);
            else await expect(disabledHeading).toBeVisible();
          } finally {
            await ctx.close();
          }
        }
        // Applicant exam page: redirected to the dashboard only when disabled
        if (hasApplicantCreds()) {
          const ctx = await browser.newContext();
          try {
            const applicantPage = await ctx.newPage();
            await loginAsApplicant(applicantPage);
            await applicantPage.goto(BASE + 'exam-schedule');
            await expect(applicantPage).toHaveURL(BASE + (enabled ? 'exam-schedule' : 'dashboard'));
          } finally {
            await ctx.close();
          }
        }
      };

      try {
        // 1. Toggle exam_enabled, save, then check the applicant and officer exam pages
        await setExam(!original);
        await checkPortals(!original);
      } finally {
        // ...and restore
        await setExam(original);
      }
      await checkPortals(original);
    } finally {
      releaseLock();
    }
  });
});
