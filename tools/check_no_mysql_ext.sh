#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")"/.. && pwd)"

if rg --word-regexp "mysql_" "$ROOT/Archive 1"; then
  echo "mysql_* usage found. Migration incomplete." >&2
  exit 1
fi

echo "No mysql_* usages detected."
