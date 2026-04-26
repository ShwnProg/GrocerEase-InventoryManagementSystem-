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
-- Table structure for table `product_supplier`
--

DROP TABLE IF EXISTS `product_supplier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_supplier` (
  `product_supplier_id_pk` int NOT NULL AUTO_INCREMENT,
  `product_id_fk` int DEFAULT NULL,
  `supplier_id_fk` int DEFAULT NULL,
  `cost_price` decimal(10,2) NOT NULL,
  `preferred` tinyint DEFAULT '0',
  PRIMARY KEY (`product_supplier_id_pk`),
  KEY `idx_ps_product` (`product_id_fk`),
  KEY `idx_ps_supplier` (`supplier_id_fk`),
  CONSTRAINT `product_supplier_ibfk_1` FOREIGN KEY (`product_id_fk`) REFERENCES `products` (`product_id_pk`),
  CONSTRAINT `product_supplier_ibfk_2` FOREIGN KEY (`supplier_id_fk`) REFERENCES `suppliers` (`supplier_id_pk`)
) ENGINE=InnoDB AUTO_INCREMENT=113 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_supplier`
--

LOCK TABLES `product_supplier` WRITE;
/*!40000 ALTER TABLE `product_supplier` DISABLE KEYS */;
INSERT INTO `product_supplier` VALUES (8,NULL,NULL,80.00,0),(10,NULL,NULL,30.00,1),(50,NULL,NULL,200.00,0),(51,NULL,NULL,250.00,1),(52,NULL,NULL,200.00,1),(53,NULL,NULL,200.00,0),(54,NULL,NULL,250.00,0),(55,NULL,NULL,200.00,1),(68,NULL,NULL,250.00,0),(72,NULL,NULL,4.00,0),(74,NULL,NULL,200.00,1),(75,102,NULL,45.50,1),(76,103,NULL,56.00,1),(77,104,NULL,31.50,1),(78,105,63,21.00,1),(79,106,NULL,49.00,1),(80,107,62,66.50,1),(81,108,62,17.50,1),(82,109,NULL,38.50,1),(83,110,65,63.00,1),(84,111,NULL,84.00,1),(85,112,60,126.00,1),(86,113,60,175.00,1),(87,114,65,42.00,1),(88,115,NULL,38.50,1),(89,116,62,52.50,1),(90,117,NULL,24.50,1),(91,118,63,105.00,1),(92,119,64,59.50,1),(107,121,65,200.00,0),(108,121,NULL,23.00,0),(109,121,64,250.00,0),(111,123,63,250.00,0),(112,121,NULL,250.00,0);
/*!40000 ALTER TABLE `product_supplier` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-27  0:49:57
