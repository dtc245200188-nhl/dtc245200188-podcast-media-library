<?php
/**
 * User Login Page
 */
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/functions.php';
require_once __DIR__ . '/../src/auth.php';

// Redirect if already logged in
if (isUserLoggedIn()) {
    header('Location: /');
    exit;
}

$error = '';
$success = $_GET['registered'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verifyCsrfToken($token)) {
        $error = 'Phiên làm việc đã hết hạn. Vui lòng thử lại.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email && $password) {
            if (attemptUserLogin($pdo, $email, $password)) {
                header('Location: /');
                exit;
            } else {
                $error = 'Email hoặc mật khẩu không đúng.';
            }
        } else {
            $error = 'Vui lòng nhập đầy đủ thông tin đăng nhập.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Đăng nhập vào PodcastHub - Thư viện podcast Việt Nam">
    <title>Đăng nhập | PodcastHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/auth.css">
</head>
<body>
    <div class="auth-page">
        <!-- Animated Background -->
        <div class="auth-bg-effects">
            <div class="auth-orb auth-orb-1"></div>
            <div class="auth-orb auth-orb-2"></div>
            <div class="auth-orb auth-orb-3"></div>
            <div class="auth-particles" id="auth-particles"></div>
        </div>

        <div class="auth-container">
            <!-- Left Side - Branding -->
            <div class="auth-branding">
                <div class="auth-branding-content">
                    <a href="/" class="logo auth-logo">
                        <span class="logo-icon">🎙️</span>
                        <span class="logo-text">Podcast<span class="logo-accent">Hub</span></span>
                    </a>
                    <h1 class="auth-branding-title">
                        Chào mừng bạn <br>trở lại!
                    </h1>
                    <p class="auth-branding-desc">
                        Đăng nhập để lưu podcast yêu thích, theo dõi các chương trình và nhận thông báo khi có tập mới.
                    </p>
                    <div class="auth-features">
                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">🎧</div>
                            <div>
                                <strong>Lưu yêu thích</strong>
                                <span>Tạo playlist podcast cá nhân</span>
                            </div>
                        </div>
                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">🔔</div>
                            <div>
                                <strong>Thông báo mới</strong>
                                <span>Nhận thông báo khi có tập mới</span>
                            </div>
                        </div>
                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">📊</div>
                            <div>
                                <strong>Lịch sử nghe</strong>
                                <span>Theo dõi tiến trình nghe của bạn</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Decorative SVG -->
                <div class="auth-branding-art" aria-hidden="true">
                    <svg viewBox="0 0 300 300" fill="none">
                        <defs>
                            <linearGradient id="authGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#7c3aed"/>
                                <stop offset="50%" stop-color="#ec4899"/>
                                <stop offset="100%" stop-color="#06b6d4"/>
                            </linearGradient>
                        </defs>
                        <circle cx="150" cy="150" r="120" stroke="url(#authGrad)" stroke-width="1" opacity="0.2" class="auth-pulse-ring"/>
                        <circle cx="150" cy="150" r="80" stroke="url(#authGrad)" stroke-width="2" opacity="0.3" class="auth-pulse-ring" style="animation-delay: 1s"/>
                        <circle cx="150" cy="150" r="40" fill="url(#authGrad)" opacity="0.08"/>
                    </svg>
                </div>
            </div>

            <!-- Right Side - Form -->
            <div class="auth-form-wrapper">
                <div class="auth-card">
                    <div class="auth-card-header">
                        <h2 class="auth-card-title">Đăng nhập</h2>
                        <p class="auth-card-subtitle">Nhập thông tin tài khoản của bạn</p>
                    </div>

                    <?php if ($success): ?>
                        <div class="auth-alert auth-alert-success">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            Đăng ký thành công! Hãy đăng nhập để tiếp tục.
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="auth-alert auth-alert-error">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="15" y1="9" x2="9" y2="15"></line>
                                <line x1="9" y1="9" x2="15" y2="15"></line>
                            </svg>
                            <?= e($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="auth-form" id="login-form">
                        <?= csrfField() ?>
                        
                        <div class="auth-field">
                            <label for="email" class="auth-label">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                                Email
                            </label>
                            <div class="auth-input-wrapper">
                                <input type="email" id="email" name="email" class="auth-input" 
                                       placeholder="you@example.com" 
                                       value="<?= e($_POST['email'] ?? '') ?>" 
                                       required autofocus>
                                <div class="auth-input-glow"></div>
                            </div>
                        </div>

                        <div class="auth-field">
                            <label for="password" class="auth-label">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                                Mật khẩu
                            </label>
                            <div class="auth-input-wrapper">
                                <input type="password" id="password" name="password" class="auth-input" 
                                       placeholder="Nhập mật khẩu" required>
                                <button type="button" class="auth-toggle-password" onclick="togglePassword('password', this)" aria-label="Hiện mật khẩu">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="eye-open">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="eye-closed" style="display:none">
                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                        <line x1="1" y1="1" x2="23" y2="23"></line>
                                    </svg>
                                </button>
                                <div class="auth-input-glow"></div>
                            </div>
                        </div>

                        <button type="submit" class="auth-btn auth-btn-primary" id="login-btn">
                            <span class="auth-btn-text">Đăng nhập</span>
                            <span class="auth-btn-loader" style="display:none">
                                <span class="auth-spinner"></span>
                            </span>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="auth-btn-arrow">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </button>
                    </form>

                    <div class="auth-divider">
                        <span>hoặc</span>
                    </div>

                    <div class="auth-footer-links">
                        <p>Chưa có tài khoản? <a href="/register.php" class="auth-link">Đăng ký ngay</a></p>
                    </div>

                    <a href="/" class="auth-back-link">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        Về trang chủ
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        const eyeOpen = btn.querySelector('.eye-open');
        const eyeClosed = btn.querySelector('.eye-closed');
        if (input.type === 'password') {
            input.type = 'text';
            eyeOpen.style.display = 'none';
            eyeClosed.style.display = 'block';
        } else {
            input.type = 'password';
            eyeOpen.style.display = 'block';
            eyeClosed.style.display = 'none';
        }
    }

    // Form submit animation
    document.getElementById('login-form').addEventListener('submit', function() {
        const btn = document.getElementById('login-btn');
        btn.querySelector('.auth-btn-text').style.display = 'none';
        btn.querySelector('.auth-btn-arrow').style.display = 'none';
        btn.querySelector('.auth-btn-loader').style.display = 'flex';
        btn.disabled = true;
    });

    // Floating particles
    (function() {
        const container = document.getElementById('auth-particles');
        if (!container) return;
        for (let i = 0; i < 20; i++) {
            const particle = document.createElement('div');
            particle.className = 'auth-particle';
            particle.style.cssText = `
                left: ${Math.random() * 100}%;
                top: ${Math.random() * 100}%;
                animation-delay: ${Math.random() * 6}s;
                animation-duration: ${4 + Math.random() * 4}s;
                width: ${2 + Math.random() * 4}px;
                height: ${2 + Math.random() * 4}px;
            `;
            container.appendChild(particle);
        }
    })();
    </script>
</body>
</html>
