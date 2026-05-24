<?php
/**
 * Base Model Class
 */

class BaseModel {
    protected $db;
    protected $table;

    public function __construct($dbConnection = null) {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findAll() {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create($data) {
        $fields = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $stmt = $this->db->prepare("INSERT INTO {$this->table} ({$fields}) VALUES ({$placeholders})");
        $stmt->execute(array_values($data));
        
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $setClause = '';
        $values = [];
        
        foreach ($data as $key => $value) {
            $setClause .= "{$key} = ?, ";
            $values[] = $value;
        }
        
        $setClause = rtrim($setClause, ', ');
        $values[] = $id;
        
        $stmt = $this->db->prepare("UPDATE {$this->table} SET {$setClause} WHERE id = ?");
        return $stmt->execute($values);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function where($conditions) {
        $whereClause = '';
        $values = [];
        
        foreach ($conditions as $key => $value) {
            $whereClause .= "{$key} = ? AND ";
            $values[] = $value;
        }
        
        $whereClause = rtrim($whereClause, 'AND ');
        
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$whereClause}");
        $stmt->execute($values);
        return $stmt->fetchAll();
    }

    public function count($conditions = []) {
        $whereClause = '';
        $values = [];
        
        if (!empty($conditions)) {
            foreach ($conditions as $key => $value) {
                $whereClause .= "{$key} = ? AND ";
                $values[] = $value;
            }
            $whereClause = rtrim($whereClause, 'AND ');
            $whereClause = "WHERE {$whereClause}";
        }
        
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM {$this->table} {$whereClause}");
        $stmt->execute($values);
        $result = $stmt->fetch();
        return (int)$result['total'];
    }
}