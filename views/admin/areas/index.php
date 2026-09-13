<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Form Section -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-secondary-100 p-6">
            <h3 class="text-lg font-bold text-secondary-800 mb-4">Add New Area</h3>
            <form action="/sodai-dorkar/public/admin/areas/store" method="POST">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                <div class="mb-4">
                    <label class="block text-secondary-600 text-sm font-medium mb-2" for="name">Area Name</label>
                    <input type="text" id="name" name="name" class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" required placeholder="e.g., Notun Bazar">
                </div>
                <div class="mb-6">
                    <label class="block text-secondary-600 text-sm font-medium mb-2" for="warehouse_id">Assigned Warehouse</label>
                    <div class="relative">
                        <select id="warehouse_id" name="warehouse_id" class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 appearance-none bg-white" required>
                            <option value="" disabled selected>Select Warehouse</option>
                            <?php foreach ($warehouses as $wh): ?>
                                <option value="<?php echo $wh['id']; ?>"><?php echo htmlspecialchars($wh['name']); ?> (<?php echo htmlspecialchars($wh['location']); ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-secondary-700">
                            <ion-icon name="chevron-down-outline"></ion-icon>
                        </div>
                    </div>
                </div>
                <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-2.5 px-4 rounded-lg transition-colors">
                    Save Area
                </button>
            </form>
        </div>
    </div>

    <!-- List Section -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-secondary-100 overflow-hidden">
            <div class="p-6 border-b border-secondary-100">
                <h3 class="font-bold text-secondary-800">Existing Areas</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-secondary-600">
                    <thead class="bg-secondary-50 text-secondary-500">
                        <tr>
                            <th class="px-6 py-3 font-medium">ID</th>
                            <th class="px-6 py-3 font-medium">Area Name</th>
                            <th class="px-6 py-3 font-medium">Warehouse</th>
                            <th class="px-6 py-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-secondary-100">
                        <?php if (empty($areas)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-secondary-400">
                                    No areas found.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($areas as $area): ?>
                                <tr class="hover:bg-secondary-50">
                                    <td class="px-6 py-4">#<?php echo $area['id']; ?></td>
                                    <td class="px-6 py-4 font-medium text-secondary-900"><?php echo htmlspecialchars($area['name']); ?></td>
                                    <td class="px-6 py-4">
                                        <?php if($area['warehouse_name']): ?>
                                            <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded text-xs font-medium">
                                                <?php echo htmlspecialchars($area['warehouse_name']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-secondary-400 italic">Unassigned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="/sodai-dorkar/public/admin/areas/edit?id=<?php echo $area['id']; ?>" class="text-blue-500 hover:text-blue-700 p-1 mr-2 inline-block">
                                            <ion-icon name="create-outline" class="text-lg"></ion-icon>
                                        </a>
                                        <form action="/sodai-dorkar/public/admin/areas/delete" method="POST" onsubmit="return confirm('Are you sure?');" class="inline">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                                            <input type="hidden" name="id" value="<?php echo $area['id']; ?>">
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
