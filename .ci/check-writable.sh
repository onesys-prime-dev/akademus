#!/usr/bin/env bash
# Filesystem & upload checks (CI `app-config` job).
#  - writable/ keeps its placeholder structure, and nothing else under writable/ is committed
#  - no .env / key / dump files are committed
#  - public/ contains no PHP besides the front controller
set -euo pipefail
cd "$(dirname "$0")/.."

fail=0
err() { echo "::error title=Filesystem check::$*"; fail=1; }

for d in cache logs session uploads debugbar; do
  [[ -f "writable/$d/index.html" ]] || err "writable/$d/index.html is missing (the directory must exist in every release)"
done

tracked_writable=$(git ls-files writable | grep -vE '^writable/(index\.html|\.htaccess|[a-z]+/index\.html)$' || true)
[[ -z "$tracked_writable" ]] || err "Runtime files are committed under writable/: $(echo "$tracked_writable" | tr '\n' ' ')"

secrets=$(git ls-files | grep -E '(^|/)\.env($|\.)|\.pem$|\.key$|id_rsa|id_ed25519|\.sql(\.gz)?$' || true)
[[ -z "$secrets" ]] || err "Sensitive file types are committed: $(echo "$secrets" | tr '\n' ' ')"

public_php=$(git ls-files public | grep -E '\.php$' | grep -vE '^public/index\.php$' || true)
[[ -z "$public_php" ]] || err "Executable PHP under public/ besides index.php: $(echo "$public_php" | tr '\n' ' ')"

for f in writable/.htaccess public/.htaccess; do
  [[ -f "$f" ]] || echo "::warning title=Filesystem check::$f is missing (Apache only; ignore on nginx)"
done

[[ $fail -eq 0 ]] && echo "Filesystem checks passed."
exit $fail
