-- Database: `fakebook`
--

-- --------------------------------------------------------
--
-- Table structure for table `fb_user`
--

CREATE TABLE `fb_user` (
  `id` int(100) NOT NULL auto_increment,
  `email` varchar(100) NOT NULL,
  `password` varchar(32) NOT NULL,
  `register_time` int(100) NOT NULL,
  `update_time` int(100) NOT NULL,
  `status` enum('Registered','Deactivated','Unverified','Provisional','Denied') NOT NULL,
  `first_name` varchar(100) NOT NULL,
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
  `id` int(100) NOT NULL auto_increment,
  `wid` int(100) NOT NULL,
  `fromid` int(100) NOT NULL,
  `text` longtext,
  `time` int(100) NOT NULL,
  `username` varchar(100) NULL
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;


-- --------------------------------------------------------

--
-- Table structure for table `fb_comment`
--

CREATE TABLE IF NOT EXISTS `fb_comment` (
  `id` int(11) NOT NULL auto_increment,
  `xid` int(100) NOT NULL,
  `fromid` int(100) NOT NULL,
  `text` longtext,
  `time` int(100) NOT NULL,
  `username` varchar(100) NULL
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;


-- --------------------------------------------------------

--
-- Table structure for table `fb_group`
--

CREATE TABLE IF NOT EXISTS `fb_group` (
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;


-- --------------------------------------------------------

--
-- Table structure for table `fb_group_member`
--

CREATE TABLE IF NOT EXISTS `fb_group_member` (
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;


-- --------------------------------------------------------

--
-- Table structure for table `fb_friend`
--

CREATE TABLE IF NOT EXISTS `fb_friend` (
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;


-- --------------------------------------------------------

--
-- Table structure for table `fb_friend_request`
--

CREATE TABLE IF NOT EXISTS `fb_friend_request` (
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;


-- --------------------------------------------------------

--
-- Table structure for table `fb_album`
--

CREATE TABLE IF NOT EXISTS `fb_album` (
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;


-- --------------------------------------------------------

--
-- Table structure for table `fb_photo`
--

CREATE TABLE IF NOT EXISTS `fb_photo` (
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;


-- --------------------------------------------------------

--
-- Table structure for table `fb_photo_tag`
--

CREATE TABLE IF NOT EXISTS `fb_photo_tag` (
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;