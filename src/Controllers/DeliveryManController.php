<?php

namespace Controllers;

use Core\Controller;
use Core\Database;
use Models\DmAllocation;
use Models\User;
use Models\Area;

class DeliveryManController extends Controller {
    protected $db;

    public function __construct() {
        $config = require __DIR__ . '/../../config/database.php';
        $this->db = new Database($config);
    }
    
    public function index() {
        $orderModel = new \Models\Order();
        $summary = $orderModel->getDeliveryMenSummaryList();
        
        return $this->view('admin/delivery-men/index', [
            'title' => 'Delivery Men & Performance Reports',
            'deliveryMen' => $summary['riders'],
            'kpis' => $summary['kpis']
        ]);
    }

    public function report() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /sodai-dorkar/public/admin/delivery-men');
            exit;
        }

        $orderModel = new \Models\Order();
        $reportData = $orderModel->getDeliveryManFullReport($id);

        if (!$reportData || empty($reportData['rider'])) {
            header('Location: /sodai-dorkar/public/admin/delivery-men');
            exit;
        }

        return $this->view('admin/delivery-men/report', [
            'title' => 'Rider Report: ' . $reportData['rider']['name'],
            'rider' => $reportData['rider'],
            'stats' => $reportData['stats'],
            'allocations' => $reportData['allocations'],
            'activeOrders' => $reportData['activeOrders'],
            'history' => $reportData['history']
        ]);
    }

    public function store() {
        $name = $_POST['name'] ?? '';
        $phone = $_POST['phone'] ?? ''; // Added phone
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if ($name && $username && $password) {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            
            try {
                $this->db->query("INSERT INTO users (name, phone, username, password, role) VALUES (:name, :phone, :username, :password, 'delivery_man')", [
                    'name' => $name,
                    'phone' => $phone,
                    'username' => $username,
                    'password' => $hashedPassword
                ]);
            } catch (\Exception $e) {
                // Handle duplicate username or other errors silently for now or redirect with error
            }
        }
        
        header('Location: /sodai-dorkar/public/admin/delivery-men');
        exit;
    }

    public function destroy() {
        $id = $_POST['id'] ?? null;
        if ($id) {
            // Using the existing $this->db connection
            $this->db->query("DELETE FROM users WHERE id = :id AND role = 'delivery_man'", ['id' => $id]);
        }
        header('Location: /sodai-dorkar/public/admin/delivery-men');
        exit;
    }

    public function changePassword() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $id = $_POST['id'] ?? null;
        $password = $_POST['password'] ?? null;

        if ($id && $password && strlen($password) >= 4) {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $this->db->query("UPDATE users SET password = :password WHERE id = :id AND role = 'delivery_man'", [
                'password' => $hashedPassword,
                'id' => $id
            ]);
            $_SESSION['success'] = "Password updated successfully.";
        } else {
            $_SESSION['error'] = "Failed to update password. Ensure it's at least 4 characters.";
        }
        header('Location: /sodai-dorkar/public/admin/delivery-men');
        exit;
    }

    public function allocation() {
        $allocationModel = new DmAllocation();
        $userModel = new User();
        $areaModel = new Area();

        $allocations = $allocationModel->all();
        $deliveryMen = $userModel->getByRole('delivery_man');
        $areas = $areaModel->all();

        return $this->view('admin/delivery-men/allocation', [
            'title' => 'Delivery Man Allocation', 
            'allocations' => $allocations,
            'deliveryMen' => $deliveryMen,
            'areas' => $areas
        ]);
    }

    public function storeAllocation() {
        $user_id = $_POST['user_id'] ?? '';
        $area_id = $_POST['area_id'] ?? '';
        $time_slot = $_POST['time_slot'] ?? 'all_time';

        if ($user_id && $area_id) {
            $allocationModel = new DmAllocation();
            try {
                $allocationModel->create([
                    'user_id' => $user_id, 
                    'area_id' => $area_id,
                    'time_slot' => $time_slot
                ]);
            } catch (\PDOException $e) {
                // Ignore duplicate entry errors silently for now or handle them
            }
        }
        
        header('Location: /sodai-dorkar/public/admin/delivery-men/allocation');
        exit;
    }

    public function editAllocation() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /sodai-dorkar/public/admin/delivery-men/allocation');
            exit;
        }

        $allocationModel = new DmAllocation();
        $allocation = $allocationModel->find($id);

        $userModel = new User();
        $deliveryMen = $userModel->getByRole('delivery_man');

        $areaModel = new Area();
        $areas = $areaModel->all();

        return $this->view('admin/delivery-men/allocation_edit', [
            'title' => 'Edit Allocation', 
            'allocation' => $allocation,
            'deliveryMen' => $deliveryMen,
            'areas' => $areas
        ]);
    }

    public function updateAllocation() {
        $id = $_POST['id'] ?? null;
        $user_id = $_POST['user_id'] ?? '';
        $area_id = $_POST['area_id'] ?? '';
        $time_slot = $_POST['time_slot'] ?? 'all_time';

        if ($id && $user_id && $area_id) {
            $allocationModel = new DmAllocation();
            $allocationModel->update($id, [
                'user_id' => $user_id, 
                'area_id' => $area_id,
                'time_slot' => $time_slot
            ]);
        }
        
        header('Location: /sodai-dorkar/public/admin/delivery-men/allocation');
        exit;
    }

    public function destroyAllocation() {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $allocationModel = new DmAllocation();
            $allocationModel->delete($id);
        }
        header('Location: /sodai-dorkar/public/admin/delivery-men/allocation');
        exit;
    }

    public function collectCash() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $adminId = $_SESSION['user_id'] ?? 1; // Fallback to 1 if not set

            $dmId = $_POST['dm_id'] ?? null;
            $amount = (float)($_POST['amount'] ?? 0);
            $note = trim($_POST['note'] ?? '');

            if ($dmId && $amount > 0) {
                $sql = "INSERT INTO rider_collections (user_id, amount, note, collected_by, created_at) 
                        VALUES (:uid, :amt, :note, :cb, NOW())";
                $this->db->query($sql, [
                    'uid' => $dmId,
                    'amt' => $amount,
                    'note' => $note,
                    'cb' => $adminId
                ]);
                $_SESSION['success'] = "Cash collection of ৳ " . number_format($amount) . " recorded successfully.";
            } else {
                $_SESSION['error'] = "Invalid amount or rider selected.";
            }
        }
        header('Location: /sodai-dorkar/public/admin/delivery-men');
        exit;
    }

    public function collectionsHistory() {
        $sql = "SELECT rc.*, u.name as rider_name, a.name as admin_name 
                FROM rider_collections rc
                JOIN users u ON rc.user_id = u.id
                LEFT JOIN users a ON rc.collected_by = a.id
                ORDER BY rc.created_at DESC";
        $collections = $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);

        return $this->view('admin/delivery-men/collections', [
            'title' => 'Cash Collections History',
            'collections' => $collections
        ]);
    }
}
