<?php

namespace Controllers;

use Core\Controller;
use Core\Middleware;
use Models\Payroll;
use Models\Setting;
use Models\Employee;

class PayrollController extends Controller {
    protected $payrollModel;
    protected $settingModel;
    protected $employeeModel;

    public function __construct() {
        Middleware::permission('payroll');
        $this->payrollModel = new Payroll();
        $this->settingModel = new Setting();
        $this->employeeModel = new Employee();
    }

    protected function redirect($path) {
        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
        header("Location: {$base}{$path}");
        exit;
    }

    public function index() {
        $month = $_GET['month'] ?? date('Y-m');
        $payrolls = $this->payrollModel->getByMonth($month);
        $summary = $this->payrollModel->getMonthSummary($month);

        return $this->view('admin/payroll/index', [
            'title' => 'মাসিক বেতন ও পেরোল শিট (Monthly Payroll)',
            'payrolls' => $payrolls,
            'summary' => $summary,
            'currentMonth' => $month,
            'success' => $_GET['success'] ?? null,
            'error' => $_GET['error'] ?? null
        ]);
    }

    public function generate() {
        $month = $_POST['month'] ?? date('Y-m');
        $workingDays = intval($_POST['working_days'] ?? 30);
        if ($workingDays <= 0) $workingDays = 30;

        try {
            $count = $this->payrollModel->generateForMonth($month, $workingDays);
            $msg = $count > 0 
                ? "{$month} মাসের জন্য {$count} জন কর্মচারীর বেতন শিট তৈরি হয়েছে"
                : "এই মাসের জন্য ইতিপূর্বে সকল কর্মচারীর বেতন তৈরি করা হয়েছে";
            $this->redirect('/admin/payroll?month=' . $month . '&success=' . urlencode($msg));
        } catch (\Exception $e) {
            $this->redirect('/admin/payroll?month=' . $month . '&error=' . urlencode($e->getMessage()));
        }
    }

    public function payslip() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect('/admin/payroll');
        }

        $payroll = $this->payrollModel->find($id);
        if (!$payroll) {
            $this->redirect('/admin/payroll?error=পেস্লিপ পাওয়া যায়নি');
        }

        $settings = $this->settingModel->getAll();

        return $this->view('admin/payroll/payslip', [
            'title' => 'বেতন রশিদ (Salary Payslip) - ' . $payroll['emp_code'] . ' - ' . $payroll['salary_month'],
            'payroll' => $payroll,
            'settings' => $settings
        ]);
    }

    public function markPaid() {
        $id = intval($_POST['id'] ?? 0);
        $month = $_POST['month'] ?? date('Y-m');
        $paymentMethod = $_POST['payment_method'] ?? 'cash';
        $paymentDate = $_POST['payment_date'] ?? date('Y-m-d');
        $note = trim($_POST['note'] ?? '');

        if ($id) {
            $this->payrollModel->markPaid($id, $paymentMethod, $paymentDate, $note);
            $this->redirect('/admin/payroll?month=' . $month . '&success=বেতন পরিশোধ হিসেবে চিহ্নিত করা হয়েছে');
        }

        $this->redirect('/admin/payroll?month=' . $month);
    }

    public function destroy() {
        $id = intval($_POST['id'] ?? 0);
        $month = $_POST['month'] ?? date('Y-m');

        if ($id) {
            $this->payrollModel->delete($id);
            $this->redirect('/admin/payroll?month=' . $month . '&success=বেতন রেকর্ড মুছে ফেলা হয়েছে');
        }

        $this->redirect('/admin/payroll?month=' . $month);
    }
}
