<?php

declare(strict_types=1);

namespace App\Domain\Model;

final class Vegetable extends Item
{
    public function getType(): string
    {
        return 'vegetable';
    }
}