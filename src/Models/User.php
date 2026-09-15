<?php

namespace Models;

use Core\Database;

class User {
    protected $db;

    public function __construct() {
        $config = require __DIR__ . '/../../config/database.php';
        $this->db = new Database($config);
    }

    public static function getRoles() {
        return [
            'admin' => 'Administrator',
            'manager' => 'Store Manager',
            'accountant' => 'Accountant',
            'staff' => 'General Staff',
            'agent' => 'Customer Agent',
            'delivery_man' => 'Delivery Rider'
        ];
    }

    public function all($filters = []) {
        $sql = "SELECT u.*, e.emp_code, e.id as employee_id
                FROM users u
                LEFT JOIN employees e ON u.id = e.user_id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['role'])) {
            $sql .= " AND u.role = ?";
            $params[] = $filters['role'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND u.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (u.name LIKE ? OR u.username LIKE ? OR u.phone LIKE ? OR u.email LIKE ?)";
            $term = '%' . trim($filters['search']) . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        $sql .= " ORDER BY u.id DESC";
        return $this->db->query($sql, $params)->fetchAll();
    }

    public function find($id) {
        $stmt = $this->db->query("
            SELECT u.*, e.emp_code, e.id as employee_id 
            FROM users u 
            LEFT JOIN employees e ON u.id = e.user_id 
            WHERE u.id = ?
        ", [$id]);
        return $stmt->fetch();
    }

    public function findByUsername($username) {
        $stmt = $this->db->query("SELECT * FROM users WHERE username = ?", [$username]);
        return $stmt->fetch();
    }

    public function create($data) {
        $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);
        $permissions = isset($data['permissions']) ? (is_array($data['permissions']) ? json_encode(array_values($data['permissions'])) : $data['permissions']) : null;
        $sql = "INSERT INTO users (name, email, phone, username, password, role, permissions, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $this->db->query($sql, [
            $data['name'],
            $data['email'] ?? null,
            $data['phone'] ?? null,
            $data['username'],
            $passwordHash,
            $data['role'] ?? 'staff',
            $permissions,
            $data['status'] ?? 'active'
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE users SET name = :name, username = :username, email = :email, phone = :phone, role = :role, status = :status";
        $params = [
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'] ?? 'staff',
            'status' => $data['status'] ?? 'active',
            'id' => $id
        ];

        if (array_key_exists('permissions', $data)) {
            $sql .= ", permissions = :permissions";
            $params['permissions'] = is_array($data['permissions']) ? json_encode(array_values($data['permissions'])) : $data['permissions'];
        }

        if (!empty($data['password'])) {
            $sql .= ", password = :password";
            $params['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        $sql .= " WHERE id = :id";
        return $this->db->query($sql, $params);
    }

    public function toggleStatus($id) {
        $user = $this->find($id);
        if (!$user) return false;
        $newStatus = ($user['status'] === 'active') ? 'inactive' : 'active';
        $this->db->query("UPDATE users SET status = ? WHERE id = ?", [$newStatus, $id]);
        return $newStatus;
    }

    public function delete($id) {
        return $this->db->query("DELETE FROM users WHERE id = ?", [$id]);
    }

    public function getByRole($role) {
        $stmt = $this->db->query("SELECT * FROM users WHERE role = ? ORDER BY id DESC", [$role]);
        return $stmt->fetchAll();
    }
}
