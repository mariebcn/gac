-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Hôte : mysql
-- Généré le :  mar. 29 mai 2018 à 06:57
-- Version du serveur :  5.7.22
-- Version de PHP :  7.2.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `gac`
--

-- --------------------------------------------------------

--
-- Structure de la table `mobile_traffic`
--

CREATE TABLE `mobile_traffic` (
  `subscriber` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `consumption` int(11) DEFAULT NULL,
  `type` enum('call','data','sms') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `mobile_traffic`
--
ALTER TABLE `mobile_traffic`
  ADD KEY `idx_subscriber` (`subscriber`),
  ADD KEY `idx_date` (`date`),
  ADD KEY `idx_type` (`type`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
