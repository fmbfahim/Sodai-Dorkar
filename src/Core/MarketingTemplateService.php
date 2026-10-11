<?php

namespace Core;

use Models\Setting;

class MarketingTemplateService {

    /**
     * Generate 100% Non-AI high-converting Bengali marketing copy for Facebook
     */
    public static function generateCaption(array $product, string $slotType = 'morning') {
        $pName = trim($product['name'] ?? 'প্রিমিয়াম গ্রোসারি পণ্য');
        $sellPrice = floatval($product['sell_price'] ?? 0);
        $regularPrice = floatval($product['regular_price'] ?? 0);
        $unit = $product['base_unit'] ?? 'পিস';
        
        $hasDiscount = ($regularPrice > $sellPrice && $sellPrice > 0);
        $savings = $hasDiscount ? ($regularPrice - $sellPrice) : 0;
        $discountPct = $hasDiscount ? round(($savings / $regularPrice) * 100) : 0;

        $siteTitle = Setting::getValue('site_name', 'সদাই দরকার (Sodai Dorkar)');
        $phone = Setting::getValue('site_phone', '01700-000000');
        $siteUrl = Setting::getValue('site_url', 'https://sodaidorkar.com');
        $deliveryFee = Setting::getValue('shipping_charge', '৫০');

        // Price Breakdown Formatting
        if ($hasDiscount) {
            $priceText = "💰 নিয়মিত মূল্য: ৳" . number_format($regularPrice, 2) . " ❌\n🔥 অফার মূল্য: মাত্র ৳" . number_format($sellPrice, 2) . " ✅\n🎉 সরাসরি সাশ্রয়: ৳" . number_format($savings, 2) . " ({$discountPct}% ডিসকাউন্ট!)";
        } else {
            $priceText = "💰 আকর্ষণীয় মূল্য: মাত্র ৳" . number_format($sellPrice, 2) . " (প্রতি {$unit})";
        }

        $templates = self::getTemplatesBySlot($slotType);
        $chosenTemplate = $templates[array_rand($templates)];

        // Replace placeholders
        $caption = strtr($chosenTemplate, [
            '{PRODUCT_NAME}' => $pName,
            '{PRICE_INFO}'   => $priceText,
            '{SITE_NAME}'    => $siteTitle,
            '{PHONE}'        => $phone,
            '{SITE_URL}'     => $siteUrl,
            '{UNIT}'         => $unit,
            '{DISCOUNT_PCT}' => $discountPct > 0 ? "{$discountPct}% ছাড়" : "বিশেষ অফার"
        ]);

        return trim($caption);
    }

    /**
     * Get banner headline without AI
     */
    public static function getBannerHeadline(array $product, string $slotType = 'morning') {
        $regularPrice = floatval($product['regular_price'] ?? 0);
        $sellPrice = floatval($product['sell_price'] ?? 0);
        $discountPct = ($regularPrice > $sellPrice && $sellPrice > 0) ? round((($regularPrice - $sellPrice) / $regularPrice) * 100) : 0;

        if ($discountPct > 0) {
            $headlines = [
                "🔥 মেগা সেভিং অফার ({$discountPct}% ছাড়)!",
                "💥 সেরা দামে সেরা বাজার!",
                "🎉 আজকের বিশেষ ডিসকাউন্ট ডিল!"
            ];
            return $headlines[array_rand($headlines)];
        }

        $bySlot = [
            'morning' => [
                '🌅 সকালের ফ্রেশ ও সেরা বাজার!',
                '🌿 ১০০% খাঁটি ও গুণগত মান নিশ্চিত!',
                '🛒 সকাল সকাল দ্রুততম হোম ডেলিভারি!'
            ],
            'afternoon' => [
                '🍛 দুপুরের স্পেশাল গ্রোসারি অফার!',
                '📦 বাজারের সেরা দামে খাঁটি পণ্য!',
                '✨ আপনার পরিবারের বিশ্বস্ত অনলাইন বাজার!'
            ],
            'evening' => [
                '⚡ সান্ধ্যকালীন সুপার ডিল অফার!',
                '🌙 আজকের মেগা ছাড় মিস করবেন না!',
                '🛍️ ঘরে বসেই রাতের ঝামেলাহীন বাজার!'
            ]
        ];

        $list = $bySlot[$slotType] ?? $bySlot['morning'];
        return $list[array_rand($list)];
    }

