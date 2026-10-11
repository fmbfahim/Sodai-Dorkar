<?php
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
?>

<div class="space-y-8 mb-16">
    <!-- Top Header & Status Bar -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-secondary-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 mb-1 flex-wrap">
                <span class="p-2 bg-blue-600 text-white rounded-xl flex items-center justify-center text-lg shadow-xs">
                    <ion-icon name="logo-facebook"></ion-icon>
                </span>
                <h2 class="text-2xl font-bold text-secondary-900 tracking-tight">Facebook Marketing Automation</h2>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-blue-100 text-blue-800">
                    প্রতিদিন ৩টি পোস্ট
                </span>
            </div>
            <p class="text-secondary-500 text-xs">
                প্রতিদিন সকাল, দুপুর ও সন্ধ্যায় ফেসবুকে পোস্ট করার জন্য ছবি ও আকর্ষণীয় সেলস ক্যাপশন তৈরি ও অটো-পোস্টিং সিস্টেম।
            </p>
        </div>

        <!-- Status Badges & Quick Action -->
        <div class="flex items-center gap-3 flex-wrap">
            <!-- Gemini Status -->
            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border <?= $isGeminiConfigured ? 'bg-purple-50 border-purple-200 text-purple-800' : 'bg-secondary-50 border-secondary-200 text-secondary-600' ?> text-xs font-bold">
                <ion-icon name="sparkles" class="<?= $isGeminiConfigured ? 'text-purple-600' : 'text-secondary-400' ?>"></ion-icon>
                <span>Gemini AI: <?= $isGeminiConfigured ? 'সক্রিয়' : 'কনফিগ প্রয়োজন' ?></span>
            </div>

            <!-- Facebook Status -->
            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border <?= $isFacebookConfigured ? 'bg-blue-50 border-blue-200 text-blue-800' : 'bg-amber-50 border-amber-200 text-amber-800' ?> text-xs font-bold">
                <ion-icon name="logo-facebook" class="<?= $isFacebookConfigured ? 'text-blue-600' : 'text-amber-500' ?>"></ion-icon>
                <span>FB পেজ: <?= $isFacebookConfigured ? (!empty($settings['facebook_page_name']) ? htmlspecialchars($settings['facebook_page_name']) : 'কানেক্টেড') : 'কানেক্ট করুন' ?></span>
            </div>

            <!-- Settings Button -->
            <a href="<?= $base ?>/admin/marketing/settings" 
               class="px-3.5 py-2 rounded-xl bg-secondary-100 hover:bg-secondary-200 text-secondary-700 font-bold text-xs transition-colors flex items-center gap-1.5 shadow-2xs">
                <ion-icon name="settings-outline" class="text-base"></ion-icon>
                <span>সেটিংস</span>
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

    <!-- Action Toolbar: 1-Click Generator & Modal Triggers -->
    <div class="bg-gradient-to-r from-emerald-500/10 via-blue-500/10 to-purple-500/10 border border-emerald-200/60 rounded-2xl p-5 flex flex-col md:flex-row items-center justify-between gap-4 shadow-xs">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-2xl shadow-sm shrink-0">
                <ion-icon name="flash"></ion-icon>
            </div>
            <div>
                <h3 class="text-sm sm:text-base font-bold text-secondary-900">আজকের ৩টি পোস্ট স্বয়ংক্রিয়ভাবে তৈরি করুন (Daily 3 Posts Generator)</h3>
                <p class="text-xs text-secondary-500 mt-0.5">১ ক্লিকে সকাল, দুপুর ও সন্ধ্যার জন্য ছবি, অফার ও বিক্রয়মুখী বাংলা ক্যাপশন প্রস্তুত হয়ে যাবে।</p>
            </div>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap shrink-0">
            <!-- Generate using No-AI Template -->
            <form action="<?= $base ?>/admin/marketing/generate-daily" method="POST" class="inline">
                <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                <input type="hidden" name="mode" value="template_no_ai">
                <button type="submit" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition-all shadow-sm hover:shadow-md flex items-center gap-1.5 cursor-pointer">
                    <ion-icon name="sparkles-outline" class="text-base"></ion-icon>
                    <span>⚡ Smart Template (AI ছাড়া - ১০০% ফ্রি)</span>
                </button>
            </form>

            <!-- Generate using Gemini AI -->
            <form action="<?= $base ?>/admin/marketing/generate-daily" method="POST" class="inline">
                <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                <input type="hidden" name="mode" value="ai_gemini">
                <button type="submit" class="px-4 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold text-xs rounded-xl transition-all shadow-sm hover:shadow-md flex items-center gap-1.5 cursor-pointer">
                    <ion-icon name="hardware-chip-outline" class="text-base"></ion-icon>
                    <span>🤖 Gemini AI দিয়ে ৩টি পোস্ট বানান</span>
                </button>
            </form>

            <!-- Facebook Test Post Button -->
            <button type="button" onclick="testFacebookPost()" class="px-4 py-2.5 bg-blue-50 border border-blue-200 hover:bg-blue-100 text-blue-700 font-bold text-xs rounded-xl transition-all shadow-2xs flex items-center gap-1.5 cursor-pointer">
                <ion-icon name="paper-plane-outline" class="text-base text-blue-600"></ion-icon>
                <span>🧪 টেস্ট পোস্ট</span>
            </button>

            <!-- Custom Post Button -->
            <button type="button" onclick="openCustomPostModal()" class="px-4 py-2.5 bg-white border border-secondary-300 hover:bg-secondary-50 text-secondary-700 font-bold text-xs rounded-xl transition-all shadow-2xs flex items-center gap-1.5 cursor-pointer">
                <ion-icon name="add-circle-outline" class="text-base text-primary-600"></ion-icon>
                <span>কাস্টম পোস্ট</span>
            </button>
        </div>
    </div>

    <!-- 3 Daily Posts Container (Morning, Afternoon, Evening) -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-secondary-900 flex items-center gap-2">
                <ion-icon name="time-outline" class="text-primary-600 text-lg"></ion-icon>
                <span>আজকের ৩টি পোস্ট (Today's Scheduled Posts - <?= date('d M, Y') ?>)</span>
            </h3>
            <span class="text-xs text-secondary-500 font-medium">নিচে প্রতিটি স্লটের ব্যানার ও ক্যাপশন প্রস্তুত রয়েছে</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <?php
            $slotConfigs = [
                'morning' => [
                    'label' => '১. সকালের পোস্ট (Morning Post)',
                    'sub' => 'ফ্রেশ সবজি, মাছ-মাংস ও নাস্তা',
                    'icon' => 'sunny-outline',
                    'color' => 'amber',
                    'badgeBg' => 'bg-amber-50 text-amber-800 border-amber-200',
                    'defaultTime' => $settings['marketing_morning_time'] ?? '09:00'
                ],
                'afternoon' => [
                    'label' => '২. দুপুরের পোস্ট (Afternoon Post)',
                    'sub' => 'রান্নার তেল, চাল, ডাল ও নিত্যপণ্য',
                    'icon' => 'restaurant-outline',
                    'color' => 'blue',
                    'badgeBg' => 'bg-blue-50 text-blue-800 border-blue-200',
                    'defaultTime' => $settings['marketing_afternoon_time'] ?? '14:00'
                ],
                'evening' => [
                    'label' => '৩. সন্ধ্যার পোস্ট (Evening Post)',
                    'sub' => 'ফ্ল্যাশ সেল, রাতের ডিল ও ফ্যামিলি প্যাক',
                    'icon' => 'moon-outline',
                    'color' => 'purple',
                    'badgeBg' => 'bg-purple-50 text-purple-800 border-purple-200',
                    'defaultTime' => $settings['marketing_evening_time'] ?? '19:00'
                ],
            ];

            foreach ($slotConfigs as $slotKey => $slotMeta):
                $post = $todayPosts[$slotKey] ?? null;
            ?>
                <!-- Slot Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-secondary-200/80 overflow-hidden flex flex-col justify-between hover:shadow-md transition-shadow">
                    
                    <!-- Slot Header -->
                    <div class="p-4 border-b border-secondary-100 flex items-center justify-between gap-2 bg-secondary-50/50">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-xl bg-<?= $slotMeta['color'] ?>-100 text-<?= $slotMeta['color'] ?>-700 flex items-center justify-center text-lg font-bold">
                                <ion-icon name="<?= $slotMeta['icon'] ?>"></ion-icon>
                            </span>
                            <div>
                                <h4 class="text-xs font-bold text-secondary-900"><?= $slotMeta['label'] ?></h4>
                                <span class="text-[10px] text-secondary-500 block"><?= $slotMeta['sub'] ?></span>
                            </div>
                        </div>

                        <!-- Schedule Time & Status Pill -->
                        <div class="text-right">
                            <span class="text-[11px] font-bold text-secondary-700 block">⏰ <?= htmlspecialchars($post['schedule_time'] ?? $slotMeta['defaultTime']) ?></span>
                            <?php if ($post): ?>
                                <?php if ($post['status'] === 'posted'): ?>
                                    <span class="inline-flex items-center gap-1 text-[9px] font-black px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">
                                        <ion-icon name="checkmark-done-circle"></ion-icon> ফেসবুকে পোস্ট করা হয়েছে
                                    </span>
                                <?php elseif ($post['status'] === 'failed'): ?>
                                    <span class="inline-flex items-center gap-1 text-[9px] font-black px-2 py-0.5 rounded-full bg-red-100 text-red-800" title="<?= htmlspecialchars($post['error_message'] ?? '') ?>">
                                        <ion-icon name="alert-circle"></ion-icon> পোস্ট ব্যর্থ
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 text-[9px] font-black px-2 py-0.5 rounded-full bg-blue-100 text-blue-800">
                                        <ion-icon name="time"></ion-icon> প্রস্তুত (Draft)
                                    </span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1 text-[9px] font-black px-2 py-0.5 rounded-full bg-secondary-100 text-secondary-600">
                                    অপেক্ষমান
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($post): ?>
                        <!-- Post Content Body -->
                        <div class="p-4 space-y-3.5 flex-1 flex flex-col">
                            
                            <!-- Banner Image Area -->
                            <div class="relative rounded-xl overflow-hidden bg-secondary-100 aspect-square border border-secondary-200 group">
                                <?php if (!empty($post['image_path'])): ?>
                                    <img src="<?= htmlspecialchars($post['image_path']) ?>" 
                                         id="banner-preview-<?= $post['id'] ?>"
                                         alt="Marketing Banner" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <?php else: ?>
                                    <div class="w-full h-full flex flex-col items-center justify-center text-secondary-400 p-4 text-center">
                                        <ion-icon name="image-outline" class="text-4xl mb-2"></ion-icon>
                                        <span class="text-xs font-semibold">কোনো ব্যানার নেই</span>
                                    </div>
                                <?php endif; ?>

                                <!-- Image Action Overlay -->
                                <div class="absolute inset-0 bg-secondary-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2 p-3">
                                    <button type="button" 
                                            onclick="openBannerStudio(<?= $post['id'] ?>, <?= htmlspecialchars(json_encode($post)) ?>)"
                                            class="px-3 py-1.5 bg-white text-secondary-800 font-bold text-xs rounded-xl hover:bg-primary-50 hover:text-primary-600 transition-all shadow-md flex items-center gap-1 cursor-pointer">
                                        <ion-icon name="color-palette-outline" class="text-base"></ion-icon>
                                        <span>ব্যানার এডিটর</span>
                                    </button>
                                    <?php if (!empty($post['image_path'])): ?>
                                        <a href="<?= htmlspecialchars($post['image_path']) ?>" download="sodai_marketing_<?= $slotKey ?>.png" 
                                           class="px-3 py-1.5 bg-emerald-600 text-white font-bold text-xs rounded-xl hover:bg-emerald-700 transition-all shadow-md flex items-center gap-1">
                                            <ion-icon name="download-outline" class="text-base"></ion-icon>
                                            <span>ডাউনলোড</span>
                                        </a>
                                    <?php endif; ?>
                                </div>

                                <!-- Mode Badge -->
                                <div class="absolute top-2.5 left-2.5">
                                    <?php if ($post['mode'] === 'ai_gemini'): ?>
                                        <span class="px-2 py-0.5 rounded-lg bg-purple-900/80 text-white font-bold text-[10px] backdrop-blur-xs flex items-center gap-1 shadow-sm">
                                            <ion-icon name="sparkles" class="text-purple-300"></ion-icon> Gemini AI
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 py-0.5 rounded-lg bg-emerald-900/80 text-white font-bold text-[10px] backdrop-blur-xs flex items-center gap-1 shadow-sm">
                                            <ion-icon name="flash" class="text-emerald-300"></ion-icon> Smart Template
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Headline & Product Tag -->
                            <div class="flex items-center justify-between gap-2">
                                <div class="min-w-0 flex-1">
                                    <span class="text-xs font-bold text-secondary-900 block truncate" title="<?= htmlspecialchars($post['headline'] ?? '') ?>">
                                        <?= htmlspecialchars($post['headline'] ?? '🔥 আজকের সেরা মেগা অফার!') ?>
                                    </span>
                                    <?php if (!empty($post['product_name'])): ?>
                                        <span class="text-[11px] text-emerald-700 font-semibold truncate block">
                                            🏷️ <?= htmlspecialchars($post['product_name']) ?> (৳<?= number_format(floatval($post['sell_price']), 2) ?>)
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <button type="button" 
                                        onclick="openBannerStudio(<?= $post['id'] ?>, <?= htmlspecialchars(json_encode($post)) ?>)"
                                        class="px-2 py-1 text-[11px] font-bold text-primary-700 bg-primary-50 hover:bg-primary-100 rounded-lg transition-colors shrink-0">
                                    🎨 ব্যানার বানান
                                </button>
                            </div>

                            <!-- Caption Box with Copy Button -->
                            <div class="relative flex-1">
                                <label class="text-[10px] uppercase font-bold text-secondary-500 mb-1 flex items-center justify-between">
                                    <span>ফেসবুক ক্যাপশন:</span>
                                    <button type="button" onclick="copyCaption(this, 'caption-<?= $post['id'] ?>')" 
                                            class="text-primary-600 hover:text-primary-800 font-bold cursor-pointer inline-flex items-center gap-1">
                                        <ion-icon name="copy-outline"></ion-icon> কপি করুন
                                    </button>
                                </label>
                                <textarea id="caption-<?= $post['id'] ?>" readonly rows="5"
                                          class="w-full p-2.5 bg-secondary-50 border border-secondary-200 rounded-xl text-xs text-secondary-800 font-sans leading-relaxed resize-none focus:outline-none focus:ring-1 focus:ring-primary-500 select-all"><?= htmlspecialchars($post['caption']) ?></textarea>
                            </div>

                        </div>

                        <!-- Card Action Footer -->
                        <div class="p-4 bg-secondary-50/70 border-t border-secondary-100 space-y-2">
                            <!-- Main Post to Facebook Button -->
                            <?php if ($post['status'] === 'posted'): ?>
                                <div class="flex items-center justify-between gap-2">
                                    <a href="https://facebook.com/<?= htmlspecialchars($post['fb_post_id'] ?? '') ?>" target="_blank"
                                       class="flex-1 py-2 px-3 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 rounded-xl text-xs font-bold text-center transition-colors flex items-center justify-center gap-1.5 shadow-2xs">
                                        <ion-icon name="logo-facebook" class="text-base"></ion-icon>
                                        <span>ফেসবুকে পোস্টটি দেখুন</span>
                                        <ion-icon name="open-outline" class="text-xs"></ion-icon>
                                    </a>
                                    <!-- Repost Option -->
                                    <form action="<?= $base ?>/admin/marketing/post-to-facebook" method="POST" onsubmit="return confirm('পোস্টটি পুনরায় ফেসবুকে পাবলিশ করতে চান?');">
                                        <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                                        <input type="hidden" name="id" value="<?= $post['id'] ?>">
                                        <button type="submit" title="পুনরায় পোস্ট করুন" class="p-2 text-secondary-500 hover:text-blue-600 bg-white border border-secondary-200 rounded-xl hover:bg-secondary-50 transition-colors cursor-pointer">
                                            <ion-icon name="sync-outline" class="text-base"></ion-icon>
                                        </button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <form action="<?= $base ?>/admin/marketing/post-to-facebook" method="POST" onsubmit="return confirm('এখনই এই পোস্টটি আপনার ফেসবুক পেজে পাবলিশ করতে চান?');">
                                    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                                    <input type="hidden" name="id" value="<?= $post['id'] ?>">
                                    <button type="submit" 
                                            class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition-all shadow-xs hover:shadow-md flex items-center justify-center gap-2 cursor-pointer">
                                        <ion-icon name="logo-facebook" class="text-base"></ion-icon>
                                        <span>ফেসবুকে পোস্ট করুন (Post Now)</span>
                                    </button>
                                </form>
                            <?php endif; ?>

                            <!-- Bottom Row: Download, Copy, Delete -->
                            <div class="flex items-center justify-between pt-1">
                                <button type="button" onclick="copyCaption(this, 'caption-<?= $post['id'] ?>')" 
                                        class="text-xs text-secondary-600 hover:text-primary-600 font-semibold flex items-center gap-1 cursor-pointer">
                                    <ion-icon name="copy-outline"></ion-icon> ক্যাপশন কপি
                                </button>

                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="openBannerStudio(<?= $post['id'] ?>, <?= htmlspecialchars(json_encode($post)) ?>)" 
                                            class="text-xs text-primary-600 hover:text-primary-700 font-semibold flex items-center gap-1 cursor-pointer">
                                        <ion-icon name="color-palette-outline"></ion-icon> ডিজাইন
                                    </button>

                                    <form action="<?= $base ?>/admin/marketing/delete-post" method="POST" onsubmit="return confirm('এই পোস্টটি ডিলিট করতে চান?');" class="inline">
                                        <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                                        <input type="hidden" name="id" value="<?= $post['id'] ?>">
                                        <button type="submit" class="text-secondary-400 hover:text-red-600 p-1 rounded-md transition-colors cursor-pointer" title="মুছে ফেলুন">
                                            <ion-icon name="trash-outline" class="text-base"></ion-icon>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                    <?php else: ?>
                        <!-- Empty Slot Placeholder -->
                        <div class="p-8 text-center flex-1 flex flex-col items-center justify-center text-secondary-400">
                            <span class="w-16 h-16 rounded-full bg-secondary-100 flex items-center justify-center text-3xl mb-3 text-secondary-400">
                                <ion-icon name="<?= $slotMeta['icon'] ?>"></ion-icon>
                            </span>
                            <h5 class="text-sm font-bold text-secondary-700 mb-1">আজকের <?= $slotMeta['label'] ?> তৈরি করা হয়নি</h5>
                            <p class="text-xs text-secondary-400 max-w-xs mb-4">উপরের 'স্মার্ট টেমপ্লেট' বা 'Gemini AI' বাটনে ক্লিক করে স্বয়ংক্রিয়ভাবে তৈরি করুন।</p>
                            
                            <form action="<?= $base ?>/admin/marketing/generate-daily" method="POST">
                                <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                                <input type="hidden" name="mode" value="template_no_ai">
                                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-bold text-xs rounded-xl shadow-xs transition-all flex items-center gap-1.5 cursor-pointer">
                                    <ion-icon name="sparkles"></ion-icon>
                                    <span>এখনই পোস্ট বানান</span>
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>

                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Recent Posts & Publication Log Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-secondary-200/80 p-6">
        <div class="flex items-center justify-between mb-4 border-b border-secondary-100 pb-3">
            <h3 class="text-base font-bold text-secondary-900 flex items-center gap-2">
                <ion-icon name="list-outline" class="text-primary-600 text-lg"></ion-icon>
                <span>সাম্প্রতিক পোস্ট হিস্টোরি (Recent Marketing Posts)</span>
            </h3>
            <span class="text-xs text-secondary-500 font-medium"><?= count($recentPosts) ?> টি পোস্ট রেকর্ড পাওয়া গেছে</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-secondary-50 text-secondary-600 uppercase font-bold text-[11px] border-b border-secondary-200">
                    <tr>
                        <th class="px-4 py-3">তারিখ ও স্লট</th>
                        <th class="px-4 py-3">পণ্য</th>
                        <th class="px-4 py-3">পদ্ধতি (Mode)</th>
                        <th class="px-4 py-3">স্ট্যাটাস</th>
                        <th class="px-4 py-3">ফেসবুক লিংক</th>
                        <th class="px-4 py-3 text-right">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-secondary-100 font-medium">
                    <?php if (!empty($recentPosts)): ?>
                        <?php foreach ($recentPosts as $rp): ?>
                            <tr class="hover:bg-secondary-50/60 transition-colors">
                                <td class="px-4 py-3 font-semibold text-secondary-800">
                                    <div><?= date('d M, Y', strtotime($rp['post_date'])) ?></div>
                                    <span class="text-[10px] text-secondary-400 capitalize">⏰ <?= htmlspecialchars($rp['slot_type']) ?> (<?= htmlspecialchars($rp['schedule_time'] ?? '') ?>)</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <?php if (!empty($rp['image_path'])): ?>
                                            <img src="<?= htmlspecialchars($rp['image_path']) ?>" class="w-8 h-8 rounded-lg object-cover border border-secondary-200">
                                        <?php endif; ?>
                                        <div>
                                            <span class="font-bold text-secondary-900 block truncate max-w-xs"><?= htmlspecialchars($rp['product_name'] ?? 'কাস্টম পণ্য') ?></span>
                                            <span class="text-[10px] text-emerald-700 font-semibold">৳<?= number_format(floatval($rp['sell_price'] ?? 0), 2) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <?php if ($rp['mode'] === 'ai_gemini'): ?>
                                        <span class="px-2 py-0.5 rounded-full bg-purple-100 text-purple-800 text-[10px] font-bold">🤖 Gemini AI</span>
                                    <?php else: ?>
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">⚡ No-AI Template</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <?php if ($rp['status'] === 'posted'): ?>
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">✅ Posted</span>
                                    <?php elseif ($rp['status'] === 'failed'): ?>
                                        <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-800 text-[10px] font-bold" title="<?= htmlspecialchars($rp['error_message'] ?? '') ?>">❌ Failed</span>
                                    <?php else: ?>
                                        <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-bold">⏳ Draft</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <?php if (!empty($rp['fb_post_id'])): ?>
                                        <a href="https://facebook.com/<?= htmlspecialchars($rp['fb_post_id']) ?>" target="_blank" 
                                           class="text-blue-600 hover:underline font-bold inline-flex items-center gap-1 text-[11px]">
                                            <span>পোস্ট দেখুন</span>
                                            <ion-icon name="open-outline" class="text-xs"></ion-icon>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-secondary-400 text-[11px]">পোস্ট হয়নি</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                        <?php if ($rp['status'] !== 'posted'): ?>
                                            <form action="<?= $base ?>/admin/marketing/post-to-facebook" method="POST" class="inline" onsubmit="return confirm('পোস্টটি এখনই ফেসবুক পেজে পাবলিশ করবেন?');">
                                                <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                                                <input type="hidden" name="id" value="<?= $rp['id'] ?>">
                                                <button type="submit" class="text-xs bg-blue-600 hover:bg-blue-700 text-white font-bold px-2.5 py-1.5 rounded-lg transition-all shadow-2xs flex items-center gap-1 cursor-pointer">
                                                    <ion-icon name="paper-plane-outline"></ion-icon>
                                                    <span>পাবলিশ করুন</span>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <button type="button" onclick="openBannerStudio(<?= $rp['id'] ?>, <?= htmlspecialchars(json_encode($rp)) ?>)" 
                                                class="text-primary-600 hover:text-primary-800 font-bold px-2 py-1 rounded hover:bg-primary-50 transition-colors">
                                            ব্যানার এডিটর
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-secondary-400">
                                কোনো পূর্ববর্তী পোস্ট রেকর্ড পাওয়া যায়নি।
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 1. HTML5 Canvas Banner Designer Studio Modal -->
<!-- ========================================== -->
<div id="bannerStudioModal" class="fixed inset-0 z-50 hidden bg-secondary-900/60 backdrop-blur-xs overflow-y-auto flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-4xl w-full p-6 sm:p-7 shadow-2xl border border-secondary-200 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-secondary-200 pb-4 mb-5">
            <div class="flex items-center gap-2.5">
                <span class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl shadow-2xs">
                    <ion-icon name="color-palette-outline"></ion-icon>
                </span>
                <div>
                    <h3 class="text-lg font-bold text-secondary-900">Facebook Ad Banner Studio (ব্যানার মেকার)</h3>
                    <p class="text-xs text-secondary-500">এইচডি রেজোলিউশনে ফেসবুক পোস্টের জন্য ব্যানার তৈরি ও ডাউনলোড করুন।</p>
                </div>
            </div>
            <button type="button" onclick="closeBannerStudio()" class="p-2 text-secondary-400 hover:text-secondary-600 rounded-xl hover:bg-secondary-100 cursor-pointer">
                <ion-icon name="close-outline" class="text-2xl"></ion-icon>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
            
            <!-- Left: Live Canvas Preview -->
            <div class="flex flex-col items-center">
                <div class="w-full max-w-[380px] aspect-square rounded-2xl overflow-hidden shadow-lg border border-secondary-200 bg-secondary-100 relative">
                    <canvas id="bannerCanvas" width="1080" height="1080" class="w-full h-full block object-contain"></canvas>
                </div>
                <div class="mt-3 flex items-center gap-2.5 w-full max-w-[380px]">
                    <button type="button" onclick="downloadCanvasBanner()" 
                            class="flex-1 py-2.5 px-3 bg-secondary-100 hover:bg-secondary-200 text-secondary-800 font-bold text-xs rounded-xl transition-colors flex items-center justify-center gap-1.5 shadow-2xs cursor-pointer">
                        <ion-icon name="download-outline" class="text-base"></ion-icon>
                        <span>ডাউনলোড HD PNG</span>
                    </button>
                    <button type="button" id="saveBannerBtn" onclick="saveBannerToServer()" 
                            class="flex-1 py-2.5 px-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition-colors flex items-center justify-center gap-1.5 shadow-sm cursor-pointer">
                        <ion-icon name="cloud-upload-outline" class="text-base"></ion-icon>
                        <span>পোস্টে সেভ করুন</span>
                    </button>
                </div>
                <span id="banner-save-status" class="text-[11px] font-bold mt-2"></span>
            </div>

            <!-- Right: Banner Customization Controls -->
            <div class="space-y-4 text-xs font-semibold">
                <input type="hidden" id="studio_post_id" value="">
                <input type="hidden" id="studio_product_image_url" value="">

                <!-- Template Theme Picker (5 Grocery Themes) -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-secondary-700">
                            🎨 ব্যানার থিম ও কালার টেমপ্লেট
                        </label>
                        <span class="text-[10px] text-emerald-600 font-bold">৫টি ব্রেন্ডেড থিম</span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2" id="themeButtonsGrid">
                        <button type="button" onclick="setStudioTheme('emerald')" class="theme-btn p-2 rounded-xl border border-emerald-500 bg-emerald-50 text-emerald-950 font-bold flex items-center gap-1.5 hover:shadow-xs transition-all text-left text-[11px] cursor-pointer" data-theme="emerald">
                            <span class="w-3.5 h-3.5 rounded-full bg-emerald-600 shrink-0"></span>
                            <span class="truncate">🌿 Farm Fresh</span>
                        </button>
                        <button type="button" onclick="setStudioTheme('ruby')" class="theme-btn p-2 rounded-xl border border-secondary-200 bg-white text-secondary-700 font-bold flex items-center gap-1.5 hover:shadow-xs transition-all text-left text-[11px] cursor-pointer" data-theme="ruby">
                            <span class="w-3.5 h-3.5 rounded-full bg-red-600 shrink-0"></span>
                            <span class="truncate">💥 Mega Deal</span>
                        </button>
                        <button type="button" onclick="setStudioTheme('amber')" class="theme-btn p-2 rounded-xl border border-secondary-200 bg-white text-secondary-700 font-bold flex items-center gap-1.5 hover:shadow-xs transition-all text-left text-[11px] cursor-pointer" data-theme="amber">
                            <span class="w-3.5 h-3.5 rounded-full bg-amber-500 shrink-0"></span>
                            <span class="truncate">☀️ Morning Fresh</span>
                        </button>
                        <button type="button" onclick="setStudioTheme('navy')" class="theme-btn p-2 rounded-xl border border-secondary-200 bg-white text-secondary-700 font-bold flex items-center gap-1.5 hover:shadow-xs transition-all text-left text-[11px] cursor-pointer" data-theme="navy">
                            <span class="w-3.5 h-3.5 rounded-full bg-slate-900 shrink-0"></span>
                            <span class="truncate">🌙 Midnight Flash</span>
                        </button>
                        <button type="button" onclick="setStudioTheme('royal')" class="theme-btn p-2 rounded-xl border border-secondary-200 bg-white text-secondary-700 font-bold flex items-center gap-1.5 hover:shadow-xs transition-all text-left text-[11px] cursor-pointer" data-theme="royal">
                            <span class="w-3.5 h-3.5 rounded-full bg-purple-700 shrink-0"></span>
                            <span class="truncate">🍇 Royal Organic</span>
                        </button>
                    </div>
                </div>

                <!-- Top Headline with Quick Presets -->
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-secondary-700 mb-1" for="studio_headline">
                        শীর্ষ আকর্ষণ / হেডলাইন
                    </label>
                    <input type="text" id="studio_headline" oninput="drawBannerCanvas()" 
                           value="🔥 আজকের সেরা মেগা অফার!" 
                           class="w-full px-3 py-2 bg-white border border-secondary-300 rounded-xl text-xs font-bold text-secondary-900 focus:ring-2 focus:ring-emerald-500">
                    <div class="flex items-center gap-1.5 mt-1.5 overflow-x-auto pb-1 text-[10px]">
                        <button type="button" onclick="setHeadlinePreset('🔥 আজকের সেরা মেগা অফার!')" class="px-2 py-0.5 rounded-lg bg-secondary-100 hover:bg-secondary-200 text-secondary-700 font-semibold shrink-0 cursor-pointer">মেগা অফার</button>
                        <button type="button" onclick="setHeadlinePreset('🌿 সকালের তাজা বাজার অফার!')" class="px-2 py-0.5 rounded-lg bg-secondary-100 hover:bg-secondary-200 text-secondary-700 font-semibold shrink-0 cursor-pointer">তাজা বাজার</button>
                        <button type="button" onclick="setHeadlinePreset('💥 বিশেষ ধামাকা ডিসকাউন্ট ডিল!')" class="px-2 py-0.5 rounded-lg bg-secondary-100 hover:bg-secondary-200 text-secondary-700 font-semibold shrink-0 cursor-pointer">ধামাকা ডিল</button>
                        <button type="button" onclick="setHeadlinePreset('⚡ ফ্ল্যাশ ডিল - সীমিত স্টক!')" class="px-2 py-0.5 rounded-lg bg-secondary-100 hover:bg-secondary-200 text-secondary-700 font-semibold shrink-0 cursor-pointer">ফ্ল্যাশ ডিল</button>
                    </div>
                </div>

                <!-- Product Title -->
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-secondary-700 mb-1" for="studio_prod_name">
                        পণ্যের নাম
                    </label>
                    <input type="text" id="studio_prod_name" oninput="drawBannerCanvas()" 
                           value="সদাই দরকার প্রিমিয়াম গ্রোসারি" 
                           class="w-full px-3 py-2 bg-white border border-secondary-300 rounded-xl text-xs font-bold text-secondary-900 focus:ring-2 focus:ring-emerald-500">
                </div>

                <!-- Pricing Row with Auto Savings Preview -->
                <div class="bg-secondary-50/70 p-3 rounded-2xl border border-secondary-200 space-y-2">
                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-secondary-700 mb-1" for="studio_sell_price">
                                অফার মূল্য (৳)
                            </label>
                            <input type="number" step="0.01" id="studio_sell_price" oninput="onStudioPriceChange()" 
                                   value="120" 
                                   class="w-full px-2.5 py-1.5 bg-white border border-secondary-300 rounded-xl text-xs font-black text-emerald-700">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-secondary-700 mb-1" for="studio_regular_price">
                                রেগুলার MRP (৳)
                            </label>
                            <input type="number" step="0.01" id="studio_regular_price" oninput="onStudioPriceChange()" 
                                   value="150" 
                                   class="w-full px-2.5 py-1.5 bg-white border border-secondary-300 rounded-xl text-xs font-bold text-secondary-600">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-secondary-700 mb-1" for="studio_unit">
                                পরিমাণ / ইউনিট
                            </label>
                            <input type="text" id="studio_unit" oninput="drawBannerCanvas()" 
                                   value="১ কেজি" 
                                   class="w-full px-2.5 py-1.5 bg-white border border-secondary-300 rounded-xl text-xs font-bold text-secondary-700">
                        </div>
                    </div>
                    <div id="studio-savings-preview" class="text-[11px] font-bold text-emerald-700 flex items-center justify-between pt-1 border-t border-secondary-200/60">
                        <span>💰 সাশ্রয়: ৳৩০.০০ (২০% ছাড়)</span>
                        <span class="text-[10px] text-secondary-400">অটো ক্যালকুলেটেড</span>
                    </div>
                </div>

                <!-- Badges Row: Quality & Discount -->
                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-secondary-700 mb-1" for="studio_quality_tag">
                            তাজাত্ব / কোয়ালিটি ব্যাজ
                        </label>
                        <select id="studio_quality_tag" onchange="drawBannerCanvas()" class="w-full px-2.5 py-1.5 bg-white border border-secondary-300 rounded-xl text-xs font-bold text-secondary-900">
                            <option value="🌿 ১০০% ফার্ম-ফ্রেশ" selected>🌿 ১০০% ফার্ম-ফ্রেশ</option>
                            <option value="✨ প্রিমিয়াম কোয়ালিটি">✨ প্রিমিয়াম কোয়ালিটি</option>
                            <option value="🥗 ১০০% খাঁটি পণ্য">🥗 ১০০% খাঁটি পণ্য</option>
                            <option value="🔥 আজকের হট ডিল">🔥 আজকের হট ডিল</option>
                            <option value="⚡ সীমিত স্টক!">⚡ সীমিত স্টক!</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-secondary-700 mb-1" for="studio_discount_tag">
                            ডিসকাউন্ট রিবন
                        </label>
                        <input type="text" id="studio_discount_tag" oninput="drawBannerCanvas()" 
                               value="২০% ছাড়!" 
                               class="w-full px-2.5 py-1.5 bg-white border border-secondary-300 rounded-xl text-xs font-bold text-red-600">
                    </div>
                </div>

                <!-- Store Info & Hotline -->
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-secondary-700 mb-1" for="studio_site_name">
                            দোকানের নাম
                        </label>
                        <input type="text" id="studio_site_name" oninput="drawBannerCanvas()" 
                               value="সদাই দরকার" 
                               class="w-full px-2.5 py-1.5 bg-white border border-secondary-300 rounded-xl text-xs font-bold text-secondary-900">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-secondary-700 mb-1" for="studio_phone">
                            হটলাইন ফোন
                        </label>
                        <input type="text" id="studio_phone" oninput="drawBannerCanvas()" 
                               value="<?= htmlspecialchars($settings['site_phone'] ?? '01700-000000') ?>" 
                               class="w-full px-2.5 py-1.5 bg-white border border-secondary-300 rounded-xl text-xs font-bold text-secondary-900">
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 2. Custom Post Modal -->
<!-- ========================================== -->
<div id="customPostModal" class="fixed inset-0 z-50 hidden bg-secondary-900/60 backdrop-blur-xs overflow-y-auto flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-xl w-full p-6 shadow-2xl border border-secondary-200">
        <div class="flex items-center justify-between border-b border-secondary-200 pb-3 mb-4">
            <h3 class="text-base font-bold text-secondary-900 flex items-center gap-2">
                <ion-icon name="create-outline" class="text-primary-600 text-lg"></ion-icon>
                <span>কাস্টম মার্কেটিং পোস্ট তৈরি করুন</span>
            </h3>
            <button type="button" onclick="closeCustomPostModal()" class="text-secondary-400 hover:text-secondary-600 text-2xl cursor-pointer">&times;</button>
        </div>

        <form action="<?= $base ?>/admin/marketing/save-post" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs font-semibold">
            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
            <input type="hidden" name="id" value="0">
            <input type="hidden" name="image_path" id="custom_image_path" value="">

            <div>
                <label class="block text-[11px] font-bold text-secondary-700 uppercase mb-1">পণ্য নির্বাচন করুন (ঐচ্ছিক)</label>
                <select name="product_id" id="custom_product_id" onchange="onCustomProductChange()" 
                        class="w-full px-3 py-2 border border-secondary-300 rounded-xl bg-white font-semibold text-xs">
                    <option value="">-- পণ্য ছাড়া সাধারণ পোস্ট অথবা পণ্য বাছুন --</option>
                    <?php foreach ($products as $p): ?>
                        <option value="<?= $p['id'] ?>" 
                                data-name="<?= htmlspecialchars($p['name']) ?>"
                                data-sell="<?= floatval($p['sell_price']) ?>"
                                data-regular="<?= floatval($p['regular_price'] ?? 0) ?>"
                                data-image="<?= htmlspecialchars($p['image_path'] ?? '') ?>"
                                data-unit="<?= htmlspecialchars($p['base_unit'] ?? 'পিস') ?>">
                            <?= htmlspecialchars($p['name']) ?> (৳<?= number_format(floatval($p['sell_price']), 2) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-secondary-700 uppercase mb-1">স্লট</label>
                    <select name="slot_type" id="custom_slot_type" class="w-full px-3 py-2 border border-secondary-300 rounded-xl bg-white font-semibold text-xs">
                        <option value="custom">✨ স্পেশাল / তাৎক্ষণিক (Custom)</option>
                        <option value="morning">🌅 সকাল (Morning)</option>
                        <option value="afternoon">🍛 দুপুর (Afternoon)</option>
                        <option value="evening">🌙 সন্ধ্যা (Evening)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-secondary-700 uppercase mb-1">জেনারেশন পদ্ধতি</label>
                    <select name="mode" id="custom_mode" class="w-full px-3 py-2 border border-secondary-300 rounded-xl bg-white font-semibold text-xs">
                        <option value="template_no_ai">⚡ Smart Template (AI ছাড়া)</option>
                        <option value="ai_gemini">🤖 Gemini AI</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <button type="button" onclick="generateCustomContent()" 
                        class="px-3.5 py-2 bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5 cursor-pointer">
                    <ion-icon name="sparkles"></ion-icon>
                    <span>ক্যাপশন জেনারেট করুন</span>
                </button>
                <span id="custom-gen-status" class="text-xs font-semibold"></span>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-secondary-700 uppercase mb-1">হেডলাইন (ঐচ্ছিক)</label>
                <input type="text" name="headline" id="custom_headline" placeholder="যেমন: 🔥 আজকের সেরা মেগা অফার!" 
                       class="w-full px-3 py-2 border border-secondary-300 rounded-xl font-bold text-xs">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-secondary-700 uppercase mb-1">পোস্টের ছবি আপলোড করুন (ঐচ্ছিক)</label>
                <input type="file" name="custom_image" accept="image/*" 
                       class="w-full px-3 py-1.5 border border-secondary-300 rounded-xl bg-white text-xs file:mr-2 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer">
                <span class="text-[10px] text-secondary-400 mt-0.5 block">ছবি না দিলে শুধুমাত্র টেক্সট হিসেবে পোস্ট প্রকাশিত হবে।</span>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-secondary-700 uppercase mb-1">ক্যাপশন টেক্সট <span class="text-red-500">*</span></label>
                <textarea name="caption" id="custom_caption" rows="5" placeholder="পোস্টের সম্পূর্ণ ক্যাপশন লিখুন..." 
                          class="w-full p-3 border border-secondary-300 rounded-xl font-sans text-xs" required></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 flex-wrap">
                <button type="button" onclick="closeCustomPostModal()" class="px-4 py-2 bg-secondary-100 rounded-xl text-secondary-700 font-bold cursor-pointer">বাতিল</button>
                <button type="submit" name="action" value="save" class="px-4 py-2 bg-secondary-700 hover:bg-secondary-800 text-white font-bold rounded-xl shadow-xs flex items-center gap-1 cursor-pointer">
                    <ion-icon name="bookmark-outline"></ion-icon>
                    <span>ড্রাফট সেভ করুন</span>
                </button>
                <button type="submit" name="action" value="publish" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-sm flex items-center gap-1.5 cursor-pointer">
                    <ion-icon name="rocket-outline"></ion-icon>
                    <span>🚀 এখনই ফেসবুকে পাবলিশ করুন</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- JavaScript: Canvas Engine & Dynamic Studio -->
<!-- ========================================== -->
<script>
let currentStudioTheme = 'emerald';
let studioImgElement = null;

const THEMES = {
    emerald: {
        name: 'Farm Fresh Green',
        bgStart: '#043e2f',
        bgMid: '#064e3b',
        bgEnd: '#022119',
        glowColor: 'rgba(16, 185, 129, 0.35)',
        accent: '#10b981',
        badgeBgStart: '#fbbf24',
        badgeBgEnd: '#f59e0b',
        badgeText: '#78350f',
        tagBg: '#059669',
        priceBg: 'rgba(0, 0, 0, 0.65)',
        priceAccent: '#34d399',
        saveBg: '#fbbf24',
        saveText: '#78350f'
    },
    ruby: {
        name: 'Mega Deal Ruby',
        bgStart: '#831843',
        bgMid: '#881337',
        bgEnd: '#4c0519',
        glowColor: 'rgba(244, 63, 94, 0.35)',
        accent: '#f43f5e',
        badgeBgStart: '#fef08a',
        badgeBgEnd: '#fde047',
        badgeText: '#713f12',
        tagBg: '#e11d48',
        priceBg: 'rgba(0, 0, 0, 0.65)',
        priceAccent: '#fb7185',
        saveBg: '#fef08a',
        saveText: '#713f12'
    },
    amber: {
        name: 'Golden Morning',
        bgStart: '#78350f',
        bgMid: '#92400e',
        bgEnd: '#451a03',
        glowColor: 'rgba(245, 158, 11, 0.35)',
        accent: '#f59e0b',
        badgeBgStart: '#fef3c7',
        badgeBgEnd: '#fde68a',
        badgeText: '#92400e',
        tagBg: '#d97706',
        priceBg: 'rgba(0, 0, 0, 0.65)',
        priceAccent: '#fbbf24',
        saveBg: '#fef3c7',
        saveText: '#92400e'
    },
    navy: {
        name: 'Midnight Flash',
        bgStart: '#0f172a',
        bgMid: '#1e1b4b',
        bgEnd: '#020617',
        glowColor: 'rgba(56, 189, 248, 0.3)',
        accent: '#38bdf8',
        badgeBgStart: '#f97316',
        badgeBgEnd: '#ea580c',
        badgeText: '#ffffff',
        tagBg: '#0284c7',
        priceBg: 'rgba(0, 0, 0, 0.7)',
        priceAccent: '#38bdf8',
        saveBg: '#f97316',
        saveText: '#ffffff'
    },
    royal: {
        name: 'Royal Organic',
        bgStart: '#3b0764',
        bgMid: '#581c87',
        bgEnd: '#1e0836',
        glowColor: 'rgba(192, 132, 252, 0.35)',
        accent: '#c084fc',
        badgeBgStart: '#fbcfe8',
        badgeBgEnd: '#f472b6',
        badgeText: '#701a75',
        tagBg: '#9333ea',
        priceBg: 'rgba(0, 0, 0, 0.7)',
        priceAccent: '#e879f9',
        saveBg: '#fbcfe8',
        saveText: '#701a75'
    }
};

function copyCaption(btn, textareaId) {
    const el = document.getElementById(textareaId);
    if (!el) return;
    el.select();
    navigator.clipboard.writeText(el.value).then(() => {
        const originalText = btn.innerHTML;
        btn.innerHTML = '✅ কপি হয়েছে!';
        btn.classList.add('text-emerald-700');
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.classList.remove('text-emerald-700');
        }, 2000);
    });
}

