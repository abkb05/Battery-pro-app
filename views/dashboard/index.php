<div class="container-fluid py-4">
    <?php if (!empty($low_stock_batteries)): ?>
    <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Low Stock Alert!</strong> <?= count($low_stock_batteries) ?> battery type(s) are below minimum stock level.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Dashboard</h4>
            <p class="text-muted mb-0">Welcome back, <?= htmlspecialchars($_SESSION['full_name'] ?? 'User') ?>!</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm stat-card stat-stock">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 fw-medium">Total Stock</p>
                            <h3 class="fw-bold mb-0"><?= number_format($total_stock) ?></h3>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-car-battery"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm stat-card stat-sales">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 fw-medium">Total Sales</p>
                            <h3 class="fw-bold mb-0"><?= currency_format($total_sales) ?></h3>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-cash-register"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm stat-card stat-profit">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 fw-medium">Total Profit</p>
                            <h3 class="fw-bold mb-0"><?= currency_format($total_profit) ?></h3>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm stat-card stat-expenses">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 fw-medium">Total Expenses</p>
                            <h3 class="fw-bold mb-0"><?= currency_format($total_expenses) ?></h3>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-coins"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom fw-bold py-3">
                    <i class="fas fa-calendar-day me-2 text-primary"></i>Daily Sales
                </div>
                <div class="card-body text-center py-4">
                    <h3 class="fw-bold text-success mb-0"><?= currency_format($daily_sales['total_amount'] ?? 0) ?></h3>
                    <small class="text-muted">Profit: <?= currency_format($daily_sales['total_profit'] ?? 0) ?></small>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom fw-bold py-3">
                    <i class="fas fa-calendar-week me-2 text-primary"></i>Weekly Sales
                </div>
                <div class="card-body text-center py-4">
                    <h3 class="fw-bold text-info mb-0"><?= currency_format($weekly_sales['total_amount'] ?? 0) ?></h3>
                    <small class="text-muted">Profit: <?= currency_format($weekly_sales['total_profit'] ?? 0) ?></small>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom fw-bold py-3">
                    <i class="fas fa-calendar-alt me-2 text-primary"></i>Monthly Sales
                </div>
                <div class="card-body text-center py-4">
                    <h3 class="fw-bold text-warning mb-0"><?= currency_format($monthly_sales['total_amount'] ?? 0) ?></h3>
                    <small class="text-muted">Profit: <?= currency_format($monthly_sales['total_profit'] ?? 0) ?></small>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom fw-bold py-3 d-flex justify-content-between align-items-center">
            <span><i class="fas fa-history me-2 text-primary"></i>Recent Activities</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Cashier</th>
                            <th class="text-end pe-4">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_activities)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                                No recent activities found.
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($recent_activities as $activity): ?>
                        <tr>
                            <td class="ps-4"><?= htmlspecialchars($activity['id']) ?></td>
                            <td><span class="text-nowrap"><?= htmlspecialchars($activity['sale_date']) ?></span></td>
                            <td><?= htmlspecialchars($activity['customer_name'] ?? 'Walk-in') ?></td>
                            <td><?= htmlspecialchars($activity['cashier'] ?? '-') ?></td>
                            <td class="text-end pe-4 fw-medium"><?= currency_format($activity['total_amount']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php if (!empty($low_stock_batteries)): ?>
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-transparent border-bottom fw-bold py-3">
        <i class="fas fa-exclamation-triangle me-2 text-warning"></i>Low Stock Items
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Brand</th>
                        <th>Size</th>
                        <th>Voltage</th>
                        <th>Plates</th>
                        <th>In Stock</th>
                        <th class="text-end pe-4">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($low_stock_batteries as $b): ?>
                    <tr>
                        <td class="ps-4 fw-medium"><?= htmlspecialchars($b['brand']) ?></td>
                        <td><?= $b['size'] ?></td>
                        <td><?= $b['voltage'] ?>V</td>
                        <td><?= $b['plates'] ?></td>
                        <td class="fw-bold text-danger"><?= $b['total_stock'] ?></td>
                        <td class="text-end pe-4">
                            <span class="badge bg-danger">Low Stock</span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="row g-3 mt-2">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom fw-bold py-3">
                <i class="fas fa-chart-line me-2 text-primary"></i>Sales Overview
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom fw-bold py-3">
                <i class="fas fa-chart-pie me-2 text-primary"></i>Top Batteries
            </div>
            <div class="card-body">
                <canvas id="topBatteriesChart" height="150"></canvas>
            </div>
        </div>
    </div>
</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sales Overview Chart
    const salesCtx = document.getElementById('salesChart');
    if (salesCtx) {
        new Chart(salesCtx, {
            type: 'bar',
            data: {
                labels: ['Daily', 'Weekly', 'Monthly'],
                datasets: [
                    {
                        label: 'Revenue',
                        data: [
                            <?= ($daily_sales['total_amount'] ?? 0) ?>,
                            <?= ($weekly_sales['total_amount'] ?? 0) ?>,
                            <?= ($monthly_sales['total_amount'] ?? 0) ?>
                        ],
                        backgroundColor: 'rgba(67, 97, 238, 0.7)',
                        borderColor: '#4361ee',
                        borderWidth: 1
                    },
                    {
                        label: 'Profit',
                        data: [
                            <?= ($daily_sales['total_profit'] ?? 0) ?>,
                            <?= ($weekly_sales['total_profit'] ?? 0) ?>,
                            <?= ($monthly_sales['total_profit'] ?? 0) ?>
                        ],
                        backgroundColor: 'rgba(6, 214, 160, 0.7)',
                        borderColor: '#06d6a0',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'top' } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    // Top Batteries Chart
    const topCtx = document.getElementById('topBatteriesChart');
    if (topCtx) {
        <?php
        $db = (new Database())->getConnection();
        $topBatteries = $db->query("SELECT b.brand, b.size, COALESCE(SUM(si.quantity),0) as total_sold FROM batteries b LEFT JOIN stock st ON st.battery_id = b.id LEFT JOIN sale_items si ON si.stock_id = st.id GROUP BY b.id ORDER BY total_sold DESC LIMIT 5")->fetchAll();
        ?>
        new Chart(topCtx, {
            type: 'pie',
            data: {
                labels: [<?php foreach ($topBatteries as $tb): ?>'<?= htmlspecialchars($tb['brand']) ?> <?= $tb['size'] ?>',<?php endforeach; ?>],
                datasets: [{
                    data: [<?php foreach ($topBatteries as $tb): ?><?= $tb['total_sold'] ?>,<?php endforeach; ?>],
                    backgroundColor: ['#4361ee','#06d6a0','#ffd166','#ef476f','#118ab2']
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } }
            }
        });
    }
});
</script>
