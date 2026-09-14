<?php
// migration_hr_payroll.php

require_once __DIR__ . '/src/Core/Database.php';
$config = require __DIR__ . '/config/database.php';

try {
    $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['user'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo "Starting HR, Payroll & User Management Migration...\n";

    // 1. Upgrade users table to support multiple roles and status
    try {
        $pdo->exec("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'manager', 'accountant', 'staff', 'agent', 'delivery_man') NOT NULL DEFAULT 'staff'");
        echo "Updated users 'role' column.\n";
    } catch (PDOException $e) { echo "Skip role alter: " . $e->getMessage() . "\n"; }

    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN status ENUM('active', 'inactive') DEFAULT 'active' AFTER role");
        echo "Added 'status' column to users.\n";
    } catch (PDOException $e) { echo "Skip status add: " . $e->getMessage() . "\n"; }

    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN email VARCHAR(100) NULL AFTER name");
        echo "Added 'email' column to users.\n";
    } catch (PDOException $e) { echo "Skip email add: " . $e->getMessage() . "\n"; }

    // 2. Create Departments table
    $pdo->exec("CREATE TABLE IF NOT EXISTS departments (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "Table 'departments' ready.\n";

    // 3. Create Designations table
    $pdo->exec("CREATE TABLE IF NOT EXISTS designations (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        department_id INT UNSIGNED NOT NULL,
        title VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "Table 'designations' ready.\n";

    // 4. Create Employees table
    $pdo->exec("CREATE TABLE IF NOT EXISTS employees (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        emp_code VARCHAR(30) UNIQUE NOT NULL,
        user_id INT UNSIGNED NULL,
        name VARCHAR(255) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        email VARCHAR(100) NULL,
        nid VARCHAR(50) NULL,
        gender ENUM('male', 'female', 'other') DEFAULT 'male',
        joining_date DATE NOT NULL,
        department_id INT UNSIGNED NULL,
        designation_id INT UNSIGNED NULL,
        employment_type ENUM('full_time', 'part_time', 'contract', 'daily') DEFAULT 'full_time',
        basic_salary DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        house_rent DECIMAL(10,2) DEFAULT 0.00,
        medical_allowance DECIMAL(10,2) DEFAULT 0.00,
        other_allowance DECIMAL(10,2) DEFAULT 0.00,
        bank_name VARCHAR(100) NULL,
        bank_account_no VARCHAR(50) NULL,
        mobile_banking_type VARCHAR(20) NULL,
        mobile_banking_number VARCHAR(20) NULL,
        emergency_contact VARCHAR(20) NULL,
        address TEXT NULL,
        photo_path VARCHAR(255) NULL,
        status ENUM('active', 'inactive', 'on_leave', 'terminated') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
        FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
        FOREIGN KEY (designation_id) REFERENCES designations(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "Table 'employees' ready.\n";

    // 5. Create Attendances table
    $pdo->exec("CREATE TABLE IF NOT EXISTS attendances (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        employee_id INT UNSIGNED NOT NULL,
        date DATE NOT NULL,
        in_time TIME NULL,
        out_time TIME NULL,
        status ENUM('present', 'late', 'half_day', 'absent', 'leave', 'holiday') DEFAULT 'present',
        note VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_emp_date (employee_id, date),
        FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "Table 'attendances' ready.\n";

    // 6. Create Leave Requests table
    $pdo->exec("CREATE TABLE IF NOT EXISTS leave_requests (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        employee_id INT UNSIGNED NOT NULL,
        leave_type ENUM('casual', 'sick', 'annual', 'unpaid') DEFAULT 'casual',
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        total_days INT NOT NULL DEFAULT 1,
        reason TEXT NULL,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        approved_by INT UNSIGNED NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
        FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "Table 'leave_requests' ready.\n";

    // 7. Create Payrolls table
    $pdo->exec("CREATE TABLE IF NOT EXISTS payrolls (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        salary_month VARCHAR(7) NOT NULL, /* e.g. 2026-09 */
        employee_id INT UNSIGNED NOT NULL,
        working_days INT NOT NULL DEFAULT 30,
        present_days INT NOT NULL DEFAULT 30,
        absent_days INT NOT NULL DEFAULT 0,
        leave_days INT NOT NULL DEFAULT 0,
        basic_salary DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        allowances DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        bonus DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        deductions DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        advance_salary_deduction DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        net_salary DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        payment_method ENUM('cash', 'bank', 'bkash', 'nagad') DEFAULT 'cash',
        payment_date DATE NULL,
        status ENUM('generated', 'approved', 'paid') DEFAULT 'generated',
        note TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_emp_month (employee_id, salary_month),
        FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "Table 'payrolls' ready.\n";

    // 8. Seed default departments & designations if table is empty
    $deptCount = $pdo->query("SELECT COUNT(*) FROM departments")->fetchColumn();
    if ($deptCount == 0) {
        $depts = [
            'Operations' => ['Operations Manager', 'Warehouse Supervisor', 'Packaging Staff', 'Quality Inspector'],
            'Delivery & Logistics' => ['Logistics Lead', 'Delivery Rider', 'Dispatch Officer'],
            'Accounts & Finance' => ['Chief Accountant', 'Accounts Executive', 'Billing Officer'],
            'Administration & HR' => ['HR Manager', 'HR Executive', 'Office Admin'],
            'Procurement & Sourcing' => ['Sourcing Specialist', 'Vendor Relations Officer']
        ];

        foreach ($depts as $deptName => $designations) {
            $stmt = $pdo->prepare("INSERT INTO departments (name, description) VALUES (?, ?)");
            $stmt->execute([$deptName, "Department for {$deptName}"]);
            $deptId = $pdo->lastInsertId();

            $desigStmt = $pdo->prepare("INSERT INTO designations (department_id, title) VALUES (?, ?)");
            foreach ($designations as $title) {
                $desigStmt->execute([$deptId, $title]);
            }
        }
        echo "Seeded default departments and designations.\n";
    }

    echo "HR, Payroll & User Management Migration Completed Successfully!\n";

} catch (PDOException $e) {
    die("Migration Failed: " . $e->getMessage() . "\n");
}
