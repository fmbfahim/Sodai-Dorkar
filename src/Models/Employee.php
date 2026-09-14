<?php

namespace Models;

use Core\Database;

class Employee {
    protected $db;

    public function __construct() {
        $config = require __DIR__ . '/../../config/database.php';
        $this->db = new Database($config);
    }

    public function generateEmpCode() {
        $stmt = $this->db->query("SELECT emp_code FROM employees ORDER BY id DESC LIMIT 1");
        $last = $stmt->fetch();
        if ($last && preg_match('/EMP-(\d+)/', $last['emp_code'], $m)) {
            $next = intval($m[1]) + 1;
            return 'EMP-' . str_pad($next, 4, '0', STR_PAD_LEFT);
        }
        return 'EMP-1001';
    }

    public function all($filters = []) {
        $sql = "SELECT e.*, 
                       d.name as department_name, 
                       des.title as designation_title,
                       u.username as linked_username,
                       u.role as linked_role
                FROM employees e
                LEFT JOIN departments d ON e.department_id = d.id
                LEFT JOIN designations des ON e.designation_id = des.id
                LEFT JOIN users u ON e.user_id = u.id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['department_id'])) {
            $sql .= " AND e.department_id = ?";
            $params[] = $filters['department_id'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND e.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (e.name LIKE ? OR e.emp_code LIKE ? OR e.phone LIKE ? OR e.nid LIKE ?)";
            $term = '%' . trim($filters['search']) . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        $sql .= " ORDER BY e.id DESC";
        return $this->db->query($sql, $params)->fetchAll();
    }

    public function find($id) {
        $sql = "SELECT e.*, 
                       d.name as department_name, 
                       des.title as designation_title,
                       u.username as linked_username,
                       u.role as linked_role
                FROM employees e
                LEFT JOIN departments d ON e.department_id = d.id
                LEFT JOIN designations des ON e.designation_id = des.id
                LEFT JOIN users u ON e.user_id = u.id
                WHERE e.id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        if (empty($data['emp_code'])) {
            $data['emp_code'] = $this->generateEmpCode();
        }

        $sql = "INSERT INTO employees (
                    emp_code, user_id, name, phone, email, nid, gender, joining_date,
                    department_id, designation_id, employment_type, basic_salary, house_rent,
                    medical_allowance, other_allowance, bank_name, bank_account_no,
                    mobile_banking_type, mobile_banking_number, emergency_contact, address,
                    photo_path, status
                ) VALUES (
                    :emp_code, :user_id, :name, :phone, :email, :nid, :gender, :joining_date,
                    :department_id, :designation_id, :employment_type, :basic_salary, :house_rent,
                    :medical_allowance, :other_allowance, :bank_name, :bank_account_no,
                    :mobile_banking_type, :mobile_banking_number, :emergency_contact, :address,
                    :photo_path, :status
                )";

        $params = [
            'emp_code' => $data['emp_code'],
            'user_id' => !empty($data['user_id']) ? $data['user_id'] : null,
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'nid' => $data['nid'] ?? null,
            'gender' => $data['gender'] ?? 'male',
            'joining_date' => !empty($data['joining_date']) ? $data['joining_date'] : date('Y-m-d'),
            'department_id' => !empty($data['department_id']) ? $data['department_id'] : null,
            'designation_id' => !empty($data['designation_id']) ? $data['designation_id'] : null,
            'employment_type' => $data['employment_type'] ?? 'full_time',
            'basic_salary' => floatval($data['basic_salary'] ?? 0),
            'house_rent' => floatval($data['house_rent'] ?? 0),
            'medical_allowance' => floatval($data['medical_allowance'] ?? 0),
            'other_allowance' => floatval($data['other_allowance'] ?? 0),
            'bank_name' => $data['bank_name'] ?? null,
            'bank_account_no' => $data['bank_account_no'] ?? null,
            'mobile_banking_type' => $data['mobile_banking_type'] ?? null,
            'mobile_banking_number' => $data['mobile_banking_number'] ?? null,
            'emergency_contact' => $data['emergency_contact'] ?? null,
            'address' => $data['address'] ?? null,
            'photo_path' => $data['photo_path'] ?? null,
            'status' => $data['status'] ?? 'active'
        ];

        $this->db->query($sql, $params);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE employees SET 
                    user_id = :user_id,
                    name = :name,
                    phone = :phone,
                    email = :email,
                    nid = :nid,
                    gender = :gender,
                    joining_date = :joining_date,
                    department_id = :department_id,
                    designation_id = :designation_id,
                    employment_type = :employment_type,
                    basic_salary = :basic_salary,
                    house_rent = :house_rent,
                    medical_allowance = :medical_allowance,
                    other_allowance = :other_allowance,
                    bank_name = :bank_name,
                    bank_account_no = :bank_account_no,
                    mobile_banking_type = :mobile_banking_type,
                    mobile_banking_number = :mobile_banking_number,
                    emergency_contact = :emergency_contact,
                    address = :address,
                    status = :status";

        $params = [
            'user_id' => !empty($data['user_id']) ? $data['user_id'] : null,
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'nid' => $data['nid'] ?? null,
            'gender' => $data['gender'] ?? 'male',
            'joining_date' => !empty($data['joining_date']) ? $data['joining_date'] : date('Y-m-d'),
            'department_id' => !empty($data['department_id']) ? $data['department_id'] : null,
            'designation_id' => !empty($data['designation_id']) ? $data['designation_id'] : null,
            'employment_type' => $data['employment_type'] ?? 'full_time',
            'basic_salary' => floatval($data['basic_salary'] ?? 0),
            'house_rent' => floatval($data['house_rent'] ?? 0),
            'medical_allowance' => floatval($data['medical_allowance'] ?? 0),
            'other_allowance' => floatval($data['other_allowance'] ?? 0),
            'bank_name' => $data['bank_name'] ?? null,
            'bank_account_no' => $data['bank_account_no'] ?? null,
            'mobile_banking_type' => $data['mobile_banking_type'] ?? null,
            'mobile_banking_number' => $data['mobile_banking_number'] ?? null,
            'emergency_contact' => $data['emergency_contact'] ?? null,
            'address' => $data['address'] ?? null,
            'status' => $data['status'] ?? 'active',
            'id' => $id
        ];

        if (!empty($data['photo_path'])) {
            $sql .= ", photo_path = :photo_path";
            $params['photo_path'] = $data['photo_path'];
        }

        $sql .= " WHERE id = :id";
        return $this->db->query($sql, $params);
    }

    public function delete($id) {
        return $this->db->query("DELETE FROM employees WHERE id = ?", [$id]);
    }

    public function getStatistics() {
        $total = $this->db->query("SELECT COUNT(*) FROM employees")->fetchColumn();
        $active = $this->db->query("SELECT COUNT(*) FROM employees WHERE status = 'active'")->fetchColumn();
        $onLeave = $this->db->query("SELECT COUNT(*) FROM employees WHERE status = 'on_leave'")->fetchColumn();
        $monthlySalarySum = $this->db->query("SELECT SUM(basic_salary + house_rent + medical_allowance + other_allowance) FROM employees WHERE status = 'active'")->fetchColumn();

        return [
            'total' => (int)$total,
            'active' => (int)$active,
            'on_leave' => (int)$onLeave,
            'monthly_salary_estimate' => floatval($monthlySalarySum ?? 0)
        ];
    }

    public function getAttendanceHistory($id, $limit = 30) {
        $stmt = $this->db->query("SELECT * FROM attendances WHERE employee_id = ? ORDER BY date DESC LIMIT ?", [$id, $limit]);
        return $stmt->fetchAll();
    }

    public function getRecentPayrolls($id, $limit = 12) {
        $stmt = $this->db->query("SELECT * FROM payrolls WHERE employee_id = ? ORDER BY salary_month DESC LIMIT ?", [$id, $limit]);
        return $stmt->fetchAll();
    }

    public function getLeaveHistory($id, $limit = 10) {
        $stmt = $this->db->query("SELECT * FROM leave_requests WHERE employee_id = ? ORDER BY start_date DESC LIMIT ?", [$id, $limit]);
        return $stmt->fetchAll();
    }
}
