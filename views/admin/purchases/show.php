<div class="max-w-4xl mx-auto bg-white rounded-xl shadow-sm border border-secondary-100 p-8">
    <!-- Header -->
    <div class="flex justify-between items-start mb-8 border-b border-secondary-100 pb-8">
        <div>
            <h1 class="text-2xl font-bold text-secondary-900 mb-1">Purchase Invoice</h1>
            <p class="text-secondary-500">Invoice: <span class="font-mono font-medium text-secondary-900"><?php echo htmlspecialchars($purchase['invoice_no']); ?></span></p>
        </div>
        <div class="text-right">
            <div class="text-sm text-secondary-500 mb-1">Supplier</div>
            <h3 class="font-bold text-secondary-900 text-lg"><?php echo htmlspecialchars($purchase['vendor_name']); ?></h3>
            <p class="text-sm text-secondary-600"><?php echo htmlspecialchars($purchase['vendor_contact']); ?></p>
            <p class="text-sm text-secondary-600"><?php echo htmlspecialchars($purchase['vendor_address']); ?></p>
        </div>
    </div>

    <!-- Info Bar -->
    <div class="flex justify-between mb-8 text-sm">
        <div>
            <span class="text-secondary-500 block">Purchase Date</span>
            <span class="font-bold text-secondary-900"><?php echo date('F d, Y', strtotime($purchase['purchase_date'])); ?></span>
        </div>
        <!-- Could add Status here if valid -->
    </div>

    <!-- Table -->
    <table class="w-full text-left text-sm mb-8">
        <thead class="bg-secondary-50 text-secondary-600 border-b border-secondary-200">
            <tr>
                <th class="px-4 py-3 font-bold">Item / SKU</th>
                <th class="px-4 py-3 font-bold text-center">Qty</th>
                <th class="px-4 py-3 font-bold text-right">Unit Price</th>
                <th class="px-4 py-3 font-bold text-right">Total</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-secondary-100">
            <?php foreach ($purchase['items'] as $item): ?>
                <tr>
                    <td class="px-4 py-3">
                        <div class="font-medium text-secondary-900"><?php echo htmlspecialchars($item['product_name']); ?></div>
                        <div class="text-xs text-secondary-500"><?php echo htmlspecialchars($item['sku']); ?></div>
                    </td>
                    <td class="px-4 py-3 text-center"><?php echo $item['quantity']; ?></td>
                    <td class="px-4 py-3 text-right">৳ <?php echo number_format($item['unit_price'], 2); ?></td>
                    <td class="px-4 py-3 text-right font-medium">৳ <?php echo number_format($item['total_price'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot class="bg-secondary-50">
            <tr>
                <td colspan="3" class="px-4 py-3 text-right font-bold text-secondary-800">Grand Total</td>
                <td class="px-4 py-3 text-right font-bold text-primary-600 text-lg">৳ <?php echo number_format($purchase['total_amount'], 2); ?></td>
            </tr>
        </tfoot>
    </table>

    <!-- Notes -->
    <?php if(!empty($purchase['notes'])): ?>
    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-100 text-yellow-800 text-sm">
        <span class="font-bold mr-2">Notes:</span> <?php echo htmlspecialchars($purchase['notes']); ?>
    </div>
    <?php endif; ?>

    <div class="mt-8 text-center print:hidden">
        <button onclick="window.print()" class="text-secondary-500 hover:text-secondary-800 font-medium flex items-center justify-center mx-auto">
            <ion-icon name="print-outline" class="mr-2 text-lg"></ion-icon> Print Invoice
        </button>
    </div>
</div>
