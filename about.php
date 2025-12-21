<?php
// ============================================================================
// FILE: about.php - About Us Page
// ============================================================================
require_once 'includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <h1>About Synergex Solutions</h1>
        <p>Transforming waste into sustainable value for a greener Zambia</p>
    </div>
</section>

<section class="container">
    <div class="fade-in">
        <h2>Who We Are</h2>
        <p style="font-size: 1.1rem; line-height: 1.8;">
            Synergex Solutions is a sustainability-driven enterprise dedicated to addressing plastic waste pollution 
            through innovative recycling and production of eco-friendly building materials. We specialize in 
            transforming plastic waste into durable pavers and tiles, creating value while protecting our environment.
        </p>
    </div>
    
    <div class="services-grid" style="margin-top: 3rem;">
        <div class="service-card fade-in">
            <div class="service-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <h3>The Problem</h3>
            <p><strong>Plastic Pollution:</strong> Millions of tons of plastic waste pollute our communities, 
            water bodies, and ecosystems.</p>
            <p><strong>Urban Waste Crisis:</strong> Growing cities struggle with inadequate waste management 
            infrastructure.</p>
            <p><strong>Youth Unemployment:</strong> Limited opportunities for young people to engage in 
            meaningful, sustainable livelihoods.</p>
        </div>
        
        <div class="service-card fade-in">
            <div class="service-icon"><i class="fas fa-lightbulb"></i></div>
            <h3>Our Solution</h3>
            <p><strong>Circular Economy:</strong> We collect, sort, and recycle plastic waste into valuable 
            products.</p>
            <p><strong>Eco-Friendly Products:</strong> Durable pavers and tiles made from 100% recycled 
            plastic materials.</p>
            <p><strong>Community Engagement:</strong> Creating jobs, educating communities, and promoting 
            sustainable practices.</p>
        </div>
    </div>
</section>

<section style="background: var(--light-grey); padding: 4rem 2rem;">
    <div class="container">
        <h2 class="section-title">Our Founders' Vision</h2>
        <div style="max-width: 800px; margin: 0 auto; text-align: center;">
            <p style="font-size: 1.1rem; line-height: 1.8;">
                Founded by a team of young innovators passionate about sustainability and community development, 
                Synergex Solutions was born from a vision to tackle Zambia's plastic waste crisis while creating 
                economic opportunities. We believe that waste is not garbage—it's a resource waiting to be 
                transformed into something valuable.
            </p>
            <div style="margin-top: 2rem;">
                <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-primary"><i class="fas fa-hands-helping"></i> Join Our Mission</a>
            </div>
        </div>
    </div>
</section>

<section class="container">
    <h2 class="section-title">Our Values</h2>
    <div class="services-grid">
        <div class="service-card fade-in">
            <div class="service-icon"><i class="fas fa-leaf"></i></div>
            <h3>Sustainability First</h3>
            <p>Every decision we make prioritizes environmental impact and long-term sustainability.</p>
        </div>
        
        <div class="service-card fade-in">
            <div class="service-icon"><i class="fas fa-lightbulb"></i></div>
            <h3>Innovation</h3>
            <p>We continuously seek new ways to improve our processes and create better products.</p>
        </div>
        
        <div class="service-card fade-in">
            <div class="service-icon"><i class="fas fa-handshake"></i></div>
            <h3>Community Engagement</h3>
            <p>We work hand-in-hand with communities, creating awareness and opportunities.</p>
        </div>
        
        <div class="service-card fade-in">
            <div class="service-icon"><i class="fas fa-chart-line"></i></div>
            <h3>Scalable Impact</h3>
            <p>Building solutions that can grow and make a difference across Zambia and beyond.</p>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
