<?php
require_once __DIR__ . '/BaseController.php';

class ExportController extends BaseController {

    private function getSettings() {
        try {
            return (new Database())->getConnection()->query("SELECT * FROM settings LIMIT 1")->fetch();
        } catch (Exception $e) {
            return null;
        }
    }

    private function exportToExcel($data, $headers, $filename, $title = '') {
        if (!check_login()) { $this->redirectTo('/login'); }
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Title row
        if ($title) {
            $sheet->setCellValue('A1', $title);
            $sheet->mergeCells(chr(64 + count($headers)) . '1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $startRow = 3;
        } else {
            $startRow = 1;
        }

        // Header row
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . $startRow, $h);
            $sheet->getStyle($col . $startRow)->getFont()->setBold(true);
            $col++;
        }

        // Data rows
        $rowNum = $startRow + 1;
        foreach ($data as $row) {
            $col = 'A';
            foreach ($row as $cell) {
                $sheet->setCellValue($col . $rowNum, $cell);
                $col++;
            }
            $rowNum++;
        }

        // Auto-size columns
        foreach (range('A', chr(64 + count($headers))) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function suppliers() {
        if (!check_login()) { $this->redirectTo('/login'); }
        $s = (new Supplier())->findAll();
        $data = []; foreach ($s as $r) { $data[] = [$r['id'], $r['name'], $r['phone'], $r['email'], $r['address']]; }
        $this->exportToExcel($data, ['ID','Name','Phone','Email','Address'], 'suppliers.xlsx', 'Suppliers Export');
    }

    public function batteries() {
        if (!check_login()) { $this->redirectTo('/login'); }
        $b = (new Battery())->findAll();
        $data = []; foreach ($b as $r) { $data[] = [$r['id'], $r['brand'], $r['size'], $r['voltage'], $r['plates'], $r['purchase_price'], $r['sale_price']]; }
        $this->exportToExcel($data, ['ID','Brand','Size','Voltage','Plates','Purchase Price','Sale Price'], 'inventory.xlsx', 'Inventory Export');
    }

    public function customers() {
        if (!check_login()) { $this->redirectTo('/login'); }
        $c = (new Customer())->findAll();
        $data = []; foreach ($c as $r) { $data[] = [$r['id'], $r['name'], $r['phone'], $r['email'], $r['address']]; }
        $this->exportToExcel($data, ['ID','Name','Phone','Email','Address'], 'customers.xlsx', 'Customers Export');
    }

    public function sales() {
        if (!check_login()) { $this->redirectTo('/login'); }
        $db = (new Database())->getConnection();
        $sales = $db->query("SELECT s.id, s.sale_date, c.name as customer, u.full_name as cashier, s.total_amount, s.total_profit, s.payment_status FROM sales s LEFT JOIN customers c ON s.customer_id = c.id LEFT JOIN users u ON s.user_id = u.id ORDER BY s.id")->fetchAll();
        $data = []; foreach ($sales as $r) { $data[] = [$r['id'], $r['sale_date'], $r['customer'] ?? 'Walk-in', $r['cashier'] ?? '-', $r['total_amount'], $r['total_profit'], $r['payment_status']]; }
        $this->exportToExcel($data, ['ID','Date','Customer','Cashier','Amount','Profit','Status'], 'sales.xlsx', 'Sales Export');
    }

    public function expenses() {
        if (!check_login()) { $this->redirectTo('/login'); }
        $e = (new Expense())->findAll();
        $data = []; foreach ($e as $r) { $data[] = [$r['id'], $r['expense_date'], $r['category'], $r['description'], $r['amount'], $r['payment_method']]; }
        $this->exportToExcel($data, ['ID','Date','Category','Description','Amount','Payment Method'], 'expenses.xlsx', 'Expenses Export');
    }
}
