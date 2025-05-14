<?php

namespace App\Repositories;

use App\Core\Database;

class OrderRepository
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findAll(): array
    {
        $sql = "SELECT * FROM orders ORDER BY created_at DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function find(int $id): ?array
    {
        $sql = "SELECT o.*, c.code as coupon_code 
                FROM orders o 
                LEFT JOIN coupons c ON o.coupon_id = c.id 
                WHERE o.id = ?";
        return $this->db->query($sql, [$id])->fetch();
    }

    public function create(array $data): int
    {
        $this->db->beginTransaction();

        try {
            // Inserir pedido
            $sql = "INSERT INTO orders (
                        customer_name, customer_email, customer_phone,
                        shipping_address, shipping_zipcode, shipping_city, shipping_state,
                        subtotal, shipping_cost, discount_amount, total_amount,
                        status, coupon_id
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $this->db->query($sql, [
                $data['customer_name'],
                $data['customer_email'],
                $data['customer_phone'] ?? null,
                $data['shipping_address'],
                $data['shipping_zipcode'],
                $data['shipping_city'],
                $data['shipping_state'],
                $data['subtotal'],
                $data['shipping_cost'],
                $data['discount_amount'] ?? 0,
                $data['total_amount'],
                $data['status'] ?? 'pending',
                $data['coupon_id'] ?? null
            ]);

            $orderId = $this->db->lastInsertId();

            // Inserir itens do pedido
            foreach ($data['items'] as $item) {
                $sql = "INSERT INTO order_items (
                            order_id, product_id, variation_id,
                            quantity, unit_price, total_price
                        ) VALUES (?, ?, ?, ?, ?, ?)";

                $this->db->query($sql, [
                    $orderId,
                    $item['product_id'],
                    $item['variation_id'] ?? null,
                    $item['quantity'],
                    $item['unit_price'],
                    $item['total_price']
                ]);

                if (is_null($item['variation_id'])) {
                    // Só produto principal
                    $sql = "UPDATE stock 
                            SET quantity = quantity - ? 
                            WHERE product_id = ? 
                            AND variation_id IS NULL";
                    $params = [$item['quantity'], $item['product_id']];
                    $this->db->query($sql, $params);
                } else {
                    // Variação: desconta variação e produto principal
                    $sql = "UPDATE stock 
                            SET quantity = quantity - ? 
                            WHERE product_id = ? 
                            AND variation_id = ?";
                    $params = [$item['quantity'], $item['product_id'], $item['variation_id']];
                    $this->db->query($sql, $params);
                    $sql = "UPDATE stock 
                            SET quantity = quantity - ? 
                            WHERE product_id = ? 
                            AND variation_id IS NULL";
                    $params = [$item['quantity'], $item['product_id']];
                    $this->db->query($sql, $params);
                }
            }

            $this->db->commit();
            return $orderId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function updateStatus(int $id, string $status): bool
    {
        $sql = "UPDATE orders SET status = ? WHERE id = ?";
        return $this->db->query($sql, [$status, $id])->rowCount() > 0;
    }

    public function cancel(int $id): bool
    {
        $this->db->beginTransaction();

        try {
            // Buscar itens do pedido
            $sql = "SELECT * FROM order_items WHERE order_id = ?";
            $items = $this->db->query($sql, [$id])->fetchAll();

            // Retornar itens ao estoque
            foreach ($items as $item) {
                if (is_null($item['variation_id'])) {
                    $sql = "UPDATE stock 
                            SET quantity = quantity + ? 
                            WHERE product_id = ? 
                            AND variation_id IS NULL";
                    $params = [$item['quantity'], $item['product_id']];
                    $this->db->query($sql, $params);
                } else {
                    // Variação: devolve para variação e produto principal
                    $sql = "UPDATE stock 
                            SET quantity = quantity + ? 
                            WHERE product_id = ? 
                            AND variation_id = ?";
                    $params = [$item['quantity'], $item['product_id'], $item['variation_id']];
                    $this->db->query($sql, $params);
                    $sql = "UPDATE stock 
                            SET quantity = quantity + ? 
                            WHERE product_id = ? 
                            AND variation_id IS NULL";
                    $params = [$item['quantity'], $item['product_id']];
                    $this->db->query($sql, $params);
                }
            }

            // Excluir pedido
            $sql = "DELETE FROM orders WHERE id = ?";
            $this->db->query($sql, [$id]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function processWebhook(array $payload): void
    {
        $this->db->beginTransaction();

        try {
            // Processar webhook
            $orderId = $payload['order_id'];
            $status = $payload['status'];

            // Atualizar status do pedido
            $sql = "UPDATE orders SET status = ? WHERE id = ?";
            $this->db->query($sql, [$status, $orderId]);

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function decrementStockForOrder(array $items): void
    {
        foreach ($items as $item) {
            if (is_null($item['variation_id'])) {
                $sql = "UPDATE stock 
                        SET quantity = quantity - ? 
                        WHERE product_id = ? 
                        AND variation_id IS NULL";
                $params = [$item['quantity'], $item['product_id']];
                $this->db->query($sql, $params);
            } else {
                $sql = "UPDATE stock 
                        SET quantity = quantity - ? 
                        WHERE product_id = ? 
                        AND variation_id = ?";
                $params = [$item['quantity'], $item['product_id'], $item['variation_id']];
                $this->db->query($sql, $params);
                $sql = "UPDATE stock 
                        SET quantity = quantity - ? 
                        WHERE product_id = ? 
                        AND variation_id IS NULL";
                $params = [$item['quantity'], $item['product_id']];
                $this->db->query($sql, $params);
            }
        }
    }

    public function getOrderItems($orderId)
    {
        $sql = "SELECT oi.*, p.name as product_name
                FROM order_items oi 
                LEFT JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = ?";
        return $this->db->query($sql, [$orderId])->fetchAll();
    }
}
