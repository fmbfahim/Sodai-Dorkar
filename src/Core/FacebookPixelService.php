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
        return (!empty($cfg['facebook_pixel_enabled']) && $cfg['facebook_pixel_enabled'] === '1' && !empty($cfg['facebook_pixel_id']));
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
        return trim(self::getSettings()['facebook_capi_test_code'] ?? '');
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
        $trackPageView = (self::getSettings()['facebook_pixel_track_pageview'] ?? '1') === '1';

        $pageViewCode = $trackPageView ? "fbq('track', 'PageView');" : "";

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
{$pageViewCode}
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id={$pixelId}&ev=PageView&noscript=1"
alt="fb-pixel" /></noscript>
<!-- End Meta Pixel Code -->
HTML;
    }

    /**
     * Send server-side event via Meta Conversions API (CAPI)
     *
     * @param string $eventName Standard event name (Purchase, AddToCart, ViewContent, InitiateCheckout, Lead)
     * @param array $customData e.g. ['value' => 500, 'currency' => 'BDT', 'order_id' => 12]
     * @param array $userData e.g. ['phone' => '017...', 'name' => 'John', 'email' => '...']
     * @param string|null $eventId Optional deduplication ID matching browser pixel eventID
     * @return array Result of cURL execution
     */
    public static function sendCapiEvent(string $eventName, array $customData = [], array $userData = [], ?string $eventId = null): array
    {
        $pixelId   = self::getPixelId();
        $capiToken = self::getCapiToken();

        if (empty($pixelId) || empty($capiToken)) {
            return ['status' => 'skipped', 'message' => 'Pixel ID or CAPI token not configured'];
        }

        // Prepare user data with SHA-256 hashing as required by Meta
        $formattedUserData = [
            'client_ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'client_user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Mozilla/5.0'
        ];

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
            $names = explode(' ', trim($userData['name']));
            $firstName = strtolower(trim($names[0]));
            $formattedUserData['fn'] = [hash('sha256', $firstName)];
            if (count($names) > 1) {
                $lastName = strtolower(trim(end($names)));
                $formattedUserData['ln'] = [hash('sha256', $lastName)];
            }
        }

        // Current page URL
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $currentUrl = "{$protocol}://{$host}{$uri}";

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
        curl_setopt($ch, CURLOPT_TIMEOUT, 6);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // compatibility for local environments

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
     * Send a live test event to verify Pixel ID and CAPI token
     */
    public static function testConnection(?string $pixelId = null, ?string $token = null, ?string $testCode = null): array
    {
        $pixelId = $pixelId ?: self::getPixelId();
        $token   = $token ?: self::getCapiToken();
        $testCode = $testCode ?: self::getTestEventCode();

        if (empty($pixelId) || empty($token)) {
            return [
                'status'  => 'error',
                'message' => 'Pixel ID ও Conversions API Access Token উভয়ই পূরণ করা আবশ্যক।'
            ];
        }

        $url = "https://graph.facebook.com/v19.0/{$pixelId}/events?access_token=" . urlencode($token);

        $payload = [
            'data' => [
                [
                    'event_name'       => 'TestEvent',
                    'event_time'       => time(),
                    'action_source'    => 'website',
                    'event_source_url' => 'https://' . ($_SERVER['HTTP_HOST'] ?? 'sodai-dorkar.local') . '/admin/settings',
                    'user_data'        => [
                        'client_ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                        'client_user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Mozilla/5.0 Test Agent',
                        'ph'                => [hash('sha256', '8801700000000')]
                    ],
                    'custom_data'      => [
                        'currency' => 'BDT',
                        'value'    => 100.00
                    ]
                ]
            ]
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
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);
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
            return [
                'status'          => 'success',
                'events_received' => $decoded['events_received'] ?? 1,
                'fbtrace_id'      => $decoded['fbtrace_id'] ?? '',
                'message'         => 'Meta Conversions API সফলভাবে সংযুক্ত হয়েছে! টেস্ট ইভেন্ট ফেসবুক সফলভাবে গ্রহণ করেছে।'
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
