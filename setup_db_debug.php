<?php

require_once __DIR__ . '/src/Core/Database.php';
$config = require __DIR__ . '/config/database.php';

try {
    // Connect without DB name first to create it if it doesn't exist
    $pdo = new PDO("mysql:host={$config['host']};port={$config['port']}", $config['user'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $dbname = $config['dbname'];
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database `$dbname` created or already exists.\n";
    
    // Now connect to the specific DB
    $db = new \Core\Database($config);

    // DROP Tables individually to ensure they are gone
    $db->query("SET FOREIGN_KEY_CHECKS=0");
    $tables = ['order_items', 'orders', 'customers', 'products', 'areas', 'warehouses', 'vendors', 'users'];
    foreach ($tables as $table) {
        $db->query("DROP TABLE IF EXISTS $table");
    }
    $db->query("SET FOREIGN_KEY_CHECKS=1");
    
    // Users Table
    $db->query("CREATE TABLE IF NOT EXISTS users (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'agent', 'delivery_man') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    echo "Users table created.\n";
    
    // Create Admin User if not exists
    $password = password_hash('admin123', PASSWORD_BCRYPT);
    $stmt = $db->query("SELECT id FROM users WHERE username = 'admin'");
    if ($stmt->rowCount() == 0) {
        $db->query("INSERT INTO users (name, username, password, role) VALUES ('Super Admin', 'admin', '$password', 'admin')");
        echo "Admin user created (username: admin, password: admin123)\n";
    }

    // Warehouses
    $db->query("CREATE TABLE IF NOT EXISTS warehouses (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        location VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    echo "Warehouses table created.\n";

    // Areas
    $db->query("CREATE TABLE IF NOT EXISTS areas (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        warehouse_id INT UNSIGNED,
        FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE SET NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    echo "Areas table created.\n";

    // Vendors
    $db->query("CREATE TABLE IF NOT EXISTS vendors (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        contact VARCHAR(100),
        address TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    echo "Vendors table created.\n";

    // Products
    $db->query("CREATE TABLE IF NOT EXISTS products (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        vendor_id INT UNSIGNED,
        name VARCHAR(255) NOT NULL,
        sku VARCHAR(50) UNIQUE,
        description TEXT,
        buy_price DECIMAL(10, 2) NOT NULL,
        sell_price DECIMAL(10, 2) NOT NULL,
        stock_qty INT DEFAULT 0,
        image_path VARCHAR(255),
        FOREIGN KEY (vendor_id) REFERENCES vendors(id) ON DELETE SET NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    echo "Products table created.\n";

    // Customers with detailed demographics
    $db->query("CREATE TABLE IF NOT EXISTS customers (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        unique_code VARCHAR(20) UNIQUE NOT NULL,
        name VARCHAR(255) NOT NULL,
        phone VARCHAR(20),
        area_id INT UNSIGNED,
        address_details TEXT,
        demographics_json JSON COMMENT 'Stores kids, elderly, expatriates counts etc',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (area_id) REFERENCES areas(id) ON DELETE SET NULL
    ) ENGINE=InnoDB");
    echo "Customers table created.\n";

    // Orders
    $db->query("CREATE TABLE IF NOT EXISTS orders_v2 (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        customer_id INT UNSIGNED NOT NULL,
        agent_id INT UNSIGNED, /* User who took the order */
        delivery_man_id INT UNSIGNED, /* User who delivers */
        status ENUM('pending', 'processing', 'packed', 'out_for_delivery', 'delivered', 'cancelled') DEFAULT 'pending',
        total_amount DECIMAL(10, 2) NOT NULL,
        delivery_address TEXT,
        contact_number VARCHAR(20),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    echo "Orders table created.\n";

    // Order Items
    $db->query("CREATE TABLE IF NOT EXISTS order_items (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        order_id INT UNSIGNED NOT NULL,
        product_id INT UNSIGNED NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10, 2) NOT NULL /* Price at moment of sale */
    ) ENGINE=InnoDB");
    echo "Order Items table created.\n";

    echo "All tables created successfully.\n";

} catch (PDOException $e) {
    die("Setup failed: " . $e->getMessage());
}
