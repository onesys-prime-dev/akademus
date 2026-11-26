import { test as base } from '@playwright/test';
import fs from 'node:fs';
import path from 'node:path';

// LoginThrottle (app/Libraries/LoginThrottle.php) allows 5 attempts per 60s per IP and
// keeps its counters as throttler_login_* files in writable/cache. Every worker shares one IP,
// so clear them before each test instead of weakening the app.
const CACHE_DIR = path.resolve(__dirname, '..', '..', 'writable', 'cache');

export function clearLoginThrottle() {
  if (!fs.existsSync(CACHE_DIR)) return;
  for (const file of fs.readdirSync(CACHE_DIR)) {
    if (file.startsWith('throttler_login_')) fs.rmSync(path.join(CACHE_DIR, file), { force: true });
  }
}

export const test = base.extend<{ clearThrottle: void }>({
  clearThrottle: [async ({}, use) => {
    clearLoginThrottle();
    await use();
  }, { auto: true }],
});

export { expect } from '@playwright/test';
