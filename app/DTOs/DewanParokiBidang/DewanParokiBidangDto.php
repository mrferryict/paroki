<?php

declare(strict_types=1);

namespace App\DTOs\DewanParokiBidang;

readonly class DewanParokiBidangDto
{
    public function __construct(
        public string $kode,
        public string $namaTampilan,
        public ?string $deskripsi,
        public string $icon,
        public int $urutan,
    ) {}

    /**
     * @return array<string, int|string|null>
     */
    public function toModelData(): array
    {
        return [
            'kode'          => $this->kode,
            'nama_tampilan' => $this->namaTampilan,
            'deskripsi'     => $this->deskripsi,
            'icon'          => $this->icon,
            'urutan'        => $this->urutan,
        ];
    }
}
