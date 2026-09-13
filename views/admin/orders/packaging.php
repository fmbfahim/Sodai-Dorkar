<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<div class="mb-6 flex justify-between items-center">
    <h3 class="text-lg font-bold text-secondary-800">Packaging Department</h3>
</div>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="mb-4 bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-lg flex items-center justify-between text-sm shadow-sm print:hidden">
        <div class="flex items-center gap-2">
            <ion-icon name="checkmark-circle" class="text-xl text-emerald-600"></ion-icon>
            <span class="font-bold"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
            <ion-icon name="close" class="text-lg"></ion-icon>
        </button>
    </div>
<?php endif; ?>

<!-- Tabs -->
<div class="mb-6 flex space-x-2 border-b border-secondary-200 print:hidden">
    <a href="?tab=new" class="px-4 py-2 text-sm font-medium transition-colors border-b-2 <?= $tab === 'new' ? 'border-primary-600 text-primary-600' : 'border-transparent text-secondary-500 hover:text-secondary-700' ?>">
        New Orders (To Receive)
    </a>
    <a href="?tab=picking" class="px-4 py-2 text-sm font-medium transition-colors border-b-2 <?= $tab === 'picking' ? 'border-primary-600 text-primary-600' : 'border-transparent text-secondary-500 hover:text-secondary-700' ?>">
        Received / Processing
    </a>
</div>

<?php if ($tab === 'new'): ?>
    <form action="/sodai-dorkar/public/admin/orders/bulk-receive-packaging" method="POST">
        <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
        <div class="mb-4 print:hidden">
            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors shadow-sm text-sm">
                <ion-icon name="download-outline" class="mr-2"></ion-icon>
                Receive Selected Orders
            </button>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-secondary-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-secondary-600">
                    <thead class="bg-secondary-50 text-secondary-500">
                        <tr>
                            <th class="px-6 py-3 font-medium w-12 text-center print:hidden">
                                <input type="checkbox" id="selectAll" class="rounded border-secondary-300 text-primary-600 focus:ring-primary-500">
                            </th>
                            <th class="px-6 py-3 font-medium">Order ID</th>
                            <th class="px-6 py-3 font-medium">Customer</th>
                            <th class="px-6 py-3 font-medium">Amount</th>
                            <th class="px-6 py-3 font-medium">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-secondary-100">
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-secondary-400">
                                    No new orders for packaging.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $o): ?>
                                <tr class="hover:bg-secondary-50">
                                    <td class="px-6 py-4 text-center print:hidden">
                                        <input type="checkbox" name="order_ids[]" value="<?php echo $o['id']; ?>" class="order-checkbox rounded border-secondary-300 text-primary-600 focus:ring-primary-500">
                                    </td>
                                    <td class="px-6 py-4 font-bold text-primary-600 hover:text-primary-800">
                                        <a href="/sodai-dorkar/public/admin/orders/show?id=<?php echo $o['id']; ?>" target="_blank">#<?php echo $o['id']; ?></a>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-secondary-900"><?php echo htmlspecialchars($o['customer_name']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 font-mono font-medium text-secondary-900">
                                        ৳ <?php echo number_format($o['total_amount']); ?>
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        <?php echo date('M d, Y h:i A', strtotime($o['created_at'])); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </form>

    <script>
        document.getElementById('selectAll')?.addEventListener('change', function() {
            document.querySelectorAll('.order-checkbox').forEach(cb => cb.checked = this.checked);
        });
    </script>

