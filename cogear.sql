-- phpMyAdmin SQL Dump
-- version 3.2.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 28, 2012 at 02:28 PM
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
(1, 1, 'блог админа', 'admin', '', '/blogs/admin.jpg', 1, 0, 0, 0, 0),
(2, 1, 'Новости', 'news', '<p>Новости сайта.</p>\r\n', '', 1, 0, 0, 0, 1335122958),
(3, 2, 'Сиамский Кот', 'CuamckuyKot', '', '<img class="avatar" src="/uploads/avatars/0/.presets/avatar.small/avatar.jpg" alt="CuamckuyKot"/>', 0, 5, 0, 0, 0),
(4, 3, 'Лев Щаранский blog', 'sharanskiy', '', '', 0, 0, 0, 0, 1335182382);

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
  `aid` int(11) unsigned NOT NULL,
  `pid` int(11) unsigned NOT NULL,
  `level` smallint(3) NOT NULL,
  `path` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `published` tinyint(1) NOT NULL,
  `fronzen` tinyint(1) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `created_date` int(11) NOT NULL,
  `last_update` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=71 ;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `aid`, `pid`, `level`, `path`, `body`, `published`, `fronzen`, `ip`, `created_date`, `last_update`) VALUES
(1, 1, 0, 1, '              1', '<p>First comment!</p>\r\n', 0, 0, '', 1335192543, 0),
(2, 1, 0, 1, '              2', '<p>This is my second comment!</p>\r\n', 0, 0, '', 1335193395, 0),
(3, 1, 0, 1, '              3', '<p>Third comment! Amazing!</p>\r\n', 0, 0, '', 1335193415, 0),
(5, 1, 0, 1, '              5', 'Пятый коммент!', 0, 0, '', 1335197394, 0),
(6, 1, 0, 1, '              6', 'Шестой коммент! Просто отлично!', 0, 0, '', 1335198767, 0),
(7, 1, 0, 1, '              7', 'Седьмой коммент!', 0, 0, '', 1335198774, 0),
(8, 1, 3, 2, '              3.8', 'Ответ на третий коммент!', 0, 0, '', 1335200551, 0),
(9, 1, 8, 3, '              3.8.9', 'Ответ на ответ!', 0, 0, '', 1335201003, 0),
(10, 1, 0, 1, '             10', 'Первый коммент!', 0, 0, '', 1335202172, 0),
(13, 1, 0, 1, '             13', 'Еще один коммент!', 0, 0, '', 1335202286, 0),
(12, 1, 11, 3, '             10.11.12', 'Всем привет!', 0, 0, '', 1335202193, 0),
(14, 1, 0, 1, '             14', 'Еще один коммент!', 0, 0, '', 1335202293, 0),
(15, 1, 0, 1, '             15', 'Ха-ха-ха! Как же это круто!', 0, 0, '', 1335202326, 0),
(16, 1, 0, 1, '             16', 'Попробуем разок!', 0, 0, '', 1335202345, 0),
(17, 1, 0, 1, '             17', 'Add new comment!', 0, 0, '', 1335202703, 0),
(18, 1, 0, 1, '             18', 'testasdasdasd', 0, 0, '', 1335202729, 0),
(19, 1, 0, 1, '             19', 'testasdasdasd', 0, 0, '', 1335202741, 0),
(20, 1, 0, 1, '             20', 'asdasdasd', 0, 0, '', 1335202749, 0),
(21, 1, 0, 1, '             21', 'asdasdasd', 0, 0, '', 1335202773, 0),
(22, 1, 0, 1, '             22', 'asdasd', 0, 0, '', 1335202801, 0),
(23, 1, 0, 1, '             23', 'asdasd', 0, 0, '', 1335202809, 0),
(24, 1, 0, 1, '             24', 'asdasdas', 0, 0, '', 1335202883, 0),
(25, 1, 0, 1, '             25', 'test12312', 0, 0, '', 1335206763, 0),
(26, 1, 0, 1, '             26', 'test12312', 0, 0, '', 1335206772, 0),
(27, 1, 0, 1, '             27', 'asdasdasd', 0, 0, '', 1335206868, 0),
(28, 1, 0, 1, '             28', 'asdasdasd', 0, 0, '', 1335206873, 0),
(29, 1, 0, 1, '             29', 'asdasdas', 0, 0, '', 1335206905, 0),
(30, 1, 0, 1, '             30', 'asdasdas', 0, 0, '', 1335206909, 0),
(31, 1, 0, 1, '             31', 'asdasdas', 0, 0, '', 1335206961, 0),
(32, 1, 0, 1, '             32', 'asdasdas', 0, 0, '', 1335206999, 0),
(33, 1, 0, 1, '             33', 'asdasdas', 0, 0, '', 1335207011, 0),
(34, 1, 0, 1, '             34', 'asdasdas', 0, 0, '', 1335207064, 0),
(35, 1, 0, 1, '             35', 'asdasdas', 0, 0, '', 1335207089, 0),
(36, 1, 0, 1, '             36', 'asdasdas', 0, 0, '', 1335207382, 0),
(37, 1, 0, 1, '             37', 'asdasdas', 0, 0, '', 1335207551, 0),
(38, 1, 0, 1, '             38', 'asdasdas', 0, 0, '', 1335207578, 0),
(39, 1, 0, 1, '             39', 'asdasdas', 0, 0, '', 1335207619, 0),
(40, 1, 0, 1, '             40', 'asdasdas', 0, 0, '', 1335207656, 0),
(41, 1, 0, 1, '             41', 'asdasdas', 0, 0, '', 1335207805, 0),
(42, 1, 0, 1, '             42', 'asdasdas', 0, 0, '', 1335207880, 0),
(43, 1, 0, 1, '             43', 'asdasdas', 0, 0, '', 1335207905, 0),
(44, 1, 0, 1, '             44', 'asdasdas', 0, 0, '', 1335207915, 0),
(45, 1, 0, 1, '             45', 'asdasdas', 0, 0, '', 1335207939, 0),
(46, 1, 0, 1, '             46', 'asdasdas', 0, 0, '', 1335207954, 0),
(47, 1, 0, 1, '             47', 'asdasdas', 0, 0, '', 1335207979, 0),
(48, 1, 0, 1, '             48', 'asdasdas', 0, 0, '', 1335207994, 0),
(49, 1, 0, 1, '             49', 'asdasdas', 0, 0, '', 1335208024, 0),
(50, 1, 0, 1, '             50', 'asdasdas', 0, 0, '', 1335208071, 0),
(51, 1, 0, 1, '             51', 'asdasdas', 0, 0, '', 1335208115, 0),
(52, 1, 0, 1, '             52', 'asdasdas', 0, 0, '', 1335208126, 0),
(53, 1, 0, 1, '             53', 'asdasdas', 0, 0, '', 1335208134, 0),
(54, 1, 0, 1, '             54', 'asdasdas', 0, 0, '', 1335208142, 0),
(55, 1, 0, 1, '             55', 'asdasdas', 0, 0, '', 1335208203, 0),
(56, 1, 0, 1, '             56', 'asdasdas', 0, 0, '', 1335208224, 0),
(57, 1, 0, 1, '             57', 'asdasdas', 0, 0, '', 1335208253, 0),
(58, 1, 0, 1, '             58', 'asdasdasd', 0, 0, '', 1335208270, 0),
(59, 1, 0, 1, '             59', 'asdasdasd', 0, 0, '', 1335208297, 0),
(60, 1, 0, 1, '             60', 'asdasdasd', 0, 0, '', 1335208325, 0),
(61, 1, 0, 1, '             61', 'asdasdasd', 0, 0, '', 1335208335, 0),
(62, 1, 0, 1, '             62', 'asdasdasd', 0, 0, '', 1335208446, 0),
(63, 1, 0, 1, '             63', 'asdasdasd', 0, 0, '', 1335208476, 0),
(64, 1, 0, 1, '             64', 'asdasdasd', 0, 0, '', 1335208538, 0),
(65, 1, 0, 1, '             65', 'asdasdasd', 0, 0, '', 1335208546, 0),
(66, 1, 0, 1, '             66', 'asdasdasd', 0, 0, '', 1335208589, 0),
(67, 1, 0, 1, '             67', 'asdasdasd', 0, 0, '', 1335208594, 0),
(68, 1, 0, 1, '             68', 'asdasdasd', 0, 0, '', 1335208603, 0),
(69, 1, 0, 1, '             69', 'Next one!', 0, 0, '', 1335208718, 0),
(70, 1, 0, 1, '             70', 'Testasdasd', 0, 0, '', 1335208777, 0);

