<?php

declare(strict_types=1);

namespace App\DTOs\JadwalMisa;

readonly class JadwalMisaDto
{
    public function __construct(
        public string $jenis,
        public string $hariLabel,
        public string $jam,
        public ?string $catatan,
        public int $urutan,
        public bool $isActive,
    ) {}

    /**
     * @return array<string, bool|int|string|null>
     */
    public function toModelData(): array
    {
        return [
            'jenis'      => $this->jenis,
            'hari_label' => $this->hariLabel,
            'jam'        => $this->jam,
            'catatan'    => $this->catatan,
            'urutan'     => $this->urutan,
            'is_active'  => $this->isActive ? 1 : 0,
        ];
    }
}
