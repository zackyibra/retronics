-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: retronics
-- ------------------------------------------------------
-- Server version	8.0.46

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `retronics`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `retronics` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `retronics`;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Kamera','Kategori kamera untuk kebutuhan fotografi.',1,'2026-10-14 07:44:36','2026-10-14 07:44:36'),(2,'Lensa','Kategori lensa untuk berbagai kebutuhan pemotretan.',1,'2026-10-14 07:44:36','2026-10-14 07:44:36'),(3,'Pencahayaan','Peralatan pencahayaan untuk fotografi.',1,'2026-10-14 07:44:36','2026-10-14 07:44:36'),(4,'Aksesori','Aksesori pendukung kegiatan fotografi.',1,'2026-10-14 07:44:36','2026-10-14 07:44:36');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
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
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
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
-- Table structure for table `fines`
--

DROP TABLE IF EXISTS `fines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `rental_id` bigint unsigned NOT NULL,
  `rental_item_id` bigint unsigned DEFAULT NULL,
  `type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `is_paid` tinyint(1) NOT NULL DEFAULT '0',
  `paid_at` timestamp NULL DEFAULT NULL,
  `payment_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `days_late` int unsigned DEFAULT NULL,
  `rate_per_day` decimal(12,2) DEFAULT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `assessed_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fines_rental_item_id_foreign` (`rental_item_id`),
  KEY `fines_assessed_by_foreign` (`assessed_by`),
  KEY `fines_rental_id_type_index` (`rental_id`,`type`),
  CONSTRAINT `fines_assessed_by_foreign` FOREIGN KEY (`assessed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fines_rental_id_foreign` FOREIGN KEY (`rental_id`) REFERENCES `rentals` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fines_rental_item_id_foreign` FOREIGN KEY (`rental_item_id`) REFERENCES `rental_items` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fines`
--

LOCK TABLES `fines` WRITE;
/*!40000 ALTER TABLE `fines` DISABLE KEYS */;
/*!40000 ALTER TABLE `fines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_10_14_140844_create_categories_table',2),(5,'2026_10_14_141610_create_products_table',3),(6,'2026_10_17_020439_add_profile_and_role_to_users_table',4),(7,'2026_10_17_020453_create_wishlists_table',4),(8,'2026_10_17_020505_create_rentals_table',4),(9,'2026_10_17_020507_create_rental_items_table',4),(10,'2026_10_19_135546_create_reviews_table',5),(11,'2026_10_24_100000_add_returned_at_to_rentals_table',6),(12,'2026_10_24_100001_create_fines_table',6),(13,'2026_10_25_012713_create_promotions_table',7),(14,'2026_10_25_012716_add_promo_to_rentals_table',7),(15,'2026_10_25_021326_create_password_reset_otps_table',8),(16,'2026_10_25_024000_add_is_paid_to_fines_table',9),(17,'2026_10_27_000000_create_payments_table',10),(18,'2026_11_01_150833_create_rental_verifications_table',11),(19,'2026_11_03_131044_add_birth_date_to_rental_verifications_table',12);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_otps`
--

