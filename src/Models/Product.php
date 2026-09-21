<?php

namespace Models;

use Core\Database;

class Product {
    protected $db;

    public function __construct() {
        $config = require __DIR__ . '/../../config/database.php';
        $this->db = new Database($config);
        $this->ensureSchema();
    }

    /**
     * Automatically ensure all required columns exist in products table
     */
    public function ensureSchema() {
        static $ensured = false;
        if ($ensured) return;
        $ensured = true;

        try {
            $stmt = $this->db->query("SHOW COLUMNS FROM products");
            $existingCols = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            $columnsToAdd = [
                'vendor_id'           => "ALTER TABLE products ADD COLUMN vendor_id INT UNSIGNED NULL",
                'category_id'         => "ALTER TABLE products ADD COLUMN category_id INT UNSIGNED NULL AFTER vendor_id",
                'brand_id'            => "ALTER TABLE products ADD COLUMN brand_id INT UNSIGNED NULL AFTER category_id",
                'sku'                 => "ALTER TABLE products ADD COLUMN sku VARCHAR(50) NULL AFTER name",
                'description'         => "ALTER TABLE products ADD COLUMN description TEXT NULL AFTER sku",
                'tags'                => "ALTER TABLE products ADD COLUMN tags TEXT NULL AFTER description",
                'buy_price'           => "ALTER TABLE products ADD COLUMN buy_price DECIMAL(10,2) NOT NULL DEFAULT 0.00",
                'regular_price'       => "ALTER TABLE products ADD COLUMN regular_price DECIMAL(10,2) NULL AFTER buy_price",
                'discount_type'       => "ALTER TABLE products ADD COLUMN discount_type VARCHAR(20) DEFAULT 'none' AFTER regular_price",
                'discount_value'      => "ALTER TABLE products ADD COLUMN discount_value DECIMAL(10,2) DEFAULT 0.00 AFTER discount_type",
                'sell_price'          => "ALTER TABLE products ADD COLUMN sell_price DECIMAL(10,2) NOT NULL DEFAULT 0.00",
                'stock_qty'           => "ALTER TABLE products ADD COLUMN stock_qty DECIMAL(10,3) DEFAULT 0.000",
                'unit_type'           => "ALTER TABLE products ADD COLUMN unit_type VARCHAR(50) DEFAULT 'piece'",
                'base_unit'           => "ALTER TABLE products ADD COLUMN base_unit VARCHAR(30) DEFAULT 'pcs'",
                'purchase_unit'       => "ALTER TABLE products ADD COLUMN purchase_unit VARCHAR(50) NULL",
                'purchase_unit_qty'   => "ALTER TABLE products ADD COLUMN purchase_unit_qty DECIMAL(10,3) DEFAULT 1.000",
                'selling_unit'        => "ALTER TABLE products ADD COLUMN selling_unit VARCHAR(50) NULL",
                'unit_variants_json'  => "ALTER TABLE products ADD COLUMN unit_variants_json LONGTEXT NULL",
                'image_path'          => "ALTER TABLE products ADD COLUMN image_path VARCHAR(255) NULL",
                'is_verified'         => "ALTER TABLE products ADD COLUMN is_verified TINYINT(1) DEFAULT 0",
                'availability_status' => "ALTER TABLE products ADD COLUMN availability_status VARCHAR(30) DEFAULT 'pending'",
                'demand_percentage'   => "ALTER TABLE products ADD COLUMN demand_percentage INT DEFAULT 0",
            ];

            foreach ($columnsToAdd as $col => $alterSql) {
                if (!in_array($col, $existingCols)) {
                    try {
                        $this->db->query($alterSql);
                        $existingCols[] = $col;
                    } catch (\Throwable $e) {
                        error_log("Product::ensureSchema warning for '{$col}': " . $e->getMessage());
                    }
                }
            }
        } catch (\Throwable $e) {
            error_log("Product::ensureSchema failed: " . $e->getMessage());
        }
    }

