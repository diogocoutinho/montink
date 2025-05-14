<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Cupons</h2>
        <a href="/coupons/create" class="btn btn-primary">Novo Cupom</a>
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
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">X</button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Desconto</th>
                    <th>Valor Mínimo</th>
                    <th>Válido De</th>
                    <th>Válido Até</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($coupons) > 0): ?>
                    <?php foreach ($coupons as $coupon): ?>
                        <tr>
                            <td><?= htmlspecialchars($coupon['code']) ?></td>
                            <td>
                                <?php if ($coupon['discount_percentage'] > 0): ?>
                                    <?= $coupon['discount_percentage'] ?>%
                                <?php else: ?>
                                    R$ <?= number_format($coupon['discount_amount'], 2, ',', '.') ?>
                                <?php endif; ?>
                            </td>
                            <td>R$ <?= number_format($coupon['min_order_value'], 2, ',', '.') ?></td>
                            <td><?= date('d/m/Y', strtotime($coupon['valid_from'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($coupon['valid_until'])) ?></td>
                            <td>
                                <span class="badge bg-<?= $coupon['is_active'] ? 'success' : 'danger' ?>">
                                    <?= $coupon['is_active'] ? 'Ativo' : 'Inativo' ?>
                                </span>
                            </td>
                            <td>
                                <a href="/coupons/<?= $coupon['id'] ?>/edit" class="btn btn-sm btn-primary">Editar</a>
                                <form action="/coupons/<?= $coupon['id'] ?>/delete" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este cupom?');">
                                    <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Nenhum cupom encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
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