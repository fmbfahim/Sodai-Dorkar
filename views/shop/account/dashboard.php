<?php
ob_start();

$statusColors = [
    'processing' => 'bg-yellow-100 text-yellow-800',
    'confirmed'  => 'bg-blue-100 text-blue-800',
    'packaging'  => 'bg-indigo-100 text-indigo-800',
    'picking'    => 'bg-purple-100 text-purple-800',
    'dispatch'   => 'bg-orange-100 text-orange-800',
    'shipped'    => 'bg-teal-100 text-teal-800',
    'out_for_delivery' => 'bg-emerald-100 text-emerald-800',
    'delivered'  => 'bg-green-100 text-green-800',
    'cancelled'  => 'bg-red-100 text-red-800',
    'returned'   => 'bg-gray-100 text-gray-800',
];
?>

<!-- Welcome Banner -->
<div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl p-6 text-white mb-6">
    <div class="text-sm font-medium text-emerald-100 mb-1">স্বাগতম,</div>
    <div class="text-2xl font-black"><?= htmlspecialchars($customer['name']) ?> 👋</div>
    <div class="text-emerald-100 text-sm mt-1">আপনার একাউন্ট ড্যাশবোর্ডে আপনাকে স্বাগতম।</div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <?php
    $cards = [
        ['label' => 'মোট অর্ডার', 'value' => $stats['total'], 'icon' => 'bag-handle', 'color' => 'blue'],
        ['label' => 'সক্রিয় অর্ডার', 'value' => $stats['active'], 'icon' => 'time', 'color' => 'yellow'],
        ['label' => 'ডেলিভারড', 'value' => $stats['delivered'], 'icon' => 'checkmark-circle', 'color' => 'emerald'],
        ['label' => 'মোট ব্যয়', 'value' => '৳' . number_format($stats['total_spent'], 0), 'icon' => 'cash', 'color' => 'purple'],
    ];
    $bgMap = ['blue'=>'bg-blue-50 text-blue-600', 'yellow'=>'bg-yellow-50 text-yellow-600', 'emerald'=>'bg-emerald-50 text-emerald-600', 'purple'=>'bg-purple-50 text-purple-600'];
    foreach ($cards as $c):
    ?>
    <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
        <div class="w-10 h-10 rounded-xl <?= $bgMap[$c['color']] ?> flex items-center justify-center mb-3">
            <ion-icon name="<?= $c['icon'] ?>" class="text-xl"></ion-icon>
        </div>
        <div class="text-2xl font-black text-gray-900"><?= $c['value'] ?></div>
        <div class="text-xs text-gray-500 mt-0.5"><?= $c['label'] ?></div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Recent Orders -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-gray-50 flex items-center justify-between">
        <h2 class="font-bold text-gray-900 flex items-center gap-2">
            <ion-icon name="time-outline" class="text-emerald-600"></ion-icon> সাম্প্রতিক অর্ডার
        </h2>
        <a href="/sodai-dorkar/public/account/orders" class="text-sm text-emerald-600 font-semibold hover:underline">সব দেখুন →</a>
    </div>
    <?php if (empty($recentOrders)): ?>
    <div class="p-10 text-center text-gray-400">
        <ion-icon name="bag-outline" class="text-5xl mb-3 block"></ion-icon>
        <p class="font-medium">এখনো কোনো অর্ডার করা হয়নি।</p>
        <a href="/sodai-dorkar/public/" class="mt-3 inline-block bg-emerald-600 text-white text-sm font-bold px-5 py-2.5 rounded-xl hover:bg-emerald-700 transition-colors">Shop Now</a>
    </div>
    <?php else: ?>
    <div class="divide-y divide-gray-50">
        <?php foreach ($recentOrders as $o): ?>
        <a href="/sodai-dorkar/public/account/order-detail?id=<?= $o['id'] ?>" class="flex items-center justify-between px-5 py-4 hover:bg-gray-50 transition-colors">
            <div>
                <div class="font-bold text-gray-800 text-sm">#<?= $o['id'] ?></div>
                <div class="text-xs text-gray-500 mt-0.5"><?= date('d M Y', strtotime($o['created_at'])) ?></div>
            </div>
            <div class="text-right">
                <div class="font-black text-gray-900 text-sm">৳<?= number_format($o['total_amount'], 2) ?></div>
                <span class="text-[11px] px-2 py-0.5 rounded-full font-semibold <?= $statusColors[$o['status']] ?? 'bg-gray-100 text-gray-600' ?>">
                    <?= ucfirst(str_replace('_', ' ', $o['status'])) ?>
                </span>
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

