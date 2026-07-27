<?php

declare(strict_types=1);

namespace App\DTOs\Lingkungan;

use App\Entities\Lingkungan;

readonly class LingkunganDetailDto
{
    public function __construct(
        public Lingkungan $lingkungan,
        public ?string $ketuaKontak,
    ) {}
}
