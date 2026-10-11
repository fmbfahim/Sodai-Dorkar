<?php
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';

$totalAgents = count($agents);
$activeAgents = 0;
$totalOrdersAll = 0;
$totalSalesAll = 0;

foreach ($agents as $a) {
    if (($a['status'] ?? 'active') === 'active') $activeAgents++;
    $totalOrdersAll += (int)($a['total_orders'] ?? 0);
    $totalSalesAll += (float)($a['total_sales'] ?? 0);
}
?>

<div class="space-y-6">

    <!-- Flash Messages -->
    <?php if (!empty($success)): ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl shadow-xs text-sm font-semibold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <ion-icon name="checkmark-circle" class="text-xl text-emerald-600"></ion-icon>
                <span><?= htmlspecialchars($success) ?></span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 cursor-pointer"><ion-icon name="close"></ion-icon></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-2xl shadow-xs text-sm font-semibold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <ion-icon name="alert-circle" class="text-xl text-red-600"></ion-icon>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 cursor-pointer"><ion-icon name="close"></ion-icon></button>
        </div>
    <?php endif; ?>

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-3xl border border-secondary-200 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-primary-600 uppercase tracking-wider mb-1">
                <ion-icon name="bag-handle-outline" class="text-base"></ion-icon>
                <span>ফিল্ড সেলস টিম</span>
            </div>
            <h1 class="text-2xl font-black text-secondary-900 tracking-tight">ফিল্ড এজেন্ট ব্যবস্থাপনা (Field Agents)</h1>
            <p class="text-xs text-secondary-500 mt-0.5">কাস্টমারদের কাছে সরাসরি গিয়ে অর্ডার নেওয়ার জন্য এজেন্ট তৈরি করুন এবং তাদের নির্দিষ্ট এরিয়া নির্ধারণ করে দিন।</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="openAddModal()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-2xl text-sm font-bold shadow-md hover:shadow-lg transition-all cursor-pointer">
                <ion-icon name="add-circle" class="text-lg"></ion-icon>
                <span>নতুন এজেন্ট যোগ করুন</span>
            </button>
        </div>
    </div>

    <!-- KPI Summary Bar -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Agents -->
        <div class="bg-white rounded-2xl p-4.5 border border-secondary-100 shadow-xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center shrink-0">
                <ion-icon name="people" class="text-2xl"></ion-icon>
            </div>
            <div>
                <span class="text-[11px] text-secondary-500 font-bold uppercase tracking-wider">মোট এজেন্ট</span>
                <div class="text-xl font-black text-secondary-900 leading-tight mt-0.5"><?= $totalAgents ?> জন</div>
                <span class="text-[10px] text-emerald-600 font-bold"><?= $activeAgents ?> জন সক্রিয়</span>
            </div>
        </div>

        <!-- Total Orders Taken -->
        <div class="bg-white rounded-2xl p-4.5 border border-secondary-100 shadow-xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <ion-icon name="cart" class="text-2xl"></ion-icon>
            </div>
            <div>
                <span class="text-[11px] text-secondary-500 font-bold uppercase tracking-wider">মোট গৃহীত অর্ডার</span>
                <div class="text-xl font-black text-amber-600 leading-tight mt-0.5"><?= $totalOrdersAll ?> টি</div>
                <span class="text-[10px] text-secondary-400">এজেন্টদের মাধ্যমে</span>
            </div>
        </div>

        <!-- Total Sales -->
        <div class="bg-white rounded-2xl p-4.5 border border-secondary-100 shadow-xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <ion-icon name="cash" class="text-2xl"></ion-icon>
            </div>
            <div>
                <span class="text-[11px] text-secondary-500 font-bold uppercase tracking-wider">মোট বিক্রয়</span>
                <div class="text-xl font-black text-emerald-600 leading-tight mt-0.5">৳<?= number_format($totalSalesAll, 2) ?></div>
                <span class="text-[10px] text-emerald-600 font-medium">ভ্যালু ক্রিয়েটেড</span>
            </div>
        </div>

        <!-- Operating Unions -->
        <div class="bg-white rounded-2xl p-4.5 border border-secondary-100 shadow-xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <ion-icon name="map" class="text-2xl"></ion-icon>
            </div>
            <div>
                <span class="text-[11px] text-secondary-500 font-bold uppercase tracking-wider">মোট এরিয়া (ইউনিয়ন)</span>
                <div class="text-xl font-black text-blue-600 leading-tight mt-0.5"><?= count($areas) ?> টি</div>
                <span class="text-[10px] text-secondary-400">অপারেশনাল অঞ্চল</span>
            </div>
        </div>
    </div>

    <!-- Agent List Table -->
    <div class="bg-white rounded-3xl border border-secondary-200 shadow-xs overflow-hidden">
        <div class="p-4.5 border-b border-secondary-100 flex items-center justify-between">
            <h2 class="text-base font-black text-secondary-900 flex items-center gap-2">
                <ion-icon name="list-outline" class="text-primary-600"></ion-icon>
                <span>নিবন্ধিত ফিল্ড এজেন্টদের তালিকা</span>
            </h2>
            <span class="text-xs text-secondary-500 font-medium">মোট <?= count($agents) ?> জন এজেন্ট</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-secondary-50 text-secondary-600 uppercase font-black tracking-wider text-[11px] border-b border-secondary-200">
                        <th class="py-3.5 px-4">এজেন্ট ও যোগাযোগ</th>
                        <th class="py-3.5 px-4">ইউজারনেম</th>
                        <th class="py-3.5 px-4">নির্ধারিত এরিয়া (Unions)</th>
                        <th class="py-3.5 px-4 text-center">মোট অর্ডার</th>
                        <th class="py-3.5 px-4 text-right">মোট বিক্রয়</th>
                        <th class="py-3.5 px-4 text-center">স্ট্যাটাস</th>
                        <th class="py-3.5 px-4 text-right">একশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-secondary-100">
                    <?php if (empty($agents)): ?>
                        <tr>
                            <td colspan="7" class="py-12 text-center text-secondary-400">
                                <ion-icon name="people-outline" class="text-4xl mb-2"></ion-icon>
                                <p class="text-sm font-semibold">কোনো ফিল্ড এজেন্ট পাওয়া যায়নি।</p>
                                <p class="text-xs mt-1">উপরে "নতুন এজেন্ট যোগ করুন" বাটনে ক্লিক করে ফিল্ড এজেন্ট তৈরি করুন।</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($agents as $agent): ?>
                            <tr class="hover:bg-secondary-50/60 transition-colors">
                                <!-- Agent Name & Phone -->
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-black text-xs shrink-0">
                                            <?= mb_substr($agent['name'] ?? 'A', 0, 1) ?>
                                        </div>
                                        <div>
                                            <div class="font-bold text-secondary-900 text-sm"><?= htmlspecialchars($agent['name']) ?></div>
                                            <div class="text-[11px] text-secondary-500 font-mono flex items-center gap-1">
                                                <ion-icon name="call-outline"></ion-icon>
                                                <span><?= htmlspecialchars($agent['phone'] ?: 'N/A') ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Username -->
                                <td class="py-3.5 px-4 font-mono font-bold text-secondary-700">
                                    @<?= htmlspecialchars($agent['username']) ?>
                                </td>

                                <!-- Assigned Areas (Unions) -->
                                <td class="py-3.5 px-4">
                                    <?php if (!empty($agent['area_names'])): ?>
                                        <div class="flex flex-wrap gap-1 max-w-xs">
                                            <?php foreach (explode(',', $agent['area_names']) as $areaName): ?>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-bold bg-primary-50 text-primary-700 border border-primary-200/60">
                                                    <?= htmlspecialchars(trim($areaName)) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-amber-600 font-semibold text-[11px] flex items-center gap-1">
                                            <ion-icon name="warning-outline"></ion-icon>
                                            <span>কোনো এরিয়া এসাইন নেই</span>
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Total Orders -->
                                <td class="py-3.5 px-4 text-center font-bold text-secondary-900">
                                    <?= (int)($agent['total_orders'] ?? 0) ?> টি
                                </td>

                                <!-- Sales Amount -->
                                <td class="py-3.5 px-4 text-right font-black text-secondary-900 text-sm">
                                    ৳<?= number_format((float)($agent['total_sales'] ?? 0), 2) ?>
                                </td>

                                <!-- Status Toggle -->
                                <td class="py-3.5 px-4 text-center">
                                    <form action="<?= $base ?>/admin/agents/toggle-status" method="POST" class="inline">
                                        <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                                        <input type="hidden" name="id" value="<?= $agent['id'] ?>">
                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold transition-transform active:scale-95 cursor-pointer <?= ($agent['status'] ?? 'active') === 'active' ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-red-100 text-red-800 hover:bg-red-200' ?>">
                                            <span class="w-1.5 h-1.5 rounded-full <?= ($agent['status'] ?? 'active') === 'active' ? 'bg-emerald-600' : 'bg-red-600' ?>"></span>
                                            <span><?= ($agent['status'] ?? 'active') === 'active' ? 'সক্রিয়' : 'নিষ্ক্রিয়' ?></span>
                                        </button>
                                    </form>
                                </td>

                                <!-- Actions -->
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Report Link -->
                                        <a href="<?= $base ?>/admin/agents/report?id=<?= $agent['id'] ?>" class="p-2 rounded-xl text-blue-600 hover:bg-blue-50 transition-colors" title="সেলস রিপোর্ট ও অর্ডার">
                                            <ion-icon name="stats-chart-outline" class="text-base"></ion-icon>
                                        </a>

                                        <!-- Assign Area Button -->
                                        <button type="button" onclick='openAssignAreaModal(<?= htmlspecialchars(json_encode($agent), ENT_QUOTES, 'UTF-8') ?>)' class="p-2 rounded-xl text-emerald-600 hover:bg-emerald-50 transition-colors cursor-pointer" title="এরিয়া নির্ধারণ">
                                            <ion-icon name="map-outline" class="text-base"></ion-icon>
                                        </button>

                                        <!-- Edit Agent Button -->
                                        <button type="button" onclick='openEditModal(<?= htmlspecialchars(json_encode($agent), ENT_QUOTES, 'UTF-8') ?>)' class="p-2 rounded-xl text-primary-600 hover:bg-primary-50 transition-colors cursor-pointer" title="এডিট এজেন্ট">
                                            <ion-icon name="create-outline" class="text-base"></ion-icon>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ─────────────────────────────────────────────── -->
    <!-- MODAL 1: Add New Agent (With Customer Selector) -->
    <!-- ─────────────────────────────────────────────── -->
    <div id="addAgentModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-secondary-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-secondary-100 space-y-5 animate-in fade-in zoom-in-95 duration-200 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-secondary-100 pb-4">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center text-xl">
                        <ion-icon name="person-add"></ion-icon>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-secondary-900">নতুন ফিল্ড এজেন্ট যোগ করুন</h3>
                        <p class="text-xs text-secondary-500">বিদ্যমান কাস্টমার থেকে সিলেক্ট করুন অথবা নতুন তথ্য দিন</p>
                    </div>
                </div>
                <button type="button" onclick="closeAddModal()" class="text-secondary-400 hover:text-secondary-600 text-2xl cursor-pointer">
                    <ion-icon name="close-circle-outline"></ion-icon>
                </button>
            </div>

            <!-- Customer Quick Selector Box -->
            <div class="bg-gradient-to-r from-emerald-50/70 to-primary-50/50 border border-primary-200/80 rounded-2xl p-3.5 space-y-2.5">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-black text-primary-900 flex items-center gap-1.5">
                        <ion-icon name="search" class="text-base text-primary-600"></ion-icon>
                        <span>যেকোনো কাস্টমারকে এজেন্ট বানান (সার্চ করুন)</span>
                    </label>
                    <button type="button" id="clearCustomerBtn" onclick="clearSelectedCustomer()" class="text-[11px] text-secondary-500 hover:text-red-600 font-bold hidden cursor-pointer">
                        ✕ ক্লিয়ার
                    </button>
                </div>
                
                <div class="relative">
                    <input type="text" id="customerSearchInput" autocomplete="off"
                           placeholder="কাস্টমারের নাম বা ফোন নম্বর লিখুন..."
                           class="w-full pl-9 pr-8 py-2.5 bg-white rounded-xl border border-secondary-200 text-xs focus:ring-2 focus:ring-primary-500 focus:outline-none">
                    <ion-icon name="search-outline" class="absolute left-3 top-3 text-secondary-400 text-sm pointer-events-none"></ion-icon>
                    <div id="customerSearchSpinner" class="hidden absolute right-3 top-2.5">
                        <svg class="animate-spin h-4 w-4 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                    </div>
                    
                    <!-- Live Suggestions Dropdown -->
                    <div id="customerSearchResults" class="absolute left-0 right-0 top-full mt-1 bg-white rounded-2xl shadow-2xl border border-secondary-200 z-50 max-h-56 overflow-y-auto hidden divide-y divide-secondary-100">
                    </div>
                </div>

                <!-- Selected Customer Feedback Badge -->
                <div id="selectedCustomerBanner" class="hidden items-center justify-between bg-emerald-100 border border-emerald-300 text-emerald-900 text-xs px-3 py-2 rounded-xl font-bold">
                    <div class="flex items-center gap-2">
                        <ion-icon name="checkmark-circle" class="text-lg text-emerald-600 shrink-0"></ion-icon>
                        <div>
                            <span id="selectedCustomerName"></span> (<span id="selectedCustomerPhone" class="font-mono"></span>)
                            <div class="text-[10px] text-emerald-700 font-normal mt-0.5" id="selectedCustomerArea"></div>
                        </div>
                    </div>
                    <span class="text-[10px] bg-emerald-700 text-white px-2 py-0.5 rounded-full font-extrabold uppercase shrink-0">সিলেক্টেড</span>
                </div>
            </div>

            <form action="<?= $base ?>/admin/agents/store" method="POST" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                <input type="hidden" name="customer_id" id="addAgentCustomerId" value="">

                <div>
                    <label class="block text-xs font-bold text-secondary-700 uppercase tracking-wider mb-1">এজেন্টের পূর্ণ নাম *</label>
                    <input type="text" name="name" id="addAgentName" required placeholder="যেমন: মো: রফিকুল ইসলাম" class="w-full px-4 py-2.5 rounded-xl border border-secondary-200 focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-secondary-700 uppercase tracking-wider mb-1">মোবাইল নম্বর *</label>
                        <input type="text" name="phone" id="addAgentPhone" required placeholder="017xxxxxxxx" class="w-full px-4 py-2.5 rounded-xl border border-secondary-200 focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-secondary-700 uppercase tracking-wider mb-1">লগইন ইউজারনেম *</label>
                        <input type="text" name="username" id="addAgentUsername" required placeholder="যেমন: rofiq_agent" class="w-full px-4 py-2.5 rounded-xl border border-secondary-200 focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-secondary-700 uppercase tracking-wider mb-1">লগইন পাসওয়ার্ড *</label>
                        <input type="password" name="password" id="addAgentPassword" required placeholder="ন্যূনতম ৪ অক্ষর (যেমন: 123456)" class="w-full px-4 py-2.5 rounded-xl border border-secondary-200 focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-secondary-700 uppercase tracking-wider mb-1">অ্যাকাউন্ট স্ট্যাটাস</label>
                        <select name="status" class="w-full px-4 py-2.5 rounded-xl border border-secondary-200 focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm bg-white">
                            <option value="active">সক্রিয় (Active)</option>
                            <option value="inactive">নিষ্ক্রিয় (Inactive)</option>
                        </select>
                    </div>
                </div>

                <!-- Assigned Areas Selection -->
                <div>
                    <label class="block text-xs font-bold text-secondary-700 uppercase tracking-wider mb-1">
                        নির্ধারিত এরিয়া / ইউনিয়ন নির্বাচন করুন
                    </label>
                    <p class="text-[11px] text-secondary-500 mb-2">এজেন্ট শুধুমাত্র এই নির্বাচিত এরিয়াগুলোর কাস্টমারদের অর্ডার নিতে ও নতুন কাস্টমার যোগ করতে পারবে।</p>
                    <div class="max-h-40 overflow-y-auto p-3 border border-secondary-200 rounded-xl space-y-2 bg-secondary-50/50" id="addAreaCheckboxesList">
                        <?php if (empty($areas)): ?>
                            <p class="text-xs text-secondary-400">কোনো এরিয়া পাওয়া যায়নি। আগে এরিয়া তৈরি করুন।</p>
                        <?php else: ?>
                            <?php foreach ($areas as $area): ?>
                                <label class="flex items-center gap-2.5 text-xs text-secondary-800 cursor-pointer hover:text-primary-600">
                                    <input type="checkbox" name="area_ids[]" value="<?= $area['id'] ?>" class="w-4 h-4 rounded text-primary-600 focus:ring-primary-500 border-secondary-300 add-area-cb" id="add_area_<?= $area['id'] ?>">
                                    <span class="font-semibold"><?= htmlspecialchars($area['name']) ?></span>
                                </label>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="pt-3 border-t border-secondary-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeAddModal()" class="px-5 py-2.5 bg-secondary-100 text-secondary-700 hover:bg-secondary-200 rounded-xl text-sm font-bold transition-colors cursor-pointer">
                        বাতিল
                    </button>
                    <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-sm font-bold shadow-md transition-colors flex items-center gap-1.5 cursor-pointer">
                        <ion-icon name="checkmark-circle"></ion-icon>
                        <span>এজেন্ট তৈরি করুন</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ─────────────────────────────────────────────── -->
    <!-- MODAL 2: Quick Assign Areas                     -->
    <!-- ─────────────────────────────────────────────── -->
    <div id="assignAreaModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-secondary-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-secondary-100 space-y-5 animate-in fade-in zoom-in-95 duration-200">
            <div class="flex items-center justify-between border-b border-secondary-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl">
                        <ion-icon name="map"></ion-icon>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-secondary-900">এরিয়া নির্ধারণ করুন</h3>
                        <p class="text-xs text-secondary-500" id="assignAreaAgentName"></p>
                    </div>
                </div>
                <button type="button" onclick="closeAssignAreaModal()" class="text-secondary-400 hover:text-secondary-600 text-2xl cursor-pointer">
                    <ion-icon name="close-circle-outline"></ion-icon>
                </button>
            </div>

            <form action="<?= $base ?>/admin/agents/assign-areas" method="POST" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                <input type="hidden" name="agent_id" id="assignAreaAgentId" value="">

                <div>
                    <label class="block text-xs font-bold text-secondary-700 uppercase tracking-wider mb-1.5">
                        অনুমোদিত ইউনিয়ন / এরিয়া সমুহ
                    </label>
                    <p class="text-[11px] text-secondary-500 mb-2">যে যে এরিয়ার কাস্টমারদের অর্ডার এই এজেন্ট নিতে পারবে তা টিক চিহ্ন দিন:</p>

                    <div class="max-h-56 overflow-y-auto p-3 border border-secondary-200 rounded-2xl space-y-2.5 bg-secondary-50/50">
                        <?php foreach ($areas as $area): ?>
                            <label class="flex items-center gap-2.5 text-xs text-secondary-800 cursor-pointer hover:text-primary-600">
                                <input type="checkbox" name="area_ids[]" value="<?= $area['id'] ?>" class="w-4 h-4 rounded text-primary-600 focus:ring-primary-500 border-secondary-300 assign-area-cb" id="assign_area_<?= $area['id'] ?>">
                                <span class="font-semibold"><?= htmlspecialchars($area['name']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="pt-2 border-t border-secondary-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeAssignAreaModal()" class="px-4 py-2 bg-secondary-100 text-secondary-700 hover:bg-secondary-200 rounded-xl text-xs font-bold transition-colors cursor-pointer">
                        বাতিল
                    </button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md transition-colors flex items-center gap-1.5 cursor-pointer">
                        <ion-icon name="save-outline"></ion-icon>
                        <span>এরিয়া সংরক্ষণ করুন</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ─────────────────────────────────────────────── -->
    <!-- MODAL 3: Edit Agent Details                     -->
    <!-- ─────────────────────────────────────────────── -->
    <div id="editAgentModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-secondary-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-secondary-100 space-y-5 animate-in fade-in zoom-in-95 duration-200">
            <div class="flex items-center justify-between border-b border-secondary-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center text-xl">
                        <ion-icon name="create"></ion-icon>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-secondary-900">এজেন্ট তথ্য সংশোধন</h3>
                        <p class="text-xs text-secondary-500 font-mono" id="editAgentUsernameText"></p>
                    </div>
                </div>
                <button type="button" onclick="closeEditModal()" class="text-secondary-400 hover:text-secondary-600 text-2xl cursor-pointer">
                    <ion-icon name="close-circle-outline"></ion-icon>
                </button>
            </div>

            <form action="<?= $base ?>/admin/agents/update" method="POST" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                <input type="hidden" name="id" id="editAgentId" value="">

                <div>
                    <label class="block text-xs font-bold text-secondary-700 uppercase tracking-wider mb-1">এজেন্টের নাম *</label>
                    <input type="text" name="name" id="editAgentName" required class="w-full px-4 py-2.5 rounded-xl border border-secondary-200 focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-secondary-700 uppercase tracking-wider mb-1">মোবাইল নম্বর</label>
                        <input type="text" name="phone" id="editAgentPhone" class="w-full px-4 py-2.5 rounded-xl border border-secondary-200 focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-secondary-700 uppercase tracking-wider mb-1">স্ট্যাটাস</label>
                        <select name="status" id="editAgentStatus" class="w-full px-4 py-2.5 rounded-xl border border-secondary-200 focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm bg-white">
                            <option value="active">সক্রিয় (Active)</option>
                            <option value="inactive">নিষ্ক্রিয় (Inactive)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-secondary-700 uppercase tracking-wider mb-1">নতুন পাসওয়ার্ড (পরিবর্তন করতে চাইলে লিখুন)</label>
                    <input type="password" name="password" id="editAgentPassword" placeholder="অপরিবর্তিত রাখতে খালি রাখুন" class="w-full px-4 py-2.5 rounded-xl border border-secondary-200 focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm">
                </div>

                <!-- Assigned Areas Selection in Edit -->
                <div>
                    <label class="block text-xs font-bold text-secondary-700 uppercase tracking-wider mb-1">
                        নির্ধারিত এরিয়া (Unions)
                    </label>
                    <div class="max-h-36 overflow-y-auto p-3 border border-secondary-200 rounded-xl space-y-2 bg-secondary-50/50">
                        <?php foreach ($areas as $area): ?>
                            <label class="flex items-center gap-2.5 text-xs text-secondary-800 cursor-pointer hover:text-primary-600">
                                <input type="checkbox" name="area_ids[]" value="<?= $area['id'] ?>" class="w-4 h-4 rounded text-primary-600 focus:ring-primary-500 border-secondary-300 edit-area-cb" id="edit_area_<?= $area['id'] ?>">
                                <span class="font-semibold"><?= htmlspecialchars($area['name']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="pt-3 border-t border-secondary-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeEditModal()" class="px-5 py-2.5 bg-secondary-100 text-secondary-700 hover:bg-secondary-200 rounded-xl text-sm font-bold transition-colors cursor-pointer">
                        বাতিল
                    </button>
                    <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-sm font-bold shadow-md transition-colors flex items-center gap-1.5 cursor-pointer">
                        <ion-icon name="checkmark-circle"></ion-icon>
                        <span>আপডেট সংরক্ষণ করুন</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<!-- ─────────────────────────────────────────────── -->
<!-- MODAL MANAGEMENT & CUSTOMER SEARCH SCRIPT       -->
<!-- ─────────────────────────────────────────────── -->
<script>
const APP_BASE = '<?= $base ?>';
const PRELOADED_CUSTOMER = <?= json_encode($selectedCustomer ?? null) ?>;

// ── Modal Handlers ──────────────────────────────
function openAddModal() {
    document.getElementById('addAgentModal').classList.remove('hidden');
}

function closeAddModal() {
    document.getElementById('addAgentModal').classList.add('hidden');
}

function openEditModal(agent) {
    if (!agent) return;
    document.getElementById('editAgentId').value = agent.id;
    document.getElementById('editAgentName').value = agent.name || '';
    document.getElementById('editAgentPhone').value = agent.phone || '';
    document.getElementById('editAgentStatus').value = agent.status || 'active';
    document.getElementById('editAgentPassword').value = '';
    document.getElementById('editAgentUsernameText').textContent = '@' + (agent.username || '');

    const areaIds = agent.area_ids ? agent.area_ids.toString().split(',').map(s => s.trim()) : [];
    document.querySelectorAll('.edit-area-cb').forEach(cb => {
        cb.checked = areaIds.includes(cb.value.toString());
    });

    document.getElementById('editAgentModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editAgentModal').classList.add('hidden');
}

function openAssignAreaModal(agent) {
    if (!agent) return;
    document.getElementById('assignAreaAgentId').value = agent.id;
    document.getElementById('assignAreaAgentName').textContent = agent.name || '';

    const areaIds = agent.area_ids ? agent.area_ids.toString().split(',').map(s => s.trim()) : [];
    document.querySelectorAll('.assign-area-cb').forEach(cb => {
        cb.checked = areaIds.includes(cb.value.toString());
    });

    document.getElementById('assignAreaModal').classList.remove('hidden');
}

function closeAssignAreaModal() {
    document.getElementById('assignAreaModal').classList.add('hidden');
}

// Close on Backdrop Click
['addAgentModal', 'editAgentModal', 'assignAreaModal'].forEach(id => {
    const el = document.getElementById(id);
    if (el) {
        el.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });
    }
});

