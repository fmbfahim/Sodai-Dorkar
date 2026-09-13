<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-secondary-800">Customer Profile</h2>
            <p class="text-sm text-secondary-500 mt-1">View all details of the customer</p>
        </div>
        <div class="flex gap-2">
            <a href="/sodai-dorkar/public/admin/customers" class="text-secondary-600 hover:text-primary-600 bg-white border border-secondary-300 hover:bg-secondary-50 font-bold py-2 px-4 rounded-lg flex items-center transition-colors">
                <ion-icon name="arrow-back-outline" class="mr-2 text-xl"></ion-icon>
                Back to List
            </a>
            <a href="/sodai-dorkar/public/admin/customers/edit?id=<?php echo $customer['id']; ?>" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors">
                <ion-icon name="create-outline" class="mr-2 text-xl"></ion-icon>
                Edit
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-secondary-100 overflow-hidden mb-6">
        <div class="p-6 border-b border-secondary-100 flex items-center gap-4 bg-secondary-50">
            <div class="w-16 h-16 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center text-3xl font-bold shadow-sm">
                <?php echo strtoupper(substr($customer['name'], 0, 1)); ?>
            </div>
            <div>
                <h3 class="text-xl font-bold text-secondary-900"><?php echo htmlspecialchars($customer['name']); ?></h3>
                <div class="flex items-center text-secondary-600 mt-1 gap-3">
                    <span class="flex items-center"><ion-icon name="call-outline" class="mr-1"></ion-icon> <?php echo htmlspecialchars($customer['phone']); ?></span>
                    <span class="bg-primary-100 text-primary-700 px-2 py-0.5 rounded font-mono text-sm font-bold border border-primary-200 shadow-sm">
                        Code: <?php echo htmlspecialchars($customer['unique_code']); ?>
                    </span>
                    <?php if(!empty($customer['reset_code'])): ?>
                        <span class="bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded text-sm font-bold border border-yellow-200 shadow-sm" title="Support PIN">
                            Support PIN: <?php echo htmlspecialchars($customer['reset_code']); ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Location Details -->
            <div>
                <h4 class="text-lg font-bold text-secondary-800 border-b border-secondary-100 pb-2 mb-4 flex items-center">
                    <ion-icon name="location-outline" class="mr-2 text-primary-500"></ion-icon> Location Details
                </h4>
                <div class="space-y-4">
                    <div class="flex items-start">
                        <span class="w-32 text-sm font-medium text-secondary-500">Address:</span>
                        <span class="flex-1 text-secondary-900"><?php echo htmlspecialchars($customer['address_details']); ?></span>
                    </div>
                    <div class="flex items-start">
                        <span class="w-32 text-sm font-medium text-secondary-500">Area:</span>
                        <span class="flex-1 text-secondary-900 font-medium">
                            <?php echo $customer['area_name'] ? htmlspecialchars($customer['area_name']) : '<span class="text-secondary-400 italic">Unassigned</span>'; ?>
                        </span>
                    </div>
                    <div class="flex items-start">
                        <span class="w-32 text-sm font-medium text-secondary-500">Zone:</span>
                        <span class="flex-1 text-secondary-900">
                            <?php echo $customer['zone_name'] ? htmlspecialchars($customer['zone_name']) : '<span class="text-secondary-400 italic">Unassigned</span>'; ?>
                        </span>
                    </div>
                    <div class="flex items-start">
                        <span class="w-32 text-sm font-medium text-secondary-500">Point:</span>
                        <span class="flex-1 text-secondary-900">
                            <?php echo $customer['point_name'] ? htmlspecialchars($customer['point_name']) : '<span class="text-secondary-400 italic">Unassigned</span>'; ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Demographics & Notes -->
            <div>
                <h4 class="text-lg font-bold text-secondary-800 border-b border-secondary-100 pb-2 mb-4 flex items-center">
                    <ion-icon name="people-outline" class="mr-2 text-primary-500"></ion-icon> Demographics & Notes
                </h4>
                <?php $demos = json_decode($customer['demographics_json'], true) ?? []; ?>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="bg-secondary-50 p-3 rounded-lg border border-secondary-100 flex items-center justify-between shadow-sm">
                        <div class="flex items-center text-secondary-600 text-sm font-medium">
                            <ion-icon name="person-outline" class="mr-1.5 text-lg"></ion-icon> Adults
                        </div>
                        <span class="font-bold text-secondary-900"><?php echo $demos['adult_count'] ?? 0; ?></span>
                    </div>
                    <div class="bg-pink-50 p-3 rounded-lg border border-pink-100 flex items-center justify-between shadow-sm">
                        <div class="flex items-center text-pink-700 text-sm font-medium">
                            <ion-icon name="happy-outline" class="mr-1.5 text-lg"></ion-icon> Kids
                        </div>
                        <span class="font-bold text-pink-800"><?php echo $demos['kids_count'] ?? 0; ?></span>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 flex items-center justify-between shadow-sm">
                        <div class="flex items-center text-gray-700 text-sm font-medium">
                            <ion-icon name="accessibility-outline" class="mr-1.5 text-lg"></ion-icon> Elderly
                        </div>
                        <span class="font-bold text-gray-800"><?php echo $demos['elderly_count'] ?? 0; ?></span>
                    </div>
                    <div class="bg-indigo-50 p-3 rounded-lg border border-indigo-100 flex items-center justify-between shadow-sm">
                        <div class="flex items-center text-indigo-700 text-sm font-medium">
                            <ion-icon name="airplane-outline" class="mr-1.5 text-lg"></ion-icon> Expatriates
                        </div>
                        <span class="font-bold text-indigo-800"><?php echo $demos['exhpat_count'] ?? 0; ?></span>
                    </div>
                </div>
                
                <div class="mt-4">
                    <span class="block text-sm font-medium text-secondary-500 mb-1">Additional Notes:</span>
                    <div class="p-4 bg-yellow-50 border border-yellow-100 rounded-lg text-secondary-800 text-sm whitespace-pre-wrap shadow-inner min-h-[60px]">
                        <?php echo !empty($demos['notes']) ? htmlspecialchars($demos['notes']) : '<span class="text-secondary-400 italic">No special notes provided for this customer.</span>'; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Actions & Info Footer -->
        <div class="p-6 bg-secondary-50 border-t border-secondary-100 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div class="text-sm text-secondary-500 flex items-center">
                <ion-icon name="calendar-outline" class="mr-1.5"></ion-icon>
                Registered on: <strong class="ml-1 text-secondary-700"><?php echo date('M j, Y, g:i A', strtotime($customer['created_at'])); ?></strong>
            </div>
            <div class="flex gap-2">
                <form action="/sodai-dorkar/public/admin/customers/generate-pin" method="POST" class="inline" title="Generate New Support PIN">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                    <input type="hidden" name="id" value="<?php echo $customer['id']; ?>">
                    <button type="submit" class="bg-secondary-800 hover:bg-secondary-900 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors text-sm shadow-sm">
                        <ion-icon name="key-outline" class="mr-1.5"></ion-icon> Generate Support PIN
                    </button>
                </form>
                <a href="/sodai-dorkar/public/admin/orders/create?customer_id=<?php echo $customer['id']; ?>" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors text-sm shadow-sm">
                    <ion-icon name="bag-add-outline" class="mr-1.5"></ion-icon> Create Order
                </a>
                <a href="/sodai-dorkar/public/admin/customers/history?id=<?php echo $customer['id']; ?>" class="bg-white border border-secondary-300 text-secondary-700 hover:bg-secondary-50 font-bold py-2 px-4 rounded-lg flex items-center transition-colors text-sm shadow-sm">
                    <ion-icon name="time-outline" class="mr-1.5"></ion-icon> Order History
                </a>
            </div>
        </div>
    </div>
</div>
