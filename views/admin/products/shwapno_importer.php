<?php
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
?>

<div class="space-y-6">
    <!-- Header Hero Section -->
    <div class="rounded-3xl p-6 sm:p-8 shadow-xl relative overflow-hidden text-white" 
         style="background: linear-gradient(135deg, #022c22 0%, #064e3b 45%, #0f766e 75%, #0f172a 100%) !important; color: #ffffff !important; border: 1px solid #134e4a;">
        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-28 top-0 w-52 h-52 bg-teal-400/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider mb-2" style="color: #6ee7b7 !important;">
                    <a href="<?= $base ?>/admin/products" class="hover:text-white transition-colors" style="color: #a7f3d0 !important;">পণ্য ও ইনভেন্টরি</a>
                    <span style="color: #6ee7b7 !important;">›</span>
                    <span style="color: #ffffff !important;">Shwapno ক্যাটাগরি স্ক্র্যাপার ও অটো-ইমপোর্টার</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight flex items-center gap-3" style="color: #ffffff !important;">
                    <span class="p-2.5 rounded-2xl border flex items-center justify-center" style="background: rgba(16, 185, 129, 0.2) !important; border-color: rgba(52, 211, 153, 0.4) !important; color: #6ee7b7 !important;">
                        <ion-icon name="cloud-download-outline" class="text-2xl"></ion-icon>
                    </span>
                    Shwapno Category Auto-Importer
                </h1>
                <p class="text-sm mt-2 max-w-2xl leading-relaxed" style="color: #cbd5e1 !important;">
                    স্বপ্ন (<span class="font-bold" style="color: #6ee7b7 !important;">Shwapno.com</span>)-এর যেকোনো ক্যাটাগরি থেকে এক ক্লিকে হাই-রেজ্যুলেশন ছবি, সঠিক বিক্রয়মূল্য, একক এবং ভ্যারিয়েন্ট সহ সরাসরি আপনার স্টোরের ডাটাবেসে পণ্য ইমপোর্ট করুন।
                </p>
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <button type="button" 
                        onclick="openCategorySetupModal()" 
                        class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-400 via-amber-300 to-amber-400 hover:from-amber-300 hover:to-amber-500 text-slate-950 text-xs font-black transition-all flex items-center gap-2 shadow-lg shadow-amber-950/40 transform hover:scale-105 active:scale-95 border border-amber-200 cursor-pointer">
                    <ion-icon name="folder-open" class="text-base text-amber-950"></ion-icon>
                    <span>১-ক্লিকে সব ক্যাটাগরি যুক্ত করুন</span>
                </button>
                <a href="<?= $base ?>/admin/products/image-finder" class="px-4 py-2.5 rounded-xl bg-emerald-500/20 hover:bg-emerald-500/30 backdrop-blur-md border border-emerald-400/40 text-emerald-200 hover:text-white text-xs font-bold transition-all flex items-center gap-2 shadow-sm">
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

    <!-- Hierarchical Category Explorer & Search Section -->
    <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-xs border border-secondary-200 space-y-6">
        <div>
            <!-- Explorer Header Bar -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4 pb-4 border-b border-secondary-100">
                <div>
                    <h3 class="text-base font-black text-secondary-900 flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center text-lg shadow-2xs">
                            <ion-icon name="git-network-outline"></ion-icon>
                        </span>
                        <span>স্বপ্নের ক্যাটাগরি এক্সপ্লোরার (হায়ারার্কিক্যাল ড্রিল-ডাউন)</span>
                    </h3>
                    <p class="text-xs text-secondary-500 font-medium mt-1">
                        মূল ক্যাটাগরি ➔ সাব-ক্যাটাগরি ➔ সাব-সাব ক্যাটাগরি ড্রিল-ডাউন করে সরাসরি নির্দিষ্ট ক্যাটাগরির পণ্য লোড করুন
                    </p>
                </div>

                <div class="flex items-center gap-2.5 flex-wrap">
                    <!-- View Mode Toggle Buttons -->
                    <div class="inline-flex p-1 bg-secondary-100 rounded-xl border border-secondary-200">
                        <button type="button" id="btnViewDrillDown" onclick="setExplorerViewMode('drilldown')" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 bg-white text-emerald-800 shadow-xs cursor-pointer">
                            <ion-icon name="apps-outline" class="text-sm"></ion-icon>
                            <span>ড্রিল-ডাউন ভিউ</span>
                        </button>
                        <button type="button" id="btnViewTree" onclick="setExplorerViewMode('tree')" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 text-secondary-600 hover:text-secondary-900 cursor-pointer">
                            <ion-icon name="list-outline" class="text-sm"></ion-icon>
                            <span>সম্পূর্ণ ট্রি ভিউ</span>
                        </button>
                    </div>

                    <button type="button" 
                            onclick="openCategorySetupModal()"
                            class="px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 text-xs font-black transition-all flex items-center gap-1.5 cursor-pointer hover:shadow-xs">
                        <ion-icon name="folder-open" class="text-sm text-emerald-700"></ion-icon>
                        <span>১-ক্লিকে সব ক্যাটাগরি তৈরি করুন</span>
                    </button>
                </div>
            </div>

            <!-- Breadcrumb Navigation Bar for Drill-Down -->
            <div id="explorerBreadcrumbs" class="flex items-center gap-2 text-xs font-bold mb-4 overflow-x-auto py-2 px-3.5 bg-secondary-50 rounded-2xl border border-secondary-200">
                <!-- Dynamically populated via JS -->
            </div>

            <!-- Instant Real-Time Search Filter inside Explorer -->
            <div class="relative mb-4">
                <ion-icon name="filter-outline" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-secondary-400 text-base"></ion-icon>
                <input type="text" 
                       id="categoryFilterInput" 
                       oninput="handleCategoryFilter(this.value)" 
                       placeholder="১১৮টি ক্যাটাগরির মধ্যে খুঁজুন (যেমন: চাল, সয়াবিন তেল, মসলা, দুধ, বিস্কুট, চা, কফি, নুডলস, মাছ, মাংস)..." 
                       class="w-full pl-10 pr-10 py-2.5 bg-white border border-secondary-200 rounded-xl text-xs font-semibold text-secondary-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all shadow-2xs">
                <button type="button" onclick="clearCategoryFilter()" id="clearCatFilterBtn" class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-secondary-400 hover:text-secondary-600 text-sm cursor-pointer">
                    <ion-icon name="close-circle"></ion-icon>
                </button>
            </div>

            <!-- View 1: Drill-Down Grid Container -->
            <div id="explorerDrillDownContainer" class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3.5" id="explorerCardsGrid">
                    <!-- Populated dynamically via JS -->
                </div>
            </div>

            <!-- View 2: Full Hierarchical Tree Accordion Container -->
            <div id="explorerTreeContainer" class="hidden space-y-2.5 max-h-[520px] overflow-y-auto pr-1">
                <!-- Populated dynamically via JS -->
            </div>

            <!-- Filter Results Container -->
            <div id="explorerFilterResultsContainer" class="hidden space-y-2">
                <div class="text-xs font-bold text-secondary-600 mb-2 flex items-center justify-between">
                    <span id="filterResultsTitle">সার্চ ফলাফল</span>
                    <button type="button" onclick="clearCategoryFilter()" class="text-xs text-emerald-600 hover:underline cursor-pointer">রিসেট করুন</button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5" id="explorerFilterResultsGrid">
                    <!-- Filter result cards -->
                </div>
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
                           placeholder="উদাহরণ: rice, soybean-oil, tea, noodles, fresh-fruits অথবা https://www.shwapno.com/fresh-fruits"
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
                    <option value="auto" selected>✨ স্বয়ংক্রিয় / ক্যাটাগরি না থাকলে ছবি সহ অটো তৈরি করুন</option>
                    <option value="">-- কোনো ক্যাটাগরি ছাড়া --</option>
                    <?php
                    $renderHierarchyOptions = function($items, $depth = 0) use (&$renderHierarchyOptions) {
                        foreach ($items as $item) {
                            $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $depth);
                            $bullet = ($depth === 0) ? '📁 ' : ($depth === 1 ? '↳ 📂 ' : '↳ ↳ 📄 ');
                            echo '<option value="' . $item['id'] . '">' . $indent . $bullet . htmlspecialchars($item['name']) . '</option>';
                            if (!empty($item['children'])) {
                                $renderHierarchyOptions($item['children'], $depth + 1);
                            }
                        }
                    };
                    if (!empty($categoryHierarchy)) {
                        $renderHierarchyOptions($categoryHierarchy);
                    } else {
                        foreach ($categories as $c) {
                            echo '<option value="' . $c['id'] . '">' . htmlspecialchars($c['name']) . '</option>';
                        }
                    }
                    ?>
                </select>
                <p class="text-[10px] text-emerald-600 font-semibold mt-1">✓ ক্যাটাগরি না থাকলে হায়ারার্কি ও ইমেজ সহ নতুন ক্যাটাগরি অটো-তৈরি হবে</p>
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
                <div class="px-3 py-1.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-800 text-xs font-bold flex items-center gap-1.5">
                    <ion-icon name="albums-outline" class="text-base text-slate-600"></ion-icon>
                    <span>পৃষ্ঠা: <strong class="current-page-display text-slate-950">1</strong> / <span class="total-pages-display">1</span></span>
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

        <!-- All Duplicates Notification Banner -->
        <div id="allDuplicatesBanner" class="hidden p-4 rounded-2xl bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-300 text-amber-900 flex flex-col md:flex-row items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-200/80 border border-amber-300 text-amber-800 flex items-center justify-center text-xl flex-shrink-0">
                    <ion-icon name="alert-circle"></ion-icon>
                </div>
                <div>
                    <h4 class="text-xs font-black text-amber-950 flex items-center gap-1.5">
                        <span>এই পৃষ্ঠার সব ৫০টি পণ্য ইতিমধ্যে আপনার ডাটাবেসে সেভ আছে!</span>
                    </h4>
                    <p class="text-[11px] text-amber-800 mt-0.5" id="allDuplicatesBannerSub">
                        নতুন পণ্য পেতে পরবর্তী পৃষ্ঠা দেখুন অথবা নিচে 'বিদ্যমান পণ্য লুকান' ফিল্টার ব্যবহার করুন।
                    </p>
                </div>
            </div>
            <button type="button" 
                    id="allDuplicatesNextBtn"
                    onclick="loadNextPage()" 
                    class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-black transition-all shadow-md shadow-emerald-700/20 flex items-center gap-1.5 cursor-pointer whitespace-nowrap active:scale-95">
                <span id="allDuplicatesNextBtnText">পরবর্তী পৃষ্ঠা (Next Page) লোড করুন</span>
                <ion-icon name="arrow-forward" class="text-sm"></ion-icon>
            </button>
        </div>

        <!-- Top Pagination & Filter Bar -->
        <div class="bg-white rounded-2xl p-3 sm:p-4 shadow-xs border border-secondary-200 flex flex-col sm:flex-row items-center justify-between gap-3">
            <!-- Left: Page status & navigation buttons -->
            <div class="flex items-center gap-2 flex-wrap">
                <button type="button" 
                        id="prevPageBtn" 
                        onclick="loadPrevPage()" 
                        disabled
                        class="px-3 py-1.5 rounded-xl bg-secondary-100 hover:bg-secondary-200 disabled:opacity-40 disabled:cursor-not-allowed text-secondary-800 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer">
                    <ion-icon name="chevron-back-outline"></ion-icon>
                    <span>পূর্ববর্তী পৃষ্ঠা</span>
                </button>

                <div class="px-3.5 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-extrabold flex items-center gap-1.5">
                    <ion-icon name="albums-outline" class="text-emerald-700"></ion-icon>
                    <span>পৃষ্ঠা <strong class="current-page-display text-emerald-800">1</strong> / <span class="total-pages-display">1</span></span>
                    <span class="text-secondary-400 font-normal text-[11px]">(মোট <span class="total-items-display">0</span>টি পণ্য)</span>
                </div>

                <button type="button" 
                        id="nextPageBtn" 
                        onclick="loadNextPage()" 
                        disabled
                        class="px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 disabled:opacity-40 disabled:cursor-not-allowed text-white text-xs font-black transition-all shadow-xs flex items-center gap-1 cursor-pointer">
                    <span>পরবর্তী পৃষ্ঠা</span>
                    <ion-icon name="chevron-forward-outline"></ion-icon>
                </button>

                <button type="button" 
                        id="loadMoreBtn" 
                        onclick="loadMoreProducts()" 
                        disabled
                        class="px-3 py-1.5 rounded-xl bg-teal-50 hover:bg-teal-100 text-teal-800 border border-teal-200 text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed"
                        title="বর্তমান তালিকার নিচে পরবর্তী ৫০টি পণ্য যুক্ত করুন">
                    <ion-icon name="add-circle-outline" class="text-sm text-teal-600"></ion-icon>
                    <span>আরও ৫০টি পণ্য যোগ করুন</span>
                </button>
            </div>

            <!-- Right: Filter out duplicates toggle -->
            <div class="flex items-center gap-2">
                <button type="button" 
                        id="toggleHideExistingBtn"
                        onclick="toggleHideExisting()" 
                        class="px-3 py-1.5 rounded-xl bg-secondary-100 hover:bg-secondary-200 text-secondary-700 text-xs font-bold transition-all flex items-center gap-1.5 border border-secondary-200 cursor-pointer">
                    <ion-icon name="eye-off-outline" class="text-sm"></ion-icon>
                    <span id="toggleHideExistingText">বিদ্যমান পণ্য লুকান</span>
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
                            <th class="py-3.5 px-4">ক্যাটাগরি</th>
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

        <!-- Bottom Pagination Bar -->
        <div class="bg-white rounded-2xl p-3 sm:p-4 shadow-xs border border-secondary-200 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="flex items-center gap-2 flex-wrap">
                <button type="button" 
                        id="prevPageBtnBottom" 
                        onclick="loadPrevPage()" 
                        disabled
                        class="px-3 py-1.5 rounded-xl bg-secondary-100 hover:bg-secondary-200 disabled:opacity-40 disabled:cursor-not-allowed text-secondary-800 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer">
                    <ion-icon name="chevron-back-outline"></ion-icon>
                    <span>পূর্ববর্তী পৃষ্ঠা</span>
                </button>

                <div class="px-3.5 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-extrabold flex items-center gap-1.5">
                    <ion-icon name="albums-outline" class="text-emerald-700"></ion-icon>
                    <span>পৃষ্ঠা <strong class="current-page-display text-emerald-800">1</strong> / <span class="total-pages-display">1</span></span>
                    <span class="text-secondary-400 font-normal text-[11px]">(মোট <span class="total-items-display">0</span>টি পণ্য)</span>
                </div>

                <button type="button" 
                        id="nextPageBtnBottom" 
                        onclick="loadNextPage()" 
                        disabled
                        class="px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 disabled:opacity-40 disabled:cursor-not-allowed text-white text-xs font-black transition-all shadow-xs flex items-center gap-1 cursor-pointer">
                    <span>পরবর্তী পৃষ্ঠা</span>
                    <ion-icon name="chevron-forward-outline"></ion-icon>
                </button>

                <button type="button" 
                        id="loadMoreBtnBottom" 
                        onclick="loadMoreProducts()" 
                        disabled
                        class="px-3 py-1.5 rounded-xl bg-teal-50 hover:bg-teal-100 text-teal-800 border border-teal-200 text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                    <ion-icon name="add-circle-outline" class="text-sm text-teal-600"></ion-icon>
                    <span>আরও ৫০টি পণ্য যোগ করুন</span>
                </button>
            </div>

            <div class="text-xs text-secondary-500 font-medium">
                পৃষ্ঠা পরিবর্তন করলে নতুন পণ্য তালিকা লোড হবে
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
        <div class="px-6 py-4 text-white flex items-center justify-between border-b border-secondary-700"
             style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%) !important; color: #ffffff !important;">
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
                <button type="button" id="bulkNextPageBtn" onclick="closeBulkImportModal(); loadNextPage();" class="hidden px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-black transition-colors flex items-center gap-1.5 shadow-xs cursor-pointer">
                    <span id="bulkNextPageBtnText">পরবর্তী পৃষ্ঠা লোড করুন</span>
                    <ion-icon name="arrow-forward"></ion-icon>
                </button>
                <button type="button" onclick="closeBulkImportModal()" class="px-4 py-2.5 rounded-xl bg-secondary-100 hover:bg-secondary-200 text-secondary-700 text-xs font-bold transition-colors">
                    বন্ধ করুন
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================== -->
<!-- 1-CLICK ALL CATEGORIES SETUP MODAL                         -->
<!-- ========================================================== -->
<div id="categorySetupModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 sm:p-6 transition-all duration-200">
    <div class="bg-white rounded-3xl shadow-2xl border border-secondary-200 w-full max-w-4xl overflow-hidden flex flex-col max-h-[92vh] transform transition-all scale-95 opacity-0" id="categorySetupModalContent">
        
        <!-- Modal Top Bar -->
        <div class="px-6 py-5 text-white flex items-center justify-between border-b border-secondary-700"
             style="background: linear-gradient(135deg, #022c22 0%, #064e3b 45%, #0f766e 80%, #0f172a 100%) !important; color: #ffffff !important;">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-emerald-500/20 border border-emerald-400/40 text-emerald-300 flex items-center justify-center text-2xl shadow-inner">
                    <ion-icon name="git-network"></ion-icon>
                </div>
                <div>
                    <h3 class="text-lg font-black text-white flex items-center gap-2.5">
                        <span>১-ক্লিকে সব ক্যাটাগরি ও সাব-ক্যাটাগরি ডাটাবেসে তৈরি করুন</span>
                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/30 border border-emerald-400/40 text-[10px] text-emerald-200 font-bold uppercase tracking-wider">Shwapno Full Tree</span>
                    </h3>
                    <p class="text-xs text-emerald-100/80 mt-0.5">
                        মূল ক্যাটাগরি (১৪টি), সাব-ক্যাটাগরি (৬১টি) ও সাব-সাব ক্যাটাগরি (৪৩টি) সহ মোট ১১৮টি ক্যাটাগরি সঠিক প্যারেন্ট রিলেশন এবং ছবি সহ ডাটাবেসে সেভ হবে।
                    </p>
                </div>
            </div>

            <button type="button" onclick="closeCategorySetupModal()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-white/80 hover:text-white flex items-center justify-center transition-colors cursor-pointer">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-5 overflow-y-auto flex-1">
            <!-- Progress Bar Section -->
            <div class="space-y-2 bg-emerald-50/60 p-4 rounded-2xl border border-emerald-100">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-bold text-secondary-800" id="catSetupProgressStatusText">হায়ারার্কিক্যাল ক্যাটাগরি সিঙ্ক শুরু করতে প্রস্তুত...</span>
                    <span class="font-mono font-black text-emerald-700 text-sm" id="catSetupProgressPct">0%</span>
                </div>
                <div class="w-full h-3.5 bg-secondary-200/80 rounded-full overflow-hidden p-0.5">
                    <div id="catSetupProgressBar" class="h-full bg-gradient-to-r from-emerald-500 via-teal-400 to-emerald-600 rounded-full transition-all duration-300" style="width: 0%;"></div>
                </div>
            </div>

            <!-- KPI Counters -->
            <div class="grid grid-cols-4 gap-3">
                <div class="p-3 bg-secondary-50 border border-secondary-200 rounded-2xl text-center">
                    <div class="text-[10px] font-bold text-secondary-500 uppercase">মোট ক্যাটাগরি</div>
                    <div class="text-lg font-black text-secondary-900 mt-0.5" id="catSetupTotal">118</div>
                </div>
                <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-2xl text-center">
                    <div class="text-[10px] font-bold text-emerald-600 uppercase">নতুন তৈরি (ছবি সহ)</div>
                    <div class="text-lg font-black text-emerald-700 mt-0.5" id="catSetupCreated">0</div>
                </div>
                <div class="p-3 bg-blue-50 border border-blue-200 rounded-2xl text-center">
                    <div class="text-[10px] font-bold text-blue-600 uppercase">বিদ্যমান / আপডেট</div>
                    <div class="text-lg font-black text-blue-700 mt-0.5" id="catSetupExisting">0</div>
                </div>
                <div class="p-3 bg-purple-50 border border-purple-200 rounded-2xl text-center">
                    <div class="text-[10px] font-bold text-purple-600 uppercase">হায়ারার্কি স্তর</div>
                    <div class="text-lg font-black text-purple-700 mt-0.5">৩ টি লেভেল</div>
                </div>
            </div>

            <!-- Filter Tabs for Category Levels in Modal -->
            <div class="flex items-center justify-between gap-2 border-b border-secondary-200 pb-2">
                <div class="flex items-center gap-1.5 overflow-x-auto">
                    <button type="button" onclick="filterModalCatTab('all')" id="modalTabAll" class="px-3 py-1 rounded-lg text-xs font-bold transition-colors bg-emerald-100 text-emerald-800 border border-emerald-200 cursor-pointer">
                        সকল (১১৮)
                    </button>
                    <button type="button" onclick="filterModalCatTab(1)" id="modalTabL1" class="px-3 py-1 rounded-lg text-xs font-bold transition-colors text-secondary-600 hover:bg-secondary-100 cursor-pointer">
                        মূল বিভাগ (১৪)
                    </button>
                    <button type="button" onclick="filterModalCatTab(2)" id="modalTabL2" class="px-3 py-1 rounded-lg text-xs font-bold transition-colors text-secondary-600 hover:bg-secondary-100 cursor-pointer">
                        সাব-ক্যাটাগরি (৬১)
                    </button>
                    <button type="button" onclick="filterModalCatTab(3)" id="modalTabL3" class="px-3 py-1 rounded-lg text-xs font-bold transition-colors text-secondary-600 hover:bg-secondary-100 cursor-pointer">
                        সাব-সাব ক্যাটাগরি (৪৩)
                    </button>
                </div>
                <span class="text-[11px] text-secondary-400 font-medium">প্যারেন্ট ক্রমানুসারে সিঙ্ক হবে</span>
            </div>

            <!-- Categories Tree/List Status in Modal -->
            <div class="space-y-2">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5 max-h-64 overflow-y-auto p-1" id="catSetupListContainer">
                    <!-- Populated dynamically via JS -->
                </div>
            </div>

            <!-- Terminal Log Console -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-bold text-secondary-600 flex items-center gap-1.5">
                        <ion-icon name="terminal-outline" class="text-secondary-500"></ion-icon>
                        <span>লাইভ হায়ারার্কি লগ টার্মিনাল</span>
                    </span>
                    <button type="button" onclick="clearCatSetupLogs()" class="text-[11px] text-secondary-400 hover:text-secondary-600 font-medium cursor-pointer">
                        ক্লিয়ার লগ
                    </button>
                </div>
                <div class="w-full h-36 bg-slate-950 text-slate-200 rounded-2xl p-3.5 font-mono text-[11px] overflow-y-auto border border-slate-800 space-y-1" id="catSetupLogConsole">
                    <div class="text-slate-500 italic">"সব ক্যাটাগরি তৈরি শুরু করুন" বাটনে চাপুন...</div>
                </div>
            </div>
        </div>

        <!-- Modal Footer Controls -->
        <div class="px-6 py-4 bg-white border-t border-secondary-200 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="text-xs text-secondary-600" id="catSetupFooterSummary">
                মোট ১১৮টি ক্যাটাগরি ৩-স্তরে প্যারেন্ট-চাইল্ড লিংক ও ছবি সহ আপনার ডাটাবেসে সেভ হবে
            </div>

            <div class="flex items-center gap-2.5">
                <button type="button" id="catSetupStartBtn" onclick="startBatchCategorySetup()" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-black transition-all shadow-md shadow-emerald-700/20 flex items-center gap-2 cursor-pointer">
                    <ion-icon name="play" class="text-sm"></ion-icon>
                    <span id="catSetupStartBtnText">সব ক্যাটাগরি তৈরি শুরু করুন (১১৮টি)</span>
                </button>
                <button type="button" onclick="closeCategorySetupModal()" class="px-4 py-2.5 rounded-xl bg-secondary-100 hover:bg-secondary-200 text-secondary-700 text-xs font-bold transition-colors cursor-pointer">
                    বন্ধ করুন
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Escape HTML helper
function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// Global State
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

