<?php

namespace Models;

use Core\Database;

class PushNotification
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
     * Ensure database tables for push notifications and device subscriptions exist.
     */
    public function ensureSchema(): void
    {
        if (self::$schemaChecked) return;
        self::$schemaChecked = true;

        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS push_notifications (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                customer_id INT UNSIGNED NULL,
                order_id INT UNSIGNED NULL,
                type VARCHAR(30) DEFAULT 'promo',
                title VARCHAR(255) NOT NULL,
                message TEXT NOT NULL,
                promo_code VARCHAR(50) NULL,
                badge_text VARCHAR(100) NULL,
                image_url VARCHAR(500) NULL,
                action_url VARCHAR(500) DEFAULT '/shop',
                target_audience VARCHAR(50) DEFAULT 'all',
                status ENUM('active', 'draft', 'expired') DEFAULT 'active',
                sent_count INT UNSIGNED DEFAULT 0,
                views_count INT UNSIGNED DEFAULT 0,
                clicks_count INT UNSIGNED DEFAULT 0,
                expires_at DATETIME NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_status (status),
                INDEX idx_customer (customer_id),
                INDEX idx_order (order_id),
                INDEX idx_type (type),
                INDEX idx_created (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            $this->db->query("CREATE TABLE IF NOT EXISTS push_subscriptions (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                customer_id INT UNSIGNED NULL,
                endpoint TEXT NOT NULL,
                keys_p256dh VARCHAR(255) NULL,
                keys_auth VARCHAR(255) NULL,
                user_agent TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_customer (customer_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            // Self-healing columns for previously created tables
            try {
                $this->db->query("ALTER TABLE push_notifications ADD COLUMN customer_id INT UNSIGNED NULL AFTER id");
            } catch (\Throwable $e) {}

            try {
                $this->db->query("ALTER TABLE push_notifications ADD COLUMN order_id INT UNSIGNED NULL AFTER customer_id");
            } catch (\Throwable $e) {}

            try {
                $this->db->query("ALTER TABLE push_notifications ADD COLUMN type VARCHAR(30) DEFAULT 'promo' AFTER order_id");
            } catch (\Throwable $e) {}

            try {
                $this->db->query("ALTER TABLE push_notifications MODIFY COLUMN target_audience VARCHAR(50) DEFAULT 'all'");
            } catch (\Throwable $e) {}

        } catch (\Throwable $e) {
            error_log("PushNotification::ensureSchema error: " . $e->getMessage());
        }
    }

    /**
     * Get all push notifications with optional status / type filter
     */
    public function getAll(?string $status = null, ?string $type = null): array
    {
        $sql = "SELECT * FROM push_notifications WHERE 1=1";
        $params = [];

        if ($status && in_array($status, ['active', 'draft', 'expired'], true)) {
            $sql .= " AND status = ?";
            $params[] = $status;
        }

        if ($type && in_array($type, ['promo', 'offer', 'notice', 'order', 'general'], true)) {
            $sql .= " AND type = ?";
            $params[] = $type;
        }

        $sql .= " ORDER BY id DESC";

        $stmt = $this->db->query($sql, $params);
        $rows = $stmt->fetchAll() ?: [];
        foreach ($rows as &$row) {
            $row['time_ago'] = $this->timeAgo($row['created_at']);
        }
        return $rows;
    }

    /**
     * Get active notifications suitable for current visitor / app user
     * Supports multiple notifications: offers, notices, and personal order updates!
     */
    public function getActiveForUser(?int $customerId = null, bool $isLoggedIn = false, int $limit = 30): array
    {
        $audiences = $isLoggedIn ? ['all', 'registered'] : ['all', 'guests'];
        $inClause = "'" . implode("','", $audiences) . "'";

        if (!empty($customerId)) {
            $sql = "SELECT * FROM push_notifications 
                    WHERE status = 'active' 
                      AND (expires_at IS NULL OR expires_at >= NOW())
                      AND (
                          target_audience IN ({$inClause})
                          OR (target_audience = 'customer' AND customer_id = ?)
                      )
                    ORDER BY id DESC 
                    LIMIT {$limit}";
            $stmt = $this->db->query($sql, [(int)$customerId]);
        } else {
            $sql = "SELECT * FROM push_notifications 
                    WHERE status = 'active' 
                      AND (expires_at IS NULL OR expires_at >= NOW())
                      AND target_audience IN ({$inClause})
                    ORDER BY id DESC 
                    LIMIT {$limit}";
            $stmt = $this->db->query($sql);
        }

        $rows = $stmt->fetchAll() ?: [];
        foreach ($rows as &$row) {
            $row['time_ago'] = $this->timeAgo($row['created_at']);
        }
        return $rows;
    }

    /**
     * Find notification by ID
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->query("SELECT * FROM push_notifications WHERE id = ? LIMIT 1", [$id]);
        $row  = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Create a new custom push notification (Offer, Notice, Order, General)
     */
    public function create(array $data): int
    {
        $sql = "INSERT INTO push_notifications 
                (customer_id, order_id, type, title, message, promo_code, badge_text, image_url, action_url, target_audience, status, expires_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $type = in_array($data['type'] ?? '', ['promo', 'offer', 'notice', 'order', 'general'], true) 
            ? $data['type'] 
            : 'promo';

        $defaultBadge = match($type) {
            'notice' => '📢 নোটিশ',
            'order'  => '📦 অর্ডার আপডেট',
            'offer'  => '🎁 স্পেশাল অফার',
            default  => '🔥 স্পেশাল অফার',
        };

        $this->db->query($sql, [
            !empty($data['customer_id']) ? (int)$data['customer_id'] : null,
            !empty($data['order_id']) ? (int)$data['order_id'] : null,
            $type,
            trim($data['title'] ?? ''),
            trim($data['message'] ?? ''),
            !empty($data['promo_code']) ? strtoupper(trim($data['promo_code'])) : null,
            !empty($data['badge_text']) ? trim($data['badge_text']) : $defaultBadge,
            !empty($data['image_url']) ? trim($data['image_url']) : null,
            !empty($data['action_url']) ? trim($data['action_url']) : '/shop',
            !empty($data['target_audience']) ? trim($data['target_audience']) : 'all',
            in_array($data['status'] ?? '', ['active', 'draft', 'expired'], true) ? $data['status'] : 'active',
            !empty($data['expires_at']) ? $data['expires_at'] : null,
        ]);

        return (int)$this->db->getConnection()->lastInsertId();
    }

    /**
     * Send Realtime Order Status Notification to specific customer
     */
    public function sendOrderStatusNotification(int $orderId, string $status, ?string $customMsg = null): ?int
    {
        try {
            // Fetch order and customer details
            $stmt = $this->db->query("SELECT o.*, c.id as cust_id, c.name as cust_name, c.phone as cust_phone 
                                      FROM orders o 
                                      LEFT JOIN customers c ON o.customer_id = c.id 
                                      WHERE o.id = ? LIMIT 1", [$orderId]);
            $order = $stmt->fetch();
            if (!$order) return null;

            $customerId = !empty($order['customer_id']) ? (int)$order['customer_id'] : null;
            $orderNumber = !empty($order['order_number']) ? $order['order_number'] : ('ORD-' . str_pad((string)$orderId, 5, '0', STR_PAD_LEFT));
            $totalAmount = number_format((float)($order['total_amount'] ?? 0), 2);

            $statusConfig = [
                'pending' => [
                    'badge' => 'অর্ডার গৃহীত',
                    'title' => "📦 নতুন অর্ডার গৃহীত হয়েছে (#{$orderNumber})",
                    'message' => "আপনার ৳{$totalAmount} টাকার অর্ডারটি সফলভাবে গ্রহণ করা হয়েছে। খুব শীঘ্রই প্রসেসিং শুরু হবে।"
                ],
                'confirmed' => [
                    'badge' => 'অর্ডার কনফার্মড',
                    'title' => "✅ অর্ডার কনফার্ম করা হয়েছে (#{$orderNumber})",
                    'message' => "আপনার অর্ডারটি সফলভাবে নিশ্চিত করা হয়েছে এবং প্রস্তুত করা হচ্ছে।"
                ],
                'accepted' => [
                    'badge' => 'অর্ডার কনফার্মড',
                    'title' => "✅ অর্ডার কনফার্ম করা হয়েছে (#{$orderNumber})",
                    'message' => "আপনার অর্ডারটি সফলভাবে নিশ্চিত করা হয়েছে এবং প্রস্তুত করা হচ্ছে।"
                ],
                'packaging' => [
                    'badge' => 'প্যাকেজিং চলছে',
                    'title' => "📦 পণ্য প্যাকেজিং চলছে (#{$orderNumber})",
                    'message' => "আপনার অর্ডারের পণ্যগুলো যত্নসহকারে প্যাক করা হচ্ছে।"
                ],
                'processing' => [
                    'badge' => 'প্রসেসিং হচ্ছে',
                    'title' => "⚙️ অর্ডার প্রসেসিং হচ্ছে (#{$orderNumber})",
                    'message' => "আপনার অর্ডারটি ওয়্যারহাউজে প্রসেস করা হচ্ছে।"
                ],
                'shipped' => [
                    'badge' => 'ডেলিভারিতে বের হয়েছে',
                    'title' => "🚀 ডেলিভারির জন্য বের হয়েছে! (#{$orderNumber})",
                    'message' => "আপনার অর্ডারটি ডেলিভারি রাইডারের কাছে হস্তান্তর করা হয়েছে। খুব শীঘ্রই আপনার ঠিকানায় পৌঁছাবে।"
                ],
                'out_for_delivery' => [
                    'badge' => 'ডেলিভারিতে বের হয়েছে',
                    'title' => "🚀 ডেলিভারির জন্য বের হয়েছে! (#{$orderNumber})",
                    'message' => "আপনার অর্ডারটি ডেলিভারি রাইডারের কাছে হস্তান্তর করা হয়েছে। খুব শীঘ্রই আপনার ঠিকানায় পৌঁছাবে।"
                ],
                'delivered' => [
                    'badge' => 'ডেলিভারি সম্পন্ন',
                    'title' => "🎉 অর্ডার ডেলিভারি সম্পন্ন! (#{$orderNumber})",
                    'message' => "আপনার ৳{$totalAmount} টাকার অর্ডারটি সফলভাবে ডেলিভারি করা হয়েছে। সদাই দরকার-এর সাথে কেনাকাটা করার জন্য ধন্যবাদ!"
                ],
                'cancelled' => [
                    'badge' => 'অর্ডার বাতিল',
                    'title' => "❌ অর্ডার বাতিল করা হয়েছে (#{$orderNumber})",
                    'message' => $customMsg ?: "আপনার অর্ডারটি (#{$orderNumber}) বাতিল করা হয়েছে। প্রয়োজনে আমাদের হেল্পলাইনে যোগাযোগ করুন।"
                ],
                'returned' => [
                    'badge' => 'অর্ডার রিটার্ন',
                    'title' => "↩️ অর্ডার রিটার্ন গৃহীত হয়েছে (#{$orderNumber})",
                    'message' => "আপনার অর্ডারের পণ্য রিটার্ন সফলভাবে গৃহীত হয়েছে।"
                ]
            ];

            $cfg = $statusConfig[$status] ?? [
                'badge' => 'অর্ডার আপডেট',
                'title' => "📦 অর্ডার স্ট্যাটাস আপডেট (#{$orderNumber})",
                'message' => $customMsg ?: "আপনার অর্ডারটির স্ট্যাটাস পরিবর্তিত হয়েছে: {$status}।"
            ];

            $actionUrl = '/account/order-detail?id=' . $orderId;

            // Insert notification
            $notifId = $this->create([
                'customer_id'     => $customerId,
                'order_id'        => $orderId,
                'type'            => 'order',
                'title'           => $cfg['title'],
                'message'         => $cfg['message'],
                'badge_text'      => $cfg['badge'],
                'action_url'      => $actionUrl,
                'target_audience' => $customerId ? 'customer' : 'all',
                'status'          => 'active',
            ]);

            return $notifId;
        } catch (\Throwable $e) {
            error_log("PushNotification::sendOrderStatusNotification error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update an existing promo/notice notification
     */
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE push_notifications 
                SET type = ?, title = ?, message = ?, promo_code = ?, badge_text = ?, 
                    image_url = ?, action_url = ?, target_audience = ?, status = ?, expires_at = ?
                WHERE id = ?";

        $type = in_array($data['type'] ?? '', ['promo', 'offer', 'notice', 'order', 'general'], true) 
            ? $data['type'] 
            : 'promo';

        $this->db->query($sql, [
            $type,
            trim($data['title'] ?? ''),
            trim($data['message'] ?? ''),
            !empty($data['promo_code']) ? strtoupper(trim($data['promo_code'])) : null,
            !empty($data['badge_text']) ? trim($data['badge_text']) : null,
            !empty($data['image_url']) ? trim($data['image_url']) : null,
            !empty($data['action_url']) ? trim($data['action_url']) : '/shop',
            !empty($data['target_audience']) ? trim($data['target_audience']) : 'all',
            in_array($data['status'] ?? '', ['active', 'draft', 'expired'], true) ? $data['status'] : 'active',
            !empty($data['expires_at']) ? $data['expires_at'] : null,
            $id
        ]);

        return true;
    }

    /**
     * Delete notification
     */
    public function delete(int $id): bool
    {
        $this->db->query("DELETE FROM push_notifications WHERE id = ?", [$id]);
        return true;
    }

    /**
     * Toggle status between active and draft
     */
    public function toggleStatus(int $id): string
    {
        $curr = $this->getById($id);
        if (!$curr) return 'draft';

        $newStatus = ($curr['status'] === 'active') ? 'draft' : 'active';
        $this->db->query("UPDATE push_notifications SET status = ? WHERE id = ?", [$newStatus, $id]);
        return $newStatus;
    }

    /**
     * Increment views/impressions count
     */
    public function recordImpression(int $id): void
    {
        $this->db->query("UPDATE push_notifications SET views_count = views_count + 1 WHERE id = ?", [$id]);
    }

    /**
     * Increment clicks count
     */
    public function recordClick(int $id): void
    {
        $this->db->query("UPDATE push_notifications SET clicks_count = clicks_count + 1 WHERE id = ?", [$id]);
    }

    /**
     * Increment broadcast sent count
     */
    public function incrementSent(int $id, int $count = 1): void
    {
        $this->db->query("UPDATE push_notifications SET sent_count = sent_count + ? WHERE id = ?", [$count, $id]);
    }

    /**
     * Save / update browser push subscription endpoint
     */
    public function saveSubscription(string $endpoint, ?string $p256dh = null, ?string $auth = null, ?int $customerId = null): bool
    {
        try {
            $stmt = $this->db->query("SELECT id FROM push_subscriptions WHERE endpoint = ? LIMIT 1", [$endpoint]);
            $existing = $stmt->fetch();

            if ($existing) {
                $this->db->query(
                    "UPDATE push_subscriptions SET customer_id = ?, keys_p256dh = ?, keys_auth = ?, updated_at = NOW() WHERE id = ?",
                    [$customerId, $p256dh, $auth, $existing['id']]
                );
            } else {
                $this->db->query(
                    "INSERT INTO push_subscriptions (customer_id, endpoint, keys_p256dh, keys_auth, user_agent) VALUES (?, ?, ?, ?, ?)",
                    [$customerId, $endpoint, $p256dh, $auth, $_SERVER['HTTP_USER_AGENT'] ?? '']
                );
            }
            return true;
        } catch (\Throwable $e) {
            error_log("saveSubscription error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get aggregate analytics statistics
     */
    public function getStats(): array
    {
        $stmt = $this->db->query("SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN type = 'notice' THEN 1 ELSE 0 END) as total_notices,
            SUM(CASE WHEN type IN ('promo', 'offer') THEN 1 ELSE 0 END) as total_offers,
            SUM(CASE WHEN type = 'order' THEN 1 ELSE 0 END) as total_orders,
            SUM(views_count) as total_views,
            SUM(clicks_count) as total_clicks,
            SUM(sent_count) as total_sent
        FROM push_notifications");
        $res = $stmt->fetch() ?: [];

        $totalViews = (int)($res['total_views'] ?? 0);
        $totalClicks = (int)($res['total_clicks'] ?? 0);
        $ctr = $totalViews > 0 ? round(($totalClicks / $totalViews) * 100, 1) : 0;

        $subStmt = $this->db->query("SELECT COUNT(*) FROM push_subscriptions");
        $deviceCount = (int)$subStmt->fetchColumn();

        return [
            'total'         => (int)($res['total'] ?? 0),
            'active'        => (int)($res['active'] ?? 0),
            'total_notices' => (int)($res['total_notices'] ?? 0),
            'total_offers'  => (int)($res['total_offers'] ?? 0),
            'total_orders'  => (int)($res['total_orders'] ?? 0),
            'total_views'   => $totalViews,
            'total_clicks'  => $totalClicks,
            'total_sent'    => (int)($res['total_sent'] ?? 0),
            'ctr'           => $ctr,
            'subscribers'   => $deviceCount
        ];
    }

    /**
     * Convert timestamp to human-readable relative time (Bengali format)
     */
    private function timeAgo(string $datetime): string
    {
        $time = strtotime($datetime);
        $diff = time() - $time;

        if ($diff < 60) {
            return 'এইমাত্র';
        }
        if ($diff < 3600) {
            $m = (int)($diff / 60);
            return $this->bnNum($m) . ' মিনিট আগে';
        }
        if ($diff < 86400) {
            $h = (int)($diff / 3600);
            return $this->bnNum($h) . ' ঘণ্টা আগে';
        }
        if ($diff < 604800) {
            $d = (int)($diff / 86400);
            return $this->bnNum($d) . ' দিন আগে';
        }
        return date('d M, Y', $time);
    }

    private function bnNum(int $num): string
    {
        $en = ['0','1','2','3','4','5','6','7','8','9'];
        $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
        return str_replace($en, $bn, (string)$num);
    }
}
