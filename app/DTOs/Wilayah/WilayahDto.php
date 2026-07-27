<?php

declare(strict_types=1);

namespace App\DTOs\Wilayah;

readonly class WilayahDto
{
    public function __construct(
        public string $nama,
        public string $ketuaNama,
        public string $ketuaKontak,
    ) {}
}
