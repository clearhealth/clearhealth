CREATE TABLE `fee_schedule` (
  `fee_schedule_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `label` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `priority` int(11) NOT NULL default '2',
  PRIMARY KEY  (`fee_schedule_id`),
  UNIQUE KEY `name` (`name`)
) TYPE=MyISAM;
CREATE TABLE `fee_schedule_data` (
  `code_id` int(11) NOT NULL default '0',
  `revision_id` int(11) NOT NULL default '0',
  `fee_schedule_id` int(11) NOT NULL default '0',
  `data` float(11,2) NOT NULL default '0.00',
  `formula` varchar(255) NOT NULL default '',
  `mapped_code` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`code_id`,`revision_id`,`fee_schedule_id`),
  KEY `fee_schedule_id` (`fee_schedule_id`),
  KEY `revision_id` (`revision_id`)
) TYPE=MyISAM;
CREATE TABLE `fee_schedule_revision` (
  `revision_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `update_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`revision_id`),
  KEY `user_id` (`user_id`)
) TYPE=MyISAM;
