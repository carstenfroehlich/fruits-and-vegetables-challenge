<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Application\Service\ItemProcessorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_')]
final class DataLoaderController extends AbstractController
{
    public function __construct(
        private readonly ItemProcessorService $itemProcessorService,
        private readonly string $projectDir
    ) {}

    #[Route('/load-data', name: 'load_data', methods: ['POST'])]
    public function loadData(Request $request): JsonResponse
    {
        try {
            // Check if data is provided in request body
            $content = $request->getContent();
            if (!empty($content)) {
                $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
            } else {
                // Load from default file
                $filePath = $this->projectDir . '/request.json';

                if (!file_exists($filePath)) {
                    return $this->json(['error' => 'Default data file not found'], Response::HTTP_NOT_FOUND);
                }

                $content = file_get_contents($filePath);
                $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
            }

            $this->itemProcessorService->processItems($data);

            return $this->json([
                'message' => 'Data loaded successfully',
                'items_processed' => count($data)
            ], Response::HTTP_OK);

        } catch (\JsonException $e) {
            return $this->json(['error' => 'Invalid JSON: ' . $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}