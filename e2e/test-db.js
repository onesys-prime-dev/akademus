// Usage: node e2e/test-db.js on|off|status
// Flips E2E_USE_TEST_DB in .env so the app (served by Apache) uses akademus_test.
const fs = require('node:fs');
const path = require('node:path');

const envPath = path.resolve(__dirname, '..', '.env');
const pattern = /^E2E_USE_TEST_DB\s*=\s*(\w+)/m;

function current() {
  const match = fs.readFileSync(envPath, 'utf8').match(pattern);
  return match ? match[1] === 'true' : false;
}

module.exports = { current };

if (require.main === module) {
  const mode = process.argv[2];
  if (mode === 'on' || mode === 'off') {
    const value = mode === 'on' ? 'true' : 'false';
    const src = fs.readFileSync(envPath, 'utf8');
    if (!pattern.test(src)) throw new Error('E2E_USE_TEST_DB is missing from .env');
    fs.writeFileSync(envPath, src.replace(pattern, `E2E_USE_TEST_DB = ${value}`));
  }
  console.log(`E2E_USE_TEST_DB = ${current()}`);
}
