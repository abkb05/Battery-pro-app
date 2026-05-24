<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Supplier.php';

class SupplierController extends BaseController {
    private $supplierModel;

    public function __construct() {
        parent::__construct();
        $this->supplierModel = new Supplier();
    }

    public function index() {
        if (!check_login()) { $this->redirectTo('/login'); }
        $suppliers = $this->supplierModel->findAll();
        $this->render('suppliers/index', [
            'title' => 'Suppliers',
            'suppliers' => $suppliers,
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
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            if (empty($data['name'])) {
                set_flash_message('error', 'Supplier name is required');
                $this->redirectTo('/suppliers');
                return;
            }
            $this->supplierModel->create($data);
            set_flash_message('success', 'Supplier added successfully');
            $this->redirectTo('/suppliers');
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
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $this->supplierModel->update($id, $data);
            set_flash_message('success', 'Supplier updated successfully');
            $this->redirectTo('/suppliers');
        }
    }

    public function delete($id) {
        if (!check_login()) { $this->redirectTo('/login'); }
        $this->supplierModel->delete($id);
        set_flash_message('info', 'Supplier deleted');
        $this->redirectTo('/suppliers');
    }

    public function import() {
        if (!check_login()) { $this->redirectTo('/login'); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
            $handle = fopen($_FILES['csv_file']['tmp_name'], 'r');
            $header = fgetcsv($handle);
            $count = 0;
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) >= 1 && !empty(trim($row[0]))) {
                    $this->supplierModel->create([
                        'name' => trim($row[0]),
                        'phone' => trim($row[1] ?? ''),
                        'email' => trim($row[2] ?? ''),
                        'address' => trim($row[3] ?? ''),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    $count++;
                }
            }
            fclose($handle);
            log_audit('import', 'suppliers', null, null, ['count' => $count]);
            set_flash_message('success', "$count suppliers imported");
            $this->redirectTo('/suppliers');
        }
        $this->render('suppliers/import', ['title' => 'Import Suppliers']);
    }
}
