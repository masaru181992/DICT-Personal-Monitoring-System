-- Add google_drive_link column to activities table
ALTER TABLE `activities`
ADD COLUMN `google_drive_link` VARCHAR(512) NULL COMMENT 'Google Drive link for activity documents' AFTER `description`;
