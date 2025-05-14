<?php

namespace App\Repositories;

use App\Core\Database;

class CouponRepository
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findByCode(string $code): ?array
    {
        $sql = "SELECT * FROM coupons WHERE code = ? LIMIT 1";
        $result = $this->db->query($sql, [$code])->fetch();
        return $result ?: null;
    }
}
