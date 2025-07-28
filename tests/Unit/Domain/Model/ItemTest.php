<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model;

use App\Domain\Model\Fruit;
use App\Domain\Model\Vegetable;
use App\Domain\ValueObject\Weight;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;

final class ItemTest extends TestCase
{
    #[Test]
    public function fruitCreation(): void
    {
        $weight = new Weight(1000, 'g');
        $fruit = new Fruit(1, 'Apple', $weight);

        $this->assertEquals(1, $fruit->getId());
        $this->assertEquals('Apple', $fruit->getName());
        $this->assertEquals(1000, $fruit->getWeight()->toGrams());
        $this->assertEquals('fruit', $fruit->getType());
    }

    #[Test]
    public function vegetableCreation(): void
    {
        $weight = new Weight(2, 'kg');
        $vegetable = new Vegetable(2, 'Carrot', $weight);

        $this->assertEquals(2, $vegetable->getId());
        $this->assertEquals('Carrot', $vegetable->getName());
        $this->assertEquals(2000, $vegetable->getWeight()->toGrams());
        $this->assertEquals('vegetable', $vegetable->getType());
    }

    #[Test]
    #[DataProvider('weightConversionProvider')]
    public function weightConversion(float $value, string $unit, float $expectedGrams, float $expectedKilos): void
    {
        $weight = new Weight($value, $unit);
        $this->assertEquals($expectedGrams, $weight->toGrams());
        $this->assertEquals($expectedKilos, $weight->toKilograms());
    }

    public static function weightConversionProvider(): array
    {
        return [
            'grams' => [1500, 'g', 1500, 1.5],
            'kilograms' => [2.5, 'kg', 2500, 2.5],
        ];
    }
}