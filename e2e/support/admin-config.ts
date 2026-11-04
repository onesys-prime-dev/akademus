import { Page, expect } from '@playwright/test';
import { BASE } from './base';
import { sessionGet, sessionPostForm } from './session-http';

// Helpers for creating and removing throwaway programs / presets / requirement categories.
// All ids are scraped from the admin list pages (the links carry the id).

export async function programIdByName(page: Page, name: string): Promise<number | null> {
  await page.goto(BASE + 'admin/programs');
  const row = page.locator('table tbody tr').filter({ hasText: name });
  if ((await row.count()) === 0) return null;
  const href = (await row.first().locator('a[href*="/edit"]').getAttribute('href')) ?? '';
  const m = href.match(/programs\/(\d+)\/edit/);
  return m ? Number(m[1]) : null;
}

export async function createProgramApi(page: Page, name: string, code: string) {
  const res = await sessionPostForm(page, 'admin/programs/create', { program_name: name, program_code: code });
  expect([302, 303], 'program create should redirect on success').toContain(res.status);
  return res;
}

export async function deleteProgramApi(page: Page, id: number) {
  return sessionGet(page, `admin/programs/${id}/delete`);
}

export async function presetIdByName(page: Page, name: string): Promise<number | null> {
  await page.goto(BASE + 'admin/presets');
  const card = page.locator('.preset-card').filter({ hasText: name });
  if ((await card.count()) === 0) return null;
  const href = (await card.first().locator('a.btn-edit').getAttribute('href')) ?? '';
  const m = href.match(/presets\/(\d+)\/edit/);
  return m ? Number(m[1]) : null;
}

// ids of the first enabled form page / requirement category offered by the Create Form screen
export async function firstPresetChoices(page: Page): Promise<{ pageId: string | null; categoryId: string | null }> {
  await page.goto(BASE + 'admin/presets/create');
  const pageId = await page.locator('input[name="form_pages[]"]').first().getAttribute('value').catch(() => null);
  const categoryId = await page.locator('input[name="requirement_categories[]"]').first().getAttribute('value').catch(() => null);
  return { pageId, categoryId };
}

export async function createPresetApi(
  page: Page,
  p: { name: string; description?: string; pageIds: string[]; categoryIds?: string[]; isGlobal?: boolean },
) {
  const form: Record<string, string | string[]> = {
    name: p.name,
    description: p.description ?? 'QA throwaway preset',
    'form_pages[]': p.pageIds,
  };
  if (p.categoryIds?.length) form['requirement_categories[]'] = p.categoryIds;
  if (p.isGlobal) form['is_global'] = '1';
  return sessionPostForm(page, 'admin/presets/store', form);
}

export async function deletePresetApi(page: Page, id: number) {
  return sessionGet(page, `admin/presets/${id}/delete`);
}

export async function categoryIdByName(page: Page, name: string): Promise<number | null> {
  await page.goto(BASE + 'admin/requirement-categories');
  const row = page.locator('table tbody tr').filter({ hasText: name });
  if ((await row.count()) === 0) return null;
  const href = (await row.first().locator('a[href*="/edit"]').getAttribute('href')) ?? '';
  const m = href.match(/requirement-categories\/(\d+)\/edit/);
  return m ? Number(m[1]) : null;
}

export async function deleteCategoryApi(page: Page, id: number) {
  return sessionGet(page, `admin/requirement-categories/${id}/delete`);
}
