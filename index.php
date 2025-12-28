<?php
// ============================================================================
// FILE: index.php - Homepage
// ============================================================================
require_once 'includes/header.php';

$stats = getImpactStats();
$products = getProducts(true);
$partners = getPartners(true);
$mission = getSetting('mission', 'Creating sustainable value from waste through innovation and community engagement');
$heroSlides = getHeroSlides(true);
?>

<!-- Hero Slider Section -->
<section class="hero-slider-section">
    <?php if (!empty($heroSlides)): ?>
    <div class="hero-slider">
        <?php foreach ($heroSlides as $index => $slide): ?>
        <div class="hero-slide <?php echo $index === 0 ? 'active' : ''; ?>" 
             style="background-image: url('<?php echo UPLOAD_URL . $slide['image']; ?>');">
            <div class="hero-overlay"></div>
            <div class="container">
                <div class="hero-content">
                    <?php if ($slide['title']): ?>
                    <h1 class="fade-in"><?php echo htmlspecialchars($slide['title']); ?></h1>
                    <?php endif; ?>
                    <?php if ($slide['subtitle']): ?>
                    <p class="tagline fade-in"><?php echo htmlspecialchars($slide['subtitle']); ?></p>
                    <?php endif; ?>
                    <?php if ($slide['button_text'] && $slide['button_link']): ?>
                    <div class="hero-buttons fade-in">
                        <a href="<?php echo htmlspecialchars($slide['button_link']); ?>" class="btn btn-primary">
                            <?php echo htmlspecialchars($slide['button_text']); ?>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        
        <!-- Slider Controls -->
        <?php if (count($heroSlides) > 1): ?>
        <button class="slider-btn slider-prev" onclick="changeSlide(-1)"><i class="fas fa-chevron-left"></i></button>
        <button class="slider-btn slider-next" onclick="changeSlide(1)"><i class="fas fa-chevron-right"></i></button>
        
        <!-- Slider Indicators -->
        <div class="slider-indicators">
            <?php foreach ($heroSlides as $index => $slide): ?>
            <span class="indicator <?php echo $index === 0 ? 'active' : ''; ?>" 
                  onclick="goToSlide(<?php echo $index; ?>)"></span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <!-- Fallback Hero Section (if no slides) -->
    <section class="hero">
        <div class="container">
            <h1 class="fade-in"><?php echo $siteName; ?></h1>
            <p class="tagline fade-in"><?php echo $tagline; ?></p>
            <p class="mission fade-in"><?php echo $mission; ?></p>
            <div class="hero-buttons fade-in">
                <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-primary"><i class="fas fa-shopping-cart"></i> Get a Quote</a>
                <a href="<?php echo SITE_URL; ?>/achievements.php" class="btn btn-secondary"><i class="fas fa-chart-line"></i> Explore Our Impact</a>
            </div>
        </div>
    </section>
    <?php endif; ?>
</section>

<!-- What We Do Section -->
<section class="container">
    <h2 class="section-title">What We Do</h2>
    <div class="services-grid">
        <div class="service-card fade-in">
            <div class="service-icon"><i class="fas fa-trash-alt"></i></div>
            <h3>Plastic Waste Management</h3>
            <p>Comprehensive waste collection and sorting services for communities and institutions across Zambia.</p>
            <a href="<?php echo SITE_URL; ?>/what-we-do.php" class="btn btn-secondary"><i class="fas fa-arrow-right"></i> Learn More</a>
        </div>
        
        <div class="service-card fade-in">
            <div class="service-icon"><i class="fas fa-recycle"></i></div>
            <h3>Plastic Recycling</h3>
            <p>Advanced recycling processes that transform plastic waste into valuable raw materials for production.</p>
            <a href="<?php echo SITE_URL; ?>/what-we-do.php" class="btn btn-secondary"><i class="fas fa-arrow-right"></i> Learn More</a>
        </div>
        
        <div class="service-card fade-in">
            <div class="service-icon"><i class="fas fa-cubes"></i></div>
            <h3>Eco-Friendly Pavers & Tiles</h3>
            <p>Durable, water-resistant pavers and tiles made from 100% recycled plastic materials.</p>
            <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-primary"><i class="fas fa-eye"></i> View Products</a>
        </div>
        
        <div class="service-card fade-in">
            <div class="service-icon"><i class="fas fa-users"></i></div>
            <h3>Community Engagement</h3>
            <p>Educational programs and awareness campaigns promoting sustainability and waste management.</p>
            <a href="<?php echo SITE_URL; ?>/what-we-do.php" class="btn btn-secondary"><i class="fas fa-arrow-right"></i> Learn More</a>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="container">
    <h2 class="section-title">Our Products</h2>
    <div class="products-grid">
        <?php foreach ($products as $product): ?>
        <div class="product-card fade-in">
            <?php if ($product['image']): ?>
            <img src="<?php echo UPLOAD_URL . $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
            <?php else: ?>
            <div class="product-image" style="background: var(--light-grey); display: flex; align-items: center; justify-content: center; color: var(--text-dark);">
                <span>No Image</span>
            </div>
            <?php endif; ?>
            
            <div class="product-content">
                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                <p><?php echo htmlspecialchars($product['description']); ?></p>
                <div class="product-price">K<?php echo number_format($product['price'], 2); ?> <span style="font-size: 0.9rem; color: var(--text-dark);"><?php echo htmlspecialchars($product['unit']); ?></span></div>
                
                <?php if ($product['features']): ?>
                <ul class="product-features">
                    <?php foreach (explode('|', $product['features']) as $feature): ?>
                    <li><?php echo htmlspecialchars($feature); ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
                
                <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-primary"><i class="fas fa-shopping-cart"></i> Get Quote</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Partners Section -->
<?php if (!empty($partners)): ?>
<section class="container">
    <h2 class="section-title">Our Partners</h2>
    <div class="partners-grid">
        <?php foreach ($partners as $partner): ?>
        <div class="partner-item fade-in">
            <?php if ($partner['logo']): ?>
            <img src="<?php echo UPLOAD_URL . $partner['logo']; ?>" alt="<?php echo htmlspecialchars($partner['name']); ?>" class="partner-logo">
            <?php else: ?>
            <p><?php echo htmlspecialchars($partner['name']); ?></p>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- Call to Action Section -->
<section class="cta-section" style="background: var(--light-grey); padding: 4rem 2rem; text-align: center;">
    <div class="container">
        <h2>Ready to Make a Difference?</h2>
        <p style="font-size: 1.2rem; margin-bottom: 2rem;">Join us in creating a cleaner, greener Zambia through sustainable waste management.</p>
        <div class="hero-buttons">
            <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-primary"><i class="fas fa-envelope"></i> Get in Touch</a>
            <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-secondary"><i class="fas fa-file-invoice"></i> Request a Quote</a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>