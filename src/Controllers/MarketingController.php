<?php

namespace Controllers;

use Core\Controller;
use Core\Auth;
use Core\GeminiService;
use Core\MarketingTemplateService;
use Core\FacebookService;
use Models\MarketingPost;
use Models\Product;
use Models\Setting;

class MarketingController extends Controller {

    protected function requireAdmin() {
        if (!Auth::check()) {
            $_SESSION['error'] = 'অনুগ্রহ করে লগইন করুন।';
            $this->redirect('/login');
        }
        if (!Auth::can('settings') && !Auth::isAdmin()) {
            $_SESSION['error'] = 'আপনার এই পেজে প্রবেশের অনুমতি নেই।';
            $this->redirect('/admin/dashboard');
        }
    }

    /**
     * Marketing Dashboard & Today's 3 Posts View
     */
    public function index() {
        $this->requireAdmin();

        $postModel = new MarketingPost();
        $todayPosts = $postModel->getTodayPosts();
        $recentPosts = $postModel->getRecentPosts(15);

        // Fetch products with images for selection
        $productModel = new Product();
        $allProducts = $productModel->all(['limit' => 100]);

        $gemini = new GeminiService();
        $facebook = new FacebookService();

        $settings = Setting::getAll();

        return $this->view('admin/marketing/index', [
            'title' => 'Facebook Marketing Automation',
            'todayPosts' => $todayPosts,
            'recentPosts' => $recentPosts,
            'products' => $allProducts,
            'isGeminiConfigured' => $gemini->isConfigured(),
            'isFacebookConfigured' => $facebook->isConfigured(),
            'settings' => $settings
        ]);
    }

    /**
     * Marketing Settings View
     */
    public function settings() {
        $this->requireAdmin();

        $gemini = new GeminiService();
        $facebook = new FacebookService();
        $settings = Setting::getAll();

        return $this->view('admin/marketing/settings', [
            'title' => 'Marketing & Facebook Settings',
            'settings' => $settings,
            'isGeminiConfigured' => $gemini->isConfigured(),
            'isFacebookConfigured' => $facebook->isConfigured(),
            'oauthPages' => $_SESSION['fb_oauth_pages'] ?? []
        ]);
    }

    /**
     * Save Marketing Configuration
     */
    public function saveSettings() {
        $this->requireAdmin();

        $keys = [
            'gemini_api_key',
            'gemini_model',
            'facebook_app_id',
            'facebook_app_secret',
            'facebook_page_id',
            'facebook_page_name',
            'facebook_page_picture',
            'facebook_page_access_token',
            'facebook_auto_post_enabled',
            'marketing_morning_time',
            'marketing_afternoon_time',
            'marketing_evening_time',
            'marketing_default_mode',
            'marketing_cron_secret'
        ];

        foreach ($keys as $k) {
            $val = trim($_POST[$k] ?? '');
            if ($k === 'facebook_auto_post_enabled') {
                $val = !empty($_POST[$k]) ? '1' : '0';
            }
            Setting::set($k, $val);
        }

        $_SESSION['success'] = 'মার্কেটিং ও ফেসবুক সেটিংস সফলভাবে সংরক্ষণ করা হয়েছে!';
        $this->redirect('/admin/marketing/settings');
    }

