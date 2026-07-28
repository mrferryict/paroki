<?php

declare(strict_types=1);

namespace App\Libraries;

use CodeIgniter\Images\Exceptions\ImageException;
use RuntimeException;

final class ImageResizer
{
    public function resizeToMaxBox(string $fullPath, int $maxWidth = 1200, int $maxHeight = 900): void
    {
        if (! is_file($fullPath)) {
            throw new RuntimeException('Berkas gambar tidak ditemukan.');
        }

        try {
            $image = \Config\Services::image()->withFile($fullPath);
        } catch (ImageException $exception) {
            throw new RuntimeException('Gambar tidak dapat diproses: ' . $exception->getMessage(), previous: $exception);
        }

        $width  = $image->getWidth();
        $height = $image->getHeight();

        if ($width <= $maxWidth && $height <= $maxHeight) {
            return;
        }

        $image->resize($maxWidth, $maxHeight, true, 'auto')->save($fullPath);
    }
}
