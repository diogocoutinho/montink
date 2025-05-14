<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Montink ERP</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="/css/style.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">Montink ERP</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/products">Produtos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/orders">Pedidos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/coupons">Cupons</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/cart">
                            <i class="fas fa-shopping-cart"></i> Carrinho
                            <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                                <span class="badge bg-primary cart-count"><?= count($_SESSION['cart']) ?></span>
                            <?php else: ?>
                                <span class="badge bg-primary cart-count">0</span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php echo $content ?? ''; ?>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom JS -->
    <script src="/js/main.js"></script>
    <script>
        // setTimeout(function() {
        //     var alerts = document.querySelectorAll('.alert');
        //     alerts.forEach(function(alert) {
        //         if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
        //             var bsAlert = new bootstrap.Alert(alert);
        //             bsAlert.close();
        //         } else {
        //             alert.style.display = 'none';
        //         }
        //     });
        // }, 3000);
    </script>
</body>

</html>