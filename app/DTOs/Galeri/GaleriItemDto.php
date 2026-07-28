<?php

declare(strict_types=1);

namespace App\DTOs\Galeri;

use App\Enums\GaleriJenis;

readonly class GaleriItemDto
{
    public function __construct(
        public int $galeriEventId,
        public GaleriJenis $jenis,
        public ?string $filePath,
        public ?string $youtubeUrl,
        public ?string $caption,
        public int $urutan,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toModelData(): array
    {
        return [
            'galeri_event_id' => $this->galeriEventId,
            'jenis'           => $this->jenis->value,
            'file_path'       => $this->filePath,
            'youtube_url'     => $this->youtubeUrl,
            'caption'         => $this->caption,
            'urutan'          => $this->urutan,
        ];
    }
}
