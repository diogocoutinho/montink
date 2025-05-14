<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Coupon;

class CouponController extends Controller
{
    private $coupon;

    public function __construct()
    {
        $this->coupon = new Coupon();
    }

    public function index()
    {
        $coupons = $this->coupon->findAll();
        return $this->render('coupons/index', [
            'coupons' => $coupons
        ]);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $discountType = $_POST['discount_type'] ?? 'percentage';
            $discountPercentage = $discountType === 'percentage' ? ($_POST['discount_percentage'] ?? 0) : 0;
            $discountAmount = $discountType === 'amount' ? ($_POST['discount_amount'] ?? 0) : 0;
            $data = [
                'code' => $_POST['code'],
                'discount_percentage' => $discountPercentage,
                'discount_amount' => $discountAmount,
                'min_order_value' => $_POST['min_order_value'] ?? 0,
                'valid_from' => $_POST['valid_from'],
                'valid_until' => $_POST['valid_until'],
                'is_active' => isset($_POST['is_active'])
            ];

            try {
                $this->coupon->create($data);
                $_SESSION['success'] = 'Cupom criado com sucesso!';
                $this->redirect('/coupons');
            } catch (\Exception $e) {
                $_SESSION['error'] = 'Erro ao criar cupom: ' . $e->getMessage();
            }
        }

        return $this->render('coupons/create');
    }

    public function edit($id)
    {
        $coupon = $this->coupon->find($id);

        if (!$coupon) {
            $_SESSION['error'] = 'Cupom não encontrado';
            $this->redirect('/coupons');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'code' => $_POST['code'],
                'discount_percentage' => $_POST['discount_percentage'] ?? 0,
                'discount_amount' => $_POST['discount_amount'] ?? 0,
                'min_order_value' => $_POST['min_order_value'] ?? 0,
                'valid_from' => $_POST['valid_from'],
                'valid_until' => $_POST['valid_until'],
                'is_active' => isset($_POST['is_active'])
            ];

            try {
                $this->coupon->update($id, $data);
                $_SESSION['success'] = 'Cupom atualizado com sucesso!';
                $this->redirect('/coupons');
            } catch (\Exception $e) {
                $_SESSION['error'] = 'Erro ao atualizar cupom: ' . $e->getMessage();
            }
        }

        return $this->render('coupons/edit', [
            'coupon' => $coupon
        ]);
    }

    public function delete($id)
    {
        try {
            $this->coupon->delete($id);
            $_SESSION['success'] = 'Cupom excluído com sucesso!';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erro ao excluir cupom: ' . $e->getMessage();
        }

        $this->redirect('/coupons');
    }

    public function validate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            return;
        }

        $code = $_GET['code'] ?? '';
        $subtotal = $_GET['subtotal'] ?? 0;
        $coupon = $this->coupon->validate($code, $subtotal);

        header('Content-Type: application/json');
        if ($coupon) {
            echo json_encode([
                'valid' => true,
                'coupon' => $coupon
            ]);
        } else {
            echo json_encode([
                'valid' => false,
                'message' => 'Cupom inválido ou não aplicável'
            ]);
        }
    }
}
