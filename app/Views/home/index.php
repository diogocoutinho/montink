<div class="container">
    <!-- Featured Products -->
    <section class="mb-5">
        <h2 class="mb-4">Produtos em Destaque</h2>
        <div class="row">
            <?php if (count($featuredProducts) > 0): ?>
                <?php foreach ($featuredProducts as $product): ?>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                                <p class="card-text">
                                    Preço: R$ <?= number_format($product['price'], 2, ',', '.') ?><br>
                                    Estoque: <?= $product['quantity'] ?>
                                </p>
                                <a href="/products/<?= $product['id'] ?>" class="btn btn-primary">Ver Detalhes</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info">Nenhum produto em destaque disponível.</div>
                </div>
            <?php endif; ?>
        </div>
        <div class="text-center mt-3">
            <a href="/products" class="btn btn-outline-primary">Ver Todos os Produtos</a>
        </div>
    </section>

    <!-- Recent Orders -->
    <section class="mb-5">
        <h2 class="mb-4">Pedidos Recentes</h2>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td><?= $order['id'] ?></td>
                            <td><?= htmlspecialchars($order['customer_name']) ?></td>
                            <td>R$ <?= number_format($order['total_amount'], 2, ',', '.') ?></td>
                            <td>
                                <span class="badge bg-<?= $this->getStatusBadgeClass($order['status']) ?>">
                                    <?= ucfirst($order['status']) ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                            <td>
                                <a href="/orders/<?= $order['id'] ?>" class="btn btn-sm btn-info">Ver</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="text-center mt-3">
            <a href="/orders" class="btn btn-outline-primary">Ver Todos os Pedidos</a>
        </div>
    </section>
</div>