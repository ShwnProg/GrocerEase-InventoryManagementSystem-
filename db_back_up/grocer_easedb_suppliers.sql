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
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suppliers` (
  `supplier_id_pk` int NOT NULL AUTO_INCREMENT,
  `supplier_name` varchar(250) NOT NULL,
  `contact_person` varchar(20) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `email` varchar(250) NOT NULL,
  `address` varchar(250) NOT NULL,
  `company_name` varchar(250) NOT NULL,
  `is_deleted` tinyint DEFAULT '0',
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`supplier_id_pk`),
  KEY `idx_supplier_name` (`supplier_name`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (60,'Metro Foods Trading','Juan Dela Cruz','09171234567','metrofoods@gmail.com','Manila, Philippines','Metro Foods Trading Co.',0,NULL),(61,'Island Supply Co','Maria Santos','09181234567','islandsupply@gmail.com','Cebu, Philippines','Island Supply Co',0,'2026-04-26 19:44:22'),(62,'FreshMart Distributors','Pedro Reyes','09192345678','freshmart@gmail.com','Davao, Philippines','FreshMart Distributors',0,NULL),(63,'Sunrise Wholesale','Ana Lim','09201234567','sunrise@gmail.com','Quezon City, Philippines','Sunrise Wholesale',0,NULL),(64,'Pacific Goods Inc','Jose Tan','09212345678','pacificgoods@gmail.com','Iloilo, Philippines','Pacific Goods Inc',0,NULL),(65,'Golden Harvest Supplies','Liza Gomez','09223456789','goldenharvest@gmail.com','Bacolod, Philippines','Golden Harvest Supplies',0,NULL),(66,'Prime Select Trading','Mark Villanueva','09234567890','primeselect@gmail.com','Laguna, Philippines','Prime Select Trading',1,'2026-04-27 00:21:59'),(67,'Evergreen Distribution','Carla Ramos','09245678901','evergreen@gmail.com','Pampanga, Philippines','Evergreen Distribution',1,'2026-04-27 00:15:44'),(68,'Blue Ocean Supplies','Eric Bautista','09256789012','sdf@gmail.com','Batangas, Philippines','Blue Ocean Supplies',1,'2026-04-27 00:04:58'),(69,'Sunset Traders','Ramon Diaz','09267890123','sunset@gmail.com','Bohol, Philippines','Sunset Traders',1,'2026-04-26 23:46:24'),(71,'Blue Ocean Supplies','Shawn geroso','09705641607','galdoshawn24@gmail.com','Purok Escano Flordeliz','None',0,NULL);
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
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
