<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-secondary-800"><?php echo htmlspecialchars($vendor['name']); ?></h2>
        <p class="text-secondary-500 text-sm"><?php echo htmlspecialchars($vendor['contact']); ?> | <?php echo htmlspecialchars($vendor['address']); ?></p>
    </div>
    <a href="/sodai-dorkar/public/admin/vendors" class="text-secondary-600 hover:text-secondary-800 font-medium flex items-center">
        <ion-icon name="arrow-back" class="mr-1"></ion-icon> Back
    </a>
</div>

<!-- Balance Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-secondary-100">
        <div class="text-xs font-bold text-secondary-500 uppercase mb-1">Total Billed</div>
        <div class="text-2xl font-bold text-secondary-900">৳ <?php echo number_format($balance['bill'] + $balance['opening']); ?></div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border border-secondary-100">
        <div class="text-xs font-bold text-secondary-500 uppercase mb-1">Total Paid</div>
        <div class="text-2xl font-bold text-green-600">৳ <?php echo number_format($balance['paid']); ?></div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border border-secondary-100">
        <div class="text-xs font-bold text-secondary-500 uppercase mb-1">Due Amount</div>
        <div class="text-3xl font-bold text-red-600">৳ <?php echo number_format($balance['due']); ?></div>
    </div>
</div>

<!-- Payment Form -->
<div class="bg-white p-6 rounded-xl shadow-sm border border-secondary-100 mb-8">
    <h3 class="text-lg font-bold text-secondary-800 mb-4 border-b border-secondary-100 pb-2">Record Payment</h3>
    <form action="/sodai-dorkar/public/admin/vendors/payment" method="POST" class="flex flex-wrap gap-4 items-end">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
        <input type="hidden" name="vendor_id" value="<?php echo $vendor['id']; ?>">
        
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-bold text-secondary-600 uppercase mb-1">Date</label>
            <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
        
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-bold text-secondary-600 uppercase mb-1">Amount (৳)</label>
            <input type="number" name="amount" placeholder="0.00" step="0.01" required class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
        
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-bold text-secondary-600 uppercase mb-1">Method</label>
            <select name="method" class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="Cash">Cash</option>
                <option value="Bank Transfer">Bank Transfer</option>
                <option value="Cheque">Cheque</option>
                <option value="Bkash">Bkash</option>
                <option value="Nagad">Nagad</option>
                <option value="Rocket">Rocket</option>
            </select>
        </div>

        <div class="flex-[2] min-w-[300px]">
            <label class="block text-xs font-bold text-secondary-600 uppercase mb-1">Transaction ID / Note</label>
            <input type="text" name="note" placeholder="e.g. TrxID: ABC12345..." class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
        
        <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-6 rounded-lg transition-colors">
            Save Payment
        </button>
    </form>
</div>

<!-- Transaction History -->
<div class="bg-white rounded-xl shadow-sm border border-secondary-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-secondary-100 bg-secondary-50">
         <h3 class="font-bold text-secondary-700">Transaction History</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-secondary-50 text-secondary-500">
                <tr>
                    <th class="px-6 py-3 font-medium">Date</th>
                    <th class="px-6 py-3 font-medium">Description</th>
                    <th class="px-6 py-3 font-medium text-right">Debit (Bill)</th>
                    <th class="px-6 py-3 font-medium text-right">Credit (Paid)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-secondary-100">
                <?php if(empty($ledger)): ?>
                    <tr><td colspan="4" class="px-6 py-8 text-center text-secondary-400">No transactions found</td></tr>
                <?php else: ?>
                    <?php foreach($ledger as $row): ?>
                        <tr class="hover:bg-secondary-50">
                            <td class="px-6 py-3 whitespace-nowrap text-secondary-600"><?php echo date('d M, Y', strtotime($row['transaction_date'])); ?></td>
                            <td class="px-6 py-3 text-secondary-800 font-medium"><?php echo htmlspecialchars($row['description']); ?></td>
                            <td class="px-6 py-3 text-right">
                                <?php if($row['type'] == 'purchase' || $row['type'] == 'opening_balance'): ?>
                                    <span class="font-bold text-secondary-800">৳ <?php echo number_format($row['amount'], 2); ?></span>
                                <?php else: ?>
                                    <span class="text-secondary-300">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <?php if($row['type'] == 'payment'): ?>
                                    <span class="font-bold text-green-600">৳ <?php echo number_format($row['amount'], 2); ?></span>
                                <?php else: ?>
                                    <span class="text-secondary-300">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
