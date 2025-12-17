-- ============================================================================
-- HERO SLIDER TABLE - Database Migration
-- Run this SQL to add the hero slider functionality
-- ============================================================================

USE synergex_db;

-- ============================================================================
-- HERO SLIDER TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS hero_slider (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    subtitle TEXT,
    image VARCHAR(255) NOT NULL,
    button_text VARCHAR(100),
    button_link VARCHAR(255),
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_active (is_active),
    INDEX idx_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- SAMPLE DATA (Optional - Remove if you don't want sample slides)
-- ============================================================================
-- Note: You'll need to upload actual images through the admin panel
-- These are just placeholder entries

