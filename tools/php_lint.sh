#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")"/.. && pwd)"
find "$ROOT/Archive 1" -type f -name "*.php" -print0 | while IFS= read -r -d '' file; do
  php -l "$file"
done
