<?php 
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';

$salesToday = (float)($todayData['sales_amount'] ?? 0);
$ordersToday = (int)($todayData['orders_count'] ?? 0);

$salesYesterday = (float)($yesterdayData['sales_amount'] ?? 0);
$ordersYesterday = (int)($yesterdayData['orders_count'] ?? 0);

$salesWeek = (float)($weekData['sales_amount'] ?? 0);
$ordersWeek = (int)($weekData['orders_count'] ?? 0);

$salesMonth = (float)($monthData['sales_amount'] ?? 0);
$ordersMonth = (int)($monthData['orders_count'] ?? 0);

$activeOrders = ($statusCounts['pending'] ?? 0) + ($statusCounts['processing'] ?? 0) + ($statusCounts['packed'] ?? 0) + ($statusCounts['shipped'] ?? 0) + ($statusCounts['out_for_delivery'] ?? 0);
?>

<div class="space-y-6">

    <!-- Top Executive Welcome Banner -->
    <div class="rounded-2xl p-6 lg:p-8 shadow-lg relative overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 55%, #064e3b 100%); color: #ffffff;">
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold tracking-wider text-emerald-300" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3);">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Sodai Dorkar &bull; Executive Command Center
                </div>
                <h1 class="text-2xl lg:text-3xl font-black tracking-tight text-white">
                    Welcome back, Admin!
                </h1>
                <p class="text-slate-300 text-sm max-w-2xl leading-relaxed">
                    Live overview for <strong class="text-white"><?= date('l, d F Y') ?></strong>. Monitor sales performance, order delivery funnel, stock inventory, and human resources in real-time.
                </p>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex flex-wrap items-center gap-3">
                <a href="<?= $base ?>/admin/orders/create" class="font-bold py-2.5 px-4 rounded-xl text-sm flex items-center gap-2 transition-all shadow-md hover:opacity-95" style="background: #059669; color: #ffffff;">
                    <ion-icon name="add-circle-outline" class="text-lg"></ion-icon>
                    <span>Create Order</span>
                </a>
                <a href="<?= $base ?>/admin/purchases/create" class="font-medium py-2.5 px-3.5 rounded-xl text-sm flex items-center gap-2 transition-all hover:bg-white/20" style="background: rgba(255, 255, 255, 0.15); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.25);">
                    <ion-icon name="cart-outline" class="text-lg"></ion-icon>
                    <span>Stock In</span>
                </a>
                <a href="<?= $base ?>/admin/dispatch" class="font-medium py-2.5 px-3.5 rounded-xl text-sm flex items-center gap-2 transition-all hover:bg-white/20" style="background: rgba(255, 255, 255, 0.15); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.25);">
                    <ion-icon name="bicycle-outline" class="text-lg"></ion-icon>
                    <span>Dispatch Board</span>
                </a>
                <a href="<?= $base ?>/admin/hr/attendance" class="font-medium py-2.5 px-3.5 rounded-xl text-sm flex items-center gap-2 transition-all hover:bg-white/20" style="background: rgba(255, 255, 255, 0.15); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.25);">
                    <ion-icon name="calendar-outline" class="text-lg"></ion-icon>
                    <span>Attendance</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Primary KPI Metric Cards (4 Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Card 1: Today's Revenue -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Today's Revenue</span>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl" style="background: #ecfdf5; color: #059669;">
                    <ion-icon name="cash-outline"></ion-icon>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl lg:text-3xl font-black text-slate-900">৳<?= number_format($salesToday, 2) ?></h3>
                <div class="flex items-center gap-2 mt-2 text-xs">
                    <span class="px-2 py-0.5 rounded-md font-bold" style="background: #dcfce7; color: #15803d;">
                        <?= $ordersToday ?> Orders Today
                    </span>
                    <span class="text-slate-500">Yesterday: ৳<?= number_format($salesYesterday, 0) ?></span>
                </div>
            </div>
        </div>

        <!-- Card 2: Monthly Revenue -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Month Revenue (<?= date('M Y') ?>)</span>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl" style="background: #faf5ff; color: #7e22ce;">
                    <ion-icon name="wallet-outline"></ion-icon>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl lg:text-3xl font-black text-purple-700">৳<?= number_format($salesMonth, 2) ?></h3>
                <div class="flex items-center gap-2 mt-2 text-xs">
                    <span class="px-2 py-0.5 rounded-md font-bold text-purple-700" style="background: #f3e8ff;">
                        <?= $ordersMonth ?> Orders
                    </span>
                    <span class="text-slate-500">AOV: ৳<?= number_format($aov, 0) ?></span>
                </div>
            </div>
        </div>

        <!-- Card 3: Active Order Pipeline -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Active Orders Pipeline</span>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl" style="background: #eff6ff; color: #2563eb;">
                    <ion-icon name="cart-outline"></ion-icon>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl lg:text-3xl font-black text-blue-600"><?= $activeOrders ?> <span class="text-sm font-semibold text-slate-500">Orders</span></h3>
                <div class="flex items-center gap-2 mt-2 text-xs">
                    <span class="px-2 py-0.5 rounded-md font-bold" style="background: #fef3c7; color: #b45309;">
                        <?= $statusCounts['pending'] ?> Pending
                    </span>
                    <span class="text-slate-500"><?= $statusCounts['out_for_delivery'] ?> in delivery</span>
                </div>
            </div>
        </div>

        <!-- Card 4: Customers & Products -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Customers & Inventory</span>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl" style="background: #f0fdfa; color: #0d9488;">
                    <ion-icon name="people-outline"></ion-icon>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl lg:text-3xl font-black text-slate-900"><?= number_format($totalCustomers) ?> <span class="text-sm font-semibold text-slate-500">Customers</span></h3>
                <div class="flex items-center gap-2 mt-2 text-xs">
                    <span class="px-2 py-0.5 rounded-md font-bold" style="background: #ccfbf1; color: #0f766e;">
                        <?= $totalProducts ?> Active Products
                    </span>
                    <?php if ($lowStockCount > 0): ?>
                        <span class="px-2 py-0.5 rounded-md font-bold" style="background: #ffe4e6; color: #e11d48;">
                            <?= $lowStockCount ?> Low Stock
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Operational Highlights Strip -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 rounded-2xl bg-white border border-slate-200 shadow-sm text-xs">
        <div class="border-r border-slate-100 pr-2">
            <span class="text-slate-400 font-medium">Last 7 Days Sales:</span>
            <p class="font-bold text-slate-800 text-sm mt-0.5">৳<?= number_format($salesWeek, 2) ?></p>
            <span class="text-[11px] text-slate-500"><?= $ordersWeek ?> Orders completed</span>
        </div>
        <div class="border-r border-slate-100 pr-2">
            <span class="text-slate-400 font-medium">Active Delivery Riders:</span>
            <p class="font-bold text-slate-800 text-sm mt-0.5"><?= $ridersCount ?> Riders on Duty</p>
            <span class="text-[11px] text-emerald-600 font-medium"><?= $deliveredToday ?> Delivered Today</span>
        </div>
        <div class="border-r border-slate-100 pr-2">
            <span class="text-slate-400 font-medium">Staff Attendance Today:</span>
            <p class="font-bold text-slate-800 text-sm mt-0.5"><?= $hrStats['present_today'] ?> / <?= $hrStats['active_employees'] ?> Present</p>
            <span class="text-[11px] text-slate-500">Rate: <?= $hrStats['active_employees'] > 0 ? round(($hrStats['present_today'] / $hrStats['active_employees']) * 100) : 0 ?>%</span>
        </div>
        <div>
            <span class="text-slate-400 font-medium">Out of Stock Warnings:</span>
            <p class="font-bold <?= $outOfStockCount > 0 ? 'text-red-600' : 'text-emerald-600' ?> text-sm mt-0.5">
                <?= $outOfStockCount ?> Out of Stock
            </p>
            <span class="text-[11px] text-slate-500"><?= $lowStockCount ?> Low Stock Items</span>
        </div>
    </div>

    <!-- Order Lifecycle Fulfillment Funnel (7-Stage Pipeline) -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
            <div>
                <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                    <ion-icon name="git-network-outline" class="text-emerald-600 text-xl"></ion-icon>
                    Order Fulfillment Pipeline
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Real-time status tracking from order placement to doorstep delivery</p>
            </div>
            <a href="<?= $base ?>/admin/orders" class="text-xs text-emerald-600 font-bold hover:underline flex items-center gap-1">
                View All Orders &rarr;
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-3.5">
            <!-- 1. Pending -->
            <a href="<?= $base ?>/admin/orders?status=pending" class="p-3.5 rounded-xl bg-slate-50 hover:bg-amber-50/60 transition-all border border-slate-200 hover:border-amber-300 group" style="border-left: 4px solid #f59e0b;">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase text-amber-700">1. Pending</span>
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                </div>
                <h4 class="text-2xl font-black text-slate-900 mt-1.5"><?= $statusCounts['pending'] ?></h4>
                <p class="text-[10px] text-slate-500 mt-0.5 font-medium">Needs Verification</p>
            </a>

            <!-- 2. Processing -->
            <a href="<?= $base ?>/admin/orders?status=processing" class="p-3.5 rounded-xl bg-slate-50 hover:bg-blue-50/60 transition-all border border-slate-200 hover:border-blue-300 group" style="border-left: 4px solid #3b82f6;">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase text-blue-700">2. Processing</span>
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                </div>
                <h4 class="text-2xl font-black text-slate-900 mt-1.5"><?= $statusCounts['processing'] ?></h4>
                <p class="text-[10px] text-slate-500 mt-0.5 font-medium">Order Confirmed</p>
            </a>

            <!-- 3. Packed -->
            <a href="<?= $base ?>/admin/orders/packaging" class="p-3.5 rounded-xl bg-slate-50 hover:bg-indigo-50/60 transition-all border border-slate-200 hover:border-indigo-300 group" style="border-left: 4px solid #6366f1;">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase text-indigo-700">3. Packed</span>
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                </div>
                <h4 class="text-2xl font-black text-slate-900 mt-1.5"><?= $statusCounts['packed'] ?></h4>
                <p class="text-[10px] text-slate-500 mt-0.5 font-medium">Packaging Done</p>
            </a>

            <!-- 4. Shipped / Ready -->
            <a href="<?= $base ?>/admin/dispatch" class="p-3.5 rounded-xl bg-slate-50 hover:bg-cyan-50/60 transition-all border border-slate-200 hover:border-cyan-300 group" style="border-left: 4px solid #06b6d4;">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase text-cyan-700">4. Dispatch</span>
                    <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
                </div>
                <h4 class="text-2xl font-black text-slate-900 mt-1.5"><?= $statusCounts['shipped'] ?></h4>
                <p class="text-[10px] text-slate-500 mt-0.5 font-medium">Ready for Rider</p>
            </a>

            <!-- 5. Out for Delivery -->
            <a href="<?= $base ?>/admin/orders?status=out_for_delivery" class="p-3.5 rounded-xl bg-slate-50 hover:bg-purple-50/60 transition-all border border-slate-200 hover:border-purple-300 group" style="border-left: 4px solid #a855f7;">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase text-purple-700">5. In Transit</span>
                    <span class="w-2 h-2 rounded-full bg-purple-500 animate-ping"></span>
                </div>
                <h4 class="text-2xl font-black text-slate-900 mt-1.5"><?= $statusCounts['out_for_delivery'] ?></h4>
                <p class="text-[10px] text-slate-500 mt-0.5 font-medium">With Rider</p>
            </a>

            <!-- 6. Delivered -->
            <a href="<?= $base ?>/admin/orders?status=delivered" class="p-3.5 rounded-xl bg-slate-50 hover:bg-emerald-50/60 transition-all border border-slate-200 hover:border-emerald-300 group" style="border-left: 4px solid #10b981;">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase text-emerald-700">6. Delivered</span>
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                </div>
                <h4 class="text-2xl font-black text-slate-900 mt-1.5"><?= $statusCounts['delivered'] ?></h4>
                <p class="text-[10px] text-slate-500 mt-0.5 font-medium">Completed</p>
            </a>

            <!-- 7. Cancelled / Returns -->
            <a href="<?= $base ?>/admin/orders/returns" class="p-3.5 rounded-xl bg-slate-50 hover:bg-rose-50/60 transition-all border border-slate-200 hover:border-rose-300 group" style="border-left: 4px solid #ef4444;">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase text-rose-700">7. Returns</span>
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                </div>
                <h4 class="text-2xl font-black text-slate-900 mt-1.5"><?= $statusCounts['cancelled'] + $statusCounts['returned'] ?></h4>
                <p class="text-[10px] text-slate-500 mt-0.5 font-medium">Cancelled/Damaged</p>
            </a>
        </div>
    </div>

    <!-- Charts Section (2 Columns: Revenue Trend + Order Status Donut) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart 1: 7-Day Revenue Trend (2 Cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                        <ion-icon name="trending-up-outline" class="text-emerald-600 text-xl"></ion-icon>
                        Sales & Orders Trend (Last 7 Days)
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Daily comparison of gross revenue and total order count</p>
                </div>
                <span class="px-3 py-1 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">Weekly View</span>
            </div>
            <div class="h-64 sm:h-72">
                <canvas id="salesTrendChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Order Pipeline Status Donut (1 Col) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div>
                <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                    <ion-icon name="pie-chart-outline" class="text-purple-600 text-xl"></ion-icon>
                    Order Status Distribution
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Share of orders across all pipeline stages</p>
            </div>
            <div class="h-64 sm:h-72 flex items-center justify-center">
                <canvas id="orderStatusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Workforce & HR Quick Monitor -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3 mb-4">
            <div>
                <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                    <ion-icon name="people-outline" class="text-emerald-600 text-xl"></ion-icon>
                    HR & Workforce Monitor
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Daily employee attendance, leave requests, and payroll disbursements</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="<?= $base ?>/admin/hr/attendance" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-medium transition-colors">
                    Daily Attendance &rarr;
                </a>
                <a href="<?= $base ?>/admin/payroll" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-xl text-xs font-medium transition-colors">
                    Monthly Payroll &rarr;
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Active Employees -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-2xl">
                    <ion-icon name="people"></ion-icon>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">Active Employees</p>
                    <h4 class="text-xl font-bold text-slate-900"><?= $hrStats['active_employees'] ?> Staff</h4>
                </div>
            </div>

            <!-- Attendance Today -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-2xl">
                    <ion-icon name="checkmark-done"></ion-icon>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">Today's Attendance</p>
                    <h4 class="text-xl font-bold text-emerald-700"><?= $hrStats['present_today'] ?> / <?= $hrStats['active_employees'] ?> Present</h4>
                </div>
            </div>

            <!-- Pending Leaves -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-2xl">
                    <ion-icon name="airplane"></ion-icon>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">Pending Leaves</p>
                    <h4 class="text-xl font-bold text-amber-700"><?= $hrStats['pending_leaves'] ?> Requests</h4>
                </div>
            </div>

            <!-- Pending Payroll -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center text-2xl">
                    <ion-icon name="wallet"></ion-icon>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">Unpaid Salary Due</p>
                    <h4 class="text-xl font-bold text-purple-700">৳<?= number_format($hrStats['payroll_due'], 2) ?></h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Highlights: Top Performing Products & Critical Low Stock (2 Columns) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Fast-Moving Products -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                    <ion-icon name="flame-outline" class="text-amber-500 text-xl"></ion-icon>
                    Top Selling Products
                </h3>
                <a href="<?= $base ?>/admin/products" class="text-xs text-emerald-600 font-bold hover:underline">All Inventory &rarr;</a>
            </div>

            <div class="divide-y divide-slate-100">
                <?php if (empty($topProducts)): ?>
                    <p class="py-6 text-center text-slate-400 text-sm">No sales records available yet.</p>
                <?php else: ?>
                    <?php foreach ($topProducts as $idx => $prod): ?>
                        <div class="py-3 flex items-center justify-between hover:bg-slate-50 transition-colors rounded-xl px-2">
                            <div class="flex items-center gap-3">
                                <span class="w-6 text-center font-bold text-xs text-slate-400">#<?= $idx + 1 ?></span>
                                <div class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden flex items-center justify-center shrink-0">
                                    <?php if (!empty($prod['image_path'])): ?>
                                        <img src="<?= (strpos($prod['image_path'], 'http') === 0) ? $prod['image_path'] : ($base . '/' . ltrim($prod['image_path'], '/')) ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <ion-icon name="cube-outline" class="text-slate-400 text-xl"></ion-icon>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-800 text-sm truncate max-w-xs"><?= htmlspecialchars($prod['name']) ?></h4>
                                    <p class="text-xs text-slate-400">Unit Price: ৳<?= number_format($prod['sell_price'], 2) ?></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold" style="background: #ecfdf5; color: #059669;">
                                    <?= number_format($prod['units_sold']) ?> Sold
                                </span>
                                <p class="text-[11px] text-slate-400 mt-1">Stock Left: <?= $prod['stock_qty'] ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Critical Low Stock Alerts (Stock <= 5) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                    <ion-icon name="alert-circle-outline" class="text-rose-600 text-xl"></ion-icon>
                    Critical Low Stock Alerts
                </h3>
                <a href="<?= $base ?>/admin/reports/stock" class="text-xs text-rose-600 font-bold hover:underline">Stock Report &rarr;</a>
            </div>

            <div class="divide-y divide-slate-100">
                <?php if (empty($lowStockItems)): ?>
                    <div class="py-8 text-center text-slate-400">
                        <ion-icon name="checkmark-circle-outline" class="text-4xl text-emerald-500 mb-1"></ion-icon>
                        <p class="text-sm font-medium text-emerald-700">Healthy inventory. All products have sufficient stock!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($lowStockItems as $lItem): ?>
                        <div class="py-3 flex items-center justify-between hover:bg-slate-50 transition-colors rounded-xl px-2">
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($lItem['name']) ?></h4>
                                <p class="text-xs text-slate-400 font-mono">SKU: <?= htmlspecialchars($lItem['sku'] ?: 'N/A') ?> &bull; ৳<?= number_format($lItem['sell_price'], 2) ?></p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold border" style="background: #ffe4e6; color: #e11d48; border-color: #fecdd3;">
                                    Only <?= $lItem['stock_qty'] ?> Left
                                </span>
                                <a href="<?= $base ?>/admin/purchases/create" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs transition-colors" title="Restock item">
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
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                    <ion-icon name="receipt-outline" class="text-emerald-600 text-xl"></ion-icon>
                    Recent Orders
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Latest 10 incoming orders across all areas</p>
            </div>
            <a href="<?= $base ?>/admin/orders" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-colors" style="background: #ecfdf5; color: #047857;">
                View All Orders &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-600 uppercase text-xs border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4 font-semibold">Order ID</th>
                        <th class="py-3 px-4 font-semibold">Customer</th>
                        <th class="py-3 px-4 font-semibold">Delivery Area</th>
                        <th class="py-3 px-4 font-semibold text-center">Items</th>
                        <th class="py-3 px-4 font-semibold text-right">Total Amount</th>
                        <th class="py-3 px-4 font-semibold text-center">Status</th>
                        <th class="py-3 px-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($recentOrders)): ?>
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">No recent orders recorded.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentOrders as $ord): 
                            $st = strtolower(trim($ord['status'] ?? 'pending'));
                            $statusBadge = match($st) {
                                'pending' => ['bg' => 'background: #fef3c7; color: #b45309; border: 1px solid #fde68a;', 'label' => 'Pending'],
                                'processing' => ['bg' => 'background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;', 'label' => 'Processing'],
                                'packed' => ['bg' => 'background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe;', 'label' => 'Packed'],
                                'shipped' => ['bg' => 'background: #ecfeff; color: #0e7490; border: 1px solid #a5f3fc;', 'label' => 'Shipped'],
                                'out_for_delivery' => ['bg' => 'background: #faf5ff; color: #7e22ce; border: 1px solid #e9d5ff;', 'label' => 'Out for Delivery'],
                                'delivered' => ['bg' => 'background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;', 'label' => 'Delivered'],
                                'cancelled' => ['bg' => 'background: #fff1f2; color: #be123c; border: 1px solid #fecdd3;', 'label' => 'Cancelled'],
                                'returned' => ['bg' => 'background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca;', 'label' => 'Returned'],
                                default => ['bg' => 'background: #f1f5f9; color: #334155; border: 1px solid #e2e8f0;', 'label' => ucfirst($st)]
                            };
                        ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-3.5 px-4 font-mono font-bold text-emerald-700">
                                    <a href="<?= $base ?>/admin/orders/show?id=<?= $ord['id'] ?>" class="hover:underline">
                                        #<?= $ord['id'] ?>
                                    </a>
                                </td>
                                <td class="py-3.5 px-4">
                                    <p class="font-bold text-slate-900"><?= htmlspecialchars($ord['customer_name'] ?? 'Guest Customer') ?></p>
                                    <p class="text-xs text-slate-500 font-mono"><?= htmlspecialchars($ord['customer_phone'] ?? '') ?></p>
                                </td>
                                <td class="py-3.5 px-4 text-xs text-slate-600">
                                    <?= htmlspecialchars($ord['area_name'] ?? 'Central Zone') ?>
                                </td>
                                <td class="py-3.5 px-4 text-center font-bold text-slate-800 text-xs">
                                    <?= $ord['items_count'] ?? 1 ?> Items
                                </td>
                                <td class="py-3.5 px-4 text-right font-black text-slate-900 text-base">
                                    ৳<?= number_format($ord['total_amount'], 2) ?>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold" style="<?= $statusBadge['bg'] ?>">
                                        <?= $statusBadge['label'] ?>
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="<?= $base ?>/admin/orders/show?id=<?= $ord['id'] ?>" class="p-1.5 text-slate-600 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="View Details">
                                            <ion-icon name="eye-outline" class="text-lg"></ion-icon>
                                        </a>
                                        <a href="<?= $base ?>/admin/orders/invoice?id=<?= $ord['id'] ?>" target="_blank" class="p-1.5 text-slate-600 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Print Invoice">
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
                        label: 'Revenue (৳)',
                        data: <?= json_encode($chartSales) ?>,
                        borderColor: '#059669',
                        backgroundColor: 'rgba(5, 150, 105, 0.12)',
                        fill: true,
                        tension: 0.35,
                        yAxisID: 'y',
                        borderWidth: 3,
                        pointRadius: 4,
                        pointBackgroundColor: '#059669'
                    },
                    {
                        label: 'Orders Count',
                        data: <?= json_encode($chartOrders) ?>,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.05)',
                        borderDash: [4, 4],
                        tension: 0.35,
                        yAxisID: 'y1',
                        borderWidth: 2,
                        pointRadius: 3,
                        pointBackgroundColor: '#2563eb'
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
                        backgroundColor: '#0f172a',
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
                            callback: function(val) { return '৳' + Number(val).toLocaleString(); },
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
                labels: ['Pending', 'Processing', 'Packed', 'In Transit', 'Delivered', 'Cancelled/Return'],
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
