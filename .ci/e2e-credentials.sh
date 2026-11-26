#!/usr/bin/env bash
# Generates throwaway credentials for the Playwright E2E accounts (seeded by E2ETestSeeder).
# Nothing is stored anywhere: the values only live for one CI run.
#
#   In GitHub Actions:  bash .ci/e2e-credentials.sh >> "$GITHUB_ENV"
#   Locally:            eval "$(bash .ci/e2e-credentials.sh --export)"
set -euo pipefail

prefix=""
[[ "${1:-}" == "--export" ]] && prefix="export "

# 24 random alphanumerics + a fixed suffix that satisfies the app's password rules (upper, lower, digit, symbol).
pw() { echo "$(LC_ALL=C tr -dc 'A-Za-z0-9' </dev/urandom | head -c 24)Aa1!"; }

main_pw="$(pw)"
# (The workflow masks the *_PASSWORD values with ::add-mask:: before exporting them.)
while IFS='=' read -r key value; do
  printf '%s%s=%s\n' "$prefix" "$key" "$value"
done <<EOF
E2E_EMAIL=e2e.main@example.com
E2E_PASSWORD=${main_pw}
E2E_EXISTING_EMAIL=e2e.existing@example.com
E2E_TEMP_EMAIL=e2e.temp@example.com
E2E_TEMP_PASSWORD=$(pw)
E2E_FRESH_EMAIL=e2e.fresh@example.com
E2E_FRESH_PASSWORD=$(pw)
E2E_SECOND_EMAIL=e2e.second@example.com
E2E_SECOND_PASSWORD=$(pw)
OFFICER_EMAIL=e2e.officer@example.com
OFFICER_PASSWORD=$(pw)
ADMIN_EMAIL=e2e.admin@example.com
ADMIN_PASSWORD=$(pw)
EOF
