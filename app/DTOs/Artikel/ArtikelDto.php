<?php

declare(strict_types=1);

namespace App\DTOs\Artikel;

use App\Enums\PublishStatus;
use CodeIgniter\I18n\Time;

readonly class ArtikelDto
{
    public function __construct(
        public string $judul,
        public string $slug,
        public string $kategori,
        public ?string $konten,
        public PublishStatus $status,
        public ?Time $tanggalTerbit,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toModelData(): array
    {
        return [
            'judul'          => $this->judul,
            'slug'           => $this->slug,
            'kategori'       => $this->kategori,
            'konten'         => $this->konten,
            'status'         => $this->status->value,
            'tanggal_terbit' => $this->tanggalTerbit?->toDateTimeString(),
        ];
    }
}
