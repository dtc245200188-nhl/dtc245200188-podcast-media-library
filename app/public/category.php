<?php
/**
 * Category Page - Episodes by Category
 */
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/functions.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) {
    header('Location: /');
    exit;
}

$category = getCategory($pdo, $slug);
if (!$category) {
    http_response_code(404);
    renderHeader('Không tìm thấy danh mục', 'category');
    echo '<div class="container page-content"><div class="empty-state"><h3>Không tìm thấy danh mục</h3><p>Danh mục bạn tìm không tồn tại.</p><p><a href="/" class="btn btn-primary" style="margin-top:16px">← Về trang chủ</a></p></div></div>';
    renderFooter();
    exit;
}

$page = max(1, (int)($_GET['page'] ?? 1));
$result = getEpisodes($pdo, $page, 9, $category['id']);
$categories = getCategories($pdo);

renderHeader($category['name'], 'category');
?>

<!-- Category Header -->
<section class="category-header">
    <div class="container">
        <nav class="breadcrumb" style="margin-bottom:16px">
            <a href="/">Trang chủ</a>
            <span class="breadcrumb-sep">›</span>
            <span>Danh mục</span>
            <span class="breadcrumb-sep">›</span>
            <span><?= e($category['name']) ?></span>
        </nav>
        <h1 class="category-name">📁 <?= e($category['name']) ?></h1>
        <?php if ($category['description']): ?>
            <p class="category-description"><?= e($category['description']) ?></p>
        <?php endif; ?>
    </div>
</section>

<!-- Episodes -->
<section class="container" style="padding: 40px 24px 60px;">
    <div class="content-layout">
        <div class="content-main">
            <div class="section-header">
                <h2 class="section-title">
                    <span class="icon">🎧</span>
                    Các tập podcast
                </h2>
                <span style="color: var(--text-muted); font-size: 14px;">
                    <?= $result['total'] ?> tập
                </span>
            </div>

            <?php if (empty($result['episodes'])): ?>
                <div class="empty-state">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                        <path d="M9 18V5l12-2v13"></path>
                        <circle cx="6" cy="18" r="3"></circle>
                        <circle cx="18" cy="16" r="3"></circle>
                    </svg>
                    <h3>Chưa có tập nào trong danh mục này</h3>
                    <p>Hãy quay lại sau để xem nội dung mới.</p>
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
                                <h3 class="card-title">
                                    <a href="/episode.php?slug=<?= e($episode['slug']) ?>"><?= e($episode['title']) ?></a>
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

                <?= renderPagination($result['current'], $result['pages'], "/category.php?slug=" . urlencode($slug)) ?>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-widget">
                <h3 class="widget-title">📂 Tất cả danh mục</h3>
                <ul class="category-list">
                    <?php foreach ($categories as $cat): ?>
                        <li>
                            <a href="/category.php?slug=<?= e($cat['slug']) ?>" class="<?= $cat['slug'] === $slug ? 'active' : '' ?>">
                                <?= e($cat['name']) ?>
                                <span class="count"><?= $cat['episode_count'] ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </aside>
    </div>
</section>

<?php renderFooter(); ?>
