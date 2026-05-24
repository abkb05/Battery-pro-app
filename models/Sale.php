<?php
/**
 * Sale Model
 */

class Sale extends BaseModel {
    protected $table = 'sales';

    public function getTotalSalesAmount() {
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(total_amount), 0) as total_sales FROM {$this->table}");
        $stmt->execute();
        $result = $stmt->fetch();
        return (float)$result['total_sales'];
    }

    public function getTotalProfit() {
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(total_profit), 0) as total_profit FROM {$this->table}");
        $stmt->execute();
        $result = $stmt->fetch();
        return (float)$result['total_profit'];
    }

    public function getSalesSummary($period = 'daily') {
        $dateExpr = '';
        switch (strtolower($period)) {
            case 'daily':
                $dateExpr = "DATE(sale_date) = CURDATE()";
                break;
            case 'weekly':
                $dateExpr = "YEARWEEK(sale_date, 1) = YEARWEEK(CURDATE(), 1)";
                break;
            case 'monthly':
                $dateExpr = "YEAR(sale_date) = YEAR(CURDATE()) AND MONTH(sale_date) = MONTH(CURDATE())";
                break;
            default:
                $dateExpr = "1=1";
        }
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(total_amount), 0) as total_amount, COALESCE(SUM(total_profit), 0) as total_profit FROM {$this->table} WHERE $dateExpr");
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getRecentActivities($limit = 5) {
        $stmt = $this->db->prepare("SELECT s.id, s.sale_date, s.total_amount, c.name as customer_name, u.full_name as cashier FROM {$this->table} s LEFT JOIN customers c ON s.customer_id = c.id LEFT JOIN users u ON s.user_id = u.id ORDER BY s.sale_date DESC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}
