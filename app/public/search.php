<?php
/**
 * PodcastHub - Search Page
 * Fully featured discovery and search interface
 */
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/functions.php';

$query = trim($_GET['q'] ?? '');
$catSlug = trim($_GET['cat'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));

// Load all categories for filter tabs
$categories = [];
try {
    $categories = getCategories($pdo);
} catch (Exception $e) {
    $categories = [];
}

// Find active category if filtered
$selectedCategory = null;
if ($catSlug !== '') {
    foreach ($categories as $cat) {
        if ($cat['slug'] === $catSlug) {
            $selectedCategory = $cat;
            break;
        }
    }
}

// Trending tags suggestions
$popularTags = [
    'AI', 
    'Docker & DevOps', 
    'Khởi nghiệp', 
    'Marketing', 
    'Vũ trụ', 
    'Biến đổi khí hậu', 
    'Mindfulness', 
    'Lập trình 2025', 
    'Tài chính GenZ'
];

$isSearching = ($query !== '' || $selectedCategory !== null);
$result = null;
$recommended = null;

if ($isSearching) {
    $catId = $selectedCategory ? (int)$selectedCategory['id'] : null;
    $searchTerm = $query !== '' ? $query : null;
    $result = getEpisodes($pdo, $page, 9, $catId, $searchTerm);
} else {
    // When no search entered yet, load popular/featured episodes for discovery
    $recommended = getEpisodes($pdo, 1, 6);
}

// Build page title
$pageTitle = 'Tìm kiếm Podcast';
if ($query !== '' && $selectedCategory !== null) {
    $pageTitle = 'Tìm kiếm: ' . $query . ' trong ' . $selectedCategory['name'];
} elseif ($query !== '') {
    $pageTitle = 'Tìm kiếm: ' . $query;
} elseif ($selectedCategory !== null) {
    $pageTitle = 'Khám phá: ' . $selectedCategory['name'];
}

renderHeader($pageTitle, 'search');
?>

<style>
/* Scoped Search Page Styles */
.search-section {
    padding: calc(var(--header-height, 70px) + 30px) 0 70px;
}
.search-header-block {
    text-align: center;
    max-width: 680px;
    margin: 0 auto 32px;
}
.search-box {
    max-width: 760px;
    margin: 0 auto 24px;
    position: relative;
}
.search-form {
    display: flex !important;
    gap: 12px !important;
    align-items: stretch !important;
    position: relative !important;
    width: 100% !important;
}
.search-input-wrapper {
    position: relative !important;
    flex: 1 !important;
    display: flex !important;
    align-items: center !important;
}
.search-icon {
    position: absolute !important;
    left: 20px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    color: #94a3b8 !important;
    pointer-events: none !important;
    z-index: 5 !important;
    width: 20px !important;
    height: 20px !important;
    display: block !important;
}
.search-input {
    width: 100% !important;
    height: 56px !important;
    padding: 0 48px 0 54px !important;
    background: #111026 !important;
    border: 2px solid #2d2b55 !important;
    border-radius: 9999px !important;
    color: #f1f5f9 !important;
    font-size: 15px !important;
    font-family: inherit !important;
    outline: none !important;
    box-sizing: border-box !important;
    transition: all 0.25s ease !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25) !important;
}
.search-input::placeholder {
    color: #64748b !important;
}
.search-input:focus {
    border-color: #8b5cf6 !important;
    box-shadow: 0 0 25px rgba(139, 92, 246, 0.4) !important;
    background: #151336 !important;
}
.search-clear-btn {
    position: absolute !important;
    right: 18px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    background: rgba(255, 255, 255, 0.12) !important;
    border: none !important;
    border-radius: 50% !important;
    width: 26px !important;
    height: 26px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #94a3b8 !important;
    font-size: 13px !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    z-index: 6 !important;
    padding: 0 !important;
}
.search-clear-btn:hover {
    background: rgba(239, 68, 68, 0.25) !important;
    color: #ef4444 !important;
}
.search-submit-btn {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    height: 56px !important;
    padding: 0 28px !important;
    background: linear-gradient(135deg, #8b5cf6, #ec4899) !important;
    color: #ffffff !important;
    font-weight: 600 !important;
    font-size: 15px !important;
    border: none !important;
    border-radius: 9999px !important;
    cursor: pointer !important;
    transition: all 0.25s ease !important;
    white-space: nowrap !important;
    box-shadow: 0 4px 15px rgba(139, 92, 246, 0.35) !important;
    box-sizing: border-box !important;
}
.search-submit-btn:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 25px rgba(139, 92, 246, 0.5) !important;
}
.search-submit-btn:active {
    transform: translateY(0) !important;
}
.search-tags-box {
    max-width: 760px;
    margin: 0 auto 28px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px;
    justify-content: center;
}
.search-tags-label {
    font-size: 13px;
    font-weight: 600;
    color: #94a3b8;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.search-tag-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid #2d2b55;
    border-radius: 9999px;
    color: #cbd5e1;
    font-size: 13px;
    text-decoration: none;
    transition: all 0.2s ease;
}
.search-tag-chip:hover, .search-tag-chip.active {
    background: rgba(139, 92, 246, 0.15);
    border-color: #8b5cf6;
    color: #c4b5fd;
    transform: translateY(-1px);
}
.search-cat-tabs {
    max-width: 860px;
    margin: 0 auto 36px;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    justify-content: center;
}
.search-cat-tab {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 18px;
    background: #111026;
    border: 1px solid #2d2b55;
    border-radius: 9999px;
    color: #cbd5e1;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
}
.search-cat-tab:hover {
    border-color: #a78bfa;
    color: #ffffff;
}
.search-cat-tab.active {
    background: linear-gradient(135deg, #8b5cf6, #ec4899);
    border-color: transparent;
    color: white;
    box-shadow: 0 4px 15px rgba(139, 92, 246, 0.35);
}
.search-cat-tab .badge {
    padding: 2px 7px;
    font-size: 11px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.15);
    color: inherit;
}
@media (max-width: 640px) {
    .search-form {
        flex-direction: column !important;
    }
    .search-submit-btn {
        width: 100% !important;
        height: 50px !important;
    }
}
</style>

