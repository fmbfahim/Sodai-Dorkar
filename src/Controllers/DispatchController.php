<?php

namespace Controllers;

use Core\Controller;
use Core\Middleware;
use Models\Order;
use Models\User;

class DispatchController extends Controller {

    public function __construct() {
        Middleware::permission('dispatch');
    }

    public function index() {
        $orderModel = new Order();
        
        // Fetch only orders relevant for dispatch (e.g., Confirmed, Processing)
        // For simple demo, fetching 'pending' or creating a new status flow is needed.
        // Let's assume we want to see ALL orders to manage their flow, or filters.
        // Ideally: Status = 'confirmed' or 'processing'
        // Since we only have 'pending' default, let's just fetch all for now and filter in view or DB
        
        // Let's fetch orders that are NOT delivered or cancelled
        $orders = $orderModel->getDispatchQueue(); 
        
        $userModel = new User();
        $deliveryMen = $userModel->getByRole('delivery_man');

        return $this->view('admin/dispatch/index', [
            'title' => 'Dispatch Board',
            'orders' => $orders,
            'deliveryMen' => $deliveryMen
        ]);
    }

    public function bulkAction() {
        $action = $_POST['action'] ?? null;
        $orderIds = $_POST['order_ids'] ?? [];

        if (empty($orderIds)) {
             header('Location: /sodai-dorkar/public/admin/dispatch?error=No orders selected');
             exit;
        }

        $orderModel = new Order();

        if ($action === 'print_invoices') {
            // Complex to allow bulk print, maybe redirect to a merged view?
            // For now, redirect to a bulk print page
            $ids = implode(',', $orderIds);
            header("Location: /sodai-dorkar/public/admin/dispatch/print-bulk?ids=$ids&type=invoice");
            exit;
        }
        
        if ($action === 'print_labels') {
            $ids = implode(',', $orderIds);
            header("Location: /sodai-dorkar/public/admin/dispatch/print-bulk?ids=$ids&type=label");
            exit;
        }

        if ($action === 'mark_shipped') {
            foreach ($orderIds as $id) {
                $orderModel->updateStatus($id, 'shipped');
            }
        }
        
        if ($action === 'assign_delivery') {
             $dmId = $_POST['delivery_man_id'] ?? null;
             if ($dmId) {
                 foreach ($orderIds as $id) {
                     $orderModel->assignDeliveryMan($id, $dmId);
                 }
             }
        }
        
        if ($action === 'auto_assign') {
             foreach ($orderIds as $id) {
                 $order = $orderModel->find($id);
                 if ($order && !empty($order['area_id'])) {
                     $orderModel->autoAssignOrder($id, $order['area_id']);
                 }
             }
        }

        header('Location: /sodai-dorkar/public/admin/dispatch?success=1');
        exit;
    }
}
