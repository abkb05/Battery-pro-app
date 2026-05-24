<div class="container-fluid py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="<?= site_url('/returns') ?>" class="btn btn-outline-secondary me-3"><i class="fas fa-arrow-left"></i></a>
        <h4 class="fw-bold mb-0"><i class="fas fa-undo me-2 text-primary"></i>Process Return / Exchange</h4>
    </div>

    <form method="POST" action="<?= site_url('/returns/create') ?>">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Sale Invoice</label>
                        <select class="form-select" name="sale_id" id="saleSelect" required>
                            <option value="">Select Sale</option>
                            <?php foreach ($sales as $s): ?>
                            <option value="<?= $s['id'] ?>">#<?= $s['id'] ?> - <?= htmlspecialchars($s['customer_name'] ?? 'Walk-in') ?> (<?= $s['sale_date'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-medium">Type</label>
                        <select class="form-select" name="return_type">
                            <option value="return">Return</option>
                            <option value="exchange">Exchange</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-medium">Reason</label>
                        <input type="text" class="form-control" name="reason" placeholder="Reason for return">
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom fw-bold py-3"><i class="fas fa-box me-2 text-primary"></i>Items to Return</div>
            <div class="card-body">
                <div id="itemsContainer">
                    <p class="text-muted text-center py-4" id="selectSaleMsg">Select a sale invoice to load items.</p>
                </div>
            </div>
        </div>

        <div class="text-end mt-3">
            <a href="<?= site_url('/returns') ?>" class="btn btn-secondary me-2">Cancel</a>
            <button type="submit" class="btn btn-warning px-4"><i class="fas fa-undo me-2"></i>Process Return</button>
        </div>
    </form>
</div>

<script>
document.getElementById('saleSelect').addEventListener('change', function() {
    const saleId = this.value;
    const container = document.getElementById('itemsContainer');
    if (!saleId) { container.innerHTML = '<p class="text-muted text-center py-4" id="selectSaleMsg">Select a sale invoice to load items.</p>'; return; }
    fetch('<?= site_url('/returns/items/') ?>' + saleId)
        .then(r => r.json())
        .then(items => {
            if (items.length === 0) { container.innerHTML = '<p class="text-muted text-center py-4">No items in this sale.</p>'; return; }
            let html = '<div class="table-responsive"><table class="table"><thead class="table-light"><tr><th>Item</th><th>Sold Qty</th><th>Price</th><th style="width:100px">Return Qty</th></tr></thead><tbody>';
            items.forEach((item, idx) => {
                html += `<tr>
                    <td><input type="hidden" name="items[${idx}][sale_item_id]" value="${item.id}">${item.brand} ${item.size}</td>
                    <td>${item.quantity}</td>
                    <td>Rs. ${parseFloat(item.unit_price).toFixed(2)}</td>
                    <td><input type="number" class="form-control form-control-sm" name="items[${idx}][quantity]" min="1" max="${item.quantity}" value="${item.quantity}"></td>
                </tr>`;
            });
            html += '</tbody></table></div>';
            container.innerHTML = html;
        });
});
</script>
