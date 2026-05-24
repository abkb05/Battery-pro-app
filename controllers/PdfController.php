<?php
require_once __DIR__ . '/BaseController.php';

class PdfController extends BaseController {
    
    public function saleInvoice($id) {
        if (!check_login()) { $this->redirectTo('/login'); }
        
        $db = (new Database())->getConnection();
        
        // Get sale
        $sale = $db->prepare("SELECT s.*, c.name as customer_name, u.full_name as cashier FROM sales s LEFT JOIN customers c ON s.customer_id = c.id LEFT JOIN users u ON s.user_id = u.id WHERE s.id = ?");
        $sale->execute([$id]);
        $sale = $sale->fetch();
        if (!$sale) { $this->redirectTo('/sales'); return; }
        
        // Get sale items
        $items = $db->prepare("SELECT si.*, b.brand, b.size, b.voltage, b.plates FROM sale_items si JOIN stock st ON si.stock_id = st.id JOIN batteries b ON st.battery_id = b.id WHERE si.sale_id = ?");
        $items->execute([$id]);
        $items = $items->fetchAll();
        
        // Get settings
        $settings = $db->query("SELECT * FROM settings LIMIT 1")->fetch();
        $shopName = $settings['shop_name'] ?? 'BatteryPro';
        $currencySymbol = $settings['currency_symbol'] ?? 'Rs.';
        
        // Generate PDF using mPDF
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 20,
            'margin_bottom' => 20,
            'margin_left' => 15,
            'margin_right' => 15,
        ]);
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #4361ee; }
        .header h2 { margin: 0; color: #4361ee; font-size: 22px; }
        .header p { margin: 3px 0; color: #666; font-size: 11px; }
        .invoice-title { text-align: center; font-size: 18px; font-weight: bold; margin: 15px 0; color: #333; }
        .details { margin-bottom: 15px; }
        .details table { width: 100%; font-size: 11px; }
        .details td { padding: 3px 5px; }
        table.items { width: 100%; border-collapse: collapse; margin: 15px 0; font-size: 11px; }
        table.items th { background: #4361ee; color: white; padding: 8px 10px; text-align: left; }
        table.items td { padding: 7px 10px; border-bottom: 1px solid #ddd; }
        table.items .right { text-align: right; }
        .totals { text-align: right; margin-top: 10px; font-size: 12px; }
        .totals .grand { font-size: 16px; font-weight: bold; color: #4361ee; }
        .footer { text-align: center; margin-top: 30px; padding-top: 15px; border-top: 1px solid #ddd; font-size: 10px; color: #999; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 10px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>' . htmlspecialchars($shopName) . '</h2>
        <p>' . htmlspecialchars($settings['shop_address'] ?? '') . '</p>
        <p>Phone: ' . htmlspecialchars($settings['shop_phone'] ?? '') . ' | Email: ' . htmlspecialchars($settings['shop_email'] ?? '') . '</p>
    </div>
    <div class="invoice-title">SALE INVOICE</div>
    <div class="details">
        <table>
            <tr>
                <td><strong>Invoice #:</strong> ' . $sale['id'] . '</td>
                <td><strong>Date:</strong> ' . $sale['sale_date'] . '</td>
            </tr>
            <tr>
                <td><strong>Customer:</strong> ' . htmlspecialchars($sale['customer_name'] ?? 'Walk-in') . '</td>
                <td><strong>Cashier:</strong> ' . htmlspecialchars($sale['cashier'] ?? '-') . '</td>
            </tr>
            <tr>
                <td><strong>Payment:</strong> ' . ucfirst($sale['payment_method']) . '</td>
                <td><strong>Status:</strong> ' . ucfirst($sale['payment_status']) . '</td>
            </tr>
        </table>
    </div>
    <table class="items">
        <thead>
            <tr>
                <th>#</th>
                <th>Item</th>
                <th class="right">Qty</th>
                <th class="right">Price</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>';
        $i = 1;
        foreach ($items as $item) {
            $html .= '<tr>
                <td>' . $i++ . '</td>
                <td>' . htmlspecialchars($item['brand']) . ' ' . $item['size'] . ' (' . $item['voltage'] . 'V / ' . $item['plates'] . 'pl)</td>
                <td class="right">' . $item['quantity'] . '</td>
                <td class="right">' . $currencySymbol . ' ' . number_format($item['unit_price'], 2) . '</td>
                <td class="right">' . $currencySymbol . ' ' . number_format($item['total_price'], 2) . '</td>
            </tr>';
        }
        $html .= '</tbody>
    </table>
    <div class="totals">
        <p>Total: ' . $currencySymbol . ' ' . number_format($sale['total_amount'], 2) . '</p>
        <p>Amount Paid: ' . $currencySymbol . ' ' . number_format($sale['amount_paid'], 2) . '</p>
        <p class="grand">Balance: ' . $currencySymbol . ' ' . number_format($sale['total_amount'] - $sale['amount_paid'], 2) . '</p>
    </div>
    <div class="footer">
        <p>Thank you for your business!</p>
        <p>Generated by BatteryPro Management System</p>
    </div>
</body>
</html>';
        
        $mpdf->WriteHTML($html);
        $mpdf->Output('Invoice-' . $sale['id'] . '.pdf', 'I');
        exit;
    }

    public function reports() {
        if (!check_login()) { $this->redirectTo('/login'); }
        
        $db = (new Database())->getConnection();
        $totalSales = $db->query("SELECT COALESCE(SUM(total_amount),0) as total, COALESCE(SUM(total_profit),0) as profit FROM sales")->fetch();
        $totalExpenses = $db->query("SELECT COALESCE(SUM(amount),0) as total FROM expenses")->fetch();
        $totalStock = $db->query("SELECT COALESCE(SUM(quantity),0) as total FROM stock")->fetch();
        $customerCount = $db->query("SELECT COUNT(*) as total FROM customers")->fetch();
        $supplierCount = $db->query("SELECT COUNT(*) as total FROM suppliers")->fetch();
        $saleCount = $db->query("SELECT COUNT(*) as total FROM sales")->fetch();
        
        $settings = $db->query("SELECT * FROM settings LIMIT 1")->fetch();
        $shopName = $settings['shop_name'] ?? 'BatteryPro';
        $currencySymbol = $settings['currency_symbol'] ?? 'Rs.';
        
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 20,
            'margin_bottom' => 20,
        ]);
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #4361ee; }
        .header h2 { margin: 0; color: #4361ee; font-size: 22px; }
        .report-title { text-align: center; font-size: 18px; font-weight: bold; margin: 15px 0; color: #333; }
        .date { text-align: center; color: #666; font-size: 11px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table th { background: #4361ee; color: white; padding: 8px 10px; text-align: left; }
        table td { padding: 8px 10px; border-bottom: 1px solid #ddd; }
        .right { text-align: right; }
        .footer { text-align: center; margin-top: 30px; padding-top: 15px; border-top: 1px solid #ddd; font-size: 10px; color: #999; }
    </style>
</head>
<body>
    <div class="header"><h2>' . htmlspecialchars($shopName) . '</h2></div>
    <div class="report-title">Business Report</div>
    <div class="date">Generated: ' . date('Y-m-d H:i:s') . '</div>
    <table>
        <tr><th>Metric</th><th class="right">Value</th></tr>
        <tr><td>Total Revenue</td><td class="right">' . $currencySymbol . ' ' . number_format($totalSales['total'], 2) . '</td></tr>
        <tr><td>Total Profit</td><td class="right">' . $currencySymbol . ' ' . number_format($totalSales['profit'], 2) . '</td></tr>
        <tr><td>Total Expenses</td><td class="right">' . $currencySymbol . ' ' . number_format($totalExpenses['total'], 2) . '</td></tr>
        <tr><td><strong>Net Profit</strong></td><td class="right"><strong>' . $currencySymbol . ' ' . number_format($totalSales['profit'] - $totalExpenses['total'], 2) . '</strong></td></tr>
        <tr><td>Total Sales Count</td><td class="right">' . $saleCount['total'] . '</td></tr>
        <tr><td>Stock Items</td><td class="right">' . $totalStock['total'] . '</td></tr>
        <tr><td>Customers</td><td class="right">' . $customerCount['total'] . '</td></tr>
        <tr><td>Suppliers</td><td class="right">' . $supplierCount['total'] . '</td></tr>
    </table>
    <div class="footer">Generated by BatteryPro Management System</div>
</body>
</html>';
        
        $mpdf->WriteHTML($html);
        $mpdf->Output('Business-Report-' . date('Y-m-d') . '.pdf', 'I');
        exit;
    }
}
