<?php
/**
 * Search Page
 */
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/functions.php';

$query = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));

$result = null;
if ($query !== '') {
    $result = getEpisodes($pdo, $page, 9, null, $query);
}

renderHeader('Tìm kiếm' . ($query ? ': ' . $query : ''), 'search');
?>

<section class="search-section">
    <div class="container">
        <div style="text-align:center; margin-bottom:40px;">
            <h1 class="page-title">🔍 Tìm kiếm Podcast</h1>
            <p class="page-subtitle">Nhập từ khóa để tìm tập podcast bạn quan tâm</p>
        </div>

        <!-- Search Form -->
        <div class="search-box">
            <form action="/search.php" method="GET">
                <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" 
                       name="q" 
                       class="search-input" 
                       placeholder="Tìm kiếm theo tiêu đề hoặc mô tả..." 
                       value="<?= e($query) ?>"
                       autofocus
                       aria-label="Tìm kiếm podcast">
            </form>
        </div>

        <?php if ($query !== '' && $result !== null): ?>
            <!-- Search Results -->
            <div class="search-results-info">
                Tìm thấy <strong><?= $result['total'] ?></strong> kết quả cho "<strong><?= e($query) ?></strong>"
            </div>

            <?php if (empty($result['episodes'])): ?>
                <div class="empty-state">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <h3>Không tìm thấy kết quả</h3>
                    <p>Hãy thử với từ khóa khác hoặc duyệt theo danh mục.</p>
                    <a href="/" class="btn btn-primary" style="margin-top:16px">← Về trang chủ</a>
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
                                    <a href="/episode.php?slug=<?= e($episode['slug']) ?>"><?= e($episode['title']) ?></a>
                                </h3>
                                <p class="card-desc"><?= e(truncate($episode['description'], 120)) ?></p>
                                <div class="card-meta">
                                    <span class="card-meta-item"><?= formatDate($episode['published_at']) ?></span>
                                    <span class="card-meta-item"><?= e(formatDuration($episode['duration'])) ?></span>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <?= renderPagination($result['current'], $result['pages'], "/search.php?q=" . urlencode($query)) ?>
            <?php endif; ?>
        <?php elseif ($query === ''): ?>
            <div class="empty-state">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <h3>Bắt đầu tìm kiếm</h3>
                <p>Nhập từ khóa ở ô tìm kiếm phía trên để khám phá podcast.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php renderFooter(); ?>
