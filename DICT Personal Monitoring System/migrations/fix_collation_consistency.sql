-- Fix collation consistency for TEV claims functionality
-- This script ensures all relevant tables use the same collation (utf8mb4_unicode_ci)

-- Update tev_claims table to use consistent collation
ALTER TABLE `tev_claims` 
CONVERT TO CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Update users table to match collation
ALTER TABLE `users` 
CONVERT TO CHARACTER SET utf8mb4 
COLLATE utf8mb4_ci;

-- Update activities table to match collation
ALTER TABLE `activities` 
CONVERT TO CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Update projects table to match collation
ALTER TABLE `projects` 
CONVERT TO CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Verify the collation of the tables
SELECT 
    TABLE_NAME, 
    TABLE_COLLATION 
FROM 
    information_schema.TABLES 
WHERE 
    TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME IN ('tev_claims', 'users', 'activities', 'projects');
