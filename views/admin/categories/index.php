<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
$selected_parent = $_GET['parent_id'] ?? ($_SESSION['last_category_parent_id'] ?? '');
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-secondary-100 shadow-xs">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-secondary-900 flex items-center gap-2.5">
                <span class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center text-xl shadow-2xs">
                    <ion-icon name="grid-outline"></ion-icon>
                </span>
                ক্যাটাগরি ব্যবস্থাপনা (Categories)
            </h1>
            <p class="text-xs text-secondary-500 mt-1">
                ম্যানুয়ালি ক্যাটাগরি তৈরি করুন, প্যারেন্ট-চাইল্ড সম্পর্ক নির্ধারণ করুন এবং অটোমেটিক ছবি যুক্ত করুন।
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="<?= $base ?>/admin/products/shwapno" class="px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-bold transition-all shadow-xs flex items-center gap-1.5">
                <ion-icon name="cloud-download-outline" class="text-sm"></ion-icon>
                <span>Shwapno পণ্য স্ক্র্যাপার</span>
            </a>
            <a href="<?= $base ?>/admin/products" class="px-4 py-2 rounded-xl bg-secondary-100 hover:bg-secondary-200 text-secondary-700 text-xs font-bold transition-all flex items-center gap-1.5">
                <ion-icon name="cube-outline" class="text-sm"></ion-icon>
                <span>পণ্য তালিকা</span>
            </a>
        </div>
    </div>

    <!-- Alert / Toast Messages -->
    <?php if (!empty($_GET['error'])): ?>
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2">
            <ion-icon name="alert-circle" class="text-base text-rose-600"></ion-icon>
            <span>
                <?php 
                    if ($_GET['error'] === 'empty_name') echo 'ত্রুটি: ক্যাটাগরির নাম অবশ্যই প্রদান করতে হবে!';
                    elseif ($_GET['error'] === 'cannot_delete') echo 'ত্রুটি: এই ক্যাটাগরিতে পণ্য বা সাব-ক্যাটাগরি বিদ্যমান থাকায় এটি মোছা যাচ্ছে না!';
                    else echo 'একটি সমস্যা হয়েছে। অনুগ্রহ করে আবার চেষ্টা করুন।';
                ?>
            </span>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 xl:grid-cols-12 gap-6">
        <!-- Add Category Form Section (1 of 3 / 4 of 12) -->
        <div class="lg:col-span-1 xl:col-span-4">
            <div class="bg-white rounded-2xl shadow-xs border border-secondary-200 p-5 sm:p-6 sticky top-6">
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-secondary-100">
                    <h3 class="text-base font-black text-secondary-900 flex items-center gap-2">
                        <ion-icon name="add-circle" class="text-emerald-600 text-lg"></ion-icon>
                        <span>নতুন ক্যাটাগরি তৈরি</span>
                    </h3>
                    <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                        ম্যানুয়াল ফর্ম
                    </span>
                </div>

                <form action="<?= $base ?>/admin/categories/store" method="POST" enctype="multipart/form-data" id="addCategoryForm" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                    <input type="hidden" id="selected_image_url" name="selected_image_url" value="">

                    <!-- 1. Category Name (Bengali only) -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-secondary-700 text-xs font-bold" for="name">
                                ক্যাটাগরির নাম <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[10px] text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded font-semibold">শুধুমাত্র বাংলা</span>
                        </div>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               class="w-full px-3.5 py-2.5 text-xs border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-medium" 
                               required 
                               placeholder="যেমন: চাল ও শস্য, প্যাকেট চাল, শিশু খাদ্য"
                               oninput="handleCategoryNameInput(this.value)">
                    </div>

                    <!-- 2. Category Slug (English only) -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-secondary-700 text-xs font-bold" for="slug">
                                স্ল্যাগ (Slug)
                            </label>
                            <span class="text-[10px] text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded font-semibold font-mono">শুধুমাত্র ইংরেজি</span>
                        </div>
                        <input type="text" 
                               id="slug" 
                               name="slug" 
                               class="w-full px-3.5 py-2.5 text-xs font-mono border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-secondary-700" 
                               placeholder="যেমন: rice, packed-rice, baby-food">
                        <div class="text-[10px] text-secondary-400 mt-1">সিস্টেম আইডেন্টিফায়ার ও URL-এর জন্য ইংরেজি স্ল্যাগ। ফাঁকা রাখলে অটো জেনারেট হবে।</div>
                    </div>

                    <!-- 3. Parent Category (Persistent on reload & add) -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-secondary-700 text-xs font-bold" for="parent_id">
                                প্যারেন্ট ক্যাটাগরি (Parent Category)
                            </label>
                            <button type="button" 
                                    onclick="resetParentCategory()" 
                                    class="text-[11px] font-bold text-rose-600 hover:text-rose-800 transition-colors flex items-center gap-0.5" 
                                    title="প্যারেন্ট সিলেকশন রিসেট করে টপ-লেভেল করুন">
                                <ion-icon name="close-circle-outline"></ion-icon>
                                <span>টপ-লেভেল করুন</span>
                            </button>
                        </div>
                        
                        <div class="relative">
                            <select id="parent_id" 
                                    name="parent_id" 
                                    onchange="handleParentCategoryChange(this.value)"
                                    class="w-full px-3.5 py-2.5 text-xs border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 appearance-none bg-white font-medium text-secondary-800 cursor-pointer">
                                <option value="">None (মূল বিভাগ / Top Level)</option>
                                <?php 
                                    foreach ($categories as $cat): 
                                        $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $cat['depth']);
                                        $iconPrefix = $cat['depth'] == 0 ? '📁 ' : ($cat['depth'] == 1 ? '↳ 📂 ' : '↳ ↳ 📄 ');
                                        $isSelected = ($cat['id'] == $selected_parent) ? 'selected' : '';
                                ?>
                                    <option value="<?= $cat['id'] ?>" <?= $isSelected ?>>
                                        <?= $indent . $iconPrefix . htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-secondary-500">
                                <ion-icon name="chevron-down-outline"></ion-icon>
                            </div>
                        </div>

                        <!-- Active Parent Indicator -->
                        <div id="activeParentIndicator" class="mt-1.5 p-2 rounded-lg bg-secondary-50 border border-secondary-200 text-[11px] text-secondary-600 flex items-center justify-between">
                            <span class="truncate">বর্তমান প্যারেন্ট: <strong id="activeParentName" class="text-emerald-700">টপ লেভেল (None)</strong></span>
                            <span class="text-[10px] text-secondary-400 font-mono">লকড থাকবে</span>
                        </div>
                    </div>

                    <!-- 4. Category Image Section (Upload OR Auto-Find) -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-secondary-700 text-xs font-bold" for="image">
                                ক্যাটাগরির ছবি
                            </label>
                            <button type="button" 
                                    id="btnAutoFindImage" 
                                    onclick="triggerAutoImageCandidates()" 
                                    class="px-2 py-0.5 rounded-lg bg-emerald-100 hover:bg-emerald-200 text-emerald-800 text-[10px] font-bold transition-all flex items-center gap-1 cursor-pointer">
                                <ion-icon name="sparkles" class="text-emerald-600"></ion-icon>
                                <span>অটো ছবি খুঁজুন</span>
                            </button>
                        </div>

                        <!-- Custom File Upload -->
                        <input type="file" 
                               id="image" 
                               name="image" 
                               accept="image/*" 
                               onchange="handleLocalImageSelected(this)"
                               class="w-full text-xs text-secondary-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer border border-secondary-200 rounded-xl p-1 bg-secondary-50/50">

                        <!-- Live Candidates Thumbnail Preview Box -->
                        <div id="imageCandidatesContainer" class="hidden mt-2.5 p-2.5 rounded-xl bg-secondary-50 border border-secondary-200 space-y-2">
                            <div class="flex items-center justify-between text-[11px] font-bold text-secondary-700">
                                <span>অনলাইনে পাওয়া ছবি (ক্লিক করে পছন্দ করুন):</span>
                                <button type="button" onclick="closeImageCandidates()" class="text-secondary-400 hover:text-rose-600">✕</button>
                            </div>
                            <div id="imageCandidatesGrid" class="grid grid-cols-4 gap-2">
                                <!-- Dynamic thumbnails will be inserted here -->
                            </div>
                        </div>

                        <!-- Selected Image Preview Badge -->
                        <div id="selectedImagePreview" class="hidden mt-2 p-2 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center gap-2.5">
                            <img id="selectedImageThumb" src="" alt="Selected Preview" class="w-10 h-10 rounded-lg object-contain bg-white border border-emerald-200">
                            <div class="min-w-0 flex-1">
                                <div class="text-[11px] font-bold text-emerald-900">ছবি নির্বাচিত হয়েছে!</div>
                                <div class="text-[10px] text-emerald-600 truncate" id="selectedImageNote">সেভ করার সময় এটি ক্যাটাগরিতে যুক্ত হবে</div>
                            </div>
                            <button type="button" onclick="clearSelectedImage()" class="text-xs text-rose-500 hover:text-rose-700 font-bold">বাতিল</button>
                        </div>

                        <p class="text-[10px] text-secondary-400 mt-1 leading-relaxed">
                            💡 ছবি আপলোড না করলেও সমস্যা নেই; ক্যাটাগরি তৈরি করার সময় সিস্টেম <strong>স্বপ্নের ডাটাবেস থেকে স্বয়ংক্রিয়ভাবে ছবি ডাউনলোড করে যুক্ত করে দেবে</strong>।
                        </p>
                    </div>

                    <!-- 5. Description -->
                    <div>
                        <label class="block text-secondary-700 text-xs font-bold mb-1.5" for="description">
                            বিবরণ (ঐচ্ছিক)
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="2" 
                                  class="w-full px-3 py-2 text-xs border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 font-medium" 
                                  placeholder="ক্যাটাগরি সম্পর্কে সংক্ষিপ্ত নোট..."></textarea>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            id="submitCategoryBtn"
                            class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-xs transition-all flex items-center justify-center gap-2 shadow-sm cursor-pointer active:scale-98">
                        <ion-icon name="save-outline" class="text-base"></ion-icon>
                        <span>ক্যাটাগরি যুক্ত করুন</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Categories List & Real-time Search Section (2 of 3 / 8 of 12) -->
        <div class="lg:col-span-2 xl:col-span-8 min-w-0">
            <div class="bg-white rounded-2xl shadow-xs border border-secondary-200 overflow-hidden">
                <!-- Search & Filters Header Bar -->
                <div class="p-4 sm:p-5 border-b border-secondary-100 bg-secondary-50/50 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <h3 class="font-black text-secondary-900 text-base flex items-center gap-2">
                                <ion-icon name="list-outline" class="text-emerald-600"></ion-icon>
                                <span>বিদ্যমান ক্যাটাগরি তালিকা</span>
                            </h3>
                            <div class="text-xs text-secondary-500 mt-0.5" id="categoryStatsText">
                                মোট <?= count($categories) ?>টি ক্যাটাগরি ডাটাবেসে সংরক্ষিত আছে
                            </div>
                        </div>

                        <!-- Active matches counter -->
                        <div>
                            <span id="searchResultCount" class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                                <?= count($categories) ?>টি প্রদর্শিত
                            </span>
                        </div>
                    </div>

                    <!-- Instant Search Input & Level Filter -->
                    <div class="flex flex-col sm:flex-row items-center gap-2.5 pt-1">
                        <!-- Search Input -->
                        <div class="w-full sm:flex-1 relative">
                            <input type="text" 
                                   id="categorySearchInput" 
                                   oninput="filterCategoriesTable()" 
                                   placeholder="🔍 নাম, স্ল্যাগ, আইডি বা প্যারেন্ট লিখে দ্রুত খুঁজুন..." 
                                   class="w-full pl-9 pr-8 py-2 text-xs border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white shadow-2xs font-medium">
                            <span class="absolute left-3 top-2.5 text-secondary-400 text-sm">
                                <ion-icon name="search-outline"></ion-icon>
                            </span>
                            <button type="button" 
                                    id="clearSearchBtn" 
                                    onclick="clearCategorySearch()" 
                                    class="hidden absolute right-2.5 top-2 text-secondary-400 hover:text-secondary-700 text-sm font-bold">
                                ✕
                            </button>
                        </div>

                        <!-- Level Filter Dropdown -->
                        <div class="w-full sm:w-60 shrink-0">
                            <select id="categoryLevelFilter" 
                                    onchange="filterCategoriesTable()" 
                                    class="w-full px-3 py-2 text-xs border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white shadow-2xs font-bold text-secondary-700 cursor-pointer">
                                <option value="all">সকল লেভেল (All Levels)</option>
                                <option value="0">শুধু মূল ক্যাটাগরি (Top Level)</option>
                                <option value="sub">শুধু সাব-ক্যাটাগরি (Sub-Categories)</option>
                                <option value="no_image">ছবি ছাড়া ক্যাটাগরি (Missing Image)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Categories Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-secondary-600 min-w-[620px]" id="categoriesTable">
                        <thead class="bg-secondary-100/70 text-secondary-600 font-bold border-b border-secondary-200">
                            <tr>
                                <th class="px-4 py-3 font-bold w-16 text-center">ছবি</th>
                                <th class="px-4 py-3 font-bold min-w-[200px]">ক্যাটাগরির নাম ও স্ল্যাগ</th>
                                <th class="px-4 py-3 font-bold min-w-[130px]">প্যারেন্ট ক্যাটাগরি</th>
                                <th class="px-4 py-3 font-bold min-w-[120px]">বিবরণ</th>
                                <th class="px-4 py-3 font-bold w-24 text-right">অ্যাকশন</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-secondary-100" id="categoriesTbody">
                            <?php if (empty($categories)): ?>
                                <tr id="noCategoriesRow">
                                    <td colspan="5" class="px-6 py-12 text-center text-secondary-400">
                                        <div class="w-12 h-12 rounded-2xl bg-secondary-100 text-secondary-400 flex items-center justify-center mx-auto mb-2 text-2xl">
                                            <ion-icon name="folder-open-outline"></ion-icon>
                                        </div>
                                        <div class="font-bold text-secondary-600">কোনো ক্যাটাগরি পাওয়া যায়নি!</div>
                                        <div class="text-[11px] mt-1">বাম পাশের ফর্ম ব্যবহার করে প্রথম ক্যাটাগরি তৈরি করুন।</div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($categories as $cat): 
                                    $hasImg = !empty($cat['image_path']);
                                    $catSlug = $cat['slug'] ?? '';
                                    $depth = $cat['depth'] ?? 0;
                                    $depthIndentPx = $depth * 18;
                                    $levelBadge = $depth === 0 
                                        ? '<span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[9px] font-bold">মূল</span>' 
                                        : ($depth === 1 
                                            ? '<span class="px-1.5 py-0.5 rounded bg-blue-100 text-blue-800 text-[9px] font-bold">সাব</span>' 
                                            : '<span class="px-1.5 py-0.5 rounded bg-purple-100 text-purple-800 text-[9px] font-bold">সাব-সাব</span>');
                                ?>
                                    <tr class="hover:bg-emerald-50/20 transition-colors category-row" 
                                        id="cat-row-<?= $cat['id'] ?>"
                                        data-id="<?= $cat['id'] ?>"
                                        data-name="<?= strtolower(htmlspecialchars($cat['name'])) ?>"
                                        data-slug="<?= strtolower(htmlspecialchars($catSlug)) ?>"
                                        data-parent="<?= strtolower(htmlspecialchars($cat['parent_name'] ?? '')) ?>"
                                        data-depth="<?= $depth ?>"
                                        data-has-image="<?= $hasImg ? '1' : '0' ?>">

                                        <!-- Image Column with 1-Click Auto Image if missing -->
                                        <td class="px-4 py-3 text-center" id="cat-img-td-<?= $cat['id'] ?>">
                                            <?php 
                                                $catImgUrl = \Models\Category::getImageUrl($cat['image_path'] ?? '', $base);
                                            ?>
                                            <div class="inline-flex items-center justify-center gap-1.5">
                                                <div class="w-10 h-10 rounded-xl bg-white border border-secondary-200 overflow-hidden shadow-2xs flex items-center justify-center p-0.5 shrink-0">
                                                    <img src="<?= $catImgUrl ?>" 
                                                         alt="<?= htmlspecialchars($cat['name']) ?>" 
                                                         class="w-full h-full object-contain"
                                                         onerror="this.onerror=null; this.src='<?= $base ?>/images/default-category.svg';">
                                                </div>
                                                <?php if (!$hasImg): ?>
                                                    <button type="button" 
                                                            onclick="oneClickAutoImage(<?= $cat['id'] ?>, '<?= addslashes($cat['name']) ?>')" 
                                                            class="p-1 rounded-lg bg-amber-50 hover:bg-emerald-100 text-amber-700 hover:text-emerald-700 border border-amber-200 hover:border-emerald-300 transition-all shadow-2xs cursor-pointer"
                                                            title="১-ক্লিকে অটো ছবি যুক্ত করুন">
                                                        <ion-icon name="sparkles" class="text-xs"></ion-icon>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>

                                        <!-- Name & Slug Column -->
                                        <td class="px-4 py-3">
                                            <div style="padding-left: <?= $depthIndentPx ?>px;" class="flex items-center gap-2">
                                                <?php if ($depth > 0): ?>
                                                    <span class="text-secondary-400 font-mono font-bold select-none">↳</span>
                                                <?php endif; ?>
                                                <div>
                                                    <div class="font-black text-secondary-900 text-xs flex items-center gap-1.5 flex-wrap">
                                                        <span><?= htmlspecialchars($cat['name']) ?></span>
                                                        <?= $levelBadge ?>
                                                    </div>
                                                    <?php if (!empty($catSlug)): ?>
                                                        <div class="text-[10px] text-secondary-400 font-mono mt-0.5">
                                                            slug: <span class="text-secondary-600 font-bold"><?= htmlspecialchars($catSlug) ?></span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Parent Column -->
                                        <td class="px-4 py-3 text-secondary-600">
                                            <?php if (!empty($cat['parent_name'])): ?>
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-secondary-100 text-secondary-700 text-[11px] font-semibold">
                                                    <ion-icon name="return-up-forward-outline" class="text-xs text-secondary-500"></ion-icon>
                                                    <span><?= htmlspecialchars($cat['parent_name']) ?></span>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-secondary-400 text-[11px] italic">মূল বিভাগ (Top Level)</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Description -->
                                        <td class="px-4 py-3 text-secondary-500 truncate max-w-xs text-[11px]">
                                            <?= !empty($cat['description']) ? htmlspecialchars($cat['description']) : '<span class="text-secondary-300 italic">—</span>' ?>
                                        </td>

                                        <!-- Actions -->
                                        <td class="px-4 py-3 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <!-- Set as Parent in Add Form -->
                                                <button type="button" 
                                                        onclick="selectAsParentInForm(<?= $cat['id'] ?>, '<?= addslashes($cat['name']) ?>')" 
                                                        class="p-1.5 rounded-lg bg-secondary-100 hover:bg-emerald-100 text-secondary-600 hover:text-emerald-700 transition-colors"
                                                        title="এর অধীনে নতুন সাব-ক্যাটাগরি যুক্ত করুন">
                                                    <ion-icon name="add-outline" class="text-base"></ion-icon>
                                                </button>

                                                <!-- Edit -->
                                                <a href="/sodai-dorkar/public/admin/categories/edit?id=<?= $cat['id'] ?>" 
                                                   class="p-1.5 rounded-lg bg-secondary-100 hover:bg-blue-100 text-secondary-600 hover:text-blue-700 transition-colors"
                                                   title="এডিট করুন">
                                                    <ion-icon name="create-outline" class="text-base"></ion-icon>
                                                </a>

                                                <!-- Delete -->
                                                <form action="/sodai-dorkar/public/admin/categories/delete" method="POST" onsubmit="return confirm('আপনি কি নিশ্চিতভাবে এই ক্যাটাগরি মুছে ফেলতে চান?');" class="inline">
                                                    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                                                    <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                                    <button type="submit" 
                                                            class="p-1.5 rounded-lg bg-secondary-100 hover:bg-rose-100 text-secondary-600 hover:text-rose-700 transition-colors cursor-pointer"
                                                            title="মুছে ফেলুন">
                                                        <ion-icon name="trash-outline" class="text-base"></ion-icon>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            
                            <!-- No Search Matches Row -->
                            <tr id="noSearchMatchesRow" class="hidden">
                                <td colspan="5" class="px-6 py-10 text-center text-secondary-500 bg-secondary-50/50">
                                    <div class="text-sm font-bold text-secondary-700">🔍 কোনো মিল খুঁজে পাওয়া যায়নি!</div>
                                    <div class="text-xs text-secondary-400 mt-1">অন্য কোনো নাম বা শব্দ দিয়ে সার্চ করুন অথবা ফিল্টার ক্লিয়ার করুন।</div>
                                    <button type="button" onclick="clearCategorySearch()" class="mt-3 px-3 py-1.5 rounded-lg bg-secondary-200 hover:bg-secondary-300 text-secondary-700 text-xs font-bold transition-all">
                                        ফিল্টার মুছুন
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- JAVASCRIPT: PERSISTENCE, SEARCH & AUTO IMAGE LOGIC -->
<!-- ============================================================ -->
<script>
const CSRF_TOKEN = '<?= \Core\CSRF::token() ?>';
const BASE_URI = '<?= $base ?>';

