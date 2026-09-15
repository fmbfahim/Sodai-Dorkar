<div class="max-w-4xl mx-auto my-8 space-y-6">
    <!-- Top Breadcrumb & Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl shadow-sm border border-secondary-100">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-primary-600 uppercase tracking-wider mb-1">
                <ion-icon name="cloud-upload-outline" class="text-base"></ion-icon>
                <span>Bulk Data Import System</span>
            </div>
            <h1 class="text-2xl font-black text-secondary-900">Bulk Product CSV Import (বাল্ক প্রোডাক্ট ইমপোর্ট)</h1>
            <p class="text-secondary-500 text-xs mt-1">৩০০+ পণ্য দ্রুত ও নিরাপদে ইম্পোর্ট করুন। ডুপ্লিকেট রোধ ও লাইভ প্রগ্রেস বার সহ।</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="/sodai-dorkar/public/admin/products/bulk-demo" 
               class="px-4 py-2.5 text-xs bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 rounded-xl font-bold transition-colors flex items-center gap-1.5 shadow-2xs">
                <ion-icon name="download-outline" class="text-base"></ion-icon>
                <span>ডেমো CSV ডাউনলোড</span>
            </a>
            <a href="/sodai-dorkar/public/admin/products/dashboard" 
               class="px-4 py-2.5 text-xs bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100 rounded-xl font-bold transition-colors flex items-center gap-1.5 shadow-2xs">
                <ion-icon name="grid-outline" class="text-base"></ion-icon>
                <span>ড্যাশবোর্ড</span>
            </a>
        </div>
    </div>

    <!-- Main Import Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-secondary-100 p-8">
        
        <!-- Step 1: File Selection Area -->
        <div id="uploadSection" class="space-y-6">
            <!-- Dropzone -->
            <div id="dropZone" 
                 class="relative border-2 border-dashed border-secondary-300 hover:border-primary-500 rounded-2xl p-10 text-center transition-all cursor-pointer bg-secondary-50/40 hover:bg-primary-50/40 group">
                <input type="file" id="csvFileInput" accept=".csv" class="hidden">
                
                <div id="dropZonePrompt" class="space-y-3">
                    <div class="w-16 h-16 bg-primary-100/60 group-hover:bg-primary-100 rounded-2xl flex items-center justify-center mx-auto text-primary-600 text-3xl group-hover:scale-110 transition-transform">
                        <ion-icon name="cloud-upload-outline"></ion-icon>
                    </div>
                    <div>
                        <p class="text-base font-bold text-secondary-800 group-hover:text-primary-700">CSV ফাইল এখানে ড্র্যাগ করুন অথবা ক্লিক করে সিলেক্ট করুন</p>
                        <p class="text-xs text-secondary-400 mt-1">৩০০ বা তার বেশি পণ্য সংবলিত .csv ফাইল সাপোর্ট করে (সর্বোচ্চ ১০MB)</p>
                    </div>
                    <span class="inline-block px-4 py-2 bg-white text-secondary-700 border border-secondary-200 rounded-xl text-xs font-bold shadow-2xs group-hover:border-primary-300">
                        ফাইল ব্রাউজ করুন
                    </span>
                </div>

                <!-- Selected File Display Card (Revealed when file is picked) -->
                <div id="fileInfoCard" class="hidden text-left bg-white p-5 rounded-xl border border-primary-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl flex-shrink-0">
                                <ion-icon name="document-text-outline"></ion-icon>
                            </div>
                            <div>
                                <h4 id="fileNameDisplay" class="text-sm font-bold text-secondary-900 truncate max-w-xs sm:max-w-md">filename.csv</h4>
                                <div class="flex items-center gap-3 text-xs text-secondary-500 mt-0.5">
                                    <span id="fileSizeDisplay">0 KB</span>
                                    <span>&bull;</span>
                                    <span id="rowCountBadge" class="font-bold text-primary-600 bg-primary-50 px-2 py-0.5 rounded border border-primary-100">0 টি পণ্য শনাক্ত হয়েছে</span>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="removeFileBtn" class="p-2 rounded-lg text-secondary-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="Remove file">
                            <ion-icon name="close-circle-outline" class="text-2xl"></ion-icon>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Duplicate Handling Setting (User Requirement: "একই প্রোডাক্ট ২বার ইনপুট হবে না") -->
            <div class="bg-secondary-50/70 p-5 rounded-2xl border border-secondary-200 space-y-3">
                <div class="flex items-center gap-2 text-secondary-900 font-bold text-sm">
                    <ion-icon name="shield-checkmark-outline" class="text-primary-600 text-lg"></ion-icon>
                    <span>ডুপ্লিকেট প্রতিরোধ কৌশল (Duplicate Prevention Rules):</span>
                </div>
                <p class="text-xs text-secondary-500">যদি CSV ফাইলের কোনো পণ্যের নাম বা SKU আগে থেকেই ডাটাবেসে থাকে, তবে কী করা হবে?</p>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <label class="flex items-start gap-3 p-3.5 bg-white rounded-xl border border-secondary-200 cursor-pointer hover:border-primary-400 transition-all">
                        <input type="radio" name="duplicate_action" value="skip" checked class="mt-0.5 text-primary-600 focus:ring-primary-500">
                        <div>
                            <span class="block text-xs font-bold text-secondary-900">এড়িয়ে যান (Skip Duplicates - Recommended)</span>
                            <span class="block text-[11px] text-secondary-500 mt-0.5">আগের পণ্যটি অপরিবর্তিত থাকবে এবং ২য় বার তৈরি হবে না।</span>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-3.5 bg-white rounded-xl border border-secondary-200 cursor-pointer hover:border-primary-400 transition-all">
                        <input type="radio" name="duplicate_action" value="update" class="mt-0.5 text-primary-600 focus:ring-primary-500">
                        <div>
                            <span class="block text-xs font-bold text-secondary-900">আপডেট করুন (Update Existing Stock & Price)</span>
                            <span class="block text-[11px] text-secondary-500 mt-0.5">নতুন কোনো রো যুক্ত না করে পূর্বের পণ্যের দাম ও স্টক হালনাগাদ হবে।</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Preview of Detected Items -->
            <div id="previewTableContainer" class="hidden space-y-2">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-bold text-secondary-800 uppercase tracking-wider">CSV ডেটা প্রিভিউ (প্রথম ৩টি পণ্য):</h4>
                    <span id="previewCountText" class="text-xs text-secondary-500 font-medium"></span>
                </div>
                <div class="overflow-x-auto border border-secondary-200 rounded-xl bg-white">
                    <table class="w-full text-left text-xs text-secondary-600">
                        <thead class="bg-secondary-50 text-secondary-600 font-bold border-b border-secondary-200">
                            <tr>
                                <th class="px-3 py-2">#</th>
                                <th class="px-3 py-2">নাম (Product Name)</th>
                                <th class="px-3 py-2">SKU</th>
                                <th class="px-3 py-2">ক্যাটাগরি পাথ</th>
                                <th class="px-3 py-2 text-right">ক্রয় মূল্য</th>
                                <th class="px-3 py-2 text-right">বিক্রয় মূল্য</th>
                                <th class="px-3 py-2 text-center">স্টক</th>
                            </tr>
                        </thead>
                        <tbody id="previewTableBody" class="divide-y divide-secondary-100 font-medium">
                            <!-- Injected by JS -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Start Import Action Button -->
            <div class="flex items-center justify-between pt-2">
                <a href="/sodai-dorkar/public/admin/products" class="px-5 py-2.5 border border-secondary-300 rounded-xl text-secondary-600 font-bold text-xs hover:bg-secondary-50 transition-colors">
                    বাতিল (Cancel)
                </a>
                <button type="button" 
                        id="startImportBtn" 
                        disabled 
                        class="px-6 py-2.5 bg-secondary-300 text-white font-bold text-xs rounded-xl transition-all shadow-sm flex items-center gap-2 cursor-not-allowed">
                    <ion-icon name="cloud-upload-outline" class="text-base"></ion-icon>
                    <span id="startBtnLabel">ফাইল নির্বাচন করুন</span>
                </button>
            </div>
        </div>

        <!-- Step 2: Live Progress Bar Section (Revealed during import) -->
        <div id="progressSection" class="hidden space-y-6 py-4">
            <div class="text-center space-y-2">
                <div class="w-14 h-14 bg-primary-50 rounded-2xl flex items-center justify-center mx-auto text-primary-600 text-3xl animate-bounce">
                    <ion-icon name="sync-outline" class="animate-spin"></ion-icon>
                </div>
                <h3 class="text-xl font-black text-secondary-900">পণ্য ইমপোর্ট প্রক্রিয়াধীন...</h3>
                <p class="text-xs text-secondary-500">৩০০+ পণ্য সার্ভারে নিরাপদ ব্যাচে আপলোড হচ্ছে, অনুগ্রহ করে অপেক্ষা করুন।</p>
            </div>

            <!-- Progress Bar -->
            <div class="space-y-2 max-w-2xl mx-auto">
                <div class="flex justify-between text-xs font-bold">
                    <span id="progressStatusText" class="text-secondary-700">শুরু হচ্ছে...</span>
                    <span id="progressPercentage" class="text-primary-600 font-black">0%</span>
                </div>
                <div class="w-full bg-secondary-100 h-4 rounded-full overflow-hidden p-0.5 border border-secondary-200">
                    <div id="progressBarFill" 
                         class="bg-gradient-to-r from-primary-500 to-emerald-500 h-full rounded-full transition-all duration-300 ease-out shadow-sm" 
                         style="width: 0%"></div>
                </div>
            </div>

            <!-- Live Status Counters Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 max-w-2xl mx-auto pt-2">
                <div class="p-3 rounded-xl bg-blue-50/70 border border-blue-200 text-center">
                    <span class="block text-[11px] font-bold text-blue-700 uppercase">মোট প্রসেসড</span>
                    <span id="counterProcessed" class="block text-xl font-black text-blue-900 mt-0.5">0</span>
                </div>
                <div class="p-3 rounded-xl bg-emerald-50/70 border border-emerald-200 text-center">
                    <span class="block text-[11px] font-bold text-emerald-700 uppercase">নতুন যোগ হয়েছে</span>
                    <span id="counterAdded" class="block text-xl font-black text-emerald-900 mt-0.5">0</span>
                </div>
                <div class="p-3 rounded-xl bg-indigo-50/70 border border-indigo-200 text-center">
                    <span class="block text-[11px] font-bold text-indigo-700 uppercase">আপডেট হয়েছে</span>
                    <span id="counterUpdated" class="block text-xl font-black text-indigo-900 mt-0.5">0</span>
                </div>
                <div class="p-3 rounded-xl bg-amber-50/70 border border-amber-200 text-center">
                    <span class="block text-[11px] font-bold text-amber-700 uppercase">স্কিপ (ডুপ্লিকেট)</span>
                    <span id="counterSkipped" class="block text-xl font-black text-amber-900 mt-0.5">0</span>
                </div>
            </div>
        </div>

        <!-- Step 3: Completion Result Summary Section -->
        <div id="completionSection" class="hidden space-y-6 py-4">
            <div class="text-center space-y-2">
                <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-3xl flex items-center justify-center mx-auto text-4xl shadow-sm">
                    <ion-icon name="checkmark-done-circle-outline"></ion-icon>
                </div>
                <h3 class="text-2xl font-black text-secondary-900">ইম্পোর্ট সফলভাবে সম্পন্ন হয়েছে!</h3>
                <p id="completionSubtext" class="text-xs text-secondary-500">সকল পণ্যের তথ্য ডাটাবেসে নিরাপদে সেভ হয়েছে।</p>
            </div>

            <!-- Final Summary Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 max-w-2xl mx-auto">
                <div class="p-4 rounded-2xl bg-white border border-secondary-200 shadow-2xs text-center">
                    <p class="text-xs text-secondary-400 font-semibold uppercase">মোট পণ্য</p>
                    <h4 id="finalTotal" class="text-2xl font-black text-secondary-900 mt-1">0</h4>
                </div>
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-center">
                    <p class="text-xs text-emerald-700 font-semibold uppercase">নতুন যোগ</p>
                    <h4 id="finalAdded" class="text-2xl font-black text-emerald-900 mt-1">0</h4>
                </div>
                <div class="p-4 rounded-2xl bg-indigo-50 border border-indigo-200 text-center">
                    <p class="text-xs text-indigo-700 font-semibold uppercase">হালনাগাদ</p>
                    <h4 id="finalUpdated" class="text-2xl font-black text-indigo-900 mt-1">0</h4>
                </div>
                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-center">
                    <p class="text-xs text-amber-700 font-semibold uppercase">স্কিপ (ডুপ্লিকেট)</p>
                    <h4 id="finalSkipped" class="text-2xl font-black text-amber-900 mt-1">0</h4>
                </div>
            </div>

            <!-- Error Notification Alert if any row failed -->
            <div id="errorReportContainer" class="hidden max-w-2xl mx-auto p-4 rounded-xl bg-red-50 border border-red-200 text-xs text-red-800 space-y-1">
                <div class="font-bold flex items-center gap-1.5 text-red-900">
                    <ion-icon name="alert-circle" class="text-base"></ion-icon>
                    <span>কিছু পণ্যে ত্রুটি পাওয়া গেছে (<span id="finalErrorsCount">0</span> টি):</span>
                </div>
                <ul id="errorList" class="list-disc list-inside space-y-0.5 text-[11px] text-red-700 max-h-32 overflow-y-auto pl-2"></ul>
            </div>

            <!-- Action Navigation Buttons -->
            <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
                <a href="/sodai-dorkar/public/admin/products" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-xs font-bold shadow-md transition-all flex items-center gap-1.5">
                    <ion-icon name="list-outline" class="text-base"></ion-icon>
                    <span>পণ্য তালিকা দেখুন</span>
                </a>
                <a href="/sodai-dorkar/public/admin/products/dashboard" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md transition-all flex items-center gap-1.5">
                    <ion-icon name="grid-outline" class="text-base"></ion-icon>
                    <span>প্রোডাক্ট ড্যাশবোর্ড</span>
                </a>
                <button type="button" id="resetImportBtn" class="px-5 py-2.5 bg-white border border-secondary-300 text-secondary-700 hover:bg-secondary-50 rounded-xl text-xs font-bold transition-all">
                    আরেকটি ফাইল ইম্পোর্ট করুন
                </button>
            </div>
        </div>

    </div>

    <!-- CSV Format Reference Help Guide -->
    <div class="bg-white rounded-2xl shadow-sm border border-secondary-100 p-6 space-y-3">
        <div class="flex items-center gap-2 text-secondary-900 font-bold text-sm">
            <ion-icon name="information-circle-outline" class="text-primary-600 text-lg"></ion-icon>
            <span>CSV কলামের ক্রম ও নির্দেশিকা (CSV Column Specification):</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs text-secondary-600">
            <div class="p-3 rounded-xl bg-secondary-50/70 border border-secondary-200">
                <strong class="text-secondary-800">১. Name <span class="text-red-500">*</span>:</strong> পণ্যের নাম<br>
                <strong class="text-secondary-800">২. SKU:</strong> ইউনিক কোড (ঐচ্ছিক)<br>
                <strong class="text-secondary-800">৩. Description:</strong> বিবরণ<br>
                <strong class="text-secondary-800">৪. Buy Price:</strong> ক্রয় মূল্য (টাকা)<br>
                <strong class="text-secondary-800">৫. Regular Price:</strong> নিয়মিত মূল্য
            </div>
            <div class="p-3 rounded-xl bg-secondary-50/70 border border-secondary-200">
                <strong class="text-secondary-800">৬. Sell Price <span class="text-red-500">*</span>:</strong> বিক্রয় মূল্য<br>
                <strong class="text-secondary-800">৭. Stock Qty:</strong> মজুদ সংখ্যা<br>
                <strong class="text-secondary-800">৮. Category Path:</strong> Food &gt; Rice &gt; Miniket<br>
                <strong class="text-secondary-800">৯. Brand Name:</strong> ব্র্যান্ড (যেমন Teer)<br>
                <strong class="text-secondary-800">১০. Vendor Name:</strong> সাপ্লায়ার নাম
            </div>
            <div class="p-3 rounded-xl bg-secondary-50/70 border border-secondary-200">
                <strong class="text-secondary-800">১১. Unit Type:</strong> sack_kg, drum_liter, piece<br>
                <strong class="text-secondary-800">১২. Base Unit:</strong> kg, liter, pcs, gm<br>
                <strong class="text-secondary-800">১৩. Purchase Unit:</strong> বস্তা, ড্রাম, বক্স<br>
                <strong class="text-secondary-800">১৪. Purchase Qty:</strong> ৫০, ১৯০, ২৪<br>
                <strong class="text-secondary-800">১৫. Selling Unit:</strong> kg, pcs
            </div>
        </div>
    </div>
