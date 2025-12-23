#!/usr/bin/env bash
set -euo pipefail

PROJECT_ROOT=$(cd "$(dirname "$0")/.." && pwd)
TARGET_DIR="$PROJECT_ROOT/Archive 1"

if rg -n "mysql_" "$TARGET_DIR" --glob '!db.php'; then
  echo "Deprecated mysql_* usages found. Please migrate to PDO or mysqli."
  exit 1
fi

echo "No mysql_* usages detected."
