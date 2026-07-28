<?php

declare(strict_types=1);

namespace App\DTOs\Wilayah;

readonly class WilayahAdminRowDto
{
    /**
     * @param list<LingkunganAdminRowDto> $lingkungan
     */
    public function __construct(
        public int $id,
        public string $nama,
        public string $koordinatorNama,
        public string $koordinatorKontak,
        public array $lingkungan,
    ) {}
}
