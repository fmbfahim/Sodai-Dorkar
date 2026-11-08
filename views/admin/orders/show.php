<?php 
if (session_status() === PHP_SESSION_NONE) session_start(); 
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
?>
<?php if (!empty($_SESSION['success'])): ?>
    <div class="max-w-4xl mx-auto mb-4 bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-2xl flex items-center justify-between text-sm shadow-sm print:hidden">
        <div class="flex items-center gap-2">
            <ion-icon name="checkmark-circle" class="text-xl text-emerald-600"></ion-icon>
            <span class="font-bold"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 cursor-pointer">
            <ion-icon name="close" class="text-lg"></ion-icon>
        </button>
    </div>
<?php endif; ?>

<div class="max-w-4xl mx-auto bg-white rounded-xl shadow-sm border border-secondary-100 overflow-hidden" id="invoice">
    <!-- Header -->
    <?php
    $siteTitle = class_exists('\Models\Setting') ? \Models\Setting::getValue('site_title', 'Fresh E mart') : 'Fresh E mart';
    $siteTagline = class_exists('\Models\Setting') ? \Models\Setting::getValue('site_tagline', 'Grocery Delivery Service') : 'Grocery Delivery Service';
    $contactAddress = class_exists('\Models\Setting') ? \Models\Setting::getValue('contact_address', 'Chandpur, Bangladesh') : 'Chandpur, Bangladesh';
    $contactPhone = class_exists('\Models\Setting') ? \Models\Setting::getValue('contact_phone', '01700-000000') : '01700-000000';
    ?>
    <div class="p-8 border-b border-secondary-100 flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-primary-600 mb-2"><?= htmlspecialchars($siteTitle) ?></h1>
            <p class="text-secondary-500 text-sm"><?= htmlspecialchars($siteTagline) ?></p>
            <p class="text-secondary-500 text-sm"><?= htmlspecialchars($contactAddress) ?></p>
            <p class="text-secondary-500 text-sm">Hotline: <?= htmlspecialchars($contactPhone) ?></p>
        </div>
        <div class="text-right">
            <h2 class="text-xl font-bold text-secondary-800">INVOICE</h2>
            <p class="text-secondary-500 font-mono mt-1">#<?php echo $order['id']; ?></p>
            <div class="mt-2">
                <?php 
                    $st = !empty($order['status']) ? strtolower(trim($order['status'])) : 'pending';
                    $statusColor = match($st) {
                        'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                        'processing' => 'bg-blue-100 text-blue-800 border-blue-200',
                        'packed' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                        'shipped' => 'bg-cyan-100 text-cyan-800 border-cyan-200',
                        'out_for_delivery' => 'bg-purple-100 text-purple-800 border-purple-200',
                        'delivered' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                        'cancelled' => 'bg-rose-100 text-rose-800 border-rose-200',
                        default => 'bg-secondary-100 text-secondary-800 border-secondary-200'
                    };
                    $displayStatus = ucfirst(str_replace('_', ' ', $st ?: 'pending'));
                ?>
                <span class="<?php echo $statusColor; ?> border px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide inline-block mb-3">
                    <?php echo $displayStatus; ?>
                </span>
                
                <!-- Action Buttons -->
                <?php if ($st === 'processing' || $st === 'pending'): ?>
                    <div class="flex items-center justify-end gap-2 mt-2 print:hidden">
                        <form action="<?= $base ?>/admin/orders/change-status" method="POST" class="inline">
                            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <input type="hidden" name="status" value="packaging">
                            <button type="submit" class="text-xs font-bold bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-2 rounded-lg shadow-sm transition-colors">Confirm Order</button>
                        </form>
                        <form action="<?= $base ?>/admin/orders/change-status" method="POST" class="inline" onsubmit="return confirm('Cancel this order?');">
                            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" class="text-xs font-bold bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg shadow-sm transition-colors">Cancel</button>
                        </form>
                    </div>
                <?php elseif ($st === 'packaging'): ?>
                    <div class="flex items-center justify-end gap-2 mt-2 print:hidden">
                        <form action="<?= $base ?>/admin/orders/change-status" method="POST" class="inline">
                            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <input type="hidden" name="status" value="dispatch">
                            <button type="submit" class="text-xs font-bold bg-indigo-500 hover:bg-indigo-600 text-white px-3 py-2 rounded-lg shadow-sm transition-colors">Send to Dispatch</button>
                        </form>
                    </div>
                <?php elseif ($st === 'cancelled'): ?>
                    <div class="flex items-center justify-end gap-2 mt-2 print:hidden">
                        <form action="<?= $base ?>/admin/orders/change-status" method="POST" class="inline" onsubmit="return confirm('Mark this cancelled order as returned to process stock?');">
                            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <input type="hidden" name="status" value="returned">
                            <button type="submit" class="text-xs font-bold bg-amber-500 hover:bg-amber-600 text-white px-3 py-2 rounded-lg shadow-sm transition-colors">Mark as Returned</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Info Grid -->
    <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8 border-b border-secondary-100">
        <div>
            <h3 class="text-xs font-bold text-secondary-400 uppercase tracking-wider mb-3">Customer Info</h3>
            <div class="text-secondary-800 font-medium"><?php echo htmlspecialchars($order['customer_name']); ?></div>
            <div class="text-secondary-600 text-sm mt-1"><?php echo htmlspecialchars($order['customer_phone']); ?></div>
            <div class="text-secondary-600 text-sm mt-1"><?php echo htmlspecialchars($order['address_details'] ?? $order['delivery_address']); ?></div>
        </div>
        <div class="text-right md:text-right">
             <h3 class="text-xs font-bold text-secondary-400 uppercase tracking-wider mb-3">Order Details</h3>
             <div class="text-secondary-600 text-sm"><span class="font-medium">Date:</span> <?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></div>
             <div class="text-secondary-600 text-sm mt-1 flex items-center justify-end gap-2">
                 <span class="font-medium">Delivery:</span>
                 <?php if ($order['status'] !== 'delivered' && $order['status'] !== 'cancelled' && !empty($deliveryMen)): ?>
                     <form action="<?= $base ?>/admin/orders/assign" method="POST" class="inline-flex items-center gap-1">
                         <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                         <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                         <input type="hidden" name="redirect" value="<?= $base ?>/admin/orders/show?id=<?php echo $order['id']; ?>">
                         <select name="delivery_man_id" onchange="this.form.submit()" class="text-xs font-bold border border-secondary-300 rounded-lg px-2 py-1 bg-white focus:outline-none focus:ring-1 focus:ring-primary-500 cursor-pointer shadow-2xs">
                             <option value="">Unassigned</option>
                             <?php foreach ($deliveryMen as $dm): ?>
                                 <option value="<?php echo $dm['id']; ?>" <?php echo ($order['delivery_man_id'] == $dm['id']) ? 'selected' : ''; ?>>
                                     <?php echo htmlspecialchars($dm['name']); ?>
                                 </option>
                             <?php endforeach; ?>
                         </select>
                     </form>
                 <?php else: ?>
                     <span class="font-bold text-secondary-900"><?php echo htmlspecialchars($order['delivery_man_name'] ?? 'Unassigned'); ?></span>
                 <?php endif; ?>
             </div>
        </div>
    </div>

    <!-- Items -->
    <div class="p-8">
        <table class="w-full text-left text-sm text-secondary-600">
            <thead class="bg-secondary-50 text-secondary-500 border-b border-secondary-200">
                <tr>
                    <th class="py-3 font-medium">Item</th>
                    <th class="py-3 font-medium text-center">Qty / Weight</th>
                    <th class="py-3 font-medium text-right">Price</th>
                    <th class="py-3 font-medium text-right">Total</th>
                    <?php if ($order['status'] !== 'delivered' && $order['status'] !== 'cancelled'): ?>
                        <th class="py-3 font-medium text-center print:hidden w-36">Action</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody class="divide-y divide-secondary-100">
                <?php foreach ($order['items'] as $item): 
                    $fQty = floatval($item['quantity']);
                    $qtyDisplay = ($fQty == intval($fQty)) ? intval($fQty) : rtrim(rtrim(number_format($fQty, 3), '0'), '.');
                ?>
                <tr>
                    <td class="py-4">
                        <div class="font-medium text-secondary-900 flex items-center gap-2 flex-wrap">
                            <span class="font-bold"><?php echo htmlspecialchars($item['product_name']); ?></span>
                            <?php if (!empty($item['unit_title'])): ?>
                                <span class="text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-md">
                                    <?php echo htmlspecialchars($item['unit_title']); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="text-xs text-secondary-400 font-mono mt-0.5"><?php echo htmlspecialchars($item['sku'] ?? ''); ?></div>
                        <?php if (!empty($item['actual_weight_note'])): ?>
                            <div class="mt-1 inline-flex items-center gap-1 bg-amber-50 border border-amber-200/80 px-2 py-0.5 rounded-md text-[11px] font-bold text-amber-900 shadow-2xs">
                                <ion-icon name="scale" class="text-xs text-amber-600"></ion-icon>
                                <span><?php echo htmlspecialchars($item['actual_weight_note']); ?></span>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td class="py-4 text-center font-bold text-secondary-800">
                        <span><?php echo $qtyDisplay; ?></span>
                        <span class="text-[11px] text-secondary-400 font-medium block">
                            <?php echo htmlspecialchars($item['base_unit'] ?? ''); ?>
                        </span>
                    </td>
                    <td class="py-4 text-right">৳ <?php echo formatPrice($item['price']); ?></td>
                    <td class="py-4 text-right font-black text-secondary-900">৳ <?php echo formatPrice($item['price'] * $fQty); ?></td>
                    <?php if ($order['status'] !== 'delivered' && $order['status'] !== 'cancelled'): ?>
                        <td class="py-4 text-center print:hidden">
                            <button type="button" 
                                    onclick='openCatchWeightModal(<?php echo json_encode($item, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>)' 
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300 text-xs font-bold transition-all shadow-2xs hover:shadow-xs cursor-pointer"
                                    title="কাঁচা সবজি, ফল, মাছ বা মাংসের আসল মাপা ওজন অনুযায়ী বিল ও স্টক সমন্বয় করুন">
                                <ion-icon name="scale-outline" class="text-sm text-amber-600"></ion-icon>
                                <span>আসল ওজন সমন্বয়</span>
                            </button>
                        </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Totals & Adjustments Section -->
    <div class="p-8 bg-secondary-50 border-t border-secondary-200">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
            
            <!-- Left: Notes & Adjustment Log -->
            <div class="space-y-4">
                <!-- Amount Change / Discount History Card (If Changed) -->
                <?php 
                    $hasDiscount = !empty($order['delivery_discount']) && floatval($order['delivery_discount']) > 0;
                    $origAmount = !empty($order['original_amount']) ? floatval($order['original_amount']) : floatval($order['total_amount']);
                    $hasAmountChange = !empty($order['amount_changed_by']);
                ?>
                <?php if ($hasDiscount || $hasAmountChange): ?>
                    <div class="p-4 rounded-2xl bg-amber-50/90 border border-amber-200 text-xs">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-amber-900 flex items-center gap-1.5 uppercase tracking-wider text-[11px]">
                                <ion-icon name="pricetag" class="text-amber-600 text-sm"></ion-icon>
                                <span>Amount Adjusted / Discount Applied</span>
                            </span>
                            <span class="px-2 py-0.5 rounded-full font-black text-[10px] uppercase <?php echo ($order['amount_changed_by'] === 'rider') ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'; ?>">
                                By <?php echo htmlspecialchars(ucfirst($order['amount_changed_by'] ?? 'Staff')); ?>
                            </span>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-center py-2 bg-white/80 rounded-xl border border-amber-100 font-mono">
                            <div>
                                <span class="text-[10px] text-secondary-500 block">Original Bill</span>
                                <span class="font-bold text-secondary-800 text-xs">৳ <?php echo number_format($origAmount, 2); ?></span>
                            </div>
                            <div>
                                <span class="text-[10px] text-red-500 block">Discount</span>
                                <span class="font-black text-red-600 text-xs">- ৳ <?php echo number_format($order['delivery_discount'] ?? 0, 2); ?></span>
                            </div>
                            <div>
                                <span class="text-[10px] text-emerald-600 block">Final Bill</span>
                                <span class="font-black text-emerald-700 text-xs">৳ <?php echo number_format($order['total_amount'], 2); ?></span>
                            </div>
                        </div>
                        <?php if (!empty($order['amount_change_reason'])): ?>
                            <div class="mt-2 text-secondary-600">
                                <span class="font-semibold text-secondary-700">Reason:</span> <?php echo htmlspecialchars($order['amount_change_reason']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Rider Note -->
                <div class="p-4 rounded-2xl bg-white border border-secondary-200 text-xs shadow-2xs mb-4">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="font-bold text-secondary-800 flex items-center gap-1.5">
                            <ion-icon name="bicycle" class="text-primary-600 text-sm"></ion-icon>
                            <span>Rider Parcel Note</span>
                        </span>
                        <?php if (empty($order['rider_note'])): ?>
                            <span class="text-[10px] text-secondary-400 italic">None</span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($order['rider_note'])): ?>
                        <p class="text-secondary-700 bg-secondary-50 p-2.5 rounded-xl border border-secondary-100 italic">
                            "<?php echo htmlspecialchars($order['rider_note']); ?>"
                        </p>
                    <?php else: ?>
                        <p class="text-secondary-400 text-[11px]">Rider hasn't added any notes to this parcel yet.</p>
                    <?php endif; ?>
                </div>

                <?php if (!empty($order['admin_note'])): ?>
                <!-- Admin Note Display -->
                <div class="p-4 rounded-2xl bg-white border border-blue-200 text-xs shadow-2xs mb-4">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="font-bold text-blue-800 flex items-center gap-1.5">
                            <ion-icon name="shield-checkmark" class="text-blue-600 text-sm"></ion-icon>
                            <span>Saved Admin Note</span>
                        </span>
                    </div>
                    <p class="text-secondary-800 bg-blue-50 p-2.5 rounded-xl border border-blue-100 font-medium whitespace-pre-wrap"><?php echo htmlspecialchars($order['admin_note']); ?></p>
                </div>
                <?php endif; ?>

                <!-- Admin Note Form -->
                <div class="p-4 rounded-2xl bg-white border border-secondary-200 text-xs shadow-2xs print:hidden">
                    <form id="adminNoteForm" action="<?= $base ?>/admin/orders/update-notes" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <div class="flex items-center justify-between mb-2">
                            <label for="adminNoteTextarea" class="font-bold text-secondary-800 flex items-center gap-1.5 text-xs">
                                <ion-icon name="shield-checkmark" class="text-blue-600 text-base"></ion-icon>
                                <span>Admin / Internal Note (অ্যাডমিন নোট)</span>
                            </label>
                            <span id="noteSaveStatus" class="hidden text-[11px] font-bold text-emerald-600 items-center gap-1">
                                <ion-icon name="checkmark-circle" class="text-sm"></ion-icon> Saved!
                            </span>
                        </div>
                        <textarea id="adminNoteTextarea" name="admin_note" rows="3" placeholder="Add instructions or notes for this order (visible to staff & rider)..." class="w-full px-3 py-2.5 border border-secondary-300 rounded-xl text-xs bg-secondary-50 focus:bg-white focus:ring-2 focus:ring-primary-500 focus:outline-none transition-all"><?php echo htmlspecialchars($order['admin_note'] ?? ''); ?></textarea>
                        
                        <div class="mt-2.5 flex items-center justify-between">
                            <span class="text-[10px] text-secondary-400">Visible to riders & backoffice staff</span>
                            <button type="submit" id="saveAdminNoteBtn" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-bold text-xs rounded-xl shadow-sm transition-all flex items-center gap-1.5 cursor-pointer">
                                <ion-icon name="save-outline" class="text-sm"></ion-icon>
                                <span>Save Admin Note</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right: Financial Breakdown & Admin Adjust Amount Button -->
            <div class="flex flex-col items-end w-full">
                <div class="w-full max-w-xs space-y-2">
                    <div class="flex justify-between text-secondary-600 text-sm">
                        <span>Original Subtotal</span>
                        <span class="font-medium text-secondary-900 font-mono">৳ <?php echo number_format($origAmount, 2); ?></span>
                    </div>

                    <?php if ($hasDiscount): ?>
                        <div class="flex justify-between text-red-600 text-sm font-bold">
                            <span>Discount / Deduction</span>
                            <span class="font-mono">- ৳ <?php echo number_format($order['delivery_discount'], 2); ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="flex justify-between text-secondary-600 text-sm border-b border-secondary-200 pb-2">
                        <span>Delivery Fee</span>
                        <span class="font-medium text-secondary-900 font-mono">৳ <?php echo number_format($order['delivery_charge'] ?? 0, 2); ?></span>
                    </div>

                    <div class="flex justify-between pt-1">
                        <span class="text-lg font-bold text-secondary-800">Grand Total</span>
                        <span class="text-xl font-black text-primary-600 font-mono">৳ <?php echo number_format($order['total_amount'], 2); ?></span>
                    </div>

                    <!-- Admin Amount Adjustment Button (Opens Modal) -->
                    <?php if ($order['status'] !== 'cancelled'): ?>
                        <div class="pt-3 print:hidden">
                            <button type="button" onclick="document.getElementById('adjustAmountModal').classList.remove('hidden')" class="w-full py-2.5 px-3 border border-primary-200 bg-primary-50 hover:bg-primary-100 text-primary-700 font-bold text-xs rounded-xl transition-colors flex items-center justify-center gap-1.5 shadow-2xs cursor-pointer">
                                <ion-icon name="create-outline" class="text-sm"></ion-icon>
                                <span>Adjust Amount / Add Discount</span>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
