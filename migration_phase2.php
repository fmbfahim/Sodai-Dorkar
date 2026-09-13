<?php
// migration_phase2.php

require_once __DIR__ . '/src/Core/Database.php';
$config = require __DIR__ . '/config/database.php';

try {
    $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['user'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo "Starting Phase 2 Migration...\n";

    // 1. Create Zones (Wards)
    $pdo->exec("CREATE TABLE IF NOT EXISTS zones (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        area_id INT UNSIGNED NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (area_id) REFERENCES areas(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");
    echo "Table 'zones' created.\n";

    // 2. Create Points (Houses/Points)
    $pdo->exec("CREATE TABLE IF NOT EXISTS points (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        zone_id INT UNSIGNED NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (zone_id) REFERENCES zones(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");
    echo "Table 'points' created.\n";

    // 3. Create DM Allocations
    $pdo->exec("CREATE TABLE IF NOT EXISTS dm_allocations (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT UNSIGNED NOT NULL,
        area_id INT UNSIGNED NOT NULL,
        time_slot ENUM('morning', 'afternoon', 'both', 'all_time') DEFAULT 'all_time',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (area_id) REFERENCES areas(id) ON DELETE CASCADE,
        UNIQUE KEY unique_allocation (user_id, area_id)
    ) ENGINE=InnoDB");
    echo "Table 'dm_allocations' created.\n";

    echo "Migration Completed Successfully!\n";

} catch (PDOException $e) {
    die("Migration Failed: " . $e->getMessage() . "\n");
}
