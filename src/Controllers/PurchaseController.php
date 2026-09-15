<?php

namespace Controllers;

use Core\Controller;
use Core\Middleware;
use Models\Purchase;
use Models\Vendor;
use Models\Product;

class PurchaseController extends Controller {

    public function __construct() {
        Middleware::permission('vendors_purchases');
    }

    public function index() {
        $purchaseModel = new Purchase();
        $purchases = $purchaseModel->all();
        
        return $this->view('admin/purchases/index', [
            'title' => 'Purchase History', 
            'purchases' => $purchases
        ]);
    }

    public function create() {
        $vendorModel = new Vendor();
        $productModel = new Product();
        
        $vendors = $vendorModel->all();
        $products = $productModel->all();

        return $this->view('admin/purchases/create', [
            'title' => 'Stock In / Purchase',
            'vendors' => $vendors,
            'products' => $products
        ]);
    }

    public function store() {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid Data']);
            exit;
        }

        try {
            $purchaseModel = new Purchase();
            $id = $purchaseModel->createTransaction([
                'vendor_id' => $input['vendor_id'],
                'invoice_no' => $input['invoice_no'],
                'purchase_date' => $input['purchase_date'],
                'total_amount' => $input['total_amount'],
                'notes' => $input['notes'] ?? null
            ], $input['items']);

            echo json_encode(['success' => true, 'redirect' => '/sodai-dorkar/public/admin/purchases/show?id=' . $id]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    public function show() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /sodai-dorkar/public/admin/purchases');
            exit;
        }

        $purchaseModel = new Purchase();
        $purchase = $purchaseModel->find($id);

        if (!$purchase) {
             header('Location: /sodai-dorkar/public/admin/purchases');
             exit;
        }

        return $this->view('admin/purchases/show', [
            'title' => 'Purchase Invoice #' . $purchase['invoice_no'],
            'purchase' => $purchase
        ]);
    }
}
