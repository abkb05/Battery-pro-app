<?php
/**
 * Supplier Model
 */

class Supplier extends BaseModel {
    protected $table = 'suppliers';

    // Get total pending amount across all suppliers
    public function getPendingPayments() {
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(pending_amount), 0) as total_pending FROM {$this->table}");
        $stmt->execute();
        $result = $stmt->fetch();
        return (float)$result['total_pending'];
    }

    public function getSupplierStats($supplierId) {
        // Get total batteries purchased from this supplier
        $query = "
            SELECT 
                COALESCE(SUM(si.quantity), 0) as total_purchased,
                COALESCE(SUM(si.quantity * si.purchase_price), 0) as total_amount,
                COUNT(DISTINCT s.id) as purchase_count
            FROM suppliers sup
            LEFT JOIN batteries b ON b.supplier_id = sup.id
            LEFT JOIN stock st ON st.battery_id = b.id
            LEFT JOIN sale_items si ON si.stock_id = st.id
            LEFT JOIN sales s ON s.id = si.sale_id
            WHERE sup.id = ?
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$supplierId]);
        return $stmt->fetch();
    }

    public function getPurchaseHistory($supplierId, $limit = 10) {
        $query = "
            SELECT 
                b.brand,
                b.size,
                b.voltage,
                b.plates,
                si.quantity,
                si.purchase_price,
                s.sale_date
            FROM suppliers sup
            LEFT JOIN batteries b ON b.supplier_id = sup.id
            LEFT JOIN stock st ON st.battery_id = b.id
            LEFT JOIN sale_items si ON si.stock_id = st.id
            LEFT JOIN sales s ON s.id = si.sale_id
            WHERE sup.id = ?
            ORDER BY s.sale_date DESC
            LIMIT ?
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$supplierId, $limit]);
        return $stmt->fetchAll();
    }
}