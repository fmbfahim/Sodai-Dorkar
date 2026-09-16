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
function getCategoryVisual($cat) {
    if (!empty($cat['image_path'])) {
        return ['type' => 'image', 'val' => $cat['image_path'], 'bg' => 'bg-white border border-gray-100'];
    }
    $n = mb_strtolower($cat['name'] ?? '');
    
    if (strpos($n, 'মাছ ও মাংস') !== false) {
        return ['type' => 'image', 'val' => '/sodai-dorkar/public/uploads/categories/1788452780_Screenshot 2026-09-03 222555.png', 'bg' => 'bg-white border border-gray-100'];
    }
    if (strpos($n, 'দুধ') !== false || strpos($n, 'দুগ্ধ') !== false || strpos($n, 'dairy') !== false) {
        return ['type' => 'image', 'val' => '/sodai-dorkar/public/uploads/products/bottle-milk-1liter.jpg', 'bg' => 'bg-white border border-gray-100'];
    }
    
    if (strpos($n, 'ফল') !== false || strpos($n, 'fruit') !== false) return ['type' => 'emoji', 'val' => '🍎', 'bg' => 'bg-rose-50 text-rose-500'];
    if (strpos($n, 'শাক') !== false || strpos($n, 'সবজি') !== false || strpos($n, 'vege') !== false) return ['type' => 'emoji', 'val' => '🥦', 'bg' => 'bg-emerald-50 text-emerald-600'];
    if (strpos($n, 'ডিম') !== false || strpos($n, 'egg') !== false) return ['type' => 'emoji', 'val' => '🥚', 'bg' => 'bg-amber-50 text-amber-600'];
    if (strpos($n, 'চাল') !== false || strpos($n, 'rice') !== false) return ['type' => 'emoji', 'val' => '🌾', 'bg' => 'bg-amber-50 text-amber-700'];
    if (strpos($n, 'ডাল') !== false || strpos($n, 'dal') !== false || strpos($n, 'lentil') !== false) return ['type' => 'emoji', 'val' => '🥣', 'bg' => 'bg-orange-50 text-orange-600'];
    if (strpos($n, 'তেল') !== false || strpos($n, 'oil') !== false) return ['type' => 'emoji', 'val' => '🫒', 'bg' => 'bg-lime-50 text-lime-700'];
    if (strpos($n, 'চা') !== false || strpos($n, 'tea') !== false) return ['type' => 'emoji', 'val' => '🍵', 'bg' => 'bg-emerald-50 text-emerald-600'];
    if (strpos($n, 'মাছ') !== false || strpos($n, 'fish') !== false) return ['type' => 'emoji', 'val' => '🐟', 'bg' => 'bg-cyan-50 text-cyan-600'];
    if (strpos($n, 'মাংস') !== false || strpos($n, 'meat') !== false) return ['type' => 'emoji', 'val' => '🥩', 'bg' => 'bg-rose-50 text-rose-600'];
    if (strpos($n, 'লবন') !== false || strpos($n, 'চিনি') !== false || strpos($n, 'salt') !== false || strpos($n, 'sugar') !== false) return ['type' => 'emoji', 'val' => '🧂', 'bg' => 'bg-blue-50 text-blue-600'];
    if (strpos($n, 'মশলা') !== false || strpos($n, 'spice') !== false) return ['type' => 'emoji', 'val' => '🌶️', 'bg' => 'bg-red-50 text-red-600'];
    if (strpos($n, 'সেমাই') !== false || strpos($n, 'সুজি') !== false || strpos($n, 'মিক্স') !== false) return ['type' => 'emoji', 'val' => '🥣', 'bg' => 'bg-amber-50 text-amber-700'];
    if (strpos($n, 'রান্না') !== false || strpos($n, 'cook') !== false) return ['type' => 'emoji', 'val' => '🍳', 'bg' => 'bg-amber-50 text-amber-600'];
    if (strpos($n, 'আইসক্রিম') !== false || strpos($n, 'ice cream') !== false) return ['type' => 'emoji', 'val' => '🍦', 'bg' => 'bg-pink-50 text-pink-500'];
    if (strpos($n, 'ক্যান্ডি') !== false || strpos($n, 'চকলেট') !== false || strpos($n, 'chocolate') !== false || strpos($n, 'candy') !== false) return ['type' => 'emoji', 'val' => '🍫', 'bg' => 'bg-amber-50 text-amber-800'];
    if (strpos($n, 'জল খাবার') !== false || strpos($n, 'নাশতা') !== false || strpos($n, 'snack') !== false || strpos($n, 'breakfast') !== false) return ['type' => 'emoji', 'val' => '🥪', 'bg' => 'bg-orange-50 text-orange-600'];
    if (strpos($n, 'পানীয়') !== false || strpos($n, 'beverage') !== false || strpos($n, 'juice') !== false || strpos($n, 'drinks') !== false) return ['type' => 'emoji', 'val' => '🧃', 'bg' => 'bg-teal-50 text-teal-600'];
    if (strpos($n, 'বেকিং') !== false || strpos($n, 'baking') !== false || strpos($n, 'cake') !== false) return ['type' => 'emoji', 'val' => '🧁', 'bg' => 'bg-purple-50 text-purple-600'];
    if (strpos($n, 'হিমায়িত') !== false || strpos($n, 'টিনজাত') !== false || strpos($n, 'frozen') !== false || strpos($n, 'canned') !== false) return ['type' => 'emoji', 'val' => '🥫', 'bg' => 'bg-blue-50 text-blue-600'];
    if (strpos($n, 'ডায়বেটিক') !== false || strpos($n, 'diabetic') !== false) return ['type' => 'emoji', 'val' => '🥗', 'bg' => 'bg-emerald-50 text-emerald-700'];
    if (strpos($n, 'সস') !== false || strpos($n, 'আচার') !== false || strpos($n, 'pickle') !== false || strpos($n, 'sauce') !== false) return ['type' => 'emoji', 'val' => '🫙', 'bg' => 'bg-red-50 text-red-700'];
    return ['type' => 'emoji', 'val' => '🛒', 'bg' => 'bg-emerald-50 text-emerald-600'];
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

// 2. Active Category Determination
$activeCat = $activeCategory ?? (!empty($railCategories) ? $railCategories[0] : null);

// 3. Subcategories for Active Category
$subs = $subCategories ?? [];
if (empty($subs) && !empty($activeCat['id']) && !empty($childrenMap[$activeCat['id']])) {
    foreach ($childrenMap[$activeCat['id']] as $cid) {
        foreach ($allCategories as $c) {
            if ($c['id'] == $cid) {
                $subs[] = $c;
                break;
            }
        }
    }
}
if (empty($subs) && !empty($activeCat) && mb_strpos($activeCat['name'], 'রান্না') !== false) {
    // Default subcategories if database hasn't populated children yet
    $sampleSubs = ['চাল ও শস্য', 'ভোজ্য তেল ও ঘি', 'মসলা', 'ডাল ও ডালজাতীয়', 'লবণ ও চিনি', 'রেডি মিক্স', 'সেমাই ও সুজি'];
    $subDummyId = 100;
    foreach ($sampleSubs as $sName) {
        $subs[] = [
            'id' => $subDummyId++,
            'name' => $sName,
            'total_product_count' => null
        ];
    }
}

// 4. Hero Banner Subtitle
if (empty($bannerSubtitle)) {
    $subNames = array_map(function($s) { return $s['name']; }, $subs);
    $bannerSubtitle = !empty($subNames) ? implode(', ', array_slice($subNames, 0, 7)) : 'চাল, ডাল, মশলা, রেডি মিক্স, লবণ এবং চিনি, সেমাই ও সুজি';
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
             Sticky vertical rail matching wireframe
             ============================================== -->
        <aside class="w-16 sm:w-20 md:w-24 lg:w-28 flex-shrink-0 sticky top-16 md:top-20 z-20 self-start">
            <div class="bg-[#dcfce7]/75 sm:bg-[#dcfce7]/90 border border-emerald-200/80 rounded-2xl sm:rounded-3xl p-1 sm:p-2 flex flex-col items-center shadow-xs max-h-[calc(100vh-5rem)] overflow-y-auto no-scrollbar">
                
                <!-- Rail Header: "All Category" -->
                <div class="text-[9px] sm:text-[11px] md:text-xs font-black text-emerald-900 text-center uppercase tracking-tight sm:tracking-wider mb-2 pb-1 border-b border-emerald-200/80 w-full select-none">
                    All Category
                </div>

                <!-- Vertical Category List with Circular Badges -->
                <div class="flex flex-col items-center gap-2.5 sm:gap-3.5 w-full py-1">
                    <?php foreach ($railCategories as $rc): ?>
                        <?php
                            $isRcActive = ($activeCat && (int)$activeCat['id'] === (int)$rc['id']);
                            $vis = getCategoryVisual($rc);
                        ?>
                        <a href="<?= $base ?>/?category=<?= $rc['id'] ?>" 
                           class="flex flex-col items-center group text-center w-full transition-transform active:scale-95 cursor-pointer"
                           title="<?= htmlspecialchars($rc['name']) ?>">
                            
                            <!-- Circular Badge (Green circle matching wireframe) -->
                            <div class="w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 rounded-full flex items-center justify-center transition-all duration-200 shadow-xs relative overflow-hidden <?= $isRcActive ? 'bg-emerald-700 text-white ring-3 ring-emerald-500 ring-offset-2 scale-105 shadow-md' : 'bg-emerald-700/90 sm:bg-emerald-700 text-white hover:bg-emerald-800 hover:scale-105' ?>">
                                <?php if ($vis['type'] === 'image'): ?>
                                    <img src="<?= htmlspecialchars($vis['val']) ?>" alt="<?= htmlspecialchars($rc['name']) ?>" class="w-full h-full object-contain p-1 sm:p-1.5 transition-transform duration-200 group-hover:scale-110" onerror="this.parentElement.innerHTML='🛒'">
                                <?php else: ?>
                                    <span class="text-base sm:text-xl md:text-2xl select-none leading-none"><?= $vis['val'] ?></span>
                                <?php endif; ?>
                            </div>

                            <!-- Underline Bar / Bengali Category Label (as in Desktop-1 and iPhone 17-1 wireframes) -->
                            <div class="w-full mt-1 px-0.5 flex flex-col items-center">
                                <span class="text-[8px] sm:text-[10px] md:text-[11px] font-bold block leading-tight text-center line-clamp-2 transition-colors <?= $isRcActive ? 'text-emerald-950 font-black' : 'text-emerald-900/90 group-hover:text-emerald-950' ?>">
                                    <?= htmlspecialchars($rc['name']) ?>
                                </span>
                                <!-- Green horizontal bar indicator beneath each circle -->
                                <div class="w-5 sm:w-7 h-1 rounded-full mt-0.5 transition-all <?= $isRcActive ? 'bg-emerald-700 h-1.5' : 'bg-emerald-600/70 group-hover:bg-emerald-700' ?>"></div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>

            </div>
        </aside>

        <!-- ==============================================
             RIGHT MAIN SECTION
             Hero Banner + Subcategory Pills + Product Grid
             ============================================== -->
        <section class="flex-1 min-w-0">

            <!-- 1. CATEGORY HERO BANNER (Matches Desktop - 1 & iPhone 17 - 1) -->
            <div class="bg-gradient-to-r from-emerald-800 via-emerald-700 to-emerald-800 rounded-2xl sm:rounded-3xl p-3 sm:p-5 lg:p-6 text-white shadow-md relative overflow-hidden mb-3 sm:mb-4 border border-emerald-900/40">
                <!-- Ambient blur lighting -->
                <div class="absolute -right-8 -bottom-8 w-44 h-44 sm:w-60 sm:h-60 bg-emerald-500/25 rounded-full blur-2xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-6">
                    
                    <!-- Left: 3D Grocery Cart + Category Title & Bengali Subtitle -->
                    <div class="flex items-center gap-3 sm:gap-5 w-full sm:w-auto">
                        <!-- 3D Grocery Shopping Cart Illustration -->
                        <div class="w-14 h-14 sm:w-20 sm:h-20 md:w-24 md:h-24 lg:w-28 lg:h-28 flex-shrink-0 relative">
                            <img src="<?= $base ?>/images/grocery_cart_hero.jpg" 
                                 alt="Fresh Grocery Basket" 
                                 class="w-full h-full object-contain filter drop-shadow-lg transform hover:scale-105 transition-transform duration-300 rounded-2xl bg-white/10 p-0.5 backdrop-blur-xs">
                        </div>

                        <!-- Text Information -->
                        <div class="min-w-0 flex-1">
                            <h1 class="text-xl sm:text-3xl md:text-4xl lg:text-5xl font-black tracking-tight text-white leading-tight">
                                <?= htmlspecialchars($activeCat['name'] ?? 'রান্নাবান্না') ?>
                            </h1>
                            <p class="text-[11px] sm:text-xs md:text-sm lg:text-base text-emerald-100 font-medium mt-1 leading-snug line-clamp-2 max-w-xl">
                                <?= htmlspecialchars($bannerSubtitle) ?>
                            </p>
                        </div>
                    </div>

                    <!-- Right Accent Block (Matching the dark green accent in Desktop - 1 wireframe) -->
                    <div class="hidden md:flex flex-col items-end justify-center text-right flex-shrink-0">
                        <div class="bg-emerald-900/80 border border-emerald-600/40 rounded-2xl p-3 px-4 shadow-inner flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-xl text-emerald-200">
                                🛒
                            </div>
                            <div class="text-left">
                                <span class="text-[10px] uppercase font-bold tracking-wider text-emerald-300 block">সরাসরি অনলাইন বাজার</span>
                                <span class="text-xs sm:text-sm font-black text-white block">সেরা দামে সেরা মান</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- 2. SUBCATEGORY FILTER PILLS BAR -->
            <div class="mb-3 sm:mb-4">
                <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1">
                    <!-- Pill 1: "সবগুলো" (All) -->
                    <?php 
                        $isAllActive = empty($activeSubId);
                        $allPillUrl = !empty($activeCat) ? $base . '/?category=' . $activeCat['id'] : $base . '/';
                    ?>
                    <a href="<?= $allPillUrl ?>" 
                       class="flex-shrink-0 px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-bold transition-all <?= $isAllActive ? 'bg-emerald-800 text-white shadow-sm ring-2 ring-emerald-700/40' : 'bg-emerald-100 hover:bg-emerald-200 text-emerald-900' ?>">
                        সবগুলো
                    </a>

                    <!-- Subcategory Pills -->
                    <?php foreach ($subs as $sc): ?>
                        <?php 
                            $isScActive = ($activeSubId && (int)$activeSubId === (int)$sc['id']);
                        ?>
                        <a href="<?= $base ?>/?category=<?= $activeCat['id'] ?? '' ?>&sub=<?= $sc['id'] ?>" 
                           class="flex-shrink-0 px-3.5 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm transition-all flex items-center gap-1.5 <?= $isScActive ? 'bg-emerald-800 text-white font-bold shadow-sm ring-2 ring-emerald-700/40' : 'bg-emerald-100 hover:bg-emerald-200 text-emerald-900 font-semibold' ?>">
                            <span><?= htmlspecialchars($sc['name']) ?></span>
                            <?php if (!empty($sc['total_product_count'])): ?>
                                <span class="text-[10px] px-1.5 py-0.2 rounded-full <?= $isScActive ? 'bg-white/20 text-white' : 'bg-emerald-200 text-emerald-900' ?>">
                                    <?= $sc['total_product_count'] ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- 3. PRODUCT CARDS GRID (6 columns on Desktop, 2 columns on Mobile) -->
            <?php if (empty($products)): ?>
                <div class="bg-white rounded-3xl p-10 sm:p-14 text-center border border-gray-100 shadow-xs max-w-md mx-auto my-6">
                    <div class="w-16 h-16 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-3 text-3xl">
                        🛒
                    </div>
                    <h3 class="text-base font-bold text-gray-800">এই ক্যাটাগরিতে কোনো পণ্য পাওয়া যায়নি</h3>
                    <p class="text-xs text-gray-400 mt-1">অনুগ্রহ করে অন্য ক্যাটাগরি বা সবগুলো পণ্য দেখুন।</p>
                    <a href="<?= $base ?>/" class="inline-block mt-4 px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 transition-colors">
                        সব পণ্য দেখুন
                    </a>
                </div>
            <?php else: ?>
                <!-- RESPONSIVE GRID: 6 columns on wide desktop matching Desktop - 1, 2 columns on mobile matching iPhone 17 - 1 -->
                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-2 sm:gap-3 lg:gap-3.5">
                    <?php foreach ($products as $product): ?>
                        <?php 
                            $uInfo = getProductUnits($product); 
                            $hasCustom = !empty($uInfo['has_custom']);
                            $initialPrice = $hasCustom ? $uInfo['default_price'] : $product['sell_price'];
                            $initialTitle = $hasCustom ? $uInfo['default_title'] : '';
                            $initialQty = $hasCustom ? $uInfo['default_qty'] : 1;

                            $hasDiscount = \Models\Product::hasDiscount($product);
                            $discountPercent = $hasDiscount ? \Models\Product::getDiscountPercent($product) : 0;
                            $stockQtyNum = floatval($product['stock_qty']);
                            $stockClean = (floor($stockQtyNum) == $stockQtyNum) 
                                ? intval($stockQtyNum) 
                                : rtrim(rtrim(number_format($stockQtyNum, 3), '0'), '.');
                        ?>

                        <!-- Product Card -->
                        <div class="product-card bg-white rounded-2xl border border-gray-100 hover:border-emerald-400 hover:shadow-md transition-all duration-200 group flex flex-col h-full overflow-hidden relative"
                             data-product-id="<?= $product['id'] ?>"
                             data-base-unit="<?= htmlspecialchars($product['base_unit'] ?? 'pcs') ?>"
                             data-selected-variant-title="<?= htmlspecialchars($initialTitle) ?>"
                             data-selected-variant-price="<?= $initialPrice ?>"
                             data-selected-variant-qty="<?= $initialQty ?>">
                            
                            <!-- Badges (Discount & Stock) -->
                            <div class="absolute top-2 left-2 right-2 z-10 flex items-center justify-between pointer-events-none gap-1">
                                <?php if ($hasDiscount): ?>
                                    <span class="bg-emerald-700 text-white text-[9px] sm:text-[10px] font-black px-1.5 py-0.5 rounded-md shadow-2xs">
                                        <?= $discountPercent ?>% ছাড়
                                    </span>
                                <?php else: ?>
                                    <span></span>
                                <?php endif; ?>

                                <?php if ($stockQtyNum < 10 && $stockQtyNum > 0): ?>
                                    <span class="bg-amber-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-md shadow-2xs">
                                        বাকি: <?= $stockClean ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Centered Image Container -->
                            <a href="<?= $base ?>/product?id=<?= $product['id'] ?>" class="relative bg-white pt-6 pb-2 px-2 flex items-center justify-center min-h-[125px] sm:min-h-[150px] block cursor-pointer">
                                <?php 
                                $productImg = !empty($product['image_path']) ? htmlspecialchars($product['image_path']) : $base . '/images/default-product.svg';
                                ?>
                                <img src="<?= $productImg ?>" 
                                     alt="<?= htmlspecialchars($product['name']) ?>" 
                                     class="max-h-24 sm:max-h-32 w-auto max-w-[90%] object-contain transition-transform duration-300 group-hover:scale-105" 
                                     loading="lazy"
                                     onerror="this.src='<?= $base ?>/images/default-product.svg'">
                            </a>

                            <!-- Card Content -->
                            <div class="p-2.5 sm:p-3 pt-1 flex flex-col flex-grow">
                                <!-- Product Name -->
                                <h3 class="font-bold text-gray-900 text-xs sm:text-[13px] leading-snug line-clamp-2 min-h-[2rem] sm:min-h-[2.4rem] mb-1 group-hover:text-emerald-700 transition-colors" 
                                    title="<?= htmlspecialchars($product['name']) ?>">
                                    <a href="<?= $base ?>/product?id=<?= $product['id'] ?>" class="hover:text-emerald-700 transition-colors">
                                        <?= htmlspecialchars($product['name']) ?>
                                    </a>
                                </h3>

                                <!-- Unit / Weight -->
                                <div class="text-[10px] sm:text-[11px] text-gray-500 font-medium mb-1.5">
                                    <?= htmlspecialchars($hasCustom ? $initialTitle : $uInfo['primary']) ?>
                                </div>

                                <!-- Price Section -->
                                <div class="flex items-baseline gap-1.5 mb-2 flex-wrap">
                                    <div class="flex items-baseline gap-0.5">
                                        <span class="text-xs font-bold text-gray-700 leading-none">৳</span>
                                        <span class="card-price text-sm sm:text-base font-black text-gray-900 leading-none">
                                            <?= number_format($initialPrice) ?>
                                        </span>
                                    </div>

                                    <?php if ($hasDiscount): ?>
                                        <span class="card-regular-price text-[10px] sm:text-[11px] text-gray-400 line-through font-medium leading-none">
                                            ৳<?= number_format($product['regular_price']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Unit Variants Picker if Available -->
                                <?php if ($hasCustom): ?>
                                    <div class="pt-0.5 pb-1 flex flex-wrap items-center gap-1 unit-badges-container">
                                        <?php foreach ($uInfo['variants'] as $v): ?>
                                            <?php 
                                                $isActive = ($v['title'] === $initialTitle); 
                                                $activeClass = $isActive 
                                                    ? 'border-emerald-600 text-emerald-700 bg-emerald-50/70 font-bold active-variant shadow-2xs' 
                                                    : 'border-gray-200 text-gray-500 bg-white hover:border-gray-300 font-medium';
                                            ?>
                                            <button type="button" 
                                                    onclick="selectProductCardVariant(this, '<?= htmlspecialchars($v['title'], ENT_QUOTES) ?>', <?= floatval($v['price']) ?>, <?= floatval($v['qty'] ?? 1) ?>)"
                                                    class="variant-pill inline-flex items-center px-1.5 py-0.5 rounded text-[10px] border transition-all cursor-pointer <?= $activeClass ?>">
                                                <?= htmlspecialchars($v['title']) ?>
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Bottom Action: Add to Bag / Interactive Stepper -->
                                <div class="mt-auto pt-2 card-action-container" data-product-id="<?= $product['id'] ?>">
                                    <button type="button" 
                                            onclick="cardAddToCart(<?= $product['id'] ?>, this)" 
                                            class="w-full py-1.5 sm:py-2 px-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-700 text-emerald-800 hover:text-white border border-emerald-200 hover:border-emerald-700 font-bold text-xs flex items-center justify-center gap-1.5 transition-all duration-200 shadow-2xs cursor-pointer group/btn">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-emerald-700 group-hover/btn:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                        <span>ব্যাগে যোগ করুন</span>
                                    </button>
                                </div>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </section>

    </div>

</main>

<script>
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
        btn.className = 'variant-pill inline-flex items-center px-1.5 py-0.5 rounded text-[10px] border transition-all cursor-pointer border-gray-200 text-gray-500 bg-white hover:border-gray-300 font-medium';
    });
    pillBtn.className = 'variant-pill inline-flex items-center px-1.5 py-0.5 rounded text-[10px] border transition-all cursor-pointer border-emerald-600 text-emerald-700 bg-emerald-50/70 font-bold active-variant shadow-2xs';

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

// Convert numbers to Bengali digits
function convertToBanglaNumber(num) {
    const bnDigits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
    return String(num).replace(/[0-9]/g, d => bnDigits[d]);
}

// Render Add to Bag OR [-] Qty [+] bar for a card
function renderCardActionButton(card) {
    const container = card.querySelector('.card-action-container');
    if (!container) return;

    const productId = card.dataset.productId;
    const selectedVariant = card.dataset.selectedVariantTitle || '';
    const cartItem = findCartItemForCard(card);
    const quantity = cartItem ? parseInt(cartItem.quantity) : 0;
    const isBn = (window.SODAI_STATE?.locale === 'bn');
    const addToBagText = 'ব্যাগে যোগ করুন';
    const inBagSuffix = 'টি ব্যাগে';

    if (quantity > 0) {
        const displayQty = isBn ? convertToBanglaNumber(quantity) : quantity;
        container.innerHTML = `
            <div class="w-full py-1 px-1 rounded-xl bg-emerald-700 text-white font-bold text-xs flex items-center justify-between shadow-md select-none transition-all">
                <button type="button" 
                        onclick="cardChangeQty(${productId}, -1, this)" 
                        class="w-6 sm:w-7 h-6 sm:h-7 rounded-lg bg-emerald-800 hover:bg-emerald-900 active:scale-90 text-white flex items-center justify-center transition-all font-black text-sm cursor-pointer"
                        title="কমান">
                    −
                </button>
                <div class="flex flex-col items-center justify-center px-1 text-center leading-tight">
                    <span class="text-[11px] sm:text-xs font-black tracking-tight text-white">${displayQty} ${inBagSuffix}</span>
                    ${selectedVariant ? `<span class="text-[9px] text-emerald-200 font-medium truncate max-w-[80px]">${selectedVariant}</span>` : ''}
                </div>
                <button type="button" 
                        onclick="cardChangeQty(${productId}, 1, this)" 
                        class="w-6 sm:w-7 h-6 sm:h-7 rounded-lg bg-emerald-800 hover:bg-emerald-900 active:scale-90 text-white flex items-center justify-center transition-all font-black text-sm cursor-pointer"
                        title="বাড়ান">
                    +
                </button>
            </div>
        `;
    } else {
        container.innerHTML = `
            <button type="button" 
                    onclick="cardAddToCart(${productId}, this)" 
                    class="w-full py-1.5 sm:py-2 px-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-700 text-emerald-800 hover:text-white border border-emerald-200 hover:border-emerald-700 font-bold text-xs flex items-center justify-center gap-1.5 transition-all duration-200 shadow-2xs cursor-pointer group/btn">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-emerald-700 group-hover/btn:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
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

// Handle Add to Bag from Card
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

    fetch((window.APP_BASE || '') + '/cart/add', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            updateCartUI(data);
            showToast(data.message || 'কার্টে যোগ করা হয়েছে');
        }
    })
    .catch(() => showToast('ত্রুটি ঘটেছে', 'error'));
}

// Handle Quantity Change (+ or -) from Card
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

    fetch((window.APP_BASE || '') + '/cart/update', {
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

// Trigger initial card sync on load
document.addEventListener('DOMContentLoaded', () => {
    syncProductCardsWithCart();
});
</script>

<?php 
$content = ob_get_clean();
require 'layout.php';
?>