DROP TABLE IF EXISTS `password_reset_otps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_otps` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `phone` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `otp` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `attempts` tinyint unsigned NOT NULL DEFAULT '0',
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `password_reset_otps_phone_index` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_otps`
--

LOCK TABLES `password_reset_otps` WRITE;
/*!40000 ALTER TABLE `password_reset_otps` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_otps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `rental_id` bigint unsigned NOT NULL,
  `payment_code` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_method` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_date` datetime NOT NULL,
  `payment_proof` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'waiting_verification',
  `verified_by` bigint unsigned DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_payment_code_unique` (`payment_code`),
  KEY `payments_verified_by_foreign` (`verified_by`),
  KEY `payments_rental_id_status_index` (`rental_id`,`status`),
  KEY `payments_payment_code_index` (`payment_code`),
  CONSTRAINT `payments_rental_id_foreign` FOREIGN KEY (`rental_id`) REFERENCES `rentals` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (5,12,'PAY-20261104-7WW5ZP',50000.00,'qris','2026-11-04 01:57:54','payments/1bKbC0LV14K9QUAzNXgKFXHNCVS4UAItSkkWq25y.jpg','success',1,'2026-11-04 02:17:45','Pembayaran diverifikasi valid oleh admin. Pesanan dikonfirmasi.','2026-11-03 18:57:54','2026-11-03 19:17:45');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price_per_day` decimal(12,2) NOT NULL,
  `stock` int unsigned NOT NULL DEFAULT '0',
  `condition` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'baik',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  KEY `products_category_id_foreign` (`category_id`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,1,'Canon EOS 1500D','canon-eos-1500d','Kamera DSLR 24.1 MP dengan sensor APS-C untuk fotografi pemula, dokumentasi acara, dan tugas kuliah.',150000.00,3,'baik','canon-eos-1500d.jpg',1,'2026-10-14 07:50:57','2026-10-19 06:51:17'),(2,2,'Canon EF 50mm f/1.8 STM','canon-ef-50mm','Lensa fix portrait aperture besar f/1.8 untuk efek bokeh lembut dan performa optimal di kondisi minim cahaya.',50000.00,0,'baik','canon-ef-50mm.jpg',1,'2026-10-14 07:50:57','2026-11-03 19:17:41'),(3,3,'LED Video Light Portable','led-video-light','Lampu LED bi-color portabel isi ulang dengan temperatur warna 3200K-5600K untuk video mobile & on-the-go.',35000.00,4,'baik','led-video-light.jpg',1,'2026-10-14 07:50:57','2026-10-17 07:45:40'),(4,4,'Tripod Kamera Profesional','tripod-kamera','Tripod paduan aluminium kokoh dengan 3-way pan fluid head dan quick release plate untuk stabilitas foto & video.',25000.00,5,'baik','tripod-kamera.jpg',1,'2026-10-14 07:50:57','2026-10-17 07:45:40'),(5,1,'Sony Alpha A7 III','sony-alpha-a7-iii','Kamera mirrorless full-frame profesional 24.2 MP dengan perekaman 4K HDR dan autofokus real-time eye AF.',250000.00,2,'sangat baik','sony-alpha-a7-iii.png',1,'2026-10-16 18:47:27','2026-10-19 07:06:09'),(6,2,'Sony FE 24-70mm f/2.8 GM','sony-fe-24-70mm-f2-8-gm','Lensa zoom standar profesional G Master bukaan konstan f/2.8 untuk portrait, lanskap, dan liputan komersial.',125000.00,2,'sangat baik','sony-fe-24-70mm-f2-8-gm.png',1,'2026-10-16 18:47:27','2026-10-17 07:45:40'),(7,3,'Godox SL-60W Video Light','godox-sl-60w','Continuous LED video light 5600K daylight-balanced dengan Bowens mount untuk podcast, wawancara, dan studio.',70000.00,3,'sangat baik','godox-sl-60w.png',1,'2026-10-16 18:47:27','2026-10-17 07:45:40'),(8,4,'DJI Ronin-SC Gimbal','dji-ronin-sc','Gimbal stabilizer 3-axis ringan berbahan magnesium alloy untuk kamera mirrorless dengan motor responsif.',85000.00,3,'sangat baik','dji-ronin-sc.png',1,'2026-10-16 18:47:27','2026-10-17 07:45:40'),(9,4,'Boya Wireless Microphone System','boya-wireless-microphone','Sistem mikrofon wireless 2.4GHz dual-channel dengan audio jernih untuk wawancara, livestreaming, dan vlogging.',45000.00,4,'sangat baik','boya-wireless-microphone.png',1,'2026-10-16 18:47:27','2026-10-17 07:45:40'),(10,1,'Canon EOS R50','canon-eos-r50','Kamera mirrorless RF-mount ringkas dan ringan, ideal untuk content creation, vlogging, dan travel photography.',200000.00,3,'sangat baik','canon-eos-r50.png',1,'2026-10-17 07:45:40','2026-10-17 07:45:40'),(11,1,'DJI Mini 3 Pro','dji-mini-3-pro','Drone lipat ultra-ringan di bawah 249g dengan video 4K/60fps HDR, sensor rintangan tri-directional, dan pemotretan vertikal.',220000.00,2,'sangat baik','dji-mini-3-pro.png',1,'2026-10-17 07:45:40','2026-10-17 07:45:40'),(12,2,'Canon EF-S 10-18mm f/4.5-5.6 IS STM','canon-ef-s-10-18mm','Lensa ultra-wide angle fleksibel dengan Image Stabilizer untuk arsitektur, lanskap luas, dan ruang sempit.',65000.00,3,'baik','canon-ef-s-10-18mm.png',1,'2026-10-17 07:45:40','2026-10-17 07:45:40'),(13,2,'Filter Lensa ND Variabel','filter-nd-variabel','Filter neutral density adjustable (ND2-ND400) multi-coating untuk kontrol pencahayaan sinematik.',25000.00,5,'sangat baik','filter-nd-variabel.png',1,'2026-10-17 07:45:40','2026-10-17 07:45:40'),(14,3,'Godox Octabox','godox-octabox','Softbox octagonal 95cm Bowens mount dengan grid sarang lebah untuk pencahayaan lembut berkarakter bundar.',35000.00,4,'sangat baik','godox-octabox.png',1,'2026-10-17 07:45:40','2026-10-17 07:45:40'),(15,3,'Godox TT685 II','godox-tt685-ii','Flash speedlite TTL dengan radio receiver nirkabel Godox 2.4G X-system, cocok untuk dokumentasi event & wedding.',55000.00,3,'sangat baik','godox-tt685-ii.png',1,'2026-10-17 07:45:40','2026-10-17 07:45:40'),(16,3,'Softbox Lighting Kit','softbox-lighting-kit','Paket 2 set continuous softbox lengkap dengan tripod stand dan lampu bohlam fotografi 5500K.',60000.00,3,'baik','softbox-lighting-kit.png',1,'2026-10-17 07:45:40','2026-10-17 07:45:40'),(17,3,'Reflektor 5-in-1','reflektor-5-in-1','Reflektor lipat bulat multi-panel (Silver, Gold, White, Black, Translucent) untuk pengarah dan pengisi cahaya.',20000.00,4,'baik','reflektor-5-in-1.png',1,'2026-10-17 07:45:40','2026-10-17 07:45:40'),(18,4,'DJI RS 3 Mini','dji-rs-3-mini','Gimbal stabilizer generasi baru berbobot 795g dengan kemampuan native vertical shooting untuk media sosial.',110000.00,2,'sangat baik','dji-rs-3-mini.png',1,'2026-10-17 07:45:40','2026-10-17 07:45:40'),(19,4,'RØDE VideoMic GO II','rode-videomic-go-ii','Mikrofon shotgun directional ultra-ringan dengan mounting shockmount Rycote Lyre dan output USB/3.5mm.',50000.00,3,'sangat baik','rode-videomic-go-ii.png',1,'2026-10-17 07:45:40','2026-10-17 07:45:40'),(20,4,'FEELWORLD Field Monitor','feelworld-field-monitor','Monitor eksternal 5.5 inci Full HD 4K HDMI dengan fitur waveform, false color, dan focus peaking.',75000.00,2,'sangat baik','feelworld-field-monitor.png',1,'2026-10-17 07:45:40','2026-10-17 07:45:40'),(21,4,'Camera Slider','camera-slider','Rel slider kamera presisi smooth bearing untuk pengambilan gambar tracking sinematik horizontal dan vertikal.',45000.00,3,'baik','camera-slider.png',1,'2026-10-17 07:45:40','2026-10-17 07:45:40'),(22,4,'Backdrop Studio','backdrop-studio','Stand background studio portabel lengkap dengan kain layar polos (Putih, Hitam, Hijau Greenscreen).',40000.00,3,'baik','backdrop-studio.png',1,'2026-10-17 07:45:40','2026-10-19 06:51:17'),(23,4,'Baterai Cadangan dan Charger','baterai-cadangan-charger','Paket 2 baterai rechargeable cadangan beserta dual slot fast charger untuk ketahanan produksi seharian.',25000.00,6,'sangat baik','baterai-cadangan-charger.png',1,'2026-10-17 07:45:40','2026-10-19 06:51:04'),(24,4,'Hard Case Kamera','hard-case-kamera','Koper pelindung gear tahan air (IP67) dan tahan banting dengan busa customisable pick & pluck foam.',35000.00,4,'sangat baik','hard-case-kamera.png',1,'2026-10-17 07:45:40','2026-10-17 07:45:40'),(25,4,'SanDisk Extreme PRO SDXC','sandisk-extreme-pro','Kartu memori SDXC 128GB kecepatan baca hingga 200MB/s V30 C10 UHS-I untuk rekam video 4K tanpa hambatan.',20000.00,8,'sangat baik','sandisk-extreme-pro.png',1,'2026-10-17 07:45:40','2026-10-17 07:45:40');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promotions`
