<?php
// Reusable Web & Google Image Finder Modal
$modalBase = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
?>
<!-- Web & Google Image Finder Modal -->
<div id="webImageFinderModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop with blur -->
    <div id="imageFinderBackdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity opacity-0" onclick="closeImageFinderModal()"></div>

    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
        <div id="imageFinderPanel" class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-4xl border border-slate-200/80 scale-95 opacity-0 flex flex-col max-h-[90vh]">
            
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 text-white">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center text-emerald-400 text-xl flex-shrink-0">
                        <ion-icon name="sparkles"></ion-icon>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-white flex items-center gap-2">
                            <span>ওয়েব ও গুগল ইমেজ ফাইন্ডার</span>
                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">Auto Match</span>
                        </h3>
                        <p id="imageFinderProductName" class="text-xs text-slate-300 line-clamp-1 mt-0.5">পণ্যের নাম লোড হচ্ছে...</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a id="imageFinderGoogleLink" href="https://www.google.com/search?tbm=isch" target="_blank" class="px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold flex items-center gap-1.5 transition-colors border border-white/15" title="গুগল ইমেজে আলাদা ট্যাবে সার্চ করুন">
                        <ion-icon name="logo-google" class="text-amber-400"></ion-icon>
                        <span class="hidden sm:inline">Google Images</span>
                        <ion-icon name="open-outline" class="text-xs"></ion-icon>
                    </a>
                    <button type="button" onclick="closeImageFinderModal()" class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors">
                        <ion-icon name="close-outline" class="text-xl"></ion-icon>
                    </button>
                </div>
            </div>

            <!-- Search Controls & Source Pills -->
            <div class="p-5 bg-slate-50/80 border-b border-slate-200/80 space-y-3">
                <div class="flex flex-col sm:flex-row gap-2">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <ion-icon name="search-outline" class="text-lg"></ion-icon>
                        </div>
                        <input type="text" id="imageFinderQueryInput" 
                               class="w-full pl-10 pr-24 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all shadow-2xs" 
                               placeholder="পণ্যের নাম বা কিওয়ার্ড লিখে সার্চ করুন..." 
                               onkeydown="if(event.key==='Enter') executeImageSearch()">
                        <button type="button" onclick="executeImageSearch()" id="imageFinderSearchBtn"
                                class="absolute inset-y-1 right-1 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg transition-colors flex items-center gap-1 shadow-xs">
                            <span id="imageFinderSearchText">খুঁজুন</span>
                            <ion-icon name="arrow-forward-outline"></ion-icon>
                        </button>
                    </div>

                    <!-- Direct URL Toggle -->
                    <button type="button" onclick="toggleDirectUrlBox()" class="px-3.5 py-2.5 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl border border-slate-300 transition-colors flex items-center justify-center gap-1.5 shadow-2xs flex-shrink-0">
                        <ion-icon name="link-outline" class="text-sm text-emerald-600"></ion-icon>
                        <span>সরাসরি লিঙ্ক পেস্ট</span>
                    </button>
                </div>

                <!-- Direct URL Input Panel (Collapsible) -->
                <div id="directUrlPanel" class="hidden p-3 bg-white rounded-xl border border-emerald-200/80 shadow-xs flex flex-col sm:flex-row items-center gap-2">
                    <input type="url" id="directImageUrlInput" 
                           placeholder="গুগল বা যেকোনো সাইটের ইমেজের সরাসরি লিঙ্ক (URL) এখানে পেস্ট করুন..."
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                    <button type="button" onclick="saveDirectImageUrl()" id="directUrlSaveBtn"
                            class="w-full sm:w-auto px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition-all flex items-center justify-center gap-1 flex-shrink-0">
                        <ion-icon name="cloud-download-outline"></ion-icon>
                        <span>সেট করুন</span>
                    </button>
                </div>

                <!-- Source Filter Pills -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
                    <span class="text-slate-500 font-semibold text-[11px] uppercase tracking-wider flex-shrink-0">উৎস:</span>
                    <button type="button" onclick="setImageSourceFilter('all', this)" class="image-source-pill px-3 py-1 rounded-full bg-emerald-600 text-white font-bold border border-emerald-600 transition-all flex items-center gap-1">
                        <span>🌟 সকল উৎস</span>
                    </button>
                    <button type="button" onclick="setImageSourceFilter('shwapno', this)" class="image-source-pill px-3 py-1 rounded-full bg-white text-slate-600 font-semibold border border-slate-200 hover:bg-slate-100 transition-all flex items-center gap-1">
                        <span>🛒 Shwapno (স্বপ্ন)</span>
                    </button>
                    <button type="button" onclick="setImageSourceFilter('wikimedia', this)" class="image-source-pill px-3 py-1 rounded-full bg-white text-slate-600 font-semibold border border-slate-200 hover:bg-slate-100 transition-all flex items-center gap-1">
                        <span>🌿 Wikimedia (মুক্ত ছবি)</span>
                    </button>
                    <button type="button" onclick="setImageSourceFilter('openfoodfacts', this)" class="image-source-pill px-3 py-1 rounded-full bg-white text-slate-600 font-semibold border border-slate-200 hover:bg-slate-100 transition-all flex items-center gap-1">
                        <span>📦 OpenFoodFacts</span>
                    </button>
                </div>
            </div>

            <!-- Results Section (Scrollable) -->
            <div class="p-6 overflow-y-auto flex-1 min-h-[320px] bg-slate-100/50">
                <!-- Status/Toast Banner -->
                <div id="imageFinderToast" class="hidden mb-4 p-3.5 rounded-xl text-xs font-bold transition-all"></div>

                <!-- Loading State -->
                <div id="imageFinderLoading" class="hidden py-16 text-center">
                    <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-emerald-500 border-t-transparent"></div>
                    <p class="mt-3 text-sm font-bold text-slate-700">ওয়েব ও ডাটাবেস থেকে ছবি অনুসন্ধান করা হচ্ছে...</p>
                    <p class="text-xs text-slate-400 mt-1">অনুগ্রহ করে কয়েক সেকেন্ড অপেক্ষা করুন</p>
                </div>

                <!-- Empty State -->
                <div id="imageFinderEmpty" class="hidden py-16 text-center">
                    <div class="w-16 h-16 rounded-full bg-slate-200/80 flex items-center justify-center mx-auto text-slate-400 text-3xl mb-3">
                        <ion-icon name="image-outline"></ion-icon>
                    </div>
                    <h4 class="text-sm font-bold text-slate-800">কোনো ছবি পাওয়া যায়নি</h4>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">অন্য কোনো সংক্ষিপ্ত কিওয়ার্ড বা ইংরেজি নাম লিখে পুনরায় সার্চ করুন, অথবা উপরের গুগল লিঙ্ক থেকে ছবি এনে সরাসরি লিঙ্ক পেস্ট করুন।</p>
                </div>

                <!-- Image Grid -->
                <div id="imageFinderGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    <!-- Cards will be injected by JavaScript -->
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-3.5 bg-white border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>যেকোনো ছবিতে ক্লিক করলেই তা স্বয়ংক্রিয়ভাবে ডাউনলোড হয়ে ডিফল্ট হিসেবে সেট হবে।</span>
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <button type="button" onclick="closeImageFinderModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition-colors">
                        বন্ধ করুন (Close)
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
let currentImageFinderProductId = null;
let currentImageFinderSource = 'all';
let currentImageFinderCsrf = '<?= \Core\CSRF::token() ?>';
let imageFinderBaseUrl = '<?= $modalBase ?>';

