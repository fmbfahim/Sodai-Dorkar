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
                title VARCHAR(255) NOT NULL,
                message TEXT NOT NULL,
                promo_code VARCHAR(50) NULL,
                badge_text VARCHAR(100) NULL,
                image_url VARCHAR(500) NULL,
                action_url VARCHAR(500) DEFAULT '/shop',
                target_audience ENUM('all', 'registered', 'guests') DEFAULT 'all',
                status ENUM('active', 'draft', 'expired') DEFAULT 'active',
                sent_count INT UNSIGNED DEFAULT 0,
                views_count INT UNSIGNED DEFAULT 0,
                clicks_count INT UNSIGNED DEFAULT 0,
                expires_at DATETIME NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_status (status),
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
        } catch (\Throwable $e) {
            error_log("PushNotification::ensureSchema error: " . $e->getMessage());
        }
    }

    /**
     * Get all promo push notifications with optional status filter
     */
    public function getAll(?string $status = null): array
    {
        if ($status && in_array($status, ['active', 'draft', 'expired'], true)) {
            $stmt = $this->db->query(
                "SELECT * FROM push_notifications WHERE status = ? ORDER BY id DESC",
                [$status]
            );
        } else {
            $stmt = $this->db->query("SELECT * FROM push_notifications ORDER BY id DESC");
        }
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Get active notifications suitable for current visitor / app user
     */
    public function getActiveForUser(bool $isLoggedIn = false, int $limit = 20): array
    {
        $audiences = $isLoggedIn ? ['all', 'registered'] : ['all', 'guests'];
        $inClause = implode("','", $audiences);

        $sql = "SELECT * FROM push_notifications 
                WHERE status = 'active' 
                  AND target_audience IN ('{$inClause}')
                  AND (expires_at IS NULL OR expires_at >= NOW())
                ORDER BY id DESC 
                LIMIT {$limit}";

        $stmt = $this->db->query($sql);
        $rows = $stmt->fetchAll() ?: [];

        // Format relative time in Bengali / friendly format
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
     * Create a new custom promo push notification
     */
    public function create(array $data): int
    {
        $sql = "INSERT INTO push_notifications 
                (title, message, promo_code, badge_text, image_url, action_url, target_audience, status, expires_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $this->db->query($sql, [
            trim($data['title'] ?? ''),
            trim($data['message'] ?? ''),
            !empty($data['promo_code']) ? strtoupper(trim($data['promo_code'])) : null,
            !empty($data['badge_text']) ? trim($data['badge_text']) : '🔥 স্পেশাল অফার',
            !empty($data['image_url']) ? trim($data['image_url']) : null,
            !empty($data['action_url']) ? trim($data['action_url']) : '/shop',
            in_array($data['target_audience'] ?? '', ['all', 'registered', 'guests'], true) ? $data['target_audience'] : 'all',
            in_array($data['status'] ?? '', ['active', 'draft', 'expired'], true) ? $data['status'] : 'active',
            !empty($data['expires_at']) ? $data['expires_at'] : null,
        ]);

        return (int)$this->db->getConnection()->lastInsertId();
    }

    /**
     * Update an existing promo notification
     */
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE push_notifications 
                SET title = ?, message = ?, promo_code = ?, badge_text = ?, 
                    image_url = ?, action_url = ?, target_audience = ?, status = ?, expires_at = ?
                WHERE id = ?";

        $this->db->query($sql, [
            trim($data['title'] ?? ''),
            trim($data['message'] ?? ''),
            !empty($data['promo_code']) ? strtoupper(trim($data['promo_code'])) : null,
            !empty($data['badge_text']) ? trim($data['badge_text']) : null,
            !empty($data['image_url']) ? trim($data['image_url']) : null,
            !empty($data['action_url']) ? trim($data['action_url']) : '/shop',
            in_array($data['target_audience'] ?? '', ['all', 'registered', 'guests'], true) ? $data['target_audience'] : 'all',
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
     * Increment broadcast/sent count
     */
    public function incrementSent(int $id, int $count = 1): void
    {
        $this->db->query("UPDATE push_notifications SET sent_count = sent_count + ? WHERE id = ?", [$count, $id]);
    }

    /**
     * Save web push device subscription
     */
    public function saveSubscription(string $endpoint, ?string $p256dh, ?string $auth, ?int $customerId = null): bool
    {
        if (empty($endpoint)) return false;

        try {
            $stmt = $this->db->query("SELECT id FROM push_subscriptions WHERE endpoint = ? LIMIT 1", [$endpoint]);
            $existing = $stmt->fetch();

            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

            if ($existing) {
                $this->db->query(
                    "UPDATE push_subscriptions SET customer_id = COALESCE(?, customer_id), keys_p256dh = ?, keys_auth = ?, user_agent = ? WHERE id = ?",
                    [$customerId, $p256dh, $auth, $ua, $existing['id']]
                );
            } else {
                $this->db->query(
                    "INSERT INTO push_subscriptions (customer_id, endpoint, keys_p256dh, keys_auth, user_agent) VALUES (?, ?, ?, ?, ?)",
                    [$customerId, $endpoint, $p256dh, $auth, $ua]
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
            'total'        => (int)($res['total'] ?? 0),
            'active'       => (int)($res['active'] ?? 0),
            'total_views'  => $totalViews,
            'total_clicks' => $totalClicks,
            'total_sent'   => (int)($res['total_sent'] ?? 0),
            'ctr'          => $ctr,
            'subscribers'  => $deviceCount
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
