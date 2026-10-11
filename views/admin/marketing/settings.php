<?php
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
$connectedPageId = $settings['facebook_page_id'] ?? '';
$connectedPageName = $settings['facebook_page_name'] ?? '';
$connectedPagePicture = $settings['facebook_page_picture'] ?? '';
$hasConnectedPage = !empty($connectedPageId) && !empty($settings['facebook_page_access_token'] ?? '');
?>

<div class="max-w-4xl mx-auto space-y-8 mb-16">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-secondary-200 pb-5">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="<?= $base ?>/admin/marketing" class="text-secondary-400 hover:text-secondary-600 transition-colors">
                    <ion-icon name="arrow-back-outline" class="text-lg"></ion-icon>
                </a>
                <h2 class="text-2xl font-bold text-secondary-900">মার্কেটিং ও ফেসবুক সেটিংস (Marketing Settings)</h2>
            </div>
            <p class="text-secondary-500 text-xs">সরাসরি Facebook সংযোগ, Gemini AI এবং প্রতিদিন ৩টি পোস্টের অটোমেশন ও শিডিউল কনফিগার করুন।</p>
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

        <!-- 1. Direct Facebook Page Integration (No 3rd-Party, Direct Meta Graph API) -->
        <div class="bg-white rounded-2xl shadow-xs border border-secondary-200 p-6 sm:p-7">
            <div class="flex items-center justify-between border-b border-secondary-100 pb-4 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-[#1877F2] shadow-2xs">
                        <ion-icon name="logo-facebook" class="text-2xl"></ion-icon>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-secondary-900">Facebook Page সংযোগ (সরাসরি Meta Graph API)</h3>
                        <p class="text-xs text-secondary-500 mt-0.5">কোনো থার্ড-পার্টি পোর্টাল ছাড়া সরাসরি আপনার সার্ভার থেকে ফেসবুকে অটো-পোস্ট হবে।</p>
                    </div>
                </div>
                <div>
                    <?php if ($hasConnectedPage): ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 border border-emerald-200 text-emerald-700">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            যুক্ত আছে
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 border border-amber-200 text-amber-700">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            কানেক্ট করা নেই
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Connected Page Status Card (If Connected) -->
            <?php if ($hasConnectedPage): ?>
                <div class="bg-emerald-50/40 border border-emerald-200/90 rounded-2xl p-5 mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-2xs">
                    <div class="flex items-center gap-4">
                        <?php if (!empty($connectedPagePicture)): ?>
                            <img src="<?= htmlspecialchars($connectedPagePicture) ?>" alt="Page Avatar" class="w-14 h-14 rounded-2xl border-2 border-white shadow-sm object-cover bg-white">
                        <?php else: ?>
                            <div class="w-14 h-14 rounded-2xl bg-[#1877F2] text-white flex items-center justify-center text-2xl shadow-sm">
                                <ion-icon name="logo-facebook"></ion-icon>
                            </div>
                        <?php endif; ?>
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-base font-bold text-secondary-900"><?= htmlspecialchars($connectedPageName ?: 'Facebook Page') ?></h4>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                    <ion-icon name="checkmark-circle" class="text-xs"></ion-icon>
                                    সক্রিয় ও কানেক্টেড
                                </span>
                            </div>
                            <p class="text-xs text-secondary-600 font-mono mt-0.5">Page ID: <span class="font-bold text-secondary-800"><?= htmlspecialchars($connectedPageId) ?></span></p>
                            <p class="text-[11px] text-emerald-700 font-medium mt-1 flex items-center gap-1">
                                <ion-icon name="shield-checkmark-outline"></ion-icon>
                                <span>পেজ সরাসরি মেটার সাথে সংযুক্ত রয়েছে। প্রতিদিনের ৩টি পোস্ট এই পেজে সরাসরি পাবলিশ হবে।</span>
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:items-end gap-2 shrink-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <button type="button" onclick="testFacebookConnection()" 
                                    class="px-3.5 py-2 bg-white hover:bg-secondary-50 text-secondary-700 border border-secondary-200 rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5 shadow-2xs cursor-pointer">
                                <ion-icon name="pulse-outline" class="text-base text-blue-600"></ion-icon>
                                <span>কানেকশন টেস্ট</span>
                            </button>
                            <button type="button" onclick="confirmDisconnectFacebook()" 
                                    class="px-3 py-2 bg-white hover:bg-red-50 text-red-600 border border-red-200 rounded-xl text-xs font-bold transition-colors flex items-center gap-1 shadow-2xs cursor-pointer">
                                <ion-icon name="unlink-outline" class="text-base"></ion-icon>
                                <span>সংযোগ বিচ্ছিন্ন করুন</span>
                            </button>
                        </div>
                        <div id="fb-top-test-result" class="text-xs font-semibold"></div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Direct Page Setup Form -->
            <div class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-2" for="facebook_page_id">
                            Facebook Page ID <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="facebook_page_id" name="facebook_page_id" 
                               value="<?= htmlspecialchars($settings['facebook_page_id'] ?? '') ?>" 
                               placeholder="যেমন: 104829384910293" 
                               class="w-full px-4 py-2.5 bg-white border border-secondary-300 rounded-xl text-sm font-mono text-secondary-900 focus:ring-2 focus:ring-primary-500">
                        <p class="text-[11px] text-secondary-500 mt-1.5">আপনার ফেসবুক পেজের About বা Settings ➔ Page Transparency সেকশন থেকে Page ID পাবেন।</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-2" for="facebook_page_name">
                            Facebook Page Name <span class="text-secondary-400 font-normal">(ঐচ্ছিক)</span>
                        </label>
                        <input type="text" id="facebook_page_name" name="facebook_page_name" 
                               value="<?= htmlspecialchars($settings['facebook_page_name'] ?? '') ?>" 
                               placeholder="যেমন: সদাই দরকার" 
                               class="w-full px-4 py-2.5 bg-white border border-secondary-300 rounded-xl text-sm text-secondary-900 focus:ring-2 focus:ring-primary-500">
                        <p class="text-[11px] text-secondary-500 mt-1.5">কানেকশন টেস্ট করলে পেজের আসল নাম স্বয়ংক্রিয়ভাবে বসে যাবে।</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-secondary-700 mb-2" for="facebook_page_access_token">
                            Page Access Token (লং-লাইভড টোকেন) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" id="facebook_page_access_token" name="facebook_page_access_token" 
                                   value="<?= htmlspecialchars($settings['facebook_page_access_token'] ?? '') ?>" 
                                   placeholder="EAAG..." 
                                   class="w-full px-4 py-2.5 bg-white border border-secondary-300 rounded-xl text-sm font-mono text-secondary-900 focus:ring-2 focus:ring-primary-500">
                            <button type="button" onclick="toggleApiKeyVisibility('facebook_page_access_token')" 
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-secondary-400 hover:text-secondary-600 cursor-pointer">
                                <ion-icon name="eye-outline" class="text-lg"></ion-icon>
                            </button>
                        </div>
                        <p class="text-[11px] text-secondary-500 mt-1.5">মেটা থেকে প্রাপ্ত পেজের পারমিশন (pages_manage_posts, pages_read_engagement) সহ পার্মানেন্ট অ্যাক্সেস টোকেন।</p>
                    </div>
                </div>

                <!-- 1-Minute Helper Callout -->
                <div class="p-4 rounded-xl bg-blue-50/70 border border-blue-200 text-xs space-y-2">
                    <div class="font-bold text-blue-900 flex items-center gap-1.5">
                        <ion-icon name="information-circle" class="text-base text-blue-600 shrink-0"></ion-icon>
                        <span>মাত্র ১ মিনিটে ফেসবুক পেজের Access Token নেওয়ার নিয়ম:</span>
                    </div>
                    <ol class="list-decimal list-inside space-y-1.5 text-blue-800 leading-relaxed pl-1">
                        <li>মেটার অফিসিয়াল <a href="https://developers.facebook.com/tools/explorer" target="_blank" class="underline font-bold text-blue-950">Graph API Explorer</a> লিংকে যান।</li>
                        <li>উপরে <b>User or Page</b> ড্রপডাউন থেকে আপনার কাঙ্ক্ষিত ফেসবুক পেজটি নির্বাচন করুন।</li>
                        <li><b>Generate Access Token</b> বাটনে চাপ দিন এবং উৎপন্ন হওয়া টোকেনটি কপি করে উপরের বক্সে পেস্ট করে দিন।</li>
                    </ol>
                </div>

                <!-- Auto Post Toggle -->
                <div class="p-4 rounded-xl bg-secondary-50/80 border border-secondary-200 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-secondary-900 block">স্বয়ংক্রিয় শিডিউল পোস্টিং চালু করুন (Auto Post Enabled)</span>
                        <span class="text-[11px] text-secondary-500">চালু থাকলে নির্দিষ্ট সময়ে (সকাল, দুপুর ও সন্ধ্যা) স্বয়ংক্রিয়ভাবে পেজে পোস্ট হয়ে যাবে।</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="facebook_auto_post_enabled" value="1" 
                               <?= ($settings['facebook_auto_post_enabled'] ?? '0') === '1' ? 'checked' : '' ?> 
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-secondary-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-secondary-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                    </label>
                </div>

                <!-- Test Connection Button & Result -->
                <div class="flex items-center justify-between pt-2">
                    <button type="button" onclick="testFacebookConnection()" 
                            class="px-4 py-2.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5 shadow-2xs cursor-pointer">
                        <ion-icon name="pulse-outline" class="text-base"></ion-icon>
                        <span>সংযোগ পরীক্ষা করুন (Test Connection)</span>
                    </button>
                    <span id="fb-test-result" class="text-xs font-semibold"></span>
                </div>
            </div>

            <!-- Optional Custom Facebook App Credentials (Accordion) -->
            <details class="group border border-secondary-200 rounded-xl overflow-hidden mt-6">
                <summary class="p-3.5 bg-secondary-50 hover:bg-secondary-100/70 cursor-pointer text-xs font-bold text-secondary-700 flex items-center justify-between transition-colors select-none">
                    <span class="flex items-center gap-2">
                        <ion-icon name="construct-outline" class="text-base text-secondary-500"></ion-icon>
                        <span>⚙️ নিজস্ব Facebook App ID থাকলে যুক্ত করতে পারেন (Advanced / ঐচ্ছিক)</span>
                    </span>
                    <ion-icon name="chevron-down-outline" class="transition-transform group-open:rotate-180"></ion-icon>
                </summary>
                <div class="p-4 bg-white border-t border-secondary-200 space-y-4">
                    <p class="text-[11px] text-secondary-500">
                        আপনার যদি মেটাতে নিজস্ব ভেরিফাইড App ID ও Secret থাকে, তবে এখানে দিতে পারেন (এটি বাধ্যতামূলক নয়):
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-secondary-700 mb-1" for="facebook_app_id">
                                Facebook App ID
                            </label>
                            <input type="text" id="facebook_app_id" name="facebook_app_id" 
                                   value="<?= htmlspecialchars($settings['facebook_app_id'] ?? '') ?>" 
                                   placeholder="123456789012345" 
                                   class="w-full px-3.5 py-2 bg-white border border-secondary-300 rounded-xl text-xs font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-secondary-700 mb-1" for="facebook_app_secret">
                                Facebook App Secret
                            </label>
                            <div class="relative">
                                <input type="password" id="facebook_app_secret" name="facebook_app_secret" 
                                       value="<?= htmlspecialchars($settings['facebook_app_secret'] ?? '') ?>" 
                                       placeholder="App Secret..." 
                                       class="w-full px-3.5 py-2 bg-white border border-secondary-300 rounded-xl text-xs font-mono">
                                <button type="button" onclick="toggleApiKeyVisibility('facebook_app_secret')" 
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-secondary-400 hover:text-secondary-600 cursor-pointer">
                                    <ion-icon name="eye-outline" class="text-base"></ion-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </details>
        </div>

        <!-- 2. Google Gemini API Configuration -->
        <div class="bg-white rounded-2xl shadow-xs border border-secondary-200 p-6 sm:p-7">
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
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 border border-emerald-200 text-emerald-700">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            API কানেক্টেড
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 border border-amber-200 text-amber-700">
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
                               class="w-full px-4 py-2.5 bg-white border border-secondary-300 rounded-xl text-sm font-mono text-secondary-900 focus:ring-2 focus:ring-purple-500">
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
                    <select id="gemini_model" name="gemini_model" class="w-full px-4 py-2.5 bg-white border border-secondary-300 rounded-xl text-sm font-semibold text-secondary-900 focus:ring-2 focus:ring-purple-500">
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
        <div class="bg-white rounded-2xl shadow-xs border border-secondary-200 p-6 sm:p-7">
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
                            class="w-full px-3.5 py-2.5 bg-white border border-secondary-300 rounded-xl text-xs font-bold text-secondary-900 focus:ring-2 focus:ring-emerald-500">
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
                           class="w-full md:w-80 px-4 py-2 bg-white border border-secondary-300 rounded-xl text-xs font-mono text-secondary-900">
                    <button type="button" onclick="document.getElementById('marketing_cron_secret').value = Math.random().toString(36).substring(2, 15);" 
                            class="px-3 py-2 bg-secondary-100 hover:bg-secondary-200 text-secondary-700 font-bold text-xs rounded-xl cursor-pointer">
                        জেনারেট
                    </button>
                </div>
                <div class="mt-3 p-3 bg-secondary-50 rounded-xl text-xs text-secondary-600 font-mono flex items-center justify-between gap-2 overflow-x-auto">
                    <span><?= (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $base ?>/admin/marketing/cron-trigger?key=<span id="cron-key-preview"><?= htmlspecialchars($settings['marketing_cron_secret'] ?? 'sodai_marketing_2026') ?></span></span>
                    <button type="button" onclick="navigator.clipboard.writeText(this.previousElementSibling.innerText); alert('ক্রোন URL কপি করা হয়েছে!');" 
                            class="px-2.5 py-1 bg-white border border-secondary-200 rounded-lg text-secondary-700 font-sans font-bold hover:bg-secondary-100 shrink-0 cursor-pointer">
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

<!-- Disconnect Form -->
<form id="disconnect-facebook-form" action="<?= $base ?>/admin/marketing/disconnect-facebook" method="POST" class="hidden">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
</form>

<script>
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
 * Test Gemini Connection
 */
async function testGeminiConnection() {
    const key = document.getElementById('gemini_api_key')?.value.trim();
    const resEl = document.getElementById('gemini-test-result');
    const csrfToken = document.querySelector('input[name="csrf_token"]')?.value || '<?= \Core\CSRF::token() ?>';

    if (!key) {
        if (resEl) resEl.innerHTML = '<span class="text-amber-600 font-bold">⚠️ অনুগ্রহ করে Gemini API Key লিখুন</span>';
        return;
    }

    if (resEl) resEl.innerHTML = '<span class="text-secondary-500 animate-pulse flex items-center gap-1.5"><ion-icon name="sync-outline" class="animate-spin text-sm"></ion-icon> <span>কানেকশন পরীক্ষা করা হচ্ছে...</span></span>';

    try {
        const formData = new FormData();
        formData.append('csrf_token', csrfToken);
        formData.append('gemini_api_key', key);

        const res = await fetch('<?= $base ?>/admin/marketing/api/test-gemini', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        });

        const text = await res.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('Invalid server response:', text);
            const cleanErr = text.replace(/<[^>]*>?/gm, ' ').replace(/\s+/g, ' ').trim().substring(0, 150);
            if (resEl) resEl.innerHTML = `<span class="text-red-600 font-bold">❌ সার্ভার রেসপন্স ত্রুটি (${res.status}): ${cleanErr || 'অবৈধ রেসপন্স'}</span>`;
            return;
        }

        if (data.success) {
            if (resEl) resEl.innerHTML = `<span class="text-emerald-700 font-bold">✅ ${data.message}</span>`;
        } else {
            if (resEl) resEl.innerHTML = `<span class="text-red-600 font-bold">❌ ${data.message}</span>`;
        }
    } catch (err) {
        if (resEl) resEl.innerHTML = `<span class="text-red-600 font-bold">❌ সার্ভার রেসপন্স দেয়নি: ${err.message}</span>`;
    }
}

