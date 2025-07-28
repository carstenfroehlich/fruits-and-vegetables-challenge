<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Application\Dto\ItemDto;
use App\Application\Service\CollectionService;
use App\Domain\Model\Fruit;
use App\Domain\Model\Vegetable;
use App\Domain\ValueObject\Weight;
use App\Exception\ValidationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/items', name: 'api_items_')]
final class ItemController extends AbstractController
{
    public function __construct(
        private readonly CollectionService $collectionService,
        private readonly ValidatorInterface $validator
    ) {}

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if ($data === null) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            $itemDto = new ItemDto();
            $itemDto->id = $data['id'] ?? null;
            $itemDto->name = $data['name'] ?? null;
            $itemDto->type = $data['type'] ?? null;
            $itemDto->quantity = $data['quantity'] ?? null;
            $itemDto->unit = $data['unit'] ?? null;

            $errors = $this->validator->validate($itemDto);

            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
                }
                return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
            }

            $weight = new Weight($itemDto->quantity, $itemDto->unit);

            $item = match ($itemDto->type) {
                'fruit' => new Fruit($itemDto->id, $itemDto->name, $weight),
                'vegetable' => new Vegetable($itemDto->id, $itemDto->name, $weight),
            };

            $collection = $this->collectionService->getCollection($itemDto->type);

            if ($collection === null) {
                return $this->json(['error' => 'Collection not initialized'], Response::HTTP_SERVICE_UNAVAILABLE);
            }

            // Item zur Collection hinzufügen
            $collection->add($item);

            // WICHTIG: Collection wieder speichern!
            if ($itemDto->type === 'fruit') {
                $this->collectionService->setFruitCollection($collection);
            } else {
                $this->collectionService->setVegetableCollection($collection);
            }

            return $this->json([
                'id' => $item->getId(),
                'name' => $item->getName(),
                'type' => $item->getType(),
                'quantity' => $item->getWeight()->toGrams(),
                'unit' => 'g',
            ], Response::HTTP_CREATED);

        } catch (ValidationException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{type}/{id}', name: 'delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(string $type, int $id): JsonResponse
    {
        $collection = $this->collectionService->getCollection($type);

        if ($collection === null) {
            return $this->json(['error' => 'Invalid collection type'], Response::HTTP_BAD_REQUEST);
        }

        $item = $collection->get($id);

        if ($item === null) {
            return $this->json(['error' => 'Item not found'], Response::HTTP_NOT_FOUND);
        }

        // Item aus Collection entfernen
        $collection->remove($id);

        // WICHTIG: Collection wieder speichern!
        if ($type === 'fruit') {
            $this->collectionService->setFruitCollection($collection);
        } else {
            $this->collectionService->setVegetableCollection($collection);
        }

        return $this->json(['message' => 'Item removed successfully'], Response::HTTP_OK);
    }
}
