-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 19, 2025 at 08:55 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `synergex_db`
--


--
-- Procedures

-- --------------------------------------------------------

--
-- Table structure for table `achievements`
--

CREATE TABLE `achievements` (
  `id` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL COMMENT 'grant, pilot, recognition, partnership, media',
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `achievements`
--

INSERT INTO `achievements` (`id`, `year`, `title`, `description`, `image`, `category`, `display_order`, `created_at`) VALUES
(1, 2024, 'Innovation Grant Award', 'Received funding from the National Innovation Fund to scale our recycling operations and expand production capacity.', NULL, 'grant', 1, '2025-12-17 15:50:11'),
(2, 2024, 'Partnership with Kitwe City Council', 'Established official partnership for waste management and recycling initiatives across the city.', NULL, 'partnership', 2, '2025-12-17 15:50:11'),
(3, 2025, 'Pilot Project at Central School', 'Successfully completed installation of eco-pavers at Central School, creating sustainable pathways for students.', NULL, 'pilot', 3, '2025-12-17 15:50:11'),
(4, 2025, 'Youth Empowerment Recognition', 'Recognized by the Ministry of Youth for outstanding contribution to youth employment and skills development.', NULL, 'recognition', 4, '2025-12-17 15:50:11'),
(5, 2025, 'National Environmental Award', 'Recognized by the Environmental Management Agency for outstanding contribution to waste reduction and recycling in Zambia.', NULL, 'recognition', 5, '2025-12-17 19:24:44'),
(6, 2025, 'Featured in Zambia Daily News', 'Our innovative recycling approach was featured in a major national newspaper, raising awareness about sustainable waste management.', NULL, 'media', 6, '2025-12-17 19:24:44'),
(7, 2025, 'National Environmental Award', 'Recognized by the Environmental Management Agency for outstanding contribution to waste reduction and recycling in Zambia.', NULL, 'recognition', 5, '2025-12-17 19:39:08'),
(8, 2025, 'Featured in Zambia Daily News', 'Our innovative recycling approach was featured in a major national newspaper, raising awareness about sustainable waste management.', NULL, 'media', 6, '2025-12-17 19:39:08');

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `email`, `password`, `name`, `created_at`) VALUES
(2, 'admin@synergex.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User', '2025-12-17 18:52:10'),
(3, 'mumarmukuka@gmail.com', '$2y$10$p3.4rD97eDkd/.xQOvJ2OOMWorTuOZTLcu0SR3O5YPwiu4papaNFe', 'MUMAR MUKUKA', '2025-12-17 19:14:50');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `status` varchar(50) DEFAULT 'unread' COMMENT 'unread, read, responded',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `status`, `created_at`) VALUES
(1, 'Michael Phiri', 'michael.phiri@email.com', 'Partnership Inquiry', 'Hello, I represent a local NGO and we are interested in partnering with Synergex Solutions for community waste management programs. Please contact us to discuss collaboration opportunities.', 'unread', '2025-01-12 06:15:00'),
(2, 'Patricia Ngoma', 'patricia.ngoma@email.com', 'Product Information Request', 'I would like to know more about your eco-pavers. Are they suitable for high-traffic areas? What is the expected lifespan?', 'read', '2025-01-18 12:30:00'),
(3, 'James Mwanza', 'james.mwanza@email.com', 'Volunteer Opportunity', 'I am interested in volunteering with your organization. How can I get involved in your community programs?', 'responded', '2025-01-25 07:45:00'),
(4, 'Ruth Kunda', 'ruth.kunda@email.com', 'Bulk Order Inquiry', 'We are a construction company and would like to place a large order for eco-pavers. Can you provide bulk pricing and delivery options?', 'unread', '2025-02-05 09:20:00'),
(5, 'Andrew Sinkala', 'andrew.sinkala@email.com', 'Educational Visit Request', 'Our school would like to organize an educational visit to your facility. Can we schedule a tour for our students?', 'read', '2025-02-14 13:00:00'),
(6, 'Esther Mbewe', 'esther.mbewe@email.com', 'Waste Collection Service', 'Our business generates significant plastic waste. Do you offer waste collection services for commercial establishments?', 'unread', '2025-02-22 08:10:00'),
(7, 'Michael Phiri', 'michael.phiri@email.com', 'Partnership Inquiry', 'Hello, I represent a local NGO and we are interested in partnering with Synergex Solutions for community waste management programs. Please contact us to discuss collaboration opportunities.', 'unread', '2025-01-12 06:15:00'),
(8, 'Patricia Ngoma', 'patricia.ngoma@email.com', 'Product Information Request', 'I would like to know more about your eco-pavers. Are they suitable for high-traffic areas? What is the expected lifespan?', 'read', '2025-01-18 12:30:00'),
(9, 'James Mwanza', 'james.mwanza@email.com', 'Volunteer Opportunity', 'I am interested in volunteering with your organization. How can I get involved in your community programs?', 'responded', '2025-01-25 07:45:00'),
(10, 'Ruth Kunda', 'ruth.kunda@email.com', 'Bulk Order Inquiry', 'We are a construction company and would like to place a large order for eco-pavers. Can you provide bulk pricing and delivery options?', 'unread', '2025-02-05 09:20:00'),
(11, 'Andrew Sinkala', 'andrew.sinkala@email.com', 'Educational Visit Request', 'Our school would like to organize an educational visit to your facility. Can we schedule a tour for our students?', 'read', '2025-02-14 13:00:00'),
(12, 'Esther Mbewe', 'esther.mbewe@email.com', 'Waste Collection Service', 'Our business generates significant plastic waste. Do you offer waste collection services for commercial establishments?', 'unread', '2025-02-22 08:10:00');