--

DROP TABLE IF EXISTS `promotions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `promotions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percentage',
  `discount_value` decimal(12,2) NOT NULL,
  `max_discount` decimal(12,2) DEFAULT NULL,
  `min_transaction` decimal(12,2) NOT NULL DEFAULT '0.00',
  `quota_total` int unsigned NOT NULL DEFAULT '100',
  `quota_used` int unsigned NOT NULL DEFAULT '0',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `promotions_code_unique` (`code`),
  KEY `promotions_category_id_foreign` (`category_id`),
  CONSTRAINT `promotions_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promotions`
--

LOCK TABLES `promotions` WRITE;
/*!40000 ALTER TABLE `promotions` DISABLE KEYS */;
INSERT INTO `promotions` VALUES (1,'RETRONICS10','Promo Lebih Banyak Karya 10%','Potongan 10% untuk penyewaan seluruh bodi kamera mirrorless, DSLR, dan gear fotografi.','percentage',10.00,NULL,200000.00,50,0,'2026-01-01','2026-12-31',NULL,1,'2026-10-24 18:41:55','2026-11-03 07:40:44'),(2,'SEWAHEMAT','Potongan Langsung Rp25.000','Potongan nominal Rp25.000 untuk sewa gear fotografi apa pun tanpa batas hari.','fixed',25000.00,NULL,150000.00,100,0,'2026-01-01','2026-12-31',NULL,1,'2026-10-24 18:41:55','2026-11-03 07:40:44'),(3,'WEEKENDPROMO','Diskon 15% Akhir Pekan','Promo akhir pekan untuk project pemotretan hunting, wedding, atau event keluarga.','percentage',15.00,NULL,300000.00,40,0,'2026-01-01','2026-12-31',NULL,1,'2026-10-24 18:41:55','2026-11-03 07:40:44'),(4,'LIGHTING10','Spesial Pencahayaan Studio 10%','Potongan 10% khusus kategori perlengkapan pencahayaan & studio.','percentage',10.00,NULL,200000.00,20,0,'2026-01-01','2026-12-31',3,1,'2026-10-24 18:41:55','2026-11-03 07:40:44'),(5,'ARSIP25','Promo Arsip Kuota Habis','Voucher promo yang telah habis masa berlakunya atau kuota penuh.','fixed',25000.00,NULL,200000.00,5,0,'2026-01-01','2026-09-30',NULL,0,'2026-10-24 18:41:55','2026-11-03 07:40:44');
/*!40000 ALTER TABLE `promotions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rental_items`
--

DROP TABLE IF EXISTS `rental_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rental_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `rental_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `quantity` int unsigned NOT NULL DEFAULT '1',
  `price_per_day` decimal(12,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rental_items_rental_id_foreign` (`rental_id`),
  KEY `rental_items_product_id_foreign` (`product_id`),
  CONSTRAINT `rental_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rental_items_rental_id_foreign` FOREIGN KEY (`rental_id`) REFERENCES `rentals` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rental_items`
--

LOCK TABLES `rental_items` WRITE;
/*!40000 ALTER TABLE `rental_items` DISABLE KEYS */;
INSERT INTO `rental_items` VALUES (13,12,2,1,50000.00,50000.00,'2026-11-03 18:56:32','2026-11-03 18:56:32');
/*!40000 ALTER TABLE `rental_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rental_verifications`
--

DROP TABLE IF EXISTS `rental_verifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rental_verifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `rental_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `customer_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'adult',
  `id_card_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_card_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `student_card_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `family_card_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_relation` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_id_card_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_id_card_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_permission_letter_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rental_purpose` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact_phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact_relation` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `signature_data` longtext COLLATE utf8mb4_unicode_ci,
  `agreement_signed_at` datetime DEFAULT NULL,
  `agreement_ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `requires_stamp_duty` tinyint(1) NOT NULL DEFAULT '0',
  `stamp_duty_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `verification_status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu',
  `verified_by` bigint unsigned DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rental_verifications_verified_by_foreign` (`verified_by`),
  KEY `rental_verifications_rental_id_verification_status_index` (`rental_id`,`verification_status`),
  KEY `rental_verifications_user_id_index` (`user_id`),
  CONSTRAINT `rental_verifications_rental_id_foreign` FOREIGN KEY (`rental_id`) REFERENCES `rentals` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rental_verifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rental_verifications_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rental_verifications`
--

LOCK TABLES `rental_verifications` WRITE;
/*!40000 ALTER TABLE `rental_verifications` DISABLE KEYS */;
INSERT INTO `rental_verifications` VALUES (2,12,6,'adult','52525r252421','private/verifications/ktp/wk42WIM5rSFlcx1n965IQpP0P9q4ueCRkJ05a9il.jpg','2009-11-04',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'jeng naon wae','pais','081929513780','dulur','data:image/png;base64,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','2026-11-04 01:56:32','127.0.0.1',0,0.00,'disetujui',1,'2026-11-04 02:17:57','Disetujui bersamaan dengan verifikasi pembayaran.','2026-11-03 18:56:32','2026-11-03 19:17:57');
/*!40000 ALTER TABLE `rental_verifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rentals`
--

DROP TABLE IF EXISTS `rentals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rentals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `rental_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `returned_at` datetime DEFAULT NULL,
  `duration_days` int unsigned NOT NULL DEFAULT '1',
  `total_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `promo_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending_payment',
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_proof` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
  `item_condition_after` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rentals_rental_code_unique` (`rental_code`),
  KEY `rentals_user_id_foreign` (`user_id`),
  CONSTRAINT `rentals_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rentals`
--

LOCK TABLES `rentals` WRITE;
/*!40000 ALTER TABLE `rentals` DISABLE KEYS */;
INSERT INTO `rentals` VALUES (12,6,'RET-20261104-EKEPI','2026-11-05','2026-11-05',NULL,1,50000.00,NULL,0.00,'in_use','qris','payments/1bKbC0LV14K9QUAzNXgKFXHNCVS4UAItSkkWq25y.jpg',NULL,'Pembayaran diverifikasi valid oleh admin. Pesanan dikonfirmasi.',NULL,'2026-11-03 18:56:32','2026-11-03 19:22:14');
/*!40000 ALTER TABLE `rentals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reviews` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `rental_id` bigint unsigned NOT NULL,
  `rating` tinyint unsigned NOT NULL COMMENT '1–5 bintang',
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reviews_user_id_product_id_unique` (`user_id`,`product_id`),
  KEY `reviews_product_id_foreign` (`product_id`),
  KEY `reviews_rental_id_foreign` (`rental_id`),
  CONSTRAINT `reviews_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_rental_id_foreign` FOREIGN KEY (`rental_id`) REFERENCES `rentals` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
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
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
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
INSERT INTO `sessions` VALUES ('kmou26KEh2m9WSGGUgh1982MUSAm0lx69MqkaZyH',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0','eyJfdG9rZW4iOiI3dldJVXZFSjFTbnZFemlYR2VXSnZYRE1EWEtaU3RMZjV2VUpKVnNpIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2FkbWluXC9sb2dpbiIsInJvdXRlIjoiYWRtaW4ubG9naW4ifX0=',1793761296);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
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
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `id_card_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin Retronics','admin@retronics.test',NULL,'$2y$12$43W3/qC.zVDuEhagjcTIseWvDHqYEJemtLQWf6SMvIGQxSzDMcshi','admin','081234567890','Store Retronics Studio',NULL,NULL,'2026-10-16 19:07:20','2026-10-16 19:07:20'),(6,'JOKO WODODO','JOKO@gmail.com',NULL,'$2y$12$l2PFL/9tcDNdkP4H.NPzE.6HhSz8rsdd8BJ5hooEbw2DeidZlgq.K','customer','081929513780','Gunung Putri, Bogor','52525r252421',NULL,'2026-11-03 18:55:05','2026-11-03 18:56:32');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wishlists`
--

DROP TABLE IF EXISTS `wishlists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wishlists` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `wishlists_user_id_product_id_unique` (`user_id`,`product_id`),
  KEY `wishlists_product_id_foreign` (`product_id`),
  CONSTRAINT `wishlists_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `wishlists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wishlists`
--

LOCK TABLES `wishlists` WRITE;
/*!40000 ALTER TABLE `wishlists` DISABLE KEYS */;
/*!40000 ALTER TABLE `wishlists` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-04 10:31:14
