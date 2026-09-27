<?php
/**
 * Helper functions for Podcast Media Library
 */

/**
 * Get all categories
 */
function getCategories(PDO $pdo): array {
    return $pdo->query("SELECT c.*, COUNT(e.id) as episode_count 
                        FROM categories c 
                        LEFT JOIN episodes e ON c.id = e.category_id 
                        GROUP BY c.id 
                        ORDER BY c.name")
               ->fetchAll();
}

/**
 * Get single category by slug
 */
function getCategory(PDO $pdo, string $slug): ?array {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ?");
    $stmt->execute([$slug]);
    $result = $stmt->fetch();
    return $result ?: null;
}

/**
 * Get episodes with pagination and optional filters
 */
function getEpisodes(PDO $pdo, int $page = 1, int $perPage = 9, ?int $categoryId = null, ?string $search = null): array {
    $where = [];
    $params = [];

    if ($categoryId) {
        $where[] = "e.category_id = ?";
        $params[] = $categoryId;
    }

    if ($search) {
        $where[] = "(e.title LIKE ? OR e.description LIKE ?)";
        $params[] = "%{$search}%";
        $params[] = "%{$search}%";
    }

    $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    // Count total records
    $countSql = "SELECT COUNT(*) FROM episodes e {$whereClause}";
    $stmt = $pdo->prepare($countSql);
    $stmt->execute($params);
    $total = (int) $stmt->fetchColumn();

    // Get paginated results
    $offset = ($page - 1) * $perPage;
    $sql = "SELECT e.*, c.name as category_name, c.slug as category_slug 
            FROM episodes e 
            LEFT JOIN categories c ON e.category_id = c.id 
            {$whereClause} 
            ORDER BY e.published_at DESC 
            LIMIT {$perPage} OFFSET {$offset}";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return [
        'episodes' => $stmt->fetchAll(),
        'total'    => $total,
        'pages'    => (int) ceil($total / $perPage),
        'current'  => $page,
        'perPage'  => $perPage,
    ];
}

/**
 * Get single episode by slug
 */
function getEpisode(PDO $pdo, string $slug): ?array {
    $stmt = $pdo->prepare(
        "SELECT e.*, c.name as category_name, c.slug as category_slug 
         FROM episodes e 
         LEFT JOIN categories c ON e.category_id = c.id 
         WHERE e.slug = ?"
    );
    $stmt->execute([$slug]);
    $result = $stmt->fetch();
    return $result ?: null;
}

/**
 * Get episode by ID
 */
function getEpisodeById(PDO $pdo, int $id): ?array {
    $stmt = $pdo->prepare(
        "SELECT e.*, c.name as category_name, c.slug as category_slug 
         FROM episodes e 
         LEFT JOIN categories c ON e.category_id = c.id 
         WHERE e.id = ?"
    );
    $stmt->execute([$id]);
    $result = $stmt->fetch();
    return $result ?: null;
}

/**
 * Get related episodes (same category, excluding current)
 */
function getRelatedEpisodes(PDO $pdo, int $categoryId, int $excludeId, int $limit = 4): array {
    $stmt = $pdo->prepare(
        "SELECT e.*, c.name as category_name, c.slug as category_slug 
         FROM episodes e 
         LEFT JOIN categories c ON e.category_id = c.id 
         WHERE e.category_id = ? AND e.id != ? 
         ORDER BY e.published_at DESC 
         LIMIT ?"
    );
    $stmt->execute([$categoryId, $excludeId, $limit]);
    return $stmt->fetchAll();
}

/**
 * Get total episode count
 */
function getTotalEpisodes(PDO $pdo): int {
    return (int) $pdo->query("SELECT COUNT(*) FROM episodes")->fetchColumn();
}

/**
 * Get total category count
 */
function getTotalCategories(PDO $pdo): int {
    return (int) $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
}

/**
 * Create URL-friendly slug from Vietnamese text
 */
