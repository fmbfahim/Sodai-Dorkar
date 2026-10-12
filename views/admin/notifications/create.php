<?php
$title = "নতুন প্রোমো পুশ নোটিফিকেশন";
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
ob_start();
?>

<div class="space-y-6" x-data="pushNotificationEditor()">
    <!-- Header -->
    <div class="flex items-center justify-between bg-white p-6 rounded-2xl border border-secondary-200/80 shadow-xs">
        <div class="flex items-center gap-3">
            <a href="<?= $base ?>/admin/notifications" class="w-10 h-10 rounded-xl bg-secondary-100 hover:bg-secondary-200 text-secondary-700 flex items-center justify-center transition-colors">
                <ion-icon name="arrow-back" class="text-xl"></ion-icon>
            </a>
            <div>
                <h1 class="text-2xl font-black text-secondary-900 tracking-tight">নতুন প্রোমো পুশ নোটিফিকেশন</h1>
                <p class="text-xs text-secondary-500 font-medium mt-0.5">গ্রাহকদের জন্য আকর্ষণীয় অফার বা ডিসকাউন্ট নোটিফিকেশন ডিজাইন করুন।</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs px-3 py-1.5 rounded-full font-bold bg-primary-50 text-primary-700 flex items-center gap-1.5 border border-primary-100">
                <span class="w-2 h-2 rounded-full bg-primary-600 animate-pulse"></span>
                <span>লাইভ অ্যাপ প্রিভিউ সক্রিয়</span>
            </span>
        </div>
    </div>

    <!-- Error Alert -->
    <?php if (!empty($error)): ?>
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl flex items-center gap-3 shadow-xs">
            <ion-icon name="alert-circle" class="text-2xl text-rose-600 shrink-0"></ion-icon>
            <span class="text-sm font-semibold"><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <!-- Two-Column Layout: Form (Left) & Realtime Smartphone Mockup (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Form Section -->
        <div class="lg:col-span-7 bg-white p-6 rounded-2xl border border-secondary-200/80 shadow-xs">
            <form action="<?= $base ?>/admin/notifications/store" method="POST" enctype="multipart/form-data" class="space-y-5">
                
                <!-- Title -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-1.5">
                        নোটিফিকেশন শিরোনাম <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="title" x-model="title" required
                        placeholder="যেমন: 🔥 ৫০% পর্যন্ত ছাড়! আজকের সেরা স্পেশাল অফার"
                        class="w-full px-4 py-2.5 rounded-xl border border-secondary-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 text-sm font-medium transition-all">
                </div>

                <!-- Message / Body -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-1.5">
                        মেসেজ বা অফারের বিবরণ <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="message" x-model="message" rows="3" required
                        placeholder="যেমন: তাজা শাকসবজি, ফলমূল ও দৈনন্দিন গ্রোসারি কেনাকাটায় আকর্ষণীয় ছাড় পান। আজই অর্ডার করুন এবং দ্রুততম হোম ডেলিভারি নিন!"
                        class="w-full px-4 py-2.5 rounded-xl border border-secondary-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 text-sm font-medium transition-all"></textarea>
                </div>

                <!-- Badge & Promo Code in Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-1.5">
                            অফার ব্যাজ / ট্যাগ
                        </label>
                        <div class="relative">
                            <input type="text" name="badge_text" x-model="badgeText"
                                placeholder="যেমন: 🔥 স্পেশাল অফার"
                                class="w-full px-4 py-2.5 rounded-xl border border-secondary-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 text-sm font-medium transition-all">
                        </div>
                        <div class="flex flex-wrap gap-1 mt-1.5">
                            <button type="button" @click="badgeText = '🔥 মেগা অফার'" class="text-[10px] px-2 py-0.5 rounded-md bg-secondary-100 hover:bg-secondary-200 text-secondary-700 font-semibold">🔥 মেগা অফার</button>
                            <button type="button" @click="badgeText = '⚡ ফ্ল্যাশ সেল'" class="text-[10px] px-2 py-0.5 rounded-md bg-secondary-100 hover:bg-secondary-200 text-secondary-700 font-semibold">⚡ ফ্ল্যাশ সেল</button>
                            <button type="button" @click="badgeText = '🎁 উপহার ভাউচার'" class="text-[10px] px-2 py-0.5 rounded-md bg-secondary-100 hover:bg-secondary-200 text-secondary-700 font-semibold">🎁 উপহার</button>
                            <button type="button" @click="badgeText = '🚚 ফ্রি ডেলিভারি'" class="text-[10px] px-2 py-0.5 rounded-md bg-secondary-100 hover:bg-secondary-200 text-secondary-700 font-semibold">🚚 ফ্রি ডেলিভারি</button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-1.5">
                            প্রোমো / কুপন কোড (যদি থাকে)
                        </label>
                        <input type="text" name="promo_code" x-model="promoCode"
                            placeholder="যেমন: SODAI20"
                            class="w-full px-4 py-2.5 rounded-xl border border-secondary-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 text-sm font-mono font-bold tracking-wider uppercase transition-all">
                        <span class="text-[11px] text-secondary-400 mt-1 block">গ্রাহকরা ১-ক্লিকে কোডটি কপি করতে পারবেন।</span>
                    </div>
                </div>

                <!-- Action URL / Target Link -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-1.5">
                        অ্যাকশন লিংক (ক্লিক করলে যে পেজটি খুলবে)
                    </label>
                    <input type="text" name="action_url" x-model="actionUrl"
                        placeholder="যেমন: /shop অথবা /category?slug=fruits"
                        class="w-full px-4 py-2.5 rounded-xl border border-secondary-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 text-sm font-medium transition-all">
                    <div class="flex flex-wrap gap-1.5 mt-1.5">
                        <button type="button" @click="actionUrl = '/shop'" class="text-[11px] px-2.5 py-1 rounded-lg bg-primary-50 text-primary-700 font-semibold hover:bg-primary-100">দোকান (/shop)</button>
                        <button type="button" @click="actionUrl = '/checkout'" class="text-[11px] px-2.5 py-1 rounded-lg bg-primary-50 text-primary-700 font-semibold hover:bg-primary-100">চেকআউট (/checkout)</button>
                        <button type="button" @click="actionUrl = '/shop?discount=1'" class="text-[11px] px-2.5 py-1 rounded-lg bg-primary-50 text-primary-700 font-semibold hover:bg-primary-100">ডিসকাউন্ট পণ্য</button>
                    </div>
                </div>

                <!-- Image URL & Upload -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700">
                        ব্যানার বা ছবি (ঐচ্ছিক)
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <input type="url" name="image_url" x-model="imageUrl"
                                placeholder="ছবির ওয়েব লিংক (URL)"
                                class="w-full px-4 py-2 rounded-xl border border-secondary-200 text-xs font-medium focus:border-primary-500">
                        </div>
                        <div>
                            <input type="file" name="image_file" accept="image/*" @change="handleFileSelect($event)"
                                class="w-full text-xs text-secondary-500 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer">
                        </div>
                    </div>
                    <span class="text-[11px] text-secondary-400">উচ্চমানের অনুভূমিক (horizontal) ব্যানার দিলে পুশ নোটিফিকেশনে দেখতে চমৎকার লাগে।</span>
                </div>

                <!-- Target Audience & Status & Expiry -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-1.5">
                            টার্গেট অডিয়েন্স
                        </label>
                        <select name="target_audience" x-model="targetAudience"
                            class="w-full px-3 py-2.5 rounded-xl border border-secondary-200 bg-white text-sm font-medium focus:border-primary-500">
                            <option value="all">সকল ভিজিটর (All)</option>
                            <option value="registered">শুধুমাত্র রেজিস্টার্ড গ্রাহক</option>
                            <option value="guests">নতুন অতিথি ভিজিটর</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-1.5">
                            স্ট্যাটাস
                        </label>
                        <select name="status" x-model="status"
                            class="w-full px-3 py-2.5 rounded-xl border border-secondary-200 bg-white text-sm font-medium focus:border-primary-500">
                            <option value="active">সক্রিয় (Active)</option>
                            <option value="draft">ড্রাফট (Draft)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-1.5">
                            মেয়াদ শেষ হওয়ার তারিখ
                        </label>
                        <input type="datetime-local" name="expires_at"
                            class="w-full px-3 py-2.5 rounded-xl border border-secondary-200 bg-white text-xs font-medium focus:border-primary-500">
                    </div>
                </div>

                <!-- Broadcast Checkbox -->
                <div class="p-4 rounded-xl bg-amber-50/70 border border-amber-200 flex items-start gap-3">
                    <input type="checkbox" name="broadcast_now" id="broadcast_now" value="1" checked
                        class="mt-1 w-4 h-4 rounded border-amber-300 text-amber-600 focus:ring-amber-500 cursor-pointer">
                    <label for="broadcast_now" class="cursor-pointer">
                        <span class="block text-xs font-bold text-amber-900">তাত্ক্ষণিক ব্রডকাস্ট পুশ নোটিফিকেশন পাঠান</span>
                        <span class="block text-[11px] text-amber-700 mt-0.5">তৈরি করার সাথে সাথে সকল কানেক্টেড মোবাইল ও ব্রাউজার ডিভাইসে নোটিফিকেশন পাঠানো হবে।</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-secondary-100">
                    <a href="<?= $base ?>/admin/notifications" class="px-5 py-2.5 rounded-xl border border-secondary-200 text-secondary-700 text-sm font-bold hover:bg-secondary-50 transition-colors">
                        বাতিল
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-sm font-bold shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                        <ion-icon name="paper-plane" class="text-base"></ion-icon>
                        <span>নোটিফিকেশন সেভ ও পাবলিশ করুন</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Realtime Mobile Mockup Preview (Right) -->
        <div class="lg:col-span-5 sticky top-6">
            <div class="bg-secondary-900 text-white p-6 rounded-3xl border border-secondary-800 shadow-xl">
                <!-- Preview Tabs -->
                <div class="flex items-center justify-between pb-4 border-b border-secondary-800">
                    <div class="flex items-center gap-2">
                        <ion-icon name="phone-portrait-outline" class="text-lg text-primary-400"></ion-icon>
                        <span class="text-xs font-bold uppercase tracking-wider text-secondary-300">মোবাইল অ্যাপ প্রিভিউ</span>
                    </div>
                    <div class="flex bg-secondary-800 p-0.5 rounded-lg text-[10px] font-bold">
                        <button type="button" @click="previewMode = 'lockscreen'" :class="previewMode === 'lockscreen' ? 'bg-primary-600 text-white' : 'text-secondary-400 hover:text-white'" class="px-2.5 py-1 rounded-md transition-colors">
                            লকস্ক্রিন পুশ
                        </button>
                        <button type="button" @click="previewMode = 'inapp'" :class="previewMode === 'inapp' ? 'bg-primary-600 text-white' : 'text-secondary-400 hover:text-white'" class="px-2.5 py-1 rounded-md transition-colors">
                            ইন-অ্যাপ ব্যানার
                        </button>
                    </div>
                </div>

                <!-- Phone Frame -->
                <div class="mt-5 mx-auto max-w-[310px] rounded-[36px] bg-secondary-950 p-3.5 border-4 border-secondary-800 shadow-2xl relative">
                    <!-- Dynamic Island / Speaker Notch -->
                    <div class="w-24 h-4 bg-secondary-800 rounded-full mx-auto mb-3"></div>

                    <!-- Screen Content -->
                    <div class="bg-gray-100 rounded-[24px] min-h-[460px] text-gray-900 overflow-hidden relative flex flex-col justify-between p-3.5">
                        
                        <!-- Top Bar Mock -->
                        <div class="flex items-center justify-between text-[11px] font-bold text-gray-500 px-1 mb-2">
                            <span>09:41</span>
                            <div class="flex items-center gap-1.5 text-xs">
                                <ion-icon name="wifi"></ion-icon>
                                <ion-icon name="cellular"></ion-icon>
                                <ion-icon name="battery-full"></ion-icon>
                            </div>
                        </div>

                        <!-- 1. Lockscreen Push Notification Card Mode -->
                        <div x-show="previewMode === 'lockscreen'" class="space-y-3 my-auto">
                            <div class="text-center my-4">
                                <div class="text-4xl font-extralight text-gray-800">09:41</div>
                                <div class="text-[11px] text-gray-500 font-medium">সোমবার, ১২ অক্টোবর</div>
                            </div>

                            <!-- Native Push Notification Card -->
                            <div class="bg-white/95 backdrop-blur-md rounded-2xl p-3 shadow-lg border border-gray-200/80 transition-all hover:scale-[1.02]">
                                <div class="flex items-center justify-between mb-1.5">
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-5 h-5 rounded-md bg-emerald-600 text-white flex items-center justify-center text-xs font-black shadow-xs">
                                            স
                                        </div>
                                        <span class="text-[11px] font-bold text-gray-900 tracking-tight">সদাই দরকার</span>
                                        <span class="text-[9px] text-gray-400">• এইমাত্র</span>
                                    </div>
                                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-800" x-text="badgeText || 'স্পেশাল'"></span>
                                </div>

                                <div class="text-xs font-black text-gray-900 line-clamp-1" x-text="title || 'শিরোনাম লিখুন...'"></div>
                                <div class="text-[11px] text-gray-600 line-clamp-2 mt-0.5 leading-snug" x-text="message || 'অফারের মেসেজ লিখুন...'"></div>

                                <!-- Promo Code Pill in Notification -->
                                <template x-if="promoCode">
                                    <div class="mt-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200 text-[10px] font-mono font-bold">
                                        <ion-icon name="pricetag-outline"></ion-icon>
                                        <span x-text="promoCode"></span>
                                    </div>
                                </template>

                                <!-- Image Thumbnail if present -->
                                <template x-if="imageUrl">
                                    <div class="mt-2 rounded-xl overflow-hidden max-h-28 border border-gray-200">
                                        <img :src="imageUrl" alt="Banner" class="w-full h-full object-cover">
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- 2. In-App Floating Promo Banner Mode -->
                        <div x-show="previewMode === 'inapp'" class="my-auto space-y-3">
                            <div class="text-[10px] uppercase font-bold text-gray-400 text-center tracking-wider mb-2">স্টোরফ্রন্ট ভিজিটে পপআপ ব্যানার</div>
                            
                            <!-- In-App Floating Card Mockup -->
                            <div class="bg-white rounded-2xl p-3.5 shadow-xl border-2 border-emerald-500 relative">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-6 h-6 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-xs font-black">
                                            🛒
                                        </div>
                                        <div>
                                            <div class="text-[10px] font-bold uppercase text-emerald-700" x-text="badgeText || '🔥 বিশেষ অফার'"></div>
                                        </div>
                                    </div>
                                    <button type="button" class="text-gray-400 text-sm">✕</button>
                                </div>

                                <template x-if="imageUrl">
                                    <div class="mb-2 rounded-xl overflow-hidden max-h-24 border border-gray-100">
                                        <img :src="imageUrl" alt="Promo" class="w-full h-full object-cover">
                                    </div>
                                </template>

                                <div class="text-xs font-black text-gray-900" x-text="title || 'শিরোনাম লিখুন...'"></div>
                                <div class="text-[11px] text-gray-600 mt-1 leading-snug" x-text="message || 'বিস্তারিত মেসেজ...'"></div>

                                <template x-if="promoCode">
                                    <div class="mt-2.5 p-1.5 rounded-lg bg-gray-50 border border-dashed border-gray-300 flex items-center justify-between text-[10px]">
                                        <span class="font-mono font-bold text-emerald-800" x-text="promoCode"></span>
                                        <span class="text-emerald-700 font-bold bg-white px-1.5 py-0.5 rounded shadow-2xs">কপি করুন</span>
                                    </div>
                                </template>

                                <button type="button" class="w-full mt-2.5 py-1.5 rounded-xl bg-emerald-600 text-white text-xs font-bold text-center shadow-xs">
                                    অফার লুফে নিন ➔
                                </button>
                            </div>
                        </div>

                        <!-- Phone Bottom Home Indicator -->
                        <div class="w-24 h-1 bg-gray-400 rounded-full mx-auto mt-2"></div>
                    </div>
                </div>

                <div class="mt-4 text-center text-xs text-secondary-400">
                    💡 গ্রাহক অ্যাপ বা ব্রাউজারে ঠিক এভাবেই নোটিফিকেশনটি দেখতে পাবেন।
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function pushNotificationEditor() {
    return {
        title: <?= json_encode($old['title'] ?? '') ?>,
        message: <?= json_encode($old['message'] ?? '') ?>,
        badgeText: <?= json_encode($old['badge_text'] ?? '🔥 স্পেশাল অফার') ?>,
        promoCode: <?= json_encode($old['promo_code'] ?? '') ?>,
        actionUrl: <?= json_encode($old['action_url'] ?? '/shop') ?>,
        imageUrl: <?= json_encode($old['image_url'] ?? '') ?>,
        targetAudience: <?= json_encode($old['target_audience'] ?? 'all') ?>,
        status: <?= json_encode($old['status'] ?? 'active') ?>,
        previewMode: 'lockscreen',

        handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imageUrl = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    };
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/admin.php';
?>
