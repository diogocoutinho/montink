<ul class="list-group mb-3">
    <?php foreach ($cart as $item): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <?= htmlspecialchars($item['name']) ?>
            <?php if ($item['variation_name']): ?>
                <small class="text-muted">(<?= htmlspecialchars($item['variation_name']) ?>)</small>
            <?php endif; ?>
            <span><?= $item['quantity'] ?> x R$ <?= number_format($item['price'], 2, ',', '.') ?></span>
        </li>
    <?php endforeach; ?>
    <li class="list-group-item d-flex justify-content-between align-items-center">
        Subtotal <span>R$ <?= number_format($subtotal, 2, ',', '.') ?></span>
    </li>
    <li class="list-group-item d-flex justify-content-between align-items-center">
        Frete <span>R$ <?= number_format($freight, 2, ',', '.') ?></span>
    </li>
    <?php if (!empty($coupon)): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            Cupom <span><?= htmlspecialchars($coupon['code']) ?></span>
        </li>
    <?php endif; ?>
    <?php if ($discount > 0): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            Desconto <span>- R$ <?= number_format($discount, 2, ',', '.') ?></span>
        </li>
    <?php endif; ?>
    <li class="list-group-item d-flex justify-content-between align-items-center fw-bold">
        Total <span>R$ <?= number_format($total, 2, ',', '.') ?></span>
    </li>
</ul>
<button type="submit" class="btn btn-success w-100">Finalizar Pedido</button>