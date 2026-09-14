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

$tabs = [
    'all'              => 'All Orders',
    'processing'       => 'Processing',
    'confirmed'        => 'Confirmed',
    'out_for_delivery' => 'On Delivery',
    'delivered'        => 'Delivered',
    'cancelled'        => 'Cancelled'
];
?>

<div class="bg-white rounded-2xl border border-gray-200/80 shadow-2xs overflow-hidden">
    <!-- Header -->
    <div class="p-4 sm:p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
        <h1 class="font-black text-gray-900 text-base sm:text-lg flex items-center gap-2">
            <ion-icon name="bag-handle" class="text-emerald-600 text-xl"></ion-icon> 
            <span>My Orders</span>
        </h1>
        <div class="text-xs text-gray-400">
            <?= count($orders) ?> order<?= count($orders) === 1 ? '' : 's' ?> listed
        </div>
    </div>

    <!-- Status Tabs (Scrollable on mobile) -->
    <div class="flex overflow-x-auto no-scrollbar border-b border-gray-100 bg-gray-50/60 px-3 sm:px-4 gap-1.5 py-2.5">
        <?php foreach ($tabs as $key => $label): ?>
        <a href="?status=<?= $key ?>"
           class="flex-shrink-0 px-3 py-1.5 rounded-lg text-xs font-bold transition-all <?= ($statusFilter ?? 'all') === $key ? 'bg-emerald-600 text-white shadow-xs' : 'text-gray-600 hover:bg-white hover:text-gray-900' ?>">
            <?= $label ?>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Orders List -->
    <?php if (empty($orders)): ?>
    <div class="p-12 sm:p-16 text-center text-gray-400">
        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
            <ion-icon name="bag-outline" class="text-3xl"></ion-icon>
        </div>
        <p class="font-bold text-gray-700 text-sm">No orders found in this filter.</p>
        <p class="text-xs text-gray-400 mt-1 max-w-xs mx-auto">Try selecting another status tab or visit our shop to explore items.</p>
        <a href="<?= $base ?>/" class="mt-4 inline-block bg-emerald-600 text-white text-xs font-bold px-5 py-2.5 rounded-xl hover:bg-emerald-700 transition-colors shadow-xs">
            Shop Fresh Items
        </a>
    </div>
    <?php else: ?>
    <div class="divide-y divide-gray-100">
        <?php foreach ($orders as $o): ?>
        <a href="<?= $base ?>/account/order-detail?id=<?= $o['id'] ?>"
           class="flex items-center justify-between p-4 sm:p-5 hover:bg-gray-50/80 transition-colors group">
            <div class="min-w-0 pr-3">
                <div class="font-bold text-gray-900 text-sm group-hover:text-emerald-600 transition-colors flex items-center gap-2">
                    <span>Order #<?= $o['id'] ?></span>
                    <span class="text-xs text-gray-400 hidden sm:inline">•</span>
                    <span class="text-xs text-gray-500 font-normal hidden sm:inline"><?= date('M d, Y, h:i A', strtotime($o['created_at'])) ?></span>
                </div>
                <div class="text-[11px] text-gray-400 sm:hidden mt-0.5 flex items-center gap-1">
                    <ion-icon name="calendar-outline" class="text-xs"></ion-icon>
                    <span><?= date('M d, Y, h:i A', strtotime($o['created_at'])) ?></span>
                </div>
            </div>
            <div class="text-right flex items-center gap-3 flex-shrink-0">
                <div>
                    <div class="font-black text-gray-900 text-sm sm:text-base">৳<?= number_format($o['total_amount'], 2) ?></div>
                    <span class="inline-block text-[10px] sm:text-[11px] px-2.5 py-0.5 rounded-full font-bold border mt-0.5 <?= $statusColors[$o['status']] ?? 'bg-gray-100 text-gray-600 border-gray-200' ?>">
                        <?= ucfirst(str_replace('_', ' ', $o['status'])) ?>
                    </span>
                </div>
                <div class="text-gray-300 group-hover:text-emerald-600 group-hover:translate-x-0.5 transition-all text-lg">
                    <ion-icon name="chevron-forward-outline"></ion-icon>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>
