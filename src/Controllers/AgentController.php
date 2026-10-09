<?php

namespace Controllers;

use Core\Controller;
use Core\Database;
use Core\Middleware;
use Core\View;
use Models\AgentAllocation;
use Models\Area;
use Models\Order;
use Models\User;

class AgentController extends Controller {
    protected $db;
    protected $allocationModel;

    public function __construct() {
        Middleware::permission(['agents', 'users']);
        $config = require __DIR__ . '/../../config/database.php';
        $this->db = new Database($config);
        $this->allocationModel = new AgentAllocation();
    }

    /**
     * Admin Agent Management List
     */
    public function index() {
        $agents = $this->allocationModel->getAgentsWithAreas();
        $areaModel = new Area();
        $allAreas = $areaModel->all();

        return $this->view('admin/agents/index', [
            'title' => 'ফিল্ড এজেন্ট ব্যবস্থাপনা (Field Sales Agents)',
            'agents' => $agents,
            'areas' => $allAreas,
            'success' => $_SESSION['success'] ?? null,
            'error' => $_SESSION['error'] ?? null
        ]);
    }

    /**
     * Store new Agent user
     */
    public function store() {
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $areaIds = $_POST['area_ids'] ?? [];
        $status = $_POST['status'] ?? 'active';

        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';

        if (empty($name) || empty($username) || empty($password)) {
            $_SESSION['error'] = 'নাম, ইউজারনেম এবং পাসওয়ার্ড আবশ্যক';
            header("Location: {$base}/admin/agents");
            exit;
        }

        // Check duplicate username
        $userModel = new User();
        $existing = $userModel->findByUsername($username);
        if ($existing) {
            $_SESSION['error'] = 'এই ইউজারনেম ইতিমধ্যে ব্যবহৃত হচ্ছে';
            header("Location: {$base}/admin/agents");
            exit;
        }

        try {
            $userId = $userModel->create([
                'name' => $name,
                'phone' => $phone,
                'username' => $username,
                'password' => $password,
                'role' => 'agent',
                'status' => $status,
                'permissions' => json_encode(['dashboard', 'orders', 'customers'])
            ]);

            // Save assigned areas
            if (!empty($areaIds)) {
                $this->allocationModel->syncAgentAreas($userId, (array)$areaIds);
            }

            $_SESSION['success'] = 'নতুন এজেন্ট সফলভাবে তৈরি হয়েছে!';
        } catch (\Throwable $e) {
            $_SESSION['error'] = 'এজেন্ট তৈরিতে ব্যর্থ: ' . $e->getMessage();
        }

        header("Location: {$base}/admin/agents");
        exit;
    }

    /**
     * Update Agent info and area allocations
     */
    public function update() {
        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $status = $_POST['status'] ?? 'active';
        $password = $_POST['password'] ?? '';
        $areaIds = $_POST['area_ids'] ?? [];

        if (!$id || empty($name)) {
            $_SESSION['error'] = 'অবৈধ অনুরোধ';
            header("Location: {$base}/admin/agents");
            exit;
        }

        $userModel = new User();
        $user = $userModel->find($id);

        if (!$user || $user['role'] !== 'agent') {
            $_SESSION['error'] = 'এজেন্ট পাওয়া যায়নি';
            header("Location: {$base}/admin/agents");
            exit;
        }

        try {
            $updateData = [
                'name' => $name,
                'phone' => $phone,
                'status' => $status
            ];

            if (!empty($password) && strlen($password) >= 4) {
                $updateData['password'] = $password;
            }

            $userModel->update($id, $updateData);

            // Sync assigned areas
            $this->allocationModel->syncAgentAreas($id, (array)$areaIds);

            $_SESSION['success'] = 'এজেন্ট তথ্য সফলভাবে আপডেট হয়েছে!';
        } catch (\Throwable $e) {
            $_SESSION['error'] = 'আপডেট ব্যর্থ: ' . $e->getMessage();
        }

        header("Location: {$base}/admin/agents");
        exit;
    }

    /**
     * Assign / Re-assign areas to Agent
     */
    public function assignAreas() {
        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
        $agentId = (int)($_POST['agent_id'] ?? 0);
        $areaIds = $_POST['area_ids'] ?? [];

        if (!$agentId) {
            $_SESSION['error'] = 'অবৈধ এজেন্ট আইডি';
            header("Location: {$base}/admin/agents");
            exit;
        }

        $this->allocationModel->syncAgentAreas($agentId, (array)$areaIds);
        $_SESSION['success'] = 'এজেন্টের এরিয়া সফলভাবে নির্ধারণ করা হয়েছে!';
        header("Location: {$base}/admin/agents");
        exit;
    }

    /**
     * Toggle Agent Status (Active / Inactive)
     */
    public function toggleStatus() {
        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
        $id = (int)($_POST['id'] ?? 0);

        if ($id) {
            $userModel = new User();
            $user = $userModel->find($id);
            if ($user && $user['role'] === 'agent') {
                $newStatus = ($user['status'] === 'active') ? 'inactive' : 'active';
                $userModel->update($id, ['status' => $newStatus]);
                $_SESSION['success'] = "এজেন্ট স্ট্যাটাস পরিবর্তিত হয়েছে: {$newStatus}";
            }
        }

        header("Location: {$base}/admin/agents");
        exit;
    }

    /**
     * Agent Performance & Orders Report View
     */
    public function report() {
        $id = (int)($_GET['id'] ?? 0);
        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';

        if (!$id) {
            header("Location: {$base}/admin/agents");
            exit;
        }

        $userModel = new User();
        $agent = $userModel->find($id);

        if (!$agent || $agent['role'] !== 'agent') {
            header("Location: {$base}/admin/agents");
            exit;
        }

        $orderModel = new Order();
        $stats = $orderModel->getAgentStats($id);
        $orders = $orderModel->getByAgent($id);
        $assignedAreas = $this->allocationModel->getByAgent($id);

        return $this->view('admin/agents/report', [
            'title' => 'এজেন্ট রিপোর্ট: ' . $agent['name'],
            'agent' => $agent,
            'stats' => $stats,
            'orders' => $orders,
            'assignedAreas' => $assignedAreas
        ]);
    }
}