// ==========================================
// 1. PARENT CATEGORY PERSISTENCE LOGIC
// ==========================================
function updateParentIndicatorText() {
    const parentSelect = document.getElementById('parent_id');
    const indicatorText = document.getElementById('activeParentName');
    if (!parentSelect || !indicatorText) return;

    if (parentSelect.value) {
        const opt = parentSelect.options[parentSelect.selectedIndex];
        indicatorText.innerText = opt ? opt.text.trim().replace(/^↳\s*/g, '') : 'সিলেক্টেড';
    } else {
        indicatorText.innerText = 'টপ লেভেল (None)';
    }
}

function handleParentCategoryChange(val) {
    localStorage.setItem('sodai_selected_parent_category', val);
    updateParentIndicatorText();
}

function resetParentCategory() {
    const parentSelect = document.getElementById('parent_id');
    if (parentSelect) {
        parentSelect.value = '';
        localStorage.setItem('sodai_selected_parent_category', '');
        updateParentIndicatorText();
    }
}

function selectAsParentInForm(catId, catName) {
    const parentSelect = document.getElementById('parent_id');
    if (parentSelect) {
        parentSelect.value = catId;
        localStorage.setItem('sodai_selected_parent_category', catId);
        updateParentIndicatorText();
        
        // Scroll to form smoothly
        const formEl = document.getElementById('addCategoryForm');
        if (formEl) {
            formEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
            document.getElementById('name').focus();
        }
    }
}

