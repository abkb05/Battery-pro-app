<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/PurchaseOrder.php';
require_once __DIR__ . '/../models/Supplier.php';
require_once __DIR__ . '/../models/Battery.php';

class PurchaseOrderController extends BaseController {
    private $poModel;

    public function __construct() {
        parent::__construct();
        $this->poModel = new PurchaseOrder();
    }

    public function index() {
        if (!check_login()) { $this->redirectTo('/login'); }
        $orders = $this->poModel->getAllWithSupplier();
        $this->render('purchase_orders/index', ['title' => 'Purchase Orders', 'orders' => $orders]);
    }

    public function create() {
        if (!check_login()) { $this->redirectTo('/login'); }
        $suppliers = (new Supplier())->findAll();
        $batteries = (new Battery())->findAll();
        $db = (new Database())->getConnection();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $supplierId = !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : null;
            $items = $_POST['items'] ?? [];
            if (empty($items)) { set_flash_message('error', 'Add at least one item'); $this->redirectTo('/purchase-orders/create'); return; }

            $totalAmount = 0;
            $poItems = [];
            foreach ($items as $item) {
                $batteryId = (int)($item['battery_id'] ?? 0);
                $qty = (int)($item['quantity'] ?? 0);
                $price = (float)($item['unit_price'] ?? 0);
                if ($batteryId <= 0 || $qty <= 0) continue;
                $totalAmount += $qty * $price;
                $poItems[] = ['battery_id' => $batteryId, 'quantity' => $qty, 'unit_price' => $price, 'total_price' => $qty * $price];
            }
            if (empty($poItems)) { set_flash_message('error', 'No valid items'); $this->redirectTo('/purchase-orders/create'); return; }

            $orderNo = $this->poModel->generateOrderNumber();
            $poId = $this->poModel->create([
                'supplier_id' => $supplierId, 'user_id' => $_SESSION['user_id'],
                'order_number' => $orderNo, 'status' => 'pending',
                'total_amount' => $totalAmount, 'order_date' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
            ]);

            foreach ($poItems as $pi) {
                $db->prepare("INSERT INTO purchase_order_items (purchase_order_id, battery_id, quantity, unit_price, total_price) VALUES (?, ?, ?, ?, ?)")
                    ->execute([$poId, $pi['battery_id'], $pi['quantity'], $pi['unit_price'], $pi['total_price']]);
            }

            log_audit('create', 'purchase_order', $poId, null, ['order_number' => $orderNo, 'total' => $totalAmount]);
            set_flash_message('success', "PO #$orderNo created");
            $this->redirectTo('/purchase-orders');
        }

        $this->render('purchase_orders/create', ['title' => 'New Purchase Order', 'suppliers' => $suppliers, 'batteries' => $batteries]);
    }

    public function view($id) {
        if (!check_login()) { $this->redirectTo('/login'); }
        $order = $this->poModel->getWithSupplier($id);
        if (!$order) { $this->redirectTo('/purchase-orders'); return; }
        $items = $this->poModel->getItems($id);
        $this->render('purchase_orders/view', ['title' => 'PO #' . $order['order_number'], 'order' => $order, 'items' => $items]);
    }

    public function receive($id) {
        if (!check_login()) { $this->redirectTo('/login'); }
        $order = $this->poModel->getWithSupplier($id);
        if (!$order) { $this->redirectTo('/purchase-orders'); return; }
        $items = $this->poModel->getItems($id);
        $db = (new Database())->getConnection();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $allReceived = true;
            foreach ($items as $item) {
                $receivedQty = (int)($_POST['receive_qty_' . $item['id']] ?? 0);
                if ($receivedQty > 0) {
                    $newReceived = $item['received_quantity'] + $receivedQty;
                    $db->prepare("UPDATE purchase_order_items SET received_quantity = ? WHERE id = ?")->execute([$newReceived, $item['id']]);
                    $existingStock = $db->prepare("SELECT id, quantity FROM stock WHERE battery_id = ? AND location = 'Main Store' ORDER BY id DESC LIMIT 1");
                    $existingStock->execute([$item['battery_id']]);
                    $stockRow = $existingStock->fetch();
                    if ($stockRow) {
                        $db->prepare("UPDATE stock SET quantity = quantity + ? WHERE id = ?")->execute([$receivedQty, $stockRow['id']]);
                    } else {
                        $db->prepare("INSERT INTO stock (battery_id, quantity, location, received_date) VALUES (?, ?, 'Main Store', NOW())")->execute([$item['battery_id'], $receivedQty]);
                    }
                    if ($newReceived < $item['quantity']) $allReceived = false;
                }
            }
            $newStatus = $allReceived ? 'received' : 'partial';
            $db->prepare("UPDATE purchase_orders SET status = ?, received_date = NOW(), updated_at = NOW() WHERE id = ?")->execute([$newStatus, $id]);
            log_audit('receive', 'purchase_order', $id, null, ['status' => $newStatus]);
            set_flash_message('success', 'Stock received for PO #' . $order['order_number']);
            $this->redirectTo('/purchase-orders');
        }

        $this->render('purchase_orders/receive', ['title' => 'Receive PO #' . $order['order_number'], 'order' => $order, 'items' => $items]);
    }

    public function cancel($id) {
        if (!check_login()) { $this->redirectTo('/login'); }
        $db = (new Database())->getConnection();
        $db->prepare("UPDATE purchase_orders SET status = 'cancelled', updated_at = NOW() WHERE id = ?")->execute([$id]);
        set_flash_message('info', 'Purchase order cancelled');
        $this->redirectTo('/purchase-orders');
    }
}
