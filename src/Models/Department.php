<?php

namespace Models;

use Core\Database;

class Department {
    protected $db;

    public function __construct() {
        $config = require __DIR__ . '/../../config/database.php';
        $this->db = new Database($config);
    }

    public function all() {
        $sql = "SELECT d.*, 
                       (SELECT COUNT(*) FROM employees WHERE department_id = d.id AND status != 'terminated') as employee_count,
                       (SELECT COUNT(*) FROM designations WHERE department_id = d.id) as designation_count
                FROM departments d 
                ORDER BY d.name ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function find($id) {
        $stmt = $this->db->query("SELECT * FROM departments WHERE id = ?", [$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $sql = "INSERT INTO departments (name, description) VALUES (?, ?)";
        $this->db->query($sql, [$data['name'], $data['description'] ?? '']);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE departments SET name = ?, description = ? WHERE id = ?";
        return $this->db->query($sql, [$data['name'], $data['description'] ?? '', $id]);
    }

    public function delete($id) {
        return $this->db->query("DELETE FROM departments WHERE id = ?", [$id]);
    }

    public function getDesignations($departmentId = null) {
        if ($departmentId) {
            $stmt = $this->db->query("SELECT * FROM designations WHERE department_id = ? ORDER BY title ASC", [$departmentId]);
        } else {
            $stmt = $this->db->query("SELECT des.*, dep.name as department_name 
                                      FROM designations des 
                                      JOIN departments dep ON des.department_id = dep.id 
                                      ORDER BY dep.name ASC, des.title ASC");
        }
        return $stmt->fetchAll();
    }

    public function createDesignation($departmentId, $title) {
        $sql = "INSERT INTO designations (department_id, title) VALUES (?, ?)";
        $this->db->query($sql, [$departmentId, $title]);
        return $this->db->lastInsertId();
    }

    public function deleteDesignation($id) {
        return $this->db->query("DELETE FROM designations WHERE id = ?", [$id]);
    }
}
