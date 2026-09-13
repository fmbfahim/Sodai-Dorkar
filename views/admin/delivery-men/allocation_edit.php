<div class="max-w-xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-secondary-800">Edit Allocation</h2>
        <a href="/sodai-dorkar/public/admin/delivery-men/allocation" class="text-secondary-600 hover:text-primary-600 flex items-center transition-colors">
            <ion-icon name="arrow-back-outline" class="mr-2 text-xl"></ion-icon>
            Back to List
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-secondary-100 p-8">
        <form action="/sodai-dorkar/public/admin/delivery-men/allocation/update" method="POST">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
            <input type="hidden" name="id" value="<?php echo $allocation['id']; ?>">
            
            <div class="mb-6">
                <label class="block text-secondary-600 text-sm font-medium mb-2" for="dm">Delivery Man</label>
                <div class="relative">
                    <select id="dm" name="user_id" class="w-full px-4 py-3 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 appearance-none bg-white" required>
                        <option value="">Choose...</option>
                        <?php foreach ($deliveryMen as $dm): 
                            $selected = ($dm['id'] == $allocation['user_id']) ? 'selected' : '';
                        ?>
                            <option value="<?php echo $dm['id']; ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($dm['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-secondary-500">
                         <ion-icon name="chevron-down-outline"></ion-icon>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-secondary-600 text-sm font-medium mb-2" for="area">Union (Area)</label>
                <div class="relative">
                    <select id="area" name="area_id" class="w-full px-4 py-3 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 appearance-none bg-white" required>
                        <option value="">Choose...</option>
                        <?php foreach ($areas as $area): 
                            $selected = ($area['id'] == $allocation['area_id']) ? 'selected' : '';
                        ?>
                            <option value="<?php echo $area['id']; ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($area['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-secondary-500">
                         <ion-icon name="chevron-down-outline"></ion-icon>
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <label class="block text-secondary-600 text-sm font-medium mb-2" for="slot">Time Slot</label>
                <div class="grid grid-cols-2 gap-3">
                    <?php 
                        $slots = [
                            'morning' => 'Morning', 
                            'afternoon' => 'Afternoon', 
                            'both' => 'Both', 
                            'all_time' => 'All Time'
                        ];
                        foreach($slots as $value => $label):
                            $checked = ($allocation['time_slot'] == $value) ? 'checked' : '';
                    ?>
                    <label class="cursor-pointer">
                        <input type="radio" name="time_slot" value="<?php echo $value; ?>" class="peer sr-only" <?php echo $checked; ?>>
                        <div class="px-3 py-3 rounded-lg border border-secondary-200 text-center text-sm font-medium peer-checked:bg-primary-100 peer-checked:text-primary-700 peer-checked:border-primary-500 transition-all">
                            <?php echo $label; ?>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-4">
                <a href="/sodai-dorkar/public/admin/delivery-men/allocation" class="px-6 py-3 rounded-lg border border-secondary-300 text-secondary-600 hover:bg-secondary-50 font-medium transition-colors">
                    Cancel
                </a>
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 px-8 rounded-lg transition-colors shadow-lg shadow-primary-200">
                    Update Allocation
                </button>
            </div>
        </form>
    </div>
</div>
