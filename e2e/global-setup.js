import { spawn, spawnSync } from 'node:child_process';
import testDb from './test-db.js';

const isTestDbOn = testDb.current;
const PHP = 'C:\\xampp\\php\\php.exe';

// Puts the exam tables back to the seeded state (no leftover bookings, check-ins, results or QA schedules).
function resetExamData(when) {
  const res = spawnSync(PHP, ['spark', 'db:seed', 'E2EExamResetSeeder'], { cwd: process.cwd(), encoding: 'utf8' });
  if (res.status !== 0) console.warn(`[e2e] exam data reset ${when} the run failed:\n${res.stdout}${res.stderr}`);
}

export default async function globalSetup() {
  // Never let the suite write to the dev database through Apache.
  if (!process.env.CI && !isTestDbOn()) {
    throw new Error('E2E_USE_TEST_DB is off in .env, so the app is using the dev database. Run "npm run e2e:db:on" first.');
  }

  // Start every run clean, and leave the database clean afterwards (Playwright calls the returned function
  // after all tests finish, pass or fail). Needs the schema + E2ETestSeeder data; skipped on CI, where the
  // database is created fresh for each run.
  const canReset = !process.env.CI;
  if (canReset) resetExamData('before');

  // Skip the mail worker on CI (GitHub Actions sets CI=true) and when you already run the worker yourself
  let worker = null;
  if (!process.env.CI && process.env.E2E_MAIL_WORKER !== '0') {
    worker = spawn(PHP, ['spark', 'emails:send'], { cwd: process.cwd(), stdio: 'ignore' });
  }

  return async () => {
    worker?.kill();
    if (canReset) resetExamData('after');
  };
}
