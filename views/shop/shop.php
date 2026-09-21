<?php
ob_start();
use Core\Lang;
Lang::init();
$__ = function($key, $r = []) { return Lang::get($key, $r); };
$locale = Lang::locale();
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';

// Calculate active filters count
$activeFilterCount = 0;
if (!empty($catId)) $activeFilterCount++;
if ($minPrice !== null || $maxPrice !== null) $activeFilterCount++;
if (!empty($selectedBrand)) $activeFilterCount++;
if (!empty($inStockOnly) && !isset($_GET['in_stock'])) { /* default */ } elseif (!empty($inStockOnly)) $activeFilterCount++;
if (!empty($isDeals)) $activeFilterCount++;
if (!empty($search)) $activeFilterCount++;
?>

<div class="bg-gray-50/60 min-h-screen py-5 sm:py-7">
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
            <span class="text-emerald-700 font-bold">Shop</span>
            <?php if (!empty($currentCategory)): ?>
                <span class="text-gray-300">/</span>
                <span class="text-gray-700 font-medium"><?= htmlspecialchars($currentCategory['name']) ?></span>
            <?php endif; ?>
        </nav>

        <!-- Shop Header Banner -->
        <div class="bg-gradient-to-r from-[#072414] via-[#0d3b20] to-[#14532d] rounded-3xl p-6 sm:p-8 text-white shadow-md mb-6 relative overflow-hidden flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="relative z-10">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/20 backdrop-blur-md text-emerald-200 text-xs font-bold mb-2 border border-emerald-400/30">
                    <span>🛒</span>
                    <span>FreshMart Online Store</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight">
                    <?= !empty($currentCategory) ? htmlspecialchars($currentCategory['name']) : 'Shop All Products' ?>
                </h1>
                <p class="text-emerald-100/80 text-xs sm:text-sm mt-1 max-w-xl">
                    <?= !empty($currentCategory) ? 'Explore our handpicked selection of ' . htmlspecialchars($currentCategory['name']) : 'Fresh groceries, farm produce, pantry staples, and everyday essentials.' ?>
                </p>
            </div>

            <!-- Total Products Found Pill -->
            <div class="relative z-10 flex-shrink-0">
                <span class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md text-white border border-white/20 text-xs sm:text-sm font-black px-4 py-2 rounded-2xl shadow-inner">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span><?= count($products) ?> Products Available</span>
                </span>
            </div>
        </div>

        <!-- Main Layout: Sidebar + Product Grid -->
        <div class="flex flex-col lg:flex-row gap-6">

            <!-- DESKTOP FILTER SIDEBAR -->
            <aside class="hidden lg:block w-72 flex-shrink-0">
                <div class="bg-white rounded-3xl border border-gray-200/90 p-5 shadow-2xs sticky top-20 space-y-6">
                    
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <span class="font-black text-gray-900 text-sm uppercase tracking-wider flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filters
                        </span>
                        <?php if ($activeFilterCount > 0): ?>
                        <a href="<?= $base ?>/shop" class="text-xs font-bold text-rose-500 hover:text-rose-700 transition-colors">
                            Reset All
                        </a>
                        <?php endif; ?>
                    </div>

                    <form action="<?= $base ?>/shop" method="GET" id="shop-filter-form" class="space-y-5">
                        <?php if (!empty($search)): ?>
                            <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                        <?php endif; ?>
                        <?php if (!empty($sort)): ?>
                            <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
                        <?php endif; ?>

                        <!-- Department / Category Selection -->
                        <div>
                            <label class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-2">Category</label>
                            <div class="space-y-1 max-h-56 overflow-y-auto custom-scrollbar pr-1">
                                <a href="<?= $base ?>/shop<?= !empty($sort) ? '?sort=' . $sort : '' ?>" 
                                   class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold transition-colors <?= empty($catId) ? 'bg-emerald-600 text-white' : 'text-gray-700 hover:bg-gray-50' ?>">
                                    <span>All Departments</span>
                                    <span class="text-[11px] <?= empty($catId) ? 'text-emerald-100' : 'text-gray-400' ?>"><?= array_sum(array_map(function($c){ return $c['total_product_count'] ?? 0; }, $mainCategories)) ?></span>
                                </a>
                                <?php foreach ($mainCategories as $mCat): 
                                    $isSelected = (!empty($catId) && $catId == $mCat['id']);
                                ?>
                                <a href="<?= $base ?>/shop?id=<?= $mCat['id'] ?><?= !empty($sort) ? '&sort=' . $sort : '' ?>" 
                                   class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold transition-colors <?= $isSelected ? 'bg-emerald-600 text-white' : 'text-gray-700 hover:bg-gray-50' ?>">
                                    <span class="truncate pr-1"><?= htmlspecialchars($mCat['name']) ?></span>
                                    <span class="text-[11px] <?= $isSelected ? 'text-emerald-100' : 'text-gray-400' ?>"><?= $mCat['total_product_count'] ?? 0 ?></span>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Price Range -->
                        <div class="pt-2 border-t border-gray-100">
                            <label class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-2">Price Range (৳)</label>
                            <div class="flex items-center gap-2">
                                <input type="number" name="min_price" value="<?= $minPrice !== null ? htmlspecialchars($minPrice) : '' ?>" placeholder="Min" class="w-full px-3 py-1.5 text-xs rounded-xl border border-gray-300 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                <span class="text-gray-400 text-xs">-</span>
                                <input type="number" name="max_price" value="<?= $maxPrice !== null ? htmlspecialchars($maxPrice) : '' ?>" placeholder="Max" class="w-full px-3 py-1.5 text-xs rounded-xl border border-gray-300 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            </div>
                        </div>

                        <!-- Quick Toggles -->
                        <div class="pt-2 border-t border-gray-100 space-y-2">
                            <label class="flex items-center gap-2 text-xs font-bold text-gray-700 cursor-pointer">
                                <input type="checkbox" name="in_stock" value="1" <?= !empty($inStockOnly) ? 'checked' : '' ?> onchange="this.form.submit()" class="rounded text-emerald-600 focus:ring-emerald-500">
                                <span>In Stock Only</span>
                            </label>
                            <label class="flex items-center gap-2 text-xs font-bold text-gray-700 cursor-pointer">
                                <input type="checkbox" name="deals" value="1" <?= !empty($isDeals) ? 'checked' : '' ?> onchange="this.form.submit()" class="rounded text-emerald-600 focus:ring-emerald-500">
                                <span class="text-amber-600">⚡ Flash Deals Only</span>
                            </label>
                        </div>

                        <!-- Brands Filter -->
                        <?php if (!empty($brands)): ?>
                        <div class="pt-2 border-t border-gray-100">
                            <label class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-2">Brand</label>
                            <select name="brand" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs rounded-xl border border-gray-300 focus:outline-none focus:ring-1 focus:ring-emerald-500 bg-white">
                                <option value="">All Brands</option>
                                <?php foreach ($brands as $b): ?>
                                <option value="<?= $b['id'] ?>" <?= (!empty($selectedBrand) && $selectedBrand == $b['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($b['name']) ?> (<?= $b['prod_count'] ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>

                        <button type="submit" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition-colors shadow-xs">
                            Apply Filter
                        </button>
                    </form>
                </div>
            </aside>

            <!-- PRODUCT GRID AREA -->
            <main class="flex-1 min-w-0">

                <!-- Top Control Bar (Mobile filter toggle + Sort + Active Tags) -->
                <div class="bg-white rounded-2xl border border-gray-200/90 p-3 sm:p-4 mb-5 shadow-2xs flex flex-wrap items-center justify-between gap-3">
                    
                    <!-- Left: Mobile Filter Button & Results Counter -->
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="toggleMobileFilterDrawer()" class="lg:hidden inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-gray-100 hover:bg-emerald-50 text-gray-800 text-xs font-bold transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                            <span>Filters</span>
                            <?php if ($activeFilterCount > 0): ?>
                                <span class="w-4 h-4 rounded-full bg-emerald-600 text-white text-[10px] flex items-center justify-center font-black"><?= $activeFilterCount ?></span>
                            <?php endif; ?>
                        </button>

                        <span class="text-xs text-gray-500 font-medium">
                            Showing <strong class="text-gray-900"><?= count($products) ?></strong> product<?= count($products) === 1 ? '' : 's' ?>
                        </span>
                    </div>

                    <!-- Right: Sort Selector -->
                    <div class="flex items-center gap-2">
                        <label for="shop-sort" class="text-xs font-bold text-gray-500 hidden sm:inline">Sort by:</label>
                        <select id="shop-sort" onchange="applyShopSort(this.value)" class="px-3 py-1.5 text-xs font-bold text-gray-700 rounded-xl border border-gray-200 bg-white focus:outline-none focus:ring-1 focus:ring-emerald-500 shadow-2xs cursor-pointer">
                            <option value="newest" <?= ($sort === 'newest') ? 'selected' : '' ?>>Newest Arrivals</option>
                            <option value="price_asc" <?= ($sort === 'price_asc') ? 'selected' : '' ?>>Price: Low to High</option>
                            <option value="price_desc" <?= ($sort === 'price_desc') ? 'selected' : '' ?>>Price: High to Low</option>
                            <option value="deals" <?= ($sort === 'deals') ? 'selected' : '' ?>>Biggest Discount</option>
                            <option value="name_asc" <?= ($sort === 'name_asc') ? 'selected' : '' ?>>Name: A to Z</option>
                        </select>
                    </div>
                </div>

                <!-- Products Grid -->
                <?php if (empty($products)): ?>
                <div class="bg-white rounded-3xl border border-gray-200/90 p-12 text-center shadow-2xs">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400 text-2xl">
                        🛒
                    </div>
                    <h3 class="text-base font-bold text-gray-800">No products match your criteria</h3>
                    <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">Try changing your filter settings, clearing search keywords, or exploring other categories.</p>
                    <a href="<?= $base ?>/shop" class="mt-4 inline-block bg-emerald-600 text-white text-xs font-bold px-5 py-2.5 rounded-xl hover:bg-emerald-700 transition-colors shadow-xs">
                        Clear Filters
                    </a>
                </div>
                <?php else: ?>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-3.5 sm:gap-4">
                    <?php foreach ($products as $prod): 
                        $pSell = (float)($prod['sell_price'] ?? 0);
                        $pReg = !empty($prod['regular_price']) ? (float)$prod['regular_price'] : null;
                        $pHasDisc = ($pReg && $pReg > $pSell);
                        $pDiscPercent = $pHasDisc ? round((($pReg - $pSell) / $pReg) * 100) : 0;
                        
                        $pImg = \Models\Product::getImageUrl($prod['image_path'] ?? '', $base);
                        $fallbackImg = !empty($base) ? rtrim($base, '/') . '/images/default-product.svg' : '/images/default-product.svg';
                        $unitDisplay = $prod['selling_unit'] ?? $prod['base_unit'] ?? '1 Unit';
                    ?>
                    <div class="product-card bg-white rounded-2xl border border-gray-100 hover:border-emerald-300 shadow-2xs hover:shadow-lg transition-all duration-300 flex flex-col overflow-hidden group">
                        
                        <!-- Product Image -->
                        <a href="<?= $base ?>/product?id=<?= $prod['id'] ?>" class="relative pt-5 pb-3 px-3 bg-white flex items-center justify-center min-h-[140px] sm:min-h-[160px]">
                            <?php if ($pHasDisc): ?>
                                <span class="absolute top-2.5 left-2.5 bg-rose-500 text-white text-[10px] font-black px-2 py-0.5 rounded-md shadow-2xs">
                                    -<?= $pDiscPercent ?>%
                                </span>
                            <?php endif; ?>
                            <img src="<?= htmlspecialchars($pImg) ?>" 
                                 alt="<?= htmlspecialchars($prod['name']) ?>" 
                                 class="max-h-28 sm:max-h-32 w-auto max-w-[85%] object-contain group-hover:scale-105 transition-transform duration-300"
                                 loading="lazy"
                                 onerror="this.onerror=null; this.src='<?= $fallbackImg ?>';">
                        </a>

                        <!-- Product Body -->
                        <div class="p-3 sm:p-4 flex flex-col flex-grow bg-white">
                            <div class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1 truncate">
                                <?= htmlspecialchars($prod['category_name'] ?? 'Fresh Food') ?>
                            </div>

                            <a href="<?= $base ?>/product?id=<?= $prod['id'] ?>" class="font-bold text-gray-900 text-xs sm:text-sm leading-snug line-clamp-2 hover:text-emerald-700 transition-colors mb-1">
                                <?= htmlspecialchars($prod['name']) ?>
                            </a>
                            
                            <div class="text-[11px] text-gray-400 font-medium mb-3">
                                <?= htmlspecialchars($unitDisplay) ?>
                            </div>

                            <!-- Price Row -->
                            <div class="flex items-baseline gap-1.5 mb-3.5 mt-auto">
                                <span class="text-xs font-bold text-emerald-700">৳</span>
                                <span class="text-base sm:text-lg font-black text-gray-900"><?= number_format($pSell, 2) ?></span>
                                <?php if ($pHasDisc): ?>
                                    <span class="text-[11px] text-gray-400 line-through">৳<?= number_format($pReg, 2) ?></span>
                                <?php endif; ?>
                            </div>

                            <!-- Add to Bag Button -->
                            <button type="button" 
                                    onclick="addToCartAjax(<?= $prod['id'] ?>, this)"
                                    class="w-full py-2 px-3 rounded-xl bg-emerald-50 hover:bg-emerald-600 text-emerald-800 hover:text-white border border-emerald-200 hover:border-emerald-600 font-bold text-xs flex items-center justify-center gap-1.5 transition-all duration-200 cursor-pointer shadow-2xs hover:shadow-sm group/btn">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600 group-hover/btn:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                <span>Add to Cart</span>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

            </main>
        </div>

    </div>
</div>

<!-- Mobile Filter Drawer -->
<div id="mobile-filter-backdrop" onclick="toggleMobileFilterDrawer()" class="fixed inset-0 bg-black/50 z-50 opacity-0 pointer-events-none transition-opacity duration-300"></div>
<div id="mobile-filter-drawer" class="fixed top-0 left-0 bottom-0 w-80 max-w-[85vw] bg-white z-50 shadow-2xl transform -translate-x-full transition-transform duration-300 p-5 flex flex-col">
    <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
        <span class="font-black text-gray-900 text-sm uppercase tracking-wider">Filter Products</span>
        <button type="button" onclick="toggleMobileFilterDrawer()" class="p-1.5 text-gray-400 hover:text-gray-700">
            ✕
        </button>
    </div>
    <div class="flex-grow overflow-y-auto space-y-5 custom-scrollbar">
        <form action="<?= $base ?>/shop" method="GET" class="space-y-5">
            <?php if (!empty($search)): ?>
                <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
            <?php endif; ?>

            <!-- Categories -->
            <div>
                <label class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-2">Department</label>
                <div class="space-y-1">
                    <a href="<?= $base ?>/shop" class="block px-3 py-2 rounded-xl text-xs font-bold <?= empty($catId) ? 'bg-emerald-600 text-white' : 'text-gray-700 hover:bg-gray-50' ?>">
                        All Departments
                    </a>
                    <?php foreach ($mainCategories as $mCat): ?>
                    <a href="<?= $base ?>/shop?id=<?= $mCat['id'] ?>" class="block px-3 py-2 rounded-xl text-xs font-bold <?= (!empty($catId) && $catId == $mCat['id']) ? 'bg-emerald-600 text-white' : 'text-gray-700 hover:bg-gray-50' ?>">
                        <?= htmlspecialchars($mCat['name']) ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Price -->
            <div class="pt-2 border-t border-gray-100">
                <label class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-2">Price Range (৳)</label>
                <div class="flex items-center gap-2">
                    <input type="number" name="min_price" value="<?= $minPrice !== null ? htmlspecialchars($minPrice) : '' ?>" placeholder="Min" class="w-full px-3 py-1.5 text-xs rounded-xl border border-gray-300">
                    <span class="text-gray-400">-</span>
                    <input type="number" name="max_price" value="<?= $maxPrice !== null ? htmlspecialchars($maxPrice) : '' ?>" placeholder="Max" class="w-full px-3 py-1.5 text-xs rounded-xl border border-gray-300">
                </div>
            </div>

            <!-- Quick Toggles -->
            <div class="pt-2 border-t border-gray-100 space-y-2">
                <label class="flex items-center gap-2 text-xs font-bold text-gray-700">
                    <input type="checkbox" name="in_stock" value="1" <?= !empty($inStockOnly) ? 'checked' : '' ?> class="rounded text-emerald-600">
                    <span>In Stock Only</span>
                </label>
                <label class="flex items-center gap-2 text-xs font-bold text-gray-700">
                    <input type="checkbox" name="deals" value="1" <?= !empty($isDeals) ? 'checked' : '' ?> class="rounded text-emerald-600">
                    <span class="text-amber-600">⚡ Flash Deals Only</span>
                </label>
            </div>

            <button type="submit" class="w-full py-2.5 rounded-xl bg-emerald-600 text-white font-bold text-xs">
                Apply Filters
            </button>
        </form>
    </div>
</div>

<script>
function toggleMobileFilterDrawer() {
    const backdrop = document.getElementById('mobile-filter-backdrop');
    const drawer = document.getElementById('mobile-filter-drawer');
    const isOpen = !drawer.classList.contains('-translate-x-full');
    if (isOpen) {
        drawer.classList.add('-translate-x-full');
        backdrop.classList.add('opacity-0', 'pointer-events-none');
    } else {
        drawer.classList.remove('-translate-x-full');
        backdrop.classList.remove('opacity-0', 'pointer-events-none');
    }
}

function applyShopSort(sortVal) {
    const url = new URL(window.location.href);
    url.searchParams.set('sort', sortVal);
    window.location.href = url.toString();
}
</script>

<?php
$content = ob_get_clean();
require 'layout.php';
?>