</div>
</div>

<?php if (!empty($trackingHistory)): ?>
<div class="max-w-4xl mx-auto mt-6 bg-white rounded-3xl shadow-sm border border-secondary-200 overflow-hidden print:hidden">
    <div class="p-6 border-b border-secondary-100 bg-secondary-50/50">
        <h2 class="text-lg font-black text-secondary-900 flex items-center gap-2">
            <ion-icon name="time-outline" class="text-primary-600 text-xl"></ion-icon>
            Tracking Updates
        </h2>
    </div>
    <div class="p-6 relative overflow-hidden">
        <div class="absolute left-28 md:left-48 top-8 bottom-8 w-px bg-secondary-200 z-0"></div>
        
        <div class="space-y-6 relative z-10">
            <?php foreach ($trackingHistory as $index => $log): ?>
                <?php 
                    $logDate = date('M d, Y', strtotime($log['created_at']));
                    $logTime = date('h:i a', strtotime($log['created_at']));
                    
                    $statusColor = 'text-blue-600';
                    $statusBg = 'bg-blue-50';
                    $icon = 'create';
                    
                    if ($log['status'] === 'delivered') {
                        $statusColor = 'text-emerald-600';
                        $statusBg = 'bg-emerald-50';
                        $icon = 'checkmark';
                    } elseif ($log['status'] === 'cancelled') {
                        $statusColor = 'text-red-600';
                        $statusBg = 'bg-red-50';
                        $icon = 'close';
                    } elseif ($log['status'] === 'out_for_delivery') {
                        $statusColor = 'text-purple-600';
                        $statusBg = 'bg-purple-50';
                        $icon = 'bicycle';
                    }
                ?>
                <div class="flex gap-4 md:gap-8 items-start">
                    <div class="w-20 md:w-36 text-right shrink-0 pt-1">
                        <div class="text-[11px] font-bold text-primary-600"><?php echo $logDate; ?></div>
                        <div class="text-[10px] text-secondary-500 font-medium"><?php echo $logTime; ?></div>
                    </div>
                    
                    <div class="relative flex flex-col items-center shrink-0">
                        <div class="w-8 h-8 rounded-full <?php echo $statusBg; ?> <?php echo $statusColor; ?> flex items-center justify-center ring-4 ring-white shadow-sm z-10">
                            <ion-icon name="<?php echo $icon; ?>" class="text-sm font-bold"></ion-icon>
                        </div>
                    </div>
                    
                    <div class="flex-1 pt-1.5 pb-2">
                        <p class="text-sm font-medium text-secondary-800">
                            <?php echo htmlspecialchars($log['message']); ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>
