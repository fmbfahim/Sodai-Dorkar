<?php 
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-secondary-200 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-secondary-900 flex items-center gap-2">
                <ion-icon name="wallet" class="text-primary-600 text-2xl"></ion-icon>
                মাসিক বেতন ও পেরোল শিট (Monthly Payroll)
            </h2>
            <p class="text-secondary-500 text-sm mt-1">কর্মচারীদের উপস্থিতি ও ছুটির ভিত্তিতে স্বয়ংক্রিয় বেতন হিসাব, পেস্লিপ ও পরিশোধ ব্যবস্থাপনা</p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="openGenerateModal()" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-xl text-sm flex items-center gap-2 transition-all shadow-sm">
                <ion-icon name="flash" class="text-lg"></ion-icon>
                <span>স্বয়ংক্রিয় বেতন শিট তৈরি করুন</span>
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

    <!-- Month Selector Bar & Summary -->
    <div class="bg-white p-4 rounded-2xl border border-secondary-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <form method="GET" action="<?= $base ?>/admin/payroll" class="flex flex-wrap items-center gap-3">
            <label class="text-xs font-semibold text-secondary-600 uppercase tracking-wider">মাস নির্বাচন করুন:</label>
            <input type="month" name="month" value="<?= htmlspecialchars($currentMonth) ?>" onchange="this.form.submit()" class="bg-secondary-50 border border-secondary-300 rounded-xl px-3 py-1.5 text-sm font-bold text-secondary-800 focus:ring-2 focus:ring-primary-500">
            <button type="submit" class="px-4 py-1.5 bg-secondary-100 hover:bg-secondary-200 text-secondary-700 text-xs font-medium rounded-xl">শিট দেখুন</button>
        </form>
        <div class="text-sm text-secondary-600">
            চলতি শিট মাস: <strong class="text-secondary-900 font-bold"><?= date('F Y', strtotime($currentMonth . '-01')) ?></strong>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-secondary-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl">
                <ion-icon name="cash"></ion-icon>
            </div>
            <div>
                <p class="text-xs font-semibold text-secondary-400 uppercase tracking-wider">মোট নিট বেতন</p>
                <h3 class="text-2xl font-black text-secondary-900">৳<?= number_format($summary['total_amount'] ?? 0, 2) ?></h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-secondary-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl">
                <ion-icon name="checkmark-done-circle"></ion-icon>
            </div>
            <div>
                <p class="text-xs font-semibold text-secondary-400 uppercase tracking-wider">পরিশোধিত (Paid)</p>
                <h3 class="text-2xl font-black text-emerald-600">৳<?= number_format($summary['paid_amount'] ?? 0, 2) ?></h3>
                <span class="text-xs text-secondary-500">(<?= intval($summary['paid_count'] ?? 0) ?> জন কর্মী)</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-secondary-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl">
                <ion-icon name="hourglass"></ion-icon>
            </div>
            <div>
                <p class="text-xs font-semibold text-secondary-400 uppercase tracking-wider">বকেয়া বেতন (Pending)</p>
                <h3 class="text-2xl font-black text-amber-600">৳<?= number_format($summary['pending_amount'] ?? 0, 2) ?></h3>
                <span class="text-xs text-secondary-500">(<?= intval($summary['pending_count'] ?? 0) ?> জন কর্মী)</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-secondary-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl">
                <ion-icon name="people"></ion-icon>
            </div>
            <div>
                <p class="text-xs font-semibold text-secondary-400 uppercase tracking-wider">মোট বেতনভুক্ত কর্মী</p>
                <h3 class="text-2xl font-black text-blue-600"><?= intval($summary['employee_count'] ?? 0) ?> জন</h3>
            </div>
        </div>
    </div>

    <!-- Payroll Table -->
    <div class="bg-white rounded-2xl border border-secondary-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-secondary-50 text-secondary-600 uppercase text-xs border-b border-secondary-200">
                    <tr>
                        <th class="py-3.5 px-4 font-semibold">কর্মচারী</th>
                        <th class="py-3.5 px-4 font-semibold text-center">উপস্থিত দিন</th>
                        <th class="py-3.5 px-4 font-semibold text-right">মূল বেতন</th>
                        <th class="py-3.5 px-4 font-semibold text-right">মোট ভাতা</th>
                        <th class="py-3.5 px-4 font-semibold text-right">অনুপস্থিতি কর্তন</th>
                        <th class="py-3.5 px-4 font-semibold text-right">প্রদেয় নেট বেতন</th>
                        <th class="py-3.5 px-4 font-semibold text-center">স্ট্যাটাস</th>
                        <th class="py-3.5 px-4 font-semibold text-right">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-secondary-100">
                    <?php if (empty($payrolls)): ?>
                        <tr>
                            <td colspan="8" class="py-12 text-center text-secondary-400">
                                <ion-icon name="cash-outline" class="text-4xl text-secondary-300 mb-2"></ion-icon>
                                <p>এই মাসের জন্য এখনো কোনো বেতন শিট তৈরি করা হয়নি।</p>
                                <button onclick="openGenerateModal()" class="mt-3 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs font-bold rounded-xl transition-colors shadow-sm inline-flex items-center gap-1.5">
                                    <ion-icon name="flash"></ion-icon>
                                    এখনই বেতন শিট তৈরি করুন
                                </button>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($payrolls as $p): ?>
                            <tr class="hover:bg-secondary-50/50 transition-colors">
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-secondary-900"><?= htmlspecialchars($p['employee_name']) ?></div>
                                    <div class="text-xs text-secondary-500 font-mono"><?= htmlspecialchars($p['emp_code']) ?> &bull; <?= htmlspecialchars($p['designation_title'] ?? '') ?></div>
                                </td>
                                <td class="py-3.5 px-4 text-center text-xs">
                                    <span class="font-bold text-secondary-800"><?= $p['present_days'] ?></span> / <?= $p['working_days'] ?> দিন
                                    <?php if ($p['absent_days'] > 0): ?>
                                        <p class="text-[11px] text-red-500">(<?= $p['absent_days'] ?> দিন অনুপস্থিত)</p>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3.5 px-4 text-right font-medium text-secondary-800">
                                    ৳<?= number_format($p['basic_salary'], 2) ?>
                                </td>
                                <td class="py-3.5 px-4 text-right text-emerald-600 font-medium text-xs">
                                    +৳<?= number_format($p['allowances'], 2) ?>
                                </td>
                                <td class="py-3.5 px-4 text-right text-red-600 font-medium text-xs">
                                    -৳<?= number_format($p['deductions'] + $p['advance_salary_deduction'], 2) ?>
                                </td>
                                <td class="py-3.5 px-4 text-right font-black text-secondary-900 text-base">
                                    ৳<?= number_format($p['net_salary'], 2) ?>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <?php if ($p['status'] === 'paid'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            পরিশোধিত (<?= strtoupper($p['payment_method']) ?>)
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            বকেয়া (Pending)
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <!-- Payslip Button -->
                                        <a href="<?= $base ?>/admin/payroll/payslip?id=<?= $p['id'] ?>" target="_blank" class="px-2.5 py-1 bg-secondary-100 hover:bg-secondary-200 text-secondary-800 rounded-lg text-xs font-medium flex items-center gap-1 transition-colors" title="পেস্লিপ প্রিন্ট করুন">
                                            <ion-icon name="print-outline"></ion-icon>
                                            <span>পেস্লিপ</span>
                                        </a>

                                        <!-- Mark Paid Button -->
                                        <?php if ($p['status'] !== 'paid'): ?>
                                            <button type="button" onclick="openPaymentModal(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['employee_name'])) ?>', <?= $p['net_salary'] ?>)" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-medium transition-colors shadow-sm" title="পরিশোধ সম্পন্ন করুন">
                                                পরিশোধ
                                            </button>
                                        <?php endif; ?>

                                        <!-- Delete / Recalculate -->
                                        <form method="POST" action="<?= $base ?>/admin/payroll/delete" onsubmit="return confirm('এই বেতন রেকর্ড মুছে ফেলতে চান?')" class="inline">
                                            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                            <input type="hidden" name="month" value="<?= htmlspecialchars($currentMonth) ?>">
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

<!-- Modal: Generate Monthly Payroll -->
<div id="generateModal" class="fixed inset-0 bg-secondary-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-secondary-200">
        <div class="flex items-center justify-between pb-4 border-b border-secondary-100">
            <h3 class="text-lg font-bold text-secondary-900 flex items-center gap-2">
                <ion-icon name="flash" class="text-primary-600"></ion-icon>
                বেতন শিট জেনারেট করুন
            </h3>
            <button onclick="closeGenerateModal()" class="text-secondary-400 hover:text-secondary-700">
                <ion-icon name="close-circle-outline" class="text-2xl"></ion-icon>
            </button>
        </div>

        <form method="POST" action="<?= $base ?>/admin/payroll/generate" class="mt-4 space-y-4">
            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">

            <div>
                <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">কোন মাসের জন্য বেতন হিসাব করবেন?</label>
                <input type="month" name="month" value="<?= htmlspecialchars($currentMonth) ?>" required class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2 text-sm font-bold text-secondary-800 focus:ring-2 focus:ring-primary-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">মাসে মোট কর্মদিবস (Working Days)</label>
                <input type="number" name="working_days" value="30" min="1" max="31" required class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
                <p class="text-[11px] text-secondary-500 mt-1">কর্মচারীদের দৈনিক হাজিরা থেকে অনুপস্থিতি হিসাব করে স্বয়ংক্রিয়ভাবে বেতন কর্তন হবে।</p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-secondary-100">
                <button type="button" onclick="closeGenerateModal()" class="px-4 py-2 border border-secondary-300 text-secondary-700 rounded-xl text-sm font-medium hover:bg-secondary-50">বাতিল</button>
                <button type="submit" class="px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-sm font-medium transition-colors shadow-sm">তৈরি শুরু করুন</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Mark as Paid -->
<div id="paymentModal" class="fixed inset-0 bg-secondary-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-secondary-200">
        <div class="flex items-center justify-between pb-4 border-b border-secondary-100">
            <h3 class="text-lg font-bold text-secondary-900 flex items-center gap-2">
                <ion-icon name="checkmark-circle" class="text-emerald-600"></ion-icon>
                বেতন পরিশোধ রেকর্ড করুন
            </h3>
            <button onclick="closePaymentModal()" class="text-secondary-400 hover:text-secondary-700">
                <ion-icon name="close-circle-outline" class="text-2xl"></ion-icon>
            </button>
        </div>

        <form method="POST" action="<?= $base ?>/admin/payroll/mark-paid" class="mt-4 space-y-4">
            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
            <input type="hidden" name="id" id="payId">
            <input type="hidden" name="month" value="<?= htmlspecialchars($currentMonth) ?>">

            <div class="p-3 bg-secondary-50 rounded-xl border border-secondary-200 text-sm">
                <p class="text-secondary-600">কর্মচারী: <strong id="payEmpName" class="text-secondary-900"></strong></p>
                <p class="text-secondary-600 mt-1">প্রদেয় নেট বেতন: <strong id="payAmount" class="text-emerald-600 text-base font-bold"></strong></p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">পরিশোধ মাধ্যম (Payment Method) <span class="text-red-500">*</span></label>
                <select name="payment_method" required class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
                    <option value="cash">নগদ (Cash)</option>
                    <option value="bank">ব্যাংক ট্রান্সফার (Bank)</option>
                    <option value="bkash">বিকাশ (bKash)</option>
                    <option value="nagad">নগদ মোবাইল ব্যাংকিং (Nagad)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">পরিশোধের তারিখ</label>
                <input type="date" name="payment_date" value="<?= date('Y-m-d') ?>" required class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">ট্রানজেকশন রেফারেন্স / নোট (ঐচ্ছিক)</label>
                <input type="text" name="note" placeholder="Txn ID অথবা চেক নম্বর..." class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-secondary-100">
                <button type="button" onclick="closePaymentModal()" class="px-4 py-2 border border-secondary-300 text-secondary-700 rounded-xl text-sm font-medium hover:bg-secondary-50">বাতিল</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-medium transition-colors shadow-sm">পরিশোধ সম্পন্ন</button>
            </div>
        </form>
    </div>
</div>

<script>
function openGenerateModal() {
    document.getElementById('generateModal').classList.remove('hidden');
}

function closeGenerateModal() {
    document.getElementById('generateModal').classList.add('hidden');
}

function openPaymentModal(id, name, amount) {
    document.getElementById('payId').value = id;
    document.getElementById('payEmpName').innerText = name;
    document.getElementById('payAmount').innerText = '৳' + parseFloat(amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('paymentModal').classList.remove('hidden');
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
}
</script>
