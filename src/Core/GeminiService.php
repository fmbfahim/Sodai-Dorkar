<?php

namespace Core;

use Models\Setting;

class GeminiService {
    protected $apiKey;
    protected $model;

    public function __construct($apiKey = null, $model = null) {
        $this->apiKey = $apiKey ?: Setting::getValue('gemini_api_key', '');
        $savedModel = $model ?: Setting::getValue('gemini_model', 'gemini-2.5-flash');
        if (empty($savedModel) || $savedModel === 'gemini-2.0-flash') {
            $savedModel = 'gemini-2.5-flash';
        }
        $this->model = $savedModel;
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
            $usedModel = $result['model'] ?? $this->model;
            return [
                'success' => true,
                'model' => $usedModel,
                'message' => "Gemini API সংযোগ সফল হয়েছে! (মডেল: {$usedModel}) রেসপন্স: " . htmlspecialchars($result['text'])
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
     * Generate creative campaign concepts (Observance Days, Viral Memes, Lifestyle Promotions, Product Deals)
     */
    public function generateCreativeCampaign(string $type, array $params = []): array {
        if (!$this->isConfigured()) {
            return $this->getBuiltinCampaignConcept($type, $params);
        }

        $siteTitle = Setting::getValue('site_name', 'সদাই দরকার');
        $phone = Setting::getValue('contact_phone', Setting::getValue('site_phone', '01609448066'));
        $siteUrl = Setting::getValue('site_url', 'https://sodaidorkar.com');

        $userPrompt = $params['user_prompt'] ?? '';
        $eventName = $params['event_name'] ?? '';
        $productDetails = $params['products_text'] ?? '';

        $typeInstruction = '';
        if ($type === 'observance') {
            $typeInstruction = <<<TXT
ক্যাম্পেইন টাইপ: বিভিন্ন দিবস ভিত্তিক (যেমন: বিশ্ব হার্ট দিবস, বিশ্ব খাদ্য দিবস, রমজানুল মোবারক, পবিত্র ঈদ, পহেলা বৈশাখ, মা দিবস ইত্যাদি)।
দিবসের নাম / প্রম্পট: "{$eventName}" - {$userPrompt}
ফোকাস: মিনা বাজারের বিজ্ঞাপনের মতো (যেমন: "২৯ সেপ্টেম্বর - বিশ্ব হার্ট দিবস: হার্টের যত্ন শুরু হোক বাজার থেকেই")। স্বাস্থ্যকর পুষ্টিকর খাবার, পরিবারের সুস্থতা এবং সদাই দরকারের তাজা বাজারের সাথে দিবসের তাৎপর্যের মেলবন্ধন।
TXT;
        } elseif ($type === 'meme') {
            $typeInstruction = <<<TXT
ক্যাম্পেইন টাইপ: মজার ও রিলেটেবল বাংলাদেশি গ্রোসারি মিমস (Viral Grocery Humor)।
প্রম্পট/টপিক: "{$userPrompt}"
ফোকাস: বাংলাদেশি মধ্যবিত্ত ও তরুণদের নিত্যদিনের বাজার ও রান্নাবান্নার মজার মজার পরিস্থিতি (যেমন: "বাজারের ভারী ব্যাগ তোলা = আসল জিম", "মা বললেন পেঁয়াজ আনতে, আনলাম চকলেট", "হবু বউয়ের চেয়েও এখন ডিমের দামের চিন্তা বেশি!", "শুক্রবার মানেই সকালের কাঁচাবাজারের যুদ্ধ")। ক্যাপশন ও হেডলাইন হবে অতীব মজার ও শেয়ার করার মতো।
TXT;
        } elseif ($type === 'lifestyle') {
            $typeInstruction = <<<TXT
ক্যাম্পেইন টাইপ: সুপারমার্কেট লাইফস্টাইল ও কিচেন কুকিং প্রমোশন (Meena Bazar Style)।
প্রম্পট/টপিক: "{$userPrompt}"
ফোকাস: আধুনিক ঝকঝকে সুপারমার্কেটে ট্রলি নিয়ে সদাই করা, খাঁটি ঘি/তেল/মসলা দিয়ে পরিচ্ছন্ন রান্নাঘরে গৃহিণীর সুস্বাদু রান্নাবান্না ("প্রতিদিনের রান্নায় থাকুক ঘিয়ের খাঁটি সুবাস"), সতেজ কাঁচাবাজার ও হোম ডেলিভারির আনন্দ।
TXT;
        } else {
            $typeInstruction = <<<TXT
ক্যাম্পেইন টাইপ: পণ্য অফার ও মেগা ডিসকাউন্ট ডিল।
পণ্যের বিবরণ: {$productDetails}
প্রম্পট: {$userPrompt}
ফোকাস: সীমিত সময়ের বিশেষ ছাড়, সাশ্রয়ী সুপারমার্কেট ডিল, হোম ডেলিভারি ও ক্যাশ অন ডেলিভারি।
TXT;
        }

        $systemPrompt = <<<PROMPT
তুমি একজন শীর্ষ বাংলাদেশি ই-কমার্স ক্রিয়েটিভ ডিরেক্টর ও সোশ্যাল মিডিয়া কপিরাইটার (মিনা বাজার, স্বপ্ন, চালডাল এর প্রিমিয়াম স্ট্যান্ডার্ড)।
আমাদের অনলাইন সুপারমার্কেট "{$siteTitle}" এর জন্য নিচের ক্যাম্পেইনের তথ্য অনুযায়ী একটি আকর্ষণীয় ক্যাম্পেইন তৈরি করো।

{$typeInstruction}

হটলাইন: {$phone}
ওয়েবসাইট: {$siteUrl}

তোমাকে অবশ্যই STRICTLY শুধুমাত্র একটি বৈধ JSON অবজেক্ট আউটপুট দিতে হবে (কোনো ব্যাকটিক, কোট বা অতিরিক্ত ভূমিকা ছাড়া):
{
  "headline": "ব্যানারের প্রধান আকর্ষণীয় বাংলা হেডলাইন (৩-৬ শব্দ, যেমন: 'হার্টের যত্ন শুরু হোক বাজার থেকেই' অথবা 'প্রতিদিনের রান্নায় থাকুক খাঁটি সুবাস')",
  "sub_headline": "সংক্ষিপ্ত সাব-হেডলাইন বা স্লোগান (১ বাক্য)",
  "badge_text": "ব্যানারের কর্নারের জন্য ছোট ব্যাজ টেক্সট (যেমন: '২৯ সেপ্টেম্বর', '২৫৳ ছাড়', '১০০% ফ্রেশ', 'মেগা ডিল')",
  "caption": "সম্পূর্ণ প্রফেশনাল ফেসবুক পোস্টের ক্যাপশন (আকর্ষণীয় হুক, বুলেট পয়েন্ট, কল-টু-অ্যাকশন, হটলাইন নম্বর {$phone}, এবং ৪-৫টি ট্রেন্ডিং হ্যাশট্যাগ সহ)",
  "visual_prompt": "An ultra-realistic, cinematic photographic prompt in English suitable for Imagen 3 or AI image generation depicting the exact scene in Meena Bazar retail advertising style (modern clean supermarket, smiling Bangladeshi customer, colorful fresh vegetables, vibrant warm lighting, 8k resolution, professional commercial photography, clean composition). Do not include any text in the visual prompt.",
  "theme": "emerald"
}
Note: "theme" ফিল্ডের মান emerald, ruby, amber, navy অথবা royal এর যেকোনো একটি বেছে নাও।
PROMPT;

        $res = $this->callApi($systemPrompt);
        if (!$res['success']) {
            return $res;
        }

        $cleanText = trim($res['text']);
        // Strip markdown code fences if model returned ```json ... ```
        if (preg_match('/```(?:json)?\s*(\{.*?\})\s*```/s', $cleanText, $matches)) {
            $cleanText = $matches[1];
        } elseif (preg_match('/\{[\s\S]*\}/', $cleanText, $matches)) {
            $cleanText = $matches[0];
        }

        $decoded = json_decode($cleanText, true);
        if (!$decoded || empty($decoded['headline'])) {
            return [
                'success' => true,
                'headline' => '🌿 সতেজ বাজার, সুস্থ পরিবার!',
                'sub_headline' => 'সদাই দরকারে সেরা মূল্যে তাজা গ্রোসারি',
                'badge_text' => 'স্পেশাল অফার',
                'caption' => $res['text'],
                'visual_prompt' => 'A modern clean Bangladeshi supermarket with fresh grocery displays and smiling shoppers, commercial retail photography, warm lighting',
                'theme' => 'emerald'
            ];
        }

        $decoded['success'] = true;
        return $decoded;
    }

    /**
     * Builtin high-converting campaign concepts matching Meena Bazar standards
     */
    public function getBuiltinCampaignConcept(string $type, array $params = []): array {
        $phone = Setting::getValue('contact_phone', Setting::getValue('site_phone', '01609448066'));
        $siteUrl = Setting::getValue('site_url', 'https://sodaidorkar.com');
        $siteTitle = Setting::getValue('site_name', 'সদাই দরকার');
        $eventName = $params['event_name'] ?? '';
        $userPrompt = $params['user_prompt'] ?? '';

        if ($type === 'observance') {
            $headline = !empty($userPrompt) ? $userPrompt : "হার্টের যত্ন শুরু হোক বাজার থেকেই";
            $badge = !empty($eventName) ? $eventName : "২৯ সেপ্টেম্বর • বিশ্ব হার্ট দিবস";
            return [
                'success' => true,
                'headline' => $headline,
                'sub_headline' => 'সদাই দরকারে তাজা শাকসবজি ও পুষ্টিকর খাবার',
                'badge_text' => $badge,
                'caption' => "💚 সুস্থ জীবনের সূচনা হোক প্রতিদিনের খাঁটি বাজার থেকেই!\n\n{$headline}\n\nআপনার পরিবারের হৃদযন্ত্র ও সুস্বাস্থ্য নিশ্চিত করতে সদাই দরকার পৌঁছে দিচ্ছে ১০০% সতেজ সবুজ শাকসবজি, টাটকা ফলমূল ও রাসায়নিকমুক্ত খাঁটি খাবার।\n\n🚚 ৩০-৬০ মিনিটে দ্রুত হোম ডেলিভারি\n💵 ক্যাশ অন ডেলিভারি (পণ্য দেখে মূল্য পরিশোধ)\n📞 হটলাইন: {$phone}\n🌐 ওয়েবসাইট: {$siteUrl}\n\n#SodaiDorkar #সুস্থ_জীবন #তাজা_বাজার #অনলাইন_সুপারমার্কেট",
                'visual_prompt' => "A commercial advertisement photograph for a premium Bangladeshi supermarket celebrating healthy living and family health. Joyful multi-generational Bangladeshi family shopping in a bright modern grocery aisle, shopping cart in foreground filled with fresh broccoli, oats, apples, bananas and olive oil, realistic warm lighting, 8k resolution, Meena Bazar advertising style",
                'theme' => 'emerald'
            ];
        } elseif ($type === 'meme') {
            $headline = !empty($userPrompt) ? $userPrompt : "বাজারের ভারী ব্যাগ তোলাই আসল জিম!";
            return [
                'success' => true,
                'headline' => $headline,
                'sub_headline' => 'সদাই দরকারের হোম ডেলিভারিতে শরীরও ভালো, পকেটও খুশি',
                'badge_text' => '😂 গ্রোসারি মিমস',
                'caption' => "😂 {$headline}\n\nবাজারের ব্যাগ তুলতে গিয়ে জিমের টাকা বাঁচাচ্ছেন ভালো কথা, তবে গরমে কষ্ট না করে সদাই দরকারে অর্ডার করলেই তো সেরা মূল্যে তাজা বাজার ঘরে চলে আসে!\n\n🛒 অর্ডার করতে ইনবক্স করুন অথবা ভিজিট করুন: {$siteUrl}\n📞 হটলাইন: {$phone}\n\n#SodaiDorkar #গ্রোসারি_মিমস #বাজারের_গল্প #অনলাইন_বাজার",
                'visual_prompt' => "A funny, relatable commercial photograph of an energetic Bangladeshi young man carrying two huge heavy grocery bags packed with fresh vegetables, bread and eggs, flexing his arm muscles with a comical proud smile, bright modern Dhaka apartment background, cheerful commercial photography, 8k resolution",
                'theme' => 'emerald'
            ];
        } elseif ($type === 'lifestyle') {
            $headline = !empty($userPrompt) ? $userPrompt : "প্রতিদিনের রান্নায় থাকুক ঘিয়ের খাঁটি সুবাস";
            return [
                'success' => true,
                'headline' => $headline,
                'sub_headline' => 'খাঁটি ঘি ও বাছাইকৃত মসলায় সুস্বাদু রান্না',
                'badge_text' => '✨ ২৫৳ ছাড়',
                'caption' => "🍳 {$headline}\n\nরান্নায় খাঁটি স্বাদ আর স্বাস্থ্যকর সুবাস পেতে বেছে নিন আমাদের প্রিমিয়াম কোয়ালিটি ঘি ও ১০০% খাঁটি মসলা। সীমিত সময়ের জন্য বিশেষ ছাড়ে পাওয়া যাচ্ছে!\n\n✨ ক্যাশ অন ডেলিভারি সুবিধা\n📞 কল করুন: {$phone}\n🌐 ভিজিট করুন: {$siteUrl}\n\n#SodaiDorkar #খাঁটি_ঘি #রান্নাবান্না #সুপারমার্কেট_অফার",
                'visual_prompt' => "A warm commercial photograph of a smiling Bangladeshi homemaker cooking delicious food in a modern clean kitchen, an elegant metal can of pure ghee on the marble counter in foreground, warm cinematic cooking lighting, authentic Bangladeshi home lifestyle, 8k resolution",
                'theme' => 'amber'
            ];
        } else {
            return [
                'success' => true,
                'headline' => '🔥 মেগা কম্বো বাজার অফার! সেরা সাশ্রয়!',
                'sub_headline' => 'দৈনন্দিন নিত্যপ্রয়োজনীয় মুদি পণ্য সেরা পাইকারি দামে',
                'badge_text' => '⚡ সুপার সেভার ডিল',
                'caption' => "💥 মেগা কম্বো বাজার অফার!\n\nদৈনন্দিন সব নিত্যপ্রয়োজনীয় পণ্য এখন পাচ্ছেন এক সাথে বিশাল সাশ্রয়ে। স্টক সীমিত, দ্রুত অর্ডার করুন!\n\n🚚 দ্রুততম হোম ডেলিভারি\n📞 অর্ডার হটলাইন: {$phone}\n🌐 ওয়েবসাইট: {$siteUrl}\n\n#SodaiDorkar #মেগা_অফার #কম্বো_ডিল #সাশ্রয়",
                'visual_prompt' => "A clean commercial advertising photo of essential Bangladeshi grocery products: fresh farm eggs, premium flour bag, noodles, and beef neatly arranged on an illuminated modern display pedestal in a supermarket, commercial studio lighting, 8k resolution",
                'theme' => 'emerald'
            ];
        }
    }

    /**
     * Generate photorealistic image using Google Imagen 3 (with automatic FLUX fallback)
     */
    public function generateImage(string $prompt, string $aspectRatio = '1:1'): array {
        // Build an ultra-realistic commercial advertising master prompt in English
        $masterPrompt = $this->buildMasterPrompt($prompt);

        // If Google Imagen API is configured, try Google Imagen 3 first
        if ($this->isConfigured()) {
            $models = ['imagen-3.0-generate-002', 'imagen-3.0-fast-generate-001'];
            foreach ($models as $modelName) {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:predict?key=" . urlencode($this->apiKey);
                $payload = [
                    'instances' => [['prompt' => $masterPrompt]],
                    'parameters' => [
                        'sampleCount' => 1,
                        'aspectRatio' => $aspectRatio,
                        'personGeneration' => 'ALLOW_ADULT'
                    ]
                ];

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                $json = json_decode($response, true);
                if ($httpCode === 200 && !empty($json['predictions'][0]['bytesBase64Encoded'])) {
                    $b64 = $json['predictions'][0]['bytesBase64Encoded'];
                    $mime = $json['predictions'][0]['mimeType'] ?? 'image/jpeg';
                    $imageData = base64_decode($b64);

                    $uploadDir = dirname(__DIR__, 2) . '/public/uploads/marketing';
                    if (!is_dir($uploadDir)) @mkdir($uploadDir, 0755, true);

                    $filename = 'ai_imagen_' . time() . '_' . rand(100, 999) . '.jpg';
                    $fullPath = $uploadDir . '/' . $filename;
                    file_put_contents($fullPath, $imageData);

                    $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
                    $relativeUrl = "{$base}/uploads/marketing/{$filename}";

                    return [
                        'success' => true,
                        'image_url' => $relativeUrl,
                        'base64' => "data:{$mime};base64,{$b64}",
                        'local_path' => $fullPath,
                        'model' => $modelName,
                        'master_prompt' => $masterPrompt
                    ];
                }
            }
        }

        // Seamless, high-speed FLUX.1 Engine fallback (100% reliable, zero-config)
        return $this->generateFluxImage($masterPrompt);
    }

    /**
     * Generate 1080x1080 photorealistic marketing image using FLUX.1 AI engine (Zero-config, 100% reliable)
     */
    public function generateFluxImage(string $masterPrompt): array {
        $encoded = urlencode($masterPrompt);
        $seed = rand(1000, 999999);
        $endpoints = [
            "https://image.pollinations.ai/prompt/{$encoded}?width=1080&height=1080&nologo=true&seed={$seed}&model=flux",
            "https://image.pollinations.ai/prompt/{$encoded}?width=1080&height=1080&nologo=true&seed={$seed}&model=turbo",
            "https://image.pollinations.ai/prompt/{$encoded}?width=1080&height=1080&nologo=true"
        ];

        $lastError = "";
        $httpCode = 0;

        foreach ($endpoints as $url) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 35);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

            $imageData = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($httpCode === 200 && strlen($imageData) > 5000) {
                $uploadDir = dirname(__DIR__, 2) . '/public/uploads/marketing';
                if (!is_dir($uploadDir)) {
                    @mkdir($uploadDir, 0755, true);
                }

                $filename = 'ai_full_' . time() . '_' . rand(100, 999) . '.jpg';
                $fullPath = $uploadDir . '/' . $filename;
                file_put_contents($fullPath, $imageData);

                $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
                $relativeUrl = "{$base}/uploads/marketing/{$filename}";
                $b64 = base64_encode($imageData);

                return [
                    'success' => true,
                    'image_url' => $relativeUrl,
                    'base64' => "data:image/jpeg;base64,{$b64}",
                    'local_path' => $fullPath,
                    'model' => 'flux-1-schnell',
                    'master_prompt' => $masterPrompt
                ];
            }
            $lastError = $curlError ?: "AI Image Server HTTP {$httpCode}";
        }

        return [
            'success' => false,
            'message' => $lastError ?: "AI Image Server HTTP {$httpCode}"
        ];
    }

