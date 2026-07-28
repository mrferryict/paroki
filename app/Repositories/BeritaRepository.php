<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\Shared\ContentListFilterDto;
use App\DTOs\Shared\PaginatedResultDto;
use App\Entities\Berita;
use App\Enums\PublishStatus;
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
            ->select('id, judul, slug, kategori, tags, ringkasan, gambar_utama, status, tanggal_terbit, view_count, created_at')
            ->orderBy('tanggal_terbit', 'DESC')
            ->orderBy('id', 'DESC');

        if ($filter->kategori !== null && $filter->kategori !== '') {
            $builder->where('kategori', $filter->kategori);
        }

        if ($filter->tag !== null && $filter->tag !== '') {
            $tag = strtolower(trim($filter->tag));
            $builder->groupStart()
                ->where("CONCAT(',', tags, ',') LIKE", '%,' . $this->model->db->escapeLikeString($tag) . ',%', false)
                ->groupEnd();
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

    /**
     * @return list<Berita>
     */
    public function findLatestPublished(int $limit): array
    {
        /** @var list<Berita> */
        return $this->model
            ->select('id, judul, slug, kategori, ringkasan, gambar_utama, tanggal_terbit, created_at')
            ->where('status', PublishStatus::Terbit->value)
            ->orderBy('tanggal_terbit', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll($limit);
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

    /**
     * @return list<string>
     */
    public function findPublishedTags(): array
    {
        /** @var list<Berita> $rows */
        $rows = $this->model
            ->select('tags')
            ->where('status', PublishStatus::Terbit->value)
            ->where('tags IS NOT NULL')
            ->where('tags !=', '')
            ->findAll();

        $tags = [];

        foreach ($rows as $row) {
            foreach (explode(',', (string) ($row->tags ?? '')) as $tag) {
                $tag = trim($tag);

                if ($tag !== '') {
                    $tags[$tag] = $tag;
                }
            }
        }

        ksort($tags);

        return array_values($tags);
    }

    public function incrementViewCount(int $id): void
    {
        $this->model
            ->where('id', $id)
            ->set('view_count', 'view_count + 1', false)
            ->update();
    }
}
