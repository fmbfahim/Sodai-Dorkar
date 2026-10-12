<?php
$title = "নতুন কুপন তৈরি করুন";
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
ob_start();
?>

<div class="space-y-6" x-data="couponEditor()">
    <!-- Header -->
    <div class="flex items-center justify-between bg-white p-6 rounded-2xl border border-secondary-200/80 shadow-xs">
        <div class="flex items-center gap-3">
            <a href="<?= $base ?>/admin/marketing/coupons" class="w-10 h-10 rounded-xl bg-secondary-100 hover:bg-secondary-200 text-secondary-700 flex items-center justify-center transition-colors">
                <ion-icon name="arrow-back" class="text-xl"></ion-icon>
            </a>
            <div>
                <h1 class="text-2xl font-black text-secondary-900 tracking-tight">নতুন প্রোমো কুপন যোগ করুন</h1>
                <p class="text-xs text-secondary-500 font-medium mt-0.5">কাস্টমারদের জন্য আকর্ষণীয় ছাড়ের কুপন কোড ও ব্যবহারের শর্ত নির্ধারণ করুন।</p>
            </div>
        </div>
    </div>

    <!-- Error Alert -->
    <?php if (!empty($error)): ?>
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl flex items-center gap-3 shadow-xs">
            <ion-icon name="alert-circle" class="text-2xl text-rose-600 shrink-0"></ion-icon>
            <span class="text-sm font-semibold"><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <!-- Two-Column Layout: Form (Left) & Realtime Coupon Voucher Ticket Preview (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Form Section -->
        <div class="lg:col-span-7 bg-white p-6 rounded-2xl border border-secondary-200/80 shadow-xs">
            <form action="<?= $base ?>/admin/marketing/coupons/store" method="POST" class="space-y-5">
                <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                
                <!-- Code & Generate Button -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700">
                            কুপন কোড <span class="text-rose-500">*</span>
                        </label>
                        <button type="button" @click="generateCode()" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1 cursor-pointer">
                            <ion-icon name="sparkles-outline"></ion-icon>
                            <span>অটো-কোড জেনারেট</span>
                        </button>
                    </div>
                    <input type="text" name="code" x-model="code" @input="code = code.toUpperCase()" required
                        placeholder="যেমন: SODAI50 বা EID2026"
                        class="w-full px-4 py-2.5 rounded-xl border border-secondary-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 text-sm font-mono font-black tracking-wider uppercase transition-all">
                </div>

                <!-- Title -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-1.5">
                        কুপন শিরোনাম <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="title" x-model="title" required
                        placeholder="যেমন: ৫০ টাকা স্পেশাল মূল্যছাড়"
                        class="w-full px-4 py-2.5 rounded-xl border border-secondary-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 text-sm font-medium transition-all">
                </div>

                <!-- Discount Type & Value in Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-1.5">
                            ছাড়ের ধরন <span class="text-rose-500">*</span>
                        </label>
                        <select name="discount_type" x-model="discountType"
                            class="w-full px-3 py-2.5 rounded-xl border border-secondary-200 bg-white text-sm font-medium focus:border-emerald-500">
                            <option value="fixed">নির্দিষ্ট টাকা ছাড় (Fixed ৳)</option>
                            <option value="percent">শতাংশ ছাড় (Percentage %)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-1.5">
                            ছাড়ের পরিমাণ <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" step="0.01" name="discount_value" x-model="discountValue" required min="1"
                                placeholder="যেমন: 50 বা 10"
                                class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-secondary-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 text-sm font-bold transition-all">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-black text-secondary-400" x-text="discountType === 'percent' ? '%' : '৳'"></span>
                        </div>
                    </div>
                </div>

                <!-- Min Order Amount & Max Discount Cap -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-1.5">
                            সর্বনিম্ন অর্ডার মূল্য (৳)
                        </label>
                        <input type="number" step="0.01" name="min_order_amount" x-model="minOrderAmount" min="0"
                            placeholder="যেমন: 200 (শর্ত না থাকলে 0 রাখুন)"
                            class="w-full px-4 py-2.5 rounded-xl border border-secondary-200 focus:border-emerald-500 text-sm font-medium transition-all">
                    </div>

                    <div x-show="discountType === 'percent'">
                        <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-1.5">
                            সর্বোচ্চ ছাড়ের সীমা (৳)
                        </label>
                        <input type="number" step="0.01" name="max_discount_amount" x-model="maxDiscountAmount" min="0"
                            placeholder="যেমন: 100 (লিমিট না থাকলে ফাঁকা রাখুন)"
                            class="w-full px-4 py-2.5 rounded-xl border border-secondary-200 focus:border-emerald-500 text-sm font-medium transition-all">
                    </div>
                </div>

                <!-- Usage Limit & Limit per User in Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-1.5">
                            সর্বমোট ব্যবহারের সীমা (মোট কতবার)
                        </label>
                        <input type="number" name="usage_limit" min="1"
                            placeholder="সীমাহীন ব্যবহারের জন্য ফাঁকা রাখুন"
                            class="w-full px-4 py-2.5 rounded-xl border border-secondary-200 focus:border-emerald-500 text-sm font-medium transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-1.5">
                            প্রতি গ্রাহক কতবার ব্যবহার করতে পারবেন
                        </label>
                        <input type="number" name="usage_limit_per_user" value="1" min="1" required
                            class="w-full px-4 py-2.5 rounded-xl border border-secondary-200 focus:border-emerald-500 text-sm font-medium transition-all">
                    </div>
                </div>

                <!-- Validity Dates in Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-1.5">
                            শুরুর তারিখ (ঐচ্ছিক)
                        </label>
                        <input type="datetime-local" name="start_date" x-model="startDate"
                            class="w-full px-3 py-2.5 rounded-xl border border-secondary-200 bg-white text-xs font-medium focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-1.5">
                            মেয়াদ শেষ হওয়ার তারিখ (ঐচ্ছিক)
                        </label>
                        <input type="datetime-local" name="end_date" x-model="endDate"
                            class="w-full px-3 py-2.5 rounded-xl border border-secondary-200 bg-white text-xs font-medium focus:border-emerald-500">
                    </div>
                </div>

                <!-- Status & Description -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-1.5">
                        স্ট্যাটাস
                    </label>
                    <select name="status"
                        class="w-full px-3 py-2.5 rounded-xl border border-secondary-200 bg-white text-sm font-medium focus:border-emerald-500">
                        <option value="active">সক্রিয় (Active)</option>
                        <option value="inactive">নিষ্ক্রিয় (Inactive)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-1.5">
                        বিবরণ বা শর্তাবলী (Description)
                    </label>
                    <textarea name="description" x-model="description" rows="2"
                        placeholder="যেমন: সর্বনিম্ন ২০০ টাকার অর্ডারে প্রযোজ্য। একজন গ্রাহক একবার ব্যবহার করতে পারবেন।"
                        class="w-full px-4 py-2.5 rounded-xl border border-secondary-200 focus:border-emerald-500 text-sm font-medium transition-all"></textarea>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-secondary-100">
                    <a href="<?= $base ?>/admin/marketing/coupons" class="px-5 py-2.5 rounded-xl border border-secondary-200 text-secondary-700 text-sm font-bold hover:bg-secondary-50 transition-colors">
                        বাতিল
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                        <ion-icon name="checkmark-circle" class="text-base"></ion-icon>
                        <span>কুপন সংরক্ষণ করুন</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Realtime Interactive Coupon Ticket Mockup (Right) -->
        <div class="lg:col-span-5 sticky top-6">
            <div class="bg-gradient-to-br from-emerald-900 to-green-950 text-white p-6 rounded-3xl border border-emerald-800 shadow-xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-emerald-800/80">
                    <div class="flex items-center gap-2">
                        <ion-icon name="ticket-outline" class="text-xl text-amber-400"></ion-icon>
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-200">লাইভ কুপন ভাউচার টিকিট</span>
                    </div>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-800 text-emerald-300 font-bold">লাইভ প্রিভিউ</span>
                </div>

                <!-- Modern Perforated Ticket Card -->
                <div class="bg-white rounded-2xl text-gray-900 overflow-hidden shadow-2xl relative border border-emerald-200">
                    <!-- Top Ribbon -->
                    <div class="bg-emerald-600 text-white p-4 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center text-lg font-black">
                                🎁
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold tracking-wider text-emerald-100">সদাই দরকার ভাউচার</span>
                                <h3 class="text-sm font-black tracking-tight" x-text="title || '৫০ টাকা বিশেষ মূল্যছাড়'"></h3>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-black leading-none" x-text="discountType === 'percent' ? (discountValue || '10') + '%' : '৳' + (discountValue || '50')"></div>
                            <span class="text-[9px] font-bold uppercase tracking-wider text-emerald-200">ছাড়</span>
                        </div>
                    </div>

                    <!-- Perforated Cutout Circles -->
                    <div class="relative py-2 px-4 flex items-center justify-between bg-emerald-50/50">
                        <div class="absolute -left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-emerald-900"></div>
                        <div class="w-full border-b-2 border-dashed border-gray-300"></div>
                        <div class="absolute -right-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-emerald-900"></div>
                    </div>

                    <!-- Ticket Bottom Content -->
                    <div class="p-4 pt-1 space-y-3">
                        <!-- Code Box -->
                        <div class="p-2.5 rounded-xl bg-gray-50 border border-dashed border-gray-300 flex items-center justify-between">
                            <div>
                                <span class="text-[9px] uppercase font-bold text-gray-400 block">কুপন কোড</span>
                                <span class="text-base font-mono font-black text-emerald-800 tracking-wider" x-text="code || 'SODAI50'"></span>
                            </div>
                            <button type="button" class="px-3 py-1 bg-emerald-600 text-white text-xs font-bold rounded-lg shadow-2xs">
                                কপি
                            </button>
                        </div>

                        <!-- Terms & Conditions -->
                        <div class="text-[11px] text-gray-600 space-y-1">
                            <div class="flex items-center gap-1.5 font-medium">
                                <ion-icon name="checkmark-circle" class="text-emerald-600 text-sm"></ion-icon>
                                <span>সর্বনিম্ন অর্ডার: <strong class="text-gray-900" x-text="'৳' + (minOrderAmount || '0')"></strong></span>
                            </div>
                            <template x-if="discountType === 'percent' && maxDiscountAmount">
                                <div class="flex items-center gap-1.5 font-medium">
                                    <ion-icon name="checkmark-circle" class="text-emerald-600 text-sm"></ion-icon>
                                    <span>সর্বোচ্চ ছাড়: <strong class="text-gray-900" x-text="'৳' + maxDiscountAmount"></strong></span>
                                </div>
                            </template>
                            <div class="text-gray-500 italic mt-1 line-clamp-2" x-text="description || 'চেকআউটে কুপন কোডটি দিয়ে সরাসরি ছাড় উপভোগ করুন।'"></div>
                        </div>
                    </div>
                </div>

                <div class="text-center text-xs text-emerald-300 pt-2">
                    💡 গ্রাহক চেকআউট পেজে এই কোডটি লিখলে সাথে সাথে ছাড় কার্যকর হবে।
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function couponEditor() {
    return {
        code: <?= json_encode($old['code'] ?? '') ?>,
        title: <?= json_encode($old['title'] ?? '') ?>,
        discountType: <?= json_encode($old['discount_type'] ?? 'fixed') ?>,
        discountValue: <?= json_encode($old['discount_value'] ?? '50') ?>,
        minOrderAmount: <?= json_encode($old['min_order_amount'] ?? '200') ?>,
        maxDiscountAmount: <?= json_encode($old['max_discount_amount'] ?? '') ?>,
        startDate: <?= json_encode($old['start_date'] ?? '') ?>,
        endDate: <?= json_encode($old['end_date'] ?? '') ?>,
        description: <?= json_encode($old['description'] ?? '') ?>,

        generateCode() {
            const prefixes = ['SODAI', 'SAVE', 'DISCOUNT', 'SUPER', 'FRESH'];
            const p = prefixes[Math.floor(Math.random() * prefixes.length)];
            const r = Math.floor(10 + Math.random() * 90);
            this.code = p + r;
        }
    };
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../../layouts/admin.php';
?>
