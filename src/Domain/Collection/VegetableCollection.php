<?php

declare(strict_types=1);

namespace App\Domain\Collection;

use App\Domain\Model\Item;
use App\Domain\Model\Vegetable;
use App\Exception\ValidationException;

final class VegetableCollection implements ItemCollectionInterface
{
    private array $items = [];
    
    public function add(Item $item): void
    {
        if (!$item instanceof Vegetable) {
            throw new ValidationException('Only vegetables can be added to VegetableCollection');
        }
        
        $this->items[$item->getId()] = $item;
    }
    
    public function remove(int $id): void
    {
        unset($this->items[$id]);
    }
    
    public function list(): array
    {
        return array_values($this->items);
    }
    
    public function search(string $query): array
    {
        $query = strtolower($query);
        
        return array_values(array_filter(
            $this->items,
            fn(Item $item) => str_contains(strtolower($item->getName()), $query)
        ));
    }
    
    public function get(int $id): ?Item
    {
        return $this->items[$id] ?? null;
    }
    
    public function count(): int
    {
        return count($this->items);
    }
}