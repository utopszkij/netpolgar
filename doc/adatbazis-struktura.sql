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

DROP TABLE IF EXISTS `accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'egyedi rekord ID',
  `from_type` varchar(45) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'köldő tipus:  teams|users',
  `from` bigint unsigned NOT NULL COMMENT 'from_type.id',
  `target_type` varchar(45) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'címzett tipus: teams|users',
  `target` bigint unsigned NOT NULL COMMENT 'target_type.id',
  `status` varchar(45) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'üres|allocated',
  `value` float DEFAULT NULL COMMENT 'összeg',
  `comment` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'publikus közlemény',
  `info` varchar(45) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'program által használt közlemény',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'rekord létrehozás időpontja',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'rekord modosítás időpontja',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_hungarian_ci COMMENT='NTC folyószámla pénz mozgások';
/*!40101 SET character_set_client = @saved_cs_client */;

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
  KEY `ballots_poll_id_index` (`poll_id`),
  KEY `user_id_index` (`user_id`),
  CONSTRAINT `ballots_poll_id_foreign` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_hungarian_ci COMMENT='szavazó lapok. A szavazás megindulásakor generálódik minden szavazásra jogosult userhez.';
/*!40101 SET character_set_client = @saved_cs_client */;

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
  `value` int DEFAULT NULL COMMENT '"érdemjegy 1 - 5',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'létrehozás időpontja',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'utolsó modosítás időpontja',
  PRIMARY KEY (`id`),
  KEY `evaluations_product_id_index` (`product_id`),
  KEY `evaluations_user_id_foreign_idx` (`user_id`),
  CONSTRAINT `evaluations_product_id_foreign1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `evaluations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_hungarian_ci COMMENT='Termék értékelések';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'egyedi rekord ID',
  `parent_type` varchar(45) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'szervező tipusa: teams|projects',
  `parent` bigint unsigned NOT NULL COMMENT 'szervező ID',
  `name` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'esemény rövid neve',
  `description` mediumtext COLLATE utf8_hungarian_ci COMMENT 'esemény leírása',
  `avatar` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'avatar kép url',
  `location` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'helyszin',
  `date` date DEFAULT NULL COMMENT 'dátum',
  `hours` int DEFAULT NULL COMMENT 'kezdés óra',
  `minute` int DEFAULT NULL COMMENT 'kezdés perc',
  `length` varchar(255) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'esemény időtartama',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'rekord létrehozás időpontja',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'utolsó modosítás időpontja',
  `created_by` bigint unsigned NOT NULL COMMENT 'létrehozó user',
  PRIMARY KEY (`id`,`length`),
  KEY `events_created_by_foreign_idx` (`created_by`),
  CONSTRAINT `events_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_hungarian_ci COMMENT='események';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `files` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'egyedi ID',
  `parent_type` varchar(45) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'tulajdonos tipusa:  teams|projects|products|events|users',
  `parent` bigint unsigned NOT NULL COMMENT 'tilajdonos ID',
  `name` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'rövid megnevezés',
  `description` mediumtext COLLATE utf8_hungarian_ci COMMENT 'leírás',
  `type` varchar(45) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'file tipus',
  `licence` varchar(45) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'licensz',
  `created_by` bigint unsigned NOT NULL COMMENT 'feltöltő user',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'rekord létrehozás időpontja',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT 'utolsó modosítás időpontja',
  PRIMARY KEY (`id`),
  KEY `files_created_by_foreign_idx` (`created_by`),
  CONSTRAINT `files_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_hungarian_ci COMMENT='fileok';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `likes`
--

DROP TABLE IF EXISTS `likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `likes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parent_type` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'teams|members|projects|polls|options|products',
  `parent` bigint unsigned DEFAULT NULL COMMENT 'parent_type.id',
  `user_id` bigint unsigned NOT NULL COMMENT 'véleményező user',
  `like_type` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'like|dislike',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'rekord létrehozás időpontjaa',
  `updated_at` timestamp NOT NULL COMMENT 'utolsó módosítás időpontja',
  PRIMARY KEY (`id`),
  KEY `likes_parent_type_parent_index` (`parent_type`,`parent`),
  KEY `likes_user_id_index` (`user_id`),
  CONSTRAINT `user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_hungarian_ci COMMENT='tetszik/nem tetszik jelzések';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `members`
--

DROP TABLE IF EXISTS `members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `members` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'Egyedi rekord ID',
  `parent_type` varchar(255) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'teams|projects|events|files',
  `parent` bigint unsigned DEFAULT NULL COMMENT 'teams.id|projects.id|events.id',
  `user_id` bigint unsigned DEFAULT NULL COMMENT 'user.id',
  `rank` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'member|admin|moderator|...a teamben definiált tisztségek...',
  `status` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'proposal|active|closed|excluded',
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
) ENGINE=InnoDB AUTO_INCREMENT=158 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_hungarian_ci COMMENT='csoport, projekt   - tagság, tisztségek, esemény résztvevők,file letöltők';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'egyedi rekord ID',
  `parent_type` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'teams|projects|tasks|polls|options|users|events|files',
  `parent` bigint unsigned NOT NULL COMMENT 'parent_type.id',
  `reply_to` bigint unsigned DEFAULT NULL COMMENT 'ha ez egy válasz, akkor erre válaszol (messages.id)',
  `user_id` bigint unsigned NOT NULL COMMENT 'üzenete küldő felhasználó',
  `msg_type` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'későbbi fejlesztésre',
  `value` mediumtext COLLATE utf8_hungarian_ci COMMENT 'üzenet szövege',
  `moderated_by` bigint unsigned DEFAULT NULL COMMENT 'moderátor user.id',
  `moderator_info` mediumtext COLLATE utf8_hungarian_ci COMMENT 'moderátor megjegyzése',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'létrehozás időpontja',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'utolsó modosítás időpontja',
  PRIMARY KEY (`id`),
  KEY `messages_parent_type_parent_index` (`parent_type`,`parent`),
  KEY `messages_user_id_index` (`user_id`),
  CONSTRAINT `messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_hungarian_ci COMMENT='Üzenetek';
/*!40101 SET character_set_client = @saved_cs_client */;

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
  KEY `msgreads_user_id_foreign_idx` (`user_id`),
  KEY `msgreads_msg_id_foreign_idx` (`msg_id`),
  CONSTRAINT `msgreads_msg_id_foreign` FOREIGN KEY (`msg_id`) REFERENCES `messages` (`id`),
  CONSTRAINT `msgreads_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_hungarian_ci COMMENT='üzenet olvasások';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `options`
