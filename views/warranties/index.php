<div class="container-fluid py-4">
    <h4 class="fw-bold mb-4"><i class="fas fa-shield-alt me-2 text-primary"></i>Warranty Tracking</h4>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 small">
                    <thead class="table-light">
                        <tr><th class="ps-4">#</th><th>Battery</th><th>Customer</th><th>Start Date</th><th>End Date</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($warranties)): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted"><i class="fas fa-shield-alt fa-2x d-block mb-2"></i>No warranties registered.</td></tr>
                        <?php else: foreach ($warranties as $w):
                            $isExpired = strtotime($w['end_date']) < time();
                            $warrantyStatus = $w['status'];
                            if ($warrantyStatus === 'active' && $isExpired) $warrantyStatus = 'expired';
                        ?>
                        <tr>
                            <td class="ps-4"><?= $w['id'] ?></td>
                            <td><?= htmlspecialchars($w['brand']) ?> <?= $w['size'] ?></td>
                            <td><?= htmlspecialchars($w['customer_name'] ?? '-') ?></td>
                            <td><?= $w['start_date'] ?></td>
                            <td class="<?= $isExpired && $w['status'] === 'active' ? 'text-danger fw-bold' : '' ?>"><?= $w['end_date'] ?></td>
                            <td><span class="badge bg-<?= $warrantyStatus === 'active' ? 'success' : ($warrantyStatus === 'expired' ? 'danger' : 'warning') ?>"><?= ucfirst($warrantyStatus) ?></span></td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
