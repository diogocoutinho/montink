<?php

namespace App\Repositories;

use App\Interfaces\ProductInterface;
use App\Core\Database;

class ProductRepository implements ProductInterface
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO products (name, price) VALUES (?, ?)";
        $this->db->query($sql, [$data['name'], $data['price']]);
        $productId = $this->db->lastInsertId();

        // Create initial stock entry
        $sql = "INSERT INTO stock (product_id, quantity) VALUES (?, ?)";
        $this->db->query($sql, [$productId, $data['quantity'] ?? 0]);

        return $productId;
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE products SET name = ?, price = ? WHERE id = ?";
        $this->db->query($sql, [$data['name'], $data['price'], $id]);

        if (isset($data['quantity'])) {
            $sql = "UPDATE stock SET quantity = ? WHERE product_id = ?";
            $this->db->query($sql, [$data['quantity'], $id]);
        }

        return true;
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM products WHERE id = ?";
        $this->db->query($sql, [$id]);
        return true;
    }

    public function find(int $id): ?array
    {
        $sql = "SELECT p.*, s.quantity 
                FROM products p 
                LEFT JOIN stock s ON p.id = s.product_id 
                WHERE p.id = ?";
        return $this->db->query($sql, [$id])->fetch();
    }

    public function findAll(): array
    {
        $sql = "SELECT p.*, s.quantity 
                FROM products p 
                LEFT JOIN stock s ON p.id = s.product_id 
                WHERE s.variation_id IS NULL
                ORDER BY p.name";
        return $this->db->query($sql)->fetchAll();
    }

    public function addVariation(int $productId, array $data): int
    {
        // Criação da variação
        $sql = "INSERT INTO product_variations (product_id, name, price_adjustment) 
                VALUES (?, ?, ?)";
        $this->db->query($sql, [
            $productId,
            $data['name'],
            $data['price_adjustment'] ?? 0
        ]);
        $variationId = $this->db->lastInsertId();
        // Create stock entry for variation
        $novaQtd = (int)($data['quantity'] ?? 0);
        $sql = "INSERT INTO stock (product_id, variation_id, quantity) 
                VALUES (?, ?, ?)";
        $this->db->query($sql, [
            $productId,
            $variationId,
            $novaQtd
        ]);
        // Após inserir, ajuste o estoque do produto principal para a soma das variações
        $variations = $this->getVariations($productId);
        $soma = 0;
        foreach ($variations as $v) {
            $soma += (int)$v['quantity'];
        }
        $sql = "UPDATE stock SET quantity = ? WHERE product_id = ? AND variation_id IS NULL";
        $this->db->query($sql, [$soma, $productId]);
        return $variationId;
    }

    public function updateVariation(int $id, array $data): bool
    {
        $variation = $this->findVariation($id);
        $productId = $variation['product_id'];
        $sql = "UPDATE product_variations 
                SET name = ?, price_adjustment = ? 
                WHERE id = ?";
        $this->db->query($sql, [
            $data['name'],
            $data['price_adjustment'] ?? 0,
            $id
        ]);
        if (isset($data['quantity'])) {
            $sql = "UPDATE stock SET quantity = ? WHERE variation_id = ?";
            $this->db->query($sql, [$data['quantity'], $id]);
        }
        // Após atualizar, ajuste o estoque do produto principal para a soma das variações
        $variations = $this->getVariations($productId);
        $soma = 0;
        foreach ($variations as $v) {
            $soma += (int)$v['quantity'];
        }
        $sql = "UPDATE stock SET quantity = ? WHERE product_id = ? AND variation_id IS NULL";
        $this->db->query($sql, [$soma, $productId]);
        return true;
    }

    public function getVariations(int $productId): array
    {
        $sql = "SELECT v.*, s.quantity 
                FROM product_variations v 
                LEFT JOIN stock s ON v.id = s.variation_id 
                WHERE v.product_id = ? 
                ORDER BY v.name";
        return $this->db->query($sql, [$productId])->fetchAll();
    }

    public function updateStock(int $productId, ?int $variationId, int $quantity): bool
    {
        if ($variationId) {
            $sql = "UPDATE stock 
                    SET quantity = ? 
                    WHERE product_id = ? AND variation_id = ?";
            $this->db->query($sql, [$quantity, $productId, $variationId]);
        } else {
            $sql = "UPDATE stock 
                    SET quantity = ? 
                    WHERE product_id = ? AND variation_id IS NULL";
            $this->db->query($sql, [$quantity, $productId]);
        }
        return true;
    }

    public function checkStock(int $productId, ?int $variationId, int $quantity): bool
    {
        if ($variationId) {
            $sql = "SELECT quantity 
                    FROM stock 
                    WHERE product_id = ? AND variation_id = ?";
            $result = $this->db->query($sql, [$productId, $variationId])->fetch();
        } else {
            $sql = "SELECT quantity 
                    FROM stock 
                    WHERE product_id = ? AND variation_id IS NULL";
            $result = $this->db->query($sql, [$productId])->fetch();
        }

        return $result && $result['quantity'] >= $quantity;
    }

    public function findVariation(int $variationId): ?array
    {
        $sql = "SELECT v.*, s.quantity 
                FROM product_variations v 
                LEFT JOIN stock s ON v.id = s.variation_id 
                WHERE v.id = ?";
        return $this->db->query($sql, [$variationId])->fetch();
    }

    public function hasOrderItems($productId)
    {
        $sql = "SELECT COUNT(*) FROM order_items WHERE product_id = ?";
        return $this->db->query($sql, [$productId])->fetchColumn() > 0;
    }
}
