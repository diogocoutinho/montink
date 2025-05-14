<?php

namespace App\Services;

use App\Interfaces\ProductServiceInterface;
use App\Repositories\ProductRepository;
use App\Exceptions\ProductException;

class ProductService implements ProductServiceInterface
{
    private $repository;

    public function __construct()
    {
        $this->repository = new ProductRepository();
    }

    public function getAllProducts(): array
    {
        return $this->repository->findAll();
    }

    public function getProduct(int $id): array
    {
        $product = $this->repository->find($id);
        $variations = $this->repository->getVariations($id);
        if (!$product) {
            throw new ProductException('Produto não encontrado');
        }
        return ['product' => $product, 'variations' => $variations];
    }

    public function createProduct(array $data): int
    {
        // Validação de dados
        $this->validateProductData($data);

        // Lógica de negócios
        if (isset($data['variations'])) {
            $this->validateVariations($data['variations']);
        }

        // Criação do produto
        $productId = $this->repository->create($data);

        // Criação das variações
        if (isset($data['variations'])) {
            foreach ($data['variations'] as $variation) {
                $this->repository->addVariation($productId, $variation);
            }
        }

        return $productId;
    }

    public function updateProduct(int $id, array $data): bool
    {
        // Validação de dados
        $this->validateProductData($data);

        // Verificação de existência
        $product = $this->repository->find($id);
        if (!$product) {
            throw new ProductException('Produto não encontrado');
        }

        // Atualização do produto
        $this->repository->update($id, $data);

        // Atualização das variações
        if (isset($data['variations'])) {
            $this->updateVariations($id, $data['variations']);
        }

        return true;
    }

    public function deleteProduct(int $id): bool
    {
        if ($this->repository->hasOrderItems($id)) {
            throw new \Exception('Não é possível excluir: produto já utilizado em pedidos.');
        }
        $product = $this->repository->find($id);
        if (!$product) {
            throw new ProductException('Produto não encontrado');
        }

        return $this->repository->delete($id);
    }

    private function validateProductData(array $data)
    {
        if (empty($data['name'])) {
            throw new ProductException('Nome do produto é obrigatório');
        }

        if (!isset($data['price']) || $data['price'] < 0) {
            throw new ProductException('Preço inválido');
        }

        if (isset($data['quantity']) && $data['quantity'] < 0) {
            throw new ProductException('Quantidade inválida');
        }
    }

    private function validateVariations(array $variations)
    {
        foreach ($variations as $variation) {
            if (empty($variation['name'])) {
                throw new ProductException('Nome da variação é obrigatório');
            }

            if (isset($variation['price_adjustment']) && $variation['price_adjustment'] < 0) {
                throw new ProductException('Ajuste de preço inválido');
            }

            if (isset($variation['quantity']) && $variation['quantity'] < 0) {
                throw new ProductException('Quantidade inválida');
            }
        }
    }

    private function updateVariations(int $productId, array $variations)
    {
        $existingVariations = $this->repository->getVariations($productId);
        $existingIds = array_column($existingVariations, 'id');

        foreach ($variations as $variation) {
            if (isset($variation['id']) && in_array($variation['id'], $existingIds)) {
                // Atualiza variação existente
                $this->repository->updateVariation($variation['id'], $variation);
            } else {
                // Adiciona nova variação
                $this->repository->addVariation($productId, $variation);
            }
        }
    }

    public function getVariation(int $variationId): array
    {
        return $this->repository->findVariation($variationId);
    }

    public function updateVariation(int $variationId, array $data): bool
    {
        return $this->repository->updateVariation($variationId, $data);
    }
}
