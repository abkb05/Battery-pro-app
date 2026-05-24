<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Expense.php';

class ExpenseController extends BaseController {
    private $expenseModel;

    public function __construct() {
        parent::__construct();
        $this->expenseModel = new Expense();
    }

    public function index() {
        if (!check_login()) { $this->redirectTo('/login'); }
        $expenses = $this->expenseModel->findAll();
        $this->render('expenses/index', [
            'title' => 'Expenses',
            'expenses' => $expenses,
        ]);
    }

    public function create() {
        if (!check_login()) { $this->redirectTo('/login'); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'category' => $_POST['category'] ?? 'other',
                'description' => trim($_POST['description'] ?? ''),
                'amount' => (float)($_POST['amount'] ?? 0),
                'expense_date' => $_POST['expense_date'] ?? date('Y-m-d'),
                'payment_method' => $_POST['payment_method'] ?? 'cash',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            if ($data['amount'] <= 0) {
                set_flash_message('error', 'Amount must be positive');
                $this->redirectTo('/expenses');
                return;
            }
            $this->expenseModel->create($data);
            set_flash_message('success', 'Expense recorded');
            $this->redirectTo('/expenses');
        }
    }

    public function edit($id) {
        if (!check_login()) { $this->redirectTo('/login'); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'category' => $_POST['category'] ?? 'other',
                'description' => trim($_POST['description'] ?? ''),
                'amount' => (float)($_POST['amount'] ?? 0),
                'expense_date' => $_POST['expense_date'] ?? date('Y-m-d'),
                'payment_method' => $_POST['payment_method'] ?? 'cash',
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $this->expenseModel->update($id, $data);
            set_flash_message('success', 'Expense updated');
            $this->redirectTo('/expenses');
        }
    }

    public function delete($id) {
        if (!check_login()) { $this->redirectTo('/login'); }
        $this->expenseModel->delete($id);
        set_flash_message('info', 'Expense deleted');
        $this->redirectTo('/expenses');
    }
}
