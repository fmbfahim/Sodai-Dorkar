<div class="space-y-6">

    <!-- Header Navigation & Title -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="/sodai-dorkar/public/admin/delivery-men" class="w-10 h-10 rounded-2xl bg-white border border-secondary-200 text-secondary-600 hover:text-secondary-900 flex items-center justify-center shadow-2xs hover:bg-secondary-50 transition-colors">
                <ion-icon name="arrow-back" class="text-xl"></ion-icon>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-black text-secondary-900"><?php echo htmlspecialchars($rider['name']); ?></h1>
                    <span class="bg-primary-100 text-primary-800 text-xs font-black px-2.5 py-0.5 rounded-full uppercase tracking-wider">
                        Delivery Partner
                    </span>
                </div>
                <p class="text-xs text-secondary-500 mt-0.5">
                    Member since <?php echo date('d M, Y', strtotime($rider['created_at'] ?? 'now')); ?> • Username: <span class="font-mono font-bold">@<?php echo htmlspecialchars($rider['username']); ?></span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 print:hidden">
            <a href="tel:<?php echo htmlspecialchars($rider['phone'] ?? ''); ?>" class="px-3.5 py-2 bg-white border border-secondary-200 text-secondary-700 hover:text-secondary-900 font-bold text-xs rounded-xl transition-colors shadow-2xs inline-flex items-center gap-1.5">
                <ion-icon name="call-outline" class="text-sm text-primary-600"></ion-icon>
                <span>Call Rider (<?php echo htmlspecialchars($rider['phone'] ?? '-'); ?>)</span>
            </a>
            <a href="/sodai-dorkar/public/admin/delivery-men/allocation" class="px-3.5 py-2 bg-white border border-secondary-200 text-secondary-700 hover:text-secondary-900 font-bold text-xs rounded-xl transition-colors shadow-2xs inline-flex items-center gap-1.5">
                <ion-icon name="map-outline" class="text-sm text-primary-600"></ion-icon>
                <span>Edit Allocations</span>
            </a>
            <button onclick="window.print()" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-bold text-xs rounded-xl transition-colors shadow-xs inline-flex items-center gap-1.5 cursor-pointer">
                <ion-icon name="print-outline" class="text-sm"></ion-icon>
                <span>Print Report</span>
            </button>
        </div>
    </div>

    <!-- 4 KPI Performance Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Orders Assigned -->
        <div class="bg-white rounded-2xl p-5 border border-secondary-100 shadow-xs">
            <div class="flex justify-between items-start">
                <span class="text-xs text-secondary-500 font-bold uppercase tracking-wider">Total Assigned</span>
                <div class="w-9 h-9 rounded-xl bg-secondary-100 text-secondary-600 flex items-center justify-center">
                    <ion-icon name="receipt-outline" class="text-lg"></ion-icon>
                </div>
            </div>
            <div class="text-3xl font-black text-secondary-900 mt-2">
                <?php echo $stats['lifetime_orders'] ?? 0; ?>
            </div>
            <div class="text-xs text-secondary-400 mt-1">Lifetime assigned orders</div>
        </div>

        <!-- Card 2: Successfully Delivered & Success Rate -->
        <div class="bg-white rounded-2xl p-5 border border-secondary-100 shadow-xs">
            <div class="flex justify-between items-start">
                <span class="text-xs text-emerald-700 font-bold uppercase tracking-wider">Delivered</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <ion-icon name="checkmark-done" class="text-lg"></ion-icon>
                </div>
            </div>
            <div class="text-3xl font-black text-emerald-600 mt-2 flex items-baseline gap-2">
                <span><?php echo $stats['lifetime_delivered'] ?? 0; ?></span>
                <span class="text-xs font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">
                    <?php echo $stats['success_ratio'] ?? 100; ?>% Success
                </span>
            </div>
            <div class="text-xs text-secondary-500 mt-1">
                ৳ <?php echo number_format($stats['lifetime_delivered_amount'] ?? 0); ?> volume • <?php echo $stats['lifetime_cancelled'] ?? 0; ?> cancelled
            </div>
        </div>

        <!-- Card 3: Cash Collected (Today / Lifetime) -->
        <div class="bg-white rounded-2xl p-5 border border-secondary-100 shadow-xs">
            <div class="flex justify-between items-start">
                <span class="text-xs text-blue-700 font-bold uppercase tracking-wider">Cash in Hand</span>
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <ion-icon name="cash" class="text-lg"></ion-icon>
                </div>
            </div>
            <div class="text-3xl font-black text-blue-700 mt-2">
                ৳ <?php echo number_format($stats['cash_collected'] ?? 0); ?>
            </div>
            <div class="text-xs text-secondary-500 mt-1">
                Lifetime Cash: ৳ <?php echo number_format($stats['lifetime_cash'] ?? 0); ?> • Digital: ৳ <?php echo number_format($stats['lifetime_digital'] ?? 0); ?>
            </div>
        </div>

        <!-- Card 4: Active Delivery Tasks -->
        <div class="bg-white rounded-2xl p-5 border border-secondary-100 shadow-xs">
            <div class="flex justify-between items-start">
                <span class="text-xs text-amber-700 font-bold uppercase tracking-wider">Active Tasks</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <ion-icon name="bicycle" class="text-lg"></ion-icon>
                </div>
            </div>
            <div class="text-3xl font-black text-amber-600 mt-2 flex items-center gap-2">
                <span><?php echo count($activeOrders ?? []); ?></span>
                <?php if (($stats['out_for_delivery'] ?? 0) > 0): ?>
                    <span class="text-xs font-bold text-amber-800 bg-amber-100 px-2 py-0.5 rounded-full flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                        <?php echo $stats['out_for_delivery']; ?> On Way
                    </span>
                <?php endif; ?>
            </div>
            <div class="text-xs text-secondary-400 mt-1">Orders currently assigned to deliver</div>
        </div>
    </div>

    <!-- Assigned Unions / Coverage Schedule -->
    <div class="bg-white rounded-2xl p-5 border border-secondary-100 shadow-xs">
        <div class="flex items-center justify-between mb-3 pb-2 border-b border-secondary-100">
            <div class="flex items-center gap-2">
                <ion-icon name="map-outline" class="text-xl text-primary-600"></ion-icon>
                <h3 class="font-bold text-secondary-900 text-sm">Assigned Delivery Unions (Coverage Areas)</h3>
            </div>
            <a href="/sodai-dorkar/public/admin/delivery-men/allocation" class="text-xs font-bold text-primary-600 hover:text-primary-700 flex items-center gap-1">
                <ion-icon name="add-circle-outline"></ion-icon>
                <span>Add / Modify Allocation</span>
            </a>
        </div>

        <?php if (empty($allocations)): ?>
            <div class="p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl text-xs flex items-center gap-2">
                <ion-icon name="alert-circle" class="text-lg text-amber-600 shrink-0"></ion-icon>
                <span>This rider has not been allocated to any union/area yet. Orders in customer areas won't be auto-assigned to them.</span>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                <?php foreach ($allocations as $alloc): ?>
                    <div class="p-3 bg-secondary-50/70 border border-secondary-200 rounded-xl flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-primary-100 text-primary-700 flex items-center justify-center font-bold text-xs">
                                <ion-icon name="navigate"></ion-icon>
                            </div>
                            <div>
                                <div class="font-bold text-secondary-900 text-xs"><?php echo htmlspecialchars($alloc['area_name']); ?></div>
                                <div class="text-[10px] text-secondary-400 capitalize">Slot: <?php echo htmlspecialchars($alloc['time_slot']); ?></div>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full border border-emerald-200">
                            Active
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Active Tasks Queue -->
    <div class="bg-white rounded-2xl shadow-xs border border-secondary-100 overflow-hidden">
        <div class="p-5 border-b border-secondary-100 flex items-center justify-between bg-secondary-50/40">
            <div class="flex items-center gap-2">
                <ion-icon name="bicycle-outline" class="text-xl text-amber-600"></ion-icon>
                <h3 class="font-bold text-secondary-900 text-sm">Active Orders Queue (In Progress)</h3>
            </div>
            <span class="text-xs font-bold text-secondary-500 bg-secondary-100 px-2 py-0.5 rounded-full">
                <?php echo count($activeOrders ?? []); ?> Tasks
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-secondary-600">
                <thead class="bg-secondary-50 text-secondary-500 uppercase tracking-wider font-bold border-b border-secondary-100">
                    <tr>
                        <th class="px-5 py-3">Order #</th>
                        <th class="px-4 py-3">Customer</th>
                        <th class="px-4 py-3">Delivery Address</th>
                        <th class="px-4 py-3 text-right">Amount</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-5 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-secondary-100">
                    <?php if (empty($activeOrders)): ?>
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-secondary-400 italic">
                                No active delivery orders currently pending for this rider.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($activeOrders as $ao): ?>
                            <?php 
                                $isOut = ($ao['status'] === 'out_for_delivery');
                            ?>
                            <tr class="hover:bg-secondary-50/70 transition-colors">
                                <td class="px-5 py-3.5 font-bold font-mono text-secondary-900">
                                    #<?php echo str_pad($ao['id'], 6, '0', STR_PAD_LEFT); ?>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="font-bold text-secondary-900"><?php echo htmlspecialchars($ao['customer_name']); ?></div>
                                    <div class="text-[11px] text-secondary-400 font-mono"><?php echo htmlspecialchars($ao['customer_phone']); ?></div>
                                </td>
                                <td class="px-4 py-3.5 max-w-[240px]">
                                    <div class="truncate text-secondary-700"><?php echo htmlspecialchars($ao['delivery_address'] ?? $ao['address_details'] ?? '-'); ?></div>
                                </td>
                                <td class="px-4 py-3.5 text-right font-mono font-bold text-secondary-900">
                                    ৳ <?php echo number_format($ao['total_amount'], 2); ?>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <?php if ($isOut): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 border border-amber-300">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> On The Way
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-black bg-blue-100 text-blue-800 border border-blue-300">
                                            Processing
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <a href="/sodai-dorkar/public/admin/orders/show?id=<?php echo $ao['id']; ?>" class="px-2.5 py-1 text-primary-600 hover:text-primary-800 bg-primary-50 hover:bg-primary-100 rounded-lg font-bold text-xs transition-colors">
                                        View Order &rarr;
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Completed Deliveries & History Log -->
    <div class="bg-white rounded-2xl shadow-xs border border-secondary-100 overflow-hidden">
        <div class="p-5 border-b border-secondary-100 flex items-center justify-between bg-secondary-50/40">
            <div class="flex items-center gap-2">
                <ion-icon name="time-outline" class="text-xl text-primary-600"></ion-icon>
                <h3 class="font-bold text-secondary-900 text-sm">Delivery History & Completed Tasks</h3>
            </div>
            <span class="text-xs font-bold text-secondary-400">Recent completed & cancelled tasks</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-secondary-600">
                <thead class="bg-secondary-50 text-secondary-500 uppercase tracking-wider font-bold border-b border-secondary-100">
                    <tr>
                        <th class="px-5 py-3">Order #</th>
                        <th class="px-4 py-3">Customer</th>
                        <th class="px-4 py-3">Union / Address</th>
                        <th class="px-4 py-3 text-right">Amount</th>
                        <th class="px-4 py-3 text-center">Payment</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-5 py-3 text-right">Date & Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-secondary-100">
                    <?php if (empty($history)): ?>
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-secondary-400 italic">
                                No past delivery records found for this rider.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($history as $h): ?>
                            <?php 
                                $isDelivered = ($h['status'] === 'delivered');
                            ?>
                            <tr class="hover:bg-secondary-50/70 transition-colors">
                                <td class="px-5 py-3 font-bold font-mono text-secondary-900">
                                    <a href="/sodai-dorkar/public/admin/orders/show?id=<?php echo $h['id']; ?>" class="hover:text-primary-600 underline">
                                        #<?php echo str_pad($h['id'], 6, '0', STR_PAD_LEFT); ?>
                                    </a>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-secondary-900"><?php echo htmlspecialchars($h['customer_name']); ?></div>
                                    <div class="text-[11px] text-secondary-400 font-mono"><?php echo htmlspecialchars($h['customer_phone']); ?></div>
                                </td>
                                <td class="px-4 py-3 max-w-[200px]">
                                    <div class="font-bold text-secondary-800 text-[11px]"><?php echo htmlspecialchars($h['area_name'] ?? ''); ?></div>
                                    <div class="truncate text-secondary-500 text-[10px]"><?php echo htmlspecialchars($h['delivery_address'] ?? $h['address_details'] ?? '-'); ?></div>
                                </td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-secondary-900">
                                    ৳ <?php echo number_format($h['total_amount'], 2); ?>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="uppercase font-bold text-[10px] bg-secondary-100 text-secondary-700 px-2 py-0.5 rounded">
                                        <?php echo htmlspecialchars($h['payment_method'] ?: 'CASH'); ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <?php if ($isDelivered): ?>
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            Delivered
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-black bg-red-100 text-red-800 border border-red-200">
                                            Cancelled
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-3 text-right text-secondary-500 text-[11px]">
                                    <?php echo date('d M, Y', strtotime($h['updated_at'])); ?> <br>
                                    <span class="text-secondary-400"><?php echo date('h:i A', strtotime($h['updated_at'])); ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
