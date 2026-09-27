<?php
/**
 * Admin Login Page
 */
require_once __DIR__ . '/../../src/config.php';
require_once __DIR__ . '/../../src/functions.php';
require_once __DIR__ . '/../../src/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: /admin/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        if (attemptLogin($pdo, $username, $password)) {
            header('Location: /admin/index.php');
            exit;
        } else {
            $error = 'Tên đăng nhập hoặc mật khẩu không đúng.';
        }
    } else {
        $error = 'Vui lòng nhập đầy đủ thông tin đăng nhập.';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Admin | PodcastHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="login-page">
        <div class="login-card">
            <div class="login-header">
                <a href="/" class="logo">
                    <span class="logo-icon">🎙️</span>
                    <span class="logo-text">Podcast<span class="logo-accent">Hub</span></span>
                </a>
                <h2>Đăng nhập Admin</h2>
                <p>Quản lý nội dung podcast của bạn</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="username">Tên đăng nhập</label>
                    <input type="text" id="username" name="username" class="form-control" 
                           placeholder="Nhập tên đăng nhập" value="<?= e($_POST['username'] ?? '') ?>" 
                           required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <input type="password" id="password" name="password" class="form-control" 
                           placeholder="Nhập mật khẩu" required>
                </div>
                <button type="submit" class="btn btn-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
                    Đăng nhập
                </button>
            </form>

            <div style="text-align:center; margin-top:24px; font-size:13px; color:var(--text-muted)">
                <a href="/" style="color:var(--text-secondary)">← Về trang chủ</a>
            </div>
        </div>
    </div>
</body>
</html>
