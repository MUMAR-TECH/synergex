<?php
// ============================================================================
// FILE: what-we-do.php - What We Do Detailed Page
// ============================================================================
require_once 'includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <h1>What We Do</h1>
        <p>Comprehensive solutions for sustainable waste management</p>
    </div>
</section>

<section class="container">
    <div class="services-grid">
        <div class="service-card fade-in">
            <div style="font-size: 3rem; margin-bottom: 1rem;">🗑️</div>
            <h3>1. Plastic Waste Management</h3>
            <p>We provide comprehensive waste collection and sorting services for communities, institutions, and businesses across Zambia. Our team works with local communities to establish efficient waste collection systems.</p>
            <ul style="margin-top: 1rem; padding-left: 1.5rem;">
                <li>Door-to-door waste collection</li>
                <li>Waste segregation at source</li>
                <li>Community waste collection points</li>
                <li>Institutional waste management programs</li>
            </ul>
        </div>
        
        <div class="service-card fade-in">
            <div style="font-size: 3rem; margin-bottom: 1rem;">♻️</div>
            <h3>2. Plastic Recycling</h3>
            <p>Using advanced recycling technology, we transform plastic waste into valuable raw materials. Our recycling process ensures minimal environmental impact while maximizing resource recovery.</p>
            <ul style="margin-top: 1rem; padding-left: 1.5rem;">
                <li>Sorting and cleaning of plastic waste</li>
                <li>Shredding and processing</li>
                <li>Quality control and testing</li>
                <li>Raw material production</li>
            </ul>
        </div>
        
        <div class="service-card fade-in">
            <div style="font-size: 3rem; margin-bottom: 1rem;">🧱</div>
            <h3>3. Eco-Friendly Paver & Tile Production</h3>
            <p>We manufacture high-quality, durable pavers and tiles from 100% recycled plastic. Our products are designed to withstand Zambian weather conditions while providing sustainable building solutions.</p>
            <ul style="margin-top: 1rem; padding-left: 1.5rem;">
                <li>Durable and long-lasting</li>
                <li>Water and UV resistant</li>
                <li>Low maintenance requirements</li>
                <li>Customizable designs and colors</li>
            </ul>
        </div>
        
        <div class="service-card fade-in">
            <div style="font-size: 3rem; margin-bottom: 1rem;">👥</div>
            <h3>4. Community Engagement & Awareness</h3>
            <p>We conduct educational programs and awareness campaigns to promote sustainable waste management practices. Our community programs engage schools, local organizations, and residents.</p>
            <ul style="margin-top: 1rem; padding-left: 1.5rem;">
                <li>School education programs</li>
                <li>Community clean-up drives</li>
                <li>Workshops on waste management</li>
                <li>Youth empowerment initiatives</li>
            </ul>
        </div>
        
        <div class="service-card fade-in">
            <div style="font-size: 3rem; margin-bottom: 1rem;">🌱</div>
            <h3>5. Sustainability Promotion</h3>
            <p>We actively promote circular economy principles and sustainable practices. Through partnerships and donations, we support communities in establishing proper waste management systems.</p>
            <ul style="margin-top: 1rem; padding-left: 1.5rem;">
                <li>Waste bin donations to schools</li>
                <li>Partnership development</li>
                <li>Policy advocacy</li>
                <li>Research and innovation</li>
            </ul>
        </div>
        
        <div class="service-card fade-in" style="background: var(--light-grey);">
            <h3>Want to Learn More?</h3>
            <p>Contact us to discuss how our solutions can benefit your community or organization.</p>
            <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-primary" style="margin-top: 1rem;">Get In Touch</a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
