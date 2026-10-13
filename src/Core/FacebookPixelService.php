<?php

namespace Core;

use Models\Setting;

/**
 * FacebookPixelService
 * 
 * Handles Meta / Facebook Pixel configuration, Browser Pixel tracking,
 * and Server-Side Meta Conversions API (CAPI) event delivery.
 */
class FacebookPixelService
{
    private static ?array $settings = null;

    /**
     * Get all pixel settings from DB cache
     */
    public static function getSettings(): array
    {
        if (self::$settings === null) {
            self::$settings = Setting::getMultiple([
                'facebook_pixel_enabled',
                'facebook_pixel_id',
                'facebook_capi_token',
                'facebook_capi_test_code',
                'facebook_pixel_track_pageview',
                'facebook_pixel_track_view_content',
                'facebook_pixel_track_add_to_cart',
                'facebook_pixel_track_initiate_checkout',
                'facebook_pixel_track_purchase'
            ]);
        }
        return self::$settings;
    }

    /**
     * Check if pixel is enabled and has a valid ID
     */
    public static function isEnabled(): bool
    {
        $cfg = self::getSettings();
        $enabled = $cfg['facebook_pixel_enabled'] ?? '0';
        $pixelId = trim($cfg['facebook_pixel_id'] ?? '');
        return (!empty($pixelId) && in_array((string)$enabled, ['1', 'true', 'on'], true));
    }

    public static function getPixelId(): string
    {
        return trim(self::getSettings()['facebook_pixel_id'] ?? '');
    }

    public static function getCapiToken(): string
    {
        return trim(self::getSettings()['facebook_capi_token'] ?? '');
    }

    public static function getTestEventCode(): string
    {
        // 1. Setting configured in database
        $code = trim(self::getSettings()['facebook_capi_test_code'] ?? '');
        if (!empty($code)) {
            return $code;
        }

        // 2. Query parameter (?test_event_code=...) when opened via Meta Events Manager
        if (!empty($_GET['test_event_code'])) {
            $code = trim($_GET['test_event_code']);
            if (session_status() === PHP_SESSION_ACTIVE) {
                $_SESSION['fb_test_code'] = $code;
            }
            return $code;
        }

        // 3. Stored in active PHP session
        if (session_status() === PHP_SESSION_ACTIVE && !empty($_SESSION['fb_test_code'])) {
            return trim($_SESSION['fb_test_code']);
        }

        // 4. Stored in browser cookie
        if (!empty($_COOKIE['fb_test_code'])) {
            return trim($_COOKIE['fb_test_code']);
        }

        return '';
    }

    public static function isTrackPageViewEnabled(): bool
    {
        return (self::getSettings()['facebook_pixel_track_pageview'] ?? '1') == '1';
    }

    public static function isTrackViewContentEnabled(): bool
    {
        return (self::getSettings()['facebook_pixel_track_view_content'] ?? '1') == '1';
    }

    public static function isTrackAddToCartEnabled(): bool
    {
        return (self::getSettings()['facebook_pixel_track_add_to_cart'] ?? '1') == '1';
    }

    public static function isTrackInitiateCheckoutEnabled(): bool
    {
        return (self::getSettings()['facebook_pixel_track_initiate_checkout'] ?? '1') == '1';
    }

    public static function isTrackPurchaseEnabled(): bool
    {
        return (self::getSettings()['facebook_pixel_track_purchase'] ?? '1') == '1';
    }

    /**
     * Render the official Meta Pixel snippet for <head>
     */
    public static function renderHeaderSnippet(): string
    {
        if (!self::isEnabled()) {
            return '';
        }

        $pixelId = htmlspecialchars(self::getPixelId(), ENT_QUOTES, 'UTF-8');
        $testCode = self::getTestEventCode();
        $trackPageView = self::isTrackPageViewEnabled();

        $testCodeJs = !empty($testCode) ? json_encode($testCode) : 'null';
        $pageViewCall = $trackPageView ? "window.fbpTrack('PageView');" : "";

        return <<<HTML
<!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '{$pixelId}');

// Persistent Meta Pixel Test Event & Multi-touch Tracker
(function() {
    try {
        var urlParams = new URLSearchParams(window.location.search);
        var urlTestCode = urlParams.get('test_event_code');
        if (urlTestCode) {
            sessionStorage.setItem('fb_test_code', urlTestCode);
            document.cookie = "fb_test_code=" + encodeURIComponent(urlTestCode) + "; path=/; max-age=86400; SameSite=Lax";
        }
    } catch (e) {}

    var activeTestCode = {$testCodeJs} || (function() {
        try {
            return sessionStorage.getItem('fb_test_code') || 
                   (document.cookie.match(/(?:^|; )fb_test_code=([^;]*)/) || [])[1] || null;
        } catch(e) { return null; }
    })();

    window.FB_TEST_CODE = activeTestCode;

    window.fbpTrack = function(eventName, customData, eventId) {
        if (typeof fbq !== 'function') return;
        customData = customData || {};

        // Generate shared unique eventId for browser & server deduplication if not provided
        eventId = eventId || ('ev_' + eventName.toLowerCase() + '_' + Date.now() + '_' + Math.random().toString(36).substr(2, 7));

        // 1. Browser Pixel tracking (Meta officially accepts { eventID: eventId })
        fbq('track', eventName, customData, { eventID: eventId });

        // 2. Asynchronous Server CAPI dispatch (delivers to Meta Events Manager with test_event_code)
        try {
            var base = (window.APP_BASE !== undefined) ? window.APP_BASE : (window.location.pathname.includes('/sodai-dorkar/public') ? '/sodai-dorkar/public' : '');
            var payload = {
                event_name: eventName,
                custom_data: customData,
                event_id: eventId,
                url: window.location.href
            };
            var jsonStr = JSON.stringify(payload);
            if (navigator.sendBeacon) {
                var blob = new Blob([jsonStr], { type: 'application/json' });
                navigator.sendBeacon(base + '/api/meta-capi', blob);
            } else {
                fetch(base + '/api/meta-capi', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: jsonStr,
                    keepalive: true
                }).catch(function() {});
            }
        } catch (e) {}
    };
})();

