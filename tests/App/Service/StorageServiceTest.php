<?php

namespace App\Tests\App\Service;

use App\Application\Service\StorageService;
use PHPUnit\Framework\TestCase;

class StorageServiceTest extends TestCase
{
    public function testReceivingRequest(): void
    {
        $request = file_get_contents('request.json');

        $storageService = new StorageService($request);

        $this->assertNotEmpty($storageService->getRequest());
        $this->assertIsString($storageService->getRequest());
    }
}
