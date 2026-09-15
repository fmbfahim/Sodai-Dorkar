<?php 
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
$isSuperAdmin = ($user['id'] == 1 || ($user['role'] === 'admin' && $user['username'] === 'admin'));
$userPerms = $user['parsed_permissions'] ?? [];
$hasWildcard = in_array('*', $userPerms, true);
?>

<div class="max-w-4xl mx-auto space-y-6 mb-16">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-secondary-900 flex items-center gap-2">
                <ion-icon name="create" class="text-primary-600 text-2xl"></ion-icon>
                ব্যবহারকারী ও পারমিশন সম্পাদন (Edit User & Permissions)
            </h2>
            <p class="text-secondary-500 text-sm mt-1">ব্যবহারকারীর প্রোফাইল তথ্য ও নির্দিষ্ট মডিউলের এক্সেস পারমিশন কন্ট্রোল করুন</p>
        </div>
        <a href="<?= $base ?>/admin/users" class="px-4 py-2 bg-secondary-100 hover:bg-secondary-200 text-secondary-700 rounded-xl text-sm font-medium flex items-center gap-1.5 transition-colors">
            <ion-icon name="arrow-back-outline"></ion-icon>
            <span>তালিকায় ফিরে যান</span>
        </a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center gap-2 text-sm shadow-xs">
            <ion-icon name="alert-circle" class="text-xl text-red-600 shrink-0"></ion-icon>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= $base ?>/admin/users/update" id="userEditForm" class="space-y-6">
        <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
        <input type="hidden" name="id" value="<?= $user['id'] ?>">

        <!-- Account Basics Card -->
        <div class="bg-white rounded-2xl border border-secondary-200 shadow-xs p-6 space-y-5">
            <h3 class="text-base font-bold text-secondary-900 flex items-center gap-2 border-b border-secondary-100 pb-3">
                <ion-icon name="person-circle-outline" class="text-primary-600 text-xl"></ion-icon>
                প্রাথমিক একাউন্ট তথ্য (Basic Account Info)
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">পূর্ণ নাম <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required value="<?= htmlspecialchars($user['name']) ?>" class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2.5 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">ইউজারনেম (Login ID) <span class="text-red-500">*</span></label>
                    <input type="text" name="username" required value="<?= htmlspecialchars($user['username']) ?>" <?= $isSuperAdmin ? 'readonly' : '' ?> class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2.5 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500 <?= $isSuperAdmin ? 'opacity-70 cursor-not-allowed' : '' ?>">
                    <?php if ($isSuperAdmin): ?>
                        <p class="text-[11px] text-amber-600 mt-1">সুপার এডমিন ইউজারনেম পরিবর্তনযোগ্য নয়।</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">মোবাইল নম্বর</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2.5 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">ইমেইল এড্রেস</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2.5 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">সিস্টেম রোল (Role) <span class="text-red-500">*</span></label>
                    <select name="role" id="roleSelect" onchange="handleRoleChange(this.value)" <?= $isSuperAdmin ? 'disabled' : '' ?> required class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2.5 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500 <?= $isSuperAdmin ? 'opacity-70 cursor-not-allowed' : '' ?>">
                        <?php foreach ($roles as $rKey => $rLabel): ?>
                            <option value="<?= $rKey ?>" <?= $user['role'] === $rKey ? 'selected' : '' ?>><?= $rLabel ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($isSuperAdmin): ?>
                        <input type="hidden" name="role" value="admin">
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">লিংকড কর্মচারী (Link with Employee)</label>
                    <select name="employee_id" class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2.5 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
                        <option value="">-- কোনো কর্মচারী লিংক নয় --</option>
                        <?php foreach (($employees ?? []) as $emp): ?>
                            <option value="<?= $emp['id'] ?>" <?= ($user['employee_id'] ?? null) == $emp['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($emp['emp_code'] . ' - ' . $emp['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Password Change Section (Optional) -->
            <div class="p-4 bg-secondary-50 rounded-xl border border-secondary-200">
                <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">নতুন পাসওয়ার্ড দিন (যদি পরিবর্তন করতে চান)</label>
                <input type="password" name="password" minlength="6" placeholder="পরিবর্তন না করতে চাইলে ফাঁকা রাখুন" class="w-full bg-white border border-secondary-300 rounded-xl px-3.5 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
                <p class="text-[11px] text-secondary-500 mt-1">পাসওয়ার্ড পরিবর্তন করতে না চাইলে এই ঘরটি ফাঁকা রাখুন।</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-2">একাউন্ট স্ট্যাটাস</label>
                <div class="flex items-center gap-6">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="status" value="active" <?= ($user['status'] ?? 'active') === 'active' ? 'checked' : '' ?> class="text-primary-600 focus:ring-primary-500">
                        <span class="text-sm font-medium text-secondary-800">সক্রিয় (Active)</span>
                    </label>
                    <label class="inline-flex items-center gap-2 <?= $isSuperAdmin ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer' ?>">
                        <input type="radio" name="status" value="inactive" <?= ($user['status'] ?? '') === 'inactive' ? 'checked' : '' ?> <?= $isSuperAdmin ? 'disabled' : '' ?> class="text-primary-600 focus:ring-primary-500">
                        <span class="text-sm font-medium text-secondary-800">নিষ্ক্রিয় (Inactive)</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Role-Based Permissions Section -->
        <div class="bg-white rounded-2xl border border-secondary-200 shadow-xs p-6 space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-secondary-100 pb-3">
                <div>
                    <h3 class="text-base font-bold text-secondary-900 flex items-center gap-2">
                        <ion-icon name="key-outline" class="text-primary-600 text-xl"></ion-icon>
                        মডিউল পারমিশন ও এক্সেস কন্ট্রোল (Module Access Permissions)
                    </h3>
                    <p class="text-xs text-secondary-500 mt-0.5">এই ব্যবহারকারী কোন কোন মডিউল দেখতে ও পরিচালনা করতে পারবেন তা নির্ধারণ করুন</p>
                </div>
                <div class="flex items-center gap-2" id="permissionActions">
                    <button type="button" onclick="setAllPermissions(false)" class="px-3 py-1.5 bg-secondary-100 hover:bg-secondary-200 text-secondary-700 rounded-lg text-xs font-semibold transition-colors">
                        সব বাতিল (Clear All)
                    </button>
                    <button type="button" onclick="setAllPermissions(true)" class="px-3 py-1.5 bg-primary-50 hover:bg-primary-100 text-primary-700 rounded-lg text-xs font-semibold transition-colors">
                        সব নির্বাচন (Select All)
                    </button>
                    <button type="button" onclick="applyRoleDefaults()" class="px-3 py-1.5 bg-secondary-800 hover:bg-secondary-900 text-white rounded-lg text-xs font-semibold transition-colors">
                        রোল ডিফল্ট (Preset)
                    </button>
                </div>
            </div>

            <?php if ($user['role'] === 'admin'): ?>
                <div id="adminNotice" class="p-4 bg-purple-50 border border-purple-200 rounded-xl text-purple-900 flex items-center gap-3">
                    <ion-icon name="shield-checkmark" class="text-2xl text-purple-600 shrink-0"></ion-icon>
                    <div>
                        <h4 class="font-bold text-sm">সুপার এডমিন আনলিমিটেড এক্সেস (Unrestricted Access)</h4>
                        <p class="text-xs text-purple-700 mt-0.5">এডমিন রোলের ব্যবহারকারীদের সিস্টেমের প্রতিটি ফিচার, সেটিংস ও ডাটাবেসের সম্পূর্ণ অ্যাক্সেস রয়েছে।</p>
                    </div>
                </div>
            <?php endif; ?>

            <div class="space-y-6 pt-1">
                <?php foreach (($allPermissions ?? []) as $groupName => $modules): ?>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between border-b border-secondary-100 pb-1.5">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-secondary-500"><?= htmlspecialchars($groupName) ?></h4>
                            <button type="button" onclick="toggleGroup('group_<?= md5($groupName) ?>')" class="text-xs text-primary-600 hover:text-primary-700 font-medium">গ্রুপ টগল</button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3" id="group_<?= md5($groupName) ?>">
                            <?php foreach ($modules as $permKey => $perm): ?>
                                <?php 
                                $isChecked = $hasWildcard || in_array($permKey, $userPerms, true);
                                ?>
                                <label class="border-2 rounded-xl p-3.5 flex items-start gap-3 cursor-pointer select-none transition-all duration-150 <?= $isChecked ? 'border-primary-500 bg-primary-50/20' : 'border-secondary-200 bg-white hover:bg-secondary-50' ?>" id="perm_label_<?= $permKey ?>">
                                    <input type="checkbox" name="permissions[]" value="<?= $permKey ?>" <?= $isChecked ? 'checked' : '' ?> onchange="handlePermToggle('<?= $permKey ?>')" class="w-4 h-4 rounded text-primary-600 focus:ring-primary-500 border-secondary-300 mt-0.5 perm-checkbox">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-1.5 mb-0.5">
                                            <ion-icon name="<?= $perm['icon'] ?>" class="text-base text-secondary-600"></ion-icon>
                                            <span class="font-bold text-xs sm:text-sm text-secondary-900"><?= htmlspecialchars($perm['label_bn']) ?></span>
                                            <span class="text-[11px] text-secondary-400 font-medium ml-auto"><?= htmlspecialchars($perm['label']) ?></span>
                                        </div>
                                        <p class="text-[11px] text-secondary-500 leading-snug"><?= htmlspecialchars($perm['desc']) ?></p>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-3">
            <a href="<?= $base ?>/admin/users" class="px-5 py-2.5 border border-secondary-300 text-secondary-700 rounded-xl text-sm font-medium hover:bg-secondary-50">বাতিল</a>
            <button type="submit" class="px-7 py-2.5 bg-primary-600 hover:bg-primary-700 active:scale-95 text-white rounded-xl text-sm font-bold transition-all shadow-sm flex items-center gap-2">
                <ion-icon name="save-outline" class="text-lg"></ion-icon>
                <span>আপডেট ও পারমিশন সংরক্ষণ করুন</span>
            </button>
        </div>
    </form>
</div>

<script>
const rolePresets = {
    admin: ['*'],
    manager: ['dashboard', 'products', 'categories_brands', 'orders', 'dispatch', 'delivery_men', 'customers', 'vendors_purchases', 'locations', 'hr', 'payroll', 'reports'],
    accountant: ['dashboard', 'vendors_purchases', 'payroll', 'reports'],
    agent: ['dashboard', 'orders', 'customers'],
    delivery_man: ['orders'],
    staff: ['dashboard', 'products', 'orders']
};

function handleRoleChange(newRole) {
    const adminNotice = document.getElementById('adminNotice');
    if (adminNotice) {
        adminNotice.style.display = (newRole === 'admin') ? 'flex' : 'none';
    }
}

function handlePermToggle(key) {
    const label = document.getElementById('perm_label_' + key);
    const cb = label.querySelector('input[type="checkbox"]');
    if (cb.checked) {
        label.classList.add('border-primary-500', 'bg-primary-50/20');
        label.classList.remove('border-secondary-200', 'bg-white');
    } else {
        label.classList.remove('border-primary-500', 'bg-primary-50/20');
        label.classList.add('border-secondary-200', 'bg-white');
    }
}

function setAllPermissions(check) {
    const checkboxes = document.querySelectorAll('.perm-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = check;
        const key = cb.value;
        const label = document.getElementById('perm_label_' + key);
        if (label) {
            if (check) {
                label.classList.add('border-primary-500', 'bg-primary-50/20');
                label.classList.remove('border-secondary-200', 'bg-white');
            } else {
                label.classList.remove('border-primary-500', 'bg-primary-50/20');
                label.classList.add('border-secondary-200', 'bg-white');
            }
        }
    });
}

