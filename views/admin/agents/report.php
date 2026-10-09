<?php
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
?>

<div class="space-y-6">

    <!-- Header & Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-3xl border border-secondary-200 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-primary-600 uppercase tracking-wider mb-1">
                <a href="<?= $base ?>/admin/agents" class="hover:underline flex items-center gap-1">
                    <ion-icon name="arrow-back"></ion-icon>
                    <span>সকল এজেন্ট</span>
                </a>
                <span>/</span>
                <span>এজেন্ট পারফরম্যান্স রিপোর্ট</span>
            </div>
            <h1 class="text-2xl font-black text-secondary-900 tracking-tight flex items-center gap-2">
                <span><?= htmlspecialchars($agent['name']) ?></span>
                <span class="text-xs font-mono font-medium text-secondary-500 bg-secondary-100 px-2 py-0.5 rounded-md">@<?= htmlspecialchars($agent['username']) ?></span>
            </h1>
            <p class="text-xs text-secondary-500 mt-0.5">মোবাইল: <?= htmlspecialchars($agent['phone'] ?: 'N/A') ?> | স্ট্যাটাস: <span class="font-bold <?= ($agent['status'] ?? 'active') === 'active' ? 'text-emerald-600' : 'text-red-600' ?>"><?= ($agent['status'] ?? 'active') === 'active' ? 'সক্রিয়' : 'নিষ্ক্রিয়' ?></span></p>
        </div>

        <div class="flex items-center gap-2">
            <a href="<?= $base ?>/admin/agents" class="px-4 py-2 bg-secondary-100 hover:bg-secondary-200 text-secondary-700 rounded-xl text-xs font-bold transition-colors">
                তালিকায় ফিরুন
            </a>
        </div>
    </div>

    <!-- Assigned Areas Card -->
    <div class="bg-white p-4.5 rounded-2xl border border-secondary-100 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shrink-0">
                <ion-icon name="map"></ion-icon>
            </div>
            <div>
                <span class="text-xs font-bold text-secondary-700">এসাইন করা কর্ম-এরিয়া (Unions):</span>
                <div class="flex flex-wrap gap-1.5 mt-1">
                    <?php if (!empty($assignedAreas)): ?>
                        <?php foreach ($assignedAreas as $area): ?>
                            <span class="px-2.5 py-0.5 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-bold">
                                <?= htmlspecialchars($area['area_name']) ?>
                            </span>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span class="text-xs text-secondary-500 italic">কোনো এরিয়া সীমাবদ্ধ নয় (সার্বজনীন)</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Metrics -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4.5 rounded-2xl border border-secondary-100 shadow-xs">
            <span class="text-[11px] text-secondary-500 font-bold uppercase tracking-wider">আজকের অর্ডার</span>
            <div class="text-2xl font-black text-secondary-900 mt-1"><?= $stats['today_orders'] ?> টি</div>
            <span class="text-[11px] text-emerald-600 font-bold">বিক্রয়: ৳<?= number_format($stats['today_sales'], 2) ?></span>
        </div>

        <div class="bg-white p-4.5 rounded-2xl border border-secondary-100 shadow-xs">
            <span class="text-[11px] text-secondary-500 font-bold uppercase tracking-wider">মোট অর্ডার</span>
            <div class="text-2xl font-black text-secondary-900 mt-1"><?= $stats['total_orders'] ?> টি</div>
            <span class="text-[11px] text-secondary-400">সর্বমোট গৃহীত</span>
        </div>

        <div class="bg-white p-4.5 rounded-2xl border border-secondary-100 shadow-xs">
            <span class="text-[11px] text-secondary-500 font-bold uppercase tracking-wider">মোট বিক্রয় ভ্যালু</span>
            <div class="text-2xl font-black text-emerald-600 mt-1">৳<?= number_format($stats['total_sales'], 2) ?></div>
            <span class="text-[11px] text-secondary-400">ক্যান্সেল ব্যতীত</span>
        </div>

        <div class="bg-white p-4.5 rounded-2xl border border-secondary-100 shadow-xs">
            <span class="text-[11px] text-secondary-500 font-bold uppercase tracking-wider">ডেলিভার্ড ও পেন্ডিং</span>
            <div class="text-xl font-black text-secondary-900 mt-1">
                <span class="text-emerald-600"><?= $stats['delivered_orders'] ?></span> / <span class="text-amber-600"><?= $stats['pending_orders'] ?></span>
            </div>
            <span class="text-[10px] text-secondary-400">সফল ডেলিভারি / অপেক্ষারত</span>
        </div>
    </div>

    <!-- Orders Taken by This Agent -->
    <div class="bg-white rounded-3xl border border-secondary-200 shadow-xs overflow-hidden">
        <div class="p-4.5 border-b border-secondary-100 flex items-center justify-between">
            <h2 class="text-base font-black text-secondary-900 flex items-center gap-2">
                <ion-icon name="receipt-outline" class="text-primary-600"></ion-icon>
                <span>এই এজেন্টের গৃহীত অর্ডারের ইতিহাস (<?= count($orders) ?> টি)</span>
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-secondary-50 text-secondary-600 uppercase font-black tracking-wider text-[11px] border-b border-secondary-200">
                        <th class="py-3 px-4">অর্ডার আইডি</th>
                        <th class="py-3 px-4">তারিখ ও সময়</th>
                        <th class="py-3 px-4">কাস্টমার ও ঠিকানা</th>
                        <th class="py-3 px-4">এরিয়া</th>
                        <th class="py-3 px-4 text-center">পণ্য সংখ্যা</th>
                        <th class="py-3 px-4 text-right">মূল্য</th>
                        <th class="py-3 px-4 text-center">স্ট্যাটাস</th>
                        <th class="py-3 px-4 text-right">ইনভয়েস</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-secondary-100">
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="8" class="py-8 text-center text-secondary-400">
                                এই এজেন্ট কর্তৃক এখনো কোনো অর্ডার গ্রহণ করা হয়নি।
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $ord): ?>
                            <tr class="hover:bg-primary-50/20">
                                <td class="py-3 px-4 font-bold text-secondary-900">#<?= $ord['id'] ?></td>
                                <td class="py-3 px-4 text-secondary-500 font-mono text-[11px]"><?= date('d M Y, h:i A', strtotime($ord['created_at'])) ?></td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-secondary-900"><?= htmlspecialchars($ord['customer_name']) ?></div>
                                    <div class="text-[11px] text-secondary-500"><?= htmlspecialchars($ord['customer_phone']) ?></div>
                                </td>
                                <td class="py-3 px-4 font-semibold text-secondary-700"><?= htmlspecialchars($ord['area_name'] ?? 'N/A') ?></td>
                                <td class="py-3 px-4 text-center font-bold text-secondary-700"><?= count($ord['items'] ?? []) ?> টি</td>
                                <td class="py-3 px-4 text-right font-black text-secondary-900">৳<?= number_format($ord['total_amount'], 2) ?></td>
                                <td class="py-3 px-4 text-center">
                                    <?php
                                    $st = $ord['status'];
                                    $bg = 'bg-gray-100 text-gray-800';
                                    if ($st === 'pending') $bg = 'bg-amber-100 text-amber-800';
                                    elseif ($st === 'delivered') $bg = 'bg-emerald-100 text-emerald-800';
                                    elseif ($st === 'cancelled') $bg = 'bg-red-100 text-red-800';
                                    elseif ($st === 'out_for_delivery') $bg = 'bg-blue-100 text-blue-800';
                                    ?>
                                    <span class="px-2.5 py-0.5 rounded-full font-bold text-[10px] <?= $bg ?>">
                                        <?= ucfirst($st) ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <a href="<?= $base ?>/admin/orders/show?id=<?= $ord['id'] ?>" class="px-3 py-1 bg-secondary-100 hover:bg-secondary-200 text-secondary-800 rounded-lg text-xs font-bold transition-colors">
                                        ভিউ
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
