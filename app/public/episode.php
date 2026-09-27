<?php
/**
 * Episode Detail Page - Audio Player
 */
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/functions.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) {
    header('Location: /');
    exit;
}

$episode = getEpisode($pdo, $slug);
if (!$episode) {
    http_response_code(404);
    renderHeader('Không tìm thấy', 'episode');
    echo '<div class="container page-content"><div class="empty-state"><h3>Không tìm thấy tập podcast</h3><p>Tập podcast bạn tìm không tồn tại hoặc đã bị xóa.</p><p><a href="/" class="btn btn-primary" style="margin-top:16px">← Về trang chủ</a></p></div></div>';
    renderFooter();
    exit;
}

$related = [];
if ($episode['category_id']) {
    $related = getRelatedEpisodes($pdo, $episode['category_id'], $episode['id']);
}

renderHeader($episode['title'], 'episode');
?>

<div class="episode-detail">
    <div class="container">
        <!-- Breadcrumb -->
        <nav class="breadcrumb">
            <a href="/">Trang chủ</a>
            <span class="breadcrumb-sep">›</span>
            <?php if ($episode['category_name']): ?>
                <a href="/category.php?slug=<?= e($episode['category_slug']) ?>"><?= e($episode['category_name']) ?></a>
                <span class="breadcrumb-sep">›</span>
            <?php endif; ?>
            <span><?= e(truncate($episode['title'], 50)) ?></span>
        </nav>

        <!-- Episode Header -->
        <div class="episode-header">
            <div class="episode-cover">
                <?php if ($episode['cover_image']): ?>
                    <img src="<?= e($episode['cover_image']) ?>" alt="<?= e($episode['title']) ?>">
                <?php else: ?>
                    <div class="episode-cover-placeholder" style="background: <?= getCoverGradient($episode['title']) ?>">
                        🎙️
                    </div>
                <?php endif; ?>
            </div>
            <div class="episode-info">
                <?php if ($episode['category_name']): ?>
                    <a href="/category.php?slug=<?= e($episode['category_slug']) ?>" class="episode-category-badge">
                        📁 <?= e($episode['category_name']) ?>
                    </a>
                <?php endif; ?>
                <h1 class="episode-title"><?= e($episode['title']) ?></h1>
                <div class="episode-meta">
                    <span class="episode-meta-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        <?= formatDate($episode['published_at']) ?>
                    </span>
                    <span class="episode-meta-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <?= e(formatDuration($episode['duration'])) ?>
                    </span>
                </div>
                <div class="episode-description">
                    <?= nl2br(e($episode['description'])) ?>
                </div>
            </div>
        </div>

        <!-- Audio Player -->
        <div class="audio-player" id="audio-player">
            <?php if ($episode['audio_file']): ?>
                <audio id="audio-element" preload="metadata">
                    <source src="<?= e($episode['audio_file']) ?>" type="audio/mpeg">
                    Trình duyệt của bạn không hỗ trợ audio.
                </audio>
                <div class="player-controls">
                    <button class="play-btn" id="play-btn" aria-label="Phát/Tạm dừng">
                        <svg viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                    </button>
                    <div class="player-info">
                        <div class="player-title"><?= e($episode['title']) ?></div>
                        <div class="player-subtitle"><?= e($episode['category_name'] ?? 'Podcast') ?> • <?= e(formatDuration($episode['duration'])) ?></div>
                    </div>
                    <div class="player-volume">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
                        <input type="range" id="volume-slider" class="volume-slider" min="0" max="100" value="80" aria-label="Âm lượng">
                    </div>
                </div>
                <div class="player-progress">
                    <div class="progress-bar-container" id="progress-container">
                        <div class="progress-bar" id="progress-bar" style="width: 0%"></div>
                    </div>
                    <div class="progress-time">
                        <span id="current-time">0:00</span>
                        <span id="duration-time">0:00</span>
                    </div>
                </div>
            <?php else: ?>
                <div class="no-audio-message">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M9 18V5l12-2v13"></path>
                        <circle cx="6" cy="18" r="3"></circle>
                        <circle cx="18" cy="16" r="3"></circle>
                    </svg>
                    <p>File audio chưa được tải lên cho tập này.</p>
                    <p style="font-size:13px; margin-top:4px">Quản trị viên có thể upload audio qua trang Admin.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Related Episodes -->
        <?php if (!empty($related)): ?>
            <section class="related-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <span class="icon">🎧</span>
                        Tập cùng danh mục
                    </h2>
                    <a href="/category.php?slug=<?= e($episode['category_slug']) ?>" class="section-link">
                        Xem tất cả
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </a>
                </div>
                <div class="related-grid">
                    <?php foreach ($related as $rel): ?>
                        <article class="episode-card">
                            <div class="card-cover" style="<?= $rel['cover_image'] ? '' : 'background:' . getCoverGradient($rel['title']) ?>">
                                <?php if ($rel['cover_image']): ?>
                                    <img src="<?= e($rel['cover_image']) ?>" alt="<?= e($rel['title']) ?>" class="card-cover-img" loading="lazy">
                                <?php else: ?>
                                    <div class="card-cover-placeholder">🎙️</div>
                                <?php endif; ?>
                                <a href="/episode.php?slug=<?= e($rel['slug']) ?>" class="card-play-btn" aria-label="Nghe">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                </a>
                            </div>
                            <div class="card-body">
                                <h3 class="card-title">
                                    <a href="/episode.php?slug=<?= e($rel['slug']) ?>"><?= e($rel['title']) ?></a>
                                </h3>
                                <div class="card-meta">
                                    <span class="card-meta-item"><?= formatDate($rel['published_at']) ?></span>
                                    <span class="card-meta-item"><?= e(formatDuration($rel['duration'])) ?></span>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </div>
</div>

<?php renderFooter(); ?>
