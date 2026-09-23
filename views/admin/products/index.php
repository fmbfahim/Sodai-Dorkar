<?php
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
// Function to compute full ancestry path for all categories
if (!function_exists('getCategoryBreadcrumbPath')) {
    function getCategoryBreadcrumbPath($catId, $allCats) {
        $path = [];
        $currentId = $catId;
        $safety = 0;
        while ($currentId && $safety < 10) {
            $safety++;
            $found = null;
            foreach ($allCats as $c) {
                if ($c['id'] == $currentId) {
                    $found = $c;
                    break;
                }
            }
            if ($found) {
                array_unshift($path, $found['name']);
                $currentId = !empty($found['parent_id']) ? $found['parent_id'] : null;
            } else {
                break;
            }
        }
        return implode(' › ', $path);
    }
}

$categoriesWithPaths = [];
foreach ($categories as $c) {
    $categoriesWithPaths[] = [
        'id' => $c['id'],
        'name' => $c['name'],
        'path' => getCategoryBreadcrumbPath($c['id'], $categories)
    ];
}
usort($categoriesWithPaths, function($a, $b) {
    return strcasecmp($a['path'], $b['path']);
});
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Form Section (Single Product Add) -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-100 p-6 sticky top-6">
            <h3 class="text-lg font-bold text-secondary-900 mb-1 flex items-center gap-2">
                <ion-icon name="add-circle-outline" class="text-primary-600 text-xl"></ion-icon>
                নতুন পণ্য যোগ করুন
            </h3>
            <p class="text-xs text-secondary-500 mb-5">মৌলিক তথ্য, ক্যাটাগরি ও প্যাকেজিং নির্ধারণ করে সেভ করুন।</p>

            <form action="<?= $base ?>/admin/products/store" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                <div class="mb-4">
                    <label class="block text-secondary-700 text-xs font-bold uppercase tracking-wider mb-2" for="name">
                        পণ্যের নাম (Product Name) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" class="w-full px-3.5 py-2.5 border border-secondary-300 rounded-xl focus:ring-2 focus:ring-primary-500 text-sm font-medium" required placeholder="যেমন: মিনিকেট চাল ৫০ কেজি বস্তা">
                </div>
                
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div>
                        <label class="block text-secondary-700 text-xs font-bold uppercase tracking-wider mb-2" for="sku">SKU / বারকোড</label>
                        <input type="text" id="sku" name="sku" class="w-full px-3 py-2 border border-secondary-300 rounded-xl text-xs font-mono focus:ring-2 focus:ring-primary-500" placeholder="Auto Code">
                    </div>
                    <div>
                        <label class="block text-secondary-700 text-xs font-bold uppercase tracking-wider mb-2" for="vendor_id">সাপ্লায়ার / ভেন্ডর</label>
                        <select id="vendor_id" name="vendor_id" class="w-full px-3 py-2 border border-secondary-300 rounded-xl text-xs focus:ring-2 focus:ring-primary-500 bg-white">
                            <option value="">কোনো ভেন্ডর নেই</option>
                            <?php foreach ($vendors as $v): ?>
                                <option value="<?php echo $v['id']; ?>"><?php echo htmlspecialchars($v['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Cascading Step-by-Step Category Selector for Single Add -->
                <div class="mb-4 bg-secondary-50/70 p-3.5 rounded-xl border border-secondary-200">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-secondary-800 text-xs font-bold uppercase tracking-wider flex items-center gap-1.5">
                            <ion-icon name="git-branch-outline" class="text-primary-600 text-sm"></ion-icon>
                            ক্যাটাগরি ও সাব-ক্যাটাগরি <span class="text-red-500">*</span>
                        </label>
                        <span class="text-[10px] text-primary-600 font-medium">🔍 নাম লিখে খুঁজুন বা ধাপে ধাপে সিলেক্ট করুন</span>
                    </div>

                    <input type="hidden" name="category_id" id="single_category_id" required>
                    
                    <div id="single_category_cascading_container" class="space-y-2">
                        <!-- Injected dynamically by CascadingCategorySelector JS -->
                    </div>
                </div>

                <!-- Multi-Unit Quick Packaging Picker for Single Add -->
                <div class="bg-secondary-50/70 p-3.5 rounded-xl border border-secondary-200 mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-secondary-800 text-xs font-bold uppercase tracking-wider flex items-center gap-1.5">
                            <ion-icon name="scale-outline" class="text-primary-600"></ion-icon>
                            প্যাকেজিং ও একক নির্ধারণ
                        </label>
                        <a href="<?= $base ?>/admin/settings/units" target="_blank" class="text-[10px] text-primary-600 hover:text-primary-700 font-bold flex items-center gap-0.5">
                            <ion-icon name="settings-outline" class="text-xs"></ion-icon> একক সেটিংস
                        </a>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-2 mb-2">
                        <div>
                            <span class="text-[11px] text-secondary-500 block mb-1 font-semibold">মূল স্টক একক:</span>
                            <select name="base_unit" id="single_base_unit" class="w-full px-2.5 py-1.5 border border-secondary-300 rounded-lg text-xs bg-white font-bold text-secondary-800">
                                <option value="kg">কেজি (kg)</option>
                                <option value="liter">লিটার (liter)</option>
                                <option value="pcs" selected>পিস (pcs)</option>
                                <option value="gm">গ্রাম (gm)</option>
                                <option value="ml">মিলি (ml)</option>
                            </select>
                        </div>
                        <div>
                            <span class="text-[11px] text-secondary-500 block mb-1 font-semibold">ক্রয় একক (ঐচ্ছিক):</span>
                            <select id="single_purchase_unit_select" onchange="onSinglePurchaseUnitChange(this)"
                                    class="w-full px-2 py-1.5 border border-secondary-300 rounded-lg text-xs bg-white font-medium text-secondary-800">
                                <option value="" data-qty="1">-- সাধারণ একক --</option>
                                <?php foreach ($packagingUnits as $pu): ?>
                                    <option value="<?php echo htmlspecialchars($pu['name']); ?>" 
                                            data-qty="<?php echo floatval($pu['default_qty']); ?>" 
                                            data-base="<?php echo htmlspecialchars($pu['base_unit']); ?>">
                                        <?php echo htmlspecialchars($pu['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                                <option value="__custom__">➕ কাস্টম একক...</option>
                            </select>
                            <input type="hidden" name="purchase_unit" id="single_purchase_unit" value="">
                        </div>
                    </div>

                    <!-- Custom Purchase Unit input for Single Add -->
                    <div id="single_custom_purchase_unit_wrapper" class="mb-2 hidden">
                        <input type="text" id="single_custom_purchase_unit_input" 
                               oninput="document.getElementById('single_purchase_unit').value = this.value;"
                               placeholder="কাস্টম ক্রয় একক লিখুন (যেমন: ক্যান, কার্টন)" 
                               class="w-full px-2.5 py-1.5 border border-primary-300 bg-primary-50/50 rounded-lg text-xs font-semibold">
                    </div>

                    <div class="flex items-center justify-between text-[11px] text-secondary-600 pt-1">
                        <span>১ ক্রয় এককে পরিমাণ:</span>
                        <input type="number" step="0.001" name="purchase_unit_qty" id="single_purchase_unit_qty" value="1" 
                               class="w-24 px-2 py-1 border border-secondary-300 rounded-lg text-xs text-center font-bold bg-white">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2 mb-2">
                    <div>
                        <label class="block text-secondary-700 text-[11px] font-bold uppercase tracking-wider mb-1" for="buy_price">ক্রয় খরচ (৳)</label>
                        <input type="number" step="0.01" id="buy_price" name="buy_price" class="w-full px-2.5 py-1.5 border border-secondary-300 rounded-lg text-xs font-semibold focus:ring-2 focus:ring-primary-500" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-secondary-700 text-[11px] font-bold uppercase tracking-wider mb-1" for="single_regular_price">রেগুলার MRP</label>
                        <input type="number" step="0.01" id="single_regular_price" name="regular_price" oninput="calculateSingleDiscount()" class="w-full px-2.5 py-1.5 border border-secondary-300 rounded-lg text-xs font-semibold focus:ring-2 focus:ring-primary-500" placeholder="১২০.০০">
                    </div>
                    <div>
                        <label class="block text-secondary-700 text-[11px] font-bold uppercase tracking-wider mb-1" for="single_sell_price">বিক্রয় মূল্য <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" id="single_sell_price" name="sell_price" oninput="calculateSingleDiscount()" class="w-full px-2.5 py-1.5 border border-emerald-300 rounded-lg text-xs font-black text-emerald-700 focus:ring-2 focus:ring-emerald-500" required placeholder="১০০.০০">
                    </div>
                </div>

                <div id="single_discount_badge" class="mb-3 hidden text-[11px] text-emerald-800 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200 flex items-center gap-1 font-bold">
                    <ion-icon name="pricetag-outline" class="text-red-500"></ion-icon>
                    <span id="single_discount_text"></span>
                </div>

                <div class="mb-4">
                    <label class="block text-secondary-700 text-xs font-bold uppercase tracking-wider mb-1.5" for="stock_qty">প্রাথমিক স্টক (Base Unit)</label>
                    <input type="number" step="0.001" id="stock_qty" name="stock_qty" class="w-full px-3 py-2 border border-secondary-300 rounded-xl text-sm font-bold text-center focus:ring-2 focus:ring-primary-500" value="0">
                </div>

                <!-- Sourcing & Availability -->
                <div class="mb-4 bg-secondary-50/70 p-3.5 rounded-xl border border-secondary-200">
                    <div class="mb-3">
                        <label class="block text-secondary-700 text-[11px] font-bold uppercase tracking-wider mb-1.5" for="availability_status">স্টোর প্রাপ্যতা (Availability)</label>
                        <select name="availability_status" id="availability_status" class="w-full px-2.5 py-1.5 border border-secondary-300 rounded-lg text-xs font-medium focus:ring-2 focus:ring-primary-500 bg-white">
                            <option value="pending" selected>⏳ Pending (Needs Review)</option>
                            <option value="in_stock">✅ In Stock (On-Demand / Local)</option>
                            <option value="out_of_stock">❌ Out of Stock</option>
                        </select>
                    </div>
                    <div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_verified" value="1" class="w-4 h-4 text-primary-600 rounded border-secondary-300 focus:ring-primary-500 transition-colors">
                            <span class="text-xs font-semibold text-secondary-900">Price Verified (মূল্য যাচাইকৃত)</span>
                        </label>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-secondary-700 text-xs font-bold uppercase tracking-wider mb-1.5" for="image">পণ্যের ছবি</label>
                    <input type="file" id="image" name="image" accept="image/*" class="w-full text-xs text-secondary-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                </div>
                
                <div class="mb-3">
                    <label class="block text-secondary-700 text-xs font-bold uppercase tracking-wider mb-1.5" for="description">সংক্ষিপ্ত বিবরণ (Description)</label>
                    <textarea id="description" name="description" rows="2" class="w-full px-3 py-2 border border-secondary-300 rounded-xl text-xs focus:ring-2 focus:ring-primary-500" placeholder="পণ্যের বিস্তারিত বা সংক্ষিপ্ত বিবরণ..."></textarea>
                </div>

                <div class="mb-5">
                    <label class="block text-secondary-700 text-xs font-bold uppercase tracking-wider mb-1.5" for="tags">ট্যাগ ও সার্চ কীওয়ার্ড (Tags)</label>
                    <input type="text" id="tags" name="tags" class="w-full px-3 py-2 border border-secondary-300 rounded-xl text-xs focus:ring-2 focus:ring-primary-500" placeholder="কমা দিয়ে লিখুন, যেমন: চাল, মিনিকেট, rice, grocery">
                    <p class="text-[10px] text-secondary-400 mt-1">গ্রাহক সার্চ বারে এই শব্দগুলো লিখলে পণ্যটি খুঁজে পাবে।</p>
                </div>

                <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 px-4 rounded-xl transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                    <ion-icon name="checkmark-outline" class="text-lg"></ion-icon>
                    পণ্য সেভ করুন
                </button>
            </form>
        </div>
    </div>

    <!-- List Section (Product Management Table) -->
    <div class="lg:col-span-2">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-secondary-900">পণ্য তালিকা ও ইনভেন্টরি</h2>
                <p class="text-secondary-500 text-xs mt-0.5">সকল পণ্যের মূল্য, ক্রয়-বিক্রয় একক ও বর্তমান মজুদ দেখুন।</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="<?= $base ?>/admin/products/image-finder" 
                   class="flex items-center gap-1.5 bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100 px-3.5 py-2 rounded-xl text-xs font-bold transition-all shadow-2xs">
                    <ion-icon name="sparkles" class="text-base text-amber-500"></ion-icon>
                    ইমেজ ফাইন্ডার
                </a>
                <a href="<?= $base ?>/admin/products/dashboard" 
                   class="flex items-center gap-1.5 bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100 px-3.5 py-2 rounded-xl text-xs font-bold transition-all shadow-2xs">
                    <ion-icon name="grid-outline" class="text-base text-indigo-600"></ion-icon>
                    প্রোডাক্ট ড্যাশবোর্ড
                </a>
                <a href="<?= $base ?>/admin/products/bulk-import" 
                   class="flex items-center gap-1.5 bg-white text-secondary-700 border border-secondary-300 hover:bg-secondary-50 px-3.5 py-2 rounded-xl text-xs font-bold transition-all shadow-2xs">
                    <ion-icon name="cloud-upload-outline" class="text-base"></ion-icon>
                    CSV ইমপোর্ট
                </a>
                <button type="button" onclick="openProductModal()" 
                        class="flex items-center gap-1.5 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-md hover:shadow-lg">
                    <ion-icon name="flash-outline" class="text-base"></ion-icon>
                    বাল্ক প্রোডাক্ট ক্রিয়েটর
                </button>
            </div>
        </div>

        <!-- Search & Filter Toolbar -->
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-100 p-4 mb-5">
            <form method="GET" action="<?= $base ?>/admin/products" id="productFilterForm" class="space-y-3">
                <!-- Top Row: Search Input + Submit + Reset -->
                <div class="flex flex-col sm:flex-row items-center gap-2.5">
                    <!-- Search Input Box -->
                    <div class="relative flex-1 w-full">
                        <ion-icon name="search-outline" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-secondary-400 text-base"></ion-icon>
                        <input type="text" 
                               name="search" 
                               id="productSearchInput" 
                               value="<?= htmlspecialchars($filters['search'] ?? '') ?>" 
                               placeholder="পণ্য, SKU বা ভেন্ডর দিয়ে খুঁজুন (Search by name, SKU, vendor)..." 
                               class="w-full pl-10 pr-4 py-2.5 border border-secondary-200 rounded-xl text-xs focus:ring-2 focus:ring-primary-500 bg-secondary-50/50 focus:bg-white transition-colors">
                    </div>

                    <!-- Search Button -->
                    <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-xs font-bold transition-all shadow-xs flex items-center justify-center gap-1.5 shrink-0 cursor-pointer">
                        <ion-icon name="search-outline" class="text-sm"></ion-icon>
                        <span>সার্চ করুন</span>
                    </button>

                    <?php if (!empty($filters['search']) || !empty($filters['category_id']) || !empty($filters['vendor_id']) || !empty($filters['stock_status']) || !empty($filters['availability_status']) || (!empty($filters['is_verified']) && $filters['is_verified'] !== '')): ?>
                        <a href="<?= $base ?>/admin/products" class="w-full sm:w-auto px-4 py-2.5 bg-secondary-100 hover:bg-secondary-200 text-secondary-600 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1 shrink-0 cursor-pointer" title="ফিল্টার রিসেট করুন">
                            <ion-icon name="close-circle-outline" class="text-sm"></ion-icon>
                            <span>রিসেট</span>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Bottom Row: 4 Filter Dropdowns in a Responsive Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 pt-2.5 border-t border-secondary-100/80">
                    <!-- Category Filter -->
                    <div>
                        <label class="block text-[10px] font-bold text-secondary-500 uppercase tracking-wider mb-1">ক্যাটাগরি</label>
                        <select name="category_id" id="categoryFilter" onchange="this.form.submit()" class="w-full px-2.5 py-2 border border-secondary-200 rounded-xl text-xs bg-secondary-50/60 focus:bg-white focus:ring-2 focus:ring-primary-500 font-medium truncate">
                            <option value="">সব ক্যাটাগরি</option>
                            <?php foreach ($categoriesWithPaths as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= (!empty($filters['category_id']) && $filters['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['path']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Vendor Filter -->
                    <div>
                        <label class="block text-[10px] font-bold text-secondary-500 uppercase tracking-wider mb-1">ভেন্ডর</label>
                        <select name="vendor_id" id="vendorFilter" onchange="this.form.submit()" class="w-full px-2.5 py-2 border border-secondary-200 rounded-xl text-xs bg-secondary-50/60 focus:bg-white focus:ring-2 focus:ring-primary-500 font-medium truncate">
                            <option value="">সব ভেন্ডর</option>
                            <option value="none" <?= (isset($filters['vendor_id']) && $filters['vendor_id'] === 'none') ? 'selected' : '' ?>>ভেন্ডর ছাড়া</option>
                            <?php foreach ($vendors as $v): ?>
                                <option value="<?= $v['id'] ?>" <?= (!empty($filters['vendor_id']) && $filters['vendor_id'] == $v['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($v['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Stock Status Filter -->
                    <div>
                        <label class="block text-[10px] font-bold text-secondary-500 uppercase tracking-wider mb-1">স্টক ফিল্টার</label>
                        <select name="stock_status" id="stockStatusFilter" onchange="this.form.submit()" class="w-full px-2.5 py-2 border border-secondary-200 rounded-xl text-xs bg-secondary-50/60 focus:bg-white focus:ring-2 focus:ring-primary-500 font-medium truncate">
                            <option value="">সকল স্টক</option>
                            <option value="in_stock" <?= (!empty($filters['stock_status']) && $filters['stock_status'] === 'in_stock') ? 'selected' : '' ?>>স্টক আছে (&ge;10)</option>
                            <option value="low_stock" <?= (!empty($filters['stock_status']) && $filters['stock_status'] === 'low_stock') ? 'selected' : '' ?>>কম স্টক (&lt;10)</option>
                            <option value="out_of_stock" <?= (!empty($filters['stock_status']) && $filters['stock_status'] === 'out_of_stock') ? 'selected' : '' ?>>স্টক শেষ (&le;0)</option>
                        </select>
                    </div>

                    <!-- Availability Status Filter -->
                    <div>
                        <label class="block text-[10px] font-bold text-secondary-500 uppercase tracking-wider mb-1">প্রাপ্যতা</label>
                        <select name="availability_status" id="availabilityStatusFilter" onchange="this.form.submit()" class="w-full px-2.5 py-2 border border-secondary-200 rounded-xl text-xs bg-secondary-50/60 focus:bg-white focus:ring-2 focus:ring-primary-500 font-medium truncate">
                            <option value="">সকল প্রাপ্যতা</option>
                            <option value="in_stock" <?= (!empty($filters['availability_status']) && $filters['availability_status'] === 'in_stock') ? 'selected' : '' ?>>🟢 ইন স্টক</option>
                            <option value="out_of_stock" <?= (!empty($filters['availability_status']) && $filters['availability_status'] === 'out_of_stock') ? 'selected' : '' ?>>🔴 স্টক শেষ</option>
                            <option value="pending" <?= (!empty($filters['availability_status']) && $filters['availability_status'] === 'pending') ? 'selected' : '' ?>>⏳ পেন্ডিং</option>
                        </select>
                    </div>
                </div>
            </form>

            <div class="flex items-center justify-between text-[11px] text-secondary-400 mt-2.5 pt-2 border-t border-secondary-100">
                <span id="productCounterText">মোট <strong class="text-secondary-700 font-bold"><?= number_format($totalProducts ?? count($products)) ?></strong> টি পণ্যের মধ্যে এই পৃষ্ঠায় <strong class="text-secondary-700 font-bold"><?= count($products) ?></strong> টি প্রদর্শিত (পৃষ্ঠা <?= $currentPage ?? 1 ?> / <?= $totalPages ?? 1 ?>)</span>
                <span class="text-[10px] text-secondary-400">প্রতি পৃষ্ঠায় ৫০টি পণ্য • চেকবক্স সিলেক্ট করে একসাথে স্ট্যাটাস আপডেট করুন</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-secondary-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-secondary-600 min-w-[860px]">
                    <thead class="bg-secondary-50/80 text-secondary-500 border-b border-secondary-200">
                        <tr>
                            <th class="w-12 px-4 py-3.5 text-center">
                                <input type="checkbox" id="selectAllProducts" class="w-4 h-4 rounded text-primary-600 border-secondary-300 focus:ring-primary-500 cursor-pointer" title="সব পণ্য সিলেক্ট / আনসিলেক্ট করুন">
                            </th>
                            <th class="px-4 py-3.5 font-bold text-xs uppercase tracking-wider">পণ্য</th>
                            <th class="px-4 py-3.5 font-bold text-xs uppercase tracking-wider">ক্যাটাগরি ও SKU</th>
                            <th class="px-3 py-3.5 font-bold text-xs uppercase tracking-wider text-center">স্ট্যাটাস</th>
                            <th class="px-4 py-3.5 font-bold text-xs uppercase tracking-wider text-right">ক্রয় / বিক্রয় মূল্য</th>
                            <th class="px-4 py-3.5 font-bold text-xs uppercase tracking-wider text-center">মজুদ (Stock)</th>
                            <th class="px-4 py-3.5 font-bold text-xs uppercase tracking-wider text-right">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody id="mainProductsTbody" class="divide-y divide-secondary-100">
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-secondary-400">
                                    কোনো পণ্য পাওয়া যায়নি। উপরের ফর্ম বা বাল্ক ক্রিয়েটর ব্যবহার করে পণ্য যোগ করুন।
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($products as $p): ?>
                                <tr class="hover:bg-secondary-50/60 transition-colors group main-product-row"
                                    id="product-row-<?= $p['id'] ?>"
                                    data-product-id="<?= $p['id'] ?>"
                                    data-name="<?= mb_strtolower(htmlspecialchars($p['name']), 'UTF-8') ?>"
                                    data-sku="<?= mb_strtolower(htmlspecialchars($p['sku'] ?? ''), 'UTF-8') ?>"
                                    data-vendor="<?= mb_strtolower(htmlspecialchars($p['vendor_name'] ?? ''), 'UTF-8') ?>"
                                    data-category="<?= mb_strtolower(htmlspecialchars($p['category_name'] ?? ''), 'UTF-8') ?>">
                                    <td class="w-12 px-4 py-3.5 text-center">
                                        <input type="checkbox" 
                                               class="product-bulk-cb w-4 h-4 rounded text-primary-600 border-secondary-300 focus:ring-primary-500 cursor-pointer" 
                                               value="<?= $p['id'] ?>" 
                                               data-product-id="<?= $p['id'] ?>"
                                               data-name="<?= htmlspecialchars($p['name']) ?>">
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-11 h-11 rounded-xl bg-white border border-secondary-200 flex-shrink-0 flex items-center justify-center overflow-hidden relative group cursor-pointer"
                                                 onclick="openProductImageFinderModal(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['name'])) ?>')"
                                                 title="ক্লিক করে ওয়েব/গুগল থেকে ছবি খুঁজুন">
                                                <?php 
                                                $prodImg = !empty($p['image_path']) ? \Models\Product::getImageUrl($p['image_path'], $base) : "{$base}/images/default-product.svg";
                                                ?>
                                                <img id="prod-thumb-<?= $p['id'] ?>" src="<?php echo $prodImg; ?>" alt="" class="w-full h-full object-contain mix-blend-multiply p-0.5 transition-transform group-hover:scale-105" onerror="this.src='<?= $base ?>/images/default-product.svg'">
                                                <div class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white transition-opacity">
                                                    <ion-icon name="sparkles" class="text-xs text-amber-400"></ion-icon>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="font-bold text-secondary-900 text-sm group-hover:text-primary-600 transition-colors">
                                                    <?php echo htmlspecialchars($p['name']); ?>
                                                </div>
                                                <div class="flex items-center gap-2 mt-0.5">
                                                    <span class="text-[11px] font-semibold text-secondary-400">
                                                        <?php echo htmlspecialchars($p['vendor_name'] ?? 'No Vendor'); ?>
                                                    </span>
                                                    <!-- Unit Badge -->
                                                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-secondary-100 text-secondary-600 border border-secondary-200">
                                                        <?php echo htmlspecialchars($p['base_unit'] ?? 'pcs'); ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <div class="text-xs font-medium text-secondary-800">
                                            <?php echo htmlspecialchars($p['category_name'] ?? 'Uncategorized'); ?>
                                        </div>
                                        <div class="text-[11px] font-mono text-secondary-400 mt-0.5">
                                            <?php echo htmlspecialchars($p['sku'] ?? 'N/A'); ?>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3.5 text-center product-status-col" data-product-id="<?= $p['id'] ?>">
                                        <div class="flex flex-col items-center gap-1">
                                            <?php
                                            $avail = $p['availability_status'] ?? 'pending';
                                            if ($avail === 'in_stock') {
                                                echo '<span class="status-badge-avail inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">🟢 ইন স্টক</span>';
                                            } elseif ($avail === 'out_of_stock') {
                                                echo '<span class="status-badge-avail inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-red-100 text-red-800 border border-red-200">🔴 স্টক শেষ</span>';
                                            } else {
                                                echo '<span class="status-badge-avail inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200">⏳ পেন্ডিং</span>';
                                            }

                                            if (!empty($p['is_verified'])) {
                                                echo '<span class="status-badge-verify text-[10px] text-emerald-600 font-bold flex items-center gap-0.5" title="যাচাইকৃত">✓ যাচাইকৃত</span>';
                                            } else {
                                                echo '<span class="status-badge-verify text-[10px] text-secondary-400 font-normal flex items-center gap-0.5" title="অযাচাইকৃত">⏳ অযাচাইকৃত</span>';
                                            }
                                            ?>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5 text-right">
                                        <div class="text-secondary-900 font-bold text-sm flex items-center justify-end gap-1.5 flex-wrap">
                                            <?php if (\Models\Product::hasDiscount($p)): ?>
                                                <span class="line-through text-xs text-secondary-400 font-medium">৳ <?php echo number_format($p['regular_price']); ?></span>
                                                <span class="text-emerald-700 font-black">৳ <?php echo number_format($p['sell_price']); ?></span>
                                                <span class="text-[10px] bg-red-100 text-red-700 font-black px-1.5 py-0.5 rounded border border-red-200">
                                                    <?php echo \Models\Product::getDiscountPercent($p); ?>% OFF
                                                </span>
                                            <?php else: ?>
                                                <span>৳ <?php echo number_format($p['sell_price']); ?></span>
                                            <?php endif; ?>
                                            <span class="text-[11px] text-secondary-400 font-normal">/ <?php echo htmlspecialchars($p['base_unit'] ?? 'pcs'); ?></span>
                                        </div>
                                        <div class="text-xs text-secondary-400 font-medium mt-0.5">
                                            Cost: ৳ <?php echo number_format($p['buy_price']); ?>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        <?php 
                                            $stockVal = floatval($p['stock_qty']);
                                            $stockColor = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                            if ($stockVal <= 0) $stockColor = 'bg-red-50 text-red-700 border-red-200';
                                            else if ($stockVal < 10) $stockColor = 'bg-amber-50 text-amber-700 border-amber-200';
                                        ?>
                                        <span class="inline-block px-3 py-1 rounded-lg text-xs font-bold border <?php echo $stockColor; ?>">
                                            <?php echo \Models\Product::formatStockDisplay($p); ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button type="button" 
                                                onclick="openProductImageFinderModal(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['name'])) ?>')" 
                                                class="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 transition-colors" 
                                                title="ওয়েব ও গুগল থেকে ছবি খুঁজুন (Auto Image Finder)">
                                                <ion-icon name="sparkles" class="text-base text-amber-500"></ion-icon>
                                            </button>
                                            <a href="<?= $base ?>/admin/products/edit?id=<?php echo $p['id']; ?>" 
                                                class="p-1.5 rounded-lg text-blue-600 hover:bg-blue-50 transition-colors" title="Edit Product">
                                                <ion-icon name="create-outline" class="text-lg"></ion-icon>
                                            </a>
                                            <form action="<?= $base ?>/admin/products/delete" method="POST" onsubmit="return confirm('পণ্যটি ডিলিট করতে চান?');" class="inline">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                                                <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                                <button type="submit" class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 transition-colors" title="Delete Product">
                                                    <ion-icon name="trash-outline" class="text-lg"></ion-icon>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar (50 items per page) -->
            <?php if (!empty($totalPages) && $totalPages > 1): ?>
                <?php
                    $queryParams = $_GET;
                    $makePageUrl = function($pNum) use ($queryParams, $base) {
                        $qp = $queryParams;
                        $qp['page'] = $pNum;
                        return $base . '/admin/products?' . http_build_query($qp);
                    };

                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($totalPages, $currentPage + 2);
                ?>
                <div class="px-5 py-3.5 border-t border-secondary-100 bg-secondary-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                    <div class="text-secondary-500 font-medium text-[11px] sm:text-xs">
                        মোট <strong class="text-secondary-800 font-bold"><?= number_format($totalProducts) ?></strong> টি পণ্যের মধ্যে 
                        <strong class="text-secondary-800 font-bold"><?= number_format(($currentPage - 1) * $perPage + 1) ?> - <?= number_format(min($totalProducts, $currentPage * $perPage)) ?></strong> টি দেখানো হচ্ছে 
                        (পৃষ্ঠা <strong class="text-secondary-800"><?= $currentPage ?></strong> / <strong class="text-secondary-800"><?= $totalPages ?></strong>)
                    </div>

                    <div class="flex items-center gap-1.5 flex-wrap">
                        <!-- First Page -->
                        <?php if ($currentPage > 1): ?>
                            <a href="<?= $makePageUrl(1) ?>" class="p-1.5 rounded-lg bg-white border border-secondary-200 text-secondary-600 hover:bg-secondary-100 hover:text-secondary-900 transition-colors" title="প্রথম পৃষ্ঠা">
                                <ion-icon name="play-back-outline" class="text-xs"></ion-icon>
                            </a>
                            <!-- Prev Page -->
                            <a href="<?= $makePageUrl($currentPage - 1) ?>" class="px-2.5 py-1.5 rounded-lg bg-white border border-secondary-200 text-secondary-700 font-semibold hover:bg-secondary-100 hover:text-secondary-900 transition-colors flex items-center gap-1">
                                <ion-icon name="chevron-back-outline"></ion-icon>
                                <span class="hidden sm:inline">আগের পৃষ্ঠা</span>
                            </a>
                        <?php else: ?>
                            <span class="px-2.5 py-1.5 rounded-lg bg-secondary-100 text-secondary-300 font-semibold cursor-not-allowed flex items-center gap-1">
                                <ion-icon name="chevron-back-outline"></ion-icon>
                                <span class="hidden sm:inline">আগের পৃষ্ঠা</span>
                            </span>
                        <?php endif; ?>

                        <!-- Numbered Pages -->
                        <?php if ($startPage > 1): ?>
                            <a href="<?= $makePageUrl(1) ?>" class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-white border border-secondary-200 text-secondary-700 font-bold hover:bg-secondary-100 flex items-center justify-center transition-colors">1</a>
                            <?php if ($startPage > 2): ?>
                                <span class="text-secondary-400 px-0.5">...</span>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                            <?php if ($i == $currentPage): ?>
                                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-primary-600 text-white font-black flex items-center justify-center shadow-xs">
                                    <?= $i ?>
                                </span>
                            <?php else: ?>
                                <a href="<?= $makePageUrl($i) ?>" class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-white border border-secondary-200 text-secondary-700 font-bold hover:bg-secondary-100 flex items-center justify-center transition-colors">
                                    <?= $i ?>
                                </a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($endPage < $totalPages): ?>
                            <?php if ($endPage < $totalPages - 1): ?>
                                <span class="text-secondary-400 px-0.5">...</span>
                            <?php endif; ?>
                            <a href="<?= $makePageUrl($totalPages) ?>" class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-white border border-secondary-200 text-secondary-700 font-bold hover:bg-secondary-100 flex items-center justify-center transition-colors"><?= $totalPages ?></a>
                        <?php endif; ?>

                        <!-- Next Page -->
                        <?php if ($currentPage < $totalPages): ?>
                            <a href="<?= $makePageUrl($currentPage + 1) ?>" class="px-2.5 py-1.5 rounded-lg bg-white border border-secondary-200 text-secondary-700 font-semibold hover:bg-secondary-100 hover:text-secondary-900 transition-colors flex items-center gap-1">
                                <span class="hidden sm:inline">পরের পৃষ্ঠা</span>
                                <ion-icon name="chevron-forward-outline"></ion-icon>
                            </a>
                            <!-- Last Page -->
                            <a href="<?= $makePageUrl($totalPages) ?>" class="p-1.5 rounded-lg bg-white border border-secondary-200 text-secondary-600 hover:bg-secondary-100 hover:text-secondary-900 transition-colors" title="শেষ পৃষ্ঠা">
                                <ion-icon name="play-forward-outline" class="text-xs"></ion-icon>
                            </a>
                        <?php else: ?>
                            <span class="px-2.5 py-1.5 rounded-lg bg-secondary-100 text-secondary-300 font-semibold cursor-not-allowed flex items-center gap-1">
                                <span class="hidden sm:inline">পরের পৃষ্ঠা</span>
                                <ion-icon name="chevron-forward-outline"></ion-icon>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- FLOATING BULK STATUS UPDATE BAR -->
        <div id="bulkActionBar" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 bg-secondary-900/95 backdrop-blur-md text-white rounded-2xl shadow-2xl px-5 py-3.5 border border-secondary-700/60 hidden transition-all duration-300 transform translate-y-8 opacity-0 max-w-4xl w-[94%] flex flex-col md:flex-row items-center justify-between gap-4">
            <!-- Counter & Select All -->
            <div class="flex items-center gap-3 w-full md:w-auto justify-between md:justify-start">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-primary-500/20 text-primary-400 flex items-center justify-center font-black text-sm border border-primary-500/30" id="bulkCounterDisplay">0</span>
                    <span class="text-xs font-semibold text-secondary-200"><span id="bulkCounterText">টি পণ্য সিলেক্টেড</span></span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="selectAllTableProducts(true)" class="text-[11px] text-primary-400 hover:text-white underline font-semibold transition-colors">
                        সবগুলো সিলেক্ট (<?= count($products) ?>)
                    </button>
                    <span class="text-secondary-600 text-xs">|</span>
                    <button type="button" onclick="selectAllTableProducts(false)" class="text-[11px] text-secondary-400 hover:text-secondary-200 transition-colors">
                        ক্লিয়ার
                    </button>
                </div>
            </div>

            <!-- Action Selectors & Submit -->
            <div class="flex flex-wrap items-center gap-2.5 w-full md:w-auto justify-end">
                <!-- Status Dropdown -->
                <div class="flex items-center gap-1.5">
                    <label class="text-[11px] text-secondary-300 font-bold uppercase hidden sm:inline">স্ট্যাটাস:</label>
                    <select id="bulkAvailabilitySelect" class="bg-secondary-800 text-white text-xs rounded-xl px-3 py-2 border border-secondary-600 focus:ring-2 focus:ring-primary-500 font-medium">
                        <option value="in_stock">🟢 ইন স্টক (In Stock)</option>
                        <option value="out_of_stock">🔴 স্টক শেষ (Out of Stock)</option>
                        <option value="pending">⏳ পেন্ডিং (Pending)</option>
                    </select>
                </div>

                <!-- Verification Dropdown -->
                <div class="flex items-center gap-1.5">
                    <select id="bulkVerificationSelect" class="bg-secondary-800 text-secondary-200 text-xs rounded-xl px-3 py-2 border border-secondary-600 focus:ring-2 focus:ring-primary-500 font-medium">
                        <option value="no_change">ভেরিফিকেশন: অপরিবর্তিত</option>
                        <option value="1">✓ যাচাইকৃত (Verified)</option>
                        <option value="0">⏳ অযাচাইকৃত (Unverified)</option>
                    </select>
                </div>

                <!-- Submit Button -->
                <button type="button" id="btnSubmitBulkStatus" onclick="submitBulkStatusUpdate()" class="bg-primary-600 hover:bg-primary-500 active:scale-95 text-white font-bold px-4 py-2 rounded-xl text-xs flex items-center gap-1.5 shadow-lg transition-all cursor-pointer">
                    <ion-icon name="checkmark-done-outline" class="text-base"></ion-icon>
                    <span id="bulkSubmitBtnText">স্ট্যাটাস আপডেট করুন</span>
                </button>
            </div>
        </div>
    </div>
<!-- UPGRADED BULK PRODUCT CREATOR MODAL WITH SEARCHABLE CATEGORIES & IMAGES -->
<div id="productModal" class="fixed inset-0 z-50 flex items-center justify-center bg-secondary-900/60 backdrop-blur-sm hidden">
    <!-- Modal Container -->
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-7xl mx-4 flex flex-col max-h-[92vh] overflow-hidden border border-secondary-100">
        
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-secondary-100 flex items-center justify-between bg-secondary-50/80">
            <div>
                <h3 class="text-lg font-bold text-secondary-900 flex items-center gap-2">
                    <ion-icon name="flash-outline" class="text-primary-600 text-xl"></ion-icon>
                    বাল্ক প্রোডাক্ট ক্রিয়েটর (Bulk Product Creator with Images & Searchable Categories)
                </h3>
                <p class="text-xs text-secondary-500 mt-0.5">সার্চ করে ক্যাটাগরি ও ছবি যুক্ত করে একসাথে দ্রুত একাধিক পণ্য যোগ করুন।</p>
            </div>
            <button type="button" onclick="document.getElementById('productModal').classList.add('hidden')" 
                    class="text-secondary-400 hover:text-secondary-600 hover:bg-secondary-200/60 p-2 rounded-full transition-all">
                <ion-icon name="close-outline" class="text-2xl"></ion-icon>
            </button>
        </div>

        <!-- Quick Template Apply Bar for Bulk Creation -->
        <div class="px-6 py-2.5 bg-emerald-50/60 border-b border-emerald-100 flex flex-wrap items-center justify-between gap-3 text-xs">
            <span class="font-bold text-emerald-900 flex items-center gap-1.5">
                <ion-icon name="sparkles-outline" class="text-emerald-600"></ion-icon>
                সব সারিতে একসাথে প্যাকেজিং টেমপ্লেট প্রয়োগ করুন:
            </span>
            <div class="flex flex-wrap gap-2">
                <button type="button" onclick="applyTemplateToAllRows('sack_kg_50')" class="px-2.5 py-1 rounded-md bg-white border border-emerald-200 hover:bg-emerald-100 font-semibold text-secondary-700 shadow-2xs">
                    🌾 বস্তা চাল/ডাল (৫০ কেজি)
                </button>
                <button type="button" onclick="applyTemplateToAllRows('drum_liter')" class="px-2.5 py-1 rounded-md bg-white border border-emerald-200 hover:bg-emerald-100 font-semibold text-secondary-700 shadow-2xs">
                    🛢️ ড্রাম তেল (১৯০ লিটার)
                </button>
                <button type="button" onclick="applyTemplateToAllRows('box_piece_24')" class="px-2.5 py-1 rounded-md bg-white border border-emerald-200 hover:bg-emerald-100 font-semibold text-secondary-700 shadow-2xs">
                    📦 বক্স সাবান (২৪ পিস)
                </button>
                <button type="button" onclick="applyTemplateToAllRows('piece_standard')" class="px-2.5 py-1 rounded-md bg-white border border-emerald-200 hover:bg-emerald-100 font-semibold text-secondary-700 shadow-2xs">
                    🏷️ সাধারণ পিস (১:১)
                </button>
            </div>
        </div>

        <!-- Form for Bulk Submission with Image Upload Support -->
        <form action="<?= $base ?>/admin/products/bulk-store-manual" method="POST" enctype="multipart/form-data" class="flex flex-col flex-1 overflow-hidden">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
            <!-- Modal Body Table -->
            <div class="flex-1 overflow-y-auto p-5">
                <div class="overflow-x-auto border border-secondary-200 rounded-2xl bg-white shadow-2xs">
                    <table class="w-full text-left text-xs text-secondary-600 min-w-[1200px]">
                        <thead class="bg-secondary-50 text-secondary-700 font-bold border-b border-secondary-200 sticky top-0 z-10 uppercase text-[11px]">
                            <tr>
                                <th class="px-3 py-3 w-10 text-center">#</th>
                                <th class="px-2 py-3 w-14 text-center">ছবি</th>
                                <th class="px-3 py-3 w-64">ক্যাটাগরি (সার্চেবল)</th>
                                <th class="px-3 py-3 min-w-[170px]">পণ্যের নাম</th>
                                <th class="px-3 py-3 w-52">প্যাকেজিং / ক্রয় একক</th>
                                <th class="px-3 py-3 w-32">সাপ্লায়ার</th>
                                <th class="px-3 py-3 w-24 text-right">ক্রয় মূল্য (৳)</th>
                                <th class="px-2 py-3 w-24 text-right">রেগুলার MRP</th>
                                <th class="px-3 py-3 w-28 text-right">বিক্রয় মূল্য (৳)</th>
                                <th class="px-3 py-3 w-24 text-center">মজুদ</th>
                                <th class="px-2 py-3 w-16 text-center" title="স্বয়ংক্রিয় সাইজ ভ্যারিয়েন্ট তৈরি">অটো প্যাক</th>
                                <th class="px-2 py-3 w-12 text-center">অ্যাকশন</th>
                            </tr>
                        </thead>
                        <tbody id="bulk-products-tbody" class="divide-y divide-secondary-100">
                            <!-- Rows injected dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-secondary-100 bg-secondary-50/80 flex items-center justify-between">
                <button type="button" onclick="addProductRow()" 
                        class="flex items-center gap-1.5 bg-white text-secondary-700 border border-secondary-300 hover:bg-secondary-50 px-4 py-2.5 rounded-xl font-bold transition-all shadow-2xs text-xs">
                    <ion-icon name="add-outline" class="text-base"></ion-icon>
                    নতুন সারি যোগ করুন (Add Row)
                </button>

                <div class="flex items-center gap-3">
                    <button type="button" onclick="document.getElementById('productModal').classList.add('hidden')" 
                            class="bg-white text-secondary-700 border border-secondary-300 hover:bg-secondary-50 px-5 py-2.5 rounded-xl font-semibold transition-all text-xs">
                        বাতিল (Cancel)
                    </button>
                    <button type="submit" 
                            class="flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-6 py-2.5 rounded-xl font-bold transition-all shadow-md text-xs">
                        <ion-icon name="checkmark-done-outline" class="text-base"></ion-icon>
                        সকল পণ্য সংরক্ষণ করুন (Save All)
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Product Row Template for Bulk Creator -->
<template id="product-row-template">
    <tr class="hover:bg-secondary-50/50 border-b border-secondary-100 product-row text-xs">
        <!-- Index Column -->
        <td class="px-3 py-2 text-secondary-500 font-bold row-number text-center">
            {index}
        </td>

        <!-- Image Upload Column -->
        <td class="px-2 py-2 text-center">
            <div class="relative w-10 h-10 mx-auto rounded-xl border-2 border-dashed border-secondary-300 hover:border-primary-500 bg-secondary-50 flex items-center justify-center cursor-pointer overflow-hidden group shadow-2xs" title="পণ্যের ছবি নির্বাচন করুন">
                <input type="file" name="product_images[{index}]" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer z-10" onchange="previewBulkRowImage(this)">
                <img class="hidden w-full h-full object-contain p-0.5 bulk-preview-img bg-white">
                <div class="flex flex-col items-center justify-center text-secondary-400 group-hover:text-primary-600 bulk-upload-placeholder">
                    <ion-icon name="camera-outline" class="text-base"></ion-icon>
                    <span class="text-[8px] font-semibold mt-0.5">ছবি</span>
                </div>
            </div>
        </td>
        
        <!-- Searchable Category Column -->
        <td class="px-3 py-2">
            <div class="relative row-category-picker">
                <input type="hidden" name="products[{index}][category_id]" class="row-category-id" required>
                
                <!-- Trigger Button -->
                <button type="button" onclick="toggleRowCategoryMenu(this)" 
                        class="w-full flex items-center justify-between px-2.5 py-1.5 border border-secondary-300 rounded-lg bg-white text-xs text-left focus:outline-none focus:ring-1 focus:ring-primary-500 shadow-2xs row-category-btn group hover:border-primary-400 transition-colors">
                    <div class="flex items-center gap-1.5 truncate">
                        <ion-icon name="folder-open-outline" class="text-secondary-400 group-hover:text-primary-600 text-sm flex-shrink-0"></ion-icon>
                        <span class="row-category-label text-secondary-400 font-medium truncate">ক্যাটাগরি সিলেক্ট...</span>
                    </div>
                    <ion-icon name="chevron-down-outline" class="text-secondary-400 text-xs flex-shrink-0 ml-1"></ion-icon>
                </button>

                <!-- Searchable Dropdown Popup -->
                <div class="absolute z-50 left-0 mt-1 w-72 max-h-72 bg-white border border-secondary-200 rounded-2xl shadow-2xl hidden row-category-dropdown flex-col overflow-hidden">
                    <!-- Search Input Box -->
                    <div class="p-2 border-b border-secondary-100 bg-secondary-50">
                        <div class="relative">
                            <input type="text" 
                                   oninput="filterRowCategoryList(this)" 
                                   placeholder="🔍 নাম লিখে খুঁজুন (যেমন: চাল, তেল)..." 
                                   class="w-full pl-8 pr-3 py-1.5 border border-secondary-300 rounded-xl text-xs bg-white focus:outline-none focus:ring-1 focus:ring-primary-500 font-medium category-search-input">
                            <ion-icon name="search-outline" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-secondary-400 text-sm"></ion-icon>
                        </div>
                    </div>

                    <!-- Category Items Container -->
                    <div class="overflow-y-auto max-h-52 p-1.5 space-y-0.5 row-category-items-container">
                        <!-- Populated dynamically via JS -->
                    </div>
                </div>
            </div>
        </td>
        
        <!-- Product Name Column -->
        <td class="px-3 py-2">
            <input type="text" name="products[{index}][name]" required 
                   placeholder="যেমন: মিনিকেট চাল ৫০ কেজি" 
                   class="w-full px-2.5 py-1.5 border border-secondary-300 rounded-lg text-xs font-medium focus:outline-none focus:ring-1 focus:ring-primary-500">
        </td>

        <!-- Packaging & Unit Template Selector Column -->
        <td class="px-3 py-2">
            <input type="hidden" name="products[{index}][unit_type]" class="row-unit-type" value="piece">
            <input type="hidden" name="products[{index}][base_unit]" class="row-base-unit" value="pcs">
            <input type="hidden" name="products[{index}][purchase_unit]" class="row-purchase-unit" value="">
            <input type="hidden" name="products[{index}][purchase_unit_qty]" class="row-purchase-qty" value="1">
            <input type="hidden" name="products[{index}][selling_unit]" class="row-selling-unit" value="pcs">

            <select onchange="onRowTemplateChange(this)" 
                    class="w-full px-2 py-1.5 border border-emerald-300 bg-emerald-50/50 rounded-lg text-xs font-semibold text-emerald-900 focus:outline-none focus:ring-1 focus:ring-emerald-500 row-template-select">
                <option value="piece_standard" data-type="piece" data-base="pcs" data-qty="1" data-punit="পিস">🏷️ সাধারণ পিস (১:১)</option>
                <?php foreach ($packagingUnits as $pu): ?>
                    <option value="<?php echo htmlspecialchars($pu['name']); ?>" 
                            data-type="bulk" 
                            data-base="<?php echo htmlspecialchars($pu['base_unit']); ?>" 
                            data-qty="<?php echo floatval($pu['default_qty']); ?>" 
                            data-punit="<?php echo htmlspecialchars($pu['name']); ?>">
                        📦 <?php echo htmlspecialchars($pu['name']); ?> (= <?php echo floatval($pu['default_qty']) . ' ' . $pu['base_unit']; ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </td>

        <!-- Vendor Column -->
        <td class="px-3 py-2">
            <select name="products[{index}][vendor_id]" 
                    class="w-full px-2 py-1.5 border border-secondary-300 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-primary-500 bg-white">
                <option value="">No Vendor</option>
                <?php foreach ($vendors as $v): ?>
                    <option value="<?php echo $v['id']; ?>"><?php echo htmlspecialchars($v['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </td>

        <!-- Buy Price Column -->
        <td class="px-2 py-2">
            <input type="number" step="0.01" name="products[{index}][buy_price]" 
                   placeholder="0.00" 
                   class="w-full px-2 py-1.5 border border-secondary-300 rounded-lg text-xs text-right font-medium focus:outline-none focus:ring-1 focus:ring-primary-500">
        </td>

        <!-- Regular Price / MRP Column -->
        <td class="px-2 py-2">
            <input type="number" step="0.01" name="products[{index}][regular_price]" 
                   placeholder="MRP" 
                   class="w-full px-2 py-1.5 border border-secondary-300 rounded-lg text-xs text-right font-medium focus:outline-none focus:ring-1 focus:ring-primary-500">
        </td>

        <!-- Sell Price Column -->
        <td class="px-2 py-2">
            <input type="number" step="0.01" name="products[{index}][sell_price]" required 
                   placeholder="0.00" 
                   class="w-full px-2 py-1.5 border border-emerald-300 rounded-lg text-xs text-right font-black text-emerald-700 focus:outline-none focus:ring-1 focus:ring-emerald-500">
        </td>

        <!-- Stock Column -->
        <td class="px-3 py-2">
            <div class="relative">
                <input type="number" step="0.001" name="products[{index}][stock_qty]" value="0" 
                       class="w-full pl-2 pr-7 py-1.5 border border-secondary-300 rounded-lg text-xs text-center font-bold focus:outline-none focus:ring-1 focus:ring-primary-500">
                <span class="absolute inset-y-0 right-0 flex items-center pr-1.5 text-[10px] text-secondary-400 font-bold row-unit-badge pointer-events-none">pcs</span>
            </div>
        </td>

        <!-- Auto Variants Checkbox Column -->
        <td class="px-2 py-2 text-center">
            <input type="checkbox" name="products[{index}][auto_variants]" value="1" checked 
                   class="rounded text-primary-600 focus:ring-primary-500" title="অটো সাইজ ভ্যারিয়েন্ট তৈরি করুন">
        </td>
        
        <!-- Action Column -->
        <td class="px-2 py-2 text-center">
            <button type="button" class="text-secondary-300 hover:text-red-500 transition-colors p-1 rounded-lg hover:bg-red-50 delete-row-btn" title="মুছুন">
                <ion-icon name="trash-outline" class="text-base"></ion-icon>
            </button>
        </td>
    </tr>
</template>

<!-- Dynamic Bulk Modal & Cascading Categories JavaScript -->
<script>
const ALL_CATEGORIES = <?php echo json_encode($categories); ?>;
const CATEGORIES_WITH_PATHS = <?php echo json_encode($categoriesWithPaths); ?>;
let rowCount = 0;

// Helper to escape string for JS
function escapeHtmlAttr(str) {
    return (str || '').replace(/'/g, "\\'").replace(/"/g, '&quot;');
}

// Preview image when selected in a bulk row
function previewBulkRowImage(input) {
    const container = input.closest('td');
    const previewImg = container.querySelector('.bulk-preview-img');
    const placeholder = container.querySelector('.bulk-upload-placeholder');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewImg.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        previewImg.src = '';
        previewImg.classList.add('hidden');
        if (placeholder) placeholder.classList.remove('hidden');
    }
}

// Populate Searchable Category Items for a row
function populateRowCategoryItems(row) {
    const container = row.querySelector('.row-category-items-container');
    if (!container) return;
    
    let html = '';
    CATEGORIES_WITH_PATHS.forEach(c => {
        const safeName = escapeHtmlAttr(c.name);
        const safePath = escapeHtmlAttr(c.path);
        html += `
            <button type="button" onclick="selectRowCategory(this, ${c.id}, '${safeName}', '${safePath}')" 
                    data-search="${(c.name + ' ' + c.path).toLowerCase()}"
                    class="w-full text-left px-2.5 py-1.5 rounded-lg hover:bg-primary-50 text-xs flex flex-col transition-colors group category-item-btn">
                <span class="font-bold text-secondary-800 group-hover:text-primary-700">${c.name}</span>
                <span class="text-[10px] text-secondary-400 group-hover:text-primary-600 font-medium">${c.path}</span>
            </button>
        `;
    });
    container.innerHTML = html;
}

// Toggle Dropdown Menu
function toggleRowCategoryMenu(btn) {
    const picker = btn.closest('.row-category-picker');
    const dropdown = picker.querySelector('.row-category-dropdown');
    const isOpen = !dropdown.classList.contains('hidden');

    // Close all other open dropdowns first
    document.querySelectorAll('.row-category-dropdown').forEach(d => {
        d.classList.add('hidden');
        d.classList.remove('flex');
    });

    if (!isOpen) {
        dropdown.classList.remove('hidden');
        dropdown.classList.add('flex');
        const searchInput = dropdown.querySelector('.category-search-input');
        if (searchInput) {
            searchInput.value = '';
            filterRowCategoryList(searchInput);
            setTimeout(() => searchInput.focus(), 50);
        }
    }
}

// Filter Categories in Dropdown
function filterRowCategoryList(input) {
    const query = input.value.trim().toLowerCase();
    const dropdown = input.closest('.row-category-dropdown');
    const items = dropdown.querySelectorAll('.category-item-btn');
    let matchCount = 0;

    items.forEach(item => {
        const searchStr = item.dataset.search || '';
        if (!query || searchStr.includes(query)) {
            item.style.display = 'flex';
            matchCount++;
        } else {
            item.style.display = 'none';
        }
    });

    let emptyNotice = dropdown.querySelector('.category-empty-notice');
    if (matchCount === 0) {
        if (!emptyNotice) {
            emptyNotice = document.createElement('div');
            emptyNotice.className = 'category-empty-notice p-3 text-center text-xs text-secondary-400 font-medium';
            emptyNotice.textContent = 'কোনো ক্যাটাগরি পাওয়া যায়নি';
            dropdown.querySelector('.row-category-items-container').appendChild(emptyNotice);
        }
        emptyNotice.style.display = 'block';
    } else if (emptyNotice) {
        emptyNotice.style.display = 'none';
    }
}

// Select Category for Row
function selectRowCategory(itemBtn, id, name, path) {
    const picker = itemBtn.closest('.row-category-picker');
    const hiddenInput = picker.querySelector('.row-category-id');
    const label = picker.querySelector('.row-category-label');
    const btn = picker.querySelector('.row-category-btn');
    const dropdown = picker.querySelector('.row-category-dropdown');

    hiddenInput.value = id;
    label.textContent = path || name;
    label.className = 'row-category-label text-emerald-800 font-bold truncate';
    btn.classList.add('border-emerald-400', 'bg-emerald-50/40');
    
    dropdown.classList.add('hidden');
    dropdown.classList.remove('flex');
}

// Close category dropdown on click outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.row-category-picker')) {
        document.querySelectorAll('.row-category-dropdown').forEach(d => {
            d.classList.add('hidden');
            d.classList.remove('flex');
        });
    }
});

// --- CASCADING CATEGORY SELECTOR CLASS WITH LIVE SEARCH (for Single Add) ---
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
                           class="category-search-input w-full pl-9 pr-9 py-2 bg-white border border-secondary-300 rounded-xl text-xs font-semibold placeholder-secondary-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all shadow-2xs" 
                           placeholder="🔍 ক্যাটাগরি বা সাব-ক্যাটাগরির নাম লিখে খুঁজুন (যেমন: নুডলস, তেল, চাল, মসলা)..."
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
            <div class="category-levels-container space-y-2"></div>

            <!-- Breadcrumb / Status Banner -->
            <div class="category-breadcrumb mt-2 text-xs px-3 py-1.5 rounded-xl flex items-center gap-1.5" style="display: none;"></div>
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

            let labelText = levelIndex === 1 ? '১. মূল ক্যাটাগরি:' : (levelIndex === 2 ? '২. সাব-ক্যাটাগরি:' : `${levelIndex}. সাব-সাব ক্যাটাগরি:`);
            let placeholder = levelIndex === 1 ? '-- মূল ক্যাটাগরি সিলেক্ট করুন --' : '-- সাব-ক্যাটাগরি সিলেক্ট করুন --';

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
                this.breadcrumbEl.innerHTML = `<ion-icon name="arrow-forward-circle-outline" class="text-base text-amber-600 flex-shrink-0"></ion-icon> <div><span class="text-amber-800 font-bold">সাব-ক্যাটাগরি সিলেক্ট করুন:</span> <span class="text-secondary-800">${pathNames}</span></div>`;
                this.breadcrumbEl.className = 'category-breadcrumb mt-2 text-xs font-medium text-amber-800 bg-amber-50 border border-amber-200 px-3 py-1.5 rounded-xl flex items-center gap-1.5';
            } else {
                this.breadcrumbEl.innerHTML = `<ion-icon name="checkmark-circle" class="text-base text-emerald-600 flex-shrink-0"></ion-icon> <div><span class="font-bold text-emerald-900">নির্বাচিত:</span> <span class="text-secondary-900 font-semibold">${pathNames}</span></div>`;
                this.breadcrumbEl.className = 'category-breadcrumb mt-2 text-xs font-semibold text-emerald-800 bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-xl flex items-center gap-1.5';
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

// Single Add: Purchase Unit Change
function onSinglePurchaseUnitChange(selectEl) {
    const val = selectEl.value;
    const customWrapper = document.getElementById('single_custom_purchase_unit_wrapper');
    const customInput = document.getElementById('single_custom_purchase_unit_input');
    const hiddenInput = document.getElementById('single_purchase_unit');
    const qtyInput = document.getElementById('single_purchase_unit_qty');
    const baseSelect = document.getElementById('single_base_unit');

    if (val === '__custom__') {
        customWrapper.classList.remove('hidden');
        hiddenInput.value = customInput.value;
    } else {
        customWrapper.classList.add('hidden');
        hiddenInput.value = val;

        const opt = selectEl.options[selectEl.selectedIndex];
        if (opt) {
            const qty = opt.dataset.qty;
            const base = opt.dataset.base;
            if (qty) qtyInput.value = qty;
            if (base && baseSelect) baseSelect.value = base;
        }
    }
}

// Bulk Creator: Row Template Change
function onRowTemplateChange(selectEl) {
    const row = selectEl.closest('tr');
    const opt = selectEl.options[selectEl.selectedIndex];
    if (!opt) return;

    const baseUnit = opt.dataset.base || 'pcs';
    const purchaseUnit = opt.dataset.punit || '';
    const purchaseQty = opt.dataset.qty || 1;
    const unitType = opt.dataset.type || 'bulk';

    row.querySelector('.row-unit-type').value = unitType;
    row.querySelector('.row-base-unit').value = baseUnit;
    row.querySelector('.row-purchase-unit').value = purchaseUnit;
    row.querySelector('.row-purchase-qty').value = purchaseQty;
    row.querySelector('.row-selling-unit').value = baseUnit;
    row.querySelector('.row-unit-badge').textContent = baseUnit;
}

function applyTemplateToAllRows(needle) {
    document.querySelectorAll('.row-template-select').forEach(select => {
        for (let i = 0; i < select.options.length; i++) {
            if (select.options[i].value.includes(needle) || select.options[i].dataset.punit?.includes(needle)) {
                select.selectedIndex = i;
                onRowTemplateChange(select);
                break;
            }
        }
    });
}

function addProductRow() {
    const template = document.getElementById('product-row-template').innerHTML;
    const tbody = document.getElementById('bulk-products-tbody');
    
    const rowHtml = template.replace(/{index}/g, rowCount);
    tbody.insertAdjacentHTML('beforeend', rowHtml);
    
    const newRow = tbody.lastElementChild;
    populateRowCategoryItems(newRow);
    
    rowCount++;
    reIndexRows();
}

function reIndexRows() {
    const rows = document.querySelectorAll('.product-row');
    rows.forEach((row, i) => {
        row.querySelector('.row-number').innerText = i + 1;
    });
}

function openProductModal() {
    const modal = document.getElementById('productModal');
    modal.classList.remove('hidden');
    
    const tbody = document.getElementById('bulk-products-tbody');
    if (tbody.children.length === 0) {
        addProductRow();
        addProductRow();
        addProductRow();
    }
}

function calculateSingleDiscount() {
    const reg = parseFloat(document.getElementById('single_regular_price').value) || 0;
    const sell = parseFloat(document.getElementById('single_sell_price').value) || 0;
    const badge = document.getElementById('single_discount_badge');
    const text = document.getElementById('single_discount_text');

    if (reg > sell && sell > 0) {
        const diff = reg - sell;
        const pct = Math.round((diff / reg) * 100);
        text.textContent = `ছাড় প্রযোজ্য: ৳ ${diff.toFixed(2)} (${pct}% OFF)`;
        badge.classList.remove('hidden');
    } else {
        badge.classList.add('hidden');
    }
}

// Event delegation
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Cascading Category Selector for Single Add
    new CascadingCategorySelector(
        'single_category_cascading_container',
        'single_category_id',
        ALL_CATEGORIES,
        null
    );

    const modal = document.getElementById('productModal');

    modal.addEventListener('click', function(e) {
        // Delete row
        const deleteBtn = e.target.closest('.delete-row-btn');
        if (deleteBtn) {
            e.preventDefault();
            const row = deleteBtn.closest('.product-row');
            row.remove();
            reIndexRows();
            return;
        }
    });

    // Real-time Instant Search & Table Filter
    const searchInput = document.getElementById('productSearchInput');
    const tbody = document.getElementById('mainProductsTbody');
    const counterText = document.getElementById('productCounterText');

    if (searchInput && tbody) {
        searchInput.addEventListener('input', function() {
            const query = this.value.trim().toLowerCase();
            const words = query ? query.split(/\s+/).filter(w => w.length > 0) : [];
            const rows = tbody.querySelectorAll('.main-product-row');
            let visibleCount = 0;

            rows.forEach(row => {
                const name = (row.dataset.name || '').toLowerCase();
                const sku = (row.dataset.sku || '').toLowerCase();
                const vendor = (row.dataset.vendor || '').toLowerCase();
                const category = (row.dataset.category || '').toLowerCase();
                const combined = `${name} ${sku} ${vendor} ${category}`;

                const match = words.length === 0 || words.every(w => combined.includes(w));

                if (match) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Handle empty state row
            let noMatchRow = document.getElementById('clientFilterNoMatchRow');
            if (visibleCount === 0 && rows.length > 0) {
                if (!noMatchRow) {
                    noMatchRow = document.createElement('tr');
                    noMatchRow.id = 'clientFilterNoMatchRow';
                    noMatchRow.innerHTML = '<td colspan="7" class="px-6 py-10 text-center text-secondary-400 text-sm">কোনো পণ্য খুঁজে পাওয়া যায়নি। এন্টার চাপুন বা ফিল্টার বাটন দিয়ে খুঁজুন।</td>';
                    tbody.appendChild(noMatchRow);
                } else {
                    noMatchRow.style.display = '';
                }
            } else if (noMatchRow) {
                noMatchRow.style.display = 'none';
            }

            if (counterText) {
                counterText.innerHTML = `মোট পণ্য প্রদর্শিত: <strong class="text-secondary-700 font-bold">${visibleCount}</strong> টি`;
            }
        });
    }

    // --- BULK PRODUCT SELECTION & STATUS UPDATE LOGIC ---
    const selectAllCb = document.getElementById('selectAllProducts');
    const bulkBar = document.getElementById('bulkActionBar');
    const bulkCountDisplay = document.getElementById('bulkCounterDisplay');
    const bulkCountText = document.getElementById('bulkCounterText');

    function getSelectedProductCheckboxes() {
        return Array.from(document.querySelectorAll('.product-bulk-cb:checked'));
    }

    function getAllProductCheckboxes() {
        return Array.from(document.querySelectorAll('.product-bulk-cb'));
    }

    window.updateBulkBarState = function() {
        const selected = getSelectedProductCheckboxes();
        const totalSelected = selected.length;

        if (bulkCountDisplay) bulkCountDisplay.textContent = totalSelected;
        if (bulkCountText) bulkCountText.textContent = `${totalSelected} টি পণ্য সিলেক্টেড`;

        if (totalSelected > 0) {
            bulkBar.classList.remove('hidden');
            setTimeout(() => {
                bulkBar.classList.remove('translate-y-8', 'opacity-0');
            }, 10);
        } else {
            bulkBar.classList.add('translate-y-8', 'opacity-0');
            setTimeout(() => {
                if (getSelectedProductCheckboxes().length === 0) {
                    bulkBar.classList.add('hidden');
                }
            }, 300);
        }

        // Update master checkbox state
        const all = getAllProductCheckboxes();
        if (selectAllCb) {
            if (totalSelected === 0) {
                selectAllCb.checked = false;
                selectAllCb.indeterminate = false;
            } else if (totalSelected === all.length) {
                selectAllCb.checked = true;
                selectAllCb.indeterminate = false;
            } else {
                selectAllCb.checked = false;
                selectAllCb.indeterminate = true;
            }
        }
    };

    window.selectAllTableProducts = function(check) {
        const cbs = getAllProductCheckboxes();
        cbs.forEach(cb => {
            const row = cb.closest('tr');
            // Only select visible rows if filtered
            if (!check || !row || row.style.display !== 'none') {
                cb.checked = check;
                if (row) {
                    if (check) row.classList.add('bg-primary-50/50');
                    else row.classList.remove('bg-primary-50/50');
                }
            }
        });
        updateBulkBarState();
    };

    if (selectAllCb) {
        selectAllCb.addEventListener('change', function() {
            selectAllTableProducts(this.checked);
        });
    }

    if (tbody) {
        tbody.addEventListener('change', function(e) {
            if (e.target && e.target.classList.contains('product-bulk-cb')) {
                const row = e.target.closest('tr');
                if (row) {
                    if (e.target.checked) row.classList.add('bg-primary-50/50');
                    else row.classList.remove('bg-primary-50/50');
                }
                updateBulkBarState();
            }
        });
    }

    // Ajax Bulk Status Submit
    window.submitBulkStatusUpdate = function() {
        const selectedCbs = getSelectedProductCheckboxes();
        if (selectedCbs.length === 0) {
            alert('অনুগ্রহ করে অন্তত একটি পণ্য সিলেক্ট করুন!');
            return;
        }

        const productIds = selectedCbs.map(cb => cb.value);
        const availStatus = document.getElementById('bulkAvailabilitySelect').value;
        const verifyStatus = document.getElementById('bulkVerificationSelect').value;
        const csrfToken = document.querySelector('input[name="csrf_token"]') ? document.querySelector('input[name="csrf_token"]').value : '';
        const btn = document.getElementById('btnSubmitBulkStatus');
        const btnText = document.getElementById('bulkSubmitBtnText');

        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed');
        const originalText = btnText.innerHTML;
        btnText.innerHTML = '<span class="inline-block animate-spin mr-1">↻</span> আপডেট হচ্ছে...';

        const formData = new FormData();
        formData.append('csrf_token', csrfToken);
        formData.append('is_ajax', '1');
        formData.append('availability_status', availStatus);
        formData.append('is_verified', verifyStatus);
        productIds.forEach(id => formData.append('product_ids[]', id));

        fetch('<?= $base ?>/admin/products/bulk-status-update', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.classList.remove('opacity-75', 'cursor-not-allowed');
            btnText.innerHTML = originalText;

            if (data.success) {
                // Dynamically update status badges in the table rows
                productIds.forEach(id => {
                    const statusCell = document.querySelector(`.product-status-col[data-product-id="${id}"]`);
                    if (statusCell) {
                        let availBadgeHtml = '';
                        if (availStatus === 'in_stock') {
                            availBadgeHtml = '<span class="status-badge-avail inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">🟢 ইন স্টক</span>';
                        } else if (availStatus === 'out_of_stock') {
                            availBadgeHtml = '<span class="status-badge-avail inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-red-100 text-red-800 border border-red-200">🔴 স্টক শেষ</span>';
                        } else {
                            availBadgeHtml = '<span class="status-badge-avail inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200">⏳ পেন্ডিং</span>';
                        }

                        let verifyBadgeHtml = '';
                        if (verifyStatus === '1') {
                            verifyBadgeHtml = '<span class="status-badge-verify text-[10px] text-emerald-600 font-bold flex items-center gap-0.5" title="যাচাইকৃত">✓ যাচাইকৃত</span>';
                        } else if (verifyStatus === '0') {
                            verifyBadgeHtml = '<span class="status-badge-verify text-[10px] text-secondary-400 font-normal flex items-center gap-0.5" title="অযাচাইকৃত">⏳ অযাচাইকৃত</span>';
                        } else {
                            const existingVerify = statusCell.querySelector('.status-badge-verify');
                            if (existingVerify) verifyBadgeHtml = existingVerify.outerHTML;
                        }

                        statusCell.innerHTML = `
                            <div class="flex flex-col items-center gap-1">
                                ${availBadgeHtml}
                                ${verifyBadgeHtml}
                            </div>
                        `;
                    }
                });

                // Toast notification
                showAdminToast(data.message || 'স্ট্যাটাস সফলভাবে আপডেট করা হয়েছে!', 'success');

                // Clear selection
                selectAllTableProducts(false);
            } else {
                showAdminToast(data.message || 'আপডেট করতে সমস্যা হয়েছে!', 'error');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.classList.remove('opacity-75', 'cursor-not-allowed');
            btnText.innerHTML = originalText;
            console.error('Bulk update error:', err);
            showAdminToast('সার্ভার এরর! পুনরায় চেষ্টা করুন।', 'error');
        });
    };

    function showAdminToast(msg, type = 'success') {
        let toast = document.getElementById('adminLiveToast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'adminLiveToast';
            toast.className = 'fixed top-6 right-6 z-[9999] max-w-md px-5 py-3.5 rounded-2xl shadow-2xl flex items-center gap-3 transition-all duration-300 transform translate-x-full opacity-0';
            document.body.appendChild(toast);
        }

        if (type === 'success') {
            toast.className = 'fixed top-6 right-6 z-[9999] max-w-md px-5 py-3.5 rounded-2xl shadow-2xl flex items-center gap-3 transition-all duration-300 transform bg-emerald-600 text-white font-bold text-xs border border-emerald-500 shadow-emerald-900/20';
            toast.innerHTML = `<ion-icon name="checkmark-circle" class="text-xl flex-shrink-0"></ion-icon><span>${msg}</span>`;
        } else {
            toast.className = 'fixed top-6 right-6 z-[9999] max-w-md px-5 py-3.5 rounded-2xl shadow-2xl flex items-center gap-3 transition-all duration-300 transform bg-red-600 text-white font-bold text-xs border border-red-500 shadow-red-900/20';
            toast.innerHTML = `<ion-icon name="alert-circle" class="text-xl flex-shrink-0"></ion-icon><span>${msg}</span>`;
        }

        // Animate in
        requestAnimationFrame(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
        });

        setTimeout(() => {
            toast.classList.add('translate-x-full', 'opacity-0');
        }, 4000);
    }
});
</script>

<?php require __DIR__ . '/image_finder_modal.php'; ?>
