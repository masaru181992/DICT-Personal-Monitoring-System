-- DICT Personal Monitoring System Database Backup
-- Backup Date: 2026-01-07 01:52:45
-- Backup User: admin
-- Database: dict_monitoring
-- Host: localhost
-- MySQL Version: 8.4.7
-- Tables: 15
-- Views: 0
-- 
SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

DROP TABLE IF EXISTS `activities`;

CREATE TABLE `activities` (
  `id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `google_drive_link` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Google Drive link for activity documents',
  `target_date` date NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('not started','in progress','completed','on hold') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'not started',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `offset_days_used` decimal(10,2) DEFAULT '0.00',
  `offset_days_available` decimal(10,2) GENERATED ALWAYS AS (1.00) STORED,
  PRIMARY KEY (`id`),
  KEY `fk_activities_project` (`project_id`),
  CONSTRAINT `fk_activities_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=448 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `activity_requirements`;

CREATE TABLE `activity_requirements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `activity_id` int NOT NULL,
  `request_letter` tinyint(1) DEFAULT '0',
  `reply_letter` tinyint(1) DEFAULT '0',
  `ad` tinyint(1) DEFAULT '0',
  `to` tinyint(1) DEFAULT '0',
  `to_number` varchar(50) DEFAULT NULL,
  `post_activity` tinyint(1) DEFAULT '0',
  `certificates` tinyint(1) DEFAULT '0',
  `verification_statements` tinyint(1) DEFAULT '0',
  `pnpki_application` tinyint(1) DEFAULT '0',
  `photos` tinyint(1) DEFAULT '0',
  `published` tinyint(1) DEFAULT '0',
  `published_link` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `activity_id` (`activity_id`),
  CONSTRAINT `fk_activity_requirements_activity` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `activity_requirements` VALUES('1','115','1','1','1','1','25-312','1','1','1','1','1','1','https://www.facebook.com/share/p/1GWFKFgrG3/','2025-08-15 21:39:55','2025-09-19 18:24:25');
INSERT INTO `activity_requirements` VALUES('2','122','1','1','1','1','25-402','1','1','1','1','1','0',NULL,'2025-08-15 21:41:20','2025-10-26 23:40:50');
INSERT INTO `activity_requirements` VALUES('3','117','1','1','0','1',NULL,'1','0','0','0','1','0',NULL,'2025-08-15 21:58:41','2025-08-15 21:58:41');
INSERT INTO `activity_requirements` VALUES('4','71','1','1','1','1','R13-25-0260','1','1','1','1','1','1','https://www.facebook.com/story.php?story_fbid=1197448209087924&id=100064682679653&mibextid=wwXIfr&rdid=0jDU3gNqn1jfc0Dc#','2025-08-15 22:07:11','2025-09-09 18:50:26');
INSERT INTO `activity_requirements` VALUES('5','70','1','1','0','1',NULL,'1','0','0','0','1','1','https://www.facebook.com/story.php?story_fbid=1197448209087924&id=100064682679653&mibextid=wwXIfr&rdid=0jDU3gNqn1jfc0Dc#','2025-08-15 22:19:50','2025-08-15 22:19:50');
INSERT INTO `activity_requirements` VALUES('6','135','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-08-15 22:31:54','2025-08-15 22:31:54');
INSERT INTO `activity_requirements` VALUES('8','139','1','0','0','1','25-0298','1','0','0','0','1','0','https://www.facebook.com/story.php?story_fbid=1197448209087924&id=100064682679653&mibextid=wwXIfr&rdid=0jDU3gNqn1jfc0Dc#','2025-08-20 17:45:28','2025-09-19 17:49:07');
INSERT INTO `activity_requirements` VALUES('9','141','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-08-20 17:46:29','2025-08-20 17:46:29');
INSERT INTO `activity_requirements` VALUES('10','87','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-08-22 14:49:24','2025-08-22 14:49:24');
INSERT INTO `activity_requirements` VALUES('11','143','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-08-22 14:51:07','2025-08-22 14:51:07');
INSERT INTO `activity_requirements` VALUES('12','144','1','1','1','1',NULL,'1','0','0','0','1','0',NULL,'2025-08-22 19:17:16','2025-09-01 18:29:56');
INSERT INTO `activity_requirements` VALUES('13','140','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-08-24 16:53:17','2025-08-24 16:53:17');
INSERT INTO `activity_requirements` VALUES('14','88','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-08-26 16:15:45','2025-08-26 16:15:45');
INSERT INTO `activity_requirements` VALUES('15','147','0','0','0','0',NULL,'0','0','0','0','1','0',NULL,'2025-08-31 01:11:37','2025-08-31 01:11:37');
INSERT INTO `activity_requirements` VALUES('16','148','0','0','0','1','25-0314','1','0','0','0','1','0',NULL,'2025-09-01 16:14:06','2025-09-29 18:48:32');
INSERT INTO `activity_requirements` VALUES('17','149','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-09-01 17:25:36','2025-09-01 17:25:36');
INSERT INTO `activity_requirements` VALUES('18','151','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-09-01 19:16:47','2025-09-01 19:16:47');
INSERT INTO `activity_requirements` VALUES('19','95','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-09-02 00:10:50','2025-09-02 00:10:50');
INSERT INTO `activity_requirements` VALUES('20','96','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-09-02 05:35:13','2025-09-02 05:35:13');
INSERT INTO `activity_requirements` VALUES('21','97','0','0','0','1','R13-25-0244','1','0','0','0','1','0',NULL,'2025-09-02 05:36:47','2025-09-19 17:46:02');
INSERT INTO `activity_requirements` VALUES('22','150','0','0','0','0','25-0314','0','0','0','0','0','0',NULL,'2025-09-02 05:38:35','2025-09-07 18:19:17');
INSERT INTO `activity_requirements` VALUES('23','137','0','0','1','1','25-0330','0','0','0','0','1','0','https://www.facebook.com/share/p/1GWFKFgrG3/','2025-09-03 02:08:53','2025-09-29 04:33:26');
INSERT INTO `activity_requirements` VALUES('25','77','0','0','0','1','R13-25-249','0','0','0','0','0','0',NULL,'2025-09-08 22:07:08','2025-09-08 22:07:08');
INSERT INTO `activity_requirements` VALUES('26','125','0','0','0','0','1111111','0','0','0','0','0','0',NULL,'2025-09-09 16:15:58','2025-09-19 16:43:01');
INSERT INTO `activity_requirements` VALUES('27','153','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-09-09 16:48:05','2025-09-09 16:48:05');
INSERT INTO `activity_requirements` VALUES('28','155','1','0','0','1','25-0347','1','0','0','0','1','0',NULL,'2025-09-09 17:40:55','2025-09-18 20:08:48');
INSERT INTO `activity_requirements` VALUES('29','156','1','0','0','1','25-0345','1','0','0','0','1','0',NULL,'2025-09-09 18:29:30','2025-09-16 20:32:40');
INSERT INTO `activity_requirements` VALUES('30','157','1','0','0','1','25-0347','1','0','0','0','1','0',NULL,'2025-09-12 19:57:06','2025-09-17 20:30:53');
INSERT INTO `activity_requirements` VALUES('31','15','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-09-12 20:43:21','2025-09-12 20:43:21');
INSERT INTO `activity_requirements` VALUES('32','162','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-09-18 17:27:56','2025-09-18 17:27:56');
INSERT INTO `activity_requirements` VALUES('33','163','0','0','0','0','25-0347','0','0','0','0','1','0',NULL,'2025-09-18 20:19:42','2025-09-18 20:19:42');
INSERT INTO `activity_requirements` VALUES('34','165','1','1','1','1','25-0381','1','1','1','0','1','0',NULL,'2025-09-18 20:45:00','2025-10-13 22:28:20');
INSERT INTO `activity_requirements` VALUES('35','166','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-09-18 22:38:28','2025-09-18 22:38:28');
INSERT INTO `activity_requirements` VALUES('36','161','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-09-19 16:42:29','2025-09-19 16:42:29');
INSERT INTO `activity_requirements` VALUES('37','126','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-09-20 21:09:07','2025-09-20 21:09:07');
INSERT INTO `activity_requirements` VALUES('38','167','0','0','0','1','25-0330','1','1','1','0','1','0',NULL,'2025-09-25 22:01:25','2025-09-29 03:23:28');
INSERT INTO `activity_requirements` VALUES('40','170','1','1','0','1','-25-0375','0','0','0','0','0','0',NULL,'2025-10-01 23:11:21','2025-10-03 19:20:45');
INSERT INTO `activity_requirements` VALUES('41','146','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-10-04 00:20:38','2025-10-04 00:20:38');
INSERT INTO `activity_requirements` VALUES('42','176','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-10-07 04:08:39','2025-10-07 04:08:39');
INSERT INTO `activity_requirements` VALUES('44','175','0','0','0','0','25-0455','1','0','0','0','1','0',NULL,'2025-10-07 06:01:47','2025-11-08 00:55:04');
INSERT INTO `activity_requirements` VALUES('45','182','1','1','0','1','25-0381','0','0','0','0','0','0',NULL,'2025-10-10 17:13:33','2025-10-10 17:14:36');
INSERT INTO `activity_requirements` VALUES('47','181','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-10-13 22:05:09','2025-10-13 22:05:09');
INSERT INTO `activity_requirements` VALUES('48','160','0','0','0','0','25-402','0','0','0','0','0','0',NULL,'2025-10-13 23:14:26','2025-10-17 18:17:52');
INSERT INTO `activity_requirements` VALUES('49','183','1','1','1','1','25-0475','1','1','1','1','1','0',NULL,'2025-10-13 23:36:06','2025-12-03 00:44:37');
INSERT INTO `activity_requirements` VALUES('52','172','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-10-15 15:50:44','2025-10-15 15:50:44');
INSERT INTO `activity_requirements` VALUES('53','197','1','1','0','0','25-402','1','1','1','1','1','0',NULL,'2025-10-15 19:29:23','2025-10-24 17:01:50');
INSERT INTO `activity_requirements` VALUES('54','119','1','0','0','1','25-405','0','0','0','0','1','0',NULL,'2025-10-15 21:12:07','2025-11-17 19:37:30');
INSERT INTO `activity_requirements` VALUES('56','288','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-10-17 03:39:48','2025-10-17 03:39:48');
INSERT INTO `activity_requirements` VALUES('57','289','0','0','0','0','25-402','0','0','0','0','0','0',NULL,'2025-10-23 21:03:13','2025-10-23 21:03:13');
INSERT INTO `activity_requirements` VALUES('58','298','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-10-24 20:59:38','2025-10-24 20:59:38');
INSERT INTO `activity_requirements` VALUES('59','173','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-10-24 20:59:49','2025-10-24 20:59:49');
INSERT INTO `activity_requirements` VALUES('60','302','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-10-26 23:24:56','2025-10-26 23:24:56');
INSERT INTO `activity_requirements` VALUES('61','303','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-10-26 23:25:25','2025-10-26 23:25:25');
INSERT INTO `activity_requirements` VALUES('62','304','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-10-26 23:26:07','2025-10-26 23:26:07');
INSERT INTO `activity_requirements` VALUES('63','305','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-10-26 23:26:39','2025-10-26 23:26:39');
INSERT INTO `activity_requirements` VALUES('64','306','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-10-26 23:27:11','2025-10-26 23:27:11');
INSERT INTO `activity_requirements` VALUES('65','269','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-10-27 18:57:10','2025-10-27 18:57:10');
INSERT INTO `activity_requirements` VALUES('66','299','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-10-27 20:44:42','2025-10-27 20:44:42');
INSERT INTO `activity_requirements` VALUES('67','180','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-11-02 00:15:14','2025-11-02 00:15:14');
INSERT INTO `activity_requirements` VALUES('68','290','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-11-02 00:18:28','2025-11-02 00:18:28');
INSERT INTO `activity_requirements` VALUES('69','307','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-11-02 00:18:42','2025-11-02 00:18:42');
INSERT INTO `activity_requirements` VALUES('70','308','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-11-02 00:26:03','2025-11-02 00:26:03');
INSERT INTO `activity_requirements` VALUES('71','310','1','0','0','1','25-0455','0','0','0','0','1','0',NULL,'2025-11-03 20:44:48','2025-11-08 21:45:32');
INSERT INTO `activity_requirements` VALUES('72','187','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-11-05 17:17:54','2025-11-05 17:17:54');
INSERT INTO `activity_requirements` VALUES('73','312','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-11-05 17:21:48','2025-11-05 17:21:48');
INSERT INTO `activity_requirements` VALUES('74','319','1','1','0','1','25-0485','1','0','0','0','1','0',NULL,'2025-11-06 21:39:11','2025-12-02 17:01:16');
INSERT INTO `activity_requirements` VALUES('75','322','1','0','1','1','25-0475','0','0','0','0','1','0',NULL,'2025-11-14 18:13:32','2025-11-24 18:08:57');
INSERT INTO `activity_requirements` VALUES('76','323','0','0','0','1','25-0469','0','0','0','0','0','0',NULL,'2025-11-14 21:41:11','2025-11-14 21:41:26');
INSERT INTO `activity_requirements` VALUES('77','129','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-11-17 19:40:12','2025-11-17 19:40:12');
INSERT INTO `activity_requirements` VALUES('78','325','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-11-17 23:24:34','2025-11-17 23:24:34');
INSERT INTO `activity_requirements` VALUES('79','327','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-11-24 16:56:11','2025-11-24 16:56:11');
INSERT INTO `activity_requirements` VALUES('80','309','0','0','0','1','25-0492','0','0','0','0','0','0',NULL,'2025-11-25 23:06:15','2025-11-27 02:21:29');
INSERT INTO `activity_requirements` VALUES('81','340','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-11-26 19:12:58','2025-11-26 19:12:58');
INSERT INTO `activity_requirements` VALUES('82','320','0','0','0','0',NULL,'0','0','0','0','1','0',NULL,'2025-11-26 19:25:16','2025-12-02 17:01:51');
INSERT INTO `activity_requirements` VALUES('83','130','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-12-03 00:45:33','2025-12-03 00:45:33');
INSERT INTO `activity_requirements` VALUES('84','341','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-12-03 00:45:46','2025-12-03 00:45:46');
INSERT INTO `activity_requirements` VALUES('85','17','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-12-05 00:06:14','2025-12-05 00:06:14');
INSERT INTO `activity_requirements` VALUES('86','13','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-12-05 00:07:43','2025-12-05 00:07:43');
INSERT INTO `activity_requirements` VALUES('87','16','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-12-05 00:08:21','2025-12-05 00:08:21');
INSERT INTO `activity_requirements` VALUES('88','24','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-12-05 00:08:54','2025-12-05 00:08:54');
INSERT INTO `activity_requirements` VALUES('89','45','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-12-05 00:09:25','2025-12-05 00:09:25');
INSERT INTO `activity_requirements` VALUES('90','54','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-12-05 00:09:57','2025-12-05 00:09:57');
INSERT INTO `activity_requirements` VALUES('91','50','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-12-05 00:10:32','2025-12-05 00:10:32');
INSERT INTO `activity_requirements` VALUES('92','37','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-12-05 00:11:04','2025-12-05 00:11:04');
INSERT INTO `activity_requirements` VALUES('93','30','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-12-05 00:11:26','2025-12-05 00:11:26');
INSERT INTO `activity_requirements` VALUES('94','28','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-12-05 00:11:49','2025-12-05 00:11:49');
INSERT INTO `activity_requirements` VALUES('95','131','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-12-08 18:01:24','2025-12-08 18:01:24');
INSERT INTO `activity_requirements` VALUES('96','328','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-12-08 18:01:38','2025-12-08 18:01:38');
INSERT INTO `activity_requirements` VALUES('97','371','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-12-13 04:47:59','2025-12-13 04:47:59');
INSERT INTO `activity_requirements` VALUES('98','368','0','0','0','1','25-0518','0','0','0','0','1','0',NULL,'2025-12-13 04:49:31','2025-12-19 21:19:09');
INSERT INTO `activity_requirements` VALUES('99','370','0','0','1','1','25-0524','1','0','0','0','0','0',NULL,'2025-12-19 21:16:54','2025-12-19 21:17:55');
INSERT INTO `activity_requirements` VALUES('100','374','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-12-19 21:17:19','2025-12-19 21:17:19');
INSERT INTO `activity_requirements` VALUES('101','324','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-12-19 21:18:11','2025-12-19 21:18:11');
INSERT INTO `activity_requirements` VALUES('102','375','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-12-22 17:46:14','2025-12-22 17:46:14');
INSERT INTO `activity_requirements` VALUES('103','334','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2025-12-25 17:50:41','2025-12-25 17:50:41');
INSERT INTO `activity_requirements` VALUES('104','372','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2026-01-01 03:21:33','2026-01-01 03:21:33');
INSERT INTO `activity_requirements` VALUES('105','134','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2026-01-01 03:21:54','2026-01-01 03:21:54');

DROP TABLE IF EXISTS `ipcr_activities`;

CREATE TABLE `ipcr_activities` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ipcr_entry_id` int NOT NULL,
  `activity_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ipcr_entry_id` (`ipcr_entry_id`),
  KEY `activity_id` (`activity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=718 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `ipcr_activities` VALUES('231','22','71','2025-08-07 18:07:49');
INSERT INTO `ipcr_activities` VALUES('232','24','71','2025-08-07 18:09:02');
INSERT INTO `ipcr_activities` VALUES('233','25','71','2025-08-07 18:09:31');
INSERT INTO `ipcr_activities` VALUES('245','27','110','2025-08-07 18:12:22');
INSERT INTO `ipcr_activities` VALUES('246','27','70','2025-08-07 18:12:22');
INSERT INTO `ipcr_activities` VALUES('247','27','77','2025-08-07 18:12:22');
INSERT INTO `ipcr_activities` VALUES('248','27','100','2025-08-07 18:12:22');
INSERT INTO `ipcr_activities` VALUES('249','27','97','2025-08-07 18:12:22');
INSERT INTO `ipcr_activities` VALUES('250','27','96','2025-08-07 18:12:22');
INSERT INTO `ipcr_activities` VALUES('251','27','95','2025-08-07 18:12:22');
INSERT INTO `ipcr_activities` VALUES('252','27','89','2025-08-07 18:12:22');
INSERT INTO `ipcr_activities` VALUES('253','27','94','2025-08-07 18:12:22');
INSERT INTO `ipcr_activities` VALUES('254','27','91','2025-08-07 18:12:22');
INSERT INTO `ipcr_activities` VALUES('255','27','80','2025-08-07 18:12:22');
INSERT INTO `ipcr_activities` VALUES('260','31','135','2025-09-01 21:25:35');
INSERT INTO `ipcr_activities` VALUES('261','31','134','2025-09-01 21:25:35');
INSERT INTO `ipcr_activities` VALUES('262','33','135','2025-09-01 21:30:33');
INSERT INTO `ipcr_activities` VALUES('383','9','40','2025-09-01 22:57:51');
INSERT INTO `ipcr_activities` VALUES('392','8','37','2025-09-01 23:02:46');
INSERT INTO `ipcr_activities` VALUES('393','8','14','2025-09-01 23:02:46');
INSERT INTO `ipcr_activities` VALUES('398','5','36','2025-09-01 23:55:51');
INSERT INTO `ipcr_activities` VALUES('399','5','15','2025-09-01 23:55:51');
INSERT INTO `ipcr_activities` VALUES('400','7','36','2025-09-01 23:56:14');
INSERT INTO `ipcr_activities` VALUES('401','7','15','2025-09-01 23:56:14');
INSERT INTO `ipcr_activities` VALUES('402','6','36','2025-09-01 23:56:27');
INSERT INTO `ipcr_activities` VALUES('403','6','15','2025-09-01 23:56:27');
INSERT INTO `ipcr_activities` VALUES('404','4','37','2025-09-02 00:00:43');
INSERT INTO `ipcr_activities` VALUES('405','4','23','2025-09-02 00:00:43');
INSERT INTO `ipcr_activities` VALUES('406','4','17','2025-09-02 00:00:43');
INSERT INTO `ipcr_activities` VALUES('407','4','13','2025-09-02 00:00:43');
INSERT INTO `ipcr_activities` VALUES('408','1','37','2025-09-02 00:01:09');
INSERT INTO `ipcr_activities` VALUES('409','1','23','2025-09-02 00:01:09');
INSERT INTO `ipcr_activities` VALUES('410','1','17','2025-09-02 00:01:09');
INSERT INTO `ipcr_activities` VALUES('411','1','13','2025-09-02 00:01:09');
INSERT INTO `ipcr_activities` VALUES('445','10','65','2025-09-05 18:20:19');
INSERT INTO `ipcr_activities` VALUES('446','10','47','2025-09-05 18:20:19');
INSERT INTO `ipcr_activities` VALUES('447','10','64','2025-09-05 18:20:19');
INSERT INTO `ipcr_activities` VALUES('448','10','49','2025-09-05 18:20:19');
INSERT INTO `ipcr_activities` VALUES('449','10','50','2025-09-05 18:20:19');
INSERT INTO `ipcr_activities` VALUES('450','10','46','2025-09-05 18:20:19');
INSERT INTO `ipcr_activities` VALUES('451','10','55','2025-09-05 18:20:19');
INSERT INTO `ipcr_activities` VALUES('452','10','43','2025-09-05 18:20:19');
INSERT INTO `ipcr_activities` VALUES('453','10','42','2025-09-05 18:20:19');
INSERT INTO `ipcr_activities` VALUES('454','10','41','2025-09-05 18:20:19');
INSERT INTO `ipcr_activities` VALUES('455','10','39','2025-09-05 18:20:19');
INSERT INTO `ipcr_activities` VALUES('456','10','38','2025-09-05 18:20:19');
INSERT INTO `ipcr_activities` VALUES('457','10','30','2025-09-05 18:20:19');
INSERT INTO `ipcr_activities` VALUES('458','10','31','2025-09-05 18:20:19');
INSERT INTO `ipcr_activities` VALUES('459','10','29','2025-09-05 18:20:19');
INSERT INTO `ipcr_activities` VALUES('460','10','27','2025-09-05 18:20:19');
INSERT INTO `ipcr_activities` VALUES('461','10','24','2025-09-05 18:20:19');
INSERT INTO `ipcr_activities` VALUES('462','10','63','2025-09-05 18:20:19');
INSERT INTO `ipcr_activities` VALUES('463','10','21','2025-09-05 18:20:19');
INSERT INTO `ipcr_activities` VALUES('464','10','20','2025-09-05 18:20:19');
INSERT INTO `ipcr_activities` VALUES('465','10','14','2025-09-05 18:20:19');
INSERT INTO `ipcr_activities` VALUES('466','10','12','2025-09-05 18:20:19');
INSERT INTO `ipcr_activities` VALUES('467','10','8','2025-09-05 18:20:19');
INSERT INTO `ipcr_activities` VALUES('639','37','197','2025-10-22 22:23:13');
INSERT INTO `ipcr_activities` VALUES('640','37','122','2025-10-22 22:23:13');
INSERT INTO `ipcr_activities` VALUES('641','37','115','2025-10-22 22:23:13');
INSERT INTO `ipcr_activities` VALUES('642','37','71','2025-10-22 22:23:13');
INSERT INTO `ipcr_activities` VALUES('669','41','319','2025-12-03 00:47:57');
INSERT INTO `ipcr_activities` VALUES('670','41','175','2025-12-03 00:47:57');
INSERT INTO `ipcr_activities` VALUES('671','41','165','2025-12-03 00:47:57');
INSERT INTO `ipcr_activities` VALUES('672','41','167','2025-12-03 00:47:57');
INSERT INTO `ipcr_activities` VALUES('673','40','319','2025-12-03 00:49:30');
INSERT INTO `ipcr_activities` VALUES('674','40','175','2025-12-03 00:49:30');
INSERT INTO `ipcr_activities` VALUES('675','40','165','2025-12-03 00:49:30');
INSERT INTO `ipcr_activities` VALUES('676','40','167','2025-12-03 00:49:30');
INSERT INTO `ipcr_activities` VALUES('677','39','183','2025-12-03 00:50:19');
INSERT INTO `ipcr_activities` VALUES('678','39','197','2025-12-03 00:50:19');
INSERT INTO `ipcr_activities` VALUES('679','39','122','2025-12-03 00:50:19');
INSERT INTO `ipcr_activities` VALUES('680','39','115','2025-12-03 00:50:19');
INSERT INTO `ipcr_activities` VALUES('681','39','71','2025-12-03 00:50:19');
INSERT INTO `ipcr_activities` VALUES('682','38','183','2025-12-03 00:51:30');
INSERT INTO `ipcr_activities` VALUES('683','38','197','2025-12-03 00:51:30');
INSERT INTO `ipcr_activities` VALUES('684','38','122','2025-12-03 00:51:30');
INSERT INTO `ipcr_activities` VALUES('685','38','115','2025-12-03 00:51:30');
INSERT INTO `ipcr_activities` VALUES('686','38','71','2025-12-03 00:51:30');
INSERT INTO `ipcr_activities` VALUES('687','36','183','2025-12-03 00:52:08');
INSERT INTO `ipcr_activities` VALUES('688','36','197','2025-12-03 00:52:08');
INSERT INTO `ipcr_activities` VALUES('689','36','122','2025-12-03 00:52:08');
INSERT INTO `ipcr_activities` VALUES('690','36','115','2025-12-03 00:52:08');
INSERT INTO `ipcr_activities` VALUES('691','36','71','2025-12-03 00:52:08');
INSERT INTO `ipcr_activities` VALUES('692','34','322','2025-12-03 00:53:24');
INSERT INTO `ipcr_activities` VALUES('693','34','323','2025-12-03 00:53:24');
INSERT INTO `ipcr_activities` VALUES('694','34','182','2025-12-03 00:53:24');
INSERT INTO `ipcr_activities` VALUES('695','34','170','2025-12-03 00:53:24');
INSERT INTO `ipcr_activities` VALUES('696','34','137','2025-12-03 00:53:24');
INSERT INTO `ipcr_activities` VALUES('697','34','156','2025-12-03 00:53:24');
INSERT INTO `ipcr_activities` VALUES('698','34','157','2025-12-03 00:53:24');
INSERT INTO `ipcr_activities` VALUES('699','34','148','2025-12-03 00:53:24');
INSERT INTO `ipcr_activities` VALUES('700','34','147','2025-12-03 00:53:24');
INSERT INTO `ipcr_activities` VALUES('701','34','144','2025-12-03 00:53:24');
INSERT INTO `ipcr_activities` VALUES('702','34','139','2025-12-03 00:53:24');
INSERT INTO `ipcr_activities` VALUES('703','34','117','2025-12-03 00:53:24');
INSERT INTO `ipcr_activities` VALUES('704','34','118','2025-12-03 00:53:24');
INSERT INTO `ipcr_activities` VALUES('705','34','114','2025-12-03 00:53:24');
INSERT INTO `ipcr_activities` VALUES('706','34','113','2025-12-03 00:53:24');
INSERT INTO `ipcr_activities` VALUES('707','34','110','2025-12-03 00:53:24');
INSERT INTO `ipcr_activities` VALUES('708','34','70','2025-12-03 00:53:24');
INSERT INTO `ipcr_activities` VALUES('709','34','104','2025-12-03 00:53:24');
INSERT INTO `ipcr_activities` VALUES('710','34','77','2025-12-03 00:53:24');
INSERT INTO `ipcr_activities` VALUES('711','34','97','2025-12-03 00:53:24');
INSERT INTO `ipcr_activities` VALUES('712','34','96','2025-12-03 00:53:24');
INSERT INTO `ipcr_activities` VALUES('713','34','95','2025-12-03 00:53:24');
INSERT INTO `ipcr_activities` VALUES('714','34','89','2025-12-03 00:53:24');
INSERT INTO `ipcr_activities` VALUES('715','34','94','2025-12-03 00:53:24');
INSERT INTO `ipcr_activities` VALUES('716','34','91','2025-12-03 00:53:24');
INSERT INTO `ipcr_activities` VALUES('717','34','80','2025-12-03 00:53:24');

DROP TABLE IF EXISTS `ipcr_entries`;

CREATE TABLE `ipcr_entries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `year` int NOT NULL,
  `semester` enum('1st','2nd') NOT NULL,
  `function_type` enum('Core Function','Support Function') NOT NULL DEFAULT 'Core Function',
  `success_indicators` text NOT NULL,
  `success_indicators_quantity` int NOT NULL DEFAULT '1',
  `actual_accomplishments` text NOT NULL,
  `actual_accomplishments_quantity` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `ipcr_entries` VALUES('1','1','2025','1st','Core Function','Number of Cybersecurity Advocacy and Awareness conducted (face-to-face)','1','','4','2025-06-02 19:49:33','2025-09-02 00:01:09');
INSERT INTO `ipcr_entries` VALUES('4','1','2025','1st','Core Function','Number of individuals reached for Advocacy and Awareness conducted (face-to-face)','25','','276','2025-06-02 22:56:36','2025-09-02 00:00:43');
INSERT INTO `ipcr_entries` VALUES('5','1','2025','1st','Core Function','Number of PKI awareness campaigns conducted','2','','2','2025-06-02 23:08:16','2025-09-01 23:55:51');
INSERT INTO `ipcr_entries` VALUES('6','1','2025','1st','Core Function','Number of Issued Digital Certificates','10','','193','2025-06-02 23:10:10','2025-09-01 23:56:27');
INSERT INTO `ipcr_entries` VALUES('7','1','2025','1st','Core Function','Number of PNPKI User\'s Training conducted','1','','2','2025-06-02 23:11:53','2025-09-01 23:56:14');
INSERT INTO `ipcr_entries` VALUES('8','1','2025','1st','Core Function','Number of PNPKI User\'s Trained','25','','193','2025-06-02 23:13:22','2025-09-01 23:02:46');
INSERT INTO `ipcr_entries` VALUES('9','1','2025','1st','Core Function','# of Technical Assistance Provided (incident response) - as the need arises','1','','1','2025-06-02 23:15:21','2025-09-01 22:57:51');
INSERT INTO `ipcr_entries` VALUES('10','1','2025','1st','Support Function','# Supported Activities','1','','23','2025-06-02 23:20:09','2025-09-05 18:20:19');
INSERT INTO `ipcr_entries` VALUES('34','1','2025','2nd','Support Function','# Supported Activities','1','','26','2025-09-01 22:54:34','2025-12-03 00:53:24');
INSERT INTO `ipcr_entries` VALUES('35','1','2025','2nd','Core Function','# of Technical Assistance Provided (incident response) - as the need arises','1','N/A','0','2025-09-02 00:08:02','2025-09-02 00:08:02');
INSERT INTO `ipcr_entries` VALUES('36','1','2025','2nd','Core Function','Number of PNPKI User\'s Trained','10','','115','2025-09-05 18:20:52','2025-12-03 00:52:08');
INSERT INTO `ipcr_entries` VALUES('37','1','2025','2nd','Core Function','Number of PNPKI User\'s Training conducted','1','','4','2025-09-05 18:39:48','2025-10-22 22:23:13');
INSERT INTO `ipcr_entries` VALUES('38','1','2025','2nd','Core Function','Number of Issued Digital Certificates','10','','115','2025-09-05 18:43:05','2025-12-03 00:51:30');
INSERT INTO `ipcr_entries` VALUES('39','1','2025','2nd','Core Function','Number of PKI awareness campaigns conducted','1','','5','2025-09-05 18:44:29','2025-12-03 00:50:19');
INSERT INTO `ipcr_entries` VALUES('40','1','2025','2nd','Core Function','Number of individuals reached for Advocacy and Awareness conducted (face-to-face)','25','','189','2025-09-05 18:49:16','2025-12-03 00:49:30');
INSERT INTO `ipcr_entries` VALUES('41','1','2025','2nd','Core Function','Number of Cybersecurity Advocacy and Awareness conducted (face-to-face)','1','','4','2025-09-05 18:49:33','2025-12-03 00:47:57');

DROP TABLE IF EXISTS `ipcr_templates`;

CREATE TABLE `ipcr_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `template_name` enum('Core Function','Support Function') NOT NULL,
  `description` text,
  `payload` json DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_template_name` (`template_name`),
  CONSTRAINT `fk_ipcr_templates_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `ipcr_templates` VALUES('1','1','Core Function','Number of Cybersecurity Advocacy and Awareness conducted (face-to-face)',NULL,'2025-09-23 23:58:30','2025-09-23 23:58:30');

DROP TABLE IF EXISTS `notes`;

CREATE TABLE `notes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` enum('high','medium','low') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `status` enum('pending','in_progress','completed','archived','active') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
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
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `notes` VALUES('14','menkent','MRMIS account(DTR)','medium','active','17','1',NULL,'2025-10-27 16:21:58','2025-10-27 16:22:47');

DROP TABLE IF EXISTS `offset_credits`;

CREATE TABLE `offset_credits` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `date_earned` date NOT NULL,
  `hours_earned` decimal(5,2) NOT NULL,
  `hours_used` decimal(5,2) DEFAULT '0.00',
  `expiry_date` date NOT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `offset_credits_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS `offset_requests`;

CREATE TABLE `offset_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `activity_id` int NOT NULL,
  `offset_date` date NOT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `activity_id` (`activity_id`),
  KEY `idx_offset_requests_user_id` (`user_id`),
  KEY `idx_offset_requests_status` (`status`),
  KEY `idx_offset_requests_date` (`offset_date`),
  KEY `idx_user_status` (`user_id`,`status`),
  KEY `idx_offset_date` (`offset_date`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `offset_requests` VALUES('32','1','65','2025-07-03','nagbarog sa haligi','approved','2025-07-04 03:07:44','2025-07-04 03:07:44');
INSERT INTO `offset_requests` VALUES('33','1','36','2025-08-22','para makaless gas','approved','2025-08-14 16:15:20','2025-08-14 16:15:20');
INSERT INTO `offset_requests` VALUES('34','1','39','2025-11-21','relax','approved','2025-11-21 17:57:01','2025-11-21 17:57:01');
INSERT INTO `offset_requests` VALUES('35','1','65','2025-10-30','leofer sam Tidalgo Wedding day','approved','2025-11-25 16:32:15','2025-11-25 16:32:15');
INSERT INTO `offset_requests` VALUES('36','1','147','2025-12-01','nagkuha passport sa butuan','approved','2025-11-27 02:22:19','2025-11-27 02:22:19');
INSERT INTO `offset_requests` VALUES('37','1','148','2025-12-19','relax','approved','2025-12-19 17:11:26','2025-12-19 17:11:26');
INSERT INTO `offset_requests` VALUES('38','1','156','2025-12-23','rest','approved','2025-12-25 00:11:15','2025-12-25 00:11:15');

DROP TABLE IF EXISTS `offset_usage`;

CREATE TABLE `offset_usage` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `offset_credit_id` int NOT NULL,
  `date_used` date NOT NULL,
  `hours_used` decimal(5,2) NOT NULL,
  `reason` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `offset_credit_id` (`offset_credit_id`),
  CONSTRAINT `offset_usage_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `offset_usage_ibfk_2` FOREIGN KEY (`offset_credit_id`) REFERENCES `offset_credits` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS `overtime_entries`;

CREATE TABLE `overtime_entries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `date` date NOT NULL,
  `hours` decimal(5,2) NOT NULL,
  `reason` text,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `overtime_entries_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS `overtime_requests`;

CREATE TABLE `overtime_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `activity_id` int NOT NULL,
  `activity_start_date` date DEFAULT NULL,
  `activity_end_date` date DEFAULT NULL,
  `days` int NOT NULL DEFAULT '1',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_days` decimal(5,1) NOT NULL DEFAULT '0.0',
  `used_days` int DEFAULT '0',
  `details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `activity_id` (`activity_id`)
) ENGINE=MyISAM AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `overtime_requests` VALUES('57','1','65',NULL,NULL,'1','2025-06-30','2025-06-30','1.0','1',NULL,'','2025-07-02 03:52:48','2025-07-04 03:07:44');
INSERT INTO `overtime_requests` VALUES('58','1','65',NULL,NULL,'1','2025-06-30','2025-06-30','1.0','1',NULL,'','2025-07-02 03:53:02','2025-11-25 16:32:15');
INSERT INTO `overtime_requests` VALUES('60','1','36',NULL,NULL,'1','2025-04-07','2025-04-09','1.0','1',NULL,'','2025-07-31 16:10:29','2025-08-14 16:15:20');
INSERT INTO `overtime_requests` VALUES('61','1','39',NULL,NULL,'1','2025-04-21','2025-04-23','1.0','1',NULL,'','2025-07-31 16:12:32','2025-11-21 17:57:01');
INSERT INTO `overtime_requests` VALUES('62','1','147',NULL,NULL,'1','2025-08-29','2025-08-30','1.0','1',NULL,'','2025-08-29 22:06:17','2025-11-27 02:22:19');
INSERT INTO `overtime_requests` VALUES('63','1','148',NULL,NULL,'1','2025-09-02','2025-09-06','1.0','1',NULL,'','2025-09-08 16:58:02','2025-12-19 17:11:26');
INSERT INTO `overtime_requests` VALUES('64','1','156',NULL,NULL,'1','2025-09-13','2025-09-13','1.0','1',NULL,'','2025-09-14 04:01:13','2025-12-25 00:11:15');
INSERT INTO `overtime_requests` VALUES('66','1','137',NULL,NULL,'1','2025-09-21','2025-09-26','1.0','0',NULL,'approved','2025-10-03 18:02:46','2025-10-03 18:02:46');
INSERT INTO `overtime_requests` VALUES('67','1','165',NULL,NULL,'1','2025-10-05','2025-10-08','1.0','0',NULL,'approved','2025-10-13 16:44:48','2025-10-13 16:44:48');
INSERT INTO `overtime_requests` VALUES('68','1','122',NULL,NULL,'1','2025-10-20','2025-10-20','1.0','0',NULL,'approved','2025-10-20 03:17:39','2025-10-20 03:17:39');
INSERT INTO `overtime_requests` VALUES('70','1','310',NULL,NULL,'1','2025-11-02','2025-11-08','4.0','0',NULL,'approved','2025-11-10 17:43:31','2025-11-10 17:43:31');
INSERT INTO `overtime_requests` VALUES('71','1','119',NULL,NULL,'1','2025-11-11','2025-11-15','2.0','0',NULL,'approved','2025-11-17 22:37:28','2025-11-17 22:37:28');

DROP TABLE IF EXISTS `point_of_contacts`;

CREATE TABLE `point_of_contacts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Type of contact (e.g., regional, provincial, etc.)',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Title/Name of the contact point',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT 'Description of the contact point',
  `phone` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Contact phone number',
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Contact email',
  `officer_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Name of officer in charge',
  `officer_position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Position of officer in charge',
  `officer_phone` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Phone number of officer in charge',
  `alt_focal_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Name of alternative focal person',
  `alt_focal_position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Position of alternative focal person',
  `alt_focal_phone` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Phone number of alternative focal person',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `point_of_contacts` VALUES('1','provincial','Provincial Governor- PDI','Province of Dinagat Islands','','governorsofficepdi@gmail.com','HON. NILO P. DEMEREY JR.','Governor','','Medel Escalante Jr.','Senior Administrative Assistant ll','','2025-05-30 01:58:38','2025-07-15 18:48:47');
INSERT INTO `point_of_contacts` VALUES('2','municipal','Municipal Mayor','LGU Basilisa','','lgubasilisa2022@gmail.com','HON. OZZY REUBEN M. ECLEO','Mayor','','','','','2025-05-30 03:25:36','2025-05-30 03:25:36');
INSERT INTO `point_of_contacts` VALUES('3','municipal','Municipal Mayor','LGU Cagdianao','','lgucagdianaopdi@gmail.com','HON. ADOLFO E. LONGOS','Mayor','','Michael P. Longos','','charlesm.longos@gmail.com','2025-05-30 03:26:46','2025-08-28 21:45:43');
INSERT INTO `point_of_contacts` VALUES('4','municipal','Municipal Mayor','LGU Dinagat','','angbag.onglgudinagat@gmail.com','HON. SIMPLICIO S. LEYRAN','Mayor','','Al E. Eligue','','','2025-05-30 03:28:06','2025-08-28 21:46:24');
INSERT INTO `point_of_contacts` VALUES('5','municipal','Municipal Mayor','LGU Libjo','','lgulibjo.pdi@gmail.com','HON. MELODY L. COMPASIVO','Mayor','mbllamera@gmail.com','Madel H. Obsioma | Phillip Jayson M. Julio','Admin Aide lll/MPDO Clerk | phillipjaysonjulio@gmail.com','delosioma@gmail.com | phillipjaysonjulio@gmail.com','2025-05-30 03:30:05','2025-10-17 00:40:38');
INSERT INTO `point_of_contacts` VALUES('6','municipal','Municipal Mayor','LGU Loreto','','mioloretopdi2019@gmail.com','HON. DOANDRE BILL A. LADAGA','Mayor','','Cresel Mia A. Socajel','','9305490408','2025-05-30 03:31:10','2025-06-02 19:02:01');
INSERT INTO `point_of_contacts` VALUES('7','municipal','Municipal Mayor','LGU San Jose','','ootmsanjose@gmail.com','HON. RUBEN J D. ZUNIEGA','Mayor','','Jurie S. Mancia','','9399072215/sanjosedi.ict@gmail.com','2025-05-30 03:32:07','2025-08-15 04:38:47');
INSERT INTO `point_of_contacts` VALUES('8','municipal','Municipal Mayor','LGU Tubajon','','tubajonofficial@gmail.com','HON. SIMPLICIA P. PEDRABLANCA','Mayor','','Leofer Sam C. Tidalgo','','9514545568/ tidalgoleofersam@gmail.com','2025-05-30 03:33:11','2025-08-15 04:39:55');
INSERT INTO `point_of_contacts` VALUES('10','nga','DENR-PENRO DINAGAT ISLANDS','Province of Dinagat Islands','','penrodinagat@denr.gov.ph','NATHANIEL E. RACHO, RPF','OIC, PNR Officer','','CHRISTIAN JAY D. DUPLITO/REAN DIAMOND MANLIGUEZ','Forest Technician II/ Asst. Chief, ICT Unit | Information System Analyst l','|rdmmanliguez@denr.gov.ph','2025-05-30 18:07:22','2025-10-16 17:29:40');
INSERT INTO `point_of_contacts` VALUES('11','provincial','Vice Governor','Provincial Local Government Unit','','vicegovernorpdi@gmail.com','GERALDINE B. ECLEO,MPA','Vice Governor','','MICHAEL G. TEMARIO','DEMO I','09996727766 mikingtem@gmail.com','2025-07-08 21:21:01','2025-07-08 21:21:01');
INSERT INTO `point_of_contacts` VALUES('12','nga','Provincial DOH Office','Provincial DOH Office - Dinagat Islands','','pdohopdi@caraga.doh.gov.ph','KERBY JOY G. EDERA, RN, MAN','OIC - Development Management Officer IV','','Mernil Jay A. Olay','Administrative Assistant II / IT','jaymernil@gmail.com','2025-07-18 19:00:27','2025-08-28 21:38:53');
INSERT INTO `point_of_contacts` VALUES('14','nga','SDO Dinagat Islands','DepEd Dinagat','','','Bryan L. Arreo, PhD, CESE','OIC-Asst. Schools Division Superimtendent','','Eric Olasiman','','eric.olasiman@deped.gov.ph / 09516869427','2025-08-28 17:34:11','2025-08-28 21:51:13');
INSERT INTO `point_of_contacts` VALUES('15','nga','DILG-PDI','Department of Interior and Local Government Units - PDI','','','','','','Julius De Guzman','','09103353544','2025-09-14 03:59:06','2025-09-14 03:59:06');

DROP TABLE IF EXISTS `projects`;

CREATE TABLE `projects` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('not started','in progress','completed','on hold') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'not started',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `projects` VALUES('2','CSB-CERT 2025','DICT 13 Project Implementation','2025-01-01','2025-12-31','in progress','2025-05-18 23:24:29','2025-07-08 20:47:08');
INSERT INTO `projects` VALUES('3','DICT PDI 2025','Provincial Activity / Initiative','2025-01-01','2025-12-31','in progress','2025-05-19 00:33:43','2025-07-08 20:49:08');
INSERT INTO `projects` VALUES('4','ILCDB 2025','DICT 13 Project Implementation','2025-01-01','2025-12-31','in progress','2025-05-19 19:04:16','2025-07-08 20:49:46');
INSERT INTO `projects` VALUES('5','DICT Caraga 2025','DICT 13 Project Implementation','2025-01-01','2025-12-31','in progress','2025-05-19 22:39:29','2025-07-08 20:47:28');
INSERT INTO `projects` VALUES('6','eGOV 2025','DICT 13 Project Implementation','2025-01-01','2025-12-31','in progress','2025-05-19 22:53:15','2025-07-08 20:49:17');
INSERT INTO `projects` VALUES('7','eLGU 2025','DICT 13 Project Implementation','2025-01-01','2025-12-31','in progress','2025-05-19 22:54:20','2025-07-08 20:49:26');
INSERT INTO `projects` VALUES('9','Wifi 2025','DICT 13 Project Implementation','2025-01-01','2025-12-31','in progress','2025-05-19 23:56:30','2025-07-08 20:49:58');
INSERT INTO `projects` VALUES('10','CSB-PNPKI 2025','DICT 13 Project Implementation','2025-01-01','2025-12-31','in progress','2025-05-20 00:02:22','2025-07-08 20:47:20');
INSERT INTO `projects` VALUES('12','Personal 2025','Kent D. Alico','2025-01-01','2025-12-31','in progress','2025-05-20 00:44:52','2025-05-20 00:44:52');
INSERT INTO `projects` VALUES('13','IIDB 2025','DICT 13 Project Implementation','2025-01-01','2025-12-31','in progress','2025-05-20 16:36:33','2025-07-08 20:49:34');
INSERT INTO `projects` VALUES('14','CSB-CEISMD 2025','DICT 13 Project Implementation','2025-01-01','2025-12-31','in progress','2025-05-28 17:40:50','2025-12-31 23:23:20');
INSERT INTO `projects` VALUES('16','PH Holiday','Holiday','2025-01-01','2030-12-31','in progress','2025-06-16 05:11:04','2025-10-23 21:26:49');
INSERT INTO `projects` VALUES('17','MISS 2025','DICT 13 Project Implementation','2025-01-01','2025-12-31','in progress','2025-10-27 16:22:34','2025-10-27 16:22:34');
INSERT INTO `projects` VALUES('18','GECS 2025','DICT 13 Project Implementation','2025-01-01','2025-12-31','in progress','2025-12-03 17:33:40','2025-12-03 17:33:57');

DROP TABLE IF EXISTS `tev_claims`;

CREATE TABLE `tev_claims` (
  `id` int NOT NULL AUTO_INCREMENT,
  `claim_reference` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `employee_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `claim_date` date NOT NULL,
  `purpose` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `google_drive_link` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Google Drive link for supporting documents',
  `status` enum('Draft','For Review','Approved','Rejected','Paid') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Draft',
  `payment_date` date DEFAULT NULL,
  `project_id` int DEFAULT NULL,
  `project_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activity_id` int DEFAULT NULL,
  `created_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `claim_reference` (`claim_reference`),
  KEY `created_by` (`created_by`),
  KEY `project_id` (`project_id`),
  KEY `activity_id` (`activity_id`),
  KEY `idx_status` (`status`),
  KEY `idx_claim_reference` (`claim_reference`),
  KEY `idx_claim_date` (`claim_date`),
  KEY `idx_employee` (`employee_name`),
  CONSTRAINT `tev_claims_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tev_claims_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tev_claims_ibfk_3` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tev_claims` VALUES('2','TEV-2025-00002','Kent D. Alico','DICT','2025-08-07','tev_claims.php\r\n6350.00 draft\r\n12/12/2025','4100.00','https://drive.google.com/drive/folders/1NZ6BE_AstS4Vt5zLev4s9wLUlUdnfyO2?usp=drive_link','Paid',NULL,'10','CSB-PNPKI 2025','71','1','2025-08-08 00:53:03','2025-12-12 19:23:29');
INSERT INTO `tev_claims` VALUES('6','TEV-2025-00003','Kent D. Alico','DICT','2025-08-08','21/11/2025','900.00','https://drive.google.com/drive/folders/1aUyVpyyyzLR4vKNKMlB8c5VaquhAFPOT?usp=drive_link','Paid',NULL,'10','CSB-PNPKI 2025','115','1','2025-08-08 17:38:05','2025-11-21 23:16:32');
INSERT INTO `tev_claims` VALUES('7','TEV-2025-00004','Kent D. Alico','DICT','2025-08-24','draft 7500\r\npaid 23/12/2025','5850.00','https://drive.google.com/drive/folders/1rWaKt9clvWFsjI8Babv1HD5f70F5lfqJ?usp=drive_link','Paid',NULL,'3','DICT PDI 2025','144','1','2025-08-24 18:03:13','2025-12-24 04:03:35');
INSERT INTO `tev_claims` VALUES('8','TEV-2025-00005','Kent D. Alico','DICT','2025-08-26','10/23/2025','3000.00','https://drive.google.com/drive/folders/1nObBJHO2P9DCEkKmcoytBP7DBgxuvUTq?usp=drive_link','Paid',NULL,'9','Wifi 2025','139','1','2025-08-26 23:06:55','2025-10-23 18:04:52');
INSERT INTO `tev_claims` VALUES('9','TEV-2025-00006','Kent D. Alico','DICT','2025-09-01','please cradt tev claims\r\ndraft-6750\r\npaid-19/12/2025','3750.00','https://drive.google.com/drive/folders/1QZ112TdA3li-BUxFOV3v8wl8zBcr7ZhJ?usp=drive_link','Paid',NULL,'9','Wifi 2025','148','1','2025-09-01 18:36:26','2025-12-22 16:49:17');
INSERT INTO `tev_claims` VALUES('12','TEV-2025-00007','User 1','DICT','2025-10-15','21/11/2025','2200.00','https://drive.google.com/drive/folders/19n2Q1Tau6MqbecxaAJysMT9po58UGdmm?usp=drive_link','Paid',NULL,'7','eLGU 2025','137','1','2025-10-15 15:09:19','2025-11-21 23:16:12');
INSERT INTO `tev_claims` VALUES('14','TEV-2025-00009','User 1','DICT','2025-10-28','gipadala kay sir rober 21/11/25','1500.00','https://drive.google.com/drive/folders/1DDClzni6kaA7WAUt2-RGB1uS8IqegC8X?usp=drive_link','Paid','2025-12-23','9','Wifi 2025','170','1','2025-10-28 15:01:08','2025-12-31 23:59:21');
INSERT INTO `tev_claims` VALUES('15','TEV-2025-00010','User 1','DICT','2025-11-21','gipadala kay sir rober 21/11/25','4850.00','https://drive.google.com/drive/folders/1qv9RWztfo55koYrHxzeOi6ETxh_hYCp9?usp=drive_link','Paid','2025-12-23','14','CSB-CEISMD 2025','165','1','2025-11-21 19:34:05','2025-12-31 23:43:21');
INSERT INTO `tev_claims` VALUES('16','TEV-2025-00011','User 1','DICT','2025-11-21','draft 6200\r\npaid 23/12/2025','7100.00','https://drive.google.com/drive/folders/1X-yTx9AfEky6CgtO7pdmn5PGeLj2H--D?usp=sharing','Paid','2025-12-23','10','CSB-PNPKI 2025','122','1','2025-11-21 19:37:57','2025-12-31 23:45:56');
INSERT INTO `tev_claims` VALUES('17','TEV-2025-00012','User 1','DICT','2025-11-25','gipadala ni mam jov 21/1/2025','4690.00','https://drive.google.com/drive/folders/11zN70zJ7us9L2YaLiraKZ4bzTL9IEU5f?usp=drive_link','Paid','2025-12-23','2','CSB-CERT 2025','119','1','2025-11-25 18:23:36','2025-12-31 23:52:56');
INSERT INTO `tev_claims` VALUES('18','TEV-2025-00013','User 1','DICT','2025-11-25','gipadala ni mam jov 21/1/2025','2700.00','https://drive.google.com/drive/folders/1uiyuXQkbt7vj2jfTB-G_bixHwuTf9DNq?usp=sharing','For Review',NULL,'10','CSB-PNPKI 2025','183','1','2025-11-25 19:25:43','2025-12-03 17:41:25');
INSERT INTO `tev_claims` VALUES('19','TEV-2025-00014','User 1','DICT','2025-12-03','Nov 2-10, 2025\r\n12/08/2025','4200.00','https://drive.google.com/drive/folders/1_9LZVBDIRPZbOwNPqdZm1nJz8v40TJvQ?usp=drive_link','Paid','2025-12-23','18','GECS 2025','310','1','2025-12-03 17:38:58','2025-12-31 23:54:58');
INSERT INTO `tev_claims` VALUES('20','TEV-2025-00015','User 1','DICT','2025-12-03','12/08/2025','900.00','https://drive.google.com/drive/folders/1D1IxKXEKwpl1GLRVR1Tkgb9M-AueuSev?usp=drive_link','Paid','2025-12-23','14','CSB-CEISMD 2025','319','1','2025-12-03 17:42:54','2025-12-31 23:47:12');
INSERT INTO `tev_claims` VALUES('21','TEV-2025-00016','User 1','DICT','2025-12-04','naa na kang mam christine- 12/03/2025\r\npaid-1200-12/19/2025','1200.00','https://drive.google.com/drive/folders/11FyUeUXMCIcAKY8aRM2YpwPHab6aEU-U?usp=drive_link','Paid',NULL,'9','Wifi 2025','155','1','2025-12-04 16:40:18','2025-12-22 16:47:29');
INSERT INTO `tev_claims` VALUES('22','TEV-2025-00017','User 1','DICT','2025-12-17','gihatag nako kay mam jov\r\n17/12/2025','10470.00','https://drive.google.com/drive/folders/1eD_GLb2yGL66Cl9x3WzfCvrg7EuOC5tB?usp=drive_link','Paid','2025-12-23','2','CSB-CERT 2025','309','1','2025-12-18 00:02:12','2025-12-31 23:51:41');
INSERT INTO `tev_claims` VALUES('23','TEV-2025-00018','User 1','DICT','2025-12-17','23/12/2025','4770.00','https://drive.google.com/drive/folders/1KfgHMSsX6S_aSl5y-4JMlGqgksN91wGG?usp=drive_link','Paid',NULL,'5','DICT Caraga 2025','368','1','2025-12-18 00:04:06','2025-12-24 04:01:48');
INSERT INTO `tev_claims` VALUES('24','TEV-2025-00019','User 1','DICT','2025-12-31','.','4770.00','https://drive.google.com/drive/folders/1GjUv1SxOteC2JHwKPZBNd2G1gCKe6U8P?usp=drive_link','Paid','2025-12-23','14','CSB-CEISMD 2025','370','1','2025-12-31 23:50:51','2025-12-31 23:55:50');
INSERT INTO `tev_claims` VALUES('25','TEV-2025-00020','User 1','DICT','2025-12-31','.','900.00','','Paid','2025-12-23','3','DICT PDI 2025','117','1','2025-12-31 23:57:59','2025-12-31 23:58:13');

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_picture` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'default-avatar.jpg',
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','user') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `role_id` int DEFAULT NULL,
  `profile_photo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_photo_updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_users_profile_photo` (`profile_photo`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` VALUES('1','admin','$2y$10$e94O.47JxKIYTuy2MDF5L.py6WNI/vhu5l2.LQUMyNS2qlJHiLBOS','Kent D. Alico','default-avatar.jpg','salamander00000@gmail.com','admin','2025-05-18 22:08:24','2025-08-26 23:12:56',NULL,'user_1_1756192376.jpg','2025-08-26 23:12:56');

DROP TRIGGER IF EXISTS `before_tev_claims_insert`;
DELIMITER $$
CREATE DEFINER=`root`@`localhost` TRIGGER `before_tev_claims_insert` BEFORE INSERT ON `tev_claims` FOR EACH ROW BEGIN
DECLARE year_str CHAR(4);
DECLARE last_seq INT;
DECLARE new_seq INT;
IF NEW.claim_date IS NULL THEN
SET NEW.claim_date = CURDATE();
END IF;
IF NEW.claim_reference IS NULL THEN
SET year_str = DATE_FORMAT(NOW(), '%Y');
SELECT IFNULL(MAX(CAST(SUBSTRING_INDEX(claim_reference, '-', -1) AS UNSIGNED)), 0) INTO last_seq
FROM tev_claims
WHERE claim_reference LIKE CONCAT('TEV-', year_str, '-%');
SET new_seq = last_seq + 1;
SET NEW.claim_reference = CONCAT('TEV-', year_str, '-', LPAD(new_seq, 5, '0'));
END IF;
END$$
DELIMITER ;

SET FOREIGN_KEY_CHECKS=1;
COMMIT;
SET AUTOCOMMIT = 1;
-- Backup completed successfully at: 2026-01-07 01:52:45
