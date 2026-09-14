<?php 
ob_start();
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
?>

<div class="space-y-5">
    <?php if (!empty($success)): ?>
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl p-4 flex items-center gap-2.5 text-xs sm:text-sm font-bold shadow-2xs">
        <ion-icon name="checkmark-circle" class="text-emerald-600 text-xl flex-shrink-0"></ion-icon>
        <span>Password updated successfully!</span>
    </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
    <?php $errorMessages = [
        'wrong_current' => 'The current password you entered is incorrect.',
        'too_short'     => 'New password must contain at least 6 characters.',
        'mismatch'      => 'New password and confirmation password do not match.',
    ]; ?>
    <div class="bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl p-4 flex items-center gap-2.5 text-xs sm:text-sm font-bold shadow-2xs">
        <ion-icon name="alert-circle" class="text-rose-600 text-xl flex-shrink-0"></ion-icon>
        <span><?= $errorMessages[$error] ?? 'Failed to update password. Please try again.' ?></span>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-2xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-gray-100 bg-gray-50/50">
            <h1 class="font-black text-gray-900 text-base sm:text-lg flex items-center gap-2">
                <ion-icon name="lock-closed-outline" class="text-emerald-600 text-xl"></ion-icon> 
                <span>Change Account Password</span>
            </h1>
            <p class="text-xs text-gray-500 mt-0.5">Ensure your account is protected with a secure password.</p>
        </div>

        <form action="<?= $base ?>/account/change-password" method="POST" class="p-4 sm:p-6 space-y-4">
            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Current Password *</label>
                <div class="relative">
                    <input type="password" name="current_password" id="currentPass" required placeholder="••••••••"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-emerald-500 outline-none transition-all pr-11 shadow-2xs">
                    <button type="button" onclick="togglePass('currentPass', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 p-1">
                        <ion-icon name="eye-outline" class="text-base"></ion-icon>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">New Password * (Min. 6 characters)</label>
                <div class="relative">
                    <input type="password" name="new_password" id="newPass" required minlength="6" placeholder="••••••••"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-emerald-500 outline-none transition-all pr-11 shadow-2xs">
                    <button type="button" onclick="togglePass('newPass', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 p-1">
                        <ion-icon name="eye-outline" class="text-base"></ion-icon>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Confirm New Password *</label>
                <div class="relative">
                    <input type="password" name="confirm_password" id="confirmPass" required minlength="6" placeholder="••••••••"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-emerald-500 outline-none transition-all pr-11 shadow-2xs">
                    <button type="button" onclick="togglePass('confirmPass', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 p-1">
                        <ion-icon name="eye-outline" class="text-base"></ion-icon>
                    </button>
                </div>
            </div>

            <div class="bg-amber-50/80 border border-amber-200 rounded-xl p-3.5 text-xs text-amber-900 flex items-start gap-2">
                <ion-icon name="shield-checkmark-outline" class="text-amber-600 text-base mt-0.5 flex-shrink-0"></ion-icon>
                <div>
                    <strong>Security Recommendation:</strong> Use a combination of uppercase letters, numbers, and symbols to create a strong password that you do not use on other sites.
                </div>
            </div>

            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl transition-colors flex items-center justify-center gap-2 text-xs sm:text-sm shadow-md hover:shadow-lg">
                <ion-icon name="lock-closed" class="text-base"></ion-icon> 
                <span>Update Password</span>
            </button>
        </form>
    </div>
</div>

<script>
function togglePass(inputId, btn) {
    const input = document.getElementById(inputId);
    const isPass = input.type === 'password';
    input.type = isPass ? 'text' : 'password';
    btn.innerHTML = isPass ? '<ion-icon name="eye-off-outline" class="text-base"></ion-icon>' : '<ion-icon name="eye-outline" class="text-base"></ion-icon>';
}
</script>

<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>