function openProductImageFinderModal(productId, productName, currentImgUrl = '') {
    currentImageFinderProductId = productId;
    
    // Set UI details
    document.getElementById('imageFinderProductName').textContent = productName || ('Product #' + productId);
    document.getElementById('imageFinderQueryInput').value = productName || '';
    
    // Set Google Search Link
    const googleQuery = encodeURIComponent(productName || '');
    document.getElementById('imageFinderGoogleLink').href = `https://www.google.com/search?tbm=isch&q=${googleQuery}`;
    
    // Reset Direct URL Box
    document.getElementById('directUrlPanel').classList.add('hidden');
    document.getElementById('directImageUrlInput').value = '';
    
    // Show Modal
    const modal = document.getElementById('webImageFinderModal');
    const backdrop = document.getElementById('imageFinderBackdrop');
    const panel = document.getElementById('imageFinderPanel');
    
    modal.classList.remove('hidden');
    setTimeout(() => {
        backdrop.classList.remove('opacity-0');
        panel.classList.remove('scale-95', 'opacity-0');
        panel.classList.add('scale-100', 'opacity-100');
    }, 10);

    // Auto trigger initial search
    executeImageSearch();
}

function closeImageFinderModal() {
    const modal = document.getElementById('webImageFinderModal');
    const backdrop = document.getElementById('imageFinderBackdrop');
    const panel = document.getElementById('imageFinderPanel');
    
    backdrop.classList.add('opacity-0');
    panel.classList.remove('scale-100', 'opacity-100');
    panel.classList.add('scale-95', 'opacity-0');
    
    setTimeout(() => {
        modal.classList.add('hidden');
        document.getElementById('imageFinderToast').classList.add('hidden');
    }, 200);
}

