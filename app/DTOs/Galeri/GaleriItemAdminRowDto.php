<?php

declare(strict_types=1);

namespace App\DTOs\Galeri;

readonly class GaleriItemAdminRowDto
{
    public function __construct(
        public int $id,
        public int $galeriEventId,
        public string $jenis,
        public string $jenisLabel,
        public ?string $filePath,
        public ?string $youtubeUrl,
        public ?string $caption,
        public int $urutan,
        public ?string $previewUrl,
    ) {}
}
