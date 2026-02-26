-- Add pnpki_application column to activity_requirements table
USE dict_monitoring;

ALTER TABLE `activity_requirements`
ADD COLUMN `pnpki_application` TINYINT(1) DEFAULT 0 AFTER `verification_statements`;
