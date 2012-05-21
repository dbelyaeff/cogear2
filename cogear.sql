-- phpMyAdmin SQL Dump
-- version 3.2.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 21, 2012 at 01:59 PM
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
-- Table structure for table `blogs`
--

CREATE TABLE IF NOT EXISTS `blogs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `aid` int(11) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `login` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `type` tinyint(1) NOT NULL,
  `users` int(11) unsigned NOT NULL,
  `posts` int(11) unsigned NOT NULL,
  `per_page` smallint(2) unsigned NOT NULL,
  `created_date` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 ;

--
-- Dumping data for table `blogs`
--

INSERT INTO `blogs` (`id`, `aid`, `name`, `login`, `body`, `avatar`, `type`, `users`, `posts`, `per_page`, `created_date`) VALUES
(1, 1, 'блог админа', 'admin', 'Всем привет!', '/blogs/admin.jpg', 0, 0, 1, 0, 0),
(2, 1, 'Новости', 'news', '<p>Новости сайта.</p>\r\n', '', 2, 0, 0, 0, 1335122958),
(30, 9, 'Dmitriy blog', 'Cuamckuy', '', '', 0, 0, 0, 0, 1337466816);

-- --------------------------------------------------------

--
-- Table structure for table `blogs_users`
--

CREATE TABLE IF NOT EXISTS `blogs_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  `bid` int(11) unsigned NOT NULL,
  `role` mediumint(3) unsigned NOT NULL,
  `created_date` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`,`bid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 ;

--
-- Dumping data for table `blogs_users`
--

INSERT INTO `blogs_users` (`id`, `uid`, `bid`, `role`, `created_date`) VALUES
(1, 2, 1, 1, 1335171884),
(2, 1, 1, 4, 1335171884),
(3, 1, 2, 4, 0),
(30, 9, 30, 4, 0),
(29, 8, 29, 4, 0),
(28, 7, 28, 4, 0),
(27, 6, 27, 4, 0),
(26, 6, 26, 4, 0),
(25, 5, 25, 4, 0),
(24, 1, 3, 1, 1335336988),
(4, 3, 4, 4, 0);

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(11) unsigned NOT NULL,
  `aid` int(11) unsigned NOT NULL,
  `pid` int(11) unsigned NOT NULL,
  `level` smallint(3) NOT NULL,
  `thread` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `published` tinyint(1) NOT NULL,
  `fronzen` tinyint(1) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `created_date` int(11) NOT NULL,
  `last_update` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `post_id`, `aid`, `pid`, `level`, `thread`, `body`, `published`, `fronzen`, `ip`, `created_date`, `last_update`) VALUES
(1, 28, 9, 0, 0, '                      001', 'asdasd', 1, 0, '127.0.0.1', 1337532111, 1337532111),
(2, 28, 9, 0, 0, '                      002', 'asdasd', 1, 0, '127.0.0.1', 1337532114, 1337532114),
(3, 28, 9, 0, 0, '                      003', 'asdasdasd', 1, 0, '127.0.0.1', 1337532163, 1337532163),
(4, 28, 9, 0, 0, '                      004', 'asdasd', 1, 0, '127.0.0.1', 1337532173, 1337532173),
(5, 28, 9, 0, 0, '                      005', 'asdasd', 1, 0, '127.0.0.1', 1337532183, 1337532183),
(6, 28, 9, 0, 0, '                      006', 'asdasd', 1, 0, '127.0.0.1', 1337532315, 1337532315),
(7, 28, 9, 0, 0, '                      007', 'asdasdasdasd', 1, 0, '127.0.0.1', 1337532438, 1337532438),
(8, 28, 9, 0, 0, '                      008', 'asdasd', 1, 0, '127.0.0.1', 1337532455, 1337532455),
(9, 28, 9, 0, 0, '                      009', 'asdasdads', 1, 0, '127.0.0.1', 1337532469, 1337532469),
(10, 28, 9, 0, 0, '                      010', 'asdasd', 1, 0, '127.0.0.1', 1337532490, 1337532490),
(11, 28, 9, 2, 1, '                      002.001', 'asdasd', 1, 0, '127.0.0.1', 1337532533, 1337532533),
(12, 28, 9, 1, 1, '                      001.001', 'asdasd', 1, 0, '127.0.0.1', 1337532544, 1337532544),
(13, 28, 9, 1, 1, '                      001.002', 'asdasd', 1, 0, '127.0.0.1', 1337532547, 1337532547),
(14, 28, 9, 2, 1, '                      002.002', 'adasdasd', 1, 0, '127.0.0.1', 1337532553, 1337532553),
(18, 28, 1, 8, 1, '                      008.002', 'asdasdasd', 1, 0, '127.0.0.1', 1337590042, 1337590042),
(17, 28, 1, 8, 1, '                      008.001', 'asdasdasd', 1, 0, '127.0.0.1', 1337590042, 1337590042),
(19, 28, 9, 0, 0, '                      011', 'asdasdasd', 1, 0, '127.0.0.1', 1337590069, 1337590069),
(20, 28, 9, 9, 1, '                      009.001', 'asdasdasdasdasdas', 1, 0, '127.0.0.1', 1337590074, 1337590074),
(21, 28, 1, 1, 1, '                      001.003', 'asdasdasd', 1, 0, '127.0.0.1', 1337590100, 1337590100);

