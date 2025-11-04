-- Safety Backup Before Restore
-- Generated: 2025-11-04 21:36:29

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `user_type` enum('admin','teacher','student') NOT NULL,
  `action` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`,`user_type`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=376 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `activity_logs` VALUES
('375','8','admin','RESTORE_DATABASE','Restored database from backup: smati_backup_2025-11-03_05-46-58.sql. Executed 46/46 queries. Safety backup: pre_restore_backup_2025-11-04_21-35-58.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-11-04 21:35:59');

DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(225) DEFAULT NULL,
  `password` varchar(225) DEFAULT NULL,
  `email` varchar(225) NOT NULL,
  `security_question` varchar(225) NOT NULL,
  `security_answer` varchar(225) NOT NULL,
  `confirm_password` varchar(225) NOT NULL,
  `createdAt` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `admin` VALUES
('6','admin','$2y$10$06rWNgsXByGjccqIxm3XxeS6/c2T29Z/ObOe7bC4kCCnsLeUi5kzC','admin1@example.com','friend','mau','Admin123','2025-10-25 00:01:48'),
('7','admin','$2y$10$lLwcEpGqn4ZV0kjIb58HE.LJ98IOR8fHJb.JDT9O/3qEIFJhHYzcK','admin@gmail.com','friend','mau','$2y$10$lLwcEpGqn4ZV0kjIb58HE.LJ98IOR8fHJb.JDT9O/3qEIFJhHYzcK','2025-10-25 00:13:07'),
('8','admin','$2y$10$TffI/m24HzsWWQ9Epu632OVr67WUZVxiZ6c0ZXCZ4X6zHTrV6Gvrm','Mauchilan@yahoo.com','friend','mau','','2025-10-25 02:43:37'),
('18','admin','$2y$10$cro8uBCFDrJrEOzFGHBaDuULlFwv5NxH9mRqRMhv39LI5Q.b/oN0G','Mau@gmail.com','friend','mau','','2025-10-26 01:27:18'),
('27','admin','$2y$10$pVRqfUu9B7/mYqdukhtE5.rHuVm5tXQ2ZyqQyV0WHdSXWb52DUgLC','test@gmail.com','friend','mau','$2y$10$pVRqfUu9B7/mYqdukhtE5.rHuVm5tXQ2ZyqQyV0WHdSXWb52DUgLC','2025-10-26 02:40:51');

DROP TABLE IF EXISTS `announcements`;
CREATE TABLE `announcements` (
  `announcement_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(225) NOT NULL,
  `details` text NOT NULL,
  `type` varchar(225) NOT NULL,
  `createdAt` datetime NOT NULL,
  PRIMARY KEY (`announcement_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `announcements` VALUES
('5','PRELIM EXAMINATION SY.2025-2026 ','October 21-22, 2025 is the prelim examintation for College level. STRICTLY NO premit, NO exam.','High','2025-10-15 23:00:02'),
('6','MIDTERM EXAMINATION  SY. 2025-2026','November 21-22, 2025. STRICTLY NO Permit, NO Exam. ','High','2025-10-15 23:10:48'),
('8','PRE FINAL EXAMINATION SY.2025-2026','please settle your payment to avoid penalty.','High','2025-10-19 21:47:54'),
('9','Datamex Foundation Day 2025','Oct. 28-29, 2025. Attendance is a must.','Low','2025-10-19 21:58:08');

DROP TABLE IF EXISTS `auth`;
CREATE TABLE `auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `password` varchar(255) NOT NULL,
  `pin` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `auth` VALUES
('1','smati2025','112601');

DROP TABLE IF EXISTS `concern`;
CREATE TABLE `concern` (
  `concern_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `section` varchar(225) NOT NULL,
  `email` varchar(225) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `type` varchar(225) NOT NULL,
  `details` varchar(225) NOT NULL,
  `reference_num` varchar(225) NOT NULL,
  `concern_status` varchar(225) NOT NULL,
  `concern_date` date NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`concern_id`),
  KEY `teacher_id` (`teacher_id`),
  KEY `student_id` (`student_id`),
  CONSTRAINT `concern_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`),
  CONSTRAINT `concern_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `concern` VALUES
('13','45','B','Mau@gmail.com','1','Grades','asdasdasdasdasd','SMATI2025-001','Case Closed','2025-10-08'),
('14','45','B','Mau@gmail.com','1','Attendance','asdadsasdsad','SMATI2025-002','Case Closed','2025-10-08'),
('15','45','B','Mau@gmail.com','1','Grades','LOREM IPSIUM TEST TESTEST','SMATI2025-003','Case Closed','2025-10-12'),
('17','45','B','Mau1@gmail.com','1','Grades','ARAY KOO','SMATI2025-004','Approved','2025-10-31'),
('18','48','B','Kurt@yahoo.com','1','Attendance','ARAY KOOO','SMATI2025-005','Pending','2025-11-02');

DROP TABLE IF EXISTS `grades`;
CREATE TABLE `grades` (
  `grades_id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_id` int(11) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `schoolyear_id` int(11) DEFAULT NULL,
  `prelim` varchar(50) DEFAULT NULL,
  `midterm` varchar(50) DEFAULT NULL,
  `prefinals` varchar(50) DEFAULT NULL,
  `finals` varchar(50) DEFAULT NULL,
  `average` decimal(10,2) NOT NULL,
  `equivalent` decimal(10,2) NOT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `createdAt` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`grades_id`),
  UNIQUE KEY `subject_id_2` (`subject_id`,`teacher_id`,`student_id`,`schoolyear_id`),
  KEY `subject_id` (`subject_id`),
  KEY `teacher_id` (`teacher_id`),
  KEY `student_id` (`student_id`),
  KEY `sy_id` (`schoolyear_id`),
  CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`),
  CONSTRAINT `grades_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`),
  CONSTRAINT `grades_ibfk_4` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`),
  CONSTRAINT `grades_ibfk_5` FOREIGN KEY (`schoolyear_id`) REFERENCES `schoolyear` (`schoolyear_id`)
) ENGINE=InnoDB AUTO_INCREMENT=302 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `grades` VALUES
('257','3','1','45','2','97','88','99','90','93.50','1.50','Passed','','2025-09-11 17:54:50'),
('258','3','1','48','2','90','89','81','82','85.50','2.00','Passed','','2025-09-11 17:54:50'),
('259','3','1','58','2','83','84','84','81','83.00','2.50','Passed','','2025-09-11 17:54:50'),
('263','7','1','45','3','89','88','82','90','87.25','2.00','Passed','Good Job','2025-09-14 23:23:38'),
('264','7','1','48','3',NULL,NULL,NULL,NULL,'0.00','5.00','Failed','','2025-09-14 23:23:38'),
('265','7','1','58','3',NULL,NULL,NULL,NULL,'0.00','5.00','Failed','','2025-09-14 23:23:38'),
('266','6','1','45','2','90','92','93','94','92.25','1.50','Passed','Goob Job','2025-09-14 23:51:14'),
('267','6','1','48','2',NULL,NULL,NULL,NULL,'0.00','5.00','Failed','','2025-09-14 23:51:14'),
('268','6','1','58','2',NULL,NULL,NULL,NULL,'0.00','5.00','Failed','','2025-09-14 23:51:14'),
('269','9','1','45','2','88','81','83','85','84.25','2.50','Passed','','2025-09-15 11:09:15'),
('270','9','1','48','2','98','79','85','70','83.00','2.50','Passed','','2025-09-15 11:09:15'),
('271','9','1','58','2',NULL,NULL,NULL,NULL,'0.00','5.00','Failed','','2025-09-15 11:09:15'),
('275','10','3','45','2','88','87','89','90','88.50','2.00','Passed','','2025-09-15 11:19:50'),
('276','10','3','48','2',NULL,NULL,NULL,NULL,'0.00','5.00','Failed','','2025-09-15 11:19:50'),
('277','10','3','58','2',NULL,NULL,NULL,NULL,'0.00','5.00','Failed','','2025-09-15 11:19:50'),
('278','19','1','45','3','99','98',NULL,'99','98.67','1.50','Passed','Good Job','2025-10-28 02:09:32'),
('279','19','1','48','3',NULL,NULL,NULL,NULL,'0.00','5.00','Failed','','2025-10-28 02:09:32'),
('280','19','1','58','3',NULL,NULL,NULL,NULL,'0.00','5.00','Failed','','2025-10-28 02:09:32'),
('281','19','1','59','3','76','78',NULL,'77','77.00','3.00','Passed','good job','2025-10-31 04:19:01'),
('282','19','1','70','3','88','76',NULL,'67','77.00','3.00','Passed','good job','2025-10-31 04:19:01'),
('283','19','1','32','3',NULL,NULL,NULL,NULL,'0.00','5.00','Failed','','2025-10-31 04:19:01'),
('284','19','1','57','3',NULL,NULL,NULL,NULL,'0.00','5.00','Failed','','2025-10-31 04:19:01'),
('285','19','1','46','3',NULL,NULL,NULL,NULL,'0.00','5.00','Failed','','2025-10-31 04:19:01'),
('286','19','1','61','3',NULL,NULL,NULL,NULL,'0.00','5.00','Failed','','2025-10-31 04:19:01'),
('287','8','1','48','3',NULL,NULL,NULL,NULL,'0.00','5.00','Failed','','2025-10-31 04:27:58'),
('288','8','1','58','3',NULL,NULL,NULL,NULL,'0.00','5.00','Failed','','2025-10-31 04:27:58'),
('289','8','1','45','3','89','88',NULL,'88','88.33','2.00','Passed','Good Job','2025-10-31 04:27:58'),
('296','20','1','48','3',NULL,NULL,NULL,NULL,'0.00','5.00','Failed','','2025-10-31 04:39:31'),
('297','20','1','58','3',NULL,NULL,NULL,NULL,'0.00','5.00','Failed','','2025-10-31 04:39:31'),
('298','20','1','45','3','89','82',NULL,'88','86.33','2.00','Passed','','2025-10-31 04:39:31'),
('299','13','1','48','2',NULL,NULL,NULL,NULL,'0.00','5.00','Failed','','2025-11-03 05:44:18'),
('300','13','1','58','2',NULL,NULL,NULL,NULL,'0.00','5.00','Failed','','2025-11-03 05:44:18'),
('301','13','1','45','2','88','89',NULL,'82','86.33','2.00','Passed','GJ','2025-11-03 05:44:18');

