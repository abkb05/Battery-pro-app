<div class="container-fluid py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="<?= site_url('/sales') ?>" class="btn btn-outline-secondary me-3"><i class="fas fa-arrow-left"></i></a>
        <div>
            <h4 class="fw-bold mb-0"><i class="fas fa-receipt me-2 text-primary"></i>Sale #<?= $sale['id'] ?></h4>
            <small class="text-muted"><?= htmlspecialchars($sale['sale_date']) ?></small>
        </div>
        <span class="ms-auto badge bg-<?= $sale['payment_status'] === 'paid' ? 'success' : ($sale['payment_status'] === 'partial' ? 'warning' : 'danger') ?> fs-6">
            <?= ucfirst($sale['payment_status']) ?>
        </span>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted d-block">Customer</small>
                    <span class="fw-medium"><?= htmlspecialchars($sale['customer_name'] ?? 'Walk-in') ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted d-block">Cashier</small>
                    <span class="fw-medium"><?= htmlspecialchars($sale['cashier'] ?? '-') ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted d-block">Payment Method</small>
                    <span class="fw-medium"><?= ucfirst($sale['payment_method']) ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-bottom fw-bold py-3">
            <i class="fas fa-box me-2 text-primary"></i>Items
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Item</th>
                            <th>Serial No.</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th class="text-end pe-4">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td class="ps-4"><?= htmlspecialchars($item['brand']) ?> <?= $item['size'] ?> (<?= $item['voltage'] ?>V / <?= $item['plates'] ?>pl)</td>
                            <td><?= htmlspecialchars($item['serial_number'] ?: '-') ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td><?= currency_format($item['unit_price']) ?></td>
                            <td class="text-end pe-4 fw-medium"><?= currency_format($item['total_price']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <?php if (!empty($exchange) && $exchange['exchange_value'] > 0): ?>
                        <tr>
                            <td colspan="4" class="text-end ps-4 text-danger">
                                <i class="fas fa-exchange-alt me-1"></i>Exchange: <?= htmlspecialchars($exchange['old_brand'] ?: 'Old Battery') ?> (<?= ucfirst($exchange['old_condition']) ?>)
                            </td>
                            <td class="text-end pe-4 text-danger">-<?= currency_format($exchange['exchange_value']) ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td colspan="4" class="text-end ps-4 fw-bold">Total</td>
                            <td class="text-end pe-4 fw-bold"><?= currency_format($sale['total_amount']) ?></td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end ps-4">Profit</td>
                            <td class="text-end pe-4 text-success"><?= currency_format($sale['total_profit']) ?></td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end ps-4">Amount Paid</td>
                            <td class="text-end pe-4"><?= currency_format($sale['amount_paid']) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="text-end">
        <a href="<?= site_url('/sales/pdf/' . $sale['id']) ?>" class="btn btn-primary me-2" target="_blank"><i class="fas fa-file-pdf me-1"></i> Download PDF</a><button class="btn btn-outline-primary" onclick="window.print()"><i class="fas fa-print me-1"></i> Print</button>
    </div>
</div>
