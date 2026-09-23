<?php 
ob_start(); 
use Core\Lang;
$__ = function($key, $r = []) { return Lang::get($key, $r); };
$locale = $locale ?? Lang::locale();
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';

// Pricing & Discounts
$sellPrice = floatval($product['sell_price']);
$regularPrice = !empty($product['regular_price']) ? floatval($product['regular_price']) : null;
$hasDiscount = ($regularPrice && $regularPrice > $sellPrice);
$discountAmount = $hasDiscount ? ($regularPrice - $sellPrice) : 0;
$discountPercent = $hasDiscount ? round(($discountAmount / $regularPrice) * 100) : 0;
$stockQty = intval($product['stock_qty'] ?? 0);
$isOutOfStock = (($product['availability_status'] ?? '') === 'out_of_stock');
$isInStock = !$isOutOfStock;
$maxOrderQty = ($stockQty > 0) ? $stockQty : 99;

// Default image
$productImg = \Models\Product::getImageUrl($product['image_path'] ?? '', $base);
$fallbackImg = !empty($base) ? rtrim($base, '/') . '/images/default-product.svg' : '/images/default-product.svg';

// Check variants
$hasVariants = !empty($variants) && is_array($variants);
$initialTitle = $hasVariants ? ($variants[0]['title'] ?? '') : ($product['selling_unit'] ?? $product['base_unit'] ?? '1 Unit');
$initialPrice = $hasVariants ? floatval($variants[0]['price'] ?? $sellPrice) : $sellPrice;
$initialQty = $hasVariants ? floatval($variants[0]['qty'] ?? 1) : 1;
?>

