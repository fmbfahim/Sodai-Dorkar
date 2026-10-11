<?php
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
$oauthPages = $oauthPages ?? ($_SESSION['fb_oauth_pages'] ?? []);
$connectedPageId = $settings['facebook_page_id'] ?? '';
$connectedPageName = $settings['facebook_page_name'] ?? '';
$connectedPagePicture = $settings['facebook_page_picture'] ?? '';
$hasConnectedPage = !empty($connectedPageId) && !empty($settings['facebook_page_access_token'] ?? '');
?>

<div class="max-w-5xl mx-auto space-y-8 mb-16">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-secondary-200 pb-5">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="<?= $base ?>/admin/marketing" class="text-secondary-400 hover:text-secondary-600 transition-colors">
                    <ion-icon name="arrow-back-outline" class="text-lg"></ion-icon>
                </a>
                <h2 class="text-2xl font-bold text-secondary-900">মার্কেটিং ও ফেসবুক সেটিংস (Marketing Settings)</h2>
            </div>
            <p class="text-secondary-500 text-xs">Facebook Login, Gemini AI এবং প্রতিদিন ৩টি পোস্টের অটোমেশন ও শিডিউল কনফিগার করুন।</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?= $base ?>/admin/marketing" class="px-4 py-2 bg-secondary-100 hover:bg-secondary-200 text-secondary-700 font-bold text-xs rounded-xl transition-colors flex items-center gap-1.5 shadow-2xs">
                <ion-icon name="grid-outline" class="text-base"></ion-icon>
                <span>মার্কেটিং ড্যাশবোর্ড</span>
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2 shadow-2xs">
            <ion-icon name="checkmark-circle" class="text-lg text-emerald-600 shrink-0"></ion-icon>
            <span><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></span>
        </div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs font-semibold flex items-center gap-2 shadow-2xs">
            <ion-icon name="alert-circle" class="text-lg text-red-600 shrink-0"></ion-icon>
            <span><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></span>
        </div>
    <?php endif; ?>

    <form id="marketing-settings-form" action="<?= $base ?>/admin/marketing/settings/save" method="POST" class="space-y-8">
        <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">

        <!-- 1. Facebook Page Integration (Login with Facebook & Auto Post) -->
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200 p-6 sm:p-7">
            <div class="flex items-center justify-between border-b border-secondary-100 pb-4 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 shadow-2xs">
                        <ion-icon name="logo-facebook" class="text-2xl"></ion-icon>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-secondary-900">Facebook Page Integration (লগইন টু ফেসবুক ও অটো পোস্ট)</h3>
                        <p class="text-xs text-secondary-500 mt-0.5">ফেসবুকে লগইন করে আপনার পেজ নির্বাচন করলেই সব স্বয়ংক্রিয়ভাবে সেট হয়ে যাবে (আলাদা Access Token লাগবে না)।</p>
                    </div>
                </div>
                <div>
                    <?php if ($hasConnectedPage): ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                            <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                            পেজ কানেক্টেড
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            পেজ কানেক্ট করা নেই
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Connected Page Status Card (If connected) -->
            <?php if ($hasConnectedPage): ?>
                <div class="bg-gradient-to-r from-blue-50/80 via-indigo-50/50 to-white border border-blue-200 rounded-2xl p-5 mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-2xs">
                    <div class="flex items-center gap-4">
                        <?php if (!empty($connectedPagePicture)): ?>
                            <img src="<?= htmlspecialchars($connectedPagePicture) ?>" alt="Page Avatar" class="w-14 h-14 rounded-2xl border-2 border-white shadow-sm object-cover bg-white">
                        <?php else: ?>
                            <div class="w-14 h-14 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-2xl shadow-sm">
                                <ion-icon name="logo-facebook"></ion-icon>
                            </div>
                        <?php endif; ?>
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-base font-bold text-secondary-900"><?= htmlspecialchars($connectedPageName ?: 'সংযুক্ত ফেসবুক পেজ') ?></h4>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                    <ion-icon name="checkmark-circle" class="text-xs"></ion-icon>
                                    কানেক্টেড
                                </span>
                            </div>
                            <p class="text-xs text-secondary-500 font-mono mt-0.5">Page ID: <span class="font-bold text-secondary-700"><?= htmlspecialchars($connectedPageId) ?></span></p>
                            <p class="text-[11px] text-emerald-700 font-medium mt-1 flex items-center gap-1">
                                <ion-icon name="shield-checkmark-outline"></ion-icon>
                                <span>পেজ অ্যাক্সেস টোকেন স্বয়ংক্রিয়ভাবে সংরক্ষিত রয়েছে — আলাদা কোনো টোকেন দেওয়ার প্রয়োজন নেই।</span>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap shrink-0">
                        <button type="button" onclick="testFacebookConnection()" 
                                class="px-3.5 py-2 bg-white hover:bg-blue-50 text-blue-700 border border-blue-200 rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5 shadow-2xs cursor-pointer">
                            <ion-icon name="pulse-outline" class="text-base"></ion-icon>
                            <span>কানেকশন টেস্ট</span>
                        </button>
                        <button type="button" onclick="startFacebookLogin()" 
                                class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5 shadow-2xs cursor-pointer">
                            <ion-icon name="swap-horizontal-outline" class="text-base"></ion-icon>
                            <span>পেজ পরিবর্তন করুন</span>
                        </button>
                        <button type="button" onclick="confirmDisconnectFacebook()" 
                                class="px-3 py-2 bg-white hover:bg-red-50 text-red-600 border border-red-200 rounded-xl text-xs font-bold transition-colors flex items-center gap-1 shadow-2xs cursor-pointer">
                            <ion-icon name="unlink-outline" class="text-base"></ion-icon>
                            <span>ডিসকানেক্ট</span>
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <!-- Not Connected: Prominent "Login with Facebook" CTA -->
                <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 text-white rounded-2xl p-6 sm:p-8 mb-6 shadow-md relative overflow-hidden">
                    <div class="absolute -right-8 -bottom-8 opacity-10 text-white pointer-events-none">
                        <ion-icon name="logo-facebook" class="text-9xl"></ion-icon>
                    </div>
                    <div class="relative z-10 max-w-2xl">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white/20 text-white backdrop-blur-xs mb-3">
                            <ion-icon name="sparkles" class="text-amber-300"></ion-icon>
                            <span>১-ক্লিকে ফেসবুক পেজ কানেকশন</span>
                        </div>
                        <h4 class="text-xl sm:text-2xl font-black mb-2">ফেসবুক পেজ সেট করুন কোনো টোকেন ছাড়াই!</h4>
                        <p class="text-xs sm:text-sm text-blue-100 leading-relaxed mb-6">
                            নিচের "লগইন টু ফেসবুক" বাটনে ক্লিক করলে ফেসবুক ওপেন হবে। আপনার অ্যাকাউন্টের পেজটি সিলেক্ট করে দিলেই সব স্বয়ংক্রিয়ভাবে সেট হয়ে যাবে। কোনো জটিল Access Token তৈরি বা কপি-পেস্ট করতে হবে না।
                        </p>

                        <div class="flex items-center gap-3 flex-wrap">
                            <button type="button" onclick="startFacebookLogin()" 
                                    class="px-6 py-3.5 bg-white hover:bg-blue-50 text-blue-700 font-extrabold text-sm rounded-xl shadow-lg hover:shadow-xl transition-all flex items-center gap-2.5 cursor-pointer transform hover:-translate-y-0.5">
                                <ion-icon name="logo-facebook" class="text-2xl text-[#1877F2]"></ion-icon>
                                <span>লগইন টু ফেসবুক (Login with Facebook)</span>
                            </button>

                            <a href="<?= $base ?>/admin/marketing/facebook-auth" 
                               class="px-4 py-3 bg-white/15 hover:bg-white/25 text-white font-bold text-xs rounded-xl backdrop-blur-xs transition-colors flex items-center gap-1.5">
                                <ion-icon name="open-outline" class="text-base"></ion-icon>
                                <span>অথবা ব্রাউজারে রিডাইরেক্ট করে কানেক্ট করুন</span>
                            </a>
                        </div>
                    </div>

                    <!-- 3 Simple Steps Info -->
                    <div class="relative z-10 grid grid-cols-1 sm:grid-cols-3 gap-3 mt-6 pt-6 border-t border-white/20">
                        <div class="flex items-center gap-2.5 text-xs text-blue-100">
                            <div class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center font-bold text-xs shrink-0">১</div>
                            <span>লগইন টু ফেসবুক বাটনে চাপুন</span>
                        </div>
                        <div class="flex items-center gap-2.5 text-xs text-blue-100">
                            <div class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center font-bold text-xs shrink-0">২</div>
                            <span>ফেসবুকে লগইন ও অনুমোদন দিন</span>
                        </div>
                        <div class="flex items-center gap-2.5 text-xs text-blue-100">
                            <div class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center font-bold text-xs shrink-0">৩</div>
                            <span>পেজ সিলেক্ট করলেই সব সেট!</span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Facebook App Credentials (Needed for Facebook Login) -->
            <div class="p-5 bg-secondary-50/80 rounded-2xl border border-secondary-200 mb-6 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-secondary-800 flex items-center gap-1.5">
                            <ion-icon name="key-outline" class="text-sm text-blue-600"></ion-icon>
                            <span>Facebook App Credentials (লগইন ডায়ালগের জন্য)</span>
                        </h4>
                        <p class="text-[11px] text-secondary-500 mt-0.5">
                            "Login with Facebook" বাটনে ক্লিক করলে ফেসবুক যাতে আপনার অ্যাপ চিনতে পারে সেজন্য App ID প্রয়োজন।
                        </p>
                    </div>
                    <a href="https://developers.facebook.com/apps" target="_blank" class="text-xs font-bold text-blue-600 hover:underline flex items-center gap-1">
                        <span>Meta for Developers</span>
                        <ion-icon name="open-outline"></ion-icon>
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-secondary-700 mb-1.5" for="facebook_app_id">
                            Facebook App ID <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="facebook_app_id" name="facebook_app_id" 
                               value="<?= htmlspecialchars($settings['facebook_app_id'] ?? '') ?>" 
                               placeholder="যেমন: 123456789012345" 
                               class="w-full px-3.5 py-2.5 bg-white border border-secondary-300 rounded-xl text-sm font-mono focus:ring-2 focus:ring-blue-500">
                        <span class="text-[10px] text-secondary-400 mt-1 block">developers.facebook.com এর App Dashboard থেকে App ID পাবেন</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-secondary-700 mb-1.5" for="facebook_app_secret">
                            Facebook App Secret <span class="text-secondary-400 font-normal">(ঐচ্ছিক, সার্ভার ভেরিফিকেশনের জন্য)</span>
                        </label>
                        <div class="relative">
                            <input type="password" id="facebook_app_secret" name="facebook_app_secret" 
                                   value="<?= htmlspecialchars($settings['facebook_app_secret'] ?? '') ?>" 
                                   placeholder="App Secret..." 
                                   class="w-full px-3.5 py-2.5 bg-white border border-secondary-300 rounded-xl text-sm font-mono focus:ring-2 focus:ring-blue-500">
                            <button type="button" onclick="toggleApiKeyVisibility('facebook_app_secret')" 
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-secondary-400 hover:text-secondary-600 cursor-pointer">
                                <ion-icon name="eye-outline" class="text-lg"></ion-icon>
                            </button>
                        </div>
                        <span class="text-[10px] text-secondary-400 mt-1 block">সার্ভার-সাইড টোকেন অটো-রিনিউ করার জন্য ব্যবহৃত হয়</span>
                    </div>
                </div>
            </div>

            <!-- Auto Post Toggle -->
            <div class="p-4 rounded-xl bg-blue-50/60 border border-blue-200/80 mb-5 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-blue-950 block">স্বয়ংক্রিয় শিডিউল পোস্টিং চালু করুন (Auto Post Enabled)</span>
                    <span class="text-[11px] text-blue-700">চালু থাকলে নির্দিষ্ট সময়ে (সকাল, দুপুর ও সন্ধ্যা) স্বয়ংক্রিয়ভাবে পেজে পোস্ট হয়ে যাবে।</span>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="facebook_auto_post_enabled" value="1" 
                           <?= ($settings['facebook_auto_post_enabled'] ?? '0') === '1' ? 'checked' : '' ?> 
                           class="sr-only peer">
                    <div class="w-11 h-6 bg-secondary-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-secondary-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>

            <!-- Advanced / Manual Token Accordion (Optional) -->
            <details class="group border border-secondary-200 rounded-xl overflow-hidden mt-4">
                <summary class="p-3.5 bg-secondary-50 hover:bg-secondary-100/70 cursor-pointer text-xs font-bold text-secondary-700 flex items-center justify-between transition-colors select-none">
                    <span class="flex items-center gap-2">
                        <ion-icon name="construct-outline" class="text-base text-secondary-500"></ion-icon>
                        <span>⚙️ ম্যানুয়ালি Page ID এবং Access Token সেট করতে চাইলে (Advanced Option)</span>
                    </span>
                    <ion-icon name="chevron-down-outline" class="transition-transform group-open:rotate-180"></ion-icon>
                </summary>
                <div class="p-4 bg-white border-t border-secondary-200 space-y-4">
                    <p class="text-[11px] text-secondary-500">
                        লগইন বাটনের বদলে আপনি যদি মেটা গ্রাফ এপিআই এক্সপ্লোরার থেকে সংগৃহীত টোকেন সরাসরি দিতে চান, তবে নিচে দিতে পারেন:
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-secondary-700 mb-1" for="facebook_page_id">
                                Facebook Page ID
                            </label>
                            <input type="text" id="facebook_page_id" name="facebook_page_id" 
                                   value="<?= htmlspecialchars($settings['facebook_page_id'] ?? '') ?>" 
                                   placeholder="104829384910293" 
                                   class="w-full px-3.5 py-2 bg-white border border-secondary-300 rounded-xl text-xs font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-secondary-700 mb-1" for="facebook_page_name">
                                Facebook Page Name
                            </label>
                            <input type="text" id="facebook_page_name" name="facebook_page_name" 
                                   value="<?= htmlspecialchars($settings['facebook_page_name'] ?? '') ?>" 
                                   placeholder="সদাই দরকার" 
                                   class="w-full px-3.5 py-2 bg-white border border-secondary-300 rounded-xl text-xs">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-secondary-700 mb-1" for="facebook_page_access_token">
                                Page Access Token
                            </label>
                            <div class="relative">
                                <input type="password" id="facebook_page_access_token" name="facebook_page_access_token" 
                                       value="<?= htmlspecialchars($settings['facebook_page_access_token'] ?? '') ?>" 
                                       placeholder="EAAG..." 
                                       class="w-full px-3.5 py-2 bg-white border border-secondary-300 rounded-xl text-xs font-mono">
                                <button type="button" onclick="toggleApiKeyVisibility('facebook_page_access_token')" 
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-secondary-400 hover:text-secondary-600 cursor-pointer">
                                    <ion-icon name="eye-outline" class="text-base"></ion-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </details>

            <div class="flex items-center justify-between pt-4">
                <button type="button" onclick="testFacebookConnection()" 
                        class="px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5 shadow-2xs cursor-pointer">
                    <ion-icon name="pulse-outline" class="text-base"></ion-icon>
                    <span>ফেসবুক পেজ কানেকশন টেস্ট করুন</span>
                </button>
                <span id="fb-test-result" class="text-xs font-semibold"></span>
            </div>
        </div>

        <!-- 2. Google Gemini API Configuration -->
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200 p-6 sm:p-7">
            <div class="flex items-center justify-between border-b border-secondary-100 pb-4 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 border border-purple-100 flex items-center justify-center text-purple-600 shadow-2xs">
                        <ion-icon name="sparkles" class="text-xl"></ion-icon>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-secondary-900">Google Gemini API (এআই ক্যাপশন ও কন্টেন্ট)</h3>
                        <p class="text-xs text-secondary-500 mt-0.5">Gemini AI ব্যবহার করে আকর্ষণীয় বাংলা ক্যাপশন, স্লোগান ও বিক্রয়মুখী অফার স্বয়ংক্রিয়ভাবে তৈরি হবে।</p>
                    </div>
                </div>
                <div>
                    <?php if ($isGeminiConfigured): ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            API কানেক্টেড
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            কনফিগার করা হয়নি
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-2" for="gemini_api_key">
                        Gemini API Key <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="gemini_api_key" name="gemini_api_key" 
                               value="<?= htmlspecialchars($settings['gemini_api_key'] ?? '') ?>" 
                               placeholder="AIzaSy..." 
                               class="w-full px-4 py-2.5 bg-white border border-secondary-300 rounded-xl text-sm font-mono focus:ring-2 focus:ring-purple-500">
                        <button type="button" onclick="toggleApiKeyVisibility('gemini_api_key')" 
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-secondary-400 hover:text-secondary-600 cursor-pointer">
                            <ion-icon name="eye-outline" class="text-lg"></ion-icon>
                        </button>
                    </div>
                    <p class="text-[11px] text-secondary-400 mt-1.5">
                        গুগল এআই স্টুডিও (<a href="https://aistudio.google.com/app/apikey" target="_blank" class="text-purple-600 hover:underline font-bold">Google AI Studio</a>) থেকে সম্পূর্ণ বিনামূল্যে আপনার API Key সংগ্রহ করতে পারেন।
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-2" for="gemini_model">
                        Gemini Model
                    </label>
                    <?php $curModel = $settings['gemini_model'] ?? 'gemini-1.5-flash'; ?>
                    <select id="gemini_model" name="gemini_model" class="w-full px-4 py-2.5 bg-white border border-secondary-300 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-purple-500">
                        <option value="gemini-1.5-flash" <?= $curModel === 'gemini-1.5-flash' ? 'selected' : '' ?>>Gemini 1.5 Flash (সুপার ফাস্ট - রেকমেন্ডেড)</option>
                        <option value="gemini-2.0-flash" <?= $curModel === 'gemini-2.0-flash' ? 'selected' : '' ?>>Gemini 2.0 Flash (নতুন ও আধুনিক)</option>
                        <option value="gemini-1.5-pro" <?= $curModel === 'gemini-1.5-pro' ? 'selected' : '' ?>>Gemini 1.5 Pro (উচ্চমানের ক্রিয়েটিভ)</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <button type="button" onclick="testGeminiConnection()" 
                        class="px-4 py-2 bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5 shadow-2xs cursor-pointer">
                    <ion-icon name="pulse-outline" class="text-base"></ion-icon>
                    <span>Gemini API কানেকশন টেস্ট করুন</span>
                </button>
                <span id="gemini-test-result" class="text-xs font-semibold"></span>
            </div>
        </div>

        <!-- 3. Daily 3 Posts Schedule & Mode Preferences -->
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200 p-6 sm:p-7">
            <div class="border-b border-secondary-100 pb-4 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 shadow-2xs">
                        <ion-icon name="calendar-outline" class="text-xl"></ion-icon>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-secondary-900">প্রতিদিনের ৩টি পোস্টের সময়সূচি ও ডিফল্ট মোড</h3>
                        <p class="text-xs text-secondary-500 mt-0.5">সকাল, দুপুর ও সন্ধ্যার পোস্টের সময় নির্ধারণ করুন এবং এআই বা নন-এআই টেমপ্লেট নির্বাচন করুন।</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-2" for="marketing_morning_time">
                        🌅 সকালের পোস্টের সময়
                    </label>
                    <input type="time" id="marketing_morning_time" name="marketing_morning_time" 
                           value="<?= htmlspecialchars($settings['marketing_morning_time'] ?? '09:00') ?>" 
                           class="w-full px-4 py-2 bg-white border border-secondary-300 rounded-xl text-sm font-bold text-secondary-900 focus:ring-2 focus:ring-emerald-500">
                    <span class="text-[10px] text-secondary-400 mt-1 block">সকালের ফ্রেশ বাজার</span>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-2" for="marketing_afternoon_time">
                        🍛 দুপুরের পোস্টের সময়
                    </label>
                    <input type="time" id="marketing_afternoon_time" name="marketing_afternoon_time" 
                           value="<?= htmlspecialchars($settings['marketing_afternoon_time'] ?? '14:00') ?>" 
                           class="w-full px-4 py-2 bg-white border border-secondary-300 rounded-xl text-sm font-bold text-secondary-900 focus:ring-2 focus:ring-emerald-500">
                    <span class="text-[10px] text-secondary-400 mt-1 block">নিত্যপ্রয়োজনীয় গ্রোসারি অফার</span>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-2" for="marketing_evening_time">
                        🌙 সন্ধ্যার পোস্টের সময়
                    </label>
                    <input type="time" id="marketing_evening_time" name="marketing_evening_time" 
                           value="<?= htmlspecialchars($settings['marketing_evening_time'] ?? '19:00') ?>" 
                           class="w-full px-4 py-2 bg-white border border-secondary-300 rounded-xl text-sm font-bold text-secondary-900 focus:ring-2 focus:ring-emerald-500">
                    <span class="text-[10px] text-secondary-400 mt-1 block">ফ্ল্যাশ সেল ও রাতের ডিল</span>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-2" for="marketing_default_mode">
                        ⚙️ ডিফল্ট জেনারেশন পদ্ধতি
                    </label>
                    <?php $curDefMode = $settings['marketing_default_mode'] ?? 'template_no_ai'; ?>
                    <select id="marketing_default_mode" name="marketing_default_mode" 
                            class="w-full px-3.5 py-2.5 bg-white border border-secondary-300 rounded-xl text-xs font-bold focus:ring-2 focus:ring-emerald-500">
                        <option value="template_no_ai" <?= $curDefMode === 'template_no_ai' ? 'selected' : '' ?>>
                            ⚡ Smart Template (AI ছাড়া - ১০০% ফ্রি ও অটো)
                        </option>
                        <option value="ai_gemini" <?= $curDefMode === 'ai_gemini' ? 'selected' : '' ?>>
                            🤖 Gemini AI (আর্টিফিশিয়াল ইন্টেলিজেন্স)
                        </option>
                    </select>
                    <span class="text-[10px] text-secondary-400 mt-1 block">ড্যাশবোর্ড থেকেও পরিবর্তন করা যাবে</span>
                </div>
            </div>

            <!-- Cron Automation Secret -->
            <div class="mt-6 pt-5 border-t border-secondary-100">
                <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-2" for="marketing_cron_secret">
                    🔑 ক্রোন জব সিক্রেট কী (Cron Job Secret Key - ঐচ্ছিক)
                </label>
                <div class="flex items-center gap-2">
                    <input type="text" id="marketing_cron_secret" name="marketing_cron_secret" 
                           value="<?= htmlspecialchars($settings['marketing_cron_secret'] ?? 'sodai_marketing_2026') ?>" 
                           class="w-full md:w-80 px-4 py-2 bg-white border border-secondary-300 rounded-xl text-xs font-mono">
                    <button type="button" onclick="document.getElementById('marketing_cron_secret').value = Math.random().toString(36).substring(2, 15);" 
                            class="px-3 py-2 bg-secondary-100 hover:bg-secondary-200 text-secondary-700 font-bold text-xs rounded-xl">
                        জেনারেট
                    </button>
                </div>
                <div class="mt-3 p-3 bg-secondary-50 rounded-xl text-xs text-secondary-600 font-mono flex items-center justify-between gap-2 overflow-x-auto">
                    <span><?= (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $base ?>/admin/marketing/cron-trigger?key=<span id="cron-key-preview"><?= htmlspecialchars($settings['marketing_cron_secret'] ?? 'sodai_marketing_2026') ?></span></span>
                    <button type="button" onclick="navigator.clipboard.writeText(this.previousElementSibling.innerText); alert('ক্রোন URL কপি করা হয়েছে!');" 
                            class="px-2.5 py-1 bg-white border border-secondary-200 rounded-lg text-secondary-700 font-sans font-bold hover:bg-secondary-100 shrink-0">
                        কপি করুন
                    </button>
                </div>
                <p class="text-[11px] text-secondary-400 mt-1.5">উক্ত URL টি আপনার হোস্টিং cPanel Cron Jobs এ প্রতিদিন প্রতি ঘন্টায় অথবা দিনে ৩ বার রান করতে সেট করতে পারেন।</p>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="<?= $base ?>/admin/marketing" class="px-5 py-2.5 rounded-xl border border-secondary-300 text-secondary-600 hover:bg-secondary-50 font-bold text-xs transition-colors">
                বাতিল করুন
            </a>
            <button type="submit" class="px-7 py-3 bg-primary-600 hover:bg-primary-700 text-white font-bold text-sm rounded-xl transition-all shadow-md hover:shadow-lg flex items-center gap-2 cursor-pointer">
                <ion-icon name="save-outline" class="text-lg"></ion-icon>
                <span>সেটিংস সেভ করুন</span>
            </button>
        </div>
    </form>
</div>

<!-- ========================================== -->
<!-- 1-CLICK FACEBOOK PAGE SELECTOR MODAL -->
<!-- ========================================== -->
<div id="facebook-pages-modal" class="<?= !empty($oauthPages) ? '' : 'hidden' ?> fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl border border-secondary-200 max-w-xl w-full p-6 sm:p-7 space-y-5 animate-in fade-in zoom-in duration-200">
        <div class="flex items-center justify-between border-b border-secondary-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl shrink-0">
                    <ion-icon name="logo-facebook"></ion-icon>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-secondary-900">আপনার ফেসবুক পেজ নির্বাচন করুন</h3>
                    <p class="text-xs text-secondary-500">যে পেজে দৈনিক ৩টি পোস্ট প্রকাশ করতে চান, সেটি বেছে নিন (আলাদা টোকেন লাগবে না)।</p>
                </div>
            </div>
            <button type="button" onclick="closeFacebookPagesModal()" class="w-8 h-8 rounded-full hover:bg-secondary-100 text-secondary-400 hover:text-secondary-600 flex items-center justify-center cursor-pointer transition-colors">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>

        <div id="facebook-pages-list" class="space-y-3 max-h-96 overflow-y-auto pr-1">
            <?php if (!empty($oauthPages)): ?>
                <?php foreach ($oauthPages as $page): ?>
                    <div class="p-4 rounded-2xl border border-secondary-200 hover:border-blue-500 bg-secondary-50/50 hover:bg-blue-50/30 transition-all flex items-center justify-between gap-3 group">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <?php if (!empty($page['picture'])): ?>
                                <img src="<?= htmlspecialchars($page['picture']) ?>" class="w-12 h-12 rounded-xl object-cover border border-secondary-200 bg-white shrink-0">
                            <?php else: ?>
                                <div class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center text-xl shrink-0 font-bold">
                                    <?= mb_substr($page['name'] ?? 'P', 0, 1) ?>
                                </div>
                            <?php endif; ?>
                            <div class="min-w-0">
                                <h4 class="text-sm font-bold text-secondary-900 truncate"><?= htmlspecialchars($page['name']) ?></h4>
                                <p class="text-[11px] text-secondary-500 truncate"><?= htmlspecialchars($page['category'] ?? 'Facebook Page') ?></p>
                                <p class="text-[10px] text-secondary-400 font-mono mt-0.5">ID: <?= htmlspecialchars($page['id']) ?></p>
                            </div>
                        </div>

                        <button type="button" 
                                onclick='selectAndSavePage(<?= json_encode($page, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>)' 
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition-all shadow-xs shrink-0 flex items-center gap-1.5 cursor-pointer">
                            <ion-icon name="checkmark-outline" class="text-sm"></ion-icon>
                            <span>সিলেক্ট করুন</span>
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="pt-3 border-t border-secondary-100 flex items-center justify-between text-xs text-secondary-500">
            <span class="flex items-center gap-1">
                <ion-icon name="shield-checkmark" class="text-emerald-600 text-sm"></ion-icon>
                <span>সিলেক্ট করলেই সব স্বয়ংক্রিয়ভাবে সেভ হবে</span>
            </span>
            <button type="button" onclick="closeFacebookPagesModal()" class="px-3.5 py-1.5 rounded-lg border border-secondary-300 hover:bg-secondary-50 text-secondary-600 font-bold">
                বন্ধ করুন
            </button>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- LOADING OVERLAY MODAL -->
<!-- ========================================== -->
<div id="facebook-loading-modal" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-6 shadow-2xl flex flex-col items-center justify-center gap-3 max-w-sm w-full text-center">
        <div class="w-12 h-12 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
        <h4 id="facebook-loading-text" class="text-sm font-bold text-secondary-900">ফেসবুক ওপেন হচ্ছে...</h4>
        <p class="text-xs text-secondary-500">অনুগ্রহ করে অপেক্ষা করুন</p>
    </div>
</div>

<!-- Disconnect Form -->
<form id="disconnect-facebook-form" action="<?= $base ?>/admin/marketing/disconnect-facebook" method="POST" class="hidden">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
</form>

<!-- Facebook JavaScript SDK -->
<script>
const fbAppId = <?= json_encode($settings['facebook_app_id'] ?? '') ?>;

window.fbAsyncInit = function() {
    if (fbAppId) {
        FB.init({
            appId      : fbAppId,
            cookie     : true,
            xfbml      : true,
            version    : 'v19.0'
        });
        window.fbSdkLoaded = true;
    }
};

(function(d, s, id){
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement(s); js.id = id;
    js.src = "https://connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

function showLoadingState(text) {
    const el = document.getElementById('facebook-loading-modal');
    const txt = document.getElementById('facebook-loading-text');
    if (txt && text) txt.innerText = text;
    if (el) el.classList.remove('hidden');
}

function hideLoadingState() {
    const el = document.getElementById('facebook-loading-modal');
    if (el) el.classList.add('hidden');
}

function openFacebookPagesModal() {
    const el = document.getElementById('facebook-pages-modal');
    if (el) el.classList.remove('hidden');
}

function closeFacebookPagesModal() {
    const el = document.getElementById('facebook-pages-modal');
    if (el) el.classList.add('hidden');
}

function confirmDisconnectFacebook() {
    if (confirm('আপনি কি নিশ্চিত যে ফেসবুক পেজ সংযোগ বিচ্ছিন্ন করতে চান?')) {
        document.getElementById('disconnect-facebook-form').submit();
    }
}

function toggleApiKeyVisibility(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.type = (el.type === 'password') ? 'text' : 'password';
}

/**
 * 1-Click Login to Facebook and Page Selection
 */
function startFacebookLogin() {
    const currentAppId = document.getElementById('facebook_app_id')?.value.trim() || fbAppId;

    if (!currentAppId) {
        alert('ফেসবুক দিয়ে লগইন করার জন্য অনুগ্রহ করে Facebook App ID টি প্রদান করুন এবং সেটিংস সেভ করুন।');
        document.getElementById('facebook_app_id')?.focus();
        return;
    }

    showLoadingState('ফেসবুক উইন্ডো ওপেন হচ্ছে...');

    // Try Facebook JS SDK Popup
    if (typeof FB !== 'undefined') {
        try {
            FB.login(function(response) {
                if (response.authResponse && response.authResponse.accessToken) {
                    handleUserAccessToken(response.authResponse.accessToken);
                } else {
                    hideLoadingState();
                    // User closed dialog or cancelled
                }
            }, {
                scope: 'pages_show_list,pages_read_engagement,pages_manage_posts',
                return_scopes: true
            });
            return;
        } catch (e) {
            console.warn('FB.login popup error, switching to redirect flow', e);
        }
    }

    // Fallback: Redirect Flow
    window.location.href = '<?= $base ?>/admin/marketing/facebook-auth';
}

/**
 * Fetch Pages with retrieved user access token
 */
async function handleUserAccessToken(userToken) {
    showLoadingState('আপনার পরিচালিত ফেসবুক পেজগুলো লোড হচ্ছে...');

    try {
        const formData = new FormData();
        formData.append('user_access_token', userToken);

        const res = await fetch('<?= $base ?>/admin/marketing/api/fetch-facebook-pages', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();
        hideLoadingState();

        if (data.success && data.pages && data.pages.length > 0) {
            renderPagesList(data.pages);
            openFacebookPagesModal();
        } else {
            // Client-side fallback via FB.api
            if (typeof FB !== 'undefined') {
                FB.api('/me/accounts?fields=id,name,access_token,category,picture{url}', function(resp) {
                    if (resp && resp.data && resp.data.length > 0) {
                        const pages = resp.data.map(p => ({
                            id: p.id,
                            name: p.name,
                            access_token: p.access_token,
                            category: p.category || 'Facebook Page',
                            picture: p.picture?.data?.url || null
                        }));
                        renderPagesList(pages);
                        openFacebookPagesModal();
                    } else {
                        alert(data.message || 'আপনার ফেসবুক আইডিতে কোনো পেজ পাওয়া যায়নি অথবা পেজ পারমিশন দেওয়া হয়নি।');
                    }
                });
            } else {
                alert(data.message || 'আপনার ফেসবুক আইডিতে কোনো পেজ পাওয়া যায়নি।');
            }
        }
    } catch (err) {
        hideLoadingState();
        // Client-side fallback via FB.api
        if (typeof FB !== 'undefined') {
            FB.api('/me/accounts?fields=id,name,access_token,category,picture{url}', function(resp) {
                if (resp && resp.data && resp.data.length > 0) {
                    const pages = resp.data.map(p => ({
                        id: p.id,
                        name: p.name,
                        access_token: p.access_token,
                        category: p.category || 'Facebook Page',
                        picture: p.picture?.data?.url || null
                    }));
                    renderPagesList(pages);
                    openFacebookPagesModal();
                } else {
                    alert('পেজ লোড করতে ত্রুটি: ' + err.message);
                }
            });
        } else {
            alert('পেজ লোড করতে ত্রুটি: ' + err.message);
        }
    }
}

/**
 * Render Pages inside Modal
 */
function renderPagesList(pages) {
    const listEl = document.getElementById('facebook-pages-list');
    if (!listEl) return;

    listEl.innerHTML = '';

    pages.forEach(page => {
        const item = document.createElement('div');
        item.className = 'p-4 rounded-2xl border border-secondary-200 hover:border-blue-500 bg-secondary-50/50 hover:bg-blue-50/30 transition-all flex items-center justify-between gap-3 group';

        const pictureHtml = page.picture 
            ? `<img src="${escapeHtml(page.picture)}" class="w-12 h-12 rounded-xl object-cover border border-secondary-200 bg-white shrink-0">`
            : `<div class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center text-xl shrink-0 font-bold">${escapeHtml((page.name || 'P').charAt(0))}</div>`;

        item.innerHTML = `
            <div class="flex items-center gap-3.5 min-w-0">
                ${pictureHtml}
                <div class="min-w-0">
                    <h4 class="text-sm font-bold text-secondary-900 truncate">${escapeHtml(page.name)}</h4>
                    <p class="text-[11px] text-secondary-500 truncate">${escapeHtml(page.category || 'Facebook Page')}</p>
                    <p class="text-[10px] text-secondary-400 font-mono mt-0.5">ID: ${escapeHtml(page.id)}</p>
                </div>
            </div>
            <button type="button" class="btn-select-page px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition-all shadow-xs shrink-0 flex items-center gap-1.5 cursor-pointer">
                <ion-icon name="checkmark-outline" class="text-sm"></ion-icon>
                <span>সিলেক্ট করুন</span>
            </button>
        `;

        item.querySelector('.btn-select-page').addEventListener('click', () => {
            selectAndSavePage(page);
        });

        listEl.appendChild(item);
    });
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

/**
 * Save selected page to system with 1 click
 */
async function selectAndSavePage(page) {
    showLoadingState(`'${page.name}' পেজটি কানেক্ট করা হচ্ছে...`);

    try {
        const formData = new FormData();
        formData.append('page_id', page.id);
        formData.append('page_name', page.name);
        formData.append('page_access_token', page.access_token);
        if (page.picture) formData.append('page_picture', page.picture);
        formData.append('ajax', '1');

        const res = await fetch('<?= $base ?>/admin/marketing/api/save-facebook-page', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();

        if (data.success) {
            window.location.href = '<?= $base ?>/admin/marketing/settings';
        } else {
            hideLoadingState();
            alert(data.message || 'পেজটি সেভ করতে সমস্যা হয়েছে!');
        }
    } catch (err) {
        hideLoadingState();
        alert('কানেকশন ত্রুটি: ' + err.message);
    }
}

/**
 * Test Gemini Connection
 */
async function testGeminiConnection() {
    const key = document.getElementById('gemini_api_key').value;
    const resEl = document.getElementById('gemini-test-result');
    resEl.innerHTML = '<span class="text-secondary-500 animate-pulse">কানেকশন পরীক্ষা করা হচ্ছে...</span>';

    try {
        const formData = new FormData();
        formData.append('gemini_api_key', key);

        const res = await fetch('<?= $base ?>/admin/marketing/api/test-gemini', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();

        if (data.success) {
            resEl.innerHTML = `<span class="text-emerald-700 font-bold">✅ ${data.message}</span>`;
        } else {
            resEl.innerHTML = `<span class="text-red-600 font-bold">❌ ${data.message}</span>`;
        }
    } catch (err) {
        resEl.innerHTML = `<span class="text-red-600 font-bold">❌ সার্ভার রেসপন্স দেয়নি: ${err.message}</span>`;
    }
}

/**
 * Test Facebook Connection
 */
async function testFacebookConnection() {
    const pageId = document.getElementById('facebook_page_id')?.value || '<?= htmlspecialchars($connectedPageId) ?>';
    const token = document.getElementById('facebook_page_access_token')?.value || '<?= htmlspecialchars($settings['facebook_page_access_token'] ?? '') ?>';
    const resEl = document.getElementById('fb-test-result');
    resEl.innerHTML = '<span class="text-secondary-500 animate-pulse">ফেসবুক পেজ যাচাই করা হচ্ছে...</span>';

    try {
        const formData = new FormData();
        formData.append('facebook_page_id', pageId);
        formData.append('facebook_page_access_token', token);

        const res = await fetch('<?= $base ?>/admin/marketing/api/test-facebook', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();

        if (data.success) {
            resEl.innerHTML = `<span class="text-emerald-700 font-bold">✅ ${data.message} (${data.followers || 0} ফলোয়ার্স)</span>`;
        } else {
            resEl.innerHTML = `<span class="text-red-600 font-bold">❌ ${data.message}</span>`;
        }
    } catch (err) {
        resEl.innerHTML = `<span class="text-red-600 font-bold">❌ সংযোগ পরীক্ষা ব্যর্থ: ${err.message}</span>`;
    }
}
</script>
