<?php
namespace App\Models;

use App\Core\Database;

class User {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }

    public function getAllUsers() {
        return $this->db->queryFetchAll("SELECT * FROM users");
    }

    public function findByEmail($email) {
        return $this->db->queryFetch("SELECT * FROM users WHERE email = ?", [$email]);
    }

    public function findById($id) {
        return $this->db->queryFetch("SELECT id, name, email, role, created_at FROM users WHERE id = ?", [$id]);
    }

    public function updateLastLogin($id) {
        $this->db->query("UPDATE users SET last_login = NOW() WHERE id = ?", [$id]);
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