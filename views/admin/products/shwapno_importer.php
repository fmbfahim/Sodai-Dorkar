<?php
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
?>

<div class="space-y-6">
    <!-- Header Hero Section -->
    <div class="bg-gradient-to-r from-emerald-900 via-teal-900 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-28 top-0 w-52 h-52 bg-teal-400/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-emerald-300 uppercase tracking-wider mb-2">
                    <a href="<?= $base ?>/admin/products" class="hover:text-white transition-colors">পণ্য ও ইনভেন্টরি</a>
                    <span>›</span>
                    <span class="text-white">Shwapno ক্যাটাগরি স্ক্র্যাপার ও অটো-ইমপোর্টার</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight flex items-center gap-3">
                    <span class="p-2.5 bg-emerald-500/20 backdrop-blur-md rounded-2xl border border-emerald-400/30 text-emerald-300 flex items-center justify-center">
                        <ion-icon name="cloud-download-outline" class="text-2xl"></ion-icon>
                    </span>
                    Shwapno Category Auto-Importer
                </h1>
                <p class="text-secondary-300 text-sm mt-2 max-w-2xl leading-relaxed">
                    স্বপ্ন (<span class="text-emerald-300 font-semibold">Shwapno.com</span>)-এর যেকোনো ক্যাটাগরি থেকে এক ক্লিকে হাই-রেজ্যুলেশন ছবি, সঠিক বিক্রয়মূল্য, একক এবং ভ্যারিয়েন্ট সহ সরাসরি আপনার স্টোরের ডাটাবেসে পণ্য ইমপোর্ট করুন।
                </p>
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <a href="<?= $base ?>/admin/products/image-finder" class="px-4 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-slate-950 text-xs font-black transition-all flex items-center gap-2 shadow-md shadow-amber-950/30">
                    <ion-icon name="sparkles" class="text-base"></ion-icon>
                    <span>অটো ইমেজ ফাইন্ডার</span>
                </a>
                <a href="<?= $base ?>/admin/products/bulk-import" class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-white text-xs font-bold transition-all flex items-center gap-2 shadow-sm">
                    <ion-icon name="document-text-outline" class="text-base"></ion-icon>
                    <span>বাল্ক সিএসভি ইমপোর্ট</span>
                </a>
                <a href="<?= $base ?>/admin/products" class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold transition-all flex items-center gap-2 shadow-md shadow-emerald-900/30">
                    <ion-icon name="cube-outline" class="text-base"></ion-icon>
                    <span>পণ্য তালিকা</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Category Selector & Search Section -->
    <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-xs border border-secondary-200 space-y-6">
        <div>
            <div class="flex items-center justify-between gap-4 mb-3">
                <h3 class="text-sm font-black text-secondary-900 uppercase tracking-wider flex items-center gap-2">
                    <ion-icon name="apps-outline" class="text-emerald-600 text-base"></ion-icon>
                    <span>জনপ্রিয় ক্যাটাগরি সমূহ (১-ক্লিকে লোড করুন)</span>
                </h3>
                <span class="text-[11px] text-secondary-400 font-medium hidden sm:inline">যেকোনো একটি ক্যাটাগরিতে ক্লিক করলেই স্বয়ংক্রিয়ভাবে পণ্য চলে আসবে</span>
            </div>
            
            <!-- Category Chips Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-2.5" id="popularCategoryChips">
                <?php foreach ($popularCategories as $cat): ?>
                    <button type="button" 
                            onclick="selectCategory('<?= htmlspecialchars($cat['slug']) ?>', '<?= htmlspecialchars(addslashes($cat['name'])) ?>')"
                            class="category-chip group p-3 rounded-2xl border border-secondary-200 bg-secondary-50/50 hover:bg-emerald-50 hover:border-emerald-300 hover:shadow-xs transition-all text-left flex items-center gap-2.5 cursor-pointer">
                        <div class="w-8 h-8 rounded-xl bg-white border border-secondary-200 text-secondary-600 group-hover:text-emerald-600 group-hover:border-emerald-200 flex items-center justify-center text-base transition-colors flex-shrink-0">
                            <ion-icon name="<?= $cat['icon'] ?>"></ion-icon>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-xs font-bold text-secondary-800 group-hover:text-emerald-900 truncate">
                                <?= htmlspecialchars(explode('(', $cat['name'])[0]) ?>
                            </div>
                            <div class="text-[10px] font-mono text-secondary-400 truncate">
                                <?= htmlspecialchars($cat['slug']) ?>
                            </div>
                        </div>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Custom Search / URL Input -->
        <div class="pt-5 border-t border-secondary-100">
            <label for="shwapnoCategoryInput" class="block text-xs font-bold text-secondary-700 mb-2">
                অথবা Shwapno-এর যেকোনো কাস্টম ক্যাটাগরি, কি-ওয়ার্ড বা পেজের লিঙ্ক দিন:
            </label>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <div class="relative flex-1">
                    <ion-icon name="search-outline" class="absolute left-4 top-1/2 -translate-y-1/2 text-secondary-400 text-lg"></ion-icon>
                    <input type="text" 
                           id="shwapnoCategoryInput" 
                           placeholder="উদাহরণ: rice, tea, noodles, fresh-fruits অথবা https://www.shwapno.com/fresh-fruits"
                           class="w-full pl-11 pr-4 py-3.5 bg-secondary-50 border border-secondary-300 rounded-2xl text-xs sm:text-sm font-semibold text-secondary-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:border-emerald-500 transition-all shadow-2xs"
                           onkeydown="if(event.key === 'Enter') { fetchCategoryProducts(); }">
                </div>
                <button type="button" 
                        id="loadProductsBtn"
                        onclick="fetchCategoryProducts()" 
                        class="px-7 py-3.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs sm:text-sm font-black transition-all shadow-md shadow-emerald-700/20 flex items-center justify-center gap-2 cursor-pointer active:scale-95">
                    <ion-icon name="cloud-download" class="text-lg"></ion-icon>
                    <span id="loadProductsBtnText">পণ্য লোড করুন</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Import Configuration / Mapping Settings -->
    <div class="bg-white rounded-3xl p-6 shadow-xs border border-secondary-200">
        <div class="flex items-center gap-2 mb-4 pb-3 border-b border-secondary-100">
            <ion-icon name="settings-outline" class="text-emerald-600 text-lg"></ion-icon>
            <h3 class="text-xs font-black text-secondary-900 uppercase tracking-wider">ইমপোর্ট কনফিগারেশন ও ডিফল্ট সেটিংস</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Target Store Category -->
            <div>
                <label for="targetCategoryId" class="block text-xs font-bold text-secondary-700 mb-1.5">
                    আপনার স্টোরের ক্যাটাগরি (Target Category)
                </label>
                <select id="targetCategoryId" class="w-full px-3.5 py-2.5 bg-secondary-50 border border-secondary-300 rounded-xl text-xs font-semibold text-secondary-800 focus:ring-2 focus:ring-emerald-500 focus:bg-white">
                    <option value="">-- কোনো ক্যাটাগরি ছাড়া / স্বয়ংক্রিয় --</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="text-[10px] text-secondary-400 mt-1">ইমপোর্ট করা সকল পণ্য এই ক্যাটাগরিতে যুক্ত হবে</p>
            </div>

            <!-- Target Vendor -->
            <div>
                <label for="targetVendorId" class="block text-xs font-bold text-secondary-700 mb-1.5">
                    ভেন্ডর / সরবরাহকারী (Vendor)
                </label>
                <select id="targetVendorId" class="w-full px-3.5 py-2.5 bg-secondary-50 border border-secondary-300 rounded-xl text-xs font-semibold text-secondary-800 focus:ring-2 focus:ring-emerald-500 focus:bg-white">
                    <option value="">-- কোনো ভেন্ডর ছাড়া --</option>
                    <?php foreach ($vendors as $v): ?>
                        <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="text-[10px] text-secondary-400 mt-1">প্রয়োজনে নির্দিষ্ট ভেন্ডর নির্বাচন করুন</p>
            </div>

            <!-- Default Stock Quantity -->
            <div>
                <label for="defaultStockQty" class="block text-xs font-bold text-secondary-700 mb-1.5">
                    ডিফল্ট স্টক সংখ্যা (Default Stock)
                </label>
                <input type="number" 
                       id="defaultStockQty" 
                       value="50" 
                       min="0" 
                       class="w-full px-3.5 py-2.5 bg-secondary-50 border border-secondary-300 rounded-xl text-xs font-semibold text-secondary-800 focus:ring-2 focus:ring-emerald-500 focus:bg-white">
                <p class="text-[10px] text-secondary-400 mt-1">নতুন পণ্যের প্রাথমিক ইনভেন্টরি স্টক</p>
            </div>

            <!-- Duplicate Action -->
            <div>
                <label class="block text-xs font-bold text-secondary-700 mb-1.5">
                    ডাটাবেসে পণ্য পূর্বে থাকলে
                </label>
                <div class="flex items-center gap-3 pt-1">
                    <label class="inline-flex items-center gap-1.5 text-xs font-medium text-secondary-700 cursor-pointer">
                        <input type="radio" name="duplicateAction" value="update" checked class="text-emerald-600 focus:ring-emerald-500">
                        <span>মূল্য ও ছবি আপডেট</span>
                    </label>
                    <label class="inline-flex items-center gap-1.5 text-xs font-medium text-secondary-700 cursor-pointer">
                        <input type="radio" name="duplicateAction" value="skip" class="text-emerald-600 focus:ring-emerald-500">
                        <span>স্কিপ করুন</span>
                    </label>
                </div>
                <p class="text-[10px] text-secondary-400 mt-1.5">বিদ্যমান পণ্যের ক্ষেত্রে গৃহীত পদক্ষেপ</p>
            </div>
        </div>
    </div>

    <!-- Live Preview & Selection Area -->
    <div id="previewContainer" class="hidden space-y-4">
        <!-- Results Summary Bar -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-xs border border-secondary-200 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
            <div class="flex items-center gap-3 flex-wrap">
                <div class="px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-1.5">
                    <ion-icon name="file-tray-full-outline" class="text-base text-emerald-600"></ion-icon>
                    <span>মোট সংগৃহীত: <strong id="statTotalCount" class="text-emerald-950">0</strong> টি</span>
                </div>
                <div class="px-3 py-1.5 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-xs font-bold flex items-center gap-1.5">
                    <ion-icon name="sparkles" class="text-base text-blue-600"></ion-icon>
                    <span>নতুন পণ্য: <strong id="statNewCount" class="text-blue-950">0</strong> টি</span>
                </div>
                <div class="px-3 py-1.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold flex items-center gap-1.5">
                    <ion-icon name="copy-outline" class="text-base text-amber-600"></ion-icon>
                    <span>বিদ্যমান (Duplicate): <strong id="statExistsCount" class="text-amber-950">0</strong> টি</span>
                </div>
                <div class="px-3 py-1.5 rounded-xl bg-purple-50 border border-purple-200 text-purple-800 text-xs font-bold flex items-center gap-1.5">
                    <ion-icon name="checkbox-outline" class="text-base text-purple-600"></ion-icon>
                    <span>নির্বাচিত: <strong id="statSelectedCount" class="text-purple-950">0</strong> টি</span>
                </div>
            </div>

            <!-- Bulk Action Buttons -->
            <div class="flex items-center gap-2 flex-wrap">
                <button type="button" 
                        onclick="selectAllProducts(true)" 
                        class="px-3 py-2 rounded-xl bg-secondary-100 hover:bg-secondary-200 text-secondary-700 text-xs font-bold transition-colors">
                    সব নির্বাচন করুন
                </button>
                <button type="button" 
                        onclick="selectOnlyNewProducts()" 
                        class="px-3 py-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold transition-colors border border-blue-200">
                    শুধুমাত্র নতুন পণ্য
                </button>
                <button type="button" 
                        onclick="selectAllProducts(false)" 
                        class="px-3 py-2 rounded-xl bg-secondary-100 hover:bg-secondary-200 text-secondary-600 text-xs font-medium transition-colors">
                    বাতিল
                </button>
                <button type="button" 
                        id="startBulkImportBtn"
                        onclick="openBulkImportModal()" 
                        class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-black transition-all shadow-md shadow-emerald-700/20 flex items-center gap-2 cursor-pointer active:scale-95">
                    <ion-icon name="cloud-upload" class="text-base"></ion-icon>
                    <span>নির্বাচিত পণ্যসমূহ ইমপোর্ট করুন (<span id="bulkImportBtnCount">0</span>)</span>
                </button>
            </div>
        </div>

        <!-- Products Table -->
        <div class="bg-white rounded-3xl shadow-xs border border-secondary-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" id="shwapnoProductsTable">
                    <thead>
                        <tr class="border-b border-secondary-200 bg-secondary-50/70 text-[11px] font-bold uppercase tracking-wider text-secondary-500">
                            <th class="py-3.5 px-4 w-12 text-center">
                                <input type="checkbox" id="masterCheckbox" onchange="toggleMasterCheckbox(this.checked)" class="rounded text-emerald-600 focus:ring-emerald-500">
                            </th>
                            <th class="py-3.5 px-4 w-12 text-center">#</th>
                            <th class="py-3.5 px-4">পণ্য ও ছবি</th>
                            <th class="py-3.5 px-4">SKU / কোড</th>
                            <th class="py-3.5 px-4 text-right">বিক্রয় মূল্য</th>
                            <th class="py-3.5 px-4">একক ও ধরন</th>
                            <th class="py-3.5 px-4">ভ্যারিয়েন্ট</th>
                            <th class="py-3.5 px-4 text-center">স্ট্যাটাস</th>
                            <th class="py-3.5 px-4 text-center">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-secondary-100 text-xs" id="shwapnoProductsTbody">
                        <!-- Populated via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Empty / Initial Placeholder State -->
    <div id="initialStateBox" class="bg-white rounded-3xl p-12 text-center border border-dashed border-secondary-300">
        <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-4 text-3xl shadow-2xs">
            <ion-icon name="basket-outline"></ion-icon>
        </div>
        <h3 class="text-base font-bold text-secondary-900">কোনো ক্যাটাগরি লোড করা হয়নি</h3>
        <p class="text-xs text-secondary-500 mt-1 max-w-md mx-auto leading-relaxed">
            উপরের যেকোনো ক্যাটাগরি চিপে ক্লিক করুন অথবা সার্চ বক্সে Shwapno ক্যাটাগরির নাম লিখে <span class="font-bold text-emerald-700">"পণ্য লোড করুন"</span> বাটনে চাপুন।
        </p>
    </div>
