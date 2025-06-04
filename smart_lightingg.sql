/*
SQLyog Enterprise
MySQL - 8.0.30 : Database - smart_lightingg
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`smart_lightingg` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `smart_lightingg`;

/*Table structure for table `energi` */

DROP TABLE IF EXISTS `energi`;

CREATE TABLE `energi` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `lampu_id` bigint unsigned NOT NULL,
  `energi` double(8,2) NOT NULL COMMENT 'Konsumsi daya dalam watt',
  `kondisi` tinyint NOT NULL COMMENT '0:Mati, 1:Redup, 2:Sedang, 3:Terang',
  `durasi` int NOT NULL DEFAULT '1' COMMENT 'Lama penggunaan dalam menit',
  `week` int NOT NULL,
  `month` int NOT NULL,
  `year` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `energi_lampu_id_foreign` (`lampu_id`),
  CONSTRAINT `energi_lampu_id_foreign` FOREIGN KEY (`lampu_id`) REFERENCES `lampu` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=230 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `energi` */

insert  into `energi`(`id`,`lampu_id`,`energi`,`kondisi`,`durasi`,`week`,`month`,`year`,`created_at`,`updated_at`) values 
(9,3,10.00,3,1,18,5,2025,'2025-05-04 11:03:30','2025-05-04 11:03:30'),
(10,3,10.00,3,1,18,5,2025,'2025-05-04 11:03:30','2025-05-04 11:03:30'),
(11,3,0.00,0,1,18,5,2025,'2025-05-04 11:03:31','2025-05-04 11:03:31'),
(12,3,0.00,0,1,18,5,2025,'2025-05-04 11:03:32','2025-05-04 11:03:32'),
(24,3,10.00,3,1,18,5,2025,'2025-05-04 11:17:39','2025-05-04 11:17:39'),
(25,3,10.00,3,1,18,5,2025,'2025-05-04 11:17:40','2025-05-04 11:17:40'),
(30,3,0.00,0,1,18,5,2025,'2025-05-04 11:20:00','2025-05-04 11:20:00'),
(31,3,0.00,0,1,18,5,2025,'2025-05-04 11:20:00','2025-05-04 11:20:00'),
(36,3,10.00,3,1,18,5,2025,'2025-05-04 11:20:19','2025-05-04 11:20:19'),
(37,3,10.00,3,1,18,5,2025,'2025-05-04 11:20:20','2025-05-04 11:20:20'),
(42,3,10.00,3,1,18,5,2025,'2025-05-04 11:20:26','2025-05-04 11:20:26'),
(43,3,3.00,1,1,18,5,2025,'2025-05-04 11:20:29','2025-05-04 11:20:29'),
(46,3,0.00,0,1,18,5,2025,'2025-05-04 11:22:23','2025-05-04 11:22:23'),
(47,3,0.00,0,1,18,5,2025,'2025-05-04 11:22:23','2025-05-04 11:22:23'),
(52,3,10.00,3,1,18,5,2025,'2025-05-04 11:22:26','2025-05-04 11:22:26'),
(53,3,10.00,3,1,18,5,2025,'2025-05-04 11:22:27','2025-05-04 11:22:27'),
(58,3,0.00,0,1,18,5,2025,'2025-05-04 11:25:19','2025-05-04 11:25:19'),
(59,3,0.00,0,1,18,5,2025,'2025-05-04 11:25:19','2025-05-04 11:25:19'),
(60,3,10.00,3,1,18,5,2025,'2025-05-04 11:25:20','2025-05-04 11:25:20'),
(61,3,10.00,3,1,18,5,2025,'2025-05-04 11:25:20','2025-05-04 11:25:20'),
(64,3,0.00,0,1,18,5,2025,'2025-05-04 11:25:22','2025-05-04 11:25:22'),
(65,3,0.00,0,1,18,5,2025,'2025-05-04 11:25:22','2025-05-04 11:25:22'),
(68,3,10.00,3,1,18,5,2025,'2025-05-04 12:05:44','2025-05-04 12:05:44'),
(69,3,10.00,3,1,18,5,2025,'2025-05-04 12:05:45','2025-05-04 12:05:45'),
(74,3,0.00,0,1,18,5,2025,'2025-05-04 12:05:56','2025-05-04 12:05:56'),
(75,3,0.00,0,1,18,5,2025,'2025-05-04 12:05:56','2025-05-04 12:05:56'),
(92,3,10.00,3,1,18,5,2025,'2025-05-04 12:36:33','2025-05-04 12:36:33'),
(93,3,10.00,3,1,18,5,2025,'2025-05-04 12:36:34','2025-05-04 12:36:34'),
(98,3,10.00,3,1,18,5,2025,'2025-05-04 12:36:43','2025-05-04 12:36:43'),
(99,3,7.00,2,1,18,5,2025,'2025-05-04 12:36:46','2025-05-04 12:36:46'),
(102,3,0.00,0,1,18,5,2025,'2025-05-04 12:36:58','2025-05-04 12:36:58'),
(103,3,0.00,0,1,18,5,2025,'2025-05-04 12:36:59','2025-05-04 12:36:59'),
(133,3,0.00,0,1,23,6,2025,'2025-06-03 11:09:51','2025-06-03 11:09:51'),
(134,3,10.00,3,1,23,6,2025,'2025-06-03 11:10:28','2025-06-03 11:10:28'),
(135,3,10.00,3,1,23,6,2025,'2025-06-03 11:10:28','2025-06-03 11:10:28'),
(136,3,0.00,0,1,23,6,2025,'2025-06-03 11:10:37','2025-06-03 11:10:37'),
(137,3,0.00,0,1,23,6,2025,'2025-06-03 11:10:38','2025-06-03 11:10:38'),
(138,3,10.00,3,1,23,6,2025,'2025-06-03 11:10:57','2025-06-03 11:10:57'),
(139,3,10.00,3,1,23,6,2025,'2025-06-03 11:10:57','2025-06-03 11:10:57'),
(140,3,0.00,0,1,23,6,2025,'2025-06-03 11:11:08','2025-06-03 11:11:08'),
(141,3,0.00,0,1,23,6,2025,'2025-06-03 11:11:09','2025-06-03 11:11:09'),
(142,3,10.00,3,1,23,6,2025,'2025-06-03 11:12:58','2025-06-03 11:12:58'),
(143,3,10.00,3,1,23,6,2025,'2025-06-03 11:12:58','2025-06-03 11:12:58'),
(144,3,0.00,0,1,23,6,2025,'2025-06-03 11:13:05','2025-06-03 11:13:05'),
(145,3,0.00,0,1,23,6,2025,'2025-06-03 11:13:06','2025-06-03 11:13:06'),
(146,3,10.00,3,1,23,6,2025,'2025-06-03 11:31:39','2025-06-03 11:31:39'),
(147,3,10.00,3,1,23,6,2025,'2025-06-03 11:31:39','2025-06-03 11:31:39'),
(148,3,0.00,0,1,23,6,2025,'2025-06-03 11:31:48','2025-06-03 11:31:48'),
(149,3,0.00,0,1,23,6,2025,'2025-06-03 11:31:49','2025-06-03 11:31:49'),
(150,3,10.00,3,1,23,6,2025,'2025-06-03 11:31:50','2025-06-03 11:31:50'),
(151,3,10.00,3,1,23,6,2025,'2025-06-03 11:31:51','2025-06-03 11:31:51'),
(152,3,0.00,0,1,23,6,2025,'2025-06-03 11:31:55','2025-06-03 11:31:55'),
(153,3,0.00,0,1,23,6,2025,'2025-06-03 11:31:56','2025-06-03 11:31:56'),
(154,3,10.00,3,1,23,6,2025,'2025-06-03 11:32:22','2025-06-03 11:32:22'),
(155,3,10.00,3,1,23,6,2025,'2025-06-03 11:32:23','2025-06-03 11:32:23'),
(156,3,0.00,0,1,23,6,2025,'2025-06-03 11:32:45','2025-06-03 11:32:45'),
(157,3,0.00,0,1,23,6,2025,'2025-06-03 11:32:45','2025-06-03 11:32:45'),
(158,3,0.00,0,1,23,6,2025,'2025-06-03 11:32:58','2025-06-03 11:32:58'),
(159,3,10.00,3,1,23,6,2025,'2025-06-03 11:33:21','2025-06-03 11:33:21'),
(160,3,10.00,3,1,23,6,2025,'2025-06-03 11:33:21','2025-06-03 11:33:21'),
(161,3,10.00,3,1,23,6,2025,'2025-06-03 11:40:15','2025-06-03 11:40:15'),
(162,3,0.00,0,1,23,6,2025,'2025-06-03 11:40:18','2025-06-03 11:40:18'),
(163,3,0.00,0,1,23,6,2025,'2025-06-03 11:40:19','2025-06-03 11:40:19'),
(164,3,10.00,3,1,23,6,2025,'2025-06-03 11:40:21','2025-06-03 11:40:21'),
(165,3,10.00,3,1,23,6,2025,'2025-06-03 11:40:22','2025-06-03 11:40:22'),
(166,3,10.00,3,1,23,6,2025,'2025-06-03 11:43:47','2025-06-03 11:43:47'),
(167,3,0.00,0,1,23,6,2025,'2025-06-03 11:43:52','2025-06-03 11:43:52'),
(168,3,0.00,0,1,23,6,2025,'2025-06-03 11:43:53','2025-06-03 11:43:53'),
(169,3,10.00,3,1,23,6,2025,'2025-06-03 11:50:35','2025-06-03 11:50:35'),
(170,3,0.00,0,1,23,6,2025,'2025-06-03 11:50:41','2025-06-03 11:50:41'),
(171,3,10.00,3,1,23,6,2025,'2025-06-03 11:50:51','2025-06-03 11:50:51'),
(172,3,0.00,0,1,23,6,2025,'2025-06-03 11:50:59','2025-06-03 11:50:59'),
(173,3,10.00,3,1,23,6,2025,'2025-06-03 12:03:53','2025-06-03 12:03:53'),
(174,3,0.00,0,1,23,6,2025,'2025-06-03 12:03:55','2025-06-03 12:03:55'),
(175,3,10.00,3,1,23,6,2025,'2025-06-03 12:19:48','2025-06-03 12:19:48'),
(176,3,0.00,0,1,23,6,2025,'2025-06-03 12:19:52','2025-06-03 12:19:52'),
(177,3,10.00,3,1,23,6,2025,'2025-06-03 12:30:31','2025-06-03 12:30:31'),
(178,3,0.00,0,1,23,6,2025,'2025-06-03 12:30:34','2025-06-03 12:30:34'),
(179,3,10.00,3,1,23,6,2025,'2025-06-03 12:30:57','2025-06-03 12:30:57'),
(180,3,0.00,0,1,23,6,2025,'2025-06-03 12:31:02','2025-06-03 12:31:02'),
(181,3,10.00,3,1,23,6,2025,'2025-06-03 12:43:00','2025-06-03 12:43:00'),
(182,3,0.00,0,1,23,6,2025,'2025-06-03 12:43:05','2025-06-03 12:43:05'),
(183,3,10.00,3,1,23,6,2025,'2025-06-03 12:48:53','2025-06-03 12:48:53'),
(184,3,0.00,0,1,23,6,2025,'2025-06-03 12:48:55','2025-06-03 12:48:55'),
(185,3,10.00,3,1,23,6,2025,'2025-06-03 12:52:07','2025-06-03 12:52:07'),
(186,3,0.00,0,1,23,6,2025,'2025-06-03 12:52:09','2025-06-03 12:52:09'),
(187,3,10.00,3,1,23,6,2025,'2025-06-03 12:58:41','2025-06-03 12:58:41'),
(188,3,0.00,0,1,23,6,2025,'2025-06-03 12:58:45','2025-06-03 12:58:45'),
(189,3,10.00,3,1,23,6,2025,'2025-06-03 13:02:31','2025-06-03 13:02:31'),
(190,3,0.00,0,1,23,6,2025,'2025-06-03 13:02:33','2025-06-03 13:02:33'),
(191,3,10.00,3,1,23,6,2025,'2025-06-03 13:09:54','2025-06-03 13:09:54'),
(192,3,0.00,0,1,23,6,2025,'2025-06-03 13:09:58','2025-06-03 13:09:58'),
(193,3,10.00,3,1,23,6,2025,'2025-06-03 13:10:37','2025-06-03 13:10:37'),
(194,3,0.00,0,1,23,6,2025,'2025-06-03 13:10:45','2025-06-03 13:10:45'),
(195,3,10.00,3,1,23,6,2025,'2025-06-03 13:10:48','2025-06-03 13:10:48'),
(196,3,0.00,0,1,23,6,2025,'2025-06-03 13:10:56','2025-06-03 13:10:56'),
(197,3,10.00,3,1,23,6,2025,'2025-06-03 13:34:14','2025-06-03 13:34:14'),
(198,3,0.00,0,1,23,6,2025,'2025-06-03 13:34:16','2025-06-03 13:34:16'),
(199,3,10.00,3,1,23,6,2025,'2025-06-03 13:37:48','2025-06-03 13:37:48'),
(200,3,0.00,0,1,23,6,2025,'2025-06-03 13:38:21','2025-06-03 13:38:21'),
(201,3,10.00,3,1,23,6,2025,'2025-06-03 13:38:26','2025-06-03 13:38:26'),
(202,3,0.00,0,1,23,6,2025,'2025-06-03 13:38:39','2025-06-03 13:38:39'),
(203,3,10.00,3,1,23,6,2025,'2025-06-03 13:39:16','2025-06-03 13:39:16'),
(204,3,0.00,0,1,23,6,2025,'2025-06-03 13:39:23','2025-06-03 13:39:23'),
(205,3,10.00,3,1,23,6,2025,'2025-06-03 14:04:32','2025-06-03 14:04:32'),
(206,3,0.00,0,1,23,6,2025,'2025-06-03 14:04:37','2025-06-03 14:04:37'),
(207,3,10.00,3,1,23,6,2025,'2025-06-04 11:34:51','2025-06-04 11:34:51'),
(208,3,0.00,0,1,23,6,2025,'2025-06-04 11:34:54','2025-06-04 11:34:54'),
(209,3,10.00,3,1,23,6,2025,'2025-06-04 11:39:10','2025-06-04 11:39:10'),
(210,3,0.00,0,1,23,6,2025,'2025-06-04 11:39:13','2025-06-04 11:39:13'),
(211,3,10.00,3,1,23,6,2025,'2025-06-04 11:57:40','2025-06-04 11:57:40'),
(212,3,0.00,0,1,23,6,2025,'2025-06-04 11:57:43','2025-06-04 11:57:43'),
(213,3,10.00,3,1,23,6,2025,'2025-06-04 12:06:52','2025-06-04 12:06:52'),
(214,3,0.00,0,1,23,6,2025,'2025-06-04 12:06:57','2025-06-04 12:06:57'),
(215,3,10.00,3,1,23,6,2025,'2025-06-04 12:16:27','2025-06-04 12:16:27'),
(217,3,0.00,0,1,23,6,2025,'2025-06-04 12:16:39','2025-06-04 12:16:39'),
(218,3,10.00,3,1,23,6,2025,'2025-06-04 12:24:41','2025-06-04 12:24:41'),
(219,3,0.00,0,1,23,6,2025,'2025-06-04 12:24:47','2025-06-04 12:24:47'),
(220,3,10.00,3,1,23,6,2025,'2025-06-04 12:49:35','2025-06-04 12:49:35'),
(221,3,0.00,0,1,23,6,2025,'2025-06-04 12:49:40','2025-06-04 12:49:40'),
(222,3,10.00,3,1,23,6,2025,'2025-06-04 13:02:32','2025-06-04 13:02:32'),
(223,3,0.00,0,1,23,6,2025,'2025-06-04 13:02:38','2025-06-04 13:02:38'),
(224,3,10.00,3,1,23,6,2025,'2025-06-04 13:05:42','2025-06-04 13:05:42'),
(225,3,0.00,0,1,23,6,2025,'2025-06-04 13:05:48','2025-06-04 13:05:48'),
(226,3,10.00,3,1,23,6,2025,'2025-06-04 13:08:05','2025-06-04 13:08:05'),
(227,3,0.00,0,1,23,6,2025,'2025-06-04 13:08:08','2025-06-04 13:08:08'),
(228,3,10.00,3,1,23,6,2025,'2025-06-04 20:26:07','2025-06-04 20:26:07'),
(229,3,0.00,0,1,23,6,2025,'2025-06-04 20:26:35','2025-06-04 20:26:35');

