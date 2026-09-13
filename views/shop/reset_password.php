<?php 
$hideCart = true;
ob_start(); 
?>

<div class="bg-gray-100 min-h-screen pt-24 pb-12">
    <div class="max-w-md mx-auto">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden p-8">
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 text-green-600 mb-4">
                    <ion-icon name="lock-open-outline" class="text-3xl"></ion-icon>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">Set New Password</h2>
                <p class="text-sm text-gray-500 mt-2">For security, please set a new password for your account.</p>
            </div>

            <?php if(isset($error)): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-xl text-sm font-medium mb-6 flex items-center gap-2">
                <ion-icon name="alert-circle"></ion-icon>
                <?php 
                    if($error === 'mismatch') echo "Passwords do not match.";
                    elseif($error === 'short') echo "Password must be at least 6 characters.";
                    else echo "Something went wrong.";
                ?>
            </div>
            <?php endif; ?>

            <form action="/sodai-dorkar/public/checkout/reset-password" method="POST">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">New Password</label>
                    <input type="password" name="new_password" required minlength="6" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors" placeholder="••••••••">
                </div>
                <div class="mb-8">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password</label>
                    <input type="password" name="confirm_password" required minlength="6" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors" placeholder="••••••••">
                </div>
                <button type="submit" class="w-full bg-green-600 text-white font-bold py-3.5 px-6 rounded-xl hover:bg-green-700 transition-colors shadow-sm">
                    Save Password & Continue Checkout
                </button>
            </form>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
require 'layout.php'; 
?>
