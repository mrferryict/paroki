<?php

declare(strict_types=1);

namespace App\DTOs\Berita;

use App\Enums\BeritaKategori;
use App\Enums\PublishStatus;
use CodeIgniter\I18n\Time;

readonly class BeritaDto
{
    public function __construct(
        public string $judul,
        public string $slug,
        public BeritaKategori $kategori,
        public ?string $ringkasan,
        public ?string $konten,
        public ?string $gambarUtama,
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
            'kategori'       => $this->kategori->value,
            'ringkasan'      => $this->ringkasan,
            'konten'         => $this->konten,
            'gambar_utama'   => $this->gambarUtama,
            'status'         => $this->status->value,
            'tanggal_terbit' => $this->tanggalTerbit?->toDateTimeString(),
        ];
    }
}
