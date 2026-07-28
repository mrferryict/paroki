<?php

declare(strict_types=1);

namespace App\DTOs\Artikel;

readonly class ArtikelKategoriDto
{
    public function __construct(
        public string $slug,
        public string $label,
        public int $urutan,
        public bool $isActive,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toModelData(): array
    {
        return [
            'slug'      => $this->slug,
            'label'     => $this->label,
            'urutan'    => $this->urutan,
            'is_active' => $this->isActive ? 1 : 0,
        ];
    }
}
