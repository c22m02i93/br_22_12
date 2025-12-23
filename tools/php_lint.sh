#!/usr/bin/env bash
set -euo pipefail

PROJECT_ROOT=$(cd "$(dirname "$0")/.." && pwd)
TARGET_DIR="$PROJECT_ROOT/Archive 1"

find "$TARGET_DIR" -name "*.php" -print0 | while IFS= read -r -d '' file; do
  echo "Linting $file"
  php -l "$file" >/dev/null
done

echo "PHP lint completed."
