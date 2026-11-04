// M11 Form Fields  (TC-M11-01 .. TC-M11-04)
// spec: specs/admin.md
// seed: e2e/seed.spec.ts
//
// Every test here is an administrator, so the login happens once in the beforeEach hook.
import { test, expect } from './support/throttle';
import { hasAdminCreds, loginAsAdmin, NO_ADMIN_CREDS_REASON } from './support/admin';
import { BASE } from './support/base';
import { sessionPostForm, sessionPostJson } from './support/session-http';

test.describe('Form Fields', () => {
  test.beforeEach(async ({ page }) => {
    test.skip(!hasAdminCreds(), NO_ADMIN_CREDS_REASON);
    await loginAsAdmin(page);
  });

  test('Form fields: add, reorder, toggle, rename and delete page', async ({ page }) => {
    await page.goto(BASE + 'admin/form-fields');

    const stamp = Date.now();
    const title = `QA Page ${stamp}`;
    const renamed = `QA Page Renamed ${stamp}`;
    const toast = page.locator('#ff-toast');
    const item = (t: string) => page.locator('.page-item').filter({ hasText: t });
    let pageId: string | null = null;
    let deleted = false;
    try {
      // 1a. Create page with an empty title: blocked in the dialog; the server says 'Title is required'
      await page.locator('.pages-add-btn').click();
      await expect(page.locator('#page-modal')).toHaveClass(/open/);
      await page.locator('#page-modal-submit').click();
      await expect(toast).toHaveText('Please enter a page title.');
      const emptyTitle = await sessionPostJson(page, 'admin/form-fields/pages', { title: '   ', description: '' });
      expect(emptyTitle.json).toMatchObject({ status: 'error', message: 'Title is required' });

      // 1b. Create 'QA Page <ts>'
      await page.locator('#page-modal-name').fill(title);
      await page.locator('#page-modal-desc').fill('QA throwaway page');
      await page.locator('#page-modal-submit').click();
      await expect(toast).toContainText(`Page "${title}" created!`);
      await expect(item(title)).toHaveCount(1);
      pageId = await item(title).getAttribute('data-page-id');
      expect(pageId).toBeTruthy();

      // APP BUG (not asserted): the item injected into the sidebar right after creation builds its Rename button with
      // onclick="ffEditPageModal(id, ${JSON.stringify(title)}, ...)" inside a double-quoted attribute, so the quotes
      // break the handler and the Rename button does nothing until the page is reloaded. Reload to get the
      // server-rendered (correctly escaped) buttons.
      await page.reload();
      await expect(item(title)).toHaveCount(1);

      // 1c. Rename
      await item(title).locator('.pi-btn').nth(0).click();
      await expect(page.locator('#page-modal-title')).toHaveText('Rename Page');
      await page.locator('#page-modal-name').fill(renamed);
      await page.locator('#page-modal-submit').click();
      await expect(toast).toHaveText('Page renamed.');
      await expect(item(renamed)).toHaveCount(1);

      // 1d. Toggle off and on again
      await item(renamed).locator('.pi-btn').nth(1).click();
      await expect(toast).toHaveText('Page disabled. Applicants will skip it.');
      await expect(item(renamed)).toHaveClass(/disabled-page/);
      await item(renamed).locator('.pi-btn').nth(1).click();
      await expect(toast).toHaveText('Page enabled.');
      await expect(item(renamed)).not.toHaveClass(/disabled-page/);

      // 1e. Reorder (only the test page's sort_order is touched)
      const reorder = await sessionPostJson(page, 'admin/form-fields/pages/reorder', { order: [{ id: Number(pageId), sort_order: 9999 }] });
      expect(reorder.json).toMatchObject({ status: 'success' });
      const none = await sessionPostJson(page, 'admin/form-fields/pages/reorder', { order: [] });
      expect(none.json).toMatchObject({ status: 'error', message: 'No order received' });
      await page.reload();
      const ids = await page.locator('.page-item').evaluateAll((els) => els.map((e: any) => e.dataset.pageId));
      expect(ids[ids.length - 1]).toBe(pageId);

      // 1f. Delete (confirmed through the browser dialog)
      page.once('dialog', (d) => d.accept());
      await item(renamed).locator('.pi-btn.danger').click();
      await expect(page.locator('#ff-toast')).toHaveText('Page deleted.');
      deleted = true;
      await expect(item(renamed)).toHaveCount(0);

      // Unknown pages are reported, not silently accepted
      const missing = await sessionPostJson(page, 'admin/form-fields/pages/999999999/rename', { title: 'x' });
      expect(missing.json).toMatchObject({ status: 'error', message: 'Page not found' });

      // Reflection in the applicant form is not asserted here: it needs a signed-in applicant and an enabled page
      // inside the active preset; a freshly created page belongs to no preset.
    } finally {
      if (pageId && !deleted) await sessionPostForm(page, `admin/form-fields/pages/${pageId}/delete`, {});
    }
  });

  test('Form fields: save and reset', async ({ page }) => {
    await page.goto(BASE + 'admin/form-fields');
    await expect(page.locator('.page-item').first()).toBeVisible();

    // 1. Post an empty payload (no fields): 'No fields received', nothing is written
    for (const body of [{ fields: [] }, {}]) {
      const res = await sessionPostJson(page, 'admin/form-fields/save', body);
      expect(res.json, JSON.stringify(body)).toMatchObject({ status: 'error', message: 'No fields received' });
    }

    // page-data for an unknown page is reported as an error (no HTML, no PHP error)
    const missing = await page.request.get(`${BASE}admin/form-fields/page-data?page_id=999999999`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    expect(await missing.json()).toMatchObject({ status: 'error', message: 'Page not found' });
  });

  test('Change a field label / required flag, save, then reset to defaults', async () => {
    test.fixme(true, 'Not safe on the shared test DB: ffSaveAll() rewrites every field of the selected page from the DOM text (placeholders shown as an em dash are written back as empty), and POST /admin/form-fields/reset deletes all custom fields and restores defaults, which cannot be undone. Needs a dedicated, restorable form-field fixture or a DB snapshot.');
  });
});
