<div class="flex flex-col h-full overflow-hidden">
    <!-- Header -->
    <header class="flex justify-end items-center mb-4 flex-shrink-0">
        <div class="flex gap-2">
            <button form="dispatchForm" type="submit" name="action" value="print_labels" class="bg-gray-800 hover:bg-black text-white px-4 py-2 rounded-lg font-bold flex items-center text-sm shadow-sm transition-colors">
                <ion-icon name="print" class="mr-2"></ion-icon> Labels
            </button>
            <button form="dispatchForm" type="submit" name="action" value="print_invoices" class="bg-gray-800 hover:bg-black text-white px-4 py-2 rounded-lg font-bold flex items-center text-sm shadow-sm transition-colors">
                <ion-icon name="document-text" class="mr-2"></ion-icon> Invoices
            </button>
            <div class="h-8 w-px bg-secondary-300 mx-2"></div>
            <button form="dispatchForm" type="submit" name="action" value="mark_shipped" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-bold flex items-center text-sm shadow-md transition-colors">
                <ion-icon name="bicycle" class="mr-2"></ion-icon> Mark Shipped
            </button>
        </div>
    </header>

    <!-- Filters / Toolbar (Optional) -->
    <div class="bg-white p-3 border border-secondary-200 rounded-lg mb-4 flex justify-between items-center flex-shrink-0 shadow-sm">
        <div class="flex items-center gap-2">
            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 h-4 w-4" onclick="toggleAll(this)">
            <span class="text-sm font-medium text-secondary-600 ml-1">Select All</span>
        </div>
        <div class="flex items-center gap-2">
            <select form="dispatchForm" name="delivery_man_id" class="text-sm border-secondary-300 rounded-md shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                <option value="">-- Assign Courier --</option>
                <?php foreach($deliveryMen as $dm): ?>
                <option value="<?php echo $dm['id']; ?>"><?php echo htmlspecialchars($dm['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <button form="dispatchForm" type="submit" name="action" value="assign_delivery" class="text-xs bg-secondary-100 hover:bg-secondary-200 text-secondary-800 px-3 py-2 rounded font-medium border border-secondary-300">
                Assign
            </button>
            <button form="dispatchForm" type="submit" name="action" value="auto_assign" class="text-xs bg-primary-600 hover:bg-primary-700 text-white px-3 py-2 rounded font-medium border border-primary-600 ml-2 shadow-sm transition-colors" title="Automatically assign Delivery Men to selected orders based on allocation strategy">
                <ion-icon name="flash-outline" class="mr-1"></ion-icon> Auto Assign
            </button>
        </div>
    </div>

    <!-- Table Container -->
    <div id="listContainer" class="flex-1 overflow-auto bg-white border border-secondary-200 rounded-xl shadow-sm">
        <form id="dispatchForm" action="/sodai-dorkar/public/admin/dispatch/bulk" method="POST">
            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
            <table class="w-full text-left border-collapse">
                <thead class="bg-secondary-50 sticky top-0 z-10 text-xs uppercase text-secondary-500 font-semibold tracking-wider">
                    <tr>
                        <th class="p-4 w-12 text-center"></th>
                        <th class="p-4">Order Details</th>
                        <th class="p-4">Customer</th>
                        <th class="p-4">Location</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Amount</th>
                        <th class="p-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-secondary-100 text-sm">
                    <?php if(empty($orders)): ?>
                    <tr><td colspan="7" class="p-8 text-center text-secondary-400 italic">No orders in queue.</td></tr>
                    <?php else: ?>
                        <?php foreach($orders as $order): ?>
                        <tr class="hover:bg-secondary-50 transition-colors group">
                            <td class="p-4 text-center">
                                <input type="checkbox" name="order_ids[]" value="<?php echo $order['id']; ?>" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 h-4 w-4 order-check">
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-secondary-800">#<?php echo $order['id']; ?></div>
                                <div class="text-xs text-secondary-500"><?php echo date('d M h:i A', strtotime($order['created_at'])); ?></div>
                                <?php if($order['delivery_man_name']): ?>
                                    <div class="mt-1 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-800">
                                        <ion-icon name="bicycle" class="mr-1"></ion-icon> <?php echo $order['delivery_man_name']; ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="p-4">
                                <div class="font-medium text-secondary-800"><?php echo htmlspecialchars($order['customer_name']); ?></div>
                                <div class="text-xs text-secondary-500 font-mono"><?php echo htmlspecialchars($order['customer_phone']); ?></div>
                            </td>
                            <td class="p-4">
                                <div class="text-secondary-800 line-clamp-1 text-xs" title="<?php echo htmlspecialchars($order['delivery_address']); ?>">
                                    <?php echo htmlspecialchars($order['delivery_address']); ?>
                                </div>
                                <?php if($order['area_name']): ?>
                                <div class="text-[10px] bg-secondary-100 px-1 rounded inline-block mt-1 text-secondary-600">
                                    <?php echo htmlspecialchars($order['area_name']); ?>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td class="p-4">
                                <span class="px-2 py-1 text-xs rounded-full font-bold
                                    <?php 
                                        if($order['status']=='pending') echo 'bg-amber-100 text-amber-700'; 
                                        elseif($order['status']=='shipped') echo 'bg-blue-100 text-blue-700';
                                        elseif($order['status']=='processing') echo 'bg-purple-100 text-purple-700';
                                        elseif($order['status']=='confirmed') echo 'bg-green-100 text-green-700';
                                        else echo 'bg-gray-100 text-gray-700';
                                    ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td class="p-4 text-right font-bold text-secondary-800">
                                ৳ <?php echo number_format($order['total_amount']); ?>
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="/sodai-dorkar/public/admin/orders/invoice?id=<?php echo $order['id']; ?>" target="_blank" class="p-2 text-secondary-500 hover:text-primary-600 bg-white border border-secondary-200 rounded shadow-sm" title="Invoice">
                                        <ion-icon name="document-text-outline"></ion-icon>
                                    </a>
                                    <a href="/sodai-dorkar/public/admin/orders/label?id=<?php echo $order['id']; ?>" target="_blank" class="p-2 text-secondary-500 hover:text-black bg-white border border-secondary-200 rounded shadow-sm" title="Label">
                                        <ion-icon name="print-outline"></ion-icon>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </form>
    </div>
</div>

<script>
    function toggleAll(source) {
        checkboxes = document.querySelectorAll('.order-check');
        for(var i=0, n=checkboxes.length;i<n;i++) {
            checkboxes[i].checked = source.checked;
        }
    }
</script>
