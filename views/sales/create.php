<div class="container-fluid py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="<?= site_url('/sales') ?>" class="btn btn-outline-secondary me-3"><i class="fas fa-arrow-left"></i></a>
        <h4 class="fw-bold mb-0"><i class="fas fa-plus-circle me-2 text-primary"></i>New Sale</h4>
    </div>

    <form method="POST" action="<?= site_url('/sales/create') ?>" id="saleForm">
        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <label class="form-label fw-medium">Customer</label>
                        <select class="form-select" name="customer_id" id="customerSelect">
                            <option value="">Walk-in Customer (Retail)</option>
                            <?php foreach ($customers as $c): ?>
                            <option value="<?= $c['id'] ?>" data-type="<?= htmlspecialchars($c['customer_type'] ?? 'retail') ?>"><?= htmlspecialchars($c['name']) ?> (<?= ucfirst($c['customer_type'] ?? 'retail') ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted" id="priceTierLabel">Pricing: <span class="fw-bold text-primary">Retail</span></small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <label class="form-label fw-medium">Payment Method</label>
                        <select class="form-select" name="payment_method">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="credit">Credit</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <label class="form-label fw-medium">Amount Paid</label>
                        <div class="input-group">
                            <span class="input-group-text">Rs.</span>
                            <input type="number" step="0.01" class="form-control" name="amount_paid" value="0">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <label class="form-label fw-medium">Grand Total</label>
                        <div class="fs-3 fw-bold text-primary" id="totalDisplay">Rs. 0.00</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sale Items -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-bottom fw-bold py-3 d-flex justify-content-between align-items-center">
                <span><i class="fas fa-shopping-cart me-2 text-primary"></i>Sale Items</span>
                <button type="button" class="btn btn-sm btn-outline-primary" id="addItemBtn"><i class="fas fa-plus me-1"></i>Add Item</button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0" id="itemsTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Item</th>
                                <th>Avail</th>
                                <th>Unit Price</th>
                                <th style="width:80px">Qty</th>
                                <th>Serial No.</th>
                                <th class="text-end pe-4" style="width:100px">Total</th>
                                <th style="width:50px"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            <tr id="noItemsRow">
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-cart-plus fa-2x d-block mb-2"></i>Add items to this sale.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Exchange Battery -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-bottom fw-bold py-3">
                <i class="fas fa-exchange-alt me-2 text-warning"></i>Old Battery Exchange
                <small class="text-muted fw-normal">(optional)</small>
            </div>
            <div class="card-body">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Old Battery Brand</label>
                        <input type="text" class="form-control form-control-sm" name="old_brand" placeholder="e.g. Exide, AGS">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Size</label>
                        <input type="text" class="form-control form-control-sm" name="old_size" placeholder="e.g. 100Ah">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Condition</label>
                        <select class="form-select form-select-sm" name="old_condition">
                            <option value="good">Good</option>
                            <option value="fair" selected>Fair</option>
                            <option value="poor">Poor</option>
                            <option value="dead">Dead</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Exchange Value (Rs.)</label>
                        <input type="number" step="0.01" class="form-control form-control-sm" name="exchange_value" id="exchangeValue" value="0" placeholder="0">
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Exchange value deducted from total</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-end">
            <a href="<?= site_url('/sales') ?>" class="btn btn-secondary me-2">Cancel</a>
            <button type="submit" class="btn btn-primary btn-lg px-5"><i class="fas fa-check me-2"></i>Complete Sale</button>
        </div>
    </form>
</div>

<script>
const stockItems = <?= json_encode($stockItems) ?>;
let priceTier = 'retail';

document.getElementById('customerSelect').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    priceTier = opt.dataset.type || 'retail';
    document.getElementById('priceTierLabel').innerHTML = 'Pricing: <span class="fw-bold text-primary">' + 
        priceTier.charAt(0).toUpperCase() + priceTier.slice(1) + '</span>';
    // Refresh all items with new pricing
    document.querySelectorAll('.stock-select').forEach(sel => updateItemRow(sel));
});

