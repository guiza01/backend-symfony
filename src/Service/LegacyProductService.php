<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class LegacyProductService
{
    private string $baseUrl;
    private HttpClientInterface $httpClient;

    public function __construct(?HttpClientInterface $httpClient = null, ?string $legacyApiUrl = null)
    {
        $this->httpClient = $httpClient ?? new NativeHttpClient();
        $envUrl = $legacyApiUrl ?? (getenv('LEGACY_API_URL') ?: null);

        if ($envUrl === null || trim($envUrl) === '') {
            throw new \InvalidArgumentException('LEGACY_API_URL não configurada no ambiente.');
        }

        $this->baseUrl = rtrim($envUrl, '/');
    }

    private function request(string $method, string $path, ?array $payload = null): array
    {
        $url = $this->baseUrl . $path;
        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ];
        if ($payload !== null) {
            $options['json'] = $payload;
        }

        $response = $this->httpClient->request($method, $url, $options);
        $decoded = $response->toArray();

        return [
            'status' => $response->getStatusCode(),
            'body' => is_array($decoded) ? $decoded : [],
        ];
    }

    private function throwFromLegacyError(array $data, int $status, string $fallback): void
    {
        $message = $data['error']['message'] ?? $fallback;
        $fields = $data['error']['fields'] ?? null;

        if ($status === 400 && is_array($fields)) {
            throw new \InvalidArgumentException(json_encode($fields));
        }

        throw new \RuntimeException($message, $status >= 400 ? $status : 500);
    }

    public function getProducts(): array
    {
        $response = $this->request('GET', '/api/products');
        $status = $response['status'];
        $data = $response['body'];
        if ($status >= 400) {
            $this->throwFromLegacyError($data, $status, 'Erro na API legada');
        }
        return $data['payload'] ?? [];
    }

    public function getProduct(int $id): array
    {
        $response = $this->request('GET', '/api/products/' . $id);
        $status = $response['status'];
        $data = $response['body'];
        if ($status >= 400) {
            $this->throwFromLegacyError($data, $status, 'Erro na API legada');
        }
        return $data['payload'] ?? [];
    }

    public function createProduct(array $payload): array
    {
        $response = $this->request('POST', '/api/products', $payload);
        $status = $response['status'];
        $data = $response['body'];
        if ($status >= 400) {
            $this->throwFromLegacyError($data, $status, 'Erro ao criar produto na API legada');
        }
        return $data['payload'] ?? [];
    }

    public function updateProduct(int $id, array $payload): array
    {
        $response = $this->request('PUT', '/api/products/' . $id, $payload);
        $status = $response['status'];
        $data = $response['body'];
        if ($status >= 400) {
            $this->throwFromLegacyError($data, $status, 'Erro ao atualizar produto na API legada');
        }
        return $data['payload'] ?? [];
    }

    public function deleteProduct(int $id): array
    {
        $response = $this->request('DELETE', '/api/products/' . $id);
        $status = $response['status'];
        $data = $response['body'];
        if ($status >= 400) {
            $this->throwFromLegacyError($data, $status, 'Erro ao excluir produto na API legada');
        }
        return $data['payload'] ?? ['deleted' => true];
    }
}