    public function all($filters = []) {
        $sql = "SELECT products.*, vendors.name as vendor_name, categories.name as category_name, brands.name as brand_name 
                FROM products 
                LEFT JOIN vendors ON products.vendor_id = vendors.id 
                LEFT JOIN categories ON products.category_id = categories.id
                LEFT JOIN brands ON products.brand_id = brands.id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $term = '%' . trim($filters['search']) . '%';
            $sql .= " AND (products.name LIKE ? OR products.sku LIKE ? OR products.description LIKE ? OR products.tags LIKE ? OR vendors.name LIKE ? OR categories.name LIKE ?)";
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        if (!empty($filters['category_id'])) {
            $sql .= " AND products.category_id = ?";
            $params[] = intval($filters['category_id']);
        }

        if (!empty($filters['vendor_id'])) {
            $sql .= " AND products.vendor_id = ?";
            $params[] = intval($filters['vendor_id']);
        }

        if (!empty($filters['stock_status'])) {
            switch ($filters['stock_status']) {
                case 'in_stock':
                    $sql .= " AND products.stock_qty >= 10";
                    break;
                case 'low_stock':
                    $sql .= " AND products.stock_qty > 0 AND products.stock_qty < 10";
                    break;
                case 'out_of_stock':
                    $sql .= " AND products.stock_qty <= 0";
                    break;
            }
        }

        if (!empty($filters['availability_status'])) {
            $sql .= " AND products.availability_status = ?";
            $params[] = $filters['availability_status'];
        }

        $sql .= " ORDER BY products.id DESC";
        return $this->db->query($sql, $params)->fetchAll();
    }

    public function findBySku($sku) {
        $sku = trim($sku);
        if (empty($sku)) return false;
        $stmt = $this->db->query("SELECT * FROM products WHERE sku = ? LIMIT 1", [$sku]);
        return $stmt->fetch();
    }

    public function findByName($name) {
        $name = trim($name);
        if (empty($name)) return false;
        $stmt = $this->db->query("SELECT * FROM products WHERE LOWER(TRIM(name)) = LOWER(?) LIMIT 1", [$name]);
        return $stmt->fetch();
    }

    public function findExisting($sku, $name) {
        if (!empty($sku)) {
            $found = $this->findBySku($sku);
            if ($found) return $found;
        }
        if (!empty($name)) {
            return $this->findByName($name);
        }
        return false;
    }

    public function updateStockAndPrices($id, $data) {
        $stmt = $this->db->query("SHOW COLUMNS FROM products");
        $existingCols = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        $setParts = [
            'buy_price = :buy_price',
            'regular_price = :regular_price',
            'discount_type = :discount_type',
            'discount_value = :discount_value',
            'sell_price = :sell_price',
            'stock_qty = :stock_qty'
        ];
        $params = [
            'buy_price' => $data['buy_price'] ?? 0,
            'regular_price' => !empty($data['regular_price']) ? floatval($data['regular_price']) : null,
            'discount_type' => $data['discount_type'] ?? 'none',
            'discount_value' => floatval($data['discount_value'] ?? 0),
            'sell_price' => $data['sell_price'] ?? 0,
            'stock_qty' => $data['stock_qty'] ?? 0,
            'id' => $id
        ];

        $optional = ['vendor_id', 'category_id', 'brand_id', 'unit_type', 'base_unit'];
        foreach ($optional as $col) {
            if (isset($data[$col]) && in_array($col, $existingCols)) {
                $setParts[] = "{$col} = :{$col}";
                $params[$col] = $data[$col];
            }
        }

        $sql = "UPDATE products SET " . implode(", ", $setParts) . " WHERE id = :id";
        return $this->db->query($sql, $params);
    }

