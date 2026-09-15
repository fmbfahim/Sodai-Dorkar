<?php

namespace Controllers;

use Core\Controller;
use Core\Middleware;
use Core\Auth;
use Models\User;
use Models\Employee;

class UserController extends Controller {
    protected $userModel;
    protected $employeeModel;

    public function __construct() {
        Middleware::permission('users');
        $this->userModel = new User();
        try {
            $this->employeeModel = new Employee();
        } catch (\Throwable $e) {
            $this->employeeModel = null;
        }
    }

    protected function redirect($path) {
        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
        header("Location: {$base}{$path}");
        exit;
    }

    public function index() {
        $role = $_GET['role'] ?? '';
        $status = $_GET['status'] ?? '';
        $search = $_GET['search'] ?? '';

        $filters = [];
        if ($role) $filters['role'] = $role;
        if ($status) $filters['status'] = $status;
        if ($search) $filters['search'] = $search;

        $users = $this->userModel->all($filters);
        $roles = User::getRoles();
        $allPermissions = Auth::allPermissions();

        // Get unlinked employees (to link with new accounts) safely
        $employees = [];
        if ($this->employeeModel) {
            try {
                $employees = $this->employeeModel->all(['status' => 'active']);
            } catch (\Throwable $e) {
                $employees = [];
            }
        }

        return $this->view('admin/users/index', [
            'title' => 'ব্যবহারকারী ও রোল ব্যবস্থাপনা (User Management)',
            'users' => $users,
            'roles' => $roles,
            'allPermissions' => $allPermissions,
            'employees' => $employees,
            'filters' => [
                'role' => $role,
                'status' => $status,
                'search' => $search
            ],
            'success' => $_GET['success'] ?? null,
            'error' => $_GET['error'] ?? null
        ]);
    }

    public function store() {
        try {
            $name = trim($_POST['name'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '') ?: null;
            $phone = trim($_POST['phone'] ?? '') ?: null;
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'staff';
            $status = $_POST['status'] ?? 'active';
            $employeeId = !empty($_POST['employee_id']) ? intval($_POST['employee_id']) : null;

            if (empty($name) || empty($username) || empty($password)) {
                $this->redirect('/admin/users?error=' . urlencode('নাম, ইউজারনেম ও পাসওয়ার্ড আবশ্যক'));
            }

            // Check if username exists
            $existing = $this->userModel->findByUsername($username);
            if ($existing) {
                $this->redirect('/admin/users?error=' . urlencode('এই ইউজারনেম ইতিমধ্যে ব্যবহৃত হয়েছে'));
            }

            // Permissions assignment
            if ($role === 'admin') {
                $permissions = ['*'];
            } else {
                $submittedPerms = $_POST['permissions'] ?? [];
                if (!empty($submittedPerms) && is_array($submittedPerms)) {
                    $permissions = array_values(array_unique($submittedPerms));
                } else {
                    $permissions = Auth::defaultPermissionsForRole($role);
                }
            }

            $userId = $this->userModel->create([
                'name' => $name,
                'username' => $username,
                'email' => $email,
                'phone' => $phone,
                'password' => $password,
                'role' => $role,
                'permissions' => $permissions,
                'status' => $status
            ]);

            // Link with employee if selected
            if ($employeeId && $userId && $this->employeeModel) {
                try {
                    $emp = $this->employeeModel->find($employeeId);
                    if ($emp) {
                        $this->employeeModel->update($employeeId, array_merge($emp, ['user_id' => $userId]));
                    }
                } catch (\Throwable $e) {
                    error_log("Failed to link employee to user: " . $e->getMessage());
                }
            }

            $this->redirect('/admin/users?success=' . urlencode('ব্যবহারকারী সফলভাবে তৈরি হয়েছে'));
        } catch (\Throwable $e) {
            error_log("UserController::store error: " . $e->getMessage());
            $this->redirect('/admin/users?error=' . urlencode('ব্যবহারকারী তৈরিতে ত্রুটি: ' . $e->getMessage()));
        }
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect('/admin/users');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            $this->redirect('/admin/users?error=' . urlencode('ব্যবহারকারী পাওয়া যায়নি'));
        }

        // Decode user permissions
        $userPerms = [];
        if ($user['role'] === 'admin') {
            $userPerms = ['*'];
        } else {
            $userPerms = json_decode($user['permissions'] ?? '[]', true) ?: [];
            if (empty($userPerms)) {
                $userPerms = Auth::defaultPermissionsForRole($user['role']);
            }
        }
        $user['parsed_permissions'] = $userPerms;

        $roles = User::getRoles();
        $employees = [];
        if ($this->employeeModel) {
            try {
                $employees = $this->employeeModel->all(['status' => 'active']);
            } catch (\Throwable $e) {
                $employees = [];
            }
        }
        $allPermissions = Auth::allPermissions();

        return $this->view('admin/users/edit', [
            'title' => 'ব্যবহারকারী সম্পাদনা - ' . $user['name'],
            'user' => $user,
            'roles' => $roles,
            'employees' => $employees,
            'allPermissions' => $allPermissions,
            'error' => $_GET['error'] ?? null
        ]);
    }

