<?php

declare(strict_types=1);

namespace App\Application\Dto;

final class CollectionFilterDto
{
    public ?string $search = null;
    public ?string $unit = 'g';
    public ?float $minWeight = null;
    public ?float $maxWeight = null;
}