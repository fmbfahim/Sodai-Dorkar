<?php

namespace Controllers;

use Core\Controller;
use Core\Middleware;
use Models\Vendor;

class AccountController extends Controller {

    public function __construct() {
        Middleware::auth(['admin']);
    }

    public function index() {
        $vendorModel = new Vendor();
        $transactions = $vendorModel->getAllTransactions();
        
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($transactions as $t) {
            if ($t['type'] === 'purchase' || $t['type'] === 'opening_balance') {
                $totalDebit += $t['amount'];
            } elseif ($t['type'] === 'payment') {
                $totalCredit += $t['amount'];
            }
        }
        
        return $this->view('admin/accounts/index', [
            'title' => 'Accounts & Ledger',
            'transactions' => $transactions,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit
        ]);
    }
}
