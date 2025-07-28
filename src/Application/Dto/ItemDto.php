<?php

declare(strict_types=1);

namespace App\Application\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class ItemDto
{
    #[Assert\NotNull]
    #[Assert\Type('integer')]
    public ?int $id = null;
    
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    public ?string $name = null;
    
    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['fruit', 'vegetable'])]
    public ?string $type = null;
    
    #[Assert\NotNull]
    #[Assert\Type('numeric')]
    #[Assert\Positive]
    public ?float $quantity = null;
    
    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['g', 'kg', 'gram', 'grams', 'kilogram', 'kilograms'])]
    public ?string $unit = null;
}