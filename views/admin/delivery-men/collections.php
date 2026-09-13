<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-secondary-900 flex items-center gap-2">
                <ion-icon name="receipt" class="text-primary-600"></ion-icon>
                Deposit History
            </h1>
            <p class="text-sm text-secondary-500 mt-1">Log of all cash deposits received from delivery staff.</p>
        </div>
        <div>
            <a href="/sodai-dorkar/public/admin/delivery-men" class="px-4 py-2 bg-white border border-secondary-200 hover:border-secondary-300 text-secondary-700 font-bold rounded-xl shadow-2xs inline-flex items-center gap-2 transition-all">
                <ion-icon name="arrow-back"></ion-icon> Back to Delivery Staff
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xs border border-secondary-100 overflow-hidden">
        <div class="p-5 border-b border-secondary-100 bg-secondary-50/50">
            <h3 class="font-bold text-secondary-900 text-base">All Transactions</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-secondary-600">
                <thead class="bg-secondary-50 text-secondary-500 uppercase tracking-wider font-bold text-xs border-b border-secondary-100">
                    <tr>
                        <th class="px-5 py-3.5">Date & Time</th>
                        <th class="px-5 py-3.5">Delivery Rider</th>
                        <th class="px-5 py-3.5">Amount</th>
                        <th class="px-5 py-3.5">Note</th>
                        <th class="px-5 py-3.5">Received By (Admin)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-secondary-100">
                    <?php if (empty($collections)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-secondary-400">
                                <ion-icon name="wallet-outline" class="text-4xl text-secondary-300 mb-2"></ion-icon>
                                <p class="font-medium">No deposit history found.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($collections as $col): ?>
                            <tr class="hover:bg-secondary-50/50 transition-colors">
                                <td class="px-5 py-3.5">
                                    <div class="font-bold text-secondary-800"><?php echo date('d M Y', strtotime($col['created_at'])); ?></div>
                                    <div class="text-xs text-secondary-500"><?php echo date('h:i A', strtotime($col['created_at'])); ?></div>
                                </td>
                                <td class="px-5 py-3.5 font-bold text-primary-700">
                                    <?php echo htmlspecialchars($col['rider_name']); ?>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="inline-block px-2.5 py-1 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 font-black">
                                        ৳ <?php echo number_format($col['amount'], 2); ?>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <?php if (!empty($col['note'])): ?>
                                        <div class="text-secondary-600 italic">"<?php echo htmlspecialchars($col['note']); ?>"</div>
                                    <?php else: ?>
                                        <span class="text-secondary-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-3.5 text-secondary-500 text-xs">
                                    <?php echo htmlspecialchars($col['admin_name'] ?? 'System'); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
