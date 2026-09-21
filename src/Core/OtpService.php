<?php

namespace Core;

use Core\Database;

/**
 * OtpService – generates, stores, sends, and verifies time-limited OTPs.
 *
 * Since this is a self-hosted XAMPP project without an SMS gateway, the OTP is
 * stored in the database AND placed in the PHP session so the customer can
 * read it from the on-screen "demo" display.  When a real SMS provider is
 * integrated, replace the `_send()` method body.
 */
class OtpService
{
    const LENGTH      = 6;
    const EXPIRY_MINS = 10;

    private $db;

    public function __construct()
    {
        $config   = require __DIR__ . '/../../config/database.php';
        $this->db = new Database($config);
    }

    /**
     * Generate a new OTP for the given phone number (or customer id) and save it.
     *
     * @param string $phone
     * @return array ['code'=>'123456', 'sent'=>bool]
     */
    public function generate(string $phone): array
    {
        $code   = str_pad((string)random_int(0, 999999), self::LENGTH, '0', STR_PAD_LEFT);
        $expiry = date('Y-m-d H:i:s', time() + self::EXPIRY_MINS * 60);

        // Persist to DB (keyed by phone)
        $this->db->query(
            "UPDATE customers SET otp_code = ?, otp_expiry = ?, otp_verified = 0 WHERE phone = ?",
            [$code, $expiry, $phone]
        );

        // Also store in session for the pending-registration flow
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['pending_otp']        = $code;
        $_SESSION['pending_otp_expiry'] = time() + self::EXPIRY_MINS * 60;
        $_SESSION['pending_otp_phone']  = $phone;

        $sent = $this->_send($phone, $code);

        return ['code' => $code, 'sent' => $sent];
    }

    /**
     * Verify OTP submitted by the user.
     *
     * @param string $phone
     * @param string $submitted
     * @param bool   $useSession  If true, validates against session (for pre-registration flows)
     * @return bool
     */
    public function verify(string $phone, string $submitted, bool $useSession = false): bool
    {
        $submitted = trim($submitted);

        if ($useSession) {
            if (session_status() === PHP_SESSION_NONE) session_start();
            if (
                isset($_SESSION['pending_otp'], $_SESSION['pending_otp_expiry'], $_SESSION['pending_otp_phone'])
                && $_SESSION['pending_otp_phone'] === $phone
                && time() < $_SESSION['pending_otp_expiry']
                && hash_equals($_SESSION['pending_otp'], $submitted)
            ) {
                // Mark verified in session
                $_SESSION['otp_phone_verified'] = $phone;
                unset($_SESSION['pending_otp'], $_SESSION['pending_otp_expiry']);
                return true;
            }
            return false;
        }

        // Verify against DB record
        $stmt = $this->db->query(
            "SELECT otp_code, otp_expiry FROM customers WHERE phone = ?",
            [$phone]
        );
        $row = $stmt->fetch();

        if (!$row || empty($row['otp_code']) || empty($row['otp_expiry'])) {
            return false;
        }

        if (strtotime($row['otp_expiry']) < time()) {
            return false; // expired
        }

        if (!hash_equals($row['otp_code'], $submitted)) {
            return false;
        }

        // Mark as verified in DB
        $this->db->query(
            "UPDATE customers SET otp_verified = 1, otp_code = NULL, otp_expiry = NULL WHERE phone = ?",
            [$phone]
        );

        return true;
    }

    /**
     * Generate OTP for a pending (not yet registered) phone number.
     * Stores only in session since the customer row doesn't exist yet.
     */
    public function generateForPending(string $phone): array
    {
        $code = str_pad((string)random_int(0, 999999), self::LENGTH, '0', STR_PAD_LEFT);

        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['pending_otp']        = $code;
        $_SESSION['pending_otp_expiry'] = time() + self::EXPIRY_MINS * 60;
        $_SESSION['pending_otp_phone']  = $phone;

        $sent = $this->_send($phone, $code);
        return ['code' => $code, 'sent' => $sent];
    }

    /**
     * Send OTP via SMS (stub – replace with a real gateway).
     * Returns true when a real send succeeds; always returns true here.
     */
    private function _send(string $phone, string $code): bool
    {
        // TODO: Integrate a real Bangladeshi SMS gateway such as:
        // - SSL Wireless  (https://www.sslwireless.com/sms-api/)
        // - BulkSMSBD     (https://bulksmsbd.net/api/)
        // Example with SSL Wireless (commented out):
        /*
        $apiUrl = 'https://sms.sslwireless.com/pushapi/dynamic/server.php';
        $data = http_build_query([
            'api_token' => 'YOUR_TOKEN',
            'sid'       => 'YOUR_SID',
            'msisdn'    => $phone,
            'sms'       => "Your Fresh E mart OTP is: $code. Valid for " . self::EXPIRY_MINS . " minutes.",
            'csmsid'    => uniqid(),
        ]);
        $ch = curl_init($apiUrl);
        curl_setopt_array($ch, [CURLOPT_POST => 1, CURLOPT_POSTFIELDS => $data, CURLOPT_RETURNTRANSFER => 1]);
        curl_exec($ch);
        curl_close($ch);
        */

        // Log to file for development debugging
        $logLine = date('Y-m-d H:i:s') . " | OTP for $phone: $code\n";
        @file_put_contents(__DIR__ . '/../../otp_log.txt', $logLine, FILE_APPEND);

        return true;
    }
}
