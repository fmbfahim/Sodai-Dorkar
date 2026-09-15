<?php
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
?>

<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-emerald-800 via-teal-800 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-20 top-0 w-48 h-48 bg-teal-400/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-emerald-300 uppercase tracking-wider mb-2">
                    <a href="<?= $base ?>/admin/products" class="hover:text-white transition-colors">পণ্য ও ইনভেন্টরি</a>
                    <span>›</span>
                    <span class="text-white">অটো ইমেজ ফাইন্ডার</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight flex items-center gap-3">
                    <span class="p-2.5 bg-emerald-500/20 backdrop-blur-md rounded-2xl border border-emerald-400/30 text-emerald-300 flex items-center justify-center">
                        <ion-icon name="sparkles-outline" class="text-2xl"></ion-icon>
                    </span>
                    Auto Image Finder (ওয়েব ইমেজ সন্ধান)
                </h1>
                <p class="text-secondary-300 text-sm mt-2 max-w-2xl leading-relaxed">
                    স্বপ্ন (<span class="text-emerald-300 font-semibold">Shwapno.com</span>) ও গ্রোসারি ডাটাবেস থেকে এক ক্লিকে হাই-রেজ্যুলেশন পণ্যের ছবি ডাউনলোড করে সরাসরি আপনার লোকাল স্টোরে সেভ করুন।
                </p>
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <a href="<?= $base ?>/admin/products/bulk-import" class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-white text-xs font-bold transition-all flex items-center gap-2 shadow-sm">
                    <ion-icon name="cloud-upload-outline" class="text-base"></ion-icon>
                    বাল্ক সিএসভি ইমপোর্ট
                </a>
                <a href="<?= $base ?>/admin/products" class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold transition-all flex items-center gap-2 shadow-md shadow-emerald-900/30">
                    <ion-icon name="cube-outline" class="text-base"></ion-icon>
                    পণ্য তালিকা
                </a>
            </div>
        </div>

        <!-- KPI Counter Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6 pt-6 border-t border-white/10">
            <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-4 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-2xl font-bold">
                    <ion-icon name="albums-outline"></ion-icon>
                </div>
                <div>
                    <div class="text-2xl font-black text-white"><?= number_format($totalProducts) ?></div>
                    <div class="text-xs text-secondary-300 font-medium">মোট ক্যাটালগ পণ্য</div>
                </div>
            </div>

            <div class="bg-amber-500/10 backdrop-blur-md border border-amber-400/30 rounded-2xl p-4 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-500/20 text-amber-300 flex items-center justify-center text-2xl font-bold">
                    <ion-icon name="image-outline"></ion-icon>
                </div>
                <div>
                    <div class="text-2xl font-black text-amber-300"><?= number_format($missingCount) ?></div>
                    <div class="text-xs text-amber-200/80 font-medium">ছবি বাকি আছে (Missing Images)</div>
                </div>
            </div>

            <div class="bg-emerald-500/10 backdrop-blur-md border border-emerald-400/30 rounded-2xl p-4 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/20 text-emerald-300 flex items-center justify-center text-2xl font-bold">
                    <ion-icon name="checkmark-circle-outline"></ion-icon>
                </div>
                <div>
                    <div class="text-2xl font-black text-emerald-300"><?= number_format($hasImageCount) ?></div>
                    <div class="text-xs text-emerald-200/80 font-medium">ছবি যুক্ত আছে (Completed)</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Live Search Toolbar -->
    <div class="bg-white rounded-2xl p-5 shadow-xs border border-secondary-200 space-y-4">
        <form method="GET" action="<?= $base ?>/admin/products/image-finder" class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
            <!-- Filter Tabs -->
            <div class="inline-flex p-1 bg-secondary-100 rounded-xl text-xs font-bold text-secondary-600">
                <a href="<?= $base ?>/admin/products/image-finder?filter=missing<?= $categoryId ? '&category_id=' . $categoryId : '' ?>" 
                   class="px-4 py-2 rounded-lg transition-all <?= $filter === 'missing' ? 'bg-white text-secondary-900 shadow-xs' : 'hover:text-secondary-900' ?>">
                    ছবি ছাড়া পণ্য (<?= $missingCount ?>)
                </a>
                <a href="<?= $base ?>/admin/products/image-finder?filter=has_image<?= $categoryId ? '&category_id=' . $categoryId : '' ?>" 
                   class="px-4 py-2 rounded-lg transition-all <?= $filter === 'has_image' ? 'bg-white text-secondary-900 shadow-xs' : 'hover:text-secondary-900' ?>">
                    ছবিযুক্ত পণ্য (<?= $hasImageCount ?>)
                </a>
                <a href="<?= $base ?>/admin/products/image-finder?filter=all<?= $categoryId ? '&category_id=' . $categoryId : '' ?>" 
                   class="px-4 py-2 rounded-lg transition-all <?= $filter === 'all' ? 'bg-white text-secondary-900 shadow-xs' : 'hover:text-secondary-900' ?>">
                    সব পণ্য (<?= $totalProducts ?>)
                </a>
            </div>

            <!-- Search & Category Filters -->
            <div class="flex items-center gap-3 flex-1 max-w-xl">
                <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
                
                <select name="category_id" onchange="this.form.submit()" class="px-3 py-2 bg-secondary-50 border border-secondary-300 rounded-xl text-xs font-semibold text-secondary-700 focus:ring-2 focus:ring-emerald-500">
                    <option value="">সব ক্যাটাগরি</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $categoryId == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <div class="relative flex-1">
                    <ion-icon name="search-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary-400 text-sm"></ion-icon>
                    <input type="text" 
                           name="search" 
                           id="productTableFilterInput"
                           value="<?= htmlspecialchars($search) ?>" 
                           placeholder="পণ্যের নাম বা SKU দিয়ে খুঁজুন..." 
                           class="w-full pl-9 pr-4 py-2 bg-secondary-50 border border-secondary-300 rounded-xl text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                </div>

                <button type="submit" class="px-4 py-2 bg-secondary-900 hover:bg-black text-white text-xs font-bold rounded-xl transition-colors">
                    ফিল্টার
                </button>
                <?php if ($search || $categoryId): ?>
                    <a href="<?= $base ?>/admin/products/image-finder?filter=<?= $filter ?>" class="p-2 text-secondary-400 hover:text-red-500 transition-colors" title="রিসেট">
                        <ion-icon name="close-circle-outline" class="text-xl"></ion-icon>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-2xl shadow-xs border border-secondary-200 overflow-hidden">
        <div class="p-4 bg-secondary-50/70 border-b border-secondary-200 flex items-center justify-between">
            <div class="text-xs font-bold text-secondary-700 flex items-center gap-2">
                <ion-icon name="list-outline" class="text-base text-secondary-500"></ion-icon>
                <span>মোট প্রদর্শিত পণ্য: <strong class="text-secondary-900"><?= count($products) ?></strong> টি</span>
            </div>
            <div class="text-[11px] text-secondary-500">
                যেকোনো পণ্যের <span class="font-bold text-emerald-700">"ওয়েব থেকে ছবি খুঁজুন"</span> বাটনে ক্লিক করুন
            </div>
        </div>

        <?php if (empty($products)): ?>
            <div class="py-16 text-center">
                <div class="w-16 h-16 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center mx-auto mb-3 text-3xl">
                    <ion-icon name="checkmark-done-circle-outline"></ion-icon>
                </div>
                <h3 class="text-base font-bold text-secondary-900">কোনো পণ্য পাওয়া যায়নি</h3>
                <p class="text-xs text-secondary-500 mt-1">সব পণ্যে ছবি যোগ করা সম্পন্ন হয়েছে অথবা ফিল্টারের সাথে মিলছে না।</p>
                <div class="mt-4">
                    <a href="<?= $base ?>/admin/products/image-finder?filter=all" class="text-xs font-bold text-emerald-600 hover:underline">
                        সব পণ্য দেখুন →
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" id="productsFinderTable">
                    <thead>
                        <tr class="border-b border-secondary-200 bg-secondary-50/50 text-[11px] font-bold uppercase tracking-wider text-secondary-500">
                            <th class="py-3.5 px-4 w-16 text-center">আইডি</th>
                            <th class="py-3.5 px-4">পণ্য ও বর্তমান ছবি</th>
                            <th class="py-3.5 px-4">ক্যাটাগরি</th>
                            <th class="py-3.5 px-4">SKU / কোড</th>
                            <th class="py-3.5 px-4 text-right">বিক্রয় মূল্য</th>
                            <th class="py-3.5 px-4 text-center">অ্যাকশন (১-ক্লিক ইমেজ ফাইন্ডার)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-secondary-100 text-xs">
                        <?php foreach ($products as $idx => $p): 
                            $hasImg = !empty($p['image_path']);
                            $pImg = $hasImg ? htmlspecialchars($p['image_path']) : ($base ?: '') . '/images/default-product.svg';
                        ?>
                            <tr class="hover:bg-emerald-50/30 transition-colors group product-row" 
                                id="product-row-<?= $p['id'] ?>"
                                data-id="<?= $p['id'] ?>"
                                data-name="<?= htmlspecialchars($p['name']) ?>"
                                data-sku="<?= htmlspecialchars($p['sku'] ?? '') ?>"
                                data-category="<?= htmlspecialchars($p['category_name'] ?? '') ?>"
                                data-has-image="<?= $hasImg ? '1' : '0' ?>">
                                
                                <td class="py-3 px-4 text-center font-mono text-secondary-400 text-[11px]">
                                    #<?= $p['id'] ?>
                                </td>

                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="relative w-12 h-12 rounded-xl bg-white border border-secondary-200 flex-shrink-0 flex items-center justify-center overflow-hidden shadow-2xs group-hover:border-emerald-300 transition-colors">
                                            <img src="<?= $pImg ?>" 
                                                 alt="<?= htmlspecialchars($p['name']) ?>" 
                                                 id="preview-img-<?= $p['id'] ?>"
                                                 class="w-full h-full object-contain p-0.5"
                                                 onerror="this.src='<?= ($base ?: '') ?>/images/default-product.svg'">
                                            <?php if (!$hasImg): ?>
                                                <span class="absolute bottom-0 inset-x-0 bg-amber-500 text-white text-[8px] font-bold text-center py-0.2 leading-tight" id="badge-missing-<?= $p['id'] ?>">
                                                    ছবি নেই
                                                </span>
                                            <?php else: ?>
                                                <span class="absolute bottom-0 inset-x-0 bg-emerald-600 text-white text-[8px] font-bold text-center py-0.2 leading-tight" id="badge-missing-<?= $p['id'] ?>">
                                                    যুক্ত আছে
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-bold text-secondary-900 text-sm group-hover:text-emerald-700 transition-colors product-title">
                                                <?= htmlspecialchars($p['name']) ?>
                                            </div>
                                            <div class="text-[11px] text-secondary-400 mt-0.5">
                                                একক: <span class="font-semibold text-secondary-600"><?= htmlspecialchars($p['base_unit'] ?? 'pcs') ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3 px-4 text-secondary-700">
                                    <span class="px-2.5 py-1 rounded-lg bg-secondary-100 font-medium text-[11px] border border-secondary-200">
                                        <?= htmlspecialchars($p['category_name'] ?? 'Uncategorized') ?>
                                    </span>
                                </td>

                                <td class="py-3 px-4 font-mono text-secondary-500 text-[11px]">
                                    <?= htmlspecialchars($p['sku'] ?? 'N/A') ?>
                                </td>

                                <td class="py-3 px-4 text-right font-bold text-secondary-900">
                                    ৳<?= number_format($p['sell_price'], 2) ?>
                                </td>

                                <td class="py-3 px-4 text-center">
                                    <button type="button" 
                                            onclick="openImageFinderModal(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['name'])) ?>', '<?= htmlspecialchars(addslashes($p['sku'] ?? '')) ?>', '<?= htmlspecialchars(addslashes($p['image_path'] ?? '')) ?>')"
                                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all shadow-xs <?= $hasImg ? 'bg-secondary-100 hover:bg-secondary-200 text-secondary-700' : 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-emerald-700/20' ?>">
                                        <ion-icon name="sparkles" class="text-sm text-amber-300"></ion-icon>
                                        <span><?= $hasImg ? 'ছবি পরিবর্তন' : 'ওয়েব থেকে ছবি খুঁজুন' ?></span>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ========================================================== -->
