#!/usr/bin/env bash
# Error & log validation: after the test run, writable/logs must not contain PHP errors, warnings,
# notices or deprecations. Known-benign lines can be listed (one regex per line) in .ci/log-allowlist.txt.
#
#   bash .ci/assert-clean-logs.sh [log-dir]
set -euo pipefail
cd "$(dirname "$0")/.."

dir="${1:-writable/logs}"
allow=".ci/log-allowlist.txt"
shopt -s nullglob
logs=("$dir"/log-*.log)

if [[ ${#logs[@]} -eq 0 ]]; then
  echo "No log files in $dir - clean."
  exit 0
fi

matches=$(grep -hE '^(CRITICAL|ALERT|EMERGENCY|ERROR|WARNING|NOTICE)|Deprecated|deprecated' "${logs[@]}" || true)
if [[ -n "$matches" && -f "$allow" ]]; then
  matches=$(grep -vEf <(grep -vE '^\s*(#|$)' "$allow") <<<"$matches" || true)
fi

if [[ -n "$matches" ]]; then
  echo "::error title=Unexpected log entries::The test run logged errors/warnings (see below and the uploaded logs)."
  echo "$matches" | head -50
  exit 1
fi

echo "Logs are clean (${#logs[@]} file(s) checked)."
