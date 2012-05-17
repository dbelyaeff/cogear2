-- phpMyAdmin SQL Dump
-- version 3.2.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 18, 2012 at 02:01 AM
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
('blog', 0, 100),
('friends', 0, 100),
('post', 0, 100),
('post', 1, 0),
('post.create', 0, 100);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

--
-- Dumping data for table `blogs`
--

INSERT INTO `blogs` (`id`, `aid`, `name`, `login`, `body`, `avatar`, `type`, `users`, `posts`, `per_page`, `created_date`) VALUES
(1, 1, 'блог админа', 'admin', '', '/blogs/admin.jpg', 0, 0, 3, 0, 0),
(2, 1, 'Новости', 'news', '<p>Новости сайта.</p>\r\n', '', 2, 0, 0, 0, 1335122958);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

--
-- Dumping data for table `blogs_users`
--

INSERT INTO `blogs_users` (`id`, `uid`, `bid`, `role`, `created_date`) VALUES
(1, 2, 1, 1, 1335171884),
(2, 1, 1, 4, 1335171884),
(3, 1, 2, 4, 0),
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `post_id`, `aid`, `pid`, `level`, `thread`, `body`, `published`, `fronzen`, `ip`, `created_date`, `last_update`) VALUES
(7, 67, 1, 0, 0, '1/', 'Прием!', 1, 0, '127.0.0.1', 1337291982, 1337291982),
(6, 28, 1, 4, 1, '1.1/', 'asdasasd', 1, 0, '127.0.0.1', 1337290794, 1337290794),
(4, 28, 1, 0, 0, '1/', 'asdasd', 1, 0, '127.0.0.1', 1337288978, 1337288978),
(8, 67, 1, 7, 1, '1.1/', 'Привет всем!', 1, 0, '127.0.0.1', 1337291991, 1337291991);

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
(4, '              4', 0, 1, 'Features', '<p>Some features</p>\r\n', 1, 0, 1, 1334920033, 1335011257);

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
(28, 1, 1, 'One new post!', 'Testing!\r\n\r\n', 2, 1, 61, 1334578261, 1337290857, 1, '127.0.0.1', 0, 1337283551),
(67, 1, 1, 'testasdasd', 'tsetset', 2, 1, 4, 1337284468, 1337291991, 1, '127.0.0.1', 1, 0),
(61, 1, 1, 'One new post!', '<p>Testing!</p>\r\n', 0, 1, 10, 1334580309, 1337290881, 1, '127.0.0.1', 1, 1334610592);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `login`, `password`, `hash`, `email`, `name`, `avatar`, `posts`, `drafts`, `friends`, `subscribers`, `pm`, `pm_new`, `comments`, `reg_date`, `last_visit`) VALUES
(1, 'admin', '742e2d9fc498b910a74846da5384a7dd', 'c535ef8cb9b02af1b3907c5ab8d4009e', 'admin@cogear.ru', 'Беляев Дмитрий', '/avatars/1/1.jpg', 3, 0, 2, 0, 0, 0, 0, 1333104704, 1337291973);
