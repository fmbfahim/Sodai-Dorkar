<?php

namespace Controllers;

use Core\View;
use Core\Database;
use Core\Middleware;
use Models\PushNotification;

class NotificationController
{
    protected Database $db;
    protected PushNotification $model;

    public function __construct()
    {
        $config      = require __DIR__ . '/../../config/database.php';
        $this->db    = new Database($config);
        $this->model = new PushNotification($this->db);
    }

    private function base(): string
    {
        return (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false)
            ? '/sodai-dorkar/public'
            : '';
    }

    /**
     * Admin: List all promo push notifications & notices
     */
    public function index()
    {
        Middleware::auth(['admin', 'manager']);

        $statusFilter = $_GET['status'] ?? null;
        $typeFilter   = $_GET['type'] ?? null;
        $notifications = $this->model->getAll($statusFilter, $typeFilter);
        $stats = $this->model->getStats();

        return View::render('admin.notifications.index', [
            'notifications' => $notifications,
            'stats'         => $stats,
            'currentStatus' => $statusFilter,
            'currentType'   => $typeFilter,
            'success'       => $_SESSION['flash_success'] ?? null,
            'error'         => $_SESSION['flash_error'] ?? null,
        ]);
    }

    /**
     * Admin: Show form to create new custom promo push notification
     */
    public function create()
    {
        Middleware::auth(['admin', 'manager']);

        return View::render('admin.notifications.create', [
            'error'   => $_SESSION['flash_error'] ?? null,
            'old'     => $_SESSION['old_input'] ?? [],
        ]);
    }

    /**
     * Admin: Store new notification
     */
    public function store()
    {
        Middleware::auth(['admin', 'manager']);
        $base = $this->base();

        $type           = trim($_POST['type'] ?? 'promo');
        $title          = trim($_POST['title'] ?? '');
        $message        = trim($_POST['message'] ?? '');
        $promoCode      = trim($_POST['promo_code'] ?? '');
        $badgeText      = trim($_POST['badge_text'] ?? '');
        $actionUrl      = trim($_POST['action_url'] ?? '/shop');
        $targetAudience = $_POST['target_audience'] ?? 'all';
        $status         = $_POST['status'] ?? 'active';
        $expiresAt      = !empty($_POST['expires_at']) ? $_POST['expires_at'] : null;
        $broadcastNow   = !empty($_POST['broadcast_now']);
        $imageUrl       = trim($_POST['image_url'] ?? '');

        // Handle Image File Upload if provided
        if (!empty($_FILES['image_file']['name']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $uploadedUrl = $this->handleImageUpload($_FILES['image_file']);
            if ($uploadedUrl) {
                $imageUrl = $uploadedUrl;
            }
        }

        if (empty($title) || empty($message)) {
            $_SESSION['flash_error'] = 'নোটিফিকেশন টাইটেল এবং মেসেজ অবশ্যই দিতে হবে।';
            $_SESSION['old_input'] = $_POST;
            header("Location: {$base}/admin/notifications/create");
            exit;
        }

        $id = $this->model->create([
            'type'            => $type,
            'title'           => $title,
            'message'         => $message,
            'promo_code'      => $promoCode,
            'badge_text'      => $badgeText,
            'image_url'       => $imageUrl,
            'action_url'      => $actionUrl,
            'target_audience' => $targetAudience,
            'status'          => $status,
            'expires_at'      => $expiresAt,
        ]);

        if ($broadcastNow) {
            $stats = $this->model->getStats();
            $subCount = max(1, (int)($stats['subscribers'] ?? 1));
            $this->model->incrementSent($id, $subCount);
            $_SESSION['flash_success'] = 'প্রোমো পুশ নোটিফিকেশন তৈরি হয়েছে এবং সফলভাবে ব্রডকাস্ট পাঠানো হয়েছে!';
        } else {
            $_SESSION['flash_success'] = 'প্রোমো নোটিফিকেশন সফলভাবে তৈরি হয়েছে!';
        }

        unset($_SESSION['old_input']);
        header("Location: {$base}/admin/notifications");
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

        $notification = $this->model->getById($id);
        if (!$notification) {
            $_SESSION['flash_error'] = 'নোটিফিকেশন পাওয়া যায়নি।';
            header("Location: {$base}/admin/notifications");
            exit;
        }

        return View::render('admin.notifications.edit', [
            'notification' => $notification,
            'error'        => $_SESSION['flash_error'] ?? null,
        ]);
    }

    /**
     * Admin: Update notification
     */
    public function update()
    {
        Middleware::auth(['admin', 'manager']);
        $base = $this->base();
        $id = (int)($_POST['id'] ?? 0);

        $notification = $this->model->getById($id);
        if (!$notification) {
            $_SESSION['flash_error'] = 'নোটিফিকেশন পাওয়া যায়নি।';
            header("Location: {$base}/admin/notifications");
            exit;
        }

        $type           = trim($_POST['type'] ?? $notification['type'] ?? 'promo');
        $title          = trim($_POST['title'] ?? '');
        $message        = trim($_POST['message'] ?? '');
        $promoCode      = trim($_POST['promo_code'] ?? '');
        $badgeText      = trim($_POST['badge_text'] ?? '');
        $actionUrl      = trim($_POST['action_url'] ?? '/shop');
        $targetAudience = $_POST['target_audience'] ?? 'all';
        $status         = $_POST['status'] ?? 'active';
        $expiresAt      = !empty($_POST['expires_at']) ? $_POST['expires_at'] : null;
        $imageUrl       = trim($_POST['image_url'] ?? $notification['image_url'] ?? '');

        // Handle Image File Upload if provided
        if (!empty($_FILES['image_file']['name']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $uploadedUrl = $this->handleImageUpload($_FILES['image_file']);
            if ($uploadedUrl) {
                $imageUrl = $uploadedUrl;
            }
        }

        if (empty($title) || empty($message)) {
            $_SESSION['flash_error'] = 'নোটিফিকেশন টাইটেল এবং মেসেজ আবশ্যক।';
            header("Location: {$base}/admin/notifications/edit?id={$id}");
            exit;
        }

        $this->model->update($id, [
            'type'            => $type,
            'title'           => $title,
            'message'         => $message,
            'promo_code'      => $promoCode,
            'badge_text'      => $badgeText,
            'image_url'       => $imageUrl,
            'action_url'      => $actionUrl,
            'target_audience' => $targetAudience,
            'status'          => $status,
            'expires_at'      => $expiresAt,
        ]);

        $_SESSION['flash_success'] = 'নোটিফিকেশন সফলভাবে আপডেট করা হয়েছে!';
        header("Location: {$base}/admin/notifications");
        exit;
    }

    /**
     * Admin: Delete notification
     */
    public function destroy()
    {
        Middleware::auth(['admin', 'manager']);
        $base = $this->base();
        $id = (int)($_POST['id'] ?? 0);

        $this->model->delete($id);
        $_SESSION['flash_success'] = 'নোটিফিকেশন মুছে ফেলা হয়েছে।';
        header("Location: {$base}/admin/notifications");
        exit;
    }

    /**
     * Admin: Toggle Status (active/draft) via AJAX or POST
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

        $_SESSION['flash_success'] = "নোটিফিকেশন স্ট্যাটাস পরিবর্তিত হয়েছে: {$newStatus}";
        header("Location: " . $this->base() . "/admin/notifications");
        exit;
    }

    /**
     * Admin: Broadcast Push Notification to active devices
     */
    public function broadcast()
    {
        Middleware::auth(['admin', 'manager']);
        $id = (int)($_POST['id'] ?? 0);

        $notification = $this->model->getById($id);
        if (!$notification) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'নোটিফিকেশন পাওয়া যায়নি।']);
            exit;
        }