    /**
     * Generate Today's 3 Facebook Posts (Morning, Afternoon, Evening)
     */
    public function generateDaily() {
        $this->requireAdmin();

        $requestedMode = $_POST['mode'] ?? Setting::getValue('marketing_default_mode', 'template_no_ai');
        $date = $_POST['date'] ?? date('Y-m-d');

        $productModel = new Product();
        $products = $productModel->all(['limit' => 50]);

        // Filter products with valid images
        $eligible = array_filter($products, function($p) {
            return !empty($p['name']) && floatval($p['sell_price']) > 0;
        });

        if (count($eligible) < 3) {
            $eligible = $products;
        }

        if (empty($eligible)) {
            $_SESSION['error'] = 'পোস্ট তৈরি করার জন্য সিস্টেমে কোনো পণ্য পাওয়া যায়নি!';
            $this->redirect('/admin/marketing');
            return;
        }

        // Shuffle to get varied products
        shuffle($eligible);
        $selectedProducts = array_slice($eligible, 0, 3);

        $slots = [
            [
                'slot_type' => 'morning',
                'time' => Setting::getValue('marketing_morning_time', '09:00'),
                'product' => $selectedProducts[0] ?? $eligible[0]
            ],
            [
                'slot_type' => 'afternoon',
                'time' => Setting::getValue('marketing_afternoon_time', '14:00'),
                'product' => $selectedProducts[1] ?? $eligible[0]
            ],
            [
                'slot_type' => 'evening',
                'time' => Setting::getValue('marketing_evening_time', '19:00'),
                'product' => $selectedProducts[2] ?? $eligible[0]
            ]
        ];

        $postModel = new MarketingPost();
        $gemini = new GeminiService();
        $isGeminiReady = $gemini->isConfigured();

        $createdCount = 0;
        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';

        foreach ($slots as $slot) {
            $prod = $slot['product'];
            $slotType = $slot['slot_type'];

            // Generate caption & headline
            $caption = '';
            $headline = '';
            $actualMode = $requestedMode;

            if ($requestedMode === 'ai_gemini' && $isGeminiReady) {
                $captionRes = $gemini->generateCaption($prod, $slotType);
                if ($captionRes['success']) {
                    $caption = $captionRes['text'];
                    $headline = $gemini->generateBannerHeadline($prod, $slotType);
                } else {
                    // Fallback to No-AI template
                    $actualMode = 'template_no_ai';
                    $caption = MarketingTemplateService::generateCaption($prod, $slotType);
                    $headline = MarketingTemplateService::getBannerHeadline($prod, $slotType);
                }
            } else {
                $actualMode = 'template_no_ai';
                $caption = MarketingTemplateService::generateCaption($prod, $slotType);
                $headline = MarketingTemplateService::getBannerHeadline($prod, $slotType);
            }

            // Image path fallback to product image
            $imagePath = $prod['image_path'] ?? null;
            if (!empty($imagePath)) {
                $imagePath = Product::getImageUrl($imagePath, $base);
            }

            // Check if post already exists for this slot & date
            $existing = (new \Core\Database(require __DIR__ . '/../../config/database.php'))->query(
                "SELECT id FROM marketing_posts WHERE post_date = :date AND slot_type = :slot LIMIT 1",
                ['date' => $date, 'slot' => $slotType]
            )->fetch();

            if ($existing) {
                $postModel->update($existing['id'], [
                    'product_id' => $prod['id'],
                    'schedule_time' => $slot['time'],
                    'mode' => $actualMode,
                    'headline' => $headline,
                    'caption' => $caption,
                    'image_path' => $imagePath,
                    'status' => 'draft'
                ]);
            } else {
                $postModel->create([
                    'product_id' => $prod['id'],
                    'slot_type' => $slotType,
                    'post_date' => $date,
                    'schedule_time' => $slot['time'],
                    'mode' => $actualMode,
                    'headline' => $headline,
                    'caption' => $caption,
                    'image_path' => $imagePath,
                    'status' => 'draft'
                ]);
            }
            $createdCount++;
        }

        $modeLabel = ($requestedMode === 'ai_gemini') ? 'Gemini AI' : 'Smart No-AI Template';
        $_SESSION['success'] = "আজকের ৩টি পোস্ট ({$modeLabel} পদ্ধতিতে) সফলভাবে তৈরি করা হয়েছে!";
        $this->redirect('/admin/marketing');
    }

