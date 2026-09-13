<?php
    $allOrdersForMap = [];
    foreach ($orders as $ord) {
        $allOrdersForMap[$ord['id']] = $ord['items'] ?? [];
    }
    foreach ($history as $h) {
        if (!isset($allOrdersForMap[$h['id']])) {
            $allOrdersForMap[$h['id']] = $h['items'] ?? [];
        }
    }
?>
<script> 
    window.orderItemsMap = <?php echo json_encode($allOrdersForMap, JSON_INVALID_UTF8_SUBSTITUTE) ?: '{}'; ?>;
</script>

<div class="max-w-md mx-auto" x-data="{
    tab: 'active',
    activeFilter: 'all',
    historyFilter: 'all',
    expandedOrder: null,
    deliveryModal: false,
    cancelModal: false,
    transferModal: false,
    noteModal: false,
    locationModal: false,
    modifyModal: false,
    activeOrderId: null,
    activeCustomerId: null,
    activeOrderAmount: 0,
    activeCustomerName: '',
    activeCustomerPhone: '',
    activeCustomerAddress: '',
    activeLocationLat: '23.2321',
    activeLocationLng: '90.6631',
    activeHasGps: false,
    activeOrderCharge: 0,
    activeOrderDiscount: 0,
    paymentMethod: 'cash',
    trxId: '',
    cancelReason: 'Customer phone switched off / unreachable',
    deliveryDiscount: 0,
    amountChangeReason: '',
    riderNote: '',
    activeRiderNote: '',
    activeOrderItems: [],
    
    openModify(orderId, customerName, totalAmount, deliveryCharge, deliveryDiscount) {
        this.activeOrderId = orderId;
        this.activeCustomerName = customerName;
        this.activeOrderAmount = parseFloat(totalAmount) || 0;
        this.activeOrderCharge = parseFloat(deliveryCharge) || 0;
        this.activeOrderDiscount = parseFloat(deliveryDiscount) || 0;
        const rawItems = (window.orderItemsMap && window.orderItemsMap[orderId]) ? window.orderItemsMap[orderId] : [];
        this.activeOrderItems = rawItems.map(item => {
            const qty = parseInt(item.quantity) || 1;
            const ret = parseInt(item.return_qty) || 0;
            const dmg = parseInt(item.damage_qty) || 0;
            const deliv = Math.max(0, qty - ret - dmg);
            return {
                id: item.id,
                product_name: item.product_name || 'Product',
                unit_title: item.unit_title || '',
                price: parseFloat(item.price) || 0,
                quantity: qty,
                return_qty: ret,
                damage_qty: dmg,
                deliver_qty: deliv
            };
        });
        this.modifyModal = true;
    },

    updateFromDeliver(item) {
        let del = parseInt(item.deliver_qty);
        if (isNaN(del) || del < 0) del = 0;
        if (del > item.quantity) del = item.quantity;
        item.deliver_qty = del;
        
        let remaining = item.quantity - del;
        let dmg = Math.min(parseInt(item.damage_qty) || 0, remaining);
        item.damage_qty = dmg;
        item.return_qty = remaining - dmg;
    },

    updateFromReturn(item) {
        let ret = parseInt(item.return_qty);
        if (isNaN(ret) || ret < 0) ret = 0;
        if (ret > item.quantity) ret = item.quantity;
        item.return_qty = ret;
        
        let remaining = item.quantity - ret;
        let dmg = Math.min(parseInt(item.damage_qty) || 0, remaining);
        item.damage_qty = dmg;
        item.deliver_qty = remaining - dmg;
    },

    updateFromDamage(item) {
        let dmg = parseInt(item.damage_qty);
        if (isNaN(dmg) || dmg < 0) dmg = 0;
        if (dmg > item.quantity) dmg = item.quantity;
        item.damage_qty = dmg;
        
        let remaining = item.quantity - dmg;
        let ret = Math.min(parseInt(item.return_qty) || 0, remaining);
        item.return_qty = ret;
        item.deliver_qty = remaining - ret;
    },

    changeDeliverQty(item, delta) {
        item.deliver_qty = Math.max(0, Math.min(item.quantity, (parseInt(item.deliver_qty) || 0) + delta));
        this.updateFromDeliver(item);
    },

    changeReturnQty(item, delta) {
        item.return_qty = Math.max(0, Math.min(item.quantity, (parseInt(item.return_qty) || 0) + delta));
        this.updateFromReturn(item);
    },

    getModifiedItemsSubtotal() {
        return this.activeOrderItems.reduce((sum, item) => {
            return sum + ((item.deliver_qty || 0) * (item.price || 0));
        }, 0);
    },

    getModifiedGrandTotal() {
        const subtotal = this.getModifiedItemsSubtotal();
        return Math.max(0, subtotal + (this.activeOrderCharge || 0) - (this.activeOrderDiscount || 0));
    },
    
    openLocation(orderId, customerId, lat, lng, customerName, customerPhone, address, hasGps) {
        this.activeOrderId = orderId;
        this.activeCustomerId = customerId;
        this.activeCustomerName = customerName || '';
        this.activeCustomerPhone = customerPhone || '';
        this.activeCustomerAddress = address || '';
        this.activeLocationLat = lat || '23.2321';
        this.activeLocationLng = lng || '90.6631';
        this.activeHasGps = !!hasGps;
        this.locationModal = true;
        if (typeof initLocationMap === 'function') {
            initLocationMap(lat, lng);
        }
    },
    
    openNote(orderId, customerName, riderNote) {
        this.activeOrderId = orderId;
        this.activeCustomerName = customerName;
        this.activeRiderNote = riderNote;
        this.noteModal = true;
    },
    
    openCancel(orderId, customerName) {
        this.activeOrderId = orderId;
        this.activeCustomerName = customerName;
        this.cancelModal = true;
    },
    
    openTransfer(orderId, customerName) {
        this.activeOrderId = orderId;
        this.activeCustomerName = customerName;
        this.transferModal = true;
    },
    
    openDeliver(orderId, customerName, amount, riderNote) {
        this.activeOrderId = orderId;
        this.activeCustomerName = customerName;
        this.activeOrderAmount = parseFloat(amount) || 0;
        this.riderNote = riderNote;
        this.deliveryDiscount = 0;
        this.amountChangeReason = '';
        this.deliveryModal = true;
    },
    
    searchQuery: '',
    searchResults: [],
    searchLoading: false,
    searchDone: false,
    doSearch() {
        if (this.searchQuery.trim().length < 2) return;
        this.searchLoading = true;
        this.searchDone = false;
        fetch('/sodai-dorkar/public/delivery/parcel-search?q=' + encodeURIComponent(this.searchQuery))
            .then(r => r.json())
            .then(data => { this.searchResults = data; this.searchLoading = false; this.searchDone = true; })
            .catch(() => { this.searchLoading = false; });
    }
}">
    <!-- Stats Header -->
    <div class="bg-gradient-to-br from-primary-700 via-primary-600 to-emerald-600 text-white p-5 rounded-b-3xl shadow-lg mb-4 -mt-6 -mx-6">
        <!-- Rider Info Row -->
        <div class="flex justify-between items-center mb-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center font-black text-lg">
                        <?php echo strtoupper(mb_substr($riderInfo['name'] ?? 'R', 0, 1)); ?>
                    </div>
                    <div>
                        <div class="font-black text-base leading-tight"><?php echo htmlspecialchars($riderInfo['name'] ?? ''); ?></div>
                        <div class="text-[10px] text-primary-200 font-medium"><?php echo htmlspecialchars($riderInfo['phone'] ?? ''); ?></div>
                    </div>
                </div>
                <?php if (!empty($riderInfo['assigned_areas'])): ?>
                <div class="flex items-center gap-1 mt-1">
                    <ion-icon name="location" class="text-xs text-primary-200"></ion-icon>
                    <span class="text-[10px] text-primary-200"><?php echo htmlspecialchars($riderInfo['assigned_areas']); ?></span>
                </div>
                <?php endif; ?>
            </div>
            <div class="text-right">
                <div class="text-[9px] uppercase tracking-wider text-primary-200">Today</div>
                <div class="text-xs font-bold opacity-90"><?php echo date('d M Y'); ?></div>
                <div class="text-[10px] text-primary-200 mt-1">Member since <?php echo date('M Y', strtotime($riderInfo['created_at'] ?? 'now')); ?></div>
            </div>
        </div>

        <!-- Pending Deposit Alert (show only if there's pending cash) -->
        <?php if (($fullStats['pending_deposit'] ?? 0) > 0): ?>
        <div class="bg-red-500/30 border border-red-400/40 rounded-xl p-3 mb-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <ion-icon name="warning" class="text-yellow-300 text-xl shrink-0"></ion-icon>
                <div>
                    <div class="text-[11px] font-bold text-yellow-200 uppercase tracking-wide">Deposit Pending!</div>
                    <div class="text-[10px] text-white/80">Please deposit to office</div>
                </div>
            </div>
            <div class="text-right">
                <div class="text-xl font-black text-yellow-200">৳<?php echo number_format($fullStats['pending_deposit'], 0); ?></div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Today's Cash Summary -->
        <div class="grid grid-cols-2 gap-2 mb-3">
            <div class="bg-white/10 rounded-xl p-3">
                <div class="text-[10px] text-primary-200 uppercase tracking-wide font-bold mb-1">Today's Cash</div>
                <div class="text-xl font-black">৳<?php echo number_format($fullStats['today_cash'] ?? 0, 0); ?></div>
                <div class="text-[10px] text-primary-200 mt-0.5">Total Deposit: ৳<?php echo number_format($fullStats['total_deposited'] ?? 0, 0); ?></div>
            </div>
            <div class="bg-white/10 rounded-xl p-3">
                <div class="text-[10px] text-primary-200 uppercase tracking-wide font-bold mb-1">Today's Digital</div>
                <div class="text-xl font-black text-emerald-200">৳<?php echo number_format($fullStats['today_digital'] ?? 0, 0); ?></div>
                <div class="text-[10px] text-primary-200 mt-0.5">bKash / Nagad / Card</div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-4 gap-1.5 text-center">
            <button type="button" @click="tab = 'active'; activeFilter = (activeFilter === 'pending' ? 'all' : 'pending');" :class="activeFilter === 'pending' ? 'ring-2 ring-white/50' : ''" class="bg-black/20 p-2 rounded-xl transition-all">
                <div class="text-base font-bold leading-none text-white"><?php echo $stats['pending_count']; ?></div>
                <div class="text-[9px] uppercase tracking-wider opacity-80 mt-0.5 text-white">Pending</div>
            </button>
            <button type="button" @click="tab = 'active'; activeFilter = (activeFilter === 'out_for_delivery' ? 'all' : 'out_for_delivery');" :class="activeFilter === 'out_for_delivery' ? 'ring-2 ring-white/50' : ''" class="bg-black/20 p-2 rounded-xl transition-all">
                <div class="text-base font-bold leading-none text-white"><?php echo $stats['out_for_delivery']; ?></div>
                <div class="text-[9px] uppercase tracking-wider opacity-80 mt-0.5 text-white">On Way</div>
            </button>
            <button type="button" @click="tab = 'history'; historyFilter = (historyFilter === 'delivered' ? 'all' : 'delivered');" :class="historyFilter === 'delivered' ? 'ring-2 ring-emerald-300' : ''" class="bg-emerald-500/30 p-2 rounded-xl transition-all">
                <div class="text-base font-bold leading-none text-emerald-200"><?php echo $fullStats['today_delivered']; ?></div>
                <div class="text-[9px] uppercase tracking-wider opacity-80 mt-0.5 text-white">Delivered</div>
            </button>
            <button type="button" @click="tab = 'history'; historyFilter = (historyFilter === 'cancelled' ? 'all' : 'cancelled');" :class="historyFilter === 'cancelled' ? 'ring-2 ring-red-300' : ''" class="bg-red-500/20 p-2 rounded-xl transition-all">
                <div class="text-base font-bold leading-none text-red-200"><?php echo $fullStats['today_cancelled']; ?></div>
                <div class="text-[9px] uppercase tracking-wider opacity-80 mt-0.5 text-white">Cancelled</div>
            </button>
        </div>
    </div>

    <!-- Notification Messages -->
    <?php if (!empty($successMsg)): ?>
        <div class="mb-4 mx-1 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-xs">
            <ion-icon name="checkmark-circle" class="text-xl text-emerald-600 shrink-0"></ion-icon>
            <span><?php echo htmlspecialchars($successMsg); ?></span>
        </div>
    <?php endif; ?>
    <?php if (!empty($errorMsg)): ?>
        <div class="mb-4 mx-1 p-3 bg-red-50 border border-red-200 text-red-800 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-xs">
            <ion-icon name="alert-circle" class="text-xl text-red-600 shrink-0"></ion-icon>
            <span><?php echo htmlspecialchars($errorMsg); ?></span>
        </div>
    <?php endif; ?>

    <!-- Interface Tabs -->
    <div>
        <!-- Tab Bar -->
        <div class="grid grid-cols-4 gap-1 p-1 bg-secondary-100 rounded-2xl mb-4 mx-1 shadow-inner">
            <button @click="tab = 'active'" :class="tab === 'active' ? 'bg-white shadow-sm text-primary-600' : 'text-secondary-500'" class="py-2 text-[10px] font-bold rounded-xl transition-all flex flex-col items-center justify-center gap-0.5">
                <ion-icon name="list" class="text-base"></ion-icon>
                <span>Active</span>
                <span class="text-[9px] font-black">(<?php echo count($orders); ?>)</span>
            </button>
            <button @click="tab = 'history'" :class="tab === 'history' ? 'bg-white shadow-sm text-primary-600' : 'text-secondary-500'" class="py-2 text-[10px] font-bold rounded-xl transition-all flex flex-col items-center justify-center gap-0.5">
                <ion-icon name="time" class="text-base"></ion-icon>
                <span>History</span>
                <span class="text-[9px] font-black">(<?php echo count($history); ?>)</span>
            </button>
            <button @click="tab = 'search'" :class="tab === 'search' ? 'bg-white shadow-sm text-primary-600' : 'text-secondary-500'" class="py-2 text-[10px] font-bold rounded-xl transition-all flex flex-col items-center justify-center gap-0.5">
                <ion-icon name="search" class="text-base"></ion-icon>
                <span>Search</span>
                <span class="text-[9px] font-black">Parcel</span>
            </button>
            <button @click="tab = 'stats'" :class="tab === 'stats' ? 'bg-white shadow-sm text-primary-600' : 'text-secondary-500'" class="py-2 text-[10px] font-bold rounded-xl transition-all flex flex-col items-center justify-center gap-0.5">
                <ion-icon name="stats-chart" class="text-base"></ion-icon>
                <span>My Stats</span>
                <span class="text-[9px] font-black">Report</span>
            </button>
        </div>

        <!-- Active Orders List -->
        <div x-show="tab === 'active'" class="space-y-4 px-1 pb-24">
            <!-- Active Status Filter Pills -->
            <div class="flex items-center gap-1.5 mb-2 px-1 overflow-x-auto pb-1">
                <button type="button" @click="activeFilter = 'all'" :class="activeFilter === 'all' ? 'bg-primary-600 text-white font-bold shadow-xs' : 'bg-white text-secondary-600 border border-secondary-200 hover:bg-secondary-50'" class="px-3 py-1.5 rounded-xl text-xs whitespace-nowrap transition-all">
                    All Active (<?php echo count($orders); ?>)
                </button>
                <button type="button" @click="activeFilter = 'pending'" :class="activeFilter === 'pending' ? 'bg-blue-600 text-white font-bold shadow-xs' : 'bg-white text-secondary-600 border border-secondary-200 hover:bg-secondary-50'" class="px-3 py-1.5 rounded-xl text-xs whitespace-nowrap transition-all">
                    Ready To Pick (<?php echo $stats['pending_count'] ?? 0; ?>)
                </button>
                <button type="button" @click="activeFilter = 'out_for_delivery'" :class="activeFilter === 'out_for_delivery' ? 'bg-amber-600 text-white font-bold shadow-xs' : 'bg-white text-secondary-600 border border-secondary-200 hover:bg-secondary-50'" class="px-3 py-1.5 rounded-xl text-xs whitespace-nowrap transition-all">
                    On The Way (<?php echo $stats['out_for_delivery'] ?? 0; ?>)
                </button>
            </div>

            <?php if (empty($orders)): ?>
                <div class="text-center py-16 text-secondary-400 bg-white rounded-2xl border border-secondary-100 shadow-sm p-6">
                    <div class="w-16 h-16 bg-green-50 text-green-500 rounded-full flex items-center justify-center mx-auto mb-3">
                        <ion-icon name="checkmark-done" class="text-3xl"></ion-icon>
                    </div>
                    <h3 class="text-lg font-bold text-secondary-700">All Caught Up!</h3>
                    <p class="text-xs text-secondary-400 mt-1">No pending deliveries at the moment. New assigned tasks will appear here.</p>
                </div>
            <?php else: ?>
                <?php foreach ($orders as $o): ?>
                    <?php 
                        $isOut = ($o['status'] === 'out_for_delivery');
                        $cleanPhone = preg_replace('/[^0-9]/', '', $o['customer_phone'] ?? '');
                        if (substr($cleanPhone, 0, 1) === '0') {
                            $waPhone = '88' . $cleanPhone;
                        } else {
                            $waPhone = $cleanPhone;
                        }

                        $lat = !empty($o['latitude']) ? $o['latitude'] : ($o['customer_lat'] ?? '');
                        $lng = !empty($o['longitude']) ? $o['longitude'] : ($o['customer_lng'] ?? '');
                        $hasLocation = (!empty($lat) && !empty($lng) && floatval($lat) != 0 && floatval($lng) != 0);

                        // Build comprehensive Bengali location string
                        $locationParts = [];
                        if (!empty($o['point_name'])) $locationParts[] = 'পয়েন্ট: ' . $o['point_name'];
                        if (!empty($o['zone_name']))  $locationParts[] = 'জোন: ' . $o['zone_name'];
                        if (!empty($o['area_name']))  $locationParts[] = 'এরিয়া: ' . $o['area_name'];
                        
                        $extraAddr = !empty($o['delivery_address']) ? $o['delivery_address'] : (!empty($o['customer_address_details']) ? $o['customer_address_details'] : '');
                        if (!empty($extraAddr)) $locationParts[] = $extraAddr;

                        $formattedAddress = !empty($locationParts) ? implode(', ', $locationParts) : 'ঠিকানা দেওয়া হয়নি';

                        if ($hasLocation) {
                            $mapLink = "https://www.google.com/maps/dir/?api=1&destination={$lat},{$lng}";
                        } else {
                            $mapLink = "https://www.google.com/maps/search/?api=1&query=" . urlencode($formattedAddress . ', Chandpur, Bangladesh');
                        }

                        $itemCount = !empty($o['items']) ? count($o['items']) : 0;
                        $orderHasModifiedItem = false;
                        if (!empty($o['items'])) {
                            foreach ($o['items'] as $it) {
                                if ((intval($it['return_qty'] ?? 0) > 0) || (intval($it['damage_qty'] ?? 0) > 0)) {
                                    $orderHasModifiedItem = true;
                                    break;
                                }
                            }
                        }

                        $escapedName = htmlspecialchars(json_encode($o['customer_name'] ?? 'Customer'), ENT_QUOTES, 'UTF-8');
                        $escapedPhone = htmlspecialchars(json_encode($o['customer_phone'] ?? ''), ENT_QUOTES, 'UTF-8');
                        $escapedAddress = htmlspecialchars(json_encode($formattedAddress), ENT_QUOTES, 'UTF-8');
                        $escapedLat = htmlspecialchars(json_encode(strval($lat ?: '23.2321')), ENT_QUOTES, 'UTF-8');
                        $escapedLng = htmlspecialchars(json_encode(strval($lng ?: '90.6631')), ENT_QUOTES, 'UTF-8');
                        $escapedHasGps = $hasLocation ? 'true' : 'false';
                    ?>
                    <div x-show="activeFilter === 'all' || activeFilter === '<?php echo $isOut ? 'out_for_delivery' : 'pending'; ?>'" class="bg-white rounded-2xl shadow-sm border border-secondary-100 overflow-hidden relative transition-all hover:shadow-md">
                        <!-- Status Accent Strip -->
                        <div class="absolute left-0 top-0 bottom-0 w-2 <?php echo $isOut ? 'bg-amber-500' : 'bg-primary-500'; ?>"></div>

                        <div class="p-4 pl-5">
                            <!-- Top Row: Order ID & Status Badge -->
                            <div class="flex justify-between items-center mb-2">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-xs font-mono font-bold text-secondary-500 bg-secondary-100 px-2 py-0.5 rounded">#<?php echo $o['id']; ?></span>
                                    <?php if ($orderHasModifiedItem): ?>
                                        <span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-orange-100 text-orange-700 flex items-center gap-0.5">
                                            <ion-icon name="create-outline"></ion-icon> Modified
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <?php if ($isOut): ?>
                                        <span class="text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider bg-amber-100 text-amber-800 flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> On The Way
                                        </span>
                                    <?php else: ?>
                                        <span class="text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider bg-blue-100 text-blue-800">
                                            Ready To Pick
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Customer Name & Quick Contacts -->
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h3 class="font-bold text-lg text-secondary-900 leading-tight"><?php echo htmlspecialchars($o['customer_name']); ?></h3>
                                    <p class="text-xs font-mono text-secondary-500 mt-0.5"><?php echo htmlspecialchars($o['customer_phone']); ?></p>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <!-- Phone Call -->
                                    <a href="tel:<?php echo htmlspecialchars($o['customer_phone']); ?>" class="w-9 h-9 rounded-full bg-green-100 text-green-700 flex items-center justify-center hover:bg-green-200 transition-colors" title="Call Customer">
                                        <ion-icon name="call" class="text-lg"></ion-icon>
                                    </a>
                                    <!-- WhatsApp -->
                                    <?php if (!empty($cleanPhone)): ?>
                                    <a href="https://wa.me/<?php echo $waPhone; ?>?text=<?php echo urlencode("Hello " . $o['customer_name'] . ", Sodai Dorkar delivery partner is on the way with your order #" . $o['id']); ?>" target="_blank" class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center hover:bg-emerald-200 transition-colors" title="WhatsApp Message">
                                        <ion-icon name="logo-whatsapp" class="text-lg"></ion-icon>
                                    </a>
                                    <?php endif; ?>
                                    <!-- Google Maps -->
                                    <a href="<?php echo $mapLink; ?>" target="_blank" class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center hover:bg-blue-200 transition-colors" title="Open in Google Maps">
                                        <ion-icon name="navigate" class="text-lg"></ion-icon>
                                    </a>
                                </div>
                            </div>

                            <!-- Address & Total -->
                            <div class="space-y-2 bg-secondary-50 p-3 rounded-xl mb-3 text-xs">
                                <div class="flex items-start text-secondary-700">
                                    <ion-icon name="location" class="text-base mr-2 mt-0.5 <?php echo $hasLocation ? 'text-primary-600' : 'text-amber-500'; ?> shrink-0"></ion-icon>
                                    <div class="leading-relaxed flex-1">
                                        <span class="font-semibold text-secondary-900"><?php echo htmlspecialchars($formattedAddress); ?></span>
                                        <?php if ($hasLocation): ?>
                                            <span class="inline-flex items-center gap-0.5 text-[9px] font-bold text-emerald-700 bg-emerald-100 px-1.5 py-0.2 rounded ml-1">
                                                <ion-icon name="pin"></ion-icon> GPS পিন সেট
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-0.5 text-[9px] font-bold text-amber-700 bg-amber-100 px-1.5 py-0.2 rounded ml-1">
                                                <ion-icon name="alert-circle"></ion-icon> GPS পিন নেই
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center pt-2 border-t border-secondary-200">
                                    <span class="text-secondary-500 font-medium">To Collect (COD):</span>
                                    <div class="text-right">
                                        <span class="font-black text-sm text-secondary-900">৳ <?php echo number_format($o['total_amount'], 2); ?></span>
                                        <?php if (!empty($o['original_amount']) && floatval($o['original_amount']) > floatval($o['total_amount'])): ?>
                                            <del class="text-[10px] text-secondary-400 block -mt-1">৳ <?php echo number_format($o['original_amount'], 2); ?></del>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <?php $showTransfer = (!empty($allowTransfer) && !empty($otherRiders)); ?>
                            <!-- Secondary Management Actions -->
                            <div class="grid grid-cols-4 gap-1.5 mb-3">
                                <!-- Cancel -->
                                <button type="button" @click="openCancel(<?php echo $o['id']; ?>, <?php echo $escapedName; ?>)" class="py-1.5 rounded-lg border border-red-200 text-red-600 bg-red-50 hover:bg-red-100 transition-colors flex flex-col items-center justify-center gap-0.5 shadow-2xs" title="Cancel Delivery">
                                    <ion-icon name="close-circle-outline" class="text-sm"></ion-icon>
                                    <span class="text-[9px] font-bold">Cancel</span>
                                </button>
                            
                                <!-- Note -->
                                <button type="button" @click="openNote(<?php echo $o['id']; ?>, <?php echo $escapedName; ?>, <?php echo htmlspecialchars(json_encode($o['rider_note'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>)" class="py-1.5 rounded-lg border border-secondary-200 text-secondary-600 bg-white hover:bg-secondary-50 transition-colors flex flex-col items-center justify-center gap-0.5 shadow-2xs" title="Add Note">
                                    <ion-icon name="create-outline" class="text-sm"></ion-icon>
                                    <span class="text-[9px] font-bold">Note</span>
                                </button>
                            
                                <!-- Location -->
                                <button type="button" @click="openLocation(<?php echo $o['id']; ?>, <?php echo intval($o['customer_id']); ?>, <?php echo $escapedLat; ?>, <?php echo $escapedLng; ?>, <?php echo $escapedName; ?>, <?php echo $escapedPhone; ?>, <?php echo $escapedAddress; ?>, <?php echo $escapedHasGps; ?>)" class="py-1.5 rounded-lg border border-secondary-200 text-secondary-600 bg-white hover:bg-secondary-50 transition-colors flex flex-col items-center justify-center gap-0.5 shadow-2xs" title="Update Location">
                                    <ion-icon name="location-outline" class="text-sm text-primary-600"></ion-icon>
                                    <span class="text-[9px] font-bold">Location</span>
                                </button>
                            
                                <!-- Transfer -->
                                <?php if ($showTransfer): ?>
                                    <button type="button" @click="openTransfer(<?php echo $o['id']; ?>, <?php echo $escapedName; ?>)" class="py-1.5 rounded-lg border border-secondary-200 text-secondary-600 bg-white hover:bg-secondary-50 transition-colors flex flex-col items-center justify-center gap-0.5 shadow-2xs" title="Transfer">
                                        <ion-icon name="swap-horizontal" class="text-sm"></ion-icon>
                                        <span class="text-[9px] font-bold">Transfer</span>
                                    </button>
                                <?php else: ?>
                                    <div class="py-1.5 rounded-lg border border-dashed border-secondary-200 bg-secondary-50/50 flex flex-col items-center justify-center opacity-50">
                                        <ion-icon name="swap-horizontal" class="text-sm text-secondary-400"></ion-icon>
                                        <span class="text-[9px] font-bold text-secondary-400">Transfer</span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Order Items Accordion Button -->
                            <div class="mb-3">
                                <button type="button" @click="expandedOrder = (expandedOrder === <?php echo $o['id']; ?> ? null : <?php echo $o['id']; ?>)" class="w-full py-1.5 px-3 rounded-lg border border-secondary-200 bg-white text-secondary-700 text-xs font-semibold flex justify-between items-center hover:bg-secondary-50 transition-colors">
                                    <span class="flex items-center gap-1.5">
                                        <ion-icon name="cube-outline" class="text-primary-600 text-sm"></ion-icon>
                                        <span>View Items (<?php echo $itemCount; ?> Products<?php if ($orderHasModifiedItem): ?> • Modified<?php endif; ?>)</span>
                                    </span>
                                    <ion-icon :name="expandedOrder === <?php echo $o['id']; ?> ? 'chevron-up' : 'chevron-down'" class="text-secondary-400"></ion-icon>
                                </button>

                                <!-- Expandable Items List -->
                                <div x-show="expandedOrder === <?php echo $o['id']; ?>" x-collapse class="mt-2 bg-secondary-50 p-2.5 rounded-xl border border-secondary-200">
                                    <?php if (empty($o['items'])): ?>
                                        <p class="text-[11px] text-secondary-400 text-center py-2">No item breakdown available.</p>
                                    <?php else: ?>
                                        <ul class="divide-y divide-secondary-200 text-xs">
                                            <?php foreach ($o['items'] as $item): ?>
                                                <?php 
                                                    $ret = intval($item['return_qty'] ?? 0);
                                                    $dmg = intval($item['damage_qty'] ?? 0);
                                                    $delivQty = max(0, intval($item['quantity']) - $ret - $dmg);
                                                    $effectiveTotal = $delivQty * floatval($item['price']);
                                                    $isItemMod = ($ret > 0 || $dmg > 0);
                                                ?>
                                                <li class="py-2 flex justify-between items-start">
                                                    <div class="flex-1 pr-2">
                                                        <p class="font-semibold text-secondary-800 line-clamp-1">
                                                            <?php echo htmlspecialchars($item['product_name']); ?>
                                                            <?php if (!empty($item['unit_title'])): ?>
                                                                <span class="text-[10px] text-secondary-400 font-normal">(<?php echo htmlspecialchars($item['unit_title']); ?>)</span>
                                                            <?php endif; ?>
                                                        </p>
                                                        <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                                            <span class="text-[11px] font-bold <?php echo $isItemMod ? 'text-primary-700 font-mono' : 'text-secondary-600'; ?>">
                                                                Qty: <?php echo $delivQty; ?> <?php if ($isItemMod): ?>(মূল অর্ডার: <?php echo $item['quantity']; ?>)<?php endif; ?>
                                                            </span>
                                                            <span class="text-[10px] text-secondary-400">× ৳<?php echo number_format($item['price'], 2); ?></span>
                                                            <?php if ($ret > 0): ?>
                                                                <span class="px-1.5 py-0.2 bg-orange-100 text-orange-700 text-[9px] font-bold rounded">ফেরত: <?php echo $ret; ?></span>
                                                            <?php endif; ?>
                                                            <?php if ($dmg > 0): ?>
                                                                <span class="px-1.5 py-0.2 bg-red-100 text-red-700 text-[9px] font-bold rounded">ড্যামেজ: <?php echo $dmg; ?></span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <div class="text-right shrink-0">
                                                        <span class="font-bold text-secondary-900 text-xs">৳<?php echo number_format($effectiveTotal, 2); ?></span>
                                                        <?php if ($isItemMod): ?>
                                                            <del class="text-[10px] text-secondary-400 block">৳<?php echo number_format($item['quantity'] * $item['price'], 2); ?></del>
                                                        <?php endif; ?>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            </div>

                                <!-- Admin & Rider Notes if present -->
                                <?php if (!empty($o['admin_note'])): ?>
                                    <div class="p-2.5 bg-blue-50 border border-blue-200 rounded-xl text-xs text-blue-900 mb-2 flex items-start gap-1.5">
                                        <ion-icon name="information-circle" class="text-base text-blue-600 shrink-0 mt-0.5"></ion-icon>
                                        <div>
                                            <span class="font-bold">Admin Note:</span>
                                            <span><?php echo htmlspecialchars($o['admin_note']); ?></span>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($o['rider_note'])): ?>
                                    <div class="p-2.5 bg-emerald-50 border border-emerald-200 rounded-xl text-xs text-emerald-900 mb-2 flex items-start gap-1.5">
                                        <ion-icon name="document-text-outline" class="text-base text-emerald-600 shrink-0 mt-0.5"></ion-icon>
                                        <div>
                                            <span class="font-bold">My Note:</span>
                                            <span><?php echo htmlspecialchars($o['rider_note']); ?></span>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Primary Actions Row -->
                                <div class="flex items-center gap-2 pt-3 mt-1 border-t border-secondary-100">
                                    <?php if (!$isOut): ?>
                                        <!-- Step 1: Start Delivery -->
                                        <button type="button" @click="openModify(<?php echo $o['id']; ?>, <?php echo $escapedName; ?>, <?php echo floatval($o['total_amount']); ?>, <?php echo floatval($o['delivery_charge'] ?? 0); ?>, <?php echo floatval($o['delivery_discount'] ?? 0); ?>)" class="py-2.5 px-3 rounded-xl border border-orange-200 bg-orange-50 hover:bg-orange-100 text-orange-700 font-bold text-xs transition-all flex justify-center items-center gap-1 shadow-sm" title="Modify Items / Partial Return">
                                            <ion-icon name="create-outline" class="text-sm"></ion-icon>
                                            <span>Modify</span>
                                        </button>
                                        <form action="/sodai-dorkar/public/delivery/update-status" method="POST" class="flex-1 min-w-0">
                                            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                                            <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                            <input type="hidden" name="status" value="out_for_delivery">
                                            <button type="submit" class="w-full py-2.5 px-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm shadow-md transition-all flex justify-center items-center gap-1.5">
                                                <ion-icon name="bicycle" class="text-lg"></ion-icon>
                                                <span>Start Delivery</span>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <!-- Modify Items / Partial Return -->
                                        <button type="button" @click="openModify(<?php echo $o['id']; ?>, <?php echo $escapedName; ?>, <?php echo floatval($o['total_amount']); ?>, <?php echo floatval($o['delivery_charge'] ?? 0); ?>, <?php echo floatval($o['delivery_discount'] ?? 0); ?>)" class="w-1/3 py-2.5 px-2 rounded-xl border border-orange-200 bg-orange-50 hover:bg-orange-100 text-orange-700 font-bold text-xs transition-all flex justify-center items-center gap-1 shadow-sm" title="Partial Return / Modify Items">
                                            <ion-icon name="create-outline" class="text-sm"></ion-icon>
                                            <span>Modify</span>
                                        </button>
                                        
                                        <!-- Confirm Delivery -->
                                        <button type="button" @click="openDeliver(<?php echo $o['id']; ?>, <?php echo $escapedName; ?>, <?php echo htmlspecialchars(json_encode($o['total_amount'] ?? '0'), ENT_QUOTES, 'UTF-8'); ?>, <?php echo htmlspecialchars(json_encode($o['rider_note'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>)" class="w-2/3 min-w-0 py-2.5 px-2 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-bold text-sm shadow-md transition-all flex justify-center items-center gap-1.5">
                                            <ion-icon name="checkmark-done" class="text-lg"></ion-icon>
                                            <span>Mark Delivered</span>
                                        </button>
                                    <?php endif; ?>
                                </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- History List Tab -->
        <div x-show="tab === 'history'" style="display: none;" class="space-y-4 px-1 pb-24" x-data="{ searchHistory: '' }">
            
            <div class="sticky top-0 z-10 bg-secondary-50/90 backdrop-blur-md pt-2 pb-3 mb-2 -mt-2 space-y-2">
                <div class="relative">
                    <ion-icon name="search-outline" class="absolute left-3 top-2.5 text-secondary-400"></ion-icon>
                    <input type="text" x-model="searchHistory" placeholder="Search by name, phone or Order ID..." class="w-full pl-9 pr-4 py-2 bg-white border border-secondary-200 rounded-xl text-xs focus:ring-2 focus:ring-primary-500 outline-none shadow-sm transition-all">
                </div>

                <!-- History Status Filter Pills -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1">
                    <button type="button" @click="historyFilter = 'all'" :class="historyFilter === 'all' ? 'bg-primary-600 text-white font-bold shadow-xs' : 'bg-white text-secondary-600 border border-secondary-200 hover:bg-secondary-50'" class="px-3 py-1.5 rounded-xl text-xs whitespace-nowrap transition-all">
                        All History (<?php echo count($history); ?>)
                    </button>
                    <button type="button" @click="historyFilter = 'delivered'" :class="historyFilter === 'delivered' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'bg-white text-secondary-600 border border-secondary-200 hover:bg-secondary-50'" class="px-3 py-1.5 rounded-xl text-xs whitespace-nowrap transition-all">
                        Delivered (<?php echo $fullStats['today_delivered'] ?? 0; ?>)
                    </button>
                    <button type="button" @click="historyFilter = 'cancelled'" :class="historyFilter === 'cancelled' ? 'bg-red-600 text-white font-bold shadow-xs' : 'bg-white text-secondary-600 border border-secondary-200 hover:bg-secondary-50'" class="px-3 py-1.5 rounded-xl text-xs whitespace-nowrap transition-all">
                        Cancelled (<?php echo $fullStats['today_cancelled'] ?? 0; ?>)
                    </button>
                    <button type="button" @click="historyFilter = 'returned'" :class="historyFilter === 'returned' ? 'bg-orange-600 text-white font-bold shadow-xs' : 'bg-white text-secondary-600 border border-secondary-200 hover:bg-secondary-50'" class="px-3 py-1.5 rounded-xl text-xs whitespace-nowrap transition-all">
                        Returned
                    </button>
                </div>
            </div>
             <?php if (empty($history)): ?>
                <div class="text-center py-16 text-secondary-400 bg-white rounded-2xl border border-secondary-100 shadow-sm p-6">
                    <ion-icon name="document-text-outline" class="text-4xl mb-2 text-secondary-300"></ion-icon>
                    <p class="text-sm font-semibold">No completed tasks today.</p>
                </div>
            <?php else: ?>
                <?php foreach ($history as $h): ?>
                    <?php $isDelivered = ($h['status'] === 'delivered'); ?>
                    <div x-show="(historyFilter === 'all' || historyFilter === '<?php echo $h['status']; ?>') && (searchHistory === '' || '<?php echo strtolower(htmlspecialchars($h['customer_name'])); ?>'.includes(searchHistory.toLowerCase()) || '<?php echo $h['customer_phone']; ?>'.includes(searchHistory) || '<?php echo $h['id']; ?>'.includes(searchHistory))" 
                         class="bg-white rounded-2xl shadow-sm border border-secondary-100 p-4 pl-5 relative overflow-hidden transition-all">
                         <div class="absolute left-0 top-0 bottom-0 w-2 <?php echo $isDelivered ? 'bg-green-500' : ($h['status'] === 'returned' ? 'bg-orange-500' : 'bg-red-500'); ?>"></div>
                        
                        <div class="flex justify-between items-center mb-1.5">
                             <span class="text-xs font-mono font-bold text-secondary-500">Order #<?php echo $h['id']; ?></span>
                             <span class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider <?php echo $isDelivered ? 'bg-green-100 text-green-700' : ($h['status'] === 'returned' ? 'bg-orange-100 text-orange-700' : 'bg-red-100 text-red-700'); ?>">
                                 <?php echo $h['status']; ?>
                             </span>
                        </div>

                        <h3 class="font-bold text-secondary-900 text-sm"><?php echo htmlspecialchars($h['customer_name']); ?></h3>

                        <?php if ($isDelivered): ?>
                            <div class="flex justify-between items-center mt-2 text-xs">
                                 <div class="text-secondary-600">
                                     <span>Method: </span>
                                     <span class="font-bold uppercase bg-secondary-100 px-1.5 py-0.5 rounded text-[10px]"><?php echo $h['payment_method'] ?: 'Cash'; ?></span>
                                     <?php if (!empty($h['payment_trx_id'])): ?>
                                        <span class="text-[10px] text-secondary-400 block font-mono">TrxID: <?php echo htmlspecialchars($h['payment_trx_id']); ?></span>
                                     <?php endif; ?>
                                 </div>
                                 <div class="text-right">
                                     <div class="font-black text-secondary-900">৳ <?php echo number_format($h['total_amount'], 2); ?></div>
                                     <div class="text-[10px] text-secondary-400"><?php echo date('h:i A', strtotime($h['delivered_at'] ?? $h['updated_at'])); ?></div>
                                 </div>
                            </div>
                        <?php else: ?>
                            <div class="mt-2 bg-red-50 p-2.5 rounded-xl text-xs text-red-700">
                                <span class="font-semibold">Reason:</span> <?php echo htmlspecialchars($h['cancel_reason'] ?: 'Cancelled by delivery agent'); ?>
                            </div>

                            <?php if ($h['status'] === 'cancelled'): ?>
                                <form action="/sodai-dorkar/public/delivery/update-status" method="POST" class="mt-2.5">
                                    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                                    <input type="hidden" name="order_id" value="<?php echo $h['id']; ?>">
                                    <input type="hidden" name="status" value="returned">
                                    <button type="submit" onclick="return confirm('Confirm returning this cancelled parcel to warehouse / hub?');" class="w-full py-2 px-3 rounded-xl bg-orange-100 hover:bg-orange-200 text-orange-800 font-bold text-xs transition-all flex items-center justify-center gap-1.5 shadow-2xs">
                                        <ion-icon name="arrow-undo-outline" class="text-sm"></ion-icon>
                                        <span>Return to Hub / Warehouse</span>
                                    </button>
                                </form>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

        <!-- ==================== PARCEL SEARCH TAB ==================== -->
        <div x-show="tab === 'search'" style="display:none;" class="px-1 pb-24">
            <div class="bg-white rounded-2xl border border-secondary-100 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-secondary-100 bg-secondary-50/50">
                    <h2 class="font-black text-secondary-800 text-sm flex items-center gap-2">
                        <ion-icon name="search-circle" class="text-primary-600 text-lg"></ion-icon>
                        Parcel Search
                    </h2>
                    <p class="text-[11px] text-secondary-500 mt-0.5">Search by Order ID, Customer Name or Phone Number</p>
                </div>
                <div class="p-4">
                    <div class="flex gap-2">
                        <input type="text" x-model="searchQuery" @keydown.enter="doSearch()"
                            placeholder="Order ID / Phone / Name..."
                            class="flex-1 border border-secondary-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-400 outline-none">
                        <button @click="doSearch()" :disabled="searchLoading"
                            class="bg-primary-600 hover:bg-primary-700 text-white font-bold px-4 py-2.5 rounded-xl text-sm transition-all flex items-center gap-1 disabled:opacity-50">
                            <ion-icon name="search" class="text-base" x-show="!searchLoading"></ion-icon>
                            <svg x-show="searchLoading" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        </button>
                    </div>
                    <div class="mt-3 space-y-2">
                        <div x-show="searchDone && searchResults.length === 0" class="text-center py-8 text-secondary-400">
                            <ion-icon name="search-outline" class="text-4xl block mb-2"></ion-icon>
                            <p class="text-sm font-semibold">No results found.</p>
                        </div>
                        <template x-for="r in searchResults" :key="r.id">
                            <div class="border border-secondary-200 rounded-xl p-3 bg-secondary-50">
                                <div class="flex justify-between items-start mb-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-black text-secondary-900" x-text="'#' + r.id"></span>
                                        <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold bg-secondary-200 text-secondary-700" x-text="r.status.replace(/_/g,' ')"></span>
                                    </div>
                                    <span class="font-black text-primary-700 text-sm" x-text="'৳' + parseFloat(r.total_amount).toLocaleString()"></span>
                                </div>
                                <div class="text-[11px] text-secondary-700 font-semibold" x-text="r.customer_name"></div>
                                <div class="text-[11px] text-secondary-500 flex items-center gap-1 mt-0.5">
                                    <ion-icon name="call-outline" class="text-xs"></ion-icon>
                                    <span x-text="r.customer_phone"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== MY STATS TAB ==================== -->
        <div x-show="tab === 'stats'" style="display:none;" class="px-1 pb-24 space-y-4">

            <!-- All-time Summary -->
            <div class="bg-white rounded-2xl border border-secondary-100 shadow-sm overflow-hidden">
                <div class="p-4 bg-gradient-to-r from-primary-600 to-emerald-600 text-white">
                    <div class="text-[11px] font-bold uppercase tracking-wide text-primary-100 mb-1">All-time Performance</div>
                    <div class="text-2xl font-black"><?php echo number_format($fullStats['total_delivered'] ?? 0); ?> Deliveries</div>
                    <div class="text-primary-200 text-xs mt-0.5">Total <?php echo number_format($fullStats['total_assigned'] ?? 0); ?> parcels assigned</div>
                </div>
                <div class="grid grid-cols-3 divide-x divide-secondary-100">
                    <div class="p-3 text-center">
                        <div class="text-xl font-black text-emerald-700"><?php echo $fullStats['total_delivered'] ?? 0; ?></div>
                        <div class="text-[10px] text-secondary-500">Delivered</div>
                    </div>
                    <div class="p-3 text-center">
                        <div class="text-xl font-black text-red-600"><?php echo $fullStats['total_cancelled'] ?? 0; ?></div>
                        <div class="text-[10px] text-secondary-500">Cancelled</div>
                    </div>
                    <div class="p-3 text-center">
                        <div class="text-xl font-black text-orange-600"><?php echo $fullStats['total_returned'] ?? 0; ?></div>
                        <div class="text-[10px] text-secondary-500">Returned</div>
                    </div>
                </div>
            </div>

            <!-- Cash & Deposit Summary -->
            <div class="bg-white rounded-2xl border border-secondary-100 shadow-sm overflow-hidden">
                <div class="p-3.5 border-b border-secondary-100 bg-secondary-50/50">
                    <h3 class="font-black text-secondary-800 text-sm flex items-center gap-1.5">
                        <ion-icon name="cash-outline" class="text-emerald-600"></ion-icon> Cash & Deposit Summary
                    </h3>
                </div>
                <div class="p-4 space-y-2">
                    <div class="flex justify-between items-center py-2 border-b border-secondary-100">
                        <div class="text-sm text-secondary-600">Total Cash Collected</div>
                        <div class="font-black text-secondary-900">৳<?php echo number_format($fullStats['total_cash'] ?? 0, 2); ?></div>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-secondary-100">
                        <div class="text-sm text-secondary-600">Total Digital Collected</div>
                        <div class="font-bold text-emerald-700">৳<?php echo number_format($fullStats['total_digital'] ?? 0, 2); ?></div>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-secondary-100">
                        <div class="text-sm text-secondary-600 flex items-center gap-1">
                            <ion-icon name="checkmark-circle" class="text-emerald-500 text-xs"></ion-icon>
                            Deposited (Office)
                        </div>
                        <div class="font-bold text-emerald-700">৳<?php echo number_format($fullStats['total_deposited'] ?? 0, 2); ?></div>
                    </div>
                    <?php $pending = $fullStats['pending_deposit'] ?? 0; ?>
                    <div class="flex justify-between items-center py-2.5 px-3 rounded-xl <?php echo $pending > 0 ? 'bg-red-50 border border-red-100' : 'bg-emerald-50 border border-emerald-100'; ?>">
                        <div class="text-sm font-bold <?php echo $pending > 0 ? 'text-red-700' : 'text-emerald-700'; ?> flex items-center gap-1">
                            <ion-icon name="<?php echo $pending > 0 ? 'warning' : 'checkmark-circle'; ?>" class="text-sm"></ion-icon>
                            Pending Deposit (To be deposited)
                        </div>
                        <div class="font-black text-xl <?php echo $pending > 0 ? 'text-red-700' : 'text-emerald-700'; ?>">৳<?php echo number_format($pending, 0); ?></div>
                    </div>
                </div>
            </div>

            <!-- This Month -->
            <div class="bg-white rounded-2xl border border-secondary-100 shadow-sm overflow-hidden">
                <div class="p-3.5 border-b border-secondary-100 bg-secondary-50/50">
                    <h3 class="font-black text-secondary-800 text-sm flex items-center gap-1.5">
                        <ion-icon name="calendar-outline" class="text-primary-600"></ion-icon>
                        This Month's Report (<?php echo date('F Y'); ?>)
                    </h3>
                </div>
                <div class="grid grid-cols-3 divide-x divide-secondary-100 text-center">
                    <div class="p-3">
                        <div class="text-xl font-black text-secondary-800"><?php echo $fullStats['month_total'] ?? 0; ?></div>
                        <div class="text-[10px] text-secondary-500">Assigned</div>
                    </div>
                    <div class="p-3">
                        <div class="text-xl font-black text-emerald-700"><?php echo $fullStats['month_delivered'] ?? 0; ?></div>
                        <div class="text-[10px] text-secondary-500">Delivered</div>
                    </div>
                    <div class="p-3">
                        <div class="text-sm font-black text-primary-700">৳<?php echo number_format(($fullStats['month_collected'] ?? 0) / 1000, 1); ?>k</div>
                        <div class="text-[10px] text-secondary-500">Collected</div>
                    </div>
                </div>
            </div>

            <!-- Deposit History -->
            <?php if (!empty($collections)): ?>
            <div class="bg-white rounded-2xl border border-secondary-100 shadow-sm overflow-hidden">
                <div class="p-3.5 border-b border-secondary-100 bg-secondary-50/50">
                    <h3 class="font-black text-secondary-800 text-sm flex items-center gap-1.5">
                        <ion-icon name="receipt-outline" class="text-primary-600"></ion-icon>
                        Deposit History
                    </h3>
                </div>
                <div class="divide-y divide-secondary-100">
                    <?php foreach ($collections as $col): ?>
                    <div class="flex items-center justify-between px-4 py-3">
                        <div>
                            <div class="text-xs font-bold text-secondary-800"><?php echo date('d M Y, h:i a', strtotime($col['created_at'])); ?></div>
                            <?php if (!empty($col['note'])): ?><div class="text-[11px] text-secondary-500"><?php echo htmlspecialchars($col['note']); ?></div><?php endif; ?>
                            <?php if (!empty($col['recorded_by_name'])): ?><div class="text-[10px] text-secondary-400">By: <?php echo htmlspecialchars($col['recorded_by_name']); ?></div><?php endif; ?>
                        </div>
                        <div class="font-black text-emerald-700 text-base">৳<?php echo number_format($col['amount'], 0); ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

        </div>


    <!-- ==================== DELIVERY CONFIRMATION MODAL ==================== -->
    <div x-show="deliveryModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4 backdrop-blur-sm" x-transition>
        <div @click.away="deliveryModal = false" class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl relative">
            <div class="flex justify-between items-center mb-4 pb-2 border-b border-secondary-100">
                <div>
                    <h3 class="font-black text-lg text-secondary-900">Confirm Delivery</h3>
                    <p class="text-xs text-secondary-400">Order #<span x-text="activeOrderId"></span> • <span x-text="activeCustomerName"></span></p>
                </div>
                <button @click="deliveryModal = false" class="text-secondary-400 hover:text-secondary-600">
                    <ion-icon name="close" class="text-2xl"></ion-icon>
                </button>
            </div>

            <form action="/sodai-dorkar/public/delivery/update-status" method="POST">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                <input type="hidden" name="order_id" :value="activeOrderId">
                <input type="hidden" name="status" value="delivered">

                <!-- Amount Breakdown & Collection Card -->
                <div class="bg-gradient-to-br from-emerald-50 to-green-50 border border-emerald-200 rounded-2xl p-4 mb-4">
                    <div class="flex justify-between items-center text-xs text-secondary-600 mb-1">
                        <span>Original Bill:</span>
                        <span class="font-bold text-secondary-800">৳ <span x-text="activeOrderAmount.toFixed(2)"></span></span>
                    </div>
                    <div class="flex justify-between items-center text-xs text-red-600 mb-2" x-show="parseFloat(deliveryDiscount || 0) > 0">
                        <span>Delivery Discount:</span>
                        <span class="font-bold font-mono">- ৳ <span x-text="parseFloat(deliveryDiscount || 0).toFixed(2)"></span></span>
                    </div>
                    <div class="border-t border-emerald-200 pt-2 flex justify-between items-center">
                        <span class="text-xs text-emerald-800 uppercase tracking-wider font-bold">Final Collected</span>
                        <span class="text-2xl font-black text-emerald-700 font-mono">৳ <span x-text="Math.max(0, activeOrderAmount - (parseFloat(deliveryDiscount) || 0)).toFixed(2)"></span></span>
                    </div>
                </div>

                <!-- Special Delivery Discount (Optional) -->
                <div class="mb-4 bg-secondary-50 p-3 rounded-2xl border border-secondary-200">
                    <label class="block text-xs font-bold text-secondary-700 mb-1.5 flex items-center justify-between">
                        <span class="flex items-center gap-1">
                            <ion-icon name="pricetag-outline" class="text-primary-600 text-sm"></ion-icon>
                            <span>Delivery Discount</span>
                        </span>
                        <span class="text-[10px] text-secondary-400 font-normal">Optional</span>
                    </label>
                    <div class="grid grid-cols-5 gap-2">
                        <div class="col-span-2 relative">
                            <span class="absolute left-2.5 top-2 text-xs font-bold text-secondary-400">৳</span>
                            <input type="number" step="any" min="0" :max="activeOrderAmount" name="delivery_discount" x-model.number="deliveryDiscount" placeholder="0.00" class="w-full pl-6 pr-2 py-2 border border-secondary-300 rounded-xl text-xs font-bold bg-white focus:ring-2 focus:ring-primary-500 focus:outline-none font-mono">
                        </div>
                        <div class="col-span-3">
                            <input type="text" name="amount_change_reason" x-model="amountChangeReason" placeholder="Reason (e.g. Broken item, round off)" class="w-full px-2.5 py-2 border border-secondary-300 rounded-xl text-[11px] bg-white focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        </div>
                    </div>
                </div>

                <!-- Payment Method Selector -->
                <div class="mb-4">
                    <label class="block text-xs font-bold text-secondary-700 uppercase tracking-wider mb-2">Payment Method</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" @click="paymentMethod = 'cash'" :class="paymentMethod === 'cash' ? 'bg-primary-600 text-white font-bold border-primary-600' : 'bg-secondary-50 text-secondary-700 border-secondary-200'" class="py-2.5 rounded-xl border text-xs text-center transition-all cursor-pointer">
                            Cash
                        </button>
                        <button type="button" @click="paymentMethod = 'bkash'" :class="paymentMethod === 'bkash' ? 'bg-pink-600 text-white font-bold border-pink-600' : 'bg-secondary-50 text-secondary-700 border-secondary-200'" class="py-2.5 rounded-xl border text-xs text-center transition-all cursor-pointer">
                            bKash
                        </button>
                        <button type="button" @click="paymentMethod = 'nagad'" :class="paymentMethod === 'nagad' ? 'bg-orange-600 text-white font-bold border-orange-600' : 'bg-secondary-50 text-secondary-700 border-secondary-200'" class="py-2.5 rounded-xl border text-xs text-center transition-all cursor-pointer">
                            Nagad
                        </button>
                    </div>
                    <input type="hidden" name="payment_method" :value="paymentMethod">
                </div>

                <!-- Transaction ID if Digital -->
                <div class="mb-4" x-show="paymentMethod !== 'cash'" x-transition>
                    <label class="block text-xs font-bold text-secondary-700 mb-1">bKash/Nagad Transaction ID (TrxID)</label>
                    <input type="text" name="payment_trx_id" x-model="trxId" placeholder="e.g. 9J832KLM..." class="w-full px-3 py-2 border border-secondary-300 rounded-xl text-sm font-mono focus:ring-2 focus:ring-primary-500 focus:outline-none">
                </div>

                <!-- Delivery / Parcel Note -->
                <div class="mb-5">
                    <label class="block text-xs font-bold text-secondary-700 mb-1.5 flex items-center justify-between">
                        <span class="flex items-center gap-1">
                            <ion-icon name="create-outline" class="text-primary-600 text-sm"></ion-icon>
                            <span>Parcel / Delivery Note</span>
                        </span>
                        <span class="text-[10px] text-secondary-400 font-normal">Optional</span>
                    </label>
                    <textarea name="rider_note" x-model="riderNote" rows="2" placeholder="e.g. Handed over to brother, delivered to 2nd floor..." class="w-full px-3 py-2 border border-secondary-300 rounded-xl text-xs bg-white focus:ring-2 focus:ring-primary-500 focus:outline-none"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-2">
                    <button type="button" @click="deliveryModal = false" class="w-full py-3 rounded-xl border border-secondary-200 text-secondary-600 font-bold text-xs hover:bg-secondary-50 transition-colors">
                        Close
                    </button>
                    <button type="submit" class="w-full py-3 rounded-xl bg-primary-600 text-white font-bold text-xs shadow-lg hover:bg-primary-700 transition-colors">
                        Mark Delivered
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== CANCELLATION REASON MODAL ==================== -->
    <div x-show="cancelModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4 backdrop-blur-sm" x-transition>
        <div @click.away="cancelModal = false" class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl relative">
            <div class="flex justify-between items-center mb-4 pb-2 border-b border-secondary-100">
                <div>
                    <h3 class="font-black text-lg text-red-600">Cancel Order</h3>
                    <p class="text-xs text-secondary-400">Order #<span x-text="activeOrderId"></span></p>
                </div>
                <button @click="cancelModal = false" class="text-secondary-400 hover:text-secondary-600">
                    <ion-icon name="close" class="text-2xl"></ion-icon>
                </button>
            </div>

            <form action="/sodai-dorkar/public/delivery/update-status" method="POST">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                <input type="hidden" name="order_id" :value="activeOrderId">
                <input type="hidden" name="status" value="cancelled">

                <div class="mb-4">
                    <label class="block text-xs font-bold text-secondary-700 mb-2 uppercase tracking-wider">Select Reason</label>
                    <select name="cancel_reason" class="w-full px-3 py-2.5 border border-secondary-300 rounded-xl text-xs bg-white focus:ring-2 focus:ring-red-500 focus:outline-none">
                        <option value="Customer phone switched off / unreachable">Customer phone unreachable</option>
                        <option value="Customer refused to accept delivery">Customer refused delivery</option>
                        <option value="Incorrect delivery address provided">Incorrect delivery address</option>
                        <option value="Customer requested rescheduling/delay">Customer requested delay</option>
                        <option value="Damaged or spoiled products">Damaged/Spoiled product</option>
                        <option value="Other reason">Other</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-2">
                    <button type="button" @click="cancelModal = false" class="w-full py-3 rounded-xl border border-secondary-200 text-secondary-600 font-bold text-xs hover:bg-secondary-50 transition-colors">
                        Back
                    </button>
                    <button type="submit" class="w-full py-3 rounded-xl bg-red-600 text-white font-bold text-xs shadow-lg hover:bg-red-700 transition-colors">
                        Confirm Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== PARCEL TRANSFER MODAL ==================== -->
    <div x-show="transferModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4 backdrop-blur-sm" x-transition>
        <div @click.away="transferModal = false" class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl relative">
            <div class="flex justify-between items-center mb-4 pb-2 border-b border-secondary-100">
                <div>
                    <h3 class="font-black text-lg text-secondary-900 flex items-center gap-1.5">
                        <ion-icon name="swap-horizontal" class="text-primary-600"></ion-icon>
                        <span>Transfer Parcel</span>
                    </h3>
                    <p class="text-xs text-secondary-400 mt-0.5">Order #<span x-text="activeOrderId"></span> • <span x-text="activeCustomerName"></span></p>
                </div>
                <button @click="transferModal = false" class="text-secondary-400 hover:text-secondary-600">
                    <ion-icon name="close" class="text-2xl"></ion-icon>
                </button>
            </div>

            <form action="/sodai-dorkar/public/delivery/transfer-order" method="POST">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                <input type="hidden" name="order_id" :value="activeOrderId">

                <!-- Target Delivery Partner -->
                <div class="mb-4">
                    <label class="block text-xs font-bold text-secondary-700 mb-1.5 uppercase tracking-wider">Select Delivery Partner *</label>
                    <select name="target_dm_id" required class="w-full px-3 py-2.5 border border-secondary-300 rounded-xl text-xs bg-white focus:ring-2 focus:ring-primary-500 focus:outline-none font-bold text-secondary-800">
                        <option value="">Choose a delivery partner...</option>
                        <?php if (!empty($otherRiders)): foreach ($otherRiders as $r): ?>
                            <option value="<?php echo $r['id']; ?>">
                                <?php echo htmlspecialchars($r['name']); ?> (<?php echo htmlspecialchars($r['phone'] ?? ''); ?>)
                            </option>
                        <?php endforeach; endif; ?>
                    </select>
                    <p class="text-[10px] text-secondary-400 mt-1">The parcel will be reassigned immediately to this rider.</p>
                </div>

                <!-- Handover Reason -->
                <div class="mb-5">
                    <label class="block text-xs font-bold text-secondary-700 mb-1.5 uppercase tracking-wider">Reason for Handover (Optional)</label>
                    <input type="text" name="transfer_reason" placeholder="e.g. Area swap, bike issue, customer requested..." class="w-full px-3 py-2 border border-secondary-300 rounded-xl text-xs bg-white focus:ring-2 focus:ring-primary-500 focus:outline-none">
                </div>

                <!-- Modal Actions -->
                <div class="grid grid-cols-2 gap-3 pt-1">
                    <button type="button" @click="transferModal = false" class="w-full py-2.5 rounded-xl border border-secondary-200 text-secondary-600 font-bold text-xs hover:bg-secondary-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="w-full py-2.5 rounded-xl bg-primary-600 text-white font-bold text-xs shadow-lg hover:bg-primary-700 transition-colors flex items-center justify-center gap-1">
                        <ion-icon name="checkmark" class="text-base"></ion-icon>
                        <span>Confirm Transfer</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== RIDER NOTE MODAL ==================== -->
    <div x-show="noteModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4 backdrop-blur-sm" x-transition>
        <div @click.away="noteModal = false" class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl relative">
            <div class="flex justify-between items-center mb-4 pb-2 border-b border-secondary-100">
                <div>
                    <h3 class="font-black text-lg text-secondary-900 flex items-center gap-1.5">
                        <ion-icon name="create-outline" class="text-primary-600"></ion-icon>
                        <span>Parcel Note</span>
                    </h3>
                    <p class="text-xs text-secondary-400 mt-0.5">Order #<span x-text="activeOrderId"></span> • <span x-text="activeCustomerName"></span></p>
                </div>
                <button @click="noteModal = false" class="text-secondary-400 hover:text-secondary-600">
                    <ion-icon name="close" class="text-2xl"></ion-icon>
                </button>
            </div>

            <form action="/sodai-dorkar/public/delivery/update-note" method="POST">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                <input type="hidden" name="order_id" :value="activeOrderId">
                <div class="mb-4">
                    <label class="block text-xs font-bold text-secondary-700 mb-1.5 uppercase tracking-wider">Rider Note on Parcel</label>
                    <textarea name="rider_note" x-model="activeRiderNote" rows="3" required placeholder="e.g. Customer will be home after 5 PM, deliver to shop..." class="w-full px-3 py-2.5 border border-secondary-300 rounded-xl text-xs bg-white focus:ring-2 focus:ring-primary-500 focus:outline-none"></textarea>
                    <p class="text-[10px] text-secondary-400 mt-1">This note will be saved and visible to Admin in the order details.</p>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-1">
                    <button type="button" @click="noteModal = false" class="w-full py-2.5 rounded-xl border border-secondary-200 text-secondary-600 font-bold text-xs hover:bg-secondary-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="w-full py-2.5 rounded-xl bg-primary-600 text-white font-bold text-xs shadow-lg hover:bg-primary-700 transition-colors flex items-center justify-center gap-1">
                        <ion-icon name="save-outline" class="text-base"></ion-icon>
                        <span>Save Note</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== MODIFY ITEMS MODAL (Partial Return) ==================== -->
    <div x-show="modifyModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4 backdrop-blur-sm" x-transition>
        <div @click.away="modifyModal = false" class="bg-white rounded-3xl max-w-sm w-full p-5 shadow-2xl relative">
            <div class="flex justify-between items-center mb-3 pb-2 border-b border-secondary-100">
                <div>
                    <h3 class="font-black text-base text-secondary-900 flex items-center gap-1.5">
                        <ion-icon name="create-outline" class="text-orange-500"></ion-icon>
                        <span>Modify Order Items</span>
                    </h3>
                    <p class="text-[11px] text-secondary-500">Order #<span x-text="activeOrderId" class="font-bold"></span> • <span x-text="activeCustomerName"></span></p>
                </div>
                <button @click="modifyModal = false" class="text-secondary-400 hover:text-secondary-600">
                    <ion-icon name="close" class="text-2xl"></ion-icon>
                </button>
            </div>

            <form action="/sodai-dorkar/public/delivery/modify-order" method="POST">
                <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                <input type="hidden" name="order_id" :value="activeOrderId">

                <div class="max-h-[50vh] overflow-y-auto pr-1 mb-3 space-y-2.5 custom-scrollbar">
                    <template x-for="(item, index) in activeOrderItems" :key="item.id">
                        <div class="bg-secondary-50 border border-secondary-200 rounded-2xl p-3">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1 pr-1">
                                    <h4 class="font-bold text-xs text-secondary-900 leading-tight" x-text="item.product_name"></h4>
                                    <div class="flex items-center gap-1.5 text-[10px] text-secondary-500 mt-0.5">
                                        <span x-show="item.unit_title" x-text="item.unit_title" class="text-secondary-400"></span>
                                        <span class="font-mono">৳<span x-text="item.price.toFixed(2)"></span>/pc</span>
                                        <span class="bg-secondary-200 text-secondary-700 px-1 rounded font-bold">অর্ডার: <span x-text="item.quantity"></span></span>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="font-black text-xs text-primary-700 font-mono">৳<span x-text="((item.deliver_qty || 0) * item.price).toFixed(2)"></span></span>
                                </div>
                            </div>
                            
                            <!-- Hidden inputs for array submission -->
                            <input type="hidden" :name="'items['+index+'][id]'" :value="item.id">
                            <input type="hidden" :name="'items['+index+'][deliver_qty]'" :value="item.deliver_qty">
                            <input type="hidden" :name="'items['+index+'][return_qty]'" :value="item.return_qty">
                            <input type="hidden" :name="'items['+index+'][damage_qty]'" :value="item.damage_qty">

                            <!-- Interactive Quantity Controls -->
                            <div class="grid grid-cols-2 gap-2 mt-2 pt-2 border-t border-secondary-200/60">
                                <!-- Deliver Qty -->
                                <div>
                                    <label class="block text-[10px] font-bold text-emerald-700 mb-1 flex items-center justify-between">
                                        <span class="flex items-center gap-0.5">
                                            <ion-icon name="checkmark-circle-outline"></ion-icon> ডেলিভারি নিবে
                                        </span>
                                    </label>
                                    <div class="flex items-center border border-emerald-300 rounded-xl bg-white overflow-hidden shadow-2xs">
                                        <button type="button" @click="changeDeliverQty(item, -1)" class="w-7 h-7 flex items-center justify-center bg-emerald-50 text-emerald-700 hover:bg-emerald-100 active:bg-emerald-200 font-bold transition-colors">
                                            -
                                        </button>
                                        <input type="number" x-model.number="item.deliver_qty" @input="updateFromDeliver(item)" min="0" :max="item.quantity" class="w-full text-center text-xs font-bold font-mono text-emerald-800 outline-none border-none p-0">
                                        <button type="button" @click="changeDeliverQty(item, 1)" class="w-7 h-7 flex items-center justify-center bg-emerald-50 text-emerald-700 hover:bg-emerald-100 active:bg-emerald-200 font-bold transition-colors">
                                            +
                                        </button>
                                    </div>
                                </div>

                                <!-- Return Qty -->
                                <div>
                                    <label class="block text-[10px] font-bold text-orange-600 mb-1 flex items-center justify-between">
                                        <span class="flex items-center gap-0.5">
                                            <ion-icon name="arrow-undo-outline"></ion-icon> ফেরত দিবে
                                        </span>
                                    </label>
                                    <div class="flex items-center border border-orange-300 rounded-xl bg-white overflow-hidden shadow-2xs">
                                        <button type="button" @click="changeReturnQty(item, -1)" class="w-7 h-7 flex items-center justify-center bg-orange-50 text-orange-700 hover:bg-orange-100 active:bg-orange-200 font-bold transition-colors">
                                            -
                                        </button>
                                        <input type="number" x-model.number="item.return_qty" @input="updateFromReturn(item)" min="0" :max="item.quantity" class="w-full text-center text-xs font-bold font-mono text-orange-800 outline-none border-none p-0">
                                        <button type="button" @click="changeReturnQty(item, 1)" class="w-7 h-7 flex items-center justify-center bg-orange-50 text-orange-700 hover:bg-orange-100 active:bg-orange-200 font-bold transition-colors">
                                            +
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Optional Damage Row if needed -->
                            <div class="mt-2 flex items-center justify-between text-[10px] bg-red-50/50 p-1.5 rounded-lg border border-red-100" x-show="item.damage_qty > 0 || item.quantity > (item.deliver_qty + item.return_qty)">
                                <span class="font-bold text-red-600 flex items-center gap-0.5">
                                    <ion-icon name="alert-circle-outline"></ion-icon> ড্যামেজ/নষ্ট:
                                </span>
                                <input type="number" x-model.number="item.damage_qty" @input="updateFromDamage(item)" min="0" :max="item.quantity" class="w-14 px-1.5 py-0.5 text-center text-xs font-bold text-red-700 border border-red-200 rounded bg-white font-mono">
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Bill Calculation Breakdown Card -->
                <div class="bg-gradient-to-br from-secondary-50 to-primary-50/30 border border-secondary-200 rounded-2xl p-3 mb-3 text-xs">
                    <div class="flex justify-between items-center text-secondary-600 mb-1">
                        <span>আইটেম সাবটোটাল:</span>
                        <span class="font-bold font-mono">৳ <span x-text="getModifiedItemsSubtotal().toFixed(2)"></span></span>
                    </div>
                    <div class="flex justify-between items-center text-secondary-600 mb-1" x-show="activeOrderCharge > 0">
                        <span>ডেলিভারি চার্জ:</span>
                        <span class="font-bold font-mono">+ ৳ <span x-text="activeOrderCharge.toFixed(2)"></span></span>
                    </div>
                    <div class="flex justify-between items-center text-red-600 mb-1" x-show="activeOrderDiscount > 0">
                        <span>ডিসকাউন্ট:</span>
                        <span class="font-bold font-mono">- ৳ <span x-text="activeOrderDiscount.toFixed(2)"></span></span>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t border-secondary-200 text-secondary-900 font-bold">
                        <span class="text-primary-800">নতুন কালেকশন মোট বিল:</span>
                        <span class="text-base font-black text-primary-700 font-mono">৳ <span x-text="getModifiedGrandTotal().toFixed(2)"></span></span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <button type="button" @click="modifyModal = false" class="w-full py-2.5 rounded-xl border border-secondary-200 text-secondary-600 font-bold text-xs hover:bg-secondary-50 transition-colors">
                        বাতিল
                    </button>
                    <button type="submit" class="w-full py-2.5 rounded-xl bg-primary-600 text-white font-bold text-xs shadow-md hover:bg-primary-700 transition-colors flex items-center justify-center gap-1">
                        <ion-icon name="save-outline" class="text-base"></ion-icon>
                        <span>সেভ ও আপডেট</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== LOCATION MODAL ==================== -->
    <div x-show="locationModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4 backdrop-blur-sm" x-transition>
        <div @click.away="locationModal = false" class="bg-white rounded-3xl max-w-sm w-full p-5 shadow-2xl relative">
            <div class="flex justify-between items-center mb-3 pb-2 border-b border-secondary-100">
                <div>
                    <h3 class="font-black text-base text-secondary-900 flex items-center gap-1.5">
                        <ion-icon name="location" class="text-primary-600"></ion-icon>
                        <span>কাস্টমার লোকেশন ও ম্যাপ</span>
                    </h3>
                    <p class="text-[11px] text-secondary-500">Order #<span x-text="activeOrderId" class="font-bold"></span></p>
                </div>
                <button @click="locationModal = false" class="text-secondary-400 hover:text-secondary-600">
                    <ion-icon name="close" class="text-2xl"></ion-icon>
                </button>
            </div>

            <!-- Customer Details Card -->
            <div class="bg-secondary-50 border border-secondary-200 rounded-2xl p-3 mb-3 text-xs">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h4 class="font-bold text-sm text-secondary-900" x-text="activeCustomerName"></h4>
                        <p class="text-[11px] font-mono text-secondary-600 mt-0.5" x-text="activeCustomerPhone"></p>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <a :href="'tel:' + activeCustomerPhone" class="w-7 h-7 rounded-full bg-green-100 text-green-700 flex items-center justify-center hover:bg-green-200 transition-colors" title="Call">
                            <ion-icon name="call" class="text-sm"></ion-icon>
                        </a>
                        <a :href="'https://wa.me/88' + activeCustomerPhone.replace(/[^0-9]/g, '')" target="_blank" class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center hover:bg-emerald-200 transition-colors" title="WhatsApp">
                            <ion-icon name="logo-whatsapp" class="text-sm"></ion-icon>
                        </a>
                    </div>
                </div>
                <div class="text-[11px] text-secondary-700 leading-relaxed pt-1.5 border-t border-secondary-200/60 flex items-start gap-1">
                    <ion-icon name="navigate-outline" class="text-primary-600 text-sm shrink-0 mt-0.5"></ion-icon>
                    <span x-text="activeCustomerAddress || 'ঠিকানা দেওয়া হয়নি'" class="font-medium"></span>
                </div>
                <div class="mt-2 flex items-center justify-between">
                    <span x-show="activeHasGps" class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">
                        <ion-icon name="checkmark-circle"></ion-icon> GPS পিন সংরক্ষিত আছে
                    </span>
                    <span x-show="!activeHasGps" class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full">
                        <ion-icon name="alert-circle"></ion-icon> GPS পিন সেট নেই
                    </span>
                    <a :href="'https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent((activeCustomerAddress || '') + ', Chandpur, Bangladesh')" target="_blank" class="text-[10px] font-bold text-blue-600 hover:text-blue-800 flex items-center gap-0.5">
                        <ion-icon name="open-outline"></ion-icon> Google Maps
                    </a>
                </div>
            </div>

            <form action="/sodai-dorkar/public/delivery/update-location" method="POST">
                <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                <input type="hidden" name="order_id" :value="activeOrderId">
                <input type="hidden" name="customer_id" :value="activeCustomerId">
                <input type="hidden" id="locLat" name="latitude" :value="activeLocationLat">
                <input type="hidden" id="locLng" name="longitude" :value="activeLocationLng">

                <div class="mb-3">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-[10px] font-bold text-secondary-600 uppercase tracking-wider">ম্যাপ পিন লোকেশন:</span>
                        <span id="latLngDisplay" class="text-[10px] font-mono text-secondary-500 font-bold">23.2321, 90.6631</span>
                    </div>
                    <div id="riderLocationMap" class="w-full h-44 rounded-2xl border border-secondary-300 relative z-0 overflow-hidden shadow-inner"></div>
                    <p class="text-[10px] text-secondary-500 mt-1 leading-normal">ম্যাপে ক্লিক করে বা মার্কার টেনে সঠিক বাড়ি/দোকানের ওপর পিন বসান।</p>
                    
                    <button type="button" onclick="getCurrentGps()" class="mt-2 w-full py-2 bg-blue-50 border border-blue-200 text-blue-700 rounded-xl text-xs font-bold flex justify-center items-center gap-1.5 hover:bg-blue-100 transition-colors shadow-2xs">
                        <ion-icon name="locate" class="text-sm"></ion-icon> 
                        <span id="gpsBtnText">আমার বর্তমান GPS লোকেশন নিন</span>
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-2.5 pt-1">
                    <button type="button" @click="locationModal = false" class="w-full py-2.5 rounded-xl border border-secondary-200 text-secondary-600 font-bold text-xs hover:bg-secondary-50 transition-colors">
                        বাতিল
                    </button>
                    <button type="submit" class="w-full py-2.5 rounded-xl bg-primary-600 text-white font-bold text-xs shadow-md hover:bg-primary-700 transition-colors flex items-center justify-center gap-1">
                        <ion-icon name="save-outline" class="text-base"></ion-icon>
                        <span>লোকেশন সেভ করুন</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let riderMap = null;
    let riderMarker = null;

    function initLocationMap(lat, lng) {
        setTimeout(() => {
            const parsedLat = parseFloat(lat);
            const parsedLng = parseFloat(lng);
            // Default to Chandpur center coordinates
            const defaultLat = (!isNaN(parsedLat) && parsedLat !== 0) ? parsedLat : 23.2321;
            const defaultLng = (!isNaN(parsedLng) && parsedLng !== 0) ? parsedLng : 90.6631;

            const locLatEl = document.getElementById('locLat');
            const locLngEl = document.getElementById('locLng');
            if (locLatEl) locLatEl.value = defaultLat.toFixed(7);
            if (locLngEl) locLngEl.value = defaultLng.toFixed(7);

            const latDisplay = document.getElementById('latLngDisplay');
            if (latDisplay) latDisplay.textContent = defaultLat.toFixed(5) + ', ' + defaultLng.toFixed(5);

            if (typeof L === 'undefined') {
                console.warn('Leaflet map library not ready yet.');
                return;
            }

            const mapContainer = document.getElementById('riderLocationMap');
            if (!mapContainer) return;

            if (!riderMap) {
                riderMap = L.map('riderLocationMap').setView([defaultLat, defaultLng], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap'
                }).addTo(riderMap);

                riderMarker = L.marker([defaultLat, defaultLng], {draggable: true}).addTo(riderMap);

                riderMarker.on('dragend', function(event) {
                    var position = riderMarker.getLatLng();
                    if (locLatEl) locLatEl.value = position.lat.toFixed(7);
                    if (locLngEl) locLngEl.value = position.lng.toFixed(7);
                    if (latDisplay) latDisplay.textContent = position.lat.toFixed(5) + ', ' + position.lng.toFixed(5);
                });

                riderMap.on('click', function(e) {
                    riderMarker.setLatLng(e.latlng);
                    if (locLatEl) locLatEl.value = e.latlng.lat.toFixed(7);
                    if (locLngEl) locLngEl.value = e.latlng.lng.toFixed(7);
                    if (latDisplay) latDisplay.textContent = e.latlng.lat.toFixed(5) + ', ' + e.latlng.lng.toFixed(5);
                });
            } else {
                riderMap.setView([defaultLat, defaultLng], 15);
                riderMarker.setLatLng([defaultLat, defaultLng]);
            }

            setTimeout(() => {
                if (riderMap) riderMap.invalidateSize();
            }, 150);
            setTimeout(() => {
                if (riderMap) riderMap.invalidateSize();
            }, 400);
        }, 150);
    }

    function getCurrentGps() {
        const statusBtn = document.getElementById('gpsBtnText');
        if (statusBtn) statusBtn.textContent = 'লোকেশন খোঁজা হচ্ছে...';

        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                const locLatEl = document.getElementById('locLat');
                const locLngEl = document.getElementById('locLng');
                if (locLatEl) locLatEl.value = lat.toFixed(7);
                if (locLngEl) locLngEl.value = lng.toFixed(7);

                const latDisplay = document.getElementById('latLngDisplay');
                if (latDisplay) latDisplay.textContent = lat.toFixed(5) + ', ' + lng.toFixed(5);
                
                if (riderMap && riderMarker) {
                    riderMap.setView([lat, lng], 17);
                    riderMarker.setLatLng([lat, lng]);
                }
                if (statusBtn) statusBtn.textContent = 'বর্তমান GPS সেট হয়েছে ✓';
            }, function(error) {
                if (statusBtn) statusBtn.textContent = 'GPS নেওয়া যায়নি';
                alert("GPS ত্রুটি: " + error.message + "\nঅনুগ্রহ করে ম্যাপে ক্লিক করে পিন বসান।");
            }, {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            });
        } else {
            alert("আপনার ব্রাউজারে Geolocation সাপোর্ট করে না। ম্যাপে ক্লিক করে লোকেশন চিহ্নিত করুন।");
        }
    }
</script>
