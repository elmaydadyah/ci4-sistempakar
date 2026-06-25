-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: db_sistempakar
-- ------------------------------------------------------
-- Server version	8.0.30

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
-- Table structure for table `tb_teorema_bayes_likelihood`
--

DROP TABLE IF EXISTS `tb_teorema_bayes_likelihood`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_teorema_bayes_likelihood` (
  `id_likelihood` int unsigned NOT NULL AUTO_INCREMENT,
  `indikator` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `kategori` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `kelas` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `probabilitas` decimal(8,5) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_likelihood`),
  UNIQUE KEY `indikator_kategori_kelas` (`indikator`,`kategori`,`kelas`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_teorema_bayes_likelihood`
--

LOCK TABLES `tb_teorema_bayes_likelihood` WRITE;
/*!40000 ALTER TABLE `tb_teorema_bayes_likelihood` DISABLE KEYS */;
INSERT INTO `tb_teorema_bayes_likelihood` VALUES (1,'BB/U','Berat badan sangat kurang','H1',0.73000,'2026-05-17 09:11:07','2026-05-17 09:11:07'),(2,'BB/U','Berat badan sangat kurang','H2',0.24000,'2026-05-17 09:11:07','2026-05-17 09:11:07'),(3,'BB/U','Berat badan sangat kurang','H3',0.03000,'2026-05-17 09:11:07','2026-05-17 09:11:07'),(4,'BB/U','Berat badan kurang','H1',0.25000,'2026-05-17 09:11:07','2026-05-17 09:11:07'),(5,'BB/U','Berat badan kurang','H2',0.65000,'2026-05-17 09:11:07','2026-05-17 09:11:07'),(6,'BB/U','Berat badan kurang','H3',0.10000,'2026-05-17 09:11:07','2026-05-17 09:11:07'),(7,'BB/U','Berat badan normal','H1',0.03000,'2026-05-17 09:11:07','2026-05-17 09:11:07'),(8,'BB/U','Berat badan normal','H2',0.13000,'2026-05-17 09:11:07','2026-05-17 09:11:07'),(9,'BB/U','Berat badan normal','H3',0.84000,'2026-05-17 09:11:07','2026-05-17 09:11:07'),(10,'BB/U','Risiko berat badan lebih','H1',0.07000,'2026-05-17 09:11:07','2026-05-17 09:11:07'),(11,'BB/U','Risiko berat badan lebih','H2',0.27000,'2026-05-17 09:11:07','2026-05-17 09:11:07'),(12,'BB/U','Risiko berat badan lebih','H3',0.66000,'2026-05-17 09:11:07','2026-05-17 09:11:07'),(13,'TB/U','Sangat pendek','H1',0.80000,'2026-05-17 09:11:07','2026-05-17 09:11:07'),(14,'TB/U','Sangat pendek','H2',0.18000,'2026-05-17 09:11:07','2026-05-17 09:11:07'),(15,'TB/U','Sangat pendek','H3',0.02000,'2026-05-17 09:11:07','2026-05-17 09:11:07'),(16,'TB/U','Pendek','H1',0.24000,'2026-05-17 09:11:07','2026-05-17 09:11:07'),(17,'TB/U','Pendek','H2',0.68000,'2026-05-17 09:11:07','2026-05-17 09:11:07'),(18,'TB/U','Pendek','H3',0.08000,'2026-05-17 09:11:07','2026-05-17 09:11:07'),(19,'TB/U','Normal','H1',0.03000,'2026-05-17 09:11:07','2026-05-17 09:11:07'),(20,'TB/U','Normal','H2',0.11000,'2026-05-17 09:11:07','2026-05-17 09:11:07'),(21,'TB/U','Normal','H3',0.86000,'2026-05-17 09:11:07','2026-05-17 09:11:07'),(22,'TB/U','Tinggi','H1',0.04000,'2026-05-17 09:11:07','2026-05-17 09:11:07'),(23,'TB/U','Tinggi','H2',0.14000,'2026-05-17 09:11:07','2026-05-17 09:11:07'),(24,'TB/U','Tinggi','H3',0.82000,'2026-05-17 09:11:07','2026-05-17 09:11:07'),(25,'BB/TB','Gizi buruk','H1',0.75000,'2026-05-17 09:11:07','2026-05-17 09:11:07'),(26,'BB/TB','Gizi buruk','H2',0.22000,'2026-05-17 09:11:07','2026-05-17 09:11:07'),(27,'BB/TB','Gizi buruk','H3',0.03000,'2026-05-17 09:11:07','2026-05-17 09:11:07'),(28,'BB/TB','Gizi kurang','H1',0.25000,'2026-05-17 09:11:07','2026-05-17 09:11:07'),(29,'BB/TB','Gizi kurang','H2',0.66000,'2026-05-17 09:11:07','2026-05-17 09:11:07'),(30,'BB/TB','Gizi kurang','H3',0.09000,'2026-05-17 09:11:07','2026-05-17 09:11:07'),(31,'BB/TB','Gizi baik','H1',0.03000,'2026-05-17 09:11:07','2026-05-17 09:11:07'),(32,'BB/TB','Gizi baik','H2',0.11000,'2026-05-17 09:11:07','2026-05-17 09:11:07'),(33,'BB/TB','Gizi baik','H3',0.86000,'2026-05-17 09:11:07','2026-05-17 09:11:07'),(34,'BB/TB','Gizi lebih','H1',0.08000,'2026-05-17 09:11:07','2026-05-17 09:11:07'),(35,'BB/TB','Gizi lebih','H2',0.27000,'2026-05-17 09:11:07','2026-05-17 09:11:07'),(36,'BB/TB','Gizi lebih','H3',0.65000,'2026-05-17 09:11:07','2026-05-17 09:11:07'),(37,'BB/TB','Berisiko gizi lebih','H1',0.06000,'2026-05-17 16:25:21','2026-05-17 16:25:21'),(38,'BB/TB','Berisiko gizi lebih','H2',0.22000,'2026-05-17 16:25:21','2026-05-17 16:25:21'),(39,'BB/TB','Berisiko gizi lebih','H3',0.72000,'2026-05-17 16:25:21','2026-05-17 16:25:21');
/*!40000 ALTER TABLE `tb_teorema_bayes_likelihood` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-24 23:15:24