<!-- 1-CLICK IMAGE FINDER INTERACTIVE MODAL                     -->
<!-- ========================================================== -->
<div id="imageFinderModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 sm:p-6 transition-all duration-200">
    <div class="bg-white rounded-3xl shadow-2xl border border-secondary-200 w-full max-w-4xl overflow-hidden flex flex-col max-h-[90vh] transform transition-all scale-95 opacity-0" id="imageFinderModalContent">
        
        <!-- Modal Top Bar -->
        <div class="px-6 py-4 bg-gradient-to-r from-secondary-900 via-slate-800 to-secondary-900 text-white flex items-center justify-between border-b border-secondary-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-400/30 text-emerald-400 flex items-center justify-center text-xl">
                    <ion-icon name="sparkles"></ion-icon>
                </div>
                <div>
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <span>ওয়েব ইমেজ ফাইন্ডার (Shwapno & Web)</span>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-[10px] text-emerald-300 font-semibold">1-Click Save</span>
                    </h3>
                    <p class="text-[11px] text-secondary-300" id="modalProductSubtitle">পণ্য নির্বাচন করুন...</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" id="nextProductBtn" onclick="goToNextProduct()" class="px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white text-xs font-semibold transition-colors flex items-center gap-1.5" title="পরবর্তী ছবিহীন পণ্য">
                    <span>পরবর্তী পণ্য</span>
                    <ion-icon name="arrow-forward-outline"></ion-icon>
                </button>
                <button type="button" onclick="closeImageFinderModal()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-secondary-300 hover:text-white flex items-center justify-center transition-colors">
                    <ion-icon name="close-outline" class="text-xl"></ion-icon>
                </button>
            </div>
        </div>

        <!-- Search Bar & Controls Inside Modal -->
        <div class="p-5 bg-secondary-50 border-b border-secondary-200 space-y-3">
            <div class="flex items-center gap-2">
                <div class="relative flex-1">
                    <ion-icon name="search-outline" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-secondary-400 text-base"></ion-icon>
                    <input type="text" 
                           id="modalSearchQuery" 
                           placeholder="পণ্য সার্চের কি-ওয়ার্ড পরিবর্তন করতে পারেন..." 
                           class="w-full pl-10 pr-4 py-2.5 bg-white border border-secondary-300 rounded-xl text-xs font-semibold text-secondary-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all shadow-2xs"
                           onkeydown="if(event.key === 'Enter') { searchCandidateImages(); }">
                </div>
                <button type="button" 
                        onclick="searchCandidateImages()" 
                        id="modalSearchBtn"
                        class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-emerald-700/20 flex items-center gap-2">
                    <ion-icon name="search-outline"></ion-icon>
                    <span>সার্চ করুন</span>
                </button>
            </div>

            <!-- Direct URL Paste Accordion -->
            <div class="flex items-center justify-between text-[11px] text-secondary-500">
                <div class="flex items-center gap-2">
                    <span class="font-bold text-secondary-700 flex items-center gap-1">
                        <ion-icon name="checkmark-done-circle" class="text-emerald-600 text-sm"></ion-icon>
                        সোর্স:
                    </span>
                    <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold border border-emerald-200">Shwapno.com Official</span>
                    <span class="px-2 py-0.5 rounded bg-secondary-200 text-secondary-700 font-medium">Grocery Database</span>
                </div>
                <button type="button" onclick="toggleDirectUrlBox()" class="text-emerald-700 font-bold hover:underline flex items-center gap-1">
                    <ion-icon name="link-outline"></ion-icon>
                    সরাসরি ছবি লিংক পেস্ট করুন
                </button>
            </div>

            <!-- Direct URL Box (Collapsible) -->
            <div id="directUrlBox" class="hidden p-3 bg-white rounded-xl border border-secondary-300 space-y-2">
                <label class="block text-[11px] font-bold text-secondary-700">বাহ্যিক ছবির সরাসরি লিংক (External Image URL):</label>
                <div class="flex items-center gap-2">
                    <input type="url" id="directImageUrlInput" placeholder="https://example.com/images/product.jpg" class="flex-1 px-3 py-1.5 border border-secondary-300 rounded-lg text-xs font-mono">
                    <button type="button" onclick="saveDirectUrlImage()" class="px-4 py-1.5 bg-secondary-900 hover:bg-black text-white rounded-lg text-xs font-bold transition-colors">
                        লিংক সেভ করুন
                    </button>
                </div>
            </div>
        </div>

        <!-- Candidate Images Area (Scrollable) -->
        <div class="p-6 overflow-y-auto flex-1 min-h-[300px] bg-slate-50/50" id="modalCandidatesContainer">
            <!-- Dynamically populated via JS -->
            <div id="modalLoadingState" class="hidden py-16 text-center">
                <div class="inline-block w-10 h-10 border-4 border-emerald-500 border-t-transparent rounded-full animate-spin"></div>
                <div class="text-xs font-bold text-secondary-700 mt-3">Shwapno ও ওয়েব থেকে পণ্যের ছবি খোঁজা হচ্ছে...</div>
                <div class="text-[11px] text-secondary-400 mt-1">অনুগ্রহ করে একটু অপেক্ষা করুন</div>
            </div>

            <div id="modalEmptyState" class="hidden py-16 text-center">
                <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center mx-auto mb-3 text-2xl">
                    <ion-icon name="alert-circle-outline"></ion-icon>
                </div>
                <h4 class="text-sm font-bold text-secondary-800">কোনো ছবি পাওয়া যায়নি</h4>
                <p class="text-xs text-secondary-500 mt-1">সার্চ কি-ওয়ার্ডে পণ্যের সাধারণ নাম লিখে আবার সার্চ করুন (যেমন: "তীর আটা" বা "রূপচাঁদা তেল")।</p>
            </div>

            <!-- Grid of candidates -->
            <div id="candidateImagesGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                <!-- Cards injected dynamically -->
            </div>
        </div>

        <!-- Modal Bottom Bar / Current Active Status -->
        <div class="px-6 py-3.5 bg-white border-t border-secondary-200 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg border border-secondary-200 overflow-hidden bg-secondary-50 flex-shrink-0 flex items-center justify-center">
                    <img src="<?= ($base ?: '') ?>/images/default-product.svg" id="modalCurrentPreview" class="w-full h-full object-contain p-0.5">
                </div>
                <div>
                    <span class="text-secondary-500">বর্তমান অবস্থা:</span>
                    <span id="modalCurrentStatus" class="font-bold text-secondary-800 ml-1">যাচাই চলছে...</span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span id="modalSaveSuccessAlert" class="hidden text-emerald-700 font-bold flex items-center gap-1.5 animate-pulse">
                    <ion-icon name="checkmark-circle" class="text-base text-emerald-600"></ion-icon>
                    ছবি সফলভাবে সেভ হয়েছে!
                </span>
                <button type="button" onclick="closeImageFinderModal()" class="px-4 py-2 bg-secondary-200 hover:bg-secondary-300 text-secondary-800 font-bold rounded-xl transition-colors">
                    বন্ধ করুন
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Global state
let currentProductId = null;
let currentProductName = '';
let currentProductSku = '';
let candidateResults = [];
const csrfToken = '<?= \Core\CSRF::token() ?>';
const baseUri = '<?= $base ?>';