// Pagination State
let currentFetchPage = 1;
let totalFetchPages = 1;
let totalFetchCount = 0;
let hasNextPage = false;
let hasPrevPage = false;
let isHideExistingActive = false;

const csrfToken = '<?= \Core\CSRF::token() ?>';
const baseUri = '<?= $base ?>';

// Shwapno Catalog Data from Controller
const categoryTree = <?= json_encode($categoryTree ?? [], JSON_UNESCAPED_UNICODE) ?>;
const categoryFlatList = <?= json_encode($categoryFlatList ?? [], JSON_UNESCAPED_UNICODE) ?>;

// Category Explorer State
let explorerCurrentPath = []; // [] = Level 1 (Roots), [mainSlug] = Level 2 (Subs), [mainSlug, subSlug] = Level 3 (Sub-Subs)
let explorerViewMode = 'drilldown'; // 'drilldown' or 'tree'
let modalActiveTab = 'all'; // 'all', 1, 2, 3
let isCatSetupRunning = false;

// Active Category Context for Fetch and Import
let currentCategorySlug = '';
let currentCategoryName = '';
let currentCategoryParentSlug = '';
let currentCategoryParentName = '';
let currentCategoryLevel = 1;

// ============================================================
// HIERARCHICAL CATEGORY EXPLORER ENGINE
// ============================================================

