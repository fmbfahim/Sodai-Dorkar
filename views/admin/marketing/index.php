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

            <!-- Meena Bazar AI Creative Studio Button -->
            <button type="button" onclick="openStudioModalTab('observance')" class="px-4 py-2.5 bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 hover:from-emerald-700 hover:to-teal-800 text-white font-bold text-xs rounded-xl transition-all shadow-sm hover:shadow-md flex items-center gap-1.5 cursor-pointer">
                <ion-icon name="sparkles" class="text-base text-yellow-300"></ion-icon>
                <span>✨ মিনা বাজার এআই স্টুডিও</span>
                <?php if (!empty($recentPriceChanged)): ?>
                    <span class="px-1.5 py-0.5 rounded-full bg-yellow-400 text-emerald-950 text-[10px] font-black">⚡ <?= count($recentPriceChanged) ?> দাম কমেছে</span>
                <?php endif; ?>
            </button>

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
<!-- 1. HTML5 Canvas Banner Designer & AI Studio Modal (Meena Bazar Style) -->
<!-- ========================================== -->
<div id="bannerStudioModal" class="fixed inset-0 z-50 hidden bg-secondary-900/70 backdrop-blur-xs overflow-y-auto flex items-center justify-center p-3 sm:p-5">
    <div class="bg-white rounded-3xl max-w-6xl w-full p-5 sm:p-7 shadow-2xl border border-secondary-200 max-h-[94vh] overflow-y-auto">
        
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-secondary-200 pb-3.5 mb-4">
            <div class="flex items-center gap-2.5">
                <span class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center text-xl shadow-xs">
                    <ion-icon name="sparkles"></ion-icon>
                </span>
                <div>
                    <h3 class="text-base sm:text-lg font-black text-secondary-900 flex items-center gap-2">
                        <span>Facebook Ad Studio (মিনা বাজার স্টাইল এআই ক্রিয়েটিভ ও ব্যানার)</span>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">1080x1080 HD</span>
                    </h3>
                    <p class="text-xs text-secondary-500">বিভিন্ন দিবস, পণ্য অফার, মজার মিমস ও লাইফস্টাইল ব্যানার তৈরি করুন—লোগো, ওয়েবসাইট ও মোবাইল নাম্বার স্বয়ংক্রিয়ভাবে যুক্ত হবে।</p>
                </div>
            </div>
            <button type="button" onclick="closeBannerStudio()" class="p-2 text-secondary-400 hover:text-secondary-600 rounded-xl hover:bg-secondary-100 cursor-pointer">
                <ion-icon name="close-outline" class="text-2xl"></ion-icon>
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- Left Column: Live Canvas Preview & Facebook Post Card (5 Cols) -->
            <div class="lg:col-span-5 flex flex-col items-center">
                <!-- Poster Display Mode Switcher (Full AI Poster vs Combo Cards) -->
                <div class="w-full max-w-[420px] mb-2">
                    <div class="grid grid-cols-2 gap-1 bg-secondary-100 p-1 rounded-xl border border-secondary-200 text-center">
                        <button type="button" onclick="setPosterLayout('full_ai')" id="layout-btn-full_ai" 
                                class="py-1.5 px-2 rounded-lg font-bold text-xs transition-all flex items-center justify-center gap-1.5 bg-white text-emerald-800 shadow-xs cursor-pointer">
                            <span>🌟 সম্পূর্ণ AI ফটো পোস্টার</span>
                        </button>
                        <button type="button" onclick="setPosterLayout('combo_cards')" id="layout-btn-combo_cards" 
                                class="py-1.5 px-2 rounded-lg font-bold text-xs transition-all flex items-center justify-center gap-1.5 text-secondary-600 hover:text-secondary-900 cursor-pointer">
                            <span>📋 প্রডাক্ট কম্বো কার্ড</span>
                        </button>
                    </div>
                </div>

                <!-- Overlay Elements Toggle Strip (Headline, Product Card, Brand) -->
                <div class="w-full max-w-[420px] mb-2.5 px-3 py-1.5 flex items-center justify-between text-[11px] font-bold text-secondary-700 bg-secondary-100/70 rounded-xl border border-secondary-200">
                    <span class="text-[10px] uppercase tracking-wider text-secondary-500 font-bold">ছবির উপাদান:</span>
                    <label class="flex items-center gap-1 cursor-pointer hover:text-secondary-950">
                        <input type="checkbox" id="studio_toggle_headline" onchange="drawBannerCanvas()" class="rounded text-emerald-600">
                        <span>হেডলাইন</span>
                    </label>
                    <label class="flex items-center gap-1 cursor-pointer hover:text-secondary-950">
                        <input type="checkbox" id="studio_toggle_product_card" onchange="drawBannerCanvas()" class="rounded text-emerald-600">
                        <span>প্রোডাক্ট কার্ড</span>
                    </label>
                    <label class="flex items-center gap-1 cursor-pointer hover:text-secondary-950">
                        <input type="checkbox" id="studio_toggle_brand_watermark" checked onchange="drawBannerCanvas()" class="rounded text-emerald-600">
                        <span>ব্র্যান্ডিং</span>
                    </label>
                </div>

                <!-- Canvas Stage -->
                <div class="w-full max-w-[420px] aspect-square rounded-2xl overflow-hidden shadow-xl border-2 border-secondary-200 bg-secondary-900 relative">
                    <canvas id="bannerCanvas" width="1080" height="1080" class="w-full h-full block object-contain"></canvas>
                    <div id="canvas-loading-overlay" class="absolute inset-0 bg-secondary-900/70 backdrop-blur-xs hidden flex-col items-center justify-center text-white text-xs font-bold gap-2">
                        <div class="w-8 h-8 border-3 border-emerald-400 border-t-transparent rounded-full animate-spin"></div>
                        <span id="canvas-loading-text">ইমেজ প্রস্তুত হচ্ছে...</span>
                    </div>
                </div>

                <!-- Export & Action Buttons -->
                <div class="mt-3 flex items-center gap-2 w-full max-w-[420px]">
                    <button type="button" onclick="downloadCanvasBanner()" 
                            class="flex-1 py-2 px-2.5 bg-secondary-100 hover:bg-secondary-200 text-secondary-800 font-bold text-xs rounded-xl transition-colors flex items-center justify-center gap-1 shadow-2xs cursor-pointer">
                        <ion-icon name="download-outline" class="text-base"></ion-icon>
                        <span>ডাউনলোড PNG</span>
                    </button>
                    <button type="button" id="saveBannerDraftBtn" onclick="saveBannerToDraftOrPost(false)" 
                            class="flex-1 py-2 px-2.5 bg-secondary-800 hover:bg-secondary-900 text-white font-bold text-xs rounded-xl transition-colors flex items-center justify-center gap-1 shadow-xs cursor-pointer">
                        <ion-icon name="bookmark-outline" class="text-base"></ion-icon>
                        <span>ড্রাফট সেভ</span>
                    </button>
                    <button type="button" id="instantPublishFbBtn" onclick="saveBannerToDraftOrPost(true)" 
                            class="flex-1 py-2 px-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition-colors flex items-center justify-center gap-1 shadow-sm cursor-pointer">
                        <ion-icon name="rocket-outline" class="text-base"></ion-icon>
                        <span>🚀 পাবলিশ</span>
                    </button>
                </div>
                <span id="banner-save-status" class="text-[11px] font-bold mt-1.5 text-center"></span>

                <!-- Live Facebook Post Caption Editor / Preview -->
                <div class="mt-3.5 w-full max-w-[420px] bg-secondary-50 p-3 rounded-2xl border border-secondary-200 text-left">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-[10px] font-bold uppercase tracking-wider text-secondary-600 flex items-center gap-1" for="studio_fb_caption">
                            <ion-icon name="logo-facebook" class="text-blue-600"></ion-icon>
                            <span>ফেসবুক পোস্ট ক্যাপশন</span>
                        </label>
                        <button type="button" onclick="copyCaption(this, 'studio_fb_caption')" class="text-[10px] text-purple-600 hover:text-purple-800 font-bold flex items-center gap-0.5 cursor-pointer">
                            <ion-icon name="copy-outline"></ion-icon>
                            <span>কপি করুন</span>
                        </button>
                    </div>
                    <textarea id="studio_fb_caption" rows="4" 
                              class="w-full p-2.5 bg-white border border-secondary-300 rounded-xl text-xs font-sans text-secondary-800" 
                              placeholder="ফেসবুক পোস্টের সম্পূর্ণ আকর্ষণীয় ক্যাপশন..."></textarea>
                </div>
            </div>

            <!-- Right Column: Interactive Studio Controls & Creative Assistant (7 Cols) -->
            <div class="lg:col-span-7 space-y-4 text-xs font-semibold">
                <input type="hidden" id="studio_post_id" value="">
                <input type="hidden" id="studio_product_image_url" value="">

                <!-- 1. Mode Switcher Tabs (4 Campaign Categories) -->
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-secondary-700 mb-1.5">
                        🎯 ক্যাম্পেইন ধরণ নির্বাচন করুন
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-1.5 bg-secondary-100 p-1 rounded-2xl border border-secondary-200" id="campaignModeTabs">
                        <button type="button" onclick="switchCampaignMode('observance')" id="tab-observance" 
                                class="mode-tab-btn py-2 px-2 rounded-xl font-bold text-xs transition-all flex items-center justify-center gap-1 bg-white text-emerald-800 shadow-xs cursor-pointer">
                            <span>💖 বিভিন্ন দিবস</span>
                        </button>
                        <button type="button" onclick="switchCampaignMode('product_offer')" id="tab-product_offer" 
                                class="mode-tab-btn py-2 px-2 rounded-xl font-bold text-xs transition-all flex items-center justify-center gap-1 text-secondary-600 hover:text-secondary-900 cursor-pointer">
                            <span>🛒 পণ্য অফার</span>
                        </button>
                        <button type="button" onclick="switchCampaignMode('meme')" id="tab-meme" 
                                class="mode-tab-btn py-2 px-2 rounded-xl font-bold text-xs transition-all flex items-center justify-center gap-1 text-secondary-600 hover:text-secondary-900 cursor-pointer">
                            <span>😂 গ্রোসারি মিমস</span>
                        </button>
                        <button type="button" onclick="switchCampaignMode('lifestyle')" id="tab-lifestyle" 
                                class="mode-tab-btn py-2 px-2 rounded-xl font-bold text-xs transition-all flex items-center justify-center gap-1 text-secondary-600 hover:text-secondary-900 cursor-pointer">
                            <span>🍳 লাইফস্টাইল</span>
                        </button>
                    </div>
                </div>

                <!-- 2. Dynamic Category Content Sections -->
                
                <!-- Section A: Observance Days (বিভিন্ন দিবস ভিত্তিক) -->
                <div id="section-observance" class="category-section space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-secondary-700">দিবস নির্বাচন করুন (মিনা বাজার রেফারেন্স ৩ স্টাইল):</span>
                        <span class="text-[10px] text-emerald-600 font-bold">ব্র্যান্ডের সতেজ ভাইব মিল রেখে</span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-1.5">
                        <button type="button" onclick="selectObservancePreset('🩺 বিশ্ব হার্ট দিবস', '২৯ সেপ্টেম্বর', 'হার্টের যত্ন শুরু হোক বাজার থেকেই', 'emerald', 'healthy_heart')" 
                                class="p-2 rounded-xl border border-emerald-300 bg-emerald-50/60 hover:bg-emerald-100 text-emerald-950 font-bold text-[11px] text-left transition-all cursor-pointer">
                            <div class="text-xs">🩺 হার্ট দিবস</div>
                            <div class="text-[9px] text-emerald-700 font-medium">২৯ সেপ্টেম্বর</div>
                        </button>
                        <button type="button" onclick="selectObservancePreset('🌾 বিশ্ব খাদ্য দিবস', '১৬ অক্টোবর', 'নিরাপদ ও পুষ্টিকর খাবার সবার অধিকার', 'amber', 'food_day')" 
                                class="p-2 rounded-xl border border-amber-300 bg-amber-50/60 hover:bg-amber-100 text-amber-950 font-bold text-[11px] text-left transition-all cursor-pointer">
                            <div class="text-xs">🌾 খাদ্য দিবস</div>
                            <div class="text-[9px] text-amber-700 font-medium">১৬ অক্টোবর</div>
                        </button>
                        <button type="button" onclick="selectObservancePreset('🌙 মাহে রমজানুল মোবারক', 'রমজান ডিল', 'সেহরি ও ইফতারের সেরা খাঁটি বাজার', 'navy', 'ramadan')" 
                                class="p-2 rounded-xl border border-indigo-300 bg-indigo-50/60 hover:bg-indigo-100 text-indigo-950 font-bold text-[11px] text-left transition-all cursor-pointer">
                            <div class="text-xs">🌙 মাহে রমজান</div>
                            <div class="text-[9px] text-indigo-700 font-medium">সেহরি ও ইফতার</div>
                        </button>
                        <button type="button" onclick="selectObservancePreset('🕌 পবিত্র ঈদুল ফিতর', 'ঈদ মোবারক', 'উৎসবের আনন্দের সেরা গ্রোসারি বাজার', 'royal', 'eid')" 
                                class="p-2 rounded-xl border border-purple-300 bg-purple-50/60 hover:bg-purple-100 text-purple-950 font-bold text-[11px] text-left transition-all cursor-pointer">
                            <div class="text-xs">🕌 ঈদ মোবারক</div>
                            <div class="text-[9px] text-purple-700 font-medium">ঈদ স্পেশাল</div>
                        </button>
                        <button type="button" onclick="selectObservancePreset('🌺 শুভ নববর্ষ', 'পহেলা বৈশাখ', 'বাঙালির প্রাণের উৎসবে তাজা বাজার', 'ruby', 'boishakh')" 
                                class="p-2 rounded-xl border border-red-300 bg-red-50/60 hover:bg-red-100 text-red-950 font-bold text-[11px] text-left transition-all cursor-pointer">
                            <div class="text-xs">🌺 পহেলা বৈশাখ</div>
                            <div class="text-[9px] text-red-700 font-medium">শুভ নববর্ষ</div>
                        </button>
                        <button type="button" onclick="selectObservancePreset('👩‍👧 বিশ্ব মা দিবস', 'মা দিবস', 'মায়ের হাতের রান্নার খাঁটি স্বাদ', 'amber', 'mothers_day')" 
                                class="p-2 rounded-xl border border-amber-300 bg-amber-50/60 hover:bg-amber-100 text-amber-950 font-bold text-[11px] text-left transition-all cursor-pointer">
                            <div class="text-xs">👩‍👧 মা দিবস</div>
                            <div class="text-[9px] text-amber-700 font-medium">মায়ের হাতের স্বাদ</div>
                        </button>
                        <button type="button" onclick="selectObservancePreset('🥦 শুক্রবারের কাঁচাবাজার', 'শুক্রবার স্পেশাল', 'ছুটির দিনে সকালের তাজা বাজার', 'emerald', 'friday_bazar')" 
                                class="p-2 rounded-xl border border-emerald-300 bg-emerald-50/60 hover:bg-emerald-100 text-emerald-950 font-bold text-[11px] text-left transition-all cursor-pointer">
                            <div class="text-xs">🥦 শুক্রবারের হাট</div>
                            <div class="text-[9px] text-emerald-700 font-medium">ছুটির দিনের বাজার</div>
                        </button>
                        <button type="button" onclick="selectObservancePreset('🛒 মাসের শুরুর বাজার', 'মাসের শুরু ডিল', 'পুরো মাসের সেরা সাশ্রয়ী সদাই', 'navy', 'month_start')" 
                                class="p-2 rounded-xl border border-slate-300 bg-slate-50/60 hover:bg-slate-100 text-slate-950 font-bold text-[11px] text-left transition-all cursor-pointer">
                            <div class="text-xs">🛒 মাসের শুরুর বাজার</div>
                            <div class="text-[9px] text-slate-700 font-medium">মেগা সাশ্রয়</div>
                        </button>
                    </div>
                </div>

                <!-- Section B: Viral Grocery Memes (মজার গ্রোসারি মিমস) -->
                <div id="section-meme" class="category-section hidden space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-secondary-700">বাংলাদেশি ভাইরাল গ্রোসারি মিমস আইডিয়া:</span>
                        <span class="text-[10px] text-purple-600 font-bold">১০ গুণ বেশি শেয়ার ও রিচ</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <button type="button" onclick="selectMemePreset('🏋️‍♂️ বাজারের ভারী ব্যাগ তোলাই আসল জিম!', 'বাজারের ব্যাগ তুললে জিমের টাকা বেঁচে যায়! আর সদাই দরকার থাকলে হোম ডেলিভারিতে কষ্টও নেই!', 'জিম বনাম বাজার', 'emerald')" 
                                class="p-2.5 rounded-xl border border-purple-200 bg-purple-50/50 hover:bg-purple-100 text-purple-950 font-bold text-[11px] text-left transition-all cursor-pointer">
                            🏋️‍♂️ বাজারের ভারী ব্যাগ তোলাই আসল জিম!
                        </button>
                        <button type="button" onclick="selectMemePreset('📝 বউয়ের দেওয়া বাজারের ফর্দ বনাম আমার মেমরি!', 'কাঁচামরিচ আনতে গিয়ে যদি আইসক্রিম নিয়ে ফেরেন, তাহলে সদাই দরকারে অনলাইনে লিস্ট মিলিয়ে অর্ডার করাই নিরাপদ!', 'বউয়ের ফর্দ', 'ruby')" 
                                class="p-2.5 rounded-xl border border-purple-200 bg-purple-50/50 hover:bg-purple-100 text-purple-950 font-bold text-[11px] text-left transition-all cursor-pointer">
                            📝 বউয়ের দেওয়া বাজারের ফর্দ বনাম আমার মেমরি!
                        </button>
                        <button type="button" onclick="selectMemePreset('🥚 হবু বউয়ের চেয়েও এখন ডিমের চিন্তা বেশি!', 'দেশজুড়ে ডিমের দাম নিয়ে যতই হইচই হোক, সদাই দরকারে পাবেন সবচেয়ে সেরা সাশ্রয়ী দাম!', 'ডিম স্পেশাল', 'amber')" 
                                class="p-2.5 rounded-xl border border-purple-200 bg-purple-50/50 hover:bg-purple-100 text-purple-950 font-bold text-[11px] text-left transition-all cursor-pointer">
                            🥚 হবু বউয়ের চেয়েও এখন ডিমের চিন্তা বেশি!
                        </button>
                        <button type="button" onclick="selectMemePreset('🏃‍♂️ শুক্রবার সকালে কাঁচাবাজার জয়ের অনুভূতি!', 'ছুটির সকালে তাজা মাছ আর সতেজ সবজি কিনতে যুদ্ধ করার দিন শেষ—সদাই দরকার পৌঁছে দিচ্ছে এক ক্লিকে!', 'শুক্রবার জয়', 'emerald')" 
                                class="p-2.5 rounded-xl border border-purple-200 bg-purple-50/50 hover:bg-purple-100 text-purple-950 font-bold text-[11px] text-left transition-all cursor-pointer">
                            🏃‍♂️ শুক্রবার সকালে কাঁচাবাজার জয়ের অনুভূতি!
                        </button>
                    </div>
                </div>

                <!-- Section C: Product Offers & Price Drops (পণ্য অফার ও ডিসকাউন্ট) -->
                <div id="section-product_offer" class="category-section hidden space-y-2.5">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] font-bold text-secondary-700">পণ্য নির্বাচন (এক বা একাধিক মার্ক করুন):</span>
                            <div class="flex items-center gap-1 bg-secondary-100 p-0.5 rounded-xl text-[10px]">
                                <button type="button" id="filter-btn-price-drop" onclick="filterStudioProducts('price_drop')" 
                                        class="px-2 py-0.5 rounded-lg bg-yellow-400 text-emerald-950 font-black cursor-pointer">
                                    ⚡ দাম কমেছে (<?= count($recentPriceChanged ?? []) ?>)
                                </button>
                                <button type="button" id="filter-btn-all" onclick="filterStudioProducts('all')" 
                                        class="px-2 py-0.5 rounded-lg text-secondary-600 font-bold hover:text-secondary-900 cursor-pointer">
                                    সব পণ্য
                                </button>
                            </div>
                        </div>
                        <input type="text" id="studio-product-search" oninput="searchStudioProducts(this.value)" 
                               placeholder="🔍 পণ্য খুঁজুন..." 
                               class="px-2.5 py-1 text-xs border border-secondary-300 rounded-xl bg-white w-36">
                    </div>

                    <!-- Scrollable Product Checklist with Images & Prices -->
                    <div id="studio-product-checklist" class="max-h-44 overflow-y-auto space-y-1.5 p-2 bg-secondary-50/80 rounded-2xl border border-secondary-200">
                        <!-- Populated dynamically via JS -->
                    </div>

                    <div id="selected-products-summary" class="text-[11px] font-bold text-emerald-700 flex items-center justify-between">
                        <span>নির্বাচিত পণ্য: <span id="selected-prod-count">১</span> টি</span>
                        <span id="selected-prod-savings" class="text-[10px] text-secondary-600"></span>
                    </div>
                </div>

                <!-- Section D: Kitchen & Lifestyle (রান্নাবান্না ও লাইফস্টাইল) -->
                <div id="section-lifestyle" class="category-section hidden space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-secondary-700">সুপারমার্কেট ও কিচেন লাইফস্টাইল (মিনা বাজার রেফারেন্স ২ ও ৪ স্টাইল):</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <button type="button" onclick="selectLifestylePreset('🍳 প্রতিদিনের রান্নায় থাকুক ঘিয়ের খাঁটি সুবাস', '২৫৳ ছাড়', 'খাঁটি ঘি ও রান্নার তেল', 'amber', 'cooking_ghee')" 
                                class="p-2 rounded-xl border border-amber-300 bg-amber-50/60 hover:bg-amber-100 text-amber-950 font-bold text-[11px] text-left transition-all cursor-pointer">
                            🍳 ঘিয়ের খাঁটি সুবাস
                            <div class="text-[9px] text-amber-700 font-normal mt-0.5">গৃহিণীর রান্নাঘর ও ঘি অফার</div>
                        </button>
                        <button type="button" onclick="selectLifestylePreset('🛒 আধুনিক সুপারমার্কেটে সেরা শপিং অভিজ্ঞতা', 'উৎসবের বাজার', 'সদাই দরকার মেগা শপিং', 'emerald', 'supermarket_cart')" 
                                class="p-2 rounded-xl border border-emerald-300 bg-emerald-50/60 hover:bg-emerald-100 text-emerald-950 font-bold text-[11px] text-left transition-all cursor-pointer">
                            🛒 শপিং ট্রলি ও সুপারমার্কেট
                            <div class="text-[9px] text-emerald-700 font-normal mt-0.5">ঝকঝকে স্টোর ও তাজা গ্রোসারি</div>
                        </button>
                        <button type="button" onclick="selectLifestylePreset('🌿 ১০০% অর্গানিক ও রাসায়নিকমুক্ত খাবার', 'ফার্ম-ফ্রেশ', 'স্বাস্থ্যকর তাজা ফলমূল ও শাকসবজি', 'emerald', 'fresh_veggies')" 
                                class="p-2 rounded-xl border border-emerald-300 bg-emerald-50/60 hover:bg-emerald-100 text-emerald-950 font-bold text-[11px] text-left transition-all cursor-pointer">
                            🌿 অর্গানিক ও স্বাস্থ্যকর
                            <div class="text-[9px] text-emerald-700 font-normal mt-0.5">১০০% কেমিক্যাল মুক্ত</div>
                        </button>
                    </div>
                </div>

                <!-- 3. Full AI Image Generator & Creative Assistant -->
                <div class="space-y-2.5">
                    <!-- A. Direct Photorealistic AI Poster Image Generator (FLUX.1 / Google Imagen 3) -->
                    <div class="p-3 bg-gradient-to-r from-purple-50 via-indigo-50 to-pink-50 rounded-2xl border border-purple-200 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-1.5 text-purple-900 font-bold text-xs">
                                <ion-icon name="image-outline" class="text-base text-purple-600"></ion-icon>
                                <span>🎨 সম্পূর্ণ এআই বিজ্ঞাপন ছবি বানান (Meena Bazar Style Full AI)</span>
                            </div>
                            <span class="text-[10px] text-purple-700 font-bold bg-purple-100 px-2 py-0.5 rounded-full">FLUX.1 • 1080x1080 HD</span>
                        </div>
                        
                        <!-- 1-Click Meena Bazar Reference Presets -->
                        <div class="flex flex-wrap gap-1">
                            <button type="button" onclick="generateAiPosterImage('Bangladeshi happy smiling homemaker in modern kitchen cooking delicious curry with pure golden ghee in cooking pot, Meena Bazar retail advertising photography style, warm soft kitchen lighting, 8k, photorealistic')" 
                                    class="px-2 py-1 bg-white hover:bg-purple-100 border border-purple-200 rounded-lg text-[10px] font-bold text-purple-900 cursor-pointer transition-colors shadow-2xs">
                                🍳 খাঁটি ঘিয়ে রান্না (Ref 4)
                            </button>
                            <button type="button" onclick="generateAiPosterImage('Bangladeshi caring father and young child smiling together at dining table with healthy fresh colorful salad bowl and fruits, World Heart Day healthy diet grocery theme, bright pleasant morning sunlight, commercial retail photography')" 
                                    class="px-2 py-1 bg-white hover:bg-purple-100 border border-purple-200 rounded-lg text-[10px] font-bold text-purple-900 cursor-pointer transition-colors shadow-2xs">
                                🥗 সুস্থ পরিবার ও হার্ট দিবস (Ref 3)
                            </button>
                            <button type="button" onclick="generateAiPosterImage('Modern premium Bangladeshi supermarket grocery store aisle, clean shopping cart full of fresh colorful vegetables, dairy and fruits, gleaming floor, Meena Bazar flagship store aesthetic, 8k photorealistic')" 
                                    class="px-2 py-1 bg-white hover:bg-purple-100 border border-purple-200 rounded-lg text-[10px] font-bold text-purple-900 cursor-pointer transition-colors shadow-2xs">
                                🛒 সুপারমার্কেটে ট্রলি ভর্তি বাজার (Ref 2)
                            </button>
                            <button type="button" onclick="generateAiPosterImage('Artistic rustic wooden crate overflowing with farm fresh crisp green vegetables, red tomatoes, garlic, broccoli, dewdrops, commercial food advertising photography, vibrant colors')" 
                                    class="px-2 py-1 bg-white hover:bg-purple-100 border border-purple-200 rounded-lg text-[10px] font-bold text-purple-900 cursor-pointer transition-colors shadow-2xs">
                                🌿 ফার্ম-ফ্রেশ শাকসবজি
                            </button>
                            <button type="button" onclick="generateAiPosterImage('Bangladeshi family celebrating Eid festival together around a festive dining table with delicious homemade food and sweets, warm celebration lighting, commercial photography')" 
                                    class="px-2 py-1 bg-white hover:bg-purple-100 border border-purple-200 rounded-lg text-[10px] font-bold text-purple-900 cursor-pointer transition-colors shadow-2xs">
                                🕌 ঈদ ও উৎসবের আনন্দ
                            </button>
                        </div>

                        <div class="flex items-center gap-2">
                            <input type="text" id="ai_image_prompt_custom" 
                                   placeholder="আপনার ইচ্ছেমতো প্রম্পট লিখুন (যেমন: হাসিমুখ ক্রেতা, রান্নার দৃশ্য, তাজা মাছ-মাংস...)" 
                                   class="flex-1 px-3 py-1.5 bg-white border border-purple-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-purple-500">
                            <button type="button" onclick="generateAiPosterImage()" 
                                    class="px-3.5 py-1.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition-all shrink-0 flex items-center gap-1.5 cursor-pointer">
                                <ion-icon name="color-wand-outline"></ion-icon>
                                <span>ছবি বানান</span>
                            </button>
                        </div>
                        <span id="ai-image-gen-status" class="text-[10px] font-bold block"></span>
                    </div>

                    <!-- B. Gemini AI Magic Content & Caption Assistant -->
                    <div class="p-3 bg-gradient-to-r from-emerald-50 via-teal-50 to-blue-50 rounded-2xl border border-emerald-200 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-1.5 text-emerald-900 font-bold text-xs">
                                <ion-icon name="sparkles" class="text-base text-emerald-600"></ion-icon>
                                <span>🤖 Gemini AI হেডলাইন ও ফেসবুক ক্যাপশন অ্যাসিস্ট্যান্ট</span>
                            </div>
                            <label class="flex items-center gap-1.5 text-[10px] text-secondary-600 font-semibold cursor-pointer">
                                <input type="checkbox" id="ai-generate-image-toggle" checked class="rounded text-emerald-600 focus:ring-emerald-500">
                                <span>একসাথে সম্পূর্ণ AI ছবিও তৈরি করুন</span>
                            </label>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <input type="text" id="ai_campaign_prompt" placeholder="যেমন: বিশ্ব হার্ট দিবসের জন্য তাজা সবজির অফার অথবা ডিমের মিমস..." 
                                   class="flex-1 px-3 py-1.5 bg-white border border-emerald-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-emerald-500">
                            <button type="button" onclick="generateAiCampaignConcept()" 
                                    class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors shrink-0 flex items-center gap-1.5 cursor-pointer">
                                <ion-icon name="sparkles"></ion-icon>
                                <span>ম্যাজিক কন্টেন্ট বানান</span>
                            </button>
                        </div>
                        <span id="ai-campaign-status" class="text-[10px] font-bold block"></span>
                    </div>
                </div>

                <!-- 4. Text & Badges Customization Form -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-secondary-700 mb-1" for="studio_headline">
                            শীর্ষ আকর্ষণ / হেডলাইন
                        </label>
                        <input type="text" id="studio_headline" oninput="drawBannerCanvas()" 
                               value="" placeholder="যেমন: ৫০০০৳ কিনাকাটায় ফ্রি ডেলিভারি / তাজা বাজার অফার..." 
                               class="w-full px-3 py-1.5 bg-white border border-secondary-300 rounded-xl text-xs font-bold text-secondary-900 focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-secondary-700 mb-1" for="studio_badge_text">
                            কর্নার বা ইভেন্ট ব্যাজ
                        </label>
                        <input type="text" id="studio_badge_text" oninput="drawBannerCanvas()" 
                               value="" placeholder="যেমন: ফ্রি ডেলিভারি / মেগা অফার..." 
                               class="w-full px-3 py-1.5 bg-white border border-secondary-300 rounded-xl text-xs font-bold text-secondary-900 focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <!-- 5. Automated Branding Capsules (Website, Phone, Logo - Meena Bazar Style) -->
                <div class="bg-secondary-50/70 p-3 rounded-2xl border border-secondary-200 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-secondary-700">🏢 স্বয়ংক্রিয় ব্র্যান্ডিং উপাদান (ছবিতে অটোমেটিক ড্র হবে)</span>
                        <span class="text-[9px] text-emerald-600 font-bold">মিনা বাজার স্ট্যান্ডার্ড</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label class="block text-[9px] font-bold text-secondary-600 mb-0.5" for="studio_site_name">দোকানের নাম / লোগো</label>
                            <input type="text" id="studio_site_name" oninput="drawBannerCanvas()" 
                                   value="সদাই দরকার" 
                                   class="w-full px-2 py-1 bg-white border border-secondary-300 rounded-lg text-xs font-bold text-secondary-900">
                        </div>
                        <div>
                            <label class="block text-[9px] font-bold text-secondary-600 mb-0.5" for="studio_phone">হটলাইন ক্যাপসুল (বামে)</label>
                            <input type="text" id="studio_phone" oninput="drawBannerCanvas()" 
                                   value="<?= htmlspecialchars($settings['contact_phone'] ?? $settings['site_phone'] ?? '01609448066') ?>" 
                                   class="w-full px-2 py-1 bg-white border border-secondary-300 rounded-lg text-xs font-bold text-secondary-900">
                        </div>
                        <div>
                            <label class="block text-[9px] font-bold text-secondary-600 mb-0.5" for="studio_website">ওয়েবসাইট ক্যাপসুল (ডানে)</label>
                            <input type="text" id="studio_website" oninput="drawBannerCanvas()" 
                                   value="sodaidorkar.com" 
                                   class="w-full px-2 py-1 bg-white border border-secondary-300 rounded-lg text-xs font-bold text-secondary-900">
                        </div>
                    </div>
                </div>

                <!-- 6. Theme Picker & Backdrop Selector -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-secondary-700 mb-1">
                            🎨 ব্যানার থিম
                        </label>
                        <div class="grid grid-cols-3 gap-1.5" id="themeButtonsGrid">
                            <button type="button" onclick="setStudioTheme('emerald')" class="theme-btn p-1.5 rounded-lg border border-emerald-500 bg-emerald-50 text-emerald-950 font-bold text-[10px] truncate cursor-pointer" data-theme="emerald">🌿 Farm Fresh</button>
                            <button type="button" onclick="setStudioTheme('ruby')" class="theme-btn p-1.5 rounded-lg border border-secondary-200 bg-white text-secondary-700 font-bold text-[10px] truncate cursor-pointer" data-theme="ruby">💥 Mega Deal</button>
                            <button type="button" onclick="setStudioTheme('amber')" class="theme-btn p-1.5 rounded-lg border border-secondary-200 bg-white text-secondary-700 font-bold text-[10px] truncate cursor-pointer" data-theme="amber">☀️ Morning</button>
                            <button type="button" onclick="setStudioTheme('navy')" class="theme-btn p-1.5 rounded-lg border border-secondary-200 bg-white text-secondary-700 font-bold text-[10px] truncate cursor-pointer" data-theme="navy">🌙 Midnight</button>
                            <button type="button" onclick="setStudioTheme('royal')" class="theme-btn p-1.5 rounded-lg border border-secondary-200 bg-white text-secondary-700 font-bold text-[10px] truncate cursor-pointer" data-theme="royal">🍇 Royal</button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-secondary-700 mb-1">
                            🖼️ ব্যাকড্রপ সিন
                        </label>
                        <select id="studio_backdrop_scene" onchange="onBackdropSceneChange(this.value)" class="w-full px-2.5 py-1.5 bg-white border border-secondary-300 rounded-xl text-xs font-bold text-secondary-900">
                            <option value="none">স্টুডিও ৩ডি স্টেজ (সফট শ্যাডো ব্যাকড্রপ)</option>
                            <option value="fresh_veggies" selected>🌿 তাজা শাকসবজি ও ফলমূল</option>
                            <option value="supermarket_cart">🛒 সুপারমার্কেট শপিং কার্ট</option>
                            <option value="cooking_ghee">🍳 রান্নাঘর ও গৃহিণীর রান্না</option>
                            <option value="healthy_heart">🩺 স্বাস্থ্যকর পরিবার ও হার্ট দিবস</option>
                            <option value="festive_facade">🎉 উৎসবের বাজার সুপারমার্কেট</option>
                        </select>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 2. Custom Post Modal (Integrated with Meena Bazar AI Studio) -->
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

        <!-- Direct Studio Launcher Banner inside Custom Post Modal -->
        <div class="mb-4 p-3 bg-gradient-to-r from-emerald-50 via-teal-50 to-purple-50 rounded-2xl border border-emerald-200 flex items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-base shadow-xs">
                    <ion-icon name="color-palette-outline"></ion-icon>
                </span>
                <div>
                    <div class="text-xs font-black text-secondary-900">মিনা বাজার স্টাইল ক্রিয়েটিভ ব্যানার চান?</div>
                    <div class="text-[10px] text-secondary-500 font-medium">লোগো, ওয়েবসাইট ও মোবাইল নম্বর স্বয়ংক্রিয়ভাবে ছবিতে বসে যাবে।</div>
                </div>
            </div>
            <button type="button" onclick="openStudioFromCustomPost()" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors shrink-0 flex items-center gap-1 cursor-pointer">
                <ion-icon name="sparkles"></ion-icon>
                <span>স্টুডিও ওপেন করুন</span>
            </button>
        </div>

        <form action="<?= $base ?>/admin/marketing/save-post" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs font-semibold">
            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
            <input type="hidden" name="id" value="0">
            <input type="hidden" name="image_path" id="custom_image_path" value="">

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-[11px] font-bold text-secondary-700 uppercase">পণ্য নির্বাচন করুন (ঐচ্ছিক)</label>
                    <div class="flex items-center gap-1 text-[10px]">
                        <button type="button" onclick="filterCustomPostSelect('price_drop')" class="text-emerald-700 font-bold hover:underline cursor-pointer">⚡ আজকেই দাম কমেছে (<?= count($recentPriceChanged ?? []) ?>)</button>
                        <span>|</span>
                        <button type="button" onclick="filterCustomPostSelect('all')" class="text-secondary-500 font-semibold hover:underline cursor-pointer">সব পণ্য</button>
                    </div>
                </div>
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
                        <option value="ai_gemini">🤖 Gemini AI</option>
                        <option value="template_no_ai">⚡ Smart Template (AI ছাড়া)</option>
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

