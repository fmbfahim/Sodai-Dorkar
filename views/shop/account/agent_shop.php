<?php
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
$title = 'ফিল্ড এজেন্ট শপ ও ক্যাটালগ';
$initialCart = array_values($_SESSION['agent_cart'] ?? []);
?>

<div class="space-y-4" x-data="{
    search: '<?= htmlspecialchars($search, ENT_QUOTES) ?>',
    activeCat: <?= $activeCatId ? (int)$activeCatId : 'null' ?>,
    cart: <?= json_encode($initialCart) ?>,
    loading: false,

    get cartCount() {
        return this.cart.reduce((sum, it) => sum + parseInt(it.quantity || 1), 0);
    },

    get cartTotal() {
        return this.cart.reduce((sum, it) => sum + (parseFloat(it.price || 0) * parseInt(it.quantity || 1)), 0);
    },

    getItemQty(productId) {
        const item = this.cart.find(it => it.id == productId);
        return item ? parseInt(item.quantity) : 0;
    },

    async addToCart(productId) {
        this.loading = true;
        try {
            const res = await fetch('<?= $base ?>/agent/cart/add', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId, quantity: 1 })
            });
            const data = await res.json();
            if (data.success) {
                this.cart = data.items;
                if (window.showAgentToast) window.showAgentToast('পণ্যটি এজেন্ট কার্টে যুক্ত হয়েছে!', 'success');
            } else {
                if (window.showAgentToast) window.showAgentToast(data.message || 'ব্যর্থ হয়েছে', 'error');
            }
        } catch (e) {
            console.error(e);
        } finally {
            this.loading = false;
        }
    },

    async updateQty(productId, action) {
        try {
            const res = await fetch('<?= $base ?>/agent/cart/update', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId, action: action })
            });
            const data = await res.json();
            if (data.success) {
                this.cart = data.items;
            }
        } catch (e) {
            console.error(e);
        }
    }
}">

    <!-- Top Breadcrumb & Navigation Header -->
    <div class="flex items-center justify-between border-b border-gray-200/80 pb-3">
        <div>
            <div class="flex items-center gap-1.5 text-xs text-gray-500 mb-0.5">
                <a href="<?= $base ?>/account" class="hover:text-emerald-600">একাউন্ট</a>
                <span>/</span>
                <a href="<?= $base ?>/account/agent/dashboard" class="hover:text-emerald-600">এজেন্ট হাব</a>
                <span>/</span>
                <span class="text-gray-900 font-bold">পণ্য ক্যাটালগ</span>
            </div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight flex items-center gap-2">
                <span>কাস্টমারদের জন্য পণ্য নির্বাচন ও অর্ডার</span>
                <ion-icon name="storefront" class="text-emerald-600"></ion-icon>
            </h1>
        </div>

        <a href="<?= $base ?>/account/agent/checkout" class="relative px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-xs font-bold shadow-xs flex items-center gap-1.5 transition-all">
            <ion-icon name="cart" class="text-base"></ion-icon>
            <span>কার্ট ও চেকআউট</span>
            <span class="ml-1 px-1.5 py-0.5 rounded-full bg-white text-emerald-800 text-[10px] font-black" x-text="cartCount"></span>
        </a>
    </div>

    <!-- Search & Category Filters -->
    <div class="bg-white rounded-2xl p-3 border border-gray-200/80 shadow-2xs space-y-2.5">
        <form action="<?= $base ?>/account/agent/shop" method="GET" class="relative">
            <?php if ($activeCatId): ?>
                <input type="hidden" name="category_id" value="<?= $activeCatId ?>">
            <?php endif; ?>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="পণ্যের নাম দিয়ে সার্চ করুন..." class="w-full pl-10 pr-10 py-2.5 bg-gray-50 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-xs font-medium">
            <ion-icon name="search-outline" class="absolute left-3.5 top-3 text-lg text-gray-400"></ion-icon>
            <?php if (!empty($search)): ?>
                <a href="<?= $base ?>/account/agent/shop<?= $activeCatId ? '?category_id=' . $activeCatId : '' ?>" class="absolute right-3.5 top-2.5 text-gray-400 hover:text-gray-600 text-lg">
                    <ion-icon name="close-circle"></ion-icon>
                </a>
            <?php endif; ?>
        </form>

        <!-- Categories Slider -->
        <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-0.5">
            <a href="<?= $base ?>/account/agent/shop<?= !empty($search) ? '?search=' . urlencode($search) : '' ?>" class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all <?= empty($activeCatId) ? 'bg-emerald-600 text-white shadow-xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' ?>">
                সব পণ্য
            </a>
            <?php foreach ($categories as $cat): ?>
                <a href="<?= $base ?>/account/agent/shop?category_id=<?= $cat['id'] ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all <?= ($activeCatId == $cat['id']) ? 'bg-emerald-600 text-white shadow-xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' ?>">
                    <?= htmlspecialchars($cat['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
        <?php if (empty($products)): ?>
            <div class="col-span-full bg-white rounded-3xl p-12 text-center text-gray-400 border border-gray-100 space-y-2">
                <ion-icon name="cube-outline" class="text-4xl text-gray-300"></ion-icon>
                <p class="text-xs font-bold text-gray-600">কোনো পণ্য পাওয়া যায়নি</p>
                <p class="text-[11px] text-gray-400">অন্য কোনো নাম বা ক্যাটাগরি দিয়ে চেষ্টা করুন।</p>
            </div>
        <?php else: ?>
            <?php foreach ($products as $prod): ?>
                <?php
                $price = (float)($prod['sell_price'] ?? 0);
                $oldPrice = (float)($prod['regular_price'] ?? 0);
                $unit = $prod['base_unit'] ?? $prod['unit_type'] ?? 'Pcs';
                $imgPath = !empty($prod['image_path']) ? $base . '/' . ltrim($prod['image_path'], '/') : $base . '/images/placeholder.png';
                ?>
                <div class="bg-white rounded-2xl p-3 border border-gray-200/80 shadow-2xs hover:border-emerald-300 hover:shadow-xs transition-all flex flex-col justify-between">
                    <div>
                        <div class="relative w-full aspect-square rounded-xl bg-gray-50 overflow-hidden mb-2.5 flex items-center justify-center">
                            <img src="<?= htmlspecialchars($imgPath) ?>" alt="<?= htmlspecialchars($prod['name']) ?>" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='https://placehold.co/200x200?text=FreshMart';">
                            <span class="absolute top-2 left-2 px-2 py-0.5 rounded-md text-[10px] font-bold bg-white/90 text-emerald-800 shadow-2xs backdrop-blur-xs">
                                ইন-স্টক
                            </span>
                        </div>
                        <h3 class="text-xs font-bold text-gray-900 line-clamp-2 leading-snug" title="<?= htmlspecialchars($prod['name']) ?>">
                            <?= htmlspecialchars($prod['name']) ?>
                        </h3>
                        <div class="text-[11px] text-gray-500 mt-0.5"><?= htmlspecialchars($unit) ?></div>
                    </div>

                    <div class="mt-3 pt-2 border-t border-gray-100 space-y-2">
                        <div class="flex items-baseline gap-1.5">
                            <span class="text-sm font-black text-gray-900">৳<?= number_format($price, 0) ?></span>
                            <?php if ($oldPrice > $price): ?>
                                <span class="text-[10px] text-gray-400 line-through">৳<?= number_format($oldPrice, 0) ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Cart Button or Stepper -->
                        <div>
                            <template x-if="getItemQty(<?= $prod['id'] ?>) === 0">
                                <button type="button" @click="addToCart(<?= $prod['id'] ?>)" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-2xs flex items-center justify-center gap-1.5 active:scale-95">
                                    <ion-icon name="add" class="text-base"></ion-icon>
                                    <span>অর্ডারে যোগ করুন</span>
                                </button>
                            </template>

                            <template x-if="getItemQty(<?= $prod['id'] ?>) > 0">
                                <div class="flex items-center justify-between bg-emerald-50 border border-emerald-200 rounded-xl p-0.5">
                                    <button type="button" @click="updateQty(<?= $prod['id'] ?>, 'dec')" class="w-7 h-7 rounded-lg bg-white text-emerald-700 hover:bg-emerald-100 flex items-center justify-center shadow-2xs font-black text-sm active:scale-90 transition-transform">
                                        <ion-icon name="remove"></ion-icon>
                                    </button>
                                    <span class="text-xs font-black text-emerald-900 px-2" x-text="getItemQty(<?= $prod['id'] ?>)"></span>
                                    <button type="button" @click="updateQty(<?= $prod['id'] ?>, 'inc')" class="w-7 h-7 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 flex items-center justify-center shadow-2xs font-black text-sm active:scale-90 transition-transform">
                                        <ion-icon name="add"></ion-icon>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Floating Sticky Cart Preview Bar -->
    <div x-show="cartCount > 0" x-cloak class="sticky bottom-4 z-40 p-2">
        <div class="bg-gradient-to-r from-emerald-800 to-teal-800 text-white rounded-2xl p-3.5 shadow-2xl border border-emerald-500/40 flex items-center justify-between gap-3 animate-in fade-in duration-200">
            <div class="flex items-center gap-3 pl-2">
                <div class="relative w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-xl">
                    <ion-icon name="cart"></ion-icon>
                    <span class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-amber-400 text-gray-900 font-black text-[10px] flex items-center justify-center shadow-xs" x-text="cartCount"></span>
                </div>
                <div>
                    <div class="text-[11px] text-emerald-200 font-bold uppercase tracking-wider">এজেন্ট কার্ট মোট</div>
                    <div class="text-base font-black leading-tight">৳<span x-text="cartTotal.toFixed(2)"></span></div>
                </div>
            </div>

            <a href="<?= $base ?>/account/agent/checkout" class="px-5 py-2.5 bg-white hover:bg-emerald-50 text-emerald-900 rounded-xl text-xs font-black shadow-md flex items-center gap-1.5 transition-all active:scale-95 shrink-0">
                <span>কাস্টমার নির্বাচন ও চেকআউট</span>
                <ion-icon name="arrow-forward" class="text-sm"></ion-icon>
            </a>
        </div>
    </div>

</div>
