<?php

namespace Core;

use Models\Setting;

class FacebookService {
    protected $pageId;
    protected $pageAccessToken;
    protected $apiVersion = 'v19.0';

    public function __construct($pageId = null, $pageAccessToken = null) {
        $this->pageId = $pageId ?: Setting::getValue('facebook_page_id', '');
        $this->pageAccessToken = $pageAccessToken ?: Setting::getValue('facebook_page_access_token', '');
    }

    /**
     * Check if Facebook Page ID and Access Token are configured
     */
    public function isConfigured(): bool {
        return !empty(trim($this->pageId)) && !empty(trim($this->pageAccessToken));
    }

    /**
     * Ensure we have a genuine Page Access Token, converting from User Token if necessary
     */
    public function resolvePageAccessToken(?string $inputToken = null): ?string {
        $token = trim((string)($inputToken ?: $this->pageAccessToken));
        $pageId = trim((string)$this->pageId);

        if (empty($token) || empty($pageId)) {
            return null;
        }

        // 1. Check if token is already a direct Page Access Token (/me)
        $meRes = $this->httpGet("https://graph.facebook.com/{$this->apiVersion}/me?fields=id,name&access_token=" . urlencode($token));
        if ($meRes['success'] && !empty($meRes['data']['id'])) {
            if ((string)$meRes['data']['id'] === (string)$pageId) {
                return $token;
            }
        }

        // 2. Try fetching the Page Access Token directly via /{pageId}?fields=access_token
        $directRes = $this->httpGet("https://graph.facebook.com/{$this->apiVersion}/" . urlencode($pageId) . "?fields=access_token,name,picture&access_token=" . urlencode($token));
        if ($directRes['success'] && !empty($directRes['data']['access_token'])) {
            $realPageToken = $directRes['data']['access_token'];
            $this->pageAccessToken = $realPageToken;
            Setting::set('facebook_page_access_token', $realPageToken);
            if (!empty($directRes['data']['name'])) {
                Setting::set('facebook_page_name', $directRes['data']['name']);
            }
            if (!empty($directRes['data']['picture']['data']['url'])) {
                Setting::set('facebook_page_picture', $directRes['data']['picture']['data']['url']);
            }
            return $realPageToken;
        }

        // 3. Try fetching managed pages via /me/accounts
        $accountsRes = $this->httpGet("https://graph.facebook.com/{$this->apiVersion}/me/accounts?fields=id,name,access_token,picture&limit=100&access_token=" . urlencode($token));
        if ($accountsRes['success'] && !empty($accountsRes['data']['data'])) {
            foreach ($accountsRes['data']['data'] as $page) {
                if ((string)$page['id'] === (string)$pageId && !empty($page['access_token'])) {
                    $realPageToken = $page['access_token'];
                    $this->pageAccessToken = $realPageToken;
                    Setting::set('facebook_page_access_token', $realPageToken);
                    if (!empty($page['name'])) {
                        Setting::set('facebook_page_name', $page['name']);
                    }
                    if (!empty($page['picture']['data']['url'])) {
                        Setting::set('facebook_page_picture', $page['picture']['data']['url']);
                    }
                    return $realPageToken;
                }
            }
        }

        return null;
    }

    /**
     * Format Graph API error into clear actionable Bengali message
     */
    protected function formatErrorMessage(array $json, int $httpCode = 0): string {
        $msg = $json['error']['message'] ?? "মেটা সার্ভার ত্রুটি (HTTP {$httpCode})";
        $code = $json['error']['code'] ?? 0;

        if ($code == 283 || str_contains($msg, 'pages_read_engagement')) {
            return "❌ মেটা পারমিশন এরর (#283): আপনার অ্যাক্সেস টোকেনে 'pages_read_engagement' পারমিশন নেই।\n👉 সমাধান: Graph API Explorer-এ Permissions অপশন থেকে 'pages_read_engagement' ও 'pages_manage_posts' যুক্ত করুন এবং 'User or Page' ড্রপডাউনে আপনার পেজটি নির্বাচন করে Page Access Token কপি করে পেস্ট করুন।";
        }

        if ($code == 200 || str_contains($msg, 'Permissions error') || str_contains($msg, 'pages_manage_posts')) {
            return "❌ পারমিশন এরর (#{$code}): পেজে পোস্ট করার জন্য 'pages_manage_posts' এবং 'pages_read_engagement' পারমিশন প্রয়োজন। Graph API Explorer থেকে পেজের আসল Page Access Token কপি করে পেস্ট করুন।";
        }

        if ($code == 190) {
            return "❌ অ্যাক্সেস টোকেনের মেয়াদ শেষ হয়ে গেছে (#190)। অনুগ্রহ করে Graph API Explorer থেকে নতুন টোকেন সংগ্রহ করে পেস্ট করুন।";
        }

        return "❌ ফেসবুক ত্রুটি (#{$code}): {$msg}";
    }

