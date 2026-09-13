<?php

namespace Controllers;

use Core\Controller;
use Core\Middleware;
use Models\User;

class ProfileController extends Controller {

    public function __construct() {
        Middleware::auth(['admin', 'delivery_man']); // Allow both to edit profile eventually, but primarily admin for now
    }

    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            header('Location: /sodai-dorkar/public/login');
            exit;
        }

        $userModel = new User();
        $user = $userModel->find($userId);

        return $this->view('admin/profile/index', [
            'title' => 'My Profile',
            'user' => $user
        ]);
    }

    public function update() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            header('Location: /sodai-dorkar/public/login');
            exit;
        }

        $name = $_POST['name'] ?? '';
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Validation could go here
        
        $updateData = [
            'name' => $name,
            'username' => $username,
            'email' => $email
        ];

        if (!empty($password)) {
            if ($password === $confirm_password) {
                $updateData['password'] = $password;
            } else {
                // Pass error to view - simplified for now
            }
        }

        $userModel = new User();
        $userModel->update($userId, $updateData);

        // Update session name if changed
        $_SESSION['name'] = $name;

        header('Location: /sodai-dorkar/public/admin/profile?success=1');
        exit;
    }
}
