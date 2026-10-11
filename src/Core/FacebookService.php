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

    public function isConfigured() {
        return !empty(trim($this->pageId)) && !empty(trim($this->pageAccessToken));
    }

    /**
     * Test connection and retrieve Facebook Page information
     */
    public function verifyConnection() {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'ফেসবুক পেজ আইডি অথবা পেজ অ্যাক্সেস টোকেন দেওয়া হয়নি!'
            ];
        }

        $url = "https://graph.facebook.com/{$this->apiVersion}/" . urlencode($this->pageId) . "?fields=id,name,link,picture,followers_count,is_published&access_token=" . urlencode($this->pageAccessToken);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return [
                'success' => false,
                'message' => 'কানেকশন এরর: ' . $curlError
            ];
        }

        $json = json_decode($response, true);

        if ($httpCode === 200 && !empty($json['id'])) {
            return [
                'success' => true,
                'page_id' => $json['id'],
                'page_name' => $json['name'] ?? 'Facebook Page',
                'link' => $json['link'] ?? "https://facebook.com/{$json['id']}",
                'picture' => $json['picture']['data']['url'] ?? null,
                'followers' => $json['followers_count'] ?? 0,
                'message' => "সফলভাবে ফেসবুক পেজ '{$json['name']}' এর সাথে কানেক্ট হয়েছে!"
            ];
        }

        $err = $json['error']['message'] ?? "ফেসবুক API এরর (HTTP {$httpCode})";
        return [
            'success' => false,
            'message' => 'ফেসবুক সংযোগ ব্যর্থ: ' . $err
        ];
    }

    /**
     * Post a photo with caption directly to Facebook Page
     * 
     * @param string $caption
     * @param string $imagePath Local absolute or relative path, or URL
     * @return array
     */
    public function postPhoto(string $caption, string $imagePath) {
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
     * Generate Facebook OAuth Login URL for Page Management
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

