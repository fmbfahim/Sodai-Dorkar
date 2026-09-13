<?php

require_once __DIR__ . '/src/Core/Database.php';
$config = require __DIR__ . '/config/database.php';

try {
    $db = new \Core\Database($config);

    // Add FKs to orders
    $db->query("ALTER TABLE orders ADD CONSTRAINT fk_orders_customers FOREIGN KEY (customer_id) REFERENCES customers(id)");
    echo "Added FK: orders -> customers\n";

    $db->query("ALTER TABLE orders ADD CONSTRAINT fk_orders_users_agent FOREIGN KEY (agent_id) REFERENCES users(id)");
    echo "Added FK: orders -> users (agent)\n";

    $db->query("ALTER TABLE orders ADD CONSTRAINT fk_orders_users_delivery FOREIGN KEY (delivery_man_id) REFERENCES users(id)");
    echo "Added FK: orders -> users (delivery_man)\n";

    // Add FKs to order_items
    $db->query("ALTER TABLE order_items ADD CONSTRAINT fk_items_orders FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE");
    echo "Added FK: order_items -> orders\n";

    $db->query("ALTER TABLE order_items ADD CONSTRAINT fk_items_products FOREIGN KEY (product_id) REFERENCES products(id)");
    echo "Added FK: order_items -> products\n";

    echo "All Foreign Keys added successfully.\n";

} catch (PDOException $e) {
    die("FK Setup failed: " . $e->getMessage());
}
