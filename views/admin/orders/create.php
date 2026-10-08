<?php
// views/admin/orders/create.php

$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';

// Helper function to decode product variants for POS
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
?>

<!-- Include Leaflet CSS & JS for Map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Mobile Mode Switcher Tabs (Only visible on screens < lg) -->
<div class="pos-mobile-bar flex lg:hidden items-center bg-white p-1 rounded-2xl border border-secondary-200 shadow-2xs mb-2 shrink-0 gap-1 select-none">
    <button type="button" id="tabBtnProducts" onclick="switchToMobileTab('products')" 
            class="flex-1 py-2 px-2.5 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition-all bg-primary-600 text-white shadow-xs cursor-pointer">
        <ion-icon name="grid-outline" class="text-base"></ion-icon>
        <span>পণ্য তালিকা</span>
    </button>
    <button type="button" id="tabBtnCart" onclick="switchToMobileTab('cart')" 
            class="flex-1 py-2 px-2.5 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition-all text-secondary-600 hover:bg-secondary-100 cursor-pointer">
        <ion-icon name="cart-outline" class="text-base"></ion-icon>
        <span>কার্ট ও চেকআউট</span>
        <span id="mobileTabBadge" class="hidden px-1.5 py-0.5 text-[10px] font-black rounded-full bg-red-500 text-white leading-none">0</span>
        <span id="mobileTabPrice" class="text-[11px] font-bold text-primary-700 ml-0.5">৳ 0</span>
    </button>
</div>

