<?php

declare(strict_types=1);

namespace App\DTOs\Pendaftaran;

readonly class PendaftaranListFilterDto
{
    public function __construct(
        public ?string $status = null,
        public int $page = 1,
        public int $perPage = 15,
    ) {}
}
