<?php

declare(strict_types=1);

namespace App\Libraries;

use RuntimeException;

final class PublicUploadDirectory
{
    /**
     * Ensures a directory under `public/` exists and is writable.
     *
     * @param non-empty-string $relativePath Path relative to `public/`, e.g. `uploads/berita`
     */
    public static function ensure(string $relativePath): string
    {
        $relativePath = trim($relativePath, '/');
        $targetDir    = rtrim(FCPATH, '/') . '/' . $relativePath;

        if (is_dir($targetDir)) {
            if (! is_writable($targetDir)) {
                throw new RuntimeException(
                    'Direktori unggahan tidak dapat ditulis: public/' . $relativePath
                    . '. Periksa izin folder (www-data / web server harus bisa menulis).',
                );
            }

            return $targetDir;
        }

        if (! @mkdir($targetDir, 0775, true) && ! is_dir($targetDir)) {
            throw new RuntimeException(
                'Direktori unggahan tidak dapat dibuat: public/' . $relativePath
                . '. Periksa izin folder public/uploads (www-data / web server harus bisa menulis).',
            );
        }

        return $targetDir;
    }
}