function initCategoryExplorer() {
    renderBreadcrumbs();
    renderExplorerDrillDown();
    renderExplorerTree();
}

function setExplorerViewMode(mode) {
    explorerViewMode = mode;
    const btnDrill = document.getElementById('btnViewDrillDown');
    const btnTree = document.getElementById('btnViewTree');
    const drillContainer = document.getElementById('explorerDrillDownContainer');
    const treeContainer = document.getElementById('explorerTreeContainer');
    const breadcrumbs = document.getElementById('explorerBreadcrumbs');

    if (mode === 'drilldown') {
        btnDrill.className = 'px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 bg-white text-emerald-800 shadow-xs cursor-pointer';
        btnTree.className = 'px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 text-secondary-600 hover:text-secondary-900 cursor-pointer';
        drillContainer.classList.remove('hidden');
        treeContainer.classList.add('hidden');
        breadcrumbs.classList.remove('hidden');
        renderBreadcrumbs();
        renderExplorerDrillDown();
    } else {
        btnTree.className = 'px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 bg-white text-emerald-800 shadow-xs cursor-pointer';
        btnDrill.className = 'px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 text-secondary-600 hover:text-secondary-900 cursor-pointer';
        drillContainer.classList.add('hidden');
        treeContainer.classList.remove('hidden');
        breadcrumbs.classList.add('hidden');
    }
}

