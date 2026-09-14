<?php 
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';

$salesToday = (float)($todayData['sales_amount'] ?? 0);
$ordersToday = (int)($todayData['orders_count'] ?? 0);

$salesMonth = (float)($monthData['sales_amount'] ?? 0);
$ordersMonth = (int)($monthData['orders_count'] ?? 0);

$activeOrders = ($statusCounts['pending'] ?? 0) + ($statusCounts['processing'] ?? 0) + ($statusCounts['packed'] ?? 0) + ($statusCounts['shipped'] ?? 0) + ($statusCounts['out_for_delivery'] ?? 0);
?>

<div class="space-y-6">

    <!-- Executive Greeting & Quick Action Banner -->
    <div class="relative overflow-hidden bg-gradient-to-r from-primary-900 via-primary-800 to-secondary-900 rounded-3xl p-6 lg:p-8 text-white shadow-xl">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-primary-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md text-xs font-semibold tracking-wider text-emerald-300 border border-white/10">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    সদাই দরকার &bull; সেন্ট্রাল কমান্ড সেন্টার
                </div>
                <h1 class="text-2xl lg:text-3xl font-black tracking-tight">
                    স্বাগতম, এডমিন!
                </h1>
                <p class="text-secondary-300 text-sm max-w-xl">
                    আজকের তারিখ: <strong class="text-white"><?= date('l, d F Y') ?></strong> &bull; এক নজরে আপনার স্টোরের সেলস, অর্ডার ডেলিভারি, ইনভেন্টরি ও এইচআর পেরোল মনিটর করুন।
                </p>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="<?= $base ?>/admin/orders/create" class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-2.5 px-4 rounded-xl text-sm flex items-center gap-2 transition-all shadow-lg shadow-emerald-900/30">
                    <ion-icon name="add-circle" class="text-lg"></ion-icon>
                    <span>নতুন অর্ডার</span>
                </a>
                <a href="<?= $base ?>/admin/purchases/create" class="bg-white/15 hover:bg-white/25 text-white font-medium py-2.5 px-3.5 rounded-xl text-sm flex items-center gap-2 backdrop-blur-md transition-all border border-white/15">
                    <ion-icon name="cart-outline" class="text-lg"></ion-icon>
                    <span>স্টক ইন (Purchase)</span>
                </a>
                <a href="<?= $base ?>/admin/dispatch" class="bg-white/15 hover:bg-white/25 text-white font-medium py-2.5 px-3.5 rounded-xl text-sm flex items-center gap-2 backdrop-blur-md transition-all border border-white/15">
                    <ion-icon name="bicycle-outline" class="text-lg"></ion-icon>
                    <span>ডেসপ্যাচ বোর্ড</span>
                </a>
                <a href="<?= $base ?>/admin/hr/attendance" class="bg-white/15 hover:bg-white/25 text-white font-medium py-2.5 px-3.5 rounded-xl text-sm flex items-center gap-2 backdrop-blur-md transition-all border border-white/15">
                    <ion-icon name="calendar-outline" class="text-lg"></ion-icon>
                    <span>দৈনিক হাজিরা</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Primary Financial KPI Cards (4 Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Sales Today -->
        <div class="bg-white rounded-2xl p-5 border border-secondary-200 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold text-secondary-400 uppercase tracking-wider">আজকের বিক্রয় (Today's Sales)</p>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                    <ion-icon name="cash"></ion-icon>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl lg:text-3xl font-black text-secondary-900">৳<?= number_format($salesToday, 2) ?></h3>
                <div class="flex items-center gap-2 mt-1.5 text-xs">
                    <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 font-bold">
                        <?= $ordersToday ?> টি অর্ডার আজ
                    </span>
                    <span class="text-secondary-400">সর্বমোট ভলিউম</span>
                </div>
            </div>
        </div>

        <!-- This Month Revenue -->
        <div class="bg-white rounded-2xl p-5 border border-secondary-200 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold text-secondary-400 uppercase tracking-wider">চলতি মাসের আয় (Monthly)</p>
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl">
                    <ion-icon name="wallet"></ion-icon>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl lg:text-3xl font-black text-purple-700">৳<?= number_format($salesMonth, 2) ?></h3>
                <div class="flex items-center gap-2 mt-1.5 text-xs">
                    <span class="px-2 py-0.5 rounded-md bg-purple-50 text-purple-700 font-bold">
                        <?= $ordersMonth ?> টি অর্ডার
                    </span>
                    <span class="text-secondary-400"><?= date('F Y') ?></span>
                </div>
            </div>
        </div>

        <!-- Active Orders in Pipeline -->
        <div class="bg-white rounded-2xl p-5 border border-secondary-200 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold text-secondary-400 uppercase tracking-wider">চলমান অর্ডার (Active Pipeline)</p>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                    <ion-icon name="cart"></ion-icon>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl lg:text-3xl font-black text-blue-600"><?= $activeOrders ?> <span class="text-sm font-semibold text-secondary-500">টি অর্ডার</span></h3>
                <div class="flex items-center gap-2 mt-1.5 text-xs">
                    <span class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 font-bold">
                        <?= $statusCounts['pending'] ?> অপেক্ষমাণ
                    </span>
                    <span class="text-secondary-400"><?= $statusCounts['out_for_delivery'] ?> টি ডেলিভারিতে</span>
                </div>
            </div>
        </div>

        <!-- Total Customers & Catalog -->
        <div class="bg-white rounded-2xl p-5 border border-secondary-200 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold text-secondary-400 uppercase tracking-wider">গ্রাহক ও প্রোডাক্ট সংখ্যা</p>
                <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl">
                    <ion-icon name="people"></ion-icon>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl lg:text-3xl font-black text-secondary-900"><?= number_format($totalCustomers) ?> <span class="text-sm font-semibold text-secondary-500">জন গ্রাহক</span></h3>
                <div class="flex items-center gap-2 mt-1.5 text-xs">
                    <span class="px-2 py-0.5 rounded-md bg-teal-50 text-teal-700 font-bold">
                        <?= $totalProducts ?> টি সক্রিয় প্রোডাক্ট
                    </span>
                    <?php if ($lowStockCount > 0): ?>
                        <span class="px-2 py-0.5 rounded-md bg-red-50 text-red-600 font-bold">
                            <?= $lowStockCount ?> টি লো-স্টক
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Operations Order Lifecycle Funnel (7-Stage Visual Pipeline) -->
    <div class="bg-white rounded-2xl border border-secondary-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4 border-b border-secondary-100 pb-3">
            <div>
                <h3 class="font-bold text-secondary-900 text-base flex items-center gap-2">
                    <ion-icon name="git-network-outline" class="text-primary-600 text-xl"></ion-icon>
                    অর্ডার লাইফসাইকেল ও অপারেশনাল পাইপলাইন (Order Lifecycle)
                </h3>
                <p class="text-xs text-secondary-400 mt-0.5">অর্ডার আসার পর থেকে গ্রাহকের কাছে পৌঁছানো পর্যন্ত প্রতিটি ধাপের লাইভ অবস্থা</p>
            </div>
            <a href="<?= $base ?>/admin/orders" class="text-xs text-primary-600 font-bold hover:underline flex items-center gap-1">
                সকল অর্ডার দেখুন &rarr;
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-3">
            <!-- Stage 1: Pending -->
            <a href="<?= $base ?>/admin/orders?status=pending" class="p-3.5 rounded-xl bg-amber-50/70 border border-amber-200 hover:border-amber-300 hover:shadow-sm transition-all group">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase text-amber-700">১. অপেক্ষমাণ</span>
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                </div>
                <h4 class="text-2xl font-black text-amber-900 mt-1"><?= $statusCounts['pending'] ?></h4>
                <p class="text-[10px] text-amber-600 font-medium mt-0.5">যাচাইকরণ প্রয়োজন</p>
            </a>

            <!-- Stage 2: Processing -->
            <a href="<?= $base ?>/admin/orders?status=processing" class="p-3.5 rounded-xl bg-blue-50/70 border border-blue-200 hover:border-blue-300 hover:shadow-sm transition-all group">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase text-blue-700">২. প্রসেসিং</span>
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                </div>
                <h4 class="text-2xl font-black text-blue-900 mt-1"><?= $statusCounts['processing'] ?></h4>
                <p class="text-[10px] text-blue-600 font-medium mt-0.5">অর্ডার গৃহীত হয়েছে</p>
            </a>

            <!-- Stage 3: Packed -->
            <a href="<?= $base ?>/admin/orders/packaging" class="p-3.5 rounded-xl bg-indigo-50/70 border border-indigo-200 hover:border-indigo-300 hover:shadow-sm transition-all group">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase text-indigo-700">৩. প্যাকড</span>
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                </div>
                <h4 class="text-2xl font-black text-indigo-900 mt-1"><?= $statusCounts['packed'] ?></h4>
                <p class="text-[10px] text-indigo-600 font-medium mt-0.5">প্যাকেজিং সমাপ্ত</p>
            </a>

            <!-- Stage 4: Shipped -->
            <a href="<?= $base ?>/admin/dispatch" class="p-3.5 rounded-xl bg-cyan-50/70 border border-cyan-200 hover:border-cyan-300 hover:shadow-sm transition-all group">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase text-cyan-700">৪. রেডি ডেসপ্যাচ</span>
                    <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
                </div>
                <h4 class="text-2xl font-black text-cyan-900 mt-1"><?= $statusCounts['shipped'] ?></h4>
                <p class="text-[10px] text-cyan-600 font-medium mt-0.5">রাইডারে বরাদ্দের জন্য</p>
            </a>

            <!-- Stage 5: Out for Delivery -->
            <a href="<?= $base ?>/admin/orders?status=out_for_delivery" class="p-3.5 rounded-xl bg-purple-50/70 border border-purple-200 hover:border-purple-300 hover:shadow-sm transition-all group">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase text-purple-700">৫. অন দ্য ওয়ে</span>
                    <span class="w-2 h-2 rounded-full bg-purple-500 animate-ping"></span>
                </div>
                <h4 class="text-2xl font-black text-purple-900 mt-1"><?= $statusCounts['out_for_delivery'] ?></h4>
                <p class="text-[10px] text-purple-600 font-medium mt-0.5">রাইডারের হাতে রয়েছে</p>
            </a>

            <!-- Stage 6: Delivered -->
            <a href="<?= $base ?>/admin/orders?status=delivered" class="p-3.5 rounded-xl bg-emerald-50/70 border border-emerald-200 hover:border-emerald-300 hover:shadow-sm transition-all group">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase text-emerald-700">৬. সফল ডেলিভারি</span>
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                </div>
                <h4 class="text-2xl font-black text-emerald-900 mt-1"><?= $statusCounts['delivered'] ?></h4>
                <p class="text-[10px] text-emerald-600 font-medium mt-0.5">টাকা সংগ্রহ সম্পন্ন</p>
            </a>

            <!-- Stage 7: Cancelled & Returns -->
            <a href="<?= $base ?>/admin/orders/returns" class="p-3.5 rounded-xl bg-rose-50/70 border border-rose-200 hover:border-rose-300 hover:shadow-sm transition-all group">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase text-rose-700">৭. বাতিল / রিটার্ন</span>
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                </div>
                <h4 class="text-2xl font-black text-rose-900 mt-1"><?= $statusCounts['cancelled'] + $statusCounts['returned'] ?></h4>
                <p class="text-[10px] text-rose-600 font-medium mt-0.5">রিটার্ন ও ড্যামেজ</p>
            </a>
        </div>
    </div>

    <!-- Charts Section (2 Columns: Sales 7-Day Trend + Order Status Donut) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart 1: 7-Day Revenue Trend (2 Cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-secondary-200 shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-secondary-900 text-base flex items-center gap-2">
                        <ion-icon name="trending-up" class="text-emerald-600 text-xl"></ion-icon>
                        গত ৭ দিনের বিক্রয় ও অর্ডার ট্রেন্ড (Sales Analytics)
                    </h3>
                    <p class="text-xs text-secondary-400 mt-0.5">দৈনিক রেভিনিউ এবং অর্ডারের পরিমাণের তুলনামূলক গ্রাফ</p>
                </div>
                <span class="px-3 py-1 bg-secondary-100 text-secondary-700 rounded-xl text-xs font-semibold">বিগত ৭ দিন</span>
            </div>
            <div class="h-64 sm:h-72">
                <canvas id="salesTrendChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Order Status Distribution Donut (1 Col) -->
        <div class="bg-white rounded-2xl border border-secondary-200 shadow-sm p-6 space-y-4">
            <div>
                <h3 class="font-bold text-secondary-900 text-base flex items-center gap-2">
                    <ion-icon name="pie-chart" class="text-purple-600 text-xl"></ion-icon>
                    অর্ডার স্ট্যাটাস অনুপাত (Distribution)
                </h3>
                <p class="text-xs text-secondary-400 mt-0.5">মোট অর্ডারের স্ট্যাটাসভিত্তিক বিভাজন</p>
            </div>
            <div class="h-64 sm:h-72 flex items-center justify-center">
                <canvas id="orderStatusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Integrated HR & Workforce Snapshot -->
    <div class="bg-white rounded-2xl border border-secondary-200 shadow-sm p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-secondary-100 pb-3 mb-4">
            <div>
                <h3 class="font-bold text-secondary-900 text-base flex items-center gap-2">
                    <ion-icon name="id-card" class="text-primary-600 text-xl"></ion-icon>
                    এইচআর ও ওয়ার্কফোর্স সারসংক্ষেপ (HR & Workforce Overview)
                </h3>
                <p class="text-xs text-secondary-400 mt-0.5">অফিস স্টাফদের আজকের উপস্থিতি, ছুটির আবেদন ও চলতি মাসের পেরোল অবস্থা</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="<?= $base ?>/admin/hr/attendance" class="px-3 py-1.5 bg-secondary-100 hover:bg-secondary-200 text-secondary-700 rounded-xl text-xs font-medium transition-colors">
                    হাজিরা খাতা &rarr;
                </a>
                <a href="<?= $base ?>/admin/payroll" class="px-3 py-1.5 bg-primary-50 hover:bg-primary-100 text-primary-700 rounded-xl text-xs font-medium transition-colors">
                    মাসিক পেরোল &rarr;
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Active Staff -->
            <div class="p-4 rounded-xl bg-secondary-50 border border-secondary-200 flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-2xl">
                    <ion-icon name="people"></ion-icon>
                </div>
                <div>
                    <p class="text-xs text-secondary-500 font-medium">সক্রিয় কর্মকর্তা/কর্মচারী</p>
                    <h4 class="text-xl font-bold text-secondary-900"><?= $hrStats['active_employees'] ?> জন</h4>
                </div>
            </div>

            <!-- Attendance Today -->
            <div class="p-4 rounded-xl bg-secondary-50 border border-secondary-200 flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-2xl">
                    <ion-icon name="checkmark-done"></ion-icon>
                </div>
                <div>
                    <p class="text-xs text-secondary-500 font-medium">আজকের উপস্থিতি</p>
                    <h4 class="text-xl font-bold text-emerald-700"><?= $hrStats['present_today'] ?> / <?= $hrStats['active_employees'] ?> জন</h4>
                </div>
            </div>

            <!-- Pending Leaves -->
            <div class="p-4 rounded-xl bg-secondary-50 border border-secondary-200 flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-2xl">
                    <ion-icon name="airplane"></ion-icon>
                </div>
                <div>
                    <p class="text-xs text-secondary-500 font-medium">অপেক্ষমাণ ছুটির আবেদন</p>
                    <h4 class="text-xl font-bold text-amber-700"><?= $hrStats['pending_leaves'] ?> টি আবেদন</h4>
                </div>
            </div>

            <!-- Pending Payroll -->
            <div class="p-4 rounded-xl bg-secondary-50 border border-secondary-200 flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center text-2xl">
                    <ion-icon name="wallet"></ion-icon>
                </div>
                <div>
                    <p class="text-xs text-secondary-500 font-medium">চলতি মাসের বকেয়া বেতন</p>
                    <h4 class="text-xl font-bold text-purple-700">৳<?= number_format($hrStats['payroll_due'], 2) ?></h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Highlights: Fast-Moving Products & Critical Low Stock (2 Columns) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Fast-Moving Products -->
        <div class="bg-white rounded-2xl border border-secondary-200 shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-secondary-100 pb-3">
                <h3 class="font-bold text-secondary-900 text-base flex items-center gap-2">
                    <ion-icon name="flame" class="text-amber-500 text-xl"></ion-icon>
                    টপ সেলিং প্রোডাক্টস (Top Moving Items)
                </h3>
                <a href="<?= $base ?>/admin/products" class="text-xs text-primary-600 font-bold hover:underline">ইনভেন্টরি &rarr;</a>
            </div>

            <div class="divide-y divide-secondary-100">
                <?php if (empty($topProducts)): ?>
                    <p class="py-6 text-center text-secondary-400 text-sm">কোনো প্রোডাক্ট ডেটা নেই।</p>
                <?php else: ?>
                    <?php foreach ($topProducts as $idx => $prod): ?>
                        <div class="py-3 flex items-center justify-between hover:bg-secondary-50 transition-colors rounded-xl px-2">
                            <div class="flex items-center gap-3">
                                <span class="w-6 text-center font-bold text-xs text-secondary-400">#<?= $idx + 1 ?></span>
                                <div class="w-10 h-10 rounded-xl bg-secondary-100 border border-secondary-200 overflow-hidden flex items-center justify-center shrink-0">
                                    <?php if (!empty($prod['image_path'])): ?>
                                        <img src="<?= (strpos($prod['image_path'], 'http') === 0) ? $prod['image_path'] : ($base . '/' . ltrim($prod['image_path'], '/')) ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <ion-icon name="cube" class="text-secondary-400 text-xl"></ion-icon>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <h4 class="font-bold text-secondary-800 text-sm truncate max-w-xs"><?= htmlspecialchars($prod['name']) ?></h4>
                                    <p class="text-xs text-secondary-400">বিক্রয়মূল্য: ৳<?= number_format($prod['sell_price'], 2) ?></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-bold">
                                    <?= number_format($prod['units_sold']) ?> ইউনিট সেল
                                </span>
                                <p class="text-[11px] text-secondary-400 mt-1">স্টক: <?= $prod['stock_qty'] ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Critical Low Stock Alerts (Stock <= 5) -->
        <div class="bg-white rounded-2xl border border-secondary-200 shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-secondary-100 pb-3">
                <h3 class="font-bold text-secondary-900 text-base flex items-center gap-2">
                    <ion-icon name="alert-circle" class="text-rose-600 text-xl"></ion-icon>
                    জরুরি লো-স্টক সতর্কতা (Low Stock Alerts)
                </h3>
                <a href="<?= $base ?>/admin/reports/stock" class="text-xs text-rose-600 font-bold hover:underline">স্টক রিপোর্ট &rarr;</a>
            </div>

            <div class="divide-y divide-secondary-100">
                <?php if (empty($lowStockItems)): ?>
                    <div class="py-8 text-center text-secondary-400">
                        <ion-icon name="checkmark-circle-outline" class="text-4xl text-emerald-500 mb-1"></ion-icon>
                        <p class="text-sm font-medium text-emerald-700">সকল প্রোডাক্টের পর্যাপ্ত স্টক মজুদ রয়েছে!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($lowStockItems as $lItem): ?>
                        <div class="py-3 flex items-center justify-between hover:bg-secondary-50 transition-colors rounded-xl px-2">
                            <div>
                                <h4 class="font-bold text-secondary-900 text-sm"><?= htmlspecialchars($lItem['name']) ?></h4>
                                <p class="text-xs text-secondary-400 font-mono">SKU: <?= htmlspecialchars($lItem['sku'] ?: 'N/A') ?> &bull; ৳<?= number_format($lItem['sell_price'], 2) ?></p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-1 rounded-lg bg-rose-100 text-rose-800 text-xs font-bold border border-rose-200">
                                    মাত্র <?= $lItem['stock_qty'] ?> টি বাকি
                                </span>
                                <a href="<?= $base ?>/admin/purchases/create" class="p-1.5 bg-secondary-100 hover:bg-secondary-200 text-secondary-700 rounded-lg text-xs" title="রিস্টক করুন">
                                    <ion-icon name="add" class="text-base"></ion-icon>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent 10 Orders Detailed Table -->
    <div class="bg-white rounded-2xl border border-secondary-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-secondary-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-bold text-secondary-900 text-base flex items-center gap-2">
                    <ion-icon name="receipt" class="text-primary-600 text-xl"></ion-icon>
                    সাম্প্রতিক অর্ডারসমূহ (Recent Orders)
                </h3>
                <p class="text-xs text-secondary-400 mt-0.5">লাইভ ইনকামিং এবং প্রসেসিং অবস্থায় থাকা শেষ ১০টি অর্ডার</p>
            </div>
            <a href="<?= $base ?>/admin/orders" class="px-3.5 py-1.5 bg-primary-50 hover:bg-primary-100 text-primary-700 rounded-xl text-xs font-bold transition-colors">
                সকল অর্ডার ম্যানেজ করুন &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-secondary-50 text-secondary-600 uppercase text-xs border-b border-secondary-200">
                    <tr>
                        <th class="py-3 px-4 font-semibold">অর্ডার আইডি</th>
                        <th class="py-3 px-4 font-semibold">গ্রাহকের নাম ও ফোন</th>
                        <th class="py-3 px-4 font-semibold">ডেলিভারি এলাকা</th>
                        <th class="py-3 px-4 font-semibold text-center">আইটেম সংখ্যা</th>
                        <th class="py-3 px-4 font-semibold text-right">মোট টাকা</th>
                        <th class="py-3 px-4 font-semibold text-center">স্ট্যাটাস</th>
                        <th class="py-3 px-4 font-semibold text-right">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-secondary-100">
                    <?php if (empty($recentOrders)): ?>
                        <tr>
                            <td colspan="7" class="py-12 text-center text-secondary-400">কোনো সাম্প্রতিক অর্ডার পাওয়া যায়নি।</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentOrders as $ord): 
                            $st = strtolower(trim($ord['status'] ?? 'pending'));
                            $statusBadge = match($st) {
                                'pending' => ['bg' => 'bg-amber-100 text-amber-800 border-amber-200', 'label' => 'Pending'],
                                'processing' => ['bg' => 'bg-blue-100 text-blue-800 border-blue-200', 'label' => 'Processing'],
                                'packed' => ['bg' => 'bg-indigo-100 text-indigo-800 border-indigo-200', 'label' => 'Packed'],
                                'shipped' => ['bg' => 'bg-cyan-100 text-cyan-800 border-cyan-200', 'label' => 'Shipped'],
                                'out_for_delivery' => ['bg' => 'bg-purple-100 text-purple-800 border-purple-200', 'label' => 'Out for Delivery'],
                                'delivered' => ['bg' => 'bg-emerald-100 text-emerald-800 border-emerald-200', 'label' => 'Delivered'],
                                'cancelled' => ['bg' => 'bg-rose-100 text-rose-800 border-rose-200', 'label' => 'Cancelled'],
                                'returned' => ['bg' => 'bg-red-100 text-red-800 border-red-200', 'label' => 'Returned'],
                                default => ['bg' => 'bg-secondary-100 text-secondary-800 border-secondary-200', 'label' => ucfirst($st)]
                            };
                        ?>
                            <tr class="hover:bg-secondary-50/60 transition-colors">
                                <td class="py-3 px-4 font-mono font-bold text-primary-700">
                                    <a href="<?= $base ?>/admin/orders/show?id=<?= $ord['id'] ?>" class="hover:underline">
                                        #<?= $ord['id'] ?>
                                    </a>
                                </td>
                                <td class="py-3 px-4">
                                    <p class="font-bold text-secondary-900"><?= htmlspecialchars($ord['customer_name'] ?? 'Guest Customer') ?></p>
                                    <p class="text-xs text-secondary-500 font-mono"><?= htmlspecialchars($ord['customer_phone'] ?? '') ?></p>
                                </td>
                                <td class="py-3 px-4 text-xs text-secondary-600">
                                    <?= htmlspecialchars($ord['area_name'] ?? 'ঢাকা সদর') ?>
                                </td>
                                <td class="py-3 px-4 text-center font-bold text-secondary-800 text-xs">
                                    <?= $ord['items_count'] ?? 1 ?> টি
                                </td>
                                <td class="py-3 px-4 text-right font-black text-secondary-900">
                                    ৳<?= number_format($ord['total_amount'], 2) ?>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold border <?= $statusBadge['bg'] ?>">
                                        <?= $statusBadge['label'] ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="<?= $base ?>/admin/orders/show?id=<?= $ord['id'] ?>" class="p-1.5 text-secondary-600 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors" title="বিস্তারিত দেখুন">
                                            <ion-icon name="eye-outline" class="text-lg"></ion-icon>
                                        </a>
                                        <a href="<?= $base ?>/admin/orders/invoice?id=<?= $ord['id'] ?>" target="_blank" class="p-1.5 text-secondary-600 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="ইনভয়েস ভাউচার">
                                            <ion-icon name="receipt-outline" class="text-lg"></ion-icon>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Include Chart.js from CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Sales & Orders Trend Line Chart
    const trendCtx = document.getElementById('salesTrendChart');
    if (trendCtx) {
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode($chartLabels) ?>,
                datasets: [
                    {
                        label: 'বিক্রয় (৳)',
                        data: <?= json_encode($chartSales) ?>,
                        borderColor: '#059669',
                        backgroundColor: 'rgba(5, 150, 105, 0.1)',
                        fill: true,
                        tension: 0.35,
                        yAxisID: 'y',
                        borderWidth: 3,
                        pointRadius: 4,
                        pointBackgroundColor: '#059669'
                    },
                    {
                        label: 'অর্ডার সংখ্যা',
                        data: <?= json_encode($chartOrders) ?>,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.05)',
                        borderDash: [5, 5],
                        tension: 0.35,
                        yAxisID: 'y1',
                        borderWidth: 2,
                        pointRadius: 3,
                        pointBackgroundColor: '#3b82f6'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { boxWidth: 12, font: { family: 'Outfit', size: 11, weight: 'bold' } }
                    },
                    tooltip: {
                        padding: 10,
                        backgroundColor: '#1e293b',
                        titleFont: { family: 'Outfit', size: 12, weight: 'bold' },
                        bodyFont: { family: 'Outfit', size: 12 }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Outfit', size: 11 } }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            callback: function(val) { return '৳' + val; },
                            font: { family: 'Outfit', size: 10 }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        ticks: {
                            stepSize: 1,
                            font: { family: 'Outfit', size: 10 }
                        }
                    }
                }
            }
        });
    }

    // 2. Order Status Doughnut Chart
    const statusCtx = document.getElementById('orderStatusChart');
    if (statusCtx) {
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['অপেক্ষমাণ', 'প্রসেসিং', 'প্যাকড', 'ডেলিভারিতে', 'সম্পন্ন', 'বাতিল/ফেরত'],
                datasets: [{
                    data: [
                        <?= (int)($statusCounts['pending'] ?? 0) ?>,
                        <?= (int)($statusCounts['processing'] ?? 0) ?>,
                        <?= (int)($statusCounts['packed'] ?? 0) ?>,
                        <?= (int)($statusCounts['out_for_delivery'] ?? 0) ?>,
                        <?= (int)($statusCounts['delivered'] ?? 0) ?>,
                        <?= (int)(($statusCounts['cancelled'] ?? 0) + ($statusCounts['returned'] ?? 0)) ?>
                    ],
                    backgroundColor: [
                        '#f59e0b', // amber
                        '#3b82f6', // blue
                        '#6366f1', // indigo
                        '#a855f7', // purple
                        '#10b981', // emerald
                        '#f43f5e'  // rose
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, font: { family: 'Outfit', size: 10, weight: '600' } }
                    }
                }
            }
        });
    }
});
</script>
