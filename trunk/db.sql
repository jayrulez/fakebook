CREATE TABLE IF NOT EXISTS `fb_users` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `account` varchar(30) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(32) NOT NULL,
  `register_time` int(100) NOT NULL,
  `update_time` int(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `account` (`account`,`email`)
) ENGINE=MyISAM;
