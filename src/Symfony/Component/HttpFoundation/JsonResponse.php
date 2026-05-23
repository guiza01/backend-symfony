<?php

namespace Symfony\Component\HttpFoundation;

class JsonResponse
{
    private array $data;
    private int $statusCode;
    private array $headers;

    public function __construct(array $data = [], int $status = 200, array $headers = [])
    {
        $this->data = $data;
        $this->statusCode = $status;
        $this->headers = array_merge(['Content-Type' => 'application/json; charset=utf-8'], $headers);
    }

    public function getContent(): string
    {
        return json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?: '{}';
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function send(): void
    {
        http_response_code($this->statusCode);
        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }
        echo $this->getContent();
    }
}