<div class="h-[calc(100dvh-150px)] sm:h-[calc(100vh-150px)] lg:h-[calc(100vh-140px)] flex flex-col lg:flex-row gap-3 min-w-0 w-full overflow-hidden relative">
    
    <!-- LEFT PANEL: Products Catalog & Search -->
    <div id="posProductsPanel" class="pos-panel-active flex-1 min-w-0 flex flex-col bg-white rounded-2xl shadow-sm border border-secondary-200 overflow-hidden h-full relative">
        
        <!-- Search & Category Header -->
        <div class="p-2.5 sm:p-3 border-b border-secondary-100 flex flex-col gap-2 bg-white shrink-0 min-w-0">
            <!-- Search Row -->
            <div class="flex items-center gap-2">
                <div class="relative flex-1 min-w-0">
                    <input type="text" id="productSearch" 
                           placeholder="Search product by name, SKU or barcode..." 
                           class="w-full pl-8 sm:pl-9 pr-3 sm:pr-4 py-1.5 sm:py-2 bg-secondary-50 border border-secondary-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all shadow-2xs"
                           autocomplete="off">
                    <div class="absolute inset-y-0 left-0 pl-2.5 sm:pl-3 flex items-center pointer-events-none text-secondary-400">
                        <ion-icon name="search-outline" class="text-sm sm:text-base"></ion-icon>
                    </div>
                </div>
                <button type="button" onclick="clearSearch()" class="px-2.5 sm:px-3 py-1.5 sm:py-2 text-xs font-bold text-secondary-600 hover:text-secondary-900 bg-secondary-100 hover:bg-secondary-200 rounded-xl transition-colors shrink-0">
                    Reset
                </button>
            </div>

            <!-- Circular Visual Category Icons Bar (with Images & Subcategories) -->
            <div class="flex gap-2 sm:gap-3 overflow-x-auto pb-1 pt-0.5 scrollbar-hide select-none w-full min-w-0" id="categoryFilter">
                <!-- Injected via JavaScript with circular category icons & drill-down -->
            </div>

            <!-- Active Selected Category Header Bar (Always shown on top when a category is selected) -->
            <div id="activeCategoryHeader" class="hidden px-2.5 py-1.5 rounded-xl bg-primary-50/90 border border-primary-200/90 items-center justify-between text-xs transition-all shadow-2xs">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="w-2 h-2 rounded-full bg-primary-600 animate-pulse shrink-0"></span>
                    <span class="text-secondary-500 font-medium shrink-0">ফিল্টার মেনু:</span>
                    <span id="activeCategoryTitle" class="font-bold text-primary-800 truncate"></span>
                    <span id="activeCategoryCount" class="text-[10px] font-bold bg-white text-primary-700 px-2 py-0.5 rounded-full border border-primary-200 shrink-0">0 টি পণ্য</span>
                </div>
                <button type="button" onclick="clearCategoryFilter()" class="text-xs font-bold text-red-600 hover:text-red-700 flex items-center gap-1 cursor-pointer shrink-0 ml-2 hover:bg-red-50 px-2 py-0.5 rounded-md transition-colors" title="সব পণ্য দেখুন">
                    <ion-icon name="close-circle" class="text-sm"></ion-icon>
                    <span>মুছুন</span>
                </button>
            </div>
        </div>
        
        <!-- Products Grid (2 columns on mobile, up to 5 on large screens) -->
        <div class="flex-1 min-w-0 overflow-y-auto p-2 sm:p-3 bg-secondary-50/70 pb-28 lg:pb-4">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-2.5 sm:gap-3" id="productsGrid">
                <?php if (!empty($products)): foreach ($products as $p): ?>
                    <?php 
                        $vInfo = getPosProductVariants($p);
                        $hasDiscount = \Models\Product::hasDiscount($p);
                        $discountPercent = $hasDiscount ? \Models\Product::getDiscountPercent($p) : 0;
                        $discountAmount = $hasDiscount ? \Models\Product::getDiscountAmount($p) : 0;
                        $stockNum = floatval($p['stock_qty']);
                        $stockClean = (floor($stockNum) == $stockNum) ? intval($stockNum) : rtrim(rtrim(number_format($stockNum, 3), '0'), '.');
                        $isOutOfStock = ($stockNum <= 0 || ($p['availability_status'] ?? '') === 'out_of_stock');
                        $baseUnit = $p['base_unit'] ?? 'pcs';
                        $posImg = !empty($p['image_path']) ? htmlspecialchars($p['image_path']) : $base . '/images/default-product.svg';
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
                                <span class="text-[8px] sm:text-[9px] font-black text-red-600 bg-red-50 border border-red-200 px-1 sm:px-1.5 py-0.2 rounded shadow-2xs shrink-0 flex items-center gap-1" title="স্টক শেষ হলেও অ্যাডমিন POS থেকে অর্ডার নেওয়া যাবে">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                                    <span>আউট</span>
                                </span>
                            <?php else: ?>
                                <span class="text-[8px] sm:text-[9px] font-bold text-amber-800 bg-amber-50 border border-amber-200 px-1 sm:px-1.5 py-0.2 rounded shrink-0">
                                    <?= $stockClean ?> <?= htmlspecialchars($baseUnit) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Product Image (Compact & Proportioned) -->
                        <div class="mb-1 sm:mb-1.5 bg-secondary-50/80 rounded-xl overflow-hidden shrink-0 flex items-center justify-center p-1 sm:p-1.5 h-16 sm:h-20 md:h-22 border border-secondary-100">
                            <?php 
                            $posImg = !empty($p['image_path']) ? htmlspecialchars($p['image_path']) : $base . '/images/default-product.svg';
                            ?>
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
                        <div class="flex flex-wrap gap-1 mb-1 sm:mb-1.5 pt-1 border-t border-dashed border-secondary-100 pos-variant-pills max-h-12 overflow-y-auto scrollbar-hide">
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

                        <!-- Add Button -->
                        <button type="button" 
                                onclick="addCardProductToCart(this)" 
                                class="w-full py-1.5 px-1.5 sm:px-2 rounded-lg <?= $isOutOfStock ? 'bg-amber-50 hover:bg-amber-600 text-amber-800 hover:text-white border border-amber-300 hover:border-amber-600' : 'bg-primary-50 hover:bg-primary-600 text-primary-700 hover:text-white border border-primary-200 hover:border-primary-600' ?> font-bold text-[11px] sm:text-xs flex items-center justify-center gap-1 transition-all shadow-xs cursor-pointer active:scale-95 mt-auto"
                                title="<?= $isOutOfStock ? 'স্টক আউট পণ্য (অর্ডার নেওয়া যাবে)' : 'কার্টে যোগ করুন' ?>">
                            <ion-icon name="cart-outline" class="text-xs sm:text-sm"></ion-icon>
                            <span><?= $isOutOfStock ? '+ Add (স্টক আউট)' : '+ Add' ?></span>
                        </button>
                    </div>
                <?php endforeach; endif; ?>

                <div id="noResults" class="hidden col-span-full py-12 flex flex-col items-center justify-center text-secondary-400">
                    <ion-icon name="search-outline" class="text-4xl sm:text-5xl mb-2 opacity-30"></ion-icon>
                    <p class="text-xs sm:text-sm font-semibold">No products found</p>
                    <p class="text-[11px] sm:text-xs text-secondary-400 mt-0.5">Try searching with a different keyword or category</p>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL: POS Cart & Checkout Details -->
    <div id="posCartPanel" class="pos-panel-inactive w-full lg:w-[380px] xl:w-[420px] lg:min-w-[340px] lg:max-w-[440px] flex flex-col bg-white rounded-2xl shadow-sm border border-secondary-200 h-full overflow-hidden shrink-0">
        
        <!-- Desktop POS Header -->
        <div class="hidden lg:flex items-center justify-between px-3.5 py-2.5 bg-secondary-50/80 border-b border-secondary-200 shrink-0">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-primary-100 text-primary-700 flex items-center justify-center font-bold">
                    <ion-icon name="cart" class="text-base"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-secondary-900 text-xs leading-tight">অর্ডার কার্ট ও চেকআউট</h3>
                    <p class="text-[10px] text-secondary-400">POS Checkout Terminal</p>
                </div>
            </div>
            <span id="desktopCartBadge" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary-100 text-primary-700">0 আইটেম</span>
        </div>

        <!-- Mobile Back Header -->
        <div class="pos-mobile-bar flex lg:hidden items-center justify-between px-3.5 py-2.5 bg-gradient-to-r from-primary-600 to-primary-700 text-white shadow-xs shrink-0">
            <button type="button" onclick="switchToMobileTab('products')" class="text-xs font-bold text-white hover:text-white/90 flex items-center gap-1.5 bg-white/15 active:bg-white/25 px-3 py-1.5 rounded-xl backdrop-blur-xs transition-all cursor-pointer">
                <ion-icon name="arrow-back-outline" class="text-base"></ion-icon>
                <span>← পণ্য তালিকা</span>
            </button>
            <div class="flex items-center gap-2">
                <span class="text-xs font-black tracking-wide">অর্ডার কার্ট</span>
                <span id="mobileCartHeaderCount" class="px-2 py-0.5 text-[10px] font-black rounded-full bg-white text-primary-700 shadow-2xs">0 আইটেম</span>
            </div>
        </div>

        <!-- Customer Selection Box -->
        <div class="p-2.5 sm:p-3.5 border-b border-secondary-100 bg-secondary-50/80 shrink-0">
            <div class="flex justify-between items-center mb-1.5">
                <label class="block text-secondary-700 text-xs font-bold uppercase tracking-wider">Customer</label>
                <button type="button" onclick="openQuickCustomerModal()" class="text-xs font-bold text-primary-600 hover:text-primary-700 flex items-center gap-1 cursor-pointer">
                    <ion-icon name="person-add-outline"></ion-icon> + New Customer
                </button>
            </div>
            
            <div class="relative" id="custSearchWrapper">
                <input type="text" id="customerSearchInput" placeholder="Type Name, Phone or ID..." 
                       class="w-full px-3 py-2 bg-white border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 text-xs sm:text-sm shadow-2xs" 
                       autocomplete="off">
                <!-- Live Search Dropdown -->
                <div id="customerSearchResults" class="hidden absolute z-50 left-0 right-0 bg-white border border-secondary-200 shadow-xl rounded-xl mt-1 max-h-56 overflow-y-auto divide-y divide-secondary-100"></div>
                <div class="text-[10px] text-secondary-400 mt-1">Typing automatically searches customers...</div>
            </div>
            
            <!-- Selected Customer Display Card -->
            <div id="selectedCustomerDisplay" class="hidden bg-white border border-emerald-300 bg-emerald-50/40 p-2 sm:p-2.5 rounded-xl mt-1.5 relative overflow-hidden shadow-xs">
                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0 flex-1">
                        <div class="font-bold text-secondary-900 text-xs sm:text-sm truncate" id="dispName"></div>
                        <div class="text-[11px] text-secondary-500 font-mono truncate" id="dispPhone"></div>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <div class="flex flex-col items-end mr-1">
                             <span class="text-[9px] text-secondary-400 font-bold uppercase">Success</span>
                             <span class="text-xs font-black text-emerald-600 leading-none" id="statRatio">-%</span>
                        </div>
                        <button type="button" onclick="clearCustomer()" class="text-[9px] sm:text-[10px] text-red-600 font-bold px-2 py-0.5 bg-white border border-red-200 rounded-md hover:bg-red-50 transition-colors cursor-pointer">CHANGE</button>
                    </div>
                </div>
            </div>

            <!-- Merge Alert -->
            <div id="mergeAlert" class="hidden mt-2 bg-amber-50 border border-amber-200 rounded-xl p-2.5 text-xs text-amber-900 flex items-center gap-2">
                <ion-icon name="alert-circle" class="text-xl text-amber-500 shrink-0"></ion-icon>
                <div>
                     <div class="font-bold">Pending Order Found</div>
                     <div class="text-[11px] text-amber-700">New items will merge into existing pending order...</div>
                </div>
            </div>
        </div>

        <!-- Cart Items List Container -->
        <div class="flex-1 overflow-y-auto p-0 min-w-0">
            <div class="px-3.5 py-2 bg-secondary-50 border-b border-secondary-100 flex items-center justify-between text-xs font-bold text-secondary-600 sticky top-0 z-10 bg-white/95 backdrop-blur-xs">
                <div class="flex items-center gap-1.5">
                    <ion-icon name="cart-outline" class="text-sm text-primary-600"></ion-icon>
                    <span>কার্ট আইটেম</span>
                    <span id="cartHeaderBadge" class="ml-1 px-1.5 py-0.2 rounded-full bg-primary-100 text-primary-700 font-bold text-[10px]">0</span>
                </div>
                <button type="button" onclick="clearEntireCart()" class="text-[11px] font-bold text-red-500 hover:text-red-700 flex items-center gap-1 cursor-pointer transition-colors" title="কার্ট খালি করুন">
                    <ion-icon name="trash-outline"></ion-icon>
                    <span>খালি করুন</span>
                </button>
            </div>
            
            <div id="cartItems" class="divide-y divide-secondary-100 min-w-0"></div>
            
            <div id="emptyCartMsg" class="text-center text-secondary-400 py-10 sm:py-14 px-4 text-xs sm:text-sm italic flex flex-col items-center">
                <div class="w-14 h-14 rounded-2xl bg-secondary-100 flex items-center justify-center mb-2.5 text-secondary-400">
                    <ion-icon name="cart-outline" class="text-3xl opacity-50"></ion-icon>
                </div>
                <span class="font-bold text-secondary-700 text-sm not-italic">কার্ট বর্তমানে খালি!</span>
                <span class="text-xs text-secondary-400 mt-0.5 not-italic">পণ্য তালিকা থেকে আইটেম যোগ করুন</span>
                <button type="button" onclick="switchToMobileTab('products')" class="lg:hidden mt-3.5 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors flex items-center gap-1.5 cursor-pointer">
                    <ion-icon name="grid-outline" class="text-base"></ion-icon>
                    <span>🛍️ পণ্য ব্রাউজ করুন</span>
                </button>
            </div>
        </div>

        <!-- Totals & Order Summary -->
        <div class="p-2.5 sm:p-3.5 border-t border-secondary-100 bg-secondary-50/90 shrink-0 space-y-1.5 sm:space-y-2">
            <!-- Subtotal -->
            <div class="flex justify-between items-center text-xs sm:text-sm">
                <span class="text-secondary-600 font-semibold">Subtotal</span>
                <span class="font-bold text-secondary-900 text-sm" id="cartSubtotal">৳ 0.00</span>
            </div>

            <!-- Counter Discount Field -->
            <div class="flex justify-between items-center text-xs sm:text-sm">
                <div class="flex items-center gap-1">
                    <span class="text-secondary-600 font-medium">Counter Discount</span>
                    <span class="text-[9px] text-red-500 font-bold bg-red-50 px-1 py-0.2 rounded border border-red-100">DISCOUNT</span>
                </div>
                <div class="flex items-center w-22 sm:w-24">
                    <span class="text-secondary-500 mr-1 text-xs">৳</span>
                    <input type="number" id="orderDiscount" value="0" min="0" oninput="calculateTotal()" 
                           class="w-full text-right px-2 py-1 text-xs font-bold border border-secondary-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-primary-500 bg-white">
                </div>
            </div>

            <!-- Coupon Code Field -->
            <div class="flex justify-between items-center text-xs sm:text-sm">
                <div class="flex items-center gap-1">
                    <span class="text-secondary-600 font-medium">কুপন কোড</span>
                    <span class="text-[9px] text-emerald-600 font-bold bg-emerald-50 px-1 py-0.2 rounded border border-emerald-100">PROMO</span>
                </div>
                <div class="flex items-center gap-1 w-32 sm:w-36">
                    <input type="text" id="orderCouponCode" placeholder="SODAI50" 
                           class="w-full uppercase text-center px-1.5 py-1 text-xs font-mono font-bold border border-secondary-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-primary-500 bg-white">
                    <button type="button" onclick="applyPosCoupon()" class="px-2 py-1 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold text-[10px] rounded-lg transition-colors cursor-pointer shrink-0">Apply</button>
                </div>
            </div>

            <!-- Express Delivery Toggle Option -->
            <?php 
                $expEnabled = ($settings['express_delivery_enabled'] ?? '0') == '1';
                $expCharge = floatval($settings['express_delivery_charge'] ?? 60);
                $expTime = !empty($settings['express_delivery_time']) && $settings['express_delivery_time'] !== '30' ? $settings['express_delivery_time'] : '২ ঘন্টা';
                $expTimeLabel = is_numeric($expTime) ? $expTime . '-মি.' : $expTime;
            ?>
            <?php if ($expEnabled): ?>
            <div class="flex justify-between items-center text-xs bg-orange-50/70 p-1.5 rounded-lg border border-orange-200/80">
                <label for="posExpressCheck" class="flex items-center gap-1 text-orange-950 font-bold cursor-pointer select-none text-[10px] sm:text-[11px]">
                    <span>⚡</span>
                    <span>এক্সপ্রেস <?= htmlspecialchars($expTimeLabel) ?> (+৳<?= number_format($expCharge) ?>)</span>
                </label>
                <input type="checkbox" id="posExpressCheck" onchange="togglePosExpress(this)" 
                       class="w-4 h-4 text-orange-600 rounded border-orange-300 focus:ring-orange-500 cursor-pointer">
            </div>
            <?php endif; ?>

            <!-- Delivery Charge Input -->
            <div class="flex justify-between items-center text-xs sm:text-sm">
                <div>
                    <span class="text-secondary-600 font-medium">Delivery Charge</span>
                    <span class="text-[10px] text-secondary-400 block sm:inline sm:ml-1">(৳ 5000+ ফ্রি)</span>
                </div>
                <div class="flex items-center w-22 sm:w-24">
                    <span class="text-secondary-500 mr-1 text-xs">৳</span>
                    <input type="number" id="deliveryCharge" value="0" min="0" oninput="calculateTotal()" 
                           class="w-full text-right px-2 py-1 text-xs font-bold border border-secondary-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-primary-500 bg-white">
                </div>
            </div>

            <!-- Mark Delivered Checkbox (for Counter Sales) -->
            <div class="pt-1.5 border-t border-secondary-200/80">
                <label class="flex items-center gap-2 cursor-pointer select-none text-[11px] sm:text-xs font-semibold text-secondary-700 bg-white/70 p-1.5 rounded-lg border border-secondary-200/60">
                    <input type="checkbox" id="markDeliveredCheck" checked class="w-4 h-4 text-primary-600 rounded border-secondary-300 focus:ring-primary-500">
                    <span>Mark as Delivered & Paid (Cash)</span>
                </label>
            </div>
            
            <!-- Grand Total -->
            <div class="flex justify-between items-center pt-1.5 sm:pt-2 border-t border-secondary-200">
                <span class="text-secondary-900 font-bold text-sm sm:text-base">Grand Total</span>
                <span class="font-black text-primary-600 text-lg sm:text-xl" id="cartTotal">৳ 0.00</span>
            </div>

            <!-- Action Button -->
            <button type="button" onclick="placeOrder()" 
                    class="w-full bg-primary-600 hover:bg-primary-700 active:scale-98 text-white font-bold py-2.5 px-4 rounded-xl flex justify-center items-center gap-2 transition-all shadow-md cursor-pointer text-xs sm:text-sm">
                <span id="btnText">Confirm Order</span>
                <ion-icon name="arrow-forward" class="text-base sm:text-lg"></ion-icon> 
            </button>
        </div>
    </div>
