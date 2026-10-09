<?php
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
?>

<div class="space-y-4">

    <!-- Flash Messages -->
    <?php if (!empty($success)): ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl shadow-xs text-xs font-bold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <ion-icon name="checkmark-circle" class="text-lg text-emerald-600"></ion-icon>
                <span><?= htmlspecialchars($success) ?></span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-2xl shadow-xs text-xs font-bold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <ion-icon name="alert-circle" class="text-lg text-red-600"></ion-icon>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">&times;</button>
        </div>
    <?php endif; ?>

    <!-- Welcome & Profile Card -->
    <div class="bg-gradient-to-br from-emerald-800 via-emerald-700 to-green-800 rounded-3xl p-5 text-white shadow-lg relative overflow-hidden">
        <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex items-start justify-between">
            <div>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-white/15 text-emerald-100 text-[10px] font-bold uppercase tracking-wider backdrop-blur-xs mb-1.5 border border-white/20">
                    <ion-icon name="shield-checkmark"></ion-icon>
                    <span>ভেরিফাইড ফিল্ড এজেন্ট</span>
                </span>
                <h1 class="text-xl font-black tracking-tight leading-tight"><?= htmlspecialchars($agent['name'] ?? 'Agent') ?></h1>
                <p class="text-emerald-100/90 text-xs mt-0.5 font-mono">@<?= htmlspecialchars($agent['username'] ?? '') ?> • <?= htmlspecialchars($agent['phone'] ?? '') ?></p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md border border-white/30 flex items-center justify-center text-white text-2xl font-black shrink-0">
                <?= strtoupper(mb_substr($agent['name'] ?? 'A', 0, 1)) ?>
            </div>
        </div>

        <!-- Assigned Areas Pill List -->
        <div class="mt-4 pt-3 border-t border-white/15 flex flex-wrap items-center gap-1.5">
            <span class="text-[11px] font-bold text-emerald-200 mr-1">কর্ম-এরিয়া:</span>
            <?php if (!empty($assignedAreas)): ?>
                <?php foreach ($assignedAreas as $area): ?>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-xl bg-white/20 text-white text-[11px] font-bold border border-white/20">
                        <ion-icon name="location" class="text-emerald-300"></ion-icon>
                        <span><?= htmlspecialchars($area['area_name']) ?></span>
                    </span>
                <?php endforeach; ?>
            <?php else: ?>
                <span class="px-2 py-0.5 rounded-xl bg-white/20 text-emerald-100 text-[10px] font-medium">
                    সকল এরিয়া (সার্বজনীন)
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Action Launchpad -->
    <div class="grid grid-cols-2 gap-3">
        <!-- Take Order (Primary CTA) -->
        <a href="<?= $base ?>/agent/shop" class="bg-gradient-to-r from-emerald-600 to-green-600 text-white rounded-3xl p-4 shadow-md hover:shadow-lg transition-all flex flex-col justify-between group active:scale-95">
            <div class="w-10 h-10 rounded-2xl bg-white/20 flex items-center justify-center text-2xl mb-2 group-hover:scale-110 transition-transform">
                <ion-icon name="cart"></ion-icon>
            </div>
            <div>
                <h2 class="text-sm font-black leading-tight">নতুন অর্ডার নিন</h2>
                <p class="text-[11px] text-emerald-100 mt-0.5">পণ্য সিলেক্ট করে কাস্টমারের নামে অর্ডার</p>
            </div>
        </a>

        <!-- Add Customer -->
        <a href="<?= $base ?>/agent/customers" class="bg-white border border-gray-200 rounded-3xl p-4 shadow-xs hover:border-emerald-300 transition-all flex flex-col justify-between group active:scale-95">
            <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-2xl mb-2 group-hover:scale-110 transition-transform">
                <ion-icon name="person-add"></ion-icon>
            </div>
            <div>
                <h2 class="text-sm font-black text-gray-900 leading-tight">নতুন কাস্টমার</h2>
                <p class="text-[11px] text-gray-500 mt-0.5">এরিয়া অনুযায়ী কাস্টমার নিবন্ধন</p>
            </div>
        </a>
    </div>

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-2 gap-3">
        <!-- Today Orders -->
        <div class="bg-white rounded-3xl p-4 border border-gray-100 shadow-xs">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">আজকের অর্ডার</span>
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            </div>
            <div class="text-2xl font-black text-gray-900 leading-tight">
                <?= $stats['today_orders'] ?> <span class="text-xs font-normal text-gray-400">টি</span>
            </div>
            <div class="text-[11px] font-bold text-emerald-600 mt-1">
                আজকের সেলস: ৳<?= number_format($stats['today_sales'], 0) ?>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="bg-white rounded-3xl p-4 border border-gray-100 shadow-xs">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">সর্বমোট অর্ডার</span>
                <ion-icon name="cube-outline" class="text-base text-gray-400"></ion-icon>
            </div>
            <div class="text-2xl font-black text-gray-900 leading-tight">
                <?= $stats['total_orders'] ?> <span class="text-xs font-normal text-gray-400">টি</span>
            </div>
            <div class="text-[11px] font-bold text-gray-600 mt-1">
                মোট সেলস: ৳<?= number_format($stats['total_sales'], 0) ?>
            </div>
        </div>

        <!-- Delivered Orders -->
        <div class="bg-white rounded-3xl p-4 border border-gray-100 shadow-xs">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">ডেলিভার্ড</span>
                <ion-icon name="checkmark-done-circle" class="text-base text-emerald-600"></ion-icon>
            </div>
            <div class="text-xl font-black text-emerald-600 leading-tight">
                <?= $stats['delivered_orders'] ?> <span class="text-xs font-normal text-gray-400">টি সম্পন্ন</span>
            </div>
            <div class="text-[10px] text-gray-400 mt-1">গ্রাহক পণ্য পেয়েছেন</div>
        </div>

        <!-- Pending / Processing Orders -->
        <div class="bg-white rounded-3xl p-4 border border-gray-100 shadow-xs">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">প্রসেসিং</span>
                <ion-icon name="time-outline" class="text-base text-amber-500"></ion-icon>
            </div>
            <div class="text-xl font-black text-amber-600 leading-tight">
                <?= $stats['pending_orders'] ?> <span class="text-xs font-normal text-gray-400">টি চলমান</span>
            </div>
            <div class="text-[10px] text-gray-400 mt-1">ডেলিভারির অপেক্ষায়</div>
        </div>
    </div>

    <!-- Recent Orders Section -->
    <div class="bg-white rounded-3xl border border-gray-200/80 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center text-sm font-bold">
                    <ion-icon name="receipt-outline"></ion-icon>
                </div>
                <h3 class="text-sm font-black text-gray-900">সাম্প্রতিক গৃহীত অর্ডার</h3>
            </div>
            <a href="<?= $base ?>/agent/orders" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 flex items-center gap-1">
                <span>সবগুলো</span>
                <ion-icon name="chevron-forward"></ion-icon>
            </a>
        </div>

        <div class="divide-y divide-gray-100">
            <?php if (empty($recentOrders)): ?>
                <div class="p-8 text-center text-gray-400">
                    <ion-icon name="bag-handle-outline" class="text-3xl text-gray-300 mb-1"></ion-icon>
                    <p class="text-xs font-bold text-gray-500">এখনো কোনো অর্ডার নেওয়া হয়নি।</p>
                    <a href="<?= $base ?>/agent/shop" class="inline-block mt-2 px-4 py-1.5 rounded-xl bg-emerald-600 text-white text-xs font-bold shadow-xs">
                        + প্রথম অর্ডার নিন
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($recentOrders as $order): ?>
                    <a href="<?= $base ?>/agent/orders/show?id=<?= $order['id'] ?>" class="p-3.5 flex items-center justify-between hover:bg-emerald-50/30 transition-colors block">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-gray-100 text-gray-700 flex items-center justify-center font-mono font-black text-xs shrink-0">
                                #<?= $order['id'] ?>
                            </div>
                            <div>
                                <div class="text-xs font-black text-gray-900 flex items-center gap-1.5">
                                    <span><?= htmlspecialchars($order['customer_name']) ?></span>
                                    <span class="text-[10px] font-mono text-gray-400">(<?= htmlspecialchars($order['customer_phone']) ?>)</span>
                                </div>
                                <div class="text-[11px] text-gray-500 flex items-center gap-2 mt-0.5">
                                    <span><?= htmlspecialchars($order['area_name'] ?? 'এরিয়া') ?></span>
                                    <span>•</span>
                                    <span><?= count($order['items'] ?? []) ?> টি আইটেম</span>
                                </div>
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            <div class="text-xs font-black text-gray-900">৳<?= number_format($order['total_amount'], 2) ?></div>
                            <?php
                            $status = $order['status'];
                            $badgeClass = 'bg-gray-100 text-gray-700';
                            $statusText = 'পেন্ডিং';
                            if ($status === 'pending') { $badgeClass = 'bg-amber-100 text-amber-800'; $statusText = 'পেন্ডিং'; }
                            elseif ($status === 'processing') { $badgeClass = 'bg-blue-100 text-blue-800'; $statusText = 'প্রসেসিং'; }
                            elseif ($status === 'out_for_delivery') { $badgeClass = 'bg-purple-100 text-purple-800'; $statusText = 'অন-ওয়ে'; }
                            elseif ($status === 'delivered') { $badgeClass = 'bg-emerald-100 text-emerald-800'; $statusText = 'ডেলিভার্ড'; }
                            elseif ($status === 'cancelled') { $badgeClass = 'bg-red-100 text-red-800'; $statusText = 'বাতিল'; }
                            ?>
                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold <?= $badgeClass ?> mt-0.5">
                                <?= $statusText ?>
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</div>
