<?php
// ============================================================================
// FILE: contact.php - Contact Page
// ============================================================================
require_once 'includes/header.php';
$email = getSetting('email', 'synergexsolutions25@gmail.com');
$phone = getSetting('phone', '0770377471');
?>

<section class="page-header">
    <div class="container">
        <h1>Contact Us</h1>
        <p>Get in touch with us for quotes, partnerships, or inquiries</p>
    </div>
</section>

<section class="container">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 3rem;">
        <div class="fade-in">
            <h2>Get In Touch</h2>
            <p style="margin-bottom: 2rem;">We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
            
            <div class="contact-info">
                <div style="margin-bottom: 1.5rem;">
                    <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem;"><i class="fas fa-envelope"></i> Email</h3>
                    <a href="mailto:<?php echo $email; ?>" style="color: var(--primary-orange);"><?php echo $email; ?></a>
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem;"><i class="fas fa-phone"></i> Phone</h3>
                    <a href="tel:<?php echo $phone; ?>" style="color: var(--primary-orange);"><?php echo $phone; ?></a>
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem;"><i class="fab fa-whatsapp"></i> WhatsApp</h3>
                    <a href="https://wa.me/<?php echo getSetting('whatsapp', '260770377471'); ?>" 
                       style="color: var(--primary-orange);" target="_blank">
                        Chat with us on WhatsApp
                    </a>
                </div>
            </div>
        </div>
        
        <div class="fade-in">
            <form id="contactForm" class="contact-form" style="margin: 0;">
                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject">
                </div>
                
                <div class="form-group">
                    <label for="message">Message *</label>
                    <textarea id="message" name="message" required></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;"><i class="fas fa-paper-plane"></i> Send Message</button>
            </form>
        </div>
    </div>
</section>

<section style="background: var(--light-grey); padding: 3rem 2rem; text-align: center;">
    <div class="container">
        <h2><i class="fas fa-map-marker-alt"></i> Visit Our Office</h2>
        <p style="font-size: 1.1rem; margin-top: 1rem;">Kitwe, Copperbelt Province, Zambia</p>
        <p>We're always happy to welcome visitors. Please contact us to schedule a visit.</p>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>