<?php
require_once __DIR__ . '/BaseController.php';

class ReturnController extends BaseController {

    public function index() {
        if (!check_login()) { $this->redirectTo('/login'); }
        $db = (new Database())->getConnection();
        $returns = $db->query("SELECT r.*, u.full_name as handled_by FROM order_returns r LEFT JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC")->fetchAll();
        $this->render('returns/index', ['title' => 'Returns & Exchanges', 'returns' => $returns]);
    }

    public function create() {
        if (!check_login()) { $this->redirectTo('/login'); }
        $db = (new Database())->getConnection();
        $sales = $db->query("SELECT s.id, s.sale_date, s.total_amount, c.name as customer_name FROM sales s LEFT JOIN customers c ON s.customer_id = c.id ORDER BY s.created_at DESC")->fetchAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $saleId = (int)($_POST['sale_id'] ?? 0);
            $returnType = $_POST['return_type'] ?? 'return';
            $reason = trim($_POST['reason'] ?? '');
            $items = $_POST['items'] ?? [];

            if (empty($items)) { set_flash_message('error', 'Select at least one item'); $this->redirectTo('/returns/create'); return; }

            $totalRefund = 0;
            $returnItems = [];
            foreach ($items as $item) {
                $saleItemId = (int)($item['sale_item_id'] ?? 0);
                $qty = (int)($item['quantity'] ?? 0);
                if ($saleItemId <= 0 || $qty <= 0) continue;

                $si = $db->prepare("SELECT si.*, st.battery_id FROM sale_items si JOIN stock st ON si.stock_id = st.id WHERE si.id = ?");
                $si->execute([$saleItemId]); $si = $si->fetch();
                if (!$si) continue;

                $refundAmount = $si['unit_price'] * $qty;
                $totalRefund += $refundAmount;
                $returnItems[] = ['sale_item_id' => $saleItemId, 'quantity' => $qty, 'refund_amount' => $refundAmount, 'battery_id' => $st['battery_id'] ?? null];
            }

            if (empty($returnItems)) { set_flash_message('error', 'No valid items'); $this->redirectTo('/returns/create'); return; }

            $db->prepare("INSERT INTO order_returns (sale_id, user_id, return_type, reason, total_refund, status, return_date) VALUES (?, ?, ?, ?, ?, 'processed', NOW())")
                ->execute([$saleId, $_SESSION['user_id'], $returnType, $reason, $totalRefund]);
            $returnId = $db->lastInsertId();

            foreach ($returnItems as $ri) {
                $db->prepare("INSERT INTO order_return_items (order_return_id, sale_item_id, quantity, refund_amount) VALUES (?, ?, ?, ?)")
                    ->execute([$returnId, $ri['sale_item_id'], $ri['quantity'], $ri['refund_amount']]);
                // Restore stock
                if ($ri['battery_id']) {
                    $stockCheck = $db->prepare("SELECT id, quantity FROM stock WHERE battery_id = ? AND location = 'Main Store' ORDER BY id DESC LIMIT 1");
                    $stockCheck->execute([$ri['battery_id']]);
                    $stockRow = $stockCheck->fetch();
                    if ($stockRow) {
                        $db->prepare("UPDATE stock SET quantity = quantity + ? WHERE id = ?")->execute([$ri['quantity'], $stockRow['id']]);
                    } else {
                        $db->prepare("INSERT INTO stock (battery_id, quantity, location, received_date) VALUES (?, ?, 'Returns', NOW())")->execute([$ri['battery_id'], $ri['quantity']]);
                    }
                }
            }

            log_audit('create', 'return', $returnId, null, ['sale_id' => $saleId, 'type' => $returnType, 'refund' => $totalRefund]);
            set_flash_message('success', ucfirst($returnType) . ' processed - Refund: ' . currency_format($totalRefund));
            $this->redirectTo('/returns');
        }

        $this->render('returns/create', ['title' => 'New Return', 'sales' => $sales]);
    }

    public function getSaleItems($saleId) {
        if (!check_login()) { return; }
        $db = (new Database())->getConnection();
        $items = $db->prepare("SELECT si.id, si.quantity, si.unit_price, b.brand, b.size FROM sale_items si JOIN stock st ON si.stock_id = st.id JOIN batteries b ON st.battery_id = b.id WHERE si.sale_id = ?");
        $items->execute([$saleId]);
        header('Content-Type: application/json');
        echo json_encode($items->fetchAll());
        exit;
    }
}
