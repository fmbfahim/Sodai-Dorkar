<div class="mb-6 space-y-3">
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-bold text-secondary-800">Customer List</h2>
        <a href="/sodai-dorkar/public/admin/customers/create" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors">
            <ion-icon name="person-add-outline" class="mr-2"></ion-icon>
            New Customer
        </a>
    </div>

    <!-- Filter Form -->
    <form action="" method="GET" class="bg-white p-4 rounded-xl shadow-sm border border-secondary-100 flex flex-wrap gap-4 items-end">
        
        <!-- Search -->
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-bold text-secondary-600 uppercase mb-1">Search</label>
            <div class="relative">
                <input type="text" name="q" value="<?php echo htmlspecialchars($filters['search']); ?>" placeholder="ID, Name or Phone..." class="w-full pl-10 pr-4 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-secondary-400">
                    <ion-icon name="search-outline"></ion-icon>
                </div>
            </div>
        </div>

        <!-- Area -->
        <div class="w-40">
            <label class="block text-xs font-bold text-secondary-600 uppercase mb-1">Area</label>
            <select name="area_id" class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm" onchange="this.form.submit()">
                <option value="">All Areas</option>
                <?php foreach($areas as $area): ?>
                    <option value="<?php echo $area['id']; ?>" <?php echo $filters['area_id'] == $area['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($area['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Zone -->
        <div class="w-40">
            <label class="block text-xs font-bold text-secondary-600 uppercase mb-1">Zone</label>
            <select name="zone_id" class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm" onchange="this.form.submit()">
                <option value="">All Zones</option>
                <?php foreach($zones as $zone): ?>
                    <option value="<?php echo $zone['id']; ?>" <?php echo $filters['zone_id'] == $zone['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($zone['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Point -->
        <div class="w-40">
            <label class="block text-xs font-bold text-secondary-600 uppercase mb-1">Point</label>
            <select name="point_id" class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm" onchange="this.form.submit()">
                <option value="">All Points</option>
                <?php foreach($points as $point): ?>
                    <option value="<?php echo $point['id']; ?>" <?php echo $filters['point_id'] == $point['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($point['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="bg-secondary-800 hover:bg-secondary-900 text-white font-bold py-2 px-4 rounded-lg text-sm transition-colors">
                Filter
            </button>
            <?php if(!empty($filters['search']) || !empty($filters['area_id']) || !empty($filters['zone_id']) || !empty($filters['point_id'])): ?>
                <a href="/sodai-dorkar/public/admin/customers" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded-lg text-sm transition-colors flex items-center">
                    <ion-icon name="close-circle-outline" class="mr-1"></ion-icon> Clear
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-secondary-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-secondary-600">
            <thead class="bg-secondary-50 text-secondary-500">
                <tr>
                    <th class="px-6 py-3 font-medium">Customer ID</th>
                    <th class="px-6 py-3 font-medium">Name & Info</th>
                    <th class="px-6 py-3 font-medium">Area</th>
                    <th class="px-6 py-3 font-medium">Demographics</th>
                    <th class="px-6 py-3 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-secondary-100">
                <?php if (empty($customers)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-secondary-400">
                            No customers found.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($customers as $c): ?>
                        <?php $demos = json_decode($c['demographics_json'], true) ?? []; ?>
                        <tr class="hover:bg-secondary-50">
                            <td class="px-6 py-4">
                                <a href="/sodai-dorkar/public/admin/customers/show?id=<?php echo $c['id']; ?>" class="font-mono font-bold text-primary-600 bg-primary-50 px-2 py-1 rounded hover:bg-primary-100 transition-colors inline-block" title="View Customer Profile">
                                    <?php echo htmlspecialchars($c['unique_code']); ?>
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <a href="/sodai-dorkar/public/admin/customers/show?id=<?php echo $c['id']; ?>" class="font-medium text-secondary-900 hover:text-primary-600 transition-colors block" title="View Customer Profile">
                                    <?php echo htmlspecialchars($c['name']); ?>
                                </a>
                                <div class="text-xs text-secondary-500 flex items-center mt-1">
                                    <ion-icon name="call-outline" class="mr-1"></ion-icon> <?php echo htmlspecialchars($c['phone']); ?>
                                </div>
                                <div class="text-xs text-secondary-400 mt-0.5 truncate max-w-xs" title="<?php echo htmlspecialchars($c['address_details']); ?>">
                                    <?php echo htmlspecialchars($c['address_details']); ?>
                                </div>
                                <?php if(!empty($c['reset_code'])): ?>
                                <div class="mt-2 inline-block px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-bold rounded border border-yellow-200">
                                    Auth PIN: <?php echo htmlspecialchars($c['reset_code']); ?>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php if($c['area_name']): ?>
                                    <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded text-xs font-medium">
                                        <?php echo htmlspecialchars($c['area_name']); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-secondary-400 italic">Unassigned</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <?php if(!empty($demos['kids_count'])): ?>
                                        <div title="Kids" class="flex items-center text-xs bg-pink-100 text-pink-700 px-2 py-1 rounded">
                                            <ion-icon name="happy-outline" class="mr-1"></ion-icon> <?php echo $demos['kids_count']; ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if(!empty($demos['elderly_count'])): ?>
                                        <div title="Elderly" class="flex items-center text-xs bg-gray-200 text-gray-700 px-2 py-1 rounded">
                                            <ion-icon name="accessibility-outline" class="mr-1"></ion-icon> <?php echo $demos['elderly_count']; ?>
                                        </div>
                                    <?php endif; ?>
                                     <?php if(!empty($demos['exhpat_count'])): ?>
                                        <div title="Expatriates" class="flex items-center text-xs bg-indigo-100 text-indigo-700 px-2 py-1 rounded">
                                            <ion-icon name="airplane-outline" class="mr-1"></ion-icon> <?php echo $demos['exhpat_count']; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="/sodai-dorkar/public/admin/orders/create?customer_id=<?php echo $c['id']; ?>" class="text-green-600 hover:text-green-800 p-1 mr-2 inline-block" title="Create Order">
                                    <ion-icon name="bag-add-outline" class="text-lg"></ion-icon>
                                </a>
                                <form action="/sodai-dorkar/public/admin/customers/generate-pin" method="POST" class="inline" title="Generate Support PIN">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                                    <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                                    <button type="submit" class="text-yellow-600 hover:text-yellow-800 p-1 mr-2 inline-block">
                                        <ion-icon name="key-outline" class="text-lg"></ion-icon>
                                    </button>
                                </form>
                                <a href="/sodai-dorkar/public/admin/customers/history?id=<?php echo $c['id']; ?>" class="text-purple-500 hover:text-purple-700 p-1 mr-2 inline-block" title="Order History">
                                    <ion-icon name="time-outline" class="text-lg"></ion-icon>
                                </a>
                                <a href="/sodai-dorkar/public/admin/customers/edit?id=<?php echo $c['id']; ?>" class="text-blue-500 hover:text-blue-700 p-1 mr-2 inline-block">
                                    <ion-icon name="create-outline" class="text-lg"></ion-icon>
                                </a>
                                <form action="/sodai-dorkar/public/admin/customers/delete" method="POST" onsubmit="return confirm('Delete customer?');" class="inline">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                                    <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
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
