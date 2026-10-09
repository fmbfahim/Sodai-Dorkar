<?php
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
?>

<div class="max-w-md mx-auto py-6 text-center space-y-6">

    <!-- Success Celebration Icon -->
    <div class="relative inline-block">
        <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-3xl flex items-center justify-center text-5xl mx-auto shadow-xl shadow-emerald-500/20 animate-in zoom-in-75 duration-300">
            <ion-icon name="checkmark-circle"></ion-icon>
        </div>
        <div class="absolute -top-1 -right-1 w-6 h-6 bg-emerald-500 rounded-full border-2 border-white animate-ping"></div>
    </div>

    <div>
        <span class="inline-block px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-black uppercase tracking-wider mb-2">
            অর্ডার নিশ্চিতকরণ সম্পন্ন
        </span>
        <h1 class="text-2xl font-black text-gray-900 tracking-tight">অর্ডার সফলভাবে গৃহীত হয়েছে!</h1>
        <p class="text-xs text-gray-500 mt-1">কাস্টমারের অর্ডারটি সিস্টেমে পেন্ডিং স্ট্যাটাসে তালিকাভুক্ত হয়েছে।</p>
    </div>

    <?php if ($order): ?>
        <!-- Order Summary Receipt Card -->
        <div class="bg-white rounded-3xl p-5 border border-gray-200/80 shadow-sm text-left space-y-3.5 text-xs">
            <div class="flex items-center justify-between border-b border-gray-100 pb-2.5">
                <div>
                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">অর্ডার নম্বর</span>
                    <div class="text-base font-black text-emerald-800">#<?= $order['id'] ?></div>
                </div>
                <div class="text-right">
                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">তারিখ ও সময়</span>
                    <div class="text-xs font-mono font-bold text-gray-700"><?= date('d M Y, h:i A') ?></div>
                </div>
            </div>

            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">গ্রাহকের নাম:</span>
                    <span class="font-black text-gray-900"><?= htmlspecialchars($order['customer_name'] ?? 'Customer') ?></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">মোবাইল নম্বর:</span>
                    <span class="font-mono font-bold text-gray-800"><?= htmlspecialchars($order['contact_number'] ?? $order['customer_phone'] ?? '') ?></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">ডেলিভারি এরিয়া:</span>
                    <span class="font-bold text-gray-800"><?= htmlspecialchars($order['area_name'] ?? 'N/A') ?></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">পেমেন্ট মাধ্যম:</span>
                    <span class="font-bold uppercase text-emerald-700"><?= htmlspecialchars($order['payment_method'] ?? 'cash') ?></span>
                </div>
            </div>

            <div class="pt-2 border-t border-gray-100 flex items-center justify-between text-sm">
                <span class="font-black text-gray-900">সর্বমোট প্রদেয় বিল:</span>
                <span class="font-black text-emerald-700 text-base">৳<?= number_format($order['total_amount'], 2) ?></span>
            </div>
        </div>
    <?php endif; ?>

    <!-- Action Buttons -->
    <div class="space-y-2 pt-2">
        <a href="<?= $base ?>/agent/shop" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-black text-sm shadow-md transition-all flex items-center justify-center gap-2 active:scale-95">
            <ion-icon name="cart"></ion-icon>
            <span>নতুন আরেকটি অর্ডার নিন (Take Another Order)</span>
        </a>

        <?php if ($order): ?>
            <a href="<?= $base ?>/agent/orders/show?id=<?= $order['id'] ?>" class="w-full py-3 bg-white hover:bg-gray-50 text-gray-800 border border-gray-200 rounded-2xl font-bold text-xs shadow-2xs transition-colors flex items-center justify-center gap-1.5">
                <ion-icon name="document-text-outline"></ion-icon>
                <span>অর্ডার ইনভয়েস ও রিসিট দেখুন</span>
            </a>
        <?php endif; ?>

        <a href="<?= $base ?>/agent/dashboard" class="inline-block text-xs font-bold text-gray-500 hover:text-emerald-700 pt-2 transition-colors">
            ড্যাশবোর্ডে ফিরে যান ➔
        </a>
    </div>

</div>
