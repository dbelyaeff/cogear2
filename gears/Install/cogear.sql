DROP TABLE IF EXISTS `roles`;
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

DROP TABLE IF EXISTS `routes`;
CREATE TABLE IF NOT EXISTS `routes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `route` varchar(255) NOT NULL,
  `callback` varchar(255) NOT NULL,
  `created_date` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `route` (`route`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `users`;
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
(1, 'admin', 'fa4654b18a5442c0914cc1c6b536cd73', 'c535ef8cb9b02af1b3907c5ab8d4009e', 'admin@cogear.ru', 'Беляев Дмитрий', '', 1, 1, 0, 1333104704, 1357674386);


CREATE TABLE IF NOT EXISTS `widgets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `callback` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `options` blob NOT NULL,
  `region` varchar(255) NOT NULL,
  `route` varchar(255) NOT NULL,
  `position` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `region` (`region`),
  KEY `route` (`route`),
  KEY `order` (`position`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2;

--
-- Дамп данных таблицы `widgets`
--

INSERT INTO `widgets` (`id`, `callback`, `name`, `options`, `region`, `route`, `position`) VALUES
(1, 'Theme_Widget_HTML', 'Логотип', 0x733a3137303a22433a31363a22436f72655f41727261794f626a656374223a3134303a7b783a693a303b613a323a7b733a353a227469746c65223b733a303a22223b733a373a22636f6e74656e74223b733a37383a223c703e3c6120687265663d222f223e3c696d67207372633d222f7468656d65732f44656661756c742f696d672f6c6f676f2e706e67222f3e3c2f613e3c62722f3e0d0a3c2f703e3c62722f3e0d0a223b7d3b6d3a613a303a7b7d7d223b, 'header', '.*', 0);
