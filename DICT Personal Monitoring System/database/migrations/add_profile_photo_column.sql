-- Add profile_photo column to users table
ALTER TABLE users 
ADD COLUMN profile_photo VARCHAR(255) DEFAULT NULL,
ADD COLUMN profile_photo_updated_at TIMESTAMP NULL DEFAULT NULL;

-- Add index for faster lookups
CREATE INDEX idx_users_profile_photo ON users(profile_photo);
