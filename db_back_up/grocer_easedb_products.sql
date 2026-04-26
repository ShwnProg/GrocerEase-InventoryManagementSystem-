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
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `product_id_pk` int NOT NULL AUTO_INCREMENT,
  `product_name` varchar(250) NOT NULL,
  `product_description` longtext,
  `selling_price` decimal(10,2) NOT NULL,
  `status` tinyint DEFAULT '1',
  `is_deleted` tinyint DEFAULT '0',
  `category_id_fk` int DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`product_id_pk`),
  KEY `category_id_fk` (`category_id_fk`),
  KEY `idx_product_name` (`product_name`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id_fk`) REFERENCES `categories` (`category_id_pk`)
) ENGINE=InnoDB AUTO_INCREMENT=124 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (102,'Jasmine Rice 1kg','Premium fragrant rice',65.00,1,0,NULL,NULL),(103,'Brown Rice 1kg','Healthy whole grain rice',80.00,1,0,NULL,NULL),(104,'Canned Tuna Flakes','Ready-to-eat tuna flakes',45.00,1,0,89,NULL),(105,'Canned Sardines','Tomato sauce sardines',30.00,1,0,89,NULL),(106,'Cola 1.5L','Carbonated soft drink',70.00,1,0,90,NULL),(107,'Orange Juice 1L','Fresh fruit juice',95.00,1,0,90,NULL),(108,'Potato Chips Classic','Crunchy salted chips',25.00,1,0,91,NULL),(109,'Chocolate Cookies','Sweet chocolate biscuits',55.00,1,0,91,NULL),(110,'Fresh Milk 1L','Pasteurized fresh milk',90.00,1,0,92,NULL),(111,'Cheddar Cheese 200g','Processed cheese block',120.00,1,0,92,NULL),(112,'Chicken Breast 1kg','Fresh poultry meat',180.00,1,0,93,NULL),(113,'Pork Belly 1kg','Fresh pork cut',250.00,1,0,93,NULL),(114,'Soy Sauce 1L','All-purpose seasoning sauce',60.00,1,0,NULL,NULL),(115,'Vinegar 1L','Distilled white vinegar',55.00,1,0,NULL,NULL),(116,'Toothpaste 100ml','Mint flavored toothpaste',75.00,1,0,NULL,NULL),(117,'Bath Soap 125g','Mild scented soap bar',35.00,1,0,NULL,NULL),(118,'Laundry Powder 1kg','Powerful cleaning detergent',150.00,1,1,NULL,'2026-04-27 00:22:28'),(119,'Dishwashing Liquid 1L','Grease removing liquid',85.00,1,0,NULL,NULL),(121,'Frozen Nuggets 500g','Chicken nuggets pack',160.00,1,1,NULL,'2026-04-27 00:14:55'),(123,'Oreo','',140.00,1,1,NULL,'2026-04-27 00:05:28');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
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