<script>
let currentStudioTheme = 'emerald';
let currentCampaignMode = 'observance';
let currentPosterLayout = 'full_ai'; // 🌟 'full_ai' (Meena Bazar Full Visual) or 'combo_cards'
let selectedProductIds = [];
let studioBackdropImgElement = null;
let studioProductImgElements = {};
let currentProductFilter = 'price_drop';

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
        priceBg: 'rgba(0, 0, 0, 0.7)',
        priceAccent: '#34d399',
        saveBg: '#fbbf24',
        saveText: '#78350f',
        pillBg: '#047857'
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
        priceBg: 'rgba(0, 0, 0, 0.7)',
        priceAccent: '#fb7185',
        saveBg: '#fef08a',
        saveText: '#713f12',
        pillBg: '#be123c'
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
        priceBg: 'rgba(0, 0, 0, 0.7)',
        priceAccent: '#fbbf24',
        saveBg: '#fef3c7',
        saveText: '#92400e',
        pillBg: '#b45309'
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
        saveText: '#ffffff',
        pillBg: '#0369a1'
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
        saveText: '#701a75',
        pillBg: '#7e22ce'
    }
};

// Preset Backdrop Images (using local public/images)
const BACKDROP_PRESETS = {
    fresh_veggies: '<?= $base ?>/images/fresh_veggies_banner.jpg',
    supermarket_cart: '<?= $base ?>/images/grocery_cart_hero.jpg',
    cooking_ghee: '<?= $base ?>/images/hero_basket.jpg',
    healthy_heart: '<?= $base ?>/images/fresh_veggies_banner.jpg',
    festive_facade: '<?= $base ?>/images/free_delivery_500.jpg'
};