<!-- Admin Adjust Amount Modal -->
<div id="adjustAmountModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4 backdrop-blur-sm print:hidden">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl relative">
        <div class="flex justify-between items-center mb-4 pb-2 border-b border-secondary-100">
            <div>
                <h3 class="font-black text-lg text-secondary-900 flex items-center gap-1.5">
                    <ion-icon name="cash-outline" class="text-primary-600"></ion-icon>
                    <span>Adjust Order Amount</span>
                </h3>
                <p class="text-xs text-secondary-400 mt-0.5">Order #<?php echo $order['id']; ?> • Current: ৳<?php echo number_format($order['total_amount'], 2); ?></p>
            </div>
            <button onclick="document.getElementById('adjustAmountModal').classList.add('hidden')" class="text-secondary-400 hover:text-secondary-600 cursor-pointer">
                <ion-icon name="close" class="text-2xl"></ion-icon>
            </button>
        </div>

        <form action="<?= $base ?>/admin/orders/adjust-amount" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">

            <!-- Adjustment Type -->
            <div>
                <label class="block text-xs font-bold text-secondary-700 mb-1.5 uppercase tracking-wider">Adjustment Method</label>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <label class="border border-secondary-200 p-2.5 rounded-xl flex items-center gap-2 cursor-pointer hover:bg-secondary-50">
                        <input type="radio" name="adjustment_type" value="discount" checked class="text-primary-600">
                        <span class="font-bold text-secondary-800">Apply Discount</span>
                    </label>
                    <label class="border border-secondary-200 p-2.5 rounded-xl flex items-center gap-2 cursor-pointer hover:bg-secondary-50">
                        <input type="radio" name="adjustment_type" value="new_total" class="text-primary-600">
                        <span class="font-bold text-secondary-800">Set New Total</span>
                    </label>
                </div>
            </div>

            <!-- Amount Value -->
            <div>
                <label class="block text-xs font-bold text-secondary-700 mb-1 uppercase tracking-wider">Amount (৳) *</label>
                <input type="number" step="any" min="0" name="amount_value" required placeholder="e.g. 50" class="w-full px-3 py-2 border border-secondary-300 rounded-xl text-sm font-bold bg-white focus:ring-2 focus:ring-primary-500 focus:outline-none font-mono">
                <p class="text-[10px] text-secondary-400 mt-1">If Discount: bill is reduced by this amount. If Set New Total: bill becomes this amount.</p>
            </div>

            <!-- Reason -->
            <div>
                <label class="block text-xs font-bold text-secondary-700 mb-1 uppercase tracking-wider">Reason / Audit Note</label>
                <input type="text" name="amount_change_reason" placeholder="e.g. Customer requested discount, damaged item..." class="w-full px-3 py-2 border border-secondary-300 rounded-xl text-xs bg-white focus:ring-2 focus:ring-primary-500 focus:outline-none">
            </div>

            <!-- Actions -->
            <div class="grid grid-cols-2 gap-3 pt-2">
                <button type="button" onclick="document.getElementById('adjustAmountModal').classList.add('hidden')" class="w-full py-2.5 rounded-xl border border-secondary-200 text-secondary-600 font-bold text-xs hover:bg-secondary-50 transition-colors cursor-pointer">
                    Cancel
                </button>
                <button type="submit" class="w-full py-2.5 rounded-xl bg-primary-600 text-white font-bold text-xs shadow-lg hover:bg-primary-700 transition-colors flex items-center justify-center gap-1 cursor-pointer">
                    <ion-icon name="checkmark" class="text-base"></ion-icon>
                    <span>Save Adjustment</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Admin Catch-Weight Item Adjustment Modal (মাছ, মাংস, সবজির আসল ওজন ও বিল সমন্বয়) -->
