<?php

declare(strict_types=1);

namespace App\Services;

class ImageService
{
    private const MAX_WIDTH = 800;
    private const JPEG_QUALITY = 85;

    /**
     * Redimensionne et compresse une image avant encodage éventuel en base64.
     *
     * @return string Binary JPEG data
     */
    public function optimizeImage(string $sourcePath): string
    {
        [$width, $height, $type] = getimagesize($sourcePath);

        $ratio = $width / $height;
        $newWidth = min($width, self::MAX_WIDTH);
        $newHeight = (int) ($newWidth / $ratio);

        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        $sourceImage = match ($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($sourcePath),
            IMAGETYPE_PNG => imagecreatefrompng($sourcePath),
            IMAGETYPE_WEBP => imagecreatefromwebp($sourcePath),
            default => throw new \Exception('Format d\'image non supporté'),
        };

        imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        ob_start();
        imagejpeg($newImage, null, self::JPEG_QUALITY);
        $imageData = ob_get_clean();

        imagedestroy($sourceImage);
        imagedestroy($newImage);

        return $imageData;
    }
}
