<?php 
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
?>

<div class="space-y-6">
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

    <!-- Profile Hero Card -->
    <div class="bg-white rounded-2xl border border-secondary-200 shadow-sm p-6 relative overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="w-24 h-24 rounded-2xl bg-primary-50 border-2 border-primary-200 shadow-md flex items-center justify-center overflow-hidden shrink-0">
                    <?php if (!empty($employee['photo_path'])): ?>
                        <img src="<?= (strpos($employee['photo_path'], 'http') === 0) ? $employee['photo_path'] : ($base . '/' . ltrim($employee['photo_path'], '/')) ?>" alt="<?= htmlspecialchars($employee['name']) ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <span class="text-3xl font-black text-primary-700 uppercase"><?= mb_substr($employee['name'], 0, 1) ?></span>
                    <?php endif; ?>
                </div>

                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-2xl font-black text-secondary-900"><?= htmlspecialchars($employee['name']) ?></h2>
                        <span class="px-2 py-0.5 bg-secondary-100 text-secondary-700 rounded-md font-mono text-xs font-bold"><?= htmlspecialchars($employee['emp_code']) ?></span>
                        
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
                        $st = $employee['status'] ?? 'active';
                        ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border <?= $statusBadges[$st] ?? '' ?>">
                            <?= $statusLabels[$st] ?? ucfirst($st) ?>
                        </span>
                    </div>

                    <p class="text-sm font-medium text-secondary-700">
                        <?= htmlspecialchars($employee['designation_title'] ?? 'পদবী নির্ধারিত নেই') ?> &bull; 
                        <span class="text-secondary-500"><?= htmlspecialchars($employee['department_name'] ?? 'ডিপার্টমেন্ট নির্ধারিত নেই') ?></span>
                    </p>

                    <div class="flex flex-wrap items-center gap-4 text-xs text-secondary-500 pt-1">
                        <span class="flex items-center gap-1">
                            <ion-icon name="call-outline" class="text-secondary-400"></ion-icon>
                            <?= htmlspecialchars($employee['phone']) ?>
                        </span>
                        <?php if (!empty($employee['email'])): ?>
                            <span class="flex items-center gap-1">
                                <ion-icon name="mail-outline" class="text-secondary-400"></ion-icon>
                                <?= htmlspecialchars($employee['email']) ?>
                            </span>
                        <?php endif; ?>
                        <span class="flex items-center gap-1">
                            <ion-icon name="calendar-outline" class="text-secondary-400"></ion-icon>
                            যোগদান: <?= date('d M, Y', strtotime($employee['joining_date'])) ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2 self-start md:self-center">
                <?php if (empty($employee['user_id'])): ?>
                    <button type="button" onclick="openCreateUserModal()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-medium flex items-center gap-1.5 transition-colors shadow-sm" title="এই কর্মচারীর জন্য একটি অ্যাডমিন লগইন একাউন্ট তৈরি করুন">
                        <ion-icon name="key-outline" class="text-lg"></ion-icon>
                        <span>সিস্টেম লগইন তৈরি</span>
                    </button>
                <?php endif; ?>
                <a href="<?= $base ?>/admin/hr/employees/edit?id=<?= $employee['id'] ?>" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-sm font-medium flex items-center gap-1.5 transition-colors shadow-sm">
                    <ion-icon name="create-outline" class="text-lg"></ion-icon>
                    <span>সম্পাদনা</span>
                </a>
                <a href="<?= $base ?>/admin/hr/leaves" class="px-4 py-2 bg-secondary-100 hover:bg-secondary-200 text-secondary-700 rounded-xl text-sm font-medium flex items-center gap-1.5 transition-colors">
                    <ion-icon name="airplane-outline" class="text-lg"></ion-icon>
                    <span>ছুটির আবেদন</span>
                </a>
                <a href="<?= $base ?>/admin/hr/employees" class="px-3.5 py-2 border border-secondary-300 text-secondary-600 hover:bg-secondary-50 rounded-xl text-sm transition-colors">
                    <ion-icon name="arrow-back-outline"></ion-icon>
                </a>
            </div>
        </div>
    </div>

    <!-- Monthly Attendance Summary KPI Bar -->
    <div class="bg-white rounded-2xl border border-secondary-200 shadow-sm p-5">
        <div class="flex items-center justify-between mb-3 border-b border-secondary-100 pb-2">
            <h3 class="text-sm font-bold text-secondary-800 flex items-center gap-2">
                <ion-icon name="calendar" class="text-primary-600"></ion-icon>
                চলতি মাসের হাজিরা সারসংক্ষেপ (<?= date('F Y') ?>)
            </h3>
            <a href="<?= $base ?>/admin/hr/attendance" class="text-xs text-primary-600 hover:underline font-medium">দৈনিক হাজিরা দিন &rarr;</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3">
            <div class="p-3 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-100 text-center">
                <p class="text-xs font-semibold">উপস্থিত (Present)</p>
                <p class="text-2xl font-black mt-1"><?= intval($attendanceStats['present_count'] ?? 0) ?></p>
            </div>
            <div class="p-3 rounded-xl bg-amber-50 text-amber-800 border border-amber-100 text-center">
                <p class="text-xs font-semibold">দেরি (Late)</p>
                <p class="text-2xl font-black mt-1"><?= intval($attendanceStats['late_count'] ?? 0) ?></p>
            </div>
            <div class="p-3 rounded-xl bg-blue-50 text-blue-800 border border-blue-100 text-center">
                <p class="text-xs font-semibold">অর্ধ দিবস (Half Day)</p>
                <p class="text-2xl font-black mt-1"><?= intval($attendanceStats['half_day_count'] ?? 0) ?></p>
            </div>
            <div class="p-3 rounded-xl bg-red-50 text-red-800 border border-red-100 text-center">
                <p class="text-xs font-semibold">অনুপস্থিত (Absent)</p>
                <p class="text-2xl font-black mt-1"><?= intval($attendanceStats['absent_count'] ?? 0) ?></p>
            </div>
            <div class="p-3 rounded-xl bg-purple-50 text-purple-800 border border-purple-100 text-center">
                <p class="text-xs font-semibold">ছুটি (Leave)</p>
                <p class="text-2xl font-black mt-1"><?= intval($attendanceStats['leave_count'] ?? 0) ?></p>
            </div>
            <div class="p-3 rounded-xl bg-secondary-100 text-secondary-800 border border-secondary-200 text-center">
                <p class="text-xs font-semibold">মোট লগ দিন</p>
                <p class="text-2xl font-black mt-1"><?= intval($attendanceStats['total_logged'] ?? 0) ?></p>
            </div>
        </div>
    </div>

    <!-- 2 Column Details Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Salary & Payment Details -->
        <div class="space-y-6">
            <!-- Salary Card -->
            <div class="bg-white rounded-2xl border border-secondary-200 shadow-sm p-5 space-y-3">
                <h3 class="text-base font-bold text-secondary-900 border-b border-secondary-100 pb-2 flex items-center gap-2">
                    <ion-icon name="cash-outline" class="text-emerald-600 text-xl"></ion-icon>
                    বেতন কাঠামো (Salary Structure)
                </h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between py-1 border-b border-secondary-50">
                        <span class="text-secondary-600">মূল বেতন (Basic)</span>
                        <span class="font-semibold text-secondary-900">৳<?= number_format($employee['basic_salary'], 2) ?></span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-secondary-50">
                        <span class="text-secondary-600">বাড়ি ভাড়া</span>
                        <span class="text-secondary-800">৳<?= number_format($employee['house_rent'] ?? 0, 2) ?></span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-secondary-50">
                        <span class="text-secondary-600">চিকিৎসা ভাতা</span>
                        <span class="text-secondary-800">৳<?= number_format($employee['medical_allowance'] ?? 0, 2) ?></span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-secondary-50">
                        <span class="text-secondary-600">অন্যান্য ভাতা</span>
                        <span class="text-secondary-800">৳<?= number_format($employee['other_allowance'] ?? 0, 2) ?></span>
                    </div>
                    <?php 
                    $gross = floatval($employee['basic_salary']) + floatval($employee['house_rent']) + floatval($employee['medical_allowance']) + floatval($employee['other_allowance']);
                    ?>
                    <div class="flex justify-between py-2 border-t border-secondary-200 font-bold">
                        <span class="text-secondary-900">মোট ধার্যকৃত বেতন</span>
                        <span class="text-emerald-600 text-base">৳<?= number_format($gross, 2) ?></span>
                    </div>
                </div>
            </div>

            <!-- Banking Card -->
            <div class="bg-white rounded-2xl border border-secondary-200 shadow-sm p-5 space-y-3">
                <h3 class="text-base font-bold text-secondary-900 border-b border-secondary-100 pb-2 flex items-center gap-2">
                    <ion-icon name="card-outline" class="text-blue-600 text-xl"></ion-icon>
                    ব্যাংকিং ও লেনদেন তথ্য
                </h3>
                <div class="space-y-2 text-xs">
                    <div class="p-2.5 rounded-xl bg-secondary-50 border border-secondary-200">
                        <p class="font-bold text-secondary-800">ব্যাংক একাউন্ট</p>
                        <p class="text-secondary-600 mt-0.5">ব্যাংক: <?= htmlspecialchars($employee['bank_name'] ?: 'নির্ধারিত নেই') ?></p>
                        <p class="text-secondary-600 font-mono">হিসাব: <?= htmlspecialchars($employee['bank_account_no'] ?: 'নির্ধারিত নেই') ?></p>
                    </div>
                    <div class="p-2.5 rounded-xl bg-secondary-50 border border-secondary-200">
                        <p class="font-bold text-secondary-800">মোবাইল ব্যাংকিং (MFS)</p>
                        <p class="text-secondary-600 mt-0.5">মাধ্যম: <?= htmlspecialchars($employee['mobile_banking_type'] ?: 'নির্ধারিত নেই') ?></p>
                        <p class="text-secondary-600 font-mono">নম্বর: <?= htmlspecialchars($employee['mobile_banking_number'] ?: 'নির্ধারিত নেই') ?></p>
                    </div>
                </div>
            </div>

            <!-- Personal Info Card -->
            <div class="bg-white rounded-2xl border border-secondary-200 shadow-sm p-5 space-y-3">
                <h3 class="text-base font-bold text-secondary-900 border-b border-secondary-100 pb-2 flex items-center gap-2">
                    <ion-icon name="information-circle-outline" class="text-purple-600 text-xl"></ion-icon>
                    অন্যান্য বিবরণ
                </h3>
                <div class="space-y-2 text-xs text-secondary-700">
                    <p><strong class="text-secondary-900">এনআইডি নম্বর:</strong> <?= htmlspecialchars($employee['nid'] ?: 'N/A') ?></p>
                    <p><strong class="text-secondary-900">জরুরি যোগাযোগ:</strong> <?= htmlspecialchars($employee['emergency_contact'] ?: 'N/A') ?></p>
                    <p><strong class="text-secondary-900">ঠিকানা:</strong> <?= nl2br(htmlspecialchars($employee['address'] ?: 'N/A')) ?></p>
                    <div class="flex items-center justify-between pt-1 border-t border-secondary-100">
                        <div>
                            <strong class="text-secondary-900">সিস্টেম লগইন:</strong>
                            <?php if (!empty($employee['linked_username'])): ?>
                                <span class="inline-flex items-center gap-1 font-bold text-primary-700 bg-primary-50 px-2 py-0.5 rounded border border-primary-200">
                                    <ion-icon name="person-circle"></ion-icon>
                                    @<?= htmlspecialchars($employee['linked_username']) ?> (<?= htmlspecialchars($employee['linked_role'] ?? '') ?>)
                                </span>
                            <?php else: ?>
                                <span class="text-secondary-400 italic">কোনো একাউন্ট লিংক নেই</span>
                            <?php endif; ?>
                        </div>
                        <?php if (empty($employee['user_id'])): ?>
                            <button type="button" onclick="openCreateUserModal()" class="text-xs font-bold text-emerald-700 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-2.5 py-1 rounded-lg transition-colors">
                                + তৈরি করুন
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Recent Attendances, Payrolls, and Leaves -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Recent Attendances (Last 15 days) -->
            <div class="bg-white rounded-2xl border border-secondary-200 shadow-sm p-5 space-y-3">
                <div class="flex items-center justify-between border-b border-secondary-100 pb-2">
                    <h3 class="text-base font-bold text-secondary-900 flex items-center gap-2">
                        <ion-icon name="time-outline" class="text-primary-600 text-xl"></ion-icon>
                        সাম্প্রতিক হাজিরা লগ (Recent Attendance)
                    </h3>
                    <a href="<?= $base ?>/admin/hr/attendance/report" class="text-xs text-primary-600 hover:underline">সম্পূর্ণ রিপোর্ট &rarr;</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-secondary-50 text-secondary-600 uppercase border-b border-secondary-200">
                            <tr>
                                <th class="py-2.5 px-3">তারিখ</th>
                                <th class="py-2.5 px-3 text-center">স্ট্যাটাস</th>
                                <th class="py-2.5 px-3">প্রবেশ (In)</th>
                                <th class="py-2.5 px-3">প্রস্থান (Out)</th>
                                <th class="py-2.5 px-3">মন্তব্য</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-secondary-100">
                            <?php if (empty($recentAttendance)): ?>
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-secondary-400">কোনো হাজিরা রেকর্ড পাওয়া যায়নি।</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentAttendance as $att): ?>
                                    <tr class="hover:bg-secondary-50/50">
                                        <td class="py-2.5 px-3 font-semibold text-secondary-800"><?= date('d M, Y (D)', strtotime($att['date'])) ?></td>
                                        <td class="py-2.5 px-3 text-center">
                                            <?php
                                            $attBadges = [
                                                'present' => 'bg-emerald-100 text-emerald-800',
                                                'late' => 'bg-amber-100 text-amber-800',
                                                'half_day' => 'bg-blue-100 text-blue-800',
                                                'absent' => 'bg-red-100 text-red-800',
                                                'leave' => 'bg-purple-100 text-purple-800',
                                                'holiday' => 'bg-secondary-100 text-secondary-700'
                                            ];
                                            $st = $att['status'] ?? 'present';
                                            ?>
                                            <span class="inline-flex px-2 py-0.5 rounded-full font-medium capitalize <?= $attBadges[$st] ?? '' ?>">
                                                <?= $st ?>
                                            </span>
                                        </td>
                                        <td class="py-2.5 px-3 font-mono text-secondary-700"><?= !empty($att['in_time']) ? date('h:i A', strtotime($att['in_time'])) : '-' ?></td>
                                        <td class="py-2.5 px-3 font-mono text-secondary-700"><?= !empty($att['out_time']) ? date('h:i A', strtotime($att['out_time'])) : '-' ?></td>
                                        <td class="py-2.5 px-3 text-secondary-500"><?= htmlspecialchars($att['note'] ?: '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Payroll Records -->
            <div class="bg-white rounded-2xl border border-secondary-200 shadow-sm p-5 space-y-3">
                <div class="flex items-center justify-between border-b border-secondary-100 pb-2">
                    <h3 class="text-base font-bold text-secondary-900 flex items-center gap-2">
                        <ion-icon name="receipt-outline" class="text-emerald-600 text-xl"></ion-icon>
                        বেতন রশিদ হিস্ট্রি (Payroll History)
                    </h3>
                    <a href="<?= $base ?>/admin/payroll" class="text-xs text-primary-600 hover:underline">পেরোল শিট &rarr;</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-secondary-50 text-secondary-600 uppercase border-b border-secondary-200">
                            <tr>
                                <th class="py-2.5 px-3">মাস (Month)</th>
                                <th class="py-2.5 px-3">উপস্থিত দিন</th>
                                <th class="py-2.5 px-3 text-right">মূল বেতন</th>
                                <th class="py-2.5 px-3 text-right">কর্তন</th>
                                <th class="py-2.5 px-3 text-right font-bold">প্রদেয় নেট বেতন</th>
                                <th class="py-2.5 px-3 text-center">স্ট্যাটাস</th>
                                <th class="py-2.5 px-3 text-right">পেস্লিপ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-secondary-100">
                            <?php if (empty($recentPayrolls)): ?>
                                <tr>
                                    <td colspan="7" class="py-6 text-center text-secondary-400">কোনো পেরোল রেকর্ড পাওয়া যায়নি।</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentPayrolls as $pay): ?>
                                    <tr class="hover:bg-secondary-50/50">
                                        <td class="py-2.5 px-3 font-semibold text-secondary-900"><?= date('F Y', strtotime($pay['salary_month'] . '-01')) ?></td>
                                        <td class="py-2.5 px-3"><?= $pay['present_days'] ?> / <?= $pay['working_days'] ?> দিন</td>
                                        <td class="py-2.5 px-3 text-right">৳<?= number_format($pay['basic_salary'], 2) ?></td>
                                        <td class="py-2.5 px-3 text-right text-red-600">-৳<?= number_format($pay['deductions'] + $pay['advance_salary_deduction'], 2) ?></td>
                                        <td class="py-2.5 px-3 text-right font-bold text-emerald-600">৳<?= number_format($pay['net_salary'], 2) ?></td>
                                        <td class="py-2.5 px-3 text-center">
                                            <?php if ($pay['status'] === 'paid'): ?>
                                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">পরিশোধিত</span>
                                            <?php else: ?>
                                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">বকেয়া</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-2.5 px-3 text-right">
                                            <a href="<?= $base ?>/admin/payroll/payslip?id=<?= $pay['id'] ?>" target="_blank" class="px-2 py-1 bg-secondary-100 hover:bg-primary-50 hover:text-primary-600 text-secondary-700 rounded-lg text-xs font-medium transition-colors">
                                                ভাউচার &rarr;
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Leave History -->
            <div class="bg-white rounded-2xl border border-secondary-200 shadow-sm p-5 space-y-3">
                <div class="flex items-center justify-between border-b border-secondary-100 pb-2">
                    <h3 class="text-base font-bold text-secondary-900 flex items-center gap-2">
                        <ion-icon name="airplane-outline" class="text-amber-600 text-xl"></ion-icon>
                        ছুটির রেকর্ড (Leave History)
                    </h3>
                    <a href="<?= $base ?>/admin/hr/leaves" class="text-xs text-primary-600 hover:underline">ছুটি ব্যবস্থাপনা &rarr;</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-secondary-50 text-secondary-600 uppercase border-b border-secondary-200">
                            <tr>
                                <th class="py-2.5 px-3">ছুটির ধরন</th>
                                <th class="py-2.5 px-3">শুরু - শেষ তারিখ</th>
                                <th class="py-2.5 px-3 text-center">মোট দিন</th>
                                <th class="py-2.5 px-3">কারণ</th>
                                <th class="py-2.5 px-3 text-center">স্ট্যাটাস</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-secondary-100">
                            <?php if (empty($leaveHistory)): ?>
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-secondary-400">কোনো ছুটির আবেদন পাওয়া যায়নি।</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($leaveHistory as $lv): ?>
                                    <tr class="hover:bg-secondary-50/50">
                                        <td class="py-2.5 px-3 font-semibold capitalize"><?= htmlspecialchars($lv['leave_type']) ?> Leave</td>
                                        <td class="py-2.5 px-3 text-secondary-700"><?= date('d M, Y', strtotime($lv['start_date'])) ?> হতে <?= date('d M, Y', strtotime($lv['end_date'])) ?></td>
                                        <td class="py-2.5 px-3 text-center font-bold"><?= $lv['total_days'] ?> দিন</td>
                                        <td class="py-2.5 px-3 text-secondary-600"><?= htmlspecialchars($lv['reason'] ?: '-') ?></td>
                                        <td class="py-2.5 px-3 text-center">
                                            <?php
                                            $lvBadges = [
                                                'approved' => 'bg-emerald-100 text-emerald-800',
                                                'pending' => 'bg-amber-100 text-amber-800',
                                                'rejected' => 'bg-red-100 text-red-800'
                                            ];
                                            $st = $lv['status'] ?? 'pending';
                                            ?>
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold capitalize <?= $lvBadges[$st] ?? '' ?>">
                                                <?= $st ?>
                                            </span>
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
</div>

