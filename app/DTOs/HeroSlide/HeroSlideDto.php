<?php

declare(strict_types=1);

namespace App\DTOs\HeroSlide;

readonly class HeroSlideDto
{
    public function __construct(
        public ?string $eyebrow,
        public string $judul,
        public ?string $subjudul,
        public ?string $cta1Label,
        public ?string $cta1Href,
        public ?string $cta2Label,
        public ?string $cta2Href,
        public string $gambar,
        public int $urutan,
        public bool $isActive,
    ) {}

    /**
     * @return array<string, bool|int|string|null>
     */
    public function toModelData(): array
    {
        return [
            'eyebrow'    => $this->eyebrow,
            'judul'      => $this->judul,
            'subjudul'   => $this->subjudul,
            'cta1_label' => $this->cta1Label,
            'cta1_href'  => $this->cta1Href,
            'cta2_label' => $this->cta2Label,
            'cta2_href'  => $this->cta2Href,
            'gambar'     => $this->gambar,
            'urutan'     => $this->urutan,
            'is_active'  => $this->isActive ? 1 : 0,
        ];
    }
}