// Pass PHP Product Collections to JS
const ALL_PRODUCTS = <?= json_encode(array_map(function($p) use ($base) {
    $sell = floatval($p['sell_price']);
    $reg = floatval($p['regular_price'] ?? 0);
    return [
        'id' => intval($p['id']),
        'name' => $p['name'],
        'sell_price' => $sell,
        'regular_price' => $reg,
        'has_discount' => ($reg > $sell),
        'savings' => ($reg > $sell) ? ($reg - $sell) : 0,
        'discount_percent' => ($reg > $sell) ? round((($reg - $sell) / $reg) * 100) : 0,
        'unit' => $p['base_unit'] ?? 'পিস',
        'image_url' => !empty($p['image_path']) ? \Models\Product::getImageUrl($p['image_path'], $base) : '',
        'category_name' => $p['category_name'] ?? ''
    ];
}, $products), JSON_UNESCAPED_UNICODE) ?>;

const RECENT_PRICE_CHANGED = <?= json_encode(array_map(function($p) use ($base) {
    $sell = floatval($p['sell_price']);
    $reg = floatval($p['regular_price'] ?? 0);
    return [
        'id' => intval($p['id']),
        'name' => $p['name'],
        'sell_price' => $sell,
        'regular_price' => $reg,
        'has_discount' => ($reg > $sell),
        'savings' => ($reg > $sell) ? ($reg - $sell) : 0,
        'discount_percent' => ($reg > $sell) ? round((($reg - $sell) / $reg) * 100) : 0,
        'unit' => $p['base_unit'] ?? 'পিস',
        'image_url' => !empty($p['image_path']) ? \Models\Product::getImageUrl($p['image_path'], $base) : '',
        'category_name' => $p['category_name'] ?? ''
    ];
}, $recentPriceChanged ?? []), JSON_UNESCAPED_UNICODE) ?>;

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    // Start in clean full AI poster mode with no obstructive overlays
    selectedProductIds = [];
    renderStudioProductChecklist();
    setPosterLayout('full_ai');
    onBackdropSceneChange('fresh_veggies');
});

