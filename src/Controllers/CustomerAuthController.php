<?php

namespace Controllers;

use Core\View;
use Core\Database;
use Core\OtpService;

class CustomerAuthController
{
    protected $db;
    protected $otpService;

    public function __construct()
    {
        $config           = require __DIR__ . '/../../config/database.php';
        $this->db         = new Database($config);
        $this->otpService = new OtpService();
    }

    // ─────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────

    private function base(): string
    {
        return (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false)
            ? '/sodai-dorkar/public'
            : '';
    }

    private function getSetting(string $key, string $default = ''): string
    {
        $stmt = $this->db->query("SELECT value FROM settings WHERE key_name = ?", [$key]);
        $row  = $stmt->fetch();
        return $row ? (string)$row['value'] : $default;
    }

    private function isOtpEnabled(): bool
    {
        return $this->getSetting('otp_required_signup', '0') === '1';
    }

    private function isOtpPasswordChangeEnabled(): bool
    {
        return $this->getSetting('otp_required_password_change', '0') === '1';
    }

    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
    }

    private function redirect(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }

    // ─────────────────────────────────────────────
    // Show Auth Page (login + signup tabs)
    // ─────────────────────────────────────────────

    public function showAuth()
    {
        $this->startSession();
        if (isset($_SESSION['customer_id'])) {
            $this->redirect($this->base() . '/checkout');
        }

        $stmt    = $this->db->query("SELECT * FROM areas ORDER BY name ASC");
        $areas   = $stmt->fetchAll();

        $stmt2      = $this->db->query("SELECT key_name, value FROM settings");
        $settingsRaw = $stmt2->fetchAll();
        $settings   = [];
        foreach ($settingsRaw as $s) {
            $settings[$s['key_name']] = $s['value'];
        }

        return View::render('shop/checkout_auth', [
            'areas'    => $areas,
            'settings' => $settings,
            'error'    => $_GET['error'] ?? null,
            'tab'      => $_GET['tab'] ?? 'login',
        ]);
    }

    // ─────────────────────────────────────────────
    // LOGIN
    // ─────────────────────────────────────────────

    public function login()
    {
        $phone    = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $base     = $this->base();

        if (empty($phone) || empty($password)) {
            $this->redirect("$base/checkout/auth?error=missing_fields");
        }

        $stmt     = $this->db->query("SELECT * FROM customers WHERE phone = ?", [$phone]);
        $customer = $stmt->fetch();

        if ($customer) {
            $isPasswordValid = password_verify($password, $customer['password']);
            $isPinValid      = (!empty($customer['reset_code']) && $password === $customer['reset_code']);

            if ($isPasswordValid || $isPinValid) {
                $this->startSession();
                session_regenerate_id(true);
                $_SESSION['customer_id']   = $customer['id'];
                $_SESSION['customer_name'] = $customer['name'];

                if ($isPinValid) {
                    $this->redirect("$base/checkout/reset-password");
                }

                $redirect = $_POST['redirect'] ?? $_GET['redirect'] ?? "$base/checkout";
                $this->redirect($redirect);
            }
        }

        $this->redirect("$base/checkout/auth?error=invalid_credentials");
    }

    // ─────────────────────────────────────────────
    // SIGNUP – Step 1: Send OTP (if OTP enabled)
    // ─────────────────────────────────────────────

    public function signup()
    {
        $base = $this->base();

        $name           = trim($_POST['name'] ?? '');
        $phone          = trim($_POST['phone'] ?? '');
        $email          = trim($_POST['email'] ?? '') ?: null;
        $password       = $_POST['password'] ?? '';
        $areaId         = $_POST['area_id'] ?? null;
        $zoneId         = $_POST['zone_id'] ?? null;
        $pointId        = $_POST['point_id'] ?? null;
        $addressDetails = trim($_POST['address'] ?? '');

        // Validate required fields
        if (empty($name) || empty($phone) || empty($password) || empty($areaId) || empty($zoneId) || empty($pointId)) {
            $this->redirect("$base/checkout/auth?error=missing_fields&tab=signup");
        }

        // Check duplicate phone
        $stmt = $this->db->query("SELECT id FROM customers WHERE phone = ?", [$phone]);
        if ($stmt->fetch()) {
            $this->redirect("$base/checkout/auth?error=phone_exists&tab=signup");
        }

        // If OTP required: save form data in session, send OTP, redirect to OTP page
        if ($this->isOtpEnabled()) {
            $this->startSession();
            $_SESSION['signup_pending'] = [
                'name'            => $name,
                'phone'           => $phone,
                'email'           => $email,
                'password'        => $password,
                'area_id'         => $areaId,
                'zone_id'         => $zoneId,
                'point_id'        => $pointId,
                'address_details' => $addressDetails,
            ];

            $result = $this->otpService->generateForPending($phone);
            $this->redirect("$base/checkout/otp-verify?purpose=signup&phone=" . urlencode($phone));
        }

        // No OTP required – create account directly
        $this->createCustomer($name, $phone, $email, $password, $areaId, $zoneId, $pointId, $addressDetails);
        $redirect = $_POST['redirect'] ?? $_GET['redirect'] ?? "$base/checkout";
        $this->redirect($redirect);
    }

    // ─────────────────────────────────────────────
    // SIGNUP – OTP Verify page
    // ─────────────────────────────────────────────

    public function showOtpVerify()
    {
        $this->startSession();
        $base    = $this->base();
        $purpose = $_GET['purpose'] ?? 'signup';
        $phone   = $_GET['phone'] ?? '';

        // If no pending data for signup, redirect back
        if ($purpose === 'signup' && empty($_SESSION['signup_pending'])) {
            $this->redirect("$base/checkout/auth?tab=signup");
        }
        if ($purpose === 'password_change' && empty($_SESSION['customer_id'])) {
            $this->redirect("$base/checkout/auth");
        }

        $stmt2      = $this->db->query("SELECT key_name, value FROM settings");
        $settingsRaw = $stmt2->fetchAll();
        $settings   = [];
        foreach ($settingsRaw as $s) $settings[$s['key_name']] = $s['value'];

        return View::render('shop/otp_verify', [
            'purpose'  => $purpose,
            'phone'    => $phone,
            'settings' => $settings,
            'error'    => $_GET['error'] ?? null,
        ]);
    }

    // ─────────────────────────────────────────────
    // SIGNUP – Step 2: Verify OTP and complete registration
    // ─────────────────────────────────────────────

    public function verifyOtp()
    {
        $this->startSession();
        $base    = $this->base();
        $purpose = $_POST['purpose'] ?? 'signup';
        $phone   = trim($_POST['phone'] ?? '');
        $code    = trim($_POST['otp_code'] ?? '');

        if (empty($phone) || empty($code)) {
            $this->redirect("$base/checkout/otp-verify?purpose=$purpose&phone=" . urlencode($phone) . "&error=missing_code");
        }

        // ── Signup OTP verify ──────────────────────────────────
        if ($purpose === 'signup') {
            if (empty($_SESSION['signup_pending'])) {
                $this->redirect("$base/checkout/auth?tab=signup");
            }

            $valid = $this->otpService->verify($phone, $code, true);
            if (!$valid) {
                $this->redirect("$base/checkout/otp-verify?purpose=signup&phone=" . urlencode($phone) . "&error=invalid_otp");
            }

            $d = $_SESSION['signup_pending'];
            unset($_SESSION['signup_pending']);

            $this->createCustomer(
                $d['name'], $d['phone'], $d['email'], $d['password'],
                $d['area_id'], $d['zone_id'], $d['point_id'], $d['address_details']
            );

            $redirect = $d['redirect'] ?? "$base/checkout";
            $this->redirect($redirect);
        }

        // ── Password-change OTP verify ─────────────────────────
        if ($purpose === 'password_change') {
            if (empty($_SESSION['customer_id'])) {
                $this->redirect("$base/checkout/auth");
            }

            $customerId = $_SESSION['customer_id'];
            $stmt = $this->db->query("SELECT phone FROM customers WHERE id = ?", [$customerId]);
            $row  = $stmt->fetch();

            if (!$row || $row['phone'] !== $phone) {
                $this->redirect("$base/checkout/otp-verify?purpose=password_change&phone=" . urlencode($phone) . "&error=phone_mismatch");
            }

            $valid = $this->otpService->verify($phone, $code);
            if (!$valid) {
                $this->redirect("$base/checkout/otp-verify?purpose=password_change&phone=" . urlencode($phone) . "&error=invalid_otp");
            }

            // Mark OTP verified in session so password-change form can proceed
            $_SESSION['otp_pwd_change_verified'] = true;
            $this->redirect("$base/account/change-password");
        }

        $this->redirect("$base/checkout/auth");
    }

    // ─────────────────────────────────────────────
    // RESEND OTP
    // ─────────────────────────────────────────────

    public function resendOtp()
    {
        $this->startSession();
        $base    = $this->base();
        $purpose = $_POST['purpose'] ?? 'signup';
        $phone   = trim($_POST['phone'] ?? '');

        if (empty($phone)) {
            $this->redirect("$base/checkout/auth");
        }

        if ($purpose === 'signup') {
            $this->otpService->generateForPending($phone);
        } else {
            $this->otpService->generate($phone);
        }

        $this->redirect("$base/checkout/otp-verify?purpose=$purpose&phone=" . urlencode($phone) . "&resent=1");
    }

    // ─────────────────────────────────────────────
    // Initiate OTP for password change
    // ─────────────────────────────────────────────

    public function initiatePasswordChangeOtp()
    {
        $this->startSession();
        $base = $this->base();

        if (empty($_SESSION['customer_id'])) {
            $this->redirect("$base/checkout/auth");
        }

        $customerId = $_SESSION['customer_id'];
        $stmt       = $this->db->query("SELECT phone FROM customers WHERE id = ?", [$customerId]);
        $row        = $stmt->fetch();

        if (!$row) {
            $this->redirect("$base/account/change-password?error=not_found");
        }

        $phone = $row['phone'];
        $this->otpService->generate($phone);
        $this->redirect("$base/checkout/otp-verify?purpose=password_change&phone=" . urlencode($phone));
    }

    // ─────────────────────────────────────────────
    // Reset Password (after PIN login)
    // ─────────────────────────────────────────────

    public function showResetPassword()
    {
        $this->startSession();
        $base = $this->base();
        if (empty($_SESSION['customer_id'])) {
            $this->redirect("$base/checkout/auth");
        }
        return View::render('shop/reset_password', ['error' => $_GET['error'] ?? null]);
    }

    public function updatePassword()
    {
        $this->startSession();
        $base = $this->base();
        if (empty($_SESSION['customer_id'])) {
            $this->redirect("$base/checkout/auth");
        }

        $newPassword     = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (strlen($newPassword) < 6) {
            $this->redirect("$base/checkout/reset-password?error=short");
        }
        if ($newPassword !== $confirmPassword) {
            $this->redirect("$base/checkout/reset-password?error=mismatch");
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $customerId     = $_SESSION['customer_id'];
        $this->db->query(
            "UPDATE customers SET password = ?, reset_code = NULL WHERE id = ?",
            [$hashedPassword, $customerId]
        );

        $this->redirect("$base/checkout");
    }

    // ─────────────────────────────────────────────
    // Firebase / OTP phone login (legacy mock)
    // ─────────────────────────────────────────────

    public function firebaseLogin()
    {
        $base  = $this->base();
        $phone = trim($_POST['phone'] ?? '');

        if (empty($phone)) {
            $this->redirect("$base/checkout/auth?error=invalid_credentials");
        }

        $stmt     = $this->db->query("SELECT * FROM customers WHERE phone = ?", [$phone]);
        $customer = $stmt->fetch();

        $this->startSession();
        if ($customer) {
            session_regenerate_id(true);
            $_SESSION['customer_id']   = $customer['id'];
            $_SESSION['customer_name'] = $customer['name'];
            $this->redirect("$base/checkout");
        }

        $this->redirect("$base/checkout/auth?error=create_account_first");
    }

    // ─────────────────────────────────────────────
    // Logout
    // ─────────────────────────────────────────────

    public function logout()
    {
        $this->startSession();
        unset($_SESSION['customer_id'], $_SESSION['customer_name']);
        $this->redirect($this->base() . '/');
    }

    // ─────────────────────────────────────────────
    // Private: Create Customer Row + Login
    // ─────────────────────────────────────────────

    private function createCustomer(
        string  $name,
        string  $phone,
        ?string $email,
        string  $password,
        mixed   $areaId,
        mixed   $zoneId,
        mixed   $pointId,
        string  $addressDetails
    ): void {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $uniqueCode     = 'CUST' . time() . rand(10, 99);

        if ($pointId === 'other') {
            $pointId = null;
        }

        $this->db->query(
            "INSERT INTO customers (unique_code, name, phone, email, password, area_id, zone_id, point_id, address_details, otp_verified)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)",
            [$uniqueCode, $name, $phone, $email ?: null, $hashedPassword, $areaId, $zoneId, $pointId, $addressDetails]
        );

        $customerId = $this->db->getConnection()->lastInsertId();

        $this->startSession();
        session_regenerate_id(true);
        $_SESSION['customer_id']   = $customerId;
        $_SESSION['customer_name'] = $name;
    }
}
