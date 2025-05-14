<?php
// @var $cart array
// @var $subtotal float
// @var $freight float
// @var $discount float
// @var $total float
// @var $coupon array|null
?>
<div class="container mt-4">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">X</button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    <h2>Finalizar Pedido</h2>
    <?php if (empty($cart)): ?>
        <div class="alert alert-info">Seu carrinho está vazio.</div>
        <a href="/products" class="btn btn-primary">Ver Produtos</a>
    <?php else: ?>
        <form method="post" action="/cart/checkout" id="checkout-form">
            <div class="row">
                <div class="col-md-7">
                    <h4>Endereço de Entrega</h4>
                    <div class="mb-3">
                        <label for="customer_name" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="customer_email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="customer_email" name="customer_email" required>
                    </div>
                    <div class="mb-3">
                        <label for="cep" class="form-label">CEP</label>
                        <input type="text" class="form-control" id="cep" name="cep" required maxlength="9">
                    </div>
                    <div class="mb-3">
                        <label for="logradouro" class="form-label">Logradouro</label>
                        <input type="text" class="form-control" id="logradouro" name="logradouro" required>
                    </div>
                    <div class="mb-3">
                        <label for="numero" class="form-label">Número</label>
                        <input type="text" class="form-control" id="numero" name="numero" required>
                    </div>
                    <div class="mb-3">
                        <label for="bairro" class="form-label">Bairro</label>
                        <input type="text" class="form-control" id="bairro" name="bairro" required>
                    </div>
                    <div class="mb-3">
                        <label for="cidade" class="form-label">Cidade</label>
                        <input type="text" class="form-control" id="cidade" name="cidade" required>
                    </div>
                    <div class="mb-3">
                        <label for="uf" class="form-label">UF</label>
                        <input type="text" class="form-control" id="uf" name="uf" required maxlength="2">
                    </div>
                    <div class="mb-3">
                        <label for="complemento" class="form-label">Complemento</label>
                        <input type="text" class="form-control" id="complemento" name="complemento">
                    </div>
                    <h4>Aplicar Cupom</h4>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="coupon_code" name="coupon_code" placeholder="Código do cupom" value="<?= $coupon['code'] ?? '' ?>">
                        <button class="btn btn-outline-secondary" type="button" id="apply-coupon-btn">Aplicar</button>
                    </div>
                    <div id="coupon-message"></div>
                </div>
                <div class="col-md-5">
                    <h4>Resumo do Pedido</h4>
                    <div id="order-summary">
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
                    </div>
                </div>
            </div>
        </form>
        <script>
            document.getElementById('cep').addEventListener('blur', function() {
                var cep = this.value.replace(/\D/g, '');
                if (cep.length === 8) {
                    fetch('https://viacep.com.br/ws/' + cep + '/json/')
                        .then(response => response.json())
                        .then(data => {
                            if (!data.erro) {
                                document.getElementById('logradouro').value = data.logradouro;
                                document.getElementById('bairro').value = data.bairro;
                                document.getElementById('cidade').value = data.localidade;
                                document.getElementById('uf').value = data.uf;
                                document.getElementById('complemento').value = data.complemento;
                            }
                        });
                }
            });

            document.getElementById('apply-coupon-btn').addEventListener('click', function() {
                var code = document.getElementById('coupon_code').value.trim();
                var messageDiv = document.getElementById('coupon-message');
                if (!code) {
                    messageDiv.innerHTML = '<div class="alert alert-warning py-2">Digite o código do cupom.</div>';
                    return;
                }
                // Envia via POST para /cart/apply-coupon
                fetch('/cart/apply-coupon', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'coupon_code=' + encodeURIComponent(code)
                    })
                    .then(response => response.text())
                    .then(() => {
                        // Atualiza o resumo do pedido via AJAX
                        fetch('/cart/checkout/summary')
                            .then(res => res.text())
                            .then(html => {
                                document.getElementById('order-summary').innerHTML = html;
                                messageDiv.innerHTML = '<div class="alert alert-success py-2">Cupom aplicado: ' + code + '</div>';
                            });
                    })
                    .catch(() => {
                        messageDiv.innerHTML = '<div class="alert alert-danger py-2">Erro ao validar cupom.</div>';
                    });
            });

            setTimeout(function() {
                var alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                        var bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    } else {
                        alert.style.display = 'none';
                    }
                });
                <?php unset($_SESSION['error']); ?>
                console.log('Flash messages auto-hidden');
            }, 3000);
        </script>
    <?php endif; ?>
</div>