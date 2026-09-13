<div class="max-w-md mx-auto pt-4" x-data="{ changePasswordModal: false }">
    <div class="flex justify-between items-center mb-6 px-2">
        <h1 class="text-2xl font-black text-secondary-900">Settings</h1>
        <span class="text-xs bg-secondary-100 text-secondary-600 px-2.5 py-1 rounded-full font-bold">App v1.2</span>
    </div>

    <?php if (!empty($error)): ?>
        <div class="mb-4 mx-2 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl text-xs flex items-center gap-2">
            <ion-icon name="alert-circle" class="text-lg text-red-500 shrink-0"></ion-icon>
            <span><?php echo htmlspecialchars($error); ?></span>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="mb-4 mx-2 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-2xl text-xs flex items-center gap-2">
            <ion-icon name="checkmark-circle" class="text-lg text-green-500 shrink-0"></ion-icon>
            <span><?php echo htmlspecialchars($success); ?></span>
        </div>
    <?php endif; ?>

    <!-- Security & Account Settings -->
    <div class="bg-white rounded-3xl shadow-sm border border-secondary-100 overflow-hidden mb-6">
        <div class="p-4 border-b border-secondary-100 text-xs font-bold uppercase tracking-wider text-secondary-400">Security & App</div>
        <div class="divide-y divide-secondary-100">
            <!-- Change Password Button -->
            <button type="button" @click="changePasswordModal = true" class="w-full flex items-center justify-between p-4 hover:bg-secondary-50 transition-colors text-left">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mr-3 shadow-inner">
                        <ion-icon name="lock-closed-outline" class="text-xl"></ion-icon>
                    </div>
                    <div>
                        <span class="font-bold text-sm text-secondary-800 block">Change Password</span>
                        <span class="text-[11px] text-secondary-400">Update your account login password</span>
                    </div>
                </div>
                <ion-icon name="chevron-forward" class="text-secondary-400"></ion-icon>
            </button>

            <!-- PWA Add to Home Screen info -->
            <div class="p-4 flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mr-3 shadow-inner">
                        <ion-icon name="phone-portrait-outline" class="text-xl"></ion-icon>
                    </div>
                    <div>
                        <span class="font-bold text-sm text-secondary-800 block">Mobile App Mode</span>
                        <span class="text-[11px] text-secondary-400">Install to Home Screen for best experience</span>
                    </div>
                </div>
                <span class="text-[10px] font-bold bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">PWA</span>
            </div>
        </div>
    </div>

    <!-- Logout Box -->
    <div class="bg-white rounded-3xl shadow-sm border border-secondary-100 overflow-hidden mb-8">
        <a href="/sodai-dorkar/public/logout" class="flex items-center p-4 text-red-500 hover:bg-red-50 transition-colors">
            <div class="w-10 h-10 rounded-2xl bg-red-50 flex items-center justify-center mr-3 shadow-inner">
                <ion-icon name="log-out-outline" class="text-xl text-red-500"></ion-icon>
            </div>
            <div>
                <span class="font-bold text-sm block">Log Out</span>
                <span class="text-[11px] text-secondary-400">Exit from delivery dashboard</span>
            </div>
        </a>
    </div>

    <!-- ==================== CHANGE PASSWORD MODAL ==================== -->
    <div x-show="changePasswordModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4 backdrop-blur-sm" x-transition>
        <div @click.away="changePasswordModal = false" class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl relative">
            <div class="flex justify-between items-center mb-4 pb-2 border-b border-secondary-100">
                <div>
                    <h3 class="font-black text-lg text-secondary-900">Change Password</h3>
                    <p class="text-xs text-secondary-400">Set a strong new password</p>
                </div>
                <button @click="changePasswordModal = false" class="text-secondary-400 hover:text-secondary-600">
                    <ion-icon name="close" class="text-2xl"></ion-icon>
                </button>
            </div>

            <form action="/sodai-dorkar/public/delivery/change-password" method="POST">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                <div class="space-y-3 mb-6">
                    <div>
                        <label class="block text-xs font-bold text-secondary-700 mb-1">Current Password</label>
                        <input type="password" name="current_password" required placeholder="••••••••" class="w-full px-3 py-2.5 border border-secondary-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-secondary-700 mb-1">New Password</label>
                        <input type="password" name="new_password" required minlength="4" placeholder="At least 4 characters" class="w-full px-3 py-2.5 border border-secondary-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-secondary-700 mb-1">Confirm New Password</label>
                        <input type="password" name="confirm_password" required minlength="4" placeholder="Repeat new password" class="w-full px-3 py-2.5 border border-secondary-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <button type="button" @click="changePasswordModal = false" class="w-full py-3 rounded-xl border border-secondary-200 text-secondary-600 font-bold text-xs hover:bg-secondary-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="w-full py-3 rounded-xl bg-primary-600 text-white font-bold text-xs shadow-lg hover:bg-primary-700 transition-colors">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
