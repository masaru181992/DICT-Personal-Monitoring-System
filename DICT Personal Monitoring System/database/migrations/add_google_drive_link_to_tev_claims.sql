-- Add google_drive_link column to tev_claims table
ALTER TABLE tev_claims 
ADD COLUMN google_drive_link VARCHAR(500) NULL DEFAULT NULL 
COMMENT 'Google Drive link for supporting documents' 
AFTER amount;
