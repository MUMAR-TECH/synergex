-- ============================================================================
-- SYNERGEX SOLUTIONS - SAMPLE DATA POPULATION
-- This script populates the database with sample data for testing/demo
-- Run this AFTER running database.sql
-- ============================================================================

USE synergex_db;

-- ============================================================================
-- IMPACT STATISTICS (Additional entries)
-- ============================================================================
-- Note: impact_stats typically has only one active record, but we can add historical entries
INSERT INTO impact_stats (plastic_recycled, eco_pavers_produced, institutions_served, youths_engaged, updated_at) VALUES
(7500, 15000, 35, 200, '2024-06-01 10:00:00'),
(10000, 20000, 45, 250, '2024-09-01 10:00:00'),
(12500, 25000, 55, 300, '2024-12-01 10:00:00'),
(15000, 30000, 65, 350, '2025-03-01 10:00:00'),
(17500, 35000, 75, 400, '2025-06-01 10:00:00'),
(20000, 40000, 85, 450, '2025-09-01 10:00:00');

-- ============================================================================
-- PRODUCTS (Additional entries to make 6 total)
-- ============================================================================
INSERT INTO products (name, description, price, unit, features, is_active) VALUES
('Premium Eco-Pavers', 'High-grade pavers with enhanced durability and weather resistance. Perfect for commercial and residential projects.', 45.00, 'per unit', 'Premium Quality|Extra Durable|Weather Resistant|Commercial Grade|Long Warranty', 1),
('Standard Eco-Tiles', 'Affordable eco-friendly tiles suitable for indoor applications. Available in multiple colors and patterns.', 28.00, 'per unit', 'Affordable|Indoor Use|Multiple Colors|Easy Installation|Eco-Friendly', 1),
('Garden Border Pavers', 'Specialty pavers designed for garden borders and landscaping. Adds aesthetic appeal to outdoor spaces.', 22.00, 'per unit', 'Garden Design|Decorative|Easy Install|Weather Resistant|Affordable', 1);

-- ============================================================================
-- GALLERY (6 entries)
-- ============================================================================
INSERT INTO gallery (title, category, image, caption, display_order) VALUES
('Waste Collection at Kitwe Market', 'waste_collection', 'gallery_waste_collection_1.jpg', 'Our team collecting plastic waste from local markets in Kitwe', 1),
('Community Clean-up Drive', 'waste_collection', 'gallery_waste_collection_2.jpg', 'Volunteers participating in community waste collection initiative', 2),
('Plastic Sorting Facility', 'recycling', 'gallery_recycling_1.jpg', 'State-of-the-art sorting facility for plastic waste processing', 3),
('Recycling Process Overview', 'recycling', 'gallery_recycling_2.jpg', 'Advanced machinery transforming waste into reusable materials', 4),
('Eco-Paver Production Line', 'production', 'gallery_production_1.jpg', 'Automated production line manufacturing eco-friendly pavers', 5),
('Quality Control Testing', 'production', 'gallery_production_2.jpg', 'Rigorous quality testing ensures product durability and standards', 6),
('School Installation Project', 'installation', 'gallery_installation_1.jpg', 'Installing eco-pavers at Central Primary School playground', 7),
('Residential Driveway Project', 'installation', 'gallery_installation_2.jpg', 'Completed residential driveway using our eco-pavers', 8),
('Youth Training Workshop', 'community', 'gallery_community_1.jpg', 'Empowering youth through recycling and sustainability workshops', 9),
('School Education Program', 'community', 'gallery_community_2.jpg', 'Teaching students about waste management and recycling', 10),
('Community Partnership Event', 'community', 'gallery_community_3.jpg', 'Collaborating with local communities for sustainable waste management', 11),
('Awareness Campaign Launch', 'community', 'gallery_community_4.jpg', 'Launching public awareness campaign on plastic waste reduction', 12);

-- ============================================================================
-- ACHIEVEMENTS (Additional entries to make 6 total)
-- ============================================================================
INSERT INTO achievements (year, title, description, category, display_order) VALUES
(2025, 'National Environmental Award', 'Recognized by the Environmental Management Agency for outstanding contribution to waste reduction and recycling in Zambia.', 'recognition', 5),
(2025, 'Featured in Zambia Daily News', 'Our innovative recycling approach was featured in a major national newspaper, raising awareness about sustainable waste management.', 'media', 6);

