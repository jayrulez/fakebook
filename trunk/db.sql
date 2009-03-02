CREATE TABLE IF NOT EXISTS `fb_users` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `truename` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(32) NOT NULL,
  `register_time` int(100) NOT NULL,
  `update_time` int(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM;

CREATE TABLE `fb_profile` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `user_id` int(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=MyISAM;

CREATE TABLE `fb_apps` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `url` varchar(100) NOT NULL,
  `icon` varchar(100) NOT NULL,
  `developer` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`,`url`,`icon`)
) ENGINE=MyISAM;

CREATE TABLE `fb_user_apps` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `app_id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `position` int(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;