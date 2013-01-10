CREATE TABLE IF NOT EXISTS `code` (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  aid int(11) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` longtext NOT NULL,
  `type` varchar(15) NOT NULL,
  created_date int(11) NOT NULL,
  PRIMARY KEY (id),
  KEY `type` (`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;