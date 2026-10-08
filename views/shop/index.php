<?php 
ob_start(); 
use Core\Lang;
Lang::init();
$__ = function($key, $r = []) { return Lang::get($key, $r); };
$locale = Lang::locale();
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';

// Helper function to extract and format unit badges
if (!function_exists('getProductUnits')) {
function getProductUnits($product) {
    if (!empty($product['unit_variants_json'])) {
        $decoded = json_decode($product['unit_variants_json'], true);
        if (!empty($decoded) && is_array($decoded)) {
            $variantsList = [];
            $defaultTitle = '';
            $defaultPrice = $product['sell_price'];
            $defaultQty = 1;

            foreach ($decoded as $v) {
                $variantsList[] = [
                    'title' => $v['title'],
                    'qty' => $v['qty'] ?? 1,
                    'price' => $v['price'],
                    'is_default' => !empty($v['is_default'])
                ];
                if (!empty($v['is_default']) && empty($defaultTitle)) {
                    $defaultTitle = $v['title'];
                    $defaultPrice = $v['price'];
                    $defaultQty = $v['qty'] ?? 1;
                }
            }

            if (empty($defaultTitle) && !empty($variantsList)) {
                $defaultTitle = $variantsList[0]['title'];
                $defaultPrice = $variantsList[0]['price'];
                $defaultQty = $variantsList[0]['qty'] ?? 1;
            }

            return [
                'has_custom' => true,
                'default_title' => $defaultTitle,
                'default_price' => $defaultPrice,
                'default_qty' => $defaultQty,
                'variants' => $variantsList
            ];
        }
    }

    $name = $product['name'] ?? '';
    $baseUnit = $product['base_unit'] ?? 'pcs';
    $primaryUnit = '';
    
    if (preg_match('/(\d+(\.\d+)?\s*(কেজি|গ্রাম|লিটার|মি\.লি\.|kg|gm|g|ml|ltr|liter|litre|pcs|pc|টি|প্যাকেট))/iu', $name, $matches)) {
        $primaryUnit = trim($matches[0]);
    }
    
    if (empty($primaryUnit)) {
        if ($baseUnit === 'kg') {
            $primaryUnit = '১ কেজি';
        } elseif ($baseUnit === 'liter') {
            $primaryUnit = '১ লিটার';
        } elseif (stripos($name, 'oil') !== false || stripos($name, 'তেল') !== false) {
            $primaryUnit = '৫০০ মিলি';
        } elseif (stripos($name, 'milk') !== false || stripos($name, 'দুধ') !== false) {
            $primaryUnit = '১ লিটার';
        } elseif (stripos($name, 'rice') !== false || stripos($name, 'চাল') !== false) {
            $primaryUnit = '১ কেজি';
        } else {
            $primaryUnit = '১ ' . $baseUnit;
        }
    }
    
    return [
        'has_custom' => false,
        'primary' => $primaryUnit,
        'variants' => []
    ];
}
}