<!-- Modal: Quick Create User Account for Employee -->
<div id="createUserModal" class="fixed inset-0 bg-secondary-900/50 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-secondary-200">
        <div class="flex items-center justify-between pb-3 border-b border-secondary-100">
            <h3 class="text-base font-bold text-secondary-900 flex items-center gap-2">
                <ion-icon name="key" class="text-emerald-600 text-xl"></ion-icon>
                <span>সিস্টেম লগইন একাউন্ট তৈরি করুন</span>
            </h3>
            <button onclick="closeCreateUserModal()" class="text-secondary-400 hover:text-secondary-700">
                <ion-icon name="close-circle-outline" class="text-2xl"></ion-icon>
            </button>
        </div>

        <form method="POST" action="<?= $base ?>/admin/hr/employees/create-user" class="mt-4 space-y-4">
            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
            <input type="hidden" name="employee_id" value="<?= $employee['id'] ?>">

            <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl">
                <p class="text-xs text-emerald-800">
                    কর্মচারী: <strong class="text-emerald-900 font-bold"><?= htmlspecialchars($employee['name']) ?></strong> (<?= htmlspecialchars($employee['emp_code']) ?>)-এর জন্য সরাসরি অ্যাডমিন লগইন একাউন্ট তৈরি ও লিংক হবে।
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-secondary-700 mb-1">ইউজারনেম (Login ID) <span class="text-red-500">*</span></label>
                    <?php
                    $defaultUsername = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $employee['name']));
                    if (empty($defaultUsername)) $defaultUsername = 'emp_' . $employee['id'];
                    else $defaultUsername = substr($defaultUsername, 0, 10);
                    ?>
                    <input type="text" name="username" value="<?= htmlspecialchars($defaultUsername) ?>" required class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3 py-2 text-xs text-secondary-800 focus:ring-2 focus:ring-emerald-500 font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-secondary-700 mb-1">লগইন পাসওয়ার্ড <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required minlength="6" placeholder="কমপক্ষে ৬ অক্ষর" class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3 py-2 text-xs text-secondary-800 focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-secondary-700 mb-1">সিস্টেম রোল (Role) <span class="text-red-500">*</span></label>
                <select name="role" id="modalRoleSelect" onchange="modalRoleChange(this.value)" required class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3 py-2 text-xs text-secondary-800 focus:ring-2 focus:ring-emerald-500">
                    <?php foreach (($roles ?? []) as $rKey => $rLabel): ?>
                        <option value="<?= $rKey ?>" <?= $rKey === 'staff' ? 'selected' : '' ?>><?= $rLabel ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-bold text-secondary-800 uppercase tracking-wider">মডিউল পারমিশন (Permissions)</label>
                    <div class="flex gap-2">
                        <button type="button" onclick="modalSetAllPerms(true)" class="text-[11px] text-emerald-600 hover:underline">সব নির্বাচন</button>
                        <span class="text-secondary-300 text-xs">|</span>
                        <button type="button" onclick="modalSetAllPerms(false)" class="text-[11px] text-secondary-500 hover:underline">সব বাতিল</button>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-44 overflow-y-auto p-2.5 border border-secondary-200 rounded-xl bg-secondary-50/50">
                    <?php foreach (($allPermissions ?? []) as $groupName => $modules): ?>
                        <div class="col-span-full pt-1 text-[10px] font-bold uppercase tracking-wider text-secondary-400"><?= htmlspecialchars($groupName) ?></div>
                        <?php foreach ($modules as $permKey => $perm): ?>
                            <label class="flex items-center gap-2 p-1.5 rounded-lg bg-white border border-secondary-200 text-xs cursor-pointer hover:bg-emerald-50">
                                <input type="checkbox" name="permissions[]" value="<?= $permKey ?>" class="rounded text-emerald-600 focus:ring-emerald-500 modal-perm-cb">
                                <span class="font-medium text-secondary-800 text-xs"><?= htmlspecialchars($perm['label_bn']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-secondary-100">
                <button type="button" onclick="closeCreateUserModal()" class="px-4 py-2 border border-secondary-300 text-secondary-700 rounded-xl text-xs font-medium hover:bg-secondary-50">বাতিল</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-colors shadow-xs flex items-center gap-1.5">
                    <ion-icon name="checkmark-circle-outline" class="text-sm"></ion-icon>
                    <span>লগইন একাউন্ট সংরক্ষণ করুন</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const modalRolePresets = {
    admin: ['*'],
    manager: ['dashboard', 'products', 'categories_brands', 'orders', 'dispatch', 'delivery_men', 'customers', 'vendors_purchases', 'locations', 'hr', 'payroll', 'reports'],
    accountant: ['dashboard', 'vendors_purchases', 'payroll', 'reports'],
    agent: ['dashboard', 'orders', 'customers'],
    delivery_man: ['orders'],
    staff: ['dashboard', 'products', 'orders']
};

function openCreateUserModal() {
    document.getElementById('createUserModal').classList.remove('hidden');
    modalRoleChange(document.getElementById('modalRoleSelect').value);
}

function closeCreateUserModal() {
    document.getElementById('createUserModal').classList.add('hidden');
}

function modalRoleChange(role) {
    const preset = modalRolePresets[role] || [];
    const isWildcard = preset.includes('*');
    document.querySelectorAll('.modal-perm-cb').forEach(cb => {
        cb.checked = isWildcard || preset.includes(cb.value);
    });
}

function modalSetAllPerms(check) {
    document.querySelectorAll('.modal-perm-cb').forEach(cb => {
        cb.checked = check;
    });
}
</script>
