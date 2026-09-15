<?php

namespace Controllers;

use Core\Controller;
use Core\Middleware;
use Models\Vendor;

class VendorController extends Controller {

    public function __construct() {
        Middleware::permission('vendors_purchases');
    }

    public function index() {
        $vendorModel = new Vendor();
        $vendors = $vendorModel->all();
        
        return $this->view('admin/vendors/index', [
            'title' => 'Vendors', 
            'vendors' => $vendors
        ]);
    }

    public function store() {
        $name = trim($_POST['name'] ?? '');
        $contact = trim($_POST['contact'] ?? '');
        $address = trim($_POST['address'] ?? '');

        if ($name) {
            $vendorModel = new Vendor();
            $vendorModel->create([
                'name' => $name, 
                'contact' => $contact,
                'address' => $address
            ]);
        }
        
        header('Location: /sodai-dorkar/public/admin/vendors');
        exit;
    }

    public function destroy() {
        $id = $_POST['id'] ?? null;
        if ($id) {
            try {
                $vendorModel = new Vendor();
                $vendorModel->delete($id);
            } catch (\PDOException $e) {
                // If it fails due to foreign key constraint (products associated), redirect with an error logic
                // For now, fail gracefully and redirect back. We can enhance error reporting later.
                header('Location: /sodai-dorkar/public/admin/vendors?error=cannot_delete');
                exit;
            }
        }
        header('Location: /sodai-dorkar/public/admin/vendors');
        exit;
    }

    public function ledger() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /sodai-dorkar/public/admin/vendors');
            exit;
        }
        
        $vendorModel = new Vendor();
        $vendor = $vendorModel->find($id); 
        $ledger = $vendorModel->getLedger($id);
        $balance = $vendorModel->getBalance($id);

        return $this->view('admin/vendors/ledger', [
            'title' => 'Vendor Ledger',
            'vendor' => $vendor,
            'ledger' => $ledger,
            'balance' => $balance
        ]);
    }

    public function payment() {
        $vendor_id = $_POST['vendor_id'];
        $amount = $_POST['amount'];
        $date = $_POST['date'];
        $method = $_POST['method'] ?? 'Cash';
        $note = $_POST['note'] ?? '';
        
        $description = "[$method] $note";
        
        if ($vendor_id && $amount) {
            $vendorModel = new Vendor();
            $vendorModel->makePayment([
                'vendor_id' => $vendor_id,
                'amount' => $amount,
                'date' => $date,
                'note' => $description
            ]);
        }
        
        header("Location: /sodai-dorkar/public/admin/vendors/ledger?id=$vendor_id");
        exit;
    }
}
