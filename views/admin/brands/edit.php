<div class="max-w-xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-secondary-800">Edit Brand</h2>
        <a href="/sodai-dorkar/public/admin/brands" class="text-secondary-600 hover:text-primary-600 flex items-center transition-colors">
            <ion-icon name="arrow-back-outline" class="mr-2 text-xl"></ion-icon>
            Back to List
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-secondary-100 p-8">
        <form action="/sodai-dorkar/public/admin/brands/update" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
            <input type="hidden" name="id" value="<?php echo $brand['id']; ?>">
            
            <div class="mb-6">
                <label class="block text-secondary-600 text-sm font-medium mb-2" for="name">Brand Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($brand['name']); ?>" class="w-full px-4 py-3 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" required>
            </div>
            
            <div class="mb-6">
                <label class="block text-secondary-600 text-sm font-medium mb-2" for="country">Country of Origin</label>
                <input type="text" id="country" name="country" value="<?php echo htmlspecialchars($brand['country']); ?>" class="w-full px-4 py-3 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>

             <div class="mb-8">
                <label class="block text-secondary-600 text-sm font-medium mb-2" for="image">Brand Logo</label>
                <?php if($brand['image_path']): ?>
                    <div class="mb-3">
                        <img src="<?php echo htmlspecialchars($brand['image_path']); ?>" alt="Current Logo" class="h-24 w-24 object-contain rounded-lg border border-secondary-200 bg-secondary-50 p-2">
                    </div>
                <?php endif; ?>
                <input type="file" id="image" name="image" accept="image/*" class="w-full text-sm text-secondary-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
            </div>

            <div class="flex items-center justify-end space-x-4">
                <a href="/sodai-dorkar/public/admin/brands" class="px-6 py-2.5 rounded-lg border border-secondary-300 text-secondary-600 hover:bg-secondary-50 font-medium transition-colors">
                    Cancel
                </a>
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2.5 px-6 rounded-lg transition-colors shadow-lg shadow-primary-200">
                    Update Brand
                </button>
            </div>
        </form>
    </div>
</div>
