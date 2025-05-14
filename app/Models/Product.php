<?php

namespace App\Models;

use App\Core\Database;

class Product
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create($data)
    {
        $sql = "INSERT INTO products (name, price) VALUES (:name, :price)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'name' => $data['name'],
            'price' => $data['price']
        ]);

        $productId = $this->db->lastInsertId();

        // Create initial stock entry
        $sql = "INSERT INTO stock (product_id, quantity) VALUES (:product_id, :quantity)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'product_id' => $productId,
            'quantity' => $data['quantity'] ?? 0
        ]);

        return $productId;
    }

    public function update($id, $data)
    {
        $sql = "UPDATE products SET name = :name, price = :price WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'price' => $data['price']
        ]);

        // Update stock if quantity is provided
        if (isset($data['quantity'])) {
            $sql = "UPDATE stock SET quantity = :quantity WHERE product_id = :product_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'product_id' => $id,
                'quantity' => $data['quantity']
            ]);
        }

        return true;
    }

    public function delete($id)
    {
        $sql = "DELETE FROM products WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function find($id)
    {
        $sql = "SELECT p.*, s.quantity 
                FROM products p 
                LEFT JOIN stock s ON p.id = s.product_id 
                WHERE p.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function findAll()
    {
        $sql = "SELECT p.*, s.quantity 
                FROM products p 
                LEFT JOIN stock s ON p.id = s.product_id";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function addVariation($productId, $data)
    {
        $sql = "INSERT INTO product_variations (product_id, name, price_adjustment) 
                VALUES (:product_id, :name, :price_adjustment)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'product_id' => $productId,
            'name' => $data['name'],
            'price_adjustment' => $data['price_adjustment']
        ]);

        $variationId = $this->db->lastInsertId();

        // Create stock entry for variation
        $sql = "INSERT INTO stock (product_id, variation_id, quantity) 
                VALUES (:product_id, :variation_id, :quantity)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'product_id' => $productId,
            'variation_id' => $variationId,
            'quantity' => $data['quantity'] ?? 0
        ]);

        return $variationId;
    }

    public function updateVariation($id, $data)
    {
        $sql = "UPDATE product_variations 
                SET name = :name, price_adjustment = :price_adjustment 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'price_adjustment' => $data['price_adjustment']
        ]);

        // Update stock if quantity is provided
        if (isset($data['quantity'])) {
            $sql = "UPDATE stock 
                    SET quantity = :quantity 
                    WHERE variation_id = :variation_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'variation_id' => $id,
                'quantity' => $data['quantity']
            ]);
        }

        return true;
    }

    public function getVariations($productId)
    {
        $sql = "SELECT v.*, s.quantity 
                FROM product_variations v 
                LEFT JOIN stock s ON v.id = s.variation_id 
                WHERE v.product_id = :product_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['product_id' => $productId]);
        return $stmt->fetchAll();
    }

    public function updateStock($productId, $variationId, $quantity)
    {
        $sql = "UPDATE stock 
                SET quantity = quantity + :quantity 
                WHERE product_id = :product_id 
                AND variation_id " . ($variationId ? "= :variation_id" : "IS NULL");

        $params = [
            'quantity' => $quantity,
            'product_id' => $productId
        ];

        if ($variationId) {
            $params['variation_id'] = $variationId;
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function checkStock($productId, $variationId, $quantity)
    {
        $sql = "SELECT quantity 
                FROM stock 
                WHERE product_id = :product_id 
                AND variation_id " . ($variationId ? "= :variation_id" : "IS NULL");

        $params = ['product_id' => $productId];
        if ($variationId) {
            $params['variation_id'] = $variationId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();

        return $result && $result['quantity'] >= $quantity;
    }
}
