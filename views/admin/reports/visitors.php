<?php 
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
$currentRange = $currentRange ?? '24h';
$visitors = $stats['visitors'] ?? [];
?>

<div class="space-y-6">

    <!-- Top Executive Header Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-2xl p-6 lg:p-8 text-white shadow-lg relative overflow-hidden border border-slate-800">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold tracking-wider text-emerald-300 bg-emerald-500/20 border border-emerald-400/30">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Live Visitor Intelligence & Audit Trail</span>
                </div>
                <h1 class="text-2xl lg:text-3xl font-black tracking-tight text-white">
                    Visitor Report & Activity Logs (ভিজিটর রিপোর্ট ও কার্যকলাপ)
                </h1>
                <p class="text-slate-300 text-sm max-w-2xl leading-relaxed">
                    আপনার ওয়েবসাইটে কোন আইপি (IP) থেকে কোন ভিজিটর প্রবেশ করছে, তারা কোন পেজে গেছে, কোন পণ্য বা ক্যাটাগরি ব্রাউজ করেছে এবং কার্টে কি কি অ্যাড করেছে তার সম্পূর্ণ টাইমলাইন সরাসরি পর্যবেক্ষণ করুন।
                </p>
            </div>
            <div class="flex items-center gap-4 bg-white/10 backdrop-blur-md px-5 py-3.5 rounded-2xl border border-white/15">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-2xl">
                    <ion-icon name="radio-outline" class="animate-pulse"></ion-icon>
                </div>
                <div>
                    <div class="text-[11px] font-bold text-slate-300 uppercase tracking-wider">Active Right Now</div>
                    <div class="text-2xl font-black text-emerald-400 flex items-center gap-2">
                        <span><?= $stats['active_now'] ?? 0 ?></span>
                        <span class="text-xs font-normal text-slate-300">Live Browsing</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Timeframe Filters -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-wrap items-center justify-between gap-4">
        <!-- Quick Preset Pills -->
        <div class="flex flex-wrap items-center gap-2">
            <a href="?range=1h" class="px-4 py-2 text-xs font-bold rounded-xl transition-all <?= ($currentRange === '1h') ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' ?>">
                ⚡ Last 1 Hour (১ ঘন্টা)
            </a>
            <a href="?range=24h" class="px-4 py-2 text-xs font-bold rounded-xl transition-all <?= ($currentRange === '24h') ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' ?>">
                📅 Last 24 Hours (২৪ ঘন্টা)
            </a>
            <a href="?range=7d" class="px-4 py-2 text-xs font-bold rounded-xl transition-all <?= ($currentRange === '7d') ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' ?>">
                📆 Last 7 Days (৭ দিন)
            </a>
            <a href="?range=30d" class="px-4 py-2 text-xs font-bold rounded-xl transition-all <?= ($currentRange === '30d') ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' ?>">
                🗓️ Last 30 Days (৩০ দিন)
            </a>
        </div>

        <!-- Custom Date Range Form -->
        <form method="GET" action="" class="flex flex-wrap items-center gap-2">
            <input type="hidden" name="range" value="custom">
            <span class="text-xs font-bold text-slate-500 uppercase">Custom Range:</span>
            <input type="date" name="from" value="<?= htmlspecialchars($from ?? date('Y-m-d', strtotime('-3 days'))) ?>" class="text-xs border border-slate-300 rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-indigo-500">
            <span class="text-xs text-slate-400">to</span>
            <input type="date" name="to" value="<?= htmlspecialchars($to ?? date('Y-m-d')) ?>" class="text-xs border border-slate-300 rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-indigo-500">
            <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition-colors shadow-xs">
                Filter
            </button>
        </form>
    </div>

    <!-- 4 KPI Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Metric 1: Unique Visitors -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Unique Visitors</span>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                    <ion-icon name="people-outline"></ion-icon>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-3xl font-black text-slate-900"><?= number_format($stats['unique_visitors'] ?? 0) ?></h3>
                <p class="text-xs text-indigo-600 font-medium mt-1">In selected timeframe</p>
            </div>
        </div>

        <!-- Metric 2: Page Views -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Pageviews</span>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                    <ion-icon name="eye-outline"></ion-icon>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-3xl font-black text-blue-700"><?= number_format($stats['total_page_views'] ?? 0) ?></h3>
                <p class="text-xs text-slate-500 font-medium mt-1">Total page impressions</p>
            </div>
        </div>

        <!-- Metric 3: Cart Additions -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Cart Add Actions</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                    <ion-icon name="cart-outline"></ion-icon>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-3xl font-black text-amber-700"><?= number_format($stats['cart_adds'] ?? 0) ?></h3>
                <p class="text-xs text-amber-600 font-medium mt-1">High purchase intent</p>
            </div>
        </div>

        <!-- Metric 4: Orders Converted -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Order Conversions</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                    <ion-icon name="checkmark-done-circle-outline"></ion-icon>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-3xl font-black text-emerald-700"><?= number_format($stats['total_orders'] ?? 0) ?></h3>
                <p class="text-xs text-emerald-600 font-medium mt-1">Successfully completed</p>
            </div>
        </div>
    </div>

    <!-- Visitors IP & Activity Table -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex flex-wrap items-center justify-between gap-4 bg-slate-50/50">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Visitor Logs by IP (ভিজিটর তালিকা ও আইপি অডিট)</h3>
                <p class="text-xs text-slate-500">Click "কি কি করেছে (Activity Log)" to inspect all actions taken by each IP.</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-xs text-slate-500 flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 font-bold text-slate-700">
                        <ion-icon name="phone-portrait-outline" class="text-sm"></ion-icon>
                        Mobile: <?= $stats['devices']['mobile'] ?? 0 ?>
                    </span>
                    <span>&bull;</span>
                    <span class="inline-flex items-center gap-1 font-bold text-slate-700">
                        <ion-icon name="desktop-outline" class="text-sm"></ion-icon>
                        Desktop: <?= $stats['devices']['desktop'] ?? 0 ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs font-bold uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Visitor & IP Address</th>
                        <th class="px-6 py-4">Device & Browser</th>
                        <th class="px-6 py-4">Customer Info</th>
                        <th class="px-6 py-4">Current / Last Page</th>
                        <th class="px-6 py-4">Cart Status</th>
                        <th class="px-6 py-4">Last Active</th>
                        <th class="px-6 py-4 text-right">Activity Audit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($visitors)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <ion-icon name="analytics-outline" class="text-5xl text-slate-300 mx-auto mb-2 block"></ion-icon>
                                <span>এই সময়সীমায় কোনো ভিজিটর লগ পাওয়া যায়নি। (No visitor logs recorded for this period)</span>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($visitors as $v): ?>
                            <tr class="hover:bg-indigo-50/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <?php if (!empty($v['is_online'])): ?>
                                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping" title="Online Right Now"></span>
                                        <?php else: ?>
                                            <span class="w-2 h-2 rounded-full bg-slate-300" title="Offline / Idle"></span>
                                        <?php endif; ?>
                                        <span class="font-mono font-bold text-slate-900 text-sm">
                                            <?= htmlspecialchars($v['ip_address']) ?>
                                        </span>
                                        <?php if (!empty($v['is_online'])): ?>
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-black bg-emerald-100 text-emerald-800">
                                                ONLINE
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-1 flex items-center gap-2">
                                        <span>First seen: <?= date('d M, h:i A', strtotime($v['first_seen'])) ?></span>
                                        <span>&bull;</span>
                                        <span><?= $v['page_views'] ?> views</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-700">
                                        <?php if ($v['device_type'] === 'mobile'): ?>
                                            <ion-icon name="phone-portrait-outline" class="text-base text-indigo-500"></ion-icon>
                                        <?php elseif ($v['device_type'] === 'tablet'): ?>
                                            <ion-icon name="tablet-portrait-outline" class="text-base text-purple-500"></ion-icon>
                                        <?php else: ?>
                                            <ion-icon name="desktop-outline" class="text-base text-slate-500"></ion-icon>
                                        <?php endif; ?>
                                        <span><?= htmlspecialchars(ucfirst($v['device_type'])) ?></span>
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        <?= htmlspecialchars(($v['browser'] ?? 'Browser') . ' (' . ($v['platform'] ?? 'OS') . ')') ?>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <?php if (!empty($v['customer_name']) || !empty($v['customer_phone'])): ?>
                                        <div class="font-bold text-slate-900">
                                            <?= htmlspecialchars($v['customer_name'] ?? 'Customer') ?>
                                        </div>
                                        <div class="text-xs text-slate-500 font-mono mt-0.5">
                                            <?= htmlspecialchars($v['customer_phone'] ?? '') ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 text-slate-500 text-xs font-medium">
                                            <ion-icon name="person-outline"></ion-icon>
                                            <span>Guest Visitor</span>
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td class="px-6 py-4 text-xs font-mono max-w-xs truncate text-slate-600" title="<?= htmlspecialchars($v['current_page'] ?? '') ?>">
                                    <?= htmlspecialchars($v['current_page'] ?? '/') ?>
                                </td>

                                <td class="px-6 py-4">
                                    <?php if (!empty($v['cart_items_count']) && $v['cart_items_count'] > 0): ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                            <ion-icon name="cart"></ion-icon>
                                            <span><?= $v['cart_items_count'] ?> items (৳<?= number_format($v['cart_total'] ?? 0) ?>)</span>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-xs text-slate-400">Empty</span>
                                    <?php endif; ?>
                                    <?php if (!empty($v['has_ordered'])): ?>
                                        <div class="mt-1">
                                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">
                                                ✅ Placed Order
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <td class="px-6 py-4 text-xs text-slate-500">
                                    <div><?= date('h:i:s A', strtotime($v['last_activity_at'])) ?></div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        <?php 
                                        $ago = $v['minutes_ago'] ?? 0;
                                        if ($ago <= 0) echo 'Just now';
                                        elseif ($ago < 60) echo $ago . 'm ago';
                                        elseif ($ago < 1440) echo round($ago / 60) . 'h ago';
                                        else echo round($ago / 1440) . 'd ago';
                                        ?>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <button onclick="openActivityTimeline('<?= htmlspecialchars($v['ip_address']) ?>', '<?= htmlspecialchars($v['session_id']) ?>')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-600 hover:text-white text-indigo-700 text-xs font-bold transition-all shadow-2xs border border-indigo-100">
                                        <ion-icon name="list-outline" class="text-sm"></ion-icon>
                                        <span>কি কি করেছে (Activity)</span>
                                        <span class="px-1.5 py-0.2 rounded-full bg-indigo-200/60 text-indigo-900 text-[10px] ml-0.5 font-mono">
                                            <?= $v['activities_count'] ?? 1 ?>
                                        </span>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Activity Timeline ("কি কি করেছে") -->
