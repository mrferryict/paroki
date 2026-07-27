<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\Wilayah\WilayahWithLingkunganDto;
use App\Entities\Lingkungan;
use App\Entities\Wilayah;
use App\Models\LingkunganModel;
use App\Models\WilayahModel;

class WilayahRepository extends BaseRepository
{
    public function __construct(WilayahModel $model)
    {
        parent::__construct($model);
    }

    /**
     * @return list<Wilayah>
     */
    public function findAllForList(): array
    {
        /** @var list<Wilayah> */
        return $this->model
            ->select('id, nama, ketua_nama, created_at, updated_at')
            ->orderBy('nama', 'ASC')
            ->findAll();
    }

    /**
     * Cross-domain read for admin detail: wilayah + daftar lingkungan tanpa kolom PII.
     * Exception §5.4 — satu query admin detail, join via LingkunganModel di method ini.
     */
    public function getWithLingkungan(int $wilayahId): ?WilayahWithLingkunganDto
    {
        $wilayah = $this->model->find($wilayahId);

        if ($wilayah === null) {
            return null;
        }

        $lingkunganModel = model(LingkunganModel::class);

        /** @var list<Lingkungan> $lingkungan */
        $lingkungan = $lingkunganModel
            ->select('id, wilayah_id, nama, ketua_nama, created_at, updated_at')
            ->where('wilayah_id', $wilayahId)
            ->orderBy('nama', 'ASC')
            ->findAll();

        return new WilayahWithLingkunganDto(
            wilayah: $wilayah,
            lingkungan: $lingkungan,
        );
    }
}