function renderBreadcrumbs() {
    const bar = document.getElementById('explorerBreadcrumbs');
    if (!bar) return;

    let html = `
        <button type="button" onclick="navigateExplorerTo([])" class="hover:text-emerald-700 flex items-center gap-1.5 transition-colors cursor-pointer ${explorerCurrentPath.length === 0 ? 'text-emerald-800 font-black' : 'text-secondary-500 font-semibold'}">
            <ion-icon name="home-outline" class="text-sm"></ion-icon>
            <span>সকল মূল ক্যাটাগরি (${categoryTree.length})</span>
        </button>
    `;

    if (explorerCurrentPath.length >= 1) {
        const mainCat = categoryTree.find(m => m.slug === explorerCurrentPath[0]);
        if (mainCat) {
            html += `
                <span class="text-secondary-400">›</span>
                <button type="button" onclick="navigateExplorerTo(['${mainCat.slug}'])" class="hover:text-emerald-700 transition-colors cursor-pointer ${explorerCurrentPath.length === 1 ? 'text-emerald-800 font-black' : 'text-secondary-500 font-semibold'}">
                    <span>${escapeHtml(mainCat.name_bn)}</span>
                </button>
            `;
        }
    }

    if (explorerCurrentPath.length >= 2) {
        const mainCat = categoryTree.find(m => m.slug === explorerCurrentPath[0]);
        const subCat = (mainCat && mainCat.children) ? mainCat.children.find(s => s.slug === explorerCurrentPath[1]) : null;
        if (subCat) {
            html += `
                <span class="text-secondary-400">›</span>
                <span class="text-emerald-800 font-black">
                    ${escapeHtml(subCat.name_bn)}
                </span>
            `;
        }
    }

    if (explorerCurrentPath.length > 0) {
        html += `
            <div class="ml-auto">
                <button type="button" onclick="goBackExplorer()" class="px-2.5 py-1 rounded-xl bg-secondary-200 hover:bg-secondary-300 text-secondary-800 text-[11px] font-bold transition-all flex items-center gap-1 cursor-pointer">
                    <ion-icon name="arrow-back-outline"></ion-icon>
                    <span>পেছনে যান</span>
                </button>
            </div>
        `;
    }

    bar.innerHTML = html;
}

function navigateExplorerTo(path) {
    explorerCurrentPath = path;
    renderBreadcrumbs();
    renderExplorerDrillDown();
}

function goBackExplorer() {
    if (explorerCurrentPath.length > 0) {
        explorerCurrentPath.pop();
        renderBreadcrumbs();
        renderExplorerDrillDown();
    }
}

function renderExplorerDrillDown() {
    const grid = document.getElementById('explorerCardsGrid');
    if (!grid) return;

    // Level 1: Root Categories
    if (explorerCurrentPath.length === 0) {
        grid.innerHTML = categoryTree.map(main => {
            const childrenCount = (main.children || []).length;
            const fullDisplayName = `${main.name_bn} (${main.name_en})`;
            return `
                <div class="group bg-gradient-to-b from-white to-secondary-50/50 rounded-2xl border border-secondary-200 hover:border-emerald-300 hover:shadow-md transition-all p-3.5 flex flex-col justify-between gap-3">
                    <div class="flex items-start gap-3 cursor-pointer" onclick="navigateExplorerTo(['${main.slug}'])">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center text-xl flex-shrink-0 group-hover:scale-105 transition-transform shadow-2xs">
                            <ion-icon name="${main.icon || 'folder-outline'}"></ion-icon>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h4 class="text-xs font-black text-secondary-900 group-hover:text-emerald-800 transition-colors truncate">
                                ${escapeHtml(main.name_bn)}
                            </h4>
                            <p class="text-[10px] text-secondary-500 truncate mt-0.5">
                                ${escapeHtml(main.name_en)}
                            </p>
                            <div class="mt-1.5">
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-lg bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    <ion-icon name="layers-outline" class="text-xs"></ion-icon>
                                    <span>${childrenCount}টি সাব-ক্যাটাগরি</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-2.5 border-t border-secondary-100 flex items-center gap-2">
                        <button type="button" 
                                onclick="navigateExplorerTo(['${main.slug}'])"
                                class="flex-1 py-1.5 px-2 rounded-xl bg-secondary-100 hover:bg-emerald-50 hover:text-emerald-800 hover:border-emerald-300 border border-secondary-200 text-[11px] font-bold text-secondary-700 transition-all flex items-center justify-center gap-1 cursor-pointer">
                            <span>সাব-ক্যাটাগরি</span>
                            <ion-icon name="chevron-forward-outline" class="text-xs"></ion-icon>
                        </button>
                        <button type="button" 
                                onclick="selectAndFetchCategory('${main.slug}', '${escapeHtml(main.name_bn)}', null, null, 1)"
                                title="এই মূল ক্যাটাগরির পণ্য লোড করুন"
                                class="py-1.5 px-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-black transition-all flex items-center justify-center gap-1 shadow-2xs active:scale-95 cursor-pointer">
                            <ion-icon name="flash" class="text-xs text-amber-300"></ion-icon>
                            <span>পণ্য লোড</span>
                        </button>
                    </div>
                </div>
            `;
        }).join('');
        return;
    }

    // Level 2: Sub-Categories under Main
    if (explorerCurrentPath.length === 1) {
        const main = categoryTree.find(m => m.slug === explorerCurrentPath[0]);
        if (!main || !main.children || main.children.length === 0) {
            grid.innerHTML = `<div class="col-span-full p-6 text-center text-xs text-secondary-500 bg-secondary-50 rounded-2xl">এই ক্যাটাগরিতে কোনো সাব-ক্যাটাগরি নেই।</div>`;
            return;
        }

        const mainFullName = `${main.name_bn} (${main.name_en})`;

        grid.innerHTML = main.children.map(sub => {
            const subChildrenCount = (sub.children || []).length;
            const subFullName = `${sub.name_bn} (${sub.name_en})`;
            return `
                <div class="group bg-gradient-to-b from-white to-blue-50/30 rounded-2xl border border-secondary-200 hover:border-blue-300 hover:shadow-md transition-all p-3.5 flex flex-col justify-between gap-3">
                    <div class="flex items-start gap-3 cursor-pointer" onclick="${subChildrenCount > 0 ? `navigateExplorerTo(['${main.slug}', '${sub.slug}'])` : `selectAndFetchCategory('${sub.slug}', '${escapeHtml(subFullName)}', '${main.slug}', '${escapeHtml(mainFullName)}', 2)`}">
                        <div class="w-10 h-10 rounded-2xl bg-blue-50 border border-blue-200 text-blue-700 flex items-center justify-center text-xl flex-shrink-0 group-hover:scale-105 transition-transform shadow-2xs">
                            <ion-icon name="${sub.icon || main.icon || 'folder-outline'}"></ion-icon>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h4 class="text-xs font-black text-secondary-900 group-hover:text-blue-800 transition-colors truncate">
                                ${escapeHtml(sub.name_bn)}
                            </h4>
                            <p class="text-[10px] text-secondary-500 truncate mt-0.5">
                                ${escapeHtml(sub.name_en)}
                            </p>
                            <div class="mt-1.5 flex items-center gap-1.5 flex-wrap">
                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-secondary-100 text-secondary-600">
                                    ${escapeHtml(main.name_bn)}
                                </span>
                                ${subChildrenCount > 0 ? `
                                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-purple-100 text-purple-800 border border-purple-200">
                                        ${subChildrenCount}টি সাব-সাব ক্যাটাগরি
                                    </span>
                                ` : ''}
                            </div>
                        </div>
                    </div>

                    <div class="pt-2.5 border-t border-secondary-100 flex items-center gap-2">
                        ${subChildrenCount > 0 ? `
                            <button type="button" 
                                    onclick="navigateExplorerTo(['${main.slug}', '${sub.slug}'])"
                                    class="flex-1 py-1.5 px-2 rounded-xl bg-secondary-100 hover:bg-purple-50 hover:text-purple-800 hover:border-purple-300 border border-secondary-200 text-[11px] font-bold text-secondary-700 transition-all flex items-center justify-center gap-1 cursor-pointer">
                                <span>সাব-সাব ক্যাটাগরি</span>
                                <ion-icon name="chevron-forward-outline" class="text-xs"></ion-icon>
                            </button>
                        ` : ''}
                        <button type="button" 
                                onclick="selectAndFetchCategory('${sub.slug}', '${escapeHtml(sub.name_bn)}', '${main.slug}', '${escapeHtml(main.name_bn)}', 2)"
                                class="${subChildrenCount > 0 ? '' : 'w-full'} py-1.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-black transition-all flex items-center justify-center gap-1 shadow-2xs active:scale-95 cursor-pointer">
                            <ion-icon name="flash" class="text-xs text-amber-300"></ion-icon>
                            <span>পণ্য লোড করুন</span>
                        </button>
                    </div>
                </div>
            `;
        }).join('');
        return;
    }

    // Level 3: Sub-Sub Categories
    if (explorerCurrentPath.length === 2) {
        const main = categoryTree.find(m => m.slug === explorerCurrentPath[0]);
        const sub = (main && main.children) ? main.children.find(s => s.slug === explorerCurrentPath[1]) : null;
        if (!sub || !sub.children || sub.children.length === 0) {
            grid.innerHTML = `<div class="col-span-full p-6 text-center text-xs text-secondary-500 bg-secondary-50 rounded-2xl">এই সাব-ক্যাটাগরিতে কোনো সাব-সাব ক্যাটাগরি নেই।</div>`;
            return;
        }

        const subFullName = `${sub.name_bn} (${sub.name_en})`;

        grid.innerHTML = sub.children.map(leaf => {
            const leafFullName = `${leaf.name_bn} (${leaf.name_en})`;
            return `
                <div class="group bg-gradient-to-b from-white to-purple-50/30 rounded-2xl border border-secondary-200 hover:border-purple-300 hover:shadow-md transition-all p-3.5 flex flex-col justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-purple-50 border border-purple-200 text-purple-700 flex items-center justify-center text-xl flex-shrink-0 group-hover:scale-105 transition-transform shadow-2xs">
                            <ion-icon name="${leaf.icon || sub.icon || 'document-outline'}"></ion-icon>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h4 class="text-xs font-black text-secondary-900 group-hover:text-purple-800 transition-colors truncate">
                                ${escapeHtml(leaf.name_bn)}
                            </h4>
                            <p class="text-[10px] text-secondary-500 truncate mt-0.5">
                                ${escapeHtml(leaf.name_en)}
                            </p>
                            <div class="mt-1.5 flex items-center gap-1.5 flex-wrap text-[9px] font-bold text-secondary-500">
                                <span>${escapeHtml(main.name_bn)}</span>
                                <span>›</span>
                                <span class="text-purple-700 font-bold">${escapeHtml(sub.name_bn)}</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-2.5 border-t border-secondary-100">
                        <button type="button" 
                                onclick="selectAndFetchCategory('${leaf.slug}', '${escapeHtml(leaf.name_bn)}', '${sub.slug}', '${escapeHtml(sub.name_bn)}', 3)"
                                class="w-full py-2 px-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-[11px] font-black transition-all flex items-center justify-center gap-1.5 shadow-xs active:scale-95 cursor-pointer">
                            <ion-icon name="cloud-download-outline" class="text-sm"></ion-icon>
                            <span>এই ক্যাটাগরির পণ্য লোড করুন</span>
                        </button>
                    </div>
                </div>
            `;
        }).join('');
    }
}

