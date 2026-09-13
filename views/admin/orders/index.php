<div class="mb-6 flex justify-between items-center">
    <h3 class="text-lg font-bold text-secondary-800">Order Management</h3>
    <a href="/sodai-dorkar/public/admin/orders/create" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors">
        <ion-icon name="cart-outline" class="mr-2"></ion-icon>
        New Order
    </a>
</div>

<!-- Status Filters -->
<div class="mb-6 flex flex-wrap gap-2">
    <?php
    $statuses = [
        'all' => 'All Orders',
        'pending' => 'Pending',
        'processing' => 'Processing',
        'packaging' => 'Packaging',
        'dispatch' => 'Dispatch',
        'out_for_delivery' => 'Out for Delivery',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled'
    ];
    $currentStatus = $currentStatus ?? 'all';
    foreach ($statuses as $val => $label):
        $isActive = ($currentStatus === $val);
        $btnClass = $isActive 
            ? 'bg-primary-600 text-white font-bold shadow-sm' 
            : 'bg-white text-secondary-600 border border-secondary-200 hover:bg-secondary-50 hover:text-primary-600';
    ?>
        <a href="?status=<?= $val ?>" class="px-4 py-2 text-sm rounded-lg transition-colors <?= $btnClass ?>">
            <?= $label ?>
        </a>
    <?php endforeach; ?>
</div>