</div>

<!-- ========================================================== -->
<!-- BULK IMPORT LIVE PROGRESS MODAL                            -->
<!-- ========================================================== -->
<div id="shwapnoBulkModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 sm:p-6 transition-all duration-200">
    <div class="bg-white rounded-3xl shadow-2xl border border-secondary-200 w-full max-w-3xl overflow-hidden flex flex-col max-h-[90vh] transform transition-all scale-95 opacity-0" id="shwapnoBulkModalContent">
        
        <!-- Modal Top Bar -->
        <div class="px-6 py-4 bg-gradient-to-r from-secondary-900 via-slate-800 to-secondary-900 text-white flex items-center justify-between border-b border-secondary-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-400/30 text-emerald-400 flex items-center justify-center text-xl">
                    <ion-icon name="cloud-download"></ion-icon>
                </div>
                <div>
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <span>Shwapno বাল্ক অটো-ইমপোর্টার</span>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-[10px] text-emerald-300 font-semibold">Live Pipeline</span>
                    </h3>
                    <p class="text-[11px] text-secondary-300" id="bulkModalSubtitle">ডাটাবেসে পণ্য তৈরি ও ইমেজ ডাউনলোড চলছে...</p>
                </div>
            </div>

            <button type="button" onclick="closeBulkImportModal()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-secondary-300 hover:text-white flex items-center justify-center transition-colors">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-5 overflow-y-auto flex-1">
            <!-- Progress Bar Section -->
            <div class="space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-bold text-secondary-700" id="bulkProgressStatusText">ইমপোর্ট শুরু করার জন্য প্রস্তুত...</span>
                    <span class="font-mono font-bold text-emerald-600 text-sm" id="bulkProgressPct">0%</span>
                </div>
                <div class="w-full h-3 bg-secondary-100 rounded-full overflow-hidden p-0.5">
                    <div id="bulkProgressBar" class="h-full bg-gradient-to-r from-emerald-500 via-teal-400 to-emerald-600 rounded-full transition-all duration-200" style="width: 0%;"></div>
                </div>
            </div>

            <!-- KPI Counters -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="p-3 bg-secondary-50 border border-secondary-200 rounded-2xl text-center">
                    <div class="text-[11px] font-bold text-secondary-500 uppercase">মোট লক্ষ্য</div>
                    <div class="text-xl font-black text-secondary-900 mt-0.5" id="bulkStatTotal">0</div>
                </div>
                <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-2xl text-center">
                    <div class="text-[11px] font-bold text-emerald-600 uppercase">সফল ইমপোর্ট</div>
                    <div class="text-xl font-black text-emerald-700 mt-0.5" id="bulkStatImported">0</div>
                </div>
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-2xl text-center">
                    <div class="text-[11px] font-bold text-amber-600 uppercase">স্কিপ হয়েছে</div>
                    <div class="text-xl font-black text-amber-700 mt-0.5" id="bulkStatSkipped">0</div>
                </div>
                <div class="p-3 bg-red-50 border border-red-200 rounded-2xl text-center">
                    <div class="text-[11px] font-bold text-red-600 uppercase">ত্রুটি (Errors)</div>
                    <div class="text-xl font-black text-red-700 mt-0.5" id="bulkStatErrors">0</div>
                </div>
            </div>

            <!-- Terminal Log Console -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-bold text-secondary-600 flex items-center gap-1.5">
                        <ion-icon name="terminal-outline" class="text-secondary-500"></ion-icon>
                        <span>রিয়েল-টাইম লাইভ লগ (Activity Stream)</span>
                    </span>
                    <button type="button" onclick="clearBulkLogs()" class="text-[11px] text-secondary-400 hover:text-secondary-600 font-medium">
                        ক্লিয়ার লগ
                    </button>
                </div>
                <div class="w-full h-48 bg-slate-950 text-slate-200 rounded-2xl p-3.5 font-mono text-[11px] overflow-y-auto border border-slate-800 space-y-1" id="bulkLogConsole">
                    <div class="text-slate-500 italic">লগ স্ক্রিন প্রস্তুত...</div>
                </div>
            </div>
        </div>

        <!-- Modal Footer Controls -->
        <div class="px-6 py-4 bg-white border-t border-secondary-200 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="text-xs text-secondary-500" id="bulkFooterSummary">
                প্রক্রিয়াকৃত পণ্য: <strong class="text-secondary-900" id="bulkProcessedCount">0</strong> টি
            </div>

            <div class="flex items-center gap-2">
                <button type="button" id="bulkStartBtn" onclick="startBulkImporting()" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-black transition-all shadow-md shadow-emerald-700/20 flex items-center gap-2">
                    <ion-icon name="play" class="text-sm"></ion-icon>
                    <span>ইমপোর্ট শুরু করুন</span>
                </button>
                <button type="button" id="bulkPauseBtn" onclick="togglePauseBulkImport()" class="hidden px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-bold transition-all shadow-xs flex items-center gap-1.5">
                    <ion-icon name="pause" class="text-sm"></ion-icon>
                    <span id="bulkPauseBtnText">পজ করুন</span>
                </button>
                <button type="button" id="bulkStopBtn" onclick="stopBulkImport()" class="hidden px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold transition-all shadow-xs flex items-center gap-1.5">
                    <ion-icon name="stop" class="text-sm"></ion-icon>
                    <span>থামান</span>
                </button>
                <a href="<?= $base ?>/admin/products" id="bulkViewProductsBtn" class="hidden px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-colors flex items-center gap-1.5">
                    <ion-icon name="cube-outline"></ion-icon>
                    <span>পণ্য দেখুন</span>
                </a>
                <button type="button" onclick="closeBulkImportModal()" class="px-4 py-2.5 rounded-xl bg-secondary-100 hover:bg-secondary-200 text-secondary-700 text-xs font-bold transition-colors">
                    বন্ধ করুন
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// State
let loadedProducts = [];
let bulkQueue = [];
let bulkIndex = 0;
let isBulkRunning = false;
let isBulkPaused = false;
let bulkStats = {
    total: 0,
    processed: 0,
    imported: 0,
    skipped: 0,
    errors: 0
};

