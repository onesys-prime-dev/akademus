import { Page } from '@playwright/test';
import { BASE } from './base';
import { sessionPostForm } from './session-http';

export const TEST_PASSWORD = 'QaPass!12345';

// Reads a user's id from the Edit button's openEdit(ID, ...) handler on /admin/users.
export async function userIdByEmail(page: Page, email: string): Promise<number | null> {
  await page.goto(BASE + 'admin/users');
  const row = page.locator('#users-tbody tr[data-role]').filter({ hasText: email });
  if ((await row.count()) === 0) return null;
  const onclick = (await row.first().locator('.btn-edit').getAttribute('onclick')) ?? '';
  const m = onclick.match(/openEdit\((\d+)/);
  return m ? Number(m[1]) : null;
}

export async function createUserApi(
  page: Page,
  u: { firstname: string; lastname: string; email: string; password?: string; role?: 'admin' | 'admission_officer' },
) {
  return sessionPostForm(page, 'admin/users/store', {
    firstname: u.firstname,
    lastname: u.lastname,
    email: u.email,
    password: u.password ?? TEST_PASSWORD,
    role: u.role ?? 'admission_officer',
  });
}

export async function deleteUserApi(page: Page, id: number) {
  return sessionPostForm(page, `admin/users/${id}/delete`, {});
}