<div class="bg-white rounded-xl shadow-sm border border-secondary-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-secondary-600">
            <thead class="bg-secondary-50 text-secondary-500">
                <tr>
                    <th class="px-6 py-3 font-medium">Order ID</th>
                    <th class="px-6 py-3 font-medium">Customer</th>
                    <th class="px-6 py-3 font-medium">Agent</th>
                    <th class="px-6 py-3 font-medium">Amount</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium">Date</th>
                    <th class="px-6 py-3 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-secondary-100">
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-secondary-400">
                            No orders found.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $o): ?>
                        <tr class="hover:bg-secondary-50">
                            <td class="px-6 py-4 font-bold text-primary-600 hover:text-primary-800">
                                <a href="/sodai-dorkar/public/admin/orders/show?id=<?php echo $o['id']; ?>">#<?php echo $o['id']; ?></a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-primary-600 hover:text-primary-800">
                                    <a href="/sodai-dorkar/public/admin/orders/show?id=<?php echo $o['id']; ?>"><?php echo htmlspecialchars($o['customer_name']); ?></a>
                                </div>
                                <div class="text-xs text-secondary-500"><?php echo htmlspecialchars($o['customer_phone']); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <?php if($o['delivery_man_name']): ?>
                                    <div class="flex items-center text-primary-700 bg-primary-50 px-2 py-1 rounded text-xs font-bold">
                                        <ion-icon name="bicycle-outline" class="mr-1"></ion-icon>
                                        <?php echo htmlspecialchars($o['delivery_man_name']); ?>
                                    </div>
                                <?php else: ?>
                                    <form action="/sodai-dorkar/public/admin/orders/assign" method="POST" class="flex items-center">
                                        <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                        <select name="delivery_man_id" class="text-xs border border-secondary-300 rounded px-2 py-1 mr-1 focus:outline-none focus:border-primary-500 w-32">
                                            <option value="">Assign DM</option>
                                            <?php foreach ($deliveryMen as $dm): ?>
                                                <option value="<?php echo $dm['id']; ?>"><?php echo htmlspecialchars($dm['name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" class="text-primary-600 hover:text-primary-800 p-1">
                                            <ion-icon name="arrow-forward-circle-outline" class="text-lg"></ion-icon>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 font-mono font-medium text-secondary-900">
                                <?php if (!empty($o['delivery_discount']) && floatval($o['delivery_discount']) > 0): ?>
                                    <div class="line-through text-xs text-secondary-400">৳ <?php echo number_format($o['original_amount'] ?? ($o['total_amount'] + $o['delivery_discount'])); ?></div>
                                    <div class="font-bold text-emerald-700">৳ <?php echo number_format($o['total_amount']); ?></div>
                                    <span class="inline-block text-[9px] font-bold text-red-600 bg-red-50 border border-red-200 px-1 py-0.2 rounded">
                                        -৳<?php echo number_format($o['delivery_discount']); ?> (<?php echo htmlspecialchars(ucfirst($o['amount_changed_by'] ?? 'staff')); ?>)
                                    </span>
                                <?php else: ?>
                                    ৳ <?php echo number_format($o['total_amount']); ?>
                                <?php endif; ?>
                                <?php if (!empty($o['rider_note']) || !empty($o['admin_note'])): ?>
                                    <span class="inline-block ml-1 text-primary-600 align-middle" title="<?php echo htmlspecialchars(!empty($o['rider_note']) ? 'Rider: ' . $o['rider_note'] : 'Admin: ' . $o['admin_note']); ?>">
                                        <ion-icon name="document-text" class="text-xs"></ion-icon>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php
                                    $st = !empty($o['status']) ? strtolower(trim($o['status'])) : 'pending';
                                    $statusColor = match($st) {
                                        'pending' => 'bg-amber-100 text-amber-800 border border-amber-200',
                                        'processing' => 'bg-blue-100 text-blue-800 border border-blue-200',
                                        'packed' => 'bg-indigo-100 text-indigo-800 border border-indigo-200',
                                        'shipped' => 'bg-cyan-100 text-cyan-800 border border-cyan-200',
                                        'out_for_delivery' => 'bg-purple-100 text-purple-800 border border-purple-200',
                                        'delivered' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                                        'cancelled' => 'bg-rose-100 text-rose-800 border border-rose-200',
                                        default => 'bg-secondary-100 text-secondary-800 border border-secondary-200'
                                    };
                                    $displayStatus = ucfirst(str_replace('_', ' ', $st ?: 'pending'));
                                ?>
                                <span class="<?php echo $statusColor; ?> px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wide">
                                    <?php echo $displayStatus; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <?php echo date('M d, Y', strtotime($o['created_at'])); ?> <br>
                                <?php echo date('h:i A', strtotime($o['created_at'])); ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <?php if ($st === 'processing'): ?>
                                    <div class="flex items-center justify-end gap-2 mb-2">
                                        <form action="/sodai-dorkar/public/admin/orders/change-status" method="POST" class="inline">
                                            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                                            <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                            <input type="hidden" name="status" value="packaging">
                                            <button type="submit" class="text-xs bg-emerald-500 hover:bg-emerald-600 text-white px-2 py-1 rounded shadow-sm">Confirm</button>
                                        </form>
                                        <form action="/sodai-dorkar/public/admin/orders/change-status" method="POST" class="inline" onsubmit="return confirm('Cancel this order?');">
                                            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                                            <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" class="text-xs bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded shadow-sm">Cancel</button>
                                        </form>
                                        <a href="/sodai-dorkar/public/admin/orders/edit?id=<?php echo $o['id']; ?>" class="text-xs bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded shadow-sm">Edit</a>
                                    </div>
                                <?php endif; ?>
                                <div class="flex items-center justify-end gap-2">
                                    <a href="/sodai-dorkar/public/admin/orders/pos-receipt?id=<?php echo $o['id']; ?>" target="_blank" class="p-1.5 text-emerald-600 hover:text-emerald-800 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors flex items-center" title="Print 3&quot; POS Receipt">
                                        <ion-icon name="receipt-outline" class="text-base"></ion-icon>
                                    </a>
                                    <a href="/sodai-dorkar/public/admin/orders/show?id=<?php echo $o['id']; ?>" class="text-primary-600 hover:text-primary-800 font-medium flex items-center">
                                        <ion-icon name="eye-outline" class="mr-1"></ion-icon> View
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
