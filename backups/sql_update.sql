-- Update User table to support post permission requests
ALTER TABLE `User` ADD COLUMN IF NOT EXISTS `can_post` BOOLEAN DEFAULT 0;
ALTER TABLE `User` ADD COLUMN IF NOT EXISTS `request_post_permission` BOOLEAN DEFAULT 0;
