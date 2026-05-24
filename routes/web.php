<?php
/**
 * Web Routes
 */

require_once __DIR__ . '/../helpers/Router.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/DashboardController.php';
require_once __DIR__ . '/../controllers/SupplierController.php';
require_once __DIR__ . '/../controllers/BatteryController.php';
require_once __DIR__ . '/../controllers/CustomerController.php';
require_once __DIR__ . '/../controllers/SaleController.php';
require_once __DIR__ . '/../controllers/ExpenseController.php';
require_once __DIR__ . '/../controllers/SettingsController.php';
require_once __DIR__ . '/../controllers/PdfController.php';
require_once __DIR__ . '/../controllers/PurchaseOrderController.php';
require_once __DIR__ . '/../controllers/ExportController.php';
require_once __DIR__ . '/../controllers/AuditController.php';
require_once __DIR__ . '/../controllers/BackupController.php';
require_once __DIR__ . '/../controllers/WarrantyController.php';
require_once __DIR__ . '/../controllers/ReportController.php';
require_once __DIR__ . '/../controllers/ReturnController.php';

$router = new Router();

// Auth routes
$router->add('GET', '/', 'DashboardController@index');
$router->add('GET', '/login', 'AuthController@login');
$router->add('POST', '/login', 'AuthController@login');
$router->add('GET', '/logout', 'AuthController@logout');
$router->add('GET', '/profile', 'AuthController@profile');
$router->add('POST', '/profile', 'AuthController@profile');

// Supplier routes
$router->add('GET', '/suppliers', 'SupplierController@index');
$router->add('POST', '/suppliers/create', 'SupplierController@create');
$router->add('POST', '/suppliers/edit/{id}', 'SupplierController@edit');
$router->add('GET', '/suppliers/delete/{id}', 'SupplierController@delete');

// Battery routes
$router->add('GET', '/batteries', 'BatteryController@index');
$router->add('POST', '/batteries/create', 'BatteryController@create');
$router->add('POST', '/batteries/edit/{id}', 'BatteryController@edit');
$router->add('GET', '/batteries/delete/{id}', 'BatteryController@delete');
$router->add('POST', '/batteries/stock/{id}', 'BatteryController@stock');

// Customer routes
$router->add('GET', '/customers', 'CustomerController@index');
$router->add('POST', '/customers/create', 'CustomerController@create');
$router->add('POST', '/customers/edit/{id}', 'CustomerController@edit');
$router->add('GET', '/customers/delete/{id}', 'CustomerController@delete');

// Sale routes
$router->add('GET', '/sales', 'SaleController@index');
$router->add('GET', '/sales/create', 'SaleController@create');
$router->add('POST', '/sales/create', 'SaleController@create');
$router->add('GET', '/sales/view/{id}', 'SaleController@view');

// Expense routes
$router->add('GET', '/expenses', 'ExpenseController@index');
$router->add('POST', '/expenses/create', 'ExpenseController@create');
$router->add('POST', '/expenses/edit/{id}', 'ExpenseController@edit');
$router->add('GET', '/expenses/delete/{id}', 'ExpenseController@delete');

// Purchase Order routes
$router->add('GET', '/purchase-orders', 'PurchaseOrderController@index');
$router->add('GET', '/purchase-orders/create', 'PurchaseOrderController@create');
$router->add('POST', '/purchase-orders/create', 'PurchaseOrderController@create');
$router->add('GET', '/purchase-orders/view/{id}', 'PurchaseOrderController@view');
$router->add('GET', '/purchase-orders/receive/{id}', 'PurchaseOrderController@receive');
$router->add('POST', '/purchase-orders/receive/{id}', 'PurchaseOrderController@receive');
$router->add('GET', '/purchase-orders/cancel/{id}', 'PurchaseOrderController@cancel');

// Settings routes
$router->add('GET', '/settings', 'SettingsController@index');
$router->add('POST', '/settings', 'SettingsController@index');

// Audit log
$router->add('GET', '/audit', 'AuditController@index');

// Backup routes
$router->add('GET', '/backups', 'BackupController@index');
$router->add('GET', '/backups/create', 'BackupController@create');
$router->add('GET', '/backups/download/{id}', 'BackupController@download');

// Warranty routes
$router->add('GET', '/warranties', 'WarrantyController@index');

// Import routes
$router->add('GET', '/suppliers/import', 'SupplierController@import');
$router->add('POST', '/suppliers/import', 'SupplierController@import');
$router->add('GET', '/customers/import', 'CustomerController@import');
$router->add('POST', '/customers/import', 'CustomerController@import');

// Export routes
$router->add('GET', '/export/suppliers', 'ExportController@suppliers');
$router->add('GET', '/export/batteries', 'ExportController@batteries');
$router->add('GET', '/export/customers', 'ExportController@customers');
$router->add('GET', '/export/sales', 'ExportController@sales');
$router->add('GET', '/export/expenses', 'ExportController@expenses');

// PDF routes
$router->add('GET', '/sales/pdf/{id}', 'PdfController@saleInvoice');
$router->add('GET', '/reports/pdf', 'PdfController@reports');

// Return routes
$router->add('GET', '/returns', 'ReturnController@index');
$router->add('GET', '/returns/create', 'ReturnController@create');
$router->add('POST', '/returns/create', 'ReturnController@create');
$router->add('GET', '/returns/items/{id}', 'ReturnController@getSaleItems');

// Exchange / Scrap routes
$router->add('GET', '/exchange', 'ExchangeController@index');
$router->add('GET', '/exchange/scrap/{id}', 'ExchangeController@markScrapped');
$router->add('GET', '/exchange/sold/{id}', 'ExchangeController@markSold');

// Report routes
$router->add('GET', '/reports', 'ReportController@index');

// Define a 404 handler
$router->setNotFound(function() {
    header('HTTP/1.0 404 Not Found');
    $title = '404 Not Found';
    $content = '<div class="container text-center mt-5"><h1>404</h1><p>Page not found</p><a href="' . site_url('/') . '" class="btn btn-primary">Back to Dashboard</a></div>';
    require __DIR__ . '/../views/layout.php';
});

// Dispatch the request
$router->dispatch();
