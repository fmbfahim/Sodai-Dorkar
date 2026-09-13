<div class="max-w-5xl mx-auto mb-16 px-4 pt-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-secondary-900">On-Demand Products</h2>
            <p class="text-secondary-500 text-xs mt-1">Products currently showing high demand among customers.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php if (empty($products)): ?>
            <div class="col-span-full bg-white p-8 rounded-xl border border-secondary-200 text-center">
                <p class="text-secondary-500">No on-demand products found.</p>
            </div>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-secondary-200 flex flex-col">
                    <div class="flex items-start gap-4">
                        <img src="<?php echo !empty($product['image_path']) ? htmlspecialchars($product['image_path']) : '/sodai-dorkar/public/images/default-product.svg'; ?>" class="w-20 h-20 rounded-lg object-cover bg-secondary-50 border border-secondary-100 flex-shrink-0">
                        <div class="flex-1">
                            <h3 class="font-bold text-secondary-900 text-sm line-clamp-2"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="text-xs text-secondary-500 mt-1"><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></p>
                            
                            <div class="mt-3 flex items-center justify-between">
                                <span class="font-bold text-secondary-900">৳<?php echo htmlspecialchars($product['sell_price']); ?></span>
                                <span class="px-2 py-1 bg-amber-50 text-amber-700 text-xs font-bold rounded-lg flex items-center gap-1 border border-amber-200">
                                    <ion-icon name="flame"></ion-icon>
                                    <?php echo htmlspecialchars($product['demand_percentage']); ?>%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