function renderExplorerTree() {
    const treeEl = document.getElementById('explorerTreeContainer');
    if (!treeEl) return;

    treeEl.innerHTML = categoryTree.map((main, mIdx) => {
        const mainFullName = `${main.name_bn} (${main.name_en})`;
        const subs = main.children || [];
        return `
            <div class="border border-secondary-200 rounded-2xl overflow-hidden bg-white mb-2 shadow-2xs">
                <!-- Level 1 Main Category Item -->
                <div class="p-3 bg-secondary-50/80 hover:bg-emerald-50/50 flex items-center justify-between gap-3 cursor-pointer transition-colors" onclick="toggleTreeSection('tree-main-${mIdx}')">
                    <div class="flex items-center gap-2.5 min-w-0 flex-1">
                        <ion-icon name="chevron-down-outline" class="text-secondary-400 text-sm transition-transform" id="tree-icon-tree-main-${mIdx}"></ion-icon>
                        <div class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm flex-shrink-0">
                            <ion-icon name="${main.icon || 'folder-outline'}"></ion-icon>
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="text-xs font-black text-secondary-900">${escapeHtml(main.name_bn)}</span>
                            <span class="text-[10px] text-secondary-500 font-normal ml-1">(${escapeHtml(main.name_en)})</span>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200 whitespace-nowrap">
                            ${subs.length}টি সাব-ক্যাটাগরি
                        </span>
                    </div>
                    <button type="button" 
                            onclick="event.stopPropagation(); selectAndFetchCategory('${main.slug}', '${escapeHtml(main.name_bn)}', null, null, 1)"
                            class="px-2.5 py-1 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-bold transition-all flex items-center gap-1 shadow-2xs cursor-pointer">
                        <ion-icon name="flash" class="text-xs text-amber-300"></ion-icon>
                        <span>লোড</span>
                    </button>
                </div>

                <!-- Level 2 Sub-Categories List -->
                <div id="tree-main-${mIdx}" class="divide-y divide-secondary-100 border-t border-secondary-100">
                    ${subs.map((sub, sIdx) => {
                        const subFullName = `${sub.name_bn} (${sub.name_en})`;
                        const leaves = sub.children || [];
                        return `
                            <div class="pl-6 pr-3 py-2.5 bg-white hover:bg-secondary-50/60 transition-colors">
                                <div class="flex items-center justify-between gap-2.5">
                                    <div class="flex items-center gap-2 min-w-0 flex-1 ${leaves.length > 0 ? 'cursor-pointer' : ''}" onclick="${leaves.length > 0 ? `toggleTreeSection('tree-sub-${mIdx}-${sIdx}')` : ''}">
                                        ${leaves.length > 0 ? `
                                            <ion-icon name="chevron-down-outline" class="text-secondary-400 text-xs transition-transform" id="tree-icon-tree-sub-${mIdx}-${sIdx}"></ion-icon>
                                        ` : '<span class="w-3"></span>'}
                                        <span class="text-emerald-600 font-bold text-xs">↳</span>
                                        <div class="min-w-0 flex-1">
                                            <span class="text-xs font-bold text-secondary-800">${escapeHtml(sub.name_bn)}</span>
                                            <span class="text-[10px] text-secondary-400 font-normal ml-1">(${escapeHtml(sub.name_en)})</span>
                                        </div>
                                        ${leaves.length > 0 ? `
                                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-purple-100 text-purple-800 border border-purple-200">
                                                ${leaves.length}টি
                                            </span>
                                        ` : ''}
                                    </div>
                                    <button type="button" 
                                            onclick="selectAndFetchCategory('${sub.slug}', '${escapeHtml(sub.name_bn)}', '${main.slug}', '${escapeHtml(main.name_bn)}', 2)"
                                            class="px-2 py-1 rounded-lg bg-teal-50 hover:bg-teal-600 hover:text-white text-teal-800 text-[10px] font-bold border border-teal-200 transition-all flex items-center gap-1 cursor-pointer">
                                        <ion-icon name="flash" class="text-xs"></ion-icon>
                                        <span>লোড</span>
                                    </button>
                                </div>

                                <!-- Level 3 Sub-Sub Categories List -->
                                ${leaves.length > 0 ? `
                                    <div id="tree-sub-${mIdx}-${sIdx}" class="mt-2 pl-6 space-y-1.5 border-l-2 border-purple-200 ml-4 pb-1">
                                        ${leaves.map(leaf => {
                                            const leafFullName = `${leaf.name_bn} (${leaf.name_en})`;
                                            return `
                                                <div class="py-1 px-2 rounded-lg hover:bg-purple-50 flex items-center justify-between gap-2 text-xs transition-colors">
                                                    <div class="min-w-0 flex-1">
                                                        <span class="text-purple-600 font-bold">↳</span>
                                                        <span class="font-semibold text-secondary-700 ml-1">${escapeHtml(leaf.name_bn)}</span>
                                                        <span class="text-[10px] text-secondary-400 font-normal ml-1">(${escapeHtml(leaf.name_en)})</span>
                                                    </div>
                                                    <button type="button" 
                                                            onclick="selectAndFetchCategory('${leaf.slug}', '${escapeHtml(leaf.name_bn)}', '${sub.slug}', '${escapeHtml(sub.name_bn)}', 3)"
                                                            class="px-2 py-0.5 rounded-md bg-purple-50 hover:bg-purple-600 hover:text-white text-purple-800 text-[10px] font-bold border border-purple-200 transition-all cursor-pointer">
                                                        লোড
                                                    </button>
                                                </div>
                                            `;
                                        }).join('')}
                                    </div>
                                ` : ''}
                            </div>
                        `;
                    }).join('')}
                </div>
            </div>
        `;
    }).join('');
}

function toggleTreeSection(sectionId) {
    const el = document.getElementById(sectionId);
    const icon = document.getElementById(`tree-icon-${sectionId}`);
    if (!el) return;
    if (el.classList.contains('hidden')) {
        el.classList.remove('hidden');
        if (icon) icon.style.transform = 'rotate(0deg)';
    } else {
        el.classList.add('hidden');
        if (icon) icon.style.transform = 'rotate(-90deg)';
    }
}