const csrfToken = '<?= \Core\CSRF::token() ?>';
const baseUri = '<?= $base ?>';

// Quick select popular category chip
function selectCategory(slug, displayName) {
    const input = document.getElementById('shwapnoCategoryInput');
    input.value = slug;
    fetchCategoryProducts();
}

// Fetch products from backend proxy
async function fetchCategoryProducts() {
    const input = document.getElementById('shwapnoCategoryInput');
    const catQuery = input.value.trim();
    if (!catQuery) {
        alert('অনুগ্রহ করে একটি ক্যাটাগরি নাম বা Shwapno লিংক প্রদান করুন।');
        return;
    }

    const btn = document.getElementById('loadProductsBtn');
    const btnText = document.getElementById('loadProductsBtnText');
    btn.disabled = true;
    btnText.innerText = 'লোড হচ্ছে...';

    try {
        const res = await fetch(`${baseUri}/admin/products/shwapno-category-fetch?category=${encodeURIComponent(catQuery)}`);
        const data = await res.json();

        if (data.success && data.products && data.products.length > 0) {
            loadedProducts = data.products;
            renderProductsTable(loadedProducts);
            document.getElementById('initialStateBox').classList.add('hidden');
            document.getElementById('previewContainer').classList.remove('hidden');
        } else {
            alert(data.message || 'কোনো পণ্য পাওয়া যায়নি।');
        }
    } catch (err) {
        console.error('Fetch error:', err);
        alert('সার্ভারের সাথে সংযোগ স্থাপন করা যায়নি। অনুগ্রহ করে আবার চেষ্টা করুন।');
    } finally {
        btn.disabled = false;
        btnText.innerText = 'পণ্য লোড করুন';
    }
}