<?php elseif ($tab === 'picking'): ?>

    <?php if (!empty($pickList)): ?>
    <!-- Pick List Section -->
    <div class="bg-white rounded-xl shadow-sm border border-secondary-100 overflow-hidden mb-8">
        <div class="bg-primary-50 px-6 py-4 border-b border-primary-100 flex justify-between items-center">
            <h4 class="font-bold text-primary-800 flex items-center">
                <ion-icon name="list-outline" class="mr-2 text-xl"></ion-icon>
                Aggregated Pick List
            </h4>
            <button onclick="window.print()" class="text-sm bg-white border border-primary-200 text-primary-700 px-3 py-1.5 rounded hover:bg-primary-100 transition-colors flex items-center print:hidden">
                <ion-icon name="print-outline" class="mr-1"></ion-icon> Print Pick List
            </button>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($pickList as $item): ?>
                    <label class="border border-secondary-200 rounded-lg p-3 flex justify-between items-center bg-gray-50 break-inside-avoid cursor-pointer hover:bg-gray-100 transition-colors has-[:checked]:bg-emerald-50 has-[:checked]:border-emerald-300 group">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" class="w-5 h-5 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500 cursor-pointer print:hidden">
                            <?php if (!empty($item['image'])): ?>
                                <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="" class="w-10 h-10 object-cover rounded shadow-sm border border-secondary-200 bg-white">
                            <?php else: ?>
                                <div class="w-10 h-10 rounded shadow-sm border border-secondary-200 bg-secondary-100 flex items-center justify-center text-secondary-400">
                                    <ion-icon name="image-outline"></ion-icon>
                                </div>
                            <?php endif; ?>
                            <div>
                                <div class="font-bold text-secondary-800 text-sm group-has-[:checked]:line-through group-has-[:checked]:text-secondary-500"><?= htmlspecialchars($item['name']) ?></div>
                                <?php if ($item['variant']): ?>
                                    <div class="text-xs text-secondary-500 mt-1"><?= htmlspecialchars($item['variant']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="bg-primary-600 group-has-[:checked]:bg-emerald-600 text-white font-bold px-3 py-1 rounded-lg text-lg ml-2 shrink-0">
                            <?= $item['qty'] ?>x
                        </div>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Individual Orders Section -->
    <h4 class="font-bold text-secondary-800 mb-4 text-lg print:hidden">Orders in Processing</h4>
    <div class="grid grid-cols-1 gap-4 print:hidden">
        <?php if (empty($orders)): ?>
            <div class="bg-white rounded-xl shadow-sm border border-secondary-100 p-8 text-center text-secondary-400">
                No orders are currently being processed.
            </div>
        <?php else: ?>
            <?php foreach ($orders as $o): ?>
                <div class="bg-white rounded-xl shadow-sm border border-secondary-100 p-4">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4 cursor-pointer" onclick="document.getElementById('items-<?php echo $o['id']; ?>').classList.toggle('hidden')">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-1">
                                <span class="font-bold text-primary-600 text-lg">#<?php echo $o['id']; ?></span>
                                <span class="bg-blue-100 text-blue-800 border border-blue-200 px-2 py-0.5 rounded text-xs font-bold uppercase">Processing</span>
                            </div>
                            <div class="text-sm text-secondary-600">
                                <span class="font-medium text-secondary-900"><?php echo htmlspecialchars($o['customer_name']); ?></span> • 
                                <?php echo htmlspecialchars($o['customer_phone']); ?> • 
                                ৳ <?php echo number_format($o['total_amount']); ?> •
                                <span class="text-primary-600 font-medium">Show <?php echo count($o['items'] ?? []); ?> Items ▾</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2" onclick="event.stopPropagation()">
                            <a href="/sodai-dorkar/public/admin/orders/show?id=<?php echo $o['id']; ?>" target="_blank" class="text-sm bg-white border border-secondary-300 text-secondary-700 hover:bg-secondary-50 px-3 py-2 rounded-lg font-medium transition-colors">
                                View Order
                            </a>
                            <!-- Arrow icon to indicate expansion -->
                            <ion-icon name="chevron-down-outline" class="text-secondary-400"></ion-icon>
                        </div>
                    </div>
                    
                    <div id="items-<?php echo $o['id']; ?>" class="hidden mt-4 pt-4 border-t border-secondary-100">
                        <h5 class="text-xs font-bold text-secondary-500 uppercase tracking-wider mb-3">Order Items Checklist</h5>
                        <ul class="space-y-2 mb-4">
                            <?php foreach ($o['items'] ?? [] as $item): ?>
                                <li class="flex items-center gap-3">
                                    <label class="flex items-center gap-3 cursor-pointer group flex-1 p-2 rounded hover:bg-gray-50 has-[:checked]:bg-emerald-50">
                                        <input type="checkbox" class="w-5 h-5 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500 cursor-pointer item-check-<?php echo $o['id']; ?>" onchange="checkOrderReady(<?php echo $o['id']; ?>)">
                                        <?php if (!empty($item['image_path'])): ?>
                                            <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="" class="w-10 h-10 object-cover rounded border border-secondary-200 bg-white shadow-sm">
                                        <?php endif; ?>
                                        <span class="text-sm font-bold text-secondary-800 group-has-[:checked]:line-through group-has-[:checked]:text-secondary-400 transition-colors">
                                            <?php echo htmlspecialchars($item['product_name']); ?>
                                            <?php if (!empty($item['unit_title'])): ?>
                                                <span class="text-xs text-secondary-500 ml-1">(<?php echo htmlspecialchars($item['unit_title']); ?>)</span>
                                            <?php endif; ?>
                                        </span>
                                        <span class="ml-auto font-bold text-lg text-secondary-900 group-has-[:checked]:text-emerald-700 group-has-[:checked]:line-through">
                                            <?php echo $item['quantity']; ?>x
                                        </span>
                                    </label>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        
                        <div class="flex justify-end border-t border-secondary-100 pt-3">
                            <form action="/sodai-dorkar/public/admin/orders/change-status" method="POST" class="m-0" id="form-<?php echo $o['id']; ?>">
                                <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                                <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                <input type="hidden" name="status" value="dispatch">
                                <input type="hidden" name="redirect" value="/sodai-dorkar/public/admin/orders/packaging?tab=picking">
                                <button type="submit" id="btn-<?php echo $o['id']; ?>" disabled class="text-sm bg-gray-300 text-gray-500 px-5 py-2.5 rounded-lg font-bold shadow-sm transition-colors flex items-center cursor-not-allowed">
                                    <ion-icon name="checkmark-circle-outline" class="mr-2 text-xl"></ion-icon> Match & Ready to Delivery
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <script>
        function checkOrderReady(orderId) {
            const checkboxes = document.querySelectorAll('.item-check-' + orderId);
            const btn = document.getElementById('btn-' + orderId);
            let allChecked = true;
            checkboxes.forEach(cb => {
                if (!cb.checked) allChecked = false;
            });
            
            if (allChecked && checkboxes.length > 0) {
                btn.disabled = false;
                btn.classList.remove('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
                btn.classList.add('bg-emerald-600', 'hover:bg-emerald-700', 'text-white');
            } else {
                btn.disabled = true;
                btn.classList.add('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
                btn.classList.remove('bg-emerald-600', 'hover:bg-emerald-700', 'text-white');
            }
        }
    </script>
<?php endif; ?>
