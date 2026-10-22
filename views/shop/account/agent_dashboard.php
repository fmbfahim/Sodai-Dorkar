<?php
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
$assignedAreaNames = !empty($assignedAreas) ? implode(', ', array_column($assignedAreas, 'name')) : 'সকল উন্মুক্ত এরিয়া (Open Area)';
?>

<div class="space-y-6">

    <!-- ══════════════════════════════════════════════════════════ -->
    <!--  AGENT HERO & PROFILE BANNER                                -->
    <!-- ══════════════════════════════════════════════════════════ -->
    <div class="bg-gradient-to-br from-emerald-800 via-teal-800 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden border border-emerald-500/30">
        <div class="absolute -right-10 -top-10 w-48 h-48 bg-emerald-400/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute right-12 -bottom-10 w-36 h-36 bg-teal-400/10 rounded-full blur-xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-5 border-b border-white/10 pb-6">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 text-[11px] font-black uppercase tracking-wider">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>সক্রিয় ফিল্ড সেলস এজেন্ট</span>
                    </span>
                    <span class="text-xs text-emerald-200/80 font-mono">@<?= htmlspecialchars($agent['username'] ?? 'agent') ?></span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center gap-2">
                    <span>ফিল্ড এজেন্ট হাব ও পারফর্মেন্স</span>
                    <ion-icon name="briefcase" class="text-amber-400 text-2xl"></ion-icon>
                </h1>
                <div class="text-xs text-emerald-100/90 mt-2 flex items-center gap-2 flex-wrap">
                    <span class="flex items-center gap-1 text-amber-300 font-bold">
                        <ion-icon name="location"></ion-icon>
                        কর্ম-এরিয়া:
                    </span>
                    <span class="bg-white/10 px-2.5 py-1 rounded-lg font-semibold text-white border border-white/10">
                        <?= htmlspecialchars($assignedAreaNames) ?>
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap">
                <a href="<?= $base ?>/account/agent/shop" class="px-5 py-2.5 rounded-2xl bg-white hover:bg-emerald-50 text-emerald-950 font-black text-xs transition-all shadow-md flex items-center gap-2 shrink-0 active:scale-95">
                    <ion-icon name="cart-outline" class="text-base text-emerald-700"></ion-icon>
                    <span>নতুন অর্ডার নিন</span>
                </a>
                <a href="<?= $base ?>/agent/dashboard" target="_blank" class="px-4 py-2.5 rounded-2xl bg-white/15 hover:bg-white/25 text-white font-bold text-xs transition-all border border-white/20 flex items-center gap-1.5 shrink-0" title="মোবাইল ইন্টারফেসে খুলুন">
                    <ion-icon name="phone-portrait-outline" class="text-base"></ion-icon>
                    <span class="hidden sm:inline">মোবাইল অ্যাপ ভিউ</span>
                </a>
            </div>
        </div>

        <!-- Metric KPI Cards Grid -->
        <div class="relative z-10 grid grid-cols-2 sm:grid-cols-4 gap-3.5 mt-6">
            <!-- Today Orders -->
            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/10">
                <div class="text-[11px] font-bold text-emerald-200 uppercase tracking-wider">আজকের অর্ডার</div>
                <div class="text-2xl sm:text-3xl font-black text-white mt-1"><?= (int)($stats['today_orders'] ?? 0) ?> <span class="text-xs font-normal text-emerald-300">টি</span></div>
                <div class="text-[10px] text-emerald-300 font-medium mt-0.5">৳<?= number_format((float)($stats['today_sales'] ?? 0), 0) ?> সেলস</div>
            </div>

            <!-- Total Orders -->
            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/10">
                <div class="text-[11px] font-bold text-emerald-200 uppercase tracking-wider">মোট সংগৃহীত অর্ডার</div>
                <div class="text-2xl sm:text-3xl font-black text-white mt-1"><?= (int)($stats['total_orders'] ?? 0) ?> <span class="text-xs font-normal text-emerald-300">টি</span></div>
                <div class="text-[10px] text-emerald-300 font-medium mt-0.5">ফিল্ড কালেকশন</div>
            </div>

            <!-- Total Sales -->
            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/10">
                <div class="text-[11px] font-bold text-emerald-200 uppercase tracking-wider">মোট সেলস ভ্যালু</div>
                <div class="text-2xl sm:text-3xl font-black text-amber-300 mt-1">৳<?= number_format((float)($stats['total_sales'] ?? 0), 0) ?></div>
                <div class="text-[10px] text-emerald-300 font-medium mt-0.5">জেনারেটেড ভ্যালু</div>
            </div>

            <!-- Delivered Orders -->
            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/10">
                <div class="text-[11px] font-bold text-emerald-200 uppercase tracking-wider">ডেলিভারি সম্পন্ন</div>
                <div class="text-2xl sm:text-3xl font-black text-emerald-300 mt-1"><?= (int)($stats['delivered_orders'] ?? 0) ?> <span class="text-xs font-normal text-emerald-200">টি</span></div>
                <div class="text-[10px] text-emerald-300 font-medium mt-0.5">সফল ডেলিভারি</div>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════ -->
    <!--  PRIMARY AGENT WORKFLOW TILES                              -->
    <!-- ══════════════════════════════════════════════════════════ -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Catalog & Take Order -->
        <a href="<?= $base ?>/account/agent/shop" class="bg-white hover:bg-emerald-50/50 p-5 rounded-2xl border border-gray-200/90 shadow-2xs hover:border-emerald-300 transition-all flex flex-col justify-between group">
            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center text-2xl font-bold mb-3 group-hover:scale-110 transition-transform shadow-xs">
                <ion-icon name="storefront"></ion-icon>
            </div>
            <div>
                <h3 class="text-sm font-black text-gray-900 group-hover:text-emerald-700 transition-colors">পণ্য ক্যাটালগ ও অর্ডার নিন</h3>
                <p class="text-xs text-gray-500 mt-1">কাস্টমারের জন্য পণ্য পছন্দ করে কার্টে যুক্ত করুন</p>
            </div>
            <div class="mt-4 text-xs font-bold text-emerald-600 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                <span>অর্ডার শুরু করুন</span>
                <ion-icon name="arrow-forward"></ion-icon>
            </div>
        </a>

        <!-- 2. Customer Checkout -->
        <a href="<?= $base ?>/account/agent/checkout" class="bg-white hover:bg-emerald-50/50 p-5 rounded-2xl border border-gray-200/90 shadow-2xs hover:border-emerald-300 transition-all flex flex-col justify-between group">
            <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-2xl font-bold mb-3 group-hover:scale-110 transition-transform shadow-xs">
                <ion-icon name="cart"></ion-icon>
            </div>
            <div>
                <h3 class="text-sm font-black text-gray-900 group-hover:text-emerald-700 transition-colors">কাস্টমার নির্বাচন ও চেকআউট</h3>
                <p class="text-xs text-gray-500 mt-1">কাস্টমার সিলেক্ট বা নতুন কাস্টমার এন্ট্রি দিয়ে অর্ডার নিশ্চিত করুন</p>
            </div>
            <div class="mt-4 text-xs font-bold text-emerald-600 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                <span>চেকআউট করুন</span>
                <ion-icon name="arrow-forward"></ion-icon>
            </div>
        </a>

        <!-- 3. Agent Orders -->
        <a href="<?= $base ?>/account/agent/orders" class="bg-white hover:bg-blue-50/50 p-5 rounded-2xl border border-gray-200/90 shadow-2xs hover:border-blue-300 transition-all flex flex-col justify-between group">
            <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-800 flex items-center justify-center text-2xl font-bold mb-3 group-hover:scale-110 transition-transform shadow-xs">
                <ion-icon name="receipt"></ion-icon>
            </div>
            <div>
                <h3 class="text-sm font-black text-gray-900 group-hover:text-blue-700 transition-colors">আমার সংগৃহীত অর্ডারসমূহ</h3>
                <p class="text-xs text-gray-500 mt-1">আপনার সকল অর্ডারের লাইভ ডেলিভারি ও পেমেন্ট স্ট্যাটাস</p>
            </div>
            <div class="mt-4 text-xs font-bold text-blue-600 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                <span>অর্ডার তালিকা দেখুন</span>
                <ion-icon name="arrow-forward"></ion-icon>
            </div>
        </a>

        <!-- 4. Area Customers -->
        <a href="<?= $base ?>/account/agent/customers" class="bg-white hover:bg-purple-50/50 p-5 rounded-2xl border border-gray-200/90 shadow-2xs hover:border-purple-300 transition-all flex flex-col justify-between group">
            <div class="w-12 h-12 rounded-2xl bg-purple-100 text-purple-800 flex items-center justify-center text-2xl font-bold mb-3 group-hover:scale-110 transition-transform shadow-xs">
                <ion-icon name="people"></ion-icon>
            </div>
            <div>
                <h3 class="text-sm font-black text-gray-900 group-hover:text-purple-700 transition-colors">এলাকার কাস্টমার তালিকা</h3>
                <p class="text-xs text-gray-500 mt-1">আপনার কর্ম-এরিয়ার কাস্টমার তথ্য ও এক-ক্লিকে অর্ডার</p>
            </div>
            <div class="mt-4 text-xs font-bold text-purple-600 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                <span>কাস্টমার দেখুন</span>
                <ion-icon name="arrow-forward"></ion-icon>
            </div>
        </a>
    </div>

    <!-- ══════════════════════════════════════════════════════════ -->
    <!--  RECENT ORDERS TAKEN BY AGENT                              -->
    <!-- ══════════════════════════════════════════════════════════ -->
    <div class="bg-white rounded-3xl p-6 border border-gray-200/90 shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-4">
            <div>
                <h2 class="text-base font-black text-gray-900 flex items-center gap-2">
                    <ion-icon name="time-outline" class="text-emerald-600 text-lg"></ion-icon>
                    <span>সম্প্রতি গৃহীত ফিল্ড অর্ডারসমূহ</span>
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">আপনার গৃহীত সর্বশেষ ১০টি অর্ডারের বিবরণ</p>
            </div>
            <a href="<?= $base ?>/account/agent/orders" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
                <span>সবগুলো দেখুন</span>
                <ion-icon name="chevron-forward"></ion-icon>
            </a>
        </div>

        <?php if (empty($recentOrders)): ?>
            <div class="py-12 text-center text-gray-400 space-y-2">
                <ion-icon name="receipt-outline" class="text-4xl text-gray-300"></ion-icon>
                <p class="text-xs font-bold text-gray-600">এখনও কোনো অর্ডার নেওয়া হয়নি</p>
                <p class="text-[11px] text-gray-400">কাস্টমারদের জন্য পণ্য নির্বাচন করে প্রথম অর্ডারটি সংগ্রহ করুন।</p>
                <a href="<?= $base ?>/account/agent/shop" class="inline-flex items-center gap-1.5 mt-3 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs transition-colors">
                    <ion-icon name="cart-outline"></ion-icon>
                    <span>অর্ডার নেওয়া শুরু করুন</span>
                </a>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-gray-400 font-bold border-b border-gray-100 text-[11px]">
                            <th class="pb-3 pl-2">অর্ডার নং</th>
                            <th class="pb-3">তারিখ ও সময়</th>
                            <th class="pb-3">কাস্টমার</th>
                            <th class="pb-3">এরিয়া</th>
                            <th class="pb-3 text-right">মূল্য</th>
                            <th class="pb-3 text-center">স্ট্যাটাস</th>
                            <th class="pb-3 text-right pr-2">একশন</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($recentOrders as $ro): 
                            $st = $ro['status'];
                            $badge = 'bg-gray-100 text-gray-700';
                            $lbl = 'পেন্ডিং';
                            if ($st === 'pending') { $badge = 'bg-amber-100 text-amber-800'; $lbl = 'পেন্ডিং'; }
                            elseif ($st === 'confirmed' || $st === 'processing') { $badge = 'bg-blue-100 text-blue-800'; $lbl = 'প্রসেসিং'; }
                            elseif ($st === 'out_for_delivery') { $badge = 'bg-purple-100 text-purple-800'; $lbl = 'অন-ওয়ে'; }
                            elseif ($st === 'delivered') { $badge = 'bg-emerald-100 text-emerald-800'; $lbl = 'ডেলিভার্ড'; }
                            elseif ($st === 'cancelled') { $badge = 'bg-rose-100 text-rose-800'; $lbl = 'বাতিল'; }
                        ?>
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <td class="py-3 pl-2 font-mono font-bold text-gray-900">#<?= $ro['id'] ?></td>
                                <td class="py-3 text-gray-500 font-mono"><?= date('d M Y, h:i A', strtotime($ro['created_at'])) ?></td>
                                <td class="py-3">
                                    <div class="font-bold text-gray-900"><?= htmlspecialchars($ro['customer_name'] ?? 'Customer') ?></div>
                                    <div class="text-[10px] text-gray-400 font-mono"><?= htmlspecialchars($ro['customer_phone'] ?? '') ?></div>
                                </td>
                                <td class="py-3 text-gray-600"><?= htmlspecialchars($ro['area_name'] ?? '—') ?></td>
                                <td class="py-3 text-right font-black text-gray-900">৳<?= number_format((float)$ro['total_amount'], 2) ?></td>
                                <td class="py-3 text-center">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-black <?= $badge ?>">
                                        <?= $lbl ?>
                                    </span>
                                </td>
                                <td class="py-3 pr-2 text-right">
                                    <a href="<?= $base ?>/account/agent/order-detail?id=<?= $ro['id'] ?>" class="px-3 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 rounded-lg text-[11px] font-bold border border-emerald-200 transition-colors">
                                        ইনভয়েস
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

</div>
