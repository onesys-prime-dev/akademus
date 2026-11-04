// M19 Manage Users  (TC-M19-01 .. TC-M19-06)
// spec: specs/admin.md
// seed: e2e/seed.spec.ts
//
// Every test here is an administrator, so the login happens once in the beforeEach hook.
import { test, expect } from './support/throttle';
import { hasAdminCreds, loginAsAdmin, NO_ADMIN_CREDS_REASON } from './support/admin';
import { BASE } from './support/base';
import { OFFICER_DASHBOARD, OFFICER_LOGIN } from './support/officer';
import { TEST_PASSWORD, deleteUserApi, userIdByEmail, createUserApi } from './support/admin-users';
import { sessionPostForm } from './support/session-http';

test.describe('Manage Users', () => {
  // Several tests assert row counts of the shared list, so they must not overlap with each other.
  test.describe.configure({ mode: 'serial' });

  test.beforeEach(async ({ page }) => {
    test.skip(!hasAdminCreds(), NO_ADMIN_CREDS_REASON);
    await loginAsAdmin(page);
  });

  test('Users list renders', async ({ page }) => {

    // 1. Open /admin/users
    await page.goto(BASE + 'admin/users');
    await expect(page.locator('.topbar-title')).toHaveText('Manage Users');
    for (const col of ['User', 'Role', 'Actions']) {
      await expect(page.locator('thead th', { hasText: col })).toBeVisible();
    }

    // The signed-in admin is listed with an Admin badge, edit and password actions, but no Delete button
    const me = page.locator('#users-tbody tr[data-role="admin"]').filter({ hasText: process.env.ADMIN_EMAIL! });
    await expect(me).toHaveCount(1);
    await expect(me.locator('.badge-admin')).toHaveText('Admin');
    await expect(me.locator('.btn-edit')).toBeVisible();
    await expect(me.locator('.btn-pwd')).toBeVisible();
    await expect(me.locator('.btn-del')).toHaveCount(0);

    // Role tabs filter the table
    await page.getByRole('button', { name: 'Admins' }).click();
    await expect(page.locator('#users-tbody tr[data-role="admission_officer"]:visible')).toHaveCount(0);
    await expect(page.locator('#users-tbody tr[data-role="admin"]:visible').first()).toBeVisible();
    await page.getByRole('button', { name: 'Admission Officers' }).click();
    await expect(page.locator('#users-tbody tr[data-role="admin"]:visible')).toHaveCount(0);
    await page.getByRole('button', { name: 'All Users' }).click();
    await expect(page.locator('#users-tbody tr[data-role="admin"]:visible').first()).toBeVisible();
  });

  test('Create officer user', async ({ page, browser }) => {

    const stamp = Date.now();
    const email = `qa.officer.${stamp}@example.com`;
    let createdId: number | null = null;
    try {
      // 1. Add user with unique email, valid role and 8+ char password
      await page.goto(BASE + 'admin/users');
      await page.getByRole('button', { name: 'Add User' }).click();
      await expect(page.locator('#user-modal')).toHaveClass(/open/);
      await expect(page.locator('#modal-title')).toHaveText('Add User');
      await page.locator('#f-firstname').fill('QA');
      await page.locator('#f-lastname').fill(`Officer${stamp}`);
      await page.locator('#f-email').fill(email);
      await page.locator('#f-password').fill(TEST_PASSWORD);
      await page.locator('#f-role').selectOption('admission_officer');
      await page.locator('#modal-save-btn').click();

      // 'User created successfully.' and the row appears after the page reloads
      await expect(page.locator('#toast')).toHaveText('User created successfully.');
      const row = page.locator('#users-tbody tr[data-role="admission_officer"]').filter({ hasText: email });
      await expect(row).toBeVisible();
      await expect(row.locator('.badge-officer')).toHaveText('Admission Officer');
      createdId = await userIdByEmail(page, email);
      expect(createdId).not.toBeNull();

      // The new officer can log in at the officer login (separate browser context)
      const context = await browser.newContext();
      try {
        const officerPage = await context.newPage();
        await officerPage.goto(OFFICER_LOGIN);
        await officerPage.locator('input[name=email]').fill(email);
        await officerPage.locator('input[name=password]').fill(TEST_PASSWORD);
        await officerPage.locator('#login-btn').click();
        await expect(officerPage).toHaveURL(OFFICER_DASHBOARD, { timeout: 15_000 });
      } finally {
        await context.close();
      }
    } finally {
      // Self-cleaning: remove the throwaway user
      const id = createdId ?? (await userIdByEmail(page, email));
      if (id) await deleteUserApi(page, id);
    }

    // Deleted user is gone
    await page.goto(BASE + 'admin/users');
    await expect(page.locator('#users-tbody tr', { hasText: email })).toHaveCount(0);
  });

  test('Create user validation', async ({ page }) => {
    await page.goto(BASE + 'admin/users');
    const rowsBefore = await page.locator('#users-tbody tr[data-role]').count();

    // 1a. Submit empty in the UI: the form checks the password length first
    await page.getByRole('button', { name: 'Add User' }).click();
    await page.locator('#modal-save-btn').click();
    await expect(page.locator('#modal-error')).toHaveText('Password must be at least 8 characters.');
    await expect(page.locator('#user-modal')).toHaveClass(/open/);

    // 1b. A 7-character password in the UI
    await page.locator('#f-firstname').fill('QA');
    await page.locator('#f-lastname').fill('Short');
    await page.locator('#f-email').fill(`qa.short.${Date.now()}@example.com`);
    await page.locator('#f-password').fill('1234567');
    await page.locator('#modal-save-btn').click();
    await expect(page.locator('#modal-error')).toHaveText('Password must be at least 8 characters.');

    // Server-side validation, posted directly (tampered requests). Order of checks: role, required, email, length.
    const base = { firstname: 'QA', lastname: 'Invalid', email: `qa.invalid.${Date.now()}@example.com`, password: 'ValidPass!123', role: 'admission_officer' };
    const cases: Array<[string, Record<string, string>, string]> = [
      ['empty fields', { ...base, firstname: '', lastname: '', email: '', password: '' }, 'All fields are required.'],
      ['invalid email', { ...base, email: 'not-an-email' }, 'Invalid email address.'],
      ['7-char password', { ...base, password: '1234567' }, 'Password must be at least 8 characters.'],
      ['tampered role', { ...base, role: 'superuser' }, 'Invalid role.'],
      ['missing role', { ...base, role: '' }, 'Invalid role.'],
      ['duplicate email', { ...base, email: process.env.ADMIN_EMAIL! }, 'Email is already in use.'],
    ];
    for (const [label, form, message] of cases) {
      const res = await sessionPostForm(page, 'admin/users/store', form);
      expect(res.json, label).toMatchObject({ status: 'error', message });
    }

    // Nothing was created
    await page.goto(BASE + 'admin/users');
    await expect(page.locator('#users-tbody tr[data-role]')).toHaveCount(rowsBefore);
    await expect(page.locator('#users-tbody tr', { hasText: 'qa.invalid.' })).toHaveCount(0);
  });

  test('Edit, reset password and delete user', async ({ page, browser }) => {

    // Throwaway officer created through the same endpoint the Add User dialog posts to
    const stamp = Date.now();
    const email = `qa.modify.${stamp}@example.com`;
    const created = await createUserApi(page, { firstname: 'QA', lastname: `Modify${stamp}`, email });
    expect(created.json).toMatchObject({ status: 'success', message: 'User created successfully.' });
    let deleted = false;
    try {
      await page.goto(BASE + 'admin/users');
      const row = () => page.locator('#users-tbody tr[data-role]').filter({ hasText: email });
      await expect(row()).toBeVisible();

      // 1a. Edit the test-created user
      await row().locator('.btn-edit').click();
      await expect(page.locator('#modal-title')).toHaveText('Edit User');
      await expect(page.locator('#f-password-group')).toBeHidden();
      await page.locator('#f-lastname').fill(`Renamed${stamp}`);
      await page.locator('#modal-save-btn').click();
      await expect(page.locator('#toast')).toHaveText('User updated successfully.');
      await expect(row()).toContainText(`Renamed${stamp}`);

      // 1b. Reset its password: mismatch and short values are refused client side
      await row().locator('.btn-pwd').click();
      await page.locator('#p-password').fill('NewPass!12345');
      await page.locator('#p-confirm').fill('Different!12345');
      await page.locator('#pwd-save-btn').click();
      await expect(page.locator('#pwd-error')).toHaveText('Passwords do not match.');
      await page.locator('#p-password').fill('short');
      await page.locator('#p-confirm').fill('short');
      await page.locator('#pwd-save-btn').click();
      await expect(page.locator('#pwd-error')).toHaveText('Password must be at least 8 characters.');
      // ...and a valid one succeeds (no email is sent by this action)
      await page.locator('#p-password').fill('NewPass!12345');
      await page.locator('#p-confirm').fill('NewPass!12345');
      await page.locator('#pwd-save-btn').click();
      await expect(page.locator('#toast')).toHaveText('Password reset successfully.');

      // The new password works at the officer login, the old one does not
      const context = await browser.newContext();
      try {
        const officerPage = await context.newPage();
        await officerPage.goto(OFFICER_LOGIN);
        await officerPage.locator('input[name=email]').fill(email);
        await officerPage.locator('input[name=password]').fill(TEST_PASSWORD);
        await officerPage.locator('#login-btn').click();
        await expect(officerPage.locator('#error-modal')).toHaveClass(/open/);
        await officerPage.locator('#error-close').click();
        await officerPage.locator('input[name=password]').fill('NewPass!12345');
        await officerPage.locator('#login-btn').click();
        await expect(officerPage).toHaveURL(OFFICER_DASHBOARD, { timeout: 15_000 });
      } finally {
        await context.close();
      }

      // 1c. Delete it
      await row().locator('.btn-del').click();
      await expect(page.locator('#del-name')).toContainText(`Renamed${stamp}`);
      await page.locator('#del-confirm-btn').click();
      await expect(page.locator('#toast')).toHaveText('User deleted.');
      deleted = true;
      await expect(row()).toHaveCount(0);

      // Deleting the currently signed-in admin is blocked: no Delete button, and a tampered request is refused
      const me = page.locator('#users-tbody tr[data-role="admin"]').filter({ hasText: process.env.ADMIN_EMAIL! });
      await expect(me.locator('.btn-del')).toHaveCount(0);
      const myId = await userIdByEmail(page, process.env.ADMIN_EMAIL!);
      expect(myId).not.toBeNull();
      const self = await sessionPostForm(page, `admin/users/${myId}/delete`, {});
      expect(self.json).toMatchObject({ status: 'error', message: 'You cannot delete your own account.' });
    } finally {
      if (!deleted) {
        const id = await userIdByEmail(page, email);
        if (id) await deleteUserApi(page, id);
      }
    }
  });
});
