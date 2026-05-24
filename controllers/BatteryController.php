<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Battery.php';
require_once __DIR__ . '/../models/Supplier.php';

class BatteryController extends BaseController {
    private $batteryModel;
    private $supplierModel;

    public function __construct() {
        parent::__construct();
        $this->batteryModel = new Battery();
        $this->supplierModel = new Supplier();
    }

    public function index() {
        if (!check_login()) { $this->redirectTo('/login'); }
        $batteries = $this->batteryModel->findAll();
        $suppliers = $this->supplierModel->findAll();
        $this->render('batteries/index', [
            'title' => 'Inventory',
            'batteries' => $batteries,
            'suppliers' => $suppliers,
        ]);
    }

    public function create() {
        if (!check_login()) { $this->redirectTo('/login'); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'brand' => trim($_POST['brand'] ?? ''),
                'size' => (int)($_POST['size'] ?? 0),
                'voltage' => (int)($_POST['voltage'] ?? 0),
                'plates' => (int)($_POST['plates'] ?? 0),
                'purchase_price' => (float)($_POST['purchase_price'] ?? 0),
                'sale_price' => (float)($_POST['sale_price'] ?? 0),
                'wholesale_price' => !empty($_POST['wholesale_price']) ? (float)$_POST['wholesale_price'] : null,
                'dealer_price' => !empty($_POST['dealer_price']) ? (float)$_POST['dealer_price'] : null,
                'supplier_id' => !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            if (empty($data['brand']) || $data['size'] <= 0) {
                set_flash_message('error', 'Brand and size are required');
                $this->redirectTo('/batteries');
                return;
            }
            $batteryId = $this->batteryModel->create($data);

            // Add initial stock if quantity provided
            $quantity = (int)($_POST['quantity'] ?? 0);
            if ($quantity > 0 && $batteryId) {
                $db = (new Database())->getConnection();
                $serialNumbers = $_POST['serial_numbers'] ?? '';
                $stmt = $db->prepare("INSERT INTO stock (battery_id, quantity, location, serial_number, received_date) VALUES (?, ?, ?, ?, NOW())");
                $stmt->execute([$batteryId, $quantity, $_POST['location'] ?? 'Main Store', $serialNumbers]);
            }

            set_flash_message('success', 'Battery added to inventory');
            $this->redirectTo('/batteries');
        }
    }

    public function edit($id) {
        if (!check_login()) { $this->redirectTo('/login'); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'brand' => trim($_POST['brand'] ?? ''),
                'size' => (int)($_POST['size'] ?? 0),
                'voltage' => (int)($_POST['voltage'] ?? 0),
                'plates' => (int)($_POST['plates'] ?? 0),
                'purchase_price' => (float)($_POST['purchase_price'] ?? 0),
                'sale_price' => (float)($_POST['sale_price'] ?? 0),
                'wholesale_price' => !empty($_POST['wholesale_price']) ? (float)$_POST['wholesale_price'] : null,
                'dealer_price' => !empty($_POST['dealer_price']) ? (float)$_POST['dealer_price'] : null,
                'supplier_id' => !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : null,
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $this->batteryModel->update($id, $data);
            set_flash_message('success', 'Battery updated');
            $this->redirectTo('/batteries');
        }
    }

    public function delete($id) {
        if (!check_login()) { $this->redirectTo('/login'); }
        $this->batteryModel->delete($id);
        set_flash_message('info', 'Battery removed from inventory');
        $this->redirectTo('/batteries');
    }

    public function stock($id) {
        if (!check_login()) { $this->redirectTo('/login'); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $quantity = (int)($_POST['quantity'] ?? 0);
            $location = trim($_POST['location'] ?? 'Main Store');
            if ($quantity <= 0) {
                set_flash_message('error', 'Quantity must be positive');
                $this->redirectTo('/batteries');
                return;
            }
            $serialNumbers = trim($_POST['serial_numbers'] ?? '');
            $db = (new Database())->getConnection();
            $stmt = $db->prepare("INSERT INTO stock (battery_id, quantity, location, serial_number, received_date) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$id, $quantity, $location, $serialNumbers]);
            set_flash_message('success', 'Stock added');
            $this->redirectTo('/batteries');
        }
    }
}
