// M16 Fee Settings  (TC-M16-01 .. TC-M16-04)
// spec: specs/admin.md
// seed: e2e/seed.spec.ts
//
// Every test here is an administrator, so the login happens once in the beforeEach hook.
import { test, expect } from './support/throttle';
import { hasAdminCreds, loginAsAdmin, NO_ADMIN_CREDS_REASON } from './support/admin';
import { BASE } from './support/base';
import { sessionPostForm } from './support/session-http';

// There is no delete route for payment methods, so every method this spec creates stays in the table as an
// INACTIVE row ("QA Method ..."). The spec creates exactly one per run and always switches it off.

test.describe('Fee Settings and payment methods', () => {
  test.beforeEach(async ({ page }) => {
    test.skip(!hasAdminCreds(), NO_ADMIN_CREDS_REASON);
    await loginAsAdmin(page);
  });

  test('Update fee amount', async ({ page }) => {
    await page.goto(BASE + 'admin/payment-fees');
    await expect(page.getByRole('heading', { name: 'Payment Settings' })).toBeVisible();

    // Prefer the admission fee row, otherwise the first fee
    const forms = page.locator('form[id^="fee-form-"]');
    test.skip((await forms.count()) === 0, 'No fee types configured');
    const admissionRow = page.locator('table.settings-table tbody tr').filter({ hasText: /admission/i }).filter({ has: page.locator('form[id^="fee-form-"]') });
    const form = (await admissionRow.count()) ? admissionRow.first().locator('form[id^="fee-form-"]') : forms.first();
    const formId = (await form.getAttribute('id'))!;
    const feeId = formId.replace('fee-form-', '');
    const input = form.locator('input[name=amount]');
    const original = await input.inputValue();
    const feePath = `admin/payment-fees/update/${feeId}`;

    const saveAmount = async (value: string) => {
      await input.fill(value);
      const [res] = await Promise.all([
        page.waitForResponse((r) => r.url().includes(feePath)),
        page.locator(`#fee-btn-${feeId}`).click(),
      ]);
      return res.json();
    };

    try {
      // 1a. Set the fee to a valid number
      const ok = await saveAmount('1234.56');
      expect(ok).toMatchObject({ success: true, message: 'Fee amount updated.' });
      await expect(page.locator(`#fee-btn-${feeId}`)).toContainText('Saved');
      await page.reload();
      await expect(page.locator(`#fee-form-${feeId} input[name=amount]`)).toHaveValue('1234.56');

      // 1b. Empty value reaches the server (the field is not `required`): 'Invalid amount.'
      const emptyInput = page.locator(`#fee-form-${feeId} input[name=amount]`);
      await emptyInput.fill('');
      const [emptyRes] = await Promise.all([
        page.waitForResponse((r) => r.url().includes(feePath)),
        page.locator(`#fee-btn-${feeId}`).click(),
      ]);
      expect(await emptyRes.json()).toMatchObject({ success: false, message: 'Invalid amount.' });

      // 1c. -1 and 'abc' are blocked by the number input in the browser; the server refuses them as well
      for (const bad of ['-1', 'abc', '']) {
        const res = await sessionPostForm(page, feePath, { amount: bad });
        expect(res.json, JSON.stringify(bad)).toMatchObject({ success: false, message: 'Invalid amount.' });
      }

      // The stored amount is unchanged by all the invalid attempts
      await page.reload();
      await expect(page.locator(`#fee-form-${feeId} input[name=amount]`)).toHaveValue('1234.56');
      // (The applicant fee page showing the new amount is not asserted: it depends on the applicant's payment stage.)
    } finally {
      // Restore the original amount
      const restored = await sessionPostForm(page, feePath, { amount: original });
      expect(restored.json).toMatchObject({ success: true });
    }
    await page.reload();
    await expect(page.locator(`#fee-form-${feeId} input[name=amount]`)).toHaveValue(original);
  });

  test.describe('payment-methods', () => {
    test.describe.configure({ mode: 'serial' });

    test('Add, edit and toggle payment method', async ({ page }) => {
      await page.goto(BASE + 'admin/payment-fees');

      const stamp = Date.now();
      const name = `QA Method ${stamp}`;
      const renamed = `QA Method Renamed ${stamp}`;

      // The "Add" dialog's own request (see the next test) omits the CSRF token, so the method is created through the
      // same endpoint with the token supplied.
      const empty = await sessionPostForm(page, 'admin/payment-methods/add', { name: '   ' });
      expect(empty.json).toMatchObject({ success: false, message: 'Method name is required.' });
      // NOTE (app/test-DB issue): payment_method_account.payment_method_account_id has no AUTO_INCREMENT in the test DB,
      // so every account row gets id 0. The row's Save then posts account_id=0 and updateMethod() INSERTS another account,
      // which makes the method render as duplicate table rows (duplicate DOM ids). To keep the row unique the method is
      // created without account details, so the first edit inserts exactly one account row.
      const added = await sessionPostForm(page, 'admin/payment-methods/add', { name });
      expect(added.json.success).toBe(true);
      const id = String(added.json.method.payment_method_id);

      try {
        await page.reload();
        const nameInput = page.locator(`#m-name-${id}`);
        await expect(nameInput).toHaveValue(name);
        await expect(page.locator(`#toggle-label-${id}`)).toHaveText('ON');

        // 1a. Edit the name
        await nameInput.fill(renamed);
        const [saved] = await Promise.all([
          page.waitForResponse((r) => r.url().includes(`admin/payment-methods/update/${id}`)),
          page.locator(`#m-btn-${id}`).click(),
        ]);
        expect(await saved.json()).toMatchObject({ success: true });
        await page.reload();
        await expect(page.locator(`#m-name-${id}`)).toHaveValue(renamed);

        // 1b. Editing with an empty name is refused in the row
        await page.locator(`#m-name-${id}`).fill('');
        await page.locator(`#m-btn-${id}`).click();
        await expect(page.locator(`#m-err-${id}`)).toHaveText('Method name is required.');

        // 1c. Add with an empty name: refused in the add row ('Method name is required.')
        await page.reload();
        await page.locator('.btn-add').click();
        await page.locator('#add-save-btn').click();
        await expect(page.locator('#add-err')).toHaveText('Method name is required.');

        // 1d. Toggle inactive
        const [toggled] = await Promise.all([
          page.waitForResponse((r) => r.url().includes(`admin/payment-methods/toggle/${id}`)),
          page.locator(`#toggle-wrap-${id}`).click(),
        ]);
        expect(await toggled.json()).toMatchObject({ success: true, is_active: 0 });
        await expect(page.locator(`#toggle-label-${id}`)).toHaveText('OFF');
        await page.reload();
        await expect(page.locator(`#toggle-label-${id}`)).toHaveText('OFF');
        // (Hidden from the applicant payment pages is not asserted: those pages only list methods at a given payment stage.)
      } finally {
        // Always leave the method inactive
        await page.goto(BASE + 'admin/payment-fees');
        const label = page.locator(`#toggle-label-${id}`).first();
        if ((await label.count()) && (await label.innerText()).trim() === 'ON') {
          await sessionPostForm(page, `admin/payment-methods/toggle/${id}`, {});
        }
      }
    });

    test('Add payment method through the dialog', async ({ page }) => {

      // CONFIRMED APP BUG (verified live): admin/payments/fees.php addMethod() posts a FormData without the CSRF
      // field/header (every other call on the page appends CSRF_NAME/CSRF_HASH), so the server answers 403
      // (SecurityException) and the dialog cannot add a method. When fixed this test passes and test.fail must be dropped.
      test.fail(true, 'App bug: Add Method dialog omits the CSRF token (403)');

      await page.goto(BASE + 'admin/payment-fees');
      const name = `QA Method UI ${Date.now()}`;
      await page.locator('.btn-add').click();
      await page.locator('#add-name').fill(name);
      const [resp] = await Promise.all([
        page.waitForResponse((r) => r.url().includes('admin/payment-methods/add')),
        page.locator('#add-save-btn').click(),
      ]);
      expect(resp.status()).toBe(200);
      await expect(page.locator('#methods-body tr').filter({ has: page.locator(`input[value="${name}"]`) })).toHaveCount(1);
      // If it did work, switch the stray method off again (there is no delete route)
      const row = page.locator('#methods-body tr').filter({ has: page.locator(`input[value="${name}"]`) });
      await row.locator('.toggle-wrap').click();
    });
  });

  test('Toggle non-existent method', async ({ page }) => {

    // 1. POST /admin/payment-methods/toggle/999999
    const res = await sessionPostForm(page, 'admin/payment-methods/toggle/999999', {});
    expect(res.status).toBe(200);
    expect(res.json).toMatchObject({ success: false, message: 'Method not found.' });
  });
});
