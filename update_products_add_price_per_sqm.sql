-- ============================================================================
-- Migration: Add price_per_sqm column to products table
-- ============================================================================
-- This adds the price_per_sqm field to allow admins to set price per square meter

ALTER TABLE `products` 
ADD COLUMN `price_per_sqm` decimal(10,2) DEFAULT 140.00 COMMENT 'Price per square meter for calculator' 
AFTER `price`;

-- Update existing products with default value
UPDATE `products` SET `price_per_sqm` = 140.00 WHERE `price_per_sqm` IS NULL;
