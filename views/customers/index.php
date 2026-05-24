<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0"><i class="fas fa-users me-2 text-primary"></i>Customers</h4>
        </div>
        <a href="<?= site_url('/customers/import') ?>" class="btn btn-outline-info me-2"><i class="fas fa-file-csv me-1"></i> Import</a>
        <a href="<?= site_url('/export/customers') ?>" class="btn btn-outline-success me-2"><i class="fas fa-file-excel me-1"></i> Export</a>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fas fa-plus me-1"></i> Add Customer
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Type</th>
                            <th>Purchases</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($customers)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fas fa-users-slash fa-2x d-block mb-2"></i>No customers yet.
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($customers as $c): ?>
                        <tr>
                            <td class="ps-4"><?= $c['id'] ?></td>
                            <td class="fw-medium"><?= htmlspecialchars($c['name']) ?></td>
                            <td><?= htmlspecialchars($c['phone'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($c['email'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($c['address'] ?? '-') ?></td>
                            <td><span class="badge bg-<?= ($c['customer_type'] ?? 'retail') === 'retail' ? 'info' : (($c['customer_type'] ?? '') === 'wholesale' ? 'primary' : 'dark') ?>"><?= ucfirst($c['customer_type'] ?? 'retail') ?></span></td>
                            <td><?= currency_format($c['total_purchases'] ?? 0) ?></td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editModal<?= $c['id'] ?>"><i class="fas fa-edit"></i></button>
                                <a href="<?= site_url('/customers/delete/' . $c['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this customer?')"><i class="fas fa-trash"></i></a>
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

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?= site_url('/customers/create') ?>">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus me-2 text-primary"></i>Add Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" name="phone">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Customer Type</label>
                        <select class="form-select" name="customer_type">
                            <option value="retail">Retail</option>
                            <option value="wholesale">Wholesale</option>
                            <option value="dealer">Dealer</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="address" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modals -->
<?php foreach ($customers as $c): ?>
<div class="modal fade" id="editModal<?= $c['id'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?= site_url('/customers/edit/' . $c['id']) ?>">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit me-2 text-primary"></i>Edit Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($c['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($c['phone'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($c['email'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Customer Type</label>
                        <select class="form-select" name="customer_type">
                            <option value="retail" <?= ($c['customer_type'] ?? '') === 'retail' ? 'selected' : '' ?>>Retail</option>
                            <option value="wholesale" <?= ($c['customer_type'] ?? '') === 'wholesale' ? 'selected' : '' ?>>Wholesale</option>
                            <option value="dealer" <?= ($c['customer_type'] ?? '') === 'dealer' ? 'selected' : '' ?>>Dealer</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="address" rows="2"><?= htmlspecialchars($c['address'] ?? '') ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>
