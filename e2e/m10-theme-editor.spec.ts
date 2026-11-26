// M10 Theme Editor  (TC-M10-01 .. TC-M10-03)
// spec: specs/admin.md
// seed: e2e/seed.spec.ts
//
// Every test here is an administrator, so the login happens once in the beforeEach hook.
import { test, expect } from './support/throttle';
import { hasAdminCreds, loginAsAdmin, NO_ADMIN_CREDS_REASON, readTheme } from './support/admin';
import { BASE } from './support/base';
import { sessionPostForm, sessionPostMultipart } from './support/session-http';

// 1x1 transparent PNG
const PNG = Buffer.from('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==', 'base64');
const LOGO_ERROR = 'Logo must be a JPEG, PNG, GIF, or WebP image under 2 MB.';

// The theme is global: it is snapshotted first and every saved key is put back in the finally block.
// A valid logo upload leaves one orphan PNG in public/assets/images/logos (upload-logo only stores the file; the
// theme still points at the old logo until it is saved), which this spec cannot remove.

test.describe('Theme Editor (admin landing page)', () => {
  test.beforeEach(async ({ page }) => {
    test.skip(!hasAdminCreds(), NO_ADMIN_CREDS_REASON);
    await loginAsAdmin(page);
  });

  test('Theme editor save, preview and logo upload', async ({ page }) => {

    const snapshot = await readTheme(page);
    const originalPrimary = snapshot.primary_color;
    const newPrimary = originalPrimary?.toLowerCase() === '#123abc' ? '#abc123' : '#123abc';

    try {
      // 1a. Open the editor: the live preview iframe points at the preview route
      await page.goto(BASE + 'admin/theme');
      await expect(page.locator('#preview-iframe')).toHaveAttribute('src', /admin\/theme\/preview/);
      const preview = await page.request.get(BASE + 'admin/theme/preview?page=dashboard');
      expect(preview.status()).toBe(200);
      expect(preview.headers()['content-type']).toContain('text/html');

      // 1b. Change the primary colour, then Save & Apply
      await page.locator('.tab[data-tab="colors"]').click();
      await page.locator('#hex_primary_color').fill(newPrimary);
      const [saveRes] = await Promise.all([
        page.waitForResponse((r) => r.url().includes('admin/theme/save') && r.request().method() === 'POST'),
        page.locator('#btn-save').click(),
      ]);
      expect(await saveRes.json()).toMatchObject({ status: 'success', message: 'Theme saved!' });
      // The page reloads after the "saved" toast; the public theme JSON now carries the new colour
      await expect.poll(async () => (await readTheme(page)).primary_color).toBe(newPrimary);

      // 1c. Upload a valid PNG logo
      const ok = await sessionPostMultipart(page, 'admin/theme/upload-logo', {
        logo_file: { name: 'qa-logo.png', mimeType: 'image/png', buffer: PNG },
      });
      expect(ok.json).toMatchObject({ status: 'success' });
      expect(ok.json.logo_path).toMatch(/^assets\/images\/logos\/.+\.png$/);

      // 1d. A .txt file and an over-size image are refused with the documented message
      const txt = await sessionPostMultipart(page, 'admin/theme/upload-logo', {
        logo_file: { name: 'notes.txt', mimeType: 'text/plain', buffer: Buffer.from('not an image') },
      });
      expect(txt.status).toBe(422);
      expect(txt.json).toMatchObject({ status: 'error', message: LOGO_ERROR });

      const big = await sessionPostMultipart(page, 'admin/theme/upload-logo', {
        logo_file: { name: 'big.png', mimeType: 'image/png', buffer: Buffer.concat([PNG, Buffer.alloc(Math.round(2.2 * 1024 * 1024))]) },
      });
      expect(big.status).toBe(422);
      expect(big.json).toMatchObject({ status: 'error', message: LOGO_ERROR });
    } finally {
      // Restore the theme exactly (only keys the save endpoint accepts are applied; null values are not sent)
      const form: Record<string, string> = {};
      for (const [k, v] of Object.entries(snapshot)) {
        if (v !== null && v !== undefined) form[k] = String(v);
      }
      const restore = await sessionPostForm(page, 'admin/theme/save', form);
      expect(restore.json).toMatchObject({ status: 'success' });
    }
    expect((await readTheme(page)).primary_color).toBe(originalPrimary);
  });

  test('Custom theme preset save and delete', async ({ page }) => {
    await page.goto(BASE + 'admin/theme');

    const name = `QA Preset ${Date.now()}`;
    let presetId: string | null = null;
    let deleted = false;
    try {
      // 1. Save the current colours as a custom preset (the page asks for the name with a prompt)
      page.once('dialog', (d) => d.accept(name));
      const [saveRes] = await Promise.all([
        page.waitForResponse((r) => r.url().includes('admin/theme/presets/save')),
        page.getByRole('button', { name: 'Save Current as Preset' }).click(),
      ]);
      const saved = await saveRes.json();
      expect(saved.status).toBe('success');
      presetId = String(saved.preset.id);

      // Preset appears
      const card = page.locator('#custom-preset-grid .preset-card').filter({ hasText: name });
      await expect(page.locator('#custom-preset-section')).toBeVisible();
      await expect(card).toHaveCount(1);

      // Still there after a reload (it is stored server side)
      await page.reload();
      await expect(page.locator('#custom-preset-grid .preset-card').filter({ hasText: name })).toHaveCount(1);

      // ...then delete it (confirm dialog) and it disappears
      page.once('dialog', (d) => d.accept());
      const [delRes] = await Promise.all([
        page.waitForResponse((r) => r.url().includes(`admin/theme/presets/delete/${presetId}`)),
        page.locator('#custom-preset-grid .preset-card').filter({ hasText: name }).locator('.preset-delete').click(),
      ]);
      // The page's own fetch never reads the delete response body, which makes delRes.json()/text() hang in
      // Playwright, so only the status is checked here; the effect is verified in the DOM and after a reload.
      expect(delRes.status()).toBe(200);
      deleted = true;
      await expect(page.locator('#custom-preset-grid .preset-card').filter({ hasText: name })).toHaveCount(0);
      await page.reload();
      await expect(page.locator('#custom-preset-grid .preset-card').filter({ hasText: name })).toHaveCount(0);
    } finally {
      if (presetId && !deleted) await sessionPostForm(page, `admin/theme/presets/delete/${presetId}`, {});
    }

    // The save endpoint validates its payload (name and 15 hex colours): an incomplete one is refused
    const bad = await sessionPostForm(page, 'admin/theme/presets/save', { name: `QA Bad ${Date.now()}`, primary_color: '#123456' });
    expect(bad.status).toBe(422);
    expect(bad.json).toMatchObject({ status: 'error', message: 'A preset name and 15 valid hex colors are required.' });
  });

  test('Admin dashboard and applicants routes gap', async ({ page }) => {

    // 1. Visit /admin/dashboard and /admin/applicants while signed in as admin.
    // Observed (also unauthenticated, checked live): both answer 404 PageNotFoundException. Routes.php defines no
    // route for them although Admin\DashboardController, Admin\ApplicantsController and views/admin/dashboard.php exist.
    // Update this test once the routes are added.
    for (const path of ['admin/dashboard', 'admin/applicants']) {
      const res = await page.request.get(BASE + path, { maxRedirects: 0 });
      expect(res.status(), path).toBe(404);
    }
    // The browser agrees: no admin dashboard to land on
    await page.goto(BASE + 'admin/dashboard');
    await expect(page).toHaveURL(BASE + 'admin/dashboard');
    await expect(page.locator('body')).toContainText(/not find|not found|404/i);

    // After login the admin lands on the theme editor instead
    await page.goto(BASE + 'admin/login');
    await expect(page).toHaveURL(BASE + 'admin/theme');
  });
});
