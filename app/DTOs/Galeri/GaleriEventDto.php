<?php

declare(strict_types=1);

namespace App\DTOs\Galeri;

readonly class GaleriEventDto
{
    public function __construct(
        public string $judul,
        public string $slug,
        public int $urutan,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toModelData(): array
    {
        return [
            'judul'  => $this->judul,
            'slug'   => $this->slug,
            'urutan' => $this->urutan,
        ];
    }
}
