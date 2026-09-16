<?php
// migration_add_category_slug.php

require_once __DIR__ . '/src/Core/Database.php';
$config = require __DIR__ . '/config/database.php';

try {
    $db = new \Core\Database($config);

    // Check if slug column exists in categories
    $check = $db->query("SHOW COLUMNS FROM categories LIKE 'slug'")->fetch();
    if (!$check) {
        $db->query("ALTER TABLE categories ADD COLUMN slug VARCHAR(150) NULL AFTER name");
        $db->query("ALTER TABLE categories ADD INDEX idx_categories_slug (slug)");
        echo "Column 'slug' successfully added to 'categories' table.\n";
    } else {
        echo "Column 'slug' already exists in 'categories' table.\n";
    }

} catch (\Throwable $e) {
    echo "Database check / migration notice: " . $e->getMessage() . "\n";
}
