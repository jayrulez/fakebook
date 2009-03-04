-- Database: `fakebook`
--

-- --------------------------------------------------------

--
-- Table structure for table `fb_apps`
--

CREATE TABLE `fb_apps` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `url` varchar(100) NOT NULL,
  `icon` varchar(100) NOT NULL,
  `developer` varchar(100) NOT NULL,
  `description` blob NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`,`url`,`icon`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fb_networks`
--

CREATE TABLE `fb_networks` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(164) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fb_profile`
--

CREATE TABLE `fb_profile` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `user_id` int(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fb_users`
--

CREATE TABLE `fb_users` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `truename` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(32) NOT NULL,
  `network_id` int(100) NOT NULL,
  `register_time` int(100) NOT NULL,
  `update_time` int(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fb_user_apps`
--

CREATE TABLE `fb_user_apps` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `app_id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `position` int(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
