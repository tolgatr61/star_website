-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : sam. 28 nov. 2020 à 12:52
-- Version du serveur :  10.4.13-MariaDB
-- Version de PHP : 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `dm`
--

-- --------------------------------------------------------

--
-- Structure de la table `account`
--

DROP TABLE IF EXISTS `account`;
CREATE TABLE IF NOT EXISTS `account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `surname` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `login` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT 'user',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `account`
--

INSERT INTO `account` (`id`, `surname`, `name`, `login`, `password`, `status`) VALUES
(1, 'Pascal', 'Vanier', 'vanier', '$2y$10$PKqushSLrDJS/ovnMwVLged3KXNK4Zd89fsThs/4YBsbN2aedn/Nq', 'user'),
(2, 'Jean-Marc', 'Lecarpentier', 'lecarpentier', '$2y$10$PKqushSLrDJS/ovnMwVLged3KXNK4Zd89fsThs/4YBsbN2aedn/Nq', 'user'),
(3, 'Jean', 'Toto', 'admin', '$2y$10$PKqushSLrDJS/ovnMwVLged3KXNK4Zd89fsThs/4YBsbN2aedn/Nq', 'admin');

-- --------------------------------------------------------

--
-- Structure de la table `cookie`
--

DROP TABLE IF EXISTS `cookie`;
CREATE TABLE IF NOT EXISTS `cookie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lifetime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `cookie`
--

INSERT INTO `cookie` (`id`, `lifetime`) VALUES
(1, 3600);

-- --------------------------------------------------------

--
-- Structure de la table `image`
--

DROP TABLE IF EXISTS `image`;
CREATE TABLE IF NOT EXISTS `image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `urlName` varchar(255) DEFAULT NULL,
  `starId` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `image`
--

INSERT INTO `image` (`id`, `urlName`, `starId`) VALUES
(1, './upload/sirius.jpg', 1),
(2, './upload/sirius2.jpg', 1),
(3, './upload/sirius3.png', 1),
(4, './upload/canopus.jpg', 2),
(5, './upload/canopus2.jpg', 2),
(6, './upload/canopus3.jpg', 2),
(7, './upload/canopus4.jpg', 2),
(8, './upload/arcturus.jpg', 3),
(9, './upload/arcturus2.jpg', 3),
(10, './upload/centaurus.png', 4),
(11, './upload/rigil.jpg', 4),
(12, './upload/vega.jpg', 5),
(15, './upload/procyon.png', 7),
(16, './upload/achernar.png', 8),
(17, './upload/achernar2.png', 8),
(18, './upload/rigel.jpg', 6),
(19, './upload/rigel2.jpg', 6),
(20, './upload/betelgeuse.png', 9),
(21, './upload/betelgeuse2.jpg', 9),
(22, './upload/hadar.jpg', 10),
(23, './upload/hadar2.png', 10);

-- --------------------------------------------------------

--
-- Structure de la table `stars`
--

DROP TABLE IF EXISTS `stars`;
CREATE TABLE IF NOT EXISTS `stars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `bayer` varchar(255) DEFAULT NULL,
  `spectralType` varchar(255) DEFAULT NULL,
  `magnitude` varchar(255) DEFAULT NULL,
  `distance` float DEFAULT NULL,
  `accountId` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `stars`
--

INSERT INTO `stars` (`id`, `name`, `bayer`, `spectralType`, `magnitude`, `distance`, `accountId`) VALUES
(1, 'Sirius', 'α CMa', 'A1V/DA', '8', 6, 1),
(2, 'Canopus', 'α Car', 'F0Ib', '-0,72', 310, 1),
(3, 'Arcturus', 'α Boo', 'K1.5 IIIpe', '-0,04', 34, 1),
(4, 'Rigil Kentaurus', 'α1 Cen', 'G2V', '-0.01', 4, 3),
(5, 'Véga', 'α Lyr', 'A0Va', '0.03', 25, 3),
(6, 'Rigel', 'β Ori', 'B8Ia', '0.12', 630, 3),
(7, 'Procyon', 'α CMi', 'F5 IV–V', '0.38', 11, 3),
(8, 'Achernar', 'α Eri', 'B3Vpe', '0.46', 130, 3),
(9, 'Bételgeuse', 'α Ori', 'M1-2 Ia-Iab', '0.5', 430, 3),
(10, 'Hadar', 'β Cen', 'B1 III', '0.60', 530, 3);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
