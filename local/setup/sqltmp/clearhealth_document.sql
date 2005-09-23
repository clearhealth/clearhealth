CREATE TABLE `document` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `type` enum('file_url','blob','web_url') default NULL,
  `size` int(11) default NULL,
  `date` datetime default NULL,
  `url` varchar(255) default NULL,
  `mimetype` varchar(255) default NULL,
  `pages` int(11) default NULL,
  `owner` int(11) default NULL,
  `revision` timestamp NOT NULL,
  `foreign_id` int(11) default NULL,
  `group_id` int(11) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `revision` (`revision`),
  KEY `foreign_id` (`foreign_id`),
  KEY `owner` (`owner`)
) TYPE=MyISAM;
