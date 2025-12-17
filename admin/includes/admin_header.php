<?php
// ============================================================================
// FILE: admin/includes/admin_header.php - Admin Header
// ============================================================================
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Synergex Solutions</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Synergex Admin</h2>
            </div>
            
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                    <span>📊</span> Dashboard
                </a>
                <a href="products.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>">
                    <span>📦</span> Products
                </a>
                <a href="quotes.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'quotes.php' ? 'active' : ''; ?>">
                    <span>📋</span> Quote Requests
                </a>
                <a href="messages.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'messages.php' ? 'active' : ''; ?>">
                    <span>✉️</span> Messages
                </a>
                <a href="gallery.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'gallery.php' ? 'active' : ''; ?>">
                    <span>🖼️</span> Gallery
                </a>
                <a href="achievements.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'achievements.php' ? 'active' : ''; ?>">
                    <span>🏆</span> Achievements
                </a>
                <a href="subscribers.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'subscribers.php' ? 'active' : ''; ?>">
                    <span>📧</span> Subscribers
                </a>
                <a href="settings.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
                    <span>⚙️</span> Settings
                </a>
                <a href="<?php echo SITE_URL; ?>" class="nav-item" target="_blank">
                    <span>🌐</span> View Website
                </a>
                <a href="logout.php" class="nav-item">
                    <span>🚪</span> Logout
                </a>
            </nav>
        </aside>
        
        <main class="main-content">
            <div class="content-wrapper">
            </div>
        </main>
    </div>
    
    <script src="<?php echo SITE_URL; ?>/assets/js/admin.js"></script>
</body>
</html>