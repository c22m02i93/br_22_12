<?php

declare(strict_types=1);

require_once __DIR__ . '/env.php';

function getCacheTtl(): int
{
    return (int) env('PAGE_CACHE_TTL', 0);
}

function getCacheDir(): string
{
    $dir = dirname(__DIR__) . '/cache';
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
    return $dir;
}

function getCachePath(string $key): string
{
    $safeKey = preg_replace('/[^A-Za-z0-9_\-]/', '_', $key);
    return getCacheDir() . '/' . $safeKey . '.cache';
}

function readPageCache(string $key): ?string
{
    $ttl = getCacheTtl();
    if ($ttl <= 0) {
        return null;
    }
    $path = getCachePath($key);
    if (!is_file($path)) {
        return null;
    }
    $age = time() - filemtime($path);
    if ($age > $ttl) {
        @unlink($path);
        return null;
    }
    $data = file_get_contents($path);
    return $data === false ? null : $data;
}

function writePageCache(string $key, string $content): void
{
    $ttl = getCacheTtl();
    if ($ttl <= 0) {
        return;
    }
    $path = getCachePath($key);
    file_put_contents($path, $content);
}

function clearPageCache(): void
{
    $dir = getCacheDir();
    if (!is_dir($dir)) {
        return;
    }
    foreach (glob($dir . '/*.cache') as $file) {
        @unlink($file);
    }
}
