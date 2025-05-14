<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\ProductService;
use App\Exceptions\ProductException;

class ProductController extends Controller
{
    private $productService;

    public function __construct()
    {
        $this->productService = new ProductService();
    }

    public function index()
    {
        $products = $this->productService->getAllProducts();
        $this->render('products/index', ['products' => $products]);
    }

    public function create()
    {
        if ($this->isPost()) {
            try {
                $data = [
                    'name' => $this->getPostData()['name'],
                    'price' => floatval($this->getPostData()['price']),
                    'quantity' => intval($this->getPostData()['quantity']),
                    'variations' => isset($this->getPostData()['variations']) ? $this->getPostData()['variations'] : []
                ];

                $this->productService->createProduct($data);
                $this->setFlashMessage('success', 'Produto criado com sucesso!');
                $this->redirect('/products');
            } catch (ProductException $e) {
                $this->setFlashMessage('error', $e->getMessage());
            }
        }

        $this->render('products/create');
    }

    public function edit($id)
    {
        try {
            $data = $this->productService->getProduct($id);
            $product = $data['product'];
            $variations = $data['variations'];

            if ($this->isPost()) {
                $postData = [
                    'name' => $this->getPostData()['name'],
                    'price' => floatval($this->getPostData()['price']),
                    'quantity' => intval($this->getPostData()['quantity']),
                    'variations' => isset($this->getPostData()['variations']) ? $this->getPostData()['variations'] : []
                ];

                $this->productService->updateProduct($id, $postData);
                $this->setFlashMessage('success', 'Produto atualizado com sucesso!');
                $this->redirect('/products');
            }

            $this->render('products/edit', ['product' => $product, 'variations' => $variations]);
        } catch (ProductException $e) {
            $this->setFlashMessage('error', $e->getMessage());
            $this->redirect('/products');
        } catch (\Exception $e) {
            $this->setFlashMessage('error', $e->getMessage());
            $this->redirect('/products/' . $id . '/edit');
        }
    }

    public function delete($id)
    {
        try {
            $this->productService->deleteProduct($id);
            $this->setFlashMessage('success', 'Produto excluído com sucesso!');
        } catch (ProductException $e) {
            $this->setFlashMessage('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->setFlashMessage('error', $e->getMessage());
        }
        $this->redirect('/products');
    }

    public function view($id)
    {
        try {
            $data = $this->productService->getProduct($id);
            $this->render('products/view', ['product' => $data['product'], 'variations' => $data['variations']]);
        } catch (ProductException $e) {
            $this->setFlashMessage('error', $e->getMessage());
            $this->redirect('/products');
        }
    }

    public function editVariation($productId, $variationId)
    {
        try {
            $productData = $this->productService->getProduct($productId);
            $variation = $this->productService->getVariation($variationId);

            if ($this->isPost()) {
                $data = [
                    'name' => $this->getPostData()['name'],
                    'price_adjustment' => floatval($this->getPostData()['price_adjustment']),
                    'quantity' => intval($this->getPostData()['quantity'])
                ];

                $this->productService->updateVariation($variationId, $data);
                $this->setFlashMessage('success', 'Variação atualizada com sucesso!');
                $this->redirect("/products/{$productId}");
            }

            $this->render('products/variations/edit', [
                'product' => $productData['product'],
                'variation' => $variation
            ]);
        } catch (ProductException $e) {
            $this->setFlashMessage('error', $e->getMessage());
            $this->redirect("/products/{$productId}");
        } catch (\Exception $e) {
            $this->setFlashMessage('error', $e->getMessage());
            $this->redirect("/products/{$productId}/variations/{$variationId}/edit");
        }
    }
}
