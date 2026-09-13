<div class="max-w-7xl mx-auto mb-16 px-4 pt-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-secondary-900">Procurement / Sourcing List</h2>
            <p class="text-secondary-500 text-xs mt-1">Products ordered but currently out of stock, grouped by vendor.</p>
        </div>
        <div>
            <button onclick="window.print()" class="bg-primary-50 text-primary-700 hover:bg-primary-100 font-bold py-2 px-4 rounded-xl flex items-center gap-2 transition-colors">
                <ion-icon name="print-outline"></ion-icon> Print List
            </button>
        </div>
    </div>

    <?php if (empty($groupedItems)): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200 p-12 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-50 text-emerald-500 mb-4">
                <ion-icon name="checkmark-circle-outline" class="text-3xl"></ion-icon>
            </div>
            <h3 class="text-lg font-bold text-secondary-900 mb-2">All Caught Up!</h3>
            <p class="text-secondary-500">There are no pending out-of-stock items that need sourcing right now.</p>
        </div>
    <?php else: ?>
        <div class="space-y-6">
            <?php foreach ($groupedItems as $vendorId => $vendorData): ?>
                <div class="bg-white rounded-2xl shadow-sm border border-secondary-200 overflow-hidden break-inside-avoid">
                    <!-- Vendor Header -->
                    <div class="bg-secondary-50 border-b border-secondary-200 px-5 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center border border-secondary-200 text-secondary-600 shadow-sm">
                                <ion-icon name="storefront" class="text-xl"></ion-icon>
                            </div>
                            <div>
                                <h3 class="font-bold text-secondary-900 text-lg leading-tight">
                                    <?= htmlspecialchars($vendorData['vendor_name']) ?>
                                </h3>
                                <?php if ($vendorData['vendor_phone']): ?>
                                    <p class="text-xs text-secondary-500 flex items-center gap-1 mt-0.5">
                                        <ion-icon name="call-outline"></ion-icon> <?= htmlspecialchars($vendorData['vendor_phone']) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Products Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead class="text-[11px] text-secondary-500 uppercase bg-secondary-50/50">
                                <tr>
                                    <th class="px-5 py-3 font-semibold">Product</th>
                                    <th class="px-5 py-3 font-semibold text-center">Qty Needed</th>
                                    <th class="px-5 py-3 font-semibold">Est. Buy Price</th>
                                    <th class="px-5 py-3 font-semibold text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-secondary-100">
                                <?php foreach ($vendorData['products'] as $item): ?>
                                    <tr class="hover:bg-secondary-50/50 transition-colors">
                                        <td class="px-5 py-3">
                                            <div class="flex items-center gap-3">
                                                <img src="<?= !empty($item['image_path']) ? htmlspecialchars($item['image_path']) : '/sodai-dorkar/public/images/default-product.svg' ?>" 
                                                     class="w-10 h-10 object-contain rounded-lg border border-secondary-200 bg-white" 
                                                     onerror="this.src='/sodai-dorkar/public/images/default-product.svg'">
                                                <div>
                                                    <div class="font-bold text-secondary-900"><?= htmlspecialchars($item['product_name']) ?></div>
                                                    <div class="text-xs text-secondary-500">
                                                        <?= htmlspecialchars($item['unit_title'] ?: 'Default Unit') ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-3 text-center">
                                            <span class="inline-flex items-center justify-center px-3 py-1 rounded-lg bg-amber-50 text-amber-700 font-bold border border-amber-200 text-base">
                                                <?= intval($item['total_needed']) ?>
                                            </span>
                                        </td>
                                        <td class="px-5 py-3 font-medium text-secondary-700">
                                            ৳ <?= number_format($item['buy_price'] * $item['total_needed'], 2) ?>
                                            <span class="text-[10px] text-secondary-400 block">(৳ <?= number_format($item['buy_price'], 2) ?>/unit)</span>
                                        </td>
                                        <td class="px-5 py-3 text-right">
                                            <a href="/sodai-dorkar/public/admin/purchases/create?product_id=<?= $item['product_id'] ?>" 
                                               class="inline-flex items-center gap-1.5 text-xs font-bold text-primary-600 hover:text-primary-800 bg-primary-50 hover:bg-primary-100 px-3 py-1.5 rounded-lg transition-colors">
                                                <ion-icon name="cart-outline"></ion-icon> Buy Stock
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
@media print {
    body { background-color: white !important; }
    aside, header, button { display: none !important; }
    main { margin: 0 !important; max-width: 100% !important; padding: 0 !important; }
    .shadow-sm { box-shadow: none !important; border-color: #000 !important; }
    .break-inside-avoid { break-inside: avoid; margin-bottom: 20px; }
}
</style>
