<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Novo Pedido</h2>
        <a href="/orders" class="btn btn-secondary">Voltar</a>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form action="/orders" method="POST">
                <h5 class="mb-3">Dados do Cliente</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="customer_name" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="customer_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="customer_email" name="customer_email" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="customer_phone" class="form-label">Telefone</label>
                        <input type="text" class="form-control" id="customer_phone" name="customer_phone">
                    </div>
                </div>

                <h5 class="mb-3">Endereço de Entrega</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="shipping_address" class="form-label">Endereço</label>
                        <input type="text" class="form-control" id="shipping_address" name="shipping_address" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="shipping_zipcode" class="form-label">CEP</label>
                        <input type="text" class="form-control" id="shipping_zipcode" name="shipping_zipcode" required>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="shipping_city" class="form-label">Cidade</label>
                        <input type="text" class="form-control" id="shipping_city" name="shipping_city" required>
                    </div>
                    <div class="col-md-1 mb-3">
                        <label for="shipping_state" class="form-label">UF</label>
                        <input type="text" class="form-control" id="shipping_state" name="shipping_state" maxlength="2" required>
                    </div>
                </div>

                <h5 class="mb-3">Itens do Pedido</h5>
                <div id="order-items">
                    <div class="alert alert-info">Adicione produtos ao pedido usando o carrinho.</div>
                    <?php if (empty($cartItems)): ?>
                        <div class="alert alert-info">Carrinho vazio.</div>
                    <?php else: ?>
                        <?php foreach ($cartItems as $item): ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span><?= $item['name'] ?></span>
                                    <span>R$ <?= number_format($item['price'], 2, ',', '.') ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <h5 class="mb-3">Resumo</h5>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="subtotal" class="form-label">Subtotal</label>
                        <input type="text" class="form-control" id="subtotal" name="subtotal" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="shipping_cost" class="form-label">Frete</label>
                        <input type="text" class="form-control" id="shipping_cost" name="shipping_cost" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="discount_amount" class="form-label">Desconto</label>
                        <input type="text" class="form-control" id="discount_amount" name="discount_amount" readonly>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="total_amount" class="form-label">Total</label>
                    <input type="text" class="form-control" id="total_amount" name="total_amount" readonly>
                </div>

                <button type="submit" class="btn btn-primary">Criar Pedido</button>
            </form>
        </div>
    </div>
</div>

<script>
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 3000);
</script>