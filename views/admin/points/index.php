<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Form Section -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-secondary-100 p-6">
            <h3 class="text-lg font-bold text-secondary-800 mb-4">Add Point (House)</h3>
            <form action="/sodai-dorkar/public/admin/points/store" method="POST">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                <div class="mb-4">
                    <label class="block text-secondary-600 text-sm font-medium mb-2" for="zone">Select Zone (Ward)</label>
                    <div class="relative">
                        <select id="zone" name="zone_id" class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 appearance-none bg-white">
                            <?php foreach ($zones as $zone): ?>
                                <option value="<?php echo $zone['id']; ?>">
                                    <?php echo htmlspecialchars($zone['name']); ?> (<?php echo htmlspecialchars($zone['area_name']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-secondary-500">
                             <ion-icon name="chevron-down-outline"></ion-icon>
                        </div>
                    </div>
                </div>
                <div class="mb-6">
                    <label class="block text-secondary-600 text-sm font-medium mb-2" for="name">Point Name (House)</label>
                    <input type="text" id="name" name="name" class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" required placeholder="e.g. Sardar Bari">
                </div>
                <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-2.5 px-4 rounded-lg transition-colors">
                    Add Point
                </button>
            </form>
        </div>
    </div>

    <!-- List Section -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-secondary-100 overflow-hidden">
            <div class="p-6 border-b border-secondary-100">
                <h3 class="font-bold text-secondary-800">Point List</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-secondary-600">
                    <thead class="bg-secondary-50 text-secondary-500">
                        <tr>
                            <th class="px-6 py-3 font-medium">Point (House)</th>
                            <th class="px-6 py-3 font-medium">Zone & Union</th>
                            <th class="px-6 py-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-secondary-100">
                         <?php if (empty($points)): ?>
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-secondary-400">
                                    No points found.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($points as $point): ?>
                                <tr class="hover:bg-secondary-50">
                                    <td class="px-6 py-4 font-medium text-secondary-900"><?php echo htmlspecialchars($point['name']); ?></td>
                                    <td class="px-6 py-4">
                                        <div class="text-secondary-800"><?php echo htmlspecialchars($point['zone_name']); ?></div>
                                        <div class="text-xs text-secondary-400"><?php echo htmlspecialchars($point['area_name']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="/sodai-dorkar/public/admin/points/edit?id=<?php echo $point['id']; ?>" class="text-blue-500 hover:text-blue-700 p-1 mr-2 inline-block">
                                            <ion-icon name="create-outline" class="text-lg"></ion-icon>
                                        </a>
                                        <form action="/sodai-dorkar/public/admin/points/delete" method="POST" onsubmit="return confirm('Delete this point?');" class="inline">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                                            <input type="hidden" name="id" value="<?php echo $point['id']; ?>">
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
