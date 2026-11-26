// M18 Fast Track Requirements  (TC-M18-01 .. TC-M18-02)
// spec: specs/admin.md
// seed: e2e/seed.spec.ts
//
// Every test here is an administrator, so the login happens once in the beforeEach hook.
import { test, expect } from './support/throttle';
import { hasAdminCreds, loginAsAdmin, NO_ADMIN_CREDS_REASON } from './support/admin';
import { BASE } from './support/base';

test.describe('Fast Track Requirements', () => {
  test.beforeEach(async ({ page }) => {
    test.skip(!hasAdminCreds(), NO_ADMIN_CREDS_REASON);
    await loginAsAdmin(page);
  });

  test('Fast track requirements', async ({ page }) => {
    await page.goto(BASE + 'admin/fast-track-requirements');

    const boxes = page.locator('input[name="is_fast_track_required[]"]');
    test.skip((await boxes.count()) === 0, 'No requirement documents exist to flag as fast-track');
    const first = boxes.first();
    const boxId = await first.getAttribute('id');
    const original = await first.isChecked();

    const save = async (checked: boolean) => {
      await page.goto(BASE + 'admin/fast-track-requirements');
      const box = page.locator(`#${boxId}`);
      if ((await box.isChecked()) !== checked) await page.locator(`label[for="${boxId}"]`).click();
      await expect(box).toBeChecked({ checked });
      await page.getByRole('button', { name: 'Save Changes' }).click();
      await expect(page).toHaveURL(BASE + 'admin/fast-track-requirements');
      await expect(page.locator('.flash-success')).toHaveText('Fast Track requirements saved.');
      await expect(page.locator(`#${boxId}`)).toBeChecked({ checked });
    };

    try {
      // 1. Toggle a document flagged as fast-track required and save
      await save(!original);
    } finally {
      // Restore the original flag
      await save(original);
    }
    // Plan item "applicant fast-track list matches" needs a fast-track applicant session; not covered here.
  });
});
