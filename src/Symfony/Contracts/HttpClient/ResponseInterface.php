<?php

namespace Symfony\Contracts\HttpClient;

interface ResponseInterface
{
    public function getStatusCode(): int;

    public function toArray(): array;
}