{$pageViewCall}
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id={$pixelId}&ev=PageView&noscript=1"
alt="fb-pixel" /></noscript>
<!-- End Meta Pixel Code -->
HTML;
    }

    /**
     * Alias for sendCapiEvent
     */
    public static function sendServerEvent(string $eventName, array $customData = [], array $userData = [], ?string $eventId = null, ?string $customUrl = null): array
    {
        return self::sendCapiEvent($eventName, $customData, $userData, $eventId, $customUrl);
    }

    /**
     * Send server-side event via Meta Conversions API (CAPI)
     *
     * @param string $eventName Standard event name (Purchase, AddToCart, ViewContent, InitiateCheckout, Lead)
     * @param array $customData e.g. ['value' => 500, 'currency' => 'BDT', 'order_id' => 12]
     * @param array $userData e.g. ['phone' => '017...', 'name' => 'John', 'email' => '...']
     * @param string|null $eventId Optional deduplication ID matching browser pixel eventID
     * @param string|null $customUrl Optional URL where event occurred
     * @return array Result of cURL execution
     */
    public static function sendCapiEvent(string $eventName, array $customData = [], array $userData = [], ?string $eventId = null, ?string $customUrl = null): array
    {
        $pixelId   = self::getPixelId();
        $capiToken = self::getCapiToken();

        if (empty($pixelId) || empty($capiToken)) {
            return ['status' => 'skipped', 'message' => 'Pixel ID or CAPI token not configured'];
        }

        // Prepare user data with SHA-256 hashing as required by Meta
        $formattedUserData = [];

        // Client IP: only send if valid public IP, do NOT send loopback/private (127.0.0.1, ::1)
        $clientIp = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
        if (!empty($clientIp)) {
            if (strpos($clientIp, ',') !== false) {
                $clientIp = trim(explode(',', $clientIp)[0]);
            }
            if (filter_var($clientIp, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                $formattedUserData['client_ip_address'] = $clientIp;
            }
        }

        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            $formattedUserData['client_user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        }

        // FBP and FBC cookies (Essential for Meta Event Match Quality)
        if (!empty($_COOKIE['_fbp'])) {
            $formattedUserData['fbp'] = $_COOKIE['_fbp'];
        }
        if (!empty($_COOKIE['_fbc'])) {
            $formattedUserData['fbc'] = $_COOKIE['_fbc'];
        }

        if (!empty($userData['phone'])) {
            $cleanPhone = preg_replace('/[^0-9]/', '', $userData['phone']);
            // Add Bangladesh country code if missing
            if (strlen($cleanPhone) === 11 && str_starts_with($cleanPhone, '01')) {
                $cleanPhone = '88' . $cleanPhone;
            }
            $formattedUserData['ph'] = [hash('sha256', $cleanPhone)];
        }

        if (!empty($userData['email'])) {
            $cleanEmail = strtolower(trim($userData['email']));
            $formattedUserData['em'] = [hash('sha256', $cleanEmail)];
        }

        if (!empty($userData['name'])) {
            $names = preg_split('/\s+/', trim($userData['name']));
            $firstName = strtolower(trim($names[0]));
            $formattedUserData['fn'] = [hash('sha256', $firstName)];
            if (count($names) > 1) {
                $lastName = strtolower(trim(end($names)));
                $formattedUserData['ln'] = [hash('sha256', $lastName)];
            }
        }

        // Current page URL
        if (!empty($customUrl)) {
            $currentUrl = $customUrl;
        } else {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $uri = $_SERVER['REQUEST_URI'] ?? '/';
            $currentUrl = "{$protocol}://{$host}{$uri}";
        }

        $eventPayload = [
            'event_name'       => $eventName,
            'event_time'       => time(),
            'action_source'    => 'website',
            'event_source_url' => $currentUrl,
            'user_data'        => $formattedUserData
        ];

        if (!empty($customData)) {
            $eventPayload['custom_data'] = $customData;
        }

        if (!empty($eventId)) {
            $eventPayload['event_id'] = $eventId;
        }

        $requestBody = [
            'data' => [$eventPayload]
        ];

        $testCode = self::getTestEventCode();
        if (!empty($testCode)) {
            $requestBody['test_event_code'] = $testCode;
        }

        // Send via cURL to Meta Graph API
        $url = "https://graph.facebook.com/v19.0/{$pixelId}/events?access_token=" . urlencode($capiToken);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 4);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return [
                'status'    => 'error',
                'http_code' => $httpCode,
                'message'   => "cURL Error: {$curlError}"
            ];
        }

        $decoded = json_decode($response, true) ?? [];
        if ($httpCode >= 200 && $httpCode < 300) {
            return [
                'status'    => 'success',
                'http_code' => $httpCode,
                'response'  => $decoded
            ];
        }

        return [
            'status'    => 'error',
            'http_code' => $httpCode,
            'message'   => $decoded['error']['message'] ?? 'Meta API error',
            'response'  => $decoded
        ];
    }

    /**
     * Send a live test event to verify Pixel ID and CAPI token directly into Meta Events Manager
     */
    public static function testConnection(?string $pixelId = null, ?string $token = null, ?string $testCode = null, string $eventName = 'Purchase'): array
    {
        $pixelId  = $pixelId ?: self::getPixelId();
        $token    = $token ?: self::getCapiToken();
        $testCode = $testCode ?: self::getTestEventCode();

        if (empty($pixelId) || empty($token)) {
            return [
                'status'  => 'error',
                'message' => 'Pixel ID ও Conversions API Access Token উভয়ই পূরণ করা আবশ্যক।'
            ];
        }

        // Allowed standard events for Meta testing
        $validStandardEvents = ['PageView', 'Purchase', 'AddToCart', 'ViewContent', 'InitiateCheckout', 'Lead'];
        if (!in_array($eventName, $validStandardEvents, true)) {
            $eventName = 'Purchase';
        }

        $url = "https://graph.facebook.com/v19.0/{$pixelId}/events?access_token=" . urlencode($token);

        $sampleCustomData = [
            'currency'     => 'BDT',
            'value'        => 120.00,
            'content_name' => 'Sodai Dorkar Test Item',
            'content_type' => 'product',
            'num_items'    => 1
        ];

        $eventPayload = [
            'event_name'       => $eventName,
            'event_time'       => time(),
            'action_source'    => 'website',
            'event_source_url' => 'https://' . ($_SERVER['HTTP_HOST'] ?? 'sodaidorkar.com') . '/shop',
            'user_data'        => [
                'client_user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) TestBrowser',
                'ph'                => [hash('sha256', '8801712345678')],
                'em'                => [hash('sha256', 'test@sodaidorkar.com')],
                'fn'                => [hash('sha256', 'test')],
                'ln'                => [hash('sha256', 'customer')]
            ],
            'custom_data'      => $sampleCustomData
        ];

        // Attach public IP if available
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
        if (!empty($ip) && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            $eventPayload['user_data']['client_ip_address'] = $ip;
        }

        $payload = [
            'data' => [$eventPayload]
        ];

        if (!empty($testCode)) {
            $payload['test_event_code'] = $testCode;
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return [
                'status'  => 'error',
                'message' => "সংযোগ ত্রুটি (cURL): {$curlError}"
            ];
        }

        $decoded = json_decode($response, true) ?? [];
        if ($httpCode >= 200 && $httpCode < 300) {
            $recCount = $decoded['events_received'] ?? 1;
            $fbtrace  = $decoded['fbtrace_id'] ?? '';
            
            if (!empty($testCode)) {
                $msg = "🎉 টেস্ট সফল হয়েছে! Meta Events Manager এর 'Test Events' ট্যাবে '{$eventName}' ইভেন্টটি এখনই রিয়েল-টাইম দেখতে পাবেন।";
            } else {
                $msg = "✅ মেটা সার্ভার ইভেন্টটি সফলভাবে গ্রহণ করেছে। তবে Test Event Code না দিলে Events Manager-এর 'Test Events' ট্যাবে লাইভ দেখা যায় না (সরাসরি Overview-তে জমা হয়)। রিয়েল-টাইম টেস্ট দেখতে Test Event Code দিয়ে টেস্ট করুন।";
            }

            return [
                'status'          => 'success',
                'event_name'      => $eventName,
                'events_received' => $recCount,
                'fbtrace_id'      => $fbtrace,
                'has_test_code'   => !empty($testCode),
                'message'         => $msg
            ];
        }

        $errorMsg = $decoded['error']['message'] ?? 'অজানা মেটা এপিআই ত্রুটি।';
        return [
            'status'  => 'error',
            'message' => "Meta API Error ({$httpCode}): {$errorMsg}",
            'details' => $decoded
        ];
    }
}
