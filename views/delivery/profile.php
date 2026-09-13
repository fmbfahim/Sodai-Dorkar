<div class="max-w-md mx-auto pt-8">
    <div class="bg-white rounded-2xl shadow-sm border border-secondary-100 p-6 text-center mb-6">
        <div class="w-24 h-24 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4 text-primary-600 font-bold text-3xl border-4 border-white shadow-md">
            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
        </div>
        <h2 class="text-xl font-bold text-secondary-900"><?php echo htmlspecialchars($user['name']); ?></h2>
        <p class="text-secondary-500 font-mono text-sm">@<?php echo htmlspecialchars($user['username']); ?></p>
        <div class="mt-2 inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold uppercase tracking-wide">
            Delivery Partner
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-secondary-100 overflow-hidden">
        <div class="p-4 border-b border-secondary-100 font-bold text-secondary-700">Contact Info</div>
        <div class="divide-y divide-secondary-100">
            <div class="p-4 flex items-center">
                <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mr-4">
                    <ion-icon name="call" class="text-xl"></ion-icon>
                </div>
                <div>
                    <p class="text-xs text-secondary-400 uppercase tracking-wide">Phone Number</p>
                    <p class="font-medium text-secondary-900"><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></p>
                </div>
            </div>
             <div class="p-4 flex items-center">
                <div class="w-10 h-10 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center mr-4">
                    <ion-icon name="card" class="text-xl"></ion-icon>
                </div>
                <div>
                    <p class="text-xs text-secondary-400 uppercase tracking-wide">Employee ID</p>
                    <p class="font-medium text-secondary-900">EMP-<?php echo str_pad($user['id'], 4, '0', STR_PAD_LEFT); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
