# Legacy site migration (PHP 8.3 / MySQL 8.0)

## Setup
1. Copy `.env.example` to `.env` and set real credentials (do not commit `.env`).
2. Ensure PHP 8.3+ with `pdo_mysql`, `imagick` or GD, and MySQL 8.0.
3. Place the web root at `Archive 1/` (do not change structure).

## Database migrations
SQL migrations live in `db/migrations/`:
- `001_utf8mb4.sql` – switch database/tables to `utf8mb4`/`utf8mb4_unicode_ci`.
- `002_indexes.sql` – add indexes for date/year filters and full-text search (fill in after schema review).

Apply in order after importing the legacy dump (`host1409556_barysh.sql`) into MySQL 8.0.

## Utilities
- `tools/convert_project_to_utf8.php` — convert project files to UTF-8 (PHP/HTML/CSS/JS).
- `tools/php_lint.sh` — run `php -l` on all PHP files under `Archive 1/`.
- `tools/check_no_mysql_ext.sh` — fails if `mysql_*` usages remain.
- `tools/smoke_checks.php` — placeholder smoke test for `api/search.php` JSON response.

Run from repo root, e.g.:
```bash
./tools/php_lint.sh
./tools/check_no_mysql_ext.sh
php tools/convert_project_to_utf8.php
php tools/smoke_checks.php http://localhost:8000/api/search.php
```

## Configuration modules
- `Archive 1/inc/env.php` — loads `.env`/`config.local.php`.
- `Archive 1/inc/db.php` — PDO connection with UTF-8 (utf8mb4), exceptions, and associative fetch mode.
- `Archive 1/inc/csrf.php` — CSRF token generation/validation (starts session if needed).
- `Archive 1/inc/cache.php` — simple file cache helper using `PAGE_CACHE_TTL`.
- `Archive 1/inc/images.php` — WebP conversion helper (Imagick preferred, GD fallback).

These modules are the foundation for replacing legacy `mysql_*` calls with PDO and modernizing security.
