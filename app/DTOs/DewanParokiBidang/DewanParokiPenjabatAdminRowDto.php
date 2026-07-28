<?php

declare(strict_types=1);

namespace App\DTOs\DewanParokiBidang;

readonly class DewanParokiPenjabatAdminRowDto
{
    public function __construct(
        public int $id,
        public string $nama,
        public string $whatsapp,
    ) {}
}