<div id="actModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl overflow-hidden border border-slate-200 animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-xl">
                    <ion-icon name="footsteps-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-white text-base">Visitor Activity Audit Trail (কার্যকলাপের বিবরণ)</h3>
                    <p class="text-xs text-slate-300 font-mono" id="actModalSubtitle">IP: Loading...</p>
                </div>
            </div>
            <button onclick="closeActModal()" class="w-8 h-8 rounded-full hover:bg-white/20 flex items-center justify-center text-slate-300">
                <ion-icon name="close" class="text-xl"></ion-icon>
            </button>
        </div>

        <div class="p-6 max-h-[70vh] overflow-y-auto" id="actModalBody">
            <div class="flex items-center justify-center py-12 text-slate-400 gap-2">
                <ion-icon name="sync-outline" class="animate-spin text-2xl"></ion-icon>
                <span>অ্যাক্টিভিটি হিস্ট্রি লোড হচ্ছে...</span>
            </div>
        </div>

        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-end">
            <button onclick="closeActModal()" class="px-4 py-2 rounded-xl text-sm font-bold bg-slate-800 text-white hover:bg-slate-900 transition-colors shadow-xs">
                বন্ধ করুন (Close)
            </button>
        </div>
    </div>
</div>

<script>
function openActivityTimeline(ip, sessionId) {
    document.getElementById('actModalSubtitle').innerText = 'IP Address: ' + ip;
    document.getElementById('actModalBody').innerHTML = `
        <div class="flex items-center justify-center py-12 text-slate-400 gap-2">
            <ion-icon name="sync-outline" class="animate-spin text-2xl text-indigo-600"></ion-icon>
            <span class="text-sm font-medium">অ্যাক্টিভিটি হিস্ট্রি ফেচ করা হচ্ছে...</span>
        </div>
    `;
    document.getElementById('actModal').classList.remove('hidden');

    const base = '<?= $base ?>';
    fetch(`${base}/admin/api/visitor-activity?ip=${encodeURIComponent(ip)}&session_id=${encodeURIComponent(sessionId)}`)
        .then(res => res.json())
        .then(res => {
            const body = document.getElementById('actModalBody');
            if (!res.activities || res.activities.length === 0) {
                body.innerHTML = '<div class="py-12 text-center text-slate-400 text-sm">কোনো অ্যাক্টিভিটি রেকর্ড পাওয়া যায়নি।</div>';
                return;
            }

            let html = '<div class="relative pl-6 border-l-2 border-indigo-100 space-y-6 my-2">';
            res.activities.forEach((act, idx) => {
                let badgeColor = 'bg-slate-100 text-slate-700';
                let icon = 'document-text-outline';
                let dotColor = 'bg-indigo-400';

                if (act.action_type === 'add_to_cart') {
                    badgeColor = 'bg-amber-100 text-amber-800 font-bold';
                    icon = 'cart';
                    dotColor = 'bg-amber-500 ring-4 ring-amber-100';
                } else if (act.action_type === 'place_order') {
                    badgeColor = 'bg-emerald-100 text-emerald-800 font-bold';
                    icon = 'checkmark-circle';
                    dotColor = 'bg-emerald-500 ring-4 ring-emerald-100';
                } else if (act.action_type === 'checkout_view') {
                    badgeColor = 'bg-blue-100 text-blue-800 font-bold';
                    icon = 'card-outline';
                    dotColor = 'bg-blue-500 ring-4 ring-blue-100';
                } else if (act.action_type === 'view_product') {
                    badgeColor = 'bg-purple-100 text-purple-800';
                    icon = 'cube-outline';
                } else if (act.action_type === 'view_category') {
                    badgeColor = 'bg-teal-100 text-teal-800';
                    icon = 'folder-open-outline';
                } else if (act.action_type === 'search') {
                    badgeColor = 'bg-sky-100 text-sky-800';
                    icon = 'search-outline';
                }

                html += `
                    <div class="relative group">
                        <div class="absolute -left-[31px] top-1 w-3.5 h-3.5 rounded-full ${dotColor} transition-transform group-hover:scale-125"></div>
                        <div class="bg-slate-50 hover:bg-slate-100/80 p-3.5 rounded-2xl border border-slate-200/80 transition-all">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-semibold ${badgeColor}">
                                    <ion-icon name="${icon}"></ion-icon>
                                    <span>${act.action_type.replace('_', ' ').toUpperCase()}</span>
                                </span>
                                <span class="text-[11px] text-slate-400 font-mono">${act.created_at}</span>
                            </div>
                            <div class="text-sm font-bold text-slate-800 mt-1">
                                ${act.description}
                            </div>
                            ${act.page_url ? `
                                <div class="text-[11px] font-mono text-slate-400 mt-1 truncate" title="${act.page_url}">
                                    URL: ${act.page_url}
                                </div>
                            ` : ''}
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            body.innerHTML = html;
        })
        .catch(err => {
            document.getElementById('actModalBody').innerHTML = `
                <div class="py-8 text-center text-rose-600 text-sm">
                    ডাটা ফেচ করতে সমস্যা হয়েছে: ${err.message}
                </div>
            `;
        });
}

function closeActModal() {
    document.getElementById('actModal').classList.add('hidden');
}

window.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeActModal();
});
</script>