/*Table structure for table `failed_jobs` */

DROP TABLE IF EXISTS `failed_jobs`;

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

/*Data for the table `failed_jobs` */

/*Table structure for table `jadwal` */

DROP TABLE IF EXISTS `jadwal`;

CREATE TABLE `jadwal` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `lampu_id` bigint unsigned NOT NULL,
  `hari` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `waktu_nyala` time NOT NULL,
  `waktu_mati` time NOT NULL,
  `frekuensi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'once' COMMENT 'once, daily, weekly, monthly',
  `tanggal_bulanan` int DEFAULT NULL COMMENT 'Tanggal untuk jadwal bulanan',
  `intensitas` int NOT NULL DEFAULT '100' COMMENT 'Intensitas cahaya lampu (0-100)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `jadwal_lampu_id_foreign` (`lampu_id`),
  CONSTRAINT `jadwal_lampu_id_foreign` FOREIGN KEY (`lampu_id`) REFERENCES `lampu` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `jadwal` */

insert  into `jadwal`(`id`,`lampu_id`,`hari`,`waktu_nyala`,`waktu_mati`,`frekuensi`,`tanggal_bulanan`,`intensitas`,`created_at`,`updated_at`) values 
(1,3,'Rabu','20:28:00','20:29:00','weekly',NULL,100,'2025-06-03 12:12:20','2025-06-04 20:26:54');

/*Table structure for table `lampu` */

DROP TABLE IF EXISTS `lampu`;

CREATE TABLE `lampu` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_lampu` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lokasi` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `otomatis` tinyint NOT NULL DEFAULT '1',
  `jadwal` tinyint NOT NULL DEFAULT '0',
  `intensitas` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `lampu` */

insert  into `lampu`(`id`,`nama_lampu`,`lokasi`,`status`,`otomatis`,`jadwal`,`intensitas`,`created_at`,`updated_at`) values 
(3,'A2','toilet',0,0,1,0,'2025-04-24 05:04:59','2025-06-04 20:28:51');

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `migrations` */

insert  into `migrations`(`id`,`migration`,`batch`) values 
(1,'2014_10_12_000000_create_users_table',1),
(2,'2014_10_12_100000_create_password_reset_tokens_table',1),
(3,'2019_08_19_000000_create_failed_jobs_table',1),
(4,'2019_12_14_000001_create_personal_access_tokens_table',1),
(5,'2025_03_01_063754_create_lampus_table',1),
(8,'2025_03_01_063818_create_notifikasis_table',1),
(9,'2025_03_01_063807_create_energis_table',2),
(11,'2025_03_01_063801_create_jadwals_table',3);

/*Table structure for table `notifikasi` */

DROP TABLE IF EXISTS `notifikasi`;

CREATE TABLE `notifikasi` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `lampu_id` bigint unsigned NOT NULL,
  `pesan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifikasi_lampu_id_foreign` (`lampu_id`),
  CONSTRAINT `notifikasi_lampu_id_foreign` FOREIGN KEY (`lampu_id`) REFERENCES `lampu` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `notifikasi` */

/*Table structure for table `password_reset_tokens` */

DROP TABLE IF EXISTS `password_reset_tokens`;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `password_reset_tokens` */

/*Table structure for table `personal_access_tokens` */

DROP TABLE IF EXISTS `personal_access_tokens`;

CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `personal_access_tokens` */

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `users` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
