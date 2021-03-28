-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 07, 2020 at 06:47 PM
-- Server version: 10.4.10-MariaDB
-- PHP Version: 7.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `biochem`
--

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
CREATE TABLE IF NOT EXISTS `tags` (
  `tagID` int(11) NOT NULL AUTO_INCREMENT,
  `tagName` text NOT NULL,
  `count` int(11) NOT NULL,
  PRIMARY KEY (`tagID`),
  UNIQUE KEY `subjectName` (`tagName`(88))
) ENGINE=MyISAM AUTO_INCREMENT=83 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`tagID`, `tagName`, `count`) VALUES
(1, 'אוקסלואצטט', 0),
(2, 'מעגל קרבס', 0),
(3, 'אצטיל קו-A', 0),
(4, 'גלוקוז', 0),
(5, 'שומנים', 0),
(6, 'אנרגיה ו-ATP', 0),
(7, 'פירובט', 0),
(8, 'כבד', 0),
(9, 'לב', 0),
(10, 'NADPH', 0),
(11, 'NAD', 0),
(12, 'NADP', 0),
(13, 'NADH', 0),
(14, 'FAD', 0),
(15, 'מערכת החיסון', 0),
(16, 'RH', 0),
(17, 'סוכרים', 0),
(18, 'נוגדנים', 0),
(19, 'אנטיגנים', 0),
(20, 'מקרופאג\'ים', 0),
(21, 'PDH', 0),
(22, 'סידן', 0),
(23, 'ארסניום', 0),
(24, 'אנזימים', 0),
(25, 'קופקטורים', 0),
(26, 'ארסנט', 0),
(27, 'אדרנלין', 0),
(28, 'גלוקגון', 0),
(29, 'אפינפרין', 0),
(30, 'cAMP', 0),
(31, 'אינסולין', 0),
(32, 'הורמונים', 0),
(33, 'מעכבים', 0),
(34, 'פנטוזות', 0),
(35, 'Km', 0),
(36, 'pH', 0),
(37, 'איזואנזימים', 0),
(38, 'חומצות אמינו', 0),
(39, 'סרין', 0),
(40, 'אוריאה', 0),
(41, 'B1', 0),
(42, 'ויטמינים', 0),
(43, 'דיאטה', 0),
(44, 'המוגלובין', 0),
(45, 'אנמיה חרמשית', 0),
(46, 'מחלות', 0),
(47, 'PKU', 0),
(48, 'גליקוגן', 0),
(49, 'MCRRADLE', 0),
(50, 'מחלת הפול', 0),
(51, 'G6PD', 0),
(52, 'חומצות שומן', 0),
(53, 'קרניטין', 0),
(54, 'כולסטרול', 0),
(55, 'בטא-אוקסידציה', 0),
(56, 'פרופיניל', 0),
(57, 'קיבה', 0),
(58, 'שריר', 0),
(59, 'גליקוליזה', 0),
(60, 'גליקוגנזה', 0),
(61, 'גלוקונאוגנזה', 0),
(62, 'היפוגליקמיה', 0),
(63, 'מעגל קורי', 0),
(64, 'טרנסאלדולזות', 0),
(65, 'תסיסה כוהלית', 0),
(66, 'מעגל פנטוזות', 0),
(67, 'לקטאט', 0),
(68, 'מעגל גליאוקסלי', 0),
(69, 'תוצרי ביניים', 0),
(70, 'סוקציניל קו-A', 0),
(71, 'שרשרת העברת אלקטרונים', 0),
(72, 'סוקצינט', 0),
(73, 'ubiquinon', 0),
(74, 'דם', 0),
(75, 'כדוריות דם אדומות', 0),
(76, 'אוליגומיצין', 0),
(77, 'התיאוריה כימואוסמוטית', 0),
(78, 'שאטל', 0),
(79, 'אצטון', 0),
(80, 'חלבונים', 0),
(81, 'ריאקציות', 0),
(82, 'ציטרט', 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
