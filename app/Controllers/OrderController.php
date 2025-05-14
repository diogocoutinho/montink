<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\OrderService;
use App\Services\ProductService;

class OrderController extends Controller
{
    private $orderService;
    private $productService;

    public function __construct()
    {
        $this->orderService = new OrderService();
        $this->productService = new ProductService();
    }

    public function index()
    {
        $orders = $this->orderService->getAllOrders();
        return $this->render('orders/index', [
            'orders' => $orders
        ]);
    }

    public function create()
    {
        if ($this->isPost()) {
            try {
                $orderData = [
                    'customer_name' => $_POST['customer_name'],
                    'customer_email' => $_POST['customer_email'],
                    'items' => $_POST['items'],
                    'coupon_code' => $_POST['coupon_code'] ?? null
                ];

                $orderId = $this->orderService->createOrder($orderData);
                $_SESSION['success'] = 'Pedido criado com sucesso!';
                $this->redirect('/orders/' . $orderId);
            } catch (\Exception $e) {
                $_SESSION['error'] = 'Erro ao criar pedido: ' . $e->getMessage();
            }
        }

        $products = $this->productService->getAllProducts();
        return $this->render('orders/create', [
            'products' => $products
        ]);
    }

    public function view($id)
    {
        try {
            $order = $this->orderService->getOrder($id);
            $order['items'] = $this->orderService->getOrderItems($id);
            return $this->render('orders/view', [
                'order' => $order,
                'items' => $order['items']
            ]);
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erro ao visualizar pedido: ' . $e->getMessage();
            $this->redirect('/orders');
        }
    }

    public function updateStatus($id)
    {
        if ($this->isPost()) {
            try {
                $status = $_POST['status'];
                $this->orderService->updateOrderStatus($id, $status);
                $_SESSION['success'] = 'Status do pedido atualizado com sucesso!';
            } catch (\Exception $e) {
                $_SESSION['error'] = 'Erro ao atualizar status: ' . $e->getMessage();
            }
        }
        $this->redirect('/orders/' . $id);
    }

    public function webhook()
    {
        if (!$this->isPost()) {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        try {
            $payload = json_decode(file_get_contents('php://input'), true);
            $this->orderService->processWebhook($payload);
            return $this->json(['success' => true]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }
}
