<?php

declare(strict_types=1);

namespace App\DTOs\Pendaftaran;

use App\Enums\PendaftaranStatus;
use CodeIgniter\I18n\Time;

readonly class PendaftaranListItemDto
{
    public function __construct(
        public int $id,
        public string $namaLengkap,
        public ?string $sakramenNama,
        public PendaftaranStatus $status,
        public Time $createdAt,
    ) {}
}
