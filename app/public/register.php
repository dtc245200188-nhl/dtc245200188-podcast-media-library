<?php
/**
 * User Registration Page
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
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verifyCsrfToken($token)) {
        $error = 'Phiên làm việc đã hết hạn. Vui lòng thử lại.';
    } else {
        $fullname = trim($_POST['fullname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        // Validation
        if (empty($fullname)) {
            $errors[] = 'Vui lòng nhập họ và tên.';
        } elseif (mb_strlen($fullname) < 2) {
            $errors[] = 'Họ và tên phải có ít nhất 2 ký tự.';
        }

        if (empty($email)) {
            $errors[] = 'Vui lòng nhập email.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ.';
        } else {
            // Check if email exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = 'Email này đã được đăng ký. <a href="/login.php">Đăng nhập</a>';
            }
        }

        if (empty($password)) {
            $errors[] = 'Vui lòng nhập mật khẩu.';
        } elseif (mb_strlen($password) < 6) {
            $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự.';
        }

        if ($password !== $password_confirm) {
            $errors[] = 'Mật khẩu xác nhận không khớp.';
        }

        if (empty($errors)) {
            try {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (fullname, email, password_hash) VALUES (?, ?, ?)");
                $stmt->execute([$fullname, $email, $hash]);
                
                header('Location: /login.php?registered=1');
                exit;
            } catch (PDOException $e) {
                error_log("Registration error: " . $e->getMessage());
                $error = 'Đã có lỗi xảy ra. Vui lòng thử lại sau.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Đăng ký tài khoản PodcastHub - Thư viện podcast Việt Nam">
    <title>Đăng ký | PodcastHub</title>
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
                        Tham gia cộng đồng <br><span class="gradient-text">Podcast Việt</span>
                    </h1>
                    <p class="auth-branding-desc">
                        Tạo tài khoản miễn phí để trải nghiệm đầy đủ tính năng của PodcastHub.
                    </p>
                    <div class="auth-features">
                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">✨</div>
                            <div>
                                <strong>Hoàn toàn miễn phí</strong>
                                <span>Không cần thẻ tín dụng</span>
                            </div>
                        </div>
                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">🔒</div>
                            <div>
                                <strong>Bảo mật cao</strong>
                                <span>Dữ liệu được mã hóa an toàn</span>
                            </div>
                        </div>
                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">🚀</div>
                            <div>
                                <strong>Đăng ký nhanh</strong>
                                <span>Chỉ mất 30 giây</span>
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
                        <h2 class="auth-card-title">Tạo tài khoản</h2>
                        <p class="auth-card-subtitle">Điền thông tin để bắt đầu</p>
                    </div>

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

                    <?php if (!empty($errors)): ?>
                        <div class="auth-alert auth-alert-error">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            <div>
                                <?php foreach ($errors as $err): ?>
                                    <div><?= $err ?></div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="auth-form" id="register-form">
                        <?= csrfField() ?>
                        
                        <div class="auth-field">
                            <label for="fullname" class="auth-label">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                Họ và tên
                            </label>
                            <div class="auth-input-wrapper">
                                <input type="text" id="fullname" name="fullname" class="auth-input" 
                                       placeholder="Nguyễn Văn A" 
                                       value="<?= e($_POST['fullname'] ?? '') ?>"
                                       required autofocus>
                                <div class="auth-input-glow"></div>
                            </div>
                        </div>

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
                                       required>
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
                                       placeholder="Tối thiểu 6 ký tự" required
                                       minlength="6">
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
                            <!-- Password Strength Indicator -->
                            <div class="password-strength" id="password-strength" style="display:none">
                                <div class="strength-bars">
                                    <div class="strength-bar" id="str-bar-1"></div>
                                    <div class="strength-bar" id="str-bar-2"></div>
                                    <div class="strength-bar" id="str-bar-3"></div>
                                    <div class="strength-bar" id="str-bar-4"></div>
                                </div>
                                <span class="strength-text" id="strength-text"></span>
                            </div>
                        </div>

                        <div class="auth-field">
                            <label for="password_confirm" class="auth-label">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                </svg>
                                Xác nhận mật khẩu
                            </label>
                            <div class="auth-input-wrapper">
                                <input type="password" id="password_confirm" name="password_confirm" class="auth-input" 
                                       placeholder="Nhập lại mật khẩu" required>
                                <button type="button" class="auth-toggle-password" onclick="togglePassword('password_confirm', this)" aria-label="Hiện mật khẩu">
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

                        <button type="submit" class="auth-btn auth-btn-primary" id="register-btn">
                            <span class="auth-btn-text">Tạo tài khoản</span>
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
                        <p>Đã có tài khoản? <a href="/login.php" class="auth-link">Đăng nhập</a></p>
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

    // Password strength checker
    const passwordInput = document.getElementById('password');
    const strengthContainer = document.getElementById('password-strength');
    const strengthText = document.getElementById('strength-text');
    const bars = [
        document.getElementById('str-bar-1'),
        document.getElementById('str-bar-2'),
        document.getElementById('str-bar-3'),
        document.getElementById('str-bar-4')
    ];

    passwordInput.addEventListener('input', function() {
        const val = this.value;
        if (val.length === 0) {
            strengthContainer.style.display = 'none';
            return;
        }
        strengthContainer.style.display = 'flex';

        let score = 0;
        if (val.length >= 6) score++;
        if (val.length >= 10) score++;
        if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        const level = Math.min(4, Math.max(1, score));
        const labels = ['', 'Yếu', 'Trung bình', 'Mạnh', 'Rất mạnh'];
        const colors = ['', '#ef4444', '#f59e0b', '#10b981', '#06b6d4'];

        strengthText.textContent = labels[level];
        strengthText.style.color = colors[level];

        bars.forEach((bar, i) => {
            if (i < level) {
                bar.style.background = colors[level];
                bar.style.transform = 'scaleY(1)';
            } else {
                bar.style.background = 'rgba(255,255,255,0.1)';
                bar.style.transform = 'scaleY(0.6)';
            }
        });
    });

    // Form submit animation
    document.getElementById('register-form').addEventListener('submit', function() {
        const btn = document.getElementById('register-btn');
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
