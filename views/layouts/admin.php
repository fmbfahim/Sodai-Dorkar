<?php
$base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
$currentRole = $_SESSION['role'] ?? 'admin';
$currentUserName = $_SESSION['name'] ?? 'Admin';
$roleTitles = [
    'admin' => 'Super Admin',
    'manager' => 'Store Manager',
    'accountant' => 'Accountant',
    'staff' => 'General Staff',
    'agent' => 'Customer Agent',
    'delivery_man' => 'Delivery Rider'
];
$displayRole = $roleTitles[$currentRole] ?? ucfirst($currentRole);
$adminSiteTitle = class_exists('\Models\Setting') ? \Models\Setting::getValue('site_title', 'Fresh E mart') : 'Fresh E mart';

// Current active menu detection
$reqUri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
$currentPath = $reqUri;
if (!empty($base) && strpos($currentPath, $base) === 0) {
    $currentPath = substr($currentPath, strlen($base));
}
$currentPath = '/' . ltrim($currentPath, '/');
$currentPath = rtrim($currentPath, '/') ?: '/';

$isMenuActive = function($path, $exact = false) use ($currentPath) {
    if ($exact) {
        return $currentPath === $path;
    }
    return $currentPath === $path || strpos($currentPath, $path . '/') === 0;
};

// Top Header Active Menu Badge Label
$activeNavLabel = 'Dashboard';
if ($isMenuActive('/admin/orders/create', true)) {
    $activeNavLabel = 'New Order (POS)';
} elseif ($isMenuActive('/admin/orders/incomplete', true)) {
    $activeNavLabel = 'Incomplete Orders';
} elseif ($isMenuActive('/admin/orders/packaging', true)) {
    $activeNavLabel = 'Packaging List';
} elseif ($isMenuActive('/admin/orders/returns', true)) {
    $activeNavLabel = 'Returns & Damages';
} elseif ($isMenuActive('/admin/orders', true)) {
    $activeNavLabel = 'Orders (অর্ডার)';
} elseif ($isMenuActive('/admin/products/dashboard', true)) {
    $activeNavLabel = 'Product Dashboard';
} elseif ($isMenuActive('/admin/products/bulk-import', true)) {
    $activeNavLabel = 'Bulk Import';
} elseif ($isMenuActive('/admin/products/image-finder', true)) {
    $activeNavLabel = 'Auto Image Finder';
} elseif ($isMenuActive('/admin/products/shwapno-importer', true)) {
    $activeNavLabel = 'Shwapno Scraper';
} elseif ($isMenuActive('/admin/products/verification', true)) {
    $activeNavLabel = 'Verification';
} elseif ($isMenuActive('/admin/products/availability', true)) {
    $activeNavLabel = 'Availability';
} elseif ($isMenuActive('/admin/products/procurement', true)) {
    $activeNavLabel = 'Procurement List';
} elseif ($isMenuActive('/admin/products', false)) {
    $activeNavLabel = 'Products (পণ্যসমূহ)';
} elseif ($isMenuActive('/admin/categories', false)) {
    $activeNavLabel = 'Categories (ক্যাটাগরি)';
} elseif ($isMenuActive('/admin/brands', false)) {
    $activeNavLabel = 'Brands (ব্র্যান্ড)';
} elseif ($isMenuActive('/admin/warehouses', false)) {
    $activeNavLabel = 'Warehouses';
} elseif ($isMenuActive('/admin/areas', false)) {
    $activeNavLabel = 'Unions (Areas)';
} elseif ($isMenuActive('/admin/zones', false)) {
    $activeNavLabel = 'Zones';
} elseif ($isMenuActive('/admin/points', false)) {
    $activeNavLabel = 'Points';
} elseif ($isMenuActive('/admin/vendors', false)) {
    $activeNavLabel = 'Suppliers';
} elseif ($isMenuActive('/admin/purchases', false)) {
    $activeNavLabel = 'Stock In (Purchases)';
} elseif ($isMenuActive('/admin/customers', false)) {
    $activeNavLabel = 'Customers';
} elseif ($isMenuActive('/admin/delivery-men', false)) {
    $activeNavLabel = 'Delivery Riders';
} elseif ($isMenuActive('/admin/dispatch', false)) {
    $activeNavLabel = 'Dispatch Board';
} elseif ($isMenuActive('/admin/reports/visitors', true)) {
    $activeNavLabel = 'Visitor Report';
} elseif ($isMenuActive('/admin/reports/sales', true)) {
    $activeNavLabel = 'Sales Report';
} elseif ($isMenuActive('/admin/reports/stock', true)) {
    $activeNavLabel = 'Stock Alert';
} elseif ($isMenuActive('/admin/accounts', false)) {
    $activeNavLabel = 'Accounts Ledger';
} elseif ($isMenuActive('/admin/hr', false) || $isMenuActive('/admin/payroll', false)) {
    $activeNavLabel = 'HR & Payroll';
} elseif ($isMenuActive('/admin/users', false)) {
    $activeNavLabel = 'User Access Control';
} elseif ($isMenuActive('/admin/ecommerce-settings', false)) {
    $activeNavLabel = 'E-Commerce Config';
} elseif ($isMenuActive('/admin/settings', false)) {
    $activeNavLabel = 'Settings';
} elseif ($isMenuActive('/admin/profile', false)) {
    $activeNavLabel = 'Profile';
}

