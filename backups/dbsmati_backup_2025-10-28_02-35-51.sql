-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: dbsmati
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `user_type` enum('admin','teacher','student') NOT NULL,
  `action` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`,`user_type`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=139 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
INSERT INTO `activity_logs` VALUES (5,1,'admin','CREATE_STUDENT','Created student account: Goco, Marc  (Set: A)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-15 17:05:02'),(12,6,'teacher','CREATE_SUBJECT','Created subject: capstone 2','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-15 17:29:22'),(17,1,'admin','UPDATE_STUDENT','Updated student account: Student ID = 70','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-16 09:18:26'),(18,1,'admin','DROP_STUDENT','Drop Student Account: Student ID = 70','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-16 09:21:29'),(19,1,'admin','CREATE_TEACHER','Created teacher account: Ortego, Norman (Department: Faculty)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-16 09:25:45'),(20,1,'admin','UPDATE_TEACHER','Updated teacher account: Teacher ID = 7','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-16 09:26:03'),(21,1,'admin','DROP_TEACHER','Drop teacher account: Teacher ID = 7','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-16 09:26:19'),(22,1,'admin','UPDATE_SUBJECT','Updated Subject Details: Programming 3','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-16 09:49:35'),(24,1,'admin','DROP_SUBJECT','Drop Subject: PE 5, 2025-2026, 1st Semester','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-16 09:54:50'),(25,1,'admin','CREATE_ANNOUNCEMENT','created new announcement. Check the Recent Announcement Board.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-16 10:02:57'),(26,1,'admin','UPDATE_ANNOUNCEMENT','updated announcement details. Check the Recent Announcement Board.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-16 10:03:21'),(27,1,'admin','DELETE_ANNOUNCEMENT','deleted an announcement.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-16 10:03:38'),(28,1,'admin','CREATE_SCHOOLYEAR','created new Schoolyear & Semester: 2026-2027, 1st','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-16 10:14:01'),(29,1,'admin','DELET_SCHOOLYEAR','deleted schoolyear & semester.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-16 10:14:44'),(30,1,'admin','RETRIEVE_ACCOUNT','retrieved student account: Student ID = 70','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-16 10:15:08'),(31,1,'admin','RETRIEVE_ACCOUNT','retrieved teacher account: Teacher ID = 7','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-16 10:15:17'),(32,1,'admin','RETRIEVE_SUBJECT','retrieved subject from the archive.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-16 10:15:26'),(33,6,'teacher','CREATE_SUBJECT','Created subject: Capstone1 1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-16 10:27:33'),(34,6,'teacher','UPDATE_SUBJECT','Updated subject details: Capstone1 11','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-16 10:28:43'),(35,6,'teacher','UPDATE_SUBJECT','Updated subject details: Capstone1 111','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-16 10:29:01'),(36,6,'teacher','DROP_SUBJECT','Drop a subject.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-16 10:29:20'),(40,1,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-16 11:57:36'),(41,1,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-16 12:19:52'),(42,1,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-16 12:23:34'),(43,1,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-16 12:43:18'),(44,1,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-16 13:13:04'),(45,1,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-16 14:55:08'),(46,1,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-16 14:57:41'),(47,1,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-16 16:01:16'),(48,1,'admin','DROP_STUDENT','Drop Student Account: Student ID = 70','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-16 16:49:24'),(49,1,'admin','RETRIEVE_ACCOUNT','retrieved student account: Student ID = 70','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-16 16:49:30'),(50,1,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-17 17:55:15'),(51,1,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-18 16:16:25'),(52,1,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-19 13:30:01'),(53,1,'admin','CREATE_ANNOUNCEMENT','created new announcement. Check the Recent Announcement Board.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-19 13:47:54'),(54,1,'admin','UPDATE_ANNOUNCEMENT','updated announcement details. Check the Recent Announcement Board.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-19 13:56:01'),(55,1,'admin','UPDATE_ANNOUNCEMENT','updated announcement details. Check the Recent Announcement Board.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-19 13:56:07'),(56,1,'admin','CREATE_ANNOUNCEMENT','created new announcement. Check the Recent Announcement Board.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-19 13:58:08'),(57,2,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-22 16:29:29'),(58,2,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-22 17:04:24'),(59,4,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-22 18:20:29'),(60,2,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-23 06:07:32'),(61,2,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-23 09:20:34'),(62,2,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-23 20:24:59'),(63,4,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-24 15:45:48'),(64,4,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-24 15:50:03'),(65,6,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-24 16:02:32'),(66,7,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-24 16:19:09'),(67,8,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-25 15:47:44'),(68,8,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-25 15:48:11'),(69,8,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-25 16:31:21'),(70,12,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-25 16:40:29'),(71,8,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-25 17:12:40'),(72,8,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-25 17:19:41'),(73,18,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-25 17:27:53'),(74,27,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-26 14:27:56'),(75,8,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-26 14:31:30'),(76,8,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-26 16:35:01'),(77,8,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-26 16:36:09'),(78,8,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-26 16:38:01'),(79,8,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-26 16:38:32'),(80,8,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-26 16:39:13'),(81,8,'admin','RESTORE_DATABASE','Restored database from backup file: dbsmati_backup_2025-10-26_17-51-16.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-26 16:53:02'),(82,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-26_18-00-25.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-26 17:00:25'),(83,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-26_18-00-26.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-26 17:00:26'),(84,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-27_01-02-11.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-26 17:02:12'),(85,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-27_01-02-12.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-26 17:02:13'),(86,8,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-26 17:07:36'),(87,8,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-26 17:07:58'),(88,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-27_01-08-13.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-26 17:08:13'),(89,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-27_01-08-14.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-26 17:08:14'),(90,8,'admin','RESTORE_DATABASE','Restored database from backup file: pre_restore_backup_2025-10-27_01-08-35.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 09:36:52'),(91,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-27_17-43-25.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 09:43:26'),(92,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-27_17-44-24.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 09:44:24'),(93,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-27_17-45-07.sql','::1','Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36','2025-10-27 09:45:07'),(94,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-27_17-45-53.sql','::1','Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36','2025-10-27 09:45:54'),(95,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-27_17-46-34.sql','::1','Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36','2025-10-27 09:46:34'),(96,8,'admin','RETRIEVE_ACCOUNT','retrieved student account: Student ID = 32','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 09:46:51'),(97,8,'admin','CREATE_SCHOOLYEAR','created new Schoolyear & Semester: 2026-2027, 1st Semester','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 09:47:03'),(98,8,'admin','CREATE_SCHOOLYEAR','created new Schoolyear & Semester: 2026-2027, 1st Semester','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 09:48:37'),(99,8,'admin','DELETE_SCHOOLYEAR','deleted schoolyear & semester.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 09:48:44'),(100,8,'admin','DELETE_SCHOOLYEAR','deleted schoolyear & semester.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 09:48:48'),(101,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-27_17-48-52.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 09:48:52'),(102,8,'admin','DELETE_SCHOOLYEAR','deleted schoolyear & semester.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 09:48:52'),(103,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-27_17-49-04.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 09:49:04'),(104,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-27_17-49-30.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 09:49:30'),(105,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-27_17-50-16.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 09:50:16'),(106,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-27_17-55-24.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 09:55:24'),(107,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-27_17-57-43.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 09:57:43'),(108,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-27_17-59-38.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 09:59:38'),(109,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-27_17-59-52.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 09:59:52'),(110,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-27_18-00-41.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 10:00:41'),(111,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-27_18-01-10.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 10:01:11'),(112,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-27_18-01-21.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 10:01:21'),(113,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-27_18-04-57.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 10:04:57'),(114,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-27_18-05-00.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 10:05:00'),(115,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-27_18-05-04.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 10:05:04'),(116,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-27_18-06-32.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 10:06:32'),(117,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-27_18-06-47.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 10:06:47'),(118,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-27_18-07-31.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 10:07:31'),(119,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-27_18-11-31.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 10:11:31'),(120,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-27_18-11-52.sql','::1','Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36','2025-10-27 10:11:52'),(121,8,'admin','RESTORE_DATABASE','Restored database from backup file: dbsmati_backup_2025-10-27_18-11-58.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 10:33:08'),(122,1,'teacher','CREATE_SUBJECT','Created subject: INTEG PROG','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 18:02:04'),(123,1,'teacher','UPDATE_SUBJECT','Updated subject details: PE3','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 18:04:40'),(124,1,'teacher','UPDATE_SUBJECT','Updated subject details: PE 4','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 18:04:49'),(125,1,'teacher','UPDATE_SUBJECT','Updated subject details: Programming 2','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 18:10:30'),(126,1,'teacher','UPDATE_SUBJECT','Updated subject details: Programming 1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 18:10:36'),(127,1,'teacher','UPDATE_SUBJECT','Updated subject details: SIA','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 18:10:42'),(128,1,'teacher','UPDATE_SUBJECT','Updated subject details: HCI','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 18:10:47'),(129,8,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 18:16:58'),(130,8,'admin','LOGIN','logged in to the system.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 18:29:35'),(131,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-28_02-34-36.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 18:34:37'),(132,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-28_02-34-43.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 18:34:43'),(133,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-28_02-34-45.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 18:34:45'),(134,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-28_02-34-48.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 18:34:48'),(135,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-28_02-35-31.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 18:35:31'),(136,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-28_02-35-33.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 18:35:33'),(137,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-28_02-35-34.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 18:35:35'),(138,8,'admin','CREATE_BACKUP','Created database backup: dbsmati_backup_2025-10-28_02-35-45.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-27 18:35:45');
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` VALUES (6,'admin','$2y$10$5wDMCLWmPQ1v1Tpsv1qU1uAEHqOPbQ5UY3IN5sUZquUl8TYPY.xuq','admin@example.com','friend','mau','Admin123','2025-10-25 00:01:48'),(7,'admin','$2y$10$WYiZaJUODnsvMtyx1jpSGO/3ZOV/5RdW0OpFJ8O.jqT4Ns6yRVkV.','admin@gmail.com','friend','mau','','2025-10-25 00:13:07'),(8,'admin','$2y$10$x2UmIqcKKPoLenosHpvXTe84ticUxYkF4.SFNZ8TF3ky4YZweDAoi','Mauchilan@yahoo.com','friend','mau','','2025-10-25 02:43:37'),(18,'admin','$2y$10$cro8uBCFDrJrEOzFGHBaDuULlFwv5NxH9mRqRMhv39LI5Q.b/oN0G','Mau@gmail.com','friend','mau','','2025-10-26 01:27:18'),(27,'admin','$2y$10$pVRqfUu9B7/mYqdukhtE5.rHuVm5tXQ2ZyqQyV0WHdSXWb52DUgLC','test@gmail.com','friend','mau','$2y$10$pVRqfUu9B7/mYqdukhtE5.rHuVm5tXQ2ZyqQyV0WHdSXWb52DUgLC','2025-10-26 02:40:51');
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `announcements` (
  `announcement_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(225) NOT NULL,
  `details` text NOT NULL,
  `type` varchar(225) NOT NULL,
  `createdAt` datetime NOT NULL,
  PRIMARY KEY (`announcement_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `announcements`
--

LOCK TABLES `announcements` WRITE;
/*!40000 ALTER TABLE `announcements` DISABLE KEYS */;
INSERT INTO `announcements` VALUES (5,'PRELIM EXAMINATION SY.2025-2026 ','October 21-22, 2025 is the prelim examintation for College level. STRICTLY NO premit, NO exam.','High','2025-10-15 23:00:02'),(6,'MIDTERM EXAMINATION  SY. 2025-2026','November 21-22, 2025. STRICTLY NO Permit, NO Exam. ','High','2025-10-15 23:10:48'),(8,'PRE FINAL EXAMINATION SY.2025-2026','please settle your payment to avoid penalty.','High','2025-10-19 21:47:54'),(9,'Datamex Foundation Day 2025','Oct. 28-29, 2025. Attendance is a must.','Low','2025-10-19 21:58:08');
/*!40000 ALTER TABLE `announcements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auth`
--

DROP TABLE IF EXISTS `auth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `password` varchar(255) NOT NULL,
  `pin` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth`
--

LOCK TABLES `auth` WRITE;
/*!40000 ALTER TABLE `auth` DISABLE KEYS */;
INSERT INTO `auth` VALUES (1,'smati2025','112601');
/*!40000 ALTER TABLE `auth` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `concern`
--

DROP TABLE IF EXISTS `concern`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `concern`
--

LOCK TABLES `concern` WRITE;
/*!40000 ALTER TABLE `concern` DISABLE KEYS */;
INSERT INTO `concern` VALUES (13,45,'B','Mau@gmail.com',1,'Grades','asdasdasdasdasd','SMATI2025-001','Case Closed','2025-10-08'),(14,45,'B','Mau@gmail.com',1,'Attendance','asdadsasdsad','SMATI2025-002','Case Closed','2025-10-08'),(15,45,'B','Mau@gmail.com',1,'Grades','LOREM IPSIUM TEST TESTEST','SMATI2025-003','Case Closed','2025-10-12');
/*!40000 ALTER TABLE `concern` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grades`
--

DROP TABLE IF EXISTS `grades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=281 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grades`
--

LOCK TABLES `grades` WRITE;
/*!40000 ALTER TABLE `grades` DISABLE KEYS */;
INSERT INTO `grades` VALUES (257,3,1,45,2,'97','88','99','90',93.50,1.50,'Passed','','2025-09-11 09:54:50'),(258,3,1,48,2,'90','89','81','82',85.50,2.00,'Passed','','2025-09-11 09:54:50'),(259,3,1,58,2,'83','84','84','81',83.00,2.50,'Passed','','2025-09-11 09:54:50'),(263,7,1,45,3,'89','88','82','90',87.25,2.00,'Passed','Good Job','2025-09-14 15:23:38'),(264,7,1,48,3,NULL,NULL,NULL,NULL,0.00,5.00,'Failed','','2025-09-14 15:23:38'),(265,7,1,58,3,NULL,NULL,NULL,NULL,0.00,5.00,'Failed','','2025-09-14 15:23:38'),(266,6,1,45,2,'90','92','93','94',92.25,1.50,'Passed','Goob Job','2025-09-14 15:51:14'),(267,6,1,48,2,NULL,NULL,NULL,NULL,0.00,5.00,'Failed','','2025-09-14 15:51:14'),(268,6,1,58,2,NULL,NULL,NULL,NULL,0.00,5.00,'Failed','','2025-09-14 15:51:14'),(269,9,1,45,2,'88','81','83','85',84.25,2.50,'Passed','','2025-09-15 03:09:15'),(270,9,1,48,2,'98','79','85','70',83.00,2.50,'Passed','','2025-09-15 03:09:15'),(271,9,1,58,2,NULL,NULL,NULL,NULL,0.00,5.00,'Failed','','2025-09-15 03:09:15'),(275,10,3,45,2,'88','87','89','90',88.50,2.00,'Passed','','2025-09-15 03:19:50'),(276,10,3,48,2,NULL,NULL,NULL,NULL,0.00,5.00,'Failed','','2025-09-15 03:19:50'),(277,10,3,58,2,NULL,NULL,NULL,NULL,0.00,5.00,'Failed','','2025-09-15 03:19:50'),(278,19,1,45,3,'89','98',NULL,'88',91.67,1.50,'Passed','Good Job','2025-10-27 18:09:32'),(279,19,1,48,3,NULL,NULL,NULL,NULL,0.00,5.00,'Failed','','2025-10-27 18:09:32'),(280,19,1,58,3,NULL,NULL,NULL,NULL,0.00,5.00,'Failed','','2025-10-27 18:09:32');
/*!40000 ALTER TABLE `grades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schoolyear`
--

DROP TABLE IF EXISTS `schoolyear`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `schoolyear` (
  `schoolyear_id` int(11) NOT NULL AUTO_INCREMENT,
  `schoolyear` varchar(225) DEFAULT NULL,
  `semester` varchar(225) DEFAULT NULL,
  PRIMARY KEY (`schoolyear_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schoolyear`
--

LOCK TABLES `schoolyear` WRITE;
/*!40000 ALTER TABLE `schoolyear` DISABLE KEYS */;
INSERT INTO `schoolyear` VALUES (2,'2025-2026','1st'),(3,'2025-2026','2nd');
/*!40000 ALTER TABLE `schoolyear` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_list`
--

DROP TABLE IF EXISTS `student_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student_list` (
  `list_id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_id` int(11) NOT NULL,
  `student_set` varchar(255) NOT NULL,
  PRIMARY KEY (`list_id`),
  KEY `subject_id` (`subject_id`),
  CONSTRAINT `student_list_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_list`
--

LOCK TABLES `student_list` WRITE;
/*!40000 ALTER TABLE `student_list` DISABLE KEYS */;
INSERT INTO `student_list` VALUES (8,3,'B'),(9,3,'A'),(10,6,'B'),(12,6,'A'),(13,7,'A'),(14,7,'B'),(15,8,'A'),(16,8,'B'),(17,9,'A'),(18,9,'B'),(19,10,'B'),(20,17,'A'),(21,17,'B'),(22,19,'A'),(23,19,'B');
/*!40000 ALTER TABLE `student_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `students` (
  `student_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(225) NOT NULL,
  `password` varchar(225) NOT NULL,
  `email` varchar(225) NOT NULL,
  `firstname` varchar(225) NOT NULL,
  `lastname` varchar(225) NOT NULL,
  `course` varchar(225) NOT NULL,
  `status` varchar(255) DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`student_id`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
INSERT INTO `students` VALUES (32,'eman1234','eman123','eman@gmail.com','Eman','Gumayagay','A','1','2025-08-26 09:27:43'),(45,'mau123','mau123','Mau@gmail.com','Mau','Soldevilla','B','1','2025-08-27 10:53:34'),(46,'rian123','rian1234','Tambor@yahoo.com','Aldrian','Tambor','A','1','2025-08-27 10:59:44'),(48,'kurt123','kurt123','Kurt@yahoo.com','Kurt Lance','Garcia','B','1','2025-08-29 07:54:43'),(57,'darren123','darren123','example@yahoo.com','Darren','James','A','1','2025-09-07 04:47:05'),(58,'jhimmel123','jhimmel123','example@yahoo.com','Jhimmel','lorem','B','1','2025-09-07 04:47:39'),(59,'eman1234','eman1234','Mauchilan@yahoo.com','Eman','Barbin','A','1','2025-09-11 07:19:07'),(61,'rian12345','rian12345','Rian@gmail.com','rian','Tambor','A','0','2025-09-15 02:53:10'),(70,'marc123','marc123','marc@gmail.com','Marc Dominic','Goco','A','1','2025-10-15 17:05:02');
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subjects`
--

LOCK TABLES `subjects` WRITE;
/*!40000 ALTER TABLE `subjects` DISABLE KEYS */;
INSERT INTO `subjects` VALUES (3,1,'CORE5','HCI','BSIT','2nd',2,'1','2025-09-02 01:41:05'),(4,4,'','PE 2','BSHM','2nd',2,'1','2025-09-02 02:15:18'),(5,4,'','PE 4','BSHM','2nd',2,'1','2025-09-02 02:18:55'),(6,1,'CORE4','SIA','BSIT','3rd',2,'1','2025-09-02 03:20:36'),(7,1,'MINOR3','PE 4','BSIT','4th',3,'1','2025-09-14 15:22:11'),(8,1,'MINOR2','PE3','BSIT','3rd',3,'1','2025-09-14 17:32:48'),(9,1,'CORE3','Programming 1','BSIT','1st',2,'1','2025-09-15 03:08:14'),(10,3,'','PE 5','BSIT','2nd',2,'0','2025-09-15 03:19:20'),(11,1,'','Programming 3',NULL,'3rd',2,'0','2025-10-14 09:48:27'),(13,1,'CORE2','Programming 2',NULL,'3rd',2,'1','2025-10-15 17:18:21'),(14,1,'','Programming 3','BSIT','2nd',2,'0','2025-10-15 17:19:43'),(17,6,'','capstone 2',NULL,'3rd',2,'1','2025-10-15 17:29:22'),(18,6,'','Capstone1 111',NULL,'4th',2,'0','2025-10-16 10:27:33'),(19,1,'CORE1','INTEG PROG',NULL,'3rd',3,'1','2025-10-27 18:02:04');
/*!40000 ALTER TABLE `subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `super_user`
--

DROP TABLE IF EXISTS `super_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `super_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(225) NOT NULL,
  `password` varchar(225) NOT NULL,
  `pin` varchar(225) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `super_user`
--

LOCK TABLES `super_user` WRITE;
/*!40000 ALTER TABLE `super_user` DISABLE KEYS */;
INSERT INTO `super_user` VALUES (1,'superadmin@dev.com','superadmin','112601');
/*!40000 ALTER TABLE `super_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teachers`
--

DROP TABLE IF EXISTS `teachers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teachers`
--

LOCK TABLES `teachers` WRITE;
/*!40000 ALTER TABLE `teachers` DISABLE KEYS */;
INSERT INTO `teachers` VALUES (1,'eman123','eman123','eman@gmail.com','Emman','Gumayagay','IT','1','2025-08-28 07:10:04'),(3,'bry123','bry123','bryan@gmail.com','Bryan','Superable','Faculty','1','2025-08-28 07:32:04'),(4,'rian12314','rian1234','Rian123@gmail.com','Aldrian','Tambor','Faculty','0','2025-08-29 07:55:28'),(6,'sirgab123','sirgab123','sirgab@gmail.com','Gabriel Thomas','Torneros','IT','1','2025-10-15 11:33:02'),(7,'norms123','norms123','Norms@gmail.com','Norms','Ortego','Faculty','1','2025-10-16 09:25:45');
/*!40000 ALTER TABLE `teachers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'dbsmati'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-28  2:35:51