<div class="bg-gray-50/70 py-6 sm:py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumbs -->
        <nav class="flex items-center text-xs sm:text-sm text-gray-500 mb-6 flex-wrap gap-1.5" aria-label="Breadcrumb">
            <a href="<?= $base ?>/" class="hover:text-emerald-600 transition-colors flex items-center gap-1 font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <?= $__('nav_home') ?>
            </a>
            <?php if (!empty($parentCategory)): ?>
                <span class="text-gray-300">/</span>
                <a href="<?= $base ?>/category?id=<?= $parentCategory['id'] ?>" class="hover:text-emerald-600 transition-colors font-medium">
                    <?= htmlspecialchars($parentCategory['name']) ?>
                </a>
            <?php endif; ?>
            <?php if (!empty($product['category_name'])): ?>
                <span class="text-gray-300">/</span>
                <a href="<?= $base ?>/category?id=<?= $product['category_id'] ?>" class="hover:text-emerald-600 transition-colors font-medium">
                    <?= htmlspecialchars($product['category_name']) ?>
                </a>
            <?php endif; ?>
            <span class="text-gray-300">/</span>
            <span class="text-gray-800 font-semibold truncate max-w-[200px] sm:max-w-xs" title="<?= htmlspecialchars($product['name']) ?>">
                <?= htmlspecialchars($product['name']) ?>
            </span>
        </nav>

        <!-- Product Details Main Card -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden mb-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 p-6 sm:p-8 lg:p-10">
                
                <!-- Left: Product Image Gallery / View -->
                <div class="lg:col-span-5 flex flex-col items-center justify-center">
                    <div class="relative w-full h-[380px] sm:h-[460px] max-w-[500px] bg-white rounded-3xl border border-gray-100/90 flex items-center justify-center p-4 sm:p-6 group overflow-hidden shadow-2xs">
                        
                        <!-- Badges -->
                        <div class="absolute top-4 left-4 flex flex-col gap-2 z-10">
                            <?php if ($hasDiscount): ?>
                                <span class="bg-rose-500 text-white text-xs font-black px-2.5 py-1 rounded-lg shadow-sm">
                                    -<?= $discountPercent ?>% <?= $locale === 'bn' ? 'ছাড়' : 'OFF' ?>
                                </span>
                            <?php endif; ?>
                            <?php if ($isInStock): ?>
                                <span class="bg-emerald-500 text-white text-[11px] font-bold px-2.5 py-0.5 rounded-lg shadow-2xs">
                                    <?= $locale === 'bn' ? '✓ ইন স্টক' : '✓ In Stock' ?>
                                </span>
                            <?php else: ?>
                                <span class="bg-red-500 text-white text-[11px] font-bold px-2.5 py-0.5 rounded-lg shadow-2xs">
                                    <?= $locale === 'bn' ? 'স্টক আউট' : 'Out of Stock' ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Brand / Origin Pill -->
                        <div class="absolute top-4 right-4 z-10">
                            <span class="inline-flex items-center gap-1 bg-white/95 backdrop-blur-xs border border-gray-200 text-gray-700 text-[11px] font-semibold px-2.5 py-1 rounded-full shadow-2xs">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <?= $locale === 'bn' ? '১০০% অরিজিনাল' : '100% Authentic' ?>
                            </span>
                        </div>

                        <img id="main-product-img" 
                             src="<?= htmlspecialchars($productImg) ?>" 
                             alt="<?= htmlspecialchars($product['name']) ?>" 
                             class="w-full h-full max-h-[340px] sm:max-h-[420px] object-contain mix-blend-multiply transition-transform duration-500 group-hover:scale-105"
                             onerror="this.onerror=null; this.src='<?= $fallbackImg ?>';">
                    </div>
                </div>

                <!-- Right: Product Information & Purchase Area -->
                <div class="lg:col-span-7 flex flex-col justify-between">
                    <div>
                        <!-- Category & Brand Tags -->
                        <div class="flex items-center gap-2 flex-wrap mb-3">
                            <?php if (!empty($product['category_name'])): ?>
                                <a href="<?= $base ?>/category?id=<?= $product['category_id'] ?>" class="text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-3 py-1 rounded-full transition-colors">
                                    <?= htmlspecialchars($product['category_name']) ?>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($product['brand_name'])): ?>
                                <span class="text-xs font-semibold text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
                                    <?= htmlspecialchars($product['brand_name']) ?>
                                </span>
                            <?php endif; ?>
                            <?php if (!empty($product['sku'])): ?>
                                <span class="text-[11px] text-gray-400 font-mono">
                                    SKU: <?= htmlspecialchars($product['sku']) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Product Title -->
                        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-gray-900 leading-tight mb-3">
                            <?= htmlspecialchars($product['name']) ?>
                        </h1>

                        <!-- Price Section -->
                        <div class="flex items-baseline gap-3 my-4 flex-wrap">
                            <div class="flex items-baseline gap-1 text-emerald-700">
                                <span class="text-xl sm:text-2xl font-bold">৳</span>
                                <span id="detail-current-price" class="text-3xl sm:text-4xl font-black">
                                    <?= number_format($initialPrice) ?>
                                </span>
                            </div>

                            <?php if ($hasDiscount): ?>
                                <div class="flex items-center gap-2">
                                    <span id="detail-regular-price" class="text-base sm:text-lg text-gray-400 line-through font-semibold">
                                        ৳<?= number_format($regularPrice) ?>
                                    </span>
                                    <span class="bg-rose-50 text-rose-600 border border-rose-200 text-xs font-bold px-2.5 py-0.5 rounded-md">
                                        <?= $locale === 'bn' ? "সাশ্রয় ৳" . number_format($discountAmount) : "Save ৳" . number_format($discountAmount) ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Variants / Weight Options (if available) -->
                        <?php if ($hasVariants): ?>
                            <div class="my-5 pt-3 border-t border-gray-100">
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                    <?= $locale === 'bn' ? 'ওজন / সাইজ নির্বাচন করুন:' : 'Select Size / Variant:' ?>
                                </label>
                                <div class="flex flex-wrap gap-2.5">
                                    <?php foreach ($variants as $idx => $v): ?>
                                        <?php $isSelected = ($idx === 0); ?>
                                        <button type="button" 
                                                onclick="selectDetailVariant(this, '<?= htmlspecialchars($v['title'], ENT_QUOTES) ?>', <?= floatval($v['price']) ?>, <?= floatval($v['qty'] ?? 1) ?>)"
                                                class="detail-variant-pill px-4 py-2 rounded-xl text-xs sm:text-sm font-bold border transition-all cursor-pointer <?= $isSelected ? 'border-emerald-600 bg-emerald-50 text-emerald-800 ring-2 ring-emerald-500/20 shadow-xs' : 'border-gray-200 text-gray-700 bg-white hover:border-gray-300' ?>">
                                            <span><?= htmlspecialchars($v['title']) ?></span>
                                            <span class="ml-1 text-emerald-600">৳<?= number_format($v['price']) ?></span>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Quantity Selector & Add to Bag -->
                        <div class="my-6 pt-2">
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                                <!-- Stepper -->
                                <div class="inline-flex items-center border border-gray-200 rounded-2xl bg-white p-1 shadow-2xs self-start">
                                    <button type="button" 
                                            onclick="changeDetailQty(-1)"
                                            class="w-10 h-10 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-700 flex items-center justify-center font-black text-lg transition-colors cursor-pointer active:scale-95">
                                        −
                                    </button>
                                    <input type="number" 
                                           id="detail-qty" 
                                           value="1" 
                                           min="1" 
                                           max="<?= $maxOrderQty ?>" 
                                           class="w-14 text-center font-black text-gray-900 text-base focus:outline-none bg-transparent"
                                           readonly>
                                    <button type="button" 
                                            onclick="changeDetailQty(1)"
                                            class="w-10 h-10 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-700 flex items-center justify-center font-black text-lg transition-colors cursor-pointer active:scale-95">
                                        +
                                    </button>
                                </div>

                                <!-- Add to Bag Primary Button -->
                                <button type="button" 
                                        id="detail-add-btn"
                                        onclick="submitDetailAddToCart(<?= $product['id'] ?>, this)"
                                        <?= !$isInStock ? 'disabled' : '' ?>
                                        class="flex-1 py-3.5 px-8 rounded-2xl bg-emerald-600 hover:bg-emerald-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-black text-base flex items-center justify-center gap-3 shadow-lg shadow-emerald-600/25 hover:shadow-emerald-600/35 active:scale-[0.99] transition-all cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    <span><?= $isInStock ? $__('add_to_bag') : ($locale === 'bn' ? 'স্টক শেষ' : 'Out of Stock') ?></span>
                                </button>
                            </div>
                        </div>

                        <!-- Trust & Delivery Value Propositions -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-6 border-t border-gray-100">
                            <div class="flex items-center gap-2 p-2.5 rounded-xl bg-gray-50/80 border border-gray-100">
                                <span class="text-xl">🚀</span>
                                <div>
                                    <h4 class="text-[11px] font-bold text-gray-900"><?= $locale === 'bn' ? 'দ্রুত ডেলিভারি' : 'Fast Delivery' ?></h4>
                                    <p class="text-[10px] text-gray-500"><?= $locale === 'bn' ? '১-২ ঘণ্টায় পৌঁছে যাবে' : 'Within 1-2 hours' ?></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 p-2.5 rounded-xl bg-gray-50/80 border border-gray-100">
                                <span class="text-xl">💵</span>
                                <div>
                                    <h4 class="text-[11px] font-bold text-gray-900"><?= $locale === 'bn' ? 'ক্যাশ অন ডেলিভারি' : 'Cash on Delivery' ?></h4>
                                    <p class="text-[10px] text-gray-500"><?= $locale === 'bn' ? 'পণ্য দেখে টাকা দিন' : 'Pay after check' ?></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 p-2.5 rounded-xl bg-gray-50/80 border border-gray-100">
                                <span class="text-xl">🌿</span>
                                <div>
                                    <h4 class="text-[11px] font-bold text-gray-900"><?= $locale === 'bn' ? 'তাজা ও খাঁটি' : 'Fresh & Pure' ?></h4>
                                    <p class="text-[10px] text-gray-500"><?= $locale === 'bn' ? '১০০% গুণগত মান' : 'Best quality' ?></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 p-2.5 rounded-xl bg-gray-50/80 border border-gray-100">
                                <span class="text-xl">🔄</span>
                                <div>
                                    <h4 class="text-[11px] font-bold text-gray-900"><?= $locale === 'bn' ? 'সহজ রিটার্ন' : 'Easy Return' ?></h4>
                                    <p class="text-[10px] text-gray-500"><?= $locale === 'bn' ? 'তাৎক্ষণিক রিটার্ন সুবিধা' : 'Instant return' ?></p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <!-- Product Description Tab Section -->
            <div class="border-t border-gray-100 bg-gray-50/40 p-6 sm:p-8 lg:p-10">
                <div class="max-w-4xl">
                    <h3 class="text-lg font-black text-gray-900 mb-3 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <?= $locale === 'bn' ? 'পণ্যের বিবরণ' : 'Product Description' ?>
                    </h3>
                    <div class="text-gray-700 text-sm leading-relaxed space-y-3 prose max-w-none">
                        <?php if (!empty($product['description'])): ?>
                            <?= nl2br(htmlspecialchars($product['description'])) ?>
                        <?php else: ?>
                            <p class="text-gray-500 italic">
                                <?= $locale === 'bn' ? 'এই পণ্যের জন্য এখনও বিস্তারিত কোনো বিবরণ দেওয়া হয়নি। আমাদের সকল পণ্য শতভাগ তাজা ও সেরা উৎস থেকে সংগৃহীত।' : 'No detailed description available for this item. All our grocery items are sourced fresh from verified suppliers.' ?>
                            </p>
                        <?php endif; ?>

                        <?php if (!empty($product['tags'])): 
                            $tagList = array_filter(array_map('trim', explode(',', $product['tags'])));
                            if (!empty($tagList)):
                        ?>
                            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center gap-2 flex-wrap">
                                <span class="text-xs font-bold text-gray-500 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    <?= $locale === 'bn' ? 'ট্যাগসমূহ:' : 'Tags:' ?>
                                </span>
                                <?php foreach ($tagList as $tag): ?>
                                    <a href="<?= $base ?>/?search=<?= urlencode($tag) ?>" class="inline-flex items-center px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-emerald-100 hover:text-emerald-800 text-gray-700 text-xs font-medium transition-colors">
                                        #<?= htmlspecialchars($tag) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; endif; ?>
                    </div>
                </div>
            </div>

        </div>

        <!-- Related Products Section -->
        <?php if (!empty($relatedProducts)): ?>
            <div class="mt-12">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-black text-gray-900">
                            <?= $locale === 'bn' ? 'একই ধরনের আরও পণ্য' : 'Related Products' ?>
                        </h2>
                        <p class="text-xs sm:text-sm text-gray-500 mt-0.5">
                            <?= $locale === 'bn' ? 'আপনার প্রয়োজনীয় অন্যান্য তাজা খাদ্যপণ্য' : 'Other fresh essentials you might need' ?>
                        </p>
                    </div>
                    <?php if (!empty($product['category_id'])): ?>
                        <a href="<?= $base ?>/category?id=<?= $product['category_id'] ?>" class="text-xs sm:text-sm font-bold text-emerald-700 hover:text-emerald-800 flex items-center gap-1 group">
                            <span><?= $locale === 'bn' ? 'সবগুলো দেখুন' : 'View All' ?></span>
                            <span class="group-hover:translate-x-0.5 transition-transform">→</span>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Product Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-3 sm:gap-5">
                    <?php foreach ($relatedProducts as $relProduct): ?>
                        <?php
                        $relSellPrice = floatval($relProduct['sell_price']);
                        $relRegPrice = !empty($relProduct['regular_price']) ? floatval($relProduct['regular_price']) : null;
                        $relHasDisc = ($relRegPrice && $relRegPrice > $relSellPrice);
                        $relDiscPercent = $relHasDisc ? round((($relRegPrice - $relSellPrice) / $relRegPrice) * 100) : 0;
                        $relImg = !empty($relProduct['image_path']) ? htmlspecialchars($relProduct['image_path']) : $base . '/images/default-product.svg';
                        ?>
                        <div class="product-card bg-white rounded-2xl border border-gray-100 hover:border-emerald-300 shadow-2xs hover:shadow-md transition-all duration-300 flex flex-col overflow-hidden group">
                            
                            <!-- Image Link -->
                            <a href="<?= $base ?>/product?id=<?= $relProduct['id'] ?>" class="relative block bg-white h-44 sm:h-52 w-full p-2.5 flex items-center justify-center overflow-hidden">
                                <?php if ($relHasDisc): ?>
                                    <span class="absolute top-2.5 left-2.5 bg-rose-500 text-white text-[10px] font-black px-1.5 py-0.5 rounded-md z-10 shadow-2xs">
                                        -<?= $relDiscPercent ?>%
                                    </span>
                                <?php endif; ?>
                                <img src="<?= $relImg ?>" 
                                     alt="<?= htmlspecialchars($relProduct['name']) ?>" 
                                     class="h-full w-full object-contain mix-blend-multiply group-hover:scale-105 transition-transform duration-300"
                                     loading="lazy"
                                     onerror="this.src='<?= $base ?>/images/default-product.svg'">
                            </a>

                            <!-- Body -->
                            <div class="p-3 flex flex-col flex-grow">
                                <a href="<?= $base ?>/product?id=<?= $relProduct['id'] ?>" class="font-bold text-gray-900 text-xs sm:text-sm leading-snug line-clamp-2 hover:text-emerald-700 transition-colors mb-1">
                                    <?= htmlspecialchars($relProduct['name']) ?>
                                </a>
                                
                                <div class="text-[11px] text-gray-400 font-medium mb-2">
                                    <?= htmlspecialchars($relProduct['selling_unit'] ?? $relProduct['base_unit'] ?? '1 Unit') ?>
                                </div>

                                <div class="flex items-baseline gap-1.5 mb-3 mt-auto">
                                    <span class="text-xs font-bold text-emerald-700">৳</span>
                                    <span class="text-base font-black text-gray-900"><?= number_format($relSellPrice) ?></span>
                                    <?php if ($relHasDisc): ?>
                                        <span class="text-[11px] text-gray-400 line-through">৳<?= number_format($relRegPrice) ?></span>
                                    <?php endif; ?>
                                </div>

                                <button type="button" 
                                        onclick="addToCartAjax(<?= $relProduct['id'] ?>, this)"
                                        class="w-full py-2 px-3 rounded-xl bg-emerald-50 hover:bg-emerald-600 text-emerald-800 hover:text-white font-bold text-xs flex items-center justify-center gap-1.5 transition-all cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    <span><?= $__('add_to_bag') ?></span>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<script>
