<?php
$title = '৪০৪ - পৃষ্ঠাটি খুঁজে পাওয়া যায়নি | ' . ($siteName ?? 'Sodai Dorkar');
ob_start();
use Core\Lang;
$__ = function($key, $r = []) { return Lang::get($key, $r); };
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
$locale = Lang::locale();
?>

<div class="min-h-[70vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-gray-50 via-white to-emerald-50/30">
    <div class="max-w-2xl w-full text-center space-y-8">
        
        <!-- Animated / Illustrated 404 Graphic -->
        <div class="relative mx-auto w-48 h-48 sm:w-56 sm:h-56 flex items-center justify-center">
            <div class="absolute inset-0 rounded-full bg-emerald-100/60 animate-ping opacity-25"></div>
            <div class="relative w-44 h-44 sm:w-52 sm:h-52 rounded-full bg-gradient-to-tr from-emerald-500 to-teal-400 p-1 shadow-2xl flex items-center justify-center">
                <div class="w-full h-full rounded-full bg-white flex flex-col items-center justify-center p-4">
                    <span class="text-5xl sm:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-600 tracking-tighter">
                        404
                    </span>
                    <span class="text-3xl mt-1">🛒💨</span>
                </div>
            </div>
            <!-- Floating Decorative Badges -->
            <div class="absolute -top-2 -right-2 bg-amber-400 text-amber-950 font-black text-[11px] px-3 py-1 rounded-full shadow-md animate-bounce">
                <?= $locale === 'bn' ? 'খুঁজে পাওয়া যায়নি!' : 'Not Found!' ?>
            </div>
        </div>

        <!-- Headings & Explanatory Text -->
        <div class="space-y-3">
            <h1 class="text-2xl sm:text-4xl font-black text-gray-900 tracking-tight">
                <?= $locale === 'bn' ? 'দুঃখিত! পৃষ্ঠাটি খুঁজে পাওয়া যায়নি' : 'Oops! Page Not Found' ?>
            </h1>
            <p class="text-sm sm:text-base text-gray-600 max-w-md mx-auto leading-relaxed">
                <?= $locale === 'bn' 
                    ? 'আপনি যে ওয়েব ঠিকানাটিতে প্রবেশের চেষ্টা করছেন তা হয়তো পরিবর্তন করা হয়েছে অথবা সাময়িকভাবে অনুপলব্ধ।' 
                    : 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.' ?>
            </p>
        </div>

        <!-- Quick Search Bar inside 404 Page -->
        <div class="max-w-md mx-auto">
            <form action="<?= $base ?>/shop" method="GET" class="relative flex items-center shadow-lg rounded-2xl overflow-hidden border border-emerald-200 bg-white">
                <div class="pl-4 text-emerald-600 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" name="q" 
                       placeholder="<?= $locale === 'bn' ? 'প্রয়োজনীয় পণ্য লিখে সার্চ করুন...' : 'Search for products...' ?>" 
                       class="w-full py-3.5 pl-3 pr-28 text-sm text-gray-800 placeholder-gray-400 focus:outline-none font-medium">
                <button type="submit" class="absolute right-1.5 top-1.5 bottom-1.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors flex items-center gap-1.5">
                    <span><?= $locale === 'bn' ? 'খুঁজুন' : 'Search' ?></span>
                </button>
            </form>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
            <a href="<?= $base ?>/" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-extrabold shadow-md hover:shadow-emerald-500/25 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span><?= $locale === 'bn' ? 'হোমপেজে ফিরে যান' : 'Go to Homepage' ?></span>
            </a>

            <a href="<?= $base ?>/shop" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-white hover:bg-gray-50 text-gray-800 text-sm font-bold border border-gray-200 shadow-2xs hover:border-emerald-300 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <span><?= $locale === 'bn' ? 'সকল পণ্য দেখুন' : 'Browse All Products' ?></span>
            </a>

            <a href="tel:01609448066" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl text-gray-600 hover:text-emerald-700 text-sm font-bold hover:bg-emerald-50 transition-all">
                <span>📞 <?= $locale === 'bn' ? 'সাপোর্টে কথা বলুন' : 'Call Support' ?></span>
            </a>
        </div>

        <!-- Popular Category Shortcuts -->
        <div class="pt-6 border-t border-gray-100 max-w-lg mx-auto">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">
                <?= $locale === 'bn' ? 'জনপ্রিয় কিছু ক্যাটাগরি' : 'Popular Categories' ?>
            </p>
            <div class="flex flex-wrap items-center justify-center gap-2">
                <a href="<?= $base ?>/category?name=শাকসবজি" class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-800 text-xs font-semibold transition-colors">
                    🥬 <?= $locale === 'bn' ? 'তাজা শাকসবজি' : 'Vegetables' ?>
                </a>
                <a href="<?= $base ?>/category?name=চাল-ডাল" class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-800 text-xs font-semibold transition-colors">
                    🌾 <?= $locale === 'bn' ? 'চাল ও ডাল' : 'Rice & Lentils' ?>
                </a>
                <a href="<?= $base ?>/category?name=তেল-মসলা" class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-800 text-xs font-semibold transition-colors">
                    🍳 <?= $locale === 'bn' ? 'তেল ও মশলা' : 'Cooking Oil & Spices' ?>
                </a>
                <a href="<?= $base ?>/category?name=ফলমূল" class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-800 text-xs font-semibold transition-colors">
                    🍎 <?= $locale === 'bn' ? 'তাজা ফলমূল' : 'Fresh Fruits' ?>
                </a>
            </div>
        </div>

    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../shop/layout.php';
?>
