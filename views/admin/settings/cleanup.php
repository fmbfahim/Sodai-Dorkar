<?php
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
?>

<div class="max-w-5xl mx-auto mb-16 space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-secondary-900 flex items-center gap-2">
                <ion-icon name="trash-bin-outline" class="text-red-500 text-3xl"></ion-icon>
                ডাটা ক্লিনআপ ও রিসেট (Database Data Reset)
            </h1>
            <p class="text-secondary-500 text-xs mt-1">প্রয়োজন অনুযায়ী নির্দিষ্ট মডিউলের ডাটা আলাদা আলাদাভাবে মুছে ফেলুন অথবা কিছু ডাটা রেখে দিন।</p>
        </div>
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
            <ion-icon name="shield-checkmark" class="text-base text-emerald-600"></ion-icon>
            <span>সুপার এডমিন একাউন্ট চিরতরে সুরক্ষিত</span>
        </div>
    </div>

    <!-- Settings Sub-Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-secondary-200">
        <a href="<?= $base ?>/admin/settings" 
           class="px-5 py-3 font-semibold text-sm transition-all border-b-2 flex items-center gap-2 text-secondary-500 border-transparent hover:text-secondary-800">
            <ion-icon name="settings-outline" class="text-lg"></ion-icon>
            সাধারণ সেটিংস (General)
        </a>
        <a href="<?= $base ?>/admin/settings/units" 
           class="px-5 py-3 font-semibold text-sm transition-all border-b-2 flex items-center gap-2 text-secondary-500 border-transparent hover:text-secondary-800">
            <ion-icon name="scale-outline" class="text-lg"></ion-icon>
            একক ও প্যাকেজিং অপশন (Units & Packaging)
        </a>
        <a href="<?= $base ?>/admin/settings/cleanup" 
           class="px-5 py-3 font-bold text-sm transition-all border-b-2 flex items-center gap-2 text-red-600 border-red-600 bg-red-50/50 rounded-t-xl">
            <ion-icon name="trash-bin-outline" class="text-lg"></ion-icon>
            ডাটা ক্লিনআপ ও রিসেট (Data Reset)
        </a>
    </div>

    <!-- Feedback Alerts -->
    <?php if (!empty($success)): ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl flex items-center gap-2 text-sm shadow-sm">
            <ion-icon name="checkmark-circle" class="text-xl text-emerald-600 shrink-0"></ion-icon>
            <span><?= htmlspecialchars($success) ?></span>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-2xl flex items-center gap-2 text-sm shadow-sm">
            <ion-icon name="alert-circle" class="text-xl text-red-600 shrink-0"></ion-icon>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <!-- Explanatory Safety Banner -->
    <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200/80 rounded-2xl p-5 text-amber-900 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 text-xl">
                <ion-icon name="alert-circle-outline"></ion-icon>
            </div>
            <div>
                <h3 class="font-bold text-sm text-amber-950">নির্বাচনী ডাটা রিসেট গাইডলাইন (Selective Clean-up Rule)</h3>
                <p class="text-xs text-amber-800 mt-0.5 leading-relaxed">
                    যেসব মডিউল আপনি মুছে ফেলতে চান কেবল সেগুলোর বক্সে টিক দিন। যেসব বক্সে টিক থাকবে না সেগুলোর সমস্ত ডাটা (যেমন কাস্টমার, ক্যাটাগরি, প্রোডাক্ট) <strong>সম্পূর্ণ অক্ষত থাকবে</strong>।
                </p>
                <p class="text-[11px] text-amber-700 font-medium mt-1">
                    🛡️ বিশেষ নিরাপত্তা: <strong>সুপার এডমিন (Super Admin)</strong> একাউন্ট কখনোই ডিলিট হবে না।
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <button type="button" onclick="selectAll(false)" class="px-3 py-1.5 bg-white border border-amber-300 hover:bg-amber-100 text-amber-800 text-xs font-semibold rounded-lg transition-colors">
                সব বাতিল (Uncheck All)
            </button>
            <button type="button" onclick="selectAll(true)" class="px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold rounded-lg transition-colors">
                সব নির্বাচন (Select All)
            </button>
        </div>
    </div>

    <!-- Cleanup Form -->
    <form method="POST" action="<?= $base ?>/admin/settings/cleanup/execute" id="cleanupForm" onsubmit="return confirmSubmit(event)">
        <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">

        <!-- Module Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($counts as $targetKey => $info): ?>
                <?php 
                $cardColors = [
                    'amber' => 'border-amber-200 hover:border-amber-300 bg-white',
                    'blue' => 'border-blue-200 hover:border-blue-300 bg-white',
                    'indigo' => 'border-indigo-200 hover:border-indigo-300 bg-white',
                    'emerald' => 'border-emerald-200 hover:border-emerald-300 bg-white',
                    'purple' => 'border-purple-200 hover:border-purple-300 bg-white',
                    'teal' => 'border-teal-200 hover:border-teal-300 bg-white',
                    'orange' => 'border-orange-200 hover:border-orange-300 bg-white',
                    'cyan' => 'border-cyan-200 hover:border-cyan-300 bg-white',
                    'rose' => 'border-rose-200 hover:border-rose-300 bg-white',
                ];
                $cClass = $cardColors[$info['color']] ?? 'border-secondary-200 bg-white';
                ?>
                <label class="relative block border-2 rounded-2xl p-5 cursor-pointer transition-all duration-200 hover:shadow-md select-none group <?= $cClass ?>" id="card_<?= $targetKey ?>">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-10 h-10 rounded-xl bg-secondary-100 flex items-center justify-center text-xl text-secondary-700 group-hover:scale-105 transition-transform">
                                <ion-icon name="<?= $info['icon'] ?>"></ion-icon>
                            </div>
                            <div>
                                <h4 class="font-bold text-secondary-900 text-sm leading-tight"><?= htmlspecialchars($info['label']) ?></h4>
                                <span class="text-[11px] text-secondary-400 font-medium"><?= htmlspecialchars($info['label_en']) ?></span>
                            </div>
                        </div>
                        <input type="checkbox" name="targets[]" value="<?= $targetKey ?>" class="w-5 h-5 rounded-md text-red-600 focus:ring-red-500 border-secondary-300 cursor-pointer target-checkbox" onchange="updateCardState('<?= $targetKey ?>')">
                    </div>

                    <p class="text-xs text-secondary-500 line-clamp-2 mb-4 leading-relaxed">
                        <?= htmlspecialchars($info['desc']) ?>
                    </p>

                    <div class="pt-3 border-t border-secondary-100 flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-secondary-100 text-secondary-700">
                            <ion-icon name="layers-outline"></ion-icon>
                            <?= number_format($info['count']) ?> রেকর্ড
                        </span>

                        <span id="badge_<?= $targetKey ?>" class="text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200">
                            অক্ষত থাকবে (Keep Safe)
                        </span>
                    </div>

                    <?php if (!empty($info['safe_notice'])): ?>
                        <div class="mt-2.5 p-2 bg-emerald-50/80 rounded-xl border border-emerald-200 text-[11px] text-emerald-800 flex items-center gap-1.5 font-medium">
                            <ion-icon name="lock-closed" class="text-emerald-600 text-sm shrink-0"></ion-icon>
                            <span><?= htmlspecialchars($info['safe_notice']) ?></span>
                        </div>
                    <?php endif; ?>
                </label>
            <?php endforeach; ?>
        </div>

        <!-- Confirmation & Execution Console -->
        <div class="mt-8 bg-white rounded-2xl border-2 border-red-200 p-6 shadow-sm space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center text-xl shrink-0">
                    <ion-icon name="shield-alert-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="text-base font-bold text-red-700">চুড়ান্ত নিশ্চিতকরণ ও নিরাপত্তা যাচাই (Action Confirmation)</h3>
                    <p class="text-xs text-secondary-600">নির্বাচিত ডাটাগুলো ডাটাবেস থেকে স্থায়ীভাবে মুছে ফেলা হবে। অ্যাকশন সম্পন্ন করতে নিচের বক্সে <strong class="text-red-600 tracking-wider font-mono uppercase bg-red-50 px-1.5 py-0.5 rounded border border-red-200">RESET</strong> শব্দটি হুবহু টাইপ করুন।</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-center pt-2 border-t border-secondary-100">
                <div class="sm:col-span-2">
                    <div class="relative">
                        <input type="text" id="confirmKeywordInput" name="confirm_keyword" placeholder="টাইপ করুন: RESET" autocomplete="off" oninput="checkFormReadiness()" class="w-full bg-secondary-50 border-2 border-secondary-300 focus:border-red-500 focus:bg-white rounded-xl px-4 py-3 text-sm font-mono tracking-widest text-secondary-900 focus:outline-none transition-all">
                        <span id="validationIcon" class="absolute right-3.5 top-3.5 text-lg hidden text-emerald-600">
                            <ion-icon name="checkmark-circle"></ion-icon>
                        </span>
                    </div>
                </div>

                <div>
                    <button type="submit" id="submitCleanupBtn" disabled class="w-full py-3 px-6 bg-secondary-300 text-secondary-500 font-bold rounded-xl text-sm transition-all flex items-center justify-center gap-2 cursor-not-allowed shadow-none">
                        <ion-icon name="trash" class="text-lg"></ion-icon>
                        <span>ডাটা রিসেট সম্পাদন করুন</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function updateCardState(key) {
    const card = document.getElementById('card_' + key);
    const badge = document.getElementById('badge_' + key);
    const cb = card.querySelector('input[type="checkbox"]');

    if (cb.checked) {
        card.classList.add('border-red-500', 'bg-red-50/20');
        badge.className = 'text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-red-100 text-red-700 border border-red-200';
        badge.innerText = 'মুছে ফেলা হবে (Delete)';
    } else {
        card.classList.remove('border-red-500', 'bg-red-50/20');
        badge.className = 'text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200';
        badge.innerText = 'অক্ষত থাকবে (Keep Safe)';
    }

    checkFormReadiness();
}

