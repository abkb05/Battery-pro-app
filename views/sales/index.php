<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0"><i class="fas fa-cash-register me-2 text-primary"></i>Sales</h4>
        </div>
        <a href="<?= site_url('/export/sales') ?>" class="btn btn-outline-success me-2"><i class="fas fa-file-excel me-1"></i> Export</a>
        <a href="<?= site_url('/sales/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> New Sale
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Cashier</th>
                            <th>Amount</th>
                            <th>Profit</th>
                            <th>Payment</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($sales)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fas fa-receipt fa-2x d-block mb-2"></i>No sales recorded yet.
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($sales as $sale): ?>
                        <tr>
                            <td class="ps-4"><?= $sale['id'] ?></td>
                            <td><span class="text-nowrap"><?= htmlspecialchars($sale['sale_date']) ?></span></td>
                            <td><?= htmlspecialchars($sale['customer_name'] ?? 'Walk-in') ?></td>
                            <td><?= htmlspecialchars($sale['cashier'] ?? '-') ?></td>
                            <td class="fw-medium"><?= currency_format($sale['total_amount']) ?></td>
                            <td class="text-success"><?= currency_format($sale['total_profit']) ?></td>
                            <td>
                                <span class="badge bg-<?= $sale['payment_status'] === 'paid' ? 'success' : ($sale['payment_status'] === 'partial' ? 'warning' : 'danger') ?>">
                                    <?= ucfirst($sale['payment_status']) ?>
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="<?= site_url('/sales/view/' . $sale['id']) ?>" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
