<?php 
ob_start(); 
use Core\Lang;
$__ = function($key, $r = []) { return Lang::get($key, $r); };
?>

<div class="bg-gray-50 py-10">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-8"><?= $__('cart_title') ?></h1>
        
        <?php if (empty($cart)): ?>
            <div class="bg-white rounded-2xl shadow-sm p-10 text-center">
                <div class="text-gray-300 mb-4 flex justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-semibold text-gray-700 mb-2"><?= $__('cart_empty_title') ?></h2>
                <p class="text-gray-500 mb-6"><?= $__('cart_empty_subtitle') ?></p>
                <a href="/sodai-dorkar/public/" class="inline-block bg-green-600 text-white font-bold py-3 px-8 rounded-full hover:bg-green-700 transition-colors shadow-md hover:shadow-lg">
                    <?= $__('cart_start_shopping') ?>
                </a>
            </div>
        <?php else: ?>
            <!-- Dynamic Spend More Offers & Free Delivery Milestone Bar -->
            <div id="cart-spend-more-container" class="mb-6 <?= empty($spendMoreOffers['enabled']) ? 'hidden' : '' ?>">
                <!-- Populated dynamically by renderSpendMoreOffersUI() -->
            </div>

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Cart Items -->
                <div class="lg:w-2/3">
                    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                        <ul class="divide-y divide-gray-100" id="cart-items-list">
                            <?php 
                            $subtotal = 0;
                            foreach ($cart as $productId => $item): 
                                $itemTotal = $item['price'] * $item['quantity'];
                                $subtotal += $itemTotal;
                            ?>
                                <li class="p-4 sm:p-6 flex flex-col sm:flex-row items-center gap-4 sm:gap-6" id="cart-item-<?= $productId ?>">
                                    <div class="w-20 h-20 sm:w-24 sm:h-24 flex-shrink-0 bg-gray-50 rounded-xl overflow-hidden border border-gray-100 relative flex items-center justify-center">
                                        <?php 
                                        $cartImg = !empty($item['image']) ? htmlspecialchars($item['image']) : '/sodai-dorkar/public/images/default-product.svg';
                                        ?>
                                        <img src="<?= $cartImg ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="w-full h-full object-contain p-1" onerror="this.src='/sodai-dorkar/public/images/default-product.svg'">
                                    </div>
                                    
                                    <div class="flex-grow text-center sm:text-left">
                                        <h3 class="text-base sm:text-lg font-bold text-gray-800 line-clamp-1"><?= htmlspecialchars($item['name']) ?></h3>
                                        <p class="text-green-600 font-semibold mt-1"><?= $__('currency') ?><?= number_format($item['price'], 2) ?></p>
                                    </div>
                                    
                                    <div class="flex items-center gap-3 sm:gap-4">
                                        <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden">
                                            <button type="button" onclick="updateCartQty(<?= $productId ?>, <?= $item['quantity'] - 1 ?>)" 
                                                    class="px-3 py-1.5 bg-gray-50 hover:bg-gray-100 text-gray-600 font-bold transition-colors">−</button>
                                            <span class="w-10 text-center text-gray-800 font-semibold bg-white text-sm" id="qty-<?= $productId ?>"><?= $item['quantity'] ?></span>
                                            <button type="button" onclick="updateCartQty(<?= $productId ?>, <?= $item['quantity'] + 1 ?>)" 
                                                    class="px-3 py-1.5 bg-gray-50 hover:bg-gray-100 text-gray-600 font-bold transition-colors">+</button>
                                        </div>
                                        
                                        <div class="w-20 sm:w-24 text-right font-black text-gray-800" id="item-total-<?= $productId ?>">
                                            <?= $__('currency') ?><?= number_format($itemTotal, 2) ?>
                                        </div>
                                        
                                        <button type="button" onclick="removeFromCart(<?= $productId ?>)" 
                                                class="text-gray-400 hover:text-red-500 transition-colors p-2" title="<?= $__('cart_remove') ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                
                <!-- Order Summary -->
                <div class="lg:w-1/3">
                    <div class="bg-white rounded-2xl shadow-sm p-6 sticky top-24">
                        <h2 class="text-xl font-bold text-gray-800 mb-6"><?= $__('cart_order_summary') ?></h2>
                        
                        <?php 
                        $cartMilestoneDiscount = floatval($spendMoreOffers['discount_amount'] ?? 0);
                        $cartEstimatedTotal = max(0, $subtotal - $cartMilestoneDiscount);
                        ?>
                        <div class="space-y-3 text-gray-600 mb-6 border-b border-gray-100 pb-6">
                            <div class="flex justify-between">
                                <span><?= $__('cart_subtotal') ?> (<span id="summary-item-count"><?= array_sum(array_column($cart, 'quantity')) ?></span> <?= $__('cart_items') ?>)</span>
                                <span class="font-semibold text-gray-800" id="summary-subtotal"><?= $__('currency') ?><?= number_format($subtotal, 2) ?></span>
                            </div>

                            <!-- Milestone Discount Row -->
                            <div id="cart-discount-row" class="flex justify-between items-center text-emerald-700 bg-emerald-50/70 px-2.5 py-1 rounded-lg <?= ($cartMilestoneDiscount > 0) ? '' : 'hidden' ?>">
                                <span class="font-bold text-xs flex items-center gap-1">🏷️ <?= Lang::locale() === 'bn' ? 'অফার বিশেষ ছাড়' : 'Milestone Discount' ?></span>
                                <span class="font-black text-xs" id="cart-summary-discount">- <?= $__('currency') ?><?= number_format($cartMilestoneDiscount, 2) ?></span>
                            </div>

                            <!-- Unlocked Gifts Row -->
                            <div id="cart-gift-row" class="<?= !empty($spendMoreOffers['unlocked_gifts']) ? 'flex' : 'hidden' ?> items-center gap-1.5 p-2 rounded-lg bg-emerald-50/90 border border-emerald-200 text-xs font-bold text-emerald-900">
                                <span>🎁</span>
                                <span><?= Lang::locale() === 'bn' ? 'ফ্রি গিফট প্যাক:' : 'Free Gift:' ?> <span id="cart-gift-text"><?= !empty($spendMoreOffers['unlocked_gifts']) ? htmlspecialchars(implode(', ', $spendMoreOffers['unlocked_gifts'])) : '' ?></span></span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span><?= $__('cart_delivery_fee') ?></span>
                                <span class="font-semibold text-green-600" id="cart-summary-delivery">
                                    <?php if (!empty($deliveryCalc['is_free']) || !empty($spendMoreOffers['is_free_delivery_unlocked'])): ?>
                                        <span class="text-green-600 font-bold"><?= $__('currency') ?>0.00 <span class="text-[11px] bg-green-100 text-green-800 px-1.5 py-0.5 rounded font-bold uppercase"><?= Lang::locale() === 'bn' ? 'ফ্রি!' : 'FREE' ?></span></span>
                                    <?php else: ?>
                                        <?= $__('cart_delivery_calc') ?>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center mb-8">
                            <span class="text-lg font-bold text-gray-800"><?= $__('cart_estimated_total') ?></span>
                            <span class="text-2xl font-black text-green-600" id="summary-total"><?= $__('currency') ?><?= number_format($cartEstimatedTotal, 2) ?></span>
                        </div>
                        
                        <a href="/sodai-dorkar/public/checkout" class="block w-full bg-green-600 text-white text-center font-bold py-4 px-6 rounded-xl hover:bg-green-700 transition-all shadow-md hover:shadow-lg hover:-translate-y-0.5">
                            <?= $__('cart_proceed_checkout') ?>
                        </a>
                        
                        <a href="/sodai-dorkar/public/" class="block w-full text-center text-gray-500 font-medium mt-4 hover:text-green-600 transition-colors">
                            <?= $__('cart_continue_shopping') ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function updateCartQty(productId, newQty) {
    if (newQty < 0) newQty = 0;
    
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', newQty);
    
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (csrfMeta) formData.append('csrf_token', csrfMeta.getAttribute('content'));
    
    fetch((window.APP_BASE || '') + '/cart/update', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            if (newQty <= 0) {
                // Remove item row
                const row = document.getElementById('cart-item-' + productId);
                if (row) {
                    row.style.transition = 'all 0.3s ease';
                    row.style.opacity = '0';
                    row.style.transform = 'translateX(-20px)';
                    setTimeout(() => row.remove(), 300);
                }
                // If cart empty, reload
                if (data.cart_count === 0) {
                    setTimeout(() => location.reload(), 400);
                }
            } else {
                // Update quantity display
                const qtyEl = document.getElementById('qty-' + productId);
                if (qtyEl) qtyEl.textContent = data.quantity;
                
                // Update item total
                const itemTotalEl = document.getElementById('item-total-' + productId);
                if (itemTotalEl) itemTotalEl.textContent = '<?= $__('currency') ?>' + data.item_total.toFixed(2);
                
                // Update +/- buttons with new quantity
                const row = document.getElementById('cart-item-' + productId);
                if (row) {
                    const minusBtn = row.querySelector('button:first-child');
                    const plusBtn = row.querySelector('button:nth-child(3)');
                    if (minusBtn) minusBtn.setAttribute('onclick', 'updateCartQty(' + productId + ', ' + (data.quantity - 1) + ')');
                    if (plusBtn) plusBtn.setAttribute('onclick', 'updateCartQty(' + productId + ', ' + (data.quantity + 1) + ')');
                }
            }
            
            // Update global cart UI (sticky cart, header badge, drawer)
            if (typeof updateCartUI === 'function') {
                updateCartUI(data);
            }
            if (document.getElementById('summary-item-count')) {
                document.getElementById('summary-item-count').textContent = data.cart_count;
            }
            if (document.getElementById('summary-subtotal')) {
                document.getElementById('summary-subtotal').textContent = '<?= $__('currency') ?>' + data.subtotal.toFixed(2);
            }
            let currentDiscount = 0;
            if (data.spend_more_offers) {
                currentDiscount = parseFloat(data.spend_more_offers.discount_amount || 0);
                const discountRow = document.getElementById('cart-discount-row');
                const discountVal = document.getElementById('cart-summary-discount');
                if (discountRow) {
                    if (currentDiscount > 0) {
                        discountRow.classList.remove('hidden');
                        if (discountVal) discountVal.textContent = '- <?= $__('currency') ?>' + currentDiscount.toFixed(2);
                    } else {
                        discountRow.classList.add('hidden');
                    }
                }

                const giftRow = document.getElementById('cart-gift-row');
                const giftText = document.getElementById('cart-gift-text');
                if (giftRow && giftText) {
                    if (data.spend_more_offers.unlocked_gifts && data.spend_more_offers.unlocked_gifts.length > 0) {
                        giftRow.classList.remove('hidden');
                        giftRow.classList.add('flex');
                        giftText.textContent = data.spend_more_offers.unlocked_gifts.join(', ');
                    } else {
                        giftRow.classList.add('hidden');
                        giftRow.classList.remove('flex');
                    }
                }
            }

            if (document.getElementById('summary-total')) {
                const finalTotal = Math.max(0, data.subtotal - currentDiscount);
                document.getElementById('summary-total').textContent = '<?= $__('currency') ?>' + finalTotal.toFixed(2);
            }
            const deliveryEl = document.getElementById('cart-summary-delivery');
            if (deliveryEl && data.spend_more_offers) {
                if (data.spend_more_offers.is_free_delivery_unlocked) {
                    deliveryEl.innerHTML = '<span class="text-green-600 font-bold"><?= $__('currency') ?>0.00 <span class="text-[11px] bg-green-100 text-green-800 px-1.5 py-0.5 rounded font-bold uppercase"><?= Lang::locale() === 'bn' ? 'ফ্রি!' : 'FREE' ?></span></span>';
                } else {
                    deliveryEl.textContent = '<?= $__('cart_delivery_calc') ?>';
                }
            }
        }
    })
    .catch(() => showToast('<?= $__('toast_error') ?>', 'error'));
}

function removeFromCart(productId) {
    updateCartQty(productId, 0);
}
</script>

<?php 
$content = ob_get_clean();
require 'layout.php';
?>