// Live Search in Table
document.getElementById('productTableFilterInput')?.addEventListener('input', function(e) {
    const q = e.target.value.toLowerCase().trim();
    const rows = document.querySelectorAll('.product-row');
    rows.forEach(r => {
        const name = r.getAttribute('data-name')?.toLowerCase() || '';
        const sku = r.getAttribute('data-sku')?.toLowerCase() || '';
        const cat = r.getAttribute('data-category')?.toLowerCase() || '';
        if (!q || name.includes(q) || sku.includes(q) || cat.includes(q)) {
            r.style.display = '';
        } else {
            r.style.display = 'none';
        }
    });
});

// Modal Open / Close
function openImageFinderModal(id, name, sku, currentImg) {
    currentProductId = id;
    currentProductName = name;
    currentProductSku = sku;

    const modal = document.getElementById('imageFinderModal');
    const content = document.getElementById('imageFinderModalContent');
    const queryInput = document.getElementById('modalSearchQuery');
    const subtitle = document.getElementById('modalProductSubtitle');
    const currPreview = document.getElementById('modalCurrentPreview');
    const currStatus = document.getElementById('modalCurrentStatus');
    const successAlert = document.getElementById('modalSaveSuccessAlert');

    subtitle.innerHTML = `<span class="font-bold text-white">${name}</span> ${sku ? `(${sku})` : ''}`;
    queryInput.value = name;
    currPreview.src = currentImg ? currentImg : `${baseUri}/images/default-product.svg`;
    currStatus.innerHTML = currentImg ? '<span class="text-emerald-600 font-bold">ছবি যুক্ত আছে</span>' : '<span class="text-amber-600 font-bold">ছবি নেই (Missing)</span>';
    successAlert.classList.add('hidden');

    modal.classList.remove('hidden');
    setTimeout(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }, 10);

    // Trigger auto-search immediately
    searchCandidateImages();
}

