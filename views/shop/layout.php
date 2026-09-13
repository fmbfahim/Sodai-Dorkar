<?php 
use Core\Lang;
Lang::init();
$__ = function($key, $r = []) { return Lang::get($key, $r); };
$locale = Lang::locale();
$otherLocale = Lang::otherLocale();

// Store configuration settings
try {
    $storeSettingModel = new \Models\Setting();
    $storeSettings = $storeSettingModel->getAll();
} catch (\Throwable $e) {
    $storeSettings = [];
}

// Cart statistics
$cart = $_SESSION['cart'] ?? [];
$cartCount = 0;
$cartTotal = 0;
foreach ($cart as $item) {
    $cartCount += (int)($item['quantity'] ?? 0);
    $cartTotal += (float)($item['price'] ?? 0) * (int)($item['quantity'] ?? 0);
}
$spendMoreOffers = \Models\Setting::getSpendMoreOffersData($cartTotal, $locale);
?>
<!DOCTYPE html>
<html lang="<?= $locale ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $__('site_name') ?> - <?= $__('site_tagline') ?></title>
    <meta name="description" content="<?= $__('hero_subtitle') ?>">
    <?php if (isset($storeSettings['seo_index_allow']) && $storeSettings['seo_index_allow'] === '0'): ?>
    <meta name="robots" content="noindex, nofollow">
    <?php endif; ?>
    <meta name="csrf-token" content="<?= \Core\CSRF::token() ?>">
    <link href="/sodai-dorkar/public/css/output.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;500;600;700;800;900&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', 'Noto Sans Bengali', sans-serif; }
        <?php if ($locale === 'bn'): ?>
        body, button, input, select, textarea { font-family: 'Noto Sans Bengali', 'Outfit', sans-serif; }
        <?php endif; ?>

        /* Toast animation */
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
        .toast-enter { animation: slideInRight 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .toast-exit { animation: slideOutRight 0.3s ease-in forwards; }
        
        /* Sticky Cart Pulse / Bump Animation */
        @keyframes cartBumpMobile {
            0% { transform: scale(1); }
            40% { transform: scale(1.05); }
            70% { transform: scale(0.98); }
            100% { transform: scale(1); }
        }
        @keyframes cartBumpDesktop {
            0% { transform: translateY(-50%) scale(1); }
            40% { transform: translateY(-50%) translateX(-8px) scale(1.12); }
            70% { transform: translateY(-50%) translateX(-4px) scale(0.96); }
            100% { transform: translateY(-50%) scale(1); }
        }
        .cart-bump { animation: cartBumpMobile 0.45s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        @media (min-width: 768px) {
            .cart-bump { animation: cartBumpDesktop 0.55s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        }

        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #9ca3af; }

        /* Hide scrollbars for sliders while allowing smooth scroll */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Smooth category tabs */
        .category-tab { transition: all 0.2s ease; }
        .category-tab.active { 
            background-color: #059669; 
            color: white; 
            box-shadow: 0 4px 14px 0 rgba(5, 150, 105, 0.39);
        }
    </style>
</head>
<body class="bg-gray-50 text-secondary-800 antialiased min-h-screen flex flex-col font-sans">
<?php
// Fallback for categories if not passed from controller
if (!isset($allCategories) || empty($allCategories)) {
    try {
        $cfg = require __DIR__ . '/../../config/database.php';
        $dbHelper = new \Core\Database($cfg);
        $allCategories = $dbHelper->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
    } catch (\Throwable $e) {
        $allCategories = [];
    }
}

$childrenMap = $childrenMap ?? [];
$catById = $catById ?? [];
if (empty($catById) && !empty($allCategories)) {
    foreach ($allCategories as $c) {
        $catById[$c['id']] = $c;
        $p = !empty($c['parent_id']) ? (int)$c['parent_id'] : 0;
        if (!isset($childrenMap[$p])) $childrenMap[$p] = [];
        $childrenMap[$p][] = (int)$c['id'];
    }
}

if (!isset($mainCategories) || empty($mainCategories)) {
    $rootIds = $childrenMap[0] ?? [];
    $mainCatIds = (count($rootIds) === 1 && isset($childrenMap[$rootIds[0]])) ? $childrenMap[$rootIds[0]] : $rootIds;
    $mainCategories = [];
    foreach ($mainCatIds as $mId) {
        if (isset($catById[$mId])) {
            $mainCategories[] = $catById[$mId];
        }
    }
}
?>
    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-6 right-6 z-[100] flex flex-col gap-3 pointer-events-none"></div>

    <!-- Top Announcement Bar (Configurable from Admin Ecommerce Settings) -->
    <?php if (!empty($storeSettings['top_announcement_bar_enabled']) && $storeSettings['top_announcement_bar_enabled'] === '1' && !empty($storeSettings['top_announcement_text'])): 
        $bannerTheme = $storeSettings['top_announcement_bg'] ?? 'emerald';
        $bannerClasses = match($bannerTheme) {
            'amber' => 'bg-amber-500 text-slate-900 border-b border-amber-600',
            'rose' => 'bg-rose-600 text-white border-b border-rose-700',
            'indigo' => 'bg-indigo-600 text-white border-b border-indigo-700',
            'dark' => 'bg-slate-900 text-white border-b border-slate-800',
            default => 'bg-emerald-600 text-white border-b border-emerald-700',
        };
    ?>
    <aside aria-label="Store Announcement" class="<?= $bannerClasses ?> px-4 py-2 text-center text-xs sm:text-sm font-semibold flex items-center justify-center gap-2 relative shadow-xs z-30">
        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-white/20 shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 3a1 1 0 00-1.447-.894L8.763 6H5a3 3 0 000 6h.28l1.771 5.316A1 1 0 008 18h1a1 1 0 001-1v-4.382l6.553 3.276A1 1 0 0018 15V3z" clip-rule="evenodd" />
            </svg>
        </span>
        <span><?= htmlspecialchars($storeSettings['top_announcement_text']) ?></span>
    </aside>
    <?php endif; ?>

    <!-- Store Status Banner (Maintenance / Catalog Mode) -->
    <?php if (($storeSettings['site_visibility_status'] ?? 'live') === 'maintenance'): ?>
    <div class="bg-amber-500 text-slate-900 px-4 py-2.5 text-center font-bold text-xs sm:text-sm flex items-center justify-center gap-2 border-b border-amber-600 shadow-sm z-30">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-slate-900" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
        <span><?= htmlspecialchars($storeSettings['maintenance_notice_message'] ?? 'আমাদের স্টোর রক্ষণাবেক্ষণে রয়েছে। সাময়িক অসুবিধার জন্য আমরা দুঃখিত।') ?></span>
    </div>
    <?php elseif (($storeSettings['site_visibility_status'] ?? 'live') === 'catalog'): ?>
    <div class="bg-sky-700 text-white px-4 py-2 text-center font-semibold text-xs sm:text-sm flex items-center justify-center gap-2 border-b border-sky-800 shadow-xs z-30">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor">
            <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
        </svg>
        <span>ক্যাটালগ মোড সক্রিয়: পণ্য দেখা যাবে, তবে অনলাইন অর্ডার সাময়িকভাবে স্থগিত রয়েছে। (Catalog Mode: Browsing only)</span>
    </div>
    <style>
        .add-to-cart-btn, [onclick*="addToCart"], #sticky-cart-btn, #mobile-sticky-cart, [href*="/checkout"] {
            display: none !important;
        }
    </style>
    <?php endif; ?>

    <!-- 1. TOP UTILITY BAR (FreshMart Style) -->
    <div class="bg-gray-50 border-b border-gray-200 text-xs text-gray-600">
        <div class="container mx-auto px-4 py-2 flex flex-wrap justify-between items-center gap-2">
            <div class="flex items-center gap-4 sm:gap-6 flex-wrap">
                <span class="flex items-center gap-1.5 text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <?= $__('topbar_location') ?>
                </span>
                <span class="hidden sm:flex items-center gap-1.5 text-emerald-700 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                    </svg>
                    <?= $__('topbar_free_delivery') ?>
                </span>
            </div>
            
            <div class="flex items-center gap-4 sm:gap-6 flex-wrap ml-auto text-[11px] sm:text-xs">
                <span class="hidden md:flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    <?= $__('topbar_phone') ?>
                </span>
                <span class="hidden lg:flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <?= $__('topbar_hours') ?>
                </span>

                <!-- Language Switcher Pill -->
                <a href="/sodai-dorkar/public/set-language?lang=<?= $otherLocale ?>" 
                   class="font-semibold text-emerald-800 bg-white hover:bg-emerald-50 px-2.5 py-0.5 rounded-full border border-gray-200 transition-all flex items-center gap-1 shadow-2xs"
                   title="Switch Language">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                    </svg>
                    <?= $__('lang_switch') ?>
                </a>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="/sodai-dorkar/public/<?= $_SESSION['role'] === 'admin' ? 'admin' : 'delivery' ?>/dashboard" class="text-xs font-semibold text-emerald-700 hover:underline"><?= $__('nav_dashboard') ?></a>
                    <a href="/sodai-dorkar/public/logout" class="text-xs font-medium text-red-500 hover:underline"><?= $__('nav_logout') ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 2. MAIN HEADER (Logo + Wide Search Bar + Account & Cart) -->
    <header class="bg-white sticky top-0 z-40 border-b border-gray-100 shadow-xs">
        <div class="container mx-auto px-4 py-3 sm:py-4 flex items-center justify-between gap-3 sm:gap-6">
            
            <!-- Brand Logo -->
            <a href="/sodai-dorkar/public/" class="flex items-center gap-2.5 group flex-shrink-0">
                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow-md shadow-emerald-600/25 group-hover:scale-105 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <div class="text-xl sm:text-2xl font-black text-gray-900 leading-none tracking-tight">
                        Fresh<span class="text-emerald-600">Mart</span>
                    </div>
                    <div class="text-[10px] text-gray-400 font-bold tracking-wider uppercase mt-1 hidden sm:block">
                        Fresh. Quality. Everyday.
                    </div>
                </div>
            </a>

            <!-- Central Search Bar with Category Dropdown -->
            <form action="/sodai-dorkar/public/" method="GET" class="hidden md:flex flex-1 max-w-2xl mx-2 relative">
                <?php if (!empty($isDeals)): ?>
                    <input type="hidden" name="deals" value="1">
                <?php endif; ?>
                <div class="flex w-full items-center border-2 border-emerald-600/90 rounded-xl overflow-hidden bg-white shadow-xs focus-within:ring-2 focus-within:ring-emerald-500/20">
                    <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" 
                           placeholder="<?= $__('header_search_placeholder') ?>" 
                           class="flex-1 px-4 py-2 text-sm text-gray-800 placeholder-gray-400 focus:outline-none bg-transparent">
                    
                    <div class="h-6 w-px bg-gray-200"></div>
                    
                    <select name="category" class="px-3 py-2 text-xs font-semibold text-gray-600 bg-transparent focus:outline-none cursor-pointer border-none max-w-[150px] truncate">
                        <option value=""><?= $__('header_all_categories') ?></option>
                        <?php foreach ($mainCategories as $mCat): ?>
                            <option value="<?= $mCat['id'] ?>" <?= (isset($currentCategory) && $currentCategory == $mCat['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($mCat['name']) ?>
                            </option>
                            <?php if (!empty($childrenMap[$mCat['id']])): ?>
                                <?php foreach ($childrenMap[$mCat['id']] as $subId): ?>
                                    <?php $sCat = $catById[$subId] ?? null; if ($sCat): ?>
                                        <option value="<?= $sCat['id'] ?>" <?= (isset($currentCategory) && $currentCategory == $sCat['id']) ? 'selected' : '' ?>>
                                            &nbsp;&nbsp;↳ <?= htmlspecialchars($sCat['name']) ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 transition-colors flex items-center justify-center font-bold">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>
            </form>

            <!-- Right: User Account & Cart Button -->
            <div class="flex items-center gap-3 sm:gap-4 flex-shrink-0">
                <!-- User Account Widget -->
                <?php if (isset($_SESSION['customer_id'])): ?>
                    <a href="/sodai-dorkar/public/account" class="flex items-center gap-2 text-gray-700 hover:text-emerald-600 transition-colors">
                        <div class="w-9 h-9 rounded-full bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-700 shadow-2xs">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div class="hidden xl:block text-left leading-tight">
                            <div class="text-[10px] text-gray-400 font-medium"><?= $__('header_account_sub') ?></div>
                            <div class="text-xs font-bold text-gray-900 truncate max-w-[90px]"><?= htmlspecialchars($_SESSION['customer_name']) ?></div>
                        </div>
                    </a>
                <?php else: ?>
                    <a href="/sodai-dorkar/public/checkout/auth" class="flex items-center gap-2 text-gray-700 hover:text-emerald-600 transition-colors">
                        <div class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 shadow-2xs">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div class="hidden sm:block text-left leading-tight">
                            <div class="text-[10px] text-gray-400 font-medium"><?= $__('header_account_title') ?></div>
                            <div class="text-xs font-bold text-gray-900"><?= $__('header_account_sub') ?></div>
                        </div>
                    </a>
                <?php endif; ?>

                <!-- My Cart Trigger Widget -->
                <button type="button" onclick="openCartDrawer()" class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl bg-gray-50 hover:bg-emerald-50 border border-gray-200 hover:border-emerald-300 transition-all text-left group cursor-pointer" id="header-cart-btn">
                    <div class="relative">
                        <div class="w-9 h-9 rounded-lg bg-emerald-600 text-white flex items-center justify-center shadow-xs group-hover:bg-emerald-700 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <span id="header-cart-badge" class="absolute -top-1.5 -right-2 min-w-[20px] h-5 px-1 bg-red-600 text-white text-[11px] font-black rounded-full ring-2 ring-white flex items-center justify-center shadow-xs leading-none transition-all duration-200 <?= $cartCount > 0 ? '' : 'hidden' ?>">
                            <?= $cartCount ?>
                        </span>
                    </div>
                    <div class="hidden sm:block leading-tight pr-1">
                        <div class="text-[10px] text-gray-400 font-medium uppercase tracking-wider"><?= $__('header_my_cart') ?></div>
                        <div class="text-xs font-black text-gray-900" id="header-cart-total"><?= $__('currency') ?><?= number_format($cartTotal, 2) ?></div>
                    </div>
                </button>
            </div>
        </div>

        <!-- 3. SECONDARY NAVIGATION BAR (Shop by Category + Links + Flash Deals) -->
        <nav class="border-t border-gray-100 bg-white">
            <div class="container mx-auto px-4 flex items-center justify-between">
                
                <!-- Category Dropdown Button -->
                <div class="relative group/cat">
                    <button type="button" class="bg-[#14532d] hover:bg-[#0f4022] text-white text-xs sm:text-sm font-bold py-2.5 sm:py-3 px-4 sm:px-5 flex items-center gap-2.5 transition-colors cursor-pointer rounded-t-lg sm:rounded-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <span><?= $__('nav_shop_by_categories') ?></span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 ml-1 transition-transform group-hover/cat:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <!-- Category Dropdown Menu with Subcategory Flyouts -->
                    <div class="absolute left-0 top-full w-64 bg-white rounded-b-xl shadow-2xl border border-gray-100 py-2 z-50 hidden group-hover/cat:block transition-all">
                        <a href="/sodai-dorkar/public/" class="flex items-center justify-between px-4 py-2.5 text-xs text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 font-bold border-b border-gray-50 transition-colors">
                            <span><?= $__('products_all_categories') ?></span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                        <?php foreach ($mainCategories as $cat): ?>
                            <?php 
                                $hasSub = !empty($childrenMap[$cat['id']]); 
                                $subIds = $hasSub ? $childrenMap[$cat['id']] : [];
                            ?>
                            <div class="relative group/sub">
                                <a href="/sodai-dorkar/public/category?id=<?= $cat['id'] ?>" 
                                   class="flex items-center justify-between px-4 py-2.5 text-xs text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 font-medium transition-colors">
                                    <span class="truncate"><?= htmlspecialchars($cat['name']) ?></span>
                                    <?php if ($hasSub): ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-emerald-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                    <?php endif; ?>
                                </a>

                                <!-- Subcategory Flyout -->
                                <?php if ($hasSub): ?>
                                    <div class="absolute left-full top-0 w-60 bg-white rounded-xl shadow-2xl border border-gray-100 py-2 z-50 hidden group-hover/sub:block transition-all -ml-1">
                                        <div class="px-3.5 py-1.5 text-[10px] font-black uppercase text-gray-400 border-b border-gray-50 tracking-wider">
                                            <?= htmlspecialchars($cat['name']) ?>
                                        </div>
                                        <?php foreach ($subIds as $sId): ?>
                                            <?php $sCat = $catById[$sId] ?? null; if ($sCat): ?>
                                                <a href="/sodai-dorkar/public/category?id=<?= $sCat['id'] ?>" 
                                                   class="flex items-center justify-between px-4 py-2 text-xs text-gray-600 hover:bg-emerald-50 hover:text-emerald-800 font-medium transition-colors">
                                                    <span class="truncate"><?= htmlspecialchars($sCat['name']) ?></span>
                                                    <span class="text-[10px] text-gray-400">→</span>
                                                </a>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Nav Links -->
                <div class="hidden lg:flex items-center gap-7 text-xs sm:text-sm font-semibold text-gray-700">
                    <a href="/sodai-dorkar/public/" class="hover:text-emerald-600 transition-colors <?= empty($currentCategory) && empty($search) && empty($isDeals) ? 'text-emerald-600 font-bold' : '' ?>"><?= $__('nav_home') ?></a>
                    <a href="#categories" class="hover:text-emerald-600 transition-colors"><?= $__('sec_shop_by_category') ?></a>
                    <a href="/sodai-dorkar/public/?deals=1" class="hover:text-emerald-600 transition-colors <?= !empty($isDeals) ? 'text-emerald-600 font-bold' : '' ?>"><?= $__('nav_deals') ?></a>
                    <a href="#products" class="hover:text-emerald-600 transition-colors"><?= $__('nav_new_arrivals') ?></a>
                    <a href="#why-choose" class="hover:text-emerald-600 transition-colors"><?= $__('nav_about') ?></a>
                    <a href="#footer" class="hover:text-emerald-600 transition-colors"><?= $__('nav_contact') ?></a>
                </div>

                <!-- Right: Flash Deals Badge -->
                <a href="/sodai-dorkar/public/?deals=1" class="flex items-center gap-1.5 text-xs font-black text-emerald-800 bg-emerald-100/70 hover:bg-emerald-200 border border-emerald-300 px-3.5 py-1.5 rounded-full transition-all tracking-wide">
                    <span class="text-amber-500 animate-pulse">⚡</span> <?= $__('nav_flash_deals') ?>
                </a>
            </div>
        </nav>

        <!-- Mobile Search Input Bar -->
        <div class="md:hidden px-4 py-2.5 bg-gray-50 border-t border-gray-100">
            <form action="/sodai-dorkar/public/" method="GET" class="flex items-center bg-white border border-gray-300 rounded-xl overflow-hidden shadow-2xs">
                <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="<?= $__('header_search_placeholder') ?>" class="flex-1 px-3 py-2 text-xs text-gray-800 focus:outline-none">
                <button type="submit" class="bg-emerald-600 text-white px-3.5 py-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </button>
            </form>
        </div>
    </header>

    <!-- Main Content (with bottom padding on mobile so bottom sticky cart never covers content) -->
    <main class="flex-grow pb-20 md:pb-0">
        <?= $content ?? '' ?>
    </main>

    <!-- STICKY FLOATING CART WIDGET (Bottom Bar on Mobile, Right-Side Pill on Desktop) -->
    <aside id="sticky-cart-btn" onclick="openCartDrawer()" 
           class="fixed z-40 cursor-pointer select-none group transition-all duration-300 backdrop-blur-md focus:outline-none focus:ring-2 focus:ring-emerald-400 bottom-3 inset-x-3 sm:inset-x-6 max-w-lg mx-auto flex flex-row items-center justify-between bg-gray-950/95 hover:bg-black text-white rounded-2xl shadow-2xl border border-emerald-500/50 p-2.5 px-4 md:bottom-auto md:top-1/2 md:-translate-y-1/2 md:right-0 md:left-auto md:inset-x-auto md:w-auto md:max-w-none md:flex-col md:rounded-l-2xl md:rounded-r-none md:border-l-2 md:border-y md:border-r-0 md:min-w-[84px] md:p-2.5 md:text-center md:hover:-translate-x-1.5"
           title="<?= $__('drawer_title') ?>">
        
        <!-- Bag Icon & Item Count -->
        <div class="relative flex items-center gap-3 md:flex-col md:items-center md:gap-0 md:mb-2 w-auto md:w-full">
            <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-emerald-500/20 text-emerald-400 group-hover:bg-emerald-500 group-hover:text-white flex items-center justify-center transition-all transform group-hover:scale-105 flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </div>
            <div class="text-left md:text-center">
                <span id="sticky-cart-item-count" class="text-xs sm:text-sm md:text-xs font-black md:font-bold text-white md:text-emerald-300 md:group-hover:text-white transition-colors tracking-tight block leading-tight">
                    <?= $cartCount ?> <?= $locale === 'bn' ? 'টি পণ্য' : ($cartCount == 1 ? 'Item' : 'Items') ?>
                </span>
                <span class="text-[10px] text-emerald-400 font-bold uppercase tracking-wider md:hidden block leading-tight">
                    <?= $locale === 'bn' ? 'ব্যাগ দেখুন' : 'View Cart' ?>
                </span>
            </div>
        </div>

        <!-- Total Amount & Arrow -->
        <div class="flex items-center gap-2 w-auto md:w-full">
            <div class="bg-emerald-600 group-hover:bg-emerald-500 text-white rounded-xl md:rounded-lg px-3.5 py-1.5 md:px-2 md:py-1 shadow-inner text-center font-black text-xs sm:text-sm tracking-tight transition-colors flex items-center gap-1.5 justify-center w-full">
                <span id="sticky-cart-total-val"><?= $__('currency') ?><?= number_format($cartTotal, 2) ?></span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:hidden text-emerald-100 transform group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </div>
    </aside>

    <!-- SLIDE-OVER SIDE CART DRAWER -->
    <!-- Backdrop -->
    <div id="cart-drawer-backdrop" onclick="closeCartDrawer()" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 opacity-0 pointer-events-none transition-opacity duration-300"></div>

    <!-- Drawer Container -->
    <div id="cart-drawer" class="fixed top-0 right-0 h-full w-full max-w-md bg-white shadow-2xl z-50 transform translate-x-full transition-transform duration-300 ease-out flex flex-col">
        <!-- Header -->
        <div class="p-4 sm:p-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/80">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-emerald-100 text-emerald-700 rounded-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 text-lg leading-tight"><?= $__('drawer_title') ?></h3>
                    <p class="text-xs text-gray-500 mt-0.5"><span id="drawer-items-count"><?= $cartCount ?></span> <?= $__('cart_items') ?></p>
                </div>
            </div>
            <button type="button" onclick="closeCartDrawer()" class="p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-200/60 rounded-full transition-colors" title="<?= $__('drawer_close') ?>">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Dynamic Spend More & Free Delivery Milestone Bar (Drawer) -->
        <div id="drawer-spend-more-container" class="border-b border-emerald-100/80 p-3 sm:p-4 bg-emerald-50/20 transition-all duration-300 <?= empty($spendMoreOffers['enabled']) ? 'hidden' : '' ?>">
            <!-- Rendered dynamically by renderSpendMoreOffersUI() -->
        </div>

        <!-- Items List (Scrollable) -->
        <div class="flex-grow overflow-y-auto p-4 sm:p-5 custom-scrollbar divide-y divide-gray-100" id="drawer-items-list">
            <!-- Dynamically populated by renderCartDrawer() -->
        </div>

        <!-- Footer / Checkout -->
        <div class="border-t border-gray-100 p-4 sm:p-5 bg-gray-50/70 space-y-3">
            <div class="flex justify-between items-center text-sm text-gray-600">
                <span class="font-medium"><?= $__('drawer_subtotal') ?></span>
                <span class="text-xl font-black text-gray-900" id="drawer-subtotal-val"><?= $__('currency') ?><?= number_format($cartTotal, 2) ?></span>
            </div>
            <p class="text-xs text-gray-400 text-center"><?= $__('drawer_delivery_note') ?></p>
            <a href="/sodai-dorkar/public/checkout" id="drawer-checkout-btn" class="block w-full bg-emerald-600 hover:bg-emerald-700 text-white text-center font-bold py-3.5 px-4 rounded-xl shadow-md hover:shadow-lg transition-all <?= $cartCount > 0 ? '' : 'pointer-events-none opacity-50' ?>">
                <?= $__('drawer_checkout') ?>
            </a>
            <a href="/sodai-dorkar/public/cart" class="block w-full text-center text-xs font-semibold text-gray-500 hover:text-emerald-700 py-1 transition-colors">
                <?= $__('drawer_view_cart') ?> →
            </a>
        </div>
    </div>

    <!-- 4. RICH MEGA FOOTER (FreshMart Style Dark Forest Theme) -->
    <footer class="bg-[#0f2819] text-white pt-16 pb-8 mt-16 border-t border-emerald-950" id="footer">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8 lg:gap-10 pb-12 border-b border-white/10">
                
                <!-- Col 1: Brand Info -->
                <div class="lg:col-span-2 space-y-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <span class="text-2xl font-black tracking-tight text-white">Fresh<span class="text-emerald-400">Mart</span></span>
                    </div>
                    <p class="text-emerald-100/70 text-xs sm:text-sm leading-relaxed max-w-sm">
                        <?= $__('footer_desc') ?>
                    </p>
                    <!-- Social Links -->
                    <div class="flex items-center gap-3 pt-2 text-emerald-300">
                        <a href="#" class="w-8 h-8 rounded-full bg-white/5 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition-all">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
                        </a>
                        <a href="#" class="w-8 h-8 rounded-full bg-white/5 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition-all">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z"/></svg>
                        </a>
                        <a href="#" class="w-8 h-8 rounded-full bg-white/5 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition-all">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.936 9.936 0 0024 4.59z"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Col 2: Quick Links -->
                <div>
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-4"><?= $__('footer_quick_links') ?></h4>
                    <ul class="space-y-2.5 text-xs text-emerald-100/70">
                        <li><a href="/sodai-dorkar/public/" class="hover:text-emerald-400 transition-colors"><?= $__('nav_home') ?></a></li>
                        <li><a href="/sodai-dorkar/public/?deals=1" class="hover:text-emerald-400 transition-colors"><?= $__('nav_deals') ?></a></li>
                        <li><a href="#products" class="hover:text-emerald-400 transition-colors"><?= $__('nav_new_arrivals') ?></a></li>
                        <li><a href="#why-choose" class="hover:text-emerald-400 transition-colors"><?= $__('nav_about') ?></a></li>
                        <li><a href="#footer" class="hover:text-emerald-400 transition-colors"><?= $__('nav_contact') ?></a></li>
                    </ul>
                </div>

                <!-- Col 3: Customer Service -->
                <div>
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-4"><?= $__('footer_customer_service') ?></h4>
                    <ul class="space-y-2.5 text-xs text-emerald-100/70">
                        <li><a href="/sodai-dorkar/public/account" class="hover:text-emerald-400 transition-colors"><?= $__('header_account_sub') ?></a></li>
                        <li><a href="/sodai-dorkar/public/account/orders" class="hover:text-emerald-400 transition-colors"><?= $__('footer_order_tracking') ?></a></li>
                        <li><a href="/sodai-dorkar/public/cart" class="hover:text-emerald-400 transition-colors"><?= $__('nav_cart') ?></a></li>
                        <li><a href="#" class="hover:text-emerald-400 transition-colors"><?= $__('footer_returns') ?></a></li>
                        <li><a href="#" class="hover:text-emerald-400 transition-colors"><?= $__('footer_privacy') ?></a></li>
                    </ul>
                </div>

                <!-- Col 4: Contact Us & Payment -->
                <div>
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-4"><?= $__('footer_contact') ?></h4>
                    <ul class="space-y-2.5 text-xs text-emerald-100/70">
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-400">📍</span>
                            <span><?= $__('topbar_location') ?></span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-emerald-400">📞</span>
                            <span><?= $__('topbar_phone') ?></span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-emerald-400">✉️</span>
                            <span>support@sodaidorkar.com</span>
                        </li>
                    </ul>

                    <!-- Payment Badges -->
                    <div class="mt-5">
                        <div class="text-[11px] font-bold text-white uppercase tracking-wider mb-2"><?= $__('footer_we_accept') ?></div>
                        <div class="flex flex-wrap items-center gap-1.5 text-[10px] font-extrabold text-gray-800">
                            <span class="bg-white px-2 py-0.5 rounded shadow-2xs">bKash</span>
                            <span class="bg-white px-2 py-0.5 rounded shadow-2xs">Nagad</span>
                            <span class="bg-white px-2 py-0.5 rounded shadow-2xs">Rocket</span>
                            <span class="bg-white px-2 py-0.5 rounded shadow-2xs">VISA</span>
                            <span class="bg-white px-2 py-0.5 rounded shadow-2xs">MasterCard</span>
                            <span class="bg-emerald-800 text-white px-2 py-0.5 rounded border border-emerald-700">COD</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Bottom Copyright -->
            <div class="pt-6 text-center text-xs text-emerald-100/50">
                &copy; <?= date('Y') ?> <span class="text-emerald-300 font-semibold"><?= $__('site_name') ?> (FreshMart)</span>. <?= $__('footer_rights') ?>
            </div>
        </div>
    </footer>
    
    <script>
        // Global State & Language
        window.SODAI_STATE = {
            locale: '<?= $locale ?>',
            currency: '<?= $__('currency') ?>',
            itemLabel: '<?= $locale === 'bn' ? 'টি পণ্য' : 'Items' ?>',
            singleItemLabel: '<?= $locale === 'bn' ? '১ টি পণ্য' : '1 Item' ?>',
            emptyCartTitle: '<?= $__('drawer_empty') ?>',
            emptyCartDesc: '<?= $__('drawer_empty_desc') ?>',
            addToBagText: '<?= $__('add_to_bag') ?>',
            inBagText: '<?= $__('in_bag') ?>',
            cart: <?= json_encode($cart ?: new stdClass()) ?>,
            cartCount: <?= (int)$cartCount ?>,
            cartTotal: <?= (float)$cartTotal ?>,
            spendMoreOffers: <?= json_encode($spendMoreOffers) ?>
        };

        // Render Spend More & Promotional Offers Progress Bar
        function renderSpendMoreOffersUI(containerId, offers) {
            const container = document.getElementById(containerId);
            if (!container) return;

            if (!offers || !offers.enabled || !offers.tiers || offers.tiers.length === 0) {
                container.innerHTML = '';
                container.classList.add('hidden');
                return;
            }

            container.classList.remove('hidden');

            const isAllUnlocked = !!offers.is_all_unlocked;
            const nextTier = offers.next_tier;
            const motivationalMsg = offers.motivational_message || offers.message || '';
            const unlockedTiers = offers.unlocked_tiers || [];
            const tiers = offers.tiers || [];
            const isBn = window.SODAI_STATE.locale === 'bn';
            const curr = window.SODAI_STATE.currency || '৳';
            const currentGoalPercent = offers.current_goal_percent !== undefined 
                ? parseInt(offers.current_goal_percent) 
                : (isAllUnlocked ? 100 : (nextTier ? parseInt(nextTier.segment_percent || 0) : 100));

            const headerIcon = isAllUnlocked ? '🎉' : (nextTier ? (nextTier.icon || '🚚') : '🎁');

            let html = `
                <div class="rounded-xl p-2 sm:p-2.5 bg-gradient-to-r from-emerald-50/90 via-teal-50/60 to-emerald-50/90 border border-emerald-200/80 shadow-2xs space-y-1.5">
                    <!-- Header Line: Icon + Short Motivational Text + Percentage Badge -->
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5 min-w-0">
                            <span class="text-sm shrink-0">${headerIcon}</span>
                            <p class="text-[11px] sm:text-xs font-bold text-gray-800 truncate leading-tight">
                                ${motivationalMsg}
                            </p>
                        </div>
                        <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-emerald-600 text-white shrink-0 shadow-2xs">
                            ${currentGoalPercent}%
                        </span>
                    </div>

                    <!-- Synchronized Multi-Step Milestone Stepper -->
                    <div class="grid gap-1.5 sm:gap-2 items-stretch" style="grid-template-columns: repeat(${tiers.length}, minmax(0, 1fr));">
                        ${tiers.map(t => {
                            const isUnlocked = !!t.is_unlocked;
                            const isCurrent = !isUnlocked && (t.is_current || (nextTier && nextTier.min_amount === t.min_amount));
                            const segPercent = Math.min(100, Math.max(0, parseInt(t.segment_percent || (isUnlocked ? 100 : 0))));
                            const needed = t.amount_needed !== undefined ? Math.round(t.amount_needed) : Math.max(0, t.min_amount - (offers.subtotal || 0));

                            return `
                                <div class="p-1.5 sm:p-2 rounded-lg transition-all ${
                                    isUnlocked 
                                        ? 'bg-emerald-100/60 border border-emerald-200/80' 
                                        : (isCurrent 
                                            ? 'bg-white border border-emerald-500 shadow-2xs ring-1 ring-emerald-200' 
                                            : 'bg-white/50 border border-gray-200/60 opacity-60')
                                }">
                                    <!-- Live Segment Micro Progress Bar -->
                                    <div class="w-full bg-gray-200/80 rounded-full h-1 overflow-hidden mb-1">
                                        <div class="h-full bg-gradient-to-r from-emerald-500 to-green-500 rounded-full transition-all duration-500" 
                                             style="width: ${segPercent}%;"></div>
                                    </div>

                                    <!-- Amount & Status Row -->
                                    <div class="flex items-center justify-between gap-1 leading-tight">
                                        <span class="text-[10px] sm:text-[11px] font-black ${isUnlocked ? 'text-emerald-800' : (isCurrent ? 'text-gray-900' : 'text-gray-500')}">
                                            ${curr}${parseFloat(t.min_amount).toFixed(0)}
                                        </span>
                                        ${isUnlocked ? `
                                            <span class="text-[9px] font-black text-emerald-700 flex items-center gap-0.5 shrink-0">
                                                <span>✓</span> <span class="hidden sm:inline">${isBn ? 'অর্জিত' : 'Done'}</span>
                                            </span>
                                        ` : (isCurrent ? `
                                            <span class="text-[9px] font-bold text-amber-700 shrink-0 whitespace-nowrap">
                                                ${isBn ? `আর ৳${needed}` : `৳${needed} left`}
                                            </span>
                                        ` : `
                                            <span class="text-[9px] text-gray-400 font-medium shrink-0">
                                                ${isBn ? 'লকড' : 'Locked'}
                                            </span>
                                        `)}
                                    </div>

                                    <!-- Benefit Title -->
                                    <div class="text-[9px] sm:text-[10px] font-semibold ${isUnlocked ? 'text-emerald-700' : (isCurrent ? 'text-gray-700' : 'text-gray-400')} truncate mt-0.5" title="${t.title}">
                                        ${t.icon ? `${t.icon} ` : ''}${t.title}
                                    </div>
                                </div>
                            `;
                        }).join('')}
                    </div>
                </div>
            `;

            container.innerHTML = html;
        }

        // Open Side Drawer
        function openCartDrawer() {
            const drawer = document.getElementById('cart-drawer');
            const backdrop = document.getElementById('cart-drawer-backdrop');
            renderCartDrawer();
            drawer.classList.remove('translate-x-full');
            backdrop.classList.remove('opacity-0', 'pointer-events-none');
            document.body.style.overflow = 'hidden';
        }

        // Close Side Drawer
        function closeCartDrawer() {
            const drawer = document.getElementById('cart-drawer');
            const backdrop = document.getElementById('cart-drawer-backdrop');
            drawer.classList.add('translate-x-full');
            backdrop.classList.add('opacity-0', 'pointer-events-none');
            document.body.style.overflow = '';
        }

        // Render Cart Drawer Content
        function renderCartDrawer() {
            const container = document.getElementById('drawer-items-list');
            const cart = window.SODAI_STATE.cart || {};
            const keys = Object.keys(cart);

            if (keys.length === 0) {
                container.innerHTML = `
                    <div class="py-16 text-center text-gray-400 flex flex-col items-center">
                        <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center text-gray-300 mb-3">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <h4 class="font-bold text-gray-700 text-base mb-1">${window.SODAI_STATE.emptyCartTitle}</h4>
                        <p class="text-xs text-gray-400 max-w-xs">${window.SODAI_STATE.emptyCartDesc}</p>
                    </div>
                `;
                return;
            }

            let html = '';
            for (const key of keys) {
                const item = cart[key];
                const itemTotal = (parseFloat(item.price) * parseInt(item.quantity)).toFixed(2);
                const defaultImg = '/sodai-dorkar/public/images/default-product.svg';
                const imgSrc = item.image ? item.image : defaultImg;
                const itemKeyStr = JSON.stringify(key);

                html += `
                    <div class="py-3.5 flex items-center gap-3 sm:gap-4" id="drawer-item-${key}">
                        <div class="w-16 h-16 flex-shrink-0 bg-gray-50 border border-gray-100 rounded-xl overflow-hidden relative flex items-center justify-center">
                            <img src="${imgSrc}" alt="${item.name}" class="w-full h-full object-contain p-1" onerror="this.src='${defaultImg}'">
                        </div>
                        <div class="flex-grow min-w-0">
                            <h4 class="text-sm font-semibold text-gray-800 truncate" title="${item.name}">${item.name}</h4>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="text-xs font-black text-emerald-600">${window.SODAI_STATE.currency}${parseFloat(item.price).toFixed(2)}</span>
                                ${item.regular_price && parseFloat(item.regular_price) > parseFloat(item.price) ? `
                                    <span class="text-[11px] text-gray-400 line-through font-medium">${window.SODAI_STATE.currency}${parseFloat(item.regular_price).toFixed(2)}</span>
                                ` : ''}
                            </div>
                            
                            <div class="flex items-center gap-2 mt-2">
                                <div class="inline-flex items-center border border-gray-200 rounded-lg overflow-hidden bg-white shadow-xs">
                                    <button type="button" onclick='drawerUpdateQty(${itemKeyStr}, ${parseInt(item.quantity) - 1})' 
                                            class="px-2 py-0.5 text-xs text-gray-600 hover:bg-gray-100 font-bold transition-colors">−</button>
                                    <span class="px-2.5 text-xs font-bold text-gray-800">${item.quantity}</span>
                                    <button type="button" onclick='drawerUpdateQty(${itemKeyStr}, ${parseInt(item.quantity) + 1})' 
                                            class="px-2 py-0.5 text-xs text-gray-600 hover:bg-gray-100 font-bold transition-colors">+</button>
                                </div>
                                <span class="text-xs font-bold text-gray-800 ml-auto">${window.SODAI_STATE.currency}${itemTotal}</span>
                            </div>
                        </div>
                        <button type="button" onclick='drawerUpdateQty(${itemKeyStr}, 0)' class="text-gray-300 hover:text-red-500 p-1.5 transition-colors" title="<?= $__('cart_remove') ?>">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                `;
            }
            container.innerHTML = html;
        }

        // Update Cart Global UI (Header, Right Sticky, Drawer)
        function updateCartUI(data) {
            if (data.cart_count !== undefined) window.SODAI_STATE.cartCount = data.cart_count;
            if (data.cart_total !== undefined) window.SODAI_STATE.cartTotal = data.cart_total;
            if (data.cart !== undefined) window.SODAI_STATE.cart = data.cart;

            const count = window.SODAI_STATE.cartCount;
            const total = window.SODAI_STATE.cartTotal;

            // 1. Header Badge & Total
            const headerBadge = document.getElementById('header-cart-badge');
            if (headerBadge) {
                if (count > 0) {
                    headerBadge.textContent = count;
                    headerBadge.classList.remove('hidden');
                } else {
                    headerBadge.classList.add('hidden');
                }
            }
            const headerTotal = document.getElementById('header-cart-total');
            if (headerTotal) {
                headerTotal.textContent = `${window.SODAI_STATE.currency}${parseFloat(total || 0).toFixed(2)}`;
            }

            // 2. Right Sticky Widget
            const stickyCount = document.getElementById('sticky-cart-item-count');
            const stickyTotal = document.getElementById('sticky-cart-total-val');
            const stickyBtn = document.getElementById('sticky-cart-btn');

            if (stickyCount) {
                const label = window.SODAI_STATE.locale === 'bn' ? `${count} টি পণ্য` : (count === 1 ? '1 Item' : `${count} Items`);
                stickyCount.textContent = label;
            }
            if (stickyTotal) {
                stickyTotal.textContent = `${window.SODAI_STATE.currency}${parseFloat(total).toFixed(2)}`;
            }

            // Animate bump on sticky cart
            if (stickyBtn) {
                stickyBtn.classList.remove('cart-bump');
                void stickyBtn.offsetWidth; // Trigger reflow
                stickyBtn.classList.add('cart-bump');
            }

            // 3. Drawer UI
            const drawerCount = document.getElementById('drawer-items-count');
            const drawerSubtotal = document.getElementById('drawer-subtotal-val');
            const drawerCheckoutBtn = document.getElementById('drawer-checkout-btn');

            if (drawerCount) drawerCount.textContent = count;
            if (drawerSubtotal) drawerSubtotal.textContent = `${window.SODAI_STATE.currency}${parseFloat(total).toFixed(2)}`;
            if (drawerCheckoutBtn) {
                if (count > 0) {
                    drawerCheckoutBtn.classList.remove('pointer-events-none', 'opacity-50');
                } else {
                    drawerCheckoutBtn.classList.add('pointer-events-none', 'opacity-50');
                }
            }

            if (data.spend_more_offers !== undefined) {
                window.SODAI_STATE.spendMoreOffers = data.spend_more_offers;
            }
            renderSpendMoreOffersUI('drawer-spend-more-container', window.SODAI_STATE.spendMoreOffers);
            renderSpendMoreOffersUI('cart-spend-more-container', window.SODAI_STATE.spendMoreOffers);

            // Re-render drawer if opened
            renderCartDrawer();

            // Sync all product cards on the page with current cart state
            if (typeof syncProductCardsWithCart === 'function') {
                syncProductCardsWithCart();
            }
        }

        // Drawer Quantity Update
        function drawerUpdateQty(itemKey, newQty) {
            const formData = new FormData();
            formData.append('cart_key', itemKey);
            formData.append('product_id', itemKey);
            formData.append('quantity', Math.max(0, newQty));
            
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            if (csrfMeta) formData.append('csrf_token', csrfMeta.getAttribute('content'));

            fetch('/sodai-dorkar/public/cart/update', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    updateCartUI(data);
                }
            })
            .catch(() => showToast('<?= $__('toast_error') ?>', 'error'));
        }

        // Toast notification system
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-emerald-600' : 'bg-red-500';
            const icon = type === 'success' 
                ? '<svg class="h-5 w-5 text-white flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>'
                : '<svg class="h-5 w-5 text-white flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
            
            toast.className = `toast-enter flex items-center gap-3 ${bgColor} text-white px-5 py-3.5 rounded-xl shadow-2xl min-w-[280px] pointer-events-auto`;
            toast.innerHTML = `${icon}<span class="font-semibold text-sm">${message}</span>`;
            container.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.remove('toast-enter');
                toast.classList.add('toast-exit');
                setTimeout(() => toast.remove(), 300);
            }, 2500);
        }

        // AJAX Add to Cart (Used by product cards)
        function addToCartAjax(productId, btn, variantOptions = null) {
            const originalContent = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', 1);

            // Read variant from passed options or product-card data attributes
            const card = btn.closest('.product-card');
            const vTitle = variantOptions?.variant_title || card?.dataset?.selectedVariantTitle || '';
            const vPrice = variantOptions?.variant_price || card?.dataset?.selectedVariantPrice || '';
            const vQty = variantOptions?.variant_qty || card?.dataset?.selectedVariantQty || '';

            if (vTitle) formData.append('variant_title', vTitle);
            if (vPrice) formData.append('variant_price', vPrice);
            if (vQty) formData.append('variant_qty', vQty);

            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            if (csrfMeta) formData.append('csrf_token', csrfMeta.getAttribute('content'));

            fetch('/sodai-dorkar/public/cart/add', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    updateCartUI(data);
                    showToast(data.message || '<?= $__('toast_added_to_cart') ?>');
                }
            })
            .catch(() => {
                showToast('<?= $__('toast_error') ?>', 'error');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalContent;
            });
        }

        // Switch variant on product card
        function selectProductCardVariant(pillBtn, title, price, qty) {
            const card = pillBtn.closest('.product-card');
            if (!card) return;

            card.dataset.selectedVariantTitle = title;
            card.dataset.selectedVariantPrice = price;
            card.dataset.selectedVariantQty = qty;

            const priceEl = card.querySelector('.card-price');
            if (priceEl) {
                priceEl.textContent = Number(price).toLocaleString('en-US');
            }

            card.querySelectorAll('.variant-pill').forEach(btn => {
                btn.className = 'variant-pill inline-flex items-center px-2 py-0.5 rounded-md text-[11px] border transition-all cursor-pointer border-gray-200 text-gray-500 bg-white hover:border-gray-300 font-medium';
            });
            pillBtn.className = 'variant-pill inline-flex items-center px-2 py-0.5 rounded-md text-[11px] border transition-all cursor-pointer border-emerald-600 text-emerald-700 bg-emerald-50/70 font-bold active-variant shadow-2xs';

            renderCardActionButton(card);
        }

        // Find matching item in cart for a product card
        function findCartItemForCard(card) {
            const productId = card.dataset.productId;
            const selectedVariant = card.dataset.selectedVariantTitle || '';
            const cart = window.SODAI_STATE?.cart || {};

            for (const key in cart) {
                const item = cart[key];
                if (String(item.product_id) === String(productId)) {
                    if (selectedVariant) {
                        if (item.variant_title === selectedVariant) return item;
                    } else {
                        if (!item.variant_title) return item;
                    }
                }
            }
            return null;
        }

        function convertToBanglaNumber(num) {
            const bnDigits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
            return String(num).replace(/[0-9]/g, d => bnDigits[d]);
        }

        // Render Add to Bag OR [-] Qty [+] controller for a card
        function renderCardActionButton(card) {
            const container = card.querySelector('.card-action-container');
            if (!container) return;

            const productId = card.dataset.productId;
            const selectedVariant = card.dataset.selectedVariantTitle || '';
            const cartItem = findCartItemForCard(card);
            const quantity = cartItem ? parseInt(cartItem.quantity) : 0;
            const isBn = (window.SODAI_STATE?.locale === 'bn');
            const addToBagText = window.SODAI_STATE?.addToBagText || (isBn ? 'ব্যাগ এ যোগ করুন' : 'Add to Bag');
            const inBagSuffix = window.SODAI_STATE?.inBagText || (isBn ? 'টি ব্যাগে' : 'in bag');

            if (quantity > 0) {
                const displayQty = isBn ? convertToBanglaNumber(quantity) : quantity;
                container.innerHTML = `
                    <div class="w-full py-1 px-1 rounded-xl bg-emerald-600 text-white font-bold text-xs flex items-center justify-between shadow-md select-none transition-all">
                        <button type="button" 
                                onclick="cardChangeQty(${productId}, -1, this)" 
                                class="w-7 sm:w-8 h-7 sm:h-8 rounded-lg bg-emerald-700/90 hover:bg-emerald-800 active:scale-90 text-white flex items-center justify-center transition-all font-black text-sm sm:text-base cursor-pointer"
                                title="কমান">
                            −
                        </button>
                        <div class="flex flex-col items-center justify-center px-1 text-center leading-tight">
                            <span class="text-xs sm:text-[13px] font-black tracking-tight text-white">${displayQty} ${inBagSuffix}</span>
                            ${selectedVariant ? `<span class="text-[10px] text-emerald-100 font-medium truncate max-w-[90px]">${selectedVariant}</span>` : ''}
                        </div>
                        <button type="button" 
                                onclick="cardChangeQty(${productId}, 1, this)" 
                                class="w-7 sm:w-8 h-7 sm:h-8 rounded-lg bg-emerald-700/90 hover:bg-emerald-800 active:scale-90 text-white flex items-center justify-center transition-all font-black text-sm sm:text-base cursor-pointer"
                                title="বাড়ান">
                            +
                        </button>
                    </div>
                `;
            } else {
                container.innerHTML = `
                    <button type="button" 
                            onclick="cardAddToCart(${productId}, this)" 
                            class="w-full py-2 px-3 rounded-xl bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 hover:border-emerald-600 font-bold text-xs sm:text-sm flex items-center justify-center gap-1.5 transition-all duration-200 shadow-2xs hover:shadow-sm cursor-pointer group/btn">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600 group-hover/btn:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span>${addToBagText}</span>
                    </button>
                `;
            }
        }

        // Sync all product cards on the page with current cart
        function syncProductCardsWithCart() {
            document.querySelectorAll('.product-card').forEach(card => {
                renderCardActionButton(card);
            });
        }

        // Card Add to Bag
        function cardAddToCart(productId, btn) {
            const card = btn.closest('.product-card');
            const vTitle = card?.dataset?.selectedVariantTitle || '';
            const vPrice = card?.dataset?.selectedVariantPrice || '';
            const vQty = card?.dataset?.selectedVariantQty || '';

            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', 1);
            if (vTitle) formData.append('variant_title', vTitle);
            if (vPrice) formData.append('variant_price', vPrice);
            if (vQty) formData.append('variant_qty', vQty);

            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            if (csrfMeta) formData.append('csrf_token', csrfMeta.getAttribute('content'));

            btn.disabled = true;
            btn.innerHTML = `<svg class="animate-spin h-4 w-4 text-emerald-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;

            fetch('/sodai-dorkar/public/cart/add', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    updateCartUI(data);
                    showToast(data.message || (window.SODAI_STATE?.locale === 'bn' ? 'কার্টে যোগ করা হয়েছে' : 'Added to cart'));
                }
            })
            .catch(() => showToast('ত্রুটি ঘটেছে', 'error'));
        }

        // Card Change Quantity (+/-)
        function cardChangeQty(productId, delta, btn) {
            const card = btn.closest('.product-card');
            const cartItem = findCartItemForCard(card);
            if (!cartItem) return;

            const newQty = parseInt(cartItem.quantity) + delta;
            const itemKey = cartItem.cart_key;

            const formData = new FormData();
            formData.append('cart_key', itemKey);
            formData.append('product_id', itemKey);
            formData.append('quantity', Math.max(0, newQty));

            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            if (csrfMeta) formData.append('csrf_token', csrfMeta.getAttribute('content'));

            fetch('/sodai-dorkar/public/cart/update', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    updateCartUI(data);
                }
            })
            .catch(() => showToast('ত্রুটি ঘটেছে', 'error'));
        }

        // Initial render of drawer and sync cards
        document.addEventListener('DOMContentLoaded', () => {
            renderCartDrawer();
            renderSpendMoreOffersUI('drawer-spend-more-container', window.SODAI_STATE.spendMoreOffers);
            renderSpendMoreOffersUI('cart-spend-more-container', window.SODAI_STATE.spendMoreOffers);
            syncProductCardsWithCart();
        });
    </script>
</body>
</html>
