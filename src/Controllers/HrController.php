<?php

namespace Controllers;

use Core\Controller;
use Core\Middleware;
use Models\Employee;
use Models\Department;
use Models\Attendance;
use Models\Leave;
use Models\User;

class HrController extends Controller {
    protected $employeeModel;
    protected $departmentModel;
    protected $attendanceModel;
    protected $leaveModel;
    protected $userModel;

    public function __construct() {
        Middleware::auth(['admin', 'manager']);
        $this->employeeModel = new Employee();
        $this->departmentModel = new Department();
        $this->attendanceModel = new Attendance();
        $this->leaveModel = new Leave();
        $this->userModel = new User();
    }

    protected function redirect($path) {
        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
        header("Location: {$base}{$path}");
        exit;
    }

    /* -------------------------------------------------------------
     * 1. EMPLOYEE DIRECTORY & CRUD
     * -----------------------------------------------------------*/

    public function employees() {
        $departmentId = $_GET['department_id'] ?? '';
        $status = $_GET['status'] ?? '';
        $search = $_GET['search'] ?? '';

        $filters = [];
        if ($departmentId) $filters['department_id'] = $departmentId;
        if ($status) $filters['status'] = $status;
        if ($search) $filters['search'] = $search;

        $employees = $this->employeeModel->all($filters);
        $departments = $this->departmentModel->all();
        $stats = $this->employeeModel->getStatistics();

        return $this->view('admin/hr/employees/index', [
            'title' => 'কর্মচারী তালিকা ও ব্যবস্থাপনা (Employee Directory)',
            'employees' => $employees,
            'departments' => $departments,
            'stats' => $stats,
            'filters' => [
                'department_id' => $departmentId,
                'status' => $status,
                'search' => $search
            ],
            'success' => $_GET['success'] ?? null,
            'error' => $_GET['error'] ?? null
        ]);
    }

    public function createEmployee() {
        $departments = $this->departmentModel->all();
        $designations = $this->departmentModel->getDesignations();
        $users = $this->userModel->all(['status' => 'active']);
        $nextEmpCode = $this->employeeModel->generateEmpCode();

        return $this->view('admin/hr/employees/create', [
            'title' => 'নতুন কর্মচারী যোগ করুন (Add New Employee)',
            'departments' => $departments,
            'designations' => $designations,
            'users' => $users,
            'nextEmpCode' => $nextEmpCode,
            'error' => $_GET['error'] ?? null
        ]);
    }

    public function storeEmployee() {
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if (empty($name) || empty($phone)) {
            $this->redirect('/admin/hr/employees/create?error=নাম এবং মোবাইল নম্বর আবশ্যক');
        }

        $photoPath = null;
        if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['photo']['tmp_name']);
            finfo_close($finfo);

