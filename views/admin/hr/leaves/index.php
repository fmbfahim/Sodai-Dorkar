<?php 
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-secondary-200 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-secondary-900 flex items-center gap-2">
                <ion-icon name="airplane" class="text-primary-600 text-2xl"></ion-icon>
                ছুটি ব্যবস্থাপনা ও আবেদন (Leave Management)
            </h2>
            <p class="text-secondary-500 text-sm mt-1">কর্মচারীদের ছুটির আবেদন গ্রহণ, অনুমোদন ও প্রত্যাখ্যান পরিচালনা করুন</p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="openLeaveModal()" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-xl text-sm flex items-center gap-2 transition-all shadow-sm">
                <ion-icon name="add-circle" class="text-lg"></ion-icon>
                <span>ছুটির আবেদন দাখিল করুন</span>
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

    <!-- Status Tabs -->
    <div class="flex items-center gap-2 border-b border-secondary-200 pb-2 overflow-x-auto">
        <a href="<?= $base ?>/admin/hr/leaves" class="px-4 py-2 rounded-xl text-sm font-medium transition-colors <?= empty($currentStatus) ? 'bg-primary-600 text-white shadow-sm' : 'bg-white text-secondary-600 hover:bg-secondary-100' ?>">
            সকল আবেদন (All)
        </a>
        <a href="<?= $base ?>/admin/hr/leaves?status=pending" class="px-4 py-2 rounded-xl text-sm font-medium transition-colors <?= $currentStatus === 'pending' ? 'bg-amber-500 text-white shadow-sm' : 'bg-white text-secondary-600 hover:bg-secondary-100' ?>">
            অপেক্ষমাণ (Pending)
        </a>
        <a href="<?= $base ?>/admin/hr/leaves?status=approved" class="px-4 py-2 rounded-xl text-sm font-medium transition-colors <?= $currentStatus === 'approved' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-secondary-600 hover:bg-secondary-100' ?>">
            অনুমোদিত (Approved)
        </a>
        <a href="<?= $base ?>/admin/hr/leaves?status=rejected" class="px-4 py-2 rounded-xl text-sm font-medium transition-colors <?= $currentStatus === 'rejected' ? 'bg-red-600 text-white shadow-sm' : 'bg-white text-secondary-600 hover:bg-secondary-100' ?>">
            প্রত্যাখ্যাত (Rejected)
        </a>
    </div>

    <!-- Leaves Table -->
    <div class="bg-white rounded-2xl border border-secondary-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-secondary-50 text-secondary-600 uppercase text-xs border-b border-secondary-200">
                    <tr>
                        <th class="py-3.5 px-4 font-semibold">কর্মচারী</th>
                        <th class="py-3.5 px-4 font-semibold">ছুটির ধরন</th>
                        <th class="py-3.5 px-4 font-semibold">সময়সীমা</th>
                        <th class="py-3.5 px-4 font-semibold text-center">দিন সংখ্যা</th>
                        <th class="py-3.5 px-4 font-semibold">ছুটির কারণ</th>
                        <th class="py-3.5 px-4 font-semibold text-center">স্ট্যাটাস</th>
                        <th class="py-3.5 px-4 font-semibold text-right">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-secondary-100">
                    <?php if (empty($leaves)): ?>
                        <tr>
                            <td colspan="7" class="py-12 text-center text-secondary-400">
                                <ion-icon name="airplane-outline" class="text-4xl text-secondary-300 mb-2"></ion-icon>
                                <p>কোনো ছুটির আবেদন পাওয়া যায়নি।</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($leaves as $l): ?>
                            <tr class="hover:bg-secondary-50/50 transition-colors">
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-secondary-900"><?= htmlspecialchars($l['employee_name']) ?></div>
                                    <div class="text-xs text-secondary-500 font-mono"><?= htmlspecialchars($l['emp_code']) ?> &bull; <?= htmlspecialchars($l['department_name'] ?? 'N/A') ?></div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <?php
                                    $typeLabels = [
                                        'casual' => 'নৈমিত্তিক (Casual)',
                                        'sick' => 'অসুস্থতা (Sick)',
                                        'annual' => 'বার্ষিক (Annual)',
                                        'unpaid' => 'বিনা বেতন (Unpaid)'
                                    ];
                                    ?>
                                    <span class="inline-flex px-2 py-0.5 rounded-lg text-xs font-semibold bg-secondary-100 text-secondary-700">
                                        <?= $typeLabels[$l['leave_type']] ?? ucfirst($l['leave_type']) ?>
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-xs text-secondary-700">
                                    <?= date('d M, Y', strtotime($l['start_date'])) ?> হতে<br>
                                    <?= date('d M, Y', strtotime($l['end_date'])) ?>
                                </td>
                                <td class="py-3.5 px-4 text-center font-bold text-secondary-900">
                                    <?= $l['total_days'] ?> দিন
                                </td>
                                <td class="py-3.5 px-4 text-xs text-secondary-600 max-w-xs truncate">
                                    <?= htmlspecialchars($l['reason'] ?: 'কোনো কারণ উল্লেখ নেই') ?>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <?php
                                    $stBadges = [
                                        'approved' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                                        'rejected' => 'bg-red-100 text-red-800 border-red-200'
                                    ];
                                    $stLabels = [
                                        'approved' => 'অনুমোদিত',
                                        'pending' => 'অপেক্ষমাণ',
                                        'rejected' => 'প্রত্যাখ্যাত'
                                    ];
                                    $st = $l['status'] ?? 'pending';
                                    ?>
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium border <?= $stBadges[$st] ?? '' ?>">
                                        <?= $stLabels[$st] ?? ucfirst($st) ?>
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <?php if ($l['status'] === 'pending'): ?>
                                            <!-- Approve Button -->
                                            <form method="POST" action="<?= $base ?>/admin/hr/leaves/update-status" class="inline">
                                                <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                                                <input type="hidden" name="id" value="<?= $l['id'] ?>">
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="px-2 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-medium transition-colors shadow-sm" title="অনুমোদন করুন">
                                                    অনুমোদন
                                                </button>
                                            </form>
                                            <!-- Reject Button -->
                                            <form method="POST" action="<?= $base ?>/admin/hr/leaves/update-status" class="inline">
                                                <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                                                <input type="hidden" name="id" value="<?= $l['id'] ?>">
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-medium transition-colors shadow-sm" title="প্রত্যাখ্যান করুন">
                                                    প্রত্যাখ্যান
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <!-- Delete Button -->
                                        <form method="POST" action="<?= $base ?>/admin/hr/leaves/delete" onsubmit="return confirm('ছুটির আবেদনটি মুছে ফেলতে চান?')" class="inline">
                                            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                                            <input type="hidden" name="id" value="<?= $l['id'] ?>">
                                            <button type="submit" class="p-1.5 text-secondary-400 hover:text-red-600 rounded-lg transition-colors" title="মুছে ফেলুন">
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

