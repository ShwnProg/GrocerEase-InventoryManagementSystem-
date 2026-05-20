-- GrocerEaseIMS Backup
-- Database: grocer_easedb
-- Created at: 2026-05-21 01:48:53
-- Backup type: Full database backup

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Table structure for `categories`
--
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `category_id_pk` int NOT NULL AUTO_INCREMENT,
  `category_name` varchar(250) NOT NULL,
  `category_description` longtext,
  `is_deleted` tinyint DEFAULT '0',
  `deleted_at` datetime DEFAULT NULL,
  `status` tinyint DEFAULT '1',
  PRIMARY KEY (`category_id_pk`),
  KEY `idx_category_name` (`category_name`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Data for `categories`
--
INSERT INTO `categories` (`category_id_pk`,`category_name`,`category_description`,`is_deleted`,`deleted_at`,`status`) VALUES
(88,'Rice & Grains','Daily staple grains and rice products',0,'2026-04-26 19:44:31',1),
(89,'Canned Goods','Preserved food items in cans',0,NULL,1),
(90,'Beverages','Drinks and refreshments',0,NULL,1),
(91,'Snacks','Chips, biscuits, and snack items',0,'2026-04-27 12:44:39',1),
(92,'Dairy Products','Milk, cheese, and dairy-based products',0,NULL,1),
(101,'Oils','',0,NULL,1);

--
-- Table structure for `product_supplier`
--
DROP TABLE IF EXISTS `product_supplier`;
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
) ENGINE=InnoDB AUTO_INCREMENT=119 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Data for `product_supplier`
--
INSERT INTO `product_supplier` (`product_supplier_id_pk`,`product_id_fk`,`supplier_id_fk`,`cost_price`,`preferred`) VALUES
(8,NULL,NULL,'80.00',0),
(10,NULL,NULL,'30.00',1),
(50,NULL,NULL,'200.00',0),
(51,NULL,NULL,'250.00',1),
(52,NULL,NULL,'200.00',1),
(53,NULL,NULL,'200.00',0),
(54,NULL,NULL,'250.00',0),
(55,NULL,NULL,'200.00',1),
(68,NULL,NULL,'250.00',0),
(72,NULL,NULL,'4.00',0),
(74,NULL,NULL,'200.00',1),
(75,102,NULL,'45.50',1),
(76,103,NULL,'56.00',1),
(77,104,NULL,'31.50',1),
(78,105,63,'21.00',1),
(79,106,NULL,'49.00',1),
(80,107,62,'66.50',1),
(81,108,62,'17.50',1),
(82,109,NULL,'38.50',1),
(83,110,65,'63.00',0),
(84,111,NULL,'84.00',1),
(85,112,60,'126.00',1),
(86,113,60,'175.00',1),
(87,114,65,'42.00',0),
(88,115,NULL,'38.50',1),
(89,116,62,'52.50',1),
(107,121,65,'200.00',0),
(108,121,NULL,'23.00',0),
(109,121,64,'250.00',0),
(112,121,NULL,'250.00',0);

