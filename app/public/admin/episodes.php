<?php
/**
 * Admin - Episodes CRUD
 */
require_once __DIR__ . '/../../src/config.php';
require_once __DIR__ . '/../../src/functions.php';
require_once __DIR__ . '/../../src/auth.php';
requireAuth();

$message = '';
$messageType = '';
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';
    
    if ($postAction === 'create') {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 0) ?: null;
        $duration = trim($_POST['duration'] ?? '00:00');
        $publishedAt = $_POST['published_at'] ?? date('Y-m-d');
        $slug = createSlug($title);
        
        // Handle file uploads
        $audioFile = handleUpload('audio_file', 'audio', [
            'audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg', 'audio/mp4', 'audio/x-m4a'
        ]);
        $coverImage = handleUpload('cover_image', 'covers', [
            'image/jpeg', 'image/png', 'image/webp', 'image/gif'
        ]);
        
        if ($title) {
            try {
                $stmt = $pdo->prepare(
                    "INSERT INTO episodes (title, slug, description, audio_file, cover_image, category_id, duration, published_at) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
                );
                $stmt->execute([$title, $slug, $description, $audioFile, $coverImage, $categoryId, $duration, $publishedAt]);
                $message = 'Đã thêm tập podcast "' . $title . '" thành công!';
                $messageType = 'success';
                $action = 'list';
            } catch (PDOException $e) {
                $message = 'Lỗi: ' . $e->getMessage();
                $messageType = 'danger';
            }
        } else {
            $message = 'Vui lòng nhập tiêu đề tập podcast.';
            $messageType = 'danger';
        }
    }
    
    if ($postAction === 'update' && $id > 0) {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 0) ?: null;
        $duration = trim($_POST['duration'] ?? '00:00');
        $publishedAt = $_POST['published_at'] ?? date('Y-m-d');
        $slug = createSlug($title);
        
        // Handle file uploads
        $audioFile = handleUpload('audio_file', 'audio', [
            'audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg', 'audio/mp4', 'audio/x-m4a'
        ]);
        $coverImage = handleUpload('cover_image', 'covers', [
            'image/jpeg', 'image/png', 'image/webp', 'image/gif'
        ]);
        
        if ($title) {
            try {
                // Build update query dynamically
                $fields = "title = ?, slug = ?, description = ?, category_id = ?, duration = ?, published_at = ?";
                $params = [$title, $slug, $description, $categoryId, $duration, $publishedAt];
                
                if ($audioFile) {
                    $fields .= ", audio_file = ?";
                    $params[] = $audioFile;
                }
                if ($coverImage) {
                    $fields .= ", cover_image = ?";
                    $params[] = $coverImage;
                }
                
                $params[] = $id;
                $stmt = $pdo->prepare("UPDATE episodes SET {$fields} WHERE id = ?");
                $stmt->execute($params);
                
                $message = 'Đã cập nhật tập podcast thành công!';
                $messageType = 'success';
                $action = 'list';
            } catch (PDOException $e) {
                $message = 'Lỗi khi cập nhật: ' . $e->getMessage();
                $messageType = 'danger';
            }
        }
    }
    
    if ($postAction === 'delete' && $id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM episodes WHERE id = ?");
            $stmt->execute([$id]);
            $message = 'Đã xóa tập podcast thành công!';
            $messageType = 'success';
            $action = 'list';
        } catch (PDOException $e) {
            $message = 'Lỗi khi xóa.';
            $messageType = 'danger';
        }
    }
}

// Get data for views
$categories = getCategories($pdo);
$editEpisode = null;

if ($action === 'edit' && $id > 0) {
    $editEpisode = getEpisodeById($pdo, $id);
    if (!$editEpisode) {
        $action = 'list';
        $message = 'Không tìm thấy tập podcast.';
        $messageType = 'danger';
    }
}

$page = max(1, (int)($_GET['page'] ?? 1));
$episodes = getEpisodes($pdo, $page, 15);

