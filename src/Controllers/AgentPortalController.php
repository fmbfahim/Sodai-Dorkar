<?php

namespace Controllers;

use Core\Controller;
use Core\Middleware;
use Core\View;
use Models\AgentAllocation;
use Models\Area;
use Models\Category;
use Models\Customer;
use Models\Order;
use Models\Point;
use Models\Product;
use Models\Setting;
use Models\User;
use Models\Zone;

class AgentPortalController extends Controller {
    protected $agentId;
    protected $allocationModel;

    public function __construct() {
        Middleware::auth(['agent', 'admin']);
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->agentId = (int)($_SESSION['user_id'] ?? 0);
        $this->allocationModel = new AgentAllocation();
    }

    /**
     * Agent Dashboard
     */
    public function dashboard() {
        $orderModel = new Order();
        $userModel = new User();

        $agent = $userModel->find($this->agentId);
        $assignedAreas = $this->allocationModel->getByAgent($this->agentId);
        $stats = $orderModel->getAgentStats($this->agentId);
        $recentOrders = $orderModel->getByAgent($this->agentId, ['limit' => 10]);

        return $this->view('agent/dashboard', [
            'title' => 'এজেন্ট ড্যাশবোর্ড',
            'agent' => $agent,
            'assignedAreas' => $assignedAreas,
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'success' => $_SESSION['success'] ?? null,
            'error' => $_SESSION['error'] ?? null
        ], 'layouts/agent');
    }

    /**
     * Agent Mobile Shop Catalog (Browse & Add to Cart)
     */
    public function shop() {
        $categoryModel = new Category();
        $productModel = new Product();

        $search = trim($_GET['search'] ?? '');
        $catId = !empty($_GET['category_id']) ? (int)$_GET['category_id'] : null;

        // Categories list
        $categories = $categoryModel->all();

        // Products query with filters
        $filters = ['availability_status' => ['in_stock', 'available']];
        if ($search) $filters['search'] = $search;
        if ($catId) $filters['category_id'] = $catId;

        $products = $productModel->all($filters);

        // Current cart summary
        $cart = $_SESSION['agent_cart'] ?? [];
        $cartCount = 0;
        $cartTotal = 0;
        foreach ($cart as $item) {
            $cartCount += (int)($item['quantity'] ?? 1);
            $cartTotal += (float)($item['price'] ?? 0) * (int)($item['quantity'] ?? 1);
        }

        $assignedAreas = $this->allocationModel->getByAgent($this->agentId);

        return $this->view('agent/shop', [
            'title' => 'পণ্য ক্যাটালগ ও অর্ডার',
            'categories' => $categories,
            'products' => $products,
            'activeCatId' => $catId,
            'search' => $search,
            'cartCount' => $cartCount,
            'cartTotal' => $cartTotal,
            'assignedAreas' => $assignedAreas
        ], 'layouts/agent');
    }

    /**
     * Get Current Agent Cart JSON
     */
    public function cartData() {
        header('Content-Type: application/json; charset=utf-8');
        $cart = $_SESSION['agent_cart'] ?? [];
        
        $subtotal = 0;
        $totalItems = 0;
        foreach ($cart as $item) {
            $subtotal += (float)$item['price'] * (int)$item['quantity'];
            $totalItems += (int)$item['quantity'];
        }

        echo json_encode([
            'success' => true,
            'items' => array_values($cart),
            'totalItems' => $totalItems,
            'subtotal' => $subtotal
        ]);
        exit;
    }

    /**
     * Add product to agent cart (AJAX)
     */
    public function addToCart() {
        header('Content-Type: application/json; charset=utf-8');
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true) ?: $_POST;

        $productId = (int)($data['product_id'] ?? 0);
        $qty = max(1, (int)($data['quantity'] ?? 1));

        if (!$productId) {
            echo json_encode(['success' => false, 'message' => 'পণ্য পাওয়া যায়নি']);
            exit;
        }

        $productModel = new Product();
        $product = $productModel->find($productId);

