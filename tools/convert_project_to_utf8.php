<?php

declare(strict_types=1);

$root = realpath(__DIR__ . '/../Archive 1');
if (!$root) {
    fwrite(STDERR, "Project root not found\n");
    exit(1);
}

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
);

$converted = [];
$skipped = [];

foreach ($files as $file) {
    /** @var SplFileInfo $file */
    if (!$file->isFile()) {
        continue;
    }
    $ext = strtolower($file->getExtension());
    if (!in_array($ext, ['php', 'html', 'htm', 'css', 'js'], true)) {
        continue;
    }

    $path = $file->getRealPath();
    if ($path === false) {
        continue;
    }

    $content = file_get_contents($path);
    if ($content === false) {
        $skipped[] = $path;
        continue;
    }

    $encoding = mb_detect_encoding($content, ['UTF-8', 'Windows-1251', 'ISO-8859-1'], true);
    if ($encoding === 'UTF-8') {
        continue;
    }

    $convertedContent = mb_convert_encoding($content, 'UTF-8', $encoding ?: 'UTF-8');
    file_put_contents($path, $convertedContent);
    $converted[] = $path;
}

echo "Converted to UTF-8:\n";
foreach ($converted as $path) {
    echo " - $path\n";
}

if ($skipped) {
    echo "\nSkipped (read error):\n";
    foreach ($skipped as $path) {
        echo " - $path\n";
    }
}