</div>

<!-- Mobile Floating Sticky Cart Bar (Always pinned to bottom of screen on mobile) -->
<div id="mobileFloatingCartBar" 
     onclick="switchToMobileTab('cart')" 
     class="hidden lg:hidden items-center justify-between cursor-pointer select-none"
     style="position: fixed !important; bottom: 12px !important; left: 12px !important; right: 12px !important; z-index: 99999 !important; background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-radius: 18px; padding: 10px 14px; box-shadow: 0 12px 30px -4px rgba(0, 0, 0, 0.4), 0 6px 14px -3px rgba(0, 0, 0, 0.25); border: 1px solid rgba(51, 65, 85, 0.6);">
    <div class="flex items-center gap-3 min-w-0">
        <!-- Thumbnail Preview / Badge -->
        <div class="relative shrink-0 flex items-center">
            <div id="mobileFloatImgPreview" class="w-10 h-10 rounded-xl bg-white/10 border border-white/20 p-1 flex items-center justify-center overflow-hidden">
                <ion-icon name="cart" class="text-xl text-primary-400"></ion-icon>
            </div>
            <span id="mobileFloatCount" class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-red-500 text-white font-black text-[10px] flex items-center justify-center shadow-xs border border-secondary-900">0</span>
        </div>
        <div class="min-w-0">
            <div class="text-[10px] text-secondary-300 font-semibold tracking-wide">কার্ট ও সর্বমোট</div>
            <div class="text-sm sm:text-base font-black text-emerald-400 leading-tight" id="mobileFloatTotal">৳ 0.00</div>
        </div>
    </div>
    <div class="flex items-center gap-1.5 text-xs font-bold bg-primary-600 hover:bg-primary-500 active:scale-95 text-white px-3.5 py-2 rounded-xl shrink-0 transition-all shadow-md">
        <span>কার্ট ও চেকআউট</span>
        <ion-icon name="arrow-forward-outline" class="text-base"></ion-icon>
    </div>
