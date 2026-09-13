<?php

namespace Controllers;

use Core\View;
use Core\Database;

class AuthController {
    
    public function showLogin() {
        return View::render('auth.login');
    }

    public function login() {
        // Simple raw PHP login logic
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            return View::render('auth.login', ['error' => 'Please fill in all fields']);
        }

        global $config; // Assuming we can access config, or better, instantiate DB here
        // Ideally DB should be a singleton or injected. For this simple setup:
        $db = new Database(require __DIR__ . '/../../config/database.php');

        $stmt = $db->query("SELECT * FROM users WHERE username = :username", ['username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if (session_status() === PHP_SESSION_NONE) session_start();
            session_regenerate_id(true);
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];

            // Redirect based on role
            $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
            if ($user['role'] === 'delivery_man') {
                header("Location: {$base}/delivery/dashboard");
            } elseif ($user['role'] === 'admin') {
                header("Location: {$base}/admin/dashboard");
            } else {
                header("Location: {$base}/");
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
