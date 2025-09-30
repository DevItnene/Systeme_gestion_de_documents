<?php
namespace App\Models;

use App\Core\Database;

class User {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }

    public function findByEmail($email) {
        $stmt = $this->db->queryFetch("SELECT * FROM users WHERE email = ?", [$email]);
        return $stmt;
    }

    public function findById($id) {
        $stmt = $this->db->queryFetch("SELECT id, name, email, role, created_at FROM users WHERE id = ?", [$id]);
        return $stmt;
    }

    public function updateLastLogin($id) {
        $stmt = $this->db->query("UPDATE users SET last_login = NOW() WHERE id = ?", [$id]);
        return true;
    }

    public function createUser($name, $email, $password) {
        $ExistingUser = $this->findByEmail($email);
        if ($ExistingUser) {
            return false;
        }
        $passwordHash = password_hash($password, \PASSWORD_DEFAULT);
        $result = $this->db->query("INSERT INTO users (name, email, password) Values(?, ?, ?)", [$name, $email, $passwordHash]);
        
        if ($result) {
            // $userId = $this->db->lastInsertId();
            return true;
        }

        return false;
    }
}
?>