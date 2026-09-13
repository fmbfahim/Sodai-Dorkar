<?php

namespace Core;

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
        // If $allowedRoles is empty, just login is enough
        if (!empty($allowedRoles)) {
            if (!in_array($_SESSION['role'], $allowedRoles)) {
                // Unauthorized
                http_response_code(403);
                
                // Redirect based on their actual role to avoid stuck pages
                if ($_SESSION['role'] === 'delivery_man') {
                    header("Location: {$base}/delivery/dashboard");
                } elseif ($_SESSION['role'] === 'admin') {
                    header("Location: {$base}/admin/dashboard");
                } else {
                    echo "403 Forbidden - You do not have access to this page.";
                    echo "<br><a href='{$base}/logout'>Logout</a>";
                }
                exit;
            }
        }
    }

    public static function verifyCsrf() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            if (!\Core\CSRF::verify($token)) {
                http_response_code(419);
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'error', 'message' => 'CSRF token mismatch.']);
                    exit;
                }
                die("419 Page Expired - CSRF token mismatch.");
            }
        }
    }
}
