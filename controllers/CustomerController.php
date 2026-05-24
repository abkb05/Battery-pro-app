<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Customer.php';

class CustomerController extends BaseController {
    private $customerModel;

    public function __construct() {
        parent::__construct();
        $this->customerModel = new Customer();
    }

    public function index() {
        if (!check_login()) { $this->redirectTo('/login'); }
        $customers = $this->customerModel->findAll();
        $this->render('customers/index', [
            'title' => 'Customers',
            'customers' => $customers,
        ]);
    }

    public function create() {
        if (!check_login()) { $this->redirectTo('/login'); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'customer_type' => $_POST['customer_type'] ?? 'retail',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            if (empty($data['name'])) {
                set_flash_message('error', 'Customer name is required');
                $this->redirectTo('/customers');
                return;
            }
            $this->customerModel->create($data);
            set_flash_message('success', 'Customer added');
            $this->redirectTo('/customers');
        }
    }

    public function edit($id) {
        if (!check_login()) { $this->redirectTo('/login'); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'customer_type' => $_POST['customer_type'] ?? 'retail',
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $this->customerModel->update($id, $data);
            set_flash_message('success', 'Customer updated');
            $this->redirectTo('/customers');
        }
    }

    public function delete($id) {
        if (!check_login()) { $this->redirectTo('/login'); }
        $this->customerModel->delete($id);
        set_flash_message('info', 'Customer deleted');
        $this->redirectTo('/customers');
    }

    public function import() {
        if (!check_login()) { $this->redirectTo('/login'); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
            $handle = fopen($_FILES['csv_file']['tmp_name'], 'r');
            fgetcsv($handle); $count = 0;
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) >= 1 && !empty(trim($row[0]))) {
                    $this->customerModel->create([
                        'name' => trim($row[0]), 'phone' => trim($row[1] ?? ''), 'email' => trim($row[2] ?? ''), 'address' => trim($row[3] ?? ''), 'customer_type' => trim($row[4] ?? 'retail'),
                        'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    $count++;
                }
            }
            fclose($handle);
            log_audit('import', 'customers', null, null, ['count' => $count]);
            set_flash_message('success', "$count customers imported");
            $this->redirectTo('/customers');
        }
        $this->render('customers/import', ['title' => 'Import Customers']);
    }
}
