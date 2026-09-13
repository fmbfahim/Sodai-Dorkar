<?php

namespace Models;

use Core\Database;

class Vendor {
    protected $db;

    public function __construct() {
        $config = require __DIR__ . '/../../config/database.php';
        $this->db = new Database($config);
    }

    public function all() {
        $stmt = $this->db->query("SELECT * FROM vendors ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    public function create($data) {
        $this->db->query("INSERT INTO vendors (name, contact, address) VALUES (:name, :contact, :address)", [
            'name' => $data['name'],
            'contact' => $data['contact'],
            'address' => $data['address']
        ]);
        return $this->db->lastInsertId();
    }

    public function delete($id) {
        $this->db->query("DELETE FROM vendors WHERE id = :id", ['id' => $id]);
    }

    public function find($id) {
        $stmt = $this->db->query("SELECT * FROM vendors WHERE id = :id", ['id' => $id]);
        return $stmt->fetch();
    }

    public function getBalance($vendorId) {
        // Purchase = Bill (We owe), Payment = Paid (We reduced debt)
        // Balance = Total Purchases - Total Payments
        $sql = "SELECT 
                    SUM(CASE WHEN type = 'purchase' THEN amount ELSE 0 END) as total_bill,
                    SUM(CASE WHEN type = 'payment' THEN amount ELSE 0 END) as total_paid,
                    SUM(CASE WHEN type = 'opening_balance' THEN amount ELSE 0 END) as opening_balance
                FROM vendor_transactions 
                WHERE vendor_id = :id";
        $stmt = $this->db->query($sql, ['id' => $vendorId]);
        $res = $stmt->fetch();
        
        $bill = $res['total_bill'] ?? 0;
        $paid = $res['total_paid'] ?? 0;
        $opening = $res['opening_balance'] ?? 0;
        
        return [
            'bill' => $bill,
            'paid' => $paid,
            'opening' => $opening,
            'due' => ($bill + $opening) - $paid
        ];
    }

    public function getLedger($vendorId) {
        $sql = "SELECT * FROM vendor_transactions WHERE vendor_id = :id ORDER BY transaction_date DESC, id DESC";
        $stmt = $this->db->query($sql, ['id' => $vendorId]);
        return $stmt->fetchAll();
    }

    public function getAllTransactions() {
        $sql = "SELECT vt.*, v.name as vendor_name 
                FROM vendor_transactions vt
                JOIN vendors v ON vt.vendor_id = v.id
                ORDER BY vt.transaction_date DESC, vt.id DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function makePayment($data) {
        $this->db->query("INSERT INTO vendor_transactions (vendor_id, type, amount, transaction_date, description) 
                          VALUES (:vendor_id, 'payment', :amount, :date, :desc)", [
            'vendor_id' => $data['vendor_id'],
            'amount' => $data['amount'],
            'date' => $data['date'],
            'desc' => $data['note']
        ]);
    }
}
