<?php

namespace Core;

use Core\Auth;

class Middleware {

    public static function auth($allowedRoles = []) {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';

        // 1. Check if logged in
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
            header("Location: {$base}/login");
            exit;
        }

        // 2. Check if role is allowed
        if (!empty($allowedRoles)) {
            if (!in_array($_SESSION['role'], $allowedRoles)) {
                http_response_code(403);
                
                if ($_SESSION['role'] === 'delivery_man') {
                    header("Location: {$base}/delivery/dashboard");
                } elseif ($_SESSION['role'] === 'admin') {
                    header("Location: {$base}/admin/dashboard");
                } else {
                    self::renderForbidden("You do not have access to this page based on your user role.");
                }
                exit;
            }
        }
    }

    /**
     * Check if the authenticated user has a specific module permission
     * 
     * @param string|array $permissions
     */
    public static function permission($permissions) {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';

        // 1. Must be logged in
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
            header("Location: {$base}/login");
            exit;
        }

        // Delivery men only have access to rider routes unless granted explicit permission
        if ($_SESSION['role'] === 'delivery_man' && !Auth::can('dashboard')) {
            $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            if (strpos($currentPath, '/admin') !== false) {
                header("Location: {$base}/delivery/dashboard");
                exit;
            }
        }

        // Super Admin always passes
        if (Auth::isAdmin()) {
            return true;
        }

        // Convert single permission to array
        $checkList = is_array($permissions) ? $permissions : [$permissions];

        // Check if user has ANY of the required permissions
        $hasAccess = false;
        foreach ($checkList as $perm) {
            if (Auth::can($perm)) {
                $hasAccess = true;
                break;
            }
        }

        if (!$hasAccess) {
            http_response_code(403);

            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Access Denied: You do not have permission for this module.'
                ]);
                exit;
            }

            self::renderForbidden("You do not have sufficient permissions to access this feature (" . implode(', ', $checkList) . "). Contact Super Admin for access.");
            exit;
        }

        return true;
    }

    /**
     * Render a clean 403 Forbidden page
     */
    protected static function renderForbidden($message) {
        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>403 - Access Denied</title>
            <link href="<?= $base ?>/css/output.css" rel="stylesheet">
            <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
            <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
        </head>
        <body class="bg-secondary-50 font-sans text-secondary-900 min-h-screen flex items-center justify-center p-4">
            <div class="max-w-md w-full bg-white rounded-3xl p-8 border border-secondary-200 shadow-xl text-center space-y-5">
                <div class="w-16 h-16 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center mx-auto text-3xl">
                    <ion-icon name="lock-closed"></ion-icon>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-secondary-900">403 - এক্সেস নিষিদ্ধ (Access Denied)</h1>
                    <p class="text-secondary-600 text-sm mt-2 leading-relaxed">
                        <?= htmlspecialchars($message) ?>
                    </p>
                </div>
                <div class="pt-3 border-t border-secondary-100 flex flex-col sm:flex-row gap-2 justify-center">
                    <a href="<?= $base ?>/admin/dashboard" class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-sm font-medium transition-colors">
                        ড্যাশবোর্ডে ফিরে যান
                    </a>
                    <a href="<?= $base ?>/logout" class="px-5 py-2.5 bg-secondary-100 hover:bg-secondary-200 text-secondary-700 rounded-xl text-sm font-medium transition-colors">
                        লগআউট
                    </a>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }

    public static function verifyCsrf() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] 
                ?? $_SERVER['HTTP_X_CSRF_TOKEN'] 
                ?? $_SERVER['HTTP_X_XSRF_TOKEN'] 
                ?? '';

            if (!$token) {
                $contentType = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
                if (stripos($contentType, 'application/json') !== false) {
                    $raw = file_get_contents('php://input');
                    $data = json_decode($raw, true);
                    if (is_array($data) && !empty($data['csrf_token'])) {
                        $token = $data['csrf_token'];
                    }
                }
            }

            if (!\Core\CSRF::verify($token)) {
                http_response_code(419);
                $isJson = (isset($_SERVER['HTTP_ACCEPT']) && stripos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
                    || (isset($_SERVER['CONTENT_TYPE']) && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== false)
                    || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

                if ($isJson) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode([
                        'success' => false,
                        'status' => 'error',
                        'message' => 'CSRF Security Token Expired. অনুগ্রহ করে পেজটি রিলোড করে আবার চেষ্টা করুন।'
                    ]);
                    exit;
                }
                die("419 Page Expired - CSRF token mismatch.");
            }
        }
    }
}
