<?php

namespace Controllers;

use Core\Controller;
use Core\Middleware;
use Models\Order;
use Models\Customer;

class AdminController extends Controller {
    
    public function __construct() {
        Middleware::auth(['admin']);
    }

    public function dashboard() {
        $db = new \Core\Database(require __DIR__ . '/../../config/database.php');
        $pdo = $db->getConnection();

        // 1. Totals
        $stats = [
            'orders_today' => 0,
            'sales_today' => 0,
            'pending_orders' => 0,
            'customers_total' => 0
        ];

        // Orders Today
        $stmt = $pdo->query("SELECT COUNT(*) as count, SUM(total_amount) as total FROM orders WHERE DATE(created_at) = CURDATE()");
        $res = $stmt->fetch();
        $stats['orders_today'] = $res['count'];
        $stats['sales_today'] = $res['total'] ?? 0;

        // Pending
        $stmt = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'");
        $stats['pending_orders'] = $stmt->fetchColumn();

        // Customers
        $stmt = $pdo->query("SELECT COUNT(*) FROM customers");
        $stats['customers_total'] = $stmt->fetchColumn();

        // Recent Orders
        $stmt = $pdo->query("SELECT o.*, c.name as customer_name FROM orders o LEFT JOIN customers c ON o.customer_id = c.id ORDER BY o.created_at DESC LIMIT 5");
        $recentOrders = $stmt->fetchAll();

        return $this->view('admin/dashboard', [
            'title' => 'Admin Dashboard',
            'stats' => $stats,
            'recentOrders' => $recentOrders
        ]);
    }
}
