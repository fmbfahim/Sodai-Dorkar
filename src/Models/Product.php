<?php

namespace Models;

use Core\Database;

class Product {
    protected $db;

    public function __construct() {
        $config = require __DIR__ . '/../../config/database.php';
        $this->db = new Database($config);
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
            $sql .= " AND (products.name LIKE ? OR products.sku LIKE ? OR products.description LIKE ? OR vendors.name LIKE ? OR categories.name LIKE ?)";
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
        $sql = "UPDATE products SET 
                buy_price = :buy_price, 
                regular_price = :regular_price, 
                discount_type = :discount_type, 
                discount_value = :discount_value, 
                sell_price = :sell_price, 
                stock_qty = :stock_qty";
        $params = [
            'buy_price' => $data['buy_price'] ?? 0,
            'regular_price' => !empty($data['regular_price']) ? floatval($data['regular_price']) : null,
            'discount_type' => $data['discount_type'] ?? 'none',
            'discount_value' => floatval($data['discount_value'] ?? 0),
            'sell_price' => $data['sell_price'] ?? 0,
            'stock_qty' => $data['stock_qty'] ?? 0,
            'id' => $id
        ];

        if (!empty($data['vendor_id'])) {
            $sql .= ", vendor_id = :vendor_id";
            $params['vendor_id'] = $data['vendor_id'];
        }
        if (!empty($data['category_id'])) {
            $sql .= ", category_id = :category_id";
            $params['category_id'] = $data['category_id'];
        }
        if (!empty($data['brand_id'])) {
            $sql .= ", brand_id = :brand_id";
            $params['brand_id'] = $data['brand_id'];
        }
        if (!empty($data['unit_type'])) {
            $sql .= ", unit_type = :unit_type";
            $params['unit_type'] = $data['unit_type'];
        }
        if (!empty($data['base_unit'])) {
            $sql .= ", base_unit = :base_unit";
            $params['base_unit'] = $data['base_unit'];
        }

        $sql .= " WHERE id = :id";
        return $this->db->query($sql, $params);
    }

    public function create($data) {
        $sql = "INSERT INTO products (
                    name, sku, description, buy_price, regular_price, discount_type, discount_value, sell_price, stock_qty, 
                    vendor_id, category_id, brand_id, image_path,
                    unit_type, base_unit, purchase_unit, purchase_unit_qty, selling_unit, unit_variants_json,
                    is_verified, availability_status, demand_percentage
                ) VALUES (
                    :name, :sku, :description, :buy_price, :regular_price, :discount_type, :discount_value, :sell_price, :stock_qty, 
                    :vendor_id, :category_id, :brand_id, :image_path,
                    :unit_type, :base_unit, :purchase_unit, :purchase_unit_qty, :selling_unit, :unit_variants_json,
                    :is_verified, :availability_status, :demand_percentage
                )";
        
        $this->db->query($sql, [
            'name' => $data['name'],
            'sku' => $data['sku'],
            'description' => $data['description'] ?? '',
            'buy_price' => $data['buy_price'] ?? 0,
            'regular_price' => !empty($data['regular_price']) ? floatval($data['regular_price']) : null,
            'discount_type' => $data['discount_type'] ?? 'none',
            'discount_value' => floatval($data['discount_value'] ?? 0),
            'sell_price' => $data['sell_price'] ?? 0,
            'stock_qty' => $data['stock_qty'] ?? 0,
            'vendor_id' => !empty($data['vendor_id']) ? $data['vendor_id'] : null,
            'category_id' => !empty($data['category_id']) ? $data['category_id'] : null,
            'brand_id' => !empty($data['brand_id']) ? $data['brand_id'] : null,
            'image_path' => $data['image_path'] ?? null,
            'unit_type' => $data['unit_type'] ?? 'piece',
            'base_unit' => $data['base_unit'] ?? 'pcs',
            'purchase_unit' => $data['purchase_unit'] ?? null,
            'purchase_unit_qty' => !empty($data['purchase_unit_qty']) ? $data['purchase_unit_qty'] : 1.000,
            'selling_unit' => $data['selling_unit'] ?? null,
            'unit_variants_json' => !empty($data['unit_variants_json']) ? $data['unit_variants_json'] : null,
            'is_verified' => $data['is_verified'] ?? 0,
            'availability_status' => $data['availability_status'] ?? 'pending',
            'demand_percentage' => $data['demand_percentage'] ?? 0
        ]);
        return $this->db->lastInsertId();
    }

    public function find($id) {
        $stmt = $this->db->query("SELECT * FROM products WHERE id = :id", ['id' => $id]);
        return $stmt->fetch();
    }

    public function update($id, $data) {
        $sql = "UPDATE products SET 
                name = :name, 
                sku = :sku, 
                description = :description, 
                buy_price = :buy_price, 
                regular_price = :regular_price,
                discount_type = :discount_type,
                discount_value = :discount_value,
                sell_price = :sell_price, 
                stock_qty = :stock_qty, 
                vendor_id = :vendor_id, 
                category_id = :category_id, 
                brand_id = :brand_id,
                unit_type = :unit_type,
                base_unit = :base_unit,
                purchase_unit = :purchase_unit,
                purchase_unit_qty = :purchase_unit_qty,
                selling_unit = :selling_unit,
                unit_variants_json = :unit_variants_json";
        
        $params = [
            'name' => $data['name'],
            'sku' => $data['sku'],
            'description' => $data['description'] ?? '',
            'buy_price' => $data['buy_price'] ?? 0,
            'regular_price' => !empty($data['regular_price']) ? floatval($data['regular_price']) : null,
            'discount_type' => $data['discount_type'] ?? 'none',
            'discount_value' => floatval($data['discount_value'] ?? 0),
            'sell_price' => $data['sell_price'] ?? 0,
            'stock_qty' => $data['stock_qty'] ?? 0,
            'vendor_id' => !empty($data['vendor_id']) ? $data['vendor_id'] : null,
            'category_id' => !empty($data['category_id']) ? $data['category_id'] : null,
            'brand_id' => !empty($data['brand_id']) ? $data['brand_id'] : null,
            'unit_type' => $data['unit_type'] ?? 'piece',
            'base_unit' => $data['base_unit'] ?? 'pcs',
            'purchase_unit' => $data['purchase_unit'] ?? null,
            'purchase_unit_qty' => !empty($data['purchase_unit_qty']) ? $data['purchase_unit_qty'] : 1.000,
            'selling_unit' => $data['selling_unit'] ?? null,
            'unit_variants_json' => !empty($data['unit_variants_json']) ? $data['unit_variants_json'] : null,
            'id' => $id
        ];

        if (isset($data['image_path'])) {
            $sql .= ", image_path = :image_path";
            $params['image_path'] = $data['image_path'];
        }

        if (isset($data['is_verified'])) {
            $sql .= ", is_verified = :is_verified";
            $params['is_verified'] = $data['is_verified'];
        }
        
        if (isset($data['availability_status'])) {
            $sql .= ", availability_status = :availability_status";
            $params['availability_status'] = $data['availability_status'];
        }
        
        if (isset($data['demand_percentage'])) {
            $sql .= ", demand_percentage = :demand_percentage";
            $params['demand_percentage'] = $data['demand_percentage'];
        }

        $sql .= " WHERE id = :id";

        $this->db->query($sql, $params);
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
}