// Category visual icon/image helper
if (!function_exists('getCategoryVisual')) {
function getCategoryVisual($cat, $base = '') {
    $img = $cat['image_path'] ?? '';
    if (!empty($img) && $img !== 'none') {
        $finalUrl = \Models\Category::getImageUrl($img, $base);
        return ['type' => 'image', 'val' => $finalUrl, 'bg' => 'bg-white border border-gray-100'];
    }
    
    $n = mb_strtolower($cat['name'] ?? '');
    
    // Priority mappings to actual category uploads with normalized base
    $imgMap = [
        'চাল' => '/uploads/categories/1768678696_rice.webp',
        'শস্য' => '/uploads/categories/1768678696_rice.webp',
        'rice' => '/uploads/categories/1768678696_rice.webp',
        'তেল' => '/uploads/categories/oil.webp',
        'ঘি' => '/uploads/categories/oil.webp',
        'oil' => '/uploads/categories/oil.webp',
        'মসলা' => '/uploads/categories/1768677548_spices.webp',
        'মশলা' => '/uploads/categories/1768677548_spices.webp',
        'spice' => '/uploads/categories/1768677548_spices.webp',
        'ডাল' => '/uploads/categories/1768678718_dal-or-lentil.webp',
        'lentil' => '/uploads/categories/1768678718_dal-or-lentil.webp',
        'daal' => '/uploads/categories/1768678718_dal-or-lentil.webp',
        'লবণ' => '/uploads/categories/1768678670_salt-sugar.webp',
        'লবন' => '/uploads/categories/1768678670_salt-sugar.webp',
        'চিনি' => '/uploads/categories/1768678670_salt-sugar.webp',
        'sugar' => '/uploads/categories/1768678670_salt-sugar.webp',
        'রেডি মিক্স' => '/uploads/categories/1779792323_ready-mix.webp',
        'সেমাই' => '/uploads/categories/1779792371_shemai-suji.webp',
        'সুজি' => '/uploads/categories/1779792371_shemai-suji.webp',
        'ফল' => '/uploads/categories/fruits-veg.jpg',
        'সবজি' => '/uploads/categories/fruits-veg.jpg',
        'শাক' => '/uploads/categories/fruits-veg.jpg',
        'fruit' => '/uploads/categories/fruits-veg.jpg',
        'vege' => '/uploads/categories/fruits-veg.jpg',
        'দুধ' => '/uploads/categories/dairy-milk.jpg',
        'দুগ্ধ' => '/uploads/categories/dairy-milk.jpg',
        'dairy' => '/uploads/categories/dairy-milk.jpg',
        'milk' => '/uploads/categories/dairy-milk.jpg',
        'ডিম' => '/uploads/categories/dairy-milk.jpg',
        'egg' => '/uploads/categories/dairy-milk.jpg',
        'বিস্কুট' => '/uploads/categories/snacks.jpg',
        'স্ন্যাক্স' => '/uploads/categories/snacks.jpg',
        'snack' => '/uploads/categories/snacks.jpg',
        'biscuit' => '/uploads/categories/snacks.jpg',
        'নাস্তা' => '/uploads/categories/breakfast.jpg',
        'নাশতা' => '/uploads/categories/breakfast.jpg',
        'বেকারি' => '/uploads/categories/breakfast.jpg',
        'breakfast' => '/uploads/categories/breakfast.jpg',
        'bakery' => '/uploads/categories/breakfast.jpg',
        'চা' => '/uploads/categories/tea-beverages.webp',
        'কফি' => '/uploads/categories/tea-beverages.webp',
        'পানীয়' => '/uploads/categories/tea-beverages.webp',
        'পানীয়' => '/uploads/categories/tea-beverages.webp',
        'tea' => '/uploads/categories/tea-beverages.webp',
        'coffee' => '/uploads/categories/tea-beverages.webp',
        'beverage' => '/uploads/categories/tea-beverages.webp',
        'juice' => '/uploads/categories/tea-beverages.webp',
        'মাছ ও মাংস' => '/uploads/categories/1788452780_Screenshot 2026-09-03 222555.png',
        'মাছ' => '/uploads/categories/1788452780_Screenshot 2026-09-03 222555.png',
        'মাংস' => '/uploads/categories/1788452809_Screenshot 2026-09-03 222638.png',
        'fish' => '/uploads/categories/1788452780_Screenshot 2026-09-03 222555.png',
        'meat' => '/uploads/categories/1788452809_Screenshot 2026-09-03 222638.png',
        'খেলনা' => '/uploads/categories/cat_toys.svg',
        'খেলাধুলা' => '/uploads/categories/cat_toys.svg',
        'toy' => '/uploads/categories/cat_toys.svg',
        'sports' => '/uploads/categories/cat_toys.svg',
        'গ্যাজেট' => '/uploads/categories/cat_gadgets.svg',
        'gadget' => '/uploads/categories/cat_gadgets.svg',
        'electronic' => '/uploads/categories/cat_gadgets.svg',
        'ডায়াপার' => '/uploads/categories/cat_diapers.svg',
        'ডায়াপার' => '/uploads/categories/cat_diapers.svg',
        'শিশু' => '/uploads/categories/cat_diapers.svg',
        'diaper' => '/uploads/categories/cat_diapers.svg',
        'baby' => '/uploads/categories/cat_diapers.svg',
        'পেট' => '/uploads/categories/cat_pet.svg',
        'pet' => '/uploads/categories/cat_pet.svg',
        'ফ্যাশন' => '/uploads/categories/cat_fashion.svg',
        'লাইফস্টাইল' => '/uploads/categories/cat_fashion.svg',
        'fashion' => '/uploads/categories/cat_fashion.svg',
        'lifestyle' => '/uploads/categories/cat_fashion.svg',
        'ক্লিনিং' => '/uploads/categories/cat_cleaning.svg',
        'cleaning' => '/uploads/categories/cat_cleaning.svg',
        'ব্যক্তিগত' => '/uploads/categories/cat_personal.svg',
        'personal' => '/uploads/categories/cat_personal.svg',
        'care' => '/uploads/categories/cat_personal.svg',
        'রান্না' => '/uploads/categories/1768677504_cooking.webp',
        'cooking' => '/uploads/categories/1768677504_cooking.webp',
        'খাবার' => '/uploads/categories/1768677504_cooking.webp',
        'মুদি' => '/uploads/categories/1768677504_cooking.webp',
        'food' => '/uploads/categories/1768677504_cooking.webp',
        'grocery' => '/uploads/categories/1768677504_cooking.webp',
        'চকলেট' => '/uploads/categories/snacks.jpg',
        'ক্যান্ডি' => '/uploads/categories/snacks.jpg',
        'chocolate' => '/uploads/categories/snacks.jpg',
        'আইসক্রিম' => '/uploads/categories/dairy-milk.jpg',
        'ice cream' => '/uploads/categories/dairy-milk.jpg',
    ];
    
    foreach ($imgMap as $keyword => $relPath) {
        if (strpos($n, $keyword) !== false) {
            $url = !empty($base) ? rtrim($base, '/') . $relPath : $relPath;
            return ['type' => 'image', 'val' => $url, 'bg' => 'bg-white border border-gray-100'];
        }
    }
    
    $defaultImg = (!empty($base) ? rtrim($base, '/') : '') . '/images/default-category.svg';
    return ['type' => 'image', 'val' => $defaultImg, 'bg' => 'bg-white border border-gray-100'];
}
}

// 1. Prepare Left Category Rail Categories (11 Reference Categories)
$railCategories = !empty($mainCategories) ? $mainCategories : (!empty($allCategories) ? array_slice($allCategories, 0, 11) : []);
if (empty($railCategories)) {
    $refCats = \Core\ShwapnoCatalog::MAIN_CATEGORIES;
    $dummyId = 1;
    foreach ($refCats as $bn => $slug) {
        $railCategories[] = [
            'id' => $dummyId++,
            'name' => $bn,
            'slug' => $slug,
            'image_path' => null
        ];
    }
}

// Prepare lookup map by ID
$catLookup = $catById ?? [];
if (empty($catLookup) && !empty($allCategories)) {
    foreach ($allCategories as $c) {
        $catLookup[$c['id']] = $c;
    }
}

// 2. Filter Status & Active Category Determination
$isFiltered = isset($isFiltered) ? $isFiltered : (!empty($_GET['category']) || !empty($_GET['sub']) || (isset($_GET['search']) && trim($_GET['search']) !== '') || !empty($_GET['deals']));
$activeCat = $isFiltered ? ($activeCategory ?? null) : null;

// 3. Subcategories or Filter Pills for Active State
$subs = $subCategories ?? [];
if ($isFiltered) {
    if (empty($subs) && !empty($activeCat['id']) && !empty($childrenMap[$activeCat['id']])) {
        foreach ($childrenMap[$activeCat['id']] as $cid) {
            if (isset($catLookup[$cid])) {
                $subs[] = $catLookup[$cid];
            }
        }
    }
} else {
    // When on Home (unfiltered), the horizontal filter pills display the main categories!
    $subs = !empty($mainCategories) ? $mainCategories : $railCategories;
}

// 4. Hero Banner Title & Subtitle
if (empty($bannerTitle)) {
    $bannerTitle = $isFiltered ? ($activeCat['name'] ?? 'পণ্যসমূহ') : 'সবচেয়ে জনপ্রিয় পণ্যসমূহ';
}
if (empty($bannerSubtitle)) {
    if ($isFiltered && !empty($subs)) {
        $subNames = array_map(function($s) { return $s['name']; }, $subs);
        $bannerSubtitle = implode(', ', array_slice($subNames, 0, 7));
    } else {
        $bannerSubtitle = 'সেরা মানের নিত্যপ্রয়োজনীয় পণ্য ও দ্রুত ডেলিভারি - আপনার দৈনন্দিন প্রয়োজনের সবকিছু এক জায়গায়';
    }
}

