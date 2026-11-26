import { Page } from '@playwright/test';

// The auth pages (auth/main.php) re-inject the form via fetch() after DOMContentLoaded,
// which wipes typed input and unbound handlers. Navigate, then wait for that swap to finish.
export async function gotoAuthPage(page: Page, url: string) {
  await page.addInitScript(() => {
    document.addEventListener('DOMContentLoaded', () => {
      const c = document.getElementById('auth-form-container');
      if (!c) return;
      new MutationObserver(() => {
        (window as any).__formSwapped = true;
      }).observe(c, { childList: true });
    });
  });
  await page.goto(url);
  await page.waitForFunction(() => (window as any).__formSwapped === true);
}