/**
 * Test Facebook Connection Directly with Meta Graph API
 */
async function testFacebookConnection() {
    const pageId = document.getElementById('facebook_page_id')?.value.trim() || '<?= htmlspecialchars($connectedPageId) ?>';
    const token = document.getElementById('facebook_page_access_token')?.value.trim();
    const resEl = document.getElementById('fb-test-result');
    const topResEl = document.getElementById('fb-top-test-result');
    const csrfToken = document.querySelector('input[name="csrf_token"]')?.value || '<?= \Core\CSRF::token() ?>';

    const setHtml = (html) => {
        if (resEl) resEl.innerHTML = html;
        if (topResEl) topResEl.innerHTML = html;
    };

    if (!pageId) {
        setHtml('<span class="text-amber-600 font-bold">⚠️ অনুগ্রহ করে Facebook Page ID দিন</span>');
        return;
    }

    setHtml('<span class="text-secondary-500 animate-pulse flex items-center gap-1.5"><ion-icon name="sync-outline" class="animate-spin text-sm"></ion-icon> <span>মেটা গ্রাফ এপিআই যাচাই করা হচ্ছে...</span></span>');

    try {
        const formData = new FormData();
        formData.append('csrf_token', csrfToken);
        formData.append('facebook_page_id', pageId);
        if (token) {
            formData.append('facebook_page_access_token', token);
        }

        const res = await fetch('<?= $base ?>/admin/marketing/api/test-facebook', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        });

        const text = await res.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('Invalid server response:', text);
            const cleanErr = text.replace(/<[^>]*>?/gm, ' ').replace(/\s+/g, ' ').trim().substring(0, 150);
            setHtml(`<span class="text-red-600 font-bold">❌ সার্ভার রেসপন্স ত্রুটি (${res.status}): ${cleanErr || 'অবৈধ রেসপন্স'}</span>`);
            return;
        }

        if (data.success) {
            setHtml(`<span class="text-emerald-700 font-bold">✅ ${data.message} (${data.followers || 0} ফলোয়ার্স)</span>`);
            // Auto fill Page Name if empty
            const nameInput = document.getElementById('facebook_page_name');
            if (nameInput && (!nameInput.value.trim() || nameInput.value === 'Facebook Page') && data.page_name) {
                nameInput.value = data.page_name;
            }
        } else {
            setHtml(`<span class="text-red-600 font-bold">❌ ${data.message}</span>`);
        }
    } catch (err) {
        setHtml(`<span class="text-red-600 font-bold">❌ সংযোগ পরীক্ষা ব্যর্থ: ${err.message}</span>`);
    }
}
</script>