<section class="search-section">
    <div class="container">
        <!-- Header Title Block -->
        <div class="search-header-block">
            <h1 class="page-title" style="margin-bottom: 8px;">🔍 Khám phá & Tìm kiếm</h1>
            <p class="page-subtitle">Nhập từ khóa, lọc theo chủ đề hoặc chọn danh mục bạn quan tâm</p>
        </div>

        <!-- Search Box Form -->
        <div class="search-box">
            <form action="/search.php" method="GET" class="search-form" id="search-form">
                <?php if ($catSlug !== ''): ?>
                    <input type="hidden" name="cat" value="<?= e($catSlug) ?>" id="search-cat-hidden">
                <?php endif; ?>
                
                <div class="search-input-wrapper">
                    <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" 
                           name="q" 
                           id="search-input"
                           class="search-input" 
                           placeholder="Nhập tên podcast, nội dung, công nghệ, kinh doanh..." 
                           value="<?= e($query) ?>"
                           autocomplete="off"
                           aria-label="Tìm kiếm podcast">
                    <button type="button" 
                            id="search-clear-btn" 
                            class="search-clear-btn" 
                            title="Xóa tìm kiếm" 
                            style="display: <?= $query !== '' ? 'flex' : 'none' ?>;">✕</button>
                </div>

                <button type="submit" class="search-submit-btn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <span>Tìm kiếm</span>
                </button>
            </form>
        </div>

        <!-- Popular Trending Tags -->
        <div class="search-tags-box">
            <span class="search-tags-label">🔥 Gợi ý:</span>
            <?php foreach ($popularTags as $tag): ?>
                <?php 
                    $isActiveTag = (mb_strtolower($query, 'UTF-8') === mb_strtolower($tag, 'UTF-8'));
                    $tagUrl = '/search.php?q=' . urlencode($tag) . ($catSlug ? '&cat=' . urlencode($catSlug) : '');
                ?>
                <a href="<?= $tagUrl ?>" class="search-tag-chip <?= $isActiveTag ? 'active' : '' ?>">
                    #<?= e($tag) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Category Filter Tabs -->
        <div class="search-cat-tabs">
            <a href="/search.php<?= $query !== '' ? '?q=' . urlencode($query) : '' ?>" 
               class="search-cat-tab <?= ($catSlug === '') ? 'active' : '' ?>">
                Tất cả chủ đề
            </a>
            <?php foreach ($categories as $cat): ?>
                <?php 
                    $isCatActive = ($catSlug === $cat['slug']);
                    $catFilterUrl = '/search.php?cat=' . urlencode($cat['slug']) . ($query !== '' ? '&q=' . urlencode($query) : '');
                ?>
                <a href="<?= $catFilterUrl ?>" class="search-cat-tab <?= $isCatActive ? 'active' : '' ?>">
                    <?= e($cat['name']) ?>
                    <span class="badge"><?= (int)$cat['episode_count'] ?></span>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Content Area: Search Results or Discovery Recommendations -->
        <?php if ($isSearching && $result !== null): ?>
            <!-- Results Information Bar -->
            <div class="search-results-bar">
                <div class="search-results-text">
                    Tìm thấy <strong><?= $result['total'] ?></strong> tập podcast
                    <?php if ($query !== ''): ?>
                        cho từ khóa "<strong><?= e($query) ?></strong>"
                    <?php endif; ?>
                    <?php if ($selectedCategory !== null): ?>
                        trong danh mục "<strong><?= e($selectedCategory['name']) ?></strong>"
                    <?php endif; ?>
                </div>
                <a href="/search.php" class="search-reset-link" title="Xem lại tất cả">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                    Xóa bộ lọc
                </a>
            </div>

            <?php if (empty($result['episodes'])): ?>
                <!-- No Results Empty State -->
                <div class="empty-state">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <h3>Không tìm thấy tập podcast nào phù hợp</h3>
                    <p>Hãy thử với từ khóa ngắn gọn hơn, kiểm tra lại chính tả hoặc chọn các danh mục gợi ý ở trên.</p>
                    <a href="/search.php" class="btn btn-primary" style="margin-top:16px">← Xem tất cả podcast</a>
                </div>
            <?php else: ?>
                <!-- Episodes Grid -->
                <div class="episodes-grid">
                    <?php foreach ($result['episodes'] as $i => $episode): ?>
                        <article class="episode-card scroll-animate stagger-<?= ($i % 4) + 1 ?>">
                            <div class="card-cover" style="<?= $episode['cover_image'] ? '' : 'background:' . getCoverGradient($episode['title']) ?>">
                                <?php if ($episode['cover_image']): ?>
                                    <img src="<?= e($episode['cover_image']) ?>" alt="<?= e($episode['title']) ?>" class="card-cover-img" loading="lazy">
                                <?php else: ?>
                                    <div class="card-cover-placeholder">🎙️</div>
                                <?php endif; ?>

                                <button type="button" 
                                        class="card-fav-btn" 
                                        data-slug="<?= e($episode['slug']) ?>" 
                                        data-title="<?= e($episode['title']) ?>" 
                                        title="Lưu vào yêu thích" 
                                        aria-label="Yêu thích">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                                </button>

                                <a href="/episode.php?slug=<?= e($episode['slug']) ?>" 
                                   class="card-play-btn" 
                                   aria-label="Nghe <?= e($episode['title']) ?>"
                                   data-play-trigger
                                   data-audio="<?= e($episode['audio_file']) ?>"
                                   data-title="<?= e($episode['title']) ?>"
                                   data-slug="<?= e($episode['slug']) ?>"
                                   data-cat="<?= e($episode['category_name']) ?>"
                                   data-cover="<?= e($episode['cover_image']) ?>">
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

                <?php 
                    $paginationUrl = "/search.php?";
                    $queryParts = [];
                    if ($query !== '') $queryParts[] = "q=" . urlencode($query);
                    if ($catSlug !== '') $queryParts[] = "cat=" . urlencode($catSlug);
                    $paginationUrl .= implode('&', $queryParts);
                ?>
                <?= renderPagination($result['current'], $result['pages'], $paginationUrl) ?>
            <?php endif; ?>

        <?php else: ?>
            <!-- Initial Discovery State (When user clicks Search without query) -->
            <div class="search-discovery-header">
                <div>
                    <h2 class="section-title"><span class="icon">✨</span> Gợi ý podcast nổi bật</h2>
                    <p style="color: var(--text-muted); font-size: 14px; margin-top: 4px;">Khám phá các tập podcast thịnh hành và được nghe nhiều nhất</p>
                </div>
            </div>

            <?php if (!empty($recommended['episodes'])): ?>
                <div class="episodes-grid">
                    <?php foreach ($recommended['episodes'] as $i => $episode): ?>
                        <article class="episode-card scroll-animate stagger-<?= ($i % 4) + 1 ?>">
                            <div class="card-cover" style="<?= $episode['cover_image'] ? '' : 'background:' . getCoverGradient($episode['title']) ?>">
                                <?php if ($episode['cover_image']): ?>
                                    <img src="<?= e($episode['cover_image']) ?>" alt="<?= e($episode['title']) ?>" class="card-cover-img" loading="lazy">
                                <?php else: ?>
                                    <div class="card-cover-placeholder">🎙️</div>
                                <?php endif; ?>

                                <button type="button" 
                                        class="card-fav-btn" 
                                        data-slug="<?= e($episode['slug']) ?>" 
                                        data-title="<?= e($episode['title']) ?>" 
                                        title="Lưu vào yêu thích" 
                                        aria-label="Yêu thích">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                                </button>

                                <a href="/episode.php?slug=<?= e($episode['slug']) ?>" 
                                   class="card-play-btn" 
                                   aria-label="Nghe <?= e($episode['title']) ?>"
                                   data-play-trigger
                                   data-audio="<?= e($episode['audio_file']) ?>"
                                   data-title="<?= e($episode['title']) ?>"
                                   data-slug="<?= e($episode['slug']) ?>"
                                   data-cat="<?= e($episode['category_name']) ?>"
                                   data-cover="<?= e($episode['cover_image']) ?>">
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
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const clearBtn = document.getElementById('search-clear-btn');

    if (searchInput && clearBtn) {
        // Toggle clear button on input change
        searchInput.addEventListener('input', function() {
            clearBtn.style.display = this.value.trim() ? 'flex' : 'none';
        });

        // Clear input and focus
        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            clearBtn.style.display = 'none';
            searchInput.focus();
        });
    }
});
</script>

<?php renderFooter(); ?>
