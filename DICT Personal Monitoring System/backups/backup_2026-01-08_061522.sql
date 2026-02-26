-- DICT Personal Monitoring System Database Backup
-- Backup Date: 2026-01-08 06:15:22
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
  PRIMARY KEY (`id`),
  KEY `fk_activities_project` (`project_id`)
) ENGINE=InnoDB AUTO_INCREMENT=501 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `activities` VALUES('8','7','eBOSS Technical Support','Technical Support and Provide Desktop Computers',NULL,'0000-00-00','2025-01-13','2025-01-16','completed','2025-05-19 23:14:12','2025-05-19 23:14:12','0.00');
INSERT INTO `activities` VALUES('10','4','DLT-CNHS','DLT Training (Conduct CA 23/1/2025)',NULL,'0000-00-00','2025-01-20','2025-01-24','completed','2025-05-19 23:26:51','2025-05-19 23:26:51','0.00');
INSERT INTO `activities` VALUES('12','7','e-readyness for eLGU systems','Virtual',NULL,'0000-00-00','2025-02-03','2025-02-03','completed','2025-05-19 23:38:34','2025-05-20 00:09:45','0.00');
INSERT INTO `activities` VALUES('13','14','Legal Information and Data Privacy Awareness','Homestay Owners, Operators and Staff in Province of Dinagat Islands.','','0000-00-00','2025-02-06','2025-02-06','completed','2025-05-19 23:43:36','2025-12-05 00:07:43','0.00');
INSERT INTO `activities` VALUES('14','9','VSAT OPCEN restoration and Comelec Internet Provision Support','Wifi Provision',NULL,'0000-00-00','2025-02-07','2025-02-08','completed','2025-05-19 23:57:49','2025-05-20 00:09:27','0.00');
INSERT INTO `activities` VALUES('15','10','PNPKI UT in LGU Loreto','TO from feb 11-12, 2025','','0000-00-00','2025-02-12','2025-02-12','completed','2025-05-20 00:20:33','2025-09-12 20:43:21','0.00');
INSERT INTO `activities` VALUES('16','14','[Dry Run] Safer Internet Day','Conducted by CSB Team','','0000-00-00','2025-02-13','2025-02-13','completed','2025-05-20 00:26:39','2025-12-05 00:08:21','0.00');
INSERT INTO `activities` VALUES('17','14','Cybersecurity Awareness in Del Pilar NHS','Emersion Students','','0000-00-00','2025-02-14','2025-02-14','completed','2025-05-20 00:30:56','2025-12-05 00:06:14','0.00');
INSERT INTO `activities` VALUES('18','2','CapDev in Cybersec team in Butuan','travel date feb 17-21',NULL,'0000-00-00','2025-02-18','2025-02-21','completed','2025-05-20 00:35:26','2025-05-20 00:35:26','0.00');
INSERT INTO `activities` VALUES('19','10','𝗢𝗿𝗶𝗲𝗻𝘁𝗮𝘁𝗶𝗼𝗻 𝗼𝗻 𝘁𝗵𝗲 𝗣𝗡𝗣𝗞𝗜 𝗢𝗥𝗦 𝗪𝗲𝗯 𝗠𝗮𝗻𝗮𝗴𝗲𝗿 (𝗘𝗻𝗵𝗮𝗻𝗰𝗲𝗺𝗲𝗻𝘁)','Virtual',NULL,'0000-00-00','2025-02-27','2025-02-27','completed','2025-05-20 00:37:56','2025-05-20 00:37:56','0.00');
INSERT INTO `activities` VALUES('20','3','Women\'s month meeting in Provincial Capitol','Yamashiro Bldg',NULL,'0000-00-00','2025-03-04','2025-03-04','completed','2025-05-20 00:41:13','2025-05-20 00:41:13','0.00');
INSERT INTO `activities` VALUES('21','9','Assist restore Vsat in RHU San Jose','with Engr. Guma',NULL,'0000-00-00','2025-03-05','2025-03-05','completed','2025-05-20 00:42:53','2025-05-20 00:42:53','0.00');
INSERT INTO `activities` VALUES('22','12','2025 ICPEP Caraga 5th regional convention, 3rd Student Congress and 7th General Assembly','with Rv maribao',NULL,'0000-00-00','2025-03-06','2025-03-07','completed','2025-05-20 00:48:51','2025-05-20 00:49:10','0.00');
INSERT INTO `activities` VALUES('23','4','Emersion in R. Ecleo NHS','',NULL,'0000-00-00','2025-02-24','2025-03-04','completed','2025-05-20 00:51:43','2025-05-20 00:51:43','0.00');
INSERT INTO `activities` VALUES('24','14','CNHS research about online threats.','Accommodating CNHS for their research about online threats.','','0000-00-00','2025-03-10','2025-03-10','completed','2025-05-20 01:02:41','2025-12-05 00:08:54','0.00');
INSERT INTO `activities` VALUES('25','4','UCE','Travel date March 11, 2025',NULL,'0000-00-00','2025-03-12','2025-03-12','completed','2025-05-20 01:04:43','2025-05-20 01:04:43','0.00');
INSERT INTO `activities` VALUES('26','5','GAD Training','Butuan City',NULL,'0000-00-00','2025-03-13','2025-03-14','completed','2025-05-20 01:05:39','2025-05-20 01:05:39','0.00');
INSERT INTO `activities` VALUES('27','3','Test of Fundamental Academic Skills','Accommodate the CNHS',NULL,'0000-00-00','2025-03-17','2025-03-21','completed','2025-05-20 01:07:06','2025-05-20 01:07:06','0.00');
INSERT INTO `activities` VALUES('28','14','Engagement in Cagdianao NHS','with Engr. Albert','','0000-00-00','2025-03-17','2025-03-17','completed','2025-05-20 01:24:48','2025-12-05 00:11:49','0.00');
INSERT INTO `activities` VALUES('29','4','LGU Tubajon-Implementation for ILCDB ,Lot Provision Update and Computerization','Travel to Tubajon, with engr. guma and engr. albert',NULL,'0000-00-00','2025-03-18','2025-03-19','completed','2025-05-20 01:28:36','2025-05-20 01:29:00','0.00');
INSERT INTO `activities` VALUES('30','14','Women\'s Month Caravan in Municipality of Dinagat, PDI','I\'m presenting the csb awareness videos( March 24, 2025)','','0000-00-00','2025-03-21','2025-03-26','completed','2025-05-20 04:53:40','2025-12-05 00:11:26','0.00');
INSERT INTO `activities` VALUES('31','4','DJEMC ICT Student OJT','OJT in DICT PDI',NULL,'0000-00-00','2025-03-19','2025-06-30','completed','2025-05-20 04:56:23','2025-07-01 08:10:21','0.00');
INSERT INTO `activities` VALUES('32','12','Interview and examination for Computer Maintenance technologist II','DICT Regional Office',NULL,'0000-00-00','2025-03-25','2025-03-25','completed','2025-05-20 04:58:26','2025-05-20 04:58:26','0.00');
INSERT INTO `activities` VALUES('35','10','PNPKI Awareness to the DJEMC OJT','For application of PNPKI certificates',NULL,'0000-00-00','2025-04-02','2025-04-02','completed','2025-05-20 16:29:33','2025-05-20 16:31:00','0.00');
INSERT INTO `activities` VALUES('36','10','PNPKI UT in SDO Dinagat','Alber Central Elem School, Cagdianao Central Elem School and DREESMNHS',NULL,'0000-00-00','2025-04-07','2025-04-09','completed','2025-05-20 16:33:23','2025-06-13 21:24:04','0.00');
INSERT INTO `activities` VALUES('37','14','MoU Signing of DICT R13 and CNHS and Promotion of Programs and Projects','Cybersecurity Caravan in CHNS','','0000-00-00','2025-04-10','2025-04-10','completed','2025-05-20 16:34:44','2025-12-05 00:11:04','0.00');
INSERT INTO `activities` VALUES('38','13','DICT CARAGA - PDI STARTUP Ecosystem Mapping Workshop','with sir Daj, at DOc Me',NULL,'0000-00-00','2025-04-14','2025-04-16','completed','2025-05-20 16:38:22','2025-05-20 16:38:22','0.00');
INSERT INTO `activities` VALUES('39','4','PDI Workforce: Cybersecurity Competency Framework Training','in DICT Surigao City',NULL,'0000-00-00','2025-04-21','2025-04-23','completed','2025-05-20 16:39:59','2025-05-20 16:39:59','0.00');
INSERT INTO `activities` VALUES('40','2','SDO Dinagat Phishing Incident','FB Page',NULL,'0000-00-00','2025-04-25','2025-04-25','completed','2025-05-20 16:43:26','2025-05-20 16:43:26','0.00');
INSERT INTO `activities` VALUES('41','13','DOST PDI for the \"LGU San Jose Gap Assessment and Design Thinking\"','with engr. Guma at Happynest Homestay',NULL,'0000-00-00','2025-04-28','2025-04-30','completed','2025-05-20 16:44:40','2025-05-20 16:44:40','0.00');
INSERT INTO `activities` VALUES('42','3','COMELEC HUB Assignment PDI (FTS)','TH Personnel',NULL,'0000-00-00','2025-05-07','2025-05-07','completed','2025-05-20 16:48:01','2025-05-20 16:50:15','0.00');
INSERT INTO `activities` VALUES('43','3','COMELEC HUB Assignment PDI NLE 2025','TH Personnel',NULL,'0000-00-00','2025-05-12','2025-05-12','completed','2025-05-20 16:50:06','2025-05-20 16:50:06','0.00');
INSERT INTO `activities` VALUES('44','10','Psychometric Exam for PNPKI RAA','',NULL,'0000-00-00','2025-05-21','2025-05-21','completed','2025-05-20 16:51:47','2025-05-23 21:43:19','0.00');
INSERT INTO `activities` VALUES('45','14','𝗦𝗰𝗵𝗲𝗱𝘂𝗹𝗲 𝗼𝗳 𝗣𝗿𝗼𝗷𝗲𝗰𝘁 𝗖𝗼𝗻𝘀𝘂𝗹𝘁𝗮𝘁𝗶𝗼𝗻 𝗼𝗻 𝗦𝗣𝗠𝗦 𝗧𝗲𝗺𝗽𝗹𝗮𝘁𝗲 𝗮𝗻𝗱 𝗚𝘂𝗶𝗱𝗲𝗹𝗶𝗻𝗲𝘀','meeting for the IPCR','','0000-00-00','2025-05-30','2025-05-30','completed','2025-05-20 16:55:08','2025-12-05 00:09:25','0.00');
INSERT INTO `activities` VALUES('46','4','\"Futures Thinking and Strategic Foresight\" Workshop','with mam jai',NULL,'0000-00-00','2025-05-21','2025-05-23','completed','2025-05-23 21:43:02','2025-05-23 21:43:02','0.00');
INSERT INTO `activities` VALUES('47','9','Tubajon Connectivity Provision of Job fair','solo flight/ TO 18-20',NULL,'0000-00-00','2025-06-19','2025-06-20','completed','2025-05-24 02:58:05','2025-06-21 04:42:22','0.00');
INSERT INTO `activities` VALUES('48','5','NICTM Culmination & Midyear Planning and Assessment','travel time 24 morning',NULL,'0000-00-00','2025-06-25','2025-06-27','completed','2025-05-24 03:31:26','2025-07-02 04:16:15','0.00');
INSERT INTO `activities` VALUES('49','4','Kick-Off Launching  | SPARK Blockchain Cryptocurrency Specialist Certification Launching','Assist',NULL,'0000-00-00','2025-05-26','2025-05-26','completed','2025-05-26 17:10:45','2025-05-28 00:55:44','0.00');
INSERT INTO `activities` VALUES('50','4','PDI Spark: Blockchain and Cryptocurrency Specialist Certification','Assist','','0000-00-00','2025-05-26','2025-06-15','completed','2025-05-26 17:34:30','2025-12-05 00:10:32','0.00');
INSERT INTO `activities` VALUES('54','14','CSB Meeting','Updates on sending of invites and registration of the following:\r\n-ASEAN-Japan Video Competition\r\n-Hack4Gov\r\n-Cyber Range\r\n2. Upcoming activities both Cyber and PNPKI\r\n3. Other matters, issues and concerns\r\n\r\nMeeting link: meet.google.com/qbv-ugkr-kzf','','0000-00-00','2025-05-27','2025-05-27','completed','2025-05-27 16:56:53','2025-12-05 00:09:57','0.00');
INSERT INTO `activities` VALUES('55','4','TECH2CLASS: \"Unlocking Digital Potential for Digital Literacy Training','Spreedsheet',NULL,'0000-00-00','2025-05-14','2025-05-16','completed','2025-05-27 23:05:10','2025-05-27 23:05:10','0.00');
INSERT INTO `activities` VALUES('60','3','ICT Month','',NULL,'0000-00-00','2025-06-01','2025-06-30','completed','2025-05-30 23:38:36','2025-07-01 08:11:00','0.00');
INSERT INTO `activities` VALUES('62','10','GIP PNPKI UT','PNPKI Users Training',NULL,'0000-00-00','2025-06-10','2025-06-10','completed','2025-06-03 16:47:28','2025-06-11 01:44:34','0.00');
INSERT INTO `activities` VALUES('63','14','Cybersecurity Awareness Session for Women’s Month','IlCDB ang nag organize pero CSB team ang nag Speaker',NULL,'0000-00-00','2025-03-06','2025-03-06','completed','2025-06-05 20:33:24','2025-06-05 20:33:24','0.00');
INSERT INTO `activities` VALUES('64','4','Web 3.0 Information Session','Assist RV maribao',NULL,'0000-00-00','2025-06-11','2025-06-11','completed','2025-06-11 21:41:13','2025-06-13 16:19:13','0.00');
INSERT INTO `activities` VALUES('65','4','MOA signing of DJEMC and with RD, Graduation of SPARK','',NULL,'0000-00-00','2025-06-30','2025-06-30','completed','2025-06-13 16:18:51','2025-07-01 08:11:20','0.00');
INSERT INTO `activities` VALUES('67','16','Eid al-Adha (Feast of the Sacrifice)','Holiday',NULL,'0000-00-00','2025-06-06','2025-06-06','completed','2025-06-16 05:13:03','2025-06-16 05:13:03','0.00');
INSERT INTO `activities` VALUES('68','16','Independence Day','Holiday',NULL,'0000-00-00','2025-06-12','2025-06-12','completed','2025-06-16 05:15:09','2025-06-16 05:15:09','0.00');
INSERT INTO `activities` VALUES('69','14','DICT Caraga Monday Convocation','CSB',NULL,'0000-00-00','2025-06-16','2025-06-16','completed','2025-06-16 05:16:51','2025-06-16 21:37:03','0.00');
INSERT INTO `activities` VALUES('70','7','eLGU Orientation and Digitalization in LGU Libjo','with mam rhea','https://drive.google.com/drive/folders/1NZ6BE_AstS4Vt5zLev4s9wLUlUdnfyO2?usp=drive_link','0000-00-00','2025-07-30','2025-07-30','completed','2025-06-16 21:35:17','2025-09-08 22:10:50','0.00');
INSERT INTO `activities` VALUES('71','10','PNPKI LGU Libjo','28 nagtravel to LGU Libjo','https://drive.google.com/drive/folders/1NZ6BE_AstS4Vt5zLev4s9wLUlUdnfyO2?usp=drive_link','0000-00-00','2025-07-29','2025-07-29','completed','2025-06-16 21:36:13','2025-09-09 18:50:26','0.00');
INSERT INTO `activities` VALUES('72','14','DICT13 (Caraga) ASEAN-Japan Cybersecurity Awareness Video Competition 2025 Regional Screening','travel time to tubajon internet provision for the job fair',NULL,'0000-00-00','2025-06-18','2025-06-18','completed','2025-06-17 17:50:36','2025-06-19 22:57:21','0.00');
INSERT INTO `activities` VALUES('73','14','CSB Weekly Huddle','meeting',NULL,'0000-00-00','2025-06-19','2025-06-19','completed','2025-06-19 20:21:24','2025-06-19 22:53:44','0.00');
INSERT INTO `activities` VALUES('74','14','𝐎𝐧𝐥𝐢𝐧𝐞 𝐏𝐫𝐞𝐬𝐞𝐧𝐭𝐚𝐭𝐢𝐨𝐧 𝐨𝐟 𝐏𝐫𝐞-𝐉𝐮𝐝𝐠𝐢𝐧𝐠 𝐑𝐞𝐬𝐮𝐥𝐭𝐬 – 𝐀𝐒𝐄𝐀𝐍-𝐉𝐚𝐩𝐚𝐧 𝐂𝐲𝐛𝐞𝐫𝐬𝐞𝐜𝐮𝐫𝐢𝐭𝐲 𝐀𝐰𝐚𝐫𝐞𝐧𝐞𝐬𝐬 𝐕𝐢𝐝𝐞𝐨 𝐂𝐨𝐦𝐩𝐞𝐭𝐢𝐭𝐢𝐨𝐧 𝟐𝟎𝟐𝟓','meet the participants',NULL,'0000-00-00','2025-06-20','2025-06-20','completed','2025-06-19 22:52:45','2025-06-20 18:02:19','0.00');
INSERT INTO `activities` VALUES('77','4','DATA PRIVACY Protection','','https://drive.google.com/drive/folders/1PRWylQK-9Efwl4qGcxn9bC_ZqlZMkoLC?usp=drive_link','0000-00-00','2025-07-15','2025-07-17','completed','2025-06-28 05:09:05','2025-09-08 22:07:08','0.00');
INSERT INTO `activities` VALUES('80','7','LGU Libjo courtesy visit','with RD mar',NULL,'0000-00-00','2025-07-01','2025-07-01','completed','2025-07-01 08:13:29','2025-07-01 18:41:49','0.00');
INSERT INTO `activities` VALUES('87','16','Ninoy Aquino Day','Holiday','','0000-00-00','2025-08-21','2025-08-21','completed','2025-07-02 02:36:05','2025-08-22 14:49:49','0.00');
INSERT INTO `activities` VALUES('88','16','National Heroes Day','Holiday','','0000-00-00','2025-08-25','2025-08-25','completed','2025-07-02 02:36:43','2025-08-26 16:15:45','0.00');
INSERT INTO `activities` VALUES('89','7','Coordination online Meeting with the elgu and LGU LOreto','9:30, SCRIPT',NULL,'0000-00-00','2025-07-04','2025-07-04','completed','2025-07-02 02:53:20','2025-07-04 18:05:07','0.00');
INSERT INTO `activities` VALUES('91','5','MOA signing for DICT Communication Tower and Provincial Office Building','with RD Mar',NULL,'0000-00-00','2025-07-02','2025-07-02','completed','2025-07-02 22:24:53','2025-07-03 02:44:26','0.00');
INSERT INTO `activities` VALUES('93','14','CSB Huddle','meeting',NULL,'0000-00-00','2025-07-03','2025-07-03','completed','2025-07-04 03:16:41','2025-07-04 03:16:41','0.00');
INSERT INTO `activities` VALUES('94','9','Installation in brgy mabuhay and in the MDRRMO DInagat','taman 6:30 PM',NULL,'0000-00-00','2025-07-04','2025-07-04','completed','2025-07-05 06:12:39','2025-07-05 06:12:39','0.00');
INSERT INTO `activities` VALUES('95','9','Continue Free Wifi installation New Mabuhay Elementary School','','','0000-00-00','2025-07-07','2025-07-07','completed','2025-07-07 22:06:00','2025-09-02 00:10:50','0.00');
INSERT INTO `activities` VALUES('96','9','Free Wifi in MSWD Dinagat','','','0000-00-00','2025-07-08','2025-07-08','completed','2025-07-08 15:04:09','2025-09-02 05:35:13','0.00');
INSERT INTO `activities` VALUES('97','9','Installation of Free Wifi Brgy Del Pilar, Cagdianao','','https://drive.google.com/drive/folders/1J0z45Ko28tB3KfZvQ3K7XLW0xzifQqkz?usp=drive_link','0000-00-00','2025-07-09','2025-07-11','completed','2025-07-08 15:05:25','2025-09-19 17:45:25','0.00');
INSERT INTO `activities` VALUES('99','4','SPARK SMM Implementation Coordination Meeting – with Partners','attended virtual',NULL,'0000-00-00','2025-07-14','2025-07-14','completed','2025-07-15 05:03:21','2025-07-15 05:03:21','0.00');
INSERT INTO `activities` VALUES('100','4','TMD DPP competency Framework Preparation Day','with sir pierce and sir mark soriano as oic- niabot, naghataod sa mga gamit',NULL,'0000-00-00','2025-07-14','2025-07-14','completed','2025-07-15 05:06:10','2025-07-15 05:06:10','0.00');
INSERT INTO `activities` VALUES('101','3','Computer maintenance for the spark smm event','10 units',NULL,'0000-00-00','2025-07-18','2025-07-18','completed','2025-07-16 22:36:44','2025-07-21 17:26:12','0.00');
INSERT INTO `activities` VALUES('103','3','Assist the CNHS for using the facility','',NULL,'0000-00-00','2025-07-21','2025-07-21','completed','2025-07-21 22:49:57','2025-08-04 16:53:05','0.00');
INSERT INTO `activities` VALUES('104','4','Assist the Spark Social Media Marketing with DTI and PLGU','gepaTO ko ni sir Mark para mag assist ni RV',NULL,'0000-00-00','2025-07-22','2025-07-22','completed','2025-07-23 16:30:56','2025-07-24 18:29:37','0.00');
INSERT INTO `activities` VALUES('106','3','Spark SMM Training','naghipos sa mga gamit from DTI',NULL,'0000-00-00','2025-07-26','2025-07-26','completed','2025-07-24 16:26:39','2025-07-29 00:13:53','0.00');
INSERT INTO `activities` VALUES('107','9','Assist the continue installation of Free Public Wifi of CNHS','',NULL,'0000-00-00','2025-07-25','2025-07-25','completed','2025-07-25 22:43:04','2025-07-25 22:43:04','0.00');
INSERT INTO `activities` VALUES('109','14','CSB Huddle','',NULL,'0000-00-00','2025-07-31','2025-07-31','completed','2025-07-31 21:16:25','2025-08-01 02:52:49','0.00');
INSERT INTO `activities` VALUES('110','14','LGU Cagdianao(Cometee hearing of science and information technology)','',NULL,'0000-00-00','2025-08-01','2025-08-01','completed','2025-08-01 02:52:21','2025-08-04 16:22:52','0.00');
INSERT INTO `activities` VALUES('113','9','Assist the reinstallation of free wifi in RHU Cagdianao','',NULL,'0000-00-00','2025-08-04','2025-08-04','completed','2025-08-05 17:55:50','2025-08-05 17:55:50','0.00');
INSERT INTO `activities` VALUES('114','9','Technical Support free wifi','in LGU Cagdianao-cant access gmail and canva(resolve)',NULL,'0000-00-00','2025-08-06','2025-08-06','completed','2025-08-06 18:31:51','2025-08-06 18:31:51','0.00');
INSERT INTO `activities` VALUES('115','10','PNPKI UT in LGU San Jose','','https://drive.google.com/drive/folders/1aUyVpyyyzLR4vKNKMlB8c5VaquhAFPOT?usp=drive_link','0000-00-00','2025-09-15','2025-09-15','completed','2025-08-06 19:14:54','2025-09-17 19:27:32','0.00');
INSERT INTO `activities` VALUES('117','3','Consulatation dialogue(DSWD)','@ dokme functionhall\r\npreppare coresponding documents\r\n\r\nhttps://docs.google.com/spreadsheets/d/1K7JHjkMRPsrA0HV5YaprRJ5WjrIMhVg8/edit?gid=637528320#gid=637528320','https://drive.google.com/drive/folders/1LhXHtOcY6ypnTbN5qux1dUk36xbi1IT2?usp=drive_link','0000-00-00','2025-08-12','2025-08-12','completed','2025-08-06 19:46:02','2025-12-05 17:07:59','0.00');
INSERT INTO `activities` VALUES('118','9','Assist the Installation of Internet Provision in the POPDEV Congress','',NULL,'0000-00-00','2025-08-06','2025-08-06','completed','2025-08-07 17:58:40','2025-08-07 17:58:40','0.00');
INSERT INTO `activities` VALUES('119','2','Hack for Gov and Cyber range','Nov 11- 15, travel date\r\nCyber Range - Nov 13, 2025\r\nHack for Gov - Nov 14, 2025','https://drive.google.com/drive/folders/11zN70zJ7us9L2YaLiraKZ4bzTL9IEU5f?usp=drive_link','0000-00-00','2025-11-11','2025-11-15','completed','2025-08-07 19:42:21','2025-11-17 19:39:34','0.00');
INSERT INTO `activities` VALUES('122','10','PNPKI UT- LGU Tubajon','ask if lahus ba ang 29\r\nmag gas lang ba ibutang sa AD?\r\npalihug ko sa TO ug pacancel and move to the new schedule','https://drive.google.com/drive/folders/1X-yTx9AfEky6CgtO7pdmn5PGeLj2H--D?usp=sharing','0000-00-00','2025-10-20','2025-10-20','completed','2025-08-11 15:58:46','2025-10-21 15:15:57','0.00');
INSERT INTO `activities` VALUES('123','14','CSB Team Thursday Huddle','',NULL,'0000-00-00','2025-08-13','2025-08-13','completed','2025-08-13 16:53:13','2025-08-13 19:39:47','0.00');
INSERT INTO `activities` VALUES('124','3','DICT PDI Meeting','AGENDA\r\nDICT Program Provincial Focal Updates\r\n\r\nProgram Updates\r\n* Free Wi-Fi for All Program – Upcoming Activities\r\n - loreto, tubajon, legislative\r\n - awareness of the status of free wifi limit capa\r\n* ILCDB (ICT Literacy and Competency Development Bureau) Trainings – Upcoming Activities\r\n - LGU Cagdianao MOA\r\n - DLT for Tubajon\r\n* CSB/ PNPKI – Upcoming Activities\r\n - San Jose, Tubajon, Cagdianao\r\n* eLGU (Electronic Local Government Unit)- updates\r\n - MOU nalang ilang kulang\r\n* Issues, Concerns, and Recommendations\r\n - attended meeting\r\n  `DSWD\r\n  `Hersay of visitation of sec aguda in PDI\r\n  `offset ko 22','','0000-00-00','2025-08-14','2025-08-14','completed','2025-08-13 19:02:01','2025-08-14 18:00:34','0.00');
INSERT INTO `activities` VALUES('125','12','My Birthday','','https://drive.google.com/drive/my-drive','0000-00-00','2025-09-18','2025-09-18','completed','2025-08-13 21:39:45','2025-09-19 16:43:01','0.00');
INSERT INTO `activities` VALUES('126','12','Mama Birthday','','','0000-00-00','2025-09-19','2025-09-19','completed','2025-08-13 21:40:32','2025-09-20 21:09:07','0.00');
INSERT INTO `activities` VALUES('129','12','Papa Birthday','','','0000-00-00','2025-11-15','2025-11-15','completed','2025-08-13 21:46:19','2025-11-17 19:40:12','0.00');
INSERT INTO `activities` VALUES('130','16','Bonifacio Day','','','0000-00-00','2025-11-30','2025-11-30','completed','2025-08-13 21:46:58','2025-12-03 00:45:33','0.00');
INSERT INTO `activities` VALUES('131','16','Feast of the Immaculate Conception','','','0000-00-00','2025-12-08','2025-12-08','completed','2025-08-13 21:57:15','2025-12-08 18:01:24','0.00');
INSERT INTO `activities` VALUES('133','16','Christmas Day','','','0000-00-00','2025-12-25','2025-12-25','completed','2025-08-13 21:58:14','2026-01-07 11:27:24','0.00');
INSERT INTO `activities` VALUES('134','16','Rizal Day','','','0000-00-00','2025-12-30','2025-12-30','completed','2025-08-13 21:58:38','2026-01-01 03:21:54','0.00');
INSERT INTO `activities` VALUES('135','16','New Year\'s Eve','','','0000-00-00','2025-12-31','2025-12-31','completed','2025-08-13 21:59:03','2026-01-07 11:28:05','0.00');
INSERT INTO `activities` VALUES('137','7','eLGU Admin Training and System Setup - Batch 3 LGUs in Partnership with DILG','guidelines for the website\r\npreBOSS\r\nTravel start 21-27, 2025','https://drive.google.com/drive/folders/19n2Q1Tau6MqbecxaAJysMT9po58UGdmm?usp=drive_link','0000-00-00','2025-09-21','2025-09-26','completed','2025-08-15 19:25:48','2025-10-15 15:08:48','0.00');
INSERT INTO `activities` VALUES('139','9','Site Survey in Loreto and Installation in Brgy Malinao','need post activity','https://drive.google.com/drive/folders/1nObBJHO2P9DCEkKmcoytBP7DBgxuvUTq?usp=drive_link','0000-00-00','2025-08-18','2025-08-20','completed','2025-08-17 05:19:02','2025-09-18 20:04:45','0.00');
INSERT INTO `activities` VALUES('140','12','pabrace sa ngipon c Danica','Surigao','','0000-00-00','2025-08-23','2025-08-24','completed','2025-08-17 05:22:28','2025-08-24 16:53:29','0.00');
INSERT INTO `activities` VALUES('141','14','CSB Team Huddle','nagmeeting sa design para sa H$G ug cyber range event colats','','0000-00-00','2025-08-19','2025-08-19','completed','2025-08-20 17:46:11','2025-08-20 21:49:48','0.00');
INSERT INTO `activities` VALUES('142','14','papa naospital','','','0000-00-00','2025-08-20','2025-08-21','completed','2025-08-22 14:48:59','2025-08-22 14:48:59','0.00');
INSERT INTO `activities` VALUES('143','12','Offset','','','0000-00-00','2025-08-22','2025-08-22','completed','2025-08-22 14:50:31','2025-08-22 14:51:07','0.00');
INSERT INTO `activities` VALUES('144','3','Planning Workshop for the Mainstreaming of STI to the CDP in LGU Libjo','about sa wifi( canva ug chatgpt)\r\nabout sa VA, muatend c mayora, then isyu sa mga unit\r\nDRRMO internet connectivity\r\ntourism connectivity\r\ncctv installtion help\r\nsocial egov app(symposium)\r\nDLT( learning resources)\r\nlimited connectivity for GIDA(tourist site like magsaysay)\r\nDLT for LGU libjo employee','https://drive.google.com/drive/folders/1rWaKt9clvWFsjI8Babv1HD5f70F5lfqJ?usp=drive_link','0000-00-00','2025-08-27','2025-08-29','completed','2025-08-22 19:17:00','2025-09-01 18:30:52','0.00');
INSERT INTO `activities` VALUES('146','3','Bugkosan sa Isla sa Dinagat','','https://drive.google.com/drive/folders/1DDClzni6kaA7WAUt2-RGB1uS8IqegC8X?usp=drive_link','0000-00-00','2025-09-27','2025-10-03','completed','2025-08-29 18:26:57','2025-11-10 17:47:34','0.00');
INSERT INTO `activities` VALUES('147','7','Consultative Meeting with San Jose, PDI on the Implementation of eLGU BPLS','assist elgu team\r\nconcern\r\n*freewifi and technical assistance during eboss\r\nbasilisa ok na daw ingon taga eLGU','https://drive.google.com/drive/folders/12ZevOCGFCIP2Ki5Wqb7-gQlVld4ImaGz?usp=drive_link','0000-00-00','2025-08-29','2025-08-30','completed','2025-08-29 19:17:42','2025-09-01 17:56:43','0.00');
INSERT INTO `activities` VALUES('148','9','Free Wifi Installation of Brgy Rosita and DOña Helen, Basilisa','nahuman sep 4, 2025','https://drive.google.com/drive/folders/1QZ112TdA3li-BUxFOV3v8wl8zBcr7ZhJ?usp=drive_link','0000-00-00','2025-09-02','2025-09-06','completed','2025-09-01 16:11:33','2025-09-05 17:52:02','0.00');
INSERT INTO `activities` VALUES('149','3','DICT PDI Meeting','unit sa DTC libjo \r\npaperma sa post activity sa libjo','','0000-00-00','2025-09-01','2025-09-01','completed','2025-09-01 16:54:05','2025-09-01 18:24:09','0.00');
INSERT INTO `activities` VALUES('150','14','CSB Meeting','','','0000-00-00','2025-09-01','2025-09-01','completed','2025-09-01 18:23:28','2025-09-02 05:38:35','0.00');
INSERT INTO `activities` VALUES('151','7','LGU Libjo, PDI E-Readiness Validation','meet.google.com/skd-rzjm-mra','','0000-00-00','2025-09-18','2025-09-18','completed','2025-09-01 19:06:50','2025-09-19 16:42:45','0.00');
INSERT INTO `activities` VALUES('153','14','CSB Meeting','1 docs colats, design and proposed activities\r\nask sam2 if lahus oct 1 or sept 29\r\nweekly stories sa PNPKI LIbjo','','0000-00-00','2025-09-09','2025-09-09','completed','2025-09-09 16:36:11','2025-09-09 18:17:51','0.00');
INSERT INTO `activities` VALUES('155','9','Internet Provision of PPDO (Comprehensive Development Plan - Executive Legislative Agenda)','TO only sep 10 & 12 only, bantay sa devices sa september 11, 2025','https://drive.google.com/drive/folders/11FyUeUXMCIcAKY8aRM2YpwPHab6aEU-U?usp=drive_link','0000-00-00','2025-09-10','2025-09-10','completed','2025-09-09 17:40:12','2025-09-12 19:55:57','0.00');
INSERT INTO `activities` VALUES('156','9','Internet Provision of HANDOG NG PANGULO:SERBISYONG SAPAT PARA SA LAHAT INITIATIVE','','https://drive.google.com/drive/folders/1Ns5LmzNfT8y2ASktF-kKCwsnAinbqj0L?usp=drive_link','0000-00-00','2025-09-13','2025-09-13','completed','2025-09-09 17:40:36','2025-09-14 03:37:45','0.00');
INSERT INTO `activities` VALUES('157','9','Internet Provision of PPDO (Comprehensive Development Plan - Executive Legislative Agenda)','pasign pako sa post activity','https://drive.google.com/drive/folders/11FyUeUXMCIcAKY8aRM2YpwPHab6aEU-U?usp=drive_link','0000-00-00','2025-09-12','2025-09-12','completed','2025-09-12 19:56:35','2025-09-14 03:41:49','0.00');
INSERT INTO `activities` VALUES('158','9','Internet Provision of PPDO (Comprehensive Development Plan - Executive Legislative Agenda)','wala koy TO ani, internal lang kay nihangyu naay magbantay sa wifi during event ang PPDO/ mam lorwin','','0000-00-00','2025-09-11','2025-09-11','completed','2025-09-12 20:13:55','2025-09-12 20:13:55','0.00');
INSERT INTO `activities` VALUES('160','4','Infosession: Understanding SMART Contract','replacement for DPO in LGU San Jose','','0000-00-00','2025-10-21','2025-10-21','completed','2025-09-16 17:02:32','2025-10-22 19:03:37','0.00');
INSERT INTO `activities` VALUES('161','14','CSB Team Huddle','','','0000-00-00','2025-09-18','2025-09-18','completed','2025-09-17 23:28:00','2025-09-19 16:42:29','0.00');
INSERT INTO `activities` VALUES('162','3','Proclamation No. 1027','Declairing a Special (Non-Working day) in the Province of Dinagat Islands','https://drive.google.com/drive/folders/1dGEX_vrqsWLS0nAYEM_FJFo3Is4GQDRB?usp=drive_link','0000-00-00','2025-10-02','2025-10-02','completed','2025-09-18 15:21:15','2025-10-03 19:21:04','0.00');
INSERT INTO `activities` VALUES('163','7','LGU Basilisa, PDI E-Readiness Validation','','https://drive.google.com/drive/folders/1cld00HktOENk0icG8C2nFT8Yu7x-ycZn?usp=drive_link','0000-00-00','2025-09-18','2025-09-18','completed','2025-09-18 18:27:33','2025-09-18 21:14:36','0.00');
INSERT INTO `activities` VALUES('165','14','Cybersecurity Caravan in Tubajon','Tubajon National High School – October 6, 2025 \r\nMalinao National High School – October 7, 2025 \r\nTrinidad Mapa Gupana National High School – October 8, 2025','https://drive.google.com/drive/folders/1qv9RWztfo55koYrHxzeOi6ETxh_hYCp9?usp=drive_link','0000-00-00','2025-10-05','2025-10-08','completed','2025-09-18 20:43:24','2025-10-10 17:13:12','0.00');
INSERT INTO `activities` VALUES('166','14','Tips Poster','catchy and modern\r\ndiscription:(not overwhelming) short and precise, picture(1-2)be creative as much as posible (technology,digital, cyber theme, modern)\r\n*posting video\r\ntopics (Building a Strong Security Culture\r\nAI and Data Privacy Don’t Mix\r\nScan QR Codes Safely\r\nThink Like a Hacker: Spot Social Engineering Tricks\r\nAI-Powered Phishing: Stay Cyber Safe\r\nBe Ransomware Ready\r\nPause Before You Click: Links & Attachments\r\nStrengthen Your Passwords with Smarter Keys\r\nCyberSmart Habits for Everyday Safety\r\nEssential Shields Against Ransomware\r\nDon’t Get Hooked: Report Phishing Attempts\r\nDeepfakes, Phishing & Breaches: The New Threats\r\nSafeguard Your Digital Footprint\r\nWatch Out for Messaging Scams)','','0000-00-00','2026-02-01','2026-02-28','in progress','2025-09-18 22:32:24','2025-09-18 22:43:28','0.00');
INSERT INTO `activities` VALUES('167','14','Cybersecurity Awareness to LGU Libjo and Basilisa PDI','DUring ELGU Training in the Parkway\r\nneed pictures-ask mam deniel','https://drive.google.com/drive/folders/1MFIUnvAqDrbcfGAK9elMAVX3CBEzqrsD?usp=drive_link','0000-00-00','2025-09-25','2025-09-25','completed','2025-09-25 22:01:02','2025-09-29 03:22:21','0.00');
INSERT INTO `activities` VALUES('170','9','Provide internet provision for the \"Araw ng Probinsya\" of the Provincial Government of Dinagat Islands','','https://drive.google.com/drive/folders/1DDClzni6kaA7WAUt2-RGB1uS8IqegC8X?usp=drive_link','0000-00-00','2025-09-29','2025-10-01','completed','2025-10-01 23:11:03','2025-10-09 21:47:37','0.00');
INSERT INTO `activities` VALUES('171','14','CBS Huddle','','','0000-00-00','2025-10-01','2025-10-01','completed','2025-10-02 21:56:26','2025-10-02 21:56:26','0.00');
INSERT INTO `activities` VALUES('172','14','Webinar on Cybercrime Landscape, Investigation and Trends in the Philippines','morning','https://drive.google.com/drive/folders/1QNta-vg0KB_PU8sSZDBZikDYQWiWiJSn?usp=drive_link','0000-00-00','2025-10-22','2025-10-22','completed','2025-10-02 22:10:13','2025-10-23 18:23:39','0.00');
INSERT INTO `activities` VALUES('173','14','Webinar on the Salient Points of Cybersecurity Plan 2023-2028','','','0000-00-00','2025-10-24','2025-10-24','completed','2025-10-02 22:11:53','2025-10-24 20:59:49','0.00');
INSERT INTO `activities` VALUES('175','14','TMD FCERT | PLGU','colab with ilcdb','https://drive.google.com/drive/folders/1364kVJnG-ABKLd3LH72TxtCFB5PzQssk?usp=drive_link','0000-00-00','2025-10-28','2025-10-29','completed','2025-10-03 23:30:41','2025-11-08 00:55:04','0.00');
INSERT INTO `activities` VALUES('176','3','DICT PDI Meeting','ilcdb fcert concern, \r\ndli c sir menkent makaspeaker\r\nnaa man gud gipahimo si sir ram sa amo in preparation sa h4g ug cyber range','','0000-00-00','2025-10-06','2025-10-06','completed','2025-10-07 01:45:36','2025-10-07 05:48:55','0.00');
INSERT INTO `activities` VALUES('180','16','Special non-working Day','','','0000-00-00','2025-10-31','2025-10-31','completed','2025-10-10 16:57:44','2025-11-02 00:15:14','0.00');
INSERT INTO `activities` VALUES('181','14','DTC Computer Formating','','','0000-00-00','2025-10-09','2025-10-10','completed','2025-10-10 16:59:09','2025-10-13 22:05:09','0.00');
INSERT INTO `activities` VALUES('182','9','Restoration of Free wifi in Malinao NHS','','https://drive.google.com/drive/folders/1qv9RWztfo55koYrHxzeOi6ETxh_hYCp9?usp=drive_link','0000-00-00','2025-10-07','2025-10-07','completed','2025-10-10 17:12:58','2025-10-10 17:14:36','0.00');
INSERT INTO `activities` VALUES('183','10','PNPKI UT in DENR-PDI 2nd Batch','','https://drive.google.com/drive/folders/1uiyuXQkbt7vj2jfTB-G_bixHwuTf9DNq?usp=sharing','0000-00-00','2025-11-20','2025-11-20','completed','2025-10-13 22:39:30','2025-11-24 21:55:55','0.00');
INSERT INTO `activities` VALUES('187','16','All Saints\' Day','','','0000-00-00','2025-11-01','2025-11-01','completed','2025-10-15 14:43:04','2025-11-05 17:17:54','0.00');
INSERT INTO `activities` VALUES('188','16','All Saints\' Day','','','0000-00-00','2026-11-01','2026-11-01','in progress','2025-10-15 14:43:04','2025-10-15 14:43:04','0.00');
INSERT INTO `activities` VALUES('189','16','All Saints\' Day','','','0000-00-00','2027-11-01','2027-11-01','in progress','2025-10-15 14:43:04','2025-10-15 14:43:04','0.00');
INSERT INTO `activities` VALUES('190','16','All Saints\' Day','','','0000-00-00','2028-11-01','2028-11-01','in progress','2025-10-15 14:43:04','2025-10-15 14:43:04','0.00');
INSERT INTO `activities` VALUES('191','16','All Saints\' Day','','','0000-00-00','2029-11-01','2029-11-01','in progress','2025-10-15 14:43:04','2025-10-15 14:43:04','0.00');
INSERT INTO `activities` VALUES('193','14','Cybersecurity Awareness Month','','','0000-00-00','2026-10-01','2026-10-31','in progress','2025-10-15 14:45:17','2025-10-15 14:45:17','0.00');
INSERT INTO `activities` VALUES('194','14','Cybersecurity Awareness Month','','','0000-00-00','2027-10-01','2027-10-31','in progress','2025-10-15 14:45:17','2025-10-15 14:45:17','0.00');
INSERT INTO `activities` VALUES('195','14','Cybersecurity Awareness Month','','','0000-00-00','2028-10-01','2028-10-31','in progress','2025-10-15 14:45:17','2025-10-15 14:45:17','0.00');
INSERT INTO `activities` VALUES('196','14','Cybersecurity Awareness Month','','','0000-00-00','2029-10-01','2029-10-31','in progress','2025-10-15 14:45:17','2025-10-15 14:45:17','0.00');
INSERT INTO `activities` VALUES('197','10','PNPKI UT in DENR-PDI 1st Batch','','https://drive.google.com/drive/folders/1zfbkBGIZmsTnQ2g241tkFvI_gQai6luK?usp=drive_link','0000-00-00','2025-10-21','2025-10-21','completed','2025-10-15 16:26:21','2025-10-22 21:28:10','0.00');
INSERT INTO `activities` VALUES('269','14','Cybersecurity Awareness Month','hackerone','','0000-00-00','2025-10-01','2025-10-31','completed','2025-10-15 17:52:03','2025-11-02 00:15:41','0.00');
INSERT INTO `activities` VALUES('288','14','CSB Team Thursday Huddle','','','0000-00-00','2025-10-16','2025-10-16','completed','2025-10-15 21:09:49','2025-10-17 03:39:48','0.00');
INSERT INTO `activities` VALUES('289','14','CSB Team Thursday Huddle','','','0000-00-00','2025-10-24','2025-10-24','completed','2025-10-15 21:09:49','2025-10-27 18:57:27','0.00');
INSERT INTO `activities` VALUES('290','14','CSB Team Thursday Huddle','','','0000-00-00','2025-10-30','2025-10-30','completed','2025-10-15 21:09:49','2025-11-02 00:18:28','0.00');
INSERT INTO `activities` VALUES('298','3','DICT PDI Meeting','1:30 - 3pm','','0000-00-00','2025-10-24','2025-10-24','completed','2025-10-22 19:03:21','2025-10-24 20:59:38','0.00');
INSERT INTO `activities` VALUES('299','2','[𝗗𝗜𝗖𝗧 𝗖𝗔𝗥𝗔𝗚𝗔] 𝗖𝗮𝗽𝗮𝗰𝗶𝘁𝘆 𝗗𝗲𝘃𝗲𝗹𝗼𝗽𝗺𝗲𝗻𝘁 𝗼𝗻 𝗘𝘁𝗵𝗶𝗰𝗮𝗹 𝗛𝗮𝗰𝗸𝗶𝗻𝗴 - 𝗖𝗮𝗽𝘁𝘂𝗿𝗲 𝘁𝗵𝗲 𝗙𝗹𝗮𝗴 𝟮𝟬𝟮𝟱','','','0000-00-00','2025-10-27','2025-10-27','completed','2025-10-23 02:23:17','2025-10-27 20:44:42','0.00');
INSERT INTO `activities` VALUES('300','3','Courtesy Visit to Mayors Office in Cagdianao','','https://drive.google.com/drive/folders/11PTYouWZGgInVtZLXCzv__93wqQ90vI9?usp=drive_link','0000-00-00','2025-10-23','2025-10-23','completed','2025-10-23 18:25:10','2025-10-23 18:25:10','0.00');
INSERT INTO `activities` VALUES('301','12','Kian Rey Alico Birthday','','','0000-00-00','2025-10-25','2025-10-25','completed','2025-10-26 23:24:25','2025-10-26 23:24:25','0.00');
INSERT INTO `activities` VALUES('302','12','Kian Rey Alico Birthday','','','0000-00-00','2026-10-25','2026-10-25','in progress','2025-10-26 23:24:25','2025-10-26 23:24:56','0.00');
INSERT INTO `activities` VALUES('303','12','Kian Rey Alico Birthday','','','0000-00-00','2027-10-25','2027-10-25','in progress','2025-10-26 23:24:25','2025-10-26 23:25:25','0.00');
INSERT INTO `activities` VALUES('304','12','Kian Rey Alico Birthday','','','0000-00-00','2028-10-25','2028-10-25','in progress','2025-10-26 23:24:25','2025-10-26 23:26:07','0.00');
INSERT INTO `activities` VALUES('305','12','Kian Rey Alico Birthday','','','0000-00-00','2029-10-25','2029-10-25','in progress','2025-10-26 23:24:25','2025-10-26 23:26:39','0.00');
INSERT INTO `activities` VALUES('306','12','Kian Rey Alico Birthday','','','0000-00-00','2030-10-25','2030-10-25','in progress','2025-10-26 23:24:25','2025-10-26 23:27:11','0.00');
INSERT INTO `activities` VALUES('307','12','Offset','Leofer Sam Wedding day','','0000-00-00','2025-10-30','2025-10-30','completed','2025-10-26 23:33:41','2025-11-02 00:18:42','0.00');
INSERT INTO `activities` VALUES('308','3','PDRA zoom meeting','5:00 PM','','0000-00-00','2025-11-01','2025-11-01','completed','2025-11-02 00:25:43','2025-11-05 17:18:25','0.00');
INSERT INTO `activities` VALUES('309','2','Hack for Gov Finals','8/12/2025- DEPART\r\n9/12/2025- H4G FINALS\r\n10/122/2025- GOOGLE PH/POST QUA\r\n11/12/2025- CICC\r\n12/12/2025- laag baguio','https://drive.google.com/drive/folders/1eD_GLb2yGL66Cl9x3WzfCvrg7EuOC5tB?usp=drive_link','0000-00-00','2025-12-08','2025-12-12','completed','2025-11-02 00:45:32','2025-12-18 00:06:29','0.00');
INSERT INTO `activities` VALUES('310','18','Duty PDRRMO - Typhoon Tino','RDANA member 11/04/2025','https://drive.google.com/drive/folders/1_9LZVBDIRPZbOwNPqdZm1nJz8v40TJvQ?usp=drive_link','0000-00-00','2025-11-02','2025-11-08','completed','2025-11-03 20:44:29','2025-12-03 17:36:56','0.00');
INSERT INTO `activities` VALUES('312','16','All Souls\' Day','preparation for the typoon tino\r\nduty in pdrrmo','','0000-00-00','2025-11-02','2025-11-02','completed','2025-11-05 17:21:15','2025-11-05 17:25:32','0.00');
INSERT INTO `activities` VALUES('313','16','All Souls\' Day','','','0000-00-00','2026-11-02','2026-11-02','in progress','2025-11-05 17:21:15','2025-11-05 17:21:15','0.00');
INSERT INTO `activities` VALUES('314','16','All Souls\' Day','','','0000-00-00','2027-11-02','2027-11-02','in progress','2025-11-05 17:21:15','2025-11-05 17:21:15','0.00');
INSERT INTO `activities` VALUES('315','16','All Souls\' Day','','','0000-00-00','2028-11-02','2028-11-02','in progress','2025-11-05 17:21:15','2025-11-05 17:21:15','0.00');
INSERT INTO `activities` VALUES('316','16','All Souls\' Day','','','0000-00-00','2029-11-02','2029-11-02','in progress','2025-11-05 17:21:15','2025-11-05 17:21:15','0.00');
INSERT INTO `activities` VALUES('317','16','All Souls\' Day','','','0000-00-00','2030-11-02','2030-11-02','in progress','2025-11-05 17:21:15','2025-11-05 17:21:15','0.00');
INSERT INTO `activities` VALUES('319','14','Safer and Smarter Internet Access-PSWDO','','https://drive.google.com/drive/folders/1D1IxKXEKwpl1GLRVR1Tkgb9M-AueuSev?usp=drive_link','0000-00-00','2025-11-28','2025-11-28','completed','2025-11-06 21:39:00','2025-12-03 00:45:22','0.00');
INSERT INTO `activities` VALUES('320','14','PARTICIPATION IN THE ORIENTATION ON GENDER-RESPONSIVE WORKPLACES, GENDER-BASED VIOLENCE IN THE DIGITAL AGE, CYBERSECURITY, AND DATA PRIVACY','','https://drive.google.com/drive/folders/11iYEpYOzKvgBjRtxFBGW9zWetCdclG5Y?usp=drive_link','0000-00-00','2025-11-26','2025-11-28','completed','2025-11-07 00:03:09','2025-12-03 00:44:55','0.00');
INSERT INTO `activities` VALUES('322','7','Capacity Building Training on the Philippine Standard Industrial Classification (PSIC)','walay reply letter kay nagdecline c eLGU team due to non availability of personel,, thats why Kent Alico will attend the event as instructed by sir gab','https://drive.google.com/drive/folders/1JTqRi79K1YFMqhS95flxtpuow8s1mZUs?usp=drive_link','0000-00-00','2025-11-17','2025-11-19','completed','2025-11-14 18:10:04','2025-11-24 18:09:33','0.00');
INSERT INTO `activities` VALUES('323','3','To conduct provincial coordination, installation, and deployment of ETC equipment, and to support monitoring and communication needs related to Typhoon Tino and Typhoon Uwan response efforts.','To conduct provincial coordination, installation, and deployment of ETC equipment, and to support monitoring and communication needs related to Typhoon Tino and Typhoon Uwan response efforts.','https://drive.google.com/drive/folders/12dzzldeW1iAn0e29Ss9-Fjo6bH5cKiQy?usp=drive_link','0000-00-00','2025-11-09','2025-11-10','completed','2025-11-14 21:40:14','2025-11-14 21:41:26','0.00');
INSERT INTO `activities` VALUES('324','3','LGU Cagdianao Year End Celebration','','','0000-00-00','2025-12-18','2025-12-18','completed','2025-11-17 19:42:04','2025-12-19 21:18:11','0.00');
INSERT INTO `activities` VALUES('327','12','Offset','relax','','0000-00-00','2025-11-21','2025-11-21','completed','2025-11-24 16:55:58','2025-11-24 16:56:11','0.00');
INSERT INTO `activities` VALUES('328','12','Tatay Birthday','','','0000-00-00','2025-12-08','2025-12-08','completed','2025-11-25 19:28:49','2025-12-08 18:01:38','0.00');
INSERT INTO `activities` VALUES('329','12','Tatay Birthday','','','0000-00-00','2026-12-08','2026-12-08','in progress','2025-11-25 19:28:49','2025-11-25 19:28:49','0.00');
INSERT INTO `activities` VALUES('330','12','Tatay Birthday','','','0000-00-00','2027-12-08','2027-12-08','in progress','2025-11-25 19:28:49','2025-11-25 19:28:49','0.00');
INSERT INTO `activities` VALUES('331','12','Tatay Birthday','','','0000-00-00','2028-12-08','2028-12-08','in progress','2025-11-25 19:28:49','2025-11-25 19:28:49','0.00');
INSERT INTO `activities` VALUES('332','12','Tatay Birthday','','','0000-00-00','2029-12-08','2029-12-08','in progress','2025-11-25 19:28:49','2025-11-25 19:28:49','0.00');
INSERT INTO `activities` VALUES('333','12','Tatay Birthday','','','0000-00-00','2030-12-08','2030-12-08','in progress','2025-11-25 19:28:49','2025-11-25 19:28:49','0.00');
INSERT INTO `activities` VALUES('334','16','Christmas Eve','','','0000-00-00','2025-12-24','2025-12-24','completed','2025-11-25 19:31:38','2025-12-25 17:50:41','0.00');
INSERT INTO `activities` VALUES('335','16','Christmas Eve','','','0000-00-00','2026-12-24','2026-12-24','in progress','2025-11-25 19:31:38','2025-11-25 19:31:38','0.00');
INSERT INTO `activities` VALUES('336','16','Christmas Eve','','','0000-00-00','2027-12-24','2027-12-24','in progress','2025-11-25 19:31:38','2025-11-25 19:31:38','0.00');
INSERT INTO `activities` VALUES('337','16','Christmas Eve','','','0000-00-00','2028-12-24','2028-12-24','in progress','2025-11-25 19:31:38','2025-11-25 19:31:38','0.00');
INSERT INTO `activities` VALUES('338','16','Christmas Eve','','','0000-00-00','2029-12-24','2029-12-24','in progress','2025-11-25 19:31:38','2025-11-25 19:31:38','0.00');
INSERT INTO `activities` VALUES('339','16','Christmas Eve','','','0000-00-00','2030-12-24','2030-12-24','in progress','2025-11-25 19:31:38','2025-11-25 19:31:38','0.00');
INSERT INTO `activities` VALUES('340','14','CSB meeting','1:30pm','','0000-00-00','2025-11-25','2025-11-25','completed','2025-11-25 21:06:48','2025-11-26 19:12:58','0.00');
INSERT INTO `activities` VALUES('341','12','Offset','Nagkuha ug passport sa Butuan','','0000-00-00','2025-12-01','2025-12-01','completed','2025-11-27 02:21:17','2025-12-03 00:45:46','0.00');
INSERT INTO `activities` VALUES('342','12','Electric bill','','','0000-00-00','2025-12-30','2025-12-30','completed','2025-12-02 21:48:36','2026-01-07 11:27:55','0.00');
INSERT INTO `activities` VALUES('343','12','Electric bill','','','0000-00-00','2026-01-30','2026-01-30','in progress','2025-12-02 21:48:36','2025-12-02 21:48:36','0.00');
INSERT INTO `activities` VALUES('367','14','CSB Meeting','','','0000-00-00','2025-12-01','2025-12-01','completed','2025-12-03 16:26:33','2025-12-03 16:26:33','0.00');
INSERT INTO `activities` VALUES('368','5','DICT Caraga YEPA','need palit exchange gift','https://drive.google.com/drive/folders/1KfgHMSsX6S_aSl5y-4JMlGqgksN91wGG?usp=drive_link','0000-00-00','2025-12-15','2025-12-15','completed','2025-12-03 20:18:12','2025-12-19 21:19:09','0.00');
INSERT INTO `activities` VALUES('370','14','Podcast for \"Misinformation/Disinformation\"','','https://drive.google.com/drive/folders/1GjUv1SxOteC2JHwKPZBNd2G1gCKe6U8P?usp=drive_link','0000-00-00','2025-12-17','2025-12-17','completed','2025-12-09 22:41:34','2025-12-19 21:18:26','0.00');
INSERT INTO `activities` VALUES('371','14','PH AI standards (AI INFRASTRUCTURE SECURITY\"','Hello @all , good eve. Since naa tay assignment jud, as per instruction ni RD to have some inputs ang cyber team for the drafting of Philippine AI Standards, please refer to the docs template I created. Gi by pair nako ang topics and ang formatting kamo na bahala bsta formal and murag research/policy style sya. Mam @⁨Sheryl Manlunas⁩ is not included since focus muna sya sa mga procurements & admin tasks sa RO, and also tech support for PNPKI.','https://docs.google.com/document/d/1CMLPz-XOI1O2oUAmjwntcRm4N3ZgzkEgv0iE4Skiex4/edit?usp=sharing','0000-00-00','2025-12-11','2025-12-11','completed','2025-12-09 22:44:10','2025-12-13 04:48:28','0.00');
INSERT INTO `activities` VALUES('372','5','Work Suspension in DICT Offices on 29 December 2025(Monday) & 2 January 2026 (Friday)','Work Suspension in DICT Offices on 29 December 2025(Monday) & 2 January 2026 (Friday)','','0000-00-00','2025-12-29','2025-12-29','completed','2025-12-19 21:12:46','2026-01-01 03:21:33','0.00');
INSERT INTO `activities` VALUES('373','5','Work Suspension in DICT Offices on 29 December 2025(Monday) & 2 January 2026 (Friday)','Work Suspension in DICT Offices on 29 December 2025(Monday) & 2 January 2026 (Friday)','','0000-00-00','2026-01-02','2026-01-02','completed','2025-12-19 21:13:24','2026-01-07 14:51:55','0.00');
INSERT INTO `activities` VALUES('374','12','offset','relax','','0000-00-00','2025-12-19','2025-12-19','completed','2025-12-19 21:14:17','2025-12-19 21:17:19','0.00');
INSERT INTO `activities` VALUES('375','12','Motorcycle Payment','','','0000-00-00','2025-12-20','2025-12-20','completed','2025-12-22 17:45:25','2025-12-22 17:46:14','0.00');
INSERT INTO `activities` VALUES('376','12','Motorcycle Payment','','','0000-00-00','2026-01-20','2026-01-20','in progress','2025-12-22 17:45:25','2025-12-22 17:45:25','0.00');
INSERT INTO `activities` VALUES('377','12','Motorcycle Payment','','','0000-00-00','2026-02-20','2026-02-20','in progress','2025-12-22 17:45:25','2025-12-22 17:45:25','0.00');
INSERT INTO `activities` VALUES('378','12','Motorcycle Payment','','','0000-00-00','2026-03-20','2026-03-20','in progress','2025-12-22 17:45:25','2025-12-22 17:45:25','0.00');
INSERT INTO `activities` VALUES('379','12','Motorcycle Payment','','','0000-00-00','2026-04-20','2026-04-20','in progress','2025-12-22 17:45:25','2025-12-22 17:45:25','0.00');
INSERT INTO `activities` VALUES('380','12','Motorcycle Payment','','','0000-00-00','2026-05-20','2026-05-20','in progress','2025-12-22 17:45:25','2025-12-22 17:45:25','0.00');
INSERT INTO `activities` VALUES('381','12','Motorcycle Payment','','','0000-00-00','2026-06-20','2026-06-20','in progress','2025-12-22 17:45:25','2025-12-22 17:45:25','0.00');
INSERT INTO `activities` VALUES('382','12','Motorcycle Payment','','','0000-00-00','2026-07-20','2026-07-20','in progress','2025-12-22 17:45:25','2025-12-22 17:45:25','0.00');
INSERT INTO `activities` VALUES('383','12','Motorcycle Payment','','','0000-00-00','2026-08-20','2026-08-20','in progress','2025-12-22 17:45:25','2025-12-22 17:45:25','0.00');
INSERT INTO `activities` VALUES('384','12','Motorcycle Payment','','','0000-00-00','2026-09-20','2026-09-20','in progress','2025-12-22 17:45:25','2025-12-22 17:45:25','0.00');
INSERT INTO `activities` VALUES('385','12','Motorcycle Payment','','','0000-00-00','2026-10-20','2026-10-20','in progress','2025-12-22 17:45:25','2025-12-22 17:45:25','0.00');
INSERT INTO `activities` VALUES('386','12','Motorcycle Payment','','','0000-00-00','2026-11-20','2026-11-20','in progress','2025-12-22 17:45:25','2025-12-22 17:45:25','0.00');
INSERT INTO `activities` VALUES('387','12','Motorcycle Payment','','','0000-00-00','2026-12-20','2026-12-20','in progress','2025-12-22 17:45:25','2025-12-22 17:45:25','0.00');
INSERT INTO `activities` VALUES('388','12','Motorcycle Payment','','','0000-00-00','2027-01-20','2027-01-20','in progress','2025-12-22 17:45:25','2025-12-22 17:45:25','0.00');
INSERT INTO `activities` VALUES('389','12','Motorcycle Payment','','','0000-00-00','2027-02-20','2027-02-20','in progress','2025-12-22 17:45:25','2025-12-22 17:45:25','0.00');
INSERT INTO `activities` VALUES('390','12','Motorcycle Payment','','','0000-00-00','2027-03-20','2027-03-20','in progress','2025-12-22 17:45:25','2025-12-22 17:45:25','0.00');
INSERT INTO `activities` VALUES('391','12','Motorcycle Payment','','','0000-00-00','2027-04-20','2027-04-20','in progress','2025-12-22 17:45:25','2025-12-22 17:45:25','0.00');
INSERT INTO `activities` VALUES('392','12','Motorcycle Payment','','','0000-00-00','2027-05-20','2027-05-20','in progress','2025-12-22 17:45:25','2025-12-22 17:45:25','0.00');
INSERT INTO `activities` VALUES('393','12','Motorcycle Payment','','','0000-00-00','2027-06-20','2027-06-20','in progress','2025-12-22 17:45:25','2025-12-22 17:45:25','0.00');
INSERT INTO `activities` VALUES('394','12','Motorcycle Payment','','','0000-00-00','2027-07-20','2027-07-20','in progress','2025-12-22 17:45:25','2025-12-22 17:45:25','0.00');
INSERT INTO `activities` VALUES('395','12','Motorcycle Payment','','','0000-00-00','2027-08-20','2027-08-20','in progress','2025-12-22 17:45:25','2025-12-22 17:45:25','0.00');
INSERT INTO `activities` VALUES('396','16','New Year\'s Day','','','0000-00-00','2026-01-01','2026-01-01','completed','2025-12-22 17:49:16','2026-01-07 14:51:47','0.00');
INSERT INTO `activities` VALUES('397','16','New Year\'s Day','','','0000-00-00','2027-01-01','2027-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('398','16','New Year\'s Day','','','0000-00-00','2028-01-01','2028-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('399','16','New Year\'s Day','','','0000-00-00','2029-01-01','2029-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('400','16','New Year\'s Day','','','0000-00-00','2030-01-01','2030-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('401','16','New Year\'s Day','','','0000-00-00','2031-01-01','2031-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('402','16','New Year\'s Day','','','0000-00-00','2032-01-01','2032-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('403','16','New Year\'s Day','','','0000-00-00','2033-01-01','2033-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('404','16','New Year\'s Day','','','0000-00-00','2034-01-01','2034-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('405','16','New Year\'s Day','','','0000-00-00','2035-01-01','2035-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('406','16','New Year\'s Day','','','0000-00-00','2036-01-01','2036-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('407','16','New Year\'s Day','','','0000-00-00','2037-01-01','2037-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('408','16','New Year\'s Day','','','0000-00-00','2038-01-01','2038-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('409','16','New Year\'s Day','','','0000-00-00','2039-01-01','2039-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('410','16','New Year\'s Day','','','0000-00-00','2040-01-01','2040-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('411','16','New Year\'s Day','','','0000-00-00','2041-01-01','2041-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('412','16','New Year\'s Day','','','0000-00-00','2042-01-01','2042-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('413','16','New Year\'s Day','','','0000-00-00','2043-01-01','2043-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('414','16','New Year\'s Day','','','0000-00-00','2044-01-01','2044-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('415','16','New Year\'s Day','','','0000-00-00','2045-01-01','2045-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('416','16','New Year\'s Day','','','0000-00-00','2046-01-01','2046-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('417','16','New Year\'s Day','','','0000-00-00','2047-01-01','2047-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('418','16','New Year\'s Day','','','0000-00-00','2048-01-01','2048-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('419','16','New Year\'s Day','','','0000-00-00','2049-01-01','2049-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('420','16','New Year\'s Day','','','0000-00-00','2050-01-01','2050-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('421','16','New Year\'s Day','','','0000-00-00','2051-01-01','2051-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('422','16','New Year\'s Day','','','0000-00-00','2052-01-01','2052-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('423','16','New Year\'s Day','','','0000-00-00','2053-01-01','2053-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('424','16','New Year\'s Day','','','0000-00-00','2054-01-01','2054-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('425','16','New Year\'s Day','','','0000-00-00','2055-01-01','2055-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('426','16','New Year\'s Day','','','0000-00-00','2056-01-01','2056-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('427','16','New Year\'s Day','','','0000-00-00','2057-01-01','2057-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('428','16','New Year\'s Day','','','0000-00-00','2058-01-01','2058-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('429','16','New Year\'s Day','','','0000-00-00','2059-01-01','2059-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('430','16','New Year\'s Day','','','0000-00-00','2060-01-01','2060-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('431','16','New Year\'s Day','','','0000-00-00','2061-01-01','2061-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('432','16','New Year\'s Day','','','0000-00-00','2062-01-01','2062-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('433','16','New Year\'s Day','','','0000-00-00','2063-01-01','2063-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('434','16','New Year\'s Day','','','0000-00-00','2064-01-01','2064-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('435','16','New Year\'s Day','','','0000-00-00','2065-01-01','2065-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('436','16','New Year\'s Day','','','0000-00-00','2066-01-01','2066-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('437','16','New Year\'s Day','','','0000-00-00','2067-01-01','2067-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('438','16','New Year\'s Day','','','0000-00-00','2068-01-01','2068-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('439','16','New Year\'s Day','','','0000-00-00','2069-01-01','2069-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('440','16','New Year\'s Day','','','0000-00-00','2070-01-01','2070-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('441','16','New Year\'s Day','','','0000-00-00','2071-01-01','2071-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('442','16','New Year\'s Day','','','0000-00-00','2072-01-01','2072-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('443','16','New Year\'s Day','','','0000-00-00','2073-01-01','2073-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('444','16','New Year\'s Day','','','0000-00-00','2074-01-01','2074-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('445','16','New Year\'s Day','','','0000-00-00','2075-01-01','2075-01-01','in progress','2025-12-22 17:49:16','2025-12-22 17:49:16','0.00');
INSERT INTO `activities` VALUES('446','12','offset','rest','','0000-00-00','2025-12-23','2025-12-23','completed','2025-12-25 00:13:32','2025-12-25 00:13:32','0.00');
INSERT INTO `activities` VALUES('447','5','REGIONAL OFFICE ORDER NO. R-13-2025-0129','work from home','','0000-00-00','2025-12-26','2025-12-26','completed','2025-12-25 00:14:02','2026-01-07 11:27:42','0.00');
INSERT INTO `activities` VALUES('448','3','Duty Start','','','0000-00-00','2026-01-06','2026-01-06','completed','2026-01-07 10:55:53','2026-01-07 10:55:53','0.00');
INSERT INTO `activities` VALUES('449','3','DICT PDI Meeting','','','0000-00-00','2026-01-07','2026-01-07','completed','2026-01-07 10:56:33','2026-01-08 08:55:36','0.00');
INSERT INTO `activities` VALUES('450','10','Meeting with DCD PMT','','','0000-00-00','2026-01-07','2026-01-07','completed','2026-01-07 10:57:06','2026-01-07 14:51:36','0.00');
INSERT INTO `activities` VALUES('451','12','SpayLater Payment','','','0000-00-00','2026-02-01','2026-02-01','in progress','2026-01-07 14:54:06','2026-01-07 14:54:06','0.00');
INSERT INTO `activities` VALUES('452','12','SpayLater Payment','','','0000-00-00','2026-03-01','2026-03-01','in progress','2026-01-07 14:54:06','2026-01-07 14:54:06','0.00');
INSERT INTO `activities` VALUES('453','12','SpayLater Payment','','','0000-00-00','2026-04-01','2026-04-01','in progress','2026-01-07 14:54:06','2026-01-07 14:54:06','0.00');
INSERT INTO `activities` VALUES('454','12','SpayLater Payment','','','0000-00-00','2026-05-01','2026-05-01','in progress','2026-01-07 14:54:06','2026-01-07 14:54:06','0.00');
INSERT INTO `activities` VALUES('455','12','SpayLater Payment','','','0000-00-00','2026-06-01','2026-06-01','in progress','2026-01-07 14:54:06','2026-01-07 14:54:06','0.00');
INSERT INTO `activities` VALUES('456','12','SpayLater Payment','','','0000-00-00','2026-07-01','2026-07-01','in progress','2026-01-07 14:54:06','2026-01-07 14:54:06','0.00');
INSERT INTO `activities` VALUES('457','12','SpayLater Payment','','','0000-00-00','2026-08-01','2026-08-01','in progress','2026-01-07 14:54:06','2026-01-07 14:54:06','0.00');
INSERT INTO `activities` VALUES('458','12','SpayLater Payment','','','0000-00-00','2026-09-01','2026-09-01','in progress','2026-01-07 14:54:06','2026-01-07 14:54:06','0.00');
INSERT INTO `activities` VALUES('459','12','SpayLater Payment','','','0000-00-00','2026-10-01','2026-10-01','in progress','2026-01-07 14:54:06','2026-01-07 14:54:06','0.00');
INSERT INTO `activities` VALUES('460','12','SpayLater Payment','','','0000-00-00','2026-11-01','2026-11-01','in progress','2026-01-07 14:54:06','2026-01-07 14:54:06','0.00');
INSERT INTO `activities` VALUES('461','12','SpayLater Payment','','','0000-00-00','2026-12-01','2026-12-01','in progress','2026-01-07 14:54:06','2026-01-07 14:54:06','0.00');
INSERT INTO `activities` VALUES('462','12','SpayLater Payment','','','0000-00-00','2027-01-01','2027-01-01','in progress','2026-01-07 14:54:06','2026-01-07 14:54:06','0.00');
INSERT INTO `activities` VALUES('463','12','SpayLater Payment','','','0000-00-00','2027-02-01','2027-02-01','in progress','2026-01-07 14:54:06','2026-01-07 14:54:06','0.00');
INSERT INTO `activities` VALUES('464','12','SpayLater Payment','','','0000-00-00','2027-03-01','2027-03-01','in progress','2026-01-07 14:54:06','2026-01-07 14:54:06','0.00');
INSERT INTO `activities` VALUES('465','12','SpayLater Payment','','','0000-00-00','2027-04-01','2027-04-01','in progress','2026-01-07 14:54:06','2026-01-07 14:54:06','0.00');
INSERT INTO `activities` VALUES('466','12','SpayLater Payment','','','0000-00-00','2027-05-01','2027-05-01','in progress','2026-01-07 14:54:06','2026-01-07 14:54:06','0.00');
INSERT INTO `activities` VALUES('467','12','SpayLater Payment','','','0000-00-00','2027-06-01','2027-06-01','in progress','2026-01-07 14:54:06','2026-01-07 14:54:06','0.00');
INSERT INTO `activities` VALUES('468','12','SpayLater Payment','','','0000-00-00','2027-07-01','2027-07-01','in progress','2026-01-07 14:54:06','2026-01-07 14:54:06','0.00');
INSERT INTO `activities` VALUES('469','12','SpayLater Payment','','','0000-00-00','2027-08-01','2027-08-01','in progress','2026-01-07 14:54:06','2026-01-07 14:54:06','0.00');
INSERT INTO `activities` VALUES('470','12','SpayLater Payment','','','0000-00-00','2027-09-01','2027-09-01','in progress','2026-01-07 14:54:06','2026-01-07 14:54:06','0.00');
INSERT INTO `activities` VALUES('471','12','SpayLater Payment','','','0000-00-00','2027-10-01','2027-10-01','in progress','2026-01-07 14:54:06','2026-01-07 14:54:06','0.00');
INSERT INTO `activities` VALUES('472','12','SpayLater Payment','','','0000-00-00','2027-11-01','2027-11-01','in progress','2026-01-07 14:54:06','2026-01-07 14:54:06','0.00');
INSERT INTO `activities` VALUES('473','12','SpayLater Payment','','','0000-00-00','2027-12-01','2027-12-01','in progress','2026-01-07 14:54:06','2026-01-07 14:54:06','0.00');
INSERT INTO `activities` VALUES('474','12','Electric bill','','','0000-00-00','2026-02-28','2026-02-28','in progress','2026-01-07 14:55:27','2026-01-07 14:55:27','0.00');
INSERT INTO `activities` VALUES('475','12','Electric bill','','','0000-00-00','2026-03-28','2026-03-28','in progress','2026-01-07 14:55:27','2026-01-07 14:55:27','0.00');
INSERT INTO `activities` VALUES('476','12','Electric bill','','','0000-00-00','2026-04-28','2026-04-28','in progress','2026-01-07 14:55:27','2026-01-07 14:55:27','0.00');
INSERT INTO `activities` VALUES('477','12','Electric bill','','','0000-00-00','2026-05-28','2026-05-28','in progress','2026-01-07 14:55:27','2026-01-07 14:55:27','0.00');
INSERT INTO `activities` VALUES('478','12','Electric bill','','','0000-00-00','2026-06-28','2026-06-28','in progress','2026-01-07 14:55:27','2026-01-07 14:55:27','0.00');
INSERT INTO `activities` VALUES('479','12','Electric bill','','','0000-00-00','2026-07-28','2026-07-28','in progress','2026-01-07 14:55:27','2026-01-07 14:55:27','0.00');
INSERT INTO `activities` VALUES('480','12','Electric bill','','','0000-00-00','2026-08-28','2026-08-28','in progress','2026-01-07 14:55:27','2026-01-07 14:55:27','0.00');
INSERT INTO `activities` VALUES('481','12','Electric bill','','','0000-00-00','2026-09-28','2026-09-28','in progress','2026-01-07 14:55:27','2026-01-07 14:55:27','0.00');
INSERT INTO `activities` VALUES('482','12','Electric bill','','','0000-00-00','2026-10-28','2026-10-28','in progress','2026-01-07 14:55:27','2026-01-07 14:55:27','0.00');
INSERT INTO `activities` VALUES('483','12','Electric bill','','','0000-00-00','2026-11-28','2026-11-28','in progress','2026-01-07 14:55:27','2026-01-07 14:55:27','0.00');
INSERT INTO `activities` VALUES('484','12','Electric bill','','','0000-00-00','2026-12-28','2026-12-28','in progress','2026-01-07 14:55:27','2026-01-07 14:55:27','0.00');
INSERT INTO `activities` VALUES('485','12','Electric bill','','','0000-00-00','2027-01-28','2027-01-28','in progress','2026-01-07 14:55:27','2026-01-07 14:55:27','0.00');
INSERT INTO `activities` VALUES('486','12','Electric bill','','','0000-00-00','2027-02-28','2027-02-28','in progress','2026-01-07 14:55:27','2026-01-07 14:55:27','0.00');
INSERT INTO `activities` VALUES('487','12','Electric bill','','','0000-00-00','2027-03-28','2027-03-28','in progress','2026-01-07 14:55:27','2026-01-07 14:55:27','0.00');
INSERT INTO `activities` VALUES('488','12','Electric bill','','','0000-00-00','2027-04-28','2027-04-28','in progress','2026-01-07 14:55:27','2026-01-07 14:55:27','0.00');
INSERT INTO `activities` VALUES('489','12','Electric bill','','','0000-00-00','2027-05-28','2027-05-28','in progress','2026-01-07 14:55:27','2026-01-07 14:55:27','0.00');
INSERT INTO `activities` VALUES('490','12','Electric bill','','','0000-00-00','2027-06-28','2027-06-28','in progress','2026-01-07 14:55:27','2026-01-07 14:55:27','0.00');
INSERT INTO `activities` VALUES('491','12','Electric bill','','','0000-00-00','2027-07-28','2027-07-28','in progress','2026-01-07 14:55:27','2026-01-07 14:55:27','0.00');
INSERT INTO `activities` VALUES('492','12','Electric bill','','','0000-00-00','2027-08-28','2027-08-28','in progress','2026-01-07 14:55:27','2026-01-07 14:55:27','0.00');
INSERT INTO `activities` VALUES('493','12','Electric bill','','','0000-00-00','2027-09-28','2027-09-28','in progress','2026-01-07 14:55:27','2026-01-07 14:55:27','0.00');
INSERT INTO `activities` VALUES('494','12','Electric bill','','','0000-00-00','2027-10-28','2027-10-28','in progress','2026-01-07 14:55:27','2026-01-07 14:55:27','0.00');
INSERT INTO `activities` VALUES('495','12','Electric bill','','','0000-00-00','2027-11-28','2027-11-28','in progress','2026-01-07 14:55:27','2026-01-07 14:55:27','0.00');
INSERT INTO `activities` VALUES('496','12','Electric bill','','','0000-00-00','2027-12-28','2027-12-28','in progress','2026-01-07 14:55:27','2026-01-07 14:55:27','0.00');
INSERT INTO `activities` VALUES('497','14','CSB Team Thursday Huddle','','','0000-00-00','2026-01-08','2026-01-08','in progress','2026-01-08 10:35:02','2026-01-08 10:35:02','0.00');
INSERT INTO `activities` VALUES('498','14','CSB Team Thursday Huddle','','','0000-00-00','2026-01-15','2026-01-15','in progress','2026-01-08 10:35:02','2026-01-08 10:35:02','0.00');
INSERT INTO `activities` VALUES('499','14','CSB Team Thursday Huddle','','','0000-00-00','2026-01-22','2026-01-22','in progress','2026-01-08 10:35:02','2026-01-08 10:35:02','0.00');

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
) ENGINE=InnoDB AUTO_INCREMENT=113 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
INSERT INTO `activity_requirements` VALUES('106','133','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2026-01-07 11:27:24','2026-01-07 11:27:24');
INSERT INTO `activity_requirements` VALUES('107','447','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2026-01-07 11:27:42','2026-01-07 11:27:42');
INSERT INTO `activity_requirements` VALUES('108','342','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2026-01-07 11:27:55','2026-01-07 11:27:55');
INSERT INTO `activity_requirements` VALUES('109','450','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2026-01-07 14:51:36','2026-01-07 14:51:36');
INSERT INTO `activity_requirements` VALUES('110','396','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2026-01-07 14:51:47','2026-01-07 14:51:47');
INSERT INTO `activity_requirements` VALUES('111','373','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2026-01-07 14:51:55','2026-01-07 14:51:55');
INSERT INTO `activity_requirements` VALUES('112','449','0','0','0','0',NULL,'0','0','0','0','0','0',NULL,'2026-01-08 08:55:36','2026-01-08 08:55:36');

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
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `projects` VALUES('2','CSB-CERT','DICT 13 Project Implementation','2025-01-01','2030-12-31','in progress','2025-05-18 23:24:29','2026-01-07 15:40:29');
INSERT INTO `projects` VALUES('3','DICT PDI','Provincial Activity / Initiative','2025-01-01','2030-12-31','in progress','2025-05-19 00:33:43','2026-01-07 15:40:17');
INSERT INTO `projects` VALUES('4','ILCDB','DICT 13 Project Implementation','2025-01-01','2030-12-31','in progress','2025-05-19 19:04:16','2026-01-07 14:46:19');
INSERT INTO `projects` VALUES('5','DICT Caraga','DICT 13 Project Implementation','2025-01-01','2030-12-31','in progress','2025-05-19 22:39:29','2026-01-07 14:46:30');
INSERT INTO `projects` VALUES('6','eGOV','DICT 13 Project Implementation','2025-01-01','2030-12-31','in progress','2025-05-19 22:53:15','2026-01-07 14:45:36');
INSERT INTO `projects` VALUES('7','eLGU','DICT 13 Project Implementation','2025-01-01','2030-12-31','in progress','2025-05-19 22:54:20','2026-01-07 14:45:14');
INSERT INTO `projects` VALUES('9','Wifi','DICT 13 Project Implementation','2025-01-01','2030-12-31','in progress','2025-05-19 23:56:30','2026-01-07 14:44:48');
INSERT INTO `projects` VALUES('10','CSB-PNPKI','DICT 13 Project Implementation','2025-01-01','2030-12-31','in progress','2025-05-20 00:02:22','2026-01-07 14:44:17');
INSERT INTO `projects` VALUES('12','Personal','Kent D. Alico','2025-01-01','2030-12-31','in progress','2025-05-20 00:44:52','2026-01-07 14:43:46');
INSERT INTO `projects` VALUES('13','IIDB','DICT 13 Project Implementation','2025-01-01','2030-12-31','in progress','2025-05-20 16:36:33','2026-01-07 14:43:21');
INSERT INTO `projects` VALUES('14','CSB-CEISMD','DICT 13 Project Implementation','2025-01-01','2030-12-31','in progress','2025-05-28 17:40:50','2026-01-07 14:42:18');
INSERT INTO `projects` VALUES('16','PH Holiday','Holiday','2025-01-01','2030-12-31','in progress','2025-06-16 05:11:04','2026-01-07 14:41:26');
INSERT INTO `projects` VALUES('17','MISS','DICT 13 Project Implementation','2025-01-01','2030-12-31','in progress','2025-10-27 16:22:34','2026-01-07 14:41:55');
INSERT INTO `projects` VALUES('18','GECS','DICT 13 Project Implementation','2025-01-01','2030-12-31','in progress','2025-12-03 17:33:40','2026-01-07 14:41:43');

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
INSERT INTO `tev_claims` VALUES('18','TEV-2025-00013','User 1','DICT','2025-11-25','gipadala ni mam jov 21/1/2025','2400.00','https://drive.google.com/drive/folders/1uiyuXQkbt7vj2jfTB-G_bixHwuTf9DNq?usp=sharing','Paid','2025-12-23','10','CSB-PNPKI 2025','183','1','2025-11-25 19:25:43','2026-01-08 13:07:16');
INSERT INTO `tev_claims` VALUES('19','TEV-2025-00014','User 1','DICT','2025-12-03','Nov 2-10, 2025\r\n12/08/2025','4200.00','https://drive.google.com/drive/folders/1_9LZVBDIRPZbOwNPqdZm1nJz8v40TJvQ?usp=drive_link','Paid','2025-12-23','18','GECS 2025','310','1','2025-12-03 17:38:58','2025-12-31 23:54:58');
INSERT INTO `tev_claims` VALUES('20','TEV-2025-00015','User 1','DICT','2025-12-03','12/08/2025','900.00','https://drive.google.com/drive/folders/1D1IxKXEKwpl1GLRVR1Tkgb9M-AueuSev?usp=drive_link','Paid','2025-12-23','14','CSB-CEISMD 2025','319','1','2025-12-03 17:42:54','2025-12-31 23:47:12');
INSERT INTO `tev_claims` VALUES('21','TEV-2025-00016','User 1','DICT','2025-12-04','naa na kang mam christine- 12/03/2025\r\npaid-1200-12/19/2025','1200.00','https://drive.google.com/drive/folders/11FyUeUXMCIcAKY8aRM2YpwPHab6aEU-U?usp=drive_link','Paid',NULL,'9','Wifi 2025','155','1','2025-12-04 16:40:18','2025-12-22 16:47:29');
INSERT INTO `tev_claims` VALUES('22','TEV-2025-00017','User 1','DICT','2025-12-17','gihatag nako kay mam jov\r\n17/12/2025','10470.00','https://drive.google.com/drive/folders/1eD_GLb2yGL66Cl9x3WzfCvrg7EuOC5tB?usp=drive_link','Paid','2025-12-23','2','CSB-CERT 2025','309','1','2025-12-18 00:02:12','2025-12-31 23:51:41');
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
-- Backup completed successfully at: 2026-01-08 06:15:22
