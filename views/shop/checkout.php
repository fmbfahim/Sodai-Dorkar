<?php 
ob_start(); 
use Core\Lang;
$__ = function($key, $r = []) { return Lang::get($key, $r); };

$minOrder = floatval($ecommerceSettings['min_order_amount'] ?? 0);
$maxOrder = floatval($ecommerceSettings['max_order_amount'] ?? 0);
$isUnderMin = ($minOrder > 0 && $subtotal < $minOrder);
$isOverMax = ($maxOrder > 0 && $subtotal > $maxOrder);

$milestoneDiscount = floatval($spendMoreOffers['discount_amount'] ?? 0);
$baseCharge = $deliveryCalc['base_charge'] ?? 30.00;
$currentCharge = $deliveryCalc['charge'] ?? 0.00;
$expressCharge = floatval($ecommerceSettings['express_delivery_charge'] ?? 60.00);
$initialTotal = max(0, round($subtotal - $milestoneDiscount + $currentCharge, 2));
?>

<div class="bg-gray-50 py-10 min-h-screen">
    <div class="container mx-auto px-4 max-w-5xl">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-800 mb-6"><?= $__('checkout_title') ?></h1>
        
        <!-- Error Alerts -->
        <?php if (isset($_GET['error']) && $_GET['error'] === 'missing_fields'): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-2xl p-4 mb-6 flex items-center gap-3 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
            <span class="font-medium text-sm"><?= Lang::locale() === 'bn' ? 'সব তথ্য পূরণ করুন।' : 'Please fill in all required fields.' ?></span>
        </div>
        <?php endif; ?>

        <!-- Order Limits Validation -->
        <?php if ($isUnderMin): ?>
        <div class="bg-amber-50 border border-amber-200 text-amber-900 rounded-2xl p-4 mb-6 flex items-center gap-3 shadow-sm">
            <span class="text-xl">⚠️</span>
            <div class="text-sm">
                <span class="font-bold">Minimum Order Requirement:</span> 
                <?= Lang::locale() === 'bn' ? 'অর্ডার সম্পন্ন করতে ন্যূনতম ৳' . number_format($minOrder, 2) . ' টাকার পণ্য প্রয়োজন। আর ৳' . number_format($minOrder - $subtotal, 2) . ' টাকার পণ্য যোগ করুন।' : 'Minimum order amount is ৳' . number_format($minOrder, 2) . '. Please add ৳' . number_format($minOrder - $subtotal, 2) . ' more to proceed.' ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($isOverMax): ?>
        <div class="bg-red-50 border border-red-200 text-red-900 rounded-2xl p-4 mb-6 flex items-center gap-3 shadow-sm">
            <span class="text-xl">🛑</span>
            <div class="text-sm">
                <span class="font-bold">Order Limit Exceeded:</span> 
                <?= Lang::locale() === 'bn' ? 'সর্বোচ্চ অর্ডারের পরিমাণ ৳' . number_format($maxOrder, 2) . ' ছাড়িয়ে গেছে।' : 'Maximum allowed order amount is ৳' . number_format($maxOrder, 2) . '.' ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Spend More Offers & Free Delivery Milestone Banner -->
        <?php if (!empty($spendMoreOffers['enabled']) && !empty($spendMoreOffers['tiers'])): ?>
            <div class="bg-gradient-to-r from-emerald-50/90 via-teal-50/60 to-emerald-50/90 border border-emerald-200/80 rounded-xl p-2 sm:p-2.5 mb-5 shadow-2xs space-y-1.5">
                <!-- Header Line: Icon + Short Motivational Text + Percentage Badge -->
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-1.5 min-w-0">
                        <span class="text-sm shrink-0">
                            <?= !empty($spendMoreOffers['is_all_unlocked']) ? '🎉' : (!empty($spendMoreOffers['next_tier']['icon']) ? $spendMoreOffers['next_tier']['icon'] : '🚚') ?>
                        </span>
                        <p class="text-[11px] sm:text-xs font-bold text-gray-800 truncate leading-tight">
                            <?= strip_tags($spendMoreOffers['motivational_message']) ?>
                        </p>
                    </div>
                    <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-emerald-600 text-white shrink-0 shadow-2xs">
                        <?= intval($spendMoreOffers['current_goal_percent'] ?? 0) ?>% <?= Lang::locale() === 'bn' ? 'অর্জিত' : 'Done' ?>
                    </span>
                </div>

                <!-- Synchronized Multi-Step Milestone Stepper -->
                <div class="grid gap-1.5 sm:gap-2 items-stretch" style="grid-template-columns: repeat(<?= count($spendMoreOffers['tiers']) ?>, minmax(0, 1fr));">
                    <?php foreach ($spendMoreOffers['tiers'] as $tier): 
                        $isUnlocked = !empty($tier['is_unlocked']);
                        $isCurrent = !empty($tier['is_current']);
                        $segPercent = intval($tier['segment_percent'] ?? ($isUnlocked ? 100 : 0));
                        $needed = isset($tier['amount_needed']) ? round($tier['amount_needed']) : max(0, $tier['min_amount'] - $subtotal);
                    ?>
                        <div class="p-1.5 sm:p-2 rounded-lg transition-all <?= $isUnlocked ? 'bg-emerald-100/60 border border-emerald-200/80' : ($isCurrent ? 'bg-white border border-emerald-500 shadow-2xs ring-1 ring-emerald-200' : 'bg-white/50 border border-gray-200/60 opacity-60') ?>">
                            <!-- Live Segment Micro Progress Bar -->
                            <div class="w-full bg-gray-200/80 rounded-full h-1 overflow-hidden mb-1">
                                <div class="h-full bg-gradient-to-r from-emerald-500 to-green-500 rounded-full transition-all duration-500" 
                                     style="width: <?= $segPercent ?>%;"></div>
                            </div>

                            <!-- Amount & Status Row -->
                            <div class="flex items-center justify-between gap-1 leading-tight">
                                <span class="text-[10px] sm:text-[11px] font-black <?= $isUnlocked ? 'text-emerald-800' : ($isCurrent ? 'text-gray-900' : 'text-gray-500') ?>">
                                    <?= $__('currency') ?><?= number_format($tier['min_amount'], 0) ?>
                                </span>
                                <?php if ($isUnlocked): ?>
                                    <span class="text-[9px] font-black text-emerald-700 flex items-center gap-0.5 shrink-0">
                                        <span>✓</span> <span class="hidden sm:inline"><?= Lang::locale() === 'bn' ? 'অর্জিত' : 'Done' ?></span>
                                    </span>
                                <?php elseif ($isCurrent): ?>
                                    <span class="text-[9px] font-bold text-amber-700 shrink-0 whitespace-nowrap">
                                        <?= Lang::locale() === 'bn' ? 'আর ৳' . $needed : '৳' . $needed . ' left' ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-[9px] text-gray-400 font-medium shrink-0">
                                        <?= Lang::locale() === 'bn' ? 'লকড' : 'Locked' ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Benefit Title -->
                            <div class="text-[9px] sm:text-[10px] font-semibold <?= $isUnlocked ? 'text-emerald-700' : ($isCurrent ? 'text-gray-700' : 'text-gray-400') ?> truncate mt-0.5" title="<?= htmlspecialchars($tier['title']) ?>">
                                <?= $tier['icon'] ? htmlspecialchars($tier['icon']) . ' ' : '' ?><?= htmlspecialchars($tier['title']) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php elseif (!empty($deliveryCalc['free_shipping_enabled'])): ?>
            <?php if (!empty($deliveryCalc['is_free'])): ?>
                <div class="bg-gradient-to-r from-emerald-500 to-green-600 text-white rounded-2xl p-4 mb-6 flex items-center justify-between shadow-md">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">🎉</span>
                        <div>
                            <p class="font-extrabold text-sm sm:text-base leading-tight">
                                <?= htmlspecialchars($deliveryCalc['free_badge_text']) ?>
                            </p>
                            <p class="text-xs text-emerald-100 mt-0.5">
                                <?= Lang::locale() === 'bn' ? 'আপনার এই অর্ডারে কোনো ডেলিভারি চার্জ লাগছে না!' : 'Your order qualifies for 100% Free Shipping!' ?>
                            </p>
                        </div>
                    </div>
                    <span class="bg-white/20 backdrop-blur-md px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider">
                        ৳0.00 FREE
                    </span>
                </div>
            <?php elseif (!empty($deliveryCalc['progress_bar_enabled']) && ($deliveryCalc['amount_to_free'] ?? 0) > 0): ?>
                <div class="bg-white border border-emerald-100 rounded-2xl p-4 mb-6 shadow-sm">
                    <div class="flex items-center justify-between text-xs sm:text-sm font-bold text-gray-800 mb-2">
                        <div class="flex items-center gap-2">
                            <span class="text-base">🚚</span>
                            <span>
                                <?= Lang::locale() === 'bn' ? 'ফ্রি ডেলিভারি পেতে আর মাত্র ' : 'Add ' ?>
                                <span class="text-emerald-600 font-extrabold">৳<?= number_format($deliveryCalc['amount_to_free'], 2) ?></span>
                                <?= Lang::locale() === 'bn' ? ' টাকার পণ্য যোগ করুন!' : ' more for FREE Delivery!' ?>
                            </span>
                        </div>
                        <span class="text-emerald-700 text-xs bg-emerald-50 px-2.5 py-0.5 rounded-full font-extrabold">
                            <?= intval($deliveryCalc['progress_percent'] ?? 0) ?>%
                        </span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-emerald-500 to-green-500 h-2.5 rounded-full transition-all duration-500" 
                             style="width: <?= min(100, intval($deliveryCalc['progress_percent'] ?? 0)) ?>%"></div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Weather Surcharge Alert Notice -->
        <?php if (!empty($deliveryCalc['weather_notice'])): ?>
        <div class="bg-amber-50/80 border border-amber-200 text-amber-900 rounded-2xl p-4 mb-6 flex items-start gap-3 shadow-sm">
            <span class="text-xl shrink-0 mt-0.5">⛈️</span>
            <div class="text-xs sm:text-sm leading-relaxed">
                <span class="font-bold">Logistics Notice:</span> <?= htmlspecialchars($deliveryCalc['weather_notice']) ?>
                <span class="inline-block font-bold text-amber-700 ml-1">(+৳<?= number_format($deliveryCalc['weather_fee'] ?? 0, 2) ?> emergency fee applied)</span>
            </div>
        </div>
        <?php endif; ?>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Checkout Main Form -->
            <div class="lg:w-2/3 space-y-6">
                <form action="/sodai-dorkar/public/checkout/place-order" method="POST" id="checkout-form">
                    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                    
                    <!-- 1. Customer & Delivery Destination Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-lg sm:text-xl font-bold text-gray-800 flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-xl bg-green-100 text-green-700 flex items-center justify-center font-bold">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </span>
                                <?= $__('checkout_delivery_info') ?>
                            </h2>
                            
                            <!-- Delivery Charge Tier Tag -->
                            <?php if (($deliveryCalc['charge_source'] ?? '') === 'point'): ?>
                                <span class="inline-flex items-center gap-1 text-xs font-bold px-3 py-1 rounded-full bg-purple-100 text-purple-800">
                                    📍 Point Rate
                                </span>
                            <?php elseif (($deliveryCalc['charge_source'] ?? '') === 'area'): ?>
                                <span class="inline-flex items-center gap-1 text-xs font-bold px-3 py-1 rounded-full bg-blue-100 text-blue-800">
                                    🏛️ Union Rate
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1 text-xs font-medium px-3 py-1 rounded-full bg-gray-100 text-gray-600">
                                    🚚 Standard Base
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="bg-gray-50/70 rounded-xl p-5 border border-gray-100">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-xs text-gray-400 font-medium mb-1">Customer Name</p>
                                    <p class="font-bold text-gray-800"><?= htmlspecialchars($customer['name'] ?? '') ?></p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-medium mb-1">Contact Phone</p>
                                    <p class="font-bold text-gray-800"><?= htmlspecialchars($customer['phone'] ?? '') ?></p>
                                </div>
                                <div class="md:col-span-2 pt-2 border-t border-gray-200/60">
                                    <p class="text-xs text-gray-400 font-medium mb-1">Delivery Destination</p>
                                    <p class="font-semibold text-gray-800 leading-relaxed">
                                        <?= htmlspecialchars($customer['address_details'] ?? '') ?><br>
                                        <span class="text-xs text-gray-500">
                                            <?php if (!empty($customer['point_name'])): ?>
                                                <span class="text-purple-700 font-bold">Drop-off: <?= htmlspecialchars($customer['point_name']) ?></span> •
                                            <?php endif; ?>
                                            <?php if (!empty($customer['zone_name'])): ?>
                                                <span><?= htmlspecialchars($customer['zone_name']) ?></span> •
                                            <?php endif; ?>
                                            <span><?= htmlspecialchars($customer['area_name'] ?? '') ?></span>
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Delivery Time Slots (If Enabled) -->
                        <?php if (($ecommerceSettings['time_slots_enabled'] ?? '0') == '1'): ?>
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <label class="block text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                                <span class="text-base">🕒</span>
                                <span>Preferred Delivery Window</span>
                            </label>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <label class="relative flex flex-col p-3 border border-gray-200 rounded-xl cursor-pointer hover:border-green-500 hover:bg-green-50/20 transition-all has-[:checked]:border-green-600 has-[:checked]:bg-green-50/50 has-[:checked]:ring-1 has-[:checked]:ring-green-600">
                                    <input type="radio" name="delivery_slot" value="Morning (08:00 AM - 12:00 PM)" checked class="sr-only">
                                    <span class="text-xs font-bold text-gray-800">Morning</span>
                                    <span class="text-[11px] text-gray-500 mt-0.5">08:00 AM - 12:00 PM</span>
                                </label>

                                <label class="relative flex flex-col p-3 border border-gray-200 rounded-xl cursor-pointer hover:border-green-500 hover:bg-green-50/20 transition-all has-[:checked]:border-green-600 has-[:checked]:bg-green-50/50 has-[:checked]:ring-1 has-[:checked]:ring-green-600">
                                    <input type="radio" name="delivery_slot" value="Afternoon (12:00 PM - 04:00 PM)" class="sr-only">
                                    <span class="text-xs font-bold text-gray-800">Afternoon</span>
                                    <span class="text-[11px] text-gray-500 mt-0.5">12:00 PM - 04:00 PM</span>
                                </label>

                                <label class="relative flex flex-col p-3 border border-gray-200 rounded-xl cursor-pointer hover:border-green-500 hover:bg-green-50/20 transition-all has-[:checked]:border-green-600 has-[:checked]:bg-green-50/50 has-[:checked]:ring-1 has-[:checked]:ring-green-600">
                                    <input type="radio" name="delivery_slot" value="Evening (04:00 PM - 08:00 PM)" class="sr-only">
                                    <span class="text-xs font-bold text-gray-800">Evening</span>
                                    <span class="text-[11px] text-gray-500 mt-0.5">04:00 PM - 08:00 PM</span>
                                </label>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- 3. Express 30-Minute Priority Toggle (If Enabled) -->
                        <?php if (($ecommerceSettings['express_delivery_enabled'] ?? '0') == '1'): ?>
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <label class="relative flex items-center justify-between p-4 rounded-xl border border-orange-200 bg-orange-50/40 cursor-pointer hover:bg-orange-50/70 transition-all">
                                <div class="flex items-center gap-3 pr-4">
                                    <span class="text-2xl">⚡</span>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-extrabold text-orange-900">Express 30-Minute Priority Delivery</span>
                                            <span class="bg-orange-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full uppercase tracking-wider">
                                                +৳<?= number_format($expressCharge, 2) ?>
                                            </span>
                                        </div>
                                        <p class="text-xs text-orange-700/80 mt-0.5">
                                            Instant prioritized picking & immediate dispatch to your doorstep. Daily cutoff: <?= htmlspecialchars($ecommerceSettings['express_delivery_cutoff'] ?? '08:00 PM') ?>
                                        </p>
                                    </div>
                                </div>
                                <label class="custom-toggle toggle-orange" for="is_express">
                                    <input type="checkbox" id="is_express" name="is_express" value="1" onchange="updateCheckoutTotals()">
                                    <span class="toggle-track" id="express-toggle-track">
                                        <span class="toggle-thumb"></span>
                                    </span>
                                </label>
                            </label>
                        </div>
                        <?php endif; ?>

                        <!-- 4. Payment Method -->
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <h2 class="text-lg sm:text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                                <span class="w-8 h-8 rounded-xl bg-green-100 text-green-700 flex items-center justify-center font-bold">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </span>
                                <?= $__('checkout_payment_method') ?>
                            </h2>
                            
                            <div class="bg-green-50/70 border border-green-200 rounded-xl p-4 flex items-center gap-4">
                                <input type="radio" checked id="cod" name="payment_method" value="cod" class="w-5 h-5 text-green-600 focus:ring-green-500">
                                <label for="cod" class="flex-grow font-bold text-green-900 cursor-pointer text-sm">
                                    <?= $__('checkout_cod') ?>
                                    <span class="block text-xs text-green-700 font-normal mt-0.5">
                                        <?= $__('checkout_cod_desc') ?>
                                        <?php if (!empty($deliveryCalc['cod_fee'])): ?>
                                            (Includes ৳<?= number_format($deliveryCalc['cod_fee'], 2) ?> handling fee)
                                        <?php endif; ?>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Order Summary Sidebar -->
            <div class="lg:w-1/3">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
                    <h2 class="text-lg font-extrabold text-gray-800 mb-5 flex items-center justify-between">
                        <span><?= $__('checkout_your_order') ?></span>
                        <span class="text-xs bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full font-bold">
                            <?= count($cart) ?> items
                        </span>
                    </h2>
                    
                    <!-- Cart Item Breakdown -->
                    <ul class="divide-y divide-gray-100 mb-6 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                        <?php 
                        foreach ($cart as $item): 
                            $itemTotal = $item['price'] * $item['quantity'];
                        ?>
                            <li class="py-3 flex justify-between items-start gap-2">
                                <div class="flex flex-col">
                                    <span class="text-xs sm:text-sm font-semibold text-gray-800 line-clamp-1" title="<?= htmlspecialchars($item['name']) ?>">
                                        <?= htmlspecialchars($item['name']) ?>
                                    </span>
                                    <span class="text-xs text-gray-400 mt-0.5">
                                        <?= $item['quantity'] ?> × <?= $__('currency') ?><?= number_format($item['price'], 2) ?>
                                        <?php if (!empty($item['variant_title'])): ?>
                                            <span class="text-[10px] text-gray-400">(<?= htmlspecialchars($item['variant_title']) ?>)</span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <span class="font-bold text-gray-800 text-xs sm:text-sm whitespace-nowrap">
                                    <?= $__('currency') ?><?= number_format($itemTotal, 2) ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <!-- Unlocked Rewards & Offers (If Any) -->
                    <?php if (!empty($spendMoreOffers['unlocked_tiers'])): ?>
                    <div class="mt-4 p-3 bg-emerald-50/80 border border-emerald-200/80 rounded-xl space-y-2">
                        <div class="flex items-center gap-1.5 text-xs font-black text-emerald-900">
                            <span>🎉</span>
                            <span><?= Lang::locale() === 'bn' ? 'আপনার অর্জিত অফার ও পুরস্কার:' : 'Your Unlocked Rewards:' ?></span>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            <?php foreach ($spendMoreOffers['unlocked_tiers'] as $uTier): ?>
                                <span class="inline-flex items-center gap-1 text-[11px] font-extrabold px-2.5 py-1 rounded-lg bg-emerald-600 text-white shadow-2xs">
                                    <span><?= $uTier['icon'] ?: '✓' ?></span>
                                    <span><?= htmlspecialchars($uTier['title']) ?></span>
                                    <?php if ($uTier['type'] === 'free_gift' && !empty($uTier['gift_item_name'])): ?>
                                        <span class="bg-emerald-700/80 px-1.5 py-0.5 rounded text-[10px]">(<?= htmlspecialchars($uTier['gift_item_name']) ?>)</span>
                                    <?php elseif ($uTier['type'] === 'custom' && !empty($uTier['custom_perk'])): ?>
                                        <span class="bg-emerald-700/80 px-1.5 py-0.5 rounded text-[10px]">(<?= htmlspecialchars($uTier['custom_perk']) ?>)</span>
                                    <?php elseif ($uTier['type'] === 'discount_flat' && !empty($uTier['discount_val'])): ?>
                                        <span class="bg-emerald-700/80 px-1.5 py-0.5 rounded text-[10px]">(৳<?= number_format($uTier['discount_val'], 0) ?> ছাড়)</span>
                                    <?php elseif ($uTier['type'] === 'discount_percent' && !empty($uTier['discount_val'])): ?>
                                        <span class="bg-emerald-700/80 px-1.5 py-0.5 rounded text-[10px]">(<?= number_format($uTier['discount_val'], 0) ?>% ছাড়)</span>
                                    <?php endif; ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                        <?php if (!empty($spendMoreOffers['unlocked_gifts'])): ?>
                            <div class="text-[11px] font-bold text-emerald-900 bg-white/80 px-2.5 py-1.5 rounded-lg border border-emerald-200 flex items-center gap-1.5">
                                <span>🎁</span>
                                <span><?= Lang::locale() === 'bn' ? 'অর্ডারের সাথে ফ্রি গিফট প্যাক পাবেন:' : 'Free Gift Included:' ?> <strong><?= htmlspecialchars(implode(', ', $spendMoreOffers['unlocked_gifts'])) ?></strong></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($spendMoreOffers['unlocked_perks'])): ?>
                            <div class="text-[11px] font-bold text-teal-900 bg-white/80 px-2.5 py-1.5 rounded-lg border border-teal-200 flex items-center gap-1.5">
                                <span>🌟</span>
                                <span><?= Lang::locale() === 'bn' ? 'বিশেষ সুবিধা:' : 'Special Perk:' ?> <strong><?= htmlspecialchars(implode(', ', $spendMoreOffers['unlocked_perks'])) ?></strong></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Charges Breakdown -->
                    <div class="space-y-3 text-xs sm:text-sm text-gray-600 mb-6 border-t border-gray-100 pt-5">
                        <div class="flex justify-between">
                            <span><?= $__('checkout_subtotal') ?></span>
                            <span class="font-bold text-gray-800"><?= $__('currency') ?><?= number_format($subtotal, 2) ?></span>
                        </div>

                        <!-- Milestone Offer Discount (If Any) -->
                        <?php if ($milestoneDiscount > 0): ?>
                        <div class="flex justify-between items-center text-emerald-700 bg-emerald-50/80 px-2.5 py-1.5 rounded-xl border border-emerald-200/80 font-bold" id="milestone-discount-row">
                            <span class="flex items-center gap-1.5">
                                <span>🏷️</span>
                                <span><?= Lang::locale() === 'bn' ? 'অফার বিশেষ ছাড়' : 'Offer Milestone Discount' ?></span>
                            </span>
                            <span class="font-black text-emerald-800 text-xs sm:text-sm">- <?= $__('currency') ?><?= number_format($milestoneDiscount, 2) ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Base Delivery Fee -->
                        <div class="flex justify-between items-center">
                            <span><?= $__('checkout_delivery_fee') ?></span>
                            <span class="font-semibold text-gray-800" id="display-delivery-fee">
                                <?php if (!empty($deliveryCalc['is_free'])): ?>
                                    <span class="line-through text-gray-400 text-xs mr-1"><?= $__('currency') ?><?= number_format($baseCharge, 2) ?></span>
                                    <span class="text-green-600 font-bold"><?= $__('currency') ?>0.00 <span class="text-[11px] ml-0.5 font-bold uppercase">(<?= $__('checkout_delivery_free') ?>)</span></span>
                                <?php else: ?>
                                    <?= $__('currency') ?><?= number_format($baseCharge, 2) ?>
                                <?php endif; ?>
                            </span>
                        </div>

                        <!-- Heavy Weight Surcharge (If Any) -->
                        <?php if (!empty($deliveryCalc['weight_fee'])): ?>
                        <div class="flex justify-between items-center text-amber-700 bg-amber-50/60 px-2 py-1 rounded-lg">
                            <span>⚖️ Heavy Order Surcharge</span>
                            <span class="font-bold">+<?= $__('currency') ?><?= number_format($deliveryCalc['weight_fee'], 2) ?></span>
                        </div>
                        <?php endif; ?>

                        <!-- Weather Emergency Fee (If Any) -->
                        <?php if (!empty($deliveryCalc['weather_fee'])): ?>
                        <div class="flex justify-between items-center text-amber-800 bg-amber-50/60 px-2 py-1 rounded-lg">
                            <span>⛈️ Bad Weather Fee</span>
                            <span class="font-bold">+<?= $__('currency') ?><?= number_format($deliveryCalc['weather_fee'], 2) ?></span>
                        </div>
                        <?php endif; ?>

                        <!-- COD Handling Fee (If Any) -->
                        <?php if (!empty($deliveryCalc['cod_fee'])): ?>
                        <div class="flex justify-between items-center text-gray-600">
                            <span>💵 COD Handling</span>
                            <span class="font-semibold">+<?= $__('currency') ?><?= number_format($deliveryCalc['cod_fee'], 2) ?></span>
                        </div>
                        <?php endif; ?>

                        <!-- Express Delivery Fee Dynamic Row -->
                        <div id="express-fee-row" class="hidden justify-between items-center text-orange-700 bg-orange-50/60 px-2 py-1 rounded-lg">
                            <span>⚡ Express 30-Min Surcharge</span>
                            <span class="font-bold">+<?= $__('currency') ?><?= number_format($expressCharge, 2) ?></span>
                        </div>
                    </div>
                    
                    <!-- Grand Total -->
                    <div class="flex justify-between items-baseline mb-6 border-t border-gray-100 pt-5">
                        <div>
                            <span class="text-base sm:text-lg font-black text-gray-800"><?= $__('checkout_total') ?></span>
                            <span class="block text-[11px] text-gray-400">Includes all taxes & delivery fees</span>
                        </div>
                        <span class="text-2xl sm:text-3xl font-black text-green-600" id="display-grand-total">
                            <?= $__('currency') ?><?= number_format($initialTotal, 2) ?>
                        </span>
                    </div>
                    
                    <!-- Order Submit Button -->
                    <button type="submit" id="confirm-order-btn" form="checkout-form" 
                            <?= ($isUnderMin || $isOverMax) ? 'disabled' : '' ?>
                            class="block w-full bg-green-600 text-white text-center font-extrabold py-4 px-6 rounded-xl hover:bg-green-700 transition-all shadow-md hover:shadow-lg hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0">
                        <span id="confirm-btn-text">
                            <?= $__('checkout_confirm_order') ?> (<span id="btn-total-text"><?= $__('currency') ?><?= number_format($initialTotal, 2) ?></span>)
                        </span>
                        <span id="confirm-btn-loading" class="hidden items-center justify-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <?= Lang::locale() === 'bn' ? 'অর্ডার প্রসেস হচ্ছে...' : 'Processing Order...' ?>
                        </span>
                    </button>
                    
                    <p class="text-[11px] text-center text-gray-400 mt-4 leading-normal"><?= $__('checkout_terms') ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const baseSubtotal = <?= floatval($subtotal) ?>;
