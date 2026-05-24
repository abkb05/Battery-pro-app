<?php
/**
 * Battery Model
 */

class Battery extends BaseModel {
    protected $table = 'batteries';

    public function getTotalStock() {
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(quantity), 0) as total FROM stock");
        $stmt->execute();
        $result = $stmt->fetch();
        return (int)$result['total'];
    }

    public function getLowStock($threshold = 5) {
        $query = "
            SELECT b.*, 
                   COALESCE(SUM(s.quantity), 0) as total_stock
            FROM batteries b
            LEFT JOIN stock s ON s.battery_id = b.id
            GROUP BY b.id
            HAVING total_stock < ?
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$threshold]);
        return $stmt->fetchAll();
    }

    public function getStockQuantity($batteryId) {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(quantity), 0) as total_quantity
            FROM stock 
            WHERE battery_id = ?
        ");
        $stmt->execute([$batteryId]);
        $result = $stmt->fetch();
        return (int)$result['total_quantity'];
    }

    public function getSupplierBatteries($supplierId) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE supplier_id = ?");
        $stmt->execute([$supplierId]);
        return $stmt->fetchAll();
    }

    public function getPopularBatteries($limit = 5) {
        $query = "
            SELECT b.*, 
                   COALESCE(SUM(si.quantity), 0) as total_sold
            FROM batteries b
            LEFT JOIN stock s ON s.battery_id = b.id
            LEFT JOIN sale_items si ON si.stock_id = s.id
            GROUP BY b.id
            ORDER BY total_sold DESC
            LIMIT ?
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}