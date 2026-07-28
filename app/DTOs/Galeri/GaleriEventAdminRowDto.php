<?php

declare(strict_types=1);

namespace App\DTOs\Galeri;

readonly class GaleriEventAdminRowDto
{
    /**
     * @param list<GaleriItemAdminRowDto> $items
     */
    public function __construct(
        public int $id,
        public string $judul,
        public string $slug,
        public int $urutan,
        public int $viewCount,
        public array $items,
    ) {}
}
