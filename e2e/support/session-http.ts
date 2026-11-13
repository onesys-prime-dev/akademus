import { Page } from '@playwright/test';
import { BASE } from './base';

// Authenticated HTTP helpers for the admin / officer portals.
//
// CI4 CSRF is cookie based (Config\Security: csrfProtection = 'cookie', regenerate = false), so the
// token is simply the value of the csrf cookie the browser already holds after any page load. These
// helpers read it from the page's context and replay it as X-CSRF-TOKEN through page.request, which
// shares the signed-in session cookies. Redirects are not followed so tests can inspect Location.

export async function csrfFromPage(page: Page): Promise<string> {
  const cookies = await page.context().cookies(BASE);
  const c = cookies.find((x) => x.name.endsWith('csrf_cookie_name'));
  if (!c) throw new Error('CSRF cookie not found; load a page first');
  return c.value;
}

export type HttpResult = {
  status: number;
  text: string;
  json: any;
  location: string;
  contentType: string;
};

async function toResult(res: import('@playwright/test').APIResponse): Promise<HttpResult> {
  const text = await res.text();
  let json: any = null;
  try { json = JSON.parse(text); } catch { /* not JSON */ }
  const headers = res.headers();
  return { status: res.status(), text, json, location: headers['location'] ?? '', contentType: headers['content-type'] ?? '' };
}

const url = (path: string) => BASE + path.replace(/^\//, '');

export async function sessionGet(page: Page, path: string): Promise<HttpResult> {
  const res = await page.request.get(url(path), {
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
    maxRedirects: 0,
  });
  return toResult(res);
}

// application/x-www-form-urlencoded; array values are repeated (use keys such as "form_pages[]").
export async function sessionPostForm(page: Page, path: string, form: Record<string, string | string[]> = {}): Promise<HttpResult> {
  const token = await csrfFromPage(page);
  const body = new URLSearchParams();
  for (const [k, v] of Object.entries(form)) {
    if (Array.isArray(v)) v.forEach((x) => body.append(k, x));
    else body.append(k, v);
  }
  const res = await page.request.post(url(path), {
    data: body.toString(),
    headers: {
      'X-CSRF-TOKEN': token,
      'X-Requested-With': 'XMLHttpRequest',
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    maxRedirects: 0,
  });
  return toResult(res);
}

export async function sessionPostJson(page: Page, path: string, body: unknown): Promise<HttpResult> {
  const token = await csrfFromPage(page);
  const res = await page.request.post(url(path), {
    data: JSON.stringify(body),
    headers: { 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json' },
    maxRedirects: 0,
  });
  return toResult(res);
}

export async function sessionPostMultipart(
  page: Page,
  path: string,
  multipart: Record<string, string | { name: string; mimeType: string; buffer: Buffer }>,
): Promise<HttpResult> {
  const token = await csrfFromPage(page);
  const res = await page.request.post(url(path), {
    multipart: multipart as any,
    headers: { 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' },
    maxRedirects: 0,
  });
  return toResult(res);
}

export function uniqueSuffix() {
  return `${Date.now()}${Math.floor(Math.random() * 1000)}`;
}
