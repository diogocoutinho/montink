<?php

namespace App\Services;

use App\Repositories\OrderRepository;
use App\Exceptions\OrderException;

class OrderService
{
    private $repository;

    public function __construct()
    {
        $this->repository = new OrderRepository();
    }

    public function getAllOrders(): array
    {
        return $this->repository->findAll();
    }

    public function getOrder(int $id): array
    {
        $order = $this->repository->find($id);
        if (!$order) {
            throw new OrderException('Pedido não encontrado');
        }
        return $order;
    }

    public function createOrder(array $data): int
    {
        // Validação de dados
        $this->validateOrderData($data);

        // Criação do pedido
        return $this->repository->create($data);
    }

    public function updateOrderStatus(int $id, string $status): bool
    {
        $order = $this->repository->find($id);
        if (!$order) {
            throw new OrderException('Pedido não encontrado');
        }

        // Validação do status
        $this->validateStatus($status);

        return $this->repository->updateStatus($id, $status);
    }

    public function cancelOrder(int $id): bool
    {
        $order = $this->repository->find($id);
        if (!$order) {
            throw new OrderException('Pedido não encontrado');
        }

        return $this->repository->cancel($id);
    }

    public function createOrderFromCart(array $cartData): int
    {
        // Validação de CEP via ViaCEP
        $cep = preg_replace('/\D/', '', $cartData['address']['cep']);
        $viacep = @file_get_contents("https://viacep.com.br/ws/{$cep}/json/");
        $cepData = $viacep ? json_decode($viacep, true) : null;
        if (!$cepData || isset($cepData['erro'])) {
            throw new OrderException('CEP inválido. Verifique o endereço informado.');
        }
        // Monta itens do pedido
        $items = [];
        foreach ($cartData['items'] as $item) {
            $items[] = [
                'product_id' => $item['product_id'],
                'variation_id' => ($item['variation_id'] === null || $item['variation_id'] === '' || $item['variation_id'] === 0 || $item['variation_id'] === '0') ? null : $item['variation_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'total_price' => $item['price'] * $item['quantity']
            ];
        }
        // Busca cupom_id se houver
        $couponId = null;
        if (!empty($cartData['coupon']['code'])) {
            $couponRepo = new \App\Repositories\CouponRepository();
            $coupon = $couponRepo->findByCode($cartData['coupon']['code']);
            $couponId = $coupon['id'] ?? null;
        }
        $orderData = [
            'customer_name' => $cartData['customer_name'] ?? ($cartData['address']['logradouro'] . ', ' . $cartData['address']['numero']),
            'customer_email' => $cartData['customer_email'] ?? 'cliente@exemplo.com',
            'customer_phone' => null,
            'shipping_address' => $cartData['address']['logradouro'] . ', ' . $cartData['address']['numero'] . ' - ' . $cartData['address']['bairro'],
            'shipping_zipcode' => $cartData['address']['cep'],
            'shipping_city' => $cartData['address']['cidade'],
            'shipping_state' => $cartData['address']['uf'],
            'subtotal' => $cartData['subtotal'],
            'shipping_cost' => $cartData['freight'],
            'discount_amount' => $cartData['discount'],
            'total_amount' => $cartData['total'],
            'status' => 'pending',
            'coupon_id' => $couponId,
            'items' => $items
        ];
        // Cria o pedido
        $orderId = $this->createOrder($orderData);
        // Envia e-mail (stub)
        $this->sendOrderEmail($orderId, $orderData);
        return $orderId;
    }

    private function validateOrderData(array $data): void
    {
        if (empty($data['customer_name'])) {
            throw new OrderException('Nome do cliente é obrigatório');
        }

        if (empty($data['customer_email'])) {
            throw new OrderException('Email do cliente é obrigatório');
        }

        if (empty($data['shipping_address'])) {
            throw new OrderException('Endereço de entrega é obrigatório');
        }

        if (empty($data['shipping_zipcode'])) {
            throw new OrderException('CEP é obrigatório');
        }

        if (!isset($data['items']) || empty($data['items'])) {
            throw new OrderException('O pedido deve conter pelo menos um item');
        }
    }

    private function validateStatus(string $status): void
    {
        $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            throw new OrderException('Status inválido');
        }
    }

    public function processWebhook(array $payload): void
    {
        $this->repository->processWebhook($payload);
    }

    private function sendOrderEmail($orderId, $orderData)
    {
        require_once __DIR__ . '/../../vendor/autoload.php';
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = getenv('MAIL_HOST');
            $mail->Port = getenv('MAIL_PORT');
            $mail->SMTPAuth = getenv('MAIL_SMTPAUTH') === 'true' || getenv('MAIL_SMTPAUTH') === '1' || getenv('MAIL_SMTPAUTH') === true;
            $mail->Username = getenv('MAIL_USERNAME');
            $mail->Password = getenv('MAIL_PASSWORD');
            $encryption = getenv('MAIL_ENCRYPTION');
            if ($encryption) {
                $mail->SMTPSecure = $encryption;
            }
            $from = getenv('MAIL_FROM_ADDRESS') ?: 'no-reply@seudominio.com';
            $fromName = getenv('MAIL_FROM_NAME') ?: 'Loja Exemplo';
            $mail->setFrom($from, $fromName);
            $mail->addAddress($orderData['customer_email'], $orderData['customer_name']);
            $mail->Subject = 'Seu pedido #' . $orderId . ' foi realizado com sucesso!';
            // Renderiza a view do e-mail
            ob_start();
            include __DIR__ . '/../Views/orders/email.php';
            $body = ob_get_clean();
            $mail->isHTML(true);
            $mail->Body = $body;
            $mail->AltBody = strip_tags($body);
            $mail->send();
        } catch (\Exception $e) {
            error_log('Erro ao enviar e-mail de pedido: ' . $mail->ErrorInfo);
        }
    }

    public function getOrderItems($orderId)
    {
        return $this->repository->getOrderItems($orderId);
    }
}
