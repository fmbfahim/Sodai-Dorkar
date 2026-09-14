<?php 
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
$currentRole = $filters['role'] ?? '';
$currentStatus = $filters['status'] ?? '';
$search = $filters['search'] ?? '';
?>

<div class="space-y-6">
    <!-- Header with Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-secondary-200 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-secondary-900 flex items-center gap-2">
                <ion-icon name="people" class="text-primary-600 text-2xl"></ion-icon>
                ব্যবহারকারী ও রোল ব্যবস্থাপনা (User & Roles)
            </h2>
            <p class="text-secondary-500 text-sm mt-1">সিস্টেমের এডমিন, ম্যানেজার, একাউন্ট্যান্ট ও কর্মচারীদের লগইন একাউন্ট ও পারমিশন কন্ট্রোল</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="openCreateUserModal()" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-xl flex items-center gap-2 transition-all shadow-sm">
                <ion-icon name="person-add" class="text-lg"></ion-icon>
                <span>নতুন ব্যবহারকারী যোগ করুন</span>
            </button>
        </div>
    </div>

    <!-- Notifications -->
    <?php if (!empty($success)): ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-2 text-sm">
            <ion-icon name="checkmark-circle" class="text-xl text-emerald-600"></ion-icon>
            <span><?= htmlspecialchars($success) ?></span>
        </div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center gap-2 text-sm">
            <ion-icon name="alert-circle" class="text-xl text-red-600"></ion-icon>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-secondary-200 shadow-sm">
        <form method="GET" action="<?= $base ?>/admin/users" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-semibold text-secondary-500 uppercase tracking-wider mb-1">রোল ফিল্টার</label>
                <select name="role" onchange="this.form.submit()" class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3 py-2 text-sm text-secondary-800 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">সকল রোল (All Roles)</option>
                    <?php foreach ($roles as $rKey => $rLabel): ?>
                        <option value="<?= $rKey ?>" <?= $currentRole === $rKey ? 'selected' : '' ?>><?= $rLabel ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-secondary-500 uppercase tracking-wider mb-1">স্ট্যাটাস</label>
                <select name="status" onchange="this.form.submit()" class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3 py-2 text-sm text-secondary-800 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">সকল স্ট্যাটাস</option>
                    <option value="active" <?= $currentStatus === 'active' ? 'selected' : '' ?>>সক্রিয় (Active)</option>
                    <option value="inactive" <?= $currentStatus === 'inactive' ? 'selected' : '' ?>>নিষ্ক্রিয় (Inactive)</option>
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-secondary-500 uppercase tracking-wider mb-1">খুঁজুন (Search)</label>
                <div class="relative">
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="নাম, ইউজারনেম, ফোন বা ইমেইল দিয়ে খুঁজুন..." class="w-full bg-secondary-50 border border-secondary-300 rounded-xl pl-9 pr-20 py-2 text-sm text-secondary-800 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <ion-icon name="search" class="absolute left-3 top-2.5 text-secondary-400 text-base"></ion-icon>
                    <div class="absolute right-1.5 top-1.5 flex gap-1">
                        <?php if ($currentRole || $currentStatus || $search): ?>
                            <a href="<?= $base ?>/admin/users" class="px-2 py-1 text-xs text-secondary-500 hover:text-secondary-800 bg-secondary-200 rounded-lg">রিসেট</a>
                        <?php endif; ?>
                        <button type="submit" class="px-3 py-1 bg-primary-600 hover:bg-primary-700 text-white text-xs font-medium rounded-lg">খুঁজুন</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl border border-secondary-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-secondary-50 text-secondary-600 border-b border-secondary-200 uppercase text-xs">
                    <tr>
                        <th class="py-3.5 px-4 font-semibold">ব্যবহারকারী (User)</th>
                        <th class="py-3.5 px-4 font-semibold">যোগাযোগ (Contact)</th>
                        <th class="py-3.5 px-4 font-semibold">সিস্টেম রোল (Role)</th>
                        <th class="py-3.5 px-4 font-semibold">লিংকড কর্মচারী</th>
                        <th class="py-3.5 px-4 font-semibold text-center">স্ট্যাটাস</th>
                        <th class="py-3.5 px-4 font-semibold text-right">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-secondary-100">
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="py-12 text-center text-secondary-500">
                                <ion-icon name="person-outline" class="text-4xl text-secondary-300 mb-2"></ion-icon>
                                <p>কোনো ব্যবহারকারী পাওয়া যায়নি।</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                            <tr class="hover:bg-secondary-50/60 transition-colors">
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold text-sm uppercase">
                                            <?= mb_substr($u['name'] ?? $u['username'], 0, 1) ?>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-secondary-900"><?= htmlspecialchars($u['name']) ?></p>
                                            <p class="text-xs text-secondary-500">@<?= htmlspecialchars($u['username']) ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-xs space-y-0.5">
                                    <p class="text-secondary-800 flex items-center gap-1">
                                        <ion-icon name="call-outline" class="text-secondary-400"></ion-icon>
                                        <?= htmlspecialchars($u['phone'] ?: 'N/A') ?>
                                    </p>
                                    <p class="text-secondary-500 flex items-center gap-1">
                                        <ion-icon name="mail-outline" class="text-secondary-400"></ion-icon>
                                        <?= htmlspecialchars($u['email'] ?: 'N/A') ?>
                                    </p>
                                </td>
                                <td class="py-3.5 px-4">
                                    <?php
                                    $roleColors = [
                                        'admin' => 'bg-purple-100 text-purple-700 border-purple-200',
                                        'manager' => 'bg-blue-100 text-blue-700 border-blue-200',
                                        'accountant' => 'bg-amber-100 text-amber-700 border-amber-200',
                                        'staff' => 'bg-secondary-100 text-secondary-700 border-secondary-200',
                                        'agent' => 'bg-teal-100 text-teal-700 border-teal-200',
                                        'delivery_man' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                    ];
                                    $rBadge = $roleColors[$u['role']] ?? 'bg-secondary-100 text-secondary-700 border-secondary-200';
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border <?= $rBadge ?>">
                                        <?= $roles[$u['role']] ?? ucfirst($u['role']) ?>
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-xs">
                                    <?php if (!empty($u['emp_code'])): ?>
                                        <a href="<?= $base ?>/admin/hr/employees/show?id=<?= $u['employee_id'] ?>" class="text-primary-600 hover:text-primary-700 font-medium inline-flex items-center gap-1">
                                            <ion-icon name="id-card-outline"></ion-icon>
                                            <?= htmlspecialchars($u['emp_code']) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-secondary-400 italic">লিংক করা নেই</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <form method="POST" action="<?= $base ?>/admin/users/toggle-status" class="inline">
                                        <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                        <?php if (($u['status'] ?? 'active') === 'active'): ?>
                                            <button type="submit" title="নিষ্ক্রিয় করতে ক্লিক করুন" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 hover:bg-emerald-200 transition-colors">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> সক্রিয়
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" title="সক্রিয় করতে ক্লিক করুন" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 hover:bg-red-200 transition-colors">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> নিষ্ক্রিয়
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="<?= $base ?>/admin/users/edit?id=<?= $u['id'] ?>" class="p-1.5 text-secondary-600 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors" title="সম্পাদনা">
                                            <ion-icon name="create-outline" class="text-lg"></ion-icon>
                                        </a>
                                        <?php if ($u['id'] != ($_SESSION['user_id'] ?? 0)): ?>
                                            <form method="POST" action="<?= $base ?>/admin/users/delete" onsubmit="return confirm('আপনি কি নিশ্চিত এই ব্যবহারকারী মুছে ফেলতে চান?')" class="inline">
                                                <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                                                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                                <button type="submit" class="p-1.5 text-secondary-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="মুছে ফেলুন">
                                                    <ion-icon name="trash-outline" class="text-lg"></ion-icon>
                                                </button>
                                            </form>
                                        <?php endif; ?>
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