    /**
     * Convert any user prompt (Bengali or English) into an industry-grade English master diffusion prompt
     */
    public function buildMasterPrompt(string $prompt): string {
        $cleanPrompt = trim($prompt);
        if (empty($cleanPrompt)) {
            return "Commercial retail advertising photography for a modern supermarket, abundant fresh colorful groceries, clean aesthetic, 8k resolution, photorealistic, no text, no watermark, no cartoon";
        }

        // If the prompt is already our long master preset, keep it
        if (strlen($cleanPrompt) > 100 && stripos($cleanPrompt, 'photorealistic') !== false && !preg_match('/[\x{0980}-\x{09FF}]/u', $cleanPrompt)) {
            return $cleanPrompt;
        }

        // Check if prompt contains Bengali characters
        $isBengali = preg_match('/[\x{0980}-\x{09FF}]/u', $cleanPrompt);
        $translatedEnglish = $cleanPrompt;

        if ($isBengali) {
            $trans = $this->translateToEnglish($cleanPrompt);
            if (!empty($trans)) {
                $translatedEnglish = $trans;
            }
        }

        $lower = strtolower($translatedEnglish . ' ' . $cleanPrompt);

        $deliveryKeywords = ['delivery', 'ডেলিভারি', 'শিপিং', 'ফ্রি ডেলিভারি', 'free delivery', 'order', 'অর্ডার', 'হোম ডেলিভারি', 'scooter', 'ডেলিভারী', 'টাকার বেশি', 'টাকা'];
        $veggieKeywords = ['vegetable', 'সবজি', 'শাকসবজি', 'কাঁচাবাজার', 'fresh', 'farm', 'সবুজ', 'তাজা', 'শাক', 'আলু', 'টমেটো'];
        $fruitKeywords = ['fruit', 'ফল', 'ফলমূল', 'আম', 'আপেল', 'কমলা', 'কলা'];
        $cookingKeywords = ['cook', 'cooking', 'kitchen', 'রান্না', 'ঘি', 'ghee', 'তেল', 'oil', 'homemaker', 'গৃহিণী', 'রেসিপি', 'recipe', 'মসলা', 'spice'];
        $fishMeatKeywords = ['meat', 'মাংস', 'beef', 'গরুর মাংস', 'fish', 'মাছ', 'chicken', 'মুরগি', 'ডিম', 'egg', 'ইলিশ'];
        $marketKeywords = ['supermarket', 'cart', 'trolley', 'সুপারমার্কেট', 'ট্রলি', 'বাজার', 'দোকান', 'শপিং'];
        $healthKeywords = ['heart', 'হার্ট', 'স্বাস্থ্য', 'health', 'healthy', 'family', 'পরিবার'];
        $festiveKeywords = ['eid', 'ঈদ', 'রমজান', 'ramadan', 'বৈশাখ', 'boishakh', 'উৎসব', 'festival'];

        $sceneSubject = "";

        if ($this->hasAnyKeyword($lower, $deliveryKeywords)) {
            $sceneSubject = "Minimalist commercial advertising photography for online grocery delivery service: {$translatedEnglish}, modern sleek electric delivery scooter parked next to fresh grocery crates overflowing with crisp vibrant green vegetables, red apples, milk cartons, and grocery packages, bright crisp daylight, clean pastel gradient background, minimalist commercial retail campaign, Meena Bazar advertising style";
        } elseif ($this->hasAnyKeyword($lower, $fishMeatKeywords)) {
            $sceneSubject = "Commercial food and grocery advertising photography: {$translatedEnglish}, fresh premium produce neatly arranged on clean ice and marble counter in a modern supermarket, pristine studio lighting, appetizing commercial presentation";
        } elseif ($this->hasAnyKeyword($lower, $cookingKeywords)) {
            $sceneSubject = "Commercial food lifestyle photography: {$translatedEnglish}, authentic modern kitchen counter, pure ingredients, appetizing culinary scene, warm soft cinematic lighting, Meena Bazar advertising style";
        } elseif ($this->hasAnyKeyword($lower, $veggieKeywords)) {
            $sceneSubject = "Commercial retail grocery photography: {$translatedEnglish}, rustic wooden crates overflowing with vibrant farm-fresh vegetables and greens, crisp dewdrops, bright natural morning sunlight, supermarket display standard";
        } elseif ($this->hasAnyKeyword($lower, $fruitKeywords)) {
            $sceneSubject = "Commercial retail grocery photography: {$translatedEnglish}, vibrant colorful fresh fruits arranged in woven baskets, glistening dewdrops, soft commercial studio lighting";
        } elseif ($this->hasAnyKeyword($lower, $healthKeywords)) {
            $sceneSubject = "Commercial advertising photography: {$translatedEnglish}, healthy family diet theme, nutritious fresh greens, organic fruits, vibrant colors, bright natural morning sunlight, clean modern aesthetic";
        } elseif ($this->hasAnyKeyword($lower, $festiveKeywords)) {
            $sceneSubject = "Festive commercial advertising photography: {$translatedEnglish}, festive celebration in a modern Bangladeshi home, luxurious dining table spread with delicious feast, sweets, warm golden festive lighting";
        } elseif ($this->hasAnyKeyword($lower, $marketKeywords)) {
            $sceneSubject = "Commercial retail supermarket photography: {$translatedEnglish}, pristine modern Bangladeshi grocery supermarket aisle, gleaming shopping cart filled with grocery packages, soft retail ambient lighting";
        } else {
            $sceneSubject = "Commercial retail advertising photography for a modern Bangladeshi grocery brand: {$translatedEnglish}, clean minimalist aesthetic, elegant composition, vibrant natural colors, professional studio lighting, Meena Bazar retail advertising standard";
        }

        // Master negative prompt modifiers & photographic excellence tokens
        return $sceneSubject . ", 8k resolution, ultra-photorealistic, high-end commercial ad photography, highly detailed, sharp focus, cinematic lighting, no text, no letters, no watermark, no logo, no cartoon, no anime, no 3d render, no distorted faces, no illustration";
    }

