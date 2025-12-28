-- ============================================================================
-- Migration: Add detailed_content and gallery_images to achievements table
-- ============================================================================

ALTER TABLE `achievements` 
ADD COLUMN `detailed_content` text DEFAULT NULL COMMENT 'Full detailed content for details page' AFTER `description`,
ADD COLUMN `gallery_images` text DEFAULT NULL COMMENT 'JSON array of additional gallery images' AFTER `image`;