<div id="catchWeightModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4 backdrop-blur-sm print:hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl relative">
        <div class="flex justify-between items-center mb-4 pb-2 border-b border-secondary-100">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-700 shadow-2xs">
                    <ion-icon name="scale" class="text-xl"></ion-icon>
                </div>
                <div>
                    <h3 class="font-black text-base text-secondary-900">আসল ওজন ও বিল সমন্বয়</h3>
                    <p class="text-xs text-secondary-400">Actual Catch-Weight Adjustment</p>
                </div>
            </div>
            <button type="button" onclick="closeCatchWeightModal()" class="text-secondary-400 hover:text-secondary-600 cursor-pointer">
                <ion-icon name="close" class="text-2xl"></ion-icon>
            </button>
        </div>

        <form action="<?= $base ?>/admin/orders/adjust-item-weight" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
            <input type="hidden" name="item_id" id="cwItemId" value="">

            <!-- Product Info Card -->
            <div class="p-3 bg-secondary-50/80 rounded-2xl border border-secondary-200/80">
                <div class="font-bold text-secondary-900 text-sm" id="cwProductName"></div>
                <div class="flex items-center gap-2 mt-1 text-xs text-secondary-600">
                    <span>প্রতি ইউনিট/কেজি দর: ৳ <strong id="cwUnitPrice" class="text-emerald-700 font-mono"></strong></span>
                    <span>•</span>
                    <span>বর্তমান রেকর্ড: <strong id="cwOldQty" class="text-secondary-800 font-mono"></strong></span>
                </div>
            </div>

            <!-- Weight Input Mode (Kg / Gm) -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-bold text-secondary-700 uppercase tracking-wider">আসল মাপা ওজন (Actual Weight) *</label>
                    <div class="inline-flex rounded-lg border border-secondary-200 bg-secondary-50 p-0.5 text-[11px] font-bold">
                        <button type="button" id="cwUnitKgBtn" onclick="switchCwUnitMode('kg')" class="px-2.5 py-0.5 rounded-md bg-white text-secondary-900 shadow-2xs">কেজি (kg)</button>
                        <button type="button" id="cwUnitGmBtn" onclick="switchCwUnitMode('gm')" class="px-2.5 py-0.5 rounded-md text-secondary-500 hover:text-secondary-800">গ্রাম (gm)</button>
                    </div>
                </div>

                <div class="relative">
                    <input type="number" step="any" min="0.001" id="cwWeightInput" required 
                           oninput="onCwWeightInputChange()"
                           placeholder="যেমন: ১.১ বা ১.১২" 
                           class="w-full pl-3 pr-20 py-2.5 border-2 border-amber-300 rounded-xl text-base font-black bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none font-mono text-secondary-900">
                    <span id="cwUnitSuffix" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-xs font-bold text-secondary-500 pointer-events-none">কেজি (kg)</span>
                </div>
                <input type="hidden" name="actual_quantity" id="cwFinalQuantityInput" value="">
                
                <!-- Quick Helper Buttons -->
                <div class="flex flex-wrap gap-1.5 mt-2 text-[11px]">
                    <span class="text-secondary-400 font-bold self-center text-[10px]">কুইক প্রিসেট:</span>
                    <button type="button" onclick="setQuickCwWeight(0.200)" class="px-2 py-0.5 bg-secondary-100 hover:bg-secondary-200 text-secondary-700 font-bold rounded-md">২০০ গ্রাম</button>
                    <button type="button" onclick="setQuickCwWeight(0.250)" class="px-2 py-0.5 bg-secondary-100 hover:bg-secondary-200 text-secondary-700 font-bold rounded-md">২৫০ গ্রাম</button>
                    <button type="button" onclick="setQuickCwWeight(0.500)" class="px-2 py-0.5 bg-secondary-100 hover:bg-secondary-200 text-secondary-700 font-bold rounded-md">৫০০ গ্রাম</button>
                    <button type="button" onclick="setQuickCwWeight(1.000)" class="px-2 py-0.5 bg-secondary-100 hover:bg-secondary-200 text-secondary-700 font-bold rounded-md">১ কেজি</button>
                    <button type="button" onclick="setQuickCwWeight(1.100)" class="px-2 py-0.5 bg-amber-100 hover:bg-amber-200 text-amber-900 font-bold rounded-md">১ কেজি ১০০ গ্রাম</button>
                    <button type="button" onclick="setQuickCwWeight(1.200)" class="px-2 py-0.5 bg-secondary-100 hover:bg-secondary-200 text-secondary-700 font-bold rounded-md">১ কেজি ২০০ গ্রাম</button>
                    <button type="button" onclick="setQuickCwWeight(1.500)" class="px-2 py-0.5 bg-secondary-100 hover:bg-secondary-200 text-secondary-700 font-bold rounded-md">১.৫ কেজি</button>
                </div>
            </div>

            <!-- Live Calculation Summary Card -->
            <div class="p-3.5 bg-amber-50/70 border border-amber-200 rounded-2xl space-y-2 text-xs">
                <div class="flex justify-between items-center text-secondary-600">
                    <span>পূর্ববর্তী আইটেম বিল:</span>
                    <span class="font-bold font-mono">৳ <span id="cwOldTotal">0.00</span></span>
                </div>
                <div class="flex justify-between items-center text-amber-900 font-bold">
                    <span>সংশোধিত নতুন আইটেম বিল:</span>
                    <span class="font-black text-sm font-mono text-emerald-700">৳ <span id="cwNewTotal">0.00</span></span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-amber-200/80 font-bold">
                    <span>অর্ডারের মোট বিল পরিবর্তন:</span>
                    <span id="cwDiffBadge" class="font-black font-mono text-xs px-2 py-0.5 rounded-full bg-white border border-amber-200"></span>
                </div>
                <div class="flex justify-between items-center pt-1 text-secondary-700 text-xs font-bold">
                    <span>সংশোধিত মোট অর্ডার বিল:</span>
                    <span class="font-black text-sm text-primary-700 font-mono">৳ <span id="cwNewOrderTotal">0.00</span></span>
                </div>
            </div>

            <!-- Reason / Audit Note -->
            <div>
                <label class="block text-xs font-bold text-secondary-700 mb-1 uppercase tracking-wider">সমন্বয়ের কারণ / কাস্টমার নোট</label>
                <input type="text" name="weight_reason" id="cwReasonInput" 
                       placeholder="যেমন: ১ কেজি মাছের প্রকৃত ওজন ১ কেজি ১০০ গ্রাম পাওয়া গেছে" 
                       class="w-full px-3 py-2 border border-secondary-300 rounded-xl text-xs bg-white focus:ring-2 focus:ring-primary-500 focus:outline-none">
            </div>

            <!-- Actions -->
            <div class="grid grid-cols-2 gap-3 pt-2">
                <button type="button" onclick="closeCatchWeightModal()" class="w-full py-2.5 rounded-xl border border-secondary-200 text-secondary-600 font-bold text-xs hover:bg-secondary-50 transition-colors cursor-pointer">
                    বাতিল
                </button>
                <button type="submit" class="w-full py-2.5 rounded-xl bg-emerald-600 text-white font-bold text-xs shadow-lg hover:bg-emerald-700 transition-colors flex items-center justify-center gap-1 cursor-pointer">
                    <ion-icon name="checkmark-circle" class="text-base"></ion-icon>
                    <span>ওজন ও বিল সেভ করুন</span>
                </button>
            </div>
        </form>
    </div>