function closeImageFinderModal() {
    const modal = document.getElementById('imageFinderModal');
    const content = document.getElementById('imageFinderModalContent');
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 150);
}

// Search candidates from backend
async function searchCandidateImages() {
    const query = document.getElementById('modalSearchQuery').value.trim();
    if (!query) return;

    const loadingState = document.getElementById('modalLoadingState');
    const emptyState = document.getElementById('modalEmptyState');
    const grid = document.getElementById('candidateImagesGrid');
    const searchBtn = document.getElementById('modalSearchBtn');

    grid.innerHTML = '';
    emptyState.classList.add('hidden');
    loadingState.classList.remove('hidden');
    searchBtn.disabled = true;

    try {
        const response = await fetch(`${baseUri}/admin/products/search-web-images?query=${encodeURIComponent(query)}`);
        const data = await response.json();

        loadingState.classList.add('hidden');
        searchBtn.disabled = false;

        if (data.success && data.results && data.results.length > 0) {
            candidateResults = data.results;
            renderCandidateImages(data.results);
        } else {
            emptyState.classList.remove('hidden');
        }
    } catch (err) {
        console.error("Search error:", err);
        loadingState.classList.add('hidden');
        searchBtn.disabled = false;
        emptyState.classList.remove('hidden');
    }
}

