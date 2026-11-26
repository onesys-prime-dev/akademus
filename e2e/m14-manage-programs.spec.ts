// M14 Manage Programs  (TC-M14-01 .. TC-M14-04)
// spec: specs/admin.md
// seed: e2e/seed.spec.ts
//
// Every test here is an administrator, so the login happens once in the beforeEach hook.
import { test, expect } from './support/throttle';
import { hasAdminCreds, loginAsAdmin, NO_ADMIN_CREDS_REASON } from './support/admin';
import { BASE } from './support/base';
import { deleteProgramApi, programIdByName, createProgramApi } from './support/admin-config';

test.describe('Manage Programs', () => {
  // Several tests assert row counts of the shared list, so they must not overlap with each other.
  test.describe.configure({ mode: 'serial' });

  test.beforeEach(async ({ page }) => {
    test.skip(!hasAdminCreds(), NO_ADMIN_CREDS_REASON);
    await loginAsAdmin(page);
  });

  test('Programs list and create program', async ({ page }) => {

    // The name starts with "Bachelor of Science in" on purpose: the officer program dropdown
    // (components/program-dropdown.php) only assigns its label variable for that prefix and would otherwise
    // render a stale/undefined label for a differently named program while this record exists.
    const stamp = Date.now();
    const name = `Bachelor of Science in QA ${stamp}`;
    const code = `QA${String(stamp).slice(-7)}`;
    try {
      // 1. Open /admin/programs
      await page.goto(BASE + 'admin/programs');
      await expect(page.getByRole('heading', { name: 'Programs', exact: true })).toBeVisible();
      await expect(page.getByRole('link', { name: 'Add Program' })).toBeVisible();

      //    ...create a program with a unique name/code
      await page.getByRole('link', { name: 'Add Program' }).click();
      await expect(page).toHaveURL(BASE + 'admin/programs/create');
      await page.locator('#program_name').fill(name);
      await page.locator('#program_code').fill(code.toLowerCase()); // the server upper-cases it
      await page.getByRole('button', { name: 'Create Program' }).click();

      // 'Program created successfully.' and listed
      await expect(page).toHaveURL(BASE + 'admin/programs');
      await expect(page.locator('.alert-success')).toHaveText('Program created successfully.');
      const row = page.locator('table tbody tr').filter({ hasText: name });
      await expect(row).toHaveCount(1);
      await expect(row.locator('.badge-code')).toHaveText(code);

      // appears in the sign-up dropdown source /get-programs
      const res = await page.request.get(BASE + 'get-programs');
      const programs = (await res.json()).programs as Array<{ id: string; name: string }>;
      expect(programs.map((p) => p.name)).toContain(name);
    } finally {
      const id = await programIdByName(page, name);
      if (id) await deleteProgramApi(page, id);
    }

    // Cleaned up
    await page.goto(BASE + 'admin/programs');
    await expect(page.locator('table tbody tr').filter({ hasText: name })).toHaveCount(0);
  });

  test('Edit and delete program', async ({ page }) => {

    const stamp = Date.now();
    const name = `Bachelor of Science in QA Edit ${stamp}`;
    const renamed = `Bachelor of Science in QA Renamed ${stamp}`;
    const code = `QE${String(stamp).slice(-6)}`;
    await createProgramApi(page, name, code);
    let deleted = false;
    try {
      // 1a. Edit the test program
      await page.goto(BASE + 'admin/programs');
      const row = page.locator('table tbody tr').filter({ hasText: name });
      await row.getByRole('link', { name: 'Edit' }).click();
      await expect(page.locator('#program_name')).toHaveValue(name);
      await page.locator('#program_name').fill(renamed);
      await page.getByRole('button', { name: 'Save Changes' }).click();
      await expect(page).toHaveURL(BASE + 'admin/programs');
      await expect(page.locator('.alert-success')).toHaveText('Program updated successfully.');
      await expect(page.locator('table tbody tr').filter({ hasText: renamed })).toHaveCount(1);

      // 1b. Delete it through the confirmation dialog
      await page.locator('table tbody tr').filter({ hasText: renamed }).getByRole('button', { name: 'Delete' }).click();
      await expect(page.locator('#deleteModal')).toHaveClass(/open/);
      await expect(page.locator('#deleteModalText')).toContainText(renamed);
      await page.locator('#deleteConfirmLink').click();
      await expect(page.locator('.alert-success')).toContainText('deleted successfully');
      deleted = true;
      await expect(page.locator('table tbody tr').filter({ hasText: renamed })).toHaveCount(0);

      // 2. Open /admin/programs/999999/edit: flashes 'Program not found.' on the list
      await page.goto(BASE + 'admin/programs/999999/edit');
      await expect(page).toHaveURL(BASE + 'admin/programs');
      await expect(page.locator('.alert-error')).toHaveText('Program not found.');
    } finally {
      if (!deleted) {
        const id = (await programIdByName(page, renamed)) ?? (await programIdByName(page, name));
        if (id) await deleteProgramApi(page, id);
      }
    }
  });

  test('Program create validation', async ({ page }) => {

    await page.goto(BASE + 'admin/programs');
    const rowsBefore = await page.locator('table tbody tr').count();
    test.skip(rowsBefore === 0, 'No existing program to collide with');
    const existingCode = (await page.locator('table tbody tr .badge-code').first().innerText()).trim();
    const existingName = (await page.locator('table tbody tr td strong').first().innerText()).trim();

    // 1a. Submit empty: blocked by the required attributes in the browser
    await page.goto(BASE + 'admin/programs/create');
    await page.getByRole('button', { name: 'Create Program' }).click();
    await expect(page).toHaveURL(BASE + 'admin/programs/create');
    expect(await page.locator('#program_name').evaluate((el: HTMLInputElement) => el.validity.valueMissing)).toBe(true);

    //     ...and on the server when the browser check is bypassed (same form, `required` removed)
    await page.locator('#program_name, #program_code').evaluateAll((els) => els.forEach((e) => e.removeAttribute('required')));
    await page.getByRole('button', { name: 'Create Program' }).click();
    await expect(page.getByText('Program name is required.')).toBeVisible();
    await expect(page.getByText('Program code is required.')).toBeVisible();

    // 1b. Duplicate name (new code) and duplicate code (new name) on the real form
    const stamp = Date.now();
    await page.goto(BASE + 'admin/programs/create');
    await page.locator('#program_name').fill(existingName);
    await page.locator('#program_code').fill(`QD${String(stamp).slice(-6)}`);
    await page.getByRole('button', { name: 'Create Program' }).click();
    await expect(page.getByText('A program with this name already exists.')).toBeVisible();

    await page.goto(BASE + 'admin/programs/create');
    await page.locator('#program_name').fill(`Bachelor of Science in QA Dup ${stamp}`);
    await page.locator('#program_code').fill(existingCode);
    await page.getByRole('button', { name: 'Create Program' }).click();
    await expect(page.getByText('A program with this code already exists.')).toBeVisible();

    // No record was created
    await page.goto(BASE + 'admin/programs');
    await expect(page.locator('table tbody tr')).toHaveCount(rowsBefore);
    await expect(page.locator('table tbody tr').filter({ hasText: `QA Dup ${stamp}` })).toHaveCount(0);
  });
});