// Restore saved parent category on DOMContentLoaded (survives reload F5!)
document.addEventListener('DOMContentLoaded', () => {
    const parentSelect = document.getElementById('parent_id');
    if (parentSelect) {
        const urlParams = new URLSearchParams(window.location.search);
        const urlParent = urlParams.get('parent_id');
        const savedParent = localStorage.getItem('sodai_selected_parent_category');

        // Priority 1: URL param if present
        if (urlParent !== null && urlParent !== '') {
            parentSelect.value = urlParent;
            localStorage.setItem('sodai_selected_parent_category', urlParent);
        } 
        // Priority 2: localStorage value if dropdown currently empty
        else if (savedParent !== null && savedParent !== '') {
            parentSelect.value = savedParent;
        }

        updateParentIndicatorText();
    }
});

// ==========================================
// 2. NAME & SLUG DYNAMIC HELPER
// ==========================================
function handleCategoryNameInput(val) {
    const slugInput = document.getElementById('slug');
    if (!slugInput) return;

    // Only auto-suggest if slug input has not been manually altered
    if (!slugInput.dataset.manualEdited) {
        // Strip non-latin characters or use transliteration helper
        const latinOnly = val.toLowerCase().replace(/[^a-z0-9\s-]/g, '').trim().replace(/\s+/g, '-');
        if (latinOnly) {
            slugInput.value = latinOnly;
        }
    }
}

