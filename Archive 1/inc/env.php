<?php

declare(strict_types=1);

/**
 * Simple .env loader with optional config.local.php override.
 */

function loadEnvFile(string $path): array
{
    if (!is_file($path)) {
        return [];
    }

    $data = [];
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return [];
    }

    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || str_starts_with($trimmed, '#')) {
            continue;
        }
        $parts = explode('=', $trimmed, 2);
        if (count($parts) === 2) {
            $data[$parts[0]] = $parts[1];
        }
    }

    return $data;
}

function loadPhpConfig(string $path): array
{
    if (!is_file($path)) {
        return [];
    }

    $config = require $path;
    if (is_array($config)) {
        return $config;
    }

    return [];
}

function loadEnvConfig(): array
{
    static $cache = null;
    if (is_array($cache)) {
        return $cache;
    }

    $projectRoot = dirname(__DIR__, 2);
    $archiveRoot = dirname(__DIR__, 1);

    $envFiles = [
        $projectRoot . '/.env',
        $archiveRoot . '/.env',
    ];

    $configFiles = [
        $projectRoot . '/config.local.php',
        $archiveRoot . '/config.local.php',
    ];

    $config = [];
    foreach ($envFiles as $file) {
        $config = array_merge($config, loadEnvFile($file));
    }

    foreach ($configFiles as $file) {
        $config = array_merge($config, loadPhpConfig($file));
    }

    $cache = $config;

    return $cache;
}

function env(string $key, mixed $default = null): mixed
{
    $config = loadEnvConfig();
    return $config[$key] ?? $default;
}
