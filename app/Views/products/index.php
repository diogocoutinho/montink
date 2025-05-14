<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Produtos</h1>
        <a href="/products/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Novo Produto
        </a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['success'];
            unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['error'];
            unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <?php foreach ($products as $product): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                        <p class="card-text">
                            <strong>Preço:</strong> R$ <?php echo number_format($product['price'], 2, ',', '.'); ?><br>
                            <strong>Estoque:</strong> <?php echo $product['quantity']; ?> unidades
                        </p>
                        <div class="d-flex justify-content-between mt-3">
                            <a href="/products/<?php echo $product['id']; ?>" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                            <a href="/products/<?php echo $product['id']; ?>/edit" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <button type="button" class="btn btn-danger btn-sm"
                                onclick="confirmDelete(<?php echo $product['id']; ?>)">
                                <i class="fas fa-trash"></i> Excluir
                            </button>
                        </div>
                        <form method="post" action="/cart/add" class="mt-2">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-success btn-sm w-100 mt-1">
                                <i class="fas fa-shopping-cart"></i> Comprar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    function confirmDelete(id) {
        if (confirm('Tem certeza que deseja excluir este produto?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/products/' + id + '/delete';
            document.body.appendChild(form);
            form.submit();
        }
    }
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 3000);
</script>