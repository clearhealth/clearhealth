CREATE TABLE `events` (
  `id` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `website` varchar(255) NOT NULL default '',
  `contact_person` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `foreign_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
INSERT INTO `events` VALUES (502532,'No Shows','','','','',502530),(502530,'','','','','',0),(502535,'Cancelations','','','','',502530);
