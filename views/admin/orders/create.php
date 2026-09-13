<?php
// views/admin/orders/create.php

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

<div class="h-[calc(100vh-140px)] flex flex-col lg:flex-row gap-3.5 min-w-0 w-full overflow-hidden">
    
    <!-- LEFT PANEL: Products Catalog & Search -->
    <div class="flex-1 min-w-0 flex flex-col bg-white rounded-2xl shadow-sm border border-secondary-200 overflow-hidden h-full">
        
        <!-- Search & Category Header -->
        <div class="p-3 border-b border-secondary-100 flex flex-col gap-2.5 bg-white shrink-0 min-w-0">
            <!-- Search Row -->
            <div class="flex items-center gap-2">
                <div class="relative flex-1 min-w-0">
                    <input type="text" id="productSearch" 
                           placeholder="Search product by name, SKU or barcode..." 
                           class="w-full pl-9 pr-4 py-2 bg-secondary-50 border border-secondary-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all shadow-2xs"
                           autocomplete="off">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-secondary-400">
                        <ion-icon name="search-outline" class="text-base"></ion-icon>
                    </div>
                </div>
                <button type="button" onclick="clearSearch()" class="px-3 py-2 text-xs font-bold text-secondary-600 hover:text-secondary-900 bg-secondary-100 hover:bg-secondary-200 rounded-xl transition-colors shrink-0">
                    Reset
                </button>
            </div>

            <!-- Circular Visual Category Icons Bar (with Images & Subcategories) -->
            <div class="flex gap-3 overflow-x-auto pb-1 pt-0.5 scrollbar-hide select-none w-full min-w-0" id="categoryFilter">
                <!-- Injected via JavaScript with circular category icons & drill-down -->
            </div>
        </div>
        
        <!-- Products Grid (6 Columns on Desktop) -->
        <div class="flex-1 min-w-0 overflow-y-auto p-3 bg-secondary-50/70">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-2.5" id="productsGrid">
                <?php if (!empty($products)): foreach ($products as $p): ?>
                    <?php 
                        $vInfo = getPosProductVariants($p);
                        $hasDiscount = \Models\Product::hasDiscount($p);
                        $discountPercent = $hasDiscount ? \Models\Product::getDiscountPercent($p) : 0;
                        $discountAmount = $hasDiscount ? \Models\Product::getDiscountAmount($p) : 0;
                        $stockNum = floatval($p['stock_qty']);
                        $stockClean = (floor($stockNum) == $stockNum) ? intval($stockNum) : rtrim(rtrim(number_format($stockNum, 3), '0'), '.');
                        $isOutOfStock = ($stockNum <= 0);
                        $baseUnit = $p['base_unit'] ?? 'pcs';
                    ?>
                    <div class="pos-product-card bg-white p-2.5 rounded-2xl shadow-xs border border-secondary-200 hover:border-primary-500 hover:shadow-md transition-all flex flex-col h-full group relative min-w-0 <?= $isOutOfStock ? 'opacity-65' : '' ?>"
                         data-product-id="<?= $p['id'] ?>"
                         data-category="<?= $p['category_id'] ?? '' ?>"
                         data-name="<?= htmlspecialchars(strtolower($p['name'])) ?>"
                         data-sku="<?= htmlspecialchars(strtolower($p['sku'] ?? '')) ?>"
                         data-stock="<?= $stockNum ?>"
                         data-base-unit="<?= htmlspecialchars($baseUnit) ?>"
                         data-regular-price="<?= floatval($p['regular_price'] ?? 0) ?>"
                         data-active-variant-title="<?= htmlspecialchars($vInfo['default_title']) ?>"
                         data-active-variant-price="<?= $vInfo['default_price'] ?>"
                         data-active-variant-qty="<?= $vInfo['default_qty'] ?>">
                         
                        <!-- Top Badges -->
                        <div class="flex items-center justify-between gap-1 mb-1.5">
                            <div class="flex items-center gap-1 min-w-0">
                                <?php if (!empty($p['category_name'])): ?>
                                    <span class="text-[9px] font-semibold text-secondary-500 bg-secondary-100 px-1.5 py-0.2 rounded truncate max-w-[65px]">
                                        <?= htmlspecialchars($p['category_name']) ?>
                                    </span>
                                <?php endif; ?>

                                <?php if ($hasDiscount): ?>
                                    <span class="text-[9px] font-black text-white bg-red-500 px-1 py-0.2 rounded shadow-2xs">
                                        <?= $discountPercent ?>% OFF
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Stock Badge -->
                            <?php if ($isOutOfStock): ?>
                                <span class="text-[9px] font-bold text-red-600 bg-red-50 border border-red-200 px-1 py-0.2 rounded shrink-0">
                                    Out of Stock
                                </span>
                            <?php else: ?>
                                <span class="text-[9px] font-bold text-amber-800 bg-amber-50 border border-amber-200 px-1.5 py-0.2 rounded shrink-0">
                                    Stock: <?= $stockClean ?> <?= htmlspecialchars($baseUnit) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Product Image (Compact & Proportioned for 6 Columns) -->
                        <div class="mb-1.5 bg-secondary-50/80 rounded-xl overflow-hidden shrink-0 flex items-center justify-center p-1.5 h-20 sm:h-22 border border-secondary-100">
                            <?php 
                            $posImg = !empty($p['image_path']) ? htmlspecialchars($p['image_path']) : '/sodai-dorkar/public/images/default-product.svg';
                            ?>
                            <img src="<?= $posImg ?>" 
                                 alt="<?= htmlspecialchars($p['name']) ?>" 
                                 class="max-h-full max-w-full object-contain drop-shadow-2xs transition-transform duration-200 group-hover:scale-105"
                                 loading="lazy"
                                 onerror="this.src='/sodai-dorkar/public/images/default-product.svg'">
                        </div>

                        <!-- Product Title -->
                        <h4 class="font-semibold text-secondary-900 text-xs line-clamp-2 h-7 leading-tight mb-1 group-hover:text-primary-600 transition-colors" 
                            title="<?= htmlspecialchars($p['name']) ?>">
                            <?= htmlspecialchars($p['name']) ?>
                        </h4>

                        <!-- Price Row -->
                        <div class="flex items-baseline gap-1 mb-1.5 flex-wrap">
                            <span class="text-xs font-bold text-primary-700">৳</span>
                            <span class="pos-card-price text-sm font-black text-primary-600 leading-none">
                                <?= number_format($vInfo['default_price']) ?>
                            </span>

                            <?php if ($hasDiscount): ?>
                                <span class="text-[10px] text-secondary-400 line-through font-medium leading-none">
                                    ৳ <?= number_format($p['regular_price']) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Multi-Unit Variant Selector Pills -->
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
                                        class="pos-variant-btn px-1.5 py-0.5 rounded text-[10px] border transition-all cursor-pointer <?= $pillClass ?>">
                                    <?= htmlspecialchars($v['title']) ?>
                                </button>
                            <?php endforeach; ?>
                        </div>

                        <!-- Add Button -->
                        <button type="button" 
                                onclick="addCardProductToCart(this)" 
                                <?= $isOutOfStock ? 'disabled' : '' ?>
                                class="w-full py-1 px-2 rounded-lg <?= $isOutOfStock ? 'bg-secondary-200 text-secondary-400 cursor-not-allowed' : 'bg-primary-50 hover:bg-primary-600 text-primary-700 hover:text-white border border-primary-200 hover:border-primary-600 active:scale-95' ?> font-bold text-xs flex items-center justify-center gap-1 transition-all shadow-xs cursor-pointer mt-auto">
                            <ion-icon name="cart-outline" class="text-sm"></ion-icon>
                            <span><?= $isOutOfStock ? 'Out of Stock' : '+ Add' ?></span>
                        </button>
                    </div>
                <?php endforeach; endif; ?>

                <div id="noResults" class="hidden col-span-full py-14 flex flex-col items-center justify-center text-secondary-400">
                    <ion-icon name="search-outline" class="text-5xl mb-2 opacity-30"></ion-icon>
                    <p class="text-sm font-semibold">No products found</p>
                    <p class="text-xs text-secondary-400 mt-0.5">Try searching with a different keyword or category</p>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL: POS Cart & Checkout Details -->
    <div class="w-full lg:w-80 xl:w-96 min-w-[310px] max-w-[380px] flex flex-col bg-white rounded-2xl shadow-sm border border-secondary-200 h-full overflow-hidden shrink-0">
        
        <!-- Customer Selection Box -->
        <div class="p-3.5 border-b border-secondary-100 bg-secondary-50/80">
            <div class="flex justify-between items-center mb-1.5">
                <label class="block text-secondary-700 text-xs font-bold uppercase tracking-wider">Customer</label>
                <button type="button" onclick="openQuickCustomerModal()" class="text-xs font-bold text-primary-600 hover:text-primary-700 flex items-center gap-1">
                    <ion-icon name="person-add-outline"></ion-icon> + New Customer
                </button>
            </div>
            
            <div class="relative" id="custSearchWrapper">
                <input type="text" id="customerSearchInput" placeholder="Type Name, Phone or ID..." 
                       class="w-full px-3 py-2 bg-white border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm shadow-2xs" 
                       autocomplete="off">
                <!-- Live Search Dropdown -->
                <div id="customerSearchResults" class="hidden absolute z-50 left-0 right-0 bg-white border border-secondary-200 shadow-xl rounded-xl mt-1 max-h-56 overflow-y-auto divide-y divide-secondary-100"></div>
                <div class="text-[10px] text-secondary-400 mt-1">Typing automatically searches customers...</div>
            </div>
            
            <!-- Selected Customer Display Card -->
            <div id="selectedCustomerDisplay" class="hidden bg-white border border-emerald-300 bg-emerald-50/40 p-0 rounded-xl mt-1 relative overflow-hidden shadow-xs">
                <div class="flex">
                    <div class="p-3 flex-1">
                        <div class="font-bold text-secondary-900 text-sm" id="dispName"></div>
                        <div class="text-xs text-secondary-500 font-mono mt-0.5" id="dispPhone"></div>
                        <div class="flex gap-2 mt-2">
                             <span class="text-[10px] text-emerald-700 font-bold px-1.5 py-0.5 bg-emerald-100 rounded border border-emerald-200">SELECTED</span>
                             <button type="button" onclick="clearCustomer()" class="text-[10px] text-red-600 font-bold px-2 py-0.5 bg-white border border-red-200 rounded hover:bg-red-50 transition-colors">CHANGE</button>
                        </div>
                    </div>
                    
                    <!-- Stats Area -->
                    <div class="w-24 bg-white border-l border-emerald-100 p-2 flex flex-col justify-center items-center gap-1">
                         <div class="flex flex-col items-center">
                             <span class="text-[9px] text-secondary-400 uppercase font-bold tracking-wider">Success</span>
                             <span class="text-base font-black text-emerald-600 leading-none" id="statRatio">-%</span>
                         </div>
                         <div class="w-full h-px bg-secondary-100 my-0.5"></div>
                         <div class="flex flex-col items-center">
                             <span class="text-[9px] text-secondary-400 uppercase font-bold tracking-wider">Orders</span>
                             <span class="text-xs font-bold text-secondary-700 leading-none" id="statOrders">-</span>
                         </div>
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

        <!-- Cart Items List -->
        <div class="flex-1 overflow-y-auto p-0">
            <table class="w-full text-left text-sm">
                <thead class="bg-secondary-50 text-secondary-500 sticky top-0 z-10 shadow-2xs border-b border-secondary-100 bg-white">
                    <tr>
                        <th class="px-3 py-2 text-xs font-bold">Item & Unit</th>
                        <th class="px-2 py-2 text-center w-24 text-xs font-bold">Qty</th>
                        <th class="px-2 py-2 text-right w-20 text-xs font-bold">Price</th>
                        <th class="px-2 py-2 w-8"></th>
                    </tr>
                </thead>
                <tbody id="cartItems" class="divide-y divide-secondary-100"></tbody>
            </table>
            
            <div id="emptyCartMsg" class="text-center text-secondary-400 py-12 text-sm italic flex flex-col items-center">
                <ion-icon name="cart-outline" class="text-4xl mb-2 opacity-25"></ion-icon>
                <span>Cart is empty! Add products to cart</span>
            </div>
        </div>

        <!-- Totals & Order Summary -->
        <div class="p-3.5 border-t border-secondary-100 bg-secondary-50/90 shrink-0 space-y-2">
            <!-- Subtotal -->
            <div class="flex justify-between items-center text-sm">
                <span class="text-secondary-600 font-medium">Subtotal</span>
                <span class="font-bold text-secondary-800" id="cartSubtotal">৳ 0.00</span>
            </div>

            <!-- Counter Discount Field -->
            <div class="flex justify-between items-center text-sm">
                <div class="flex items-center gap-1">
                    <span class="text-secondary-600 font-medium">Counter Discount</span>
                    <span class="text-[9px] text-red-500 font-bold bg-red-50 px-1 py-0.2 rounded border border-red-100">DISCOUNT</span>
                </div>
                <div class="flex items-center w-24">
                    <span class="text-secondary-500 mr-1 text-xs">৳</span>
                    <input type="number" id="orderDiscount" value="0" min="0" oninput="calculateTotal()" 
                           class="w-full text-right px-2 py-1 text-xs font-bold border border-secondary-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-primary-500 bg-white">
                </div>
            </div>

            <!-- Delivery Charge Input -->
            <div class="flex justify-between items-center text-sm">
                <span class="text-secondary-600 font-medium">Delivery Charge</span>
                <div class="flex items-center w-24">
                    <span class="text-secondary-500 mr-1 text-xs">৳</span>
                    <input type="number" id="deliveryCharge" value="0" min="0" oninput="calculateTotal()" 
                           class="w-full text-right px-2 py-1 text-xs font-bold border border-secondary-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-primary-500 bg-white">
                </div>
            </div>

            <!-- Mark Delivered Checkbox (for Counter Sales) -->
            <div class="pt-1.5 border-t border-secondary-200/80">
                <label class="flex items-center gap-2 cursor-pointer select-none text-xs font-semibold text-secondary-700">
                    <input type="checkbox" id="markDeliveredCheck" checked class="w-4 h-4 text-primary-600 rounded border-secondary-300 focus:ring-primary-500">
                    <span>Mark as Delivered & Paid (Cash)</span>
                </label>
            </div>
            
            <!-- Grand Total -->
            <div class="flex justify-between items-center pt-2 border-t border-secondary-200">
                <span class="text-secondary-900 font-bold text-base">Grand Total</span>
                <span class="font-black text-primary-600 text-xl" id="cartTotal">৳ 0.00</span>
            </div>

            <!-- Action Button -->
            <button type="button" onclick="placeOrder()" 
                    class="w-full bg-primary-600 hover:bg-primary-700 active:scale-98 text-white font-bold py-2.5 px-4 rounded-xl flex justify-center items-center gap-2 transition-all shadow-md cursor-pointer">
                <span id="btnText">Confirm Order</span>
                <ion-icon name="arrow-forward" class="text-lg"></ion-icon> 
            </button>
        </div>
    </div>
