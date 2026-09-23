<?php

namespace Controllers;

use Core\Database;
use Models\Setting;

/**
 * Handles Firebase Phone Authentication token verification.
 *
 * Flow:
 *  1. Client-side: Firebase JS SDK sends OTP → user verifies → idToken generated
 *  2. Client POSTs: idToken + signup form data → this controller
 *  3. Server: verifies idToken via Firebase REST API → extracts phone number
 *  4. Server: finds/creates customer → sets session → redirects
 */
class FirebaseAuthController
{
    private Database $db;

    public function __construct()
    {
        $config   = require __DIR__ . '/../../config/database.php';
        $this->db = new Database($config);
        (new \Models\Customer())->ensureSchema();
    }

    private function base(): string
    {
        return (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false)
            ? '/sodai-dorkar/public'
            : '';
    }

    private function redirect(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }

    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
    }

    // ──────────────────────────────────────────────────────────────
    //  Verify Firebase ID Token via REST and extract phone number
    // ──────────────────────────────────────────────────────────────

    private function verifyIdToken(string $idToken): ?string
    {
        // API Key — Admin Settings থেকে পড়াহয় বে (Admin সেটিংস → Firebase API Key)
        $apiKey = Setting::getValue('firebase_api_key', '');

        if (empty($apiKey)) {
            error_log('FirebaseAuthController: firebase_api_key is not configured in Admin Settings.');
            return null;
        }

        $url  = "https://identitytoolkit.googleapis.com/v1/accounts:lookup?key={$apiKey}";
        $body = json_encode(['idToken' => $idToken]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $resp = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$resp) {
            error_log("Firebase token verify failed: HTTP $httpCode — $resp");
            return null;
        }

        $data = json_decode($resp, true);
        $user = $data['users'][0] ?? null;

        if (!$user) {
            error_log("Firebase token verify: no user in response");
            return null;
        }

        return $user['phoneNumber'] ?? null; // e.g. "+8801711234567"
    }

    // ──────────────────────────────────────────────────────────────
    //  Normalize BD phone: "+8801711234567" → "01711234567"
    // ──────────────────────────────────────────────────────────────

    private function normalizePhone(string $phone): string
    {
        // Remove spaces and dashes
        $phone = preg_replace('/[\s\-]/', '', $phone);
        // "+8801711234567" → "01711234567"
        if (str_starts_with($phone, '+88')) {
            $phone = substr($phone, 3);
        } elseif (str_starts_with($phone, '88') && strlen($phone) === 13) {
            $phone = substr($phone, 2);
        }
        return $phone;
    }

    // ──────────────────────────────────────────────────────────────
    //  POST /checkout/firebase-verify-signup
    //  Called after Firebase OTP confirmed on signup
    // ──────────────────────────────────────────────────────────────

    public function verifySignup(): void
    {
        $this->startSession();
        $base = $this->base();

        $idToken  = trim($_POST['firebase_token'] ?? '');
        $name     = trim($_POST['name'] ?? '');
        $password = $_POST['password'] ?? '';
        $email    = trim($_POST['email'] ?? '') ?: null;
        $areaId   = $_POST['area_id'] ?? null;
        $zoneId   = $_POST['zone_id'] ?? null;
        $pointId  = $_POST['point_id'] ?? null;
        $address  = trim($_POST['address'] ?? '');

        // Validate token
        if (empty($idToken)) {
            $this->redirect("$base/checkout/auth?error=firebase_failed&tab=signup");
        }

        $firebasePhone = $this->verifyIdToken($idToken);
        if (!$firebasePhone) {
            $this->redirect("$base/checkout/auth?error=firebase_failed&tab=signup");
        }

        $phone = $this->normalizePhone($firebasePhone);

        // Check if customer already exists (phone already registered)
        $stmt = $this->db->query("SELECT * FROM customers WHERE phone = ?", [$phone]);
        $existing = $stmt->fetch();

        if ($existing) {
            // Auto-login if already exists
            session_regenerate_id(true);
            $_SESSION['customer_id']   = $existing['id'];
            $_SESSION['customer_name'] = $existing['name'];
            $this->redirect("$base/checkout");
        }

        // Validate required signup fields
        if (empty($name) || empty($password) || empty($areaId) || empty($zoneId) || empty($pointId)) {
            $this->redirect("$base/checkout/auth?error=missing_fields&tab=signup");
        }

        // Create customer
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $uniqueCode     = 'CUST' . time() . rand(10, 99);

        if ($pointId === 'other') $pointId = null;

        (new \Models\Customer())->ensureSchema();

        try {
            $colsStmt = $this->db->query("SHOW COLUMNS FROM customers");
            $cols = $colsStmt ? $colsStmt->fetchAll(\PDO::FETCH_COLUMN) : [];
        } catch (\Throwable $e) {
            $cols = [];
        }

        $fields = ['unique_code', 'name', 'phone'];
        $placeholders = ['?', '?', '?'];
        $values = [$uniqueCode, $name, $phone];

        if (empty($cols) || in_array('email', $cols)) {
            $fields[] = 'email';
            $placeholders[] = '?';
            $values[] = $email ?: null;
        }
        if (empty($cols) || in_array('password', $cols)) {
            $fields[] = 'password';
            $placeholders[] = '?';
            $values[] = $hashedPassword;
        }
        if (empty($cols) || in_array('area_id', $cols)) {
            $fields[] = 'area_id';
            $placeholders[] = '?';
            $values[] = $areaId;
        }
        if (empty($cols) || in_array('zone_id', $cols)) {
            $fields[] = 'zone_id';
            $placeholders[] = '?';
            $values[] = $zoneId;
        }
        if (empty($cols) || in_array('point_id', $cols)) {
            $fields[] = 'point_id';
            $placeholders[] = '?';
            $values[] = $pointId;
        }
        if (empty($cols) || in_array('address_details', $cols)) {
            $fields[] = 'address_details';
            $placeholders[] = '?';
            $values[] = $address;
        }
        if (!empty($cols) && in_array('otp_verified', $cols)) {
            $fields[] = 'otp_verified';
            $placeholders[] = '?';
            $values[] = 1;
        }

        $sql = "INSERT INTO customers (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $this->db->query($sql, $values);

        $customerId = $this->db->getConnection()->lastInsertId();

        session_regenerate_id(true);
        $_SESSION['customer_id']   = $customerId;
        $_SESSION['customer_name'] = $name;

        $this->redirect("$base/checkout");
    }

    // ──────────────────────────────────────────────────────────────
    //  POST /checkout/firebase-verify-login
    //  Called after Firebase OTP confirmed on login
    // ──────────────────────────────────────────────────────────────

    public function verifyLogin(): void
    {
        $this->startSession();
        $base = $this->base();

        $idToken = trim($_POST['firebase_token'] ?? '');

        if (empty($idToken)) {
            $this->redirect("$base/checkout/auth?error=firebase_failed");
        }

        $firebasePhone = $this->verifyIdToken($idToken);
        if (!$firebasePhone) {
            $this->redirect("$base/checkout/auth?error=firebase_failed");
        }

        $phone = $this->normalizePhone($firebasePhone);

        $stmt     = $this->db->query("SELECT * FROM customers WHERE phone = ?", [$phone]);
        $customer = $stmt->fetch();

        if (!$customer) {
            // No account → send to signup tab with phone pre-filled
            $this->redirect("$base/checkout/auth?error=create_account_first&tab=signup&phone=" . urlencode($phone));
        }

        session_regenerate_id(true);
        $_SESSION['customer_id']   = $customer['id'];
        $_SESSION['customer_name'] = $customer['name'];

        $redirect = $_POST['redirect'] ?? "$base/checkout";
        $this->redirect($redirect);
    }
}