    /**
     * Test connection and retrieve Facebook Page information directly from Meta
     */
    public function verifyConnection(?string $pageId = null, ?string $token = null): array {
        $pageId = trim((string)($pageId ?: $this->pageId));
        $token = trim((string)($token ?: $this->pageAccessToken));

        if (empty($pageId) || empty($token)) {
            return [
                'success' => false,
                'message' => 'ফেসবুক পেজ আইডি অথবা পেজ অ্যাক্সেস টোকেন দেওয়া হয়নি!'
            ];
        }

        $this->pageId = $pageId;

        // 1. Check if token is already a direct Page Access Token (/me)
        $meRes = $this->httpGet("https://graph.facebook.com/{$this->apiVersion}/me?fields=id,name,link,picture&access_token=" . urlencode($token));
        if ($meRes['success'] && !empty($meRes['data']['id'])) {
            $meData = $meRes['data'];
            if ((string)$meData['id'] === (string)$pageId) {
                return [
                    'success' => true,
                    'page_id' => $meData['id'],
                    'page_name' => $meData['name'] ?? 'Facebook Page',
                    'link' => $meData['link'] ?? "https://facebook.com/{$meData['id']}",
                    'picture' => $meData['picture']['data']['url'] ?? null,
                    'message' => "সফলভাবে ফেসবুক পেজ '{$meData['name']}' এর সাথে কানেক্ট হয়েছে! (Page Access Token সক্রিয়)"
                ];
            }
        }

        // 2. Try auto-converting from User Token to genuine Page Access Token
        $resolvedToken = $this->resolvePageAccessToken($token);
        if ($resolvedToken) {
            $pageName = Setting::getValue('facebook_page_name', 'Facebook Page');
            $pagePic = Setting::getValue('facebook_page_picture', null);
            return [
                'success' => true,
                'page_id' => $pageId,
                'page_name' => $pageName,
                'picture' => $pagePic,
                'page_access_token' => $resolvedToken,
                'auto_converted' => true,
                'message' => "সফলভাবে পেজ '{$pageName}' এর সাথে কানেক্ট হয়েছে! (User Token থেকে স্বয়ংক্রিয়ভাবে আসল Page Token সক্রিয় করা হয়েছে)"
            ];
        }

        // 3. If conversion failed, check permissions
        $permRes = $this->httpGet("https://graph.facebook.com/{$this->apiVersion}/me/permissions?access_token=" . urlencode($token));
        $grantedPerms = [];
        if (!empty($permRes['data']['data'])) {
            foreach ($permRes['data']['data'] as $p) {
                if (($p['status'] ?? '') === 'granted') {
                    $grantedPerms[] = $p['permission'];
                }
            }
        }

        $missingPerms = [];
        if (!in_array('pages_manage_posts', $grantedPerms)) $missingPerms[] = 'pages_manage_posts';
        if (!in_array('pages_read_engagement', $grantedPerms)) $missingPerms[] = 'pages_read_engagement';

        if (!empty($missingPerms) && !empty($grantedPerms)) {
            $missingList = implode(', ', $missingPerms);
            return [
                'success' => false,
                'message' => "❌ মেটা পারমিশন অনুপস্থিত ({$missingList}): Graph API Explorer-এ Permissions অপশনে pages_manage_posts এবং pages_read_engagement যোগ করে 'User or Page' ড্রপডাউনে আপনার পেজটি নির্বাচন করে টোকেন নিন।"
            ];
        }

        return [
            'success' => false,
            'message' => "❌ এই ফেসবুক আইডি দিয়ে পেজ (ID: {$pageId}) অ্যাক্সেস করা সম্ভব হয়নি। Graph API Explorer-এ Permissions অপশনে pages_manage_posts ও pages_read_engagement যোগ করে 'User or Page' ড্রপডাউন থেকে আপনার পেজটি (Sohozsodai.com) নির্বাচন করে টোকেন কপি করুন।"
        ];
    }

