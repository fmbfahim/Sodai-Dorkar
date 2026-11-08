<?php
// views/admin/orders/edit.php
// Full Mobile & Desktop Responsive Order Editor with Catch-Weight & Multi-Variant Support

$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
$existingDiscount = floatval($order['delivery_discount'] ?? 0);
$existingDelivery = floatval($order['delivery_charge'] ?? 0);
$orderId = intval($order['id']);
$orderTotal = floatval($order['total_amount'] ?? 0);
$posAllowCustomPrice = (\Models\Setting::getValue('pos_allow_custom_price', '0') === '1');

if (!function_exists('getPosProductVariants')) {
    function getPosProductVariants($product) {
        if (!empty($product['unit_variants_json'])) {
            $decoded = json_decode($product['unit_variants_json'], true);
            if (!empty($decoded) && is_array($decoded)) {
                $variants = [];
                $defaultTitle = '';
                $defaultPrice = floatval($product['sell_price']);
                $defaultQty = 1.000;

                foreach ($decoded as $v) {
                    $item = [
                        'title' => $v['title'],
                        'qty' => floatval($v['qty'] ?? 1.000),
                        'price' => floatval($v['price']),
                        'is_default' => !empty($v['is_default'])
                    ];
                    $variants[] = $item;
                    if (!empty($v['is_default']) && empty($defaultTitle)) {
                        $defaultTitle = $v['title'];
                        $defaultPrice = floatval($v['price']);
                        $defaultQty = floatval($v['qty'] ?? 1.000);
                    }
                }

                if (empty($defaultTitle) && !empty($variants)) {
                    $defaultTitle = $variants[0]['title'];
                    $defaultPrice = floatval($variants[0]['price']);
                    $defaultQty = floatval($variants[0]['qty'] ?? 1.000);
                }

                return [
                    'has_custom' => true,
                    'default_title' => $defaultTitle,
                    'default_price' => $defaultPrice,
                    'default_qty' => $defaultQty,
                    'variants' => $variants
                ];
            }
        }

        // Default base unit
        $baseUnit = $product['base_unit'] ?? 'pcs';
        return [
            'has_custom' => false,
            'default_title' => '1 ' . $baseUnit,
            'default_price' => floatval($product['sell_price']),
            'default_qty' => 1.000,
            'variants' => [
                [
                    'title' => '1 ' . $baseUnit,
                    'qty' => 1.000,
                    'price' => floatval($product['sell_price']),
                    'is_default' => true
                ]
            ]
        ];
    }
}
?>

