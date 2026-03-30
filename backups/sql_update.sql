ALTER TABLE `User` ADD COLUMN IF NOT EXISTS `can_post` BOOLEAN DEFAULT 0;
ALTER TABLE `User` ADD COLUMN IF NOT EXISTS `request_post_permission` BOOLEAN DEFAULT 0;

-- Ensure Product table has showed column
ALTER TABLE `Product` ADD COLUMN IF NOT EXISTS `showed` BOOLEAN DEFAULT 1;
