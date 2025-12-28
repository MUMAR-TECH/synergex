<?php
// ============================================================================
// FILE: admin/logout.php - Admin Logout
// ============================================================================
require_once __DIR__ . '/../config.php';

// Clear all session data using SessionManager
SessionManager::destroy();

// Clear remember me cookie if exists
CookieManager::delete('remember_token');

header('Location: index.php');
exit;
?>

