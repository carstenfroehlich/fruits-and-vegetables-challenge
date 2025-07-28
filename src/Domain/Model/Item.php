<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\ValueObject\Weight;

abstract class Item
{
    public function __construct(
        private readonly int $id,
        private readonly string $name,
        private readonly Weight $weight
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getWeight(): Weight
    {
        return $this->weight;
    }

    abstract public function getType(): string;
}