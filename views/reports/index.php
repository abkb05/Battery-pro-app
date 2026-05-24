<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i>Reports & Analytics</h4>
        <div>
            <a href="<?= site_url('/reports/pdf') ?>" class="btn btn-outline-danger me-2" target="_blank"><i class="fas fa-file-pdf me-1"></i> PDF</a>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-auto">
                    <label class="form-label small">From</label>
                    <input type="date" class="form-control form-control-sm" name="from" value="<?= $fromDate ?>">
                </div>
                <div class="col-auto">
                    <label class="form-label small">To</label>
                    <input type="date" class="form-control form-control-sm" name="to" value="<?= $toDate ?>">
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary btn-sm"><i class="fas fa-filter me-1"></i> Apply</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Period Summary -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card stat-sales">
                <div class="card-body">
                    <small class="text-muted">Period Sales</small>
                    <h4 class="fw-bold mb-0"><?= currency_format($salesData['total']) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card stat-profit">
                <div class="card-body">
                    <small class="text-muted">Period Profit</small>
                    <h4 class="fw-bold mb-0"><?= currency_format($salesData['profit']) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card stat-expenses">
                <div class="card-body">
                    <small class="text-muted">Period Expenses</small>
                    <h4 class="fw-bold mb-0"><?= currency_format($expenseData['total']) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Net Profit (Period)</small>
                    <h4 class="fw-bold mb-0 <?= ($salesData['profit'] - $expenseData['total']) >= 0 ? 'text-success' : 'text-danger' ?>">
                        <?= currency_format($salesData['profit'] - $expenseData['total']) ?>
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Lifetime Summary -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm"><div class="card-body">
                <small class="text-muted">Lifetime Revenue</small>
                <h5 class="fw-bold"><?= currency_format($totalSales['total']) ?></h5>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm"><div class="card-body">
                <small class="text-muted">Lifetime Profit</small>
                <h5 class="fw-bold"><?= currency_format($totalSales['profit']) ?></h5>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm"><div class="card-body">
                <small class="text-muted">Lifetime Expenses</small>
                <h5 class="fw-bold"><?= currency_format($totalExpenses['total']) ?></h5>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm"><div class="card-body">
                <small class="text-muted">Stock Value</small>
                <h5 class="fw-bold"><?= currency_format($stockVal['stock_value']) ?> / <?= currency_format($stockVal['sale_value']) ?></h5>
            </div></div>
        </div>
    </div>

    <!-- Monthly Sales Chart -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-bottom fw-bold py-3">
            <i class="fas fa-chart-line me-2 text-primary"></i>Monthly Sales Trend (12 Months)
        </div>
        <div class="card-body">
            <canvas id="monthlyChart" height="80"></canvas>
        </div>
    </div>

    <!-- Supplier Performance & Top Customers -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom fw-bold py-3"><i class="fas fa-truck me-2 text-primary"></i>Supplier Performance</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 small">
                            <thead class="table-light"><tr><th class="ps-4">Supplier</th><th>Orders</th><th>Ordered</th><th class="text-end pe-4">Received</th></tr></thead>
                            <tbody>
                                <?php if (empty($supplierPerf)): ?><tr><td colspan="4" class="text-center py-3 text-muted">No data</td></tr>
                                <?php else: foreach ($supplierPerf as $sp): ?>
                                <tr><td class="ps-4"><?= htmlspecialchars($sp['name']) ?></td><td><?= $sp['po_count'] ?></td><td><?= currency_format($sp['total_ordered']) ?></td><td class="text-end pe-4"><?= currency_format($sp['total_received']) ?></td></tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom fw-bold py-3"><i class="fas fa-users me-2 text-primary"></i>Top Customers</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 small">
                            <thead class="table-light"><tr><th class="ps-4">Customer</th><th>Sales</th><th class="text-end pe-4">Total</th></tr></thead>
                            <tbody>
                                <?php if (empty($topCustomers)): ?><tr><td colspan="3" class="text-center py-3 text-muted">No data</td></tr>
                                <?php else: foreach ($topCustomers as $tc): ?>
                                <tr><td class="ps-4"><?= htmlspecialchars($tc['name']) ?></td><td><?= $tc['sale_count'] ?></td><td class="text-end pe-4"><?= currency_format($tc['total']) ?></td></tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('monthlyChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: [<?php foreach ($monthlySales as $m): ?>'<?= $m['month'] ?>',<?php endforeach; ?>],
                datasets: [
                    { label: 'Revenue', data: [<?php foreach ($monthlySales as $m): ?><?= $m['amount'] ?>,<?php endforeach; ?>], borderColor: '#4361ee', backgroundColor: 'rgba(67,97,238,0.1)', fill: true, tension: 0.3 },
                    { label: 'Profit', data: [<?php foreach ($monthlySales as $m): ?><?= $m['profit'] ?>,<?php endforeach; ?>], borderColor: '#06d6a0', backgroundColor: 'rgba(6,214,160,0.1)', fill: true, tension: 0.3 }
                ]
            },
            options: { responsive: true, plugins: { legend: { position: 'top' } }, scales: { y: { beginAtZero: true } } }
        });
    }
});
</script>
