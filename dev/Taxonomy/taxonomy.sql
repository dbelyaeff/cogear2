-- phpMyAdmin SQL Dump
-- version 3.2.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 24, 2012 at 04:09 PM
-- Server version: 5.1.40
-- PHP Version: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `cogear`
--

-- --------------------------------------------------------

--
-- Table structure for table `taxonomy_links`
--

DROP TABLE IF EXISTS `taxonomy_links`;
CREATE TABLE IF NOT EXISTS `taxonomy_links` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned NOT NULL,
  `vid` int(11) NOT NULL,
  `pid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tid` (`tid`,`vid`,`pid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `taxonomy_links`
--

INSERT INTO `taxonomy_links` (`id`, `tid`, `vid`, `pid`) VALUES
(11, 12, 4, 106),
(15, 7, 5, 106),
(4, 8, 5, 106),
(14, 6, 5, 106);

-- --------------------------------------------------------

--
-- Table structure for table `taxonomy_terms`
--

DROP TABLE IF EXISTS `taxonomy_terms`;
CREATE TABLE IF NOT EXISTS `taxonomy_terms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `aid` int(10) unsigned NOT NULL,
  `vid` int(10) unsigned NOT NULL,
  `pid` int(10) unsigned NOT NULL,
  `level` tinyint(3) unsigned NOT NULL,
  `thread` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `created_date` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `taxonomy_terms`
--

INSERT INTO `taxonomy_terms` (`id`, `aid`, `vid`, `pid`, `level`, `thread`, `name`, `link`, `ip`, `created_date`) VALUES
(7, 1, 5, 0, 0, '                      003', 'Статьи', 'articles', '127.0.0.1', 1340303763),
(6, 1, 5, 0, 0, '                      002', 'Новости', 'news', '127.0.0.1', 1340303750),
(5, 1, 4, 0, 0, '                      001', 'Test', 'test', '127.0.0.1', 1340302225),
(8, 1, 5, 0, 0, '                      004', 'Обзоры', 'reviews', '127.0.0.1', 1340303774),
(10, 1, 4, 0, 0, '                      005', 'Новый Test', 'novyjj Test', '127.0.0.1', 1340372928),
(12, 1, 4, 0, 0, '                      006', '', '', '127.0.0.1', 1340373888);

-- --------------------------------------------------------

--
-- Table structure for table `taxonomy_vocabulary`
--

DROP TABLE IF EXISTS `taxonomy_vocabulary`;
CREATE TABLE IF NOT EXISTS `taxonomy_vocabulary` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `type` tinyint(1) unsigned NOT NULL,
  `is_open` tinyint(1) NOT NULL,
  `is_multiple` tinyint(4) NOT NULL,
  `position` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `link` (`link`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `taxonomy_vocabulary`
--

INSERT INTO `taxonomy_vocabulary` (`id`, `name`, `link`, `description`, `type`, `is_open`, `is_multiple`, `position`) VALUES
(4, 'Tags', 'tags', 'Post tags.', 0, 1, 0, 0),
(5, 'Categories', 'categories', 'Some description.', 1, 0, 1, 0);
