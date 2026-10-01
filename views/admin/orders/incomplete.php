<?php 
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
$currentStatus = $currentStatus ?? 'incomplete';
?>

<div class="space-y-6">

    <!-- Flash Messages -->
    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-3">
                <ion-icon name="checkmark-circle" class="text-2xl text-emerald-600"></ion-icon>
                <span class="text-sm font-semibold"><?= htmlspecialchars($_SESSION['flash_success']) ?></span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <ion-icon name="close" class="text-lg"></ion-icon>
            </button>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-3">
                <ion-icon name="alert-circle" class="text-2xl text-rose-600"></ion-icon>
                <span class="text-sm font-semibold"><?= htmlspecialchars($_SESSION['flash_error']) ?></span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                <ion-icon name="close" class="text-lg"></ion-icon>
            </button>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <!-- Top Executive Header -->
    <div class="bg-gradient-to-r from-amber-600 via-amber-700 to-amber-900 rounded-2xl p-6 lg:p-8 text-white shadow-lg relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold tracking-wider text-amber-200 bg-amber-500/20 border border-amber-400/30">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    <span>Live Cart Tracking & Recovery</span>
                </div>
                <h1 class="text-2xl lg:text-3xl font-black tracking-tight">
                    Incomplete Orders & Abandoned Carts (ইনকমপ্লিট অর্ডার)
                </h1>
                <p class="text-amber-100/90 text-sm max-w-2xl leading-relaxed">
                    কাস্টমাররা যে পণ্যগুলো কার্টে যোগ করেছেন কিন্তু এখনো অর্ডার সম্পন্ন করেননি, সেগুলো রিয়েল-টাইমে এখানে দেখা যাচ্ছে। আপনি চাইলে সরাসরি কাস্টমারের সাথে যোগাযোগ করতে পারেন অথবা ১-ক্লিকে এটিকে কনফার্মড অর্ডারে রূপান্তর করতে পারেন।
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="<?= $base ?>/admin/orders" class="bg-white/15 hover:bg-white/25 text-white font-medium py-2.5 px-4 rounded-xl text-sm flex items-center gap-2 transition-all border border-white/20">
                    <ion-icon name="cart-outline" class="text-lg"></ion-icon>
                    <span>Regular Orders</span>
                </a>
                <a href="<?= $base ?>/admin/reports/visitors" class="bg-white text-amber-900 font-bold py-2.5 px-4 rounded-xl text-sm flex items-center gap-2 transition-all shadow-md hover:bg-amber-50">
                    <ion-icon name="analytics-outline" class="text-lg text-amber-600"></ion-icon>
                    <span>Visitor Activity</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 4 KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Card 1: Active Incomplete -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Active Incomplete</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                    <ion-icon name="cart-outline"></ion-icon>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-3xl font-black text-slate-900"><?= number_format($stats['total_incomplete'] ?? 0) ?></h3>
                <p class="text-xs text-amber-600 font-medium mt-1">Pending recovery / unplaced</p>
            </div>
        </div>

        <!-- Card 2: Today's Incomplete -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Added Today</span>
                <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-xl">
                    <ion-icon name="today-outline"></ion-icon>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-3xl font-black text-orange-600"><?= number_format($stats['today_count'] ?? 0) ?></h3>
                <p class="text-xs text-slate-500 font-medium mt-1">Carts generated today</p>
            </div>
        </div>

        <!-- Card 3: Recoverable Amount -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Potential Cart Value</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                    <ion-icon name="cash-outline"></ion-icon>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-3xl font-black text-emerald-700">৳<?= number_format($stats['total_amount'] ?? 0, 2) ?></h3>
                <p class="text-xs text-slate-500 font-medium mt-1">Total revenue potential</p>
            </div>
        </div>

        <!-- Card 4: Converted to Orders -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Converted Orders</span>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                    <ion-icon name="checkmark-done-circle-outline"></ion-icon>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-3xl font-black text-blue-600"><?= number_format($stats['converted_count'] ?? 0) ?></h3>
                <p class="text-xs text-slate-500 font-medium mt-1">Orders successfully placed</p>
            </div>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-wrap gap-2">
            <a href="?status=incomplete" class="px-4 py-2 text-sm rounded-xl font-bold transition-all <?= ($currentStatus === 'incomplete') ? 'bg-amber-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' ?>">
                <span class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                    <span>Incomplete / Open (<?= number_format($stats['total_incomplete'] ?? 0) ?>)</span>
                </span>
            </a>
            <a href="?status=converted" class="px-4 py-2 text-sm rounded-xl font-bold transition-all <?= ($currentStatus === 'converted') ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' ?>">
                <span class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span>Converted to Order (<?= number_format($stats['converted_count'] ?? 0) ?>)</span>
                </span>
            </a>
            <a href="?status=all" class="px-4 py-2 text-sm rounded-xl font-bold transition-all <?= ($currentStatus === 'all') ? 'bg-slate-800 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' ?>">
                <span>All Carts</span>
            </a>
        </div>
        <div class="text-xs text-slate-500 font-medium">
            Showing <?= count($incompletes) ?> records
        </div>
    </div>

    <!-- Incomplete Orders Table -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs font-bold uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Draft ID</th>
                        <th class="px-6 py-4">Customer Details</th>
                        <th class="px-6 py-4">Cart Items</th>
                        <th class="px-6 py-4">Cart Value</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Last Activity</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($incompletes)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <ion-icon name="cart-outline" class="text-5xl text-slate-300 mx-auto mb-2 block"></ion-icon>
                                <span>কোনো ইনকমপ্লিট অর্ডার পাওয়া যায়নি। (No incomplete orders found)</span>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($incompletes as $inc): ?>
                            <tr class="hover:bg-amber-50/40 transition-colors">
                                <td class="px-6 py-4 font-mono font-bold text-amber-700">
                                    <div class="flex items-center gap-1.5">
                                        <span>#INC-<?= $inc['id'] ?></span>
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5" title="IP Address">
                                        IP: <?= htmlspecialchars($inc['ip_address'] ?? 'Unknown') ?>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <?php if (!empty($inc['customer_name']) || !empty($inc['customer_phone'])): ?>
                                        <div class="font-bold text-slate-900">
                                            <?= htmlspecialchars($inc['customer_name'] ?? 'Guest Customer') ?>
                                        </div>
                                        <?php if (!empty($inc['customer_phone'])): ?>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="text-xs font-mono font-semibold text-slate-600"><?= htmlspecialchars($inc['customer_phone']) ?></span>
                                                <a href="tel:<?= htmlspecialchars($inc['customer_phone']) ?>" class="p-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs" title="Call Customer">
                                                    <ion-icon name="call-outline"></ion-icon>
                                                </a>
                                                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $inc['customer_phone']) ?>?text=<?= urlencode('আসসালামু আলাইকুম, আপনার সদাই দরকার কার্ট সম্পর্কিত সহায়তা প্রয়োজন কি?') ?>" target="_blank" class="p-1 rounded bg-emerald-100 hover:bg-emerald-200 text-emerald-800 text-xs flex items-center" title="WhatsApp Message">
                                                    <ion-icon name="logo-whatsapp"></ion-icon>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($inc['customer_address'])): ?>
                                            <div class="text-[11px] text-slate-400 truncate max-w-xs mt-0.5">
                                                <?= htmlspecialchars($inc['customer_address']) ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 text-slate-600 text-xs font-semibold">
                                            <ion-icon name="person-outline"></ion-icon>
                                            <span>Guest Visitor (অন-রেজিস্টার্ড)</span>
                                        </div>
                                        <div class="text-[11px] text-slate-400 mt-1">
                                            <?= htmlspecialchars($inc['ip_address'] ?? '') ?>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-xs font-black">
                                            <?= $inc['items_count'] ?> items
                                        </span>
                                    </div>
                                    <div class="text-xs text-slate-500 mt-1 max-w-xs truncate">
                                        <?php 
                                        $previewNames = [];
                                        foreach (array_slice($inc['items'], 0, 3) as $it) {
                                            $previewNames[] = ($it['name'] ?? 'Item') . ' (' . ($it['quantity'] ?? 1) . ')';
                                        }
                                        echo htmlspecialchars(implode(', ', $previewNames));
                                        if (count($inc['items']) > 3) echo '...';
                                        ?>
                                    </div>
                                </td>

                                <td class="px-6 py-4 font-mono font-bold text-slate-900">
                                    <span class="text-emerald-700 text-base">৳<?= number_format($inc['total_amount'], 2) ?></span>
                                </td>

                                <td class="px-6 py-4">
                                    <?php if ($inc['status'] === 'converted'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            <ion-icon name="checkmark-circle"></ion-icon>
                                            <span>Converted</span>
                                            <?php if (!empty($inc['converted_order_id'])): ?>
                                                <a href="<?= $base ?>/admin/orders/show?id=<?= $inc['converted_order_id'] ?>" class="underline ml-0.5 font-mono">
                                                    #<?= $inc['converted_order_id'] ?>
                                                </a>
                                            <?php endif; ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            <span>Incomplete</span>
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td class="px-6 py-4 text-xs text-slate-500">
                                    <div><?= date('d M Y, h:i A', strtotime($inc['updated_at'])) ?></div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        <?php 
                                        $diffMins = round((time() - strtotime($inc['updated_at'])) / 60);
                                        if ($diffMins < 60) {
                                            echo $diffMins . ' mins ago';
                                        } elseif ($diffMins < 1440) {
                                            echo round($diffMins / 60) . ' hours ago';
                                        } else {
                                            echo round($diffMins / 1440) . ' days ago';
                                        }
                                        ?>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- View Cart Items Button -->
                                        <button onclick="viewIncompleteModal(<?= htmlspecialchars(json_encode($inc)) ?>)" class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors" title="View Cart Items">
                                            <ion-icon name="eye-outline" class="text-base"></ion-icon>
                                        </button>

                                        <?php if ($inc['status'] !== 'converted'): ?>
                                            <!-- Convert to Real Order Form -->
                                            <form action="<?= $base ?>/admin/orders/incomplete/convert" method="POST" onsubmit="return confirm('আপনি কি নিশ্চিত যে এই ইনকমপ্লিট অর্ডারটিকে সরাসরি কনফার্মড অর্ডারে রূপান্তর করতে চান?');" class="inline">
                                                <input type="hidden" name="id" value="<?= $inc['id'] ?>">
                                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-colors shadow-xs" title="Convert to Active Order">
                                                    <ion-icon name="flash-outline"></ion-icon>
                                                    <span>Convert Order</span>
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <!-- Delete Form -->
                                        <form action="<?= $base ?>/admin/orders/incomplete/delete" method="POST" onsubmit="return confirm('মুছে ফেলতে চান?');" class="inline">
                                            <input type="hidden" name="id" value="<?= $inc['id'] ?>">
                                            <button type="submit" class="p-2 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition-colors" title="Delete">
                                                <ion-icon name="trash-outline" class="text-base"></ion-icon>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: View Cart Items -->
<div id="incModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl overflow-hidden border border-slate-200 animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-sm">
                    <ion-icon name="cart"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900" id="incModalTitle">Incomplete Cart Details</h3>
                    <p class="text-xs text-slate-500" id="incModalSub">Customer & items breakdown</p>
                </div>
            </div>
            <button onclick="closeIncModal()" class="w-8 h-8 rounded-full hover:bg-slate-200 flex items-center justify-center text-slate-500">
                <ion-icon name="close" class="text-xl"></ion-icon>
            </button>
        </div>

        <div class="p-6 max-h-[70vh] overflow-y-auto space-y-4">
            <!-- Customer Card -->
            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200 text-xs grid grid-cols-2 gap-3">
                <div>
                    <span class="text-slate-400 block uppercase font-bold text-[10px]">Customer Name</span>
                    <span class="font-bold text-slate-800 text-sm" id="incCustName">-</span>
                </div>
                <div>
                    <span class="text-slate-400 block uppercase font-bold text-[10px]">Phone Number</span>
                    <span class="font-bold text-slate-800 text-sm font-mono" id="incCustPhone">-</span>
                </div>
                <div class="col-span-2">
                    <span class="text-slate-400 block uppercase font-bold text-[10px]">Delivery Address</span>
                    <span class="text-slate-700" id="incCustAddress">-</span>
                </div>
            </div>

            <!-- Items List -->
            <div>
                <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Cart Products (আইটেম তালিকা)</h4>
                <div class="border border-slate-200 rounded-2xl overflow-hidden divide-y divide-slate-100" id="incItemsList">
                    <!-- Populated via JS -->
                </div>
            </div>

            <!-- Total Bar -->
            <div class="bg-amber-50 rounded-2xl p-4 border border-amber-200 flex items-center justify-between">
                <span class="font-bold text-amber-900 text-sm">Estimated Total Amount:</span>
                <span class="font-mono font-black text-xl text-amber-800" id="incModalTotal">৳0.00</span>
            </div>
        </div>

        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-3" id="incModalActions">
            <button onclick="closeIncModal()" class="px-4 py-2 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-200">
                Close
            </button>
        </div>
    </div>
</div>

<script>
function viewIncompleteModal(data) {
    document.getElementById('incModalTitle').innerText = 'Incomplete Order #' + data.id;
    document.getElementById('incModalSub').innerText = 'Last updated: ' + data.updated_at + ' | IP: ' + (data.ip_address || 'N/A');
    
    document.getElementById('incCustName').innerText = data.customer_name || 'Guest Visitor';
    document.getElementById('incCustPhone').innerText = data.customer_phone || 'Not provided';
    document.getElementById('incCustAddress').innerText = data.customer_address || 'Not provided yet';
    document.getElementById('incModalTotal').innerText = '৳' + parseFloat(data.total_amount).toFixed(2);

    const itemsContainer = document.getElementById('incItemsList');
    itemsContainer.innerHTML = '';

    if (!data.items || data.items.length === 0) {
        itemsContainer.innerHTML = '<div class="p-4 text-center text-slate-400 text-xs">No items in cart</div>';
    } else {
        data.items.forEach(item => {
            const itemTotal = (parseFloat(item.price || 0) * parseInt(item.quantity || 1)).toFixed(2);
            const div = document.createElement('div');
            div.className = 'p-3 flex items-center justify-between gap-3 text-sm hover:bg-slate-50';
            div.innerHTML = `
                <div class="flex items-center gap-3">
                    <img src="${item.image || '/sodai-dorkar/public/images/default-product.svg'}" class="w-12 h-12 rounded-xl object-contain border border-slate-200 bg-white p-1">
                    <div>
                        <div class="font-bold text-slate-900">${item.name || 'Product'}</div>
                        <div class="text-xs text-slate-500">
                            ${item.variant_title ? `<span class="bg-slate-100 px-1.5 py-0.5 rounded text-[11px] font-medium mr-1">${item.variant_title}</span>` : ''}
                            ৳${parseFloat(item.price || 0).toFixed(2)} × ${item.quantity || 1}
                        </div>
                    </div>
                </div>
                <div class="font-mono font-bold text-slate-900 text-right">
                    ৳${itemTotal}
                </div>
            `;
            itemsContainer.appendChild(div);
        });
    }

    const actionsContainer = document.getElementById('incModalActions');
    if (data.status !== 'converted') {
        actionsContainer.innerHTML = `
            <button onclick="closeIncModal()" class="px-4 py-2 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-200">
                Close
            </button>
            <form action="<?= $base ?>/admin/orders/incomplete/convert" method="POST" onsubmit="return confirm('আপনি কি নিশ্চিত যে এই ইনকমপ্লিট অর্ডারটিকে সরাসরি কনফার্মড অর্ডারে রূপান্তর করতে চান?');">
                <input type="hidden" name="id" value="${data.id}">
                <button type="submit" class="px-4 py-2 rounded-xl text-sm font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-md flex items-center gap-1.5">
                    <ion-icon name="flash-outline"></ion-icon>
                    <span>Convert to Confirmed Order</span>
                </button>
            </form>
        `;
    } else {
        actionsContainer.innerHTML = `
            <button onclick="closeIncModal()" class="px-4 py-2 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-200">
                Close
            </button>
            <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-200">Already Converted</span>
        `;
    }

    document.getElementById('incModal').classList.remove('hidden');
}

function closeIncModal() {
    document.getElementById('incModal').classList.add('hidden');
}

// Close on escape
window.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeIncModal();
});
</script>
