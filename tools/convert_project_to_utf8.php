<?php

declare(strict_types=1);

$root = realpath(__DIR__ . '/..');
$skipped = [];
$converted = [];

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
);

foreach ($iterator as $fileInfo) {
    if (!$fileInfo->isFile()) {
        continue;
    }

    $path = $fileInfo->getPathname();

    if (str_contains($path, DIRECTORY_SEPARATOR . '.git' . DIRECTORY_SEPARATOR)) {
        continue;
    }

    $content = file_get_contents($path);
    if ($content === false) {
        $skipped[] = [$path, 'unreadable'];
        continue;
    }

    $detected = mb_detect_encoding($content, ['UTF-8', 'Windows-1251', 'CP1251', 'ISO-8859-5'], true);

    if ($detected === false) {
        $skipped[] = [$path, 'unknown encoding'];
        continue;
    }

    $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);

    if ($detected !== 'UTF-8') {
        $content = mb_convert_encoding($content, 'UTF-8', $detected);
        file_put_contents($path, $content);
        $converted[] = $path;
        continue;
    }

    if (!str_starts_with($content, "\xEF\xBB\xBF")) {
        // Already UTF-8 without BOM
        continue;
    }

    file_put_contents($path, $content);
}

echo "Converted files: " . count($converted) . PHP_EOL;
foreach ($converted as $file) {
    echo " - $file" . PHP_EOL;
}

echo "Skipped files: " . count($skipped) . PHP_EOL;
foreach ($skipped as [$file, $reason]) {
    echo " - $file ($reason)" . PHP_EOL;
}