</div>

<div class="max-w-4xl mx-auto mt-6 flex justify-end gap-3 print:hidden">
    <a href="<?= $base ?>/admin/orders" class="px-4 py-2 text-secondary-600 bg-white border border-secondary-200 rounded-lg font-medium hover:bg-secondary-50">Back to List</a>
    <a href="<?= $base ?>/admin/orders/edit?id=<?php echo $order['id']; ?>" class="px-4 py-2 text-white bg-blue-600 rounded-lg font-medium hover:bg-blue-700 flex items-center">
        <ion-icon name="create-outline" class="mr-2"></ion-icon> Edit Order
    </a>
    <a href="<?= $base ?>/admin/orders/pos-receipt?id=<?php echo $order['id']; ?>" target="_blank" class="px-4 py-2 text-white bg-emerald-600 rounded-lg font-bold hover:bg-emerald-700 flex items-center shadow-sm">
        <ion-icon name="receipt-outline" class="mr-2 text-lg"></ion-icon> Print 3" POS Receipt (80mm)
    </a>
    <button onclick="window.print()" class="px-4 py-2 text-white bg-primary-600 rounded-lg font-medium hover:bg-primary-700 flex items-center">
        <ion-icon name="print-outline" class="mr-2"></ion-icon> Print A4 Invoice
    </button>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #invoice, #invoice * {
            visibility: visible;
        }
        #invoice {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            border: none;
            box-shadow: none;
        }
        aside, header, footer {
            display: none !important;
        }
        main {
            margin: 0 !important;
            padding: 0 !important;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const noteForm = document.getElementById('adminNoteForm');
    if (noteForm) {
        noteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('saveAdminNoteBtn');
            const statusSpan = document.getElementById('noteSaveStatus');
            const originalHtml = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = '<ion-icon name="refresh-outline" class="animate-spin text-sm"></ion-icon> <span>Saving...</span>';
            if (statusSpan) {
                statusSpan.classList.add('hidden');
                statusSpan.classList.remove('inline-flex');
            }

            const formData = new FormData(noteForm);

            fetch(noteForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => {
                if (res.ok) {
                    return res.json().catch(() => ({ success: true }));
                }
                throw new Error('Network error');
            })
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                if (data && data.success !== false) {
                    if (statusSpan) {
                        statusSpan.classList.remove('hidden');
                        statusSpan.classList.add('inline-flex');
                        statusSpan.innerHTML = '<ion-icon name="checkmark-circle" class="text-sm text-emerald-600"></ion-icon> <span class="font-bold text-emerald-700">Note Saved Successfully!</span>';
                        setTimeout(() => {
                            statusSpan.classList.add('hidden');
                            statusSpan.classList.remove('inline-flex');
                        }, 4000);
                    }
                } else {
                    alert(data.error || 'Failed to save note');
                }
            })
            .catch(err => {
                // Fallback to standard form submit if fetch fails
                noteForm.submit();
            });
        });
    }
});
// --- CATCH WEIGHT ADJUSTMENT MODAL JS ---
let currentCwItem = null;
let currentCwUnitMode = 'kg'; // 'kg' or 'gm'
const orderCurrentTotal = <?= floatval($order['total_amount']) ?>;

