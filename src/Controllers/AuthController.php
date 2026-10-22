<?php

namespace Controllers;

use Core\View;
use Core\Database;
use Core\Auth;

class AuthController {
    
    public function showLogin() {
        Auth::restoreSessionFromCookie();
        if (Auth::check()) {
            $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
            $role = Auth::role();
            if ($role === 'delivery_man') {
                header("Location: {$base}/delivery/dashboard");
            } elseif ($role === 'agent') {
                header("Location: {$base}/agent/dashboard");
            } else {
                header("Location: {$base}/admin/dashboard");
            }
            exit;
        }
        return View::render('auth.login');
    }

    public function login() {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            return View::render('auth.login', ['error' => 'Please fill in all fields']);
        }

        $db = new Database(require __DIR__ . '/../../config/database.php');

        $corePhone = class_exists('\\Models\\Customer') ? \Models\Customer::extractCorePhone($username) : preg_replace('/\\D/', '', $username);

        $sql = "SELECT * FROM users WHERE username = :ident OR email = :ident OR phone = :ident";
        $params = ['ident' => $username];
        if (!empty($corePhone)) {
            $sql .= " OR phone LIKE :phone_like OR username LIKE :user_phone";
            $params['phone_like'] = '%' . $corePhone . '%';
            $params['user_phone'] = '%' . $corePhone . '%';
        }

        $stmt = $db->query($sql, $params);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Check status
            if (($user['status'] ?? 'active') === 'inactive') {
                return View::render('auth.login', ['error' => 'Your account has been deactivated. Please contact Super Admin.']);
            }

            if (session_status() === PHP_SESSION_NONE) session_start();
            session_regenerate_id(true);
            
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['username'] = $user['username'];

            // Cache permissions
            $_SESSION['permissions'] = Auth::userPermissions($user['id']);

            // Persist login so closing the browser does not log the user out
            Auth::persistUserLogin($user['id']);

            // If user has a matching customer profile, auto-link customer session
            if (!empty($user['phone'])) {
                try {
                    $custModel = new \Models\Customer();
                    $cust = $custModel->findByPhone($user['phone']);
                    if ($cust) {
                        $_SESSION['customer_id'] = (int)$cust['id'];
                        $_SESSION['customer_name'] = $cust['name'];
                        $_SESSION['customer_phone'] = $cust['phone'];
                        $_SESSION['customer_email'] = $cust['email'] ?? '';
                        $_SESSION['customer_address'] = $cust['address_details'] ?? '';
                        Auth::persistCustomerLogin($cust['id']);
                    }
                } catch (\Throwable $e) {}
            }

            $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';

            // Redirect based on role
            if ($user['role'] === 'delivery_man') {
                header("Location: {$base}/delivery/dashboard");
            } elseif ($user['role'] === 'agent') {
                header("Location: {$base}/agent/dashboard");
            } else {
                // Admin, Manager, Accountant, Staff go to admin dashboard
                header("Location: {$base}/admin/dashboard");
            }
            exit;
        } else {
            return View::render('auth.login', ['error' => 'Invalid credentials']);
        }
    }

    public function logout() {
        Auth::clearUserLogin();
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
        header("Location: {$base}/login");
        exit;
    }
}
