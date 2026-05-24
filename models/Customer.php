<?php
/**
 * Customer Model
 */

class Customer extends BaseModel {
    protected $table = 'customers';

    public function getTotalCustomers() {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM {$this->table}");
        $stmt->execute();
        $result = $stmt->fetch();
        return (int)$result['total'];
    }
}