// Render candidate tiles
function renderCandidateImages(items) {
    const grid = document.getElementById('candidateImagesGrid');
    grid.innerHTML = '';

    items.forEach((item, index) => {
        const card = document.createElement('div');
        card.className = 'bg-white rounded-2xl border border-secondary-200 p-3 shadow-2xs hover:shadow-md hover:border-emerald-400 transition-all flex flex-col justify-between group relative overflow-hidden';
        card.id = `candidate-card-${index}`;

        const isShwapno = item.source.includes('Shwapno');
        const badgeColor = isShwapno ? 'bg-emerald-500 text-white' : 'bg-slate-700 text-white';

        card.innerHTML = `
            <div class="relative w-full aspect-square bg-slate-50 rounded-xl overflow-hidden mb-2.5 flex items-center justify-center border border-secondary-100 p-2">
                <img src="${item.thumbnail || item.image}" 
                     alt="${escapeHtml(item.title)}" 
                     loading="lazy"
                     class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-200"
                     onerror="this.src='${baseUri}/images/default-product.svg'">
                <span class="absolute top-2 left-2 px-2 py-0.5 rounded-md text-[9px] font-bold ${badgeColor} shadow-2xs">
                    ${item.source}
                </span>
                ${item.price ? `<span class="absolute top-2 right-2 px-1.5 py-0.5 rounded bg-white/90 backdrop-blur-xs text-[9px] font-bold text-secondary-800 shadow-2xs border border-secondary-200">${item.price}</span>` : ''}
            </div>

            <div class="mb-3">
                <div class="text-[11px] font-bold text-secondary-800 line-clamp-2 leading-tight" title="${escapeHtml(item.title)}">
                    ${escapeHtml(item.title)}
                </div>
            </div>

            <button type="button" 
                    onclick="saveCandidateImage(${index})" 
                    id="save-btn-${index}"
                    class="w-full py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-xs font-bold transition-all shadow-xs flex items-center justify-center gap-1.5">
                <ion-icon name="cloud-download-outline" class="text-sm"></ion-icon>
                <span>১-ক্লিকে সেভ</span>
            </button>
        `;

        grid.appendChild(card);
    });
}

