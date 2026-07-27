<?php

declare(strict_types=1);

namespace App\DTOs\Pendaftaran;

use App\Entities\Pendaftaran;

readonly class PendaftaranDetailDto
{
    public function __construct(
        public Pendaftaran $pendaftaran,
        public string $whatsapp,
        public ?string $sakramenNama,
    ) {}
}