document.getElementById('slug')?.addEventListener('input', function() {
    if (this.value.trim().length > 0) {
        this.dataset.manualEdited = 'true';
    } else {
        delete this.dataset.manualEdited;
    }
});

// ==========================================
// 3. AUTO IMAGE FINDER & PREVIEW
// ==========================================
let isSearchingImages = false;

async function triggerAutoImageCandidates() {
    const nameVal = document.getElementById('name').value.trim();
    const slugVal = document.getElementById('slug').value.trim();
    const query = slugVal || nameVal;

    if (!query) {
        alert('অনুগ্রহ করে প্রথমে ক্যাটাগরির নাম লিখুন!');
        document.getElementById('name').focus();
        return;
    }

    if (isSearchingImages) return;
    isSearchingImages = true;

    const btn = document.getElementById('btnAutoFindImage');
    const originalHtml = btn.innerHTML;
    btn.innerHTML = `<span class="inline-block w-3 h-3 border-2 border-current border-t-transparent rounded-full animate-spin"></span> <span>খোঁজা হচ্ছে...</span>`;

    const container = document.getElementById('imageCandidatesContainer');
    const grid = document.getElementById('imageCandidatesGrid');
    grid.innerHTML = '<div class="col-span-4 py-3 text-center text-xs text-secondary-400 animate-pulse">অনলাইনে ছবি খোঁজা হচ্ছে...</div>';
    container.classList.remove('hidden');

    try {
        const res = await fetch(`${BASE_URI}/admin/categories/fetch-image?query=${encodeURIComponent(query)}`);
        const data = await res.json();

        if (data.success && data.images && data.images.length > 0) {
            grid.innerHTML = data.images.map((imgUrl, i) => `
                <div class="aspect-square rounded-xl bg-white border-2 border-secondary-200 hover:border-emerald-500 overflow-hidden cursor-pointer transition-all p-1 group relative shadow-2xs hover:scale-105"
                     onclick="chooseCandidateImage('${imgUrl}')"
                     title="ক্লিক করে এই ছবিটি পছন্দ করুন">
                    <img src="${imgUrl}" class="w-full h-full object-contain" alt="Candidate ${i+1}">
                    <div class="absolute inset-0 bg-emerald-600/10 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <ion-icon name="checkmark-circle" class="text-emerald-600 text-lg bg-white rounded-full"></ion-icon>
                    </div>
                </div>
            `).join('');
        } else {
            grid.innerHTML = `<div class="col-span-4 py-3 text-center text-xs text-secondary-500">কোনো ছবি পাওয়া যায়নি। আপনি নিজের কম্পিউটার থেকে ছবি আপলোড করতে পারেন।</div>`;
        }
    } catch (err) {
        console.error('Fetch image candidates error:', err);
        grid.innerHTML = `<div class="col-span-4 py-3 text-center text-xs text-rose-500">সার্ভার ত্রুটি: ছবি লোড করা সম্ভব হয়নি।</div>`;
    } finally {
        isSearchingImages = false;
        btn.innerHTML = originalHtml;
    }
}