// 1-Click Save candidate image
async function saveCandidateImage(index) {
    const item = candidateResults[index];
    if (!item || !currentProductId) return;

    const btn = document.getElementById(`save-btn-${index}`);
    const card = document.getElementById(`candidate-card-${index}`);
    const originalBtnHtml = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = `<span class="inline-block w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span> <span>সেভ হচ্ছে...</span>`;

    try {
        const formData = new FormData();
        formData.append('product_id', currentProductId);
        formData.append('image_url', item.image);
        formData.append('csrf_token', csrfToken);

        const res = await fetch(`${baseUri}/admin/products/save-web-image`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });

        const data = await res.json();

        if (data.success) {
            // Visual success indicator on button & card
            btn.className = 'w-full py-2 px-3 rounded-xl bg-emerald-700 text-white text-xs font-bold flex items-center justify-center gap-1.5 shadow-xs';
            btn.innerHTML = `<ion-icon name="checkmark-circle" class="text-sm"></ion-icon> <span>সংরক্ষিত!</span>`;
            card.classList.add('ring-2', 'ring-emerald-500');

            // Update modal status & preview
            document.getElementById('modalCurrentPreview').src = data.image_path;
            document.getElementById('modalCurrentStatus').innerHTML = '<span class="text-emerald-600 font-bold">✓ ছবি সেভ সম্পন্ন</span>';
            const successAlert = document.getElementById('modalSaveSuccessAlert');
            successAlert.classList.remove('hidden');

            // Update row in table background
            updateTableRowImage(currentProductId, data.image_path);

        } else {
            alert(data.message || 'ইমেজ সেভ করতে সমস্যা হয়েছে।');
            btn.disabled = false;
            btn.innerHTML = originalBtnHtml;
        }
    } catch (err) {
        console.error("Save error:", err);
        alert('সার্ভার এরর: ইমেজ ডাউনলোড সম্পন্ন করা যায়নি।');
        btn.disabled = false;
        btn.innerHTML = originalBtnHtml;
    }
}