function openCatchWeightModal(item) {
    currentCwItem = item;
    currentCwUnitMode = 'kg';
    
    document.getElementById('cwItemId').value = item.id;
    document.getElementById('cwProductName').textContent = item.product_name + (item.unit_title ? ` (${item.unit_title})` : '');
    document.getElementById('cwUnitPrice').textContent = Number(item.price).toFixed(2);
    
    const qty = parseFloat(item.quantity) || 1;
    const unit = item.base_unit || 'kg';
    document.getElementById('cwOldQty').textContent = `${qty} ${unit}`;
    
    const oldLineTotal = Math.round(qty * parseFloat(item.price) * 100) / 100;
    document.getElementById('cwOldTotal').textContent = oldLineTotal.toFixed(2);
    
    // Set initial input value
    const input = document.getElementById('cwWeightInput');
    input.value = qty;
    
    switchCwUnitMode('kg');
    onCwWeightInputChange();
    
    document.getElementById('catchWeightModal').classList.remove('hidden');
    input.focus();
    input.select();
}

function closeCatchWeightModal() {
    document.getElementById('catchWeightModal').classList.add('hidden');
    currentCwItem = null;
}

function switchCwUnitMode(mode) {
    currentCwUnitMode = mode;
    const btnKg = document.getElementById('cwUnitKgBtn');
    const btnGm = document.getElementById('cwUnitGmBtn');
    const suffix = document.getElementById('cwUnitSuffix');
    const input = document.getElementById('cwWeightInput');

    const curKg = parseFloat(document.getElementById('cwFinalQuantityInput').value) || (parseFloat(input.value) || 1);

    if (mode === 'gm') {
        btnGm.className = 'px-2.5 py-0.5 rounded-md bg-white text-secondary-900 shadow-2xs font-bold';
        btnKg.className = 'px-2.5 py-0.5 rounded-md text-secondary-500 hover:text-secondary-800 font-bold';
        suffix.textContent = 'গ্রাম (gm)';
        input.value = Math.round(curKg * 1000);
        input.step = '1';
    } else {
        btnKg.className = 'px-2.5 py-0.5 rounded-md bg-white text-secondary-900 shadow-2xs font-bold';
        btnGm.className = 'px-2.5 py-0.5 rounded-md text-secondary-500 hover:text-secondary-800 font-bold';
        suffix.textContent = 'কেজি (kg)';
        input.value = curKg;
        input.step = '0.001';
    }
    onCwWeightInputChange();
}

