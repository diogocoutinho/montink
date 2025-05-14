<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detalhes do Produto</h2>
        <div>
            <a href="/products/<?= $product['id'] ?>/edit" class="btn btn-primary">Editar</a>
            <form action="/products/<?= $product['id'] ?>/delete" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este produto?');">
                <button type="submit" class="btn btn-danger">Excluir</button>
            </form>
        </div>
    </div>

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
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <h3 class="card-title"><?= htmlspecialchars($product['name']) ?></h3>
            <div class="row mt-4">
                <div class="col-md-6">
                    <p><strong>Preço:</strong> R$ <?= number_format($product['price'], 2, ',', '.') ?></p>
                    <p><strong>Estoque (soma das variações):</strong> <?= $product['quantity'] ?></p>
                </div>
                <?php if (!empty($variations)): ?>
                    <div class="col-md-6">
                        <p class="text-muted small">O estoque do produto principal é a soma dos estoques das variações abaixo.</p>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($variations)): ?>
                <h4 class="mt-4">Variações</h4>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Ajuste de Preço</th>
                                <th>Estoque</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($variations as $variation): ?>
                                <tr>
                                    <td><?= htmlspecialchars($variation['name']) ?></td>
                                    <td>R$ <?= number_format($variation['price_adjustment'], 2, ',', '.') ?></td>
                                    <td><?= $variation['quantity'] ?></td>
                                    <td>
                                        <a href="/products/<?= $product['id'] ?>/variations/<?= $variation['id'] ?>/edit" class="btn btn-sm btn-primary">Editar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <hr>
            <div class="mb-2">
                <strong>Preço base:</strong> R$ <?= number_format($product['price'], 2, ',', '.') ?>
            </div>
            <form method="post" action="/cart/add" class="row g-2 align-items-end mt-3">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <?php if (!empty($variations)): ?>
                    <div class="col-md-4">
                        <label for="variation_id" class="form-label">Variação</label>
                        <select name="variation_id" id="variation_id" class="form-select" required>
                            <option value="">Selecione</option>
                            <?php foreach ($variations as $variation): ?>
                                <option value="<?= $variation['id'] ?>" data-stock="<?= $variation['quantity'] ?>" data-price="<?= $product['price'] + $variation['price_adjustment'] ?>">
                                    <?= htmlspecialchars($variation['name']) ?> (R$ <?= number_format($product['price'] + $variation['price_adjustment'], 2, ',', '.') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
                <div class="col-md-3">
                    <label for="quantity" class="form-label">Quantidade</label>
                    <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1" max="<?= $product['quantity'] ?>" required>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-shopping-cart"></i> Comprar
                    </button>
                </div>
            </form>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var variationSelect = document.getElementById('variation_id');
                    var quantityInput = document.getElementById('quantity');
                    if (variationSelect) {
                        variationSelect.addEventListener('change', function() {
                            var selected = this.options[this.selectedIndex];
                            var stock = selected.getAttribute('data-stock');
                            if (stock) {
                                quantityInput.max = stock;
                            } else {
                                quantityInput.max = <?= $product['quantity'] ?>;
                            }
                        });
                    }
                });
            </script>
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