function createSlug(string $string): string {
    $slug = mb_strtolower($string, 'UTF-8');
    
    // Vietnamese diacritics mapping
    $slug = preg_replace('/[áàảãạăắằẳẵặâấầẩẫậ]/u', 'a', $slug);
    $slug = preg_replace('/[éèẻẽẹêếềểễệ]/u', 'e', $slug);
    $slug = preg_replace('/[íìỉĩị]/u', 'i', $slug);
    $slug = preg_replace('/[óòỏõọôốồổỗộơớờởỡợ]/u', 'o', $slug);
    $slug = preg_replace('/[úùủũụưứừửữự]/u', 'u', $slug);
    $slug = preg_replace('/[ýỳỷỹỵ]/u', 'y', $slug);
    $slug = preg_replace('/[đ]/u', 'd', $slug);
    
    // Replace non-alphanumeric with hyphens
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');
    
    return $slug;
}

/**
 * Format duration string
 */
function formatDuration(string $duration): string {
    return $duration ?: '00:00';
}

/**
 * Format date to Vietnamese style
 */
function formatDate(?string $date): string {
    if (!$date) return 'Chưa xuất bản';
    $timestamp = strtotime($date);
    $months = ['', 'Th01', 'Th02', 'Th03', 'Th04', 'Th05', 'Th06', 
               'Th07', 'Th08', 'Th09', 'Th10', 'Th11', 'Th12'];
    $day = date('d', $timestamp);
    $month = $months[(int)date('m', $timestamp)];
    $year = date('Y', $timestamp);
    return "{$day} {$month}, {$year}";
}

/**
 * Escape HTML for safe output
 */
function e(?string $str): string {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Truncate text to specified length
 */
function truncate(string $text, int $length = 150): string {
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length) . '...';
}

/**
 * Handle file upload (audio or image)
 */
function handleUpload(string $inputName, string $subDir, array $allowedTypes): ?string {
    if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $file = $_FILES[$inputName];
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, $allowedTypes)) {
        return null;
    }

    $uploadDir = __DIR__ . '/../public/uploads/' . $subDir . '/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }

    $filename = uniqid() . '_' . time() . '.' . $extension;
    $destination = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return '/uploads/' . $subDir . '/' . $filename;
    }

    return null;
}

/**
 * Render pagination HTML
 */
function renderPagination(int $currentPage, int $totalPages, string $baseUrl): string {
    if ($totalPages <= 1) return '';

    // Ensure baseUrl has proper separator
    $separator = (strpos($baseUrl, '?') !== false) ? '&' : '?';

    $html = '<nav class="pagination" aria-label="Phân trang">';

    // Previous button
    if ($currentPage > 1) {
        $html .= '<a href="' . $baseUrl . $separator . 'page=' . ($currentPage - 1) . '" class="page-link page-prev" aria-label="Trang trước">';
        $html .= '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>';
        $html .= ' Trước</a>';
    }

    // Page numbers
    $html .= '<div class="page-numbers">';
    for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++) {
        $active = $i === $currentPage ? ' active' : '';
        $html .= '<a href="' . $baseUrl . $separator . 'page=' . $i . '" class="page-link page-num' . $active . '">' . $i . '</a>';
    }
    $html .= '</div>';

    // Next button
    if ($currentPage < $totalPages) {
        $html .= '<a href="' . $baseUrl . $separator . 'page=' . ($currentPage + 1) . '" class="page-link page-next" aria-label="Trang sau">';
        $html .= 'Sau <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>';
        $html .= '</a>';
    }

    $html .= '</nav>';
    return $html;
}

/**
 * Render the page header HTML
 */
function renderHeader(string $title = 'PodcastHub', string $activePage = 'home'): void {
    $categories = [];
    global $pdo;
    if (isset($pdo)) {
        try {
            $categories = getCategories($pdo);
        } catch (Exception $e) {
            $categories = [];
        }
    }
    ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="PodcastHub - Thư viện podcast Việt Nam với hàng trăm tập podcast chất lượng cao về công nghệ, kinh doanh, khoa học và nhiều lĩnh vực khác.">
    <title><?= e($title) ?> | PodcastHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="site-header" id="site-header">
        <div class="container header-inner">
            <a href="/" class="logo" id="logo">
                <span class="logo-icon">🎙️</span>
                <span class="logo-text">Podcast<span class="logo-accent">Hub</span></span>
            </a>
            
            <nav class="main-nav" id="main-nav">
                <a href="/" class="nav-link <?= $activePage === 'home' ? 'active' : '' ?>">Trang chủ</a>
                <div class="nav-dropdown">
                    <button class="nav-link dropdown-toggle <?= $activePage === 'category' ? 'active' : '' ?>" id="categories-dropdown">
                        Danh mục
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    <div class="dropdown-menu" id="categories-menu">
                        <?php foreach ($categories as $cat): ?>
                            <a href="/category.php?slug=<?= e($cat['slug']) ?>" class="dropdown-item">
                                <?= e($cat['name']) ?>
                                <span class="badge"><?= $cat['episode_count'] ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <a href="/search.php" class="nav-link <?= $activePage === 'search' ? 'active' : '' ?>">Tìm kiếm</a>
            </nav>

            <div class="header-actions">
                <a href="/search.php" class="search-toggle" id="search-toggle" aria-label="Tìm kiếm">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </a>
                <button class="mobile-menu-toggle" id="mobile-menu-toggle" aria-label="Menu">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </header>

    <main class="main-content">
    <?php
}

