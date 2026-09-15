<?php
// migration_rbac_and_cleanup.php

require_once __DIR__ . '/src/Core/Database.php';
$config = require __DIR__ . '/config/database.php';

try {
    $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['user'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo "Starting RBAC & Data Cleanup Migration...\n";

    // 1. Add permissions column to users table if not exists
    $cols = $pdo->query("SHOW COLUMNS FROM users LIKE 'permissions'")->fetchAll();
    if (empty($cols)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN permissions TEXT NULL AFTER role");
        echo "Added 'permissions' column to users table.\n";
    } else {
        echo "'permissions' column already exists.\n";
    }

    // 2. Set Super Admin permissions to ['*']
    $pdo->exec("UPDATE users SET permissions = '[\"*\"]' WHERE role = 'admin' AND (permissions IS NULL OR permissions = '' OR permissions = '[]')");
    echo "Super admin permissions initialized to ['*'].\n";

    // 3. Set default permissions for existing users based on roles if permissions is null
    $users = $pdo->query("SELECT id, role, permissions FROM users WHERE permissions IS NULL OR permissions = ''")->fetchAll();
    foreach ($users as $u) {
        $perms = [];
        switch ($u['role']) {
            case 'admin':
                $perms = ['*'];
                break;
            case 'manager':
                $perms = ['dashboard', 'products', 'categories_brands', 'orders', 'dispatch', 'delivery_men', 'customers', 'vendors_purchases', 'locations', 'hr', 'payroll', 'reports'];
                break;
            case 'accountant':
                $perms = ['dashboard', 'vendors_purchases', 'payroll', 'reports'];
                break;
            case 'agent':
                $perms = ['dashboard', 'orders', 'customers'];
                break;
            case 'delivery_man':
                $perms = ['orders'];
                break;
            case 'staff':
            default:
                $perms = ['dashboard', 'products', 'orders'];
                break;
        }
        $stmt = $pdo->prepare("UPDATE users SET permissions = ? WHERE id = ?");
        $stmt->execute([json_encode($perms), $u['id']]);
    }
    echo "Initialized default permissions for existing staff accounts.\n";

    echo "Migration Completed Successfully!\n";

} catch (PDOException $e) {
    die("Migration Failed: " . $e->getMessage() . "\n");
}
