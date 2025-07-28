<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Application\Dto\CollectionFilterDto;
use App\Application\Service\CollectionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/collections', name: 'api_collections_')]
final class CollectionController extends AbstractController
{
    public function __construct(
        private CollectionService $collectionService
    ) {}
    
    #[Route('/{type}', name: 'list', methods: ['GET'])]
    public function list(string $type, Request $request): JsonResponse
    {
        $collection = $this->collectionService->getCollection($type);
        
        if ($collection === null) {
            return $this->json(['error' => 'Invalid collection type'], Response::HTTP_BAD_REQUEST);
        }
        
        $filters = new CollectionFilterDto();
        $filters->search = $request->query->get('search');
        $filters->unit = $request->query->get('unit', 'g');
        $filters->minWeight = $request->query->get('minWeight') !== null
            ? (float) $request->query->get('minWeight')
            : null;
        $filters->maxWeight = $request->query->get('maxWeight') !== null
            ? (float) $request->query->get('maxWeight')
            : null;
        
        $items = $this->collectionService->applyFilters($collection, $filters);
        $serialized = $this->collectionService->serializeItems($items, $filters->unit);
        
        return $this->json([
            'type' => $type,
            'count' => count($serialized),
            'items' => $serialized,
        ]);
    }
    
    #[Route('', name: 'all', methods: ['GET'])]
    public function all(Request $request): JsonResponse
    {
        $unit = $request->query->get('unit', 'g');
        
        $fruits = $this->collectionService->getFruitCollection()?->list() ?? [];
        $vegetables = $this->collectionService->getVegetableCollection()?->list() ?? [];
        
        return $this->json([
            'fruits' => [
                'count' => count($fruits),
                'items' => $this->collectionService->serializeItems($fruits, $unit),
            ],
            'vegetables' => [
                'count' => count($vegetables),
                'items' => $this->collectionService->serializeItems($vegetables, $unit),
            ],
        ]);
    }
}