    /**
     * Helper to translate Bengali prompt to English using Google Translate client API
     */
    public function translateToEnglish(string $text): string {
        $url = 'https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=en&dt=t&q=' . urlencode($text);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
        $res = curl_exec($ch);
        curl_close($ch);

        if ($res) {
            $json = json_decode($res, true);
            if (!empty($json[0])) {
                $translated = '';
                foreach ($json[0] as $segment) {
                    if (!empty($segment[0])) $translated .= $segment[0] . ' ';
                }
                return trim($translated);
            }
        }
        return $text;
    }

    protected function hasAnyKeyword(string $text, array $keywords): bool {
        foreach ($keywords as $kw) {
            if (mb_strpos($text, $kw) !== false) return true;
        }
        return false;
    }

    /**
     * Call Gemini API endpoint with smart model fallback and auto-discovery
     */
    protected function callApi(string $prompt) {
        $primaryModel = ($this->model === 'gemini-2.0-flash') ? 'gemini-2.5-flash' : $this->model;
        $candidates = [
            $primaryModel,
            'gemini-2.5-flash',
            'gemini-1.5-flash',
            'gemini-2.5-pro',
            'gemini-1.5-flash-latest',
            'gemini-1.5-pro'
        ];

        // Explicitly remove deprecated/unavailable 2.0-flash
        $modelsToTry = array_values(array_filter(array_unique($candidates), function($m) {
            return !empty($m) && $m !== 'gemini-2.0-flash';
        }));

        $lastError = 'অজানা ত্রুটি';

        foreach ($modelsToTry as $modelName) {
            $res = $this->requestGenerateContent($modelName, $prompt);
            if ($res['success']) {
                if ($this->model !== $modelName) {
                    Setting::set('gemini_model', $modelName);
                    $this->model = $modelName;
                }
                return $res;
            }
            $lastError = $res['message'];
        }

        // If standard models failed, query Google models API directly to discover active models
        $activeModels = $this->fetchAvailableModels();
        foreach ($activeModels as $discovered) {
            if ($discovered === 'gemini-2.0-flash') continue;
            if (!in_array($discovered, $modelsToTry)) {
                $res = $this->requestGenerateContent($discovered, $prompt);
                if ($res['success']) {
                    Setting::set('gemini_model', $discovered);
                    $this->model = $discovered;
                    return $res;
                }
                $lastError = $res['message'];
            }
        }

        return [
            'success' => false,
            'message' => $lastError
        ];
    }

    /**
     * Send generateContent HTTP request to a specific Gemini model
     */
    protected function requestGenerateContent(string $modelName, string $prompt): array {
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
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return [
                'success' => false,
                'message' => "cURL Error: " . $curlError
            ];
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

        $errMsg = $json['error']['message'] ?? "Gemini API HTTP {$httpCode}: " . substr((string)$response, 0, 250);
        return [
            'success' => false,
            'message' => "Gemini API Error ({$modelName}): " . $errMsg
        ];
    }

    /**
     * Dynamically fetch all models from Google that support generateContent
     */
    protected function fetchAvailableModels(): array {
        $url = "https://generativelanguage.googleapis.com/v1beta/models?key=" . urlencode($this->apiKey);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $response = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($response, true);
        $result = [];
        if (!empty($json['models'])) {
            foreach ($json['models'] as $m) {
                if (in_array('generateContent', $m['supportedGenerationMethods'] ?? [])) {
                    $cleanName = str_replace('models/', '', $m['name']);
                    $result[] = $cleanName;
                }
            }
        }
        return $result;
    }
}
