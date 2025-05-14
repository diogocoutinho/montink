<?php
// @var $cart array
// @var $subtotal float
// @var $freight float
// @var $discount float
// @var $total float
// @var $coupon array|null
?>
<div class="container mt-4">
    <h2>Carrinho de Compras</h2>
    <?php if (empty($cart)): ?>
        <div class="alert alert-info">Seu carrinho está vazio.</div>
        <a href="/products" class="btn btn-primary">Ver Produtos</a>
    <?php else: ?>
        <form method="post" action="/cart/update">
            <table class="table table-bordered table-striped mt-3">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Variação</th>
                        <th>Preço</th>
                        <th>Quantidade</th>
                        <th>Subtotal</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td><?= htmlspecialchars($item['variation_name'] ?? '-') ?></td>
                            <td>R$ <?= number_format($item['price'], 2, ',', '.') ?></td>
                            <td>
                                <input type="number" name="quantities[<?= $item['product_id'] ?>][<?= $item['variation_id'] ?? 'null' ?>]" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock'] ?>" class="form-control" style="width:80px;">
                            </td>
                            <td>R$ <?= number_format($item['price'] * $item['quantity'], 2, ',', '.') ?></td>
                            <td>
                                <button type="submit" formaction="/cart/update" name="update" value="<?= $item['product_id'] ?>:<?= $item['variation_id'] ?? 'null' ?>" class="btn btn-sm btn-info">Atualizar</button>
                                <button type="submit" formaction="/cart/remove" name="remove" value="<?= $item['product_id'] ?>:<?= $item['variation_id'] ?? 'null' ?>" class="btn btn-sm btn-danger">Remover</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </form>
        <div class="row">
            <div class="col-md-6">
                <form method="post" action="/cart/clear">
                    <button type="submit" class="btn btn-warning">Limpar Carrinho</button>
                </form>
            </div>
            <div class="col-md-6 text-end">
                <div class="alert alert-info mb-2" style="font-size: 0.95em;">
                    <strong>Regras de Frete:</strong>
                    <span data-bs-toggle="tooltip" title="O frete é calculado automaticamente conforme o subtotal do seu pedido.">
                        <i class="fas fa-info-circle"></i>
                    </span><br>
                    • Subtotal entre <strong>R$52,00</strong> e <strong>R$166,59</strong>: frete <strong>R$15,00</strong>.<br>
                    • Subtotal acima de <strong>R$200,00</strong>: <strong>frete grátis</strong>.<br>
                    • Outros valores: frete <strong>R$20,00</strong>.
                </div>
                <?php if ($subtotal < 200): ?>
                    <div class="alert alert-warning py-2" style="font-size:0.95em;">
                        <?php if ($subtotal < 52): ?>
                            Faltam <strong>R$ <?= number_format(52 - $subtotal, 2, ',', '.') ?></strong> para reduzir o frete para R$15,00.<br>
                        <?php elseif ($subtotal <= 166.59): ?>
                            Faltam <strong>R$ <?= number_format(200 - $subtotal, 2, ',', '.') ?></strong> para <strong>frete grátis</strong>!
                        <?php else: ?>
                            Faltam <strong>R$ <?= number_format(200 - $subtotal, 2, ',', '.') ?></strong> para <strong>frete grátis</strong>!
                        <?php endif; ?>
                    </div>
                <?php elseif ($freight == 0): ?>
                    <div class="alert alert-success py-2" style="font-size:0.95em;">
                        <i class="fas fa-truck"></i> Parabéns! Você ganhou <strong>frete grátis</strong>!
                    </div>
                <?php endif; ?>
                <ul class="list-group mb-2">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Subtotal <span>R$ <?= number_format($subtotal, 2, ',', '.') ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center <?php if ($freight == 0) echo 'list-group-item-success fw-bold'; ?>">
                        Frete <span><?= $freight == 0 ? '<i class="fas fa-gift"></i> Grátis' : 'R$ ' . number_format($freight, 2, ',', '.') ?></span>
                    </li>
                    <?php if ($discount > 0): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Desconto <span>- R$ <?= number_format($discount, 2, ',', '.') ?></span>
                        </li>
                    <?php endif; ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center fw-bold">
                        Total <span>R$ <?= number_format($total, 2, ',', '.') ?></span>
                    </li>
                </ul>
                <a href="/cart/checkout" class="btn btn-success mt-3">Finalizar Pedido</a>
                <script>
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                        new bootstrap.Tooltip(tooltipTriggerEl);
                    });
                </script>
            </div>
        </div>
    <?php endif; ?>
</div>