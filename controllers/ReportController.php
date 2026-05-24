<?php
require_once __DIR__ . '/BaseController.php';

class ReportController extends BaseController {

    public function index() {
        if (!check_login()) { $this->redirectTo('/login'); }
        $db = (new Database())->getConnection();

        // Date range filter
        $fromDate = $_GET['from'] ?? date('Y-m-01');
        $toDate = $_GET['to'] ?? date('Y-m-d');

        // Summary stats for the period
        $salesData = $db->prepare("SELECT COALESCE(SUM(total_amount),0) as total, COALESCE(SUM(total_profit),0) as profit FROM sales WHERE DATE(sale_date) BETWEEN ? AND ?");
        $salesData->execute([$fromDate, $toDate]); $salesData = $salesData->fetch();

        $expenseData = $db->prepare("SELECT COALESCE(SUM(amount),0) as total FROM expenses WHERE expense_date BETWEEN ? AND ?");
        $expenseData->execute([$fromDate, $toDate]); $expenseData = $expenseData->fetch();

        // Monthly breakdown for chart
        $monthlySales = $db->query("SELECT DATE_FORMAT(sale_date, '%Y-%m') as month, COALESCE(SUM(total_amount),0) as amount, COALESCE(SUM(total_profit),0) as profit FROM sales WHERE sale_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH) GROUP BY month ORDER BY month")->fetchAll();

        // Supplier performance
        $supplierPerf = $db->query("SELECT s.name, COUNT(DISTINCT po.id) as po_count, COALESCE(SUM(po.total_amount),0) as total_ordered, COALESCE(SUM(CASE WHEN po.status='received' THEN po.total_amount ELSE 0 END),0) as total_received FROM suppliers s LEFT JOIN purchase_orders po ON po.supplier_id = s.id GROUP BY s.id ORDER BY total_ordered DESC LIMIT 10")->fetchAll();

        // Top customers
        $topCustomers = $db->query("SELECT c.name, COUNT(s.id) as sale_count, COALESCE(SUM(s.total_amount),0) as total FROM customers c LEFT JOIN sales s ON s.customer_id = c.id GROUP BY c.id ORDER BY total DESC LIMIT 10")->fetchAll();

        // Stock valuation
        $stockVal = $db->query("SELECT COALESCE(SUM(st.quantity * b.purchase_price),0) as stock_value, COALESCE(SUM(st.quantity * b.sale_price),0) as sale_value FROM stock st JOIN batteries b ON st.battery_id = b.id")->fetch();

        // Overall totals
        $totalSales = $db->query("SELECT COALESCE(SUM(total_amount),0) as total, COALESCE(SUM(total_profit),0) as profit FROM sales")->fetch();
        $totalExpenses = $db->query("SELECT COALESCE(SUM(amount),0) as total FROM expenses")->fetch();

        $this->render('reports/index', [
            'title' => 'Reports',
            'fromDate' => $fromDate, 'toDate' => $toDate,
            'salesData' => $salesData, 'expenseData' => $expenseData,
            'monthlySales' => $monthlySales,
            'supplierPerf' => $supplierPerf,
            'topCustomers' => $topCustomers,
            'stockVal' => $stockVal,
            'totalSales' => $totalSales, 'totalExpenses' => $totalExpenses,
        ]);
    }
}
