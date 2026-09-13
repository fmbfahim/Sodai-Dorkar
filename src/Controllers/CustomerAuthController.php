<?php

namespace Controllers;

use Core\View;
use Core\Database;

class CustomerAuthController {
    protected $db;

    public function __construct() {
        $config = require __DIR__ . '/../../config/database.php';
        $this->db = new Database($config);
    }

    public function showAuth() {
        // If already logged in, redirect to checkout directly
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (isset($_SESSION['customer_id'])) {
            header('Location: /sodai-dorkar/public/checkout');
            exit;
        }

        // Fetch areas for signup dropdown
        $stmt = $this->db->query("SELECT * FROM areas ORDER BY name ASC");
        $areas = $stmt->fetchAll();

        // Fetch settings
        $stmt = $this->db->query("SELECT key_name, value FROM settings");
        $settingsRaw = $stmt->fetchAll();
        $settings = [];
        foreach ($settingsRaw as $s) {
            $settings[$s['key_name']] = $s['value'];
        }

        return View::render('shop/checkout_auth', [
            'areas' => $areas,
            'settings' => $settings,
            'error' => $_GET['error'] ?? null
        ]);
    }

    public function login() {
        $phone = $_POST['phone'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($phone) || empty($password)) {
            header('Location: /sodai-dorkar/public/checkout/auth?error=missing_fields');
            exit;
        }

        $stmt = $this->db->query("SELECT * FROM customers WHERE phone = ?", [$phone]);
        $customer = $stmt->fetch();

        if ($customer) {
            $isPasswordValid = password_verify($password, $customer['password']);
            $isPinValid = (!empty($customer['reset_code']) && $password === $customer['reset_code']);

            if ($isPasswordValid || $isPinValid) {
                if (session_status() === PHP_SESSION_NONE) session_start();
                session_regenerate_id(true);
                
                $_SESSION['customer_id'] = $customer['id'];
                $_SESSION['customer_name'] = $customer['name'];
                
                // If they logged in with PIN, force them to set a new password
                if ($isPinValid) {
                    header('Location: /sodai-dorkar/public/checkout/reset-password');
                    exit;
                }

                // Redirect to intended destination: default to checkout
                $redirect = !empty($_POST['redirect']) ? $_POST['redirect'] : (!empty($_GET['redirect']) ? $_GET['redirect'] : '/sodai-dorkar/public/checkout');
                header('Location: ' . $redirect);
                exit;
            }
        }
        
        header('Location: /sodai-dorkar/public/checkout/auth?error=invalid_credentials');
        exit;
    }

    public function signup() {
        $name = $_POST['name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? '';
        $areaId = $_POST['area_id'] ?? null;
        $zoneId = $_POST['zone_id'] ?? null;
        $pointId = $_POST['point_id'] ?? null;
        $addressDetails = $_POST['address'] ?? ''; // Contains the text from text area if Point is "Other"

        if (empty($name) || empty($phone) || empty($password) || empty($areaId) || empty($zoneId) || empty($pointId)) {
            header('Location: /sodai-dorkar/public/checkout/auth?error=missing_fields');
            exit;
        }

        // Check if phone already exists
        $stmt = $this->db->query("SELECT id FROM customers WHERE phone = ?", [$phone]);
        if ($stmt->fetch()) {
            header('Location: /sodai-dorkar/public/checkout/auth?error=phone_exists');
            exit;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $uniqueCode = 'CUST' . time() . rand(10,99);

        if ($pointId === 'other') {
            $pointId = null;
        }

        $this->db->query("INSERT INTO customers (unique_code, name, phone, email, password, area_id, zone_id, point_id, address_details) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$uniqueCode, $name, $phone, $email, $hashedPassword, $areaId, $zoneId, $pointId, $addressDetails]);
        
        $customerId = $this->db->getConnection()->lastInsertId();

        if (session_status() === PHP_SESSION_NONE) session_start();
        session_regenerate_id(true);
        $_SESSION['customer_id'] = $customerId;
        $_SESSION['customer_name'] = $name;

        // Redirect to checkout so user can immediately complete their order
        $redirect = !empty($_POST['redirect']) ? $_POST['redirect'] : (!empty($_GET['redirect']) ? $_GET['redirect'] : '/sodai-dorkar/public/checkout');
        header('Location: ' . $redirect);
        exit;
    }

    public function showResetPassword() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['customer_id'])) {
            header('Location: /sodai-dorkar/public/checkout/auth');
            exit;
        }

        return View::render('shop/reset_password', [
            'error' => $_GET['error'] ?? null
        ]);
    }

    public function updatePassword() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['customer_id'])) {
            header('Location: /sodai-dorkar/public/checkout/auth');
            exit;
        }

        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (strlen($newPassword) < 6) {
            header('Location: /sodai-dorkar/public/checkout/reset-password?error=short');
            exit;
        }

        if ($newPassword !== $confirmPassword) {
            header('Location: /sodai-dorkar/public/checkout/reset-password?error=mismatch');
            exit;
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $customerId = $_SESSION['customer_id'];

        $this->db->query("UPDATE customers SET password = ?, reset_code = NULL WHERE id = ?", [$hashedPassword, $customerId]);

        header('Location: /sodai-dorkar/public/checkout');
        exit;
    }

    public function firebaseLogin() {
        $phone = $_POST['phone'] ?? '';
        $otp = $_POST['otp'] ?? ''; // Currently mock verification

        if (empty($phone)) {
            header('Location: /sodai-dorkar/public/checkout/auth?error=invalid_credentials');
            exit;
        }

        // Mock verification: In reality, you'd verify the Firebase JWT token here
        // For this demo, we assume the frontend verified it and we just log the user in.
        
        $stmt = $this->db->query("SELECT * FROM customers WHERE phone = ?", [$phone]);
        $customer = $stmt->fetch();

        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($customer) {
            session_regenerate_id(true);
            // Customer exists, log them in
            $_SESSION['customer_id'] = $customer['id'];
            $_SESSION['customer_name'] = $customer['name'];
            
            // Force reset password for security if they are logging in via OTP 
            // OR if you want them to just log in, send them to checkout.
            // Since OTP is secure, we can just send to checkout.
            header('Location: /sodai-dorkar/public/checkout');
            exit;
        } else {
            // New user via OTP. Can't complete order without Area/Zone/Point.
            // Ideally redirect them to a partial profile completion page.
            // For now, redirect back with an error to sign up properly.
            header('Location: /sodai-dorkar/public/checkout/auth?error=create_account_first');
            exit;
        }
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        unset($_SESSION['customer_id']);
        unset($_SESSION['customer_name']);
        header('Location: /sodai-dorkar/public/');
        exit;
    }
}
