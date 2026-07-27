<?php

declare(strict_types=1);

namespace App\DTOs\Dokumen;

readonly class DokumenDto
{
    public function __construct(
        public string $nama,
        public string $filePath,
        public string $kategori,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toModelData(): array
    {
        return [
            'nama'      => $this->nama,
            'file_path' => $this->filePath,
            'kategori'  => $this->kategori,
        ];
    }
}