</div>

<!-- Robust In-Browser CSV Parser & Chunked AJAX Processor -->
<script>
let parsedProducts = [];
const CHUNK_SIZE = 35; // 35 items per request to effortlessly handle 300+ items without timeout

const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('csvFileInput');
const dropPrompt = document.getElementById('dropZonePrompt');
const fileCard = document.getElementById('fileInfoCard');
const fileNameDisplay = document.getElementById('fileNameDisplay');
const fileSizeDisplay = document.getElementById('fileSizeDisplay');
const rowCountBadge = document.getElementById('rowCountBadge');
const removeFileBtn = document.getElementById('removeFileBtn');

const previewContainer = document.getElementById('previewTableContainer');
const previewBody = document.getElementById('previewTableBody');
const previewCountText = document.getElementById('previewCountText');

const startBtn = document.getElementById('startImportBtn');
const startBtnLabel = document.getElementById('startBtnLabel');

const uploadSection = document.getElementById('uploadSection');
const progressSection = document.getElementById('progressSection');
const completionSection = document.getElementById('completionSection');

const progressBarFill = document.getElementById('progressBarFill');
const progressPercentage = document.getElementById('progressPercentage');
const progressStatusText = document.getElementById('progressStatusText');

const counterProcessed = document.getElementById('counterProcessed');
const counterAdded = document.getElementById('counterAdded');
const counterUpdated = document.getElementById('counterUpdated');
const counterSkipped = document.getElementById('counterSkipped');

