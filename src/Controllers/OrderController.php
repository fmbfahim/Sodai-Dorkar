<?php

namespace Controllers;

use Core\Controller;
use Core\Middleware;
use Models\Order;
use Models\Customer;
use Models\Product;
use Models\Category;
use Models\User;

class OrderController extends Controller {

    public function __construct() {
        Middleware::permission('orders');
    }
    
    public function index() {
        $orderModel = new Order();
        $userModel = new User();
        $allocationModel = new \Models\DmAllocation();
        
        $currentStatus = $_GET['status'] ?? 'all';
        $orders = $orderModel->all(); 
        
        if ($currentStatus !== 'all') {
            $orders = array_filter($orders, function($o) use ($currentStatus) {
                return strtolower(trim($o['status'])) === strtolower(trim($currentStatus));
            });
        }
        
        $deliveryMen = $userModel->getByRole('delivery_man');
        $allocations = $allocationModel->getAll();

        return $this->view('admin/orders/index', [
            'title' => 'Orders', 
            'orders' => $orders,
            'currentStatus' => $currentStatus,
            'deliveryMen' => $deliveryMen,
            'allocations' => $allocations
        ]);
    }

    public function packagingList() {
        $orderModel = new Order();
        $orders = $orderModel->all();
        
        $tab = $_GET['tab'] ?? 'new';
        $filteredOrders = [];
        $pickList = [];

        if ($tab === 'new') {
            $filteredOrders = array_filter($orders, fn($o) => strtolower(trim($o['status'])) === 'packaging');
        } elseif ($tab === 'picking') {
            $pickingOrders = array_filter($orders, fn($o) => strtolower(trim($o['status'])) === 'picking');
            
            // Calculate aggregated pick list and attach items
            foreach ($pickingOrders as $o) {
                // Fetch items for this order
                $items = $orderModel->find($o['id'])['items'] ?? [];
                $o['items'] = $items;
                $filteredOrders[] = $o; // Add to filtered list with items
                
                foreach ($items as $item) {
                    $pid = $item['product_id'];
                    $variant = $item['unit_title'] ?? '';
                    $key = $pid . '_' . $variant;
                    if (!isset($pickList[$key])) {
                        $pickList[$key] = [
                            'name' => $item['product_name'],
                            'variant' => $variant,
                            'qty' => 0,
                            'image' => $item['image_path'] ?? ''
                        ];
                    }
                    $pickList[$key]['qty'] += intval($item['quantity']);
                }
            }
        }
        
        return $this->view('admin/orders/packaging', [
            'title' => 'Packaging List', 
            'orders' => $filteredOrders,
            'tab' => $tab,
            'pickList' => $pickList
        ]);
    }

    public function bulkReceivePackaging() {
        $orderIds = $_POST['order_ids'] ?? [];
        if (!empty($orderIds) && is_array($orderIds)) {
            $orderModel = new Order();
            foreach ($orderIds as $id) {
                // Ensure it's in packaging status before receiving
                $order = $orderModel->find($id);
                if ($order && strtolower(trim($order['status'])) === 'packaging') {
                    $orderModel->updateStatus($id, 'picking');
                }
            }
        }
        
        $_SESSION['success'] = "Selected orders received for packaging.";
        header('Location: /sodai-dorkar/public/admin/orders/packaging?tab=picking');
        exit;
    }

    public function changeStatus() {
        $order_id = $_POST['order_id'] ?? null;
        $status = $_POST['status'] ?? null;
        $redirect = $_POST['redirect'] ?? $_SERVER['HTTP_REFERER'] ?? '/sodai-dorkar/public/admin/orders';

        if ($order_id && $status) {
            $orderModel = new Order();
            $orderModel->updateStatus($order_id, $status);
        }

        header('Location: ' . $redirect);
        exit;
    }