// Express delivery dynamic settings
$expressDeliveryEnabled = class_exists('\Models\Setting') ? (\Models\Setting::getValue('express_delivery_enabled', '0') == '1') : false;
$expressDeliveryTime = class_exists('\Models\Setting') ? trim(\Models\Setting::getValue('express_delivery_time', '২ ঘন্টা')) : '২ ঘন্টা';
if (empty($expressDeliveryTime) || $expressDeliveryTime === '30') $expressDeliveryTime = '২ ঘন্টা';
if (is_numeric($expressDeliveryTime)) {
    $expressDeliveryDisplay = ($locale === 'bn') ? Lang::bengaliNumber($expressDeliveryTime) . ' মিনিট' : $expressDeliveryTime . ' Mins';
} else {
    $expressDeliveryDisplay = ($locale !== 'bn' && (strpos($expressDeliveryTime, 'ঘন্ট') !== false || strpos($expressDeliveryTime, 'ঘণ্ট') !== false)) 
        ? '2 Hours' 
        : $expressDeliveryTime;
}

$activeSubCatName = '';
if (!empty($activeSubId) && isset($catLookup[$activeSubId])) {
    $activeSubCatName = $catLookup[$activeSubId]['name'];
}
?>

<!-- =======================================================
     MAIN CONTAINER: Two-Column Wireframe Layout
     Matches Desktop - 1 and iPhone 17 - 1 exactly
     ======================================================= -->
