<?php

namespace Models;

use Core\Database;

class Payroll {
    protected $db;

    public function __construct() {
        $config = require __DIR__ . '/../../config/database.php';
        $this->db = new Database($config);
    }

    public function getByMonth($month) {
        $sql = "SELECT p.*, 
                       e.name as employee_name, e.emp_code, e.phone, e.bank_name, e.bank_account_no, 
                       e.mobile_banking_type, e.mobile_banking_number,
                       d.name as department_name, des.title as designation_title
                FROM payrolls p
                JOIN employees e ON p.employee_id = e.id
                LEFT JOIN departments d ON e.department_id = d.id
                LEFT JOIN designations des ON e.designation_id = des.id
                WHERE p.salary_month = ?
                ORDER BY e.name ASC";
        return $this->db->query($sql, [$month])->fetchAll();
    }

    public function find($id) {
        $sql = "SELECT p.*, 
                       e.name as employee_name, e.emp_code, e.phone, e.email, e.nid, e.joining_date,
                       e.bank_name, e.bank_account_no, e.mobile_banking_type, e.mobile_banking_number,
                       e.house_rent, e.medical_allowance, e.other_allowance,
                       d.name as department_name, des.title as designation_title
                FROM payrolls p
                JOIN employees e ON p.employee_id = e.id
                LEFT JOIN departments d ON e.department_id = d.id
                LEFT JOIN designations des ON e.designation_id = des.id
                WHERE p.id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }

    public function generateForMonth($month, $workingDays = 30) {
        $employeeModel = new Employee();
        $employees = $employeeModel->all(['status' => 'active']);
        $attendanceModel = new Attendance();

        $pdo = $this->db->getConnection();
        $pdo->beginTransaction();

        $generatedCount = 0;

        try {
            $checkStmt = $pdo->prepare("SELECT id FROM payrolls WHERE employee_id = ? AND salary_month = ?");
            $insertStmt = $pdo->prepare("
                INSERT INTO payrolls (
                    salary_month, employee_id, working_days, present_days, absent_days, leave_days,
                    basic_salary, allowances, bonus, deductions, advance_salary_deduction,
                    net_salary, payment_method, status
                ) VALUES (
                    :salary_month, :employee_id, :working_days, :present_days, :absent_days, :leave_days,
                    :basic_salary, :allowances, :bonus, :deductions, :advance_salary_deduction,
                    :net_salary, 'cash', 'generated'
                )
            ");

            foreach ($employees as $emp) {
                $checkStmt->execute([$emp['id'], $month]);
                if ($checkStmt->rowCount() > 0) {
                    continue; // already generated
                }

                // Calculate attendance metrics
                $stats = $attendanceModel->getMonthlyStats($month, $emp['id']);
                $presentCount = intval($stats['present_count'] ?? 0) + intval($stats['late_count'] ?? 0);
                $halfDayCount = intval($stats['half_day_count'] ?? 0);
                $absentCount = intval($stats['absent_count'] ?? 0);
                $leaveCount = intval($stats['leave_count'] ?? 0);

                // If no attendances were logged for month, default to full working days
                if (intval($stats['total_logged'] ?? 0) === 0) {
                    $presentDays = $workingDays;
                    $absentDays = 0;
                } else {
                    $presentDays = $presentCount + ($halfDayCount * 0.5);
                    $absentDays = $absentCount + ($halfDayCount * 0.5);
                }

                $basicSalary = floatval($emp['basic_salary']);
                $allowances = floatval($emp['house_rent']) + floatval($emp['medical_allowance']) + floatval($emp['other_allowance']);
                
                // Absent deduction calculation (if any)
                $dailyRate = $workingDays > 0 ? ($basicSalary / $workingDays) : 0;
                $deductions = round($dailyRate * $absentDays, 2);

                $netSalary = max(0, round(($basicSalary + $allowances) - $deductions, 2));

                $insertStmt->execute([
                    'salary_month' => $month,
                    'employee_id' => $emp['id'],
                    'working_days' => $workingDays,
                    'present_days' => $presentDays,
                    'absent_days' => $absentDays,
                    'leave_days' => $leaveCount,
                    'basic_salary' => $basicSalary,
                    'allowances' => $allowances,
                    'bonus' => 0.00,
                    'deductions' => $deductions,
                    'advance_salary_deduction' => 0.00,
                    'net_salary' => $netSalary
                ]);

                $generatedCount++;
            }

            $pdo->commit();
            return $generatedCount;
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function update($id, $data) {
        $sql = "UPDATE payrolls SET 
                    working_days = :working_days,
                    present_days = :present_days,
                    absent_days = :absent_days,
                    basic_salary = :basic_salary,
                    allowances = :allowances,
                    bonus = :bonus,
                    deductions = :deductions,
                    advance_salary_deduction = :advance_salary_deduction,
                    net_salary = :net_salary,
                    note = :note
                WHERE id = :id";

        $params = [
            'working_days' => $data['working_days'],
            'present_days' => $data['present_days'],
            'absent_days' => $data['absent_days'],
            'basic_salary' => floatval($data['basic_salary']),
            'allowances' => floatval($data['allowances']),
            'bonus' => floatval($data['bonus'] ?? 0),
            'deductions' => floatval($data['deductions'] ?? 0),
            'advance_salary_deduction' => floatval($data['advance_salary_deduction'] ?? 0),
            'net_salary' => floatval($data['net_salary']),
            'note' => $data['note'] ?? null,
            'id' => $id
        ];

        return $this->db->query($sql, $params);
    }

    public function markPaid($id, $paymentMethod, $paymentDate = null, $note = null) {
        $sql = "UPDATE payrolls SET 
                    status = 'paid',
                    payment_method = ?,
                    payment_date = ?,
                    note = COALESCE(?, note)
                WHERE id = ?";
        return $this->db->query($sql, [$paymentMethod, $paymentDate ?: date('Y-m-d'), $note, $id]);
    }

    public function delete($id) {
        return $this->db->query("DELETE FROM payrolls WHERE id = ?", [$id]);
    }

    public function getMonthSummary($month) {
        $sql = "SELECT 
                    COUNT(*) as employee_count,
                    SUM(net_salary) as total_amount,
                    SUM(CASE WHEN status = 'paid' THEN net_salary ELSE 0 END) as paid_amount,
                    SUM(CASE WHEN status != 'paid' THEN net_salary ELSE 0 END) as pending_amount,
                    SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as paid_count,
                    SUM(CASE WHEN status != 'paid' THEN 1 ELSE 0 END) as pending_count
                FROM payrolls 
                WHERE salary_month = ?";
        $stmt = $this->db->query($sql, [$month]);
        return $stmt->fetch();
    }
}