-- --------------------------------------------------------

--
-- Stand-in structure for view `dashboard_stats`
-- (See below for the actual view)
--
CREATE TABLE `dashboard_stats` (
`total_products` bigint(21)
,`active_products` bigint(21)
,`total_quotes` bigint(21)
,`pending_quotes` bigint(21)
,`total_messages` bigint(21)
,`unread_messages` bigint(21)
,`active_subscribers` bigint(21)
,`total_gallery_images` bigint(21)
,`total_achievements` bigint(21)
);

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL COMMENT 'waste_collection, recycling, production, installation, community',
  `image` varchar(255) NOT NULL,
  `media_type` enum('image','video') NOT NULL DEFAULT 'image',
  `caption` text DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `title`, `category`, `image`, `media_type`, `caption`, `display_order`, `created_at`) VALUES
(3, 'Plastic Sorting Facility', 'recycling', 'gallery_1766126072_6944f1f824548.mp4', 'video', 'State-of-the-art sorting facility for plastic waste processing', 3, '2025-12-17 19:24:44'),
(4, 'Recycling Process Overview', 'recycling', 'gallery_1766126097_6944f21119504.mp4', 'video', 'Advanced machinery transforming waste into reusable materials', 4, '2025-12-17 19:24:44'),
(7, 'School Installation Project', 'installation', 'gallery_1766087182_69445a0eeeedc.jpeg', 'image', 'Installing eco-pavers at Central Primary School playground', 7, '2025-12-17 19:24:44'),
(8, 'Residential Driveway Project', 'installation', 'gallery_1766087136_694459e034819.jpeg', 'image', 'Completed residential driveway using our eco-pavers', 8, '2025-12-17 19:24:44'),
(10, 'School Education Program', 'community', 'gallery_1766086870_694458d618dad.jpeg', 'image', 'Teaching students about waste management and recycling', 10, '2025-12-17 19:24:44'),
(11, 'Community Partnership Event', 'community', 'gallery_1766086979_69445943c905b.jpeg', 'image', 'Collaborating with local communities for sustainable waste management', 11, '2025-12-17 19:24:44'),
(12, 'Awareness Campaign Launch', 'community', 'gallery_1766086962_69445932799ce.jpeg', 'image', 'Launching public awareness campaign on plastic waste reduction', 12, '2025-12-17 19:24:44'),
(13, 'Waste Collection at Kitwe Market', 'waste_collection', 'gallery_1766126125_6944f22dd597d.mp4', 'video', 'Our team collecting plastic waste from local markets in Kitwe', 1, '2025-12-17 19:39:08'),
(17, 'Eco-Paver Production Line', 'production', 'gallery_1766087158_694459f60d27d.jpeg', 'image', 'Automated production line manufacturing eco-friendly pavers', 5, '2025-12-17 19:39:08'),
(20, 'Residential Driveway Project', 'installation', 'gallery_1766087095_694459b7296c8.jpeg', 'image', 'Completed residential driveway using our eco-pavers', 8, '2025-12-17 19:39:08'),
(23, 'Community Partnership Event', 'community', 'gallery_1766086900_694458f43706b.jpeg', 'image', 'Collaborating with local communities for sustainable waste management', 11, '2025-12-17 19:39:08');

