<?php
$title = "কুপন ব্যবহারের হিস্ট্রি ও অডিট লগ";
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
$filterCoupon = trim($_GET['coupon'] ?? '');

ob_start();
?>

<div class="space-y-6" x-data="{ searchQuery: '<?= htmlspecialchars($filterCoupon) ?>' }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-secondary-200/80 shadow-xs">
        <div class="flex items-center gap-3">
            <a href="<?= $base ?>/admin/marketing/coupons" class="w-10 h-10 rounded-xl bg-secondary-100 hover:bg-secondary-200 text-secondary-700 flex items-center justify-center transition-colors">
                <ion-icon name="arrow-back" class="text-xl"></ion-icon>
            </a>
            <div>
                <h1 class="text-2xl font-black text-secondary-900 tracking-tight">কুপন রিডেম্পশন হিস্ট্রি</h1>
                <p class="text-xs text-secondary-500 font-medium mt-0.5">কোন কোন অর্ডারে কোন গ্রাহক কী পরিমাণ ডিসকাউন্ট নিয়েছেন তার বিস্তারিত লগ।</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="<?= $base ?>/admin/marketing/coupons" class="px-4 py-2.5 rounded-xl border border-secondary-200 text-secondary-700 text-xs font-bold hover:bg-secondary-50 flex items-center gap-2 transition-colors">
                <ion-icon name="pricetag-outline" class="text-base text-emerald-600"></ion-icon>
                <span>কুপন তালিকা</span>
            </a>
            <a href="<?= $base ?>/admin/marketing/coupons/create" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold flex items-center gap-2 transition-all shadow-xs">
                <ion-icon name="add-circle" class="text-base"></ion-icon>
                <span>নতুন কুপন</span>
            </a>
        </div>
    </div>

    <!-- Quick Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-secondary-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl shrink-0">
                <ion-icon name="receipt-outline"></ion-icon>
            </div>
            <div>
                <span class="text-xs font-bold text-secondary-500 uppercase tracking-wider block">মোট রিডেম্পশন</span>
                <span class="text-2xl font-black text-secondary-900"><?= number_format($stats['total_redemptions'] ?? 0) ?> বার</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-secondary-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl shrink-0">
                <ion-icon name="wallet-outline"></ion-icon>
            </div>
            <div>
                <span class="text-xs font-bold text-secondary-500 uppercase tracking-wider block">মোট ডিসকাউন্ট দেওয়া হয়েছে</span>
                <span class="text-2xl font-black text-emerald-700">৳<?= number_format($stats['total_savings'] ?? 0, 2) ?></span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-secondary-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl shrink-0">
                <ion-icon name="pricetag-outline"></ion-icon>
            </div>
            <div>
                <span class="text-xs font-bold text-secondary-500 uppercase tracking-wider block">সক্রিয় কুপন কোড</span>
                <span class="text-2xl font-black text-secondary-900"><?= number_format($stats['active'] ?? 0) ?> টি</span>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-secondary-200/80 shadow-xs flex items-center gap-3">
        <div class="relative flex-1">
            <ion-icon name="search-outline" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-secondary-400 text-lg"></ion-icon>
            <input type="text" x-model="searchQuery" placeholder="কুপন কোড, কাস্টমার নাম বা ফোন নাম্বার দিয়ে খুঁজুন..."
                class="w-full pl-10 pr-4 py-2 rounded-xl border border-secondary-200 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
        </div>
        <button type="button" @click="searchQuery = ''" x-show="searchQuery" class="px-3 py-2 text-xs font-bold text-secondary-500 hover:text-secondary-800 transition-colors">
            রিসেট
        </button>
    </div>

    <!-- Redemption Logs Table -->
    <div class="bg-white rounded-2xl border border-secondary-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-secondary-50/70 border-b border-secondary-200/70 text-[11px] font-black uppercase text-secondary-500 tracking-wider">
                        <th class="py-3.5 px-4">কুপন কোড</th>
                        <th class="py-3.5 px-4">গ্রাহকের বিবরণ</th>
                        <th class="py-3.5 px-4">অর্ডার বিবরণ</th>
                        <th class="py-3.5 px-4 text-right">ডিসকাউন্ট সেভড</th>
                        <th class="py-3.5 px-4 text-right">ব্যবহারের সময়</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-secondary-100 text-sm">
                    <?php if (empty($usages)): ?>
                        <tr>
                            <td colspan="5" class="py-12 text-center text-secondary-400">
                                <ion-icon name="receipt-outline" class="text-4xl mx-auto block mb-2 text-secondary-300"></ion-icon>
                                <span class="font-bold text-base">এখনো কোনো কুপন ব্যবহার করা হয়নি</span>
                                <p class="text-xs text-secondary-400 mt-1">গ্রাহকরা চেকআউটে কুপন ব্যবহার করলে সাথে সাথে এখানে তালিকাভুক্ত হবে।</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($usages as $usage): ?>
                            <?php
                            $searchHaystack = strtolower(($usage['coupon_code'] ?? '') . ' ' . ($usage['customer_name'] ?? '') . ' ' . ($usage['customer_phone'] ?? '') . ' ' . ($usage['order_number'] ?? ''));
                            ?>
                            <tr class="hover:bg-secondary-50/50 transition-colors"
                                x-show="!searchQuery || '<?= addslashes($searchHaystack) ?>'.includes(searchQuery.toLowerCase())">
                                
                                <!-- Coupon Code -->
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-200 font-mono font-black text-xs text-emerald-800 tracking-wider">
                                            <?= htmlspecialchars($usage['coupon_code'] ?? 'N/A') ?>
                                        </span>
                                    </div>
                                    <?php if (!empty($usage['coupon_title'])): ?>
                                        <span class="text-xs text-secondary-500 block mt-0.5"><?= htmlspecialchars($usage['coupon_title']) ?></span>
                                    <?php endif; ?>
                                </td>

                                <!-- Customer -->
                                <td class="py-3.5 px-4">
                                    <?php if (!empty($usage['customer_name']) || !empty($usage['customer_phone'])): ?>
                                        <div class="font-bold text-secondary-900 text-xs">
                                            <?= htmlspecialchars($usage['customer_name'] ?: 'গেস্ট কাস্টমার') ?>
                                        </div>
                                        <div class="text-[11px] text-secondary-500 font-mono">
                                            <?= htmlspecialchars($usage['customer_phone'] ?: '-') ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-xs text-secondary-400 italic">গেস্ট / অচিহ্নিত</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Order -->
                                <td class="py-3.5 px-4">
                                    <?php if (!empty($usage['order_id'])): ?>
                                        <div class="flex items-center gap-1.5 font-bold text-xs text-secondary-900">
                                            <span>অর্ডার:</span>
                                            <a href="<?= $base ?>/admin/orders/show?id=<?= $usage['order_id'] ?>" class="text-emerald-700 hover:underline font-mono">
                                                #<?= htmlspecialchars($usage['order_number'] ?: $usage['order_id']) ?>
                                            </a>
                                        </div>
                                        <?php if (isset($usage['order_amount'])): ?>
                                            <span class="text-[11px] text-secondary-500 block">মোট মূল্য: ৳<?= number_format((float)$usage['order_amount'], 2) ?></span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-xs text-secondary-400 italic">-</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Discount Amount -->
                                <td class="py-3.5 px-4 text-right">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-black">
                                        -৳<?= number_format((float)($usage['discount_amount'] ?? 0), 2) ?>
                                    </span>
                                </td>

                                <!-- Date / Time -->
                                <td class="py-3.5 px-4 text-right text-xs text-secondary-600 font-medium">
                                    <?= date('d M Y, h:i A', strtotime($usage['used_at'] ?? 'now')) ?>
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
