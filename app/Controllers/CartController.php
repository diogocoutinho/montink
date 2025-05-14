<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\CartService;
use App\Services\OrderService;
use App\Exceptions\ProductException;

class CartController extends Controller
{
    private $cartService;
    private $orderService;

    public function __construct()
    {
        $this->cartService = new CartService();
        $this->orderService = new OrderService();
    }

    public function index()
    {
        $this->render('cart/index', [
            'cart' => $this->cartService->getCart(),
            'subtotal' => $this->cartService->getSubtotal(),
            'freight' => $this->cartService->getFreight(),
            'discount' => $this->cartService->getDiscount(),
            'total' => $this->cartService->getTotal(),
            'coupon' => $this->cartService->getCoupon(),
        ]);
    }

    public function add()
    {
        try {
            $data = $this->getPostData();
            $productId = $data['product_id'];
            $variationId = $data['variation_id'] ?? null;
            $quantity = $data['quantity'] ?? 1;
            $this->cartService->add($productId, $variationId, $quantity);
            $this->setFlashMessage('success', 'Produto adicionado ao carrinho!');
        } catch (ProductException $e) {
            $this->setFlashMessage('error', $e->getMessage());
        }
        $this->redirect('/cart');
    }

    public function update()
    {
        $data = $this->getPostData();
        if (isset($data['update'])) {
            list($productId, $variationId) = explode(':', $data['update']);
            $variationId = $variationId === 'null' ? null : $variationId;
            $quantity = $data['quantities'][$productId][$variationId ?? 'null'] ?? 1;
            $this->cartService->update($productId, $variationId, $quantity);
        }
        $this->redirect('/cart');
    }

    public function remove()
    {
        $data = $this->getPostData();
        if (isset($data['remove'])) {
            list($productId, $variationId) = explode(':', $data['remove']);
            $variationId = $variationId === 'null' ? null : $variationId;
            $this->cartService->remove($productId, $variationId);
        }
        $this->redirect('/cart');
    }

    public function clear()
    {
        $this->cartService->clear();
        $this->redirect('/cart');
    }

    public function applyCoupon()
    {
        $data = $this->getPostData();
        $code = $data['coupon_code'] ?? '';
        $coupon = $this->cartService->applyCoupon($code);
        if ($coupon) {
            $this->setFlashMessage('success', 'Cupom aplicado!');
        } else {
            $this->setFlashMessage('error', 'Cupom inválido ou não aplicável.');
        }
        $this->redirect('/cart/checkout');
    }

    public function checkout()
    {
        if ($this->isPost()) {
            $data = $this->getPostData();
            $cart = $this->cartService->getCart();
            if (empty($cart)) {
                $this->setFlashMessage('error', 'Carrinho vazio.');
                $this->redirect('/cart');
                return;
            }
            $orderData = [
                'items' => $cart,
                'subtotal' => $this->cartService->getSubtotal(),
                'freight' => $this->cartService->getFreight(),
                'discount' => $this->cartService->getDiscount(),
                'total' => $this->cartService->getTotal(),
                'coupon' => $this->cartService->getCoupon(),
                'address' => [
                    'cep' => $data['cep'],
                    'logradouro' => $data['logradouro'],
                    'numero' => $data['numero'],
                    'bairro' => $data['bairro'],
                    'cidade' => $data['cidade'],
                    'uf' => $data['uf'],
                    'complemento' => $data['complemento'] ?? ''
                ],
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email']
            ];
            try {
                $orderId = $this->orderService->createOrderFromCart($orderData);
                $this->cartService->clear();
                unset($_SESSION['cart_coupon']);
                $this->setFlashMessage('success', 'Pedido realizado com sucesso!');
                $this->redirect('/orders/' . $orderId);
            } catch (\Exception $e) {
                $this->setFlashMessage('error', $e->getMessage());
                $this->redirect('/cart/checkout');
            }
        } else {
            $this->render('cart/checkout', [
                'cart' => $this->cartService->getCart(),
                'subtotal' => $this->cartService->getSubtotal(),
                'freight' => $this->cartService->getFreight(),
                'discount' => $this->cartService->getDiscount(),
                'total' => $this->cartService->getTotal(),
                'coupon' => $this->cartService->getCoupon(),
            ]);
        }
    }

    public function checkoutSummary()
    {
        // Renderiza apenas o resumo do pedido (usado via AJAX)
        ob_start();
        $cart = $this->cartService->getCart();
        $subtotal = $this->cartService->getSubtotal();
        $freight = $this->cartService->getFreight();
        $discount = $this->cartService->getDiscount();
        $total = $this->cartService->getTotal();
        $coupon = $this->cartService->getCoupon();
        include __DIR__ . '/../Views/cart/_summary.php';
        $html = ob_get_clean();
        echo $html;
    }
}