-- --------------------------------------------------------

--
-- Table structure for table `hero_slider`
--

CREATE TABLE `hero_slider` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `subtitle` text DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `button_text` varchar(100) DEFAULT NULL,
  `button_link` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hero_slider`
--

INSERT INTO `hero_slider` (`id`, `title`, `subtitle`, `image`, `button_text`, `button_link`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(3, 'Turning Waste Into Sustainable Value', 'Join us in creating a cleaner, greener Zambia through innovative recycling solutions and eco-friendly products', 'hero_slider_1766127200_6944f660a3f51.jpeg', 'Get Started', '/products.php', 1, 1, '2025-12-17 19:39:42', '2025-12-19 06:53:20'),
(4, 'Eco-Friendly Building Materials', 'Durable pavers and tiles made from 100% recycled plastic waste. Transform your spaces while protecting the environment.', 'hero_slider_1766127261_6944f69dd7af9.jpeg', 'View Products', '/products.php', 2, 1, '2025-12-17 19:39:42', '2025-12-19 06:54:21'),
(6, 'Partnership for a Sustainable Future', 'Working with communities, institutions, and organizations across Zambia to promote waste management and recycling', 'hero_slider_1766127289_6944f6b9cf319.jpeg', 'Partner With Us', '/contact.php', 4, 1, '2025-12-17 19:39:42', '2025-12-19 06:54:49'),
(7, 'Innovation in Waste Management', 'Advanced recycling technology transforming plastic waste into valuable resources for a circular economy', 'hero_slider_1766127363_6944f7036f48d.jpeg', 'Learn More', '/what-we-do.php', 5, 1, '2025-12-17 19:39:42', '2025-12-19 06:56:03'),
(8, 'Youth Empowerment &amp; Skills Development', 'Creating employment opportunities and training programs for young people in sustainable waste management', 'hero_slider_1766127338_6944f6eab8dbe.jpeg', 'Join Our Mission', '/contact.php', 6, 1, '2025-12-17 19:39:42', '2025-12-19 06:55:38');

-- --------------------------------------------------------

--
-- Table structure for table `impact_stats`
--

CREATE TABLE `impact_stats` (
  `id` int(11) NOT NULL,
  `plastic_recycled` int(11) DEFAULT 0 COMMENT 'Kilograms of plastic recycled',
  `eco_pavers_produced` int(11) DEFAULT 0 COMMENT 'Number of eco-pavers produced',
  `institutions_served` int(11) DEFAULT 0 COMMENT 'Institutions/communities served',
  `youths_engaged` int(11) DEFAULT 0 COMMENT 'Youth engaged in programs',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `impact_stats`
--

INSERT INTO `impact_stats` (`id`, `plastic_recycled`, `eco_pavers_produced`, `institutions_served`, `youths_engaged`, `updated_at`) VALUES
(1, 5000, 10000, 25, 150, '2025-12-17 15:50:10'),
(2, 7500, 15000, 35, 200, '2024-06-01 08:00:00'),
(3, 10000, 20000, 45, 250, '2024-09-01 08:00:00'),
(4, 12500, 25000, 55, 300, '2024-12-01 08:00:00'),
(5, 15000, 30000, 65, 350, '2025-03-01 08:00:00'),
(6, 17500, 35000, 75, 400, '2025-06-01 08:00:00'),
(7, 20000, 40000, 85, 450, '2025-09-01 08:00:00'),
(8, 7500, 15000, 35, 200, '2024-06-01 08:00:00'),
(9, 10000, 20000, 45, 250, '2024-09-01 08:00:00'),
(10, 12500, 25000, 55, 300, '2024-12-01 08:00:00'),
(11, 15000, 30000, 65, 350, '2025-03-01 08:00:00'),
(12, 17500, 35000, 75, 400, '2025-06-01 08:00:00'),
(13, 20000, 40000, 85, 450, '2025-09-01 08:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `page_content`
--

CREATE TABLE `page_content` (
  `id` int(11) NOT NULL,
  `page_name` varchar(100) NOT NULL COMMENT 'home, about, what-we-do, vision-sdgs',
  `section_name` varchar(100) NOT NULL COMMENT 'hero, mission, values, approach, etc.',
  `content` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `page_content`
--

INSERT INTO `page_content` (`id`, `page_name`, `section_name`, `content`, `updated_at`) VALUES
(1, 'about', 'who_we_are', 'Synergex Solutions is a sustainability-driven enterprise dedicated to addressing plastic waste pollution through innovative recycling and production of eco-friendly building materials.', '2025-12-17 15:50:14'),
(2, 'about', 'founder_note', 'Founded by a team of young innovators passionate about sustainability and community development.', '2025-12-17 15:50:14'),
(3, 'vision-sdgs', 'vision', 'A cleaner, greener Zambia where waste is transformed into valuable resources.', '2025-12-17 15:50:14'),
(4, 'vision-sdgs', 'approach', 'Sustainability First|Innovation|Community Engagement|Scalable Impact', '2025-12-17 15:50:14'),
(5, 'what-we-do', 'waste_management', 'Comprehensive waste collection and sorting services for communities and institutions across Zambia.', '2025-12-17 15:50:14'),
(6, 'what-we-do', 'recycling', 'Advanced recycling processes that transform plastic waste into valuable raw materials for production.', '2025-12-17 15:50:14'),
(7, 'what-we-do', 'production', 'Manufacturing durable, water-resistant pavers and tiles made from 100% recycled plastic materials.', '2025-12-17 15:50:14'),
(8, 'what-we-do', 'community', 'Educational programs and awareness campaigns promoting sustainability and waste management.', '2025-12-17 15:50:14'),
(9, 'what-we-do', 'sustainability', 'Promoting circular economy principles and donating waste bins to communities and schools.', '2025-12-17 15:50:14'),
(10, 'home', 'hero_title', 'Transforming Waste Into Sustainable Value', '2025-12-17 19:24:45'),
(11, 'home', 'hero_subtitle', 'Join us in creating a cleaner, greener Zambia through innovative recycling solutions', '2025-12-17 19:24:45'),
(12, 'about', 'our_story', 'Synergex Solutions was founded in 2023 by a group of passionate young entrepreneurs who saw the growing plastic waste crisis in Zambia and decided to take action. Starting with a small pilot project in Kitwe, we have grown into a recognized leader in sustainable waste management.', '2025-12-17 19:24:45'),
(13, 'about', 'our_team', 'Our team consists of dedicated professionals from diverse backgrounds including environmental science, engineering, business, and community development. Together, we work towards our shared vision of a waste-free Zambia.', '2025-12-17 19:24:45'),
(14, 'what-we-do', 'impact', 'Since our inception, we have recycled over 20,000 kg of plastic waste, produced more than 40,000 eco-pavers, served 85+ institutions, and engaged over 450 youths in our programs.', '2025-12-17 19:24:45'),
(15, 'what-we-do', 'technology', 'We use state-of-the-art recycling technology and processes to ensure maximum efficiency and quality in our production. Our facilities are equipped with modern machinery for sorting, processing, and manufacturing.', '2025-12-17 19:24:45'),
(16, 'vision-sdgs', 'mission_statement', 'To transform plastic waste into valuable resources while creating employment opportunities and promoting environmental sustainability across Zambia.', '2025-12-17 19:24:45'),
(17, 'vision-sdgs', 'future_goals', 'By 2030, we aim to expand our operations to all 10 provinces of Zambia, establish 50+ collection points, and engage 10,000+ youths in our programs.', '2025-12-17 19:24:45'),
(18, 'products', 'why_choose_us', 'Our products are made from 100% recycled materials, are highly durable, weather-resistant, and contribute to environmental conservation. Every purchase helps reduce plastic waste in our communities.', '2025-12-17 19:24:45'),
(19, 'contact', 'office_hours', 'Monday - Friday: 8:00 AM - 5:00 PM<br>Saturday: 9:00 AM - 1:00 PM<br>Sunday: Closed', '2025-12-17 19:24:45');

-- --------------------------------------------------------

--
-- Table structure for table `partners`
--

CREATE TABLE `partners` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `partners`
--

INSERT INTO `partners` (`id`, `name`, `logo`, `website`, `display_order`, `is_active`, `created_at`) VALUES
(1, 'Kitwe City Council', NULL, NULL, 1, 1, '2025-12-17 15:50:13'),
(2, 'Zambia Environmental Agency', 'partner_zea.png', 'https://www.zea.gov.zm', 2, 1, '2025-12-17 19:24:45'),
(3, 'Copperbelt University', 'partner_cbu.png', 'https://www.cbu.ac.zm', 3, 1, '2025-12-17 19:24:45'),
(4, 'Green Earth Foundation', 'partner_green_earth.png', 'https://www.greenearth.org.zm', 4, 1, '2025-12-17 19:24:45'),
(5, 'Zambia Waste Management Association', 'partner_zwma.png', 'https://www.zwma.org.zm', 5, 1, '2025-12-17 19:24:45'),
(6, 'National Youth Development Council', 'partner_nydc.png', 'https://www.nydc.gov.zm', 6, 1, '2025-12-17 19:24:45');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `unit` varchar(50) DEFAULT 'per unit',
  `features` text DEFAULT NULL COMMENT 'Pipe-separated list: Feature1|Feature2|Feature3',
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `unit`, `features`, `image`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Eco-Friendly Pavers', 'Durable pavers made from 100% recycled plastic waste. Perfect for walkways, driveways, and outdoor spaces.', 25.00, 'per unit', 'Durable|Water-resistant|Eco-friendly|UV Protected|Low Maintenance', NULL, 1, '2025-12-17 15:50:11', '2025-12-17 15:50:11'),
(2, 'Eco-Friendly Tiles', 'High-quality tiles manufactured from recycled materials. Ideal for indoor and outdoor flooring.', 30.00, 'per unit', 'Durable|Water-resistant|Eco-friendly|Easy Installation|Slip Resistant', NULL, 1, '2025-12-17 15:50:11', '2025-12-17 15:50:11'),
(3, 'Custom Pavers', 'Custom-designed pavers for specific project requirements. Available in various sizes and colors.', 35.00, 'per unit', 'Custom Design|Durable|Eco-friendly|Multiple Sizes|Color Options', NULL, 1, '2025-12-17 15:50:11', '2025-12-17 15:50:11'),
(4, 'Premium Eco-Pavers', 'High-grade pavers with enhanced durability and weather resistance. Perfect for commercial and residential projects.', 45.00, 'per unit', 'Premium Quality|Extra Durable|Weather Resistant|Commercial Grade|Long Warranty', NULL, 1, '2025-12-17 19:24:44', '2025-12-17 19:24:44'),
(5, 'Standard Eco-Tiles', 'Affordable eco-friendly tiles suitable for indoor applications. Available in multiple colors and patterns.', 28.00, 'per unit', 'Affordable|Indoor Use|Multiple Colors|Easy Installation|Eco-Friendly', NULL, 1, '2025-12-17 19:24:44', '2025-12-17 19:24:44'),
(6, 'Garden Border Pavers', 'Specialty pavers designed for garden borders and landscaping. Adds aesthetic appeal to outdoor spaces.', 22.00, 'per unit', 'Garden Design|Decorative|Easy Install|Weather Resistant|Affordable', NULL, 1, '2025-12-17 19:24:44', '2025-12-17 19:24:44'),
(7, 'Premium Eco-Pavers', 'High-grade pavers with enhanced durability and weather resistance. Perfect for commercial and residential projects.', 45.00, 'per unit', 'Premium Quality|Extra Durable|Weather Resistant|Commercial Grade|Long Warranty', NULL, 1, '2025-12-17 19:39:08', '2025-12-17 19:39:08'),
(9, 'Garden Border Pavers', 'Specialty pavers designed for garden borders and landscaping. Adds aesthetic appeal to outdoor spaces.', 22.00, 'per unit', 'Garden Design|Decorative|Easy Install|Weather Resistant|Affordable', NULL, 1, '2025-12-17 19:39:08', '2025-12-17 19:39:08');

-- --------------------------------------------------------

--
-- Table structure for table `product_audit_log`
--

CREATE TABLE `product_audit_log` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `old_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_data`)),
  `new_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_data`)),
  `changed_by` int(11) DEFAULT NULL,
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quote_requests`
--

