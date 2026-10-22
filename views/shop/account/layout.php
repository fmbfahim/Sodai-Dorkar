<?php
use Core\Lang;
Lang::init();
$__ = function($key, $r = []) { return Lang::get($key, $r); };
$locale = Lang::locale();

if (session_status() === PHP_SESSION_NONE) session_start();
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
$cart = $_SESSION['cart'] ?? [];
$cartCount = array_sum(array_map(function($i){ return (int)($i['quantity'] ?? 0); }, $cart));
$cartTotal = array_sum(array_map(function($i){ return (float)($i['price'] ?? 0) * (int)($i['quantity'] ?? 0); }, $cart));

$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$cleanPath = $base ? str_replace($base, '', $currentPath) : $currentPath;

if (!isset($isAgent)) {
    $isAgent = class_exists('\Core\Auth') ? \Core\Auth::isAgent() : false;
}
if (!isset($agentData) && $isAgent) {
    $agentData = class_exists('\Core\Auth') ? \Core\Auth::getCurrentAgentDashboardData() : null;
}

if (!isset($storeSettings)) {
    try {
        $storeSettings = class_exists('\Models\Setting') ? \Models\Setting::getAll() : [];
    } catch (\Throwable $e) {
        $storeSettings = [];
    }
}
$siteTitle = !empty($storeSettings['site_title']) ? $storeSettings['site_title'] : 'Fresh E mart';
$mshParts = explode(' ', $siteTitle, 2);
$mshPart1 = $mshParts[0] ?? 'Fresh';
$mshPart2 = $mshParts[1] ?? 'E mart';

