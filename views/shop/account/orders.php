<?php ob_start();
$statusColors = [
    'processing'=>'bg-yellow-100 text-yellow-800','confirmed'=>'bg-blue-100 text-blue-800',
    'packaging'=>'bg-indigo-100 text-indigo-800','picking'=>'bg-purple-100 text-purple-800',
    'dispatch'=>'bg-orange-100 text-orange-800','shipped'=>'bg-teal-100 text-teal-800',
    'out_for_delivery'=>'bg-emerald-100 text-emerald-800','delivered'=>'bg-green-100 text-green-800',
    'cancelled'=>'bg-red-100 text-red-800','returned'=>'bg-gray-100 text-gray-800',
];
$tabs = ['all'=>'সব','processing'=>'প্রসেসিং','confirmed'=>'কনফার্মড','out_for_delivery'=>'ডেলিভারিতে','delivered'=>'ডেলিভারড','cancelled'=>'ক্যান্সেলড'];
?>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-gray-100">
        <h1 class="font-black text-gray-900 text-lg flex items-center gap-2">
            <ion-icon name="bag-handle" class="text-emerald-600"></ion-icon> আমার অর্ডারসমূহ
        </h1>
    </div>

    <!-- Status Tabs -->
    <div class="flex overflow-x-auto border-b border-gray-100 bg-gray-50/50 px-4 gap-1 py-2">
        <?php foreach ($tabs as $key => $label): ?>
        <a href="?status=<?= $key ?>"
           class="flex-shrink-0 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all <?= $statusFilter === $key ? 'bg-emerald-600 text-white shadow-sm' : 'text-gray-600 hover:bg-white hover:shadow-sm' ?>">
            <?= $label ?>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Orders List -->
    <?php if (empty($orders)): ?>
    <div class="p-16 text-center text-gray-400">
        <ion-icon name="bag-outline" class="text-5xl mb-3 block"></ion-icon>
        <p class="font-semibold">এই স্ট্যাটাসে কোনো অর্ডার নেই।</p>
    </div>
    <?php else: ?>
    <div class="divide-y divide-gray-50">
        <?php foreach ($orders as $o): ?>
        <a href="/sodai-dorkar/public/account/order-detail?id=<?= $o['id'] ?>"
           class="flex items-center justify-between px-5 py-4 hover:bg-emerald-50/30 transition-colors group">
            <div>
                <div class="font-bold text-gray-800 text-sm group-hover:text-emerald-700 transition-colors">
                    অর্ডার #<?= $o['id'] ?>
                </div>
                <div class="text-xs text-gray-500 mt-0.5 flex items-center gap-2">
                    <ion-icon name="calendar-outline" class="text-xs"></ion-icon>
                    <?= date('d M Y, h:i a', strtotime($o['created_at'])) ?>
                </div>
            </div>
            <div class="text-right flex flex-col items-end gap-1">
                <div class="font-black text-gray-900 text-sm">৳<?= number_format($o['total_amount'], 2) ?></div>
                <span class="text-[11px] px-2.5 py-0.5 rounded-full font-semibold <?= $statusColors[$o['status']] ?? 'bg-gray-100 text-gray-600' ?>">
                    <?= ucfirst(str_replace('_', ' ', $o['status'])) ?>
                </span>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>

