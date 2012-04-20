-- phpMyAdmin SQL Dump
-- version 3.2.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 20, 2012 at 03:08 PM
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
-- Table structure for table `access`
--

CREATE TABLE IF NOT EXISTS `access` (
  `name` varchar(255) NOT NULL,
  `uid` int(11) unsigned NOT NULL,
  `rid` int(11) unsigned NOT NULL,
  KEY `name` (`name`,`uid`,`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `access`
--

INSERT INTO `access` (`name`, `uid`, `rid`) VALUES
('friends', 0, 100),
('post', 0, 100),
('post', 1, 0),
('post.create', 0, 100);

-- --------------------------------------------------------

--
-- Table structure for table `cron`
--

CREATE TABLE IF NOT EXISTS `cron` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cid` int(11) unsigned NOT NULL,
  `exec_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `cron`
--


-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE IF NOT EXISTS `friends` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `f` int(11) unsigned NOT NULL,
  `t` int(11) unsigned NOT NULL,
  `created_date` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `f1` (`f`,`t`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=65 ;

--
-- Dumping data for table `friends`
--

INSERT INTO `friends` (`id`, `f`, `t`, `created_date`) VALUES
(64, 1, 2, 1334915817),
(63, 2, 1, 1334904560);

-- --------------------------------------------------------

--
-- Table structure for table `im_chat`
--

CREATE TABLE IF NOT EXISTS `im_chat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `aid` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `created_date` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `im_chat`
--


-- --------------------------------------------------------

--
-- Table structure for table `im_chat_users`
--

CREATE TABLE IF NOT EXISTS `im_chat_users` (
  `cid` int(11) unsigned NOT NULL,
  `uid` int(11) unsigned NOT NULL,
  `created_date` int(11) unsigned NOT NULL,
  UNIQUE KEY `cid` (`cid`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `im_chat_users`
--


-- --------------------------------------------------------

--
-- Table structure for table `im_pm`
--

CREATE TABLE IF NOT EXISTS `im_pm` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `aid` int(11) unsigned NOT NULL,
  `body` text NOT NULL,
  `created_date` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `im_pm`
--


-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `path` varchar(255) NOT NULL,
  `pid` int(11) unsigned NOT NULL,
  `level` int(3) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `body` longtext NOT NULL,
  `aid` int(10) unsigned NOT NULL,
  `views` int(11) unsigned NOT NULL,
  `published` tinyint(1) unsigned NOT NULL,
  `created_date` int(10) unsigned NOT NULL,
  `last_update` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `path`, `pid`, `level`, `name`, `body`, `aid`, `views`, `published`, `created_date`, `last_update`) VALUES
(2, '              2', 0, 1, 'Introduction', '<p>Welcome!</p>\r\n', 1, 1, 1, 1334917547, 1334917547),
(3, '              2.3', 2, 2, 'Story', '<p>This is dev story.</p>\r\n', 1, 1, 1, 1334918157, 1334918157),
(4, '              2.4', 2, 2, 'Features', '<p>Some features</p>\r\n', 1, 0, 0, 1334920033, 1334920050);

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
  `views` int(11) unsigned NOT NULL,
  `created_date` int(11) NOT NULL,
  `last_update` int(11) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `front` tinyint(1) unsigned NOT NULL,
  `front_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=64 ;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `aid`, `name`, `body`, `comments`, `views`, `created_date`, `last_update`, `published`, `front`, `front_time`) VALUES
(5, 1, 'First post', '<p>Some content. It might be interesting for you.</p>\r\n', 0, 11, 1333018639, 1334818863, 1, 1, 1333049583),
(6, 1, 'Second post', '<p>Just testing!</p>\r\n', 0, 0, 1333049639, 1334771437, 1, 1, 1333049731),
(7, 1, 'Third post!', '<p>God bless!!!</p>\r\n', 0, 0, 1333104973, 1334585204, 0, 1, 1333106250),
(60, 1, 'One new post!', '<p>Testing!</p>\r\n', 0, 0, 1334580303, 1334588819, 1, 1, 0),
(36, 1, 'One new post!', '<p>Testing!</p>\r\n', 0, 0, 1334578826, 1334578826, 0, 1, 0),
(28, 1, 'One new post!', '<p>Testing!</p>\r\n', 0, 0, 1334578261, 1334578261, 0, 1, 0),
(62, 1, 'Самый новый пост!', '<p>Теперь по-русски!</p>\r\n', 0, 0, 1334612245, 1334612344, 1, 0, 0),
(61, 1, 'One new post!', '<p>Testing!</p>\r\n', 0, 0, 1334580309, 1334611913, 1, 0, 1334610592),
(63, 2, 'My first post!', '<p>Test\r\n\r\n<br></p>\r\n', 0, 7, 1334835885, 1334915807, 1, 1, 1334836174);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(3) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(0, 'guest'),
(1, 'admin'),
(100, 'user');

-- --------------------------------------------------------

--
-- Table structure for table `roles_users`
--

CREATE TABLE IF NOT EXISTS `roles_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  `rid` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`,`rid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `roles_users`
--

INSERT INTO `roles_users` (`id`, `uid`, `rid`) VALUES
(1, 0, 0),
(2, 1, 1),
(16, 2, 100);

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
  `avatar` varchar(255) NOT NULL,
  `posts` int(11) unsigned NOT NULL,
  `drafts` int(11) unsigned NOT NULL,
  `friends` int(11) unsigned NOT NULL,
  `subscribers` int(11) unsigned NOT NULL,
  `pm` int(11) unsigned NOT NULL,
  `pm_new` int(11) unsigned NOT NULL,
  `comments` int(11) unsigned NOT NULL,
  `reg_date` int(11) unsigned NOT NULL,
  `last_visit` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `login`, `password`, `hash`, `email`, `name`, `avatar`, `posts`, `drafts`, `friends`, `subscribers`, `pm`, `pm_new`, `comments`, `reg_date`, `last_visit`) VALUES
(1, 'admin', '742e2d9fc498b910a74846da5384a7dd', 'c535ef8cb9b02af1b3907c5ab8d4009e', 'admin@cogear.ru', 'Беляев Дмитрий', '/avatars/1/1.jpg', 5, 3, 2, 0, 0, 0, 0, 1333104704, 1334920052),
(2, 'CuamckuyKot', '17fb5a13cfe983a7e288bc502c6ec322', 'a390fe3b68249bf13d2394cc3a031b2f', 'cuamckuy@gmail.com', 'Дмитрий', '', 1, 0, 0, 1, 0, 0, 0, 1334766221, 1334904895);