        if (!$product || !in_array($product['availability_status'] ?? '', ['in_stock', 'available'])) {
            echo json_encode(['success' => false, 'message' => 'পণ্যটি বর্তমানে স্টকে নেই']);
            exit;
        }

        if (!isset($_SESSION['agent_cart'])) {
            $_SESSION['agent_cart'] = [];
        }

        $key = 'p_' . $productId;
        $price = (float)($product['sell_price'] ?? 0);

        if (isset($_SESSION['agent_cart'][$key])) {
            $_SESSION['agent_cart'][$key]['quantity'] += $qty;
        } else {
            $_SESSION['agent_cart'][$key] = [
                'id' => $productId,
                'name' => $product['name'],
                'price' => $price,
                'quantity' => $qty,
                'image_path' => $product['image_path'] ?? null,
                'unit' => $product['base_unit'] ?? $product['unit_type'] ?? 'Pcs',
                'sku' => $product['sku'] ?? ''
            ];
        }

        $this->cartData();
    }

    /**
     * Update quantity in agent cart (AJAX)
     */
    public function updateCart() {
        header('Content-Type: application/json; charset=utf-8');
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true) ?: $_POST;

        $productId = (int)($data['product_id'] ?? 0);
        $action = $data['action'] ?? ''; // 'inc', 'dec', or 'set'
        $setQty = isset($data['quantity']) ? (int)$data['quantity'] : null;

        $key = 'p_' . $productId;

        if (isset($_SESSION['agent_cart'][$key])) {
            if ($action === 'inc') {
                $_SESSION['agent_cart'][$key]['quantity'] += 1;
            } elseif ($action === 'dec') {
                $_SESSION['agent_cart'][$key]['quantity'] -= 1;
                if ($_SESSION['agent_cart'][$key]['quantity'] <= 0) {
                    unset($_SESSION['agent_cart'][$key]);
                }
            } elseif ($setQty !== null) {
                if ($setQty <= 0) {
                    unset($_SESSION['agent_cart'][$key]);
                } else {
                    $_SESSION['agent_cart'][$key]['quantity'] = $setQty;
                }
            }
        }

        $this->cartData();
    }

    /**
     * Remove item from agent cart (AJAX)
     */
    public function removeFromCart() {
        header('Content-Type: application/json; charset=utf-8');
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true) ?: $_POST;

        $productId = (int)($data['product_id'] ?? 0);
        $key = 'p_' . $productId;

        if (isset($_SESSION['agent_cart'][$key])) {
            unset($_SESSION['agent_cart'][$key]);
        }

        $this->cartData();
    }

    /**
     * Clear agent cart
     */
    public function clearCart() {
        $_SESSION['agent_cart'] = [];
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && stripos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
            $this->cartData();
        }
        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
        header("Location: {$base}/agent/shop");
        exit;
    }

    /**
     * Agent Checkout Screen
     */
    public function checkout() {
        $cart = $_SESSION['agent_cart'] ?? [];
        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';

        if (empty($cart)) {
            header("Location: {$base}/agent/shop");
            exit;
        }

        $assignedAreas = $this->allocationModel->getByAgent($this->agentId);
        
        // If agent has assigned areas, load only those. Otherwise, load all areas.
        $areaModel = new Area();
        if (!empty($assignedAreas)) {
            $availableAreas = $assignedAreas;
        } else {
            $availableAreas = $areaModel->all();
        }

        // Subtotal calculation
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += (float)$item['price'] * (int)$item['quantity'];
        }

        return $this->view('agent/checkout', [
            'title' => 'চেকআউট ও কাস্টমার নির্বাচন',
            'cart' => $cart,
            'subtotal' => $subtotal,
            'assignedAreas' => $assignedAreas,
            'availableAreas' => $availableAreas,
            'agentId' => $this->agentId
        ], 'layouts/agent');
    }

    /**
     * Search Customers filtered by Agent's Assigned Area(s) (AJAX)
     */
    public function searchCustomers() {
        header('Content-Type: application/json; charset=utf-8');
        $q = trim($_GET['q'] ?? '');

        if (empty($q)) {
            echo json_encode([]);
            exit;
        }

        $customerModel = new Customer();
        $assignedAreaIds = $this->allocationModel->getAreaIdsByAgent($this->agentId);

        $filters = ['search' => $q];
        if (!empty($assignedAreaIds)) {
            $filters['area_ids'] = $assignedAreaIds;
        }

        $customers = $customerModel->all($filters);

        // Limit results to 20 for fast response
        $results = array_slice($customers, 0, 20);

        echo json_encode($results);
        exit;
    }

    /**
     * Create New Customer directly from Agent Panel (AJAX / POST)
     */
    public function storeCustomer() {
        header('Content-Type: application/json; charset=utf-8');
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true) ?: $_POST;

        $name = trim($data['name'] ?? '');
        $phone = trim($data['phone'] ?? '');
        $areaId = (int)($data['area_id'] ?? 0);
        $zoneId = !empty($data['zone_id']) ? (int)$data['zone_id'] : null;
        $pointId = !empty($data['point_id']) ? (int)$data['point_id'] : null;
        $address = trim($data['address_details'] ?? '');

        if (empty($name) || empty($phone) || empty($areaId)) {
            echo json_encode(['success' => false, 'message' => 'নাম, মোবাইল নম্বর এবং এরিয়া আবশ্যক']);
            exit;
        }

        // Validate agent's assigned area restriction
        $assignedAreaIds = $this->allocationModel->getAreaIdsByAgent($this->agentId);
        if (!empty($assignedAreaIds) && !in_array($areaId, $assignedAreaIds)) {
            echo json_encode([
                'success' => false, 
                'message' => 'আপনি শুধুমাত্র আপনার এসাইন করা এরিয়ার কাস্টমার যুক্ত করতে পারবেন।'
            ]);
            exit;
        }

        $customerModel = new Customer();

        // Check if customer with same phone already exists
        $existing = $customerModel->all(['search' => $phone]);
        foreach ($existing as $ex) {
            if ($ex['phone'] === $phone) {
                // If exists in same or allowed area, return existing customer
                echo json_encode([
                    'success' => true,
                    'alreadyExists' => true,
                    'customer' => $ex,
                    'message' => 'এই নম্বরের কাস্টমার ইতিমধ্যে নিবন্ধিত রয়েছে। কাস্টমারটি নির্বাচিত হয়েছে।'
                ]);
                exit;
            }
        }

        try {
            $newCustomerId = $customerModel->create([
                'name' => $name,
                'phone' => $phone,
                'area_id' => $areaId,
                'zone_id' => $zoneId,
                'point_id' => $pointId,
                'address_details' => $address,
                'demographics_json' => json_encode(['created_by_agent_id' => $this->agentId]),
                'latitude' => null,
                'longitude' => null
            ]);

            // Fetch newly created customer with area & zone names
            $customer = $customerModel->find($newCustomerId);
            
            // Enrich with area/zone names
            $areaModel = new Area();
            $area = $areaModel->find($areaId);
            $customer['area_name'] = $area['name'] ?? '';

            if ($zoneId) {
                $zoneModel = new Zone();
                $zone = $zoneModel->find($zoneId);
                $customer['zone_name'] = $zone['name'] ?? '';
            }

            if ($pointId) {
                $pointModel = new Point();
                $point = $pointModel->find($pointId);
                $customer['point_name'] = $point['name'] ?? '';
            }

            echo json_encode([
                'success' => true,
                'customer' => $customer,
                'message' => 'নতুন কাস্টমার সফলভাবে যুক্ত হয়েছে!'
            ]);
            exit;
        } catch (\Throwable $e) {
            echo json_encode([
                'success' => false,
                'message' => 'কাস্টমার সংরক্ষণ ব্যর্থ হয়েছে: ' . $e->getMessage()
            ]);
            exit;
        }
    }

    /**
     * Dynamic cascaded dropdown: Zones by Area
     */
    public function getZones() {
        header('Content-Type: application/json; charset=utf-8');
        $areaId = (int)($_GET['area_id'] ?? 0);
        if (!$areaId) {
            echo json_encode([]);
            exit;
        }
        $zoneModel = new Zone();
        echo json_encode($zoneModel->getByArea($areaId));
        exit;
    }

    /**
     * Dynamic cascaded dropdown: Points by Zone
     */
    public function getPoints() {
        header('Content-Type: application/json; charset=utf-8');
        $zoneId = (int)($_GET['zone_id'] ?? 0);
        if (!$zoneId) {
            echo json_encode([]);
            exit;
        }
        $pointModel = new Point();
        echo json_encode($pointModel->getByZone($zoneId));
        exit;
    }

    /**
     * Place Order on Behalf of Selected Customer
     */
    public function placeOrder() {
        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
        $cart = $_SESSION['agent_cart'] ?? [];

        if (empty($cart)) {
            $_SESSION['error'] = 'কার্ট খালি রয়েছে। অনুগ্রহ করে পণ্য যুক্ত করুন।';
            header("Location: {$base}/agent/shop");
            exit;
        }

        $customerId = (int)($_POST['customer_id'] ?? 0);
        if (!$customerId) {
            $_SESSION['error'] = 'অনুগ্রহ করে একজন কাস্টমার নির্বাচন করুন অথবা নতুন কাস্টমার যোগ করুন।';
            header("Location: {$base}/agent/checkout");
            exit;
        }

        $customerModel = new Customer();
        $customer = $customerModel->find($customerId);

        if (!$customer) {
            $_SESSION['error'] = 'নির্বাচিত কাস্টমার পাওয়া যায়নি।';
            header("Location: {$base}/agent/checkout");
            exit;
        }

        // Security Area Check: Verify customer belongs to agent's assigned area(s)
        $assignedAreaIds = $this->allocationModel->getAreaIdsByAgent($this->agentId);
        if (!empty($assignedAreaIds) && !empty($customer['area_id']) && !in_array((int)$customer['area_id'], $assignedAreaIds)) {
            $_SESSION['error'] = 'এই কাস্টমার আপনার নির্ধারিত এরিয়ার অন্তর্ভুক্ত নয়।';
            header("Location: {$base}/agent/checkout");
            exit;
        }

        // Calculate Subtotal & Weight
        $subtotal = 0;
        $totalWeightKg = 0;
        foreach ($cart as $item) {
            $subtotal += (float)$item['price'] * (int)$item['quantity'];
            $totalWeightKg += (float)($item['base_qty'] ?? 1.0) * (int)$item['quantity'];
        }

        // Payment method
        $rawPayment = $_POST['payment_method'] ?? 'cash';
        $paymentMethod = ($rawPayment === 'cod' || $rawPayment === 'cash') ? 'cash' : $rawPayment;
        $trxId = trim($_POST['payment_trx_id'] ?? '') ?: null;

        // Delivery calculation
        $deliveryCalc = Setting::calculateDeliveryCharge(
            $subtotal,
            $customer['area_id'] ?? null,
            $customer['point_id'] ?? null,
            [
                'is_cod' => ($paymentMethod === 'cash'),
                'weight_kg' => $totalWeightKg
            ]
        );

        $deliveryCharge = (float)($deliveryCalc['final_charge'] ?? 0.00);
        $totalAmount = max(0, round($subtotal + $deliveryCharge, 2));

        $orderModel = new Order();
        $db = new \Core\Database(require __DIR__ . '/../../config/database.php');
        $pdo = $db->getConnection();

        try {
            $pdo->beginTransaction();

            $userModel = new User();
            $agentUser = $userModel->find($this->agentId);
            $agentName = $agentUser['name'] ?? 'Agent';

            $agentNote = "অর্ডারটি ফিল্ড এজেন্ট '{$agentName}' কর্তৃক গ্রহণ করা হয়েছে।";
            $customNote = trim($_POST['order_note'] ?? '');
            if ($customNote) {
                $agentNote .= " নোট: " . $customNote;
            }

            // 1. Insert into orders table with agent_id
            $stmt = $pdo->prepare("
                INSERT INTO orders (
                    customer_id, agent_id, area_id, zone_id, point_id,
                    status, total_amount, original_amount, delivery_charge, delivery_discount,
                    payment_method, payment_trx_id, rider_note, admin_note,
                    delivery_address, contact_number, created_at, updated_at
                ) VALUES (
                    :customer_id, :agent_id, :area_id, :zone_id, :point_id,
                    'pending', :total_amount, :original_amount, :delivery_charge, 0,
                    :payment_method, :payment_trx_id, :rider_note, :admin_note,
                    :delivery_address, :contact_number, NOW(), NOW()
                )
            ");

            $stmt->execute([
                'customer_id'       => $customerId,
                'agent_id'          => $this->agentId,
                'area_id'           => $customer['area_id'] ?? null,
                'zone_id'           => $customer['zone_id'] ?? null,
                'point_id'          => $customer['point_id'] ?? null,
                'total_amount'      => $totalAmount,
                'original_amount'   => $totalAmount,
                'delivery_charge'   => $deliveryCharge,
                'payment_method'    => $paymentMethod,
                'payment_trx_id'    => $trxId,
                'rider_note'        => $agentNote,
                'admin_note'        => "Agent Order #{$this->agentId} ({$agentName})",
                'delivery_address'  => $customer['address_details'] ?? '',
                'contact_number'    => $customer['phone'] ?? ''
            ]);

            $orderId = $pdo->lastInsertId();

            // 2. Insert Order Items & Deduct Stock
            $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, unit_title, base_qty, quantity, price) VALUES (?, ?, ?, ?, ?, ?)");
            $stmtStock = $pdo->prepare("UPDATE products SET stock_qty = stock_qty - ? WHERE id = ?");

            foreach ($cart as $item) {
                $pid = (int)$item['id'];
                $qty = (int)$item['quantity'];
                $price = (float)$item['price'];
                $unit = $item['unit'] ?? null;
                $baseQty = (float)($item['base_qty'] ?? 1.0);

                $stmtItem->execute([$orderId, $pid, $unit, $baseQty, $qty, $price]);
                $stmtStock->execute([$qty * $baseQty, $pid]);
            }

            $pdo->commit();

            // 3. Add Tracking Log
            try {
                $orderModel->addTrackingLog($orderId, 'pending', "অর্ডারটি ফিল্ড এজেন্ট {$agentName} কর্তৃক কাস্টমার {$customer['name']} এর জন্য নিশ্চিত করা হয়েছে।");
            } catch (\Throwable $e) {}

            // Clear Agent Cart
            unset($_SESSION['agent_cart']);

            $_SESSION['success'] = "অর্ডার সফলভাবে তৈরি হয়েছে! অর্ডার নং #{$orderId}";
            header("Location: {$base}/agent/orders/success?id={$orderId}");
            exit;

        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $_SESSION['error'] = "অর্ডার তৈরিতে সমস্যা হয়েছে: " . $e->getMessage();
            header("Location: {$base}/agent/checkout");
            exit;
        }
    }

    /**
     * Order Placement Success Page
     */
    public function success() {
        $orderId = (int)($_GET['id'] ?? 0);
        $orderModel = new Order();
        $order = $orderId ? $orderModel->find($orderId) : null;

        return $this->view('agent/success', [
            'title' => 'অর্ডার কনফার্মেশন',
            'order' => $order
        ], 'layouts/agent');
    }

    /**
     * Orders List for Agent
     */
    public function orders() {
        $status = $_GET['status'] ?? 'all';
        $search = trim($_GET['search'] ?? '');
        $date = trim($_GET['date'] ?? '');

        $orderModel = new Order();
        $orders = $orderModel->getByAgent($this->agentId, [
            'status' => $status,
            'search' => $search,
            'date'   => $date
        ]);

        $stats = $orderModel->getAgentStats($this->agentId);

        return $this->view('agent/orders', [
            'title' => 'আমার অর্ডার তালিকা',
            'orders' => $orders,
            'activeStatus' => $status,
            'search' => $search,
            'date' => $date,
            'stats' => $stats
        ], 'layouts/agent');
    }

    /**
     * Order Details / Invoice for Agent
     */
    public function showOrder() {
        $orderId = (int)($_GET['id'] ?? 0);
        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';

        if (!$orderId) {
            header("Location: {$base}/agent/orders");
            exit;
        }

        $orderModel = new Order();
        $order = $orderModel->find($orderId);

        if (!$order) {
            $_SESSION['error'] = 'অর্ডার পাওয়া যায়নি';
            header("Location: {$base}/agent/orders");
            exit;
        }

        // Ensure this order belongs to logged-in agent (or admin)
        if ($order['agent_id'] != $this->agentId && ($_SESSION['role'] ?? '') !== 'admin') {
            $_SESSION['error'] = 'এই অর্ডারে আপনার প্রবেশাধিকার নেই';
            header("Location: {$base}/agent/orders");
            exit;
        }

        $trackingHistory = $orderModel->getTrackingHistory($orderId);

        return $this->view('agent/order_detail', [
            'title' => 'অর্ডার বিবরণ #' . $orderId,
            'order' => $order,
            'trackingHistory' => $trackingHistory
        ], 'layouts/agent');
    }

    /**
     * Agent Customer List
     */
    public function customers() {
        $customerModel = new Customer();
        $assignedAreaIds = $this->allocationModel->getAreaIdsByAgent($this->agentId);
        $search = trim($_GET['search'] ?? '');

        $filters = [];
        if ($search) $filters['search'] = $search;
        if (!empty($assignedAreaIds)) {
            $filters['area_ids'] = $assignedAreaIds;
        }

        $customers = $customerModel->all($filters);
        $assignedAreas = $this->allocationModel->getByAgent($this->agentId);

        return $this->view('agent/customers', [
            'title' => 'গ্রাহক তালিকা',
            'customers' => $customers,
            'assignedAreas' => $assignedAreas,
            'search' => $search
        ], 'layouts/agent');
    }

    /**
     * Agent Profile View
     */
    public function profile() {
        $userModel = new User();
        $agent = $userModel->find($this->agentId);
        $assignedAreas = $this->allocationModel->getByAgent($this->agentId);
        $orderModel = new Order();
        $stats = $orderModel->getAgentStats($this->agentId);

        return $this->view('agent/profile', [
            'title' => 'এজেন্ট প্রোফাইল',
            'agent' => $agent,
            'assignedAreas' => $assignedAreas,
            'stats' => $stats,
            'success' => $_SESSION['success'] ?? null,
            'error' => $_SESSION['error'] ?? null
        ], 'layouts/agent');
    }

    /**
     * Agent Change Password
     */
    public function changePassword() {
        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($currentPassword) || empty($newPassword) || strlen($newPassword) < 4) {
            $_SESSION['error'] = 'নতুন পাসওয়ার্ড ন্যূনতম ৪ অক্ষরের হতে হবে';
            header("Location: {$base}/agent/profile");
            exit;
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = 'নতুন পাসওয়ার্ড ও নিশ্চিতকরণ মিলছে না';
            header("Location: {$base}/agent/profile");
            exit;
        }

        $userModel = new User();
        $agent = $userModel->find($this->agentId);

        if (!$agent || !password_verify($currentPassword, $agent['password'])) {
            $_SESSION['error'] = 'বর্তমান পাসওয়ার্ড সঠিক নয়';
            header("Location: {$base}/agent/profile");
            exit;
        }

        $db = new \Core\Database(require __DIR__ . '/../../config/database.php');
        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
        $db->query("UPDATE users SET password = :p WHERE id = :id", [
            'p' => $hashed,
            'id' => $this->agentId
        ]);

        $_SESSION['success'] = 'পাসওয়ার্ড সফলভাবে পরিবর্তন করা হয়েছে';
        header("Location: {$base}/agent/profile");
        exit;
    }
}