$navItemClass = function($path, $exact = false, $isSub = false) use ($isMenuActive) {
    $active = $isMenuActive($path, $exact);
    if ($isSub) {
        return $active 
            ? 'flex items-center px-4 py-1.5 text-xs rounded-xl bg-primary-100 text-primary-800 font-bold border-l-4 border-primary-600 pl-7 transition-colors group shadow-2xs' 
            : 'flex items-center px-4 py-1.5 text-xs rounded-xl text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group pl-8 font-medium';
    }
    return $active
        ? 'flex items-center px-4 py-2 rounded-xl bg-primary-50 text-primary-700 font-bold border-l-4 border-primary-600 shadow-2xs transition-colors group text-sm'
        : 'flex items-center px-4 py-2 rounded-xl text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group text-sm font-medium';
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - <?= htmlspecialchars($adminSiteTitle) ?></title>
    <link href="<?= $base ?>/css/output.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body, button, input, select, textarea { font-family: 'Hind Siliguri', 'Outfit', sans-serif; }
    </style>
    <!-- Ionicons for icons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body class="bg-secondary-50 font-sans text-secondary-900 flex min-h-screen">

    <!-- Mobile Sidebar Backdrop -->
    <div id="sidebarBackdrop" class="fixed inset-0 bg-secondary-900/50 z-40 hidden lg:hidden transition-opacity"></div>

    <!-- Sidebar -->
    <aside id="adminSidebar" class="w-64 bg-white border-r border-secondary-200 flex flex-col fixed h-full z-50 transition-transform duration-300 transform -translate-x-full lg:translate-x-0">
        <div class="p-5 flex items-center justify-between border-b border-secondary-100">
            <div>
                <a href="<?= $base ?>/admin/dashboard" class="text-2xl font-bold text-primary-600 tracking-tight block"><?= htmlspecialchars($adminSiteTitle) ?></a>
                <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full bg-primary-50 text-primary-700 inline-block mt-0.5">
                    <?= htmlspecialchars($displayRole) ?>
                </span>
            </div>
            <a href="<?= $base ?>/" target="_blank" title="View Storefront" class="p-1.5 text-secondary-400 hover:text-primary-600 rounded-lg hover:bg-secondary-50 transition-colors">
                <ion-icon name="open-outline" class="text-lg"></ion-icon>
            </a>
        </div>

        <nav class="flex-1 overflow-y-auto py-4">
            <ul class="space-y-1 px-3">
                <?php if (\Core\Auth::can('dashboard')): ?>
                <li>
                    <a href="<?= $base ?>/admin/dashboard" class="<?= $isMenuActive('/admin/dashboard', true) ? 'flex items-center px-4 py-2.5 rounded-xl bg-primary-50 text-primary-700 font-bold border-l-4 border-primary-600 shadow-2xs transition-colors group text-sm' : 'flex items-center px-4 py-2.5 rounded-xl text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group text-sm font-medium' ?>">
                        <ion-icon name="grid-outline" class="text-xl mr-3 group-hover:text-primary-600"></ion-icon>
                        <span class="font-medium">Dashboard</span>
                    </a>
                </li>
                <?php endif; ?>
                
                <!-- Master Data Section -->
                <?php if (\Core\Auth::can('locations') || \Core\Auth::can('products') || \Core\Auth::can('categories_brands') || \Core\Auth::can('vendors_purchases')): ?>
                    <li class="px-4 pt-4 pb-2 text-[11px] font-bold text-secondary-400 uppercase tracking-wider">Master Data</li>
                    
                    <?php if (\Core\Auth::can('locations')): ?>
                        <li>
                            <a href="<?= $base ?>/admin/warehouses" class="<?= $navItemClass('/admin/warehouses', false) ?>">
                                <ion-icon name="business-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Warehouses</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $base ?>/admin/areas" class="<?= $navItemClass('/admin/areas', false) ?>">
                                <ion-icon name="map-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Unions (Areas)</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $base ?>/admin/zones" class="<?= $navItemClass('/admin/zones', false, true) ?>">
                                <ion-icon name="navigate-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Zones</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $base ?>/admin/points" class="<?= $navItemClass('/admin/points', false, true) ?>">
                                <ion-icon name="location-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Points</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (\Core\Auth::can('vendors_purchases')): ?>
                        <li>
                            <a href="<?= $base ?>/admin/vendors" class="<?= $navItemClass('/admin/vendors', false) ?>">
                                <ion-icon name="storefront-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Suppliers</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $base ?>/admin/purchases" class="<?= $navItemClass('/admin/purchases', false) ?>">
                                <ion-icon name="receipt-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Stock In (Purchases)</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (\Core\Auth::can('products')): ?>
                        <li>
                            <a href="<?= $base ?>/admin/products/dashboard" class="<?= $navItemClass('/admin/products/dashboard', true) ?>">
                                <ion-icon name="grid-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Product Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $base ?>/admin/products" class="<?= $navItemClass('/admin/products', true) ?>">
                                <ion-icon name="cube-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">All Products</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $base ?>/admin/products/bulk-import" class="<?= $navItemClass('/admin/products/bulk-import', true, true) ?>">
                                <ion-icon name="cloud-upload-outline" class="text-base mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Bulk Import (CSV)</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $base ?>/admin/products/image-finder" class="<?= $navItemClass('/admin/products/image-finder', true, true) ?> justify-between">
                                <div class="flex items-center">
                                    <ion-icon name="sparkles-outline" class="text-base mr-3 text-amber-500 group-hover:text-emerald-600"></ion-icon>
                                    <span class="font-medium">Auto Image Finder</span>
                                </div>
                                <span class="px-1.5 py-0.5 rounded-md bg-amber-100 text-amber-800 text-[10px] font-bold">1-Click</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $base ?>/admin/products/shwapno-importer" class="<?= $navItemClass('/admin/products/shwapno-importer', true, true) ?> justify-between">
                                <div class="flex items-center">
                                    <ion-icon name="cloud-download-outline" class="text-base mr-3 text-teal-600 group-hover:text-teal-700"></ion-icon>
                                    <span class="font-medium">Shwapno Scraper</span>
                                </div>
                                <span class="px-1.5 py-0.5 rounded-md bg-teal-100 text-teal-800 text-[10px] font-bold">Auto</span>
                            </a>
                        </li>

                        <li>
                            <a href="<?= $base ?>/admin/products/verification" class="<?= $navItemClass('/admin/products/verification', true, true) ?>">
                                <ion-icon name="checkmark-done-outline" class="text-base mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Verification</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $base ?>/admin/products/availability" class="<?= $navItemClass('/admin/products/availability', true, true) ?>">
                                <ion-icon name="flash-outline" class="text-base mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Availability</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $base ?>/admin/products/procurement" class="<?= $navItemClass('/admin/products/procurement', true, true) ?>">
                                <ion-icon name="cart-outline" class="text-base mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Procurement List</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (\Core\Auth::can('categories_brands')): ?>
                        <li>
                            <a href="<?= $base ?>/admin/categories" class="<?= $navItemClass('/admin/categories', false) ?>">
                                <ion-icon name="folder-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Categories</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $base ?>/admin/brands" class="<?= $navItemClass('/admin/brands', false) ?>">
                                <ion-icon name="pricetag-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Brands</span>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Operations Section -->
                <?php if (\Core\Auth::can('customers') || \Core\Auth::can('delivery_men') || \Core\Auth::can('orders') || \Core\Auth::can('dispatch')): ?>
                    <li class="px-4 pt-4 pb-2 text-[11px] font-bold text-secondary-400 uppercase tracking-wider">Operations</li>

                    <?php if (\Core\Auth::can('customers')): ?>
                        <li>
                            <a href="<?= $base ?>/admin/customers" class="<?= $navItemClass('/admin/customers', false) ?>">
                                <ion-icon name="people-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Customers</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (\Core\Auth::can('delivery_men')): ?>
                        <li>
                            <a href="<?= $base ?>/admin/delivery-men" class="<?= $navItemClass('/admin/delivery-men', false) ?>">
                                <ion-icon name="bicycle-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Delivery Men</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $base ?>/admin/delivery-men/allocation" class="<?= $navItemClass('/admin/delivery-men/allocation', true, true) ?>">
                                <ion-icon name="calendar-outline" class="text-base mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Area Allocations</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (\Core\Auth::can('orders')): ?>
                        <li>
                            <a href="<?= $base ?>/admin/orders" class="<?= $navItemClass('/admin/orders', true) ?>">
                                <ion-icon name="cart-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Orders</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $base ?>/admin/orders/create" class="<?= $navItemClass('/admin/orders/create', true, true) ?> font-semibold text-primary-700">
                                <ion-icon name="add-circle-outline" class="text-base mr-3 text-primary-600"></ion-icon>
                                <span>New Order (নতুন অর্ডার)</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $base ?>/admin/orders/incomplete" class="<?= $navItemClass('/admin/orders/incomplete', true, true) ?> justify-between text-amber-700 font-semibold">
                                <div class="flex items-center">
                                    <ion-icon name="alert-circle-outline" class="text-base mr-3 text-amber-500"></ion-icon>
                                    <span>Incomplete Orders</span>
                                </div>
                                <span class="px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-black">Live</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $base ?>/admin/orders/packaging" class="<?= $navItemClass('/admin/orders/packaging', true, true) ?>">
                                <ion-icon name="cube-outline" class="text-base mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Packaging List</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $base ?>/admin/orders/returns" class="<?= $navItemClass('/admin/orders/returns', true, true) ?>">
                                <ion-icon name="sync-circle-outline" class="text-base mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Returns & Damages</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (\Core\Auth::can('dispatch')): ?>
                        <li>
                            <a href="<?= $base ?>/admin/dispatch" class="<?= $navItemClass('/admin/dispatch', false) ?>">
                                <ion-icon name="paper-plane-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Dispatch Board</span>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Reports Section -->
                <?php if (\Core\Auth::can('reports')): ?>
                    <li class="px-4 pt-4 pb-2 text-[11px] font-bold text-secondary-400 uppercase tracking-wider">Reports & Analytics</li>
                    <li>
                        <a href="<?= $base ?>/admin/reports/visitors" class="<?= $navItemClass('/admin/reports/visitors', true) ?> justify-between">
                            <div class="flex items-center">
                                <ion-icon name="analytics-outline" class="text-lg mr-3 text-emerald-600"></ion-icon>
                                <span class="font-medium">Visitor Report</span>
                            </div>
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" title="Real-time Tracking"></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= $base ?>/admin/reports/sales" class="<?= $navItemClass('/admin/reports/sales', true) ?>">
                            <ion-icon name="bar-chart-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                            <span class="font-medium">Sales Report</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= $base ?>/admin/reports/stock" class="<?= $navItemClass('/admin/reports/stock', true) ?>">
                            <ion-icon name="alert-circle-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                            <span class="font-medium">Stock Alert</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= $base ?>/admin/accounts" class="<?= $navItemClass('/admin/accounts', false) ?>">
                            <ion-icon name="wallet-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                            <span class="font-medium">Accounts Ledger</span>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- HR & Employees Section -->
                <?php if (\Core\Auth::can('hr') || \Core\Auth::can('payroll')): ?>
                    <li class="px-4 pt-4 pb-2 text-[11px] font-bold text-secondary-400 uppercase tracking-wider">HR & Payroll</li>
                    
                    <?php if (\Core\Auth::can('hr')): ?>
                        <li>
                            <a href="<?= $base ?>/admin/hr/employees" class="<?= $navItemClass('/admin/hr/employees', false) ?>">
                                <ion-icon name="people-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Employees</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $base ?>/admin/hr/attendance" class="<?= $navItemClass('/admin/hr/attendance', true, true) ?>">
                                <ion-icon name="calendar-outline" class="text-base mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Daily Attendance</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $base ?>/admin/hr/leaves" class="<?= $navItemClass('/admin/hr/leaves', true, true) ?>">
                                <ion-icon name="airplane-outline" class="text-base mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Leave Requests</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $base ?>/admin/hr/departments" class="<?= $navItemClass('/admin/hr/departments', true, true) ?>">
                                <ion-icon name="business-outline" class="text-base mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Departments</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (\Core\Auth::can('payroll')): ?>
                        <li>
                            <a href="<?= $base ?>/admin/payroll" class="<?= $navItemClass('/admin/payroll', false) ?>">
                                <ion-icon name="wallet-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Payroll & Salary</span>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Administration & Access Control Section -->
                <?php if (\Core\Auth::can('users') || \Core\Auth::can('settings')): ?>
                    <li class="px-4 pt-4 pb-2 text-[11px] font-bold text-secondary-400 uppercase tracking-wider">Administration</li>
                    
                    <?php if (\Core\Auth::can('users')): ?>
                        <li>
                            <a href="<?= $base ?>/admin/users" class="<?= $navItemClass('/admin/users', false) ?>">
                                <ion-icon name="shield-checkmark-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">User Access Control</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (\Core\Auth::can('settings')): ?>
                        <li>
                            <a href="<?= $base ?>/admin/ecommerce-settings" class="<?= $navItemClass('/admin/ecommerce-settings', false) ?>">
                                <ion-icon name="storefront-outline" class="text-lg mr-3 group-hover:text-emerald-600"></ion-icon>
                                <span class="font-medium">E-Commerce Config</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $base ?>/admin/ecommerce-settings?tab=marketing" class="<?= $navItemClass('/admin/ecommerce-settings?tab=marketing', false) ?>">
                                <ion-icon name="megaphone-outline" class="text-lg mr-3 group-hover:text-blue-600"></ion-icon>
                                <span class="font-medium">মার্কেটিং ও ট্র্যাকিং</span>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
        </nav>

        <div class="p-4 border-t border-secondary-100 space-y-1 bg-white">
            <a href="<?= $base ?>/admin/profile" class="<?= $navItemClass('/admin/profile', false) ?>">
                <ion-icon name="person-circle-outline" class="text-lg mr-2.5"></ion-icon>
                <span>Profile</span>
            </a>
            <?php if (\Core\Auth::can('settings')): ?>
                <a href="<?= $base ?>/admin/settings" class="<?= $navItemClass('/admin/settings', true) ?>">
                    <ion-icon name="settings-outline" class="text-lg mr-2.5"></ion-icon>
                    <span>Settings</span>
                </a>
            <?php endif; ?>
            <a href="<?= $base ?>/logout" class="flex items-center px-3 py-2 rounded-xl text-red-600 hover:bg-red-50 transition-colors text-sm font-medium">
                <ion-icon name="log-out-outline" class="text-lg mr-2.5"></ion-icon>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="lg:ml-64 flex-1 min-w-0 p-4 lg:p-6 w-full lg:w-[calc(100%-16rem)] overflow-x-hidden transition-all duration-300">
        <header class="flex justify-between items-center mb-5 shrink-0 gap-2">
            <div class="flex items-center gap-3">
                <button id="sidebarToggle" class="lg:hidden p-2 -ml-2 rounded-xl text-secondary-600 hover:bg-secondary-100 focus:outline-none flex items-center justify-center">
                    <ion-icon name="menu-outline" class="text-2xl"></ion-icon>
                </button>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl md:text-2xl font-bold text-secondary-800"><?php echo $title ?? 'Dashboard'; ?></h1>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-primary-100 text-primary-800 text-xs font-bold border border-primary-200 shadow-2xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-primary-600 animate-pulse"></span>
                            <span><?= htmlspecialchars($activeNavLabel) ?></span>
                        </span>
                    </div>
                    <p class="text-secondary-500 text-xs mt-0.5 hidden md:block">Logged in as <strong class="text-secondary-800"><?= htmlspecialchars($currentUserName) ?></strong> (<?= htmlspecialchars($displayRole) ?>)</p>
                </div>
            </div>
            <div class="flex items-center gap-2 md:gap-3">
                <?php if (\Core\Auth::can('vendors_purchases')): ?>
                    <a href="<?= $base ?>/admin/purchases/create" class="bg-white border border-secondary-200 hover:bg-secondary-50 text-secondary-700 font-medium py-1.5 px-3 md:py-2 md:px-3.5 rounded-xl flex items-center transition-colors shadow-xs text-xs md:text-sm">
                        <ion-icon name="cart-outline" class="md:mr-1.5 text-base"></ion-icon>
                        <span class="hidden sm:inline">Stock In</span>
                    </a>
                <?php endif; ?>

                <?php if (\Core\Auth::can('orders')): ?>
                    <a href="<?= $base ?>/admin/orders/create" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-1.5 px-3 md:py-2 md:px-3.5 rounded-xl flex items-center transition-colors shadow-xs text-xs md:text-sm">
                        <ion-icon name="add-circle-outline" class="md:mr-1.5 text-base"></ion-icon>
                        <span class="hidden sm:inline">New Order</span>
                    </a>
                <?php endif; ?>

                <div class="w-9 h-9 rounded-xl bg-primary-100 text-primary-700 font-bold flex items-center justify-center text-sm shadow-xs uppercase">
                    <?= mb_substr($currentUserName, 0, 1) ?>
                </div>
            </div>
        </header>

        <!-- Global Alerts -->
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-700 text-sm flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-3">
                    <ion-icon name="alert-circle" class="text-2xl text-red-500 shrink-0"></ion-icon>
                    <span class="font-medium"><?= htmlspecialchars($_SESSION['error']); ?></span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-700 text-lg">
                    <ion-icon name="close-outline"></ion-icon>
                </button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['success'])): ?>
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-3">
                    <ion-icon name="checkmark-circle" class="text-2xl text-emerald-600 shrink-0"></ion-icon>
                    <span class="font-medium"><?= htmlspecialchars($_SESSION['success']); ?></span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-700 text-lg">
                    <ion-icon name="close-outline"></ion-icon>
                </button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <!-- Dynamic Content -->
        <?php echo $content ?? ''; ?>
        
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('adminSidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarBackdrop = document.getElementById('sidebarBackdrop');

            function toggleSidebar() {
                sidebar.classList.toggle('-translate-x-full');
                sidebarBackdrop.classList.toggle('hidden');
            }

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', toggleSidebar);
            }

            if (sidebarBackdrop) {
                sidebarBackdrop.addEventListener('click', toggleSidebar);
            }
        });
    </script>
</body>
</html>