</div>

<!-- Quick Customer Creation Modal -->
<div id="quickCustomerModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 hidden">
    <div class="bg-white rounded-2xl shadow-2xl border border-secondary-100 w-full max-w-md max-h-[92vh] flex flex-col overflow-hidden">
        <div class="flex justify-between items-center p-3.5 sm:p-4 border-b border-secondary-100 shrink-0">
            <h3 class="font-bold text-secondary-900 text-sm sm:text-base">Register New Customer</h3>
            <button type="button" onclick="closeQuickCustomerModal()" class="text-secondary-400 hover:text-secondary-600 p-1">
                <ion-icon name="close-outline" class="text-2xl"></ion-icon>
            </button>
        </div>
        <form id="quickCustomerForm" onsubmit="saveQuickCustomer(event)" class="p-3.5 sm:p-4 overflow-y-auto space-y-3">
            <div>
                <label class="block text-xs font-bold text-secondary-700 mb-1">Customer Name *</label>
                <input type="text" id="qcName" required placeholder="Full Name" class="w-full px-3 py-2 border border-secondary-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-secondary-700 mb-1">Phone Number *</label>
                <input type="text" id="qcPhone" required placeholder="017xxxxxxxx" class="w-full px-3 py-2 border border-secondary-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-secondary-700 mb-1">Address Details</label>
                <input type="text" id="qcAddress" placeholder="Street, Area or Landmark" class="w-full px-3 py-2 border border-secondary-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-secondary-700 mb-1">Pin Location on Map (Optional)</label>
                <div id="qcMap" class="w-full h-32 sm:h-40 rounded-xl border border-secondary-300 relative z-0"></div>
                <input type="hidden" id="qcLatitude">
                <input type="hidden" id="qcLongitude">
                <p class="text-[10px] text-secondary-500 mt-1">Click on the map or drag the marker to pinpoint location.</p>
            </div>
            <div class="pt-2 flex justify-end gap-2 border-t border-secondary-100">
                <button type="button" onclick="closeQuickCustomerModal()" class="px-3.5 py-2 text-xs font-bold text-secondary-600 bg-secondary-100 hover:bg-secondary-200 rounded-xl transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-primary-600 hover:bg-primary-700 rounded-xl transition-colors shadow-sm">
                    Save & Select
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    /* POS Mobile & Desktop Responsiveness Architecture */
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
        .pos-desktop-only {
            display: none !important;
        }
        /* Tighten admin main header spacing on mobile so POS has max viewport */
        main > header {
            margin-bottom: 0.5rem !important;
        }
    }
    @media (min-width: 1024px) {
        #posProductsPanel {
            display: flex !important;
            flex: 1 1 0% !important;
            min-width: 0 !important;
        }
        #posCartPanel {
            display: flex !important;
            flex: 0 0 380px !important;
            max-width: 440px !important;
        }
        .pos-mobile-bar,
        #mobileFloatingCartBar {
            display: none !important;
        }
        .pos-desktop-only {
            display: flex !important;
        }
    }

    /* Floating sticky cart bar style */
    #mobileFloatingCartBar {
        position: fixed !important;
        bottom: 14px !important;
        left: 12px !important;
        right: 12px !important;
        z-index: 99999 !important;
    }

    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    
    @keyframes bounceSubtle {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-4px); }
    }
    .animate-bounce-subtle {
        animation: bounceSubtle 2.5s ease-in-out infinite;
    }
</style>

