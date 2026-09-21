<?php

namespace Core;

use Core\Database;

/**
 * OtpService – generates, stores, sends, and verifies time-limited OTPs.
 *
 * SMS Providers supported (configurable from Admin Settings):
 *  - GreenWeb SMS  (greenweb.com.bd)
 *  - SSL Wireless  (sslwireless.com)
 *  - BulkSMSBD    (bulksmsbd.net)
 *  - Twilio        (international)
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

    // ─────────────────────────────────────────────────────────────
    // Public: Generate OTP for an existing customer (by phone)
    // ─────────────────────────────────────────────────────────────

    public function generate(string $phone): array
    {
        $code   = str_pad((string)random_int(0, 999999), self::LENGTH, '0', STR_PAD_LEFT);
        $expiry = date('Y-m-d H:i:s', time() + self::EXPIRY_MINS * 60);

        // Persist to DB (keyed by phone)
        $this->db->query(
            "UPDATE customers SET otp_code = ?, otp_expiry = ?, otp_verified = 0 WHERE phone = ?",
            [$code, $expiry, $phone]
        );

        // Also store in session for fallback
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['pending_otp']        = $code;
        $_SESSION['pending_otp_expiry'] = time() + self::EXPIRY_MINS * 60;
        $_SESSION['pending_otp_phone']  = $phone;

        $sent = $this->_send($phone, $code);

        return ['code' => $code, 'sent' => $sent];
    }

    // ─────────────────────────────────────────────────────────────
    // Public: Generate OTP for a pending (pre-registration) user
    //         Stored in session only (no DB row yet)
    // ─────────────────────────────────────────────────────────────

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

    // ─────────────────────────────────────────────────────────────
    // Public: Verify OTP
    // ─────────────────────────────────────────────────────────────

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
            return false;
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

    // ─────────────────────────────────────────────────────────────
    // Private: Get SMS gateway settings from DB
    // ─────────────────────────────────────────────────────────────

    private function getSmsSettings(): array
    {
        $stmt = $this->db->query(
            "SELECT key_name, value FROM settings WHERE key_name IN (
                'sms_provider', 'sms_api_key', 'sms_api_token', 'sms_sender_id',
                'sms_username', 'sms_password', 'sms_enabled'
            )"
        );
        $rows = $stmt->fetchAll();
        $cfg  = [];
        foreach ($rows as $r) $cfg[$r['key_name']] = $r['value'];
        return $cfg;
    }

    // ─────────────────────────────────────────────────────────────
    // Private: Send OTP via configured SMS gateway
    // ─────────────────────────────────────────────────────────────

    private function _send(string $phone, string $code): bool
    {
        $cfg = $this->getSmsSettings();

        // Log to file regardless of gateway (for debugging)
        $logLine = date('Y-m-d H:i:s') . " | OTP for $phone: $code\n";
        @file_put_contents(__DIR__ . '/../../otp_log.txt', $logLine, FILE_APPEND);

        // Check if SMS is enabled in admin
        if (($cfg['sms_enabled'] ?? '0') !== '1') {
            return false; // SMS disabled, OTP visible in log only
        }

        $provider = $cfg['sms_provider'] ?? '';
        $message  = "Your verification code is: $code\nValid for " . self::EXPIRY_MINS . " minutes.\n- " . ($cfg['sms_sender_id'] ?? 'FreshMart');

        // Normalize phone: ensure starts with 88 for BD
        $msisdn = $phone;
        if (strlen($msisdn) === 11 && substr($msisdn, 0, 2) === '01') {
            $msisdn = '88' . $msisdn;
        }

        try {
            switch ($provider) {

                // ── GreenWeb SMS ─────────────────────────────────
                case 'greenweb':
                    $url = 'http://api.greenweb.com.bd/api.php?' . http_build_query([
                        'token'   => $cfg['sms_api_token'] ?? '',
                        'to'      => $msisdn,
                        'message' => $message,
                    ]);
                    return $this->_httpGet($url);

                // ── SSL Wireless ─────────────────────────────────
                case 'ssl_wireless':
                    $url  = 'https://sms.sslwireless.com/pushapi/dynamic/server.php';
                    $data = http_build_query([
                        'api_token' => $cfg['sms_api_token'] ?? '',
                        'sid'       => $cfg['sms_sender_id'] ?? '',
                        'msisdn'    => $msisdn,
                        'sms'       => $message,
                        'csmsid'    => uniqid('otp_'),
                    ]);
                    return $this->_httpPost($url, $data);

                // ── BulkSMSBD ────────────────────────────────────
                case 'bulksmsbd':
                    $url = 'https://bulksmsbd.net/api/smsapi?' . http_build_query([
                        'api_key' => $cfg['sms_api_key'] ?? '',
                        'type'    => 'text',
                        'number'  => $msisdn,
                        'senderid'=> $cfg['sms_sender_id'] ?? 'FreshMart',
                        'message' => $message,
                    ]);
                    return $this->_httpGet($url);

                // ── Twilio ───────────────────────────────────────
                case 'twilio':
                    $sid  = $cfg['sms_username'] ?? '';
                    $auth = $cfg['sms_password'] ?? '';
                    $from = $cfg['sms_sender_id'] ?? '';
                    $url  = "https://api.twilio.com/2010-04-01/Accounts/$sid/Messages.json";
                    $data = http_build_query([
                        'To'   => '+' . $msisdn,
                        'From' => $from,
                        'Body' => $message,
                    ]);
                    $ch = curl_init($url);
                    curl_setopt_array($ch, [
                        CURLOPT_POST           => true,
                        CURLOPT_POSTFIELDS     => $data,
                        CURLOPT_USERPWD        => "$sid:$auth",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_TIMEOUT        => 10,
                    ]);
                    $resp = curl_exec($ch);
                    curl_close($ch);
                    $json = json_decode($resp, true);
                    return !empty($json['sid']);

                default:
                    // No recognised provider configured
                    return false;
            }
        } catch (\Throwable $e) {
            @file_put_contents(__DIR__ . '/../../otp_log.txt', date('Y-m-d H:i:s') . " | SMS ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
            return false;
        }
    }

    private function _httpGet(string $url): bool
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $code >= 200 && $code < 300;
    }

    private function _httpPost(string $url, string $data): bool
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $data,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $code >= 200 && $code < 300;
    }
}