// Render Products Preview Table
function renderProductsTable(products) {
    const tbody = document.getElementById('shwapnoProductsTbody');
    tbody.innerHTML = '';

    let newCount = 0;
    let existsCount = 0;

    products.forEach((p, idx) => {
        if (p.exists_in_db) existsCount++;
        else newCount++;

        const tr = document.createElement('tr');
        tr.className = 'hover:bg-emerald-50/30 transition-colors group item-row';
        tr.id = `item-row-${idx}`;

        const isNew = !p.exists_in_db;
        const statusBadge = isNew
            ? `<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 text-[10px] font-bold border border-emerald-200">
                 <ion-icon name="sparkles" class="text-xs text-emerald-600"></ion-icon> নতুন পণ্য
               </span>`
            : `<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-100 text-amber-800 text-[10px] font-bold border border-amber-200">
                 <ion-icon name="copy-outline" class="text-xs text-amber-600"></ion-icon> বিদ্যমান (#${p.existing_id})
               </span>`;

        const variantsHtml = (p.variants && p.variants.length > 0)
            ? `<span class="px-2 py-0.5 rounded-md bg-secondary-100 text-secondary-700 text-[10px] font-semibold">${p.variants.length} টি ভ্যারিয়েন্ট</span>`
            : `<span class="text-secondary-400 text-[10px]">—</span>`;

        const oldPriceHtml = (p.old_price && p.old_price > p.sell_price)
            ? `<div class="text-[10px] text-secondary-400 line-through">৳${p.old_price.toFixed(2)}</div>`
            : '';

        tr.innerHTML = `
            <td class="py-3 px-4 text-center">
                <input type="checkbox" 
                       class="product-checkbox rounded text-emerald-600 focus:ring-emerald-500" 
                       data-index="${idx}" 
                       data-is-new="${isNew ? '1' : '0'}"
                       checked
                       onchange="updateSelectedStats()">
            </td>
            <td class="py-3 px-4 text-center font-mono text-secondary-400 text-[11px]">
                ${idx + 1}
            </td>
            <td class="py-3 px-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-white border border-secondary-200 flex-shrink-0 flex items-center justify-center overflow-hidden shadow-2xs group-hover:border-emerald-300 transition-colors">
                        <img src="${p.thumbnail || p.image_url || baseUri + '/images/default-product.svg'}" 
                             alt="${escapeHtml(p.name)}" 
                             class="w-full h-full object-contain p-0.5"
                             onerror="this.src='${baseUri}/images/default-product.svg'">
                    </div>
                    <div class="min-w-0 max-w-sm">
                        <div class="font-bold text-secondary-900 text-sm group-hover:text-emerald-700 transition-colors line-clamp-2">
                            ${escapeHtml(p.name)}
                        </div>
                        <div class="text-[10px] text-secondary-400 mt-0.5">
                            আসল একক: <span class="font-semibold text-secondary-600">${escapeHtml(p.unit)}</span>
                        </div>
                    </div>
                </div>
            </td>
            <td class="py-3 px-4 font-mono text-secondary-600 text-[11px]">
                ${p.sku ? escapeHtml(p.sku) : '<span class="text-secondary-400 italic">অটো-জেনারেটেড</span>'}
            </td>
            <td class="py-3 px-4 text-right">
                <div class="font-bold text-secondary-900 text-sm">৳${p.sell_price.toFixed(2)}</div>
                ${oldPriceHtml}
            </td>
            <td class="py-3 px-4">
                <div class="font-semibold text-secondary-700 text-xs">${escapeHtml(p.base_unit)}</div>
                <div class="text-[10px] text-secondary-400 uppercase">${escapeHtml(p.unit_type)}</div>
            </td>
            <td class="py-3 px-4">
                ${variantsHtml}
            </td>
            <td class="py-3 px-4 text-center" id="item-status-col-${idx}">
                ${statusBadge}
            </td>
            <td class="py-3 px-4 text-center">
                <button type="button" 
                        id="single-import-btn-${idx}"
                        onclick="importSingleProduct(${idx})" 
                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-secondary-100 hover:bg-emerald-600 hover:text-white text-secondary-700 text-xs font-bold transition-all shadow-2xs">
                    <ion-icon name="cloud-download-outline" class="text-sm"></ion-icon>
                    <span>ইমপোর্ট</span>
                </button>
            </td>
        `;

        tbody.appendChild(tr);
    });

    document.getElementById('statTotalCount').innerText = products.length;
    document.getElementById('statNewCount').innerText = newCount;
    document.getElementById('statExistsCount').innerText = existsCount;
    updateSelectedStats();
}