DROP TABLE IF EXISTS `login_attempts`;
CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempt_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_username_ip` (`username`,`ip_address`),
  KEY `idx_attempt_time` (`attempt_time`)
) ENGINE=InnoDB AUTO_INCREMENT=199 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `password_reset_log`;
CREATE TABLE `password_reset_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `reset_date` datetime NOT NULL,
  `month_year` varchar(7) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_email_month` (`email`,`month_year`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `password_reset_log` VALUES
('1','admin@gmail.com','2025-10-31 08:17:56','2025-10'),
('2','admin@gmail.com','2025-10-31 08:18:59','2025-10');

DROP TABLE IF EXISTS `registrars`;
CREATE TABLE `registrars` (
  `registrar_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `createdAt` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`registrar_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `registrars` VALUES
('1','registrar1234','$2y$10$WnoZfZYDcd1KS79w4kKKHexzV/iwJSKqAkifKigB8U.B5j6BdJwEO','1','Registrar','registar1@gmail.com','0','2025-10-30 02:01:09'),
('2','reg1234','$2y$10$BqPfiEXs913u8TWl2qyyC.AJN3TMw.le571.IUuc22ISPPYTmpaly','Cynthia','Maam','reg123456@yahoo.com','1','2025-10-31 01:51:39');

DROP TABLE IF EXISTS `schoolyear`;
CREATE TABLE `schoolyear` (
  `schoolyear_id` int(11) NOT NULL AUTO_INCREMENT,
  `schoolyear` varchar(225) DEFAULT NULL,
  `semester` varchar(225) DEFAULT NULL,
  `status` varchar(225) NOT NULL,
  PRIMARY KEY (`schoolyear_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `schoolyear` VALUES