<!-- Modal: Add New User -->
<div id="createUserModal" class="fixed inset-0 bg-secondary-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-secondary-200">
        <div class="flex items-center justify-between pb-4 border-b border-secondary-100">
            <h3 class="text-lg font-bold text-secondary-900 flex items-center gap-2">
                <ion-icon name="person-add" class="text-primary-600"></ion-icon>
                নতুন ব্যবহারকারী তৈরি
            </h3>
            <button onclick="closeCreateUserModal()" class="text-secondary-400 hover:text-secondary-700">
                <ion-icon name="close-circle-outline" class="text-2xl"></ion-icon>
            </button>
        </div>

        <form method="POST" action="<?= $base ?>/admin/users/store" class="mt-4 space-y-4">
            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">

            <!-- Link with existing employee (optional) -->
            <div>
                <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">বিদ্যমান কর্মচারীর সাথে লিংক করুন (ঐচ্ছিক)</label>
                <select name="employee_id" id="modalEmployeeSelect" onchange="autoFillEmployee(this)" class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
                    <option value="">-- কোনো কর্মচারী লিংক নয় (স্বতন্ত্র একাউন্ট) --</option>
                    <?php foreach ($employees as $emp): ?>
                        <option value="<?= $emp['id'] ?>" data-name="<?= htmlspecialchars($emp['name']) ?>" data-phone="<?= htmlspecialchars($emp['phone']) ?>" data-email="<?= htmlspecialchars($emp['email'] ?? '') ?>">
                            <?= htmlspecialchars($emp['emp_code'] . ' - ' . $emp['name'] . ' (' . ($emp['department_name'] ?? 'N/A') . ')') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="text-[11px] text-secondary-500 mt-1">কর্মচারী নির্বাচন করলে নাম, ফোন ও ইমেইল স্বয়ংক্রিয় পূরণ হবে</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-secondary-700 mb-1">পূর্ণ নাম <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="modalName" required class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500" placeholder="ব্যবহারকারীর নাম">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-secondary-700 mb-1">ইউজারনেম (Login ID) <span class="text-red-500">*</span></label>
                    <input type="text" name="username" id="modalUsername" required class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500" placeholder="e.g. fahim_admin">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-secondary-700 mb-1">মোবাইল নম্বর</label>
                    <input type="text" name="phone" id="modalPhone" class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500" placeholder="017xxxxxxxx">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-secondary-700 mb-1">ইমেইল</label>
                    <input type="email" name="email" id="modalEmail" class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500" placeholder="email@domain.com">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-secondary-700 mb-1">লগইন পাসওয়ার্ড <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required minlength="6" class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500" placeholder="কমপক্ষে ৬ অক্ষর">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-secondary-700 mb-1">সিস্টেম রোল (Role) <span class="text-red-500">*</span></label>
                    <select name="role" required class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
                        <?php foreach ($roles as $rKey => $rLabel): ?>
                            <option value="<?= $rKey ?>" <?= $rKey === 'staff' ? 'selected' : '' ?>><?= $rLabel ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-secondary-700 mb-1">স্ট্যাটাস</label>
                <div class="flex items-center gap-4 mt-1">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="status" value="active" checked class="text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-secondary-800">সক্রিয় (Active)</span>
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="status" value="inactive" class="text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-secondary-800">নিষ্ক্রিয় (Inactive)</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-secondary-100">
                <button type="button" onclick="closeCreateUserModal()" class="px-4 py-2 border border-secondary-300 text-secondary-700 rounded-xl text-sm font-medium hover:bg-secondary-50">বাতিল</button>
                <button type="submit" class="px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-sm font-medium transition-colors shadow-sm">সংরক্ষণ করুন</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCreateUserModal() {
    document.getElementById('createUserModal').classList.remove('hidden');
}

function closeCreateUserModal() {
    document.getElementById('createUserModal').classList.add('hidden');
}

function autoFillEmployee(select) {
    const opt = select.options[select.selectedIndex];
    if (select.value) {
        document.getElementById('modalName').value = opt.getAttribute('data-name') || '';
        document.getElementById('modalPhone').value = opt.getAttribute('data-phone') || '';
        document.getElementById('modalEmail').value = opt.getAttribute('data-email') || '';
        
        // Suggest username from name
        const nameParts = (opt.getAttribute('data-name') || '').toLowerCase().replace(/[^a-z0-9]/g, '');
        if (nameParts && !document.getElementById('modalUsername').value) {
            document.getElementById('modalUsername').value = nameParts.substring(0, 10);
        }
    }
}
</script>
