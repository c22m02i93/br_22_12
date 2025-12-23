<?php

declare(strict_types=1);

require_once __DIR__ . '/env.php';

function getPdoConnection(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = env('DB_HOST', 'localhost');
    $port = (int) env('DB_PORT', 3306);
    $database = env('DB_DATABASE', '');
    $username = env('DB_USERNAME', '');
    $password = env('DB_PASSWORD', '');
    $charset = env('DB_CHARSET', 'utf8mb4');
    $collation = env('DB_COLLATION', 'utf8mb4_unicode_ci');

    if ($database === '') {
        throw new RuntimeException('Database name is not configured. Set DB_DATABASE in your environment.');
    }

    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $host, $port, $database, $charset);

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $pdo = new PDO($dsn, $username, $password, $options);

    // Align collation if provided
    if ($collation !== '') {
        $charsetSafe = preg_replace('/[^a-zA-Z0-9_]/', '', $charset);
        $collationSafe = preg_replace('/[^a-zA-Z0-9_]/', '', $collation);

        if ($charsetSafe !== '' && $collationSafe !== '') {
            $pdo->exec(sprintf('SET NAMES %s COLLATE %s', $charsetSafe, $collationSafe));
        }
    }

    return $pdo;
}
