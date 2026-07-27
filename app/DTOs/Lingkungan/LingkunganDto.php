<?php

declare(strict_types=1);

namespace App\DTOs\Lingkungan;

readonly class LingkunganDto
{
    public function __construct(
        public int $wilayahId,
        public string $nama,
        public string $ketuaNama,
        public ?string $ketuaKontak,
    ) {}
}
