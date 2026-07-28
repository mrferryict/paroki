<?php

declare(strict_types=1);

namespace App\DTOs\DewanParokiBidang;

readonly class DewanParokiPenjabatDto
{
    public function __construct(
        public int $bidangId,
        public string $nama,
        public string $whatsapp,
    ) {}
}
