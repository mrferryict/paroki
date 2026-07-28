<?php

declare(strict_types=1);

namespace App\DTOs\Wilayah;

readonly class LingkunganAdminRowDto
{
    public function __construct(
        public int $id,
        public int $wilayahId,
        public string $nama,
        public string $ketuaNama,
        public ?string $ketuaKontak,
    ) {}
}
