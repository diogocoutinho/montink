<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use App\Repositories\CouponRepository;
use App\Exceptions\ProductException;

class CartService
{
    private $productRepository;
    private $couponRepository;

    public function __construct()
    {
        $this->productRepository = new ProductRepository();
        $this->couponRepository = new CouponRepository();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    public function getCart(): array
    {
        return $_SESSION['cart'] ?? [];
    }

    public function add($productId, $variationId = null, $quantity = 1): void
    {
        $product = $this->productRepository->find($productId);
        if (!$product) {
            throw new ProductException('Produto não encontrado');
        }
        $variation = null;
        $variationName = null;
        $price = $product['price'];
        if ($variationId) {
            $variation = $this->productRepository->findVariation($variationId);
            if (!$variation) {
                throw new ProductException('Variação não encontrada');
            }
            $variationName = $variation['name'];
            $price += $variation['price_adjustment'];
            $stock = $variation['quantity'];
            // Só adiciona a variação, não o produto principal
            $productIdForCart = $productId;
            $variationIdForCart = $variationId;
        } else {
            $stock = $product['quantity'];
            $productIdForCart = $productId;
            $variationIdForCart = null;
        }
        if ($quantity > $stock) {
            throw new ProductException('Estoque insuficiente');
        }
        // Verifica se já existe no carrinho
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['product_id'] == $productIdForCart && $item['variation_id'] == $variationIdForCart) {
                $item['quantity'] += $quantity;
                if ($item['quantity'] > $stock) {
                    $item['quantity'] = $stock;
                }
                return;
            }
        }
        // Adiciona novo item
        $_SESSION['cart'][] = [
            'product_id' => $productIdForCart,
            'variation_id' => $variationIdForCart,
            'name' => $product['name'],
            'variation_name' => $variationName,
            'price' => $price,
            'quantity' => $quantity,
            'stock' => $stock
        ];
    }

    public function update($productId, $variationId = null, $quantity = 1): void
    {
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['product_id'] == $productId && $item['variation_id'] == $variationId) {
                if ($quantity <= 0) {
                    $this->remove($productId, $variationId);
                    return;
                }
                $item['quantity'] = min($quantity, $item['stock']);
                return;
            }
        }
    }

    public function remove($productId, $variationId = null): void
    {
        foreach ($_SESSION['cart'] as $i => $item) {
            if ($item['product_id'] == $productId && $item['variation_id'] == $variationId) {
                unset($_SESSION['cart'][$i]);
                $_SESSION['cart'] = array_values($_SESSION['cart']);
                return;
            }
        }
    }

    public function clear(): void
    {
        $_SESSION['cart'] = [];
    }

    public function getSubtotal(): float
    {
        $subtotal = 0;
        foreach ($this->getCart() as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        return $subtotal;
    }

    public function getFreight(): float
    {
        $subtotal = $this->getSubtotal();
        if ($subtotal > 200) {
            return 0;
        } elseif ($subtotal >= 52 && $subtotal <= 166.59) {
            return 15;
        } else {
            return 20;
        }
    }

    public function applyCoupon($code): ?array
    {
        $coupon = $this->couponRepository->findByCode($code);
        if (!$coupon) {
            return null;
        }
        $subtotal = $this->getSubtotal();
        if ($subtotal < $coupon['min_order_value']) {
            return null;
        }
        if (strtotime($coupon['valid_until']) < time()) {
            return null;
        }
        // Mapeia para o formato esperado pelo restante do sistema
        if (isset($coupon['discount_percentage']) && $coupon['discount_percentage'] > 0) {
            $coupon['type'] = 'percent';
            $coupon['value'] = $coupon['discount_percentage'];
        } else {
            $coupon['type'] = 'fixed';
            $coupon['value'] = $coupon['discount_amount'];
        }
        $_SESSION['cart_coupon'] = $coupon;
        return $coupon;
    }

    public function getCoupon(): ?array
    {
        return $_SESSION['cart_coupon'] ?? null;
    }

    public function getDiscount(): float
    {
        $coupon = $this->getCoupon();
        if (!$coupon || !isset($coupon['type']) || !isset($coupon['value'])) {
            return 0.0;
        }
        $subtotal = $this->getSubtotal();
        if ($coupon['type'] === 'percent') {
            return $subtotal * ((float)$coupon['value'] / 100);
        } else {
            return min((float)$coupon['value'], $subtotal);
        }
    }

    public function getTotal(): float
    {
        return $this->getSubtotal() + $this->getFreight() - $this->getDiscount();
    }
}
