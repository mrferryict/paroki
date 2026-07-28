<?php

declare(strict_types=1);

namespace App\DTOs\SakramenJenis;

readonly class SakramenJenisDto
{
    public function __construct(
        public string $kode,
        public string $grup,
        public string $nama,
        public ?string $deskripsi,
        public string $icon,
        public int $urutan,
        public bool $isActive,
    ) {}

    /**
     * @return array<string, bool|int|string|null>
     */
    public function toModelData(): array
    {
        return [
            'kode'      => $this->kode,
            'grup'      => $this->grup,
            'nama'      => $this->nama,
            'deskripsi' => $this->deskripsi,
            'icon'      => $this->icon,
            'urutan'    => $this->urutan,
            'is_active' => $this->isActive ? 1 : 0,
        ];
    }
}
