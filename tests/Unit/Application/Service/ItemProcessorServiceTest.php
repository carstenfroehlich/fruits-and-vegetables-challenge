<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Service;

use App\Application\Service\CollectionService;
use App\Application\Service\ItemProcessorService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class ItemProcessorServiceTest extends TestCase
{
    private ItemProcessorService $service;
    private CollectionService $collectionService;
    private string $tempDir;

    protected function setUp(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        // Temporäres Verzeichnis für Tests
        $this->tempDir = sys_get_temp_dir() . "/test_" . uniqid();
        mkdir($this->tempDir, 0777, true);

        // CollectionService mit projectDir erstellen
        $this->collectionService = new CollectionService($this->tempDir);
        $this->service = new ItemProcessorService($validator, $this->collectionService);
    }

    protected function tearDown(): void
    {
        // Rekursiv Verzeichnis löschen
        $this->deleteDirectory($this->tempDir);
    }

    private function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            if ($item->isDir()) {
                rmdir($item->getRealPath());
            } else {
                unlink($item->getRealPath());
            }
        }

        rmdir($dir);
    }

    public function testProcessItems(): void
    {
        $items = [
            [
                "id" => 1,
                "name" => "Apple",
                "type" => "fruit",
                "quantity" => 1000,
                "unit" => "g"
            ],
            [
                "id" => 2,
                "name" => "Carrot",
                "type" => "vegetable",
                "quantity" => 2,
                "unit" => "kg"
            ]
        ];

        $this->service->processItems($items);

        $fruitCollection = $this->collectionService->getFruitCollection();
        $vegetableCollection = $this->collectionService->getVegetableCollection();

        $this->assertNotNull($fruitCollection);
        $this->assertNotNull($vegetableCollection);
        $this->assertEquals(1, $fruitCollection->count());
        $this->assertEquals(1, $vegetableCollection->count());
    }

    public function testProcessItemsWithInvalidData(): void
    {
        $this->expectException(\App\Exception\ValidationException::class);

        $items = [
            [
                "id" => 1,
                "name" => "",  // Invalid: empty name
                "type" => "fruit",
                "quantity" => 1000,
                "unit" => "g"
            ]
        ];

        $this->service->processItems($items);
    }
}