function setHeadlinePreset(txt) {
    const el = document.getElementById('studio_headline');
    if (el) {
        el.value = txt;
        drawBannerCanvas();
    }
}

function onStudioPriceChange() {
    const sPrice = parseFloat(document.getElementById('studio_sell_price')?.value) || 0;
    const rPrice = parseFloat(document.getElementById('studio_regular_price')?.value) || 0;
    const prevEl = document.getElementById('studio-savings-preview');

    if (rPrice > sPrice && rPrice > 0) {
        const diff = rPrice - sPrice;
        const pct = Math.round((diff / rPrice) * 100);
        document.getElementById('studio_discount_tag').value = `${pct}% ছাড়!`;
        if (prevEl) prevEl.innerHTML = `<span>💰 সাশ্রয়: ৳${diff.toFixed(2)} (${pct}% ছাড়)</span><span class="text-[10px] text-emerald-600 font-bold">অটো সেভ হবে</span>`;
    } else {
        document.getElementById('studio_discount_tag').value = 'সেরা অফার!';
        if (prevEl) prevEl.innerHTML = `<span>⚡ রেগুলার স্পেশাল প্রাইস</span><span class="text-[10px] text-secondary-400">ছাড় নেই</span>`;
    }
    drawBannerCanvas();
}

function openBannerStudio(postId, postData) {
    document.getElementById('studio_post_id').value = postId || '';
    document.getElementById('studio_headline').value = postData.headline || '🔥 আজকের সেরা মেগা অফার!';
    document.getElementById('studio_prod_name').value = postData.product_name || 'সদাই দরকার সেরা পণ্য';
    document.getElementById('studio_sell_price').value = postData.sell_price || '120';
    document.getElementById('studio_regular_price').value = postData.regular_price || '150';
    document.getElementById('studio_unit').value = postData.base_unit || '১ কেজি';

    const sPrice = parseFloat(postData.sell_price) || 0;
    const rPrice = parseFloat(postData.regular_price) || 0;
    if (rPrice > sPrice) {
        const pct = Math.round(((rPrice - sPrice) / rPrice) * 100);
        document.getElementById('studio_discount_tag').value = `${pct}% ছাড়!`;
    } else {
        document.getElementById('studio_discount_tag').value = 'সেরা অফার!';
    }

    // Determine slot theme
    if (postData.slot_type === 'morning') currentStudioTheme = 'amber';
    else if (postData.slot_type === 'evening') currentStudioTheme = 'navy';
    else currentStudioTheme = 'emerald';

    updateThemeButtonUI();

    // Load image
    const imgUrl = postData.image_path || postData.product_image || '';
    document.getElementById('studio_product_image_url').value = imgUrl;

    if (imgUrl) {
        studioImgElement = new Image();
        studioImgElement.crossOrigin = 'anonymous';
        studioImgElement.onload = () => drawBannerCanvas();
        studioImgElement.onerror = () => { studioImgElement = null; drawBannerCanvas(); };
        studioImgElement.src = imgUrl;
    } else {
        studioImgElement = null;
        drawBannerCanvas();
    }

    onStudioPriceChange();
    document.getElementById('bannerStudioModal').classList.remove('hidden');
    drawBannerCanvas();
}

