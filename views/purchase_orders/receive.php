<div class="container-fluid py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="<?= site_url('/purchase-orders') ?>" class="btn btn-outline-secondary me-3"><i class="fas fa-arrow-left"></i></a>
        <h4 class="fw-bold mb-0"><i class="fas fa-warehouse me-2 text-success"></i>Receive Stock - <?= htmlspecialchars($order['order_number']) ?></h4>
    </div>
    <form method="POST" action="<?= site_url('/purchase-orders/receive/' . $order['id']) ?>">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light"><tr><th class="ps-4">Item</th><th>Ordered</th><th>Received</th><th style="width:120px">Receive Now</th></tr></thead>
                        <tbody>
                            <?php foreach ($items as $item): $remaining = $item['quantity'] - $item['received_quantity']; ?>
                            <tr>
                                <td class="ps-4"><?= htmlspecialchars($item['brand']) ?> <?= $item['size'] ?>V</td>
                                <td><?= $item['quantity'] ?></td>
                                <td><?= $item['received_quantity'] ?></td>
                                <td><input type="number" class="form-control form-control-sm" name="receive_qty_<?= $item['id'] ?>" value="<?= max(0, $remaining) ?>" min="0" max="<?= $remaining ?>"></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="text-end mt-3">
            <a href="<?= site_url('/purchase-orders') ?>" class="btn btn-secondary me-2">Cancel</a>
            <button type="submit" class="btn btn-success px-4"><i class="fas fa-check me-2"></i>Receive Stock</button>
        </div>
    </form>
</div>
