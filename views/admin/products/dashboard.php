<?php
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
?>
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl shadow-sm border border-secondary-100">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-primary-600 uppercase tracking-wider mb-1">
                <ion-icon name="grid-outline" class="text-base"></ion-icon>
                <span>Inventory & Catalog Command Center</span>
            </div>
            <h1 class="text-2xl font-black text-secondary-900">Product Dashboard & Analytics</h1>
            <p class="text-secondary-500 text-xs mt-1">Real-time overview of inventory valuation, stock health, price verification, and procurement workflows.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="<?= $base ?>/admin/products/image-finder" 
               class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm hover:shadow-md">
                <ion-icon name="sparkles" class="text-base text-amber-300"></ion-icon>
                <span>Auto Image Finder</span>
            </a>
            <a href="<?= $base ?>/admin/products/bulk-import" 
               class="flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm hover:shadow-md">
                <ion-icon name="cloud-upload-outline" class="text-base"></ion-icon>
                <span>Bulk CSV Import</span>
            </a>
            <a href="<?= $base ?>/admin/products" 
               class="flex items-center gap-2 bg-white text-secondary-700 border border-secondary-300 hover:bg-secondary-50 px-4 py-2.5 rounded-xl text-xs font-bold transition-all shadow-2xs">
                <ion-icon name="list-outline" class="text-base"></ion-icon>
                <span>Manage Products</span>
            </a>
            <a href="<?= $base ?>/admin/products/procurement" 
               class="flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white px-4 py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm">
                <ion-icon name="cart-outline" class="text-base"></ion-icon>
                <span>Procurement List</span>
            </a>
        </div>
    </div>


    <!-- KPI Summary Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Products -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-secondary-100 flex items-center justify-between hover:border-primary-300 transition-colors group">
            <div>
                <p class="text-xs font-bold text-secondary-400 uppercase tracking-wider">Total Products</p>
                <h3 class="text-2xl font-black text-secondary-900 mt-1"><?= number_format($totalProducts) ?></h3>
                <span class="text-[11px] text-secondary-500 mt-1 inline-block">Active items in system</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                <ion-icon name="cube-outline"></ion-icon>
            </div>
        </div>

        <!-- In Stock Products -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-secondary-100 flex items-center justify-between hover:border-emerald-300 transition-colors group">
            <div>
                <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider">In Stock</p>
                <h3 class="text-2xl font-black text-emerald-700 mt-1"><?= number_format($inStock) ?></h3>
                <span class="text-[11px] text-emerald-600 font-medium mt-1 inline-block">Maturity: &ge; 10 units</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                <ion-icon name="checkmark-circle-outline"></ion-icon>
            </div>
        </div>

        <!-- Low Stock Items -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-secondary-100 flex items-center justify-between hover:border-amber-300 transition-colors group">
            <div>
                <p class="text-xs font-bold text-amber-600 uppercase tracking-wider">Low Stock Warning</p>
                <h3 class="text-2xl font-black text-amber-700 mt-1"><?= number_format($lowStock) ?></h3>
                <a href="/sodai-dorkar/public/admin/products?stock_status=low_stock" class="text-[11px] text-amber-700 font-semibold hover:underline mt-1 inline-block">
                    Needs re-ordering &rarr;
                </a>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                <ion-icon name="warning-outline"></ion-icon>
            </div>
        </div>

        <!-- Out of Stock Items -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-secondary-100 flex items-center justify-between hover:border-red-300 transition-colors group">
            <div>
                <p class="text-xs font-bold text-red-600 uppercase tracking-wider">Out of Stock</p>
                <h3 class="text-2xl font-black text-red-700 mt-1"><?= number_format($outOfStock) ?></h3>
                <a href="/sodai-dorkar/public/admin/products?stock_status=out_of_stock" class="text-[11px] text-red-600 font-semibold hover:underline mt-1 inline-block">
                    Stocked out items &rarr;
                </a>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                <ion-icon name="alert-circle-outline"></ion-icon>
            </div>
        </div>
    </div>

    <!-- Valuation & Sourcing KPIs Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Stock Valuation (Cost) -->
        <div class="bg-gradient-to-br from-indigo-50/50 to-white p-5 rounded-2xl shadow-sm border border-indigo-100">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold text-indigo-700 uppercase tracking-wider">Inventory Cost Value</p>
                <ion-icon name="wallet-outline" class="text-xl text-indigo-600"></ion-icon>
            </div>
            <h3 class="text-2xl font-black text-indigo-950 mt-2">৳ <?= number_format($costValuation, 2) ?></h3>
            <span class="text-[11px] text-indigo-600 mt-1 inline-block">Total purchasing value of current stock</span>
        </div>

        <!-- Stock Valuation (Retail) -->
        <div class="bg-gradient-to-br from-purple-50/50 to-white p-5 rounded-2xl shadow-sm border border-purple-100">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold text-purple-700 uppercase tracking-wider">Inventory Retail Value</p>
                <ion-icon name="pricetags-outline" class="text-xl text-purple-600"></ion-icon>
            </div>
            <h3 class="text-2xl font-black text-purple-950 mt-2">৳ <?= number_format($retailValuation, 2) ?></h3>
            <span class="text-[11px] text-purple-600 mt-1 inline-block">Projected retail sales revenue</span>
        </div>

        <!-- Expected Gross Margin -->
        <div class="bg-gradient-to-br from-teal-50/50 to-white p-5 rounded-2xl shadow-sm border border-teal-100">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold text-teal-700 uppercase tracking-wider">Potential Gross Profit</p>
                <ion-icon name="trending-up-outline" class="text-xl text-teal-600"></ion-icon>
            </div>
            <h3 class="text-2xl font-black text-teal-950 mt-2">৳ <?= number_format($expectedProfit, 2) ?></h3>
            <span class="text-[11px] text-teal-600 mt-1 inline-block">Estimated profit at full clearance</span>
        </div>

        <!-- Pending Workflow Action Required -->
        <div class="bg-gradient-to-br from-orange-50/50 to-white p-5 rounded-2xl shadow-sm border border-orange-100">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold text-orange-700 uppercase tracking-wider">Pending Price Checks</p>
                <ion-icon name="shield-checkmark-outline" class="text-xl text-orange-600"></ion-icon>
            </div>
            <h3 class="text-2xl font-black text-orange-950 mt-2"><?= number_format($unverifiedCount) ?></h3>
            <a href="/sodai-dorkar/public/admin/products/verification" class="text-[11px] text-orange-700 font-semibold hover:underline mt-1 inline-block">
                Verify prices now &rarr;
            </a>
        </div>
    </div>

    <!-- Stock Health Distribution Bar -->
    <?php 
    $totalCountCalc = max($totalProducts, 1);
    $inStockPct = round(($inStock / $totalCountCalc) * 100, 1);
    $lowStockPct = round(($lowStock / $totalCountCalc) * 100, 1);
    $outStockPct = round(($outOfStock / $totalCountCalc) * 100, 1);
    ?>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-secondary-100">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-2 mb-3">
            <div>
                <h4 class="text-sm font-bold text-secondary-900">Overall Inventory Health Distribution</h4>
                <p class="text-xs text-secondary-500">Live proportion of available inventory versus low and stocked-out products.</p>
            </div>
            <div class="flex items-center gap-4 text-xs font-medium">
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
                    <span class="text-secondary-700">Healthy (<?= $inStockPct ?>%)</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-amber-500 inline-block"></span>
                    <span class="text-secondary-700">Low Stock (<?= $lowStockPct ?>%)</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-red-500 inline-block"></span>
                    <span class="text-secondary-700">Out of Stock (<?= $outStockPct ?>%)</span>
                </div>
            </div>
        </div>
        <div class="w-full bg-secondary-100 h-3.5 rounded-full overflow-hidden flex shadow-inner">
            <div class="bg-emerald-500 h-full transition-all duration-500" style="width: <?= $inStockPct ?>%" title="Healthy: <?= $inStock ?> items"></div>
            <div class="bg-amber-500 h-full transition-all duration-500" style="width: <?= $lowStockPct ?>%" title="Low Stock: <?= $lowStock ?> items"></div>
            <div class="bg-red-500 h-full transition-all duration-500" style="width: <?= $outStockPct ?>%" title="Out of Stock: <?= $outOfStock ?> items"></div>
        </div>
    </div>

    <!-- Quick Operations Launchpad -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <a href="<?= $base ?>/admin/products/image-finder" 
           class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200 hover:border-emerald-400 hover:shadow-md transition-all flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-xl group-hover:scale-110 transition-transform shadow-sm">
                <ion-icon name="sparkles"></ion-icon>
            </div>
            <div>
                <h5 class="text-xs font-bold text-emerald-950 group-hover:text-emerald-700 transition-colors flex items-center gap-1">
                    <span>Auto Image Finder</span>
                    <span class="px-1.5 py-0.2 bg-amber-400 text-slate-900 text-[8px] font-black rounded">NEW</span>
                </h5>
                <p class="text-[11px] text-emerald-800/80">1-ক্লিকে Shwapno থেকে ছবি সেভ</p>
            </div>
        </a>

        <a href="<?= $base ?>/admin/products/bulk-import" 
           class="p-4 rounded-2xl bg-white border border-secondary-100 hover:border-primary-400 hover:shadow-md transition-all flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                <ion-icon name="cloud-upload-outline"></ion-icon>
            </div>
            <div>
                <h5 class="text-xs font-bold text-secondary-900 group-hover:text-primary-600 transition-colors">Bulk CSV Import</h5>
                <p class="text-[11px] text-secondary-500">Upload 300+ items with progress bar</p>
            </div>
        </a>

        <a href="<?= $base ?>/admin/products/verification" 
           class="p-4 rounded-2xl bg-white border border-secondary-100 hover:border-blue-400 hover:shadow-md transition-all flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                <ion-icon name="checkmark-done-outline"></ion-icon>
            </div>
            <div>
                <h5 class="text-xs font-bold text-secondary-900 group-hover:text-blue-600 transition-colors">Price Verification</h5>
                <p class="text-[11px] text-secondary-500"><?= $unverifiedCount ?> unconfirmed prices</p>
            </div>
        </a>

        <a href="<?= $base ?>/admin/products/availability" 
           class="p-4 rounded-2xl bg-white border border-secondary-100 hover:border-amber-400 hover:shadow-md transition-all flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                <ion-icon name="flash-outline"></ion-icon>
            </div>
            <div>
                <h5 class="text-xs font-bold text-secondary-900 group-hover:text-amber-600 transition-colors">Availability Control</h5>
                <p class="text-[11px] text-secondary-500"><?= $pendingAvailability ?> items pending</p>
            </div>
        </a>

        <a href="<?= $base ?>/admin/products/procurement" 
           class="p-4 rounded-2xl bg-white border border-secondary-100 hover:border-red-400 hover:shadow-md transition-all flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                <ion-icon name="cart-outline"></ion-icon>
            </div>
            <div>
                <h5 class="text-xs font-bold text-secondary-900 group-hover:text-red-600 transition-colors">Procurement List</h5>
                <p class="text-[11px] text-secondary-500">Items requiring purchase</p>
            </div>
        </a>
    </div>


    <!-- Two Column Breakdown Tables: Low Stock Urgency & Recently Added Products -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Low Stock Urgency Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-secondary-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-secondary-900 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 inline-block animate-pulse"></span>
                        Low Stock Urgency List (মজুদ শেষ হওয়ার ঝুঁকিতে)
                    </h3>
                    <p class="text-[11px] text-secondary-400 mt-0.5">পণ্যগুলোর স্টক ১০ এর নিচে নেমে গেছে, পুনরায় ক্রয় করুন।</p>
                </div>
                <a href="/sodai-dorkar/public/admin/products?stock_status=low_stock" class="text-xs font-bold text-primary-600 hover:underline">
                    সব দেখুন &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-secondary-600">
                    <thead class="bg-secondary-50/80 text-secondary-500 font-bold uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3">পণ্য</th>
                            <th class="px-3 py-3">ভেন্ডর</th>
                            <th class="px-3 py-3 text-center">মজুদ</th>
                            <th class="px-4 py-3 text-right">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-secondary-100">
                        <?php if (empty($lowStockItems)): ?>
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-secondary-400">
                                    বর্তমানে কোনো কম স্টকের পণ্য নেই! সব পণ্য পর্যাপ্ত মজুদে রয়েছে।
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($lowStockItems as $item): ?>
                                <tr class="hover:bg-secondary-50/60 transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-secondary-900 line-clamp-1"><?= htmlspecialchars($item['name']) ?></div>
                                        <div class="text-[10px] text-secondary-400 font-mono"><?= htmlspecialchars($item['sku'] ?? 'N/A') ?></div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <span class="text-secondary-700 font-medium"><?= htmlspecialchars($item['vendor_name'] ?? 'ভেন্ডর নেই') ?></span>
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <span class="inline-block px-2 py-0.5 rounded font-black text-[11px] <?= (float)$item['stock_qty'] <= 0 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-800' ?>">
                                            <?= floatval($item['stock_qty']) ?> <?= htmlspecialchars($item['base_unit'] ?? 'pcs') ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="/sodai-dorkar/public/admin/products/edit?id=<?= $item['id'] ?>" class="text-primary-600 hover:text-primary-700 font-bold hover:underline">
                                            স্টক বাড়ান
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recently Added Products Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-secondary-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-secondary-900 flex items-center gap-2">
                        <ion-icon name="time-outline" class="text-blue-600 text-base"></ion-icon>
                        Recently Added Products (সম্প্রতি যুক্ত পণ্য)
                    </h3>
                    <p class="text-[11px] text-secondary-400 mt-0.5">সিস্টেমে সম্প্রতি যুক্ত করা ৮টি পণ্যের বিস্তারিত।</p>
                </div>
                <a href="/sodai-dorkar/public/admin/products" class="text-xs font-bold text-primary-600 hover:underline">
                    সকল পণ্য &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-secondary-600">
                    <thead class="bg-secondary-50/80 text-secondary-500 font-bold uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3">পণ্য</th>
                            <th class="px-3 py-3">ক্যাটাগরি</th>
                            <th class="px-3 py-3 text-right">বিক্রয় মূল্য</th>
                            <th class="px-4 py-3 text-right">মজুদ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-secondary-100">
                        <?php if (empty($recentProducts)): ?>
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-secondary-400">
                                    এখনো কোনো পণ্য যুক্ত করা হয়নি।
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentProducts as $rp): ?>
                                <tr class="hover:bg-secondary-50/60 transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-secondary-900 line-clamp-1"><?= htmlspecialchars($rp['name']) ?></div>
                                        <div class="text-[10px] text-secondary-400 font-mono"><?= htmlspecialchars($rp['sku'] ?? 'N/A') ?></div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <span class="text-secondary-700"><?= htmlspecialchars($rp['category_name'] ?? 'General') ?></span>
                                    </td>
                                    <td class="px-3 py-3 text-right font-bold text-secondary-900">
                                        ৳ <?= number_format($rp['sell_price']) ?>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="inline-block px-2 py-0.5 rounded text-[11px] font-bold <?= (float)$rp['stock_qty'] <= 0 ? 'bg-red-50 text-red-600' : 'bg-emerald-50 text-emerald-700' ?>">
                                            <?= floatval($rp['stock_qty']) ?> <?= htmlspecialchars($rp['base_unit'] ?? 'pcs') ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
