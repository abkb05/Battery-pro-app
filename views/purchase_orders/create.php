<div class="container-fluid py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="<?= site_url('/purchase-orders') ?>" class="btn btn-outline-secondary me-3"><i class="fas fa-arrow-left"></i></a>
        <h4 class="fw-bold mb-0"><i class="fas fa-plus-circle me-2 text-primary"></i>New Purchase Order</h4>
    </div>
    <form method="POST" action="<?= site_url('/purchase-orders/create') ?>">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Supplier</label>
                        <select class="form-select" name="supplier_id">
                            <option value="">Select Supplier</option>
                            <?php foreach ($suppliers as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-bottom fw-bold py-3"><i class="fas fa-box me-2 text-primary"></i>Order Items</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0" id="itemsTable">
                        <thead class="table-light">
                            <tr><th class="ps-4">Battery</th><th>Unit Price</th><th style="width:120px">Quantity</th><th class="text-end pe-4">Total</th><th style="width:50px"></th></tr>
                        </thead>
                        <tbody id="itemsBody">
                            <tr id="noItemsRow"><td colspan="5" class="text-center py-4 text-muted"><i class="fas fa-cart-plus fa-2x d-block mb-2"></i>Add items to this order.</td></tr>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3"><button type="button" class="btn btn-sm btn-outline-primary" id="addItemBtn"><i class="fas fa-plus me-1"></i>Add Item</button></td>
                                <td class="text-end pe-4 fw-bold" id="totalDisplay">Rs. 0.00</td><td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="text-end">
            <a href="<?= site_url('/purchase-orders') ?>" class="btn btn-secondary me-2">Cancel</a>
            <button type="submit" class="btn btn-primary btn-lg px-5"><i class="fas fa-check me-2"></i>Create PO</button>
        </div>
    </form>
</div>

<script>
const batteries = <?= json_encode($batteries) ?>;
document.getElementById('addItemBtn').addEventListener('click', function() {
    const noRow = document.getElementById('noItemsRow'); if (noRow) noRow.remove();
    const tbody = document.getElementById('itemsBody');
    const idx = tbody.children.length;
    const opts = batteries.map(b => `<option value="${b.id}" data-price="${b.purchase_price}">${b.brand} ${b.size} (${b.voltage}V)</option>`).join('');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td class="ps-4"><select class="form-select form-select-sm" name="items[${idx}][battery_id]" required><option value="">Select</option>${opts}</select></td>
        <td><input type="number" step="0.01" class="form-control form-control-sm item-price" name="items[${idx}][unit_price]" value="0" min="0"></td>
        <td><input type="number" class="form-control form-control-sm item-qty" name="items[${idx}][quantity]" value="1" min="1"></td>
        <td class="text-end pe-4 line-total">Rs. 0.00</td>
        <td><button type="button" class="btn btn-sm btn-outline-danger remove-item"><i class="fas fa-times"></i></button></td>
    `;
    tbody.appendChild(tr);
    const sel = tr.querySelector('select');
    sel.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        tr.querySelector('.item-price').value = opt.dataset.price || 0;
        updateLine(tr); updateTotal();
    });
    tr.querySelector('.item-price').addEventListener('input', function() { updateLine(tr); updateTotal(); });
    tr.querySelector('.item-qty').addEventListener('input', function() { updateLine(tr); updateTotal(); });
    tr.querySelector('.remove-item').addEventListener('click', function() { tr.remove(); updateTotal(); });
});
function updateLine(tr) {
    const price = parseFloat(tr.querySelector('.item-price').value) || 0;
    const qty = parseInt(tr.querySelector('.item-qty').value) || 0;
    tr.querySelector('.line-total').textContent = 'Rs. ' + (price * qty).toFixed(2);
}
function updateTotal() {
    let t = 0;
    document.querySelectorAll('.line-total').forEach(el => { const v = parseFloat(el.textContent.replace('Rs.','')); if (!isNaN(v)) t += v; });
    document.getElementById('totalDisplay').textContent = 'Rs. ' + t.toFixed(2);
}
</script>
