<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface RepositoryInterface
{
    public function find(int|string $id): ?object;

    /**
     * @param array<string, mixed> $filters
     *
     * @return list<object>
     */
    public function findAll(array $filters = [], int $perPage = 0, int $page = 1): array;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): int|string|false;

    /**
     * @param array<string, mixed> $data
     */
    public function update(int|string $id, array $data): bool;

    public function delete(int|string $id): bool;
}
