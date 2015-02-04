-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.5.40-0+wheezy1 - (Debian)
-- Server OS:                    debian-linux-gnu
-- HeidiSQL Version:             9.1.0.4867
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping database structure for R
CREATE DATABASE IF NOT EXISTS `R` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `R`;


-- Dumping structure for table R.environment
CREATE TABLE IF NOT EXISTS `environment` (
  `ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) NOT NULL,
  `Commands` text NOT NULL,
  `LastModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Obsolete` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table R.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `ID` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) DEFAULT NULL,
  `Status` enum('Preparing','Queued','Running','Parsing-Fail','Success','Failure') DEFAULT 'Preparing',
  `Environment` smallint(5) unsigned NOT NULL,
  `Commands` mediumtext NOT NULL,
  `Comments` text,
  `Result` mediumtext,
  `Queued` datetime NOT NULL,
  `Started` datetime DEFAULT NULL,
  `Completed` datetime DEFAULT NULL,
  `Retired` enum('Y','N') NOT NULL DEFAULT 'N',
  `RetiredDate` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `Status` (`Status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table R.results
CREATE TABLE IF NOT EXISTS `results` (
  `ID` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `JobID` int(6) unsigned NOT NULL,
  `FilenameInternal` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `JobID` (`JobID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
