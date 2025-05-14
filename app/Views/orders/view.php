<style>
    @media print {

        .btn,
        .d-flex.justify-content-end,
        .btn-secondary,
        .btn-primary,
        .btn-danger {
            display: none !important;
        }

        body {
            background: #fff !important;
        }

        .card {
            box-shadow: none !important;
        }
    }
</style>
<div class="container">
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Detalhes do Pedido #<?= $order['id'] ?></h2>
                <div>
                    <a href="/orders" class="btn btn-secondary">Voltar</a>
                    <button class="btn btn-outline-primary" onclick="window.print()">
                        Imprimir
                    </button>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Dados do Cliente</h5>
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Nome:</strong> <?= htmlspecialchars($order['customer_name']) ?></li>
                        <li class="list-group-item"><strong>Email:</strong> <?= htmlspecialchars($order['customer_email']) ?></li>
                        <li class="list-group-item"><strong>Telefone:</strong> <?= htmlspecialchars($order['customer_phone'] ?? '-') ?></li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>Endereço de Entrega</h5>
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Endereço:</strong> <?= htmlspecialchars($order['shipping_address']) ?></li>
                        <li class="list-group-item"><strong>CEP:</strong> <?= htmlspecialchars($order['shipping_zipcode']) ?></li>
                        <li class="list-group-item"><strong>Cidade:</strong> <?= htmlspecialchars($order['shipping_city']) ?></li>
                        <li class="list-group-item"><strong>Estado:</strong> <?= htmlspecialchars($order['shipping_state']) ?></li>
                    </ul>
                </div>
            </div>

            <h5>Itens do Pedido</h5>
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Variação</th>
                            <th>Quantidade</th>
                            <th>Preço Unitário</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order['items'] as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['product_name']) ?></td>
                                <td><?= htmlspecialchars($item['variation_name'] ?? '-') ?></td>
                                <td><?= $item['quantity'] ?></td>
                                <td>R$ <?= number_format($item['unit_price'], 2, ',', '.') ?></td>
                                <td>R$ <?= number_format($item['total_price'], 2, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Status do Pedido</h5>
                    <span class="badge bg-<?= $this->getStatusBadgeClass($order['status']) ?>">
                        <?= ucfirst($order['status']) ?>
                    </span>
                </div>
                <div class="col-md-6">
                    <h5>Resumo Financeiro</h5>
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Subtotal:</strong> R$ <?= number_format($order['subtotal'], 2, ',', '.') ?></li>
                        <li class="list-group-item"><strong>Frete:</strong> R$ <?= number_format($order['shipping_cost'], 2, ',', '.') ?></li>
                        <li class="list-group-item"><strong>Desconto:</strong> R$ <?= number_format($order['discount_amount'], 2, ',', '.') ?></li>
                        <li class="list-group-item"><strong>Total:</strong> <span class="fw-bold">R$ <?= number_format($order['total_amount'], 2, ',', '.') ?></span></li>
                    </ul>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <a href="/orders/<?= $order['id'] ?>/edit" class="btn btn-primary me-2">Editar</a>
                <form action="/orders/<?= $order['id'] ?>/delete" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este pedido?');">
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>