    /**
     * API: Generate Caption and Headline on demand
     */
    public function apiGenerateContent() {
        $this->requireAdmin();
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');

        $productId = intval($_POST['product_id'] ?? 0);
        $slotType = $_POST['slot_type'] ?? 'morning';
        $mode = $_POST['mode'] ?? 'template_no_ai';
        $customNotes = trim($_POST['custom_notes'] ?? '');

        $productModel = new Product();
        $product = $productModel->find($productId);

        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'পণ্যটি খুঁজে পাওয়া যায়নি!']);
            exit;
        }

        $caption = '';
        $headline = '';

        if ($mode === 'ai_gemini') {
            $gemini = new GeminiService();
            if (!$gemini->isConfigured()) {
                echo json_encode(['success' => false, 'message' => 'Gemini API Key সেট করা নেই! দয়া করে সেটিংস পেজে আপনার API Key দিন।']);
                exit;
            }
            $capRes = $gemini->generateCaption($product, $slotType, $customNotes);
            if (!$capRes['success']) {
                echo json_encode(['success' => false, 'message' => $capRes['message']]);
                exit;
            }
            $caption = $capRes['text'];
            $headline = $gemini->generateBannerHeadline($product, $slotType);
        } else {
            $caption = MarketingTemplateService::generateCaption($product, $slotType);
            $headline = MarketingTemplateService::getBannerHeadline($product, $slotType);
        }

        echo json_encode([
            'success' => true,
            'headline' => $headline,
            'caption' => $caption,
            'mode' => $mode
        ]);
        exit;
    }

    /**
     * API: Save Generated Canvas Banner Image to Server
     */
    public function apiSaveBanner() {
        $this->requireAdmin();
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');

        $postId = intval($_POST['post_id'] ?? 0);
        $base64 = $_POST['image_base64'] ?? '';

        if (empty($base64) || !str_contains($base64, 'base64,')) {
            echo json_encode(['success' => false, 'message' => 'সঠিক ইমেজ ডাটা পাওয়া যায়নি!']);
            exit;
        }

        list(, $data) = explode('base64,', $base64);
        $imageData = base64_decode($data);

        if (!$imageData) {
            echo json_encode(['success' => false, 'message' => 'ইমেজ ডিকোড করতে সমস্যা হয়েছে!']);
            exit;
        }

        $uploadDir = dirname(__DIR__, 2) . '/public/uploads/marketing';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }

        $filename = 'marketing_' . ($postId ?: 'custom') . '_' . time() . '_' . rand(100, 999) . '.png';
        $fullPath = $uploadDir . '/' . $filename;

        if (file_put_contents($fullPath, $imageData)) {
            $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
            $relativeUrl = "{$base}/uploads/marketing/{$filename}";

            if ($postId > 0) {
                $postModel = new MarketingPost();
                $postModel->update($postId, ['image_path' => $relativeUrl]);
            }

            echo json_encode([
                'success' => true,
                'image_url' => $relativeUrl,
                'local_path' => $fullPath
            ]);
            exit;
        }

        echo json_encode(['success' => false, 'message' => 'সার্ভারে ইমেজ সংরক্ষণ করা সম্ভব হয়নি!']);
        exit;
    }

    /**
     * Save/Update a Marketing Post
     */
    public function savePost() {
        $this->requireAdmin();

        $id = intval($_POST['id'] ?? 0);
        $data = [
            'product_id' => !empty($_POST['product_id']) ? intval($_POST['product_id']) : null,
            'slot_type' => $_POST['slot_type'] ?? 'morning',
            'post_date' => $_POST['post_date'] ?? date('Y-m-d'),
            'schedule_time' => $_POST['schedule_time'] ?? null,
            'mode' => $_POST['mode'] ?? 'template_no_ai',
            'headline' => trim($_POST['headline'] ?? ''),
            'caption' => trim($_POST['caption'] ?? ''),
            'image_path' => trim($_POST['image_path'] ?? ''),
            'status' => $_POST['status'] ?? 'draft'
        ];

        $postModel = new MarketingPost();
        if ($id > 0) {
            $postModel->update($id, $data);
            $_SESSION['success'] = 'পোস্ট সফলভাবে আপডেট করা হয়েছে!';
        } else {
            $id = $postModel->create($data);
            $_SESSION['success'] = 'নতুন পোস্ট সফলভাবে তৈরি করা হয়েছে!';
        }

        $this->redirect('/admin/marketing');
    }

    /**
     * Post directly to Facebook Page via Graph API
     */
    public function postToFacebook() {
        $this->requireAdmin();

        $id = intval($_POST['id'] ?? 0);
        $postModel = new MarketingPost();
        $post = $postModel->find($id);

        if (!$post) {
            $_SESSION['error'] = 'পোস্টটি খুঁজে পাওয়া যায়নি!';
            $this->redirect('/admin/marketing');
            return;
        }

        $facebook = new FacebookService();
        if (!$facebook->isConfigured()) {
            $_SESSION['error'] = 'ফেসবুক পেজ আইডি অথবা পেজ অ্যাক্সেস টোকেন সেট করা নেই! সেটিংস পেজে গিয়ে কনফিগার করুন।';
            $this->redirect('/admin/marketing/settings');
            return;
        }

        $image = $post['image_path'];
        if (empty($image)) {
            $_SESSION['error'] = 'পোস্ট করার জন্য কোনো ব্যানার বা ইমেজ নেই!';
            $this->redirect('/admin/marketing');
            return;
        }

        $caption = $post['caption'];
        $res = $facebook->postPhoto($caption, $image);

        if ($res['success']) {
            $postModel->update($id, [
                'status' => 'posted',
                'fb_post_id' => $res['post_id'],
                'posted_at' => date('Y-m-d H:i:s'),
                'error_message' => null
            ]);
            $_SESSION['success'] = "🎉 ফেসবুকে সফলভাবে পোস্ট পাবলিশ হয়েছে! পোস্ট আইডি: {$res['post_id']}";
        } else {
            $postModel->update($id, [
                'status' => 'failed',
                'error_message' => $res['message']
            ]);
            $_SESSION['error'] = "ফেসবুকে পোস্ট ব্যর্থ হয়েছে: " . $res['message'];
        }

        $this->redirect('/admin/marketing');
    }

    /**
     * Delete a Marketing Post
     */
    public function deletePost() {
        $this->requireAdmin();

        $id = intval($_POST['id'] ?? 0);
        if ($id > 0) {
            (new MarketingPost())->delete($id);
            $_SESSION['success'] = 'পোস্টটি সফলভাবে মুছে ফেলা হয়েছে!';
        }
        $this->redirect('/admin/marketing');
    }

    /**
     * Test Gemini API Connection (Ajax)
     */
    public function testGemini() {
        $this->requireAdmin();
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');

        $apiKey = trim($_POST['gemini_api_key'] ?? '') ?: Setting::getValue('gemini_api_key', '');
        $gemini = new GeminiService($apiKey);
        echo json_encode($gemini->testConnection());
        exit;
    }

    /**
     * Test Facebook Graph API Connection (Ajax)
     */
    public function testFacebook() {
        $this->requireAdmin();
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');

        $pageId = trim($_POST['facebook_page_id'] ?? '') ?: Setting::getValue('facebook_page_id', '');
        $token = trim($_POST['facebook_page_access_token'] ?? '') ?: Setting::getValue('facebook_page_access_token', '');
        
        $fb = new FacebookService($pageId, $token);
        $result = $fb->verifyConnection($pageId, $token);
        echo json_encode($result);
        exit;
    }

    /**
     * Automated Cron Trigger for Daily Scheduled Posts
     * Can be run via Windows Task Scheduler or cURL:
     * GET /admin/marketing/cron-trigger?key=YOUR_SECRET_KEY
     */
    public function cronTrigger() {
        header('Content-Type: application/json; charset=utf-8');

        $configuredSecret = Setting::getValue('marketing_cron_secret', '');
        $providedKey = $_GET['key'] ?? '';

        if (!empty($configuredSecret) && $configuredSecret !== $providedKey) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized cron key']);
            exit;
        }

        $autoPostEnabled = (Setting::getValue('facebook_auto_post_enabled', '0') === '1');
        $postModel = new MarketingPost();
        $facebook = new FacebookService();

        // 1. Check if today's 3 posts are generated, if not generate them
        $todayPosts = $postModel->getTodayPosts();
        if (!$todayPosts['morning'] || !$todayPosts['afternoon'] || !$todayPosts['evening']) {
            $_POST['mode'] = Setting::getValue('marketing_default_mode', 'template_no_ai');
            $_POST['date'] = date('Y-m-d');
            // Auto trigger daily generation
            // re-instantiate Product
            $pModel = new Product();
            $products = $pModel->all(['limit' => 30]);
            if (!empty($products)) {
                shuffle($products);
                $slots = [
                    ['slot_type' => 'morning', 'time' => Setting::getValue('marketing_morning_time', '09:00'), 'prod' => $products[0]],
                    ['slot_type' => 'afternoon', 'time' => Setting::getValue('marketing_afternoon_time', '14:00'), 'prod' => $products[1] ?? $products[0]],
                    ['slot_type' => 'evening', 'time' => Setting::getValue('marketing_evening_time', '19:00'), 'prod' => $products[2] ?? $products[0]]
                ];
                foreach ($slots as $s) {
                    if (empty($todayPosts[$s['slot_type']])) {
                        $cap = MarketingTemplateService::generateCaption($s['prod'], $s['slot_type']);
                        $head = MarketingTemplateService::getBannerHeadline($s['prod'], $s['slot_type']);
                        $postModel->create([
                            'product_id' => $s['prod']['id'],
                            'slot_type' => $s['slot_type'],
                            'post_date' => date('Y-m-d'),
                            'schedule_time' => $s['time'],
                            'mode' => 'template_no_ai',
                            'headline' => $head,
                            'caption' => $cap,
                            'image_path' => Product::getImageUrl($s['prod']['image_path'] ?? '', ''),
                            'status' => 'draft'
                        ]);
                    }
                }
            }
        }

        // 2. If auto-post is enabled, post pending scheduled posts
        $postedCount = 0;
        $errors = [];

        if ($autoPostEnabled && $facebook->isConfigured()) {
            $pending = $postModel->getPendingScheduledForNow();
            foreach ($pending as $p) {
                if (!empty($p['image_path'])) {
                    $res = $facebook->postPhoto($p['caption'], $p['image_path']);
                    if ($res['success']) {
                        $postModel->update($p['id'], [
                            'status' => 'posted',
                            'fb_post_id' => $res['post_id'],
                            'posted_at' => date('Y-m-d H:i:s'),
                            'error_message' => null
                        ]);
                        $postedCount++;
                    } else {
                        $postModel->update($p['id'], [
                            'status' => 'failed',
                            'error_message' => $res['message']
                        ]);
                        $errors[] = "Post #{$p['id']}: " . $res['message'];
                    }
                }
            }
        }

        echo json_encode([
            'success' => true,
            'time' => date('Y-m-d H:i:s'),
            'auto_post_enabled' => $autoPostEnabled,
            'posted_count' => $postedCount,
            'errors' => $errors
        ]);
        exit;
    }

    /**
     * Start Facebook OAuth Login Flow
     */
    public function facebookOAuthStart() {
        $this->requireAdmin();

        $appId = trim(Setting::getValue('facebook_app_id', ''));
        if (empty($appId)) {
            $_SESSION['error'] = 'ফেসবুক দিয়ে লগইন করার জন্য সেটিংস পেজে Facebook App ID সেট করুন।';
            $this->redirect('/admin/marketing/settings');
            return;
        }

        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
        $proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $redirectUri = $proto . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $base . '/admin/marketing/facebook-callback';

        $fb = new FacebookService();
        $oauthUrl = $fb->getOAuthUrl($redirectUri, $appId);

        header("Location: {$oauthUrl}");
        exit;
    }

    /**
     * Handle Facebook OAuth Callback & Retrieve Pages
     */
    public function facebookOAuthCallback() {
        $this->requireAdmin();

        $code = $_GET['code'] ?? null;
        if (!$code) {
            $errDesc = $_GET['error_description'] ?? 'লগইন বাতিল করা হয়েছে।';
            $_SESSION['error'] = 'ফেসবুক লগইন ব্যর্থ: ' . $errDesc;
            $this->redirect('/admin/marketing/settings');
            return;
        }

        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
        $proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $redirectUri = $proto . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $base . '/admin/marketing/facebook-callback';

        $fb = new FacebookService();
        $res = $fb->handleOAuthCallback($code, $redirectUri);

        if (!$res['success']) {
            $_SESSION['error'] = 'ফেসবুক টোকেন সংগ্রহ ব্যর্থ: ' . ($res['message'] ?? 'অজানা ত্রুটি');
            $this->redirect('/admin/marketing/settings');
            return;
        }

        $pages = $res['pages'] ?? [];
        if (empty($pages)) {
            $_SESSION['error'] = 'আপনার ফেসবুক অ্যাকাউন্টে ম্যানেজ করার মতো কোনো ফেসবুক পেজ পাওয়া যায়নি!';
            $this->redirect('/admin/marketing/settings');
            return;
        }

        // Store retrieved pages in session for user selection
        $_SESSION['fb_oauth_pages'] = $pages;
        $_SESSION['success'] = 'লগইন সফল হয়েছে! নিচে থেকে আপনার কাঙ্ক্ষিত ফেসবুক পেজটি নির্বাচন করুন।';
        $this->redirect('/admin/marketing/settings?action=select_page');
    }

    /**
     * API: Save Selected Facebook Page
     */
    public function apiSaveFacebookPage() {
        $this->requireAdmin();

        $pageId = trim($_POST['page_id'] ?? '');
        $pageName = trim($_POST['page_name'] ?? '');
        $pageToken = trim($_POST['page_access_token'] ?? '');
        $pagePicture = trim($_POST['page_picture'] ?? '');

        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
                  || (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false)
                  || isset($_POST['ajax']);

        if (empty($pageId) || empty($pageToken)) {
            if ($isAjax) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['success' => false, 'message' => 'পেজ আইডি অথবা টোকেন পাওয়া যায়নি!']);
                exit;
            }
            $_SESSION['error'] = 'পেজ আইডি অথবা টোকেন পাওয়া যায়নি!';
            $this->redirect('/admin/marketing/settings');
            return;
        }

        Setting::set('facebook_page_id', $pageId);
        Setting::set('facebook_page_name', $pageName);
        Setting::set('facebook_page_access_token', $pageToken);
        if (!empty($pagePicture)) {
            Setting::set('facebook_page_picture', $pagePicture);
        }

        unset($_SESSION['fb_oauth_pages']);
        $_SESSION['success'] = "🎉 ফেসবুক পেজ '{$pageName}' সফলভাবে সংযুক্ত হয়েছে! আলাদাভাবে কোনো Access Token লাগবে না।";

        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => true,
                'message' => "পেজ '{$pageName}' সফলভাবে সংযুক্ত হয়েছে!",
                'page_id' => $pageId,
                'page_name' => $pageName
            ]);
            exit;
        }

        $this->redirect('/admin/marketing/settings');
    }

    /**
     * Disconnect Facebook Page
     */
    public function disconnectFacebookPage() {
        $this->requireAdmin();

        Setting::set('facebook_page_id', '');
        Setting::set('facebook_page_name', '');
        Setting::set('facebook_page_access_token', '');
        Setting::set('facebook_page_picture', '');
        Setting::set('facebook_connected_provider', '');
        Setting::set('facebook_portal_key', '');
        unset($_SESSION['fb_oauth_pages']);

        $_SESSION['success'] = 'ফেসবুক পেজ সংযোগ বিচ্ছিন্ন করা হয়েছে।';
        $this->redirect('/admin/marketing/settings');
    }

    /**
     * API: Fetch Managed Pages given a User Token (Used by JS SDK)
     */
    public function apiFetchPagesWithToken() {
        $this->requireAdmin();
        header('Content-Type: application/json; charset=utf-8');

        $userToken = trim($_POST['user_access_token'] ?? '');
        if (empty($userToken)) {
            echo json_encode(['success' => false, 'message' => 'ইউজার অ্যাক্সেস টোকেন পাওয়া যায়নি!']);
            exit;
        }

        $appId = Setting::getValue('facebook_app_id', '');
        $appSecret = Setting::getValue('facebook_app_secret', '');

        $fb = new FacebookService();
        $longLived = (!empty($appId) && !empty($appSecret)) 
            ? ($fb->exchangeLongLivedUserToken($userToken, $appId, $appSecret) ?: $userToken) 
            : $userToken;

        $pages = $fb->getUserPages($longLived);

        if (empty($pages)) {
            echo json_encode(['success' => false, 'message' => 'আপনার আইডিতে কোনো পেজ পাওয়া যায়নি অথবা পেজ পারমিশন দেওয়া হয়নি।']);
            exit;
        }

        echo json_encode([
            'success' => true,
            'pages' => $pages
        ]);
        exit;
    }
}
