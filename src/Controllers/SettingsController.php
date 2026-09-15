<?php

namespace Controllers;

use Core\Controller;
use Core\Middleware;
use Models\Setting;
use Models\PackagingUnit;

class SettingsController extends Controller {

    public function __construct() {
        Middleware::permission('settings');
    }

    public function index() {
        $settingModel = new Setting();
        $settings = $settingModel->getAll();

        return $this->view('admin/settings/index', [
            'title' => 'Settings',
            'settings' => $settings,
            'activeTab' => 'general'
        ]);
    }

    public function update() {
        $settingModel = new Setting();
        
        foreach ($_POST as $key => $value) {
            $settingModel->update($key, $value);
        }

        header('Location: /sodai-dorkar/public/admin/settings?success=1');
        exit;
    }

    public function ecommerce() {
        $settingModel = new Setting();
        $settings = $settingModel->getAll();
        
        $validTabs = ['products', 'shipping', 'payments', 'privacy', 'visibility'];
        $tab = $_GET['tab'] ?? 'products';
        if (!in_array($tab, $validTabs)) {
            $tab = 'products';
        }

        $areas = [];
        $points = [];
        if ($tab === 'shipping') {
            $areaModel = new \Models\Area();
            $areas = $areaModel->all();

            $pointModel = new \Models\Point();
            $points = $pointModel->all();
        }

        return $this->view('admin/settings/ecommerce', [
            'title' => 'E-Commerce Management',
            'settings' => $settings,
            'activeTab' => $tab,
            'areas' => $areas,
            'points' => $points
        ]);
    }

    public function updateEcommerce() {
        $settingModel = new Setting();
        $activeTab = $_POST['active_tab'] ?? 'products';
        
        $excludeKeys = ['csrf_token', 'active_tab'];

        $checkboxMap = [
            'products' => [
                'manage_stock_enabled',
                'hide_out_of_stock_products',
                'show_discount_badge',
                'prices_include_tax',
                'enable_product_reviews',
                'reviews_verified_buyers_only'
            ],
            'shipping' => [
                'spend_more_offers_enabled',
                'free_shipping_enabled',
                'free_shipping_progress_bar_enabled',
                'free_shipping_exclude_heavy',
                'express_delivery_enabled',
                'delivery_weight_surcharge_enabled',
                'delivery_slots_enabled',
                'delivery_bad_weather_surcharge_enabled',
                'delivery_cod_fee_enabled',
                'allow_rider_transfer'
            ],
            'payments' => [
                'payment_cod_enabled',
                'payment_bkash_enabled',
                'payment_nagad_enabled',
                'payment_rocket_enabled',
                'payment_online_enabled'
            ],
            'privacy' => [
                'allow_guest_checkout',
                'enable_checkout_signup',
                'require_login_to_checkout',
                'auth_manual_pin_enabled',
                'auth_firebase_otp_enabled'
            ],
            'visibility' => [
                'top_announcement_bar_enabled',
                'seo_index_allow'
            ]
        ];

        // Process submitted form data
        foreach ($_POST as $key => $value) {
            if (!in_array($key, $excludeKeys)) {
                if ($key === 'spend_more_offers_tiers') {
                    if (is_array($value)) {
                        $settingModel->update($key, json_encode(array_values($value)));
                    } else {
                        $settingModel->update($key, trim($value));
                    }
                } elseif (is_array($value)) {
                    $cleanArr = [];
                    foreach ($value as $k => $v) {
                        if (is_array($v)) {
                            $cleanArr[$k] = $v;
                        } else {
                            $vTrim = trim($v);
                            if ($vTrim !== '') {
                                $cleanArr[$k] = is_numeric($vTrim) ? floatval($vTrim) : $vTrim;
                            }
                        }
                    }
                    $settingModel->update($key, json_encode($cleanArr));
                } else {
                    $settingModel->update($key, trim($value));
                }
            }
        }

        // For checkboxes belonging to the active tab, if not present in $_POST, set to '0'
        if (isset($checkboxMap[$activeTab])) {
            foreach ($checkboxMap[$activeTab] as $cbKey) {
                if (!isset($_POST[$cbKey])) {
                    $settingModel->update($cbKey, '0');
                }
            }
        }

        header('Location: /sodai-dorkar/public/admin/ecommerce-settings?tab=' . urlencode($activeTab) . '&success=1');
        exit;
    }