function setQuickCwWeight(kgVal) {
    const input = document.getElementById('cwWeightInput');
    if (currentCwUnitMode === 'gm') {
        input.value = Math.round(kgVal * 1000);
    } else {
        input.value = kgVal;
    }
    onCwWeightInputChange();
}

function onCwWeightInputChange() {
    if (!currentCwItem) return;
    const input = document.getElementById('cwWeightInput');
    const rawVal = parseFloat(input.value) || 0;
    
    // Convert to Kg if typed in grams
    const actualKg = (currentCwUnitMode === 'gm') ? (rawVal / 1000) : rawVal;
    document.getElementById('cwFinalQuantityInput').value = actualKg;

    const unitPrice = parseFloat(currentCwItem.price) || 0;
    const oldQty = parseFloat(currentCwItem.quantity) || 1;
    const oldTotal = Math.round(oldQty * unitPrice * 100) / 100;
    const newTotal = Math.round(actualKg * unitPrice * 100) / 100;
    const diff = Math.round((newTotal - oldTotal) * 100) / 100;

    document.getElementById('cwNewTotal').textContent = newTotal.toFixed(2);
    
    const diffBadge = document.getElementById('cwDiffBadge');
    if (diff > 0) {
        diffBadge.textContent = `+ ৳${diff.toFixed(2)} (বিল বৃদ্ধি)`;
        diffBadge.className = 'font-black font-mono text-xs px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300';
    } else if (diff < 0) {
        diffBadge.textContent = `- ৳${Math.abs(diff).toFixed(2)} (বিল হ্রাস)`;
        diffBadge.className = 'font-black font-mono text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-800 border border-red-300';
    } else {
        diffBadge.textContent = '৳0.00 (অপরিবর্তিত)';
        diffBadge.className = 'font-black font-mono text-xs px-2 py-0.5 rounded-full bg-secondary-100 text-secondary-700 border border-secondary-200';
    }

    const newOrderTotal = Math.max(0, Math.round((orderCurrentTotal + diff) * 100) / 100);
    document.getElementById('cwNewOrderTotal').textContent = newOrderTotal.toFixed(2);

    // Auto suggest helpful reason
    const reasonInput = document.getElementById('cwReasonInput');
    if (reasonInput && (!reasonInput.value || reasonInput.value.startsWith('ওজন অনুযায়ী সমন্বয়'))) {
        reasonInput.value = `ওজন অনুযায়ী সমন্বয়: ${oldQty} কেজির বদলে প্রকৃত ওজন ${actualKg} কেজি পাওয়া গেছে (${diff >= 0 ? '+' : '-'}৳${Math.abs(diff).toFixed(2)})`;
    }
}

// Close on escape
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeCatchWeightModal();
    }
});
