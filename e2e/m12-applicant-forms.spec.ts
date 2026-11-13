// M12 Applicant Forms  (TC-M12-01 .. TC-M12-04)
// spec: specs/admin.md
// seed: e2e/seed.spec.ts
//
// Every test here is an administrator, so the login happens once in the beforeEach hook.
import { test, expect } from './support/throttle';
import { hasAdminCreds, loginAsAdmin, NO_ADMIN_CREDS_REASON } from './support/admin';
import { BASE } from './support/base';
import { deletePresetApi, presetIdByName } from './support/admin-config';

test.describe('Applicant Forms (presets)', () => {
  test.beforeEach(async ({ page }) => {
    test.skip(!hasAdminCreds(), NO_ADMIN_CREDS_REASON);
    await loginAsAdmin(page);
  });

  test('Form presets CRUD', async ({ page }) => {

    const stamp = Date.now();
    const name = `QA Preset ${stamp}`;
    const renamed = `QA Preset Renamed ${stamp}`;
    let id: number | null = null;
    try {
      // 1a. Create a (non-global) preset
      await page.goto(BASE + 'admin/presets/create');
      const firstSection = page.locator('input[name="form_pages[]"]').first();
      test.skip((await firstSection.count()) === 0, 'No enabled form pages exist to build a preset from');
      await page.locator('#name').fill(name);
      await page.locator('#description').fill('QA throwaway preset');
      await firstSection.check();
      const category = page.locator('input[name="requirement_categories[]"]').first();
      if (await category.count()) await category.check();
      await page.getByRole('button', { name: 'Create Form' }).click();
      await expect(page).toHaveURL(BASE + 'admin/presets');
      await expect(page.getByText('Preset created successfully!')).toBeVisible();
      const card = () => page.locator('.preset-card').filter({ hasText: name });
      await expect(card()).toHaveCount(1);
      await expect(card()).toContainText('Course-Specific');
      id = await presetIdByName(page, name);
      expect(id).not.toBeNull();

      // 1b. Edit
      await card().getByRole('link', { name: /Edit/ }).click();
      await expect(page.getByRole('heading', { name: 'Edit Applicant Form' })).toBeVisible();
      await page.locator('#name').fill(renamed);
      await page.getByRole('button', { name: 'Update Form' }).click();
      await expect(page).toHaveURL(BASE + 'admin/presets');
      await expect(page.getByText('Preset updated successfully!')).toBeVisible();
      await expect(page.locator('.preset-card').filter({ hasText: renamed })).toHaveCount(1);
      await expect(page.locator('.preset-card').filter({ hasText: name })).toHaveCount(0);

      // 1c. Deactivate (there is no button for it: it is the GET /admin/presets/{id}/deactivate route)
      await page.goto(`${BASE}admin/presets/${id}/deactivate`);
      await expect(page).toHaveURL(BASE + 'admin/presets');
      await expect(page.getByText('Preset deactivated successfully!')).toBeVisible();
      await expect(page.locator('.preset-card').filter({ hasText: renamed })).toHaveCount(0); // list shows active presets only

      // 1d. Delete
      await page.goto(`${BASE}admin/presets/${id}/delete`);
      await expect(page).toHaveURL(BASE + 'admin/presets');
      await expect(page.getByText('Preset deleted successfully!')).toBeVisible();
      id = null;
    } finally {
      // Self-cleaning if an earlier step failed (active presets are findable by name)
      const left = id ?? (await presetIdByName(page, renamed)) ?? (await presetIdByName(page, name));
      if (left) await deletePresetApi(page, left);
    }
  });

  test('Only one global preset allowed', async ({ page }) => {

    // The test needs a global preset that is already active. Creating one here would change which form every
    // program without its own preset serves to applicants, so it is skipped when none exists.
    await page.goto(BASE + 'admin/presets');
    test.skip((await page.locator('.preset-badge.global').count()) === 0, 'No active global preset in the test DB; creating one would alter the applicant form for every program');

    const name = `QA Second Global ${Date.now()}`;
    try {
      // 1. With a global preset active create a second global preset
      await page.goto(BASE + 'admin/presets/create');
      const firstSection = page.locator('input[name="form_pages[]"]').first();
      test.skip((await firstSection.count()) === 0, 'No enabled form pages exist to build a preset from');
      await page.locator('#name').fill(name);
      await firstSection.check();
      await page.locator('#is_global').check();
      await page.getByRole('button', { name: 'Create Form' }).click();

      // Redirected back to the form with the flash error; nothing was created
      await expect(page).toHaveURL(BASE + 'admin/presets/create');
      await expect(page.getByText('A global preset already exists. Deactivate it first.')).toBeVisible();
      await page.goto(BASE + 'admin/presets');
      await expect(page.locator('.preset-card').filter({ hasText: name })).toHaveCount(0);
    } finally {
      const id = await presetIdByName(page, name);
      if (id) await deletePresetApi(page, id);
    }
  });
});
