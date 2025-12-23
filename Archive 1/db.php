<?php

declare(strict_types=1);

require_once __DIR__ . '/inc/env.php';
require_once __DIR__ . '/inc/db.php';

$pdo = getPdoConnection();
$GLOBALS['pdo'] = $pdo;
$GLOBALS['db'] = $pdo;

/**
 * Lightweight compatibility wrapper that emulates the mysql_* API on top of PDO.
 * This keeps legacy code running while new code can interact with PDO directly.
 */
class LegacyPdoResult
{
    private array $rows = [];
    private int $position = 0;

    public function __construct(PDOStatement $statement)
    {
        if ($statement->columnCount() > 0) {
            $this->rows = $statement->fetchAll(PDO::FETCH_BOTH);
        }
    }

    public function fetchAssoc(): array|false
    {
        if (!isset($this->rows[$this->position])) {
            return false;
        }

        $row = $this->rows[$this->position++];
        $assoc = [];
        foreach ($row as $key => $value) {
            if (is_string($key)) {
                $assoc[$key] = $value;
            }
        }

        return $assoc;
    }

    public function fetchArray(): array|false
    {
        if (!isset($this->rows[$this->position])) {
            return false;
        }

        return $this->rows[$this->position++];
    }

    public function fetchRow(): array|false
    {
        if (!isset($this->rows[$this->position])) {
            return false;
        }

        $row = $this->rows[$this->position++];

        return array_values(array_filter($row, static fn($key) => is_int($key), ARRAY_FILTER_USE_KEY));
    }

    public function numRows(): int
    {
        return count($this->rows);
    }

    public function result(int $row = 0, int|string $field = 0): mixed
    {
        return $this->rows[$row][$field] ?? false;
    }
}

function mysql_connect(?string $host = null, ?string $user = null, ?string $password = null): PDO
{
    return getPdoConnection();
}

function mysql_select_db(string $database, ?PDO $connection = null): bool
{
    // Database is configured via DSN; provided for compatibility
    return true;
}

function mysql_query(string $query, ?PDO $connection = null): LegacyPdoResult
{
    $pdo = $connection instanceof PDO ? $connection : getPdoConnection();
    $statement = $pdo->query($query);

    return new LegacyPdoResult($statement);
}

function mysql_fetch_assoc(LegacyPdoResult $result): array|false
{
    return $result->fetchAssoc();
}

function mysql_fetch_array(LegacyPdoResult $result): array|false
{
    return $result->fetchArray();
}

function mysql_fetch_row(LegacyPdoResult $result): array|false
{
    return $result->fetchRow();
}

function mysql_num_rows(LegacyPdoResult $result): int
{
    return $result->numRows();
}

function mysql_result(LegacyPdoResult $result, int $row = 0, int|string $field = 0): mixed
{
    return $result->result($row, $field);
}

function mysql_real_escape_string(string $string, ?PDO $connection = null): string
{
    $pdo = $connection instanceof PDO ? $connection : getPdoConnection();
    return substr($pdo->quote($string), 1, -1);
}

function mysql_error(?PDO $connection = null): string
{
    $pdo = $connection instanceof PDO ? $connection : getPdoConnection();
    $info = $pdo->errorInfo();
    return $info[2] ?? '';
}
