# Barysh Legacy Site

This repository contains the archived PHP code for the Barysh eparchy web site. The codebase is being modernised incrementally: configuration now comes from environment variables, PDO is used for new database access, and helper tools ease encoding and health checks.

## Configuration

1. Copy `.env.example` to `.env` and adjust values:
   - Database: `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `DB_CHARSET`, `DB_COLLATION`.
   - Cache: `CACHE_DIR`, `CACHE_TTL_SECONDS`, `CACHE_PREFIX`.
   - WebP conversion: `WEBP_ENABLED`, `WEBP_QUALITY`, `WEBP_MAX_DIMENSION`.
2. The environment file is loaded automatically from `Archive 1/inc/env.php`.

## Database migrations

Placeholder SQL migrations live in `db/migrations`:

- `001_utf8mb4.sql` – convert the schema to UTF-8/utf8mb4.
- `002_indexes.sql` – add supporting indexes for date filtering and search.

Apply them manually with your MySQL/MariaDB client when ready, adjusting database and table names to match production.

## Tooling

Utility scripts live under `tools/` and are executable:

- `tools/php_lint.sh` – run `php -l` across all PHP files inside `Archive 1`.
- `tools/check_no_mysql_ext.sh` – fails if legacy `mysql_*` calls are still present.
- `tools/convert_project_to_utf8.php` – recodes project files to UTF-8 without BOM and reports conversions.
- `tools/smoke_checks.php` – stub smoke test; fetches `api/search.php` (configure `SMOKE_SEARCH_URL`) and ensures JSON decodes.

## Development notes

- Central environment loading and PDO setup live in `Archive 1/inc/env.php` and `Archive 1/inc/db.php`.
- CSRF helpers, cache scaffolding, and WebP conversion helpers are in `Archive 1/inc/`.
- Legacy includes `Archive 1/init.php` and `Archive 1/core.php` now rely on PDO; future work should continue replacing `mysql_*` usage.
