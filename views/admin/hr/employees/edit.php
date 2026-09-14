<?php 
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
?>

<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-secondary-900 flex items-center gap-2">
                <ion-icon name="create-outline" class="text-primary-600 text-2xl"></ion-icon>
                কর্মচারীর তথ্য সম্পাদনা - <?= htmlspecialchars($employee['name']) ?>
            </h2>
            <p class="text-secondary-500 text-sm mt-1">EMP Code: <span class="font-mono font-bold text-primary-600"><?= htmlspecialchars($employee['emp_code']) ?></span></p>
        </div>
        <a href="<?= $base ?>/admin/hr/employees/show?id=<?= $employee['id'] ?>" class="px-4 py-2 bg-secondary-100 hover:bg-secondary-200 text-secondary-700 rounded-xl text-sm font-medium flex items-center gap-1.5 transition-colors">
            <ion-icon name="arrow-back-outline"></ion-icon>
            <span>প্রোফাইলে ফিরুন</span>
        </a>
    </div>

    <!-- Error Alert -->
    <?php if (!empty($error)): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center gap-2 text-sm">
            <ion-icon name="alert-circle" class="text-xl text-red-600"></ion-icon>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= $base ?>/admin/hr/employees/update" enctype="multipart/form-data" class="space-y-6">
        <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
        <input type="hidden" name="id" value="<?= $employee['id'] ?>">

        <!-- Section 1: Basic Information -->
        <div class="bg-white rounded-2xl border border-secondary-200 shadow-sm p-6 space-y-4">
            <h3 class="text-base font-bold text-secondary-900 border-b border-secondary-100 pb-3 flex items-center gap-2">
                <ion-icon name="person-circle-outline" class="text-primary-600 text-xl"></ion-icon>
                ১. ব্যক্তিগত তথ্য (Personal Information)
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">পূর্ণ নাম <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="<?= htmlspecialchars($employee['name']) ?>" required class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">মোবাইল নম্বর <span class="text-red-500">*</span></label>
                            <input type="text" name="phone" value="<?= htmlspecialchars($employee['phone']) ?>" required class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">ইমেইল এড্রেস</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($employee['email'] ?? '') ?>" class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">জাতীয় পরিচয়পত্র (NID)</label>
                            <input type="text" name="nid" value="<?= htmlspecialchars($employee['nid'] ?? '') ?>" class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">লিঙ্গ (Gender)</label>
                            <select name="gender" class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
                                <option value="male" <?= $employee['gender'] === 'male' ? 'selected' : '' ?>>পুরুষ (Male)</option>
                                <option value="female" <?= $employee['gender'] === 'female' ? 'selected' : '' ?>>মহিলা (Female)</option>
                                <option value="other" <?= $employee['gender'] === 'other' ? 'selected' : '' ?>>অন্যান্য (Other)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">জরুরি যোগাযোগ নম্বর</label>
                            <input type="text" name="emergency_contact" value="<?= htmlspecialchars($employee['emergency_contact'] ?? '') ?>" class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>
                </div>

                <!-- Photo Upload Box -->
                <div class="flex flex-col items-center justify-center p-4 border-2 border-dashed border-secondary-300 rounded-2xl bg-secondary-50">
                    <div id="photoPreviewBox" class="w-28 h-28 rounded-full bg-secondary-200 border-2 border-white shadow-md flex items-center justify-center overflow-hidden mb-3">
                        <?php if (!empty($employee['photo_path'])): ?>
                            <img src="<?= (strpos($employee['photo_path'], 'http') === 0) ? $employee['photo_path'] : ($base . '/' . ltrim($employee['photo_path'], '/')) ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <ion-icon name="person" class="text-4xl text-secondary-400"></ion-icon>
                        <?php endif; ?>
                    </div>
                    <label class="cursor-pointer bg-white border border-secondary-300 hover:bg-secondary-100 text-secondary-700 px-3 py-1.5 rounded-xl text-xs font-medium transition-colors shadow-sm">
                        ছবি পরিবর্তন করুন
                        <input type="file" name="photo" accept="image/*" class="hidden" onchange="previewEmployeePhoto(event)">
                    </label>
                    <p class="text-[11px] text-secondary-400 mt-2 text-center">JPG, PNG বা WEBP</p>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">বর্তমান ও স্থায়ী ঠিকানা</label>
                <textarea name="address" rows="2" class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500"><?= htmlspecialchars($employee['address'] ?? '') ?></textarea>
            </div>
        </div>

        <!-- Section 2: Job & Position -->
        <div class="bg-white rounded-2xl border border-secondary-200 shadow-sm p-6 space-y-4">
            <h3 class="text-base font-bold text-secondary-900 border-b border-secondary-100 pb-3 flex items-center gap-2">
                <ion-icon name="briefcase-outline" class="text-primary-600 text-xl"></ion-icon>
                ২. অফিসিয়াল পদবী ও চাকরি সংক্রান্ত তথ্য
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">কর্মচারী কোড (EMP Code)</label>
                    <input type="text" readonly value="<?= htmlspecialchars($employee['emp_code']) ?>" class="w-full bg-secondary-100 border border-secondary-300 rounded-xl px-3.5 py-2 text-sm font-mono font-bold text-secondary-600 cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">যোগদানের তারিখ</label>
                    <input type="date" name="joining_date" value="<?= htmlspecialchars($employee['joining_date']) ?>" required class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">চাকরির ধরন</label>
                    <select name="employment_type" class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
                        <option value="full_time" <?= $employee['employment_type'] === 'full_time' ? 'selected' : '' ?>>পূর্ণকালীন (Full Time)</option>
                        <option value="part_time" <?= $employee['employment_type'] === 'part_time' ? 'selected' : '' ?>>খণ্ডকালীন (Part Time)</option>
                        <option value="contract" <?= $employee['employment_type'] === 'contract' ? 'selected' : '' ?>>চুক্তিভিত্তিক (Contractual)</option>
                        <option value="daily" <?= $employee['employment_type'] === 'daily' ? 'selected' : '' ?>>দৈনিক হাজিরা (Daily Basis)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">ডিপার্টমেন্ট (Department)</label>
                    <select name="department_id" id="departmentSelect" onchange="filterDesignations(this.value)" class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
                        <option value="">-- ডিপার্টমেন্ট নির্বাচন করুন --</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= $dept['id'] ?>" <?= $employee['department_id'] == $dept['id'] ? 'selected' : '' ?>><?= htmlspecialchars($dept['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">পদবী (Designation)</label>
                    <select name="designation_id" id="designationSelect" class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
                        <option value="">-- পদবী নির্বাচন করুন --</option>
                        <?php foreach ($designations as $des): ?>
                            <option value="<?= $des['id'] ?>" data-dept="<?= $des['department_id'] ?>" <?= $employee['designation_id'] == $des['id'] ? 'selected' : '' ?>><?= htmlspecialchars($des['title'] . ' (' . $des['department_name'] . ')') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">সিস্টেম লগইন লিংক (ঐচ্ছিক)</label>
                    <select name="user_id" class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
                        <option value="">-- কোনো ইউজার লিংক নয় --</option>
                        <?php foreach ($users as $u): ?>
                            <option value="<?= $u['id'] ?>" <?= $employee['user_id'] == $u['id'] ? 'selected' : '' ?>><?= htmlspecialchars($u['name'] . ' (@' . $u['username'] . ')') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-2">কর্মচারী স্ট্যাটাস</label>
                <div class="flex flex-wrap gap-4">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="status" value="active" <?= $employee['status'] === 'active' ? 'checked' : '' ?> class="text-primary-600 focus:ring-primary-500">
                        <span class="text-sm font-medium text-emerald-700">সক্রিয় (Active)</span>
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="status" value="on_leave" <?= $employee['status'] === 'on_leave' ? 'checked' : '' ?> class="text-primary-600 focus:ring-primary-500">
                        <span class="text-sm font-medium text-amber-700">ছুটিতে (On Leave)</span>
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="status" value="inactive" <?= $employee['status'] === 'inactive' ? 'checked' : '' ?> class="text-primary-600 focus:ring-primary-500">
                        <span class="text-sm font-medium text-secondary-700">নিষ্ক্রিয় (Inactive)</span>
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="status" value="terminated" <?= $employee['status'] === 'terminated' ? 'checked' : '' ?> class="text-primary-600 focus:ring-primary-500">
                        <span class="text-sm font-medium text-red-700">বহিষ্কৃত (Terminated)</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Section 3: Salary & Compensation -->
        <div class="bg-white rounded-2xl border border-secondary-200 shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-secondary-100 pb-3">
                <h3 class="text-base font-bold text-secondary-900 flex items-center gap-2">
                    <ion-icon name="cash-outline" class="text-primary-600 text-xl"></ion-icon>
                    ৩. মাসিক বেতন ও ভাতা কাঠামো (Salary Structure)
                </h3>
                <div class="text-right">
                    <span class="text-xs text-secondary-500">আনুমানিক মোট বেতন: </span>
                    <span id="grossPreview" class="text-base font-bold text-emerald-600">৳0.00</span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">মূল বেতন (Basic) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="basic_salary" id="basicSalary" value="<?= htmlspecialchars($employee['basic_salary']) ?>" oninput="calculateGross()" required class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2 text-sm font-bold text-secondary-800 focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">বাড়ি ভাড়া (House Rent)</label>
                    <input type="number" step="0.01" name="house_rent" id="houseRent" value="<?= htmlspecialchars($employee['house_rent']) ?>" oninput="calculateGross()" class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">চিকিৎসা ভাতা (Medical)</label>
                    <input type="number" step="0.01" name="medical_allowance" id="medicalAllowance" value="<?= htmlspecialchars($employee['medical_allowance']) ?>" oninput="calculateGross()" class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">অন্যান্য ভাতা (Other)</label>
                    <input type="number" step="0.01" name="other_allowance" id="otherAllowance" value="<?= htmlspecialchars($employee['other_allowance']) ?>" oninput="calculateGross()" class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
                </div>
            </div>
        </div>

        <!-- Section 4: Banking & Disbursement -->
        <div class="bg-white rounded-2xl border border-secondary-200 shadow-sm p-6 space-y-4">
            <h3 class="text-base font-bold text-secondary-900 border-b border-secondary-100 pb-3 flex items-center gap-2">
                <ion-icon name="card-outline" class="text-primary-600 text-xl"></ion-icon>
                ৪. ব্যাংক ও মোবাইল ব্যাংকিং বিবরণ (Payment Details)
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">ব্যাংকের নাম</label>
                    <input type="text" name="bank_name" value="<?= htmlspecialchars($employee['bank_name'] ?? '') ?>" class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">ব্যাংক একাউন্ট নম্বর</label>
                    <input type="text" name="bank_account_no" value="<?= htmlspecialchars($employee['bank_account_no'] ?? '') ?>" class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">মোবাইল ব্যাংকিং মাধ্যম</label>
                    <select name="mobile_banking_type" class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
                        <option value="">-- প্রযোজ্য নয় --</option>
                        <option value="bKash" <?= ($employee['mobile_banking_type'] ?? '') === 'bKash' ? 'selected' : '' ?>>বিকাশ (bKash)</option>
                        <option value="Nagad" <?= ($employee['mobile_banking_type'] ?? '') === 'Nagad' ? 'selected' : '' ?>>নগদ (Nagad)</option>
                        <option value="Rocket" <?= ($employee['mobile_banking_type'] ?? '') === 'Rocket' ? 'selected' : '' ?>>রকেট (Rocket)</option>
                        <option value="Upay" <?= ($employee['mobile_banking_type'] ?? '') === 'Upay' ? 'selected' : '' ?>>উপায় (Upay)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-secondary-700 uppercase tracking-wider mb-1">মোবাইল ব্যাংকিং নম্বর</label>
                    <input type="text" name="mobile_banking_number" value="<?= htmlspecialchars($employee['mobile_banking_number'] ?? '') ?>" class="w-full bg-secondary-50 border border-secondary-300 rounded-xl px-3.5 py-2 text-sm text-secondary-800 focus:ring-2 focus:ring-primary-500">
                </div>
            </div>
        </div>

        <!-- Form Submit Bar -->
        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="<?= $base ?>/admin/hr/employees/show?id=<?= $employee['id'] ?>" class="px-6 py-2.5 border border-secondary-300 text-secondary-700 rounded-xl text-sm font-medium hover:bg-secondary-50 transition-colors">বাতিল</a>
            <button type="submit" class="px-8 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-sm font-medium transition-colors shadow-md flex items-center gap-2">
                <ion-icon name="save-outline" class="text-lg"></ion-icon>
                <span>পরিবর্তন সংরক্ষণ করুন</span>
            </button>
        </div>
    </form>
</div>

<script>
function previewEmployeePhoto(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('photoPreviewBox').innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
        }
        reader.readAsDataURL(file);
    }
}

function calculateGross() {
    const basic = parseFloat(document.getElementById('basicSalary').value) || 0;
    const house = parseFloat(document.getElementById('houseRent').value) || 0;
    const medical = parseFloat(document.getElementById('medicalAllowance').value) || 0;
    const other = parseFloat(document.getElementById('otherAllowance').value) || 0;
    const total = basic + house + medical + other;
    document.getElementById('grossPreview').innerText = '৳' + total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

document.addEventListener('DOMContentLoaded', calculateGross);
</script>
