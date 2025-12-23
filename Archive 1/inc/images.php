<?php

declare(strict_types=1);

require_once __DIR__ . '/env.php';

function convertToWebpAndReplace(string $path, ?int $quality = null): bool
{
    if (!is_file($path)) {
        return false;
    }

    $enabled = filter_var(env('WEBP_ENABLED', 'true'), FILTER_VALIDATE_BOOLEAN);
    if ($enabled === false) {
        return false;
    }

    $quality ??= (int) env('WEBP_QUALITY', 85);

    $destination = $path . '.webp';

    if (class_exists('Imagick')) {
        $image = new Imagick($path);
        $image->setImageFormat('webp');
        $image->setImageCompressionQuality($quality);
        $image->writeImage($destination);
        $image->clear();
        $image->destroy();
        return rename($destination, $path);
    }

    if (function_exists('imagecreatefromstring') && function_exists('imagewebp')) {
        $content = file_get_contents($path);
        if ($content === false) {
            return false;
        }

        $resource = @imagecreatefromstring($content);
        if ($resource === false) {
            return false;
        }

        imagepalettetotruecolor($resource);
        imagealphablending($resource, true);
        imagesavealpha($resource, true);

        $result = imagewebp($resource, $destination, $quality);
        imagedestroy($resource);

        if ($result === false) {
            return false;
        }

        return rename($destination, $path);
    }

    // No supported image library found; leave file untouched for now.
    return false;
}
