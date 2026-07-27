<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\Lingkungan;
use App\Models\LingkunganModel;

class LingkunganRepository extends BaseRepository
{
    public function __construct(LingkunganModel $model)
    {
        parent::__construct($model);
    }

    /**
     * @return list<Lingkungan>
     */
    public function findAllByWilayahIdForList(int $wilayahId): array
    {
        /** @var list<Lingkungan> */
        return $this->model
            ->select('id, wilayah_id, nama, ketua_nama, created_at, updated_at')
            ->where('wilayah_id', $wilayahId)
            ->orderBy('nama', 'ASC')
            ->findAll();
    }

    public function findByIdForDetail(int $id): ?Lingkungan
    {
        /** @var Lingkungan|null */
        return $this->model->find($id);
    }
}
