<?php

declare(strict_types=1);

require_once __DIR__ . '/env.php';

function resizeImageGd($image, int $maxWidth, int $maxHeight): GdImage
{
    $width = imagesx($image);
    $height = imagesy($image);

    $ratio = min($maxWidth / $width, $maxHeight / $height, 1);
    if ($ratio >= 1) {
        return $image;
    }

    $newWidth = (int) floor($width * $ratio);
    $newHeight = (int) floor($height * $ratio);

    $resized = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    return $resized;
}

function convertToWebpAndReplace(string $srcPath): string
{
    $extension = strtolower(pathinfo($srcPath, PATHINFO_EXTENSION));
    if (!in_array($extension, ['jpg', 'jpeg', 'png'], true)) {
        return $srcPath;
    }

    $quality = (int) env('WEBP_QUALITY', 82);
    $maxWidth = (int) env('IMG_MAX_WIDTH', 2560);
    $maxHeight = (int) env('IMG_MAX_HEIGHT', 2560);
    $deleteOriginal = filter_var(env('DELETE_ORIGINALS', 'true'), FILTER_VALIDATE_BOOL);

    $targetPath = preg_replace('/\.(jpe?g|png)$/i', '.webp', $srcPath);
    if ($targetPath === null) {
        return $srcPath;
    }

    if (class_exists('Imagick')) {
        $image = new Imagick($srcPath);
        $image->setImageFormat('webp');
        if ($maxWidth > 0 && $maxHeight > 0) {
            $image->thumbnailImage($maxWidth, $maxHeight, true, true);
        }
        $image->setImageCompressionQuality($quality);
        $image->writeImage($targetPath);
        $image->clear();
    } elseif (function_exists('imagewebp')) {
        $data = file_get_contents($srcPath);
        if ($data === false) {
            return $srcPath;
        }
        $image = imagecreatefromstring($data);
        if (!$image) {
            return $srcPath;
        }
        if ($maxWidth > 0 && $maxHeight > 0) {
            $image = resizeImageGd($image, $maxWidth, $maxHeight);
        }
        imagewebp($image, $targetPath, $quality);
        imagedestroy($image);
    } else {
        return $srcPath;
    }

    if ($deleteOriginal && is_file($targetPath) && filesize($targetPath) > 0) {
        @unlink($srcPath);
    }

    return $targetPath;
}
