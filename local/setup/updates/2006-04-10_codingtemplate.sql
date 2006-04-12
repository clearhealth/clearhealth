CREATE TABLE `coding_template` (
  `coding_template_id` int(11) NOT NULL default '0',
  `practice_id` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `coding_parent_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`coding_template_id`),
  KEY `practice_id` (`practice_id`)
) ENGINE=MyISAM;