// Drag & Drop Listeners
dropZone.addEventListener('click', (e) => {
    if (e.target.closest('#removeFileBtn')) return;
    fileInput.click();
});

dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('border-primary-500', 'bg-primary-50');
});

dropZone.addEventListener('dragleave', () => {
    dropZone.classList.remove('border-primary-500', 'bg-primary-50');
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('border-primary-500', 'bg-primary-50');
    if (e.dataTransfer.files.length > 0) {
        handleSelectedFile(e.dataTransfer.files[0]);
    }
});

fileInput.addEventListener('change', (e) => {
    if (e.target.files.length > 0) {
        handleSelectedFile(e.target.files[0]);
    }
});

removeFileBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    resetFileSelection();
});

function resetFileSelection() {
    fileInput.value = '';
    parsedProducts = [];
    dropPrompt.classList.remove('hidden');
    fileCard.classList.add('hidden');
    previewContainer.classList.add('hidden');
    previewBody.innerHTML = '';
    startBtn.disabled = true;
    startBtn.classList.remove('bg-primary-600', 'hover:bg-primary-700', 'cursor-pointer');
    startBtn.classList.add('bg-secondary-300', 'cursor-not-allowed');
    startBtnLabel.textContent = 'ফাইল নির্বাচন করুন';
}