function copyCaption(btn, textareaId) {
    const el = document.getElementById(textareaId);
    if (!el) return;
    el.select();
    navigator.clipboard.writeText(el.value).then(() => {
        const originalText = btn.innerHTML;
        btn.innerHTML = '✅ কপি হয়েছে!';
        setTimeout(() => { btn.innerHTML = originalText; }, 2000);
    });
}

function openStudioModalTab(mode = 'observance') {
    document.getElementById('bannerStudioModal').classList.remove('hidden');
    switchCampaignMode(mode);
}

function closeBannerStudio() {
    document.getElementById('bannerStudioModal').classList.add('hidden');
}

function switchCampaignMode(mode) {
    currentCampaignMode = mode;
    // Update tab buttons
    document.querySelectorAll('.mode-tab-btn').forEach(btn => {
        btn.classList.remove('bg-white', 'text-emerald-800', 'shadow-xs');
        btn.classList.add('text-secondary-600');
    });
    const activeTab = document.getElementById(`tab-${mode}`);
    if (activeTab) {
        activeTab.classList.add('bg-white', 'text-emerald-800', 'shadow-xs');
        activeTab.classList.remove('text-secondary-600');
    }

    // Toggle category sections
    document.querySelectorAll('.category-section').forEach(sec => sec.classList.add('hidden'));
    const activeSec = document.getElementById(`section-${mode}`);
    if (activeSec) activeSec.classList.remove('hidden');

    if (mode === 'observance') {
        selectObservancePreset('🩺 বিশ্ব হার্ট দিবস', '২৯ সেপ্টেম্বর', 'হার্টের যত্ন শুরু হোক বাজার থেকেই', 'emerald', 'healthy_heart');
    } else if (mode === 'meme') {
        selectMemePreset('🏋️‍♂️ বাজারের ভারী ব্যাগ তোলাই আসল জিম!', 'বাজারের ব্যাগ তুললে জিমের টাকা বেঁচে যায়! আর সদাই দরকার থাকলে হোম ডেলিভারিতে কষ্টও নেই!', 'জিম বনাম বাজার', 'emerald');
    } else if (mode === 'lifestyle') {
        selectLifestylePreset('🍳 প্রতিদিনের রান্নায় থাকুক ঘিয়ের খাঁটি সুবাস', '২৫৳ ছাড়', 'খাঁটি ঘি ও রান্নার তেল', 'amber', 'cooking_ghee');
    } else if (mode === 'product_offer') {
        renderStudioProductChecklist();
        drawBannerCanvas();
    }
}

function selectObservancePreset(title, badge, headline, theme, backdrop) {
    document.getElementById('studio_headline').value = headline;
    document.getElementById('studio_badge_text').value = `${badge} • ${title}`;
    document.getElementById('studio_fb_caption').value = `💚 ${title} উপলক্ষ্যে বিশেষ বার্তা!\n\n${headline}\n\nসদাই দরকার আপনার পরিবারের সুস্বাস্থ্য নিশ্চিত করতে সরবরাহ করে ১০০% তাজা ফলমূল, সবুজ শাকসবজি ও রাসায়নিকমুক্ত খাঁটি গ্রোসারি।\n\n🚚 ৩০-৬০ মিনিটে দ্রুত হোম ডেলিভারি\n💵 ক্যাশ অন ডেলিভারি (পণ্য দেখে মূল্য পরিশোধ)\n📞 হটলাইন: ${document.getElementById('studio_phone')?.value || '01609448066'}\n🌐 ওয়েবসাইট: ${document.getElementById('studio_website')?.value || 'sodaidorkar.com'}\n\n#SodaiDorkar #বিশ্ব_হার্ট_দিবস #সুস্থ_জীবন #তাজা_বাজার #অনলাইন_সুপারমার্কেট`;
    setStudioTheme(theme);
    onBackdropSceneChange(backdrop);
}

function selectMemePreset(headline, memeText, badge, theme) {
    document.getElementById('studio_headline').value = headline;
    document.getElementById('studio_badge_text').value = `😂 ${badge}`;
    document.getElementById('studio_fb_caption').value = `😂 ${headline}\n\n${memeText}\n\nআপনার প্রতিদিনের বাজার নিয়ে নো চিন্তা—সদাই দরকার আছে আপনার পাশে! সেরা মূল্যে তাজা বাজার ঘরে বসেই অর্ডার করুন।\n\n🛒 অর্ডার করতে ইনবক্স করুন অথবা ভিজিট করুন: ${document.getElementById('studio_website')?.value || 'sodaidorkar.com'}\n📞 হটলাইন: ${document.getElementById('studio_phone')?.value || '01609448066'}\n\n#SodaiDorkar #গ্রোসারি_মিমস #বাজারের_গল্প #অনলাইন_বাজার`;
    setStudioTheme(theme);
    drawBannerCanvas();
}

function selectLifestylePreset(headline, badge, subText, theme, backdrop) {
    document.getElementById('studio_headline').value = headline;
    document.getElementById('studio_badge_text').value = `✨ ${badge}`;
    document.getElementById('studio_fb_caption').value = `🍳 ${headline}\n\nরান্নায় খাঁটি স্বাদ আর সুবাস বজায় রাখতে ব্যবহার করুন সেরা ব্র্যান্ডের খাঁটি ঘি, সরিষার তেল ও বাছাইকৃত খাঁটি মসলা।\n\n✨ সীমিত সময়ের বিশেষ অফারে পাচ্ছেন আকর্ষণীয় ছাড়!\n🚚 সুপারফাস্ট হোম ডেলিভারি\n📞 হটলাইন: ${document.getElementById('studio_phone')?.value || '01609448066'}\n🌐 ভিজিট করুন: ${document.getElementById('studio_website')?.value || 'sodaidorkar.com'}\n\n#SodaiDorkar #খাঁটি_ঘি #রান্নাবান্না #সুপারমার্কেট_অফার`;
    setStudioTheme(theme);
    onBackdropSceneChange(backdrop);
}

function setStudioTheme(themeKey) {
    if (!THEMES[themeKey]) return;
    currentStudioTheme = themeKey;
    document.querySelectorAll('.theme-btn').forEach(btn => {
        if (btn.dataset.theme === themeKey) {
            btn.classList.remove('border-secondary-200', 'bg-white', 'text-secondary-700');
            btn.classList.add('border-emerald-500', 'bg-emerald-50', 'text-emerald-950');
        } else {
            btn.classList.add('border-secondary-200', 'bg-white', 'text-secondary-700');
            btn.classList.remove('border-emerald-500', 'bg-emerald-50', 'text-emerald-950');
        }
    });
    drawBannerCanvas();
}

function onBackdropSceneChange(sceneKey) {
    const sel = document.getElementById('studio_backdrop_scene');
    if (sel && sel.value !== sceneKey) sel.value = sceneKey;

    if (sceneKey === 'none' || !BACKDROP_PRESETS[sceneKey]) {
        studioBackdropImgElement = null;
        drawBannerCanvas();
        return;
    }

    const img = new Image();
    img.crossOrigin = 'anonymous';
    img.onload = () => {
        studioBackdropImgElement = img;
        drawBannerCanvas();
    };
    img.onerror = () => {
        studioBackdropImgElement = null;
        drawBannerCanvas();
    };
    img.src = BACKDROP_PRESETS[sceneKey];
}

// Product Checklist & Multi-Product Marking
function filterStudioProducts(mode) {
    currentProductFilter = mode;
    const btnDrop = document.getElementById('filter-btn-price-drop');
    const btnAll = document.getElementById('filter-btn-all');
    if (mode === 'price_drop') {
        btnDrop.classList.add('bg-yellow-400', 'text-emerald-950', 'font-black');
        btnDrop.classList.remove('text-secondary-600');
        btnAll.classList.remove('bg-yellow-400', 'text-emerald-950', 'font-black');
        btnAll.classList.add('text-secondary-600');
    } else {
        btnAll.classList.add('bg-yellow-400', 'text-emerald-950', 'font-black');
        btnAll.classList.remove('text-secondary-600');
        btnDrop.classList.remove('bg-yellow-400', 'text-emerald-950', 'font-black');
        btnDrop.classList.add('text-secondary-600');
    }
    renderStudioProductChecklist();
}

function searchStudioProducts(query) {
    renderStudioProductChecklist(query);
}

function renderStudioProductChecklist(query = '') {
    const container = document.getElementById('studio-product-checklist');
    if (!container) return;

    let list = (currentProductFilter === 'price_drop') ? RECENT_PRICE_CHANGED : ALL_PRODUCTS;
    if (query.trim()) {
        const q = query.toLowerCase();
        list = list.filter(p => p.name.toLowerCase().includes(q) || (p.category_name && p.category_name.toLowerCase().includes(q)));
    }

    if (list.length === 0) {
        container.innerHTML = '<div class="text-center py-4 text-secondary-400 text-xs">কোনো পণ্য পাওয়া যায়নি</div>';
        return;
    }

    let html = '';
    list.forEach(p => {
        const isChecked = selectedProductIds.includes(p.id);
        const discountBadge = p.has_discount ? `<span class="px-1.5 py-0.2 rounded bg-red-100 text-red-700 font-bold text-[9px]">${p.discount_percent}% ছাড়</span>` : '';
        const regStrike = p.has_discount ? `<span class="line-through text-secondary-400 text-[10px]">৳${p.regular_price}</span>` : '';
        
        html += `
        <label class="flex items-center justify-between p-2 rounded-xl bg-white border ${isChecked ? 'border-emerald-500 ring-1 ring-emerald-500' : 'border-secondary-200'} hover:border-emerald-400 transition-all cursor-pointer">
            <div class="flex items-center gap-2">
                <input type="checkbox" onchange="toggleProductSelection(${p.id})" ${isChecked ? 'checked' : ''} 
                       class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500">
                ${p.image_url ? `<img src="${p.image_url}" class="w-8 h-8 rounded-lg object-contain bg-secondary-50 border border-secondary-100" onerror="this.style.display='none'">` : ''}
                <div class="truncate max-w-[200px]">
                    <div class="font-bold text-secondary-900 truncate text-xs">${p.name}</div>
                    <div class="text-[10px] text-secondary-500">${p.unit}</div>
                </div>
            </div>
            <div class="text-right shrink-0">
                <div class="font-black text-emerald-700 text-xs">৳${p.sell_price}</div>
                <div class="flex items-center gap-1 justify-end">${regStrike} ${discountBadge}</div>
            </div>
        </label>
        `;
    });

    container.innerHTML = html;
    updateSelectedProductsSummary();
}

function toggleProductSelection(prodId) {
    prodId = parseInt(prodId);
    if (selectedProductIds.includes(prodId)) {
        selectedProductIds = selectedProductIds.filter(id => id !== prodId);
    } else {
        if (selectedProductIds.length >= 4) {
            alert('সর্বোচ্চ ৪টি পণ্য নির্বাচন করতে পারবেন (কম্বো অফার ব্যানারের জন্য)।');
            return;
        }
        selectedProductIds.push(prodId);
    }
    renderStudioProductChecklist();
    loadSelectedProductImages();
}

function updateSelectedProductsSummary() {
    const countEl = document.getElementById('selected-prod-count');
    const savingsEl = document.getElementById('selected-prod-savings');
    if (!countEl) return;

    countEl.innerText = selectedProductIds.length;
    const selectedProds = ALL_PRODUCTS.filter(p => selectedProductIds.includes(p.id));

    if (selectedProds.length > 0) {
        const totalSavings = selectedProds.reduce((sum, p) => sum + p.savings, 0);
        if (totalSavings > 0) {
            savingsEl.innerText = `মোট সাশ্রয়: ৳${totalSavings.toFixed(0)}`;
        } else {
            savingsEl.innerText = '';
        }
    } else {
        savingsEl.innerText = '';
    }
}

