<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Model\Item;
use App\Domain\Repository\ItemRepositoryInterface;

final class InMemoryItemRepository implements ItemRepositoryInterface
{
    private array $items = [];
    
    public function save(Item $item): void
    {
        $this->items[$item->getId()] = $item;
    }
    
    public function remove(int $id): void
    {
        unset($this->items[$id]);
    }
    
    public function find(int $id): ?Item
    {
        return $this->items[$id] ?? null;
    }
    
    public function findAll(): array
    {
        return array_values($this->items);
    }
    
    public function findByType(string $type): array
    {
        return array_values(array_filter(
            $this->items,
            fn(Item $item) => $item->getType() === $type
        ));
    }
}