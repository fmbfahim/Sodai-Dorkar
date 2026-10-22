<?php
ob_start();
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
$title = $title ?? ('অর্ডার বিবরণ #' . ($order['id'] ?? ''));
$status = $order['status'] ?? 'pending';
$badgeClass = 'bg-gray-100 text-gray-700';
$statusText = 'পেন্ডিং';
if ($status === 'pending') { $badgeClass = 'bg-amber-100 text-amber-800 border border-amber-200'; $statusText = 'পেন্ডিং'; }
elseif ($status === 'confirmed' || $status === 'processing') { $badgeClass = 'bg-blue-100 text-blue-800 border border-blue-200'; $statusText = 'প্রসেসিং'; }
elseif ($status === 'out_for_delivery') { $badgeClass = 'bg-purple-100 text-purple-800 border border-purple-200'; $statusText = 'অন-ওয়ে (ডেলিভারিম্যান)'; }
elseif ($status === 'delivered') { $badgeClass = 'bg-emerald-100 text-emerald-800 border border-emerald-200'; $statusText = 'ডেলিভার্ড সম্পন্ন'; }
elseif ($status === 'cancelled') { $badgeClass = 'bg-rose-100 text-rose-800 border border-rose-200'; $statusText = 'বাতিল'; }
?>

<div class="space-y-6">

    <!-- Top Breadcrumb & Navigation -->
    <div class="flex items-center justify-between border-b border-gray-200/80 pb-3">
        <div>
            <div class="flex items-center gap-1.5 text-xs text-gray-500 mb-0.5">
                <a href="<?= $base ?>/account" class="hover:text-emerald-600">একাউন্ট</a>
                <span>/</span>
                <a href="<?= $base ?>/account/agent/orders" class="hover:text-emerald-600">এজেন্ট অর্ডার</a>
                <span>/</span>
                <span class="text-gray-900 font-bold">ইনভয়েস #<?= $order['id'] ?></span>
            </div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight flex items-center gap-2">
                <span>অর্ডার বিবরণ ও ইনভয়েস #<?= $order['id'] ?></span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold <?= $badgeClass ?>"><?= $statusText ?></span>
            </h1>
        </div>

        <a href="<?= $base ?>/account/agent/orders" class="text-xs font-bold text-gray-600 hover:text-emerald-600 flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-gray-200 hover:bg-gray-50 transition-colors">
            <ion-icon name="arrow-back"></ion-icon>
            <span>অর্ডার তালিকায় ফিরুন</span>
        </a>
    </div>

    <!-- Order Meta & Customer Info Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Customer Info Card -->
        <div class="bg-white rounded-3xl p-5 border border-gray-200/80 shadow-2xs space-y-2.5">
            <h3 class="text-xs font-black text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                <ion-icon name="person" class="text-emerald-600"></ion-icon>
                <span>গ্রাহক ও ডেলিভারি তথ্য</span>
            </h3>
            <div>
                <div class="text-sm font-black text-gray-900"><?= htmlspecialchars($order['customer_name'] ?? 'গ্রাহক') ?></div>
                <div class="text-xs font-mono text-gray-600 mt-0.5"><?= htmlspecialchars($order['contact_number'] ?: ($order['customer_phone'] ?? '')) ?></div>
                <div class="text-xs text-gray-600 mt-1 flex items-start gap-1">
                    <ion-icon name="location-outline" class="text-emerald-600 mt-0.5 shrink-0"></ion-icon>
                    <span><?= htmlspecialchars($order['area_name'] ?? '') ?><?= !empty($order['delivery_address']) ? ' • ' . htmlspecialchars($order['delivery_address']) : '' ?></span>
                </div>
            </div>
        </div>

        <!-- Order Meta Card -->
        <div class="bg-white rounded-3xl p-5 border border-gray-200/80 shadow-2xs space-y-2.5">
            <h3 class="text-xs font-black text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                <ion-icon name="information-circle" class="text-emerald-600"></ion-icon>
                <span>অর্ডার সারাংশ</span>
            </h3>
            <div class="space-y-1 text-xs">
                <div class="flex justify-between text-gray-600">
                    <span>অর্ডারের সময়:</span>
                    <span class="font-mono font-bold text-gray-900"><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>পেমেন্ট মাধ্যম:</span>
                    <span class="font-bold text-gray-900 uppercase"><?= htmlspecialchars($order['payment_method'] ?? 'CASH') ?></span>
                </div>
                <?php if (!empty($order['payment_trx_id'])): ?>
                    <div class="flex justify-between text-gray-600">
                        <span>TrxID / রেফারেন্স:</span>
                        <span class="font-mono font-bold text-emerald-700"><?= htmlspecialchars($order['payment_trx_id']) ?></span>
                    </div>
                <?php endif; ?>
                <?php if (!empty($order['rider_note'])): ?>
                    <div class="pt-1 text-[11px] text-gray-500 italic">
                        "<?= htmlspecialchars($order['rider_note']) ?>"
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Order Items Table Card -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-gray-200/90 shadow-xs space-y-4">
        <h3 class="text-sm font-black text-gray-900 flex items-center gap-2 border-b border-gray-100 pb-3">
            <ion-icon name="bag-check" class="text-emerald-600 text-base"></ion-icon>
            <span>পণ্য তালিকা</span>
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-gray-400 font-bold border-b border-gray-100 text-[11px]">
                        <th class="pb-3 pl-2">পণ্য</th>
                        <th class="pb-3 text-center">পরিমাণ</th>
                        <th class="pb-3 text-right">একক মূল্য</th>
                        <th class="pb-3 text-right pr-2">মোট মূল্য</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php 
                    $subtotal = 0;
                    if (!empty($order['items'])): 
                        foreach ($order['items'] as $item):
                            $lineTotal = (float)$item['price'] * (int)$item['quantity'];
                            $subtotal += $lineTotal;
                    ?>
                        <tr>
                            <td class="py-3 pl-2">
                                <div class="font-bold text-gray-900"><?= htmlspecialchars($item['product_name'] ?? 'পণ্য') ?></div>
                                <?php if (!empty($item['unit_title'])): ?>
                                    <div class="text-[10px] text-gray-400"><?= htmlspecialchars($item['unit_title']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 text-center font-bold text-gray-900"><?= $item['quantity'] ?></td>
                            <td class="py-3 text-right font-mono text-gray-700">৳<?= number_format((float)$item['price'], 2) ?></td>
                            <td class="py-3 pr-2 text-right font-mono font-bold text-gray-900">৳<?= number_format($lineTotal, 2) ?></td>
                        </tr>
                    <?php 
                        endforeach; 
                    endif; 
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Bill Totals Breakdown -->
        <div class="pt-4 border-t border-gray-100 flex justify-end">
            <div class="w-full sm:w-64 space-y-2 text-xs">
                <div class="flex justify-between text-gray-600">
                    <span>পণ্যের উপমোট:</span>
                    <span class="font-bold text-gray-900">৳<?= number_format($subtotal, 2) ?></span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>ডেলিভারি চার্জ:</span>
                    <span class="font-bold text-emerald-700">৳<?= number_format((float)($order['delivery_charge'] ?? 0), 2) ?></span>
                </div>
                <div class="pt-2 border-t border-gray-100 flex justify-between text-sm font-black text-gray-900">
                    <span>সর্বমোট বিল:</span>
                    <span class="text-emerald-700 font-mono text-base">৳<?= number_format((float)($order['total_amount'] ?? 0), 2) ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Tracking Timeline Card -->
    <?php if (!empty($trackingHistory)): ?>
        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-gray-200/90 shadow-xs space-y-4">
            <h3 class="text-sm font-black text-gray-900 flex items-center gap-2 border-b border-gray-100 pb-3">
                <ion-icon name="map-outline" class="text-emerald-600 text-base"></ion-icon>
                <span>ডেলিভারি ট্র্যাকিং হিস্ট্রি</span>
            </h3>

            <div class="space-y-4 pl-3 border-l-2 border-emerald-500/30 ml-2">
                <?php foreach ($trackingHistory as $th): ?>
                    <div class="relative pl-3">
                        <span class="absolute -left-[19px] top-1 w-3 h-3 rounded-full bg-emerald-600 border-2 border-white shadow-xs"></span>
                        <div class="text-xs font-bold text-gray-900 uppercase tracking-tight"><?= htmlspecialchars($th['status']) ?></div>
                        <div class="text-xs text-gray-600 mt-0.5"><?= htmlspecialchars($th['note'] ?? '') ?></div>
                        <div class="text-[10px] text-gray-400 font-mono mt-0.5"><?= date('d M Y, h:i A', strtotime($th['created_at'])) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

</div>

<?php 
$content = ob_get_clean(); 
require __DIR__ . '/layout.php'; 
?>
