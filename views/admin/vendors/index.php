<div class="flex justify-between items-center mb-6">
    <h3 class="text-lg font-bold text-secondary-800">Supplier / Vendor Management</h3>
    <button onclick="document.getElementById('addVendorModal').classList.remove('hidden')" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors">
        <ion-icon name="add-circle-outline" class="mr-2"></ion-icon>
        Add Vendor
    </button>
</div>

<div class="bg-white rounded-xl shadow-sm border border-secondary-100 overflow-hidden">
    <table class="w-full text-left text-sm text-secondary-600">
        <thead class="bg-secondary-50 text-secondary-500">
            <tr>
                <th class="px-6 py-3 font-medium">Name</th>
                <th class="px-6 py-3 font-medium">Contact</th>
                <th class="px-6 py-3 font-medium">Address</th>
                <th class="px-6 py-3 font-medium text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-secondary-100">
            <?php foreach ($vendors as $v): ?>
                <tr class="hover:bg-secondary-50 transition-colors">
                    <td class="px-6 py-4 font-medium text-secondary-900">
                        <?php echo htmlspecialchars($v['name']); ?>
                    </td>
                    <td class="px-6 py-4">
                        <?php echo htmlspecialchars($v['contact']); ?>
                    </td>
                    <td class="px-6 py-4 text-xs">
                        <?php echo htmlspecialchars($v['address']); ?>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="/sodai-dorkar/public/admin/vendors/ledger?id=<?php echo $v['id']; ?>" class="text-blue-600 hover:text-blue-800 mr-3 font-medium text-xs border border-blue-200 bg-blue-50 px-2 py-1 rounded">
                            Ledger
                        </a>
                        <form action="/sodai-dorkar/public/admin/vendors/delete" method="POST" onsubmit="return confirm('Delete vendor?');" class="inline">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                            <input type="hidden" name="id" value="<?php echo $v['id']; ?>">
                            <button type="submit" class="text-red-500 hover:text-red-700 p-1">
                                <ion-icon name="trash-outline" class="text-lg"></ion-icon>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if(empty($vendors)): ?>
                <tr><td colspan="4" class="px-6 py-8 text-center text-secondary-400">No vendors found. Add one to get started.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Add Modal -->
<div id="addVendorModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4 border-b border-secondary-100 pb-2">
            <h4 class="text-lg font-bold text-secondary-800">Add New Vendor</h4>
            <button onclick="document.getElementById('addVendorModal').classList.add('hidden')" class="text-secondary-400 hover:text-secondary-600">
                <ion-icon name="close-outline" class="text-2xl"></ion-icon>
            </button>
        </div>
        <form action="/sodai-dorkar/public/admin/vendors/store" method="POST">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
            <div class="mb-4">
                <label class="block text-sm font-medium text-secondary-600 mb-1">Company / Vendor Name</label>
                <input type="text" name="name" required class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-secondary-600 mb-1">Contact Details</label>
                <input type="text" name="contact" required class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-secondary-600 mb-1">Address</label>
                <textarea name="address" rows="3" class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('addVendorModal').classList.add('hidden')" class="px-4 py-2 text-secondary-600 hover:bg-secondary-50 rounded-lg">Cancel</button>
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-6 rounded-lg">Save Vendor</button>
            </div>
        </form>
    </div>
</div>
