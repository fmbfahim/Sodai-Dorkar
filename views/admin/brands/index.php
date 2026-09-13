<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Form Section -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-secondary-100 p-6">
            <h3 class="text-lg font-bold text-secondary-800 mb-4">Add Brand</h3>
            <form action="/sodai-dorkar/public/admin/brands/store" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                <div class="mb-4">
                    <label class="block text-secondary-600 text-sm font-medium mb-2" for="name">Brand Name</label>
                    <input type="text" id="name" name="name" class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" required placeholder="e.g. Samsung">
                </div>
                 <div class="mb-4">
                    <label class="block text-secondary-600 text-sm font-medium mb-2" for="country">Country of Origin (Optional)</label>
                    <input type="text" id="country" name="country" class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="e.g. South Korea">
                </div>
                <div class="mb-6">
                    <label class="block text-secondary-600 text-sm font-medium mb-2" for="image">Logo (Optional)</label>
                    <input type="file" id="image" name="image" accept="image/*" class="w-full text-sm text-secondary-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                </div>
                <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-2.5 px-4 rounded-lg transition-colors">
                    Add Brand
                </button>
            </form>
        </div>
    </div>

    <!-- List Section -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-secondary-100 overflow-hidden">
            <div class="p-6 border-b border-secondary-100">
                <h3 class="font-bold text-secondary-800">Brand List</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-secondary-600">
                    <thead class="bg-secondary-50 text-secondary-500">
                        <tr>
                            <th class="px-6 py-3 font-medium">Logo</th>
                            <th class="px-6 py-3 font-medium">Name</th>
                            <th class="px-6 py-3 font-medium">Country</th>
                            <th class="px-6 py-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-secondary-100">
                        <?php if (empty($brands)): ?>
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-secondary-400">
                                    No brands found.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($brands as $brand): ?>
                                <tr class="hover:bg-secondary-50">
                                    <td class="px-6 py-4">
                                        <?php if($brand['image_path']): ?>
                                            <img src="<?php echo htmlspecialchars($brand['image_path']); ?>" alt="Logo" class="h-8 w-8 object-contain rounded-full border border-secondary-200 bg-white">
                                        <?php else: ?>
                                            <div class="h-8 w-8 rounded-full bg-secondary-100 flex items-center justify-center text-secondary-400 text-xs">N/A</div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 font-bold text-secondary-900"><?php echo htmlspecialchars($brand['name']); ?></td>
                                    <td class="px-6 py-4 text-secondary-500">
                                        <?php echo $brand['country'] ? htmlspecialchars($brand['country']) : '-'; ?>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="/sodai-dorkar/public/admin/brands/edit?id=<?php echo $brand['id']; ?>" class="text-blue-500 hover:text-blue-700 p-1 mr-2 inline-block">
                                            <ion-icon name="create-outline" class="text-lg"></ion-icon>
                                        </a>
                                        <form action="/sodai-dorkar/public/admin/brands/delete" method="POST" onsubmit="return confirm('Delete this brand?');" class="inline">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                                            <input type="hidden" name="id" value="<?php echo $brand['id']; ?>">
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
