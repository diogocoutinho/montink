<?php

namespace App\Models;

use App\Core\Database;

class Order
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create($data)
    {
        try {
            $this->db->pdo->beginTransaction();

            // Create order
            $sql = "INSERT INTO orders (
                customer_name, customer_email, customer_phone,
                shipping_address, shipping_zipcode, shipping_city, shipping_state,
                subtotal, shipping_cost, discount_amount, total_amount, coupon_id
            ) VALUES (
                :customer_name, :customer_email, :customer_phone,
                :shipping_address, :shipping_zipcode, :shipping_city, :shipping_state,
                :subtotal, :shipping_cost, :discount_amount, :total_amount, :coupon_id
            )";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'shipping_address' => $data['shipping_address'],
                'shipping_zipcode' => $data['shipping_zipcode'],
                'shipping_city' => $data['shipping_city'],
                'shipping_state' => $data['shipping_state'],
                'subtotal' => $data['subtotal'],
                'shipping_cost' => $data['shipping_cost'],
                'discount_amount' => $data['discount_amount'] ?? 0,
                'total_amount' => $data['total_amount'],
                'coupon_id' => $data['coupon_id'] ?? null
            ]);

            $orderId = $this->db->lastInsertId();

            // Create order items
            foreach ($data['items'] as $item) {
                $sql = "INSERT INTO order_items (
                    order_id, product_id, variation_id, quantity, unit_price, total_price
                ) VALUES (
                    :order_id, :product_id, :variation_id, :quantity, :unit_price, :total_price
                )";

                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'variation_id' => $item['variation_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price']
                ]);

                // Update stock
                $product = new Product();
                $product->updateStock(
                    $item['product_id'],
                    $item['variation_id'] ?? null,
                    -$item['quantity']
                );
            }

            $this->db->pdo->commit();
            return $orderId;
        } catch (\Exception $e) {
            $this->db->pdo->rollBack();
            throw $e;
        }
    }

    public function find($id)
    {
        $sql = "SELECT * FROM orders WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $order = $stmt->fetch();

        if ($order) {
            $sql = "SELECT oi.*, p.name as product_name, v.name as variation_name
                    FROM order_items oi
                    LEFT JOIN products p ON oi.product_id = p.id
                    LEFT JOIN product_variations v ON oi.variation_id = v.id
                    WHERE oi.order_id = :order_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['order_id' => $id]);
            $order['items'] = $stmt->fetchAll();
        }

        return $order;
    }

    public function findAll()
    {
        $sql = "SELECT * FROM orders ORDER BY created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function updateStatus($id, $status)
    {
        $sql = "UPDATE orders SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'status' => $status
        ]);
    }

    public function cancel($id)
    {
        try {
            $this->db->pdo->beginTransaction();

            // Get order items
            $sql = "SELECT * FROM order_items WHERE order_id = :order_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['order_id' => $id]);
            $items = $stmt->fetchAll();

            // Return items to stock
            $product = new Product();
            foreach ($items as $item) {
                $product->updateStock(
                    $item['product_id'],
                    $item['variation_id'],
                    $item['quantity']
                );
            }

            // Delete order
            $sql = "DELETE FROM orders WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);

            $this->db->pdo->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->pdo->rollBack();
            throw $e;
        }
    }
}
