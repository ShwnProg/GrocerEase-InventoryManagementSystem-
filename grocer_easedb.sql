-- MySQL dump 10.13  Distrib 8.0.44, for Win64 (x86_64)
--
-- Host: localhost    Database: grocer_easedb
-- ------------------------------------------------------
-- Server version	8.0.44

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
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `category_id_pk` int NOT NULL AUTO_INCREMENT,
  `category_name` varchar(250) NOT NULL,
  `category_description` longtext,
  `is_deleted` tinyint DEFAULT '0',
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`category_id_pk`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (2,'Dairy','Milk, cheese, and dairy products',1,'2026-04-21 22:11:02'),(3,'Grains','Rice, wheat, and grain products',1,'2026-04-21 22:11:02'),(4,'Canned Goods','Preserved and canned food items',1,'2026-04-24 01:57:46'),(5,'Oils','Cooking oils and relate',1,'2026-04-24 01:57:13'),(6,'Yogurt','',1,'2026-04-21 22:11:02'),(8,'8985','',1,'2026-04-24 01:14:55'),(9,'Bread','',1,'2026-04-21 22:11:02'),(10,'Grains','',1,'2026-04-21 22:11:02'),(11,'Qwewqe','',1,'2026-04-24 01:57:09'),(12,'Yguy','',1,'2026-04-24 01:59:23'),(13,'Bread','',1,'2026-04-24 01:58:16'),(14,'Grains','',1,'2026-04-24 01:59:19'),(15,'Bread','',0,NULL),(16,'Grains','',0,NULL),(17,'Oils','',1,'2026-04-24 02:01:51'),(18,'Dairy','',0,NULL),(19,'Vegetables','',0,NULL),(20,'Fruits','',0,NULL);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

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
  KEY `product_id_fk` (`product_id_fk`),
  KEY `supplier_id_fk` (`supplier_id_fk`),
  CONSTRAINT `product_supplier_ibfk_1` FOREIGN KEY (`product_id_fk`) REFERENCES `products` (`product_id_pk`),
  CONSTRAINT `product_supplier_ibfk_2` FOREIGN KEY (`supplier_id_fk`) REFERENCES `suppliers` (`supplier_id_pk`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_supplier`
--

LOCK TABLES `product_supplier` WRITE;
/*!40000 ALTER TABLE `product_supplier` DISABLE KEYS */;
INSERT INTO `product_supplier` VALUES (8,NULL,NULL,80.00,0),(10,NULL,3,30.00,1),(50,NULL,4,200.00,0),(51,NULL,3,250.00,1),(52,NULL,NULL,200.00,1),(53,NULL,2,200.00,0),(54,NULL,NULL,250.00,0),(55,NULL,4,200.00,1),(62,58,5,250.00,1),(63,57,3,200.00,1);
/*!40000 ALTER TABLE `product_supplier` ENABLE KEYS */;
UNLOCK TABLES;

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
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id_fk`) REFERENCES `categories` (`category_id_pk`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (34,'Century tuna','',50.00,1,0,NULL,'2026-04-21 22:03:08'),(57,'Oreo','',120.00,1,0,18,NULL),(58,'Apple','',120.00,1,0,20,NULL),(59,'Oatmeal','',140.00,1,1,16,'2026-04-24 02:41:03'),(60,'Oreo','',12.00,1,1,19,'2026-04-24 03:02:37');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

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
  KEY `fk_stock_movements_product` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_movements`
--

LOCK TABLES `stock_movements` WRITE;
/*!40000 ALTER TABLE `stock_movements` DISABLE KEYS */;
INSERT INTO `stock_movements` VALUES (5,2,'IN','STK242','2026-04-18','',48),(6,5,'IN','STK981','2026-04-18','Dumating si superman',48),(7,2,'IN','STK034','2026-04-18','New oil supplier',49),(8,12,'IN','STK677','2026-04-18','',49),(9,2,'IN','STK458','2026-04-18','',10),(10,2,'IN','STK540','2026-04-18','',10),(11,2,'IN','STK900','2026-04-18','',10),(12,100,'IN','STK918','2026-04-18','',48),(13,100,'IN','STK084','2026-04-18','',50),(14,500,'IN','STK334','2026-04-18','New supply',44),(15,2,'IN','STK057','2026-04-18','',19),(16,10,'IN','STK835','2026-04-19','',49),(17,2,'IN','STK931','2026-04-19','',50),(18,2,'IN','STK228','2026-04-19','',14),(19,2,'OUT','STK892','2026-04-19','',50),(20,23,'OUT','STK275','2026-04-19','',50),(21,2,'OUT','STK740','2026-04-19','',50),(22,2,'IN','STK029','2026-04-19','',50),(23,2,'IN','STK208','2026-04-19','',50),(24,2,'OUT','STK455','2026-04-19','',50),(25,2,'IN','STK094','2026-04-19','',50),(26,2,'OUT','STK862','2026-04-19','',50),(27,100,'IN','STK612','2026-04-19','',50),(28,100,'OUT','STK987','2026-04-19','',50),(29,2,'IN','STK993','2026-04-19','',50),(30,70,'OUT','STK937','2026-04-19','',50),(31,2,'OUT','STK339','2026-04-19','',50),(32,10,'OUT','STK700','2026-04-19','',50),(33,2,'OUT','STK388','2026-04-19','',50),(34,2,'OUT','STK619','2026-04-19','',50),(35,3,'OUT','STK490','2026-04-19','',50),(36,2,'IN','STK600','2026-04-19','',50),(37,1,'OUT','STK500','2026-04-19','',50),(38,100,'IN','STK693','2026-04-19','',50),(39,100,'IN','STK399','2026-04-19','New delivery',44),(40,2,'IN','STK081','2026-04-19','',50),(41,2,'OUT','STK484','2026-04-19','',50),(42,2,'IN','STK934','2026-04-19','',50),(43,2,'OUT','STK041','2026-04-19','',50),(44,2,'IN','STK469','2026-04-19','',50),(45,2,'IN','STK650','2026-04-19','',51),(46,2,'IN','STK966','2026-04-21','',51),(47,2,'IN','STK202','2026-04-21','',51),(48,2,'IN','STK467','2026-04-21','',51),(49,8,'OUT','STK573','2026-04-21','',51),(50,40,'IN','STK517','2026-04-21','',53),(51,2,'IN','STK947','2026-04-21','',53),(52,2,'IN','STK187','2026-04-21','Asdasasd',11),(53,100,'IN','STK561','2026-04-21','',20),(54,10000000000,'IN','STK073','2026-04-22','',50),(55,10000000000,'OUT','STK752','2026-04-22','',50),(56,2,'IN','STK921','2026-04-23','',31),(57,2,'IN','STK410','2026-04-23','',59),(58,2,'IN','STK157','2026-04-23','',57),(59,2,'IN','STK527','2026-04-23','',58),(60,2,'OUT','STK390','2026-04-23','',58),(61,2,'IN','STK848','2026-04-23','',57),(62,2,'IN','STK098','2026-04-23','',57);
/*!40000 ALTER TABLE `stock_movements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stocks`
--

DROP TABLE IF EXISTS `stocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stocks` (
  `stock_id_pk` int NOT NULL AUTO_INCREMENT,
  `product_id_fk` int NOT NULL,
  `quantity` bigint DEFAULT '0',
  `last_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`stock_id_pk`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stocks`
--

LOCK TABLES `stocks` WRITE;
/*!40000 ALTER TABLE `stocks` DISABLE KEYS */;
INSERT INTO `stocks` VALUES (37,34,0,'2026-04-18 12:31:18'),(51,52,0,'2026-04-20 18:41:27'),(56,57,6,'2026-04-23 10:51:56'),(57,58,0,'2026-04-23 10:51:41'),(58,59,2,'2026-04-23 10:37:46'),(59,60,0,'2026-04-23 11:02:09');
/*!40000 ALTER TABLE `stocks` ENABLE KEYS */;
UNLOCK TABLES;

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
  PRIMARY KEY (`supplier_id_pk`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (2,'Negros Fresh','Maria Santos','09281234567','maria@negrosfresh.com','Silay City','Negros Fresh Supply Inc.',0,'2026-04-24 02:12:00'),(3,'Visayas Agro Suppliers','Pedro Reyes','09391234567','pedro@visayasagro.com','Talisay City','Visayas Agro Co.',0,NULL),(4,'Green Harvest Trading','','','','','Green Harvest Trading Corp.',0,'2026-04-22 23:09:35'),(5,'Island Food Distributors','Mark Cruz','09561234567','mark@islandfood.com','Bacolod City','Island Food Distributors Ltd.',1,'2026-04-24 02:41:54'),(6,'Juan Dela Cruz Trading','Shawn geroso','09705641607','galdoshawn24@gmail.com','Purok Escano Flordeliz','',0,'2026-04-24 02:10:58');
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `role` enum('admin') DEFAULT 'admin',
  `profile_picture` blob,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (4,'shawn','$2y$12$DaFV.d.7ZSKGmQXF5oVHj.T5f5Wxm0coiTeqNW7f3jA.58mcELYvi','admin@gmail.com','+639705641607','admin',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-24  3:08:38
