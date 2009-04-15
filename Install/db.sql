-- Database: `fakebook`
--

-- --------------------------------------------------------
--
-- Table structure for table `fb_user`
--

CREATE TABLE IF NOT EXISTS `fb_user` (
  `id` int(100) NOT NULL auto_increment,
  `email` varchar(100) NOT NULL,
  `password` varchar(32) NOT NULL,
  `register_time` int(100) NULL,
  `update_time` int(100) NULL,
  `status` enum('Registered','Deactivated','Unverified','Provisional','Denied') NOT NULL,
  `sex` enum('Male','Female') NULL,
  `first_name` varchar(100) NULL,
  `middle_name` varchar(100) NULL,
  `last_name` varchar(100) NULL,
  `name` varchar(100) NOT NULL,
  `pic_big` varchar(100) NULL COMMENT 'max 200x600',
  `pic_small` varchar(100) NULL COMMENT 'max 100x300',
  `pic_square` varchar(100) NULL COMMENT 'max 50x50',
  `timezone` int(10) NULL,
  `language` varchar(10) NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `fb_wall`
--

CREATE TABLE IF NOT EXISTS `fb_wall` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('u','g') NOT NULL DEFAULT 'g',
  `wid` int(100) DEFAULT NULL,
  `fromid` int(100) DEFAULT NULL,
  `text` longtext,
  `time` int(100) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `del` tinyint(1) NOT NULL DEFAULT '0',
  `delby` int(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;


-- --------------------------------------------------------

--
-- Table structure for table `fb_comment`
--

CREATE TABLE IF NOT EXISTS `fb_comment` (
  `id` int(100) NOT NULL auto_increment,
  `xid` int(100) NOT NULL,
  `fromid` int(100) NOT NULL,
  `text` longtext,
  `time` int(100) NOT NULL,
  `username` varchar(100) NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;


-- --------------------------------------------------------

--
-- Table structure for table `fb_group`
--

CREATE TABLE IF NOT EXISTS `fb_group` (
  `id` int(100) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `pic_big` varchar(100),
  `pic_small` varchar(100),
  `pic_square` varchar(100),
  `description` text NULL,
  `group_type` varchar(100) NULL,
  `group_subtype` varchar(100) NULL,
  `recent_news` text NULL,
  `creator` int(100) NOT NULL,
  `update_time` int(100) NULL,
  `office` varchar(100) NULL,
  `website` varchar(100) NULL,
  `venue` text NULL,
  `privacy` enum('OPEN','CLOSED','SECRET') NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;


-- --------------------------------------------------------

--
-- Table structure for table `fb_group_member`
--

CREATE TABLE IF NOT EXISTS `fb_group_member` (
  `uid` int(100) NOT NULL,
  `gid` int(100) NOT NULL,
  `time` int(100) NULL,
  `title` enum('member','admin','creator') NOT NULL default 'member',
  `positions` varchar(100) NULL,
  PRIMARY KEY  (`uid`,`gid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;


-- --------------------------------------------------------

--
-- Table structure for table `fb_group_request`
--

CREATE TABLE IF NOT EXISTS `fb_group_request` (
  `uid` int(100) NOT NULL,
  `gid` int(100) NOT NULL,
  `time` int(100) NULL,
  PRIMARY KEY  (`uid`,`gid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;


-- --------------------------------------------------------

--
-- Table structure for table `fb_group_invite`
--

CREATE TABLE IF NOT EXISTS `fb_group_invite` (
  `uid` int(100) NOT NULL,
  `gid` int(100) NOT NULL,
  `uid_from` int(100) NOT NULL,
  `time` int(100) NULL,
  PRIMARY KEY  (`uid`,`gid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;


-- --------------------------------------------------------

--
-- Table structure for table `fb_friend`
--

CREATE TABLE IF NOT EXISTS `fb_friend` (
  `uid1` int(100) NOT NULL,
  `uid2` int(100) NOT NULL,
  PRIMARY KEY  (`uid1`,`uid2`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;


-- --------------------------------------------------------

--
-- Table structure for table `fb_friend_request`
--

CREATE TABLE IF NOT EXISTS `fb_friend_request` (
  `uid_from` int(100) NOT NULL,
  `uid_to` int(100) NOT NULL,
  `time` int(100) NULL,
  PRIMARY KEY  (`uid_from`,`uid_to`)
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


-- --------------------------------------------------------

--
-- Table structure for table `fb_feed`
--

CREATE TABLE IF NOT EXISTS `fb_feed` (
  `id` int(100) NOT NULL auto_increment,
  `type` enum('wall','photo','album','comment','group','friend','language') NOT NULL,
  `parameter` text NULL,
  `uid` int(100) NOT NULL,
  `time` int(100) NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;


-- --------------------------------------------------------

--
-- Table structure for table `fb_report`
--

CREATE TABLE IF NOT EXISTS `fb_report` (
  `id` int(100) NOT NULL auto_increment,
  `type` varchar(20) NOT NULL,
  `xid` int(100) NOT NULL,
  `uid` int(100) NOT NULL,
  `status` tinyint(1) default '0',
  `time` int(100) NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;