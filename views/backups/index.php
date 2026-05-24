<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="fas fa-database me-2 text-primary"></i>Database Backups</h4>
        <a href="<?= site_url('/backups/create') ?>" class="btn btn-primary" onclick="return confirm('Create a new database backup?')"><i class="fas fa-play me-1"></i> Create Backup</a>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th class="ps-4">#</th><th>Filename</th><th>Size</th><th>Type</th><th>Status</th><th>Date</th><th class="text-end pe-4">Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($backups)): ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted"><i class="fas fa-database fa-2x d-block mb-2"></i>No backups yet.</td></tr>
                        <?php else: foreach ($backups as $b): ?>
                        <tr>
                            <td class="ps-4"><?= $b['id'] ?></td>
                            <td><?= htmlspecialchars($b['filename']) ?></td>
                            <td><?= $b['file_size'] ? number_format($b['file_size'] / 1024, 1) . ' KB' : '-' ?></td>
                            <td><span class="badge bg-<?= $b['backup_type'] === 'manual' ? 'primary' : 'secondary' ?>"><?= ucfirst($b['backup_type']) ?></span></td>
                            <td><span class="badge bg-<?= $b['status'] === 'completed' ? 'success' : 'danger' ?>"><?= ucfirst($b['status']) ?></span></td>
                            <td><?= htmlspecialchars($b['created_at']) ?></td>
                            <td class="text-end pe-4">
                                <?php if ($b['status'] === 'completed'): ?>
                                <a href="<?= site_url('/backups/download/' . $b['id']) ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-download"></i></a>
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
