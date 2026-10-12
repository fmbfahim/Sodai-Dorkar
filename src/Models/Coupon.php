<?php

namespace Models;

use Core\Database;

class Coupon
{
    protected Database $db;
    private static bool $schemaChecked = false;

    public function __construct(?Database $db = null)
    {
        if ($db) {
            $this->db = $db;
        } else {
            $config   = require __DIR__ . '/../../config/database.php';
            $this->db = new Database($config);
        }
        $this->ensureSchema();
    }

    /**
     * Ensure database tables for coupons and usages exist.
     */
    public function ensureSchema(): void
    {
        if (self::$schemaChecked) return;
        self::$schemaChecked = true;

        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS coupons (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                code VARCHAR(50) UNIQUE NOT NULL,
                title VARCHAR(255) NOT NULL,
                discount_type ENUM('fixed', 'percent') NOT NULL DEFAULT 'fixed',
                discount_value DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                min_order_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                max_discount_amount DECIMAL(10,2) NULL,
                usage_limit INT UNSIGNED NULL,
                usage_limit_per_user INT UNSIGNED NOT NULL DEFAULT 1,
                times_used INT UNSIGNED NOT NULL DEFAULT 0,
                start_date DATETIME NULL,
                end_date DATETIME NULL,
                status ENUM('active', 'inactive', 'expired') NOT NULL DEFAULT 'active',
                description TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_code (code),
                INDEX idx_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            $this->db->query("CREATE TABLE IF NOT EXISTS coupon_usages (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                coupon_id INT UNSIGNED NOT NULL,
                coupon_code VARCHAR(50) NOT NULL,
                customer_id INT UNSIGNED NULL,
                order_id INT UNSIGNED NULL,
                discount_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_coupon (coupon_id),
                INDEX idx_customer (customer_id),
                INDEX idx_code (coupon_code)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            // Seed standard initial coupons if table is completely empty
            $countStmt = $this->db->query("SELECT COUNT(*) FROM coupons");
            if ((int)$countStmt->fetchColumn() === 0) {
                $defaults = [
                    [
                        'code' => 'SODAI50',
                        'title' => '৫০ টাকা স্পেশাল মূল্যছাড়',
                        'discount_type' => 'fixed',
                        'discount_value' => 50.00,
                        'min_order_amount' => 200.00,
                        'max_discount_amount' => null,
                        'usage_limit' => null,
                        'usage_limit_per_user' => 1,
                        'description' => 'সর্বনিম্ন ২০০ টাকার অর্ডারে ৫০ টাকা সরাসরি ছাড়'
                    ],
                    [
                        'code' => 'SODAI10',
                        'title' => '১০% গ্রোসারি ডিসকাউন্ট',
                        'discount_type' => 'percent',
                        'discount_value' => 10.00,
                        'min_order_amount' => 300.00,
                        'max_discount_amount' => 100.00,
                        'usage_limit' => null,
                        'usage_limit_per_user' => 2,
                        'description' => 'সর্বনিম্ন ৩০০ টাকার অর্ডারে ১০% ছাড় (সর্বোচ্চ ১০০ টাকা)'
                    ],
                    [
                        'code' => 'WELCOME',
                        'title' => 'নতুন গ্রাহক ওয়েলকাম কুপন',
                        'discount_type' => 'fixed',
                        'discount_value' => 30.00,
                        'min_order_amount' => 150.00,
                        'max_discount_amount' => null,
                        'usage_limit' => null,
                        'usage_limit_per_user' => 1,
                        'description' => 'প্রথম অর্ডারে ৩০ টাকা ওয়েলকাম ছাড়'
                    ],
                    [
                        'code' => 'FAST30',
                        'title' => 'ফাস্ট ডেলিভারি বোনাস ছাড়',
                        'discount_type' => 'fixed',
                        'discount_value' => 30.00,
                        'min_order_amount' => 200.00,
                        'max_discount_amount' => null,
                        'usage_limit' => null,
                        'usage_limit_per_user' => 1,
                        'description' => 'সুপার ফাস্ট ডেলিভারির সাথে ৩০ টাকা বিশেষ ছাড়'
                    ]
                ];

                foreach ($defaults as $d) {
                    $this->create($d);
                }
            }
        } catch (\Throwable $e) {
            error_log("Coupon::ensureSchema error: " . $e->getMessage());
        }
    }

    /**
     * Get all coupons with optional filters
     */
    public function getAll(?string $status = null, ?string $search = null): array
    {
        $sql = "SELECT * FROM coupons WHERE 1=1";
        $params = [];

        if ($status && in_array($status, ['active', 'inactive', 'expired'], true)) {
            $sql .= " AND status = ?";
            $params[] = $status;
        }

        if (!empty($search)) {
            $sql .= " AND (code LIKE ? OR title LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $sql .= " ORDER BY id DESC";
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Get coupon by ID
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->query("SELECT * FROM coupons WHERE id = ? LIMIT 1", [$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Find coupon by unique code
     */
    public function findByCode(string $code): ?array
    {
        $clean = strtoupper(trim($code));
        $stmt = $this->db->query("SELECT * FROM coupons WHERE code = ? LIMIT 1", [$clean]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Create a new coupon
     */
    public function create(array $data): int
    {
        $code = strtoupper(trim($data['code'] ?? ''));
        $title = trim($data['title'] ?? '');
        $type = in_array($data['discount_type'] ?? '', ['fixed', 'percent'], true) ? $data['discount_type'] : 'fixed';
        $val = floatval($data['discount_value'] ?? 0);
        $minOrder = floatval($data['min_order_amount'] ?? 0);
        $maxDisc = !empty($data['max_discount_amount']) ? floatval($data['max_discount_amount']) : null;
        $limit = !empty($data['usage_limit']) ? intval($data['usage_limit']) : null;
        $limitPerUser = !empty($data['usage_limit_per_user']) ? intval($data['usage_limit_per_user']) : 1;
        $start = !empty($data['start_date']) ? $data['start_date'] : null;
        $end = !empty($data['end_date']) ? $data['end_date'] : null;
        $status = in_array($data['status'] ?? '', ['active', 'inactive', 'expired'], true) ? $data['status'] : 'active';
        $desc = trim($data['description'] ?? '');

        $sql = "INSERT INTO coupons 
                (code, title, discount_type, discount_value, min_order_amount, max_discount_amount, 
                 usage_limit, usage_limit_per_user, start_date, end_date, status, description)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $this->db->query($sql, [
            $code, $title, $type, $val, $minOrder, $maxDisc,
            $limit, $limitPerUser, $start, $end, $status, $desc
        ]);

        return (int)$this->db->getConnection()->lastInsertId();
    }

    /**
     * Update an existing coupon
     */
    public function update(int $id, array $data): bool
    {
        $code = strtoupper(trim($data['code'] ?? ''));
        $title = trim($data['title'] ?? '');
        $type = in_array($data['discount_type'] ?? '', ['fixed', 'percent'], true) ? $data['discount_type'] : 'fixed';
        $val = floatval($data['discount_value'] ?? 0);
        $minOrder = floatval($data['min_order_amount'] ?? 0);
        $maxDisc = !empty($data['max_discount_amount']) ? floatval($data['max_discount_amount']) : null;
        $limit = !empty($data['usage_limit']) ? intval($data['usage_limit']) : null;
        $limitPerUser = !empty($data['usage_limit_per_user']) ? intval($data['usage_limit_per_user']) : 1;
        $start = !empty($data['start_date']) ? $data['start_date'] : null;
        $end = !empty($data['end_date']) ? $data['end_date'] : null;
        $status = in_array($data['status'] ?? '', ['active', 'inactive', 'expired'], true) ? $data['status'] : 'active';
        $desc = trim($data['description'] ?? '');

        $sql = "UPDATE coupons 
                SET code = ?, title = ?, discount_type = ?, discount_value = ?, min_order_amount = ?, 
                    max_discount_amount = ?, usage_limit = ?, usage_limit_per_user = ?, 
                    start_date = ?, end_date = ?, status = ?, description = ?
                WHERE id = ?";

        $this->db->query($sql, [
            $code, $title, $type, $val, $minOrder, $maxDisc,
            $limit, $limitPerUser, $start, $end, $status, $desc,
            $id
        ]);

        return true;
    }

    /**
     * Delete coupon
     */
    public function delete(int $id): bool
    {
        $this->db->query("DELETE FROM coupons WHERE id = ?", [$id]);
        return true;
    }

    /**
     * Toggle status between active and inactive
     */
    public function toggleStatus(int $id): string
    {
        $curr = $this->getById($id);
        if (!$curr) return 'inactive';

        $newStatus = ($curr['status'] === 'active') ? 'inactive' : 'active';
        $this->db->query("UPDATE coupons SET status = ? WHERE id = ?", [$newStatus, $id]);
        return $newStatus;
    }

    /**
     * Comprehensive validation of a coupon code against subtotal and user context
     */
    public function validateCoupon(string $code, float $subtotal, ?int $customerId = null): array
    {
        $clean = strtoupper(trim($code));
        if (empty($clean)) {
            return ['valid' => false, 'message' => 'অনুগ্রহ করে কুপন কোড লিখুন।'];
        }

        $coupon = $this->findByCode($clean);
        if (!$coupon) {
            return ['valid' => false, 'message' => "অকার্যকর কুপন কোড: '{$clean}'"];
        }

        // 1. Status Check
        if ($coupon['status'] !== 'active') {
            return ['valid' => false, 'message' => "এই কুপনটি বর্তমানে নিষ্ক্রিয় বা বন্ধ রয়েছে।"];
        }

        // 2. Date Range Check
        $now = time();
        if (!empty($coupon['start_date']) && strtotime($coupon['start_date']) > $now) {
            return [
                'valid' => false, 
                'message' => "কুপনটি " . date('d M, Y', strtotime($coupon['start_date'])) . " তারিখ থেকে সক্রিয় হবে।"
            ];
        }
        if (!empty($coupon['end_date']) && strtotime($coupon['end_date']) < $now) {
            // Auto update status to expired
            $this->db->query("UPDATE coupons SET status = 'expired' WHERE id = ?", [$coupon['id']]);
            return ['valid' => false, 'message' => "দুঃখিত, এই কুপনটির মেয়াদ শেষ হয়ে গেছে।"];
        }

        // 3. Minimum Order Amount Check
        $minOrder = floatval($coupon['min_order_amount']);
        if ($subtotal < $minOrder) {
            return [
                'valid' => false,
                'message' => "এই কুপনটি ব্যবহার করতে সর্বনিম্ন ৳" . number_format($minOrder) . " মূল্যের পণ্য অর্ডারের প্রয়োজন।"
            ];
        }

        // 4. Overall Usage Limit Check
        if ($coupon['usage_limit'] !== null && intval($coupon['times_used']) >= intval($coupon['usage_limit'])) {
            return ['valid' => false, 'message' => "দুঃখিত, এই কুপনটির সর্বমোট ব্যবহারের সীমা শেষ হয়ে গেছে।"];
        }

        // 5. Per-Customer Usage Limit Check
        if ($customerId && !empty($coupon['usage_limit_per_user'])) {
            $stmt = $this->db->query(
                "SELECT COUNT(*) FROM coupon_usages WHERE coupon_id = ? AND customer_id = ?",
                [$coupon['id'], $customerId]
            );
            $userUsedCount = (int)$stmt->fetchColumn();
            if ($userUsedCount >= intval($coupon['usage_limit_per_user'])) {
                return [
                    'valid' => false,
                    'message' => "আপনি ইতিমধ্যে এই কুপনটি ব্যবহারের সর্বোচ্চ সীমা ({$coupon['usage_limit_per_user']} বার) পূরণ করেছেন।"
                ];
            }
        }

        // 6. Calculate Discount Amount
        $discount = 0.00;
        if ($coupon['discount_type'] === 'percent') {
            $discount = round(($subtotal * floatval($coupon['discount_value'])) / 100, 2);
            if (!empty($coupon['max_discount_amount']) && floatval($coupon['max_discount_amount']) > 0) {
                $discount = min($discount, floatval($coupon['max_discount_amount']));
            }
        } else {
            $discount = floatval($coupon['discount_value']);
        }

        // Discount cannot exceed subtotal
        $discount = min($discount, $subtotal);

        return [
            'valid' => true,
            'code' => $coupon['code'],
            'coupon_id' => $coupon['id'],
            'discount' => $discount,
            'title' => $coupon['title'],
            'description' => $coupon['description'],
            'message' => "অভিনন্দন! কুপন '{$coupon['code']}' সফলভাবে প্রয়োগ করা হয়েছে (-৳" . number_format($discount, 2) . ")।"
        ];
    }

    /**
     * Record usage after successful order placement
     */
    public function recordUsage(string $code, float $discountAmount, ?int $orderId = null, ?int $customerId = null): void
    {
        $clean = strtoupper(trim($code));
        $coupon = $this->findByCode($clean);
        if (!$coupon) return;

        try {
            // Increment coupon times_used
            $this->db->query("UPDATE coupons SET times_used = times_used + 1 WHERE id = ?", [$coupon['id']]);

            // Insert audit record in coupon_usages
            $this->db->query(
                "INSERT INTO coupon_usages (coupon_id, coupon_code, customer_id, order_id, discount_amount) VALUES (?, ?, ?, ?, ?)",
                [$coupon['id'], $coupon['code'], $customerId, $orderId, $discountAmount]
            );
        } catch (\Throwable $e) {
            error_log("Coupon::recordUsage error: " . $e->getMessage());
        }
    }

    /**
     * Get recent coupon usages log
     */
    public function getUsages(int $limit = 50): array
    {
        $sql = "SELECT cu.*, c.title as coupon_title, cust.name as customer_name, cust.phone as customer_phone, o.total_amount as order_amount, o.order_number
                FROM coupon_usages cu
                LEFT JOIN coupons c ON cu.coupon_id = c.id
                LEFT JOIN customers cust ON cu.customer_id = cust.id
                LEFT JOIN orders o ON cu.order_id = o.id
                ORDER BY cu.id DESC
                LIMIT {$limit}";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Get aggregate statistics for Marketing Dashboard
     */
    public function getStats(): array
    {
        $stmt = $this->db->query("SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
            SUM(times_used) as total_redemptions
        FROM coupons");
        $res = $stmt->fetch() ?: [];

        $discStmt = $this->db->query("SELECT SUM(discount_amount) FROM coupon_usages");
        $totalSavings = floatval($discStmt->fetchColumn() ?: 0);

        return [
            'total'             => (int)($res['total'] ?? 0),
            'active'            => (int)($res['active'] ?? 0),
            'total_redemptions' => (int)($res['total_redemptions'] ?? 0),
            'total_savings'     => $totalSavings
        ];
    }
}
