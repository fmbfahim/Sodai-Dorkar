<?php

namespace Controllers;

use Core\View;
use Core\Database;
use Core\Middleware;
use Models\Coupon;

class CouponController
{
    protected Database $db;
    protected Coupon $model;

    public function __construct()
    {
        $config      = require __DIR__ . '/../../config/database.php';
        $this->db    = new Database($config);
        $this->model = new Coupon($this->db);
    }

    private function base(): string
    {
        return (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false)
            ? '/sodai-dorkar/public'
            : '';
    }

    /**
     * Admin: List all marketing coupons
     */
    public function index()
    {
        Middleware::auth(['admin', 'manager']);

        $status = $_GET['status'] ?? null;
        $search = trim($_GET['search'] ?? '');

        $coupons = $this->model->getAll($status, $search);
        $stats = $this->model->getStats();

        return View::render('admin.marketing.coupons.index', [
            'coupons'       => $coupons,
            'stats'         => $stats,
            'currentStatus' => $status,
            'search'        => $search,
            'success'       => $_SESSION['flash_success'] ?? null,
            'error'         => $_SESSION['flash_error'] ?? null,
        ]);
    }

    /**
     * Admin: Show create coupon form
     */
    public function create()
    {
        Middleware::auth(['admin', 'manager']);

        return View::render('admin.marketing.coupons.create', [
            'error' => $_SESSION['flash_error'] ?? null,
            'old'   => $_SESSION['old_input'] ?? [],
        ]);
    }

    /**
     * Admin: Store new coupon
     */
    public function store()
    {
        Middleware::auth(['admin', 'manager']);
        $base = $this->base();

        $code = strtoupper(trim($_POST['code'] ?? ''));
        $title = trim($_POST['title'] ?? '');
        $discountType = $_POST['discount_type'] ?? 'fixed';
        $discountValue = floatval($_POST['discount_value'] ?? 0);
        $minOrderAmount = floatval($_POST['min_order_amount'] ?? 0);
        $maxDiscountAmount = !empty($_POST['max_discount_amount']) ? floatval($_POST['max_discount_amount']) : null;
        $usageLimit = !empty($_POST['usage_limit']) ? intval($_POST['usage_limit']) : null;
        $usageLimitPerUser = !empty($_POST['usage_limit_per_user']) ? intval($_POST['usage_limit_per_user']) : 1;
        $startDate = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
        $endDate = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
        $status = $_POST['status'] ?? 'active';
        $description = trim($_POST['description'] ?? '');

        if (empty($code) || empty($title) || $discountValue <= 0) {
            $_SESSION['flash_error'] = 'কুপন কোড, শিরোনাম এবং ছাড়ের সঠিক পরিমাণ উল্লেখ করা আবশ্যক।';
            $_SESSION['old_input'] = $_POST;
            header("Location: {$base}/admin/marketing/coupons/create");
            exit;
        }

        // Check duplicate code
        if ($this->model->findByCode($code)) {
            $_SESSION['flash_error'] = "এই কুপন কোড ('{$code}') ইতোমধ্যে বিদ্যমান। অনুগ্রহ করে অন্য কোড দিন।";
            $_SESSION['old_input'] = $_POST;
            header("Location: {$base}/admin/marketing/coupons/create");
            exit;
        }

        $this->model->create([
            'code'                 => $code,
            'title'                => $title,
            'discount_type'        => $discountType,
            'discount_value'       => $discountValue,
            'min_order_amount'     => $minOrderAmount,
            'max_discount_amount'  => $maxDiscountAmount,
            'usage_limit'          => $usageLimit,
            'usage_limit_per_user' => $usageLimitPerUser,
            'start_date'           => $startDate,
            'end_date'             => $endDate,
            'status'               => $status,
            'description'          => $description,
        ]);

        unset($_SESSION['old_input']);
        $_SESSION['flash_success'] = "কুপন '{$code}' সফলভাবে তৈরি হয়েছে!";
        header("Location: {$base}/admin/marketing/coupons");
        exit;
    }

    /**
     * Admin: Show edit form
     */
    public function edit()
    {
        Middleware::auth(['admin', 'manager']);
        $base = $this->base();
        $id = (int)($_GET['id'] ?? 0);

        $coupon = $this->model->getById($id);
        if (!$coupon) {
            $_SESSION['flash_error'] = 'কুপন পাওয়া যায়নি।';
            header("Location: {$base}/admin/marketing/coupons");
            exit;
        }

        return View::render('admin.marketing.coupons.edit', [
            'coupon' => $coupon,
            'error'  => $_SESSION['flash_error'] ?? null,
        ]);
    }

