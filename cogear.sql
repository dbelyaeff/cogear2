-- phpMyAdmin SQL Dump
-- version 3.5.3
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1:3306
-- Время создания: Ноя 01 2012 г., 16:36
-- Версия сервера: 5.1.65-community-log
-- Версия PHP: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `cogear`
--

-- --------------------------------------------------------

--
-- Структура таблицы `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `aid` int(11) unsigned NOT NULL,
  `bid` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `body` longtext NOT NULL,
  `views` int(11) unsigned NOT NULL,
  `created_date` int(11) NOT NULL,
  `last_update` int(11) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `ip` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=111 ;

--
-- Дамп данных таблицы `posts`
--

INSERT INTO `posts` (`id`, `aid`, `bid`, `name`, `body`, `views`, `created_date`, `last_update`, `published`, `ip`) VALUES
(110, 1, 0, 'Добро пожаловать!', 'Поздравляю, вы успешно установили PHP-фреймворк <b>Cogear</b>!\r\n\r\nЕсли вы еще не авторизировались, то используйте для входа на сайт логин <b>admin</b> и пароль <b>password</b>.\r\n\r\nНа данный момент вам доступен базовый функционал публикации постов. \r\n\r\nРасширить функционал можно за счет активации «шестеренок», то есть компонентов системы. \r\n\r\nПосмотреть список шестеренок можно <a href="http://admin/gears">здесь</a> (не забудьте предварительно авторизироваться под админом):\r\n\r\nО том, как работать с системой вы узнаете на <a href="http://cogear.ru">официальном сайте</a>.\r\n\r\nУдачи!\r\n', 1, 1351776024, 1351776024, 1, '127.0.0.1');

-- --------------------------------------------------------

--
-- Структура таблицы `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(3) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(0, 'guest'),
(1, 'admin'),
(100, 'user');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
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
  `reg_date` int(11) unsigned NOT NULL,
  `last_visit` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `password`, `hash`, `email`, `name`, `avatar`, `role`, `posts`, `drafts`, `reg_date`, `last_visit`) VALUES
(1, 'admin', 'fa4654b18a5442c0914cc1c6b536cd73', 'c535ef8cb9b02af1b3907c5ab8d4009e', 'admin@cogear.ru', 'Беляев Дмитрий', '/avatars/1/1.jpg', 1, 1, 0, 1333104704, 1351776873);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
