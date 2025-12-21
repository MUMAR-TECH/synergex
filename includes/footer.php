<?php
$email = getSetting('email', 'synergexsolutions25@gmail.com');
$phone = getSetting('phone', '0770377471');
$whatsapp = getSetting('whatsapp', '260770377471');
?>
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3><i class="fas fa-leaf"></i> About Synergex</h3>
                <p>Turning waste into sustainable value through innovative recycling solutions and community engagement.</p>
            </div>
            
            <div class="footer-section">
                <h3><i class="fas fa-link"></i> Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="<?php echo SITE_URL; ?>/about.php"><i class="fas fa-chevron-right"></i> About Us</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/products.php"><i class="fas fa-chevron-right"></i> Products</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/achievements.php"><i class="fas fa-chevron-right"></i> Achievements</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/contact.php"><i class="fas fa-chevron-right"></i> Contact Us</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3><i class="fas fa-address-book"></i> Contact Us</h3>
                <p><i class="fas fa-envelope"></i> Email: <a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></p>
                <p><i class="fas fa-phone"></i> Phone: <a href="tel:<?php echo $phone; ?>"><?php echo $phone; ?></a></p>
                <p><i class="fab fa-whatsapp"></i> WhatsApp: <a href="https://wa.me/<?php echo $whatsapp; ?>"><?php echo $whatsapp; ?></a></p>
            </div>
            
            <div class="footer-section">
                <h3><i class="fas fa-envelope-open-text"></i> Newsletter</h3>
                <p>Subscribe for updates on our impact and initiatives.</p>
                <form id="newsletterForm" class="newsletter-form">
                    <input type="email" name="email" placeholder="Your email" required>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Subscribe</button>
                </form>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <?php echo $siteName; ?>. All rights reserved.</p>
        </div>
    </footer>
    
    <!-- WhatsApp Float Button -->
    <a href="https://wa.me/<?php echo $whatsapp; ?>?text=<?php echo urlencode(WHATSAPP_MESSAGE); ?>" 
       class="whatsapp-float" target="_blank" title="Chat on WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>
    
    <!-- AI Chatbot Styles and Script -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/chatbot.css">
    <script>
        // Chatbot configuration
        window.CHATBOT_CONFIG = {
            apiUrl: '<?php echo SITE_URL; ?>/api/chatbot.php',
            siteUrl: '<?php echo SITE_URL; ?>'
        };
    </script>
    <script src="<?php echo SITE_URL; ?>/assets/js/chatbot.js"></script>
    
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
</body>
</html>
<?php
// Close any open database connections
$db = null;
?>