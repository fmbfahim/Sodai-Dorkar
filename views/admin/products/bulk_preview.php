<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-secondary-100 overflow-hidden">
        <div class="p-6 border-b border-secondary-100 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-secondary-900">Preview & Edit Products</h2>
                <p class="text-sm text-secondary-500 mt-1">Review the data below. You can edit any field before importing.</p>
            </div>
            <div>
                 <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                    <?php echo count($products); ?> Products Found
                </span>
            </div>
        </div>

        <form action="/sodai-dorkar/public/admin/products/bulk-store" method="POST">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-secondary-600 min-w-[1200px]">
                    <thead class="bg-secondary-50 text-secondary-700 font-bold uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3 min-w-[200px]">Product Name</th>
                            <th class="px-4 py-3 min-w-[150px]">Category Path</th>
                            <th class="px-4 py-3 min-w-[120px]">Brand</th>
                            <th class="px-4 py-3 min-w-[120px]">Vendor</th>
                            <th class="px-4 py-3 min-w-[100px]">SKU</th>
                            <th class="px-4 py-3 min-w-[200px]">Description</th>
                            <th class="px-4 py-3 w-28">Buy Price</th>
                            <th class="px-4 py-3 w-28">Reg Price</th>
                            <th class="px-4 py-3 w-28">Sell Price</th>
                            <th class="px-4 py-3 w-24">Stock</th>
                            <th class="px-4 py-3 w-28">Base Unit</th>
                            <th class="px-4 py-3 w-32">Purchase Unit</th>
                            <th class="px-4 py-3 w-16 text-center">Ignore</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-secondary-100">
                        <?php foreach ($products as $index => $product): ?>
                            <tr class="hover:bg-amber-50 transition-colors">
                                <td class="p-2">
                                    <input type="text" name="products[<?php echo $index; ?>][name]" value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>" class="w-full px-2 py-1.5 border border-secondary-200 rounded focus:ring-1 focus:ring-primary-500 focus:border-primary-500 text-sm" required>
                                </td>
                                <td class="p-2">
                                    <input type="text" name="products[<?php echo $index; ?>][category_path]" value="<?php echo htmlspecialchars($product['category_path'] ?? ''); ?>" class="w-full px-2 py-1.5 border border-secondary-200 rounded focus:ring-1 focus:ring-primary-500 focus:border-primary-500 text-sm" placeholder="e.g. Food > Fruits">
                                </td>
                                <td class="p-2">
                                    <input type="text" name="products[<?php echo $index; ?>][brand_name]" value="<?php echo htmlspecialchars($product['brand_name'] ?? ''); ?>" class="w-full px-2 py-1.5 border border-secondary-200 rounded focus:ring-1 focus:ring-primary-500 focus:border-primary-500 text-sm">
                                </td>
                                <td class="p-2">
                                    <input type="text" name="products[<?php echo $index; ?>][vendor_name]" value="<?php echo htmlspecialchars($product['vendor_name'] ?? ''); ?>" class="w-full px-2 py-1.5 border border-secondary-200 rounded focus:ring-1 focus:ring-primary-500 focus:border-primary-500 text-sm">
                                </td>
                                <td class="p-2">
                                    <input type="text" name="products[<?php echo $index; ?>][sku]" value="<?php echo htmlspecialchars($product['sku'] ?? ''); ?>" class="w-full px-2 py-1.5 border border-secondary-200 rounded focus:ring-1 focus:ring-primary-500 focus:border-primary-500 text-sm">
                                </td>
                                <td class="p-2">
                                    <input type="text" name="products[<?php echo $index; ?>][description]" value="<?php echo htmlspecialchars($product['description'] ?? ''); ?>" class="w-full px-2 py-1.5 border border-secondary-200 rounded focus:ring-1 focus:ring-primary-500 focus:border-primary-500 text-sm">
                                </td>
                                <td class="p-2">
                                    <input type="number" step="0.01" name="products[<?php echo $index; ?>][buy_price]" value="<?php echo htmlspecialchars($product['buy_price'] ?? '0'); ?>" class="w-full px-2 py-1.5 border border-secondary-200 rounded focus:ring-1 focus:ring-primary-500 focus:border-primary-500 text-sm">
                                </td>
                                <td class="p-2">
                                    <input type="number" step="0.01" name="products[<?php echo $index; ?>][regular_price]" value="<?php echo htmlspecialchars($product['regular_price'] ?? ''); ?>" class="w-full px-2 py-1.5 border border-secondary-200 rounded focus:ring-1 focus:ring-primary-500 focus:border-primary-500 text-sm">
                                </td>
                                <td class="p-2">
                                    <input type="number" step="0.01" name="products[<?php echo $index; ?>][sell_price]" value="<?php echo htmlspecialchars($product['sell_price'] ?? '0'); ?>" class="w-full px-2 py-1.5 border border-secondary-200 rounded focus:ring-1 focus:ring-primary-500 focus:border-primary-500 text-sm" required>
                                </td>
                                <td class="p-2">
                                    <input type="number" step="0.001" name="products[<?php echo $index; ?>][stock_qty]" value="<?php echo htmlspecialchars($product['stock_qty'] ?? '0'); ?>" class="w-full px-2 py-1.5 border border-secondary-200 rounded focus:ring-1 focus:ring-primary-500 focus:border-primary-500 text-sm">
                                </td>
                                <td class="p-2">
                                    <input type="text" name="products[<?php echo $index; ?>][base_unit]" value="<?php echo htmlspecialchars($product['base_unit'] ?? 'pcs'); ?>" placeholder="kg, liter, pcs" class="w-full px-2 py-1.5 border border-secondary-200 rounded focus:ring-1 focus:ring-primary-500 focus:border-primary-500 text-sm">
                                </td>
                                <td class="p-2">
                                    <input type="text" name="products[<?php echo $index; ?>][purchase_unit]" value="<?php echo htmlspecialchars($product['purchase_unit'] ?? ''); ?>" placeholder="বস্তা, বক্স" class="w-full px-2 py-1.5 border border-secondary-200 rounded focus:ring-1 focus:ring-primary-500 focus:border-primary-500 text-sm">
                                </td>
                                <td class="p-2 text-center">
                                    <input type="checkbox" name="products[<?php echo $index; ?>][ignore]" value="1" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="p-6 bg-secondary-50 border-t border-secondary-100 flex justify-end gap-3 sticky bottom-0">
                <a href="/sodai-dorkar/public/admin/products/bulk-import" class="px-6 py-2.5 border border-secondary-300 rounded-lg text-secondary-600 font-bold hover:bg-white transition-colors">
                    Back to Upload
                </a>
                <button type="submit" class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition-colors shadow-sm flex items-center">
                    <ion-icon name="checkmark-circle-outline" class="text-xl mr-2"></ion-icon>
                    Confirm & Import
                </button>
            </div>
        </form>
    </div>
</div>
