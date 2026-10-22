<?php

namespace Controllers;

use Core\Database;
use Models\Order;

class CustomerDashboardController {
    protected $db;

    public function __construct() {
        $config = require __DIR__ . "/../../config/database.php";
        $this->db = new Database($config);
        $this->requireCustomerLogin();
    }

    private function requireCustomerLogin() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION["customer_id"])) {
            \Core\Auth::restoreSessionFromCookie();
        }
        if (empty($_SESSION["customer_id"])) {
            \Core\Auth::syncAgentAndCustomerSessions();
        }
        if (empty($_SESSION["customer_id"])) {
            $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
            header("Location: {$base}/checkout/auth");
            exit;
        }
    }

    private function getCustomer() {
        $id = $_SESSION["customer_id"];
        $stmt = $this->db->query("
            SELECT c.*, a.name as area_name, z.name as zone_name, p.name as point_name
            FROM customers c
            LEFT JOIN areas a ON c.area_id = a.id
            LEFT JOIN zones z ON c.zone_id = z.id
            LEFT JOIN points p ON c.point_id = p.id
            WHERE c.id = ?
        ", [$id]);
        return $stmt->fetch();
    }

    private function renderAccount($view, $data = []) {
        extract($data);
        if (!isset($customer)) $customer = $this->getCustomer();
        if (!isset($isAgent)) $isAgent = \Core\Auth::isAgent();
        if (!isset($agentData) && $isAgent) $agentData = \Core\Auth::getCurrentAgentDashboardData();
        ob_start();
        require __DIR__ . "/../../views/shop/account/" . $view . ".php";
        return ob_get_clean();
    }

    public function dashboard() {
        $customer = $this->getCustomer();
        $orderModel = new Order();
        $orders = $orderModel->getByCustomer($customer["id"]);

        $stats = [
            "total"       => count($orders),
            "active"      => count(array_filter($orders, function($o) { return in_array($o["status"], ["processing","confirmed","packaging","picking","dispatch","shipped","out_for_delivery"]); })),
            "delivered"   => count(array_filter($orders, function($o) { return $o["status"] === "delivered"; })),
            "cancelled"   => count(array_filter($orders, function($o) { return $o["status"] === "cancelled"; })),
            "total_spent" => array_sum(array_map(function($o) { return ($o["status"] !== "cancelled") ? floatval($o["total_amount"]) : 0; }, $orders)),
        ];
        $recentOrders = array_slice($orders, 0, 5);
        $agentData = \Core\Auth::getCurrentAgentDashboardData();

        return $this->renderAccount("dashboard", [
            "customer"     => $customer,
            "stats"        => $stats,
            "recentOrders" => $recentOrders,
            "isAgent"      => !empty($agentData),
            "agentData"    => $agentData,
            "title"        => "My Dashboard"
        ]);
    }

    public function orders() {
        $customer = $this->getCustomer();
        $orderModel = new Order();
        $allOrders = $orderModel->getByCustomer($customer["id"]);

        $statusFilter = $_GET["status"] ?? "all";
        if ($statusFilter !== "all") {
            $allOrders = array_filter($allOrders, function($o) use ($statusFilter) {
                return $o["status"] === $statusFilter;
            });
        }

        return $this->renderAccount("orders", [
            "customer"     => $customer,
            "orders"       => array_values($allOrders),
            "statusFilter" => $statusFilter,
            "title"        => "My Orders"
        ]);
    }

    public function orderDetail() {
        $customer = $this->getCustomer();
        $orderId = $_GET["id"] ?? null;

        if (!$orderId) {
            header("Location: /sodai-dorkar/public/account/orders");
            exit;
        }

        $orderModel = new Order();
        $order = $orderModel->find($orderId);

        // Security: ensure order belongs to this customer
        if (!$order || (int)$order["customer_id"] !== (int)$customer["id"]) {
            header("Location: /sodai-dorkar/public/account/orders");
            exit;
        }

        $trackingHistory = $orderModel->getTrackingHistory($orderId);

        $stmt = $this->db->query(
            "SELECT oi.*, p.name as product_name, p.image_path FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?",
            [$orderId]
        );
        $order["items"] = $stmt->fetchAll();

        return $this->renderAccount("order_detail", [
            "customer"        => $customer,
            "order"           => $order,
            "trackingHistory" => $trackingHistory,
            "title"           => "Order #" . $orderId
        ]);
    }

    public function profile() {
        $customer = $this->getCustomer();
        $areas = $this->db->query("SELECT * FROM areas ORDER BY name ASC")->fetchAll();
        $zones = $customer["area_id"]
            ? $this->db->query("SELECT * FROM zones WHERE area_id = ?", [$customer["area_id"]])->fetchAll()
            : [];
        $points = $customer["zone_id"]
            ? $this->db->query("SELECT * FROM points WHERE zone_id = ?", [$customer["zone_id"]])->fetchAll()
            : [];

        return $this->renderAccount("profile", [
            "customer" => $customer,
            "areas"    => $areas,
            "zones"    => $zones,
            "points"   => $points,
            "success"  => $_GET["success"] ?? null,
            "error"    => $_GET["error"] ?? null,
            "title"    => "Edit Profile"
        ]);
    }

    public function updateProfile() {
        $customerId = $_SESSION["customer_id"];
        $name           = trim($_POST["name"] ?? "");
        $email          = trim($_POST["email"] ?? "");
        $areaId         = $_POST["area_id"] ?? null;
        $zoneId         = $_POST["zone_id"] ?? null;
        $pointId        = $_POST["point_id"] ?? null;
        $addressDetails = trim($_POST["address_details"] ?? "");

        if (empty($name)) {
            header("Location: /sodai-dorkar/public/account/profile?error=name_required");
            exit;
        }

        $this->db->query(
            "UPDATE customers SET name = ?, email = ?, area_id = ?, zone_id = ?, point_id = ?, address_details = ? WHERE id = ?",
            [$name, $email ?: null, $areaId ?: null, $zoneId ?: null, $pointId ?: null, $addressDetails, $customerId]
        );

        $_SESSION["customer_name"] = $name;
        header("Location: /sodai-dorkar/public/account/profile?success=1");
        exit;
    }

    public function changePassword() {
        $customer = $this->getCustomer();

        // Fetch settings to pass OTP state
        $stmt = $this->db->query("SELECT key_name, value FROM settings WHERE key_name IN ('otp_required_password_change')");
        $settings = [];
        foreach ($stmt->fetchAll() as $s) $settings[$s['key_name']] = $s['value'];

        $otpRequired = ($settings['otp_required_password_change'] ?? '0') === '1';
        $otpVerified = !empty($_SESSION['otp_pwd_change_verified']);

        return $this->renderAccount("change_password", [
            "customer"    => $customer,
            "success"     => $_GET["success"] ?? null,
            "error"       => $_GET["error"] ?? null,
            "title"       => "Change Password",
            "otpRequired" => $otpRequired,
            "otpVerified" => $otpVerified,
        ]);
    }

    public function updatePassword() {
        $customerId      = $_SESSION["customer_id"];
        $currentPassword = $_POST["current_password"] ?? "";
        $newPassword     = $_POST["new_password"] ?? "";
        $confirmPassword = $_POST["confirm_password"] ?? "";

        // Check OTP requirement
        $stmt = $this->db->query("SELECT value FROM settings WHERE key_name = 'otp_required_password_change'");
        $row  = $stmt->fetch();
        $otpRequired = ($row['value'] ?? '0') === '1';

        if ($otpRequired && empty($_SESSION['otp_pwd_change_verified'])) {
            header("Location: /sodai-dorkar/public/account/change-password?error=otp_required");
            exit;
        }

        $stmt = $this->db->query("SELECT password FROM customers WHERE id = ?", [$customerId]);
        $row  = $stmt->fetch();

        if (!$row || !password_verify($currentPassword, $row["password"])) {
            header("Location: /sodai-dorkar/public/account/change-password?error=wrong_current");
            exit;
        }

        if (strlen($newPassword) < 6) {
            header("Location: /sodai-dorkar/public/account/change-password?error=too_short");
            exit;
        }

        if ($newPassword !== $confirmPassword) {
            header("Location: /sodai-dorkar/public/account/change-password?error=mismatch");
            exit;
        }

        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->db->query("UPDATE customers SET password = ? WHERE id = ?", [$hashed, $customerId]);

        // Clear OTP verification after successful change
        unset($_SESSION['otp_pwd_change_verified']);

        header("Location: /sodai-dorkar/public/account/change-password?success=1");
        exit;
    }

    // ─────────────────────────────────────────────────────────────
    // EMBEDDED FIELD AGENT OPERATIONS & VIEWS
    // ─────────────────────────────────────────────────────────────

    private function requireAgentAccess(): array {
        $this->requireCustomerLogin();
        \Core\Auth::syncAgentAndCustomerSessions();
        if (!\Core\Auth::isAgent()) {
            $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
            header("Location: {$base}/account");
            exit;
        }
        $agent = \Core\Auth::getAgentForCurrentCustomer();
        if (!$agent) {
            $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
            header("Location: {$base}/account");
            exit;
        }
        return $agent;
    }

    public function agentDashboard() {
        $agent = $this->requireAgentAccess();
        $agentId = (int)$agent['id'];
        $customer = $this->getCustomer();

        $orderModel = new Order();
        $allocationModel = new \Models\AgentAllocation();

        $assignedAreas = $allocationModel->getByAgent($agentId);
        $stats = $orderModel->getAgentStats($agentId);
        $recentOrders = $orderModel->getByAgent($agentId, ['limit' => 10]);

        return $this->renderAccount("agent_dashboard", [
            "customer"      => $customer,
            "agent"         => $agent,
            "assignedAreas" => $assignedAreas,
            "stats"         => $stats,
            "recentOrders"  => $recentOrders,
            "isAgent"       => true,
            "inAccount"     => true,
            "title"         => "ফিল্ড এজেন্ট ড্যাশবোর্ড"
        ]);
    }

    public function agentShop() {
        $agent = $this->requireAgentAccess();
        $agentId = (int)$agent['id'];
        $customer = $this->getCustomer();

        $categoryModel = new \Models\Category();
        $productModel = new \Models\Product();
        $allocationModel = new \Models\AgentAllocation();

        $search = trim($_GET['search'] ?? '');
        $catId = !empty($_GET['category_id']) ? (int)$_GET['category_id'] : null;

        $categories = $categoryModel->all();
        $filters = ['availability_status' => ['in_stock', 'available']];
        if ($search) $filters['search'] = $search;
        if ($catId) $filters['category_id'] = $catId;

        $products = $productModel->all($filters);

        $cart = $_SESSION['agent_cart'] ?? [];
        $cartCount = 0;
        $cartTotal = 0;
        foreach ($cart as $item) {
            $cartCount += (int)($item['quantity'] ?? 1);
            $cartTotal += (float)($item['price'] ?? 0) * (int)($item['quantity'] ?? 1);
        }

        $assignedAreas = $allocationModel->getByAgent($agentId);

        return $this->renderAccount("agent_shop", [
            "customer"      => $customer,
            "agent"         => $agent,
            "categories"    => $categories,
            "products"      => $products,
            "activeCatId"   => $catId,
            "search"        => $search,
            "cartCount"     => $cartCount,
            "cartTotal"     => $cartTotal,
            "assignedAreas" => $assignedAreas,
            "isAgent"       => true,
            "inAccount"     => true,
            "title"         => "পণ্য ক্যাটালগ ও অর্ডার নিন"
        ]);
    }

    public function agentCheckout() {
        $agent = $this->requireAgentAccess();
        $agentId = (int)$agent['id'];
        $customer = $this->getCustomer();
        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';

        $cart = $_SESSION['agent_cart'] ?? [];
        if (empty($cart)) {
            header("Location: {$base}/account/agent/shop");
            exit;
        }

        $allocationModel = new \Models\AgentAllocation();
        $areaModel = new \Models\Area();

        $assignedAreas = $allocationModel->getByAgent($agentId);
        $availableAreas = !empty($assignedAreas) ? $assignedAreas : $areaModel->all();

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += (float)$item['price'] * (int)$item['quantity'];
        }

        return $this->renderAccount("agent_checkout", [
            "customer"       => $customer,
            "agent"          => $agent,
            "cart"           => $cart,
            "subtotal"       => $subtotal,
            "assignedAreas"  => $assignedAreas,
            "availableAreas" => $availableAreas,
            "agentId"        => $agentId,
            "isAgent"        => true,
            "inAccount"      => true,
            "title"          => "কাস্টমার নির্বাচন ও চেকআউট"
        ]);
    }

    public function agentOrders() {
        $agent = $this->requireAgentAccess();
        $agentId = (int)$agent['id'];
        $customer = $this->getCustomer();

        $status = $_GET['status'] ?? 'all';
        $search = trim($_GET['search'] ?? '');
        $date = trim($_GET['date'] ?? '');

        $orderModel = new Order();
        $orders = $orderModel->getByAgent($agentId, [
            'status' => $status,
            'search' => $search,
            'date'   => $date
        ]);

        $stats = $orderModel->getAgentStats($agentId);

        return $this->renderAccount("agent_orders", [
            "customer"     => $customer,
            "agent"        => $agent,
            "orders"       => $orders,
            "activeStatus" => $status,
            "search"       => $search,
            "date"         => $date,
            "stats"        => $stats,
            "isAgent"      => true,
            "inAccount"    => true,
            "title"        => "সংগৃহীত এজেন্ট অর্ডারসমূহ"
        ]);
    }

    public function agentOrderDetail() {
        $agent = $this->requireAgentAccess();
        $agentId = (int)$agent['id'];
        $customer = $this->getCustomer();
        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';

        $orderId = (int)($_GET['id'] ?? 0);
        if (!$orderId) {
            header("Location: {$base}/account/agent/orders");
            exit;
        }

        $orderModel = new Order();
        $order = $orderModel->find($orderId);

        if (!$order || ($order['agent_id'] != $agentId && ($_SESSION['role'] ?? '') !== 'admin')) {
            header("Location: {$base}/account/agent/orders");
            exit;
        }

        $trackingHistory = $orderModel->getTrackingHistory($orderId);

        return $this->renderAccount("agent_order_detail", [
            "customer"        => $customer,
            "agent"           => $agent,
            "order"           => $order,
            "trackingHistory" => $trackingHistory,
            "isAgent"         => true,
            "inAccount"       => true,
            "title"           => "অর্ডার বিবরণ #" . $orderId
        ]);
    }

    public function agentCustomers() {
        $agent = $this->requireAgentAccess();
        $agentId = (int)$agent['id'];
        $customer = $this->getCustomer();

        $customerModel = new \Models\Customer();
        $allocationModel = new \Models\AgentAllocation();

        $assignedAreaIds = $allocationModel->getAreaIdsByAgent($agentId);
        $search = trim($_GET['search'] ?? '');

        $filters = [];
        if ($search) $filters['search'] = $search;
        if (!empty($assignedAreaIds)) {
            $filters['area_ids'] = $assignedAreaIds;
        }

        $customers = $customerModel->all($filters);
        $assignedAreas = $allocationModel->getByAgent($agentId);

        return $this->renderAccount("agent_customers", [
            "customer"      => $customer,
            "agent"         => $agent,
            "customers"     => $customers,
            "assignedAreas" => $assignedAreas,
            "search"        => $search,
            "isAgent"       => true,
            "inAccount"     => true,
            "title"         => "এলাকার কাস্টমার তালিকা"
        ]);
    }
}