function loadSelectedProductImages() {
    const selectedProds = ALL_PRODUCTS.filter(p => selectedProductIds.includes(p.id));
    selectedProds.forEach(p => {
        if (p.image_url && !studioProductImgElements[p.id]) {
            const img = new Image();
            img.crossOrigin = 'anonymous';
            img.onload = () => {
                studioProductImgElements[p.id] = img;
                drawBannerCanvas();
            };
            img.src = p.image_url;
        }
    });

    if (selectedProds.length === 1) {
        const p = selectedProds[0];
        document.getElementById('studio_headline').value = p.has_discount ? `🔥 ${p.name} - ${p.discount_percent}% ছাড়!` : `🔥 ${p.name} - আজকের সেরা অফার!`;
        document.getElementById('studio_badge_text').value = p.has_discount ? `⚡ ${p.discount_percent}% ছাড়!` : '🌿 ১০০% ফার্ম-ফ্রেশ';
        document.getElementById('studio_fb_caption').value = `💥 বিশেষ অফার: ${p.name}!\n\nঅফার মূল্য: ৳${p.sell_price} (প্রতি ${p.unit})${p.has_discount ? `\nরেগুলার MRP: ৳${p.regular_price}\n💰 সাশ্রয়: ৳${p.savings} (${p.discount_percent}% ছাড়)` : ''}\n\nসদাই দরকারে অর্ডার করলেই পাচ্ছেন দ্রুততম সময়ে ফ্রেশ হোম ডেলিভারি!\n\n🚚 ক্যাশ অন ডেলিভারি সুবিধা\n📞 অর্ডার করতে কল করুন: ${document.getElementById('studio_phone')?.value || '01609448066'}\n🌐 অথবা ভিজিট করুন: ${document.getElementById('studio_website')?.value || 'sodaidorkar.com'}\n\n#SodaiDorkar #মুদি_বাজার #অফার #ডিসকাউন্ট #হোম_ডেলিভারি`;
    } else if (selectedProds.length > 1) {
        document.getElementById('studio_headline').value = `💥 মেগা কম্বো বাজার অফার! (${selectedProds.length}টি পণ্য)`;
        document.getElementById('studio_badge_text').value = '⚡ সুপার সেভার ডিল';
        let prodLines = selectedProds.map(p => `• ${p.name}: ৳${p.sell_price} (${p.unit})`).join('\n');
        document.getElementById('studio_fb_caption').value = `💥 মেগা কম্বো বাজার অফার!\n\n${prodLines}\n\nএকসাথে কিনুন আর উপভোগ করুন বাড়তি সাশ্রয়! স্টক সীমিত, এখনই অর্ডার করুন।\n\n🚚 দ্রুত হোম ডেলিভারি\n📞 কল করুন: ${document.getElementById('studio_phone')?.value || '01609448066'}\n🌐 ওয়েবসাইট: ${document.getElementById('studio_website')?.value || 'sodaidorkar.com'}\n\n#SodaiDorkar #কম্বো_অফার #মেগা_ডিল #বাজার`;
    }
    drawBannerCanvas();
}

// -------------------------------------------------------------------------
// Poster Layout Switcher (Full AI Visual vs Combo Deal Cards)
// -------------------------------------------------------------------------
function setPosterLayout(layout) {
    currentPosterLayout = layout;
    const btnFull = document.getElementById('layout-btn-full_ai');
    const btnCombo = document.getElementById('layout-btn-combo_cards');
    const chkHeadline = document.getElementById('studio_toggle_headline');
    const chkCard = document.getElementById('studio_toggle_product_card');

    if (btnFull && btnCombo) {
        if (layout === 'full_ai') {
            btnFull.className = 'py-1.5 px-2 rounded-lg font-bold text-xs transition-all flex items-center justify-center gap-1.5 bg-white text-emerald-800 shadow-xs cursor-pointer';
            btnCombo.className = 'py-1.5 px-2 rounded-lg font-bold text-xs transition-all flex items-center justify-center gap-1.5 text-secondary-600 hover:text-secondary-900 cursor-pointer';
            if (chkHeadline) chkHeadline.checked = false; // By default no headline on full AI photo
            if (chkCard) chkCard.checked = false; // By default no product card on full AI photo
        } else {
            btnCombo.className = 'py-1.5 px-2 rounded-lg font-bold text-xs transition-all flex items-center justify-center gap-1.5 bg-white text-emerald-800 shadow-xs cursor-pointer';
            btnFull.className = 'py-1.5 px-2 rounded-lg font-bold text-xs transition-all flex items-center justify-center gap-1.5 text-secondary-600 hover:text-secondary-900 cursor-pointer';
            if (chkHeadline) chkHeadline.checked = true; // Show headline on combo flyer
            if (chkCard) chkCard.checked = true; // Show product cards on combo flyer
        }
    }
    drawBannerCanvas();
}

/**
 * Intelligent Bengali & English text wrapping with auto-scaling font (100% prevents text cutting)
 */
function drawWrappedText(ctx, text, x, y, maxWidth, lineHeight, maxLines = 2, align = 'left', baseFontSize = 24) {
    if (!text) return 0;
    ctx.save();
    ctx.textAlign = align;
    ctx.textBaseline = 'top';

    // Auto-scale font size if text is long to prevent cutting or overflow
    let fontSize = baseFontSize;
    if (text.length > 22 && baseFontSize >= 24) fontSize = Math.max(18, baseFontSize - 4);
    if (text.length > 36 && fontSize >= 20) fontSize = Math.max(16, fontSize - 3);
    ctx.font = `bold ${fontSize}px "Hind Siliguri", sans-serif`;

    const words = text.trim().split(/\s+/);
    let lines = [];
    let currentLine = '';

    for (let i = 0; i < words.length; i++) {
        const word = words[i];
        const testLine = currentLine ? (currentLine + ' ' + word) : word;
        const testWidth = ctx.measureText(testLine).width;

        if (testWidth <= maxWidth) {
            currentLine = testLine;
        } else {
            if (currentLine) {
                lines.push(currentLine);
            }
            if (lines.length >= maxLines - 1) {
                const remaining = words.slice(i).join(' ');
                lines.push(remaining);
                currentLine = '';
                break;
            } else {
                currentLine = word;
            }
        }
    }
    if (currentLine && lines.length < maxLines) {
        lines.push(currentLine);
    }

    lines = lines.slice(0, maxLines);

    lines.forEach((line, idx) => {
        let drawStr = line;
        if (ctx.measureText(drawStr).width > maxWidth) {
            while (ctx.measureText(drawStr + '..').width > maxWidth && drawStr.length > 3) {
                drawStr = drawStr.slice(0, -1);
            }
            drawStr += '..';
        }
        ctx.fillText(drawStr, x, y + (idx * lineHeight));
    });

    ctx.restore();
    return lines.length;
}

// -------------------------------------------------------------------------
// Core 1080x1080 HTML5 Canvas Rendering Engine (Meena Bazar Agency Grade)
// -------------------------------------------------------------------------
function drawBannerCanvas() {
    const canvas = document.getElementById('bannerCanvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const width = 1080;
    const height = 1080;
    const theme = THEMES[currentStudioTheme] || THEMES.emerald;

    const showHeadline = document.getElementById('studio_toggle_headline')?.checked || false;
    const showProductCard = document.getElementById('studio_toggle_product_card')?.checked || false;
    const showBrand = document.getElementById('studio_toggle_brand_watermark')?.checked ?? true;

    const headline = document.getElementById('studio_headline')?.value?.trim() || '';
    const badgeText = document.getElementById('studio_badge_text')?.value?.trim() || '';
    const siteName = document.getElementById('studio_site_name')?.value || 'সদাই দরকার';
    const phone = document.getElementById('studio_phone')?.value || '01609448066';
    const website = document.getElementById('studio_website')?.value || 'sodaidorkar.com';

    ctx.clearRect(0, 0, width, height);

    // 1. Background Scene (Image Backdrop or Rich Multi-Stop Supermarket Gradient)
    if (studioBackdropImgElement && studioBackdropImgElement.complete && studioBackdropImgElement.naturalWidth > 0) {
        // Draw photographic backdrop aspect-fill
        const iw = studioBackdropImgElement.naturalWidth;
        const ih = studioBackdropImgElement.naturalHeight;
        const ratio = Math.max(width / iw, height / ih);
        const nw = iw * ratio;
        const nh = ih * ratio;
        const nx = (width - nw) / 2;
        const ny = (height - nh) / 2;
        ctx.drawImage(studioBackdropImgElement, nx, ny, nw, nh);

        // Soft contrast vignette: Full AI mode keeps visual crystal clear; only gentle bottom gradient if brand is shown
        if (currentPosterLayout === 'full_ai') {
            if (showBrand || showHeadline) {
                const vig = ctx.createLinearGradient(0, 0, 0, height);
                vig.addColorStop(0, (showHeadline && headline) ? 'rgba(0, 0, 0, 0.45)' : 'rgba(0, 0, 0, 0)');
                vig.addColorStop(0.2, 'rgba(0, 0, 0, 0)');
                vig.addColorStop(0.78, 'rgba(0, 0, 0, 0)');
                vig.addColorStop(1, showBrand ? 'rgba(0, 0, 0, 0.72)' : 'rgba(0, 0, 0, 0)');
                ctx.fillStyle = vig;
                ctx.fillRect(0, 0, width, height);
            }
        } else {
            const vig = ctx.createLinearGradient(0, 0, 0, height);
            vig.addColorStop(0, 'rgba(0, 0, 0, 0.65)');
            vig.addColorStop(0.3, 'rgba(0, 0, 0, 0.35)');
            vig.addColorStop(0.7, 'rgba(0, 0, 0, 0.35)');
            vig.addColorStop(1, 'rgba(0, 0, 0, 0.82)');
            ctx.fillStyle = vig;
            ctx.fillRect(0, 0, width, height);
        }
    } else {
        // Rich Multi-stop Supermarket Gradient
        const bgGrad = ctx.createLinearGradient(0, 0, width, height);
        bgGrad.addColorStop(0, theme.bgStart);
        bgGrad.addColorStop(0.5, theme.bgMid);
        bgGrad.addColorStop(1, theme.bgEnd);
        ctx.fillStyle = bgGrad;
        ctx.fillRect(0, 0, width, height);

        // Center Light Spotlight Aura
        ctx.save();
        const aura = ctx.createRadialGradient(width / 2, 500, 60, width / 2, 500, 520);
        aura.addColorStop(0, theme.glowColor);
        aura.addColorStop(0.7, theme.glowColor.replace(/[\d\.]+\)$/, '0.06)'));
        aura.addColorStop(1, 'rgba(0, 0, 0, 0)');
        ctx.fillStyle = aura;
        ctx.fillRect(0, 0, width, height);
        ctx.restore();

        // Subtle Modern Dotted Concentric Rings
        ctx.save();
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.08)';
        ctx.lineWidth = 2.5;
        ctx.setLineDash([8, 14]);
        ctx.beginPath(); ctx.arc(width / 2, 500, 420, 0, Math.PI * 2); ctx.stroke();
        ctx.beginPath(); ctx.arc(width / 2, 500, 470, 0, Math.PI * 2); ctx.stroke();
        ctx.restore();
    }

    // 2. Top Header: Store Logo Capsule (Left) & Corner Badge (Right)
    if (showBrand) {
        // Brand Logo Capsule (Left)
        ctx.save();
        ctx.fillStyle = 'rgba(0, 0, 0, 0.55)';
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.35)';
        ctx.lineWidth = 2;
        ctx.shadowColor = 'rgba(0,0,0,0.4)';
        ctx.shadowBlur = 12;
        roundRect(ctx, 50, 45, 370, 68, 34, true, true);
        ctx.restore();

        // Shopping Bag Icon
        ctx.save();
        ctx.fillStyle = theme.accent;
        ctx.beginPath(); ctx.arc(88, 79, 22, 0, Math.PI * 2); ctx.fill();
        ctx.font = '24px sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText('🛍️', 88, 80);
        ctx.restore();

        // Store Name & Verified Tick
        ctx.save();
        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 30px "Hind Siliguri", sans-serif';
        ctx.textAlign = 'left';
        ctx.textBaseline = 'middle';
        ctx.fillText(siteName, 122, 79);
        const snW = ctx.measureText(siteName).width;

        ctx.fillStyle = '#10b981';
        ctx.beginPath(); ctx.arc(122 + snW + 16, 79, 11, 0, Math.PI * 2); ctx.fill();
        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 13px sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText('✓', 122 + snW + 16, 80);
        ctx.restore();
    }

    // Top-Right Event Badge (Meena Bazar Style) - ONLY if Headline toggle is checked and badgeText exists
    if (showHeadline && badgeText) {
        ctx.save();
        const badgeGrad = ctx.createLinearGradient(width - 450, 45, width - 50, 113);
        badgeGrad.addColorStop(0, theme.badgeBgStart);
        badgeGrad.addColorStop(1, theme.badgeBgEnd);
        ctx.fillStyle = badgeGrad;
        ctx.shadowColor = 'rgba(0,0,0,0.35)';
        ctx.shadowBlur = 14;
        roundRect(ctx, width - 420, 45, 370, 68, 34, true, false);
        ctx.restore();

        ctx.save();
        ctx.fillStyle = theme.badgeText;
        ctx.font = 'bold 24px "Hind Siliguri", sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(badgeText, width - 235, 79);
        ctx.restore();
    }

    // 3. 3D Promotional Headline Ribbon (Y: 135 to 225) - ONLY if Headline toggle is checked and headline exists!
    if (showHeadline && headline) {
        ctx.save();
        const ribbonGrad = ctx.createLinearGradient(50, 135, width - 50, 225);
        ribbonGrad.addColorStop(0, '#ffffff');
        ribbonGrad.addColorStop(1, '#f1f5f9');
        ctx.fillStyle = ribbonGrad;
        ctx.shadowColor = 'rgba(0, 0, 0, 0.45)';
        ctx.shadowBlur = 24;
        ctx.shadowOffsetY = 6;
        roundRect(ctx, 50, 135, width - 100, 90, 26, true, false);
        ctx.restore();

        // Inner gold/accent ribbon border
        ctx.save();
        ctx.strokeStyle = theme.accent;
        ctx.lineWidth = 3;
        roundRect(ctx, 54, 139, width - 108, 82, 22, false, true);
        ctx.restore();

        // Headline Text
        ctx.save();
        ctx.fillStyle = '#0f172a';
        let hlFontSize = 44;
        if (headline.length > 28) hlFontSize = 36;
        if (headline.length > 38) hlFontSize = 30;
        ctx.font = 'bold ' + hlFontSize + 'px "Hind Siliguri", sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(headline, width / 2, 180);
        ctx.restore();
    }

    // 4. Center Stage (Based on Layout Mode: Full AI Visual vs Combo Cards)
    const selectedProds = ALL_PRODUCTS.filter(p => selectedProductIds.includes(p.id));

    if (currentPosterLayout === 'full_ai') {
        renderFullAiPosterStage(ctx, selectedProds, currentCampaignMode, theme, width, height, showProductCard);
    } else {
        if (selectedProds.length === 1) {
            renderSingleProductHero(ctx, selectedProds[0], theme, width, height);
        } else if (selectedProds.length === 2) {
            renderDualProductsStage(ctx, selectedProds, theme, width, height);
        } else {
            renderMultiProductsGrid(ctx, selectedProds, theme, width, height);
        }
    }

    // 5. Automated Brand Capsules (Hotline at Bottom-Left & Website at Bottom-Right)
    if (showBrand) {
        // Hotline Pill (Bottom-Left)
        ctx.save();
        ctx.fillStyle = theme.pillBg;
        ctx.shadowColor = 'rgba(0, 0, 0, 0.5)';
        ctx.shadowBlur = 16;
        ctx.shadowOffsetY = 4;
        roundRect(ctx, 50, 920, 380, 72, 36, true, false);
        ctx.restore();

        ctx.save();
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.4)';
        ctx.lineWidth = 2;
        roundRect(ctx, 52, 922, 376, 68, 34, false, true);
        ctx.restore();

        ctx.save();
        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 30px "Outfit", sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText('📞 ' + phone, 240, 956);
        ctx.restore();

        // Website Capsule (Bottom-Right)
        ctx.save();
        ctx.fillStyle = theme.pillBg;
        ctx.shadowColor = 'rgba(0, 0, 0, 0.5)';
        ctx.shadowBlur = 16;
        ctx.shadowOffsetY = 4;
        roundRect(ctx, width - 430, 920, 380, 72, 36, true, false);
        ctx.restore();

        ctx.save();
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.4)';
        ctx.lineWidth = 2;
        roundRect(ctx, width - 428, 922, 376, 68, 34, false, true);
        ctx.restore();

        ctx.save();
        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 28px "Outfit", sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(website + ' 🌐', width - 240, 956);
        ctx.restore();

        // 6. Full-Width Bottom Trust Bar
        ctx.save();
        ctx.fillStyle = 'rgba(0, 0, 0, 0.85)';
        ctx.fillRect(0, 1020, width, 60);

        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 22px "Hind Siliguri", sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText('🚚 ৩০-৬০ মিনিটে দ্রুত ডেলিভারি  |  💵 ক্যাশ অন ডেলিভারি  |  ✨ ১০০% খাঁটি পণ্য', width / 2, 1050);
        ctx.restore();
    }
}

