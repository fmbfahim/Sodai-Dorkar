<?php 
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-secondary-200 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-secondary-900 flex items-center gap-2">
                <ion-icon name="business" class="text-primary-600 text-2xl"></ion-icon>
                ডিপার্টমেন্ট ও পদবী ব্যবস্থাপনা (Departments & Designations)
            </h2>
            <p class="text-secondary-500 text-sm mt-1">কোম্পানির বিভাগসমূহ এবং প্রতিটি বিভাগের অধীনস্থ পদবীসমূহ নিয়ন্ত্রণ করুন</p>
        </div>
        <a href="<?= $base ?>/admin/hr/employees" class="px-4 py-2 bg-secondary-100 hover:bg-secondary-200 text-secondary-700 rounded-xl text-sm font-medium flex items-center gap-1.5 transition-colors">
            <ion-icon name="people-outline" class="text-lg"></ion-icon>
            <span>কর্মচারী তালিকায় যান</span>
        </a>
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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Column 1: Departments -->
        <div class="space-y-6">
            <!-- Add Department Card -->
            <div class="bg-white rounded-2xl border border-secondary-200 shadow-sm p-5 space-y-4">
                <h3 class="text-base font-bold text-secondary-900 flex items-center gap-2 border-b border-secondary-100 pb-2">
                    <ion-icon name="add-circle-outline" class="text-primary-600 text-xl"></ion-icon>
                    নতুন ডিপার্টমেন্ট যোগ করুন
                </h3>
                <form method="POST" action="<?= $base ?>/admin/hr/departments/store" class="space-y-3">
                    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                    <div>
                        <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">ডিপার্টমেন্টের নাম <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required placeholder="e.g. Accounts, Operations, HR" class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">বিবরণ (ঐচ্ছিক)</label>
                        <input type="text" name="description" placeholder="ডিপার্টমেন্টের সাধারণ দায়িত্ব..." class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div class="text-right">
                        <button type="submit" class="px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-sm font-medium transition-colors shadow-sm">
                            ডিপার্টমেন্ট সংরক্ষণ
                        </button>
                    </div>
                </form>
            </div>

            <!-- Existing Departments Table -->
            <div class="bg-white rounded-2xl border border-secondary-200 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-secondary-100 flex items-center justify-between">
                    <h3 class="font-bold text-secondary-800 text-sm">বিদ্যমান ডিপার্টমেন্ট তালিকা (<?= count($departments) ?>টি)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-secondary-50 text-secondary-600 uppercase text-xs">
                            <tr>
                                <th class="py-3 px-4">ডিপার্টমেন্টের নাম</th>
                                <th class="py-3 px-4 text-center">পদবী</th>
                                <th class="py-3 px-4 text-center">কর্মচারী</th>
                                <th class="py-3 px-4 text-right">অ্যাকশন</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-secondary-100">
                            <?php if (empty($departments)): ?>
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-secondary-400">কোনো ডিপার্টমেন্ট তৈরি করা হয়নি।</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($departments as $d): ?>
                                    <tr class="hover:bg-secondary-50/50">
                                        <td class="py-3 px-4 font-semibold text-secondary-800">
                                            <?= htmlspecialchars($d['name']) ?>
                                            <?php if (!empty($d['description'])): ?>
                                                <p class="text-xs text-secondary-400 font-normal"><?= htmlspecialchars($d['description']) ?></p>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded-md font-bold text-xs"><?= $d['designation_count'] ?? 0 ?></span>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded-md font-bold text-xs"><?= $d['employee_count'] ?? 0 ?> জন</span>
                                        </td>
                                        <td class="py-3 px-4 text-right">
                                            <form method="POST" action="<?= $base ?>/admin/hr/departments/delete" onsubmit="return confirm('আপনি কি নিশ্চিত এই ডিপার্টমেন্ট মুছে ফেলতে চান?')" class="inline">
                                                <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                                                <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                                <button type="submit" class="p-1 text-secondary-400 hover:text-red-600 rounded-lg transition-colors" title="মুছে ফেলুন">
                                                    <ion-icon name="trash-outline" class="text-lg"></ion-icon>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Column 2: Designations -->
        <div class="space-y-6">
            <!-- Add Designation Card -->
            <div class="bg-white rounded-2xl border border-secondary-200 shadow-sm p-5 space-y-4">
                <h3 class="text-base font-bold text-secondary-900 flex items-center gap-2 border-b border-secondary-100 pb-2">
                    <ion-icon name="add-circle-outline" class="text-blue-600 text-xl"></ion-icon>
                    নতুন পদবী যোগ করুন
                </h3>
                <form method="POST" action="<?= $base ?>/admin/hr/designations/store" class="space-y-3">
                    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                    <div>
                        <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">ডিপার্টমেন্ট নির্বাচন করুন <span class="text-red-500">*</span></label>
                        <select name="department_id" required class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
                            <option value="">-- ডিপার্টমেন্ট বাছুন --</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">পদবীর নাম (Designation Title) <span class="text-red-500">*</span></label>
                        <input type="text" name="title" required placeholder="e.g. Senior Accountant, Delivery Rider" class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div class="text-right">
                        <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-medium transition-colors shadow-sm">
                            পদবী সংরক্ষণ
                        </button>
                    </div>
                </form>
            </div>

            <!-- Existing Designations List -->
            <div class="bg-white rounded-2xl border border-secondary-200 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-secondary-100">
                    <h3 class="font-bold text-secondary-800 text-sm">সকল পদবী তালিকা (<?= count($designations) ?>টি)</h3>
                </div>
                <div class="divide-y divide-secondary-100 max-h-[480px] overflow-y-auto">
                    <?php if (empty($designations)): ?>
                        <p class="py-6 text-center text-secondary-400 text-sm">কোনো পদবী তৈরি করা হয়নি।</p>
                    <?php else: ?>
                        <?php foreach ($designations as $des): ?>
                            <div class="p-3.5 flex items-center justify-between hover:bg-secondary-50 transition-colors">
                                <div>
                                    <p class="font-bold text-secondary-800 text-sm"><?= htmlspecialchars($des['title']) ?></p>
                                    <span class="inline-block text-xs text-primary-600 font-medium">
                                        <?= htmlspecialchars($des['department_name']) ?>
                                    </span>
                                </div>
                                <form method="POST" action="<?= $base ?>/admin/hr/designations/delete" onsubmit="return confirm('পদবীটি মুছে ফেলতে চান?')" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                                    <input type="hidden" name="id" value="<?= $des['id'] ?>">
                                    <button type="submit" class="p-1.5 text-secondary-400 hover:text-red-600 rounded-lg transition-colors" title="মুছে ফেলুন">
                                        <ion-icon name="trash-outline" class="text-base"></ion-icon>
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
