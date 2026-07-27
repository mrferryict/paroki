<?php

declare(strict_types=1);

namespace App\DTOs\Pendaftaran;

readonly class PendaftaranDto
{
    public function __construct(
        public string $namaLengkap,
        public string $whatsapp,
        public ?int $sakramenJenisId,
        public ?string $pesan,
    ) {}
}
