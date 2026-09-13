<?php

namespace Controllers;

use Core\Controller;
use Core\Middleware;
use Models\Product;
use Models\Order;

class ReportController extends Controller {

    public function __construct() {
        Middleware::auth(['admin', 'manager']);
    }

    public function stock() {
        $productModel = new Product();
        // Assume we need a method to get low stock items
        // For now, let's fetch all and filter in PHP or add mode method later
        // Ideally: $products = $productModel->getLowStock(5);
        $products = $productModel->all(); 
        
        // Filter low stock (threshold < 5)
        $lowStock = array_filter($products, function($p) {
            return floatval($p['stock_qty'] ?? 0) < 5; // Alert threshold
        });

        return $this->view('admin/reports/stock', [
            'title' => 'Stock Alert Report',
            'products' => $lowStock
        ]);
    }

    public function sales() {
        $orderModel = new Order();
        
        $start = $_GET['start'] ?? date('Y-m-d', strtotime('-30 days'));
        $end = $_GET['end'] ?? date('Y-m-d');
        
        // We need a method to get sales by date range
        // Let's add getSalesReport($start, $end) to Order model
        $sales = $orderModel->getSalesByDate($start, $end);

        return $this->view('admin/reports/sales', [
            'title' => 'Sales Report',
            'sales' => $sales,
            'start' => $start,
            'end' => $end
        ]);
    }
}