function chooseCandidateImage(imgUrl) {
    document.getElementById('selected_image_url').value = imgUrl;
    
    // Show preview
    const previewContainer = document.getElementById('selectedImagePreview');
    const previewImg = document.getElementById('selectedImageThumb');
    previewImg.src = imgUrl;
    previewContainer.classList.remove('hidden');

    // Reset file input
    const fileInput = document.getElementById('image');
    if (fileInput) fileInput.value = '';

    // Hide candidates container
    closeImageCandidates();
}

function closeImageCandidates() {
    document.getElementById('imageCandidatesContainer').classList.add('hidden');
}

function clearSelectedImage() {
    document.getElementById('selected_image_url').value = '';
    document.getElementById('selectedImagePreview').classList.add('hidden');
}

function handleLocalImageSelected(input) {
    if (input.files && input.files[0]) {
        // Clear remote candidate
        document.getElementById('selected_image_url').value = '';
        document.getElementById('selectedImagePreview').classList.add('hidden');
        closeImageCandidates();
    }
}

// ==========================================
// 4. 1-CLICK AUTO IMAGE FOR TABLE ROWS
// ==========================================
async function oneClickAutoImage(catId, catName) {
    const td = document.getElementById(`cat-img-td-${catId}`);
    if (!td) return;

    const originalHtml = td.innerHTML;
    td.innerHTML = `<div class="w-10 h-10 rounded-xl bg-secondary-100 flex items-center justify-center mx-auto text-emerald-600"><span class="inline-block w-4 h-4 border-2 border-current border-t-transparent rounded-full animate-spin"></span></div>`;

    try {
        const formData = new FormData();
        formData.append('id', catId);
        formData.append('csrf_token', CSRF_TOKEN);

        const res = await fetch(`${BASE_URI}/admin/categories/auto-image`, {
            method: 'POST',
            body: formData
        });

        const data = await res.json();
        if (data.success && data.image_path) {
            td.innerHTML = `
                <div class="w-10 h-10 rounded-xl bg-white border border-secondary-200 overflow-hidden mx-auto shadow-2xs flex items-center justify-center animate-fade-in p-0.5">
                    <img src="${data.image_path}" alt="${catName}" class="w-full h-full object-contain" onerror="this.onerror=null; this.src='<?= $base ?>/images/default-category.svg';">
                </div>
            `;
            // Update row dataset
            const row = document.getElementById(`cat-row-${catId}`);
            if (row) row.dataset.hasImage = '1';
        } else {
            alert(data.message || 'ছবি খুঁজে পাওয়া যায়নি!');
            td.innerHTML = originalHtml;
        }
    } catch (err) {
        console.error('Auto image error:', err);
        alert('নেটওয়ার্ক ত্রুটি! ছবি ডাউনলোড করা যায়নি।');
        td.innerHTML = originalHtml;
    }
}

