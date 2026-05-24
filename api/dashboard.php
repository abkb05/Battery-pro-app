<?php
// API endpoint for dashboard summary – JWT protected
require_once __DIR__ . '/../config/autoload.php';
require_once __DIR__ . '/../models/Battery.php';
require_once __DIR__ . '/../models/Sale.php';
require_once __DIR__ . '/../models/Expense.php';
require_once __DIR__ . '/../models/Supplier.php';

header('Content-Type: application/json');

// Verify JWT token from Authorization: Bearer <token>
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] 
    ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] 
    ?? $_SERVER['Authorization'] 
    ?? '';
// Fix for Apache/FastCGI where Authorization header may be mangled
if (function_exists('apache_request_headers')) {
    $headers = apache_request_headers();
    $authHeader = $headers['Authorization'] ?? $authHeader;
}
if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode(['error' => 'Missing Authorization header']);
    exit;
}
$jwt = $matches[1];
$appCfg = $GLOBALS['appConfig'];
$secretKey = $appCfg['jwt_secret'];

$decoded = verify_jwt($jwt, $secretKey);
if (!$decoded) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid or expired token']);
    exit;
}

// Load models
$batteryModel = new Battery();
$saleModel = new Sale();
$expenseModel = new Expense();
$supplierModel = new Supplier();

// Gather stats similar to web dashboard
$totalStock = $batteryModel->getTotalStock();
$totalSales = $saleModel->getTotalSalesAmount();
$totalProfit = $saleModel->getTotalProfit();
$totalExpenses = $expenseModel->getTotalExpenses();
$pendingSupplierPayments = $supplierModel->getPendingPayments();

$response = [
    'total_stock' => $totalStock,
    'total_sales' => $totalSales,
    'total_profit' => $totalProfit,
    'total_expenses' => $totalExpenses,
    'pending_supplier_payments' => $pendingSupplierPayments,
];

echo json_encode($response);
?>