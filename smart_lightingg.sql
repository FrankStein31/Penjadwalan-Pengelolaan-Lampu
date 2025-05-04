/*
SQLyog Enterprise v13.1.1 (64 bit)
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
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `energi` */

insert  into `energi`(`id`,`lampu_id`,`energi`,`kondisi`,`durasi`,`week`,`month`,`year`,`created_at`,`updated_at`) values 
(1,2,0.00,0,1,18,5,2025,'2025-05-04 11:03:20','2025-05-04 11:03:20'),
(2,2,10.00,3,1,18,5,2025,'2025-05-04 11:03:22','2025-05-04 11:03:22'),
(3,2,3.00,1,1,18,5,2025,'2025-05-04 11:03:24','2025-05-04 11:03:24'),
(4,2,0.00,0,1,18,5,2025,'2025-05-04 11:03:25','2025-05-04 11:03:25'),
(5,2,10.00,3,1,18,5,2025,'2025-05-04 11:03:26','2025-05-04 11:03:26'),
(6,2,10.00,3,1,18,5,2025,'2025-05-04 11:03:27','2025-05-04 11:03:27'),
(7,2,0.00,0,1,18,5,2025,'2025-05-04 11:03:28','2025-05-04 11:03:28'),
(8,2,0.00,0,1,18,5,2025,'2025-05-04 11:03:29','2025-05-04 11:03:29'),
(9,3,10.00,3,1,18,5,2025,'2025-05-04 11:03:30','2025-05-04 11:03:30'),
(10,3,10.00,3,1,18,5,2025,'2025-05-04 11:03:30','2025-05-04 11:03:30'),
(11,3,0.00,0,1,18,5,2025,'2025-05-04 11:03:31','2025-05-04 11:03:31'),
(12,3,0.00,0,1,18,5,2025,'2025-05-04 11:03:32','2025-05-04 11:03:32'),
(13,6,10.00,3,1,18,5,2025,'2025-05-04 11:03:33','2025-05-04 11:03:33'),
(14,6,10.00,3,1,18,5,2025,'2025-05-04 11:03:33','2025-05-04 11:03:33'),
(15,6,0.00,0,1,18,5,2025,'2025-05-04 11:03:34','2025-05-04 11:03:34'),
(16,6,0.00,0,1,18,5,2025,'2025-05-04 11:03:35','2025-05-04 11:03:35'),
(17,2,10.00,3,1,18,5,2025,'2025-05-04 11:11:07','2025-05-04 11:11:07'),
(18,2,10.00,3,1,18,5,2025,'2025-05-04 11:11:07','2025-05-04 11:11:07'),
(19,2,3.00,1,1,18,5,2025,'2025-05-04 11:11:28','2025-05-04 11:11:28'),
(20,2,0.00,0,1,18,5,2025,'2025-05-04 11:13:02','2025-05-04 11:13:02'),
(21,2,0.00,0,1,18,5,2025,'2025-05-04 11:13:03','2025-05-04 11:13:03'),
(22,2,10.00,3,1,18,5,2025,'2025-05-04 11:17:36','2025-05-04 11:17:36'),
(23,2,10.00,3,1,18,5,2025,'2025-05-04 11:17:37','2025-05-04 11:17:37'),
(24,3,10.00,3,1,18,5,2025,'2025-05-04 11:17:39','2025-05-04 11:17:39'),
(25,3,10.00,3,1,18,5,2025,'2025-05-04 11:17:40','2025-05-04 11:17:40'),
(26,6,10.00,3,1,18,5,2025,'2025-05-04 11:17:40','2025-05-04 11:17:40'),
(27,6,10.00,3,1,18,5,2025,'2025-05-04 11:17:41','2025-05-04 11:17:41'),
(28,2,0.00,0,1,18,5,2025,'2025-05-04 11:19:58','2025-05-04 11:19:58'),
(29,2,0.00,0,1,18,5,2025,'2025-05-04 11:19:59','2025-05-04 11:19:59'),
(30,3,0.00,0,1,18,5,2025,'2025-05-04 11:20:00','2025-05-04 11:20:00'),
(31,3,0.00,0,1,18,5,2025,'2025-05-04 11:20:00','2025-05-04 11:20:00'),
(32,6,0.00,0,1,18,5,2025,'2025-05-04 11:20:01','2025-05-04 11:20:01'),
(33,6,0.00,0,1,18,5,2025,'2025-05-04 11:20:01','2025-05-04 11:20:01'),
(34,2,10.00,3,1,18,5,2025,'2025-05-04 11:20:18','2025-05-04 11:20:18'),
(35,2,10.00,3,1,18,5,2025,'2025-05-04 11:20:18','2025-05-04 11:20:18'),
(36,3,10.00,3,1,18,5,2025,'2025-05-04 11:20:19','2025-05-04 11:20:19'),
(37,3,10.00,3,1,18,5,2025,'2025-05-04 11:20:20','2025-05-04 11:20:20'),
(38,6,10.00,3,1,18,5,2025,'2025-05-04 11:20:21','2025-05-04 11:20:21'),
(39,6,10.00,3,1,18,5,2025,'2025-05-04 11:20:21','2025-05-04 11:20:21'),
(40,2,10.00,3,1,18,5,2025,'2025-05-04 11:20:23','2025-05-04 11:20:23'),
(41,2,7.00,2,1,18,5,2025,'2025-05-04 11:20:25','2025-05-04 11:20:25'),
(42,3,10.00,3,1,18,5,2025,'2025-05-04 11:20:26','2025-05-04 11:20:26'),
(43,3,3.00,1,1,18,5,2025,'2025-05-04 11:20:29','2025-05-04 11:20:29'),
(44,2,0.00,0,1,18,5,2025,'2025-05-04 11:22:22','2025-05-04 11:22:22'),
(45,2,0.00,0,1,18,5,2025,'2025-05-04 11:22:22','2025-05-04 11:22:22'),
(46,3,0.00,0,1,18,5,2025,'2025-05-04 11:22:23','2025-05-04 11:22:23'),
(47,3,0.00,0,1,18,5,2025,'2025-05-04 11:22:23','2025-05-04 11:22:23'),
(48,6,0.00,0,1,18,5,2025,'2025-05-04 11:22:24','2025-05-04 11:22:24'),
(49,6,0.00,0,1,18,5,2025,'2025-05-04 11:22:24','2025-05-04 11:22:24'),
(50,2,10.00,3,1,18,5,2025,'2025-05-04 11:22:25','2025-05-04 11:22:25'),
(51,2,10.00,3,1,18,5,2025,'2025-05-04 11:22:26','2025-05-04 11:22:26'),
(52,3,10.00,3,1,18,5,2025,'2025-05-04 11:22:26','2025-05-04 11:22:26'),
(53,3,10.00,3,1,18,5,2025,'2025-05-04 11:22:27','2025-05-04 11:22:27'),
(54,6,10.00,3,1,18,5,2025,'2025-05-04 11:22:27','2025-05-04 11:22:27'),
(55,6,10.00,3,1,18,5,2025,'2025-05-04 11:22:28','2025-05-04 11:22:28'),
(56,2,0.00,0,1,18,5,2025,'2025-05-04 11:25:17','2025-05-04 11:25:17'),
(57,2,0.00,0,1,18,5,2025,'2025-05-04 11:25:18','2025-05-04 11:25:18'),
(58,3,0.00,0,1,18,5,2025,'2025-05-04 11:25:19','2025-05-04 11:25:19'),
(59,3,0.00,0,1,18,5,2025,'2025-05-04 11:25:19','2025-05-04 11:25:19'),
(60,3,10.00,3,1,18,5,2025,'2025-05-04 11:25:20','2025-05-04 11:25:20'),
(61,3,10.00,3,1,18,5,2025,'2025-05-04 11:25:20','2025-05-04 11:25:20'),
(62,6,0.00,0,1,18,5,2025,'2025-05-04 11:25:21','2025-05-04 11:25:21'),
(63,6,0.00,0,1,18,5,2025,'2025-05-04 11:25:21','2025-05-04 11:25:21'),
(64,3,0.00,0,1,18,5,2025,'2025-05-04 11:25:22','2025-05-04 11:25:22'),
(65,3,0.00,0,1,18,5,2025,'2025-05-04 11:25:22','2025-05-04 11:25:22');

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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `jadwal_lampu_id_foreign` (`lampu_id`),
  CONSTRAINT `jadwal_lampu_id_foreign` FOREIGN KEY (`lampu_id`) REFERENCES `lampu` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `jadwal` */