// Close on Escape Key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAddModal();
        closeEditModal();
        closeAssignAreaModal();
    }
});

// ── Customer Search & Make Agent Logic ───────────
let searchTimer = null;
const searchInput = document.getElementById('customerSearchInput');
const searchResults = document.getElementById('customerSearchResults');
const searchSpinner = document.getElementById('customerSearchSpinner');

if (searchInput) {
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        clearTimeout(searchTimer);

        if (query.length < 1) {
            searchResults.innerHTML = '';
            searchResults.classList.add('hidden');
            return;
        }

        searchSpinner.classList.remove('hidden');

        searchTimer = setTimeout(() => {
            fetch(`${APP_BASE}/admin/customers/search-api?q=${encodeURIComponent(query)}`)
                .then(r => r.json())
                .then(customers => {
                    searchSpinner.classList.add('hidden');
                    if (!customers || customers.length === 0) {
                        searchResults.innerHTML = '<div class="p-3 text-xs text-secondary-400 text-center">কোনো কাস্টমার পাওয়া যায়নি</div>';
                        searchResults.classList.remove('hidden');
                        return;
                    }

                    let html = '';
                    customers.forEach(c => {
                        const areaText = c.area_name ? ` · ${c.area_name}` : '';
                        const jsonStr = encodeURIComponent(JSON.stringify(c));
                        html += `
                            <div onclick="selectCustomerFromEncoded('${jsonStr}')" class="p-2.5 hover:bg-primary-50 cursor-pointer flex items-center justify-between text-xs transition-colors">
                                <div>
                                    <div class="font-bold text-secondary-900">${c.name || 'Unknown'}</div>
                                    <div class="text-[11px] text-secondary-500 font-mono">${c.phone || ''}${areaText}</div>
                                </div>
                                <span class="text-[10px] bg-primary-100 text-primary-700 px-2 py-0.5 rounded-md font-bold">এজেন্ট বানান ➔</span>
                            </div>
                        `;
                    });
                    searchResults.innerHTML = html;
                    searchResults.classList.remove('hidden');
                })
                .catch(err => {
                    searchSpinner.classList.add('hidden');
                    console.error('Customer search error:', err);
                });
        }, 250);
    });

    // Close dropdown on outside click
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.add('hidden');
        }
    });
}

