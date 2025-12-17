-- ============================================================================
-- SYNERGEX SOLUTIONS - DATABASE SCHEMA
-- Database: synergex_db
-- ============================================================================

CREATE DATABASE IF NOT EXISTS synergex_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE synergex_db;

-- ============================================================================
-- ADMIN USERS TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default admin user (password: Admin@123)
INSERT INTO admin_users (email, password, name) VALUES 
('admin@synergex.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User');

-- ============================================================================
-- SITE SETTINGS TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings
INSERT INTO site_settings (setting_key, setting_value) VALUES
('site_name', 'Synergex Solutions'),
('tagline', 'Turning Waste Into Sustainable Value'),
('email', 'synergexsolutions25@gmail.com'),
('phone', '0770377471'),
('whatsapp', '260770377471'),
('mission', 'Creating sustainable value from waste through innovation and community engagement'),
('vision', 'A cleaner, greener Zambia where waste is transformed into valuable resources'),
('address', 'Kitwe, Copperbelt Province, Zambia');

-- ============================================================================
-- IMPACT STATISTICS TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS impact_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plastic_recycled INT DEFAULT 0 COMMENT 'Kilograms of plastic recycled',
    eco_pavers_produced INT DEFAULT 0 COMMENT 'Number of eco-pavers produced',
    institutions_served INT DEFAULT 0 COMMENT 'Institutions/communities served',
    youths_engaged INT DEFAULT 0 COMMENT 'Youth engaged in programs',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default impact statistics
INSERT INTO impact_stats (plastic_recycled, eco_pavers_produced, institutions_served, youths_engaged) 
VALUES (5000, 10000, 25, 150);

-- ============================================================================
-- PRODUCTS TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    unit VARCHAR(50) DEFAULT 'per unit',
    features TEXT COMMENT 'Pipe-separated list: Feature1|Feature2|Feature3',
    image VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample products
INSERT INTO products (name, description, price, unit, features, is_active) VALUES
('Eco-Friendly Pavers', 'Durable pavers made from 100% recycled plastic waste. Perfect for walkways, driveways, and outdoor spaces.', 25.00, 'per unit', 'Durable|Water-resistant|Eco-friendly|UV Protected|Low Maintenance', 1),
('Eco-Friendly Tiles', 'High-quality tiles manufactured from recycled materials. Ideal for indoor and outdoor flooring.', 30.00, 'per unit', 'Durable|Water-resistant|Eco-friendly|Easy Installation|Slip Resistant', 1),
('Custom Pavers', 'Custom-designed pavers for specific project requirements. Available in various sizes and colors.', 35.00, 'per unit', 'Custom Design|Durable|Eco-friendly|Multiple Sizes|Color Options', 1);

-- ============================================================================
-- GALLERY TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(100) COMMENT 'waste_collection, recycling, production, installation, community',
    image VARCHAR(255) NOT NULL,
    caption TEXT,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- ACHIEVEMENTS TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS achievements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    year INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    category VARCHAR(100) COMMENT 'grant, pilot, recognition, partnership, media',
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_year (year),
    INDEX idx_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample achievements
INSERT INTO achievements (year, title, description, category, display_order) VALUES
(2024, 'Innovation Grant Award', 'Received funding from the National Innovation Fund to scale our recycling operations and expand production capacity.', 'grant', 1),
(2024, 'Partnership with Kitwe City Council', 'Established official partnership for waste management and recycling initiatives across the city.', 'partnership', 2),
(2025, 'Pilot Project at Central School', 'Successfully completed installation of eco-pavers at Central School, creating sustainable pathways for students.', 'pilot', 3),
(2025, 'Youth Empowerment Recognition', 'Recognized by the Ministry of Youth for outstanding contribution to youth employment and skills development.', 'recognition', 4);

-- ============================================================================
-- QUOTE REQUESTS TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS quote_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    product_id INT,
    area DECIMAL(10,2) COMMENT 'Area in square meters',
    include_installation TINYINT(1) DEFAULT 0,
    message TEXT,
    status VARCHAR(50) DEFAULT 'pending' COMMENT 'pending, responded, completed, cancelled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- CONTACT MESSAGES TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(255),
    message TEXT NOT NULL,
    status VARCHAR(50) DEFAULT 'unread' COMMENT 'unread, read, responded',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- NEWSLETTER SUBSCRIBERS TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(255),
    status VARCHAR(50) DEFAULT 'active' COMMENT 'active, unsubscribed',
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- PARTNERS TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS partners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    logo VARCHAR(255),
    website VARCHAR(255),
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_active (is_active),
    INDEX idx_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample partner
INSERT INTO partners (name, display_order, is_active) VALUES
('Kitwe City Council', 1, 1);

