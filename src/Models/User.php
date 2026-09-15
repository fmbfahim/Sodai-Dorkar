<?php

namespace Models;

use Core\Database;

class User {
    protected $db;
    private static $schemaChecked = false;

    public function __construct() {
        $config = require __DIR__ . '/../../config/database.php';
        $this->db = new Database($config);
        $this->ensureSchema();
    }

    public function ensureSchema() {
        if (self::$schemaChecked) return;
        self::$schemaChecked = true;

        try {
            $cols = $this->db->query("SHOW COLUMNS FROM users")->fetchAll();
            $colNames = array_column($cols, 'Field');

            if (!in_array('email', $colNames)) {
                $this->db->query("ALTER TABLE users ADD COLUMN email VARCHAR(100) NULL AFTER name");
            }

            if (!in_array('phone', $colNames)) {
                $this->db->query("ALTER TABLE users ADD COLUMN phone VARCHAR(20) NULL AFTER name");
            }

            if (!in_array('status', $colNames)) {
                $this->db->query("ALTER TABLE users ADD COLUMN status ENUM('active', 'inactive') DEFAULT 'active' AFTER role");
            }

            if (!in_array('permissions', $colNames)) {
                $this->db->query("ALTER TABLE users ADD COLUMN permissions TEXT NULL AFTER role");
            }

            // Ensure role enum has all roles
            $this->db->query("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'manager', 'accountant', 'staff', 'agent', 'delivery_man') NOT NULL DEFAULT 'staff'");

            // Set Super Admin permissions if missing
            $this->db->query("UPDATE users SET permissions = '[\"*\"]' WHERE role = 'admin' AND (permissions IS NULL OR permissions = '' OR permissions = '[]')");
        } catch (\Throwable $e) {
            error_log("User::ensureSchema error: " . $e->getMessage());
        }
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

    private function hasEmployeesTable() {
        static $hasTable = null;
        if ($hasTable !== null) return $hasTable;
        try {
            $test = $this->db->query("SHOW TABLES LIKE 'employees'")->fetch();
            $hasTable = !empty($test);
        } catch (\Throwable $e) {
            $hasTable = false;
        }
        return $hasTable;
    }

    public function all($filters = []) {
        $hasEmp = $this->hasEmployeesTable();

        if ($hasEmp) {
            $sql = "SELECT u.*, e.emp_code, e.id as employee_id
                    FROM users u
                    LEFT JOIN employees e ON u.id = e.user_id
                    WHERE 1=1";
        } else {
            $sql = "SELECT u.*, NULL as emp_code, NULL as employee_id
                    FROM users u
                    WHERE 1=1";
        }
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
            $term = '%' . trim($filters['search']) . '%';
            $sql .= " AND (u.name LIKE ? OR u.username LIKE ? OR u.phone LIKE ? OR u.email LIKE ?)";
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        $sql .= " ORDER BY u.id DESC";
        return $this->db->query($sql, $params)->fetchAll();
    }

    public function find($id) {
        $hasEmp = $this->hasEmployeesTable();

        if ($hasEmp) {
            $stmt = $this->db->query("
                SELECT u.*, e.emp_code, e.id as employee_id 
                FROM users u 
                LEFT JOIN employees e ON u.id = e.user_id 
                WHERE u.id = ?
            ", [$id]);
        } else {
            $stmt = $this->db->query("
                SELECT u.*, NULL as emp_code, NULL as employee_id 
                FROM users u 
                WHERE u.id = ?
            ", [$id]);
        }
        return $stmt->fetch();
    }

    public function findByUsername($username) {
        $stmt = $this->db->query("SELECT * FROM users WHERE username = ?", [$username]);
        return $stmt->fetch();
    }

    public function create($data) {
        $this->ensureSchema();
        $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);
        
        $cols = $this->db->query("SHOW COLUMNS FROM users")->fetchAll();
        $colNames = array_column($cols, 'Field');

        $insertData = [
            'name' => $data['name'],
            'username' => $data['username'],
            'password' => $passwordHash,
            'role' => $data['role'] ?? 'staff'
        ];

        if (in_array('email', $colNames)) {
            $insertData['email'] = $data['email'] ?? null;
        }
        if (in_array('phone', $colNames)) {
            $insertData['phone'] = $data['phone'] ?? null;
        }
        if (in_array('status', $colNames)) {
            $insertData['status'] = $data['status'] ?? 'active';
        }
        if (in_array('permissions', $colNames)) {
            $permissions = isset($data['permissions']) ? (is_array($data['permissions']) ? json_encode(array_values($data['permissions'])) : $data['permissions']) : null;
            $insertData['permissions'] = $permissions;
        }

        $fields = implode(', ', array_keys($insertData));
        $placeholders = implode(', ', array_fill(0, count($insertData), '?'));
        $sql = "INSERT INTO users ({$fields}) VALUES ({$placeholders})";

        $this->db->query($sql, array_values($insertData));
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $this->ensureSchema();
        $cols = $this->db->query("SHOW COLUMNS FROM users")->fetchAll();
        $colNames = array_column($cols, 'Field');

        $updates = [
            'name = :name',
            'username = :username',
            'role = :role'
        ];
        $params = [
            'name' => $data['name'],
            'username' => $data['username'],
            'role' => $data['role'] ?? 'staff',
            'id' => $id
        ];

        if (in_array('email', $colNames)) {
            $updates[] = 'email = :email';
            $params['email'] = $data['email'] ?? null;
        }
        if (in_array('phone', $colNames)) {
            $updates[] = 'phone = :phone';
            $params['phone'] = $data['phone'] ?? null;
        }
        if (in_array('status', $colNames)) {
            $updates[] = 'status = :status';
            $params['status'] = $data['status'] ?? 'active';
        }
        if (in_array('permissions', $colNames) && array_key_exists('permissions', $data)) {
            $updates[] = 'permissions = :permissions';
            $params['permissions'] = is_array($data['permissions']) ? json_encode(array_values($data['permissions'])) : $data['permissions'];
        }

        if (!empty($data['password'])) {
            $updates[] = 'password = :password';
            $params['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = :id";
        return $this->db->query($sql, $params);
    }

    public function toggleStatus($id) {
        $user = $this->find($id);
        if (!$user) return false;
        $newStatus = (($user['status'] ?? 'active') === 'active') ? 'inactive' : 'active';
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
