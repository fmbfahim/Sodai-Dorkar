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

    private static function getDb() {
        static $db = null;
        if ($db === null) {
            $config = require __DIR__ . '/../../config/database.php';
            $db = new Database($config);
        }
        return $db;
    }

    /**
     * Persist customer login via secure cookie (1 year duration)
     */
    public static function persistCustomerLogin($customerId): void {
        try {
            $token = bin2hex(random_bytes(32));
            $db = self::getDb();
            (new \Models\Customer())->ensureSchema();
            $db->query("UPDATE customers SET remember_token = ? WHERE id = ?", [$token, $customerId]);

            $expires = time() + (365 * 86400); // 1 year
            $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
            setcookie('sd_cust_remember', $token, [
                'expires'  => $expires,
                'path'     => '/',
                'domain'   => '',
                'secure'   => $isSecure,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            $_COOKIE['sd_cust_remember'] = $token;
        } catch (\Throwable $e) {
            error_log("persistCustomerLogin error: " . $e->getMessage());
        }
    }

    /**
     * Clear customer persistent login
     */
    public static function clearCustomerLogin(): void {
        try {
            if (!empty($_SESSION['customer_id'])) {
                $db = self::getDb();
                $db->query("UPDATE customers SET remember_token = NULL WHERE id = ?", [$_SESSION['customer_id']]);
            }
            $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
            setcookie('sd_cust_remember', '', [
                'expires'  => time() - 3600,
                'path'     => '/',
                'domain'   => '',
                'secure'   => $isSecure,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            unset($_COOKIE['sd_cust_remember']);
        } catch (\Throwable $e) {
            error_log("clearCustomerLogin error: " . $e->getMessage());
        }
    }

    /**
     * Persist user (admin / rider / agent / staff) login via secure cookie (1 year duration)
     */
    public static function persistUserLogin($userId): void {
        try {
            $token = bin2hex(random_bytes(32));
            $db = self::getDb();
            (new \Models\User())->ensureSchema();
            $db->query("UPDATE users SET remember_token = ? WHERE id = ?", [$token, $userId]);

            $expires = time() + (365 * 86400); // 1 year
            $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
            setcookie('sd_user_remember', $token, [
                'expires'  => $expires,
                'path'     => '/',
                'domain'   => '',
                'secure'   => $isSecure,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            $_COOKIE['sd_user_remember'] = $token;
        } catch (\Throwable $e) {
            error_log("persistUserLogin error: " . $e->getMessage());
        }
    }

    /**
     * Clear user persistent login
     */
    public static function clearUserLogin(): void {
        try {
            if (!empty($_SESSION['user_id'])) {
                $db = self::getDb();
                $db->query("UPDATE users SET remember_token = NULL WHERE id = ?", [$_SESSION['user_id']]);
            }
            $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
            setcookie('sd_user_remember', '', [
                'expires'  => time() - 3600,
                'path'     => '/',
                'domain'   => '',
                'secure'   => $isSecure,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            unset($_COOKIE['sd_user_remember']);
        } catch (\Throwable $e) {
            error_log("clearUserLogin error: " . $e->getMessage());
        }
    }

    /**
     * Check cookies and restore active session if session expired or browser was reopened
     */
    public static function restoreSessionFromCookie($db = null): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $db = $db ?: self::getDb();

        // 1. Restore Customer Session if missing
        if (empty($_SESSION['customer_id']) && !empty($_COOKIE['sd_cust_remember'])) {
            try {
                (new \Models\Customer())->ensureSchema();
                $token = $_COOKIE['sd_cust_remember'];
                $stmt = $db->query("SELECT id, name, status FROM customers WHERE remember_token = ? LIMIT 1", [$token]);
                $cust = $stmt->fetch();
                if ($cust && ($cust['status'] ?? 'active') !== 'inactive') {
                    $_SESSION['customer_id'] = (int)$cust['id'];
                    $_SESSION['customer_name'] = $cust['name'];
                } else {
                    self::clearCustomerLogin();
                }
            } catch (\Throwable $e) {
                // Ignore DB error
            }
        }

        // 2. Restore System User (Admin / Agent / Delivery / Staff) Session if missing
        if (empty($_SESSION['user_id']) && !empty($_COOKIE['sd_user_remember'])) {
            try {
                (new \Models\User())->ensureSchema();
                $token = $_COOKIE['sd_user_remember'];
                $stmt = $db->query("SELECT id, name, username, role, status FROM users WHERE remember_token = ? LIMIT 1", [$token]);
                $u = $stmt->fetch();
                if ($u && ($u['status'] ?? 'active') !== 'inactive') {
                    $_SESSION['user_id'] = (int)$u['id'];
                    $_SESSION['role'] = $u['role'];
                    $_SESSION['name'] = $u['name'];
                    $_SESSION['username'] = $u['username'];
                    $_SESSION['permissions'] = self::userPermissions($u['id']);
                } else {
                    self::clearUserLogin();
                }
            } catch (\Throwable $e) {
                // Ignore DB error
            }
        }
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
                    'dispatch', 'delivery_men', 'agents', 'customers', 'vendors_purchases',
                    'locations', 'hr', 'payroll', 'reports', 'notifications'
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
                'agents' => [
                    'label' => 'Field Agents & Order Desk',
                    'label_bn' => 'ফিল্ড এজেন্ট ও এরিয়া নির্ধারণ',
                    'desc' => 'Agent accounts, assign operating union areas, monitor agent performance and orders',
                    'icon' => 'bag-handle-outline'
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
                'notifications' => [
                    'label' => 'Push Notifications',
                    'label_bn' => 'প্রোমো ও পুশ নোটিফিকেশন',
                    'desc' => 'Manage custom promo push notifications and in-app announcements',
                    'icon' => 'notifications-outline'
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
