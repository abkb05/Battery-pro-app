<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="fas fa-undo me-2 text-primary"></i>Returns & Exchanges</h4>
        <a href="<?= site_url('/returns/create') ?>" class="btn btn-primary"><i class="fas fa-plus me-1"></i> New Return</a>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th class="ps-4">#</th><th>Date</th><th>Type</th><th>Reason</th><th>Refund</th><th>Status</th><th>Handled By</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($returns)): ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted"><i class="fas fa-undo fa-2x d-block mb-2"></i>No returns yet.</td></tr>
                        <?php else: foreach ($returns as $r): ?>
                        <tr>
                            <td class="ps-4"><?= $r['id'] ?></td>
                            <td><?= htmlspecialchars($r['return_date']) ?></td>
                            <td><span class="badge bg-<?= $r['return_type'] === 'exchange' ? 'info' : 'warning' ?>"><?= ucfirst($r['return_type']) ?></span></td>
                            <td><?= htmlspecialchars($r['reason'] ?? '-') ?></td>
                            <td><?= currency_format($r['total_refund']) ?></td>
                            <td><span class="badge bg-<?= $r['status'] === 'processed' ? 'success' : ($r['status'] === 'rejected' ? 'danger' : 'secondary') ?>"><?= ucfirst($r['status']) ?></span></td>
                            <td><?= htmlspecialchars($r['handled_by'] ?? '-') ?></td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