--
-- Table structure for `products`
--
DROP TABLE IF EXISTS `products`;
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
) ENGINE=InnoDB AUTO_INCREMENT=127 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Data for `products`
--
INSERT INTO `products` (`product_id_pk`,`product_name`,`product_description`,`selling_price`,`status`,`is_deleted`,`category_id_fk`,`deleted_at`) VALUES
(102,'Jasmine Rice 1kg','Premium fragrant rice','65.00',1,0,NULL,NULL),
(103,'Brown Rice 1kg','Healthy whole grain rice','80.00',1,0,NULL,NULL),
(104,'Canned Tuna Flakes','Ready-to-eat tuna flakes','45.00',1,0,89,NULL),
(105,'Canned Sardines','Tomato sauce sardines','30.00',1,0,89,NULL),
(106,'Cola 1.5L','Carbonated soft drink','70.00',1,0,90,NULL),
(107,'Orange Juice 1L','Fresh fruit juice','95.00',1,0,90,NULL),
(108,'Potato Chips Classic','Crunchy salted chips','25.00',1,0,NULL,NULL),
(109,'Chocolate Cookies','Sweet chocolate biscuits','55.00',1,0,NULL,NULL),
(110,'Fresh Milk 1L','Pasteurized fresh milk','90.00',1,0,NULL,NULL),
(111,'Cheddar Cheese 200g','Processed cheese block','120.00',1,0,NULL,NULL),
(112,'Chicken Breast 1kg','Fresh poultry meat','180.00',1,0,NULL,NULL),
(113,'Pork Belly 1kg','Fresh pork cut','250.00',1,0,NULL,NULL),
(114,'Soy Sauce 1L','All-purpose seasoning sauce','60.00',1,0,90,NULL),
(115,'Vinegar 1L','Distilled white vinegar','55.00',1,1,NULL,'2026-05-21 00:49:37'),
(116,'Toothpaste 100ml','Mint flavored toothpaste','75.00',1,1,NULL,'2026-05-21 00:49:33'),
(121,'Frozen Nuggets 500g','Chicken nuggets pack','160.00',1,1,NULL,'2026-05-21 00:49:28');

--
-- Table structure for `stock_movements`
--
DROP TABLE IF EXISTS `stock_movements`;
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
) ENGINE=InnoDB AUTO_INCREMENT=156 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Data for `stock_movements`
--
INSERT INTO `stock_movements` (`movement_id_pk`,`quantity`,`reference_type`,`reference_id`,`date`,`reason`,`product_id`) VALUES
(82,50,'IN','STK0001','2026-04-26','Initial Stock Load',102),
(83,50,'IN','STK0002','2026-04-26','Initial Stock Load',103),
(84,50,'IN','STK0003','2026-04-26','Initial Stock Load',104),
(85,50,'IN','STK0004','2026-04-26','Initial Stock Load',105),
(86,50,'IN','STK0005','2026-04-26','Initial Stock Load',106),
(87,50,'IN','STK0006','2026-04-26','Initial Stock Load',107),
(88,50,'IN','STK0007','2026-04-26','Initial Stock Load',108),
(89,50,'IN','STK0008','2026-04-26','Initial Stock Load',109),
(90,50,'IN','STK0009','2026-04-26','Initial Stock Load',110),
(91,50,'IN','STK0010','2026-04-26','Initial Stock Load',111),
(92,50,'IN','STK0011','2026-04-26','Initial Stock Load',112),
(93,50,'IN','STK0012','2026-04-26','Initial Stock Load',113),
(94,50,'IN','STK0013','2026-04-26','Initial Stock Load',114),
(95,50,'IN','STK0014','2026-04-26','Initial Stock Load',115),
(96,50,'IN','STK0015','2026-04-26','Initial Stock Load',116),
(97,50,'IN','STK0016','2026-04-26','Initial Stock Load',117),
(98,50,'IN','STK0017','2026-04-26','Initial Stock Load',118),
(99,50,'IN','STK0018','2026-04-26','Initial Stock Load',119),
(100,50,'IN','STK0019','2026-04-26','Initial Stock Load',120),
(101,50,'IN','STK0020','2026-04-26','Initial Stock Load',121),
(113,50,'IN','STK907','2026-04-26','',121),
(114,20,'IN','STK772','2026-04-26','New delivery',119),
(115,2,'IN','STK656','2026-04-26','',121),
(116,2,'OUT','STK107','2026-04-19','',105),
(117,2,'IN','STK851','2026-04-26','',118),
(118,2,'IN','STK112','2026-04-26','',118),
(119,2,'OUT','STK854','2026-04-26','',119),
(120,2,'OUT','STK397','2026-04-26','',117),
(121,2,'OUT','STK184','2026-04-26','',119),
(122,2,'OUT','STK522','2026-04-26','',121),
(123,2,'IN','STK806','2026-04-26','',121),
(124,2,'OUT','STK694','2026-04-26','',121),
(125,2,'IN','STK345','2026-04-26','',122),
(126,2,'OUT','STK481','2026-04-26','',122),
(127,10,'IN','STK270','2026-04-26','',122),
(128,1,'IN','STK452','2026-04-26','',122),
(129,2,'IN','STK272','2026-04-26','',121),
(130,3,'OUT','STK680','2026-04-26','',119),
(131,3,'OUT','STK384','2026-04-26','',119),
(132,3,'OUT','STK689','2026-04-26','',119),
(133,23,'IN','STK234','2026-04-26','',123),
(134,100,'IN','STK917','2026-04-26','',121),
(135,2,'IN','STK508','2026-04-26','',121),
(136,3,'OUT','STK719','2026-04-26','',121),
(137,50,'OUT','STK123','2026-04-27','',114),
(138,50,'OUT','STK429','2026-04-27','',107),
(139,10,'OUT','STK366','2026-04-27','',105),
(140,50,'IN','STK634','2026-04-27','',124),
(141,50,'OUT','STK594','2026-04-27','',124),
(142,50,'OUT','STK473','2026-04-27','',110);
INSERT INTO `stock_movements` (`movement_id_pk`,`quantity`,`reference_type`,`reference_id`,`date`,`reason`,`product_id`) VALUES
(143,20,'OUT','STK526','2026-04-27','',112),
(144,10,'IN','STK205','2026-04-27','',124),
(145,120,'IN','STK517','2026-04-27','',110),
(146,2,'OUT','STK520','2026-04-27','',124),
(147,30,'IN','STK149','2026-04-27','',119),
(148,20,'OUT','STK600','2026-04-27','',117),
(149,100,'OUT','STK228','2026-04-27','',110),
(150,1,'IN','STK591','2026-05-07','',124),
(151,2,'IN','STK595','2026-05-07','',124),
(152,2,'OUT','STK031','2026-05-07','',124),
(153,2,'IN','STK086','2026-05-07','',119),
(154,2,'IN','STK868','2026-05-07','',119),
(155,100,'IN','STK925','2026-05-07','',126);