-- ============================================================================
-- PAGE CONTENT TABLE (for dynamic content management)
-- ============================================================================
CREATE TABLE IF NOT EXISTS page_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_name VARCHAR(100) NOT NULL COMMENT 'home, about, what-we-do, vision-sdgs',
    section_name VARCHAR(100) NOT NULL COMMENT 'hero, mission, values, approach, etc.',
    content TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY page_section (page_name, section_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default page content
INSERT INTO page_content (page_name, section_name, content) VALUES
('about', 'who_we_are', 'Synergex Solutions is a sustainability-driven enterprise dedicated to addressing plastic waste pollution through innovative recycling and production of eco-friendly building materials.'),
('about', 'founder_note', 'Founded by a team of young innovators passionate about sustainability and community development.'),
('vision-sdgs', 'vision', 'A cleaner, greener Zambia where waste is transformed into valuable resources.'),
('vision-sdgs', 'approach', 'Sustainability First|Innovation|Community Engagement|Scalable Impact'),
('what-we-do', 'waste_management', 'Comprehensive waste collection and sorting services for communities and institutions across Zambia.'),
('what-we-do', 'recycling', 'Advanced recycling processes that transform plastic waste into valuable raw materials for production.'),
('what-we-do', 'production', 'Manufacturing durable, water-resistant pavers and tiles made from 100% recycled plastic materials.'),
('what-we-do', 'community', 'Educational programs and awareness campaigns promoting sustainability and waste management.'),
('what-we-do', 'sustainability', 'Promoting circular economy principles and donating waste bins to communities and schools.');

-- ============================================================================
-- VIEWS FOR REPORTING
-- ============================================================================

-- View for dashboard statistics
CREATE OR REPLACE VIEW dashboard_stats AS
SELECT 
    (SELECT COUNT(*) FROM products) as total_products,
    (SELECT COUNT(*) FROM products WHERE is_active = 1) as active_products,
    (SELECT COUNT(*) FROM quote_requests) as total_quotes,
    (SELECT COUNT(*) FROM quote_requests WHERE status = 'pending') as pending_quotes,
    (SELECT COUNT(*) FROM contact_messages) as total_messages,
    (SELECT COUNT(*) FROM contact_messages WHERE status = 'unread') as unread_messages,
    (SELECT COUNT(*) FROM subscribers WHERE status = 'active') as active_subscribers,
    (SELECT COUNT(*) FROM gallery) as total_gallery_images,
    (SELECT COUNT(*) FROM achievements) as total_achievements;

-- View for recent activity
CREATE OR REPLACE VIEW recent_activity AS
SELECT 
    'quote' as type,
    id,
    name,
    email,
    created_at
FROM quote_requests
UNION ALL
SELECT 
    'message' as type,
    id,
    name,
    email,
    created_at
FROM contact_messages
UNION ALL
SELECT 
    'subscriber' as type,
    id,
    name,
    email,
    subscribed_at as created_at
FROM subscribers
ORDER BY created_at DESC
LIMIT 10;

-- ============================================================================
-- STORED PROCEDURES
-- ============================================================================

-- Procedure to update impact statistics
DELIMITER //
CREATE PROCEDURE update_impact_stats(
    IN p_plastic INT,
    IN p_pavers INT,
    IN p_institutions INT,
    IN p_youths INT
)
BEGIN
    UPDATE impact_stats 
    SET 
        plastic_recycled = p_plastic,
        eco_pavers_produced = p_pavers,
        institutions_served = p_institutions,
        youths_engaged = p_youths,
        updated_at = NOW()
    WHERE id = 1;
END //
DELIMITER ;

-- Procedure to archive old data (optional)
DELIMITER //
CREATE PROCEDURE archive_old_quotes()
BEGIN
    -- Archive quotes older than 1 year
    CREATE TABLE IF NOT EXISTS quote_requests_archive LIKE quote_requests;
    
    INSERT INTO quote_requests_archive
    SELECT * FROM quote_requests 
    WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR);
    
    DELETE FROM quote_requests 
    WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR);
END //
DELIMITER ;

-- ============================================================================
-- TRIGGERS
-- ============================================================================

-- Trigger to log product changes
CREATE TABLE IF NOT EXISTS product_audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    action VARCHAR(50),
    old_data JSON,
    new_data JSON,
    changed_by INT,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- INDEXES FOR PERFORMANCE
-- ============================================================================

-- Additional indexes for better query performance
ALTER TABLE quote_requests ADD INDEX idx_email (email);
ALTER TABLE contact_messages ADD INDEX idx_email (email);
ALTER TABLE products ADD INDEX idx_price (price);
ALTER TABLE achievements ADD INDEX idx_category (category);
ALTER TABLE gallery ADD INDEX idx_created (created_at);

-- ============================================================================
-- GRANT PERMISSIONS (adjust as needed)
-- ============================================================================

-- If you want to create a specific database user (uncomment and modify):
-- CREATE USER 'synergex_user'@'localhost' IDENTIFIED BY 'your_secure_password';
-- GRANT ALL PRIVILEGES ON synergex_db.* TO 'synergex_user'@'localhost';
-- FLUSH PRIVILEGES;

-- ============================================================================
-- DATA VERIFICATION QUERIES
-- ============================================================================

-- Verify installation
SELECT 'Database created successfully' as status;
SELECT COUNT(*) as admin_users FROM admin_users;
SELECT COUNT(*) as products FROM products;
SELECT COUNT(*) as settings FROM site_settings;
SELECT * FROM impact_stats;

-- ============================================================================
-- BACKUP RECOMMENDATION
-- ============================================================================

/*
Regular backup command (run from command line):
mysqldump -u root -p synergex_db > synergex_backup_$(date +%Y%m%d).sql

Restore command:
mysql -u root -p synergex_db < synergex_backup_YYYYMMDD.sql
*/