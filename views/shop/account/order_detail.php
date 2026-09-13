<?php ob_start();
$statusColors = [
    'processing'=>'bg-yellow-100 text-yellow-800 border-yellow-200','confirmed'=>'bg-blue-100 text-blue-800 border-blue-200',
    'packaging'=>'bg-indigo-100 text-indigo-800 border-indigo-200','picking'=>'bg-purple-100 text-purple-800 border-purple-200',
    'dispatch'=>'bg-orange-100 text-orange-800 border-orange-200','shipped'=>'bg-teal-100 text-teal-800 border-teal-200',
    'out_for_delivery'=>'bg-emerald-100 text-emerald-800 border-emerald-200','delivered'=>'bg-green-100 text-green-800 border-green-200',
    'cancelled'=>'bg-red-100 text-red-800 border-red-200','returned'=>'bg-gray-100 text-gray-800 border-gray-200',
];
?>

<div class="space-y-5">
    <!-- Back + Header -->
    <div class="flex items-center gap-3">
        <a href="/sodai-dorkar/public/account/orders" class="p-2 rounded-xl bg-white border border-gray-200 text-gray-500 hover:text-emerald-600 transition-colors">
            <ion-icon name="arrow-back" class="text-base block"></ion-icon>
        </a>
        <div>
            <h1 class="font-black text-gray-900 text-lg">অর্ডার #<?= $order['id'] ?></h1>
            <p class="text-xs text-gray-500"><?= date('d M Y, h:i a', strtotime($order['created_at'])) ?></p>
        </div>
        <span class="ml-auto text-xs px-3 py-1.5 rounded-full font-bold border <?= $statusColors[$order['status']] ?? 'bg-gray-100 text-gray-600 border-gray-200' ?>">
            <?= ucfirst(str_replace('_', ' ', $order['status'])) ?>
        </span>
    </div>

    <!-- Order Items -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-50">
            <h2 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                <ion-icon name="list-outline" class="text-emerald-600"></ion-icon> অর্ডার আইটেম
            </h2>
        </div>
        <div class="divide-y divide-gray-50">
            <?php foreach ($order['items'] as $item): ?>
            <div class="flex items-center gap-3 px-4 py-3">
                <?php if (!empty($item['image_path'])): ?>
                <img src="<?= htmlspecialchars($item['image_path']) ?>" alt="" class="w-12 h-12 object-contain rounded-lg bg-gray-50 border border-gray-100">
                <?php else: ?>
                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400">
                    <ion-icon name="image-outline" class="text-xl"></ion-icon>
                </div>
                <?php endif; ?>
                <div class="flex-1 min-w-0">
                    <div class="font-semibold text-sm text-gray-800 truncate"><?= htmlspecialchars($item['product_name']) ?></div>
                    <?php if (!empty($item['unit_title'])): ?>
                    <div class="text-xs text-gray-500"><?= htmlspecialchars($item['unit_title']) ?></div>
                    <?php endif; ?>
                    <div class="text-xs text-gray-500">পরিমাণ: <?= $item['quantity'] ?> × ৳<?= number_format($item['price'], 2) ?></div>
                </div>
                <div class="font-black text-gray-900 text-sm">৳<?= number_format($item['quantity'] * $item['price'], 2) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="px-4 py-3 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
            <span class="font-semibold text-gray-700 text-sm">মোট</span>
            <span class="font-black text-lg text-emerald-700">৳<?= number_format($order['total_amount'], 2) ?></span>
        </div>
    </div>

    <!-- Delivery Info -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <h2 class="font-bold text-gray-800 text-sm mb-3 flex items-center gap-2">
            <ion-icon name="location-outline" class="text-emerald-600"></ion-icon> ডেলিভারি তথ্য
        </h2>
        <div class="text-sm text-gray-600 space-y-1">
            <div><span class="font-medium text-gray-700">ঠিকানা:</span> <?= htmlspecialchars($order['delivery_address'] ?? 'N/A') ?></div>
            <div><span class="font-medium text-gray-700">ফোন:</span> <?= htmlspecialchars($order['contact_number'] ?? 'N/A') ?></div>
            <?php if (!empty($order['admin_note'])): ?>
            <div class="mt-2 bg-blue-50 rounded-lg p-2.5 border border-blue-100 text-blue-800">
                <span class="font-semibold">Admin Note:</span> <?= htmlspecialchars($order['admin_note']) ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tracking Timeline -->
    <?php if (!empty($trackingHistory)): ?>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-50 bg-gray-50/50">
            <h2 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                <ion-icon name="time-outline" class="text-emerald-600"></ion-icon> ট্র্যাকিং আপডেট
            </h2>
        </div>
        <div class="p-4 relative">
            <div class="absolute left-24 top-6 bottom-6 w-px bg-gray-200"></div>
            <div class="space-y-5">
                <?php foreach ($trackingHistory as $log):
                    $icon = 'create';
                    $iconBg = 'bg-blue-50 text-blue-600';
                    if ($log['status'] === 'delivered') { $icon = 'checkmark'; $iconBg = 'bg-emerald-50 text-emerald-600'; }
                    elseif ($log['status'] === 'cancelled') { $icon = 'close'; $iconBg = 'bg-red-50 text-red-600'; }
                    elseif ($log['status'] === 'out_for_delivery') { $icon = 'bicycle'; $iconBg = 'bg-purple-50 text-purple-600'; }
                    elseif ($log['status'] === 'confirmed') { $icon = 'checkmark-circle'; $iconBg = 'bg-blue-50 text-blue-600'; }
                ?>
                <div class="flex gap-5 items-start">
                    <div class="w-20 text-right shrink-0 pt-1">
                        <div class="text-[10px] font-bold text-emerald-700"><?= date('M d', strtotime($log['created_at'])) ?></div>
                        <div class="text-[10px] text-gray-400"><?= date('h:i a', strtotime($log['created_at'])) ?></div>
                    </div>
                    <div class="w-7 h-7 rounded-full <?= $iconBg ?> flex items-center justify-center ring-4 ring-white shadow-sm shrink-0 z-10">
                        <ion-icon name="<?= $icon ?>" class="text-xs"></ion-icon>
                    </div>
                    <div class="flex-1 pt-1">
                        <p class="text-sm text-gray-700 font-medium"><?= htmlspecialchars($log['message']) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>

