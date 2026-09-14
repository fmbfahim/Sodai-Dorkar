<?php 
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
?>

<div class="space-y-6">
    <!-- Header & Date Navigation -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-secondary-200 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-secondary-900 flex items-center gap-2">
                <ion-icon name="calendar" class="text-primary-600 text-2xl"></ion-icon>
                দৈনিক হাজিরা খাতা (Daily Attendance Register)
            </h2>
            <p class="text-secondary-500 text-sm mt-1">কর্মচারীদের দৈনিক উপস্থিতি, ইন/আউট সময় ও মন্তব্য লিপিবদ্ধ করুন</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?= $base ?>/admin/hr/attendance/report" class="px-3.5 py-2 bg-secondary-100 hover:bg-secondary-200 text-secondary-800 rounded-xl text-sm font-medium flex items-center gap-1.5 transition-colors">
                <ion-icon name="bar-chart-outline" class="text-lg text-primary-600"></ion-icon>
                <span>মাসিক রিপোর্ট</span>
            </a>
            <a href="<?= $base ?>/admin/hr/leaves" class="px-3.5 py-2 bg-secondary-100 hover:bg-secondary-200 text-secondary-800 rounded-xl text-sm font-medium flex items-center gap-1.5 transition-colors">
                <ion-icon name="airplane-outline" class="text-lg text-amber-600"></ion-icon>
                <span>ছুটির খাতা</span>
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

    <!-- Date Selector Bar & Quick Buttons -->
    <div class="bg-white p-4 rounded-2xl border border-secondary-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <form method="GET" action="<?= $base ?>/admin/hr/attendance" class="flex flex-wrap items-center gap-2">
            <label class="text-xs font-semibold text-secondary-500 uppercase tracking-wider">হাজিরা তারিখ:</label>
            <input type="date" name="date" value="<?= htmlspecialchars($currentDate) ?>" onchange="this.form.submit()" class="bg-secondary-50 border border-secondary-300 rounded-xl px-3 py-1.5 text-sm font-bold text-secondary-800 focus:ring-2 focus:ring-primary-500">
            <button type="submit" class="px-3 py-1.5 bg-secondary-100 hover:bg-secondary-200 text-secondary-700 text-xs font-medium rounded-xl">লোড করুন</button>
            <a href="<?= $base ?>/admin/hr/attendance?date=<?= date('Y-m-d') ?>" class="px-2.5 py-1.5 text-xs text-primary-600 font-medium hover:underline">আজকের দিন</a>
            <a href="<?= $base ?>/admin/hr/attendance?date=<?= date('Y-m-d', strtotime('-1 day')) ?>" class="px-2.5 py-1.5 text-xs text-secondary-500 hover:underline">গতকাল</a>
        </form>

        <div class="flex items-center gap-2">
            <button type="button" onclick="markAllStatus('present')" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-xl text-xs font-bold transition-colors border border-emerald-200">
                সবাইকে 'উপস্থিত' চিহ্নিত করুন
            </button>
        </div>
    </div>

    <!-- Attendance Form -->
    <form method="POST" action="<?= $base ?>/admin/hr/attendance/store" class="space-y-4">
        <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
        <input type="hidden" name="date" value="<?= htmlspecialchars($currentDate) ?>">

        <div class="bg-white rounded-2xl border border-secondary-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-secondary-50 text-secondary-600 uppercase text-xs border-b border-secondary-200">
                        <tr>
                            <th class="py-3.5 px-4 font-semibold">কর্মচারী</th>
                            <th class="py-3.5 px-4 font-semibold text-center">উপস্থিতি স্ট্যাটাস (Status)</th>
                            <th class="py-3.5 px-4 font-semibold text-center">ইন টাইম</th>
                            <th class="py-3.5 px-4 font-semibold text-center">আউট টাইম</th>
                            <th class="py-3.5 px-4 font-semibold">মন্তব্য</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-secondary-100">
                        <?php if (empty($attendanceList)): ?>
                            <tr>
                                <td colspan="5" class="py-12 text-center text-secondary-400">কোনো সক্রিয় কর্মচারী পাওয়া যায়নি।</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($attendanceList as $att): 
                                $empId = $att['employee_id'];
                                $curStatus = $att['status'] ?? 'present';
                            ?>
                                <tr class="hover:bg-secondary-50/50 transition-colors">
                                    <td class="py-3.5 px-4">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold text-xs">
                                                <?= mb_substr($att['name'], 0, 1) ?>
                                            </div>
                                            <div>
                                                <a href="<?= $base ?>/admin/hr/employees/show?id=<?= $empId ?>" class="font-bold text-secondary-900 hover:text-primary-600">
                                                    <?= htmlspecialchars($att['name']) ?>
                                                </a>
                                                <p class="text-xs text-secondary-500 font-mono"><?= htmlspecialchars($att['emp_code']) ?> &bull; <?= htmlspecialchars($att['designation_title'] ?? 'N/A') ?></p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Status Radios -->
                                    <td class="py-3.5 px-4 text-center">
                                        <div class="inline-flex items-center gap-2 p-1 bg-secondary-50 rounded-xl border border-secondary-200 text-xs">
                                            <label class="px-2.5 py-1 rounded-lg cursor-pointer transition-all has-[:checked]:bg-emerald-600 has-[:checked]:text-white text-secondary-700 font-medium">
                                                <input type="radio" name="attendance[<?= $empId ?>][status]" value="present" <?= $curStatus === 'present' ? 'checked' : '' ?> class="sr-only status-radio" data-status="present">
                                                উপস্থিত
                                            </label>
                                            <label class="px-2.5 py-1 rounded-lg cursor-pointer transition-all has-[:checked]:bg-amber-500 has-[:checked]:text-white text-secondary-700 font-medium">
                                                <input type="radio" name="attendance[<?= $empId ?>][status]" value="late" <?= $curStatus === 'late' ? 'checked' : '' ?> class="sr-only status-radio" data-status="late">
                                                দেরি
                                            </label>
                                            <label class="px-2.5 py-1 rounded-lg cursor-pointer transition-all has-[:checked]:bg-blue-600 has-[:checked]:text-white text-secondary-700 font-medium">
                                                <input type="radio" name="attendance[<?= $empId ?>][status]" value="half_day" <?= $curStatus === 'half_day' ? 'checked' : '' ?> class="sr-only status-radio" data-status="half_day">
                                                অর্ধ দিবস
                                            </label>
                                            <label class="px-2.5 py-1 rounded-lg cursor-pointer transition-all has-[:checked]:bg-red-600 has-[:checked]:text-white text-secondary-700 font-medium">
                                                <input type="radio" name="attendance[<?= $empId ?>][status]" value="absent" <?= $curStatus === 'absent' ? 'checked' : '' ?> class="sr-only status-radio" data-status="absent">
                                                অনুপস্থিত
                                            </label>
                                            <label class="px-2.5 py-1 rounded-lg cursor-pointer transition-all has-[:checked]:bg-purple-600 has-[:checked]:text-white text-secondary-700 font-medium">
                                                <input type="radio" name="attendance[<?= $empId ?>][status]" value="leave" <?= $curStatus === 'leave' ? 'checked' : '' ?> class="sr-only status-radio" data-status="leave">
                                                ছুটি
                                            </label>
                                            <label class="px-2.5 py-1 rounded-lg cursor-pointer transition-all has-[:checked]:bg-secondary-600 has-[:checked]:text-white text-secondary-700 font-medium">
                                                <input type="radio" name="attendance[<?= $empId ?>][status]" value="holiday" <?= $curStatus === 'holiday' ? 'checked' : '' ?> class="sr-only status-radio" data-status="holiday">
                                                ছুটির দিন
                                            </label>
                                        </div>
                                    </td>

                                    <!-- In / Out Times -->
                                    <td class="py-3.5 px-4 text-center">
                                        <input type="time" name="attendance[<?= $empId ?>][in_time]" value="<?= htmlspecialchars($att['in_time'] ?? '') ?>" class="bg-secondary-50 border border-secondary-300 rounded-lg px-2 py-1 text-xs text-secondary-800 focus:ring-1 focus:ring-primary-500">
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <input type="time" name="attendance[<?= $empId ?>][out_time]" value="<?= htmlspecialchars($att['out_time'] ?? '') ?>" class="bg-secondary-50 border border-secondary-300 rounded-lg px-2 py-1 text-xs text-secondary-800 focus:ring-1 focus:ring-primary-500">
                                    </td>

                                    <!-- Note -->
                                    <td class="py-3.5 px-4">
                                        <input type="text" name="attendance[<?= $empId ?>][note]" value="<?= htmlspecialchars($att['note'] ?? '') ?>" placeholder="মন্তব্য..." class="w-full bg-secondary-50 border border-secondary-300 rounded-lg px-2.5 py-1 text-xs text-secondary-800 focus:ring-1 focus:ring-primary-500">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex items-center justify-between p-4 bg-white rounded-2xl border border-secondary-200 shadow-sm">
            <span class="text-xs text-secondary-500">তারিখ: <strong class="text-secondary-800"><?= date('d F, Y', strtotime($currentDate)) ?></strong></span>
            <button type="submit" class="px-8 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-sm font-bold transition-all shadow-md flex items-center gap-2">
                <ion-icon name="checkmark-done" class="text-xl"></ion-icon>
                <span>হাজিরা সংরক্ষণ করুন</span>
            </button>
        </div>
    </form>
</div>

<script>
function markAllStatus(status) {
    const radios = document.querySelectorAll(`input.status-radio[data-status="${status}"]`);
    radios.forEach(radio => {
        radio.checked = true;
    });
}
</script>