$navLinks = [
    ['href' => $base . '/account', 'match' => '/account', 'icon' => 'grid-outline', 'label' => 'Dashboard'],
    ['href' => $base . '/account/orders', 'match' => '/account/orders', 'icon' => 'bag-handle-outline', 'label' => 'My Orders'],
    ['href' => $base . '/account/profile', 'match' => '/account/profile', 'icon' => 'person-outline', 'label' => 'Edit Profile'],
    ['href' => $base . '/account/change-password', 'match' => '/account/change-password', 'icon' => 'lock-closed-outline', 'label' => 'Change Password'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Customer Account') ?> — <?= htmlspecialchars($siteTitle) ?></title>
    <meta name="csrf-token" content="<?= \Core\CSRF::token() ?>">
    <script>
        window.APP_BASE = '<?= $base ?>';
        window.CSRF_TOKEN = '<?= \Core\CSRF::token() ?>';

        // Global fetch interceptor to guarantee CSRF token & AJAX headers are attached
        (function() {
            const originalFetch = window.fetch;
            window.fetch = async function(resource, init = {}) {
                init = init || {};
                const method = (init.method || 'GET').toUpperCase();
                if (['POST', 'PUT', 'DELETE', 'PATCH'].includes(method)) {
                    init.headers = init.headers || {};
                    const token = window.CSRF_TOKEN || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    if (token) {
                        if (init.headers instanceof Headers) {
                            if (!init.headers.has('X-CSRF-TOKEN')) init.headers.set('X-CSRF-TOKEN', token);
                            if (!init.headers.has('X-Requested-With')) init.headers.set('X-Requested-With', 'XMLHttpRequest');
                        } else if (Array.isArray(init.headers)) {
                            init.headers.push(['X-CSRF-TOKEN', token]);
                            init.headers.push(['X-Requested-With', 'XMLHttpRequest']);
                        } else {
                            if (!init.headers['X-CSRF-TOKEN']) init.headers['X-CSRF-TOKEN'] = token;
                            if (!init.headers['X-Requested-With']) init.headers['X-Requested-With'] = 'XMLHttpRequest';
                        }

                        if (typeof init.body === 'string') {
                            try {
                                const parsed = JSON.parse(init.body);
                                if (parsed && typeof parsed === 'object' && !Array.isArray(parsed) && !parsed.csrf_token) {
                                    parsed.csrf_token = token;
                                    init.body = JSON.stringify(parsed);
                                }
                            } catch (e) {}
                        } else if (init.body instanceof FormData) {
                            if (!init.body.has('csrf_token')) {
                                init.body.append('csrf_token', token);
                            }
                        }
                    }
                }

                const response = await originalFetch(resource, init);

                // Auto-refresh token and retry once if 419 Page Expired occurred
                if (response.status === 419 && !init._retried) {
                    try {
                        const cloned = response.clone();
                        const data = await cloned.json();
                        if (data && data.csrf_token) {
                            window.CSRF_TOKEN = data.csrf_token;
                            const meta = document.querySelector('meta[name="csrf-token"]');
                            if (meta) meta.setAttribute('content', data.csrf_token);

                            init._retried = true;
                            if (init.headers instanceof Headers) {
                                init.headers.set('X-CSRF-TOKEN', data.csrf_token);
                            } else if (!Array.isArray(init.headers)) {
                                init.headers['X-CSRF-TOKEN'] = data.csrf_token;
                            }
                            if (typeof init.body === 'string') {
                                try {
                                    const parsed = JSON.parse(init.body);
                                    if (parsed && typeof parsed === 'object') {
                                        parsed.csrf_token = data.csrf_token;
                                        init.body = JSON.stringify(parsed);
                                    }
                                } catch (e) {}
                            }
                            return originalFetch(resource, init);
                        }
                    } catch (e) {}
                }

                return response;
            };

            // Automatically inject CSRF token into all POST forms
            document.addEventListener('submit', function(e) {
                const form = e.target;
                if (form && form.tagName === 'FORM' && (form.method || '').toUpperCase() === 'POST') {
                    if (!form.querySelector('input[name="csrf_token"]')) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'csrf_token';
                        input.value = window.CSRF_TOKEN || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                        form.prepend(input);
                    }
                }
            }, true);
        })();
    </script>
    <link href="<?= $base ?>/css/output.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ["'Hind Siliguri'", "'Outfit'", 'sans-serif'],
                    }
                }
            }
        };
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        * { font-family: 'Hind Siliguri', 'Outfit', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important; }
        [x-cloak] { display: none !important; }
        .nav-link.active { background: rgba(5,150,105,0.1); color: #059669; font-weight: 700; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-gray-50/70 text-slate-800 min-h-screen flex flex-col font-sans antialiased">

<!-- Header -->
<header class="bg-white border-b border-gray-200/80 sticky top-0 z-40 shadow-2xs">
    <div class="max-w-6xl mx-auto px-4 py-3 sm:py-3.5 flex justify-between items-center">
        <a href="<?= $base ?>/" class="text-xl font-black text-gray-900 flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center shadow-xs">
                <ion-icon name="storefront" class="text-lg"></ion-icon>
            </div>
            <span><?= htmlspecialchars($mshPart1) ?> <span class="text-emerald-600"><?= htmlspecialchars($mshPart2) ?></span></span>
        </a>
        <div class="flex items-center gap-3 sm:gap-4">
            <?php if ($isAgent): ?>
                <a href="<?= $base ?>/agent/dashboard" class="text-xs sm:text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition-all flex items-center gap-1.5 px-3 py-1.5 rounded-lg shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                    <span>এজেন্ট হাব</span>
                    <ion-icon name="briefcase-outline" class="text-sm"></ion-icon>
                </a>
            <?php endif; ?>
            <a href="<?= $base ?>/" class="text-xs sm:text-sm font-semibold text-gray-600 hover:text-emerald-600 transition-colors flex items-center gap-1.5 px-3 py-1.5 rounded-lg hover:bg-gray-50 border border-gray-200/60">
                <ion-icon name="arrow-back-outline" class="text-base"></ion-icon>
                <span>Back to Shop</span>
            </a>
            <a href="<?= $base ?>/customer/logout" class="text-xs sm:text-sm font-bold text-rose-600 hover:text-rose-700 transition-colors flex items-center gap-1 px-3 py-1.5 rounded-lg hover:bg-rose-50 border border-rose-100">
                <ion-icon name="log-out-outline" class="text-base"></ion-icon>
                <span class="hidden sm:inline">Logout</span>
            </a>
        </div>
    </div>
</header>

<div class="max-w-6xl mx-auto px-4 py-5 sm:py-8 flex-grow w-full pb-24 md:pb-8">
    <div class="flex flex-col lg:flex-row gap-6">

        <!-- Mobile Profile & Scrollable Navigation Tab Bar -->
        <div class="lg:hidden space-y-3">
            <!-- User Card Mobile -->
            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-2xl p-4 text-white shadow-sm flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-full bg-white/20 border border-white/30 flex items-center justify-center text-xl font-black flex-shrink-0 shadow-inner">
                    <?= strtoupper(mb_substr($customer['name'] ?? 'U', 0, 1)) ?>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="font-black text-base truncate leading-tight"><?= htmlspecialchars($customer['name'] ?? 'Customer') ?></div>
                    <div class="text-emerald-100 text-xs truncate mt-0.5"><?= htmlspecialchars($customer['phone'] ?? '') ?></div>
                </div>
                <?php if ($isAgent): ?>
                    <span class="text-[10px] font-black uppercase tracking-wider bg-amber-400 text-gray-900 px-2.5 py-1 rounded-full border border-amber-300 shadow-xs flex items-center gap-1">
                        <ion-icon name="shield-checkmark" class="text-xs"></ion-icon>
                        এজেন্ট
                    </span>
                <?php else: ?>
                    <span class="text-[10px] font-bold uppercase tracking-wider bg-white/20 px-2 py-0.5 rounded-full border border-white/30">
                        Customer
                    </span>
                <?php endif; ?>
            </div>

            <!-- Horizontal Scrollable Tabs -->
            <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-1 bg-white p-2 rounded-xl border border-gray-200/80 shadow-2xs">
                <?php if ($isAgent): ?>
                    <a href="<?= $base ?>/account/agent/dashboard" 
                       class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-black whitespace-nowrap transition-all flex-shrink-0 <?= $cleanPath === '/account/agent/dashboard' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-emerald-50 text-emerald-800 border border-emerald-200' ?>">
                        <ion-icon name="speedometer-outline" class="text-sm"></ion-icon>
                        <span>এজেন্ট হাব</span>
                    </a>
                    <a href="<?= $base ?>/account/agent/shop" 
                       class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-bold whitespace-nowrap transition-all flex-shrink-0 <?= $cleanPath === '/account/agent/shop' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-emerald-50 text-emerald-800 border border-emerald-200' ?>">
                        <ion-icon name="storefront-outline" class="text-sm"></ion-icon>
                        <span>অর্ডার নিন</span>
                    </a>
                    <a href="<?= $base ?>/account/agent/checkout" 
                       class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-bold whitespace-nowrap transition-all flex-shrink-0 <?= $cleanPath === '/account/agent/checkout' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-emerald-50 text-emerald-800 border border-emerald-200' ?>">
                        <ion-icon name="cart-outline" class="text-sm"></ion-icon>
                        <span>চেকআউট</span>
                    </a>
                    <a href="<?= $base ?>/account/agent/orders" 
                       class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-bold whitespace-nowrap transition-all flex-shrink-0 <?= strpos($cleanPath, '/account/agent/order') === 0 ? 'bg-emerald-600 text-white shadow-xs' : 'bg-emerald-50 text-emerald-800 border border-emerald-200' ?>">
                        <ion-icon name="receipt-outline" class="text-sm"></ion-icon>
                        <span>এজেন্ট অর্ডার</span>
                    </a>
                    <a href="<?= $base ?>/account/agent/customers" 
                       class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-bold whitespace-nowrap transition-all flex-shrink-0 <?= $cleanPath === '/account/agent/customers' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-emerald-50 text-emerald-800 border border-emerald-200' ?>">
                        <ion-icon name="people-outline" class="text-sm"></ion-icon>
                        <span>কাস্টমার</span>
                    </a>
                <?php endif; ?>

                <?php foreach ($navLinks as $link): 
                    $isActive = ($cleanPath === $link['match']) || ($link['match'] === '/account/orders' && strpos($cleanPath, '/account/order-detail') === 0);
                ?>
                <a href="<?= $link['href'] ?>" 
                   class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-bold whitespace-nowrap transition-all flex-shrink-0 <?= $isActive ? 'bg-emerald-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' ?>">
                    <ion-icon name="<?= $link['icon'] ?>" class="text-sm"></ion-icon>
                    <span><?= $link['label'] ?></span>
                </a>
                <?php endforeach; ?>
                <a href="<?= $base ?>/customer/logout" 
                   class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-bold whitespace-nowrap transition-all flex-shrink-0 text-rose-600 hover:bg-rose-50">
                    <ion-icon name="log-out-outline" class="text-sm"></ion-icon>
                    <span>Logout</span>
                </a>
            </div>
        </div>

        <!-- Desktop Sidebar -->
        <aside class="hidden lg:block w-64 flex-shrink-0">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 overflow-hidden sticky top-20">
                <!-- Profile summary -->
                <div class="bg-gradient-to-br from-emerald-600 to-teal-700 p-5 text-white">
                    <div class="w-14 h-14 rounded-full bg-white/20 border border-white/30 flex items-center justify-center mb-3 text-2xl font-black shadow-inner">
                        <?= strtoupper(mb_substr($customer['name'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div class="font-black text-base leading-tight truncate"><?= htmlspecialchars($customer['name'] ?? '') ?></div>
                    <div class="text-emerald-100 text-xs mt-1 truncate"><?= htmlspecialchars($customer['phone'] ?? '') ?></div>
                    <?php if ($isAgent): ?>
                        <div class="mt-2.5 inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-400 text-gray-900 text-[11px] font-black shadow-xs">
                            <ion-icon name="shield-checkmark" class="text-xs"></ion-icon>
                            <span>ফিল্ড সেলস এজেন্ট (Agent)</span>
                        </div>
                    <?php else: ?>
                        <div class="mt-2 text-[11px] font-bold text-emerald-200">Customer Account</div>
                    <?php endif; ?>
                </div>

                <!-- Nav links -->
                <nav class="p-3 space-y-1">
                    <?php foreach ($navLinks as $link):
                        $isActive = ($cleanPath === $link['match']) || ($link['match'] === '/account/orders' && strpos($cleanPath, '/account/order-detail') === 0);
                    ?>
                    <a href="<?= $link['href'] ?>" class="nav-link <?= $isActive ? 'active' : '' ?> flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-50 hover:text-emerald-700 transition-all">
                        <ion-icon name="<?= $link['icon'] ?>" class="text-base flex-shrink-0"></ion-icon>
                        <?= $link['label'] ?>
                    </a>
                    <?php endforeach; ?>

                    <?php if ($isAgent): ?>
                    <div class="pt-3 pb-1 border-t border-emerald-100 my-2">
                        <div class="px-3.5 pb-2 flex items-center justify-between text-[11px] font-black uppercase tracking-wider text-emerald-800">
                            <span class="flex items-center gap-1.5">
                                <ion-icon name="briefcase" class="text-emerald-600 text-sm"></ion-icon>
                                <span>ফিল্ড এজেন্ট অপশন</span>
                            </span>
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        </div>
                        <div class="space-y-1">
                            <a href="<?= $base ?>/account/agent/dashboard" class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all <?= $cleanPath === '/account/agent/dashboard' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200/60 shadow-2xs' ?>">
                                <ion-icon name="speedometer-outline" class="text-base shrink-0 <?= $cleanPath === '/account/agent/dashboard' ? 'text-white' : 'text-emerald-600' ?>"></ion-icon>
                                <span>এজেন্ট ড্যাশবোর্ড ও রিপোর্ট</span>
                            </a>
                            <a href="<?= $base ?>/account/agent/shop" class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all <?= $cleanPath === '/account/agent/shop' ? 'bg-emerald-600 text-white shadow-xs' : 'text-gray-700 hover:text-emerald-700 hover:bg-gray-50' ?>">
                                <ion-icon name="storefront-outline" class="text-base shrink-0 <?= $cleanPath === '/account/agent/shop' ? 'text-white' : 'text-emerald-600' ?>"></ion-icon>
                                <span>পণ্য ক্যাটালগ ও অর্ডার নিন</span>
                            </a>
                            <a href="<?= $base ?>/account/agent/checkout" class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all <?= $cleanPath === '/account/agent/checkout' ? 'bg-emerald-600 text-white shadow-xs' : 'text-gray-700 hover:text-emerald-700 hover:bg-gray-50' ?>">
                                <ion-icon name="cart-outline" class="text-base shrink-0 <?= $cleanPath === '/account/agent/checkout' ? 'text-white' : 'text-emerald-600' ?>"></ion-icon>
                                <span>কাস্টমার চেকআউট</span>
                            </a>
                            <a href="<?= $base ?>/account/agent/orders" class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all <?= strpos($cleanPath, '/account/agent/order') === 0 ? 'bg-emerald-600 text-white shadow-xs' : 'text-gray-700 hover:text-emerald-700 hover:bg-gray-50' ?>">
                                <ion-icon name="receipt-outline" class="text-base shrink-0 <?= strpos($cleanPath, '/account/agent/order') === 0 ? 'text-white' : 'text-emerald-600' ?>"></ion-icon>
                                <span>সংগৃহীত অর্ডারসমূহ</span>
                            </a>
                            <a href="<?= $base ?>/account/agent/customers" class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all <?= $cleanPath === '/account/agent/customers' ? 'bg-emerald-600 text-white shadow-xs' : 'text-gray-700 hover:text-emerald-700 hover:bg-gray-50' ?>">
                                <ion-icon name="people-outline" class="text-base shrink-0 <?= $cleanPath === '/account/agent/customers' ? 'text-white' : 'text-emerald-600' ?>"></ion-icon>
                                <span>এলাকার কাস্টমার তালিকা</span>
                            </a>
                            <a href="<?= $base ?>/agent/dashboard" target="_blank" class="flex items-center justify-between px-3.5 py-2 rounded-xl text-[11px] font-semibold text-gray-500 hover:text-emerald-700 hover:bg-gray-50 transition-all border-t border-gray-100 mt-1 pt-1.5">
                                <span class="flex items-center gap-1.5">
                                    <ion-icon name="phone-portrait-outline" class="text-sm"></ion-icon>
                                    <span>পূর্ণাঙ্গ মোবাইল অ্যাপ ভিউ</span>
                                </span>
                                <ion-icon name="open-outline" class="text-xs"></ion-icon>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="border-t border-gray-100 mt-2 pt-2">
                        <a href="<?= $base ?>/customer/logout" class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-rose-500 hover:bg-rose-50 transition-all">
                            <ion-icon name="log-out-outline" class="text-base flex-shrink-0"></ion-icon>
                            Logout
                        </a>
                    </div>
                </nav>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 min-w-0">
            <?= $content ?? '' ?>
        </main>
    </div>
</div>

<!-- Mobile Bottom Navigation Bar (Category, Shop, Profile) -->
<nav id="mobile-bottom-nav" class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-t border-gray-200/90 shadow-[0_-4px_25px_rgba(0,0,0,0.08)] px-3 py-1.5 transition-all">
    <div class="flex items-center justify-around max-w-md mx-auto">
        
        <!-- Category Tab -->
        <a href="<?= $base ?>/category" class="flex flex-col items-center justify-center flex-1 py-1 text-center transition-all group text-gray-500 hover:text-emerald-600">
            <div class="w-6 h-6 flex items-center justify-center relative mb-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 stroke-2 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
            </div>
            <span class="text-[11px] leading-tight tracking-tight">Category</span>
        </a>

        <!-- Shop Tab -->
        <a href="<?= $base ?>/shop" class="flex flex-col items-center justify-center flex-1 py-1 text-center transition-all group text-gray-500 hover:text-emerald-600">
            <div class="w-6 h-6 flex items-center justify-center relative mb-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 stroke-2 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </div>
            <span class="text-[11px] leading-tight tracking-tight">Shop</span>
        </a>

        <!-- Profile Tab (Active) -->
        <a href="<?= $base ?>/account" class="flex flex-col items-center justify-center flex-1 py-1 text-center transition-all group text-emerald-600 font-bold">
            <div class="w-6 h-6 flex items-center justify-center relative mb-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 stroke-[2.5] transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <span class="text-[11px] leading-tight tracking-tight">Profile</span>
        </a>

    </div>
</nav>

<!-- Global Toast Notification Container for Agent Operations -->
<div id="agentToastContainer" class="fixed top-14 left-1/2 -translate-x-1/2 z-50 w-full max-w-sm px-4 pointer-events-none"></div>

<script>
    window.showAgentToast = function(msg, type = 'success') {
        const container = document.getElementById('agentToastContainer');
        if (!container) return;
        const toast = document.createElement('div');
        const bg = type === 'success' ? 'bg-emerald-700 text-white' : 'bg-rose-700 text-white';
        toast.className = `${bg} px-4 py-2.5 rounded-2xl shadow-xl text-xs font-bold flex items-center justify-between pointer-events-auto transform transition-all duration-300 opacity-0 translate-y-2 mb-2`;
        toast.innerHTML = `<span>${msg}</span><button onclick="this.parentElement.remove()" class="ml-2 text-white/80 hover:text-white">&times;</button>`;
        container.appendChild(toast);
        setTimeout(() => toast.classList.remove('opacity-0', 'translate-y-2'), 10);
        setTimeout(() => {
            toast.classList.add('opacity-0', '-translate-y-2');
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    };
</script>

</body>
</html>