// Update selected statistics & bulk button count
function updateSelectedStats() {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    let selectedCount = 0;
    checkboxes.forEach(cb => {
        if (cb.checked) selectedCount++;
    });

    document.getElementById('statSelectedCount').innerText = selectedCount;
    document.getElementById('bulkImportBtnCount').innerText = selectedCount;
    
    const master = document.getElementById('masterCheckbox');
    if (master) {
        master.checked = selectedCount === checkboxes.length && checkboxes.length > 0;
    }
}

// Master Checkbox Toggle
function toggleMasterCheckbox(checked) {
    document.querySelectorAll('.product-checkbox').forEach(cb => {
        cb.checked = checked;
    });
    updateSelectedStats();
}

// Select All / Deselect All
function selectAllProducts(checked) {
    document.querySelectorAll('.product-checkbox').forEach(cb => {
        cb.checked = checked;
    });
    updateSelectedStats();
}

// Select only new products
function selectOnlyNewProducts() {
    document.querySelectorAll('.product-checkbox').forEach(cb => {
        cb.checked = cb.getAttribute('data-is-new') === '1';
    });
    updateSelectedStats();
}

// Import a Single Product directly from row
async function importSingleProduct(idx) {
    const p = loadedProducts[idx];
    if (!p) return;

    const btn = document.getElementById(`single-import-btn-${idx}`);
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = `<span class="inline-block w-3 h-3 border-2 border-current border-t-transparent rounded-full animate-spin"></span>`;

    const targetCat = document.getElementById('targetCategoryId').value;
    const targetVen = document.getElementById('targetVendorId').value;
    const stockQty = document.getElementById('defaultStockQty').value;

    try {
        const formData = new FormData();
        formData.append('csrf_token', csrfToken);
        formData.append('name', p.name);
        formData.append('sku', p.sku || '');
        formData.append('sell_price', p.sell_price);
        if (p.old_price) formData.append('old_price', p.old_price);
        formData.append('category_id', targetCat);
        formData.append('vendor_id', targetVen);
        formData.append('stock_qty', stockQty);
        formData.append('unit_type', p.unit_type);
        formData.append('base_unit', p.base_unit);
        formData.append('image_url', p.image_url);
        if (p.variants && p.variants.length > 0) {
            formData.append('variants', JSON.stringify(p.variants));
        }

        const res = await fetch(`${baseUri}/admin/products/shwapno-import-single`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });

        const data = await res.json();
        if (data.success) {
            btn.className = 'inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-emerald-600 text-white text-xs font-bold shadow-xs';
            btn.innerHTML = `<ion-icon name="checkmark-circle" class="text-sm"></ion-icon> <span>সম্পন্ন!</span>`;
            document.getElementById(`item-status-col-${idx}`).innerHTML = `
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-600 text-white text-[10px] font-bold">
                    <ion-icon name="checkmark-done" class="text-xs"></ion-icon> ইমপোর্ট সম্পন্ন (#${data.id})
                </span>
            `;
            p.exists_in_db = true;
            p.existing_id = data.id;
        } else {
            alert(data.message || 'ইমপোর্ট করা যায়নি!');
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    } catch (err) {
        console.error('Import error:', err);
        alert('সার্ভার ত্রুটি: ইমপোর্ট করা সম্ভব হয়নি।');
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    }
}

