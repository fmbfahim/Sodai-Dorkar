<?php

namespace Models;

use Core\Database;
use PDO;

class Order {
    protected $db;

    public function __construct() {
        $config = require __DIR__ . '/../../config/database.php';
        $this->db = new Database($config);
    }

    public function all() {
        $sql = "SELECT orders.*, customers.name as customer_name, customers.phone as customer_phone,
                       users.name as agent_name, 
                       dm.name as delivery_man_name,
                       areas.name as area_name
                FROM orders 
                JOIN customers ON orders.customer_id = customers.id
                LEFT JOIN users ON orders.agent_id = users.id
                LEFT JOIN users as dm ON orders.delivery_man_id = dm.id
                LEFT JOIN areas ON orders.area_id = areas.id
                ORDER BY orders.id DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function assignDeliveryMan($orderId, $deliveryManId) {
        if (!empty($deliveryManId)) {
            $this->db->query("UPDATE orders SET delivery_man_id = :dm_id, status = 'out_for_delivery' WHERE id = :id", [
                'dm_id' => $deliveryManId,
                'id' => $orderId
            ]);
            $this->addTrackingLog($orderId, 'out_for_delivery', 'Assigned to rider.');
        } else {
            $this->db->query("UPDATE orders SET delivery_man_id = NULL, status = 'pending' WHERE id = :id", [
                'id' => $orderId
            ]);
            $this->addTrackingLog($orderId, 'pending', 'Unassigned from rider.');
        }
    }

    public function autoAssignOrder($orderId, $areaId) {
        if (empty($orderId) || empty($areaId)) {
            return null;
        }

        $settingModel = new \Models\Setting();
        $allocMode = $settingModel->get('delivery_allocation_mode', 'auto');
        if ($allocMode !== 'auto') {
            return null; // Manual assignment mode
        }

        $allocModel = new \Models\DmAllocation();
        $riders = $allocModel->getRidersForArea($areaId);

        if (empty($riders)) {
            return null; // No delivery man assigned to this union/area
        }

        $selectedDmId = null;

        if (count($riders) === 1) {
            $selectedDmId = $riders[0]['id'];
        } else {
            $strategy = $settingModel->get('delivery_auto_assign_strategy', 'least_busy');
            $riderIds = array_column($riders, 'id');

            if ($strategy === 'least_busy') {
                $placeholders = implode(',', array_fill(0, count($riderIds), '?'));
                $sql = "SELECT delivery_man_id, COUNT(*) as active_count 
                        FROM orders 
                        WHERE delivery_man_id IN ($placeholders) 
                        AND status IN ('pending', 'processing', 'out_for_delivery') 
                        GROUP BY delivery_man_id";
                $stmt = $this->db->query($sql, $riderIds);
                $counts = [];
                foreach ($stmt->fetchAll() as $row) {
                    $counts[$row['delivery_man_id']] = intval($row['active_count']);
                }

                $minCount = PHP_INT_MAX;
                foreach ($riders as $r) {
                    $c = $counts[$r['id']] ?? 0;
                    if ($c < $minCount) {
                        $minCount = $c;
                        $selectedDmId = $r['id'];
                    }
                }
            } else {
                // Round-robin / Least recently assigned order
                $placeholders = implode(',', array_fill(0, count($riderIds), '?'));
                $sql = "SELECT delivery_man_id, MAX(id) as last_order_id 
                        FROM orders 
                        WHERE delivery_man_id IN ($placeholders) 
                        GROUP BY delivery_man_id";
                $stmt = $this->db->query($sql, $riderIds);
                $lastOrders = [];
                foreach ($stmt->fetchAll() as $row) {
                    $lastOrders[$row['delivery_man_id']] = intval($row['last_order_id']);
                }

                $minOrderId = PHP_INT_MAX;
                foreach ($riders as $r) {
                    $lastId = $lastOrders[$r['id']] ?? 0;
                    if ($lastId < $minOrderId) {
                        $minOrderId = $lastId;
                        $selectedDmId = $r['id'];
                    }
                }
            }
        }

        if ($selectedDmId) {
            $this->assignDeliveryMan($orderId, $selectedDmId);
            return $selectedDmId;
        }

        return null;
    }

    public function getByDeliveryMan($dmId) {
        $sql = "SELECT orders.*, 
                       customers.name as customer_name, 
                       customers.phone as customer_phone, 
                       customers.address_details as customer_address_details,
                       customers.latitude as customer_lat, 
                       customers.longitude as customer_lng,
                       a.name as area_name,
                       z.name as zone_name,
                       p.name as point_name
                FROM orders 
                JOIN customers ON orders.customer_id = customers.id
                LEFT JOIN areas a ON COALESCE(orders.area_id, customers.area_id) = a.id
                LEFT JOIN zones z ON COALESCE(orders.zone_id, customers.zone_id) = z.id
                LEFT JOIN points p ON COALESCE(orders.point_id, customers.point_id) = p.id
                WHERE orders.delivery_man_id = :dm_id 
                AND orders.status IN ('processing', 'pending', 'out_for_delivery')
                ORDER BY orders.id DESC";
        $stmt = $this->db->query($sql, ['dm_id' => $dmId]);
        $orders = $stmt->fetchAll();

        foreach ($orders as &$order) {
            $stmtItems = $this->db->query("SELECT order_items.*, products.name as product_name, products.sku, products.image_path 
                                           FROM order_items 
                                           JOIN products ON order_items.product_id = products.id 
                                           WHERE order_items.order_id = :id", ['id' => $order['id']]);
            $order['items'] = $stmtItems->fetchAll();
        }

        return $orders;
    }

    public function getByCustomer($customerId) {
        $sql = "SELECT orders.*, 
                       users.name as agent_name, 
                       dm.name as delivery_man_name 
                FROM orders 
                LEFT JOIN users ON orders.agent_id = users.id
                LEFT JOIN users as dm ON orders.delivery_man_id = dm.id
                WHERE orders.customer_id = :customer_id 
                ORDER BY orders.id DESC";
        $stmt = $this->db->query($sql, ['customer_id' => $customerId]);
        return $stmt->fetchAll();
    }

    public function updateStatus($id, $status) {
        $this->db->query("UPDATE orders SET status = :status, updated_at = NOW() WHERE id = :id", [
            'status' => $status,
            'id' => $id
        ]);
        $this->addTrackingLog($id, $status, "Consignment status has been updated as " . ucfirst($status));
    }

    public function updateDeliveryStatus($id, $status, $paymentMethod = 'cash', $trxId = null, $cancelReason = null, $deliveryDiscount = 0, $amountChangeReason = null, $riderNote = null) {
        $sql = "UPDATE orders SET status = :status, updated_at = NOW()";
        $params = ['status' => $status, 'id' => $id];

        if ($status === 'delivered') {
            $sql .= ", payment_method = :pm, payment_trx_id = :trx, delivered_at = NOW()";
            $params['pm'] = $paymentMethod ?: 'cash';
            $params['trx'] = $trxId;

            // Handle rider discount / amount adjustment if given
            $deliveryDiscount = floatval($deliveryDiscount);
            if ($deliveryDiscount > 0) {
                $order = $this->find($id);
                if ($order) {
                    $orig = !empty($order['original_amount']) ? floatval($order['original_amount']) : floatval($order['total_amount']);
                    $newTotal = max(0, $orig - $deliveryDiscount);
                    
                    $sql .= ", original_amount = :orig_amt, delivery_discount = :disc, total_amount = :new_total, amount_changed_by = 'rider', amount_change_reason = :change_reason";
                    $params['orig_amt'] = $orig;
                    $params['disc'] = $deliveryDiscount;
                    $params['new_total'] = $newTotal;
                    $params['change_reason'] = !empty($amountChangeReason) ? trim($amountChangeReason) : 'Rider applied delivery discount';
                }
            }
        } elseif ($status === 'cancelled') {
            $sql .= ", cancel_reason = :reason";
            $params['reason'] = $cancelReason;
        }

        if (!empty($riderNote)) {
            $sql .= ", rider_note = :rider_note";
            $params['rider_note'] = trim($riderNote);
        }

        $sql .= " WHERE id = :id";
        $this->db->query($sql, $params);
        
        $msg = "Consignment status updated.";
        if ($status === 'delivered') {
            $msg = "Consignment has been marked as delivered.";
        } elseif ($status === 'cancelled') {
            $msg = "Consignment has been cancelled. Reason: " . $cancelReason;
        } elseif ($status === 'returned') {
            $msg = "Consignment has been returned.";
        }
        $this->addTrackingLog($id, $status, $msg);
    }

    public function restoreOrderStock($orderId) {
        $stmtItems = $this->db->query("SELECT product_id, quantity FROM order_items WHERE order_id = :id", ['id' => $orderId]);
        $items = $stmtItems->fetchAll(\PDO::FETCH_ASSOC);
        
        if ($items) {
            $stmtRestore = $this->db->getPDO()->prepare("UPDATE products SET stock_qty = stock_qty + :qty WHERE id = :id");
            foreach ($items as $item) {
                $stmtRestore->execute([
                    'qty' => $item['quantity'],
                    'id' => $item['product_id']
                ]);
            }
        }
    }

    public function adjustOrderAmount($orderId, $newTotal = null, $discount = 0, $reason = '', $changedBy = 'admin') {
        $order = $this->find($orderId);
        if (!$order) return false;

        $orig = !empty($order['original_amount']) ? floatval($order['original_amount']) : floatval($order['total_amount']);
        $discount = floatval($discount);

        if ($newTotal !== null && is_numeric($newTotal)) {
            $finalTotal = max(0, floatval($newTotal));
            $finalDiscount = max(0, $orig - $finalTotal);
        } else {
            $finalDiscount = $discount;
            $finalTotal = max(0, $orig - $finalDiscount);
        }

        $sql = "UPDATE orders 
                SET original_amount = :orig,
                    delivery_discount = :disc,
                    total_amount = :final_total,
                    amount_changed_by = :changed_by,
                    amount_change_reason = :reason,
                    updated_at = NOW()
                WHERE id = :id";
        
        $this->db->query($sql, [
            'orig' => $orig,
            'disc' => $finalDiscount,
            'final_total' => $finalTotal,
            'changed_by' => $changedBy,
            'reason' => trim($reason),
            'id' => $orderId
        ]);
        return true;
    }

    public function updateNotes($orderId, $adminNote = null, $riderNote = null) {
        $updates = [];
        $params = ['id' => $orderId];

        if ($adminNote !== null) {
            $updates[] = "admin_note = :admin_note";
            $params['admin_note'] = trim($adminNote);
        }

        if ($riderNote !== null) {
            $updates[] = "rider_note = :rider_note";
            $params['rider_note'] = trim($riderNote);
        }

        if (!empty($updates)) {
            $sql = "UPDATE orders SET " . implode(', ', $updates) . ", updated_at = NOW() WHERE id = :id";
            $this->db->query($sql, $params);
            
            if ($adminNote !== null) {
                $this->addTrackingLog($orderId, null, "Admin note updated.");
            }
            if ($riderNote !== null) {
                $this->addTrackingLog($orderId, null, "Rider note updated.");
            }
            
            return true;
        }
        return false;
    }
    
    public function find($id) {
        // Fetch order details
        $stmt = $this->db->query("SELECT orders.*, 
                                         customers.name as customer_name, 
                                         customers.unique_code, 
                                         customers.phone as customer_phone,
                                         customers.address_details as customer_address_details,
                                         customers.latitude as customer_lat,
                                         customers.longitude as customer_lng,
                                         a.name as area_name,
                                         z.name as zone_name,
                                         p.name as point_name
                                  FROM orders 
                                  JOIN customers ON orders.customer_id = customers.id 
                                  LEFT JOIN areas a ON COALESCE(orders.area_id, customers.area_id) = a.id
                                  LEFT JOIN zones z ON COALESCE(orders.zone_id, customers.zone_id) = z.id
                                  LEFT JOIN points p ON COALESCE(orders.point_id, customers.point_id) = p.id
                                  WHERE orders.id = :id", ['id' => $id]);
        $order = $stmt->fetch();
        
        if ($order) {
            // Fetch items
             $stmtItems = $this->db->query("SELECT order_items.*, products.name as product_name, products.sku, products.image_path 
                                           FROM order_items 
                                           JOIN products ON order_items.product_id = products.id 
                                           WHERE order_items.order_id = :id", ['id' => $id]);
             $order['items'] = $stmtItems->fetchAll();
        }
        
        return $order;
    }

    public function findPendingByCustomer($customerId) {
        $stmt = $this->db->query("SELECT id, total_amount FROM orders WHERE customer_id = :cid AND status = 'pending' ORDER BY id DESC LIMIT 1", ['cid' => $customerId]);
        return $stmt->fetch();
    }

    public function addItemsToOrder($orderId, $currentTotal, $items) {
        $pdo = $this->db->getConnection();
        
        try {
            $pdo->beginTransaction();

            $sqlItem = "INSERT INTO order_items (order_id, product_id, unit_title, base_qty, quantity, price) 
                        VALUES (:order_id, :product_id, :unit_title, :base_qty, :quantity, :price)";
            $stmtItem = $pdo->prepare($sqlItem);

            $sqlUpdateStock = "UPDATE products SET stock_qty = stock_qty - :qty WHERE id = :id";
            $stmtUpdateStock = $pdo->prepare($sqlUpdateStock);

            $newItemsTotal = 0;

            // Prepared statements for Check, Update, Insert
            $sqlCheck = "SELECT id, quantity FROM order_items 
                         WHERE order_id = :order_id AND product_id = :product_id 
                         AND (unit_title = :unit_title OR (unit_title IS NULL AND :unit_title_null IS NULL))";
            $stmtCheck = $pdo->prepare($sqlCheck);

            $sqlUpdateItem = "UPDATE order_items SET quantity = quantity + :qty WHERE id = :id";
            $stmtUpdateItem = $pdo->prepare($sqlUpdateItem);

            foreach ($items as $item) {
                $baseQty = floatval($item['base_qty'] ?? 1.000);
                $qtyOrdered = intval($item['quantity']);
                $deductStock = $baseQty * $qtyOrdered;
                $unitTitle = !empty($item['unit_title']) ? $item['unit_title'] : null;

                // 1. Check if item exists in this order with same unit
                $stmtCheck->execute([
                    'order_id' => $orderId, 
                    'product_id' => $item['product_id'],
                    'unit_title' => $unitTitle,
                    'unit_title_null' => $unitTitle
                ]);
                $existingItem = $stmtCheck->fetch(PDO::FETCH_ASSOC);

                if ($existingItem) {
                    // Update existing row
                    $stmtUpdateItem->execute([
                        'qty' => $qtyOrdered,
                        'id' => $existingItem['id']
                    ]);
                } else {
                    // Insert new row
                    $stmtItem->execute([
                        'order_id' => $orderId,
                        'product_id' => $item['product_id'],
                        'unit_title' => $unitTitle,
                        'base_qty' => $baseQty,
                        'quantity' => $qtyOrdered,
                        'price' => $item['price']
                    ]);
                }

                $stmtUpdateStock->execute([
                    'qty' => $deductStock,
                    'id' => $item['product_id']
                ]);

                $newItemsTotal += ($item['price'] * $qtyOrdered);
            }

            // Update Order Total
            $pdo->prepare("UPDATE orders SET total_amount = total_amount + :added_amount, updated_at = NOW() WHERE id = :id")
                ->execute(['added_amount' => $newItemsTotal, 'id' => $orderId]);

            $pdo->commit();
            return $orderId;

        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function createTransaction($orderData, $items) {
        $pdo = $this->db->getConnection();
        
        try {
            $pdo->beginTransaction();

            // 1. Create Order
            $sql = "INSERT INTO orders (customer_id, agent_id, area_id, zone_id, point_id, total_amount, delivery_charge, delivery_address, contact_number, latitude, longitude, created_at, status) 
                VALUES (:customer_id, :agent_id, :area_id, :zone_id, :point_id, :total_amount, :delivery_charge, :delivery_address, :contact_number, :latitude, :longitude, NOW(), 'processing')";
        
            $this->db->query($sql, [
                'customer_id' => $orderData['customer_id'],
                'agent_id' => $orderData['agent_id'],
                'area_id' => $orderData['area_id'] ?? null,
                'zone_id' => $orderData['zone_id'] ?? null,
                'point_id' => $orderData['point_id'] ?? null,
                'total_amount' => $orderData['total_amount'],
                'delivery_charge' => $orderData['delivery_charge'] ?? 0,
                'delivery_address' => $orderData['delivery_address'],
                'contact_number' => $orderData['contact_number'],
                'latitude' => $orderData['latitude'] ?? null,
                'longitude' => $orderData['longitude'] ?? null
            ]);
            
            $orderId = $pdo->lastInsertId();
            $this->addTrackingLog($orderId, 'processing', 'Consignment created by Sender (API/Admin).');

            // 2. Create Order Items & Update Stock
            $sqlItem = "INSERT INTO order_items (order_id, product_id, unit_title, base_qty, quantity, price) 
                        VALUES (:order_id, :product_id, :unit_title, :base_qty, :quantity, :price)";
            $stmtItem = $pdo->prepare($sqlItem);

            $sqlUpdateStock = "UPDATE products SET stock_qty = stock_qty - :qty WHERE id = :id";
            $stmtUpdateStock = $pdo->prepare($sqlUpdateStock);

            foreach ($items as $item) {
                $baseQty = floatval($item['base_qty'] ?? 1.000);
                $qtyOrdered = intval($item['quantity']);
                $deductStock = $baseQty * $qtyOrdered;
                $unitTitle = !empty($item['unit_title']) ? $item['unit_title'] : null;

                $stmtItem->execute([
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'unit_title' => $unitTitle,
                    'base_qty' => $baseQty,
                    'quantity' => $qtyOrdered,
                    'price' => $item['price']
                ]);

                // Deduct Stock
                $stmtUpdateStock->execute([
                    'qty' => $deductStock,
                    'id' => $item['product_id']
                ]);
            }

            $pdo->commit();
            return $orderId;

        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function updateTransaction($orderId, $orderData, $items) {
        $pdo = $this->db->getConnection();
        try {
            $pdo->beginTransaction();

            // 1. Get current items to restore stock
            $stmtOld = $pdo->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = :order_id");
            $stmtOld->execute(['order_id' => $orderId]);
            $oldItems = $stmtOld->fetchAll(PDO::FETCH_ASSOC);

            $stmtRestore = $pdo->prepare("UPDATE products SET stock_qty = stock_qty + :qty WHERE id = :id");
            foreach ($oldItems as $item) {
                $stmtRestore->execute(['qty' => $item['quantity'], 'id' => $item['product_id']]);
            }

            // 2. Delete old items
            $pdo->prepare("DELETE FROM order_items WHERE order_id = :order_id")->execute(['order_id' => $orderId]);

            // 3. Update order record
            $stmtUpdateOrder = $pdo->prepare("UPDATE orders SET customer_id = :customer_id, total_amount = :total_amount, delivery_address = :delivery_address, contact_number = :contact_number, latitude = :latitude, longitude = :longitude, updated_at = NOW() WHERE id = :id");
            $stmtUpdateOrder->execute([
                'customer_id' => $orderData['customer_id'],
                'total_amount' => $orderData['total_amount'],
                'delivery_address' => $orderData['delivery_address'] ?? '',
                'contact_number' => $orderData['contact_number'] ?? '',
                'latitude' => $orderData['latitude'] ?? null,
                'longitude' => $orderData['longitude'] ?? null,
                'id' => $orderId
            ]);

            // 4. Insert new items and deduct stock
            $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)");
            $stmtDeduct = $pdo->prepare("UPDATE products SET stock_qty = stock_qty - :qty WHERE id = :id");

            foreach ($items as $item) {
                $stmtItem->execute([
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);
                $stmtDeduct->execute([
                    'qty' => $item['quantity'],
                    'id' => $item['product_id']
                ]);
            }

            $pdo->commit();
            return true;
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function getStats() {
        $stats = [
            'total_orders' => 0,
            'total_revenue' => 0,
            'pending_delivery' => 0
        ];

        // Total Orders
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM orders WHERE status != 'cancelled'");
        $stats['total_orders'] = $stmt->fetch()['count'] ?? 0;

        // Total Revenue
        $stmt = $this->db->query("SELECT SUM(total_amount) as total FROM orders WHERE status != 'cancelled'");
        $stats['total_revenue'] = $stmt->fetch()['total'] ?? 0;

        // Pending Delivery
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM orders WHERE status IN ('pending', 'processing', 'packed', 'out_for_delivery')");
        $stats['pending_delivery'] = $stmt->fetch()['count'] ?? 0;

        return $stats;
    }

    public function getRecentOrders($limit = 5) {
        $sql = "SELECT orders.*, customers.name as customer_name 
                FROM orders 
                JOIN customers ON orders.customer_id = customers.id
                ORDER BY orders.id DESC 
                LIMIT $limit";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getDeliveryManStats($dmId) {
        $stats = [
            'delivered_today' => 0,
            'pending_count' => 0,
            'out_for_delivery' => 0,
            'cash_collected' => 0,
            'digital_collected' => 0,
            'total_collected' => 0,
            'cancelled_today' => 0
        ];
        
        $today = date('Y-m-d');

        // Delivered Today & Collections
        $sql = "SELECT COUNT(*) as count, 
                       COALESCE(SUM(total_amount), 0) as total,
                       COALESCE(SUM(CASE WHEN payment_method = 'cash' OR payment_method IS NULL THEN total_amount ELSE 0 END), 0) as cash_total,
                       COALESCE(SUM(CASE WHEN payment_method IN ('bkash', 'nagad', 'card') THEN total_amount ELSE 0 END), 0) as digital_total
                FROM orders 
                WHERE delivery_man_id = :dm_id 
                AND status = 'delivered' 
                AND DATE(updated_at) = :today";
        $stmt = $this->db->query($sql, ['dm_id' => $dmId, 'today' => $today]);
        $res = $stmt->fetch();
        $stats['delivered_today'] = (int)($res['count'] ?? 0);
        $stats['total_collected'] = (float)($res['total'] ?? 0);
        $stats['cash_collected'] = (float)($res['cash_total'] ?? 0);
        $stats['digital_collected'] = (float)($res['digital_total'] ?? 0);

        // Cancelled Today
        $sql = "SELECT COUNT(*) as count 
                FROM orders 
                WHERE delivery_man_id = :dm_id 
                AND status = 'cancelled' 
                AND DATE(updated_at) = :today";
        $stmt = $this->db->query($sql, ['dm_id' => $dmId, 'today' => $today]);
        $stats['cancelled_today'] = (int)($stmt->fetch()['count'] ?? 0);

        // Out For Delivery
        $sql = "SELECT COUNT(*) as count FROM orders WHERE delivery_man_id = :dm_id AND status = 'out_for_delivery'";
        $stmt = $this->db->query($sql, ['dm_id' => $dmId]);
        $stats['out_for_delivery'] = (int)($stmt->fetch()['count'] ?? 0);

        // Pending & Active
        $sql = "SELECT COUNT(*) as count 
                FROM orders 
                WHERE delivery_man_id = :dm_id 
                AND status IN ('pending', 'processing', 'out_for_delivery')";
        $stmt = $this->db->query($sql, ['dm_id' => $dmId]);
        $stats['pending_count'] = (int)($stmt->fetch()['count'] ?? 0);

        return $stats;
    }

    public function getDeliveryManFullStats($dmId) {
        $today      = date('Y-m-d');
        $monthStart = date('Y-m-01');

        $res = $this->db->query("SELECT
            COUNT(*) as total_assigned,
            SUM(CASE WHEN status='delivered' THEN 1 ELSE 0 END) as total_delivered,
            SUM(CASE WHEN status='cancelled' THEN 1 ELSE 0 END) as total_cancelled,
            SUM(CASE WHEN status='returned'  THEN 1 ELSE 0 END) as total_returned,
            COALESCE(SUM(CASE WHEN status='delivered' THEN total_amount ELSE 0 END),0) as total_cash_collected,
            COALESCE(SUM(CASE WHEN status='delivered' AND payment_method IN ('bkash','nagad','card') THEN total_amount ELSE 0 END),0) as total_digital,
            COALESCE(SUM(CASE WHEN status='delivered' AND (payment_method='cash' OR payment_method IS NULL) THEN total_amount ELSE 0 END),0) as total_cash
            FROM orders WHERE delivery_man_id = :dm_id",
            ['dm_id' => $dmId])->fetch();

        $resMonth = $this->db->query("SELECT
            COUNT(*) as month_total,
            SUM(CASE WHEN status='delivered' THEN 1 ELSE 0 END) as month_delivered,
            COALESCE(SUM(CASE WHEN status='delivered' THEN total_amount ELSE 0 END),0) as month_collected
            FROM orders WHERE delivery_man_id = :dm_id AND DATE(created_at) >= :ms",
            ['dm_id' => $dmId, 'ms' => $monthStart])->fetch();

        $resToday = $this->db->query("SELECT
            COALESCE(SUM(CASE WHEN status='delivered' THEN total_amount ELSE 0 END),0) as today_collected,
            COALESCE(SUM(CASE WHEN status='delivered' AND (payment_method='cash' OR payment_method IS NULL) THEN total_amount ELSE 0 END),0) as today_cash,
            COALESCE(SUM(CASE WHEN status='delivered' AND payment_method IN ('bkash','nagad','card') THEN total_amount ELSE 0 END),0) as today_digital,
            COUNT(CASE WHEN status='delivered' THEN 1 END) as today_delivered,
            COUNT(CASE WHEN status='cancelled' THEN 1 END) as today_cancelled
            FROM orders WHERE delivery_man_id = :dm_id AND DATE(updated_at) = :today",
            ['dm_id' => $dmId, 'today' => $today])->fetch();

        $resDeposit = $this->db->query(
            "SELECT COALESCE(SUM(amount),0) as total_deposited FROM rider_collections WHERE user_id = :dm_id",
            ['dm_id' => $dmId])->fetch();

        $totalCash      = (float)($res['total_cash'] ?? 0);
        $totalDeposited = (float)($resDeposit['total_deposited'] ?? 0);

        return [
            'total_assigned'       => (int)($res['total_assigned'] ?? 0),
            'total_delivered'      => (int)($res['total_delivered'] ?? 0),
            'total_cancelled'      => (int)($res['total_cancelled'] ?? 0),
            'total_returned'       => (int)($res['total_returned'] ?? 0),
            'total_cash_collected' => (float)($res['total_cash_collected'] ?? 0),
            'total_digital'        => (float)($res['total_digital'] ?? 0),
            'total_cash'           => $totalCash,
            'total_deposited'      => $totalDeposited,
            'pending_deposit'      => max(0, $totalCash - $totalDeposited),
            'month_total'          => (int)($resMonth['month_total'] ?? 0),
            'month_delivered'      => (int)($resMonth['month_delivered'] ?? 0),
            'month_collected'      => (float)($resMonth['month_collected'] ?? 0),
            'today_collected'      => (float)($resToday['today_collected'] ?? 0),
            'today_cash'           => (float)($resToday['today_cash'] ?? 0),
            'today_digital'        => (float)($resToday['today_digital'] ?? 0),
            'today_delivered'      => (int)($resToday['today_delivered'] ?? 0),
            'today_cancelled'      => (int)($resToday['today_cancelled'] ?? 0),
        ];
    }

    public function searchParcelsForRider($dmId, $query) {
        $query = trim($query);
        if (empty($query)) return [];

        $select = "SELECT o.*, 
                          c.name as customer_name, 
                          c.phone as customer_phone, 
                          c.address_details as customer_address_details,
                          c.latitude as customer_lat,
                          c.longitude as customer_lng,
                          a.name as area_name,
                          z.name as zone_name,
                          p.name as point_name
                   FROM orders o 
                   JOIN customers c ON o.customer_id = c.id
                   LEFT JOIN areas a ON COALESCE(o.area_id, c.area_id) = a.id
                   LEFT JOIN zones z ON COALESCE(o.zone_id, c.zone_id) = z.id
                   LEFT JOIN points p ON COALESCE(o.point_id, c.point_id) = p.id";

        if (is_numeric($query)) {
            return $this->db->query(
                "$select WHERE o.delivery_man_id = :dm AND o.id = :id LIMIT 10",
                ['dm' => $dmId, 'id' => (int)$query])->fetchAll();
        }

        $like = '%' . $query . '%';
        $pdo  = $this->db->getConnection();
        $stmt = $pdo->prepare(
            "$select WHERE o.delivery_man_id = ? AND (c.name LIKE ? OR c.phone LIKE ?)
             ORDER BY o.id DESC LIMIT 15");
        $stmt->execute([$dmId, $like, $like]);
        return $stmt->fetchAll();
    }

    public function getRiderCollections($dmId, $limit = 20) {
        $pdo  = $this->db->getConnection();
        $stmt = $pdo->prepare(
            "SELECT rc.*, u.name as recorded_by_name
             FROM rider_collections rc
             LEFT JOIN users u ON rc.collected_by = u.id
             WHERE rc.user_id = ?
             ORDER BY rc.created_at DESC LIMIT ?");
        $stmt->bindValue(1, (int)$dmId, \PDO::PARAM_INT);
        $stmt->bindValue(2, (int)$limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getDeliveryManHistory($dmId) {
        $sql = "SELECT orders.*, 
                       customers.name as customer_name, 
                       customers.phone as customer_phone, 
                       customers.address_details as customer_address_details,
                       customers.latitude as customer_lat, 
                       customers.longitude as customer_lng,
                       a.name as area_name,
                       z.name as zone_name,
                       p.name as point_name
                FROM orders 
                JOIN customers ON orders.customer_id = customers.id
                LEFT JOIN areas a ON COALESCE(orders.area_id, customers.area_id) = a.id
                LEFT JOIN zones z ON COALESCE(orders.zone_id, customers.zone_id) = z.id
                LEFT JOIN points p ON COALESCE(orders.point_id, customers.point_id) = p.id
                WHERE orders.delivery_man_id = :dm_id 
                AND orders.status IN ('delivered', 'cancelled', 'returned')
                ORDER BY orders.updated_at DESC
                LIMIT 50";
        $stmt = $this->db->query($sql, ['dm_id' => $dmId]);
        $history = $stmt->fetchAll();

        foreach ($history as &$order) {
            $stmtItems = $this->db->query("SELECT order_items.*, products.name as product_name, products.sku 
                                           FROM order_items 
                                           JOIN products ON order_items.product_id = products.id 
                                           WHERE order_items.order_id = :id", ['id' => $order['id']]);
            $order['items'] = $stmtItems->fetchAll();
        }

        return $history;
    }
    public function getCustomerStats($customerId) {
        // Total Orders
        $stmt = $this->db->query("SELECT COUNT(*) as total, 
                                         SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered,
                                         SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                                         SUM(CASE WHEN status = 'returned' THEN 1 ELSE 0 END) as returned,
                                         SUM(total_amount) as total_spent
                                  FROM orders WHERE customer_id = :id", ['id' => $customerId]);
        $res = $stmt->fetch();
        
        $total = $res['total'] > 0 ? $res['total'] : 0;
        $delivered = $res['delivered'] > 0 ? $res['delivered'] : 0;
        
        $ratio = $total > 0 ? round(($delivered / $total) * 100) : 0;
        
        return [
            'total_orders' => $total,
            'success_ratio' => $ratio,
            'total_spent' => $res['total_spent'] ?? 0
        ];
    }

    public function getDispatchQueue() {
        // Fetch orders that are in dispatch state
        $sql = "SELECT o.*, c.name as customer_name, c.phone as customer_phone, 
                       a.name as area_name, z.name as zone_name, u.name as delivery_man_name
                FROM orders o
                LEFT JOIN customers c ON o.customer_id = c.id
                LEFT JOIN areas a ON o.area_id = a.id
                LEFT JOIN zones z ON o.zone_id = z.id
                LEFT JOIN users u ON o.delivery_man_id = u.id
                WHERE o.status = 'dispatch'
                ORDER BY o.created_at DESC"; 
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }



    public function getSalesByDate($start, $end) {
        $sql = "SELECT DATE(created_at) as date, COUNT(*) as count, SUM(total_amount) as total 
                FROM orders 
                WHERE status != 'cancelled' 
                AND DATE(created_at) BETWEEN :start AND :end
                GROUP BY DATE(created_at)
                ORDER BY date DESC";
        $stmt = $this->db->query($sql, ['start' => $start, 'end' => $end]);
        return $stmt->fetchAll();
    }

    public function getDeliveryMenSummaryList() {
        $today = date('Y-m-d');
        $riders = $this->db->query("SELECT * FROM users WHERE role = 'delivery_man' ORDER BY id DESC")->fetchAll(\PDO::FETCH_ASSOC);

        $kpis = [
            'total_riders' => count($riders),
            'active_tasks' => 0,
            'delivered_today' => 0,
            'delivered_today_amount' => 0,
            'cash_in_hand_today' => 0
        ];

        foreach ($riders as &$r) {
            $dmId = $r['id'];

            // 1. Get allocated areas
            $sqlAlloc = "SELECT da.time_slot, a.name as area_name 
                         FROM dm_allocations da 
                         JOIN areas a ON da.area_id = a.id 
                         WHERE da.user_id = :dm_id";
            $r['allocations'] = $this->db->query($sqlAlloc, ['dm_id' => $dmId])->fetchAll(\PDO::FETCH_ASSOC);

            // 2. Active tasks (pending, processing, out_for_delivery)
            $sqlActive = "SELECT COUNT(*) as cnt, 
                                 SUM(CASE WHEN status = 'out_for_delivery' THEN 1 ELSE 0 END) as on_way_cnt,
                                 SUM(CASE WHEN status = 'out_for_delivery' AND updated_at < DATE_SUB(NOW(), INTERVAL 12 HOUR) THEN 1 ELSE 0 END) as overdue_cnt
                          FROM orders 
                          WHERE delivery_man_id = :dm_id 
                          AND status IN ('pending', 'processing', 'out_for_delivery')";
            $activeRes = $this->db->query($sqlActive, ['dm_id' => $dmId])->fetch();
            $r['active_tasks'] = (int)($activeRes['cnt'] ?? 0);
            $r['on_way_count'] = (int)($activeRes['on_way_cnt'] ?? 0);
            $r['overdue_count'] = (int)($activeRes['overdue_cnt'] ?? 0);

            // 3. Delivered Today
            $sqlToday = "SELECT COUNT(*) as count, 
                                COALESCE(SUM(total_amount), 0) as total,
                                COALESCE(SUM(CASE WHEN payment_method = 'cash' OR payment_method IS NULL THEN total_amount ELSE 0 END), 0) as cash_total,
                                COALESCE(SUM(CASE WHEN payment_method IN ('bkash', 'nagad', 'card') THEN total_amount ELSE 0 END), 0) as digital_total
                         FROM orders 
                         WHERE delivery_man_id = :dm_id 
                         AND status = 'delivered' 
                         AND DATE(updated_at) = :today";
            $todayRes = $this->db->query($sqlToday, ['dm_id' => $dmId, 'today' => $today])->fetch();
            $r['delivered_today'] = (int)($todayRes['count'] ?? 0);
            $r['delivered_today_amount'] = (float)($todayRes['total'] ?? 0);
            $r['cash_today'] = (float)($todayRes['cash_total'] ?? 0);
            $r['digital_today'] = (float)($todayRes['digital_total'] ?? 0);

            // 4. Lifetime Stats & Collections
            $sqlLife = "SELECT COUNT(*) as total_assigned,
                               SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as total_delivered,
                               SUM(CASE WHEN status = 'delivered' THEN total_amount ELSE 0 END) as total_delivered_amount,
                               SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as total_cancelled,
                               COALESCE(SUM(CASE WHEN status = 'delivered' AND (payment_method = 'cash' OR payment_method IS NULL) THEN total_amount ELSE 0 END), 0) as total_cash_collected
                        FROM orders 
                        WHERE delivery_man_id = :dm_id";
            $lifeRes = $this->db->query($sqlLife, ['dm_id' => $dmId])->fetch();
            $r['total_assigned'] = (int)($lifeRes['total_assigned'] ?? 0);
            $r['total_delivered'] = (int)($lifeRes['total_delivered'] ?? 0);
            $r['total_delivered_amount'] = (float)($lifeRes['total_delivered_amount'] ?? 0);
            $r['total_cancelled'] = (int)($lifeRes['total_cancelled'] ?? 0);
            $r['total_cash_collected'] = (float)($lifeRes['total_cash_collected'] ?? 0);

            // Fetch total deposited
            $depRes = $this->db->query("SELECT COALESCE(SUM(amount),0) as total_deposited FROM rider_collections WHERE user_id = :dm_id", ['dm_id' => $dmId])->fetch();
            $r['total_deposited'] = (float)($depRes['total_deposited'] ?? 0);
            $r['pending_deposit'] = max(0, $r['total_cash_collected'] - $r['total_deposited']);

            $completedSum = $r['total_delivered'] + $r['total_cancelled'];
            $r['success_rate'] = $completedSum > 0 ? round(($r['total_delivered'] / $completedSum) * 100) : 100;

            // Status Badge
            if ($r['on_way_count'] > 0) {
                $r['work_status'] = 'on_delivery'; // On the road
            } elseif ($r['active_tasks'] > 0) {
                $r['work_status'] = 'busy'; // Processing orders
            } elseif (!empty($r['allocations'])) {
                $r['work_status'] = 'active'; // Ready for tasks
            } else {
                $r['work_status'] = 'idle'; // No area allocated
            }

            // Global KPI increments
            $kpis['active_tasks'] += $r['active_tasks'];
            $kpis['delivered_today'] += $r['delivered_today'];
            $kpis['delivered_today_amount'] += $r['delivered_today_amount'];
            $kpis['cash_in_hand_today'] += $r['cash_today']; // Kept for reference
            $kpis['total_pending_cash'] = ($kpis['total_pending_cash'] ?? 0) + $r['pending_deposit'];
        }

        return [
            'riders' => $riders,
            'kpis' => $kpis
        ];
    }

    public function getDeliveryManFullReport($dmId) {
        $rider = $this->db->query("SELECT * FROM users WHERE id = :id AND role = 'delivery_man'", ['id' => $dmId])->fetch(\PDO::FETCH_ASSOC);
        if (!$rider) return null;

        // Allocations
        $sqlAlloc = "SELECT da.*, a.name as area_name 
                     FROM dm_allocations da 
                     JOIN areas a ON da.area_id = a.id 
                     WHERE da.user_id = :dm_id";
        $allocations = $this->db->query($sqlAlloc, ['dm_id' => $dmId])->fetchAll(\PDO::FETCH_ASSOC);

        // Detailed Stats
        $stats = $this->getDeliveryManStats($dmId);

        // Lifetime stats
        $sqlLife = "SELECT COUNT(*) as total_orders,
                           SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered,
                           SUM(CASE WHEN status = 'delivered' THEN total_amount ELSE 0 END) as delivered_amount,
                           SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                           SUM(CASE WHEN status = 'delivered' AND (payment_method = 'cash' OR payment_method IS NULL) THEN total_amount ELSE 0 END) as life_cash,
                           SUM(CASE WHEN status = 'delivered' AND payment_method IN ('bkash', 'nagad', 'card') THEN total_amount ELSE 0 END) as life_digital
                    FROM orders 
                    WHERE delivery_man_id = :dm_id";
        $lifeRes = $this->db->query($sqlLife, ['dm_id' => $dmId])->fetch();

        $stats['lifetime_orders'] = (int)($lifeRes['total_orders'] ?? 0);
        $stats['lifetime_delivered'] = (int)($lifeRes['delivered'] ?? 0);
        $stats['lifetime_delivered_amount'] = (float)($lifeRes['delivered_amount'] ?? 0);
        $stats['lifetime_cancelled'] = (int)($lifeRes['cancelled'] ?? 0);
        $stats['lifetime_cash'] = (float)($lifeRes['life_cash'] ?? 0);
        $stats['lifetime_digital'] = (float)($lifeRes['life_digital'] ?? 0);

        $completed = $stats['lifetime_delivered'] + $stats['lifetime_cancelled'];
        $stats['success_ratio'] = $completed > 0 ? round(($stats['lifetime_delivered'] / $completed) * 100) : 100;

        // Active Orders
        $activeOrders = $this->getByDeliveryMan($dmId);

        // Completed / Cancelled Order History
        $sqlHistory = "SELECT orders.*, customers.name as customer_name, customers.phone as customer_phone, customers.address_details, areas.name as area_name 
                       FROM orders 
                       JOIN customers ON orders.customer_id = customers.id 
                       LEFT JOIN areas ON orders.area_id = areas.id 
                       WHERE orders.delivery_man_id = :dm_id 
                       AND orders.status IN ('delivered', 'cancelled', 'returned') 
                       ORDER BY orders.updated_at DESC 
                       LIMIT 50";
        $history = $this->db->query($sqlHistory, ['dm_id' => $dmId])->fetchAll(\PDO::FETCH_ASSOC);

        return [
            'rider' => $rider,
            'allocations' => $allocations,
            'stats' => $stats,
            'activeOrders' => $activeOrders,
            'history' => $history
        ];
    }

    public function addTrackingLog($orderId, $status, $message) {
        $sql = "INSERT INTO order_tracking (order_id, status, message, created_at) VALUES (:order_id, :status, :message, NOW())";
        $this->db->query($sql, [
            'order_id' => $orderId,
            'status' => $status,
            'message' => $message
        ]);
    }

    public function getTrackingHistory($orderId) {
        $sql = "SELECT * FROM order_tracking WHERE order_id = :order_id ORDER BY created_at DESC";
        $stmt = $this->db->query($sql, ['order_id' => $orderId]);
        return $stmt->fetchAll();
    }
}
