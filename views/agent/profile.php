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

    <!-- Profile Header Card -->
    <div class="bg-white rounded-3xl p-5 border border-gray-200/90 shadow-sm text-center space-y-3">
        <div class="w-16 h-16 rounded-3xl bg-gradient-to-tr from-emerald-600 to-green-500 text-white flex items-center justify-center font-black text-2xl mx-auto shadow-lg shadow-emerald-500/20">
            <?= strtoupper(mb_substr($agent['name'] ?? 'A', 0, 1)) ?>
        </div>

        <div>
            <h1 class="text-lg font-black text-gray-900"><?= htmlspecialchars($agent['name'] ?? 'Agent') ?></h1>
            <p class="text-xs text-gray-500 font-mono">@<?= htmlspecialchars($agent['username'] ?? '') ?></p>
            <div class="mt-1">
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                    <span>সক্রিয় ফিল্ড এজেন্ট</span>
                </span>
            </div>
        </div>

        <div class="pt-3 border-t border-gray-100 grid grid-cols-2 gap-2 text-left text-xs">
            <div class="bg-gray-50 p-3 rounded-2xl">
                <span class="text-[10px] text-gray-400 font-bold uppercase">মোবাইল নম্বর</span>
                <div class="font-mono font-bold text-gray-900 mt-0.5"><?= htmlspecialchars($agent['phone'] ?: 'N/A') ?></div>
            </div>
            <div class="bg-gray-50 p-3 rounded-2xl">
                <span class="text-[10px] text-gray-400 font-bold uppercase">যোগদানের তারিখ</span>
                <div class="font-bold text-gray-900 mt-0.5"><?= date('d M Y', strtotime($agent['created_at'] ?? 'now')) ?></div>
            </div>
        </div>
    </div>

    <!-- Assigned Areas Card -->
    <div class="bg-white rounded-3xl p-5 border border-gray-200/90 shadow-sm space-y-2.5">
        <h2 class="text-xs font-black text-gray-900 uppercase tracking-wider flex items-center gap-1.5">
            <ion-icon name="map" class="text-emerald-600 text-sm"></ion-icon>
            <span>নির্ধারিত কর্ম-এরিয়া (Unions)</span>
        </h2>

        <div class="flex flex-wrap gap-1.5 pt-1">
            <?php if (!empty($assignedAreas)): ?>
                <?php foreach ($assignedAreas as $area): ?>
                    <span class="px-3 py-1 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-bold">
                        📍 <?= htmlspecialchars($area['area_name']) ?>
                    </span>
                <?php endforeach; ?>
            <?php else: ?>
                <span class="text-xs text-gray-400 italic">সার্বজনীন (সকল এরিয়ার কাস্টমারদের অর্ডার নেওয়ার অনুমতি রয়েছে)</span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Performance Stats Card -->
    <div class="bg-white rounded-3xl p-5 border border-gray-200/90 shadow-sm space-y-2.5">
        <h2 class="text-xs font-black text-gray-900 uppercase tracking-wider flex items-center gap-1.5">
            <ion-icon name="trophy" class="text-amber-500 text-sm"></ion-icon>
            <span>লাইফটাইম সেলস সামারি</span>
        </h2>

        <div class="grid grid-cols-2 gap-2 text-xs">
            <div class="p-3 bg-gray-50 rounded-2xl">
                <span class="text-[10px] text-gray-400 font-bold uppercase">সর্বমোট অর্ডার</span>
                <div class="text-lg font-black text-gray-900 mt-0.5"><?= $stats['total_orders'] ?> টি</div>
            </div>
            <div class="p-3 bg-gray-50 rounded-2xl">
                <span class="text-[10px] text-gray-400 font-bold uppercase">মোট বিক্রয় ভ্যালু</span>
                <div class="text-lg font-black text-emerald-700 mt-0.5">৳<?= number_format($stats['total_sales'], 0) ?></div>
            </div>
        </div>
    </div>

    <!-- Change Password Form -->
    <div class="bg-white rounded-3xl p-5 border border-gray-200/90 shadow-sm space-y-3">
        <h2 class="text-xs font-black text-gray-900 uppercase tracking-wider flex items-center gap-1.5">
            <ion-icon name="key-outline" class="text-emerald-600 text-sm"></ion-icon>
            <span>পাসওয়ার্ড পরিবর্তন করুন</span>
        </h2>

        <form action="<?= $base ?>/agent/change-password" method="POST" class="space-y-3 text-xs">
            <div>
                <label class="block font-bold text-gray-700 mb-1">বর্তমান পাসওয়ার্ড *</label>
                <input type="password" name="current_password" required placeholder="বর্তমান পাসওয়ার্ড লিখুন" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">নতুন পাসওয়ার্ড *</label>
                <input type="password" name="new_password" required placeholder="ন্যূনতম ৪ অক্ষর" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">নতুন পাসওয়ার্ড নিশ্চিত করুন *</label>
                <input type="password" name="confirm_password" required placeholder="আবার লিখুন" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition-colors">
                পাসওয়ার্ড পরিবর্তন সংরক্ষণ করুন
            </button>
        </form>
    </div>

    <!-- Logout Button -->
    <div class="pt-2">
        <a href="<?= $base ?>/logout" class="w-full py-3 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 rounded-2xl font-bold text-xs flex items-center justify-center gap-1.5 transition-colors">
            <ion-icon name="log-out-outline" class="text-base"></ion-icon>
            <span>অ্যাকাউন্ট থেকে লগআউট করুন</span>
        </a>
    </div>

</div>