--
-- Table structure for `stocks`
--
DROP TABLE IF EXISTS `stocks`;
CREATE TABLE `stocks` (
  `stock_id_pk` int NOT NULL AUTO_INCREMENT,
  `product_id_fk` int NOT NULL,
  `quantity` bigint DEFAULT '0',
  `last_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`stock_id_pk`),
  KEY `idx_stock_product` (`product_id_fk`)
) ENGINE=InnoDB AUTO_INCREMENT=128 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Data for `stocks`
--
INSERT INTO `stocks` (`stock_id_pk`,`product_id_fk`,`quantity`,`last_updated`) VALUES
(37,34,2,'2026-04-28 11:06:44'),
(51,52,0,'2026-04-23 10:41:27'),
(58,59,104,'2026-04-28 11:06:51'),
(61,82,50,'2026-04-28 08:00:00'),
(62,83,50,'2026-04-28 08:00:00'),
(63,84,50,'2026-04-28 08:00:00'),
(64,85,50,'2026-04-28 08:00:00'),
(65,86,50,'2026-04-28 08:00:00'),
(66,87,50,'2026-04-28 08:00:00'),
(67,88,50,'2026-04-28 08:00:00'),
(68,89,50,'2026-04-28 08:00:00'),
(69,90,50,'2026-04-28 08:00:00'),
(70,91,50,'2026-04-28 08:00:00'),
(71,92,50,'2026-04-28 08:00:00'),
(72,93,50,'2026-04-28 08:00:00'),
(73,94,50,'2026-04-28 08:00:00'),
(74,95,50,'2026-04-28 08:00:00'),
(75,96,50,'2026-04-28 08:00:00'),
(76,97,50,'2026-04-28 08:00:00'),
(77,98,50,'2026-04-28 08:00:00'),
(78,99,50,'2026-04-28 08:00:00'),
(79,100,50,'2026-04-28 08:00:00'),
(80,101,50,'2026-04-28 08:00:00'),
(92,102,50,'2026-04-28 08:00:00'),
(93,103,50,'2026-04-28 08:00:00'),
(94,104,50,'2026-04-28 08:00:00'),
(95,105,38,'2026-04-29 12:14:05'),
(96,106,50,'2026-04-28 08:00:00'),
(97,107,0,'2026-04-29 11:45:39'),
(98,108,50,'2026-04-28 08:00:00'),
(99,109,50,'2026-04-28 08:00:00'),
(100,110,20,'2026-04-29 22:20:04'),
(101,111,50,'2026-04-28 08:00:00'),
(102,112,30,'2026-04-29 12:33:38'),
(103,113,50,'2026-04-28 08:00:00'),
(104,114,0,'2026-04-29 11:45:18'),
(105,115,50,'2026-04-28 08:00:00'),
(106,116,50,'2026-04-28 08:00:00'),
(111,121,201,'2026-04-29 00:09:56');

--
-- Table structure for `suppliers`
--
DROP TABLE IF EXISTS `suppliers`;
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
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Data for `suppliers`
--
INSERT INTO `suppliers` (`supplier_id_pk`,`supplier_name`,`contact_person`,`phone_number`,`email`,`address`,`company_name`,`is_deleted`,`deleted_at`) VALUES
(60,'Metro Foods Trading','Juan Dela Cruz','09171234567','metrofoods@gmail.com','Manila, Philippines','Metro Foods Trading Co.',0,NULL),
(61,'Island Supply Co','Maria Santos','09181234567','islandsupply@gmail.com','Cebu, Philippines','Island Supply Co',0,'2026-04-26 19:44:22'),
(62,'FreshMart Distributors','Pedro Reyes','09192345678','freshmart@gmail.com','Davao, Philippines','FreshMart Distributors',0,NULL),
(63,'Sunrise Wholesale','Ana Lim','09201234567','sunrise@gmail.com','Quezon City, Philippines','Sunrise Wholesale',0,NULL),
(64,'Pacific Goods Inc','Jose Tan','09212345678','pacificgoods@gmail.com','Iloilo, Philippines','Pacific Goods Inc',0,NULL),
(65,'Golden Harvest Supplies','Liza Gomez','09223456789','goldenharvest@gmail.com','Bacolod, Philippines','Golden Harvest Supplies',0,NULL),
(66,'Prime Select Trading','Mark Villanueva','09234567890','primeselect@gmail.com','Laguna, Philippines','Prime Select Trading',0,NULL),
(67,'Evergreen Distribution','Carla Ramos','09245678901','evergreen@gmail.com','Pampanga, Philippines','Evergreen Distribution',1,'2026-04-27 00:15:44'),
(68,'Blue Ocean Supplies','Eric Bautista','09256789012','sdf@gmail.com','Batangas, Philippines','Blue Ocean Supplies',1,'2026-04-27 00:04:58'),
(69,'Sunset Traders','Ramon Diaz','09267890123','sunset@gmail.com','Bohol, Philippines','Sunset Traders',1,'2026-04-26 23:46:24'),
(71,'Blue Ocean Supplies','Shawn geroso','09705641607','galdoshawn24@gmail.com','Purok Escano Flordeliz','None',1,'2026-05-20 20:19:37');

--
-- Table structure for `users`
--
DROP TABLE IF EXISTS `users`;
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

--
-- Data for `users`
--
INSERT INTO `users` (`user_id`,`username`,`password`,`email`,`contact_number`,`role`,`profile_picture`) VALUES
(4,'shawn','$2y$12$uctxjJ4RNncW.khaXyOW2OlgG18BYHs1v3Vyu11Feiwy5OlRc5/5i','shawn@gmail.com','+639705641607','admin',NULL);

SET FOREIGN_KEY_CHECKS=1;
