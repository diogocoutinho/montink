<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\ProductService;
use App\Services\OrderService;

class HomeController extends Controller
{
    private $productService;
    private $orderService;

    public function __construct()
    {
        $this->productService = new ProductService();
        $this->orderService = new OrderService();
    }

    public function index(): void
    {
        // Buscar todos os produtos
        $allProducts = $this->productService->getAllProducts();

        // Filtrar apenas produtos principais (sem parent_id ou campo equivalente)
        $featuredProducts = array_filter($allProducts, function ($product) {
            return !isset($product['variant_id']) || !$product['variant_id'];
        });

        // Pegar os 4 primeiros
        $featuredProducts = array_slice($featuredProducts, 0, 4);

        // Buscar últimas ordens (últimas 5 ordens)
        $recentOrders = $this->orderService->getAllOrders();
        $recentOrders = array_slice($recentOrders, 0, 5);

        // Renderizar a view com os dados
        $this->render('home/index', [
            'featuredProducts' => $featuredProducts,
            'recentOrders' => $recentOrders
        ]);
    }
}