    public function show() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /sodai-dorkar/public/admin/orders');
            exit;
        }

        $orderModel = new Order();
        $order = $orderModel->find($id);

        if (!$order) {
            header('Location: /sodai-dorkar/public/admin/orders');
            exit;
        }

        $userModel = new \Models\User();
        $deliveryMen = $userModel->getByRole('delivery_man');
        
        $trackingHistory = $orderModel->getTrackingHistory($id);

        return $this->view('admin/orders/show', [
            'title' => 'Invoice #' . $order['id'], 
            'order' => $order,
            'deliveryMen' => $deliveryMen,
            'trackingHistory' => $trackingHistory
        ]);
    }

    public function assign() {
        $order_id = $_POST['order_id'] ?? null;
        $delivery_man_id = isset($_POST['delivery_man_id']) ? trim($_POST['delivery_man_id']) : null;

        if ($order_id) {
            $orderModel = new Order();
            $orderModel->assignDeliveryMan($order_id, !empty($delivery_man_id) ? $delivery_man_id : null);
        }
        
        $redirect = $_POST['redirect'] ?? $_SERVER['HTTP_REFERER'] ?? '/sodai-dorkar/public/admin/orders';
        header('Location: ' . $redirect);
        exit;
    }

    public function create() {
        $customerModel = new Customer();
        $productModel = new Product();
        
        $customers = $customerModel->all();
        $products = $productModel->all();

        // Check for pre-selected customer
        $selectedCustomerId = $_GET['customer_id'] ?? null;

        $categoryModel = new Category();
        $categories = $categoryModel->all();

        $settingModel = new \Models\Setting();
        $settings = $settingModel->getAll();

        return $this->view('admin/orders/create', [
            'title' => 'New Order',
            'customers' => $customers,
            'products' => $products,
            'categories' => $categories,
            'settings' => $settings,
            'selectedCustomerId' => $selectedCustomerId
        ]);
    }

    public function store() {
        // Expecting JSON payload because the form is complex
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid data']);
            exit;
        }

        if (session_status() === PHP_SESSION_NONE) session_start();
        $agentId = $_SESSION['user_id'] ?? null;

        // Fetch Customer Location Data
        $customerModel = new Customer();
        $customer = $customerModel->find($input['customer_id']);

        $orderData = [
            'customer_id' => $input['customer_id'],
            'agent_id' => $agentId,
            'area_id' => $customer['area_id'] ?? null,
            'zone_id' => $customer['zone_id'] ?? null,
            'point_id' => $customer['point_id'] ?? null,
            'total_amount' => $input['total'],
            'delivery_charge' => $input['delivery_charge'] ?? 0,
            'delivery_address' => $input['address'] ?? ($customer['address_details'] ?? ''),
            'contact_number' => $input['phone'] ?? ($customer['phone'] ?? ''),
            'latitude' => $input['latitude'] ?? ($customer['latitude'] ?? null),
            'longitude' => $input['longitude'] ?? ($customer['longitude'] ?? null)
        ];

        try {
            $orderModel = new Order();
            
            // Check for existing pending order
            $pendingOrder = $orderModel->findPendingByCustomer($input['customer_id']);
            
            if ($pendingOrder) {
                // Merge into existing order
                $orderModel->addItemsToOrder($pendingOrder['id'], $pendingOrder['total_amount'], $input['items']);
                $orderId = $pendingOrder['id'];
                $msg = "Added to existing Order #" . $orderId;
            } else {
                // Create new order
                $orderId = $orderModel->createTransaction($orderData, $input['items']);
                $msg = "Order created successfully!";
            }

            if (!empty($input['mark_delivered'])) {
                $orderModel->updateDeliveryStatus($orderId, 'delivered', 'cash');
            } else {
                // Auto assign order to area rider if enabled is removed from here
                // It now happens in Dispatch phase
            }
            
            if (ob_get_length()) ob_clean();
            header('Content-Type: application/json');
            $redirectUrl = !empty($input['mark_delivered']) 
                ? '/sodai-dorkar/public/admin/orders/show?id=' . $orderId 
                : '/sodai-dorkar/public/admin/orders';
            echo json_encode(['success' => true, 'order_id' => $orderId, 'message' => $msg, 'redirect' => $redirectUrl]);
        } catch (\Exception $e) {
            if (ob_get_length()) ob_clean();
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /sodai-dorkar/public/admin/orders');
            exit;
        }

        $orderModel = new Order();
        $order = $orderModel->find($id);

        if (!$order) {
            header('Location: /sodai-dorkar/public/admin/orders');
            exit;
        }

        $customerModel = new Customer();
        $productModel = new Product();
        $categoryModel = new Category();

        return $this->view('admin/orders/edit', [
            'title' => 'Edit Order #' . $order['id'],
            'order' => $order,
            'customers' => $customerModel->all(),
            'products' => $productModel->all(),
            'categories' => $categoryModel->all(),
            'selectedCustomerId' => $order['customer_id']
        ]);
    }

    public function update() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
             http_response_code(400); 
             header('Content-Type: application/json');
             echo json_encode(['error' => 'Missing ID']); 
             exit; 
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Invalid data']);
            exit;
        }

        // 1. Restore Stock for Old Items
        $orderModel = new Order();
        $oldOrder = $orderModel->find($id);
        
        try {
            $orderModel->updateTransaction($id, [
                'customer_id' => $input['customer_id'],
                'total_amount' => $input['total'],
                'delivery_address' => $input['address'] ?? ($oldOrder['delivery_address'] ?? ''), 
                'contact_number' => $input['phone'] ?? ($oldOrder['contact_number'] ?? '')
            ], $input['items']);

            if (ob_get_length()) ob_clean();
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Order Updated', 'redirect' => '/sodai-dorkar/public/admin/orders']);

        } catch (\Exception $e) {
            if (ob_get_length()) ob_clean();
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }
    public function checkPending() {
        $customerId = $_GET['customer_id'] ?? null;
        if (!$customerId) {
            echo json_encode(['success' => false]);
            exit;
        }

        $orderModel = new Order();
        $pendingOrder = $orderModel->findPendingByCustomer($customerId);
        
        $stats = $orderModel->getCustomerStats($customerId);

        echo json_encode([
            'success' => true,
            'exists' => !!$pendingOrder,
            'order' => $pendingOrder,
            'stats' => $stats
        ]);
        exit;
    }

    public function invoice() {
        $id = $_GET['id'] ?? null;
        if (!$id) die("Order ID required");

        $orderModel = new Order();
        $order = $orderModel->find($id);
        
        $settingModel = new \Models\Setting();
        $settings = $settingModel->getAll();

        return $this->view('admin/orders/invoice', [
            'order' => $order,
            'settings' => $settings
        ], false);
    }

    public function posReceipt() {
        $id = $_GET['id'] ?? null;
        if (!$id) die("Order ID required");

        $orderModel = new Order();
        $order = $orderModel->find($id);
        
        $settingModel = new \Models\Setting();
        $settings = $settingModel->getAll();

        return $this->view('admin/orders/pos_receipt', [
            'order' => $order,
            'settings' => $settings
        ], false);
    }

    public function label() {
        $id = $_GET['id'] ?? null;
        if (!$id) die("Order ID required");

        $orderModel = new Order();
        $order = $orderModel->find($id);
        
        $settingModel = new \Models\Setting();
        $settings = $settingModel->getAll();

        return $this->view('admin/orders/label', [
            'order' => $order, 
            'settings' => $settings
        ], false);
    }

    public function adjustAmount() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $orderId = $_POST['order_id'] ?? null;
        $adjustmentType = $_POST['adjustment_type'] ?? 'discount'; // 'discount' or 'new_total'
        $amountValue = floatval($_POST['amount_value'] ?? 0);
        $reason = trim($_POST['amount_change_reason'] ?? '');

        if ($orderId) {
            $orderModel = new Order();
            if ($adjustmentType === 'new_total') {
                $orderModel->adjustOrderAmount($orderId, $amountValue, 0, $reason, 'admin');
            } else {
                $orderModel->adjustOrderAmount($orderId, null, $amountValue, $reason, 'admin');
            }
            $_SESSION['success'] = "Order #{$orderId} amount adjusted successfully!";
        }

        $redirect = $_POST['redirect'] ?? ('/sodai-dorkar/public/admin/orders/show?id=' . $orderId);
        header('Location: ' . $redirect);
        exit;
    }

    public function updateNotes() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $isJson = (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false)
                  || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
                  || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

        $input = $_POST;
        if (empty($input)) {
            $raw = file_get_contents('php://input');
            if (!empty($raw)) {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) $input = $decoded;
            }
        }

        $orderId = $input['order_id'] ?? null;
        $adminNote = isset($input['admin_note']) ? trim($input['admin_note']) : null;
        $riderNote = isset($input['rider_note']) ? trim($input['rider_note']) : null;

        if ($orderId) {
            $orderModel = new Order();
            $orderModel->updateNotes($orderId, $adminNote, $riderNote);
            $_SESSION['success'] = "Order notes updated successfully!";

            if ($isJson) {
                if (ob_get_length()) ob_clean();
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Admin note saved successfully!',
                    'admin_note' => $adminNote
                ]);
                exit;
            }
        } else {
            if ($isJson) {
                if (ob_get_length()) ob_clean();
                http_response_code(400);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Order ID is required']);
                exit;
            }
        }

        $redirect = $input['redirect'] ?? ('/sodai-dorkar/public/admin/orders/show?id=' . $orderId);
        header('Location: ' . $redirect);
        exit;
    }

    public function returns() {
        $orderModel = new Order();
        $sql = "SELECT DISTINCT o.*, 
                       c.name as customer_name, c.phone as customer_phone,
                       dm.name as rider_name
                FROM orders o
                LEFT JOIN order_items oi ON o.id = oi.order_id
                LEFT JOIN customers c ON o.customer_id = c.id
                LEFT JOIN users dm ON o.delivery_man_id = dm.id
                WHERE o.status = 'returned' OR oi.return_qty > 0
                ORDER BY o.updated_at DESC";
        $pendingReturns = $orderModel->getDb()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($pendingReturns as &$order) {
            $order['items'] = $orderModel->getDb()->query(
                "SELECT oi.*, p.name as product_name 
                 FROM order_items oi 
                 JOIN products p ON oi.product_id = p.id 
                 WHERE oi.order_id = ? AND (oi.return_qty > 0 OR ? = 'returned')", 
                [$order['id'], $order['status']]
            )->fetchAll(\PDO::FETCH_ASSOC);
        }

        return $this->view('admin/orders/returns', [
            'title' => 'Returns & Damage Management',
            'returns' => $pendingReturns
        ]);
    }

    public function processReturn() {
        $orderId = $_POST['order_id'] ?? null;
        $action = $_POST['action'] ?? null; // 'receive' or 'damage'
        $adminNote = trim($_POST['admin_note'] ?? '');

        if ($orderId && $action) {
            $orderModel = new Order();
            $order = $orderModel->find($orderId);

            if ($order) {
                $db = $orderModel->getDb();
                $isFullReturn = ($order['status'] === 'returned');
                
                // Get all items that need processing
                $items = $db->query("SELECT * FROM order_items WHERE order_id = ?", [$orderId])->fetchAll(\PDO::FETCH_ASSOC);
                
                $stockRestored = false;
                $hasItemsToProcess = false;

                foreach ($items as $item) {
                    $qtyToProcess = 0;
                    if ($isFullReturn) {
                        $qtyToProcess = $item['quantity'];
                    } elseif ($item['return_qty'] > 0 || $item['damage_qty'] > 0) {
                        // For partial returns, only process what was marked returned by the rider.
                        // Since they marked it during delivery, we just handle what's pending.
                        $qtyToProcess = $item['return_qty'] + $item['damage_qty'];
                    }

                    if ($qtyToProcess > 0) {
                        $hasItemsToProcess = true;
                        if ($action === 'receive') {
                            $db->query("UPDATE products SET stock = stock + ? WHERE id = ?", [$qtyToProcess, $item['product_id']]);
                            $stockRestored = true;
                        }
                    }
                }

                if ($hasItemsToProcess) {
                    if ($isFullReturn) {
                        $newStatus = ($action === 'receive') ? 'return_received' : 'damaged';
                        $orderModel->updateStatus($orderId, $newStatus);
                    } else {
                        // It was a partial return, we processed the items.
                        // We reset return_qty and damage_qty to 0 so they don't show up in Returns page again
                        $db->query("UPDATE order_items SET return_qty = 0, damage_qty = 0 WHERE order_id = ?", [$orderId]);
                    }

                    $msg = $stockRestored ? "Returns processed successfully. Stock has been restored." : "Products marked as damaged. Stock was NOT restored.";

                    if (!empty($adminNote)) {
                        $newNote = "Admin Note (" . date('d M Y') . "): " . $adminNote;
                        $existingNote = $order['admin_note'];
                        $finalNote = $existingNote ? $existingNote . "\n" . $newNote : $newNote;
                        $orderModel->updateNotes($orderId, $finalNote, $order['rider_note']);
                    }

                    $_SESSION['success'] = $msg;
                } else {
                    $_SESSION['error'] = "No returnable items found for this order.";
                }
            } else {
                $_SESSION['error'] = "Order not found.";
            }
        }

        header('Location: /sodai-dorkar/public/admin/orders/returns');
        exit;
    }
}
