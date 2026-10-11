<?php
/**
 * Script to fix UTF-8 encoding for categories and episodes
 */
require_once __DIR__ . '/src/config.php';

echo "Fixing UTF-8 encoding in database...\n";

// 1. Fix Categories
$categories = [
    'cong-nghe' => [
        'name' => 'Công nghệ',
        'description' => 'Podcast về công nghệ, lập trình, AI và xu hướng digital mới nhất. Cập nhật kiến thức tech mỗi tuần.'
    ],
    'kinh-doanh' => [
        'name' => 'Kinh doanh',
        'description' => 'Podcast về khởi nghiệp, marketing số, quản trị doanh nghiệp và chiến lược phát triển bền vững.'
    ],
    'khoa-hoc' => [
        'name' => 'Khoa học',
        'description' => 'Podcast khám phá thế giới khoa học, từ vũ trụ bao la đến thế giới vi mô, giải đáp những bí ẩn tự nhiên.'
    ],
    'loi-song' => [
        'name' => 'Lối sống',
        'description' => 'Podcast về phát triển bản thân, sức khỏe tinh thần, và xây dựng lối sống cân bằng, hạnh phúc.'
    ],
    'giao-duc' => [
        'name' => 'Giáo dục',
        'description' => 'Podcast về giáo dục, phương pháp học tập hiệu quả và định hướng nghề nghiệp cho thế hệ trẻ.'
    ],
];

$stmtCat = $pdo->prepare("UPDATE categories SET name = ?, description = ? WHERE slug = ?");
foreach ($categories as $slug => $data) {
    $stmtCat->execute([$data['name'], $data['description'], $slug]);
    echo "Updated category: {$data['name']} ({$slug})\n";
}

// 2. Fix Episodes Titles
$episodeTitles = [
    'tuong-lai-cua-ai-2025' => 'Tương lai của AI trong 2025',
    'docker-kubernetes-nguoi-moi' => 'Docker và Kubernetes cho người mới bắt đầu',
    'khoi-nghiep-tu-so-0' => 'Khởi nghiệp từ số 0 - Bài học thực tế',
    'marketing-so-thoi-dai-moi' => 'Marketing số thời đại mới',
    'bi-an-cua-vu-tru' => 'Bí ẩn của vũ trụ - Hố đen và Vật chất tối',
    'bien-doi-khi-hau' => 'Biến đổi khí hậu - Thực trạng và giải pháp',
    'mindfulness-thien-dinh' => 'Mindfulness - Thiền định trong thời đại số',
    'hoc-lap-trinh-2025' => 'Học lập trình từ đâu trong 2025?',
    'web3-blockchain-hype-hay-revolution' => 'Web3 và Blockchain - Hype hay Revolution?',
    'quan-ly-tai-chinh-genz' => 'Quản lý tài chính cá nhân cho GenZ',
];

$stmtEpTitle = $pdo->prepare("UPDATE episodes SET title = ? WHERE slug = ?");
foreach ($episodeTitles as $slug => $title) {
    $stmtEpTitle->execute([$title, $slug]);
    echo "Updated episode title: {$title}\n";
}

// 3. Fix Episodes Detailed Descriptions from scripts
$scriptsDir = __DIR__ . '/scripts';
$stmtEpDesc = $pdo->prepare("UPDATE episodes SET description = ? WHERE slug = ?");

if (is_dir($scriptsDir)) {
    foreach ($episodeTitles as $slug => $title) {
        $txtFile = $scriptsDir . '/episode-' . $slug . '.txt';
        if (file_exists($txtFile)) {
            $desc = file_get_contents($txtFile);
            if ($desc !== false && trim($desc) !== '') {
                $stmtEpDesc->execute([trim($desc), $slug]);
                echo "Updated episode full description from script: {$slug}\n";
            }
        }
    }
}

echo "Encoding fix completed successfully!\n";
