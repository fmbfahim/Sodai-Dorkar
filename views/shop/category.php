<?php
ob_start();
use Core\Lang;
Lang::init();
$__ = function($key, $r = []) { return Lang::get($key, $r); };
$locale = Lang::locale();
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';

// Unit helper if not already defined
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
            $primaryUnit = '1 kg';
        } elseif ($baseUnit === 'liter') {
            $primaryUnit = '1 liter';
        } elseif (stripos($name, 'oil') !== false || stripos($name, 'তেল') !== false) {
            $primaryUnit = '500 ml';
        } elseif (stripos($name, 'milk') !== false || stripos($name, 'দুধ') !== false) {
            $primaryUnit = '1 liter';
        } elseif (stripos($name, 'rice') !== false || stripos($name, 'চাল') !== false) {
            $primaryUnit = '1 kg';
        } else {
            $primaryUnit = '1 ' . $baseUnit;
        }
    }
    
    $variants = [];
    if (stripos($primaryUnit, '500 ml') !== false) {
        $variants = ['1 ltr', '5 ltr'];
    } elseif (stripos($primaryUnit, '1 liter') !== false || stripos($primaryUnit, '১ লিটার') !== false) {
        $variants = ['2 ltr', '5 ltr'];
    } elseif (stripos($primaryUnit, '1 kg') !== false || stripos($primaryUnit, '১ কেজি') !== false) {
        $variants = ['2 kg', '5 kg'];
    } elseif (stripos($primaryUnit, '5 kg') !== false || stripos($primaryUnit, '৫ কেজি') !== false) {
        $variants = ['10 kg', '25 kg'];
    } elseif (stripos($primaryUnit, '500 gm') !== false || stripos($primaryUnit, '৫০০ গ্রাম') !== false) {
        $variants = ['1 kg', '2 kg'];
    }
    
    return [
        'has_custom' => false,
        'primary' => $primaryUnit,
        'variants' => $variants
    ];
}
}

