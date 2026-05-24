<?php
/**
 * User Model
 */

class User extends BaseModel {
    protected $table = 'users';

    public function findByUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function register($data) {
        // Hash password before storing
        if (isset($data['password'])) {
            $data['password'] = hash_password($data['password']);
        }
        
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return parent::create($data);
    }

    public function login($username, $password) {
        $user = $this->findByUsername($username);
        
        if ($user && verify_password($password, $user['password'])) {
            // Update last login
            $this->update($user['id'], [
                'last_login' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            // Remove password from returned data
            unset($user['password']);
            return $user;
        }
        
        return false;
    }

    public function updateLastActivity($userId) {
        return $this->update($userId, [
            'last_activity' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}