    /**
     * Admin: Update coupon
     */
    public function update()
    {
        Middleware::auth(['admin', 'manager']);
        $base = $this->base();
        $id = (int)($_POST['id'] ?? 0);

        $coupon = $this->model->getById($id);
        if (!$coupon) {
            $_SESSION['flash_error'] = 'কুপন পাওয়া যায়নি।';
            header("Location: {$base}/admin/marketing/coupons");
            exit;
        }

        $code = strtoupper(trim($_POST['code'] ?? ''));
        $title = trim($_POST['title'] ?? '');
        $discountType = $_POST['discount_type'] ?? 'fixed';
        $discountValue = floatval($_POST['discount_value'] ?? 0);
        $minOrderAmount = floatval($_POST['min_order_amount'] ?? 0);
        $maxDiscountAmount = !empty($_POST['max_discount_amount']) ? floatval($_POST['max_discount_amount']) : null;
        $usageLimit = !empty($_POST['usage_limit']) ? intval($_POST['usage_limit']) : null;
        $usageLimitPerUser = !empty($_POST['usage_limit_per_user']) ? intval($_POST['usage_limit_per_user']) : 1;
        $startDate = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
        $endDate = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
        $status = $_POST['status'] ?? 'active';
        $description = trim($_POST['description'] ?? '');

        if (empty($code) || empty($title) || $discountValue <= 0) {
            $_SESSION['flash_error'] = 'কুপন কোড, শিরোনাম এবং ছাড়ের সঠিক পরিমাণ আবশ্যক।';
            header("Location: {$base}/admin/marketing/coupons/edit?id={$id}");
            exit;
        }

        // Check if code used by another coupon
        $existing = $this->model->findByCode($code);
        if ($existing && (int)$existing['id'] !== $id) {
            $_SESSION['flash_error'] = "এই কুপন কোড ('{$code}') অন্য কুপনে ব্যবহৃত হচ্ছে।";
            header("Location: {$base}/admin/marketing/coupons/edit?id={$id}");
            exit;
        }

        $this->model->update($id, [
            'code'                 => $code,
            'title'                => $title,
            'discount_type'        => $discountType,
            'discount_value'       => $discountValue,
            'min_order_amount'     => $minOrderAmount,
            'max_discount_amount'  => $maxDiscountAmount,
            'usage_limit'          => $usageLimit,
            'usage_limit_per_user' => $usageLimitPerUser,
            'start_date'           => $startDate,
            'end_date'             => $endDate,
            'status'               => $status,
            'description'          => $description,
        ]);

        $_SESSION['flash_success'] = "কুপন '{$code}' সফলভাবে আপডেট হয়েছে!";
        header("Location: {$base}/admin/marketing/coupons");
        exit;
    }

    /**
     * Admin: Delete coupon
     */
    public function destroy()
    {
        Middleware::auth(['admin', 'manager']);
        $base = $this->base();
        $id = (int)($_POST['id'] ?? 0);

        $this->model->delete($id);
        $_SESSION['flash_success'] = 'কুপন মুছে ফেলা হয়েছে।';
        header("Location: {$base}/admin/marketing/coupons");
        exit;
    }

    /**
     * Admin: Toggle Status (active/inactive)
     */
    public function toggleStatus()
    {
        Middleware::auth(['admin', 'manager']);
        $id = (int)($_POST['id'] ?? 0);
        $newStatus = $this->model->toggleStatus($id);

        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
               || (isset($_POST['is_ajax']) && $_POST['is_ajax'] == '1');

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'status' => $newStatus]);
            exit;
        }

        $_SESSION['flash_success'] = "কুপন স্ট্যাটাস পরিবর্তিত হয়েছে: {$newStatus}";
        header("Location: " . $this->base() . "/admin/marketing/coupons");
        exit;
    }

    /**
     * Admin: Show Coupon Usages / Audit Log
     */
    public function usages()
    {
        Middleware::auth(['admin', 'manager']);

        $usages = $this->model->getUsages(100);
        $stats = $this->model->getStats();

        return View::render('admin.marketing.coupons.usages', [
            'usages' => $usages,
            'stats'  => $stats,
        ]);
    }
}
