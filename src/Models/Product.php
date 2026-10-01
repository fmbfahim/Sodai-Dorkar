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
                'addons_json'         => "ALTER TABLE products ADD COLUMN addons_json LONGTEXT NULL AFTER unit_variants_json",
                'image_path'          => "ALTER TABLE products ADD COLUMN image_path VARCHAR(255) NULL",
                'is_verified'         => "ALTER TABLE products ADD COLUMN is_verified TINYINT(1) DEFAULT 0",
                'availability_status' => "ALTER TABLE products ADD COLUMN availability_status VARCHAR(30) DEFAULT 'pending'",
                'special_badge'       => "ALTER TABLE products ADD COLUMN special_badge VARCHAR(50) DEFAULT 'none' AFTER availability_status",
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

    private function buildFilterConditions($filters = []) {
        $sql = "";
        $params = [];

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $words = preg_split('/\s+/', $search);
            $searchClauses = [];
            foreach ($words as $w) {
                if (mb_strlen($w) > 0) {
                    $term = '%' . $w . '%';
                    $searchClauses[] = "(products.name LIKE ? OR products.sku LIKE ? OR products.description LIKE ? OR products.tags LIKE ? OR vendors.name LIKE ? OR categories.name LIKE ?)";
                    $params[] = $term;
                    $params[] = $term;
                    $params[] = $term;
                    $params[] = $term;
                    $params[] = $term;
                    $params[] = $term;
                }
            }
            if (!empty($searchClauses)) {
                $sql .= " AND (" . implode(" AND ", $searchClauses) . ")";
            }
        }

        if (!empty($filters['category_id'])) {
            $catId = intval($filters['category_id']);
            $catIds = [$catId];
            try {
                $stmt = $this->db->query("SELECT id, parent_id FROM categories");
                $allCats = $stmt->fetchAll();
                $added = true;
                while ($added) {
                    $added = false;
                    foreach ($allCats as $c) {
                        $cId = (int)$c['id'];
                        $pId = !empty($c['parent_id']) ? (int)$c['parent_id'] : 0;
                        if (in_array($pId, $catIds, true) && !in_array($cId, $catIds, true)) {
                            $catIds[] = $cId;
                            $added = true;
                        }
                    }
                }
            } catch (\Throwable $e) {
                error_log("Failed resolving category hierarchy: " . $e->getMessage());
            }

            $placeholders = implode(',', array_fill(0, count($catIds), '?'));
            $sql .= " AND products.category_id IN ({$placeholders})";
            foreach ($catIds as $cid) {
                $params[] = $cid;
            }
        }

        if (!empty($filters['vendor_id'])) {
            if ($filters['vendor_id'] === 'none') {
                $sql .= " AND (products.vendor_id IS NULL OR products.vendor_id = 0)";
            } else {
                $sql .= " AND products.vendor_id = ?";
                $params[] = intval($filters['vendor_id']);
            }
        }

        if (!empty($filters['stock_status'])) {
            switch ($filters['stock_status']) {
                case 'in_stock':
                    $sql .= " AND COALESCE(products.stock_qty, 0) >= 10";
                    break;
                case 'low_stock':
                    $sql .= " AND products.stock_qty > 0 AND products.stock_qty < 10";
                    break;
                case 'out_of_stock':
                    $sql .= " AND (products.stock_qty IS NULL OR products.stock_qty <= 0)";
                    break;
            }
        }

        if (!empty($filters['availability_status'])) {
            if ($filters['availability_status'] === 'pending') {
                $sql .= " AND (products.availability_status = 'pending' OR products.availability_status IS NULL OR products.availability_status = '')";
            } elseif ($filters['availability_status'] === 'in_stock') {
                $sql .= " AND products.availability_status = 'in_stock'";
            } elseif ($filters['availability_status'] === 'out_of_stock') {
                $sql .= " AND products.availability_status = 'out_of_stock'";
            } else {
                $sql .= " AND products.availability_status = ?";
                $params[] = $filters['availability_status'];
            }
        }

        if (isset($filters['is_verified']) && $filters['is_verified'] !== '') {
            $sql .= " AND products.is_verified = ?";
            $params[] = intval($filters['is_verified']);
        }

        return [$sql, $params];
    }

    public function count($filters = []) {
        list($whereSql, $params) = $this->buildFilterConditions($filters);
        $sql = "SELECT COUNT(*) as total 
                FROM products 
                LEFT JOIN vendors ON products.vendor_id = vendors.id 
                LEFT JOIN categories ON products.category_id = categories.id 
                LEFT JOIN brands ON products.brand_id = brands.id 
                WHERE 1=1" . $whereSql;
        $row = $this->db->query($sql, $params)->fetch();
        return (int)($row['total'] ?? 0);
    }

    public function all($filters = [], $limit = null, $offset = null) {
        list($whereSql, $params) = $this->buildFilterConditions($filters);
        $sql = "SELECT products.*, vendors.name as vendor_name, categories.name as category_name, brands.name as brand_name 
                FROM products 
                LEFT JOIN vendors ON products.vendor_id = vendors.id 
                LEFT JOIN categories ON products.category_id = categories.id
                LEFT JOIN brands ON products.brand_id = brands.id
                WHERE 1=1" . $whereSql . " ORDER BY products.id DESC";

        if ($limit !== null) {
            $sql .= " LIMIT " . intval($limit);
            if ($offset !== null) {
                $sql .= " OFFSET " . intval($offset);
            }
        }
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
            'addons_json'         => !empty($data['addons_json']) ? $data['addons_json'] : null,
            'is_verified'         => $data['is_verified'] ?? 0,
            'availability_status' => $data['availability_status'] ?? 'pending',
            'special_badge'       => !empty($data['special_badge']) ? $data['special_badge'] : 'none',
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
        if (isset($data['addons_json'])) {
            $fields['addons_json'] = !empty($data['addons_json']) ? $data['addons_json'] : null;
        }
        if (isset($data['special_badge'])) {
            $fields['special_badge'] = $data['special_badge'];
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

    /**
     * Resolves product criteria:
     * - Chicken / Poultry Dressing (ড্রেসিং)
     * - Fish Cutting (মাছ কাটিং)
     * - Meat Cutting (মাংস কাটিং)
     * - Explicit Addons (Database addons_json)
     * - BOGO Offers (Buy 1 Get 1)
     */
    public static function getCriteriaData($product, $locale = 'bn') {
        if (!is_array($product)) {
            return [
                'has_criteria' => false,
                'has_addons' => false,
                'is_bogo' => false,
                'is_fish' => false,
                'is_meat' => false,
                'criteria_type' => 'none',
                'badge_label' => '',
                'button_label' => '',
                'addons' => []
            ];
        }

        // 1. Explicit addons in database
        $addons = [];
        if (!empty($product['addons_json'])) {
            $decoded = is_string($product['addons_json']) ? json_decode($product['addons_json'], true) : $product['addons_json'];
            if (is_array($decoded)) {
                $addons = $decoded;
            }
        }

        // 2. Check if product is BOGO (Buy 1 Get 1)
        $specialBadge = $product['special_badge'] ?? 'none';
        $nameLower = mb_strtolower($product['name'] ?? '', 'UTF-8');
        $tagsLower = mb_strtolower($product['tags'] ?? '', 'UTF-8');

        $isBogo = ($specialBadge === 'bogo')
            || (strpos($tagsLower, 'bogo') !== false)
            || (strpos($tagsLower, '১+১') !== false)
            || (strpos($nameLower, 'bogo') !== false)
            || (strpos($nameLower, '১+১') !== false)
            || (strpos($nameLower, 'buy 1 get 1') !== false);

        // 3. Category Name and Slug if available
        $catName = mb_strtolower($product['category_name'] ?? '', 'UTF-8');
        $catSlug = mb_strtolower($product['category_slug'] ?? $product['slug'] ?? '', 'UTF-8');

        // 4. Check if product is Fish (মাছ)
        $fishRegex = '/(মাছ|ইলিশ|রুই|কাতলা|পাঙ্গাশ|পাঙ্গাস|তেলাপিয়া|তেলাপিয়া|চিংড়ি|চিংড়ি|বোয়াল|বোয়াল|ট্যাংরা|টেংরা|মাগুর|শিং|কৈ|পাবদা|কোরাল|রূপচাঁদা|রূপচাদা|আইড়|আইড়|বাইলা|বেলে|মলা|কাচকি|বাটা|শোল|টাকি|বাইম|ফলি|মেনি|চিতল|পোয়া|পোয়া|লইট্টা|শুঁটকি|শুটকি|fish|seafood|prawn|shrimp|hilsa|salmon)/iu';
        $isFish = ($specialBadge === 'fresh_catch')
            || preg_match($fishRegex, $nameLower)
            || (!empty($catName) && preg_match('/(মাছ|fish|seafood)/iu', $catName))
            || (!empty($catSlug) && preg_match('/(fish|seafood)/iu', $catSlug));

        // 5. Check if product is Poultry / Meat (মুরগি / মাংস)
        $meatRegex = '/(মুরগি|মুরগী|চিকেন|ব্রয়লার|ব্রয়লার|সোনালী|সোনালি|কক|লেয়ার|লেয়ার|হাঁস|গরু|খাসি|ছাগল|বিফ|মাটন|রোস্ট|কলিজা|গিলা|মাথা|chicken|poultry|beef|mutton|meat)/iu';
        $isMeat = ($specialBadge === 'halal_meat')
            || preg_match($meatRegex, $nameLower)
            || (!empty($catName) && preg_match('/(মাংস|মুরগি|chicken|meat|beef|mutton)/iu', $catName))
            || (!empty($catSlug) && preg_match('/(meat|chicken|beef|mutton)/iu', $catSlug));

        // 6. If no explicit addons configured in database, provide smart default cutting/dressing criteria!
        if (empty($addons) || !is_array($addons) || count($addons) === 0) {
            if ($isFish) {
                $addons = [
                    ['name' => ($locale === 'bn' ? 'আস্ত নাড়ি-ভুঁড়ি ও আঁশ পরিষ্কার' : 'Whole Cleaned (Scales & Guts Removed)'), 'price' => 0, 'is_default' => 1],
                    ['name' => ($locale === 'bn' ? 'কারি কাট / টুকরো পিস' : 'Curry Cut / Medium Slices'), 'price' => 0, 'is_default' => 0],
                    ['name' => ($locale === 'bn' ? 'মাথা আলাদা ও পিস কাট' : 'Head Separate & Slices'), 'price' => 0, 'is_default' => 0],
                    ['name' => ($locale === 'bn' ? 'আস্ত মাছ দিন (না কেটে)' : 'Intact Whole Fish (Uncut)'), 'price' => 0, 'is_default' => 0],
                ];
            } elseif ($isMeat) {
                $isChicken = preg_match('/(মুরগি|মুরগী|চিকেন|ব্রয়লার|ব্রয়লার|সোনালী|সোনালি|কক|লেয়ার|লেয়ার|হাঁস|chicken)/iu', $nameLower)
                             || preg_match('/(chicken|মুরগি)/iu', $catName);
                if ($isChicken) {
                    $addons = [
                        ['name' => ($locale === 'bn' ? 'চামড়া সহ কারি কাট' : 'Curry Cut with Skin'), 'price' => 0, 'is_default' => 1],
                        ['name' => ($locale === 'bn' ? 'চামড়া ছাড়া কারি কাট' : 'Skinless Curry Cut'), 'price' => 0, 'is_default' => 0],
                        ['name' => ($locale === 'bn' ? 'রোস্ট সাইজ (৪ টুকরা)' : 'Roast Cut (4 Pcs)'), 'price' => 0, 'is_default' => 0],
                        ['name' => ($locale === 'bn' ? 'চামড়া সহ আস্ত ড্রেসিং' : 'Whole Dressing with Skin'), 'price' => 0, 'is_default' => 0],
                        ['name' => ($locale === 'bn' ? 'চামড়া ছাড়া আস্ত ড্রেসিং' : 'Skinless Whole Dressing'), 'price' => 0, 'is_default' => 0],
                    ];
                } else {
                    $addons = [
                        ['name' => ($locale === 'bn' ? 'নিয়মিত কারি কাট (হাড় সহ)' : 'Regular Curry Cut (Bone-in)'), 'price' => 0, 'is_default' => 1],
                        ['name' => ($locale === 'bn' ? 'ছোট পিস / বিরিয়ানি কাট' : 'Small Cut / Biryani Cut'), 'price' => 0, 'is_default' => 0],
                        ['name' => ($locale === 'bn' ? 'চর্বি ছাড়িয়ে মাঝারি পিস' : 'Fat Trimmed Medium Cut'), 'price' => 0, 'is_default' => 0],
                        ['name' => ($locale === 'bn' ? 'আস্ত মাংস (না কেটে)' : 'Whole Meat Piece (Uncut)'), 'price' => 0, 'is_default' => 0],
                    ];
                }
            }
        }

        $hasAddons = !empty($addons) && is_array($addons) && count($addons) > 0;
        $hasCriteria = $hasAddons || $isBogo;

        // Visual labels
        $badgeLabel = '';
        $buttonLabel = ($locale === 'bn' ? 'ব্যাগে যোগ করুন' : 'Add to Bag');
        $criteriaType = 'none';

        if ($isBogo && $hasAddons) {
            $criteriaType = 'both';
            $badgeLabel = ($locale === 'bn' ? '🎁 ১+১ ফ্রি ও কাটিং সুবিধা' : '🎁 BOGO & Cut Option');
            $buttonLabel = ($locale === 'bn' ? '🎁 ১+১ ও কাটিং পছন্দ করুন' : '🎁 BOGO & Choose Cut');
        } elseif ($isBogo) {
            $criteriaType = 'bogo';
            $badgeLabel = ($locale === 'bn' ? '🎁 ১+১ ফ্রি' : '🎁 Buy 1 Get 1 Free');
            $buttonLabel = ($locale === 'bn' ? '🎁 ১+১ অফার সহ নিন' : '🎁 Get BOGO Offer');
        } elseif ($hasAddons) {
            if ($isFish) {
                $criteriaType = 'fish_cutting';
                $badgeLabel = ($locale === 'bn' ? '🔪 মাছ কাটিং সুবিধা' : '🔪 Fish Cut Option');
                $buttonLabel = ($locale === 'bn' ? '🔪 কাটিং পছন্দ করুন' : '🔪 Choose Cut');
            } elseif ($isMeat) {
                $criteriaType = 'dressing';
                $badgeLabel = ($locale === 'bn' ? '🔪 ড্রেসিং সুবিধা' : '🔪 Dressing Option');
                $buttonLabel = ($locale === 'bn' ? '🔪 ড্রেসিং পছন্দ করুন' : '🔪 Choose Dressing');
            } else {
                $criteriaType = 'custom_addons';
                $badgeLabel = ($locale === 'bn' ? '🔪 কাটিং/ড্রেসিং সুবিধা' : '🔪 Options Available');
                $buttonLabel = ($locale === 'bn' ? '🔪 অপশন পছন্দ করুন' : '🔪 Choose Options');
            }
        }

        return [
            'has_criteria' => $hasCriteria,
            'has_addons' => $hasAddons,
            'is_bogo' => $isBogo,
            'is_fish' => $isFish,
            'is_meat' => $isMeat,
            'criteria_type' => $criteriaType,
            'badge_label' => $badgeLabel,
            'button_label' => $buttonLabel,
            'addons' => $addons
        ];
    }
}
