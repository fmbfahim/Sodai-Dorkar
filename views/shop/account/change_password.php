<?php ob_start(); ?>

<div class="space-y-5">
    <?php if (!empty($success)): ?>
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-3.5 flex items-center gap-2 text-sm font-semibold">
        <ion-icon name="checkmark-circle" class="text-emerald-600 text-lg flex-shrink-0"></ion-icon>
        পাসওয়ার্ড সফলভাবে পরিবর্তন হয়েছে!
    </div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
    <?php $errorMessages = [
        'wrong_current' => 'বর্তমান পাসওয়ার্ড সঠিক নয়।',
        'too_short' => 'নতুন পাসওয়ার্ড কমপক্ষে ৬ অক্ষরের হতে হবে।',
        'mismatch' => 'নতুন পাসওয়ার্ড দুটি মিলছে না।',
    ]; ?>
    <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl p-3.5 flex items-center gap-2 text-sm font-semibold">
        <ion-icon name="alert-circle" class="text-red-500 text-lg flex-shrink-0"></ion-icon>
        <?= $errorMessages[$error] ?? 'একটি ত্রুটি ঘটেছে।' ?>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 bg-gray-50/50">
            <h1 class="font-black text-gray-900 text-lg flex items-center gap-2">
                <ion-icon name="lock-closed-outline" class="text-emerald-600 text-xl"></ion-icon> পাসওয়ার্ড পরিবর্তন
            </h1>
        </div>
        <form action="/sodai-dorkar/public/account/change-password" method="POST" class="p-5 space-y-4">
            <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">

            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">বর্তমান পাসওয়ার্ড *</label>
                <div class="relative">
                    <input type="password" name="current_password" id="currentPass" required
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 outline-none transition-all pr-10">
                    <button type="button" onclick="togglePass('currentPass', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700">
                        <ion-icon name="eye-outline" class="text-base"></ion-icon>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">নতুন পাসওয়ার্ড * (কমপক্ষে ৬ অক্ষর)</label>
                <div class="relative">
                    <input type="password" name="new_password" id="newPass" required minlength="6"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 outline-none transition-all pr-10">
                    <button type="button" onclick="togglePass('newPass', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700">
                        <ion-icon name="eye-outline" class="text-base"></ion-icon>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">নতুন পাসওয়ার্ড নিশ্চিত করুন *</label>
                <div class="relative">
                    <input type="password" name="confirm_password" id="confirmPass" required minlength="6"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 outline-none transition-all pr-10">
                    <button type="button" onclick="togglePass('confirmPass', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700">
                        <ion-icon name="eye-outline" class="text-base"></ion-icon>
                    </button>
                </div>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-800">
                <ion-icon name="shield-checkmark-outline" class="text-amber-600 mr-1"></ion-icon>
                <strong>নিরাপত্তা টিপস:</strong> শক্তিশালী পাসওয়ার্ড ব্যবহার করুন। বড় ও ছোট হাতের অক্ষর, সংখ্যা এবং বিশেষ চিহ্ন মিলিয়ে পাসওয়ার্ড তৈরি করুন।
            </div>

            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl transition-colors flex items-center justify-center gap-2 text-sm">
                <ion-icon name="lock-closed" class="text-base"></ion-icon> পাসওয়ার্ড পরিবর্তন করুন
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