renderAdminHeader('Quản lý Tập Podcast', 'episodes');
?>

<?php if ($message): ?>
    <div class="alert alert-<?= $messageType ?>">
        <?= e($message) ?>
    </div>
<?php endif; ?>

<?php if ($action === 'list'): ?>
    <!-- Episodes List -->
    <div class="admin-header" style="margin-top:-16px; margin-bottom:24px">
        <span style="color:var(--text-muted); font-size:14px">Tổng cộng <?= $episodes['total'] ?> tập</span>
        <a href="/admin/episodes.php?action=add" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Thêm tập mới
        </a>
    </div>

    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tiêu đề</th>
                    <th>Danh mục</th>
                    <th>Thời lượng</th>
                    <th>Audio</th>
                    <th>Ngày đăng</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($episodes['episodes'] as $ep): ?>
                    <tr>
                        <td><?= $ep['id'] ?></td>
                        <td>
                            <div style="display:flex; align-items:center; gap:12px">
                                <?php if ($ep['cover_image']): ?>
                                    <img src="<?= e($ep['cover_image']) ?>" alt="" style="width:40px; height:40px; border-radius:8px; object-fit:cover">
                                <?php else: ?>
                                    <div style="width:40px; height:40px; border-radius:8px; background:<?= getCoverGradient($ep['title']) ?>; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0">🎙️</div>
                                <?php endif; ?>
                                <div>
                                    <strong style="color:var(--text-primary)"><?= e(truncate($ep['title'], 40)) ?></strong>
                                    <div style="font-size:12px; color:var(--text-muted)"><?= e($ep['slug']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge"><?= e($ep['category_name'] ?? 'N/A') ?></span></td>
                        <td><?= e(formatDuration($ep['duration'])) ?></td>
                        <td>
                            <?php if ($ep['audio_file']): ?>
                                <span style="color:var(--success)">✓ Có</span>
                            <?php else: ?>
                                <span style="color:var(--text-muted)">— Chưa</span>
                            <?php endif; ?>
                        </td>
                        <td><?= formatDate($ep['published_at']) ?></td>
                        <td>
                            <div class="btn-group">
                                <a href="/episode.php?slug=<?= e($ep['slug']) ?>" class="btn btn-secondary btn-sm" target="_blank" title="Xem">👁</a>
                                <a href="/admin/episodes.php?action=edit&id=<?= $ep['id'] ?>" class="btn btn-secondary btn-sm">Sửa</a>
                                <form method="POST" style="display:inline" onsubmit="return confirmDelete('Bạn có chắc chắn muốn xóa tập podcast này?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $ep['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($episodes['episodes'])): ?>
                    <tr><td colspan="7" style="text-align:center; padding:32px; color:var(--text-muted)">Chưa có tập podcast nào.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?= renderPagination($episodes['current'], $episodes['pages'], '/admin/episodes.php?action=list') ?>

<?php elseif ($action === 'add'): ?>
    <!-- Add Episode Form -->
    <div class="admin-form">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="create">
            
            <div class="form-group">
                <label for="title">Tiêu đề *</label>
                <input type="text" id="title" name="title" class="form-control" placeholder="Nhập tiêu đề tập podcast" required>
            </div>
            
            <div class="form-group">
                <label for="description">Mô tả</label>
                <textarea id="description" name="description" class="form-control" rows="5" placeholder="Mô tả chi tiết về tập podcast"></textarea>
            </div>
            
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px">
                <div class="form-group">
                    <label for="category_id">Danh mục</label>
                    <select id="category_id" name="category_id" class="form-control">
                        <option value="">-- Chọn danh mục --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="duration">Thời lượng</label>
                    <input type="text" id="duration" name="duration" class="form-control" placeholder="VD: 45:30" value="00:00">
                    <span class="form-hint">Định dạng: MM:SS hoặc HH:MM:SS</span>
                </div>
            </div>
            
            <div class="form-group">
                <label for="published_at">Ngày đăng</label>
                <input type="date" id="published_at" name="published_at" class="form-control" value="<?= date('Y-m-d') ?>">
            </div>
            
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px">
                <div class="form-group">
                    <label>File Audio</label>
                    <div class="file-input-wrapper">
                        <input type="file" name="audio_file" accept="audio/*">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 18V5l12-2v13"></path><circle cx="6" cy="18" r="3"></circle><circle cx="18" cy="16" r="3"></circle></svg>
                        <span>Kéo thả hoặc click để chọn file audio</span>
                        <small>MP3, WAV, OGG, M4A - Tối đa 100MB</small>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Ảnh Cover</label>
                    <div class="file-input-wrapper">
                        <input type="file" name="cover_image" accept="image/*">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                        <span>Kéo thả hoặc click để chọn ảnh</span>
                        <small>JPG, PNG, WebP - Tối đa 10MB</small>
                    </div>
                </div>
            </div>
            
            <div class="btn-group" style="margin-top:8px">
                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Lưu tập podcast
                </button>
                <a href="/admin/episodes.php" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>

<?php elseif ($action === 'edit' && $editEpisode): ?>
    <!-- Edit Episode Form -->
    <div class="admin-form">
        <form method="POST" enctype="multipart/form-data" action="/admin/episodes.php?action=edit&id=<?= $editEpisode['id'] ?>">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?= $editEpisode['id'] ?>">
            
            <div class="form-group">
                <label for="title">Tiêu đề *</label>
                <input type="text" id="title" name="title" class="form-control" value="<?= e($editEpisode['title']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="description">Mô tả</label>
                <textarea id="description" name="description" class="form-control" rows="5"><?= e($editEpisode['description']) ?></textarea>
            </div>
            
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px">
                <div class="form-group">
                    <label for="category_id">Danh mục</label>
                    <select id="category_id" name="category_id" class="form-control">
                        <option value="">-- Chọn danh mục --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $editEpisode['category_id'] ? 'selected' : '' ?>>
                                <?= e($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="duration">Thời lượng</label>
                    <input type="text" id="duration" name="duration" class="form-control" value="<?= e($editEpisode['duration']) ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="published_at">Ngày đăng</label>
                <input type="date" id="published_at" name="published_at" class="form-control" value="<?= e($editEpisode['published_at']) ?>">
            </div>
            
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px">
                <div class="form-group">
                    <label>File Audio</label>
                    <?php if ($editEpisode['audio_file']): ?>
                        <div class="alert alert-success" style="margin-bottom:12px">
                            ✓ Đã có file: <?= e(basename($editEpisode['audio_file'])) ?>
                        </div>
                    <?php endif; ?>
                    <div class="file-input-wrapper">
                        <input type="file" name="audio_file" accept="audio/*">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 18V5l12-2v13"></path><circle cx="6" cy="18" r="3"></circle><circle cx="18" cy="16" r="3"></circle></svg>
                        <span>Chọn file mới để thay thế</span>
                        <small>MP3, WAV, OGG, M4A - Tối đa 100MB</small>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Ảnh Cover</label>
                    <?php if ($editEpisode['cover_image']): ?>
                        <div style="margin-bottom:12px">
                            <img src="<?= e($editEpisode['cover_image']) ?>" alt="Cover" style="width:80px; height:80px; border-radius:8px; object-fit:cover; border:1px solid var(--border-color)">
                        </div>
                    <?php endif; ?>
                    <div class="file-input-wrapper">
                        <input type="file" name="cover_image" accept="image/*">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                        <span>Chọn ảnh mới để thay thế</span>
                        <small>JPG, PNG, WebP - Tối đa 10MB</small>
                    </div>
                </div>
            </div>
            
            <div class="btn-group" style="margin-top:8px">
                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Cập nhật
                </button>
                <a href="/admin/episodes.php" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
<?php endif; ?>

<?php renderAdminFooter(); ?>
