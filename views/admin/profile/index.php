<div class="max-w-xl mx-auto">
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-secondary-800">My Profile</h2>
        <p class="text-secondary-500">Manage your account settings and preferences.</p>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="mb-6 bg-green-50 text-green-700 px-4 py-3 rounded-lg border border-green-200 flex items-center">
            <ion-icon name="checkmark-circle-outline" class="text-xl mr-2"></ion-icon>
            Profile updated successfully!
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-secondary-100 p-8">
        <form action="/sodai-dorkar/public/admin/profile/update" method="POST">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
            
            <div class="mb-6 flex justify-center">
                <div class="w-24 h-24 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center text-3xl font-bold border-4 border-white shadow-md">
                    <?php echo strtoupper(substr($user['name'] ?? 'A', 0, 1)); ?>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label class="block text-secondary-600 text-sm font-medium mb-2" for="name">Full Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" class="w-full px-4 py-3 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 transition-shadow" required>
                </div>

                <div>
                    <label class="block text-secondary-600 text-sm font-medium mb-2" for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" class="w-full px-4 py-3 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 transition-shadow" required>
                </div>

                <div>
                    <label class="block text-secondary-600 text-sm font-medium mb-2" for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" class="w-full px-4 py-3 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 transition-shadow">
                </div>

                <div class="border-t border-secondary-100 pt-6 mt-2">
                    <h3 class="text-lg font-semibold text-secondary-800 mb-4">Change Password</h3>
                    <p class="text-secondary-500 text-sm mb-4">Leave blank if you don't want to change it.</p>
                    
                    <div class="mb-4">
                        <label class="block text-secondary-600 text-sm font-medium mb-2" for="password">New Password</label>
                        <input type="password" id="password" name="password" class="w-full px-4 py-3 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 transition-shadow">
                    </div>

                    <div class="mb-2">
                        <label class="block text-secondary-600 text-sm font-medium mb-2" for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="w-full px-4 py-3 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 transition-shadow">
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 px-8 rounded-lg transition-colors shadow-lg shadow-primary-200">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