/**
 * Render the page footer HTML
 */
function renderFooter(): void {
    ?>
    </main>

    <!-- Footer -->
    <footer class="site-footer" id="site-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="/" class="logo">
                        <span class="logo-icon">🎙️</span>
                        <span class="logo-text">Podcast<span class="logo-accent">Hub</span></span>
                    </a>
                    <p class="footer-desc">Thư viện podcast Việt Nam - Nơi hội tụ kiến thức, chia sẻ đam mê và kết nối cộng đồng.</p>
                </div>
                <div class="footer-links">
                    <h4>Liên kết</h4>
                    <a href="/">Trang chủ</a>
                    <a href="/search.php">Tìm kiếm</a>
                    <a href="/admin/login.php">Admin</a>
                </div>
                <div class="footer-links">
                    <h4>Hệ thống</h4>
                    <a href="/phpmyadmin/" target="_blank">phpMyAdmin</a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> PodcastHub. Đồ án môn Triển khai & Quản trị Hệ thống Phần mềm.</p>
            </div>
        </div>
    </footer>

    <script src="/assets/js/player.js"></script>
</body>
</html>
    <?php
}

/**
 * Generate cover image placeholder with gradient based on text
 */
function getCoverGradient(string $text): string {
    $hash = crc32($text);
    $hue1 = abs($hash % 360);
    $hue2 = ($hue1 + 40) % 360;
    return "linear-gradient(135deg, hsl({$hue1}, 70%, 40%), hsl({$hue2}, 80%, 30%))";
}

/**
 * Render admin panel header with sidebar
 */
function renderAdminHeader(string $title = 'Dashboard', string $activePage = 'dashboard'): void {
    $username = $_SESSION['admin_username'] ?? 'Admin';
    ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?> - Admin | PodcastHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <!-- Admin Header Bar -->
    <header class="admin-header-bar">
        <div style="display:flex;align-items:center;gap:16px">
            <a href="/" class="logo">
                <span class="logo-icon">🎙️</span>
                <span class="logo-text">Podcast<span class="logo-accent">Hub</span></span>
            </a>
            <span class="badge" style="background:rgba(236,72,153,0.15);color:var(--secondary)">Admin</span>
        </div>
        <div class="admin-user-info">
            <a href="/" class="btn btn-secondary btn-sm" style="margin-right:8px">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
                Xem trang
            </a>
            <div class="user-avatar"><?= strtoupper(mb_substr($username, 0, 1)) ?></div>
            <span class="user-name"><?= e($username) ?></span>
            <a href="/admin/logout.php" class="btn btn-danger btn-sm">Đăng xuất</a>
        </div>
    </header>

    <!-- Admin Layout -->
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="admin-sidebar-header">
                <h3>⚙️ Quản lý</h3>
            </div>
            <ul class="admin-nav">
                <li>
                    <a href="/admin/index.php" class="<?= $activePage === 'dashboard' ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="/admin/categories.php" class="<?= $activePage === 'categories' ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                        Danh mục
                    </a>
                </li>
                <li>
                    <a href="/admin/episodes.php" class="<?= $activePage === 'episodes' ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18V5l12-2v13"></path><circle cx="6" cy="18" r="3"></circle><circle cx="18" cy="16" r="3"></circle></svg>
                        Tập Podcast
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <div class="admin-main">
            <div class="admin-header">
                <h1 class="admin-title"><?= e($title) ?></h1>
            </div>
    <?php
}

/**
 * Render admin panel footer
 */
function renderAdminFooter(): void {
    ?>
        </div>
    </div>
    <script src="/assets/js/player.js"></script>
</body>
</html>
    <?php
}