// State for the Detail View
window.DETAIL_STATE = {
    productId: <?= (int)$product['id'] ?>,
    selectedVariantTitle: <?= json_encode($initialTitle) ?>,
    selectedVariantPrice: <?= floatval($initialPrice) ?>,
    selectedVariantQty: <?= floatval($initialQty) ?>,
    maxStock: <?= $maxOrderQty ?>
};

function selectDetailVariant(btn, title, price, qty) {
    // Update pills active state
    document.querySelectorAll('.detail-variant-pill').forEach(el => {
        el.className = 'detail-variant-pill px-4 py-2 rounded-xl text-xs sm:text-sm font-bold border transition-all cursor-pointer border-gray-200 text-gray-700 bg-white hover:border-gray-300';
    });
    btn.className = 'detail-variant-pill px-4 py-2 rounded-xl text-xs sm:text-sm font-bold border transition-all cursor-pointer border-emerald-600 bg-emerald-50 text-emerald-800 ring-2 ring-emerald-500/20 shadow-xs';

    window.DETAIL_STATE.selectedVariantTitle = title;
    window.DETAIL_STATE.selectedVariantPrice = price;
    window.DETAIL_STATE.selectedVariantQty = qty;

    // Update display price
    const priceEl = document.getElementById('detail-current-price');
    if (priceEl) {
        priceEl.textContent = Number(price).toLocaleString();
    }
}

