-- MySQL dump 10.13  Distrib 8.0.28, for Linux (x86_64)
--
-- Host: localhost    Database: laravel-netpolgar
-- ------------------------------------------------------
-- Server version	8.0.28-0ubuntu0.20.04.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `accounts`
--

USE netpolgar-test;

DROP TABLE IF EXISTS `accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'egyedi rekord ID',
  `from_type` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'köldő tipus:  teams|users',
  `from` bigint unsigned NOT NULL COMMENT 'from_type.id',
  `target_type` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'címzett tipus: teams|users',
  `target` bigint unsigned NOT NULL COMMENT 'target_type.id',
  `status` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'üres|allocated',
  `value` double(8,2) DEFAULT NULL COMMENT 'összeg',
  `comment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'publikus közlemény',
  `info` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'program által használt közlemény',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'rekord létrehozás időpontja',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'rekord modosítás időpontja',
  PRIMARY KEY (`id`),
  UNIQUE KEY `accounts_id_unique` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounts`
--

LOCK TABLES `accounts` WRITE;
/*!40000 ALTER TABLE `accounts` DISABLE KEYS */;
INSERT INTO `accounts` VALUES (1,'',0,'users',1,'',500.00,'Kezdeti feltöltés','startInit','2022-02-07 13:01:18','2022-02-07 13:01:18');
/*!40000 ALTER TABLE `accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ballots`
--

DROP TABLE IF EXISTS `ballots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ballots` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'egyedi rekord ID',
  `poll_id` bigint unsigned NOT NULL COMMENT 'vita/szavazás ID',
  `user_id` bigint unsigned NOT NULL COMMENT 'felhasználó',
  PRIMARY KEY (`id`),
  KEY `ballots_poll_id_foreign` (`poll_id`),
  KEY `user_id_index` (`user_id`),
  CONSTRAINT `ballots_poll_id_foreign` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ballots`
--

