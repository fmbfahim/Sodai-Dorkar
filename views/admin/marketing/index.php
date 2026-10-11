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
                                    <button type="button" onclick="openBannerStudio(<?= $rp['id'] ?>, <?= htmlspecialchars(json_encode($rp)) ?>)" 
                                            class="text-primary-600 hover:text-primary-800 font-bold px-2 py-1 rounded hover:bg-primary-50 transition-colors">
                                        ব্যানার এডিটর
                                    </button>
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

                <!-- Template Theme Picker -->
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-secondary-600 mb-2">
                        🎨 ব্যানার থিম ও কালার টেমপ্লেট
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" onclick="setStudioTheme('emerald')" class="p-2.5 rounded-xl border border-emerald-400 bg-emerald-50 text-emerald-900 font-bold flex items-center gap-2 hover:shadow-xs transition-all text-left">
                            <span class="w-4 h-4 rounded-full bg-emerald-600 shrink-0"></span>
                            <span>Emerald Fresh (সদাই ফ্রেশ)</span>
                        </button>
                        <button type="button" onclick="setStudioTheme('ruby')" class="p-2.5 rounded-xl border border-red-400 bg-red-50 text-red-900 font-bold flex items-center gap-2 hover:shadow-xs transition-all text-left">
                            <span class="w-4 h-4 rounded-full bg-red-600 shrink-0"></span>
                            <span>Ruby Mega Deal (ধামাকা ছাড়)</span>
                        </button>
                        <button type="button" onclick="setStudioTheme('navy')" class="p-2.5 rounded-xl border border-blue-400 bg-blue-50 text-blue-900 font-bold flex items-center gap-2 hover:shadow-xs transition-all text-left">
                            <span class="w-4 h-4 rounded-full bg-slate-900 shrink-0"></span>
                            <span>Midnight Sunset (সন্ধ্যার অফার)</span>
                        </button>
                        <button type="button" onclick="setStudioTheme('amber')" class="p-2.5 rounded-xl border border-amber-400 bg-amber-50 text-amber-900 font-bold flex items-center gap-2 hover:shadow-xs transition-all text-left">
                            <span class="w-4 h-4 rounded-full bg-amber-500 shrink-0"></span>
                            <span>Golden Morning (সকালের বাজার)</span>
                        </button>
                    </div>
                </div>

                <!-- Top Headline -->
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-secondary-600 mb-1" for="studio_headline">
                        শীর্ষ আকর্ষণ / হেডলাইন
                    </label>
                    <input type="text" id="studio_headline" oninput="drawBannerCanvas()" 
                           value="🔥 আজকের সেরা মেগা অফার!" 
                           class="w-full px-3 py-2 bg-white border border-secondary-300 rounded-xl text-xs font-bold text-secondary-900 focus:ring-2 focus:ring-primary-500">
                </div>

                <!-- Product Title -->
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-secondary-600 mb-1" for="studio_prod_name">
                        পণ্যের নাম
                    </label>
                    <input type="text" id="studio_prod_name" oninput="drawBannerCanvas()" 
                           value="সদাই দরকার প্রিমিয়াম গ্রোসারি" 
                           class="w-full px-3 py-2 bg-white border border-secondary-300 rounded-xl text-xs font-bold text-secondary-900 focus:ring-2 focus:ring-primary-500">
                </div>

                <!-- Pricing Row -->
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-secondary-600 mb-1" for="studio_sell_price">
                            অফার মূল্য (৳)
                        </label>
                        <input type="number" step="0.01" id="studio_sell_price" oninput="drawBannerCanvas()" 
                               value="120" 
                               class="w-full px-2.5 py-1.5 bg-white border border-secondary-300 rounded-xl text-xs font-bold text-emerald-700">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-secondary-600 mb-1" for="studio_regular_price">
                            রেগুলার মূল্য (৳)
                        </label>
                        <input type="number" step="0.01" id="studio_regular_price" oninput="drawBannerCanvas()" 
                               value="150" 
                               class="w-full px-2.5 py-1.5 bg-white border border-secondary-300 rounded-xl text-xs font-bold text-secondary-600">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-secondary-600 mb-1" for="studio_unit">
                            ইউনিট / পরিমাণ
                        </label>
                        <input type="text" id="studio_unit" oninput="drawBannerCanvas()" 
                               value="১ কেজি" 
                               class="w-full px-2.5 py-1.5 bg-white border border-secondary-300 rounded-xl text-xs font-bold text-secondary-700">
                    </div>
                </div>

                <!-- Discount Tag Text -->
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-secondary-600 mb-1" for="studio_discount_tag">
                        রিবন ডিসকাউন্ট ব্যাজ
                    </label>
                    <input type="text" id="studio_discount_tag" oninput="drawBannerCanvas()" 
                           value="২০% ছাড়!" 
                           class="w-full px-3 py-2 bg-white border border-secondary-300 rounded-xl text-xs font-bold text-secondary-900 focus:ring-2 focus:ring-primary-500">
                </div>

                <!-- Store Info & Hotline -->
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-secondary-600 mb-1" for="studio_site_name">
                            দোকানের নাম
                        </label>
                        <input type="text" id="studio_site_name" oninput="drawBannerCanvas()" 
                               value="সদাই দরকার" 
                               class="w-full px-2.5 py-1.5 bg-white border border-secondary-300 rounded-xl text-xs font-bold text-secondary-900">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-secondary-600 mb-1" for="studio_phone">
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

        <form action="<?= $base ?>/admin/marketing/save-post" method="POST" class="space-y-4 text-xs font-semibold">
            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
            <input type="hidden" name="id" value="0">
            <input type="hidden" name="image_path" id="custom_image_path" value="">

            <div>
                <label class="block text-[11px] font-bold text-secondary-700 uppercase mb-1">পণ্য নির্বাচন করুন <span class="text-red-500">*</span></label>
                <select name="product_id" id="custom_product_id" onchange="onCustomProductChange()" 
                        class="w-full px-3 py-2 border border-secondary-300 rounded-xl bg-white font-semibold text-xs" required>
                    <option value="">-- একটি পণ্য নির্বাচন করুন --</option>
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
                        <option value="morning">🌅 সকাল (Morning)</option>
                        <option value="afternoon">🍛 দুপুর (Afternoon)</option>
                        <option value="evening">🌙 সন্ধ্যা (Evening)</option>
                        <option value="custom">✨ স্পেশাল (Custom)</option>
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
                <label class="block text-[11px] font-bold text-secondary-700 uppercase mb-1">হেডলাইন</label>
                <input type="text" name="headline" id="custom_headline" placeholder="যেমন: 🔥 আজকের সেরা মেগা অফার!" 
                       class="w-full px-3 py-2 border border-secondary-300 rounded-xl font-bold text-xs">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-secondary-700 uppercase mb-1">ক্যাপশন টেক্সট</label>
                <textarea name="caption" id="custom_caption" rows="5" placeholder="পোস্টের সম্পূর্ণ ক্যাপশন লিখুন..." 
                          class="w-full p-3 border border-secondary-300 rounded-xl font-sans text-xs" required></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeCustomPostModal()" class="px-4 py-2 bg-secondary-100 rounded-xl text-secondary-700 font-bold">বাতিল</button>
                <button type="submit" class="px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl shadow-xs">সেভ করুন</button>
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
        bgStart: '#064e3b',
        bgEnd: '#022c22',
        cardBg: '#ffffff',
        accent: '#10b981',
        badgeBg: '#fbbf24',
        badgeText: '#78350f',
        priceBg: '#10b981',
        footerBg: '#022c22'
    },
    ruby: {
        bgStart: '#881337',
        bgEnd: '#4c0519',
        cardBg: '#ffffff',
        accent: '#f43f5e',
        badgeBg: '#fef08a',
        badgeText: '#713f12',
        priceBg: '#e11d48',
        footerBg: '#4c0519'
    },
    navy: {
        bgStart: '#0f172a',
        bgEnd: '#020617',
        cardBg: '#ffffff',
        accent: '#f97316',
        badgeBg: '#f97316',
        badgeText: '#ffffff',
        priceBg: '#ea580c',
        footerBg: '#090d16'
    },
    amber: {
        bgStart: '#78350f',
        bgEnd: '#451a03',
        cardBg: '#ffffff',
        accent: '#f59e0b',
        badgeBg: '#fef3c7',
        badgeText: '#92400e',
        priceBg: '#d97706',
        footerBg: '#451a03'
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

function openBannerStudio(postId, postData) {
    document.getElementById('studio_post_id').value = postId || '';
    document.getElementById('studio_headline').value = postData.headline || '🔥 আজকের সেরা মেগা অফার!';
    document.getElementById('studio_prod_name').value = postData.product_name || 'সদাই দরকার সেরা পণ্য';
    document.getElementById('studio_sell_price').value = postData.sell_price || '120';
    document.getElementById('studio_regular_price').value = postData.regular_price || '150';
    document.getElementById('studio_unit').value = '১ ইউনিট';

    const sPrice = parseFloat(postData.sell_price) || 0;
    const rPrice = parseFloat(postData.regular_price) || 0;
    if (rPrice > sPrice) {
        const pct = Math.round(((rPrice - sPrice) / rPrice) * 100);
        document.getElementById('studio_discount_tag').value = `${pct}% ছাড়!`;
    } else {
        document.getElementById('studio_discount_tag').value = 'বিশেষ ছাড়!';
    }

    // Determine slot theme
    if (postData.slot_type === 'morning') currentStudioTheme = 'amber';
    else if (postData.slot_type === 'evening') currentStudioTheme = 'navy';
    else currentStudioTheme = 'emerald';

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

    document.getElementById('bannerStudioModal').classList.remove('hidden');
    drawBannerCanvas();
}

function closeBannerStudio() {
    document.getElementById('bannerStudioModal').classList.add('hidden');
}

function setStudioTheme(themeKey) {
    currentStudioTheme = themeKey;
    drawBannerCanvas();
}

/**
 * Draw High-Resolution 1080x1080 Facebook Ad Creative on HTML5 Canvas
 */
function drawBannerCanvas() {
    const canvas = document.getElementById('bannerCanvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const width = 1080;
    const height = 1080;

    const theme = THEMES[currentStudioTheme] || THEMES.emerald;
    const headline = document.getElementById('studio_headline').value || '🔥 আজকের সেরা অফার!';
    const prodName = document.getElementById('studio_prod_name').value || 'সদাই দরকার সেরা পণ্য';
    const sellPrice = parseFloat(document.getElementById('studio_sell_price').value) || 0;
    const regPrice = parseFloat(document.getElementById('studio_regular_price').value) || 0;
    const unit = document.getElementById('studio_unit').value || '১ ইউনিট';
    const discountTag = document.getElementById('studio_discount_tag').value || 'বিশেষ ছাড়!';
    const siteName = document.getElementById('studio_site_name').value || 'সদাই দরকার';
    const phone = document.getElementById('studio_phone').value || '01700-000000';

    // 1. Background Gradient
    const grad = ctx.createLinearGradient(0, 0, width, height);
    grad.addColorStop(0, theme.bgStart);
    grad.addColorStop(1, theme.bgEnd);
    ctx.fillStyle = grad;
    ctx.fillRect(0, 0, width, height);

    // Decorative background radial circles
    ctx.beginPath();
    ctx.arc(width * 0.85, height * 0.15, 300, 0, Math.PI * 2);
    ctx.fillStyle = 'rgba(255, 255, 255, 0.04)';
    ctx.fill();

    ctx.beginPath();
    ctx.arc(width * 0.15, height * 0.85, 250, 0, Math.PI * 2);
    ctx.fillStyle = 'rgba(255, 255, 255, 0.03)';
    ctx.fill();

    // 2. Top Header Ribbon (Store Branding & Headline)
    ctx.fillStyle = 'rgba(0, 0, 0, 0.25)';
    ctx.fillRect(0, 0, width, 140);

    // Store Name on Left
    ctx.fillStyle = '#ffffff';
    ctx.font = 'bold 36px "Hind Siliguri", sans-serif';
    ctx.textAlign = 'left';
    ctx.fillText('🛒 ' + siteName, 60, 85);

    // Hotline on Right
    ctx.fillStyle = '#fef08a';
    ctx.font = 'bold 28px "Outfit", sans-serif';
    ctx.textAlign = 'right';
    ctx.fillText('📞 ' + phone, width - 60, 85);

    // 3. Catchy Headline Banner
    ctx.save();
    ctx.fillStyle = theme.badgeBg;
    ctx.shadowColor = 'rgba(0,0,0,0.3)';
    ctx.shadowBlur = 15;
    roundRect(ctx, 60, 165, width - 120, 90, 24, true, false);
    ctx.restore();

    ctx.fillStyle = theme.badgeText;
    ctx.font = 'bold 44px "Hind Siliguri", sans-serif';
    ctx.textAlign = 'center';
    ctx.fillText(headline, width / 2, 227);

    // 4. Center Product Card
    const cardX = 140;
    const cardY = 280;
    const cardW = 800;
    const cardH = 460;

    ctx.save();
    ctx.shadowColor = 'rgba(0,0,0,0.4)';
    ctx.shadowBlur = 30;
    ctx.shadowOffsetY = 15;
    ctx.fillStyle = '#ffffff';
    roundRect(ctx, cardX, cardY, cardW, cardH, 32, true, false);
    ctx.restore();

    // Product Image or Placeholder inside Card
    if (studioImgElement && studioImgElement.complete && studioImgElement.naturalWidth > 0) {
        ctx.save();
        roundRect(ctx, cardX + 20, cardY + 20, cardW - 40, cardH - 40, 24, false, false);
        ctx.clip();
        
        // Draw image aspect fitted
        const iw = studioImgElement.naturalWidth;
        const ih = studioImgElement.naturalHeight;
        const targetW = cardW - 40;
        const targetH = cardH - 40;
        const ratio = Math.min(targetW / iw, targetH / ih);
        const nw = iw * ratio;
        const nh = ih * ratio;
        const nx = cardX + 20 + (targetW - nw) / 2;
        const ny = cardY + 20 + (targetH - nh) / 2;
        ctx.drawImage(studioImgElement, nx, ny, nw, nh);
        ctx.restore();
    } else {
        ctx.fillStyle = '#f1f5f9';
        roundRect(ctx, cardX + 20, cardY + 20, cardW - 40, cardH - 40, 24, true, false);
        ctx.fillStyle = '#94a3b8';
        ctx.font = 'bold 48px "Hind Siliguri", sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText('📦 ' + prodName, cardX + cardW / 2, cardY + cardH / 2);
    }

    // 5. Discount Ribbon on Top-Right of Card
    if (discountTag) {
        ctx.save();
        ctx.fillStyle = '#ef4444';
        ctx.shadowColor = 'rgba(239, 68, 68, 0.5)';
        ctx.shadowBlur = 15;
        roundRect(ctx, cardX + cardW - 200, cardY - 20, 220, 65, 18, true, false);
        ctx.restore();

        ctx.fillStyle = '#ffffff';
        ctx.font = 'black 30px "Hind Siliguri", sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText('⚡ ' + discountTag, cardX + cardW - 90, cardY + 24);
    }

    // 6. Product Name Below Card
    ctx.fillStyle = '#ffffff';
    ctx.font = 'bold 50px "Hind Siliguri", sans-serif';
    ctx.textAlign = 'center';
    ctx.fillText(prodName, width / 2, 790);

    // 7. Price Badges
    const priceBoxY = 825;
    ctx.save();
    ctx.fillStyle = theme.priceBg;
    ctx.shadowColor = 'rgba(0,0,0,0.3)';
    ctx.shadowBlur = 20;
    roundRect(ctx, 240, priceBoxY, 600, 110, 28, true, false);
    ctx.restore();

    ctx.textAlign = 'center';
    let priceText = `৳${sellPrice.toFixed(2)}`;
    if (regPrice > sellPrice) {
        ctx.fillStyle = 'rgba(255, 255, 255, 0.7)';
        ctx.font = 'bold 32px "Outfit", sans-serif';
        ctx.fillText(`MRP: ৳${regPrice.toFixed(2)}`, width / 2 - 130, priceBoxY + 68);

        // Strikethrough line
        ctx.strokeStyle = '#ef4444';
        ctx.lineWidth = 4;
        ctx.beginPath();
        ctx.moveTo(width / 2 - 240, priceBoxY + 58);
        ctx.lineTo(width / 2 - 20, priceBoxY + 58);
        ctx.stroke();

        ctx.fillStyle = '#ffffff';
        ctx.font = '900 52px "Outfit", sans-serif';
        ctx.fillText(priceText, width / 2 + 130, priceBoxY + 72);
    } else {
        ctx.fillStyle = '#ffffff';
        ctx.font = '900 56px "Outfit", sans-serif';
        ctx.fillText(priceText + (unit ? ` / ${unit}` : ''), width / 2, priceBoxY + 75);
    }

    // 8. Footer Trust Badges
    ctx.fillStyle = '#ffffff';
    ctx.font = 'bold 26px "Hind Siliguri", sans-serif';
    ctx.textAlign = 'center';
    ctx.fillText('🚚 ক্যাশ অন ডেলিভারি   |   ✨ ১০০% খাঁটি পণ্য   |   ⚡ দ্রুত হোম ডেলিভারি', width / 2, 995);

    // Bottom decorative bar
    ctx.fillStyle = theme.accent;
    ctx.fillRect(0, height - 16, width, 16);
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
        const formData = new FormData();
        formData.append('post_id', postId);
        formData.append('image_base64', base64);

        const res = await fetch('<?= $base ?>/admin/marketing/api/save-banner', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();

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
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('slot_type', slot);
        formData.append('mode', mode);

        const res = await fetch('<?= $base ?>/admin/marketing/api/generate-content', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();

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
</script>
