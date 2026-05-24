<?php

class PurchaseOrder extends BaseModel {
    protected $table = 'purchase_orders';

    public function getWithSupplier($id) {
        $stmt = $this->db->prepare("SELECT po.*, s.name as supplier_name FROM purchase_orders po LEFT JOIN suppliers s ON po.supplier_id = s.id WHERE po.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getAllWithSupplier() {
        return $this->db->query("SELECT po.*, s.name as supplier_name FROM purchase_orders po LEFT JOIN suppliers s ON po.supplier_id = s.id ORDER BY po.created_at DESC")->fetchAll();
    }

    public function getItems($poId) {
        $stmt = $this->db->prepare("SELECT poi.*, b.brand, b.size, b.voltage, b.plates FROM purchase_order_items poi JOIN batteries b ON poi.battery_id = b.id WHERE poi.purchase_order_id = ?");
        $stmt->execute([$poId]);
        return $stmt->fetchAll();
    }

    public function generateOrderNumber() {
        $prefix = 'PO-' . date('Ymd') . '-';
        $stmt = $this->db->query("SELECT COUNT(*) as cnt FROM purchase_orders WHERE order_number LIKE '$prefix%'")->fetch();
        return $prefix . str_pad($stmt['cnt'] + 1, 3, '0', STR_PAD_LEFT);
    }
}
