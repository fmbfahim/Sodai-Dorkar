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
        header('Content-Type: application/json; charset=utf-8');
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input || empty($input['items'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false, 
                'error' => 'সঠিক তথ্য পাওয়া যায়নি বা কোনো পণ্য নির্বাচন করা হয়নি',
                'message' => 'সঠিক তথ্য পাওয়া যায়নি বা কোনো পণ্য নির্বাচন করা হয়নি'
            ]);
            exit;
        }

        if (empty($input['vendor_id'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false, 
                'error' => 'ভেন্ডর বা সরবরাহকারী নির্বাচন করুন',
                'message' => 'ভেন্ডর বা সরবরাহকারী নির্বাচন করুন'
            ]);
            exit;
        }

        if (empty($input['invoice_no'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false, 
                'error' => 'ইনভয়েস নম্বর প্রদান করুন',
                'message' => 'ইনভয়েস নম্বর প্রদান করুন'
            ]);
            exit;
        }

        try {
            $purchaseModel = new Purchase();
            $id = $purchaseModel->createTransaction([
                'vendor_id' => $input['vendor_id'],
                'invoice_no' => $input['invoice_no'],
                'purchase_date' => $input['purchase_date'] ?? date('Y-m-d'),
                'total_amount' => $input['total_amount'] ?? 0,
                'notes' => $input['notes'] ?? null
            ], $input['items']);

            $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
            echo json_encode([
                'success' => true, 
                'message' => 'ক্রয় চালান সফলভাবে সংরক্ষণ করা হয়েছে',
                'redirect' => $base . '/admin/purchases/show?id=' . $id
            ]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false, 
                'error' => $e->getMessage(),
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    public function show() {
        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: {$base}/admin/purchases");
            exit;
        }

        $purchaseModel = new Purchase();
        $purchase = $purchaseModel->find($id);

        if (!$purchase) {
             header("Location: {$base}/admin/purchases");
             exit;
        }

        return $this->view('admin/purchases/show', [
            'title' => 'Purchase Invoice #' . $purchase['invoice_no'],
            'purchase' => $purchase
        ]);
    }
}
