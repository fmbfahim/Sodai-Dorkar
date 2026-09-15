<?php

namespace Controllers;

use Core\Controller;
use Core\Middleware;
use Core\Database;

class AdminController extends Controller {
    
    public function __construct() {
        Middleware::permission('dashboard');
    }

    public function dashboard() {
        $config = require __DIR__ . '/../../config/database.php';
        $db = new Database($config);
        $pdo = $db->getConnection();

        // 1. Timeframe Definitions
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $thisMonth = date('Y-m');
        $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));

        // Today's stats
        $stmt = $pdo->query("SELECT 
            COUNT(*) as orders_count, 
            COALESCE(SUM(CASE WHEN status != 'cancelled' THEN total_amount ELSE 0 END), 0) as sales_amount 
            FROM orders 
            WHERE DATE(created_at) = '{$today}'");
        $todayData = $stmt->fetch();

        // Yesterday's stats
        $stmt = $pdo->query("SELECT 
            COUNT(*) as orders_count, 
            COALESCE(SUM(CASE WHEN status != 'cancelled' THEN total_amount ELSE 0 END), 0) as sales_amount 
            FROM orders 
            WHERE DATE(created_at) = '{$yesterday}'");
        $yesterdayData = $stmt->fetch();

        // Last 7 Days (Week) stats
        $stmt = $pdo->query("SELECT 
            COUNT(*) as orders_count, 
            COALESCE(SUM(CASE WHEN status != 'cancelled' THEN total_amount ELSE 0 END), 0) as sales_amount 
            FROM orders 
            WHERE DATE(created_at) >= '{$sevenDaysAgo}'");
        $weekData = $stmt->fetch();

        // This Month's stats
        $stmt = $pdo->query("SELECT 
            COUNT(*) as orders_count, 
            COALESCE(SUM(CASE WHEN status != 'cancelled' THEN total_amount ELSE 0 END), 0) as sales_amount 
            FROM orders 
            WHERE DATE_FORMAT(created_at, '%Y-%m') = '{$thisMonth}'");
        $monthData = $stmt->fetch();

        // All Time stats
        $stmt = $pdo->query("SELECT 
            COUNT(*) as orders_count, 
            COALESCE(SUM(CASE WHEN status = 'delivered' THEN total_amount ELSE 0 END), 0) as sales_amount 
            FROM orders");
        $allTimeData = $stmt->fetch();

        // Average Order Value (AOV)
        $totalOrdersCount = (int)($allTimeData['orders_count'] ?? 0);
        $totalSalesSum = (float)($allTimeData['sales_amount'] ?? 0);
        $aov = $totalOrdersCount > 0 ? round($totalSalesSum / $totalOrdersCount, 2) : 0;

        // 2. Orders Status Breakdown (Funnel)
        $statusCounts = [
            'pending' => 0,
            'processing' => 0,
            'packed' => 0,
            'shipped' => 0,
            'out_for_delivery' => 0,
            'delivered' => 0,
            'cancelled' => 0,
            'returned' => 0
        ];
        $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM orders GROUP BY status");
        while ($row = $stmt->fetch()) {
            $st = strtolower(trim($row['status'] ?? ''));
            if (isset($statusCounts[$st])) {
                $statusCounts[$st] = (int)$row['count'];
            }
        }

        // 3. Customers & Products
        $totalCustomers = (int)$pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn();
        $newCustomersToday = (int)$pdo->query("SELECT COUNT(*) FROM customers WHERE DATE(created_at) = '{$today}'")->fetchColumn();
        $totalProducts = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
        $lowStockCount = (int)$pdo->query("SELECT COUNT(*) FROM products WHERE stock_qty <= 5")->fetchColumn();
        $outOfStockCount = (int)$pdo->query("SELECT COUNT(*) FROM products WHERE stock_qty = 0")->fetchColumn();

        // 4. Delivery & Riders
        $ridersCount = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'delivery_man' AND status = 'active'")->fetchColumn();
        $deliveredToday = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'delivered' AND DATE(updated_at) = '{$today}'")->fetchColumn();

        // 5. HR & Workforce Integration
        $hrStats = [
            'active_employees' => 0,
            'present_today' => 0,
            'pending_leaves' => 0,
            'payroll_due' => 0
        ];
        try {
            $hrStats['active_employees'] = (int)$pdo->query("SELECT COUNT(*) FROM employees WHERE status = 'active'")->fetchColumn();
            $hrStats['present_today'] = (int)$pdo->query("SELECT COUNT(*) FROM attendances WHERE date = '{$today}' AND status IN ('present', 'late', 'half_day')")->fetchColumn();
            $hrStats['pending_leaves'] = (int)$pdo->query("SELECT COUNT(*) FROM leave_requests WHERE status = 'pending'")->fetchColumn();
            $hrStats['payroll_due'] = (float)$pdo->query("SELECT COALESCE(SUM(net_salary), 0) FROM payrolls WHERE salary_month = '{$thisMonth}' AND status != 'paid'")->fetchColumn();
        } catch (\Exception $e) {
            // Gracefully handle if tables are not initialized
        }

        // 6. Last 7 Days Sales Trend for Chart.js
        $chartLabels = [];
        $chartSales = [];
        $chartOrders = [];

        for ($i = 6; $i >= 0; $i--) {
            $dayDate = date('Y-m-d', strtotime("-{$i} days"));
            $chartLabels[] = date('d M', strtotime($dayDate));

            $stmt = $pdo->query("SELECT 
                COUNT(*) as count, 
                COALESCE(SUM(CASE WHEN status != 'cancelled' THEN total_amount ELSE 0 END), 0) as total 
                FROM orders WHERE DATE(created_at) = '{$dayDate}'");
            $dayRes = $stmt->fetch();
            $chartOrders[] = (int)($dayRes['count'] ?? 0);
            $chartSales[] = (float)($dayRes['total'] ?? 0);
        }

        // 7. Top Selling Products
        $topProducts = [];
        try {
            $topProducts = $pdo->query("
                SELECT p.id, p.name, p.sell_price, p.stock_qty, p.image_path,
                       COALESCE(SUM(oi.quantity), 0) as units_sold,
                       COALESCE(SUM(oi.quantity * oi.price), 0) as total_revenue
                FROM products p
                LEFT JOIN order_items oi ON p.id = oi.product_id
                GROUP BY p.id
                ORDER BY units_sold DESC, p.id DESC
                LIMIT 5
            ")->fetchAll();
        } catch (\Exception $e) {
            $topProducts = $pdo->query("SELECT id, name, sell_price, stock_qty, image_path, 0 as units_sold, 0 as total_revenue FROM products ORDER BY id DESC LIMIT 5")->fetchAll();
        }

        // 8. Critical Low Stock Alerts (Stock <= 5)
        $lowStockItems = $pdo->query("
            SELECT id, name, sku, stock_qty, sell_price
            FROM products 
            WHERE stock_qty <= 5
            ORDER BY stock_qty ASC 
            LIMIT 5
        ")->fetchAll();

        // 9. Recent 10 Orders with Customer & Area
        $recentOrders = $pdo->query("
            SELECT o.*, c.name as customer_name, c.phone as customer_phone, a.name as area_name,
                   (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as items_count
            FROM orders o 
            LEFT JOIN customers c ON o.customer_id = c.id 
            LEFT JOIN areas a ON o.area_id = a.id
            ORDER BY o.created_at DESC 
            LIMIT 10
        ")->fetchAll();

        return $this->view('admin/dashboard', [
            'title' => 'Executive Dashboard - Operations & Business Analytics',
            'todayData' => $todayData,
            'yesterdayData' => $yesterdayData,
            'weekData' => $weekData,
            'monthData' => $monthData,
            'allTimeData' => $allTimeData,
            'aov' => $aov,
            'statusCounts' => $statusCounts,
            'totalCustomers' => $totalCustomers,
            'newCustomersToday' => $newCustomersToday,
            'totalProducts' => $totalProducts,
            'lowStockCount' => $lowStockCount,
            'outOfStockCount' => $outOfStockCount,
            'ridersCount' => $ridersCount,
            'deliveredToday' => $deliveredToday,
            'hrStats' => $hrStats,
            'chartLabels' => $chartLabels,
            'chartSales' => $chartSales,
            'chartOrders' => $chartOrders,
            'topProducts' => $topProducts,
            'lowStockItems' => $lowStockItems,
            'recentOrders' => $recentOrders
        ]);
    }
}
