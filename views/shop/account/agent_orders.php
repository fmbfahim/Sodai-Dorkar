<?php
ob_start();
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
$title = $title ?? 'সংগৃহীত এজেন্ট অর্ডারসমূহ';
?>

<div class="space-y-6">

    <!-- Top Breadcrumb & Actions -->
    <div class="flex items-center justify-between border-b border-gray-200/80 pb-3">
        <div>
            <div class="flex items-center gap-1.5 text-xs text-gray-500 mb-0.5">
                <a href="<?= $base ?>/account" class="hover:text-emerald-600">একাউন্ট</a>
                <span>/</span>
                <a href="<?= $base ?>/account/agent/dashboard" class="hover:text-emerald-600">এজেন্ট হাব</a>
                <span>/</span>
                <span class="text-gray-900 font-bold">সংগৃহীত অর্ডারসমূহ</span>
            </div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight flex items-center gap-2">
                <span>আমার সংগৃহীত ফিল্ড অর্ডারসমূহ</span>
                <ion-icon name="receipt" class="text-emerald-600"></ion-icon>
            </h1>
        </div>

        <a href="<?= $base ?>/account/agent/shop" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-xs font-bold shadow-xs flex items-center gap-1.5 transition-all">
            <ion-icon name="add-circle-outline" class="text-base"></ion-icon>
            <span>নতুন অর্ডার নিন</span>
        </a>
    </div>

    <!-- Search Form -->
    <form action="<?= $base ?>/account/agent/orders" method="GET" class="relative">
        <?php if (!empty($activeStatus) && $activeStatus !== 'all'): ?>
            <input type="hidden" name="status" value="<?= htmlspecialchars($activeStatus) ?>">
        <?php endif; ?>
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="অর্ডার আইডি (#), কাস্টমারের নাম বা মোবাইল নম্বর দিয়ে খুঁজুন..." class="w-full pl-10 pr-10 py-3 bg-white rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-2xs text-xs font-medium">
        <ion-icon name="search-outline" class="absolute left-3.5 top-3.5 text-lg text-gray-400"></ion-icon>
        <?php if (!empty($search)): ?>
            <a href="<?= $base ?>/account/agent/orders<?= !empty($activeStatus) && $activeStatus !== 'all' ? '?status=' . $activeStatus : '' ?>" class="absolute right-3.5 top-3 text-gray-400 hover:text-gray-600 text-lg">
                <ion-icon name="close-circle"></ion-icon>
            </a>
        <?php endif; ?>
    </form>

    <!-- Status Tabs -->
    <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-0.5">
        <?php
        $tabs = [
            'all' => 'সবগুলো (' . ($stats['total_orders'] ?? 0) . ')',
            'pending' => 'পেন্ডিং (' . ($stats['pending_orders'] ?? 0) . ')',
            'out_for_delivery' => 'অন-ওয়ে (' . ($stats['out_for_delivery_orders'] ?? 0) . ')',
            'delivered' => 'ডেলিভার্ড (' . ($stats['delivered_orders'] ?? 0) . ')',
            'cancelled' => 'বাতিল (' . ($stats['cancelled_orders'] ?? 0) . ')'
        ];
        ?>
        <?php foreach ($tabs as $k => $label): ?>
            <?php
            $isCur = ($activeStatus === $k) || (empty($activeStatus) && $k === 'all');
            $url = $base . '/account/agent/orders?status=' . $k . (!empty($search) ? '&search=' . urlencode($search) : '');
            ?>
            <a href="<?= $url ?>" class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all <?= $isCur ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white text-gray-600 border border-gray-200/80 hover:border-emerald-300' ?>">
                <?= $label ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Orders Cards List -->
    <div class="space-y-3.5">
        <?php if (empty($orders)): ?>
            <div class="bg-white rounded-3xl p-12 text-center text-gray-400 border border-gray-200/80 space-y-2">
                <ion-icon name="receipt-outline" class="text-4xl text-gray-300"></ion-icon>
                <p class="text-xs font-bold text-gray-600">কোনো অর্ডার পাওয়া যায়নি</p>
                <p class="text-[11px] text-gray-400">এই ফিল্টারে আপনার সংগৃহীত কোনো অর্ডার পাওয়া যায়নি।</p>
                <a href="<?= $base ?>/account/agent/shop" class="inline-flex items-center gap-1.5 mt-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs">
                    <ion-icon name="cart-outline"></ion-icon>
                    <span>নতুন অর্ডার গ্রহণ করুন</span>
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <?php
                $status = $order['status'];
                $badgeClass = 'bg-gray-100 text-gray-700';
                $statusText = 'পেন্ডিং';
                if ($status === 'pending') { $badgeClass = 'bg-amber-100 text-amber-800 border border-amber-200'; $statusText = 'পেন্ডিং'; }
                elseif ($status === 'confirmed' || $status === 'processing') { $badgeClass = 'bg-blue-100 text-blue-800 border border-blue-200'; $statusText = 'প্রসেসিং'; }
                elseif ($status === 'out_for_delivery') { $badgeClass = 'bg-purple-100 text-purple-800 border border-purple-200'; $statusText = 'অন-ওয়ে (ডেলিভারিম্যান)'; }
                elseif ($status === 'delivered') { $badgeClass = 'bg-emerald-100 text-emerald-800 border border-emerald-200'; $statusText = 'ডেলিভার্ড সম্পন্ন'; }
                elseif ($status === 'cancelled') { $badgeClass = 'bg-rose-100 text-rose-800 border border-rose-200'; $statusText = 'বাতিল'; }
                ?>
                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-gray-200/80 shadow-2xs hover:border-emerald-300 transition-all space-y-3">
                    <!-- Order Card Top Bar -->
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-9 h-9 rounded-xl bg-gray-100 text-gray-800 font-mono font-black text-xs flex items-center justify-center">
                                #<?= $order['id'] ?>
                            </span>
                            <div>
                                <div class="text-xs font-mono font-bold text-gray-600">
                                    <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?>
                                </div>
                                <div class="text-[10px] text-gray-400">পেমেন্ট: <?= strtoupper($order['payment_method'] ?? 'CASH') ?></div>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black <?= $badgeClass ?>">
                            <?= $statusText ?>
                        </span>
                    </div>

                    <!-- Customer Info -->
                    <div class="space-y-1 text-xs">
                        <div class="flex items-center justify-between">
                            <div class="font-black text-gray-900 flex items-center gap-1.5">
                                <ion-icon name="person" class="text-emerald-600 text-sm"></ion-icon>
                                <span><?= htmlspecialchars($order['customer_name'] ?? 'গ্রাহক') ?></span>
                            </div>
                            <span class="font-mono text-gray-600 text-xs font-semibold"><?= htmlspecialchars($order['customer_phone'] ?? '') ?></span>
                        </div>
                        <div class="text-[11px] text-gray-500 flex items-center gap-1">
                            <ion-icon name="location-outline" class="text-gray-400"></ion-icon>
                            <span><?= htmlspecialchars($order['area_name'] ?? 'এরিয়া') ?> <?= !empty($order['customer_address']) ? ' • ' . htmlspecialchars($order['customer_address']) : '' ?></span>
                        </div>
                    </div>

                    <!-- Items Preview -->
                    <?php if (!empty($order['items'])): ?>
                        <div class="bg-gray-50/70 rounded-xl p-3 text-[11px] text-gray-600 space-y-1">
                            <?php foreach (array_slice($order['items'], 0, 3) as $it): ?>
                                <div class="flex items-center justify-between">
                                    <span class="truncate max-w-[280px]"><?= htmlspecialchars($it['product_name'] ?? 'পণ্য') ?> × <?= $it['quantity'] ?></span>
                                    <span class="font-bold text-gray-700">৳<?= number_format($it['price'] * $it['quantity'], 0) ?></span>
                                </div>
                            <?php endforeach; ?>
                            <?php if (count($order['items']) > 3): ?>
                                <div class="text-[10px] text-gray-400 font-bold pt-0.5">
                                    + আরও <?= count($order['items']) - 3 ?> টি আইটেম...
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Bottom Bar: Price & View Invoice Action -->
                    <div class="pt-2 border-t border-gray-100 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">সর্বমোট বিল</span>
                            <div class="text-base font-black text-emerald-800">৳<?= number_format((float)$order['total_amount'], 2) ?></div>
                        </div>

                        <a href="<?= $base ?>/account/agent/order-detail?id=<?= $order['id'] ?>" class="px-4 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-xl text-xs font-bold transition-colors flex items-center gap-1">
                            <span>বিস্তারিত ইনভয়েস</span>
                            <ion-icon name="chevron-forward"></ion-icon>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

<?php 
$content = ob_get_clean(); 
require __DIR__ . '/layout.php'; 
?>
