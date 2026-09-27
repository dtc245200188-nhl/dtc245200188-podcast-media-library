<?php
/**
 * Admin Dashboard
 */
require_once __DIR__ . '/../../src/config.php';
require_once __DIR__ . '/../../src/functions.php';
require_once __DIR__ . '/../../src/auth.php';
requireAuth();

$totalEpisodes = getTotalEpisodes($pdo);
$totalCategories = getTotalCategories($pdo);

// Recent episodes
$recentResult = getEpisodes($pdo, 1, 5);

renderAdminHeader('Dashboard', 'dashboard');
?>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-card-label">Tổng tập podcast</div>
        <div class="stat-card-value"><?= $totalEpisodes ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-card-label">Danh mục</div>
        <div class="stat-card-value"><?= $totalCategories ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-card-label">Trạng thái</div>
        <div class="stat-card-value" style="font-size:20px; -webkit-text-fill-color: var(--success);">🟢 Hoạt động</div>
    </div>
</div>

<!-- Recent Episodes -->
<div class="admin-header" style="margin-top:16px">
    <h2 class="admin-title" style="font-size:18px">📋 Tập podcast gần đây</h2>
    <a href="/admin/episodes.php" class="btn btn-secondary btn-sm">Xem tất cả</a>
</div>

<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Tiêu đề</th>
                <th>Danh mục</th>
                <th>Thời lượng</th>
                <th>Ngày đăng</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recentResult['episodes'] as $ep): ?>
                <tr>
                    <td><strong style="color:var(--text-primary)"><?= e($ep['title']) ?></strong></td>
                    <td><span class="badge"><?= e($ep['category_name'] ?? 'N/A') ?></span></td>
                    <td><?= e(formatDuration($ep['duration'])) ?></td>
                    <td><?= formatDate($ep['published_at']) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($recentResult['episodes'])): ?>
                <tr><td colspan="4" style="text-align:center; padding:32px; color:var(--text-muted)">Chưa có tập podcast nào.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php renderAdminFooter(); ?>
