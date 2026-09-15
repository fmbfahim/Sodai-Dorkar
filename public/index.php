<?php

// Autoloader (Manual implementation since we might not have Composer autoloader set up for custom namespace yet, standard PSR-4 is better)
spl_autoload_register(function ($class) {
    $base_dir = __DIR__ . '/../src/';
    $file = $base_dir . str_replace('\\', '/', $class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

use Core\Router;
use Core\Database;
use Core\Lang;

// Load Config
$config = require __DIR__ . '/../config/database.php';

// Initialize Database (Global helper or Dependency Injection)
// For simplicity in raw PHP, we might store it in a static registry or global variable
$db = new Database($config);

// Initialize Language
Lang::init();

// Initialize Router
$router = new Router();

// Define Routes
$router->get('/', 'ShopController@index');
$router->get('/shop', 'ShopController@shop');
$router->get('/category', 'ShopController@category');
$router->get('/product', 'ShopController@product');
$router->get('/cart', 'ShopController@cart');
$router->get('/cart/data', 'ShopController@cartData');
$router->post('/cart/add', 'ShopController@addToCart');
$router->post('/cart/remove', 'ShopController@removeFromCart');
$router->post('/cart/update', 'ShopController@updateCart');
$router->get('/checkout', 'ShopController@checkout');
$router->post('/checkout/place-order', 'ShopController@placeOrder');
$router->get('/order/success', 'ShopController@success');
$router->get('/set-language', 'ShopController@setLanguage');
$router->post('/set-language', 'ShopController@setLanguage');

// API Routes for cascading dropdowns
$router->get('/api/zones', 'ShopController@getZones');
$router->get('/api/points', 'ShopController@getPoints');

// Customer Auth Routes
$router->get('/checkout/auth', 'CustomerAuthController@showAuth');
$router->post('/checkout/login', 'CustomerAuthController@login');
$router->post('/checkout/signup', 'CustomerAuthController@signup');
$router->get('/checkout/reset-password', 'CustomerAuthController@showResetPassword');
$router->post('/checkout/reset-password', 'CustomerAuthController@updatePassword');
$router->post('/checkout/firebase-login', 'CustomerAuthController@firebaseLogin');
$router->get('/customer/logout', 'CustomerAuthController@logout');

// Customer Account / Dashboard Routes
$router->get('/account', 'CustomerDashboardController@dashboard');
$router->get('/account/orders', 'CustomerDashboardController@orders');
$router->get('/account/order-detail', 'CustomerDashboardController@orderDetail');
$router->get('/account/profile', 'CustomerDashboardController@profile');
$router->post('/account/profile/update', 'CustomerDashboardController@updateProfile');
$router->get('/account/change-password', 'CustomerDashboardController@changePassword');
$router->post('/account/change-password', 'CustomerDashboardController@updatePassword');

$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');

// Admin Routes
$router->get('/admin/dashboard', 'AdminController@dashboard');
$router->get('/admin/profile', 'ProfileController@index');
$router->post('/admin/profile/update', 'ProfileController@update');

// Warehouse Routes
$router->get('/admin/warehouses', 'WarehouseController@index');
$router->post('/admin/warehouses/store', 'WarehouseController@store');
$router->post('/admin/warehouses/delete', 'WarehouseController@destroy');

// Area Routes
$router->get('/admin/areas', 'AreaController@index');
$router->post('/admin/areas/store', 'AreaController@store');
$router->post('/admin/areas/delete', 'AreaController@destroy');

// Vendor Routes
$router->get('/admin/vendors', 'VendorController@index');
$router->post('/admin/vendors/store', 'VendorController@store');
$router->post('/admin/vendors/delete', 'VendorController@destroy');

// Product Routes
$router->get('/admin/products/dashboard', 'ProductController@dashboard');
$router->get('/admin/products', 'ProductController@index');
$router->post('/admin/products/store', 'ProductController@store');
$router->post('/admin/products/delete', 'ProductController@destroy');
$router->get('/admin/products/edit', 'ProductController@edit');
$router->post('/admin/products/update', 'ProductController@update');
$router->get('/admin/products/bulk-import', 'ProductController@bulkImportIndex');
$router->get('/admin/products/bulk-demo', 'ProductController@bulkImportDemo');
$router->post('/admin/products/bulk-chunk-import', 'ProductController@bulkChunkImport');
$router->post('/admin/products/bulk-preview', 'ProductController@bulkImportPreview');
$router->post('/admin/products/bulk-store', 'ProductController@bulkImportStore');
$router->post('/admin/products/bulk-store-manual', 'ProductController@bulkStoreManual');
$router->get('/admin/products/verification', 'ProductController@verificationIndex');
$router->post('/admin/products/verification-update', 'ProductController@verificationUpdate');
$router->get('/admin/products/on-demand', 'ProductController@onDemandIndex');
$router->get('/admin/products/availability', 'ProductController@availabilityIndex');
$router->post('/admin/products/availability-update', 'ProductController@availabilityUpdate');
$router->get('/admin/products/procurement', 'ProductController@procurementIndex');

// Category Routes
$router->get('/admin/categories', 'CategoryController@index');
$router->post('/admin/categories/store', 'CategoryController@store');
$router->post('/admin/categories/delete', 'CategoryController@destroy');
$router->get('/admin/categories/edit', 'CategoryController@edit');
$router->post('/admin/categories/update', 'CategoryController@update');

// Brand Routes
$router->get('/admin/brands', 'BrandController@index');
$router->post('/admin/brands/store', 'BrandController@store');
$router->post('/admin/brands/delete', 'BrandController@destroy');
$router->get('/admin/brands/edit', 'BrandController@edit');
$router->post('/admin/brands/update', 'BrandController@update');

// Area Routes
$router->get('/admin/areas', 'AreaController@index');
$router->post('/admin/areas/store', 'AreaController@store');
$router->post('/admin/areas/delete', 'AreaController@destroy');
$router->get('/admin/areas/edit', 'AreaController@edit');
$router->post('/admin/areas/update', 'AreaController@update');

// Zones
$router->get('/admin/zones', 'ZoneController@index');
$router->post('/admin/zones/store', 'ZoneController@store');
$router->get('/admin/zones/edit', 'ZoneController@edit');
$router->post('/admin/zones/update', 'ZoneController@update');
$router->post('/admin/zones/delete', 'ZoneController@destroy');

// Vendors
$router->get('/admin/customers/search-api', 'CustomerController@apiSearch');
$router->get('/admin/vendors', 'VendorController@index');
$router->post('/admin/vendors/store', 'VendorController@store');
$router->post('/admin/vendors/delete', 'VendorController@destroy');
$router->get('/admin/vendors/ledger', 'VendorController@ledger');
$router->post('/admin/vendors/payment', 'VendorController@payment');

// Purchases
$router->get('/admin/purchases', 'PurchaseController@index');
$router->get('/admin/purchases/create', 'PurchaseController@create');
$router->post('/admin/purchases/store', 'PurchaseController@store');
$router->get('/admin/purchases/show', 'PurchaseController@show');

// Point Routes
$router->get('/admin/points', 'PointController@index');
$router->post('/admin/points/store', 'PointController@store');
$router->post('/admin/points/delete', 'PointController@destroy');
$router->get('/admin/points/edit', 'PointController@edit');
$router->post('/admin/points/update', 'PointController@update');

// Customer Routes
$router->get('/admin/customers', 'CustomerController@index');
$router->get('/admin/customers/create', 'CustomerController@create');
$router->post('/admin/customers/store', 'CustomerController@store');
$router->get('/admin/customers/show', 'CustomerController@show');
$router->get('/admin/customers/edit', 'CustomerController@edit');
$router->post('/admin/customers/update', 'CustomerController@update');
$router->post('/admin/customers/delete', 'CustomerController@destroy');
$router->get('/admin/customers/history', 'CustomerController@history');
$router->post('/admin/customers/generate-pin', 'CustomerController@generatePin');

// Order Routes
$router->get('/admin/orders', 'OrderController@index');
$router->get('/admin/orders/packaging', 'OrderController@packagingList');
$router->get('/admin/orders/create', 'OrderController@create');
$router->get('/admin/orders/show', 'OrderController@show');
$router->post('/admin/orders/store', 'OrderController@store');
$router->post('/admin/orders/change-status', 'OrderController@changeStatus');
$router->post('/admin/orders/bulk-receive-packaging', 'OrderController@bulkReceivePackaging');
$router->get('/admin/orders/check-pending', 'OrderController@checkPending');
$router->post('/admin/orders/assign', 'OrderController@assign');
$router->get('/admin/orders/edit', 'OrderController@edit');
$router->post('/admin/orders/update', 'OrderController@update');
$router->get('/admin/orders/invoice', 'OrderController@invoice');
$router->get('/admin/orders/pos-receipt', 'OrderController@posReceipt');
$router->get('/admin/orders/label', 'OrderController@label');
$router->post('/admin/orders/adjust-amount', 'OrderController@adjustAmount');
$router->post('/admin/orders/update-notes', 'OrderController@updateNotes');
$router->get('/admin/orders/returns', 'OrderController@returns');
$router->post('/admin/orders/process-return', 'OrderController@processReturn');

// Delivery Man Management Routes (Admin)
$router->get('/admin/delivery-men', 'DeliveryManController@index');
$router->get('/admin/delivery-men/report', 'DeliveryManController@report');
$router->post('/admin/delivery-men/store', 'DeliveryManController@store');
$router->post('/admin/delivery-men/delete', 'DeliveryManController@destroy');
$router->post('/admin/delivery-men/change-password', 'DeliveryManController@changePassword');
$router->post('/admin/delivery-men/collect-cash', 'DeliveryManController@collectCash');
$router->get('/admin/delivery-men/collections', 'DeliveryManController@collectionsHistory');

$router->get('/admin/delivery-men/allocation', 'DeliveryManController@allocation');
$router->post('/admin/delivery-men/allocation/store', 'DeliveryManController@storeAllocation');
$router->post('/admin/delivery-men/allocation/delete', 'DeliveryManController@destroyAllocation');
$router->get('/admin/delivery-men/allocation/edit', 'DeliveryManController@editAllocation');
$router->post('/admin/delivery-men/allocation/update', 'DeliveryManController@updateAllocation');

// Delivery Man App Routes (App)
$router->get('/delivery/dashboard', 'DeliveryController@dashboard');
$router->get('/delivery/profile', 'DeliveryController@profile');
$router->get('/delivery/settings', 'DeliveryController@settings');
$router->post('/delivery/update-status', 'DeliveryController@updateStatus');
$router->post('/delivery/modify-order', 'DeliveryController@modifyOrder');
$router->post('/delivery/transfer-order', 'DeliveryController@transferOrder');
$router->post('/delivery/update-note', 'DeliveryController@updateNote');
$router->post('/delivery/update-location', 'DeliveryController@updateLocation');
$router->post('/delivery/change-password', 'DeliveryController@changePassword');
$router->get('/delivery/parcel-search', 'DeliveryController@parcelSearch');

// Reports
$router->get('/admin/reports/stock', 'ReportController@stock');
$router->get('/admin/reports/sales', 'ReportController@sales');
$router->get('/admin/accounts', 'AccountController@index');

// Settings
$router->get('/admin/settings', 'SettingsController@index');
$router->post('/admin/settings/update', 'SettingsController@update');
$router->get('/admin/ecommerce-settings', 'SettingsController@ecommerce');
$router->post('/admin/ecommerce-settings/update', 'SettingsController@updateEcommerce');
$router->get('/admin/settings/units', 'SettingsController@units');
$router->post('/admin/settings/units/store', 'SettingsController@storeUnit');
$router->post('/admin/settings/units/delete', 'SettingsController@deleteUnit');
$router->get('/admin/settings/cleanup', 'SettingsController@cleanup');
$router->post('/admin/settings/cleanup/execute', 'SettingsController@executeCleanup');

// Delivery Man Management Routes
$router->get('/admin/delivery-men', 'DeliveryManController@index');
$router->post('/admin/delivery-men/store', 'DeliveryManController@store');
$router->post('/admin/delivery-men/delete', 'DeliveryManController@destroy');

// Dispatch
$router->get('/admin/dispatch', 'DispatchController@index');
$router->post('/admin/dispatch/bulk', 'DispatchController@bulkAction');

// User Management Routes
$router->get('/admin/users', 'UserController@index');
$router->post('/admin/users/store', 'UserController@store');
$router->get('/admin/users/edit', 'UserController@edit');
$router->post('/admin/users/update', 'UserController@update');
$router->post('/admin/users/toggle-status', 'UserController@toggleStatus');
$router->post('/admin/users/delete', 'UserController@destroy');
$router->post('/admin/users/create-employee', 'UserController@createQuickEmployee');

// HR & Employee Management Routes
$router->get('/admin/hr/employees', 'HrController@employees');
$router->get('/admin/hr/employees/create', 'HrController@createEmployee');
$router->post('/admin/hr/employees/store', 'HrController@storeEmployee');
$router->post('/admin/hr/employees/create-user', 'HrController@createQuickUser');
$router->get('/admin/hr/employees/show', 'HrController@showEmployee');
$router->get('/admin/hr/employees/edit', 'HrController@editEmployee');
$router->post('/admin/hr/employees/update', 'HrController@updateEmployee');
$router->post('/admin/hr/employees/delete', 'HrController@destroyEmployee');
$router->get('/admin/hr/departments', 'HrController@departments');
$router->post('/admin/hr/departments/store', 'HrController@storeDepartment');
$router->post('/admin/hr/departments/delete', 'HrController@destroyDepartment');
$router->post('/admin/hr/designations/store', 'HrController@storeDesignation');
$router->post('/admin/hr/designations/delete', 'HrController@destroyDesignation');
$router->get('/admin/hr/attendance', 'HrController@attendance');
$router->post('/admin/hr/attendance/store', 'HrController@storeAttendance');
$router->get('/admin/hr/attendance/report', 'HrController@attendanceReport');
$router->get('/admin/hr/leaves', 'HrController@leaves');
$router->post('/admin/hr/leaves/store', 'HrController@storeLeave');
$router->post('/admin/hr/leaves/update-status', 'HrController@updateLeaveStatus');
$router->post('/admin/hr/leaves/delete', 'HrController@destroyLeave');

// Payroll Management Routes
$router->get('/admin/payroll', 'PayrollController@index');
$router->post('/admin/payroll/generate', 'PayrollController@generate');
$router->get('/admin/payroll/payslip', 'PayrollController@payslip');
$router->post('/admin/payroll/mark-paid', 'PayrollController@markPaid');
$router->post('/admin/payroll/delete', 'PayrollController@destroy');

$router->resolve();