    public function create($data) {
        $this->ensureSchema();

        $stmt = $this->db->query("SHOW COLUMNS FROM products");
        $existingCols = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        // Sanitize SKU to prevent duplicate '' unique constraint violation
        if (isset($data['sku'])) {
            $skuVal = trim($data['sku']);
            $data['sku'] = ($skuVal !== '') ? $skuVal : null;
        }

        $fields = [
            'name'                => $data['name'] ?? '',
            'sku'                 => $data['sku'] ?? null,
            'description'         => $data['description'] ?? '',
            'tags'                => $data['tags'] ?? null,
            'buy_price'           => $data['buy_price'] ?? 0,
            'regular_price'       => !empty($data['regular_price']) ? floatval($data['regular_price']) : null,
            'discount_type'       => $data['discount_type'] ?? 'none',
            'discount_value'      => floatval($data['discount_value'] ?? 0),
            'sell_price'          => $data['sell_price'] ?? 0,
            'stock_qty'           => $data['stock_qty'] ?? 0,
            'vendor_id'           => !empty($data['vendor_id']) ? $data['vendor_id'] : null,
            'category_id'         => !empty($data['category_id']) ? $data['category_id'] : null,
            'brand_id'            => !empty($data['brand_id']) ? $data['brand_id'] : null,
            'image_path'          => $data['image_path'] ?? null,
            'unit_type'           => $data['unit_type'] ?? 'piece',
            'base_unit'           => $data['base_unit'] ?? 'pcs',
            'purchase_unit'       => $data['purchase_unit'] ?? null,
            'purchase_unit_qty'   => !empty($data['purchase_unit_qty']) ? $data['purchase_unit_qty'] : 1.000,
            'selling_unit'        => $data['selling_unit'] ?? null,
            'unit_variants_json'  => !empty($data['unit_variants_json']) ? $data['unit_variants_json'] : null,
            'is_verified'         => $data['is_verified'] ?? 0,
            'availability_status' => $data['availability_status'] ?? 'pending',
            'demand_percentage'   => $data['demand_percentage'] ?? 0
        ];

        $insertCols = [];
        $placeholders = [];
        $params = [];

        foreach ($fields as $col => $val) {
            if (in_array($col, $existingCols)) {
                $insertCols[] = $col;
                $placeholders[] = ":{$col}";
                $params[$col] = $val;
            }
        }

        if (empty($insertCols)) {
            return false;
        }

        $sql = "INSERT INTO products (" . implode(", ", $insertCols) . ") VALUES (" . implode(", ", $placeholders) . ")";
        $this->db->query($sql, $params);
        return $this->db->lastInsertId();
    }

    public function find($id) {
        $stmt = $this->db->query("SELECT * FROM products WHERE id = :id", ['id' => $id]);
        return $stmt->fetch();
    }

    public function update($id, $data) {
        $this->ensureSchema();

        $stmt = $this->db->query("SHOW COLUMNS FROM products");
        $existingCols = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        // Sanitize SKU to prevent duplicate '' unique constraint violation
        if (isset($data['sku'])) {
            $skuVal = trim($data['sku']);
            $data['sku'] = ($skuVal !== '') ? $skuVal : null;
        }

        $fields = [
            'name'                => $data['name'] ?? '',
            'sku'                 => $data['sku'] ?? null,
            'description'         => $data['description'] ?? '',
            'tags'                => $data['tags'] ?? null,
            'buy_price'           => $data['buy_price'] ?? 0,
            'regular_price'       => !empty($data['regular_price']) ? floatval($data['regular_price']) : null,
            'discount_type'       => $data['discount_type'] ?? 'none',
            'discount_value'      => floatval($data['discount_value'] ?? 0),
            'sell_price'          => $data['sell_price'] ?? 0,
            'stock_qty'           => $data['stock_qty'] ?? 0,
            'vendor_id'           => !empty($data['vendor_id']) ? $data['vendor_id'] : null,
            'category_id'         => !empty($data['category_id']) ? $data['category_id'] : null,
            'brand_id'            => !empty($data['brand_id']) ? $data['brand_id'] : null,
            'unit_type'           => $data['unit_type'] ?? 'piece',
            'base_unit'           => $data['base_unit'] ?? 'pcs',
            'purchase_unit'       => $data['purchase_unit'] ?? null,
            'purchase_unit_qty'   => !empty($data['purchase_unit_qty']) ? $data['purchase_unit_qty'] : 1.000,
            'selling_unit'        => $data['selling_unit'] ?? null,
            'unit_variants_json'  => !empty($data['unit_variants_json']) ? $data['unit_variants_json'] : null,
        ];

        if (isset($data['image_path'])) {
            $fields['image_path'] = $data['image_path'];
        }
        if (isset($data['is_verified'])) {
            $fields['is_verified'] = $data['is_verified'];
        }
        if (isset($data['availability_status'])) {
            $fields['availability_status'] = $data['availability_status'];
        }
        if (isset($data['demand_percentage'])) {
            $fields['demand_percentage'] = $data['demand_percentage'];
        }

        $setParts = [];
        $params = ['id' => $id];

        foreach ($fields as $col => $val) {
            if (in_array($col, $existingCols)) {
                $setParts[] = "{$col} = :{$col}";
                $params[$col] = $val;
            }
        }

        if (empty($setParts)) {
            return false;
        }

        $sql = "UPDATE products SET " . implode(", ", $setParts) . " WHERE id = :id";
        return $this->db->query($sql, $params);
    }