function closeBannerStudio() {
    document.getElementById('bannerStudioModal').classList.add('hidden');
}

function updateThemeButtonUI() {
    document.querySelectorAll('.theme-btn').forEach(btn => {
        if (btn.dataset.theme === currentStudioTheme) {
            btn.classList.add('border-emerald-500', 'bg-emerald-50', 'text-emerald-950');
            btn.classList.remove('border-secondary-200', 'bg-white', 'text-secondary-700');
        } else {
            btn.classList.remove('border-emerald-500', 'bg-emerald-50', 'text-emerald-950');
            btn.classList.add('border-secondary-200', 'bg-white', 'text-secondary-700');
        }
    });
}

function setStudioTheme(themeKey) {
    currentStudioTheme = themeKey;
    updateThemeButtonUI();
    drawBannerCanvas();
}

/**
 * Draw Ultra-High-Resolution 1080x1080 Facebook Ad Creative on HTML5 Canvas
 * Professional Grocery Brand Design System (Chaldal / Shwapno / Zepto Style)
 */
function drawBannerCanvas() {
    const canvas = document.getElementById('bannerCanvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const width = 1080;
    const height = 1080;

    const theme = THEMES[currentStudioTheme] || THEMES.emerald;
    const headline = document.getElementById('studio_headline')?.value || '🔥 আজকের সেরা মেগা অফার!';
    const prodName = document.getElementById('studio_prod_name')?.value || 'সদাই দরকার প্রিমিয়াম গ্রোসারি';
    const sellPrice = parseFloat(document.getElementById('studio_sell_price')?.value) || 0;
    const regPrice = parseFloat(document.getElementById('studio_regular_price')?.value) || 0;
    const unit = document.getElementById('studio_unit')?.value || '১ কেজি';
    const discountTag = document.getElementById('studio_discount_tag')?.value || '';
    const qualityTag = document.getElementById('studio_quality_tag')?.value || '🌿 ১০০% ফার্ম-ফ্রেশ';
    const siteName = document.getElementById('studio_site_name')?.value || 'সদাই দরকার';
    const phone = document.getElementById('studio_phone')?.value || '01700-000000';

    // 1. Multi-Stop Background Gradient
    const bgGrad = ctx.createLinearGradient(0, 0, width, height);
    bgGrad.addColorStop(0, theme.bgStart);
    bgGrad.addColorStop(0.5, theme.bgMid);
    bgGrad.addColorStop(1, theme.bgEnd);
    ctx.fillStyle = bgGrad;
    ctx.fillRect(0, 0, width, height);

    // 2. Center Light Spotlight Aura (High-depth stage lighting)
    ctx.save();
    const aura = ctx.createRadialGradient(width / 2, 470, 50, width / 2, 470, 500);
    aura.addColorStop(0, theme.glowColor);
    aura.addColorStop(0.65, theme.glowColor.replace(/[\d\.]+\)$/, '0.08)'));
    aura.addColorStop(1, 'rgba(0, 0, 0, 0)');
    ctx.fillStyle = aura;
    ctx.fillRect(0, 0, width, height);
    ctx.restore();

    // 3. Subtle Modern Geometric Rings (Dotted eCommerce accents)
    ctx.save();
    ctx.strokeStyle = 'rgba(255, 255, 255, 0.08)';
    ctx.lineWidth = 2;
    ctx.setLineDash([8, 12]);
    ctx.beginPath();
    ctx.arc(width / 2, 470, 420, 0, Math.PI * 2);
    ctx.stroke();
    ctx.beginPath();
    ctx.arc(width / 2, 470, 460, 0, Math.PI * 2);
    ctx.stroke();
    ctx.restore();

    // Decorative corner light bubbles
    ctx.save();
    ctx.fillStyle = 'rgba(255, 255, 255, 0.03)';
    ctx.beginPath(); ctx.arc(120, 100, 180, 0, Math.PI * 2); ctx.fill();
    ctx.beginPath(); ctx.arc(width - 100, height - 120, 220, 0, Math.PI * 2); ctx.fill();
    ctx.restore();

    // 4. Header: Brand Capsule & Hotline Capsule
    // Brand Capsule (Left)
    ctx.save();
    ctx.fillStyle = 'rgba(255, 255, 255, 0.14)';
    ctx.strokeStyle = 'rgba(255, 255, 255, 0.32)';
    ctx.lineWidth = 2;
    ctx.shadowColor = 'rgba(0,0,0,0.25)';
    ctx.shadowBlur = 10;
    roundRect(ctx, 60, 40, 390, 64, 32, true, true);
    ctx.restore();

    // Store Logo icon circle inside capsule
    ctx.save();
    ctx.fillStyle = theme.accent;
    ctx.beginPath();
    ctx.arc(96, 72, 20, 0, Math.PI * 2);
    ctx.fill();
    ctx.font = '22px sans-serif';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText('🛒', 96, 73);
    ctx.restore();

    // Store Name
    ctx.save();
    ctx.fillStyle = '#ffffff';
    ctx.font = 'bold 30px "Hind Siliguri", sans-serif';
    ctx.textAlign = 'left';
    ctx.textBaseline = 'middle';
    ctx.fillText(siteName, 126, 72);

    // Green Verified Badge
    const nameWidth = ctx.measureText(siteName).width;
    ctx.fillStyle = '#10b981';
    ctx.beginPath(); ctx.arc(126 + nameWidth + 16, 72, 10, 0, Math.PI * 2); ctx.fill();
    ctx.fillStyle = '#ffffff';
    ctx.font = 'bold 12px sans-serif';
    ctx.textAlign = 'center';
    ctx.fillText('✓', 126 + nameWidth + 16, 73);
    ctx.restore();

    // Hotline Capsule (Right)
    ctx.save();
    ctx.fillStyle = 'rgba(0, 0, 0, 0.4)';
    ctx.strokeStyle = theme.accent;
    ctx.lineWidth = 2;
    ctx.shadowColor = 'rgba(0,0,0,0.25)';
    ctx.shadowBlur = 10;
    roundRect(ctx, width - 420, 40, 360, 64, 32, true, true);
    ctx.restore();

    ctx.save();
    ctx.fillStyle = '#fef08a';
    ctx.font = 'bold 26px "Outfit", sans-serif';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText('📞 ' + phone, width - 240, 72);
    ctx.restore();

    // 5. Promotional Headline Ribbon (Y: 122 to 212)
    ctx.save();
    const ribbonGrad = ctx.createLinearGradient(60, 122, width - 60, 212);
    ribbonGrad.addColorStop(0, theme.badgeBgStart);
    ribbonGrad.addColorStop(1, theme.badgeBgEnd);
    ctx.fillStyle = ribbonGrad;
    ctx.shadowColor = 'rgba(0, 0, 0, 0.4)';
    ctx.shadowBlur = 20;
    ctx.shadowOffsetY = 6;
    roundRect(ctx, 60, 122, width - 120, 88, 26, true, false);
    ctx.restore();

    // Inner subtle border on headline ribbon
    ctx.save();
    ctx.strokeStyle = 'rgba(255, 255, 255, 0.45)';
    ctx.lineWidth = 2;
    roundRect(ctx, 64, 126, width - 128, 80, 22, false, true);
    ctx.restore();

    // Headline Text
    ctx.save();
    ctx.fillStyle = theme.badgeText;
    ctx.font = 'bold 44px "Hind Siliguri", sans-serif';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText(headline, width / 2, 166);
    ctx.restore();

    // 6. Center Hero Product Card / 3D Pedestal Stage
    const cardX = 140;
    const cardY = 234;
    const cardW = 800;
    const cardH = 475;

    // Card drop shadow
    ctx.save();
    ctx.shadowColor = 'rgba(0, 0, 0, 0.45)';
    ctx.shadowBlur = 35;
    ctx.shadowOffsetY = 18;
    ctx.fillStyle = '#ffffff';
    roundRect(ctx, cardX, cardY, cardW, cardH, 36, true, false);
    ctx.restore();

    // Inner card border
    ctx.save();
    ctx.strokeStyle = 'rgba(0, 0, 0, 0.06)';
    ctx.lineWidth = 3;
    roundRect(ctx, cardX, cardY, cardW, cardH, 36, false, true);
    ctx.restore();

    // 3D Realistic Contact Pedestal Shadow inside Card
    ctx.save();
    const pedGrad = ctx.createRadialGradient(width / 2, cardY + cardH - 65, 30, width / 2, cardY + cardH - 65, 280);
    pedGrad.addColorStop(0, 'rgba(0, 0, 0, 0.16)');
    pedGrad.addColorStop(0.5, 'rgba(0, 0, 0, 0.08)');
    pedGrad.addColorStop(1, 'rgba(0, 0, 0, 0)');
    ctx.fillStyle = pedGrad;
    ctx.beginPath();
    ctx.ellipse(width / 2, cardY + cardH - 65, 270, 32, 0, 0, Math.PI * 2);
    ctx.fill();
    ctx.restore();

    // Product Image Render inside Card
    if (studioImgElement && studioImgElement.complete && studioImgElement.naturalWidth > 0) {
        ctx.save();
        roundRect(ctx, cardX + 24, cardY + 24, cardW - 48, cardH - 58, 28, false, false);
        ctx.clip();

        const iw = studioImgElement.naturalWidth;
        const ih = studioImgElement.naturalHeight;
        const targetW = cardW - 48;
        const targetH = cardH - 58;
        const ratio = Math.min(targetW / iw, targetH / ih);
        const nw = iw * ratio;
        const nh = ih * ratio;
        const nx = cardX + 24 + (targetW - nw) / 2;
        const ny = cardY + 24 + (targetH - nh) / 2;
        ctx.drawImage(studioImgElement, nx, ny, nw, nh);
        ctx.restore();
    } else {
        ctx.save();
        ctx.fillStyle = '#f8fafc';
        roundRect(ctx, cardX + 24, cardY + 24, cardW - 48, cardH - 58, 28, true, false);
        ctx.fillStyle = '#64748b';
        ctx.font = 'bold 44px "Hind Siliguri", sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText('📦 ' + prodName, cardX + cardW / 2, cardY + cardH / 2);
        ctx.restore();
    }

    // 7. Floating Badges overlapping the card
    // Top-Left: Freshness Quality Badge
    if (qualityTag) {
        ctx.save();
        ctx.fillStyle = theme.tagBg;
        ctx.shadowColor = 'rgba(0, 0, 0, 0.35)';
        ctx.shadowBlur = 15;
        ctx.shadowOffsetY = 6;
        roundRect(ctx, cardX - 20, cardY + 16, 260, 56, 28, true, false);
        ctx.restore();

        // Inner border on badge
        ctx.save();
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.4)';
        ctx.lineWidth = 2;
        roundRect(ctx, cardX - 18, cardY + 18, 256, 52, 26, false, true);
        ctx.restore();

        ctx.save();
        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 24px "Hind Siliguri", sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(qualityTag, cardX - 20 + 130, cardY + 16 + 28);
        ctx.restore();
    }

    // Top-Right: 3D Discount Ribbon
    if (discountTag) {
        ctx.save();
        const discGrad = ctx.createLinearGradient(cardX + cardW - 220, cardY + 14, cardX + cardW + 15, cardY + 74);
        discGrad.addColorStop(0, '#ef4444');
        discGrad.addColorStop(1, '#b91c1c');
        ctx.fillStyle = discGrad;
        ctx.shadowColor = 'rgba(239, 68, 68, 0.6)';
        ctx.shadowBlur = 18;
        ctx.shadowOffsetY = 6;
        roundRect(ctx, cardX + cardW - 235, cardY + 14, 250, 60, 22, true, false);
        ctx.restore();

        ctx.save();
        ctx.strokeStyle = '#fef08a';
        ctx.lineWidth = 2.5;
        roundRect(ctx, cardX + cardW - 232, cardY + 17, 244, 54, 19, false, true);
        ctx.restore();

        ctx.save();
        ctx.fillStyle = '#ffffff';
        ctx.font = 'black 30px "Hind Siliguri", sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText('⚡ ' + discountTag, cardX + cardW - 110, cardY + 44);
        ctx.restore();
    }

    // 8. Product Name (Below Card)
    ctx.save();
    let nameFontSize = 46;
    if (prodName.length > 25) nameFontSize = 38;
    if (prodName.length > 38) nameFontSize = 32;

    ctx.fillStyle = '#ffffff';
    ctx.font = 'bold ' + nameFontSize + 'px "Hind Siliguri", sans-serif';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.shadowColor = 'rgba(0, 0, 0, 0.7)';
    ctx.shadowBlur = 14;
    ctx.shadowOffsetY = 4;
    ctx.fillText(prodName, width / 2, 755);
    ctx.restore();

    // 9. Retail Price & Huge Savings Bar (Y: 798 to 924)
    const priceBoxY = 798;
    const priceBoxH = 126;
    const priceBoxW = 840;
    const priceBoxX = (width - priceBoxW) / 2;

    ctx.save();
    ctx.fillStyle = theme.priceBg;
    ctx.strokeStyle = theme.priceAccent;
    ctx.lineWidth = 3.5;
    ctx.shadowColor = 'rgba(0, 0, 0, 0.5)';
    ctx.shadowBlur = 24;
    ctx.shadowOffsetY = 8;
    roundRect(ctx, priceBoxX, priceBoxY, priceBoxW, priceBoxH, 30, true, true);
    ctx.restore();

    const hasDiscount = regPrice > sellPrice;
    const savingsAmount = hasDiscount ? (regPrice - sellPrice) : 0;

    if (hasDiscount) {
        // Left Column: MRP & Savings Tag
        ctx.save();
        ctx.fillStyle = 'rgba(255, 255, 255, 0.75)';
        ctx.font = 'bold 28px "Outfit", sans-serif';
        ctx.textAlign = 'left';
        ctx.textBaseline = 'middle';
        const mrpText = `MRP: ৳${regPrice.toFixed(0)}`;
        ctx.fillText(mrpText, priceBoxX + 45, priceBoxY + 38);

        // Strikethrough line
        const mrpW = ctx.measureText(mrpText).width;
        ctx.strokeStyle = '#ef4444';
        ctx.lineWidth = 3.5;
        ctx.beginPath();
        ctx.moveTo(priceBoxX + 40, priceBoxY + 38);
        ctx.lineTo(priceBoxX + 45 + mrpW + 5, priceBoxY + 38);
        ctx.stroke();

        // Shiny Savings Pill Badge
        ctx.fillStyle = theme.saveBg;
        ctx.shadowColor = 'rgba(0, 0, 0, 0.3)';
        ctx.shadowBlur = 8;
        roundRect(ctx, priceBoxX + 45, priceBoxY + 68, 235, 42, 21, true, false);

        ctx.fillStyle = theme.saveText;
        ctx.font = 'bold 22px "Hind Siliguri", sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(`💰 সাশ্রয়: ৳${savingsAmount.toFixed(0)}!`, priceBoxX + 45 + 117, priceBoxY + 89);
        ctx.restore();

        // Right Column: Huge Offer Price
        ctx.save();
        ctx.fillStyle = '#ffffff';
        ctx.font = '900 64px "Outfit", sans-serif';
        ctx.textAlign = 'right';
        ctx.textBaseline = 'middle';
        const priceStr = `৳${sellPrice.toFixed(0)}`;
        ctx.fillText(priceStr, priceBoxX + priceBoxW - 45, priceBoxY + 54);

        // Unit label under offer price
        ctx.fillStyle = theme.priceAccent;
        ctx.font = 'bold 24px "Hind Siliguri", sans-serif';
        ctx.fillText(`প্রতি ${unit}`, priceBoxX + priceBoxW - 45, priceBoxY + 95);
        ctx.restore();
    } else {
        // Single Price Center Display
        ctx.save();
        ctx.fillStyle = '#ffffff';
        ctx.font = '900 68px "Outfit", sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(`৳${sellPrice.toFixed(0)} ${unit ? '/ ' + unit : ''}`, width / 2, priceBoxY + priceBoxH / 2);
        ctx.restore();
    }

    // 10. Footer Trust Badges
    ctx.save();
    ctx.fillStyle = 'rgba(255, 255, 255, 0.9)';
    ctx.font = 'bold 24px "Hind Siliguri", sans-serif';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.shadowColor = 'rgba(0,0,0,0.5)';
    ctx.shadowBlur = 8;
    ctx.fillText('🚚 ৩০-৬০ মিনিটে দ্রুত ডেলিভারি   |   💵 ক্যাশ অন ডেলিভারি   |   ✨ ১০০% খাঁটি পণ্য', width / 2, 970);
    ctx.restore();

    // 11. Bottom Call to Action Ribbon
    ctx.save();
    ctx.fillStyle = theme.accent;
    ctx.fillRect(0, height - 60, width, 60);

    ctx.fillStyle = (currentStudioTheme === 'amber' || currentStudioTheme === 'ruby' || currentStudioTheme === 'navy' || currentStudioTheme === 'royal') ? '#ffffff' : '#042f24';
    ctx.font = 'bold 24px "Hind Siliguri", sans-serif';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText('🛒 অর্ডার করতে এখনই ইনবক্স করুন  |  🌐 ' + siteName, width / 2, height - 30);
    ctx.restore();
}