function selectAll(check) {
    const checkboxes = document.querySelectorAll('.target-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = check;
        const key = cb.value;
        const card = document.getElementById('card_' + key);
        const badge = document.getElementById('badge_' + key);

        if (check) {
            card.classList.add('border-red-500', 'bg-red-50/20');
            badge.className = 'text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-red-100 text-red-700 border border-red-200';
            badge.innerText = 'মুছে ফেলা হবে (Delete)';
        } else {
            card.classList.remove('border-red-500', 'bg-red-50/20');
            badge.className = 'text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200';
            badge.innerText = 'অক্ষত থাকবে (Keep Safe)';
        }
    });

    checkFormReadiness();
}

function checkFormReadiness() {
    const input = document.getElementById('confirmKeywordInput');
    const btn = document.getElementById('submitCleanupBtn');
    const icon = document.getElementById('validationIcon');
    const checkedCount = document.querySelectorAll('.target-checkbox:checked').length;
    const isKeywordValid = input.value.trim() === 'RESET';

    if (isKeywordValid) {
        icon.classList.remove('hidden');
    } else {
        icon.classList.add('hidden');
    }

    if (isKeywordValid && checkedCount > 0) {
        btn.disabled = false;
        btn.className = 'w-full py-3 px-6 bg-red-600 hover:bg-red-700 active:scale-95 text-white font-bold rounded-xl text-sm transition-all flex items-center justify-center gap-2 cursor-pointer shadow-lg shadow-red-600/30';
    } else {
        btn.disabled = true;
        btn.className = 'w-full py-3 px-6 bg-secondary-300 text-secondary-500 font-bold rounded-xl text-sm transition-all flex items-center justify-center gap-2 cursor-not-allowed shadow-none';
    }
}

function confirmSubmit(e) {
    const checkedBoxes = document.querySelectorAll('.target-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('অনুগ্রহ করে অন্তত একটি ডাটা মডিউল নির্বাচন করুন।');
        e.preventDefault();
        return false;
    }

    let items = [];
    checkedBoxes.forEach(cb => {
        const parent = cb.closest('label');
        const title = parent.querySelector('h4').innerText;
        items.push('• ' + title);
    });

    const promptMsg = "সতর্কতা!\n\nআপনি নিম্নলিখিত মডিউলগুলোর সমস্ত ডাটা মুছে ফেলতে যাচ্ছেন:\n" + 
                      items.join('\n') + 
                      "\n\n(সুপার এডমিন অ্যাকাউন্ট সর্বদা অক্ষত থাকবে)\n\nআপনি কি নিশ্চিত এই অপরিবর্তনীয় পদক্ষেপটি সম্পন্ন করতে চান?";

    if (!confirm(promptMsg)) {
        e.preventDefault();
        return false;
    }

    return true;
}
</script>
