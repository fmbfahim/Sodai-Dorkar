<?php

namespace Models;

use Core\Database;

class User {
    protected $db;

    public function __construct() {
        $config = require __DIR__ . '/../../config/database.php';
        $this->db = new Database($config);
    }

    public function find($id) {
        $stmt = $this->db->query("SELECT * FROM users WHERE id = :id", ['id' => $id]);
        return $stmt->fetch();
    }

    public function findByUsername($username) {
        $stmt = $this->db->query("SELECT * FROM users WHERE username = :username", ['username' => $username]);
        return $stmt->fetch();
    }

    public function update($id, $data) {
        // If password is set, we need to hash it. But ideally, logic might be in controller or here.
        // Let's assume data comes prepared or we handle password separately.
        
        $sql = "UPDATE users SET name = :name, username = :username, email = :email";
        $params = [
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'id' => $id
        ];

        if (!empty($data['password'])) {
            $sql .= ", password = :password";
            $params['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $sql .= " WHERE id = :id";
        
        $this->db->query($sql, $params);
    }
    public function getByRole($role) {
        $stmt = $this->db->query("SELECT * FROM users WHERE role = :role ORDER BY id DESC", ['role' => $role]);
        return $stmt->fetchAll();
    }
}
