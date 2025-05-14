<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Novo Cupom</h2>
        <a href="/coupons" class="btn btn-secondary">Voltar</a>
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
            <form method="post" action="/coupons/create">
                <div class="mb-3">
                    <label for="code" class="form-label">Código do Cupom</label>
                    <input type="text" class="form-control" id="code" name="code" required>
                </div>
                <div class="mb-3">
                    <label for="discount_type" class="form-label">Tipo de Desconto</label>
                    <select class="form-select" id="discount_type" name="discount_type" required>
                        <option value="percentage">Porcentagem</option>
                        <option value="fixed">Valor Fixo</option>
                    </select>
                </div>
                <div class="mb-3" id="percentage_field">
                    <label for="discount_percentage" class="form-label">Porcentagem de Desconto</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="discount_percentage" name="discount_percentage" min="0" max="100" step="0.01">
                        <span class="input-group-text">%</span>
                    </div>
                </div>

                <div class="mb-3" id="fixed_field" style="display: none;">
                    <label for="discount_amount" class="form-label">Valor do Desconto</label>
                    <div class="input-group">
                        <span class="input-group-text">R$</span>
                        <input type="number" class="form-control" id="discount_amount" name="discount_amount" min="0" step="0.01">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="min_order_value" class="form-label">Valor Mínimo do Pedido</label>
                    <input type="number" class="form-control" id="min_order_value" name="min_order_value" min="0" step="0.01" required>
                </div>
                <div class="mb-3">
                    <label for="valid_from" class="form-label">Válido De</label>
                    <input type="date" class="form-control" id="valid_from" name="valid_from" required>
                </div>
                <div class="mb-3">
                    <label for="valid_until" class="form-label">Válido Até</label>
                    <input type="date" class="form-control" id="valid_until" name="valid_until" required>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                    <label class="form-check-label" for="is_active">Ativo</label>
                </div>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const discountType = document.getElementById('discount_type');
        const percentageField = document.getElementById('percentage_field');
        const fixedField = document.getElementById('fixed_field');

        discountType.addEventListener('change', function() {
            if (this.value === 'percentage') {
                percentageField.style.display = 'block';
                fixedField.style.display = 'none';
                document.getElementById('discount_percentage').required = true;
                document.getElementById('discount_amount').required = false;
            } else {
                percentageField.style.display = 'none';
                fixedField.style.display = 'block';
                document.getElementById('discount_percentage').required = false;
                document.getElementById('discount_amount').required = true;
            }
        });
    });

    function toggleDiscountFields() {
        var type = document.getElementById('discount_type').value;
        document.getElementById('percentage_field').classList.toggle('d-none', type !== 'percentage');
        document.getElementById('amount_field').classList.toggle('d-none', type !== 'amount');
    }
    document.addEventListener('DOMContentLoaded', toggleDiscountFields);
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 3000);
</script>