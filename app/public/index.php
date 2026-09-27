<?php
/**
 * Homepage - Latest Episodes
 */
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/functions.php';

$page = max(1, (int)($_GET['page'] ?? 1));
$result = getEpisodes($pdo, $page, 9);
$categories = getCategories($pdo);
$totalEpisodes = getTotalEpisodes($pdo);
$totalCategories = getTotalCategories($pdo);

renderHeader('Trang chủ', 'home');
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <div class="hero-badge">
                <span>🎧</span> Thư viện Podcast hàng đầu
            </div>
            <h1 class="hero-title">
                Khám phá thế giới<br>
                <span class="gradient-text">Podcast Việt Nam</span>
            </h1>
            <p class="hero-subtitle">
                Hàng trăm tập podcast chất lượng cao về công nghệ, kinh doanh, khoa học 
                và nhiều chủ đề hấp dẫn khác. Nghe mọi lúc, mọi nơi.
            </p>
            <div class="hero-stats">
                <div class="stat-item">
                    <span class="stat-number"><?= $totalEpisodes ?></span>
                    <span class="stat-label">Tập podcast</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?= $totalCategories ?></span>
                    <span class="stat-label">Danh mục</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">24/7</span>
                    <span class="stat-label">Luôn sẵn sàng</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Episodes Section -->
<section class="container" style="padding: 60px 24px;">
    <div class="content-layout">
        <div class="content-main">
            <div class="section-header">
                <h2 class="section-title">
                    <span class="icon">🎙️</span>
                    Tập mới nhất
                </h2>
                <span style="color: var(--text-muted); font-size: 14px;">
                    Tổng cộng <?= $result['total'] ?> tập
                </span>
            </div>

            <?php if (empty($result['episodes'])): ?>
                <div class="empty-state">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                        <path d="M9 18V5l12-2v13"></path>
                        <circle cx="6" cy="18" r="3"></circle>
                        <circle cx="18" cy="16" r="3"></circle>
                    </svg>
                    <h3>Chưa có tập podcast nào</h3>
                    <p>Hãy thêm tập podcast đầu tiên qua trang admin.</p>
                </div>
            <?php else: ?>
                <div class="episodes-grid">
                    <?php foreach ($result['episodes'] as $i => $episode): ?>
                        <article class="episode-card scroll-animate stagger-<?= ($i % 4) + 1 ?>">
                            <div class="card-cover" style="<?= $episode['cover_image'] ? '' : 'background:' . getCoverGradient($episode['title']) ?>">
                                <?php if ($episode['cover_image']): ?>
                                    <img src="<?= e($episode['cover_image']) ?>" alt="<?= e($episode['title']) ?>" class="card-cover-img" loading="lazy">
                                <?php else: ?>
                                    <div class="card-cover-placeholder">🎙️</div>
                                <?php endif; ?>
                                <a href="/episode.php?slug=<?= e($episode['slug']) ?>" class="card-play-btn" aria-label="Nghe">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                </a>
                            </div>
                            <div class="card-body">
                                <?php if ($episode['category_name']): ?>
                                    <a href="/category.php?slug=<?= e($episode['category_slug']) ?>" class="card-category">
                                        <?= e($episode['category_name']) ?>
                                    </a>
                                <?php endif; ?>
                                <h3 class="card-title">
                                    <a href="/episode.php?slug=<?= e($episode['slug']) ?>">
                                        <?= e($episode['title']) ?>
                                    </a>
                                </h3>
                                <p class="card-desc"><?= e(truncate($episode['description'], 120)) ?></p>
                                <div class="card-meta">
                                    <span class="card-meta-item">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                        <?= formatDate($episode['published_at']) ?>
                                    </span>
                                    <span class="card-meta-item">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                        <?= e(formatDuration($episode['duration'])) ?>
                                    </span>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <?= renderPagination($result['current'], $result['pages'], '/?') ?>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-widget">
                <h3 class="widget-title">📂 Danh mục</h3>
                <ul class="category-list">
                    <?php foreach ($categories as $cat): ?>
                        <li>
                            <a href="/category.php?slug=<?= e($cat['slug']) ?>">
                                <?= e($cat['name']) ?>
                                <span class="count"><?= $cat['episode_count'] ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="sidebar-widget">
                <h3 class="widget-title">🔍 Tìm kiếm</h3>
                <form action="/search.php" method="GET">
                    <input type="text" name="q" class="form-control" placeholder="Tìm tập podcast..." aria-label="Tìm kiếm">
                </form>
            </div>
        </aside>
    </div>
</section>

<?php renderFooter(); ?>
