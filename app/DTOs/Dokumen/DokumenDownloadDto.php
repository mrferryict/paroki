<?php

declare(strict_types=1);

namespace App\DTOs\Dokumen;

readonly class DokumenDownloadDto
{
    public function __construct(
        public string $fullPath,
        public string $clientName,
    ) {}
}
