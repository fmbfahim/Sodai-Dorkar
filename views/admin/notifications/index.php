<?php
$title = "পুশ নোটিফিকেশন ও অফার সেন্টার";
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
$currentType = $_GET['type'] ?? '';
$csrfToken = \Core\CSRF::token();
ob_start();
?>

<div class="space-y-6">
    <!-- Header with Action Buttons -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-secondary-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-emerald-600 to-green-500 text-white flex items-center justify-center text-2xl font-bold shadow-md shadow-emerald-600/20">
                    <ion-icon name="notifications"></ion-icon>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-secondary-900 tracking-tight">পুশ নোটিফিকেশন ও নোটিশ সেন্টার</h1>
                    <p class="text-xs text-secondary-500 font-medium mt-0.5">মোবাইল অ্যাপ ও ওয়েবসাইট গ্রাহকদের জন্য বিশেষ ছাড়, নোটিশ ও অর্ডার আপডেট পাঠান।</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="<?= $base ?>/admin/notifications/create?type=notice" class="inline-flex items-center gap-1.5 bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 text-xs font-bold px-3.5 py-2.5 rounded-xl transition-all shadow-2xs">
                <ion-icon name="megaphone" class="text-base text-amber-600"></ion-icon>
                <span>📢 নোটিশ পাঠান</span>
            </a>
            <a href="<?= $base ?>/admin/notifications/create" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition-all shadow-md hover:shadow-lg group">
                <ion-icon name="add-circle" class="text-lg group-hover:scale-110 transition-transform"></ion-icon>
                <span>নতুন নোটিফিকেশন / অফার</span>
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
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Total -->
        <div class="bg-white p-4.5 rounded-2xl border border-secondary-200/80 shadow-xs">
            <div class="flex items-center justify-between text-secondary-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider">মোট নোটিফিকেশন</span>
                <span class="p-1.5 rounded-lg bg-secondary-100 text-secondary-600"><ion-icon name="layers-outline" class="text-base"></ion-icon></span>
            </div>
            <div class="text-2xl font-black text-secondary-900"><?= number_format($stats['total'] ?? 0) ?></div>
            <div class="text-[11px] text-secondary-400 font-medium mt-1">সবগুলো ক্যাম্পেইন</div>
        </div>

        <!-- Active Offers & Promos -->
        <div class="bg-white p-4.5 rounded-2xl border border-secondary-200/80 shadow-xs">
            <div class="flex items-center justify-between text-secondary-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">সক্রিয় অফার ও নোটিশ</span>
                <span class="p-1.5 rounded-lg bg-emerald-100 text-emerald-600"><ion-icon name="flash-outline" class="text-base"></ion-icon></span>
            </div>
            <div class="text-2xl font-black text-emerald-600"><?= number_format($stats['active'] ?? 0) ?></div>
            <div class="text-[11px] text-emerald-600 font-semibold mt-1">অ্যাপ ও ওয়েবে লাইভ আছে</div>
        </div>

        <!-- Total Views -->
        <div class="bg-white p-4.5 rounded-2xl border border-secondary-200/80 shadow-xs">
            <div class="flex items-center justify-between text-secondary-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-700">মোট ভিউ</span>
                <span class="p-1.5 rounded-lg bg-blue-100 text-blue-600"><ion-icon name="eye-outline" class="text-base"></ion-icon></span>
            </div>
            <div class="text-2xl font-black text-blue-600"><?= number_format($stats['total_views'] ?? 0) ?></div>
            <div class="text-[11px] text-secondary-400 font-medium mt-1">ইমপ্রেশন সংখ্যা</div>
        </div>

        <!-- Clicks & CTR -->
        <div class="bg-white p-4.5 rounded-2xl border border-secondary-200/80 shadow-xs">
            <div class="flex items-center justify-between text-secondary-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-purple-700">ক্লিক ও সিটিআর</span>
                <span class="p-1.5 rounded-lg bg-purple-100 text-purple-600"><ion-icon name="finger-print-outline" class="text-base"></ion-icon></span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-black text-purple-600"><?= number_format($stats['total_clicks'] ?? 0) ?></span>
                <span class="text-xs font-black text-purple-700 bg-purple-50 px-1.5 py-0.5 rounded-md"><?= $stats['ctr'] ?>% CTR</span>
            </div>
            <div class="text-[11px] text-secondary-400 font-medium mt-1">অফার দেখার রূপান্তর</div>
        </div>

        <!-- Device Subscribers -->
        <div class="bg-white p-4.5 rounded-2xl border border-secondary-200/80 shadow-xs col-span-2 lg:col-span-1">
            <div class="flex items-center justify-between text-secondary-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-700">ডিভাইস সাবস্ক্রাইবার</span>
                <span class="p-1.5 rounded-lg bg-amber-100 text-amber-600"><ion-icon name="phone-portrait-outline" class="text-base"></ion-icon></span>
            </div>
            <div class="text-2xl font-black text-amber-600"><?= number_format($stats['subscribers'] ?? 0) ?></div>
            <div class="text-[11px] text-secondary-400 font-medium mt-1">পুশ পারমিশন সক্রিয়</div>
        </div>
    </div>

    <!-- Filters & Table Section -->
    <div class="bg-white rounded-2xl border border-secondary-200/80 shadow-xs overflow-hidden">
        <!-- Tabs Bar -->
        <div class="p-4 border-b border-secondary-100 flex flex-wrap items-center justify-between gap-3 bg-secondary-50/50">
            <div class="flex items-center gap-1.5 flex-wrap">
                <!-- All -->
                <a href="<?= $base ?>/admin/notifications" class="px-3.5 py-1.5 text-xs font-bold rounded-lg transition-all <?= (empty($currentStatus) && empty($currentType)) ? 'bg-secondary-900 text-white shadow-xs' : 'bg-white text-secondary-600 border border-secondary-200 hover:bg-secondary-50' ?>">
                    সবগুলো (<?= $stats['total'] ?>)
                </a>
                
                <!-- Offers -->
                <a href="<?= $base ?>/admin/notifications?type=promo" class="px-3.5 py-1.5 text-xs font-bold rounded-lg transition-all <?= ($currentType === 'promo') ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white text-secondary-600 border border-secondary-200 hover:bg-secondary-50' ?>">
                    🎁 অফার ও প্রোমো
                </a>

                <!-- Notices -->
                <a href="<?= $base ?>/admin/notifications?type=notice" class="px-3.5 py-1.5 text-xs font-bold rounded-lg transition-all <?= ($currentType === 'notice') ? 'bg-amber-600 text-white shadow-xs' : 'bg-white text-secondary-600 border border-secondary-200 hover:bg-secondary-50' ?>">
                    📢 নোটিশ ও ঘোষণা
                </a>

                <!-- Order Updates -->
                <a href="<?= $base ?>/admin/notifications?type=order" class="px-3.5 py-1.5 text-xs font-bold rounded-lg transition-all <?= ($currentType === 'order') ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-secondary-600 border border-secondary-200 hover:bg-secondary-50' ?>">
                    📦 অর্ডার আপডেট
                </a>

                <!-- Active -->
                <a href="<?= $base ?>/admin/notifications?status=active" class="px-3.5 py-1.5 text-xs font-bold rounded-lg transition-all <?= ($currentStatus === 'active') ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white text-secondary-600 border border-secondary-200 hover:bg-secondary-50' ?>">
                    সক্রিয় (<?= $stats['active'] ?>)
                </a>

                <!-- Draft -->
                <a href="<?= $base ?>/admin/notifications?status=draft" class="px-3.5 py-1.5 text-xs font-bold rounded-lg transition-all <?= ($currentStatus === 'draft') ? 'bg-secondary-700 text-white shadow-xs' : 'bg-white text-secondary-600 border border-secondary-200 hover:bg-secondary-50' ?>">
                    ড্রাফট
                </a>
            </div>

            <div class="text-xs text-secondary-500 font-medium">
                মোট <span class="font-bold text-secondary-900"><?= count($notifications) ?></span> টি নোটিফিকেশন
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-secondary-50/70 border-b border-secondary-200/80 text-[11px] uppercase tracking-wider text-secondary-500 font-bold">
                        <th class="py-3.5 px-4">ধরন ও ব্যানার</th>
                        <th class="py-3.5 px-4">শিরোনাম ও মেসেজ</th>
                        <th class="py-3.5 px-4">টার্গেট ও লিংক</th>
                        <th class="py-3.5 px-4 text-center">ভিউ / ক্লিক</th>
                        <th class="py-3.5 px-4 text-center">স্ট্যাটাস</th>
                        <th class="py-3.5 px-4 text-right">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-secondary-100 font-normal">
                    <?php if (empty($notifications)): ?>
                        <tr>
                            <td colspan="6" class="py-12 text-center text-secondary-400">
                                <div class="w-16 h-16 rounded-2xl bg-secondary-50 text-secondary-300 flex items-center justify-center mx-auto text-3xl mb-3">
                                    <ion-icon name="notifications-off-outline"></ion-icon>
                                </div>
                                <p class="text-base font-bold text-secondary-700">কোনো নোটিফিকেশন পাওয়া যায়নি</p>
                                <p class="text-xs text-secondary-400 mt-1">নতুন একটি কাস্টম প্রোমো অফার বা নোটিশ তৈরি করুন</p>
                                <div class="flex items-center justify-center gap-2 mt-4">
                                    <a href="<?= $base ?>/admin/notifications/create" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-xs font-bold rounded-xl hover:bg-emerald-700 transition-colors shadow-xs">
                                        <ion-icon name="add"></ion-icon>
                                        <span>নতুন অফার যোগ করুন</span>
                                    </a>
                                    <a href="<?= $base ?>/admin/notifications/create?type=notice" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 text-white text-xs font-bold rounded-xl hover:bg-amber-600 transition-colors shadow-xs">
                                        <ion-icon name="megaphone"></ion-icon>
                                        <span>জরুরি নোটিশ পাঠান</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($notifications as $n): ?>
                            <?php
                            $notifType = $n['type'] ?? 'promo';
                            $typeBadge = match($notifType) {
                                'notice' => ['label' => '📢 নোটিশ', 'class' => 'bg-amber-100 text-amber-800 border-amber-200'],
                                'order'  => ['label' => '📦 অর্ডার', 'class' => 'bg-blue-100 text-blue-800 border-blue-200'],
                                'offer'  => ['label' => '🎁 অফার', 'class' => 'bg-emerald-100 text-emerald-800 border-emerald-200'],
                                default  => ['label' => '🔥 প্রোমো', 'class' => 'bg-rose-100 text-rose-800 border-rose-200'],
                            };
                            ?>
                            <tr class="hover:bg-secondary-50/50 transition-colors">
                                <!-- Type & Thumbnail -->
                                <td class="py-3.5 px-4 align-top w-28">
                                    <div class="flex flex-col gap-1.5">
                                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold border text-center <?= $typeBadge['class'] ?>">
                                            <?= $typeBadge['label'] ?>
                                        </span>
                                        <?php if (!empty($n['image_url'])): ?>
                                            <div class="w-12 h-12 rounded-xl overflow-hidden bg-secondary-100 border border-secondary-200 shrink-0">
                                                <img src="<?= htmlspecialchars($n['image_url']) ?>" alt="Banner" class="w-full h-full object-cover">
                                            </div>
                                        <?php else: ?>
                                            <div class="w-12 h-12 rounded-xl bg-secondary-100 text-secondary-600 flex items-center justify-center text-xl shrink-0 border border-secondary-200">
                                                <ion-icon name="<?= $notifType === 'notice' ? 'megaphone' : ($notifType === 'order' ? 'cube' : 'gift') ?>-outline"></ion-icon>
                                            </div>
                                        <?php endif; ?>
                                        <span class="text-[10px] text-secondary-400 font-mono">#<?= $n['id'] ?></span>
                                    </div>
                                </td>

                                <!-- Title, Message & Badge -->
                                <td class="py-3.5 px-4 align-top max-w-sm">
                                    <div class="flex items-center gap-1.5 flex-wrap mb-1">
                                        <?php if (!empty($n['badge_text'])): ?>
                                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-secondary-100 text-secondary-700">
                                                <?= htmlspecialchars($n['badge_text']) ?>
                                            </span>
                                        <?php endif; ?>
                                        <span class="text-xs text-secondary-400">
                                            <?= $n['time_ago'] ?? date('d M, Y', strtotime($n['created_at'])) ?>
                                        </span>
                                    </div>
                                    <div class="font-bold text-secondary-900 text-sm leading-snug">
                                        <?= htmlspecialchars($n['title']) ?>
                                    </div>
                                    <div class="text-xs text-secondary-500 mt-1 line-clamp-2 leading-relaxed">
                                        <?= nl2br(htmlspecialchars($n['message'])) ?>
                                    </div>
                                    <?php if (!empty($n['promo_code'])): ?>
                                        <div class="mt-1.5 inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-800 font-mono text-[11px] font-bold">
                                            <span>কুপন: <?= htmlspecialchars($n['promo_code']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <!-- Target Audience & Link -->
                                <td class="py-3.5 px-4 align-top">
                                    <?php
                                    $audMap = [
                                        'all' => ['label' => 'সকল গ্রাহক', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                                        'registered' => ['label' => 'রেজিস্টার্ড ব্যবহারকারী', 'class' => 'bg-purple-50 text-purple-700 border-purple-200'],
                                        'guests' => ['label' => 'গেস্ট ভিজিটর', 'class' => 'bg-secondary-100 text-secondary-700 border-secondary-200'],
                                        'customer' => ['label' => 'নির্দিষ্ট কাস্টমার', 'class' => 'bg-blue-50 text-blue-700 border-blue-200'],
                                    ];
                                    $a = $audMap[$n['target_audience']] ?? $audMap['all'];
                                    ?>
                                    <span class="inline-block px-2.5 py-0.5 rounded-lg text-[10px] font-bold border <?= $a['class'] ?> mb-1">
                                        <?= $a['label'] ?>
                                    </span>
                                    <div class="text-[11px] text-secondary-500 font-medium truncate max-w-[180px]" title="<?= htmlspecialchars($n['action_url']) ?>">
                                        <span class="text-secondary-400">লিংক:</span> <?= htmlspecialchars($n['action_url']) ?>
                                    </div>
                                </td>

                                <!-- Views / Clicks -->
                                <td class="py-3.5 px-4 align-top text-center">
                                    <div class="flex flex-col items-center gap-0.5">
                                        <div class="text-xs font-bold text-secondary-900 flex items-center gap-1">
                                            <ion-icon name="eye" class="text-secondary-400"></ion-icon>
                                            <span><?= number_format($n['views_count']) ?></span>
                                        </div>
                                        <div class="text-[11px] text-purple-700 font-semibold flex items-center gap-1">
                                            <ion-icon name="finger-print"></ion-icon>
                                            <span><?= number_format($n['clicks_count']) ?> ক্লিক</span>
                                        </div>
                                        <?php if ($n['views_count'] > 0): ?>
                                            <span class="text-[10px] text-secondary-400 font-medium">
                                                (<?= round(($n['clicks_count'] / $n['views_count']) * 100, 1) ?>% CTR)
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <!-- Status Toggle (with CSRF Token) -->
                                <td class="py-3.5 px-4 align-top text-center">
                                    <form method="POST" action="<?= $base ?>/admin/notifications/toggle" class="inline-block">
                                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                        <input type="hidden" name="id" value="<?= $n['id'] ?>">
                                        <?php if ($n['status'] === 'active'): ?>
                                            <button type="submit" class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 hover:bg-emerald-200 transition-colors flex items-center gap-1 mx-auto cursor-pointer" title="ক্লিক করে ড্রাফট করুন">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                <span>সক্রিয় (Active)</span>
                                            </button>
                                        <?php elseif ($n['status'] === 'draft'): ?>
                                            <button type="submit" class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-secondary-100 text-secondary-700 border border-secondary-300 hover:bg-secondary-200 transition-colors flex items-center gap-1 mx-auto cursor-pointer" title="ক্লিক করে সক্রিয় করুন">
                                                <span>ড্রাফট (Draft)</span>
                                            </button>
                                        <?php else: ?>
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                                মেয়াদোত্তীর্ণ
                                            </span>
                                        <?php endif; ?>
                                    </form>
                                </td>

                                <!-- Action Buttons (with CSRF Tokens) -->
                                <td class="py-3.5 px-4 align-top text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Instant Broadcast Button -->
                                        <form method="POST" action="<?= $base ?>/admin/notifications/broadcast" class="inline" onsubmit="return confirm('আপনি কি এই নোটিফিকেশনটি সকল পুশ সাবস্ক্রাইবারদের ডিভাইসে পাঠাতে চান?')">
                                            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                            <input type="hidden" name="id" value="<?= $n['id'] ?>">
                                            <button type="submit" class="p-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 text-sm transition-colors cursor-pointer" title="সরাসরি ব্রডকাস্ট পুশ পাঠান">
                                                <ion-icon name="paper-plane-outline"></ion-icon>
                                            </button>
                                        </form>

                                        <!-- Edit -->
                                        <a href="<?= $base ?>/admin/notifications/edit?id=<?= $n['id'] ?>" class="p-2 rounded-xl bg-secondary-100 hover:bg-secondary-200 text-secondary-700 text-sm transition-colors" title="সম্পাদনা করুন">
                                            <ion-icon name="create-outline"></ion-icon>
                                        </a>

                                        <!-- Delete -->
                                        <form method="POST" action="<?= $base ?>/admin/notifications/delete" class="inline" onsubmit="return confirm('আপনি কি নিশ্চিত যে এই নোটিফিকেশনটি মুছে ফেলতে চান?')">
                                            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                            <input type="hidden" name="id" value="<?= $n['id'] ?>">
                                            <button type="submit" class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 text-sm transition-colors cursor-pointer" title="মুছে ফেলুন">
                                                <ion-icon name="trash-outline"></ion-icon>
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
require __DIR__ . '/../../layouts/admin.php';
?>
