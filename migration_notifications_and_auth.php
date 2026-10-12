<?php
// migration_notifications_and_auth.php

require_once __DIR__ . '/src/Core/Database.php';
$config = require __DIR__ . '/config/database.php';

try {
    $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['user'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo "Running Migration: Persistent Auth & Push Notifications...\n";

    // 1. Add remember_token to customers if missing
    $cols = $pdo->query("SHOW COLUMNS FROM customers")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('remember_token', $cols)) {
        $pdo->exec("ALTER TABLE customers ADD COLUMN remember_token VARCHAR(255) NULL AFTER status");
        echo "Added 'remember_token' to customers table.\n";
    }

    // 2. Add remember_token to users if missing
    $cols = $pdo->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('remember_token', $cols)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN remember_token VARCHAR(255) NULL AFTER permissions");
        echo "Added 'remember_token' to users table.\n";
    }

    // 3. Create push_notifications table
    $pdo->exec("CREATE TABLE IF NOT EXISTS push_notifications (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        promo_code VARCHAR(50) NULL,
        badge_text VARCHAR(100) NULL,
        image_url VARCHAR(500) NULL,
        action_url VARCHAR(500) DEFAULT '/shop',
        target_audience ENUM('all', 'registered', 'guests') DEFAULT 'all',
        status ENUM('active', 'draft', 'expired') DEFAULT 'active',
        sent_count INT UNSIGNED DEFAULT 0,
        views_count INT UNSIGNED DEFAULT 0,
        clicks_count INT UNSIGNED DEFAULT 0,
        expires_at DATETIME NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_status (status),
        INDEX idx_created (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "Table 'push_notifications' ensured.\n";

    // 4. Create push_subscriptions table
    $pdo->exec("CREATE TABLE IF NOT EXISTS push_subscriptions (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        customer_id INT UNSIGNED NULL,
        endpoint TEXT NOT NULL,
        keys_p256dh VARCHAR(255) NULL,
        keys_auth VARCHAR(255) NULL,
        user_agent TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_customer (customer_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "Table 'push_subscriptions' ensured.\n";

    echo "Migration Completed Successfully!\n";

} catch (PDOException $e) {
    die("Migration Failed: " . $e->getMessage() . "\n");
}