// Real-Time Search Filter across all 118 categories
function handleCategoryFilter(val) {
    const query = val.trim().toLowerCase();
    const clearBtn = document.getElementById('clearCatFilterBtn');
    const drillContainer = document.getElementById('explorerDrillDownContainer');
    const treeContainer = document.getElementById('explorerTreeContainer');
    const filterContainer = document.getElementById('explorerFilterResultsContainer');
    const breadcrumbs = document.getElementById('explorerBreadcrumbs');

    if (!query) {
        clearBtn.classList.add('hidden');
        filterContainer.classList.add('hidden');
        if (explorerViewMode === 'drilldown') {
            drillContainer.classList.remove('hidden');
            breadcrumbs.classList.remove('hidden');
        } else {
            treeContainer.classList.remove('hidden');
        }
        return;
    }

    clearBtn.classList.remove('hidden');
    drillContainer.classList.add('hidden');
    treeContainer.classList.add('hidden');
    breadcrumbs.classList.add('hidden');
    filterContainer.classList.remove('hidden');

    const matches = categoryFlatList.filter(item => {
        return item.name_bn.toLowerCase().includes(query) ||
               item.name_en.toLowerCase().includes(query) ||
               item.slug.toLowerCase().includes(query) ||
               (item.parent_name && item.parent_name.toLowerCase().includes(query));
    });

    document.getElementById('filterResultsTitle').innerText = `"${val}" এর জন্য ${matches.length}টি ক্যাটাগরি পাওয়া গেছে:`;

    const grid = document.getElementById('explorerFilterResultsGrid');
    if (matches.length === 0) {
        grid.innerHTML = `<div class="col-span-full p-6 text-center text-xs text-secondary-500 bg-secondary-50 rounded-2xl">কোনো ক্যাটাগরি পাওয়া যায়নি। আপনি নিচে সরাসরি Shwapno সার্চ করতে পারেন।</div>`;
        return;
    }

    const levelBadgeMap = {
        1: '<span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 text-[10px] font-bold">মূল বিভাগ</span>',
        2: '<span class="px-2 py-0.5 rounded-md bg-blue-100 text-blue-800 text-[10px] font-bold">সাব-ক্যাটাগরি</span>',
        3: '<span class="px-2 py-0.5 rounded-md bg-purple-100 text-purple-800 text-[10px] font-bold">সাব-সাব</span>'
    };

    grid.innerHTML = matches.map(item => {
        return `
            <div class="p-3 rounded-2xl border border-secondary-200 bg-white hover:border-emerald-300 hover:shadow-xs transition-all flex flex-col justify-between gap-2.5">
                <div class="flex items-start gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-secondary-100 text-secondary-700 flex items-center justify-center text-base flex-shrink-0">
                        <ion-icon name="${item.icon || 'folder-outline'}"></ion-icon>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="text-xs font-black text-secondary-900">${escapeHtml(item.name_bn)}</span>
                            ${levelBadgeMap[item.level] || ''}
                        </div>
                        <div class="text-[10px] text-secondary-400 mt-0.5">${escapeHtml(item.name_en)}</div>
                        ${item.parent_name ? `<div class="text-[9px] text-secondary-500 mt-1">প্যারেন্ট: <span class="font-bold text-secondary-700">${escapeHtml(item.parent_name.split('(')[0])}</span></div>` : ''}
                    </div>
                </div>

                <button type="button" 
                        onclick="selectAndFetchCategory('${item.slug}', '${escapeHtml(item.name_bn)}', '${item.parent_slug || ''}', '${escapeHtml(item.parent_name || '')}', ${item.level})"
                        class="w-full py-1.5 px-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-[11px] font-black transition-all flex items-center justify-center gap-1 shadow-2xs active:scale-95 cursor-pointer">
                    <ion-icon name="cloud-download-outline" class="text-xs"></ion-icon>
                    <span>পণ্য লোড করুন</span>
                </button>
            </div>
        `;
    }).join('');
}

function clearCategoryFilter() {
    const input = document.getElementById('categoryFilterInput');
    if (input) input.value = '';
    handleCategoryFilter('');
}