function toggleDirectUrlBox() {
    const panel = document.getElementById('directUrlPanel');
    panel.classList.toggle('hidden');
    if (!panel.classList.contains('hidden')) {
        document.getElementById('directImageUrlInput').focus();
    }
}

function setImageSourceFilter(source, btn) {
    currentImageFinderSource = source;
    document.querySelectorAll('.image-source-pill').forEach(b => {
        b.className = 'image-source-pill px-3 py-1 rounded-full bg-white text-slate-600 font-semibold border border-slate-200 hover:bg-slate-100 transition-all flex items-center gap-1';
    });
    btn.className = 'image-source-pill px-3 py-1 rounded-full bg-emerald-600 text-white font-bold border border-emerald-600 transition-all flex items-center gap-1';
    executeImageSearch();
}

function showImageFinderToast(msg, isSuccess = true) {
    const toast = document.getElementById('imageFinderToast');
    toast.className = isSuccess 
        ? 'mb-4 p-3.5 rounded-xl text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 flex items-center gap-2'
        : 'mb-4 p-3.5 rounded-xl text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200 flex items-center gap-2';
    toast.innerHTML = `<ion-icon name="${isSuccess ? 'checkmark-circle' : 'alert-circle'}" class="text-base"></ion-icon><span>${msg}</span>`;
    toast.classList.remove('hidden');
}

async function executeImageSearch() {
    const query = document.getElementById('imageFinderQueryInput').value.trim();
    if (!query) return;

    const grid = document.getElementById('imageFinderGrid');
    const loading = document.getElementById('imageFinderLoading');
    const empty = document.getElementById('imageFinderEmpty');
    const toast = document.getElementById('imageFinderToast');

    grid.innerHTML = '';
    toast.classList.add('hidden');
    empty.classList.add('hidden');
    loading.classList.remove('hidden');

    try {
        const url = `${imageFinderBaseUrl}/admin/products/search-web-images?query=${encodeURIComponent(query)}&source=${encodeURIComponent(currentImageFinderSource)}`;
        const res = await fetch(url);
        const data = await res.json();

        loading.classList.add('hidden');

        if (!data.success || !data.results || data.results.length === 0) {
            empty.classList.remove('hidden');
            return;
        }

        renderImageCards(data.results);
    } catch (err) {
        loading.classList.add('hidden');
        showImageFinderToast('ইমেজ খুঁজতে সমস্যা হয়েছে: ' + err.message, false);
    }
}

