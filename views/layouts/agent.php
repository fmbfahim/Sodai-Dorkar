<?php
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
$agentSiteTitle = class_exists('\Models\Setting') ? \Models\Setting::getValue('site_title', 'Fresh E mart') : 'Fresh E mart';
$agentName = $_SESSION['name'] ?? 'Agent';
$agentUsername = $_SESSION['username'] ?? '';
$agentCartCount = 0;
if (!empty($_SESSION['agent_cart'])) {
    foreach ($_SESSION['agent_cart'] as $it) {
        $agentCartCount += (int)($it['quantity'] ?? 1);
    }
}

$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
$isActive = function($segment) use ($currentPath) {
    return strpos($currentPath, $segment) !== false;
};
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= htmlspecialchars($title ?? 'ফিল্ড এজেন্ট পোর্টাল') ?> - <?= htmlspecialchars($agentSiteTitle) ?></title>
    
    <!-- Tailwind CSS -->
    <link href="<?= $base ?>/css/output.css" rel="stylesheet">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body, button, input, select, textarea { font-family: 'Hind Siliguri', 'Outfit', sans-serif; }
        [x-cloak] { display: none !important; }
        .pb-safe { padding-bottom: calc(5rem + env(safe-area-inset-bottom, 0px)); }
        .active-tab-glow {
            box-shadow: 0 4px 15px -3px rgba(16, 185, 129, 0.25);
        }
    </style>

    <meta name="theme-color" content="#15803d">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50/70 font-sans text-secondary-900 min-h-screen flex flex-col antialiased selection:bg-emerald-500 selection:text-white">

    <!-- Top AppBar (Mobile Responsive Header) -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-gray-200/80 shadow-xs">
        <div class="max-w-2xl mx-auto px-4 py-2.5 flex items-center justify-between">
            <!-- Brand & Agent Badge -->
            <div class="flex items-center gap-2.5">
                <a href="<?= $base ?>/agent/dashboard" class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-2xl bg-gradient-to-tr from-emerald-600 to-green-500 text-white flex items-center justify-center font-black shadow-md shadow-emerald-500/20 text-lg">
                        <ion-icon name="bag-handle"></ion-icon>
                    </div>
                    <div>
                        <div class="text-sm font-black text-gray-900 tracking-tight leading-none"><?= htmlspecialchars($agentSiteTitle) ?></div>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">ফিল্ড এজেন্ট পোর্টাল</span>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Header Right Actions -->
            <div class="flex items-center gap-2">
                <!-- Cart Button -->
                <a href="<?= $base ?>/agent/checkout" class="relative p-2 rounded-2xl bg-gray-100 hover:bg-emerald-50 text-gray-700 hover:text-emerald-700 transition-colors" title="কার্ট">
                    <ion-icon name="cart-outline" class="text-xl"></ion-icon>
                    <?php if ($agentCartCount > 0): ?>
                        <span class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-emerald-600 text-white font-black text-[10px] flex items-center justify-center shadow-xs animate-bounce">
                            <?= $agentCartCount ?>
                        </span>
                    <?php endif; ?>
                </a>

                <!-- Agent Profile Link -->
                <a href="<?= $base ?>/agent/profile" class="flex items-center gap-1.5 pl-1.5 pr-2.5 py-1 rounded-2xl bg-gray-100 hover:bg-gray-200/80 text-gray-800 transition-colors">
                    <div class="w-6 h-6 rounded-full bg-emerald-600 text-white text-[11px] font-bold flex items-center justify-center">
                        <?= strtoupper(mb_substr($agentName, 0, 1)) ?>
                    </div>
                    <span class="text-xs font-bold hidden sm:inline-block max-w-[90px] truncate"><?= htmlspecialchars($agentName) ?></span>
                </a>

                <!-- Logout -->
                <a href="<?= $base ?>/logout" class="p-2 text-gray-400 hover:text-red-600 transition-colors" title="লগআউট">
                    <ion-icon name="log-out-outline" class="text-lg"></ion-icon>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Mobile Content Area (Constrained on desktop to mobile/tablet width for ultimate app feel) -->
    <main class="flex-1 max-w-2xl w-full mx-auto px-4 py-4 pb-safe">
        <?= $content ?? '' ?>
    </main>

    <!-- Bottom App Navigation (Fixed Bar) -->
    <nav class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-gray-200/80 shadow-[0_-4px_20px_rgba(0,0,0,0.05)]">
        <div class="max-w-2xl mx-auto flex items-center justify-around py-1.5 px-2">
            <!-- 1. Dashboard -->
            <a href="<?= $base ?>/agent/dashboard" class="flex flex-col items-center py-1 px-3 rounded-2xl transition-all <?= $isActive('/agent/dashboard') ? 'text-emerald-700 font-bold scale-105' : 'text-gray-500 hover:text-emerald-600' ?>">
                <ion-icon name="<?= $isActive('/agent/dashboard') ? 'grid' : 'grid-outline' ?>" class="text-xl mb-0.5"></ion-icon>
                <span class="text-[10px] tracking-tight">ড্যাশবোর্ড</span>
            </a>

            <!-- 2. Take Order / Shop (Main Action Highlighted) -->
            <a href="<?= $base ?>/agent/shop" class="flex flex-col items-center py-1 px-3 rounded-2xl transition-all <?= $isActive('/agent/shop') ? 'text-emerald-700 font-bold scale-105' : 'text-gray-500 hover:text-emerald-600' ?>">
                <div class="relative">
                    <ion-icon name="<?= $isActive('/agent/shop') ? 'storefront' : 'storefront-outline' ?>" class="text-xl mb-0.5"></ion-icon>
                    <?php if ($agentCartCount > 0): ?>
                        <span class="absolute -top-1 -right-2 w-2 h-2 rounded-full bg-emerald-500"></span>
                    <?php endif; ?>
                </div>
                <span class="text-[10px] tracking-tight">অর্ডার নিন</span>
            </a>

            <!-- 3. Checkout (Quick Access) -->
            <a href="<?= $base ?>/agent/checkout" class="flex flex-col items-center py-1 px-3 rounded-2xl transition-all <?= $isActive('/agent/checkout') ? 'text-emerald-700 font-bold scale-105' : 'text-gray-500 hover:text-emerald-600' ?>">
                <div class="relative">
                    <ion-icon name="<?= $isActive('/agent/checkout') ? 'cart' : 'cart-outline' ?>" class="text-xl mb-0.5"></ion-icon>
                    <?php if ($agentCartCount > 0): ?>
                        <span class="absolute -top-1.5 -right-2.5 px-1 min-w-[14px] h-3.5 rounded-full bg-emerald-600 text-white text-[9px] font-black flex items-center justify-center">
                            <?= $agentCartCount ?>
                        </span>
                    <?php endif; ?>
                </div>
                <span class="text-[10px] tracking-tight">চেকআউট</span>
            </a>

            <!-- 4. My Orders -->
            <a href="<?= $base ?>/agent/orders" class="flex flex-col items-center py-1 px-3 rounded-2xl transition-all <?= $isActive('/agent/orders') ? 'text-emerald-700 font-bold scale-105' : 'text-gray-500 hover:text-emerald-600' ?>">
                <ion-icon name="<?= $isActive('/agent/orders') ? 'receipt' : 'receipt-outline' ?>" class="text-xl mb-0.5"></ion-icon>
                <span class="text-[10px] tracking-tight">আমার অর্ডার</span>
            </a>

            <!-- 5. Customers -->
            <a href="<?= $base ?>/agent/customers" class="flex flex-col items-center py-1 px-3 rounded-2xl transition-all <?= $isActive('/agent/customers') ? 'text-emerald-700 font-bold scale-105' : 'text-gray-500 hover:text-emerald-600' ?>">
                <ion-icon name="<?= $isActive('/agent/customers') ? 'people' : 'people-outline' ?>" class="text-xl mb-0.5"></ion-icon>
                <span class="text-[10px] tracking-tight">কাস্টমার</span>
            </a>
        </div>
    </nav>

    <!-- Global Flash Notification Toast Container (if triggered via JS) -->
    <div id="agentToastContainer" class="fixed top-14 left-1/2 -translate-x-1/2 z-50 w-full max-w-sm px-4 pointer-events-none"></div>

    <script>
        window.showAgentToast = function(msg, type = 'success') {
            const container = document.getElementById('agentToastContainer');
            if (!container) return;
            const toast = document.createElement('div');
            const bg = type === 'success' ? 'bg-emerald-700 text-white' : 'bg-red-700 text-white';
            toast.className = `${bg} px-4 py-2.5 rounded-2xl shadow-xl text-xs font-bold flex items-center justify-between pointer-events-auto transform transition-all duration-300 opacity-0 translate-y-2 mb-2`;
            toast.innerHTML = `<span>${msg}</span><button onclick="this.parentElement.remove()" class="ml-2 text-white/80 hover:text-white">&times;</button>`;
            container.appendChild(toast);
            setTimeout(() => {
                toast.classList.remove('opacity-0', 'translate-y-2');
            }, 10);
            setTimeout(() => {
                toast.classList.add('opacity-0', '-translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        };
    </script>
</body>
</html>
