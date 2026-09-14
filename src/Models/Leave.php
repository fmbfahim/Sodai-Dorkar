<?php

namespace Models;

use Core\Database;

class Leave {
    protected $db;

    public function __construct() {
        $config = require __DIR__ . '/../../config/database.php';
        $this->db = new Database($config);
    }

    public function all($status = null) {
        $sql = "SELECT lr.*, 
                       e.name as employee_name, e.emp_code, e.phone,
                       d.name as department_name,
                       u.name as approver_name
                FROM leave_requests lr
                JOIN employees e ON lr.employee_id = e.id
                LEFT JOIN departments d ON e.department_id = d.id
                LEFT JOIN users u ON lr.approved_by = u.id
                WHERE 1=1";
        $params = [];

        if ($status) {
            $sql .= " AND lr.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY lr.id DESC";
        return $this->db->query($sql, $params)->fetchAll();
    }

    public function create($data) {
        $start = new \DateTime($data['start_date']);
        $end = new \DateTime($data['end_date']);
        $days = $start->diff($end)->days + 1;

        $sql = "INSERT INTO leave_requests (employee_id, leave_type, start_date, end_date, total_days, reason, status)
                VALUES (?, ?, ?, ?, ?, ?, 'pending')";
        $this->db->query($sql, [
            $data['employee_id'],
            $data['leave_type'] ?? 'casual',
            $data['start_date'],
            $data['end_date'],
            $days,
            $data['reason'] ?? ''
        ]);
        return $this->db->lastInsertId();
    }

    public function updateStatus($id, $status, $approvedBy = null) {
        $sql = "UPDATE leave_requests SET status = ?, approved_by = ? WHERE id = ?";
        return $this->db->query($sql, [$status, $approvedBy, $id]);
    }

    public function delete($id) {
        return $this->db->query("DELETE FROM leave_requests WHERE id = ?", [$id]);
    }
}
