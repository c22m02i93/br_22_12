<?php

declare(strict_types=1);

/**
 * Minimal smoke checks placeholder.
 * Extend once endpoints (e.g., api/search.php) are migrated to PDO/UTF-8.
 */

$baseUrl = $argv[1] ?? 'http://localhost:8000/api/search.php';

echo "Checking $baseUrl ...\n";
$response = @file_get_contents($baseUrl);
if ($response === false) {
    echo "Warning: cannot fetch $baseUrl\n";
    exit(1);
}

$data = json_decode($response, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "Invalid JSON response\n";
    exit(1);
}

echo "OK: JSON decoded, keys: " . implode(', ', array_keys($data)) . "\n";
