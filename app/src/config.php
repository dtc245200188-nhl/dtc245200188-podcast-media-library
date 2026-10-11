<?php
/**
 * Database Configuration
 * Connects to MySQL via PDO using environment variables
 */

// Start session for authentication
session_start();

// Database connection parameters from environment
$dbHost = getenv('DB_HOST') ?: 'mysql';
$dbName = getenv('DB_NAME') ?: (getenv('MYSQL_DATABASE') ?: 'podcast_db');
$dbUser = getenv('DB_USER') ?: (getenv('MYSQL_USER') ?: 'podcast_user');
$dbPass = getenv('DB_PASS') ?: (getenv('MYSQL_PASSWORD') ?: '');

$dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
];

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    http_response_code(500);
    die('<!DOCTYPE html><html><head><title>Lỗi kết nối</title></head><body style="background:#0a0a1a;color:#f1f5f9;font-family:Inter,sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;margin:0"><div style="text-align:center"><h1 style="color:#ef4444">⚠️ Không thể kết nối cơ sở dữ liệu</h1><p>Vui lòng kiểm tra cấu hình và thử lại sau.</p></div></body></html>');
}

// Bootstrap: create default admin user if none exists
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM admin_users");
    if ((int)$stmt->fetchColumn() === 0) {
        $hash = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO admin_users (username, password_hash) VALUES (?, ?)")
            ->execute(['admin', $hash]);
    }
} catch (PDOException $e) {
    // Table might not exist yet during initial setup, ignore silently
    error_log("Admin bootstrap notice: " . $e->getMessage());
}

// Bootstrap: create users table if not exists (for existing deployments)
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        fullname VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
} catch (PDOException $e) {
    error_log("Users table bootstrap notice: " . $e->getMessage());
}
