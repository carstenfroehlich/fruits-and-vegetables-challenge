<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Dto\ItemDto;
use App\Domain\Collection\FruitCollection;
use App\Domain\Collection\VegetableCollection;
use App\Domain\Model\Fruit;
use App\Domain\Model\Vegetable;
use App\Domain\ValueObject\Weight;
use App\Exception\ValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ItemProcessorService
{
    public function __construct(
        private ValidatorInterface $validator,
        private CollectionService $collectionService
    ) {}
    
    public function processItems(array $items): void
    {
        $fruitCollection = new FruitCollection();
        $vegetableCollection = new VegetableCollection();
        
        foreach ($items as $itemData) {
            $itemDto = $this->createItemDto($itemData);
            $this->validateItemDto($itemDto);
            
            $item = $this->createItem($itemDto);
            
            if ($item instanceof Fruit) {
                $fruitCollection->add($item);
            } else {
                $vegetableCollection->add($item);
            }
        }
        
        $this->collectionService->setFruitCollection($fruitCollection);
        $this->collectionService->setVegetableCollection($vegetableCollection);
    }
    
    private function createItemDto(array $data): ItemDto
    {
        $dto = new ItemDto();
        $dto->id = $data['id'] ?? null;
        $dto->name = $data['name'] ?? null;
        $dto->type = $data['type'] ?? null;
        $dto->quantity = $data['quantity'] ?? null;
        $dto->unit = $data['unit'] ?? null;
        
        return $dto;
    }
    
    private function validateItemDto(ItemDto $dto): void
    {
        $errors = $this->validator->validate($dto);
        
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
            }
            throw new ValidationException(implode(', ', $messages));
        }
    }
    
    private function createItem(ItemDto $dto): Fruit|Vegetable
    {
        $weight = new Weight($dto->quantity, $dto->unit);
        
        return match ($dto->type) {
            'fruit' => new Fruit($dto->id, $dto->name, $weight),
            'vegetable' => new Vegetable($dto->id, $dto->name, $weight),
            default => throw new ValidationException('Invalid type: ' . $dto->type),
        };
    }
}