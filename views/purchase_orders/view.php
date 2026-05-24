<div class="container-fluid py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="<?= site_url('/purchase-orders') ?>" class="btn btn-outline-secondary me-3"><i class="fas fa-arrow-left"></i></a>
        <div><h4 class="fw-bold mb-0"><?= htmlspecialchars($order['order_number']) ?></h4><small class="text-muted"><?= $order['order_date'] ?></small></div>
        <span class="ms-auto badge bg-<?= $order['status'] === 'received' ? 'success' : ($order['status'] === 'cancelled' ? 'danger' : 'info') ?> fs-6"><?= ucfirst($order['status']) ?></span>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-md-4"><div class="card border-0 shadow-sm"><div class="card-body"><small class="text-muted d-block">Supplier</small><span class="fw-medium"><?= htmlspecialchars($order['supplier_name'] ?? '-') ?></span></div></div></div>
        <div class="col-md-4"><div class="card border-0 shadow-sm"><div class="card-body"><small class="text-muted d-block">Total Amount</small><span class="fw-medium"><?= currency_format($order['total_amount']) ?></span></div></div></div>
        <div class="col-md-4"><div class="card border-0 shadow-sm"><div class="card-body"><small class="text-muted d-block">Notes</small><span><?= htmlspecialchars($order['notes'] ?? '-') ?></span></div></div></div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom fw-bold py-3"><i class="fas fa-box me-2 text-primary"></i>Items</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light"><tr><th class="ps-4">Item</th><th>Qty Ordered</th><th>Received</th><th>Unit Price</th><th class="text-end pe-4">Total</th></tr></thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td class="ps-4"><?= htmlspecialchars($item['brand']) ?> <?= $item['size'] ?> (<?= $item['voltage'] ?>V)</td>
                            <td><?= $item['quantity'] ?></td>
                            <td><?= $item['received_quantity'] ?></td>
                            <td><?= currency_format($item['unit_price']) ?></td>
                            <td class="text-end pe-4"><?= currency_format($item['total_price']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
