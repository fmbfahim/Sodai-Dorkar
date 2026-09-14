<?php 
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
?>

<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-secondary-900 flex items-center gap-2">
                <ion-icon name="create" class="text-primary-600 text-2xl"></ion-icon>
                ব্যবহারকারী তথ্য সম্পাদন (Edit User)
            </h2>
            <p class="text-secondary-500 text-sm mt-1">ব্যবহারকারীর নাম, যোগাযোগের তথ্য, পাসওয়ার্ড ও রোল পরিবর্তন করুন</p>
        </div>
        <a href="<?= $base ?>/admin/users" class="px-4 py-2 bg-secondary-100 hover:bg-secondary-200 text-secondary-700 rounded-xl text-sm font-medium flex items-center gap-1.5 transition-colors">
            <ion-icon name="arrow-back-outline"></ion-icon>
            <span>তালিকায় ফিরে যান</span>
        </a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center gap-2 text-sm">
            <ion-icon name="alert-circle" class="text-xl text-red-600"></ion-icon>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl border border-secondary-200 shadow-sm p-6">
        <form method="POST" action="<?= $base ?>/admin/users/update" class="space-y-5">
            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
            <input type="hidden" name="id" value="<?= $user['id'] ?>">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">পূর্ণ নাম <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required value="<?= htmlspecialchars($user['name']) ?>" class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2.5 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">ইউজারনেম (Login ID) <span class="text-red-500">*</span></label>
                    <input type="text" name="username" required value="<?= htmlspecialchars($user['username']) ?>" class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2.5 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
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
                    <select name="role" required class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2.5 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
                        <?php foreach ($roles as $rKey => $rLabel): ?>
                            <option value="<?= $rKey ?>" <?= $user['role'] === $rKey ? 'selected' : '' ?>><?= $rLabel ?></option>
                        <?php endforeach; ?>
                    </select>
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
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="status" value="inactive" <?= ($user['status'] ?? '') === 'inactive' ? 'checked' : '' ?> class="text-primary-600 focus:ring-primary-500">
                        <span class="text-sm font-medium text-secondary-800">নিষ্ক্রিয় (Inactive)</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-5 border-t border-secondary-100">
                <a href="<?= $base ?>/admin/users" class="px-5 py-2.5 border border-secondary-300 text-secondary-700 rounded-xl text-sm font-medium hover:bg-secondary-50">বাতিল</a>
                <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-sm font-medium transition-colors shadow-sm">আপডেট সংরক্ষণ করুন</button>
            </div>
        </form>
    </div>
</div>
