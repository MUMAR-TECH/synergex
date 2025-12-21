<?php
require_once __DIR__ . '/functions.php';
$siteName = getSetting('site_name', 'Synergex Solutions');
$tagline = getSetting('tagline', 'Turning Waste Into Sustainable Value');
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $siteName; ?> - <?php echo $tagline; ?></title>
    <meta name="description" content="<?php echo $tagline; ?>">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Stylesheet -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo SITE_URL; ?>/assets/images/logo.png">
</head>
<body>
    <header class="header">
        <nav class="nav-container">
            <div class="logo">
                <a href="<?php echo SITE_URL; ?>/index.php">
                    <img src="<?php echo SITE_URL; ?>/assets/images/logo.png" alt="<?php echo $siteName; ?>">
                </a>
            </div>
            
            <button class="mobile-toggle" id="mobileToggle">
                <span>☰</span>
            </button>
            
            <ul class="nav-menu" id="navMenu">
                <li><a href="<?php echo SITE_URL; ?>/index.php" class="<?php echo $currentPage == 'index' ? 'active' : ''; ?>">Home</a></li>
                <li><a href="<?php echo SITE_URL; ?>/about.php" class="<?php echo $currentPage == 'about' ? 'active' : ''; ?>">About</a></li>
                <li><a href="<?php echo SITE_URL; ?>/what-we-do.php" class="<?php echo $currentPage == 'what-we-do' ? 'active' : ''; ?>">What We Do</a></li>
                <li><a href="<?php echo SITE_URL; ?>/products.php" class="<?php echo $currentPage == 'products' ? 'active' : ''; ?>">Products</a></li>
                <li><a href="<?php echo SITE_URL; ?>/achievements.php" class="<?php echo $currentPage == 'achievements' ? 'active' : ''; ?>">Achievements</a></li>
                <li><a href="<?php echo SITE_URL; ?>/gallery.php" class="<?php echo $currentPage == 'gallery' ? 'active' : ''; ?>">Gallery</a></li>
                <li><a href="<?php echo SITE_URL; ?>/vision-sdgs.php" class="<?php echo $currentPage == 'vision-sdgs' ? 'active' : ''; ?>">Vision & SDGs</a></li>
                <li><a href="<?php echo SITE_URL; ?>/contact.php" class="<?php echo $currentPage == 'contact' ? 'active' : ''; ?>">Contact</a></li>
            </ul>
        </nav>
    </header>
