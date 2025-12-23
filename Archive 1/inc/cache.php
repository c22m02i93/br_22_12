<?php

declare(strict_types=1);

require_once __DIR__ . '/env.php';

function getCacheTtl(): int
{
    return (int) env('CACHE_TTL_SECONDS', 300);
}

function getCacheDirectory(): string
{
    $dir = env('CACHE_DIR', __DIR__ . '/../cache');
    return rtrim($dir, '/');
}

function cacheRemember(string $key, callable $resolver, ?int $ttlSeconds = null): mixed
{
    $ttlSeconds ??= getCacheTtl();
    $dir = getCacheDirectory();
    $prefix = (string) env('CACHE_PREFIX', '');

    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        throw new RuntimeException(sprintf('Unable to create cache directory at %s', $dir));
    }

    $safeKey = preg_replace('/[^a-zA-Z0-9_-]/', '_', $key);
    $path = $dir . '/' . $prefix . $safeKey . '.cache';

    if (is_readable($path)) {
        $payload = json_decode((string) file_get_contents($path), true);
        if (is_array($payload) && isset($payload['expires'], $payload['value']) && $payload['expires'] >= time()) {
            return $payload['value'];
        }
    }

    $value = $resolver();

    $payload = [
        'expires' => time() + $ttlSeconds,
        'value'   => $value,
    ];

    file_put_contents($path, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

    return $value;
}
