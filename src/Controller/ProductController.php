<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\LegacyProductService;
use App\DTO\ProductDTO;

class ProductController
{
    private LegacyProductService $legacyService;

    public function __construct(?LegacyProductService $legacyService = null)
    {
        $this->legacyService = $legacyService ?? new LegacyProductService();
    }

    #[Route('/api/products', name: 'api_products_list', methods: ['GET'])]
    public function index(): JsonResponse
    {
        try {
            $products = $this->legacyService->getProducts();
            return new JsonResponse(['payload' => $products, 'error' => null], 200);
        } catch (\Exception $e) {
            $code = $e->getCode() >= 400 ? $e->getCode() : 500;
            return new JsonResponse(['payload' => null, 'error' => ['message' => $e->getMessage()]], $code);
        }
    }

    #[Route('/api/products/{id<\d+>}', name: 'api_products_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        try {
            $product = $this->legacyService->getProduct((int) $id);
            return new JsonResponse(['payload' => $product, 'error' => null], 200);
        } catch (\RuntimeException $e) {
            $code = $e->getCode() >= 400 ? $e->getCode() : 500;
            return new JsonResponse(['payload' => null, 'error' => ['message' => $e->getMessage()]], $code);
        } catch (\Exception $e) {
            return new JsonResponse(['payload' => null, 'error' => ['message' => $e->getMessage()]], 500);
        }
    }

    #[Route('/api/products', name: 'api_products_store', methods: ['POST'])]
    public function store(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?: [];
        try {
            $dto = ProductDTO::fromArray($data);
            $dto->validate();
            $created = $this->legacyService->createProduct($dto->toArray());
            return new JsonResponse(['payload' => $created, 'error' => null], 201);
        } catch (\InvalidArgumentException $e) {
            $fields = json_decode($e->getMessage(), true) ?: $e->getMessage();
            return new JsonResponse(['payload' => null, 'error' => ['message' => 'Dados inválidos', 'fields' => $fields]], 400);
        } catch (\RuntimeException $e) {
            $code = $e->getCode() >= 400 ? $e->getCode() : 500;
            return new JsonResponse(['payload' => null, 'error' => ['message' => $e->getMessage()]], $code);
        } catch (\Exception $e) {
            return new JsonResponse(['payload' => null, 'error' => ['message' => $e->getMessage()]], 500);
        }
    }

    #[Route('/api/products/{id<\d+>}', name: 'api_products_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?: [];
        try {
            $dto = ProductDTO::fromArray($data);
            $dto->validate();
            $updated = $this->legacyService->updateProduct($id, $dto->toArray());
            return new JsonResponse(['payload' => $updated, 'error' => null], 200);
        } catch (\InvalidArgumentException $e) {
            $fields = json_decode($e->getMessage(), true) ?: $e->getMessage();
            return new JsonResponse(['payload' => null, 'error' => ['message' => 'Dados inválidos', 'fields' => $fields]], 400);
        } catch (\RuntimeException $e) {
            $code = $e->getCode() >= 400 ? $e->getCode() : 500;
            return new JsonResponse(['payload' => null, 'error' => ['message' => $e->getMessage()]], $code);
        } catch (\Exception $e) {
            return new JsonResponse(['payload' => null, 'error' => ['message' => $e->getMessage()]], 500);
        }
    }

    #[Route('/api/products/{id<\d+>}', name: 'api_products_delete', methods: ['DELETE'])]
    public function destroy(int $id): JsonResponse
    {
        try {
            $result = $this->legacyService->deleteProduct($id);
            return new JsonResponse(['payload' => $result, 'error' => null], 200);
        } catch (\RuntimeException $e) {
            $code = $e->getCode() >= 400 ? $e->getCode() : 500;
            return new JsonResponse(['payload' => null, 'error' => ['message' => $e->getMessage()]], $code);
        } catch (\Exception $e) {
            return new JsonResponse(['payload' => null, 'error' => ['message' => $e->getMessage()]], 500);
        }
    }
}
