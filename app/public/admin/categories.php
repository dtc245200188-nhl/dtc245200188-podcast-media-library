<?php
/**
 * Admin - Categories CRUD
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
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $slug = createSlug($name);
        
        if ($name) {
            try {
                $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)");
                $stmt->execute([$name, $slug, $description]);
                $message = 'Đã thêm danh mục "' . $name . '" thành công!';
                $messageType = 'success';
                $action = 'list';
            } catch (PDOException $e) {
                $message = 'Lỗi: Slug đã tồn tại hoặc dữ liệu không hợp lệ.';
                $messageType = 'danger';
            }
        } else {
            $message = 'Vui lòng nhập tên danh mục.';
            $messageType = 'danger';
        }
    }
    
    if ($postAction === 'update' && $id > 0) {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $slug = createSlug($name);
        
        if ($name) {
            try {
                $stmt = $pdo->prepare("UPDATE categories SET name = ?, slug = ?, description = ? WHERE id = ?");
                $stmt->execute([$name, $slug, $description, $id]);
                $message = 'Đã cập nhật danh mục thành công!';
                $messageType = 'success';
                $action = 'list';
            } catch (PDOException $e) {
                $message = 'Lỗi khi cập nhật danh mục.';
                $messageType = 'danger';
            }
        }
    }
    
    if ($postAction === 'delete' && $id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            $message = 'Đã xóa danh mục thành công!';
            $messageType = 'success';
            $action = 'list';
        } catch (PDOException $e) {
            $message = 'Lỗi khi xóa danh mục.';
            $messageType = 'danger';
        }
    }
}

// Get data for edit form
$editCategory = null;
if ($action === 'edit' && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    $editCategory = $stmt->fetch();
    if (!$editCategory) {
        $action = 'list';
        $message = 'Không tìm thấy danh mục.';
        $messageType = 'danger';
    }
}

$categories = getCategories($pdo);

renderAdminHeader('Quản lý Danh mục', 'categories');
?>

<?php if ($message): ?>
    <div class="alert alert-<?= $messageType ?>">
        <?= e($message) ?>
    </div>
<?php endif; ?>

<?php if ($action === 'list'): ?>
    <!-- Category List -->
    <div class="admin-header" style="margin-top:-16px; margin-bottom:24px">
        <span></span>
        <a href="/admin/categories.php?action=add" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Thêm danh mục
        </a>
    </div>

    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên danh mục</th>
                    <th>Slug</th>
                    <th>Số tập</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?= $cat['id'] ?></td>
                        <td><strong style="color:var(--text-primary)"><?= e($cat['name']) ?></strong></td>
                        <td><code style="color:var(--accent); font-size:13px"><?= e($cat['slug']) ?></code></td>
                        <td><span class="badge"><?= $cat['episode_count'] ?></span></td>
                        <td><?= formatDate($cat['created_at']) ?></td>
                        <td>
                            <div class="btn-group">
                                <a href="/admin/categories.php?action=edit&id=<?= $cat['id'] ?>" class="btn btn-secondary btn-sm">Sửa</a>
                                <form method="POST" style="display:inline" onsubmit="return confirmDelete('Bạn có chắc chắn muốn xóa danh mục này?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($categories)): ?>
                    <tr><td colspan="6" style="text-align:center; padding:32px; color:var(--text-muted)">Chưa có danh mục nào.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php elseif ($action === 'add'): ?>
    <!-- Add Category Form -->
    <div class="admin-form">
        <form method="POST">
            <input type="hidden" name="action" value="create">
            <div class="form-group">
                <label for="name">Tên danh mục *</label>
                <input type="text" id="name" name="name" class="form-control" placeholder="Nhập tên danh mục" required>
            </div>
            <div class="form-group">
                <label for="description">Mô tả</label>
                <textarea id="description" name="description" class="form-control" placeholder="Mô tả ngắn về danh mục"></textarea>
            </div>
            <div class="btn-group">
                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Lưu danh mục
                </button>
                <a href="/admin/categories.php" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>

<?php elseif ($action === 'edit' && $editCategory): ?>
    <!-- Edit Category Form -->
    <div class="admin-form">
        <form method="POST" action="/admin/categories.php?action=edit&id=<?= $editCategory['id'] ?>">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?= $editCategory['id'] ?>">
            <div class="form-group">
                <label for="name">Tên danh mục *</label>
                <input type="text" id="name" name="name" class="form-control" value="<?= e($editCategory['name']) ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Mô tả</label>
                <textarea id="description" name="description" class="form-control"><?= e($editCategory['description']) ?></textarea>
            </div>
            <div class="btn-group">
                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Cập nhật
                </button>
                <a href="/admin/categories.php" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
<?php endif; ?>

<?php renderAdminFooter(); ?>
