<?php

declare(strict_types=1);

namespace App\DTOs\Shared;

readonly class ContentListFilterDto
{
    public function __construct(
        public ?string $kategori = null,
        public ?string $status = null,
        public int $page = 1,
        public int $perPage = 10,
    ) {}
}
