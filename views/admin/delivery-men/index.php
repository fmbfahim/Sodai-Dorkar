<div class="space-y-6">

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl shadow-xs text-sm font-bold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <ion-icon name="checkmark-circle" class="text-xl text-emerald-600"></ion-icon>
                <span><?php echo htmlspecialchars($_SESSION['success']); ?></span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700"><ion-icon name="close"></ion-icon></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl shadow-xs text-sm font-bold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <ion-icon name="alert-circle" class="text-xl text-red-600"></ion-icon>
                <span><?php echo htmlspecialchars($_SESSION['error']); ?></span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700"><ion-icon name="close"></ion-icon></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Top KPI Summary Bar -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Delivery Staff -->
        <div class="bg-white rounded-2xl p-5 border border-secondary-100 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center shrink-0">
                <ion-icon name="people" class="text-2xl"></ion-icon>
            </div>
            <div>
                <span class="text-xs text-secondary-500 font-bold uppercase tracking-wider">Total Staff</span>
                <div class="text-2xl font-black text-secondary-900 leading-tight mt-0.5">
                    <?php echo $kpis['total_riders'] ?? 0; ?>
                </div>
                <span class="text-[11px] text-secondary-400">Registered delivery riders</span>
            </div>
        </div>

        <!-- Active Tasks Right Now -->
        <div class="bg-white rounded-2xl p-5 border border-secondary-100 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <ion-icon name="bicycle" class="text-2xl"></ion-icon>
            </div>
            <div>
                <span class="text-xs text-secondary-500 font-bold uppercase tracking-wider">Active Tasks</span>
                <div class="text-2xl font-black text-amber-600 leading-tight mt-0.5 flex items-center gap-2">
                    <span><?php echo $kpis['active_tasks'] ?? 0; ?></span>
                    <?php if (($kpis['active_tasks'] ?? 0) > 0): ?>
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                    <?php endif; ?>
                </div>
                <span class="text-[11px] text-secondary-400">Orders in transit or processing</span>
            </div>
        </div>

        <!-- Delivered Today -->
        <div class="bg-white rounded-2xl p-5 border border-secondary-100 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <ion-icon name="checkmark-done-circle" class="text-2xl"></ion-icon>
            </div>
            <div>
                <span class="text-xs text-secondary-500 font-bold uppercase tracking-wider">Delivered Today</span>
                <div class="text-2xl font-black text-emerald-600 leading-tight mt-0.5">
                    <?php echo $kpis['delivered_today'] ?? 0; ?>
                </div>
                <span class="text-[11px] text-secondary-500 font-medium">৳ <?php echo number_format($kpis['delivered_today_amount'] ?? 0); ?> volume</span>
            </div>
        </div>

        <!-- Total Pending Cash (All Riders) -->
        <div class="bg-white rounded-2xl p-5 border border-secondary-100 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                <ion-icon name="cash-outline" class="text-2xl"></ion-icon>
            </div>
            <div>
                <span class="text-xs text-secondary-500 font-bold uppercase tracking-wider">Total Pending Cash</span>
                <div class="text-2xl font-black text-red-700 leading-tight mt-0.5">
                    ৳ <?php echo number_format($kpis['total_pending_cash'] ?? 0); ?>
                </div>
                <span class="text-[11px] text-secondary-400">Total cash owed by all riders</span>
            </div>
        </div>
    </div>

    <!-- Main Content Area: Form & Enhanced Table -->
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6 items-start">
        
        <!-- Left: Add New Delivery Man Form -->
        <div class="xl:col-span-1 bg-white rounded-2xl shadow-xs border border-secondary-100 p-5">
            <div class="flex items-center gap-2 mb-4 pb-2 border-b border-secondary-100">
                <div class="w-8 h-8 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center">
                    <ion-icon name="person-add" class="text-lg"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-secondary-900 text-sm">Add Delivery Rider</h3>
                    <p class="text-[11px] text-secondary-400">Create new login credentials</p>
                </div>
            </div>

            <form action="/sodai-dorkar/public/admin/delivery-men/store" method="POST" class="space-y-3.5">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                <div>
                    <label class="block text-secondary-700 text-xs font-bold mb-1" for="name">Full Name *</label>
                    <input type="text" id="name" name="name" class="w-full px-3 py-2 bg-secondary-50 border border-secondary-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white" required placeholder="e.g. Rahim Uddin">
                </div>
                <div>
                    <label class="block text-secondary-700 text-xs font-bold mb-1" for="phone">Phone Number *</label>
                    <input type="text" id="phone" name="phone" class="w-full px-3 py-2 bg-secondary-50 border border-secondary-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white" required placeholder="017xxxxxxxx">
                </div>
                <div>
                    <label class="block text-secondary-700 text-xs font-bold mb-1" for="username">Username (Login ID) *</label>
                    <input type="text" id="username" name="username" class="w-full px-3 py-2 bg-secondary-50 border border-secondary-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white font-mono" required placeholder="unique username">
                </div>
                <div>
                    <label class="block text-secondary-700 text-xs font-bold mb-1" for="password">Password *</label>
                    <input type="password" id="password" name="password" class="w-full px-3 py-2 bg-secondary-50 border border-secondary-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white" required placeholder="••••••••">
                </div>
                <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-2.5 px-4 rounded-xl text-xs transition-colors shadow-sm flex items-center justify-center gap-1.5 cursor-pointer">
                    <ion-icon name="checkmark-circle" class="text-base"></ion-icon>
                    <span>Create Rider Account</span>
                </button>
            </form>

            <div class="mt-4 pt-4 border-t border-secondary-100 text-center">
                <a href="/sodai-dorkar/public/admin/delivery-men/allocation" class="text-xs font-bold text-primary-600 hover:text-primary-700 inline-flex items-center gap-1">
                    <ion-icon name="git-network-outline"></ion-icon>
                    <span>Manage Area Allocations &rarr;</span>
                </a>
            </div>
        </div>

        <!-- Right: Comprehensive Delivery Staff & Status Table -->
        <div class="xl:col-span-3 bg-white rounded-2xl shadow-xs border border-secondary-100 overflow-hidden">
            <div class="p-5 border-b border-secondary-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-secondary-50/50">
                <div>
                    <h3 class="font-bold text-secondary-900 text-base">Delivery Staff Overview & Live Reports</h3>
                    <p class="text-xs text-secondary-500 mt-0.5">Real-time status, active tasks, cash collected, and performance statistics.</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="/sodai-dorkar/public/admin/delivery-men/collections" class="px-3 py-1.5 bg-emerald-50 border border-emerald-200 text-emerald-700 hover:text-white hover:bg-emerald-600 font-bold text-xs rounded-xl transition-colors shadow-2xs inline-flex items-center gap-1">
                        <ion-icon name="receipt-outline"></ion-icon>
                        <span>Deposit History</span>
                    </a>
                    <a href="/sodai-dorkar/public/admin/delivery-men/allocation" class="px-3 py-1.5 bg-white border border-secondary-200 text-secondary-700 hover:text-secondary-900 font-bold text-xs rounded-xl transition-colors shadow-2xs inline-flex items-center gap-1">
                        <ion-icon name="map-outline"></ion-icon>
                        <span>Area Allocations</span>
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-secondary-600">
                    <thead class="bg-secondary-50 text-secondary-500 uppercase tracking-wider font-bold border-b border-secondary-100">
                        <tr>
                            <th class="px-5 py-3.5">Rider</th>
                            <th class="px-4 py-3.5">Assigned Coverage</th>
                            <th class="px-4 py-3.5 text-center">Active Tasks</th>
                            <th class="px-4 py-3.5 text-center">Today</th>
                            <th class="px-4 py-3.5 text-center">Lifetime / Ratio</th>
                            <th class="px-4 py-3.5 text-right">Pending Cash</th>
                            <th class="px-5 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-secondary-100">
                        <?php if (empty($deliveryMen)): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-secondary-400">
                                    <ion-icon name="people-outline" class="text-4xl text-secondary-300 mb-2"></ion-icon>
                                    <p class="font-medium text-sm">No delivery staff registered yet.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($deliveryMen as $dm): ?>
                                <?php 
                                    // Status Badge styling
                                    $statusBadge = match($dm['work_status']) {
                                        'on_delivery' => ['bg' => 'bg-amber-100 text-amber-800 border-amber-300', 'label' => 'On Delivery', 'pulse' => true],
                                        'busy' => ['bg' => 'bg-blue-100 text-blue-800 border-blue-300', 'label' => 'Processing', 'pulse' => false],
                                        'active' => ['bg' => 'bg-emerald-100 text-emerald-800 border-emerald-300', 'label' => 'Active', 'pulse' => false],
                                        default => ['bg' => 'bg-secondary-100 text-secondary-600 border-secondary-200', 'label' => 'Idle', 'pulse' => false]
                                    };
                                ?>
                                <tr class="hover:bg-secondary-50/70 transition-colors">
                                    <!-- Rider Profile Column -->
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-600 to-emerald-600 text-white font-black text-sm flex items-center justify-center shrink-0 shadow-xs uppercase">
                                                <?php echo htmlspecialchars(substr($dm['name'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div class="font-bold text-secondary-900 text-sm flex items-center gap-1.5">
                                                    <span><?php echo htmlspecialchars($dm['name']); ?></span>
                                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded-full border text-[9px] font-black <?php echo $statusBadge['bg']; ?>">
                                                        <?php if ($statusBadge['pulse']): ?>
                                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                        <?php endif; ?>
                                                        <span><?php echo $statusBadge['label']; ?></span>
                                                    </span>
                                                    <?php if (($dm['overdue_count'] ?? 0) > 0): ?>
                                                        <span class="text-red-500 animate-pulse" title="<?php echo $dm['overdue_count']; ?> orders overdue (>12 hours)">
                                                            <ion-icon name="warning"></ion-icon>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="text-[11px] text-secondary-500 flex items-center gap-2 mt-0.5 font-mono">
                                                    <span><?php echo htmlspecialchars($dm['phone'] ?? '-'); ?></span>
                                                    <span class="text-secondary-300">•</span>
                                                    <span class="bg-secondary-100 text-secondary-600 px-1 py-0.2 rounded text-[10px]">@<?php echo htmlspecialchars($dm['username']); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Assigned Coverage / Unions -->
                                    <td class="px-4 py-3.5 max-w-[200px]">
                                        <?php if (!empty($dm['allocations'])): ?>
                                            <div class="flex flex-wrap gap-1">
                                                <?php foreach ($dm['allocations'] as $alloc): ?>
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-primary-50 text-primary-800 border border-primary-200 px-2 py-0.5 rounded-lg">
                                                        <ion-icon name="location-outline" class="text-xs"></ion-icon>
                                                        <span><?php echo htmlspecialchars($alloc['area_name']); ?></span>
                                                        <span class="text-primary-400 font-mono text-[9px]">(<?php echo htmlspecialchars($alloc['time_slot']); ?>)</span>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <a href="/sodai-dorkar/public/admin/delivery-men/allocation" class="text-[11px] text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-lg inline-flex items-center gap-1 font-medium hover:bg-amber-100 transition-colors">
                                                <ion-icon name="alert-circle-outline"></ion-icon>
                                                <span>No Area Assigned &rarr;</span>
                                            </a>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Active Tasks -->
                                    <td class="px-4 py-3.5 text-center">
                                        <?php if ($dm['active_tasks'] > 0): ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full font-black text-xs bg-amber-100 text-amber-800 border border-amber-300 shadow-2xs">
                                                <ion-icon name="bicycle" class="text-sm"></ion-icon>
                                                <span><?php echo $dm['active_tasks']; ?> Orders</span>
                                            </span>
                                            <?php if ($dm['on_way_count'] > 0): ?>
                                                <div class="text-[10px] text-amber-600 font-bold mt-0.5"><?php echo $dm['on_way_count']; ?> on way</div>
                                            <?php endif; ?>
                                            <?php if (($dm['overdue_count'] ?? 0) > 0): ?>
                                                <div class="text-[10px] text-red-600 font-bold mt-0.5"><ion-icon name="alert-circle" class="align-middle"></ion-icon> <?php echo $dm['overdue_count']; ?> overdue!</div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-secondary-400 font-medium">0 active</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Delivered Today -->
                                    <td class="px-4 py-3.5 text-center">
                                        <div class="font-bold text-secondary-800 text-sm"><?php echo $dm['delivered_today']; ?> orders</div>
                                        <div class="text-[10px] text-emerald-600 font-bold font-mono">৳ <?php echo number_format($dm['delivered_today_amount']); ?></div>
                                    </td>

                                    <!-- Lifetime / Success Ratio -->
                                    <td class="px-4 py-3.5 text-center">
                                        <div class="font-bold text-secondary-900"><?php echo $dm['total_delivered']; ?> orders</div>
                                        <div class="inline-flex items-center gap-1 text-[10px] font-black <?php echo ($dm['success_rate'] >= 80) ? 'text-emerald-600' : 'text-amber-600'; ?>">
                                            <span><?php echo $dm['success_rate']; ?>% Success</span>
                                        </div>
                                    </td>

                                    <!-- Pending Cash -->
                                    <td class="px-4 py-3.5 text-right font-mono">
                                        <?php if (($dm['pending_deposit'] ?? 0) > 0): ?>
                                            <div class="inline-block px-2.5 py-1 rounded-xl bg-red-50 border border-red-200 text-red-700 font-black text-sm" title="Total Collected: ৳<?php echo number_format($dm['total_cash_collected']); ?> | Deposited: ৳<?php echo number_format($dm['total_deposited']); ?>">
                                                ৳ <?php echo number_format($dm['pending_deposit']); ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-emerald-600 font-bold text-xs flex items-center justify-end gap-1" title="All cash handed over">
                                                <ion-icon name="checkmark-circle"></ion-icon> Settled
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Actions Column -->
                                    <td class="px-5 py-3.5 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <?php if (($dm['pending_deposit'] ?? 0) > 0): ?>
                                                <button type="button" onclick="openCashModal(<?php echo $dm['id']; ?>, '<?php echo addslashes($dm['name']); ?>', <?php echo $dm['pending_deposit']; ?>)" class="px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 hover:border-emerald-600 font-bold text-xs rounded-xl transition-all shadow-2xs flex items-center gap-1" title="Receive Cash">
                                                    <ion-icon name="cash-outline" class="text-sm"></ion-icon> Receive
                                                </button>
                                            <?php endif; ?>
                                            
                                            <!-- View Report Button -->
                                            <a href="/sodai-dorkar/public/admin/delivery-men/report?id=<?php echo $dm['id']; ?>" 
                                               class="px-2.5 py-1.5 bg-primary-50 hover:bg-primary-600 text-primary-700 hover:text-white border border-primary-200 hover:border-primary-600 font-bold text-xs rounded-xl transition-all shadow-2xs flex items-center gap-1"
                                               title="View Performance Report">
                                                <ion-icon name="stats-chart" class="text-sm"></ion-icon>
                                                <span>Report</span>
                                            </a>

                                            <!-- Change Password Button -->
                                            <button type="button" onclick="openPasswordModal(<?php echo $dm['id']; ?>, '<?php echo addslashes($dm['name']); ?>')" class="p-1.5 text-secondary-500 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-colors" title="Change Password">
                                                <ion-icon name="key-outline" class="text-base"></ion-icon>
                                            </button>

                                            <!-- Delete Form -->
                                            <form action="/sodai-dorkar/public/admin/delivery-men/delete" method="POST" onsubmit="return confirm('Are you sure you want to delete this delivery rider account?');" class="inline">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                                                <input type="hidden" name="id" value="<?php echo $dm['id']; ?>">
                                                <button type="submit" class="p-1.5 text-secondary-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-colors" title="Delete Account">
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
</div>

<!-- Password Change Modal -->
<div id="passwordModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-sm p-5 relative shadow-2xl">
        <h3 class="font-bold text-lg mb-1 text-secondary-900 flex items-center gap-2">
            <ion-icon name="key" class="text-primary-600"></ion-icon> Change Password
        </h3>
        <p class="text-xs text-secondary-500 mb-4">Rider: <span id="pmRiderName" class="font-bold text-secondary-800"></span></p>
        
        <form action="/sodai-dorkar/public/admin/delivery-men/change-password" method="POST">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
            <input type="hidden" name="id" id="pmRiderId">
            <div class="mb-4">
                <label class="block text-xs font-bold text-secondary-700 mb-1">New Password *</label>
                <input type="text" name="password" required class="w-full px-3 py-2 border border-secondary-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 outline-none" placeholder="Min 4 characters">
            </div>
            <div class="flex gap-2 justify-end mt-2">
                <button type="button" onclick="closePasswordModal()" class="px-4 py-2.5 bg-secondary-100 hover:bg-secondary-200 text-secondary-700 rounded-xl text-xs font-bold transition-colors">Cancel</button>
                <button type="submit" class="px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-xs font-bold shadow-md transition-colors">Update Password</button>
            </div>
        </form>
    </div>
</div>

<script>
function openPasswordModal(id, name) {
    document.getElementById('pmRiderId').value = id;
    document.getElementById('pmRiderName').innerText = name;
    document.getElementById('passwordModal').classList.remove('hidden');
}
function closePasswordModal() {
    document.getElementById('passwordModal').classList.add('hidden');
}

function openCashModal(id, name, maxAmount) {
    document.getElementById('cashRiderId').value = id;
    document.getElementById('cashRiderName').innerText = name;
    document.getElementById('cashAmountInput').value = maxAmount;
    document.getElementById('cashAmountInput').max = maxAmount;
    document.getElementById('cashModal').classList.remove('hidden');
}
function closeCashModal() {
    document.getElementById('cashModal').classList.add('hidden');
}
</script>

<!-- Cash Receive Modal -->
<div id="cashModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-sm p-5 relative shadow-2xl">
        <h3 class="font-bold text-lg mb-1 text-emerald-700 flex items-center gap-2">
            <ion-icon name="wallet"></ion-icon> Receive Cash
        </h3>
        <p class="text-xs text-secondary-500 mb-4">Rider: <span id="cashRiderName" class="font-bold text-secondary-800"></span></p>
        
        <form action="/sodai-dorkar/public/admin/delivery-men/collect-cash" method="POST">
            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
            <input type="hidden" name="dm_id" id="cashRiderId">
            <div class="mb-3 relative">
                <label class="block text-xs font-bold text-secondary-700 mb-1">Amount Received *</label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-secondary-500 font-bold">৳</span>
                    <input type="number" name="amount" id="cashAmountInput" required min="1" step="any" class="w-full pl-7 pr-3 py-2 border border-emerald-300 rounded-xl text-sm font-black focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-secondary-700 mb-1">Note (Optional)</label>
                <input type="text" name="note" class="w-full px-3 py-2 border border-secondary-300 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 outline-none" placeholder="e.g. Paid in full">
            </div>
            <div class="flex gap-2 justify-end mt-2">
                <button type="button" onclick="closeCashModal()" class="px-4 py-2.5 bg-secondary-100 hover:bg-secondary-200 text-secondary-700 rounded-xl text-xs font-bold transition-colors">Cancel</button>
                <button type="submit" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md transition-colors flex items-center gap-1"><ion-icon name="checkmark-circle"></ion-icon> Confirm Receipt</button>
            </div>
        </form>
    </div>
</div>
