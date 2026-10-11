<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
require_once __DIR__ . '/src/config.php';

$uploadDir = __DIR__ . '/public/uploads/audio';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Download một file âm thanh mẫu (mp3) nhỏ
$mp3Url = 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3';
$mp3File = $uploadDir . '/sample.mp3';

echo "Đang tải file âm thanh mẫu...\n";
if (!file_exists($mp3File)) {
    $audioData = file_get_contents($mp3Url);
    if ($audioData !== false) {
        file_put_contents($mp3File, $audioData);
        echo "Đã tải xong file mẫu.\n";
    } else {
        echo "Lỗi: Không thể tải file âm thanh.\n";
        exit;
    }
} else {
    echo "File mẫu đã tồn tại.\n";
}

$dbPath = '/uploads/audio/sample.mp3';

// Cập nhật tất cả các tập chưa có audio
$stmt = $pdo->prepare("UPDATE episodes SET audio_file = ? WHERE audio_file IS NULL");
$stmt->execute([$dbPath]);

$count = $stmt->rowCount();
echo "Hoàn thành! Đã cập nhật âm thanh cho {$count} episodes.\n";
