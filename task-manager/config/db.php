<?php
$config = parse_ini_file(__DIR__ . '../');
$dsn = "mysql:host={$config['DB_HOST']};dbname={$config['DB_NAME']};charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Show detailed errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Return associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                   // Native prepared statements
];

try {
    $pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASS'], $options);
} catch (PDOException $e) {
    die('Database Connection Failed: ' . $e->getMessage());
}