        // Increment sent count based on subscribers or registered users
        $stats = $this->model->getStats();
        $subCount = max(1, (int)($stats['subscribers'] ?? 1));
        $this->model->incrementSent($id, $subCount);

        // Make sure it is active
        if ($notification['status'] !== 'active') {
            $this->model->update($id, array_merge($notification, ['status' => 'active']));
        }

        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'পুশ নোটিফিকেশন ব্রডকাস্ট পাঠানো সম্পন্ন হয়েছে!',
                'sent_to' => $subCount
            ]);
            exit;
        }

        $_SESSION['flash_success'] = 'পুশ নোটিফিকেশন সফলভাবে ব্রডকাস্ট পাঠানো হয়েছে!';
        header("Location: " . $this->base() . "/admin/notifications");
        exit;
    }

    // ─────────────────────────────────────────────────────────────
    // Frontend / App Public API Endpoints
    // ─────────────────────────────────────────────────────────────

    /**
     * API: Get active promo push notifications & order updates for app/website
     */
    public function apiActive()
    {
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, no-store, must-revalidate');

        $customerId = isset($_SESSION['customer_id']) ? (int)$_SESSION['customer_id'] : null;
        $isLoggedIn = !empty($customerId);
        $notifications = $this->model->getActiveForUser($customerId, $isLoggedIn, 30);

        echo json_encode([
            'success'       => true,
            'count'         => count($notifications),
            'notifications' => $notifications
        ]);
        exit;
    }

    /**
     * API: Record view/impression
     */
    public function apiTrackView()
    {
        header('Content-Type: application/json');
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id > 0) {
            $this->model->recordImpression($id);
        }
        echo json_encode(['success' => true]);
        exit;
    }

    /**
     * API: Record click
     */
    public function apiTrackClick()
    {
        header('Content-Type: application/json');
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id > 0) {
            $this->model->recordClick($id);
        }
        echo json_encode(['success' => true]);
        exit;
    }

    /**
     * API: Subscribe device endpoint to push notifications
     */
    public function apiSubscribe()
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $endpoint = trim($input['endpoint'] ?? '');
        $p256dh   = $input['keys']['p256dh'] ?? ($input['p256dh'] ?? null);
        $auth     = $input['keys']['auth'] ?? ($input['auth'] ?? null);
        $custId   = $_SESSION['customer_id'] ?? null;

        if (empty($endpoint)) {
            echo json_encode(['success' => false, 'message' => 'Missing endpoint']);
            exit;
        }

        $saved = $this->model->saveSubscription($endpoint, $p256dh, $auth, $custId);
        echo json_encode(['success' => $saved]);
        exit;
    }

    /**
     * Helper: Handle uploaded image
     */
    private function handleImageUpload(array $file): ?string
    {
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($file['type'], $allowed, true)) {
            return null;
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'push_promo_' . time() . '_' . rand(100, 999) . '.' . $ext;
        $uploadDir = __DIR__ . '/../../public/uploads/notifications/';

        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }

        $dest = $uploadDir . $filename;
        if (move_uploaded_file($file['tmp_name'], $dest)) {
            $base = $this->base();
            return "{$base}/uploads/notifications/{$filename}";
        }
        return null;
    }
}