// Open Bulk Import Modal
function openBulkImportModal() {
    const checkboxes = document.querySelectorAll('.product-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('অনুগ্রহ করে অন্তত একটি পণ্য নির্বাচন করুন!');
        return;
    }

    bulkQueue = [];
    checkboxes.forEach(cb => {
        const idx = parseInt(cb.getAttribute('data-index'), 10);
        if (loadedProducts[idx]) {
            bulkQueue.push({ index: idx, product: loadedProducts[idx] });
        }
    });

    bulkStats = {
        total: bulkQueue.length,
        processed: 0,
        imported: 0,
        skipped: 0,
        errors: 0
    };
    bulkIndex = 0;
    isBulkRunning = false;
    isBulkPaused = false;

    updateBulkUiCounters();
    clearBulkLogs();

    const modal = document.getElementById('shwapnoBulkModal');
    const content = document.getElementById('shwapnoBulkModalContent');
    modal.classList.remove('hidden');
    setTimeout(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }, 10);

    document.getElementById('bulkStartBtn').classList.remove('hidden');
    document.getElementById('bulkStartBtn').innerHTML = `<ion-icon name="play" class="text-sm"></ion-icon> <span>ইমপোর্ট শুরু করুন</span>`;
    document.getElementById('bulkPauseBtn').classList.add('hidden');
    document.getElementById('bulkStopBtn').classList.add('hidden');
    document.getElementById('bulkViewProductsBtn').classList.add('hidden');
    document.getElementById('bulkProgressStatusText').innerText = `মোট ${bulkStats.total}টি পণ্য ইমপোর্ট করার জন্য প্রস্তুত...`;
}

