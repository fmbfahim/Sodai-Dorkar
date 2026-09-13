<?php
require_once __DIR__ . '/src/Core/Database.php';
$config = require __DIR__ . '/config/database.php';

use Core\Database;

try {
    $db = new Database($config);
    $pdo = $db->getConnection();

    $sql = "CREATE TABLE IF NOT EXISTS vendor_transactions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        vendor_id INT NOT NULL,
        type ENUM('purchase', 'payment', 'opening_balance') NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        ref_id INT DEFAULT NULL,
        transaction_date DATE NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (vendor_id) REFERENCES vendors(id) ON DELETE CASCADE
    )";

    $pdo->exec($sql);
    echo "Table 'vendor_transactions' created successfully.";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
