<?php
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
$initialCart = array_values($_SESSION['agent_cart'] ?? []);
?>

<div class="space-y-3.5" x-data="{
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
                window.showAgentToast('পণ্যটি কার্টে যুক্ত হয়েছে!', 'success');
            } else {
                window.showAgentToast(data.message || 'ব্যর্থ হয়েছে', 'error');
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

    <!-- Top Search & Filter Bar -->
    <div class="sticky top-[53px] z-30 bg-gray-50/90 backdrop-blur-md pt-1 pb-2 space-y-2">
        <form action="<?= $base ?>/agent/shop" method="GET" class="relative">
            <?php if ($activeCatId): ?>
                <input type="hidden" name="category_id" value="<?= $activeCatId ?>">
            <?php endif; ?>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="পণ্যের নাম দিয়ে সার্চ করুন..." class="w-full pl-10 pr-10 py-2.5 bg-white rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-xs text-xs font-medium">
            <ion-icon name="search-outline" class="absolute left-3.5 top-3 text-lg text-gray-400"></ion-icon>
            <?php if (!empty($search)): ?>
                <a href="<?= $base ?>/agent/shop<?= $activeCatId ? '?category_id=' . $activeCatId : '' ?>" class="absolute right-3.5 top-2.5 text-gray-400 hover:text-gray-600 text-lg">
                    <ion-icon name="close-circle"></ion-icon>
                </a>
            <?php endif; ?>
        </form>

        <!-- Categories Horizontal Slider -->
        <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-0.5">
            <a href="<?= $base ?>/agent/shop<?= !empty($search) ? '?search=' . urlencode($search) : '' ?>" class="px-3.5 py-1.5 rounded-2xl text-xs font-bold whitespace-nowrap transition-all <?= empty($activeCatId) ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white text-gray-600 border border-gray-200/80 hover:border-emerald-300' ?>">
                সব পণ্য
            </a>
            <?php foreach ($categories as $cat): ?>
                <a href="<?= $base ?>/agent/shop?category_id=<?= $cat['id'] ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="px-3 py-1.5 rounded-2xl text-xs font-bold whitespace-nowrap transition-all <?= ($activeCatId == $cat['id']) ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white text-gray-600 border border-gray-200/80 hover:border-emerald-300' ?>">
                    <?= htmlspecialchars($cat['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Active Area Badge Notice -->
    <?php if (!empty($assignedAreas)): ?>
        <div class="flex items-center justify-between bg-emerald-50/80 border border-emerald-200/60 rounded-2xl px-3.5 py-2 text-[11px] text-emerald-800">
            <div class="flex items-center gap-1.5">
                <ion-icon name="information-circle" class="text-base text-emerald-600"></ion-icon>
                <span>নির্ধারিত এরিয়া: <strong><?= htmlspecialchars(implode(', ', array_column($assignedAreas, 'area_name'))) ?></strong></span>
            </div>
            <span class="text-[10px] font-bold text-emerald-600 uppercase">ফিল্ড সেলস</span>
        </div>
    <?php endif; ?>

    <!-- Product Grid (2 columns on mobile, exactly like normal user ecommerce experience) -->
    <div class="grid grid-cols-2 gap-3 sm:gap-4">
        <?php if (empty($products)): ?>
            <div class="col-span-2 bg-white rounded-3xl p-10 text-center text-gray-400 border border-gray-100">
                <ion-icon name="search-outline" class="text-4xl text-gray-300 mb-2"></ion-icon>
                <p class="text-xs font-bold text-gray-600">কোনো পণ্য খুঁজে পাওয়া যায়নি।</p>
                <p class="text-[11px] text-gray-400 mt-1">অন্য ক্যাটাগরি বা কিওয়ার্ড দিয়ে সার্চ করুন।</p>
                <a href="<?= $base ?>/agent/shop" class="inline-block mt-3 px-4 py-2 bg-emerald-600 text-white rounded-xl text-xs font-bold">
                    সব পণ্য দেখুন
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($products as $prod): ?>
                <?php
                $img = !empty($prod['image_path']) ? $base . '/' . ltrim($prod['image_path'], '/') : $base . '/images/placeholder-product.png';
                $price = (float)($prod['sell_price'] ?? 0);
                $oldPrice = !empty($prod['regular_price']) ? (float)$prod['regular_price'] : 0;
                $hasDiscount = ($oldPrice > $price);
                ?>
                <div class="bg-white rounded-3xl border border-gray-200/80 shadow-xs hover:border-emerald-300 transition-all flex flex-col justify-between overflow-hidden group">
                    <!-- Image Area -->
                    <div class="relative bg-gray-50/70 p-3 pt-4 flex items-center justify-center aspect-square overflow-hidden">
                        <?php if ($hasDiscount): ?>
                            <span class="absolute top-2 left-2 px-1.5 py-0.5 rounded-lg bg-red-500 text-white text-[9px] font-black tracking-wide">
                                ছাড়
                            </span>
                        <?php endif; ?>
                        
                        <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($prod['name']) ?>" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300" loading="lazy" onerror="this.src='https://placehold.co/200x200/f3f4f6/15803d?text=Product'">
                    </div>

                    <!-- Details Area -->
                    <div class="p-3 flex flex-col justify-between flex-1 space-y-2">
                        <div>
                            <span class="text-[10px] text-emerald-700 font-bold bg-emerald-50 px-1.5 py-0.5 rounded-md inline-block mb-1">
                                <?= htmlspecialchars($prod['unit'] ?? '১ পিস') ?>
                            </span>
                            <h3 class="text-xs font-black text-gray-900 line-clamp-2 leading-tight min-h-[2rem]">
                                <?= htmlspecialchars($prod['name']) ?>
                            </h3>
                        </div>

                        <!-- Price & Action Button -->
                        <div class="pt-1 border-t border-gray-100 flex flex-col gap-2">
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-sm font-black text-emerald-700">৳<?= number_format($price, 0) ?></span>
                                <?php if ($hasDiscount): ?>
                                    <span class="text-[10px] text-gray-400 line-through">৳<?= number_format($oldPrice, 0) ?></span>
                                <?php endif; ?>
                            </div>

                            <!-- Cart Controls (Dynamic Inline Adjust) -->
                            <div>
                                <template x-if="getItemQty(<?= $prod['id'] ?>) === 0">
                                    <button @click="addToCart(<?= $prod['id'] ?>)" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-xs font-bold transition-all shadow-xs flex items-center justify-center gap-1.5 active:scale-95">
                                        <ion-icon name="add" class="text-base"></ion-icon>
                                        <span>যোগ করুন</span>
                                    </button>
                                </template>

                                <template x-if="getItemQty(<?= $prod['id'] ?>) > 0">
                                    <div class="flex items-center justify-between bg-emerald-50 border border-emerald-200 rounded-2xl p-0.5">
                                        <button @click="updateQty(<?= $prod['id'] ?>, 'dec')" class="w-7 h-7 rounded-xl bg-white text-emerald-700 hover:bg-emerald-100 flex items-center justify-center shadow-2xs font-black text-sm active:scale-90 transition-transform">
                                            <ion-icon name="remove"></ion-icon>
                                        </button>
                                        <span class="text-xs font-black text-emerald-900 px-2" x-text="getItemQty(<?= $prod['id'] ?>)"></span>
                                        <button @click="updateQty(<?= $prod['id'] ?>, 'inc')" class="w-7 h-7 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 flex items-center justify-center shadow-2xs font-black text-sm active:scale-90 transition-transform">
                                            <ion-icon name="add"></ion-icon>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Floating Sticky Bottom Cart Preview Bar -->
    <div x-show="cartCount > 0" x-cloak class="fixed bottom-14 left-0 right-0 z-40 p-3 pointer-events-none transition-all duration-300">
        <div class="max-w-2xl mx-auto pointer-events-auto">
            <div class="bg-gradient-to-r from-emerald-800 to-green-800 text-white rounded-3xl p-3 shadow-2xl border border-emerald-500/30 flex items-center justify-between gap-3 animate-in fade-in slide-in-from-bottom-3 duration-200">
                <div class="flex items-center gap-3 pl-2">
                    <div class="relative w-10 h-10 rounded-2xl bg-white/20 flex items-center justify-center text-xl">
                        <ion-icon name="cart"></ion-icon>
                        <span class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-white text-emerald-900 font-black text-[10px] flex items-center justify-center shadow-xs" x-text="cartCount"></span>
                    </div>
                    <div>
                        <div class="text-[11px] text-emerald-200 font-bold uppercase tracking-wider">মোট কার্ট মূল্য</div>
                        <div class="text-base font-black leading-tight">৳<span x-text="cartTotal.toFixed(2)"></span></div>
                    </div>
                </div>

                <a href="<?= $base ?>/agent/checkout" class="px-5 py-2.5 bg-white hover:bg-emerald-50 text-emerald-900 rounded-2xl text-xs font-black shadow-md flex items-center gap-1.5 transition-all active:scale-95 shrink-0">
                    <span>চেকআউট ও কাস্টমার</span>
                    <ion-icon name="arrow-forward" class="text-sm"></ion-icon>
                </a>
            </div>
        </div>
    </div>

</div>
