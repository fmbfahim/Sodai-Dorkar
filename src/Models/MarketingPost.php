<?php

namespace Models;

use Core\Database;

class MarketingPost {
    protected $db;

    public function __construct() {
        $config = require __DIR__ . '/../../config/database.php';
        $this->db = new Database($config);
        $this->ensureSchema();
    }

    /**
     * Ensure the marketing_posts table exists
     */
    public function ensureSchema() {
        static $ensured = false;
        if ($ensured) return;
        $ensured = true;

        try {
            $sql = "CREATE TABLE IF NOT EXISTS marketing_posts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                product_id INT UNSIGNED NULL,
                slot_type VARCHAR(20) NOT NULL DEFAULT 'morning',
                post_date DATE NOT NULL,
                schedule_time VARCHAR(10) NULL,
                mode VARCHAR(30) NOT NULL DEFAULT 'ai_gemini',
                headline VARCHAR(255) NULL,
                caption TEXT NOT NULL,
                image_path VARCHAR(500) NULL,
                status VARCHAR(20) NOT NULL DEFAULT 'draft',
                fb_post_id VARCHAR(100) NULL,
                posted_at DATETIME NULL,
                error_message TEXT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_date_slot (post_date, slot_type)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            $this->db->query($sql);
        } catch (\Throwable $e) {
            error_log("MarketingPost::ensureSchema error: " . $e->getMessage());
        }
    }

    public function find($id) {
        $stmt = $this->db->query("SELECT * FROM marketing_posts WHERE id = :id LIMIT 1", ['id' => $id]);
        return $stmt->fetch();
    }

    public function getTodayPosts($date = null) {
        if (!$date) {
            $date = date('Y-m-d');
        }
        $stmt = $this->db->query("
            SELECT m.*, p.name as product_name, p.image_path as product_image, p.sell_price, p.regular_price 
            FROM marketing_posts m
            LEFT JOIN products p ON m.product_id = p.id
            WHERE m.post_date = :date
            ORDER BY FIELD(m.slot_type, 'morning', 'afternoon', 'evening', 'custom'), m.id ASC
        ", ['date' => $date]);
        
        $posts = $stmt->fetchAll();
        $bySlot = [
            'morning' => null,
            'afternoon' => null,
            'evening' => null,
            'custom' => []
        ];

        foreach ($posts as $post) {
            if (isset($bySlot[$post['slot_type']]) && $post['slot_type'] !== 'custom') {
                if ($bySlot[$post['slot_type']] === null) {
                    $bySlot[$post['slot_type']] = $post;
                }
            } else {
                $bySlot['custom'][] = $post;
            }
        }

        return $bySlot;
    }

    public function getRecentPosts($limit = 15) {
        $stmt = $this->db->query("
            SELECT m.*, p.name as product_name, p.image_path as product_image, p.sell_price 
            FROM marketing_posts m
            LEFT JOIN products p ON m.product_id = p.id
            ORDER BY m.post_date DESC, m.id DESC 
            LIMIT " . intval($limit)
        );
        return $stmt->fetchAll();
    }

    public function create(array $data) {
        $fields = [
            'product_id' => $data['product_id'] ?? null,
            'slot_type' => $data['slot_type'] ?? 'morning',
            'post_date' => $data['post_date'] ?? date('Y-m-d'),
            'schedule_time' => $data['schedule_time'] ?? null,
            'mode' => $data['mode'] ?? 'ai_gemini',
            'headline' => $data['headline'] ?? null,
            'caption' => $data['caption'] ?? '',
            'image_path' => $data['image_path'] ?? null,
            'status' => $data['status'] ?? 'draft',
            'fb_post_id' => $data['fb_post_id'] ?? null,
            'posted_at' => $data['posted_at'] ?? null,
            'error_message' => $data['error_message'] ?? null
        ];

        $columns = implode(', ', array_keys($fields));
        $placeholders = ':' . implode(', :', array_keys($fields));

        $sql = "INSERT INTO marketing_posts ({$columns}) VALUES ({$placeholders})";
        $this->db->query($sql, $fields);
        return $this->db->lastInsertId();
    }

    public function update($id, array $data) {
        $set = [];
        $params = ['id' => $id];
        foreach ($data as $col => $val) {
            $set[] = "{$col} = :{$col}";
            $params[$col] = $val;
        }

        if (empty($set)) return false;

        $sql = "UPDATE marketing_posts SET " . implode(', ', $set) . " WHERE id = :id";
        return $this->db->query($sql, $params);
    }

    public function delete($id) {
        return $this->db->query("DELETE FROM marketing_posts WHERE id = :id", ['id' => $id]);
    }

    public function getPendingScheduledForNow() {
        $today = date('Y-m-d');
        $nowTime = date('H:i');
        
        $stmt = $this->db->query("
            SELECT * FROM marketing_posts 
            WHERE post_date = :today 
              AND status IN ('scheduled', 'draft') 
              AND schedule_time <= :now 
              AND fb_post_id IS NULL
            ORDER BY schedule_time ASC
        ", [
            'today' => $today,
            'now' => $nowTime
        ]);
        return $stmt->fetchAll();
    }
}