function renderImageCards(images) {
    const grid = document.getElementById('imageFinderGrid');
    grid.innerHTML = '';

    images.forEach((item, index) => {
        const card = document.createElement('div');
        card.className = 'group relative bg-white rounded-2xl border border-slate-200 hover:border-emerald-500 overflow-hidden shadow-xs hover:shadow-md transition-all cursor-pointer flex flex-col';
        card.id = `img-card-${index}`;

        const sourceLabel = item.source || 'Web';
        let badgeColor = 'bg-slate-700 text-white';
        if (sourceLabel.includes('Shwapno')) badgeColor = 'bg-emerald-600 text-white';
        else if (sourceLabel.includes('Wikimedia')) badgeColor = 'bg-blue-600 text-white';
        else if (sourceLabel.includes('OpenFoodFacts') || sourceLabel.includes('Grocery DB')) badgeColor = 'bg-amber-600 text-white';

        card.innerHTML = `
            <div class="relative w-full aspect-square bg-slate-50 flex items-center justify-center p-3 overflow-hidden">
                <img src="${item.thumbnail || item.image}" 
                     alt="${item.title || ''}" 
                     class="max-w-full max-h-full object-contain transition-transform duration-300 group-hover:scale-105"
                     loading="lazy"
                     onerror="this.src='${imageFinderBaseUrl}/images/default-product.svg'">
                
                <span class="absolute top-2 left-2 text-[10px] font-bold px-2 py-0.5 rounded-md ${badgeColor} shadow-xs">
                    ${sourceLabel}
                </span>

                <!-- Hover Selection Overlay -->
                <div class="absolute inset-0 bg-emerald-900/75 opacity-0 group-hover:opacity-100 flex flex-col items-center justify-center text-white transition-opacity p-3 text-center">
                    <div class="w-10 h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xl mb-1 shadow-md transform scale-75 group-hover:scale-100 transition-transform">
                        <ion-icon name="checkmark-outline"></ion-icon>
                    </div>
                    <span class="text-xs font-black tracking-wide">ডিফল্ট হিসেবে সেট করুন</span>
                    <span class="text-[10px] text-emerald-200 mt-0.5">Click to Save</span>
                </div>

                <!-- Saving Spinner Overlay -->
                <div id="card-spinner-${index}" class="absolute inset-0 bg-white/90 backdrop-blur-2xs hidden flex-col items-center justify-center text-emerald-700">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-3 border-emerald-600 border-t-transparent mb-1"></div>
                    <span class="text-[11px] font-bold">ডাউনলোড হচ্ছে...</span>
                </div>
            </div>

            <div class="p-2.5 bg-white flex flex-col justify-between flex-1 border-t border-slate-100">
                <p class="text-xs font-semibold text-slate-800 line-clamp-1" title="${item.title || ''}">${item.title || 'পণ্য ছবি'}</p>
                <div class="flex items-center justify-between mt-1 text-[10px] text-slate-400">
                    <span>${item.price || ''}</span>
                    <span class="text-emerald-600 font-bold group-hover:underline">সেট করুন ›</span>
                </div>
            </div>
        `;

        card.onclick = () => selectAndSaveImage(item.image, index);
        grid.appendChild(card);
    });
}

