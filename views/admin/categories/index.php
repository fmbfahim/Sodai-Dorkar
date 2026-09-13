<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Form Section -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-secondary-100 p-6">
            <h3 class="text-lg font-bold text-secondary-800 mb-4">Add Category</h3>
            <form action="/sodai-dorkar/public/admin/categories/store" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                <div class="mb-4">
                    <label class="block text-secondary-600 text-sm font-medium mb-2" for="name">Category Name</label>
                    <input type="text" id="name" name="name" class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" required placeholder="e.g. Vegetables">
                </div>
                <div class="mb-4">
                    <label class="block text-secondary-600 text-sm font-medium mb-2" for="image">Image (Optional)</label>
                    <input type="file" id="image" name="image" accept="image/*" class="w-full text-sm text-secondary-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                </div>
                <div class="mb-4">
                    <label class="block text-secondary-600 text-sm font-medium mb-2" for="parent_id">Parent Category</label>
                    <div class="relative">
                        <select id="parent_id" name="parent_id" class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 appearance-none bg-white">
                            <option value="">None (Top Level)</option>
                            <?php 
                                $selected_parent = $_GET['parent_id'] ?? '';
                                // Use the sorted $categories which has depth!
                                foreach ($categories as $cat): 
                                    $isSelected = ($cat['id'] == $selected_parent) ? 'selected' : '';
                                    $indent = str_repeat('&nbsp;&nbsp;&nbsp;', $cat['depth']);
                                    $symbol = $cat['depth'] > 0 ? '↳ ' : '';
                            ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo $isSelected; ?>>
                                    <?php echo $indent . $symbol . htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-secondary-500">
                             <ion-icon name="chevron-down-outline"></ion-icon>
                        </div>
                    </div>
                </div>
                <div class="mb-6">
                    <label class="block text-secondary-600 text-sm font-medium mb-2" for="description">Description (Optional)</label>
                    <textarea id="description" name="description" rows="3" class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Short description..."></textarea>
                </div>
                <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-2.5 px-4 rounded-lg transition-colors">
                    Add Category
                </button>
            </form>
        </div>
    </div>

    <!-- List Section -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-secondary-100 overflow-hidden">
            <div class="p-6 border-b border-secondary-100">
                <h3 class="font-bold text-secondary-800">Existing Categories</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-secondary-600">
                    <thead class="bg-secondary-50 text-secondary-500">
                        <tr>
                            <th class="px-6 py-3 font-medium">Image</th>
                            <th class="px-6 py-3 font-medium">Name</th>
                            <th class="px-6 py-3 font-medium">Parent</th>
                            <th class="px-6 py-3 font-medium">Description</th>
                            <th class="px-6 py-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-secondary-100">
                        <?php if (empty($categories)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-secondary-400">
                                    No categories found.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($categories as $cat): ?>
                                <tr class="hover:bg-secondary-50">
                                     <td class="px-6 py-4">
                                        <?php if($cat['image_path']): ?>
                                            <img src="<?php echo htmlspecialchars($cat['image_path']); ?>" alt="Img" class="h-10 w-10 object-cover rounded-lg border border-secondary-200">
                                        <?php else: ?>
                                            <div class="h-10 w-10 rounded-lg bg-secondary-100 flex items-center justify-center text-secondary-400">
                                                <ion-icon name="image-outline" class="text-xl"></ion-icon>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 font-bold text-secondary-900">
                                        <div style="padding-left: <?php echo $cat['depth'] * 20; ?>px;" class="flex items-center">
                                            <?php if($cat['depth'] > 0): ?>
                                                <span class="text-secondary-400 mr-2">↳</span>
                                            <?php endif; ?>
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-secondary-500">
                                        <?php echo $cat['parent_name'] ? htmlspecialchars($cat['parent_name']) : '-'; ?>
                                    </td>
                                    <td class="px-6 py-4 text-secondary-500 truncate max-w-xs">
                                        <?php echo htmlspecialchars($cat['description']); ?>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="/sodai-dorkar/public/admin/categories/edit?id=<?php echo $cat['id']; ?>" class="text-blue-500 hover:text-blue-700 p-1 mr-2 inline-block">
                                            <ion-icon name="create-outline" class="text-lg"></ion-icon>
                                        </a>
                                        <form action="/sodai-dorkar/public/admin/categories/delete" method="POST" onsubmit="return confirm('Delete this category?');" class="inline">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                                            <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                                            <button type="submit" class="text-red-500 hover:text-red-700 p-1">
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
    </div>
</div>
