<?php

namespace App\Interfaces;

interface ProductServiceInterface
{
    public function getAllProducts(): array;
    public function getProduct(int $id): array;
    public function createProduct(array $data): int;
    public function updateProduct(int $id, array $data): bool;
    public function deleteProduct(int $id): bool;
}
