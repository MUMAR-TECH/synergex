<?php
$email = getSetting('email', 'synergexsolutions25@gmail.com');
$phone = getSetting('phone', '0770377471');
$whatsapp = getSetting('whatsapp', '260770377471');
?>
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>About Synergex</h3>
                <p>Turning waste into sustainable value through innovative recycling solutions and community engagement.</p>
            </div>
            
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="<?php echo SITE_URL; ?>/about.php">About Us</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/products.php">Products</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/achievements.php">Achievements</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/contact.php">Contact Us</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Contact Us</h3>
                <p>Email: <a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></p>
                <p>Phone: <a href="tel:<?php echo $phone; ?>"><?php echo $phone; ?></a></p>
                <p>WhatsApp: <a href="https://wa.me/<?php echo $whatsapp; ?>"><?php echo $whatsapp; ?></a></p>
            </div>
            
            <div class="footer-section">
                <h3>Newsletter</h3>
                <p>Subscribe for updates on our impact and initiatives.</p>
                <form id="newsletterForm" class="newsletter-form">
                    <input type="email" name="email" placeholder="Your email" required>
                    <button type="submit" class="btn btn-primary">Subscribe</button>
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
        <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
            <path d="M16 0c-8.837 0-16 7.163-16 16 0 2.825 0.737 5.607 2.137 8.048l-2.137 7.952 7.933-2.127c2.42 1.37 5.173 2.127 8.067 2.127 8.837 0 16-7.163 16-16s-7.163-16-16-16zM16 29.467c-2.482 0-4.908-0.646-7.07-1.87l-0.507-0.292-5.293 1.424 1.424-5.293-0.292-0.507c-1.225-2.163-1.87-4.588-1.87-7.070 0-7.444 6.056-13.5 13.5-13.5s13.5 6.056 13.5 13.5-6.056 13.5-13.5 13.5zM21.984 18.516c-0.396-0.198-2.344-1.156-2.708-1.29-0.364-0.132-0.628-0.198-0.892 0.198s-1.026 1.29-1.258 1.554c-0.231 0.264-0.462 0.297-0.858 0.099s-1.674-0.617-3.188-1.966c-1.179-1.053-1.975-2.35-2.206-2.746s-0.025-0.608 0.174-0.805c0.178-0.178 0.396-0.462 0.594-0.693 0.198-0.231 0.264-0.396 0.396-0.66 0.132-0.264 0.066-0.495-0.033-0.693s-0.892-2.146-1.224-2.938c-0.324-0.792-0.651-0.66-0.892-0.66-0.231 0-0.495-0.033-0.759-0.033s-0.693 0.099-1.057 0.495c-0.364 0.396-1.388 1.353-1.388 3.301s1.421 3.831 1.619 4.095c0.198 0.264 2.771 4.229 6.714 5.933 0.94 0.396 1.674 0.628 2.245 0.792 0.951 0.297 1.815 0.231 2.5 0.132 0.759-0.099 2.344-0.957 2.675-1.881s0.331-1.716 0.231-1.881c-0.099-0.198-0.364-0.297-0.759-0.495z" fill="#fff"/>
        </svg>
    </a>
    
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
</body>
</html>
<?php
// Close any open database connections
$db = null;
?>