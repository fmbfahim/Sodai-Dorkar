<div class="max-w-5xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-secondary-800">Order History</h2>
            <div class="flex items-center text-secondary-500 mt-1">
                 <ion-icon name="person-outline" class="mr-2"></ion-icon>
                 <span class="font-medium mr-2"><?php echo htmlspecialchars($customer['name']); ?></span>
                 <span class="text-secondary-300 mx-2">|</span>
                 <ion-icon name="call-outline" class="mr-2"></ion-icon>
                 <span><?php echo htmlspecialchars($customer['phone']); ?></span>
            </div>
        </div>
        <a href="/sodai-dorkar/public/admin/customers" class="text-secondary-600 hover:text-primary-600 flex items-center transition-colors">
            <ion-icon name="arrow-back-outline" class="mr-2 text-xl"></ion-icon>
            Back to List
        </a>
    </div>

    <!-- Stats Overview (Optional helper) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-secondary-100 flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                <ion-icon name="cart-outline" class="text-2xl"></ion-icon>
            </div>
            <div>
                <p class="text-sm text-secondary-500 font-medium">Total Orders</p>
                <p class="text-2xl font-bold text-secondary-800"><?php echo count($orders); ?></p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-secondary-100 flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                <ion-icon name="cash-outline" class="text-2xl"></ion-icon>
            </div>
            <div>
                <p class="text-sm text-secondary-500 font-medium">Total Spent</p>
                <?php 
                    $totalSpent = array_reduce($orders, function($sum, $order) {
                        return $sum + ($order['status'] != 'cancelled' ? $order['total_amount'] : 0);
                    }, 0);
                ?>
                <p class="text-2xl font-bold text-secondary-800">৳<?php echo number_format($totalSpent); ?></p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-secondary-100 flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                <ion-icon name="time-outline" class="text-2xl"></ion-icon>
            </div>
            <div>
                <p class="text-sm text-secondary-500 font-medium">Last Order</p>
                <p class="text-lg font-bold text-secondary-800">
                    <?php echo !empty($orders) ? date('M j, Y', strtotime($orders[0]['created_at'])) : 'N/A'; ?>
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-secondary-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-secondary-600">
                <thead class="bg-secondary-50 text-secondary-500">
                    <tr>
                        <th class="px-6 py-3 font-medium">Order ID</th>
                        <th class="px-6 py-3 font-medium">Date</th>
                        <th class="px-6 py-3 font-medium">Amount</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium">Delivery Man</th>
                        <th class="px-6 py-3 font-medium text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-secondary-100">
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-secondary-400">
                                    <ion-icon name="bag-handle-outline" class="text-4xl mb-2"></ion-icon>
                                    <p>No orders found for this customer.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr class="hover:bg-secondary-50 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="font-mono font-bold text-secondary-900">#<?php echo $order['id']; ?></span>
                                </td>
                                <td class="px-6 py-4">
                                    <?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?>
                                </td>
                                <td class="px-6 py-4 font-bold text-secondary-900">
                                    ৳<?php echo number_format($order['total_amount']); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'processing' => 'bg-blue-100 text-blue-800',
                                            'packed' => 'bg-indigo-100 text-indigo-800',
                                            'out_for_delivery' => 'bg-purple-100 text-purple-800',
                                            'delivered' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                        ];
                                        $colorClass = $statusColors[$order['status']] ?? 'bg-gray-100 text-gray-800';
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize <?php echo $colorClass; ?>">
                                        <?php echo str_replace('_', ' ', $order['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs">
                                    <?php if ($order['delivery_man_name']): ?>
                                        <div class="flex items-center text-secondary-700">
                                            <ion-icon name="bicycle-outline" class="mr-1"></ion-icon>
                                            <?php echo htmlspecialchars($order['delivery_man_name']); ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-secondary-400 italic">Unassigned</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="/sodai-dorkar/public/admin/orders/show?id=<?php echo $order['id']; ?>" class="text-blue-500 hover:text-blue-700 p-1 inline-block" title="View Invoice">
                                        <ion-icon name="document-text-outline" class="text-lg"></ion-icon>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
