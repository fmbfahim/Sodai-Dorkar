<?php

require_once __DIR__ . '/src/Core/Database.php';
$config = require __DIR__ . '/config/database.php';

try {
    $db = new \Core\Database($config);

    // Rename orders_v2 back to orders if it exists
    $stmt = $db->query("SHOW TABLES LIKE 'orders_v2'");
    if ($stmt->rowCount() > 0) {
        $db->query("RENAME TABLE orders_v2 TO orders");
        echo "Renamed orders_v2 to orders.\n";
    }

    // Add FKs to orders
    // Check if FK exists first to avoid error? simpler to just try/catch or assume fresh
    // We'll wrap in try-catch blocks for safety individually

    try {
        $db->query("ALTER TABLE orders ADD CONSTRAINT fk_orders_customers FOREIGN KEY (customer_id) REFERENCES customers(id)");
        echo "Added FK: orders -> customers\n";
    } catch (PDOException $e) { echo "Skip: " . $e->getMessage() . "\n"; }

    try {
        $db->query("ALTER TABLE orders ADD CONSTRAINT fk_orders_users_agent FOREIGN KEY (agent_id) REFERENCES users(id)");
        echo "Added FK: orders -> users (agent)\n";
    } catch (PDOException $e) { echo "Skip: " . $e->getMessage() . "\n"; }

    try {
        $db->query("ALTER TABLE orders ADD CONSTRAINT fk_orders_users_delivery FOREIGN KEY (delivery_man_id) REFERENCES users(id)");
        echo "Added FK: orders -> users (delivery_man)\n";
    } catch (PDOException $e) { echo "Skip: " . $e->getMessage() . "\n"; }

    // Add FKs to order_items
    try {
        $db->query("ALTER TABLE order_items ADD CONSTRAINT fk_items_orders FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE");
        echo "Added FK: order_items -> orders\n";
    } catch (PDOException $e) { echo "Skip: " . $e->getMessage() . "\n"; }

    try {
        $db->query("ALTER TABLE order_items ADD CONSTRAINT fk_items_products FOREIGN KEY (product_id) REFERENCES products(id)");
        echo "Added FK: order_items -> products\n";
    } catch (PDOException $e) { echo "Skip: " . $e->getMessage() . "\n"; }

    echo "Database structure finalized.\n";

} catch (PDOException $e) {
    die("Finalize failed: " . $e->getMessage());
}
