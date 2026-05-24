<?php
require_once __DIR__ . '/BaseController.php';

class WarrantyController extends BaseController {
    public function index() {
        if (!check_login()) { $this->redirectTo('/login'); }
        $db = (new Database())->getConnection();
        $warranties = $db->query("SELECT w.*, b.brand, b.size, c.name as customer_name FROM warranties w JOIN batteries b ON w.battery_id = b.id LEFT JOIN customers c ON w.customer_id = c.id ORDER BY w.end_date ASC")->fetchAll();
        $this->render('warranties/index', ['title' => 'Warranties', 'warranties' => $warranties]);
    }
}