LOCK TABLES `ballots` WRITE;
/*!40000 ALTER TABLE `ballots` DISABLE KEYS */;
INSERT INTO `ballots` VALUES (1,3,2),(2,3,3),(3,3,1);
/*!40000 ALTER TABLE `ballots` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `evaluations`
--

DROP TABLE IF EXISTS `evaluations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `evaluations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'egyedi rekord ID',
  `product_id` bigint unsigned NOT NULL COMMENT 'termék',
  `user_id` bigint unsigned NOT NULL COMMENT 'felhasználó',
  `value` int DEFAULT NULL COMMENT 'érdemjegy 1 - 5',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'létrehozás időpontja',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'utolsó modosítás időpontja',
  PRIMARY KEY (`id`),
  KEY `evaluations_product_id_foreign1` (`product_id`),
  KEY `evaluations_user_id_foreign` (`user_id`),
  CONSTRAINT `evaluations_product_id_foreign1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `evaluations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `evaluations`
--

LOCK TABLES `evaluations` WRITE;
/*!40000 ALTER TABLE `evaluations` DISABLE KEYS */;
/*!40000 ALTER TABLE `evaluations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `events` (
  `id` bigint unsigned NOT NULL COMMENT 'egyedi rekord ID',
  `parent_type` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'szervező tipusa: teams|projects',
  `parent` bigint unsigned NOT NULL COMMENT 'szervező ID',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'esemény rövid neve',
  `description` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'esemény leírása',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'avatar kép url',
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'helyszin',
  `date` date DEFAULT NULL COMMENT 'dátum',
  `hours` int DEFAULT NULL COMMENT 'kezdés óra',
  `minute` int DEFAULT NULL COMMENT 'kezdés perc',
  `length` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'esemény időtartama',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'rekord létrehozás időpontja',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'utolsó modosítás időpontja',
  `created_by` bigint unsigned NOT NULL COMMENT 'létrehozó user',
  PRIMARY KEY (`id`,`length`),
  KEY `events_created_by_foreign` (`created_by`),
  CONSTRAINT `events_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `files` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'egyedi ID',
  `parent_type` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'tulajdonos tipusa:  teams|projects|products|events|users',
  `parent` bigint unsigned NOT NULL COMMENT 'tilajdonos ID',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'rövid megnevezés',
  `description` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'leírás',
  `type` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'file tipus',
  `licence` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'licensz',
  `created_by` bigint unsigned NOT NULL COMMENT 'feltöltő user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'rekord létrehozás időpontja',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'rekord utolsó modosítás időpontja',
  PRIMARY KEY (`id`),
  KEY `files_created_by_foreign` (`created_by`),
  CONSTRAINT `files_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `files`
--

LOCK TABLES `files` WRITE;
/*!40000 ALTER TABLE `files` DISABLE KEYS */;
/*!40000 ALTER TABLE `files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `likes`
--

DROP TABLE IF EXISTS `likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `likes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parent_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'teams|members|projects|polls|options|products',
  `parent` bigint unsigned DEFAULT NULL COMMENT 'parent_type.id',
  `user_id` bigint unsigned NOT NULL COMMENT 'véleményező user',
  `like_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'like|dislike',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'rekord létrehozás időpontjaa',
  `updated_at` timestamp NOT NULL COMMENT 'utolsó módosítás időpontja',
  PRIMARY KEY (`id`),
  KEY `likes_parent_type_parent_index` (`parent_type`,`parent`),
  KEY `user_id_foreign` (`user_id`),
  CONSTRAINT `user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `likes`
--

LOCK TABLES `likes` WRITE;
/*!40000 ALTER TABLE `likes` DISABLE KEYS */;
INSERT INTO `likes` VALUES (17,'projects',1,2,'like','2022-02-09 07:46:04','2022-02-09 07:46:04');
/*!40000 ALTER TABLE `likes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `members`
--

DROP TABLE IF EXISTS `members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `members` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'Egyedi rekord ID',
  `parent_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'teams|projects|events|files',
  `parent` bigint unsigned DEFAULT NULL COMMENT 'teams.id|projects.id|events.id',
  `user_id` bigint unsigned DEFAULT NULL COMMENT 'user.id',
  `rank` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'member|admin|moderator|...a teamben definiált tisztségek...',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'proposal|active|closed|excluded',
  `activated_at` date DEFAULT NULL COMMENT 'érvénybelépés dátuma',
  `closed_at` date DEFAULT NULL COMMENT 'megszünés dátuma',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'rekord létrehozás időpontja',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'utolsó modosítás időpontja',
  `created_by` bigint unsigned NOT NULL COMMENT 'rekordot létrehozó user',
  PRIMARY KEY (`id`),
  KEY `parent_type_parent_index` (`parent_type`,`parent`),
  KEY `members_created_by_foreign` (`created_by`),
  KEY `members_user_id_foreign` (`user_id`),
  CONSTRAINT `members_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `members_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=124 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `members`
--

LOCK TABLES `members` WRITE;
/*!40000 ALTER TABLE `members` DISABLE KEYS */;
INSERT INTO `members` VALUES (113,'teams',1,94,'member','active',NULL,NULL,'2022-02-11 15:21:18','2022-02-11 15:21:18',94),(114,'teams',1,95,'member','active',NULL,NULL,'2022-02-11 15:21:18','2022-02-11 15:21:18',95),(115,'teams',1,2,'member','active',NULL,NULL,'2022-02-11 15:21:18','2022-02-11 15:21:18',2),(116,'teams',1,3,'member','active',NULL,NULL,'2022-02-11 15:21:18','2022-02-11 15:21:18',3),(117,'teams',1,1,'member','active',NULL,NULL,'2022-02-11 15:21:18','2022-02-11 15:21:18',1),(120,'teams',2,1,'member','active',NULL,NULL,'2022-02-10 23:00:00','2022-02-10 23:00:00',1),(121,'teams',2,1,'admin','active',NULL,NULL,'2022-02-10 23:00:00','2022-02-11 15:24:25',1),(122,'teams',6,1,'member','active',NULL,NULL,'2022-02-11 14:28:13','2022-02-11 14:28:13',1),(123,'teams',6,1,'admin','active',NULL,NULL,'2022-02-11 14:28:53','2022-02-11 14:28:53',1);
/*!40000 ALTER TABLE `members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'egyedi rekord ID',
  `parent_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'teams|projects|tasks|polls|options|users|events|files',
  `parent` bigint unsigned NOT NULL COMMENT 'parent_type.id',
  `reply_to` bigint unsigned DEFAULT NULL COMMENT 'ha ez egy válasz, akkor erre válaszol (messages.id)',
  `user_id` bigint unsigned NOT NULL COMMENT 'üzenete küldő felhasználó',
  `msg_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'későbbi fejlesztésre',
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'üzenet szövege',
  `moderated_by` bigint unsigned DEFAULT NULL COMMENT 'moderátor user.id',
  `moderator_info` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'moderátor megjegyzése',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'létrehozás időpontja',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'utolsó modosítás időpontja',
  PRIMARY KEY (`id`),
  KEY `messages_parent_type_parent_index` (`parent_type`,`parent`),
  KEY `messages_user_id_foreign` (`user_id`),
  CONSTRAINT `messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` VALUES (1,'polls',4,0,2,'','teszt1 hozzászólása a Titkos szavazáshoz',0,'','2022-02-08 15:48:01','2022-02-08 15:48:01'),(2,'teams',1,0,1,'','Irok egy üzenetet',0,'','2022-02-11 06:57:40','2022-02-11 06:57:40');
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (2,'2022_02_07_064822_create_accounts_table',1),(3,'2022_02_07_064823_create_users_table',1),(4,'2022_02_07_064824_create_polls_table',1),(5,'2022_02_07_064825_create_ballots_table',1),(6,'2022_02_07_064826_create_products_table',1),(7,'2022_02_07_064827_create_evaluations_table',1),(8,'2022_02_07_064828_create_events_table',1),(9,'2022_02_07_064829_create_failed_jobs_table',1),(10,'2022_02_07_064830_create_files_table',2),(11,'2022_02_07_064831_create_likes_table',2),(12,'2022_02_07_064832_create_members_table',2),(13,'2022_02_07_064833_create_messages_table',2),(14,'2022_02_07_064834_create_msgreads_table',2),(15,'2022_02_07_064835_create_options_table',2),(16,'2022_02_07_064836_create_orders_table',2),(17,'2022_02_07_064837_create_orderitems_table',2),(18,'2022_02_07_064838_create_password_resets_table',2),(19,'2022_02_07_064840_create_productadds_table',2),(20,'2022_02_07_064841_create_productcats_table',2),(21,'2022_02_07_064842_create_teams_table',3),(22,'2022_02_07_064843_create_projects_table',3),(23,'2022_02_07_064844_create_sessions_table',3),(24,'2022_02_07_064845_create_tasks_table',3),(25,'2022_02_07_064846_create_votes_table',3);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `msgreads`
--

DROP TABLE IF EXISTS `msgreads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `msgreads` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'egyedi rekord ID',
  `msg_id` bigint unsigned NOT NULL COMMENT 'messages.id',
  `user_id` bigint unsigned NOT NULL COMMENT 'user.id (aki olvasta az üzenetet)',
  PRIMARY KEY (`id`),
  KEY `msgreads_msg_id_foreign` (`msg_id`),
  KEY `msgreads_user_id_foreign` (`user_id`),
  CONSTRAINT `msgreads_msg_id_foreign` FOREIGN KEY (`msg_id`) REFERENCES `messages` (`id`),
  CONSTRAINT `msgreads_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `msgreads`
--

LOCK TABLES `msgreads` WRITE;
/*!40000 ALTER TABLE `msgreads` DISABLE KEYS */;
INSERT INTO `msgreads` VALUES (1,1,2),(2,2,1);
/*!40000 ALTER TABLE `msgreads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `options`
--

DROP TABLE IF EXISTS `options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `options` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'egyedi rekord ID',
  `poll_id` bigint unsigned NOT NULL COMMENT 'vita, szavazás',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'rövid megnevezés',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'proposal|active',
  `description` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'leírás',
  `created_by` bigint unsigned NOT NULL COMMENT 'létrehozó user',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'létrehozás időpontja',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'utolsó modositás időpontja',
  PRIMARY KEY (`id`),
  KEY `options_poll_id_foreign` (`poll_id`),
  KEY `options_user_id_foreign` (`created_by`),
  CONSTRAINT `options_poll_id_foreign` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`),
  CONSTRAINT `options_user_id_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `options`
--

LOCK TABLES `options` WRITE;
/*!40000 ALTER TABLE `options` DISABLE KEYS */;
INSERT INTO `options` VALUES (3,4,'Igen','active','',2,'2022-02-08 15:47:08','2022-02-08 15:47:08'),(4,4,'Nem','active','',2,'2022-02-08 15:47:08','2022-02-08 15:47:08');
/*!40000 ALTER TABLE `options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orderitems`
--

DROP TABLE IF EXISTS `orderitems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orderitems` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'egyedi rekord ID',
  `order_id` bigint unsigned NOT NULL COMMENT 'megrendelés ID',
  `product_id` bigint unsigned NOT NULL COMMENT 'termék ID',
  `quantity` double DEFAULT NULL COMMENT 'mennyiség',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'open|ordering|confirmed|denied|closed1|closed2|canceled',
  `confirmInfo` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'információ',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'rekord létrehozás időpontja',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'utolsó modositás időpontja',
  PRIMARY KEY (`id`),
  KEY `orderitems_order_id_foreign` (`order_id`),
  KEY `orderitems_product_id_foreign` (`product_id`),
  CONSTRAINT `orderitems_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  CONSTRAINT `orderitems_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orderitems`
--

LOCK TABLES `orderitems` WRITE;
/*!40000 ALTER TABLE `orderitems` DISABLE KEYS */;
/*!40000 ALTER TABLE `orderitems` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'egyedi rekord ID',
  `customer_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'megrendelő tipusa teams|users',
  `customer` bigint unsigned NOT NULL COMMENT 'megrendelő ID',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'open|ordering|closed2|closed2',
  `description` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'leírás',
  `address` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'szállítász, átvételi cím',
  `shipping` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'szállítási mód',
  `confirmInfo` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'információ',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'rekord létrehozás időpontja',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'rekord modositás időpontja',
  PRIMARY KEY (`id`),
  KEY `orders_user_id_index` (`customer_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `polls`
--

DROP TABLE IF EXISTS `polls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `polls` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'rekord egyedi ID',
  `parent_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'parent tipus teams|projects',
  `parent` bigint unsigned NOT NULL COMMENT 'parent_type.id',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'rövid megnevezés',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'proposal|deabate|voks|closed',
  `description` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'leírás',
  `config` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'beállítások JSON string',
  `created_by` bigint unsigned NOT NULL COMMENT 'létrehozó user',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'rekord létrehozás időpontja',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'utolsó modosítás időpontja',
  `debate_start` date DEFAULT NULL COMMENT 'vita megindulásának dátuma',
  PRIMARY KEY (`id`),
  KEY `polls_parent_type_parent_index` (`parent_type`,`parent`),
  KEY `polls_created_by_foreign` (`created_by`),
  CONSTRAINT `polls_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `polls`
--

LOCK TABLES `polls` WRITE;
/*!40000 ALTER TABLE `polls` DISABLE KEYS */;
INSERT INTO `polls` VALUES (3,'teams',6,'Próba szavazás','closed','ez egy próba','{\"pollType\":\"pref\",\"secret\":\"0\",\"liquied\":\"1\",\"debateStart\":\"0\",\"optionActivate\":\"0\",\"debateDays\":\"1\",\"voteDays\":\"10\",\"valid\":\"30\"}',2,'2022-02-08 10:07:47','2022-02-08 10:07:47','2022-01-01'),(4,'teams',6,'Titkos szavazás','debate','mi ez?','{\"pollType\":\"yesno\",\"secret\":\"1\",\"liquied\":\"0\",\"debateStart\":\"0\",\"optionActivate\":\"0\",\"debateDays\":\"1\",\"voteDays\":\"5\",\"valid\":\"30\"}',2,'2022-02-08 14:47:07','2022-02-08 14:47:07','2022-02-08');
/*!40000 ALTER TABLE `polls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productadds`
--

DROP TABLE IF EXISTS `productadds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `productadds` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'egyedi rekord ID',
  `product_id` bigint unsigned NOT NULL COMMENT 'produkt ID',
  `quantity` double DEFAULT NULL COMMENT 'mennyiség',
  `user_id` bigint unsigned NOT NULL COMMENT 'létrehozó user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'rekord létrehozás időpontja',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'utolsó modositás időpontja',
  PRIMARY KEY (`id`),
  KEY `productadds_user_id_foreign` (`user_id`),
  KEY `productadds_product_id_index` (`product_id`),
  CONSTRAINT `productadds_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productadds`
--

LOCK TABLES `productadds` WRITE;
/*!40000 ALTER TABLE `productadds` DISABLE KEYS */;
/*!40000 ALTER TABLE `productadds` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productcats`
--

DROP TABLE IF EXISTS `productcats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `productcats` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'Egyedi rekord ID',
  `product_id` bigint unsigned NOT NULL COMMENT 'produkt ID',
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'kategória ID',
  PRIMARY KEY (`id`),
  KEY `productcats_product_id_foreign` (`product_id`),
  KEY `productcats_category_index` (`category`),
  CONSTRAINT `productcats_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productcats`
--

LOCK TABLES `productcats` WRITE;
/*!40000 ALTER TABLE `productcats` DISABLE KEYS */;
/*!40000 ALTER TABLE `productcats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'egyedi rekord ID',
  `parent_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'gyártó, forgalmazó tipus:  teams|users',
  `parent` bigint unsigned NOT NULL COMMENT 'parent_type.id',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'rövid megnevezés',
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'mértékegység',
  `price` double DEFAULT NULL COMMENT 'egységár',
  `currency` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'NTC' COMMENT 'pénznem',
  `vat` double DEFAULT NULL COMMENT 'ÁFA%',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'tipus',
  `description` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'leírás',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'avatar kép url',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'active|disabled',
  `stock` double DEFAULT NULL COMMENT 'készlet',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'rekord létrehozás időpontja',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'utolsó modosítás időpontja',
  PRIMARY KEY (`id`),
  KEY `team_id` (`parent_type`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'users',1,'próba termék','db',0,'HUF',NULL,NULL,'wwwwwww','/img/homokozo.png','active',0,'2022-02-11 06:11:30','2022-02-11 06:11:30');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `projects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'egyedi rekord ID',
  `team_id` bigint unsigned NOT NULL COMMENT 'csoport ID',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'projekt rövid neve',
  `description` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'projekt leírása',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'projekt avatar kép url',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'proposal|active|closed',
  `deadline` date DEFAULT NULL COMMENT 'határidő',
  `config` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'JSON string',
  `activated_at` date DEFAULT NULL COMMENT 'aktiválás dátuma',
  `closed_at` date DEFAULT NULL COMMENT 'lezárás dátuma',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'rekord létrehozás időpontja',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'rekord utolsó modositás időpontja',
  `created_by` bigint unsigned NOT NULL COMMENT 'létrehozó user',
  PRIMARY KEY (`id`),
  KEY `projects_created_by_foreign` (`created_by`),
  KEY `team_id_foreign` (`team_id`),
  CONSTRAINT `projects_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` VALUES (1,6,'próba projekt','','/img/project.png','active','2022-02-09','{\"ranks\":[\"admin\",\"president\",\"manager\",\"moderator\",\"accredited\"],\"close\":\"98\",\"memberActivate\":\"2\",\"memberExclude\":\"95\",\"rankActivate\":\"40\",\"rankClose\":\"95\",\"debateActivate\":\"2\"}',NULL,NULL,'2022-02-09 07:45:36','2022-02-09 07:46:05',2);
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('A9pR3gGVLBTeDrz4K0MsvkmiJyADJNvAKMdMRMAv',1,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.87 Safari/537.36','YTo3OntzOjY6Il90b2tlbiI7czo0MDoidHVnT3FRamdieFVrVm5pSkRpdEJ0dzRmRktqMVVBNGxSeU8wbnN0eCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC90ZWFtcy82Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czoxMjoidGVhbUluZGV4VXJsIjtzOjM3OiJodHRwOi8vbG9jYWxob3N0OjgwMDAvcGFyZW50cy8wL3RlYW1zIjtzOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjYwOiIkMnkkMTAkWnNleW5WZHpnbHJwQnIxVUlCS3BMdVVEckt6VjRkNWRIMDJ0TDMvMjBlVGRLTFg4c1JUWU8iO3M6ODoidGFza0luZm8iO086ODoic3RkQ2xhc3MiOjY6e3M6MTE6IlJFUVVFU1RfVVJJIjtzOjg6Ii90ZWFtcy82IjtzOjE0OiJSRVFVRVNUX01FVEhPRCI7czozOiJHRVQiO3M6MTU6IkhUVFBfVVNFUl9BR0VOVCI7czoxMDQ6Ik1vemlsbGEvNS4wIChYMTE7IExpbnV4IHg4Nl82NCkgQXBwbGVXZWJLaXQvNTM3LjM2IChLSFRNTCwgbGlrZSBHZWNrbykgQ2hyb21lLzk4LjAuNDc1OC44NyBTYWZhcmkvNTM3LjM2IjtzOjg6IlJFUVVFU1RTIjthOjA6e31zOjg6IlNFU1NJT05TIjthOjc6e3M6NjoiX3Rva2VuIjtzOjQwOiJ0dWdPcVFqZ2J4VWtWbmlKRGl0QnR3NGZGS2oxVUE0bFJ5TzBuc3R4IjtzOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czo3MjoiaHR0cDovL2xvY2FsaG9zdDo4MDAwL21lbWJlci9zdG9yZT9wYXJlbnQ9NiZwYXJlbnRfdHlwZT10ZWFtcyZyYW5rPWFkbWluIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MTp7aTowO3M6Nzoic3VjY2VzcyI7fXM6MzoibmV3IjthOjA6e319czoxMjoidGVhbUluZGV4VXJsIjtzOjM3OiJodHRwOi8vbG9jYWxob3N0OjgwMDAvcGFyZW50cy8wL3RlYW1zIjtzOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjYwOiIkMnkkMTAkWnNleW5WZHpnbHJwQnIxVUlCS3BMdVVEckt6VjRkNWRIMDJ0TDMvMjBlVGRLTFg4c1JUWU8iO3M6Nzoic3VjY2VzcyI7czo4OiJUw6Fyb2x2YSI7fXM6NDoiVVNFUiI7TzoxNToiQXBwXE1vZGVsc1xVc2VyIjozMjp7czoxMToiACoAZmlsbGFibGUiO2E6Njp7aTowO3M6NDoibmFtZSI7aToxO3M6NToiZW1haWwiO2k6MjtzOjg6InBhc3N3b3JkIjtpOjM7czo1OiJmYl9pZCI7aTo0O3M6OToiZ29vZ2xlX2lkIjtpOjU7czo5OiJnaXRodWJfaWQiO31zOjk6IgAqAGhpZGRlbiI7YTo0OntpOjA7czo4OiJwYXNzd29yZCI7aToxO3M6MTQ6InJlbWVtYmVyX3Rva2VuIjtpOjI7czoyNToidHdvX2ZhY3Rvcl9yZWNvdmVyeV9jb2RlcyI7aTozO3M6MTc6InR3b19mYWN0b3Jfc2VjcmV0Ijt9czo4OiIAKgBjYXN0cyI7YToxOntzOjE3OiJlbWFpbF92ZXJpZmllZF9hdCI7czo4OiJkYXRldGltZSI7fXM6MTA6IgAqAGFwcGVuZHMiO2E6MTp7aTowO3M6MTc6InByb2ZpbGVfcGhvdG9fdXJsIjt9czoxMzoiACoAY29ubmVjdGlvbiI7czo1OiJteXNxbCI7czo4OiIAKgB0YWJsZSI7czo1OiJ1c2VycyI7czoxMzoiACoAcHJpbWFyeUtleSI7czoyOiJpZCI7czoxMDoiACoAa2V5VHlwZSI7czozOiJpbnQiO3M6MTI6ImluY3JlbWVudGluZyI7YjoxO3M6NzoiACoAd2l0aCI7YTowOnt9czoxMjoiACoAd2l0aENvdW50IjthOjA6e31zOjE5OiJwcmV2ZW50c0xhenlMb2FkaW5nIjtiOjA7czoxMDoiACoAcGVyUGFnZSI7aToxNTtzOjY6ImV4aXN0cyI7YjoxO3M6MTg6Indhc1JlY2VudGx5Q3JlYXRlZCI7YjowO3M6Mjg6IgAqAGVzY2FwZVdoZW5DYXN0aW5nVG9TdHJpbmciO2I6MDtzOjEzOiIAKgBhdHRyaWJ1dGVzIjthOjE2OntzOjI6ImlkIjtpOjE7czo0OiJuYW1lIjtzOjk6InV0b3BzemtpaiI7czo1OiJlbWFpbCI7czoyMjoidGlib3IuZm9nbGVyQGdtYWlsLmNvbSI7czoxNzoiZW1haWxfdmVyaWZpZWRfYXQiO047czo4OiJwYXNzd29yZCI7czo2MDoiJDJ5JDEwJFpzZXluVmR6Z2xycEJyMVVJQktwTHVVRHJLelY0ZDVkSDAydEwzLzIwZVRkS0xYOHNSVFlPIjtzOjE3OiJ0d29fZmFjdG9yX3NlY3JldCI7TjtzOjI1OiJ0d29fZmFjdG9yX3JlY292ZXJ5X2NvZGVzIjtOO3M6MTQ6InJlbWVtYmVyX3Rva2VuIjtzOjYwOiJxQkVBbWlydGp5QzAwZTlTdXZySTFDNm95c01MYVRHSnByUnRPcVlPVzhkdVQ3dVVtOFQ3S2twS2lZSXAiO3M6MTU6ImN1cnJlbnRfdGVhbV9pZCI7aTowO3M6MTg6InByb2ZpbGVfcGhvdG9fcGF0aCI7czowOiIiO3M6MTA6ImNyZWF0ZWRfYXQiO3M6MTk6IjIwMjItMDItMDcgMTM6MDg6NDUiO3M6MTA6InVwZGF0ZWRfYXQiO3M6MTk6IjIwMjItMDItMDcgMTM6MDg6NDUiO3M6NToiZmJfaWQiO047czo5OiJnaXRodWJfaWQiO047czo5OiJnb29nbGVfaWQiO047czo2OiJhdmF0YXIiO3M6NjA6Imh0dHBzOi8vZ3JhdmF0YXIuY29tL2F2YXRhci8yYzBhMGU2ZTJkYzhiMzdmMjRkZGI0N2RmYjdlM2ViNSI7fXM6MTE6IgAqAG9yaWdpbmFsIjthOjE1OntzOjI6ImlkIjtpOjE7czo0OiJuYW1lIjtzOjk6InV0b3BzemtpaiI7czo1OiJlbWFpbCI7czoyMjoidGlib3IuZm9nbGVyQGdtYWlsLmNvbSI7czoxNzoiZW1haWxfdmVyaWZpZWRfYXQiO047czo4OiJwYXNzd29yZCI7czo2MDoiJDJ5JDEwJFpzZXluVmR6Z2xycEJyMVVJQktwTHVVRHJLelY0ZDVkSDAydEwzLzIwZVRkS0xYOHNSVFlPIjtzOjE3OiJ0d29fZmFjdG9yX3NlY3JldCI7TjtzOjI1OiJ0d29fZmFjdG9yX3JlY292ZXJ5X2NvZGVzIjtOO3M6MTQ6InJlbWVtYmVyX3Rva2VuIjtzOjYwOiJxQkVBbWlydGp5QzAwZTlTdXZySTFDNm95c01MYVRHSnByUnRPcVlPVzhkdVQ3dVVtOFQ3S2twS2lZSXAiO3M6MTU6ImN1cnJlbnRfdGVhbV9pZCI7aTowO3M6MTg6InByb2ZpbGVfcGhvdG9fcGF0aCI7czowOiIiO3M6MTA6ImNyZWF0ZWRfYXQiO3M6MTk6IjIwMjItMDItMDcgMTM6MDg6NDUiO3M6MTA6InVwZGF0ZWRfYXQiO3M6MTk6IjIwMjItMDItMDcgMTM6MDg6NDUiO3M6NToiZmJfaWQiO047czo5OiJnaXRodWJfaWQiO047czo5OiJnb29nbGVfaWQiO047fXM6MTA6IgAqAGNoYW5nZXMiO2E6MDp7fXM6MTc6IgAqAGNsYXNzQ2FzdENhY2hlIjthOjA6e31zOjIxOiIAKgBhdHRyaWJ1dGVDYXN0Q2FjaGUiO2E6MDp7fXM6ODoiACoAZGF0ZXMiO2E6MDp7fXM6MTM6IgAqAGRhdGVGb3JtYXQiO047czoxOToiACoAZGlzcGF0Y2hlc0V2ZW50cyI7YTowOnt9czoxNDoiACoAb2JzZXJ2YWJsZXMiO2E6MDp7fXM6MTI6IgAqAHJlbGF0aW9ucyI7YTowOnt9czoxMDoiACoAdG91Y2hlcyI7YTowOnt9czoxMDoidGltZXN0YW1wcyI7YjoxO3M6MTA6IgAqAHZpc2libGUiO2E6MDp7fXM6MTA6IgAqAGd1YXJkZWQiO2E6MTp7aTowO3M6MToiKiI7fXM6MjA6IgAqAHJlbWVtYmVyVG9rZW5OYW1lIjtzOjE0OiJyZW1lbWJlcl90b2tlbiI7czoxNDoiACoAYWNjZXNzVG9rZW4iO047fX19',1644593333),('E4uFKrHTjaKE3dni6ghcM30VXZRB2hi0usRY234X',2,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.87 Safari/537.36','YTo4OntzOjY6Il90b2tlbiI7czo0MDoiRU96S2pFVGhveUZJUTN6UmVUWVdmWXRsYXFtTXBSdUhPS2c2M0xhYyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9tZW1iZXIvbGlzdC9wcm9qZWN0cy8xIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MjtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2MDoiJDJ5JDEwJE5sYUhqcmFQL3p6SExGbEJPeElBOU9pMkl0N1dNa3NtZDYvekRhZFVZUkxERmI5MzBxbEtxIjtzOjEyOiJ0ZWFtSW5kZXhVcmwiO3M6Mzc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9wYXJlbnRzLzAvdGVhbXMiO3M6MTU6InByb2plY3RzTGlzdFVybCI7czozMjoiaHR0cDovL2xvY2FsaG9zdDo4MDAwLzYvcHJvamVjdHMiO3M6ODoidGFza0luZm8iO086ODoic3RkQ2xhc3MiOjY6e3M6MTE6IlJFUVVFU1RfVVJJIjtzOjIzOiIvbWVtYmVyL2xpc3QvcHJvamVjdHMvMSI7czoxNDoiUkVRVUVTVF9NRVRIT0QiO3M6MzoiR0VUIjtzOjE1OiJIVFRQX1VTRVJfQUdFTlQiO3M6MTA0OiJNb3ppbGxhLzUuMCAoWDExOyBMaW51eCB4ODZfNjQpIEFwcGxlV2ViS2l0LzUzNy4zNiAoS0hUTUwsIGxpa2UgR2Vja28pIENocm9tZS85OC4wLjQ3NTguODcgU2FmYXJpLzUzNy4zNiI7czo4OiJSRVFVRVNUUyI7YTowOnt9czo4OiJTRVNTSU9OUyI7YTo3OntzOjY6Il90b2tlbiI7czo0MDoiRU96S2pFVGhveUZJUTN6UmVUWVdmWXRsYXFtTXBSdUhPS2c2M0xhYyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzI6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9wcm9qZWN0cy8xIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MjtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2MDoiJDJ5JDEwJE5sYUhqcmFQL3p6SExGbEJPeElBOU9pMkl0N1dNa3NtZDYvekRhZFVZUkxERmI5MzBxbEtxIjtzOjEyOiJ0ZWFtSW5kZXhVcmwiO3M6Mzc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9wYXJlbnRzLzAvdGVhbXMiO3M6MTU6InByb2plY3RzTGlzdFVybCI7czozMjoiaHR0cDovL2xvY2FsaG9zdDo4MDAwLzYvcHJvamVjdHMiO31zOjQ6IlVTRVIiO086MTU6IkFwcFxNb2RlbHNcVXNlciI6MzI6e3M6MTE6IgAqAGZpbGxhYmxlIjthOjY6e2k6MDtzOjQ6Im5hbWUiO2k6MTtzOjU6ImVtYWlsIjtpOjI7czo4OiJwYXNzd29yZCI7aTozO3M6NToiZmJfaWQiO2k6NDtzOjk6Imdvb2dsZV9pZCI7aTo1O3M6OToiZ2l0aHViX2lkIjt9czo5OiIAKgBoaWRkZW4iO2E6NDp7aTowO3M6ODoicGFzc3dvcmQiO2k6MTtzOjE0OiJyZW1lbWJlcl90b2tlbiI7aToyO3M6MjU6InR3b19mYWN0b3JfcmVjb3ZlcnlfY29kZXMiO2k6MztzOjE3OiJ0d29fZmFjdG9yX3NlY3JldCI7fXM6ODoiACoAY2FzdHMiO2E6MTp7czoxNzoiZW1haWxfdmVyaWZpZWRfYXQiO3M6ODoiZGF0ZXRpbWUiO31zOjEwOiIAKgBhcHBlbmRzIjthOjE6e2k6MDtzOjE3OiJwcm9maWxlX3Bob3RvX3VybCI7fXM6MTM6IgAqAGNvbm5lY3Rpb24iO3M6NToibXlzcWwiO3M6ODoiACoAdGFibGUiO3M6NToidXNlcnMiO3M6MTM6IgAqAHByaW1hcnlLZXkiO3M6MjoiaWQiO3M6MTA6IgAqAGtleVR5cGUiO3M6MzoiaW50IjtzOjEyOiJpbmNyZW1lbnRpbmciO2I6MTtzOjc6IgAqAHdpdGgiO2E6MDp7fXM6MTI6IgAqAHdpdGhDb3VudCI7YTowOnt9czoxOToicHJldmVudHNMYXp5TG9hZGluZyI7YjowO3M6MTA6IgAqAHBlclBhZ2UiO2k6MTU7czo2OiJleGlzdHMiO2I6MTtzOjE4OiJ3YXNSZWNlbnRseUNyZWF0ZWQiO2I6MDtzOjI4OiIAKgBlc2NhcGVXaGVuQ2FzdGluZ1RvU3RyaW5nIjtiOjA7czoxMzoiACoAYXR0cmlidXRlcyI7YToxNjp7czoyOiJpZCI7aToyO3M6NDoibmFtZSI7czo2OiJ0ZXN6dDEiO3M6NToiZW1haWwiO3M6MTU6InRlc3p0MUB0ZXN6dC5odSI7czoxNzoiZW1haWxfdmVyaWZpZWRfYXQiO047czo4OiJwYXNzd29yZCI7czo2MDoiJDJ5JDEwJE5sYUhqcmFQL3p6SExGbEJPeElBOU9pMkl0N1dNa3NtZDYvekRhZFVZUkxERmI5MzBxbEtxIjtzOjE3OiJ0d29fZmFjdG9yX3NlY3JldCI7TjtzOjI1OiJ0d29fZmFjdG9yX3JlY292ZXJ5X2NvZGVzIjtOO3M6MTQ6InJlbWVtYmVyX3Rva2VuIjtzOjYwOiJnN2duNm1zemRwaHFIa01OdmNpQ0tFak92d0IxenllcGpwV3BZVTlkdDIxTFc4Z0pwSTJCTHBLanRzT3IiO3M6MTU6ImN1cnJlbnRfdGVhbV9pZCI7TjtzOjE4OiJwcm9maWxlX3Bob3RvX3BhdGgiO3M6MDoiIjtzOjEwOiJjcmVhdGVkX2F0IjtzOjE5OiIyMDIyLTAyLTA4IDEwOjQzOjI2IjtzOjEwOiJ1cGRhdGVkX2F0IjtzOjE5OiIyMDIyLTAyLTA4IDEwOjQzOjI2IjtzOjU6ImZiX2lkIjtOO3M6OToiZ2l0aHViX2lkIjtOO3M6OToiZ29vZ2xlX2lkIjtOO3M6NjoiYXZhdGFyIjtzOjY1OiJodHRwczovL3VpLWF2YXRhcnMuY29tL2FwaS8/bmFtZT10JmNvbG9yPTdGOUNGNSZiYWNrZ3JvdW5kPUVCRjRGRiI7fXM6MTE6IgAqAG9yaWdpbmFsIjthOjE1OntzOjI6ImlkIjtpOjI7czo0OiJuYW1lIjtzOjY6InRlc3p0MSI7czo1OiJlbWFpbCI7czoxNToidGVzenQxQHRlc3p0Lmh1IjtzOjE3OiJlbWFpbF92ZXJpZmllZF9hdCI7TjtzOjg6InBhc3N3b3JkIjtzOjYwOiIkMnkkMTAkTmxhSGpyYVAvenpITEZsQk94SUE5T2kySXQ3V01rc21kNi96RGFkVVlSTERGYjkzMHFsS3EiO3M6MTc6InR3b19mYWN0b3Jfc2VjcmV0IjtOO3M6MjU6InR3b19mYWN0b3JfcmVjb3ZlcnlfY29kZXMiO047czoxNDoicmVtZW1iZXJfdG9rZW4iO3M6NjA6Imc3Z242bXN6ZHBocUhrTU52Y2lDS0VqT3Z3QjF6eWVwanBXcFlVOWR0MjFMVzhnSnBJMkJMcEtqdHNPciI7czoxNToiY3VycmVudF90ZWFtX2lkIjtOO3M6MTg6InByb2ZpbGVfcGhvdG9fcGF0aCI7czowOiIiO3M6MTA6ImNyZWF0ZWRfYXQiO3M6MTk6IjIwMjItMDItMDggMTA6NDM6MjYiO3M6MTA6InVwZGF0ZWRfYXQiO3M6MTk6IjIwMjItMDItMDggMTA6NDM6MjYiO3M6NToiZmJfaWQiO047czo5OiJnaXRodWJfaWQiO047czo5OiJnb29nbGVfaWQiO047fXM6MTA6IgAqAGNoYW5nZXMiO2E6MDp7fXM6MTc6IgAqAGNsYXNzQ2FzdENhY2hlIjthOjA6e31zOjIxOiIAKgBhdHRyaWJ1dGVDYXN0Q2FjaGUiO2E6MDp7fXM6ODoiACoAZGF0ZXMiO2E6MDp7fXM6MTM6IgAqAGRhdGVGb3JtYXQiO047czoxOToiACoAZGlzcGF0Y2hlc0V2ZW50cyI7YTowOnt9czoxNDoiACoAb2JzZXJ2YWJsZXMiO2E6MDp7fXM6MTI6IgAqAHJlbGF0aW9ucyI7YTowOnt9czoxMDoiACoAdG91Y2hlcyI7YTowOnt9czoxMDoidGltZXN0YW1wcyI7YjoxO3M6MTA6IgAqAHZpc2libGUiO2E6MDp7fXM6MTA6IgAqAGd1YXJkZWQiO2E6MTp7aTowO3M6MToiKiI7fXM6MjA6IgAqAHJlbWVtYmVyVG9rZW5OYW1lIjtzOjE0OiJyZW1lbWJlcl90b2tlbiI7czoxNDoiACoAYWNjZXNzVG9rZW4iO047fX19',1644396373),('kIXXRi6QTTfLUgdZbIk6iUUyA8ZOizf1qF1VPBwY',1,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.87 Safari/537.36','YTo4OntzOjY6Il90b2tlbiI7czo0MDoicXhMSDVsUExhRXdBMVhKSDdqU1dYczhqODg0QjdaSVNPQ1k1T0NYWSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC90ZWFtcy82L2VkaXQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjEyOiJ0ZWFtSW5kZXhVcmwiO3M6Mzc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9wYXJlbnRzLzAvdGVhbXMiO3M6MTU6InByb2plY3RzTGlzdFVybCI7czozMjoiaHR0cDovL2xvY2FsaG9zdDo4MDAwLzYvcHJvamVjdHMiO3M6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjA6IiQyeSQxMCRac2V5blZkemdscnBCcjFVSUJLcEx1VURyS3pWNGQ1ZEgwMnRMMy8yMGVUZEtMWDhzUlRZTyI7czo4OiJ0YXNrSW5mbyI7Tzo4OiJzdGRDbGFzcyI6Njp7czoxMToiUkVRVUVTVF9VUkkiO3M6MTM6Ii90ZWFtcy82L2VkaXQiO3M6MTQ6IlJFUVVFU1RfTUVUSE9EIjtzOjM6IkdFVCI7czoxNToiSFRUUF9VU0VSX0FHRU5UIjtzOjEwNDoiTW96aWxsYS81LjAgKFgxMTsgTGludXggeDg2XzY0KSBBcHBsZVdlYktpdC81MzcuMzYgKEtIVE1MLCBsaWtlIEdlY2tvKSBDaHJvbWUvOTguMC40NzU4Ljg3IFNhZmFyaS81MzcuMzYiO3M6ODoiUkVRVUVTVFMiO2E6MDp7fXM6ODoiU0VTU0lPTlMiO2E6Nzp7czo2OiJfdG9rZW4iO3M6NDA6InF4TEg1bFBMYUV3QTFYSkg3alNXWHM4ajg4NEI3WklTT0NZNU9DWFkiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjI5OiJodHRwOi8vbG9jYWxob3N0OjgwMDAvdGVhbXMvNiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MTI6InRlYW1JbmRleFVybCI7czozNzoiaHR0cDovL2xvY2FsaG9zdDo4MDAwL3BhcmVudHMvMC90ZWFtcyI7czoxNToicHJvamVjdHNMaXN0VXJsIjtzOjMyOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvNi9wcm9qZWN0cyI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2MDoiJDJ5JDEwJFpzZXluVmR6Z2xycEJyMVVJQktwTHVVRHJLelY0ZDVkSDAydEwzLzIwZVRkS0xYOHNSVFlPIjt9czo0OiJVU0VSIjtPOjE1OiJBcHBcTW9kZWxzXFVzZXIiOjMyOntzOjExOiIAKgBmaWxsYWJsZSI7YTo2OntpOjA7czo0OiJuYW1lIjtpOjE7czo1OiJlbWFpbCI7aToyO3M6ODoicGFzc3dvcmQiO2k6MztzOjU6ImZiX2lkIjtpOjQ7czo5OiJnb29nbGVfaWQiO2k6NTtzOjk6ImdpdGh1Yl9pZCI7fXM6OToiACoAaGlkZGVuIjthOjQ6e2k6MDtzOjg6InBhc3N3b3JkIjtpOjE7czoxNDoicmVtZW1iZXJfdG9rZW4iO2k6MjtzOjI1OiJ0d29fZmFjdG9yX3JlY292ZXJ5X2NvZGVzIjtpOjM7czoxNzoidHdvX2ZhY3Rvcl9zZWNyZXQiO31zOjg6IgAqAGNhc3RzIjthOjE6e3M6MTc6ImVtYWlsX3ZlcmlmaWVkX2F0IjtzOjg6ImRhdGV0aW1lIjt9czoxMDoiACoAYXBwZW5kcyI7YToxOntpOjA7czoxNzoicHJvZmlsZV9waG90b191cmwiO31zOjEzOiIAKgBjb25uZWN0aW9uIjtzOjU6Im15c3FsIjtzOjg6IgAqAHRhYmxlIjtzOjU6InVzZXJzIjtzOjEzOiIAKgBwcmltYXJ5S2V5IjtzOjI6ImlkIjtzOjEwOiIAKgBrZXlUeXBlIjtzOjM6ImludCI7czoxMjoiaW5jcmVtZW50aW5nIjtiOjE7czo3OiIAKgB3aXRoIjthOjA6e31zOjEyOiIAKgB3aXRoQ291bnQiO2E6MDp7fXM6MTk6InByZXZlbnRzTGF6eUxvYWRpbmciO2I6MDtzOjEwOiIAKgBwZXJQYWdlIjtpOjE1O3M6NjoiZXhpc3RzIjtiOjE7czoxODoid2FzUmVjZW50bHlDcmVhdGVkIjtiOjA7czoyODoiACoAZXNjYXBlV2hlbkNhc3RpbmdUb1N0cmluZyI7YjowO3M6MTM6IgAqAGF0dHJpYnV0ZXMiO2E6MTY6e3M6MjoiaWQiO2k6MTtzOjQ6Im5hbWUiO3M6OToidXRvcHN6a2lqIjtzOjU6ImVtYWlsIjtzOjIyOiJ0aWJvci5mb2dsZXJAZ21haWwuY29tIjtzOjE3OiJlbWFpbF92ZXJpZmllZF9hdCI7TjtzOjg6InBhc3N3b3JkIjtzOjYwOiIkMnkkMTAkWnNleW5WZHpnbHJwQnIxVUlCS3BMdVVEckt6VjRkNWRIMDJ0TDMvMjBlVGRLTFg4c1JUWU8iO3M6MTc6InR3b19mYWN0b3Jfc2VjcmV0IjtOO3M6MjU6InR3b19mYWN0b3JfcmVjb3ZlcnlfY29kZXMiO047czoxNDoicmVtZW1iZXJfdG9rZW4iO3M6NjA6InFCRUFtaXJ0anlDMDBlOVN1dnJJMUM2b3lzTUxhVEdKcHJSdE9xWU9XOGR1VDd1VW04VDdLa3BLaVlJcCI7czoxNToiY3VycmVudF90ZWFtX2lkIjtpOjA7czoxODoicHJvZmlsZV9waG90b19wYXRoIjtzOjA6IiI7czoxMDoiY3JlYXRlZF9hdCI7czoxOToiMjAyMi0wMi0wNyAxMzowODo0NSI7czoxMDoidXBkYXRlZF9hdCI7czoxOToiMjAyMi0wMi0wNyAxMzowODo0NSI7czo1OiJmYl9pZCI7TjtzOjk6ImdpdGh1Yl9pZCI7TjtzOjk6Imdvb2dsZV9pZCI7TjtzOjY6ImF2YXRhciI7czo2MDoiaHR0cHM6Ly9ncmF2YXRhci5jb20vYXZhdGFyLzJjMGEwZTZlMmRjOGIzN2YyNGRkYjQ3ZGZiN2UzZWI1Ijt9czoxMToiACoAb3JpZ2luYWwiO2E6MTU6e3M6MjoiaWQiO2k6MTtzOjQ6Im5hbWUiO3M6OToidXRvcHN6a2lqIjtzOjU6ImVtYWlsIjtzOjIyOiJ0aWJvci5mb2dsZXJAZ21haWwuY29tIjtzOjE3OiJlbWFpbF92ZXJpZmllZF9hdCI7TjtzOjg6InBhc3N3b3JkIjtzOjYwOiIkMnkkMTAkWnNleW5WZHpnbHJwQnIxVUlCS3BMdVVEckt6VjRkNWRIMDJ0TDMvMjBlVGRLTFg4c1JUWU8iO3M6MTc6InR3b19mYWN0b3Jfc2VjcmV0IjtOO3M6MjU6InR3b19mYWN0b3JfcmVjb3ZlcnlfY29kZXMiO047czoxNDoicmVtZW1iZXJfdG9rZW4iO3M6NjA6InFCRUFtaXJ0anlDMDBlOVN1dnJJMUM2b3lzTUxhVEdKcHJSdE9xWU9XOGR1VDd1VW04VDdLa3BLaVlJcCI7czoxNToiY3VycmVudF90ZWFtX2lkIjtpOjA7czoxODoicHJvZmlsZV9waG90b19wYXRoIjtzOjA6IiI7czoxMDoiY3JlYXRlZF9hdCI7czoxOToiMjAyMi0wMi0wNyAxMzowODo0NSI7czoxMDoidXBkYXRlZF9hdCI7czoxOToiMjAyMi0wMi0wNyAxMzowODo0NSI7czo1OiJmYl9pZCI7TjtzOjk6ImdpdGh1Yl9pZCI7TjtzOjk6Imdvb2dsZV9pZCI7Tjt9czoxMDoiACoAY2hhbmdlcyI7YTowOnt9czoxNzoiACoAY2xhc3NDYXN0Q2FjaGUiO2E6MDp7fXM6MjE6IgAqAGF0dHJpYnV0ZUNhc3RDYWNoZSI7YTowOnt9czo4OiIAKgBkYXRlcyI7YTowOnt9czoxMzoiACoAZGF0ZUZvcm1hdCI7TjtzOjE5OiIAKgBkaXNwYXRjaGVzRXZlbnRzIjthOjA6e31zOjE0OiIAKgBvYnNlcnZhYmxlcyI7YTowOnt9czoxMjoiACoAcmVsYXRpb25zIjthOjA6e31zOjEwOiIAKgB0b3VjaGVzIjthOjA6e31zOjEwOiJ0aW1lc3RhbXBzIjtiOjE7czoxMDoiACoAdmlzaWJsZSI7YTowOnt9czoxMDoiACoAZ3VhcmRlZCI7YToxOntpOjA7czoxOiIqIjt9czoyMDoiACoAcmVtZW1iZXJUb2tlbk5hbWUiO3M6MTQ6InJlbWVtYmVyX3Rva2VuIjtzOjE0OiIAKgBhY2Nlc3NUb2tlbiI7Tjt9fX0=',1644478325),('uq6pBpxcnTJesZWTB1XO3vMfvjwfP8hq8ccp6qMg',1,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.87 Safari/537.36','YToxMzp7czo2OiJfdG9rZW4iO3M6NDA6IlR1aGNjTnRMamM2eXF2TTd5blBZV0s0M2l4UE1LUTJMdWNxeUY1TlAiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjQzOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvcHJvZHVjdHMvbGlzdGJ5dXNlci8xIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czoxMjoidGVhbUluZGV4VXJsIjtzOjM3OiJodHRwOi8vbG9jYWxob3N0OjgwMDAvcGFyZW50cy8wL3RlYW1zIjtzOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjYwOiIkMnkkMTAkWnNleW5WZHpnbHJwQnIxVUlCS3BMdVVEckt6VjRkNWRIMDJ0TDMvMjBlVGRLTFg4c1JUWU8iO3M6MTM6InByb2R1Y3RzT3JkZXIiO3M6ODoibmFtZSxhc2MiO3M6MTQ6InByb2R1Y3RzU2VhcmNoIjtzOjA6IiI7czoxODoicHJvZHVjdHNDYXRlZ29yaWVzIjtzOjA6IiI7czoxNDoicHJvZHVjdHNUZWFtSWQiO2k6MDtzOjE0OiJwcm9kdWN0c1VzZXJJZCI7czoxOiIxIjtzOjE1OiJwcm9kdWN0c0xpc3RVcmwiO3M6NDM6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9wcm9kdWN0cy9saXN0Ynl1c2VyLzEiO3M6ODoidGFza0luZm8iO086ODoic3RkQ2xhc3MiOjY6e3M6MTE6IlJFUVVFU1RfVVJJIjtzOjIyOiIvcHJvZHVjdHMvbGlzdGJ5dXNlci8xIjtzOjE0OiJSRVFVRVNUX01FVEhPRCI7czozOiJHRVQiO3M6MTU6IkhUVFBfVVNFUl9BR0VOVCI7czoxMDQ6Ik1vemlsbGEvNS4wIChYMTE7IExpbnV4IHg4Nl82NCkgQXBwbGVXZWJLaXQvNTM3LjM2IChLSFRNTCwgbGlrZSBHZWNrbykgQ2hyb21lLzk4LjAuNDc1OC44NyBTYWZhcmkvNTM3LjM2IjtzOjg6IlJFUVVFU1RTIjthOjA6e31zOjg6IlNFU1NJT05TIjthOjEzOntzOjY6Il90b2tlbiI7czo0MDoiVHVoY2NOdExqYzZ5cXZNN3luUFlXSzQzaXhQTUtRMkx1Y3F5RjVOUCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzk6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9wcm9kdWN0cy9jcmVhdGUvMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjE6e2k6MDtzOjc6InN1Y2Nlc3MiO31zOjM6Im5ldyI7YTowOnt9fXM6MTI6InRlYW1JbmRleFVybCI7czozNzoiaHR0cDovL2xvY2FsaG9zdDo4MDAwL3BhcmVudHMvMC90ZWFtcyI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2MDoiJDJ5JDEwJFpzZXluVmR6Z2xycEJyMVVJQktwTHVVRHJLelY0ZDVkSDAydEwzLzIwZVRkS0xYOHNSVFlPIjtzOjEzOiJwcm9kdWN0c09yZGVyIjtzOjg6Im5hbWUsYXNjIjtzOjE0OiJwcm9kdWN0c1NlYXJjaCI7czowOiIiO3M6MTg6InByb2R1Y3RzQ2F0ZWdvcmllcyI7czowOiIiO3M6MTQ6InByb2R1Y3RzVGVhbUlkIjtpOjA7czoxNDoicHJvZHVjdHNVc2VySWQiO3M6MToiMSI7czoxNToicHJvZHVjdHNMaXN0VXJsIjtzOjQzOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvcHJvZHVjdHMvbGlzdGJ5dXNlci8xIjtzOjc6InN1Y2Nlc3MiO3M6MzQ6IlRlcm3DqWsgYWRhdG9rIHNpa2VyZXNlbiB0w6Fyb2x2YS4iO31zOjQ6IlVTRVIiO086MTU6IkFwcFxNb2RlbHNcVXNlciI6MzI6e3M6MTE6IgAqAGZpbGxhYmxlIjthOjY6e2k6MDtzOjQ6Im5hbWUiO2k6MTtzOjU6ImVtYWlsIjtpOjI7czo4OiJwYXNzd29yZCI7aTozO3M6NToiZmJfaWQiO2k6NDtzOjk6Imdvb2dsZV9pZCI7aTo1O3M6OToiZ2l0aHViX2lkIjt9czo5OiIAKgBoaWRkZW4iO2E6NDp7aTowO3M6ODoicGFzc3dvcmQiO2k6MTtzOjE0OiJyZW1lbWJlcl90b2tlbiI7aToyO3M6MjU6InR3b19mYWN0b3JfcmVjb3ZlcnlfY29kZXMiO2k6MztzOjE3OiJ0d29fZmFjdG9yX3NlY3JldCI7fXM6ODoiACoAY2FzdHMiO2E6MTp7czoxNzoiZW1haWxfdmVyaWZpZWRfYXQiO3M6ODoiZGF0ZXRpbWUiO31zOjEwOiIAKgBhcHBlbmRzIjthOjE6e2k6MDtzOjE3OiJwcm9maWxlX3Bob3RvX3VybCI7fXM6MTM6IgAqAGNvbm5lY3Rpb24iO3M6NToibXlzcWwiO3M6ODoiACoAdGFibGUiO3M6NToidXNlcnMiO3M6MTM6IgAqAHByaW1hcnlLZXkiO3M6MjoiaWQiO3M6MTA6IgAqAGtleVR5cGUiO3M6MzoiaW50IjtzOjEyOiJpbmNyZW1lbnRpbmciO2I6MTtzOjc6IgAqAHdpdGgiO2E6MDp7fXM6MTI6IgAqAHdpdGhDb3VudCI7YTowOnt9czoxOToicHJldmVudHNMYXp5TG9hZGluZyI7YjowO3M6MTA6IgAqAHBlclBhZ2UiO2k6MTU7czo2OiJleGlzdHMiO2I6MTtzOjE4OiJ3YXNSZWNlbnRseUNyZWF0ZWQiO2I6MDtzOjI4OiIAKgBlc2NhcGVXaGVuQ2FzdGluZ1RvU3RyaW5nIjtiOjA7czoxMzoiACoAYXR0cmlidXRlcyI7YToxNjp7czoyOiJpZCI7aToxO3M6NDoibmFtZSI7czo5OiJ1dG9wc3praWoiO3M6NToiZW1haWwiO3M6MjI6InRpYm9yLmZvZ2xlckBnbWFpbC5jb20iO3M6MTc6ImVtYWlsX3ZlcmlmaWVkX2F0IjtOO3M6ODoicGFzc3dvcmQiO3M6NjA6IiQyeSQxMCRac2V5blZkemdscnBCcjFVSUJLcEx1VURyS3pWNGQ1ZEgwMnRMMy8yMGVUZEtMWDhzUlRZTyI7czoxNzoidHdvX2ZhY3Rvcl9zZWNyZXQiO047czoyNToidHdvX2ZhY3Rvcl9yZWNvdmVyeV9jb2RlcyI7TjtzOjE0OiJyZW1lbWJlcl90b2tlbiI7czo2MDoicUJFQW1pcnRqeUMwMGU5U3V2ckkxQzZveXNNTGFUR0pwclJ0T3FZT1c4ZHVUN3VVbThUN0trcEtpWUlwIjtzOjE1OiJjdXJyZW50X3RlYW1faWQiO2k6MDtzOjE4OiJwcm9maWxlX3Bob3RvX3BhdGgiO3M6MDoiIjtzOjEwOiJjcmVhdGVkX2F0IjtzOjE5OiIyMDIyLTAyLTA3IDEzOjA4OjQ1IjtzOjEwOiJ1cGRhdGVkX2F0IjtzOjE5OiIyMDIyLTAyLTA3IDEzOjA4OjQ1IjtzOjU6ImZiX2lkIjtOO3M6OToiZ2l0aHViX2lkIjtOO3M6OToiZ29vZ2xlX2lkIjtOO3M6NjoiYXZhdGFyIjtzOjYwOiJodHRwczovL2dyYXZhdGFyLmNvbS9hdmF0YXIvMmMwYTBlNmUyZGM4YjM3ZjI0ZGRiNDdkZmI3ZTNlYjUiO31zOjExOiIAKgBvcmlnaW5hbCI7YToxNTp7czoyOiJpZCI7aToxO3M6NDoibmFtZSI7czo5OiJ1dG9wc3praWoiO3M6NToiZW1haWwiO3M6MjI6InRpYm9yLmZvZ2xlckBnbWFpbC5jb20iO3M6MTc6ImVtYWlsX3ZlcmlmaWVkX2F0IjtOO3M6ODoicGFzc3dvcmQiO3M6NjA6IiQyeSQxMCRac2V5blZkemdscnBCcjFVSUJLcEx1VURyS3pWNGQ1ZEgwMnRMMy8yMGVUZEtMWDhzUlRZTyI7czoxNzoidHdvX2ZhY3Rvcl9zZWNyZXQiO047czoyNToidHdvX2ZhY3Rvcl9yZWNvdmVyeV9jb2RlcyI7TjtzOjE0OiJyZW1lbWJlcl90b2tlbiI7czo2MDoicUJFQW1pcnRqeUMwMGU5U3V2ckkxQzZveXNNTGFUR0pwclJ0T3FZT1c4ZHVUN3VVbThUN0trcEtpWUlwIjtzOjE1OiJjdXJyZW50X3RlYW1faWQiO2k6MDtzOjE4OiJwcm9maWxlX3Bob3RvX3BhdGgiO3M6MDoiIjtzOjEwOiJjcmVhdGVkX2F0IjtzOjE5OiIyMDIyLTAyLTA3IDEzOjA4OjQ1IjtzOjEwOiJ1cGRhdGVkX2F0IjtzOjE5OiIyMDIyLTAyLTA3IDEzOjA4OjQ1IjtzOjU6ImZiX2lkIjtOO3M6OToiZ2l0aHViX2lkIjtOO3M6OToiZ29vZ2xlX2lkIjtOO31zOjEwOiIAKgBjaGFuZ2VzIjthOjA6e31zOjE3OiIAKgBjbGFzc0Nhc3RDYWNoZSI7YTowOnt9czoyMToiACoAYXR0cmlidXRlQ2FzdENhY2hlIjthOjA6e31zOjg6IgAqAGRhdGVzIjthOjA6e31zOjEzOiIAKgBkYXRlRm9ybWF0IjtOO3M6MTk6IgAqAGRpc3BhdGNoZXNFdmVudHMiO2E6MDp7fXM6MTQ6IgAqAG9ic2VydmFibGVzIjthOjA6e31zOjEyOiIAKgByZWxhdGlvbnMiO2E6MDp7fXM6MTA6IgAqAHRvdWNoZXMiO2E6MDp7fXM6MTA6InRpbWVzdGFtcHMiO2I6MTtzOjEwOiIAKgB2aXNpYmxlIjthOjA6e31zOjEwOiIAKgBndWFyZGVkIjthOjE6e2k6MDtzOjE6IioiO31zOjIwOiIAKgByZW1lbWJlclRva2VuTmFtZSI7czoxNDoicmVtZW1iZXJfdG9rZW4iO3M6MTQ6IgAqAGFjY2Vzc1Rva2VuIjtOO319fQ==',1644563491);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tasks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'egyedi rekord ID',
  `project_id` bigint unsigned NOT NULL COMMENT 'projekt ID',
  `name` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'rövid leírás',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'waiting|canwork|working|cancontroll|incontrol|closed',
  `position` int DEFAULT NULL COMMENT 'prioritás (kics a sürgősebb)',
  `deadline` date DEFAULT NULL COMMENT 'határidő',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'bug|task|info|question',
  `assign` bigint unsigned DEFAULT NULL COMMENT 'felelős user',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'rekord létrehozás időpontja',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'utolsó modositás időpontja',
  PRIMARY KEY (`id`),
  KEY `project_id_foreign` (`project_id`),
  CONSTRAINT `project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tasks`
--

LOCK TABLES `tasks` WRITE;
/*!40000 ALTER TABLE `tasks` DISABLE KEYS */;
/*!40000 ALTER TABLE `tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teams`
--

DROP TABLE IF EXISTS `teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teams` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'egyedi rekord ID',
  `parent` bigint unsigned DEFAULT NULL COMMENT 'csoport fa szerkezetet alakitja ki',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'csoport neve',
  `description` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'csoport leírása',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'avatar kép url',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'proposal|active|closed',
  `config` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'json string',
  `activated_at` date DEFAULT NULL COMMENT 'csoport megynitás dátuma',
  `closed_at` date DEFAULT NULL COMMENT 'csoport lezárás dátuma',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'rekord létrehozás időpontja',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'rekord utolsó modisítás időpontja',
  `created_by` bigint unsigned NOT NULL COMMENT 'létrehozó user',
  PRIMARY KEY (`id`),
  KEY `teams_created_by_foreign` (`created_by`),
  CONSTRAINT `teams_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=170 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teams`
--

LOCK TABLES `teams` WRITE;
/*!40000 ALTER TABLE `teams` DISABLE KEYS */;
INSERT INTO `teams` VALUES (1,0,'Regisztrált felhasználók','minden regisztrált felhasználó tagja ennek a csoportnak','/img/team.png','active','{\"ranks\":[\"admin\",\"president\",\"manager\",\"moderator\"],\"close\":98,\"memberActivate\":2,\"memberExclude\":95,\"rankActivate\":40,\"rankClose\":95,\"projectActivate\":2,\"productActivate\":50,\"subTeamActivate\":2,\"debateActivate\":2}',NULL,NULL,'2022-02-07 12:08:53','2022-02-07 12:08:53',1),(2,0,'System admins','rendszer adminisztrátorok','/img/team.png','active','{\"ranks\":[\"admin\",\"president\",\"manager\",\"moderator\"],\"close\":98,\"memberActivate\":2,\"memberExclude\":95,\"rankActivate\":40,\"rankClose\":95,\"projectActivate\":2,\"productActivate\":50,\"subTeamActivate\":2,\"debateActivate\":2}',NULL,NULL,'2022-02-07 12:08:53','2022-02-07 12:08:53',1),(6,0,'- Homokozó','Ez a csoport kisérletezésre, tanulásra, a rendszerrel történő ismerkedésre való.','/img/homokozo.png','active','{\"ranks\":[\"admin\",\"president\",\"manager\",\"moderator\",\"accredited\"],\"close\":\"100\",\"memberActivate\":\"0\",\"memberExclude\":\"100\",\"rankActivate\":\"40\",\"rankClose\":\"95\",\"projectActivate\":\"1\",\"productActivate\":\"1\",\"subTeamActivate\":\"2\",\"debateActivate\":\"2\"}',NULL,NULL,'2021-12-04 10:58:34','2022-02-07 13:23:40',1),(16,0,'Társadalom tdományok, ideológia, politika','Társadalom tudományok, társadalmi szervezetek, politikai szervezetek, társadalmi kérdésekkel foglalkozó civil szervezetek','/img/tarsadalom.jpg','active','{\"ranks\":[\"admin\",\"president\",\"manager\",\"moderator\"],\"close\":\"98\",\"memberActivate\":\"2\",\"memberExclude\":\"95\",\"rankActivate\":\"40\",\"rankClose\":\"95\",\"projectActivate\":\"2\",\"productActivate\":\"50\",\"subTeamActivate\":\"2\",\"debateActivate\":\"2\"}',NULL,NULL,'2021-12-06 09:58:54','2021-12-18 11:39:34',1),(106,0,'Természet tudományok, technika','természettudományok, technika vita csoportok, teamek','/img/fold.png','active','{\"ranks\":[\"admin\",\"president\",\"manager\",\"moderator\"],\"close\":\"98\",\"memberActivate\":\"2\",\"memberExclude\":\"95\",\"rankActivate\":\"40\",\"rankClose\":\"95\",\"projectActivate\":\"2\",\"productActivate\":\"50\",\"subTeamActivate\":\"2\",\"debateActivate\":\"2\"}',NULL,NULL,'2021-12-18 04:31:03','2021-12-18 11:51:32',1),(107,0,'Termelők','Anyagi és szellemi termék előállító csoportok','/img/termelo.png','active','{\"ranks\":[\"admin\",\"president\",\"manager\",\"moderator\"],\"close\":\"98\",\"memberActivate\":\"2\",\"memberExclude\":\"95\",\"rankActivate\":\"40\",\"rankClose\":\"95\",\"projectActivate\":\"2\",\"productActivate\":\"50\",\"subTeamActivate\":\"2\",\"debateActivate\":\"2\"}',NULL,NULL,'2021-12-18 04:31:44','2021-12-18 11:43:00',1),(108,0,'Kultúra, oktatás','Kultúrális kérdésekkel, oktatással foglalkozó csoportok','/img/kultura.jpg','active','{\"ranks\":[\"admin\",\"president\",\"manager\",\"moderator\"],\"close\":\"98\",\"memberActivate\":\"2\",\"memberExclude\":\"95\",\"rankActivate\":\"40\",\"rankClose\":\"95\",\"projectActivate\":\"2\",\"productActivate\":\"50\",\"subTeamActivate\":\"2\",\"debateActivate\":\"2\"}',NULL,NULL,'2021-12-18 04:32:40','2021-12-18 11:38:30',1),(109,0,'Egészségügy','Testi és szellemi egészséggel, gyógyítással, megelőzéssel, egészséges táplálkozással foglalkozó csoportok','/img/egeszsegugy.jpg','active','{\"ranks\":[\"admin\",\"president\",\"manager\",\"moderator\"],\"close\":\"98\",\"memberActivate\":\"2\",\"memberExclude\":\"95\",\"rankActivate\":\"40\",\"rankClose\":\"95\",\"projectActivate\":\"2\",\"productActivate\":\"50\",\"subTeamActivate\":\"2\",\"debateActivate\":\"2\"}',NULL,NULL,'2021-12-18 04:33:46','2021-12-18 11:37:56',1),(110,0,'Sport','verseny és tömegsporttal foglalkozó csoportok, sport klubbok','/img/sport.png','active','{\"ranks\":[\"admin\",\"president\",\"manager\",\"moderator\"],\"close\":\"98\",\"memberActivate\":\"2\",\"memberExclude\":\"95\",\"rankActivate\":\"40\",\"rankClose\":\"95\",\"projectActivate\":\"2\",\"productActivate\":\"50\",\"subTeamActivate\":\"2\",\"debateActivate\":\"2\"}',NULL,NULL,'2021-12-18 04:34:26','2021-12-18 11:44:41',1),(111,0,'Szórakozás','szabadidő, pop kultúra, TV, film stb','/img/hobby.png','active','{\"ranks\":[\"admin\",\"president\",\"manager\",\"moderator\"],\"close\":\"98\",\"memberActivate\":\"2\",\"memberExclude\":\"95\",\"rankActivate\":\"40\",\"rankClose\":\"95\",\"projectActivate\":\"2\",\"productActivate\":\"50\",\"subTeamActivate\":\"2\",\"debateActivate\":\"2\"}',NULL,NULL,'2021-12-18 04:36:27','2021-12-18 11:49:51',1),(112,0,'Család','Családi élet, gyermek nevelés, emberi kapcsolatok','/img/csalad.png','active','{\"ranks\":[\"admin\",\"president\",\"manager\",\"moderator\"],\"close\":\"98\",\"memberActivate\":\"2\",\"memberExclude\":\"95\",\"rankActivate\":\"40\",\"rankClose\":\"95\",\"projectActivate\":\"2\",\"productActivate\":\"50\",\"subTeamActivate\":\"2\",\"debateActivate\":\"2\"}',NULL,NULL,'2021-12-18 11:53:09','2021-12-18 11:53:09',1);
/*!40000 ALTER TABLE `teams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `two_factor_secret` text COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text COLLATE utf8mb4_unicode_ci,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_team_id` bigint unsigned DEFAULT NULL,
  `profile_photo_path` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `fb_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `github_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'utopszkij','tibor.fogler@gmail.com',NULL,'$2y$10$ZseynVdzglrpBr1UIBKpLuUDrKzV4d5dH02tL3/20eTdKLX8sRTYO',NULL,NULL,'qBEAmirtjyC00e9SuvrI1C6oysMLaTGJprRtOqYOW8duT7uUm8T7KkpKiYIp',0,'','2022-02-07 12:08:45','2022-02-07 12:08:45',NULL,NULL,NULL),(2,'teszt1','teszt1@teszt.hu',NULL,'$2y$10$NlaHjraP/zzHLFlBOxIA9Oi2It7WMksmd6/zDadUYRLDFb930qlKq',NULL,NULL,'g7gn6mszdphqHkMNvciCKEjOvwB1zyepjpWpYU9dt21LW8gJpI2BLpKjtsOr',NULL,'','2022-02-08 09:43:26','2022-02-08 09:43:26',NULL,NULL,NULL),(3,'teszt2','teszt2@teszt.hu',NULL,'$2y$10$STinYHP9oaZ3YlVkIiqW7uwLcAocHF38XXjWqXhbhzXaWe9wr2b3G',NULL,NULL,NULL,NULL,'','2022-02-08 09:44:49','2022-02-08 09:44:49',NULL,NULL,NULL),(94,'testUser1','testUser1email@something.com',NULL,'$2y$04$qUBn73UwjN6GcAtB6tNi/e/RVoEgi.SdYKmhJM07Z9yAzvcy3NpSS',NULL,NULL,'YFRv1xlaqMqPiuEsSsBTIWKY7IxB7AIMLx02JsuoQQghYOixDkLhcK8Q0yqr',NULL,'','2022-02-11 13:14:38','2022-02-11 13:14:38',NULL,NULL,NULL),(95,'testUser2','testUser2email@something.com',NULL,'$2y$04$yuwvsNooFCCszM0veg5pNufdUFKFgi6CWokXn8z8eh0We9NS1XpW2',NULL,NULL,'h860RGphv5YOAB9Tfqe8PG70cdnE2rcnuEhWLCDQhvp5nxOpWWXKKL4uyBhv',NULL,'','2022-02-11 13:14:39','2022-02-11 13:14:39',NULL,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `votes`
--

DROP TABLE IF EXISTS `votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `votes` (
  `poll_id` bigint unsigned NOT NULL COMMENT 'szavazás ID',
  `ballot_id` bigint unsigned NOT NULL COMMENT 'szavazólap ID',
  `option_id` bigint unsigned NOT NULL COMMENT 'opció ID (erre szavaz)',
  `position` int DEFAULT NULL COMMENT 'sorbarendező szavazásnál a pozió ahová sorolta',
  `accredited_id` bigint DEFAULT NULL COMMENT 'ha a user helyett a likvid képviselője szavazott',
  `user_id` bigint DEFAULT NULL COMMENT 'szavazó user ID (titkos szavazásnál nincs tárolva)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'rekord létrehozás időpontja',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'rekord utolsó modositás időpontja',
  KEY `votes_ballot_id` (`ballot_id`),
  KEY `votes_option_id_foreign` (`option_id`),
  KEY `votes_poll_id_foreign` (`poll_id`),
  CONSTRAINT `votes_ballot_id` FOREIGN KEY (`ballot_id`) REFERENCES `ballots` (`id`),
  CONSTRAINT `votes_option_id_foreign` FOREIGN KEY (`option_id`) REFERENCES `options` (`id`),
  CONSTRAINT `votes_poll_id_foreign` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `votes`
--

LOCK TABLES `votes` WRITE;
/*!40000 ALTER TABLE `votes` DISABLE KEYS */;
/*!40000 ALTER TABLE `votes` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-02-11 16:34:00
