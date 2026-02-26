SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

DROP TABLE IF EXISTS `activities`;

CREATE TABLE `activities` (
  `id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `google_drive_link` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Google Drive link for activity documents',
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
) ENGINE=InnoDB AUTO_INCREMENT=148 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `activities` VALUES("8","7","eBOSS Technical Support","Technical Support and Provide Desktop Computers",NULL,"0000-00-00","2025-01-13","2025-01-16","completed","2025-05-19 15:14:12","2025-05-19 15:14:12","0.00","1.00");
INSERT INTO `activities` VALUES("10","4","DLT-CNHS","DLT Training (Conduct CA 23/1/2025)",NULL,"0000-00-00","2025-01-20","2025-01-24","completed","2025-05-19 15:26:51","2025-05-19 15:26:51","0.00","1.00");
INSERT INTO `activities` VALUES("12","7","e-readyness for eLGU systems","Virtual",NULL,"0000-00-00","2025-02-03","2025-02-03","completed","2025-05-19 15:38:34","2025-05-19 16:09:45","0.00","1.00");
INSERT INTO `activities` VALUES("13","2","Legal Information and Data Privacy Awareness","Homestay Owners, Operators and Staff in Province of Dinagat Islands.",NULL,"0000-00-00","2025-02-06","2025-02-06","completed","2025-05-19 15:43:36","2025-05-19 15:43:36","0.00","1.00");
INSERT INTO `activities` VALUES("14","9","VSAT OPCEN restoration and Comelec Internet Provision Support","Wifi Provision",NULL,"0000-00-00","2025-02-07","2025-02-08","completed","2025-05-19 15:57:49","2025-05-19 16:09:27","0.00","1.00");
INSERT INTO `activities` VALUES("15","10","PNPKI UT in LGU Loreto","TO from feb 11-12, 2025",NULL,"0000-00-00","2025-02-12","2025-05-12","completed","2025-05-19 16:20:33","2025-05-19 16:25:51","0.00","1.00");
INSERT INTO `activities` VALUES("16","2","[Dry Run] Safer Internet Day","Conducted by CSB Team",NULL,"0000-00-00","2025-02-13","2025-02-13","completed","2025-05-19 16:26:39","2025-05-19 16:29:13","0.00","1.00");
INSERT INTO `activities` VALUES("17","2","Cybersecurity Awareness in Del Pilar NHS","Emersion Students",NULL,"0000-00-00","2025-02-14","2025-02-14","completed","2025-05-19 16:30:56","2025-05-19 16:30:56","0.00","1.00");
INSERT INTO `activities` VALUES("18","2","CapDev in Cybersec team in Butuan","travel date feb 17-21",NULL,"0000-00-00","2025-02-18","2025-02-21","completed","2025-05-19 16:35:26","2025-05-19 16:35:26","0.00","1.00");
INSERT INTO `activities` VALUES("19","10","𝗢𝗿𝗶𝗲𝗻𝘁𝗮𝘁𝗶𝗼𝗻 𝗼𝗻 𝘁𝗵𝗲 𝗣𝗡𝗣𝗞𝗜 𝗢𝗥𝗦 𝗪𝗲𝗯 𝗠𝗮𝗻𝗮𝗴𝗲𝗿 (𝗘𝗻𝗵𝗮𝗻𝗰𝗲𝗺𝗲𝗻𝘁)","Virtual",NULL,"0000-00-00","2025-02-27","2025-02-27","completed","2025-05-19 16:37:56","2025-05-19 16:37:56","0.00","1.00");
INSERT INTO `activities` VALUES("20","3","Women\'s month meeting in Provincial Capitol","Yamashiro Bldg",NULL,"0000-00-00","2025-03-04","2025-03-04","completed","2025-05-19 16:41:13","2025-05-19 16:41:13","0.00","1.00");
INSERT INTO `activities` VALUES("21","9","Assist restore Vsat in RHU San Jose","with Engr. Guma",NULL,"0000-00-00","2025-03-05","2025-03-05","completed","2025-05-19 16:42:53","2025-05-19 16:42:53","0.00","1.00");
INSERT INTO `activities` VALUES("22","12","2025 ICPEP Caraga 5th regional convention, 3rd Student Congress and 7th General Assembly","with Rv maribao",NULL,"0000-00-00","2025-03-06","2025-03-07","completed","2025-05-19 16:48:51","2025-05-19 16:49:10","0.00","1.00");
INSERT INTO `activities` VALUES("23","4","Emersion in R. Ecleo NHS","",NULL,"0000-00-00","2025-02-24","2025-03-04","completed","2025-05-19 16:51:43","2025-05-19 16:51:43","0.00","1.00");
INSERT INTO `activities` VALUES("24","2","CNHS research about online threats.","Accommodating CNHS for their research about online threats.",NULL,"0000-00-00","2025-03-10","2025-03-10","completed","2025-05-19 17:02:41","2025-05-19 17:02:41","0.00","1.00");
INSERT INTO `activities` VALUES("25","4","UCE","Travel date March 11, 2025",NULL,"0000-00-00","2025-03-12","2025-03-12","completed","2025-05-19 17:04:43","2025-05-19 17:04:43","0.00","1.00");
INSERT INTO `activities` VALUES("26","5","GAD Training","Butuan City",NULL,"0000-00-00","2025-03-13","2025-03-14","completed","2025-05-19 17:05:39","2025-05-19 17:05:39","0.00","1.00");
INSERT INTO `activities` VALUES("27","3","Test of Fundamental Academic Skills","Accommodate the CNHS",NULL,"0000-00-00","2025-03-17","2025-03-21","completed","2025-05-19 17:07:06","2025-05-19 17:07:06","0.00","1.00");
INSERT INTO `activities` VALUES("28","2","Engagement in Cagdianao NHS","with Engr. Albert",NULL,"0000-00-00","2025-03-17","2025-03-17","completed","2025-05-19 17:24:48","2025-05-19 17:24:48","0.00","1.00");
INSERT INTO `activities` VALUES("29","4","LGU Tubajon-Implementation for ILCDB ,Lot Provision Update and Computerization","Travel to Tubajon, with engr. guma and engr. albert",NULL,"0000-00-00","2025-03-18","2025-03-19","completed","2025-05-19 17:28:36","2025-05-19 17:29:00","0.00","1.00");
INSERT INTO `activities` VALUES("30","2","Women\'s Month Caravan in Municipality of Dinagat, PDI","I\'m presenting the csb awareness videos( March 24, 2025)",NULL,"0000-00-00","2025-03-21","2025-03-26","completed","2025-05-19 20:53:40","2025-05-19 20:59:27","0.00","1.00");
INSERT INTO `activities` VALUES("31","4","DJEMC ICT Student OJT","OJT in DICT PDI",NULL,"0000-00-00","2025-03-19","2025-06-30","completed","2025-05-19 20:56:23","2025-07-01 00:10:21","0.00","1.00");
INSERT INTO `activities` VALUES("32","12","Interview and examination for Computer Maintenance technologist II","DICT Regional Office",NULL,"0000-00-00","2025-03-25","2025-03-25","completed","2025-05-19 20:58:26","2025-05-19 20:58:26","0.00","1.00");
INSERT INTO `activities` VALUES("33","2","DTC Computer Configuration","Pre-Configuration",NULL,"0000-00-00","2025-03-26","2025-12-31","in progress","2025-05-19 21:01:07","2025-06-23 10:52:42","0.00","1.00");
INSERT INTO `activities` VALUES("34","2","DTC Computer Asset Recording and Maintenance","DTC PDI",NULL,"0000-00-00","2025-03-27","2025-12-31","in progress","2025-05-19 21:09:37","2025-06-23 10:53:06","0.00","1.00");
INSERT INTO `activities` VALUES("35","10","PNPKI Awareness to the DJEMC OJT","For application of PNPKI certificates",NULL,"0000-00-00","2025-04-02","2025-04-02","completed","2025-05-20 08:29:33","2025-05-20 08:31:00","0.00","1.00");
INSERT INTO `activities` VALUES("36","10","PNPKI UT in SDO Dinagat","Alber Central Elem School, Cagdianao Central Elem School and DREESMNHS",NULL,"0000-00-00","2025-04-07","2025-04-09","completed","2025-05-20 08:33:23","2025-06-13 13:24:04","0.00","1.00");
INSERT INTO `activities` VALUES("37","2","MoU Signing of DICT R13 and CNHS and Promotion of Programs and Projects","Cybersecurity Caravan in CHNS",NULL,"0000-00-00","2025-04-10","2025-04-10","completed","2025-05-20 08:34:44","2025-05-20 08:34:44","0.00","1.00");
INSERT INTO `activities` VALUES("38","13","DICT CARAGA - PDI STARTUP Ecosystem Mapping Workshop","with sir Daj, at DOc Me",NULL,"0000-00-00","2025-04-14","2025-04-16","completed","2025-05-20 08:38:22","2025-05-20 08:38:22","0.00","1.00");
INSERT INTO `activities` VALUES("39","4","PDI Workforce: Cybersecurity Competency Framework Training","in DICT Surigao City",NULL,"0000-00-00","2025-04-21","2025-04-23","completed","2025-05-20 08:39:59","2025-05-20 08:39:59","0.00","1.00");
INSERT INTO `activities` VALUES("40","2","SDO Dinagat Phishing Incident","FB Page",NULL,"0000-00-00","2025-04-25","2025-04-25","completed","2025-05-20 08:43:26","2025-05-20 08:43:26","0.00","1.00");
INSERT INTO `activities` VALUES("41","13","DOST PDI for the \"LGU San Jose Gap Assessment and Design Thinking\"","with engr. Guma at Happynest Homestay",NULL,"0000-00-00","2025-04-28","2025-04-30","completed","2025-05-20 08:44:40","2025-05-20 08:44:40","0.00","1.00");
INSERT INTO `activities` VALUES("42","3","COMELEC HUB Assignment PDI (FTS)","TH Personnel",NULL,"0000-00-00","2025-05-07","2025-05-07","completed","2025-05-20 08:48:01","2025-05-20 08:50:15","0.00","1.00");
INSERT INTO `activities` VALUES("43","3","COMELEC HUB Assignment PDI NLE 2025","TH Personnel",NULL,"0000-00-00","2025-05-12","2025-05-12","completed","2025-05-20 08:50:06","2025-05-20 08:50:06","0.00","1.00");
INSERT INTO `activities` VALUES("44","10","Psychometric Exam for PNPKI RAA","",NULL,"0000-00-00","2025-05-21","2025-05-21","completed","2025-05-20 08:51:47","2025-05-23 13:43:19","0.00","1.00");
INSERT INTO `activities` VALUES("45","2","𝗦𝗰𝗵𝗲𝗱𝘂𝗹𝗲 𝗼𝗳 𝗣𝗿𝗼𝗷𝗲𝗰𝘁 𝗖𝗼𝗻𝘀𝘂𝗹𝘁𝗮𝘁𝗶𝗼𝗻 𝗼𝗻 𝗦𝗣𝗠𝗦 𝗧𝗲𝗺𝗽𝗹𝗮𝘁𝗲 𝗮𝗻𝗱 𝗚𝘂𝗶𝗱𝗲𝗹𝗶𝗻𝗲𝘀","meeting for the IPCR",NULL,"0000-00-00","2025-05-30","2025-05-30","completed","2025-05-20 08:55:08","2025-06-15 21:35:46","0.00","1.00");
INSERT INTO `activities` VALUES("46","4","\"Futures Thinking and Strategic Foresight\" Workshop","with mam jai",NULL,"0000-00-00","2025-05-21","2025-05-23","completed","2025-05-23 13:43:02","2025-05-23 13:43:02","0.00","1.00");
INSERT INTO `activities` VALUES("47","9","Tubajon Connectivity Provision of Job fair","solo flight/ TO 18-20",NULL,"0000-00-00","2025-06-19","2025-06-20","completed","2025-05-23 18:58:05","2025-06-20 20:42:22","0.00","1.00");
INSERT INTO `activities` VALUES("48","5","NICTM Culmination & Midyear Planning and Assessment","travel time 24 morning",NULL,"0000-00-00","2025-06-25","2025-06-27","completed","2025-05-23 19:31:26","2025-07-01 20:16:15","0.00","1.00");
INSERT INTO `activities` VALUES("49","4","Kick-Off Launching  | SPARK Blockchain Cryptocurrency Specialist Certification Launching","Assist",NULL,"0000-00-00","2025-05-26","2025-05-26","completed","2025-05-26 09:10:45","2025-05-27 16:55:44","0.00","1.00");
INSERT INTO `activities` VALUES("50","2","PDI Spark: Blockchain and Cryptocurrency Specialist Certification","Assist",NULL,"0000-00-00","2025-05-26","2025-06-15","completed","2025-05-26 09:34:30","2025-05-27 16:55:02","0.00","1.00");
INSERT INTO `activities` VALUES("54","2","CSB Meeting","Updates on sending of invites and registration of the following:\n-ASEAN-Japan Video Competition\n-Hack4Gov\n-Cyber Range\n2. Upcoming activities both Cyber and PNPKI\n3. Other matters, issues and concerns\n\nMeeting link: meet.google.com/qbv-ugkr-kzf",NULL,"0000-00-00","2025-05-27","2025-05-27","completed","2025-05-27 08:56:53","2025-05-27 15:05:39","0.00","1.00");
INSERT INTO `activities` VALUES("55","4","TECH2CLASS: \"Unlocking Digital Potential for Digital Literacy Training","Spreedsheet",NULL,"0000-00-00","2025-05-14","2025-05-16","completed","2025-05-27 15:05:10","2025-05-27 15:05:10","0.00","1.00");
INSERT INTO `activities` VALUES("60","3","ICT Month","",NULL,"0000-00-00","2025-06-01","2025-06-30","completed","2025-05-30 15:38:36","2025-07-01 00:11:00","0.00","1.00");
INSERT INTO `activities` VALUES("62","10","GIP PNPKI UT","PNPKI Users Training",NULL,"0000-00-00","2025-06-10","2025-06-10","completed","2025-06-03 08:47:28","2025-06-10 17:44:34","0.00","1.00");
INSERT INTO `activities` VALUES("63","14","Cybersecurity Awareness Session for Women’s Month","IlCDB ang nag organize pero CSB team ang nag Speaker",NULL,"0000-00-00","2025-03-06","2025-03-06","completed","2025-06-05 12:33:24","2025-06-05 12:33:24","0.00","1.00");
INSERT INTO `activities` VALUES("64","4","Web 3.0 Information Session","Assist RV maribao",NULL,"0000-00-00","2025-06-11","2025-06-11","completed","2025-06-11 13:41:13","2025-06-13 08:19:13","0.00","1.00");
INSERT INTO `activities` VALUES("65","4","MOA signing of DJEMC and with RD, Graduation of SPARK","",NULL,"0000-00-00","2025-06-30","2025-06-30","completed","2025-06-13 08:18:51","2025-07-01 00:11:20","0.00","1.00");
INSERT INTO `activities` VALUES("67","16","Eid al-Adha (Feast of the Sacrifice)","Holiday",NULL,"0000-00-00","2025-06-06","2025-06-06","completed","2025-06-15 21:13:03","2025-06-15 21:13:03","0.00","1.00");
INSERT INTO `activities` VALUES("68","16","Independence Day","Holiday",NULL,"0000-00-00","2025-06-12","2025-06-12","completed","2025-06-15 21:15:09","2025-06-15 21:15:09","0.00","1.00");
INSERT INTO `activities` VALUES("69","14","DICT Caraga Monday Convocation","CSB",NULL,"0000-00-00","2025-06-16","2025-06-16","completed","2025-06-15 21:16:51","2025-06-16 13:37:03","0.00","1.00");
INSERT INTO `activities` VALUES("70","14","eLGU Orientation and Digitalization","","","0000-00-00","2025-07-30","2025-07-30","completed","2025-06-16 13:35:17","2025-08-15 14:19:50","0.00","1.00");
INSERT INTO `activities` VALUES("71","10","PNPKI LGU Libjo","28 nagtravel to LGU Libjo","","0000-00-00","2025-07-29","2025-07-29","completed","2025-06-16 13:36:13","2025-08-15 14:07:11","0.00","1.00");
INSERT INTO `activities` VALUES("72","14","DICT13 (Caraga) ASEAN-Japan Cybersecurity Awareness Video Competition 2025 Regional Screening","travel time to tubajon internet provision for the job fair",NULL,"0000-00-00","2025-06-18","2025-06-18","completed","2025-06-17 09:50:36","2025-06-19 14:57:21","0.00","1.00");
INSERT INTO `activities` VALUES("73","14","CSB Weekly Huddle","meeting",NULL,"0000-00-00","2025-06-19","2025-06-19","completed","2025-06-19 12:21:24","2025-06-19 14:53:44","0.00","1.00");
INSERT INTO `activities` VALUES("74","14","𝐎𝐧𝐥𝐢𝐧𝐞 𝐏𝐫𝐞𝐬𝐞𝐧𝐭𝐚𝐭𝐢𝐨𝐧 𝐨𝐟 𝐏𝐫𝐞-𝐉𝐮𝐝𝐠𝐢𝐧𝐠 𝐑𝐞𝐬𝐮𝐥𝐭𝐬 – 𝐀𝐒𝐄𝐀𝐍-𝐉𝐚𝐩𝐚𝐧 𝐂𝐲𝐛𝐞𝐫𝐬𝐞𝐜𝐮𝐫𝐢𝐭𝐲 𝐀𝐰𝐚𝐫𝐞𝐧𝐞𝐬𝐬 𝐕𝐢𝐝𝐞𝐨 𝐂𝐨𝐦𝐩𝐞𝐭𝐢𝐭𝐢𝐨𝐧 𝟐𝟎𝟐𝟓","meet the participants",NULL,"0000-00-00","2025-06-20","2025-06-20","completed","2025-06-19 14:52:45","2025-06-20 10:02:19","0.00","1.00");
INSERT INTO `activities` VALUES("77","14","DATA PRIVACY Protection","",NULL,"0000-00-00","2025-07-15","2025-07-17","completed","2025-06-27 21:09:05","2025-07-17 21:34:06","0.00","1.00");
INSERT INTO `activities` VALUES("79","14","FREE AI Master Class","forwarded by sir ram\npretest done",NULL,"0000-00-00","2025-07-14","2025-07-14","completed","2025-06-30 10:14:59","2025-07-15 10:28:08","0.00","1.00");
INSERT INTO `activities` VALUES("80","7","LGU Libjo courtesy visit","with RD mar",NULL,"0000-00-00","2025-07-01","2025-07-01","completed","2025-07-01 00:13:29","2025-07-01 10:41:49","0.00","1.00");
INSERT INTO `activities` VALUES("87","16","Ninoy Aquino Day","Holiday","","0000-00-00","2025-08-21","2025-08-21","completed","2025-07-01 18:36:05","2025-08-22 06:49:49","0.00","1.00");
INSERT INTO `activities` VALUES("88","16","National Heroes Day","Holiday","","0000-00-00","2025-08-25","2025-08-25","completed","2025-07-01 18:36:43","2025-08-26 08:15:45","0.00","1.00");
INSERT INTO `activities` VALUES("89","7","Coordination online Meeting with the elgu and LGU LOreto","9:30, SCRIPT",NULL,"0000-00-00","2025-07-04","2025-07-04","completed","2025-07-01 18:53:20","2025-07-04 10:05:07","0.00","1.00");
INSERT INTO `activities` VALUES("91","5","MOA signing for DICT Communication Tower and Provincial Office Building","with RD Mar",NULL,"0000-00-00","2025-07-02","2025-07-02","completed","2025-07-02 14:24:53","2025-07-02 18:44:26","0.00","1.00");
INSERT INTO `activities` VALUES("93","14","CSB Huddle","meeting",NULL,"0000-00-00","2025-07-03","2025-07-03","completed","2025-07-03 19:16:41","2025-07-03 19:16:41","0.00","1.00");
INSERT INTO `activities` VALUES("94","9","Installation in brgy mabuhay and in the MDRRMO DInagat","taman 6:30 PM",NULL,"0000-00-00","2025-07-04","2025-07-04","completed","2025-07-04 22:12:39","2025-07-04 22:12:39","0.00","1.00");
INSERT INTO `activities` VALUES("95","9","Continue Free Wifi installation","New Mabuhay Elementary School",NULL,"0000-00-00","2025-07-07","2025-07-07","completed","2025-07-07 14:06:00","2025-07-08 07:03:13","0.00","1.00");
INSERT INTO `activities` VALUES("96","9","Free Wifi in MSWD","LGU Dinagat",NULL,"0000-00-00","2025-07-08","2025-07-08","completed","2025-07-08 07:04:09","2025-07-08 14:25:56","0.00","1.00");
INSERT INTO `activities` VALUES("97","9","Installation of Free Wifi","Brgy Del Pilar, Cagdianao",NULL,"0000-00-00","2025-07-09","2025-07-11","completed","2025-07-08 07:05:25","2025-07-10 18:00:08","0.00","1.00");
INSERT INTO `activities` VALUES("99","4","SPARK SMM Implementation Coordination Meeting – with Partners","attended virtual",NULL,"0000-00-00","2025-07-14","2025-07-14","completed","2025-07-14 21:03:21","2025-07-14 21:03:21","0.00","1.00");
INSERT INTO `activities` VALUES("100","4","TMD DPP competency Framework Preparation Day","with sir pierce and sir mark soriano as oic- niabot, naghataod sa mga gamit",NULL,"0000-00-00","2025-07-14","2025-07-14","completed","2025-07-14 21:06:10","2025-07-14 21:06:10","0.00","1.00");
INSERT INTO `activities` VALUES("101","3","Computer maintenance for the spark smm event","10 units",NULL,"0000-00-00","2025-07-18","2025-07-18","completed","2025-07-16 14:36:44","2025-07-21 09:26:12","0.00","1.00");
INSERT INTO `activities` VALUES("103","3","Assist the CNHS for using the facility","",NULL,"0000-00-00","2025-07-21","2025-07-21","completed","2025-07-21 14:49:57","2025-08-04 08:53:05","0.00","1.00");
INSERT INTO `activities` VALUES("104","4","Assist the Spark Social Media Marketing with DTI and PLGU","gepaTO ko ni sir Mark para mag assist ni RV",NULL,"0000-00-00","2025-07-22","2025-07-22","completed","2025-07-23 08:30:56","2025-07-24 10:29:37","0.00","1.00");
INSERT INTO `activities` VALUES("106","3","Spark SMM Training","naghipos sa mga gamit from DTI",NULL,"0000-00-00","2025-07-26","2025-07-26","completed","2025-07-24 08:26:39","2025-07-28 16:13:53","0.00","1.00");
INSERT INTO `activities` VALUES("107","9","Assist the continue installation of Free Public Wifi of CNHS","",NULL,"0000-00-00","2025-07-25","2025-07-25","completed","2025-07-25 14:43:04","2025-07-25 14:43:04","0.00","1.00");
INSERT INTO `activities` VALUES("109","14","CSB Huddle","",NULL,"0000-00-00","2025-07-31","2025-07-31","completed","2025-07-31 13:16:25","2025-07-31 18:52:49","0.00","1.00");
INSERT INTO `activities` VALUES("110","14","LGU Cagdianao(Cometee hearing of science and information technology)","",NULL,"0000-00-00","2025-08-01","2025-08-01","completed","2025-07-31 18:52:21","2025-08-04 08:22:52","0.00","1.00");
INSERT INTO `activities` VALUES("113","9","Assist the reinstallation of free wifi in RHU Cagdianao","",NULL,"0000-00-00","2025-08-04","2025-08-04","completed","2025-08-05 09:55:50","2025-08-05 09:55:50","0.00","1.00");
INSERT INTO `activities` VALUES("114","9","Technical Support free wifi","in LGU Cagdianao-cant access gmail and canva(resolve)",NULL,"0000-00-00","2025-08-06","2025-08-06","completed","2025-08-06 10:31:51","2025-08-06 10:31:51","0.00","1.00");
INSERT INTO `activities` VALUES("115","10","PNPKI UT in LGU San Jose","prepare coresponding documents if the letter is recieved\n\nhttps://drive.google.com/drive/folders/1aUyVpyyyzLR4vKNKMlB8c5VaquhAFPOT?usp=drive_link","https://drive.google.com/drive/folders/1aUyVpyyyzLR4vKNKMlB8c5VaquhAFPOT?usp=drive_link","0000-00-00","2025-09-15","2025-09-15","in progress","2025-08-06 11:14:54","2025-08-13 13:06:36","0.00","1.00");
INSERT INTO `activities` VALUES("117","3","Consulatation dialogue(DSWD)","preppare coresponding documents\n\nhttps://docs.google.com/spreadsheets/d/1K7JHjkMRPsrA0HV5YaprRJ5WjrIMhVg8/edit?gid=637528320#gid=637528320","https://drive.google.com/drive/folders/1LhXHtOcY6ypnTbN5qux1dUk36xbi1IT2?usp=drive_link","0000-00-00","2025-08-12","2025-08-12","completed","2025-08-06 11:46:02","2025-08-13 13:04:18","0.00","1.00");
INSERT INTO `activities` VALUES("118","9","Assist the Installation of Internet Provision in the POPDEV Congress","",NULL,"0000-00-00","2025-08-06","2025-08-06","completed","2025-08-07 09:58:40","2025-08-07 09:58:40","0.00","1.00");
INSERT INTO `activities` VALUES("119","2","Hack for Gov","",NULL,"0000-00-00","2025-11-13","2025-11-13","in progress","2025-08-07 11:42:21","2025-08-07 11:42:21","0.00","1.00");
INSERT INTO `activities` VALUES("120","2","Cyber range 2025","",NULL,"0000-00-00","2025-11-14","2025-11-14","in progress","2025-08-07 11:42:50","2025-08-13 11:49:56","0.00","1.00");
INSERT INTO `activities` VALUES("122","10","[Sep 30. 2025] PNPKI UT- LGU Tubajon","prepare corresponding documents","https://drive.google.com/drive/folders/1X-yTx9AfEky6CgtO7pdmn5PGeLj2H--D?usp=sharing","0000-00-00","2025-09-30","2025-09-30","in progress","2025-08-11 07:58:46","2025-08-16 10:14:41","0.00","1.00");
INSERT INTO `activities` VALUES("123","14","CSB Team Thursday Huddle","",NULL,"0000-00-00","2025-08-13","2025-08-13","completed","2025-08-13 08:53:13","2025-08-13 11:39:47","0.00","1.00");
INSERT INTO `activities` VALUES("124","3","DICT PDI Meeting","AGENDA\nDICT Program Provincial Focal Updates\n\nProgram Updates\n* Free Wi-Fi for All Program – Upcoming Activities\n - loreto, tubajon, legislative\n - awareness of the status of free wifi limit capa\n* ILCDB (ICT Literacy and Competency Development Bureau) Trainings – Upcoming Activities\n - LGU Cagdianao MOA\n - DLT for Tubajon\n* CSB/ PNPKI – Upcoming Activities\n - San Jose, Tubajon, Cagdianao\n* eLGU (Electronic Local Government Unit)- updates\n - MOU nalang ilang kulang\n* Issues, Concerns, and Recommendations\n - attended meeting\n  `DSWD\n  `Hersay of visitation of sec aguda in PDI\n  `offset ko 22","","0000-00-00","2025-08-14","2025-08-14","completed","2025-08-13 11:02:01","2025-08-14 10:00:34","0.00","1.00");
INSERT INTO `activities` VALUES("125","12","My Birthday","","","0000-00-00","2025-09-18","2025-09-18","in progress","2025-08-13 13:39:45","2025-08-13 13:39:45","0.00","1.00");
INSERT INTO `activities` VALUES("126","12","Mama Birthday","","","0000-00-00","2025-09-19","2025-09-19","in progress","2025-08-13 13:40:32","2025-08-13 13:40:32","0.00","1.00");
INSERT INTO `activities` VALUES("127","16","All Saints\' Day","","","0000-00-00","2025-11-01","2025-11-01","in progress","2025-08-13 13:41:45","2025-08-13 13:41:45","0.00","1.00");
INSERT INTO `activities` VALUES("128","16","All Souls\' Day","","","0000-00-00","2025-11-02","2025-11-02","in progress","2025-08-13 13:42:23","2025-08-13 13:42:23","0.00","1.00");
INSERT INTO `activities` VALUES("129","12","Papa Birthday","","","0000-00-00","2025-11-15","2025-11-15","in progress","2025-08-13 13:46:19","2025-08-13 13:46:19","0.00","1.00");
INSERT INTO `activities` VALUES("130","16","Bonifacio Day","","","0000-00-00","2025-11-30","2025-11-30","in progress","2025-08-13 13:46:58","2025-08-13 13:46:58","0.00","1.00");
INSERT INTO `activities` VALUES("131","16","Feast of the Immaculate Conception","","","0000-00-00","2025-12-08","2025-12-08","in progress","2025-08-13 13:57:15","2025-08-13 13:57:15","0.00","1.00");
INSERT INTO `activities` VALUES("132","16","Christmas Eve","","","0000-00-00","2025-12-24","2025-12-24","in progress","2025-08-13 13:57:42","2025-08-13 13:57:42","0.00","1.00");
INSERT INTO `activities` VALUES("133","16","Christmas Day","","","0000-00-00","2025-12-25","2025-12-25","in progress","2025-08-13 13:58:14","2025-08-13 13:58:14","0.00","1.00");
INSERT INTO `activities` VALUES("134","16","Rizal Day","","","0000-00-00","2025-12-30","2025-12-30","in progress","2025-08-13 13:58:38","2025-08-13 13:58:38","0.00","1.00");
INSERT INTO `activities` VALUES("135","16","New Year\'s Eve","","","0000-00-00","2025-12-31","2025-12-31","in progress","2025-08-13 13:59:03","2025-08-15 14:36:57","0.00","1.00");
INSERT INTO `activities` VALUES("137","6","eLGU Orientation in Siargao","for LGU Basilisa","","0000-00-00","2025-09-22","2025-09-26","in progress","2025-08-15 11:25:48","2025-08-15 11:25:48","0.00","1.00");
INSERT INTO `activities` VALUES("139","9","Site Survey in Loreto and Installation in Brgy Malinao","","","0000-00-00","2025-08-18","2025-08-20","completed","2025-08-16 21:19:02","2025-08-20 09:45:28","0.00","1.00");
INSERT INTO `activities` VALUES("140","12","pabrace sa ngipon c Danica","Surigao","","0000-00-00","2025-08-23","2025-08-24","completed","2025-08-16 21:22:28","2025-08-24 08:53:29","0.00","1.00");
INSERT INTO `activities` VALUES("141","14","CSB Team Huddle","nagmeeting sa design para sa H$G ug cyber range event colats","","0000-00-00","2025-08-19","2025-08-19","completed","2025-08-20 09:46:11","2025-08-20 13:49:48","0.00","1.00");
INSERT INTO `activities` VALUES("142","14","papa naospital","","","0000-00-00","2025-08-20","2025-08-21","completed","2025-08-22 06:48:59","2025-08-22 06:48:59","0.00","1.00");
INSERT INTO `activities` VALUES("143","12","Offset","","","0000-00-00","2025-08-22","2025-08-22","completed","2025-08-22 06:50:31","2025-08-22 06:51:07","0.00","1.00");
INSERT INTO `activities` VALUES("144","3","Planning Workshop for the Mainstreaming of STI to the CDP in LGU Libjo","about sa wifi( canva ug chatgpt)\nabout sa VA, muatend c mayora, then isyu sa mga unit\nDRRMO internet connectivity\ntourism connectivity\ncctv installtion help\nsocial egov app(symposium)\nDLT( learning resources)\nlimited connectivity for GIDA(tourist site like magsaysay)\nDLT for LGU libjo employee","https://drive.google.com/drive/folders/1rWaKt9clvWFsjI8Babv1HD5f70F5lfqJ?usp=drive_link","0000-00-00","2025-08-27","2025-08-29","in progress","2025-08-22 11:17:00","2025-08-29 11:21:54","0.00","1.00");
INSERT INTO `activities` VALUES("145","14","Proposed CSA in Malinao National High School","propose date","","0000-00-00","2025-09-29","2025-10-01","in progress","2025-08-28 09:18:02","2025-08-28 09:18:02","0.00","1.00");
INSERT INTO `activities` VALUES("146","3","Bugkosan sa Isla sa Dinagat","ready for the support","","0000-00-00","2025-09-27","2025-10-03","in progress","2025-08-29 10:26:57","2025-08-29 10:26:57","0.00","1.00");
INSERT INTO `activities` VALUES("147","7","Consultative Meeting with San Jose, PDI on the Implementation of eLGU BPLS","assist elgu team","","0000-00-00","2025-08-29","2025-08-30","in progress","2025-08-29 11:17:42","2025-08-29 11:17:42","0.00","1.00");


DROP TABLE IF EXISTS `activity_requirements`;

CREATE TABLE `activity_requirements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `activity_id` int NOT NULL,
  `request_letter` tinyint(1) DEFAULT '0',
  `reply_letter` tinyint(1) DEFAULT '0',
  `ad` tinyint(1) DEFAULT '0',
  `to` tinyint(1) DEFAULT '0',
  `post_activity` tinyint(1) DEFAULT '0',
  `certificates` tinyint(1) DEFAULT '0',
  `verification_statements` tinyint(1) DEFAULT '0',
  `photos` tinyint(1) DEFAULT '0',
  `published` tinyint(1) DEFAULT '0',
  `published_link` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `activity_id` (`activity_id`),
  CONSTRAINT `fk_activity_requirements_activity` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `activity_requirements` VALUES("1","115","1","1","1","1","0","0","0","0","0",NULL,"2025-08-15 13:39:55","2025-08-29 16:35:55");
INSERT INTO `activity_requirements` VALUES("2","122","1","1","0","0","0","0","0","0","0",NULL,"2025-08-15 13:41:20","2025-08-15 13:41:20");
INSERT INTO `activity_requirements` VALUES("3","117","1","1","0","1","1","0","0","1","0",NULL,"2025-08-15 13:58:41","2025-08-15 13:58:41");
INSERT INTO `activity_requirements` VALUES("4","71","1","1","1","1","1","1","1","1","1","https://www.facebook.com/story.php?story_fbid=1197448209087924&id=100064682679653&mibextid=wwXIfr&rdid=0jDU3gNqn1jfc0Dc#","2025-08-15 14:07:11","2025-08-15 14:24:05");
INSERT INTO `activity_requirements` VALUES("5","70","1","1","0","1","1","0","0","1","1","https://www.facebook.com/story.php?story_fbid=1197448209087924&id=100064682679653&mibextid=wwXIfr&rdid=0jDU3gNqn1jfc0Dc#","2025-08-15 14:19:50","2025-08-15 14:19:50");
INSERT INTO `activity_requirements` VALUES("6","135","0","0","0","0","0","0","0","0","0",NULL,"2025-08-15 14:31:54","2025-08-15 14:31:54");
INSERT INTO `activity_requirements` VALUES("8","139","0","0","0","1","0","0","0","0","0",NULL,"2025-08-20 09:45:28","2025-08-20 09:48:57");
INSERT INTO `activity_requirements` VALUES("9","141","0","0","0","0","0","0","0","0","0",NULL,"2025-08-20 09:46:29","2025-08-20 09:46:29");
INSERT INTO `activity_requirements` VALUES("10","87","0","0","0","0","0","0","0","0","0",NULL,"2025-08-22 06:49:24","2025-08-22 06:49:24");
INSERT INTO `activity_requirements` VALUES("11","143","0","0","0","0","0","0","0","0","0",NULL,"2025-08-22 06:51:07","2025-08-22 06:51:07");
INSERT INTO `activity_requirements` VALUES("12","144","1","1","1","1","0","0","0","1","0",NULL,"2025-08-22 11:17:16","2025-08-29 14:01:56");
INSERT INTO `activity_requirements` VALUES("13","140","0","0","0","0","0","0","0","0","0",NULL,"2025-08-24 08:53:17","2025-08-24 08:53:17");
INSERT INTO `activity_requirements` VALUES("14","88","0","0","0","0","0","0","0","0","0",NULL,"2025-08-26 08:15:45","2025-08-26 08:15:45");


DROP TABLE IF EXISTS `ipcr_activities`;

CREATE TABLE `ipcr_activities` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ipcr_entry_id` int NOT NULL,
  `activity_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ipcr_entry_id` (`ipcr_entry_id`),
  KEY `activity_id` (`activity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=256 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `ipcr_activities` VALUES("112","6","36","2025-06-09 15:14:45");
INSERT INTO `ipcr_activities` VALUES("113","6","15","2025-06-09 15:14:45");
INSERT INTO `ipcr_activities` VALUES("116","5","36","2025-06-09 15:22:37");
INSERT INTO `ipcr_activities` VALUES("117","5","15","2025-06-09 15:22:37");
INSERT INTO `ipcr_activities` VALUES("122","7","36","2025-06-09 15:27:00");
INSERT INTO `ipcr_activities` VALUES("123","7","15","2025-06-09 15:27:00");
INSERT INTO `ipcr_activities` VALUES("172","1","37","2025-06-09 16:14:31");
INSERT INTO `ipcr_activities` VALUES("173","1","23","2025-06-09 16:14:31");
INSERT INTO `ipcr_activities` VALUES("174","1","17","2025-06-09 16:14:31");
INSERT INTO `ipcr_activities` VALUES("175","1","13","2025-06-09 16:14:31");
INSERT INTO `ipcr_activities` VALUES("176","4","37","2025-06-09 16:14:52");
INSERT INTO `ipcr_activities` VALUES("177","4","23","2025-06-09 16:14:52");
INSERT INTO `ipcr_activities` VALUES("178","4","17","2025-06-09 16:14:52");
INSERT INTO `ipcr_activities` VALUES("179","4","13","2025-06-09 16:14:52");
INSERT INTO `ipcr_activities` VALUES("180","9","40","2025-06-09 16:15:26");
INSERT INTO `ipcr_activities` VALUES("181","10","49","2025-06-09 16:15:49");
INSERT INTO `ipcr_activities` VALUES("182","10","50","2025-06-09 16:15:49");
INSERT INTO `ipcr_activities` VALUES("183","10","46","2025-06-09 16:15:49");
INSERT INTO `ipcr_activities` VALUES("184","10","55","2025-06-09 16:15:49");
INSERT INTO `ipcr_activities` VALUES("185","10","43","2025-06-09 16:15:49");
INSERT INTO `ipcr_activities` VALUES("186","10","42","2025-06-09 16:15:49");
INSERT INTO `ipcr_activities` VALUES("187","10","41","2025-06-09 16:15:49");
INSERT INTO `ipcr_activities` VALUES("188","10","39","2025-06-09 16:15:49");
INSERT INTO `ipcr_activities` VALUES("189","10","38","2025-06-09 16:15:49");
INSERT INTO `ipcr_activities` VALUES("190","10","31","2025-06-09 16:15:49");
INSERT INTO `ipcr_activities` VALUES("191","10","30","2025-06-09 16:15:49");
INSERT INTO `ipcr_activities` VALUES("192","10","29","2025-06-09 16:15:49");
INSERT INTO `ipcr_activities` VALUES("193","10","27","2025-06-09 16:15:49");
INSERT INTO `ipcr_activities` VALUES("194","10","24","2025-06-09 16:15:49");
INSERT INTO `ipcr_activities` VALUES("195","10","63","2025-06-09 16:15:49");
INSERT INTO `ipcr_activities` VALUES("196","10","21","2025-06-09 16:15:49");
INSERT INTO `ipcr_activities` VALUES("197","10","20","2025-06-09 16:15:49");
INSERT INTO `ipcr_activities` VALUES("198","10","14","2025-06-09 16:15:49");
INSERT INTO `ipcr_activities` VALUES("199","10","12","2025-06-09 16:15:49");
INSERT INTO `ipcr_activities` VALUES("200","10","8","2025-06-09 16:15:49");
INSERT INTO `ipcr_activities` VALUES("205","8","37","2025-07-07 19:56:35");
INSERT INTO `ipcr_activities` VALUES("206","8","14","2025-07-07 19:56:35");
INSERT INTO `ipcr_activities` VALUES("231","22","71","2025-08-07 10:07:49");
INSERT INTO `ipcr_activities` VALUES("232","24","71","2025-08-07 10:09:02");
INSERT INTO `ipcr_activities` VALUES("233","25","71","2025-08-07 10:09:31");
INSERT INTO `ipcr_activities` VALUES("245","27","110","2025-08-07 10:12:22");
INSERT INTO `ipcr_activities` VALUES("246","27","70","2025-08-07 10:12:22");
INSERT INTO `ipcr_activities` VALUES("247","27","77","2025-08-07 10:12:22");
INSERT INTO `ipcr_activities` VALUES("248","27","100","2025-08-07 10:12:22");
INSERT INTO `ipcr_activities` VALUES("249","27","97","2025-08-07 10:12:22");
INSERT INTO `ipcr_activities` VALUES("250","27","96","2025-08-07 10:12:22");
INSERT INTO `ipcr_activities` VALUES("251","27","95","2025-08-07 10:12:22");
INSERT INTO `ipcr_activities` VALUES("252","27","89","2025-08-07 10:12:22");
INSERT INTO `ipcr_activities` VALUES("253","27","94","2025-08-07 10:12:22");
INSERT INTO `ipcr_activities` VALUES("254","27","91","2025-08-07 10:12:22");
INSERT INTO `ipcr_activities` VALUES("255","27","80","2025-08-07 10:12:22");


DROP TABLE IF EXISTS `ipcr_entries`;

CREATE TABLE `ipcr_entries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `year` int NOT NULL,
  `semester` enum('1st','2nd') NOT NULL,
  `function_type` enum('Core Function','Support Function') NOT NULL DEFAULT 'Core Function',
  `success_indicators` text NOT NULL,
  `actual_accomplishments` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `ipcr_entries` VALUES("1","1","2025","1st","Core Function","(1) Number of Cybersecurity Advocacy and Awareness conducted (face-to-face)","A total of (4) Cybersecurity Advocacy and Awareness Program was facilitated.","2025-06-02 11:49:33","2025-06-09 16:14:31");
INSERT INTO `ipcr_entries` VALUES("4","1","2025","1st","Core Function","(25) Number of individuals reached for Advocacy and Awareness conducted (face-to-face)","A total of (289) individuals reached for Advocacy and Awareness conducted (face-to-face)","2025-06-02 14:56:36","2025-06-09 16:14:52");
INSERT INTO `ipcr_entries` VALUES("5","1","2025","1st","Core Function","(2) Number of PKI awareness campaigns conducted","A total of (2) PKI awareness campaigns conducted","2025-06-02 15:08:16","2025-06-09 15:22:37");
INSERT INTO `ipcr_entries` VALUES("6","1","2025","1st","Core Function","(10) Number of Issued Digital Certificates","A total of (193) Issued Digital Certificates","2025-06-02 15:10:10","2025-06-09 15:14:45");
INSERT INTO `ipcr_entries` VALUES("7","1","2025","1st","Core Function","(2) Number of PNPKI User\'s Training conducted","A total of (2) of PNPKI Users Training\nconducted","2025-06-02 15:11:53","2025-06-09 15:27:00");
INSERT INTO `ipcr_entries` VALUES("8","1","2025","1st","Core Function","(25) Number of PNPKI User\'s Trained","A total of (193) PNPKI User\'s Trained","2025-06-02 15:13:22","2025-07-07 19:56:35");
INSERT INTO `ipcr_entries` VALUES("9","1","2025","1st","Core Function","# of Technical Assistance Provided (incident response) - as the need arises","A total of (1) Technical Assistance Provided (incident response)","2025-06-02 15:15:21","2025-06-09 16:15:26");
INSERT INTO `ipcr_entries` VALUES("10","1","2025","1st","Support Function","# Supported Activities","A total of (20) Supported Activities","2025-06-02 15:20:09","2025-06-09 16:15:49");
INSERT INTO `ipcr_entries` VALUES("20","1","2025","2nd","Core Function","(1) Number of Cybersecurity Advocacy and Awareness conducted (face-to-face)","A total of () Cybersecurity Advocacy and Awareness Program was facilitated.","2025-07-07 19:52:00","2025-07-07 19:52:00");
INSERT INTO `ipcr_entries` VALUES("21","1","2025","2nd","Core Function","(25) Number of individuals reached for Advocacy and Awareness conducted (face-to-face)","A total of () individuals reached for Advocacy and Awareness conducted (face-to-face)","2025-07-07 19:52:10","2025-07-07 19:52:10");
INSERT INTO `ipcr_entries` VALUES("22","1","2025","2nd","Core Function","(1) Number of PKI awareness campaigns conducted","A total of (1) PKI awareness campaigns conducted","2025-07-07 19:52:26","2025-08-07 10:07:49");
INSERT INTO `ipcr_entries` VALUES("23","1","2025","2nd","Core Function","(10) Number of Issued Digital Certificates","A total of (25) Issued Digital Certificates","2025-07-07 19:52:47","2025-08-07 10:08:40");
INSERT INTO `ipcr_entries` VALUES("24","1","2025","2nd","Core Function","(1) Number of PNPKI User\'s Training conducted","A total of (1) of PNPKI Users Training\nconducted","2025-07-07 19:53:45","2025-08-07 10:09:02");
INSERT INTO `ipcr_entries` VALUES("25","1","2025","2nd","Core Function","(25) Number of PNPKI User\'s Trained","A total of (23) PNPKI User\'s Trained","2025-07-07 19:56:06","2025-08-07 10:09:31");
INSERT INTO `ipcr_entries` VALUES("26","1","2025","2nd","Core Function","# of Technical Assistance Provided (incident response) - as the need arises","A total of () Technical Assistance Provided (incident response)","2025-07-07 19:56:56","2025-07-07 19:56:56");
INSERT INTO `ipcr_entries` VALUES("27","1","2025","2nd","Support Function","# Supported Activities","A total of (11) Supported Activities","2025-07-07 19:57:17","2025-08-07 10:12:22");


DROP TABLE IF EXISTS `notes`;

CREATE TABLE `notes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` enum('high','medium','low') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `status` enum('pending','in_progress','completed','archived','active') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `notes` VALUES("2","computer unit of dtc libjo","ask sir PJ","medium","active","4","1",NULL,"2025-08-14 20:37:20","2025-08-14 20:37:20");
INSERT INTO `notes` VALUES("5","PMS","problem time in the sidebar not sync in the pc date","medium","active","12","1",NULL,"2025-08-22 06:55:03","2025-08-22 06:55:03");


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
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `offset_requests` VALUES("32","1","65","2025-07-03","nagbarog sa haligi","approved","2025-07-03 19:07:44","2025-07-03 19:07:44");
INSERT INTO `offset_requests` VALUES("33","1","36","2025-08-22","para makaless gas","approved","2025-08-14 08:15:20","2025-08-14 08:15:20");


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
) ENGINE=MyISAM AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `overtime_requests` VALUES("57","1","65",NULL,NULL,"1","2025-06-30","2025-06-30","1.0","1",NULL,"","2025-07-01 19:52:48","2025-07-03 19:07:44");
INSERT INTO `overtime_requests` VALUES("58","1","65",NULL,NULL,"1","2025-06-30","2025-06-30","1.0","0",NULL,"approved","2025-07-01 19:53:02","2025-07-01 19:53:02");
INSERT INTO `overtime_requests` VALUES("60","1","36",NULL,NULL,"1","2025-04-07","2025-04-09","1.0","1",NULL,"","2025-07-31 08:10:29","2025-08-14 08:15:20");
INSERT INTO `overtime_requests` VALUES("61","1","39",NULL,NULL,"1","2025-04-21","2025-04-23","1.0","0",NULL,"approved","2025-07-31 08:12:32","2025-07-31 08:12:32");
INSERT INTO `overtime_requests` VALUES("62","1","147",NULL,NULL,"1","2025-08-29","2025-08-30","1.0","0",NULL,"approved","2025-08-29 14:06:17","2025-08-29 14:06:17");


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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `point_of_contacts` VALUES("1","provincial","Provincial Governor- PDI","Province of Dinagat Islands","","governorsofficepdi@gmail.com","HON. NILO P. DEMEREY JR.","Governor","","Medel Escalante Jr.","Senior Administrative Assistant ll","","2025-05-29 17:58:38","2025-07-15 10:48:47");
INSERT INTO `point_of_contacts` VALUES("2","municipal","Municipal Mayor","LGU Basilisa","","lgubasilisa2022@gmail.com","HON. OZZY REUBEN M. ECLEO","Mayor","","","","","2025-05-29 19:25:36","2025-05-29 19:25:36");
INSERT INTO `point_of_contacts` VALUES("3","municipal","Municipal Mayor","LGU Cagdianao","","lgucagdianaopdi@gmail.com","HON. ADOLFO E. LONGOS","Mayor","","Michael P. Longos","","charlesm.longos@gmail.com","2025-05-29 19:26:46","2025-08-28 13:45:43");
INSERT INTO `point_of_contacts` VALUES("4","municipal","Municipal Mayor","LGU Dinagat","","angbag.onglgudinagat@gmail.com","HON. SIMPLICIO S. LEYRAN","Mayor","","Al E. Eligue","","","2025-05-29 19:28:06","2025-08-28 13:46:24");
INSERT INTO `point_of_contacts` VALUES("5","municipal","Municipal Mayor","LGU Libjo","","lgulibjo.pdi@gmail.com","HON. MELODY L. COMPASIVO","Mayor","mbllamera@gmail.com","Madel H. Obsioma","Admin Aide lll/MPDO Clerk","delosioma@gmail.com","2025-05-29 19:30:05","2025-07-29 18:44:02");
INSERT INTO `point_of_contacts` VALUES("6","municipal","Municipal Mayor","LGU Loreto","","mioloretopdi2019@gmail.com","HON. DOANDRE BILL A. LADAGA","Mayor","","Cresel Mia A. Socajel","","9305490408","2025-05-29 19:31:10","2025-06-02 11:02:01");
INSERT INTO `point_of_contacts` VALUES("7","municipal","Municipal Mayor","LGU San Jose","","ootmsanjose@gmail.com","HON. RUBEN J D. ZUNIEGA","Mayor","","Jurie S. Mancia","","9399072215/sanjosedi.ict@gmail.com","2025-05-29 19:32:07","2025-08-14 20:38:47");
INSERT INTO `point_of_contacts` VALUES("8","municipal","Municipal Mayor","LGU Tubajon","","tubajonofficial@gmail.com","HON. SIMPLICIA P. PEDRABLANCA","Mayor","","Leofer Sam C. Tidalgo","","9514545568/ tidalgoleofersam@gmail.com","2025-05-29 19:33:11","2025-08-14 20:39:55");
INSERT INTO `point_of_contacts` VALUES("10","nga","DENR-PENRO DINAGAT ISLANDS","Province of Dinagat Islands","","penrodinagat@denr.gov.ph","NATHANIEL E. RACHO, RPF","OIC, PNR Officer","","CHRISTIAN JAY D. DUPLITO","Forest Technician II/ Asst. Chief, ICT Unit","","2025-05-30 10:07:22","2025-06-03 09:28:37");
INSERT INTO `point_of_contacts` VALUES("11","provincial","Vice Governor","Provincial Local Government Unit","","vicegovernorpdi@gmail.com","GERALDINE B. ECLEO,MPA","Vice Governor","","MICHAEL G. TEMARIO","DEMO I","09996727766 mikingtem@gmail.com","2025-07-08 13:21:01","2025-07-08 13:21:01");
INSERT INTO `point_of_contacts` VALUES("12","nga","Provincial DOH Office","Provincial DOH Office - Dinagat Islands","","pdohopdi@caraga.doh.gov.ph","KERBY JOY G. EDERA, RN, MAN","OIC - Development Management Officer IV","","Mernil Jay A. Olay","Administrative Assistant II / IT","jaymernil@gmail.com","2025-07-18 11:00:27","2025-08-28 13:38:53");
INSERT INTO `point_of_contacts` VALUES("14","nga","SDO Dinagat Islands","DepEd Dinagat","","","Bryan L. Arreo, PhD, CESE","OIC-Asst. Schools Division Superimtendent","","Eric Olasiman","","eric.olasiman@deped.gov.ph / 09516869427","2025-08-28 09:34:11","2025-08-28 13:51:13");


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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `projects` VALUES("2","CSB-CERT 2025","DICT 13 Project Implementation","2025-01-01","2025-12-31","in progress","2025-05-18 15:24:29","2025-07-08 12:47:08");
INSERT INTO `projects` VALUES("3","DICT PDI 2025","Provincial Activity / Initiative","2025-01-01","2025-12-31","in progress","2025-05-18 16:33:43","2025-07-08 12:49:08");
INSERT INTO `projects` VALUES("4","ILCDB 2025","DICT 13 Project Implementation","2025-01-01","2025-12-31","in progress","2025-05-19 11:04:16","2025-07-08 12:49:46");
INSERT INTO `projects` VALUES("5","DICT Caraga 2025","DICT 13 Project Implementation","2025-01-01","2025-12-31","in progress","2025-05-19 14:39:29","2025-07-08 12:47:28");
INSERT INTO `projects` VALUES("6","eGOV 2025","DICT 13 Project Implementation","2025-01-01","2025-12-31","in progress","2025-05-19 14:53:15","2025-07-08 12:49:17");
INSERT INTO `projects` VALUES("7","eLGU 2025","DICT 13 Project Implementation","2025-01-01","2025-12-31","in progress","2025-05-19 14:54:20","2025-07-08 12:49:26");
INSERT INTO `projects` VALUES("9","Wifi 2025","DICT 13 Project Implementation","2025-01-01","2025-12-31","in progress","2025-05-19 15:56:30","2025-07-08 12:49:58");
INSERT INTO `projects` VALUES("10","CSB-PNPKI 2025","DICT 13 Project Implementation","2025-01-01","2025-12-31","in progress","2025-05-19 16:02:22","2025-07-08 12:47:20");
INSERT INTO `projects` VALUES("12","Personal 2025","Kent D. Alico","2025-01-01","2025-12-31","in progress","2025-05-19 16:44:52","2025-05-19 16:44:52");
INSERT INTO `projects` VALUES("13","IIDB 2025","DICT 13 Project Implementation","2025-01-01","2025-12-31","in progress","2025-05-20 08:36:33","2025-07-08 12:49:34");
INSERT INTO `projects` VALUES("14","CSB-CEISMD 2025","DICT 13 Project Implementation","2025-01-01","2025-12-31","in progress","2025-05-28 09:40:50","2025-08-13 11:48:37");
INSERT INTO `projects` VALUES("16","PH Holiday","Holiday 2025","2025-01-01","2025-12-31","in progress","2025-06-15 21:11:04","2025-06-23 10:21:13");


DROP TABLE IF EXISTS `tev_claims`;

CREATE TABLE `tev_claims` (
  `id` int NOT NULL AUTO_INCREMENT,
  `claim_reference` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `employee_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `claim_date` date NOT NULL,
  `purpose` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `status` enum('Draft','For Review','Approved','Rejected','Paid') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Draft',
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tev_claims` VALUES("2","TEV-2025-00002","Kent D. Alico","DICT","2025-08-07","for signature of engr gab\n\nC:\\Users\\DICT-Alico\\Desktop\\DICT\\travel reimbursement\\2025\\july\\[july 29-30, 2025] PNPKI eLGU Orientation- LGU Libjo","6350.00","For Review","10","CSB-PNPKI 2025","71","1","2025-08-07 16:53:03","2025-08-13 08:20:34");
INSERT INTO `tev_claims` VALUES("6","TEV-2025-00003","Kent D. Alico","DICT","2025-08-08","C:\\Users\\DICT-Alico\\Desktop\\DICT\\travel reimbursement\\2025\\Sep\\[Sep. 2025] PNPKI UT- LGU San Jose","1000.00","Draft","10","CSB-PNPKI 2025","115","1","2025-08-08 09:38:05","2025-08-13 08:14:23");
INSERT INTO `tev_claims` VALUES("7","TEV-2025-00004","Kent D. Alico","DICT","2025-08-24","wala pa maghimo\n\nC:\\Users\\DICT-Alico\\Desktop\\DICT\\travel reimbursement\\2025\\august\\[August 27-29, 2025] Planning Workshop for the Mainstreaming of STI to the CDP in LGU Libjo","7500.00","Draft","3","DICT PDI 2025","144","1","2025-08-24 10:03:13","2025-08-24 10:04:23");
INSERT INTO `tev_claims` VALUES("8","TEV-2025-00005","Kent D. Alico","DICT","2025-08-26","please make a claim\nC:\\Users\\DICT-Alico\\Desktop\\DICT\\travel reimbursement\\2025\\august\\[August 19-20, 2025] Site Survey in Loreto and Installation in Brgy Malinao","4500.00","Draft","9","Wifi 2025","139","1","2025-08-26 15:06:55","2025-08-26 15:09:41");


DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_picture` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'default-avatar.jpg',
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','user') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `role_id` int DEFAULT NULL,
  `profile_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_photo_updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_users_profile_photo` (`profile_photo`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` VALUES("1","admin","$2y$10$e94O.47JxKIYTuy2MDF5L.py6WNI/vhu5l2.LQUMyNS2qlJHiLBOS","Kent D. Alico","default-avatar.jpg","salamander00000@gmail.com","admin","2025-05-18 14:08:24","2025-08-26 15:12:56",NULL,"user_1_1756192376.jpg","2025-08-26 15:12:56");


SET FOREIGN_KEY_CHECKS=1;
COMMIT;