const milestoneDiscount = <?= floatval($milestoneDiscount) ?>;
const baseDeliveryCharge = <?= floatval($currentCharge) ?>;
const expressFeeAmount = <?= floatval($expressCharge) ?>;
const currencySymbol = '<?= $__('currency') ?>';

function updateCheckoutTotals() {
    const expressCheckbox = document.getElementById('is_express');
    const expressToggleTrack = document.getElementById('express-toggle-track');
    const expressRow = document.getElementById('express-fee-row');
    const grandTotalDisplay = document.getElementById('display-grand-total');
    const btnTotalText = document.getElementById('btn-total-text');
    
    let totalDelivery = baseDeliveryCharge;
    
    if (expressCheckbox && expressCheckbox.checked) {
        totalDelivery += expressFeeAmount;
        if (expressToggleTrack) expressToggleTrack.classList.add('is-checked');
        if (expressRow) {
            expressRow.classList.remove('hidden');
            expressRow.classList.add('flex');
        }
    } else {
        if (expressToggleTrack) expressToggleTrack.classList.remove('is-checked');
        if (expressRow) {
            expressRow.classList.remove('flex');
            expressRow.classList.add('hidden');
        }
    }
    
    const grandTotal = Math.max(0, baseSubtotal - milestoneDiscount + totalDelivery);
    const formattedTotal = currencySymbol + grandTotal.toFixed(2);
    
    if (grandTotalDisplay) {
        grandTotalDisplay.innerText = formattedTotal;
    }
    if (btnTotalText) {
        btnTotalText.innerText = formattedTotal;
    }
}

document.getElementById('checkout-form').addEventListener('submit', function(e) {
    const btn = document.getElementById('confirm-order-btn');
    const btnText = document.getElementById('confirm-btn-text');
    const btnLoading = document.getElementById('confirm-btn-loading');
    
    if (btn.disabled) {
        e.preventDefault();
        return;
    }
    
    btn.disabled = true;
    btnText.classList.add('hidden');
    btnLoading.classList.remove('hidden');
    btnLoading.classList.add('flex');
    
    setTimeout(function() {
        btn.disabled = false;
        btnText.classList.remove('hidden');
        btnLoading.classList.add('hidden');
        btnLoading.classList.remove('flex');
    }, 10000);
});
</script>

<?php 
$content = ob_get_clean();
require 'layout.php';
?>