// -------------------------------------------------------------------------
// Sub-renderers: Meena Bazar Full AI Stage & Product Deal Cards
// -------------------------------------------------------------------------
function renderFullAiPosterStage(ctx, prods, mode, theme, width, height, showProductCard = false) {
    // If no backdrop/AI image is loaded yet, show inviting guidance on canvas
    if (!studioBackdropImgElement || !studioBackdropImgElement.complete || studioBackdropImgElement.naturalWidth === 0) {
        ctx.save();
        ctx.fillStyle = 'rgba(0, 0, 0, 0.45)';
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.35)';
        ctx.lineWidth = 2;
        roundRect(ctx, 150, 410, width - 300, 260, 28, true, true);

        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 36px "Hind Siliguri", sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText('🎨 সম্পূর্ণ এআই বিজ্ঞাপন ছবি মোড (Meena Bazar Style)', width / 2, 500);

        ctx.fillStyle = '#fde047';
        ctx.font = 'bold 24px "Hind Siliguri", sans-serif';
        ctx.fillText('ডানপাশের "ছবি বানান" বাটনে ক্লিক করুন অথবা প্রিসেট থেকে ছবি বাছুন', width / 2, 560);
        ctx.restore();
    }

    // Floating Meena Bazar Style Product Offer Badges / Capsules at bottom (ONLY if Product Card toggle is checked)
    if (showProductCard && prods && prods.length > 0) {
        if (prods.length === 1) {
            const p = prods[0];
            const cardX = 120;
            const cardY = 745;
            const cardW = width - 240; // 840px
            const cardH = 135;

            // Glassmorphism Floating Pill
            ctx.save();
            ctx.fillStyle = 'rgba(15, 23, 42, 0.84)';
            ctx.strokeStyle = 'rgba(255, 255, 255, 0.45)';
            ctx.lineWidth = 2.5;
            ctx.shadowColor = 'rgba(0, 0, 0, 0.55)';
            ctx.shadowBlur = 24;
            ctx.shadowOffsetY = 6;
            roundRect(ctx, cardX, cardY, cardW, cardH, 28, true, true);
            ctx.restore();

            // Product thumbnail (Left)
            const pImg = studioProductImgElements[p.id];
            const thumbSize = 100;
            const thumbX = cardX + 18;
            const thumbY = cardY + 17;

            ctx.save();
            ctx.fillStyle = '#ffffff';
            roundRect(ctx, thumbX, thumbY, thumbSize, thumbSize, 18, true, false);
            if (pImg && pImg.complete && pImg.naturalWidth > 0) {
                roundRect(ctx, thumbX, thumbY, thumbSize, thumbSize, 18, false, false);
                ctx.clip();
                const ratio = Math.min((thumbSize - 8) / pImg.naturalWidth, (thumbSize - 8) / pImg.naturalHeight);
                const nw = pImg.naturalWidth * ratio;
                const nh = pImg.naturalHeight * ratio;
                ctx.drawImage(pImg, thumbX + (thumbSize - nw) / 2, thumbY + (thumbSize - nh) / 2, nw, nh);
            } else {
                ctx.fillStyle = '#64748b';
                ctx.font = '36px sans-serif';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText('🛍️', thumbX + thumbSize / 2, thumbY + thumbSize / 2);
            }
            ctx.restore();

            // Product Name (Middle) - Wrapped & Never cut!
            const textX = thumbX + thumbSize + 22;
            const textMaxW = 410;
            ctx.save();
            ctx.fillStyle = '#ffffff';
            const linesDrawn = drawWrappedText(ctx, p.name, textX, cardY + 24, textMaxW, 30, 2, 'left', 26);

            // MRP / Savings tag
            if (p.has_discount) {
                const subY = (linesDrawn > 1) ? (cardY + 88) : (cardY + 68);
                ctx.fillStyle = '#cbd5e1';
                ctx.font = 'bold 18px "Hind Siliguri", sans-serif';
                ctx.fillText(`MRP ৳${p.regular_price.toFixed(0)}  •  সাশ্রয় ৳${p.savings.toFixed(0)}`, textX, subY);
            }
            ctx.restore();

            // Price & Discount Badge (Right)
            const rightX = cardX + cardW - 25;
            ctx.save();
            ctx.textAlign = 'right';
            ctx.textBaseline = 'middle';

            if (p.has_discount) {
                // Red Floating Discount Tag
                ctx.fillStyle = '#ef4444';
                roundRect(ctx, rightX - 165, cardY + 16, 165, 36, 18, true, false);
                ctx.fillStyle = '#ffffff';
                ctx.font = 'black 19px "Hind Siliguri", sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText(`⚡ ${p.discount_percent}% ছাড়!`, rightX - 82, cardY + 34);

                // Price
                ctx.textAlign = 'right';
                ctx.fillStyle = '#fbbf24';
                ctx.font = '900 52px "Outfit", sans-serif';
                ctx.fillText(`৳${p.sell_price.toFixed(0)}`, rightX, cardY + 80);

                ctx.fillStyle = '#e2e8f0';
                ctx.font = 'bold 18px "Hind Siliguri", sans-serif';
                ctx.fillText(`প্রতি ${p.unit}`, rightX, cardY + 112);
            } else {
                ctx.fillStyle = '#34d399';
                ctx.font = '900 58px "Outfit", sans-serif';
                ctx.fillText(`৳${p.sell_price.toFixed(0)}`, rightX, cardY + 56);

                ctx.fillStyle = '#e2e8f0';
                ctx.font = 'bold 20px "Hind Siliguri", sans-serif';
                ctx.fillText(`প্রতি ${p.unit}`, rightX, cardY + 98);
            }
            ctx.restore();

        } else {
            // 2 to 4 Products in Sleek Floating Bottom Glass Cards
            const count = Math.min(4, prods.length);
            const cardH = 135;
            const cardY = 745;

            if (count === 2) {
                const cardW = 460;
                [0, 1].forEach(idx => {
                    const p = prods[idx];
                    const cardX = (idx === 0) ? 60 : width - cardW - 60;

                    ctx.save();
                    ctx.fillStyle = 'rgba(15, 23, 42, 0.86)';
                    ctx.strokeStyle = 'rgba(255, 255, 255, 0.4)';
                    ctx.lineWidth = 2;
                    ctx.shadowColor = 'rgba(0, 0, 0, 0.55)';
                    ctx.shadowBlur = 20;
                    roundRect(ctx, cardX, cardY, cardW, cardH, 24, true, true);
                    ctx.restore();

                    // Thumb
                    const pImg = studioProductImgElements[p.id];
                    ctx.save();
                    ctx.fillStyle = '#ffffff';
                    roundRect(ctx, cardX + 15, cardY + 17, 100, 100, 16, true, false);
                    if (pImg && pImg.complete && pImg.naturalWidth > 0) {
                        roundRect(ctx, cardX + 15, cardY + 17, 100, 100, 16, false, false);
                        ctx.clip();
                        const ratio = Math.min(90 / pImg.naturalWidth, 90 / pImg.naturalHeight);
                        const nw = pImg.naturalWidth * ratio;
                        const nh = pImg.naturalHeight * ratio;
                        ctx.drawImage(pImg, cardX + 15 + (100 - nw) / 2, cardY + 17 + (100 - nh) / 2, nw, nh);
                    }
                    ctx.restore();

                    // Text
                    ctx.save();
                    ctx.fillStyle = '#ffffff';
                    drawWrappedText(ctx, p.name, cardX + 128, cardY + 22, cardW - 145, 28, 2, 'left', 22);

                    ctx.fillStyle = '#fbbf24';
                    ctx.font = '900 40px "Outfit", sans-serif';
                    ctx.fillText(`৳${p.sell_price.toFixed(0)}`, cardX + 128, cardY + 84);

                    ctx.fillStyle = '#94a3b8';
                    ctx.font = 'bold 16px "Hind Siliguri", sans-serif';
                    ctx.fillText(`প্রতি ${p.unit} ${p.has_discount ? `(${p.discount_percent}% ছাড়)` : ''}`, cardX + 130 + ctx.measureText(`৳${p.sell_price.toFixed(0)} `).width + 20, cardY + 98);
                    ctx.restore();
                });
            } else {
                // 3 or 4 products: Compact horizontal floating cards
                const cardW = Math.floor((width - 120 - ((count - 1) * 16)) / count);
                prods.slice(0, count).forEach((p, idx) => {
                    const cardX = 60 + idx * (cardW + 16);

                    ctx.save();
                    ctx.fillStyle = 'rgba(15, 23, 42, 0.88)';
                    ctx.strokeStyle = 'rgba(255, 255, 255, 0.4)';
                    ctx.lineWidth = 2;
                    ctx.shadowColor = 'rgba(0, 0, 0, 0.5)';
                    ctx.shadowBlur = 16;
                    roundRect(ctx, cardX, cardY, cardW, cardH, 20, true, true);
                    ctx.restore();

                    // Product title
                    ctx.save();
                    ctx.fillStyle = '#ffffff';
                    drawWrappedText(ctx, p.name, cardX + cardW / 2, cardY + 16, cardW - 20, 24, 2, 'center', 19);

                    // Price
                    ctx.fillStyle = '#fbbf24';
                    ctx.font = '900 36px "Outfit", sans-serif';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(`৳${p.sell_price.toFixed(0)}`, cardX + cardW / 2, cardY + 80);

                    ctx.fillStyle = '#e2e8f0';
                    ctx.font = 'bold 15px "Hind Siliguri", sans-serif';
                    ctx.fillText(`প্রতি ${p.unit} ${p.has_discount ? `⚡${p.discount_percent}%` : ''}`, cardX + cardW / 2, cardY + 110);
                    ctx.restore();
                });
            }
        }
    }
}

