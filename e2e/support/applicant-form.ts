import { Page, expect } from '@playwright/test';
import fs from 'node:fs';
import os from 'node:os';
import path from 'node:path';
import { test as throttled } from './throttle';
import { BASE } from './base';

export { expect };

// The E2E applicant's draft is shared state, and specs run in parallel workers. Tests that edit it
// take a cross-process lock (an atomically created directory) so they run one at a time.
const LOCK = path.join(os.tmpdir(), 'akademus-e2e-applicant-draft.lock');
const STALE_MS = 3 * 60_000;

async function acquire() {
  const deadline = Date.now() + 5 * 60_000;
  for (;;) {
    try {
      fs.mkdirSync(LOCK);
      return;
    } catch {
      try {
        if (Date.now() - fs.statSync(LOCK).mtimeMs > STALE_MS) fs.rmSync(LOCK, { recursive: true, force: true });
      } catch { /* released meanwhile */ }
      if (Date.now() > deadline) throw new Error('Timed out waiting for the applicant draft lock');
      await new Promise((r) => setTimeout(r, 250));
    }
  }
}

export const test = throttled.extend<{ draftLock: void }>({
  draftLock: [async ({}, use) => {
    await acquire();
    try {
      await use();
    } finally {
      fs.rmSync(LOCK, { recursive: true, force: true });
    }
    // Waiting for the lock must not count against the per-test timeout, only against its own budget.
  }, { auto: true, timeout: 6 * 60_000 }],
});

export const STEP2_VALID = {
  firstname: 'Qa',
  middlename: 'Auto',
  lastname: 'Tester',
  gender: 'Male',
  date_of_birth: '2000-01-15',
  place_of_birth: 'Manila, Metro Manila',
  citizenship: 'Filipino',
  home_address: '1 Test Street, Barangay QA, Manila',
  contact_number: '+63 912 345 6789',
  email_address: 'qa.tester@example.com',
} as const;

export type Step2 = Record<keyof typeof STEP2_VALID, string>;

// Applicant-form steps are 1 Application Type, 2 Personal Information, 3 Family, 4 Education
// (the plan numbers personal information as step 1).
export async function gotoPersonalInfo(page: Page) {
  await page.goto(BASE + 'applicant-form/step/2');
  await expect(page.getByRole('heading', { name: 'Personal Information' })).toBeVisible();
}

export async function fillPersonalInfo(page: Page, values: Partial<Step2> = {}) {
  const data = { ...STEP2_VALID, ...values };
  for (const [name, value] of Object.entries(data)) {
    const field = page.locator(`main [name="${name}"]`);
    if (name === 'gender') await field.selectOption(value);
    else await field.fill(value);
  }
}

export const saveButton = (page: Page) => page.getByRole('button', { name: 'Save & Continue' });
