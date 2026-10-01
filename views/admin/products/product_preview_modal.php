<?php
// Reusable Product Quick Preview Modal for Admin
$previewBase = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
?>
<!-- Product Quick Preview Modal -->
<div id="productPreviewModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop with blur -->
    <div id="productPreviewBackdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity opacity-0" onclick="closeProductPreviewModal()"></div>

    <div class="flex min-h-screen items-center justify-center p-3 sm:p-4 text-center">
        <div id="productPreviewPanel" class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 w-full max-w-3xl border border-slate-200/80 scale-95 opacity-0 flex flex-col max-h-[90vh]">
            
            <!-- Modal Header -->
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-950 text-white">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-500/20 border border-indigo-400/30 flex items-center justify-center text-indigo-400 text-xl flex-shrink-0">
                        <ion-icon name="eye-outline"></ion-icon>
                    </div>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-white flex items-center gap-2">
                            <span>পণ্য প্রিভিউ ও স্পেসিফিকেশন</span>
                            <span id="previewStatusBadge" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-700 text-slate-200 border border-slate-600">লোড হচ্ছে...</span>
                        </h3>
                        <p id="previewHeaderSubtitle" class="text-xs text-slate-300 line-clamp-1 mt-0.5">বিস্তারিত তথ্য ও সরাসরি ওয়েবসাইট ভিউ</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a id="previewStorefrontBtn" href="#" target="_blank" class="px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold flex items-center gap-1.5 transition-colors border border-white/15" title="ওয়েবসাইটে সরাসরি দেখুন">
                        <ion-icon name="globe-outline" class="text-emerald-400"></ion-icon>
                        <span class="hidden sm:inline">ওয়েবসাইটে দেখুন</span>
                        <ion-icon name="open-outline" class="text-xs"></ion-icon>
                    </a>
                    <button type="button" onclick="closeProductPreviewModal()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors">
                        <ion-icon name="close-outline" class="text-lg"></ion-icon>
                    </button>
                </div>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="overflow-y-auto flex-1 p-5 sm:p-6 bg-slate-50/50">
                
                <!-- Loading State -->
                <div id="previewLoadingState" class="py-16 text-center">
                    <div class="inline-block animate-spin w-10 h-10 border-4 border-indigo-500 border-t-transparent rounded-full mb-3"></div>
                    <p class="text-sm font-semibold text-secondary-600">পণ্যের তথ্য লোড করা হচ্ছে...</p>
                </div>

                <!-- Product Data Container -->
                <div id="previewDataContainer" class="hidden space-y-6">
                    <!-- Top Section: Image & Main Details -->
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-5 items-start">
                        
                        <!-- Left: Product Image Card -->
                        <div class="md:col-span-5 flex flex-col items-center">
                            <div class="w-full aspect-square bg-white rounded-2xl border border-secondary-200 shadow-sm flex items-center justify-center p-3 relative overflow-hidden group">
                                <img id="previewImage" src="" alt="Product" class="w-full h-full object-contain mix-blend-multiply transition-transform duration-300 group-hover:scale-105" onerror="this.src='<?= $previewBase ?>/images/default-product.svg'">
                                
                                <!-- Floating Badges on Image -->
                                <div id="previewDiscountBadge" class="hidden absolute top-2.5 left-2.5 bg-rose-600 text-white text-[11px] font-black px-2 py-0.5 rounded-lg shadow-sm">
                                    0% OFF
                                </div>
                                <div id="previewSpecialBadge" class="hidden absolute top-2.5 right-2.5 bg-amber-500 text-white text-[10px] font-black px-2 py-0.5 rounded-lg shadow-sm">
                                    Special
                                </div>
                            </div>

                            <!-- Image Action Hint -->
                            <div class="mt-2.5 flex items-center justify-center gap-2 text-xs">
                                <span id="previewVerifiedBadge" class="inline-flex items-center gap-1 font-bold text-[11px]"></span>
                            </div>
                        </div>

                        <!-- Right: Title, Categorization, Pricing, Stock -->
                        <div class="md:col-span-7 space-y-3.5">
                            <div>
                                <div class="flex items-center gap-1.5 flex-wrap text-[11px] text-secondary-500 font-medium mb-1">
                                    <span class="px-2 py-0.5 rounded-md bg-secondary-100 text-secondary-700 font-bold" id="previewCategory">ক্যাটাগরি</span>
                                    <span>•</span>
                                    <span class="text-secondary-600 font-semibold" id="previewVendor">ভেন্ডর</span>
                                    <span id="previewBrandDot">•</span>
                                    <span class="text-secondary-600 font-semibold" id="previewBrand">ব্র্যান্ড</span>
                                </div>
                                <h2 id="previewName" class="text-lg sm:text-xl font-black text-secondary-900 leading-snug">
                                    পণ্যের নাম
                                </h2>
                                <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                                    <span class="text-xs text-secondary-400 font-mono bg-white px-2 py-0.5 rounded border border-secondary-200 flex items-center gap-1">
                                        <ion-icon name="barcode-outline" class="text-sm"></ion-icon>
                                        SKU: <strong class="text-secondary-800" id="previewSku">N/A</strong>
                                    </span>
                                    <span id="previewBaseUnitBadge" class="text-[11px] font-bold px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        একক: <span id="previewBaseUnit">pcs</span>
                                    </span>
                                </div>
                            </div>

                            <!-- Pricing Dashboard Box -->
                            <div class="bg-white rounded-2xl border border-secondary-200 p-3.5 shadow-2xs">
                                <div class="text-[10px] font-bold text-secondary-400 uppercase tracking-wider mb-2">মূল্য ও লাভ পর্যালোচনা (Pricing & Margin)</div>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-center">
                                    <!-- Sell Price -->
                                    <div class="bg-emerald-50/70 border border-emerald-200/80 rounded-xl p-2.5">
                                        <div class="text-[10px] text-emerald-700 font-semibold">বিক্রয় মূল্য</div>
                                        <div class="text-base sm:text-lg font-black text-emerald-800 mt-0.5" id="previewSellPrice">৳ 0</div>
                                    </div>
                                    <!-- Regular Price -->
                                    <div class="bg-secondary-50 border border-secondary-200 rounded-xl p-2.5">
                                        <div class="text-[10px] text-secondary-500 font-semibold">MRP / গায়ের দাম</div>
                                        <div class="text-sm sm:text-base font-bold text-secondary-700 mt-0.5" id="previewRegularPrice">৳ 0</div>
                                    </div>
                                    <!-- Buy Price -->
                                    <div class="bg-amber-50/70 border border-amber-200/80 rounded-xl p-2.5">
                                        <div class="text-[10px] text-amber-700 font-semibold">ক্রয় মূল্য (Cost)</div>
                                        <div class="text-sm sm:text-base font-bold text-amber-800 mt-0.5" id="previewBuyPrice">৳ 0</div>
                                    </div>
                                    <!-- Profit Margin -->
                                    <div class="bg-blue-50/70 border border-blue-200/80 rounded-xl p-2.5">
                                        <div class="text-[10px] text-blue-700 font-semibold">আনুমানিক লাভ</div>
                                        <div class="text-sm sm:text-base font-black text-blue-800 mt-0.5" id="previewMargin">৳ 0</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Stock & Inventory Details -->
                            <div class="bg-white rounded-2xl border border-secondary-200 p-3.5 shadow-2xs flex items-center justify-between gap-3">
                                <div>
                                    <span class="text-[10px] font-bold text-secondary-400 uppercase tracking-wider block">বর্তমান মজুদ (Stock)</span>
                                    <div class="text-sm font-black text-secondary-900 mt-0.5" id="previewStockFormatted">0 pcs</div>
                                </div>
                                <div id="previewPurchaseUnitBox" class="text-right border-l border-secondary-100 pl-4 hidden">
                                    <span class="text-[10px] font-bold text-secondary-400 uppercase tracking-wider block">পাইকারি প্যাকেজিং</span>
                                    <div class="text-xs font-semibold text-secondary-700 mt-0.5" id="previewPurchaseUnitText">১ বস্তা = ৫০ কেজি</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Middle Section: Variants & Addons (if any) -->
                    <div id="previewVariantsContainer" class="hidden bg-white rounded-2xl border border-secondary-200 p-4 shadow-2xs">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-base text-indigo-600"><ion-icon name="layers-outline"></ion-icon></span>
                            <h4 class="text-xs font-bold text-secondary-800 uppercase tracking-wider">উপলব্ধ সাইজ / ওজন ভ্যারিয়েন্ট (Size Variants)</h4>
                        </div>
                        <div id="previewVariantsList" class="grid grid-cols-2 sm:grid-cols-3 gap-2"></div>
                    </div>

                    <div id="previewAddonsContainer" class="hidden bg-white rounded-2xl border border-secondary-200 p-4 shadow-2xs">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-base text-purple-600"><ion-icon name="cut-outline"></ion-icon></span>
                            <h4 class="text-xs font-bold text-secondary-800 uppercase tracking-wider">কাটিং, ড্রেসিং ও কাস্টম অপশন (Addons / Options)</h4>
                        </div>
                        <div id="previewAddonsList" class="flex flex-wrap gap-2"></div>
                    </div>

                    <!-- Description & Tags Section -->
                    <div class="bg-white rounded-2xl border border-secondary-200 p-4 shadow-2xs space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="text-base text-secondary-500"><ion-icon name="document-text-outline"></ion-icon></span>
                            <h4 class="text-xs font-bold text-secondary-800 uppercase tracking-wider">পণ্যের বিবরণ (Description)</h4>
                        </div>
                        <div id="previewDescription" class="text-xs text-secondary-600 leading-relaxed whitespace-pre-line max-h-36 overflow-y-auto bg-secondary-50/50 p-3 rounded-xl border border-secondary-100">
                            কোনো বিবরণ নেই।
                        </div>

                        <!-- Tags -->
                        <div id="previewTagsWrapper" class="pt-2 border-t border-secondary-100 flex items-center gap-2 flex-wrap">
                            <span class="text-[10px] font-bold text-secondary-400 uppercase">ট্যাগ:</span>
                            <div id="previewTagsList" class="flex items-center gap-1.5 flex-wrap"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer with Quick Actions -->
            <div class="px-5 py-3.5 bg-white border-t border-secondary-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-xs font-mono text-secondary-400 flex items-center gap-1">
                    <span>আইডি:</span>
                    <strong class="text-secondary-700 font-bold" id="previewProductIdText">#0</strong>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <!-- Duplicate Button Form -->
                    <form id="previewDuplicateForm" action="<?= $previewBase ?>/admin/products/duplicate" method="POST" onsubmit="return confirm('এই পণ্যটির একটি হুবহু কপি (Duplicate) তৈরি করতে চান?');" class="inline">
                        <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                        <input type="hidden" name="id" id="previewDuplicateId" value="">
                        <button type="submit" class="px-3.5 py-2 rounded-xl bg-purple-50 text-purple-700 hover:bg-purple-100 border border-purple-200 text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer shadow-2xs">
                            <ion-icon name="copy-outline" class="text-base"></ion-icon>
                            <span>ডুপ্লিকেট করুন</span>
                        </button>
                    </form>

                    <!-- Edit Button -->
                    <a id="previewEditBtn" href="#" class="px-4 py-2 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm cursor-pointer">
                        <ion-icon name="create-outline" class="text-base"></ion-icon>
                        <span>এডিট করুন</span>
                    </a>

                    <!-- Close Button -->
                    <button type="button" onclick="closeProductPreviewModal()" class="px-3.5 py-2 rounded-xl bg-secondary-100 hover:bg-secondary-200 text-secondary-700 text-xs font-bold transition-colors">
                        বন্ধ করুন
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
window.openProductPreviewModal = function(productId) {
    const modal = document.getElementById('productPreviewModal');
    const backdrop = document.getElementById('productPreviewBackdrop');
    const panel = document.getElementById('productPreviewPanel');
    const loading = document.getElementById('previewLoadingState');
    const container = document.getElementById('previewDataContainer');

    if (!modal) return;

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // Animation
    setTimeout(() => {
        backdrop.classList.remove('opacity-0');
        panel.classList.remove('opacity-0', 'scale-95');
        panel.classList.add('opacity-100', 'scale-100');
    }, 10);

    loading.classList.remove('hidden');
    container.classList.add('hidden');

    // Fetch product details via AJAX
    fetch('<?= $previewBase ?>/admin/products/get?id=' + productId)
        .then(res => res.json())
        .then(data => {
            if (!data.success || !data.product) {
                alert(data.message || 'পণ্যের তথ্য লোড করা যায়নি!');
                closeProductPreviewModal();
                return;
            }

            const p = data.product;

            // Header info
            document.getElementById('previewProductIdText').textContent = '#' + p.id;
            document.getElementById('previewHeaderSubtitle').textContent = p.name;
            document.getElementById('previewName').textContent = p.name;
            document.getElementById('previewCategory').textContent = p.category_name || 'Uncategorized';
            document.getElementById('previewVendor').textContent = p.vendor_name || 'কোনো ভেন্ডর নেই';
            
            const brandEl = document.getElementById('previewBrand');
            const brandDot = document.getElementById('previewBrandDot');
            if (p.brand_name && p.brand_name !== 'None') {
                brandEl.textContent = p.brand_name;
                brandEl.classList.remove('hidden');
                brandDot.classList.remove('hidden');
            } else {
                brandEl.classList.add('hidden');
                brandDot.classList.add('hidden');
            }

            document.getElementById('previewSku').textContent = p.sku || 'N/A';
            document.getElementById('previewBaseUnit').textContent = p.base_unit || 'pcs';

            // Status Badge in header
            const statusEl = document.getElementById('previewStatusBadge');
            const avail = p.availability_status || 'pending';
            if (p.is_deleted == 1 || avail === 'archived') {
                statusEl.className = 'text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-slate-700 text-slate-100 border border-slate-600';
                statusEl.textContent = '📦 আর্কাইভড';
            } else if (avail === 'in_stock') {
                statusEl.className = 'text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/30';
                statusEl.textContent = '🟢 ইন স্টক';
            } else if (avail === 'out_of_stock') {
                statusEl.className = 'text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-red-500/20 text-red-300 border border-red-400/30';
                statusEl.textContent = '🔴 স্টক শেষ';
            } else {
                statusEl.className = 'text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-400/30';
                statusEl.textContent = '⏳ পেন্ডিং';
            }

            // Image
            const imgEl = document.getElementById('previewImage');
            imgEl.src = p.image_url || '<?= $previewBase ?>/images/default-product.svg';

            // Discount Badge on Image
            const discEl = document.getElementById('previewDiscountBadge');
            if (p.has_discount && p.discount_percent > 0) {
                discEl.textContent = p.discount_percent + '% OFF';
                discEl.classList.remove('hidden');
            } else {
                discEl.classList.add('hidden');
            }

            // Special Badge
            const specEl = document.getElementById('previewSpecialBadge');
            if (p.special_badge && p.special_badge !== 'none') {
                const labels = {
                    'bogo': '🎁 BOGO (১+১)',
                    'hot_deal': '⚡ Hot Deal',
                    'fresh_catch': '🐟 Fresh Catch',
                    'halal_meat': '🥩 Halal Meat'
                };
                specEl.textContent = labels[p.special_badge] || p.special_badge;
                specEl.classList.remove('hidden');
            } else {
                specEl.classList.add('hidden');
            }

            // Verified Badge
            const verEl = document.getElementById('previewVerifiedBadge');
            if (p.is_verified == 1) {
                verEl.className = 'inline-flex items-center gap-1 font-bold text-[11px] text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200';
                verEl.innerHTML = '<span>✓</span> <span>অ্যাডমিন কর্তৃক যাচাইকৃত পণ্য</span>';
            } else {
                verEl.className = 'inline-flex items-center gap-1 font-normal text-[11px] text-secondary-400 bg-secondary-50 px-2 py-0.5 rounded-md border border-secondary-200';
                verEl.innerHTML = '<span>⏳</span> <span>অযাচাইকৃত পণ্য</span>';
            }

            // Pricing
            const sellPrice = parseFloat(p.sell_price) || 0;
            const regPrice = parseFloat(p.regular_price) || 0;
            const buyPrice = parseFloat(p.buy_price) || 0;
            const margin = sellPrice - buyPrice;
            const marginPct = buyPrice > 0 ? Math.round((margin / buyPrice) * 100) : 0;

            document.getElementById('previewSellPrice').textContent = '৳ ' + Number(sellPrice).toLocaleString();
            document.getElementById('previewRegularPrice').textContent = regPrice > 0 ? '৳ ' + Number(regPrice).toLocaleString() : 'N/A';
            document.getElementById('previewBuyPrice').textContent = '৳ ' + Number(buyPrice).toLocaleString();
            
            const marginEl = document.getElementById('previewMargin');
            if (margin >= 0) {
                marginEl.textContent = '৳ ' + Number(margin).toLocaleString() + (buyPrice > 0 ? ` (${marginPct}%)` : '');
                marginEl.className = 'text-sm sm:text-base font-black text-blue-800 mt-0.5';
            } else {
                marginEl.textContent = '-৳ ' + Number(Math.abs(margin)).toLocaleString();
                marginEl.className = 'text-sm sm:text-base font-black text-rose-700 mt-0.5';
            }

            // Stock
            document.getElementById('previewStockFormatted').textContent = p.stock_formatted || `${p.stock_qty} ${p.base_unit || 'pcs'}`;

            // Bulk packaging conversion
            const bulkBox = document.getElementById('previewPurchaseUnitBox');
            if (p.purchase_unit && parseFloat(p.purchase_unit_qty) > 1) {
                document.getElementById('previewPurchaseUnitText').textContent = `১ ${p.purchase_unit} = ${parseFloat(p.purchase_unit_qty)} ${p.base_unit}`;
                bulkBox.classList.remove('hidden');
            } else {
                bulkBox.classList.add('hidden');
            }

            // Variants
            const varContainer = document.getElementById('previewVariantsContainer');
            const varList = document.getElementById('previewVariantsList');
            varList.innerHTML = '';
            if (p.variants && p.variants.length > 0) {
                p.variants.forEach(v => {
                    const vDiv = document.createElement('div');
                    vDiv.className = 'p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-left';
                    vDiv.innerHTML = `
                        <div class="font-bold text-xs text-secondary-900">${v.name || 'Variant'}</div>
                        <div class="text-[11px] text-emerald-700 font-black mt-0.5">৳ ${Number(v.sell_price || v.price || 0).toLocaleString()}</div>
                    `;
                    varList.appendChild(vDiv);
                });
                varContainer.classList.remove('hidden');
            } else {
                varContainer.classList.add('hidden');
            }

            // Addons
            const addContainer = document.getElementById('previewAddonsContainer');
            const addList = document.getElementById('previewAddonsList');
            addList.innerHTML = '';
            if (p.addons && p.addons.length > 0) {
                p.addons.forEach(a => {
                    const aSpan = document.createElement('span');
                    aSpan.className = 'px-3 py-1.5 rounded-xl bg-purple-50 text-purple-800 border border-purple-200 text-xs font-semibold flex items-center gap-1.5';
                    const extra = parseFloat(a.price || a.extra_price || 0);
                    aSpan.innerHTML = `
                        <span>${a.name || a.title || 'Addon'}</span>
                        ${extra > 0 ? `<strong class="text-purple-600 font-bold">(+৳ ${extra})</strong>` : '<span class="text-[10px] text-emerald-600 font-bold">ফ্রি</span>'}
                    `;
                    addList.appendChild(aSpan);
                });
                addContainer.classList.remove('hidden');
            } else {
                addContainer.classList.add('hidden');
            }

            // Description
            const descEl = document.getElementById('previewDescription');
            descEl.textContent = p.description && p.description.trim() ? p.description.trim() : 'এই পণ্যের জন্য কোনো বিস্তারিত বিবরণ উল্লেখ করা হয়নি।';

            // Tags
            const tagsWrapper = document.getElementById('previewTagsWrapper');
            const tagsList = document.getElementById('previewTagsList');
            tagsList.innerHTML = '';
            if (p.tags && p.tags.trim()) {
                const tags = p.tags.split(',').map(t => t.trim()).filter(Boolean);
                if (tags.length > 0) {
                    tags.forEach(tag => {
                        const tSpan = document.createElement('span');
                        tSpan.className = 'text-[11px] px-2 py-0.5 rounded-md bg-secondary-100 text-secondary-700 font-medium';
                        tSpan.textContent = '#' + tag;
                        tagsList.appendChild(tSpan);
                    });
                    tagsWrapper.classList.remove('hidden');
                } else {
                    tagsWrapper.classList.add('hidden');
                }
            } else {
                tagsWrapper.classList.add('hidden');
            }

            // Action Links
            document.getElementById('previewStorefrontBtn').href = p.shop_url;
            document.getElementById('previewEditBtn').href = '<?= $previewBase ?>/admin/products/edit?id=' + p.id;
            document.getElementById('previewDuplicateId').value = p.id;

            // Show container
            loading.classList.add('hidden');
            container.classList.remove('hidden');
        })
        .catch(err => {
            console.error('Failed to load preview:', err);
            loading.innerHTML = `
                <div class="text-rose-500 font-bold mb-2">❌ তথ্য লোড করতে সমস্যা হয়েছে!</div>
                <button type="button" onclick="closeProductPreviewModal()" class="px-4 py-1.5 rounded-xl bg-secondary-100 text-secondary-700 text-xs font-semibold">বন্ধ করুন</button>
            `;
        });
};

window.closeProductPreviewModal = function() {
    const modal = document.getElementById('productPreviewModal');
    const backdrop = document.getElementById('productPreviewBackdrop');
    const panel = document.getElementById('productPreviewPanel');

    if (!modal) return;

    backdrop.classList.add('opacity-0');
    panel.classList.remove('opacity-100', 'scale-100');
    panel.classList.add('opacity-0', 'scale-95');

    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }, 200);
};

// Close on ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('productPreviewModal');
        if (modal && !modal.classList.contains('hidden')) {
            closeProductPreviewModal();
        }
    }
});
</script>
