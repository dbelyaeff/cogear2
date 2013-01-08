CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `aid` int(11) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `body` longtext NOT NULL,
  `views` int(11) unsigned NOT NULL,
  `created_date` int(11) NOT NULL,
  `last_update` int(11) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `ip` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `posts`
--

INSERT INTO `posts` (`id`, `aid`, `name`, `body`, `views`, `created_date`, `last_update`, `published`, `ip`) VALUES
(1, 1, 'Добро пожаловать', 'Поздравляю, вы успешно установили PHP-фреймворк <b>Cogear</b>!<br/>\r\n<br/>\r\nЕсли вы еще не авторизировались, то используйте для входа на сайт логин <b>admin</b> и пароль <b>password</b>.<br/>\r\n<br/>\r\nНа данный момент вам доступен базовый функционал публикации постов. [cut text=Смотреть]<br/>\r\nРасширить функционал можно за счет активации «шестеренок», то есть компонентов системы. <br/>\r\n<br/>\r\nПосмотреть список шестеренок можно <a href="/admin/gears">здесь</a> (не забудьте предварительно авторизироваться под админом):<br/>\r\n<br/>\r\nО том, как работать с системой вы узнаете на <a href="http://cogear.ru">официальном сайте</a>.<br/>\r\n<br/>\r\nУдачи!<br/>\r\n<br/>\r\n', 40, 1351776024, 1355836964, 1, '127.0.0.1');