// ==========================================
// 5. INSTANT REAL-TIME SEARCH & FILTER
// ==========================================
function filterCategoriesTable() {
    const searchInput = document.getElementById('categorySearchInput');
    const query = (searchInput ? searchInput.value : '').trim().toLowerCase();
    const levelFilter = document.getElementById('categoryLevelFilter').value;
    const clearBtn = document.getElementById('clearSearchBtn');

    if (clearBtn) {
        if (query.length > 0) clearBtn.classList.remove('hidden');
        else clearBtn.classList.add('hidden');
    }

    const rows = document.querySelectorAll('.category-row');
    let visibleCount = 0;

    rows.forEach(row => {
        const id = row.dataset.id || '';
        const name = row.dataset.name || '';
        const slug = row.dataset.slug || '';
        const parent = row.dataset.parent || '';
        const depth = parseInt(row.dataset.depth || '0', 10);
        const hasImage = row.dataset.hasImage === '1';

        // 1. Text Search Match
        const matchesQuery = !query || 
            id.includes(query) || 
            name.includes(query) || 
            slug.includes(query) || 
            parent.includes(query);

        // 2. Level Filter Match
        let matchesLevel = true;
        if (levelFilter === '0') {
            matchesLevel = (depth === 0);
        } else if (levelFilter === 'sub') {
            matchesLevel = (depth > 0);
        } else if (levelFilter === 'no_image') {
            matchesLevel = !hasImage;
        }

        if (matchesQuery && matchesLevel) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    // Update Counter
    const countBadge = document.getElementById('searchResultCount');
    if (countBadge) {
        countBadge.innerText = `${visibleCount}টি প্রদর্শিত`;
    }

    // Show/Hide No matches row
    const noMatchesRow = document.getElementById('noSearchMatchesRow');
    if (noMatchesRow) {
        if (visibleCount === 0 && rows.length > 0) {
            noMatchesRow.classList.remove('hidden');
        } else {
            noMatchesRow.classList.add('hidden');
        }
    }
}

function clearCategorySearch() {
    const searchInput = document.getElementById('categorySearchInput');
    if (searchInput) searchInput.value = '';
    document.getElementById('categoryLevelFilter').value = 'all';
    filterCategoriesTable();
}
</script>