function applyRoleDefaults() {
    const role = document.getElementById('roleSelect').value;
    const preset = rolePresets[role] || [];
    const isWildcard = preset.includes('*');

    const checkboxes = document.querySelectorAll('.perm-checkbox');
    checkboxes.forEach(cb => {
        const key = cb.value;
        const shouldCheck = isWildcard || preset.includes(key);
        cb.checked = shouldCheck;
        const label = document.getElementById('perm_label_' + key);
        if (label) {
            if (shouldCheck) {
                label.classList.add('border-primary-500', 'bg-primary-50/20');
                label.classList.remove('border-secondary-200', 'bg-white');
            } else {
                label.classList.remove('border-primary-500', 'bg-primary-50/20');
                label.classList.add('border-secondary-200', 'bg-white');
            }
        }
    });
}

function toggleGroup(groupId) {
    const group = document.getElementById(groupId);
    if (!group) return;
    const cbs = group.querySelectorAll('.perm-checkbox');
    const allChecked = Array.from(cbs).every(cb => cb.checked);
    cbs.forEach(cb => {
        cb.checked = !allChecked;
        const key = cb.value;
        const label = document.getElementById('perm_label_' + key);
        if (label) {
            if (!allChecked) {
                label.classList.add('border-primary-500', 'bg-primary-50/20');
                label.classList.remove('border-secondary-200', 'bg-white');
            } else {
                label.classList.remove('border-primary-500', 'bg-primary-50/20');
                label.classList.add('border-secondary-200', 'bg-white');
            }
        }
    });
}
</script>