function renderSingleProductHero(ctx, prod, theme, width, height) {
    const cardX = 160;
    const cardY = 250;
    const cardW = 760;
    const cardH = 450;

    // Card drop shadow
    ctx.save();
    ctx.shadowColor = 'rgba(0, 0, 0, 0.45)';
    ctx.shadowBlur = 35;
    ctx.shadowOffsetY = 16;
    ctx.fillStyle = '#ffffff';
    roundRect(ctx, cardX, cardY, cardW, cardH, 36, true, false);
    ctx.restore();

    // Realistic Pedestal Contact Shadow
    ctx.save();
    const pedGrad = ctx.createRadialGradient(width / 2, cardY + cardH - 50, 30, width / 2, cardY + cardH - 50, 260);
    pedGrad.addColorStop(0, 'rgba(0, 0, 0, 0.18)');
    pedGrad.addColorStop(0.5, 'rgba(0, 0, 0, 0.08)');
    pedGrad.addColorStop(1, 'rgba(0, 0, 0, 0)');
    ctx.fillStyle = pedGrad;
    ctx.beginPath();
    ctx.ellipse(width / 2, cardY + cardH - 50, 260, 30, 0, 0, Math.PI * 2);
    ctx.fill();
    ctx.restore();

    // Product Image
    const pImg = studioProductImgElements[prod.id];
    if (pImg && pImg.complete && pImg.naturalWidth > 0) {
        ctx.save();
        roundRect(ctx, cardX + 25, cardY + 25, cardW - 50, cardH - 60, 26, false, false);
        ctx.clip();
        const ratio = Math.min((cardW - 50) / pImg.naturalWidth, (cardH - 60) / pImg.naturalHeight);
        const nw = pImg.naturalWidth * ratio;
        const nh = pImg.naturalHeight * ratio;
        const nx = cardX + 25 + (cardW - 50 - nw) / 2;
        const ny = cardY + 25 + (cardH - 60 - nh) / 2;
        ctx.drawImage(pImg, nx, ny, nw, nh);
        ctx.restore();
    } else {
        ctx.save();
        ctx.fillStyle = '#f8fafc';
        roundRect(ctx, cardX + 25, cardY + 25, cardW - 50, cardH - 60, 26, true, false);
        ctx.fillStyle = '#64748b';
        ctx.font = 'bold 38px "Hind Siliguri", sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText('📦 ' + prod.name, width / 2, cardY + cardH / 2);
        ctx.restore();
    }

    // Top-Right Discount Ribbon (Meena Bazar Style Ref 4)
    if (prod.has_discount) {
        ctx.save();
        const discGrad = ctx.createLinearGradient(cardX + cardW - 220, cardY + 12, cardX + cardW + 10, cardY + 70);
        discGrad.addColorStop(0, '#ef4444');
        discGrad.addColorStop(1, '#b91c1c');
        ctx.fillStyle = discGrad;
        ctx.shadowColor = 'rgba(239, 68, 68, 0.6)';
        ctx.shadowBlur = 16;
        roundRect(ctx, cardX + cardW - 225, cardY + 12, 235, 58, 22, true, false);
        ctx.restore();

        ctx.save();
        ctx.fillStyle = '#ffffff';
        ctx.font = 'black 30px "Hind Siliguri", sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(`⚡ ${prod.discount_percent}% ছাড়!`, cardX + cardW - 105, cardY + 41);
        ctx.restore();
    }

    // Product Title (Wrapped & Never cut!)
    ctx.save();
    ctx.fillStyle = '#ffffff';
    ctx.shadowColor = 'rgba(0, 0, 0, 0.8)';
    ctx.shadowBlur = 12;
    drawWrappedText(ctx, prod.name, width / 2, 715, 820, 46, 2, 'center', 42);
    ctx.restore();

    // Price Box (Y: 775 to 885)
    const pbY = 775;
    const pbH = 110;
    const pbW = 820;
    const pbX = (width - pbW) / 2;

    ctx.save();
    ctx.fillStyle = theme.priceBg;
    ctx.strokeStyle = theme.priceAccent;
    ctx.lineWidth = 3;
    ctx.shadowColor = 'rgba(0,0,0,0.5)';
    ctx.shadowBlur = 20;
    roundRect(ctx, pbX, pbY, pbW, pbH, 28, true, true);
    ctx.restore();

    if (prod.has_discount) {
        // Left Column: MRP & Savings Pill
        ctx.save();
        ctx.fillStyle = 'rgba(255, 255, 255, 0.75)';
        ctx.font = 'bold 26px "Outfit", sans-serif';
        ctx.textAlign = 'left';
        ctx.textBaseline = 'middle';
        const mrpStr = `MRP: ৳${prod.regular_price.toFixed(0)}`;
        ctx.fillText(mrpStr, pbX + 40, pbY + 34);

        const mrpW = ctx.measureText(mrpStr).width;
        ctx.strokeStyle = '#ef4444';
        ctx.lineWidth = 3;
        ctx.beginPath(); ctx.moveTo(pbX + 35, pbY + 34); ctx.lineTo(pbX + 40 + mrpW + 5, pbY + 34); ctx.stroke();

        ctx.fillStyle = theme.saveBg;
        roundRect(ctx, pbX + 40, pbY + 58, 220, 38, 19, true, false);

        ctx.fillStyle = theme.saveText;
        ctx.font = 'bold 20px "Hind Siliguri", sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText(`💰 সাশ্রয়: ৳${prod.savings.toFixed(0)}!`, pbX + 40 + 110, pbY + 77);
        ctx.restore();

        // Right Column: Offer Price
        ctx.save();
        ctx.fillStyle = '#ffffff';
        ctx.font = '900 62px "Outfit", sans-serif';
        ctx.textAlign = 'right';
        ctx.textBaseline = 'middle';
        ctx.fillText(`৳${prod.sell_price.toFixed(0)}`, pbX + pbW - 40, pbY + 48);

        ctx.fillStyle = theme.priceAccent;
        ctx.font = 'bold 22px "Hind Siliguri", sans-serif';
        ctx.fillText(`প্রতি ${prod.unit}`, pbX + pbW - 40, pbY + 84);
        ctx.restore();
    } else {
        ctx.save();
        ctx.fillStyle = '#ffffff';
        ctx.font = '900 64px "Outfit", sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(`৳${prod.sell_price.toFixed(0)} / ${prod.unit}`, width / 2, pbY + pbH / 2);
        ctx.restore();
    }
}

function renderDualProductsStage(ctx, prods, theme, width, height) {
    const cardW = 460;
    const cardH = 590;
    const startY = 260;

    prods.slice(0, 2).forEach((prod, idx) => {
        const cardX = (idx === 0) ? 60 : width - cardW - 60;

        ctx.save();
        ctx.fillStyle = '#ffffff';
        ctx.shadowColor = 'rgba(0,0,0,0.45)';
        ctx.shadowBlur = 24;
        roundRect(ctx, cardX, startY, cardW, cardH, 32, true, false);
        ctx.restore();

        const pImg = studioProductImgElements[prod.id];
        if (pImg && pImg.complete && pImg.naturalWidth > 0) {
            ctx.save();
            roundRect(ctx, cardX + 20, startY + 20, cardW - 40, 330, 22, false, false);
            ctx.clip();
            const ratio = Math.min((cardW - 40) / pImg.naturalWidth, 330 / pImg.naturalHeight);
            const nw = pImg.naturalWidth * ratio;
            const nh = pImg.naturalHeight * ratio;
            ctx.drawImage(pImg, cardX + 20 + (cardW - 40 - nw) / 2, startY + 20 + (330 - nh) / 2, nw, nh);
            ctx.restore();
        }

        // Product Name (Wrapped & Never cut!)
        ctx.save();
        ctx.fillStyle = '#0f172a';
        drawWrappedText(ctx, prod.name, cardX + cardW / 2, startY + 365, cardW - 30, 32, 2, 'center', 26);
        ctx.restore();

        // Price Pill inside card
        ctx.save();
        ctx.fillStyle = theme.pillBg;
        roundRect(ctx, cardX + 30, startY + 420, cardW - 60, 130, 24, true, false);

        ctx.fillStyle = '#ffffff';
        ctx.font = '900 52px "Outfit", sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(`৳${prod.sell_price.toFixed(0)}`, cardX + cardW / 2, startY + 465);

        ctx.fillStyle = '#fef08a';
        ctx.font = 'bold 22px "Hind Siliguri", sans-serif';
        ctx.fillText(`প্রতি ${prod.unit} ${prod.has_discount ? `(MRP: ৳${prod.regular_price.toFixed(0)})` : ''}`, cardX + cardW / 2, startY + 515);
        ctx.restore();
    });
}

function renderMultiProductsGrid(ctx, prods, theme, width, height) {
    const boxW = 460;
    const boxH = 280;
    const positions = [
        { x: 60, y: 255 },
        { x: width - 520, y: 255 },
        { x: 60, y: 565 },
        { x: width - 520, y: 565 }
    ];

    prods.slice(0, 4).forEach((prod, i) => {
        const pos = positions[i];
        ctx.save();
        ctx.fillStyle = 'rgba(255, 255, 255, 0.96)';
        ctx.strokeStyle = 'rgba(226, 232, 240, 0.8)';
        ctx.lineWidth = 2;
        ctx.shadowColor = 'rgba(0,0,0,0.35)';
        ctx.shadowBlur = 18;
        roundRect(ctx, pos.x, pos.y, boxW, boxH, 24, true, true);
        ctx.restore();

        // Product thumbnail (140px wide, leaves 276px for text column)
        const thumbW = 140;
        const thumbH = 244;
        const thumbX = pos.x + 14;
        const thumbY = pos.y + 18;

        ctx.save();
        ctx.fillStyle = '#f8fafc';
        roundRect(ctx, thumbX, thumbY, thumbW, thumbH, 16, true, false);
        ctx.restore();

        const pImg = studioProductImgElements[prod.id];
        if (pImg && pImg.complete && pImg.naturalWidth > 0) {
            ctx.save();
            roundRect(ctx, thumbX, thumbY, thumbW, thumbH, 16, false, false);
            ctx.clip();
            const ratio = Math.min((thumbW - 8) / pImg.naturalWidth, (thumbH - 8) / pImg.naturalHeight);
            const nw = pImg.naturalWidth * ratio;
            const nh = pImg.naturalHeight * ratio;
            ctx.drawImage(pImg, thumbX + (thumbW - nw) / 2, thumbY + (thumbH - nh) / 2, nw, nh);
            ctx.restore();
        } else {
            ctx.save();
            ctx.fillStyle = '#94a3b8';
            ctx.font = '36px sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText('🛍️', thumbX + thumbW / 2, thumbY + thumbH / 2);
            ctx.restore();
        }

        // Details on Right (X: pos.x + 168, width: 276px)
        const textX = pos.x + 168;
        const textMaxW = 276;

        // 1. Full Product Name with wrapping (NEVER truncate!)
        ctx.save();
        ctx.fillStyle = '#0f172a';
        const linesDrawn = drawWrappedText(ctx, prod.name, textX, pos.y + 24, textMaxW, 28, 2, 'left', 23);
        ctx.restore();

        // 2. Offer Price
        const priceY = (linesDrawn > 1) ? (pos.y + 88) : (pos.y + 76);
        ctx.save();
        ctx.fillStyle = '#059669';
        ctx.font = '900 46px "Outfit", sans-serif';
        ctx.textAlign = 'left';
        ctx.textBaseline = 'top';
        ctx.fillText(`৳${prod.sell_price.toFixed(0)}`, textX, priceY);

        // 3. Unit
        ctx.fillStyle = '#64748b';
        ctx.font = 'bold 18px "Hind Siliguri", sans-serif';
        ctx.fillText(`প্রতি ${prod.unit}`, textX, priceY + 54);

        // 4. MRP & Discount
        if (prod.has_discount) {
            ctx.fillStyle = '#ef4444';
            ctx.font = 'bold 17px "Hind Siliguri", sans-serif';
            ctx.fillText(`MRP ৳${prod.regular_price.toFixed(0)}  •  ${prod.discount_percent}% ছাড়`, textX, priceY + 86);
        }
        ctx.restore();
    });
}

