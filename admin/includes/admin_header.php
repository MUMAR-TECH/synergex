<?php
// ============================================================================
// FILE: admin/includes/admin_header.php - Admin Panel Header
// ============================================================================
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Synergex Solutions</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Synergex Admin</h2>
                <p style="font-size: 0.85rem; opacity: 0.8; margin-top: 0.5rem;">
                    <?php echo htmlspecialchars($_SESSION['admin_name']); ?>
                </p>
            </div>
            
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item <?php echo $currentPage == 'dashboard' ? 'active' : ''; ?>">
                    <span>📊</span> Dashboard
                </a>
                <a href="products.php" class="nav-item <?php echo $currentPage == 'products' ? 'active' : ''; ?>">
                    <span>📦</span> Products
                </a>
                <a href="quotes.php" class="nav-item <?php echo $currentPage == 'quotes' ? 'active' : ''; ?>">
                    <span>📋</span> Quote Requests
                </a>
                <a href="messages.php" class="nav-item <?php echo $currentPage == 'messages' ? 'active' : ''; ?>">
                    <span>✉️</span> Messages
                </a>
                <a href="gallery.php" class="nav-item <?php echo $currentPage == 'gallery' ? 'active' : ''; ?>">
                    <span>🖼️</span> Gallery
                </a>
                <a href="achievements.php" class="nav-item <?php echo $currentPage == 'achievements' ? 'active' : ''; ?>">
                    <span>🏆</span> Achievements
                </a>
                <a href="subscribers.php" class="nav-item <?php echo $currentPage == 'subscribers' ? 'active' : ''; ?>">
                    <span>📧</span> Subscribers
                </a>
                <a href="settings.php" class="nav-item <?php echo $currentPage == 'settings' ? 'active' : ''; ?>">
                    <span>⚙️</span> Settings
                </a>
                <a href="<?php echo SITE_URL; ?>" class="nav-item" target="_blank">
                    <span>🌐</span> View Website
                </a>
                <a href="logout.php" class="nav-item" onclick="return confirm('Are you sure you want to logout?')">
                    <span>🚪</span> Logout
                </a>
            </nav>
        </aside>
        
        <main class="main-content">
            <div class="content-wrapper">

<?php
