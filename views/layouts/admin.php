<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Sodai Dorkar</title>
    <link href="/sodai-dorkar/public/css/output.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Ionicons for icons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body class="bg-secondary-50 font-sans text-secondary-900 flex min-h-screen">

    <!-- Mobile Sidebar Backdrop -->
    <div id="sidebarBackdrop" class="fixed inset-0 bg-secondary-900/50 z-40 hidden lg:hidden transition-opacity"></div>

    <!-- Sidebar -->
    <aside id="adminSidebar" class="w-64 bg-white border-r border-secondary-200 flex flex-col fixed h-full z-50 transition-transform duration-300 transform -translate-x-full lg:translate-x-0">
        <div class="p-6 flex items-center justify-center border-b border-secondary-100">
            <h2 class="text-2xl font-bold text-primary-600 tracking-tight">Sodai Dorkar</h2>
        </div>

        <nav class="flex-1 overflow-y-auto py-4">
            <ul class="space-y-1 px-3">
                <li>
                    <a href="/sodai-dorkar/public/admin/dashboard" class="flex items-center px-4 py-3 rounded-lg text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group">
                        <ion-icon name="grid-outline" class="text-xl mr-3 group-hover:text-primary-600"></ion-icon>
                        <span class="font-medium">Dashboard</span>
                    </a>
                </li>
                
                <li class="px-4 pt-4 pb-2 text-xs font-semibold text-secondary-400 uppercase tracking-wider">Master Data</li>
                
                <li>
                    <a href="/sodai-dorkar/public/admin/warehouses" class="flex items-center px-4 py-3 rounded-lg text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group">
                        <ion-icon name="business-outline" class="text-xl mr-3 group-hover:text-primary-600"></ion-icon>
                        <span class="font-medium">Warehouses</span>
                    </a>
                </li>
                <li>
                    <a href="/sodai-dorkar/public/admin/areas" class="flex items-center px-4 py-3 rounded-lg text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group">
                        <ion-icon name="map-outline" class="text-xl mr-3 group-hover:text-primary-600"></ion-icon>
                        <span class="font-medium">Unions (Areas)</span>
                    </a>
                </li>
                 <li>
                    <a href="/sodai-dorkar/public/admin/zones" class="flex items-center px-4 py-3 rounded-lg text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group pl-8">
                        <ion-icon name="navigate-outline" class="text-xl mr-3 group-hover:text-primary-600"></ion-icon>
                        <span class="font-medium">Zones</span>
                    </a>
                </li>
                 <li>
                    <a href="/sodai-dorkar/public/admin/points" class="flex items-center px-4 py-3 rounded-lg text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group pl-8">
                        <ion-icon name="location-outline" class="text-xl mr-3 group-hover:text-primary-600"></ion-icon>
                        <span class="font-medium">Points</span>
                    </a>
                </li>
                 <li>
                    <a href="/sodai-dorkar/public/admin/vendors" class="flex items-center px-4 py-3 rounded-lg text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group">
                        <ion-icon name="storefront-outline" class="text-xl mr-3 group-hover:text-primary-600"></ion-icon>
                        <span class="font-medium">Vendors</span>
                    </a>
                </li>
                <li>
                    <a href="/sodai-dorkar/public/admin/products" class="flex items-center px-4 py-3 rounded-lg text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group">
                        <ion-icon name="cube-outline" class="text-xl mr-3 group-hover:text-primary-600"></ion-icon>
                        <span class="font-medium">Products</span>
                    </a>
                </li>
                <li class="px-4 pt-2 pb-1 text-[10px] font-semibold text-secondary-400 uppercase tracking-wider pl-8">Sourcing Workflow</li>
                <li>
                    <a href="/sodai-dorkar/public/admin/products/verification" class="flex items-center px-4 py-2 text-sm rounded-lg text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group pl-8">
                        <ion-icon name="checkmark-done-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                        <span class="font-medium">Verification</span>
                    </a>
                </li>
                <li>
                    <a href="/sodai-dorkar/public/admin/products/availability" class="flex items-center px-4 py-2 text-sm rounded-lg text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group pl-8">
                        <ion-icon name="storefront-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                        <span class="font-medium">Availability</span>
                    </a>
                </li>
                <li>
                    <a href="/sodai-dorkar/public/admin/products/on-demand" class="flex items-center px-4 py-2 text-sm rounded-lg text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group pl-8">
                        <ion-icon name="flame-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                        <span class="font-medium">On-Demand</span>
                    </a>
                </li>
                <li>
                    <a href="/sodai-dorkar/public/admin/products/procurement" class="flex items-center px-4 py-2 text-sm rounded-lg text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group pl-8">
                        <ion-icon name="cart-outline" class="text-lg mr-3 group-hover:text-primary-600"></ion-icon>
                        <span class="font-medium">Procurement List</span>
                    </a>
                </li>
                 <li>
                    <a href="/sodai-dorkar/public/admin/categories" class="flex items-center px-4 py-3 rounded-lg text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group pl-8">
                        <ion-icon name="grid-outline" class="text-xl mr-3 group-hover:text-primary-600"></ion-icon>
                        <span class="font-medium">Categories</span>
                    </a>
                </li>
                 <li>
                    <a href="/sodai-dorkar/public/admin/brands" class="flex items-center px-4 py-3 rounded-lg text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group pl-8">
                        <ion-icon name="pricetag-outline" class="text-xl mr-3 group-hover:text-primary-600"></ion-icon>
                        <span class="font-medium">Brands</span>
                    </a>
                </li>
                 <li>
                    <a href="/sodai-dorkar/public/admin/vendors" class="flex items-center px-4 py-2 text-secondary-600 hover:bg-primary-50 hover:text-primary-600 rounded-lg transition-colors mb-1 pl-8">
                        <ion-icon name="business-outline" class="mr-3 text-xl"></ion-icon>
                        <span class="font-medium">Suppliers</span>
                    </a>
                    <a href="/sodai-dorkar/public/admin/purchases" class="flex items-center px-4 py-2 text-secondary-600 hover:bg-primary-50 hover:text-primary-600 rounded-lg transition-colors mb-1 pl-8">
                        <ion-icon name="cart-outline" class="mr-3 text-xl"></ion-icon>
                        <span class="font-medium">Stock In</span>
                    </a>
                </li>

                <li class="px-4 pt-4 pb-2 text-xs font-semibold text-secondary-400 uppercase tracking-wider">Operations</li>

                 <li>
                    <a href="/sodai-dorkar/public/admin/customers" class="flex items-center px-4 py-3 rounded-lg text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group">
                        <ion-icon name="people-outline" class="text-xl mr-3 group-hover:text-primary-600"></ion-icon>
                        <span class="font-medium">Customers</span>
                    </a>
                </li>
                 <li>
                    <a href="/sodai-dorkar/public/admin/delivery-men" class="flex items-center px-4 py-3 rounded-lg text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group">
                        <ion-icon name="bicycle-outline" class="text-xl mr-3 group-hover:text-primary-600"></ion-icon>
                        <span class="font-medium">Delivery Men</span>
                    </a>
                </li>
                <li>
                    <a href="/sodai-dorkar/public/delivery/dashboard" target="_blank" class="flex items-center px-4 py-2 rounded-lg text-emerald-600 hover:bg-emerald-50 transition-colors group pl-8">
                        <ion-icon name="open-outline" class="text-lg mr-3 text-emerald-500"></ion-icon>
                        <span class="font-medium text-xs">Open Delivery App ↗</span>
                    </a>
                </li>
                 <li>
                    <a href="/sodai-dorkar/public/admin/delivery-men/allocation" class="flex items-center px-4 py-3 rounded-lg text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group pl-8">
                        <ion-icon name="calendar-outline" class="text-xl mr-3 group-hover:text-primary-600"></ion-icon>
                        <span class="font-medium">Allocations</span>
                    </a>
                </li>
                <li>
                    <a href="/sodai-dorkar/public/admin/orders" class="flex items-center px-4 py-3 rounded-lg text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group">
                        <ion-icon name="cart-outline" class="text-xl mr-3 group-hover:text-primary-600"></ion-icon>
                        <span class="font-medium">Orders</span>
                    </a>
                </li>
                <li>
                    <a href="/sodai-dorkar/public/admin/orders/returns" class="flex items-center px-4 py-3 rounded-lg text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group pl-8">
                        <ion-icon name="sync-circle-outline" class="text-xl mr-3 group-hover:text-primary-600"></ion-icon>
                        <span class="font-medium">Returns & Damages</span>
                    </a>
                </li>
                <li>
                    <a href="/sodai-dorkar/public/admin/orders/packaging" class="flex items-center px-4 py-3 rounded-lg text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group pl-8">
                        <ion-icon name="cube-outline" class="text-xl mr-3 group-hover:text-primary-600"></ion-icon>
                        <span class="font-medium">Packaging List</span>
                    </a>
                </li>
                <li>
                    <a href="/sodai-dorkar/public/admin/dispatch" class="flex items-center px-4 py-3 rounded-lg text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group">
                        <ion-icon name="paper-plane-outline" class="text-xl mr-3 group-hover:text-primary-600"></ion-icon>
                        <span class="font-medium">Dispatch Board</span>
                    </a>
                </li>

                <li class="px-4 pt-4 pb-2 text-xs font-semibold text-secondary-400 uppercase tracking-wider">Reports</li>

                 <li>
                    <a href="/sodai-dorkar/public/admin/reports/stock" class="flex items-center px-4 py-3 rounded-lg text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group">
                        <ion-icon name="alert-circle-outline" class="text-xl mr-3 group-hover:text-primary-600"></ion-icon>
                        <span class="font-medium">Stock Alert</span>
                    </a>
                </li>
                <li>
                    <a href="/sodai-dorkar/public/admin/reports/sales" class="flex items-center px-4 py-3 rounded-lg text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group">
                        <ion-icon name="bar-chart-outline" class="text-xl mr-3 group-hover:text-primary-600"></ion-icon>
                        <span class="font-medium">Sales Report</span>
                    </a>
                </li>                 <li>
                    <a href="/sodai-dorkar/public/admin/accounts" class="flex items-center px-4 py-3 rounded-lg text-secondary-600 hover:bg-primary-50 hover:text-primary-600 transition-colors group">
                        <ion-icon name="wallet-outline" class="text-xl mr-3 group-hover:text-primary-600"></ion-icon>
                        <span class="font-medium">Accounts</span>
                    </a>
                </li>

                <li class="px-4 pt-4 pb-2 text-xs font-semibold text-secondary-400 uppercase tracking-wider">Store Config</li>
                <li>
                    <a href="/sodai-dorkar/public/admin/ecommerce-settings" class="flex items-center px-4 py-3 rounded-lg text-secondary-600 hover:bg-emerald-50 hover:text-emerald-700 transition-colors group">
                        <ion-icon name="storefront-outline" class="text-xl mr-3 group-hover:text-emerald-600"></ion-icon>
                        <span class="font-medium">E-Commerce Management</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="p-4 border-t border-secondary-100 space-y-2">
            <a href="/sodai-dorkar/public/admin/ecommerce-settings" class="flex items-center px-4 py-2 rounded-lg text-secondary-600 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                <ion-icon name="cart-outline" class="text-xl mr-3 text-emerald-600"></ion-icon>
                <span class="font-medium">E-Commerce Management</span>
            </a>
            <a href="/sodai-dorkar/public/admin/profile" class="flex items-center px-4 py-2 rounded-lg text-secondary-600 hover:bg-secondary-50 transition-colors">
                <ion-icon name="person-circle-outline" class="text-xl mr-3"></ion-icon>
                <span class="font-medium">Profile</span>
            </a>
            <a href="/sodai-dorkar/public/admin/settings" class="flex items-center px-4 py-2 rounded-lg text-secondary-600 hover:bg-secondary-50 transition-colors">
                <ion-icon name="settings-outline" class="text-xl mr-3"></ion-icon>
                <span class="font-medium">Settings</span>
            </a>
             <a href="/sodai-dorkar/public/logout" class="flex items-center px-4 py-2 rounded-lg text-red-500 hover:bg-red-50 transition-colors">
                <ion-icon name="log-out-outline" class="text-xl mr-3"></ion-icon>
                <span class="font-medium">Logout</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="lg:ml-64 flex-1 min-w-0 p-4 lg:p-6 w-full lg:max-w-[calc(100vw-16rem)] overflow-x-hidden transition-all duration-300">
        <header class="flex justify-between items-center mb-4 shrink-0 gap-2">
            <div class="flex items-center gap-3">
                <button id="sidebarToggle" class="lg:hidden p-2 -ml-2 rounded-md text-secondary-600 hover:bg-secondary-100 focus:outline-none flex items-center justify-center">
                    <ion-icon name="menu-outline" class="text-2xl"></ion-icon>
                </button>
                <div>
                    <h1 class="text-xl md:text-2xl font-bold text-secondary-800"><?php echo $title ?? 'Dashboard'; ?></h1>
                    <p class="text-secondary-500 text-sm mt-1 hidden md:block">Welcome back, Admin</p>
                </div>
            </div>
            <div class="flex items-center gap-2 md:gap-4">
                <a href="/sodai-dorkar/public/admin/purchases/create" class="bg-white border border-secondary-200 hover:bg-secondary-50 text-secondary-700 font-medium py-1.5 px-3 md:py-2 md:px-4 rounded-lg flex items-center transition-colors shadow-sm text-sm md:text-base">
                    <ion-icon name="cart-outline" class="md:mr-2 text-lg"></ion-icon>
                    <span class="hidden md:inline">Stock In</span>
                </a>
                <a href="/sodai-dorkar/public/admin/orders/create" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-1.5 px-3 md:py-2 md:px-4 rounded-lg flex items-center transition-colors shadow-sm text-sm md:text-base">
                    <ion-icon name="add-circle-outline" class="md:mr-2 text-lg"></ion-icon>
                    <span class="hidden md:inline">New Order</span>
                </a>
                <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold shrink-0">
                    A
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