CREATE TABLE `quote_requests` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `area` decimal(10,2) DEFAULT NULL COMMENT 'Area in square meters',
  `include_installation` tinyint(1) DEFAULT 0,
  `message` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending' COMMENT 'pending, responded, completed, cancelled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quote_requests`
--

INSERT INTO `quote_requests` (`id`, `name`, `email`, `phone`, `product_id`, `area`, `include_installation`, `message`, `status`, `created_at`) VALUES
(1, 'John Mwansa', 'john.mwansa@email.com', '0977123456', 1, 50.00, 1, 'I need eco-pavers for my residential driveway. Please provide a quote with installation included.', 'pending', '2025-01-15 07:30:00'),
(2, 'Sarah Chanda', 'sarah.chanda@email.com', '0966789012', 2, 120.00, 1, 'Looking to install eco-tiles for our office building. Need professional installation service.', 'responded', '2025-01-20 12:15:00'),
(3, 'David Banda', 'david.banda@email.com', '0955123456', 3, 75.50, 0, 'Interested in custom pavers for a commercial project. Please send detailed pricing information.', 'pending', '2025-02-01 09:00:00'),
(4, 'Mary Tembo', 'mary.tembo@email.com', '0978456789', 1, 200.00, 1, 'Large-scale project for a housing development. Need bulk pricing and installation quote.', 'completed', '2025-02-10 14:45:00'),
(5, 'Peter Mulenga', 'peter.mulenga@email.com', '0966234567', 4, 35.00, 1, 'Small garden project. Need premium pavers with installation for my backyard.', 'pending', '2025-02-18 08:20:00'),
(6, 'Grace Mwale', 'grace.mwale@email.com', '0955345678', 2, 90.00, 0, 'School project - need eco-tiles for our new classroom building. Budget-conscious pricing preferred.', 'responded', '2025-02-25 11:30:00'),
(7, 'John Mwansa', 'john.mwansa@email.com', '0977123456', 1, 50.00, 1, 'I need eco-pavers for my residential driveway. Please provide a quote with installation included.', 'pending', '2025-01-15 07:30:00'),
(8, 'Sarah Chanda', 'sarah.chanda@email.com', '0966789012', 2, 120.00, 1, 'Looking to install eco-tiles for our office building. Need professional installation service.', 'responded', '2025-01-20 12:15:00'),
(9, 'David Banda', 'david.banda@email.com', '0955123456', 3, 75.50, 0, 'Interested in custom pavers for a commercial project. Please send detailed pricing information.', 'pending', '2025-02-01 09:00:00'),
(10, 'Mary Tembo', 'mary.tembo@email.com', '0978456789', 1, 200.00, 1, 'Large-scale project for a housing development. Need bulk pricing and installation quote.', 'completed', '2025-02-10 14:45:00'),
(11, 'Peter Mulenga', 'peter.mulenga@email.com', '0966234567', 4, 35.00, 1, 'Small garden project. Need premium pavers with installation for my backyard.', 'pending', '2025-02-18 08:20:00'),
(12, 'Grace Mwale', 'grace.mwale@email.com', '0955345678', 2, 90.00, 0, 'School project - need eco-tiles for our new classroom building. Budget-conscious pricing preferred.', 'responded', '2025-02-25 11:30:00');

-- --------------------------------------------------------

--
-- Stand-in structure for view `recent_activity`
-- (See below for the actual view)
--
CREATE TABLE `recent_activity` (
`type` varchar(10)
,`id` int(11)
,`name` varchar(255)
,`email` varchar(255)
,`created_at` timestamp
);

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'site_name', 'Synergex Solutions', '2025-12-17 15:50:10'),
(2, 'tagline', 'Turning Waste Into Sustainable Value', '2025-12-17 15:50:10'),
(3, 'email', 'synergexsolutions25@gmail.com', '2025-12-17 15:50:10'),
(4, 'phone', '0770377471', '2025-12-17 15:50:10'),
(5, 'whatsapp', '260770377471', '2025-12-17 15:50:10'),
(6, 'mission', 'Creating sustainable value from waste through innovation and community engagement', '2025-12-17 15:50:10'),
(7, 'vision', 'A cleaner, greener Zambia where waste is transformed into valuable resources', '2025-12-17 15:50:10'),
(8, 'address', 'Kitwe, Copperbelt Province, Zambia', '2025-12-17 15:50:10');

-- --------------------------------------------------------

--
-- Table structure for table `subscribers`
--

CREATE TABLE `subscribers` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'active' COMMENT 'active, unsubscribed',
  `subscribed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscribers`
--

INSERT INTO `subscribers` (`id`, `email`, `name`, `status`, `subscribed_at`) VALUES
(1, 'subscriber1@email.com', 'Alice Mwila', 'active', '2024-12-01 08:00:00'),
(2, 'subscriber2@email.com', 'Brian Chisanga', 'active', '2024-12-15 12:30:00'),
(3, 'subscriber3@email.com', 'Catherine Lungu', 'active', '2025-01-05 07:15:00'),
(4, 'subscriber4@email.com', 'Daniel Mumba', 'active', '2025-01-20 14:45:00'),
(5, 'subscriber5@email.com', 'Faith Mwale', 'unsubscribed', '2025-02-01 09:20:00'),
(6, 'subscriber6@email.com', 'George Phiri', 'active', '2025-02-15 11:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `what_we_do`
--

CREATE TABLE `what_we_do` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `icon` varchar(50) NOT NULL,
  `features` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `what_we_do`
--

INSERT INTO `what_we_do` (`id`, `title`, `description`, `icon`, `features`, `created_at`, `updated_at`) VALUES
(1, 'Plastic Waste Management', 'We provide comprehensive waste collection and sorting services for communities, institutions, and businesses across Zambia.', '🗑️', 'Door-to-door waste collection|Waste segregation at source|Community waste collection points|Institutional waste management programs', '2025-12-18 18:01:53', '2025-12-18 18:01:53'),
(2, 'Plastic Recycling', 'Using advanced recycling technology, we transform plastic waste into valuable raw materials.', '♻️', 'Sorting and cleaning of plastic waste|Shredding and processing|Quality control and testing|Raw material production', '2025-12-18 18:01:53', '2025-12-18 18:01:53'),
(3, 'Eco-Friendly Paver & Tile Production', 'We manufacture high-quality, durable pavers and tiles from 100% recycled plastic.', '🧱', 'Durable and long-lasting|Water and UV resistant|Low maintenance requirements|Customizable designs and colors', '2025-12-18 18:01:53', '2025-12-18 18:01:53');

-- --------------------------------------------------------

--
-- Structure for view `dashboard_stats`
--
DROP TABLE IF EXISTS `dashboard_stats`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `dashboard_stats`  AS SELECT (select count(0) from `products`) AS `total_products`, (select count(0) from `products` where `products`.`is_active` = 1) AS `active_products`, (select count(0) from `quote_requests`) AS `total_quotes`, (select count(0) from `quote_requests` where `quote_requests`.`status` = 'pending') AS `pending_quotes`, (select count(0) from `contact_messages`) AS `total_messages`, (select count(0) from `contact_messages` where `contact_messages`.`status` = 'unread') AS `unread_messages`, (select count(0) from `subscribers` where `subscribers`.`status` = 'active') AS `active_subscribers`, (select count(0) from `gallery`) AS `total_gallery_images`, (select count(0) from `achievements`) AS `total_achievements` ;

-- --------------------------------------------------------

--
-- Structure for view `recent_activity`
--
DROP TABLE IF EXISTS `recent_activity`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `recent_activity`  AS SELECT 'quote' AS `type`, `quote_requests`.`id` AS `id`, `quote_requests`.`name` AS `name`, `quote_requests`.`email` AS `email`, `quote_requests`.`created_at` AS `created_at` FROM `quote_requests`union all select 'message' AS `type`,`contact_messages`.`id` AS `id`,`contact_messages`.`name` AS `name`,`contact_messages`.`email` AS `email`,`contact_messages`.`created_at` AS `created_at` from `contact_messages` union all select 'subscriber' AS `type`,`subscribers`.`id` AS `id`,`subscribers`.`name` AS `name`,`subscribers`.`email` AS `email`,`subscribers`.`subscribed_at` AS `created_at` from `subscribers` order by `created_at` desc limit 10  ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `achievements`
--
ALTER TABLE `achievements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_year` (`year`),
  ADD KEY `idx_order` (`display_order`),
  ADD KEY `idx_category` (`category`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_order` (`display_order`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `idx_media_type` (`media_type`);

--
-- Indexes for table `hero_slider`
--
ALTER TABLE `hero_slider`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_order` (`display_order`);

--
-- Indexes for table `impact_stats`
--
ALTER TABLE `impact_stats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `page_content`
--
ALTER TABLE `page_content`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `page_section` (`page_name`,`section_name`);

--
-- Indexes for table `partners`
--
ALTER TABLE `partners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_order` (`display_order`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_price` (`price`);

--
-- Indexes for table `product_audit_log`
--
ALTER TABLE `product_audit_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quote_requests`
--
ALTER TABLE `quote_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `idx_key` (`setting_key`);

--
-- Indexes for table `subscribers`
--
ALTER TABLE `subscribers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `what_we_do`
--
ALTER TABLE `what_we_do`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `achievements`
--
ALTER TABLE `achievements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `hero_slider`
--
ALTER TABLE `hero_slider`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `impact_stats`
--
ALTER TABLE `impact_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `page_content`
--
ALTER TABLE `page_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `partners`
--
ALTER TABLE `partners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `product_audit_log`
--
ALTER TABLE `product_audit_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quote_requests`
--
ALTER TABLE `quote_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `subscribers`
--
ALTER TABLE `subscribers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `what_we_do`
--
ALTER TABLE `what_we_do`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `quote_requests`
--
ALTER TABLE `quote_requests`
  ADD CONSTRAINT `quote_requests_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