<!-- Mobile Mode Switcher Tabs (Only visible on screens < lg) -->
<div class="pos-mobile-bar flex lg:hidden items-center bg-white p-1 rounded-2xl border border-secondary-200 shadow-2xs mb-2.5 shrink-0 gap-1.5 select-none sticky top-0 z-30">
    <button type="button" id="tabBtnProducts" onclick="switchToMobileTab('products')" 
            class="flex-1 py-2.5 px-3 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition-all text-secondary-600 hover:bg-secondary-100 cursor-pointer">
        <ion-icon name="grid-outline" class="text-base"></ion-icon>
        <span>➕ পণ্য যোগ করুন</span>
    </button>
    <button type="button" id="tabBtnCart" onclick="switchToMobileTab('cart')" 
            class="flex-1 py-2.5 px-3 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition-all bg-emerald-600 text-white shadow-xs cursor-pointer">
        <ion-icon name="cart-outline" class="text-base"></ion-icon>
        <span>অর্ডার (#<?= $orderId ?>)</span>
        <span id="mobileTabBadge" class="px-2 py-0.5 text-[10px] font-black rounded-full bg-white text-emerald-700 leading-none shadow-2xs"><?= count($order['items'] ?? []) ?></span>
        <span id="mobileTabPrice" class="text-[11px] font-black text-white ml-0.5">৳ <?= number_format($orderTotal) ?></span>
    </button>
</div>

<div class="h-[calc(100dvh-150px)] sm:h-[calc(100vh-150px)] lg:h-[calc(100vh-140px)] flex flex-col lg:flex-row gap-3 min-w-0 w-full overflow-hidden relative">
    
    <!-- LEFT PANEL: Products Catalog & Search -->
    <div id="posProductsPanel" class="pos-panel-inactive lg:flex flex-1 min-w-0 flex flex-col bg-white rounded-2xl shadow-sm border border-secondary-200 overflow-hidden h-full relative">
        
        <!-- Search & Category Header -->
        <div class="p-2.5 sm:p-3 border-b border-secondary-100 flex flex-col gap-2 bg-white shrink-0 min-w-0">
            <!-- Search Row -->
            <div class="flex items-center gap-2">
                <div class="relative flex-1 min-w-0">
                    <input type="text" id="productSearch" 
                           placeholder="Search product by name, SKU or code..." 
                           class="w-full pl-8 sm:pl-9 pr-3 sm:pr-4 py-2 bg-secondary-50 border border-secondary-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all shadow-2xs"
                           autocomplete="off">
                    <div class="absolute inset-y-0 left-0 pl-2.5 sm:pl-3 flex items-center pointer-events-none text-secondary-400">
                        <ion-icon name="search-outline" class="text-sm sm:text-base"></ion-icon>
                    </div>
                </div>
                <button type="button" onclick="clearSearch()" class="px-3 py-2 text-xs font-bold text-secondary-600 hover:text-secondary-900 bg-secondary-100 hover:bg-secondary-200 rounded-xl transition-colors shrink-0 cursor-pointer">
                    Reset
                </button>
            </div>

            <!-- Visual Circular Category Icons Bar -->
            <div class="flex gap-2 sm:gap-3 overflow-x-auto pb-1 pt-0.5 scrollbar-hide select-none w-full min-w-0" id="categoryFilter"></div>

            <!-- Active Selected Category Header Bar -->
            <div id="activeCategoryHeader" class="hidden px-2.5 py-1.5 rounded-xl bg-primary-50/90 border border-primary-200/90 items-center justify-between text-xs transition-all shadow-2xs">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="w-2 h-2 rounded-full bg-primary-600 animate-pulse shrink-0"></span>
                    <span class="text-secondary-500 font-medium shrink-0">ফিল্টার মেনু:</span>
                    <span id="activeCategoryTitle" class="font-bold text-primary-800 truncate"></span>
                </div>
                <button type="button" onclick="clearCategoryFilter()" class="text-xs font-bold text-red-600 hover:text-red-700 flex items-center gap-1 cursor-pointer shrink-0 ml-2 hover:bg-red-50 px-2 py-0.5 rounded-md transition-colors" title="সব পণ্য দেখুন">
                    <ion-icon name="close-circle" class="text-sm"></ion-icon>
                    <span>মুছুন</span>
                </button>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div class="flex-1 min-w-0 overflow-y-auto p-2 sm:p-3 bg-secondary-50/70 pb-28 lg:pb-4">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-2.5 sm:gap-3" id="productsGrid">
                <?php if (!empty($products)): foreach ($products as $p): ?>
                    <?php 
                        $vInfo = getPosProductVariants($p);
                        $hasDiscount = \Models\Product::hasDiscount($p);
                        $discountPercent = $hasDiscount ? \Models\Product::getDiscountPercent($p) : 0;
                        $stockNum = floatval($p['stock_qty']);
                        $stockClean = (floor($stockNum) == $stockNum) ? intval($stockNum) : rtrim(rtrim(number_format($stockNum, 3), '0'), '.');
                        $isOutOfStock = ($stockNum <= 0 || ($p['availability_status'] ?? '') === 'out_of_stock');
                        $baseUnit = $p['base_unit'] ?? 'pcs';
                        $posImg = !empty($p['image_path']) ? htmlspecialchars($p['image_path']) : $base . '/images/default-product.svg';
                        $isWeightProduct = ($baseUnit === 'kg' || $baseUnit === 'gm' || ($p['unit_type'] ?? '') === 'weight');
                    ?>
                    <div class="pos-product-card bg-white p-2 sm:p-2.5 rounded-2xl shadow-xs border <?= $isOutOfStock ? 'border-red-200 hover:border-red-400' : 'border-secondary-200 hover:border-primary-500' ?> hover:shadow-md transition-all flex flex-col h-full group relative min-w-0"
                         data-product-id="<?= $p['id'] ?>"
                         data-category="<?= $p['category_id'] ?? '' ?>"
                         data-name="<?= htmlspecialchars(strtolower($p['name'])) ?>"
                         data-sku="<?= htmlspecialchars(strtolower($p['sku'] ?? '')) ?>"
                         data-stock="<?= $stockNum ?>"
                         data-is-out-of-stock="<?= $isOutOfStock ? '1' : '0' ?>"
                         data-base-unit="<?= htmlspecialchars($baseUnit) ?>"
                         data-regular-price="<?= floatval($p['regular_price'] ?? 0) ?>"
                         data-active-variant-title="<?= htmlspecialchars($vInfo['default_title']) ?>"
                         data-active-variant-price="<?= $vInfo['default_price'] ?>"
                         data-active-variant-qty="<?= $vInfo['default_qty'] ?>"
                         data-image="<?= $posImg ?>">
                         
                        <!-- Top Badges -->
                        <div class="flex items-center justify-between gap-1 mb-1 sm:mb-1.5">
                            <div class="flex items-center gap-1 min-w-0">
                                <?php if (!empty($p['category_name'])): ?>
                                    <span class="text-[8px] sm:text-[9px] font-semibold text-secondary-500 bg-secondary-100 px-1 sm:px-1.5 py-0.2 rounded truncate max-w-[55px] sm:max-w-[65px]">
                                        <?= htmlspecialchars($p['category_name']) ?>
                                    </span>
                                <?php endif; ?>

                                <?php if ($hasDiscount): ?>
                                    <span class="text-[8px] sm:text-[9px] font-black text-white bg-red-500 px-1 py-0.2 rounded shadow-2xs">
                                        <?= $discountPercent ?>%
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Stock Badge -->
                            <?php if ($isOutOfStock): ?>
                                <span class="text-[8px] sm:text-[9px] font-black text-red-600 bg-red-50 border border-red-200 px-1 sm:px-1.5 py-0.2 rounded shadow-2xs shrink-0 flex items-center gap-1" title="স্টক আউট হলেও অর্ডারে যোগ করা যাবে">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                                    <span>আউট</span>
                                </span>
                            <?php else: ?>
                                <span class="text-[8px] sm:text-[9px] font-bold text-amber-800 bg-amber-50 border border-amber-200 px-1 sm:px-1.5 py-0.2 rounded shrink-0">
                                    <?= $stockClean ?> <?= htmlspecialchars($baseUnit) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Product Image -->
                        <div class="mb-1 sm:mb-1.5 bg-secondary-50/80 rounded-xl overflow-hidden shrink-0 flex items-center justify-center p-1 sm:p-1.5 h-16 sm:h-20 border border-secondary-100">
                            <img src="<?= $posImg ?>" 
                                 alt="<?= htmlspecialchars($p['name']) ?>" 
                                 class="max-h-full max-w-full object-contain drop-shadow-2xs transition-transform duration-200 group-hover:scale-105"
                                 loading="lazy"
                                 onerror="this.src='<?= $base ?>/images/default-product.svg'">
                        </div>

                        <!-- Product Title -->
                        <h4 class="font-semibold text-secondary-900 text-[11px] sm:text-xs line-clamp-2 min-h-[26px] sm:min-h-[30px] leading-tight mb-1 group-hover:text-primary-600 transition-colors" 
                            title="<?= htmlspecialchars($p['name']) ?>">
                            <?= htmlspecialchars($p['name']) ?>
                        </h4>

                        <!-- Price Row -->
                        <div class="flex items-baseline gap-1 mb-1 sm:mb-1.5 flex-wrap">
                            <span class="text-xs font-bold text-primary-700">৳</span>
                            <span class="pos-card-price text-xs sm:text-sm font-black text-primary-600 leading-none">
                                <?= number_format($vInfo['default_price']) ?>
                            </span>

                            <?php if ($hasDiscount): ?>
                                <span class="text-[9px] sm:text-[10px] text-secondary-400 line-through font-medium leading-none">
                                    ৳ <?= number_format($p['regular_price']) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Multi-Unit Variant Selector Pills -->
                        <?php if (!empty($vInfo['variants']) && count($vInfo['variants']) > 1): ?>
                            <div class="flex flex-wrap gap-1 mb-1.5 pt-1 border-t border-dashed border-secondary-100 pos-variant-pills max-h-12 overflow-y-auto scrollbar-hide">
                                <?php foreach ($vInfo['variants'] as $v): ?>
                                    <?php 
                                        $isDef = ($v['title'] === $vInfo['default_title']);
                                        $pillClass = $isDef 
                                            ? 'border-primary-600 text-primary-700 bg-primary-50 font-bold active-pos-variant shadow-2xs' 
                                            : 'border-secondary-200 text-secondary-500 bg-white hover:border-secondary-300 font-medium';
                                    ?>
                                    <button type="button" 
                                            onclick="selectPosCardVariant(this, '<?= htmlspecialchars($v['title'], ENT_QUOTES) ?>', <?= floatval($v['price']) ?>, <?= floatval($v['qty']) ?>)"
                                            class="pos-variant-btn px-1 sm:px-1.5 py-0.5 rounded text-[9px] sm:text-[10px] border transition-all cursor-pointer <?= $pillClass ?>">
                                        <?= htmlspecialchars($v['title']) ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Add / Weight Actions -->
                        <?php if ($isWeightProduct): ?>
                            <div class="flex items-center gap-1 mt-auto pt-1">
                                <button type="button" 
                                        onclick="addCardProductToCart(this)" 
                                        class="flex-1 py-1.5 px-1 sm:px-1.5 rounded-lg <?= $isOutOfStock ? 'bg-amber-50 hover:bg-amber-600 text-amber-800 hover:text-white border border-amber-300' : 'bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200' ?> font-bold text-[10px] sm:text-xs flex items-center justify-center gap-0.5 transition-all shadow-xs cursor-pointer active:scale-95"
                                        title="কার্টে যোগ করুন">
                                    <ion-icon name="cart-outline" class="text-xs"></ion-icon>
                                    <span>+ Add</span>
                                </button>
                                <button type="button" 
                                        onclick="openPosCardWeightModal(this)" 
                                        class="py-1.5 px-2 rounded-lg bg-amber-50 hover:bg-amber-500 text-amber-900 hover:text-white border border-amber-300 font-bold text-[10px] sm:text-xs flex items-center justify-center gap-0.5 transition-all shadow-2xs cursor-pointer active:scale-95 shrink-0" 
                                        title="সরাসরি ওজন দিয়ে যোগ করুন">
                                    <ion-icon name="scale-outline" class="text-xs text-amber-600"></ion-icon>
                                    <span>⚖️ মেপে</span>
                                </button>
                            </div>
                        <?php else: ?>
                            <button type="button" 
                                    onclick="addCardProductToCart(this)" 
                                    class="w-full py-1.5 px-1.5 sm:px-2 rounded-lg <?= $isOutOfStock ? 'bg-amber-50 hover:bg-amber-600 text-amber-800 hover:text-white border border-amber-300' : 'bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200' ?> font-bold text-[11px] sm:text-xs flex items-center justify-center gap-1 transition-all shadow-xs cursor-pointer active:scale-95 mt-auto"
                                    title="কার্টে যোগ করুন">
                                <ion-icon name="cart-outline" class="text-xs sm:text-sm"></ion-icon>
                                <span>+ Add</span>
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; endif; ?>

                <div id="noResults" class="hidden col-span-full py-12 flex flex-col items-center justify-center text-secondary-400">
                    <ion-icon name="search-outline" class="text-4xl sm:text-5xl mb-2 opacity-30"></ion-icon>
                    <p class="text-xs sm:text-sm font-semibold">No products found</p>
                    <p class="text-[11px] sm:text-xs text-secondary-400 mt-0.5">Try searching with a different keyword</p>
                </div>
            </div>
        </div>

        <!-- Floating Mobile Return Bar (Appears when browsing products on mobile) -->
        <div id="mobileFloatingCartBar" 
             onclick="switchToMobileTab('cart')" 
             class="pos-mobile-bar flex lg:hidden fixed bottom-4 left-3 right-3 z-40 bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white p-3 rounded-2xl shadow-2xl items-center justify-between font-bold text-xs cursor-pointer transition-all active:scale-[0.98] border border-white/20">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center text-white">
                    <ion-icon name="cart" class="text-base"></ion-icon>
                </div>
                <div>
                    <span class="block text-[11px] text-emerald-100 font-semibold leading-tight">অর্ডারে ফিরুন (#<?= $orderId ?>)</span>
                    <span class="block text-xs font-black text-white"><span id="floatCartCount"><?= count($order['items'] ?? []) ?></span>টি পণ্য • <span id="floatCartTotal">৳ <?= number_format($orderTotal) ?></span></span>
                </div>
            </div>
            <span class="flex items-center gap-1 bg-white text-emerald-800 px-3 py-1.5 rounded-xl font-black text-xs shadow-xs">
                <span>চেকআউট</span>
                <ion-icon name="arrow-forward"></ion-icon>
            </span>
        </div>
    </div>

    <!-- RIGHT PANEL: Order Details & Cart -->
    <div id="posCartPanel" class="pos-panel-active lg:flex w-full lg:w-[380px] xl:w-[420px] lg:min-w-[340px] lg:max-w-[440px] flex flex-col bg-white rounded-2xl shadow-sm border border-secondary-200 h-full overflow-hidden shrink-0">
        
        <!-- Desktop Header -->
        <div class="hidden lg:flex items-center justify-between px-3.5 py-2.5 bg-secondary-50/80 border-b border-secondary-200 shrink-0">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                    <ion-icon name="create-outline" class="text-base"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-secondary-900 text-xs leading-tight">অর্ডার এডিট (#<?= $orderId ?>)</h3>
                    <p class="text-[10px] text-secondary-400">Order Items & Total Editor</p>
                </div>
            </div>
            <span id="desktopCartBadge" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700"><?= count($order['items'] ?? []) ?> আইটেম</span>
        </div>

        <!-- Mobile Back Header -->
        <div class="pos-mobile-bar flex lg:hidden items-center justify-between px-3 py-2 bg-gradient-to-r from-emerald-600 to-teal-700 text-white shadow-xs shrink-0">
            <button type="button" onclick="switchToMobileTab('products')" class="text-xs font-bold text-white hover:text-white/90 flex items-center gap-1.5 bg-white/20 active:bg-white/30 px-3 py-1.5 rounded-xl backdrop-blur-xs transition-all cursor-pointer">
                <ion-icon name="add-circle-outline" class="text-base"></ion-icon>
                <span>➕ নতুন পণ্য যোগ</span>
            </button>
            <div class="flex items-center gap-1.5">
                <span class="text-xs font-black tracking-wide">অর্ডার #<?= $orderId ?></span>
                <span id="mobileCartHeaderCount" class="px-2 py-0.5 text-[10px] font-black rounded-full bg-white text-emerald-700 shadow-2xs"><?= count($order['items'] ?? []) ?> আইটেম</span>
            </div>
        </div>

        <!-- Customer Selection Box -->
        <div class="p-2.5 sm:p-3 border-b border-secondary-100 bg-secondary-50/80 shrink-0">
            <div class="flex justify-between items-center mb-1">
                <label class="block text-secondary-700 text-xs font-bold uppercase tracking-wider">Customer (গ্রাহক)</label>
                <button type="button" onclick="toggleCustomerSearch()" class="text-xs font-bold text-primary-600 hover:text-primary-700 flex items-center gap-1 cursor-pointer">
                    <ion-icon name="create-outline"></ion-icon> <span id="btnCustActionText">Change</span>
                </button>
            </div>
            
            <div class="relative hidden" id="custSearchWrapper">
                <input type="text" id="customerSearchInput" placeholder="গ্রাহকের নাম, মোবাইল বা আইডি লিখুন..." 
                       class="w-full px-3 py-1.5 bg-white border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 text-xs sm:text-sm shadow-2xs mb-1" 
                       autocomplete="off">
                <select id="customerSelect" class="hidden">
                    <?php if (!empty($customers)): foreach ($customers as $c): ?>
                        <option value="<?= $c['id'] ?>" data-phone="<?= htmlspecialchars($c['phone'] ?? '') ?>" <?= ($c['id'] == $order['customer_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['name']) ?> - <?= htmlspecialchars($c['phone'] ?? '') ?>
                        </option>
                    <?php endforeach; endif; ?>
                </select>
                <!-- Live Search Dropdown -->
                <div id="customerSearchResults" class="hidden absolute z-50 left-0 right-0 bg-white border border-secondary-200 shadow-xl rounded-xl mt-0.5 max-h-48 overflow-y-auto divide-y divide-secondary-100 text-xs"></div>
            </div>
            
            <!-- Selected Customer Display Card -->
            <div id="selectedCustomerDisplay" class="bg-white border border-blue-200 bg-blue-50/50 p-2 sm:p-2.5 rounded-xl relative overflow-hidden shadow-2xs">
                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0 flex-1">
                        <div class="font-bold text-secondary-900 text-xs sm:text-sm truncate" id="dispName"><?= htmlspecialchars($order['customer_name'] ?? 'Unknown Customer') ?></div>
                        <div class="text-[11px] text-secondary-500 font-mono truncate" id="dispPhone"><?= htmlspecialchars($order['customer_phone'] ?? 'No Phone') ?></div>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        <span class="text-[10px] text-blue-700 font-black px-2 py-0.5 bg-blue-100 border border-blue-200 rounded-lg">EDITING #<?= $orderId ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cart Items List Container -->
        <div class="flex-1 overflow-y-auto p-0 min-w-0">
            <div class="px-3 py-2 bg-secondary-50 border-b border-secondary-100 flex items-center justify-between text-xs font-bold text-secondary-600 sticky top-0 z-10 bg-white/95 backdrop-blur-xs">
                <div class="flex items-center gap-1.5">
                    <ion-icon name="cart-outline" class="text-sm text-emerald-600"></ion-icon>
                    <span>অর্ডারের আইটেম</span>
                    <span id="cartHeaderBadge" class="ml-1 px-1.5 py-0.2 rounded-full bg-emerald-100 text-emerald-700 font-bold text-[10px]"><?= count($order['items'] ?? []) ?></span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="switchToMobileTab('products')" class="lg:hidden text-[11px] font-bold text-primary-600 hover:text-primary-700 flex items-center gap-0.5 cursor-pointer">
                        <ion-icon name="add-circle-outline"></ion-icon>
                        <span>+ পণ্য যোগ</span>
                    </button>
                    <button type="button" onclick="clearEntireCart()" class="text-[11px] font-bold text-red-500 hover:text-red-700 flex items-center gap-0.5 cursor-pointer transition-colors" title="সব আইটেম মুছুন">
                        <ion-icon name="trash-outline"></ion-icon>
                        <span>মুছুন</span>
                    </button>
                </div>
            </div>
            
            <div id="cartItems" class="divide-y divide-secondary-100 min-w-0"></div>
            
            <div id="emptyCartMsg" class="hidden text-center text-secondary-400 py-12 px-4 text-xs sm:text-sm italic flex flex-col items-center">
                <div class="w-12 h-12 rounded-2xl bg-secondary-100 flex items-center justify-center mb-2 text-secondary-400">
                    <ion-icon name="cart-outline" class="text-2xl opacity-40"></ion-icon>
                </div>
                <span class="font-bold text-secondary-700 text-xs not-italic">অর্ডারে কোনো পণ্য নেই!</span>
                <button type="button" onclick="switchToMobileTab('products')" class="mt-2.5 px-3 py-1.5 rounded-xl bg-emerald-600 text-white font-bold text-xs shadow-xs hover:bg-emerald-700 transition-colors flex items-center gap-1 cursor-pointer not-italic">
                    <ion-icon name="add-circle"></ion-icon>
                    <span>পণ্য যোগ করুন</span>
                </button>
            </div>
        </div>

        <!-- Totals & Save Footer -->
        <div class="p-3 sm:p-3.5 border-t border-secondary-200 bg-secondary-50/90 space-y-2 shrink-0">
            <!-- Subtotal -->
            <div class="flex justify-between items-center text-xs">
                <span class="text-secondary-600 font-semibold">Subtotal</span>
                <span class="font-bold text-secondary-900 text-sm" id="cartSubtotal">৳ 0</span>
            </div>

            <!-- Discount -->
            <div class="flex justify-between items-center text-xs">
                <div class="flex items-center gap-1">
                    <span class="text-secondary-600 font-medium">ডিসকাউন্ট</span>
                    <span class="text-[9px] text-red-500 font-bold bg-red-50 px-1 py-0.2 rounded border border-red-100">DISCOUNT</span>
                </div>
                <div class="flex items-center w-28">
                    <span class="text-secondary-500 mr-1 text-xs">৳</span>
                    <input type="number" id="orderDiscount" value="<?= $existingDiscount ?>" min="0" step="any" oninput="calculateTotal()" 
                           class="w-full text-right px-2 py-1 text-xs font-bold border border-secondary-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500 bg-white shadow-2xs">
                </div>
            </div>

            <!-- Delivery Charge -->
            <div class="flex justify-between items-center text-xs">
                <span class="text-secondary-600 font-medium">ডেলিভারি চার্জ</span>
                <div class="flex items-center w-28">
                    <span class="text-secondary-500 mr-1 text-xs">৳</span>
                    <input type="number" id="deliveryCharge" value="<?= $existingDelivery ?>" min="0" step="any" oninput="calculateTotal()" 
                           class="w-full text-right px-2 py-1 text-xs font-bold border border-secondary-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500 bg-white shadow-2xs">
                </div>
            </div>

            <!-- Grand Total -->
            <div class="flex justify-between items-center pt-2 border-t border-secondary-200">
                <span class="text-secondary-800 font-bold text-sm sm:text-base">Grand Total</span>
                <span class="font-black text-emerald-700 text-lg sm:text-xl" id="cartTotal">৳ <?= number_format($orderTotal) ?></span>
            </div>

            <button type="button" id="btnUpdateOrder" onclick="updateOrder()" 
                    class="w-full bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-black py-3 px-4 rounded-xl flex justify-center items-center gap-2 transition-all shadow-md active:scale-[0.99] cursor-pointer text-sm sm:text-base">
                <span id="btnText">Update Order 💾</span>
            </button>
        </div>
    </div>
</div>

<!-- Digital Weighing Scale Modal for Catch-Weight Products -->
<div id="posWeightModal" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-3">
    <div class="bg-white rounded-2xl shadow-2xl border border-secondary-200 w-full max-w-sm overflow-hidden flex flex-col animate-in fade-in zoom-in-95 duration-150">
        <div class="px-4 py-3 bg-gradient-to-r from-emerald-600 to-teal-700 text-white flex items-center justify-between">
            <div class="flex items-center gap-2">
                <ion-icon name="scale-outline" class="text-xl"></ion-icon>
                <h4 class="font-bold text-sm">ডিজিটাল ওজন স্কেল</h4>
            </div>
            <button type="button" onclick="closePosWeightModal()" class="text-white/80 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-colors">
                <ion-icon name="close" class="text-lg"></ion-icon>
            </button>
        </div>
        <div class="p-4 space-y-3.5">
            <div>
                <h5 id="posWeightProductName" class="font-bold text-secondary-900 text-xs sm:text-sm truncate"></h5>
                <?php if ($posAllowCustomPrice): ?>
                    <div class="flex items-center justify-between mt-1.5 pt-1.5 border-t border-secondary-200/70">
                        <span class="text-[11px] font-bold text-amber-900 flex items-center gap-1">
                            <span>✏️ ইউনিট বিক্রয় দর (৳):</span>
                        </span>
                        <div class="flex items-center gap-1">
                            <span class="text-xs font-bold text-secondary-500">৳</span>
                            <input type="number" step="any" min="0" id="posWeightCustomRateInput" oninput="onPosWeightCustomRateChange()" class="w-24 px-2 py-0.5 text-xs font-black text-emerald-700 bg-white border border-amber-300 rounded-lg text-right focus:outline-none focus:ring-1 focus:ring-emerald-500 shadow-2xs" placeholder="0.00">
                        </div>
                    </div>
                <?php else: ?>
                    <p id="posWeightProductPrice" class="text-[11px] text-secondary-500 font-medium"></p>
                <?php endif; ?>
            </div>

            <!-- Unit Mode Toggle (Kg / Gm) -->
            <div class="flex items-center justify-center bg-secondary-100 p-1 rounded-xl gap-1 text-xs font-bold">
                <button type="button" id="posWeightModeKg" onclick="switchPosWeightMode('kg')" class="flex-1 py-1.5 rounded-lg bg-white text-emerald-800 shadow-xs transition-all">কেজি (Kg)</button>
                <button type="button" id="posWeightModeGm" onclick="switchPosWeightMode('gm')" class="flex-1 py-1.5 rounded-lg text-secondary-600 hover:text-secondary-900 transition-all">গ্রাম (Gm)</button>
            </div>

            <!-- Weight Input Display -->
            <div class="bg-secondary-50 border-2 border-emerald-400 rounded-2xl p-3 text-center">
                <span class="text-[10px] uppercase tracking-wider text-secondary-500 font-bold block mb-1">স্কেলের ওজন লিখুন</span>
                <div class="flex items-center justify-center gap-1.5">
                    <input type="number" id="posWeightInput" step="any" min="0.001" placeholder="0.00" oninput="onPosWeightInputChange()" class="w-36 text-center text-2xl font-black text-secondary-900 bg-transparent border-b-2 border-emerald-600 focus:outline-none focus:border-emerald-700">
                    <span id="posWeightUnitLabel" class="text-base font-bold text-secondary-600">kg</span>
                </div>
            </div>

            <!-- Quick Preset Weight Buttons -->
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-secondary-500">কুইক ওজন নির্বাচন:</span>
                <div class="grid grid-cols-4 gap-1.5 text-xs font-bold">
                    <button type="button" onclick="setQuickPosWeight(0.200)" class="py-1 px-1.5 rounded-lg bg-secondary-100 hover:bg-emerald-100 text-secondary-800 hover:text-emerald-800 transition-colors">২০০ g</button>
                    <button type="button" onclick="setQuickPosWeight(0.250)" class="py-1 px-1.5 rounded-lg bg-secondary-100 hover:bg-emerald-100 text-secondary-800 hover:text-emerald-800 transition-colors">২৫০ g</button>
                    <button type="button" onclick="setQuickPosWeight(0.500)" class="py-1 px-1.5 rounded-lg bg-secondary-100 hover:bg-emerald-100 text-secondary-800 hover:text-emerald-800 transition-colors">৫০০ g</button>
                    <button type="button" onclick="setQuickPosWeight(0.750)" class="py-1 px-1.5 rounded-lg bg-secondary-100 hover:bg-emerald-100 text-secondary-800 hover:text-emerald-800 transition-colors">৭৫০ g</button>
                    <button type="button" onclick="setQuickPosWeight(1.000)" class="py-1 px-1.5 rounded-lg bg-secondary-100 hover:bg-emerald-100 text-secondary-800 hover:text-emerald-800 transition-colors">১ kg</button>
                    <button type="button" onclick="setQuickPosWeight(1.100)" class="py-1 px-1.5 rounded-lg bg-secondary-100 hover:bg-emerald-100 text-secondary-800 hover:text-emerald-800 transition-colors">১.১ kg</button>
                    <button type="button" onclick="setQuickPosWeight(1.250)" class="py-1 px-1.5 rounded-lg bg-secondary-100 hover:bg-emerald-100 text-secondary-800 hover:text-emerald-800 transition-colors">১.২৫ kg</button>
                    <button type="button" onclick="setQuickPosWeight(1.500)" class="py-1 px-1.5 rounded-lg bg-secondary-100 hover:bg-emerald-100 text-secondary-800 hover:text-emerald-800 transition-colors">১.৫ kg</button>
                </div>
            </div>

            <!-- Price Preview -->
            <div class="p-2.5 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-between text-xs">
                <span class="text-emerald-900 font-bold">মোট হিসাবকৃত মূল্য:</span>
                <span id="posWeightCalculatedPrice" class="text-sm font-black text-emerald-700">৳ 0</span>
            </div>

            <!-- Confirm Button -->
            <button type="button" onclick="applyPosWeightToItem()" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm shadow-md transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                <ion-icon name="checkmark-circle" class="text-base"></ion-icon>
                <span>ওজন নিশ্চিত করে যোগ করুন</span>
            </button>
        </div>
    </div>
</div>

<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

    /* Mobile & Desktop Responsive Panels */
    @media (max-width: 1023.98px) {
        .pos-panel-active {
            display: flex !important;
            width: 100% !important;
            height: 100% !important;
            flex: 1 1 100% !important;
        }
        .pos-panel-inactive {
            display: none !important;
        }
        .pos-mobile-bar {
            display: flex !important;
        }
    }
</style>

<script>
    const products = <?php echo !empty($products) ? json_encode($products) : '[]'; ?>;
    const allCategories = <?php echo !empty($categories) ? json_encode($categories) : '[]'; ?>;
    const baseUrl = '<?= $base ?>';
    let selectedCustId = <?= $order['customer_id'] ?>;
    const orderId = <?= $orderId ?>;
    let filterCatId = 'all';
    let currentParentId = null;
    let currentMobileTab = 'cart'; // Default on mobile: show order details first

    // Pre-populate Cart with existing items from order
    const serverItems = <?php echo json_encode($order['items'] ?? []); ?>;
    
    let cart = (serverItems || []).map((si, idx) => {
        const prod = products.find(p => p.id == si.product_id);
        const maxStock = prod ? parseFloat(prod.stock_qty || 0) : 0;
        const curQty = parseFloat(si.quantity) || 1;
        const variantTitle = si.unit_title || '';
        const cartKey = si.product_id + '_' + variantTitle;
        const baseQty = parseFloat(si.base_qty) || 1.000;
        const baseUnit = prod ? (prod.base_unit || 'pcs') : 'pcs';
        const image = si.image_path || (prod && prod.image_path ? prod.image_path : baseUrl + '/images/default-product.svg');

        return {
            cartKey: cartKey,
            id: parseInt(si.product_id),
            name: si.product_name,
            image: image,
            variant_title: variantTitle,
            baseQty: baseQty,
            qty: curQty,
            price: parseFloat(si.price),
            maxStock: maxStock + curQty,
            baseUnit: baseUnit,
            isOutOfStock: false
        };
    });

    // --- Mobile Tab Switching ---
    function switchToMobileTab(tab) {
        currentMobileTab = tab;
        const prodPanel = document.getElementById('posProductsPanel');
        const cartPanel = document.getElementById('posCartPanel');
        const btnProd = document.getElementById('tabBtnProducts');
        const btnCart = document.getElementById('tabBtnCart');
        const mobileFloatBar = document.getElementById('mobileFloatingCartBar');

        if (tab === 'cart') {
            if (prodPanel) {
                prodPanel.classList.remove('pos-panel-active');
                prodPanel.classList.add('pos-panel-inactive');
            }
            if (cartPanel) {
                cartPanel.classList.remove('pos-panel-inactive');
                cartPanel.classList.add('pos-panel-active');
            }
            if (btnProd) {
                btnProd.className = "flex-1 py-2.5 px-3 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition-all text-secondary-600 hover:bg-secondary-100 cursor-pointer";
            }
            if (btnCart) {
                btnCart.className = "flex-1 py-2.5 px-3 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition-all bg-emerald-600 text-white shadow-xs cursor-pointer";
            }
            if (mobileFloatBar) {
                mobileFloatBar.style.display = 'none';
            }
        } else {
            if (prodPanel) {
                prodPanel.classList.remove('pos-panel-inactive');
                prodPanel.classList.add('pos-panel-active');
            }
            if (cartPanel) {
                cartPanel.classList.remove('pos-panel-active');
                cartPanel.classList.add('pos-panel-inactive');
            }
            if (btnProd) {
                btnProd.className = "flex-1 py-2.5 px-3 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition-all bg-emerald-600 text-white shadow-xs cursor-pointer";
            }
            if (btnCart) {
                btnCart.className = "flex-1 py-2.5 px-3 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition-all text-secondary-600 hover:bg-secondary-100 cursor-pointer";
            }
            if (mobileFloatBar) {
                mobileFloatBar.style.display = 'flex';
            }
        }
    }

    // --- Category Rendering & Filter ---
    function getChildren(parentId) {
        return allCategories.filter(c => {
            if (parentId === null) return !c.parent_id || c.parent_id == 0; 
            return c.parent_id == parentId;
        });
    }

    function renderCategories(parentId = null) {
        currentParentId = parentId;
        const container = document.getElementById('categoryFilter');
        if (!container) return;
        container.innerHTML = '';

        if (parentId !== null) {
            const currentObj = allCategories.find(c => c.id == parentId);
            const grandParentId = currentObj && currentObj.parent_id ? currentObj.parent_id : null;
            const backBtn = document.createElement('button');
            backBtn.className = "cat-btn flex flex-col items-center gap-1 min-w-[56px] group transition-all shrink-0 cursor-pointer";
            backBtn.onclick = () => renderCategories(grandParentId);
            backBtn.innerHTML = `
                <div style="width: 50px; height: 50px;" class="rounded-full bg-secondary-200 text-secondary-600 flex items-center justify-center shadow-xs group-hover:bg-secondary-300">
                    <ion-icon name="arrow-back" class="text-xl"></ion-icon>
                </div>
                <span class="text-[9px] font-bold text-secondary-600">Back</span>`;
            container.appendChild(backBtn);
        } else {
            const allBtn = document.createElement('button');
            allBtn.className = `cat-btn flex flex-col items-center gap-1 min-w-[56px] group transition-all shrink-0 cursor-pointer ${filterCatId === 'all' ? 'active' : 'opacity-70'}`;
            allBtn.onclick = () => { filterCatId = 'all'; renderCategories(null); applyFilters(); };
            allBtn.innerHTML = `
                <div style="width: 50px; height: 50px;" class="rounded-full ${filterCatId === 'all' ? 'bg-primary-600 text-white shadow-xs ring-2 ring-primary-600 ring-offset-2' : 'bg-secondary-100 text-secondary-500'} flex items-center justify-center shadow-xs">
                    <ion-icon name="apps" class="text-xl"></ion-icon>
                </div>
                <span class="text-[9px] font-bold text-secondary-800">All</span>`;
            container.appendChild(allBtn);
        }

        getChildren(parentId).forEach(cat => {
            const hasSub = allCategories.some(c => c.parent_id == cat.id);
            const isActive = filterCatId == cat.id;
            const btn = document.createElement('button');
            btn.className = `cat-btn flex flex-col items-center gap-1 min-w-[56px] group transition-all shrink-0 cursor-pointer ${isActive ? 'active opacity-100' : 'opacity-70 hover:opacity-100'}`;
            btn.onclick = () => {
                if (hasSub) renderCategories(cat.id);
                else {
                    filterCatId = cat.id;
                    document.querySelectorAll('.cat-btn > div').forEach(d => d.classList.remove('ring-2', 'ring-primary-600', 'ring-offset-2'));
                    btn.querySelector('div').classList.add('ring-2', 'ring-primary-600', 'ring-offset-2');
                    applyFilters();
                }
            };
            const imgHtml = cat.image_path ? `<img src="${cat.image_path}" class="w-full h-full object-cover">` : `<span class="text-base font-bold text-secondary-400 uppercase">${cat.name.charAt(0)}</span>`;
            btn.innerHTML = `<div style="width: 50px; height: 50px;" class="rounded-full bg-secondary-50 border border-secondary-200 overflow-hidden flex items-center justify-center shadow-2xs group-hover:border-primary-400 group-hover:shadow-xs transition-all ${isActive ? 'ring-2 ring-primary-600 ring-offset-2' : ''}">${imgHtml}</div><span class="text-[9px] font-medium text-secondary-600 text-center leading-tight max-w-[62px] truncate group-hover:text-primary-600">${cat.name} ${hasSub ? '›' : ''}</span>`;
            container.appendChild(btn);
        });

        if (parentId !== null && filterCatId !== parentId) { 
            filterCatId = parentId; 
            applyFilters(); 
        }
    }

    function clearCategoryFilter() {
        filterCatId = 'all';
        renderCategories(null);
        applyFilters();
    }

    function clearSearch() {
        const input = document.getElementById('productSearch');
        if (input) input.value = '';
        applyFilters();
    }

    function applyFilters() {
        const term = (document.getElementById('productSearch')?.value || '').toLowerCase().trim();
        let allowedCatIds = filterCatId === 'all' ? null : [filterCatId];
        if (allowedCatIds) {
            const stack = [filterCatId];
            while(stack.length > 0) {
                const pid = stack.pop();
                const kids = allCategories.filter(c => c.parent_id == pid).map(c => c.id);
                allowedCatIds.push(...kids);
                stack.push(...kids);
            }
        }

        // Active Category Header Bar
        const activeHeader = document.getElementById('activeCategoryHeader');
        const activeTitle = document.getElementById('activeCategoryTitle');
        if (filterCatId !== 'all') {
            const currentCat = allCategories.find(c => c.id == filterCatId);
            if (activeHeader && activeTitle && currentCat) {
                activeTitle.innerText = currentCat.name;
                activeHeader.classList.remove('hidden');
                activeHeader.classList.add('flex');
            }
        } else if (activeHeader) {
            activeHeader.classList.add('hidden');
            activeHeader.classList.remove('flex');
        }

        let visible = 0;
        document.querySelectorAll('.pos-product-card').forEach(card => {
            const name = (card.dataset.name || '').toLowerCase();
            const sku = (card.dataset.sku || '').toLowerCase();
            const cat = card.dataset.category;
            const matchesSearch = term === '' || name.includes(term) || sku.includes(term);
            const matchesCat = allowedCatIds === null || allowedCatIds.some(id => id == cat);

            if (matchesSearch && matchesCat) {
                card.style.display = 'flex';
                visible++;
            } else {
                card.style.display = 'none';
            }
        });

        const noRes = document.getElementById('noResults');
        if (noRes) {
            noRes.classList.toggle('hidden', visible > 0);
        }
    }

    // --- Variant Selection on Cards ---
    function selectPosCardVariant(btn, title, price, qty) {
        const card = btn.closest('.pos-product-card');
        if (!card) return;

        card.querySelectorAll('.pos-variant-btn').forEach(b => {
            b.className = 'pos-variant-btn px-1 sm:px-1.5 py-0.5 rounded text-[9px] sm:text-[10px] border border-secondary-200 text-secondary-500 bg-white hover:border-secondary-300 font-medium transition-all cursor-pointer';
        });

        btn.className = 'pos-variant-btn px-1 sm:px-1.5 py-0.5 rounded text-[9px] sm:text-[10px] border border-primary-600 text-primary-700 bg-primary-50 font-bold active-pos-variant shadow-2xs transition-all cursor-pointer';

        card.dataset.activeVariantTitle = title;
        card.dataset.activeVariantPrice = price;
        card.dataset.activeVariantQty = qty;

        const priceEl = card.querySelector('.pos-card-price');
        if (priceEl) priceEl.innerText = Number(price).toLocaleString();
    }

    // --- Add Product to Order Cart ---
    function addCardProductToCart(btn) {
        const card = btn.closest('.pos-product-card');
        if (!card) return;

        const productId = parseInt(card.dataset.productId);
        const name = card.querySelector('h4').innerText.trim();
        const variantTitle = card.dataset.activeVariantTitle || '';
        const price = parseFloat(card.dataset.activeVariantPrice || 0);
        const baseQty = parseFloat(card.dataset.activeVariantQty || 1.000);
        const maxStock = parseFloat(card.dataset.stock || 0);
        const isOutOfStock = (card.dataset.isOutOfStock === '1' || maxStock <= 0);
        const baseUnit = card.dataset.baseUnit || 'pcs';
        const imgEl = card.querySelector('img');
        const image = card.dataset.image || (imgEl ? imgEl.src : baseUrl + '/images/default-product.svg');

        const cartKey = productId + '_' + variantTitle;
        const existing = cart.find(i => i.cartKey === cartKey);

        if (existing) {
            existing.qty = Math.round((existing.qty + 1) * 1000) / 1000;
        } else {
            cart.push({
                cartKey: cartKey,
                id: productId,
                name: name,
                image: image,
                variant_title: variantTitle,
                price: price,
                baseQty: baseQty,
                qty: 1,
                maxStock: maxStock + 1,
                isOutOfStock: isOutOfStock,
                baseUnit: baseUnit
            });
        }

        renderCart();

        // Flash button feedback
        const origHtml = btn.innerHTML;
        btn.innerHTML = `<ion-icon name="checkmark-circle" class="text-xs"></ion-icon> <span>যোগ হয়েছে!</span>`;
        btn.classList.add('bg-emerald-600', 'text-white');
        setTimeout(() => {
            btn.innerHTML = origHtml;
            btn.classList.remove('bg-emerald-600', 'text-white');
        }, 800);
    }

    // --- Cart Rendering ---
    function renderCart() {
        const container = document.getElementById('cartItems');
        const emptyMsg = document.getElementById('emptyCartMsg');
        
        let subtotal = 0;
        const totalItems = cart.reduce((sum, item) => sum + item.qty, 0);
        
        if (cart.length === 0) {
            container.innerHTML = '';
            if (emptyMsg) emptyMsg.classList.remove('hidden');
        } else {
            if (emptyMsg) emptyMsg.classList.add('hidden');
            container.innerHTML = cart.map(item => `
                <div class="p-2 sm:p-2.5 hover:bg-secondary-50/70 transition-colors flex items-center gap-2 group">
                    <!-- Thumbnail -->
                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-secondary-50 border border-secondary-200 p-1 flex items-center justify-center shrink-0 overflow-hidden shadow-2xs">
                        <img src="${item.image || baseUrl + '/images/default-product.svg'}" 
                             alt="${item.name}" 
                             class="w-full h-full object-contain"
                             onerror="this.src='${baseUrl}/images/default-product.svg'">
                    </div>
                    
                    <!-- Info & Stepper -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-1">
                            <div class="min-w-0 flex-1">
                                <h5 class="font-bold text-secondary-900 text-xs sm:text-sm line-clamp-1 leading-snug" title="${item.name}">${item.name}</h5>
                                <div class="flex items-center gap-1 flex-wrap mt-0.5">
                                    ${item.variant_title ? `
                                        <span class="text-[9px] font-bold text-emerald-800 bg-emerald-50 border border-emerald-200 px-1.5 py-0.2 rounded">
                                            ${item.variant_title}
                                        </span>
                                    ` : ''}
                                    <?php if ($posAllowCustomPrice): ?>
                                        <div class="inline-flex items-center gap-1 bg-amber-50/90 border border-amber-300 rounded-lg px-1.5 py-0.5 shadow-2xs mt-0.5" title="কাউন্টারে বিক্রয় দর পরিবর্তন করুন">
                                            <span class="text-[9px] font-black text-amber-800">৳ দর:</span>
                                            <input type="number" step="any" min="0" value="${item.price}" 
                                                   oninput="manualPrice('${item.cartKey}', this.value)" 
                                                   onblur="manualPrice('${item.cartKey}', this.value, true)"
                                                   class="w-14 sm:w-16 text-center text-[11px] sm:text-xs font-black text-emerald-700 bg-white border border-amber-300 rounded px-1 py-0.2 focus:outline-none focus:ring-1 focus:ring-emerald-500 shadow-2xs" 
                                                   placeholder="0.00">
                                            <span class="text-[9px] font-bold text-amber-700">/${item.baseUnit || 'ইউনিট'}</span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-[10px] text-secondary-400 font-medium">৳ ${Number(item.price).toLocaleString()} / ইউনিট</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Remove Item -->
                            <button type="button" onclick="removeItem('${item.cartKey}')" class="text-secondary-300 hover:text-red-500 transition-colors p-1 rounded-lg hover:bg-red-50 cursor-pointer shrink-0" title="রিমুভ করুন">
                                <ion-icon name="trash-outline" class="text-base"></ion-icon>
                            </button>
                        </div>
                        
                        <!-- Stepper & Price Row -->
                        <div class="flex items-center justify-between mt-1.5 pt-1 border-t border-dashed border-secondary-100 gap-1.5">
                            <div class="flex items-center gap-1">
                                <div class="inline-flex items-center border border-secondary-200 rounded-lg overflow-hidden h-7 bg-white shadow-2xs">
                                    <button type="button" onclick="changeQty('${item.cartKey}', -1)" class="w-7 h-full bg-secondary-50 hover:bg-secondary-100 flex items-center justify-center text-secondary-700 font-black text-xs cursor-pointer active:bg-secondary-200 transition-colors">−</button>
                                    <input type="number" step="any" min="0.001" value="${item.qty}" onchange="manualQty('${item.cartKey}', this.value)" class="w-14 h-full text-center text-xs border-none focus:ring-0 p-0 text-secondary-900 font-bold bg-white" title="সরাসরি ওজন বা পরিমাণ লিখুন">
                                    <button type="button" onclick="changeQty('${item.cartKey}', 1)" class="w-7 h-full bg-secondary-50 hover:bg-secondary-100 flex items-center justify-center text-secondary-700 font-black text-xs cursor-pointer active:bg-secondary-200 transition-colors">+</button>
                                </div>
                                <button type="button" onclick="openPosWeightModal('${item.cartKey}')" class="px-1.5 py-1 bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300 rounded-lg text-[10px] font-bold flex items-center gap-0.5 cursor-pointer transition-colors shadow-2xs" title="কাঁচা সবজি, ফল বা মাছ-মাংসের আসল ওজন ইনপুট দিন">
                                    <ion-icon name="scale-outline" class="text-xs text-amber-600"></ion-icon>
                                    <span>⚖️ ওজন</span>
                                </button>
                            </div>
                            <div class="text-right">
                                <span id="cart-line-total-${item.cartKey}" class="text-xs sm:text-sm font-black text-emerald-700">৳ ${Number(Math.round(item.price * item.qty * 100) / 100).toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 2})}</span>
                                ${(item.qty !== 1) ? `<div class="text-[9px] text-secondary-400 font-bold leading-none mt-0.5">${item.qty} ${item.baseUnit || ''}</div>` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
            
            subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        }

        // Update Subtotal and Totals
        calculateTotal();

        // Update Badges
        const totalCount = cart.length;
        const desktopBadge = document.getElementById('desktopCartBadge');
        const headerBadge = document.getElementById('cartHeaderBadge');
        const mobileBadge = document.getElementById('mobileTabBadge');
        const mobileHeaderCount = document.getElementById('mobileCartHeaderCount');
        const floatCount = document.getElementById('floatCartCount');

        if (desktopBadge) desktopBadge.innerText = totalCount + ' আইটেম';
        if (headerBadge) headerBadge.innerText = totalCount;
        if (mobileBadge) mobileBadge.innerText = totalCount;
        if (mobileHeaderCount) mobileHeaderCount.innerText = totalCount + ' আইটেম';
        if (floatCount) floatCount.innerText = totalCount;
    }

    function changeQty(cartKey, delta) {
        const item = cart.find(i => i.cartKey === cartKey);
        if (!item) return;
        const newQty = Math.round((item.qty + delta) * 1000) / 1000;
        if (newQty > 0) {
            item.qty = newQty;
            renderCart();
        } else {
            removeItem(cartKey);
        }
    }

    function manualQty(cartKey, val) {
        const item = cart.find(i => i.cartKey === cartKey);
        if (item) {
            const parsed = parseFloat(val);
            if (!isNaN(parsed) && parsed > 0) {
                item.qty = Math.round(parsed * 1000) / 1000;
                renderCart();
            } else {
                renderCart();
            }
        }
    }

    // Manual input for item selling price (when POS custom price edit is enabled)
    function manualPrice(cartKey, val, isBlur = false) {
        const item = cart.find(i => i.cartKey === cartKey);
        if (!item) return;
        const parsed = parseFloat(val);
        if (!isNaN(parsed) && parsed >= 0) {
            item.price = Math.round(parsed * 100) / 100;
            const lineTotalEl = document.getElementById(`cart-line-total-${cartKey}`);
            if (lineTotalEl) {
                lineTotalEl.innerText = '৳ ' + Number(Math.round(item.price * item.qty * 100) / 100).toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 2});
            }
            calculateTotal();
        }
        if (isBlur) {
            renderCart();
        }
    }

    function removeItem(cartKey) {
        cart = cart.filter(i => i.cartKey !== cartKey);
        renderCart();
    }

    function clearEntireCart() {
        if (cart.length === 0) return;
        if (confirm('আপনি কি এই অর্ডারের সকল আইটেম মুছতে চান?')) {
            cart = [];
            renderCart();
        }
    }

    function calculateTotal() {
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        const subEl = document.getElementById('cartSubtotal');
        if (subEl) {
            subEl.innerText = '৳ ' + Number(Math.round(subtotal * 100) / 100).toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 2});
        }
        const discount = parseFloat(document.getElementById('orderDiscount')?.value) || 0;
        const delivery = parseFloat(document.getElementById('deliveryCharge')?.value) || 0;
        const total = Math.max(0, subtotal - discount + delivery);
        const totEl = document.getElementById('cartTotal');
        if (totEl) {
            totEl.innerText = '৳ ' + Number(Math.round(total * 100) / 100).toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 2});
        }

        const mobPrice = document.getElementById('mobileTabPrice');
        if (mobPrice) mobPrice.innerText = '৳ ' + Math.round(total);

        const floatTotal = document.getElementById('floatCartTotal');
        if (floatTotal) floatTotal.innerText = '৳ ' + Math.round(total);
    }

    // --- Customer Selection Handlers ---
    function toggleCustomerSearch() {
        const wrap = document.getElementById('custSearchWrapper');
        const disp = document.getElementById('selectedCustomerDisplay');
        const btnText = document.getElementById('btnCustActionText');
        if (!wrap) return;

        if (wrap.classList.contains('hidden')) {
            wrap.classList.remove('hidden');
            if (btnText) btnText.innerText = 'Cancel';
            const input = document.getElementById('customerSearchInput');
            if (input) {
                input.focus();
                searchCustomers(input.value);
            }
        } else {
            wrap.classList.add('hidden');
            if (btnText) btnText.innerText = 'Change';
            const results = document.getElementById('customerSearchResults');
            if (results) results.classList.add('hidden');
        }
    }

    const custInput = document.getElementById('customerSearchInput');
    const custSelect = document.getElementById('customerSelect');
    if (custInput && custSelect) {
        custInput.addEventListener('input', (e) => searchCustomers(e.target.value));
        custInput.addEventListener('focus', (e) => searchCustomers(e.target.value));
    }

    function searchCustomers(term) {
        const results = document.getElementById('customerSearchResults');
        if (!results || !custSelect) return;
        term = (term || '').toLowerCase().trim();

        const matches = Array.from(custSelect.options).filter(o => {
            if (!o.value) return false;
            const text = o.text.toLowerCase();
            const phone = (o.dataset.phone || '').toLowerCase();
            return term === '' || text.includes(term) || phone.includes(term) || o.value == term;
        }).slice(0, 15);

        if (matches.length === 0) {
            results.innerHTML = `<div class="p-2.5 text-center text-secondary-400">কোনো গ্রাহক পাওয়া যায়নি</div>`;
            results.classList.remove('hidden');
            return;
        }

        results.innerHTML = matches.map(o => `
            <div onclick="selectCustomer(${o.value}, '${o.text.replace(/'/g, "\\'")}', '${o.dataset.phone || ''}')" 
                 class="p-2 hover:bg-emerald-50 cursor-pointer transition-colors flex items-center justify-between">
                <div>
                    <span class="font-bold text-secondary-900">${o.text.split('-')[0].trim()}</span>
                    <span class="text-[11px] text-secondary-400 font-mono ml-1">${o.dataset.phone || ''}</span>
                </div>
                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-1.5 py-0.5 rounded">সিলেক্ট</span>
            </div>
        `).join('');
        results.classList.remove('hidden');
    }

    function selectCustomer(id, text, phone) {
        selectedCustId = id;
        document.getElementById('custSearchWrapper').classList.add('hidden');
        document.getElementById('customerSearchResults').classList.add('hidden');
        document.getElementById('dispName').innerText = text.split('-')[0].trim();
        document.getElementById('dispPhone').innerText = phone || 'ID: ' + id;
        const btnText = document.getElementById('btnCustActionText');
        if (btnText) btnText.innerText = 'Change';
    }

    // --- Digital Weighing Scale Modal Logic ---
    let activePosWeightTarget = null;
    let posWeightMode = 'kg';

    function openPosCardWeightModal(btn) {
        const card = btn.closest('.pos-product-card');
        if (!card) return;
        const productId = parseInt(card.dataset.productId);
        const variantTitle = card.dataset.activeVariantTitle || '';
        const cartKey = productId + '_' + variantTitle;

        // Ensure item is in cart first
        const existing = cart.find(i => i.cartKey === cartKey);
        if (!existing) {
            addCardProductToCart(btn);
        }
        openPosWeightModal(cartKey);
    }

    function openPosWeightModal(cartKey) {
        const item = cart.find(i => i.cartKey === cartKey);
        if (!item) return;

        activePosWeightTarget = item;
        posWeightMode = 'kg';

        document.getElementById('posWeightProductName').innerText = item.name + (item.variant_title ? ` (${item.variant_title})` : '');
        const priceDisplay = document.getElementById('posWeightProductPrice');
        if (priceDisplay) priceDisplay.innerText = `ইউনিট দর: ৳ ${Number(item.price).toLocaleString()} প্রতি ${item.baseUnit || 'kg'}`;
        const customRateInput = document.getElementById('posWeightCustomRateInput');
        if (customRateInput) customRateInput.value = item.price;
        document.getElementById('posWeightInput').value = item.qty;
        document.getElementById('posWeightUnitLabel').innerText = 'kg';

        switchPosWeightMode('kg');
        onPosWeightInputChange();

        const modal = document.getElementById('posWeightModal');
        if (modal) modal.classList.remove('hidden');
    }

    function closePosWeightModal() {
        const modal = document.getElementById('posWeightModal');
        if (modal) modal.classList.add('hidden');
        activePosWeightTarget = null;
    }

    function switchPosWeightMode(mode) {
        posWeightMode = mode;
        const btnKg = document.getElementById('posWeightModeKg');
        const btnGm = document.getElementById('posWeightModeGm');
        const unitLabel = document.getElementById('posWeightUnitLabel');
        const input = document.getElementById('posWeightInput');
        const curVal = parseFloat(input.value) || 0;

        if (mode === 'gm') {
            btnGm.className = "flex-1 py-1.5 rounded-lg bg-white text-emerald-800 shadow-xs transition-all";
            btnKg.className = "flex-1 py-1.5 rounded-lg text-secondary-600 hover:text-secondary-900 transition-all";
            unitLabel.innerText = 'gm';
            if (curVal > 0 && curVal <= 50) {
                input.value = Math.round(curVal * 1000);
            }
            input.step = "1";
        } else {
            btnKg.className = "flex-1 py-1.5 rounded-lg bg-white text-emerald-800 shadow-xs transition-all";
            btnGm.className = "flex-1 py-1.5 rounded-lg text-secondary-600 hover:text-secondary-900 transition-all";
            unitLabel.innerText = 'kg';
            if (curVal > 50) {
                input.value = Math.round((curVal / 1000) * 1000) / 1000;
            }
            input.step = "any";
        }
        onPosWeightInputChange();
    }

    function setQuickPosWeight(kgVal) {
        const input = document.getElementById('posWeightInput');
        if (posWeightMode === 'gm') {
            input.value = Math.round(kgVal * 1000);
        } else {
            input.value = kgVal;
        }
        onPosWeightInputChange();
    }

    function onPosWeightCustomRateChange() {
        if (!activePosWeightTarget) return;
        const rateInput = document.getElementById('posWeightCustomRateInput');
        if (!rateInput) return;
        const newRate = parseFloat(rateInput.value);
        if (!isNaN(newRate) && newRate >= 0) {
            activePosWeightTarget.price = Math.round(newRate * 100) / 100;
            onPosWeightInputChange();
        }
    }

    function onPosWeightInputChange() {
        if (!activePosWeightTarget) return;
        const input = document.getElementById('posWeightInput');
        const val = parseFloat(input.value) || 0;
        let qtyInKg = (posWeightMode === 'gm') ? (val / 1000) : val;
        qtyInKg = Math.max(0, qtyInKg);

        const calculatedPrice = Math.round(activePosWeightTarget.price * qtyInKg * 100) / 100;
        const priceEl = document.getElementById('posWeightCalculatedPrice');
        if (priceEl) priceEl.innerText = `৳ ${Number(calculatedPrice).toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 2})}`;
    }

    function applyPosWeightToItem() {
        if (!activePosWeightTarget) return;
        const input = document.getElementById('posWeightInput');
        const val = parseFloat(input.value) || 0;
        let finalQty = (posWeightMode === 'gm') ? (val / 1000) : val;
        finalQty = Math.round(finalQty * 1000) / 1000;

        if (finalQty <= 0) {
            alert('অনুগ্রহ করে সঠিক ওজন দিন!');
            return;
        }

        activePosWeightTarget.qty = finalQty;
        closePosWeightModal();
        renderCart();
    }

    // --- Update Order via AJAX ---
    async function updateOrder() {
        if (!selectedCustId) return alert('অনুগ্রহ করে গ্রাহক নির্বাচন করুন!');
        if (cart.length === 0) return alert('অর্ডারে কমপক্ষে একটি পণ্য থাকতে হবে!');

        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        const discount = parseFloat(document.getElementById('orderDiscount')?.value) || 0;
        const delivery = parseFloat(document.getElementById('deliveryCharge')?.value) || 0;
        const total = Math.max(0, subtotal - discount + delivery);

        const csrfToken = '<?= \Core\CSRF::token() ?>';

        const payload = {
            csrf_token: csrfToken,
            customer_id: selectedCustId,
            subtotal: subtotal,
            discount_amount: discount,
            delivery_charge: delivery,
            total: total,
            items: cart.map(i => ({
                product_id: i.id,
                quantity: i.qty,
                price: i.price,
                unit_title: i.variant_title || null,
                base_qty: i.baseQty || 1.000
            }))
        };

        const btn = document.getElementById('btnUpdateOrder');
        const btnText = document.getElementById('btnText');
        if (btn) btn.disabled = true;
        if (btnText) btnText.innerHTML = `<span class="inline-block animate-spin mr-2">🔄</span> Updating Order...`;

        try {
            const res = await fetch(`${baseUrl}/admin/orders/update?id=${orderId}`, {
                method: 'POST', 
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }, 
                body: JSON.stringify(payload)
            });

            const text = await res.text();
            let data;
            try { 
                data = JSON.parse(text); 
            } catch(e) { 
                console.error(text); 
                throw new Error(text || 'সার্ভার থেকে অপ্রত্যাশিত প্রতিক্রিয়া পাওয়া গেছে'); 
            }

            if (data.success) {
                if (btnText) btnText.innerHTML = `✅ সফলভাবে আপডেট হয়েছে!`;
                setTimeout(() => {
                    window.location.href = data.redirect || `${baseUrl}/admin/orders/${orderId}`;
                }, 400);
            } else {
                alert(data.error || 'অর্ডার আপডেট ব্যর্থ হয়েছে');
                if (btn) btn.disabled = false;
                if (btnText) btnText.innerText = 'Update Order 💾';
            }
        } catch(e) {
            alert('ত্রুটি: ' + e.message);
            if (btn) btn.disabled = false;
            if (btnText) btnText.innerText = 'Update Order 💾';
        }
    }

    // Initialize UI on page load
    document.addEventListener('DOMContentLoaded', () => {
        renderCategories(null);
        renderCart();

        const searchInput = document.getElementById('productSearch');
        if (searchInput) {
            searchInput.addEventListener('input', applyFilters);
        }

        // On small mobile screens, ensure the user starts on Cart tab
        if (window.innerWidth < 1024) {
            switchToMobileTab('cart');
        }
    });

    // Close customer search dropdown when clicking outside
    document.addEventListener('click', (e) => {
        const results = document.getElementById('customerSearchResults');
        const custWrap = document.getElementById('custSearchWrapper');
        if (results && !custWrap.contains(e.target)) {
            results.classList.add('hidden');
        }
    });
</script>