document.getElementById('addItemBtn').addEventListener('click', function() {
    const noRow = document.getElementById('noItemsRow');
    if (noRow) noRow.remove();

    const tbody = document.getElementById('itemsBody');
    const idx = tbody.children.length;

    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td class="ps-4">
            <select class="form-select form-select-sm stock-select" name="items[${idx}][stock_id]" required>
                <option value="">Select item</option>
                ${stockItems.map(s => {
                    const prices = { retail: s.sale_price, wholesale: s.wholesale_price, dealer: s.dealer_price };
                    const p = prices[priceTier] || s.sale_price;
                    return `<option value="${s.id}" data-retail="${s.sale_price}" data-wholesale="${s.wholesale_price || ''}" data-dealer="${s.dealer_price || ''}" data-qty="${s.quantity}" data-name="${s.brand} ${s.size}V ${s.voltage}V ${s.plates}pl">${s.brand} ${s.size} (${s.quantity} avail)</option>`;
                }).join('')}
            </select>
        </td>
        <td class="avail-qty">-</td>
        <td class="unit-price">-</td>
        <td><input type="number" class="form-control form-control-sm item-qty" name="items[${idx}][quantity]" min="1" value="1" required></td>
        <td><input type="text" class="form-control form-control-sm item-serial" name="items[${idx}][serial_number]" placeholder="Serial #"></td>
        <td class="text-end pe-4 line-total">Rs. 0.00</td>
        <td><button type="button" class="btn btn-sm btn-outline-danger remove-item"><i class="fas fa-times"></i></button></td>
    `;

    tbody.appendChild(tr);

    tr.querySelector('.stock-select').addEventListener('change', function() {
        updateItemRow(this);
    });

    tr.querySelector('.item-qty').addEventListener('input', function() {
        updateLineTotal(tr);
    });

    tr.querySelector('.remove-item').addEventListener('click', function() {
        tr.remove();
        updateGrandTotal();
    });

    updateItemRow(tr.querySelector('.stock-select'));
});

function updateItemRow(select) {
    const tr = select.closest('tr');
    const opt = select.options[select.selectedIndex];
    if (opt.value) {
        const prices = { retail: opt.dataset.retail, wholesale: opt.dataset.wholesale, dealer: opt.dataset.dealer };
        const price = parseFloat(prices[priceTier] || opt.dataset.retail);
        const avail = parseInt(opt.dataset.qty);
        tr.querySelector('.avail-qty').textContent = avail;
        tr.querySelector('.unit-price').textContent = 'Rs. ' + price.toFixed(2);
        tr.querySelector('.item-qty').max = avail;
        updateLineTotal(tr);
    } else {
        tr.querySelector('.avail-qty').textContent = '-';
        tr.querySelector('.unit-price').textContent = '-';
        tr.querySelector('.line-total').textContent = 'Rs. 0.00';
    }
    updateGrandTotal();
}

function updateLineTotal(tr) {
    const select = tr.querySelector('.stock-select');
    const opt = select.options[select.selectedIndex];
    if (opt.value) {
        const prices = { retail: opt.dataset.retail, wholesale: opt.dataset.wholesale, dealer: opt.dataset.dealer };
        const price = parseFloat(prices[priceTier] || opt.dataset.retail);
        const qty = parseInt(tr.querySelector('.item-qty').value) || 0;
        tr.querySelector('.line-total').textContent = 'Rs. ' + (price * qty).toFixed(2);
    }
    updateGrandTotal();
}

function updateGrandTotal() {
    let total = 0;
    document.querySelectorAll('.line-total').forEach(el => {
        const val = parseFloat(el.textContent.replace('Rs. ', ''));
        if (!isNaN(val)) total += val;
    });
    const exchange = parseFloat(document.getElementById('exchangeValue').value) || 0;
    const finalTotal = Math.max(0, total - exchange);
    document.getElementById('totalDisplay').textContent = 'Rs. ' + finalTotal.toFixed(2);
}

document.getElementById('exchangeValue').addEventListener('input', updateGrandTotal);
</script>