<!-- Modal: Apply for Leave -->
<div id="leaveModal" class="fixed inset-0 bg-secondary-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-secondary-200">
        <div class="flex items-center justify-between pb-4 border-b border-secondary-100">
            <h3 class="text-lg font-bold text-secondary-900 flex items-center gap-2">
                <ion-icon name="airplane" class="text-primary-600"></ion-icon>
                নতুন ছুটির আবেদন দাখিল
            </h3>
            <button onclick="closeLeaveModal()" class="text-secondary-400 hover:text-secondary-700">
                <ion-icon name="close-circle-outline" class="text-2xl"></ion-icon>
            </button>
        </div>

        <form method="POST" action="<?= $base ?>/admin/hr/leaves/store" class="mt-4 space-y-4">
            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">

            <div>
                <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">কর্মচারী নির্বাচন করুন <span class="text-red-500">*</span></label>
                <select name="employee_id" required class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
                    <option value="">-- কর্মচারী বাছুন --</option>
                    <?php foreach (($employees ?? []) as $emp): ?>
                        <option value="<?= $emp['id'] ?>"><?= htmlspecialchars($emp['emp_code'] . ' - ' . $emp['name'] . ' (' . ($emp['department_name'] ?? 'N/A') . ')') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">ছুটির ধরন <span class="text-red-500">*</span></label>
                <select name="leave_type" required class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
                    <option value="casual">নৈমিত্তিক ছুটি (Casual Leave)</option>
                    <option value="sick">অসুস্থতাজনিত ছুটি (Sick Leave)</option>
                    <option value="annual">বাৎসরিক ছুটি (Annual Leave)</option>
                    <option value="unpaid">বিনা বেতনে ছুটি (Unpaid Leave)</option>
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">ছুটি শুরুর তারিখ <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" required value="<?= date('Y-m-d') ?>" class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">ছুটি সমাপ্তির তারিখ <span class="text-red-500">*</span></label>
                    <input type="date" name="end_date" required value="<?= date('Y-m-d') ?>" class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">ছুটির কারণ ও বিস্তারিত</label>
                <textarea name="reason" rows="3" placeholder="ছুটি চাওয়ার কারণ উল্লেখ করুন..." class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-secondary-100">
                <button type="button" onclick="closeLeaveModal()" class="px-4 py-2 border border-secondary-300 text-secondary-700 rounded-xl text-sm font-medium hover:bg-secondary-50">বাতিল</button>
                <button type="submit" class="px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-sm font-medium transition-colors shadow-sm">আবেদন জমা দিন</button>
            </div>
        </form>
    </div>
</div>

<script>
function openLeaveModal() {
    document.getElementById('leaveModal').classList.remove('hidden');
}

function closeLeaveModal() {
    document.getElementById('leaveModal').classList.add('hidden');
}
</script>
