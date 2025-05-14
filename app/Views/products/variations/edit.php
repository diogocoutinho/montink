<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Editar Variação</h1>
        <a href="/products/<?= $product['id'] ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
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
            <form action="/products/<?= $product['id'] ?>/variations/<?= $variation['id'] ?>/edit" method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Nome da Variação</label>
                    <input type="text" class="form-control" id="name" name="name"
                        value="<?= htmlspecialchars($variation['name']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="price_adjustment" class="form-label">Ajuste de Preço</label>
                    <div class="input-group">
                        <span class="input-group-text">R$</span>
                        <input type="number" class="form-control" id="price_adjustment" name="price_adjustment"
                            value="<?= $variation['price_adjustment'] ?>" step="0.01" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantidade em Estoque</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" value="<?= $variation['quantity'] ?>" min="0" required>
                </div>

                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar Alterações
                    </button>
                </div>
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