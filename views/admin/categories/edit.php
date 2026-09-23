<?php
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
?>
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-xs border border-secondary-200 p-6 sm:p-8">
        <div class="flex items-center justify-between mb-6 border-b border-secondary-100 pb-4">
            <div>
                <h3 class="text-lg font-black text-secondary-900 flex items-center gap-2">
                    <ion-icon name="create-outline" class="text-emerald-600 text-xl"></ion-icon>
                    <span>ক্যাটাগরি সম্পাদনা (Edit Category)</span>
                </h3>
                <p class="text-xs text-secondary-500 mt-0.5">ক্যাটাগরির বাংলা নাম, ইংরেজি স্ল্যাগ ও ছবি আপডেট করুন।</p>
            </div>
            <a href="<?= $base ?>/admin/categories" class="p-2 rounded-xl bg-secondary-100 hover:bg-secondary-200 text-secondary-500 hover:text-secondary-800 transition-colors">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </a>
        </div>
        
        <form action="<?= $base ?>/admin/categories/update" method="POST" enctype="multipart/form-data" class="space-y-5">
            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
            <input type="hidden" name="id" value="<?= $category['id'] ?>">
            <input type="hidden" id="selected_image_url" name="selected_image_url" value="">
            
            <!-- Category Name (Bengali) -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-secondary-700 text-xs font-bold" for="name">
                        ক্যাটাগরির নাম <span class="text-rose-500">*</span>
                    </label>
                    <span class="text-[10px] text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded font-bold">শুধুমাত্র বাংলা</span>
                </div>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="<?= htmlspecialchars($category['name']) ?>" 
                       class="w-full px-4 py-2.5 text-xs border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 font-bold text-secondary-900" 
                       required>
            </div>

            <!-- Category Slug (English) -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-secondary-700 text-xs font-bold" for="slug">
                        স্ল্যাগ (Slug)
                    </label>
                    <span class="text-[10px] text-blue-700 bg-blue-50 px-2 py-0.5 rounded font-bold font-mono">শুধুমাত্র ইংরেজি</span>
                </div>
                <input type="text" 
                       id="slug" 
                       name="slug" 
                       value="<?= htmlspecialchars($category['slug'] ?? '') ?>" 
                       class="w-full px-4 py-2.5 text-xs font-mono border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-secondary-800" 
                       placeholder="যেমন: rice, cooking, baby-food">
                <div class="text-[10px] text-secondary-400 mt-1">সিস্টেম আইডেন্টিফায়ার ও URL-এর জন্য ব্যবহৃত ইউনিক ইংরেজি নাম।</div>
            </div>
            
            <!-- Category Image Section -->
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
                        <span>অনলাইনে ছবি খুঁজুন</span>
                    </button>
                </div>

                <div class="flex items-center gap-4 mb-3">
                    <div class="w-16 h-16 rounded-xl bg-white border border-secondary-200 overflow-hidden flex-shrink-0 shadow-2xs p-1">
                        <img src="<?= \Models\Category::getImageUrl($category['image_path'] ?? '', $base) ?>" 
                             alt="Current Image" 
                             class="w-full h-full object-contain"
                             onerror="this.onerror=null; this.src='<?= $base ?>/images/default-category.svg';">
                    </div>

                    <div class="flex-1">
                        <input type="file" 
                               id="image" 
                               name="image" 
                               accept="image/*" 
                               class="w-full text-xs text-secondary-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer border border-secondary-200 rounded-xl p-1 bg-secondary-50/50">
                        <div class="flex items-center justify-between mt-1.5 flex-wrap gap-1">
                            <span class="text-[10px] text-secondary-400">নতুন ছবি আপলোড করতে ব্রাউজ করুন অথবা উপরের 'অনলাইনে ছবি খুঁজুন' চাপুন।</span>
                            <?php if (!empty($category['image_path'])): ?>
                                <label class="inline-flex items-center gap-1.5 text-[11px] text-rose-600 font-bold cursor-pointer">
                                    <input type="checkbox" name="remove_image" value="1" class="rounded text-rose-600 focus:ring-rose-500 border-secondary-300">
                                    <span>ছবি মুছুন (Remove)</span>
                                </label>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Live Candidates Thumbnail Preview Box -->
                <div id="imageCandidatesContainer" class="hidden p-3 rounded-xl bg-secondary-50 border border-secondary-200 space-y-2">
                    <div class="flex items-center justify-between text-[11px] font-bold text-secondary-700">
                        <span>অনলাইনে পাওয়া ছবি (ক্লিক করে পছন্দ করুন):</span>
                        <button type="button" onclick="closeImageCandidates()" class="text-secondary-400 hover:text-rose-600">✕</button>
                    </div>
                    <div id="imageCandidatesGrid" class="grid grid-cols-4 gap-2"></div>
                </div>

                <!-- Selected Image Preview Badge -->
                <div id="selectedImagePreview" class="hidden p-2 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center gap-2.5">
                    <img id="selectedImageThumb" src="" alt="Selected Preview" class="w-10 h-10 rounded-lg object-contain bg-white border border-emerald-200">
                    <div class="min-w-0 flex-1">
                        <div class="text-[11px] font-bold text-emerald-900">নতুন ছবি নির্বাচন করা হয়েছে!</div>
                        <div class="text-[10px] text-emerald-600 truncate">আপডেট করার পর এই ছবিটি সেভ হবে</div>
                    </div>
                    <button type="button" onclick="clearSelectedImage()" class="text-xs text-rose-500 hover:text-rose-700 font-bold">বাতিল</button>
                </div>
            </div>

            <!-- Parent Category -->
            <div>
                <label class="block text-secondary-700 text-xs font-bold mb-1.5" for="parent_id">
                    প্যারেন্ট ক্যাটাগরি (Parent Category)
                </label>
                <div class="relative">
                    <select id="parent_id" name="parent_id" class="w-full px-4 py-2.5 text-xs border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 appearance-none bg-white font-medium text-secondary-800 cursor-pointer">
                        <option value="">None (মূল বিভাগ / Top Level)</option>
                        <?php foreach ($categories as $cat): 
                            if ($cat['id'] == $category['id']) continue; // Prevent self-parenting
                            $isSelected = ($cat['id'] == $category['parent_id']) ? 'selected' : '';
                        ?>
                            <option value="<?= $cat['id'] ?>" <?= $isSelected ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                                <?php if (!empty($cat['parent_name'])): ?>
                                    ( <?= htmlspecialchars($cat['parent_name']) ?> )
                                <?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-secondary-500">
                        <ion-icon name="chevron-down-outline"></ion-icon>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-secondary-700 text-xs font-bold mb-1.5" for="description">
                    বিবরণ (Description)
                </label>
                <textarea id="description" 
                          name="description" 
                          rows="3" 
                          class="w-full px-4 py-2.5 text-xs border border-secondary-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 font-medium"><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="<?= $base ?>/admin/categories" class="px-5 py-2.5 border border-secondary-300 rounded-xl text-secondary-600 font-bold text-xs hover:bg-secondary-50 transition-colors">
                    বাতিল
                </a>
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-xs rounded-xl transition-all shadow-xs flex items-center gap-1.5 cursor-pointer">
                    <ion-icon name="save-outline" class="text-sm"></ion-icon>
                    <span>আপডেট সংরক্ষণ করুন</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const BASE_URI = '<?= $base ?>';