    /**
     * Slot specific high-converting copywriting templates
     */
    protected static function getTemplatesBySlot(string $slot): array {
        switch ($slot) {
            case 'morning':
                return [
                    "🌅 সকালের ফ্রেশ বাজার নিয়ে এলো {SITE_NAME}! 🌿\n\nদিনের শুরুটা হোক সেরা মানের খাঁটি ও সতেজ পণ্যে। আপনার ঘরের নিত্যদিনের প্রয়োজনের জন্য আজই অর্ডার করুন '{PRODUCT_NAME}'।\n\n{PRICE_INFO}\n\n✨ কেন আমাদের থেকে নিবেন?\n✅ ১০০% সেরা মান ও সতেজতার নিশ্চয়তা\n✅ দ্রুততম সময়ে দোরগোড়ায় হোম ডেলিভারি\n✅ পণ্য দেখে মূল্য পরিশোধ (ক্যাশ অন ডেলিভারি)\n\n🛒 এখনই অর্ডার করতে আমাদের পেইজে ইনবক্স করুন অথবা ভিজিট করুন:\n🌐 {SITE_URL}\n📞 সরাসরি অর্ডার হটলাইন: {PHONE}\n\n#সদাই_দরকার #সকালের_বাজার #অনলাইন_গ্রোসারি #সেরা_অফার #SodaiDorkar",

                    "🌿 সকাল সকাল ফ্রেশ বাজার ঘরে বসেই পেয়ে যান! 🛒\n\nবাজারে গিয়ে ভিড় আর ঝক্কি নয়, সেরা মানের '{PRODUCT_NAME}' এখন আরও সাশ্রয়ী মূল্যে পৌঁছে যাবে আপনার বাসায়।\n\n{PRICE_INFO}\n\n🚚 বিশেষ সুবিধাসমূহ:\n🔹 নিরাপদ ও হাইজিনিক প্যাকেজিং\n🔹 ফাস্ট হোম ডেলিভারি সেবা\n🔹 সহজ রিটার্ন ও এক্সচেঞ্জ পলিসি\n\n👉 স্টক সীমিত! আজই আপনার অর্ডারটি কনফার্ম করুন:\n📞 কল করুন: {PHONE}\n💬 ইনবক্স করুন আমাদের ফেসবুক পেইজে\n\n#সদাই_বাজার #ফ্রেশ_গ্রোসারি #হোম_ডেলিভারি #সদাইদরকার #DailyGroceries"
                ];

            case 'afternoon':
                return [
                    "🍛 দুপুরের রান্নায় চাই সেরা মানের বাজার! 💥\n\nপরিবারের সুস্বাস্থ্য ও পুষ্টি নিশ্চিত করতে আজই সংগ্রহ করুন খাঁটি '{PRODUCT_NAME}'। {SITE_NAME}-এ পাচ্ছেন বাজারের চেয়েও কম খরচে!\n\n{PRICE_INFO}\n\n🌟 আমাদের প্রতিশ্রুতি:\n✔️ সেরা কোয়ালিটি ও সঠিক ওজন\n✔️ ঝামেলাহীন হোম ডেলিভারি\n✔️ ঘরে বসেই সহজ কেনাকাটার অনুভূতি\n\n📦 অর্ডার করতে ইনবক্সে মেসেজ পাঠান অথবা কল করুন:\n📱 হটলাইন: {PHONE}\n🌐 ওয়েবসাইট: {SITE_URL}\n\n#গ্রোসারি_শপ #নিত্যপ্রয়োজনীয়_বাজার #দুপুরের_অফার #অনলাইন_শপিং #SodaiDorkar",

                    "💥 দুপুরের মেগা সেভিংস অফার! {DISCOUNT_PCT}! 🛍️\n\nরান্নাঘরের প্রয়োজনীয় পণ্য কিনুন সর্বোচ্চ সাশ্রয়ে। আজই অর্ডার করুন '{PRODUCT_NAME}'।\n\n{PRICE_INFO}\n\n🛵 আর দেরি কেন? এখনই আপনার পছন্দের পণ্যটি কার্ট করুন:\n📲 ইনবক্স করুন পেইজে\n📞 হেল্পলাইন: {PHONE}\n\n#গ্রোসারি_অফার #মেগা_ডিসকাউন্ট #সদাই_দরকার #বাজার_অনলাইনে"
                ];

            case 'evening':
            default:
                return [
                    "⚡ সান্ধ্যকালীন ধামাকা অফার! সীমিত সময়ের সুযোগ! 🌙\n\nদিনের ব্যস্ততা শেষে ঘরে বসেই নিশ্চিন্তে বাজার করুন {SITE_NAME} থেকে। আজই সেরা মূল্যে পেয়ে যাচ্ছেন '{PRODUCT_NAME}'!\n\n{PRICE_INFO}\n\n🎉 স্পেশাল ফিচার:\n⚡ সুপারফাস্ট ডেলিভারি\n⚡ ক্যাশ অন ডেলিভারি সুবিধা\n⚡ ১০০% স্যাটিসফ্যাকশন গ্যারান্টি\n\n⏰ অফারটি সীমিত সময়ের জন্য! এখনই ইনবক্স করে আপনার কপিটি নিশ্চিত করুন।\n📞 কল করুন: {PHONE}\n🌐 ওয়েবসাইট: {SITE_URL}\n\n#ফ্ল্যাশ_সেল #সন্ধ্যার_অফার #সেরা_ছাড় #সদাই_দরকার #FlashSale",

                    "🌙 রাতের বাজার হোক নির্ঝঞ্ঝাট ও সাশ্রয়ী! 🛍️\n\nঘরে বসেই মাত্র একটি ক্লিকে সংগ্রহ করুন পরিবারের প্রয়োজনীয় '{PRODUCT_NAME}'।\n\n{PRICE_INFO}\n\n✨ ঘরে বসেই সম্পূর্ণ ক্যাশ অন ডেলিভারিতে পণ্য বুঝে নিন।\n👉 অর্ডার করতে ইনবক্স করুন অথবা ডায়াল করুন:\n📞 {PHONE}\n\n#অনলাইন_বাজার #সদাই_দরকার #ক্যাশ_অন_ডেলিভারি #গ্রোসারি"
                ];
        }
    }
}