function handleSelectedFile(file) {
    if (!file.name.toLowerCase().endsWith('.csv')) {
        alert('শুধুমাত্র .csv ফরম্যাটের ফাইল আপলোড করুন।');
        return;
    }

    fileNameDisplay.textContent = file.name;
    fileSizeDisplay.textContent = (file.size / 1024).toFixed(1) + ' KB';
    dropPrompt.classList.add('hidden');
    fileCard.classList.remove('hidden');

    const reader = new FileReader();
    reader.onload = function(e) {
        const text = e.target.result;
        parsedProducts = parseCsvText(text);

        if (parsedProducts.length === 0) {
            alert('সিএসভি ফাইলটিতে কোনো বৈধ পণ্যের তথ্য পাওয়া যায়নি। অনুগ্রহ করে ডেমো ফাইলটি দেখে নিন।');
            resetFileSelection();
            return;
        }

        rowCountBadge.textContent = `${parsedProducts.length} টি পণ্য শনাক্ত হয়েছে`;
        previewCountText.textContent = `মোট ${parsedProducts.length} টির মধ্যে প্রথম ৩টি দেখানো হচ্ছে`;

        // Render preview of first 3 rows
        previewBody.innerHTML = '';
        parsedProducts.slice(0, 3).forEach((item, idx) => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-secondary-50';
            tr.innerHTML = `
                <td class="px-3 py-2 text-secondary-400 font-mono">${idx + 1}</td>
                <td class="px-3 py-2 font-bold text-secondary-900">${escapeHtml(item.name || 'N/A')}</td>
                <td class="px-3 py-2 font-mono text-secondary-500">${escapeHtml(item.sku || 'Auto')}</td>
                <td class="px-3 py-2 text-secondary-600">${escapeHtml(item.category_path || 'None')}</td>
                <td class="px-3 py-2 text-right text-secondary-600">৳ ${parseFloat(item.buy_price || 0).toFixed(0)}</td>
                <td class="px-3 py-2 text-right font-bold text-emerald-700">৳ ${parseFloat(item.sell_price || 0).toFixed(0)}</td>
                <td class="px-3 py-2 text-center">
                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-secondary-100 text-secondary-700">
                        ${item.stock_qty || 0} ${escapeHtml(item.base_unit || 'pcs')}
                    </span>
                </td>
            `;
            previewBody.appendChild(tr);
        });

        previewContainer.classList.remove('hidden');

        // Enable Start Import Button
        startBtn.disabled = false;
        startBtn.classList.remove('bg-secondary-300', 'cursor-not-allowed');
        startBtn.classList.add('bg-primary-600', 'hover:bg-primary-700', 'cursor-pointer');
        startBtnLabel.textContent = `${parsedProducts.length} টি পণ্য ইমপোর্ট শুরু করুন`;
    };
    reader.readAsText(file, 'UTF-8');
}

