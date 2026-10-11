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

        // Helper curl function
        $fetchGraph = function(string $endpoint, array $params = []) use ($token) {
            $params['access_token'] = $token;
            $url = "https://graph.facebook.com/{$this->apiVersion}/" . ltrim($endpoint, '/') . '?' . http_build_query($params);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_USERAGENT, 'SodaiDorkar/1.0 (Meta Graph API)');

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            return [
                'code' => $httpCode,
                'error' => $curlError,
                'data' => json_decode($response, true) ?: []
            ];
        };

        // 1. First, check if the token itself is already a direct Page Access Token (/me)
        $meRes = $fetchGraph('me', ['fields' => 'id,name,link,picture']);
        if ($meRes['code'] === 200 && !empty($meRes['data']['id'])) {
            $meData = $meRes['data'];
            // If /me ID matches the requested pageId, it's definitely a valid Page Access Token!
            if ((string)$meData['id'] === (string)$pageId) {
                return [
                    'success' => true,
                    'page_id' => $meData['id'],
                    'page_name' => $meData['name'] ?? 'Facebook Page',
                    'link' => $meData['link'] ?? "https://facebook.com/{$meData['id']}",
                    'picture' => $meData['picture']['data']['url'] ?? null,
                    'message' => "সফলভাবে ফেসবুক পেজ '{$meData['name']}' এর সাথে কানেক্ট হয়েছে!"
                ];
            }
        }

        // 2. Try fetching the Page directly by ID (/pageId) with only safe fields (id, name, link, picture)
        // (Do NOT include followers_count as that requires 'Page Public Metadata Access' app review)
        $pageRes = $fetchGraph($pageId, ['fields' => 'id,name,link,picture']);
        if ($pageRes['code'] === 200 && !empty($pageRes['data']['id'])) {
            $pageData = $pageRes['data'];
            return [
                'success' => true,
                'page_id' => $pageData['id'],
                'page_name' => $pageData['name'] ?? 'Facebook Page',
                'link' => $pageData['link'] ?? "https://facebook.com/{$pageData['id']}",
                'picture' => $pageData['picture']['data']['url'] ?? null,
                'message' => "সফলভাবে ফেসবুক পেজ '{$pageData['name']}' এর সাথে কানেক্ট হয়েছে!"
            ];
        }

        // 3. If direct page access failed, maybe the user provided a User Access Token instead of Page Token!
        // Check /me/accounts to see if this user manages the target page
        $accountsRes = $fetchGraph('me/accounts', ['fields' => 'id,name,access_token,picture', 'limit' => 50]);
        if ($accountsRes['code'] === 200 && !empty($accountsRes['data']['data'])) {
            foreach ($accountsRes['data']['data'] as $account) {
                if ((string)$account['id'] === (string)$pageId && !empty($account['access_token'])) {
                    // Found the real Page Access Token!
                    $actualPageToken = $account['access_token'];
                    Setting::set('facebook_page_access_token', $actualPageToken);
                    Setting::set('facebook_page_name', $account['name'] ?? 'Facebook Page');
                    if (!empty($account['picture']['data']['url'])) {
                        Setting::set('facebook_page_picture', $account['picture']['data']['url']);
                    }
                    $this->pageAccessToken = $actualPageToken;

                    return [
                        'success' => true,
                        'page_id' => $account['id'],
                        'page_name' => $account['name'] ?? 'Facebook Page',
                        'picture' => $account['picture']['data']['url'] ?? null,
                        'page_access_token' => $actualPageToken,
                        'auto_converted' => true,
                        'message' => "সফলভাবে পেজ '{$account['name']}' এর সাথে কানেক্ট হয়েছে! (User Token থেকে স্বয়ংক্রিয়ভাবে আসল Page Token নেওয়া হয়েছে)"
                    ];
                }
            }
        }

        // 4. If still failed, inspect the token permissions to explain EXACTLY what's missing
        $permRes = $fetchGraph('me/permissions');
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
                'message' => "আপনার অ্যাক্সেস টোকেনে পেজের পারমিশন অনুপস্থিত ({$missingList})। Graph API Explorer-এ Permissions সেকশনে গিয়ে pages_manage_posts এবং pages_read_engagement যোগ করে আবার Generate Access Token বাটনে চাপুন।"
            ];
        }

        // Return error message from Meta
        $err = $pageRes['data']['error']['message'] ?? $meRes['data']['error']['message'] ?? ($pageRes['error'] ?: 'মেটা গ্রাফ এপিআই কানেক্ট করা যায়নি। পেজ আইডি বা টোকেন সঠিক কিনা যাচাই করুন।');
        return [
            'success' => false,
            'message' => 'ফেসবুক সংযোগ ব্যর্থ: ' . $err
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
            'access_token' => $this->pageAccessToken
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

        $json = json_decode($response, true);

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

        $err = $json['error']['message'] ?? "ফেসবুক প্রকাশনা ব্যর্থ (HTTP {$httpCode})";
        return [
            'success' => false,
            'message' => $err,
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

        $url = "https://graph.facebook.com/{$this->apiVersion}/" . urlencode($this->pageId) . "/feed";

        $postFields = [
            'message' => $message,
            'access_token' => $this->pageAccessToken
        ];

        if (!empty($link)) {
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

        $json = json_decode($response, true);

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

        $err = $json['error']['message'] ?? "ফেসবুক প্রকাশনা ব্যর্থ (HTTP {$httpCode})";
        return [
            'success' => false,
            'message' => $err,
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
        $siteUrl = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . ($_SERVER['HTTP_HOST'] ?? 'localhost');
        $timeStr = date('d M Y, h:i A');

        $message = !empty($customMessage) 
            ? $customMessage 
            : "🛒 এটি একটি টেস্ট পোস্ট ({$timeStr})।\n\n🎉 অভিনন্দন! {$pageName}-এর সাথে আমাদের ওয়েবসাইট সরাসরি Meta Graph API-এর মাধ্যমে সফলভাবে যুক্ত হয়েছে।\n\nএখন থেকে প্রতিদিনের আকর্ষণীয় অফার ও বাজার আপডেট স্বয়ংক্রিয়ভাবে এই পেজে প্রকাশিত হবে।\n\n🔗 ওয়েবসাইট ভিজিট করুন: {$siteUrl}";

        return $this->postFeed($message, $siteUrl);
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
