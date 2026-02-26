-- Select the database
USE dict_monitoring;

-- Create activity_requirements table
CREATE TABLE IF NOT EXISTS `activity_requirements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `activity_id` int(11) NOT NULL,
  `request_letter` tinyint(1) DEFAULT 0,
  `reply_letter` tinyint(1) DEFAULT 0,
  `ad` tinyint(1) DEFAULT 0,
  `to` tinyint(1) DEFAULT 0,
  `post_activity` tinyint(1) DEFAULT 0,
  `certificates` tinyint(1) DEFAULT 0,
  `verification_statements` tinyint(1) DEFAULT 0,
  `photos` tinyint(1) DEFAULT 0,
  `published` tinyint(1) DEFAULT 0,
  `published_link` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `activity_id` (`activity_id`),
  CONSTRAINT `fk_activity_requirements_activity` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
