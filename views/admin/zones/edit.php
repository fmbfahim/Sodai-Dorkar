<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-secondary-100 p-8">
        <div class="flex items-center justify-between mb-6 border-b border-secondary-100 pb-4">
            <h3 class="text-xl font-bold text-secondary-800">Edit Zone (Ward)</h3>
            <a href="/sodai-dorkar/public/admin/zones" class="text-secondary-500 hover:text-secondary-700">
                <ion-icon name="close-outline" class="text-2xl"></ion-icon>
            </a>
        </div>
        
        <form action="/sodai-dorkar/public/admin/zones/update" method="POST">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
            <input type="hidden" name="id" value="<?php echo $zone['id']; ?>">
            
            <div class="mb-6">
                <label class="block text-secondary-600 text-sm font-medium mb-2" for="name">Zone Name (Ward No)</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($zone['name']); ?>" class="w-full px-4 py-3 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" required>
            </div>

            <div class="mb-8">
                <label class="block text-secondary-600 text-sm font-medium mb-2" for="area">Parent Union (Area)</label>
                <div class="relative">
                    <select id="area" name="area_id" class="w-full px-4 py-3 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 appearance-none bg-white">
                        <?php foreach ($areas as $area): ?>
                            <option value="<?php echo $area['id']; ?>" <?php echo $zone['area_id'] == $area['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($area['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-secondary-500">
                         <ion-icon name="chevron-down-outline"></ion-icon>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                 <a href="/sodai-dorkar/public/admin/zones" class="px-6 py-2.5 border border-secondary-300 rounded-lg text-secondary-600 font-bold hover:bg-secondary-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-lg transition-colors shadow-sm">
                    Update Zone
                </button>
            </div>
        </form>
    </div>
</div>