    public function delete($id) {
        $this->db->query("DELETE FROM products WHERE id = :id", ['id' => $id]);
    }

    /**
     * Helper to format stock display in base unit and bulk package unit (e.g. 450 kg / 9 বস্তা)
     */
    public static function formatStockDisplay($product) {
        $stock = floatval($product['stock_qty'] ?? 0);
        $baseUnit = $product['base_unit'] ?? 'pcs';
        $purchaseUnit = $product['purchase_unit'] ?? '';
        $purchaseQty = floatval($product['purchase_unit_qty'] ?? 1);

        // Format decimal nicely: 45.000 -> 45, 45.500 -> 45.5
        $formattedStock = (floor($stock) == $stock) ? number_format($stock, 0) : rtrim(rtrim(number_format($stock, 3), '0'), '.');
        $display = $formattedStock . ' ' . $baseUnit;

        if (!empty($purchaseUnit) && $purchaseQty > 1 && $stock >= $purchaseQty) {
            $bulkCount = floor($stock / $purchaseQty);
            $remainder = fmod($stock, $purchaseQty);
            if ($remainder == 0) {
                $display .= " ({$bulkCount} {$purchaseUnit})";
            } else {
                $remFormatted = (floor($remainder) == $remainder) ? number_format($remainder, 0) : rtrim(rtrim(number_format($remainder, 2), '0'), '.');
                $display .= " ({$bulkCount} {$purchaseUnit} + {$remFormatted} {$baseUnit})";
            }
        }

        return $display;
    }

    public static function hasDiscount($product) {
        $regular = floatval($product['regular_price'] ?? 0);
        $sell = floatval($product['sell_price'] ?? 0);
        return ($regular > $sell && $sell > 0);
    }

    public static function getDiscountPercent($product) {
        $regular = floatval($product['regular_price'] ?? 0);
        $sell = floatval($product['sell_price'] ?? 0);
        if ($regular > $sell && $regular > 0) {
            return round((($regular - $sell) / $regular) * 100);
        }
        return 0;
    }

    public static function getDiscountAmount($product) {
        $regular = floatval($product['regular_price'] ?? 0);
        $sell = floatval($product['sell_price'] ?? 0);
        if ($regular > $sell) {
            return $regular - $sell;
        }
        return 0;
    }

    public static function recalculateDemand() {
        $db = new \Core\Database(require __DIR__ . '/../../config/database.php');
        
        // 1. Get total number of orders placed in the last 30 days
        $sqlTotal = "SELECT COUNT(*) as total_orders FROM orders WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) AND status != 'cancelled'";
        $stmtTotal = $db->query($sqlTotal);
        $totalOrders = (int)($stmtTotal->fetch()['total_orders'] ?? 0);

        if ($totalOrders > 0) {
            // 2. Reset demand
            $db->query("UPDATE products SET demand_percentage = 0");

            // 3. Calculate demand per product (percentage of orders containing this product)
            $sqlItems = "SELECT oi.product_id, COUNT(DISTINCT oi.order_id) as order_count 
                         FROM order_items oi
                         JOIN orders o ON oi.order_id = o.id
                         WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) 
                         AND o.status != 'cancelled'
                         GROUP BY oi.product_id";
            $stmtItems = $db->query($sqlItems);
            $items = $stmtItems->fetchAll();

            foreach ($items as $item) {
                $pid = $item['product_id'];
                $count = $item['order_count'];
                // Calculate percentage (max 100)
                $percent = min(100, round(($count / $totalOrders) * 100));
                
                if ($percent > 0) {
                    $db->query("UPDATE products SET demand_percentage = :pct WHERE id = :id", [
                        'pct' => $percent,
                        'id' => $pid
                    ]);
                }
            }
        }
    }

    public static function getImageUrl($imagePath, $base = null) {
        if ($base === null) {
            $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
        }
        $img = trim($imagePath ?? '');
        if (empty($img)) {
            return !empty($base) ? rtrim($base, '/') . '/images/default-product.svg' : '/images/default-product.svg';
        }
        if (strpos($img, 'http://') === 0 || strpos($img, 'https://') === 0) {
            return $img;
        }
        $clean = preg_replace('#^/?(sodai-dorkar/public/|public/)#', '', ltrim($img, '/'));
        return !empty($base) ? rtrim($base, '/') . '/' . $clean : '/' . $clean;
    }
}
