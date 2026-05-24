<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0"><i class="fas fa-car-battery me-2 text-primary"></i>Inventory</h4>
        </div>
        <a href="<?= site_url('/export/batteries') ?>" class="btn btn-outline-success me-2"><i class="fas fa-file-excel me-1"></i> Export</a>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fas fa-plus me-1"></i> Add Battery
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Brand</th>
                            <th>Size</th>
                            <th>Voltage</th>
                            <th>Plates</th>
                            <th>Purchase</th>
                            <th>Retail</th>
                            <th>Wholesale</th>
                            <th>Dealer</th>
                            <th>Supplier</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($batteries)): ?>
                        <tr>
                            <td colspan="11" class="text-center py-4 text-muted">
                                <i class="fas fa-box-open fa-2x d-block mb-2"></i>No batteries in inventory.
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($batteries as $b): ?>
                        <tr>
                            <td class="ps-4"><?= $b['id'] ?></td>
                            <td class="fw-medium"><?= htmlspecialchars($b['brand']) ?></td>
                            <td><?= $b['size'] ?></td>
                            <td><?= $b['voltage'] ?>V</td>
                            <td><?= $b['plates'] ?></td>
                            <td><?= currency_format($b['purchase_price']) ?></td>
                            <td><?= currency_format($b['sale_price']) ?></td>
                            <td><?= $b['wholesale_price'] ? currency_format($b['wholesale_price']) : '-' ?></td>
                            <td><?= $b['dealer_price'] ? currency_format($b['dealer_price']) : '-' ?></td>
                            <td><?= htmlspecialchars($b['supplier_id'] ? 'ID: ' . $b['supplier_id'] : '-') ?></td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-outline-success me-1" data-bs-toggle="modal" data-bs-target="#stockModal<?= $b['id'] ?>" title="Add Stock"><i class="fas fa-plus-circle"></i></button>
                                <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editModal<?= $b['id'] ?>"><i class="fas fa-edit"></i></button>
                                <a href="<?= site_url('/batteries/delete/' . $b['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this battery?')"><i class="fas fa-trash"></i></a>
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="<?= site_url('/batteries/create') ?>">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus me-2 text-primary"></i>Add Battery</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Brand <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="brand" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Size <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="size" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Voltage</label>
                            <input type="number" class="form-control" name="voltage" value="12">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Plates</label>
                            <input type="number" class="form-control" name="plates">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Purchase Price</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" name="purchase_price">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Retail Price</label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" step="0.01" class="form-control" name="sale_price">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Wholesale</label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" step="0.01" class="form-control" name="wholesale_price">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Dealer</label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" step="0.01" class="form-control" name="dealer_price">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Supplier</label>
                            <select class="form-select" name="supplier_id">
                                <option value="">Select Supplier</option>
                                <?php foreach ($suppliers as $sup): ?>
                                <option value="<?= $sup['id'] ?>"><?= htmlspecialchars($sup['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Initial Stock</label>
                            <input type="number" class="form-control" name="quantity" value="0" min="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Location</label>
                            <input type="text" class="form-control" name="location" value="Main Store">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Serial Numbers <small class="text-muted">(comma separated)</small></label>
                            <input type="text" class="form-control" name="serial_numbers" placeholder="e.g. SN001, SN002, SN003">
                        </div>
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

<!-- Stock Modals -->
<?php foreach ($batteries as $b): ?>
<div class="modal fade" id="stockModal<?= $b['id'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?= site_url('/batteries/stock/' . $b['id']) ?>">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus-circle me-2 text-success"></i>Add Stock - <?= htmlspecialchars($b['brand']) ?> <?= $b['size'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control" name="quantity" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" class="form-control" name="location" value="Main Store">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Serial Numbers <small class="text-muted">(comma separated)</small></label>
                        <input type="text" class="form-control" name="serial_numbers" placeholder="e.g. SN001, SN002, SN003">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-plus me-1"></i>Add Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal<?= $b['id'] ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="<?= site_url('/batteries/edit/' . $b['id']) ?>">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit me-2 text-primary"></i>Edit Battery</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Brand</label>
                            <input type="text" class="form-control" name="brand" value="<?= htmlspecialchars($b['brand']) ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Size</label>
                            <input type="number" class="form-control" name="size" value="<?= $b['size'] ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Voltage</label>
                            <input type="number" class="form-control" name="voltage" value="<?= $b['voltage'] ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Plates</label>
                            <input type="number" class="form-control" name="plates" value="<?= $b['plates'] ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Purchase Price</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" name="purchase_price" value="<?= $b['purchase_price'] ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Retail Price</label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" step="0.01" class="form-control" name="sale_price" value="<?= $b['sale_price'] ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Wholesale</label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" step="0.01" class="form-control" name="wholesale_price" value="<?= $b['wholesale_price'] ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Dealer</label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" step="0.01" class="form-control" name="dealer_price" value="<?= $b['dealer_price'] ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Supplier</label>
                            <select class="form-select" name="supplier_id">
                                <option value="">None</option>
                                <?php foreach ($suppliers as $sup): ?>
                                <option value="<?= $sup['id'] ?>" <?= $b['supplier_id'] == $sup['id'] ? 'selected' : '' ?>><?= htmlspecialchars($sup['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
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
