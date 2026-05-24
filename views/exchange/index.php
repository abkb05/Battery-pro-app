<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="fas fa-exchange-alt me-2 text-warning"></i>Exchange / Scrap Batteries</h4>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Sale ID</th>
                            <th>Old Brand</th>
                            <th>Size</th>
                            <th>Condition</th>
                            <th>Exchange Value</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($exchanges)): ?>
                        <tr><td colspan="9" class="text-center py-4 text-muted"><i class="fas fa-box-open fa-2x d-block mb-2"></i>No exchange batteries recorded.</td></tr>
                        <?php else: foreach ($exchanges as $e): ?>
                        <tr>
                            <td class="ps-4"><?= $e['id'] ?></td>
                            <td><a href="<?= site_url('/sales/view/' . $e['sale_id']) ?>">#<?= $e['sale_id'] ?></a></td>
                            <td><?= htmlspecialchars($e['old_brand'] ?: '-') ?></td>
                            <td><?= htmlspecialchars($e['old_size'] ?: '-') ?></td>
                            <td><span class="badge bg-<?= $e['old_condition'] === 'good' ? 'success' : ($e['old_condition'] === 'fair' ? 'info' : ($e['old_condition'] === 'poor' ? 'warning' : 'danger')) ?>"><?= ucfirst($e['old_condition']) ?></span></td>
                            <td><?= currency_format($e['exchange_value']) ?></td>
                            <td><span class="badge bg-<?= $e['scrap_status'] === 'pending' ? 'warning' : ($e['scrap_status'] === 'sold' ? 'success' : 'secondary') ?>"><?= ucfirst($e['scrap_status']) ?></span></td>
                            <td><?= date('d-m-Y', strtotime($e['created_at'])) ?></td>
                            <td class="text-end pe-4">
                                <?php if ($e['scrap_status'] === 'pending'): ?>
                                <a href="<?= site_url('/exchange/scrap/' . $e['id']) ?>" class="btn btn-sm btn-outline-secondary me-1" onclick="return confirm('Mark as scrapped?')"><i class="fas fa-trash"></i> Scrap</a>
                                <a href="<?= site_url('/exchange/sold/' . $e['id']) ?>" class="btn btn-sm btn-outline-success" onclick="return confirm('Mark as sold?')"><i class="fas fa-check"></i> Sold</a>
                                <?php else: ?>
                                <span class="text-muted">-</span>
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