<div class="container-fluid py-4">
    <h4 class="fw-bold mb-4"><i class="fas fa-history me-2 text-primary"></i>Audit Log</h4>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 small">
                    <thead class="table-light">
                        <tr><th class="ps-4">Time</th><th>User</th><th>Action</th><th>Entity</th><th>Entity ID</th><th>IP</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">No activity logged yet.</td></tr>
                        <?php else: foreach ($logs as $l): ?>
                        <tr>
                            <td class="ps-4"><?= htmlspecialchars($l['created_at']) ?></td>
                            <td><?= htmlspecialchars($l['user_name'] ?? 'System') ?></td>
                            <td><span class="badge bg-<?= $l['action'] === 'create' ? 'success' : ($l['action'] === 'update' ? 'info' : ($l['action'] === 'delete' ? 'danger' : 'secondary')) ?>"><?= htmlspecialchars($l['action']) ?></span></td>
                            <td><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $l['entity_type']))) ?></td>
                            <td><?= $l['entity_id'] ?? '-' ?></td>
                            <td><?= htmlspecialchars($l['ip_address'] ?? '-') ?></td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php if ($totalPages > 1): ?>
    <nav class="mt-3"><ul class="pagination pagination-sm justify-content-center">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= $i === $page ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a></li>
        <?php endfor; ?>
    </ul></nav>
    <?php endif; ?>
</div>
