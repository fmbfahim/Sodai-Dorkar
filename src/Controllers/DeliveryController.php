<?php

namespace Controllers;

use Core\Controller;
use Core\Middleware;
use Models\Order;

class DeliveryController extends Controller {
    
    public function __construct() {
        Middleware::auth(['delivery_man', 'admin']);
    }

    public function dashboard() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $dmId = $_SESSION['user_id'];

        $orderModel = new Order();
        $orders      = $orderModel->getByDeliveryMan($dmId);
        $history     = $orderModel->getDeliveryManHistory($dmId);
        $stats       = $orderModel->getDeliveryManStats($dmId);
        $fullStats   = $orderModel->getDeliveryManFullStats($dmId);
        $collections = $orderModel->getRiderCollections($dmId, 10);

        // Rider info with assigned areas
        $db = new \Core\Database(require __DIR__ . '/../../config/database.php');
        $riderInfo = $db->query("
            SELECT u.*, GROUP_CONCAT(a.name ORDER BY a.name SEPARATOR ', ') as assigned_areas
            FROM users u
            LEFT JOIN dm_allocations dma ON dma.user_id = u.id
            LEFT JOIN areas a ON dma.area_id = a.id
            WHERE u.id = ?
            GROUP BY u.id
        ", [$dmId])->fetch();

        $settingModel = new \Models\Setting();
        $allowTransfer = ($settingModel->get('allow_rider_transfer', '1') === '1');

        $userModel = new \Models\User();
        $allDeliveryMen = $userModel->getByRole('delivery_man');
        $otherRiders = array_filter($allDeliveryMen, function($u) use ($dmId) {
            return $u['id'] != $dmId;
        });

        $successMsg = $_SESSION['success'] ?? null;
        $errorMsg   = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        return $this->view('delivery/dashboard', [
            'title'        => 'Dashboard',
            'orders'       => $orders,
            'history'      => $history,
            'stats'        => $stats,
            'fullStats'    => $fullStats,
            'collections'  => $collections,
            'riderInfo'    => $riderInfo,
            'allowTransfer' => $allowTransfer,
            'otherRiders'  => array_values($otherRiders),
            'successMsg'   => $successMsg,
            'errorMsg'     => $errorMsg
        ], 'layouts/delivery');
    }


    public function parcelSearch() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $dmId  = $_SESSION['user_id'] ?? null;
        $query = trim($_GET['q'] ?? '');

        header('Content-Type: application/json');
        if (!$dmId || strlen($query) < 2) {
            echo json_encode([]);
            exit;
        }

