<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage;

use App\Domain\Collection\FruitCollection;
use App\Domain\Collection\VegetableCollection;

final class FileStorage
{
    private string $storagePath;

    public function __construct(string $projectDir)
    {
        $this->storagePath = $projectDir . '/var/storage';
        if (!is_dir($this->storagePath)) {
            mkdir($this->storagePath, 0777, true);
        }
    }

    public function saveFruitCollection(FruitCollection $collection): void
    {
        file_put_contents(
            $this->storagePath . '/fruits.json',
            serialize($collection)
        );
    }

    public function saveVegetableCollection(VegetableCollection $collection): void
    {
        file_put_contents(
            $this->storagePath . '/vegetables.json',
            serialize($collection)
        );
    }

    public function getFruitCollection(): ?FruitCollection
    {
        $file = $this->storagePath . '/fruits.json';
        if (file_exists($file)) {
            return unserialize(file_get_contents($file));
        }
        return null;
    }

    public function getVegetableCollection(): ?VegetableCollection
    {
        $file = $this->storagePath . '/vegetables.json';
        if (file_exists($file)) {
            return unserialize(file_get_contents($file));
        }
        return null;
    }
}
