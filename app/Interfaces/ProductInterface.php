<?php

namespace App\Interfaces;

interface ProductInterface
{
    public function create(array $data): int;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function find(int $id): ?array;
    public function findAll(): array;
    public function addVariation(int $productId, array $data): int;
    public function updateVariation(int $id, array $data): bool;
    public function getVariations(int $productId): array;
    public function updateStock(int $productId, ?int $variationId, int $quantity): bool;
    public function checkStock(int $productId, ?int $variationId, int $quantity): bool;
}
