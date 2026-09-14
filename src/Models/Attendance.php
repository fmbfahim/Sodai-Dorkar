<?php

namespace Models;

use Core\Database;

class Attendance {
    protected $db;

    public function __construct() {
        $config = require __DIR__ . '/../../config/database.php';
        $this->db = new Database($config);
    }

    public function getByDate($date) {
        $sql = "SELECT e.id as employee_id, e.emp_code, e.name, e.phone,
                       d.name as department_name, des.title as designation_title,
                       a.id as attendance_id, a.in_time, a.out_time, 
                       COALESCE(a.status, 'present') as status, a.note
                FROM employees e
                LEFT JOIN departments d ON e.department_id = d.id
                LEFT JOIN designations des ON e.designation_id = des.id
                LEFT JOIN attendances a ON e.id = a.employee_id AND a.date = ?
                WHERE e.status != 'terminated'
                ORDER BY e.name ASC";
        return $this->db->query($sql, [$date])->fetchAll();
    }

    public function saveDailyAttendance($date, $records) {
        $pdo = $this->db->getConnection();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare("
                INSERT INTO attendances (employee_id, date, in_time, out_time, status, note)
                VALUES (:emp_id, :date, :in_time, :out_time, :status, :note)
                ON DUPLICATE KEY UPDATE 
                    in_time = VALUES(in_time),
                    out_time = VALUES(out_time),
                    status = VALUES(status),
                    note = VALUES(note)
            ");

            foreach ($records as $empId => $data) {
                $status = $data['status'] ?? 'present';
                $inTime = !empty($data['in_time']) ? $data['in_time'] : null;
                $outTime = !empty($data['out_time']) ? $data['out_time'] : null;
                $note = !empty($data['note']) ? $data['note'] : null;

                $stmt->execute([
                    'emp_id' => $empId,
                    'date' => $date,
                    'in_time' => $inTime,
                    'out_time' => $outTime,
                    'status' => $status,
                    'note' => $note
                ]);
            }

            $pdo->commit();
            return true;
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function getMonthlyStats($month, $employeeId) {
        $sql = "SELECT 
                    COUNT(*) as total_logged,
                    SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count,
                    SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late_count,
                    SUM(CASE WHEN status = 'half_day' THEN 1 ELSE 0 END) as half_day_count,
                    SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_count,
                    SUM(CASE WHEN status = 'leave' THEN 1 ELSE 0 END) as leave_count,
                    SUM(CASE WHEN status = 'holiday' THEN 1 ELSE 0 END) as holiday_count
                FROM attendances
                WHERE employee_id = ? AND date LIKE ?";
        $stmt = $this->db->query($sql, [$employeeId, $month . '%']);
        return $stmt->fetch();
    }

    public function getMonthlyReport($month) {
        $sql = "SELECT e.id as employee_id, e.emp_code, e.name, e.phone,
                       d.name as department_name, des.title as designation_title,
                       COUNT(a.id) as total_logged,
                       SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present_count,
                       SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) as late_count,
                       SUM(CASE WHEN a.status = 'half_day' THEN 1 ELSE 0 END) as half_day_count,
                       SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as absent_count,
                       SUM(CASE WHEN a.status = 'leave' THEN 1 ELSE 0 END) as leave_count,
                       SUM(CASE WHEN a.status = 'holiday' THEN 1 ELSE 0 END) as holiday_count
                FROM employees e
                LEFT JOIN departments d ON e.department_id = d.id
                LEFT JOIN designations des ON e.designation_id = des.id
                LEFT JOIN attendances a ON e.id = a.employee_id AND a.date LIKE ?
                WHERE e.status != 'terminated'
                GROUP BY e.id
                ORDER BY e.name ASC";
        return $this->db->query($sql, [$month . '%'])->fetchAll();
    }
}
