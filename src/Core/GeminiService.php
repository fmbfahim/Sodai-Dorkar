<?php

namespace Core;

use Models\Setting;

class GeminiService {
    protected $apiKey;
    protected $model;

    public function __construct($apiKey = null) {
        $this->apiKey = $apiKey ?: Setting::getValue('gemini_api_key', '');
        $this->model = Setting::getValue('gemini_model', 'gemini-1.5-flash');
    }

    public function isConfigured() {
        return !empty(trim($this->apiKey));
    }

    /**
     * Test connection to Google Gemini API
     */
    public function testConnection() {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Gemini API Key কনফিগার করা হয়নি! সেটিংস পেজে আপনার API Key দিন।'
            ];
        }

        $prompt = "তুমি কি সক্রিয় আছো? সংক্ষিপ্ত ১ বাক্যে বাংলায় উত্তর দাও।";
        $result = $this->callApi($prompt);

        if ($result['success']) {
            return [
                'success' => true,
                'message' => 'Gemini API সংযোগ সফল হয়েছে! রেসপন্স: ' . htmlspecialchars($result['text'])
            ];
        }

        return $result;
    }

    /**
     * Generate engaging Facebook Marketing Caption in Bengali
     */
    public function generateCaption(array $product, string $slotType = 'morning', string $customNotes = '') {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Gemini API Key সেট করা নেই।'
            ];
        }

        $pName = $product['name'] ?? 'বিশেষ পণ্য';
        $pBrand = !empty($product['brand_name']) ? "ব্র্যান্ড: {$product['brand_name']}" : '';
        $sellPrice = floatval($product['sell_price'] ?? 0);
        $regularPrice = floatval($product['regular_price'] ?? 0);
        $discount = ($regularPrice > $sellPrice) ? round((($regularPrice - $sellPrice) / $regularPrice) * 100) : 0;
        $unit = $product['base_unit'] ?? 'পিস';

        $slotContext = [
            'morning' => 'সকালের ফ্রেশ বাজার ও সকালের নাস্তা/রান্নার পণ্য। গ্রাহকদের সকাল সকাল তাজা বাজার ডেলিভারি নেওয়ার আহ্বান জানাও।',
            'afternoon' => 'দুপুরের বিশেষ আকর্ষণ ও দুপুরের রান্না/নিত্যপ্রয়োজনীয় গ্রোসারি অফার। সেরা সাশ্রয়ী কেনাকাটার ওপর ফোকাস করো।',
            'evening' => 'সন্ধ্যার মেগা ডিসকাউন্ট ও রাতের বিশেষ অফার। সীমিত সময় বা স্টক সীমিত এমন জরুরী তাগিদ (Urgency) রাখো।'
        ][$slotType] ?? 'দৈনন্দিন নিত্যপ্রয়োজনীয় বাজার অফার।';

        $siteTitle = Setting::getValue('site_name', 'সদাই দরকার (Sodai Dorkar)');
        $phone = Setting::getValue('site_phone', '01700-000000');
        $siteUrl = Setting::getValue('site_url', 'https://sodaidorkar.com');

        $prompt = <<<PROMPT
তুমি একজন শীর্ষ বাংলাদেশি ই-কমার্স ও সোশ্যাল মিডিয়া ডিজিটাল মার্কেটিং বিশেষজ্ঞ।
তোমাকে আমাদের অনলাইন গ্রোসারি শপ "{$siteTitle}" এর জন্য ফেসবুকে পোস্ট করার মতো আকর্ষণীয়, নজরকাড়া এবং সর্বোচ্চ সেলস কনভার্ট করে এমন একটি প্রফেশনাল বাংলা ক্যাপশন লিখে দিতে হবে।

পণ্যের বিবরণ:
- পণ্যের নাম: {$pName} {$pBrand}
- অফার বিক্রয় মূল্য: ৳{$sellPrice} (প্রতি {$unit})
- নিয়মিত মূল্য (MRP): ৳{$regularPrice}
- ডিসকাউন্ট/ছাড়: {$discount}% ছাড়
- বর্তমান স্লট: {$slotType} ({$slotContext})
{$customNotes}

