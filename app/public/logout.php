<?php
/**
 * User Logout
 */
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/auth.php';

userLogout();
header('Location: /');
exit;
