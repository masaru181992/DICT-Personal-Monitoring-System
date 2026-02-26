-- Add to_number column to activity_requirements table
USE dict_monitoring;

ALTER TABLE `activity_requirements`
ADD COLUMN `to_number` VARCHAR(50) DEFAULT NULL AFTER `to`;
