<?php
// ============================================================================
// FILE: vision-sdgs.php - Vision & SDG Alignment Page
// ============================================================================
require_once 'includes/header.php';
$vision = getSetting('vision', 'A cleaner, greener Zambia where waste is transformed into valuable resources');
?>

<section class="page-header">
    <div class="container">
        <h1>Vision & SDGs</h1>
        <p>Aligning our work with global sustainability goals</p>
    </div>
</section>

<section class="container">
    <div class="fade-in" style="text-align: center; max-width: 800px; margin: 0 auto 4rem;">
        <h2><a href="vision-details.php?id=1">Our Vision</a></h2>
        <p style="font-size: 1.2rem; line-height: 1.8;"><?php echo htmlspecialchars($vision); ?></p>
    </div>
    
    <div>
        <h2 class="section-title">Our Approach</h2>
        <div class="services-grid">
            <div class="service-card fade-in">
                <h3>🌍 Sustainability First</h3>
                <p>Every decision we make prioritizes environmental impact and long-term sustainability. We believe in creating solutions that benefit both people and planet.</p>
            </div>
            
            <div class="service-card fade-in">
                <h3>💡 Innovation</h3>
                <p>We continuously seek new ways to improve our processes, develop better products, and find creative solutions to waste management challenges.</p>
            </div>
            
            <div class="service-card fade-in">
                <h3>🤝 Community Engagement</h3>
                <p>We work hand-in-hand with communities, creating awareness, providing education, and generating economic opportunities through sustainable practices.</p>
            </div>
            
            <div class="service-card fade-in">
                <h3>📈 Scalable Impact</h3>
                <p>Building solutions that can grow and replicate across Zambia and beyond, creating lasting positive change for generations to come.</p>
            </div>
        </div>
    </div>
</section>

<section style="background: var(--light-grey); padding: 4rem 2rem;">
    <div class="container">
        <h2 class="section-title">SDG Alignment</h2>
        <p style="text-align: center; margin-bottom: 3rem; font-size: 1.1rem;">Our work contributes to the United Nations Sustainable Development Goals</p>
        
        <div class="services-grid">
            <div class="service-card fade-in" style="border-left: 4px solid #8B1A1A;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">8️⃣</div>
                <h3>SDG 8: Decent Work and Economic Growth</h3>
                <p>Creating employment opportunities for youth and promoting inclusive economic growth through sustainable enterprise.</p>
                <ul style="margin-top: 1rem; padding-left: 1.5rem;">
                    <li>Youth employment programs</li>
                    <li>Skills development training</li>
                    <li>Entrepreneurship support</li>
                </ul>
            </div>
            
            <div class="service-card fade-in" style="border-left: 4px solid #F99D26;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">1️⃣1️⃣</div>
                <h3>SDG 11: Sustainable Cities and Communities</h3>
                <p>Making cities and human settlements inclusive, safe, resilient, and sustainable through better waste management.</p>
                <ul style="margin-top: 1rem; padding-left: 1.5rem;">
                    <li>Urban waste management</li>
                    <li>Community clean-up initiatives</li>
                    <li>Sustainable infrastructure development</li>
                </ul>
            </div>
            
            <div class="service-card fade-in" style="border-left: 4px solid #C08B2D;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">1️⃣2️⃣</div>
                <h3>SDG 12: Responsible Consumption and Production</h3>
                <p>Promoting circular economy principles and ensuring sustainable consumption and production patterns.</p>
                <ul style="margin-top: 1rem; padding-left: 1.5rem;">
                    <li>Plastic waste recycling</li>
                    <li>Resource efficiency</li>
                    <li>Waste reduction programs</li>
                </ul>
            </div>
            
            <div class="service-card fade-in" style="border-left: 4px solid #3F7E44;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">1️⃣3️⃣</div>
                <h3>SDG 13: Climate Action</h3>
                <p>Taking urgent action to combat climate change by reducing plastic pollution and promoting sustainable practices.</p>
                <ul style="margin-top: 1rem; padding-left: 1.5rem;">
                    <li>Reducing plastic pollution</li>
                    <li>Carbon footprint reduction</li>
                    <li>Climate education and awareness</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="container" style="text-align: center;">
    <h2>Join Us in Making a Difference</h2>
    <p style="font-size: 1.1rem; margin: 1rem 0 2rem;">Together, we can create a sustainable future for Zambia</p>
    <div class="hero-buttons">
        <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-primary">Partner With Us</a>
        <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-secondary">View Our Products</a>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>