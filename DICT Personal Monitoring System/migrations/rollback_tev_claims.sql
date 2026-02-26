-- Rollback script for TEV claims
-- This script backs up the TEV claims table before deletion

-- Create backup table if it doesn't exist
CREATE TABLE IF NOT EXISTS tev_claims_backup_20240807 LIKE tev_claims;

-- Copy data to backup table
INSERT INTO tev_claims_backup_20240807
SELECT * FROM tev_claims;

-- Verify the backup
SELECT COUNT(*) AS backup_count FROM tev_claims_backup_20240807;

-- Uncomment the following line to drop the original table after verification
-- DROP TABLE IF EXISTS tev_claims;
