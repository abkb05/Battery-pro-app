<?php
/**
 * Dashboard Controller
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Battery.php';
require_once __DIR__ . '/../models/Sale.php';
require_once __DIR__ . '/../models/Expense.php';
require_once __DIR__ . '/../models/Supplier.php';
require_once __DIR__ . '/../models/Customer.php';

class DashboardController extends BaseController {
    public function index() {
        if (!check_login()) {
            $this->redirectTo('/login');
        }
        // Load models
        $batteryModel = new Battery();
        $saleModel = new Sale();
        $expenseModel = new Expense();
        $supplierModel = new Supplier();
        $customerModel = new Customer();

        // Gather statistics
        $stockCount = $batteryModel->getTotalStock();

        $totalSales = $saleModel->getTotalSalesAmount();
        $totalProfit = $saleModel->getTotalProfit();
        $totalExpenses = $expenseModel->getTotalExpenses();
        $pendingSupplierPayments = $supplierModel->getPendingPayments();
        $dailySales = $saleModel->getSalesSummary('daily');
        $weeklySales = $saleModel->getSalesSummary('weekly');
        $monthlySales = $saleModel->getSalesSummary('monthly');
        $recentActivities = $saleModel->getRecentActivities(5);
        $lowStockBatteries = $batteryModel->getLowStock(5); // threshold 5

        $this->render('dashboard/index', [
            'title' => 'Dashboard',
            'total_stock' => $stockCount,
            'total_sales' => $totalSales,
            'total_profit' => $totalProfit,
            'total_expenses' => $totalExpenses,
            'pending_supplier_payments' => $pendingSupplierPayments,
            'daily_sales' => $dailySales,
            'weekly_sales' => $weeklySales,
            'monthly_sales' => $monthlySales,
            'recent_activities' => $recentActivities,
            'low_stock_batteries' => $lowStockBatteries,
        ]);
    }
}
