<?php

declare(strict_types=1);

/**
 * Simple .env loader for the legacy project structure.
 */
function loadEnv(?string $path = null): void
{
    $defaultRootEnv = dirname(__DIR__, 2) . '/.env';
    $path = $path ?? $defaultRootEnv;

    if (!is_readable($path) && is_readable(dirname(__DIR__, 1) . '/.env')) {
        // Fallback to legacy location inside \"Archive 1\" if present
        $path = dirname(__DIR__, 1) . '/.env';
    }

    if (!is_readable($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $trimmed = trim($line);

        if ($trimmed === '' || strncmp($trimmed, '#', 1) === 0) {
            continue;
        }

        [$name, $value] = array_pad(explode('=', $trimmed, 2), 2, '');
        $name = trim($name);
        $value = trim($value, " \"'\t");

        if ($name === '') {
            continue;
        }

        $_ENV[$name] = $value;
        putenv($name . '=' . $value);
    }
}

function env(string $key, mixed $default = null): mixed
{
    if (array_key_exists($key, $_ENV)) {
        return $_ENV[$key];
    }

    $value = getenv($key);
    if ($value !== false) {
        return $value;
    }

    return $default;
}

loadEnv();
