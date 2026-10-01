<?php

namespace Core;

use Core\Database;
use PDO;

class Tracker {
    private static $db = null;

    private static function getDb() {
        if (self::$db === null) {
            $config = require __DIR__ . '/../../config/database.php';
            self::$db = new Database($config);
        }
        return self::$db;
    }

    /**
     * Get accurate Client IP
     */
    public static function getClientIp() {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($parts[0]);
        }
        if ($ip === '::1') {
            $ip = '127.0.0.1';
        }
        return substr($ip, 0, 45);
    }

    /**
     * Parse device, browser, and platform from User-Agent
     */
    public static function parseUserAgent($ua = null) {
        $ua = $ua ?: ($_SERVER['HTTP_USER_AGENT'] ?? '');
        $deviceType = 'desktop';
        $browser = 'Other';
        $platform = 'Other';

        // Device
        if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i', $ua)) {
            $deviceType = 'tablet';
        } elseif (preg_match('/(mobile|ipod|iphone|android|blackberry|iemobile|opera mini)/i', $ua)) {
            $deviceType = 'mobile';
        }

        // Platform / OS
        if (preg_match('/windows nt/i', $ua)) {
            $platform = 'Windows';
        } elseif (preg_match('/android/i', $ua)) {
            $platform = 'Android';
        } elseif (preg_match('/iphone|ipad|ipod/i', $ua)) {
            $platform = 'iOS';
        } elseif (preg_match('/macintosh|mac os x/i', $ua)) {
            $platform = 'macOS';
        } elseif (preg_match('/linux/i', $ua)) {
            $platform = 'Linux';
        }

        // Browser
        if (preg_match('/edg/i', $ua)) {
            $browser = 'Edge';
        } elseif (preg_match('/chrome|crios/i', $ua)) {
            $browser = 'Chrome';
        } elseif (preg_match('/firefox|fxios/i', $ua)) {
            $browser = 'Firefox';
        } elseif (preg_match('/safari/i', $ua) && !preg_match('/chrome/i', $ua)) {
            $browser = 'Safari';
        } elseif (preg_match('/opera|opr/i', $ua)) {
            $browser = 'Opera';
        } elseif (preg_match('/samsungbrowser/i', $ua)) {
            $browser = 'Samsung Internet';
        }

        return [
            'device_type' => $deviceType,
            'browser' => $browser,
            'platform' => $platform,
            'user_agent' => substr($ua, 0, 255)
        ];
    }

    /**
     * Automatically track public page views
     */
    public static function trackPageView($pageUrl = null, $customTitle = null) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $url = $pageUrl ?: ($_SERVER['REQUEST_URI'] ?? '/');
        
        // Skip tracking for static assets or admin paths
        if (preg_match('/\.(css|js|jpg|jpeg|png|gif|svg|ico|woff|woff2|ttf|map)$/i', $url)) {
            return;
        }
        if (strpos($url, '/admin') !== false || strpos($url, '/delivery') !== false) {
            return;
        }

        try {
            $db = self::getDb();
            $sessionId = session_id();
            if (empty($sessionId)) return;

            $ip = self::getClientIp();
            $agentInfo = self::parseUserAgent();
            $referrer = $_SERVER['HTTP_REFERER'] ?? null;
            $now = date('Y-m-d H:i:s');

            // Cart info
            $cart = $_SESSION['cart'] ?? [];
            $cartCount = 0;
            $cartTotal = 0.00;
            foreach ($cart as $item) {
                $qty = intval($item['quantity'] ?? 1);
                $cartCount += $qty;
                $cartTotal += ($qty * floatval($item['price'] ?? 0));
            }

            // Customer info
            $customerId = $_SESSION['customer_id'] ?? null;
            $customerName = null;
            $customerPhone = null;
            if ($customerId) {
                $c = $db->query("SELECT name, phone FROM customers WHERE id = ?", [$customerId])->fetch();
                if ($c) {
                    $customerName = $c['name'];
                    $customerPhone = $c['phone'];
                }
            }

            // Clean title/description
            $description = $customTitle;
            if (empty($description)) {
                if ($url === '/' || $url === '/sodai-dorkar/public/' || $url === '/sodai-dorkar/public') {
                    $description = 'হোমপেজ ব্রাউজ করেছেন (Visited Homepage)';
                } elseif (strpos($url, '/cart') !== false) {
                    $description = 'শপিং কার্ট পেজ দেখেছেন (Viewed Shopping Cart)';
                } elseif (strpos($url, '/checkout') !== false) {
                    $description = 'চেকআউট পেজে গিয়েছেন (Reached Checkout)';
                } elseif (strpos($url, '/shop') !== false) {
                    $description = 'শপ ক্যাটালগ ব্রাউজ করেছেন (Browsing Shop Catalog)';
                } else {
                    $description = 'পেজ ভিজিট: ' . substr($url, 0, 80);
                }
            }

            // Upsert visitor_sessions
            $stmt = $db->query("SELECT id, page_views FROM visitor_sessions WHERE session_id = ? LIMIT 1", [$sessionId]);
            $existing = $stmt->fetch();

            if ($existing) {
                $sql = "UPDATE visitor_sessions SET 
                            ip_address = ?,
                            customer_id = COALESCE(?, customer_id),
                            customer_name = COALESCE(?, customer_name),
                            customer_phone = COALESCE(?, customer_phone),
                            current_page = ?,
                            page_views = page_views + 1,
                            cart_items_count = ?,
                            cart_total = ?,
                            last_activity_at = ?
                        WHERE id = ?";
                $db->query($sql, [
                    $ip,
                    $customerId,
                    $customerName,
                    $customerPhone,
                    substr($url, 0, 255),
                    $cartCount,
                    $cartTotal,
                    $now,
                    $existing['id']
                ]);
            } else {
                $sql = "INSERT INTO visitor_sessions 
                        (session_id, ip_address, device_type, browser, platform, user_agent, customer_id, customer_name, customer_phone, landing_page, current_page, referrer, page_views, cart_items_count, cart_total, last_activity_at, created_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?, ?, ?)";
                $db->query($sql, [
                    $sessionId,
                    $ip,
                    $agentInfo['device_type'],
                    $agentInfo['browser'],
                    $agentInfo['platform'],
                    $agentInfo['user_agent'],
                    $customerId,
                    $customerName,
                    $customerPhone,
                    substr($url, 0, 255),
                    substr($url, 0, 255),
                    $referrer ? substr($referrer, 0, 255) : null,
                    $cartCount,
                    $cartTotal,
                    $now,
                    $now
                ]);
            }

            // Log into visitor_activities
            $sqlAct = "INSERT INTO visitor_activities (session_id, ip_address, action_type, description, page_url, meta_data, created_at)
                       VALUES (?, ?, 'page_view', ?, ?, NULL, ?)";
            $db->query($sqlAct, [
                $sessionId,
                $ip,
                $description,
                substr($url, 0, 255),
                $now
            ]);

        } catch (\Exception $e) {
            // Fail silently so customer browsing is never interrupted
        }
    }

    /**
     * Track a custom user event / action
     */
    public static function trackAction($actionType, $description, $meta = []) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $sessionId = session_id();
        if (empty($sessionId)) return;

        try {
            $db = self::getDb();
            $ip = self::getClientIp();
            $now = date('Y-m-d H:i:s');
            $url = $_SERVER['REQUEST_URI'] ?? '';

            // Update visitor_sessions last_activity
            $cart = $_SESSION['cart'] ?? [];
            $cartCount = 0;
            $cartTotal = 0.00;
            foreach ($cart as $item) {
                $qty = intval($item['quantity'] ?? 1);
                $cartCount += $qty;
                $cartTotal += ($qty * floatval($item['price'] ?? 0));
            }

            $customerId = $_SESSION['customer_id'] ?? null;
            $hasOrdered = ($actionType === 'place_order') ? 1 : 0;

            $db->query("UPDATE visitor_sessions SET 
                            cart_items_count = ?, 
                            cart_total = ?, 
                            has_ordered = GREATEST(has_ordered, ?),
                            customer_id = COALESCE(?, customer_id),
                            last_activity_at = ? 
                        WHERE session_id = ?", [
                $cartCount, $cartTotal, $hasOrdered, $customerId, $now, $sessionId
            ]);

            // Insert into visitor_activities
            $metaJson = !empty($meta) ? json_encode($meta, JSON_UNESCAPED_UNICODE) : null;
            $db->query("INSERT INTO visitor_activities 
                        (session_id, ip_address, action_type, description, page_url, meta_data, created_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?)", [
                $sessionId,
                $ip,
                $actionType,
                $description,
                substr($url, 0, 255),
                $metaJson,
                $now
            ]);

        } catch (\Exception $e) {
            // Fail silently
        }
    }

    /**
     * Get active visitors count (last 5 minutes)
     */
    public static function getActiveVisitorsCount($minutes = 5) {
        $db = self::getDb();
        $sql = "SELECT COUNT(DISTINCT ip_address) FROM visitor_sessions WHERE last_activity_at >= (NOW() - INTERVAL ? MINUTE)";
        return (int)$db->query($sql, [$minutes])->fetchColumn();
    }

    /**
     * Get full visitor stats and IP breakdown for dashboard
     */
    public static function getVisitorStats($range = '24h', $from = null, $to = null) {
        $db = self::getDb();
        $pdo = $db->getConnection();

        // Calculate WHERE clause based on range
        $whereSql = "1=1";
        $params = [];

        if ($range === '1h') {
            $whereSql = "vs.last_activity_at >= (NOW() - INTERVAL 1 HOUR)";
        } elseif ($range === '24h') {
            $whereSql = "vs.last_activity_at >= (NOW() - INTERVAL 24 HOUR)";
        } elseif ($range === '7d') {
            $whereSql = "vs.last_activity_at >= (NOW() - INTERVAL 7 DAY)";
        } elseif ($range === '30d') {
            $whereSql = "vs.last_activity_at >= (NOW() - INTERVAL 30 DAY)";
        } elseif ($range === 'custom' && $from && $to) {
            $whereSql = "vs.last_activity_at BETWEEN ? AND ?";
            $params = [$from . ' 00:00:00', $to . ' 23:59:59'];
        }

        // 1. Summary Numbers
        $activeNow = self::getActiveVisitorsCount(5);

        // Total unique visitors in period
        $stmtTot = $db->query("SELECT COUNT(DISTINCT vs.ip_address) as unique_visitors, 
                                      COUNT(*) as total_sessions, 
                                      COALESCE(SUM(vs.page_views), 0) as total_page_views,
                                      COALESCE(SUM(CASE WHEN vs.has_ordered = 1 THEN 1 ELSE 0 END), 0) as total_orders
                               FROM visitor_sessions vs 
                               WHERE {$whereSql}", $params);
        $summary = $stmtTot->fetch(PDO::FETCH_ASSOC);

        // Cart Add actions count
        $actWhere = str_replace('vs.last_activity_at', 'created_at', $whereSql);
        $cartAdds = (int)$db->query("SELECT COUNT(*) FROM visitor_activities WHERE action_type = 'add_to_cart' AND {$actWhere}", $params)->fetchColumn();

        // Device Breakdown
        $deviceStmt = $db->query("SELECT device_type, COUNT(*) as count FROM visitor_sessions vs WHERE {$whereSql} GROUP BY device_type", $params);
        $devices = ['mobile' => 0, 'desktop' => 0, 'tablet' => 0];
        while ($d = $deviceStmt->fetch()) {
            $dt = strtolower($d['device_type'] ?? 'desktop');
            if (isset($devices[$dt])) {
                $devices[$dt] = (int)$d['count'];
            }
        }

        // 2. IP Breakdown List (Grouped by IP or Session, ordered by last_activity_at DESC)
        $sqlList = "SELECT 
                        vs.ip_address,
                        vs.session_id,
                        vs.device_type,
                        vs.browser,
                        vs.platform,
                        vs.customer_id,
                        COALESCE(vs.customer_name, c.name) as customer_name,
                        COALESCE(vs.customer_phone, c.phone) as customer_phone,
                        vs.current_page,
                        vs.landing_page,
                        vs.page_views,
                        vs.cart_items_count,
                        vs.cart_total,
                        vs.has_ordered,
                        vs.created_at as first_seen,
                        vs.last_activity_at,
                        TIMESTAMPDIFF(MINUTE, vs.last_activity_at, NOW()) as minutes_ago,
                        (SELECT COUNT(*) FROM visitor_activities WHERE session_id = vs.session_id OR ip_address = vs.ip_address) as activities_count
                    FROM visitor_sessions vs
                    LEFT JOIN customers c ON vs.customer_id = c.id
                    WHERE {$whereSql}
                    ORDER BY vs.last_activity_at DESC
                    LIMIT 100";
        $list = $db->query($sqlList, $params)->fetchAll(PDO::FETCH_ASSOC);

        // Enhance list with status
        foreach ($list as &$item) {
            $item['is_online'] = ($item['minutes_ago'] !== null && $item['minutes_ago'] <= 5);
        }

        return [
            'range' => $range,
            'active_now' => $activeNow,
            'unique_visitors' => (int)($summary['unique_visitors'] ?? 0),
            'total_sessions' => (int)($summary['total_sessions'] ?? 0),
            'total_page_views' => (int)($summary['total_page_views'] ?? 0),
            'cart_adds' => $cartAdds,
            'total_orders' => (int)($summary['total_orders'] ?? 0),
            'devices' => $devices,
            'visitors' => $list
        ];
    }

    /**
     * Get detailed activity timeline for an IP address or Session ID ("কি কি করেছে")
     */
    public static function getActivityTimeline($ipOrSession, $limit = 100) {
        $db = self::getDb();
        $sql = "SELECT va.*, 
                       vs.browser, vs.platform, vs.device_type,
                       COALESCE(vs.customer_name, c.name) as customer_name,
                       COALESCE(vs.customer_phone, c.phone) as customer_phone
                FROM visitor_activities va
                LEFT JOIN visitor_sessions vs ON va.session_id = vs.session_id
                LEFT JOIN customers c ON vs.customer_id = c.id
                WHERE va.ip_address = ? OR va.session_id = ?
                ORDER BY va.created_at DESC
                LIMIT " . intval($limit);
        
        $rows = $db->query($sql, [$ipOrSession, $ipOrSession])->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as &$r) {
            $r['meta'] = !empty($r['meta_data']) ? json_decode($r['meta_data'], true) : [];
        }
        return $rows;
    }
}
