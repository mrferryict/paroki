<?php

declare(strict_types=1);

namespace App\DTOs\Galeri;

readonly class GaleriDto
{
    public function __construct(
        public string $filePath,
        public ?string $caption,
        public int $urutan,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toModelData(): array
    {
        return [
            'file_path' => $this->filePath,
            'caption'   => $this->caption,
            'urutan'    => $this->urutan,
        ];
    }
}
