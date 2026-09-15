<?php 
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
$currentDept = $filters['department_id'] ?? '';
$currentStatus = $filters['status'] ?? '';
$search = $filters['search'] ?? '';
?>

<div class="space-y-6">
    <!-- Header & Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-secondary-200 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-secondary-900 flex items-center gap-2">
                <ion-icon name="people" class="text-primary-600 text-2xl"></ion-icon>
                কর্মচারী ডিরেক্টরি (Employee Management)
            </h2>
            <p class="text-secondary-500 text-sm mt-1">কর্মচারীদের প্রোফাইল, বেতন কাঠামো, পদবী ও দায়িত্ব পরিচালনা করুন</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="<?= $base ?>/admin/hr/attendance" class="bg-secondary-100 hover:bg-secondary-200 text-secondary-800 font-medium py-2 px-3.5 rounded-xl text-sm flex items-center gap-1.5 transition-colors">
                <ion-icon name="calendar-outline" class="text-lg text-primary-600"></ion-icon>
                <span>দৈনিক হাজিরা</span>
            </a>
            <a href="<?= $base ?>/admin/payroll" class="bg-secondary-100 hover:bg-secondary-200 text-secondary-800 font-medium py-2 px-3.5 rounded-xl text-sm flex items-center gap-1.5 transition-colors">
                <ion-icon name="cash-outline" class="text-lg text-emerald-600"></ion-icon>
                <span>মাসিক পেরোল</span>
            </a>
            <a href="<?= $base ?>/admin/hr/employees/create" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-xl text-sm flex items-center gap-1.5 transition-colors shadow-sm">
                <ion-icon name="person-add" class="text-lg"></ion-icon>
                <span>নতুন কর্মচারী যোগ করুন</span>
            </a>
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

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-secondary-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl">
                <ion-icon name="people"></ion-icon>
            </div>
            <div>
                <p class="text-xs font-semibold text-secondary-400 uppercase tracking-wider">মোট কর্মচারী</p>
                <h3 class="text-2xl font-bold text-secondary-900"><?= number_format($stats['total'] ?? 0) ?> জন</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-secondary-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl">
                <ion-icon name="checkmark-circle"></ion-icon>
            </div>
            <div>
                <p class="text-xs font-semibold text-secondary-400 uppercase tracking-wider">সক্রিয় কর্মী (Active)</p>
                <h3 class="text-2xl font-bold text-emerald-600"><?= number_format($stats['active'] ?? 0) ?> জন</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-secondary-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl">
                <ion-icon name="time"></ion-icon>
            </div>
            <div>
                <p class="text-xs font-semibold text-secondary-400 uppercase tracking-wider">ছুটিতে আছেন (On Leave)</p>
                <h3 class="text-2xl font-bold text-amber-600"><?= number_format($stats['on_leave'] ?? 0) ?> জন</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-secondary-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl">
                <ion-icon name="wallet"></ion-icon>
            </div>
            <div>
                <p class="text-xs font-semibold text-secondary-400 uppercase tracking-wider">মাসিক বেতন বাজেট</p>
                <h3 class="text-2xl font-bold text-purple-700">৳<?= number_format($stats['monthly_salary_estimate'] ?? 0, 2) ?></h3>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-secondary-200 shadow-sm">
        <form method="GET" action="<?= $base ?>/admin/hr/employees" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-semibold text-secondary-500 uppercase tracking-wider mb-1">ডিপার্টমেন্ট</label>
                <select name="department_id" onchange="this.form.submit()" class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3 py-2 text-sm text-secondary-800 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">সকল ডিপার্টমেন্ট (All)</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?= $dept['id'] ?>" <?= $currentDept == $dept['id'] ? 'selected' : '' ?>><?= htmlspecialchars($dept['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-secondary-500 uppercase tracking-wider mb-1">স্ট্যাটাস</label>
                <select name="status" onchange="this.form.submit()" class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3 py-2 text-sm text-secondary-800 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">সকল স্ট্যাটাস</option>
                    <option value="active" <?= $currentStatus === 'active' ? 'selected' : '' ?>>সক্রিয় (Active)</option>
                    <option value="on_leave" <?= $currentStatus === 'on_leave' ? 'selected' : '' ?>>ছুটিতে (On Leave)</option>
                    <option value="inactive" <?= $currentStatus === 'inactive' ? 'selected' : '' ?>>নিষ্ক্রিয় (Inactive)</option>
                    <option value="terminated" <?= $currentStatus === 'terminated' ? 'selected' : '' ?>>বহিষ্কৃত (Terminated)</option>
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-secondary-500 uppercase tracking-wider mb-1">খুঁজুন (Search)</label>
                <div class="relative">
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="নাম, EMP কোড, মোবাইল বা NID নম্বর দিয়ে খুঁজুন..." class="w-full bg-secondary-50 border border-secondary-300 rounded-xl pl-9 pr-20 py-2 text-sm text-secondary-800 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <ion-icon name="search" class="absolute left-3 top-2.5 text-secondary-400 text-base"></ion-icon>
                    <div class="absolute right-1.5 top-1.5 flex gap-1">
                        <?php if ($currentDept || $currentStatus || $search): ?>
                            <a href="<?= $base ?>/admin/hr/employees" class="px-2 py-1 text-xs text-secondary-500 hover:text-secondary-800 bg-secondary-200 rounded-lg">রিসেট</a>
                        <?php endif; ?>
                        <button type="submit" class="px-3 py-1 bg-primary-600 hover:bg-primary-700 text-white text-xs font-medium rounded-lg">ফিল্টার</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Employee Table -->
    <div class="bg-white rounded-2xl border border-secondary-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-secondary-50 text-secondary-600 border-b border-secondary-200 uppercase text-xs">
                    <tr>
                        <th class="py-3.5 px-4 font-semibold">কর্মচারী (Employee)</th>
                        <th class="py-3.5 px-4 font-semibold">ডিপার্টমেন্ট ও পদবী</th>
                        <th class="py-3.5 px-4 font-semibold">যোগাযোগ</th>
                        <th class="py-3.5 px-4 font-semibold">যোগদানের তারিখ</th>
                        <th class="py-3.5 px-4 font-semibold text-right">মূল বেতন</th>
                        <th class="py-3.5 px-4 font-semibold text-center">স্ট্যাটাস</th>
                        <th class="py-3.5 px-4 font-semibold text-right">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-secondary-100">
                    <?php if (empty($employees)): ?>
                        <tr>
                            <td colspan="7" class="py-12 text-center text-secondary-500">
                                <ion-icon name="people-outline" class="text-4xl text-secondary-300 mb-2"></ion-icon>
                                <p>কোনো কর্মচারী পাওয়া যায়নি।</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($employees as $emp): ?>
                            <tr class="hover:bg-secondary-50/60 transition-colors">
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <?php if (!empty($emp['photo_path'])): ?>
                                            <img src="<?= (strpos($emp['photo_path'], 'http') === 0) ? $emp['photo_path'] : ($base . '/' . ltrim($emp['photo_path'], '/')) ?>" alt="<?= htmlspecialchars($emp['name']) ?>" class="w-10 h-10 rounded-full object-cover border border-secondary-200 shadow-sm">
                                        <?php else: ?>
                                            <div class="w-10 h-10 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold text-sm uppercase">
                                                <?= mb_substr($emp['name'], 0, 1) ?>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <a href="<?= $base ?>/admin/hr/employees/show?id=<?= $emp['id'] ?>" class="font-bold text-secondary-900 hover:text-primary-600 transition-colors flex items-center gap-1.5">
                                                <?= htmlspecialchars($emp['name']) ?>
                                            </a>
                                            <div class="flex flex-wrap items-center gap-1.5 mt-0.5">
                                                <span class="inline-block px-1.5 py-0.5 bg-secondary-100 text-secondary-700 rounded text-[11px] font-mono">
                                                    <?= htmlspecialchars($emp['emp_code']) ?>
                                                </span>
                                                <?php if (!empty($emp['linked_username'])): ?>
                                                    <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 bg-primary-50 text-primary-700 border border-primary-100 rounded text-[11px] font-medium" title="সিস্টেম লগইন লিংক করা">
                                                        <ion-icon name="person-circle-outline" class="text-xs"></ion-icon>
                                                        @<?= htmlspecialchars($emp['linked_username']) ?>
                                                    </span>
                                                <?php else: ?>
                                                    <button type="button" onclick="openHrQuickUserModal(<?= $emp['id'] ?>, '<?= addslashes(htmlspecialchars($emp['name'])) ?>')" class="inline-flex items-center gap-0.5 px-1.5 py-0.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 hover:text-emerald-900 border border-emerald-200 rounded text-[10px] font-bold transition-colors shadow-2xs" title="এই কর্মচারীর জন্য সিস্টেম লগইন তৈরি করুন">
                                                        <ion-icon name="key-outline" class="text-[11px]"></ion-icon>
                                                        + লগইন তৈরি
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <p class="font-medium text-secondary-800"><?= htmlspecialchars($emp['designation_title'] ?? 'পদবী নির্ধারিত নেই') ?></p>
                                    <p class="text-xs text-secondary-500"><?= htmlspecialchars($emp['department_name'] ?? 'ডিপার্টমেন্ট নেই') ?></p>
                                </td>
                                <td class="py-3.5 px-4 text-xs space-y-0.5">
                                    <p class="text-secondary-800 flex items-center gap-1">
                                        <ion-icon name="call-outline" class="text-secondary-400"></ion-icon>
                                        <?= htmlspecialchars($emp['phone']) ?>
                                    </p>
                                    <?php if (!empty($emp['email'])): ?>
                                        <p class="text-secondary-500 flex items-center gap-1">
                                            <ion-icon name="mail-outline" class="text-secondary-400"></ion-icon>
                                            <?= htmlspecialchars($emp['email']) ?>
                                        </p>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3.5 px-4 text-xs text-secondary-600">
                                    <?= !empty($emp['joining_date']) ? date('d M, Y', strtotime($emp['joining_date'])) : 'N/A' ?>
                                    <p class="text-[11px] text-secondary-400 capitalize"><?= str_replace('_', ' ', $emp['employment_type'] ?? 'full_time') ?></p>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <p class="font-bold text-secondary-900">৳<?= number_format($emp['basic_salary'] ?? 0, 2) ?></p>
                                    <?php 
                                    $allowances = floatval($emp['house_rent'] ?? 0) + floatval($emp['medical_allowance'] ?? 0) + floatval($emp['other_allowance'] ?? 0);
                                    if ($allowances > 0): 
                                    ?>
                                        <p class="text-[11px] text-emerald-600">+ ভাতা: ৳<?= number_format($allowances, 2) ?></p>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <?php
                                    $statusBadges = [
                                        'active' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        'on_leave' => 'bg-amber-100 text-amber-800 border-amber-200',
                                        'inactive' => 'bg-secondary-100 text-secondary-700 border-secondary-200',
                                        'terminated' => 'bg-red-100 text-red-800 border-red-200'
                                    ];
                                    $statusLabels = [
                                        'active' => 'সক্রিয়',
                                        'on_leave' => 'ছুটিতে',
                                        'inactive' => 'নিষ্ক্রিয়',
                                        'terminated' => 'বহিষ্কৃত'
                                    ];
                                    $st = $emp['status'] ?? 'active';
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border <?= $statusBadges[$st] ?? '' ?>">
                                        <?= $statusLabels[$st] ?? ucfirst($st) ?>
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="inline-flex items-center gap-1">
                                        <a href="<?= $base ?>/admin/hr/employees/show?id=<?= $emp['id'] ?>" class="p-1.5 text-secondary-600 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors" title="প্রোফাইল ভিউ">
                                            <ion-icon name="eye-outline" class="text-lg"></ion-icon>
                                        </a>
                                        <a href="<?= $base ?>/admin/hr/employees/edit?id=<?= $emp['id'] ?>" class="p-1.5 text-secondary-600 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors" title="সম্পাদনা">
                                            <ion-icon name="create-outline" class="text-lg"></ion-icon>
                                        </a>
                                        <form method="POST" action="<?= $base ?>/admin/hr/employees/delete" onsubmit="return confirm('আপনি কি নিশ্চিত এই কর্মচারীর রেকর্ড মুছে ফেলতে চান?')" class="inline">
                                            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                                            <input type="hidden" name="id" value="<?= $emp['id'] ?>">
                                            <button type="submit" class="p-1.5 text-secondary-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="মুছে ফেলুন">
                                                <ion-icon name="trash-outline" class="text-lg"></ion-icon>
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

<!-- Modal: Quick Create User Account from Employee List -->
<div id="hrQuickUserModal" class="fixed inset-0 bg-secondary-900/50 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-secondary-200">
        <div class="flex items-center justify-between pb-3 border-b border-secondary-100">
            <h3 class="text-base font-bold text-secondary-900 flex items-center gap-2">
                <ion-icon name="key" class="text-emerald-600 text-xl"></ion-icon>
                <span>সিস্টেম লগইন একাউন্ট তৈরি (Add User)</span>
            </h3>
            <button onclick="closeHrQuickUserModal()" class="text-secondary-400 hover:text-secondary-700">
                <ion-icon name="close-circle-outline" class="text-2xl"></ion-icon>
            </button>
        </div>

        <form method="POST" action="<?= $base ?>/admin/hr/employees/create-user" class="mt-4 space-y-4">
            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
            <input type="hidden" name="employee_id" id="hrQuickEmpId" value="">

            <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl">
                <p class="text-xs text-emerald-800">
                    কর্মচারী: <strong id="hrQuickEmpName" class="text-emerald-900 font-bold"></strong>-এর জন্য সরাসরি একটি সিস্টেম লগইন একাউন্ট তৈরি ও স্বয়ংক্রিয় লিংক হবে।
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-secondary-700 mb-1">ইউজারনেম (Login ID) <span class="text-red-500">*</span></label>
                    <input type="text" name="username" id="hrQuickUsername" required class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3 py-2 text-xs text-secondary-800 focus:ring-2 focus:ring-emerald-500 font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-secondary-700 mb-1">লগইন পাসওয়ার্ড <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required minlength="6" placeholder="কমপক্ষে ৬ অক্ষর" class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3 py-2 text-xs text-secondary-800 focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-secondary-700 mb-1">সিস্টেম রোল (Role) <span class="text-red-500">*</span></label>
                <select name="role" id="hrQuickRoleSelect" onchange="hrQuickRoleChange(this.value)" required class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3 py-2 text-xs text-secondary-800 focus:ring-2 focus:ring-emerald-500">
                    <?php foreach (($roles ?? []) as $rKey => $rLabel): ?>
                        <option value="<?= $rKey ?>" <?= $rKey === 'staff' ? 'selected' : '' ?>><?= $rLabel ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-bold text-secondary-800 uppercase tracking-wider">মডিউল পারমিশন (Permissions)</label>
                    <div class="flex gap-2">
                        <button type="button" onclick="hrQuickSetAllPerms(true)" class="text-[11px] text-emerald-600 hover:underline">সব নির্বাচন</button>
                        <span class="text-secondary-300 text-xs">|</span>
                        <button type="button" onclick="hrQuickSetAllPerms(false)" class="text-[11px] text-secondary-500 hover:underline">সব বাতিল</button>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-40 overflow-y-auto p-2.5 border border-secondary-200 rounded-xl bg-secondary-50/50">
                    <?php foreach (($allPermissions ?? []) as $groupName => $modules): ?>
                        <div class="col-span-full pt-1 text-[10px] font-bold uppercase tracking-wider text-secondary-400"><?= htmlspecialchars($groupName) ?></div>
                        <?php foreach ($modules as $permKey => $perm): ?>
                            <label class="flex items-center gap-2 p-1.5 rounded-lg bg-white border border-secondary-200 text-xs cursor-pointer hover:bg-emerald-50">
                                <input type="checkbox" name="permissions[]" value="<?= $permKey ?>" class="rounded text-emerald-600 focus:ring-emerald-500 hr-quick-perm-cb">
                                <span class="font-medium text-secondary-800 text-xs"><?= htmlspecialchars($perm['label_bn']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-secondary-100">
                <button type="button" onclick="closeHrQuickUserModal()" class="px-4 py-2 border border-secondary-300 text-secondary-700 rounded-xl text-xs font-medium hover:bg-secondary-50">বাতিল</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-colors shadow-xs flex items-center gap-1.5">
                    <ion-icon name="checkmark-circle-outline" class="text-sm"></ion-icon>
                    <span>লগইন একাউন্ট সংরক্ষণ করুন</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const hrQuickRolePresets = {
    admin: ['*'],
    manager: ['dashboard', 'products', 'categories_brands', 'orders', 'dispatch', 'delivery_men', 'customers', 'vendors_purchases', 'locations', 'hr', 'payroll', 'reports'],
    accountant: ['dashboard', 'vendors_purchases', 'payroll', 'reports'],
    agent: ['dashboard', 'orders', 'customers'],
    delivery_man: ['orders'],
    staff: ['dashboard', 'products', 'orders']
};

function openHrQuickUserModal(empId, empName) {
    document.getElementById('hrQuickEmpId').value = empId;
    document.getElementById('hrQuickEmpName').innerText = empName;
    
    let clean = empName.toLowerCase().replace(/[^a-z0-9]/g, '');
    document.getElementById('hrQuickUsername').value = clean ? clean.substring(0, 10) : ('emp_' + empId);
    
    document.getElementById('hrQuickUserModal').classList.remove('hidden');
    hrQuickRoleChange(document.getElementById('hrQuickRoleSelect').value);
}

function closeHrQuickUserModal() {
    document.getElementById('hrQuickUserModal').classList.add('hidden');
}

function hrQuickRoleChange(role) {
    const preset = hrQuickRolePresets[role] || [];
    const isWildcard = preset.includes('*');
    document.querySelectorAll('.hr-quick-perm-cb').forEach(cb => {
        cb.checked = isWildcard || preset.includes(cb.value);
    });
}

function hrQuickSetAllPerms(check) {
    document.querySelectorAll('.hr-quick-perm-cb').forEach(cb => {
        cb.checked = check;
    });
}
</script>
