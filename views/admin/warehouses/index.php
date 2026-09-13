<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Form Section -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-secondary-100 p-6">
            <h3 class="text-lg font-bold text-secondary-800 mb-4">Add New Warehouse</h3>
            <form action="/sodai-dorkar/public/admin/warehouses/store" method="POST">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                <div class="mb-4">
                    <label class="block text-secondary-600 text-sm font-medium mb-2" for="name">Warehouse Name</label>
                    <input type="text" id="name" name="name" class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" required placeholder="e.g., Central Hub">
                </div>
                <div class="mb-6">
                    <label class="block text-secondary-600 text-sm font-medium mb-2" for="location">Location</label>
                    <input type="text" id="location" name="location" class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" required placeholder="e.g., Puran Bazar, Chandpur">
                </div>
                <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-2.5 px-4 rounded-lg transition-colors">
                    Save Warehouse
                </button>
            </form>
        </div>
    </div>

    <!-- List Section -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-secondary-100 overflow-hidden">
            <div class="p-6 border-b border-secondary-100">
                <h3 class="font-bold text-secondary-800">Existing Warehouses</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-secondary-600">
                    <thead class="bg-secondary-50 text-secondary-500">
                        <tr>
                            <th class="px-6 py-3 font-medium">ID</th>
                            <th class="px-6 py-3 font-medium">Name</th>
                            <th class="px-6 py-3 font-medium">Location</th>
                            <th class="px-6 py-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-secondary-100">
                        <?php if (empty($warehouses)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-secondary-400">
                                    No warehouses found.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($warehouses as $wh): ?>
                                <tr class="hover:bg-secondary-50">
                                    <td class="px-6 py-4">#<?php echo $wh['id']; ?></td>
                                    <td class="px-6 py-4 font-medium text-secondary-900"><?php echo htmlspecialchars($wh['name']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($wh['location']); ?></td>
                                    <td class="px-6 py-4 text-right">
                                        <form action="/sodai-dorkar/public/admin/warehouses/delete" method="POST" onsubmit="return confirm('Are you sure?');" class="inline">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                                            <input type="hidden" name="id" value="<?php echo $wh['id']; ?>">
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
