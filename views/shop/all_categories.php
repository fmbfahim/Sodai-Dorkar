<?php
ob_start();
use Core\Lang;
Lang::init();
$__ = function($key, $r = []) { return Lang::get($key, $r); };
$locale = Lang::locale();
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';

// Visual helper for category
if (!function_exists('getCategoryVisual')) {
function getCategoryVisual($cat, $base = '') {
    if (!empty($cat['image_path'])) {
        $img = $cat['image_path'];
        if (strpos($img, 'http') !== 0 && strpos($img, $base) !== 0 && strpos($img, '/') === 0) {
            $img = $base . $img;
        }
        return ['type' => 'image', 'val' => $img, 'bg' => 'bg-white border border-gray-100'];
    }
    $n = mb_strtolower($cat['name'] ?? '');
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
    if (strpos($n, 'হিমায়িত') !== false || strpos($n, 'টিনজাত') !== false || strpos($n, 'frozen') !== false || strpos($n, 'canned') !== false) return ['type' => 'emoji', 'val' => '🥫', 'bg' => 'bg-blue-50 text-blue-600'];
    if (strpos($n, 'ডায়বেটিক') !== false || strpos($n, 'diabetic') !== false) return ['type' => 'emoji', 'val' => '🥗', 'bg' => 'bg-emerald-50 text-emerald-700'];
    if (strpos($n, 'সস') !== false || strpos($n, 'আচার') !== false || strpos($n, 'pickle') !== false || strpos($n, 'sauce') !== false) return ['type' => 'emoji', 'val' => '🫙', 'bg' => 'bg-red-50 text-red-700'];
    return ['type' => 'emoji', 'val' => '🛒', 'bg' => 'bg-emerald-50 text-emerald-600'];
}
}

$totalProductsCount = array_sum(array_map(function($c) { return $c['total_product_count'] ?? 0; }, $mainCategories));
?>

