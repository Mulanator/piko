-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Erstellungszeit: 11. Okt 2019 um 08:51
-- Server-Version: 5.7.26
-- PHP-Version: 7.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Datenbank: `piko`
--
DROP DATABASE piko;
CREATE DATABASE `piko`;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `log`
--

use piko;

DROP TABLE IF EXISTS `log`;
CREATE TABLE IF NOT EXISTS `log` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `Datum` datetime NOT NULL,
	  `Erzeugung aktuell` float NOT NULL,
	  `Gesamtenergie` float NOT NULL,
	  `Status` text NOT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=latin1;
	COMMIT;