// Close Bulk Import Modal
function closeBulkImportModal() {
    if (isBulkRunning) {
        if (!confirm('ইমপোর্ট প্রক্রিয়া এখনও চলছে। আপনি কি সত্যিই বন্ধ করতে চান?')) {
            return;
        }
        isBulkRunning = false;
    }

    const modal = document.getElementById('shwapnoBulkModal');
    const content = document.getElementById('shwapnoBulkModalContent');
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 200);
}

// Update UI Progress and Counters in Modal
function updateBulkUiCounters() {
    document.getElementById('bulkStatTotal').innerText = bulkStats.total;
    document.getElementById('bulkStatImported').innerText = bulkStats.imported;
    document.getElementById('bulkStatSkipped').innerText = bulkStats.skipped;
    document.getElementById('bulkStatErrors').innerText = bulkStats.errors;
    document.getElementById('bulkProcessedCount').innerText = `${bulkStats.processed} / ${bulkStats.total}`;

    const pct = bulkStats.total > 0 ? Math.round((bulkStats.processed / bulkStats.total) * 100) : 0;
    document.getElementById('bulkProgressBar').style.width = `${pct}%`;
    document.getElementById('bulkProgressPct').innerText = `${pct}%`;
}

// Append log to dark terminal console
function appendBulkLog(msg, type = 'info') {
    const consoleEl = document.getElementById('bulkLogConsole');
    const time = new Date().toLocaleTimeString('en-US', { hour12: false });
    const div = document.createElement('div');
    
    let colorClass = 'text-slate-300';
    if (type === 'success') colorClass = 'text-emerald-400 font-bold';
    else if (type === 'skip') colorClass = 'text-amber-300';
    else if (type === 'error') colorClass = 'text-red-400 font-bold';
    else if (type === 'done') colorClass = 'text-teal-300 font-bold';

    div.className = `${colorClass} leading-tight py-0.5`;
    div.innerHTML = `<span class="text-slate-500">[${time}]</span> ${msg}`;
    consoleEl.appendChild(div);
    consoleEl.scrollTop = consoleEl.scrollHeight;
}

function clearBulkLogs() {
    document.getElementById('bulkLogConsole').innerHTML = '';
}

