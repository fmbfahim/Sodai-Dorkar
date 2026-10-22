<?php
ob_start();
use Core\Lang;
Lang::init();
$__ = function($key, $r = []) { return Lang::get($key, $r); };
$locale = Lang::locale();
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
$pageTitle = ($locale === 'bn') ? 'সকল ক্যাটাগরি ও বাজার তালিকা' : 'All Categories';

// Visual helper for category
if (!function_exists('getCategoryVisual')) {
function getCategoryVisual($cat, $base = '') {
    if (!empty($cat['image_path'])) {
        $finalUrl = \Models\Category::getImageUrl($cat['image_path'], $base);
        return ['type' => 'image', 'val' => $finalUrl, 'bg' => 'bg-white border border-gray-100'];
    }
    $n = mb_strtolower($cat['name'] ?? '');
    if (strpos($n, 'খাদ্য') !== false || strpos($n, 'মুদি') !== false || strpos($n, 'grocery') !== false) return ['type' => 'emoji', 'val' => '🛒', 'bg' => 'bg-emerald-50 text-emerald-600'];
    if (strpos($n, 'মাছ ও মাংস') !== false) return ['type' => 'image', 'val' => $base . '/uploads/categories/1788452780_Screenshot 2026-09-03 222555.png', 'bg' => 'bg-white border border-gray-100'];
    if (strpos($n, 'দুধ') !== false || strpos($n, 'দুগ্ধ') !== false || strpos($n, 'dairy') !== false) return ['type' => 'image', 'val' => $base . '/uploads/products/bottle-milk-1liter.jpg', 'bg' => 'bg-white border border-gray-100'];
    if (strpos($n, 'ফল') !== false || strpos($n, 'fruit') !== false) return ['type' => 'emoji', 'val' => '🍎', 'bg' => 'bg-rose-50 text-rose-500'];
    if (strpos($n, 'শাক') !== false || strpos($n, 'সবজি') !== false || strpos($n, 'vege') !== false) return ['type' => 'emoji', 'val' => '🥦', 'bg' => 'bg-emerald-50 text-emerald-600'];
    if (strpos($n, 'ডিম') !== false || strpos($n, 'egg') !== false) return ['type' => 'emoji', 'val' => '🥚', 'bg' => 'bg-amber-50 text-amber-600'];
    if (strpos($n, 'চাল') !== false || strpos($n, 'rice') !== false) return ['type' => 'emoji', 'val' => '🌾', 'bg' => 'bg-amber-50 text-amber-700'];
    if (strpos($n, 'ডাল') !== false || strpos($n, 'dal') !== false || strpos($n, 'lentil') !== false) return ['type' => 'emoji', 'val' => '🥣', 'bg' => 'bg-orange-50 text-orange-600'];
    if (strpos($n, 'তেল') !== false || strpos($n, 'oil') !== false) return ['type' => 'emoji', 'val' => '🫒', 'bg' => 'bg-lime-50 text-lime-700'];
    if (strpos($n, 'চা') !== false || strpos($n, 'tea') !== false) return ['type' => 'emoji', 'val' => '🍵', 'bg' => 'bg-emerald-50 text-emerald-600'];
    if (strpos($n, 'মাছ') !== false || strpos($n, 'fish') !== false) return ['type' => 'emoji', 'val' => '🐟', 'bg' => 'bg-cyan-50 text-cyan-600'];
    if (strpos($n, 'মাংস') !== false || strpos($n, 'meat') !== false) return ['type' => 'emoji', 'val' => '🥩', 'bg' => 'bg-rose-50 text-rose-600'];
    if (strpos($n, 'লবন') !== false || strpos($n, 'চিনি') !== false || strpos($n, 'salt') !== false || strpos($n, 'sugar') !== false) return ['type' => 'emoji', 'val' => '🧂', 'bg' => 'bg-blue-50 text-blue-600'];
    if (strpos($n, 'মশলা') !== false || strpos($n, 'spice') !== false) return ['type' => 'emoji', 'val' => '🌶️', 'bg' => 'bg-red-50 text-red-600'];
    if (strpos($n, 'সেমাই') !== false || strpos($n, 'সুজি') !== false || strpos($n, 'মিক্স') !== false) return ['type' => 'emoji', 'val' => '🥣', 'bg' => 'bg-amber-50 text-amber-700'];
    if (strpos($n, 'রান্না') !== false || strpos($n, 'cook') !== false) return ['type' => 'emoji', 'val' => '🍳', 'bg' => 'bg-amber-50 text-amber-600'];
    if (strpos($n, 'আইসক্রিম') !== false || strpos($n, 'ice cream') !== false) return ['type' => 'emoji', 'val' => '🍦', 'bg' => 'bg-pink-50 text-pink-500'];
    if (strpos($n, 'ক্যান্ডি') !== false || strpos($n, 'চকলেট') !== false || strpos($n, 'chocolate') !== false || strpos($n, 'candy') !== false) return ['type' => 'emoji', 'val' => '🍫', 'bg' => 'bg-amber-50 text-amber-800'];
    if (strpos($n, 'জল খাবার') !== false || strpos($n, 'নাশতা') !== false || strpos($n, 'snack') !== false || strpos($n, 'breakfast') !== false) return ['type' => 'emoji', 'val' => '🥪', 'bg' => 'bg-orange-50 text-orange-600'];
    if (strpos($n, 'পানীয়') !== false || strpos($n, 'beverage') !== false || strpos($n, 'juice') !== false || strpos($n, 'drinks') !== false) return ['type' => 'emoji', 'val' => '🧃', 'bg' => 'bg-teal-50 text-teal-600'];
    if (strpos($n, 'বেকিং') !== false || strpos($n, 'baking') !== false || strpos($n, 'cake') !== false) return ['type' => 'emoji', 'val' => '🧁', 'bg' => 'bg-purple-50 text-purple-600'];
    if (strpos($n, 'খেলনা') !== false || strpos($n, 'খেলা') !== false || strpos($n, 'toy') !== false) return ['type' => 'emoji', 'val' => '🧸', 'bg' => 'bg-amber-50 text-amber-600'];
    if (strpos($n, 'গ্যাজেট') !== false || strpos($n, 'ইলেকট্রনিক্স') !== false || strpos($n, 'gadget') !== false) return ['type' => 'emoji', 'val' => '📱', 'bg' => 'bg-indigo-50 text-indigo-600'];
    if (strpos($n, 'ডায়াপার') !== false || strpos($n, 'বেবি') !== false || strpos($n, 'baby') !== false || strpos($n, 'diaper') !== false) return ['type' => 'emoji', 'val' => '👶', 'bg' => 'bg-sky-50 text-sky-600'];
    if (strpos($n, 'প্রসাধনী') !== false || strpos($n, 'বিউটি') !== false || strpos($n, 'beauty') !== false) return ['type' => 'emoji', 'val' => '✨', 'bg' => 'bg-pink-50 text-pink-600'];
    if (strpos($n, 'পরিষ্কার') !== false || strpos($n, 'cleaning') !== false) return ['type' => 'emoji', 'val' => '🧼', 'bg' => 'bg-cyan-50 text-cyan-600'];
    return ['type' => 'image', 'val' => (!empty($base) ? rtrim($base, '/') : '') . '/images/default-category.svg', 'bg' => 'bg-white border border-gray-100'];
}
}

