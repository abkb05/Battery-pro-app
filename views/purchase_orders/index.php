<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="fas fa-clipboard-list me-2 text-primary"></i>Purchase Orders</h4>
        <a href="<?= site_url('/purchase-orders/create') ?>" class="btn btn-primary"><i class="fas fa-plus me-1"></i> New PO</a>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th class="ps-4">PO #</th><th>Supplier</th><th>Date</th><th>Amount</th><th>Status</th><th class="text-end pe-4">Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted"><i class="fas fa-clipboard fa-2x d-block mb-2"></i>No purchase orders yet.</td></tr>
                        <?php else: foreach ($orders as $o): ?>
                        <tr>
                            <td class="ps-4 fw-medium"><?= htmlspecialchars($o['order_number']) ?></td>
                            <td><?= htmlspecialchars($o['supplier_name'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($o['order_date']) ?></td>
                            <td><?= currency_format($o['total_amount']) ?></td>
                            <td><span class="badge bg-<?= $o['status'] === 'received' ? 'success' : ($o['status'] === 'cancelled' ? 'danger' : ($o['status'] === 'partial' ? 'warning' : 'info')) ?>"><?= ucfirst($o['status']) ?></span></td>
                            <td class="text-end pe-4">
                                <a href="<?= site_url('/purchase-orders/view/' . $o['id']) ?>" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a>
                                <?php if (in_array($o['status'], ['pending','ordered','partial'])): ?>
                                <a href="<?= site_url('/purchase-orders/receive/' . $o['id']) ?>" class="btn btn-sm btn-outline-success"><i class="fas fa-warehouse"></i></a>
                                <a href="<?= site_url('/purchase-orders/cancel/' . $o['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Cancel this PO?')"><i class="fas fa-times"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
