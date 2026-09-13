<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Form Section -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-secondary-100 p-6">
            <h3 class="text-lg font-bold text-secondary-800 mb-4">Assign Area & Slot</h3>
            <form action="/sodai-dorkar/public/admin/delivery-men/allocation/store" method="POST">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                <div class="mb-4">
                    <label class="block text-secondary-600 text-sm font-medium mb-2" for="dm">Select Delivery Man</label>
                    <div class="relative">
                        <select id="dm" name="user_id" class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 appearance-none bg-white" required>
                            <option value="">Choose...</option>
                            <?php foreach ($deliveryMen as $dm): ?>
                                <option value="<?php echo $dm['id']; ?>"><?php echo htmlspecialchars($dm['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-secondary-500">
                             <ion-icon name="chevron-down-outline"></ion-icon>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-secondary-600 text-sm font-medium mb-2" for="area">Select Union (Area)</label>
                    <div class="relative">
                        <select id="area" name="area_id" class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 appearance-none bg-white" required>
                            <option value="">Choose...</option>
                            <?php foreach ($areas as $area): ?>
                                <option value="<?php echo $area['id']; ?>"><?php echo htmlspecialchars($area['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-secondary-500">
                             <ion-icon name="chevron-down-outline"></ion-icon>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-secondary-600 text-sm font-medium mb-2" for="slot">Time Slot</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" name="time_slot" value="morning" class="peer sr-only">
                            <div class="px-3 py-2 rounded-lg border border-secondary-200 text-center text-sm font-medium peer-checked:bg-primary-100 peer-checked:text-primary-700 peer-checked:border-primary-500 transition-all">
                                Morning
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="time_slot" value="afternoon" class="peer sr-only">
                            <div class="px-3 py-2 rounded-lg border border-secondary-200 text-center text-sm font-medium peer-checked:bg-primary-100 peer-checked:text-primary-700 peer-checked:border-primary-500 transition-all">
                                Afternoon
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="time_slot" value="both" class="peer sr-only">
                            <div class="px-3 py-2 rounded-lg border border-secondary-200 text-center text-sm font-medium peer-checked:bg-primary-100 peer-checked:text-primary-700 peer-checked:border-primary-500 transition-all">
                                Both
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="time_slot" value="all_time" class="peer sr-only" checked>
                            <div class="px-3 py-2 rounded-lg border border-secondary-200 text-center text-sm font-medium peer-checked:bg-primary-100 peer-checked:text-primary-700 peer-checked:border-primary-500 transition-all">
                                All Time
                            </div>
                        </label>
                    </div>
                </div>

                <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-2.5 px-4 rounded-lg transition-colors">
                    Assign Allocation
                </button>
            </form>
        </div>
    </div>

    <!-- List Section -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-secondary-100 overflow-hidden">
            <div class="p-6 border-b border-secondary-100">
                <h3 class="font-bold text-secondary-800">Current Allocations</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-secondary-600">
                    <thead class="bg-secondary-50 text-secondary-500">
                        <tr>
                            <th class="px-6 py-3 font-medium">Delivery Man</th>
                            <th class="px-6 py-3 font-medium">Union (Area)</th>
                             <th class="px-6 py-3 font-medium">Time Slot</th>
                            <th class="px-6 py-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-secondary-100">
                        <?php if (empty($allocations)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-secondary-400">
                                    No allocations found.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($allocations as $a): ?>
                                <tr class="hover:bg-secondary-50">
                                    <td class="px-6 py-4 font-bold text-secondary-900"><?php echo htmlspecialchars($a['dm_name']); ?></td>
                                    <td class="px-6 py-4">
                                        <?php echo htmlspecialchars($a['area_name']); ?>
                                    </td>
                                     <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 capitalize">
                                            <?php echo str_replace('_', ' ', $a['time_slot']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="/sodai-dorkar/public/admin/delivery-men/allocation/edit?id=<?php echo $a['id']; ?>" class="text-blue-500 hover:text-blue-700 p-1 mr-2 inline-block">
                                            <ion-icon name="create-outline" class="text-lg"></ion-icon>
                                        </a>
                                        <form action="/sodai-dorkar/public/admin/delivery-men/allocation/delete" method="POST" onsubmit="return confirm('Remove this allocation?');" class="inline">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                                            <input type="hidden" name="id" value="<?php echo $a['id']; ?>">
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