async function triggerAutoImageCandidates() {
    const nameVal = document.getElementById('name').value.trim();
    const slugVal = document.getElementById('slug').value.trim();
    const query = slugVal || nameVal;

    if (!query) {
        alert('অনুগ্রহ করে প্রথমে ক্যাটাগরির নাম প্রদান করুন!');
        return;
    }

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
            grid.innerHTML = `<div class="col-span-4 py-3 text-center text-xs text-secondary-500">কোনো ছবি পাওয়া যায়নি।</div>`;
        }
    } catch (err) {
        console.error('Fetch image error:', err);
        grid.innerHTML = `<div class="col-span-4 py-3 text-center text-xs text-rose-500">ছবি লোড করা সম্ভব হয়নি।</div>`;
    }
}

function chooseCandidateImage(imgUrl) {
    document.getElementById('selected_image_url').value = imgUrl;
    const previewContainer = document.getElementById('selectedImagePreview');
    const previewImg = document.getElementById('selectedImageThumb');
    previewImg.src = imgUrl;
    previewContainer.classList.remove('hidden');
    closeImageCandidates();
}

function closeImageCandidates() {
    document.getElementById('imageCandidatesContainer').classList.add('hidden');
}

function clearSelectedImage() {
    document.getElementById('selected_image_url').value = '';
    document.getElementById('selectedImagePreview').classList.add('hidden');
}
</script>