</div>

<!-- Quick Customer Creation Modal -->
<div id="quickCustomerModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl shadow-2xl border border-secondary-100 w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4 pb-2 border-b border-secondary-100">
            <h3 class="font-bold text-secondary-900 text-base">Register New Customer</h3>
            <button type="button" onclick="closeQuickCustomerModal()" class="text-secondary-400 hover:text-secondary-600 p-1">
                <ion-icon name="close-outline" class="text-2xl"></ion-icon>
            </button>
        </div>
        <form id="quickCustomerForm" onsubmit="saveQuickCustomer(event)" class="space-y-3.5">
            <div>
                <label class="block text-xs font-bold text-secondary-700 mb-1">Customer Name *</label>
                <input type="text" id="qcName" required placeholder="Full Name" class="w-full px-3 py-2 border border-secondary-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-secondary-700 mb-1">Phone Number *</label>
                <input type="text" id="qcPhone" required placeholder="017xxxxxxxx" class="w-full px-3 py-2 border border-secondary-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-secondary-700 mb-1">Address Details</label>
                <input type="text" id="qcAddress" placeholder="Street, Area or Landmark" class="w-full px-3 py-2 border border-secondary-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-secondary-700 mb-1">Pin Location on Map (Optional)</label>
                <div id="qcMap" class="w-full h-40 rounded-xl border border-secondary-300 relative z-0"></div>
                <input type="hidden" id="qcLatitude">
                <input type="hidden" id="qcLongitude">
                <p class="text-[10px] text-secondary-500 mt-1">Click on the map or drag the marker to pinpoint location.</p>
            </div>
            <div class="pt-2 flex justify-end gap-2">
                <button type="button" onclick="closeQuickCustomerModal()" class="px-4 py-2 text-xs font-bold text-secondary-600 bg-secondary-100 hover:bg-secondary-200 rounded-xl transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-primary-600 hover:bg-primary-700 rounded-xl transition-colors shadow-sm">
                    Save & Select
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<script>
    // Global Data & Cart State
    let cart = [];
    let pendingOrder = null;
    let selectedCustId = null;
    let filterCatId = 'all';

    const allCategories = <?= !empty($categories) ? json_encode($categories) : '[]' ?>;

    // Settings
    const defaultDeliveryCharge = <?= floatval($settings['delivery_charge_default'] ?? 60) ?>;
    const freeDeliveryThreshold = <?= floatval($settings['delivery_free_threshold'] ?? 5000) ?>;

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
            backBtn.className = "cat-btn flex flex-col items-center gap-1 min-w-[58px] group transition-all shrink-0 cursor-pointer";
            backBtn.onclick = () => renderCategories(grandParentId);
            backBtn.innerHTML = `
                <div style="width: 48px; height: 48px;" class="rounded-full bg-secondary-100 hover:bg-secondary-200 text-secondary-600 flex items-center justify-center shadow-xs border border-secondary-200">
                    <ion-icon name="arrow-back" class="text-xl"></ion-icon>
                </div>
                <span class="text-[10px] font-bold text-secondary-600">Back</span>
            `;
            container.appendChild(backBtn);
        } else {
            // "All" Button
            const allBtn = document.createElement('button');
            allBtn.type = 'button';
            allBtn.className = `cat-btn flex flex-col items-center gap-1 min-w-[58px] group transition-all shrink-0 cursor-pointer ${filterCatId === 'all' ? 'active' : 'opacity-70 hover:opacity-100'}`;
            allBtn.onclick = () => { 
                filterCatId = 'all'; 
                renderCategories(null); 
                applyProductFilters(); 
            };
            allBtn.innerHTML = `
                <div style="width: 48px; height: 48px;" class="rounded-full ${filterCatId === 'all' ? 'bg-primary-600 text-white ring-2 ring-primary-600 ring-offset-2' : 'bg-secondary-100 text-secondary-600'} flex items-center justify-center shadow-sm">
                    <ion-icon name="apps" class="text-2xl"></ion-icon>
                </div>
                <span class="text-[10px] font-bold ${filterCatId === 'all' ? 'text-primary-600' : 'text-secondary-700'}">All</span>
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
            btn.className = `cat-btn flex flex-col items-center gap-1 min-w-[60px] group transition-all shrink-0 cursor-pointer ${isActive ? 'active opacity-100' : 'opacity-75 hover:opacity-100'}`;
            
            btn.onclick = () => {
                if (hasSub) {
                    renderCategories(cat.id);
                } else {
                    filterCatId = cat.id;
                    renderCategories(parentId);
                    applyProductFilters();
                }
            };

            const imgHtml = cat.image_path 
                ? `<img src="${cat.image_path}" class="w-full h-full object-contain p-1">`
                : `<span class="text-base font-bold text-secondary-400 uppercase">${cat.name.charAt(0)}</span>`;

            btn.innerHTML = `
                <div style="width: 48px; height: 48px;" class="rounded-full bg-secondary-50 border border-secondary-200 overflow-hidden flex items-center justify-center shadow-2xs transition-all group-hover:border-primary-500 group-hover:shadow-md ${isActive ? 'ring-2 ring-primary-600 ring-offset-2 border-primary-600 bg-primary-50/50' : ''}">
                    ${imgHtml}
                </div>
                <span class="text-[10px] font-semibold ${isActive ? 'text-primary-600 font-bold' : 'text-secondary-600'} text-center leading-tight max-w-[64px] truncate group-hover:text-primary-600" title="${cat.name}">
                    ${cat.name} ${hasSub ? '›' : ''}
                </span>
            `;
            container.appendChild(btn);
        });
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
            b.className = 'pos-variant-btn px-1.5 py-0.5 rounded text-[10px] border transition-all cursor-pointer border-secondary-200 text-secondary-500 bg-white hover:border-secondary-300 font-medium';
        });
        pillBtn.className = 'pos-variant-btn px-1.5 py-0.5 rounded text-[10px] border transition-all cursor-pointer border-primary-600 text-primary-700 bg-primary-50 font-bold active-pos-variant shadow-2xs';
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
        const baseUnit = card.dataset.baseUnit || 'pcs';

        if (maxStock <= 0) {
            alert('Item is out of stock!');
            return;
        }

        // Unique cart item identifier (product_id + variant_title)
        const cartKey = productId + '_' + variantTitle;
        const existing = cart.find(i => i.cartKey === cartKey);

        if (existing) {
            if ((existing.qty + 1) * existing.baseQty > maxStock) {
                alert(`Stock limit reached! Available stock: ${maxStock} ${baseUnit}`);
                return;
            }
            existing.qty++;
        } else {
            if (baseQty > maxStock) {
                alert(`Stock limit reached! Available stock: ${maxStock} ${baseUnit}`);
                return;
            }
            cart.push({
                cartKey: cartKey,
                id: productId,
                name: name,
                variant_title: variantTitle,
                price: price,
                baseQty: baseQty,
                qty: 1,
                maxStock: maxStock,
                baseUnit: baseUnit
            });
        }

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

        if (newQty * item.baseQty > item.maxStock) {
            alert(`Stock limit reached! Available stock: ${item.maxStock} ${item.baseUnit}`);
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

        if (v * item.baseQty > item.maxStock) {
            alert(`Stock limit reached! Available stock: ${item.maxStock} ${item.baseUnit}`);
            v = Math.floor(item.maxStock / item.baseQty) || 1;
        }

        item.qty = v;
        renderCart();
    }

    // Remove Item from Cart
    function removeItem(cartKey) {
        cart = cart.filter(i => i.cartKey !== cartKey);
        renderCart();
    }

    // Render POS Cart Table
    function renderCart() {
        const container = document.getElementById('cartItems');
        const emptyMsg = document.getElementById('emptyCartMsg');
        
        let subtotal = 0;
        
        if (cart.length === 0) {
            container.innerHTML = '';
            emptyMsg.style.display = 'flex';
        } else {
            emptyMsg.style.display = 'none';
            container.innerHTML = cart.map(item => `
                <tr class="group hover:bg-secondary-50 transition-colors">
                    <td class="px-3 py-2 align-middle">
                        <div class="font-bold text-secondary-900 text-xs leading-snug line-clamp-2" title="${item.name}">${item.name}</div>
                        ${item.variant_title ? `
                            <span class="inline-block mt-0.5 text-[10px] font-bold text-primary-700 bg-primary-50 border border-primary-200 px-1.5 py-0.2 rounded">
                                ${item.variant_title}
                            </span>
                        ` : ''}
                        <div class="text-[10px] text-secondary-400 mt-0.5">৳ ${item.price.toFixed(0)} / unit</div>
                    </td>
                    <td class="px-1 py-1 align-middle">
                        <div class="flex items-center justify-center border border-secondary-200 rounded-lg overflow-hidden h-7 w-20 bg-white shadow-2xs">
                            <button type="button" onclick="changeQty('${item.cartKey}', -1)" class="w-6 h-full bg-secondary-50 hover:bg-secondary-100 flex items-center justify-center text-secondary-600 transition-colors border-r border-secondary-200 font-black text-xs cursor-pointer">−</button>
                            <input type="number" value="${item.qty}" onchange="manualQty('${item.cartKey}', this.value)" class="w-8 h-full text-center text-xs border-none focus:ring-0 p-0 text-secondary-900 font-bold appearance-none bg-white">
                            <button type="button" onclick="changeQty('${item.cartKey}', 1)" class="w-6 h-full bg-secondary-50 hover:bg-secondary-100 flex items-center justify-center text-secondary-600 transition-colors border-l border-secondary-200 font-black text-xs cursor-pointer">+</button>
                        </div>
                    </td>
                    <td class="px-2 py-2 text-right align-middle font-bold text-secondary-900 text-xs">
                        ৳ ${(item.price * item.qty).toFixed(0)}
                    </td>
                    <td class="px-1 py-1 text-center align-middle">
                        <button type="button" onclick="removeItem('${item.cartKey}')" class="text-secondary-400 hover:text-red-500 transition-colors p-1.5 rounded-lg hover:bg-red-50 cursor-pointer" title="Remove">
                            <ion-icon name="trash-outline" class="text-base"></ion-icon>
                        </button>
                    </td>
                </tr>
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
                const res = await fetch(`/sodai-dorkar/public/admin/customers/search-api?q=${encodeURIComponent(term)}`);
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
            const res = await fetch(`/sodai-dorkar/public/admin/orders/check-pending?customer_id=${id}`);
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
            const res = await fetch('/sodai-dorkar/public/admin/customers/store', {
                method: 'POST',
                body: formData
            });
            
            closeQuickCustomerModal();
            // Automatically search and select this new customer
            const searchRes = await fetch(`/sodai-dorkar/public/admin/customers/search-api?q=${encodeURIComponent(phone)}`);
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
            document.getElementById('customerSearchInput').focus();
            return;
        }
        if (cart.length === 0) {
            alert('No products added to cart!');
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
            const res = await fetch('/sodai-dorkar/public/admin/orders/store', {
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
                    window.open('/sodai-dorkar/public/admin/orders/pos-receipt?id=' + data.order_id, '_blank', 'width=400,height=600');
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
