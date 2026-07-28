<?php

declare(strict_types=1);

namespace App\DTOs\DewanParokiBidang;

readonly class DewanParokiBidangAdminRowDto
{
    /**
     * @param list<DewanParokiPenjabatAdminRowDto> $penjabat
     */
    public function __construct(
        public int $id,
        public string $kode,
        public string $kodeLabel,
        public string $nama,
        public ?string $deskripsi,
        public string $icon,
        public int $urutan,
        public array $penjabat,
    ) {}
}
