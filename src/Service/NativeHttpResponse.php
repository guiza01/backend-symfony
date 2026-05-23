<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\ResponseInterface;

class NativeHttpResponse implements ResponseInterface
{
    private int $statusCode;
    private array $body;

    public function __construct(int $statusCode, array $body)
    {
        $this->statusCode = $statusCode;
        $this->body = $body;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function toArray(): array
    {
        return $this->body;
    }
}
