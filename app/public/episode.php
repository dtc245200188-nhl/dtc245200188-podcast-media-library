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

$hasAudio = !empty($episode['audio_file']);

renderHeader($episode['title'], 'episode');
?>

<!-- Episode Banner Hero -->
<section class="ep-banner" id="ep-banner">
    <?php if ($episode['cover_image']): ?>
        <img src="<?= e($episode['cover_image']) ?>" alt="<?= e($episode['title']) ?>" class="ep-banner-img">
    <?php else: ?>
        <div class="ep-banner-img ep-banner-gradient" style="background: <?= getCoverGradient($episode['title']) ?>"></div>
    <?php endif; ?>
    <div class="ep-banner-overlay"></div>
    <div class="container ep-banner-content">
        <!-- Breadcrumb -->
        <nav class="breadcrumb ep-breadcrumb">
            <a href="/">Trang chủ</a>
            <span class="breadcrumb-sep">›</span>
            <?php if ($episode['category_name']): ?>
                <a href="/category.php?slug=<?= e($episode['category_slug']) ?>"><?= e($episode['category_name']) ?></a>
                <span class="breadcrumb-sep">›</span>
            <?php endif; ?>
            <span><?= e(truncate($episode['title'], 50)) ?></span>
        </nav>
        <?php if ($episode['category_name']): ?>
            <a href="/category.php?slug=<?= e($episode['category_slug']) ?>" class="ep-category-badge">
                📁 <?= e($episode['category_name']) ?>
            </a>
        <?php endif; ?>
        <h1 class="ep-title"><?= e($episode['title']) ?></h1>
        <div class="ep-meta">
            <span class="ep-meta-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                <?= formatDate($episode['published_at']) ?>
            </span>
            <span class="ep-meta-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                <?= e(formatDuration($episode['duration'])) ?>
            </span>
        </div>
    </div>
</section>

<div class="ep-detail-body">
    <div class="container">

        <!-- Custom Audio Player -->
        <div class="custom-player <?= $hasAudio ? '' : 'custom-player--disabled' ?>" id="custom-player">
            <?php if ($hasAudio): ?>
                <audio id="audio-element" preload="metadata">
                    <source src="<?= e($episode['audio_file']) ?>" type="audio/mpeg">
                </audio>
            <?php endif; ?>

            <!-- Waveform decoration -->
            <div class="player-waveform" aria-hidden="true">
                <?php for ($i = 0; $i < 60; $i++): ?>
                    <span class="waveform-bar" style="height:<?= rand(15, 90) ?>%"></span>
                <?php endfor; ?>
            </div>

            <!-- Player controls row -->
            <div class="player-row">
                <button class="skip-btn" id="skip-back" aria-label="Tua lại 10 giây" <?= $hasAudio ? '' : 'disabled' ?>>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 4v6h6"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                    <span class="skip-label">10</span>
                </button>

                <button class="play-btn-lg" id="play-btn" aria-label="Phát/Tạm dừng" <?= $hasAudio ? '' : 'disabled' ?>>
                    <?php if ($hasAudio): ?>
                        <svg class="icon-play" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                        <svg class="icon-pause" viewBox="0 0 24 24" fill="currentColor" style="display:none"><rect x="6" y="4" width="4" height="16"></rect><rect x="14" y="4" width="4" height="16"></rect></svg>
                    <?php else: ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 18V5l12-2v13"></path><circle cx="6" cy="18" r="3"></circle><circle cx="18" cy="16" r="3"></circle></svg>
                    <?php endif; ?>
                </button>

                <button class="skip-btn" id="skip-forward" aria-label="Tua tới 10 giây" <?= $hasAudio ? '' : 'disabled' ?>>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 4v6h-6"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                    <span class="skip-label">10</span>
                </button>
            </div>

            <!-- Progress bar -->
            <div class="player-progress-row">
                <span class="time-display" id="current-time">0:00</span>
                <div class="progress-track" id="progress-container">
                    <div class="progress-filled" id="progress-bar" style="width:0%"></div>
                </div>
                <span class="time-display" id="duration-time"><?= e(formatDuration($episode['duration'])) ?></span>
            </div>

            <!-- Volume row -->
            <div class="player-volume-row">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
                <input type="range" id="volume-slider" class="volume-slider" min="0" max="100" value="80" aria-label="Âm lượng" <?= $hasAudio ? '' : 'disabled' ?>>
            </div>

            <?php if (!$hasAudio): ?>
                <div class="player-no-audio">
                    <p>🎵 Chưa có audio cho tập này</p>
                    <p class="player-no-audio-sub">Quản trị viên có thể upload audio qua trang Admin.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Description Section -->
        <div class="ep-description-section">
            <h2 class="ep-section-heading">📝 Nội dung tập</h2>
            
            <?php if (!empty($episode['host_name'])): ?>
                <div class="ep-host-info">
                    <?php if (!empty($episode['host_avatar'])): ?>
                        <img src="<?= e($episode['host_avatar']) ?>" alt="<?= e($episode['host_name']) ?>" class="host-avatar">
                    <?php else: ?>
                        <div class="host-avatar-placeholder"><?= mb_substr($episode['host_name'], 0, 1) ?></div>
                    <?php endif; ?>
                    <div class="host-details">
                        <span class="host-label">Hosted by</span>
                        <span class="host-name"><?= e($episode['host_name']) ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <div class="ep-description <?= mb_strlen($episode['description']) > 400 ? 'ep-description--collapsed' : '' ?>" id="ep-description">
                <?= nl2br(e($episode['description'])) ?>
            </div>
            <?php if (mb_strlen($episode['description']) > 400): ?>
                <button class="ep-toggle-desc" id="toggle-desc" onclick="toggleDescription()">
                    <span class="toggle-text">Xem thêm</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </button>
            <?php endif; ?>
        </div>

        <!-- Related Episodes -->
        <?php if (!empty($related)): ?>
            <section class="related-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <span class="icon">🎧</span>
                        Có thể bạn quan tâm
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

<script>
function toggleDescription() {
    const desc = document.getElementById('ep-description');
    const btn = document.getElementById('toggle-desc');
    if (!desc || !btn) return;
    const isCollapsed = desc.classList.contains('ep-description--collapsed');
    desc.classList.toggle('ep-description--collapsed');
    btn.querySelector('.toggle-text').textContent = isCollapsed ? 'Thu gọn' : 'Xem thêm';
    btn.querySelector('svg').style.transform = isCollapsed ? 'rotate(180deg)' : '';
}
</script>

<?php renderFooter(); ?>
