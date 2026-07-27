<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\Shared\ContentListFilterDto;
use App\DTOs\Shared\PaginatedResultDto;
use App\Entities\Artikel;
use App\Enums\PublishStatus;
use App\Models\ArtikelModel;
use CodeIgniter\Pager\PagerInterface;

class ArtikelRepository extends BaseRepository
{
    public function __construct(ArtikelModel $model)
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

        /** @var list<Artikel> $items */
        $items = $builder->paginate($filter->perPage, 'default', $filter->page);

        /** @var PagerInterface $pager */
        $pager = $this->model->pager;

        return new PaginatedResultDto(items: $items, pager: $pager);
    }

    /**
     * @return list<Artikel>
     */
    public function findLatestPublished(?string $kategori, int $limit): array
    {
        $builder = $this->model
            ->select('id, judul, slug, kategori, konten, tanggal_terbit, created_at')
            ->where('status', PublishStatus::Terbit->value)
            ->orderBy('tanggal_terbit', 'DESC')
            ->orderBy('id', 'DESC');

        if ($kategori !== null && $kategori !== '') {
            $builder->where('kategori', $kategori);
        }

        /** @var list<Artikel> */
        return $builder->findAll($limit);
    }

    public function findBySlug(string $slug): ?Artikel
    {
        /** @var Artikel|null */
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
