import { Browser, expect } from '@playwright/test';
import fs from 'node:fs';
import os from 'node:os';
import path from 'node:path';
import { BASE } from './base';
import { hasAdminCreds, loginAsAdmin } from './admin';
import { clearLoginThrottle } from './throttle';

// exam_enabled is a global setting that the test DB keeps ON (the E2ETestSeeder baseline).
// The "exam disabled" specs need it OFF, so they flip it through the real admin Settings page and
// restore it afterwards; withExamEnabled() is a no-op flip when it is already ON. A lock directory serialises every spec that touches the flag across workers.
// NOTE: applicant/exam-disabled.spec.ts is not covered by this lock; run admin/officer specs with
// --workers=1 and not concurrently with the applicant specs.

const LOCK_DIR = path.join(os.tmpdir(), 'akademus-e2e-exam-setting.lock');

async function acquireLock(timeoutMs = 180_000) {
  const start = Date.now();
  for (;;) {
    try {
      fs.mkdirSync(LOCK_DIR);
      return;
    } catch {
      // Break a stale lock (a crashed run) after 5 minutes
      try {
        if (Date.now() - fs.statSync(LOCK_DIR).mtimeMs > 300_000) fs.rmSync(LOCK_DIR, { recursive: true, force: true });
      } catch { /* raced with the owner */ }
      if (Date.now() - start > timeoutMs) throw new Error('Timed out waiting for the exam-setting lock');
      await new Promise((r) => setTimeout(r, 500));
    }
  }
}

function releaseLock() {
  fs.rmSync(LOCK_DIR, { recursive: true, force: true });
}

async function setExamEnabled(browser: Browser, enabled: boolean): Promise<boolean> {
  const context = await browser.newContext();
  const page = await context.newPage();
  try {
    clearLoginThrottle();
    await loginAsAdmin(page);
    await page.goto(BASE + 'admin/settings');
    const box = page.locator('#toggle-exam');
    const previous = await box.isChecked();
    if (previous !== enabled) {
      await page.locator('label[for="toggle-exam"]').click();
      await expect(box).toBeChecked({ checked: enabled });
      await page.getByRole('button', { name: 'Save Settings' }).click();
      await expect(page.getByText('Settings saved.')).toBeVisible();
    }
    return previous;
  } finally {
    await context.close();
  }
}

// Runs fn with exam scheduling enabled, then puts the flag back exactly as it was.
// Without admin credentials the flag is left alone (callers must cope with 'disabled'), but fn still runs
// under the lock so parallel browser projects never use the same seeded applicants at the same time.
export async function withExamEnabled<T>(browser: Browser, fn: () => Promise<T>): Promise<T> {
  if (!hasAdminCreds()) {
    await acquireLock();
    try {
      return await fn();
    } finally {
      releaseLock();
    }
  }

  await acquireLock();
  let previous: boolean | null = null;
  try {
    previous = await setExamEnabled(browser, true);
    return await fn();
  } finally {
    try {
      if (previous === false) await setExamEnabled(browser, false);
    } finally {
      releaseLock();
    }
  }
}

export { acquireLock, releaseLock, setExamEnabled };