// Select category and immediately load products with hierarchy context
function selectAndFetchCategory(slug, displayName, parentSlug = null, parentName = null, level = 1) {
    const input = document.getElementById('shwapnoCategoryInput');
    if (input) input.value = slug;

    currentCategorySlug = slug;
    currentCategoryName = displayName;
    currentCategoryParentSlug = parentSlug || '';
    currentCategoryParentName = parentName || '';
    currentCategoryLevel = level || 1;
    currentFetchPage = 1;

    fetchCategoryProducts(1, false);

    // Smooth scroll down to products section
    setTimeout(() => {
        const preview = document.getElementById('previewContainer');
        if (preview && !preview.classList.contains('hidden')) {
            preview.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }, 600);
}

// ============================================================
// 1-CLICK ALL CATEGORIES BATCH SETUP MODAL LOGIC (118 ITEMS)
// ============================================================

function openCategorySetupModal() {
    const modal = document.getElementById('categorySetupModal');
    const content = document.getElementById('categorySetupModalContent');
    renderCatSetupList();
    modal.classList.remove('hidden');
    setTimeout(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function closeCategorySetupModal() {
    if (isCatSetupRunning) {
        if (!confirm('ক্যাটাগরি সিঙ্ক প্রক্রিয়া এখনও চলছে। আপনি কি সত্যিই এটি বন্ধ করতে চান?')) return;
        isCatSetupRunning = false;
    }
    const modal = document.getElementById('categorySetupModal');
    const content = document.getElementById('categorySetupModalContent');
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 200);
}

function filterModalCatTab(tab) {
    modalActiveTab = tab;
    ['all', 1, 2, 3].forEach(t => {
        const btn = document.getElementById(`modalTab${t === 'all' ? 'All' : 'L' + t}`);
        if (!btn) return;
        if (t === tab) {
            btn.className = 'px-3 py-1 rounded-lg text-xs font-bold transition-colors bg-emerald-100 text-emerald-800 border border-emerald-200 cursor-pointer';
        } else {
            btn.className = 'px-3 py-1 rounded-lg text-xs font-bold transition-colors text-secondary-600 hover:bg-secondary-100 cursor-pointer';
        }
    });
    renderCatSetupList();
}

function renderCatSetupList() {
    const container = document.getElementById('catSetupListContainer');
    if (!container) return;

    let items = categoryFlatList;
    if (modalActiveTab !== 'all') {
        items = categoryFlatList.filter(c => c.level === modalActiveTab);
    }

    const levelBadgeMap = {
        1: '<span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 border border-emerald-200">মূল</span>',
        2: '<span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-blue-100 text-blue-800 border border-blue-200">সাব</span>',
        3: '<span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-purple-100 text-purple-800 border border-purple-200">সাব-সাব</span>'
    };

    container.innerHTML = items.map((cat) => {
        const cleanName = cat.name_bn || cat.name.split('(')[0].trim();
        const originalIndex = categoryFlatList.findIndex(c => c.slug === cat.slug);
        return `
            <div class="p-2.5 rounded-2xl border border-secondary-200 bg-secondary-50/70 flex items-center justify-between gap-2.5 transition-all" id="cat-row-${originalIndex}">
                <div class="flex items-center gap-2.5 min-w-0 flex-1">
                    <div class="w-9 h-9 rounded-xl bg-white border border-secondary-200 overflow-hidden flex items-center justify-center flex-shrink-0 shadow-2xs" id="cat-thumb-${originalIndex}">
                        <ion-icon name="${cat.icon || 'folder-outline'}" class="text-secondary-400 text-lg"></ion-icon>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5">
                            <div class="text-xs font-bold text-secondary-800 truncate">${escapeHtml(cleanName)}</div>
                            ${levelBadgeMap[cat.level] || ''}
                        </div>
                        <div class="text-[10px] text-secondary-400 font-mono truncate">${escapeHtml(cat.slug)}</div>
                        ${cat.parent_name ? `<div class="text-[9px] text-secondary-500 truncate">প্যারেন্ট: ${escapeHtml(cat.parent_name.split('(')[0])}</div>` : ''}
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-secondary-200 text-secondary-600 border border-secondary-300 whitespace-nowrap inline-block" id="cat-badge-${originalIndex}">
                        অপেক্ষারত
                    </span>
                </div>
            </div>
        `;
    }).join('');
}

function appendCatSetupLog(msg, type = 'info') {
    const consoleEl = document.getElementById('catSetupLogConsole');
    if (!consoleEl) return;
    const time = new Date().toLocaleTimeString('bn-BD');
    const colors = {
        info: 'text-slate-300',
        success: 'text-emerald-400 font-bold',
        skip: 'text-blue-300',
        error: 'text-rose-400 font-bold'
    };
    const colorClass = colors[type] || 'text-slate-300';
    const entry = document.createElement('div');
    entry.className = `leading-relaxed ${colorClass}`;
    entry.innerHTML = `<span class="text-slate-600 font-mono">[${time}]</span> ${msg}`;
    consoleEl.appendChild(entry);
    consoleEl.scrollTop = consoleEl.scrollHeight;
}

function clearCatSetupLogs() {
    const consoleEl = document.getElementById('catSetupLogConsole');
    if (consoleEl) {
        consoleEl.innerHTML = '<div class="text-slate-500 italic">লগ স্ক্রিন ক্লিয়ার করা হয়েছে...</div>';
    }
}

async function startBatchCategorySetup() {
    if (isCatSetupRunning) return;
    isCatSetupRunning = true;

    const startBtn = document.getElementById('catSetupStartBtn');
    const startBtnText = document.getElementById('catSetupStartBtnText');
    startBtn.disabled = true;
    startBtn.classList.add('opacity-70', 'cursor-not-allowed');
    startBtnText.innerText = 'সিঙ্ক চলছে...';

    const progressBar = document.getElementById('catSetupProgressBar');
    const progressPct = document.getElementById('catSetupProgressPct');
    const progressStatus = document.getElementById('catSetupProgressStatusText');
    const statCreated = document.getElementById('catSetupCreated');
    const statExisting = document.getElementById('catSetupExisting');
    const statTotal = document.getElementById('catSetupTotal');

    statTotal.innerText = categoryFlatList.length;
    let createdCount = 0;
    let existingCount = 0;

    clearCatSetupLogs();
    appendCatSetupLog(`🚀 ৩-স্তরে স্বপ্নের মোট ${categoryFlatList.length}টি ক্যাটাগরি ও সাব-ক্যাটাগরি প্যারেন্ট রিলেশন সহ সিঙ্ক শুরু হচ্ছে...`, 'info');

    for (let i = 0; i < categoryFlatList.length; i++) {
        if (!isCatSetupRunning) {
            appendCatSetupLog('🛑 প্রক্রিয়া ব্যবহারকারী কর্তৃক স্থগিত করা হয়েছে।', 'error');
            break;
        }

        const cat = categoryFlatList[i];
        const cleanName = cat.name_bn || cat.name.split('(')[0].trim();
        const row = document.getElementById(`cat-row-${i}`);
        const badge = document.getElementById(`cat-badge-${i}`);
        const thumb = document.getElementById(`cat-thumb-${i}`);

        if (row) {
            row.classList.add('border-emerald-300', 'bg-emerald-50/40');
        }
        if (badge) {
            badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-300 whitespace-nowrap animate-pulse';
            badge.innerText = 'ডাউনলোড হচ্ছে...';
        }

        const levelPrefix = cat.level === 1 ? '[L1 মূল]' : (cat.level === 2 ? '[L2 সাব]' : '[L3 সাব-সাব]');
        progressStatus.innerText = `[${i + 1}/${categoryFlatList.length}] ${levelPrefix} "${cleanName}" তৈরি ও ছবি সিঙ্ক হচ্ছে...`;
        appendCatSetupLog(`[${i + 1}/${categoryFlatList.length}] ${levelPrefix} ${cleanName} (${cat.slug}) সিঙ্ক শুরু...`, 'info');

        try {
            const formData = new URLSearchParams();
            formData.append('slug', cat.slug);
            formData.append('name', cat.name);
            formData.append('parent_slug', cat.parent_slug || '');
            formData.append('parent_name', cat.parent_name || '');
            formData.append('level', cat.level);

            const res = await fetch(`${baseUri}/admin/products/shwapno-setup-category`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData.toString()
            });

            const data = await res.json();

            if (data.success) {
                if (data.status === 'created') {
                    createdCount++;
                    statCreated.innerText = createdCount;
                    if (badge) {
                        badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 whitespace-nowrap';
                        badge.innerText = '✅ তৈরি হয়েছে';
                    }
                    appendCatSetupLog(`✅ ${levelPrefix} "${cleanName}" ক্যাটাগরি সফলভাবে তৈরি হয়েছে (ছবি সহ, প্যারেন্ট ID: ${data.parent_id || 'নেই'})।`, 'success');
                } else if (data.status === 'updated') {
                    createdCount++;
                    statCreated.innerText = createdCount;
                    if (badge) {
                        badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-teal-100 text-teal-800 border border-teal-300 whitespace-nowrap';
                        badge.innerText = '🖼️ তথ্য আপডেট';
                    }
                    appendCatSetupLog(`🖼️ ${levelPrefix} "${cleanName}" বিদ্যমান ছিল, ছবি ও প্যারেন্ট রিলেশন আপডেট হয়েছে।`, 'success');
                } else {
                    existingCount++;
                    statExisting.innerText = existingCount;
                    if (badge) {
                        badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 border border-blue-300 whitespace-nowrap';
                        badge.innerText = '✓ বিদ্যমান';
                    }
                    appendCatSetupLog(`✓ ${levelPrefix} "${cleanName}" ইতোমধ্যে সিস্টেমে সঠিক তথ্য সহ বিদ্যমান।`, 'skip');
                }

                // Update thumbnail image if available
                if (data.image_path && thumb) {
                    thumb.innerHTML = `<img src="${data.image_path}" class="w-full h-full object-cover" alt="${escapeHtml(cleanName)}">`;
                }

                // Dynamically update target category select box on page
                updateTargetCategoryDropdown(data.id, data.name, data.level, data.parent_id);

            } else {
                if (badge) {
                    badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 border border-rose-300 whitespace-nowrap';
                    badge.innerText = 'ত্রুটি';
                }
                appendCatSetupLog(`❌ ${levelPrefix} "${cleanName}" ব্যর্থ: ${data.message || 'অজানা ত্রুটি'}`, 'error');
            }
        } catch (err) {
            if (badge) {
                badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 border border-rose-300 whitespace-nowrap';
                badge.innerText = 'নেটওয়ার্ক ত্রুটি';
            }
            appendCatSetupLog(`❌ ${levelPrefix} "${cleanName}" সংযোগ ত্রুটি: ${err.message}`, 'error');
        }

        // Update progress bar
        const pct = Math.round(((i + 1) / categoryFlatList.length) * 100);
        progressBar.style.width = `${pct}%`;
        progressPct.innerText = `${pct}%`;
    }

    isCatSetupRunning = false;
    startBtn.disabled = false;
    startBtn.classList.remove('opacity-70', 'cursor-not-allowed');
    startBtnText.innerText = 'পুনরায় সব ক্যাটাগরি সিঙ্ক করুন';
    progressStatus.innerText = '🎉 সব ক্যাটাগরি ও সাব-ক্যাটাগরি হায়ারার্কি সহ সফলভাবে ডাটাবেসে সম্পন্ন হয়েছে!';
    appendCatSetupLog(`🏁 ৩-স্তরের সকল ক্যাটাগরি প্রক্রিয়া সম্পন্ন! মোট নতুন: ${createdCount}টি, বিদ্যমান: ${existingCount}টি।`, 'success');
}

// Dynamically add category to dropdown if not present
function updateTargetCategoryDropdown(catId, catName, level = 1, parentId = null) {
    const select = document.getElementById('targetCategoryId');
    if (!select) return;
    
    let exists = false;
    for (let opt of select.options) {
        if (opt.value == catId || opt.text.trim().toLowerCase() === catName.trim().toLowerCase()) {
            exists = true;
            break;
        }
    }
    if (!exists) {
        const indent = level === 1 ? '📁 ' : (level === 2 ? '    ↳ 📂 ' : '        ↳ ↳ 📄 ');
        const newOption = document.createElement('option');
        newOption.value = catId;
        newOption.text = indent + catName;
        select.appendChild(newOption);
    }
}

// Fetch products from backend proxy
async function fetchCategoryProducts(page = 1, append = false) {
    const input = document.getElementById('shwapnoCategoryInput');
    const catQuery = input.value.trim();
    if (!catQuery) {
        alert('অনুগ্রহ করে একটি ক্যাটাগরি নাম বা Shwapno লিংক প্রদান করুন।');
        return;
    }

    const btn = document.getElementById('loadProductsBtn');
    const btnText = document.getElementById('loadProductsBtnText');
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    const loadMoreBtnBottom = document.getElementById('loadMoreBtnBottom');
    
    if (append) {
        if (loadMoreBtn) {
            loadMoreBtn.disabled = true;
            loadMoreBtn.innerHTML = `<span class="inline-block w-3 h-3 border-2 border-current border-t-transparent rounded-full animate-spin"></span> <span>লোড হচ্ছে...</span>`;
        }
        if (loadMoreBtnBottom) {
            loadMoreBtnBottom.disabled = true;
            loadMoreBtnBottom.innerHTML = `<span class="inline-block w-3 h-3 border-2 border-current border-t-transparent rounded-full animate-spin"></span> <span>লোড হচ্ছে...</span>`;
        }
    } else {
        btn.disabled = true;
        btnText.innerText = 'লোড হচ্ছে...';
    }

    try {
        const res = await fetch(`${baseUri}/admin/products/shwapno-category-fetch?category=${encodeURIComponent(catQuery)}&page=${page}`);
        const data = await res.json();

        if (data.success && data.products && data.products.length > 0) {
            currentFetchPage = data.current_page || page;
            totalFetchPages = data.total_pages || 1;
            totalFetchCount = data.total_count || data.products.length;
            hasNextPage = !!data.has_next_page;
            hasPrevPage = !!data.has_prev_page;

            currentCategoryName = data.category_name || catQuery;
            currentCategorySlug = data.category || catQuery;
            currentCategoryParentSlug = data.parent_slug || '';
            currentCategoryParentName = data.parent_name || '';
            currentCategoryLevel = data.level || 1;

            if (append) {
                // Append only products not already in loadedProducts (by SKU / Name)
                const existingSkus = new Set(loadedProducts.map(p => p.sku).filter(Boolean));
                const existingNames = new Set(loadedProducts.map(p => p.name.toLowerCase()));
                const freshItems = data.products.filter(p => {
                    if (p.sku && existingSkus.has(p.sku)) return false;
                    if (p.name && existingNames.has(p.name.toLowerCase())) return false;
                    return true;
                });
                loadedProducts = [...loadedProducts, ...freshItems];
            } else {
                loadedProducts = data.products;
            }

            renderProductsTable(loadedProducts);
            updatePaginationUI();
            
            // Check if all items in this page are already in database
            const allDuplicatesBanner = document.getElementById('allDuplicatesBanner');
            if (allDuplicatesBanner) {
                if (!append && data.new_count === 0 && data.total_pages > data.current_page) {
                    allDuplicatesBanner.classList.remove('hidden');
                    const sub = document.getElementById('allDuplicatesBannerSub');
                    if (sub) {
                        sub.innerHTML = `এই পৃষ্ঠার সব ৫০টি পণ্য ইতিমধ্যে আপনার ডাটাবেসে সেভ আছে। নতুন পণ্য পেতে পরবর্তী পৃষ্ঠা দেখুন (পৃষ্ঠা ${currentFetchPage + 1} / ${totalFetchPages})।`;
                    }
                    const nextText = document.getElementById('allDuplicatesNextBtnText');
                    if (nextText) {
                        nextText.innerText = `পরবর্তী পৃষ্ঠা (${currentFetchPage + 1}) লোড করুন`;
                    }
                } else {
                    allDuplicatesBanner.classList.add('hidden');
                }
            }

            document.getElementById('initialStateBox').classList.add('hidden');
            document.getElementById('previewContainer').classList.remove('hidden');

            if (!append && page > 1) {
                const preview = document.getElementById('previewContainer');
                if (preview) preview.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        } else {
            alert(data.message || 'কোনো পণ্য পাওয়া যায়নি।');
        }
    } catch (err) {
        console.error('Fetch error:', err);
        alert('সার্ভারের সাথে সংযোগ স্থাপন করা যায়নি। অনুগ্রহ করে আবার চেষ্টা করুন।');
    } finally {
        btn.disabled = false;
        btnText.innerText = 'পণ্য লোড করুন';
        if (loadMoreBtn) {
            loadMoreBtn.disabled = (currentFetchPage >= totalFetchPages);
            loadMoreBtn.innerHTML = `<ion-icon name="add-circle-outline" class="text-sm text-teal-600"></ion-icon> <span>আরও ৫০টি পণ্য যোগ করুন</span>`;
        }
        if (loadMoreBtnBottom) {
            loadMoreBtnBottom.disabled = (currentFetchPage >= totalFetchPages);
            loadMoreBtnBottom.innerHTML = `<ion-icon name="add-circle-outline" class="text-sm text-teal-600"></ion-icon> <span>আরও ৫০টি পণ্য যোগ করুন</span>`;
        }
    }
}

function loadNextPage() {
    if (currentFetchPage < totalFetchPages) {
        fetchCategoryProducts(currentFetchPage + 1, false);
    } else {
        alert('আপনি শেষ পৃষ্ঠায় আছেন।');
    }
}

function loadPrevPage() {
    if (currentFetchPage > 1) {
        fetchCategoryProducts(currentFetchPage - 1, false);
    }
}

function loadMoreProducts() {
    if (currentFetchPage < totalFetchPages) {
        fetchCategoryProducts(currentFetchPage + 1, true);
    } else {
        alert('আর কোনো অতিরিক্ত পৃষ্ঠা বা পণ্য নেই।');
    }
}

function updatePaginationUI() {
    // Current page numbers
    document.querySelectorAll('.current-page-display').forEach(el => el.innerText = currentFetchPage);
    document.querySelectorAll('.total-pages-display').forEach(el => el.innerText = totalFetchPages);
    document.querySelectorAll('.total-items-display').forEach(el => el.innerText = totalFetchCount);

    // Prev / Next button states
    const prevBtns = [document.getElementById('prevPageBtn'), document.getElementById('prevPageBtnBottom')];
    prevBtns.forEach(btn => {
        if (!btn) return;
        btn.disabled = (currentFetchPage <= 1);
    });

    const nextBtns = [document.getElementById('nextPageBtn'), document.getElementById('nextPageBtnBottom')];
    nextBtns.forEach(btn => {
        if (!btn) return;
        btn.disabled = (currentFetchPage >= totalFetchPages);
    });

    const moreBtns = [document.getElementById('loadMoreBtn'), document.getElementById('loadMoreBtnBottom')];
    moreBtns.forEach(btn => {
        if (!btn) return;
        btn.disabled = (currentFetchPage >= totalFetchPages);
    });
}

function toggleHideExisting() {
    isHideExistingActive = !isHideExistingActive;
    const btn = document.getElementById('toggleHideExistingBtn');
    const text = document.getElementById('toggleHideExistingText');
    const rows = document.querySelectorAll('#shwapnoProductsTbody tr');

    rows.forEach(tr => {
        const isNew = tr.getAttribute('data-is-new') === '1';
        if (isHideExistingActive && !isNew) {
            tr.classList.add('hidden');
        } else {
            tr.classList.remove('hidden');
        }
    });

    if (isHideExistingActive) {
        btn.className = 'px-3 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-black transition-all flex items-center gap-1.5 shadow-xs cursor-pointer';
        text.innerText = 'সব পণ্য দেখান';
    } else {
        btn.className = 'px-3 py-1.5 rounded-xl bg-secondary-100 hover:bg-secondary-200 text-secondary-700 text-xs font-bold transition-all flex items-center gap-1.5 border border-secondary-200 cursor-pointer';
        text.innerText = 'বিদ্যমান পণ্য লুকান';
    }
}

// Initialize Explorer on page load
document.addEventListener('DOMContentLoaded', () => {
    initCategoryExplorer();
});

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
        const isNew = !p.exists_in_db;
        tr.className = 'hover:bg-emerald-50/30 transition-colors group item-row' + (isHideExistingActive && !isNew ? ' hidden' : '');
        tr.id = `item-row-${idx}`;
        tr.setAttribute('data-is-new', isNew ? '1' : '0');

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

        const catDisplayName = p.category_name || currentCategoryName || 'স্বয়ংক্রিয়';

        tr.innerHTML = `
            <td class="py-3 px-4 text-center">
                <input type="checkbox" 
                       class="product-checkbox rounded text-emerald-600 focus:ring-emerald-500" 
                       data-index="${idx}" 
                       data-is-new="${isNew ? '1' : '0'}"
                       ${isNew ? 'checked' : (document.querySelector('input[name="duplicateAction"]:checked')?.value === 'skip' ? '' : 'checked')}
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
            <td class="py-3 px-4">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-teal-50 text-teal-800 text-[11px] font-bold border border-teal-200 shadow-2xs" title="${escapeHtml(catDisplayName)}">
                    <ion-icon name="folder-outline" class="text-xs text-teal-600"></ion-icon>
                    <span class="max-w-[120px] truncate">${escapeHtml(catDisplayName)}</span>
                </span>
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
        if (targetCat === 'auto' || !targetCat) {
            formData.append('category_name', p.category_name || currentCategoryName || '');
            formData.append('category_slug', currentCategorySlug || '');
            formData.append('parent_slug', currentCategoryParentSlug || '');
            formData.append('parent_name', currentCategoryParentName || '');
            formData.append('level', currentCategoryLevel || 1);
        } else {
            formData.append('category_id', targetCat);
        }
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
            
            let catNotice = '';
            if (data.category_created && data.category_info) {
                catNotice = `<div class="text-[9px] text-teal-700 font-bold mt-0.5">📁 ক্যাটাগরি তৈরি: ${escapeHtml(data.category_info.name)}</div>`;
            }

            document.getElementById(`item-status-col-${idx}`).innerHTML = `
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-600 text-white text-[10px] font-bold">
                    <ion-icon name="checkmark-done" class="text-xs"></ion-icon> ইমপোর্ট সম্পন্ন (#${data.id})
                </span>
                ${catNotice}
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
    const nextModalBtn = document.getElementById('bulkNextPageBtn');
    if (nextModalBtn) nextModalBtn.classList.add('hidden');
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
            if (targetCat === 'auto' || !targetCat) {
                formData.append('category_name', p.category_name || currentCategoryName || '');
                formData.append('category_slug', currentCategorySlug || '');
                formData.append('parent_slug', currentCategoryParentSlug || '');
                formData.append('parent_name', currentCategoryParentName || '');
                formData.append('level', currentCategoryLevel || 1);
            } else {
                formData.append('category_id', targetCat);
            }
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

                if (data.category_created && data.category_info) {
                    appendBulkLog(`📁 [অটো-ক্যাটাগরি] "${data.category_info.name}" ক্যাটাগরি ছবি সহ সফলভাবে তৈরি হয়েছে (ID #${data.category_info.id})!`, 'done');
                }
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

        if (currentFetchPage < totalFetchPages) {
            const nextBtn = document.getElementById('bulkNextPageBtn');
            const nextBtnText = document.getElementById('bulkNextPageBtnText');
            if (nextBtn) {
                if (nextBtnText) nextBtnText.innerText = `পরবর্তী পৃষ্ঠা (${currentFetchPage + 1}) লোড করুন`;
                nextBtn.classList.remove('hidden');
            }
            appendBulkLog(`💡 টিপস: এই ক্যাটাগরির পরবর্তী পৃষ্ঠা (পৃষ্ঠা ${currentFetchPage + 1} / ${totalFetchPages}) লোড করে আরও নতুন পণ্য ইমপোর্ট করতে পারবেন।`, 'info');
        }

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