    public function update() {
        try {
            $id = $_POST['id'] ?? null;
            if (!$id) {
                $this->redirect('/admin/users');
            }

            $existingUser = $this->userModel->find($id);
            if (!$existingUser) {
                $this->redirect('/admin/users?error=' . urlencode('ব্যবহারকারী পাওয়া যায়নি'));
            }

            $name = trim($_POST['name'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '') ?: null;
            $phone = trim($_POST['phone'] ?? '') ?: null;
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'staff';
            $status = $_POST['status'] ?? 'active';
            $employeeId = !empty($_POST['employee_id']) ? intval($_POST['employee_id']) : null;

            if (empty($name) || empty($username)) {
                $this->redirect('/admin/users/edit?id=' . $id . '&error=' . urlencode('নাম ও ইউজারনেম আবশ্যক'));
            }

            // Super Admin Protection: Cannot demote id=1 or username=admin
            if ($existingUser['id'] == 1 || ($existingUser['role'] === 'admin' && $existingUser['username'] === 'admin')) {
                $role = 'admin';
                $status = 'active'; // Super admin cannot be deactivated
            }

            // Check if username taken by another user
            $checkUsername = $this->userModel->findByUsername($username);
            if ($checkUsername && $checkUsername['id'] != $id) {
                $this->redirect('/admin/users/edit?id=' . $id . '&error=' . urlencode('এই ইউজারনেম অন্য একজন ব্যবহার করছেন'));
            }

            // Handle permissions
            if ($role === 'admin') {
                $permissions = ['*'];
            } else {
                $submittedPerms = $_POST['permissions'] ?? [];
                if (is_array($submittedPerms)) {
                    $permissions = array_values(array_unique($submittedPerms));
                } else {
                    $permissions = Auth::defaultPermissionsForRole($role);
                }
            }

            $updateData = [
                'name' => $name,
                'username' => $username,
                'email' => $email,
                'phone' => $phone,
                'role' => $role,
                'permissions' => $permissions,
                'status' => $status
            ];

            if (!empty($password)) {
                $updateData['password'] = $password;
            }

            $this->userModel->update($id, $updateData);

            // If editing own account, refresh permissions in session
            if ($id == ($_SESSION['user_id'] ?? 0)) {
                $_SESSION['name'] = $name;
                $_SESSION['role'] = $role;
                $_SESSION['permissions'] = $permissions;
            }

            // Update employee link if changed
            if ($employeeId && $this->employeeModel) {
                try {
                    $emp = $this->employeeModel->find($employeeId);
                    if ($emp) {
                        $this->employeeModel->update($employeeId, array_merge($emp, ['user_id' => $id]));
                    }
                } catch (\Throwable $e) {
                    error_log("Failed to link employee on update: " . $e->getMessage());
                }
            }

            $this->redirect('/admin/users?success=' . urlencode('তথ্য ও পারমিশন সফলভাবে আপডেট হয়েছে'));
        } catch (\Throwable $e) {
            error_log("UserController::update error: " . $e->getMessage());
            $this->redirect('/admin/users/edit?id=' . ($id ?? '') . '&error=' . urlencode('আপডেটে ত্রুটি: ' . $e->getMessage()));
        }
    }

    public function toggleStatus() {
        try {
            $id = $_POST['id'] ?? null;
            if ($id) {
                if ($id == 1 || $id == ($_SESSION['user_id'] ?? 0)) {
                    $this->redirect('/admin/users?error=' . urlencode('সুপার এডমিন বা নিজের একাউন্ট নিষ্ক্রিয় করা যাবে না'));
                }
                $target = $this->userModel->find($id);
                if ($target && $target['role'] === 'admin' && $target['username'] === 'admin') {
                    $this->redirect('/admin/users?error=' . urlencode('সুপার এডমিন একাউন্ট নিষ্ক্রিয় করা যাবে না'));
                }
                $this->userModel->toggleStatus($id);
            }
            $this->redirect('/admin/users?success=' . urlencode('স্ট্যাটাস পরিবর্তন সম্পন্ন হয়েছে'));
        } catch (\Throwable $e) {
            $this->redirect('/admin/users?error=' . urlencode('স্ট্যাটাস পরিবর্তনে ত্রুটি: ' . $e->getMessage()));
        }
    }

    public function destroy() {
        try {
            $id = $_POST['id'] ?? null;
            if ($id) {
                if ($id == 1 || $id == ($_SESSION['user_id'] ?? 0)) {
                    $this->redirect('/admin/users?error=' . urlencode('সুপার এডমিন বা নিজের একাউন্ট মুছে ফেলা যাবে না'));
                }

                $user = $this->userModel->find($id);
                if ($user && ($user['role'] === 'admin' || $user['username'] === 'admin')) {
                    $this->redirect('/admin/users?error=' . urlencode('সুপার এডমিন একাউন্ট মুছে ফেলা সম্পূর্ণ নিষিদ্ধ'));
                }

                $this->userModel->delete($id);
                $this->redirect('/admin/users?success=' . urlencode('ব্যবহারকারী মুছে ফেলা হয়েছে'));
            }
            $this->redirect('/admin/users');
        } catch (\Throwable $e) {
            $this->redirect('/admin/users?error=' . urlencode('ব্যবহারকারী মুছে ফেলতে ত্রুটি: ' . $e->getMessage()));
        }
    }
}