        $orderModel = new Order();
        $results = $orderModel->searchParcelsForRider($dmId, $query);
        echo json_encode(array_values($results));
        exit;
    }

    public function transferOrder() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $currentDmId = $_SESSION['user_id'] ?? null;
        
        $orderId = $_POST['order_id'] ?? null;
        $targetDmId = $_POST['target_dm_id'] ?? null;
        $reason = trim($_POST['transfer_reason'] ?? '');

        $settingModel = new \Models\Setting();
        $allowTransfer = ($settingModel->get('allow_rider_transfer', '1') === '1');

        if (!$allowTransfer) {
            $_SESSION['error'] = 'Rider-to-rider parcel transfer is disabled by admin.';
            header('Location: /sodai-dorkar/public/delivery/dashboard');
            exit;
        }

        if ($orderId && $targetDmId && $currentDmId) {
            $orderModel = new Order();
            $order = $orderModel->find($orderId);

            // Verify order belongs to current delivery man and is active
            if ($order && $order['delivery_man_id'] == $currentDmId && !in_array($order['status'], ['delivered', 'cancelled'])) {
                $orderModel->assignDeliveryMan($orderId, $targetDmId);

                $userModel = new \Models\User();
                $targetRider = $userModel->find($targetDmId);
                $targetName = $targetRider ? $targetRider['name'] : 'Rider #' . $targetDmId;

                $_SESSION['success'] = "Order #{$orderId} successfully transferred to {$targetName}!";
            } else {
                $_SESSION['error'] = 'Invalid order or unauthorized transfer.';
            }
        }

        header('Location: /sodai-dorkar/public/delivery/dashboard');
        exit;
    }

    public function updateStatus() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $dmId = $_SESSION['user_id'] ?? null;
        $isAdmin = (($_SESSION['role'] ?? '') === 'admin');
        $id = $_POST['order_id'] ?? null;
        $status = $_POST['status'] ?? null;
        $paymentMethod = $_POST['payment_method'] ?? 'cash';
        $trxId = !empty($_POST['payment_trx_id']) ? trim($_POST['payment_trx_id']) : null;
        $cancelReason = !empty($_POST['cancel_reason']) ? trim($_POST['cancel_reason']) : null;
        $deliveryDiscount = isset($_POST['delivery_discount']) ? floatval($_POST['delivery_discount']) : 0;
        $amountChangeReason = !empty($_POST['amount_change_reason']) ? trim($_POST['amount_change_reason']) : null;
        $riderNote = !empty($_POST['rider_note']) ? trim($_POST['rider_note']) : null;

        if ($id && $status && ($dmId || $isAdmin)) {
            $orderModel = new Order();
            $order = $orderModel->find($id);
            if ($order && ($order['delivery_man_id'] == $dmId || $isAdmin)) {
                $orderModel->updateDeliveryStatus($id, $status, $paymentMethod, $trxId, $cancelReason, $deliveryDiscount, $amountChangeReason, $riderNote);

                if ($status === 'delivered') {
                    $msg = "Order #{$id} marked as Delivered!";
                    if ($deliveryDiscount > 0) {
                        $msg .= " (৳{$deliveryDiscount} discount applied)";
                    }
                    $_SESSION['success'] = $msg;
                } elseif ($status === 'cancelled') {
                    $_SESSION['error'] = "Order #{$id} marked as Cancelled.";
                } elseif ($status === 'returned') {
                    $_SESSION['success'] = "Order #{$id} marked as Returned to Warehouse.";
                } else {
                    $_SESSION['success'] = "Order #{$id} status updated to " . ucfirst(str_replace('_', ' ', $status)) . ".";
                }
            } else {
                $_SESSION['error'] = 'Invalid order or unauthorized access.';
            }
        }
        
        header('Location: /sodai-dorkar/public/delivery/dashboard');
        exit;
    }

    public function modifyOrder() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $dmId = $_SESSION['user_id'] ?? null;
        $isAdmin = (($_SESSION['role'] ?? '') === 'admin');
        $orderId = $_POST['order_id'] ?? null;
        $items = $_POST['items'] ?? [];

        if ($orderId && ($dmId || $isAdmin) && !empty($items)) {
            $orderModel = new Order();
            $order = $orderModel->find($orderId);
            
            if ($order && ($order['delivery_man_id'] == $dmId || $isAdmin)) {
                $db = new \Core\Database(require __DIR__ . '/../../config/database.php');
                $hasModifications = false;
                $totalDeliveredQty = 0;
                $totalReturnedQty = 0;
                $totalDamagedQty = 0;

                // Save original amount if not already saved
                if (empty($order['original_amount'])) {
                    $db->query("UPDATE orders SET original_amount = :orig WHERE id = :id", [
                        'orig' => $order['total_amount'],
                        'id'   => $orderId
                    ]);
                }

                foreach ($items as $item) {
                    $itemId = $item['id'] ?? null;
                    if (!$itemId) continue;

                    $stmt = $db->query("SELECT quantity, price FROM order_items WHERE id = ? AND order_id = ?", [$itemId, $orderId]);
                    $orderItem = $stmt->fetch();
                    if ($orderItem) {
                        $originalQty = intval($orderItem['quantity']);

                        $returnQty = isset($item['return_qty']) ? max(0, intval($item['return_qty'])) : 0;
                        $damageQty = isset($item['damage_qty']) ? max(0, intval($item['damage_qty'])) : 0;

                        // If deliver_qty is explicitly supplied and return_qty was omitted
                        if (isset($item['deliver_qty']) && !isset($item['return_qty'])) {
                            $deliverQty = min($originalQty, max(0, intval($item['deliver_qty'])));
                            $damageQty = min($damageQty, $originalQty - $deliverQty);
                            $returnQty = max(0, $originalQty - $deliverQty - $damageQty);
                        } else {
                            if ($returnQty + $damageQty > $originalQty) {
                                $returnQty = min($returnQty, $originalQty);
                                $damageQty = min($damageQty, $originalQty - $returnQty);
                            }
                        }
                        
                        $db->query("UPDATE order_items SET return_qty = ?, damage_qty = ? WHERE id = ?", [$returnQty, $damageQty, $itemId]);
                        
                        $effective = max(0, $originalQty - $returnQty - $damageQty);
                        $totalDeliveredQty += $effective;
                        $totalReturnedQty += $returnQty;
                        $totalDamagedQty += $damageQty;

                        if ($returnQty > 0 || $damageQty > 0) {
                            $hasModifications = true;
                        }
                    }
                }

                // Recalculate total amount
                $itemsAfterMod = $db->query("SELECT quantity, return_qty, damage_qty, price FROM order_items WHERE order_id = ?", [$orderId])->fetchAll();
                $itemsTotal = 0;
                foreach ($itemsAfterMod as $it) {
                    $effectiveQty = max(0, intval($it['quantity']) - intval($it['return_qty'] ?? 0) - intval($it['damage_qty'] ?? 0));
                    $itemsTotal += ($effectiveQty * floatval($it['price']));
                }

                $deliveryCharge = floatval($order['delivery_charge'] ?? 0);
                $deliveryDiscount = floatval($order['delivery_discount'] ?? 0);
                $finalTotal = max(0, $itemsTotal + $deliveryCharge - $deliveryDiscount);

                $db->query("UPDATE orders 
                            SET total_amount = :total,
                                amount_changed_by = :by,
                                amount_change_reason = :reason,
                                updated_at = NOW() 
                            WHERE id = :id", [
                    'total'  => $finalTotal,
                    'by'     => $isAdmin ? 'admin' : 'delivery_man',
                    'reason' => "Partial return / modified by rider (Delivered: {$totalDeliveredQty}, Returned: {$totalReturnedQty}, Damaged: {$totalDamagedQty})",
                    'id'     => $orderId
                ]);

                if ($hasModifications) {
                    $orderModel->addTrackingLog($orderId, $order['status'], "Order modified: Returned: {$totalReturnedQty}, Damaged: {$totalDamagedQty}. New Total: ৳" . number_format($finalTotal, 2));
                    $_SESSION['success'] = "অর্ডার #{$orderId} মডিফাই সম্পন্ন হয়েছে (ফেরত: {$totalReturnedQty} টি, ড্যামেজ: {$totalDamagedQty} টি)। নতুন কালেকশন বিল: ৳" . number_format($finalTotal, 2);
                } else {
                    $_SESSION['success'] = "অর্ডার #{$orderId} আইটেম আপডেট হয়েছে। মোট বিল: ৳" . number_format($finalTotal, 2);
                }
            } else {
                $_SESSION['error'] = "অননুমোদিত অনুরোধ বা ভুল অর্ডার।";
            }
        } else {
            $_SESSION['error'] = "কোন আইটেম ডেটা বা অর্ডার পাওয়া যায়নি।";
        }

        header('Location: /sodai-dorkar/public/delivery/dashboard');
        exit;
    }

    public function updateNote() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $dmId = $_SESSION['user_id'] ?? null;
        $isAdmin = (($_SESSION['role'] ?? '') === 'admin');
        $orderId = $_POST['order_id'] ?? null;
        $riderNote = trim($_POST['rider_note'] ?? '');

        if ($orderId && ($dmId || $isAdmin)) {
            $orderModel = new Order();
            $order = $orderModel->find($orderId);
            if ($order && ($order['delivery_man_id'] == $dmId || $isAdmin)) {
                $orderModel->updateNotes($orderId, null, $riderNote);
                $_SESSION['success'] = "Rider note saved for Order #{$orderId}.";
            } else {
                $_SESSION['error'] = "Unauthorized action or invalid order.";
            }
        }

        header('Location: /sodai-dorkar/public/delivery/dashboard');
        exit;
    }

    public function updateLocation() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $dmId = $_SESSION['user_id'] ?? null;
        $isAdmin = (($_SESSION['role'] ?? '') === 'admin');
        $orderId = $_POST['order_id'] ?? null;
        $customerId = $_POST['customer_id'] ?? null;
        $lat = !empty($_POST['latitude']) ? trim($_POST['latitude']) : null;
        $lng = !empty($_POST['longitude']) ? trim($_POST['longitude']) : null;

        if ($orderId && ($dmId || $isAdmin) && $lat && $lng) {
            $orderModel = new Order();
            $order = $orderModel->find($orderId);
            if ($order && ($order['delivery_man_id'] == $dmId || $isAdmin)) {
                // Update Order Location
                $db = new \Core\Database(require __DIR__ . '/../../config/database.php');
                $db->query("UPDATE orders SET latitude = :lat, longitude = :lng WHERE id = :id", [
                    'lat' => $lat,
                    'lng' => $lng,
                    'id' => $orderId
                ]);

                // Update Customer Location as well
                $cid = $customerId ?: ($order['customer_id'] ?? null);
                if ($cid) {
                    $db->query("UPDATE customers SET latitude = :lat, longitude = :lng WHERE id = :id", [
                        'lat' => $lat,
                        'lng' => $lng,
                        'id' => $cid
                    ]);
                }

                $orderModel->addTrackingLog($orderId, $order['status'], "GPS Location coordinates updated ({$lat}, {$lng}).");
                $_SESSION['success'] = "অর্ডার #{$orderId} এর কাস্টমার লোকেশন সফলভাবে সেভ করা হয়েছে।";
            } else {
                $_SESSION['error'] = 'Invalid order or unauthorized access.';
            }
        } else {
            $_SESSION['error'] = 'লোকেশন সেভ করা সম্ভব হয়নি। সঠিক কো-অর্ডিনেট পাওয়া যায়নি।';
        }

        header('Location: /sodai-dorkar/public/delivery/dashboard');
        exit;
    }

    public function profile() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userId = $_SESSION['user_id'];
        
        $db = new \Core\Database(require __DIR__ . '/../../config/database.php');
        $stmt = $db->query("SELECT * FROM users WHERE id = :id", ['id' => $userId]);
        $user = $stmt->fetch();

        $orderModel = new Order();
        $stats = $orderModel->getDeliveryManStats($userId);
        
        return $this->view('delivery/profile', [
            'title' => 'My Profile', 
            'user' => $user,
            'stats' => $stats
        ], 'layouts/delivery');
    }

    public function settings() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $error = $_SESSION['error'] ?? null;
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['error'], $_SESSION['success']);

        return $this->view('delivery/settings', [
            'title' => 'Settings',
            'error' => $error,
            'success' => $success
        ], 'layouts/delivery');
    }

    public function changePassword() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userId = $_SESSION['user_id'];

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($currentPassword) || empty($newPassword)) {
            $_SESSION['error'] = 'All password fields are required.';
            header('Location: /sodai-dorkar/public/delivery/settings');
            exit;
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = 'New password and confirmation do not match.';
            header('Location: /sodai-dorkar/public/delivery/settings');
            exit;
        }

        if (strlen($newPassword) < 4) {
            $_SESSION['error'] = 'Password must be at least 4 characters.';
            header('Location: /sodai-dorkar/public/delivery/settings');
            exit;
        }

        $db = new \Core\Database(require __DIR__ . '/../../config/database.php');
        $stmt = $db->query("SELECT * FROM users WHERE id = :id", ['id' => $userId]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($currentPassword, $user['password'])) {
            $_SESSION['error'] = 'Current password does not match.';
            header('Location: /sodai-dorkar/public/delivery/settings');
            exit;
        }

        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
        $db->query("UPDATE users SET password = :p WHERE id = :id", ['p' => $hashed, 'id' => $userId]);

        $_SESSION['success'] = 'Password updated successfully!';
        header('Location: /sodai-dorkar/public/delivery/settings');
        exit;
    }
}
