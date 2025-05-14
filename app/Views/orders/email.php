<h2>Pedido #<?= htmlspecialchars($orderId) ?> confirmado!</h2>
<p>Olá, <b><?= htmlspecialchars($orderData['customer_name']) ?></b>!</p>
<p>Recebemos seu pedido e ele está sendo processado.</p>
<h4>Endereço de entrega:</h4>
<p>
    <?= htmlspecialchars($orderData['shipping_address']) ?><br>
    <?= htmlspecialchars($orderData['shipping_zipcode']) ?> -
    <?= htmlspecialchars($orderData['shipping_city']) ?>,
    <?= htmlspecialchars($orderData['shipping_state']) ?>
</p>
<p><b>Total:</b> R$ <?= number_format($orderData['total_amount'], 2, ',', '.') ?></p>