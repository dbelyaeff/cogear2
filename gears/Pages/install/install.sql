DROP TABLE IF EXISTS `pages`;
CREATE TABLE `pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `thread` varchar(255) NOT NULL,
  `pid` int(11) unsigned NOT NULL,
  `level` int(3) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `body` longtext NOT NULL,
  `route` int(11) unsigned NOT NULL DEFAULT '0',
  `aid` int(10) unsigned NOT NULL,
  `views` int(11) unsigned NOT NULL,
  `show_title` tinyint(1) NOT NULL DEFAULT '1',
  `show_breadcrumb` tinyint(1) NOT NULL DEFAULT '1',
  `published` tinyint(1) unsigned NOT NULL,
  `created_date` int(10) unsigned NOT NULL,
  `last_update` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;