<?php

namespace Controllers;

use Core\View;
use Core\Database;
use Core\Auth;

class AuthController {
    
    public function showLogin() {
        return View::render('auth.login');
    }

    public function login() {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            return View::render('auth.login', ['error' => 'Please fill in all fields']);
        }

        $db = new Database(require __DIR__ . '/../../config/database.php');

        $stmt = $db->query("SELECT * FROM users WHERE username = :username", ['username' => $username]);
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

            $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';

            // Redirect based on role
            if ($user['role'] === 'delivery_man') {
                header("Location: {$base}/delivery/dashboard");
            } else {
                // Admin, Manager, Accountant, Staff, Agent go to admin dashboard
                header("Location: {$base}/admin/dashboard");
            }
            exit;
        } else {
            return View::render('auth.login', ['error' => 'Invalid credentials']);
        }
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
        header("Location: {$base}/login");
        exit;
    }
}