-- ============================================================================
-- QUOTE REQUESTS (6 entries)
-- ============================================================================
INSERT INTO quote_requests (name, email, phone, product_id, area, include_installation, message, status, created_at) VALUES
('John Mwansa', 'john.mwansa@email.com', '0977123456', 1, 50.00, 1, 'I need eco-pavers for my residential driveway. Please provide a quote with installation included.', 'pending', '2025-01-15 09:30:00'),
('Sarah Chanda', 'sarah.chanda@email.com', '0966789012', 2, 120.00, 1, 'Looking to install eco-tiles for our office building. Need professional installation service.', 'responded', '2025-01-20 14:15:00'),
('David Banda', 'david.banda@email.com', '0955123456', 3, 75.50, 0, 'Interested in custom pavers for a commercial project. Please send detailed pricing information.', 'pending', '2025-02-01 11:00:00'),
('Mary Tembo', 'mary.tembo@email.com', '0978456789', 1, 200.00, 1, 'Large-scale project for a housing development. Need bulk pricing and installation quote.', 'completed', '2025-02-10 16:45:00'),
('Peter Mulenga', 'peter.mulenga@email.com', '0966234567', 4, 35.00, 1, 'Small garden project. Need premium pavers with installation for my backyard.', 'pending', '2025-02-18 10:20:00'),
('Grace Mwale', 'grace.mwale@email.com', '0955345678', 2, 90.00, 0, 'School project - need eco-tiles for our new classroom building. Budget-conscious pricing preferred.', 'responded', '2025-02-25 13:30:00');

-- ============================================================================
-- CONTACT MESSAGES (6 entries)
-- ============================================================================
INSERT INTO contact_messages (name, email, subject, message, status, created_at) VALUES
('Michael Phiri', 'michael.phiri@email.com', 'Partnership Inquiry', 'Hello, I represent a local NGO and we are interested in partnering with Synergex Solutions for community waste management programs. Please contact us to discuss collaboration opportunities.', 'unread', '2025-01-12 08:15:00'),
('Patricia Ngoma', 'patricia.ngoma@email.com', 'Product Information Request', 'I would like to know more about your eco-pavers. Are they suitable for high-traffic areas? What is the expected lifespan?', 'read', '2025-01-18 14:30:00'),
('James Mwanza', 'james.mwanza@email.com', 'Volunteer Opportunity', 'I am interested in volunteering with your organization. How can I get involved in your community programs?', 'responded', '2025-01-25 09:45:00'),
('Ruth Kunda', 'ruth.kunda@email.com', 'Bulk Order Inquiry', 'We are a construction company and would like to place a large order for eco-pavers. Can you provide bulk pricing and delivery options?', 'unread', '2025-02-05 11:20:00'),
('Andrew Sinkala', 'andrew.sinkala@email.com', 'Educational Visit Request', 'Our school would like to organize an educational visit to your facility. Can we schedule a tour for our students?', 'read', '2025-02-14 15:00:00'),
('Esther Mbewe', 'esther.mbewe@email.com', 'Waste Collection Service', 'Our business generates significant plastic waste. Do you offer waste collection services for commercial establishments?', 'unread', '2025-02-22 10:10:00');

-- ============================================================================
-- NEWSLETTER SUBSCRIBERS (6 entries)
-- ============================================================================
INSERT INTO subscribers (email, name, status, subscribed_at) VALUES
('subscriber1@email.com', 'Alice Mwila', 'active', '2024-12-01 10:00:00'),
('subscriber2@email.com', 'Brian Chisanga', 'active', '2024-12-15 14:30:00'),
('subscriber3@email.com', 'Catherine Lungu', 'active', '2025-01-05 09:15:00'),
('subscriber4@email.com', 'Daniel Mumba', 'active', '2025-01-20 16:45:00'),
('subscriber5@email.com', 'Faith Mwale', 'unsubscribed', '2025-02-01 11:20:00'),
('subscriber6@email.com', 'George Phiri', 'active', '2025-02-15 13:00:00');

