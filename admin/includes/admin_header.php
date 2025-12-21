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
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-leaf"></i> Synergex Admin</h2>
                <p style="font-size: 0.85rem; opacity: 0.8; margin-top: 0.5rem;">
                    <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['admin_name']); ?>
                </p>
            </div>
            
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item <?php echo $currentPage == 'dashboard' ? 'active' : ''; ?>">
                    <i class="fas fa-chart-line"></i> Dashboard
                </a>
                <a href="products.php" class="nav-item <?php echo $currentPage == 'products' ? 'active' : ''; ?>">
                    <i class="fas fa-box"></i> Products
                </a>
                <a href="quotes.php" class="nav-item <?php echo $currentPage == 'quotes' ? 'active' : ''; ?>">
                    <i class="fas fa-clipboard-list"></i> Quote Requests
                </a>
                <a href="messages.php" class="nav-item <?php echo $currentPage == 'messages' ? 'active' : ''; ?>">
                    <i class="fas fa-envelope"></i> Messages
                </a>
                <a href="hero-slider.php" class="nav-item <?php echo $currentPage == 'hero-slider' ? 'active' : ''; ?>">
                    <i class="fas fa-images"></i> Hero Slider
                </a>
                <a href="gallery.php" class="nav-item <?php echo $currentPage == 'gallery' ? 'active' : ''; ?>">
                    <i class="fas fa-photo-video"></i> Gallery
                </a>
                <a href="achievements.php" class="nav-item <?php echo $currentPage == 'achievements' ? 'active' : ''; ?>">
                    <i class="fas fa-trophy"></i> Achievements
                </a>
                <a href="partners.php" class="nav-item <?php echo $currentPage == 'partners' ? 'active' : ''; ?>">
                    <i class="fas fa-handshake"></i> Partners
                </a>
                <a href="content.php" class="nav-item <?php echo $currentPage == 'content' ? 'active' : ''; ?>">
                    <i class="fas fa-file-alt"></i> Page Content
                </a>
                <a href="subscribers.php" class="nav-item <?php echo $currentPage == 'subscribers' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> Subscribers
                </a>
                <a href="chatbot.php" class="nav-item <?php echo $currentPage == 'chatbot' ? 'active' : ''; ?>">
                    <i class="fas fa-robot"></i> AI Chatbot
                </a>
                <a href="settings.php" class="nav-item <?php echo $currentPage == 'settings' ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i> Settings
                </a>
                <a href="<?php echo SITE_URL; ?>" class="nav-item" target="_blank">
                    <i class="fas fa-external-link-alt"></i> View Website
                </a>
                <a href="logout.php" class="nav-item" onclick="return confirm('Are you sure you want to logout?')">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </aside>
        
        <main class="main-content">
            <div class="content-wrapper">

<?php