<script>
    // Global Constants & Path Base
    const baseUrl = '<?= $base ?>';

    // Global Data & Cart State
    let cart = [];
    let pendingOrder = null;
    let selectedCustId = null;
    let filterCatId = 'all';
    let currentMobileTab = 'products';

    const allCategories = <?= !empty($categories) ? json_encode($categories) : '[]' ?>;

    // Settings
    const defaultDeliveryCharge = <?= floatval($settings['delivery_charge_default'] ?? 60) ?>;
    const freeDeliveryThreshold = <?= floatval($settings['delivery_free_threshold'] ?? 5000) ?>;

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
                btnProd.className = "flex-1 py-2 px-2.5 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition-all text-secondary-600 hover:bg-secondary-100 cursor-pointer";
            }
            if (btnCart) {
                btnCart.className = "flex-1 py-2 px-2.5 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition-all bg-primary-600 text-white shadow-xs cursor-pointer";
            }

            // Hide floating bar when on Cart tab
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
                btnProd.className = "flex-1 py-2 px-2.5 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition-all bg-primary-600 text-white shadow-xs cursor-pointer";
            }
            if (btnCart) {
                btnCart.className = "flex-1 py-2 px-2.5 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition-all text-secondary-600 hover:bg-secondary-100 cursor-pointer";
            }

            // Show floating bar if cart has items
            const totalItems = cart.reduce((sum, item) => sum + item.qty, 0);
            if (mobileFloatBar && totalItems > 0 && window.innerWidth < 1024) {
                mobileFloatBar.style.display = 'flex';
            }
        }
    }

    // Auto-adjust tabs on window resize
    window.addEventListener('resize', () => {
        if (window.innerWidth < 1024) {
            switchToMobileTab(currentMobileTab);
        }
    });

    // --- Circular Hierarchical Category Logic ---
    let currentParentId = null; // null = root categories

    function getCategoryChildren(parentId) {
        return allCategories.filter(c => {
            if (parentId === null) return !c.parent_id || c.parent_id == 0;
            return c.parent_id == parentId;
        });
    }

    // Check if category or any of its descendants matches
    function getDescendantCategoryIds(catId) {
        let ids = [parseInt(catId)];
        let queue = [parseInt(catId)];
        while (queue.length > 0) {
            const cur = queue.shift();
            const children = allCategories.filter(c => c.parent_id == cur);
            for (const child of children) {
                const childId = parseInt(child.id);
                if (!ids.includes(childId)) {
                    ids.push(childId);
                    queue.push(childId);
                }
            }
        }
        return ids;
    }

    function renderCategories(parentId = null) {
        currentParentId = parentId;
        const container = document.getElementById('categoryFilter');
        if (!container) return;
        container.innerHTML = '';

        // "Back" Button if inside subcategory
        if (parentId !== null) {
            const currentObj = allCategories.find(c => c.id == parentId);
            const grandParentId = currentObj && currentObj.parent_id ? currentObj.parent_id : null;
            
            const backBtn = document.createElement('button');
            backBtn.type = 'button';
            backBtn.className = "cat-btn flex flex-col items-center gap-1 min-w-[50px] sm:min-w-[58px] group transition-all shrink-0 cursor-pointer";
            backBtn.onclick = () => renderCategories(grandParentId);
            backBtn.innerHTML = `
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-secondary-100 hover:bg-secondary-200 text-secondary-600 flex items-center justify-center shadow-xs border border-secondary-200">
                    <ion-icon name="arrow-back" class="text-lg sm:text-xl"></ion-icon>
                </div>
                <span class="text-[9px] sm:text-[10px] font-bold text-secondary-600">Back</span>
            `;
            container.appendChild(backBtn);
        } else {
            // "All" Button
            const allBtn = document.createElement('button');
            allBtn.type = 'button';
            allBtn.className = `cat-btn flex flex-col items-center gap-1 min-w-[50px] sm:min-w-[58px] group transition-all shrink-0 cursor-pointer ${filterCatId === 'all' ? 'active' : 'opacity-70 hover:opacity-100'}`;
            allBtn.onclick = () => { 
                filterCatId = 'all'; 
                renderCategories(null); 
                applyProductFilters(); 
            };
            allBtn.innerHTML = `
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full ${filterCatId === 'all' ? 'bg-primary-600 text-white ring-2 ring-primary-600 ring-offset-2' : 'bg-secondary-100 text-secondary-600'} flex items-center justify-center shadow-sm">
                    <ion-icon name="apps" class="text-xl sm:text-2xl"></ion-icon>
                </div>
                <span class="text-[9px] sm:text-[10px] font-bold ${filterCatId === 'all' ? 'text-primary-600' : 'text-secondary-700'}">All</span>
            `;
            container.appendChild(allBtn);
        }

        // Render Children for this level
        const children = getCategoryChildren(parentId);
        
        children.forEach(cat => {
            const hasSub = allCategories.some(c => c.parent_id == cat.id);
            const isActive = (filterCatId == cat.id);
            
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = `cat-btn flex flex-col items-center gap-1 min-w-[54px] sm:min-w-[62px] group transition-all shrink-0 cursor-pointer ${isActive ? 'active opacity-100 scale-105' : 'opacity-75 hover:opacity-100'}`;
            
            btn.onclick = () => {
                filterCatId = cat.id;
                if (hasSub) {
                    renderCategories(cat.id);
                } else {
                    renderCategories(parentId);
                }
                applyProductFilters();
            };

            const imgHtml = cat.image_path 
                ? `<img src="${cat.image_path}" class="w-full h-full object-contain p-1">`
                : `<span class="text-sm sm:text-base font-bold text-secondary-400 uppercase">${cat.name.charAt(0)}</span>`;

            btn.innerHTML = `
                <div class="relative w-10 h-10 sm:w-12 sm:h-12 rounded-full overflow-hidden flex items-center justify-center shadow-2xs transition-all ${isActive ? 'ring-3 ring-primary-600 ring-offset-2 border-2 border-primary-600 bg-primary-100/90 shadow-md' : 'bg-secondary-50 border border-secondary-200 group-hover:border-primary-500 group-hover:shadow-sm'}">
                    ${imgHtml}
                    ${isActive ? `<span class="absolute top-0 right-0 w-3 h-3 bg-primary-600 text-white rounded-full flex items-center justify-center text-[7px] font-black shadow-xs">✓</span>` : ''}
                </div>
                <span class="text-[9px] sm:text-[10px] ${isActive ? 'text-primary-700 font-black' : 'text-secondary-600 font-semibold'} text-center leading-tight max-w-[56px] sm:max-w-[64px] truncate group-hover:text-primary-600" title="${cat.name}">
                    ${cat.name} ${hasSub ? '›' : ''}
                </span>
            `;
            container.appendChild(btn);
        });

        // Auto-scroll active item into view
        const activeBtn = container.querySelector('.cat-btn.active');
        if (activeBtn) {
            activeBtn.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
        }
    }

    // Initialize Categories on Load
    document.addEventListener('DOMContentLoaded', () => {
        renderCategories(null);
    });

    // Search and Filter Products
    document.getElementById('productSearch').addEventListener('input', applyProductFilters);

    function clearSearch() {
        document.getElementById('productSearch').value = '';
        filterCatId = 'all';
        renderCategories(null);
        applyProductFilters();
    }

    function clearCategoryFilter() {
        filterCatId = 'all';
        renderCategories(null);
        applyProductFilters();
    }

    function applyProductFilters() {
        const query = document.getElementById('productSearch').value.trim().toLowerCase();
        let visibleCount = 0;

        let allowedCategoryIds = null;
        if (filterCatId !== 'all') {
            allowedCategoryIds = getDescendantCategoryIds(filterCatId);
        }

        document.querySelectorAll('.pos-product-card').forEach(card => {
            const name = card.dataset.name || '';
            const sku = card.dataset.sku || '';
            const catId = parseInt(card.dataset.category || 0);

            const matchQuery = !query || name.includes(query) || sku.includes(query);
            const matchCat = (filterCatId === 'all') || (allowedCategoryIds && allowedCategoryIds.includes(catId));

            if (matchQuery && matchCat) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Update active category indicator banner at top
        const activeHeader = document.getElementById('activeCategoryHeader');
        const activeTitle = document.getElementById('activeCategoryTitle');
        const activeCount = document.getElementById('activeCategoryCount');

        if (activeHeader) {
            if (filterCatId !== 'all') {
                const activeCatObj = allCategories.find(c => c.id == filterCatId);
                const catName = activeCatObj ? activeCatObj.name : 'ক্যাটাগরি #' + filterCatId;
                if (activeTitle) activeTitle.innerText = catName;
                if (activeCount) activeCount.innerText = visibleCount + ' টি পণ্য';
                activeHeader.classList.remove('hidden');
                activeHeader.classList.add('flex');
            } else {
                activeHeader.classList.add('hidden');
                activeHeader.classList.remove('flex');
            }
        }

        const noRes = document.getElementById('noResults');
        if (visibleCount === 0) {
            noRes.classList.remove('hidden');
            noRes.classList.add('flex');
        } else {
            noRes.classList.add('hidden');
            noRes.classList.remove('flex');
        }
    }

    // Switch active variant on a product card
    function selectPosCardVariant(pillBtn, title, price, qty) {
        const card = pillBtn.closest('.pos-product-card');
        if (!card) return;

        // 1. Update data attributes
        card.dataset.activeVariantTitle = title;
        card.dataset.activeVariantPrice = price;
        card.dataset.activeVariantQty = qty;

        // 2. Update price display on card
        const priceEl = card.querySelector('.pos-card-price');
        if (priceEl) {
            priceEl.textContent = Number(price).toLocaleString('en-US');
        }

        // 3. Highlight selected pill
        card.querySelectorAll('.pos-variant-btn').forEach(b => {
            b.className = 'pos-variant-btn px-1 sm:px-1.5 py-0.5 rounded text-[9px] sm:text-[10px] border transition-all cursor-pointer border-secondary-200 text-secondary-500 bg-white hover:border-secondary-300 font-medium';
        });
        pillBtn.className = 'pos-variant-btn px-1 sm:px-1.5 py-0.5 rounded text-[9px] sm:text-[10px] border transition-all cursor-pointer border-primary-600 text-primary-700 bg-primary-50 font-bold active-pos-variant shadow-2xs';
    }

    // Add selected product / variant to POS cart
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

        // Unique cart item identifier (product_id + variant_title)
        const cartKey = productId + '_' + variantTitle;
        const existing = cart.find(i => i.cartKey === cartKey);

        if (existing) {
            existing.qty++;
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
                maxStock: maxStock,
                isOutOfStock: isOutOfStock,
                baseUnit: baseUnit
            });
        }

        // Update preview thumbnail in floating cart bar
        const floatPreview = document.getElementById('mobileFloatImgPreview');
        if (floatPreview && image) {
            floatPreview.innerHTML = `<img src="${image}" alt="${name}" class="w-full h-full object-contain">`;
        }

        // Brief visual feedback on button
        const origHtml = btn.innerHTML;
        btn.innerHTML = '<ion-icon name="checkmark-outline" class="text-sm"></ion-icon> <span>Added</span>';
        btn.classList.add('bg-emerald-600', 'text-white');
        setTimeout(() => {
            btn.innerHTML = origHtml;
            btn.classList.remove('bg-emerald-600', 'text-white');
        }, 600);

        renderCart();
    }

    // Change Cart Item Quantity
    function changeQty(cartKey, delta) {
        const item = cart.find(i => i.cartKey === cartKey);
        if (!item) return;

        const newQty = item.qty + delta;
        if (newQty <= 0) {
            removeItem(cartKey);
            return;
        }

        item.qty = newQty;
        renderCart();
    }

    // Manual input for quantity
    function manualQty(cartKey, val) {
        const item = cart.find(i => i.cartKey === cartKey);
        if (!item) return;

        let v = parseInt(val);
        if (isNaN(v) || v < 1) v = 1;

        item.qty = v;
        renderCart();
    }

    // Remove Item from Cart
    function removeItem(cartKey) {
        cart = cart.filter(i => i.cartKey !== cartKey);
        renderCart();
    }

    // Clear Entire Cart
    function clearEntireCart() {
        if (cart.length === 0) return;
        if (confirm('আপনি কি নিশ্চিত যে সম্পূর্ণ কার্ট খালি করতে চান?')) {
            cart = [];
            const floatPreview = document.getElementById('mobileFloatImgPreview');
            if (floatPreview) {
                floatPreview.innerHTML = '<ion-icon name="cart" class="text-xl text-primary-400"></ion-icon>';
            }
            renderCart();
        }
    }

    // Render POS Cart Items (with product thumbnails & touch controls)
    function renderCart() {
        const container = document.getElementById('cartItems');
        const emptyMsg = document.getElementById('emptyCartMsg');
        
        let subtotal = 0;
        const totalItems = cart.reduce((sum, item) => sum + item.qty, 0);
        
        if (cart.length === 0) {
            container.innerHTML = '';
            emptyMsg.style.display = 'flex';
        } else {
            emptyMsg.style.display = 'none';
            container.innerHTML = cart.map(item => `
                <div class="p-2.5 sm:p-3 hover:bg-secondary-50/70 transition-colors flex items-center gap-2.5 sm:gap-3 group">
                    <!-- Product Image Thumbnail -->
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-secondary-50 border border-secondary-200 p-1 flex items-center justify-center shrink-0 overflow-hidden shadow-2xs">
                        <img src="${item.image || baseUrl + '/images/default-product.svg'}" 
                             alt="${item.name}" 
                             class="w-full h-full object-contain"
                             onerror="this.src='${baseUrl}/images/default-product.svg'">
                    </div>
                    
                    <!-- Item Info & Controls -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-1.5">
                            <div class="min-w-0 flex-1">
                                <h5 class="font-bold text-secondary-900 text-xs sm:text-sm line-clamp-1 leading-snug" title="${item.name}">${item.name}</h5>
                                <div class="flex items-center gap-1 flex-wrap mt-0.5">
                                    ${item.variant_title ? `
                                        <span class="text-[9px] sm:text-[10px] font-bold text-primary-700 bg-primary-50 border border-primary-200/80 px-1.5 py-0.2 rounded">
                                            ${item.variant_title}
                                        </span>
                                    ` : ''}
                                    ${(item.isOutOfStock || (item.qty * item.baseQty > item.maxStock)) ? `
                                        <span class="text-[8px] sm:text-[9px] font-bold text-red-600 bg-red-50 border border-red-200 px-1.5 py-0.2 rounded shadow-2xs" title="স্টক শেষ হলেও অর্ডারে গ্রহণ করা হয়েছে">
                                            স্টক আউট
                                        </span>
                                    ` : ''}
                                    <span class="text-[10px] sm:text-[11px] text-secondary-400 font-medium">৳ ${Number(item.price).toLocaleString()} / ইউনিট</span>
                                </div>
                            </div>
                            
                            <!-- Remove Item -->
                            <button type="button" onclick="removeItem('${item.cartKey}')" class="text-secondary-300 hover:text-red-500 transition-colors p-1 rounded-lg hover:bg-red-50 cursor-pointer shrink-0" title="রিমুভ করুন">
                                <ion-icon name="trash-outline" class="text-base"></ion-icon>
                            </button>
                        </div>
                        
                        <!-- Stepper & Price Row -->
                        <div class="flex items-center justify-between mt-2 pt-1 border-t border-dashed border-secondary-100">
                            <div class="inline-flex items-center border border-secondary-200 rounded-lg overflow-hidden h-7 bg-white shadow-2xs">
                                <button type="button" onclick="changeQty('${item.cartKey}', -1)" class="w-6 sm:w-7 h-full bg-secondary-50 hover:bg-secondary-100 flex items-center justify-center text-secondary-700 font-black text-xs cursor-pointer active:bg-secondary-200 transition-colors">−</button>
                                <input type="number" value="${item.qty}" onchange="manualQty('${item.cartKey}', this.value)" class="w-8 sm:w-9 h-full text-center text-xs border-none focus:ring-0 p-0 text-secondary-900 font-bold bg-white">
                                <button type="button" onclick="changeQty('${item.cartKey}', 1)" class="w-6 sm:w-7 h-full bg-secondary-50 hover:bg-secondary-100 flex items-center justify-center text-secondary-700 font-black text-xs cursor-pointer active:bg-secondary-200 transition-colors">+</button>
                            </div>
                            <div class="text-right">
                                <span class="text-xs sm:text-sm font-black text-primary-700">৳ ${Number(item.price * item.qty).toLocaleString()}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
            
            subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        }

        if (pendingOrder) {
            subtotal += parseFloat(pendingOrder.total_amount);
        }
        
        // Auto-apply delivery fee logic
        const deliveryInput = document.getElementById('deliveryCharge');
        let charge = defaultDeliveryCharge;
        if (subtotal >= freeDeliveryThreshold || subtotal === 0) {
            charge = 0;
        }
        deliveryInput.value = charge;

        document.getElementById('cartSubtotal').innerText = '৳ ' + subtotal.toFixed(2);
        calculateTotal();

        // Update Mobile UI Badges and Floating Bar
        const mobileTabBadge = document.getElementById('mobileTabBadge');
        const mobileTabPrice = document.getElementById('mobileTabPrice');
        const mobileFloatBar = document.getElementById('mobileFloatingCartBar');
        const mobileFloatCount = document.getElementById('mobileFloatCount');
        const mobileFloatTotal = document.getElementById('mobileFloatTotal');
        const mobileFloatImgPreview = document.getElementById('mobileFloatImgPreview');
        const cartHeaderBadge = document.getElementById('cartHeaderBadge');
        const mobileCartHeaderCount = document.getElementById('mobileCartHeaderCount');
        const desktopCartBadge = document.getElementById('desktopCartBadge');

        if (cartHeaderBadge) cartHeaderBadge.innerText = totalItems;
        if (mobileCartHeaderCount) mobileCartHeaderCount.innerText = totalItems + ' আইটেম';
        if (desktopCartBadge) desktopCartBadge.innerText = totalItems + ' আইটেম';

        if (mobileTabBadge) {
            if (totalItems > 0) {
                mobileTabBadge.innerText = totalItems;
                mobileTabBadge.classList.remove('hidden');
            } else {
                mobileTabBadge.classList.add('hidden');
            }
        }
        if (mobileTabPrice) {
            mobileTabPrice.innerText = '৳ ' + Math.round(subtotal);
        }
        if (mobileFloatBar) {
            if (totalItems > 0 && currentMobileTab === 'products' && window.innerWidth < 1024) {
                mobileFloatBar.style.display = 'flex';
                if (mobileFloatCount) mobileFloatCount.innerText = totalItems;
                if (mobileFloatTotal) mobileFloatTotal.innerText = '৳ ' + subtotal.toFixed(2);
                
                // Show thumbnail of latest item in sticky cart bar
                if (mobileFloatImgPreview && cart.length > 0) {
                    const lastItem = cart[cart.length - 1];
                    if (lastItem && lastItem.image) {
                        mobileFloatImgPreview.innerHTML = `<img src="${lastItem.image}" alt="${lastItem.name}" class="w-full h-full object-contain">`;
                    }
                }
            } else {
                mobileFloatBar.style.display = 'none';
            }
        }
    }

    // Recalculate Grand Total with Discount & Delivery
    function calculateTotal() {
        const subText = document.getElementById('cartSubtotal').innerText.replace(/[^\d.-]/g, '');
        const subtotal = parseFloat(subText) || 0;
        const discount = parseFloat(document.getElementById('orderDiscount').value) || 0;
        const delivery = parseFloat(document.getElementById('deliveryCharge').value) || 0;
        
        const total = Math.max(0, subtotal - discount + delivery);
        document.getElementById('cartTotal').innerText = '৳ ' + total.toFixed(2);
    }

    const posExpressFee = <?= floatval($settings['express_delivery_charge'] ?? 60) ?>;
    let isPosExpressActive = false;

    function togglePosExpress(cb) {
        isPosExpressActive = cb.checked;
        const delivInput = document.getElementById('deliveryCharge');
        let currentDeliv = parseFloat(delivInput.value) || 0;
        if (isPosExpressActive) {
            delivInput.value = currentDeliv + posExpressFee;
        } else {
            delivInput.value = Math.max(0, currentDeliv - posExpressFee);
        }
        calculateTotal();
    }

    async function applyPosCoupon() {
        const codeInput = document.getElementById('orderCouponCode');
        const code = (codeInput ? codeInput.value : '').trim().toUpperCase();
        if (!code) {
            alert('কুপন কোড লিখুন (যেমন: SODAI50)');
            return;
        }
        const subText = document.getElementById('cartSubtotal').innerText.replace(/[^\d.-]/g, '');
        const subtotal = parseFloat(subText) || 0;

        try {
            const res = await fetch(`${baseUrl}/api/apply-coupon`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ code: code, subtotal: subtotal })
            });
            const data = await res.json();
            if (data.success) {
                document.getElementById('orderDiscount').value = data.discount;
                calculateTotal();
                alert(data.message);
            } else {
                alert(data.message);
            }
        } catch(e) {
            alert('কুপন যাচাই করতে সমস্যা হয়েছে');
        }
    }

    // Live Customer Search
    const custSearch = document.getElementById('customerSearchInput');
    const custResults = document.getElementById('customerSearchResults');
    let searchTimeout;

    custSearch.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        const term = e.target.value.trim();
        
        if (term.length < 1) {
            custResults.classList.add('hidden');
            return;
        }

        searchTimeout = setTimeout(async () => {
            try {
                const res = await fetch(`${baseUrl}/admin/customers/search-api?q=${encodeURIComponent(term)}`);
                const data = await res.json();
                
                if (data.length > 0) {
                    custResults.innerHTML = data.map(c => `
                        <div onclick="selectCustomer('${c.id}', '${c.name.replace(/'/g, "\\'")}', '${c.phone}')" class="p-3 hover:bg-secondary-50 cursor-pointer transition-colors block">
                            <div class="font-bold text-secondary-900 text-xs sm:text-sm">${c.name}</div>
                            <div class="text-xs text-secondary-500 flex justify-between mt-1">
                                <span>${c.phone}</span>
                                <span class="bg-secondary-100 text-secondary-600 px-1.5 rounded font-mono text-[10px]">${c.unique_code || ''}</span>
                            </div>
                        </div>
                    `).join('');
                    custResults.classList.remove('hidden');
                } else {
                    custResults.innerHTML = '<div class="p-3 text-xs text-secondary-400 italic text-center">No customer found</div>';
                    custResults.classList.remove('hidden');
                }
            } catch(e) { console.error(e); }
        }, 250);
    });

    // Hide dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!custSearch.contains(e.target) && !custResults.contains(e.target)) {
            custResults.classList.add('hidden');
        }
    });

    // Select a Customer
    async function selectCustomer(id, name, phone) {
        selectedCustId = id;
        document.getElementById('custSearchWrapper').classList.add('hidden');
        document.getElementById('selectedCustomerDisplay').classList.remove('hidden');
        document.getElementById('dispName').innerText = name;
        document.getElementById('dispPhone').innerText = phone || 'ID: ' + id;
        custResults.classList.add('hidden'); 
        
        // Reset Stats
        document.getElementById('statRatio').innerText = '...';
        document.getElementById('statOrders').innerText = '...';

        try {
            const res = await fetch(`${baseUrl}/admin/orders/check-pending?customer_id=${id}`);
            const data = await res.json();
            
            if (data.success) {
                if (data.stats) {
                    document.getElementById('statRatio').innerText = data.stats.success_ratio + '%';
                    document.getElementById('statOrders').innerText = data.stats.total_orders;
                    
                    const ratioEl = document.getElementById('statRatio');
                    if (data.stats.success_ratio < 50 && data.stats.total_orders > 2) {
                         ratioEl.className = "text-base font-black text-red-600 leading-none";
                    } else if (data.stats.success_ratio > 90) {
                         ratioEl.className = "text-base font-black text-emerald-600 leading-none";
                    } else {
                         ratioEl.className = "text-base font-black text-amber-600 leading-none";
                    }
                }

                if (data.exists) {
                    pendingOrder = data.order;
                    document.getElementById('mergeAlert').classList.remove('hidden');
                    document.getElementById('btnText').innerText = 'Merge & Confirm Order';
                    renderCart();
                }
            }
        } catch(e) {}
    }
    
    // Clear Selected Customer
    function clearCustomer() {
        selectedCustId = null;
        pendingOrder = null;
        document.getElementById('custSearchWrapper').classList.remove('hidden');
        document.getElementById('selectedCustomerDisplay').classList.add('hidden');
        document.getElementById('mergeAlert').classList.add('hidden');
        document.getElementById('btnText').innerText = 'Confirm Order';
        document.getElementById('customerSearchInput').focus();
        renderCart();
    }

    // Quick Customer Modal
    let qcMap = null;
    let qcMarker = null;

    function openQuickCustomerModal() {
        document.getElementById('quickCustomerModal').classList.remove('hidden');
        document.getElementById('qcName').focus();
        
        if (!qcMap) {
            setTimeout(() => {
                var defaultLat = 23.8103;
                var defaultLng = 90.4125;
                
                qcMap = L.map('qcMap').setView([defaultLat, defaultLng], 12);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(qcMap);

                qcMarker = L.marker([defaultLat, defaultLng], {draggable: true}).addTo(qcMap);

                function updateQcLocation(lat, lng) {
                    document.getElementById('qcLatitude').value = lat;
                    document.getElementById('qcLongitude').value = lng;
                }

                qcMarker.on('dragend', function(event) {
                    var position = qcMarker.getLatLng();
                    updateQcLocation(position.lat, position.lng);
                });

                qcMap.on('click', function(e) {
                    qcMarker.setLatLng(e.latlng);
                    updateQcLocation(e.latlng.lat, e.latlng.lng);
                });
            }, 200);
        }
    }

    function closeQuickCustomerModal() {
        document.getElementById('quickCustomerModal').classList.add('hidden');
    }

    async function saveQuickCustomer(e) {
        e.preventDefault();
        const name = document.getElementById('qcName').value.trim();
        const phone = document.getElementById('qcPhone').value.trim();
        const address = document.getElementById('qcAddress').value.trim();

        if (!name || !phone) {
            alert('Please enter Name and Phone number!');
            return;
        }

        const formData = new FormData();
        formData.append('name', name);
        formData.append('phone', phone);
        formData.append('address_details', address);
        formData.append('area_id', 1); // Default area if not specified

        const lat = document.getElementById('qcLatitude').value;
        const lng = document.getElementById('qcLongitude').value;
        if (lat) formData.append('latitude', lat);
        if (lng) formData.append('longitude', lng);

        try {
            const res = await fetch(`${baseUrl}/admin/customers/store`, {
                method: 'POST',
                body: formData
            });
            
            closeQuickCustomerModal();
            // Automatically search and select this new customer
            const searchRes = await fetch(`${baseUrl}/admin/customers/search-api?q=${encodeURIComponent(phone)}`);
            const searchData = await searchRes.json();
            if (searchData.length > 0) {
                selectCustomer(searchData[0].id, searchData[0].name, searchData[0].phone);
            }
        } catch(err) {
            alert('Error saving customer');
        }
    }

    // Submit / Place Order
    async function placeOrder() {
        if (!selectedCustId) {
            alert('Please select a customer first!');
            // If on mobile and in products view, switch to cart view
            switchToMobileTab('cart');
            document.getElementById('customerSearchInput').focus();
            return;
        }
        if (cart.length === 0) {
            alert('No products added to cart!');
            switchToMobileTab('products');
            return;
        }
        
        const markDelivered = document.getElementById('markDeliveredCheck')?.checked || false;
        const subtotalText = document.getElementById('cartSubtotal').innerText.replace(/[^\d.-]/g, '');
        const grandTotal = parseFloat(document.getElementById('cartTotal').innerText.replace(/[^\d.-]/g, '')) || 0;
        const discountVal = parseFloat(document.getElementById('orderDiscount').value) || 0;
        const deliveryCharge = parseFloat(document.getElementById('deliveryCharge').value) || 0;

        const payload = {
            customer_id: selectedCustId,
            total: grandTotal,
            delivery_charge: deliveryCharge,
            discount_amount: discountVal,
            mark_delivered: markDelivered,
            items: cart.map(i => ({
                product_id: i.id,
                unit_title: i.variant_title || null,
                base_qty: i.baseQty || 1,
                quantity: i.qty,
                price: i.price
            }))
        };

        const btn = document.querySelector('button[onclick="placeOrder()"]');
        btn.disabled = true;
        btn.innerHTML = '<span class="animate-spin inline-block mr-2">⟳</span> Processing...';

        try {
            const res = await fetch(`${baseUrl}/admin/orders/store`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '<?= \Core\CSRF::token() ?>'
                },
                body: JSON.stringify(payload)
            });
            
            const text = await res.text();
            let data;
            try {
                data = JSON.parse(text);
            } catch (err) {
                console.error('Server Response:', text);
                throw new Error('Server returned invalid response');
            }

            if (data.success) {
                // If marked delivered from POS, offer immediate 3" receipt popup or redirect to invoice
                if (markDelivered) {
                    window.open(baseUrl + '/admin/orders/pos-receipt?id=' + data.order_id, '_blank', 'width=400,height=600');
                }
                window.location.href = data.redirect;
            } else {
                alert(data.error || 'Error saving order');
                btn.disabled = false;
                btn.innerHTML = '<span id="btnText">Confirm Order</span><ion-icon name="arrow-forward" class="text-lg"></ion-icon>';
            }
        } catch(e) { 
            alert('Request failed: ' + e.message); 
            console.error(e);
            btn.disabled = false;
            btn.innerHTML = '<span id="btnText">Confirm Order</span><ion-icon name="arrow-forward" class="text-lg"></ion-icon>';
        }
    }

    // Auto-select customer if preselected via URL query (?customer_id=X)
    <?php if (!empty($selectedCustomerId) && !empty($customers)): ?>
        <?php 
            $preselected = null;
            foreach ($customers as $c) {
                if ($c['id'] == $selectedCustomerId) {
                    $preselected = $c;
                    break;
                }
            }
            if ($preselected):
        ?>
        document.addEventListener('DOMContentLoaded', () => {
            selectCustomer('<?= $preselected['id'] ?>', '<?= addslashes($preselected['name']) ?>', '<?= $preselected['phone'] ?>');
        });
        <?php endif; ?>
    <?php endif; ?>
</script>