// Category visual helper
if (!function_exists('getCategoryVisual')) {
function getCategoryVisual($cat) {
    if (!empty($cat['image_path'])) {
        return ['type' => 'image', 'val' => $cat['image_path'], 'bg' => 'bg-white border border-gray-100'];
    }
    $n = mb_strtolower($cat['name'] ?? '');
    if (strpos($n, 'মাছ ও মাংস') !== false) return ['type' => 'image', 'val' => '/sodai-dorkar/public/uploads/categories/1788452780_Screenshot 2026-09-03 222555.png', 'bg' => 'bg-white border border-gray-100'];
    if (strpos($n, 'দুধ') !== false || strpos($n, 'দুগ্ধ') !== false || strpos($n, 'dairy') !== false) return ['type' => 'image', 'val' => '/sodai-dorkar/public/uploads/products/bottle-milk-1liter.jpg', 'bg' => 'bg-white border border-gray-100'];
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

// Active displayed category title
$displayCategoryTitle = $activeSubCategory ? $activeSubCategory['name'] : ($parentCategory ? $parentCategory['name'] : $currentCategory['name']);
$bannerBg = !empty($currentCategory['image_path']) ? $currentCategory['image_path'] : '/sodai-dorkar/public/images/fresh_veggies_banner.jpg';

// Calculate active filter count
$activeFilterCount = 0;
if (!empty($activeSubId)) $activeFilterCount++;
if ($minPrice !== null || $maxPrice !== null) $activeFilterCount++;
if (!empty($selectedBrand)) $activeFilterCount++;
if (!empty($isDeals)) $activeFilterCount++;
if (!empty($search)) $activeFilterCount++;
?>

<div class="bg-gray-50/60 min-h-screen pb-16">

    <!-- ==========================================
         SECTION 1: 1200x203 HERO BANNER
         ========================================== -->
    <section class="container mx-auto px-4 pt-4 sm:pt-6">
        <div class="relative w-full min-h-[190px] sm:min-h-[210px] py-6 sm:py-8 rounded-2xl sm:rounded-3xl overflow-hidden shadow-sm border border-emerald-900/20 bg-gradient-to-r from-[#072414] via-[#0d3b20] to-[#14532d] flex items-center">
            
            <!-- Banner Background Image -->
            <img src="<?= htmlspecialchars($bannerBg) ?>" 
                 alt="<?= htmlspecialchars($displayCategoryTitle) ?>" 
                 class="absolute inset-0 w-full h-full object-cover opacity-40 mix-blend-overlay filter saturate-150 transform hover:scale-105 transition-transform duration-700"
                 onerror="this.src='/sodai-dorkar/public/images/fresh_veggies_banner.jpg'">
            
            <!-- Gradient Light & Shadow Overlays -->
            <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/55 to-black/20"></div>
            <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Banner Text Content -->
            <div class="relative z-10 px-6 sm:px-10 lg:px-12 w-full flex flex-col justify-center">
                
                <!-- Breadcrumbs -->
                <nav class="flex items-center gap-1.5 text-[11px] sm:text-xs font-semibold text-emerald-200/90 mb-2 flex-wrap">
                    <a href="/sodai-dorkar/public/" class="hover:text-white transition-colors flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span><?= $locale === 'bn' ? 'হোম' : 'Home' ?></span>
                    </a>
                    <span class="text-emerald-400/60">/</span>
                    <?php if ($parentCategory && ($activeSubCategory || $parentCategory['id'] != $currentCategory['id'])): ?>
                        <a href="/sodai-dorkar/public/category?id=<?= $parentCategory['id'] ?>" class="hover:text-white transition-colors">
                            <?= htmlspecialchars($parentCategory['name']) ?>
                        </a>
                        <span class="text-emerald-400/60">/</span>
                    <?php endif; ?>
                    <span class="text-white font-bold truncate max-w-[200px] sm:max-w-xs">
                        <?= htmlspecialchars($displayCategoryTitle) ?>
                    </span>
                </nav>

                <!-- Category Heading & Badge -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h1 class="text-2xl sm:text-4xl font-black text-white tracking-tight leading-tight flex items-center gap-3">
                            <span><?= htmlspecialchars($displayCategoryTitle) ?></span>
                        </h1>
                        <p class="text-emerald-100/85 text-xs sm:text-sm mt-1 max-w-xl font-normal">
                            <?= !empty($currentCategory['description']) ? htmlspecialchars($currentCategory['description']) : ($locale === 'bn' ? 'তাজা ও খাঁটি পণ্যের সেরা কালেকশন, সরাসরি আপনার দোরগোড়ায়।' : 'Fresh & quality products delivered directly to your doorstep.') ?>
                        </p>
                    </div>

                    <!-- Total Products Counter Pill -->
                    <div class="flex-shrink-0 self-start sm:self-center">
                        <span class="inline-flex items-center gap-1.5 bg-emerald-500/20 backdrop-blur-md text-emerald-200 border border-emerald-400/40 text-xs sm:text-sm font-black px-4 py-1.5 rounded-full shadow-sm">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span><?= count($products) ?> <?= $locale === 'bn' ? 'টি পণ্য পাওয়া গেছে' : 'products found' ?></span>
                        </span>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- ==========================================
         SECTION 2: PARENT CATEGORY'S SUBCATEGORIES
         ========================================== -->
    <?php if (!empty($subCategories)): ?>
    <section class="container mx-auto px-4 pt-6 pb-2">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                <h3 class="text-xs sm:text-sm font-black text-gray-800 uppercase tracking-wider">
                    <?= htmlspecialchars($parentCategory['name'] ?? $currentCategory['name']) ?> <?= $locale === 'bn' ? 'এর সাব-ক্যাটাগরিসমূহ' : 'Sub-categories' ?>
                </h3>
            </div>
            
            <!-- Slider Controls -->
            <div class="flex items-center gap-1.5">
                <button type="button" onclick="scrollCatSubSlider(-1)" class="w-8 h-8 rounded-lg bg-white border border-gray-200 hover:border-emerald-500 hover:bg-emerald-50 text-gray-600 flex items-center justify-center transition-all shadow-2xs cursor-pointer active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" /></svg>
                </button>
                <button type="button" onclick="scrollCatSubSlider(1)" class="w-8 h-8 rounded-lg bg-white border border-gray-200 hover:border-emerald-500 hover:bg-emerald-50 text-gray-600 flex items-center justify-center transition-all shadow-2xs cursor-pointer active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                </button>
            </div>
        </div>

        <!-- Horizontal Subcategory Slider -->
        <div id="subcat-slider-container" class="flex flex-nowrap gap-3.5 sm:gap-4 overflow-x-auto scroll-smooth no-scrollbar py-2 px-1">
            
            <!-- "All in Category" Tab Card -->
            <?php 
                $isAllActive = empty($activeSubId) || ($parentCategory && $activeSubId == $parentCategory['id']);
                $parentTotal = $parentCategory['total_product_count'] ?? 0;
            ?>
            <a href="/sodai-dorkar/public/category?id=<?= $parentCategory['id'] ?? $currentCategory['id'] ?>"
               class="flex-shrink-0 w-36 sm:w-40 md:w-44 bg-white rounded-2xl border p-3 sm:p-4 flex flex-col items-center justify-between text-center transition-all duration-200 group hover:-translate-y-1 hover:shadow-lg <?= $isAllActive ? 'border-emerald-600 ring-2 ring-emerald-500/20 bg-emerald-50/50 shadow-md font-bold' : 'border-gray-200/80 hover:border-emerald-300' ?>">
                <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl bg-emerald-100/80 text-emerald-800 flex items-center justify-center mb-3 text-4xl sm:text-5xl shadow-2xs group-hover:scale-105 transition-transform duration-300">
                    🛒
                </div>
                <div class="w-full">
                    <span class="text-xs sm:text-sm font-extrabold text-gray-800 group-hover:text-emerald-700 block truncate">
                        <?= $locale === 'bn' ? 'সব পণ্য' : 'All Items' ?>
                    </span>
                    <span class="text-[11px] text-gray-400 font-semibold block mt-1">
                        <?= $parentTotal ?> <?= $locale === 'bn' ? 'টি পণ্য' : 'items' ?>
                    </span>
                </div>
            </a>

            <!-- Subcategory Item Cards -->
            <?php foreach ($subCategories as $sub): ?>
                <?php 
                    $vis = getCategoryVisual($sub);
                    $isActiveSub = ($activeSubId == $sub['id']);
                ?>
                <a href="/sodai-dorkar/public/category?id=<?= $parentCategory['id'] ?? $currentCategory['id'] ?>&sub=<?= $sub['id'] ?>"
                   class="flex-shrink-0 w-36 sm:w-40 md:w-44 bg-white rounded-2xl border p-3 sm:p-4 flex flex-col items-center justify-between text-center transition-all duration-200 group hover:-translate-y-1 hover:shadow-lg <?= $isActiveSub ? 'border-emerald-600 ring-2 ring-emerald-500/20 bg-emerald-50/50 shadow-md font-bold' : 'border-gray-200/80 hover:border-emerald-300' ?>">
                    
                    <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl flex items-center justify-center mb-3 overflow-hidden shadow-2xs group-hover:scale-105 transition-all duration-300 <?= $vis['type'] === 'image' ? 'bg-gray-50 border border-gray-100 p-2' : $vis['bg'] ?>">
                        <?php if ($vis['type'] === 'image'): ?>
                            <img src="<?= htmlspecialchars($vis['val']) ?>" alt="<?= htmlspecialchars($sub['name']) ?>" class="w-full h-full object-contain transition-transform duration-300 group-hover:scale-110 drop-shadow-xs" onerror="this.parentElement.innerHTML='🛒'">
                        <?php else: ?>
                            <span class="text-4xl sm:text-5xl select-none leading-none filter drop-shadow-sm transition-transform duration-300 group-hover:scale-110"><?= $vis['val'] ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="w-full">
                        <span class="text-xs sm:text-sm font-extrabold text-gray-800 group-hover:text-emerald-700 block truncate">
                            <?= htmlspecialchars($sub['name']) ?>
                        </span>
                        <span class="text-[11px] text-gray-400 font-semibold block mt-1">
                            <?= $sub['total_product_count'] ?? 0 ?> <?= $locale === 'bn' ? 'টি পণ্য' : 'items' ?>
                        </span>
                    </div>
                </a>
            <?php endforeach; ?>

        </div>
    </section>
    <?php endif; ?>

    <!-- ==========================================
         SECTION 3: SORT BY BAR
         ========================================== -->
    <section class="container mx-auto px-4 pt-4 pb-2">
        <div class="bg-white rounded-2xl border border-gray-200/90 p-3 sm:p-4 shadow-2xs flex flex-wrap items-center justify-between gap-3">
            
            <!-- Left: Product Count & Mobile Filter Button -->
            <div class="flex items-center gap-3">
                <!-- Mobile Filter Drawer Toggle Button -->
                <button type="button" 
                        onclick="toggleMobileFilterDrawer(true)"
                        class="lg:hidden inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-black border border-emerald-200/80 transition-all cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <span><?= $locale === 'bn' ? 'ফিল্টার' : 'Filters' ?></span>
                    <?php if ($activeFilterCount > 0): ?>
                        <span class="w-4 h-4 rounded-full bg-emerald-600 text-white text-[10px] flex items-center justify-center font-bold">
                            <?= $activeFilterCount ?>
                        </span>
                    <?php endif; ?>
                </button>

                <div class="text-xs sm:text-sm font-black text-gray-800 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span><?= count($products) ?> <?= $locale === 'bn' ? 'টি পণ্য পাওয়া গেছে' : 'products found' ?></span>
                    <?php if (!empty($activeSubCategory)): ?>
                        <span class="text-xs font-normal text-gray-500 hidden sm:inline">(<?= htmlspecialchars($activeSubCategory['name']) ?>)</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right: Sort By Dropdown -->
            <div class="flex items-center gap-2 ml-auto">
                <label for="sort-dropdown" class="text-xs sm:text-sm font-bold text-gray-500 whitespace-nowrap flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                    </svg>
                    <span><?= $locale === 'bn' ? 'সাজান (Sort By):' : 'Sort By:' ?></span>
                </label>
                <div class="relative">
                    <select id="sort-dropdown" 
                            onchange="onSortChange(this.value)"
                            class="bg-gray-50 hover:bg-gray-100 text-gray-800 text-xs sm:text-sm font-bold py-1.5 pl-3 pr-8 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 cursor-pointer appearance-none">
                        <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>><?= $locale === 'bn' ? 'সম্প্রতি যোগ করা (Newest)' : 'Newest' ?></option>
                        <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>><?= $locale === 'bn' ? 'দাম: কম থেকে বেশি' : 'Price: Low to High' ?></option>
                        <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>><?= $locale === 'bn' ? 'দাম: বেশি থেকে কম' : 'Price: High to Low' ?></option>
                        <option value="discount" <?= $sort === 'discount' ? 'selected' : '' ?>><?= $locale === 'bn' ? 'সর্বোচ্চ ছাড় / অফার' : 'Biggest Discount' ?></option>
                        <option value="name_asc" <?= $sort === 'name_asc' ? 'selected' : '' ?>><?= $locale === 'bn' ? 'নাম (A - Z)' : 'Name (A to Z)' ?></option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- ==========================================
         SECTION 4: 2-COLUMN SECTION (LEFT: FILTERS, RIGHT: PRODUCTS)
         ========================================== -->
    <section class="container mx-auto px-4 pt-4">
        <div class="flex flex-col lg:flex-row gap-6 lg:gap-8 items-start">
            
            <!-- ---------------------------------------
                 LEFT: FILTERS SIDEBAR (Sticky on Desktop)
                 --------------------------------------- -->
            <!-- Mobile Backdrop Overlay -->
            <div id="filter-drawer-backdrop" onclick="toggleMobileFilterDrawer(false)" class="fixed inset-0 bg-black/50 z-40 lg:hidden opacity-0 pointer-events-none transition-opacity duration-300"></div>

            <aside id="filter-sidebar" 
                   class="fixed inset-y-0 left-0 z-50 w-72 sm:w-80 bg-white p-5 overflow-y-auto transform -translate-x-full lg:translate-x-0 transition-transform duration-300 shadow-2xl lg:shadow-none lg:static lg:z-auto lg:w-64 xl:w-72 lg:flex-shrink-0 lg:p-0 lg:bg-transparent lg:overflow-visible space-y-5">
                
                <div class="bg-white rounded-2xl border border-gray-200/80 p-4 sm:p-5 shadow-xs space-y-6">
                    
                    <!-- Sidebar Header (Title + Reset) -->
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                            <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider"><?= $locale === 'bn' ? 'ফিল্টারসমূহ' : 'Filters' ?></h3>
                        </div>

                        <!-- Reset Link -->
                        <?php if ($activeFilterCount > 0): ?>
                            <a href="/sodai-dorkar/public/category?id=<?= $parentCategory['id'] ?? $currentCategory['id'] ?>" 
                               class="text-[11px] font-bold text-red-600 hover:text-red-700 hover:underline">
                                <?= $locale === 'bn' ? 'সব মুছুন' : 'Clear All' ?>
                            </a>
                        <?php endif; ?>

                        <!-- Mobile Close Button -->
                        <button type="button" onclick="toggleMobileFilterDrawer(false)" class="lg:hidden text-gray-400 hover:text-gray-600 p-1">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Filter 1: Subcategories List -->
                    <?php if (!empty($subCategories)): ?>
                    <div class="space-y-2.5">
                        <h4 class="text-xs font-black text-gray-900 uppercase tracking-wider flex items-center justify-between">
                            <span><?= $locale === 'bn' ? 'সাব-ক্যাটাগরি' : 'Sub-categories' ?></span>
                        </h4>
                        
                        <div class="space-y-1.5 max-h-52 overflow-y-auto custom-scrollbar pr-1">
                            <!-- All subcategories option -->
                            <label class="flex items-center justify-between p-2 rounded-xl text-xs font-medium cursor-pointer transition-colors <?= empty($activeSubId) ? 'bg-emerald-50 text-emerald-800 font-bold' : 'hover:bg-gray-50 text-gray-700' ?>">
                                <div class="flex items-center gap-2">
                                    <input type="radio" name="subcat_filter" value="" 
                                           onchange="onSubcategoryFilterChange('')"
                                           <?= empty($activeSubId) ? 'checked' : '' ?>
                                           class="text-emerald-600 focus:ring-emerald-500 h-3.5 w-3.5">
                                    <span><?= $locale === 'bn' ? 'সকল সাব-ক্যাটাগরি' : 'All Sub-categories' ?></span>
                                </div>
                                <span class="text-[10px] text-gray-400"><?= $parentCategory['total_product_count'] ?? 0 ?></span>
                            </label>

                            <?php foreach ($subCategories as $sub): ?>
                                <?php $isSubChecked = ($activeSubId == $sub['id']); ?>
                                <label class="flex items-center justify-between p-2 rounded-xl text-xs font-medium cursor-pointer transition-colors <?= $isSubChecked ? 'bg-emerald-50 text-emerald-800 font-bold' : 'hover:bg-gray-50 text-gray-700' ?>">
                                    <div class="flex items-center gap-2">
                                        <input type="radio" name="subcat_filter" value="<?= $sub['id'] ?>" 
                                               onchange="onSubcategoryFilterChange('<?= $sub['id'] ?>')"
                                               <?= $isSubChecked ? 'checked' : '' ?>
                                               class="text-emerald-600 focus:ring-emerald-500 h-3.5 w-3.5">
                                        <span class="truncate max-w-[130px]"><?= htmlspecialchars($sub['name']) ?></span>
                                    </div>
                                    <span class="text-[10px] text-gray-400"><?= $sub['total_product_count'] ?? 0 ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Filter 2: Price Range -->
                    <div class="space-y-3 pt-2 border-t border-gray-100">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-black text-gray-900 uppercase tracking-wider"><?= $locale === 'bn' ? 'মূল্যসীমা' : 'Price Range' ?></h4>
                            <span class="text-[11px] font-bold text-emerald-700">৳<?= (int)($minPrice ?? $minPriceBound) ?> - ৳<?= (int)($maxPrice ?? $maxPriceBound) ?></span>
                        </div>

                        <!-- Min and Max Inputs -->
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-[10px] text-gray-400 font-bold"><?= $locale === 'bn' ? 'সর্বনিম্ন (৳)' : 'Min (৳)' ?></label>
                                <input type="number" id="filter-min-price" 
                                       value="<?= $minPrice !== null ? (int)$minPrice : '' ?>" 
                                       placeholder="<?= (int)$minPriceBound ?>"
                                       class="w-full px-2.5 py-1.5 text-xs font-semibold rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="text-[10px] text-gray-400 font-bold"><?= $locale === 'bn' ? 'সর্বোচ্চ (৳)' : 'Max (৳)' ?></label>
                                <input type="number" id="filter-max-price" 
                                       value="<?= $maxPrice !== null ? (int)$maxPrice : '' ?>" 
                                       placeholder="<?= (int)$maxPriceBound ?>"
                                       class="w-full px-2.5 py-1.5 text-xs font-semibold rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 focus:outline-none">
                            </div>
                        </div>

                        <!-- Apply Price Filter Button -->
                        <button type="button" 
                                onclick="applyPriceFilter()"
                                class="w-full py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 hover:border-emerald-600 font-bold text-xs transition-all cursor-pointer">
                            <?= $locale === 'bn' ? 'ফিল্টার প্রয়োগ করুন' : 'Apply Price' ?>
                        </button>
                    </div>

                    <!-- Filter 3: Brands Filter -->
                    <?php if (!empty($brands)): ?>
                    <div class="space-y-2.5 pt-2 border-t border-gray-100">
                        <h4 class="text-xs font-black text-gray-900 uppercase tracking-wider"><?= $locale === 'bn' ? 'ব্র্যান্ড' : 'Brands' ?></h4>
                        <div class="space-y-1.5 max-h-40 overflow-y-auto custom-scrollbar pr-1">
                            <?php foreach ($brands as $b): ?>
                                <?php $isBActive = ($selectedBrand == $b['id']); ?>
                                <label class="flex items-center justify-between p-1.5 rounded-lg text-xs cursor-pointer hover:bg-gray-50 <?= $isBActive ? 'font-bold text-emerald-700' : 'text-gray-700' ?>">
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" 
                                               value="<?= $b['id'] ?>" 
                                               onchange="onBrandFilterChange(this)"
                                               <?= $isBActive ? 'checked' : '' ?>
                                               class="rounded text-emerald-600 focus:ring-emerald-500 h-3.5 w-3.5">
                                        <span class="truncate max-w-[130px]"><?= htmlspecialchars($b['name']) ?></span>
                                    </div>
                                    <span class="text-[10px] text-gray-400">(<?= $b['prod_count'] ?>)</span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Filter 4: Availability & Discounts Toggle -->
                    <div class="space-y-2.5 pt-2 border-t border-gray-100">
                        <h4 class="text-xs font-black text-gray-900 uppercase tracking-wider"><?= $locale === 'bn' ? 'অন্যান্য' : 'Availability' ?></h4>
                        
                        <!-- In Stock Only -->
                        <label class="flex items-center justify-between p-1.5 rounded-lg text-xs text-gray-700 hover:bg-gray-50 cursor-pointer">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" id="filter-in-stock" 
                                       onchange="onToggleFilter('in_stock', this.checked ? '1' : '0')"
                                       <?= $inStockOnly ? 'checked' : '' ?>
                                       class="rounded text-emerald-600 focus:ring-emerald-500 h-3.5 w-3.5">
                                <span><?= $locale === 'bn' ? 'স্টকে আছে এমন পণ্য' : 'In Stock' ?></span>
                            </div>
                            <span class="text-emerald-600 text-xs">✓</span>
                        </label>

                        <!-- Discount Deals Only -->
                        <label class="flex items-center justify-between p-1.5 rounded-lg text-xs text-gray-700 hover:bg-gray-50 cursor-pointer">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" id="filter-deals" 
                                       onchange="onToggleFilter('deals', this.checked ? '1' : '')"
                                       <?= !empty($isDeals) ? 'checked' : '' ?>
                                       class="rounded text-emerald-600 focus:ring-emerald-500 h-3.5 w-3.5">
                                <span class="flex items-center gap-1">
                                    <span class="text-amber-500">⚡</span>
                                    <span><?= $locale === 'bn' ? 'শুধুমাত্র বিশেষ অফার / ছাড়' : 'Deals & Offers' ?></span>
                                </span>
                            </div>
                        </label>
                    </div>

                </div>

            </aside>

            <!-- ---------------------------------------
                 RIGHT: PRODUCTS GRID SECTION
                 --------------------------------------- -->
            <main class="flex-1 min-w-0 w-full">
                
                <!-- Active Filters Chips Bar -->
                <?php if ($activeFilterCount > 0): ?>
                    <div class="flex flex-wrap items-center gap-2 mb-4 bg-emerald-50/60 p-3 rounded-2xl border border-emerald-100/80">
                        <span class="text-xs font-bold text-emerald-900 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            <span><?= $locale === 'bn' ? 'সক্রিয় ফিল্টার:' : 'Active Filters:' ?></span>
                        </span>

                        <?php if (!empty($activeSubCategory)): ?>
                            <span class="inline-flex items-center gap-1.5 bg-white text-emerald-800 text-xs font-bold px-3 py-1 rounded-full border border-emerald-200 shadow-2xs">
                                <span><?= htmlspecialchars($activeSubCategory['name']) ?></span>
                                <button type="button" onclick="onSubcategoryFilterChange('')" class="text-gray-400 hover:text-red-500 ml-0.5">×</button>
                            </span>
                        <?php endif; ?>

                        <?php if ($minPrice !== null || $maxPrice !== null): ?>
                            <span class="inline-flex items-center gap-1.5 bg-white text-emerald-800 text-xs font-bold px-3 py-1 rounded-full border border-emerald-200 shadow-2xs">
                                <span>৳<?= (int)($minPrice ?? 0) ?> - ৳<?= (int)($maxPrice ?? $maxPriceBound) ?></span>
                                <button type="button" onclick="clearPriceFilter()" class="text-gray-400 hover:text-red-500 ml-0.5">×</button>
                            </span>
                        <?php endif; ?>

                        <?php if (!empty($selectedBrand)): ?>
                            <?php 
                                $bName = '';
                                foreach ($brands as $b) { if ($b['id'] == $selectedBrand) { $bName = $b['name']; break; } }
                            ?>
                            <?php if ($bName): ?>
                                <span class="inline-flex items-center gap-1.5 bg-white text-emerald-800 text-xs font-bold px-3 py-1 rounded-full border border-emerald-200 shadow-2xs">
                                    <span><?= htmlspecialchars($bName) ?></span>
                                    <button type="button" onclick="onToggleFilter('brand', '')" class="text-gray-400 hover:text-red-500 ml-0.5">×</button>
                                </span>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if (!empty($isDeals)): ?>
                            <span class="inline-flex items-center gap-1.5 bg-white text-emerald-800 text-xs font-bold px-3 py-1 rounded-full border border-emerald-200 shadow-2xs">
                                <span>⚡ <?= $locale === 'bn' ? 'অফার' : 'Deals' ?></span>
                                <button type="button" onclick="onToggleFilter('deals', '')" class="text-gray-400 hover:text-red-500 ml-0.5">×</button>
                            </span>
                        <?php endif; ?>

                        <a href="/sodai-dorkar/public/category?id=<?= $parentCategory['id'] ?? $currentCategory['id'] ?>" 
                           class="text-xs font-bold text-red-600 hover:text-red-700 hover:underline ml-auto">
                            <?= $locale === 'bn' ? 'ফিল্টার রিসেট' : 'Reset' ?>
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Product Grid OR Empty State -->
                <?php if (empty($products)): ?>
                    <div class="bg-white rounded-3xl p-12 border border-gray-100 shadow-sm text-center max-w-md mx-auto my-8">
                        <div class="w-16 h-16 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-4 text-3xl">
                            🛒
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-800">
                            <?= $locale === 'bn' ? 'এই ফিল্টারে কোনো পণ্য পাওয়া যায়নি' : 'No products found' ?>
                        </h3>
                        <p class="text-gray-500 text-xs sm:text-sm mt-1">
                            <?= $locale === 'bn' ? 'অনুগ্রহ করে ফিল্টার পরিবর্তন করুন অথবা রিসেট বাটন চাপুন।' : 'Try changing your filters or click reset to view all items.' ?>
                        </p>
                        <a href="/sodai-dorkar/public/category?id=<?= $parentCategory['id'] ?? $currentCategory['id'] ?>" 
                           class="inline-flex items-center gap-1.5 mt-5 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black shadow-sm transition-all">
                            <span><?= $locale === 'bn' ? 'সকল ফিল্টার মুছুন' : 'Clear All Filters' ?></span>
                            <span>→</span>
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Product Cards Grid: 4 cols on xl, 3 on lg/md, 2 on mobile -->
                    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-3.5 sm:gap-4.5">
                        <?php foreach ($products as $product): ?>
                            <?php 
                                $uInfo = getProductUnits($product); 
                                $hasCustom = !empty($uInfo['has_custom']);
                                $initialPrice = $hasCustom ? $uInfo['default_price'] : $product['sell_price'];
                                $initialTitle = $hasCustom ? $uInfo['default_title'] : '';
                                $initialQty = $hasCustom ? $uInfo['default_qty'] : 1;
                            ?>
                            <?php 
                                $hasDiscount = \Models\Product::hasDiscount($product);
                                $discountPercent = $hasDiscount ? \Models\Product::getDiscountPercent($product) : 0;
                                $discountAmount = $hasDiscount ? \Models\Product::getDiscountAmount($product) : 0;
                                $stockQtyNum = floatval($product['stock_qty']);
                                $stockClean = (floor($stockQtyNum) == $stockQtyNum) 
                                    ? intval($stockQtyNum) 
                                    : rtrim(rtrim(number_format($stockQtyNum, 3), '0'), '.');
                            ?>

                            <div class="product-card bg-white rounded-2xl border border-gray-100 hover:border-emerald-400 hover:shadow-lg transition-all duration-300 group flex flex-col h-full overflow-hidden relative"
                                 data-product-id="<?= $product['id'] ?>"
                                 data-base-unit="<?= htmlspecialchars($product['base_unit'] ?? 'pcs') ?>"
                                 data-selected-variant-title="<?= htmlspecialchars($initialTitle) ?>"
                                 data-selected-variant-price="<?= $initialPrice ?>"
                                 data-selected-variant-qty="<?= $initialQty ?>">
                                
                                <!-- Top Category & Discount Badges -->
                                <div class="absolute top-2.5 left-2.5 right-2.5 z-10 flex items-center justify-between pointer-events-none gap-1">
                                    <div class="flex items-center gap-1 flex-wrap min-w-0">
                                        <?php if ($hasDiscount): ?>
                                            <span class="bg-[#14532d] text-white text-[10px] sm:text-[11px] font-black px-2 py-0.5 rounded-md shadow-xs whitespace-nowrap">
                                                <?= $__('products_off', ['percent' => $discountPercent]) ?>
                                            </span>
                                        <?php elseif (!empty($product['category_name'])): ?>
                                            <span class="bg-gray-100/90 text-gray-600 text-[10px] font-bold px-2 py-0.5 rounded-md border border-gray-200/70 truncate max-w-[90px]">
                                                <?= htmlspecialchars($product['category_name']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($stockQtyNum < 10 && $stockQtyNum > 0): ?>
                                        <span class="bg-amber-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-md shadow-2xs whitespace-nowrap">
                                            <?= $__('products_only_left', ['count' => $stockClean]) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Centered Product Image (Link to Details) -->
                                <a href="<?= $base ?>/product?id=<?= $product['id'] ?>" class="relative bg-white pt-8 pb-3 px-3 flex items-center justify-center min-h-[155px] sm:min-h-[175px] block cursor-pointer">
                                    <?php 
                                    $productImg = !empty($product['image_path']) ? htmlspecialchars($product['image_path']) : $base . '/images/default-product.svg';
                                    ?>
                                    <img src="<?= $productImg ?>" 
                                         alt="<?= htmlspecialchars($product['name']) ?>" 
                                         class="max-h-32 sm:max-h-36 w-auto max-w-[85%] object-contain transition-transform duration-300 group-hover:scale-105" 
                                         loading="lazy"
                                         onerror="this.src='<?= $base ?>/images/default-product.svg'">
                                </a>

                                <!-- Details Section -->
                                <div class="p-3 sm:p-4 pt-1 flex flex-col flex-grow">
                                    
                                    <!-- Product Title -->
                                    <h3 class="font-bold text-gray-900 text-xs sm:text-sm leading-snug line-clamp-2 min-h-[2.4rem] mb-1.5 group-hover:text-emerald-700 transition-colors" 
                                        title="<?= htmlspecialchars($product['name']) ?>">
                                        <a href="<?= $base ?>/product?id=<?= $product['id'] ?>" class="hover:text-emerald-700 transition-colors">
                                            <?= htmlspecialchars($product['name']) ?>
                                        </a>
                                    </h3>

                                    <!-- Unit Weight Display -->
                                    <div class="text-[11px] text-gray-500 font-medium mb-2">
                                        <?= htmlspecialchars($hasCustom ? $initialTitle : $uInfo['primary']) ?>
                                    </div>

                                    <!-- Price Display -->
                                    <div class="flex items-baseline gap-1.5 mb-2.5 flex-wrap">
                                        <div class="flex items-baseline gap-0.5">
                                            <span class="text-xs font-bold text-gray-700 leading-none">৳</span>
                                            <span class="card-price text-base sm:text-lg font-black text-gray-900 leading-none">
                                                <?= number_format($initialPrice) ?>
                                            </span>
                                        </div>

                                        <?php if ($hasDiscount): ?>
                                            <span class="card-regular-price text-[11px] text-gray-400 line-through font-medium leading-none">
                                                ৳<?= number_format($product['regular_price']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Unit Variant Badges (Interactive Pills) -->
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
                                                        class="variant-pill inline-flex items-center px-2 py-0.5 rounded-md text-[11px] border transition-all cursor-pointer <?= $activeClass ?>">
                                                    <?= htmlspecialchars($v['title']) ?>
                                                </button>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Bottom Dynamic Add to Bag / Interactive Quantity Controller -->
                                    <div class="mt-auto pt-3 card-action-container" data-product-id="<?= $product['id'] ?>">
                                        <button type="button" 
                                                onclick="cardAddToCart(<?= $product['id'] ?>, this)" 
                                                class="w-full py-2 px-3 rounded-xl bg-emerald-50 hover:bg-[#14532d] text-emerald-800 hover:text-white border border-emerald-200 hover:border-[#14532d] font-bold text-xs sm:text-sm flex items-center justify-center gap-1.5 transition-all duration-200 shadow-2xs hover:shadow-sm cursor-pointer group/btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600 group-hover/btn:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                            </svg>
                                            <span><?= $__('add_to_bag') ?></span>
                                        </button>
                                    </div>

                                </div>

                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </main>

        </div>
    </section>

</div>

<script>
// Category Page Filter State & Interactivity
function updateFilterUrl(newParams) {
    const url = new URL(window.location.href);
    for (const [key, val] of Object.entries(newParams)) {
        if (val === null || val === undefined || val === '') {
            url.searchParams.delete(key);
        } else {
            url.searchParams.set(key, val);
        }
    }
    window.location.href = url.toString();
}

function onSortChange(sortVal) {
    updateFilterUrl({ sort: sortVal });
}

function onSubcategoryFilterChange(subId) {
    updateFilterUrl({ sub: subId });
}

function onBrandFilterChange(checkbox) {
    updateFilterUrl({ brand: checkbox.checked ? checkbox.value : '' });
}

function onToggleFilter(paramKey, val) {
    updateFilterUrl({ [paramKey]: val });
}

function applyPriceFilter() {
    const minVal = document.getElementById('filter-min-price').value.trim();
    const maxVal = document.getElementById('filter-max-price').value.trim();
    updateFilterUrl({
        min_price: minVal,
        max_price: maxVal
    });
}

function clearPriceFilter() {
    updateFilterUrl({
        min_price: '',
        max_price: ''
    });
}

function toggleMobileFilterDrawer(open) {
    const sidebar = document.getElementById('filter-sidebar');
    const backdrop = document.getElementById('filter-drawer-backdrop');
    if (!sidebar || !backdrop) return;

    if (open) {
        sidebar.classList.remove('-translate-x-full');
        backdrop.classList.remove('opacity-0', 'pointer-events-none');
        document.body.style.overflow = 'hidden';
    } else {
        sidebar.classList.add('-translate-x-full');
        backdrop.classList.add('opacity-0', 'pointer-events-none');
        document.body.style.overflow = '';
    }
}

function scrollCatSubSlider(direction) {
    const slider = document.getElementById('subcat-slider-container');
    if (!slider) return;
    const scrollAmount = slider.clientWidth * 0.75;
    slider.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
