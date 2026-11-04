import { APIRequestContext, APIResponse } from '@playwright/test';
import { BASE, SIGN_IN } from './base';

// CI4 CSRF is cookie based: every non-GET request needs the csrf cookie plus a matching
// X-CSRF-TOKEN header (the app exposes it as <meta name="csrf-token">). The request context
// keeps cookies, so fetch a public page first and then replay the token.
export async function csrfToken(request: APIRequestContext, fromPath = SIGN_IN): Promise<string> {
  const res = await request.get(fromPath);
  const html = await res.text();
  const m = html.match(/<meta name="csrf-token" content="([^"]+)"/);
  if (!m) throw new Error('csrf-token meta not found on ' + fromPath);
  return m[1];
}

export async function postForm(
  request: APIRequestContext,
  path: string,
  form: Record<string, string> = {},
  opts: { tokenFrom?: string } = {},
): Promise<APIResponse> {
  const token = await csrfToken(request, opts.tokenFrom ?? SIGN_IN);
  return request.post(BASE + path.replace(/^\//, ''), {
    form,
    headers: { 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' },
    maxRedirects: 0,
  });
}
