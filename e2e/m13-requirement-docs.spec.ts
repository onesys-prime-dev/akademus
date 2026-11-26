// M13 Requirement Docs  (TC-M13-01 .. TC-M13-04)
// spec: specs/admin.md
// seed: e2e/seed.spec.ts
//
// Every test here is an administrator, so the login happens once in the beforeEach hook.
import { test, expect } from './support/throttle';
import { hasAdminCreds, loginAsAdmin, NO_ADMIN_CREDS_REASON } from './support/admin';
import { BASE } from './support/base';
import { categoryIdByName, deleteCategoryApi } from './support/admin-config';

test.describe('Requirement categories', () => {
  test.beforeEach(async ({ page }) => {
    test.skip(!hasAdminCreds(), NO_ADMIN_CREDS_REASON);
    await loginAsAdmin(page);
  });

  test('Requirement categories CRUD', async ({ page }) => {

    const stamp = Date.now();
    const name = `QA Category ${stamp}`;
    const renamed = `QA Category Renamed ${stamp}`;
    const row = (n: string) => page.locator('table tbody tr').filter({ hasText: n });
    try {
      // 1a. Submit an empty name (browser check bypassed): rejected, nothing is created
      await page.goto(BASE + 'admin/requirement-categories');
      const rowsBefore = await page.locator('table tbody tr').count();
      await page.goto(BASE + 'admin/requirement-categories/create');
      await page.getByRole('button', { name: 'Create Category' }).click();
      await expect(page).toHaveURL(BASE + 'admin/requirement-categories/create'); // required blocks it
      await page.locator('#category_name').evaluate((el) => el.removeAttribute('required'));
      await page.getByRole('button', { name: 'Create Category' }).click();
      await expect(page).toHaveURL(BASE + 'admin/requirement-categories/store');
      await expect(page.getByText(/category_name field is required/i)).toBeVisible();
      await page.goto(BASE + 'admin/requirement-categories');
      await expect(page.locator('table tbody tr')).toHaveCount(rowsBefore);

      // 1b. Create a test category (no documents, so it affects no applicant checklist)
      await page.goto(BASE + 'admin/requirement-categories/create');
      await page.locator('#category_name').fill(name);
      await page.locator('#description').fill('QA throwaway category');
      await page.getByRole('button', { name: 'Create Category' }).click();
      await expect(page).toHaveURL(BASE + 'admin/requirement-categories');
      await expect(page.locator('.alert-success')).toHaveText('Requirement category created successfully!');
      await expect(row(name)).toHaveCount(1);
      await expect(row(name)).toContainText('0 docs');

      // 1c. Edit
      await row(name).getByRole('link', { name: 'Edit' }).click();
      await page.locator('#category_name').fill(renamed);
      await page.getByRole('button', { name: 'Update Category' }).click();
      await expect(page.locator('.alert-success')).toHaveText('Category updated successfully!');
      await expect(row(renamed)).toHaveCount(1);
      await expect(row(name)).toHaveCount(0);

      // 1d. Delete (confirm dialog)
      page.once('dialog', (d) => d.accept());
      await row(renamed).getByRole('link', { name: 'Delete' }).click();
      await expect(page.locator('.alert-success')).toHaveText('Category deleted successfully!');
      await expect(row(renamed)).toHaveCount(0);
    } finally {
      const id = (await categoryIdByName(page, renamed)) ?? (await categoryIdByName(page, name));
      if (id) await deleteCategoryApi(page, id);
    }
  });
});
