<?php 
ob_start(); 
use Core\Lang;
$__ = function($key, $r = []) { return Lang::get($key, $r); };
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
$locale = Lang::locale();
?>

<div class="bg-gray-50/60 flex-grow py-8 sm:py-12">
    <div class="container mx-auto px-4 max-w-5xl">
        
        <!-- Step Indicator -->
        <div class="max-w-md mx-auto mb-8">
            <div class="flex items-center justify-between text-xs font-semibold text-gray-400">
                <span class="flex items-center gap-1.5 text-emerald-600 font-bold">
                    <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[11px]">✓</span>
                    Cart
                </span>
                <span class="h-0.5 w-12 bg-emerald-200"></span>
                <span class="flex items-center gap-1.5 text-emerald-600 font-bold">
                    <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[11px]">✓</span>
                    Checkout
                </span>
                <span class="h-0.5 w-12 bg-emerald-500"></span>
                <span class="flex items-center gap-1.5 text-emerald-600 font-black">
                    <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[11px]">3</span>
                    Order Placed
                </span>
            </div>
        </div>

        <!-- Success Confirmation Card -->
        <div class="bg-white rounded-3xl shadow-sm border border-emerald-100/80 p-6 sm:p-10 relative overflow-hidden text-center max-w-2xl mx-auto mb-12">
            <!-- Decorative circle -->
            <div class="absolute -top-16 left-1/2 -translate-x-1/2 w-48 h-48 bg-gradient-to-b from-emerald-100/60 to-transparent rounded-full pointer-events-none"></div>
            
            <!-- Animated Icon -->
            <div class="relative z-10 flex justify-center mb-5">
                <div class="w-20 h-20 bg-gradient-to-tr from-emerald-600 to-teal-500 rounded-full flex items-center justify-center shadow-xl shadow-emerald-600/25 ring-8 ring-emerald-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
            
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 mb-2">Order Confirmed!</h1>
            <p class="text-gray-500 text-sm sm:text-base mb-6 max-w-md mx-auto">
                Thank you for your order. We have received your request and our delivery team is preparing your fresh items!
            </p>
            
            <?php if (!empty($orderId)): ?>
            <div class="bg-emerald-50/50 border border-emerald-100 rounded-2xl p-5 mb-6 max-w-md mx-auto">
                <div class="flex items-center justify-between pb-3 border-b border-emerald-100 text-xs sm:text-sm">
                    <span class="text-gray-500 font-medium">Order Reference:</span>
                    <span class="font-black text-emerald-800 tracking-wider text-base">#<?= str_pad($orderId, 6, '0', STR_PAD_LEFT) ?></span>
                </div>
                <?php if (!empty($order)): ?>
                    <div class="flex items-center justify-between py-2 border-b border-emerald-100 text-xs sm:text-sm">
                        <span class="text-gray-500 font-medium">Total Amount:</span>
                        <span class="font-black text-gray-900 text-base">৳<?= number_format($order['total_amount'] ?? 0, 2) ?></span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-emerald-100 text-xs sm:text-sm">
                        <span class="text-gray-500 font-medium">Payment Mode:</span>
                        <span class="font-bold text-gray-800 uppercase text-xs px-2 py-0.5 bg-white rounded-md border border-gray-200">
                            <?= htmlspecialchars(str_replace('_', ' ', $order['payment_method'] ?? 'COD')) ?>
                        </span>
                    </div>
                    <?php if (!empty($order['delivery_address'])): ?>
                    <div class="pt-2 text-left text-xs text-gray-600">
                        <span class="font-semibold text-gray-700">Delivery Address: </span>
                        <?= htmlspecialchars($order['delivery_address']) ?>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Quick Actions -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 max-w-md mx-auto">
                <?php if (!empty($orderId)): ?>
                <a href="<?= $base ?>/account/order-detail?id=<?= $orderId ?>" 
                   class="w-full sm:w-auto flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-6 rounded-xl transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Track My Order
                </a>
                <?php endif; ?>
                <a href="<?= $base ?>/" 
                   class="w-full sm:w-auto flex-1 bg-white hover:bg-gray-50 text-gray-700 font-bold py-3 px-6 rounded-xl border border-gray-200 transition-all flex items-center justify-center gap-2 text-sm shadow-2xs">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    Continue Shopping
                </a>
            </div>
        </div>

        <!-- Cross Selling / Recommended Products Section -->
        <?php if (!empty($crossSellingProducts)): ?>
        <div class="mt-12">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-2 mb-6 border-b border-gray-200/80 pb-4">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold mb-1.5">
                        <span>⚡</span>
                        <span>Frequently Bought Together</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">You May Also Like</h2>
                    <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Customers who ordered these items also bought these daily kitchen favorites</p>
                </div>
                <a href="<?= $base ?>/" class="text-xs sm:text-sm font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1 self-start sm:self-auto group">
                    <span>Explore Full Catalog</span>
                    <span class="group-hover:translate-x-0.5 transition-transform">→</span>
                </a>
            </div>

            <!-- Products Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3.5 sm:gap-5">
                <?php foreach ($crossSellingProducts as $crossProd): 
                    $pSell = (float)($crossProd['sell_price'] ?? 0);
                    $pReg = !empty($crossProd['regular_price']) ? (float)$crossProd['regular_price'] : null;
                    $pHasDisc = ($pReg && $pReg > $pSell);
                    $pDiscPercent = $pHasDisc ? round((($pReg - $pSell) / $pReg) * 100) : 0;
                    
                    $pImg = !empty($crossProd['image_path']) ? htmlspecialchars($crossProd['image_path']) : $base . '/images/default-product.svg';
                    if (strpos($pImg, 'http') !== 0 && strpos($pImg, $base) !== 0 && strpos($pImg, '/') === 0) {
                        $pImg = $base . $pImg;
                    }
                    $unitDisplay = $crossProd['selling_unit'] ?? $crossProd['base_unit'] ?? '1 Unit';
                ?>
                <div class="product-card bg-white rounded-2xl border border-gray-100 hover:border-emerald-300 shadow-2xs hover:shadow-lg transition-all duration-300 flex flex-col overflow-hidden group">
                    
                    <!-- Product Image -->
                    <a href="<?= $base ?>/product?id=<?= $crossProd['id'] ?>" class="relative block bg-white h-44 sm:h-52 w-full p-2.5 flex items-center justify-center overflow-hidden">
                        <?php if ($pHasDisc): ?>
                            <span class="absolute top-2.5 left-2.5 bg-rose-500 text-white text-[10px] font-black px-2 py-0.5 rounded-md shadow-2xs z-10">
                                -<?= $pDiscPercent ?>%
                            </span>
                        <?php endif; ?>
                        <img src="<?= $pImg ?>" 
                             alt="<?= htmlspecialchars($crossProd['name']) ?>" 
                             class="h-full w-full object-contain mix-blend-multiply group-hover:scale-105 transition-transform duration-300"
                             loading="lazy"
                             onerror="this.src='<?= $base ?>/images/default-product.svg'">
                    </a>

                    <!-- Product Body -->
                    <div class="p-3 sm:p-4 flex flex-col flex-grow bg-white">
                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1 truncate">
                            <?= htmlspecialchars($crossProd['category_name'] ?? 'Fresh Food') ?>
                        </div>

                        <a href="<?= $base ?>/product?id=<?= $crossProd['id'] ?>" class="font-bold text-gray-900 text-xs sm:text-sm leading-snug line-clamp-2 hover:text-emerald-700 transition-colors mb-1">
                            <?= htmlspecialchars($crossProd['name']) ?>
                        </a>
                        
                        <div class="text-[11px] text-gray-400 font-medium mb-3">
                            <?= htmlspecialchars($unitDisplay) ?>
                        </div>

                        <!-- Price Row -->
                        <div class="flex items-baseline gap-1.5 mb-3.5 mt-auto">
                            <span class="text-xs font-bold text-emerald-700">৳</span>
                            <span class="text-base sm:text-lg font-black text-gray-900"><?= number_format($pSell, 2) ?></span>
                            <?php if ($pHasDisc): ?>
                                <span class="text-[11px] text-gray-400 line-through">৳<?= number_format($pReg, 2) ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Add to Bag Button -->
                        <button type="button" 
                                onclick="addToCartAjax(<?= $crossProd['id'] ?>, this)"
                                class="w-full py-2.5 px-3 rounded-xl bg-emerald-50 hover:bg-emerald-600 text-emerald-800 hover:text-white border border-emerald-200 hover:border-emerald-600 font-bold text-xs flex items-center justify-center gap-1.5 transition-all duration-200 cursor-pointer shadow-2xs hover:shadow-sm group/btn">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600 group-hover/btn:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <span>Add to Cart</span>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php 
$content = ob_get_clean();
require 'layout.php';
?>
