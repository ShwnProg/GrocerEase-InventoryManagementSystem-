-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: localhost    Database: grocer_easedb
-- ------------------------------------------------------
-- Server version	8.0.44

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
-- Table structure for table `stock_movements`
--

DROP TABLE IF EXISTS `stock_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_movements` (
  `movement_id_pk` int NOT NULL AUTO_INCREMENT,
  `quantity` bigint NOT NULL,
  `reference_type` varchar(200) DEFAULT NULL,
  `reference_id` varchar(200) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `reason` longtext,
  `product_id` int DEFAULT NULL,
  PRIMARY KEY (`movement_id_pk`),
  KEY `idx_inentory_product` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=137 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_movements`
--

LOCK TABLES `stock_movements` WRITE;
/*!40000 ALTER TABLE `stock_movements` DISABLE KEYS */;
INSERT INTO `stock_movements` VALUES (82,50,'IN','STK0001','2026-04-26','Initial Stock Load',102),(83,50,'IN','STK0002','2026-04-26','Initial Stock Load',103),(84,50,'IN','STK0003','2026-04-26','Initial Stock Load',104),(85,50,'IN','STK0004','2026-04-26','Initial Stock Load',105),(86,50,'IN','STK0005','2026-04-26','Initial Stock Load',106),(87,50,'IN','STK0006','2026-04-26','Initial Stock Load',107),(88,50,'IN','STK0007','2026-04-26','Initial Stock Load',108),(89,50,'IN','STK0008','2026-04-26','Initial Stock Load',109),(90,50,'IN','STK0009','2026-04-26','Initial Stock Load',110),(91,50,'IN','STK0010','2026-04-26','Initial Stock Load',111),(92,50,'IN','STK0011','2026-04-26','Initial Stock Load',112),(93,50,'IN','STK0012','2026-04-26','Initial Stock Load',113),(94,50,'IN','STK0013','2026-04-26','Initial Stock Load',114),(95,50,'IN','STK0014','2026-04-26','Initial Stock Load',115),(96,50,'IN','STK0015','2026-04-26','Initial Stock Load',116),(97,50,'IN','STK0016','2026-04-26','Initial Stock Load',117),(98,50,'IN','STK0017','2026-04-26','Initial Stock Load',118),(99,50,'IN','STK0018','2026-04-26','Initial Stock Load',119),(100,50,'IN','STK0019','2026-04-26','Initial Stock Load',120),(101,50,'IN','STK0020','2026-04-26','Initial Stock Load',121),(113,50,'IN','STK907','2026-04-26','',121),(114,20,'IN','STK772','2026-04-26','New delivery',119),(115,2,'IN','STK656','2026-04-26','',121),(116,2,'OUT','STK107','2026-04-19','',105),(117,2,'IN','STK851','2026-04-26','',118),(118,2,'IN','STK112','2026-04-26','',118),(119,2,'OUT','STK854','2026-04-26','',119),(120,2,'OUT','STK397','2026-04-26','',117),(121,2,'OUT','STK184','2026-04-26','',119),(122,2,'OUT','STK522','2026-04-26','',121),(123,2,'IN','STK806','2026-04-26','',121),(124,2,'OUT','STK694','2026-04-26','',121),(125,2,'IN','STK345','2026-04-26','',122),(126,2,'OUT','STK481','2026-04-26','',122),(127,10,'IN','STK270','2026-04-26','',122),(128,1,'IN','STK452','2026-04-26','',122),(129,2,'IN','STK272','2026-04-26','',121),(130,3,'OUT','STK680','2026-04-26','',119),(131,3,'OUT','STK384','2026-04-26','',119),(132,3,'OUT','STK689','2026-04-26','',119),(133,23,'IN','STK234','2026-04-26','',123),(134,100,'IN','STK917','2026-04-26','',121),(135,2,'IN','STK508','2026-04-26','',121),(136,3,'OUT','STK719','2026-04-26','',121);
/*!40000 ALTER TABLE `stock_movements` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-27  0:49:58