-- --------------------------------------------------------

--
-- Table structure for table `comments_views`
--

CREATE TABLE IF NOT EXISTS `comments_views` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL,
  `cn` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=79 ;

--
-- Dumping data for table `comments_views`
--

INSERT INTO `comments_views` (`id`, `pid`, `uid`, `cid`, `cn`) VALUES
(78, 28, 1, 21, 19);

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
-- Table structure for table `fave`
--

CREATE TABLE IF NOT EXISTS `fave` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `created_date` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cid` (`cid`,`pid`,`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

--
-- Dumping data for table `fave`
--

INSERT INTO `fave` (`id`, `cid`, `pid`, `uid`, `created_date`) VALUES
(19, 0, 28, 1, 1337546240),
(20, 15, 0, 1, 1337590021),
(10, 9, 0, 1, 1337544816),
(11, 10, 0, 1, 1337544848),
(12, 3, 0, 1, 1337546103),
(13, 8, 0, 1, 1337546105),
(14, 7, 0, 1, 1337546105),
(15, 6, 0, 1, 1337546107),
(16, 5, 0, 1, 1337546109),
(17, 4, 0, 1, 1337546111),
(18, 11, 0, 1, 1337546143);

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
  `thread` varchar(255) NOT NULL,
  `pid` int(11) unsigned NOT NULL,
  `level` int(3) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `body` longtext NOT NULL,
  `aid` int(10) unsigned NOT NULL,
  `views` int(11) unsigned NOT NULL,
  `published` tinyint(1) unsigned NOT NULL,
  `created_date` int(10) unsigned NOT NULL,
  `last_update` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `thread`, `pid`, `level`, `name`, `link`, `body`, `aid`, `views`, `published`, `created_date`, `last_update`) VALUES
(1, '                      001', 0, 0, 'Документация', 'user_guide', '1111', 1, 21, 1, 1337413742, 1337413742),
(2, '                      001.001', 1, 1, 'Вступление', 'intro', 'asdasd', 1, 26, 1, 1337413753, 1337413753),
(3, '                      001.001.001', 2, 2, 'Первая часть', 'first-part', 'asdasdasd', 1, 0, 1, 1337413769, 1337413769),
(4, '                      001.001.002', 2, 2, 'Вторая часть', 'second-part', 'asdasdasd', 1, 11, 1, 1337413782, 1337413782),
(5, '                      001.002', 1, 1, 'Основная часть', 'main-part', 'asdasasd', 1, 1, 1, 1337413803, 1337413803),
(6, '                      001.003', 1, 1, 'Заключение', 'outro', 'asdasdasd', 1, 0, 1, 1337413817, 1337413817),
(7, '                      002', 0, 0, 'Лицензионное соглашение', 'license', 'asdasd', 1, 6, 1, 1337416088, 1337416088);

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `aid` int(11) unsigned NOT NULL,
  `bid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `body` longtext NOT NULL,
  `comments` int(11) unsigned NOT NULL,
  `allow_comments` tinyint(1) NOT NULL,
  `views` int(11) unsigned NOT NULL,
  `created_date` int(11) NOT NULL,
  `last_update` int(11) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `front` tinyint(1) unsigned NOT NULL,
  `front_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=68 ;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `aid`, `bid`, `name`, `body`, `comments`, `allow_comments`, `views`, `created_date`, `last_update`, `published`, `ip`, `front`, `front_time`) VALUES
(28, 1, 1, 'One new post!', 'Som content.', 19, 1, 662, 1334578261, 1337590100, 1, '127.0.0.1', 1, 1337283551);

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `roles_users`
--


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
  `role` smallint(3) NOT NULL,
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `login`, `password`, `hash`, `email`, `name`, `avatar`, `role`, `posts`, `drafts`, `friends`, `subscribers`, `pm`, `pm_new`, `comments`, `reg_date`, `last_visit`) VALUES
(1, 'admin', 'fa4654b18a5442c0914cc1c6b536cd73', 'c535ef8cb9b02af1b3907c5ab8d4009e', 'admin@cogear.ru', 'Беляев Дмитрий', '/avatars/1/1.jpg', 1, 1, 0, 2, 0, 0, 0, 0, 1333104704, 1337594362),
(9, 'Cuamckuy', '2fc1f3d9c3aad0014f802083a78eb8e4', '3282f6a3b2df4658a051f89c03fcc3f7', 'cuamckuy@gmail.com', 'Dmitriy', '', 100, 0, 0, 0, 0, 0, 0, 0, 1337466816, 1337590056);
