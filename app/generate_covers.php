<?php
/**
 * Script tạo 10 ảnh cover podcast dạng gradient và icon sóng âm thanh
 */

// Bỏ qua cảnh báo session_start() nếu script chạy từ CLI
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

require_once __DIR__ . '/src/config.php';

// Đảm bảo thư mục lưu trữ tồn tại
$uploadDir = __DIR__ . '/public/uploads/covers';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Lấy danh sách episodes chưa có cover image (bỏ qua episode đã có ảnh thực)
$stmt = $pdo->query("SELECT id, slug FROM episodes WHERE cover_image IS NULL");
$episodes = $stmt->fetchAll();

if (empty($episodes)) {
    echo "Không tìm thấy episode nào trong database.\n";
    exit;
}

// Bảng màu gradient (Start RGB -> End RGB)
$palettes = [
    [[255, 95, 109], [255, 195, 113]],  // Sweet Morning
    [[33, 147, 176], [109, 213, 237]], // Blue Skies
    [[204, 43, 94], [117, 58, 136]],   // Purple Love
    [[238, 9, 121], [255, 106, 0]],    // Fire
    [[5, 117, 230], [2, 27, 121]],     // Deep Sea
    [[17, 153, 142], [56, 239, 125]],  // Fresh Melon
    [[62, 81, 81], [222, 203, 164]],   // Sand
    [[252, 74, 26], [247, 183, 51]],   // Sunrise
    [[116, 235, 213], [159, 172, 230]],// Calm Water
    [[101, 78, 163], [234, 175, 200]], // Violet
];

$width = 800;
$height = 450;
$count = 0;

foreach ($episodes as $index => $ep) {
    $slug = $ep['slug'];
    $palette = $palettes[$index % count($palettes)];
    
    // 1. Tạo image rỗng
    $img = imagecreatetruecolor($width, $height);
    imagealphablending($img, true);
    imagesavealpha($img, true);
    
    // 2. Vẽ gradient chéo
    $start = $palette[0];
    $end = $palette[1];
    
    // Dùng kỹ thuật vẽ dải ngang cho nhanh thay vì từng pixel
    for ($y = 0; $y < $height; $y++) {
        $r = $start[0] + (($end[0] - $start[0]) * ($y / $height));
        $g = $start[1] + (($end[1] - $start[1]) * ($y / $height));
        $b = $start[2] + (($end[2] - $start[2]) * ($y / $height));
        $color = imagecolorallocate($img, (int)$r, (int)$g, (int)$b);
        imagefilledrectangle($img, 0, $y, $width, $y+1, $color);
    }
    
    // 3. Vẽ icon sóng âm thanh ở giữa
    $cx = $width / 2;
    $cy = $height / 2;
    // Màu trắng, độ trong suốt khoảng 50% (alpha 0-127, 60 ~ 47%)
    $iconColor = imagecolorallocatealpha($img, 255, 255, 255, 60);
    
    $num_bars = 9;
    $bar_w = 16;
    $gap = 12;
    $total_w = ($num_bars * $bar_w) + (($num_bars - 1) * $gap);
    $start_x = $cx - ($total_w / 2);
    $heights = [40, 80, 120, 180, 240, 180, 120, 80, 40];
    
    for ($i = 0; $i < $num_bars; $i++) {
        $x = $start_x + ($i * ($bar_w + $gap));
        $bar_h = $heights[$i];
        
        $y1 = $cy - ($bar_h / 2);
        $y2 = $cy + ($bar_h / 2);
        
        // Vẽ khối chữ nhật và 2 hình tròn ở 2 đầu để bo góc
        imagefilledrectangle($img, (int)$x, (int)$y1, (int)($x + $bar_w), (int)$y2, $iconColor);
        imagefilledellipse($img, (int)($x + $bar_w/2), (int)$y1, $bar_w, $bar_w, $iconColor);
        imagefilledellipse($img, (int)($x + $bar_w/2), (int)$y2, $bar_w, $bar_w, $iconColor);
    }
    
    // 4. Lưu ảnh
    $filename = $slug . '.png';
    $filePath = $uploadDir . '/' . $filename;
    imagepng($img, $filePath);
    imagedestroy($img);
    
    // 5. Update Database
    $dbPath = '/uploads/covers/' . $filename;
    $upStmt = $pdo->prepare("UPDATE episodes SET cover_image = ? WHERE id = ?");
    $upStmt->execute([$dbPath, $ep['id']]);
    
    echo "Đã tạo: {$filename} và cập nhật CSDL.\n";
    $count++;
}

echo "Hoàn thành! Đã xử lý {$count} episodes.\n";