function changeDetailQty(delta) {
    const qtyInput = document.getElementById('detail-qty');
    if (!qtyInput) return;
    let curr = parseInt(qtyInput.value) || 1;
    curr += delta;
    if (curr < 1) curr = 1;
    if (curr > window.DETAIL_STATE.maxStock) curr = window.DETAIL_STATE.maxStock;
    qtyInput.value = curr;
}

function submitDetailAddToCart(productId, btn) {
    const qtyInput = document.getElementById('detail-qty');
    const quantity = parseInt(qtyInput?.value) || 1;

    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = `<svg class="animate-spin h-5 w-5 text-white mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;

    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);
    
    if (window.DETAIL_STATE.selectedVariantTitle) {
        formData.append('variant_title', window.DETAIL_STATE.selectedVariantTitle);
    }
    if (window.DETAIL_STATE.selectedVariantPrice) {
        formData.append('variant_price', window.DETAIL_STATE.selectedVariantPrice);
    }
    if (window.DETAIL_STATE.selectedVariantQty) {
        formData.append('variant_qty', window.DETAIL_STATE.selectedVariantQty);
    }

    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (csrfMeta) formData.append('csrf_token', csrfMeta.getAttribute('content'));

    fetch((window.APP_BASE || '') + '/cart/add', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            if (typeof updateCartUI === 'function') {
                updateCartUI(data);
            }
            if (typeof showToast === 'function') {
                showToast(data.message || 'কার্টে যোগ করা হয়েছে');
            }
            // Auto open cart drawer
            if (typeof toggleCartDrawer === 'function') {
                toggleCartDrawer(true);
            }
        } else {
            if (typeof showToast === 'function') {
                showToast(data.message || 'ত্রুটি ঘটেছে', 'error');
            }
        }
    })
    .catch(() => {
        if (typeof showToast === 'function') {
            showToast('ত্রুটি ঘটেছে', 'error');
        }
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    });
}
</script>

<?php 
$content = ob_get_clean();
require 'layout.php';
?>
