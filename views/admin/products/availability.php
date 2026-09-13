<div class="max-w-5xl mx-auto mb-16 px-4 pt-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-secondary-900">Availability Status</h2>
        <p class="text-secondary-500 text-xs mt-1">Select products to mark them as In Stock in the store. Unselected products will be marked as Out of Stock.</p>
    </div>

    <form action="/sodai-dorkar/public/admin/products/availability-update" method="POST">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mb-24">
            <?php if (empty($products)): ?>
                <div class="col-span-full bg-white p-8 rounded-xl border border-secondary-200 text-center">
                    <p class="text-secondary-500">No pending products available.</p>
                </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <input type="hidden" name="all_products[]" value="<?php echo $product['id']; ?>">
                    <label class="cursor-pointer group">
                        <div class="relative bg-white p-4 rounded-xl shadow-sm border-2 border-transparent hover:border-primary-300 transition-all flex flex-col h-full has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50">
                            
                            <div class="absolute top-4 right-4">
                                <input type="checkbox" name="selected_products[]" value="<?php echo $product['id']; ?>" class="w-5 h-5 text-primary-600 rounded border-secondary-300 focus:ring-primary-500 transition-colors">
                            </div>

                            <div class="flex items-center gap-4 mb-3 pr-8">
                                <img src="<?php echo !empty($product['image_path']) ? htmlspecialchars($product['image_path']) : '/sodai-dorkar/public/images/default-product.svg'; ?>" class="w-16 h-16 rounded-lg object-cover bg-secondary-50 border border-secondary-100">
                                <div>
                                    <h3 class="font-bold text-secondary-900 text-sm line-clamp-2"><?php echo htmlspecialchars($product['name']); ?></h3>
                                    <p class="text-xs text-secondary-500 mt-1"><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></p>
                                </div>
                            </div>
                            
                            <div class="mt-auto flex items-end justify-between pt-3 border-t border-secondary-100">
                                <div>
                                    <span class="text-xs text-secondary-500 block">Price</span>
                                    <span class="font-bold text-secondary-900">৳<?php echo htmlspecialchars($product['sell_price']); ?></span>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs text-secondary-500 block">Demand</span>
                                    <span class="font-bold text-amber-600 flex items-center gap-1 justify-end">
                                        <ion-icon name="trending-up-outline"></ion-icon>
                                        <?php echo htmlspecialchars($product['demand_percentage']); ?>%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </label>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if (!empty($products)): ?>
            <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-secondary-200 p-4 md:pl-64 shadow-[0_-4px_15px_-3px_rgba(0,0,0,0.1)] z-40">
                <div class="max-w-5xl mx-auto flex justify-end">
                    <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl shadow-md transition-all flex items-center justify-center gap-2 transform active:scale-95">
                        <ion-icon name="storefront-outline" class="text-xl"></ion-icon>
                        Update Store Availability
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </form>
</div>
