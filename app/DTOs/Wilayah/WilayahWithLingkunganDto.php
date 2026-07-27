<?php

declare(strict_types=1);

namespace App\DTOs\Wilayah;

use App\Entities\Lingkungan;
use App\Entities\Wilayah;

readonly class WilayahWithLingkunganDto
{
    /**
     * @param list<Lingkungan> $lingkungan
     */
    public function __construct(
        public Wilayah $wilayah,
        public array $lingkungan,
    ) {}
}
