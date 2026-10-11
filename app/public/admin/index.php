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

<!-- Quick System Tools -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin: 20px 0;">
    <a href="/phpmyadmin/" target="_blank" rel="noopener" class="stat-card" style="text-decoration: none; color: inherit; border-color: rgba(99, 102, 241, 0.3); transition: transform 0.2s, border-color 0.2s; display: flex; align-items: center; gap: 14px;">
        <div style="font-size: 28px;">🗄️</div>
        <div>
            <div style="font-weight: 600; font-size: 15px; color: var(--text-primary); margin-bottom: 2px;">phpMyAdmin ↗</div>
            <div style="font-size: 12px; color: var(--text-muted);">Quản lý cơ sở dữ liệu MySQL</div>
        </div>
    </a>
    <a href="http://localhost:3000" target="_blank" rel="noopener" class="stat-card" style="text-decoration: none; color: inherit; border-color: rgba(249, 115, 22, 0.3); transition: transform 0.2s, border-color 0.2s; display: flex; align-items: center; gap: 14px;">
        <div style="font-size: 28px;">📊</div>
        <div>
            <div style="font-weight: 600; font-size: 15px; color: var(--text-primary); margin-bottom: 2px;">Grafana Monitoring ↗</div>
            <div style="font-size: 12px; color: var(--text-muted);">Giám sát hệ thống & Metrics</div>
        </div>
    </a>
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