            if (in_array($mime, $allowedMimeTypes)) {
                $uploadDir = __DIR__ . '/../../public/uploads/employees/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                $fileName = 'emp_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
                    $photoPath = '/uploads/employees/' . $fileName;
                }
            }
        }

        $data = [
            'emp_code' => trim($_POST['emp_code'] ?? ''),
            'user_id' => !empty($_POST['user_id']) ? intval($_POST['user_id']) : null,
            'name' => $name,
            'phone' => $phone,
            'email' => trim($_POST['email'] ?? '') ?: null,
            'nid' => trim($_POST['nid'] ?? '') ?: null,
            'gender' => $_POST['gender'] ?? 'male',
            'joining_date' => !empty($_POST['joining_date']) ? $_POST['joining_date'] : date('Y-m-d'),
            'department_id' => !empty($_POST['department_id']) ? intval($_POST['department_id']) : null,
            'designation_id' => !empty($_POST['designation_id']) ? intval($_POST['designation_id']) : null,
            'employment_type' => $_POST['employment_type'] ?? 'full_time',
            'basic_salary' => floatval($_POST['basic_salary'] ?? 0),
            'house_rent' => floatval($_POST['house_rent'] ?? 0),
            'medical_allowance' => floatval($_POST['medical_allowance'] ?? 0),
            'other_allowance' => floatval($_POST['other_allowance'] ?? 0),
            'bank_name' => trim($_POST['bank_name'] ?? '') ?: null,
            'bank_account_no' => trim($_POST['bank_account_no'] ?? '') ?: null,
            'mobile_banking_type' => trim($_POST['mobile_banking_type'] ?? '') ?: null,
            'mobile_banking_number' => trim($_POST['mobile_banking_number'] ?? '') ?: null,
            'emergency_contact' => trim($_POST['emergency_contact'] ?? '') ?: null,
            'address' => trim($_POST['address'] ?? '') ?: null,
            'photo_path' => $photoPath,
            'status' => $_POST['status'] ?? 'active'
        ];

        try {
            $id = $this->employeeModel->create($data);
            $this->redirect('/admin/hr/employees/show?id=' . $id . '&success=কর্মচারীর তথ্য সফলভাবে সংরক্ষণ করা হয়েছে');
        } catch (\Exception $e) {
            $this->redirect('/admin/hr/employees/create?error=' . urlencode($e->getMessage()));
        }
    }

    public function showEmployee() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect('/admin/hr/employees');
        }

        $employee = $this->employeeModel->find($id);
        if (!$employee) {
            $this->redirect('/admin/hr/employees?error=কর্মচারী পাওয়া যায়নি');
        }

        $currentMonth = date('Y-m');
        $attendanceStats = $this->attendanceModel->getMonthlyStats($currentMonth, $id);
        $recentAttendance = $this->employeeModel->getAttendanceHistory($id, 15);
        $recentPayrolls = $this->employeeModel->getRecentPayrolls($id, 6);
        $leaveHistory = $this->employeeModel->getLeaveHistory($id, 5);

        return $this->view('admin/hr/employees/show', [
            'title' => $employee['name'] . ' (' . $employee['emp_code'] . ') - Profile',
            'employee' => $employee,
            'attendanceStats' => $attendanceStats,
            'recentAttendance' => $recentAttendance,
            'recentPayrolls' => $recentPayrolls,
            'leaveHistory' => $leaveHistory,
            'currentMonth' => $currentMonth,
            'success' => $_GET['success'] ?? null,
            'error' => $_GET['error'] ?? null
        ]);
    }

    public function editEmployee() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect('/admin/hr/employees');
        }

        $employee = $this->employeeModel->find($id);
        if (!$employee) {
            $this->redirect('/admin/hr/employees?error=কর্মচারী পাওয়া যায়নি');
        }

        $departments = $this->departmentModel->all();
        $designations = $this->departmentModel->getDesignations();
        $users = $this->userModel->all(['status' => 'active']);

        return $this->view('admin/hr/employees/edit', [
            'title' => 'কর্মচারী তথ্য পরিবর্তন - ' . $employee['name'],
            'employee' => $employee,
            'departments' => $departments,
            'designations' => $designations,
            'users' => $users,
            'error' => $_GET['error'] ?? null
        ]);
    }

    public function updateEmployee() {
        $id = $_POST['id'] ?? null;
        if (!$id) {
            $this->redirect('/admin/hr/employees');
        }

        $existing = $this->employeeModel->find($id);
        if (!$existing) {
            $this->redirect('/admin/hr/employees?error=কর্মচারী পাওয়া যায়নি');
        }

        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if (empty($name) || empty($phone)) {
            $this->redirect('/admin/hr/employees/edit?id=' . $id . '&error=নাম এবং মোবাইল নম্বর আবশ্যক');
        }

        $photoPath = $existing['photo_path'];
        if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['photo']['tmp_name']);
            finfo_close($finfo);

            if (in_array($mime, $allowedMimeTypes)) {
                $uploadDir = __DIR__ . '/../../public/uploads/employees/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                $fileName = 'emp_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
                    $photoPath = '/uploads/employees/' . $fileName;
                }
            }
        }

        $data = [
            'user_id' => !empty($_POST['user_id']) ? intval($_POST['user_id']) : null,
            'name' => $name,
            'phone' => $phone,
            'email' => trim($_POST['email'] ?? '') ?: null,
            'nid' => trim($_POST['nid'] ?? '') ?: null,
            'gender' => $_POST['gender'] ?? 'male',
            'joining_date' => !empty($_POST['joining_date']) ? $_POST['joining_date'] : $existing['joining_date'],
            'department_id' => !empty($_POST['department_id']) ? intval($_POST['department_id']) : null,
            'designation_id' => !empty($_POST['designation_id']) ? intval($_POST['designation_id']) : null,
            'employment_type' => $_POST['employment_type'] ?? 'full_time',
            'basic_salary' => floatval($_POST['basic_salary'] ?? 0),
            'house_rent' => floatval($_POST['house_rent'] ?? 0),
            'medical_allowance' => floatval($_POST['medical_allowance'] ?? 0),
            'other_allowance' => floatval($_POST['other_allowance'] ?? 0),
            'bank_name' => trim($_POST['bank_name'] ?? '') ?: null,
            'bank_account_no' => trim($_POST['bank_account_no'] ?? '') ?: null,
            'mobile_banking_type' => trim($_POST['mobile_banking_type'] ?? '') ?: null,
            'mobile_banking_number' => trim($_POST['mobile_banking_number'] ?? '') ?: null,
            'emergency_contact' => trim($_POST['emergency_contact'] ?? '') ?: null,
            'address' => trim($_POST['address'] ?? '') ?: null,
            'photo_path' => $photoPath,
            'status' => $_POST['status'] ?? 'active'
        ];

        try {
            $this->employeeModel->update($id, $data);
            $this->redirect('/admin/hr/employees/show?id=' . $id . '&success=কর্মচারীর তথ্য সফলভাবে আপডেট হয়েছে');
        } catch (\Exception $e) {
            $this->redirect('/admin/hr/employees/edit?id=' . $id . '&error=' . urlencode($e->getMessage()));
        }
    }

    public function destroyEmployee() {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $this->employeeModel->delete($id);
            $this->redirect('/admin/hr/employees?success=কর্মচারী সফলভাবে মুছে ফেলা হয়েছে');
        }
        $this->redirect('/admin/hr/employees');
    }

    /* -------------------------------------------------------------
     * 2. DEPARTMENTS & DESIGNATIONS
     * -----------------------------------------------------------*/

    public function departments() {
        $departments = $this->departmentModel->all();
        $designations = $this->departmentModel->getDesignations();

        return $this->view('admin/hr/departments/index', [
            'title' => 'ডিপার্টমেন্ট ও পদবী ব্যবস্থাপনা (Departments & Designations)',
            'departments' => $departments,
            'designations' => $designations,
            'success' => $_GET['success'] ?? null,
            'error' => $_GET['error'] ?? null
        ]);
    }

    public function storeDepartment() {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (!empty($name)) {
            $this->departmentModel->create([
                'name' => $name,
                'description' => $description
            ]);
            $this->redirect('/admin/hr/departments?success=ডিপার্টমেন্ট তৈরি সম্পন্ন হয়েছে');
        }

        $this->redirect('/admin/hr/departments?error=নাম আবশ্যক');
    }

    public function destroyDepartment() {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $this->departmentModel->delete($id);
            $this->redirect('/admin/hr/departments?success=ডিপার্টমেন্ট মুছে ফেলা হয়েছে');
        }
        $this->redirect('/admin/hr/departments');
    }

    public function storeDesignation() {
        $departmentId = intval($_POST['department_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');

        if ($departmentId > 0 && !empty($title)) {
            $this->departmentModel->createDesignation($departmentId, $title);
            $this->redirect('/admin/hr/departments?success=নতুন পদবী যোগ করা হয়েছে');
        }

        $this->redirect('/admin/hr/departments?error=ডিপার্টমেন্ট ও পদবীর নাম নির্বাচন করুন');
    }

    public function destroyDesignation() {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $this->departmentModel->deleteDesignation($id);
            $this->redirect('/admin/hr/departments?success=পদবী মুছে ফেলা হয়েছে');
        }
        $this->redirect('/admin/hr/departments');
    }

    /* -------------------------------------------------------------
     * 3. ATTENDANCE MANAGEMENT
     * -----------------------------------------------------------*/

    public function attendance() {
        $date = $_GET['date'] ?? date('Y-m-d');
        $attendanceList = $this->attendanceModel->getByDate($date);

        return $this->view('admin/hr/attendance/index', [
            'title' => 'দৈনিক উপস্থিতি খাতা (Daily Attendance)',
            'attendanceList' => $attendanceList,
            'currentDate' => $date,
            'success' => $_GET['success'] ?? null,
            'error' => $_GET['error'] ?? null
        ]);
    }

    public function storeAttendance() {
        $date = $_POST['date'] ?? date('Y-m-d');
        $attendanceData = $_POST['attendance'] ?? [];

        if (!empty($attendanceData)) {
            try {
                $this->attendanceModel->saveDailyAttendance($date, $attendanceData);
                $this->redirect('/admin/hr/attendance?date=' . $date . '&success=উপস্থিতি সফলভাবে সংরক্ষণ করা হয়েছে');
            } catch (\Exception $e) {
                $this->redirect('/admin/hr/attendance?date=' . $date . '&error=' . urlencode($e->getMessage()));
            }
        }

        $this->redirect('/admin/hr/attendance?date=' . $date);
    }

    public function attendanceReport() {
        $month = $_GET['month'] ?? date('Y-m');
        $reportData = $this->attendanceModel->getMonthlyReport($month);

        return $this->view('admin/hr/attendance/report', [
            'title' => 'মাসিক হাজিরা রিপোর্ট (Monthly Attendance Report)',
            'reportData' => $reportData,
            'currentMonth' => $month
        ]);
    }

    /* -------------------------------------------------------------
     * 4. LEAVE REQUESTS
     * -----------------------------------------------------------*/

    public function leaves() {
        $status = $_GET['status'] ?? null;
        $leaves = $this->leaveModel->all($status);
        $employees = $this->employeeModel->all(['status' => 'active']);

        return $this->view('admin/hr/leaves/index', [
            'title' => 'ছুটি ব্যবস্থাপনা ও আবেদন (Leave Management)',
            'leaves' => $leaves,
            'employees' => $employees,
            'currentStatus' => $status,
            'success' => $_GET['success'] ?? null,
            'error' => $_GET['error'] ?? null
        ]);
    }

    public function storeLeave() {
        $employeeId = intval($_POST['employee_id'] ?? 0);
        $startDate = $_POST['start_date'] ?? '';
        $endDate = $_POST['end_date'] ?? '';

        if ($employeeId && $startDate && $endDate) {
            $this->leaveModel->create([
                'employee_id' => $employeeId,
                'leave_type' => $_POST['leave_type'] ?? 'casual',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'reason' => trim($_POST['reason'] ?? '')
            ]);

            $this->redirect('/admin/hr/leaves?success=ছুটির আবেদন জমা নেওয়া হয়েছে');
        }

        $this->redirect('/admin/hr/leaves?error=কর্মচারী এবং ছুটির তারিখ নির্বাচন করুন');
    }

    public function updateLeaveStatus() {
        $id = intval($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? 'pending';
        $userId = $_SESSION['user_id'] ?? null;

        if ($id && in_array($status, ['approved', 'rejected', 'pending'])) {
            $this->leaveModel->updateStatus($id, $status, $userId);
            $this->redirect('/admin/hr/leaves?success=আবেদনের স্ট্যাটাস হালনাগাদ করা হয়েছে');
        }

        $this->redirect('/admin/hr/leaves');
    }

    public function destroyLeave() {
        $id = intval($_POST['id'] ?? 0);
        if ($id) {
            $this->leaveModel->delete($id);
            $this->redirect('/admin/hr/leaves?success=আবেদনটি মুছে ফেলা হয়েছে');
        }
        $this->redirect('/admin/hr/leaves');
    }
}
