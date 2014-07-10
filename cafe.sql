-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jul 10, 2014 at 07:05 AM
-- Server version: 5.5.36
-- PHP Version: 5.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `cafe`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `created` datetime NOT NULL,
  `places_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_comment` (`places_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `image` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `places_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_events` (`places_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `places`
--

CREATE TABLE IF NOT EXISTS `places` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(12) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `latitude` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `longtitude` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `intro` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `image` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `district` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `street` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `national` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `province` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `houseno` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ward` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `view` int(11) DEFAULT NULL,
  `numlike` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=55 ;

-- --------------------------------------------------------

--
-- Table structure for table `servies`
--

CREATE TABLE IF NOT EXISTS `servies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime DEFAULT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `image` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `categories_id` int(12) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categories_id` (`categories_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `servies_places`
--

CREATE TABLE IF NOT EXISTS `servies_places` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `servies_id` int(11) DEFAULT NULL,
  `places_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_gh` (`servies_id`),
  KEY `fk_gk` (`places_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `fk_comment` FOREIGN KEY (`places_id`) REFERENCES `places` (`id`);

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `fk_events` FOREIGN KEY (`places_id`) REFERENCES `places` (`id`);

--
-- Constraints for table `servies`
--
ALTER TABLE `servies`
  ADD CONSTRAINT `servies_ibfk_1` FOREIGN KEY (`categories_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `servies_places`
--
ALTER TABLE `servies_places`
  ADD CONSTRAINT `fk_gh` FOREIGN KEY (`servies_id`) REFERENCES `servies` (`id`),
  ADD CONSTRAINT `fk_gk` FOREIGN KEY (`places_id`) REFERENCES `places` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
