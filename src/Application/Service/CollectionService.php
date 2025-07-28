<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Dto\CollectionFilterDto;
use App\Domain\Collection\FruitCollection;
use App\Domain\Collection\ItemCollectionInterface;
use App\Domain\Collection\VegetableCollection;
use App\Domain\Model\Item;
use App\Infrastructure\Storage\FileStorage;

final class CollectionService
{
    private FileStorage $storage;

    public function __construct(string $projectDir)
    {
        $this->storage = new FileStorage($projectDir);
    }

    public function setFruitCollection(FruitCollection $collection): void
    {
        $this->storage->saveFruitCollection($collection);
    }

    public function setVegetableCollection(VegetableCollection $collection): void
    {
        $this->storage->saveVegetableCollection($collection);
    }

    public function getFruitCollection(): ?FruitCollection
    {
        $collection = $this->storage->getFruitCollection();
        return $collection ?: new FruitCollection();
    }

    public function getVegetableCollection(): ?VegetableCollection
    {
        $collection = $this->storage->getVegetableCollection();
        return $collection ?: new VegetableCollection();
    }

    public function getCollection(string $type): ?ItemCollectionInterface
    {
        return match ($type) {
            'fruit' => $this->getFruitCollection(),
            'vegetable' => $this->getVegetableCollection(),
            default => null,
        };
    }

    public function applyFilters(ItemCollectionInterface $collection, CollectionFilterDto $filters): array
    {
        $items = $collection->list();

        if ($filters->search !== null) {
            $items = array_filter(
                $items,
                fn(Item $item) => str_contains(
                    strtolower($item->getName()),
                    strtolower($filters->search)
                )
            );
        }

        if ($filters->minWeight !== null || $filters->maxWeight !== null) {
            $items = array_filter($items, function (Item $item) use ($filters) {
                $weight = $item->getWeight()->toGrams();

                if ($filters->minWeight !== null && $weight < $filters->minWeight) {
                    return false;
                }

                if ($filters->maxWeight !== null && $weight > $filters->maxWeight) {
                    return false;
                }

                return true;
            });
        }

        return array_values($items);
    }

    public function serializeItems(array $items, string $unit = 'g'): array
    {
        return array_map(
            fn(Item $item) => [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'type' => $item->getType(),
                'quantity' => $item->getWeight()->getValue($unit),
                'unit' => $unit,
            ],
            $items
        );
    }
}
