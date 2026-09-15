<?php

namespace Core;

use Core\Database;

class Auth {
    
    /**
     * Get the logged-in user ID
     */
    public static function id() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Check if a user is logged in
     */
    public static function check() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        return !empty($_SESSION['user_id']) && !empty($_SESSION['role']);
    }

    /**
     * Get current user role
     */
    public static function role() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        return $_SESSION['role'] ?? null;
    }

    /**
     * Check if current user is Super Admin
     */
    public static function isAdmin() {
        return self::role() === 'admin';
    }

    /**
     * Check if current user has a specific permission
     * 
     * @param string $permission
     * @return bool
     */
    public static function can($permission) {
        if (!self::check()) {
            return false;
        }

        // Super Admin has all permissions unconditionally
        if (self::isAdmin()) {
            return true;
        }

        $permissions = self::userPermissions();

        // Wildcard permission
        if (in_array('*', $permissions, true)) {
            return true;
        }

        return in_array($permission, $permissions, true);
    }

    /**
     * Get the list of permission keys granted to the current user
     * 
     * @param int|null $userId
     * @return array
     */
    public static function userPermissions($userId = null) {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($userId === null) {
            $userId = $_SESSION['user_id'] ?? null;
            if (!$userId) return [];

            // If session already has cached permissions, use it
            if (isset($_SESSION['permissions']) && is_array($_SESSION['permissions'])) {
                return $_SESSION['permissions'];
            }
        }

        // Fetch from database
        try {
            $config = require __DIR__ . '/../../config/database.php';
            $db = new Database($config);
            $stmt = $db->query("SELECT role, permissions FROM users WHERE id = ?", [$userId]);
            $row = $stmt->fetch();

            if (!$row) return [];

            if ($row['role'] === 'admin') {
                $perms = ['*'];
            } else {
                $perms = json_decode($row['permissions'] ?? '[]', true) ?: [];
                if (empty($perms)) {
                    $perms = self::defaultPermissionsForRole($row['role']);
                }
            }

            if ($userId === ($_SESSION['user_id'] ?? null)) {
                $_SESSION['permissions'] = $perms;
            }

            return $perms;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Refresh current user's session permissions
     */
    public static function refreshSessionPermissions() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        unset($_SESSION['permissions']);
        return self::userPermissions();
    }

    /**
     * Get sensible default permissions for a system role
     */
    public static function defaultPermissionsForRole($role) {
        switch ($role) {
            case 'admin':
                return ['*'];
            case 'manager':
                return [
                    'dashboard', 'products', 'categories_brands', 'orders',
                    'dispatch', 'delivery_men', 'customers', 'vendors_purchases',
                    'locations', 'hr', 'payroll', 'reports'
                ];
            case 'accountant':
                return ['dashboard', 'vendors_purchases', 'payroll', 'reports'];
            case 'agent':
                return ['dashboard', 'orders', 'customers'];
            case 'delivery_man':
                return ['orders'];
            case 'staff':
            default:
                return ['dashboard', 'products', 'orders'];
        }
    }

    /**
     * Master catalog of all permission modules in the system
     */
    public static function allPermissions() {
        return [
            'Core Operations' => [
                'dashboard' => [
                    'label' => 'Dashboard & KPIs',
                    'label_bn' => 'ড্যাশবোর্ড ও সেলস স্ট্যাটাস',
                    'desc' => 'View main analytics, sales graphs, today summary, and order counters',
                    'icon' => 'grid-outline'
                ],
                'orders' => [
                    'label' => 'Orders & POS',
                    'label_bn' => 'অর্ডার ও পজ সেলস',
                    'desc' => 'Manage customer orders, status changes, manual order creation, invoice, POS receipts, and returns',
                    'icon' => 'cart-outline'
                ],
                'dispatch' => [
                    'label' => 'Dispatch Board',
                    'label_bn' => 'ডিসপ্যাচ ও পার্সেল বোর্ড',
                    'desc' => 'Batch parcel dispatching, order packaging checks, and bulk rider assignments',
                    'icon' => 'paper-plane-outline'
                ],
                'customers' => [
                    'label' => 'Customer Management',
                    'label_bn' => 'গ্রাহক ব্যবস্থাপনা',
                    'desc' => 'Customer accounts, purchase history, profile details, and login PIN reset',
                    'icon' => 'people-outline'
                ],
            ],
            'Catalog & Inventory' => [
                'products' => [
                    'label' => 'Products & Procurement',
                    'label_bn' => 'পণ্য ও প্রকিউরমেন্ট',
                    'desc' => 'Add/edit products, verification workflow, availability list, procurement list, and bulk CSV import',
                    'icon' => 'cube-outline'
                ],
                'categories_brands' => [
                    'label' => 'Categories & Brands',
                    'label_bn' => 'ক্যাটাগরি ও ব্র্যান্ড',
                    'desc' => 'Create and modify store departments, subcategories, and product brand tags',
                    'icon' => 'pricetags-outline'
                ],
                'vendors_purchases' => [
                    'label' => 'Suppliers & Stock In',
                    'label_bn' => 'সাপ্লায়ার ও স্টক ইন',
                    'desc' => 'Vendor directories, purchase orders, purchase ledger, and supplier payments',
                    'icon' => 'storefront-outline'
                ],
                'locations' => [
                    'label' => 'Locations & Warehouses',
                    'label_bn' => 'ওয়্যারহাউস ও এরিয়া',
                    'desc' => 'Central warehouses, union areas, delivery zones, and neighborhood points',
                    'icon' => 'map-outline'
                ],
            ],
            'Logistics & HR' => [
                'delivery_men' => [
                    'label' => 'Delivery Riders & Logistics',
                    'label_bn' => 'ডেলিভারি রাইডার ও কালেকশন',
                    'desc' => 'Rider accounts, union area allocations, and COD cash collection tracking',
                    'icon' => 'bicycle-outline'
                ],
                'hr' => [
                    'label' => 'HR & Employee Management',
                    'label_bn' => 'এইচআর ও কর্মচারী',
                    'desc' => 'Employee directory, daily attendance register, leave requests, departments, and designations',
                    'icon' => 'briefcase-outline'
                ],
                'payroll' => [
                    'label' => 'Payroll & Salaries',
                    'label_bn' => 'পেরোল ও বেতন',
                    'desc' => 'Monthly payroll sheet calculation, payslip vouchers, and salary disbursements',
                    'icon' => 'wallet-outline'
                ],
            ],
            'Administration & Control' => [
                'reports' => [
                    'label' => 'Reports & Accounts',
                    'label_bn' => 'রিপোর্ট ও হিসাব লেজার',
                    'desc' => 'Stock alert report, detailed sales reports, and ledger account summaries',
                    'icon' => 'bar-chart-outline'
                ],
                'users' => [
                    'label' => 'User & Permission Control',
                    'label_bn' => 'ব্যবহারকারী ও এক্সেস কন্ট্রোল',
                    'desc' => 'Manage system users, passwords, account status, and assign granular feature permissions',
                    'icon' => 'shield-checkmark-outline'
                ],
                'settings' => [
                    'label' => 'Store Settings & Config',
                    'label_bn' => 'স্টোর সেটিংস ও কনফিগ',
                    'desc' => 'General store settings, delivery charges, packaging units, and e-commerce rules',
                    'icon' => 'settings-outline'
                ],
                'database_reset' => [
                    'label' => 'Data Cleanup & Reset',
                    'label_bn' => 'ডাটা ক্লিনআপ ও রিসেট',
                    'desc' => 'Selective deletion of orders, products, categories, or staff records with Super Admin protection',
                    'icon' => 'trash-bin-outline'
                ],
            ]
        ];
    }
}