// Start sequential bulk importing
async function startBulkImporting() {
    if (isBulkRunning) return;

    isBulkRunning = true;
    isBulkPaused = false;

    document.getElementById('bulkStartBtn').classList.add('hidden');
    document.getElementById('bulkPauseBtn').classList.remove('hidden');
    document.getElementById('bulkStopBtn').classList.remove('hidden');
    document.getElementById('bulkPauseBtnText').innerText = 'পজ করুন';

    const targetCat = document.getElementById('targetCategoryId').value;
    const targetVen = document.getElementById('targetVendorId').value;
    const stockQty = document.getElementById('defaultStockQty').value;
    const duplicateAction = document.querySelector('input[name="duplicateAction"]:checked')?.value || 'update';

    appendBulkLog(`🚀 Shwapno বাল্ক ইমপোর্ট শুরু হয়েছে (মোট নির্বাচিত: ${bulkStats.total}টি)...`, 'info');

    while (bulkIndex < bulkQueue.length && isBulkRunning) {
        if (isBulkPaused) {
            await new Promise(r => setTimeout(r, 250));
            continue;
        }

        const item = bulkQueue[bulkIndex];
        const p = item.product;
        const rowIdx = item.index;

        document.getElementById('bulkProgressStatusText').innerText = `প্রক্রিয়া চলছে: "${p.name}" (${bulkIndex + 1}/${bulkStats.total})`;

        // Check if duplicate and mode is 'skip'
        if (p.exists_in_db && duplicateAction === 'skip') {
            bulkStats.skipped++;
            bulkStats.processed++;
            appendBulkLog(`⏭ স্কিপ (বিদ্যমান পণ্য): "${p.name}"`, 'skip');
            bulkIndex++;
            updateBulkUiCounters();
            continue;
        }

        appendBulkLog(`📥 [${bulkIndex + 1}/${bulkStats.total}] ইমপোর্ট হচ্ছে: "${p.name}" (ছবি ডাউনলোড ও সেভ)...`, 'info');

        try {
            const formData = new FormData();
            formData.append('csrf_token', csrfToken);
            formData.append('name', p.name);
            formData.append('sku', p.sku || '');
            formData.append('sell_price', p.sell_price);
            if (p.old_price) formData.append('old_price', p.old_price);
            formData.append('category_id', targetCat);
            formData.append('vendor_id', targetVen);
            formData.append('stock_qty', stockQty);
            formData.append('unit_type', p.unit_type);
            formData.append('base_unit', p.base_unit);
            formData.append('image_url', p.image_url);
            if (p.variants && p.variants.length > 0) {
                formData.append('variants', JSON.stringify(p.variants));
            }

            const res = await fetch(`${baseUri}/admin/products/shwapno-import-single`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const data = await res.json();

            if (data.success) {
                bulkStats.imported++;
                const actWord = data.status === 'updated' ? 'আপডেট' : 'তৈরি';
                appendBulkLog(`✅ সফলভাবে ${actWord}: "${p.name}" (ID #${data.id})`, 'success');
                
                // Update table row
                const statusCol = document.getElementById(`item-status-col-${rowIdx}`);
                if (statusCol) {
                    statusCol.innerHTML = `
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-600 text-white text-[10px] font-bold">
                            <ion-icon name="checkmark-done" class="text-xs"></ion-icon> ইমপোর্ট সম্পন্ন (#${data.id})
                        </span>
                    `;
                }
                const rowBtn = document.getElementById(`single-import-btn-${rowIdx}`);
                if (rowBtn) {
                    rowBtn.className = 'inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-emerald-600 text-white text-xs font-bold shadow-xs';
                    rowBtn.innerHTML = `<ion-icon name="checkmark-circle" class="text-sm"></ion-icon> <span>সম্পন্ন!</span>`;
                }
                p.exists_in_db = true;
                p.existing_id = data.id;
            } else {
                bulkStats.errors++;
                appendBulkLog(`❌ ত্রুটি (${p.name}): ${data.message || 'অজানা সমস্যা'}`, 'error');
            }
        } catch (err) {
            console.error('Import item error:', err);
            bulkStats.errors++;
            appendBulkLog(`❌ নেটওয়ার্ক/সার্ভার ত্রুটি: ${p.name}`, 'error');
        }

        bulkStats.processed++;
        bulkIndex++;
        updateBulkUiCounters();

        // Brief delay for gentle server load
        await new Promise(r => setTimeout(r, 200));
    }

    if (isBulkRunning) {
        isBulkRunning = false;
        document.getElementById('bulkProgressStatusText').innerText = '🎉 বাল্ক ইমপোর্ট প্রক্রিয়া সম্পন্ন হয়েছে!';
        appendBulkLog(`🏁 প্রক্রিয়া সম্পন্ন! মোট সফল: ${bulkStats.imported}টি, স্কিপ: ${bulkStats.skipped}টি, ত্রুটি: ${bulkStats.errors}টি।`, 'done');
        
        document.getElementById('bulkPauseBtn').classList.add('hidden');
        document.getElementById('bulkStopBtn').classList.add('hidden');
        document.getElementById('bulkViewProductsBtn').classList.remove('hidden');

        const startBtn = document.getElementById('bulkStartBtn');
        startBtn.innerHTML = `<ion-icon name="refresh" class="text-sm"></ion-icon> <span>পুনরায় চালান</span>`;
        startBtn.classList.remove('hidden');
    }
}

// Pause / Resume bulk import
function togglePauseBulkImport() {
    if (!isBulkRunning) return;
    isBulkPaused = !isBulkPaused;
    const btnText = document.getElementById('bulkPauseBtnText');
    if (isBulkPaused) {
        btnText.innerText = 'চালিয়ে যান (Resume)';
        appendBulkLog('⏸ প্রক্রিয়া সাময়িক স্থগিত (Paused) করা হয়েছে...', 'skip');
        document.getElementById('bulkProgressStatusText').innerText = 'প্রক্রিয়া সাময়িক স্থগিত (Paused)';
    } else {
        btnText.innerText = 'পজ করুন';
        appendBulkLog('▶️ প্রক্রিয়া পুনরায় চালু করা হলো...', 'info');
    }
}

// Stop bulk import
function stopBulkImport() {
    if (!isBulkRunning) return;
    if (confirm('আপনি কি সত্যি ইমপোর্ট থামাতে চান?')) {
        isBulkRunning = false;
        appendBulkLog('🛑 ব্যবহারকারী কর্তৃক প্রক্রিয়া থামানো হয়েছে।', 'error');
        document.getElementById('bulkProgressStatusText').innerText = 'প্রক্রিয়া থামানো হয়েছে';
        
        document.getElementById('bulkPauseBtn').classList.add('hidden');
        document.getElementById('bulkStopBtn').classList.add('hidden');
        const startBtn = document.getElementById('bulkStartBtn');
        startBtn.innerHTML = `<ion-icon name="refresh" class="text-sm"></ion-icon> <span>পুনরায় শুরু করুন</span>`;
        startBtn.classList.remove('hidden');
    }
}

// Utility: escape HTML
function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
</script>
