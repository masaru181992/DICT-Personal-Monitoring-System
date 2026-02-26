-- IPCR Templates Schema
-- Created: 2025-09-23
-- Purpose: Store reusable IPCR templates a user can apply when adding IPCR entries

-- Safety: Adjust the foreign key to match your users table name/PK if different
--         If your MySQL version is < 5.7 (no JSON type), change payload to TEXT

-- Select the target database
USE `dict_monitoring`;

CREATE TABLE IF NOT EXISTS ipcr_templates (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  template_name ENUM('Core Function','Support Function') NOT NULL,
  description TEXT NULL,
  payload JSON NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  INDEX idx_user (user_id),
  INDEX idx_template_name (template_name),

  CONSTRAINT fk_ipcr_templates_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Uncomment the following if your MySQL version doesn't support JSON
-- ALTER TABLE ipcr_templates MODIFY payload TEXT NULL;
