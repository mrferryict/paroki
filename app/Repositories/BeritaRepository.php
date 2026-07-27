<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\Shared\ContentListFilterDto;
use App\DTOs\Shared\PaginatedResultDto;
use App\Entities\Berita;
use App\Models\BeritaModel;
use CodeIgniter\Pager\PagerInterface;

class BeritaRepository extends BaseRepository
{
    public function __construct(BeritaModel $model)
    {
        parent::__construct($model);
    }

    public function findPaginated(ContentListFilterDto $filter): PaginatedResultDto
    {
        $builder = $this->model
            ->select('id, judul, slug, kategori, status, tanggal_terbit, created_at')
            ->orderBy('tanggal_terbit', 'DESC')
            ->orderBy('id', 'DESC');

        if ($filter->kategori !== null && $filter->kategori !== '') {
            $builder->where('kategori', $filter->kategori);
        }

        if ($filter->status !== null && $filter->status !== '') {
            $builder->where('status', $filter->status);
        }

        /** @var list<Berita> $items */
        $items = $builder->paginate($filter->perPage, 'default', $filter->page);

        /** @var PagerInterface $pager */
        $pager = $this->model->pager;

        return new PaginatedResultDto(items: $items, pager: $pager);
    }

    public function findBySlug(string $slug): ?Berita
    {
        /** @var Berita|null */
        return $this->model->where('slug', $slug)->first();
    }

    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $builder = $this->model->where('slug', $slug);

        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }
}
