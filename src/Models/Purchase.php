<?php

namespace Models;

use Core\Database;
use PDO;

class Purchase {
    protected $db;

    public function __construct() {
        $config = require __DIR__ . '/../../config/database.php';
        $this->db = new Database($config);
    }

    public function all() {
        $stmt = $this->db->query("SELECT purchases.*, vendors.name as vendor_name 
                                  FROM purchases 
                                  JOIN vendors ON purchases.vendor_id = vendors.id 
                                  ORDER BY purchases.purchase_date DESC");
        return $stmt->fetchAll();
    }

    public function find($id) {
        $stmt = $this->db->query("SELECT purchases.*, vendors.name as vendor_name, vendors.contact as vendor_contact, vendors.address as vendor_address 
                                  FROM purchases 
                                  JOIN vendors ON purchases.vendor_id = vendors.id 
                                  WHERE purchases.id = :id", ['id' => $id]);
        $purchase = $stmt->fetch();
        
        if ($purchase) {
            $stmtItems = $this->db->query("SELECT purchase_items.*, products.name as product_name, products.sku 
                                           FROM purchase_items 
                                           JOIN products ON purchase_items.product_id = products.id 
                                           WHERE purchase_items.purchase_id = :id", ['id' => $id]);
            $purchase['items'] = $stmtItems->fetchAll();
        }
        return $purchase;
    }

    public function createTransaction($data, $items) {
        $pdo = $this->db->getConnection();
        
        try {
            $pdo->beginTransaction();

            // 1. Create Purchase Record
            $sql = "INSERT INTO purchases (vendor_id, invoice_no, purchase_date, total_amount, notes) 
                    VALUES (:vendor_id, :invoice_no, :purchase_date, :total_amount, :notes)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'vendor_id' => $data['vendor_id'],
                'invoice_no' => $data['invoice_no'],
                'purchase_date' => $data['purchase_date'],
                'total_amount' => $data['total_amount'],
                'notes' => $data['notes'] ?? null
            ]);
            $purchaseId = $pdo->lastInsertId();

            // 2. Insert Items & Update Product Stock/Price
            $sqlItem = "INSERT INTO purchase_items (purchase_id, product_id, quantity, unit_price, total_price) 
                        VALUES (:purchase_id, :product_id, :quantity, :unit_price, :total_price)";
            $stmtItem = $pdo->prepare($sqlItem);

            $sqlProduct = "UPDATE products SET stock_qty = stock_qty + :qty, buy_price = :buy_price WHERE id = :id";
            $stmtProduct = $pdo->prepare($sqlProduct);

            foreach ($items as $item) {
                // Insert Item
                $stmtItem->execute([
                    'purchase_id' => $purchaseId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price']
                ]);

                // Update Product
                $stmtProduct->execute([
                    'qty' => $item['quantity'],
                    'buy_price' => $item['unit_price'],
                    'id' => $item['product_id']
                ]);
            }

            // 3. Add to Vendor Ledger (Transaction)
            $sqlTrans = "INSERT INTO vendor_transactions (vendor_id, type, amount, ref_id, transaction_date, description) 
                         VALUES (:vendor_id, 'purchase', :amount, :ref_id, :date, :desc)";
            $pdo->prepare($sqlTrans)->execute([
                'vendor_id' => $data['vendor_id'],
                'amount' => $data['total_amount'], // Bill amount
                'ref_id' => $purchaseId,
                'date' => $data['purchase_date'],
                'desc' => 'Purchase Invoice #' . $data['invoice_no']
            ]);

            $pdo->commit();
            return $purchaseId;

        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
