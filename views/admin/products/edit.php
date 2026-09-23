<?php
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
?>
<div class="max-w-5xl mx-auto mb-16">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-secondary-900">Edit Product</h2>
            <p class="text-secondary-500 text-xs mt-1">Manage product specifications, unit conversions, pricing strategies, and size variants.</p>
        </div>
        <a href="<?= $base ?>/admin/products" class="text-secondary-600 hover:text-primary-600 flex items-center font-medium transition-colors">
            <ion-icon name="arrow-back-outline" class="mr-2 text-xl"></ion-icon>
            Back to Products
        </a>
    </div>

    <!-- 1-Click Quick Preset Selector -->
    <div class="bg-gradient-to-r from-emerald-50 via-teal-50 to-blue-50 border border-emerald-200/80 rounded-2xl p-5 mb-8 shadow-xs">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <span class="p-1.5 bg-emerald-600 text-white rounded-lg flex items-center justify-center text-sm shadow-xs">
                    <ion-icon name="flash-outline"></ion-icon>
                </span>
                <h3 class="font-bold text-emerald-900 text-sm">1-Click Quick Presets (Packaging Templates)</h3>
            </div>
            <span class="text-xs text-emerald-700 font-medium">Click any template to auto-fill units, ratios and customer variants</span>
        </div>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5">
            <button type="button" onclick="applyUnitPreset('sack_kg')" 
                    class="preset-btn flex flex-col items-center justify-center p-3 rounded-xl bg-white border border-emerald-200 hover:border-emerald-500 hover:bg-emerald-50/50 hover:shadow-md transition-all text-center group">
                <span class="text-2xl mb-1 group-hover:scale-110 transition-transform">🌾</span>
                <span class="font-bold text-xs text-secondary-800">Sack Rice / Lentils</span>
                <span class="text-[10px] text-secondary-500 mt-0.5">50 kg Sack ➔ kg/gm</span>
            </button>

            <button type="button" onclick="applyUnitPreset('drum_liter')" 
                    class="preset-btn flex flex-col items-center justify-center p-3 rounded-xl bg-white border border-emerald-200 hover:border-emerald-500 hover:bg-emerald-50/50 hover:shadow-md transition-all text-center group">
                <span class="text-2xl mb-1 group-hover:scale-110 transition-transform">🛢️</span>
                <span class="font-bold text-xs text-secondary-800">Drum / Bulk Oil</span>
                <span class="text-[10px] text-secondary-500 mt-0.5">190 L Drum ➔ Liter/ml</span>
            </button>

            <button type="button" onclick="applyUnitPreset('box_piece')" 
                    class="preset-btn flex flex-col items-center justify-center p-3 rounded-xl bg-white border border-emerald-200 hover:border-emerald-500 hover:bg-emerald-50/50 hover:shadow-md transition-all text-center group">
                <span class="text-2xl mb-1 group-hover:scale-110 transition-transform">📦</span>
                <span class="font-bold text-xs text-secondary-800">Box Soap / Biscuits</span>
                <span class="text-[10px] text-secondary-500 mt-0.5">Box (24 pcs) ➔ pcs</span>
            </button>

            <button type="button" onclick="applyUnitPreset('loose_kg')" 
                    class="preset-btn flex flex-col items-center justify-center p-3 rounded-xl bg-white border border-emerald-200 hover:border-emerald-500 hover:bg-emerald-50/50 hover:shadow-md transition-all text-center group">
                <span class="text-2xl mb-1 group-hover:scale-110 transition-transform">⚖️</span>
                <span class="font-bold text-xs text-secondary-800">Loose kg & gm</span>
                <span class="text-[10px] text-secondary-500 mt-0.5">250 gm, 500 gm, 1 kg</span>
            </button>

            <button type="button" onclick="applyUnitPreset('piece_standard')" 
                    class="preset-btn flex flex-col items-center justify-center p-3 rounded-xl bg-white border border-emerald-200 hover:border-emerald-500 hover:bg-emerald-50/50 hover:shadow-md transition-all text-center group">
                <span class="text-2xl mb-1 group-hover:scale-110 transition-transform">🏷️</span>
                <span class="font-bold text-xs text-secondary-800">Standard Piece / Packet</span>
                <span class="text-[10px] text-secondary-500 mt-0.5">1 Piece = 1 Unit</span>
            </button>
        </div>
    </div>

    <form action="<?= $base ?>/admin/products/update" method="POST" enctype="multipart/form-data" id="edit-product-form">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left 2 Cols: Main Configuration -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- 1. Basic Information Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-secondary-200/80 p-6 sm:p-7">
                    <div class="flex items-center gap-3 border-b border-secondary-100 pb-4 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-primary-50 border border-primary-100 flex items-center justify-center text-primary-600 shadow-2xs">
                            <ion-icon name="document-text" class="text-xl"></ion-icon>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-secondary-900">Basic Information</h3>
                            <p class="text-xs text-secondary-500 mt-0.5">General product title, taxonomy, branding, and suppliers.</p>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="block text-secondary-700 text-xs font-bold uppercase tracking-wider mb-2" for="name">
                            Product Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" 
                               class="w-full px-4 py-2.5 border border-secondary-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm font-medium" required>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label class="block text-secondary-700 text-xs font-bold uppercase tracking-wider mb-2" for="sku">
                                SKU / Barcode
                            </label>
                            <input type="text" id="sku" name="sku" value="<?php echo htmlspecialchars($product['sku']); ?>" 
                                   class="w-full px-4 py-2.5 border border-secondary-300 rounded-xl font-mono text-xs focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>

                        <div>
                            <label class="block text-secondary-700 text-xs font-bold uppercase tracking-wider mb-2">Brand</label>
                            <div class="relative">
                                <select name="brand_id" class="w-full px-4 py-2.5 border border-secondary-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 appearance-none bg-white">
                                    <option value="">No Brand</option>
                                    <?php foreach ($brands as $b): 
                                        $isSelected = ($b['id'] == $product['brand_id']) ? 'selected' : '';
                                    ?>
                                        <option value="<?php echo $b['id']; ?>" <?php echo $isSelected; ?>><?php echo htmlspecialchars($b['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-secondary-400">
                                    <ion-icon name="chevron-down-outline"></ion-icon>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cascading Step-by-Step Category Selector -->
                    <div class="mb-5 bg-secondary-50/70 p-4 rounded-2xl border border-secondary-200">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-secondary-800 text-xs font-bold uppercase tracking-wider flex items-center gap-1.5">
                                <ion-icon name="git-branch-outline" class="text-primary-600 text-sm"></ion-icon>
                                Category & Sub-Category Selection <span class="text-red-500">*</span>
                            </label>
                            <span class="text-[11px] text-primary-700 font-semibold flex items-center gap-1">🔍 নাম লিখে খুঁজুন অথবা ধাপে ধাপে সিলেক্ট করুন</span>
                        </div>
                        
                        <input type="hidden" name="category_id" id="edit_category_id" value="<?php echo htmlspecialchars($product['category_id'] ?? ''); ?>" required>
                        
                        <div id="edit_category_cascading_container" class="space-y-2.5">
                            <!-- Injected dynamically by CascadingCategorySelector JS -->
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-1">
                        <div>
                            <label class="block text-secondary-700 text-xs font-bold uppercase tracking-wider mb-2">Supplier / Vendor</label>
                            <div class="relative">
                                <select name="vendor_id" class="w-full px-4 py-2.5 border border-secondary-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 appearance-none bg-white">
                                    <option value="">No Vendor</option>
                                    <?php foreach ($vendors as $v): 
                                        $isSelected = ($v['id'] == $product['vendor_id']) ? 'selected' : '';
                                    ?>
                                        <option value="<?php echo $v['id']; ?>" <?php echo $isSelected; ?>><?php echo htmlspecialchars($v['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-secondary-400">
                                    <ion-icon name="chevron-down-outline"></ion-icon>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-secondary-700 text-xs font-bold uppercase tracking-wider mb-2" for="description">পণ্যের বিস্তারিত বিবরণ (Description)</label>
                            <textarea id="description" name="description" rows="4"
                                      class="w-full px-4 py-3 border border-secondary-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 font-sans leading-relaxed" 
                                      placeholder="পণ্যের বিস্তারিত বিবরণ, পুষ্টিগুণ, ব্যবহারের নিয়ম ইত্যাদি লিখুন..."><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                        </div>

                        <div>
                            <label class="block text-secondary-700 text-xs font-bold uppercase tracking-wider mb-2" for="tags">ট্যাগ ও সার্চ কীওয়ার্ড (Search Keywords / Tags)</label>
                            <input type="text" id="tags" name="tags" 
                                   value="<?php echo htmlspecialchars($product['tags'] ?? ''); ?>"
                                   class="w-full px-4 py-2.5 border border-secondary-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500" 
                                   placeholder="কমা দিয়ে লিখুন, যেমন: চাল, মিনিকেট, basmati, rice, grocery">
                            <p class="text-[11px] text-secondary-400 mt-1 flex items-center gap-1">
                                <ion-icon name="information-circle-outline"></ion-icon>
                                গ্রাহকরা ফ্রন্টএন্ড সার্চ বারে এই শব্দগুলো লিখলে পণ্যটি তৎক্ষণাৎ খুঁজে পাবে।
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 2. Pricing & Selling Strategy (Clean Redesign) -->
                <div class="bg-white rounded-2xl shadow-sm border border-secondary-200/80 p-6 sm:p-7">
                    <div class="flex items-center justify-between border-b border-secondary-100 pb-4 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 shadow-2xs">
                                <ion-icon name="pricetag" class="text-xl"></ion-icon>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-secondary-900">Pricing & Margins</h3>
                                <p class="text-xs text-secondary-500 mt-0.5">Set retail selling price, optional promotional discounts, and monitor profit margins.</p>
                            </div>
                        </div>
                        <!-- Live Profit Margin Badge -->
                        <div id="live-margin-badge" class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs font-bold text-emerald-800">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Margin:</span>
                            <span id="margin-value" class="font-black text-emerald-700">0%</span>
                        </div>
                    </div>

                    <!-- 3 Primary Price Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-6">
                        <!-- 1. Retail Selling Price (Hero Focus) -->
                        <div class="p-4 rounded-2xl bg-gradient-to-b from-emerald-50/50 to-white border-2 border-emerald-500 shadow-xs relative">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-black uppercase tracking-wider text-emerald-950" for="sell_price">
                                    Selling Price <span class="text-red-500">*</span>
                                </label>
                                <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-emerald-600 text-white uppercase tracking-wider">Customer Pays</span>
                            </div>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-emerald-700 font-bold text-sm">Tk</span>
                                <input type="number" step="0.01" id="sell_price" name="sell_price" 
                                       value="<?php echo htmlspecialchars($product['sell_price']); ?>" 
                                       oninput="onUnitSellPriceChange()"
                                       class="w-full pl-10 pr-4 py-2.5 bg-white border border-emerald-300 rounded-xl text-lg font-black text-emerald-800 focus:ring-2 focus:ring-emerald-500 shadow-2xs" required>
                            </div>
                            <p class="text-[11px] text-emerald-700 font-medium mt-1.5">
                                Final retail price per <span class="base-unit-label font-bold"><?php echo htmlspecialchars($product['base_unit'] ?? 'pcs'); ?></span>
                            </p>
                        </div>

                        <!-- 2. Regular Price / MRP -->
                        <div class="p-4 rounded-2xl bg-secondary-50/70 border border-secondary-200">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700" for="regular_price">
                                    Regular Price / MRP
                                </label>
                                <span class="text-[10px] font-medium text-secondary-400">Optional</span>
                            </div>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-secondary-400 font-bold text-sm">Tk</span>
                                <input type="number" step="0.01" id="regular_price" name="regular_price" 
                                       value="<?php echo htmlspecialchars($product['regular_price'] ?? ''); ?>" 
                                       oninput="onRegularPriceInput()"
                                       placeholder="e.g. 250.00" 
                                       class="w-full pl-10 pr-4 py-2.5 bg-white border border-secondary-300 rounded-xl text-base font-bold text-secondary-900 focus:ring-2 focus:ring-primary-500">
                            </div>
                            <p class="text-[11px] text-secondary-400 mt-1.5">Original price displayed as strikethrough</p>
                        </div>

                        <!-- 3. Unit Cost (Buy Price) -->
                        <div class="p-4 rounded-2xl bg-secondary-50/70 border border-secondary-200">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700" for="buy_price">
                                    Unit Buy Cost
                                </label>
                                <span id="unit-profit-pill" class="text-[10px] font-bold text-secondary-600 bg-white px-2 py-0.5 rounded-full border border-secondary-200">
                                    Profit: Tk 0.00
                                </span>
                            </div>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-secondary-400 font-bold text-sm">Tk</span>
                                <input type="number" step="0.01" id="buy_price" name="buy_price" 
                                       value="<?php echo htmlspecialchars($product['buy_price']); ?>" 
                                       oninput="onUnitBuyPriceChange()"
                                       class="w-full pl-10 pr-4 py-2.5 bg-white border border-secondary-300 rounded-xl text-base font-bold text-secondary-900 focus:ring-2 focus:ring-primary-500">
                            </div>
                            <p class="text-[11px] text-secondary-400 mt-1.5">Purchase cost per <span class="base-unit-label font-semibold"><?php echo htmlspecialchars($product['base_unit'] ?? 'pcs'); ?></span></p>
                        </div>
                    </div>

                    <!-- Discount Promotion Controls -->
                    <div class="rounded-2xl border border-secondary-200 bg-secondary-50/50 p-4 sm:p-5">
                        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                            <div class="flex items-center gap-2">
                                <span class="w-7 h-7 rounded-lg bg-red-100 text-red-600 flex items-center justify-center text-sm font-bold">
                                    <ion-icon name="sparkles-outline"></ion-icon>
                                </span>
                                <div>
                                    <h4 class="text-xs font-bold text-secondary-900 uppercase tracking-wider">Promotional Discount</h4>
                                    <span class="text-[11px] text-secondary-500">Select discount calculation method or leave as None</span>
                                </div>
                            </div>
                            <div id="discount-badge-preview" class="text-xs font-black px-3 py-1 rounded-lg shadow-2xs hidden"></div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                            <!-- Toggle Buttons -->
                            <div>
                                <label class="block text-xs font-semibold text-secondary-700 mb-1.5">Discount Method</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <button type="button" onclick="setDiscountType('none')" id="btn-discount-none" 
                                            class="py-2 px-3 text-xs font-bold rounded-xl border transition-all text-center">
                                        No Discount
                                    </button>
                                    <button type="button" onclick="setDiscountType('percent')" id="btn-discount-percent" 
                                            class="py-2 px-3 text-xs font-bold rounded-xl border transition-all text-center">
                                        % Percent
                                    </button>
                                    <button type="button" onclick="setDiscountType('fixed')" id="btn-discount-fixed" 
                                            class="py-2 px-3 text-xs font-bold rounded-xl border transition-all text-center">
                                        Fixed (Tk)
                                    </button>
                                </div>
                                <input type="hidden" id="discount_type" name="discount_type" value="<?php echo htmlspecialchars($product['discount_type'] ?? 'none'); ?>">
                            </div>

                            <!-- Discount Value Input -->
                            <div id="discount-value-container" class="<?php echo ($product['discount_type'] ?? 'none') === 'none' ? 'opacity-50 pointer-events-none' : ''; ?>">
                                <label class="block text-xs font-semibold text-secondary-700 mb-1.5">
                                    Discount Value (<span id="discount-unit-symbol">%</span>)
                                </label>
                                <div class="relative">
                                    <input type="number" step="0.01" min="0" id="discount_value" name="discount_value" 
                                           value="<?php echo floatval($product['discount_value'] ?? 0); ?>" 
                                           oninput="onDiscountValueInput()"
                                           placeholder="0" 
                                           class="w-full px-4 py-2 bg-white border border-secondary-300 rounded-xl text-sm font-bold text-secondary-900 focus:ring-2 focus:ring-primary-500">
                                    <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-xs font-bold text-secondary-400 pointer-events-none" id="discount-suffix">%</span>
                                </div>
                            </div>
                        </div>
                        <div id="discount-summary-msg" class="text-xs text-secondary-600 mt-3 font-medium bg-white/80 p-2.5 rounded-xl border border-secondary-200/60">
                            No discount applicable. Product sells at regular retail price.
                        </div>
                    </div>
                </div>

                <!-- 3. Packaging & Wholesale Conversion Card (Clean Redesign) -->
                <div class="bg-white rounded-2xl shadow-sm border border-secondary-200/80 p-6 sm:p-7">
                    <div class="flex items-center justify-between border-b border-secondary-100 pb-4 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 shadow-2xs">
                                <ion-icon name="cube" class="text-xl"></ion-icon>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-secondary-900">Packaging & Unit Conversion</h3>
                                <p class="text-xs text-secondary-500 mt-0.5">Map supplier wholesale containers (sacks, drums, cartons) to your warehouse inventory unit.</p>
                            </div>
                        </div>
                        <a href="<?= $base ?>/admin/settings/units" target="_blank" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-secondary-50 hover:bg-secondary-100 text-secondary-700 font-bold text-xs transition-colors border border-secondary-200">
                            <ion-icon name="settings-outline" class="text-sm"></ion-icon> Manage Units
                        </a>
                    </div>

                    <input type="hidden" name="unit_type" id="unit_type" value="<?php echo htmlspecialchars($product['unit_type'] ?? 'piece'); ?>">

                    <!-- 3 Unit Columns -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                        <!-- 1. Stock Base Unit -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-2" for="base_unit">
                                Stock Base Unit <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select id="base_unit" name="base_unit" onchange="calculateUnitPricing()" 
                                        class="w-full px-4 py-2.5 border border-secondary-300 rounded-xl text-sm font-bold text-secondary-800 focus:ring-2 focus:ring-primary-500 bg-white appearance-none">
                                    <option value="pcs" <?php echo ($product['base_unit'] ?? '') === 'pcs' ? 'selected' : ''; ?>>Piece (pcs)</option>
                                    <option value="kg" <?php echo ($product['base_unit'] ?? '') === 'kg' ? 'selected' : ''; ?>>Kilogram (kg)</option>
                                    <option value="gm" <?php echo ($product['base_unit'] ?? '') === 'gm' ? 'selected' : ''; ?>>Gram (gm)</option>
                                    <option value="liter" <?php echo ($product['base_unit'] ?? '') === 'liter' ? 'selected' : ''; ?>>Liter (liter)</option>
                                    <option value="ml" <?php echo ($product['base_unit'] ?? '') === 'ml' ? 'selected' : ''; ?>>Milliliter (ml)</option>
                                    <option value="packet" <?php echo ($product['base_unit'] ?? '') === 'packet' ? 'selected' : ''; ?>>Packet (packet)</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-secondary-400">
                                    <ion-icon name="chevron-down-outline"></ion-icon>
                                </div>
                            </div>
                            <p class="text-[11px] text-secondary-400 mt-1.5">Warehouse inventory unit</p>
                        </div>

                        <!-- 2. Purchase / Bulk Unit -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-2" for="purchase_unit_select">
                                Purchase / Bulk Unit
                            </label>
                            <div class="relative">
                                <select id="purchase_unit_select" onchange="onPurchaseUnitSelectChange(this)"
                                        class="w-full px-4 py-2.5 border border-secondary-300 rounded-xl text-sm font-bold text-secondary-800 focus:ring-2 focus:ring-primary-500 bg-white appearance-none">
                                    <option value="" data-qty="1">-- None (Single Base Unit) --</option>
                                    <?php 
                                        $hasMatchedUnit = false;
                                        foreach ($packagingUnits as $pu): 
                                            $isSelected = ($product['purchase_unit'] === $pu['name']);
                                            if ($isSelected) $hasMatchedUnit = true;
                                    ?>
                                        <option value="<?php echo htmlspecialchars($pu['name']); ?>" 
                                                data-qty="<?php echo floatval($pu['default_qty']); ?>" 
                                                data-base="<?php echo htmlspecialchars($pu['base_unit']); ?>"
                                                <?php echo $isSelected ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($pu['name']); ?> (= <?php echo floatval($pu['default_qty']) . ' ' . $pu['base_unit']; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                    <option value="__custom__" <?php echo (!empty($product['purchase_unit']) && !$hasMatchedUnit) ? 'selected' : ''; ?>>
                                        ➕ Custom Packaging Unit...
                                    </option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-secondary-400">
                                    <ion-icon name="chevron-down-outline"></ion-icon>
                                </div>
                            </div>

                            <!-- Hidden field submitted to server -->
                            <input type="hidden" id="purchase_unit" name="purchase_unit" value="<?php echo htmlspecialchars($product['purchase_unit'] ?? ''); ?>">

                            <!-- Custom Purchase Unit input (visible when __custom__ is selected) -->
                            <div id="custom_purchase_unit_wrapper" class="mt-2 <?php echo (!empty($product['purchase_unit']) && !$hasMatchedUnit) ? '' : 'hidden'; ?>">
                                <input type="text" id="custom_purchase_unit_input" 
                                       value="<?php echo htmlspecialchars($product['purchase_unit'] ?? ''); ?>"
                                       oninput="onCustomPurchaseUnitInput(this)"
                                       placeholder="Enter custom container (e.g. Barrel, Crate)" 
                                       class="w-full px-3.5 py-2 border border-primary-400 bg-primary-50/40 rounded-xl text-xs font-semibold text-secondary-800">
                            </div>
                            <p class="text-[11px] text-secondary-400 mt-1.5">Supplier delivery container</p>
                        </div>

                        <!-- 3. Conversion Ratio Qty -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-2" for="purchase_unit_qty">
                                Units per Container
                            </label>
                            <div class="relative">
                                <input type="number" step="0.001" min="0.001" id="purchase_unit_qty" name="purchase_unit_qty" 
                                       value="<?php echo floatval($product['purchase_unit_qty'] ?? 1); ?>" 
                                       oninput="calculateUnitPricing()"
                                       class="w-full pl-4 pr-14 py-2.5 border border-secondary-300 rounded-xl text-sm font-bold text-secondary-800 focus:ring-2 focus:ring-primary-500">
                                <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-xs font-bold text-secondary-400 pointer-events-none base-unit-label">
                                    <?php echo htmlspecialchars($product['base_unit'] ?? 'pcs'); ?>
                                </span>
                            </div>
                            <p class="text-[11px] text-secondary-400 mt-1.5">e.g. 1 Sack = 50 kg or 1 Box = 24 pcs</p>
                        </div>
                    </div>

                    <!-- Bulk Container Cost Calculator (Appears only if purchase_unit_qty > 1) -->
                    <div id="bulk-cost-wrapper" class="p-4 rounded-xl bg-blue-50/50 border border-blue-100 flex flex-wrap items-center justify-between gap-4 <?php echo (floatval($product['purchase_unit_qty'] ?? 1) <= 1) ? 'hidden' : ''; ?>">
                        <div class="flex items-center gap-3">
                            <span class="text-xl">📦</span>
                            <div>
                                <span class="text-xs font-bold text-blue-900 block" id="bulk-buy-label">
                                    Wholesale Container Buy Price (<span id="purchase-unit-label-span"><?php echo htmlspecialchars($product['purchase_unit'] ?: 'Container'); ?></span>)
                                </span>
                                <span class="text-[11px] text-blue-700">Changing this automatically updates the per-unit cost above</span>
                            </div>
                        </div>
                        <div class="relative w-44">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-blue-600 font-bold text-sm">Tk</span>
                            <input type="number" step="0.01" id="bulk_buy_price" 
                                   value="<?php 
                                        $pQty = floatval($product['purchase_unit_qty'] ?? 1);
                                        echo number_format(floatval($product['buy_price']) * ($pQty > 0 ? $pQty : 1), 2, '.', ''); 
                                   ?>" 
                                   oninput="onBulkBuyPriceChange()"
                                   placeholder="0.00" 
                                   class="w-full pl-9 pr-3 py-1.5 bg-white border border-blue-200 rounded-xl text-sm font-black text-blue-950 focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- 4. Pack Sizes & Customer Variants (Clean Redesign) -->
                <div class="bg-white rounded-2xl shadow-sm border border-secondary-200/80 p-6 sm:p-7">
                    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-secondary-100 pb-4 mb-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-purple-50 border border-purple-100 flex items-center justify-center text-purple-600 shadow-2xs">
                                <ion-icon name="layers" class="text-xl"></ion-icon>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-secondary-900">Pack Sizes & Customer Variants</h3>
                                <p class="text-xs text-secondary-500 mt-0.5">Let shoppers pick different size packages (e.g. 500 gm, 1 kg, 5 kg sack) on storefront cards.</p>
                            </div>
                        </div>
                        <button type="button" onclick="addVariantRow()" 
                                class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-xs hover:shadow-md">
                            <ion-icon name="add-circle" class="text-base"></ion-icon>
                            Add Size Variant
                        </button>
                    </div>

                    <div class="overflow-hidden border border-secondary-200 rounded-2xl bg-white shadow-2xs">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-secondary-50/80 border-b border-secondary-200 uppercase text-[11px] font-bold text-secondary-600">
                                <tr>
                                    <th class="px-4 py-3">Variant Label</th>
                                    <th class="px-4 py-3 w-44">Quantity In Base Unit</th>
                                    <th class="px-4 py-3 w-40 text-right">Selling Price (Tk)</th>
                                    <th class="px-4 py-3 w-24 text-center">Default</th>
                                    <th class="px-3 py-3 w-14 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="variants-tbody" class="divide-y divide-secondary-100">
                                <?php if (!empty($variants)): ?>
                                    <?php foreach ($variants as $idx => $v): ?>
                                        <tr class="variant-row hover:bg-secondary-50/60 transition-colors">
                                            <td class="px-4 py-2.5">
                                                <input type="text" name="variants[<?php echo $idx; ?>][title]" value="<?php echo htmlspecialchars($v['title']); ?>" 
                                                       placeholder="e.g. 1 kg or 50 kg Sack" class="w-full px-3 py-1.5 border border-secondary-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-primary-500" required>
                                            </td>
                                            <td class="px-4 py-2.5">
                                                <div class="relative">
                                                    <input type="number" step="0.001" min="0.001" name="variants[<?php echo $idx; ?>][qty]" value="<?php echo floatval($v['qty']); ?>" 
                                                           oninput="onVariantQtyChange(this)"
                                                           placeholder="1" class="w-full pl-3 pr-12 py-1.5 border border-secondary-300 rounded-xl text-xs font-bold text-secondary-800 focus:ring-2 focus:ring-primary-500" required>
                                                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-[10px] text-secondary-400 font-bold base-unit-label pointer-events-none"><?php echo htmlspecialchars($product['base_unit'] ?? 'pcs'); ?></span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-2.5 text-right">
                                                <div class="relative">
                                                    <span class="absolute inset-y-0 left-0 flex items-center pl-2.5 text-emerald-600 font-bold text-xs">Tk</span>
                                                    <input type="number" step="0.01" min="0" name="variants[<?php echo $idx; ?>][price]" value="<?php echo floatval($v['price']); ?>" 
                                                           placeholder="0.00" class="w-full pl-7 pr-3 py-1.5 border border-secondary-300 rounded-xl text-xs font-black text-emerald-700 text-right focus:ring-2 focus:ring-emerald-500" required>
                                                </div>
                                            </td>
                                            <td class="px-4 py-2.5 text-center">
                                                <input type="checkbox" name="variants[<?php echo $idx; ?>][is_default]" value="1" <?php echo !empty($v['is_default']) ? 'checked' : ''; ?> class="rounded text-primary-600 focus:ring-primary-500 h-4 w-4">
                                            </td>
                                            <td class="px-3 py-2.5 text-center">
                                                <button type="button" onclick="deleteVariantRow(this)" class="text-secondary-400 hover:text-red-500 p-1.5 rounded-lg hover:bg-red-50 transition-colors" title="Delete Variant">
                                                    <ion-icon name="trash-outline" class="text-base"></ion-icon>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <div id="variants-empty-state" class="<?php echo empty($variants) ? '' : 'hidden'; ?> py-8 text-center text-secondary-400">
                            <ion-icon name="layers-outline" class="text-3xl text-secondary-300 mb-1"></ion-icon>
                            <p class="text-xs font-medium">No custom size variants added. Product will sell in single base units.</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Col: Stock, Image, Actions -->
            <div class="space-y-8">
                
                <!-- Stock Overview & Entry -->
                <div class="bg-white rounded-2xl shadow-sm border border-secondary-200/80 p-6">
                    <h3 class="text-base font-bold text-secondary-900 mb-4 flex items-center gap-2 border-b border-secondary-100 pb-3">
                        <ion-icon name="cube-outline" class="text-primary-600 text-lg"></ion-icon>
                        Inventory & Stock
                    </h3>

                    <div class="mb-4">
                        <label class="block text-secondary-700 text-xs font-bold uppercase tracking-wider mb-2" for="stock_qty">
                            Total Warehouse Stock (<span class="base-unit-label"><?php echo htmlspecialchars($product['base_unit'] ?? 'pcs'); ?></span>)
                        </label>
                        <div class="relative">
                            <input type="number" step="0.001" id="stock_qty" name="stock_qty" 
                                   value="<?php echo floatval($product['stock_qty']); ?>" 
                                   oninput="updateStockOverview()"
                                   class="w-full pl-4 pr-14 py-3 border border-secondary-300 rounded-xl text-base font-bold text-secondary-900 focus:ring-2 focus:ring-primary-500">
                            <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-xs font-semibold text-secondary-400 pointer-events-none base-unit-label">
                                <?php echo htmlspecialchars($product['base_unit'] ?? 'pcs'); ?>
                            </span>
                        </div>
                    </div>

                    <!-- Package Conversion Helper for Stock -->
                    <div class="bg-emerald-50/70 border border-emerald-200/80 rounded-xl p-4" id="bulk-stock-helper">
                        <span class="text-xs font-semibold text-emerald-900 block mb-1">📦 Package Breakdown:</span>
                        <p class="text-xs text-emerald-700 font-bold" id="bulk-stock-display">
                            <?php echo \Models\Product::formatStockDisplay($product); ?>
                        </p>
                    </div>
                </div>

                <!-- Sourcing & Availability -->
                <div class="bg-white rounded-2xl shadow-sm border border-secondary-200/80 p-6">
                    <h3 class="text-base font-bold text-secondary-900 mb-4 flex items-center gap-2 border-b border-secondary-100 pb-3">
                        <ion-icon name="storefront-outline" class="text-primary-600 text-lg"></ion-icon>
                        Store Availability
                    </h3>

                    <div class="mb-4">
                        <label class="block text-secondary-700 text-xs font-bold uppercase tracking-wider mb-2" for="availability_status">Status</label>
                        <select name="availability_status" id="availability_status" class="w-full px-4 py-3 border border-secondary-300 rounded-xl text-sm font-medium focus:ring-2 focus:ring-primary-500 bg-white">
                            <option value="in_stock" <?php echo ($product['availability_status'] ?? '') === 'in_stock' ? 'selected' : ''; ?>>✅ In Stock (On-Demand / Local)</option>
                            <option value="out_of_stock" <?php echo ($product['availability_status'] ?? '') === 'out_of_stock' ? 'selected' : ''; ?>>❌ Out of Stock</option>
                            <option value="pending" <?php echo ($product['availability_status'] ?? '') === 'pending' ? 'selected' : ''; ?>>⏳ Pending (Needs Review)</option>
                        </select>
                        <p class="text-[10px] text-secondary-500 mt-1">If "In Stock", product appears on frontend even if stock is 0.</p>
                    </div>

                    <div>
                        <label class="flex items-center gap-2 cursor-pointer mt-4">
                            <input type="checkbox" name="is_verified" value="1" <?php echo !empty($product['is_verified']) ? 'checked' : ''; ?> class="w-5 h-5 text-primary-600 rounded border-secondary-300 focus:ring-primary-500 transition-colors">
                            <span class="text-sm font-semibold text-secondary-900">Price Verified</span>
                        </label>
                        <p class="text-[10px] text-secondary-500 mt-1 pl-7">Unverified products show up in the verification list.</p>
                    </div>
                </div>

                <!-- Product Image -->
                <div class="bg-white rounded-2xl shadow-sm border border-secondary-200/80 p-6">
                    <h3 class="text-base font-bold text-secondary-900 mb-4 flex items-center gap-2 border-b border-secondary-100 pb-3">
                        <ion-icon name="image-outline" class="text-primary-600 text-lg"></ion-icon>
                        Product Image
                    </h3>

                    <div class="mb-4 text-center">
                        <?php 
                        $editImg = !empty($product['image_path']) ? \Models\Product::getImageUrl($product['image_path'], $base) : "{$base}/images/default-product.svg";
                        ?>
                        <div class="mb-3 inline-block relative border border-secondary-200 rounded-2xl p-2 bg-secondary-50">
                            <img id="previewImage" src="<?php echo $editImg; ?>" alt="Product Image" class="h-32 w-32 object-contain rounded-xl mx-auto" onerror="this.src='<?= $base ?>/images/default-product.svg'">
                            <?php if(empty($product['image_path'])): ?>
                                <span class="block text-[11px] text-secondary-400 font-medium mt-1">Default Placeholder Image</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <button type="button" 
                                    onclick="openProductImageFinderModal(<?= $product['id'] ?>, '<?= htmlspecialchars(addslashes($product['name'])) ?>')" 
                                    class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-sm transition-all cursor-pointer">
                                <ion-icon name="sparkles" class="text-amber-300 text-base"></ion-icon>
                                <span>ওয়েব ও গুগল থেকে ছবি খুঁজুন</span>
                            </button>
                        </div>

                        <div class="relative flex py-2 items-center">
                            <div class="flex-grow border-t border-secondary-200"></div>
                            <span class="flex-shrink mx-2 text-[10px] text-secondary-400 uppercase font-semibold">অথবা ডিভাইস থেকে আপলোড</span>
                            <div class="flex-grow border-t border-secondary-200"></div>
                        </div>

                        <input type="file" id="image" name="image" accept="image/*" 
                               class="w-full text-xs text-secondary-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-white rounded-2xl shadow-sm border border-secondary-200/80 p-6 space-y-3">
                    <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3.5 px-6 rounded-xl transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                        <ion-icon name="save-outline" class="text-xl"></ion-icon>
                        Save Changes
                    </button>
                    <a href="<?= $base ?>/admin/products" class="w-full block text-center px-6 py-2.5 rounded-xl border border-secondary-300 text-secondary-600 hover:bg-secondary-50 font-semibold transition-colors text-sm">
                        Cancel
                    </a>
                </div>

            </div>

        </div>
    </form>
</div>

<!-- Dynamic Calculator, Cascading Categories & Presets JavaScript -->
<script>
const ALL_CATEGORIES = <?php echo json_encode($categories); ?>;
let variantCount = <?php echo count($variants); ?>;

// --- CASCADING CATEGORY SELECTOR CLASS WITH LIVE SEARCH ---
class CascadingCategorySelector {
    constructor(container, hiddenInput, categories, initialId = null) {
        this.container = typeof container === 'string' ? document.getElementById(container) : container;
        this.hiddenInput = typeof hiddenInput === 'string' ? document.getElementById(hiddenInput) : hiddenInput;
        this.categories = categories || [];
        this.selectedId = initialId ? parseInt(initialId) : null;
        this.activeIndex = -1;

        // Precompute category full paths for searching
        this.categoryPaths = this.categories.map(c => {
            const ancestry = this.getAncestry(c.id);
            const pathNames = ancestry.map(a => a.name).join(' › ');
            const searchStr = (c.name + ' ' + (c.slug || '') + ' ' + ancestry.map(a => a.name).join(' ')).toLowerCase();
            const hasChildren = this.getChildren(c.id).length > 0;
            return {
                id: c.id,
                name: c.name,
                pathNames: pathNames,
                searchStr: searchStr,
                hasChildren: hasChildren,
                level: ancestry.length
            };
        });

        this.initStructure();
        this.render();
    }

    getCategory(id) {
        return this.categories.find(c => c.id == id);
    }

    getChildren(parentId) {
        return this.categories.filter(c => {
            if (!parentId) return !c.parent_id || c.parent_id == 0;
            return c.parent_id == parentId;
        });
    }

    getAncestry(id) {
        const path = [];
        let cur = this.getCategory(id);
        while (cur) {
            path.unshift(cur);
            cur = cur.parent_id ? this.getCategory(cur.parent_id) : null;
        }
        return path;
    }

    initStructure() {
        this.container.innerHTML = `
            <!-- Live Category Search Bar -->
            <div class="relative mb-3 category-search-wrapper">
                <div class="relative flex items-center">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-secondary-400">
                        <ion-icon name="search-outline" class="text-base text-primary-600"></ion-icon>
                    </div>
                    <input type="text" 
                           class="category-search-input w-full pl-9 pr-9 py-2.5 bg-white border border-secondary-300 rounded-xl text-xs font-semibold placeholder-secondary-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all shadow-2xs" 
                           placeholder="🔍 ক্যাটাগরি বা সাব-ক্যাটাগরির নাম লিখে সরাসরি খুঁজুন (যেমন: নুডলস, তেল, চাল, মসলা)..."
                           autocomplete="off">
                    <button type="button" 
                            class="category-search-clear hidden absolute inset-y-0 right-0 flex items-center pr-2.5 text-secondary-400 hover:text-secondary-600 cursor-pointer"
                            title="মুছে ফেলুন">
                        <ion-icon name="close-circle" class="text-lg"></ion-icon>
                    </button>
                </div>
                
                <!-- Autocomplete Dropdown List -->
                <div class="category-search-dropdown hidden absolute z-30 left-0 right-0 mt-1 max-h-64 overflow-y-auto bg-white border border-secondary-200 rounded-xl shadow-xl divide-y divide-secondary-100 text-xs">
                </div>
            </div>

            <!-- Step-by-Step Cascading Dropdowns -->
            <div class="category-levels-container space-y-2.5"></div>

            <!-- Breadcrumb / Status Banner -->
            <div class="category-breadcrumb mt-2 text-xs px-3 py-2 rounded-xl flex items-center gap-1.5" style="display: none;"></div>
        `;

        this.searchInput = this.container.querySelector('.category-search-input');
        this.clearBtn = this.container.querySelector('.category-search-clear');
        this.dropdown = this.container.querySelector('.category-search-dropdown');
        this.levelsContainer = this.container.querySelector('.category-levels-container');
        this.breadcrumbEl = this.container.querySelector('.category-breadcrumb');

        this.attachSearchEvents();
    }

    attachSearchEvents() {
        this.searchInput.addEventListener('input', (e) => {
            const query = e.target.value.trim().toLowerCase();
            if (query.length > 0) {
                this.clearBtn.classList.remove('hidden');
                this.performSearch(query);
            } else {
                this.clearBtn.classList.add('hidden');
                this.hideDropdown();
            }
        });

        this.searchInput.addEventListener('focus', () => {
            this.searchInput.select();
            const query = this.searchInput.value.trim().toLowerCase();
            if (query.length > 0) {
                this.performSearch(query);
            }
        });

        this.clearBtn.addEventListener('click', () => {
            this.searchInput.value = '';
            this.clearBtn.classList.add('hidden');
            this.hideDropdown();
            this.searchInput.focus();
        });

        document.addEventListener('click', (e) => {
            if (!this.container.contains(e.target)) {
                this.hideDropdown();
            }
        });

        this.searchInput.addEventListener('keydown', (e) => {
            const items = this.dropdown.querySelectorAll('.search-result-item');
            if (items.length === 0 || this.dropdown.classList.contains('hidden')) {
                if (e.key === 'Escape') this.hideDropdown();
                return;
            }

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                this.activeIndex = (this.activeIndex + 1) % items.length;
                this.updateActiveItem(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                this.activeIndex = (this.activeIndex - 1 + items.length) % items.length;
                this.updateActiveItem(items);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (this.activeIndex >= 0 && this.activeIndex < items.length) {
                    items[this.activeIndex].click();
                }
            } else if (e.key === 'Escape') {
                this.hideDropdown();
            }
        });
    }

    performSearch(query) {
        this.activeIndex = -1;
        const matches = this.categoryPaths.filter(cp => cp.searchStr.includes(query));

        matches.sort((a, b) => {
            const aName = a.name.toLowerCase();
            const bName = b.name.toLowerCase();
            if (aName === query && bName !== query) return -1;
            if (bName === query && aName !== query) return 1;
            if (aName.startsWith(query) && !bName.startsWith(query)) return -1;
            if (!aName.startsWith(query) && bName.startsWith(query)) return 1;
            if (!a.hasChildren && b.hasChildren) return -1;
            if (a.hasChildren && !b.hasChildren) return 1;
            return a.name.localeCompare(b.name);
        });

        if (matches.length === 0) {
            this.dropdown.innerHTML = `
                <div class="p-3.5 text-center text-secondary-500 font-medium flex items-center justify-center gap-1.5">
                    <ion-icon name="alert-circle-outline" class="text-base text-secondary-400"></ion-icon>
                    <span>"${this.escapeHtml(query)}" নামে কোনো ক্যাটাগরি পাওয়া যায়নি</span>
                </div>
            `;
            this.dropdown.classList.remove('hidden');
            return;
        }

        const displayed = matches.slice(0, 30);
        this.dropdown.innerHTML = displayed.map(m => {
            const highlightedName = this.highlightText(m.name, query);
            const badge = m.hasChildren 
                ? '<span class="text-[9px] bg-secondary-100 text-secondary-600 font-bold px-1.5 py-0.5 rounded">ক্যাটাগরি গ্রুপ</span>'
                : '<span class="text-[9px] bg-emerald-100 text-emerald-800 font-bold px-1.5 py-0.5 rounded">পণ্য ক্যাটাগরি</span>';

            return `
                <div class="search-result-item px-3.5 py-2.5 hover:bg-primary-50/80 cursor-pointer transition-colors flex items-center justify-between gap-2"
                     data-id="${m.id}">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5 mb-0.5">
                            <span class="font-bold text-secondary-900 text-xs">${highlightedName}</span>
                            ${badge}
                        </div>
                        <div class="text-[11px] text-secondary-400 truncate flex items-center gap-1">
                            <ion-icon name="git-commit-outline" class="text-xs shrink-0 text-secondary-400"></ion-icon>
                            <span>${this.escapeHtml(m.pathNames)}</span>
                        </div>
                    </div>
                    <ion-icon name="arrow-forward-outline" class="text-secondary-400 text-sm shrink-0"></ion-icon>
                </div>
            `;
        }).join('');

        this.dropdown.querySelectorAll('.search-result-item').forEach(el => {
            el.addEventListener('click', () => {
                const id = parseInt(el.dataset.id);
                this.selectCategory(id);
            });
        });

        this.dropdown.classList.remove('hidden');
    }

    selectCategory(id) {
        this.selectedId = id;
        if (this.hiddenInput) {
            this.hiddenInput.value = id;
        }

        const ancestry = this.getAncestry(id);
        const chain = ancestry.map(c => c.id);

        if (this.searchInput && ancestry.length > 0) {
            this.searchInput.value = ancestry[ancestry.length - 1].name;
            this.clearBtn.classList.remove('hidden');
        }

        this.hideDropdown();
        this.renderLevels(chain);
        this.updateBreadcrumb();
    }

    render() {
        let chain = [];
        if (this.selectedId) {
            const ancestry = this.getAncestry(this.selectedId);
            chain = ancestry.map(c => c.id);
            if (this.searchInput && ancestry.length > 0) {
                this.searchInput.value = ancestry[ancestry.length - 1].name;
                this.clearBtn.classList.remove('hidden');
            }
        }
        this.renderLevels(chain);
        this.updateBreadcrumb();
    }

    renderLevels(chain) {
        this.levelsContainer.innerHTML = '';
        let parentId = null;
        let levelIndex = 1;

        while (true) {
            const children = this.getChildren(parentId);
            if (!children || children.length === 0) {
                break;
            }

            const currentSelected = chain[levelIndex - 1] || null;
            const selectDiv = document.createElement('div');
            selectDiv.className = 'category-level-wrapper';

            let labelText = levelIndex === 1 ? '1. Primary Category:' : (levelIndex === 2 ? '2. Sub-Category:' : `${levelIndex}. Sub-Category:`);
            let placeholder = levelIndex === 1 ? '-- Select Primary Category --' : '-- Select Sub-Category --';

            selectDiv.innerHTML = `
                <div class="flex items-center justify-between mb-1">
                    <label class="text-[11px] font-bold text-secondary-700">${labelText}</label>
                </div>
                <div class="relative">
                    <select class="w-full px-3 py-2 border border-secondary-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-primary-500 appearance-none bg-white">
                        <option value="">${placeholder}</option>
                        ${children.map(c => `<option value="${c.id}" ${c.id == currentSelected ? 'selected' : ''}>${c.name}</option>`).join('')}
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-secondary-400">
                        <ion-icon name="chevron-down-outline"></ion-icon>
                    </div>
                </div>
            `;

            const selectEl = selectDiv.querySelector('select');
            const thisLevel = levelIndex;

            selectEl.addEventListener('change', (e) => {
                const val = e.target.value ? parseInt(e.target.value) : null;
                const newChain = chain.slice(0, thisLevel - 1);
                if (val) {
                    newChain.push(val);
                    this.selectedId = val;
                } else {
                    this.selectedId = newChain.length > 0 ? newChain[newChain.length - 1] : null;
                }

                if (this.hiddenInput) {
                    this.hiddenInput.value = this.selectedId || '';
                }

                if (this.selectedId) {
                    const selCat = this.getCategory(this.selectedId);
                    if (this.searchInput && selCat) {
                        this.searchInput.value = selCat.name;
                        this.clearBtn.classList.remove('hidden');
                    }
                } else {
                    if (this.searchInput) {
                        this.searchInput.value = '';
                        this.clearBtn.classList.add('hidden');
                    }
                }

                this.renderLevels(newChain);
                this.updateBreadcrumb();
            });

            this.levelsContainer.appendChild(selectDiv);

            if (!currentSelected) {
                break;
            }

            parentId = currentSelected;
            levelIndex++;
        }
    }

    updateBreadcrumb() {
        if (!this.breadcrumbEl) return;

        if (this.selectedId) {
            const ancestry = this.getAncestry(this.selectedId);
            const pathNames = ancestry.map(c => c.name).join(' › ');
            const hasChildren = this.getChildren(this.selectedId).length > 0;
            
            if (hasChildren) {
                this.breadcrumbEl.innerHTML = `<ion-icon name="arrow-forward-circle-outline" class="text-base text-amber-600 flex-shrink-0"></ion-icon> <div><span class="text-amber-800 font-bold">Select next sub-category:</span> <span class="text-secondary-800">${pathNames}</span></div>`;
                this.breadcrumbEl.className = 'category-breadcrumb mt-2 text-xs font-medium text-amber-800 bg-amber-50 border border-amber-200 px-3 py-1.5 rounded-xl flex items-center gap-1.5';
            } else {
                this.breadcrumbEl.innerHTML = `<ion-icon name="checkmark-circle" class="text-base text-emerald-600 flex-shrink-0"></ion-icon> <div><span class="font-bold text-emerald-900">Selected Category:</span> <span class="text-secondary-900 font-semibold">${pathNames}</span></div>`;
                this.breadcrumbEl.className = 'category-breadcrumb mt-2 text-xs font-semibold text-emerald-800 bg-emerald-50 border border-emerald-200 px-3 py-2 rounded-xl flex items-center gap-1.5';
            }
            this.breadcrumbEl.style.display = 'flex';
        } else {
            this.breadcrumbEl.style.display = 'none';
        }
    }

    escapeHtml(str) {
        return String(str || '').replace(/[&<>"']/g, function(m) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m];
        });
    }

    highlightText(text, query) {
        if (!query) return this.escapeHtml(text);
        const escapedQuery = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const regex = new RegExp(`(${escapedQuery})`, 'gi');
        const parts = text.split(regex);
        return parts.map(part => {
            if (part.toLowerCase() === query.toLowerCase()) {
                return `<mark class="bg-amber-100 text-amber-900 font-bold px-0.5 rounded">${this.escapeHtml(part)}</mark>`;
            }
            return this.escapeHtml(part);
        }).join('');
    }

    updateActiveItem(items) {
        items.forEach((item, idx) => {
            if (idx === this.activeIndex) {
                item.classList.add('bg-primary-50', 'text-primary-900', 'font-bold');
                item.scrollIntoView({ block: 'nearest' });
            } else {
                item.classList.remove('bg-primary-50', 'text-primary-900', 'font-bold');
            }
        });
    }

    hideDropdown() {
        if (this.dropdown) {
            this.dropdown.classList.add('hidden');
        }
        this.activeIndex = -1;
    }
}

// 1. Preset Definitions
const PRESETS = {
    sack_kg: {
        unit_type: 'sack_kg',
        base_unit: 'kg',
        purchase_unit: 'Sack (50 kg)',
        purchase_unit_qty: 50,
        selling_unit: 'kg',
        variants: [
            { title: '1 kg', qty: 1, discount: 1 },
            { title: '5 kg', qty: 5, discount: 0.98 },
            { title: '25 kg Sack', qty: 25, discount: 0.96 },
            { title: '50 kg Sack', qty: 50, discount: 0.94 }
        ]
    },
    drum_liter: {
        unit_type: 'drum_liter',
        base_unit: 'liter',
        purchase_unit: 'Drum (190 L)',
        purchase_unit_qty: 190,
        selling_unit: 'Liter',
        variants: [
            { title: '500 ml', qty: 0.5, discount: 1.05 },
            { title: '1 Liter', qty: 1, discount: 1 },
            { title: '2 Liter', qty: 2, discount: 0.99 },
            { title: '5 Liter', qty: 5, discount: 0.97 }
        ]
    },
    box_piece: {
        unit_type: 'box_piece',
        base_unit: 'pcs',
        purchase_unit: 'Box (24 pcs)',
        purchase_unit_qty: 24,
        selling_unit: 'pcs',
        variants: [
            { title: '1 pc', qty: 1, discount: 1 },
            { title: '4 pcs Pack', qty: 4, discount: 0.97 },
            { title: '1 Box (24 pcs)', qty: 24, discount: 0.94 }
        ]
    },
    loose_kg: {
        unit_type: 'weight',
        base_unit: 'kg',
        purchase_unit: 'kg (1:1)',
        purchase_unit_qty: 1,
        selling_unit: 'kg',
        variants: [
            { title: '250 gm', qty: 0.25, discount: 1.05 },
            { title: '500 gm', qty: 0.5, discount: 1.02 },
            { title: '1 kg', qty: 1, discount: 1 }
        ]
    },
    piece_standard: {
        unit_type: 'piece',
        base_unit: 'pcs',
        purchase_unit: 'Piece (1:1)',
        purchase_unit_qty: 1,
        selling_unit: 'pcs',
        variants: [
            { title: '1 pc', qty: 1, discount: 1 }
        ]
    }
};

// Purchase Unit Select Change Handler
function onPurchaseUnitSelectChange(selectEl) {
    const val = selectEl.value;
    const customWrapper = document.getElementById('custom_purchase_unit_wrapper');
    const customInput = document.getElementById('custom_purchase_unit_input');
    const hiddenInput = document.getElementById('purchase_unit');
    const qtyInput = document.getElementById('purchase_unit_qty');
    const baseSelect = document.getElementById('base_unit');

    if (val === '__custom__') {
        customWrapper.classList.remove('hidden');
        hiddenInput.value = customInput.value;
    } else {
        customWrapper.classList.add('hidden');
        hiddenInput.value = val;

        const selectedOption = selectEl.options[selectEl.selectedIndex];
        if (selectedOption) {
            const qty = selectedOption.dataset.qty;
            const base = selectedOption.dataset.base;
            if (qty) qtyInput.value = qty;
            if (base && baseSelect) {
                baseSelect.value = base;
            }
        }
    }

    calculateUnitPricing();
}

function onCustomPurchaseUnitInput(inputEl) {
    document.getElementById('purchase_unit').value = inputEl.value;
    calculateUnitPricing();
}

// Apply Preset
function applyUnitPreset(presetKey) {
    const preset = PRESETS[presetKey];
    if (!preset) return;

    document.getElementById('unit_type').value = preset.unit_type;
    document.getElementById('base_unit').value = preset.base_unit;
    document.getElementById('purchase_unit').value = preset.purchase_unit;
    document.getElementById('purchase_unit_qty').value = preset.purchase_unit_qty;

    // Sync purchase unit select
    const pSelect = document.getElementById('purchase_unit_select');
    let matched = false;
    for (let i = 0; i < pSelect.options.length; i++) {
        if (pSelect.options[i].value === preset.purchase_unit) {
            pSelect.selectedIndex = i;
            matched = true;
            break;
        }
    }
    const customWrapper = document.getElementById('custom_purchase_unit_wrapper');
    if (!matched) {
        pSelect.value = '__custom__';
        customWrapper.classList.remove('hidden');
        document.getElementById('custom_purchase_unit_input').value = preset.purchase_unit;
    } else {
        customWrapper.classList.add('hidden');
    }

    // Refresh labels
    updateLabels();

    // Auto generate variants based on current sell_price
    const currentSell = parseFloat(document.getElementById('sell_price').value) || 0;
    const tbody = document.getElementById('variants-tbody');
    tbody.innerHTML = '';
    variantCount = 0;

    preset.variants.forEach((v, i) => {
        const calculatedPrice = currentSell > 0 ? (currentSell * v.qty * v.discount).toFixed(2) : '0.00';
        addVariantRow(v.title, v.qty, calculatedPrice, i === 0);
    });

    calculateUnitPricing();
}

// Add Variant Row
function addVariantRow(title = '', qty = 1, price = '', isDefault = false) {
    const tbody = document.getElementById('variants-tbody');
    const emptyState = document.getElementById('variants-empty-state');
    const baseUnit = document.getElementById('base_unit').value;
    const currentSell = parseFloat(document.getElementById('sell_price').value) || 0;
    
    if (!price && currentSell > 0) {
        price = (currentSell * qty).toFixed(2);
    }

    const rowHtml = `
        <tr class="variant-row hover:bg-secondary-50/60 transition-colors">
            <td class="px-4 py-2.5">
                <input type="text" name="variants[${variantCount}][title]" value="${title}" 
                       placeholder="e.g. 1 kg or 50 kg Sack" class="w-full px-3 py-1.5 border border-secondary-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-primary-500" required>
            </td>
            <td class="px-4 py-2.5">
                <div class="relative">
                    <input type="number" step="0.001" min="0.001" name="variants[${variantCount}][qty]" value="${qty}" 
                           oninput="onVariantQtyChange(this)"
                           placeholder="1" class="w-full pl-3 pr-12 py-1.5 border border-secondary-300 rounded-xl text-xs font-bold text-secondary-800 focus:ring-2 focus:ring-primary-500" required>
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-[10px] text-secondary-400 font-bold base-unit-label pointer-events-none">${baseUnit}</span>
                </div>
            </td>
            <td class="px-4 py-2.5 text-right">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-2.5 text-emerald-600 font-bold text-xs">Tk</span>
                    <input type="number" step="0.01" min="0" name="variants[${variantCount}][price]" value="${price}" 
                           placeholder="0.00" class="w-full pl-7 pr-3 py-1.5 border border-secondary-300 rounded-xl text-xs font-black text-emerald-700 text-right focus:ring-2 focus:ring-emerald-500" required>
                </div>
            </td>
            <td class="px-4 py-2.5 text-center">
                <input type="checkbox" name="variants[${variantCount}][is_default]" value="1" ${isDefault ? 'checked' : ''} class="rounded text-primary-600 focus:ring-primary-500 h-4 w-4">
            </td>
            <td class="px-3 py-2.5 text-center">
                <button type="button" onclick="deleteVariantRow(this)" class="text-secondary-400 hover:text-red-500 p-1.5 rounded-lg hover:bg-red-50 transition-colors" title="Delete Variant">
                    <ion-icon name="trash-outline" class="text-base"></ion-icon>
                </button>
            </td>
        </tr>
    `;

    tbody.insertAdjacentHTML('beforeend', rowHtml);
    if (emptyState) emptyState.classList.add('hidden');
    variantCount++;
}

function deleteVariantRow(btn) {
    const row = btn.closest('tr');
    row.remove();
    const tbody = document.getElementById('variants-tbody');
    const emptyState = document.getElementById('variants-empty-state');
    if (tbody && tbody.querySelectorAll('tr').length === 0 && emptyState) {
        emptyState.classList.remove('hidden');
    }
}

function onVariantQtyChange(input) {
    const row = input.closest('tr');
    const priceInput = row.querySelector('input[name*="[price]"]');
    const unitSell = parseFloat(document.getElementById('sell_price').value) || 0;
    const qty = parseFloat(input.value) || 1;
    if (unitSell > 0) {
        priceInput.value = (unitSell * qty).toFixed(2);
    }
}

// Update UI Labels when Base Unit changes
function updateLabels() {
    const baseUnit = document.getElementById('base_unit').value;
    const purchaseUnit = document.getElementById('purchase_unit').value || 'Container';
    
    document.querySelectorAll('.base-unit-label').forEach(el => el.textContent = baseUnit);
    document.querySelectorAll('.purchase-unit-text').forEach(el => el.textContent = purchaseUnit);
    const pSpan = document.getElementById('purchase-unit-label-span');
    if (pSpan) pSpan.textContent = purchaseUnit;
}

// Calculations & Margin Updates
function calculateUnitPricing() {
    updateLabels();
    const purchaseQty = parseFloat(document.getElementById('purchase_unit_qty').value) || 1;
    const buyPrice = parseFloat(document.getElementById('buy_price').value) || 0;
    const sellPrice = parseFloat(document.getElementById('sell_price').value) || 0;

    // Show/hide bulk container calculator
    const bulkWrapper = document.getElementById('bulk-cost-wrapper');
    if (bulkWrapper) {
        if (purchaseQty > 1) {
            bulkWrapper.classList.remove('hidden');
            const bulkInput = document.getElementById('bulk_buy_price');
            if (bulkInput && buyPrice > 0) {
                bulkInput.value = (buyPrice * purchaseQty).toFixed(2);
            }
        } else {
            bulkWrapper.classList.add('hidden');
        }
    }

    // Profit & Margin
    const profit = sellPrice - buyPrice;
    const profitPill = document.getElementById('unit-profit-pill');
    if (profitPill) {
        if (profit >= 0) {
            profitPill.textContent = `Profit: +Tk ${profit.toFixed(2)}`;
            profitPill.className = 'text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200';
        } else {
            profitPill.textContent = `Loss: -Tk ${Math.abs(profit).toFixed(2)}`;
            profitPill.className = 'text-[10px] font-bold text-red-700 bg-red-50 px-2.5 py-0.5 rounded-full border border-red-200';
        }
    }

    const marginValue = document.getElementById('margin-value');
    const marginBadge = document.getElementById('live-margin-badge');
    if (marginValue && marginBadge) {
        if (sellPrice > 0) {
            const marginPct = ((profit / sellPrice) * 100).toFixed(1);
            marginValue.textContent = `${marginPct}%`;
            if (profit >= 0) {
                marginBadge.className = 'flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs font-bold text-emerald-800';
            } else {
                marginBadge.className = 'flex items-center gap-2 px-3 py-1.5 rounded-xl bg-red-50 border border-red-200 text-xs font-bold text-red-800';
            }
        } else {
            marginValue.textContent = '0%';
        }
    }

    updateStockOverview();
}

function onBulkBuyPriceChange() {
    const bulkBuy = parseFloat(document.getElementById('bulk_buy_price').value) || 0;
    const purchaseQty = parseFloat(document.getElementById('purchase_unit_qty').value) || 1;
    if (purchaseQty > 0) {
        const unitBuy = bulkBuy / purchaseQty;
        document.getElementById('buy_price').value = unitBuy.toFixed(2);
        calculateUnitPricing();
    }
}

function onUnitBuyPriceChange() {
    calculateUnitPricing();
}

function onUnitSellPriceChange() {
    const regularPrice = parseFloat(document.getElementById('regular_price').value) || 0;
    const currentSell = parseFloat(document.getElementById('sell_price').value) || 0;
    const discountType = document.getElementById('discount_type').value;
    const valInput = document.getElementById('discount_value');

    if (regularPrice > currentSell && currentSell > 0) {
        const diff = regularPrice - currentSell;
        const pct = Math.round((diff / regularPrice) * 100);
        
        if (discountType === 'percent') {
            valInput.value = pct;
        } else if (discountType === 'fixed') {
            valInput.value = diff.toFixed(2);
        }
        showDiscountBadge(`${pct}% OFF`, `Save Tk ${diff.toFixed(2)}`, 'bg-red-500 text-white');
    } else if (discountType === 'none') {
        hideDiscountBadge();
    }

    calculateUnitPricing();
}

function updateStockOverview() {
    const stock = parseFloat(document.getElementById('stock_qty').value) || 0;
    const purchaseQty = parseFloat(document.getElementById('purchase_unit_qty').value) || 1;
    const purchaseUnit = document.getElementById('purchase_unit').value;
    const baseUnit = document.getElementById('base_unit').value;

    const displayEl = document.getElementById('bulk-stock-display');
    if (!displayEl) return;

    let text = `${stock} ${baseUnit}`;
    if (purchaseUnit && purchaseQty > 1 && stock >= purchaseQty) {
        const bulk = Math.floor(stock / purchaseQty);
        const rem = stock % purchaseQty;
        if (rem === 0) {
            text += ` (= ${bulk} ${purchaseUnit})`;
        } else {
            text += ` (= ${bulk} ${purchaseUnit} + ${rem} ${baseUnit})`;
        }
    }
    displayEl.textContent = text;
}

// --- DISCOUNT METHOD BUTTON SELECTOR ---
function setDiscountType(type) {
    document.getElementById('discount_type').value = type;
    updateDiscountButtonUI(type);
    applyDiscountCalculation();
}

function updateDiscountButtonUI(type) {
    const btnNone = document.getElementById('btn-discount-none');
    const btnPercent = document.getElementById('btn-discount-percent');
    const btnFixed = document.getElementById('btn-discount-fixed');
    const container = document.getElementById('discount-value-container');
    const unitSymbol = document.getElementById('discount-unit-symbol');
    const suffix = document.getElementById('discount-suffix');

    // Reset styles
    [btnNone, btnPercent, btnFixed].forEach(b => {
        if (b) b.className = 'py-2 px-3 text-xs font-bold rounded-xl border border-secondary-200 bg-white text-secondary-600 hover:bg-secondary-50 transition-all text-center';
    });

    const activeClass = 'py-2 px-3 text-xs font-bold rounded-xl border-2 border-primary-600 bg-primary-50 text-primary-700 shadow-2xs transition-all text-center';

    if (type === 'percent') {
        if (btnPercent) btnPercent.className = activeClass;
        if (container) container.className = 'opacity-100 transition-opacity';
        if (unitSymbol) unitSymbol.textContent = '%';
        if (suffix) suffix.textContent = '%';
    } else if (type === 'fixed') {
        if (btnFixed) btnFixed.className = activeClass;
        if (container) container.className = 'opacity-100 transition-opacity';
        if (unitSymbol) unitSymbol.textContent = 'Tk';
        if (suffix) suffix.textContent = 'Tk';
    } else {
        if (btnNone) btnNone.className = activeClass;
        if (container) container.className = 'opacity-50 pointer-events-none transition-opacity';
        if (unitSymbol) unitSymbol.textContent = '%';
        if (suffix) suffix.textContent = '%';
        const valInput = document.getElementById('discount_value');
        if (valInput) valInput.value = '0';
    }
}

function onRegularPriceInput() {
    applyDiscountCalculation();
}

function onDiscountValueInput() {
    applyDiscountCalculation();
}

function applyDiscountCalculation() {
    const regularPrice = parseFloat(document.getElementById('regular_price').value) || 0;
    const discountType = document.getElementById('discount_type').value;
    let discountVal = parseFloat(document.getElementById('discount_value').value) || 0;
    const sellInput = document.getElementById('sell_price');

    if (discountType === 'percent' && discountVal > 0) {
        if (discountVal > 99) discountVal = 99;
        if (regularPrice > 0) {
            const savings = (regularPrice * discountVal) / 100;
            const newSell = Math.max(0, regularPrice - savings);
            sellInput.value = newSell.toFixed(2);
            showDiscountBadge(`${discountVal}% OFF`, `Save Tk ${savings.toFixed(2)}`, 'bg-red-500 text-white');
        } else {
            hideDiscountBadge();
        }
    } else if (discountType === 'fixed' && discountVal > 0) {
        if (regularPrice > 0) {
            const newSell = Math.max(0, regularPrice - discountVal);
            sellInput.value = newSell.toFixed(2);
            const percent = Math.round((discountVal / regularPrice) * 100);
            showDiscountBadge(`Tk ${discountVal.toFixed(2)} OFF`, `(${percent}% OFF)`, 'bg-red-500 text-white');
        } else {
            hideDiscountBadge();
        }
    } else {
        // discountType === 'none'
        const currentSell = parseFloat(sellInput.value) || 0;
        if (regularPrice > currentSell && currentSell > 0) {
            const diff = regularPrice - currentSell;
            const pct = Math.round((diff / regularPrice) * 100);
            showDiscountBadge(`${pct}% OFF`, `Regular Tk${regularPrice.toFixed(2)} - Save Tk${diff.toFixed(2)}`, 'bg-amber-500 text-white');
        } else {
            hideDiscountBadge();
        }
    }

    calculateUnitPricing();
}

function showDiscountBadge(title, subtitle, badgeClass) {
    const badgeEl = document.getElementById('discount-badge-preview');
    const summaryMsg = document.getElementById('discount-summary-msg');
    if (badgeEl) {
        badgeEl.textContent = title;
        badgeEl.className = `text-xs font-black px-3 py-1 rounded-lg shadow-2xs inline-block ${badgeClass}`;
        badgeEl.classList.remove('hidden');
    }
    if (summaryMsg) {
        summaryMsg.innerHTML = `🎉 <strong class="text-emerald-700 font-bold">Promotion Active:</strong> Customer saves ${subtitle} per unit.`;
    }
}

function hideDiscountBadge() {
    const badgeEl = document.getElementById('discount-badge-preview');
    const summaryMsg = document.getElementById('discount-summary-msg');
    if (badgeEl) badgeEl.classList.add('hidden');
    if (summaryMsg) summaryMsg.textContent = 'No discount applicable. Product sells at regular retail price.';
}

document.addEventListener('DOMContentLoaded', () => {
    // Initialize Cascading Category Selector
    new CascadingCategorySelector(
        'edit_category_cascading_container',
        'edit_category_id',
        ALL_CATEGORIES,
        <?php echo json_encode($product['category_id'] ?? null); ?>
    );

    updateDiscountButtonUI(document.getElementById('discount_type').value || 'none');
    updateLabels();
    updateStockOverview();
    applyDiscountCalculation();
});
</script>

<?php require __DIR__ . '/image_finder_modal.php'; ?>
