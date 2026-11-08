<?php 
ob_start(); 
use Core\Lang;
Lang::init();
$__ = function($key, $r = []) { return Lang::get($key, $r); };
$locale = Lang::locale();
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';

// Determine Active Homepage Variant (Admin setting + live URL preview support)
$savedVariant = class_exists('\Models\Setting') ? \Models\Setting::getValue('homepage_variant', 'variant_1') : 'variant_1';
$homeVariant = $_GET['home_variant'] ?? $savedVariant;
if (!in_array($homeVariant, ['variant_1', 'variant_2', 'variant_3'], true)) {
    $homeVariant = 'variant_1';
}
$variantQuerySuffix = isset($_GET['home_variant']) ? '&home_variant=' . urlencode($homeVariant) : '';
$variantQueryFirst  = isset($_GET['home_variant']) ? '?home_variant=' . urlencode($homeVariant) : '';

// Helper function to extract and format unit badges
if (!function_exists('getProductUnits')) {
function getProductUnits($product) {
    $locale = \Core\Lang::locale();
    if (!empty($product['unit_variants_json'])) {
        $decoded = json_decode($product['unit_variants_json'], true);
        if (!empty($decoded) && is_array($decoded)) {
            $variantsList = [];
            $defaultTitle = '';
            $defaultPrice = $product['sell_price'];
            $defaultQty = 1;
            $firstOffer = '';

            foreach ($decoded as $v) {
                $offer = trim($v['offer'] ?? '');
                $offerLimit = trim($v['offer_limit'] ?? 'unlimited');
                $offerLimitLabel = \Models\Product::getOfferLimitLabel($offerLimit, $locale);
                if (empty($firstOffer) && !empty($offer)) {
                    $firstOffer = $offer;
                }
                $variantsList[] = [
                    'title' => $v['title'],
                    'qty' => $v['qty'] ?? 1,
                    'price' => $v['price'],
                    'is_default' => !empty($v['is_default']),
                    'offer' => $offer,
                    'offer_limit' => $offerLimit,
                    'offer_limit_label' => $offerLimitLabel
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
                'variants' => $variantsList,
                'featured_offer' => $firstOffer
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

if (!function_exists('getCategoryPromoBadge')) {
function getCategoryPromoBadge($cName) {
    $cName = mb_strtolower($cName ?? '');
    if (mb_strpos($cName, 'রান্না') !== false || mb_strpos($cName, 'তেল') !== false || mb_strpos($cName, 'মুদি') !== false) {
        return ['text' => '১০% ছাড়', 'cls' => 'bg-rose-500 text-white'];
    } elseif (mb_strpos($cName, 'মাছ') !== false || mb_strpos($cName, 'মাংস') !== false) {
        return ['text' => 'তাজা', 'cls' => 'bg-sky-500 text-white'];
    } elseif (mb_strpos($cName, 'শাক') !== false || mb_strpos($cName, 'সবজি') !== false || mb_strpos($cName, 'ফল') !== false) {
        return ['text' => 'ফার্ম তাজা', 'cls' => 'bg-emerald-600 text-white'];
    } elseif (mb_strpos($cName, 'দুধ') !== false || mb_strpos($cName, 'ডিম') !== false || mb_strpos($cName, 'ডেইরি') !== false) {
        return ['text' => 'জনপ্রিয়', 'cls' => 'bg-amber-500 text-white'];
    } elseif (mb_strpos($cName, 'স্ন্যাকস') !== false || mb_strpos($cName, 'বিস্কুট') !== false) {
        return ['text' => 'অফার', 'cls' => 'bg-purple-600 text-white'];
    }
    return null;
}
}

// Reusable Product Card Renderer (fixes overlapping badges, icon sizes & preserves all JS cart hooks)
if (!function_exists('renderStorefrontProductCard')) {
function renderStorefrontProductCard($product, $pIdx, $isFiltered, $base, $locale, $__, $compactMode = false) {
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
    $fallbackImg = !empty($base) ? rtrim($base, '/') . '/images/default-product.svg' : '/images/default-product.svg';

    // Select single primary left badge on mobile so badges never collide in narrow viewports
    $primaryBadgeHtml = '';
    if (!empty($badgeLabel)) {
        $pulseCls = ($isBogo || !empty($criteria['has_variant_offers'])) ? 'animate-pulse' : '';
        $primaryBadgeHtml = '<span class="bg-gradient-to-r from-amber-500 to-rose-600 text-white text-[9px] sm:text-[10px] font-black px-2 py-0.5 rounded-full shadow-xs inline-flex items-center gap-0.5 truncate max-w-full ' . $pulseCls . '">' . htmlspecialchars($badgeLabel) . '</span>';
    } elseif ($specialBadge === 'hot_deal') {
        $primaryBadgeHtml = '<span class="bg-gradient-to-r from-red-600 to-orange-500 text-white text-[9px] sm:text-[10px] font-black px-2 py-0.5 rounded-full shadow-xs inline-flex items-center gap-0.5">⚡ ' . ($locale === 'bn' ? 'হট ডিল' : 'Hot Deal') . '</span>';
    } elseif ($specialBadge === 'fresh_catch') {
        $primaryBadgeHtml = '<span class="bg-gradient-to-r from-cyan-600 to-blue-600 text-white text-[9px] sm:text-[10px] font-black px-2 py-0.5 rounded-full shadow-xs inline-flex items-center gap-0.5">🐟 ' . ($locale === 'bn' ? 'তাজা সংগ্রহ' : 'Fresh') . '</span>';
    } elseif ($specialBadge === 'halal_meat') {
        $primaryBadgeHtml = '<span class="bg-gradient-to-r from-emerald-700 to-teal-700 text-white text-[9px] sm:text-[10px] font-black px-2 py-0.5 rounded-full shadow-xs inline-flex items-center gap-0.5">🥩 ' . ($locale === 'bn' ? 'হালাল মাংস' : 'Halal') . '</span>';
    } elseif ($isTopOne) {
        $primaryBadgeHtml = '<span class="bg-gradient-to-r from-amber-500 to-orange-500 text-white text-[9px] sm:text-[10px] font-black px-2 py-0.5 rounded-full shadow-xs inline-flex items-center gap-0.5">👑 ' . ($locale === 'bn' ? '#১ সেরা পণ্য' : '#1 Popular') . '</span>';
    } elseif ($hasDiscount) {
        $primaryBadgeHtml = '<span class="bg-emerald-600 text-white text-[9px] sm:text-[10px] font-black px-2 py-0.5 rounded-full shadow-xs tracking-tight">' . $discountPercent . '% ' . ($locale === 'bn' ? 'ছাড়' : 'OFF') . '</span>';
    } elseif ($demandPct > 0) {
        $primaryBadgeHtml = '<span class="bg-gradient-to-r from-amber-500 to-orange-500 text-white text-[9px] sm:text-[10px] font-bold px-2 py-0.5 rounded-full shadow-xs inline-flex items-center gap-0.5">🔥 ' . $demandPct . '% ' . ($locale === 'bn' ? 'জনপ্রিয়' : 'Popular') . '</span>';
    } elseif ($totalSold > 0) {
        $primaryBadgeHtml = '<span class="bg-amber-600 text-white text-[9px] sm:text-[10px] font-bold px-2 py-0.5 rounded-full shadow-xs inline-flex items-center gap-0.5">🔥 ' . ($locale === 'bn' ? 'সেরা বিক্রিত' : 'Best Seller') . '</span>';
    }
    ?>
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
        <!-- Admin Quick Edit Floating Action -->
        <a href="<?= $base ?>/admin/products/edit?id=<?= $product['id'] ?>" target="_blank" title="<?= $locale === 'bn' ? 'অ্যাডমিনে এডিট করুন' : 'Edit in Admin' ?>" class="absolute top-2 right-2 z-20 w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-slate-900/90 hover:bg-amber-500 text-amber-300 hover:text-slate-950 flex items-center justify-center shadow-md transition-all hover:scale-110 border border-white/20">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" class="h-3 w-3 sm:h-3.5 sm:w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
            </svg>
        </a>
        <?php endif; ?>

        <!-- Non-Overlapping Top Badges Row -->
        <div class="absolute top-2 left-2 right-2 z-10 flex items-center justify-between pointer-events-none gap-1">
            <div class="min-w-0 max-w-[68%] flex flex-col gap-1 items-start">
                <?= $primaryBadgeHtml ?>
            </div>

            <?php if ($isOutOfStock): ?>
                <span class="bg-rose-600 text-white text-[8.5px] sm:text-[9px] font-bold px-1.5 py-0.5 rounded-full shadow-xs shrink-0 whitespace-nowrap">
                    <?= $__('products_out_of_stock') ?>
                </span>
            <?php elseif ($stockQtyNum < 10 && $stockQtyNum > 0 && empty($_SESSION['user_id'])): ?>
                <span class="bg-amber-50/95 text-amber-900 border border-amber-300 text-[8.5px] sm:text-[9px] font-bold px-1.5 py-0.5 rounded-full shadow-xs shrink-0 whitespace-nowrap">
                    <?= $locale === 'bn' ? 'বাকি:' : 'Left:' ?> <?= $stockClean ?>
                </span>
            <?php endif; ?>
        </div>

        <!-- Centered Clean Product Image Container -->
        <a href="<?= $base ?>/product?id=<?= $product['id'] ?>" class="relative bg-white <?= $compactMode ? 'h-32 sm:h-44 p-2.5 pt-7' : 'h-36 sm:h-48 p-3 pt-7' ?> w-full flex items-center justify-center overflow-hidden cursor-pointer group/img">
            <img src="<?= htmlspecialchars($productImg) ?>" 
                 alt="<?= htmlspecialchars($product['name']) ?>" 
                 class="h-full w-full object-contain mix-blend-multiply transition-transform duration-500 group-hover/img:scale-105" 
                 loading="lazy"
                 onerror="this.onerror=null; this.src='<?= $fallbackImg ?>';">
        </a>

        <!-- Card Content -->
        <div class="<?= $compactMode ? 'p-2.5 sm:p-3.5' : 'p-3 sm:p-3.5' ?> pt-1 flex flex-col flex-grow bg-white">
            <!-- Product Name -->
            <h3 class="font-bold text-gray-900 text-xs sm:text-[13px] leading-snug line-clamp-2 min-h-[2.1rem] sm:min-h-[2.4rem] mb-1 group-hover:text-emerald-700 transition-colors" 
                title="<?= htmlspecialchars($product['name']) ?>">
                <a href="<?= $base ?>/product?id=<?= $product['id'] ?>" class="hover:text-emerald-700 transition-colors">
                    <?= htmlspecialchars($product['name']) ?>
                </a>
            </h3>

            <!-- Unit / Weight -->
            <div class="text-[10px] sm:text-[11px] text-gray-400 font-semibold mb-1.5 flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" class="h-3 w-3 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                </svg>
                <span class="card-unit-label truncate"><?= htmlspecialchars($hasCustom ? $initialTitle : $uInfo['primary']) ?></span>
            </div>

            <!-- Price Section & Savings Badge -->
            <div class="flex items-baseline gap-1.5 mb-2 flex-wrap">
                <div class="flex items-baseline gap-0.5">
                    <span class="text-xs font-bold text-emerald-800 leading-none">৳</span>
                    <span class="card-price text-sm sm:text-base font-black text-gray-900 leading-none">
                        <?= formatPrice($initialPrice) ?>
                    </span>
                </div>

                <?php if ($hasDiscount): ?>
                    <span class="card-regular-price text-[10px] sm:text-[11px] text-gray-400 line-through font-medium leading-none">
                        ৳<?= formatPrice($product['regular_price']) ?>
                    </span>
                    <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200/80 leading-none">
                        ৳<?= formatPrice($discountSavings) ?> <?= $locale === 'bn' ? 'সাশ্রয়' : 'Save' ?>
                    </span>
                <?php endif; ?>
            </div>

            <!-- Unit Variant Badges (Interactive Pills) -->
            <?php if ($hasCustom && !empty($uInfo['variants'])): ?>
                <div class="pt-0.5 pb-2 flex flex-wrap items-center gap-1 unit-badges-container">
                    <?php foreach ($uInfo['variants'] as $v): ?>
                        <?php 
                            $isActive = ($v['title'] === $initialTitle); 
                            $activeClass = $isActive 
                                ? 'border-emerald-600 text-emerald-700 bg-emerald-50/80 font-bold active-variant shadow-xs' 
                                : 'border-gray-200 text-gray-500 bg-white hover:border-gray-300 font-medium';
                        ?>
                        <button type="button" 
                                onclick="selectProductCardVariant(this, '<?= htmlspecialchars($v['title'], ENT_QUOTES) ?>', <?= floatval($v['price']) ?>, <?= floatval($v['qty'] ?? 1) ?>)"
                                class="variant-pill inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] sm:text-[11px] border transition-all cursor-pointer <?= $activeClass ?>">
                            <?= htmlspecialchars($v['title']) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Bottom Action: Add to Bag / Interactive Stepper -->
            <div class="mt-auto pt-1 card-action-container" data-product-id="<?= $product['id'] ?>">
                <?php if ($isOutOfStock): ?>
                    <button type="button" disabled class="w-full py-2 px-2 rounded-xl bg-gray-100 text-gray-400 border border-gray-200 font-bold text-xs flex items-center justify-center gap-1.5 cursor-not-allowed">
                        <span><?= $__('products_out_of_stock') ?></span>
                    </button>
                <?php elseif ($hasCriteria): ?>
                    <button type="button" 
                            onclick="openQuickCriteriaModal(<?= $product['id'] ?>, this)" 
                            class="w-full py-2 px-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 active:scale-95 text-white font-black text-xs flex items-center justify-center gap-1.5 transition-all duration-200 shadow-xs hover:shadow-amber-500/30 cursor-pointer group/btn <?= ($isBogo || !empty($criteria['has_variant_offers'])) ? 'animate-pulse' : '' ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" class="h-3.5 w-3.5 text-white shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z" />
                        </svg>
                        <span class="truncate"><?= htmlspecialchars($buttonLabel) ?></span>
                    </button>
                <?php else: ?>
                    <button type="button" 
                            onclick="cardAddToCart(<?= $product['id'] ?>, this)" 
                            class="w-full py-2 px-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-extrabold text-xs flex items-center justify-center gap-1.5 transition-all duration-200 shadow-xs hover:shadow-emerald-600/30 cursor-pointer group/btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" class="h-3.5 w-3.5 sm:h-4 sm:w-4 text-white shrink-0 transition-transform group-hover/btn:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span class="truncate"><?= $__('add_to_bag') ?></span>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
}
}

// 1. Prepare Categories
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

$isAdminViewer = !empty($_SESSION['user_id']) && in_array($_SESSION['role'] ?? '', ['admin', 'super_admin', 'manager', 'accountant', 'staff']);
?>

<?php if ($isAdminViewer): ?>
<!-- Admin Live Homepage Variant Switcher Bar -->
<div class="bg-emerald-950/95 border-b border-emerald-800/80 text-white px-3 py-1.5">
    <div class="max-w-[1720px] mx-auto flex items-center justify-between gap-2 flex-wrap text-[11px]">
        <div class="flex items-center gap-1.5 font-bold text-emerald-300">
            <span>🎨 হোম পেজ ডিজাইন প্রিভিউ:</span>
            <?php if ($homeVariant !== $savedVariant): ?>
                <span class="bg-amber-400 text-slate-950 px-1.5 py-0.5 rounded font-black text-[10px]">টেস্ট প্রিভিউ মোড</span>
            <?php endif; ?>
        </div>
        <div class="flex items-center gap-1.5 flex-wrap">
            <a href="<?= $base ?>/?home_variant=variant_1" class="px-2.5 py-1 rounded-lg font-bold transition-all <?= $homeVariant === 'variant_1' ? 'bg-emerald-500 text-white shadow-xs' : 'bg-white/10 text-emerald-100 hover:bg-white/20' ?>">
                ১. মডার্ন ফুল-উইডথ
            </a>
            <a href="<?= $base ?>/?home_variant=variant_2" class="px-2.5 py-1 rounded-lg font-bold transition-all <?= $homeVariant === 'variant_2' ? 'bg-emerald-500 text-white shadow-xs' : 'bg-white/10 text-emerald-100 hover:bg-white/20' ?>">
                ২. স্মার্ট স্প্লিট রেল
            </a>
            <a href="<?= $base ?>/?home_variant=variant_3" class="px-2.5 py-1 rounded-lg font-bold transition-all <?= $homeVariant === 'variant_3' ? 'bg-emerald-500 text-white shadow-xs' : 'bg-white/10 text-emerald-100 hover:bg-white/20' ?>">
                ৩. সুপারস্টোর শোকেস
            </a>
            <a href="<?= $base ?>/admin/settings" class="ml-1 px-2.5 py-1 rounded-lg bg-amber-400 hover:bg-amber-300 text-slate-950 font-black transition-colors">
                সেটিংসে স্থায়ী করুন ⚙
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($homeVariant === 'variant_1'): ?>
<!-- =========================================================================
     VARIANT 1: MODERN FULL-WIDTH APP STYLE (Chaldal / Zepto Pro Experience)
     Full-width on mobile with swipeable visual category cards + desktop sidebar
     ========================================================================= -->
<main class="w-full max-w-[1720px] mx-auto px-3 sm:px-4 py-3 sm:py-5">
    <div class="flex items-start gap-4 lg:gap-5">

        <!-- Desktop Sticky Left Category Rail (Hidden on Mobile so Mobile gets 100% Full Width!) -->
        <aside class="hidden lg:block w-[108px] flex-shrink-0 sticky top-20 z-20 self-start">
            <div class="bg-white/95 backdrop-blur-md border border-gray-200/90 rounded-3xl p-1.5 flex flex-col items-center shadow-xs max-h-[calc(100vh-6rem)] overflow-y-auto no-scrollbar">
                <a href="<?= $base ?>/<?= $variantQueryFirst ?>" class="text-[11px] font-black text-gray-800 hover:text-emerald-700 text-center uppercase tracking-wider mb-2 pb-1.5 border-b border-gray-100 w-full select-none block transition-colors">
                    <?= $locale === 'bn' ? 'সকল ক্যাটাগরি' : 'Categories' ?>
                </a>
                <div class="flex flex-col items-center gap-2.5 w-full py-0.5">
                    <?php foreach ($railCategories as $rc): ?>
                        <?php
                            $isRcActive = false;
                            if (!empty($activeCat)) {
                                if ((int)$activeCat['id'] === (int)$rc['id']) {
                                    $isRcActive = true;
                                } elseif (!empty($parentCategory) && (int)$parentCategory['id'] === (int)$rc['id']) {
                                    $isRcActive = true;
                                }
                            }
                            $vis = getCategoryVisual($rc, $base);
                            $catBadge = getCategoryPromoBadge($rc['name'] ?? '');
                        ?>
                        <a href="<?= $base ?>/?category=<?= $rc['id'] ?><?= $variantQuerySuffix ?>" 
                           class="flex flex-col items-center group text-center w-full transition-transform active:scale-95 cursor-pointer"
                           title="<?= htmlspecialchars($rc['name']) ?>">
                            <div class="w-[76px] h-[76px] rounded-2xl flex items-center justify-center transition-all duration-300 shadow-xs relative overflow-hidden bg-white border-2 <?= $isRcActive ? 'border-emerald-600 ring-4 ring-emerald-500/20 scale-105 shadow-md bg-emerald-50/40' : 'border-gray-100 group-hover:border-emerald-400 group-hover:scale-105' ?>">
                                <?php if ($catBadge): ?>
                                    <span class="absolute top-1 right-1 <?= $catBadge['cls'] ?> font-black text-[8px] px-1 py-0.5 rounded-full shadow-xs leading-none z-10">
                                        <?= $catBadge['text'] ?>
                                    </span>
                                <?php endif; ?>
                                <?php if ($vis['type'] === 'image'): ?>
                                    <img src="<?= htmlspecialchars($vis['val']) ?>" alt="<?= htmlspecialchars($rc['name']) ?>" class="w-full h-full object-contain p-1 transition-transform duration-300 group-hover:scale-110" loading="lazy" onerror="this.onerror=null; this.src='<?= $base ?>/images/default-category.svg';">
                                <?php else: ?>
                                    <span class="text-3xl select-none leading-none"><?= $vis['val'] ?></span>
                                <?php endif; ?>
                            </div>
                            <span class="text-[11px] font-extrabold mt-1 leading-tight text-center line-clamp-2 <?= $isRcActive ? 'text-emerald-950 font-black' : 'text-gray-700 group-hover:text-emerald-800' ?>">
                                <?= htmlspecialchars($rc['name']) ?>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </aside>

        <!-- Main Content Column (100% Width on Mobile & Tablet!) -->
        <section class="flex-1 min-w-0">

            <!-- 1. Sleek Modern Hero Banner -->
            <div class="bg-gradient-to-br from-emerald-900 via-emerald-800 to-teal-950 rounded-3xl p-4 sm:p-6 lg:p-7 text-white shadow-lg relative overflow-hidden mb-3.5 sm:mb-4 border border-emerald-700/30">
                <div class="absolute -right-12 -bottom-12 w-60 h-60 bg-emerald-400/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute left-1/3 -top-12 w-48 h-48 bg-teal-400/15 rounded-full blur-2xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-4 lg:gap-6">
                    <div class="flex items-center gap-3.5 sm:gap-5 w-full lg:w-auto">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 flex-shrink-0 relative">
                            <img src="<?= $base ?>/images/grocery_cart_hero.jpg" 
                                 alt="Fresh Grocery Basket" 
                                 class="w-full h-full object-contain drop-shadow-xl rounded-2xl bg-white/10 p-1 border border-white/15">
                        </div>

                        <div class="min-w-0 flex-1">
                            <?php if (!$isFiltered): ?>
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/15 backdrop-blur-md text-amber-300 text-[10px] sm:text-xs font-black mb-1.5 border border-white/20">
                                    <span>🔥</span>
                                    <span><?= $locale === 'bn' ? 'শীর্ষ চাহিদাসম্পন্ন ও সেরা বাজার' : 'Top In-Demand & Best Market' ?></span>
                                </div>
                            <?php elseif (!empty($parentCategory) && (int)$parentCategory['id'] !== (int)($activeCat['id'] ?? 0)): ?>
                                <a href="<?= $base ?>/?category=<?= $parentCategory['id'] ?><?= $variantQuerySuffix ?>" class="inline-flex items-center gap-1 text-xs text-emerald-200 hover:text-white mb-1 transition-colors font-semibold">
                                    <svg width="14" height="14" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                                    <span><?= htmlspecialchars($parentCategory['name']) ?></span>
                                </a>
                            <?php endif; ?>

                            <h1 class="text-lg sm:text-2xl md:text-3xl lg:text-4xl font-black tracking-tight text-white leading-tight">
                                <?= htmlspecialchars($bannerTitle) ?>
                            </h1>

                            <p class="text-xs sm:text-sm text-emerald-100/90 font-medium mt-1 leading-snug line-clamp-2 max-w-xl">
                                <?= htmlspecialchars($bannerSubtitle) ?>
                            </p>

                            <div class="flex items-center gap-2 mt-2.5 flex-wrap">
                                <a href="<?= $base ?>/?deals=1<?= $variantQuerySuffix ?>" class="inline-flex items-center gap-1 bg-amber-400 hover:bg-amber-300 text-gray-950 font-black text-xs px-3 py-1.5 rounded-xl shadow-xs transition-transform active:scale-95">
                                    <span>⚡</span>
                                    <span><?= $locale === 'bn' ? 'ফ্ল্যাশ ডিলস' : 'Flash Deals' ?></span>
                                </a>

                                <button type="button" 
                                        onclick="navigator.clipboard.writeText('SODAI50'); showToast('<?= $locale === 'bn' ? 'কুপন SODAI50 কপি করা হয়েছে!' : 'Coupon SODAI50 copied!' ?>', 'success')" 
                                        class="inline-flex items-center gap-1 bg-white/15 hover:bg-white/25 text-white font-bold text-xs px-3 py-1.5 rounded-xl border border-white/20 transition-all cursor-pointer active:scale-95">
                                    <span>🎁</span>
                                    <span><?= $locale === 'bn' ? 'কুপন: SODAI50' : 'Coupon: SODAI50' ?></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <?php if (!$isFiltered && !empty($topPopularProduct)): ?>
                        <div class="hidden lg:flex items-center justify-start gap-3 bg-emerald-950/60 border border-emerald-500/30 rounded-2xl p-3 shadow-lg backdrop-blur-md flex-shrink-0 group hover:border-amber-400/50 transition-all">
                            <div class="relative w-16 h-16 bg-white rounded-xl p-1 flex items-center justify-center shrink-0 overflow-hidden">
                                <span class="absolute top-0 left-0 bg-amber-500 text-white text-[8px] font-black px-1.5 py-0.5 rounded-br-lg z-10">👑 #১</span>
                                <img src="<?= htmlspecialchars(\Models\Product::getImageUrl($topPopularProduct['image_path'] ?? '', $base)) ?>" 
                                     alt="<?= htmlspecialchars($topPopularProduct['name']) ?>" 
                                     class="w-full h-full object-contain group-hover:scale-110 transition-transform"
                                     onerror="this.onerror=null; this.src='<?= $base ?>/images/default-product.svg';">
                            </div>
                            <div class="text-left max-w-[210px]">
                                <span class="text-[10px] uppercase font-black tracking-wider text-amber-300 flex items-center gap-1">
                                    ⭐ <?= $locale === 'bn' ? '১ নম্বর সেরা পণ্য' : '#1 Best Seller' ?>
                                </span>
                                <a href="<?= $base ?>/product?id=<?= $topPopularProduct['id'] ?>" class="text-xs sm:text-sm font-bold text-white hover:text-amber-200 block truncate transition-colors">
                                    <?= htmlspecialchars($topPopularProduct['name']) ?>
                                </a>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs sm:text-sm font-black text-amber-300">৳<?= formatPrice($topPopularProduct['sell_price']) ?></span>
                                    <a href="<?= $base ?>/product?id=<?= $topPopularProduct['id'] ?>" class="ml-auto text-[10px] font-black bg-emerald-700 hover:bg-emerald-600 text-white px-2 py-0.5 rounded-lg transition-colors">
                                        <?= $locale === 'bn' ? 'দেখুন' : 'View' ?> →
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 2. Mobile & Tablet Visual Category Slider (Replaces cramped left sidebar on mobile!) -->
            <div class="lg:hidden mb-3.5" id="categories">
                <div class="flex items-center justify-between mb-2 px-0.5">
                    <h2 class="text-xs sm:text-sm font-black text-gray-800 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                        <span><?= $locale === 'bn' ? 'ক্যাটাগরি অনুযায়ী বাজার করুন' : 'Shop by Category' ?></span>
                    </h2>
                    <a href="<?= $base ?>/category" class="text-[11px] font-bold text-emerald-700 hover:underline">
                        <?= $locale === 'bn' ? 'সব দেখুন →' : 'View All →' ?>
                    </a>
                </div>
                <div class="flex items-start gap-2.5 overflow-x-auto no-scrollbar pb-1">
                    <?php foreach ($railCategories as $rc): ?>
                        <?php
                            $isRcActive = !empty($activeCat) && ((int)$activeCat['id'] === (int)$rc['id'] || (!empty($parentCategory) && (int)$parentCategory['id'] === (int)$rc['id']));
                            $vis = getCategoryVisual($rc, $base);
                            $catBadge = getCategoryPromoBadge($rc['name'] ?? '');
                        ?>
                        <a href="<?= $base ?>/?category=<?= $rc['id'] ?><?= $variantQuerySuffix ?>" 
                           class="flex flex-col items-center flex-shrink-0 w-[72px] sm:w-[82px] group text-center transition-transform active:scale-95">
                            <div class="w-16 h-16 sm:w-[72px] sm:h-[72px] rounded-2xl flex items-center justify-center transition-all relative overflow-hidden bg-white border-2 <?= $isRcActive ? 'border-emerald-600 ring-2 ring-emerald-500/20 shadow-md bg-emerald-50/40' : 'border-gray-200/80 shadow-xs' ?>">
                                <?php if ($catBadge): ?>
                                    <span class="absolute top-0.5 right-0.5 <?= $catBadge['cls'] ?> font-black text-[7px] px-1 py-0.5 rounded-full leading-none z-10">
                                        <?= $catBadge['text'] ?>
                                    </span>
                                <?php endif; ?>
                                <?php if ($vis['type'] === 'image'): ?>
                                    <img src="<?= htmlspecialchars($vis['val']) ?>" alt="<?= htmlspecialchars($rc['name']) ?>" class="w-full h-full object-contain p-1" loading="lazy" onerror="this.onerror=null; this.src='<?= $base ?>/images/default-category.svg';">
                                <?php else: ?>
                                    <span class="text-2xl select-none leading-none"><?= $vis['val'] ?></span>
                                <?php endif; ?>
                            </div>
                            <span class="text-[10px] sm:text-[11px] font-bold mt-1 leading-tight line-clamp-2 <?= $isRcActive ? 'text-emerald-800 font-black' : 'text-gray-700' ?>">
                                <?= htmlspecialchars($rc['name']) ?>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- 3. Compact 4-Feature Trust Bar -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-2.5 sm:p-3.5 mb-3.5 sm:mb-4 grid grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 text-sm sm:text-base">⚡</div>
                    <div class="min-w-0">
                        <h4 class="text-[11px] sm:text-xs font-black text-gray-900 leading-tight"><?= $locale === 'bn' ? 'দ্রুত ডেলিভারি' : 'Fast Delivery' ?></h4>
                        <p class="text-[9.5px] sm:text-[10px] text-gray-400 font-medium leading-tight truncate">
                            <?= $expressDeliveryEnabled ? ($locale === 'bn' ? "{$expressDeliveryDisplay}র মধ্যে ডেলিভারি" : "Within {$expressDeliveryDisplay}") : ($locale === 'bn' ? 'সরাসরি আপনার ঠিকানায়' : 'To your doorstep') ?>
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 text-sm sm:text-base">🥦</div>
                    <div class="min-w-0">
                        <h4 class="text-[11px] sm:text-xs font-black text-gray-900 leading-tight"><?= $locale === 'bn' ? '১০০% খাঁটি ও তাজা' : '100% Fresh' ?></h4>
                        <p class="text-[9.5px] sm:text-[10px] text-gray-400 font-medium leading-tight truncate"><?= $locale === 'bn' ? 'বাছাইকৃত সেরা পণ্য' : 'Top quality guaranteed' ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 text-sm sm:text-base">💵</div>
                    <div class="min-w-0">
                        <h4 class="text-[11px] sm:text-xs font-black text-gray-900 leading-tight"><?= $locale === 'bn' ? 'ক্যাশ অন ডেলিভারি' : 'Cash on Delivery' ?></h4>
                        <p class="text-[9.5px] sm:text-[10px] text-gray-400 font-medium leading-tight truncate"><?= $locale === 'bn' ? 'পণ্য পেয়ে টাকা দিন' : 'Pay on delivery' ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 text-sm sm:text-base">🔄</div>
                    <div class="min-w-0">
                        <h4 class="text-[11px] sm:text-xs font-black text-gray-900 leading-tight"><?= $locale === 'bn' ? 'সহজ রিটার্ন' : 'Easy Return' ?></h4>
                        <p class="text-[9.5px] sm:text-[10px] text-gray-400 font-medium leading-tight truncate"><?= $locale === 'bn' ? 'তাৎক্ষণিক সমাধান' : 'Instant replacement' ?></p>
                    </div>
                </div>
            </div>

            <!-- 4. Subcategory / Filter Pills Bar -->
            <div class="mb-3.5">
                <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1">
                    <?php if ($isFiltered): ?>
                        <a href="<?= $base ?>/<?= $variantQueryFirst ?>" class="flex-shrink-0 pl-2.5 pr-3 py-1.5 rounded-full text-xs font-bold transition-all flex items-center gap-1.5 bg-emerald-950 hover:bg-emerald-900 text-white shadow-xs">
                            <svg width="14" height="14" class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            <span class="whitespace-nowrap"><?= $__('nav_home') ?></span>
                        </a>
                    <?php endif; ?>

                    <?php 
                        $isAllActive = empty($activeSubId) && (!$isFiltered || empty($activeCat));
                        $allPillUrl = !empty($activeCat) ? ($base . '/?category=' . $activeCat['id'] . $variantQuerySuffix) : ($base . '/' . $variantQueryFirst);
                    ?>
                    <a href="<?= $allPillUrl ?>" 
                       class="flex-shrink-0 pl-1.5 pr-3.5 py-1.5 rounded-full text-xs sm:text-sm font-bold transition-all flex items-center gap-2 <?= $isAllActive ? 'bg-emerald-700 text-white shadow-md shadow-emerald-700/25' : 'bg-white hover:bg-emerald-50 text-gray-700 border border-gray-200/90 shadow-xs' ?>">
                        <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-white flex items-center justify-center shrink-0 <?= $isAllActive ? 'border border-white/40' : 'border border-gray-200' ?>">
                            <span class="text-xs leading-none select-none">🛒</span>
                        </div>
                        <span class="whitespace-nowrap"><?= $locale === 'bn' ? 'সবগুলো' : 'All' ?></span>
                    </a>

                    <?php foreach ($subs as $sc): ?>
                        <?php 
                            $hasChildCats = !empty($childrenMap[$sc['id']]);
                            if (!$isFiltered) {
                                $isScActive = false;
                                $scUrl = $base . '/?category=' . $sc['id'] . $variantQuerySuffix;
                            } else {
                                $isScActive = ($activeSubId && (int)$activeSubId === (int)$sc['id']);
                                $scUrl = $hasChildCats ? ($base . '/?category=' . $sc['id'] . $variantQuerySuffix) : ($base . '/?category=' . ($activeCat['id'] ?? '') . '&sub=' . $sc['id'] . $variantQuerySuffix);
                            }
                            $scVis = getCategoryVisual($sc, $base);
                        ?>
                        <a href="<?= $scUrl ?>" 
                           class="flex-shrink-0 pl-1.5 pr-3 py-1.5 rounded-full text-xs sm:text-sm transition-all flex items-center gap-2 <?= $isScActive ? 'bg-emerald-700 text-white font-black shadow-md' : 'bg-white hover:bg-emerald-50 text-gray-700 border border-gray-200/90 font-semibold shadow-xs' ?>">
                            <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-white flex items-center justify-center overflow-hidden shrink-0 border border-gray-200">
                                <?php if ($scVis['type'] === 'image'): ?>
                                    <img src="<?= htmlspecialchars($scVis['val']) ?>" alt="<?= htmlspecialchars($sc['name']) ?>" class="w-full h-full object-contain p-0.5" loading="lazy" onerror="this.onerror=null; this.src='<?= $base ?>/images/default-category.svg';">
                                <?php else: ?>
                                    <span class="text-xs leading-none"><?= $scVis['val'] ?></span>
                                <?php endif; ?>
                            </div>
                            <span class="whitespace-nowrap"><?= htmlspecialchars($sc['name']) ?></span>
                            <?php if (!empty($sc['total_product_count'])): ?>
                                <span class="text-[10px] px-1.5 py-0.5 rounded-full <?= $isScActive ? 'bg-white/20 text-white font-bold' : 'bg-gray-100 text-gray-600 font-bold' ?>">
                                    <?= $sc['total_product_count'] ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Active Filter Bar -->
            <?php if ($isFiltered): ?>
                <div class="mb-3.5 px-3.5 py-2.5 rounded-2xl bg-emerald-50/90 border border-emerald-200/90 flex items-center justify-between text-xs shadow-xs">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 animate-pulse shrink-0"></span>
                        <span class="font-bold text-emerald-950 truncate text-xs sm:text-sm">
                            <?= htmlspecialchars($activeCat['name'] ?? $bannerTitle) ?>
                        </span>
                        <?php if (!empty($activeSubCatName)): ?>
                            <span class="text-gray-400 font-bold">›</span>
                            <span class="font-bold text-emerald-800 truncate"><?= htmlspecialchars($activeSubCatName) ?></span>
                        <?php endif; ?>
                        <span class="text-[10px] font-bold bg-white text-emerald-800 px-2 py-0.5 rounded-full border border-emerald-200 shrink-0">
                            <?= count($products) ?> <?= $locale === 'bn' ? 'টি পণ্য' : 'items' ?>
                        </span>
                    </div>
                    <a href="<?= $base ?>/<?= $variantQueryFirst ?>" class="text-xs font-bold text-red-600 hover:text-red-700 flex items-center gap-1 shrink-0 ml-2 hover:bg-red-50 px-2.5 py-1 rounded-lg transition-colors border border-red-200/60">
                        <span>✕</span>
                        <span><?= $locale === 'bn' ? 'মুছুন' : 'Clear' ?></span>
                    </a>
                </div>
            <?php endif; ?>

            <!-- 5. Spacious Full-Width Product Grid -->
            <div id="products">
                <?php if (empty($products)): ?>
                    <div class="bg-white rounded-3xl p-10 text-center border border-gray-100 shadow-xs max-w-md mx-auto my-6">
                        <div class="w-16 h-16 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-3 text-3xl">🛒</div>
                        <h3 class="text-base font-bold text-gray-800"><?= $__('products_empty_title') ?></h3>
                        <p class="text-xs text-gray-400 mt-1"><?= $__('products_empty_subtitle') ?></p>
                        <a href="<?= $base ?>/<?= $variantQueryFirst ?>" class="inline-block mt-4 px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 transition-colors">
                            <?= $locale === 'bn' ? 'সব পণ্য দেখুন' : 'View All Products' ?>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-3 sm:gap-4" id="products-catalog-grid">
                        <?php foreach ($products as $pIdx => $product): ?>
                            <?php renderStorefrontProductCard($product, $pIdx, $isFiltered, $base, $locale, $__, false); ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </section>
    </div>
</main>

<?php elseif ($homeVariant === 'variant_2'): ?>
<!-- =========================================================================
     VARIANT 2: SMART SPLIT APP RAIL STYLE (Blinkit / Instamart Experience)
     Refined, slim left category rail + compact promo bar + optimized split cards
     ========================================================================= -->
<main class="w-full max-w-[1720px] mx-auto px-2 sm:px-4 py-2.5 sm:py-4">
    <div class="flex items-start gap-2 sm:gap-3.5 md:gap-4">

        <!-- Slim, Proportioned Left Vertical Category Rail -->
        <aside class="w-[68px] sm:w-[86px] md:w-[98px] flex-shrink-0 sticky top-14 md:top-20 z-20 self-start">
            <div class="bg-white/95 backdrop-blur-md border border-gray-200/90 rounded-2xl p-1 sm:p-1.5 flex flex-col items-center shadow-xs max-h-[calc(100vh-4.5rem)] overflow-y-auto no-scrollbar">
                <a href="<?= $base ?>/<?= $variantQueryFirst ?>" class="text-[8.5px] sm:text-[10px] font-black text-emerald-800 hover:text-emerald-600 text-center uppercase tracking-tight mb-1.5 pb-1 border-b border-gray-100 w-full block">
                    <?= $locale === 'bn' ? 'ক্যাটাগরি' : 'Categories' ?>
                </a>
                <div class="flex flex-col items-center gap-2 w-full py-0.5">
                    <?php foreach ($railCategories as $rc): ?>
                        <?php
                            $isRcActive = false;
                            if (!empty($activeCat)) {
                                if ((int)$activeCat['id'] === (int)$rc['id'] || (!empty($parentCategory) && (int)$parentCategory['id'] === (int)$rc['id'])) {
                                    $isRcActive = true;
                                }
                            }
                            $vis = getCategoryVisual($rc, $base);
                        ?>
                        <a href="<?= $base ?>/?category=<?= $rc['id'] ?><?= $variantQuerySuffix ?>" 
                           class="flex flex-col items-center group text-center w-full transition-transform active:scale-95 relative"
                           title="<?= htmlspecialchars($rc['name']) ?>">
                            <?php if ($isRcActive): ?>
                                <span class="absolute left-0 top-2 bottom-4 w-1 bg-emerald-600 rounded-r-full"></span>
                            <?php endif; ?>
                            <div class="w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16 rounded-2xl flex items-center justify-center transition-all duration-200 relative overflow-hidden bg-white border-2 <?= $isRcActive ? 'border-emerald-600 shadow-sm bg-emerald-50/50' : 'border-gray-100 group-hover:border-emerald-300' ?>">
                                <?php if ($vis['type'] === 'image'): ?>
                                    <img src="<?= htmlspecialchars($vis['val']) ?>" alt="<?= htmlspecialchars($rc['name']) ?>" class="w-full h-full object-contain p-1" loading="lazy" onerror="this.onerror=null; this.src='<?= $base ?>/images/default-category.svg';">
                                <?php else: ?>
                                    <span class="text-xl sm:text-2xl select-none leading-none"><?= $vis['val'] ?></span>
                                <?php endif; ?>
                            </div>
                            <span class="text-[9.5px] sm:text-[11px] font-bold mt-1 leading-tight text-center line-clamp-2 px-0.5 <?= $isRcActive ? 'text-emerald-900 font-black' : 'text-gray-600' ?>">
                                <?= htmlspecialchars($rc['name']) ?>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </aside>

        <!-- Right Split Content Column -->
        <section class="flex-1 min-w-0">

            <!-- Compact App Promo Banner (Saves vertical space so products appear higher) -->
            <div class="bg-gradient-to-r from-emerald-900 via-emerald-800 to-teal-900 rounded-2xl p-3 sm:p-5 text-white shadow-md relative overflow-hidden mb-2.5 sm:mb-3.5 border border-emerald-700/40">
                <div class="relative z-10 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5 sm:gap-4 min-w-0">
                        <div class="w-12 h-12 sm:w-16 sm:h-16 flex-shrink-0 rounded-xl bg-white/10 p-1 border border-white/15">
                            <img src="<?= $base ?>/images/grocery_cart_hero.jpg" alt="Grocery" class="w-full h-full object-contain rounded-lg">
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5 flex-wrap mb-0.5">
                                <span class="bg-amber-400 text-slate-950 text-[9px] sm:text-[10px] font-black px-2 py-0.5 rounded-full">⚡ <?= $locale === 'bn' ? 'সুপার ফাস্ট বাজার' : 'Super Fast Market' ?></span>
                                <button type="button" onclick="navigator.clipboard.writeText('SODAI50'); showToast('কুপন SODAI50 কপি হয়েছে!', 'success')" class="bg-white/15 hover:bg-white/25 text-white text-[9px] sm:text-[10px] font-bold px-2 py-0.5 rounded-full border border-white/20 cursor-pointer">
                                    🎁 SODAI50
                                </button>
                            </div>
                            <h1 class="text-sm sm:text-xl md:text-2xl font-black text-white truncate">
                                <?= htmlspecialchars($bannerTitle) ?>
                            </h1>
                            <p class="text-[10px] sm:text-xs text-emerald-100/85 truncate max-w-md">
                                <?= htmlspecialchars($bannerSubtitle) ?>
                            </p>
                        </div>
                    </div>
                    <a href="<?= $base ?>/?deals=1<?= $variantQuerySuffix ?>" class="hidden sm:inline-flex items-center gap-1 bg-amber-400 hover:bg-amber-300 text-slate-950 font-black text-xs px-3.5 py-2 rounded-xl shrink-0 shadow-xs">
                        <span>🔥 অফার দেখুন</span>
                    </a>
                </div>
            </div>

            <!-- Single-Line Scrollable Trust Strip -->
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar mb-2.5">
                <div class="flex items-center gap-1.5 bg-white border border-gray-100 rounded-xl px-2.5 py-1.5 shrink-0 shadow-2xs">
                    <span class="text-xs">⚡</span>
                    <span class="text-[10px] sm:text-[11px] font-bold text-gray-700"><?= $locale === 'bn' ? 'দ্রুত ডেলিভারি' : 'Fast Delivery' ?></span>
                </div>
                <div class="flex items-center gap-1.5 bg-white border border-gray-100 rounded-xl px-2.5 py-1.5 shrink-0 shadow-2xs">
                    <span class="text-xs">🥦</span>
                    <span class="text-[10px] sm:text-[11px] font-bold text-gray-700"><?= $locale === 'bn' ? '১০০% তাজা পণ্য' : '100% Fresh' ?></span>
                </div>
                <div class="flex items-center gap-1.5 bg-white border border-gray-100 rounded-xl px-2.5 py-1.5 shrink-0 shadow-2xs">
                    <span class="text-xs">💵</span>
                    <span class="text-[10px] sm:text-[11px] font-bold text-gray-700"><?= $locale === 'bn' ? 'ক্যাশ অন ডেলিভারি' : 'Cash on Delivery' ?></span>
                </div>
                <div class="flex items-center gap-1.5 bg-white border border-gray-100 rounded-xl px-2.5 py-1.5 shrink-0 shadow-2xs">
                    <span class="text-xs">🔄</span>
                    <span class="text-[10px] sm:text-[11px] font-bold text-gray-700"><?= $locale === 'bn' ? 'সহজ রিটার্ন' : 'Easy Return' ?></span>
                </div>
            </div>

            <!-- Subcategory Filter Pills -->
            <div class="mb-3">
                <div class="flex items-center gap-1.5 sm:gap-2 overflow-x-auto no-scrollbar py-0.5">
                    <?php 
                        $isAllActive = empty($activeSubId) && (!$isFiltered || empty($activeCat));
                        $allPillUrl = !empty($activeCat) ? ($base . '/?category=' . $activeCat['id'] . $variantQuerySuffix) : ($base . '/' . $variantQueryFirst);
                    ?>
                    <a href="<?= $allPillUrl ?>" 
                       class="flex-shrink-0 px-3 py-1.5 rounded-full text-[11px] sm:text-xs font-bold transition-all flex items-center gap-1.5 <?= $isAllActive ? 'bg-emerald-700 text-white shadow-sm' : 'bg-white text-gray-700 border border-gray-200' ?>">
                        <span>🛒</span>
                        <span><?= $locale === 'bn' ? 'সবগুলো' : 'All' ?></span>
                    </a>
                    <?php foreach ($subs as $sc): ?>
                        <?php 
                            $hasChildCats = !empty($childrenMap[$sc['id']]);
                            if (!$isFiltered) {
                                $isScActive = false;
                                $scUrl = $base . '/?category=' . $sc['id'] . $variantQuerySuffix;
                            } else {
                                $isScActive = ($activeSubId && (int)$activeSubId === (int)$sc['id']);
                                $scUrl = $hasChildCats ? ($base . '/?category=' . $sc['id'] . $variantQuerySuffix) : ($base . '/?category=' . ($activeCat['id'] ?? '') . '&sub=' . $sc['id'] . $variantQuerySuffix);
                            }
                        ?>
                        <a href="<?= $scUrl ?>" 
                           class="flex-shrink-0 px-3 py-1.5 rounded-full text-[11px] sm:text-xs transition-all flex items-center gap-1.5 whitespace-nowrap <?= $isScActive ? 'bg-emerald-700 text-white font-black shadow-sm' : 'bg-white hover:bg-emerald-50 text-gray-700 border border-gray-200 font-semibold' ?>">
                            <span><?= htmlspecialchars($sc['name']) ?></span>
                            <?php if (!empty($sc['total_product_count'])): ?>
                                <span class="text-[9px] px-1.5 py-0.5 rounded-full <?= $isScActive ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500' ?>"><?= $sc['total_product_count'] ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Products Grid (Compact Mode for Split Rail) -->
            <div id="products">
                <?php if (empty($products)): ?>
                    <div class="bg-white rounded-2xl p-8 text-center border border-gray-100 shadow-xs my-4">
                        <div class="w-14 h-14 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-2 text-2xl">🛒</div>
                        <h3 class="text-sm font-bold text-gray-800"><?= $__('products_empty_title') ?></h3>
                        <a href="<?= $base ?>/<?= $variantQueryFirst ?>" class="inline-block mt-3 px-4 py-1.5 rounded-xl bg-emerald-600 text-white text-xs font-bold"><?= $locale === 'bn' ? 'সব পণ্য দেখুন' : 'View All' ?></a>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-2 sm:gap-3.5" id="products-catalog-grid">
                        <?php foreach ($products as $pIdx => $product): ?>
                            <?php renderStorefrontProductCard($product, $pIdx, $isFiltered, $base, $locale, $__, true); ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </section>
    </div>
</main>

<?php else: ?>
<!-- =========================================================================
     VARIANT 3: SUPERSTORE MAGAZINE & FLASH SHOWCASE (Shwapno / MeenaClick Pro)
     Mobile-First Compact Hero + 4-Col App Category Grid + Swipeable Flash Deals + Sticky Filter Catalog
     ========================================================================= -->
<?php
    $dealProducts = array_values(array_filter($products, function($p) {
        return \Models\Product::hasDiscount($p) || !empty($p['special_badge']) || intval($p['demand_percentage'] ?? 0) >= 30;
    }));
    $spotlightDeals = array_slice(!empty($dealProducts) ? $dealProducts : $products, 0, 6);
?>
<main class="w-full max-w-[1640px] mx-auto px-2.5 sm:px-5 py-2.5 sm:py-5 space-y-3.5 sm:space-y-6">

    <!-- 1. COMPACT MOBILE-FIRST BENTO HERO & PROMO STRIP -->
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-2.5 sm:gap-4">
        
        <!-- Main Hero Banner (Side-by-side on mobile & desktop — no wasted vertical empty space!) -->
        <div class="lg:col-span-8 bg-gradient-to-br from-emerald-950 via-emerald-800 to-teal-900 rounded-2xl sm:rounded-3xl p-3.5 sm:p-6 lg:p-7 text-white shadow-lg relative overflow-hidden flex items-center border border-emerald-700/40">
            <div class="absolute -right-8 -bottom-8 w-44 h-44 sm:w-64 sm:h-64 bg-amber-400/15 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute left-1/3 -top-10 w-36 h-36 bg-teal-400/10 rounded-full blur-xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-row items-center justify-between gap-3 sm:gap-6 w-full">
                <!-- Left Text & CTA -->
                <div class="flex-1 min-w-0 space-y-1.5 sm:space-y-2.5">
                    <div class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-400 text-slate-950 text-[10px] sm:text-[11px] font-black shadow-2xs">
                        <span>🌟</span>
                        <span><?= $locale === 'bn' ? 'সুপারস্টোর স্পেশাল' : 'Superstore Special' ?></span>
                    </div>

                    <h1 class="text-base sm:text-2xl md:text-3xl lg:text-4xl font-black tracking-tight leading-snug sm:leading-tight text-white">
                        <?= htmlspecialchars($bannerTitle) ?>
                    </h1>

                    <p class="text-[11px] sm:text-sm text-emerald-100/90 leading-snug line-clamp-2 max-w-xl">
                        <?= htmlspecialchars($bannerSubtitle) ?>
                    </p>

                    <div class="pt-1 flex items-center gap-1.5 sm:gap-2.5 flex-wrap">
                        <a href="#products" class="inline-flex items-center gap-1 bg-emerald-500 hover:bg-emerald-400 text-white font-black text-[11px] sm:text-xs px-3 sm:px-4 py-1.5 sm:py-2 rounded-xl shadow-sm transition-all active:scale-95">
                            <span>🛒 <?= $locale === 'bn' ? 'এখনই বাজার করুন' : 'Shop Now' ?></span>
                        </a>
                        <button type="button" onclick="navigator.clipboard.writeText('SODAI50'); showToast('কুপন SODAI50 কপি করা হয়েছে!', 'success')" class="inline-flex items-center gap-1 bg-white/15 hover:bg-white/25 text-white font-bold text-[10px] sm:text-xs px-2.5 sm:px-3.5 py-1.5 sm:py-2 rounded-xl border border-white/20 cursor-pointer active:scale-95">
                            <span>🎁 SODAI50</span>
                        </button>
                    </div>
                </div>

                <!-- Right Compact 3D Grocery Illustration -->
                <div class="w-20 h-20 sm:w-28 sm:h-28 md:w-32 md:h-32 flex-shrink-0 bg-white/10 backdrop-blur-xs rounded-2xl sm:rounded-3xl p-1.5 sm:p-2 border border-white/20 shadow-inner relative">
                    <span class="absolute -top-1.5 -right-1.5 bg-rose-500 text-white text-[8px] sm:text-[9px] font-black px-1.5 py-0.5 rounded-full shadow-xs">🔥 সেরা ডিল</span>
                    <img src="<?= $base ?>/images/grocery_cart_hero.jpg" alt="Superstore Basket" class="w-full h-full object-contain rounded-xl sm:rounded-2xl">
                </div>
            </div>
        </div>

        <!-- Right Side Compact Dual Promo Cards (Sleek horizontal pills on mobile, stacked Bento on desktop) -->
        <div class="lg:col-span-4 grid grid-cols-2 lg:grid-cols-1 gap-2 sm:gap-3.5">
            <!-- Promo Card 1: Flash Deals -->
            <a href="<?= $base ?>/?deals=1<?= $variantQuerySuffix ?>" class="bg-gradient-to-br from-amber-500 via-orange-500 to-rose-600 rounded-2xl sm:rounded-3xl p-2.5 sm:p-4 text-white shadow-sm hover:shadow-md transition-all flex items-center justify-between gap-2 group relative overflow-hidden">
                <div class="min-w-0 relative z-10">
                    <span class="inline-block bg-white/25 text-white text-[9px] sm:text-[10px] font-black px-2 py-0.5 rounded-full mb-1">⚡ সীমিত অফার</span>
                    <h3 class="text-xs sm:text-base font-black leading-tight truncate">মেগা ফ্ল্যাশ ডিলস</h3>
                    <span class="inline-flex items-center gap-0.5 text-[10px] sm:text-xs font-extrabold text-amber-100 mt-1 group-hover:translate-x-0.5 transition-transform">
                        <span>ডিল দেখুন</span>
                        <span>→</span>
                    </span>
                </div>
                <div class="w-9 h-9 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-white/20 backdrop-blur-xs flex items-center justify-center text-lg sm:text-2xl shrink-0 shadow-inner">
                    🔥
                </div>
            </a>

            <!-- Promo Card 2: Express Delivery & Fresh Guarantee -->
            <div class="bg-gradient-to-br from-teal-900 via-emerald-950 to-slate-900 rounded-2xl sm:rounded-3xl p-2.5 sm:p-4 text-white shadow-sm flex items-center justify-between gap-2 border border-teal-700/40">
                <div class="min-w-0">
                    <span class="inline-block bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[9px] sm:text-[10px] font-black px-2 py-0.5 rounded-full mb-1">🚚 হোম ডেলিভারি</span>
                    <h3 class="text-xs sm:text-base font-black leading-tight truncate">
                        <?= $expressDeliveryEnabled ? "{$expressDeliveryDisplay} ডেলিভারি" : "১০০% তাজা বাজার" ?>
                    </h3>
                    <span class="block text-[10px] sm:text-xs font-bold text-emerald-300 mt-1 truncate">✓ ক্যাশ অন ডেলিভারি</span>
                </div>
                <div class="w-9 h-9 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-emerald-500/15 border border-emerald-500/25 flex items-center justify-center text-lg sm:text-2xl shrink-0">
                    🥦
                </div>
            </div>
        </div>
    </section>

    <!-- 2. COMPACT 4-COLUMN APP CATEGORY GRID ("ক্যাটাগরি অনুযায়ী বাজার করুন") -->
    <section id="categories" class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-xs p-3 sm:p-5">
        <div class="flex items-center justify-between mb-2.5 sm:mb-4">
            <div class="flex items-center gap-2">
                <span class="w-2 h-4 rounded-full bg-emerald-600"></span>
                <h2 class="text-sm sm:text-lg font-black text-gray-900 leading-none">
                    <?= $locale === 'bn' ? 'ক্যাটাগরি অনুযায়ী বাজার করুন' : 'Shop by Category' ?>
                </h2>
            </div>
            <?php if ($isFiltered): ?>
                <a href="<?= $base ?>/<?= $variantQueryFirst ?>" class="text-[11px] font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-2.5 py-1 rounded-lg transition-colors">
                    <?= $locale === 'bn' ? 'সব দেখুন' : 'Reset' ?>
                </a>
            <?php else: ?>
                <a href="<?= $base ?>/category" class="text-[11px] font-bold text-emerald-700 hover:underline">
                    <?= $locale === 'bn' ? 'সব ক্যাটাগরি →' : 'View All →' ?>
                </a>
            <?php endif; ?>
        </div>

        <!-- 4 Columns on Mobile (Clean App Squircle Grid), 6-11 Columns on Desktop -->
        <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-11 gap-2 sm:gap-3">
            <?php foreach ($railCategories as $rc): ?>
                <?php
                    $isRcActive = !empty($activeCat) && ((int)$activeCat['id'] === (int)$rc['id'] || (!empty($parentCategory) && (int)$parentCategory['id'] === (int)$rc['id']));
                    $vis = getCategoryVisual($rc, $base);
                    $catBadge = getCategoryPromoBadge($rc['name'] ?? '');
                ?>
                <a href="<?= $base ?>/?category=<?= $rc['id'] ?><?= $variantQuerySuffix ?>" 
                   class="group flex flex-col items-center text-center transition-transform active:scale-95">
                    <div class="w-full aspect-square max-w-[68px] sm:max-w-[74px] rounded-2xl flex items-center justify-center p-1.5 relative overflow-hidden transition-all duration-200 <?= $isRcActive ? 'bg-emerald-50 border-2 border-emerald-600 shadow-sm ring-2 ring-emerald-500/20' : 'bg-gradient-to-b from-gray-50 to-white border border-gray-200/80 group-hover:border-emerald-400 group-hover:shadow-xs' ?>">
                        <?php if ($catBadge): ?>
                            <span class="absolute top-0.5 right-0.5 <?= $catBadge['cls'] ?> font-black text-[6.5px] sm:text-[7px] px-1 py-0.5 rounded-full leading-none z-10 shadow-2xs">
                                <?= $catBadge['text'] ?>
                            </span>
                        <?php endif; ?>
                        <?php if ($vis['type'] === 'image'): ?>
                            <img src="<?= htmlspecialchars($vis['val']) ?>" alt="<?= htmlspecialchars($rc['name']) ?>" class="w-full h-full object-contain group-hover:scale-105 transition-transform" loading="lazy" onerror="this.onerror=null; this.src='<?= $base ?>/images/default-category.svg';">
                        <?php else: ?>
                            <span class="text-2xl"><?= $vis['val'] ?></span>
                        <?php endif; ?>
                    </div>
                    <span class="text-[10px] sm:text-[11px] font-bold mt-1 leading-tight line-clamp-1 w-full px-0.5 <?= $isRcActive ? 'text-emerald-800 font-black' : 'text-gray-700 group-hover:text-emerald-700' ?>">
                        <?= htmlspecialchars($rc['name']) ?>
                    </span>
                </a>
            <?php endforeach; ?>

            <!-- 12th Symmetrical Tile on Mobile when 11 categories exist: Flash Deals Tile -->
            <?php if (count($railCategories) % 4 === 3): ?>
                <a href="<?= $base ?>/?deals=1<?= $variantQuerySuffix ?>" class="sm:hidden group flex flex-col items-center text-center transition-transform active:scale-95">
                    <div class="w-full aspect-square max-w-[68px] rounded-2xl flex items-center justify-center p-1.5 relative overflow-hidden bg-gradient-to-br from-amber-50 via-orange-50 to-rose-50 border border-amber-300/80 shadow-2xs">
                        <span class="absolute top-0.5 right-0.5 bg-rose-600 text-white font-black text-[6.5px] px-1 py-0.5 rounded-full leading-none">HOT</span>
                        <span class="text-2xl">⚡</span>
                    </div>
                    <span class="text-[10px] font-black mt-1 leading-tight line-clamp-1 text-rose-700">
                        <?= $locale === 'bn' ? 'মেগা অফার' : 'Mega Deals' ?>
                    </span>
                </a>
            <?php endif; ?>
        </div>
    </section>

    <!-- 3. HORIZONTAL SWIPEABLE FLASH DEALS & SPOTLIGHT (Single compact row on mobile!) -->
    <?php if (!$isFiltered && count($spotlightDeals) >= 3): ?>
    <section class="bg-gradient-to-r from-amber-50/90 via-orange-50/50 to-emerald-50/60 rounded-2xl sm:rounded-3xl border border-amber-200/80 p-3 sm:p-5 shadow-xs">
        <div class="flex items-center justify-between gap-2 mb-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-gradient-to-br from-amber-500 to-rose-600 text-white flex items-center justify-center text-sm sm:text-base shadow-xs shrink-0">🔥</div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <h2 class="text-sm sm:text-lg font-black text-gray-900 leading-none">
                            <?= $locale === 'bn' ? 'ফ্ল্যাশ ডিলস ও সেরা অফার' : 'Flash Deals & Spotlight' ?>
                        </h2>
                        <span class="hidden sm:inline-block bg-rose-600 text-white text-[9px] font-black px-2 py-0.5 rounded-full animate-pulse">LIVE</span>
                    </div>
                    <p class="text-[10px] sm:text-xs text-gray-500 mt-0.5"><?= $locale === 'bn' ? 'সোয়াইপ করে দেখুন আজকের সেরা ছাড়সমূহ' : 'Swipe to explore top deals' ?></p>
                </div>
            </div>
            <a href="<?= $base ?>/?deals=1<?= $variantQuerySuffix ?>" class="inline-flex items-center gap-1 text-[11px] sm:text-xs font-black text-rose-600 bg-white hover:bg-rose-50 border border-rose-200 px-2.5 sm:px-3.5 py-1 sm:py-1.5 rounded-xl shadow-2xs transition-colors shrink-0">
                <span>সব ডিল →</span>
            </a>
        </div>

        <!-- Horizontal Snap Carousel on Mobile, 6-Col Grid on Desktop -->
        <div class="flex lg:grid lg:grid-cols-6 gap-2.5 sm:gap-3.5 overflow-x-auto no-scrollbar snap-x snap-mandatory pb-1">
            <?php foreach ($spotlightDeals as $sIdx => $sProd): ?>
                <div class="w-[158px] sm:w-[190px] lg:w-auto flex-shrink-0 snap-start">
                    <?php renderStorefrontProductCard($sProd, $sIdx + 100, true, $base, $locale, $__, true); ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- 4. STICKY FILTER BAR & ALL PRODUCTS CATALOG -->
    <section id="products">
        <!-- Sticky Category Filter Pills Header -->
        <div class="sticky top-[48px] md:top-[68px] z-30 bg-gray-50/95 backdrop-blur-md py-2 -mx-2.5 px-2.5 sm:mx-0 sm:px-0 mb-3 border-b border-gray-200/60 sm:border-none">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm sm:text-xl font-black text-gray-900 flex items-center gap-1.5">
                        <span class="w-2 h-4 rounded-full bg-emerald-600"></span>
                        <span><?= htmlspecialchars($isFiltered ? ($activeCat['name'] ?? $bannerTitle) : ($locale === 'bn' ? 'সকল নিত্যপ্রয়োজনীয় পণ্যসমূহ' : 'All Daily Essentials')) ?></span>
                        <span class="text-[10px] sm:text-xs font-bold bg-emerald-100/80 text-emerald-800 px-2 py-0.5 rounded-full">
                            <?= count($products) ?>
                        </span>
                    </h2>
                    <?php if ($isFiltered): ?>
                        <a href="<?= $base ?>/<?= $variantQueryFirst ?>" class="sm:hidden text-[11px] font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded-lg border border-red-200">
                            ✕ মুছুন
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Scrollable Quick Filter Pills -->
                <div class="flex items-center gap-1.5 sm:gap-2 overflow-x-auto no-scrollbar py-0.5">
                    <?php 
                        $isAllActive = empty($activeSubId) && (!$isFiltered || empty($activeCat));
                        $allPillUrl = !empty($activeCat) ? ($base . '/?category=' . $activeCat['id'] . $variantQuerySuffix) : ($base . '/' . $variantQueryFirst);
                    ?>
                    <a href="<?= $allPillUrl ?>" class="flex-shrink-0 px-3 py-1 rounded-full text-[11px] sm:text-xs font-bold transition-all <?= $isAllActive ? 'bg-emerald-700 text-white shadow-xs' : 'bg-white text-gray-700 border border-gray-200/90' ?>">
                        🛒 <?= $locale === 'bn' ? 'সবগুলো' : 'All' ?>
                    </a>
                    <?php foreach ($subs as $sc): ?>
                        <?php 
                            $hasChildCats = !empty($childrenMap[$sc['id']]);
                            if (!$isFiltered) {
                                $isScActive = false;
                                $scUrl = $base . '/?category=' . $sc['id'] . $variantQuerySuffix;
                            } else {
                                $isScActive = ($activeSubId && (int)$activeSubId === (int)$sc['id']);
                                $scUrl = $hasChildCats ? ($base . '/?category=' . $sc['id'] . $variantQuerySuffix) : ($base . '/?category=' . ($activeCat['id'] ?? '') . '&sub=' . $sc['id'] . $variantQuerySuffix);
                            }
                        ?>
                        <a href="<?= $scUrl ?>" class="flex-shrink-0 px-3 py-1 rounded-full text-[11px] sm:text-xs font-semibold transition-all whitespace-nowrap <?= $isScActive ? 'bg-emerald-700 text-white font-black shadow-xs' : 'bg-white hover:bg-emerald-50 text-gray-700 border border-gray-200/90' ?>">
                            <?= htmlspecialchars($sc['name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <?php if (empty($products)): ?>
            <div class="bg-white rounded-3xl p-10 text-center border border-gray-100 shadow-xs max-w-md mx-auto my-6">
                <div class="w-16 h-16 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-3 text-3xl">🛒</div>
                <h3 class="text-base font-bold text-gray-800"><?= $__('products_empty_title') ?></h3>
                <a href="<?= $base ?>/<?= $variantQueryFirst ?>" class="inline-block mt-4 px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">
                    <?= $locale === 'bn' ? 'সব পণ্য দেখুন' : 'View All Products' ?>
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-2.5 sm:gap-4" id="products-catalog-grid">
                <?php foreach ($products as $pIdx => $product): ?>
                    <?php renderStorefrontProductCard($product, $pIdx, $isFiltered, $base, $locale, $__, false); ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

</main>
<?php endif; ?>

<?php 
$content = ob_get_clean();
require 'layout.php';
?>
