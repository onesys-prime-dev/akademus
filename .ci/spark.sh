#!/usr/bin/env bash
# Runs `php spark <args>` and fails when the command printed an exception.
# Needed because spark commands such as migrate and db:seed catch exceptions, print them and still
# exit 0 - a broken migration or seeder would otherwise pass CI. Same check as deploy/remote-deploy.sh.
#
#   bash .ci/spark.sh migrate
#   bash .ci/spark.sh db:seed DatabaseSeeder
set -uo pipefail
cd "$(dirname "$0")/.." || exit 1

out=$(php spark "$@" 2>&1)
rc=$?
printf '%s\n' "$out"

if [[ $rc -ne 0 ]]; then
  echo "::error title=spark $1::exited with code $rc"
  exit "$rc"
fi
if grep -qE '^\s*\[[A-Za-z0-9_\]*(Exception|Error)\]' <<<"$out"; then
  echo "::error title=spark $1::the command printed an exception (spark still exited 0)"
  exit 1
fi
