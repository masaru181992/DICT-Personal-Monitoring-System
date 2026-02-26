-- SQL Script to Recreate Notes Table
-- This is a simplified version that works in all MySQL versions
-- Run these commands one by one in your MySQL client

-- 1. First, disable foreign key checks
SET FOREIGN_KEY_CHECKS = 0;

-- 2. Create a backup of the existing notes table if it exists
CREATE TABLE IF NOT EXISTS notes_backup LIKE notes;
TRUNCATE TABLE notes_backup;
INSERT INTO notes_backup SELECT * FROM notes;

-- 3. Drop the existing notes table if it exists
DROP TABLE IF EXISTS notes;

-- 4. Create the new notes table with proper structure
CREATE TABLE `notes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `priority` enum('high','medium','low') NOT NULL DEFAULT 'medium',
  `status` enum('pending','in_progress','completed','archived','active') NOT NULL DEFAULT 'pending',
  `project_id` int DEFAULT NULL,
  `user_id` int NOT NULL,
  `reminder_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_notes_project` (`project_id`),
  KEY `fk_notes_user` (`user_id`),
  CONSTRAINT `fk_notes_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_notes_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Restore data from backup if it exists and has data
INSERT INTO notes (id, title, content, priority, status, project_id, user_id, reminder_date, created_at, updated_at)
SELECT id, title, content, priority, status, project_id, user_id, reminder_date, created_at, updated_at
FROM notes_backup
WHERE 1=1
ON DUPLICATE KEY UPDATE
  title = VALUES(title),
  content = VALUES(content),
  priority = VALUES(priority),
  status = VALUES(status),
  project_id = VALUES(project_id),
  user_id = VALUES(user_id),
  reminder_date = VALUES(reminder_date),
  updated_at = VALUES(updated_at);

-- 6. Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- 7. Show the structure of the new table
DESCRIBE notes;

-- 8. Show the number of records in the new table
SELECT COUNT(*) AS total_notes FROM notes;
