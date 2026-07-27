<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\Pendaftaran\PaginatedPendaftaranListDto;
use App\DTOs\Pendaftaran\PendaftaranListFilterDto;
use App\DTOs\Pendaftaran\PendaftaranListItemDto;
use App\Entities\Pendaftaran;
use App\Enums\PendaftaranStatus;
use App\Models\PendaftaranModel;
use CodeIgniter\I18n\Time;
use CodeIgniter\Pager\PagerInterface;

class PendaftaranRepository extends BaseRepository
{
    public function __construct(PendaftaranModel $model)
    {
        parent::__construct($model);
    }

    public function findPaginatedForAdmin(PendaftaranListFilterDto $filter): PaginatedPendaftaranListDto
    {
        $builder = $this->model
            ->select([
                'pendaftaran.id',
                'pendaftaran.nama_lengkap',
                'pendaftaran.status',
                'pendaftaran.created_at',
                'sakramen_jenis.nama AS sakramen_nama',
            ])
            ->join('sakramen_jenis', 'sakramen_jenis.id = pendaftaran.sakramen_jenis_id', 'left')
            ->orderBy('pendaftaran.created_at', 'DESC')
            ->orderBy('pendaftaran.id', 'DESC');

        if ($filter->status !== null && $filter->status !== '') {
            $builder->where('pendaftaran.status', $filter->status);
        }

        $rows = $builder->paginate($filter->perPage, 'default', $filter->page);

        $items = [];

        foreach ($rows as $row) {
            $items[] = new PendaftaranListItemDto(
                id: (int) $row->id,
                namaLengkap: (string) $row->nama_lengkap,
                sakramenNama: $row->sakramen_nama !== null ? (string) $row->sakramen_nama : null,
                status: $row->status instanceof PendaftaranStatus
                    ? $row->status
                    : PendaftaranStatus::from((string) $row->status),
                createdAt: $row->created_at instanceof Time
                    ? $row->created_at
                    : Time::parse((string) $row->created_at),
            );
        }

        /** @var PagerInterface $pager */
        $pager = $this->model->pager;

        return new PaginatedPendaftaranListDto(items: $items, pager: $pager);
    }

    public function findByIdForDetail(int $id): ?Pendaftaran
    {
        /** @var Pendaftaran|null */
        return $this->model
            ->select([
                'pendaftaran.id',
                'pendaftaran.nama_lengkap',
                'pendaftaran.whatsapp_cipher',
                'pendaftaran.whatsapp_hash',
                'pendaftaran.sakramen_jenis_id',
                'pendaftaran.pesan',
                'pendaftaran.status',
                'pendaftaran.created_at',
                'pendaftaran.updated_at',
                'sakramen_jenis.nama AS sakramen_nama',
            ])
            ->join('sakramen_jenis', 'sakramen_jenis.id = pendaftaran.sakramen_jenis_id', 'left')
            ->where('pendaftaran.id', $id)
            ->first();
    }

    /**
     * Resolve sakramen label stored on joined detail row.
     */
    public function resolveSakramenNamaFromEntity(Pendaftaran $pendaftaran): ?string
    {
        $nama = $pendaftaran->sakramen_nama ?? null;

        return $nama !== null && $nama !== '' ? (string) $nama : null;
    }
}
