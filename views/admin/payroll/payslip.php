<?php 
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
$storeName = $settings['store_name'] ?? 'Sodai Dorkar';
$storePhone = $settings['store_phone'] ?? '+8801700-000000';
$storeEmail = $settings['store_email'] ?? 'support@sodaidorkar.com';
$storeAddress = $settings['store_address'] ?? 'Dhaka, Bangladesh';
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>বেতন রশিদ (Salary Slip) - <?= htmlspecialchars($payroll['emp_code']) ?> - <?= htmlspecialchars($payroll['salary_month']) ?></title>
    <link href="/sodai-dorkar/public/css/output.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        body, button, input, select, textarea { font-family: 'Hind Siliguri', 'Outfit', sans-serif; }
        @media print {
            body { background: white !important; }
            .no-print { display: none !important; }
            .payslip-container { box-shadow: none !important; border: none !important; max-width: 100% !important; margin: 0 !important; }
        }
    </style>
</head>
<body class="bg-secondary-100 font-sans text-secondary-900 py-8 px-4">

    <!-- Top Toolbar (Hidden on Print) -->
    <div class="max-w-3xl mx-auto mb-6 flex items-center justify-between no-print">
        <a href="<?= $base ?>/admin/payroll?month=<?= $payroll['salary_month'] ?>" class="px-4 py-2 bg-white text-secondary-700 hover:bg-secondary-50 rounded-xl text-sm font-medium border border-secondary-200 flex items-center gap-1.5 transition-colors shadow-sm">
            <ion-icon name="arrow-back-outline"></ion-icon>
            <span>পেরোল তালিকায় ফিরুন</span>
        </a>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-sm font-bold flex items-center gap-2 shadow-md transition-colors">
                <ion-icon name="print-outline" class="text-lg"></ion-icon>
                <span>প্রিন্ট করুন (Print Slip)</span>
            </button>
        </div>
    </div>

    <!-- Payslip Voucher Card -->
    <div class="payslip-container max-w-3xl mx-auto bg-white rounded-2xl border border-secondary-200 shadow-xl p-8 space-y-6">
        <!-- Header -->
        <div class="border-b-2 border-primary-600 pb-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-black text-primary-600 tracking-tight"><?= htmlspecialchars($storeName) ?></h1>
                    <p class="text-xs text-secondary-500 mt-1"><?= htmlspecialchars($storeAddress) ?></p>
                    <p class="text-xs text-secondary-500">ফোন: <?= htmlspecialchars($storePhone) ?> &bull; ইমেইল: <?= htmlspecialchars($storeEmail) ?></p>
                </div>
                <div class="text-left sm:text-right">
                    <span class="inline-block px-3 py-1 bg-primary-50 text-primary-700 rounded-lg text-xs font-bold uppercase tracking-wider border border-primary-100 mb-1">
                        বেতন রশিদ (Salary Payslip)
                    </span>
                    <p class="text-lg font-bold text-secondary-900"><?= date('F Y', strtotime($payroll['salary_month'] . '-01')) ?></p>
                    <p class="text-xs text-secondary-400">রশিদ নং: PAY-<?= str_pad($payroll['id'], 5, '0', STR_PAD_LEFT) ?></p>
                </div>
            </div>
        </div>

        <!-- Employee Particulars Grid -->
        <div class="bg-secondary-50 rounded-xl p-4 border border-secondary-200 text-xs">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div>
                    <span class="text-secondary-400 font-medium">কর্মচারীর নাম:</span>
                    <p class="font-bold text-secondary-900 text-sm mt-0.5"><?= htmlspecialchars($payroll['employee_name']) ?></p>
                </div>
                <div>
                    <span class="text-secondary-400 font-medium">কর্মচারী কোড:</span>
                    <p class="font-bold font-mono text-secondary-900 mt-0.5"><?= htmlspecialchars($payroll['emp_code']) ?></p>
                </div>
                <div>
                    <span class="text-secondary-400 font-medium">পদবী (Designation):</span>
                    <p class="font-bold text-secondary-900 mt-0.5"><?= htmlspecialchars($payroll['designation_title'] ?? 'N/A') ?></p>
                </div>
                <div>
                    <span class="text-secondary-400 font-medium">ডিপার্টমেন্ট:</span>
                    <p class="font-bold text-secondary-900 mt-0.5"><?= htmlspecialchars($payroll['department_name'] ?? 'N/A') ?></p>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-3 mt-3 border-t border-secondary-200/60">
                <div>
                    <span class="text-secondary-400 font-medium">মোবাইল নম্বর:</span>
                    <p class="font-medium text-secondary-800 mt-0.5"><?= htmlspecialchars($payroll['phone']) ?></p>
                </div>
                <div>
                    <span class="text-secondary-400 font-medium">মোট কর্মদিবস:</span>
                    <p class="font-bold text-secondary-800 mt-0.5"><?= $payroll['working_days'] ?> দিন</p>
                </div>
                <div>
                    <span class="text-secondary-400 font-medium">উপস্থিত দিন:</span>
                    <p class="font-bold text-emerald-700 mt-0.5"><?= $payroll['present_days'] ?> দিন</p>
                </div>
                <div>
                    <span class="text-secondary-400 font-medium">অনুপস্থিত দিন:</span>
                    <p class="font-bold text-red-600 mt-0.5"><?= $payroll['absent_days'] ?> দিন</p>
                </div>
            </div>
        </div>

        <!-- Earnings & Deductions Breakdown -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <!-- Left: Earnings -->
            <div class="border border-secondary-200 rounded-xl overflow-hidden">
                <div class="bg-secondary-50 px-4 py-2.5 font-bold text-xs text-secondary-800 border-b border-secondary-200 uppercase tracking-wider">
                    ধার্যকৃত আয় (Earnings)
                </div>
                <div class="p-4 space-y-2 text-xs">
                    <div class="flex justify-between py-1 border-b border-secondary-100">
                        <span class="text-secondary-600">মূল বেতন (Basic)</span>
                        <span class="font-bold text-secondary-900">৳<?= number_format($payroll['basic_salary'], 2) ?></span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-secondary-100">
                        <span class="text-secondary-600">বাড়ি ভাড়া ভাতা</span>
                        <span class="text-secondary-800">৳<?= number_format($payroll['house_rent'] ?? 0, 2) ?></span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-secondary-100">
                        <span class="text-secondary-600">চিকিৎসা ভাতা</span>
                        <span class="text-secondary-800">৳<?= number_format($payroll['medical_allowance'] ?? 0, 2) ?></span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-secondary-100">
                        <span class="text-secondary-600">অন্যান্য সুযোগ-সুবিধা</span>
                        <span class="text-secondary-800">৳<?= number_format($payroll['other_allowance'] ?? 0, 2) ?></span>
                    </div>
                    <?php if (floatval($payroll['bonus'] ?? 0) > 0): ?>
                        <div class="flex justify-between py-1 border-b border-secondary-100 text-emerald-600">
                            <span>বোনাস / ইনসেনটিভ</span>
                            <span class="font-bold">৳<?= number_format($payroll['bonus'], 2) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php 
                    $totalGross = floatval($payroll['basic_salary'] ?? 0) + floatval($payroll['allowances'] ?? 0) + floatval($payroll['bonus'] ?? 0);
                    ?>
                    <div class="flex justify-between pt-2 font-bold text-sm text-secondary-900">
                        <span>মোট উপার্জিত বেতন:</span>
                        <span class="text-emerald-700">৳<?= number_format($totalGross, 2) ?></span>
                    </div>
                </div>
            </div>

            <!-- Right: Deductions -->
            <div class="border border-secondary-200 rounded-xl overflow-hidden">
                <div class="bg-secondary-50 px-4 py-2.5 font-bold text-xs text-secondary-800 border-b border-secondary-200 uppercase tracking-wider">
                    কর্তনসমূহ (Deductions)
                </div>
                <div class="p-4 space-y-2 text-xs">
                    <div class="flex justify-between py-1 border-b border-secondary-100">
                        <span class="text-secondary-600">অনুপস্থিতির জন্য কর্তন (<?= $payroll['absent_days'] ?> দিন)</span>
                        <span class="font-bold text-red-600">৳<?= number_format($payroll['deductions'], 2) ?></span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-secondary-100">
                        <span class="text-secondary-600">অগ্রিম বেতন / লোন কর্তন</span>
                        <span class="text-secondary-800">৳<?= number_format($payroll['advance_salary_deduction'] ?? 0, 2) ?></span>
                    </div>
                    <?php 
                    $totalDeductions = floatval($payroll['deductions']) + floatval($payroll['advance_salary_deduction'] ?? 0);
                    ?>
                    <div class="flex justify-between pt-2 font-bold text-sm text-secondary-900">
                        <span>মোট কর্তন:</span>
                        <span class="text-red-600">৳<?= number_format($totalDeductions, 2) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Net Payable Amount Highlight Banner -->
        <div class="bg-emerald-50 border-2 border-emerald-300 rounded-2xl p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <p class="text-xs font-bold text-emerald-800 uppercase tracking-wider">সর্বমোট প্রদেয় নেট বেতন (Net Salary Payable)</p>
                <p class="text-3xl font-black text-emerald-700 mt-1">৳<?= number_format($payroll['net_salary'], 2) ?></p>
            </div>
            <div class="text-left sm:text-right text-xs text-secondary-600 space-y-1">
                <p>স্ট্যাটাস: 
                    <?php if ($payroll['status'] === 'paid'): ?>
                        <span class="font-bold text-emerald-700 bg-emerald-200 px-2 py-0.5 rounded-md">পরিশোধিত (PAID)</span>
                    <?php else: ?>
                        <span class="font-bold text-amber-700 bg-amber-200 px-2 py-0.5 rounded-md">বকেয়া (UNPAID)</span>
                    <?php endif; ?>
                </p>
                <p>পরিশোধ মাধ্যম: <strong class="uppercase text-secondary-800"><?= $payroll['payment_method'] ?></strong></p>
                <?php if (!empty($payroll['payment_date'])): ?>
                    <p>পরিশোধ তারিখ: <strong class="text-secondary-800"><?= date('d M, Y', strtotime($payroll['payment_date'])) ?></strong></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Payment Channel Details -->
        <?php if (!empty($payroll['bank_name']) || !empty($payroll['mobile_banking_number'])): ?>
            <div class="text-xs text-secondary-600 border border-secondary-200 rounded-xl p-3 bg-secondary-50">
                <span class="font-bold text-secondary-800">অ্যাকাউন্ট তথ্য: </span>
                <?php if (!empty($payroll['bank_name'])): ?>
                    ব্যাংক: <?= htmlspecialchars($payroll['bank_name']) ?> (A/C: <?= htmlspecialchars($payroll['bank_account_no'] ?? '') ?>) &bull;
                <?php endif; ?>
                <?php if (!empty($payroll['mobile_banking_number'])): ?>
                    মোবাইল ব্যাংকিং: <?= htmlspecialchars($payroll['mobile_banking_type'] ?? 'MFS') ?> (<?= htmlspecialchars($payroll['mobile_banking_number']) ?>)
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Signatures Section -->
        <div class="pt-16 grid grid-cols-3 gap-4 text-center text-xs text-secondary-700">
            <div>
                <div class="border-t border-secondary-400 pt-2 font-semibold">কর্মচারীর স্বাক্ষর</div>
            </div>
            <div>
                <div class="border-t border-secondary-400 pt-2 font-semibold">হিসাবরক্ষকের স্বাক্ষর</div>
            </div>
            <div>
                <div class="border-t border-secondary-400 pt-2 font-semibold">কর্তৃপক্ষের স্বাক্ষর</div>
            </div>
        </div>

        <div class="text-center text-[10px] text-secondary-400 pt-4 border-t border-secondary-100">
            এটি একটি কম্পিউটার জেনারেটেড বেতন রশিদ &bull; জেনারেট তারিখ: <?= date('d M, Y h:i A') ?>
        </div>
    </div>

</body>
</html>
