<?php
use Core\Lang;
Lang::init();
$__ = function($key, $r = []) { return Lang::get($key, $r); };
$locale = Lang::locale();

if (session_status() === PHP_SESSION_NONE) session_start();
$cart = $_SESSION['cart'] ?? [];
$cartCount = array_sum(array_map(function($i){ return (int)($i['quantity'] ?? 0); }, $cart));
$cartTotal = array_sum(array_map(function($i){ return (float)($i['price'] ?? 0) * (int)($i['quantity'] ?? 0); }, $cart));
?>
<!DOCTYPE html>
<html lang="<?= $locale ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'My Account') ?> — <?= $__('site_name') ?></title>
    <meta name="csrf-token" content="<?= \Core\CSRF::token() ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Noto+Sans+Bengali:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <style>
        body { font-family: 'Outfit', 'Noto Sans Bengali', sans-serif; }
        .nav-link.active { background: rgba(5,150,105,0.1); color: #059669; font-weight: 700; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- Header -->
<header class="bg-white shadow-sm sticky top-0 z-40">
    <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
        <a href="/sodai-dorkar/public/" class="text-xl font-black text-emerald-600 flex items-center gap-2">
            <ion-icon name="storefront" class="text-2xl"></ion-icon>
            <?= $__('site_name') ?>
        </a>
        <div class="flex items-center gap-4">
            <a href="/sodai-dorkar/public/" class="text-sm text-gray-500 hover:text-emerald-600 transition-colors">
                <ion-icon name="home-outline" class="text-base align-middle"></ion-icon> Shop
            </a>
            <a href="/sodai-dorkar/public/customer/logout" class="text-sm font-semibold text-red-500 hover:text-red-700 transition-colors flex items-center gap-1">
                <ion-icon name="log-out-outline" class="text-base"></ion-icon> Logout
            </a>
        </div>
    </div>
</header>

<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex gap-6">

        <!-- Sidebar -->
        <aside class="w-64 flex-shrink-0">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <!-- Profile summary -->
                <div class="bg-gradient-to-br from-emerald-500 to-teal-600 p-5 text-white">
                    <div class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center mb-3 text-2xl font-black">
                        <?= strtoupper(mb_substr($customer['name'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div class="font-bold text-base leading-tight"><?= htmlspecialchars($customer['name'] ?? '') ?></div>
                    <div class="text-emerald-100 text-xs mt-0.5"><?= htmlspecialchars($customer['phone'] ?? '') ?></div>
                </div>

                <!-- Nav links -->
                <nav class="p-3 space-y-1">
                    <?php
                    $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                    $navLinks = [
                        ['href' => '/sodai-dorkar/public/account', 'icon' => 'grid-outline', 'label' => 'Dashboard'],
                        ['href' => '/sodai-dorkar/public/account/orders', 'icon' => 'bag-handle-outline', 'label' => 'My Orders'],
                        ['href' => '/sodai-dorkar/public/account/profile', 'icon' => 'person-outline', 'label' => 'Edit Profile'],
                        ['href' => '/sodai-dorkar/public/account/change-password', 'icon' => 'lock-closed-outline', 'label' => 'Change Password'],
                    ];
                    foreach ($navLinks as $link):
                        $isActive = str_replace('/sodai-dorkar/public', '', $currentPath) === str_replace('/sodai-dorkar/public', '', $link['href']);
                    ?>
                    <a href="<?= $link['href'] ?>" class="nav-link <?= $isActive ? 'active' : '' ?> flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm text-gray-600 hover:bg-gray-50 hover:text-emerald-700 transition-all">
                        <ion-icon name="<?= $link['icon'] ?>" class="text-base flex-shrink-0"></ion-icon>
                        <?= $link['label'] ?>
                    </a>
                    <?php endforeach; ?>
                    <div class="border-t border-gray-100 mt-2 pt-2">
                        <a href="/sodai-dorkar/public/customer/logout" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm text-red-500 hover:bg-red-50 transition-all">
                            <ion-icon name="log-out-outline" class="text-base flex-shrink-0"></ion-icon>
                            Logout
                        </a>
                    </div>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 min-w-0">
            <?= $content ?? '' ?>
        </main>
    </div>
</div>

</body>
</html>