$totalProductsCount = array_sum(array_map(function($c) { return $c['total_product_count'] ?? 0; }, $mainCategories));
?>

<div class="bg-gradient-to-b from-emerald-50/40 via-slate-50/70 to-gray-100/50 min-h-screen py-5 sm:py-8">
    <div class="container mx-auto px-4 max-w-7xl">

        <!-- Modern Breadcrumbs Pill -->
        <nav class="flex items-center gap-2 text-xs font-semibold text-gray-500 mb-5">
            <a href="<?= $base ?>/" class="hover:text-emerald-700 transition-colors flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white/80 border border-gray-200/80 shadow-2xs hover:border-emerald-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span><?= $__('nav_home') ?></span>
            </a>
            <span class="text-gray-300">/</span>
            <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 font-bold border border-emerald-200/70 shadow-2xs">
                <?= $__('all_category') ?>
            </span>
        </nav>

        <!-- Ultra-Premium Hero Banner -->
        <div class="relative overflow-hidden rounded-[32px] bg-gradient-to-br from-emerald-950 via-emerald-800 to-teal-950 p-6 sm:p-10 text-white shadow-xl mb-8 border border-emerald-700/30">
            <!-- Ambient Glowing Spheres -->
            <div class="absolute -right-16 -top-16 w-80 h-80 bg-emerald-400/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute right-1/3 -bottom-20 w-64 h-64 bg-teal-300/15 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute -left-10 bottom-0 w-48 h-48 bg-amber-400/10 rounded-full blur-2xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                <div class="max-w-2xl">
                    <!-- Glass Badge -->
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md text-emerald-200 text-xs font-bold mb-3.5 border border-white/20 shadow-xs">
                        <span class="animate-pulse">✨</span>
                        <span><?= $locale === 'bn' ? 'সদাই দরকার • ক্যাটাগরি ডিরেক্টরি' : 'Sodai Dorkar • Department Directory' ?></span>
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        <span class="text-emerald-100 font-medium"><?= count($mainCategories) ?> <?= $locale === 'bn' ? 'টি মূল বিভাগ' : 'Main Depts' ?></span>
                    </div>

                    <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-tight mb-3 text-white">
                        <?= $locale === 'bn' ? 'সকল ক্যাটাগরি ও পণ্য সম্ভার' : 'Explore All Categories' ?>
                    </h1>

                    <p class="text-emerald-100/90 text-xs sm:text-sm md:text-base leading-relaxed mb-6 font-medium max-w-xl">
                        <?= $locale === 'bn' ? 'আমাদের সেরা মানের তাজা শাকসবজি, মাছ-মাংস, চাল-ডাল ও নিত্যপ্রয়োজনীয় পণ্যের সম্পূর্ণ তালিকা ঘুরে দেখুন এবং সহজেই আপনার পছন্দের পণ্যটি বেছে নিন।' : 'Browse our premium selection of farm-fresh produce, halal meat, fresh seafood, pantry essentials, and daily household groceries.' ?>
                    </p>

                    <!-- Interactive Live Search Bar -->
                    <div class="relative max-w-lg">
                        <div class="relative flex items-center">
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-emerald-600 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" id="cat-search-input" onkeyup="filterCategoryCards()" 
                                   placeholder="<?= $locale === 'bn' ? 'ক্যাটাগরি বা সাবক্যাটাগরি খুঁজুন (যেমন: দুধ, শাকসবজি, মাছ)...' : 'Search category or subcategory...' ?>" 
                                   class="w-full pl-12 pr-12 py-3.5 rounded-2xl bg-white text-gray-900 placeholder-gray-400 text-xs sm:text-sm font-semibold focus:outline-none focus:ring-4 focus:ring-emerald-400/50 shadow-lg border border-white/50 transition-all">
                            <button type="button" id="cat-search-clear" onclick="clearCategorySearch()" 
                                    class="hidden absolute right-3.5 top-1/2 -translate-y-1/2 w-7 h-7 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 items-center justify-center text-xs font-black transition-all">
                                ✕
                            </button>
                        </div>
                        <div id="cat-search-count-badge" class="hidden mt-2 text-[11px] font-bold text-emerald-200 flex items-center gap-1.5 pl-2">
                            <span>🔍</span>
                            <span id="cat-search-count-text"></span>
                        </div>
                    </div>

                    <!-- Trust Highlights / Value Badges -->
                    <div class="flex flex-wrap items-center gap-2.5 mt-5 pt-4 border-t border-white/10">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-white/10 backdrop-blur-sm text-white text-[11px] font-bold border border-white/10">
                            <span>🌿</span> <?= $locale === 'bn' ? '১০০% তাজা ও খাঁটি' : '100% Fresh & Authentic' ?>
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-white/10 backdrop-blur-sm text-white text-[11px] font-bold border border-white/10">
                            <span>⚡</span> <?= $locale === 'bn' ? 'এক্সপ্রেস হোম ডেলিভারি' : 'Express Doorstep Delivery' ?>
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-white/10 backdrop-blur-sm text-white text-[11px] font-bold border border-white/10">
                            <span>💰</span> <?= $locale === 'bn' ? 'সেরা সাশ্রয়ী বাজার দর' : 'Best Market Value' ?>
                        </span>
                    </div>
                </div>

                <!-- Right Side Glass Stats Cards (Visible on Desktop & Tablet) -->
                <div class="hidden sm:grid grid-cols-2 lg:grid-cols-1 gap-3 shrink-0 w-full sm:w-auto lg:w-56">
                    <div class="p-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 text-white flex items-center gap-3.5 shadow-sm">
                        <div class="w-12 h-12 rounded-xl bg-emerald-500/30 border border-emerald-400/40 flex items-center justify-center text-2xl shrink-0">
                            🗂️
                        </div>
                        <div>
                            <div class="text-2xl font-black leading-none"><?= count($mainCategories) ?></div>
                            <div class="text-[11px] font-bold text-emerald-200 mt-0.5"><?= $locale === 'bn' ? 'মূল ক্যাটাগরি' : 'Main Categories' ?></div>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 text-white flex items-center gap-3.5 shadow-sm">
                        <div class="w-12 h-12 rounded-xl bg-teal-500/30 border border-teal-400/40 flex items-center justify-center text-2xl shrink-0">
                            📦
                        </div>
                        <div>
                            <div class="text-2xl font-black leading-none"><?= count($allCategories) ?></div>
                            <div class="text-[11px] font-bold text-emerald-200 mt-0.5"><?= $locale === 'bn' ? 'মোট ডিপার্টমেন্ট' : 'Departments Total' ?></div>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 text-white flex items-center gap-3.5 shadow-sm col-span-2 lg:col-span-1">
                        <div class="w-12 h-12 rounded-xl bg-amber-500/30 border border-amber-400/40 flex items-center justify-center text-2xl shrink-0">
                            🛒
                        </div>
                        <div>
                            <div class="text-2xl font-black leading-none"><?= $totalProductsCount ?>+</div>
                            <div class="text-[11px] font-bold text-emerald-200 mt-0.5"><?= $locale === 'bn' ? 'মোট রেডি পণ্য' : 'Available Items' ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Interactive Quick Jump Pills Bar -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-2.5 px-1">
                <span class="text-xs font-black text-gray-500 uppercase tracking-wider flex items-center gap-1.5">
                    <span>⚡</span>
                    <span><?= $locale === 'bn' ? 'দ্রুত ক্যাটাগরিতে যান:' : 'Quick Navigation:' ?></span>
                </span>
                <span class="text-[11px] text-gray-400 font-bold hidden sm:inline-block">
                    <?= $locale === 'bn' ? 'ক্লিক করে সরাসরি সেকশনে স্ক্রোল করুন' : 'Click to jump to section' ?>
                </span>
            </div>
            
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-2 -mx-4 px-4 sm:mx-0 sm:px-0">
                <button type="button" onclick="scrollToTopCategories()" 
                        class="quick-jump-pill flex-shrink-0 px-4 py-2 rounded-2xl bg-emerald-600 text-white font-extrabold text-xs shadow-xs hover:bg-emerald-700 transition-all flex items-center gap-1.5 border border-emerald-600 active:scale-95">
                    <span>🌟</span>
                    <span><?= $locale === 'bn' ? 'সবগুলো' : 'All' ?></span>
                </button>

                <?php foreach ($mainCategories as $mCat): 
                    $mVis = getCategoryVisual($mCat, $base);
                    $mCount = $mCat['total_product_count'] ?? 0;
                ?>
                <a href="#cat-card-<?= $mCat['id'] ?>" onclick="highlightCard('cat-card-<?= $mCat['id'] ?>')"
                   class="quick-jump-pill flex-shrink-0 px-3.5 py-2 rounded-2xl bg-white hover:bg-emerald-50 text-gray-700 hover:text-emerald-800 border border-gray-200/90 hover:border-emerald-300 text-xs font-bold shadow-2xs hover:shadow-xs transition-all flex items-center gap-2 group whitespace-nowrap active:scale-95">
                    <?php if ($mVis['type'] === 'image'): ?>
                        <img src="<?= htmlspecialchars($mVis['val']) ?>" alt="" class="w-4 h-4 object-contain rounded-md" onerror="this.style.display='none';">
                    <?php else: ?>
                        <span class="text-sm leading-none"><?= $mVis['val'] ?></span>
                    <?php endif; ?>
                    <span><?= htmlspecialchars($mCat['name']) ?></span>
                    <?php if ($mCount > 0): ?>
                        <span class="px-1.5 py-0.2 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black group-hover:bg-emerald-200">
                            <?= $mCount ?>
                        </span>
                    <?php endif; ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Categories Grid (1 col on mobile, 2 on tablet, 3 on desktop for optimal breathing room) -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 sm:gap-6" id="categories-grid-container">
            <?php foreach ($mainCategories as $cat): 
                $vis = getCategoryVisual($cat, $base);
                $hasSub = !empty($childrenMap[$cat['id']]);
                $subIds = $hasSub ? $childrenMap[$cat['id']] : [];
                $prodCount = $cat['total_product_count'] ?? 0;
                $subNamesString = mb_strtolower(implode(' ', array_map(function($sId) use ($catById) { return $catById[$sId]['name'] ?? ''; }, $subIds)));
            ?>
            <div id="cat-card-<?= $cat['id'] ?>" 
                 class="category-catalog-card bg-white rounded-[28px] border border-gray-200/90 hover:border-emerald-400 hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden group relative bg-gradient-to-b from-white via-white to-emerald-50/25"
                 data-category-name="<?= htmlspecialchars(mb_strtolower($cat['name'])) ?>"
                 data-sub-names="<?= htmlspecialchars($subNamesString) ?>">
                
                <!-- Card Header -->
                <div class="p-5 sm:p-6 pb-4 border-b border-gray-100 flex items-start justify-between gap-3.5">
                    <div class="flex items-start gap-4 min-w-0">
                        <a href="<?= $base ?>/category?id=<?= $cat['id'] ?>" 
                           class="w-18 h-18 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-br from-emerald-50/80 via-white to-teal-50 border border-emerald-100/90 flex items-center justify-center flex-shrink-0 shadow-2xs group-hover:scale-105 group-hover:rotate-1 transition-all duration-300 overflow-hidden p-2">
                            <?php if ($vis['type'] === 'image'): ?>
                                <img src="<?= htmlspecialchars($vis['val']) ?>" alt="<?= htmlspecialchars($cat['name']) ?>" class="w-full h-full object-contain" onerror="this.onerror=null; this.src='<?= $base ?>/images/default-category.svg';">
                            <?php else: ?>
                                <span class="text-3xl sm:text-4xl select-none leading-none"><?= $vis['val'] ?></span>
                            <?php endif; ?>
                        </a>

                        <div class="flex-1 min-w-0">
                            <a href="<?= $base ?>/category?id=<?= $cat['id'] ?>" 
                               class="font-black text-gray-900 text-base sm:text-lg hover:text-emerald-700 transition-colors leading-snug line-clamp-2 block">
                                <?= htmlspecialchars($cat['name']) ?>
                            </a>

                            <div class="flex flex-wrap items-center gap-1.5 mt-2">
                                <?php if ($prodCount > 0): ?>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-800 text-[11px] font-bold border border-emerald-200/60 shadow-2xs">
                                        <span class="relative flex h-2 w-2">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                        </span>
                                        <span><?= $prodCount ?> <?= $locale === 'bn' ? 'টি পণ্য পাওয়া যাচ্ছে' : 'Items in Stock' ?></span>
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-gray-100 text-gray-500 text-[11px] font-semibold border border-gray-200/60">
                                        <span>📦 <?= $locale === 'bn' ? '০ টি পণ্য' : '0 Items' ?></span>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Direct Link Arrow Button Top Right -->
                    <a href="<?= $base ?>/category?id=<?= $cat['id'] ?>" 
                       aria-label="<?= htmlspecialchars($cat['name']) ?>"
                       class="w-9 h-9 rounded-xl bg-gray-50 group-hover:bg-emerald-600 text-gray-400 group-hover:text-white transition-all flex items-center justify-center shrink-0 shadow-2xs group-hover:shadow-xs mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                <!-- Subcategories List / Badges -->
                <div class="p-5 sm:p-6 pt-4 flex-grow flex flex-col justify-between space-y-4">
                    <?php if (!empty($subIds)): ?>
                    <div>
                        <div class="text-[11px] font-black text-gray-500 uppercase tracking-wider mb-2.5 flex items-center justify-between">
                            <span class="flex items-center gap-1.5">
                                <span>📂</span>
                                <span><?= $locale === 'bn' ? 'সাব-ক্যাটাগরি সমূহ' : 'Sub-categories' ?></span>
                            </span>
                            <span class="px-2 py-0.2 rounded-full bg-gray-100 text-gray-600 text-[10px] font-bold">
                                <?= count($subIds) ?> <?= $locale === 'bn' ? 'টি' : '' ?>
                            </span>
                        </div>

                        <div class="flex flex-wrap gap-2" id="sub-list-<?= $cat['id'] ?>">
                            <?php 
                            $maxVisible = 8;
                            $countSubs = count($subIds);
                            foreach ($subIds as $idx => $sId): 
                                $sCat = $catById[$sId] ?? null;
                                if (!$sCat) continue;
                                $sCount = $sCat['total_product_count'] ?? 0;
                                $isExtra = ($idx >= $maxVisible);
                            ?>
                            <a href="<?= $base ?>/category?id=<?= $cat['id'] ?>&sub=<?= $sCat['id'] ?>" 
                               class="sub-item-pill inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-gray-50/90 hover:bg-emerald-50 text-gray-700 hover:text-emerald-800 text-xs font-semibold border border-gray-200/70 hover:border-emerald-300 transition-all shadow-2xs group/sub <?= $isExtra ? 'hidden extra-sub-' . $cat['id'] : '' ?>">
                                <span class="truncate max-w-[140px]"><?= htmlspecialchars($sCat['name']) ?></span>
                                <span class="text-[10px] px-1.5 py-0.2 rounded-md font-bold <?= $sCount > 0 ? 'bg-emerald-100 text-emerald-800 group-hover/sub:bg-emerald-200' : 'bg-gray-100 text-gray-400' ?>">
                                    <?= $sCount ?>
                                </span>
                            </a>
                            <?php endforeach; ?>

                            <?php if ($countSubs > $maxVisible): ?>
                            <button type="button" onclick="toggleExtraSubs('<?= $cat['id'] ?>', this)" 
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs font-bold border border-emerald-200 transition-all">
                                <span>+ আরও <?= ($countSubs - $maxVisible) ?>টি দেখুন</span>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="p-3.5 rounded-2xl bg-gray-50/70 border border-gray-100 text-xs text-gray-500 font-medium flex items-center gap-2">
                        <span>✨</span>
                        <span><?= $locale === 'bn' ? 'এই ক্যাটাগরির সকল পণ্য সরাসরি ব্রাউজ করুন।' : 'Explore all available items in this category.' ?></span>
                    </div>
                    <?php endif; ?>

                    <!-- Action Button CTA -->
                    <div class="pt-2">
                        <a href="<?= $base ?>/category?id=<?= $cat['id'] ?>" 
                           class="w-full py-3.5 px-5 rounded-2xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 hover:from-emerald-700 hover:via-teal-700 hover:to-emerald-800 text-white font-extrabold text-xs sm:text-sm flex items-center justify-center gap-2 transition-all shadow-sm hover:shadow-md group/btn active:scale-[0.99]">
                            <span><?= $locale === 'bn' ? htmlspecialchars($cat['name']) . '-এর সব পণ্য দেখুন' : 'Browse ' . htmlspecialchars($cat['name']) ?></span>
                            <span class="group-hover/btn:translate-x-1.5 transition-transform duration-200 text-base font-bold">→</span>
                        </a>
                    </div>
                </div>

            </div>
            <?php endforeach; ?>
        </div>

        <!-- No Results Fallback for Category Search -->
        <div id="no-cats-found" class="hidden text-center py-20 px-4">
            <div class="w-20 h-20 bg-emerald-50 text-emerald-600 rounded-3xl flex items-center justify-center mx-auto mb-4 text-3xl shadow-sm border border-emerald-100 animate-bounce">
                🔍
            </div>
            <h3 class="text-lg font-black text-gray-900"><?= $locale === 'bn' ? 'কোনো ক্যাটাগরি খুঁজে পাওয়া যায়নি' : 'No matching categories found' ?></h3>
            <p class="text-xs sm:text-sm text-gray-500 mt-1.5 max-w-sm mx-auto">
                <?= $locale === 'bn' ? 'আপনার অনুসন্ধানের সাথে মিলে এমন কোনো ক্যাটাগরি বা সাবক্যাটাগরি নেই। অন্য নামে অনুসন্ধান করে দেখুন।' : 'Try searching with different keywords or browse from all categories.' ?>
            </p>
            <button type="button" onclick="clearCategorySearch()" 
                    class="mt-5 px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition-all inline-flex items-center gap-2">
                <span>🔄</span>
                <span><?= $locale === 'bn' ? 'সব ক্যাটাগরি পুনরায় দেখুন' : 'Reset Search' ?></span>
            </button>
        </div>

    </div>
