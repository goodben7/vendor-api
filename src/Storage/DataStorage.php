<?php

namespace App\Storage;

use Symfony\Component\HttpFoundation\RequestStack;

class DataStorage
{

    private ?string $platformId = null;
    private array $headers = [];

    public function __construct(
        private readonly RequestStack $requestStack,
    )
    {
    }

    /**
     * Get the value of platformId
     */ 
    public function getPlatformId(): string|null
    {
        return $this->platformId;
    }

    /**
     * Set the value of platformId
     *
     * @return  self
     */ 
    public function setPlatformId(?string $platformId): static
    {
        $this->platformId = $platformId;

        return $this;
    }
    
    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    public function getHeader(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }

    public function clear(): void
    {
        $this->platformId = null;
        $this->headers = [];
    }
}