function roundRect(ctx, x, y, width, height, radius, fill, stroke) {
    if (typeof radius === 'undefined') radius = 5;
    if (typeof radius === 'number') {
        radius = {tl: radius, tr: radius, br: radius, bl: radius};
    }
    ctx.beginPath();
    ctx.moveTo(x + radius.tl, y);
    ctx.lineTo(x + width - radius.tr, y);
    ctx.quadraticCurveTo(x + width, y, x + width, y + radius.tr);
    ctx.lineTo(x + width, y + height - radius.br);
    ctx.quadraticCurveTo(x + width, y + height, x + width - radius.br, y + height);
    ctx.lineTo(x + radius.bl, y + height);
    ctx.quadraticCurveTo(x, y + height, x, y + height - radius.bl);
    ctx.lineTo(x, y + radius.tl);
    ctx.quadraticCurveTo(x, y, x + radius.tl, y);
    ctx.closePath();
    if (fill) ctx.fill();
    if (stroke) ctx.stroke();
}

function downloadCanvasBanner() {
    const canvas = document.getElementById('bannerCanvas');
    const link = document.createElement('a');
    link.download = 'sodai_facebook_ad_1080x1080.png';
    link.href = canvas.toDataURL('image/png');
    link.click();
}

async function saveBannerToServer() {
    const canvas = document.getElementById('bannerCanvas');
    const base64 = canvas.toDataURL('image/png');
    const postId = document.getElementById('studio_post_id').value;
    const statusEl = document.getElementById('banner-save-status');

    statusEl.innerHTML = '<span class="text-secondary-500 animate-pulse">সার্ভারে ব্যানার আপলোড হচ্ছে...</span>';

    try {
        const csrfToken = document.querySelector('input[name="csrf_token"]')?.value || '<?= \Core\CSRF::token() ?>';
        const formData = new FormData();
        formData.append('csrf_token', csrfToken);
        formData.append('post_id', postId);
        formData.append('image_base64', base64);

        const res = await fetch('<?= $base ?>/admin/marketing/api/save-banner', {
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
            statusEl.innerHTML = `<span class="text-red-600 font-bold">❌ সার্ভার রেসপন্স ত্রুটি (${res.status})</span>`;
            return;
        }

        if (data.success) {
            statusEl.innerHTML = '<span class="text-emerald-700 font-bold">✅ ব্যানার সফলভাবে সেভ হয়েছে!</span>';
            // Update preview image if element exists
            const imgEl = document.getElementById(`banner-preview-${postId}`);
            if (imgEl) {
                imgEl.src = data.image_url;
            }
            setTimeout(() => {
                closeBannerStudio();
                statusEl.innerHTML = '';
            }, 1200);
        } else {
            statusEl.innerHTML = `<span class="text-red-600 font-bold">❌ ${data.message}</span>`;
        }
    } catch (err) {
        statusEl.innerHTML = `<span class="text-red-600 font-bold">❌ আপলোড ব্যর্থ: ${err.message}</span>`;
    }
}

function openCustomPostModal() {
    document.getElementById('customPostModal').classList.remove('hidden');
}

function closeCustomPostModal() {
    document.getElementById('customPostModal').classList.add('hidden');
}

function onCustomProductChange() {
    const sel = document.getElementById('custom_product_id');
    const opt = sel.options[sel.selectedIndex];
    if (opt && opt.value) {
        document.getElementById('custom_headline').value = `🔥 ${opt.dataset.name} - বিশেষ মেগা অফার!`;
        document.getElementById('custom_image_path').value = opt.dataset.image ? `<?= $base ?>/uploads/${opt.dataset.image}` : '';
    }
}

async function generateCustomContent() {
    const productId = document.getElementById('custom_product_id').value;
    const slot = document.getElementById('custom_slot_type').value;
    const mode = document.getElementById('custom_mode').value;
    const statusEl = document.getElementById('custom-gen-status');

    if (!productId) {
        alert('অনুগ্রহ করে প্রথমে একটি পণ্য নির্বাচন করুন!');
        return;
    }

    statusEl.innerHTML = '<span class="text-purple-600 animate-pulse">ক্যাপশন তৈরি হচ্ছে...</span>';

    try {
        const csrfToken = document.querySelector('input[name="csrf_token"]')?.value || '<?= \Core\CSRF::token() ?>';
        const formData = new FormData();
        formData.append('csrf_token', csrfToken);
        formData.append('product_id', productId);
        formData.append('slot_type', slot);
        formData.append('mode', mode);

        const res = await fetch('<?= $base ?>/admin/marketing/api/generate-content', {
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
            statusEl.innerHTML = `<span class="text-red-600 font-bold">❌ সার্ভার রেসপন্স ত্রুটি (${res.status})</span>`;
            return;
        }

        if (data.success) {
            document.getElementById('custom_headline').value = data.headline;
            document.getElementById('custom_caption').value = data.caption;
            statusEl.innerHTML = '<span class="text-emerald-700 font-bold">✅ প্রস্তুত!</span>';
        } else {
            statusEl.innerHTML = `<span class="text-red-600 font-bold">❌ ${data.message}</span>`;
        }
    } catch (err) {
        statusEl.innerHTML = `<span class="text-red-600 font-bold">❌ এরর: ${err.message}</span>`;
    }
}

/**
 * Test Facebook Post directly on Page from Dashboard
 */
async function testFacebookPost() {
    if (!confirm('আপনার ফেসবুক পেজে একটি টেস্ট পোস্ট পাবলিশ হবে। আপনি কি নিশ্চিত?')) {
        return;
    }

    const csrfToken = document.querySelector('input[name="csrf_token"]')?.value || '<?= \Core\CSRF::token() ?>';
    const formData = new FormData();
    formData.append('csrf_token', csrfToken);
    formData.append('message', '🎉 টেস্ট পোস্ট: সদাই দরকার ফেসবুক অটোমেশন সক্রিয় রয়েছে!');

    try {
        const res = await fetch('<?= $base ?>/admin/marketing/api/test-post', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        });

        const text = await res.text();
        const data = JSON.parse(text);

        if (data.success) {
            alert('✅ টেস্ট পোস্ট সফলভাবে প্রকাশিত হয়েছে!\nপোস্ট আইডি: ' + data.post_id);
            if (data.post_url) {
                window.open(data.post_url, '_blank');
            }
        } else {
            alert('❌ টেস্ট পোস্ট ব্যর্থ হয়েছে: ' + data.message);
        }
    } catch (e) {
        alert('❌ এরর: ' + e.message);
    }
}
</script>
