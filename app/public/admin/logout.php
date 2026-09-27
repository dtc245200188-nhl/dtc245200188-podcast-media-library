<?php
/**
 * Admin Logout
 */
require_once __DIR__ . '/../../src/config.php';
require_once __DIR__ . '/../../src/functions.php';
require_once __DIR__ . '/../../src/auth.php';

logout();
header('Location: /admin/login.php');
exit;