--

DROP TABLE IF EXISTS `options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `options` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'egyedi rekord ID',
  `poll_id` bigint unsigned NOT NULL COMMENT 'vita, szavazás',
  `name` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'rövid megnevezés',
  `status` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'proposal|active',
  `description` mediumtext COLLATE utf8_hungarian_ci COMMENT 'leírás',
  `created_by` bigint unsigned NOT NULL COMMENT 'létrehozó user',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'létrehozás időpontja',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'utolsó modositás időpontja',
  PRIMARY KEY (`id`),
  KEY `options_poll_id_index` (`poll_id`),
  KEY `options_user_id_foreign_idx` (`created_by`),
  CONSTRAINT `options_poll_id_foreign` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`),
  CONSTRAINT `options_user_id_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_hungarian_ci COMMENT='eldöntendő kérdés,szavazás - választható opciók';
/*!40101 SET character_set_client = @saved_cs_client */;

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
  `status` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'open|ordering|confirmed|denied|closed1|closed2|canceled',
  `confirmInfo` mediumtext COLLATE utf8_hungarian_ci COMMENT 'információ',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'rekord létrehozás időpontja',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'utolsó modositás időpontja',
  PRIMARY KEY (`id`),
  KEY `orderitems_order_id_index` (`order_id`),
  KEY `orderitems_product_id_index` (`product_id`),
  CONSTRAINT `orderitems_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  CONSTRAINT `orderitems_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_hungarian_ci COMMENT='kosár,megrendelés tételek';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'egyedi rekord ID',
  `customer_type` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'megrendelő tipusa teams|users',
  `customer` bigint unsigned NOT NULL COMMENT 'megrendelő ID',
  `status` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'open|ordering|closed2|closed2',
  `description` mediumtext COLLATE utf8_hungarian_ci COMMENT 'leírás',
  `address` mediumtext COLLATE utf8_hungarian_ci COMMENT 'szállítász, átvételi cím',
  `shipping` mediumtext COLLATE utf8_hungarian_ci COMMENT 'szállítási mód',
  `confirmInfo` mediumtext COLLATE utf8_hungarian_ci COMMENT 'információ',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'rekord létrehozás időpontja',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'rekord modositás időpontja',
  PRIMARY KEY (`id`),
  KEY `orders_user_id_index` (`customer_type`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_hungarian_ci COMMENT='bevásárló kosár vagy  megrendelés';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `polls`
--

DROP TABLE IF EXISTS `polls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `polls` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'rekord egyedi ID',
  `parent_type` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'parent tipus teams|projects',
  `parent` bigint unsigned NOT NULL COMMENT 'parent_type.id',
  `name` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'rövid megnevezés',
  `status` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'proposal|deabate|voks|closed',
  `description` mediumtext COLLATE utf8_hungarian_ci COMMENT 'leírás',
  `config` mediumtext COLLATE utf8_hungarian_ci COMMENT 'beállítások JSON string',
  `created_by` bigint unsigned NOT NULL COMMENT 'létrehozó user',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'rekord létrehozás időpontja',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'utolsó modosítás időpontja',
  `debate_start` date DEFAULT NULL COMMENT 'vita megindulásának dátuma',
  PRIMARY KEY (`id`),
  KEY `polls_parent_type_parent_index` (`parent_type`,`parent`),
  KEY `polls_created_by_foreign_idx` (`created_by`),
  CONSTRAINT `polls_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_hungarian_ci COMMENT='vita, szavazás';
/*!40101 SET character_set_client = @saved_cs_client */;

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
  KEY `productadds_product_id_index` (`product_id`),
  KEY `productadds_user_id_foreign_idx` (`user_id`),
  CONSTRAINT `productadds_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_hungarian_ci COMMENT='termék készlet növelések';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `productcats`
