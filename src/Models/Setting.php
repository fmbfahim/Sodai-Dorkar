<?php

namespace Models;

use Core\Database;

class Setting {
    protected $db;

    public function __construct() {
        $config = require __DIR__ . '/../../config/database.php';
        $this->db = new Database($config);
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM settings");
        $results = $stmt->fetchAll();
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['key_name']] = $row['value'];
        }
        return $settings;
    }

    public function get($key, $default = null) {
        $stmt = $this->db->query("SELECT value FROM settings WHERE key_name = :key", ['key' => $key]);
        $row = $stmt->fetch();
        return $row ? $row['value'] : $default;
    }

    public static function getValue($key, $default = null) {
        $model = new self();
        return $model->get($key, $default);
    }

    public function update($key, $value) {
        // Check if exists
        $stmt = $this->db->query("SELECT id FROM settings WHERE key_name = :key", ['key' => $key]);
        if ($stmt->fetch()) {
            $this->db->query("UPDATE settings SET value = :value WHERE key_name = :key", ['key' => $key, 'value' => $value]);
        } else {
            $this->db->query("INSERT INTO settings (key_name, value) VALUES (:key, :value)", ['key' => $key, 'value' => $value]);
        }
    }

    public static function getMultiple(array $keys) {
        $model = new self();
        $all = $model->getAll();
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $all[$key] ?? null;
        }
        return $result;
    }

    public static function set($key, $value) {
        $model = new self();
        $model->update($key, $value);
    }

    /**
     * Calculate delivery charge based on subtotal, customer location, and delivery options
     *
     * @param float $subtotal Order subtotal
     * @param int|null $areaId Customer Area ID (Union)
     * @param int|null $pointId Customer Delivery Point ID
     * @param array $options Additional options (is_express, weight_kg, is_cod, exclude_free_shipping)
     * @return array Calculation breakdown and final fee
     */
    public static function calculateDeliveryCharge($subtotal, $areaId = null, $pointId = null, $options = []) {
        $settingModel = new self();
        $settings = $settingModel->getAll();

        $freeShippingEnabled = ($settings['free_shipping_enabled'] ?? '1') == '1';
        $freeThreshold = floatval($settings['free_shipping_threshold'] ?? $settings['delivery_free_threshold'] ?? 1100);
        $defaultCharge = floatval($settings['delivery_charge_default'] ?? $settings['default_delivery_charge'] ?? 30);

        $pointCharges = json_decode($settings['point_delivery_charges'] ?? '{}', true) ?: [];
        $areaCharges = json_decode($settings['area_delivery_charges'] ?? '{}', true) ?: [];

        // 1. Base rate resolution (Point > Area > Default)
        $baseCharge = $defaultCharge;
        $chargeSource = 'default';

        if ($pointId && isset($pointCharges[$pointId]) && $pointCharges[$pointId] !== '' && is_numeric($pointCharges[$pointId])) {
            $baseCharge = floatval($pointCharges[$pointId]);
            $chargeSource = 'point';
        } elseif ($areaId && isset($areaCharges[$areaId]) && $areaCharges[$areaId] !== '' && is_numeric($areaCharges[$areaId])) {
            $baseCharge = floatval($areaCharges[$areaId]);
            $chargeSource = 'area';
        }

        // 2. Free shipping evaluation
        $isFree = false;
        $finalCharge = $baseCharge;
        $freeCondition = $settings['free_shipping_requirement'] ?? 'min_amount';
        $qualifiesForFree = false;

        if ($freeShippingEnabled) {
            if ($freeCondition === 'all_orders') {
                $qualifiesForFree = true;
            } else {
                $qualifiesForFree = ($subtotal >= $freeThreshold);
            }
        }

        // Check spend more offers free delivery tier as well
        $spendOffers = json_decode($settings['spend_more_offers_tiers'] ?? '[]', true);
        if (is_array($spendOffers)) {
            foreach ($spendOffers as $st) {
                if (!empty($st['enabled']) && ($st['type'] ?? '') === 'free_delivery') {
                    if ($subtotal >= floatval($st['min_amount'])) {
                        $qualifiesForFree = true;
                        break;
                    }
                }
            }
        }

        if ($qualifiesForFree && empty($options['exclude_free_shipping'])) {
            $isFree = true;
            $finalCharge = 0.00;
        }

        // 3. Surcharges
        $expressFee = 0.00;
        if (!empty($options['is_express']) && ($settings['express_delivery_enabled'] ?? '0') == '1') {
            $expressFee = floatval($settings['express_delivery_charge'] ?? 60);
            $finalCharge += $expressFee;
        }

        $weightFee = 0.00;
        if (!empty($options['weight_kg']) && ($settings['delivery_weight_surcharge_enabled'] ?? '0') == '1') {
            $baseLimit = floatval($settings['delivery_base_weight_limit'] ?? 5);
            $perKgFee = floatval($settings['delivery_extra_per_kg_fee'] ?? 5);
            if ($options['weight_kg'] > $baseLimit) {
                $extraKg = ceil($options['weight_kg'] - $baseLimit);
                $weightFee = $extraKg * $perKgFee;
                $finalCharge += $weightFee;
            }
        }

        $weatherFee = 0.00;
        $weatherNotice = '';
        if (($settings['delivery_bad_weather_surcharge_enabled'] ?? '0') == '1') {
            $weatherFee = floatval($settings['delivery_bad_weather_fee'] ?? 0);
            $weatherNotice = $settings['delivery_bad_weather_notice'] ?? 'Due to severe weather conditions, an emergency logistics surcharge applies.';
            $finalCharge += $weatherFee;
        }

        $codFee = 0.00;
        if (!empty($options['is_cod']) && ($settings['delivery_cod_fee_enabled'] ?? '0') == '1') {
            $codFee = floatval($settings['delivery_cod_fee'] ?? 0);
            $finalCharge += $codFee;
        }

        $calcCharge = max(0, round($finalCharge, 2));

        return [
            'charge' => $calcCharge,
            'final_charge' => $calcCharge,
            'base_charge' => $baseCharge,
            'discount' => $isFree ? $baseCharge : 0.00,
            'is_free' => $isFree,
            'free_threshold' => $freeThreshold,
            'free_shipping_enabled' => $freeShippingEnabled,
            'amount_to_free' => max(0, $freeThreshold - $subtotal),
            'progress_percent' => ($freeThreshold > 0) ? min(100, round(($subtotal / $freeThreshold) * 100)) : 100,
            'free_badge_text' => !empty($settings['free_shipping_badge_text']) ? $settings['free_shipping_badge_text'] : 'Free Delivery',
            'progress_bar_enabled' => ($settings['free_shipping_progress_bar_enabled'] ?? '1') == '1',
            'charge_source' => $chargeSource,
            'express_fee' => $expressFee,
            'weight_fee' => $weightFee,
            'weather_fee' => $weatherFee,
            'weather_notice' => $weatherNotice,
            'cod_fee' => $codFee
        ];
    }

    /**
     * Get Spend-More Promotional Tiered Offers Data & Dynamic Motivators
     * 
     * @param float $subtotal Current cart subtotal
     * @param string $locale Current locale ('bn' or 'en')
     * @return array Calculated progress, active tiers, motivational message, and unlocked perks
     */
    public static function getSpendMoreOffersData($subtotal, $locale = 'bn') {
        $settingModel = new self();
        $settings = $settingModel->getAll();

        $subtotal = floatval($subtotal);
        $enabled = ($settings['spend_more_offers_enabled'] ?? '1') == '1';
        $freeShippingEnabled = ($settings['free_shipping_enabled'] ?? '1') == '1';
        $freeThreshold = floatval($settings['delivery_free_threshold'] ?? $settings['free_shipping_threshold'] ?? 500);

        // Load configured tiers or default tiers
        $rawTiers = json_decode($settings['spend_more_offers_tiers'] ?? '[]', true);
        if (empty($rawTiers) || !is_array($rawTiers)) {
            $rawTiers = [
                [
                    'id' => 1,
                    'min_amount' => $freeThreshold > 0 ? $freeThreshold : 500,
                    'type' => 'free_delivery',
                    'title' => ($locale === 'bn' ? 'ফ্রি ডেলিভারি' : 'Free Delivery'),
                    'title_en' => 'Free Delivery',
                    'desc' => ($locale === 'bn' ? 'ডেলিভারি চার্জ একদম ফ্রি' : '100% Free Home Delivery'),
                    'desc_en' => '100% Free Home Delivery',
                    'icon' => '🚚',
                    'enabled' => 1
                ],
                [
                    'id' => 2,
                    'min_amount' => max(1000, ($freeThreshold > 0 ? $freeThreshold * 2 : 1000)),
                    'type' => 'free_gift',
                    'title' => ($locale === 'bn' ? 'ফ্রি গিফট প্যাক' : 'Free Gift Pack'),
                    'title_en' => 'Free Gift Pack',
                    'desc' => ($locale === 'bn' ? 'একটি আকর্ষণীয় সারপ্রাইজ গিফট' : 'Special Surprise Grocery Gift'),
                    'desc_en' => 'Special Surprise Grocery Gift',
                    'icon' => '🎁',
                    'enabled' => 1
                ],
                [
                    'id' => 3,
                    'min_amount' => max(1500, ($freeThreshold > 0 ? $freeThreshold * 3 : 1500)),
                    'type' => 'discount_flat',
                    'title' => ($locale === 'bn' ? '৳১০০ ছাড়' : '৳100 Flat Discount'),
                    'title_en' => '৳100 Flat Discount',
                    'desc' => ($locale === 'bn' ? 'অর্ডারে ১০০ টাকা বিশেষ ডিসকাউন্ট' : 'Flat ৳100 off total order'),
                    'desc_en' => 'Flat ৳100 off total order',
                    'icon' => '🏷️',
                    'discount_val' => 100,
                    'enabled' => 1
                ]
            ];
        }

        // Filter and sort active tiers by min_amount ascending
        $activeTiers = [];
        foreach ($rawTiers as $t) {
            if (!empty($t['enabled']) && floatval($t['min_amount']) > 0) {
                $minAmt = floatval($t['min_amount']);
                $type = $t['type'] ?? 'custom';
                $title = ($locale === 'bn') ? ($t['title'] ?? $t['title_en'] ?? '') : ($t['title_en'] ?? $t['title'] ?? '');
                $desc = ($locale === 'bn') ? ($t['desc'] ?? $t['desc_en'] ?? '') : ($t['desc_en'] ?? $t['desc'] ?? '');
                $icon = !empty($t['icon']) ? $t['icon'] : '🎁';
                $rawRewardVal = $t['reward_value'] ?? $t['discount_val'] ?? $t['gift_item_name'] ?? $t['custom_perk'] ?? '';

                $isUnlocked = ($subtotal >= $minAmt);
                $amountNeeded = max(0, $minAmt - $subtotal);

                $discountVal = 0;
                $giftItemName = '';
                $customPerk = '';

                if ($type === 'discount_flat' || $type === 'discount_percent') {
                    $discountVal = floatval($rawRewardVal);
                } elseif ($type === 'free_gift') {
                    $giftItemName = trim((string)$rawRewardVal);
                } elseif ($type === 'custom') {
                    $customPerk = trim((string)$rawRewardVal);
                }

                $activeTiers[] = [
                    'id' => $t['id'] ?? uniqid(),
                    'min_amount' => $minAmt,
                    'type' => $type,
                    'title' => $title,
                    'title_en' => $t['title_en'] ?? $title,
                    'desc' => $desc,
                    'icon' => $icon,
                    'reward_value' => $rawRewardVal,
                    'discount_val' => $discountVal,
                    'gift_item_name' => $giftItemName,
                    'custom_perk' => $customPerk,
                    'is_unlocked' => $isUnlocked,
                    'amount_needed' => $amountNeeded,
                    'percent' => $minAmt > 0 ? min(100, round(($subtotal / $minAmt) * 100)) : 100
                ];
            }
        }

        usort($activeTiers, function($a, $b) {
            return $a['min_amount'] <=> $b['min_amount'];
        });

        $unlockedTiers = [];
        $nextTier = null;
        $highestUnlockedTier = null;
        $maxAmount = 0;
        $totalTiers = count($activeTiers);
        $sumSegmentPercents = 0;

        $prevMin = 0;
        for ($i = 0; $i < $totalTiers; $i++) {
            $t = &$activeTiers[$i];
            if ($t['min_amount'] > $maxAmount) {
                $maxAmount = $t['min_amount'];
            }

            // Calculate segment-specific progress (from prevMin to min_amount)
            $tierRange = max(1, $t['min_amount'] - $prevMin);
            if ($subtotal >= $t['min_amount']) {
                $segmentPercent = 100;
            } elseif ($subtotal <= $prevMin) {
                $segmentPercent = 0;
            } else {
                $segmentPercent = min(100, max(0, round((($subtotal - $prevMin) / $tierRange) * 100)));
            }
            $t['prev_amount'] = $prevMin;
            $t['segment_percent'] = $segmentPercent;
            $sumSegmentPercents += $segmentPercent;

            if ($t['is_unlocked']) {
                $t['is_current'] = false;
                $unlockedTiers[] = $t;
                $highestUnlockedTier = $t;
            } elseif ($nextTier === null) {
                $t['is_current'] = true;
                $nextTier = $t;
            } else {
                $t['is_current'] = false;
            }

            $prevMin = $t['min_amount'];
        }
        unset($t);

        // Synced overall percentage across all milestone segments
        $overallPercent = $totalTiers > 0 ? min(100, max(0, round($sumSegmentPercents / $totalTiers))) : 0;

        // Current target goal percentage for header badge (progress towards the immediate active milestone)
        $currentGoalPercent = $nextTier !== null ? $nextTier['segment_percent'] : 100;

        // Process unlocked rewards: discounts, gifts, perks, and free shipping
        $isFreeDeliveryUnlocked = false;
        $highestDiscount = 0.00;
        $unlockedGifts = [];
        $unlockedPerks = [];

        foreach ($unlockedTiers as $t) {
            if ($t['type'] === 'free_delivery') {
                $isFreeDeliveryUnlocked = true;
            } elseif ($t['type'] === 'discount_flat') {
                $highestDiscount = max($highestDiscount, floatval($t['discount_val']));
            } elseif ($t['type'] === 'discount_percent') {
                $pctVal = round(($subtotal * floatval($t['discount_val'])) / 100, 2);
                $highestDiscount = max($highestDiscount, $pctVal);
            } elseif ($t['type'] === 'free_gift') {
                $giftName = !empty($t['gift_item_name']) ? $t['gift_item_name'] : (!empty($t['desc']) ? $t['desc'] : $t['title']);
                if (!in_array($giftName, $unlockedGifts)) {
                    $unlockedGifts[] = $giftName;
                }
            } elseif ($t['type'] === 'custom') {
                $perkName = !empty($t['custom_perk']) ? $t['custom_perk'] : (!empty($t['desc']) ? $t['desc'] : $t['title']);
                if (!in_array($perkName, $unlockedPerks)) {
                    $unlockedPerks[] = $perkName;
                }
            }
        }

        if (!$isFreeDeliveryUnlocked && $freeShippingEnabled && $freeThreshold > 0 && $subtotal >= $freeThreshold) {
            $isFreeDeliveryUnlocked = true;
        }

        // Formulate dynamic motivational notification message
        $message = '';
        $headline = '';
        $currencySymbol = '৳';

        if (empty($activeTiers)) {
            $message = '';
        } elseif ($subtotal <= 0) {
            $first = $activeTiers[0];
            $headline = $locale === 'bn' ? 'স্পেশাল অফার ও রিওয়ার্ডস' : 'Special Offers & Rewards';
            $message = $locale === 'bn' 
                ? "{$currencySymbol}" . number_format($first['min_amount'], 0) . " বা বেশি কেনাকাটায় পাচ্ছেন {$first['title']}! {$first['icon']}"
                : "Shop for {$currencySymbol}" . number_format($first['min_amount'], 0) . " or more to unlock {$first['title']}! {$first['icon']}";
        } elseif ($nextTier !== null) {
            $neededFormatted = number_format($nextTier['amount_needed'], 0);
            if ($highestUnlockedTier !== null) {
                $headline = $locale === 'bn' ? '🎉 অফার আনলক হয়েছে!' : '🎉 Reward Unlocked!';
                $message = $locale === 'bn'
                    ? "অভিনন্দন! আপনি <strong>{$highestUnlockedTier['title']}</strong> পেয়েছেন! আর মাত্র <strong>{$currencySymbol}{$neededFormatted}</strong> যোগ করলেই পাচ্ছেন <strong>{$nextTier['title']}</strong>! {$nextTier['icon']}"
                    : "Great! You unlocked <strong>{$highestUnlockedTier['title']}</strong>! Add <strong>{$currencySymbol}{$neededFormatted}</strong> more for <strong>{$nextTier['title']}</strong>! {$nextTier['icon']}";
            } else {
                $headline = $locale === 'bn' ? 'অফার আনলক করুন' : 'Unlock Your Offer';
                $message = $locale === 'bn'
                    ? "আর মাত্র <strong>{$currencySymbol}{$neededFormatted}</strong> টাকার পণ্য যোগ করলেই পাচ্ছেন <strong>{$nextTier['title']}</strong>! {$nextTier['icon']}"
                    : "Add <strong>{$currencySymbol}{$neededFormatted}</strong> more to unlock <strong>{$nextTier['title']}</strong>! {$nextTier['icon']}";
            }
        } else {
            // All tiers unlocked!
            $headline = $locale === 'bn' ? '🎉 অভিনন্দন! সব অফার আনলকড!' : '🎉 All Rewards Unlocked!';
            $message = $locale === 'bn'
                ? "অসাধারণ! আপনি সকল স্পেশাল অফার ও উপহার আনলক করেছেন! 🌟"
                : "Amazing! You have unlocked all exclusive rewards and free delivery! 🌟";
        }

        return [
            'enabled' => $enabled,
            'subtotal' => $subtotal,
            'max_amount' => $maxAmount,
            'overall_percent' => $overallPercent,
            'current_goal_percent' => $currentGoalPercent,
            'tiers' => $activeTiers,
            'unlocked_tiers' => $unlockedTiers,
            'highest_unlocked' => $highestUnlockedTier,
            'next_tier' => $nextTier,
            'is_free_delivery_unlocked' => $isFreeDeliveryUnlocked,
            'discount_amount' => $highestDiscount,
            'unlocked_gifts' => $unlockedGifts,
            'unlocked_perks' => $unlockedPerks,
            'headline' => $headline,
            'message' => $message,
            'motivational_message' => $message,
            'is_all_unlocked' => ($nextTier === null && !empty($activeTiers)),
            'raw_tiers' => $rawTiers
        ];
    }
}
