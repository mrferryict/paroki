<?php

declare(strict_types=1);

namespace App\Libraries;

class SlugGenerator
{
    /**
     * @param callable(string, ?int): bool $existsChecker
     */
    public function unique(string $judul, callable $existsChecker, ?int $excludeId = null): string
    {
        helper('text');

        $base = url_title($judul, '-', true);

        if ($base === '') {
            $base = 'item';
        }

        $slug  = $base;
        $index = 2;

        while ($existsChecker($slug, $excludeId)) {
            $slug = $base . '-' . $index;
            $index++;
        }

        return $slug;
    }
}
