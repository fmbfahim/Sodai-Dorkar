<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-secondary-100 p-8">
        <div class="flex items-center justify-between mb-6 border-b border-secondary-100 pb-4">
            <h3 class="text-xl font-bold text-secondary-800">Edit Category</h3>
            <a href="/sodai-dorkar/public/admin/categories" class="text-secondary-500 hover:text-secondary-700">
                <ion-icon name="close-outline" class="text-2xl"></ion-icon>
            </a>
        </div>
        
        <form action="/sodai-dorkar/public/admin/categories/update" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
            <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
            
            <div class="mb-6">
                <label class="block text-secondary-600 text-sm font-medium mb-2" for="name">Category Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($category['name']); ?>" class="w-full px-4 py-3 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" required>
            </div>
            
             <div class="mb-6">
                <label class="block text-secondary-600 text-sm font-medium mb-2" for="image">Category Image</label>
                <?php if($category['image_path']): ?>
                    <div class="mb-2">
                        <img src="<?php echo htmlspecialchars($category['image_path']); ?>" alt="Current Image" class="h-24 w-24 object-cover rounded-lg border border-secondary-200">
                    </div>
                <?php endif; ?>
                <input type="file" id="image" name="image" accept="image/*" class="w-full text-sm text-secondary-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
            </div>

            <div class="mb-6">
                <label class="block text-secondary-600 text-sm font-medium mb-2" for="parent_id">Parent Category</label>
                <div class="relative">
                    <select id="parent_id" name="parent_id" class="w-full px-4 py-3 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 appearance-none bg-white">
                        <option value="">None (Top Level)</option>
                        <?php foreach ($categories as $cat): 
                            if ($cat['id'] == $category['id']) continue; // Prevent self-parenting
                            $isSelected = ($cat['id'] == $category['parent_id']) ? 'selected' : '';
                        ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo $isSelected; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                                <?php if($cat['parent_name']) echo ' ( < ' . htmlspecialchars($cat['parent_name']) . ' )'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-secondary-500">
                         <ion-icon name="chevron-down-outline"></ion-icon>
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <label class="block text-secondary-600 text-sm font-medium mb-2" for="description">Description</label>
                <textarea id="description" name="description" rows="4" class="w-full px-4 py-3 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"><?php echo htmlspecialchars($category['description']); ?></textarea>
            </div>

            <div class="flex justify-end gap-3">
                 <a href="/sodai-dorkar/public/admin/categories" class="px-6 py-2.5 border border-secondary-300 rounded-lg text-secondary-600 font-bold hover:bg-secondary-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-lg transition-colors shadow-sm">
                    Update Category
                </button>
            </div>
        </form>
    </div>
</div>
