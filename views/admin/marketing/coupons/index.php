<?php
$title = "কুপন ও ডিসকাউন্ট কোড";
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
ob_start();
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-secondary-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl font-bold shadow-xs">
                    <ion-icon name="pricetag"></ion-icon>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-secondary-900 tracking-tight">মার্কেটিং কুপন ও প্রোমো কোড</h1>
                    <p class="text-xs text-secondary-500 font-medium mt-0.5">গ্রাহকদের জন্য আকর্ষণীয় ডিসকাউন্ট কুপন তৈরি করুন যা চেকআউটে সরাসরি ব্যবহার করা যাবে।</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="<?= $base ?>/admin/marketing/coupons/usages" class="inline-flex items-center gap-2 bg-secondary-100 hover:bg-secondary-200 text-secondary-800 text-xs font-bold px-3.5 py-2.5 rounded-xl transition-all">
                <ion-icon name="time-outline" class="text-base"></ion-icon>
                <span>ব্যবহারের হিস্ট্রি</span>
            </a>
            <a href="<?= $base ?>/admin/marketing/coupons/create" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition-all shadow-sm hover:shadow group">
                <ion-icon name="add-circle" class="text-lg group-hover:scale-110 transition-transform"></ion-icon>
                <span>নতুন কুপন যোগ করুন</span>
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (!empty($success)): ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-3 shadow-xs">
            <ion-icon name="checkmark-circle" class="text-2xl text-emerald-600 shrink-0"></ion-icon>
            <span class="text-sm font-semibold"><?= htmlspecialchars($success) ?></span>
        </div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl flex items-center gap-3 shadow-xs">
            <ion-icon name="alert-circle" class="text-2xl text-rose-600 shrink-0"></ion-icon>
            <span class="text-sm font-semibold"><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <!-- KPI Metrics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Coupons -->
        <div class="bg-white p-4.5 rounded-2xl border border-secondary-200/80 shadow-xs">
            <div class="flex items-center justify-between text-secondary-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider">মোট কুপন</span>
                <span class="p-1.5 rounded-lg bg-secondary-100 text-secondary-600"><ion-icon name="ticket-outline" class="text-base"></ion-icon></span>
            </div>
            <div class="text-2xl font-black text-secondary-900"><?= number_format($stats['total'] ?? 0) ?></div>
            <div class="text-[11px] text-secondary-400 font-medium mt-1">সবগুলো ক্যাম্পেইন</div>
        </div>

        <!-- Active Coupons -->
        <div class="bg-white p-4.5 rounded-2xl border border-secondary-200/80 shadow-xs">
            <div class="flex items-center justify-between text-secondary-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">সক্রিয় কুপন</span>
                <span class="p-1.5 rounded-lg bg-emerald-100 text-emerald-600"><ion-icon name="checkmark-done-circle-outline" class="text-base"></ion-icon></span>
            </div>
            <div class="text-2xl font-black text-emerald-600"><?= number_format($stats['active'] ?? 0) ?></div>
            <div class="text-[11px] text-emerald-600 font-semibold mt-1">বর্তমানে চালু আছে</div>
        </div>

        <!-- Total Redemptions -->
        <div class="bg-white p-4.5 rounded-2xl border border-secondary-200/80 shadow-xs">
            <div class="flex items-center justify-between text-secondary-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-700">মোট ব্যবহার</span>
                <span class="p-1.5 rounded-lg bg-blue-100 text-blue-600"><ion-icon name="bag-check-outline" class="text-base"></ion-icon></span>
            </div>
            <div class="text-2xl font-black text-blue-600"><?= number_format($stats['total_redemptions'] ?? 0) ?> বার</div>
            <div class="text-[11px] text-secondary-400 font-medium mt-1">অর্ডারে সফল রিডিম</div>
        </div>

        <!-- Total Discount Given -->
        <div class="bg-white p-4.5 rounded-2xl border border-secondary-200/80 shadow-xs">
            <div class="flex items-center justify-between text-secondary-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-purple-700">মোট ছাড় দেওয়া হয়েছে</span>
                <span class="p-1.5 rounded-lg bg-purple-100 text-purple-600"><ion-icon name="gift-outline" class="text-base"></ion-icon></span>
            </div>
            <div class="text-2xl font-black text-purple-600">৳<?= number_format($stats['total_savings'] ?? 0, 2) ?></div>
            <div class="text-[11px] text-secondary-400 font-medium mt-1">গ্রাহকদের মোট সঞ্চয়</div>
        </div>
    </div>

    <!-- Filters & Table Section -->
    <div class="bg-white rounded-2xl border border-secondary-200/80 shadow-xs overflow-hidden">
        <!-- Status Tabs Bar -->
        <div class="p-4 border-b border-secondary-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-secondary-50/50">
            <div class="flex items-center gap-1.5 flex-wrap">
                <a href="<?= $base ?>/admin/marketing/coupons" class="px-3.5 py-1.5 text-xs font-bold rounded-lg transition-all <?= empty($currentStatus) ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white text-secondary-600 border border-secondary-200 hover:bg-secondary-50' ?>">
                    সবগুলো (<?= $stats['total'] ?>)
                </a>
                <a href="<?= $base ?>/admin/marketing/coupons?status=active" class="px-3.5 py-1.5 text-xs font-bold rounded-lg transition-all <?= ($currentStatus === 'active') ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white text-secondary-600 border border-secondary-200 hover:bg-secondary-50' ?>">
                    সক্রিয় (<?= $stats['active'] ?>)
                </a>
                <a href="<?= $base ?>/admin/marketing/coupons?status=inactive" class="px-3.5 py-1.5 text-xs font-bold rounded-lg transition-all <?= ($currentStatus === 'inactive') ? 'bg-secondary-800 text-white shadow-xs' : 'bg-white text-secondary-600 border border-secondary-200 hover:bg-secondary-50' ?>">
                    নিষ্ক্রিয়
                </a>
                <a href="<?= $base ?>/admin/marketing/coupons?status=expired" class="px-3.5 py-1.5 text-xs font-bold rounded-lg transition-all <?= ($currentStatus === 'expired') ? 'bg-rose-600 text-white shadow-xs' : 'bg-white text-secondary-600 border border-secondary-200 hover:bg-secondary-50' ?>">
                    মেয়াদ শেষ
                </a>
            </div>

            <!-- Search Box -->
            <form method="GET" action="<?= $base ?>/admin/marketing/coupons" class="flex items-center gap-2">
                <?php if (!empty($currentStatus)): ?>
                    <input type="hidden" name="status" value="<?= htmlspecialchars($currentStatus) ?>">
                <?php endif; ?>
                <div class="relative">
                    <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="কুপন কোড বা নাম দিয়ে খুঁজুন..." 
                           class="w-56 pl-8 pr-3 py-1.5 text-xs bg-white border border-secondary-200 rounded-lg focus:outline-none focus:border-emerald-500 font-medium">
                    <ion-icon name="search-outline" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-secondary-400 text-xs"></ion-icon>
                </div>
                <button type="submit" class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-bold rounded-lg hover:bg-emerald-700 transition-colors">
                    খুঁজুন
                </button>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-secondary-50/70 border-b border-secondary-200/80 text-[11px] uppercase tracking-wider text-secondary-500 font-bold">
                        <th class="py-3.5 px-4">কুপন কোড</th>
                        <th class="py-3.5 px-4">শিরোনাম ও বিবরণ</th>
                        <th class="py-3.5 px-4 text-center">ছাড়ের ধরন ও পরিমাণ</th>
                        <th class="py-3.5 px-4 text-center">শর্ত ও লিমিট</th>
                        <th class="py-3.5 px-4 text-center">ব্যবহার সংখ্যা</th>
                        <th class="py-3.5 px-4 text-center">মেয়াদ</th>
                        <th class="py-3.5 px-4 text-center">স্ট্যাটাস</th>
                        <th class="py-3.5 px-4 text-right">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-secondary-100 font-normal">
                    <?php if (empty($coupons)): ?>
                        <tr>
                            <td colspan="8" class="py-12 text-center text-secondary-400">
                                <div class="w-16 h-16 rounded-2xl bg-secondary-50 text-secondary-300 flex items-center justify-center mx-auto text-3xl mb-3">
                                    <ion-icon name="pricetag-outline"></ion-icon>
                                </div>
                                <p class="text-base font-bold text-secondary-700">কোনো কুপন পাওয়া যায়নি</p>
                                <p class="text-xs text-secondary-400 mt-1">নতুন একটি কুপন তৈরি করে অফার চালু করুন।</p>
                                <a href="<?= $base ?>/admin/marketing/coupons/create" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-emerald-600 text-white text-xs font-bold rounded-xl hover:bg-emerald-700 transition-colors">
                                    <ion-icon name="add"></ion-icon>
                                    <span>নতুন কুপন যোগ করুন</span>
                                </a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($coupons as $c): ?>
                            <tr class="hover:bg-secondary-50/50 transition-colors">
                                <!-- Code with Copy button -->
                                <td class="py-3.5 px-4 align-top">
                                    <div class="flex items-center gap-1.5">
                                        <span class="inline-block px-3 py-1 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 font-mono text-sm font-black tracking-wider shadow-2xs">
                                            <?= htmlspecialchars($c['code']) ?>
                                        </span>
                                        <button type="button" onclick="navigator.clipboard.writeText('<?= htmlspecialchars($c['code']) ?>'); alert('কুপন কোড কপি করা হয়েছে!');" 
                                                class="p-1 rounded text-secondary-400 hover:text-emerald-600 hover:bg-emerald-50 transition-colors cursor-pointer" title="কপি করুন">
                                            <ion-icon name="copy-outline" class="text-base"></ion-icon>
                                        </button>
                                    </div>
                                    <div class="text-[10px] text-secondary-400 mt-1">আইডি: #<?= $c['id'] ?></div>
                                </td>

                                <!-- Title & Description -->
                                <td class="py-3.5 px-4 align-top max-w-xs">
                                    <div class="font-bold text-secondary-900 text-sm">
                                        <?= htmlspecialchars($c['title']) ?>
                                    </div>
                                    <?php if (!empty($c['description'])): ?>
                                        <div class="text-xs text-secondary-500 mt-0.5 line-clamp-2">
                                            <?= htmlspecialchars($c['description']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <!-- Discount Type & Value -->
                                <td class="py-3.5 px-4 align-top text-center">
                                    <?php if ($c['discount_type'] === 'percent'): ?>
                                        <div class="text-sm font-black text-purple-700">
                                            <?= floatval($c['discount_value']) ?>% ছাড়
                                        </div>
                                        <?php if (!empty($c['max_discount_amount'])): ?>
                                            <span class="text-[10px] text-secondary-500 font-medium">
                                                (সর্বোচ্চ ৳<?= number_format($c['max_discount_amount']) ?>)
                                            </span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="text-sm font-black text-emerald-700">
                                            ৳<?= number_format($c['discount_value'], 2) ?> ছাড়
                                        </div>
                                        <span class="text-[10px] text-secondary-400 font-medium">নির্দিষ্ট ছাড়</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Min Order & User Limit -->
                                <td class="py-3.5 px-4 align-top text-center">
                                    <div class="text-xs font-semibold text-secondary-800">
                                        মিনিমাম ৳<?= number_format($c['min_order_amount']) ?>
                                    </div>
                                    <div class="text-[11px] text-secondary-400 mt-0.5">
                                        গ্রাহক প্রতি: <?= $c['usage_limit_per_user'] ?> বার
                                    </div>
                                </td>

                                <!-- Times Used vs Limit -->
                                <td class="py-3.5 px-4 align-top text-center">
                                    <div class="text-xs font-bold text-secondary-900">
                                        <?= number_format($c['times_used']) ?>
                                        <?php if ($c['usage_limit'] !== null): ?>
                                            <span class="text-secondary-400 font-normal">/ <?= number_format($c['usage_limit']) ?></span>
                                        <?php else: ?>
                                            <span class="text-[10px] text-secondary-400 font-normal">(সীমাহীন)</span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($c['usage_limit'] !== null && $c['usage_limit'] > 0): 
                                        $pct = min(100, round(($c['times_used'] / $c['usage_limit']) * 100));
                                    ?>
                                        <div class="w-20 mx-auto bg-secondary-100 rounded-full h-1.5 mt-1 overflow-hidden">
                                            <div class="bg-emerald-600 h-1.5 rounded-full" style="width: <?= $pct ?>%"></div>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <!-- Validity Dates -->
                                <td class="py-3.5 px-4 align-top text-center text-xs">
                                    <?php if (empty($c['start_date']) && empty($c['end_date'])): ?>
                                        <span class="text-secondary-400">সর্বদা সক্রিয়</span>
                                    <?php else: ?>
                                        <?php if (!empty($c['start_date'])): ?>
                                            <div class="text-[11px] text-secondary-500 font-medium">
                                                শুরু: <?= date('d M, Y', strtotime($c['start_date'])) ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($c['end_date'])): ?>
                                            <div class="text-[11px] <?= (strtotime($c['end_date']) < time()) ? 'text-rose-600 font-bold' : 'text-secondary-600' ?>">
                                                শেষ: <?= date('d M, Y', strtotime($c['end_date'])) ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>

                                <!-- Status Toggle -->
                                <td class="py-3.5 px-4 align-top text-center">
                                    <form method="POST" action="<?= $base ?>/admin/marketing/coupons/toggle" class="inline-block">
                                        <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                        <?php if ($c['status'] === 'active'): ?>
                                            <button type="submit" class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 hover:bg-emerald-200 transition-colors flex items-center gap-1 mx-auto cursor-pointer" title="ক্লিক করে নিষ্ক্রিয় করুন">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                <span>সক্রিয় (Active)</span>
                                            </button>
                                        <?php elseif ($c['status'] === 'inactive'): ?>
                                            <button type="submit" class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-secondary-100 text-secondary-700 border border-secondary-300 hover:bg-secondary-200 transition-colors flex items-center gap-1 mx-auto cursor-pointer" title="ক্লিক করে সক্রিয় করুন">
                                                <span>নিষ্ক্রিয় (Inactive)</span>
                                            </button>
                                        <?php else: ?>
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 border border-rose-200 inline-block">
                                                মেয়াদ শেষ
                                            </span>
                                        <?php endif; ?>
                                    </form>
                                </td>

                                <!-- Actions -->
                                <td class="py-3.5 px-4 align-top text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Edit -->
                                        <a href="<?= $base ?>/admin/marketing/coupons/edit?id=<?= $c['id'] ?>" class="p-2 rounded-lg bg-secondary-100 text-secondary-700 hover:bg-secondary-200 transition-colors" title="সম্পাদনা করুন">
                                            <ion-icon name="create-outline" class="text-base"></ion-icon>
                                        </a>

                                        <!-- Delete -->
                                        <form method="POST" action="<?= $base ?>/admin/marketing/coupons/delete" class="inline" onsubmit="return confirm('আপনি কি নিশ্চিত যে এই কুপনটি মুছে ফেলতে চান?');">
                                            <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                            <button type="submit" class="p-2 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-colors cursor-pointer" title="মুছে ফেলুন">
                                                <ion-icon name="trash-outline" class="text-base"></ion-icon>
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
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../../layouts/admin.php';
?>
