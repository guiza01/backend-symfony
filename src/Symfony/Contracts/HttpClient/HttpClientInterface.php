<?php

namespace Symfony\Contracts\HttpClient;

interface HttpClientInterface
{
    public function request(string $method, string $url, array $options = []): ResponseInterface;
}
