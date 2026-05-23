<?php

require_once dirname(__DIR__) . '/src/bootstrap.php';

use App\Controller\ProductController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

$controller = new ProductController();

if ($method === 'GET' && $uri === '/api/products') {
    $response = $controller->index();
} elseif ($method === 'GET' && preg_match('#^/api/products/(\d+)$#', $uri, $matches)) {
    $response = $controller->show((int) $matches[1]);
} elseif ($method === 'POST' && $uri === '/api/products') {
    $response = $controller->store(Request::createFromGlobals());
} elseif ($method === 'PUT' && preg_match('#^/api/products/(\d+)$#', $uri, $matches)) {
    $response = $controller->update((int) $matches[1], Request::createFromGlobals());
} elseif ($method === 'DELETE' && preg_match('#^/api/products/(\d+)$#', $uri, $matches)) {
    $response = $controller->destroy((int) $matches[1]);
} else {
    $response = new JsonResponse([
        'payload' => null,
        'error' => [
            'message' => 'Not Found',
        ],
    ], 404);
}

$response->send();
