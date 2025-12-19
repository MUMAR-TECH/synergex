-- ============================================================================
-- Add video support to gallery table
-- ============================================================================

-- Add media_type column to gallery table
ALTER TABLE gallery 
ADD COLUMN media_type ENUM('image', 'video') DEFAULT 'image' NOT NULL 
AFTER image;

-- Update existing records to be marked as images
UPDATE gallery SET media_type = 'image';

-- Add index for better performance when filtering by media type
CREATE INDEX idx_media_type ON gallery(media_type);