function renderCreativeSceneStage(ctx, mode, theme, width, height) {
    // Elegant central card for Observance / Meme / Lifestyle
    ctx.save();
    ctx.fillStyle = 'rgba(0, 0, 0, 0.45)';
    ctx.strokeStyle = 'rgba(255, 255, 255, 0.3)';
    ctx.lineWidth = 3;
    ctx.shadowColor = 'rgba(0,0,0,0.5)';
    ctx.shadowBlur = 24;
    roundRect(ctx, 100, 360, width - 200, 480, 36, true, true);
    ctx.restore();

    ctx.save();
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';

    if (mode === 'observance') {
        ctx.font = '80px sans-serif';
        ctx.fillText('💚', width / 2, 470);

        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 46px "Hind Siliguri", sans-serif';
        ctx.fillText('সদাই দরকার এর সতেজ বার্তা', width / 2, 580);

        ctx.fillStyle = '#fef08a';
        ctx.font = 'bold 32px "Hind Siliguri", sans-serif';
        ctx.fillText('✨ ভালো খাবার, সুস্থ পরিবার, সুন্দর জীবন ✨', width / 2, 650);

        ctx.fillStyle = 'rgba(255, 255, 255, 0.85)';
        ctx.font = 'bold 26px "Hind Siliguri", sans-serif';
        ctx.fillText('রাসায়নিকমুক্ত তাজা শাকসবজি ও খাঁটি খাদ্যদ্রব্যের বিশ্বস্ত ঠিকানা', width / 2, 720);
    } else if (mode === 'meme') {
        ctx.font = '80px sans-serif';
        ctx.fillText('😂', width / 2, 470);

        ctx.fillStyle = '#fef08a';
        ctx.font = 'black 48px "Hind Siliguri", sans-serif';
        ctx.fillText('বাজারের নো টেনশন!', width / 2, 580);

        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 34px "Hind Siliguri", sans-serif';
        ctx.fillText('সদাই দরকারে এক ক্লিকেই পৌঁছে যাবে তাজা বাজার!', width / 2, 660);
    } else {
        ctx.font = '80px sans-serif';
        ctx.fillText('🍳', width / 2, 470);

        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 48px "Hind Siliguri", sans-serif';
        ctx.fillText('সুস্বাদু রান্নায় খাঁটি উপাদানের নিশ্চয়তা', width / 2, 580);

        ctx.fillStyle = '#fef08a';
        ctx.font = 'bold 32px "Hind Siliguri", sans-serif';
        ctx.fillText('খাঁটি ঘি, সরিষার তেল ও সেরা মানের মসলা', width / 2, 660);
    }
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

// -------------------------------------------------------------------------
// Direct Photorealistic AI Poster Image Generator (FLUX.1 / Imagen 3)
// -------------------------------------------------------------------------
async function generateAiPosterImage(customPrompt = null) {
    const promptInput = document.getElementById('ai_image_prompt_custom');
    const statusEl = document.getElementById('ai-image-gen-status');
    const prompt = customPrompt || (promptInput ? promptInput.value.trim() : '');

    if (!prompt) {
        if (statusEl) statusEl.innerHTML = '<span class="text-amber-600 font-bold">⚠️ অনুগ্রহ করে ছবির একটি বিবরণ লিখুন বা উপরের বাটনে ক্লিক করুন!</span>';
        return;
    }

    if (promptInput && !customPrompt) promptInput.value = prompt;

    if (statusEl) statusEl.innerHTML = '<span class="text-purple-600 animate-pulse">🎨 AI দিয়ে সম্পূর্ণ ছবি জেনারেট হচ্ছে (FLUX.1 / Imagen 3)...</span>';
    const overlay = document.getElementById('canvas-loading-overlay');
    if (overlay) overlay.classList.remove('hidden');
    const loadText = document.getElementById('canvas-loading-text');
    if (loadText) loadText.innerText = '🎨 হাই-কোয়ালিটি সম্পূর্ণ এআই ছবি তৈরি হচ্ছে...';

    try {
        const csrfToken = document.querySelector('input[name="csrf_token"]')?.value || '<?= \Core\CSRF::token() ?>';
        const formData = new FormData();
        formData.append('csrf_token', csrfToken);
        formData.append('prompt', prompt);
        formData.append('aspect_ratio', '1:1');

        const res = await fetch('<?= $base ?>/admin/marketing/api/generate-ai-image', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        });

        const data = await res.json();
        if (data.success && (data.image_url || data.base64)) {
            const aiImg = new Image();
            aiImg.onload = () => {
                studioBackdropImgElement = aiImg;
                setPosterLayout('full_ai'); // Automatically switch to full visual poster mode!

                // Uncheck headline and product card overlays so image is 100% unobstructed!
                const chkHeadline = document.getElementById('studio_toggle_headline');
                const chkCard = document.getElementById('studio_toggle_product_card');
                if (chkHeadline) chkHeadline.checked = false;
                if (chkCard) chkCard.checked = false;

                // Clear leftover product selections so no Harpic or previous deal card lingers
                selectedProductIds = [];
                renderStudioProductChecklist();

                // Clear previous badge text and set prompt into input field without forcing on canvas
                if (document.getElementById('studio_badge_text')) {
                    document.getElementById('studio_badge_text').value = '';
                }
                if (document.getElementById('studio_headline')) {
                    document.getElementById('studio_headline').value = prompt;
                }

                if (overlay) overlay.classList.add('hidden');
                drawBannerCanvas();
                if (statusEl) statusEl.innerHTML = '<span class="text-emerald-700 font-bold">✅ সম্পূর্ণ এআই ছবি সফলভাবে তৈরি ও ক্যানভাসে যুক্ত হয়েছে!</span>';
            };
            aiImg.onerror = () => {
                if (overlay) overlay.classList.add('hidden');
                if (statusEl) statusEl.innerHTML = '<span class="text-red-600 font-bold">❌ ছবি ক্যানভাসে লোড করতে সমস্যা হয়েছে।</span>';
            };
            aiImg.src = data.base64 || data.image_url;
        } else {
            if (overlay) overlay.classList.add('hidden');
            if (statusEl) statusEl.innerHTML = `<span class="text-red-600 font-bold">❌ ${data.message || 'ছবি তৈরি ব্যর্থ'}</span>`;
        }
    } catch (err) {
        if (overlay) overlay.classList.add('hidden');
        if (statusEl) statusEl.innerHTML = `<span class="text-red-600 font-bold">❌ এরর: ${err.message}</span>`;
    }
}

// -------------------------------------------------------------------------
// Gemini AI Creative Assistant Integration
// -------------------------------------------------------------------------
async function generateAiCampaignConcept() {
    const promptInput = document.getElementById('ai_campaign_prompt');
    const statusEl = document.getElementById('ai-campaign-status');
    const generateImage = document.getElementById('ai-generate-image-toggle')?.checked || false;

    const userPrompt = promptInput.value.trim();
    statusEl.innerHTML = '<span class="text-purple-600 animate-pulse">🤖 Gemini AI কন্টেন্ট তৈরি করছে...</span>';

    // Show loading on canvas if image generation requested
    if (generateImage) {
        document.getElementById('canvas-loading-overlay').classList.remove('hidden');
        document.getElementById('canvas-loading-text').innerText = '🎨 সম্পূর্ণ এআই ছবি জেনারেট হচ্ছে...';
    }

    try {
        const csrfToken = document.querySelector('input[name="csrf_token"]')?.value || '<?= \Core\CSRF::token() ?>';
        const formData = new FormData();
        formData.append('csrf_token', csrfToken);
        formData.append('type', currentCampaignMode);
        formData.append('user_prompt', userPrompt);
        formData.append('event_name', document.getElementById('studio_badge_text')?.value || '');
        if (generateImage) formData.append('generate_image', '1');

        const res = await fetch('<?= $base ?>/admin/marketing/api/generate-ai-campaign', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        });

        const data = await res.json();
        if (data.success) {
            if (data.headline) document.getElementById('studio_headline').value = data.headline;
            if (data.badge_text) document.getElementById('studio_badge_text').value = data.badge_text;
            if (data.caption) document.getElementById('studio_fb_caption').value = data.caption;
            if (data.theme && THEMES[data.theme]) setStudioTheme(data.theme);

            // Handle AI generated image from FLUX.1 / Imagen 3
            if (data.image_base64 || data.image_url) {
                const aiImg = new Image();
                aiImg.onload = () => {
                    studioBackdropImgElement = aiImg;
                    setPosterLayout('full_ai'); // Unobstructed full visual!
                    const chkHeadline = document.getElementById('studio_toggle_headline');
                    const chkCard = document.getElementById('studio_toggle_product_card');
                    if (chkHeadline) chkHeadline.checked = false;
                    if (chkCard) chkCard.checked = false;

                    selectedProductIds = [];
                    renderStudioProductChecklist();

                    document.getElementById('canvas-loading-overlay').classList.add('hidden');
                    drawBannerCanvas();
                };
                aiImg.src = data.image_base64 || data.image_url;
            } else {
                document.getElementById('canvas-loading-overlay').classList.add('hidden');
                drawBannerCanvas();
            }

            statusEl.innerHTML = '<span class="text-emerald-700 font-bold">✅ Gemini AI কন্টেন্ট সফলভাবে তৈরি হয়েছে!</span>';
        } else {
            document.getElementById('canvas-loading-overlay').classList.add('hidden');
            statusEl.innerHTML = `<span class="text-red-600 font-bold">❌ ${data.message}</span>`;
        }
    } catch (err) {
        document.getElementById('canvas-loading-overlay').classList.add('hidden');
        statusEl.innerHTML = `<span class="text-red-600 font-bold">❌ এরর: ${err.message}</span>`;
    }
}

// -------------------------------------------------------------------------
// Save & Instant Facebook Publishing Integration
// -------------------------------------------------------------------------
async function saveBannerToDraftOrPost(isPublish = false) {
    const canvas = document.getElementById('bannerCanvas');
    const base64 = canvas.toDataURL('image/png');
    const postId = document.getElementById('studio_post_id').value;
    const statusEl = document.getElementById('banner-save-status');
    const caption = document.getElementById('studio_fb_caption')?.value || '';
    const headline = document.getElementById('studio_headline')?.value || '';

    statusEl.innerHTML = '<span class="text-secondary-500 animate-pulse">সার্ভারে ব্যানার সংরক্ষণ হচ্ছে...</span>';

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

        const data = await res.json();
        if (!data.success) {
            statusEl.innerHTML = `<span class="text-red-600 font-bold">❌ ${data.message}</span>`;
            return;
        }

        const bannerImageUrl = data.image_url;

        // If saving from custom post modal: transfer image and caption back
        const customImgPath = document.getElementById('custom_image_path');
        if (customImgPath) {
            customImgPath.value = bannerImageUrl;
            document.getElementById('custom_headline').value = headline;
            document.getElementById('custom_caption').value = caption;
        }

        if (isPublish) {
            // Instant Publish to Facebook
            statusEl.innerHTML = '<span class="text-blue-600 animate-pulse">🚀 ফেসবুকে পাবলিশ হচ্ছে...</span>';
            const postForm = new FormData();
            postForm.append('csrf_token', csrfToken);
            postForm.append('id', postId || '0');
            postForm.append('action', 'publish');
            postForm.append('headline', headline);
            postForm.append('caption', caption);
            postForm.append('image_path', bannerImageUrl);
            postForm.append('product_id', selectedProductIds[0] || '');

            const pubRes = await fetch('<?= $base ?>/admin/marketing/save-post', {
                method: 'POST',
                body: postForm
            });

            statusEl.innerHTML = '<span class="text-emerald-700 font-bold">🎉 অভিনন্দন! পোস্টটি ফেসবুকে প্রকাশিত হয়েছে!</span>';
            setTimeout(() => { window.location.reload(); }, 1500);
        } else {
            statusEl.innerHTML = '<span class="text-emerald-700 font-bold">✅ ব্যানার সফলভাবে সেভ হয়েছে!</span>';
            setTimeout(() => {
                closeBannerStudio();
                statusEl.innerHTML = '';
            }, 1200);
        }
    } catch (err) {
        statusEl.innerHTML = `<span class="text-red-600 font-bold">❌ আপলোড ব্যর্থ: ${err.message}</span>`;
    }
}

function downloadCanvasBanner() {
    const canvas = document.getElementById('bannerCanvas');
    const link = document.createElement('a');
    link.download = `sodai_meenabazar_ad_${Date.now()}.png`;
    link.href = canvas.toDataURL('image/png');
    link.click();
}

// -------------------------------------------------------------------------
// Custom Post Modal Helpers & Integration
// -------------------------------------------------------------------------
function openCustomPostModal() {
    document.getElementById('customPostModal').classList.remove('hidden');
}

function closeCustomPostModal() {
    document.getElementById('customPostModal').classList.add('hidden');
}

function openStudioFromCustomPost() {
    const prodId = document.getElementById('custom_product_id').value;
    closeCustomPostModal();
    if (prodId) {
        selectedProductIds = [parseInt(prodId)];
        loadSelectedProductImages();
        openStudioModalTab('product_offer');
    } else {
        openStudioModalTab('observance');
    }
}

function filterCustomPostSelect(mode) {
    const sel = document.getElementById('custom_product_id');
    if (!sel) return;
    const list = (mode === 'price_drop') ? RECENT_PRICE_CHANGED : ALL_PRODUCTS;
    sel.innerHTML = '<option value="">-- পণ্য ছাড়া সাধারণ পোস্ট অথবা পণ্য বাছুন --</option>' +
        list.map(p => `<option value="${p.id}" data-name="${p.name}" data-sell="${p.sell_price}" data-regular="${p.regular_price}" data-image="${p.image_url}" data-unit="${p.unit}">${p.name} (৳${p.sell_price.toFixed(2)})</option>`).join('');
}

function onCustomProductChange() {
    const sel = document.getElementById('custom_product_id');
    const opt = sel.options[sel.selectedIndex];
    if (opt && opt.value) {
        document.getElementById('custom_headline').value = `🔥 ${opt.dataset.name} - বিশেষ মেগা অফার!`;
        document.getElementById('custom_image_path').value = opt.dataset.image ? opt.dataset.image : '';
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
        const data = JSON.parse(text);

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