('2','2025-2026','1st','1'),
('3','2025-2026','2nd','1'),
('14','2026-2027','1st','0');

DROP TABLE IF EXISTS `student_list`;
CREATE TABLE `student_list` (
  `list_id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_id` int(11) NOT NULL,
  `student_set` varchar(255) NOT NULL,
  PRIMARY KEY (`list_id`),
  KEY `subject_id` (`subject_id`),
  CONSTRAINT `student_list_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `student_list` VALUES
('8','3','B'),
('9','3','A'),
('10','6','B'),
('12','6','A'),
('13','7','A'),
('14','7','B'),
('15','8','A'),
('16','8','B'),
('17','9','A'),
('18','9','B'),
('19','10','B'),
('20','17','A'),
('21','17','B'),
('22','19','A'),
('23','19','B'),
('24','20','B'),
('25','20','A'),
('26','13','B');

DROP TABLE IF EXISTS `students`;
CREATE TABLE `students` (
  `student_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(225) NOT NULL,
  `password` varchar(225) NOT NULL,
  `email` varchar(225) NOT NULL,
  `firstname` varchar(225) NOT NULL,
  `lastname` varchar(225) NOT NULL,
  `course` varchar(225) NOT NULL,
  `status` varchar(255) DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`student_id`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `students` VALUES
('32','eman1234','$2y$10$jrYunrZFy5W9iyWaGXouj.PedUBPk.uiXsbEbDJ6FpLLvc70fYXoe','eman@gmail.com','Eman12','Gumayagay','A','1','','2025-08-26 17:27:43'),
('45','mau123','$2y$10$XZCelMp1iuCVGwBI8s3SmectXxuDT1B/TsJ6Y/JqGrxq.rmdMouc.','Mau1@gmail.com','Mau Chi Lan','Soldevilla','B','1','../images/690682b39d279_Mau_Chi_Lan_Soldevilla.png','2025-08-27 18:53:34'),
('46','rian123','$2y$10$Y/AjWMlL73/bsAd5dGukxeXBE/LqodPyFeYhIT191A/p1RJhs4k3e','Tambor@yahoo.com','Aldrian','Tambor','A','1','../images/Aldrian_Tambor.png','2025-08-27 18:59:44'),
('48','kurt123','$2y$10$SRRKjKgu5j7nMqLlZn9DMuts0cC2r.XdJLxgagKaqIyPjvjI/vlgS','Kurt@yahoo.com','Kurt Lance','Garcia','B','1','','2025-08-29 15:54:43'),
('57','darren123','darren123','example@yahoo.com','Darren','James','A','1','','2025-09-07 12:47:05'),
('58','jhimmel123','jhimmel123','example@yahoo.com','Jhimmel','lorem','B','1','','2025-09-07 12:47:39'),
('59','eman1234','eman1234','Mauchilan@yahoo.com','Eman','Barbin','A','1','','2025-09-11 15:19:07'),
('61','rian12345','rian12345','Rian@gmail.com','rian','Tambor','A','0','','2025-09-15 10:53:10'),
('70','marc123','marc123','marc@gmail.com','Marc Dominic','Goco','A','1','','2025-10-16 01:05:02');

DROP TABLE IF EXISTS `subjects`;
CREATE TABLE `subjects` (
  `subject_id` int(11) NOT NULL AUTO_INCREMENT,
  `teacher_id` int(11) NOT NULL,
  `subject_code` varchar(255) NOT NULL,
  `subject` varchar(225) DEFAULT NULL,
  `course` varchar(225) DEFAULT NULL,
  `yearlevel` varchar(255) DEFAULT NULL,
  `schoolyear_id` int(11) NOT NULL,
  `status` varchar(255) DEFAULT NULL,
  `subject_created` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`subject_id`),
  KEY `teacher_id` (`teacher_id`),
  KEY `schoolyear_id` (`schoolyear_id`),
  CONSTRAINT `subjects_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`),
  CONSTRAINT `subjects_ibfk_2` FOREIGN KEY (`schoolyear_id`) REFERENCES `schoolyear` (`schoolyear_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `subjects` VALUES
('3','1','CORE5','HCI','BSIT','2nd','2','1','2025-09-02 09:41:05'),
('4','4','MINOR2','PE 2',NULL,'2nd','2','1','2025-09-02 10:15:18'),
('5','4','','PE 4','BSHM','2nd','2','1','2025-09-02 10:18:55'),
('6','1','CORE4','SIA','BSIT','3rd','2','1','2025-09-02 11:20:36'),
('7','1','MINOR3','PE 4','BSIT','4th','3','1','2025-09-14 23:22:11'),
('8','1','MINOR2','PE3','BSIT','3rd','3','1','2025-09-15 01:32:48'),
('9','1','CORE3','Programming 1','BSIT','1st','2','1','2025-09-15 11:08:14'),
('10','3','','PE 5','BSIT','2nd','2','0','2025-09-15 11:19:20'),
('11','1','','Programming 3',NULL,'3rd','2','0','2025-10-14 17:48:27'),
('13','1','CORE2','Programming 2',NULL,'3rd','2','1','2025-10-16 01:18:21'),
('14','1','','Programming 3','BSIT','2nd','2','0','2025-10-16 01:19:43'),
('17','6','','capstone 2',NULL,'3rd','2','1','2025-10-16 01:29:22'),
('18','6','','Capstone1 111',NULL,'4th','2','0','2025-10-16 18:27:33'),
('19','1','CORE1','INTEG PROG',NULL,'3rd','3','1','2025-10-28 02:02:04'),
('20','1','MINOR1','GEN MATH',NULL,'2nd','3','1','2025-10-31 04:24:08');

DROP TABLE IF EXISTS `super_user`;
CREATE TABLE `super_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(225) NOT NULL,
  `password` varchar(225) NOT NULL,
  `pin` varchar(225) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `super_user` VALUES
('1','superadmin@dev.com','superadmin','112601');

DROP TABLE IF EXISTS `teachers`;
CREATE TABLE `teachers` (
  `teacher_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `createdAt` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`teacher_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `teachers` VALUES
('1','eman123','$2y$10$ws1kYuVXUamiT2X7ssQspugbGfV6t7j2iDTsm2kU9N0vD1AJ3pwk.','eman@gmail.com','Emman','Gumayagay','IT','1','2025-08-28 15:10:04'),
('3','bry123','$2y$10$V9lKbwWd1Se1Ay2Dx4JXYOXftql1/O23rZWCaY3GuwCAMXSpOW8AO','bryan@gmail.com','Bryan1','Superable','Faculty','1','2025-08-28 15:32:04'),
('4','rian12314','rian1234','Rian123@gmail.com','Aldrian','Tambor','Faculty','0','2025-08-29 15:55:28'),
('6','sirgab123','sirgab123','sirgab@gmail.com','Gabriel Thomas','Torneros','IT','1','2025-10-15 19:33:02'),
('7','norms123','norms123','Norms@gmail.com','Norms','Ortego','Faculty','1','2025-10-16 17:25:45');

SET FOREIGN_KEY_CHECKS=1;
