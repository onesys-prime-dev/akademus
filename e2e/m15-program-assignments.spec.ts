// M15 Program Assignments  (TC-M15-01 .. TC-M15-02)
// spec: specs/admin.md
// seed: e2e/seed.spec.ts
//
// Every test here is an administrator, so the login happens once in the beforeEach hook.
import { test, expect } from './support/throttle';
import { hasAdminCreds, loginAsAdmin, NO_ADMIN_CREDS_REASON } from './support/admin';
import { BASE } from './support/base';
import { createPresetApi, createProgramApi, deletePresetApi, deleteProgramApi, firstPresetChoices, presetIdByName, programIdByName } from './support/admin-config';

test.describe('Program Assignments', () => {
  test.beforeEach(async ({ page }) => {
    test.skip(!hasAdminCreds(), NO_ADMIN_CREDS_REASON);
    await loginAsAdmin(page);
  });

  test('Assign preset to program', async ({ page }) => {

    const stamp = Date.now();
    const programName = `Bachelor of Science in QA Assign ${stamp}`;
    const presetName = `QA Assign Preset ${stamp}`;
    let programId: number | null = null;
    let presetId: number | null = null;
    try {
      // Throwaway program and a non-global preset to assign
      await createProgramApi(page, programName, `QA${String(stamp).slice(-7)}`);
      programId = await programIdByName(page, programName);
      expect(programId).not.toBeNull();

      const { pageId, categoryId } = await firstPresetChoices(page);
      test.skip(!pageId, 'No enabled form page available to build a preset from');
      const created = await createPresetApi(page, { name: presetName, pageIds: [pageId!], categoryIds: categoryId ? [categoryId] : [] });
      expect([302, 303]).toContain(created.status);
      presetId = await presetIdByName(page, presetName);
      expect(presetId).not.toBeNull();

      // 1. Open /admin/programs/presets: the new program uses Global / Default
      await page.goto(BASE + 'admin/programs/presets');
      await expect(page.getByRole('heading', { name: 'Program Assignments' })).toBeVisible();
      const row = page.locator('table tbody tr').filter({ hasText: programName });
      await expect(row.locator('.badge-global')).toBeVisible();

      //    ...assign the preset to the test program
      await row.getByRole('button', { name: 'Assign Preset' }).click();
      await expect(page.locator('#modalBg')).toHaveClass(/open/);
      await expect(page.locator('#modalProgramName')).toContainText(programName);
      await page.locator('#modalPresetId').selectOption({ label: presetName });
      await page.locator('#modalBg').getByRole('button', { name: 'Save' }).click();

      // Assignment saved
      await expect(page.locator('.alert-success')).toHaveText('Preset assignment updated successfully!');
      await expect(page.locator('table tbody tr').filter({ hasText: programName }).locator('.badge-preset')).toHaveText(presetName);

      // /api/presets/program/{id} returns that preset's pages
      const api = await page.request.get(`${BASE}api/presets/program/${programId}`);
      const json = await api.json();
      expect(json.success).toBe(true);
      expect(json.pages.map((p: any) => String(p.id))).toContain(pageId);
      expect(json.pages).toHaveLength(1);
    } finally {
      if (programId) await deleteProgramApi(page, programId);
      if (presetId) await deletePresetApi(page, presetId);
    }
  });
});
