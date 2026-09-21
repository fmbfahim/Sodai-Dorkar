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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Sodai Dorkar</title>
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
                <a href="<?= $base ?>/admin/dashboard" class="text-2xl font-bold text-primary-600 tracking-tight block">Sodai Dorkar</a>
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
                    <a href="<?= $base ?>/admin/dashboard" class="flex items-center px-4 py-2.5 rounded-xl text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group">
                        <ion-icon name="grid-outline" class="text-xl mr-3 group-hover:text-primary-600"></ion-icon>
                        <span class="font-medium text-sm">Dashboard</span>
                    </a>
                </li>
                <?php endif; ?>
                
                <!-- Master Data Section -->
                <?php if (\Core\Auth::can('locations') || \Core\Auth::can('products') || \Core\Auth::can('categories_brands') || \Core\Auth::can('vendors_purchases')): ?>
                    <li class="px-4 pt-4 pb-2 text-[11px] font-bold text-secondary-400 uppercase tracking-wider">Master Data</li>
                    
                    <?php if (\Core\Auth::can('locations')): ?>
                        <li>
                            <a href="<?= $base ?>/admin/warehouses" class="flex items-center px-4 py-2 rounded-xl text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group text-sm">
                                <ion-icon name="business-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Warehouses</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $base ?>/admin/areas" class="flex items-center px-4 py-2 rounded-xl text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group text-sm">
                                <ion-icon name="map-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Unions (Areas)</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $base ?>/admin/zones" class="flex items-center px-4 py-2 rounded-xl text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group pl-8 text-sm">
                                <ion-icon name="navigate-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Zones</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $base ?>/admin/points" class="flex items-center px-4 py-2 rounded-xl text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group pl-8 text-sm">
                                <ion-icon name="location-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Points</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (\Core\Auth::can('vendors_purchases')): ?>
                        <li>
                            <a href="<?= $base ?>/admin/vendors" class="flex items-center px-4 py-2 rounded-xl text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group text-sm">
                                <ion-icon name="storefront-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Suppliers</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $base ?>/admin/purchases" class="flex items-center px-4 py-2 rounded-xl text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group text-sm">
                                <ion-icon name="receipt-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Stock In (Purchases)</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (\Core\Auth::can('products')): ?>
                        <li>
                            <a href="<?= $base ?>/admin/products/dashboard" class="flex items-center px-4 py-2 rounded-xl text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group text-sm">
                                <ion-icon name="grid-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Product Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $base ?>/admin/products" class="flex items-center px-4 py-2 rounded-xl text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group text-sm">
                                <ion-icon name="cube-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">All Products</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $base ?>/admin/products/bulk-import" class="flex items-center px-4 py-1.5 text-xs rounded-xl text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group pl-8">
                                <ion-icon name="cloud-upload-outline" class="text-base mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Bulk Import (CSV)</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $base ?>/admin/products/image-finder" class="flex items-center justify-between px-4 py-1.5 text-xs rounded-xl text-secondary-600 hover:bg-emerald-50 hover:text-emerald-700 transition-colors group pl-8">
                                <div class="flex items-center">
                                    <ion-icon name="sparkles-outline" class="text-base mr-3 text-amber-500 group-hover:text-emerald-600"></ion-icon>
                                    <span class="font-medium">Auto Image Finder</span>
                                </div>
                                <span class="px-1.5 py-0.5 rounded-md bg-amber-100 text-amber-800 text-[10px] font-bold">1-Click</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $base ?>/admin/products/shwapno-importer" class="flex items-center justify-between px-4 py-1.5 text-xs rounded-xl text-secondary-600 hover:bg-teal-50 hover:text-teal-700 transition-colors group pl-8">
                                <div class="flex items-center">
                                    <ion-icon name="cloud-download-outline" class="text-base mr-3 text-teal-600 group-hover:text-teal-700"></ion-icon>
                                    <span class="font-medium">Shwapno Scraper</span>
                                </div>
                                <span class="px-1.5 py-0.5 rounded-md bg-teal-100 text-teal-800 text-[10px] font-bold">Auto</span>
                            </a>
                        </li>

                        <li>
                            <a href="<?= $base ?>/admin/products/verification" class="flex items-center px-4 py-1.5 text-xs rounded-xl text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group pl-8">
                                <ion-icon name="checkmark-done-outline" class="text-base mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Verification</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $base ?>/admin/products/availability" class="flex items-center px-4 py-1.5 text-xs rounded-xl text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group pl-8">
                                <ion-icon name="flash-outline" class="text-base mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Availability</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $base ?>/admin/products/procurement" class="flex items-center px-4 py-1.5 text-xs rounded-xl text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group pl-8">
                                <ion-icon name="cart-outline" class="text-base mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Procurement List</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (\Core\Auth::can('categories_brands')): ?>
                        <li>
                            <a href="<?= $base ?>/admin/categories" class="flex items-center px-4 py-2 rounded-xl text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group text-sm">
                                <ion-icon name="folder-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Categories</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $base ?>/admin/brands" class="flex items-center px-4 py-2 rounded-xl text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group text-sm">
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
                            <a href="<?= $base ?>/admin/customers" class="flex items-center px-4 py-2 rounded-xl text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group text-sm">
                                <ion-icon name="people-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Customers</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (\Core\Auth::can('delivery_men')): ?>
                        <li>
                            <a href="<?= $base ?>/admin/delivery-men" class="flex items-center px-4 py-2 rounded-xl text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group text-sm">
                                <ion-icon name="bicycle-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Delivery Men</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $base ?>/admin/delivery-men/allocation" class="flex items-center px-4 py-1.5 text-xs rounded-xl text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group pl-8">
                                <ion-icon name="calendar-outline" class="text-base mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Area Allocations</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (\Core\Auth::can('orders')): ?>
                        <li>
                            <a href="<?= $base ?>/admin/orders" class="flex items-center px-4 py-2 rounded-xl text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group text-sm">
                                <ion-icon name="cart-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Orders</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $base ?>/admin/orders/packaging" class="flex items-center px-4 py-1.5 text-xs rounded-xl text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group pl-8">
                                <ion-icon name="cube-outline" class="text-base mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Packaging List</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $base ?>/admin/orders/returns" class="flex items-center px-4 py-1.5 text-xs rounded-xl text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group pl-8">
                                <ion-icon name="sync-circle-outline" class="text-base mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Returns & Damages</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (\Core\Auth::can('dispatch')): ?>
                        <li>
                            <a href="<?= $base ?>/admin/dispatch" class="flex items-center px-4 py-2 rounded-xl text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group text-sm">
                                <ion-icon name="paper-plane-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Dispatch Board</span>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Reports Section -->
                <?php if (\Core\Auth::can('reports')): ?>
                    <li class="px-4 pt-4 pb-2 text-[11px] font-bold text-secondary-400 uppercase tracking-wider">Reports</li>
                    <li>
                        <a href="<?= $base ?>/admin/reports/sales" class="flex items-center px-4 py-2 rounded-xl text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group text-sm">
                            <ion-icon name="bar-chart-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                            <span class="font-medium">Sales Report</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= $base ?>/admin/reports/stock" class="flex items-center px-4 py-2 rounded-xl text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group text-sm">
                            <ion-icon name="alert-circle-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                            <span class="font-medium">Stock Alert</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= $base ?>/admin/accounts" class="flex items-center px-4 py-2 rounded-xl text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group text-sm">
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
                            <a href="<?= $base ?>/admin/hr/employees" class="flex items-center px-4 py-2 rounded-xl text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group text-sm">
                                <ion-icon name="people-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Employees</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $base ?>/admin/hr/attendance" class="flex items-center px-4 py-1.5 text-xs rounded-xl text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group pl-8">
                                <ion-icon name="calendar-outline" class="text-base mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Daily Attendance</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $base ?>/admin/hr/leaves" class="flex items-center px-4 py-1.5 text-xs rounded-xl text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group pl-8">
                                <ion-icon name="airplane-outline" class="text-base mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Leave Requests</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= $base ?>/admin/hr/departments" class="flex items-center px-4 py-1.5 text-xs rounded-xl text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group pl-8">
                                <ion-icon name="business-outline" class="text-base mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">Departments</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (\Core\Auth::can('payroll')): ?>
                        <li>
                            <a href="<?= $base ?>/admin/payroll" class="flex items-center px-4 py-2 rounded-xl text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group text-sm">
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
                            <a href="<?= $base ?>/admin/users" class="flex items-center px-4 py-2 rounded-xl text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group text-sm">
                                <ion-icon name="shield-checkmark-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                                <span class="font-medium">User Access Control</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (\Core\Auth::can('settings')): ?>
                        <li>
                            <a href="<?= $base ?>/admin/ecommerce-settings" class="flex items-center px-4 py-2 rounded-xl text-secondary-600 hover:bg-emerald-50 hover:text-emerald-700 transition-colors group text-sm">
                                <ion-icon name="storefront-outline" class="text-lg mr-3 group-hover:text-emerald-600"></ion-icon>
                                <span class="font-medium">E-Commerce Config</span>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
        </nav>

        <div class="p-4 border-t border-secondary-100 space-y-1 bg-white">
            <a href="<?= $base ?>/admin/profile" class="flex items-center px-3 py-2 rounded-xl text-secondary-600 hover:bg-secondary-50 transition-colors text-sm font-medium">
                <ion-icon name="person-circle-outline" class="text-lg mr-2.5"></ion-icon>
                <span>Profile</span>
            </a>
            <?php if (\Core\Auth::can('settings')): ?>
                <a href="<?= $base ?>/admin/settings" class="flex items-center px-3 py-2 rounded-xl text-secondary-600 hover:bg-secondary-50 transition-colors text-sm font-medium">
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
                    <h1 class="text-xl md:text-2xl font-bold text-secondary-800"><?php echo $title ?? 'Dashboard'; ?></h1>
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
