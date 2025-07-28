<?php

namespace App\Application\Service;

class StorageService
{
    protected string $request = '';

    public function __construct(
        string $request
    )
    {
        $this->request = $request;
    }

    public function getRequest(): string
    {
        return $this->request;
    }
}