--

DROP TABLE IF EXISTS `productcats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `productcats` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'Egyedi rekord ID',
  `product_id` bigint unsigned NOT NULL COMMENT 'produkt ID',
  `category` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'kategória ID',
  PRIMARY KEY (`id`),
  KEY `productcats_product_id_index` (`product_id`),
  KEY `productcats_category_index` (`category`),
  CONSTRAINT `productcats_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_hungarian_ci COMMENT='termék kategóriák kapcsoló tábla';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'egyedi rekord ID',
  `parent_type` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'gyártó, forgalmazó tipus:  teams|users',
  `parent` bigint unsigned NOT NULL COMMENT 'parent_type.id',
  `name` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'rövid megnevezés',
  `unit` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'mértékegység',
  `price` double DEFAULT NULL COMMENT 'egységár',
  `currency` varchar(255) COLLATE utf8_hungarian_ci DEFAULT 'NTC' COMMENT 'pénznem',
  `vat` double DEFAULT NULL COMMENT 'ÁFA%',
  `type` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'tipus',
  `description` mediumtext COLLATE utf8_hungarian_ci COMMENT 'leírás',
  `avatar` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'avatar kép url',
  `status` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'active|disabled',
  `stock` double DEFAULT NULL COMMENT 'készlet',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'rekord létrehozás időpontja',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'utolsó modosítás időpontja',
  PRIMARY KEY (`id`),
  KEY `team_id` (`parent_type`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_hungarian_ci COMMENT='termékek';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `projects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'egyedi rekord ID',
  `team_id` bigint unsigned NOT NULL COMMENT 'csoport ID',
  `name` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'projekt rövid neve',
  `description` mediumtext COLLATE utf8_hungarian_ci COMMENT 'projekt leírása',
  `avatar` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'projekt avatar kép url',
  `status` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'proposal|active|closed',
  `deadline` date DEFAULT NULL COMMENT 'határidő',
  `config` mediumtext COLLATE utf8_hungarian_ci COMMENT 'JSON string',
  `activated_at` date DEFAULT NULL COMMENT 'aktiválás dátuma',
  `closed_at` date DEFAULT NULL COMMENT 'lezárás dátuma',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'rekord létrehozás időpontja',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'rekord utolsó modositás időpontja',
  `created_by` bigint unsigned NOT NULL COMMENT 'létrehozó user',
  PRIMARY KEY (`id`),
  KEY `team_id_foreign_idx` (`team_id`),
  KEY `projects_created_by_foreign` (`created_by`),
  CONSTRAINT `projects_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_hungarian_ci COMMENT='projektek';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tasks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'egyedi rekord ID',
  `project_id` bigint unsigned NOT NULL COMMENT 'projekt ID',
  `name` mediumtext COLLATE utf8_hungarian_ci COMMENT 'rövid leírás',
  `status` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'waiting|canwork|working|cancontroll|incontrol|closed',
  `position` int DEFAULT NULL COMMENT 'prioritás (kics a sürgősebb)',
  `deadline` date DEFAULT NULL COMMENT 'határidő',
  `type` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'bug|task|info|question',
  `assign` bigint unsigned DEFAULT NULL COMMENT 'felelős user',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'rekord létrehozás időpontja',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'utolsó modositás időpontja',
  PRIMARY KEY (`id`),
  KEY `project_id_foreign_idx` (`project_id`),
  CONSTRAINT `project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_hungarian_ci COMMENT='projekt feladatok';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `teams`
--

DROP TABLE IF EXISTS `teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teams` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'egyedi rekord ID',
  `parent` bigint unsigned DEFAULT NULL COMMENT 'csoport fa szerkezetet alakitja ki',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'csoport neve',
  `description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'csoport leírása',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'avatar kép url',
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'proposal|active|closed',
  `config` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'json string',
  `activated_at` date DEFAULT NULL COMMENT 'csoport megynitás dátuma',
  `closed_at` date DEFAULT NULL COMMENT 'csoport lezárás dátuma',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'rekord létrehozás időpontja',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'rekord utolsó modisítás időpontja',
  `created_by` bigint unsigned NOT NULL COMMENT 'létrehozó user',
  PRIMARY KEY (`id`),
  KEY `teams_created_by_foreign` (`created_by`),
  CONSTRAINT `teams_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=143 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_hungarian_ci COMMENT='csoportok';
/*!40101 SET character_set_client = @saved_cs_client */;

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
  `profile_photo_path` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `fb_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `github_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=206 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  KEY `votes_poll_id_index` (`poll_id`),
  KEY `votes_ballot_id_index` (`ballot_id`),
  KEY `votes_option_id_foreign_idx` (`option_id`),
  CONSTRAINT `votes_ballot_id` FOREIGN KEY (`ballot_id`) REFERENCES `ballots` (`id`),
  CONSTRAINT `votes_option_id_foreign` FOREIGN KEY (`option_id`) REFERENCES `options` (`id`),
  CONSTRAINT `votes_poll_id_foreign` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_hungarian_ci COMMENT='leadott szavazatok';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-02-06 11:40:11
