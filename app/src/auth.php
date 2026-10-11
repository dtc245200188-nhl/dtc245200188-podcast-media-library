<?php
/**
 * Authentication helpers for admin panel
 */

/**
 * Check if user is logged in, redirect to login if not
 */
function requireAuth(): void {
    if (!isLoggedIn()) {
        header('Location: /admin/login.php');
        exit;
    }
}

/**
 * Check if user is currently logged in
 */
function isLoggedIn(): bool {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

/**
 * Attempt to log in with given credentials
 */
function attemptLogin(PDO $pdo, string $username, string $password): bool {
    $stmt = $pdo->prepare("SELECT id, username, password_hash FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);
        return true;
    }

    return false;
}

/**
 * Log out the current user
 */
function logout(): void {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

/**
 * Get current admin username
 */
function getAdminUsername(): string {
    return $_SESSION['admin_username'] ?? 'Admin';
}

/**
 * Generate CSRF token
 */
function generateCsrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCsrfToken(string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Render CSRF hidden input field
 */
function csrfField(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(generateCsrfToken()) . '">';
}

/* ============================================= 
 * User Authentication (separate from admin)
 * ============================================= */

/**
 * Check if a regular user is logged in
 */
function isUserLoggedIn(): bool {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Attempt to log in a regular user with email + password
 */
function attemptUserLogin(PDO $pdo, string $email, string $password): bool {
    $stmt = $pdo->prepare("SELECT id, fullname, email, password_hash FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_fullname'] = $user['fullname'];
        $_SESSION['user_email'] = $user['email'];
        session_regenerate_id(true);
        return true;
    }

    return false;
}

/**
 * Log out the current regular user (preserves admin session if any)
 */
function userLogout(): void {
    unset($_SESSION['user_id'], $_SESSION['user_fullname'], $_SESSION['user_email']);
}

/**
 * Get current user info
 */
function getCurrentUser(): ?array {
    if (!isUserLoggedIn()) return null;
    return [
        'id' => $_SESSION['user_id'],
        'fullname' => $_SESSION['user_fullname'] ?? '',
        'email' => $_SESSION['user_email'] ?? '',
    ];
}