    public function units() {
        $unitModel = new PackagingUnit();
        $units = $unitModel->all();

        return $this->view('admin/settings/units', [
            'title' => 'Packaging & Unit Settings',
            'units' => $units,
            'activeTab' => 'units'
        ]);
    }

    public function storeUnit() {
        $name = trim($_POST['name'] ?? '');
        $baseUnit = trim($_POST['base_unit'] ?? 'kg');
        $defaultQty = floatval($_POST['default_qty'] ?? 1.000);

        if (!empty($name) && $defaultQty > 0) {
            $unitModel = new PackagingUnit();
            $unitModel->create([
                'name' => $name,
                'base_unit' => $baseUnit,
                'default_qty' => $defaultQty,
                'is_default' => !empty($_POST['is_default']) ? 1 : 0
            ]);
        }

        header('Location: /sodai-dorkar/public/admin/settings/units?success=1');
        exit;
    }

    public function deleteUnit() {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $unitModel = new PackagingUnit();
            $unitModel->delete($id);
        }

        header('Location: /sodai-dorkar/public/admin/settings/units?deleted=1');
        exit;
    }

    public function cleanup() {
        if (!\Core\Auth::can('database_reset') && !\Core\Auth::isAdmin()) {
            \Core\Middleware::permission('database_reset');
        }

        $config = require __DIR__ . '/../../config/database.php';
        $db = new \Core\Database($config);
        $pdo = $db->getConnection();

        $getCount = function($table, $where = '') use ($pdo) {
            try {
                $sql = "SELECT COUNT(*) FROM `{$table}`" . ($where ? " WHERE {$where}" : "");
                return (int)$pdo->query($sql)->fetchColumn();
            } catch (\Exception $e) {
                return 0;
            }
        };

        $counts = [
            'orders' => [
                'label' => 'অর্ডার ও সেলস রেকর্ডস (Orders & Sales)',
                'label_en' => 'Orders & Sales Records',
                'desc' => 'All customer orders, ordered item lines, delivery tracking, and returns logs',
                'count' => $getCount('orders'),
                'details' => $getCount('orders') . ' orders, ' . $getCount('order_items') . ' items, ' . $getCount('order_tracking') . ' logs',
                'icon' => 'cart-outline',
                'color' => 'amber'
            ],
            'products' => [
                'label' => 'পণ্য ও ইনভেন্টরি ক্যাটালগ (Products & Catalog)',
                'label_en' => 'Products & Inventory Catalog',
                'desc' => 'All product listings, stock levels, procurement items, and images',
                'count' => $getCount('products'),
                'details' => $getCount('products') . ' product items',
                'icon' => 'cube-outline',
                'color' => 'blue'
            ],
            'categories_brands' => [
                'label' => 'ক্যাটাগরি ও ব্র্যান্ড (Categories & Brands)',
                'label_en' => 'Categories & Brands',
                'desc' => 'Store department hierarchy, subcategories, and manufacturer brand tags',
                'count' => $getCount('categories') + $getCount('brands'),
                'details' => $getCount('categories') . ' categories, ' . $getCount('brands') . ' brands',
                'icon' => 'pricetags-outline',
                'color' => 'indigo'
            ],
            'customers' => [
                'label' => 'গ্রাহক তথ্য ও একাউন্ট (Customer Profiles)',
                'label_en' => 'Customer Profiles & Accounts',
                'desc' => 'All registered customer profiles, phone numbers, login PINs, and saved addresses',
                'count' => $getCount('customers'),
                'details' => $getCount('customers') . ' customer accounts',
                'icon' => 'people-outline',
                'color' => 'emerald'
            ],
            'hr_payroll' => [
                'label' => 'এইচআর, হাজিরা ও পেরোল (HR & Payroll)',
                'label_en' => 'HR, Attendance & Payroll',
                'desc' => 'Employee staff profiles, daily attendance logs, leaves, and salary vouchers',
                'count' => $getCount('employees') + $getCount('attendances') + $getCount('payrolls'),
                'details' => $getCount('employees') . ' employees, ' . $getCount('attendances') . ' attendance logs, ' . $getCount('payrolls') . ' payrolls',
                'icon' => 'briefcase-outline',
                'color' => 'purple'
            ],
            'purchases_vendors' => [
                'label' => 'সাপ্লায়ার ও স্টক ইন (Suppliers & Stock In)',
                'label_en' => 'Suppliers & Purchases',
                'desc' => 'Supplier profiles, purchase orders, purchase items, and supplier ledger',
                'count' => $getCount('vendors') + $getCount('purchases'),
                'details' => $getCount('vendors') . ' suppliers, ' . $getCount('purchases') . ' purchases, ' . $getCount('vendor_transactions') . ' ledger logs',
                'icon' => 'storefront-outline',
                'color' => 'teal'
            ],
            'logistics' => [
                'label' => 'লজিস্টিকস ও ক্যাশ কালেকশন (Logistics & Allocations)',
                'label_en' => 'Rider Allocations & Collections',
                'desc' => 'Delivery rider union allocations and COD cash collections history',
                'count' => $getCount('dm_allocations') + $getCount('rider_collections'),
                'details' => $getCount('dm_allocations') . ' allocations, ' . $getCount('rider_collections') . ' collections',
                'icon' => 'bicycle-outline',
                'color' => 'orange'
            ],
            'locations' => [
                'label' => 'লোকেশন, জোন ও ওয়্যারহাউস (Locations & Zones)',
                'label_en' => 'Warehouses, Unions, Zones & Points',
                'desc' => 'Delivery points, wards/zones, union areas, and central warehouses',
                'count' => $getCount('points') + $getCount('zones') + $getCount('areas') + $getCount('warehouses'),
                'details' => $getCount('points') . ' points, ' . $getCount('zones') . ' zones, ' . $getCount('areas') . ' areas, ' . $getCount('warehouses') . ' warehouses',
                'icon' => 'map-outline',
                'color' => 'cyan'
            ],
            'staff_users' => [
                'label' => 'স্টাফ ও ডেলিভারি রাইডার একাউন্ট (Staff Accounts)',
                'label_en' => 'Staff & Rider Accounts',
                'desc' => 'General staff, managers, agents, and riders. SUPER ADMIN IS ALWAYS PERMANENTLY PROTECTED.',
                'count' => $getCount('users', "role != 'admin' AND id != 1"),
                'details' => $getCount('users', "role != 'admin' AND id != 1") . ' staff/riders (Super Admin immune)',
                'icon' => 'shield-checkmark-outline',
                'color' => 'rose',
                'safe_notice' => 'সুপার এডমিন একাউন্ট কখনো মুছে ফেলা হবে না (Super Admin is immune)'
            ]
        ];

        return $this->view('admin/settings/cleanup', [
            'title' => 'ডাটা ক্লিনআপ ও রিসেট (Database Data Reset)',
            'counts' => $counts,
            'activeTab' => 'cleanup',
            'success' => $_GET['success'] ?? null,
            'error' => $_GET['error'] ?? null
        ]);
    }

    public function executeCleanup() {
        if (!\Core\Auth::isAdmin()) {
            header('Location: /sodai-dorkar/public/admin/settings/cleanup?error=' . urlencode('অননুমোদিত: কেবল সুপার এডমিন ডাটা ক্লিনআপ চালাতে পারবেন (Only Super Admin can execute data reset)'));
            exit;
        }

        $keyword = trim($_POST['confirm_keyword'] ?? '');
        if ($keyword !== 'RESET') {
            header('Location: /sodai-dorkar/public/admin/settings/cleanup?error=' . urlencode('নিশ্চিতকরণ কোড ভুল হয়েছে। অনুগ্রহ করে সঠিকভাবে RESET টাইপ করুন।'));
            exit;
        }

        $targets = $_POST['targets'] ?? [];
        if (empty($targets) || !is_array($targets)) {
            header('Location: /sodai-dorkar/public/admin/settings/cleanup?error=' . urlencode('কোনো মডিউল নির্বাচন করা হয়নি।'));
            exit;
        }

        $config = require __DIR__ . '/../../config/database.php';
        $db = new \Core\Database($config);
        $pdo = $db->getConnection();

        $cleared = [];

        try {
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

            foreach ($targets as $target) {
                switch ($target) {
                    case 'orders':
                        $pdo->exec("TRUNCATE TABLE order_items");
                        $pdo->exec("TRUNCATE TABLE order_tracking");
                        $pdo->exec("TRUNCATE TABLE orders");
                        $cleared[] = "Orders & Sales Records";
                        break;
                    case 'products':
                        $pdo->exec("TRUNCATE TABLE products");
                        $cleared[] = "Products & Inventory";
                        break;
                    case 'categories_brands':
                        $pdo->exec("TRUNCATE TABLE categories");
                        $pdo->exec("TRUNCATE TABLE brands");
                        $cleared[] = "Categories & Brands";
                        break;
                    case 'customers':
                        $pdo->exec("TRUNCATE TABLE customers");
                        $cleared[] = "Customer Accounts";
                        break;
                    case 'hr_payroll':
                        $pdo->exec("TRUNCATE TABLE payrolls");
                        $pdo->exec("TRUNCATE TABLE attendances");
                        $pdo->exec("TRUNCATE TABLE leave_requests");
                        $pdo->exec("TRUNCATE TABLE employees");
                        $cleared[] = "HR, Employees & Payroll";
                        break;
                    case 'purchases_vendors':
                        $pdo->exec("TRUNCATE TABLE purchase_items");
                        $pdo->exec("TRUNCATE TABLE purchases");
                        $pdo->exec("TRUNCATE TABLE vendor_transactions");
                        $pdo->exec("TRUNCATE TABLE vendors");
                        $cleared[] = "Suppliers & Purchases";
                        break;
                    case 'logistics':
                        $pdo->exec("TRUNCATE TABLE dm_allocations");
                        $pdo->exec("TRUNCATE TABLE rider_collections");
                        $cleared[] = "Rider Allocations & Collections";
                        break;
                    case 'locations':
                        $pdo->exec("TRUNCATE TABLE points");
                        $pdo->exec("TRUNCATE TABLE zones");
                        $pdo->exec("TRUNCATE TABLE areas");
                        $pdo->exec("TRUNCATE TABLE warehouses");
                        $cleared[] = "Locations, Areas & Warehouses";
                        break;
                    case 'staff_users':
                        // STRICT RULE: Super Admin is NEVER deleted
                        $currentUserId = (int)($_SESSION['user_id'] ?? 0);
                        $pdo->exec("DELETE FROM users WHERE role != 'admin' AND id != 1 AND id != {$currentUserId}");
                        $cleared[] = "Staff Accounts (Super Admin Preserved)";
                        break;
                }
            }

            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

            $msg = "নির্বাচিত ডাটা সফলভাবে মুছে ফেলা হয়েছে: " . implode(', ', $cleared);
            header('Location: /sodai-dorkar/public/admin/settings/cleanup?success=' . urlencode($msg));
            exit;
        } catch (\Exception $e) {
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
            header('Location: /sodai-dorkar/public/admin/settings/cleanup?error=' . urlencode('ত্রুটি ঘটেছে: ' . $e->getMessage()));
            exit;
        }
    }
}
