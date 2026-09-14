<?php 
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-secondary-200 shadow-sm print:hidden">
        <div>
            <h2 class="text-xl font-bold text-secondary-900 flex items-center gap-2">
                <ion-icon name="bar-chart" class="text-primary-600 text-2xl"></ion-icon>
                মাসিক হাজিরা রিপোর্ট (Monthly Attendance Report)
            </h2>
            <p class="text-secondary-500 text-sm mt-1">নির্বাচিত মাসের কর্মচারীদের সামগ্রিক উপস্থিতি, অনুপস্থিতি ও ছুটির বিবরণ</p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-secondary-100 hover:bg-secondary-200 text-secondary-800 rounded-xl text-sm font-medium flex items-center gap-1.5 transition-colors">
                <ion-icon name="print-outline" class="text-lg text-secondary-600"></ion-icon>
                <span>প্রিন্ট করুন</span>
            </button>
            <a href="<?= $base ?>/admin/hr/attendance" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-sm font-medium flex items-center gap-1.5 transition-colors shadow-sm">
                <ion-icon name="calendar-outline" class="text-lg"></ion-icon>
                <span>দৈনিক হাজিরা</span>
            </a>
        </div>
    </div>

    <!-- Month Filter Form -->
    <div class="bg-white p-4 rounded-2xl border border-secondary-200 shadow-sm print:hidden">
        <form method="GET" action="<?= $base ?>/admin/hr/attendance/report" class="flex flex-wrap items-center gap-3">
            <label class="text-xs font-semibold text-secondary-600 uppercase tracking-wider">মাস নির্বাচন করুন:</label>
            <input type="month" name="month" value="<?= htmlspecialchars($currentMonth) ?>" onchange="this.form.submit()" class="bg-secondary-50 border border-secondary-300 rounded-xl px-3 py-1.5 text-sm font-bold text-secondary-800 focus:ring-2 focus:ring-primary-500">
            <button type="submit" class="px-4 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-xs font-medium rounded-xl">রিপোর্ট দেখুন</button>
            <span class="text-xs text-secondary-500 ml-auto">রিপোর্ট মাস: <strong class="text-secondary-800"><?= date('F Y', strtotime($currentMonth . '-01')) ?></strong></span>
        </form>
    </div>

    <!-- Report Table -->
    <div class="bg-white rounded-2xl border border-secondary-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-secondary-100 hidden print:block">
            <h3 class="text-lg font-bold text-center text-secondary-900">মাসিক উপস্থিতি প্রতিবেদন</h3>
            <p class="text-xs text-center text-secondary-500 mt-0.5">মাস: <?= date('F Y', strtotime($currentMonth . '-01')) ?></p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-secondary-50 text-secondary-600 uppercase text-xs border-b border-secondary-200">
                    <tr>
                        <th class="py-3 px-4">কর্মচারী</th>
                        <th class="py-3 px-4">ডিপার্টমেন্ট</th>
                        <th class="py-3 px-4 text-center">উপস্থিত</th>
                        <th class="py-3 px-4 text-center">দেরি</th>
                        <th class="py-3 px-4 text-center">অর্ধ দিবস</th>
                        <th class="py-3 px-4 text-center">অনুপস্থিত</th>
                        <th class="py-3 px-4 text-center">ছুটি</th>
                        <th class="py-3 px-4 text-center">হাজিরা হার (%)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-secondary-100">
                    <?php if (empty($reportData)): ?>
                        <tr>
                            <td colspan="8" class="py-12 text-center text-secondary-400">এই মাসের জন্য কোনো উপস্থিতি রেকর্ড নেই।</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reportData as $row): 
                            $logged = intval($row['total_logged'] ?? 0);
                            $present = intval($row['present_count'] ?? 0);
                            $late = intval($row['late_count'] ?? 0);
                            $half = intval($row['half_day_count'] ?? 0);
                            $absent = intval($row['absent_count'] ?? 0);
                            $leave = intval($row['leave_count'] ?? 0);

                            // Effective present days
                            $effective = $present + $late + ($half * 0.5);
                            $pct = $logged > 0 ? min(100, round(($effective / $logged) * 100)) : 0;
                        ?>
                            <tr class="hover:bg-secondary-50/50 transition-colors">
                                <td class="py-3 px-4 font-semibold text-secondary-900">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-xs px-1.5 py-0.5 bg-secondary-100 rounded text-secondary-700"><?= htmlspecialchars($row['emp_code']) ?></span>
                                        <span><?= htmlspecialchars($row['name']) ?></span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-xs text-secondary-600">
                                    <?= htmlspecialchars($row['department_name'] ?? 'N/A') ?>
                                    <p class="text-[11px] text-secondary-400"><?= htmlspecialchars($row['designation_title'] ?? '') ?></p>
                                </td>
                                <td class="py-3 px-4 text-center font-bold text-emerald-600"><?= $present ?></td>
                                <td class="py-3 px-4 text-center font-bold text-amber-600"><?= $late ?></td>
                                <td class="py-3 px-4 text-center font-bold text-blue-600"><?= $half ?></td>
                                <td class="py-3 px-4 text-center font-bold text-red-600"><?= $absent ?></td>
                                <td class="py-3 px-4 text-center font-bold text-purple-600"><?= $leave ?></td>
                                <td class="py-3 px-4 text-center">
                                    <div class="inline-flex items-center gap-2">
                                        <div class="w-16 bg-secondary-200 rounded-full h-2 overflow-hidden print:hidden">
                                            <div class="bg-primary-600 h-2 rounded-full" style="width: <?= $pct ?>%"></div>
                                        </div>
                                        <span class="text-xs font-bold <?= $pct >= 80 ? 'text-emerald-700' : ($pct >= 50 ? 'text-amber-700' : 'text-red-700') ?>">
                                            <?= $pct ?>%
                                        </span>
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
