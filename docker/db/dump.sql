-- MySQL dump 10.13  Distrib 8.0.36, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: soktestapp
-- ------------------------------------------------------
-- Server version	5.5.5-10.6.18-MariaDB-0ubuntu0.22.04.1

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
-- Table structure for table `sections`
--

DROP TABLE IF EXISTS `sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sections` (
  `id` char(36) NOT NULL,
  `parent_id` char(36) DEFAULT NULL,
  `slug` varchar(45) NOT NULL,
  `title` varchar(45) NOT NULL,
  `text` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `code_UNIQUE` (`slug`),
  KEY `fk_parent_idx` (`parent_id`,`id`),
  CONSTRAINT `fk_parent` FOREIGN KEY (`parent_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sections`
--

LOCK TABLES `sections` WRITE;
/*!40000 ALTER TABLE `sections` DISABLE KEYS */;
INSERT INTO `sections` VALUES ('3b97c0c1-7e95-4924-98c2-e587da795d28',NULL,'home','Home','<p>This is the Home Page</p>'),('65d7f90c-0fea-43f3-bbe7-9c3371ae3612',NULL,'about-us','About Us','<p>This is About Us Page</p>'),('d145d2b2-b466-47c3-8f72-3051d85f35b2',NULL,'game-news','Game News',''),('f6043505-c6fb-4577-ac3e-3f611bb61ab4','d145d2b2-b466-47c3-8f72-3051d85f35b2','kcd-two-review','Kingdom Come: Deliverance 2 Review','<p class=\"news-deck type--xlarge\">Kingdom Come: Deliverance 2 is a triumphant sequel, improving upon its predecessor with an open-world RPG that delights in its complexity and emphasis on player choice.</p>\r\n<p class=\"news-byline\">By&nbsp;<span class=\"byline-author \"><a class=\"byline-author__name\" href=\"https://www.gamespot.com/profile/richardwakeling/\" rel=\"author\">Richard Wakeling&nbsp;</a></span>&nbsp;on&nbsp;<time datetime=\"2025-02-03T08:00:00-0800\">February 3, 2025 at 8:00AM PST<br><br></time></p>\r\n<p dir=\"ltr\">There\'s a tavern in the town of Troskowitz where the barmaids must be sick at the sight of me. I\'ve been there twice, and both times been involved in messy brawls despite not tasting a lick of alcohol. The second of these fisticuffs was against a small group of Cuman deserters. They seemed nice enough, but I was still wary considering the Cumans are who killed my parents a few months back. I wasn\'t the only one, and after failing to ease the growing tension, I decided to side with the local townspeople when push came to shove.</p>\r\n<p dir=\"ltr\">Needless to say, we didn\'t put up much of a fight. Once the Cumans left, one of the locals implored me to track them down and deal with them once and for all. After eventually tracking them down, the sole Czech-speaking Cuman was so welcoming that I ended up getting drunk with them. I got so inebriated, in fact, that my night revolved around almost drowning, playing matchmaker for one of the soldiers, and then having a conversation with a talking dog that definitely wasn\'t real. This night of revelry would\'ve been fine on any other evening, but I promised two new acquaintances that I\'d be up bright and early to join them on a critical mission. Even after sleeping for seven hours, I was still completely plastered when I arose from my stupor. And let me tell you, fighting bandits with blurred vision, a swaying body, and a soundtrack of frequent farting isn\'t ideal.</p>\r\n<p dir=\"ltr\">To some, this last part might sound utterly infuriating. To me, and others like me, this is part of what makes Kingdom Come: Deliverance 2 utterly enticing. This is a game that sings when you\'re swept up in an entertaining series of events ignited by the consequences of your actions; where the game\'s quest design and emphasis on player choice wonderfully intertwine with its systems-driven sandbox to create a wholly immersive experience. While its 2018 predecessor was often disrupted by technical issues--which were further exacerbated by a frustrating save system--and clunky combat, Kingdom Come 2 refines and improves upon it in every respect. The obtuse nature of some of its RPG systems still won\'t click for everybody, but this is a confident sequel that builds on the foundations established by the original game, presenting a rich and sprawling adventure that effortlessly oscillates between medieval drama and slice-of-life hijinks in a world that feels distinctly alive.</p>\r\n<p class=\"news-byline\"><time datetime=\"2025-02-03T08:00:00-0800\">This article was taken from <a href=\"https://www.gamespot.com/reviews/kingdom-come-deliverance-2-review-whats-old-is-new-again/1900-6418333/\" target=\"_blank\" rel=\"noopener\">https://www.gamespot.com/reviews/kingdom-come-deliverance-2-review-whats-old-is-new-again/1900-6418333/</a></time></p>');
/*!40000 ALTER TABLE `sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` char(36) NOT NULL,
  `username` varchar(45) NOT NULL,
  `password_hash` char(60) NOT NULL,
  `auth_token_hash` char(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `username_UNIQUE` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('22f4be79-e5a7-4a8a-b106-8191056686ad','admin','$2a$12$nbgQKDKCZitYYJDmeDToCOKmbUlXflWniOJIZl678Zx6v0k83pEr2','5060bd311af3161c73cc85403da19711c9e7384fab1d57abe5c6fa952cfa60d2');
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

-- Dump completed on 2025-02-04 19:17:24