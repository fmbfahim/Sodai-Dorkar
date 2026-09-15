<?php

// 1. যদি সার্ভারে database.local.php ফাইল থাকে, সেটি অগ্রাধিকার পাবে (Git কখনোই এটিকে ওভাররাইট করবে না)
if (file_exists(__DIR__ . '/database.local.php')) {
    return require __DIR__ . '/database.local.php';
}

// 2. যদি রুট ডিরেক্টরিতে .env ফাইল থাকে, সেটি থেকে তথ্য লোড করবে
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $env = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $val) = explode('=', $line, 2);
            $env[trim($key)] = trim(trim($val), '"\'');
        }
    }
    if (!empty($env['DB_NAME']) || !empty($env['DB_DATABASE'])) {
        return [
            'host'     => $env['DB_HOST'] ?? 'localhost',
            'port'     => intval($env['DB_PORT'] ?? 3306),
            'dbname'   => $env['DB_NAME'] ?? $env['DB_DATABASE'] ?? 'sodai_test',
            'user'     => $env['DB_USER'] ?? $env['DB_USERNAME'] ?? 'root',
            'password' => $env['DB_PASS'] ?? $env['DB_PASSWORD'] ?? '',
            'charset'  => $env['DB_CHARSET'] ?? 'utf8mb4',
        ];
    }
}

// 3. ডিফল্ট লোকালহোস্ট কনফিগারেশন (Default Localhost Fallback)
return [
    'host'     => 'localhost',
    'port'     => 3306,
    'dbname'   => 'sodai_test',
    'user'     => 'root',
    'password' => '',
    'charset'  => 'utf8mb4',
];

