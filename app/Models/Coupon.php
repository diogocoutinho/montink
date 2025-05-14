<?php

namespace App\Models;

use App\Core\Database;

class Coupon
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create($data)
    {
        $sql = "INSERT INTO coupons (
            code, discount_percentage, discount_amount,
            min_order_value, valid_from, valid_until, is_active
        ) VALUES (
            :code, :discount_percentage, :discount_amount,
            :min_order_value, :valid_from, :valid_until, :is_active
        )";

        $this->db->query($sql, [
            'code' => $data['code'],
            'discount_percentage' => $data['discount_percentage'] ?? 0,
            'discount_amount' => $data['discount_amount'] ?? 0,
            'min_order_value' => $data['min_order_value'] ?? 0,
            'valid_from' => $data['valid_from'],
            'valid_until' => $data['valid_until'],
            'is_active' => $data['is_active'] ?? true
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data)
    {
        $sql = "UPDATE coupons SET 
                code = :code,
                discount_percentage = :discount_percentage,
                discount_amount = :discount_amount,
                min_order_value = :min_order_value,
                valid_from = :valid_from,
                valid_until = :valid_until,
                is_active = :is_active
                WHERE id = :id";

        $this->db->query($sql, [
            'id' => $id,
            'code' => $data['code'],
            'discount_percentage' => $data['discount_percentage'] ?? 0,
            'discount_amount' => $data['discount_amount'] ?? 0,
            'min_order_value' => $data['min_order_value'] ?? 0,
            'valid_from' => $data['valid_from'],
            'valid_until' => $data['valid_until'],
            'is_active' => $data['is_active'] ?? true
        ]);
        return true;
    }

    public function delete($id)
    {
        $sql = "DELETE FROM coupons WHERE id = :id";
        $this->db->query($sql, ['id' => $id]);
        return true;
    }

    public function find($id)
    {
        $sql = "SELECT * FROM coupons WHERE id = :id";
        return $this->db->query($sql, ['id' => $id])->fetch();
    }

    public function findByCode($code)
    {
        $sql = "SELECT * FROM coupons WHERE code = :code";
        return $this->db->query($sql, ['code' => $code])->fetch();
    }

    public function findAll()
    {
        $sql = "SELECT * FROM coupons ORDER BY created_at DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function validate($code, $subtotal)
    {
        $coupon = $this->findByCode($code);

        if (!$coupon) {
            return false;
        }

        if (!$coupon['is_active']) {
            return false;
        }

        $now = new \DateTime();
        $validFrom = new \DateTime($coupon['valid_from']);
        $validUntil = new \DateTime($coupon['valid_until']);

        if ($now < $validFrom || $now > $validUntil) {
            return false;
        }

        if ($subtotal < $coupon['min_order_value']) {
            return false;
        }

        return $coupon;
    }

    public function calculateDiscount($coupon, $subtotal)
    {
        if ($coupon['discount_percentage'] > 0) {
            return $subtotal * ($coupon['discount_percentage'] / 100);
        }
        return $coupon['discount_amount'];
    }
}