async function selectAndSaveImage(imageUrl, cardIndex) {
    if (!currentImageFinderProductId || !imageUrl) return;

    const spinner = document.getElementById(`card-spinner-${cardIndex}`);
    if (spinner) spinner.classList.remove('hidden');

    try {
        const formData = new FormData();
        formData.append('product_id', currentImageFinderProductId);
        formData.append('image_url', imageUrl);
        formData.append('csrf_token', currentImageFinderCsrf);

        const res = await fetch(`${imageFinderBaseUrl}/admin/products/save-web-image`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': currentImageFinderCsrf
            },
            body: formData
        });

        const data = await res.json();

        if (spinner) spinner.classList.add('hidden');

        if (data.success && data.image_path) {
            showImageFinderToast('🎉 পণ্যের ছবি সফলভাবে ডাউনলোড ও ডিফল্ট হিসেবে সেট করা হয়েছে!', true);
            
            // 1. Update Thumbnail in product list table if it exists on page
            const tableThumb = document.getElementById(`prod-thumb-${currentImageFinderProductId}`);
            if (tableThumb) {
                tableThumb.src = data.image_path + '?v=' + Date.now();
            }

            // 2. Update preview on edit page if it exists
            const editPreview = document.getElementById('previewImage') || document.getElementById('currentImageDisplay');
            if (editPreview) {
                editPreview.src = data.image_path + '?v=' + Date.now();
                editPreview.classList.remove('hidden');
            }

            // 3. Mark the card as active
            const card = document.getElementById(`img-card-${cardIndex}`);
            if (card) {
                card.classList.add('ring-3', 'ring-emerald-500', 'border-emerald-500');
            }

            setTimeout(() => {
                closeImageFinderModal();
            }, 800);
        } else {
            showImageFinderToast(data.message || 'ইমেজ সেভ করতে ব্যর্থ হয়েছে!', false);
        }
    } catch (err) {
        if (spinner) spinner.classList.add('hidden');
        showImageFinderToast('সার্ভার এরর: ' + err.message, false);
    }
}

async function saveDirectImageUrl() {
    const url = document.getElementById('directImageUrlInput').value.trim();
    if (!url) {
        alert('অনুগ্রহ করে একটি সঠিক ইমেজ লিঙ্ক পেস্ট করুন!');
        return;
    }

    const saveBtn = document.getElementById('directUrlSaveBtn');
    const oldBtnText = saveBtn.innerHTML;
    saveBtn.disabled = true;
    saveBtn.innerHTML = `<span class="inline-block animate-spin rounded-full h-3.5 w-3.5 border-2 border-white border-t-transparent"></span> সেভ হচ্ছে...`;

    try {
        const formData = new FormData();
        formData.append('product_id', currentImageFinderProductId);
        formData.append('image_url', url);
        formData.append('csrf_token', currentImageFinderCsrf);

        const res = await fetch(`${imageFinderBaseUrl}/admin/products/save-web-image`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': currentImageFinderCsrf
            },
            body: formData
        });

        const data = await res.json();
        saveBtn.disabled = false;
        saveBtn.innerHTML = oldBtnText;

        if (data.success && data.image_path) {
            showImageFinderToast('🎉 ডাইরেক্ট লিঙ্ক থেকে ইমেজ সফলভাবে সেট করা হয়েছে!', true);
            
            const tableThumb = document.getElementById(`prod-thumb-${currentImageFinderProductId}`);
            if (tableThumb) {
                tableThumb.src = data.image_path + '?v=' + Date.now();
            }

            const editPreview = document.getElementById('previewImage') || document.getElementById('currentImageDisplay');
            if (editPreview) {
                editPreview.src = data.image_path + '?v=' + Date.now();
                editPreview.classList.remove('hidden');
            }

            setTimeout(() => {
                closeImageFinderModal();
            }, 800);
        } else {
            showImageFinderToast(data.message || 'লিঙ্কটি থেকে ইমেজ ডাউনলোড করা যায়নি!', false);
        }
    } catch (err) {
        saveBtn.disabled = false;
        saveBtn.innerHTML = oldBtnText;
        showImageFinderToast('সার্ভার এরর: ' + err.message, false);
    }
}

// Close on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('webImageFinderModal');
        if (modal && !modal.classList.contains('hidden')) {
            closeImageFinderModal();
        }
    }
});
</script>
