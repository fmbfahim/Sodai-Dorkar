<?php 
ob_start();
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';

$statusColors = [
    'processing'       => 'bg-amber-100 text-amber-800 border-amber-200',
    'confirmed'        => 'bg-blue-100 text-blue-800 border-blue-200',
    'packaging'        => 'bg-indigo-100 text-indigo-800 border-indigo-200',
    'picking'          => 'bg-purple-100 text-purple-800 border-purple-200',
    'dispatch'         => 'bg-orange-100 text-orange-800 border-orange-200',
    'shipped'          => 'bg-teal-100 text-teal-800 border-teal-200',
    'out_for_delivery' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
    'delivered'        => 'bg-green-100 text-green-800 border-green-200',
    'cancelled'        => 'bg-rose-100 text-rose-800 border-rose-200',
    'returned'         => 'bg-gray-100 text-gray-800 border-gray-200',
];
?>

<div class="space-y-5">
    <!-- Back + Header -->
    <div class="flex items-center justify-between gap-3 flex-wrap bg-white p-4 rounded-2xl border border-gray-200/80 shadow-2xs">
        <div class="flex items-center gap-3">
            <a href="<?= $base ?>/account/orders" class="p-2 rounded-xl bg-gray-50 border border-gray-200 text-gray-600 hover:text-emerald-600 hover:bg-emerald-50 transition-colors" title="Back to Orders">
                <ion-icon name="arrow-back" class="text-base block"></ion-icon>
            </a>
            <div>
                <h1 class="font-black text-gray-900 text-base sm:text-lg leading-tight">Order #<?= $order['id'] ?></h1>
                <p class="text-xs text-gray-500 mt-0.5"><?= date('M d, Y, h:i A', strtotime($order['created_at'])) ?></p>
            </div>
        </div>
        <span class="text-xs px-3 py-1 rounded-full font-bold border <?= $statusColors[$order['status']] ?? 'bg-gray-100 text-gray-600 border-gray-200' ?>">
            <?= ucfirst(str_replace('_', ' ', $order['status'])) ?>
        </span>
    </div>

    <!-- Order Items List -->
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-2xs overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-bold text-gray-900 text-sm sm:text-base flex items-center gap-2">
                <ion-icon name="receipt-outline" class="text-emerald-600"></ion-icon> 
                <span>Ordered Items (<?= count($order['items'] ?? []) ?>)</span>
            </h2>
        </div>
        <div class="divide-y divide-gray-100">
            <?php foreach (($order['items'] ?? []) as $item): 
                $img = !empty($item['image_path']) ? htmlspecialchars($item['image_path']) : $base . '/images/default-product.svg';
                if (strpos($img, 'http') !== 0 && strpos($img, $base) !== 0 && strpos($img, '/') === 0) {
                    $img = $base . $img;
                }
            ?>
            <div class="flex items-center gap-3 sm:gap-4 p-3.5 sm:p-4">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gray-50 rounded-xl border border-gray-100 flex items-center justify-center flex-shrink-0 p-1">
                    <img src="<?= $img ?>" alt="" class="max-h-full max-w-full object-contain" onerror="this.src='<?= $base ?>/images/default-product.svg'">
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-xs sm:text-sm text-gray-900 truncate"><?= htmlspecialchars($item['product_name'] ?? 'Product') ?></div>
                    <?php if (!empty($item['unit_title'])): ?>
                    <div class="text-[11px] text-gray-400 font-medium"><?= htmlspecialchars($item['unit_title']) ?></div>
                    <?php endif; ?>
                    <div class="text-xs text-gray-500 mt-0.5">
                        Qty: <span class="font-bold text-gray-700"><?= $item['quantity'] ?></span> × ৳<?= number_format($item['price'], 2) ?>
                    </div>
                </div>
                <div class="font-black text-gray-900 text-sm sm:text-base text-right flex-shrink-0">
                    ৳<?= number_format($item['quantity'] * $item['price'], 2) ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <!-- Order Totals Footer -->
        <div class="p-4 bg-gray-50/80 border-t border-gray-100 space-y-1.5 text-xs sm:text-sm">
            <div class="flex justify-between items-center text-gray-500">
                <span>Subtotal</span>
                <span class="font-bold text-gray-800">৳<?= number_format($order['total_amount'], 2) ?></span>
            </div>
            <div class="flex justify-between items-center text-gray-500">
                <span>Delivery Charge</span>
                <span class="font-bold text-emerald-600">Free</span>
            </div>
            <div class="flex justify-between items-center pt-2 border-t border-gray-200/80 font-black text-base text-gray-900">
                <span>Grand Total</span>
                <span class="text-emerald-700 text-lg">৳<?= number_format($order['total_amount'], 2) ?></span>
            </div>
        </div>
    </div>

    <!-- Delivery Information -->
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-2xs p-4 sm:p-5">
        <h2 class="font-bold text-gray-900 text-sm sm:text-base mb-3 flex items-center gap-2">
            <ion-icon name="location-outline" class="text-emerald-600"></ion-icon> 
            <span>Delivery & Contact Info</span>
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs sm:text-sm">
            <div class="bg-gray-50 p-3.5 rounded-xl border border-gray-100">
                <span class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Delivery Address</span>
                <span class="font-medium text-gray-800 leading-relaxed"><?= htmlspecialchars($order['delivery_address'] ?? 'N/A') ?></span>
            </div>
            <div class="bg-gray-50 p-3.5 rounded-xl border border-gray-100">
                <span class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Contact Phone</span>
                <span class="font-medium text-gray-800"><?= htmlspecialchars($order['contact_number'] ?? 'N/A') ?></span>
            </div>
        </div>
        <?php if (!empty($order['admin_note'])): ?>
        <div class="mt-3 bg-blue-50/80 border border-blue-100 rounded-xl p-3.5 text-xs text-blue-900 flex items-start gap-2">
            <ion-icon name="information-circle-outline" class="text-base text-blue-600 mt-0.5 flex-shrink-0"></ion-icon>
            <div>
                <span class="font-bold">Store Notice:</span> <?= htmlspecialchars($order['admin_note']) ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Tracking Timeline -->
    <?php if (!empty($trackingHistory)): ?>
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-2xs overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-gray-50/50">
            <h2 class="font-bold text-gray-900 text-sm sm:text-base flex items-center gap-2">
                <ion-icon name="time-outline" class="text-emerald-600"></ion-icon> 
                <span>Shipment Tracking History</span>
            </h2>
        </div>
        <div class="p-4 sm:p-5">
            <div class="space-y-4 relative before:absolute before:left-4 before:top-2 before:bottom-2 before:w-0.5 before:bg-gray-200">
                <?php foreach ($trackingHistory as $log):
                    $icon = 'create-outline';
                    $iconBg = 'bg-blue-50 text-blue-600 border-blue-200';
                    if ($log['status'] === 'delivered') { 
                        $icon = 'checkmark-outline'; 
                        $iconBg = 'bg-emerald-50 text-emerald-600 border-emerald-300'; 
                    } elseif ($log['status'] === 'cancelled') { 
                        $icon = 'close-outline'; 
                        $iconBg = 'bg-rose-50 text-rose-600 border-rose-300'; 
                    } elseif ($log['status'] === 'out_for_delivery') { 
                        $icon = 'bicycle-outline'; 
                        $iconBg = 'bg-purple-50 text-purple-600 border-purple-300'; 
                    } elseif ($log['status'] === 'confirmed') { 
                        $icon = 'checkmark-circle-outline'; 
                        $iconBg = 'bg-blue-50 text-blue-600 border-blue-300'; 
                    }
                ?>
                <div class="flex items-start gap-3.5 relative pl-1">
                    <div class="w-7 h-7 rounded-full bg-white border-2 <?= $iconBg ?> flex items-center justify-center flex-shrink-0 z-10 shadow-2xs">
                        <ion-icon name="<?= $icon ?>" class="text-xs"></ion-icon>
                    </div>
                    <div class="flex-1 min-w-0 bg-gray-50/70 p-3 rounded-xl border border-gray-100">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-1">
                            <span class="font-bold text-xs sm:text-sm text-gray-900"><?= ucfirst(str_replace('_', ' ', $log['status'])) ?></span>
                            <span class="text-[11px] text-gray-400"><?= date('M d, Y, h:i A', strtotime($log['created_at'])) ?></span>
                        </div>
                        <p class="text-xs text-gray-600 leading-relaxed"><?= htmlspecialchars($log['message']) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>
