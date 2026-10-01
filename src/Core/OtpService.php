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
        (new \Models\Customer())->ensureSchema();
    }

    // ─────────────────────────────────────────────────────────────
    // Public: Generate OTP for an existing customer (by phone)
    // ─────────────────────────────────────────────────────────────

    public function generate(string $phone): array
    {
        $code   = str_pad((string)random_int(0, 999999), self::LENGTH, '0', STR_PAD_LEFT);
        $expiry = date('Y-m-d H:i:s', time() + self::EXPIRY_MINS * 60);

        // Persist to DB (keyed by phone)
        try {
            $this->db->query(
                "UPDATE customers SET otp_code = ?, otp_expiry = ?, otp_verified = 0 WHERE phone = ?",
                [$code, $expiry, $phone]
            );
        } catch (\Throwable $e) {
            // DB column may not exist; session fallback below is sufficient
        }

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
        try {
            $this->db->query(
                "UPDATE customers SET otp_verified = 1, otp_code = NULL, otp_expiry = NULL WHERE phone = ?",
                [$phone]
            );
        } catch (\Throwable $e) {
            // DB column may not exist; verified in session
        }

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

        // Normalize phone: ensure digits only and starts with 88 for BD
        $msisdn = preg_replace('/\D/', '', $phone);
        if (strlen($msisdn) === 11 && substr($msisdn, 0, 2) === '01') {
            $msisdn = '88' . $msisdn;
        }

        try {
            switch ($provider) {

                // ── Automas SMS (asms.automas.com.bd) ───────────
                case 'automas':
                case 'asms_automas':
                    $apiKey = !empty($cfg['sms_api_key']) ? $cfg['sms_api_key'] : ($cfg['sms_api_token'] ?? '');
                    $sender = $cfg['sms_sender_id'] ?? '';
                    $params = [
                        'apikey'  => $apiKey,
                        'sender'  => $sender,
                        'msisdn'  => $msisdn,
                        'smstext' => $message,
                    ];
                    // If message contains Unicode/Bangla characters, set type=8 & smsformat=8
                    if (preg_match('/[^\x20-\x7E\t\r\n]/', $message)) {
                        $params['type']      = '8';
                        $params['smsformat'] = '8';
                    }
                    $url = 'https://api.automas.com.bd/smsapiv3';
                    $ch = curl_init($url);
                    curl_setopt_array($ch, [
                        CURLOPT_POST           => true,
                        CURLOPT_POSTFIELDS     => http_build_query($params),
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_TIMEOUT        => 12,
                        CURLOPT_SSL_VERIFYPEER => false,
                    ]);
                    $resp = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);

                    @file_put_contents(__DIR__ . '/../../otp_log.txt', date('Y-m-d H:i:s') . " | Automas SMS to $msisdn | Code: $httpCode | Resp: $resp\n", FILE_APPEND);

                    $json = json_decode($resp, true);
                    if (!empty($json['response'][0]['status'])) {
                        $st = (string)$json['response'][0]['status'];
                        return in_array($st, ['100', 'success', 'OK']);
                    }
                    return $httpCode >= 200 && $httpCode < 300 && (strpos($resp, '100') !== false || stripos($resp, 'success') !== false);

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

    // ─────────────────────────────────────────────────────────────
    // Public: Send a test SMS to check gateway connectivity
    // ─────────────────────────────────────────────────────────────

    public function sendTestMessage(string $phone, string $message): array
    {
        $cfg = $this->getSmsSettings();
        if (($cfg['sms_enabled'] ?? '0') !== '1') {
            return [
                'success' => false,
                'message' => 'SMS গেটওয়ে নিষ্ক্রিয় করা আছে (SMS is Disabled in Admin Settings)।'
            ];
        }

        $provider = $cfg['sms_provider'] ?? '';
        if (empty($provider)) {
            return [
                'success' => false,
                'message' => 'কোনো SMS Provider নির্বাচন করা নেই।'
            ];
        }

        $msisdn = preg_replace('/\D/', '', $phone);
        if (strlen($msisdn) === 11 && substr($msisdn, 0, 2) === '01') {
            $msisdn = '88' . $msisdn;
        }

        if ($provider === 'automas' || $provider === 'asms_automas') {
            $apiKey = !empty($cfg['sms_api_key']) ? $cfg['sms_api_key'] : ($cfg['sms_api_token'] ?? '');
            $sender = $cfg['sms_sender_id'] ?? '';
            if (empty($apiKey) || empty($sender)) {
                return [
                    'success' => false,
                    'message' => 'Automas API Key বা Sender ID পূরণ করা হয়নি।'
                ];
            }
            $params = [
                'apikey'  => $apiKey,
                'sender'  => $sender,
                'msisdn'  => $msisdn,
                'smstext' => $message,
            ];
            if (preg_match('/[^\x20-\x7E\t\r\n]/', $message)) {
                $params['type']      = '8';
                $params['smsformat'] = '8';
            }
            $url = 'https://api.automas.com.bd/smsapiv3';
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => http_build_query($params),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 12,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);
            $resp = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlErr = curl_error($ch);
            curl_close($ch);

            @file_put_contents(__DIR__ . '/../../otp_log.txt', date('Y-m-d H:i:s') . " | Automas Test SMS to $msisdn | Code: $httpCode | Resp: $resp\n", FILE_APPEND);

            $json = json_decode($resp, true);
            $st = !empty($json['response'][0]['status']) ? (string)$json['response'][0]['status'] : '';

            $statusTextMap = [
                '100' => 'মেসেজ সফলভাবে পাঠানো হয়েছে (Success)',
                '101' => 'ভুল API Key (Invalid API Key)',
                '102' => 'ভুল Sender ID (Invalid Sender ID)',
                '103' => 'পর্যাপ্ত ব্যালেন্স নেই (Insufficient Balance)',
                '104' => 'ভুল মোবাইল নম্বর (Invalid Mobile Number)',
                '105' => 'প্রয়োজনীয় প্যারামিটার বাদ পড়েছে (Missing Parameter)',
                '106' => 'অননুমোদিত API Key বা IP এক্সেস নেই (Invalid API Key or IP)',
                '107' => 'মেসেজ খালি (Empty Message)',
            ];

            if ($st === '100' || $st === 'success') {
                return [
                    'success'  => true,
                    'message'  => '✅ টেস্ট SMS সফলভাবে পাঠানো হয়েছে!',
                    'raw'      => $resp,
                    'provider' => 'Automas SMS (asms.automas.com.bd)'
                ];
            } else {
                $errDesc = $statusTextMap[$st] ?? "স্ট্যাটাস কোড: $st";
                return [
                    'success'  => false,
                    'message'  => "❌ SMS পাঠাতে ব্যর্থ: $errDesc",
                    'raw'      => $resp ?: $curlErr,
                    'provider' => 'Automas SMS (asms.automas.com.bd)'
                ];
            }
        }

        $sent = $this->_send($msisdn, 'TEST00');
        return [
            'success'  => $sent,
            'message'  => $sent ? 'টেস্ট SMS সফল হয়েছে' : 'টেস্ট SMS পাঠাতে ব্যর্থ হয়েছে। অনুগ্রহ করে সেটিংস ও লগ চেক করুন।',
            'provider' => $provider
        ];
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
