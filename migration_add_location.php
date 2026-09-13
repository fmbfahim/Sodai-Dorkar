<?php

require_once __DIR__ . '/src/Core/Database.php';
$config = require __DIR__ . '/config/database.php';

try {
    $db = new \Core\Database($config);

    echo "Adding latitude and longitude to customers table...\n";
    $db->query("ALTER TABLE customers 
                ADD COLUMN latitude DECIMAL(10, 8) NULL DEFAULT NULL AFTER address_details,
                ADD COLUMN longitude DECIMAL(11, 8) NULL DEFAULT NULL AFTER latitude");

    echo "Adding latitude and longitude to orders table...\n";
    $db->query("ALTER TABLE orders 
                ADD COLUMN latitude DECIMAL(10, 8) NULL DEFAULT NULL AFTER delivery_address,
                ADD COLUMN longitude DECIMAL(11, 8) NULL DEFAULT NULL AFTER latitude");

    echo "Migration completed successfully!\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Columns already exist.\n";
    } else {
        die("Migration failed: " . $e->getMessage());
    }
}
