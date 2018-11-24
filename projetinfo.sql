-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  jeu. 17 mai 2018 à 10:08
-- Version du serveur :  5.7.21
-- Version de PHP :  5.6.35

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `projetinfo`
--

-- --------------------------------------------------------

--
-- Structure de la table `etudiants`
--

DROP TABLE IF EXISTS `etudiants`;
CREATE TABLE IF NOT EXISTS `etudiants` (
  `id_etu` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` varchar(20) NOT NULL,
  `prenom` varchar(20) NOT NULL,
  `date_cotis` date NOT NULL,
  `admin` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_etu`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `etudiants`
--

INSERT INTO `etudiants` (`id_etu`, `nom`, `prenom`, `date_cotis`, `admin`) VALUES
(5, 'ilandarideva', 'Sasila', '2018-05-31', 0),
(4, 'gerstein', 'maxime', '2018-12-31', 1),
(6, 'Hdija', 'Ramzi', '2018-05-31', 0);

-- --------------------------------------------------------

--
-- Structure de la table `reservation`
--

DROP TABLE IF EXISTS `reservation`;
CREATE TABLE IF NOT EXISTS `reservation` (
  `id_res` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `jour` date NOT NULL,
  `heure` time NOT NULL,
  `terrain` tinyint(3) UNSIGNED NOT NULL,
  `nom` varchar(20) NOT NULL,
  `prenom` varchar(20) NOT NULL,
  `id_etu` smallint(5) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_res`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `reservation`
--

INSERT INTO `reservation` (`id_res`, `jour`, `heure`, `terrain`, `nom`, `prenom`, `id_etu`) VALUES
(22, '2018-05-16', '11:00:00', 2, 'gerstein', 'maxime', 4),
(23, '2018-05-18', '10:00:00', 2, 'gerstein', 'maxime', 4),
(24, '2018-05-17', '12:00:00', 1, 'gerstein', 'maxime', 4),
(25, '2018-05-21', '13:00:00', 2, 'gerstein', 'maxime', 4),
(26, '2018-05-19', '08:00:00', 2, 'gerstein', 'maxime', 4);

-- --------------------------------------------------------

--
-- Structure de la table `terrains`
--

DROP TABLE IF EXISTS `terrains`;
CREATE TABLE IF NOT EXISTS `terrains` (
  `idter` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `heure_ouv` time NOT NULL,
  `heure_fer` time NOT NULL,
  `dispo` tinyint(1) NOT NULL,
  PRIMARY KEY (`idter`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `terrains`
--

INSERT INTO `terrains` (`idter`, `heure_ouv`, `heure_fer`, `dispo`) VALUES
(1, '09:00:00', '15:00:00', 1),
(2, '08:00:00', '18:00:00', 1),
(3, '08:00:00', '19:00:00', 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
