<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Editar Produto</h1>
        <a href="/products" class="btn btn-secondary">
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
            <form action="/products/<?= $product['id'] ?? '' ?>/edit" method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Nome do Produto</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($product['name'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Preço</label>
                    <div class="input-group">
                        <span class="input-group-text">R$</span>
                        <input type="number" class="form-control" id="price" name="price" value="<?= htmlspecialchars($product['price'] ?? '0') ?>" step="0.01" min="0" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantidade em Estoque</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" value="<?= htmlspecialchars($product['quantity'] ?? '0') ?>" min="0" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Variações</label>
                    <div id="variations">
                        <?php if (!empty($variations)): ?>
                            <?php foreach ($variations as $variation): ?>
                                <div class="variation-item mb-3">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="variations[<?= $variation['id'] ?>][name]" value="<?= htmlspecialchars($variation['name']) ?>" placeholder="Nome da Variação">
                                        </div>
                                        <div class="col-md-3">
                                            <div class="input-group">
                                                <span class="input-group-text">R$</span>
                                                <input type="number" class="form-control" name="variations[<?= $variation['id'] ?>][price_adjustment]" value="<?= htmlspecialchars($variation['price_adjustment']) ?>" step="0.01" placeholder="Ajuste de Preço">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number" class="form-control" name="variations[<?= $variation['id'] ?>][quantity]" value="<?= htmlspecialchars($variation['quantity']) ?>" min="0" placeholder="Estoque">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger remove-variation">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <input type="hidden" name="variations[<?= $variation['id'] ?>][id]" value="<?= $variation['id'] ?>">
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="variation-item mb-3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="variations[0][name]" placeholder="Nome da Variação">
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <span class="input-group-text">R$</span>
                                            <input type="number" class="form-control" name="variations[0][price_adjustment]" step="0.01" placeholder="Ajuste de Preço">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" class="form-control" name="variations[0][quantity]" min="0" value="0" placeholder="Estoque">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger remove-variation">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="btn btn-secondary" id="add-variation">
                        <i class="fas fa-plus"></i> Adicionar Variação
                    </button>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let variationCount = <?= !empty($variations) ? count($variations) : 1 ?>;

    document.getElementById('add-variation').addEventListener('click', function() {
        const variations = document.getElementById('variations');
        const newVariation = document.createElement('div');
        newVariation.className = 'variation-item mb-3';
        newVariation.innerHTML = `
        <div class="row">
            <div class="col-md-4">
                <input type="text" class="form-control" name="variations[${variationCount}][name]" placeholder="Nome da Variação">
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <input type="number" class="form-control" name="variations[${variationCount}][price_adjustment]" step="0.01" placeholder="Ajuste de Preço">
                </div>
            </div>
            <div class="col-md-3">
                <input type="number" class="form-control" name="variations[${variationCount}][quantity]" min="0" value="0" placeholder="Estoque">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger remove-variation">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
        variations.appendChild(newVariation);
        variationCount++;
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-variation') ||
            e.target.parentElement.classList.contains('remove-variation')) {
            const button = e.target.classList.contains('remove-variation') ? e.target : e.target.parentElement;
            button.closest('.variation-item').remove();
        }
    });

    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 3000);
</script>