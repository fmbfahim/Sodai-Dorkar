<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Sales Card -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-secondary-100 flex items-center">
        <div class="p-3 rounded-full bg-primary-100 text-primary-600 mr-4">
            <ion-icon name="wallet" class="text-2xl"></ion-icon>
        </div>
        <div>
            <p class="text-sm text-secondary-500 font-medium">Sales Today</p>
            <h3 class="text-2xl font-bold text-secondary-800">৳ <?php echo number_format($stats['sales_today']); ?></h3>
        </div>
    </div>

    <!-- Orders Card -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-secondary-100 flex items-center">
        <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
            <ion-icon name="cart" class="text-2xl"></ion-icon>
        </div>
        <div>
            <p class="text-sm text-secondary-500 font-medium">Orders Today</p>
            <h3 class="text-2xl font-bold text-secondary-800"><?php echo $stats['orders_today']; ?></h3>
        </div>
    </div>

    <!-- Pending Card -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-secondary-100 flex items-center">
        <div class="p-3 rounded-full bg-amber-100 text-amber-600 mr-4">
            <ion-icon name="time" class="text-2xl"></ion-icon>
        </div>
        <div>
            <p class="text-sm text-secondary-500 font-medium">Pending Orders</p>
            <h3 class="text-2xl font-bold text-secondary-800"><?php echo $stats['pending_orders']; ?></h3>
        </div>
    </div>

    <!-- Customers Card -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-secondary-100 flex items-center">
        <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
            <ion-icon name="people" class="text-2xl"></ion-icon>
        </div>
        <div>
            <p class="text-sm text-secondary-500 font-medium">Total Customers</p>
            <h3 class="text-2xl font-bold text-secondary-800"><?php echo $stats['customers_total']; ?></h3>
        </div>
    </div>
</div>

<!-- Recent Orders & Shortcuts -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Recent Orders -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-secondary-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-secondary-100 flex justify-between items-center bg-secondary-50">
            <h3 class="font-bold text-secondary-700">Recent Orders</h3>
            <a href="/sodai-dorkar/public/admin/orders" class="text-xs text-primary-600 font-bold hover:underline">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-secondary-50 text-secondary-500 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 font-semibold">Order ID</th>
                        <th class="px-6 py-3 font-semibold">Customer</th>
                        <th class="px-6 py-3 font-semibold">Status</th>
                        <th class="px-6 py-3 font-semibold text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-secondary-100">
                    <?php if(empty($recentOrders)): ?>
                        <tr><td colspan="4" class="p-6 text-center text-secondary-400">No recent orders</td></tr>
                    <?php else: ?>
                        <?php foreach($recentOrders as $order): ?>
                        <tr class="hover:bg-secondary-50 transition-colors">
                            <td class="px-6 py-3 font-medium text-secondary-800">#<?php echo $order['id']; ?></td>
                            <td class="px-6 py-3 text-secondary-600"><?php echo htmlspecialchars($order['customer_name']); ?></td>
                            <td class="px-6 py-3">
                                <?php 
                                    $st = !empty($order['status']) ? strtolower(trim($order['status'])) : 'pending';
                                    $statusBadge = match($st) {
                                        'pending' => ['bg' => 'bg-amber-100 text-amber-800 border-amber-200', 'label' => 'Pending'],
                                        'processing' => ['bg' => 'bg-blue-100 text-blue-800 border-blue-200', 'label' => 'Processing'],
                                        'packed' => ['bg' => 'bg-indigo-100 text-indigo-800 border-indigo-200', 'label' => 'Packed'],
                                        'shipped' => ['bg' => 'bg-cyan-100 text-cyan-800 border-cyan-200', 'label' => 'Shipped'],
                                        'out_for_delivery' => ['bg' => 'bg-purple-100 text-purple-800 border-purple-200', 'label' => 'Out for Delivery'],
                                        'delivered' => ['bg' => 'bg-emerald-100 text-emerald-800 border-emerald-200', 'label' => 'Delivered'],
                                        'cancelled' => ['bg' => 'bg-rose-100 text-rose-800 border-rose-200', 'label' => 'Cancelled'],
                                        default => ['bg' => 'bg-secondary-100 text-secondary-800 border-secondary-200', 'label' => ucfirst(str_replace('_', ' ', $st ?: 'pending'))]
                                    };
                                ?>
                                <span class="px-2.5 py-1 text-xs rounded-full font-bold border <?php echo $statusBadge['bg']; ?>">
                                    <?php echo $statusBadge['label']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right font-bold text-secondary-800">৳ <?php echo number_format($order['total_amount']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-secondary-100 p-6">
        <h3 class="font-bold text-secondary-700 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 gap-3">
            <a href="/sodai-dorkar/public/admin/orders/create" class="flex flex-col items-center justify-center p-4 bg-primary-50 rounded-xl hover:bg-primary-100 transition-colors text-primary-700 border border-primary-100">
                <ion-icon name="add-circle" class="text-3xl mb-2"></ion-icon>
                <span class="text-xs font-bold">New Order</span>
            </a>
            <a href="/sodai-dorkar/public/admin/dispatch" class="flex flex-col items-center justify-center p-4 bg-secondary-50 rounded-xl hover:bg-secondary-100 transition-colors text-secondary-700 border border-secondary-100">
                <ion-icon name="bicycle" class="text-3xl mb-2"></ion-icon>
                <span class="text-xs font-bold">Dispatch</span>
            </a>
            <a href="/sodai-dorkar/public/admin/customers/create" class="flex flex-col items-center justify-center p-4 bg-secondary-50 rounded-xl hover:bg-secondary-100 transition-colors text-secondary-700 border border-secondary-100">
                <ion-icon name="person-add" class="text-3xl mb-2"></ion-icon>
                <span class="text-xs font-bold">Add User</span>
            </a>
            <a href="/sodai-dorkar/public/admin/reports/sales" class="flex flex-col items-center justify-center p-4 bg-secondary-50 rounded-xl hover:bg-secondary-100 transition-colors text-secondary-700 border border-secondary-100">
                <ion-icon name="stats-chart" class="text-3xl mb-2"></ion-icon>
                <span class="text-xs font-bold">Reports</span>
            </a>
        </div>
    </div>
</div>