// Update table row in background
function updateTableRowImage(prodId, newImgPath) {
    const row = document.getElementById(`product-row-${prodId}`);
    if (row) {
        const img = document.getElementById(`preview-img-${prodId}`);
        if (img) img.src = newImgPath;

        const badge = document.getElementById(`badge-missing-${prodId}`);
        if (badge) {
            badge.className = 'absolute bottom-0 inset-x-0 bg-emerald-600 text-white text-[8px] font-bold text-center py-0.2 leading-tight';
            badge.innerText = 'যুক্ত আছে';
        }
        row.setAttribute('data-has-image', '1');
    }
}

// Go to next product missing an image without closing modal
function goToNextProduct() {
    const rows = Array.from(document.querySelectorAll('.product-row'));
    const currentIndex = rows.findIndex(r => r.getAttribute('data-id') == currentProductId);

    // Look for the next row that has data-has-image="0"
    let nextRow = null;
    for (let i = currentIndex + 1; i < rows.length; i++) {
        if (rows[i].getAttribute('data-has-image') === '0') {
            nextRow = rows[i];
            break;
        }
    }

    // If not found after, wrap around from beginning
    if (!nextRow) {
        for (let i = 0; i <= currentIndex; i++) {
            if (rows[i].getAttribute('data-has-image') === '0') {
                nextRow = rows[i];
                break;
            }
        }
    }

    if (nextRow) {
        const nextId = nextRow.getAttribute('data-id');
        const nextName = nextRow.getAttribute('data-name');
        const nextSku = nextRow.getAttribute('data-sku');
        openImageFinderModal(nextId, nextName, nextSku, '');
    } else {
        alert('অভিনন্দন! এই তালিকার সব পণ্যের ছবি যুক্ত করা সম্পন্ন হয়েছে।');
    }
}

// Toggle Direct URL Box
function toggleDirectUrlBox() {
    const box = document.getElementById('directUrlBox');
    box.classList.toggle('hidden');
}

// Save Direct URL Image
async function saveDirectUrlImage() {
    const url = document.getElementById('directImageUrlInput').value.trim();
    if (!url || !currentProductId) {
        alert('অনুগ্রহ করে সঠিক ছবির লিংক দিন!');
        return;
    }

    try {
        const formData = new FormData();
        formData.append('product_id', currentProductId);
        formData.append('image_url', url);
        formData.append('csrf_token', csrfToken);

        const res = await fetch(`${baseUri}/admin/products/save-web-image`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });

        const data = await res.json();
        if (data.success) {
            document.getElementById('modalCurrentPreview').src = data.image_path;
            document.getElementById('modalCurrentStatus').innerHTML = '<span class="text-emerald-600 font-bold">✓ ছবি সেভ সম্পন্ন</span>';
            updateTableRowImage(currentProductId, data.image_path);
            alert('ছবি সফলভাবে সেভ করা হয়েছে!');
            toggleDirectUrlBox();
        } else {
            alert(data.message || 'ছবি সেভ করতে সমস্যা হয়েছে।');
        }
    } catch (err) {
        alert('সার্ভার রিকোয়েস্টে সমস্যা হয়েছে।');
    }
}

function escapeHtml(text) {
    if (!text) return '';
    return String(text).replace(/[&<>"']/g, function(m) {
        return {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        }[m];
    });
}
</script>
