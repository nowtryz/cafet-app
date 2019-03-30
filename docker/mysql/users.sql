-- --
-- User table as compatibility with original database
-- --

-- Table
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `Pseudo` varchar(255) NOT NULL,
  `MDP` varchar(500) NOT NULL,
  `Annee` int(4) NOT NULL,
  `SU` int(11) NOT NULL,
  `admin` int(1) NOT NULL,
  `cafet` int(11) NOT NULL,
  `res_cafet` int(11) NOT NULL,
  `adm_cafet` int(11) NOT NULL,
  `Comm` int(11) NOT NULL,
  `Nom` varchar(20) NOT NULL,
  `Prenom` varchar(20) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Tel` varchar(10) NOT NULL,
  `actif` int(11) NOT NULL,
  `adherent` int(1) NOT NULL,
  `Credit` float NOT NULL,
  `log` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `online` int(11) NOT NULL,
  `regkey` varchar(20) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=`utf8mb4` COLLATE `utf8mb4_unicode_ci`;