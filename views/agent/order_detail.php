<?php
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
$items = $order['items'] ?? [];
?>

<div class="space-y-4">

    <!-- Top Action Navigation -->
    <div class="flex items-center justify-between">
        <a href="<?= $base ?>/agent/orders" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-500 hover:text-emerald-700 transition-colors">
            <ion-icon name="arrow-back"></ion-icon>
            <span>অর্ডার তালিকায় ফিরুন</span>
        </a>
        <button onclick="window.print()" class="px-3 py-1.5 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-xl text-xs font-bold shadow-2xs flex items-center gap-1">
            <ion-icon name="print-outline"></ion-icon>
            <span>রিসিট প্রিন্ট</span>
        </button>
    </div>

    <!-- Main Order Voucher Card -->
    <div class="bg-white rounded-3xl p-5 border border-gray-200/90 shadow-sm space-y-4">
        <!-- Order Header -->
        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
            <div>
                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">অর্ডার ভাউচার</span>
                <h1 class="text-xl font-black text-gray-900 leading-tight">#<?= $order['id'] ?></h1>
                <div class="text-[11px] font-mono text-gray-500 mt-0.5"><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></div>
            </div>

            <div class="text-right">
                <?php
                $status = $order['status'];
                $badgeClass = 'bg-gray-100 text-gray-700';
                $statusText = 'পেন্ডিং';
                if ($status === 'pending') { $badgeClass = 'bg-amber-100 text-amber-800'; $statusText = 'পেন্ডিং'; }
                elseif ($status === 'processing') { $badgeClass = 'bg-blue-100 text-blue-800'; $statusText = 'প্রসেসিং'; }
                elseif ($status === 'out_for_delivery') { $badgeClass = 'bg-purple-100 text-purple-800'; $statusText = 'অন-ওয়ে'; }
                elseif ($status === 'delivered') { $badgeClass = 'bg-emerald-100 text-emerald-800'; $statusText = 'ডেলিভার্ড'; }
                elseif ($status === 'cancelled') { $badgeClass = 'bg-red-100 text-red-800'; $statusText = 'বাতিল'; }
                ?>
                <span class="px-3 py-1 rounded-full text-xs font-black <?= $badgeClass ?>">
                    <?= $statusText ?>
                </span>
                <div class="text-[11px] text-gray-400 mt-1 uppercase font-bold tracking-wider">
                    পেমেন্ট: <?= strtoupper($order['payment_method'] ?? 'cash') ?>
                </div>
            </div>
        </div>

        <!-- Customer & Delivery Info -->
        <div class="bg-gray-50/80 rounded-2xl p-3.5 space-y-2 text-xs">
            <h3 class="font-black text-gray-900 uppercase tracking-wider text-[11px] flex items-center gap-1.5 text-emerald-800">
                <ion-icon name="person-circle"></ion-icon>
                <span>গ্রাহক ও ডেলিভারি বিবরণ</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-gray-700">
                <div>
                    <span class="text-gray-400 block text-[10px]">গ্রাহকের নাম:</span>
                    <strong class="text-gray-900"><?= htmlspecialchars($order['customer_name'] ?? 'Customer') ?></strong>
                </div>
                <div>
                    <span class="text-gray-400 block text-[10px]">যোগাযোগ নম্বর:</span>
                    <strong class="font-mono text-gray-900"><?= htmlspecialchars($order['contact_number'] ?? $order['customer_phone'] ?? 'N/A') ?></strong>
                </div>
                <div class="sm:col-span-2">
                    <span class="text-gray-400 block text-[10px]">ডেলিভারি ঠিকানা:</span>
                    <span><?= htmlspecialchars($order['area_name'] ?? '') ?>, <?= htmlspecialchars($order['delivery_address'] ?? $order['customer_address_details'] ?? 'N/A') ?></span>
                </div>
            </div>

            <?php if (!empty($order['rider_note'])): ?>
                <div class="pt-2 border-t border-gray-200/60 text-[11px] text-emerald-900">
                    <strong>নোট:</strong> <?= htmlspecialchars($order['rider_note']) ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Ordered Items Table -->
        <div class="space-y-2">
            <h3 class="text-xs font-black text-gray-900 uppercase tracking-wider">পণ্যের তালিকা</h3>
            <div class="border border-gray-200 rounded-2xl overflow-hidden text-xs">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-gray-600 font-bold text-[11px] border-b border-gray-200">
                        <tr>
                            <th class="py-2.5 px-3">পণ্য</th>
                            <th class="py-2.5 px-3 text-center">পরিমাণ</th>
                            <th class="py-2.5 px-3 text-right">দর</th>
                            <th class="py-2.5 px-3 text-right">মোট</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php 
                        $itemsSubtotal = 0;
                        foreach ($items as $it): 
                            $lineTotal = (float)$it['price'] * (int)$it['quantity'];
                            $itemsSubtotal += $lineTotal;
                        ?>
                            <tr>
                                <td class="py-2.5 px-3">
                                    <div class="font-bold text-gray-900"><?= htmlspecialchars($it['product_name'] ?? 'Product') ?></div>
                                    <?php if (!empty($it['unit_title'])): ?>
                                        <span class="text-[10px] text-gray-400"><?= htmlspecialchars($it['unit_title']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-2.5 px-3 text-center font-bold text-gray-700"><?= $it['quantity'] ?></td>
                                <td class="py-2.5 px-3 text-right text-gray-600">৳<?= number_format($it['price'], 2) ?></td>
                                <td class="py-2.5 px-3 text-right font-black text-gray-900">৳<?= number_format($lineTotal, 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bill Calculation Summary -->
        <div class="pt-2 border-t border-gray-100 space-y-1.5 text-xs">
            <div class="flex items-center justify-between text-gray-600">
                <span>পণ্যের উপমোট:</span>
                <span class="font-bold text-gray-900">৳<?= number_format($itemsSubtotal, 2) ?></span>
            </div>
            <div class="flex items-center justify-between text-gray-600">
                <span>ডেলিভারি চার্জ:</span>
                <span class="font-bold text-emerald-700">৳<?= number_format($order['delivery_charge'] ?? 0, 2) ?></span>
            </div>
            <?php if (!empty($order['delivery_discount']) && $order['delivery_discount'] > 0): ?>
                <div class="flex items-center justify-between text-emerald-600 font-bold">
                    <span>ছাড় (ডিসকাউন্ট):</span>
                    <span>-৳<?= number_format($order['delivery_discount'], 2) ?></span>
                </div>
            <?php endif; ?>
            <div class="pt-2 border-t border-gray-100 flex items-center justify-between text-base">
                <span class="font-black text-gray-900">সর্বমোট প্রদেয় বিল:</span>
                <span class="font-black text-emerald-800 text-lg">৳<?= number_format($order['total_amount'], 2) ?></span>
            </div>
        </div>
    </div>

    <!-- Tracking History Timeline -->
    <?php if (!empty($trackingHistory)): ?>
        <div class="bg-white rounded-3xl p-5 border border-gray-200/90 shadow-xs space-y-3">
            <h3 class="text-xs font-black text-gray-900 uppercase tracking-wider flex items-center gap-1.5">
                <ion-icon name="git-branch-outline" class="text-emerald-600"></ion-icon>
                <span>অর্ডার ট্র্যাকিং ও আপডেট ইতিহাস</span>
            </h3>

            <div class="space-y-3 relative pl-4 border-l-2 border-emerald-200 ml-2">
                <?php foreach ($trackingHistory as $log): ?>
                    <div class="relative">
                        <span class="absolute -left-[21px] top-1 w-2.5 h-2.5 rounded-full bg-emerald-600 border-2 border-white"></span>
                        <div class="text-[11px] font-black text-gray-900"><?= htmlspecialchars($log['message']) ?></div>
                        <div class="text-[10px] text-gray-400 font-mono"><?= date('d M Y, h:i A', strtotime($log['created_at'])) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

</div>
