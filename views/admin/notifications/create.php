<?php
$title = "নতুন নোটিফিকেশন / নোটিশ তৈরি করুন";
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
$initialType = $_GET['type'] ?? ($old['type'] ?? 'promo');
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
                <h1 class="text-2xl font-black text-secondary-900 tracking-tight">নতুন নোটিফিকেশন / নোটিশ পাঠান</h1>
                <p class="text-xs text-secondary-500 font-medium mt-0.5">গ্রাহকদের জন্য আকর্ষণীয় অফার, জরুরি নোটিশ বা সার্ভিস বার্তা তৈরি করুন।</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs px-3 py-1.5 rounded-full font-bold bg-emerald-50 text-emerald-700 flex items-center gap-1.5 border border-emerald-200">
                <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                <span>লাইভ প্রিভিউ সক্রিয়</span>
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
                <!-- CSRF Token (Crucial for 419 Page Expired fix) -->
                <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                
                <!-- Notification Type Selector -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-2">
                        নোটিফিকেশনের ধরন (Notification Type) <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <label class="flex flex-col items-center p-3 rounded-xl border-2 cursor-pointer transition-all text-center"
                               :class="type === 'promo' ? 'border-emerald-500 bg-emerald-50/60 text-emerald-900 font-bold shadow-2xs' : 'border-secondary-200 hover:border-secondary-300 text-secondary-600'">
                            <input type="radio" name="type" value="promo" x-model="type" @change="onTypeChange('promo')" class="hidden">
                            <span class="text-xl mb-1">🔥</span>
                            <span class="text-xs">প্রোমো অফার</span>
                        </label>
                        
                        <label class="flex flex-col items-center p-3 rounded-xl border-2 cursor-pointer transition-all text-center"
                               :class="type === 'notice' ? 'border-amber-500 bg-amber-50/60 text-amber-900 font-bold shadow-2xs' : 'border-secondary-200 hover:border-secondary-300 text-secondary-600'">
                            <input type="radio" name="type" value="notice" x-model="type" @change="onTypeChange('notice')" class="hidden">
                            <span class="text-xl mb-1">📢</span>
                            <span class="text-xs">জরুরি নোটিশ</span>
                        </label>

                        <label class="flex flex-col items-center p-3 rounded-xl border-2 cursor-pointer transition-all text-center"
                               :class="type === 'offer' ? 'border-purple-500 bg-purple-50/60 text-purple-900 font-bold shadow-2xs' : 'border-secondary-200 hover:border-secondary-300 text-secondary-600'">
                            <input type="radio" name="type" value="offer" x-model="type" @change="onTypeChange('offer')" class="hidden">
                            <span class="text-xl mb-1">🎁</span>
                            <span class="text-xs">ভাউচার / ছাড়</span>
                        </label>

                        <label class="flex flex-col items-center p-3 rounded-xl border-2 cursor-pointer transition-all text-center"
                               :class="type === 'general' ? 'border-blue-500 bg-blue-50/60 text-blue-900 font-bold shadow-2xs' : 'border-secondary-200 hover:border-secondary-300 text-secondary-600'">
                            <input type="radio" name="type" value="general" x-model="type" @change="onTypeChange('general')" class="hidden">
                            <span class="text-xl mb-1">ℹ️</span>
                            <span class="text-xs">সাধারণ বার্তা</span>
                        </label>
                    </div>
                </div>

                <!-- Title -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-1.5">
                        নোটিফিকেশন শিরোনাম <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="title" x-model="title" required
                        :placeholder="type === 'notice' ? 'যেমন: 📢 জরুরি নোটিশ: ঈদের ছুটি উপলক্ষে ডেলিভারি সময়সূচী' : 'যেমন: 🔥 ৫০% পর্যন্ত ছাড়! আজকের সেরা স্পেশাল অফার'"
                        class="w-full px-4 py-2.5 rounded-xl border border-secondary-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 text-sm font-medium transition-all">
                </div>

                <!-- Message / Body -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-1.5">
                        মেসেজ বা নোটিশের বিস্তারিত বিবরণ <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="message" x-model="message" rows="3" required
                        :placeholder="type === 'notice' ? 'যেমন: প্রিয় গ্রাহক, আগামী কাল আমাদের ওয়্যারহাউজে ইনভেন্টরি অডিট চলবে। ফলে দুপুর ২টার পর সকল ডেলিভারি পরদিন সকালে সম্পন্ন হবে।' : 'যেমন: তাজা শাকসবজি, ফলমূল ও দৈনন্দিন গ্রোসারি কেনাকাটায় আকর্ষণীয় ছাড় পান। আজই অর্ডার করুন এবং দ্রুততম হোম ডেলিভারি নিন!'"
                        class="w-full px-4 py-2.5 rounded-xl border border-secondary-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 text-sm font-medium transition-all"></textarea>
                </div>

                <!-- Badge & Promo Code in Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-1.5">
                            ব্যাজ / ট্যাগ লেবেল
                        </label>
                        <div class="relative">
                            <input type="text" name="badge_text" x-model="badgeText"
                                placeholder="যেমন: 🔥 স্পেশাল অফার বা 📢 জরুরি নোটিশ"
                                class="w-full px-4 py-2.5 rounded-xl border border-secondary-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 text-sm font-medium transition-all">
                        </div>
                        <div class="flex flex-wrap gap-1 mt-1.5">
                            <button type="button" @click="badgeText = '🔥 মেগা অফার'" class="text-[10px] px-2 py-0.5 rounded-md bg-secondary-100 hover:bg-secondary-200 text-secondary-700 font-semibold">🔥 মেগা অফার</button>
                            <button type="button" @click="badgeText = '📢 জরুরি নোটিশ'" class="text-[10px] px-2 py-0.5 rounded-md bg-amber-100 hover:bg-amber-200 text-amber-800 font-semibold">📢 জরুরি নোটিশ</button>
                            <button type="button" @click="badgeText = '⚡ ফ্ল্যাশ সেল'" class="text-[10px] px-2 py-0.5 rounded-md bg-secondary-100 hover:bg-secondary-200 text-secondary-700 font-semibold">⚡ ফ্ল্যাশ সেল</button>
                            <button type="button" @click="badgeText = '🚚 ফ্রি ডেলিভারি'" class="text-[10px] px-2 py-0.5 rounded-md bg-emerald-100 hover:bg-emerald-200 text-emerald-800 font-semibold">🚚 ফ্রি ডেলিভারি</button>
                        </div>
                    </div>

                    <div x-show="type !== 'notice'">
                        <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-1.5">
                            প্রোমো / কুপন কোড (যদি থাকে)
                        </label>
                        <input type="text" name="promo_code" x-model="promoCode"
                            placeholder="যেমন: SODAI20"
                            class="w-full px-4 py-2.5 rounded-xl border border-secondary-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 text-sm font-mono font-bold tracking-wider uppercase transition-all">
                        <span class="text-[11px] text-secondary-400 mt-1 block">গ্রাহকরা ১-ক্লিকে কোডটি কপি করতে পারবেন।</span>
                    </div>
                </div>

                <!-- Action URL / Target Link -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-1.5">
                        অ্যাকশন লিংক (ক্লিক করলে যে পেজটি খুলবে)
                    </label>
                    <input type="text" name="action_url" x-model="actionUrl"
                        placeholder="যেমন: /shop অথবা /checkout"
                        class="w-full px-4 py-2.5 rounded-xl border border-secondary-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 text-sm font-medium transition-all">
                    <div class="flex flex-wrap gap-1.5 mt-1.5">
                        <button type="button" @click="actionUrl = '/shop'" class="text-[11px] px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 font-semibold hover:bg-emerald-100">দোকান (/shop)</button>
                        <button type="button" @click="actionUrl = '/checkout'" class="text-[11px] px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 font-semibold hover:bg-emerald-100">চেকআউট (/checkout)</button>
                        <button type="button" @click="actionUrl = '/account/orders'" class="text-[11px] px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 font-semibold hover:bg-emerald-100">অর্ডার হিস্ট্রি</button>
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
                                class="w-full px-4 py-2 rounded-xl border border-secondary-200 text-xs font-medium focus:border-emerald-500">
                        </div>
                        <div>
                            <input type="file" name="image_file" accept="image/*" @change="handleFileSelect($event)"
                                class="w-full text-xs text-secondary-500 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                        </div>
                    </div>
                </div>

                <!-- Audience, Expiry & Status in Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-1.5">
                            টার্গেট গ্রাহক
                        </label>
                        <select name="target_audience" x-model="targetAudience"
                            class="w-full px-3 py-2.5 rounded-xl border border-secondary-200 bg-white text-xs font-medium focus:border-emerald-500">
                            <option value="all">সকল গ্রাহক ও ভিজিটর</option>
                            <option value="registered">শুধু রেজিস্টার্ড ব্যবহারকারী</option>
                            <option value="guests">শুধু নতুন ভিজিটর (Guest)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-1.5">
                            মেয়াদ শেষ (ঐচ্ছিক)
                        </label>
                        <input type="datetime-local" name="expires_at"
                            class="w-full px-3 py-2.5 rounded-xl border border-secondary-200 bg-white text-xs font-medium focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-1.5">
                            স্ট্যাটাস
                        </label>
                        <select name="status" x-model="status"
                            class="w-full px-3 py-2.5 rounded-xl border border-secondary-200 bg-white text-xs font-medium focus:border-emerald-500">
                            <option value="active">সক্রিয় (Active)</option>
                            <option value="draft">ড্রাফট (Draft)</option>
                        </select>
                    </div>
                </div>

                <!-- Instant Broadcast Checkbox -->
                <div class="p-3.5 rounded-xl bg-amber-50/70 border border-amber-200/80 flex items-start gap-3">
                    <input type="checkbox" name="broadcast_now" id="broadcast_now" value="1"
                        class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                    <label for="broadcast_now" class="text-xs text-amber-900 cursor-pointer">
                        <strong class="font-bold">তাত্ক্ষণিকভাবে পুশ ব্রডকাস্ট পাঠান</strong>
                        <p class="text-[11px] text-amber-700 mt-0.5">সংরক্ষণ করার সাথে সাথে সকল গ্রাহকের ডিভাইসে পুশ নোটিফিকেশন চলে যাবে।</p>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-secondary-100">
                    <a href="<?= $base ?>/admin/notifications" class="px-5 py-2.5 rounded-xl border border-secondary-200 text-secondary-700 text-sm font-bold hover:bg-secondary-50 transition-colors">
                        বাতিল
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                        <ion-icon name="send" class="text-base"></ion-icon>
                        <span>নোটিফিকেশন প্রকাশ করুন</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Smartphone Mockup Preview (Right) -->
        <div class="lg:col-span-5 sticky top-6">
            <div class="bg-secondary-900 text-white p-4 sm:p-6 rounded-3xl border border-secondary-800 shadow-xl space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-secondary-800">
                    <div class="flex items-center gap-2">
                        <ion-icon name="phone-portrait-outline" class="text-xl text-emerald-400"></ion-icon>
                        <span class="text-xs font-bold uppercase tracking-wider text-secondary-300">রিয়েলটাইম ডিভাইস প্রিভিউ</span>
                    </div>
                    <!-- View toggle -->
                    <div class="flex rounded-lg bg-secondary-800 p-0.5 text-[10px] font-bold">
                        <button type="button" @click="previewMode = 'lockscreen'"
                            :class="previewMode === 'lockscreen' ? 'bg-emerald-600 text-white shadow-2xs' : 'text-secondary-400 hover:text-white'"
                            class="px-2.5 py-1 rounded-md transition-all">পুশ নোটিফিকেশন</button>
                        <button type="button" @click="previewMode = 'inapp'"
                            :class="previewMode === 'inapp' ? 'bg-emerald-600 text-white shadow-2xs' : 'text-secondary-400 hover:text-white'"
                            class="px-2.5 py-1 rounded-md transition-all">ইন-অ্যাপ ব্যানার</button>
                    </div>
                </div>

                <!-- Modern Smartphone Frame -->
                <div class="w-full max-w-[320px] mx-auto bg-slate-950 p-3 rounded-[36px] shadow-2xl border-4 border-secondary-700 relative">
                    <!-- Dynamic Island Notch -->
                    <div class="w-20 h-4 bg-black rounded-full mx-auto mb-3 flex items-center justify-center">
                        <div class="w-2 h-2 rounded-full bg-slate-800 mr-2"></div>
                        <div class="w-1.5 h-1.5 rounded-full bg-blue-900"></div>
                    </div>

                    <!-- Screen Content -->
                    <div class="bg-gradient-to-b from-slate-900 to-slate-950 rounded-[26px] p-3 min-h-[420px] flex flex-col justify-between text-white relative overflow-hidden border border-slate-800">
                        
                        <!-- Top status bar -->
                        <div class="flex justify-between items-center text-[10px] text-gray-400 px-1 mb-2">
                            <span>09:41</span>
                            <div class="flex gap-1">
                                <span>📶</span>
                                <span>🔋</span>
                            </div>
                        </div>

                        <!-- 1. Lockscreen Push Notification Style -->
                        <div x-show="previewMode === 'lockscreen'" class="space-y-3 mt-4">
                            <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-3.5 shadow-lg text-white">
                                <div class="flex items-center justify-between gap-2 mb-1.5">
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-5 h-5 rounded-md bg-emerald-600 flex items-center justify-center text-[10px] font-black text-white">
                                            🛒
                                        </div>
                                        <span class="text-[11px] font-bold text-gray-200">সদাই দরকার</span>
                                        <span class="text-[9px] text-gray-400">• এইমাত্র</span>
                                    </div>
                                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-full bg-amber-400/20 text-amber-300 border border-amber-400/30" x-text="badgeText || 'অফার'"></span>
                                </div>

                                <template x-if="imageUrl">
                                    <div class="my-2 rounded-xl overflow-hidden max-h-28 border border-white/10">
                                        <img :src="imageUrl" alt="Preview" class="w-full h-full object-cover">
                                    </div>
                                </template>

                                <h4 class="text-xs font-black text-white tracking-tight" x-text="title || '৫০% পর্যন্ত ছাড়! আজকের সেরা স্পেশাল অফার'"></h4>
                                <p class="text-[11px] text-gray-300 mt-1 line-clamp-2 leading-relaxed" x-text="message || 'দৈনন্দিন গ্রোসারি কেনাকাটায় বিশেষ মূল্যছাড় উপভোগ করুন। আজই অর্ডার করুন!'"></p>

                                <template x-if="promoCode && type !== 'notice'">
                                    <div class="mt-2.5 p-1.5 rounded-lg bg-black/40 border border-white/10 flex items-center justify-between text-[11px]">
                                        <span class="font-mono font-bold text-emerald-400" x-text="'কুপন কোড: ' + promoCode"></span>
                                        <span class="text-[9px] text-gray-300 bg-white/20 px-1.5 py-0.5 rounded">কপি</span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- 2. In-App Floating Card Style -->
                        <div x-show="previewMode === 'inapp'" class="space-y-3 mt-4">
                            <div class="bg-white rounded-2xl p-3 shadow-xl text-gray-900 border border-emerald-300">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-800" x-text="badgeText || 'অফার'"></span>
                                    <span class="text-[9px] text-gray-400">সদাই দরকার অ্যাপ</span>
                                </div>

                                <template x-if="imageUrl">
                                    <div class="my-1.5 rounded-lg overflow-hidden max-h-24">
                                        <img :src="imageUrl" alt="Preview" class="w-full h-full object-cover">
                                    </div>
                                </template>

                                <h4 class="text-xs font-black text-gray-900" x-text="title || '৫০% পর্যন্ত ছাড়! আজকের সেরা অফার'"></h4>
                                <p class="text-[10px] text-gray-600 mt-0.5 line-clamp-2" x-text="message || 'আজই অর্ডার করুন এবং দ্রুততম হোম ডেলিভারি নিন।'"></p>

                                <div class="mt-2 flex gap-1.5">
                                    <button type="button" class="flex-1 py-1 px-2 rounded-lg bg-emerald-600 text-white text-[10px] font-bold text-center">
                                        <span x-text="type === 'notice' ? 'বিস্তারিত দেখুন' : 'অফার লুফে নিন'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Phone Bottom Home Indicator -->
                        <div class="w-24 h-1 bg-gray-600 rounded-full mx-auto mt-2"></div>
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
        type: <?= json_encode($initialType) ?>,
        title: <?= json_encode($old['title'] ?? '') ?>,
        message: <?= json_encode($old['message'] ?? '') ?>,
        badgeText: <?= json_encode($old['badge_text'] ?? ($initialType === 'notice' ? '📢 জরুরি নোটিশ' : '🔥 স্পেশাল অফার')) ?>,
        promoCode: <?= json_encode($old['promo_code'] ?? '') ?>,
        actionUrl: <?= json_encode($old['action_url'] ?? '/shop') ?>,
        imageUrl: <?= json_encode($old['image_url'] ?? '') ?>,
        targetAudience: <?= json_encode($old['target_audience'] ?? 'all') ?>,
        status: <?= json_encode($old['status'] ?? 'active') ?>,
        previewMode: 'lockscreen',

        onTypeChange(t) {
            if (t === 'notice') {
                if (!this.badgeText || this.badgeText === '🔥 স্পেশাল অফার') this.badgeText = '📢 জরুরি নোটিশ';
            } else if (t === 'offer') {
                if (!this.badgeText || this.badgeText === '📢 জরুরি নোটিশ') this.badgeText = '🎁 স্পেশাল অফার';
            } else if (t === 'general') {
                if (!this.badgeText || this.badgeText === '🔥 স্পেশাল অফার') this.badgeText = 'ℹ️ বার্তা';
            } else {
                if (!this.badgeText || this.badgeText === '📢 জরুরি নোটিশ') this.badgeText = '🔥 স্পেশাল অফার';
            }
        },

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
