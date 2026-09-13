<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-secondary-800">Stock Alert</h2>
            <p class="text-sm text-secondary-500">Products with stock level below 5.</p>
        </div>
        <button onclick="window.print()" class="bg-white border border-secondary-300 text-secondary-700 px-4 py-2 rounded-lg text-sm font-bold hover:bg-secondary-50">
            <ion-icon name="print-outline" class="mr-2"></ion-icon> Print List
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-secondary-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-red-50 text-red-700 text-xs uppercase font-semibold">
                <tr>
                    <th class="p-4">Product Name</th>
                    <th class="p-4">Category</th>
                    <th class="p-4 text-center">Current Stock</th>
                    <th class="p-4 text-right">Price</th>
                    <th class="p-4 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-secondary-100 text-sm">
                <?php if(empty($products)): ?>
                <tr>
                    <td colspan="5" class="p-8 text-center text-secondary-400">
                        <div class="flex flex-col items-center">
                            <ion-icon name="checkmark-circle" class="text-4xl text-green-500 mb-2"></ion-icon>
                            <p>Good news! No products are low on stock.</p>
                        </div>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach($products as $p): ?>
                    <?php 
                        $stockVal = floatval($p['stock_qty'] ?? 0);
                        $stockClean = (floor($stockVal) == $stockVal) ? intval($stockVal) : rtrim(rtrim(number_format($stockVal, 3), '0'), '.');
                        $unit = $p['base_unit'] ?? 'pcs';
                        $price = floatval($p['sell_price'] ?? 0);
                    ?>
                    <tr class="hover:bg-red-50 transition-colors">
                        <td class="p-4 font-bold text-secondary-800"><?php echo htmlspecialchars($p['name']); ?></td>
                        <td class="p-4 text-secondary-600"><?php echo htmlspecialchars($p['category_name'] ?? '-'); ?></td>
                        <td class="p-4 text-center">
                            <span class="inline-block bg-red-100 text-red-700 px-3 py-1 rounded-full font-bold text-xs">
                                <?php echo $stockClean . ' ' . htmlspecialchars($unit); ?>
                            </span>
                        </td>
                        <td class="p-4 text-right font-mono">৳ <?php echo number_format($price, 2); ?></td>
                        <td class="p-4 text-center">
                            <a href="/sodai-dorkar/public/admin/products/edit?id=<?php echo $p['id']; ?>" class="text-primary-600 hover:text-primary-800 font-bold text-xs border border-primary-200 px-2 py-1 rounded">
                                Update Stock
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
