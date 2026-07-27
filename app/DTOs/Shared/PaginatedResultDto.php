<?php

declare(strict_types=1);

namespace App\DTOs\Shared;

use CodeIgniter\Pager\PagerInterface;

readonly class PaginatedResultDto
{
    /**
     * @param list<object> $items
     */
    public function __construct(
        public array $items,
        public PagerInterface $pager,
    ) {}
}
