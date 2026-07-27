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
        /** @var Wilayah|null $wilayah */
        $wilayah = $this->model
            ->select('id, nama, ketua_nama, created_at, updated_at')
            ->find($wilayahId);

        if ($wilayah === null) {
            return null;
        }

        return new WilayahWithLingkunganDto(
            wilayah: $wilayah,
            lingkungan: $this->findLingkunganForWilayah($wilayahId),
        );
    }

    public function findForDetail(int $id): ?Wilayah
    {
        /** @var Wilayah|null */
        return $this->model->find($id);
    }

    /**
     * @return list<Lingkungan>
     */
    public function findLingkunganForWilayah(int $wilayahId): array
    {
        $lingkunganModel = model(LingkunganModel::class);

        /** @var list<Lingkungan> */
        return $lingkunganModel
            ->select('id, wilayah_id, nama, ketua_nama, created_at, updated_at')
            ->where('wilayah_id', $wilayahId)
            ->orderBy('nama', 'ASC')
            ->findAll();
    }

    /**
     * @return list<WilayahWithLingkunganDto>
     */
    public function findAllWithLingkunganForPublic(): array
    {
        /** @var list<Wilayah> $wilayahList */
        $wilayahList = $this->findAllForList();

        if ($wilayahList === []) {
            return [];
        }

        $wilayahIds = array_map(static fn (Wilayah $wilayah): int => (int) $wilayah->id, $wilayahList);

        $lingkunganModel = model(LingkunganModel::class);

        /** @var list<Lingkungan> $allLingkungan */
        $allLingkungan = $lingkunganModel
            ->select('id, wilayah_id, nama, ketua_nama, created_at, updated_at')
            ->whereIn('wilayah_id', $wilayahIds)
            ->orderBy('nama', 'ASC')
            ->findAll();

        /** @var array<int, list<Lingkungan>> $grouped */
        $grouped = [];

        foreach ($allLingkungan as $lingkungan) {
            $wilayahId = (int) $lingkungan->wilayah_id;
            $grouped[$wilayahId][] = $lingkungan;
        }

        $result = [];

        foreach ($wilayahList as $wilayah) {
            $wilayahId = (int) $wilayah->id;

            $result[] = new WilayahWithLingkunganDto(
                wilayah: $wilayah,
                lingkungan: $grouped[$wilayahId] ?? [],
            );
        }

        return $result;
    }
}