<main class="w-full max-w-[1720px] mx-auto px-2 sm:px-4 py-2.5 sm:py-5">
    
    <div class="flex items-start gap-2 sm:gap-3.5 md:gap-4 lg:gap-5">

        <!-- ==============================================
             LEFT VERTICAL CATEGORY RAIL ("All Category")
             Clean, Modern, Rounded-2xl Category Rail
             ============================================== -->
        <aside class="w-20 sm:w-24 md:w-26 lg:w-28 flex-shrink-0 sticky top-16 md:top-20 z-20 self-start">
            <div class="bg-white/95 backdrop-blur-md border border-gray-200/90 rounded-2xl sm:rounded-3xl p-1.5 sm:p-2 flex flex-col items-center shadow-xs max-h-[calc(100vh-5rem)] overflow-y-auto no-scrollbar">
                
                <!-- Rail Header: "All Category" -->
                <a href="<?= $base ?>/" class="text-[9px] sm:text-[11px] md:text-xs font-black text-gray-800 hover:text-emerald-700 text-center uppercase tracking-tight sm:tracking-wider mb-2 pb-1.5 border-b border-gray-100 w-full select-none block transition-colors" title="<?= $locale === 'bn' ? 'হোম পেজ ও সব চেয়ে জনপ্রিয় পণ্য' : 'Home & Most Popular' ?>">
                    <?= $locale === 'bn' ? 'সকল ক্যাটাগরি' : 'All Categories' ?>
                </a>

                <!-- Vertical Category List with Crisp Squircle Badges -->
                <div class="flex flex-col items-center gap-2.5 sm:gap-3.5 w-full py-1">
                    <?php foreach ($railCategories as $rc): ?>
                        <?php
                            $isRcActive = false;
                            if (!empty($activeCat)) {
                                if ((int)$activeCat['id'] === (int)$rc['id']) {
                                    $isRcActive = true;
                                } elseif (!empty($parentCategory) && (int)$parentCategory['id'] === (int)$rc['id']) {
                                    $isRcActive = true;
                                } else {
                                    $currP = $activeCat['parent_id'] ?? null;
                                    while ($currP && isset($catLookup[$currP])) {
                                        if ((int)$currP === (int)$rc['id']) {
                                            $isRcActive = true;
                                            break;
                                        }
                                        $currP = $catLookup[$currP]['parent_id'] ?? null;
                                    }
                                }
                            }
                            $vis = getCategoryVisual($rc, $base);
                        ?>
                        <a href="<?= $base ?>/?category=<?= $rc['id'] ?>" 
                           class="flex flex-col items-center group text-center w-full transition-transform active:scale-95 cursor-pointer"
                           title="<?= htmlspecialchars($rc['name']) ?>">
                            
                            <!-- Squircle / Circle Badge with High-Quality Contrast -->
                            <div class="w-14 h-14 sm:w-16 sm:h-16 md:w-17 md:h-17 rounded-2xl flex items-center justify-center transition-all duration-300 shadow-2xs relative overflow-hidden bg-white border-2 <?= $isRcActive ? 'border-emerald-600 ring-4 ring-emerald-500/20 scale-105 shadow-md bg-emerald-50/40' : 'border-gray-100 group-hover:border-emerald-400 group-hover:shadow-xs group-hover:scale-105' ?>">
                                <?php if ($vis['type'] === 'image'): ?>
                                    <img src="<?= htmlspecialchars($vis['val']) ?>" 
                                         alt="<?= htmlspecialchars($rc['name']) ?>" 
                                         class="w-full h-full object-contain p-1.5 sm:p-2 transition-transform duration-300 group-hover:scale-110" 
                                         loading="lazy"
                                         onerror="this.onerror=null; this.src='<?= $base ?>/images/default-category.svg';">
                                <?php else: ?>
                                    <span class="text-2xl sm:text-3xl md:text-3xl select-none leading-none"><?= $vis['val'] ?></span>
                                <?php endif; ?>
                            </div>

                            <!-- Bengali Category Label -->
                            <div class="w-full mt-1.5 px-0.5 flex flex-col items-center">
                                <span class="text-[10px] sm:text-[11px] font-bold block leading-tight text-center line-clamp-2 transition-colors <?= $isRcActive ? 'text-emerald-950 font-black' : 'text-gray-700 group-hover:text-emerald-800' ?>">
                                    <?= htmlspecialchars($rc['name']) ?>
                                </span>
                                <?php if ($isRcActive): ?>
                                    <div class="w-2 h-1 rounded-full bg-emerald-600 mt-1 shadow-2xs"></div>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>

            </div>
        </aside>

        <!-- ==============================================
             RIGHT MAIN SECTION
             Hero Banner + Trust Bar + Subcategory Pills + Product Grid
             ============================================== -->
        <section class="flex-1 min-w-0">

            <!-- 1. CATEGORY HERO BANNER (10/10 Modern Dual-Tone Mesh Banner) -->
            <div class="bg-gradient-to-br from-emerald-900 via-emerald-800 to-teal-950 rounded-3xl p-4 sm:p-6 lg:p-7 text-white shadow-xl relative overflow-hidden mb-3 sm:mb-4 border border-emerald-700/30">
                <!-- Ambient blur glow elements -->
                <div class="absolute -right-12 -bottom-12 w-64 h-64 sm:w-80 sm:h-80 bg-emerald-400/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute left-1/3 -top-12 w-52 h-52 bg-teal-400/15 rounded-full blur-2xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-4 lg:gap-6">
                    
                    <!-- Left: 3D Grocery Illustration + Headline & CTA Chips -->
                    <div class="flex items-center gap-3.5 sm:gap-5 w-full lg:w-auto">
                        <!-- 3D Grocery Shopping Cart Illustration -->
                        <div class="w-16 h-16 sm:w-22 sm:h-22 md:w-26 md:h-26 flex-shrink-0 relative">
                            <img src="<?= $base ?>/images/grocery_cart_hero.jpg" 
                                 alt="Fresh Grocery Basket" 
                                 class="w-full h-full object-contain filter drop-shadow-xl transform hover:scale-105 transition-transform duration-300 rounded-2xl bg-white/10 p-1 backdrop-blur-xs border border-white/15">
                        </div>

                        <!-- Text Information & Interactive Badges -->
                        <div class="min-w-0 flex-1">
                            <?php if (!$isFiltered): ?>
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-amber-300 text-[10px] sm:text-xs font-black mb-1.5 border border-white/20 shadow-2xs">
                                    <span>🔥</span>
                                    <span><?= $locale === 'bn' ? 'শীর্ষ চাহিদাসম্পন্ন ও সেরা বাজার' : 'Top In-Demand & Best Market' ?></span>
                                </div>
                            <?php elseif (!empty($parentCategory) && (int)$parentCategory['id'] !== (int)($activeCat['id'] ?? 0)): ?>
                                <a href="<?= $base ?>/?category=<?= $parentCategory['id'] ?>" class="inline-flex items-center gap-1.5 text-xs text-emerald-200 hover:text-white mb-1.5 transition-colors group font-semibold">
                                    <svg class="w-3.5 h-3.5 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                                    <span><?= htmlspecialchars($parentCategory['name']) ?></span>
                                </a>
                            <?php endif; ?>

                            <h1 class="text-xl sm:text-3xl md:text-4xl lg:text-[40px] font-black tracking-tight text-white leading-tight drop-shadow-xs">
                                <?= htmlspecialchars($bannerTitle ?? ($activeCat['name'] ?? ($locale === 'bn' ? 'সবচেয়ে জনপ্রিয় পণ্যসমূহ' : 'Most Popular Products'))) ?>
                            </h1>

                            <p class="text-xs sm:text-sm text-emerald-100/90 font-medium mt-1 leading-snug line-clamp-2 max-w-xl">
                                <?= htmlspecialchars($bannerSubtitle) ?>
                            </p>

                            <!-- Hero Action Chips (Flash Deals & Promo Coupon) -->
                            <div class="flex items-center gap-2 sm:gap-3 mt-3 flex-wrap">
                                <a href="<?= $base ?>/?deals=1" class="inline-flex items-center gap-1.5 bg-amber-400 hover:bg-amber-300 text-gray-950 font-black text-xs px-3.5 py-1.5 rounded-xl shadow-xs transition-transform active:scale-95">
                                    <span>⚡</span>
                                    <span><?= $locale === 'bn' ? 'ফ্ল্যাশ ডিলস' : 'Flash Deals' ?></span>
                                </a>

                                <button type="button" 
                                        onclick="navigator.clipboard.writeText('SODAI50'); showToast('<?= $locale === 'bn' ? 'কুপন SODAI50 কপি করা হয়েছে!' : 'Coupon SODAI50 copied!' ?>', 'success')" 
                                        class="inline-flex items-center gap-1.5 bg-white/15 hover:bg-white/25 text-white font-bold text-xs px-3 py-1.5 rounded-xl border border-white/20 transition-all cursor-pointer shadow-2xs backdrop-blur-xs active:scale-95"
                                        title="<?= $locale === 'bn' ? 'ক্লিক করে কুপন কপি করুন' : 'Click to copy coupon' ?>">
                                    <span>🎁</span>
                                    <span><?= $locale === 'bn' ? 'কুপন: SODAI50 (ট্যাপ করুন)' : 'Coupon: SODAI50 (Tap)' ?></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Right Accent Block: Glassmorphic Spotlight Card -->
                    <?php if (!$isFiltered && !empty($topPopularProduct)): ?>
                        <div class="w-full lg:w-auto flex items-center justify-between lg:justify-start gap-3 bg-emerald-950/60 border border-emerald-500/30 rounded-2xl p-3 shadow-lg backdrop-blur-md flex-shrink-0 group hover:border-amber-400/50 transition-all">
                            <div class="relative w-16 h-16 bg-white rounded-xl p-1 flex items-center justify-center shrink-0 shadow-inner overflow-hidden">
                                <span class="absolute top-0 left-0 bg-amber-500 text-white text-[8px] font-black px-1.5 py-0.5 rounded-br-lg shadow-2xs z-10">👑 #১</span>
                                <img src="<?= htmlspecialchars(\Models\Product::getImageUrl($topPopularProduct['image_path'] ?? '', $base)) ?>" 
                                     alt="<?= htmlspecialchars($topPopularProduct['name']) ?>" 
                                     class="w-full h-full object-contain group-hover:scale-110 transition-transform"
                                     onerror="this.onerror=null; this.src='<?= $base ?>/images/default-product.svg';">
                            </div>
                            <div class="text-left max-w-[210px]">
                                <span class="text-[10px] uppercase font-black tracking-wider text-amber-300 flex items-center gap-1">
                                    ⭐ <?= $locale === 'bn' ? '১ নম্বর সেরা পণ্য' : '#1 Best Seller' ?>
                                </span>
                                <a href="<?= $base ?>/product?id=<?= $topPopularProduct['id'] ?>" class="text-xs sm:text-sm font-bold text-white hover:text-amber-200 block truncate transition-colors" title="<?= htmlspecialchars($topPopularProduct['name']) ?>">
                                    <?= htmlspecialchars($topPopularProduct['name']) ?>
                                </a>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs sm:text-sm font-black text-amber-300">
                                        ৳<?= number_format($topPopularProduct['sell_price']) ?>
                                    </span>
                                    <?php if (\Models\Product::hasDiscount($topPopularProduct)): ?>
                                        <span class="text-[10px] text-emerald-300/70 line-through">
                                            ৳<?= number_format($topPopularProduct['regular_price']) ?>
                                        </span>
                                    <?php endif; ?>
                                    <a href="<?= $base ?>/product?id=<?= $topPopularProduct['id'] ?>" class="ml-auto text-[10px] font-black bg-emerald-700 hover:bg-emerald-600 text-white px-2 py-0.5 rounded-lg transition-colors">
                                        <?= $locale === 'bn' ? 'দেখুন' : 'View' ?> →
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="hidden md:flex flex-col items-end justify-center text-right flex-shrink-0">
                            <div class="bg-emerald-950/60 border border-emerald-600/30 rounded-2xl p-3 px-4 shadow-inner flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-xl text-emerald-200">
                                    ⚡
                                </div>
                                <div class="text-left">
                                    <span class="text-[10px] uppercase font-bold tracking-wider text-emerald-300 block">
                                        <?= $locale === 'bn' ? ($expressDeliveryEnabled ? 'এক্সপ্রেস সুপার ফাস্ট ডেলিভারি' : 'সরাসরি হোম ডেলিভারি') : ($expressDeliveryEnabled ? 'Express Fast Delivery' : 'Standard Delivery') ?>
                                    </span>
                                    <span class="text-xs sm:text-sm font-black text-white block">
                                        <?= $expressDeliveryEnabled 
                                            ? ($locale === 'bn' ? "{$expressDeliveryDisplay}র মধ্যে আপনার দরজায়" : "At Your Door in {$expressDeliveryDisplay}") 
                                            : ($locale === 'bn' ? 'বিশ্বস্ত ও নিরাপদ ডেলিভারি' : 'Reliable & Safe Delivery') ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

            <!-- TRUST & VALUE PROPOSITION BAR (Adds High-End E-Commerce Credibility) -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-2xs p-3 sm:p-3.5 mb-3 sm:mb-4 grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 text-base">
                        ⚡
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-xs font-black text-gray-900 leading-tight"><?= $locale === 'bn' ? 'দ্রুত ডেলিভারি' : 'Fast Delivery' ?></h4>
                        <p class="text-[10px] text-gray-400 font-medium leading-tight truncate">
                            <?= $expressDeliveryEnabled 
                                ? ($locale === 'bn' ? "{$expressDeliveryDisplay}র মধ্যে পৌঁছে যাবে" : "Within {$expressDeliveryDisplay}") 
                                : ($locale === 'bn' ? 'সরাসরি পৌঁছে যাবে আপনার ঠিকানায়' : 'Delivered to your address') ?>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 text-base">
                        🥦
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-xs font-black text-gray-900 leading-tight"><?= $locale === 'bn' ? '১০০% খাঁটি ও তাজা' : '100% Fresh & Halal' ?></h4>
                        <p class="text-[10px] text-gray-400 font-medium leading-tight truncate"><?= $locale === 'bn' ? 'বাছাইকৃত সেরা পণ্য' : 'Top quality guaranteed' ?></p>
                    </div>
                </div>

                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 text-base">
                        💵
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-xs font-black text-gray-900 leading-tight"><?= $locale === 'bn' ? 'ক্যাশ অন ডেলিভারি' : 'Cash on Delivery' ?></h4>
                        <p class="text-[10px] text-gray-400 font-medium leading-tight truncate"><?= $locale === 'bn' ? 'পণ্য পেয়ে টাকা দিন' : 'Pay after checking' ?></p>
                    </div>
                </div>

                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 text-base">
                        🔄
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-xs font-black text-gray-900 leading-tight"><?= $locale === 'bn' ? 'সহজ রিটার্ন' : 'Easy Return' ?></h4>
                        <p class="text-[10px] text-gray-400 font-medium leading-tight truncate"><?= $locale === 'bn' ? 'তাত্ক্ষণিক সমাধান' : 'Hassle-free replacement' ?></p>
                    </div>
                </div>
            </div>

            <!-- 2. SUBCATEGORY FILTER PILLS BAR -->
            <div class="mb-3 sm:mb-4">
                <div class="flex items-center gap-2 sm:gap-2.5 overflow-x-auto no-scrollbar py-1">
                    
                    <!-- If filtered, Back to All Products / Home Button -->
                    <?php if ($isFiltered): ?>
                        <a href="<?= $base ?>/" 
                           class="flex-shrink-0 pl-2 pr-3 py-1.5 rounded-full text-xs sm:text-sm font-bold transition-all flex items-center gap-1.5 bg-emerald-950 hover:bg-emerald-900 text-white border border-emerald-700/60 shadow-xs group"
                           title="<?= $locale === 'bn' ? 'হোম পেজে ফিরুন (সবচেয়ে জনপ্রিয় পণ্যসমূহ)' : 'Back to Home (Most Popular Products)' ?>">
                            <svg class="w-3.5 h-3.5 shrink-0 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            <span class="whitespace-nowrap"><?= $__('nav_home') ?></span>
                        </a>
                    <?php endif; ?>

                    <!-- Back Pill if drilled down to a child category -->
                    <?php if (!empty($parentCategory) && (int)$parentCategory['id'] !== (int)($activeCat['id'] ?? 0)): ?>
                        <a href="<?= $base ?>/?category=<?= $parentCategory['id'] ?>" 
                           class="flex-shrink-0 pl-2.5 pr-3.5 py-1.5 rounded-full text-xs sm:text-sm font-bold transition-all flex items-center gap-1.5 bg-emerald-900/90 hover:bg-emerald-950 text-white border border-emerald-600/50 shadow-xs group"
                           title="পূর্ববর্তী ক্যাটাগরি: <?= htmlspecialchars($parentCategory['name']) ?>">
                            <svg class="w-3.5 h-3.5 shrink-0 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                            </svg>
                            <span class="whitespace-nowrap"><?= htmlspecialchars($parentCategory['name']) ?></span>
                        </a>
                    <?php endif; ?>

                    <!-- Pill 1: "সবগুলো" (All) -->
                    <?php 
                        $isAllActive = empty($activeSubId) && (!$isFiltered || empty($activeCat));
                        $allPillUrl = !empty($activeCat) ? $base . '/?category=' . $activeCat['id'] : $base . '/';
                    ?>
                    <a href="<?= $allPillUrl ?>" 
                       class="flex-shrink-0 pl-1.5 sm:pl-2 pr-3.5 sm:pr-4 py-1.5 rounded-full text-xs sm:text-sm font-bold transition-all flex items-center gap-2 <?= $isAllActive ? 'bg-emerald-700 text-white shadow-md shadow-emerald-700/25 ring-2 ring-emerald-700/30' : 'bg-white hover:bg-emerald-50/80 text-gray-700 hover:text-emerald-900 border border-gray-200/90 hover:border-emerald-300 shadow-2xs font-semibold' ?>">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white flex items-center justify-center shrink-0 shadow-2xs <?= $isAllActive ? 'border border-white/40' : 'border border-gray-200' ?>">
                            <span class="text-xs sm:text-sm leading-none select-none">🛒</span>
                        </div>
                        <span class="whitespace-nowrap"><?= $locale === 'bn' ? 'সবগুলো' : 'All' ?></span>
                    </a>

                    <!-- Subcategory / Main Category Filter Pills -->
                    <?php foreach ($subs as $sc): ?>
                        <?php 
                            $hasChildCats = !empty($childrenMap[$sc['id']]);
                            if (!$isFiltered) {
                                $isScActive = false;
                                $scUrl = $base . '/?category=' . $sc['id'];
                            } else {
                                $isScActive = ($activeSubId && (int)$activeSubId === (int)$sc['id']);
                                if ($hasChildCats) {
                                    $scUrl = $base . '/?category=' . $sc['id'];
                                } else {
                                    $scUrl = $base . '/?category=' . ($activeCat['id'] ?? '') . '&sub=' . $sc['id'];
                                }
                            }
                            $scVis = getCategoryVisual($sc, $base);
                        ?>
                        <a href="<?= $scUrl ?>" 
                           class="flex-shrink-0 pl-1.5 sm:pl-2 pr-3.5 sm:pr-4 py-1.5 rounded-full text-xs sm:text-sm transition-all flex items-center gap-2 <?= $isScActive ? 'bg-emerald-700 text-white font-black shadow-md shadow-emerald-700/25 ring-2 ring-emerald-700/30 border border-emerald-700' : 'bg-white hover:bg-emerald-50 text-gray-700 hover:text-emerald-900 border border-gray-200/90 hover:border-emerald-300 font-semibold shadow-2xs' ?>">
                            
                            <!-- Subcategory Visual Image / Icon Container -->
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white flex items-center justify-center overflow-hidden shadow-2xs shrink-0 <?= $isScActive ? 'border border-white/40' : 'border border-gray-200' ?>">
                                <?php if ($scVis['type'] === 'image'): ?>
                                    <img src="<?= htmlspecialchars($scVis['val']) ?>" 
                                         alt="<?= htmlspecialchars($sc['name']) ?>" 
                                         class="w-full h-full object-contain p-0.5" 
                                         loading="lazy"
                                         onerror="this.onerror=null; this.src='<?= $base ?>/images/default-category.svg';">
                                <?php else: ?>
                                    <span class="text-xs sm:text-sm leading-none select-none"><?= $scVis['val'] ?></span>
                                <?php endif; ?>
                            </div>

                            <span class="whitespace-nowrap"><?= htmlspecialchars($sc['name']) ?></span>
                            <?php if (!empty($sc['total_product_count'])): ?>
                                <span class="text-[10px] px-1.5 py-0.5 rounded-full <?= $isScActive ? 'bg-white/20 text-white font-bold' : 'bg-gray-100 text-gray-600 font-bold' ?>">
                                    <?= $sc['total_product_count'] ?>
                                </span>
                            <?php endif; ?>
                            <?php if ($hasChildCats): ?>
                                <svg class="w-3 h-3 text-emerald-700 shrink-0 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- ACTIVE CATEGORY SELECTION HEADER (Shows at top above products when filtered) -->
            <?php if ($isFiltered): ?>
                <div class="mb-3.5 px-3.5 py-2.5 rounded-2xl bg-emerald-50/90 border border-emerald-200/90 flex items-center justify-between text-xs shadow-2xs">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 animate-pulse shrink-0"></span>
                        <span class="text-gray-500 font-medium shrink-0"><?= $locale === 'bn' ? 'সিলেক্টেড ক্যাটাগরি:' : 'Selected Category:' ?></span>
                        <span class="font-bold text-emerald-950 truncate text-xs sm:text-sm">
                            <?= htmlspecialchars($activeCat['name'] ?? ($bannerTitle ?? 'পণ্যসমূহ')) ?>
                        </span>
                        <?php if (!empty($activeSubCatName)): ?>
                            <span class="text-gray-400 font-bold">›</span>
                            <span class="font-bold text-emerald-800 truncate"><?= htmlspecialchars($activeSubCatName) ?></span>
                        <?php endif; ?>
                        <span class="text-[10px] font-bold bg-white text-emerald-800 px-2 py-0.5 rounded-full border border-emerald-200 shrink-0">
                            <?= count($products) ?> <?= $locale === 'bn' ? 'টি পণ্য' : 'items' ?>
                        </span>
                    </div>
                    <a href="<?= $base ?>/" class="text-xs font-bold text-red-600 hover:text-red-700 flex items-center gap-1 shrink-0 ml-2 hover:bg-red-50 px-2.5 py-1 rounded-lg transition-colors border border-red-200/60" title="<?= $locale === 'bn' ? 'সকল পণ্য দেখুন' : 'Show all' ?>">
                        <span>✕</span>
                        <span><?= $locale === 'bn' ? 'মুছুন' : 'Clear' ?></span>
                    </a>
                </div>
            <?php endif; ?>

            <!-- 3. PRODUCT CARDS GRID (6 columns on Desktop, 2 columns on Mobile) -->
            <?php if (empty($products)): ?>
                <div class="bg-white rounded-3xl p-10 sm:p-14 text-center border border-gray-100 shadow-xs max-w-md mx-auto my-6">
                    <div class="w-16 h-16 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-3 text-3xl">
                        🛒
                    </div>
                    <h3 class="text-base font-bold text-gray-800"><?= $__('products_empty_title') ?></h3>
                    <p class="text-xs text-gray-400 mt-1"><?= $__('products_empty_subtitle') ?></p>
                    <a href="<?= $base ?>/" class="inline-block mt-4 px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 transition-colors">
                        <?= $locale === 'bn' ? 'সব পণ্য দেখুন' : 'View All Products' ?>
                    </a>
                </div>
            <?php else: ?>
                <!-- RESPONSIVE GRID: 6 columns on wide desktop, 2 columns on mobile -->
                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-2.5 sm:gap-3.5 lg:gap-4" id="products-catalog-grid">
                    <?php foreach ($products as $pIdx => $product): ?>
                        <?php 
                            $uInfo = getProductUnits($product); 
                            $hasCustom = !empty($uInfo['has_custom']);
                            $initialPrice = $hasCustom ? $uInfo['default_price'] : $product['sell_price'];
                            $initialTitle = $hasCustom ? $uInfo['default_title'] : '';
                            $initialQty = $hasCustom ? $uInfo['default_qty'] : 1;

                            $hasDiscount = \Models\Product::hasDiscount($product);
                            $discountPercent = $hasDiscount ? \Models\Product::getDiscountPercent($product) : 0;
                            $discountSavings = $hasDiscount ? ((float)($product['regular_price'] ?? 0) - (float)$product['sell_price']) : 0;
                            $stockQtyNum = floatval($product['stock_qty']);
                            $stockClean = (floor($stockQtyNum) == $stockQtyNum) 
                                ? intval($stockQtyNum) 
                                : rtrim(rtrim(number_format($stockQtyNum, 3), '0'), '.');

                            $isTopOne = ($pIdx === 0 && !$isFiltered);
                            $demandPct = intval($product['demand_percentage'] ?? 0);
                            $totalSold = intval($product['total_sold'] ?? 0);
                            $isOutOfStock = (($product['availability_status'] ?? '') === 'out_of_stock');
                            $criteria = \Models\Product::getCriteriaData($product, $locale);
                            $productAddons = $criteria['addons'];
                            $hasAddons = $criteria['has_addons'];
                            $isBogo = $criteria['is_bogo'];
                            $hasCriteria = $criteria['has_criteria'];
                            $criteriaType = $criteria['criteria_type'];
                            $badgeLabel = $criteria['badge_label'];
                            $buttonLabel = $criteria['button_label'];
                            $specialBadge = $product['special_badge'] ?? ($isBogo ? 'bogo' : 'none');
                            $productImg = \Models\Product::getImageUrl($product['image_path'] ?? '', $base);
                        ?>

                        <!-- Product Card (10/10 Modern Design with Smooth Hover Lift & Shadows) -->
                        <div class="product-card bg-white rounded-2xl border border-gray-100/90 hover:border-emerald-400 shadow-[0_2px_10px_rgba(0,0,0,0.03)] hover:shadow-[0_12px_28px_rgba(0,0,0,0.08)] hover:-translate-y-1 transition-all duration-300 group flex flex-col h-full overflow-hidden relative"
                             data-product-id="<?= $product['id'] ?>"
                             data-product-name="<?= htmlspecialchars($product['name'], ENT_QUOTES) ?>"
                             data-product-img="<?= htmlspecialchars($productImg, ENT_QUOTES) ?>"
                             data-product-price="<?= floatval($initialPrice) ?>"
                             data-product-reg-price="<?= floatval($product['regular_price'] ?? 0) ?>"
                             data-has-addons="<?= $hasAddons ? '1' : '0' ?>"
                             data-is-bogo="<?= $isBogo ? '1' : '0' ?>"
                             data-has-criteria="<?= $hasCriteria ? '1' : '0' ?>"
                             data-criteria-type="<?= htmlspecialchars($criteriaType, ENT_QUOTES) ?>"
                             data-badge-label="<?= htmlspecialchars($badgeLabel, ENT_QUOTES) ?>"
                             data-button-label="<?= htmlspecialchars($buttonLabel, ENT_QUOTES) ?>"
                             data-special-badge="<?= htmlspecialchars($specialBadge, ENT_QUOTES) ?>"
                             data-addons='<?= htmlspecialchars(json_encode($productAddons, JSON_UNESCAPED_UNICODE), ENT_QUOTES) ?>'
                             data-variants='<?= htmlspecialchars(json_encode($uInfo['variants'] ?? [], JSON_UNESCAPED_UNICODE), ENT_QUOTES) ?>'
                             data-out-of-stock="<?= $isOutOfStock ? '1' : '0' ?>"
                             data-base-unit="<?= htmlspecialchars($product['base_unit'] ?? 'pcs') ?>"
                             data-selected-variant-title="<?= htmlspecialchars($initialTitle) ?>"
                             data-selected-variant-price="<?= $initialPrice ?>"
                             data-selected-variant-qty="<?= $initialQty ?>">
                            
                            <?php if (!empty($_SESSION['user_id'])): ?>
                            <!-- Admin Quick Edit Floating Action (WordPress Style) -->
                            <a href="<?= $base ?>/admin/products/edit?id=<?= $product['id'] ?>" target="_blank" title="<?= $locale === 'bn' ? 'অ্যাডমিনে এডিট করুন' : 'Edit in Admin' ?>" class="absolute top-2 right-2 z-20 w-7 h-7 rounded-full bg-slate-900/90 hover:bg-amber-500 text-amber-300 hover:text-slate-950 flex items-center justify-center shadow-lg transition-all hover:scale-110 border border-white/20">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </a>
                            <?php endif; ?>

                            <!-- Badges (Special, Popularity, Discount & Stock) -->
                            <div class="absolute top-2 left-2 right-2 z-10 flex items-start justify-between pointer-events-none gap-1">
                                <div class="flex flex-col gap-1 items-start">
                                    <?php if (!empty($badgeLabel)): ?>
                                        <span class="bg-gradient-to-r from-amber-500 to-rose-600 text-white text-[9px] sm:text-[10px] font-black px-2 py-0.5 rounded-full shadow-2xs flex items-center gap-0.5 <?= $isBogo ? 'animate-pulse' : '' ?>">
                                            <?= htmlspecialchars($badgeLabel) ?>
                                        </span>
                                    <?php endif; ?>

                                    <?php if ($specialBadge === 'hot_deal'): ?>
                                        <span class="bg-gradient-to-r from-red-600 to-orange-500 text-white text-[9px] sm:text-[10px] font-black px-2 py-0.5 rounded-full shadow-2xs flex items-center gap-0.5">
                                            ⚡ <?= $locale === 'bn' ? 'হট ডিল' : 'Hot Deal' ?>
                                        </span>
                                    <?php elseif ($specialBadge === 'fresh_catch' && empty($badgeLabel)): ?>
                                        <span class="bg-gradient-to-r from-cyan-600 to-blue-600 text-white text-[9px] sm:text-[10px] font-black px-2 py-0.5 rounded-full shadow-2xs flex items-center gap-0.5">
                                            🐟 <?= $locale === 'bn' ? 'তাজা সংগ্রহ' : 'Fresh Catch' ?>
                                        </span>
                                    <?php elseif ($specialBadge === 'halal_meat' && empty($badgeLabel)): ?>
                                        <span class="bg-gradient-to-r from-emerald-700 to-teal-700 text-white text-[9px] sm:text-[10px] font-black px-2 py-0.5 rounded-full shadow-2xs flex items-center gap-0.5">
                                            🥩 <?= $locale === 'bn' ? 'হালাল মাংস' : 'Halal Meat' ?>
                                        </span>
                                    <?php endif; ?>

                                    <?php if ($isTopOne): ?>
                                        <span class="bg-gradient-to-r from-amber-500 to-orange-500 text-white text-[9px] sm:text-[10px] font-black px-2 py-0.5 rounded-full shadow-2xs flex items-center gap-0.5">
                                            👑 <?= $locale === 'bn' ? '#১ সেরা পণ্য' : '#1 Popular' ?>
                                        </span>
                                    <?php elseif ($demandPct > 0): ?>
                                        <span class="bg-gradient-to-r from-amber-500 to-orange-500 text-white text-[9px] sm:text-[10px] font-bold px-2 py-0.5 rounded-full shadow-2xs flex items-center gap-0.5">
                                            🔥 <?= $demandPct ?>% <?= $locale === 'bn' ? 'জনপ্রিয়' : 'Popular' ?>
                                        </span>
                                    <?php elseif ($totalSold > 0): ?>
                                        <span class="bg-amber-600 text-white text-[9px] sm:text-[10px] font-bold px-2 py-0.5 rounded-full shadow-2xs flex items-center gap-0.5">
                                            🔥 <?= $locale === 'bn' ? 'সেরা বিক্রিত' : 'Best Seller' ?>
                                        </span>
                                    <?php endif; ?>

                                    <?php if ($hasDiscount): ?>
                                        <span class="bg-emerald-600 text-white text-[9px] sm:text-[10px] font-black px-2 py-0.5 rounded-full shadow-2xs tracking-tight">
                                            <?= $discountPercent ?>% <?= $locale === 'bn' ? 'ছাড়' : 'OFF' ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <?php if ($isOutOfStock): ?>
                                    <span class="bg-rose-600 text-white text-[9px] font-bold px-2 py-0.5 rounded-full shadow-2xs">
                                        <?= $__('products_out_of_stock') ?>
                                    </span>
                                <?php elseif ($stockQtyNum < 10 && $stockQtyNum > 0): ?>
                                    <span class="bg-amber-100 text-amber-900 border border-amber-300 text-[9px] font-bold px-1.5 py-0.5 rounded-full shadow-2xs">
                                        <?= $locale === 'bn' ? 'বাকি: ' : 'Left: ' ?><?= $stockClean ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Centered Clean Product Image Container -->
                            <a href="<?= $base ?>/product?id=<?= $product['id'] ?>" class="relative bg-white h-44 sm:h-52 w-full p-3 flex items-center justify-center overflow-hidden cursor-pointer group/img">
                                <?php 
                                $fallbackImg = !empty($base) ? rtrim($base, '/') . '/images/default-product.svg' : '/images/default-product.svg';
                                ?>
                                <img src="<?= htmlspecialchars($productImg) ?>" 
                                     alt="<?= htmlspecialchars($product['name']) ?>" 
                                     class="h-full w-full object-contain mix-blend-multiply transition-transform duration-500 group-hover/img:scale-108" 
                                     loading="lazy"
                                     onerror="this.onerror=null; this.src='<?= $fallbackImg ?>';">
                            </a>

                            <!-- Card Content (Typography Hierarchy & Spacing) -->
                            <div class="p-3 sm:p-3.5 pt-1 flex flex-col flex-grow bg-white">
                                <!-- Product Name -->
                                <h3 class="font-bold text-gray-900 text-xs sm:text-[13px] leading-snug line-clamp-2 min-h-[2.2rem] sm:min-h-[2.5rem] mb-1 group-hover:text-emerald-700 transition-colors" 
                                    title="<?= htmlspecialchars($product['name']) ?>">
                                    <a href="<?= $base ?>/product?id=<?= $product['id'] ?>" class="hover:text-emerald-700 transition-colors">
                                        <?= htmlspecialchars($product['name']) ?>
                                    </a>
                                </h3>

                                <!-- Unit / Weight -->
                                <div class="text-[10px] sm:text-[11px] text-gray-400 font-semibold mb-2 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                                    </svg>
                                    <span><?= htmlspecialchars($hasCustom ? $initialTitle : $uInfo['primary']) ?></span>
                                </div>

                                <!-- Price Section & Savings Badge -->
                                <div class="flex items-baseline gap-1.5 mb-2.5 flex-wrap">
                                    <div class="flex items-baseline gap-0.5">
                                        <span class="text-xs font-bold text-emerald-800 leading-none">৳</span>
                                        <span class="card-price text-sm sm:text-base font-black text-gray-900 leading-none">
                                            <?= number_format($initialPrice) ?>
                                        </span>
                                    </div>

                                    <?php if ($hasDiscount): ?>
                                        <span class="card-regular-price text-[10px] sm:text-[11px] text-gray-400 line-through font-medium leading-none">
                                            ৳<?= number_format($product['regular_price']) ?>
                                        </span>
                                        <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200/80 leading-none">
                                            ৳<?= number_format($discountSavings) ?> <?= $locale === 'bn' ? 'সাশ্রয়' : 'Save' ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Bottom Action: Add to Bag / Interactive Stepper (Solid Punchy Emerald) -->
                                <div class="mt-auto pt-1 card-action-container" data-product-id="<?= $product['id'] ?>">
                                    <?php if ($isOutOfStock): ?>
                                        <button type="button" disabled class="w-full py-2 px-2.5 rounded-xl bg-gray-100 text-gray-400 border border-gray-200 font-bold text-xs flex items-center justify-center gap-1.5 cursor-not-allowed">
                                            <span><?= $__('products_out_of_stock') ?></span>
                                        </button>
                                    <?php elseif ($hasCriteria): ?>
                                        <button type="button" 
                                                onclick="openQuickCriteriaModal(<?= $product['id'] ?>, this)" 
                                                class="w-full py-2 px-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 active:scale-95 text-white font-black text-xs flex items-center justify-center gap-1.5 transition-all duration-200 shadow-xs hover:shadow-amber-500/30 cursor-pointer group/btn <?= $isBogo ? 'animate-pulse' : '' ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z" />
                                            </svg>
                                            <span><?= htmlspecialchars($buttonLabel) ?></span>
                                        </button>
                                    <?php else: ?>
                                        <button type="button" 
                                                onclick="cardAddToCart(<?= $product['id'] ?>, this)" 
                                                class="w-full py-2 px-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-extrabold text-xs flex items-center justify-center gap-1.5 transition-all duration-200 shadow-xs hover:shadow-emerald-600/30 cursor-pointer group/btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white transition-transform group-hover/btn:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                            </svg>
                                            <span><?= $__('add_to_bag') ?></span>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </section>

    </div>

</main>

<?php 
$content = ob_get_clean();
require 'layout.php';
?>