<div class="mb-6 flex justify-between items-center">
    <h3 class="text-lg font-bold text-secondary-800">Purchase History</h3>
    <a href="/sodai-dorkar/public/admin/purchases/create" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors">
        <ion-icon name="cart-outline" class="mr-2"></ion-icon>
        New Purchase
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-secondary-100 overflow-hidden">
    <table class="w-full text-left text-sm text-secondary-600">
        <thead class="bg-secondary-50 text-secondary-500">
            <tr>
                <th class="px-6 py-3 font-medium">Date</th>
                <th class="px-6 py-3 font-medium">Invoice #</th>
                <th class="px-6 py-3 font-medium">Supplier</th>
                <th class="px-6 py-3 font-medium">Amount</th>
                <th class="px-6 py-3 font-medium text-right">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-secondary-100">
            <?php foreach ($purchases as $p): ?>
                <tr class="hover:bg-secondary-50">
                    <td class="px-6 py-4"><?php echo date('M d, Y', strtotime($p['purchase_date'])); ?></td>
                    <td class="px-6 py-4 font-mono font-medium text-secondary-900"><?php echo htmlspecialchars($p['invoice_no']); ?></td>
                    <td class="px-6 py-4"><?php echo htmlspecialchars($p['vendor_name']); ?></td>
                    <td class="px-6 py-4 font-bold text-secondary-900">৳ <?php echo number_format($p['total_amount'], 2); ?></td>
                    <td class="px-6 py-4 text-right">
                        <a href="/sodai-dorkar/public/admin/purchases/show?id=<?php echo $p['id']; ?>" class="text-primary-600 hover:text-primary-800 font-medium">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if(empty($purchases)): ?>
                <tr><td colspan="5" class="px-6 py-8 text-center text-secondary-400">No purchases found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
