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

<!-- Welcome Banner -->
<div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-2xl p-5 sm:p-6 text-white mb-6 shadow-sm relative overflow-hidden">
    <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/10 rounded-full pointer-events-none"></div>
    <div class="relative z-10">
        <div class="text-xs font-semibold text-emerald-100 uppercase tracking-wider mb-1">Welcome back,</div>
        <div class="text-xl sm:text-2xl font-black"><?= htmlspecialchars($customer['name']) ?> 👋</div>
        <div class="text-emerald-100/90 text-xs sm:text-sm mt-1 max-w-lg">Track your active shipments, view order receipts, and update your delivery preferences.</div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-6">
    <?php
    $cards = [
        ['label' => 'Total Orders', 'value' => $stats['total'], 'icon' => 'bag-handle', 'color' => 'blue'],
        ['label' => 'Active Orders', 'value' => $stats['active'], 'icon' => 'time', 'color' => 'yellow'],
        ['label' => 'Delivered', 'value' => $stats['delivered'], 'icon' => 'checkmark-circle', 'color' => 'emerald'],
        ['label' => 'Total Spent', 'value' => '৳' . number_format($stats['total_spent'], 0), 'icon' => 'wallet', 'color' => 'purple'],
    ];
    $bgMap = [
        'blue' => 'bg-blue-50 text-blue-600',
        'yellow' => 'bg-amber-50 text-amber-600',
        'emerald' => 'bg-emerald-50 text-emerald-600',
        'purple' => 'bg-purple-50 text-purple-600'
    ];
    foreach ($cards as $c):
    ?>
    <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-gray-200/80 shadow-2xs">
        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl <?= $bgMap[$c['color']] ?> flex items-center justify-center mb-2.5">
            <ion-icon name="<?= $c['icon'] ?>" class="text-lg sm:text-xl"></ion-icon>
        </div>
        <div class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight"><?= $c['value'] ?></div>
        <div class="text-[11px] sm:text-xs text-gray-500 font-medium mt-0.5"><?= $c['label'] ?></div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Recent Orders -->
<div class="bg-white rounded-2xl border border-gray-200/80 shadow-2xs overflow-hidden">
    <div class="p-4 sm:p-5 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-bold text-gray-900 text-sm sm:text-base flex items-center gap-2">
            <ion-icon name="receipt-outline" class="text-emerald-600 text-lg"></ion-icon> 
            <span>Recent Orders</span>
        </h2>
        <a href="<?= $base ?>/account/orders" class="text-xs sm:text-sm text-emerald-600 font-bold hover:underline flex items-center gap-1">
            <span>View All</span>
            <span>→</span>
        </a>
    </div>

    <?php if (empty($recentOrders)): ?>
    <div class="p-10 sm:p-14 text-center text-gray-400">
        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
            <ion-icon name="bag-outline" class="text-3xl"></ion-icon>
        </div>
        <p class="font-bold text-gray-700 text-sm">No orders placed yet.</p>
        <p class="text-xs text-gray-400 mt-1 max-w-xs mx-auto">Explore our fresh groceries and place your first order with lightning-fast delivery.</p>
        <a href="<?= $base ?>/" class="mt-4 inline-block bg-emerald-600 text-white text-xs font-bold px-5 py-2.5 rounded-xl hover:bg-emerald-700 transition-colors shadow-xs">
            Start Shopping
        </a>
    </div>
    <?php else: ?>
    <div class="divide-y divide-gray-100">
        <?php foreach ($recentOrders as $o): ?>
        <a href="<?= $base ?>/account/order-detail?id=<?= $o['id'] ?>" class="flex items-center justify-between p-3.5 sm:p-4 hover:bg-gray-50/80 transition-colors group">
            <div class="min-w-0 pr-3">
                <div class="font-bold text-gray-900 text-xs sm:text-sm group-hover:text-emerald-600 transition-colors flex items-center gap-1.5">
                    <span>Order #<?= $o['id'] ?></span>
                    <span class="text-[10px] text-gray-400 hidden sm:inline">•</span>
                    <span class="text-[11px] text-gray-500 font-normal hidden sm:inline"><?= date('M d, Y', strtotime($o['created_at'])) ?></span>
                </div>
                <div class="text-[11px] text-gray-400 sm:hidden mt-0.5">
                    <?= date('M d, Y', strtotime($o['created_at'])) ?>
                </div>
            </div>
            <div class="text-right flex items-center gap-3 flex-shrink-0">
                <div>
                    <div class="font-black text-gray-900 text-xs sm:text-sm">৳<?= number_format($o['total_amount'], 2) ?></div>
                    <span class="inline-block text-[10px] px-2 py-0.5 rounded-full font-bold border mt-0.5 <?= $statusColors[$o['status']] ?? 'bg-gray-100 text-gray-600 border-gray-200' ?>">
                        <?= ucfirst(str_replace('_', ' ', $o['status'])) ?>
                    </span>
                </div>
                <div class="text-gray-300 group-hover:text-emerald-600 group-hover:translate-x-0.5 transition-all text-base">
                    <ion-icon name="chevron-forward-outline"></ion-icon>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
