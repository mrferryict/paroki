<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Repositories\Contracts\RepositoryInterface;
use CodeIgniter\Model;

abstract class BaseRepository implements RepositoryInterface
{
    public function __construct(protected Model $model) {}

    public function find(int|string $id): ?object
    {
        return $this->model->find($id);
    }

    public function findAll(array $filters = [], int $perPage = 0, int $page = 1): array
    {
        $builder = $this->model;

        foreach ($filters as $field => $value) {
            $builder = $builder->where($field, $value);
        }

        if ($perPage > 0) {
            /** @var list<object> */
            return $builder->paginate($perPage, 'default', $page);
        }

        /** @var list<object> */
        return $builder->findAll();
    }

    public function create(array $data): int|string|false
    {
        return $this->model->insert($data);
    }

    public function update(int|string $id, array $data): bool
    {
        return $this->model->update($id, $data);
    }

    public function delete(int|string $id): bool
    {
        return $this->model->delete($id);
    }
}
