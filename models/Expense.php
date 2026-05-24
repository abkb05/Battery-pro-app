<?php
/**
 * Expense Model
 */

class Expense extends BaseModel {
    protected $table = 'expenses';

    public function getTotalExpenses() {
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(amount), 0) as total_expenses FROM {$this->table}");
        $stmt->execute();
        $result = $stmt->fetch();
        return (float)$result['total_expenses'];
    }
}