/*Table structure for table `lampu` */

DROP TABLE IF EXISTS `lampu`;

CREATE TABLE `lampu` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_lampu` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lokasi` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `intensitas` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `lampu` */

insert  into `lampu`(`id`,`nama_lampu`,`lokasi`,`status`,`intensitas`,`created_at`,`updated_at`) values 
(2,'A1','dapurr',0,0,'2025-04-24 04:59:35','2025-05-04 11:25:17'),
(3,'A2','toilet',0,0,'2025-04-24 05:04:59','2025-05-04 11:25:22'),
(6,'A3','aaaaaaaaaaaaaa',0,0,'2025-05-04 10:56:07','2025-05-04 11:25:21');

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `migrations` */

insert  into `migrations`(`id`,`migration`,`batch`) values 
(1,'2014_10_12_000000_create_users_table',1),
(2,'2014_10_12_100000_create_password_reset_tokens_table',1),
(3,'2019_08_19_000000_create_failed_jobs_table',1),
(4,'2019_12_14_000001_create_personal_access_tokens_table',1),
(5,'2025_03_01_063754_create_lampus_table',1),
(6,'2025_03_01_063801_create_jadwals_table',1),
(8,'2025_03_01_063818_create_notifikasis_table',1),
(9,'2025_03_01_063807_create_energis_table',2);

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
