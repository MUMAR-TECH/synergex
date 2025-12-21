-- ============================================================================
-- CHATBOT DATABASE SCHEMA
-- ============================================================================
-- Add chatbot tables to the existing synergex_db database

-- Chatbot conversations table
CREATE TABLE IF NOT EXISTS chatbot_conversations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(255) NOT NULL,
    visitor_name VARCHAR(100) DEFAULT NULL,
    visitor_email VARCHAR(100) DEFAULT NULL,
    visitor_phone VARCHAR(20) DEFAULT NULL,
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('active', 'resolved', 'closed') DEFAULT 'active',
    INDEX idx_session (session_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Chatbot messages table
CREATE TABLE IF NOT EXISTS chatbot_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT NOT NULL,
    message TEXT NOT NULL,
    sender ENUM('user', 'bot', 'admin') DEFAULT 'user',
    intent VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conversation_id) REFERENCES chatbot_conversations(id) ON DELETE CASCADE,
    INDEX idx_conversation (conversation_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Chatbot knowledge base table
CREATE TABLE IF NOT EXISTS chatbot_knowledge (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    category VARCHAR(50) DEFAULT 'general',
    keywords TEXT,
    priority INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default knowledge base
INSERT INTO chatbot_knowledge (question, answer, category, keywords, priority) VALUES
('What is Synergex Solutions?', 
'Synergex Solutions is a sustainability-driven enterprise in Zambia dedicated to addressing plastic waste pollution through innovative recycling. We transform plastic waste into durable eco-friendly pavers and tiles, creating value while protecting our environment.', 
'company', 'about,company,synergex,who,what,business', 100),

('What products do you offer?', 
'We specialize in eco-friendly building materials made from 100% recycled plastic, including: Eco-Pavers (durable interlocking pavers for driveways, walkways, and patios), Plastic Tiles (versatile tiles for various applications), and Custom Solutions (tailored products for specific needs). All our products are durable, weather-resistant, and environmentally friendly.', 
'products', 'products,pavers,tiles,eco,offer,sell,buy', 90),

('How do I get a quote?', 
'Getting a quote is easy! You can: 1) Fill out our quote form on the products page, 2) Contact us directly through our contact page, 3) WhatsApp us at +260 770 377471, or 4) Visit our office in Lusaka, Zambia. We''ll respond within 24 hours with a customized quote.', 
'sales', 'quote,price,cost,pricing,buy,purchase,order', 95),

('Where are you located?', 
'We are based in Lusaka, Zambia. You can reach us via WhatsApp at +260 770 377471 or through our contact form on the website. We serve clients across Zambia and are expanding regionally.', 
'contact', 'location,address,where,find,office,visit', 85),

('What is your mission?', 
'Our mission is to create sustainable value from waste through innovation and community engagement. We tackle Zambia''s plastic waste crisis while creating economic opportunities, especially for young people. We believe waste is not garbage—it''s a resource waiting to be transformed.', 
'company', 'mission,vision,goal,purpose,why', 80),

('How does plastic recycling work?', 
'Our recycling process involves: 1) Collection - We collect plastic waste from communities and institutions, 2) Sorting - Plastics are sorted by type and cleaned, 3) Processing - Plastics are shredded and melted, 4) Manufacturing - We mold the recycled plastic into pavers and tiles, 5) Quality Control - Every product is tested for durability and quality.', 
'process', 'recycling,process,how,work,manufacturing,production', 75),

('What are the benefits of eco-pavers?', 
'Our eco-pavers offer numerous benefits: Environmentally friendly (made from 100% recycled plastic), Durable and long-lasting (weather-resistant and strong), Cost-effective (competitive pricing), Low maintenance (easy to clean and maintain), Versatile (available in various colors and designs), and Supports sustainability (helps reduce plastic pollution).', 
'products', 'benefits,advantages,why,eco-pavers,features', 70),

('How do I place an order?', 
'To place an order: 1) Contact us for a quote using the quote form or WhatsApp, 2) Our team will provide pricing and product details, 3) Confirm your order specifications, 4) We''ll arrange production and delivery, 5) Payment terms will be discussed based on order size. We work with both individuals and institutions.', 
'sales', 'order,buy,purchase,ordering,place', 85),

('What is your environmental impact?', 
'We''ve made significant environmental impact: Thousands of kg of plastic recycled, Numerous eco-pavers produced, Multiple institutions served, and Many youths engaged in sustainable practices. Check our achievements page for detailed statistics and success stories.', 
'company', 'impact,environment,statistics,achievement,contribution', 75),

('Do you offer delivery?', 
'Yes, we offer delivery services across Zambia. Delivery terms, costs, and timelines depend on your location and order size. Contact us for specific delivery information for your area.', 
'sales', 'delivery,shipping,transport,logistics', 65),

('Can I visit your facility?', 
'Yes! We welcome visitors who want to see our recycling process and products. Please contact us in advance to schedule a visit. We also offer educational tours for schools and organizations interested in sustainability.', 
'contact', 'visit,tour,facility,see,location', 60),

('What sizes do pavers come in?', 
'Our eco-pavers come in various sizes to suit different applications. Standard sizes include interlocking pavers for pathways and driveways. We also offer custom sizes for specific projects. Contact us to discuss your requirements.', 
'products', 'size,dimensions,measurements,specifications', 60),

('How can I support your mission?', 
'You can support us by: 1) Purchasing our eco-friendly products, 2) Donating plastic waste for recycling, 3) Spreading awareness about plastic pollution, 4) Partnering with us for sustainability projects, 5) Following us on social media, 6) Subscribing to our newsletter for updates.', 
'company', 'support,help,partner,contribute,join', 55),

('What types of plastic do you recycle?', 
'We recycle various types of plastic waste, focusing on plastics suitable for manufacturing durable building materials. Our team can assess the plastic waste you have. Contact us for specific information about plastic types we accept.', 
'process', 'plastic,types,materials,recycle,accept', 65),

('Are your products durable?', 
'Absolutely! Our eco-pavers and tiles are designed for durability. They are: Weather-resistant (withstand rain, sun, and temperature changes), Strong and sturdy (can handle heavy foot traffic and vehicle loads), Long-lasting (maintain quality for many years), and Quality-tested (every product undergoes quality control).', 
'products', 'durable,quality,strong,lasting,reliable', 70);

-- Chatbot settings table
CREATE TABLE IF NOT EXISTS chatbot_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default chatbot settings
INSERT INTO chatbot_settings (setting_key, setting_value) VALUES
('chatbot_enabled', '1'),
('chatbot_name', 'Synergex Assistant'),
('chatbot_greeting', 'Hello! 👋 I''m here to help you learn about Synergex Solutions and our eco-friendly products. How can I assist you today?'),
('chatbot_color', '#27ae60'),
('chatbot_position', 'bottom-right'),
('offline_message', 'Thanks for your message! Our team will get back to you soon.'),
('working_hours', 'Monday - Friday: 8:00 AM - 5:00 PM CAT');