// RFC-4180 Compliant In-Browser CSV Parser
function parseCsvText(text) {
    const lines = [];
    let row = [];
    let inQuotes = false;
    let currentField = '';

    // Remove BOM if present
    if (text.charCodeAt(0) === 0xFEFF) {
        text = text.substring(1);
    }

    for (let i = 0; i < text.length; i++) {
        const char = text[i];
        const nextChar = text[i + 1];

        if (char === '"') {
            if (inQuotes && nextChar === '"') {
                currentField += '"';
                i++; // Skip escaped quote
            } else {
                inQuotes = !inQuotes;
            }
        } else if (char === ',' && !inQuotes) {
            row.push(currentField.trim());
            currentField = '';
        } else if ((char === '\r' || char === '\n') && !inQuotes) {
            if (char === '\r' && nextChar === '\n') {
                i++; // Skip CRLF
            }
            row.push(currentField.trim());
            if (row.length > 0 && row.some(cell => cell.length > 0)) {
                lines.push(row);
            }
            row = [];
            currentField = '';
        } else {
            currentField += char;
        }
    }

    if (currentField.length > 0 || row.length > 0) {
        row.push(currentField.trim());
        if (row.some(cell => cell.length > 0)) {
            lines.push(row);
        }
    }

    if (lines.length === 0) return [];

    // Check if first line is header
    let startIndex = 0;
    const firstCol = (lines[0][0] || '').toLowerCase();
    if (firstCol === 'name' || firstCol === 'product name' || firstCol === 'পণ্য' || firstCol === 'পণ্যের নাম') {
        startIndex = 1;
    }

    const products = [];
    for (let i = startIndex; i < lines.length; i++) {
        const d = lines[i];
        const name = (d[0] || '').trim();
        if (!name) continue; // Skip rows without name

        products.push({
            name: name,
            sku: (d[1] || '').trim(),
            description: (d[2] || '').trim(),
            buy_price: parseFloat(d[3]) || 0,
            regular_price: d[4] ? parseFloat(d[4]) : '',
            sell_price: parseFloat(d[5]) || 0,
            stock_qty: d[6] !== undefined && d[6] !== '' ? parseFloat(d[6]) : 0,
            category_path: (d[7] || '').trim(),
            brand_name: (d[8] || '').trim(),
            vendor_name: (d[9] || '').trim(),
            unit_type: (d[10] || 'piece').trim(),
            base_unit: (d[11] || 'pcs').trim(),
            purchase_unit: (d[12] || '').trim(),
            purchase_unit_qty: parseFloat(d[13]) || 1.000,
            selling_unit: (d[14] || d[11] || 'pcs').trim()
        });
    }

    return products;
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// Start Chunked Batch Import
startBtn.addEventListener('click', async () => {
    if (parsedProducts.length === 0) return;

    const duplicateAction = document.querySelector('input[name="duplicate_action"]:checked')?.value || 'skip';
    
    uploadSection.classList.add('hidden');
    progressSection.classList.remove('hidden');

    let totalItems = parsedProducts.length;
    let processedCount = 0;
    let totalAdded = 0;
    let totalUpdated = 0;
    let totalSkipped = 0;
    let totalErrors = 0;
    let accumulatedErrors = [];

    const totalChunks = Math.ceil(totalItems / CHUNK_SIZE);

    for (let chunkIdx = 0; chunkIdx < totalChunks; chunkIdx++) {
        const start = chunkIdx * CHUNK_SIZE;
        const end = Math.min(start + CHUNK_SIZE, totalItems);
        const chunk = parsedProducts.slice(start, end);

        progressStatusText.textContent = `ব্যাচ ${chunkIdx + 1}/${totalChunks} প্রসেস হচ্ছে (${start + 1} - ${end} নং পণ্য)...`;

        try {
            const response = await fetch('/sodai-dorkar/public/admin/products/bulk-chunk-import', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    items: chunk,
                    duplicate_action: duplicateAction
                })
            });

            const result = await response.json();

            if (result.success) {
                totalAdded += (result.added || 0);
                totalUpdated += (result.updated || 0);
                totalSkipped += (result.skipped || 0);
                totalErrors += (result.errors || 0);

                if (result.error_messages && result.error_messages.length > 0) {
                    accumulatedErrors.push(...result.error_messages);
                }
            } else {
                totalErrors += chunk.length;
                accumulatedErrors.push(`ব্যাচ ${chunkIdx + 1} ব্যর্থ: ` + (result.message || 'অজানা ত্রুটি'));
            }
        } catch (err) {
            totalErrors += chunk.length;
            accumulatedErrors.push(`ব্যাচ ${chunkIdx + 1} নেটওয়ার্ক ত্রুটি: ` + err.message);
        }

        processedCount = end;

        // Update Progress Bar & Counters in Real-Time
        const percent = Math.min(Math.round((processedCount / totalItems) * 100), 100);
        progressBarFill.style.width = percent + '%';
        progressPercentage.textContent = percent + '%';

        counterProcessed.textContent = processedCount;
        counterAdded.textContent = totalAdded;
        counterUpdated.textContent = totalUpdated;
        counterSkipped.textContent = totalSkipped;

        // Small breather between chunks to keep browser UI butter smooth
        await new Promise(r => setTimeout(r, 60));
    }

    // Finished! Reveal Completion Section
    progressSection.classList.add('hidden');
    completionSection.classList.remove('hidden');

    document.getElementById('finalTotal').textContent = totalItems;
    document.getElementById('finalAdded').textContent = totalAdded;
    document.getElementById('finalUpdated').textContent = totalUpdated;
    document.getElementById('finalSkipped').textContent = totalSkipped;

    if (accumulatedErrors.length > 0) {
        document.getElementById('finalErrorsCount').textContent = totalErrors;
        const errList = document.getElementById('errorList');
        errList.innerHTML = '';
        accumulatedErrors.forEach(msg => {
            const li = document.createElement('li');
            li.textContent = msg;
            errList.appendChild(li);
        });
        document.getElementById('errorReportContainer').classList.remove('hidden');
    }
});

// Reset Button Handler
document.getElementById('resetImportBtn').addEventListener('click', () => {
    completionSection.classList.add('hidden');
    uploadSection.classList.remove('hidden');
    resetFileSelection();
});
</script>
