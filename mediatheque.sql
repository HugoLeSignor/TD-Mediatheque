-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 06, 2026 at 10:29 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mediatheque`
--

-- --------------------------------------------------------

--
-- Table structure for table `fiche_film`
--

DROP TABLE IF EXISTS `fiche_film`;
CREATE TABLE IF NOT EXISTS `fiche_film` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(200) NOT NULL,
  `realisateur` varchar(100) NOT NULL,
  `genre` varchar(50) NOT NULL,
  `duree` int NOT NULL,
  `synopsis` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `user_id` int NOT NULL,
  `date_ajout` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `fiche_film`
--

INSERT INTO `fiche_film` (`id`, `titre`, `realisateur`, `genre`, `duree`, `synopsis`, `image`, `user_id`, `date_ajout`) VALUES
(2, 'Interstellar', 'Christopher Nolan', 'Sci-Fi', 169, 'When Earth becomes uninhabitable in the future, a farmer and ex-NASA pilot, Joseph Cooper, is tasked to pilot a spacecraft, along with a team of researchers, to find a new planet for humans.', '695cdf75c4a36.jpg', 1, '2026-01-06 10:09:57'),
(3, 'Tenet', 'Christopher Nolan', 'Sci-Fi', 150, 'When a few objects that can be manipulated and used as weapons in the future fall into the wrong hands, a CIA operative, known as the Protagonist, must save the world.', '695cdfa32c0b7.webp', 1, '2026-01-06 10:10:43'),
(4, 'A Minecraft Movie', 'Jared Hess', 'Action', 101, 'A mysterious portal pulls four misfits into the Overworld, a bizarre, cubic wonderland that thrives on imagination. To get back home, they\'ll have to master the terrain while embarking on a magical quest with an unexpected crafter named Steve.', '695cdfe6c7585.jpg', 1, '2026-01-06 10:11:50'),
(5, 'Avatar', 'James Cameron', 'Sci-Fi', 162, 'Jake, a paraplegic marine, replaces his brother on the Na\'vi-inhabited Pandora for a corporate mission. He is accepted by the natives as one of their own, but he must decide where his loyalties lie.', '695ce09394090.jpg', 1, '2026-01-06 10:14:43');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `nom`, `prenom`, `password`) VALUES
(1, 'Le Signor', 'Hugo', '$2y$10$3Sjis3.5s6/wsChkFgIdXO.ePCw5RCOgbFesHfoDQp628LQIlHFWW');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