</div>

<style>
/* Highlight pulse for jumping to category */
@keyframes cardPulse {
    0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
    50% { transform: scale(1.02); box-shadow: 0 0 0 10px rgba(16, 185, 129, 0.2); }
    100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}
.card-highlight-pulse {
    animation: cardPulse 1s ease-in-out;
    border-color: #10b981 !important;
}
</style>

<script>
function filterCategoryCards() {
    const input = document.getElementById('cat-search-input');
    const filter = (input ? input.value : '').toLowerCase().trim();
    const cards = document.querySelectorAll('.category-catalog-card');
    const clearBtn = document.getElementById('cat-search-clear');
    const countBadge = document.getElementById('cat-search-count-badge');
    const countText = document.getElementById('cat-search-count-text');
    let visibleCount = 0;

    if (clearBtn) {
        clearBtn.style.display = filter ? 'flex' : 'none';
    }

    cards.forEach(card => {
        const catName = card.getAttribute('data-category-name') || '';
        const subNames = card.getAttribute('data-sub-names') || '';
        if (filter === '' || catName.includes(filter) || subNames.includes(filter)) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    if (countBadge && countText) {
        if (filter) {
            countBadge.classList.remove('hidden');
            countText.textContent = `পাওয়া গেছে ${visibleCount}টি ক্যাটাগরি`;
        } else {
            countBadge.classList.add('hidden');
        }
    }

    const noResults = document.getElementById('no-cats-found');
    if (noResults) {
        noResults.className = (visibleCount === 0) ? 'text-center py-20 px-4 block' : 'hidden';
    }
}

function clearCategorySearch() {
    const input = document.getElementById('cat-search-input');
    if (input) {
        input.value = '';
        filterCategoryCards();
        input.focus();
    }
}

function scrollToTopCategories() {
    clearCategorySearch();
    const container = document.getElementById('categories-grid-container');
    if (container) {
        container.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function highlightCard(cardId) {
    const card = document.getElementById(cardId);
    if (card) {
        card.classList.remove('card-highlight-pulse');
        void card.offsetWidth; // trigger reflow
        card.classList.add('card-highlight-pulse');
    }
}

function toggleExtraSubs(catId, btn) {
    const extras = document.querySelectorAll('.extra-sub-' + catId);
    let isHidden = true;
    extras.forEach(el => {
        if (el.classList.contains('hidden')) {
            el.classList.remove('hidden');
            isHidden = false;
        } else {
            el.classList.add('hidden');
            isHidden = true;
        }
    });
    if (!isHidden) {
        btn.textContent = 'সংক্ষিপ্ত করুন ▲';
    } else {
        btn.textContent = '+ আরও ' + extras.length + 'টি দেখুন';
    }
}
</script>

<?php
$content = ob_get_clean();
require 'layout.php';
?>
