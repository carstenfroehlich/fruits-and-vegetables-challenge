<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\Item;

interface ItemRepositoryInterface
{
    public function save(Item $item): void;
    public function remove(int $id): void;
    public function find(int $id): ?Item;
    public function findAll(): array;
    public function findByType(string $type): array;
}