    /**
     * Post a photo with caption directly to Facebook Page via Meta Graph API
     * 
     * @param string $caption
     * @param string $imagePath Local absolute or relative path, or URL
     * @return array
     */
    public function postPhoto(string $caption, string $imagePath): array {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'ফেসবুক পেজ আইডি এবং অ্যাক্সেস টোকেন কনফিগার করা নেই।'
            ];
        }

        // Auto-resolve genuine Page Access Token if token is User token
        $resolvedToken = $this->resolvePageAccessToken();
        $tokenToUse = $resolvedToken ?: $this->pageAccessToken;

        $url = "https://graph.facebook.com/{$this->apiVersion}/" . urlencode($this->pageId) . "/photos";

        // Resolve absolute local file path if available
        $resolvedFile = null;
        if (!str_starts_with($imagePath, 'http://') && !str_starts_with($imagePath, 'https://')) {
            $baseDir = dirname(__DIR__, 2);
            $clean = ltrim($imagePath, '/\\');
            if (file_exists($imagePath)) {
                $resolvedFile = $imagePath;
            } elseif (file_exists($baseDir . '/public/' . $clean)) {
                $resolvedFile = $baseDir . '/public/' . $clean;
            } elseif (file_exists($baseDir . '/' . $clean)) {
                $resolvedFile = $baseDir . '/' . $clean;
            }
        }

        $postFields = [
            'message' => $caption,
            'access_token' => $tokenToUse
        ];

        if ($resolvedFile && file_exists($resolvedFile)) {
            $mime = mime_content_type($resolvedFile) ?: 'image/png';
            $postFields['source'] = new \CURLFile($resolvedFile, $mime, basename($resolvedFile));
        } else {
            // Treat as public URL
            $postFields['url'] = $imagePath;
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, 'SodaiDorkar/1.0 (Meta Graph API)');

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return [
                'success' => false,
                'message' => 'ফেসবুক আপলোড এরর: ' . $curlError
            ];
        }

        $json = json_decode($response, true) ?: [];

        if ($httpCode === 200 && (!empty($json['id']) || !empty($json['post_id']))) {
            $postId = $json['post_id'] ?? $json['id'];
            $postUrl = "https://facebook.com/" . urlencode($postId);
            return [
                'success' => true,
                'post_id' => $postId,
                'post_url' => $postUrl,
                'message' => 'ফেসবুকে সফলভাবে পোস্ট করা হয়েছে!'
            ];
        }

        return [
            'success' => false,
            'message' => $this->formatErrorMessage($json, $httpCode),
            'response' => $json
        ];
    }

    /**
     * Post text / status feed directly to Facebook Page
     */
    public function postFeed(string $message, ?string $link = null): array {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'ফেসবুক পেজ আইডি এবং অ্যাক্সেস টোকেন কনফিগার করা নেই।'
            ];
        }

        // Auto-resolve genuine Page Access Token if token is User token
        $resolvedToken = $this->resolvePageAccessToken();
        $tokenToUse = $resolvedToken ?: $this->pageAccessToken;

        $url = "https://graph.facebook.com/{$this->apiVersion}/" . urlencode($this->pageId) . "/feed";

        $postFields = [
            'message' => $message,
            'access_token' => $tokenToUse
        ];

        // Only attach link if it's a valid public external URL (never localhost/127.0.0.1)
        if (!empty($link) && !str_contains($link, 'localhost') && !str_contains($link, '127.0.0.1')) {
            $postFields['link'] = $link;
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, 'SodaiDorkar/1.0 (Meta Graph API)');

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return [
                'success' => false,
                'message' => 'ফেসবুক পোস্ট এরর: ' . $curlError
            ];
        }

        $json = json_decode($response, true) ?: [];

        if ($httpCode === 200 && (!empty($json['id']) || !empty($json['post_id']))) {
            $postId = $json['post_id'] ?? $json['id'];
            $postUrl = "https://facebook.com/" . urlencode($postId);
            return [
                'success' => true,
                'post_id' => $postId,
                'post_url' => $postUrl,
                'message' => 'ফেসবুকে সফলভাবে পোস্ট পাবলিশ করা হয়েছে!'
            ];
        }

        return [
            'success' => false,
            'message' => $this->formatErrorMessage($json, $httpCode),
            'response' => $json
        ];
    }

    /**
     * Smart Publisher: publishes with photo if image is available, otherwise publishes text status feed
     */
    public function publishPost(string $caption, ?string $imagePath = null): array {
        if (!empty(trim((string)$imagePath))) {
            return $this->postPhoto($caption, $imagePath);
        }
        return $this->postFeed($caption);
    }

    /**
     * Publish a quick test post to verify posting capability directly on Facebook Page
     */
    public function testPost(string $customMessage = ''): array {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'ফেসবুক পেজ আইডি অথবা অ্যাক্সেস টোকেন কনফিগার করা নেই!'
            ];
        }

        $pageName = Setting::getValue('facebook_page_name', 'আমাদের পেজ');
        $timeStr = date('d M Y, h:i A');

        $message = !empty($customMessage) 
            ? $customMessage 
            : "🎉 টেস্ট পোস্ট: সদাই দরকার (Sodai Dorkar) ফেসবুক অটোমেশন সংযোগ সফল হয়েছে!\n\nপেজ: {$pageName}\nসময়: {$timeStr}";

        // Post clean status message (do not attach localhost URL)
        return $this->postFeed($message);
    }

    /**
     * Generate Facebook OAuth Login URL (Direct Self-Hosted)
     */
    public function getOAuthUrl(string $redirectUri, string $appId = ''): string {
        $appId = $appId ?: Setting::getValue('facebook_app_id', '');
        $scope = urlencode('pages_show_list,pages_read_engagement,pages_manage_posts');
        $redirect = urlencode($redirectUri);
        return "https://www.facebook.com/{$this->apiVersion}/dialog/oauth?client_id={$appId}&redirect_uri={$redirect}&scope={$scope}&response_type=code";
    }

    /**
     * Exchange authorization code for User Access Token and fetch managed Pages
     */
    public function handleOAuthCallback(string $code, string $redirectUri, string $appId = '', string $appSecret = ''): array {
        $appId = $appId ?: Setting::getValue('facebook_app_id', '');
        $appSecret = $appSecret ?: Setting::getValue('facebook_app_secret', '');

        if (empty($appId) || empty($appSecret)) {
            return [
                'success' => false,
                'message' => 'Facebook App ID এবং App Secret কনফিগার করা নেই।'
            ];
        }

        // 1. Get Short-lived User Access Token
        $tokenUrl = "https://graph.facebook.com/{$this->apiVersion}/oauth/access_token?" . http_build_query([
            'client_id' => $appId,
            'redirect_uri' => $redirectUri,
            'client_secret' => $appSecret,
            'code' => $code
        ]);

        $res = $this->httpGet($tokenUrl);
        if (!$res['success']) {
            return $res;
        }

        $userToken = $res['data']['access_token'] ?? null;
        if (!$userToken) {
            return [
                'success' => false,
                'message' => 'ইউজার টোকেন পাওয়া যায়নি: ' . ($res['data']['error']['message'] ?? 'অজানা ত্রুটি')
            ];
        }

        // 2. Exchange for Long-lived User Token (60 days)
        $longLivedToken = $this->exchangeLongLivedUserToken($userToken, $appId, $appSecret) ?: $userToken;

        // 3. Fetch all Facebook Pages managed by this user
        $pages = $this->getUserPages($longLivedToken);

        return [
            'success' => true,
            'user_token' => $longLivedToken,
            'pages' => $pages
        ];
    }

    /**
     * Exchange short-lived token for long-lived User Access Token
     */
    public function exchangeLongLivedUserToken(string $shortToken, string $appId, string $appSecret): ?string {
        $url = "https://graph.facebook.com/{$this->apiVersion}/oauth/access_token?" . http_build_query([
            'grant_type' => 'fb_exchange_token',
            'client_id' => $appId,
            'client_secret' => $appSecret,
            'fb_exchange_token' => $shortToken
        ]);

        $res = $this->httpGet($url);
        return $res['success'] ? ($res['data']['access_token'] ?? null) : null;
    }

    /**
     * Fetch all Facebook Pages administered by this User Token
     */
    public function getUserPages(string $userAccessToken): array {
        $url = "https://graph.facebook.com/{$this->apiVersion}/me/accounts?" . http_build_query([
            'fields' => 'id,name,access_token,category,picture{url},tasks',
            'limit' => 100,
            'access_token' => $userAccessToken
        ]);

        $res = $this->httpGet($url);
        if (!$res['success']) {
            return [];
        }

        $pages = [];
        foreach ($res['data']['data'] ?? [] as $item) {
            $pages[] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'access_token' => $item['access_token'],
                'category' => $item['category'] ?? 'Facebook Page',
                'picture' => $item['picture']['data']['url'] ?? null
            ];
        }

        return $pages;
    }

    /**
     * HTTP GET Helper
     */
    protected function httpGet(string $url): array {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return ['success' => false, 'message' => "cURL Error: {$curlError}"];
        }

        $json = json_decode($response, true);
        if ($httpCode >= 200 && $httpCode < 300) {
            return ['success' => true, 'data' => $json];
        }

        $errMsg = $json['error']['message'] ?? "HTTP Error {$httpCode}";
        return ['success' => false, 'message' => $errMsg, 'data' => $json];
    }
}
