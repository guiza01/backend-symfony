<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class NativeHttpClient implements HttpClientInterface
{
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $headers = $options['headers'] ?? [];
        $content = $options['json'] ?? null;

        $normalizedHeaders = [];
        foreach ($headers as $name => $value) {
            if (is_int($name)) {
                $normalizedHeaders[] = (string) $value;
            } else {
                $normalizedHeaders[] = $name . ': ' . $value;
            }
        }

        $httpOptions = [
            'method' => $method,
            'header' => implode("\r\n", $normalizedHeaders),
            'ignore_errors' => true,
            'timeout' => 10,
        ];

        if ($content !== null) {
            $httpOptions['content'] = json_encode($content, JSON_UNESCAPED_UNICODE);
        }

        $context = stream_context_create(['http' => $httpOptions]);
        $raw = @file_get_contents($url, false, $context);
        $bodyText = $raw === false ? '' : $raw;

        $statusCode = 0;
        if (!empty($http_response_header[0]) && preg_match('#HTTP/\S+\s+(\d{3})#', $http_response_header[0], $matches)) {
            $statusCode = (int) $matches[1];
        }

        $decoded = json_decode($bodyText, true);
        $body = is_array($decoded) ? $decoded : [];

        return new NativeHttpResponse($statusCode, $body);
    }
}