নির্দেশনা:
১. প্রথম লাইনে একটি সুপার আকর্ষণীয় হুক/হেডলাইন লিখবে ইমোজি সহ (যেমন: 💥, 🛒, 🌿, 🔥)।
২. পণ্যের গুণাগুণ ও সতেজতা বা ব্যবহারের সুবিধা নিয়ে ২-৩টি বুলেট পয়েন্ট দেবে।
৩. মূল্য ও সাশ্রয়ের পরিমাণটি স্পষ্টভাবে হাইলাইট করবে (নিয়মিত মূল্য কেটে অফার মূল্য)।
৪. হোম ডেলিভারি ও ক্যাশ অন ডেলিভারি (পণ্য দেখে মূল্য পরিশোধ) সুবিধার কথা উল্লেখ করবে।
৫. কল-টু-অ্যাকশন (অর্ডার করার জন্য ইনবক্স বা লিংকে ক্লিক করুন) এবং হটলাইন নম্বর: {$phone} দেবে।
৬. শেষে ৪-৫টি ট্রেন্ডিং প্রাসঙ্গিক বাংলা ও ইংরেজি হ্যাশট্যাগ দেবে (যেমন: #SodaiDorkar #অনলাইন_বাজার #অফার)।
৭. কোনো অপ্রয়োজনীয় ভূমিকা বা কথা বলবে না, সরাসরি ফেসবুক পোস্টের সম্পূর্ণ টেক্সটটি দাও।
PROMPT;

        return $this->callApi($prompt);
    }

    /**
     * Generate 1-line punchy banner headline
     */
    public function generateBannerHeadline(array $product, string $slotType = 'morning') {
        if (!$this->isConfigured()) {
            return '🔥 আজকের সেরা মেগা অফার!';
        }

        $pName = $product['name'] ?? 'গ্রোসারি বাজার';
        $prompt = "আমাদের অনলাইন গ্রোসারি শপে '{$pName}' পণ্যের ফেসবুক ব্যানার ছবির উপরে লেখার জন্য ৩-৬ শব্দের মধ্যে একটি অতীব আকর্ষণীয় বাংলা হেডলাইন বা স্লোগান দাও (যেমন: 'সেরা মূল্যে ১০০% খাঁটি!', 'আজকের স্পেশাল ডিল!', 'সকালের সেরা বাজার!')। শুধুমাত্র হেডলাইনের লেখাটি ফেরত দাও।";

        $res = $this->callApi($prompt);
        return $res['success'] ? trim($res['text'], "\"' \n\r") : '🔥 আজকের সেরা মেগা অফার!';
    }

    /**
     * Call Gemini API endpoint
     */
    protected function callApi(string $prompt) {
        $modelsToTry = [$this->model, 'gemini-1.5-flash', 'gemini-2.0-flash'];
        $modelsToTry = array_unique($modelsToTry);

        $lastError = 'অজানা ত্রুটি';

        foreach ($modelsToTry as $modelName) {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key=" . urlencode($this->apiKey);

            $payload = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 1000
                ]
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                $lastError = "cURL Error: " . $curlError;
                continue;
            }

            $json = json_decode($response, true);

            if ($httpCode === 200 && !empty($json['candidates'][0]['content']['parts'][0]['text'])) {
                $text = trim($json['candidates'][0]['content']['parts'][0]['text']);
                return [
                    'success' => true,
                    'text' => $text,
                    'model' => $modelName
                ];
            }

            if (!empty($json['error']['message'])) {
                $lastError = "Gemini API Error ({$modelName}): " . $json['error']['message'];
            } else {
                $lastError = "Gemini API HTTP {$httpCode}: " . substr($response, 0, 300);
            }
        }

        return [
            'success' => false,
            'message' => $lastError
        ];
    }
}
