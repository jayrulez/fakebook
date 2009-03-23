-- Database: `fakebook`
--

-- --------------------------------------------------------
--
-- Table structure for table `fb_user`
--

CREATE TABLE `fb_user` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(32) NOT NULL,
  `register_time` int(100) NOT NULL,
  `update_time` int(100) NOT NULL,
  `status` enum('Registered','Deactivated','Unverified','Provisional','Denied') NOT NULL,
  `fist_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) NULL,
  `last_name` varchar(100) NULL,
  `display_name` varchar(100) NOT NULL,
  `timezone` int(10) NOT NULL,
  `language` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `fb_wall`
--

CREATE TABLE IF NOT EXISTS `fb_wall` (
  `id` int(11) NOT NULL auto_increment,
  `wall_id` int(100) NOT NULL,
  `post_author` int(100) NOT NULL,
  `post_content` longtext,
  `post_time` int(100) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;