-- ============================================================================
-- PARTNERS (Additional entries to make 6 total)
-- ============================================================================
INSERT INTO partners (name, logo, website, display_order, is_active) VALUES
('Zambia Environmental Agency', 'partner_zea.png', 'https://www.zea.gov.zm', 2, 1),
('Copperbelt University', 'partner_cbu.png', 'https://www.cbu.ac.zm', 3, 1),
('Green Earth Foundation', 'partner_green_earth.png', 'https://www.greenearth.org.zm', 4, 1),
('Zambia Waste Management Association', 'partner_zwma.png', 'https://www.zwma.org.zm', 5, 1),
('National Youth Development Council', 'partner_nydc.png', 'https://www.nydc.gov.zm', 6, 1);

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
-- PAGE CONTENT (Additional entries)
-- ============================================================================
INSERT INTO page_content (page_name, section_name, content) VALUES
('home', 'hero_title', 'Transforming Waste Into Sustainable Value'),
('home', 'hero_subtitle', 'Join us in creating a cleaner, greener Zambia through innovative recycling solutions'),
('about', 'our_story', 'Synergex Solutions was founded in 2023 by a group of passionate young entrepreneurs who saw the growing plastic waste crisis in Zambia and decided to take action. Starting with a small pilot project in Kitwe, we have grown into a recognized leader in sustainable waste management.'),
('about', 'our_team', 'Our team consists of dedicated professionals from diverse backgrounds including environmental science, engineering, business, and community development. Together, we work towards our shared vision of a waste-free Zambia.'),
('what-we-do', 'impact', 'Since our inception, we have recycled over 20,000 kg of plastic waste, produced more than 40,000 eco-pavers, served 85+ institutions, and engaged over 450 youths in our programs.'),
('what-we-do', 'technology', 'We use state-of-the-art recycling technology and processes to ensure maximum efficiency and quality in our production. Our facilities are equipped with modern machinery for sorting, processing, and manufacturing.'),
('vision-sdgs', 'mission_statement', 'To transform plastic waste into valuable resources while creating employment opportunities and promoting environmental sustainability across Zambia.'),
('vision-sdgs', 'future_goals', 'By 2030, we aim to expand our operations to all 10 provinces of Zambia, establish 50+ collection points, and engage 10,000+ youths in our programs.'),
('products', 'why_choose_us', 'Our products are made from 100% recycled materials, are highly durable, weather-resistant, and contribute to environmental conservation. Every purchase helps reduce plastic waste in our communities.'),
('contact', 'office_hours', 'Monday - Friday: 8:00 AM - 5:00 PM<br>Saturday: 9:00 AM - 1:00 PM<br>Sunday: Closed');

-- ============================================================================
-- VERIFICATION QUERIES
-- ============================================================================
-- Run these queries to verify the data was inserted correctly

-- SELECT '=== PRODUCTS ===' as info;
-- SELECT COUNT(*) as total_products FROM products;
-- SELECT id, name, price FROM products;

-- SELECT '=== GALLERY ===' as info;
-- SELECT COUNT(*) as total_gallery_images FROM gallery;
-- SELECT id, title, category FROM gallery;

-- SELECT '=== ACHIEVEMENTS ===' as info;
-- SELECT COUNT(*) as total_achievements FROM achievements;
-- SELECT id, year, title, category FROM achievements;

-- SELECT '=== QUOTE REQUESTS ===' as info;
-- SELECT COUNT(*) as total_quotes FROM quote_requests;
-- SELECT id, name, email, status FROM quote_requests;

-- SELECT '=== CONTACT MESSAGES ===' as info;
-- SELECT COUNT(*) as total_messages FROM contact_messages;
-- SELECT id, name, email, status FROM contact_messages;

-- SELECT '=== SUBSCRIBERS ===' as info;
-- SELECT COUNT(*) as total_subscribers FROM subscribers;
-- SELECT id, email, name, status FROM subscribers;

-- SELECT '=== PARTNERS ===' as info;
-- SELECT COUNT(*) as total_partners FROM partners;
-- SELECT id, name, is_active FROM partners;

-- SELECT '=== PAGE CONTENT ===' as info;
-- SELECT COUNT(*) as total_content_sections FROM page_content;
-- SELECT page_name, section_name FROM page_content;

-- SELECT '=== HERO SLIDER ===' as info;
-- SELECT COUNT(*) as total_slides FROM hero_slider;
-- SELECT id, title, display_order, is_active FROM hero_slider ORDER BY display_order;

-- ============================================================================
-- NOTES
-- ============================================================================
-- 1. Gallery images: The image filenames are placeholders. You'll need to upload
--    actual images through the admin panel and they will be stored in 
--    assets/images/uploads/ directory.
--
-- 2. Partner logos: Similar to gallery images, partner logos need to be uploaded
--    through the admin panel.
--
-- 3. Dates: All dates are set to recent dates (2025) for realistic demo data.
--
-- 4. Email addresses: All email addresses are example addresses. Replace with
--    real ones if needed for testing.
--
-- 5. Phone numbers: Phone numbers follow Zambian format (09XXXXXXXX).
--
-- 6. To verify data insertion, uncomment and run the verification queries above.

