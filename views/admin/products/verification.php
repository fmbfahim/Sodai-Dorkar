<div class="max-w-5xl mx-auto mb-16 px-4 pt-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-secondary-900">Price Verification</h2>
            <p class="text-secondary-500 text-xs mt-1">Verify and update prices for newly added products.</p>
        </div>
    </div>

    <!-- Category Filter -->
    <div class="mb-6 overflow-x-auto pb-2 scrollbar-hide">
        <div class="flex gap-2">
            <a href="?category=" class="whitespace-nowrap px-4 py-2 rounded-full text-sm font-medium transition-colors <?php echo empty($currentCategory) ? 'bg-primary-600 text-white' : 'bg-white border border-secondary-200 text-secondary-600 hover:bg-secondary-50'; ?>">
                All Categories
            </a>
            <?php foreach ($categories as $cat): ?>
                <a href="?category=<?php echo $cat['id']; ?>" class="whitespace-nowrap px-4 py-2 rounded-full text-sm font-medium transition-colors <?php echo ($currentCategory == $cat['id']) ? 'bg-primary-600 text-white' : 'bg-white border border-secondary-200 text-secondary-600 hover:bg-secondary-50'; ?>">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <form action="/sodai-dorkar/public/admin/products/verification-update" method="POST">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mb-24">
            <?php if (empty($products)): ?>
                <div class="col-span-full bg-white p-8 rounded-xl border border-secondary-200 text-center">
                    <p class="text-secondary-500">No pending products to verify.</p>
                </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-secondary-200 flex flex-col hover:border-primary-300 transition-colors">
                        <div class="flex items-center gap-3 mb-3">
                            <img src="<?php echo !empty($product['image_path']) ? htmlspecialchars($product['image_path']) : '/sodai-dorkar/public/images/default-product.svg'; ?>" class="w-16 h-16 rounded-lg object-cover bg-secondary-50 border border-secondary-100">
                            <div>
                                <h3 class="font-bold text-secondary-900 text-sm line-clamp-2"><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p class="text-xs text-secondary-500 mt-1"><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 mt-auto bg-secondary-50 p-3 rounded-lg">
                            <div>
                                <label class="block text-xs font-bold text-secondary-700 mb-1">Buy Price</label>
                                <input type="number" step="0.01" name="products[<?php echo $product['id']; ?>][buy_price]" value="<?php echo htmlspecialchars($product['buy_price']); ?>" class="w-full px-3 py-2 border border-secondary-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-secondary-700 mb-1">Sell Price</label>
                                <input type="number" step="0.01" name="products[<?php echo $product['id']; ?>][sell_price]" value="<?php echo htmlspecialchars($product['sell_price']); ?>" class="w-full px-3 py-2 border border-secondary-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 bg-white">
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if (!empty($products)): ?>
            <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-secondary-200 p-4 md:pl-64 shadow-[0_-4px_15px_-3px_rgba(0,0,0,0.1)] z-40">
                <div class="max-w-5xl mx-auto flex justify-end">
                    <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl shadow-md transition-all flex items-center justify-center gap-2 transform active:scale-95">
                        <ion-icon name="checkmark-circle-outline" class="text-xl"></ion-icon>
                        Update & Verify Products
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </form>
</div>
