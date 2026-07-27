<?php

declare(strict_types=1);

namespace App\DTOs\Pendaftaran;

use CodeIgniter\Pager\PagerInterface;

readonly class PaginatedPendaftaranListDto
{
    /**
     * @param list<PendaftaranListItemDto> $items
     */
    public function __construct(
        public array $items,
        public PagerInterface $pager,
    ) {}
}