function selectCustomerFromEncoded(encodedStr) {
    try {
        const c = JSON.parse(decodeURIComponent(encodedStr));
        selectCustomerForAgent(c);
    } catch(e) {
        console.error(e);
    }
}

function selectCustomerForAgent(c) {
    if (!c) return;

    // Set Customer ID
    document.getElementById('addAgentCustomerId').value = c.id || '';

    // Set Name & Phone
    document.getElementById('addAgentName').value = c.name || '';
    document.getElementById('addAgentPhone').value = c.phone || '';

    // Generate smart username from phone
    const cleanPhone = (c.phone || '').replace(/[^0-9]/g, '');
    const defaultUsername = cleanPhone ? 'agent_' + cleanPhone.slice(-6) : 'agent_' + (c.id || '');
    const usernameInput = document.getElementById('addAgentUsername');
    if (!usernameInput.value || usernameInput.value.startsWith('agent_')) {
        usernameInput.value = defaultUsername;
    }

    // Set default password if empty
    const passwordInput = document.getElementById('addAgentPassword');
    if (!passwordInput.value) {
        passwordInput.value = '123456';
    }

    // Auto-select area if customer has an area
    if (c.area_id) {
        const cb = document.getElementById('add_area_' + c.area_id);
        if (cb) cb.checked = true;
    }

    // Update feedback banner
    document.getElementById('selectedCustomerName').textContent = c.name || 'Customer';
    document.getElementById('selectedCustomerPhone').textContent = c.phone || '';
    document.getElementById('selectedCustomerArea').textContent = c.area_name ? 'নিবন্ধিত এরিয়া: ' + c.area_name : '';
    
    document.getElementById('selectedCustomerBanner').classList.remove('hidden');
    document.getElementById('selectedCustomerBanner').classList.add('flex');
    document.getElementById('clearCustomerBtn').classList.remove('hidden');
    
    if (searchResults) searchResults.classList.add('hidden');
    if (searchInput) searchInput.value = '';
}

function clearSelectedCustomer() {
    document.getElementById('addAgentCustomerId').value = '';
    document.getElementById('selectedCustomerBanner').classList.add('hidden');
    document.getElementById('selectedCustomerBanner').classList.remove('flex');
    document.getElementById('clearCustomerBtn').classList.add('hidden');
    if (searchInput) searchInput.value = '';
}

// ── Preloaded Customer from URL ─────────────────
document.addEventListener('DOMContentLoaded', function() {
    if (PRELOADED_CUSTOMER) {
        openAddModal();
        selectCustomerForAgent(PRELOADED_CUSTOMER);
    }
});
</script>
