<?php

declare(strict_types=1);

namespace App\Domain\Collection;

use App\Domain\Model\Item;

interface ItemCollectionInterface
{
    public function add(Item $item): void;
    public function remove(int $id): void;
    public function list(): array;
    public function search(string $query): array;
    public function get(int $id): ?Item;
    public function count(): int;
}