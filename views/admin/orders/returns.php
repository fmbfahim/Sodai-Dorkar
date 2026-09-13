<?php
// Layout handling
$content = function() use ($returns) {
?>
<div class="space-y-6">

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl shadow-xs text-sm font-bold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <ion-icon name="checkmark-circle" class="text-xl text-emerald-600"></ion-icon>
                <span><?php echo htmlspecialchars($_SESSION['success']); ?></span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700"><ion-icon name="close"></ion-icon></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl shadow-xs text-sm font-bold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <ion-icon name="alert-circle" class="text-xl text-red-600"></ion-icon>
                <span><?php echo htmlspecialchars($_SESSION['error']); ?></span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700"><ion-icon name="close"></ion-icon></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-secondary-900 flex items-center gap-2">
                <ion-icon name="sync-circle" class="text-primary-600"></ion-icon>
                Returns & Damage Management
            </h1>
            <p class="text-sm text-secondary-500 mt-1">Review orders marked as returned by riders and process stock or damages.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xs border border-secondary-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-secondary-600">
                <thead class="bg-secondary-50 text-secondary-500 uppercase tracking-wider font-bold text-xs border-b border-secondary-100">
                    <tr>
                        <th class="px-5 py-3.5">Order ID</th>
                        <th class="px-5 py-3.5">Customer & Rider</th>
                        <th class="px-5 py-3.5">Amount</th>
                        <th class="px-5 py-3.5">Return Note</th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-secondary-100">
                    <?php if (empty($returns)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-secondary-400">
                                <ion-icon name="checkmark-done-circle-outline" class="text-4xl text-secondary-300 mb-2"></ion-icon>
                                <p class="font-medium">No pending returns to process. All caught up!</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($returns as $order): ?>
                            <tr class="hover:bg-secondary-50 transition-colors">
                                <td class="px-5 py-3.5">
                                    <a href="/sodai-dorkar/public/admin/orders/show?id=<?php echo $order['id']; ?>" class="font-black text-primary-600 hover:text-primary-700 hover:underline text-base">
                                        #<?php echo $order['id']; ?>
                                    </a>
                                    <div class="text-[11px] text-secondary-400 font-mono mt-0.5">
                                        <?php echo date('d M, h:i A', strtotime($order['updated_at'])); ?>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="font-bold text-secondary-800"><?php echo htmlspecialchars($order['customer_name'] ?? 'Unknown'); ?></div>
                                    <div class="text-xs text-secondary-500 flex items-center gap-1 mt-0.5">
                                        <ion-icon name="bicycle"></ion-icon>
                                        <?php echo htmlspecialchars($order['rider_name'] ?? 'Unassigned'); ?>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="font-bold text-secondary-700 font-mono">৳ <?php echo number_format($order['total_amount'], 2); ?></div>
                                </td>
                                <td class="px-5 py-3.5 max-w-xs">
                                    <?php if (!empty($order['rider_note'])): ?>
                                        <p class="text-xs text-amber-700 bg-amber-50 border border-amber-100 px-2 py-1.5 rounded-lg">
                                            "<?php echo htmlspecialchars($order['rider_note']); ?>"
                                        </p>
                                    <?php else: ?>
                                        <span class="text-secondary-400 text-xs italic">No note provided</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Receive Return -->
                                        <button type="button" onclick="openProcessModal(<?php echo $order['id']; ?>, 'receive')" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 hover:border-emerald-600 font-bold text-xs rounded-xl transition-all shadow-2xs flex items-center gap-1">
                                            <ion-icon name="archive-outline" class="text-sm"></ion-icon> Receive & Restore Stock
                                        </button>
                                        <!-- Mark Damaged -->
                                        <button type="button" onclick="openProcessModal(<?php echo $order['id']; ?>, 'damage')" class="px-3 py-1.5 bg-red-50 hover:bg-red-600 text-red-700 hover:text-white border border-red-200 hover:border-red-600 font-bold text-xs rounded-xl transition-all shadow-2xs flex items-center gap-1">
                                            <ion-icon name="warning-outline" class="text-sm"></ion-icon> Mark Damaged
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Process Modal -->
<div id="processModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md p-5 relative shadow-2xl">
        <h3 id="modalTitle" class="font-black text-lg mb-1 flex items-center gap-2"></h3>
        <p id="modalDesc" class="text-xs text-secondary-500 mb-4"></p>
        
        <form action="/sodai-dorkar/public/admin/orders/process-return" method="POST">
            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
            <input type="hidden" name="order_id" id="processOrderId">
            <input type="hidden" name="action" id="processAction">
            
            <div class="mb-4">
                <label class="block text-xs font-bold text-secondary-700 mb-1">Admin Note (Optional)</label>
                <textarea name="admin_note" rows="2" class="w-full px-3 py-2 border border-secondary-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 outline-none" placeholder="e.g. Products checked, 1 item damaged..."></textarea>
            </div>
            
            <div class="flex gap-2 justify-end mt-2">
                <button type="button" onclick="closeProcessModal()" class="px-4 py-2.5 bg-secondary-100 hover:bg-secondary-200 text-secondary-700 rounded-xl text-xs font-bold transition-colors">Cancel</button>
                <button type="submit" id="submitBtn" class="px-4 py-2.5 text-white rounded-xl text-xs font-bold shadow-md transition-colors flex items-center gap-1"></button>
            </div>
        </form>
    </div>
</div>

<script>
function openProcessModal(orderId, action) {
    document.getElementById('processOrderId').value = orderId;
    document.getElementById('processAction').value = action;
    
    const title = document.getElementById('modalTitle');
    const desc = document.getElementById('modalDesc');
    const btn = document.getElementById('submitBtn');
    
    if (action === 'receive') {
        title.innerHTML = '<ion-icon name="archive" class="text-emerald-600"></ion-icon> <span class="text-emerald-800">Receive Return</span>';
        desc.innerText = "Are you sure? This will RESTORE the products back into your inventory stock.";
        btn.className = "px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md transition-colors flex items-center gap-1";
        btn.innerHTML = '<ion-icon name="checkmark-circle"></ion-icon> Confirm Receive';
    } else {
        title.innerHTML = '<ion-icon name="warning" class="text-red-600"></ion-icon> <span class="text-red-800">Mark Damaged</span>';
        desc.innerText = "Are you sure? This will mark the items as damaged. Stock will NOT be restored.";
        btn.className = "px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold shadow-md transition-colors flex items-center gap-1";
        btn.innerHTML = '<ion-icon name="alert-circle"></ion-icon> Confirm Damage';
    }
    
    document.getElementById('processModal').classList.remove('hidden');
}
function closeProcessModal() {
    document.getElementById('processModal').classList.add('hidden');
}
</script>
<?php
};
require __DIR__ . '/../../layouts/admin.php';
?>
