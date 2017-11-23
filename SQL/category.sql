-- --------------------------------------------------------
-- Host:                         188.166.65.212
-- Server version:               5.7.20-0ubuntu0.16.04.1 - (Ubuntu)
-- Server OS:                    Linux
-- HeidiSQL Version:             9.3.0.4984
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for table privatedb.category
CREATE TABLE IF NOT EXISTS `category` (
  `id` int(128) NOT NULL AUTO_INCREMENT,
  `category` tinytext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=latin1 COMMENT='Addon Categories';

-- Dumping data for table privatedb.category: 21 rows
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` (`id`, `category`) VALUES
	(1, 'Action Bars'),
	(2, 'Chat & Communication'),
	(3, 'Artwork'),
	(4, 'Auction & Economy'),
	(5, 'Audio & Video'),
	(6, 'Bags & Inventory'),
	(7, 'Boss Encounters'),
	(8, 'Buffs & Debuffs'),
	(9, 'Class'),
	(10, 'Combat'),
	(11, 'Guild'),
	(12, 'Mail'),
	(13, 'Map & Minimap'),
	(14, 'Minigames'),
	(15, 'Miscellaneous'),
	(16, 'Professions'),
	(17, 'PvP'),
	(18, 'Quests & Leveling'),
	(19, 'Roleplay'),
	(20, 'Tooltip'),
	(21, 'Unitframes');
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
