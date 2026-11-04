import { spawn } from 'node:child_process';
import testDb from './test-db.js';

const isTestDbOn = testDb.current;

export default async function globalSetup() {
  // Never let the suite write to the dev database through Apache.
  if (!process.env.CI && !isTestDbOn()) {
    throw new Error('E2E_USE_TEST_DB is off in .env, so the app is using the dev database. Run "npm run e2e:db:on" first.');
  }

  // Skip on CI (GitHub Actions sets CI=true) and when you already run the worker yourself
  if (process.env.CI || process.env.E2E_MAIL_WORKER === '0') return;

  // Start the mail worker from the project root
  const worker = spawn('C:\\xampp\\php\\php.exe', ['spark', 'emails:send'], {
    cwd: process.cwd(),
    stdio: 'ignore',
  });

  // Playwright runs this after all the tests finish
  return async () => {
    worker.kill();
  };
}