-- --------------------------------------------------------

--
-- Table structure for table `comments_posts`
--

CREATE TABLE IF NOT EXISTS `comments_posts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=69 ;

--
-- Dumping data for table `comments_posts`
--

INSERT INTO `comments_posts` (`id`, `cid`, `pid`) VALUES
(1, 1, 63),
(2, 2, 63),
(3, 3, 63),
(4, 4, 63),
(6, 5, 63),
(7, 6, 63),
(8, 7, 63),
(9, 8, 63),
(10, 9, 63),
(11, 10, 64),
(12, 11, 64),
(13, 12, 64),
(14, 13, 64),
(15, 14, 64),
(16, 15, 64),
(17, 16, 64),
(18, 17, 65),
(19, 19, 65),
(20, 21, 65),
(21, 23, 65),
(22, 24, 65),
(23, 25, 63),
(24, 26, 63),
(25, 27, 63),
(26, 28, 63),
(27, 29, 63),
(28, 30, 63),
(29, 31, 63),
(30, 32, 63),
(31, 33, 63),
(32, 34, 63),
(33, 35, 63),
(34, 36, 63),
(35, 37, 63),
(36, 38, 63),
(37, 39, 63),
(38, 40, 63),
(39, 41, 63),
(40, 42, 63),
(41, 43, 63),
(42, 44, 63),
(43, 45, 63),
(44, 46, 63),
(45, 47, 63),
(46, 48, 63),
(47, 49, 63),
(48, 50, 63),
(49, 51, 63),
(50, 52, 63),
(51, 53, 63),
(52, 54, 63),
(53, 55, 63),
(54, 56, 63),
(55, 57, 63),
(56, 58, 63),
(57, 59, 63),
(58, 60, 63),
(59, 61, 63),
(60, 62, 63),
(61, 63, 63),
(62, 64, 63),
(63, 65, 63),
(64, 66, 63),
(65, 67, 63),
(66, 68, 63),
(67, 69, 63),
(68, 70, 63);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=66 ;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `aid`, `bid`, `name`, `body`, `comments`, `allow_comments`, `views`, `created_date`, `last_update`, `published`, `ip`, `front`, `front_time`) VALUES
(5, 1, 1, 'First post', '<p>Some content. It might be interesting for you.</p>\r\n', 0, 1, 11, 1333018639, 1334818863, 1, '', 1, 1333049583),
(6, 1, 1, 'Second post', '<p>Just testing!</p>\r\n', 0, 1, 1, 1333049639, 1334771437, 1, '', 1, 1333049731),
(7, 1, 1, 'Third post!', '<p>God bless!!!</p>\r\n', 0, 1, 0, 1333104973, 1334585204, 0, '', 1, 1333106250),
(60, 1, 1, 'One new post!', '<p>Testing!</p>\r\n', 0, 1, 0, 1334580303, 1334588819, 1, '', 1, 0),
(36, 1, 1, 'One new post!', '<p>Testing!</p>\r\n', 0, 1, 0, 1334578826, 1334578826, 0, '', 1, 0),
(28, 1, 1, 'One new post!', '<p>Testing!</p>\r\n', 0, 1, 0, 1334578261, 1334578261, 0, '', 1, 0),
(62, 1, 1, 'Самый новый пост!', '<p>Теперь по-русски!</p>\r\n', 0, 1, 0, 1334612245, 1334612344, 1, '', 0, 0),
(61, 1, 1, 'One new post!', '<p>Testing!</p>\r\n', 0, 1, 0, 1334580309, 1334611913, 1, '', 0, 1334610592),
(63, 2, 1, 'My first post!', '<p>Test\r\n\r\n<br></p>\r\n', 2, 1, 243, 1334835885, 1335206772, 1, '', 1, 1334836174),
(64, 1, 1, 'Первая новость!', '<p>Всем привет!</p>\r\n', 0, 1, 47, 1335179797, 1335179797, 1, '', 0, 0),
(65, 1, 2, 'Новость ё', '<p>Пишем первый псот!</p>\r\n', 0, 1, 12, 1335202428, 1335202692, 1, '', 0, 0);

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
(1, 'admin', '742e2d9fc498b910a74846da5384a7dd', 'c535ef8cb9b02af1b3907c5ab8d4009e', 'admin@cogear.ru', 'Беляев Дмитрий', '/avatars/1/1.jpg', 7, 3, 2, 0, 0, 0, 0, 1333104704, 1335352754),
(2, 'CuamckuyKot', '17fb5a13cfe983a7e288bc502c6ec322', 'a390fe3b68249bf13d2394cc3a031b2f', 'cuamckuy@gmail.com', 'Дмитрий', '', 1, 0, 2, 0, 0, 0, 0, 1334766221, 1335180871),
(3, 'sharanskiy', '2fc1f3d9c3aad0014f802083a78eb8e4', '97e324618fc604b03180419d1c54b390', 'lev@sharanskiy.ru', 'Лев Щаранский', '', 0, 0, 2, 0, 0, 0, 0, 1335182382, 1335182517);
