<?php

namespace Models;

use Core\Database;
use PDO;

class IncompleteOrder {
    protected $db;

    public function __construct() {
        $config = require __DIR__ . '/../../config/database.php';
        $this->db = new Database($config);
    }

    /**
     * Get Client IP Address
     */
    public static function getClientIp() {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($parts[0]);
        }
        return substr($ip, 0, 45);
    }

    /**
     * Synchronize the current session's cart into incomplete_orders
     */
    public function syncCart($cart, $extraInfo = []) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $sessionId = session_id();
        if (empty($sessionId)) return null;

        $pdo = $this->db->getConnection();
        $ip = self::getClientIp();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        // Calculate count & total
        $itemsCount = 0;
        $totalAmount = 0.00;
        if (!empty($cart) && is_array($cart)) {
            foreach ($cart as $item) {
                $qty = intval($item['quantity'] ?? 1);
                $price = floatval($item['price'] ?? 0);
                $itemsCount += $qty;
                $totalAmount += ($qty * $price);
            }
        }

        // Check if customer info available
        $customerId = $extraInfo['customer_id'] ?? ($_SESSION['customer_id'] ?? null);
        $customerName = $extraInfo['customer_name'] ?? null;
        $customerPhone = $extraInfo['customer_phone'] ?? null;
        $customerAddress = $extraInfo['customer_address'] ?? null;

        if ($customerId && (empty($customerName) || empty($customerPhone))) {
            $stmt = $this->db->query("SELECT name, phone, address_details FROM customers WHERE id = ?", [$customerId]);
            if ($cust = $stmt->fetch()) {
                if (empty($customerName)) $customerName = $cust['name'];
                if (empty($customerPhone)) $customerPhone = $cust['phone'];
                if (empty($customerAddress)) $customerAddress = $cust['address_details'];
            }
        }

        // If cart is empty, check if existing record needs updating
        if (empty($cart)) {
            // Keep existing record but set cart empty or leave it as abandoned
            return null;
        }

        // Check if active incomplete order exists for this session
        $stmt = $this->db->query("SELECT id FROM incomplete_orders WHERE session_id = ? AND status = 'incomplete' ORDER BY id DESC LIMIT 1", [$sessionId]);
        $existing = $stmt->fetch();

        $cartJson = json_encode(array_values($cart), JSON_UNESCAPED_UNICODE);
        $now = date('Y-m-d H:i:s');

        if ($existing) {
            $sql = "UPDATE incomplete_orders SET 
                        customer_id = COALESCE(?, customer_id),
                        customer_name = COALESCE(?, customer_name),
                        customer_phone = COALESCE(?, customer_phone),
                        customer_address = COALESCE(?, customer_address),
                        ip_address = ?,
                        user_agent = ?,
                        cart_items = ?,
                        items_count = ?,
                        total_amount = ?,
                        updated_at = ?
                    WHERE id = ?";
            $this->db->query($sql, [
                $customerId,
                $customerName,
                $customerPhone,
                $customerAddress,
                $ip,
                $userAgent,
                $cartJson,
                $itemsCount,
                $totalAmount,
                $now,
                $existing['id']
            ]);
            return $existing['id'];
        } else {
            $sql = "INSERT INTO incomplete_orders 
                    (session_id, customer_id, customer_name, customer_phone, customer_address, ip_address, user_agent, cart_items, items_count, total_amount, status, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'incomplete', ?, ?)";
            $this->db->query($sql, [
                $sessionId,
                $customerId,
                $customerName,
                $customerPhone,
                $customerAddress,
                $ip,
                $userAgent,
                $cartJson,
                $itemsCount,
                $totalAmount,
                $now,
                $now
            ]);
            return $pdo->lastInsertId();
        }
    }

    /**
     * Mark incomplete order as converted when real order is placed
     */
    public function markConverted($sessionId, $orderId) {
        if (empty($sessionId)) return false;
        $now = date('Y-m-d H:i:s');
        $this->db->query("UPDATE incomplete_orders 
                          SET status = 'converted', converted_order_id = ?, updated_at = ? 
                          WHERE session_id = ? AND status = 'incomplete'", [
            $orderId, $now, $sessionId
        ]);
        return true;
    }

    /**
     * Get all incomplete orders with filter and pagination
     */
    public function all($status = 'incomplete', $limit = 50, $offset = 0) {
        $params = [];
        $sql = "SELECT io.*, 
                       c.name as registered_customer_name, 
                       c.phone as registered_customer_phone,
                       c.address_details as registered_customer_address
                FROM incomplete_orders io
                LEFT JOIN customers c ON io.customer_id = c.id";
        
        if ($status !== 'all') {
            $sql .= " WHERE io.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY io.updated_at DESC LIMIT " . intval($limit) . " OFFSET " . intval($offset);
        
        $stmt = $this->db->query($sql, $params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Decode cart items for convenience
        foreach ($rows as &$r) {
            $r['items'] = json_decode($r['cart_items'] ?? '[]', true) ?: [];
            if (empty($r['customer_name']) && !empty($r['registered_customer_name'])) {
                $r['customer_name'] = $r['registered_customer_name'];
            }
            if (empty($r['customer_phone']) && !empty($r['registered_customer_phone'])) {
                $r['customer_phone'] = $r['registered_customer_phone'];
            }
            if (empty($r['customer_address']) && !empty($r['registered_customer_address'])) {
                $r['customer_address'] = $r['registered_customer_address'];
            }
        }
        return $rows;
    }

    /**
     * Count incomplete orders
     */
    public function count($status = 'incomplete') {
        if ($status === 'all') {
            return (int)$this->db->query("SELECT COUNT(*) FROM incomplete_orders")->fetchColumn();
        }
        return (int)$this->db->query("SELECT COUNT(*) FROM incomplete_orders WHERE status = ?", [$status])->fetchColumn();
    }

    /**
     * Find single record by ID
     */
    public function find($id) {
        $stmt = $this->db->query("
            SELECT io.*, 
                   c.name as registered_customer_name, 
                   c.phone as registered_customer_phone,
                   c.address_details as registered_customer_address
            FROM incomplete_orders io
            LEFT JOIN customers c ON io.customer_id = c.id
            WHERE io.id = ?
        ", [$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $row['items'] = json_decode($row['cart_items'] ?? '[]', true) ?: [];
            if (empty($row['customer_name']) && !empty($row['registered_customer_name'])) {
                $row['customer_name'] = $row['registered_customer_name'];
            }
            if (empty($row['customer_phone']) && !empty($row['registered_customer_phone'])) {
                $row['customer_phone'] = $row['registered_customer_phone'];
            }
            if (empty($row['customer_address']) && !empty($row['registered_customer_address'])) {
                $row['customer_address'] = $row['registered_customer_address'];
            }
        }
        return $row;
    }

    /**
     * Delete an incomplete order
     */
    public function delete($id) {
        return $this->db->query("DELETE FROM incomplete_orders WHERE id = ?", [$id]);
    }

    /**
     * Get statistics summary for admin dashboard
     */
    public function getStats() {
        $today = date('Y-m-d');
        $todayCount = (int)$this->db->query("SELECT COUNT(*) FROM incomplete_orders WHERE status = 'incomplete' AND DATE(updated_at) = '{$today}'")->fetchColumn();
        $totalIncomplete = (int)$this->db->query("SELECT COUNT(*) FROM incomplete_orders WHERE status = 'incomplete'")->fetchColumn();
        $totalAmount = (float)$this->db->query("SELECT COALESCE(SUM(total_amount), 0) FROM incomplete_orders WHERE status = 'incomplete'")->fetchColumn();
        $convertedCount = (int)$this->db->query("SELECT COUNT(*) FROM incomplete_orders WHERE status = 'converted'")->fetchColumn();

        return [
            'today_count' => $todayCount,
            'total_incomplete' => $totalIncomplete,
            'total_amount' => $totalAmount,
            'converted_count' => $convertedCount
        ];
    }

    /**
     * Convert an incomplete order into an actual real order in orders table
     */
    public function convertToRealOrder($id, $adminUserId = null) {
        $inc = $this->find($id);
        if (!$inc || empty($inc['items'])) {
            throw new \Exception("Incomplete order data not found or empty.");
        }

        $pdo = $this->db->getConnection();
        $pdo->beginTransaction();

        try {
            $customerId = $inc['customer_id'];
            if (!$customerId) {
                // Check if customer exists with phone or create a quick guest customer
                $phone = !empty($inc['customer_phone']) ? $inc['customer_phone'] : ('guest_' . time());
                $name = !empty($inc['customer_name']) ? $inc['customer_name'] : 'Guest Customer';
                $address = !empty($inc['customer_address']) ? $inc['customer_address'] : 'Online / Cart Checkout';

                $cStmt = $this->db->query("SELECT id FROM customers WHERE phone = ? LIMIT 1", [$phone]);
                $cRow = $cStmt->fetch();
                if ($cRow) {
                    $customerId = $cRow['id'];
                } else {
                    $this->db->query("INSERT INTO customers (name, phone, address_details, created_at) VALUES (?, ?, ?, NOW())", [$name, $phone, $address]);
                    $customerId = $pdo->lastInsertId();
                }
            }

            $totalAmount = floatval($inc['total_amount']);
            $adminNote = "Converted from Incomplete Order #INC-" . $inc['id'] . ($adminUserId ? " by Admin" : "");
            $address = $inc['customer_address'] ?? 'Not specified';
            $phone = $inc['customer_phone'] ?? '';

            // Insert into orders
            $this->db->query("
                INSERT INTO orders 
                (customer_id, status, total_amount, original_amount, delivery_charge, delivery_discount, payment_method, admin_note, delivery_address, contact_number, created_at, updated_at)
                VALUES (?, 'pending', ?, ?, 0.00, 0.00, 'cash', ?, ?, ?, NOW(), NOW())
            ", [$customerId, $totalAmount, $totalAmount, $adminNote, $address, $phone]);
            $orderId = $pdo->lastInsertId();

            // Insert order items
            $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, unit_title, base_qty, quantity, price) VALUES (?, ?, ?, ?, ?, ?)");
            foreach ($inc['items'] as $item) {
                $pid = $item['product_id'] ?? null;
                if ($pid) {
                    $unitTitle = $item['variant_title'] ?? null;
                    $baseQty = floatval($item['base_qty'] ?? 1.0);
                    $qty = intval($item['quantity'] ?? 1);
                    $price = floatval($item['price'] ?? 0);
                    $stmtItem->execute([$orderId, $pid, $unitTitle, $baseQty, $qty, $price]);
                }
            }

            // Mark incomplete order as converted
            $this->db->query("UPDATE incomplete_orders SET status = 'converted', converted_order_id = ?, updated_at = NOW() WHERE id = ?", [$orderId, $id]);

            $pdo->commit();
            return $orderId;
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
