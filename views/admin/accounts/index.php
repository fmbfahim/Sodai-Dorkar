<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-secondary-800">Accounts & General Ledger</h2>
        <p class="text-secondary-500 text-sm">Overview of all financial transactions</p>
    </div>
</div>

<!-- Financial Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-secondary-100">
        <div class="text-xs font-bold text-secondary-500 uppercase mb-1">Total Payable (Debit)</div>
        <div class="text-2xl font-bold text-secondary-900">৳ <?php echo number_format($totalDebit, 2); ?></div>
        <p class="text-xs text-secondary-400 mt-2">Total bills recorded</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border border-secondary-100">
        <div class="text-xs font-bold text-secondary-500 uppercase mb-1">Total Paid (Credit)</div>
        <div class="text-2xl font-bold text-green-600">৳ <?php echo number_format($totalCredit, 2); ?></div>
         <p class="text-xs text-secondary-400 mt-2">Total payments made</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border border-secondary-100">
        <div class="text-xs font-bold text-secondary-500 uppercase mb-1">Outstanding Balance</div>
        <div class="text-3xl font-bold text-red-600">৳ <?php echo number_format($totalDebit - $totalCredit, 2); ?></div>
        <p class="text-xs text-secondary-400 mt-2">Current liabilities</p>
    </div>
</div>

<!-- Transaction Table -->
<div class="bg-white rounded-xl shadow-sm border border-secondary-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-secondary-100 bg-secondary-50 flex justify-between items-center">
         <h3 class="font-bold text-secondary-700">All Transactions</h3>
         <div class="flex gap-2">
             <button onclick="window.print()" class="text-xs font-bold text-secondary-500 hover:text-primary-600">
                 <ion-icon name="print-outline" class="text-lg align-middle"></ion-icon> Print Report
             </button>
         </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-secondary-50 text-secondary-500">
                <tr>
                    <th class="px-6 py-3 font-medium">Date</th>
                    <th class="px-6 py-3 font-medium">Vendor</th>
                    <th class="px-6 py-3 font-medium">Description</th>
                    <th class="px-6 py-3 font-medium">Type</th>
                    <th class="px-6 py-3 font-medium text-right">Debit</th>
                    <th class="px-6 py-3 font-medium text-right">Credit</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-secondary-100">
                <?php if(empty($transactions)): ?>
                    <tr><td colspan="6" class="px-6 py-8 text-center text-secondary-400">No transactions found</td></tr>
                <?php else: ?>
                    <?php foreach($transactions as $row): ?>
                        <tr class="hover:bg-secondary-50">
                            <td class="px-6 py-3 whitespace-nowrap text-secondary-600"><?php echo date('d M, Y', strtotime($row['transaction_date'])); ?></td>
                            <td class="px-6 py-3 font-medium text-secondary-900">
                                <a href="/sodai-dorkar/public/admin/vendors/ledger?id=<?php echo $row['vendor_id']; ?>" class="hover:underline hover:text-primary-600">
                                    <?php echo htmlspecialchars($row['vendor_name']); ?>
                                </a>
                            </td>
                            <td class="px-6 py-3 text-secondary-800"><?php echo htmlspecialchars($row['description']); ?></td>
                            <td class="px-6 py-3">
                                <span class="text-xs font-bold px-2 py-1 rounded-full
                                    <?php 
                                        if($row['type'] == 'payment') echo 'bg-green-100 text-green-700';
                                        elseif($row['type'] == 'purchase') echo 'bg-amber-100 text-amber-700';
                                        else echo 'bg-gray-100 text-gray-700';
                                    ?>
                                ">
                                    <?php echo ucfirst(str_replace('_', ' ', $row['type'])); ?>
                                </span>
                            </td>
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
