<?php
require_once __DIR__ . '/src/config.php';

// Check DB connection charset
$pdo->exec("SET NAMES utf8mb4");

$stmt = $pdo->query("SELECT slug FROM episodes");
$slugs = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (!$slugs) {
    echo "No episodes found.\n";
    exit;
}

$updateStmt = $pdo->prepare("UPDATE episodes SET audio_file = ?, duration = '00:40' WHERE slug = ?");

foreach ($slugs as $slug) {
    $audioPath = '/uploads/audio/' . $slug . '.wav';
    $updateStmt->execute([$audioPath, $slug]);
    echo "Updated $slug\n";
}

echo "All episodes updated!\n";
