-- phpMyAdmin SQL Dump
-- version 3.2.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 05, 2012 at 06:50 PM
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
-- Table structure for table `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `aid` int(11) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `body` longtext NOT NULL,
  `comments` int(11) unsigned NOT NULL,
  `created_date` int(11) NOT NULL,
  `last_update` int(11) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `front` tinyint(1) unsigned NOT NULL,
  `front_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `aid`, `name`, `body`, `comments`, `created_date`, `last_update`, `published`, `front`, `front_time`) VALUES
(5, 1, 'First post', '<p>Some content. It might be interesting for you.</p>\r\n', 0, 1333018639, 1333049583, 1, 1, 1333049583),
(6, 1, 'Second post', '<p>Just testing!</p>\r\n', 0, 1333049639, 1333049731, 1, 1, 1333049731),
(7, 1, 'Third post!', '<p>God bless!!!</p>\r\n', 0, 1333104973, 1333106250, 1, 1, 1333106250);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `role` int(3) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `posts` int(11) unsigned NOT NULL,
  `comments` int(11) unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `reg_date` int(11) unsigned NOT NULL,
  `last_visit` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `login`, `password`, `hash`, `email`, `name`, `role`, `avatar`, `posts`, `comments`, `is_active`, `reg_date`, `last_visit`) VALUES
(1, 'admin', '9307dfe7122068a78c5d37c77d9c2c15', '', 'admin@cogear.ru', 'Беляев Дмитрий', 100, '/avatars/1/av.jpg', 3, 0, 0, 1333104704, 1333371473);
