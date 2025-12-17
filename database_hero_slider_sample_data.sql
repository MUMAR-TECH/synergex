-- ============================================================================
-- SYNERGEX SOLUTIONS - HERO SLIDER SAMPLE DATA
-- This script populates the hero_slider table with sample slides
-- Run this AFTER running database_hero_slider.sql
-- ============================================================================

USE synergex_db;

-- ============================================================================
-- HERO SLIDER (6 entries)
-- ============================================================================
INSERT INTO hero_slider (title, subtitle, image, button_text, button_link, display_order, is_active) VALUES
(
    'Turning Waste Into Sustainable Value',
    'Join us in creating a cleaner, greener Zambia through innovative recycling solutions and eco-friendly products',
    'hero_slide_1.jpg',
    'Get Started',
    '/products.php',
    1,
    1
),
(
    'Eco-Friendly Building Materials',
    'Durable pavers and tiles made from 100% recycled plastic waste. Transform your spaces while protecting the environment.',
    'hero_slide_2.jpg',
    'View Products',
    '/products.php',
    2,
    1
),
(
    'Community Impact & Engagement',
    'We\'ve recycled over 20,000 kg of plastic, produced 40,000+ eco-pavers, and engaged 450+ youths in sustainable programs',
    'hero_slide_3.jpg',
    'Our Impact',
    '/achievements.php',
    3,
    1
),
(
    'Partnership for a Sustainable Future',
    'Working with communities, institutions, and organizations across Zambia to promote waste management and recycling',
    'hero_slide_4.jpg',
    'Partner With Us',
    '/contact.php',
    4,
    1
),
(
    'Innovation in Waste Management',
    'Advanced recycling technology transforming plastic waste into valuable resources for a circular economy',
    'hero_slide_5.jpg',
    'Learn More',
    '/what-we-do.php',
    5,
    1
),
(
    'Youth Empowerment & Skills Development',
    'Creating employment opportunities and training programs for young people in sustainable waste management',
    'hero_slide_6.jpg',
    'Join Our Mission',
    '/contact.php',
    6,
    1
);

-- ============================================================================
-- VERIFICATION QUERIES
-- ============================================================================
-- Run these queries to verify the data was inserted correctly

-- SELECT '=== HERO SLIDER ===' as info;
-- SELECT COUNT(*) as total_slides FROM hero_slider;
-- SELECT id, title, display_order, is_active FROM hero_slider ORDER BY display_order;

-- ============================================================================
-- NOTES
-- ============================================================================
-- 1. Images: The image filenames (hero_slide_1.jpg, etc.) are placeholders.
--    You'll need to upload actual hero slider images through the admin panel
--    at admin/hero-slider.php. Recommended image size: 1920x1080px or similar
--    wide format for best display.
--
-- 2. Display Order: Slides are ordered by display_order (1-6). You can adjust
--    the order through the admin panel.
--
-- 3. Active Status: All slides are set to active (is_active = 1). You can
--    deactivate any slide through the admin panel without deleting it.
--
-- 4. Button Links: All links are relative paths. Make sure they match your
--    actual page URLs:
--    - /products.php
--    - /achievements.php
--    - /contact.php
--    - /what-we-do.php
--
-- 5. To add more slides or modify existing ones, use the admin panel at:
--    http://localhost/synergex/admin/hero-slider.php