<div class="bg-gray-50/60 min-h-screen py-6 sm:py-8">
    <div class="container mx-auto px-4 max-w-7xl">

        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-2 text-xs font-semibold text-gray-500 mb-4">
            <a href="<?= $base ?>/" class="hover:text-emerald-600 transition-colors flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Home</span>
            </a>
            <span class="text-gray-300">/</span>
            <span class="text-emerald-700 font-bold">All Categories</span>
        </nav>

        <!-- Page Header Banner -->
        <div class="bg-gradient-to-r from-emerald-800 via-emerald-700 to-teal-800 rounded-3xl p-6 sm:p-10 text-white shadow-md mb-8 relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="relative z-10 max-w-2xl">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-emerald-100 text-xs font-bold mb-3 border border-white/20">
                    <span>🛒</span>
                    <span>Department Directory</span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-black tracking-tight leading-tight mb-2">
                    All Categories
                </h1>
                <p class="text-emerald-100/90 text-xs sm:text-sm leading-relaxed mb-5">
                    Browse our full range of fresh groceries, pantry staples, dairy, fish, meat, and everyday essentials.
                </p>

                <!-- Search Input for Categories -->
                <div class="relative max-w-md">
                    <input type="text" id="cat-search-input" onkeyup="filterCategoryCards()" 
                           placeholder="Search category or subcategory..." 
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-white text-gray-800 placeholder-gray-400 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <!-- Stats Badge Top Right -->
            <div class="hidden md:flex absolute top-8 right-8 flex-col items-end gap-1.5 text-right">
                <div class="text-3xl font-black text-white"><?= count($mainCategories) ?></div>
                <div class="text-xs font-bold text-emerald-200 uppercase tracking-wider">Main Categories</div>
                <div class="text-xs text-emerald-100 mt-1"><?= count($allCategories) ?> Departments Total</div>
            </div>
        </div>

        <!-- Quick Jump Pill Bar (Horizontal scroll on mobile) -->
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-2 mb-6 -mx-4 px-4 sm:mx-0 sm:px-0">
            <span class="text-xs font-black text-gray-400 uppercase tracking-wider flex-shrink-0 mr-1">Jump to:</span>
            <?php foreach ($mainCategories as $mCat): ?>
            <a href="#cat-card-<?= $mCat['id'] ?>" 
               class="flex-shrink-0 px-3 py-1.5 rounded-full bg-white hover:bg-emerald-50 text-gray-700 hover:text-emerald-700 border border-gray-200 text-xs font-bold shadow-2xs transition-all whitespace-nowrap">
                <?= htmlspecialchars($mCat['name']) ?>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- Categories Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6" id="categories-grid-container">
            <?php foreach ($mainCategories as $cat): 
                $vis = getCategoryVisual($cat, $base);
                $hasSub = !empty($childrenMap[$cat['id']]);
                $subIds = $hasSub ? $childrenMap[$cat['id']] : [];
                $prodCount = $cat['total_product_count'] ?? 0;
            ?>
            <div id="cat-card-<?= $cat['id'] ?>" 
                 class="category-catalog-card bg-white rounded-3xl border border-gray-200/90 hover:border-emerald-400 hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden group relative"
                 data-category-name="<?= htmlspecialchars(mb_strtolower($cat['name'])) ?>"
                 data-sub-names="<?= htmlspecialchars(mb_strtolower(implode(' ', array_map(function($sId) use ($catById) { return $catById[$sId]['name'] ?? ''; }, $subIds)))) ?>">
                
                <!-- Card Header with Visual & Title -->
                <div class="p-5 pb-4 border-b border-gray-100 flex items-start gap-4">
                    <a href="<?= $base ?>/category?id=<?= $cat['id'] ?>" 
                       class="w-16 h-16 sm:w-18 sm:h-18 rounded-2xl <?= $vis['type'] === 'image' ? 'bg-white border border-gray-100 p-1.5' : $vis['bg'] ?> flex items-center justify-center flex-shrink-0 shadow-2xs group-hover:scale-105 transition-transform duration-300 overflow-hidden">
                        <?php if ($vis['type'] === 'image'): ?>
                            <img src="<?= htmlspecialchars($vis['val']) ?>" alt="<?= htmlspecialchars($cat['name']) ?>" class="w-full h-full object-contain" onerror="this.parentElement.innerHTML='🛒'">
                        <?php else: ?>
                            <span class="text-3xl sm:text-4xl select-none"><?= $vis['val'] ?></span>
                        <?php endif; ?>
                    </a>

                    <div class="flex-1 min-w-0">
                        <a href="<?= $base ?>/category?id=<?= $cat['id'] ?>" 
                           class="font-black text-gray-900 text-base hover:text-emerald-600 transition-colors leading-snug line-clamp-2">
                            <?= htmlspecialchars($cat['name']) ?>
                        </a>
                        <div class="flex items-center gap-1.5 mt-1.5">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-extrabold border border-emerald-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <?= $prodCount ?> <?= $locale === 'bn' ? 'টি পণ্য' : 'Items' ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Subcategories List / Badges -->
                <div class="p-4 sm:p-5 flex-grow flex flex-col justify-between space-y-4">
                    <?php if (!empty($subIds)): ?>
                    <div>
                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-wider mb-2 flex items-center justify-between">
                            <span>Sub-categories (<?= count($subIds) ?>)</span>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            <?php foreach ($subIds as $sId): 
                                $sCat = $catById[$sId] ?? null;
                                if (!$sCat) continue;
                                $sCount = $sCat['total_product_count'] ?? 0;
                            ?>
                            <a href="<?= $base ?>/category?id=<?= $cat['id'] ?>&sub=<?= $sCat['id'] ?>" 
                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-gray-50 hover:bg-emerald-50 text-gray-700 hover:text-emerald-700 text-xs font-semibold border border-gray-100 hover:border-emerald-200 transition-all group/sub">
                                <span class="truncate max-w-[130px]"><?= htmlspecialchars($sCat['name']) ?></span>
                                <span class="text-[10px] text-gray-400 group-hover/sub:text-emerald-600 font-bold"><?= $sCount ?></span>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="text-xs text-gray-400 italic py-2">
                        Explore all available products in this category.
                    </div>
                    <?php endif; ?>

                    <!-- Action Button -->
                    <div class="pt-2">
                        <a href="<?= $base ?>/category?id=<?= $cat['id'] ?>" 
                           class="w-full py-2.5 px-4 rounded-xl bg-gray-50 hover:bg-emerald-600 text-gray-800 hover:text-white border border-gray-200 hover:border-emerald-600 font-bold text-xs flex items-center justify-center gap-1.5 transition-all shadow-2xs group/btn">
                            <span>Browse All in <?= htmlspecialchars($cat['name']) ?></span>
                            <span class="group-hover/btn:translate-x-1 transition-transform">→</span>
                        </a>
                    </div>
                </div>

            </div>
            <?php endforeach; ?>
        </div>

        <!-- No Results Fallback for Category Search -->
        <div id="no-cats-found" class="hidden text-center py-16">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400 text-2xl">
                🔍
            </div>
            <h3 class="text-base font-bold text-gray-800">No matching categories found</h3>
            <p class="text-xs text-gray-500 mt-1">Try searching with a different term or clear the search input.</p>
        </div>

    </div>
</div>

<script>
function filterCategoryCards() {
    const input = document.getElementById('cat-search-input');
    const filter = (input ? input.value : '').toLowerCase().trim();
    const cards = document.querySelectorAll('.category-catalog-card');
    let visibleCount = 0;

    cards.forEach(card => {
        const catName = card.getAttribute('data-category-name') || '';
        const subNames = card.getAttribute('data-sub-names') || '';
        if (catName.includes(filter) || subNames.includes(filter)) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    const noResults = document.getElementById('no-cats-found');
    if (noResults) {
        noResults.className = (visibleCount === 0) ? 'text-center py-16 block' : 'hidden';
    }
}
</script>

<?php
$content = ob_get_clean();
require 'layout.php';
?>
