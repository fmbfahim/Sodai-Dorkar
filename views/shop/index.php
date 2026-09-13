<?php 
ob_start(); 
use Core\Lang;
Lang::init();
$__ = function($key, $r = []) { return Lang::get($key, $r); };
$locale = Lang::locale();

// Helper function to extract and format unit badges matching the reference design
if (!function_exists('getProductUnits')) {
function getProductUnits($product) {
    // 1. If product has custom configured selling variants in unit_variants_json
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

    // 2. Fallback heuristic from name/base_unit
    $name = $product['name'] ?? '';
    $baseUnit = $product['base_unit'] ?? 'pcs';
    $primaryUnit = '';
    
    // Check regex for standard weights / volume
    if (preg_match('/(\d+(\.\d+)?\s*(কেজি|গ্রাম|লিটার|মি\.লি\.|kg|gm|g|ml|ltr|liter|litre|pcs|pc|টি|প্যাকেট))/iu', $name, $matches)) {
        $primaryUnit = trim($matches[0]);
    }
    
    // Contextual defaults if not found in name
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
    
    // Variant badges like in reference mockup
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

// Category visual icon/image helper
if (!function_exists('getCategoryVisual')) {
function getCategoryVisual($cat) {
    if (!empty($cat['image_path'])) {
        return ['type' => 'image', 'val' => $cat['image_path'], 'bg' => 'bg-white border border-gray-100'];
    }
    $n = mb_strtolower($cat['name'] ?? '');
    
    // Check known uploads / category images
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
?>

<!-- ==========================================
     1. HERO SPLIT SECTION (FreshMart Style)
     ========================================== -->
<section class="container mx-auto px-4 pt-4 sm:pt-6 pb-4">
    <div class="bg-gradient-to-br from-[#f2f8f4] via-white to-[#edf7f1] rounded-3xl p-6 sm:p-10 lg:p-12 border border-emerald-100/90 shadow-sm relative overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-center">
            
            <!-- Left Content -->
            <div class="lg:col-span-7 space-y-5 sm:space-y-6 z-10">
                <span class="inline-flex items-center gap-1.5 bg-emerald-100/80 text-emerald-800 text-[11px] sm:text-xs font-black px-3.5 py-1.5 rounded-full uppercase tracking-wider shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                    <?= $__('hero_badge') ?>
                </span>

                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black text-gray-900 tracking-tight leading-[1.12]">
                    <?= $__('hero_heading_1') ?><br>
                    <span class="text-emerald-600"><?= $__('hero_heading_2') ?></span>
                </h1>

                <p class="text-gray-600 text-xs sm:text-base leading-relaxed max-w-lg font-normal">
                    <?= $__('hero_description') ?>
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-wrap items-center gap-3 sm:gap-4 pt-1">
                    <a href="#products" class="inline-flex items-center gap-2 bg-[#14532d] hover:bg-[#0f4022] text-white font-black px-6 sm:px-8 py-3.5 rounded-xl shadow-lg shadow-emerald-950/20 hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5 text-xs sm:text-sm tracking-wide">
                        <span><?= $__('hero_btn_shop') ?></span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                    <a href="/sodai-dorkar/public/?deals=1" class="inline-flex items-center gap-2 bg-white hover:bg-emerald-50 text-gray-800 hover:text-emerald-800 font-bold px-6 py-3.5 rounded-xl border border-gray-200 hover:border-emerald-300 shadow-2xs transition-all text-xs sm:text-sm">
                        <span><?= $__('hero_btn_deals') ?></span>
                    </a>
                </div>

                <!-- 4 Value Proposition Pills -->
                <div class="pt-6 grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 border-t border-emerald-100/90">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center flex-shrink-0 text-sm shadow-2xs">
                            🥬
                        </div>
                        <div>
                            <div class="text-xs font-black text-gray-900 leading-tight"><?= $__('trust_farm_fresh') ?></div>
                            <div class="text-[10px] text-gray-500 leading-tight mt-0.5"><?= $__('trust_farm_fresh_sub') ?></div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center flex-shrink-0 text-sm shadow-2xs">
                            🚚
                        </div>
                        <div>
                            <div class="text-xs font-black text-gray-900 leading-tight"><?= $__('trust_free_delivery') ?></div>
                            <div class="text-[10px] text-gray-500 leading-tight mt-0.5"><?= $__('trust_free_delivery_sub') ?></div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center flex-shrink-0 text-sm shadow-2xs">
                            🔒
                        </div>
                        <div>
                            <div class="text-xs font-black text-gray-900 leading-tight"><?= $__('trust_secure_pay') ?></div>
                            <div class="text-[10px] text-gray-500 leading-tight mt-0.5"><?= $__('trust_secure_pay_sub') ?></div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center flex-shrink-0 text-sm shadow-2xs">
                            🔄
                        </div>
                        <div>
                            <div class="text-xs font-black text-gray-900 leading-tight"><?= $__('trust_easy_return') ?></div>
                            <div class="text-[10px] text-gray-500 leading-tight mt-0.5"><?= $__('trust_easy_return_sub') ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Visual: Grocery Basket + 30% OFF badge -->
            <div class="lg:col-span-5 relative flex items-center justify-center pt-4 lg:pt-0">
                <!-- Glowing backdrop -->
                <div class="absolute w-64 sm:w-80 h-64 sm:h-80 rounded-full bg-emerald-300/30 blur-3xl pointer-events-none"></div>

                <!-- Floating Discount Badge -->
                <div class="absolute -top-2 right-4 sm:right-8 z-20 bg-emerald-800 text-white rounded-full w-20 h-20 sm:w-24 sm:h-24 flex flex-col items-center justify-center shadow-2xl border-2 border-dashed border-emerald-300 transform rotate-12 hover:rotate-0 transition-transform">
                    <span class="text-[8px] sm:text-[9px] font-bold tracking-widest uppercase text-emerald-200">UP TO</span>
                    <span class="text-lg sm:text-xl font-black leading-none text-white">30%</span>
                    <span class="text-[8px] sm:text-[9px] font-extrabold uppercase tracking-wider text-emerald-200">OFF</span>
                </div>

                <!-- Hero Basket Image -->
                <img src="/sodai-dorkar/public/images/hero_basket.jpg" 
                     alt="FreshMart Grocery Basket" 
                     class="w-full max-w-xs sm:max-w-sm lg:max-w-md h-auto object-contain drop-shadow-2xl transform hover:scale-105 transition-transform duration-500">
            </div>

        </div>
    </div>
</section>

<?php
// Capture DUAL PROMOTIONAL BANNERS HTML to allow dynamic placement
ob_start();
?>
<!-- ==========================================
     DUAL PROMOTIONAL BANNERS
     ========================================== -->
<section class="container mx-auto px-4 py-4 sm:py-6" id="promo-banners-section">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        
        <!-- Left Banner: Weekend Super Saver (7 cols on lg) -->
        <div class="lg:col-span-7 relative rounded-3xl overflow-hidden bg-cover bg-center min-h-[220px] sm:min-h-[250px] flex items-center p-6 sm:p-8 shadow-md group"
             style="background-image: url('/sodai-dorkar/public/images/fresh_veggies_banner.jpg');">
            <div class="absolute inset-0 bg-gradient-to-r from-emerald-950/95 via-emerald-950/85 to-transparent"></div>
            
            <div class="relative z-10 space-y-3 max-w-sm">
                <span class="inline-block bg-emerald-800/90 text-emerald-300 text-[10px] sm:text-xs font-black px-2.5 py-1 rounded-md uppercase tracking-wider border border-emerald-700/60">
                    <?= $__('promo_limited_offer') ?>
                </span>
                
                <h3 class="text-2xl sm:text-3xl font-black text-white leading-tight">
                    Weekend <span class="text-amber-400 block sm:inline">Super Saver</span>
                </h3>
                
                <p class="text-xs sm:text-sm text-emerald-100/80 leading-snug">
                    <?= $__('promo_weekend_desc') ?>
                </p>
                
                <a href="/sodai-dorkar/public/?deals=1" class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-400 text-white font-black text-xs sm:text-sm px-5 py-2.5 rounded-xl shadow-md transition-all">
                    <span><?= $__('promo_shop_now') ?></span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>

            <!-- Floating Pill -->
            <div class="absolute top-4 right-4 z-10 bg-amber-400 text-gray-900 rounded-full w-14 h-14 sm:w-16 sm:h-16 flex flex-col items-center justify-center shadow-lg font-black leading-none transform rotate-12">
                <span class="text-[8px] sm:text-[9px] uppercase">UP TO</span>
                <span class="text-sm sm:text-base">30%</span>
                <span class="text-[8px] sm:text-[9px] uppercase">OFF</span>
            </div>
        </div>

        <!-- Right Banner: 30 Minutes Delivery (5 cols on lg) -->
        <div class="lg:col-span-5 relative rounded-3xl overflow-hidden bg-[#edf7f1] border border-emerald-200/90 p-6 sm:p-7 flex flex-col justify-between shadow-sm group min-h-[220px]">
            <div class="space-y-2 z-10 max-w-[210px] sm:max-w-xs">
                <h3 class="text-xl sm:text-2xl font-black text-gray-900 leading-tight">
                    Get Delivery in<br>
                    <span class="text-emerald-700 font-extrabold text-2xl sm:text-3xl">30 Minutes!</span>
                </h3>
                <p class="text-xs text-gray-600 leading-snug">
                    <?= $__('promo_speedy_sub') ?>
                </p>
                <div class="pt-2">
                    <a href="#products" class="inline-flex items-center gap-1.5 bg-[#14532d] hover:bg-[#0f4022] text-white text-xs font-black px-4 py-2.5 rounded-xl shadow-sm transition-colors">
                        <?= $__('promo_order_now') ?>
                    </a>
                </div>
            </div>

            <!-- Delivery Scooter Visual -->
            <div class="absolute bottom-2 right-2 sm:right-3 w-36 sm:w-44 h-auto pointer-events-none">
                <img src="/sodai-dorkar/public/images/delivery_rider.jpg" alt="Fast Delivery" class="w-full h-auto object-contain rounded-2xl transform group-hover:scale-105 transition-transform duration-300">
            </div>
        </div>

    </div>
</section>
<?php 
$promoBannersHtml = ob_get_clean(); 
$isCatSelected = !empty($currentCategory) || !empty($parentCategory);
?>

<?php if ($isCatSelected): ?>
    <?= $promoBannersHtml ?>
<?php endif; ?>

<!-- ==========================================
     2. SHOP BY CATEGORY SECTION (Hierarchical Drilldown)
     ========================================== -->
<section class="container mx-auto px-4 py-8 sm:py-10" id="categories">
    
    <!-- State A: Default Main Categories Header -->
    <div id="cat-header-main" class="<?= !empty($parentCategory) ? 'hidden' : '' ?> flex flex-row items-end justify-between mb-6 pb-2 border-b border-gray-100/80 gap-3">
        <div>
            <div class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-800 text-[11px] font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-1.5 border border-emerald-100">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                <?= $locale === 'bn' ? 'প্রধান ক্যাটাগরিসমূহ' : 'Main Departments' ?>
            </div>
            <h2 class="text-xl sm:text-2xl lg:text-3xl font-black text-gray-900 tracking-tight uppercase">
                <?= $__('sec_shop_by_category') ?>
            </h2>
            <p class="text-gray-500 text-xs sm:text-sm mt-0.5">
                <?= $locale === 'bn' ? 'যেকোনো ক্যাটাগরিতে ক্লিক করে তার সাব-ক্যাটাগরিসমূহ দেখুন' : 'Click any category to browse its sub-categories' ?>
            </p>
        </div>

        <!-- Slide Navigation Arrows (< >) -->
        <div class="flex items-center gap-2 flex-shrink-0">
            <button type="button" 
                    onclick="slideCategories(-1)" 
                    id="cat-slider-prev"
                    aria-label="<?= $locale === 'bn' ? 'আগের ক্যাটাগরি' : 'Previous Categories' ?>" 
                    class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-white border border-gray-200 hover:border-emerald-500 hover:bg-emerald-600 hover:text-white text-gray-700 shadow-2xs flex items-center justify-center transition-all duration-200 hover:scale-105 active:scale-95 cursor-pointer group">
                <svg class="w-5 h-5 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <button type="button" 
                    onclick="slideCategories(1)" 
                    id="cat-slider-next"
                    aria-label="<?= $locale === 'bn' ? 'পরের ক্যাটাগরি' : 'Next Categories' ?>" 
                    class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-white border border-gray-200 hover:border-emerald-500 hover:bg-emerald-600 hover:text-white text-gray-700 shadow-2xs flex items-center justify-center transition-all duration-200 hover:scale-105 active:scale-95 cursor-pointer group">
                <svg class="w-5 h-5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
    </div>

    <!-- State B: Sub-categories Drilldown Header -->
    <div id="cat-header-drilldown" class="<?= empty($parentCategory) ? 'hidden' : '' ?> mb-6 pb-3 border-b border-gray-100">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <!-- Back Button to Return to Main Categories -->
                <a href="<?= htmlspecialchars($parentBackUrl ?? '/sodai-dorkar/public/#categories') ?>" 
                   onclick="resetToMainCategories(event)"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs sm:text-sm font-black transition-all shadow-xs border border-emerald-200/70 group cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span><?= $__('cat_back_to_main') ?></span>
                </a>

                <!-- Active Breadcrumb Title -->
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-xs text-gray-400 font-medium hidden md:inline"><?= $__('sec_shop_by_category') ?></span>
                    <span class="text-xs text-gray-400 hidden md:inline">/</span>
                    <h2 class="text-lg sm:text-2xl font-black text-gray-900 tracking-tight flex items-center gap-2">
                        <span id="drilldown-parent-name"><?= htmlspecialchars($parentCategory['name'] ?? '') ?></span>
                        <span id="drilldown-badge" class="text-[11px] sm:text-xs font-black px-2.5 py-0.5 rounded-full bg-emerald-600 text-white shadow-xs">
                            <?= !empty($subCategories) ? count($subCategories) : 0 ?> <?= $locale === 'bn' ? 'টি সাব-ক্যাটাগরি' : 'Sub-categories' ?>
                        </span>
                    </h2>
                </div>
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-end">
                <!-- View All in Parent Button -->
                <a id="drilldown-view-all" 
                   href="/sodai-dorkar/public/?category=<?= $parentCategory['id'] ?? '' ?>#products" 
                   class="<?= empty($parentCategory) ? 'hidden' : 'inline-flex' ?> items-center gap-1.5 text-xs sm:text-sm font-black text-emerald-700 hover:text-emerald-900 bg-white hover:bg-emerald-50 border border-emerald-200 px-4 py-2 rounded-xl shadow-2xs transition-all">
                    <span id="drilldown-view-all-text">
                        <?= !empty($parentCategory) ? $__('cat_view_all_in_category', ['name' => htmlspecialchars($parentCategory['name'])]) : '' ?>
                    </span>
                    <span class="text-emerald-700">↓</span>
                </a>

                <!-- Slide Navigation Arrows (< >) for Drilldown -->
                <div class="flex items-center gap-1.5">
                    <button type="button" 
                            onclick="slideCategories(-1)" 
                            aria-label="<?= $locale === 'bn' ? 'আগের ক্যাটাগরি' : 'Previous Categories' ?>" 
                            class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-white border border-gray-200 hover:border-emerald-500 hover:bg-emerald-600 hover:text-white text-gray-700 shadow-2xs flex items-center justify-center transition-all duration-200 hover:scale-105 active:scale-95 cursor-pointer group">
                        <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button type="button" 
                            onclick="slideCategories(1)" 
                            aria-label="<?= $locale === 'bn' ? 'পরের ক্যাটাগরি' : 'Next Categories' ?>" 
                            class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-white border border-gray-200 hover:border-emerald-500 hover:bg-emerald-600 hover:text-white text-gray-700 shadow-2xs flex items-center justify-center transition-all duration-200 hover:scale-105 active:scale-95 cursor-pointer group">
                        <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Categories Slider (Single line horizontal carousel: Exactly 7 cards visible per row on desktop) -->
    <div id="cat-grid-main" class="<?= !empty($parentCategory) ? 'hidden' : '' ?> flex flex-nowrap gap-3 sm:gap-3.5 overflow-x-auto scroll-smooth no-scrollbar py-2 px-1">
        <?php foreach ($mainCategories as $cat): ?>
            <?php 
                $vis = getCategoryVisual($cat);
                $isCatActive = (isset($currentCategory) && $currentCategory == $cat['id']);
                $hasSub = !empty($cat['sub_count']) && $cat['sub_count'] > 0;
            ?>
            <a href="/sodai-dorkar/public/category?id=<?= $cat['id'] ?>" 
               class="category-main-card flex-shrink-0 w-[calc((100%-16px)/2.3)] sm:w-[calc((100%-36px)/4)] md:w-[calc((100%-56px)/5)] lg:w-[calc((100%-84px)/7)] bg-white rounded-2xl border p-3 sm:p-3.5 flex flex-col items-center justify-between text-center transition-all duration-200 group hover:-translate-y-1 <?= $isCatActive ? 'border-emerald-600 ring-2 ring-emerald-500/20 bg-emerald-50/50 shadow-md' : 'border-gray-100 hover:border-emerald-300 hover:shadow-md' ?>"
               data-cat-id="<?= $cat['id'] ?>"
               data-has-sub="<?= $hasSub ? '1' : '0' ?>"
               title="<?= htmlspecialchars($cat['name']) ?>">
                
                <div class="w-20 h-20 sm:w-22 sm:h-22 lg:w-22 lg:h-22 xl:w-24 xl:h-24 rounded-2xl flex items-center justify-center mb-2.5 overflow-hidden transition-transform duration-300 group-hover:scale-105 shadow-2xs <?= $vis['type'] === 'image' ? 'bg-white border border-gray-100' : $vis['bg'] ?>">
                    <?php if ($vis['type'] === 'image'): ?>
                        <img src="<?= htmlspecialchars($vis['val']) ?>" alt="<?= htmlspecialchars($cat['name']) ?>" class="w-full h-full object-contain p-1.5 transition-transform duration-300 group-hover:scale-110" onerror="this.parentElement.innerHTML='🛒'">
                    <?php else: ?>
                        <span class="text-4xl sm:text-5xl xl:text-6xl select-none leading-none filter drop-shadow-sm transition-transform duration-300 group-hover:scale-110"><?= $vis['val'] ?></span>
                    <?php endif; ?>
                </div>

                <div class="w-full flex flex-col items-center">
                    <span class="text-xs sm:text-[13px] font-extrabold text-gray-800 group-hover:text-emerald-700 transition-colors line-clamp-1 leading-snug w-full px-1">
                        <?= htmlspecialchars($cat['name']) ?>
                    </span>
                    
                    <?php if ($hasSub): ?>
                        <span class="inline-flex items-center gap-1 text-[10px] sm:text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full mt-1.5 border border-emerald-200/80 group-hover:bg-emerald-600 group-hover:text-white transition-all shadow-2xs">
                            <span><?= $cat['sub_count'] ?> <?= $locale === 'bn' ? 'সাব-ক্যাটাগরি' : 'subcats' ?></span>
                            <span class="text-[9px]">▾</span>
                        </span>
                    <?php else: ?>
                        <span class="text-[10px] sm:text-[11px] text-gray-400 font-semibold mt-1">
                            <?= $cat['total_product_count'] ?? 0 ?> <?= $locale === 'bn' ? 'টি পণ্য' : 'items' ?>
                        </span>
                    <?php endif; ?>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Sub-categories Slider (Single line horizontal carousel: Exactly 7 cards visible per row on desktop) -->
    <div id="cat-grid-sub" class="<?= empty($parentCategory) ? 'hidden' : '' ?> flex flex-nowrap gap-3 sm:gap-3.5 overflow-x-auto scroll-smooth no-scrollbar py-2 px-1">
        <?php if (!empty($subCategories)): ?>
            <?php foreach ($subCategories as $subCat): ?>
                <?php 
                    $visSub = getCategoryVisual($subCat);
                    $isSubActive = (isset($currentCategory) && $currentCategory == $subCat['id']);
                    $hasChildSub = !empty($subCat['sub_count']) && $subCat['sub_count'] > 0;
                ?>
                <a href="/sodai-dorkar/public/category?id=<?= $subCat['id'] ?>" 
                   class="subcategory-card flex-shrink-0 w-[calc((100%-16px)/2.3)] sm:w-[calc((100%-36px)/4)] md:w-[calc((100%-56px)/5)] lg:w-[calc((100%-84px)/7)] bg-white rounded-2xl border p-3 sm:p-3.5 flex flex-col items-center justify-between text-center transition-all duration-200 group hover:-translate-y-1 hover:shadow-md <?= $isSubActive ? 'border-emerald-600 ring-2 ring-emerald-500/20 bg-emerald-50/60 shadow-md font-bold' : 'border-gray-100 hover:border-emerald-300' ?>"
                   data-subcat-id="<?= $subCat['id'] ?>"
                   data-has-child="<?= $hasChildSub ? '1' : '0' ?>"
                   title="<?= htmlspecialchars($subCat['name']) ?>">
                    
                    <div class="w-20 h-20 sm:w-22 sm:h-22 lg:w-22 lg:h-22 xl:w-24 xl:h-24 rounded-2xl flex items-center justify-center mb-2.5 overflow-hidden transition-transform duration-300 group-hover:scale-105 shadow-2xs <?= $visSub['type'] === 'image' ? 'bg-white border border-gray-100' : $visSub['bg'] ?>">
                        <?php if ($visSub['type'] === 'image'): ?>
                            <img src="<?= htmlspecialchars($visSub['val']) ?>" alt="<?= htmlspecialchars($subCat['name']) ?>" class="w-full h-full object-contain p-1.5 transition-transform duration-300 group-hover:scale-110" onerror="this.parentElement.innerHTML='🛒'">
                        <?php else: ?>
                            <span class="text-4xl sm:text-5xl xl:text-6xl select-none leading-none filter drop-shadow-sm transition-transform duration-300 group-hover:scale-110"><?= $visSub['val'] ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="w-full flex flex-col items-center">
                        <span class="text-xs sm:text-[13px] font-extrabold text-gray-800 group-hover:text-emerald-700 transition-colors line-clamp-1 leading-snug w-full px-1">
                            <?= htmlspecialchars($subCat['name']) ?>
                        </span>
                        
                        <?php if ($hasChildSub): ?>
                            <span class="inline-flex items-center gap-0.5 text-[10px] sm:text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full mt-1.5 border border-emerald-200/80 group-hover:bg-emerald-600 group-hover:text-white transition-all shadow-2xs">
                                <span><?= $subCat['sub_count'] ?> <?= $locale === 'bn' ? 'সাব-ক্যাটাগরি' : 'subcats' ?></span>
                                <span>▾</span>
                            </span>
                        <?php else: ?>
                            <span class="text-[10px] sm:text-[11px] text-gray-400 font-semibold mt-1">
                                <?= $subCat['total_product_count'] ?? 0 ?> <?= $locale === 'bn' ? 'টি পণ্য' : 'items' ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php if (!$isCatSelected): ?>
    <?= $promoBannersHtml ?>
<?php endif; ?>

<!-- ==========================================
     4. DEAL OF THE DAY / PRODUCTS SECTION
     ========================================== -->
<div id="products" class="container mx-auto px-4 py-10 md:py-12">
    
    <!-- Header with Title & All Deals Link -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-6 gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-amber-500 text-lg">⚡</span>
                <h2 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">
                    <?= !empty($isDeals) ? $__('sec_deal_of_the_day') : $__('products_title') ?>
                </h2>
            </div>
            <p class="text-gray-500 text-xs sm:text-sm"><?= $__('products_subtitle') ?></p>
        </div>
        
        <div class="flex items-center gap-4 w-full md:w-auto justify-between md:justify-end">
            <?php if (empty($isDeals)): ?>
                <a href="/sodai-dorkar/public/?deals=1" class="text-xs sm:text-sm font-bold text-emerald-700 hover:text-emerald-800 flex items-center gap-1 group">
                    <span><?= $__('sec_view_all_deals') ?></span>
                    <span class="group-hover:translate-x-1 transition-transform">→</span>
                </a>
            <?php else: ?>
                <a href="/sodai-dorkar/public/#products" class="text-xs sm:text-sm font-bold text-emerald-700 hover:text-emerald-800 flex items-center gap-1">
                    <span><?= $__('products_all_categories') ?></span>
                    <span>→</span>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Category Filter Tabs -->
    <div class="flex flex-wrap gap-2 mb-8 overflow-x-auto pb-2 custom-scrollbar items-center">
        <?php if (!empty($parentCategory)): ?>
            <!-- Back to All Products Tab -->
            <a href="/sodai-dorkar/public/#products" 
               class="category-tab px-3.5 py-2 rounded-full text-xs font-bold border border-gray-200 hover:bg-emerald-50 text-gray-600 hover:text-emerald-800 whitespace-nowrap transition-all flex items-center gap-1">
                <span>←</span>
                <span><?= $__('products_all_categories') ?></span>
            </a>
            
            <!-- Parent Category All in Parent Tab -->
            <a href="/sodai-dorkar/public/category?id=<?= $parentCategory['id'] ?>" 
               class="category-tab px-4 py-2 rounded-full text-xs sm:text-sm font-bold border border-gray-200 hover:bg-[#14532d] hover:text-white hover:border-[#14532d] whitespace-nowrap transition-all <?= ($currentCategory == $parentCategory['id']) ? 'active' : 'bg-white text-gray-700' ?>">
                <?= $__('cat_view_all_in_category', ['name' => htmlspecialchars($parentCategory['name'])]) ?>
                <span class="text-[11px] opacity-75 ml-1">(<?= $parentCategory['total_product_count'] ?? 0 ?>)</span>
            </a>

            <!-- Subcategory Tabs -->
            <?php foreach ($subCategories as $subCat): ?>
                <a href="/sodai-dorkar/public/category?id=<?= $subCat['id'] ?>" 
                   class="category-tab px-4 py-2 rounded-full text-xs sm:text-sm font-bold border border-gray-200 hover:bg-[#14532d] hover:text-white hover:border-[#14532d] whitespace-nowrap transition-all <?= ($currentCategory == $subCat['id']) ? 'active' : 'bg-white text-gray-600' ?>">
                    <?= htmlspecialchars($subCat['name']) ?>
                    <?php if (!empty($subCat['total_product_count'])): ?>
                        <span class="text-[11px] opacity-75 ml-1">(<?= $subCat['total_product_count'] ?>)</span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>

        <?php else: ?>
            <a href="/sodai-dorkar/public/<?= $search ? '?search=' . urlencode($search) : '' ?>#products" 
               class="category-tab px-4 py-2 rounded-full text-xs sm:text-sm font-bold border border-gray-200 hover:bg-[#14532d] hover:text-white hover:border-[#14532d] whitespace-nowrap transition-all <?= (!$currentCategory && empty($isDeals)) ? 'active' : 'bg-white text-gray-600' ?>">
                <?= $__('products_all_categories') ?>
            </a>
            <a href="/sodai-dorkar/public/?deals=1#products" 
               class="category-tab px-4 py-2 rounded-full text-xs sm:text-sm font-bold border border-gray-200 hover:bg-[#14532d] hover:text-white hover:border-[#14532d] whitespace-nowrap transition-all <?= !empty($isDeals) ? 'active' : 'bg-white text-gray-600' ?>">
                ⚡ <?= $__('nav_deals') ?>
            </a>
            <?php foreach ($categories as $cat): ?>
                <a href="/sodai-dorkar/public/category?id=<?= $cat['id'] ?><?= $search ? '&search=' . urlencode($search) : '' ?>" 
                   class="category-tab px-4 py-2 rounded-full text-xs sm:text-sm font-bold border border-gray-200 hover:bg-[#14532d] hover:text-white hover:border-[#14532d] whitespace-nowrap transition-all <?= ($currentCategory == $cat['id'] && empty($isDeals)) ? 'active' : 'bg-white text-gray-600' ?>">
                    <?= htmlspecialchars($cat['name']) ?>
                    <span class="text-[11px] opacity-75 ml-1">(<?= $cat['product_count'] ?>)</span>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if (empty($products)): ?>
        <div class="bg-white p-12 rounded-3xl shadow-sm border border-gray-100 text-center max-w-lg mx-auto">
            <div class="w-16 h-16 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-4 text-2xl">
                🛒
            </div>
            <h3 class="text-lg font-bold text-gray-800"><?= $__('products_empty_title') ?></h3>
            <p class="text-gray-500 text-xs mt-1"><?= $__('products_empty_subtitle') ?></p>
            <?php if ($search || $currentCategory || !empty($isDeals)): ?>
                <a href="/sodai-dorkar/public/#products" class="inline-block mt-4 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-4 py-2 rounded-xl transition-colors">
                    <?= $__('products_all_categories') ?> →
                </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <!-- Products Grid (FreshMart Style: 5 cols on xl, 4 on lg, 3 on md, 2 on mobile) -->
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3.5 sm:gap-4.5">
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
                    
                    <!-- Top Category, Discount & Stock Badges -->
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

                    <!-- Centered Image Container -->
                    <div class="relative bg-white pt-8 pb-3 px-3 flex items-center justify-center min-h-[155px] sm:min-h-[180px]">
                        <?php 
                        $productImg = !empty($product['image_path']) ? htmlspecialchars($product['image_path']) : '/sodai-dorkar/public/images/default-product.svg';
                        ?>
                        <img src="<?= $productImg ?>" 
                             alt="<?= htmlspecialchars($product['name']) ?>" 
                             class="max-h-32 sm:max-h-36 w-auto max-w-[85%] object-contain transition-transform duration-300 group-hover:scale-105" 
                             loading="lazy"
                             onerror="this.src='/sodai-dorkar/public/images/default-product.svg'">
                    </div>

                    <!-- Details Section -->
                    <div class="p-3 sm:p-4 pt-1 flex flex-col flex-grow">
                        <!-- Product Title -->
                        <h3 class="font-bold text-gray-900 text-xs sm:text-sm leading-snug line-clamp-2 min-h-[2.4rem] mb-1.5 group-hover:text-emerald-700 transition-colors" 
                            title="<?= htmlspecialchars($product['name']) ?>">
                            <?= htmlspecialchars($product['name']) ?>
                        </h3>

                        <!-- Unit Weight Display -->
                        <div class="text-[11px] text-gray-500 font-medium mb-2">
                            <?= htmlspecialchars($hasCustom ? $initialTitle : $uInfo['primary']) ?>
                        </div>

                        <!-- Price (৳ symbol + bold price + strikethrough regular price) -->
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

                        <!-- Unit Badges (Interactive Weight / Pack options) -->
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

                        <!-- Bottom Add to Bag / Interactive Quantity Controller -->
                        <div class="mt-auto pt-3 card-action-container" data-product-id="<?= $product['id'] ?>">
                            <!-- Button rendered dynamically by syncProductCardsWithCart() -->
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
</div>

<!-- ==========================================
     5. WHY CHOOSE FRESHMART / SODAI DORKAR?
     ========================================== -->
<section class="container mx-auto px-4 py-10" id="why-choose">
    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-gray-100 shadow-xs">
        <div class="text-center mb-8 sm:mb-10">
            <h3 class="text-lg sm:text-2xl font-black text-gray-900 tracking-tight uppercase">
                <?= $__('why_choose_title') ?>
            </h3>
            <div class="w-12 h-1 bg-emerald-500 rounded-full mx-auto mt-2"></div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 text-center">
            <div class="space-y-2.5 p-3 rounded-2xl hover:bg-emerald-50/50 transition-colors">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto text-xl shadow-2xs">
                    🏆
                </div>
                <h4 class="font-black text-xs sm:text-sm text-gray-900"><?= $__('why_quality_title') ?></h4>
                <p class="text-[11px] sm:text-xs text-gray-500 leading-relaxed"><?= $__('why_quality_desc') ?></p>
            </div>

            <div class="space-y-2.5 p-3 rounded-2xl hover:bg-emerald-50/50 transition-colors">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto text-xl shadow-2xs">
                    🏷️
                </div>
                <h4 class="font-black text-xs sm:text-sm text-gray-900"><?= $__('why_price_title') ?></h4>
                <p class="text-[11px] sm:text-xs text-gray-500 leading-relaxed"><?= $__('why_price_desc') ?></p>
            </div>

            <div class="space-y-2.5 p-3 rounded-2xl hover:bg-emerald-50/50 transition-colors">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto text-xl shadow-2xs">
                    ⚡
                </div>
                <h4 class="font-black text-xs sm:text-sm text-gray-900"><?= $__('why_delivery_title') ?></h4>
                <p class="text-[11px] sm:text-xs text-gray-500 leading-relaxed"><?= $__('why_delivery_desc') ?></p>
            </div>

            <div class="space-y-2.5 p-3 rounded-2xl hover:bg-emerald-50/50 transition-colors">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto text-xl shadow-2xs">
                    🛡️
                </div>
                <h4 class="font-black text-xs sm:text-sm text-gray-900"><?= $__('why_secure_title') ?></h4>
                <p class="text-[11px] sm:text-xs text-gray-500 leading-relaxed"><?= $__('why_secure_desc') ?></p>
            </div>

            <div class="space-y-2.5 p-3 rounded-2xl hover:bg-emerald-50/50 transition-colors col-span-2 md:col-span-1">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto text-xl shadow-2xs">
                    🔄
                </div>
                <h4 class="font-black text-xs sm:text-sm text-gray-900"><?= $__('why_returns_title') ?></h4>
                <p class="text-[11px] sm:text-xs text-gray-500 leading-relaxed"><?= $__('why_returns_desc') ?></p>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================
     6. SOCIAL PROOF, REVIEWS & NEWSLETTER
     ========================================== -->
<section class="container mx-auto px-4 py-6 sm:py-8">
    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-gray-100 shadow-xs">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-8 items-center">
            
            <!-- Col 1: Customer Review Quote -->
            <div class="lg:col-span-4 space-y-3 lg:border-r lg:border-gray-100 lg:pr-8">
                <div class="text-[10px] sm:text-[11px] font-black uppercase text-gray-400 tracking-wider">
                    <?= $__('testimonials_title') ?>
                </div>
                <div class="flex text-amber-400 text-sm">★★★★★</div>
                <blockquote class="text-xs sm:text-sm text-gray-700 italic leading-relaxed">
                    "<?= $__('testimonials_quote') ?>"
                </blockquote>
                <div class="text-xs font-bold text-gray-900">
                    — <?= $__('testimonials_author') ?>
                </div>
            </div>

            <!-- Col 2: Happy Customers Counter -->
            <div class="lg:col-span-4 flex flex-col items-center text-center space-y-3 lg:border-r lg:border-gray-100 lg:pr-8">
                <div class="text-[10px] sm:text-[11px] font-black uppercase text-gray-400 tracking-wider">
                    <?= $__('happy_customers_title') ?>
                </div>
                <!-- Avatar Stack -->
                <div class="flex items-center -space-x-2">
                    <img class="inline-block h-10 w-10 rounded-full ring-2 ring-white object-cover shadow-xs" src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80" alt="Customer 1">
                    <img class="inline-block h-10 w-10 rounded-full ring-2 ring-white object-cover shadow-xs" src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&auto=format&fit=crop&q=80" alt="Customer 2">
                    <img class="inline-block h-10 w-10 rounded-full ring-2 ring-white object-cover shadow-xs" src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&auto=format&fit=crop&q=80" alt="Customer 3">
                    <img class="inline-block h-10 w-10 rounded-full ring-2 ring-white object-cover shadow-xs" src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&auto=format&fit=crop&q=80" alt="Customer 4">
                </div>
                <div>
                    <div class="text-2xl sm:text-3xl font-black text-gray-900 leading-none"><?= $__('happy_customers_count') ?></div>
                    <div class="text-xs text-gray-500 font-medium mt-1"><?= $__('happy_customers_label') ?></div>
                </div>
                <div class="text-[11px] font-bold text-amber-600 bg-amber-50 px-3 py-1 rounded-full border border-amber-200">
                    ★ <?= $__('happy_customers_rating') ?>
                </div>
            </div>

            <!-- Col 3: Newsletter Signup -->
            <div class="lg:col-span-4 space-y-3">
                <div class="text-[10px] sm:text-[11px] font-black uppercase text-gray-400 tracking-wider">
                    <?= $__('newsletter_title') ?>
                </div>
                <h4 class="text-xs sm:text-sm font-black text-gray-900 leading-snug">
                    <?= $__('newsletter_subtitle') ?>
                </h4>
                <form onsubmit="event.preventDefault(); showToast('ধন্যবাদ! আপনি সফলভাবে যুক্ত হয়েছেন।'); this.reset();" class="space-y-2">
                    <div class="flex rounded-xl overflow-hidden border border-gray-300 focus-within:ring-2 focus-within:ring-emerald-500/20 shadow-2xs">
                        <input type="text" required placeholder="<?= $__('newsletter_placeholder') ?>" class="flex-1 px-3 py-2 text-xs text-gray-800 focus:outline-none">
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black px-4 py-2 transition-colors uppercase tracking-wider">
                            <?= $__('newsletter_subscribe_btn') ?>
                        </button>
                    </div>
                    <p class="text-[10px] text-gray-400 leading-tight"><?= $__('newsletter_privacy') ?></p>
                </form>
            </div>

        </div>
    </div>
</section>

<script>
// Switch variant on product card
function selectProductCardVariant(pillBtn, title, price, qty) {
    const card = pillBtn.closest('.product-card');
    if (!card) return;

    // 1. Update data attributes
    card.dataset.selectedVariantTitle = title;
    card.dataset.selectedVariantPrice = price;
    card.dataset.selectedVariantQty = qty;

    // 2. Update price display
    const priceEl = card.querySelector('.card-price');
    if (priceEl) {
        priceEl.textContent = Number(price).toLocaleString('en-US');
    }

    // 3. Highlight selected pill
    card.querySelectorAll('.variant-pill').forEach(btn => {
        btn.className = 'variant-pill inline-flex items-center px-2 py-0.5 rounded-md text-[11px] border transition-all cursor-pointer border-gray-200 text-gray-500 bg-white hover:border-gray-300 font-medium';
    });
    pillBtn.className = 'variant-pill inline-flex items-center px-2 py-0.5 rounded-md text-[11px] border transition-all cursor-pointer border-emerald-600 text-emerald-700 bg-emerald-50/70 font-bold active-variant shadow-2xs';

    // 4. Update action button state for this specific variant
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
                if (item.variant_title === selectedVariant) {
                    return item;
                }
            } else {
                if (!item.variant_title) {
                    return item;
                }
            }
        }
    }
    return null;
}

// Convert numbers to Bengali digits if needed
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
    const addToBagText = window.SODAI_STATE?.addToBagText || (isBn ? 'ব্যাগ এ যোগ করুন' : 'Add to Bag');
    const inBagSuffix = window.SODAI_STATE?.inBagText || (isBn ? 'টি ব্যাগে' : 'in bag');

    if (quantity > 0) {
        // Active [-] Qty [+] controller
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
        // Initial "Add to Bag" button
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

    fetch('/sodai-dorkar/public/cart/add', {
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

// ==========================================
// HIERARCHICAL CATEGORY DRILLDOWN LOGIC
// ==========================================
window.SODAI_CAT_DATA = {
    locale: <?= json_encode($locale) ?>,
    all: <?= json_encode($allCategories, JSON_UNESCAPED_UNICODE) ?>,
    main: <?= json_encode($mainCategories, JSON_UNESCAPED_UNICODE) ?>,
    childrenMap: <?= json_encode($childrenMap) ?>,
    currentCategory: <?= json_encode($currentCategory) ?>,
    labels: {
        shopByCategory: <?= json_encode($__('sec_shop_by_category'), JSON_UNESCAPED_UNICODE) ?>,
        backToMain: <?= json_encode($__('cat_back_to_main'), JSON_UNESCAPED_UNICODE) ?>,
        subcategories: <?= json_encode($locale === 'bn' ? 'টি সাব-ক্যাটাগরি' : 'Sub-categories', JSON_UNESCAPED_UNICODE) ?>,
        subcatsShort: <?= json_encode($locale === 'bn' ? 'সাব-ক্যাটাগরি' : 'subcats', JSON_UNESCAPED_UNICODE) ?>,
        items: <?= json_encode($locale === 'bn' ? 'টি পণ্য' : 'items', JSON_UNESCAPED_UNICODE) ?>,
        viewAllIn: <?= json_encode($__('cat_view_all_in_category', ['name' => ':name']), JSON_UNESCAPED_UNICODE) ?>
    }
};

function slideCategories(direction) {
    const mainGrid = document.getElementById('cat-grid-main');
    const subGrid = document.getElementById('cat-grid-sub');
    const activeGrid = (subGrid && !subGrid.classList.contains('hidden')) ? subGrid : mainGrid;
    if (!activeGrid) return;

    const scrollAmount = activeGrid.clientWidth * 0.85;
    activeGrid.scrollBy({
        left: direction * scrollAmount,
        behavior: 'smooth'
    });
}

function getCategoryVisualJs(cat) {
    if (cat.image_path) {
        return { type: 'image', val: cat.image_path, bg: 'bg-white border border-gray-100' };
    }
    const n = (cat.name || '').toLowerCase();
    if (n.includes('মাছ ও মাংস')) return { type: 'image', val: '/sodai-dorkar/public/uploads/categories/1788452780_Screenshot 2026-09-03 222555.png', bg: 'bg-white border border-gray-100' };
    if (n.includes('দুধ') || n.includes('দুগ্ধ') || n.includes('dairy')) return { type: 'image', val: '/sodai-dorkar/public/uploads/products/bottle-milk-1liter.jpg', bg: 'bg-white border border-gray-100' };
    
    if (n.includes('ফল') || n.includes('fruit')) return { type: 'emoji', val: '🍎', bg: 'bg-rose-50 text-rose-500' };
    if (n.includes('শাক') || n.includes('সবজি') || n.includes('vege')) return { type: 'emoji', val: '🥦', bg: 'bg-emerald-50 text-emerald-600' };
    if (n.includes('ডিম') || n.includes('egg')) return { type: 'emoji', val: '🥚', bg: 'bg-amber-50 text-amber-600' };
    if (n.includes('চাল') || n.includes('rice')) return { type: 'emoji', val: '🌾', bg: 'bg-amber-50 text-amber-700' };
    if (n.includes('ডাল') || n.includes('dal') || n.includes('lentil')) return { type: 'emoji', val: '🥣', bg: 'bg-orange-50 text-orange-600' };
    if (n.includes('তেল') || n.includes('oil')) return { type: 'emoji', val: '🫒', bg: 'bg-lime-50 text-lime-700' };
    if (n.includes('চা') || n.includes('tea')) return { type: 'emoji', val: '🍵', bg: 'bg-emerald-50 text-emerald-600' };
    if (n.includes('মাছ') || n.includes('fish')) return { type: 'emoji', val: '🐟', bg: 'bg-cyan-50 text-cyan-600' };
    if (n.includes('মাংস') || n.includes('meat')) return { type: 'emoji', val: '🥩', bg: 'bg-rose-50 text-rose-600' };
    if (n.includes('লবন') || n.includes('চিনি') || n.includes('salt') || n.includes('sugar')) return { type: 'emoji', val: '🧂', bg: 'bg-blue-50 text-blue-600' };
    if (n.includes('মশলা') || n.includes('spice')) return { type: 'emoji', val: '🌶️', bg: 'bg-red-50 text-red-600' };
    if (n.includes('সেমাই') || n.includes('সুজি') || n.includes('মিক্স')) return { type: 'emoji', val: '🥣', bg: 'bg-amber-50 text-amber-700' };
    if (n.includes('রান্না') || n.includes('cook')) return { type: 'emoji', val: '🍳', bg: 'bg-amber-50 text-amber-600' };
    if (n.includes('আইসক্রিম') || n.includes('ice cream')) return { type: 'emoji', val: '🍦', bg: 'bg-pink-50 text-pink-500' };
    if (n.includes('ক্যান্ডি') || n.includes('চকলেট') || n.includes('chocolate') || n.includes('candy')) return { type: 'emoji', val: '🍫', bg: 'bg-amber-50 text-amber-800' };
    if (n.includes('জল খাবার') || n.includes('নাশতা') || n.includes('snack') || n.includes('breakfast')) return { type: 'emoji', val: '🥪', bg: 'bg-orange-50 text-orange-600' };
    if (n.includes('পানীয়') || n.includes('beverage') || n.includes('juice') || n.includes('drinks')) return { type: 'emoji', val: '🧃', bg: 'bg-teal-50 text-teal-600' };
    if (n.includes('বেকিং') || n.includes('baking') || n.includes('cake')) return { type: 'emoji', val: '🧁', bg: 'bg-purple-50 text-purple-600' };
    if (n.includes('হিমায়িত') || n.includes('টিনজাত') || n.includes('frozen') || n.includes('canned')) return { type: 'emoji', val: '🥫', bg: 'bg-blue-50 text-blue-600' };
    if (n.includes('ডায়বেটিক') || n.includes('diabetic')) return { type: 'emoji', val: '🥗', bg: 'bg-emerald-50 text-emerald-700' };
    if (n.includes('সস') || n.includes('আচার') || n.includes('pickle') || n.includes('sauce')) return { type: 'emoji', val: '🫙', bg: 'bg-red-50 text-red-700' };
    return { type: 'emoji', val: '🛒', bg: 'bg-emerald-50 text-emerald-600' };
}

function onMainCategoryCardClick(event, catId) {
    const data = window.SODAI_CAT_DATA;
    if (!data || !data.childrenMap || !data.childrenMap[catId] || data.childrenMap[catId].length === 0) {
        // No subcategories: proceed with standard navigation to products
        return true;
    }
    
    // Has subcategories! Let's drill down in-place smoothly
    if (event) event.preventDefault();
    drillDownCategory(catId, true);
    return false;
}

function drillDownCategory(catId, pushState = true) {
    const data = window.SODAI_CAT_DATA;
    const cat = (data.all || []).find(c => parseInt(c.id) === parseInt(catId));
    if (!cat) return;

    const childIds = (data.childrenMap && data.childrenMap[catId]) ? data.childrenMap[catId] : [];
    if (childIds.length === 0) return;

    const subCats = childIds.map(cid => (data.all || []).find(c => parseInt(c.id) === parseInt(cid))).filter(Boolean);

    // Update Header
    const drilldownName = document.getElementById('drilldown-parent-name');
    const drilldownBadge = document.getElementById('drilldown-badge');
    const drilldownViewAll = document.getElementById('drilldown-view-all');
    const drilldownViewAllText = document.getElementById('drilldown-view-all-text');

    if (drilldownName) drilldownName.textContent = cat.name;
    if (drilldownBadge) drilldownBadge.textContent = `${subCats.length} ${data.labels.subcategories}`;
    if (drilldownViewAll) {
        drilldownViewAll.href = `/sodai-dorkar/public/?category=${cat.id}#products`;
        drilldownViewAll.classList.remove('hidden');
        drilldownViewAll.classList.add('inline-flex');
    }
    if (drilldownViewAllText) {
        const viewAllLabel = data.labels.viewAllIn.replace(':name', cat.name);
        drilldownViewAllText.textContent = `${viewAllLabel} (${cat.total_product_count || 0})`;
    }

    // Render Subcategory Cards in single row slider
    const subGrid = document.getElementById('cat-grid-sub');
    if (subGrid) {
        subGrid.innerHTML = '';
        subGrid.scrollLeft = 0;
        subCats.forEach(sub => {
            const vis = getCategoryVisualJs(sub);
            const hasChild = (data.childrenMap && data.childrenMap[sub.id] && data.childrenMap[sub.id].length > 0);
            const subCount = hasChild ? data.childrenMap[sub.id].length : 0;
            const isSubActive = (parseInt(data.currentCategory) === parseInt(sub.id));

            const a = document.createElement('a');
            a.href = hasChild ? `/sodai-dorkar/public/?category=${sub.id}#categories` : `/sodai-dorkar/public/?category=${sub.id}#products`;
            a.className = `subcategory-card flex-shrink-0 w-[calc((100%-16px)/2.3)] sm:w-[calc((100%-36px)/4)] md:w-[calc((100%-56px)/5)] lg:w-[calc((100%-84px)/7)] bg-white rounded-2xl border p-3 sm:p-3.5 flex flex-col items-center justify-between text-center transition-all duration-200 group hover:-translate-y-1 hover:shadow-md cursor-pointer ${isSubActive ? 'border-emerald-600 ring-2 ring-emerald-500/20 bg-emerald-50/60 shadow-md font-bold' : 'border-gray-100 hover:border-emerald-300'}`;
            a.title = sub.name;
            if (hasChild) {
                a.onclick = (e) => onMainCategoryCardClick(e, sub.id);
            }

            let badgeHtml = '';
            if (hasChild) {
                badgeHtml = `<span class="inline-flex items-center gap-0.5 text-[10px] sm:text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full mt-1.5 border border-emerald-200/80 group-hover:bg-emerald-600 group-hover:text-white transition-all shadow-2xs">
                    <span>${subCount} ${data.labels.subcatsShort}</span>
                    <span>▾</span>
                </span>`;
            } else {
                badgeHtml = `<span class="text-[10px] sm:text-[11px] text-gray-400 font-semibold mt-1">
                    ${sub.total_product_count || 0} ${data.labels.items}
                </span>`;
            }

            const visualContent = vis.type === 'image' 
                ? `<img src="${vis.val}" alt="${sub.name}" class="w-full h-full object-contain p-1.5 transition-transform duration-300 group-hover:scale-110" onerror="this.parentElement.innerHTML='🛒'">`
                : `<span class="text-4xl sm:text-5xl xl:text-6xl select-none leading-none filter drop-shadow-sm transition-transform duration-300 group-hover:scale-110">${vis.val}</span>`;

            a.innerHTML = `
                <div class="w-20 h-20 sm:w-22 sm:h-22 lg:w-22 lg:h-22 xl:w-24 xl:h-24 rounded-2xl flex items-center justify-center mb-2.5 overflow-hidden transition-transform duration-300 group-hover:scale-105 shadow-2xs ${vis.type === 'image' ? 'bg-white border border-gray-100' : vis.bg}">
                    ${visualContent}
                </div>
                <div class="w-full flex flex-col items-center">
                    <span class="text-xs sm:text-[13px] font-extrabold text-gray-800 group-hover:text-emerald-700 transition-colors line-clamp-1 leading-snug w-full px-1">
                        ${sub.name}
                    </span>
                    ${badgeHtml}
                </div>
            `;
            subGrid.appendChild(a);
        });
    }

    // Move promotional banners section above categories section
    const promoSection = document.getElementById('promo-banners-section');
    const catSection = document.getElementById('categories');
    if (promoSection && catSection && promoSection.nextElementSibling !== catSection) {
        catSection.parentNode.insertBefore(promoSection, catSection);
    }

    // Toggle Section Views
    const mainHeader = document.getElementById('cat-header-main');
    const drilldownHeader = document.getElementById('cat-header-drilldown');
    const mainGrid = document.getElementById('cat-grid-main');

    if (mainHeader) mainHeader.classList.add('hidden');
    if (mainGrid) mainGrid.classList.add('hidden');
    if (drilldownHeader) drilldownHeader.classList.remove('hidden');
    if (subGrid) subGrid.classList.remove('hidden');

    if (pushState) {
        history.pushState({ categoryDrillId: cat.id }, '', `/sodai-dorkar/public/?category=${cat.id}#categories`);
    }

    if (catSection && catSection.getBoundingClientRect().top < -50) {
        catSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function resetToMainCategories(event) {
    if (event) event.preventDefault();
    const mainHeader = document.getElementById('cat-header-main');
    const drilldownHeader = document.getElementById('cat-header-drilldown');
    const mainGrid = document.getElementById('cat-grid-main');
    const subGrid = document.getElementById('cat-grid-sub');

    if (drilldownHeader) drilldownHeader.classList.add('hidden');
    if (subGrid) subGrid.classList.add('hidden');
    if (mainHeader) mainHeader.classList.remove('hidden');
    if (mainGrid) {
        mainGrid.classList.remove('hidden');
        mainGrid.scrollLeft = 0;
    }

    // Move promotional banners section back below categories section
    const promoSection = document.getElementById('promo-banners-section');
    const catSection = document.getElementById('categories');
    if (promoSection && catSection && catSection.nextElementSibling !== promoSection) {
        catSection.parentNode.insertBefore(promoSection, catSection.nextSibling);
    }

    history.pushState({ categoryDrillId: null }, '', `/sodai-dorkar/public/#categories`);
}

window.addEventListener('popstate', (e) => {
    if (e.state && e.state.categoryDrillId) {
        drillDownCategory(e.state.categoryDrillId, false);
    } else {
        const mainHeader = document.getElementById('cat-header-main');
        const drilldownHeader = document.getElementById('cat-header-drilldown');
        const mainGrid = document.getElementById('cat-grid-main');
        const subGrid = document.getElementById('cat-grid-sub');
        if (drilldownHeader) drilldownHeader.classList.add('hidden');
        if (subGrid) subGrid.classList.add('hidden');
        if (mainHeader) mainHeader.classList.remove('hidden');
        if (mainGrid) mainGrid.classList.remove('hidden');

        const promoSection = document.getElementById('promo-banners-section');
        const catSection = document.getElementById('categories');
        if (promoSection && catSection && catSection.nextElementSibling !== promoSection) {
            catSection.parentNode.insertBefore(promoSection, catSection.nextSibling);
        }
    }
});

// Trigger initial card sync on load
document.addEventListener('DOMContentLoaded', () => {
    syncProductCardsWithCart();
});
</script>

<?php 
$content = ob_get_clean();
require 'layout.php';
?>
