<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Exception\ValidationException;

final readonly class Weight
{
    private float $grams;

    public function __construct(float $value, string $unit)
    {
        $this->grams = $this->convertToGrams($value, $unit);

        if ($this->grams < 0) {
            throw new ValidationException('Weight cannot be negative');
        }
    }

    private function convertToGrams(float $value, string $unit): float
    {
        return match (strtolower($unit)) {
            'g', 'gram', 'grams' => $value,
            'kg', 'kilogram', 'kilograms' => $value * 1000,
            default => throw new ValidationException(sprintf('Unknown unit: %s', $unit)),
        };
    }

    public function toGrams(): float
    {
        return $this->grams;
    }

    public function toKilograms(): float
    {
        return $this->grams / 1000;
    }

    public function getValue(string $unit = 'g'): float
    {
        return match (strtolower($unit)) {
            'g', 'gram', 'grams' => $this->toGrams(),
            'kg', 'kilogram', 'kilograms' => $this->toKilograms(),
            default => throw new ValidationException(sprintf('Unknown unit: %s', $unit)),
        };
    }
}