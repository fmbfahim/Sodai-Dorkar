<div class="max-w-5xl mx-auto mb-16">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-secondary-900">সেটিংস (Settings)</h1>
            <p class="text-secondary-500 text-xs mt-1">সিস্টেম কনফিগারেশন, একক ও প্যাকেজিং ব্যবস্থাপনা।</p>
        </div>
    </div>

    <!-- Settings Sub-Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-secondary-200 mb-8">
        <a href="/sodai-dorkar/public/admin/settings" 
           class="px-5 py-3 font-semibold text-sm transition-all border-b-2 flex items-center gap-2 text-secondary-500 border-transparent hover:text-secondary-800">
            <ion-icon name="settings-outline" class="text-lg"></ion-icon>
            সাধারণ সেটিংস (General)
        </a>
        <a href="/sodai-dorkar/public/admin/settings/units" 
           class="px-5 py-3 font-bold text-sm transition-all border-b-2 flex items-center gap-2 text-primary-600 border-primary-600 bg-primary-50/40 rounded-t-xl">
            <ion-icon name="scale-outline" class="text-lg"></ion-icon>
            একক ও প্যাকেজিং অপশন (Units & Packaging)
        </a>
        <?php if (\Core\Auth::can('database_reset') || \Core\Auth::isAdmin()): ?>
        <a href="/sodai-dorkar/public/admin/settings/cleanup" 
           class="px-5 py-3 font-semibold text-sm transition-all border-b-2 flex items-center gap-2 text-red-500 border-transparent hover:text-red-700">
            <ion-icon name="trash-bin-outline" class="text-lg"></ion-icon>
            ডাটা ক্লিনআপ ও রিসেট (Data Reset)
        </a>
        <?php endif; ?>
    </div>

    <?php if (isset($_GET['success'])): ?>
    <div class="bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-xl mb-6 flex items-center gap-2 text-sm shadow-xs" role="alert">
        <ion-icon name="checkmark-circle-outline" class="text-xl text-emerald-600 flex-shrink-0"></ion-icon>
        <span class="font-medium">নতুন একক / প্যাকেজিং অপশন সফলভাবে যোগ করা হয়েছে!</span>
    </div>
    <?php elseif (isset($_GET['deleted'])): ?>
    <div class="bg-amber-50 border border-amber-300 text-amber-800 px-4 py-3 rounded-xl mb-6 flex items-center gap-2 text-sm shadow-xs" role="alert">
        <ion-icon name="information-circle-outline" class="text-xl text-amber-600 flex-shrink-0"></ion-icon>
        <span class="font-medium">একক অপশনটি সফলভাবে মুছে ফেলা হয়েছে।</span>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left 1 Col: Add New Packaging Unit Form -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-secondary-100 p-6 sticky top-6">
                <h3 class="text-base font-bold text-secondary-900 mb-1 flex items-center gap-2">
                    <ion-icon name="add-circle-outline" class="text-primary-600 text-xl"></ion-icon>
                    নতুন একক / প্যাকেজ যোগ করুন
                </h3>
                <p class="text-xs text-secondary-500 mb-5">এখানে যোগ করা এককগুলো প্রোডাক্ট ক্রিয়েটর ড্রপডাউনে স্বয়ংক্রিয়ভাবে দেখাবে।</p>

                <form action="/sodai-dorkar/public/admin/settings/units/store" method="POST">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                    <div class="mb-4">
                        <label class="block text-secondary-700 text-xs font-bold uppercase tracking-wider mb-2">
                            একক / প্যাকেজের নাম <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" required placeholder="যেমন: বস্তা (৫০ কেজি) বা কার্টন (২৪ পিস)" 
                               class="w-full px-3.5 py-2.5 border border-secondary-300 rounded-xl text-sm font-medium focus:ring-2 focus:ring-primary-500">
                        <p class="text-[11px] text-secondary-400 mt-1">ব্যবহারকারী ড্রপডাউনে এই নামটি দেখতে পাবে।</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-secondary-700 text-xs font-bold uppercase tracking-wider mb-2">
                            মূল স্টক একক (Base Unit) <span class="text-red-500">*</span>
                        </label>
                        <select name="base_unit" id="new_unit_base" 
                                class="w-full px-3.5 py-2.5 border border-secondary-300 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-primary-500 bg-white">
                            <option value="kg">কেজি (kg)</option>
                            <option value="liter">লিটার (liter)</option>
                            <option value="pcs">পিস (pcs)</option>
                            <option value="gm">গ্রাম (gm)</option>
                            <option value="ml">মিলি (ml)</option>
                        </select>
                    </div>

                    <div class="mb-5">
                        <label class="block text-secondary-700 text-xs font-bold uppercase tracking-wider mb-2">
                            ডিফল্ট রূপান্তর পরিমাণ <span class="text-red-500">*</span>
                        </label>
                        <input type="number" step="0.001" min="0.001" name="default_qty" value="1" required 
                               class="w-full px-3.5 py-2.5 border border-secondary-300 rounded-xl text-sm font-bold text-secondary-800 focus:ring-2 focus:ring-primary-500">
                        <p class="text-[11px] text-secondary-400 mt-1">যেমন: ১ বস্তা = ৫০ কেজি, ১ বক্স = ২৪ পিস।</p>
                    </div>

                    <button type="submit" 
                            class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 px-4 rounded-xl transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2 text-sm">
                        <ion-icon name="save-outline" class="text-lg"></ion-icon>
                        একক সংরক্ষণ করুন
                    </button>
                </form>
            </div>
        </div>

        <!-- Right 2 Cols: List of Current Units & Packaging Options -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-secondary-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-secondary-100 bg-secondary-50/70 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-secondary-800 text-base">বিদ্যমান একক ও প্যাকেজিং তালিকা</h3>
                        <p class="text-xs text-secondary-500">পণ্য ক্রয় ও বিক্রয়ের জন্য সক্রিয় সকল ড্রপডাউন অপশন।</p>
                    </div>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-primary-100 text-primary-700">
                        মোট: <?php echo count($units); ?> টি
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-secondary-600">
                        <thead class="bg-secondary-50 text-secondary-700 uppercase text-xs font-bold border-b border-secondary-200">
                            <tr>
                                <th class="px-6 py-3.5">একক / প্যাকেজের নাম</th>
                                <th class="px-4 py-3.5">মূল স্টক একক</th>
                                <th class="px-4 py-3.5 text-right">রূপান্তর মান</th>
                                <th class="px-6 py-3.5 text-center">অ্যাকশন</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-secondary-100">
                            <?php if (empty($units)): ?>
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-secondary-400">
                                        কোনো একক পাওয়া যায়নি। বাঁ পাশের ফর্ম দিয়ে নতুন একক যোগ করুন।
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($units as $u): ?>
                                    <tr class="hover:bg-secondary-50/60 transition-colors">
                                        <td class="px-6 py-3.5 font-bold text-secondary-900 flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-primary-500"></span>
                                            <?php echo htmlspecialchars($u['name']); ?>
                                        </td>
                                        <td class="px-4 py-3.5">
                                            <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-secondary-100 text-secondary-700 border border-secondary-200">
                                                <?php echo htmlspecialchars($u['base_unit']); ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3.5 text-right font-mono font-bold text-secondary-800">
                                            = <?php echo floatval($u['default_qty']); ?> <?php echo htmlspecialchars($u['base_unit']); ?>
                                        </td>
                                        <td class="px-6 py-3.5 text-center">
                                            <form action="/sodai-dorkar/public/admin/settings/units/delete" method="POST" onsubmit="return confirm('এই এককটি মুছে ফেলতে চান?');" class="inline">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                                                <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                                <button type="submit" class="text-secondary-400 hover:text-red-500 p-1 rounded-lg hover:bg-red-50 transition-colors" title="মুছুন">
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

            <!-- Hint Box -->
            <div class="mt-6 p-4 rounded-2xl bg-blue-50/60 border border-blue-200/80 flex items-start gap-3 text-xs text-blue-800">
                <ion-icon name="bulb-outline" class="text-xl text-blue-600 flex-shrink-0 mt-0.5"></ion-icon>
                <div>
                    <strong>💡 ড্রপডাউন ব্যবহারের নিয়ম:</strong><br>
                    এখানে নতুন কোনো একক (যেমন: "কার্টন ৪৮ পিস" বা "টিন ১৬ লিটার") যোগ করলে পণ্য সম্পাদনা (Edit Product) ও বাল্ক ক্রিয়েটর (Bulk Creator)-এর **"ক্রয় একক"** ড্রপডাউনে সাথে সাথে চলে আসবে।
                </div>
            </div>
        </div>

    </div>
</div>
