<?php 
ob_start(); 
use Core\Lang;
$__ = function($key, $r = []) { return Lang::get($key, $r); };
?>

<div class="bg-gray-50 flex-grow flex items-center justify-center py-20">
    <div class="container mx-auto px-4 max-w-md text-center">
        <div class="bg-white rounded-3xl shadow-xl p-10 relative overflow-hidden">
            <!-- Decorative circle behind checkmark -->
            <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2 w-40 h-40 bg-green-50 rounded-full"></div>
            
            <div class="relative z-10 flex justify-center mb-6">
                <div class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center shadow-lg shadow-green-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
            
            <h1 class="text-3xl font-black text-gray-800 mb-2"><?= $__('success_title') ?></h1>
            <p class="text-gray-500 mb-8"><?= $__('success_subtitle') ?></p>
            
            <?php if ($orderId): ?>
            <div class="bg-gray-50 border border-gray-100 rounded-xl p-4 mb-8">
                <p class="text-sm text-gray-500 mb-1"><?= $__('success_order_id') ?></p>
                <p class="text-2xl font-bold text-gray-800 tracking-wider">#<?= str_pad($orderId, 6, '0', STR_PAD_LEFT) ?></p>
            </div>
            <?php endif; ?>
            
            <div class="space-y-4">
                <a href="/sodai-dorkar/public/" class="block w-full bg-green-600 text-white font-bold py-3 px-6 rounded-xl hover:bg-green-700 transition-all shadow-md hover:shadow-lg hover:-translate-y-0.5">
                    <?= $__('success_continue') ?>
                </a>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
require 'layout.php';
?>
