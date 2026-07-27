<?php

declare(strict_types=1);

namespace App\DTOs\Wilayah;

use App\Entities\Lingkungan;
use App\Entities\Wilayah;

readonly class WilayahDetailDto
{
    /**
     * @param list<Lingkungan> $lingkungan
     */
    public function __construct(
        public Wilayah $wilayah,
        public string $ketuaKontak,
        public array $lingkungan,
    ) {}
}
