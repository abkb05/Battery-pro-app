<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Sale.php';
require_once __DIR__ . '/../models/Battery.php';
require_once __DIR__ . '/../models/Customer.php';

class SaleController extends BaseController {
    private $saleModel;
    private $batteryModel;
    private $customerModel;

    public function __construct() {
        parent::__construct();
        $this->saleModel = new Sale();
        $this->batteryModel = new Battery();
        $this->customerModel = new Customer();
    }

    public function index() {
        if (!check_login()) { $this->redirectTo('/login'); }
        $db = (new Database())->getConnection();
        $sales = $db->query("SELECT s.*, c.name as customer_name, u.full_name as cashier FROM sales s LEFT JOIN customers c ON s.customer_id = c.id LEFT JOIN users u ON s.user_id = u.id ORDER BY s.created_at DESC")->fetchAll();
        $this->render('sales/index', [
            'title' => 'Sales',
            'sales' => $sales,
        ]);
    }

    public function create() {
        if (!check_login()) { $this->redirectTo('/login'); }
        $customers = $this->customerModel->findAll();
        $batteries = $this->batteryModel->findAll();
        $db = (new Database())->getConnection();
        $stockItems = $db->query("SELECT st.*, b.brand, b.size, b.voltage, b.plates, b.sale_price, b.wholesale_price, b.dealer_price, b.purchase_price FROM stock st JOIN batteries b ON st.battery_id = b.id WHERE st.quantity > 0 ORDER BY b.brand, b.size")->fetchAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $customerId = !empty($_POST['customer_id']) ? (int)$_POST['customer_id'] : null;
            $items = $_POST['items'] ?? [];
            $paymentMethod = $_POST['payment_method'] ?? 'cash';
            $amountPaid = (float)($_POST['amount_paid'] ?? 0);

            // Get customer type for pricing
            $customerType = 'retail';
            if ($customerId) {
                $cust = $db->prepare("SELECT customer_type FROM customers WHERE id = ?");
                $cust->execute([$customerId]);
                $customerType = $cust->fetchColumn() ?: 'retail';
            }

            if (empty($items)) {
                set_flash_message('error', 'Add at least one item');
                $this->redirectTo('/sales/create');
                return;
            }

            $totalAmount = 0;
            $totalProfit = 0;
            $saleItems = [];

            foreach ($items as $item) {
                $stockId = (int)($item['stock_id'] ?? 0);
                $qty = (int)($item['quantity'] ?? 0);
                $serialNo = trim($item['serial_number'] ?? '');
                if ($stockId <= 0 || $qty <= 0) continue;

                $st = $db->prepare("SELECT s.*, b.sale_price, b.wholesale_price, b.dealer_price, b.purchase_price FROM stock s JOIN batteries b ON s.battery_id = b.id WHERE s.id = ?");
                $st->execute([$stockId]);
                $stock = $st->fetch();

                if (!$stock || $stock['quantity'] < $qty) continue;

                // Use price based on customer type
                $unitPrice = $stock['sale_price'];
                if ($customerType === 'wholesale' && $stock['wholesale_price']) {
                    $unitPrice = $stock['wholesale_price'];
                } elseif ($customerType === 'dealer' && $stock['dealer_price']) {
                    $unitPrice = $stock['dealer_price'];
                }

                $lineTotal = $unitPrice * $qty;
                $lineProfit = ($unitPrice - $stock['purchase_price']) * $qty;
                $totalAmount += $lineTotal;
                $totalProfit += $lineProfit;

                $saleItems[] = [
                    'stock_id' => $stockId,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'purchase_price' => $stock['purchase_price'],
                    'profit' => $lineProfit,
                    'total_price' => $lineTotal,
                    'serial_number' => $serialNo,
                ];
            }

            if (empty($saleItems)) {
                set_flash_message('error', 'No valid items in sale');
                $this->redirectTo('/sales/create');
                return;
            }

            // Handle exchange battery
            $exchangeValue = (float)($_POST['exchange_value'] ?? 0);
            if ($exchangeValue > 0) {
                $totalAmount -= $exchangeValue;
                $totalProfit -= $exchangeValue;
            }

            $paymentStatus = $amountPaid >= $totalAmount ? 'paid' : ($amountPaid > 0 ? 'partial' : 'pending');

            $saleData = [
                'customer_id' => $customerId,
                'user_id' => $_SESSION['user_id'],
                'total_amount' => $totalAmount,
                'total_profit' => $totalProfit,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'amount_paid' => $amountPaid,
                'sale_date' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $saleId = $this->saleModel->create($saleData);

            // Insert sale items with serial numbers and deduct stock
            foreach ($saleItems as $si) {
                $db->prepare("INSERT INTO sale_items (sale_id, stock_id, quantity, unit_price, purchase_price, profit, total_price, serial_number, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())")
                    ->execute([$saleId, $si['stock_id'], $si['quantity'], $si['unit_price'], $si['purchase_price'], $si['profit'], $si['total_price'], $si['serial_number']]);
                $db->prepare("UPDATE stock SET quantity = quantity - ? WHERE id = ?")
                    ->execute([$si['quantity'], $si['stock_id']]);
            }

            // Save exchange battery record
            if ($exchangeValue > 0) {
                $oldBrand = trim($_POST['old_brand'] ?? '');
                $oldSize = trim($_POST['old_size'] ?? '');
                $oldCondition = $_POST['old_condition'] ?? 'fair';
                $db->prepare("INSERT INTO exchange_batteries (sale_id, old_brand, old_size, old_condition, exchange_value, scrap_status, created_at) VALUES (?, ?, ?, ?, ?, 'pending', NOW())")
                    ->execute([$saleId, $oldBrand, $oldSize, $oldCondition, $exchangeValue]);
            }

            // Create warranty records for sale items
            $warrantyMonths = 12;
            foreach ($saleItems as $si) {
                $st = $db->prepare("SELECT battery_id FROM stock WHERE id = ?");
                $st->execute([$si['stock_id']]);
                $batteryId = $st->fetchColumn();
                if ($batteryId) {
                    $siSt = $db->prepare("SELECT id FROM sale_items WHERE sale_id = ? AND stock_id = ? ORDER BY id DESC LIMIT 1");
                    $siSt->execute([$saleId, $si['stock_id']]);
                    $saleItemId = $siSt->fetchColumn();
                    $startDate = date('Y-m-d');
                    $endDate = date('Y-m-d', strtotime("+$warrantyMonths months"));
                    $db->prepare("INSERT INTO warranties (sale_item_id, battery_id, customer_id, warranty_period, start_date, end_date, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'active', NOW())")
                        ->execute([$saleItemId, $batteryId, $customerId, $warrantyMonths, $startDate, $endDate]);
                }
            }

            log_audit('create', 'sale', $saleId, null, ['customer' => $customerId, 'total' => $totalAmount, 'exchange' => $exchangeValue]);
            set_flash_message('success', 'Sale #' . $saleId . ' recorded successfully');
            $this->redirectTo('/sales');
        }

        $this->render('sales/create', [
            'title' => 'New Sale',
            'customers' => $customers,
            'batteries' => $batteries,
            'stockItems' => $stockItems,
        ]);
    }

    public function view($id) {
        if (!check_login()) { $this->redirectTo('/login'); }
        $db = (new Database())->getConnection();
        $sale = $db->prepare("SELECT s.*, c.name as customer_name, u.full_name as cashier FROM sales s LEFT JOIN customers c ON s.customer_id = c.id LEFT JOIN users u ON s.user_id = u.id WHERE s.id = ?");
        $sale->execute([$id]);
        $sale = $sale->fetch();
        if (!$sale) { $this->redirectTo('/sales'); return; }

        $items = $db->prepare("SELECT si.*, b.brand, b.size, b.voltage, b.plates, st.serial_number FROM sale_items si JOIN stock st ON si.stock_id = st.id JOIN batteries b ON st.battery_id = b.id WHERE si.sale_id = ?");
        $items->execute([$id]);
        $items = $items->fetchAll();

        // Get exchange battery info if any
        $exchange = $db->prepare("SELECT * FROM exchange_batteries WHERE sale_id = ?");
        $exchange->execute([$id]);
        $exchange = $exchange->fetch();

        $this->render('sales/view', [
            'title' => 'Sale #' . $id,
            'sale' => $sale,
            'items' => $items,
            'exchange' => $exchange,
        ]);
    }
}
